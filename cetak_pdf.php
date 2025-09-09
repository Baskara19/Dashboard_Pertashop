<?php
// cek login
require __DIR__ . '/auth_admin.php';

// hanya admin
if ($_SESSION['role'] !== 'admin') {
    die("❌ Akses ditolak. Halaman ini hanya untuk Admin.");
}

require_once __DIR__ . '/vendor/autoload.php'; // pastikan dompdf sudah ada di vendor

use Dompdf\Dompdf;
use Dompdf\Options;

$host = "localhost";
$user = "root";
$pass = "";
$db   = "pertashop";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

$sql = "SELECT * FROM penjualan_harian WHERE tanggal = '$tanggal' ORDER BY shift";
$result = $conn->query($sql);

$total_liter = 0;
$total_rp = 0;
$total_hasil_pengukuran = 0;

// rasio keuntungan
$rasio_keuntungan = 823.8395;

// siapkan HTML
$html = "
<h2 style='text-align:center;'>Laporan Penjualan (Admin)</h2>
<p style='text-align:center;'>Tanggal: $tanggal</p>
<table border='1' cellspacing='0' cellpadding='6' width='100%'>
    <tr style='background:#343a40; color:white;'>
        <th>Nama Operator</th>
        <th>Shift</th>
        <th>Odo Awal</th>
        <th>Odo Akhir</th>
        <th>Pengukuran Awal</th>
        <th>Pengukuran Akhir</th>
        <th>Stok Pertamax (L)</th>
        <th>Harga Pertamax</th>
        <th>Penjualan (L)</th>
        <th>Hasil Pengukuran (L)</th>
        <th>Penghasilan (Rp)</th>
    </tr>
";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hasil_pengukuran = ($row['ukur_awal'] - $row['ukur_akhir']) * 21.23;
        $stok_pertamax    = $row['ukur_akhir'] * 21.23;

        $html .= "
        <tr>
            <td>".$row['nama']."</td>
            <td>".$row['shift']."</td>
            <td>".$row['odo_awal']."</td>
            <td>".$row['odo_akhir']."</td>
            <td>".number_format($row['ukur_awal'],2,',','.')."</td>
            <td>".number_format($row['ukur_akhir'],2,',','.')."</td>
            <td>".number_format($stok_pertamax,2,',','.')."</td>
            <td>Rp ".number_format($row['harga_pertamax'],0,',','.')."</td>
            <td>".$row['penjualan_liter']."</td>
            <td>".number_format($hasil_pengukuran,2,',','.')."</td>
            <td>Rp ".number_format($row['penghasilan_rp'],0,',','.')."</td>
        </tr>
        ";

        $total_liter += $row['penjualan_liter'];
        $total_hasil_pengukuran += $hasil_pengukuran;
        $total_rp += $row['penghasilan_rp'];
    }

    $html .= "
    <tr style='font-weight:bold; background:#e9ecef;'>
        <td colspan='8'>TOTAL</td>
        <td>$total_liter</td>
        <td>".number_format($total_hasil_pengukuran,2,',','.')."</td>
        <td>Rp ".number_format($total_rp,0,',','.')."</td>
    </tr>
    ";
} else {
    $html .= "<tr><td colspan='11' style='text-align:center;'>Tidak ada data pada tanggal ini.</td></tr>";
}

$html .= "</table>";

if ($total_liter > 0) {
    $keuntungan = $total_liter * $rasio_keuntungan;
    $html .= "
    <br><br>
    <table border='1' cellspacing='0' cellpadding='6' width='100%'>
        <tr style='background:#343a40; color:white;'>
            <th>Total Penjualan (L)</th>
            <th>Rasio Keuntungan</th>
            <th>Keuntungan (Rp)</th>
        </tr>
        <tr>
            <td>".number_format($total_liter,0,',','.')."</td>
            <td>Rp ".number_format($rasio_keuntungan,2,',','.')."</td>
            <td><b>Rp ".number_format($keuntungan,0,',','.')."</b></td>
        </tr>
    </table>
    ";
}

// Inisialisasi Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// download otomatis
$dompdf->stream("Laporan Penjualan $tanggal", array("Attachment" => true));
exit;
