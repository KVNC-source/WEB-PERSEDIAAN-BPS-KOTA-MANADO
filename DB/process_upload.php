<?php
include '../DB/config.php';

// Role Protection
$user_role = 'admin'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user_role == 'admin') {
    $type = $_POST['type'];
    $file_tmp = $_FILES['excel_file']['tmp_name'];
    
    $handle = fopen($file_tmp, "r");
    $rowCount = 0;
    $success = 0;

    /**
     * Helper function for Date conversion
     * Handles Indonesian month names and converts to MySQL format (YYYY-MM-DD)
     */
    function convertCsvDate($dateString) {
        if (empty(trim($dateString))) return date('Y-m-d');
        
        // Define Indonesian month names and their English counterparts
        $indonesianMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $englishMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        
        // Replace Indonesian months with English months so PHP can parse it correctly
        $translatedDate = str_ireplace($indonesianMonths, $englishMonths, trim($dateString));
        
        $date = date_create($translatedDate);
        return $date ? date_format($date, 'Y-m-d') : date('Y-m-d');
    }

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $rowCount++;
        
        // Skip headers based on your specific CSV structures
        if ($type == 'pemasukan' && $rowCount <= 3) continue; 
        if ($type == 'pengeluaran' && $rowCount <= 1) continue; 
        if ($type == 'master' && $rowCount <= 4) continue; 

        if (empty(array_filter($data))) continue;

        $query = "";

        if ($type == 'pengeluaran') {
            // Respect the spreadsheet date ($data[0]) instead of using today's date
            $tanggal   = convertCsvDate($data[0]); 
            $pegawai   = mysqli_real_escape_string($conn, $data[1]);
            $barang    = mysqli_real_escape_string($conn, $data[2]);
            $jumlah    = (int)$data[3];
            $satuan    = isset($data[4]) ? mysqli_real_escape_string($conn, $data[4]) : '';
            $ket       = isset($data[5]) ? mysqli_real_escape_string($conn, $data[5]) : 'Imported';
            $no_bukti  = isset($data[6]) ? mysqli_real_escape_string($conn, $data[6]) : rand(1000, 9999);

            $query = "INSERT INTO pengeluaran (tanggal, nama_pegawai, nama_barang_input, jumlah, satuan, keterangan, no_bukti) 
                      VALUES ('$tanggal', '$pegawai', '$barang', '$jumlah', '$satuan', '$ket', '$no_bukti')";
        } 
        else if ($type == 'pemasukan') {
            // Use spreadsheet date for Pemasukan
            $tanggal = convertCsvDate($data[0]);
            $barang  = mysqli_real_escape_string($conn, $data[1]);
            $jumlah  = (float)$data[2];
            $satuan  = isset($data[3]) ? mysqli_real_escape_string($conn, $data[3]) : '';
            $ket     = isset($data[4]) ? mysqli_real_escape_string($conn, $data[4]) : '';

            $query = "INSERT INTO pemasukan (tanggal, nama_barang_input, jumlah, satuan, keterangan) 
                      VALUES ('$tanggal', '$barang', '$jumlah', '$satuan', '$ket')";
        }
        else if ($type == 'master') {
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

    // Sync IDs for stock calculations
    mysqli_query($conn, "UPDATE pemasukan p JOIN barang_atk b ON p.nama_barang_input = b.nama_barang SET p.id_barang = b.id_barang WHERE p.id_barang IS NULL");
    mysqli_query($conn, "UPDATE pengeluaran pg JOIN barang_atk b ON pg.nama_barang_input = b.nama_barang SET pg.id_barang = b.id_barang WHERE pg.id_barang IS NULL");

    header("Location: ../Template/index.php?msg=Berhasil memproses $success data $type");
    exit;
} else {
    die("Akses ditolak.");
}
?>