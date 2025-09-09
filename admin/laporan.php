<?php
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
$total_untung = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan Pertashop</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #f2f2f2; }
        h2 { text-align: center; }
        .hapus { color: red; text-decoration: none; font-weight: bold; }
        .hapus:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>Laporan Penjualan Pertashop</h2>
 

    <!-- Tabel Utama -->
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
            <th>Penghasilan (Rp)</th>
            <th>Aksi</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $stok_pertamax = ($row['ukur_awal'] - $row['ukur_akhir']) * 21.23;
                $keuntungan = $row['penjualan_liter'] * 823.8395;

                echo "<tr>
                    <td>".$row['nama']."</td>
                    <td>".$row['shift']."</td>
                    <td>".$row['odo_awal']."</td>
                    <td>".$row['odo_akhir']."</td>
                    <td>".number_format($row['ukur_awal'],2,',','.')."</td>
                    <td>".number_format($row['ukur_akhir'],2,',','.')."</td>
                    <td>".number_format($stok_pertamax,2,',','.')."</td>
                    <td>Rp ".number_format($row['harga_pertamax'],0,',','.')."</td>
                    <td>".$row['penjualan_liter']."</td>
                    <td>Rp ".number_format($row['penghasilan_rp'],0,',','.')."</td>
                    <td><a class='hapus' href='hapus.php?id=".$row['id']."&tanggal=$tanggal' onclick=\"return confirm('Yakin mau hapus data ini?')\">❌ Hapus</a></td>
                </tr>";

                $total_liter += $row['penjualan_liter'];
                $total_rp += $row['penghasilan_rp'];
                $total_untung += $keuntungan;
            }

            echo "<tr style='font-weight:bold; background:#eaeaea;'>
                <td colspan='8'>TOTAL</td>
                <td>$total_liter</td>
                <td>Rp ".number_format($total_rp,0,',','.')."</td>
                <td>-</td>
            </tr>";
        } else {
            echo "<tr><td colspan='11'>Tidak ada data pada tanggal ini.</td></tr>";
        }
        ?>
    </table>

    <!-- Tabel Keuntungan -->
    <h3>Keuntungan</h3>
    <table>
        <tr>
            <th>Penjualan (L)</th>
            <th>Rasio Keuntungan per Liter</th>
            <th>Total Keuntungan (Rp)</th>
        </tr>
        <tr>
            <td><?= $total_liter ?></td>
            <td>Rp <?= number_format(823.8395,2,',','.') ?></td>
            <td>Rp <?= number_format($total_untung,0,',','.') ?></td>
        </tr>
    </table>

    <div style="text-align:center;">
    <a href="cetak_pdf.php?tanggal=<?= $tanggal ?>" target="_blank" 
       style="display:inline-block; padding:10px 20px; background:#28a745; color:white; 
              text-decoration:none; border-radius:6px; font-weight:bold; margin:5px;">
        📄 Download PDF
    </a>

    <a href="dashboard.php" 
       style="display:inline-block; padding:10px 20px; background:#007bff; color:white; 
              text-decoration:none; border-radius:6px; font-weight:bold; margin:5px;">
        ⬅️ Kembali ke Dashboard
    </a>

    <a href="input_penjualan.php" 
       style="display:inline-block; padding:10px 20px; background:#ffc107; color:black; 
              text-decoration:none; border-radius:6px; font-weight:bold; margin:5px;">
        ➕ Kembali ke Input Data
    </a>
</div>

</body>
</html>
