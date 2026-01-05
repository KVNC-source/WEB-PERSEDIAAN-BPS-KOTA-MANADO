<?php
include '../DB/config.php';

// Fetch history of outgoing goods (Pengeluaran)
$query = "SELECT * FROM pengeluaran ORDER BY tanggal DESC, no_bukti DESC";
$result = mysqli_query($conn, $query);

// Navigation & Role Logic
$user_role = 'admin'; // Set to 'admin' to see management features
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BPS KOTA MANADO | Pengeluaran</title>
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

        /* Subtle Visual Components */
        .avatar-circle-subtle {
            width: 38px;
            height: 38px;
            background: #eef2f7;
            color: #004488;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .qty-pronounced-subtle {
            background: #fff5f5;
            color: #ef4444;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.95rem;
            border: 1px solid #fee2e2;
        }

        .btn-action-view { color: #004488; background: #f1f5f9; border: none; transition: 0.2s; }
        .btn-action-delete { color: #ef4444; background: #fff5f5; border: none; transition: 0.2s; }
        .btn-action-view:hover { background: #e2e8f0; }
        .btn-action-delete:hover { background: #fee2e2; }
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
            <h2 class="hero-title-clean mb-1">Daftar Pengeluaran Barang</h2>
            <p class="text-muted small">Manajemen mutasi inventaris keluar BPS Kota Manado</p>
        </div>
        <?php if ($user_role == 'admin'): ?>
            <a href="../DB/manage_data.php?action=delete_all_drafts" class="btn btn-outline-danger btn-sm fw-bold shadow-sm" onclick="return confirm('Hapus semua draft?')">
                <i class="fas fa-eraser me-2"></i>Bersihkan Draft
            </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-info alert-dismissible fade show mb-4 shadow-sm border-0 bg-white" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle text-primary me-3 fa-lg"></i>
                <span class="small fw-bold text-dark"><?php echo htmlspecialchars($_GET['msg']); ?></span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table id="atkTable" class="table table-pronounced">
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>PENERIMA</th>
                    <th>NAMA BARANG</th>
                    <th class="text-center">QTY</th>
                    <th class="text-center">NO. BUKTI</th>
                    <th class="text-center">STATUS</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): 
                    $is_approved = (strpos($row['keterangan'], 'Sudah Disetujui') !== false);
                ?>
                <tr>
                    <td class="date-cell">
                        <div class="fw-bold text-dark fs-6"><?php echo date('d', strtotime($row['tanggal'])); ?></div>
                        <div class="text-muted extra-small text-uppercase" style="font-size: 0.65rem;"><?php echo date('M Y', strtotime($row['tanggal'])); ?></div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle-subtle me-3"><?php echo substr($row['nama_pegawai'], 0, 1); ?></div>
                            <div>
                                <div class="fw-bold text-dark small"><?php echo htmlspecialchars($row['nama_pegawai']); ?></div>
                                <div class="extra-small text-muted" style="font-size: 0.65rem;">BPS Kota Manado</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold text-primary small"><?php echo htmlspecialchars($row['nama_barang_input']); ?></div>
                        <div class="extra-small text-muted" style="font-size: 0.65rem;">Persediaan ATK</div>
                    </td>
                    <td class="text-center">
                        <span class="qty-pronounced-subtle">
                            - <?php echo number_format($row['jumlah'], 0, ',', '.'); ?>
                        </span>
                        <div class="extra-small text-muted fw-bold mt-1" style="font-size: 0.6rem;"><?php echo strtoupper($row['satuan']); ?></div>
                    </td>
                    <td class="text-center">
                        <span class="no-bukti-badge border-0 bg-light text-dark fw-bold" style="font-size: 0.75rem;">
                            #<?php echo str_pad($row['no_bukti'], 4, '0', STR_PAD_LEFT); ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <?php if ($user_role == 'admin'): ?>
                            <div class="dropdown">
                                <button class="status-pill border-0 shadow-sm <?php echo $is_approved ? 'status-approved' : 'status-draft'; ?> dropdown-toggle w-100 py-1" type="button" data-bs-toggle="dropdown">
                                    <span class="small fw-bold"><?php echo $is_approved ? 'Verified' : 'Pending'; ?></span>
                                </button>
                                <ul class="dropdown-menu shadow-sm border-0 small">
                                    <li><a class="dropdown-item" href="../DB/manage_data.php?action=acc&no_bukti=<?php echo $row['no_bukti']; ?>"><i class="fas fa-check text-success me-2"></i>Setujui</a></li>
                                    <li><a class="dropdown-item" href="../DB/manage_data.php?action=draft&no_bukti=<?php echo $row['no_bukti']; ?>"><i class="fas fa-undo text-warning me-2"></i>Draft</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <span class="status-pill <?php echo $is_approved ? 'status-approved' : 'status-draft'; ?> py-1 px-3">
                                <span class="small fw-bold"><?php echo $is_approved ? 'Verified' : 'Pending'; ?></span>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="btn-group rounded shadow-sm overflow-hidden">
                            <a href="generate_pdf.php?no_bukti=<?php echo $row['no_bukti']; ?>" target="_blank" class="btn btn-action-view btn-sm">
                                <i class="fas fa-print"></i>
                            </a>
                            <?php if ($user_role == 'admin'): ?>
                            <a href="../DB/manage_data.php?action=delete&no_bukti=<?php echo $row['no_bukti']; ?>" onclick="return confirm('Hapus data?')" class="btn btn-action-delete btn-sm">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
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
        $("#atkTable").DataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "language": {
                "sSearch": "",
                "searchPlaceholder": "Cari mutasi",
                "oPaginate": { "sPrevious": "<i class='fas fa-chevron-left'></i>", "sNext": "<i class='fas fa-chevron-right'></i>" },
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri"
            }
        });
        $('.dataTables_filter input').addClass('form-control shadow-sm border-0 bg-white px-4 py-2 mb-3 small');
    });
</script>
</body>
</html>