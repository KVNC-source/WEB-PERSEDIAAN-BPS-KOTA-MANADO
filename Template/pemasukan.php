<?php 
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
                <i class="fas fa-arrow-down me-2"></i>Riwayat Pemasukan Barang
            </h4>
        </div>
        
        <div class="table-responsive">
          <table id="pemasukanTable" class="table table-striped table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th class="text-center">Jumlah</th>
                <th>Satuan</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php while($row = mysqli_fetch_assoc($result)) { ?>
              <tr>
                <td class="fw-bold"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                <td><?php echo htmlspecialchars($row['nama_barang_input']); ?></td>
                <td class="text-center fw-bold text-success"><?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                <td><span class="badge bg-secondary opacity-75"><?php echo $row['satuan']; ?></span></td>
                <td><small class="text-muted"><?php echo $row['keterangan']; ?></small></td>
              </tr>
              <?php } ?>
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
    $(document).ready(function () {
        $("#pemasukanTable").DataTable({
            "order": [[0, "desc"]],
            "pageLength": 15,
            "language": {
                "sSearch": "Cari Data:",
                "oPaginate": { "sPrevious": "Kembali", "sNext": "Selanjutnya" },
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data pemasukan",
                "sLengthMenu": "Tampilkan _MENU_ data"
            }
        });
    });
</script>
</body>
</html>