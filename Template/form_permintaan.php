<?php
include '../DB/config.php';

// Ambil daftar barang untuk dropdown
$barang_query = mysqli_query($conn, "SELECT id_barang, nama_barang, satuan FROM barang_atk ORDER BY nama_barang ASC");

// Ambil daftar pegawai dari tabel pegawai untuk dropdown
// Asumsi: tabel bernama 'pegawai' dengan kolom 'nama_pegawai'
$pegawai_query = mysqli_query($conn, "SELECT id_pegawai, nama_pegawai FROM pegawai ORDER BY nama_pegawai ASC");

$current_date = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>PRISMA | Form Permintaan Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --deep-navy: #002d5a;
        }
        body {
            background: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .form-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 550px;
            box-shadow: 0 10px 25px rgba(0, 45, 90, 0.05);
            border: 1px solid #e2e8f0;
        }
        .form-label {
            font-weight: 700;
            color: var(--deep-navy);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--deep-navy);
            box-shadow: 0 0 0 4px rgba(0, 45, 90, 0.1);
            background: #fff;
        }
        .btn-submit {
            background: var(--deep-navy);
            color: white;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px;
            border-radius: 14px;
            border: none;
            width: 100%;
            margin-top: 25px;
            transition: 0.3s;
        }
        .btn-submit:hover {
            background: #004488;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 45, 90, 0.15);
        }
        .hero-title-dark {
            font-weight: 800;
            color: var(--deep-navy);
            font-size: 1.75rem;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="form-card">
    <div class="text-center mb-4">
        <img src="../asset/Logo BPS Kota Manado - All White.png" alt="Logo" style="height: 45px; filter: brightness(0) saturate(100%) invert(13%) sepia(37%) saturate(4208%) hue-rotate(195deg) brightness(92%) contrast(106%);">
        <h2 class="hero-title-dark mt-3">Form Permintaan</h2>
        <p class="text-muted small">Input laporan pengambilan ATK BPS Kota Manado</p>
    </div>

    <form action="proses_permintaan.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Tanggal Pengambilan</label>
            <input type="date" name="tanggal" class="form-control" value="<?= $current_date ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Pegawai</label>
            <select name="nama_pegawai" class="form-select" required>
                <option value="" selected disabled>Pilih nama Anda...</option>
                <?php while($p = mysqli_fetch_assoc($pegawai_query)): ?>
                    <option value="<?= htmlspecialchars($p['nama_pegawai']) ?>">
                        <?= htmlspecialchars($p['nama_pegawai']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Barang yang Diambil</label>
            <select name="id_barang" class="form-select" required>
                <option value="" selected disabled>Pilih barang...</option>
                <?php while($b = mysqli_fetch_assoc($barang_query)): ?>
                    <option value="<?= $b['id_barang'] ?>">
                        <?= htmlspecialchars($b['nama_barang']) ?> (<?= $b['satuan'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Jumlah Barang</label>
            <input type="number" name="jumlah" class="form-control" placeholder="Masukkan volume angka" min="1" required>
        </div>

        <button type="submit" class="btn btn-submit shadow-sm">
            Kirim Laporan <i class="fas fa-paper-plane ms-2"></i>
        </button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>