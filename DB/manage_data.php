<?php
include 'config.php';

// Simulasi proteksi role
$user_role = 'admin'; 

if ($user_role !== 'admin' || !isset($_GET['action'])) {
    header("Location: ../Template/index.php");
    exit;
}

$action = $_GET['action'];

if ($action === 'delete' && isset($_GET['no_bukti'])) {
    $no_bukti = mysqli_real_escape_string($conn, $_GET['no_bukti']);
    $query = "DELETE FROM pengeluaran WHERE no_bukti = '$no_bukti'";
    $msg = "Data berhasil dihapus.";
} 
elseif ($action === 'acc' && isset($_GET['no_bukti'])) {
    $no_bukti = mysqli_real_escape_string($conn, $_GET['no_bukti']);
    $query = "UPDATE pengeluaran SET keterangan = 'Sudah Disetujui' WHERE no_bukti = '$no_bukti'";
    $msg = "Data berhasil disetujui (ACC).";
} 
elseif ($action === 'delete_all_drafts') {
    $query = "DELETE FROM pengeluaran WHERE keterangan NOT LIKE '%Sudah Disetujui%'";
    $msg = "Semua data draft telah dibersihkan.";
}

if (isset($query) && mysqli_query($conn, $query)) {
    header("Location: ../Template/index.php?msg=" . urlencode($msg));
} else {
    header("Location: ../Template/index.php?msg=" . urlencode("Gagal memproses permintaan."));
}
?>