<?php
require __DIR__ . '/../auth_operator.php';  // ✅ hanya operator yang bisa akses

// hanya untuk operator
if ($_SESSION['role'] !== 'operator') {
    die("❌ Akses ditolak. Halaman ini hanya untuk Operator.");
}

// Koneksi DB
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pertashop";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$error = "";

// Prefill form
$prefill_nama   = $_POST['nama']            ?? ($_SESSION['nama'] ?? '');
$prefill_tanggal= $_POST['tanggal']         ?? date('Y-m-d');
$prefill_shift  = $_POST['shift']           ?? '';
$prefill_odo_awal = $_POST['odo_awal']      ?? '';
$prefill_odo_akhir= $_POST['odo_akhir']     ?? '';
$prefill_ukur_awal = $_POST['pengukuran_awal']  ?? '';
$prefill_ukur_akhir= $_POST['pengukuran_akhir'] ?? '';
$prefill_harga     = $_POST['harga_pertamax']   ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama              = trim($_POST['nama'] ?? '');
    $tanggal           = $_POST['tanggal'] ?? '';
    $shift             = $_POST['shift'] ?? '';
    $odo_awal          = (int) ($_POST['odo_awal'] ?? 0);
    $odo_akhir         = (int) ($_POST['odo_akhir'] ?? 0);
    $pengukuran_awal   = (float) ($_POST['pengukuran_awal'] ?? 0);
    $pengukuran_akhir  = (float) ($_POST['pengukuran_akhir'] ?? 0);
    $harga_pertamax    = (int) ($_POST['harga_pertamax'] ?? 0);

    if ($nama === '' || $tanggal === '' || $shift === '') {
        $error = "Nama, tanggal, dan shift wajib diisi.";
    } elseif ($odo_akhir < $odo_awal) {
        $error = "Odo akhir tidak boleh lebih kecil dari odo awal.";
    } elseif ($pengukuran_awal < $pengukuran_akhir) {
        $error = "Pengukuran awal tidak boleh lebih kecil dari pengukuran akhir.";
    } elseif ($harga_pertamax <= 0) {
        $error = "Harga pertamax harus lebih dari 0.";
    } else {
        $penjualan_liter   = $odo_akhir - $odo_awal;
        $penghasilan       = $penjualan_liter * $harga_pertamax;
        $hasil_pengukuran  = ($pengukuran_awal - $pengukuran_akhir) * 21.23; // ✅ sesuai rumus
        $stok_pertamax     = $pengukuran_akhir * 21.23; // ✅ stok dari pengukuran akhir

        $sql = "INSERT INTO penjualan_harian 
            (nama, tanggal, shift, odo_awal, odo_akhir, penjualan_liter, penghasilan_rp, ukur_awal, ukur_akhir, harga_pertamax, hasil_pengukuran, stok_hari_ini) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param(
                "sssiiiiddidd",
                $nama,
                $tanggal,
                $shift,
                $odo_awal,
                $odo_akhir,
                $penjualan_liter,
                $penghasilan,
                $pengukuran_awal,
                $pengukuran_akhir,
                $harga_pertamax,
                $hasil_pengukuran,
                $stok_pertamax
            );
            if ($stmt->execute()) {
                // ✅ setelah simpan balik ke laporan operator
                header("Location: laporan_operator.php?tanggal=" . urlencode($tanggal));
                exit();
            } else {
                $error = "Gagal menyimpan data: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Gagal menyiapkan query: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Input Penjualan Operator</title>
    <meta charset="utf-8" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg,#f4f4f9,#e8ebf0);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 0;
            padding: 40px 20px;
        }
        .form-container {
            background: #ffffff;
            padding: 40px 50px;
            border-radius: 18px;
            box-shadow: 0 10px 28px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 760px;
            animation: fadeIn .5s ease;
        }
        @keyframes fadeIn { from{opacity:0; transform: translateY(12px);} to{opacity:1; transform: translateY(0);} }
        h2 {
            text-align: center;
            margin: 10px 0 18px 0;
            color: #0077cc;
            font-size: 26px;
        }
        .desc {
            text-align: center;
            color: #555;
            margin-bottom: 24px;
            font-size: 14px;
        }
        .alert-error {
            background: #ffe5e5;
            color: #b30000;
            border: 1px solid #f5b5b3;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        label {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 4px;
            display: block;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 13px 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 15px;
            margin-bottom: 22px;
            background: #fafafa;
            color: #333;
            transition: 0.25s;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #0077cc;
            box-shadow: 0 0 0 3px rgba(0,119,204,0.2);
            background: #fff;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 12px;
        }
        small.hint { 
            color:#777; 
            display:block; 
            margin: 4px 0 10px 0; 
            font-size: 13px;
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 25px;
        }
        .top-bar {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 18px;
        }
        .btn-primary, .btn-secondary {
            padding: 13px 20px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.25s;
        }
        .btn-primary {
            background: linear-gradient(45deg,#007bff,#00c6ff);
            color: white;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg,#0062cc,#0096d6);
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: linear-gradient(45deg,#28a745,#6edc82);
            color: white;
            text-decoration: none;
            display: inline-block;
        }
        .btn-secondary:hover {
            background: linear-gradient(45deg,#218838,#57b870);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="top-bar">
            <a class="btn-secondary" href="dashboard_operator.php">⬅️ Kembali ke Dashboard</a>
        </div>

        <h2>📝 Input Penjualan (Operator)</h2>
        <p class="desc">Masukkan data lengkap untuk menyimpan transaksi harian.</p>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <label>Nama Operator</label>
            <input type="text" name="nama" required value="<?= h($prefill_nama) ?>">

            <label>Tanggal</label>
            <input type="date" name="tanggal" required value="<?= h($prefill_tanggal) ?>">

            <label>Shift</label>
            <select name="shift" required>
                <option value="">-- Pilih Shift --</option>
                <option value="Pagi" <?= $prefill_shift==='Pagi'?'selected':''; ?>>🌅 Pagi</option>
                <option value="Sore" <?= $prefill_shift==='Sore'?'selected':''; ?>>🌆 Sore</option>
            </select>

            <div class="grid-2">
                <div>
                    <label>Odo Awal</label>
                    <input type="number" name="odo_awal" required value="<?= h($prefill_odo_awal) ?>">
                </div>
                <div>
                    <label>Odo Akhir</label>
                    <input type="number" name="odo_akhir" required value="<?= h($prefill_odo_akhir) ?>">
                </div>
            </div>

            <div class="grid-2">
                <div>
                    <label>Pengukuran Awal</label>
                    <small class="hint">(pakai titik, misal 12.35)</small>
                    <input type="number" step="0.01" name="pengukuran_awal" required value="<?= h($prefill_ukur_awal) ?>">
                </div>
                <div>
                    <label>Pengukuran Akhir</label>
                    <small class="hint">(pakai titik, misal 11.87)</small>
                    <input type="number" step="0.01" name="pengukuran_akhir" required value="<?= h($prefill_ukur_akhir) ?>">
                </div>
            </div>

            <label>Harga Pertamax (Rp)</label>
            <input type="number" name="harga_pertamax" required value="<?= h($prefill_harga) ?>">

            <div class="actions">
                <button type="submit" class="btn-primary">💾 Simpan Data</button>
            </div>
        </form>
    </div>
</body>
</html>
