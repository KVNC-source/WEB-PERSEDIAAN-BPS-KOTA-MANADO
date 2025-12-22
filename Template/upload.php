<?php
include '../DB/config.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Impor Data GForm | BPS Kota Manado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-bps shadow-sm mb-4">
      <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
          <img src="../asset/Logo BPS Kota Manado - All White.png" alt="BPS Logo" class="navbar-logo-white" style="height: 50px;">
        </a>
        <div class="d-flex gap-3">
            <a href="index.php" class="btn btn-outline-light btn-sm fw-bold">Pengeluaran</a>
            <a href="pemasukan.php" class="btn btn-outline-light btn-sm fw-bold">Pemasukan</a>
            <a href="stok.php" class="btn btn-outline-light btn-sm fw-bold">Data Stok</a>
            <a href="upload.php" class="btn btn-light text-primary btn-sm fw-bold">Input Data</a>
        </div>
      </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-file-csv fa-3x text-success mb-3"></i>
                            <h4 class="fw-bold">Impor Hasil Google Form</h4>
                            <p class="text-muted small">Unggah file <b>.csv</b> hasil ekspor dari Google Form Pengeluaran Barang</p>
                        </div>
                        
                        <form id="uploadForm" action="../DB/process_upload.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Pilih File CSV</label>
                                <input type="file" name="excel_file" id="fileInput" class="form-control" accept=".csv" required>
                                <div class="form-text mt-2 italic small">
                                    <i class="fas fa-info-circle me-1"></i> 
                                    Pastikan urutan kolom: [0]Timestamp, [1]Tanggal, [2]Pegawai, [3]Nama Barang, [4]Jumlah.
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-warning w-100 fw-bold py-2 shadow-sm">
                                <i class="fas fa-upload me-2"></i> PROSES DATA PENGELUARAN
                            </button>
                        </form>
                        
                        <div class="mt-4 pt-3 border-top">
                            <a href="index.php" class="text-decoration-none small text-muted">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>