<?php
include '../DB/config.php';
$user_role = 'admin'; // Simulasi proteksi

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user_role == 'admin') {
    $type = $_POST['type'];
    $file_tmp = $_FILES['excel_file']['tmp_name'];
    
    $handle = fopen($file_tmp, "r");
    $rowCount = 0;
    $success = 0;

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $rowCount++;
        // Lewati 7 baris awal (metadata + header)
        if ($rowCount <= 7) continue; 

        // Mapping yang sesuai dengan file CSV Anda:
        $tanggal_raw = trim($data[0]); // Kolom 0 adalah Tanggal
        $nama_pegawai = mysqli_real_escape_string($conn, trim($data[1])); // Kolom 1 adalah Pegawai
        $nama_barang_input = trim($data[2]); // Kolom 2 adalah Nama Barang
        $jumlah = (float)$data[3]; // Kolom 3 adalah Jumlah // Skip header

        if ($type == 'pengeluaran') {
            // Logika Pengeluaran: Tanggal, Pegawai, Barang, Jumlah
            $tanggal = $data[1]; $pegawai = $data[2]; $barang = $data[3]; $jumlah = $data[4];
            $query = "INSERT INTO pengeluaran (nama_pegawai, nama_barang_input, jumlah, tanggal, keterangan) 
                      VALUES ('$pegawai', '$barang', '$jumlah', '$tanggal', 'Import Admin')";
        } 
        else if ($type == 'pemasukan') {
            // Logika Pemasukan: Tanggal, Barang, Jumlah, Ket
            $tanggal = $data[0]; $barang = $data[1]; $jumlah = $data[2]; $ket = $data[3];
            $query = "INSERT INTO pemasukan (tanggal, nama_barang_input, jumlah, keterangan) 
                      VALUES ('$tanggal', '$barang', '$jumlah', '$ket')";
        }
        else if ($type == 'master') {
            // Logika Master: Nama Barang, Satuan
            $nama = $data[0]; $satuan = $data[1];
            $query = "INSERT INTO barang_atk (nama_barang, satuan) VALUES ('$nama', '$satuan') 
                      ON DUPLICATE KEY UPDATE satuan='$satuan'";
        }

        if (mysqli_query($conn, $query)) $success++;
    }
    fclose($handle);
    header("Location: ../Template/index.php?msg=Berhasil memproses $success data $type");
} else {
    die("Akses ditolak.");
}
?>