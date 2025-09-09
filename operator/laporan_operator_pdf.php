<?php
require __DIR__ . '/../auth_operator.php';

// hanya operator
if ($_SESSION['role'] !== 'operator') {
    die("❌ Akses ditolak. Halaman ini hanya untuk Operator.");
}

require_once __DIR__ . '/../vendor/autoload.php'; // pastikan dompdf ada di vendor

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

$total_liter   = 0;
$total_rp      = 0;
$total_ukur    = 0; // total hasil pengukuran

// kumpulkan HTML
$html = "
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    .box {
        background: white;
        padding: 15px;
        border-radius: 8px;
    }
    h2 { margin-bottom: 5px; text-align:center; }
    p { margin-top: 0; text-align:center; }
    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 10px;
    }
    th, td {
        border: 1px solid #ccc;
        padding: 6px;
        text-align: center;
    }
    th { background: #6c757d; color: white; }
    tr.total-row { font-weight:bold; background:#e9ecef; }
</style>
<div class='box'>
    <h2>Laporan Penjualan Operator</h2>
    <p><b>Tanggal:</b> $tanggal</p>
    <table>
        <tr>
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
        </tr>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // ✅ rumus
        $hasil_pengukuran = ($row['ukur_awal'] - $row['ukur_akhir']) * 21.23;
        $stok_pertamax    = $row['ukur_akhir'] * 21.23;

        $html .= "<tr>
            <td>".$row['nama']."</td>
            <td>".$row['shift']."</td>
            <td>".$row['odo_awal']."</td>
            <td>".$row['odo_akhir']."</td>
            <td>".number_format($row['ukur_awal'],2,',','.')."</td>
            <td>".number_format($row['ukur_akhir'],2,',','.')."</td>
            <td>".number_format($stok_pertamax,2,',','.')."</td>
            <td>Rp ".number_format($row['harga_pertamax'],0,',','.')."</td>
            <td>".number_format($row['penjualan_liter'],0,',','.')."</td>
            <td>".number_format($hasil_pengukuran,2,',','.')."</td>
            <td>Rp ".number_format($row['penghasilan_rp'],0,',','.')."</td>
        </tr>";

        $total_liter += $row['penjualan_liter'];
        $total_rp    += $row['penghasilan_rp'];
        $total_ukur  += $hasil_pengukuran;
    }

    $html .= "<tr class='total-row'>
        <td colspan='8'>TOTAL</td>
        <td>".number_format($total_liter,0,',','.')."</td>
        <td>".number_format($total_ukur,2,',','.')."</td>
        <td>Rp ".number_format($total_rp,0,',','.')."</td>
    </tr>";
} else {
    $html .= "<tr><td colspan='11'>Tidak ada data pada tanggal ini.</td></tr>";
}

$html .= "</table></div>";

// setup Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// nama file
$dompdf->stream("laporan_operator_$tanggal.pdf", ["Attachment" => true]);
