<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

include '../DB/config.php';

// Proteksi Role: Hanya Admin yang dapat mengakses halaman ini
$user_role = 'admin'; 
if ($user_role !== 'admin') {
    header("Location: index.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BPS KOTA MANADO | Manajemen Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --deep-navy: #002d5a; /* Color update for darker text */
        }

        body { 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            min-height: 100vh; 
        }

        .glass-nav { 
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(15px); 
            border-bottom: 1px solid rgba(255, 255, 255, 0.3); 
        }

        .hero-title { 
            font-weight: 800; 
            /* Darker Gradient */
            background: linear-gradient(to right, var(--deep-navy), #005bb5); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
        }

        .text-dark-navy {
            color: var(--deep-navy) !important;
        }

        .neumorphic-card { 
            background: rgba(255, 255, 255, 0.8); 
            border: 1px solid rgba(255, 255, 255, 0.4); 
            border-radius: 24px; 
            padding: 30px; 
            box-shadow: 20px 20px 60px #bebebe, -20px -20px 60px #ffffff; 
        }

        .upload-row-neo {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
        }

        .upload-row-neo:hover {
            transform: translateX(10px);
            border-color: var(--deep-navy);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .icon-box-neo {
            width: 55px;
            height: 55px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-right: 20px;
            box-shadow: inset 4px 4px 8px rgba(0,0,0,0.05);
        }

        .instruction-panel {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 24px;
            padding: 30px;
            position: sticky;
            top: 30px;
            border: 1px solid #fff;
            box-shadow: 10px 10px 30px rgba(0,0,0,0.05);
        }

        .guide-step-neo {
            position: relative;
            padding-left: 25px;
            margin-bottom: 25px;
        }

        .guide-step-neo::before {
            content: '';
            position: absolute;
            left: 0;
            top: 5px;
            width: 4px;
            height: 20px;
            border-radius: 10px;
            background: var(--deep-navy);
        }

        .btn-modern {
            border-radius: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: 0.3s;
            padding: 10px 25px;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        /* Sync button specific color */
        .btn-sync {
            background-color: var(--deep-navy);
            color: white;
            border: none;
        }
        .btn-sync:hover {
            background-color: #004080;
            color: white;
        }

        .upload-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            align-items: start;
        }

        @media (max-width: 992px) {
            .upload-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-bps shadow-sm mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">
      <img src="../asset/Logo BPS Kota Manado - All White.png" alt="BPS Logo" class="navbar-logo-white" style="height: 50px;">
    </a>
    <div class="d-flex gap-3">
        <a href="index.php" class="btn <?php echo ($current_page == 'index.php') ? 'btn-light text-primary' : 'btn-outline-light'; ?> btn-sm fw-bold">
            <i class="fas fa-arrow-up me-1"></i> Pengeluaran
        </a>
        <a href="pemasukan.php" class="btn <?php echo ($current_page == 'pemasukan.php') ? 'btn-light text-primary' : 'btn-outline-light'; ?> btn-sm fw-bold">
            <i class="fas fa-arrow-down me-1"></i> Pemasukan
        </a>
        <a href="stok.php" class="btn <?php echo ($current_page == 'stok.php') ? 'btn-light text-primary' : 'btn-outline-light'; ?> btn-sm fw-bold">
            <i class="fas fa-boxes me-1"></i> Data Stok
        </a>
        <a href="dashboard.php" class="btn <?php echo ($current_page == 'dashboard.php') ? 'btn-light text-primary' : 'btn-outline-light'; ?> btn-sm fw-bold">
            <i class="fas fa-chart-line me-1"></i> Dashboard
        </a>
        <a href="upload.php" class="btn <?php echo ($current_page == 'upload.php') ? 'btn-light text-primary' : 'btn-outline-light'; ?> btn-sm fw-bold">
            <i class="fas fa-plus-circle me-1"></i> Input Data
        </a>
    </div>
  </div>
</nav>

<div class="container pb-5">
    <header class="text-center mb-5">
        <h1 class="hero-title mb-1">Pusat Sinkronisasi Data</h1>
        <p class="text-dark-navy fw-semibold">Pembaruan basis data XLSX melalui sistem manajemen modern</p>
    </header>

    <div class="upload-grid">
        <main>
            <div class="neumorphic-card">
                <div class="upload-row-neo">
                    <div class="icon-box-neo bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <div class="flex-grow-1">
                        <label class="d-block fw-bold text-dark-navy mb-1">Data Pengeluaran</label>
                        <form id="form-pengeluaran">
                            <input type="file" class="form-control border-0 bg-light rounded-3" accept=".xlsx, .xls" required>
                        </form>
                    </div>
                    <button type="submit" form="form-pengeluaran" class="btn btn-danger btn-modern ms-3">Unggah</button>
                </div>

                <div class="upload-row-neo">
                    <div class="icon-box-neo bg-success bg-opacity-10 text-success">
                        <i class="fas fa-file-import"></i>
                    </div>
                    <div class="flex-grow-1">
                        <label class="d-block fw-bold text-dark-navy mb-1">Data Pemasukan</label>
                        <form id="form-pemasukan">
                            <input type="file" class="form-control border-0 bg-light rounded-3" accept=".xlsx, .xls" required>
                        </form>
                    </div>
                    <button type="submit" form="form-pemasukan" class="btn btn-success btn-modern ms-3">Unggah</button>
                </div>

                <div class="upload-row-neo">
                    <div class="icon-box-neo bg-primary bg-opacity-10 text-dark-navy">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="flex-grow-1">
                        <label class="d-block fw-bold text-dark-navy mb-1">Master Barang</label>
                        <form id="form-master">
                            <input type="file" class="form-control border-0 bg-light rounded-3" accept=".xlsx, .xls" required>
                        </form>
                    </div>
                    <button type="submit" form="form-master" class="btn btn-sync btn-modern ms-3">Sinkron</button>
                </div>
            </div>
        </main>

        <aside>
            <div class="instruction-panel">
                <h5 class="fw-bold text-dark-navy mb-4 border-bottom pb-2">Konfigurasi Berkas</h5>
                
                <div class="guide-step-neo">
                    <h6 class="fw-bold text-dark-navy mb-1">Identitas Sheet</h6>
                    <p class="small text-muted mb-0">Pastikan tab Excel dinamai tepat: <strong>KELUAR</strong>, <strong>MASUK</strong>, atau <strong>STOCK</strong>.</p>
                </div>

                <div class="guide-step-neo">
                    <h6 class="fw-bold text-dark-navy mb-1">Struktur Kolom</h6>
                    <p class="small text-muted mb-0">Jangan mengubah urutan kolom. Sistem membaca data berdasarkan posisi kolom dari template standar.</p>
                </div>

                <div class="guide-step-neo">
                    <h6 class="fw-bold text-dark-navy mb-1">Pengisian Tanggal</h6>
                    <p class="small text-muted mb-0">Sistem otomatis mengisi sel tanggal kosong berdasarkan nilai pada baris sebelumnya (Carry-Forward).</p>
                </div>

                <div class="alert alert-primary border-0 rounded-4 mt-4 py-3 shadow-sm" style="background-color: rgba(0, 45, 90, 0.05);">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-3 fa-lg text-dark-navy"></i>
                        <div class="small fw-bold text-dark-navy">Berkas diproses secara lokal di peramban untuk keamanan maksimal.</div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="js/script.js"></script>
</body>
</html> 