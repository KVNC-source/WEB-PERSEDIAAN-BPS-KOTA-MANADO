<?php
include '../DB/config.php';

// Query untuk menghitung stok secara real-time
// Rumus: Total Pemasukan - Total Pengeluaran (yang sudah disetujui)
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Stok | BPS KOTA MANADO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar navbar-bps shadow-sm mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">
      <img src="../asset/Logo BPS Kota Manado - All White.png" alt="BPS Logo" style="height: 50px;">
    </a>
    <div class="d-flex gap-2">
        <a href="index.php" class="btn btn-outline-light btn-sm">Pengeluaran</a>
        <a href="pemasukan.php" class="btn btn-outline-light btn-sm">Pemasukan</a>
        <a href="stok.php" class="btn btn-light text-primary btn-sm fw-bold">Data Stok</a>
        <a href="upload.php" class="btn btn-outline-light btn-sm">Input CSV</a>
    </div>
  </div>
</nav>

<div class="container-fluid px-4">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Monitoring Persediaan Barang</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="stokTable" class="table table-hover align-middle">
                    <thead class="table-light">
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
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="fw-medium"><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                            <td><span class="badge bg-secondary opacity-75"><?php echo $row['satuan']; ?></span></td>
                            <td class="text-center text-success fw-bold"><?php echo number_format($row['total_masuk'], 0, ',', '.'); ?></td>
                            <td class="text-center text-danger fw-bold"><?php echo number_format($row['total_keluar'], 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <span class="fs-5 fw-bold <?php echo ($row['sisa_stok'] <= 0) ? 'text-danger' : 'text-primary'; ?>">
                                    <?php echo number_format($row['sisa_stok'], 0, ',', '.'); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if($row['sisa_stok'] <= 0): ?>
                                    <span class="badge bg-danger">Habis</span>
                                <?php elseif($row['sisa_stok'] < 5): ?>
                                    <span class="badge bg-warning text-dark">Menipis</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Tersedia</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#stokTable').DataTable({
            pageLength: 25,
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' }
        });
    });
</script>
</body>
</html>