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

    // Tracker for carry-forward logic (defaults to today if the first row is empty)
    $last_valid_date = date('Y-m-d'); 

    /**
     * Helper function for Date conversion
     * Returns NULL if the date string is truly empty to allow for carry-forward logic
     */
    function convertCsvDate($dateString) {
        $cleaned = trim($dateString);
        if (empty($cleaned)) return null; 
        
        $indonesianMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $englishMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        
        $translatedDate = str_ireplace($indonesianMonths, $englishMonths, $cleaned);
        $date = date_create($translatedDate);
        
        return $date ? date_format($date, 'Y-m-d') : null;
    }

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $rowCount++;
        
        // Skip headers based on specific CSV structures
        if ($type == 'pemasukan' && $rowCount <= 3) continue; 
        if ($type == 'pengeluaran' && $rowCount <= 1) continue; 
        if ($type == 'master' && $rowCount <= 4) continue; 

        // IGNORE EMPTY ROWS OR ROWS WITH ONLY COMMAS (e.g., ,,,,)
        // array_filter removes empty elements; array_map('trim') ensures spaces aren't counted as data
        $cleanData = array_filter(array_map('trim', $data));
        if (empty($cleanData)) {
            continue; 
        }

        $query = "";

        if ($type == 'pengeluaran') {
            // Carry-forward date logic: update tracker if a date exists, otherwise use last known date
            $csvDate = convertCsvDate($data[0]);
            if ($csvDate !== null) {
                $last_valid_date = $csvDate;
            }
            
            $tanggal   = $last_valid_date;
            $pegawai   = mysqli_real_escape_string($conn, $data[1]);
            $barang    = mysqli_real_escape_string($conn, $data[2]);
            $jumlah    = (float)$data[3];
            $satuan    = isset($data[4]) ? mysqli_real_escape_string($conn, $data[4]) : '';
            $ket       = isset($data[5]) ? mysqli_real_escape_string($conn, $data[5]) : 'Imported';
            $no_bukti  = isset($data[6]) ? mysqli_real_escape_string($conn, $data[6]) : rand(1000, 9999);

            $query = "INSERT INTO pengeluaran (tanggal, nama_pegawai, nama_barang_input, jumlah, satuan, keterangan, no_bukti) 
                      VALUES ('$tanggal', '$pegawai', '$barang', '$jumlah', '$satuan', '$ket', '$no_bukti')";
        } 
        else if ($type == 'pemasukan') {
            // Carry-forward date logic for Pemasukan
            $csvDate = convertCsvDate($data[0]);
            if ($csvDate !== null) {
                $last_valid_date = $csvDate;
            }

            $tanggal = $last_valid_date;
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