<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

include 'DB/config.php';

// Dropdown data
$barang_query = mysqli_query(
    $conn,
    "SELECT id_barang, nama_barang, satuan 
     FROM barang_atk 
     ORDER BY nama_barang ASC"
);

$pegawai_query = mysqli_query(
    $conn,
    "SELECT id_pegawai, nama_pegawai 
     FROM pegawai 
     ORDER BY nama_pegawai ASC"
);

$current_date = date('Y-m-d');

include 'partials/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="form-card">

            <div class="text-center mb-4">
                <img src="asset/Logo BPS Kota Manado - All White.png"
                     alt="Logo"
                     style="height:45px; filter:brightness(0) saturate(100%) invert(13%) sepia(37%) saturate(4208%) hue-rotate(195deg) brightness(92%) contrast(106%);">
                <h2 class="hero-title-dark mt-3">Form Permintaan</h2>
                <p class="text-muted small">
                    Input laporan pengambilan ATK BPS Kota Manado
                </p>
            </div>

            <form action="proses_permintaan.php" method="POST">

                <div class="mb-3">
                    <label class="form-label">Tanggal Pengambilan</label>
                    <input type="date"
                           name="tanggal"
                           class="form-control"
                           value="<?= $current_date ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Pegawai</label>
                    <select name="id_pegawai" class="form-select" required>
                        <option value="" disabled selected>Pilih nama Anda...</option>
                        <?php while ($p = mysqli_fetch_assoc($pegawai_query)): ?>
                            <option value="<?= $p['id_pegawai'] ?>">
                                <?= htmlspecialchars($p['nama_pegawai']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Barang yang Diambil</label>
                    <select name="id_barang" class="form-select" required>
                        <option value="" disabled selected>Pilih barang...</option>
                        <?php while ($b = mysqli_fetch_assoc($barang_query)): ?>
                            <option value="<?= $b['id_barang'] ?>">
                                <?= htmlspecialchars($b['nama_barang']) ?> (<?= $b['satuan'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah Barang</label>
                    <input type="number"
                           name="jumlah"
                           class="form-control"
                           min="1"
                           placeholder="Masukkan jumlah"
                           required>
                </div>

                <button type="submit" class="btn btn-submit shadow-sm">
                    Kirim Laporan <i class="fas fa-paper-plane ms-2"></i>
                </button>

            </form>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
                            