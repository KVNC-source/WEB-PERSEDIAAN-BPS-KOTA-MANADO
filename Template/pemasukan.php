<?php 
include '../DB/config.php'; 
// Join with barang_atk to get official names if needed, though nama_barang_input is already in pemasukan table
$query = "SELECT * FROM pemasukan ORDER BY tanggal DESC, id_pemasukan DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pemasukan Barang | BPS Kota Manado</title>
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
              <h5 class="card-title text-primary"><i class="fas fa-file-import me-2"></i>Riwayat Pemasukan Barang</h5>
          </div>
          <table id="pemasukanTable" class="table table-striped table-hover align-middle">
            <thead>
              <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th class="text-center">Jumlah</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php while($row = mysqli_fetch_assoc($result)) { ?>
              <tr>
                <td class="text-center fw-bold">
                  <?php echo str_pad($row['id_pemasukan'], 3, '0', STR_PAD_LEFT); ?>
                </td>
                <td><i class="far fa-calendar-alt me-1 text-muted"></i> <?php echo $row['tanggal']; ?></td>
                <td>
                  <?php echo $row['nama_barang_input']; ?> 
                  <small class="text-muted">(<?php echo $row['satuan']; ?>)</small>
                </td>
                <td class="text-center fw-bold text-success">
                  +<?php echo $row['jumlah']; ?>
                </td>
                <td><small class="text-muted"><?php echo $row['keterangan']; ?></small></td>
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
        $("#pemasukanTable").DataTable({
            "order": [[0, "desc"]], // Urutkan berdasarkan ID terbaru
            "pageLength": 15,
            "language": {
                "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
                "sProcessing":   "Sedang memproses...",
                "sLengthMenu":   "Tampilkan _MENU_ data",
                "sZeroRecords":  "Riwayat pemasukan tidak ditemukan",
                "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ data pemasukan",
                "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 data",
                "sInfoFiltered": "(disaring dari _MAX_ total data)",
                "sSearch":       "Cari Data:",
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