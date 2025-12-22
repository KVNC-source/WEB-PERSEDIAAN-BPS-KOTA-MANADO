<?php
include '../DB/config.php';
$query = "SELECT * FROM pengeluaran ORDER BY tanggal DESC, no_bukti DESC";
$result = mysqli_query($conn, $query);

// Simulasi Role: Ganti 'admin' ke 'pegawai' untuk tes tampilan user biasa
$user_role = 'admin'; 
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
        <?php if ($user_role == 'admin'): ?>
        <a href="upload.php" class="btn btn-warning btn-sm fw-bold shadow-sm">
            <i class="fas fa-plus-circle me-1"></i> Input Data
        </a>
        <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container-fluid px-4">
    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title text-primary m-0"><i class="fas fa-list me-2"></i>Data Pengeluaran Barang</h5>
                
                <?php if ($user_role == 'admin'): ?>
                <div class="text-end">
                    <a href="../DB/manage_data.php?action=delete_all_drafts" 
                       class="btn btn-danger btn-sm fw-bold shadow-sm" 
                       onclick="return confirm('Hapus SEMUA data yang masih berstatus Draft?')">
                        <i class="fas fa-trash-alt me-1"></i> Hapus Semua Draft
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <table id="atkTable" class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center">No Bukti</th>
                        <th>Tanggal</th>
                        <th>Pegawai</th>
                        <th>Nama Barang</th>
                        <th class="text-center">Satuan</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)) { 
                        $is_approved = (strpos($row['keterangan'], 'Sudah Disetujui') !== false);
                    ?>
                    <tr>
                        <td class="text-center fw-bold"><?php echo str_pad($row['no_bukti'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td><i class="far fa-calendar-alt me-1 text-muted"></i> <?php echo $row['tanggal']; ?></td>
                        <td class="fw-semibold"><?php echo $row['nama_pegawai']; ?></td>
                        <td><?php echo $row['nama_barang_input']; ?></td>
                        <td class="text-center"><span class="badge bg-light text-dark border"><?php echo $row['satuan']; ?></span></td>
                        <td class="text-center fw-bold"><?php echo $row['jumlah']; ?></td>
                        <td class="text-center">
                            <?php if ($is_approved): ?>
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Disetujui</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Draft</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <a href="generate_pdf.php?no_bukti=<?php echo $row['no_bukti']; ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Cetak">
                                    <i class="fas fa-print"></i>
                                </a>
                                <?php if ($user_role == 'admin'): ?>
                                    <?php if (!$is_approved): ?>
                                    <a href="../DB/manage_data.php?action=acc&no_bukti=<?php echo $row['no_bukti']; ?>" class="btn btn-sm btn-outline-success" onclick="return confirm('Setujui data ini?')" title="ACC">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="../DB/manage_data.php?action=delete&no_bukti=<?php echo $row['no_bukti']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini secara permanen?')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $("#atkTable").DataTable({
            "order": [[0, "desc"]],
            "pageLength": 15,
            "language": {
                "sSearch": "Cari Data:",
                "oPaginate": { "sPrevious": "Kembali", "sNext": "Lanjut" }
            }
        });
    });
</script>
</body>
</html>