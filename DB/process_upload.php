<?php
include '../DB/config.php';

/**
 * Pemroses Unggahan Data Pengeluaran (GForm) - Versi CSV Ringan
 * Alur:
 * 1. Unggah file .csv hasil download dari GForm.
 * 2. Sistem mendeteksi pemisah (koma atau titik koma).
 * 3. Sistem mencocokkan Nama Barang dengan database master (barang_atk).
 * 4. Stok berkurang secara otomatis di halaman Data Stok.
 */

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
    $file_name = $_FILES['excel_file']['name'];
    $file_tmp = $_FILES['excel_file']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $successCount = 0;
    $errorCount = 0;
    $errorDetails = [];

    // Proteksi format file
    if ($file_ext !== 'csv') {
        header("Location: index.php?msg=" . urlencode("Error: Harap gunakan format .csv. (Buka file Excel Anda, Save As -> CSV)"));
        exit;
    }

    try {
        $handle = fopen($file_tmp, "r");
        
        // Deteksi pemisah (delimiter) secara otomatis
        $firstLine = fgets($handle);
        $separator = (strpos($firstLine, ';') !== false) ? ';' : ',';
        rewind($handle);

        $rowCount = 0;
        while (($data = fgetcsv($handle, 1000, $separator)) !== FALSE) {
            $rowCount++;
            
            // Lewati baris header (Baris 1)
            if ($rowCount == 1) continue; 
            
            // Abaikan jika baris kosong
            if (count($data) < 4 || empty($data[1]) || empty($data[3])) continue;

            /**
             * Mapping Berdasarkan Dummy GForm:
             * $data[0] = Timestamp (Abaikan)
             * $data[1] = Tanggal Pengambilan
             * $data[2] = Diambil Oleh (Pegawai)
             * $data[3] = Nama Barang
             * $data[4] = Jumlah
             */
            
            // 1. Bersihkan & Format Tanggal
            // Menggunakan DateTime untuk menangani berbagai format GForm (M/D/Y atau D/M/Y)
            $raw_date = trim($data[1]);
            $date_obj = date_create($raw_date);
            if ($date_obj) {
                $tanggal = date_format($date_obj, "Y-m-d");
            } else {
                $tanggal = date("Y-m-d"); // Fallback ke hari ini jika gagal
            }
            
            // 2. Sanitasi Input
            $nama_pegawai = mysqli_real_escape_string($conn, trim($data[2]));
            $nama_barang_input = trim($data[3]);
            $nama_barang_sql = mysqli_real_escape_string($conn, $nama_barang_input);
            
            // Bersihkan angka (hilangkan pemisah ribuan jika ada, ganti koma desimal ke titik)
            $jumlah_raw = str_replace(['.', ','], ['', '.'], $data[4]);
            $jumlah = (float)$jumlah_raw;

            // 3. Cari ID Barang di master (barang_atk)
            // Menggunakan BINARY/Exact match dulu, baru LIKE sebagai cadangan
            $find_query = "SELECT id_barang, satuan FROM barang_atk 
                          WHERE nama_barang = '$nama_barang_sql' 
                          OR nama_barang_sakti = '$nama_barang_sql' 
                          OR nama_barang LIKE '%$nama_barang_sql%' 
                          LIMIT 1";
                          
            $res = mysqli_query($conn, $find_query);
            $barang = mysqli_fetch_assoc($res);

            if ($barang && $jumlah > 0) {
                $id_barang = $barang['id_barang'];
                $satuan = $barang['satuan'];

                // 4. Masukkan ke tabel pengeluaran
                $insert = "INSERT INTO pengeluaran (id_barang, nama_barang_input, tanggal, jumlah, satuan, nama_pegawai, keterangan) 
                           VALUES ('$id_barang', '$nama_barang_sql', '$tanggal', '$jumlah', '$satuan', '$nama_pegawai', 'Import GForm')";
                
                if (mysqli_query($conn, $insert)) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            } else {
                // Barang tidak ditemukan di master atau jumlah 0
                $errorCount++;
                $errorDetails[] = $nama_barang_input;
            }
        }
        fclose($handle);

        // Buat pesan laporan yang lebih mendetail
        $msg = "Berhasil impor $successCount data.";
        if ($errorCount > 0) {
            $failed_items = array_unique($errorDetails);
            $msg .= " Gagal $errorCount data. Pastikan nama barang berikut terdaftar di database: " . implode(", ", array_slice($failed_items, 0, 3));
            if (count($failed_items) > 3) $msg .= "...";
        }
        
        header("Location: index.php?msg=" . urlencode($msg));
        
    } catch (Exception $e) {
        header("Location: index.php?msg=" . urlencode("Error: " . $e->getMessage()));
    }
} else {
    header("Location: index.php");
}
?>