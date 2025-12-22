<?php 
include '../DB/config.php'; 

/** * SQL Logic:
 * 1. SUM total quantities from 'pemasukan' table
 * 2. SUM total quantities from 'pengeluaran' table
 * 3. Subtract (Pemasukan - Pengeluaran) to get Current Stock
 */
$query = "SELECT 
            b.id_barang,
            b.nama_barang, 
            b.satuan, 
            IFNULL(m.total_masuk, 0) as total_masuk,
            IFNULL(p.total_keluar, 0) as total_keluar,
            (IFNULL(m.total_masuk, 0) - IFNULL(p.total_keluar, 0)) as sisa_stok
          FROM barang_atk b
          LEFT JOIN (
              SELECT id_barang, SUM(jumlah) as total_masuk 
              FROM pemasukan GROUP BY id_barang
          ) m ON b.id_barang = m.id_barang
          LEFT JOIN (
              SELECT id_barang, SUM(jumlah) as total_keluar 
              FROM pengeluaran GROUP BY id_barang
          ) p ON b.id_barang = p.id_barang
          HAVING sisa_stok != 0 OR total_masuk != 0
          ORDER BY b.nama_barang ASC";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Stok Persediaan | BPS Kota Manado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php
// Simulasi Role: Ganti ke 'pegawai' untuk mencoba tampilan sebagai pegawai
$user_role = 'admin'; 
$current_page = basename($_SERVER['PHP_SELF']);
?>
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
              <h5 class="card-title text-primary"><i class="fas fa-boxes me-2"></i>Posisi Stok Persediaan</h5>
          </div>
          <table id="stokTable" class="table table-striped table-hover align-middle">
  <thead>
    <tr>
      <th>Nama Barang</th>
      <th>Satuan</th> <th class="text-center">Total Masuk</th>
      <th class="text-center">Total Keluar</th>
      <th class="text-center">Sisa Stok</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = mysqli_fetch_assoc($result)) { 
        $stockBadge = ($row['sisa_stok'] <= 0) ? "bg-danger" : (($row['sisa_stok'] <= 5) ? "bg-warning text-dark" : "bg-primary");
    ?>
    <tr>
      <td class="fw-semibold"><?php echo $row['nama_barang']; ?></td>
      <td class="text-center"><?php echo $row['satuan']; ?></td>
      <td class="text-center text-success fw-bold"><?php echo $row['total_masuk']; ?></td>
      <td class="text-center text-danger fw-bold"><?php echo $row['total_keluar']; ?></td>
      <td class="text-center">
          <span class="badge <?php echo $stockBadge; ?> fs-6" style="min-width: 45px;">
              <?php echo $row['sisa_stok']; ?>
          </span>
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
        "pageLength": 25,
        "language": {
            "sEmptyTable":   "Tidak ada data stok yang tersedia",
            "sProcessing":   "Sedang memproses...",
            "sLengthMenu":   "Tampilkan _MENU_ jenis barang",
            "sZeroRecords":  "Data stok tidak ditemukan",
            "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ jenis barang",
            "sInfoEmpty":    "Tidak ada data stok untuk ditampilkan",
            "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "sSearch":       "Cari Nama Barang:",
            "oPaginate": {
                "sFirst":    "Pertama",
                "sPrevious": "Kembali",
                "sNext":     "Lanjut",
                "sLast":     "Terakhir"
            }
        }
    });
});
    </script>
</body>
</html>