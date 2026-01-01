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
    <!-- Dependencies consistent with BPS theme -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom dropdown styling for status inside table */
        .status-dropdown .dropdown-toggle {
            padding: 4px 12px;
            font-size: 0.85rem;
            border-radius: 20px;
            min-width: 140px;
        }
        .status-dropdown .dropdown-menu {
            font-size: 0.9rem;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .dropdown-item i {
            width: 20px;
        }
    </style>
</head>
<body>

<!-- Unified Navbar -->
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
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title text-primary fw-bold">
                    <i class="fas fa-arrow-up me-2"></i>Daftar Pengeluaran Barang
                </h4>
                
                <?php if ($user_role == 'admin'): ?>
                <div class="d-flex gap-2">
                    <a href="../DB/manage_data.php?action=delete_all_drafts" class="btn btn-outline-danger btn-sm fw-bold" onclick="return confirm('Bersihkan semua data yang belum disetujui?')">
                        <i class="fas fa-eraser me-1"></i> Bersihkan Draft
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Alert notification -->
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-info-circle me-2"></i><?php echo htmlspecialchars($_GET['msg']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table id="atkTable" class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th>Pegawai</th>
                            <th>Nama Barang</th>
                            <th class="text-center">Jumlah</th>
                            <th>Satuan</th>
                            <th class="text-center">No Bukti</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)) { 
                            $is_approved = (strpos($row['keterangan'], 'Sudah Disetujui') !== false);
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_pegawai']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_barang_input']); ?></td>
                            <td class="text-center fw-bold text-danger"><?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                            <td><span class="badge bg-secondary opacity-75"><?php echo $row['satuan']; ?></span></td>
                            <td class="text-center">
                                <span class="no-bukti-badge"><?php echo str_pad($row['no_bukti'], 4, '0', STR_PAD_LEFT); ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($user_role == 'admin'): ?>
                                    <!-- Interactive Dropdown Status -->
                                    <div class="dropdown status-dropdown">
                                        <button class="btn btn-sm dropdown-toggle fw-bold <?php echo $is_approved ? 'btn-success' : 'btn-warning text-dark'; ?>" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <?php if ($is_approved): ?>
                                                <i class="fas fa-check-circle me-1"></i> Disetujui
                                            <?php else: ?>
                                                <i class="fas fa-clock me-1"></i> Draft
                                            <?php endif; ?>
                                        </button>
                                        <ul class="dropdown-menu shadow-sm border-0">
                                            <li><h6 class="dropdown-header">Ubah Status</h6></li>
                                            <li><a class="dropdown-item py-2" href="../DB/manage_data.php?action=acc&no_bukti=<?php echo $row['no_bukti']; ?>"><i class="fas fa-check text-success me-2"></i>Setujui (ACC)</a></li>
                                            <li><a class="dropdown-item py-2" href="../DB/manage_data.php?action=draft&no_bukti=<?php echo $row['no_bukti']; ?>"><i class="fas fa-history text-warning me-2"></i>Kembali ke Draft</a></li>
                                        </ul>
                                    </div>
                                <?php else: ?>
                                    <!-- Simple Badge for non-admins -->
                                    <?php if ($is_approved): ?>
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Disetujui</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Draft</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm">
                                    <?php if ($user_role == 'admin'): ?>
                                        <a href="../DB/manage_data.php?action=delete&no_bukti=<?php echo $row['no_bukti']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini secara permanen?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="generate_pdf.php?no_bukti=<?php echo $row['no_bukti']; ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Cetak PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Essential Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS is required for the dropdowns to open -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $("#atkTable").DataTable({
            "order": [[0, "desc"], [5, "desc"]],
            "pageLength": 15,
            "language": {
                "sSearch": "Cari Data:",
                "oPaginate": { "sPrevious": "Kembali", "sNext": "Selanjutnya" },
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data pengeluaran",
                "sLengthMenu": "Tampilkan _MENU_ data"
            }
        });
    });
</script>
</body>
</html> 