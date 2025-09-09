<?php
require __DIR__ . '/../auth_admin.php';

// koneksi DB
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pertashop";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

// cek ID
$id = $_GET['id'] ?? 0;
if (!$id) { die("ID tidak ditemukan!"); }

// jika tombol simpan ditekan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama   = $_POST['nama'];
    $shift  = $_POST['shift'];
    $odo_awal = (int)$_POST['odo_awal'];
    $odo_akhir = (int)$_POST['odo_akhir'];
    $ukur_awal = (float)$_POST['ukur_awal'];
    $ukur_akhir = (float)$_POST['ukur_akhir'];
    $harga = (int)$_POST['harga_pertamax'];

    // hitungan ulang
    $penjualan_liter = $odo_akhir - $odo_awal;
    $penghasilan = $penjualan_liter * $harga;
    $stok_pertamax = ($ukur_awal - $ukur_akhir) * 21.23;

    $sql = "UPDATE penjualan_harian 
            SET nama=?, shift=?, odo_awal=?, odo_akhir=?, ukur_awal=?, ukur_akhir=?, 
                harga_pertamax=?, penjualan_liter=?, penghasilan_rp=?, stok_hari_ini=? 
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiddidiii", 
        $nama, $shift, $odo_awal, $odo_akhir, $ukur_awal, $ukur_akhir,
        $harga, $penjualan_liter, $penghasilan, $stok_pertamax, $id
    );

    if ($stmt->execute()) {
        header("Location: laporan_admin.php?tanggal=" . ($_POST['tanggal'] ?? date('Y-m-d')));
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// ambil data lama
$sql = "SELECT * FROM penjualan_harian WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
if (!$data) { die("Data tidak ditemukan!"); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- penting -->
    <title>Edit Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 10px;
            margin: 0;
            background: #f8f9fa;
        }
        .form-box {
            width: 100%;
            max-width: 600px;  /* biar di desktop ga terlalu lebar */
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            box-sizing: border-box;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 12px;
            font-size: 14px;
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 18px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #007bff;
            font-size: 14px;
        }

        /* ✅ Responsive khusus HP */
        @media (max-width: 480px) {
            body {
                padding: 5px;
            }
            .form-box {
                padding: 15px;
                border-radius: 6px;
            }
            h2 {
                font-size: 18px;
            }
            label {
                font-size: 13px;
            }
            input, select, button {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Edit Data Penjualan</h2>
        <form method="POST">
            <input type="hidden" name="tanggal" value="<?= $data['tanggal'] ?>">

            <label>Nama Operator</label>
            <input type="text" name="nama" value="<?= $data['nama'] ?>" required>

            <label>Shift</label>
            <select name="shift" required>
                <option value="Pagi" <?= $data['shift']=='Pagi'?'selected':'' ?>>🌅 Pagi</option>
                <option value="Sore" <?= $data['shift']=='Sore'?'selected':'' ?>>🌆 Sore</option>
            </select>

            <label>Odo Awal</label>
            <input type="number" name="odo_awal" value="<?= $data['odo_awal'] ?>" required>

            <label>Odo Akhir</label>
            <input type="number" name="odo_akhir" value="<?= $data['odo_akhir'] ?>" required>

            <label>Pengukuran Awal</label>
            <input type="number" step="0.01" name="ukur_awal" value="<?= $data['ukur_awal'] ?>" required>

            <label>Pengukuran Akhir</label>
            <input type="number" step="0.01" name="ukur_akhir" value="<?= $data['ukur_akhir'] ?>" required>

            <label>Harga Pertamax</label>
            <input type="number" name="harga_pertamax" value="<?= $data['harga_pertamax'] ?>" required>

            <button type="submit">💾 Simpan Perubahan</button>
        </form>
        <a href="laporan_admin.php?tanggal=<?= $data['tanggal'] ?>">⬅️ Kembali ke Laporan</a>
    </div>
</body>
</html>


