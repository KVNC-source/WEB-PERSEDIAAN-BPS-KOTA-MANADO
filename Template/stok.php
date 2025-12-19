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
// Mendapatkan nama file saat ini untuk menentukan tombol mana yang aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-bps shadow-sm mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">
      <img src="../asset/Logo BPS Kota Manado - All White.png" 
           alt="BPS Manado Logo" 
           class="navbar-logo-white">
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
        <a href="upload.php" class="btn btn-warning btn-sm fw-bold">
            <i class="fas fa-plus-circle me-1"></i> Input Data
        </a>
    </div>
  </div>
</nav>

    <div class="container-fluid px-4">
      <div class="card shadow">
        <div class="card-body">
          <h5 class="card-title text-primary"><i class="fas fa-boxes me-2"></i>Posisi Stok Persediaan</h5>
          <table id="stokTable" class="table table-striped table-hover align-middle">
            <thead>
              <tr>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Total Masuk</th>
                <th>Total Keluar</th>
                <th>Sisa Stok</th>
              </tr>
            </thead>
            <tbody>
              <?php while($row = mysqli_fetch_assoc($result)) { 
                  // Highlight low stock (less than 5)
                  $lowStockClass = ($row['sisa_stok'] <= 5) ? 'text-danger fw-bold' : '';
              ?>
              <tr>
                <td><?php echo $row['nama_barang']; ?></td>
                <td><?php echo $row['satuan']; ?></td>
                <td class="text-center"><?php echo $row['total_masuk']; ?></td>
                <td class="text-center"><?php echo $row['total_keluar']; ?></td>
                <td class="text-center <?php echo $lowStockClass; ?>">
                    <?php echo $row['sisa_stok']; ?>
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
        "pageLength": 25, // Menampilkan lebih banyak baris untuk inventory
        "language": {
            "sLengthMenu":   "Tampilkan _MENU_ jenis barang",
            "sZeroRecords":  "Data stok tidak tersedia",
            "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ jenis barang",
            "sInfoEmpty":    "Tidak ada data stok untuk ditampilkan",
            "sSearch":       "Cari Nama Barang:",
            "oPaginate": {
                "sPrevious": "Kembali",
                "sNext":     "Lanjut"
            }
        }
    });
});
    </script>
</body>
</html>