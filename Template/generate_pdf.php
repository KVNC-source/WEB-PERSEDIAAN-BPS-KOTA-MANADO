<?php
ob_start();

session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

include '../DB/config.php';

$no_bukti = isset($_GET['no_bukti']) ? (int)$_GET['no_bukti'] : 0;
$query = "SELECT * FROM pengeluaran WHERE no_bukti = $no_bukti";
$result = mysqli_query($conn, $query);
$items = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (empty($items)) {
    die("Data tidak ditemukan.");
}

// Variabel tahun tetap diambil otomatis untuk identitas dokumen
$tahun = date('Y', strtotime($items[0]['tanggal']));

// 🔹 NAMA PENGAMBIL (AUTO DARI DB)
$nama_pengambil = $items[0]['nama_pegawai'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form BPS Manado - Official Print</title>
    <style>
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 11pt; 
            margin: 0.5in; 
            color: #000; 
            position: relative;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .imprint-container { 
            width: 100%; 
            margin-bottom: 5px; 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start; 
        }

        .logo-section { display: flex; align-items: center; }
        .logo-img { 
            width: 65px; 
            height: auto; 
            margin-right: 12px; 
            opacity: 0.8; 
        }
        .bps-text-container { display: flex; flex-direction: column; }
        .bps-text-line { 
            font-family: Arial, Helvetica, sans-serif !important; 
            color: #8db4d9; 
            font-weight: bold; 
            font-size: 15pt; 
            line-height: 1.1; 
            font-style: italic;
            letter-spacing: 0.5px;
        }

        .header-info { 
            width: auto; 
            min-width: 140px; 
            margin-top: 55px; 
        }
        .header-info table { border-collapse: collapse; width: 100%; }
        .header-info td { border: none; padding: 1px 0; font-size: 11pt; text-align: left; }
        .header-info td:first-child { width: 50px; } 

        .title-block { 
            text-align: center; 
            font-weight: bold; 
            margin-top: 20px; 
            margin-bottom: 20px; 
            font-size: 12pt; 
        }
        
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; background: transparent; }
        .main-table th, .main-table td { border: 1px solid black; padding: 6px; text-align: center; }
        .main-table th { font-weight: bold; }

        .footer-section { 
            width: 100%; 
            margin-top: 15px; 
        }
        .manado-line { 
            text-align: right; 
            margin-bottom: 25px; 
            padding-right: 40px; 
        }
        .sig-container { 
            display: flex; 
            justify-content: space-between; 
            padding: 0 20px;
        }
        .sig-box-left { text-align: center; width: 30%; }
        .sig-box-right { text-align: center; width: 60%; }
        .sig-space { height: 80px; } 
        
        .sig-approver-grid { 
            display: flex; 
            justify-content: space-between; 
            width: 100%; 
            margin-top: -10px; 
        }
        .sig-approver-grid span { width: 50%; }

        @media print { 
            .no-print { display: none; } 
            body { -webkit-print-color-adjust: exact; }
            .logo-img { opacity: 0.8 !important; }
        }
    </style>
</head>
<body>

<div class="imprint-container">
    <div class="logo-section">
        <img src="../asset/LOGO.png" class="logo-img" alt="BPS Logo">
        <div class="bps-text-container">
            <div class="bps-text-line">BADAN PUSAT STATISTIK</div>
            <div class="bps-text-line">KOTA MANADO</div>
        </div>
    </div>

    <div class="header-info">
        <table>
            <tr>
                <td>No</td>
                <td>: <?= str_pad($no_bukti, 3, '0', STR_PAD_LEFT); ?></td>
            </tr>
            <tr>
                <td>Tahun</td>
                <td>: <?= $tahun; ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="title-block">
    FORM PENGAMBILAN BARANG<br>
    ATK/ARK PERSEDIAAN
</div>  

<table class="main-table">
    <thead>
        <tr>
            <th rowspan="2" style="width: 40px;">No.</th>
            <th rowspan="2">Nama Barang</th>
            <th rowspan="2" style="width: 120px;">Satuan barang</th>
            <th colspan="2" style="width: 160px;">Jumlah</th>
            <th rowspan="2" style="width: 120px;">Keterangan</th>
        </tr>
        <tr>
            <th>Dimintai</th>
            <th>Disetujui</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        for ($i = 0; $i < 15; $i++) { 
            $item = $items[$i] ?? null;
            echo '<tr>';
            echo '<td>' . ($i + 1) . '</td>';
            echo '<td style="text-align: left;">' . ($item ? htmlspecialchars($item['nama_barang_input']) : '') . '</td>';
            echo '<td>' . ($item ? htmlspecialchars($item['satuan']) : '') . '</td>';
            echo '<td>' . ($item ? htmlspecialchars($item['jumlah']) : '') . '</td>';
            echo '<td>' . ($item ? htmlspecialchars($item['jumlah']) : '') . '</td>';
            echo '<td>' . ($item ? htmlspecialchars($item['keterangan']) : '') . '</td>';
            echo '</tr>';
        } 
        ?>
    </tbody>
</table>

<div class="footer-section">
    <div class="manado-line">
        Manado, ............................................
    </div>
    
    <div class="sig-container">
        <div class="sig-box-left">
            <div>Diambil oleh,</div>
            <div class="sig-space"></div>
            <div>( <?= htmlspecialchars($nama_pengambil); ?> )</div>
        </div>
        
        <div class="sig-box-right">
            <div>Disetujui oleh</div>
            <div class="sig-space"></div>
            <div class="sig-approver-grid">
                <span>Kasubbag Umum</span>
                <span>Operator Persediaan</span>
            </div>
        </div>
    </div>
</div>

<div class="no-print" style="text-align: center; margin-top: 30px;">
    <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; font-family: Arial;">
        PRINT FORM
    </button>
</div>

</body>
</html>

<?php
$html = ob_get_clean();

$dir = __DIR__ . '/laporan';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$filename = 'pengeluaran_' . $no_bukti . '_' . date('Y-m-d_H-i-s') . '.html';
file_put_contents($dir . '/' . $filename, $html);

echo $html;
