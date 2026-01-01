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
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title text-primary fw-bold">
                    <i class="fas fa-boxes me-2"></i>Monitoring Persediaan Barang
                </h4>
            </div>

            <table id="stokTable" class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th class="text-center">Total Masuk</th>
                        <th class="text-center">Total Keluar</th>
                        <th class="text-center">Sisa Stok</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                        <td><span class="badge bg-secondary opacity-75"><?php echo htmlspecialchars($row['satuan']); ?></span></td>
                        <td class="text-center text-success fw-bold"><?php echo number_format($row['total_masuk'], 0, ',', '.'); ?></td>
                        <td class="text-center text-danger fw-bold"><?php echo number_format($row['total_keluar'], 0, ',', '.'); ?></td>
                        <td class="text-center">
                            <span class="fs-6 fw-bold <?php echo ($row['sisa_stok'] <= 0) ? 'text-danger' : 'text-primary'; ?>">
                                <?php echo number_format($row['sisa_stok'], 0, ',', '.'); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if($row['sisa_stok'] <= 0): ?>
                                <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Habis</span>
                            <?php elseif($row['sisa_stok'] < 5): ?>
                                <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i>Menipis</span>
                            <?php else: ?>
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Tersedia</span>
                            <?php endif; ?>
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
        $("#stokTable").DataTable({
            "pageLength": 15,
            "language": {
                "sSearch": "Cari Barang:",
                "oPaginate": { "sPrevious": "Kembali", "sNext": "Selanjutnya" },
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ barang",
                "sLengthMenu": "Tampilkan _MENU_ barang"
            }
        });
    });
</script>
</body>
</html>