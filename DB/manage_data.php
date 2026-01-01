<?php
include 'config.php';

// Proteksi Role: Hanya admin yang diizinkan melakukan perubahan data
$user_role = 'admin'; 

if ($user_role !== 'admin' || !isset($_GET['action'])) {
    header("Location: ../Template/index.php");
    exit;
}

$action = $_GET['action'];

if ($action === 'delete' && isset($_GET['no_bukti'])) {
    // Menghapus data pengeluaran berdasarkan nomor bukti
    $no_bukti = mysqli_real_escape_string($conn, $_GET['no_bukti']);
    $query = "DELETE FROM pengeluaran WHERE no_bukti = '$no_bukti'";
    $msg = "Data berhasil dihapus.";
} 
elseif ($action === 'acc' && isset($_GET['no_bukti'])) {
    // Mengubah status keterangan menjadi 'Sudah Disetujui' (ACC)
    $no_bukti = mysqli_real_escape_string($conn, $_GET['no_bukti']);
    $query = "UPDATE pengeluaran SET keterangan = 'Sudah Disetujui' WHERE no_bukti = '$no_bukti'";
    $msg = "Data berhasil disetujui (ACC).";
} 
elseif ($action === 'draft' && isset($_GET['no_bukti'])) {
    // Mengembalikan status data menjadi Draft (tidak memotong stok)
    $no_bukti = mysqli_real_escape_string($conn, $_GET['no_bukti']);
    $query = "UPDATE pengeluaran SET keterangan = 'Draft (Menunggu Persetujuan)' WHERE no_bukti = '$no_bukti'";
    $msg = "Data berhasil dikembalikan ke status draft.";
} 
elseif ($action === 'delete_all_drafts') {
    // Menghapus semua baris yang belum memiliki keterangan 'Sudah Disetujui'
    $query = "DELETE FROM pengeluaran WHERE keterangan NOT LIKE '%Sudah Disetujui%'";
    $msg = "Semua data draft telah dibersihkan.";
}

// Eksekusi query dan arahkan kembali ke halaman utama dengan pesan sukses/gagal
if (isset($query) && mysqli_query($conn, $query)) {
    header("Location: ../Template/index.php?msg=" . urlencode($msg));
} else {
    header("Location: ../Template/index.php?msg=" . urlencode("Gagal memproses permintaan."));
}
?>