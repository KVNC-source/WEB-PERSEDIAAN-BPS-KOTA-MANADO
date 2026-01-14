<?php 
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

include '../DB/config.php'; 

// Fetch history of incoming goods
$query = "SELECT * FROM pemasukan ORDER BY tanggal DESC, id_pemasukan DESC";
$result = mysqli_query($conn, $query);

// Navigation logic
$user_role = 'admin'; 
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BPS KOTA MANADO | Pemasukan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        /* Subtle Clean Header Styling */
        .hero-title-clean {
            font-weight: 700;
            color: #1e293b;
            font-size: 2rem;
        }

        /* Refined Floating Rows for Table */
        .table-pronounced {
            border-collapse: separate !important;
            border-spacing: 0 10px !important;
            background: transparent !important;
        }

        .table-pronounced thead th {
            background: transparent !important;
            color: #64748b !important;
            border: none !important;
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 1px;
            padding: 10px 20px !important;
            text-transform: uppercase;
        }

        .table-pronounced tbody tr {
            background-color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            border-radius: 12px !important;
            transition: all 0.2s ease;
        }

        .table-pronounced tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        }

        .table-pronounced tbody td {
            padding: 15px 20px !important;
            border: none !important;
            vertical-align: middle;
        }

        /* Row Border Radius Support */
        .table-pronounced tbody td:first-child { border-radius: 12px 0 0 12px !important; }
        .table-pronounced tbody td:last-child { border-radius: 0 12px 12px 0 !important; }

        /* Subtle Visual Components for Pemasukan */
        .icon-box-success-subtle {
            width: 38px;
            height: 38px;
            background: #f0fdf4;
            color: #16a34a;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .qty-pemasukan-subtle {
            background: #f0fdf4;
            color: #16a34a;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.95rem;
            border: 1px solid #dcfce7;
        }

        .badge-satuan {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 0.65rem;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body class="bg-light">

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

<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-end mb-4 px-2">
        <div>
            <h2 class="hero-title-clean mb-1">Riwayat Pemasukan Barang</h2>
            <p class="text-muted small">Log penambahan stok inventaris gudang BPS Kota Manado</p>
        </div>
        <button onclick="window.location.reload();" class="btn btn-white border shadow-sm fw-bold btn-sm bg-white">
            <i class="fas fa-sync-alt me-1 text-success"></i> Refresh
        </button>
    </div>

    <div class="table-responsive">
        <table id="pemasukanTable" class="table table-pronounced">
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>DETAIL BARANG</th>
                    <th class="text-center">VOL. MASUK</th>
                    <th class="text-center">SATUAN</th>
                    <th>KETERANGAN SUMBER</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td class="date-cell">
                        <div class="fw-bold text-dark fs-6"><?php echo date('d', strtotime($row['tanggal'])); ?></div>
                        <div class="text-muted extra-small text-uppercase" style="font-size: 0.65rem;"><?php echo date('M Y', strtotime($row['tanggal'])); ?></div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="icon-box-success-subtle me-3">
                                <i class="fas fa-download"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark small"><?php echo htmlspecialchars($row['nama_barang_input']); ?></div>
                                <div class="extra-small text-muted" style="font-size: 0.65rem;">Logistics Entry</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="qty-pemasukan-subtle">
                            + <?php echo number_format($row['jumlah'], 0, ',', '.'); ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge-satuan">
                            <?php echo strtoupper($row['satuan']); ?>
                        </span>
                    </td>
                    <td>
                        <div class="p-2 px-3 bg-light rounded-pill small text-muted border-0 d-inline-block" style="font-size: 0.75rem;">
                            <i class="fas fa-info-circle me-1 text-success"></i> 
                            <?php echo !empty($row['keterangan']) ? htmlspecialchars($row['keterangan']) : 'Tanpa keterangan'; ?>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $("#pemasukanTable").DataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "language": {
                "sSearch": "",
                "searchPlaceholder": "Cari data pemasukan",
                "oPaginate": { "sPrevious": "<i class='fas fa-chevron-left'></i>", "sNext": "<i class='fas fa-chevron-right'></i>" },
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri"
            }
        });
        $('.dataTables_filter input').addClass('form-control shadow-sm border-0 bg-white px-4 py-2 mb-3 small');
    });
</script>
</body>
</html>