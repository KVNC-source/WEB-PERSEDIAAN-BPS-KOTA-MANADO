<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>BPS ATK System</title>

  <!-- CORE STYLES -->
  <link rel="stylesheet" href="css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<!-- ===== NAVBAR (SINGLE SOURCE OF TRUTH) ===== -->
<nav class="navbar navbar-bps shadow-sm mb-4">
  <div class="container-fluid px-4">
    <a class="navbar-brand" href="index.php">
      <img src="../asset/Logo BPS Kota Manado - All White.png" alt="BPS Logo">
    </a>

    <div class="d-flex gap-3">
      <a href="index.php"
         class="btn <?= $current_page === 'index.php' ? 'btn-light text-primary' : 'btn-outline-light' ?> btn-sm fw-bold">
        <i class="fas fa-arrow-up me-1"></i> Pengeluaran
      </a>

      <a href="pemasukan.php"
         class="btn <?= $current_page === 'pemasukan.php' ? 'btn-light text-primary' : 'btn-outline-light' ?> btn-sm fw-bold">
        <i class="fas fa-arrow-down me-1"></i> Pemasukan
      </a>

      <a href="stok.php"
         class="btn <?= $current_page === 'stok.php' ? 'btn-light text-primary' : 'btn-outline-light' ?> btn-sm fw-bold">
        <i class="fas fa-boxes me-1"></i> Data Stok
      </a>

      <a href="dashboard.php"
         class="btn <?= $current_page === 'dashboard.php' ? 'btn-light text-primary' : 'btn-outline-light' ?> btn-sm fw-bold">
        <i class="fas fa-chart-line me-1"></i> Dashboard
      </a>

      <a href="upload.php"
         class="btn <?= $current_page === 'upload.php' ? 'btn-light text-primary' : 'btn-outline-light' ?> btn-sm fw-bold">
        <i class="fas fa-plus-circle me-1"></i> Input Data
      </a>

      <a href="logout.php" class="btn btn-danger btn-sm fw-bold">
        Logout
      </a>
    </div>
  </div>
</nav>
