<?php
include '../DB/config.php';
$query = "SELECT * FROM pengeluaran ORDER BY tanggal DESC, no_bukti DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BPS KOTA MANADO | Dashboard Persediaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php
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
          <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="card-title text-primary"><i class="fas fa-list me-2"></i>Data Pengeluaran Barang</h5>
          </div>
          <table id="atkTable" class="table table-striped table-hover align-middle">
            <thead>
              <tr>
                <th>No Bukti</th>
                <th>Tanggal</th>
                <th>Pegawai</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php while($row = mysqli_fetch_assoc($result)) { ?>
              <tr>
                <td class="text-center">
                  <span class="no-bukti-badge">
                    <?php echo str_pad($row['no_bukti'], 3, '0', STR_PAD_LEFT); ?>
                  </span>
                </td>
                <td><i class="far fa-calendar-alt me-1 text-muted"></i> <?php echo $row['tanggal']; ?></td>
                <td class="fw-semibold"><?php echo $row['nama_pegawai']; ?></td>
                <td><?php echo $row['nama_barang_input']; ?> <small class="text-muted">(<?php echo $row['satuan']; ?>)</small></td>
                <td class="text-center fw-bold"><?php echo $row['jumlah']; ?></td>
                <td class="text-center">
                  <?php if (strpos($row['keterangan'], 'Sudah Disetujui') !== false) { ?>
                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Disetujui</span>
                  <?php } else { ?>
                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Draft</span>
                  <?php } ?>
                </td>
                <td class="text-center">
                  <a href="generate_pdf.php?no_bukti=<?php echo $row['no_bukti']; ?>" 
                     target="_blank" class="btn btn-sm btn-outline-primary shadow-sm">
                    <i class="fas fa-print"></i> Cetak
                  </a>
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
        "order": [[0, "desc"]], // Urutkan berdasarkan No Bukti
        "pageLength": 15, // Menampilkan 15 data per halaman
        "language": {
            "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
            "sProcessing":   "Sedang memproses...",
            "sLengthMenu":   "Tampilkan _MENU_ data",
            "sZeroRecords":  "Tidak ditemukan data yang sesuai",
            "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 data",
            "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "sInfoPostFix":  "",
            "sSearch":       "Cari Data:",
            "sUrl":          "",
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