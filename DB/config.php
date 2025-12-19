<?php
// Pengaturan Koneksi Database XAMPP Anda
$host = "localhost";
$user = "root";
$pass = "kenola20"; // Ganti jika Anda menggunakan password MySQL
$db   = "barang_persediaan";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

// Set timezone for date consistency (Crucial for No. Bukti logic)
date_default_timezone_set('Asia/Makassar'); 
?>