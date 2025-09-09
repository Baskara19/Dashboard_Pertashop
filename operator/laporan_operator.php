<?php
// cek login
require __DIR__ . '/../auth_operator.php';

// hanya untuk operator
if ($_SESSION['role'] !== 'operator') {
    die("❌ Akses ditolak. Halaman ini hanya untuk Operator.");
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Operator</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 15px; 
            background:#f4f4f9; 
        }
        h2 { 
            text-align: center; 
            margin-bottom: 15px; 
            font-size: 22px;
        }
        form { 
            text-align:center; 
            margin-bottom:15px; 
        }
        form input, form button {
            padding: 7px 10px; 
            border-radius:6px; 
            border:1px solid #ccc; 
            font-size:14px;
        }
        form button {
            background:#007bff; 
            color:white; 
            border:none; 
            cursor:pointer;
        }
        form button:hover { background:#0056b3; }

        /* 📊 Tabel responsif */
        .table-wrapper {
            overflow-x: auto; /* scroll horizontal di HP */
            -webkit-overflow-scrolling: touch;
        }
        table { 
            border-collapse: collapse; 
            width: 100%; 
            margin-top: 15px; 
            background:white; 
            border-radius: 8px; 
            overflow: hidden; 
            font-size: 14px;
        }
        th, td { 
            border: 1px solid #ccc; 
            padding: 8px; 
            text-align: center; 
        }
        th { 
            background: #343a40; 
            color: white; 
            font-size: 14px;
        }
        tr.total-row { 
            font-weight:bold; 
            background:#e9ecef; 
        }

        /* 🎨 Tombol */
        a.edit-btn, a.delete-btn { 
            display: inline-block; 
            padding: 5px 10px; 
            margin: 2px;
            color: white; 
            text-decoration: none; 
            border-radius: 6px; 
            font-size: 13px;
        }
        a.edit-btn { background: #007bff; }
        a.edit-btn:hover { background: #0056b3; }
        a.delete-btn { background: #dc3545; }
        a.delete-btn:hover { background: #a71d2a; }

        .btn-download, .btn-back {
            display:inline-block; 
            padding:8px 14px; 
            border-radius:6px;
            font-weight:bold; 
            text-decoration:none; 
            margin:10px 5px;
            font-size:14px;
        }
        .btn-download { background:#007bff; color:white; }
        .btn-download:hover { background:#0056b3; }
        .btn-back { background:#28a745; color:white; }
        .btn-back:hover { background:#1e7e34; }

        /* 📱 Mode HP */
        @media (max-width: 600px) {
            body { padding: 10px; }
            h2 { font-size: 18px; }
            table { font-size: 12px; }
            th, td { padding: 6px; }
            form input, form button { font-size: 12px; padding: 6px 8px; }
            .btn-download, .btn-back { 
                font-size:12px; 
                padding:7px 12px; 
                margin:6px 3px;
            }
            a.edit-btn, a.delete-btn { font-size: 11px; padding: 4px 7px; }
        }
    </style>
</head>
<body>
    <h2>Laporan Penjualan (Operator)</h2>
    <form method="GET">
        Pilih Tanggal: 
        <input type="date" name="tanggal" value="<?= $tanggal ?>">
        <button type="submit">Cari</button>
    </form>

    <div class="table-wrapper">
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
                            <a class='edit-btn' href='edit_penjualan.php?id=".$row['id']."&tanggal=$tanggal'>✏️</a>
                            <a class='delete-btn' href='hapus_penjualan.php?id=".$row['id']."&tanggal=$tanggal' 
                               onclick=\"return confirm('Yakin ingin menghapus data ini?')\">🗑️</a>
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

    <div style="text-align:center; margin-top:15px;">
        <a href="../cetak.pdf.php?tanggal=<?= $tanggal ?>" target="_blank" class="btn-download">
            📄 Download PDF
        </a>
        <a href="dashboard_operator.php" class="btn-back">
            ⬅️ Kembali ke Dashboard
        </a>
    </div>
</body>
</html>

