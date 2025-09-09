<?php
// cek login
require __DIR__ . '/../auth_admin.php';

// hanya untuk admin
if ($_SESSION['role'] !== 'admin') {
    die("❌ Akses ditolak. Halaman ini hanya untuk Admin.");
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "pertashop";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

// ambil data penjualan harian
$sql = "SELECT * FROM penjualan_harian WHERE tanggal = '$tanggal' ORDER BY shift";
$result = $conn->query($sql);

$total_liter = 0;
$total_rp = 0;
$total_hasil_pengukuran = 0;

// rasio keuntungan
$rasio_keuntungan = 823.8395;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; padding: 15px; background:#f4f4f9; }
        h2 { text-align: center; margin-bottom:20px; }

        /* Form pencarian */
        form { text-align:center; margin-bottom:20px; }
        form input, form button {
            padding: 8px 12px; border-radius:6px; border:1px solid #ccc;
        }
        form button {
            background:#007bff; color:white; border:none; cursor:pointer;
        }
        form button:hover { background:#0056b3; }

        /* Tabel umum */
        .table-container { overflow-x:auto; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; background:white; border-radius: 8px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #343a40; color: white; }
        tr.total-row { font-weight:bold; background:#e9ecef; }

        /* Tombol aksi */
        a.edit-btn, a.delete-btn { 
            display: inline-block; 
            padding: 6px 12px; 
            margin: 2px;
            color: white; 
            text-decoration: none; 
            border-radius: 6px; 
            font-size: 14px;
        }
        a.edit-btn { background: #007bff; }
        a.edit-btn:hover { background: #0056b3; }
        a.delete-btn { background: #dc3545; }
        a.delete-btn:hover { background: #a71d2a; }

        /* Tombol bawah */
        .btn-download, .btn-back {
            display:inline-block; padding:10px 20px; border-radius:6px;
            font-weight:bold; text-decoration:none; margin:10px 5px;
        }
        .btn-download { background:#007bff; color:white; }
        .btn-download:hover { background:#0056b3; }
        .btn-back { background:#28a745; color:white; }
        .btn-back:hover { background:#1e7e34; }

        /* Responsive text di HP */
        @media (max-width: 600px) {
            table, th, td {
                font-size: 12px;
                padding: 6px;
            }
            .btn-download, .btn-back {
                display:block;
                margin:8px auto;
                text-align:center;
            }
        }
    </style>
</head>
<body>
    <h2>Laporan Penjualan (Admin)</h2>
    <form method="GET">
        Pilih Tanggal: 
        <input type="date" name="tanggal" value="<?= $tanggal ?>">
        <button type="submit">Cari</button>
    </form>

    <!-- Tabel Penjualan -->
    <div class="table-container">
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
                <th>Aksi</th>
            </tr>

            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // ✅ rumus yang benar
                    $hasil_pengukuran = ($row['ukur_awal'] - $row['ukur_akhir']) * 21.23;
                    $stok_pertamax    = $row['ukur_akhir'] * 21.23;

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
                        <td>".number_format($hasil_pengukuran,2,',','.')."</td>
                        <td>Rp ".number_format($row['penghasilan_rp'],0,',','.')."</td>
                        <td>
                            <a class='edit-btn' href='edit_penjualan.php?id=".$row['id']."&tanggal=$tanggal'>✏️ Edit</a>
                            <a class='delete-btn' href='hapus_penjualan.php?id=".$row['id']."&tanggal=$tanggal' 
                               onclick=\"return confirm('Yakin ingin menghapus data ini?')\">🗑️ Hapus</a>
                        </td>
                    </tr>";

                    $total_liter += $row['penjualan_liter'];
                    $total_hasil_pengukuran += $hasil_pengukuran;
                    $total_rp += $row['penghasilan_rp'];
                }

                echo "<tr class='total-row'>
                    <td colspan='8'>TOTAL</td>
                    <td>$total_liter</td>
                    <td>".number_format($total_hasil_pengukuran,2,',','.')."</td>
                    <td>Rp ".number_format($total_rp,0,',','.')."</td>
                    <td></td>
                </tr>";
            } else {
                echo "<tr><td colspan='12'>Tidak ada data pada tanggal ini.</td></tr>";
            }
            ?>
        </table>
    </div>

    <?php if ($total_liter > 0): 
        $keuntungan = $total_liter * $rasio_keuntungan;
    ?>
    <!-- Tabel Keuntungan -->
    <div class="table-container">
        <table class="profit-table">
            <tr>
                <th>Total Penjualan (L)</th>
                <th>Rasio Keuntungan</th>
                <th>Keuntungan (Rp)</th>
            </tr>
            <tr>
                <td><?= number_format($total_liter,0,',','.') ?></td>
                <td>Rp <?= number_format($rasio_keuntungan,2,',','.') ?></td>
                <td><b>Rp <?= number_format($keuntungan,0,',','.') ?></b></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>

    <div style="text-align:center; margin-top:20px;">
        <a href="../cetak_pdf.php?tanggal=<?= $tanggal ?>" target="_blank" class="btn-download">
            📄 Download PDF
        </a>
        <a href="dashboard.php" class="btn-back">
            ⬅️ Kembali ke Dashboard
        </a>
    </div>
</body>
</html>