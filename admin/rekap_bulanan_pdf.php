<?php
require __DIR__ . '/../auth_admin.php';

$host = "localhost";
$user = "root";
$pass = "";
$db   = "pertashop";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

$bulan = $_GET['bulan'] ?? date('Y-m');

$sql = "
    SELECT tanggal,
           SUM(penjualan_liter) AS total_liter,
           SUM(penghasilan_rp) AS total_rp
    FROM penjualan_harian
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'
    GROUP BY tanggal
    ORDER BY tanggal
";
$result = $conn->query($sql);

// pakai dompdf
require __DIR__ . '/../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$html = '
<h2 style="text-align:center;">Rekap Penjualan Bulanan Pertashop ('.$bulan.')</h2>
<table border="1" cellspacing="0" cellpadding="6" width="100%" style="border-collapse:collapse; font-size:12px;">
    <thead>
        <tr style="background:#f2f2f2; text-align:center;">
            <th>Tanggal</th>
            <th>Penjualan (L)</th>
            <th>Penghasilan (Rp)</th>
            <th>Keuntungan (Rp)</th>
        </tr>
    </thead>
    <tbody>';

$grand_liter = $grand_rp = $grand_keuntungan = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $keuntungan = $row['total_liter'] * 823.839;

        $html .= "<tr>
            <td align='center'>".$row['tanggal']."</td>
            <td align='right'>".$row['total_liter']."</td>
            <td align='right'>Rp ".number_format($row['total_rp'],0,',','.')."</td>
            <td align='right'>Rp ".number_format($keuntungan,0,',','.')."</td>
        </tr>";

        $grand_liter += $row['total_liter'];
        $grand_rp += $row['total_rp'];
        $grand_keuntungan += $keuntungan;
    }

    $html .= "<tr style='font-weight:bold; background:#eaeaea;'>
        <td align='center'>TOTAL</td>
        <td align='right'>$grand_liter</td>
        <td align='right'>Rp ".number_format($grand_rp,0,',','.')."</td>
        <td align='right'>Rp ".number_format($grand_keuntungan,0,',','.')."</td>
    </tr>";
} else {
    $html .= "<tr><td colspan='4' align='center'>Tidak ada data pada bulan ini.</td></tr>";
}

$html .= "</tbody></table>";

// render pdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("rekap_bulanan_$bulan.pdf", ["Attachment" => true]); // true = download, false = preview
?>
