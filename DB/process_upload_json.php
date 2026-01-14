<?php
include '../DB/config.php';
$user_role = 'admin'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user_role == 'admin') {
    $type = $_POST['type'];
    $data = json_decode($_POST['payload'], true);
    $success = 0;

    foreach ($data as $row) {
        $query = "";
        if ($type == 'pemasukan') {
            $tgl = mysqli_real_escape_string($conn, $row[0]);
            $brg = mysqli_real_escape_string($conn, $row[1]);
            $jml = (float)$row[2];
            $sat = mysqli_real_escape_string($conn, trim($row[3] ?? ''));
            $ket = mysqli_real_escape_string($conn, $row[4] ?? '');

            $query = "INSERT INTO pemasukan (tanggal, nama_barang_input, jumlah, satuan, keterangan) 
                      VALUES ('$tgl', '$brg', '$jml', '$sat', '$ket')";
        } 
        else if ($type == 'pengeluaran') {
            $tgl = mysqli_real_escape_string($conn, $row[0]);
            $pgw = mysqli_real_escape_string($conn, $row[1]);
            $brg = mysqli_real_escape_string($conn, $row[2]);
            $jml = (float)$row[3];
            $sat = mysqli_real_escape_string($conn, trim($row[4] ?? ''));
            $ket = mysqli_real_escape_string($conn, $row[5] ?? 'Imported');
            $nob = mysqli_real_escape_string($conn, $row[6] ?? rand(1000, 9999));

            $query = "INSERT INTO pengeluaran (tanggal, nama_pegawai, nama_barang_input, jumlah, satuan, keterangan, no_bukti) 
                      VALUES ('$tgl', '$pgw', '$brg', '$jml', '$sat', '$ket', '$nob')";
        }
        else if ($type == 'master') {
            $brg = mysqli_real_escape_string($conn, $row[0]);
            $sat = mysqli_real_escape_string($conn, trim($row[1]));
            $query = "INSERT INTO barang_atk (nama_barang, satuan) VALUES ('$brg', '$sat') ON DUPLICATE KEY UPDATE satuan='$sat'";
        }

        if (!empty($query) && mysqli_query($conn, $query)) $success++;
    }

    // Run Sync to link IDs
    mysqli_query($conn, "UPDATE pemasukan p JOIN barang_atk b ON p.nama_barang_input = b.nama_barang SET p.id_barang = b.id_barang WHERE p.id_barang IS NULL");
    mysqli_query($conn, "UPDATE pengeluaran pg JOIN barang_atk b ON pg.nama_barang_input = b.nama_barang SET pg.id_barang = b.id_barang WHERE pg.id_barang IS NULL");

    echo json_encode(['success' => $success]);
    exit;
}
?>