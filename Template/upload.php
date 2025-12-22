<?php
include '../DB/config.php';

// Simulasi Role: Hanya Admin yang bisa mengakses halaman ini
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
    <title>Admin Panel | BPS KOTA MANADO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .upload-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
        }
        .upload-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        .icon-box {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 20px;
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

<div class="container-fluid px-4">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h3 class="fw-bold text-primary mb-2">Pusat Manajemen Data</h3>
            <p class="text-muted">Pilih kategori yang ingin diperbarui menggunakan file .csv</p>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm upload-card h-100">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-file-export fa-2xl"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Data Pengeluaran</h5>
                    <p class="small text-muted mb-4">Update riwayat pengambilan barang pegawai melalui ekspor Google Form.</p>
                    <form action="../DB/process_upload.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="type" value="pengeluaran">
                        <input type="file" name="excel_file" class="form-control form-control-sm mb-3" accept=".csv" required>
                        <button type="submit" class="btn btn-danger w-100 fw-bold shadow-sm">
                            <i class="fas fa-upload me-1"></i> Impor Pengeluaran
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm upload-card h-100">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="fas fa-file-import fa-2xl"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Data Pemasukan</h5>
                    <p class="small text-muted mb-4">Catat barang masuk (droping/pembelian) untuk menambah saldo stok.</p>
                    <form action="../DB/process_upload.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="type" value="pemasukan">
                        <input type="file" name="excel_file" class="form-control form-control-sm mb-3" accept=".csv" required>
                        <button type="submit" class="btn btn-success w-100 fw-bold shadow-sm">
                            <i class="fas fa-upload me-1"></i> Impor Pemasukan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm upload-card h-100">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-database fa-2xl"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Master Barang</h5>
                    <p class="small text-muted mb-4">Perbarui daftar nama barang, satuan, atau inisialisasi master stok.</p>
                    <form action="../DB/process_upload.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="type" value="master">
                        <input type="file" name="excel_file" class="form-control form-control-sm mb-3" accept=".csv" required>
                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                            <i class="fas fa-sync-alt me-1"></i> Sinkron Master
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mt-5">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title text-primary"><i class="fas fa-info-circle me-2"></i>Panduan Format Kolom CSV</h5>
            </div>
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">Kategori</th>
                        <th>Struktur Kolom CSV (Urutan Harus Tepat)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center fw-bold">Pengeluaran</td>
                        <td><code>Timestamp, Tanggal, Pegawai, Nama Barang, Jumlah</code></td>
                    </tr>
                    <tr>
                        <td class="text-center fw-bold text-success">Pemasukan</td>
                        <td><code>Tanggal, Nama Barang, Jumlah, Keterangan</code></td>
                    </tr>
                    <tr>
                        <td class="text-center fw-bold text-primary">Master Barang</td>
                        <td><code>Nama Barang, Satuan</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
</body>
</html>