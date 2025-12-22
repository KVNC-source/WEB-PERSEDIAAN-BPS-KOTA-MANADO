<?php
include '../DB/config.php';
$user_role = 'admin'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user_role == 'admin') {
    $type = $_POST['type'];
    $file_tmp = $_FILES['excel_file']['tmp_name'];
    
    $handle = fopen($file_tmp, "r");
    $rowCount = 0;
    $success = 0;

    // Helper function to format dates to MySQL format (YYYY-MM-DD)
    function formatTanggal($dateString) {
        if (empty(trim($dateString))) return date('Y-m-d');
        $date = date_create($dateString);
        return $date ? date_format($date, 'Y-m-d') : date('Y-m-d');
    }

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $rowCount++;
        
        // Skip headers and metadata based on your specific CSV files
        if ($type == 'pemasukan' && $rowCount <= 3) continue; 
        if ($type == 'pengeluaran' && $rowCount <= 1) continue; 
        if ($type == 'master' && $rowCount <= 4) continue; 

        // Skip completely empty rows
        if (empty(array_filter($data))) continue;

        $query = "";

        if ($type == 'pengeluaran' && isset($data[6])) {
            // Structure: Tanggal(0), Pegawai(1), Barang(2), Jumlah(3), Satuan(4), Keterangan(5), No Bukti(6)
            $tanggal = formatTanggal($data[0]);
            $pegawai = mysqli_real_escape_string($conn, $data[1]);
            $barang  = mysqli_real_escape_string($conn, $data[2]);
            $jumlah  = (int)$data[3];
            $satuan  = mysqli_real_escape_string($conn, $data[4]);
            $ket     = mysqli_real_escape_string($conn, $data[5]);
            $bukti   = mysqli_real_escape_string($conn, $data[6]);

            $query = "INSERT INTO pengeluaran (tanggal, nama_pegawai, nama_barang_input, jumlah, satuan, keterangan, no_bukti) 
                      VALUES ('$tanggal', '$pegawai', '$barang', '$jumlah', '$satuan', '$ket', '$bukti')";
        } 
        else if ($type == 'pemasukan' && isset($data[2])) {
            // Structure: Tanggal(0), Nama Barang(1), Jumlah(2), Satuan(3), Keterangan(4)
            $tanggal = formatTanggal($data[0]);
            $barang  = mysqli_real_escape_string($conn, $data[1]);
            $jumlah  = (float)$data[2];
            $satuan  = isset($data[3]) ? mysqli_real_escape_string($conn, $data[3]) : '';
            $ket     = isset($data[4]) ? mysqli_real_escape_string($conn, $data[4]) : '';

            $query = "INSERT INTO pemasukan (tanggal, nama_barang_input, jumlah, satuan, keterangan) 
                      VALUES ('$tanggal', '$barang', '$jumlah', '$satuan', '$ket')";
        }
        else if ($type == 'master' && isset($data[1])) {
            // Structure: Nama Barang(0), Satuan(1)
            $nama   = mysqli_real_escape_string($conn, $data[0]);
            $satuan = mysqli_real_escape_string($conn, $data[1]);
            
            $query = "INSERT INTO barang_atk (nama_barang, satuan) VALUES ('$nama', '$satuan') 
                      ON DUPLICATE KEY UPDATE satuan='$satuan'";
        }

        if (!empty($query) && mysqli_query($conn, $query)) {
            $success++;
        }
    }
    fclose($handle);

    // Final Sync: Link transactions to the Master Item IDs based on names
    mysqli_query($conn, "UPDATE pemasukan p JOIN barang_atk b ON p.nama_barang_input = b.nama_barang SET p.id_barang = b.id_barang WHERE p.id_barang IS NULL");
    mysqli_query($conn, "UPDATE pengeluaran pg JOIN barang_atk b ON pg.nama_barang_input = b.nama_barang SET pg.id_barang = b.id_barang WHERE pg.id_barang IS NULL");

    header("Location: ../Template/index.php?msg=Berhasil memproses $success data $type");
    exit;
} else {
    die("Akses ditolak.");
}
?>