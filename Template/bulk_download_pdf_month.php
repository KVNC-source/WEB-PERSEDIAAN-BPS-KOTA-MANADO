<?php
session_start();

if (!isset($_SESSION['user'])) {
    exit('Unauthorized');
}

require_once __DIR__ . '/../DB/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Mpdf\Mpdf;

/*
|--------------------------------------------------------------------------
| INPUT: MONTH (YYYY-MM)
|--------------------------------------------------------------------------
*/
$month = $_GET['month'] ?? '';

if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
    exit('Format bulan tidak valid. Gunakan YYYY-MM');
}

/*
|--------------------------------------------------------------------------
| GET ALL NO_BUKTI FOR THAT MONTH
|--------------------------------------------------------------------------
*/
$query = "
    SELECT DISTINCT no_bukti
    FROM pengeluaran
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$month'
    ORDER BY no_bukti ASC
";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    exit('Tidak ada data untuk bulan tersebut.');
}

/*
|--------------------------------------------------------------------------
| ZIP SETUP
|--------------------------------------------------------------------------
*/
if (!class_exists('ZipArchive')) {
    exit('ZIP extension tidak tersedia.');
}

$zipName = "LAPORAN_PENGELUARAN_$month.zip";
$zipPath = sys_get_temp_dir() . '/' . $zipName;

$zip = new ZipArchive();
$zip->open($zipPath, ZipArchive::CREATE);

/*
|--------------------------------------------------------------------------
| LOOP → CALL generate_pdf.php → CAPTURE HTML → PDF
|--------------------------------------------------------------------------
*/
while ($row = mysqli_fetch_assoc($result)) {

    $no_bukti = (int)$row['no_bukti'];

    // 1️⃣ Capture HTML output EXACTLY as browser sees it
    ob_start();
    $_GET['no_bukti'] = $no_bukti;
    include __DIR__ . '/generate_pdf.php';
    $html = ob_get_clean();

    // 2️⃣ Convert HTML → PDF (exact CSS rendering)
    $mpdf = new Mpdf([
        'format' => 'A4',
        'default_font' => 'Times New Roman'
    ]);

    $mpdf->WriteHTML($html);

    // 3️⃣ Get PDF as string
    $pdfContent = $mpdf->Output('', 'S');

    // 4️⃣ Add to ZIP
    $filename = 'pengeluaran_' . str_pad($no_bukti, 3, '0', STR_PAD_LEFT) . '.pdf';
    $zip->addFromString($filename, $pdfContent);
}

$zip->close();

/*
|--------------------------------------------------------------------------
| DOWNLOAD ZIP
|--------------------------------------------------------------------------
*/
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipName . '"');
header('Content-Length: ' . filesize($zipPath));
header('Cache-Control: no-store, no-cache, must-revalidate');

readfile($zipPath);
unlink($zipPath);
exit;
