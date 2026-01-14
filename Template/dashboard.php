<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

include '../DB/config.php';


$currentMonth = date('Y-m');
$current_page = basename($_SERVER['PHP_SELF']);

/**
 * ============================
 * SUMMARY STATISTICS LOGIC
 * ============================
 */

// Prepare last 6 month labels
$monthsLabel = [];
for ($i = 5; $i >= 0; $i--) {
    $monthsLabel[] = date('M Y', strtotime("-$i months"));
}

/**
 * ===== PEMASUKAN =====
 */
$allPemasukan = mysqli_query($conn, "SELECT tanggal, jumlah FROM pemasukan ORDER BY id_pemasukan ASC");
$masuk_recent = 0;
$last_valid_date_in = null;
$incomingTrendData = array_fill(0, 6, 0);

while ($row = mysqli_fetch_assoc($allPemasukan)) {
    if (!empty($row['tanggal']) && $row['tanggal'] !== '0000-00-00') {
        $last_valid_date_in = $row['tanggal'];
    }

    if ($last_valid_date_in) {
        $rowMonth = date('Y-m', strtotime($last_valid_date_in));
        $rowLabel = date('M Y', strtotime($last_valid_date_in));

        if ($rowMonth === $currentMonth) {
            $masuk_recent += (float)$row['jumlah'];
        }

        $idx = array_search($rowLabel, $monthsLabel);
        if ($idx !== false) {
            $incomingTrendData[$idx] += (float)$row['jumlah'];
        }
    }
}

/**
 * ===== PENGELUARAN =====
 */
$allPengeluaran = mysqli_query(
    $conn,
    "SELECT tanggal, jumlah FROM pengeluaran 
     WHERE keterangan LIKE '%Sudah Disetujui%' 
     ORDER BY no_bukti ASC"
);

$keluar_recent = 0;
$last_valid_date_out = null;
$outgoingTrendData = array_fill(0, 6, 0);

while ($row = mysqli_fetch_assoc($allPengeluaran)) {
    if (!empty($row['tanggal']) && $row['tanggal'] !== '0000-00-00') {
        $last_valid_date_out = $row['tanggal'];
    }

    if ($last_valid_date_out) {
        $rowMonth = date('Y-m', strtotime($last_valid_date_out));
        $rowLabel = date('M Y', strtotime($last_valid_date_out));

        if ($rowMonth === $currentMonth) {
            $keluar_recent += (float)$row['jumlah'];
        }

        $idx = array_search($rowLabel, $monthsLabel);
        if ($idx !== false) {
            $outgoingTrendData[$idx] += (float)$row['jumlah'];
        }
    }
}

/**
 * ===== KATALOG =====
 */
$total_catalog = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang_atk")
)['total'];

include 'partials/header.php';
?>

<div class="hero-section text-center py-5">
    <h1 class="hero-title mb-1">Analitik Persediaan</h1>
    <p class="text-dark-navy fw-semibold">
        Visualisasi real-time arus barang masuk dan keluar
    </p>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="neumorphic-card d-flex align-items-center">
            <div class="icon-box-stat bg-success bg-opacity-10 text-success me-4">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div>
                <span class="label-pill bg-success bg-opacity-10 text-success">Masuk Bulan Ini</span>
                <h3 class="fw-extrabold mb-0"><?= number_format($masuk_recent, 0, ',', '.') ?></h3>
                <small class="text-muted fw-bold">Unit barang masuk</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="neumorphic-card d-flex align-items-center">
            <div class="icon-box-stat bg-danger bg-opacity-10 text-danger me-4">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div>
                <span class="label-pill bg-danger bg-opacity-10 text-danger">Keluar Bulan Ini</span>
                <h3 class="fw-extrabold mb-0"><?= number_format($keluar_recent, 0, ',', '.') ?></h3>
                <small class="text-muted fw-bold">Unit barang keluar</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="neumorphic-card d-flex align-items-center">
            <div class="icon-box-stat bg-primary bg-opacity-10 text-primary me-4">
                <i class="fas fa-boxes"></i>
            </div>
            <div>
                <span class="label-pill bg-primary bg-opacity-10 text-primary">Katalog Item</span>
                <h3 class="fw-extrabold mb-0"><?= $total_catalog ?></h3>
                <small class="text-muted fw-bold">Jenis barang</small>
                <a href="bulk_download_all.php" class="btn btn-danger">
    ⬇ Download Semua Laporan (Semua Bulan)
</a>

            </div>
        </div>
    </div>
</div>

<div class="neumorphic-card chart-container-neo">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-extrabold mb-0">Tren Volume Barang (6 Bulan)</h5>
        <div class="d-flex gap-3">
            <span class="fw-bold text-success">● Masuk</span>
            <span class="fw-bold text-danger">● Keluar</span>
        </div>
    </div>
    <div style="height:400px;">
        <canvas id="trendChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('trendChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($monthsLabel) ?>,
        datasets: [
            {
                label: 'Masuk',
                data: <?= json_encode($incomingTrendData) ?>,
                borderColor: '#198754',
                backgroundColor: 'rgba(25,135,84,0.05)',
                fill: true,
                tension: 0.45
            },
            {
                label: 'Keluar',
                data: <?= json_encode($outgoingTrendData) ?>,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220,53,69,0.05)',
                fill: true,
                tension: 0.45
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true },
            x: { grid: { display: false } }
        }
    }
});
</script>

<?php include 'partials/footer.php'; ?>
