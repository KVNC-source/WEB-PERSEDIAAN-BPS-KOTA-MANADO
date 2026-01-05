<?php
include '../DB/config.php';

// Query untuk menghitung stok secara real-time
$query = "SELECT 
    b.id_barang,
    b.nama_barang,
    b.satuan,
    IFNULL(SUM(p.jumlah), 0) AS total_masuk,
    IFNULL(sq_out.total_keluar, 0) AS total_keluar,
    (IFNULL(SUM(p.jumlah), 0) - IFNULL(sq_out.total_keluar, 0)) AS sisa_stok
FROM barang_atk b
LEFT JOIN pemasukan p ON b.id_barang = p.id_barang
LEFT JOIN (
    SELECT id_barang, SUM(jumlah) AS total_keluar 
    FROM pengeluaran 
    WHERE keterangan LIKE '%Sudah Disetujui%'
    GROUP BY id_barang
) AS sq_out ON b.id_barang = sq_out.id_barang
GROUP BY b.id_barang, b.nama_barang, b.satuan
ORDER BY b.nama_barang ASC";

$result = mysqli_query($conn, $query);
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BPS KOTA MANADO | Data Stok</title>
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

        /* Specific accent for Stock Monitoring */
        .qty-stock-subtle {
            background: #f1f5f9;
            color: #334155;
            font-weight: 800;
            font-size: 1rem;
            padding: 6px 16px;
            border-radius: 10px;
            display: inline-block;
            border: 1px solid #e2e8f0;
        }
        
        .qty-critical {
            background: #fff5f5;
            color: #dc2626;
            border: 1px solid #fee2e2;
        }

        .icon-box-primary-subtle {
            width: 40px;
            height: 40px;
            background: #eef2f7;
            color: #004488;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-satuan-stock {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 700;
            font-size: 0.65rem;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #f1f5f9;
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
            <h2 class="hero-title-clean mb-1">Monitoring Persediaan Barang</h2>
            <p class="text-muted small"><i class="fas fa-warehouse text-primary me-2"></i>Status ketersediaan stok inventaris gudang BPS secara real-time.</p>
        </div>
        <button onclick="window.location.reload();" class="btn btn-white border shadow-sm fw-bold btn-sm bg-white">
            <i class="fas fa-sync-alt me-1 text-primary"></i> Update Data
        </button>
    </div>

    <div class="table-responsive">
        <table id="stokTable" class="table table-pronounced">
            <thead>
                <tr>
                    <th>NAMA BARANG</th>
                    <th class="text-center">SATUAN</th>
                    <th class="text-center">TOTAL MASUK</th>
                    <th class="text-center">TOTAL KELUAR</th>
                    <th class="text-center">SISA STOK</th>
                    <th class="text-center">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)) { 
                    $is_critical = ($row['sisa_stok'] < 5);
                    $is_empty = ($row['sisa_stok'] <= 0);
                ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="icon-box-primary-subtle me-3">
                                <i class="fas fa-cube"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark small"><?php echo htmlspecialchars($row['nama_barang']); ?></div>
                                <div class="extra-small text-muted text-uppercase" style="font-size: 0.65rem;">ID: <?php echo $row['id_barang']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge-satuan-stock">
                            <?php echo strtoupper($row['satuan']); ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="text-success fw-bold small">+<?php echo number_format($row['total_masuk'], 0, ',', '.'); ?></div>
                        <div class="extra-small text-muted" style="font-size: 0.65rem;">Unit Masuk</div>
                    </td>
                    <td class="text-center">
                        <div class="text-danger fw-bold small">-<?php echo number_format($row['total_keluar'], 0, ',', '.'); ?></div>
                        <div class="extra-small text-muted" style="font-size: 0.65rem;">Unit Keluar</div>
                    </td>
                    <td class="text-center">
                        <span class="qty-stock-subtle <?php echo ($is_critical) ? 'qty-critical' : ''; ?>">
                            <?php echo number_format($row['sisa_stok'], 0, ',', '.'); ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <?php if($is_empty): ?>
                            <span class="status-pill border-0 shadow-sm" style="background: #fff5f5; color: #dc2626; padding: 4px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">
                                <i class="fas fa-times-circle me-1"></i> Habis
                            </span>
                        <?php elseif($is_critical): ?>
                            <span class="status-pill border-0 shadow-sm" style="background: #fffaf0; color: #d97706; padding: 4px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">
                                <i class="fas fa-exclamation-triangle me-1"></i> Menipis
                            </span>
                        <?php else: ?>
                            <span class="status-pill border-0 shadow-sm" style="background: #f0fdf4; color: #16a34a; padding: 4px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700;">
                                <i class="fas fa-check-circle me-1"></i> Tersedia
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $("#stokTable").DataTable({
            "pageLength": 15,
            "language": {
                "sSearch": "",
                "searchPlaceholder": "Cari stok barang",
                "oPaginate": { 
                    "sPrevious": "<i class='fas fa-chevron-left'></i>", 
                    "sNext": "<i class='fas fa-chevron-right'></i>" 
                },
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ item"
            }
        });
        $('.dataTables_filter input').addClass('form-control shadow-sm border-0 bg-white px-4 py-2 mb-3 small');
    });
</script>
</body>
</html>