<?php
require __DIR__ . '/../auth_admin.php';
?>
<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "pertashop";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Bulanan Pertashop</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 15px; background:#f8f9fa; }
        h2 { text-align: center; margin-bottom: 20px; }

        form { text-align: center; margin-bottom: 20px; }
        form input, form button {
            padding: 8px 12px; border-radius:6px; border:1px solid #ccc;
        }
        form button {
            background:#007bff; color:white; border:none; cursor:pointer;
        }
        form button:hover { background:#0056b3; }

        /* Bungkus tabel biar bisa scroll horizontal di HP */
        .table-container { overflow-x:auto; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; background:white; border-radius: 6px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #343a40; color:white; }
        tr.total-row { font-weight:bold; background:#e9ecef; }

        /* Tombol bawah */
        .btn { 
            display:inline-block; padding:10px 20px; border-radius:6px; 
            font-weight:bold; text-decoration:none; margin:8px 5px;
        }
        .btn-download { background:#28a745; color:white; }
        .btn-download:hover { background:#1e7e34; }
        .btn-back { background:#007bff; color:white; }
        .btn-back:hover { background:#0056b3; }

        /* Responsive di layar kecil */
        @media (max-width: 600px) {
            table, th, td { font-size: 12px; padding: 6px; }
            .btn { display:block; margin:10px auto; text-align:center; }
        }
    </style>
</head>
<body>
    <h2>Rekap Penjualan Bulanan Pertashop</h2>

    <form method="get" action="">
        <label>Pilih Bulan: </label>
        <input type="month" name="bulan" value="<?= $bulan ?>">
        <button type="submit">Tampilkan</button>
    </form>

    <div class="table-container">
        <table>
            <tr>
                <th>Tanggal</th>
                <th>Penjualan (L)</th>
                <th>Penghasilan (Rp)</th>
                <th>Keuntungan (Rp)</th>
            </tr>
            <?php
            $grand_liter = 0;
            $grand_rp = 0;
            $grand_keuntungan = 0;

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $keuntungan = $row['total_liter'] * 823.839;

                    echo "<tr>
                        <td>".$row['tanggal']."</td>
                        <td>".$row['total_liter']."</td>
                        <td>Rp ".number_format($row['total_rp'],0,',','.')."</td>
                        <td>Rp ".number_format($keuntungan,0,',','.')."</td>
                    </tr>";

                    $grand_liter += $row['total_liter'];
                    $grand_rp += $row['total_rp'];
                    $grand_keuntungan += $keuntungan;
                }

                echo "<tr class='total-row'>
                    <td>TOTAL</td>
                    <td>$grand_liter</td>
                    <td>Rp ".number_format($grand_rp,0,',','.')."</td>
                    <td>Rp ".number_format($grand_keuntungan,0,',','.')."</td>
                </tr>";
            } else {
                echo "<tr><td colspan='4'>Tidak ada data pada bulan ini.</td></tr>";
            }
            ?>
        </table>
    </div>

    <div style="text-align:center; margin-top:20px;">
        <a href="rekap_bulanan_pdf.php?bulan=<?= $bulan ?>" target="_blank" class="btn btn-download">
            📄 Download PDF
        </a>
        <a href="dashboard.php" class="btn btn-back">
            ⬅️ Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
