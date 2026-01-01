<?php
include '../DB/config.php';

// Role Simulation: Only Admin can access this page
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
    <title>BPS KOTA MANADO | Input Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .upload-card {
            transition: all 0.3s cubic-bezier(.25,.8,.25,1);
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }
        .upload-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.12) !important;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 25px;
            font-size: 2rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .card-title-sub {
            font-size: 0.85rem;
            color: #6c757d;
            height: 40px;
            line-height: 1.4;
        }
        .btn-upload {
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .guide-section {
            background-color: #fff;
            border-radius: 12px;
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
        <a href="upload.php" class="btn <?php echo ($current_page == 'upload.php') ? 'btn-light text-primary' : 'btn-outline-light'; ?> btn-sm fw-bold">
            <i class="fas fa-plus-circle me-1"></i> Input Data
        </a>
    </div>
  </div>
</nav>

<div class="container py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary mb-2">Pusat Manajemen Data</h2>
        <p class="text-muted">Perbarui basis data persediaan menggunakan file .csv</p>
    </div>

    <div class="row g-4 mb-5">
        <!-- Pengeluaran Card -->
        <div class="col-md-4">
            <div class="card shadow-sm upload-card h-100 border-top border-danger border-4">
                <div class="card-body p-4 text-center">
                    <div class="icon-circle bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Data Pengeluaran</h5>
                    <p class="card-title-sub mb-4 px-2">Unggah riwayat distribusi barang kepada pegawai.</p>
                    <form action="../DB/process_upload.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="type" value="pengeluaran">
                        <input type="file" name="excel_file" class="form-control form-control-sm mb-3" accept=".csv" required>
                        <button type="submit" class="btn btn-danger btn-upload w-100 shadow-sm">
                            <i class="fas fa-cloud-upload-alt me-2"></i>Impor Pengeluaran
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Pemasukan Card -->
        <div class="col-md-4">
            <div class="card shadow-sm upload-card h-100 border-top border-success border-4">
                <div class="card-body p-4 text-center">
                    <div class="icon-circle bg-success bg-opacity-10 text-success">
                        <i class="fas fa-file-import"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Data Pemasukan</h5>
                    <p class="card-title-sub mb-4 px-2">Catat barang masuk untuk menambah saldo stok gudang.</p>
                    <form action="../DB/process_upload.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="type" value="pemasukan">
                        <input type="file" name="excel_file" class="form-control form-control-sm mb-3" accept=".csv" required>
                        <button type="submit" class="btn btn-success btn-upload w-100 shadow-sm">
                            <i class="fas fa-cloud-upload-alt me-2"></i>Impor Pemasukan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Master Data Card -->
        <div class="col-md-4">
            <div class="card shadow-sm upload-card h-100 border-top border-primary border-4">
                <div class="card-body p-4 text-center">
                    <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-database"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Master Barang</h5>
                    <p class="card-title-sub mb-4 px-2">Sinkronkan daftar nama barang dan satuan resmi.</p>
                    <form action="../DB/process_upload.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="type" value="master">
                        <input type="file" name="excel_file" class="form-control form-control-sm mb-3" accept=".csv" required>
                        <button type="submit" class="btn btn-primary btn-upload w-100 shadow-sm">
                            <i class="fas fa-sync-alt me-2"></i>Sinkron Master
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- CSV Format Guide -->
    <div class="card shadow-sm border-0 guide-section">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3">
                    <i class="fas fa-lightbulb text-warning"></i>
                </div>
                <h5 class="fw-bold text-dark mb-0">Panduan Format Kolom CSV</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4" width="200">Kategori</th>
                            <th>Struktur Kolom (Urutan Wajib Sama)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 fw-bold text-danger">Pengeluaran</td>
                            <td><code class="bg-light p-1 rounded text-dark">Tanggal, Pegawai, Nama Barang, Jumlah, Satuan, Keterangan, No Bukti</code></td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-bold text-success">Pemasukan</td>
                            <td><code class="bg-light p-1 rounded text-dark">Tanggal, Nama Barang, Jumlah, Satuan, Keterangan</code></td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-bold text-primary">Master Barang</td>
                            <td><code class="bg-light p-1 rounded text-dark">Nama Barang, Satuan</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="alert alert-secondary mt-3 small mb-0">
                <i class="fas fa-info-circle me-2"></i> Pastikan file berformat <strong>.csv</strong> dan menggunakan pemisah koma.
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>x