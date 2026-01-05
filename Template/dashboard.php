<?php
include '../DB/config.php';

// Set current date context
$today = date('Y-m-d');
$currentMonth = date('Y-m');
$current_page = basename($_SERVER['PHP_SELF']);

/**
 * LOGIC: Carry-forward date assignment for Summary Stats
 */
$allPemasukan = mysqli_query($conn, "SELECT tanggal, jumlah FROM pemasukan ORDER BY id_pemasukan ASC");
$masuk_recent = 0;
$last_valid_date_in = null;
$incomingTrendData = array_fill(0, 6, 0);
$monthsLabel = [];

for ($i = 5; $i >= 0; $i--) {
    $monthsLabel[] = date('M Y', strtotime("-$i months"));
}

while ($row = mysqli_fetch_assoc($allPemasukan)) {
    if (!empty($row['tanggal']) && $row['tanggal'] != '0000-00-00') {
        $last_valid_date_in = $row['tanggal'];
    }
    if ($last_valid_date_in) {
        $rowMonth = date('Y-m', strtotime($last_valid_date_in));
        $rowLabel = date('M Y', strtotime($last_valid_date_in));
        if ($rowMonth == $currentMonth) { $masuk_recent += (float)$row['jumlah']; }
        $idx = array_search($rowLabel, $monthsLabel);
        if ($idx !== false) { $incomingTrendData[$idx] += (float)$row['jumlah']; }
    }
}

$allPengeluaran = mysqli_query($conn, "SELECT tanggal, jumlah FROM pengeluaran WHERE keterangan LIKE '%Sudah Disetujui%' ORDER BY no_bukti ASC");
$keluar_recent = 0;
$last_valid_date_out = null;
$outgoingTrendData = array_fill(0, 6, 0);

while ($row = mysqli_fetch_assoc($allPengeluaran)) {
    if (!empty($row['tanggal']) && $row['tanggal'] != '0000-00-00') {
        $last_valid_date_out = $row['tanggal'];
    }
    if ($last_valid_date_out) {
        $rowMonth = date('Y-m', strtotime($last_valid_date_out));
        $rowLabel = date('M Y', strtotime($last_valid_date_out));
        if ($rowMonth == $currentMonth) { $keluar_recent += (float)$row['jumlah']; }
        $idx = array_search($rowLabel, $monthsLabel);
        if ($idx !== false) { $outgoingTrendData[$idx] += (float)$row['jumlah']; }
    }
}

$catalogRes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM barang_atk"));
$total_catalog = $catalogRes['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BPS KOTA MANADO | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --deep-navy: #002d5a; /* Darker blue for text */
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        .neumorphic-card {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 20px 20px 60px #bebebe, -20px -20px 60px #ffffff;
            transition: transform 0.3s ease;
        }

        .hero-title {
            font-weight: 800;
            background: linear-gradient(to right, var(--deep-navy), #005bb5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2.5rem;
        }

        .text-dark-navy {
            color: var(--deep-navy) !important;
        }

        .icon-box-stat {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: inset 4px 4px 8px rgba(0,0,0,0.05);
        }

        .label-pill {
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .chart-container-neo {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 24px;
            padding: 35px;
            border: 1px solid #fff;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-bps shadow-sm mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">
      <img src="../asset/Logo BPS Kota Manado - All White.png" alt="BPS Logo" style="height: 50px;">
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

<div class="container-fluid px-4 py-4">
    <div class="hero-section text-center py-5">
        <h1 class="hero-title mb-1">Analitik Persediaan</h1>
        <p class="text-dark-navy fw-semibold">Visualisasi real-time arus barang masuk dan keluar BPS Kota Manado</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="neumorphic-card d-flex align-items-center">
                <div class="icon-box-stat bg-success bg-opacity-10 text-success me-4">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div>
                    <span class="label-pill bg-success bg-opacity-10 text-success mb-2 d-inline-block">Masuk Bulan Ini</span>
                    <h3 class="fw-extrabold text-dark-navy mb-0"><?= number_format($masuk_recent, 0, ',', '.') ?></h3>
                    <small class="text-muted fw-bold">Total unit barang masuk</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="neumorphic-card d-flex align-items-center">
                <div class="icon-box-stat bg-danger bg-opacity-10 text-danger me-4">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div>
                    <span class="label-pill bg-danger bg-opacity-10 text-danger mb-2 d-inline-block">Keluar Bulan Ini</span>
                    <h3 class="fw-extrabold text-dark-navy mb-0"><?= number_format($keluar_recent, 0, ',', '.') ?></h3>
                    <small class="text-muted fw-bold">Total unit barang keluar</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="neumorphic-card d-flex align-items-center">
                <div class="icon-box-stat bg-primary bg-opacity-10 text-dark-navy me-4">
                    <i class="fas fa-boxes"></i>
                </div>
                <div>
                    <span class="label-pill bg-primary bg-opacity-10 text-dark-navy mb-2 d-inline-block">Katalog Item</span>
                    <h3 class="fw-extrabold text-dark-navy mb-0"><?= $total_catalog ?></h3>
                    <small class="text-muted fw-bold">Jenis barang terdaftar</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="neumorphic-card chart-container-neo shadow-lg">
                <div class="d-flex justify-content-between align-items-center mb-5 px-2">
                    <h5 class="fw-extrabold text-dark-navy mb-0">Tren Volume Barang (6 Bulan Terakhir)</h5>
                    <div class="d-flex gap-4">
                        <div class="small fw-extrabold text-success"><i class="fas fa-circle me-2"></i> Masuk</div>
                        <div class="small fw-extrabold text-danger"><i class="fas fa-circle me-2"></i> Keluar</div>
                    </div>
                </div>
                <div style="height: 400px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('trendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($monthsLabel) ?>,
            datasets: [
                {
                    label: 'Unit Masuk',
                    data: <?= json_encode($incomingTrendData) ?>,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.05)',
                    borderWidth: 5,
                    fill: true,
                    tension: 0.45,
                    pointRadius: 6,
                    pointHoverRadius: 9,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#198754',
                    pointBorderWidth: 4
                },
                {
                    label: 'Unit Keluar',
                    data: <?= json_encode($outgoingTrendData) ?>,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.05)',
                    borderWidth: 5,
                    fill: true,
                    tension: 0.45,
                    pointRadius: 6,
                    pointHoverRadius: 9,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#dc3545',
                    pointBorderWidth: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: 'rgba(0,0,0,0.03)' }, 
                    ticks: { 
                        color: '#002d5a', /* Darker Y-Axis Ticks */
                        font: { weight: '700', family: 'Plus Jakarta Sans', size: 12 } 
                    } 
                },
                x: { 
                    grid: { display: false }, 
                    ticks: { 
                        color: '#002d5a', /* Darker X-Axis Ticks */
                        font: { weight: '700', family: 'Plus Jakarta Sans', size: 12 } 
                    } 
                }
            }
        }
    });
</script>
</body>
</html>