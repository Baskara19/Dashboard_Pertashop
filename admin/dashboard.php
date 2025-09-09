<?php
require __DIR__ . '/../auth_admin.php';

// pastikan hanya admin yang boleh akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../operator/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Pertashop</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #800000; /* merah marun */
            margin: 0;
            padding: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: #fff; /* kotak putih */
            padding: 40px 50px;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 900px;
            text-align: center;
            animation: fadeIn .6s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        h1 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #800000;
        }
        p {
            color: #555;
            margin-bottom: 35px;
            font-size: 16px;
        }
        .menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
        }
        .card {
            background: #d9d9d9;
            padding: 28px 22px;
            border-radius: 14px;
            text-decoration: none;
            color: #222;
            font-weight: 600;
            font-size: 17px;
            letter-spacing: .3px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            transition: all 0.3s ease;
        }
        .card:hover {
            background: #800000;
            color: #fff;
            transform: translateY(-6px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.2);
        }

        /* 🔴 Tombol Logout */
        .logout {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 18px;         /* ukuran tombol pas */
            background: #dc3545;        /* merah jelas */
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: background 0.3s ease, transform 0.2s ease;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
            width: auto;                /* biar sesuai isi teks */
        }
        .logout:hover {
            background: #a71d2a;        /* merah lebih gelap saat hover */
            transform: translateY(-2px);
        }

        /* 📱 Responsif untuk HP */
        @media (max-width: 600px) {
            .container {
                padding: 25px 20px;
                border-radius: 12px;
            }
            h1 {
                font-size: 22px;
            }
            p {
                font-size: 14px;
                margin-bottom: 25px;
            }
            .menu {
                grid-template-columns: 1fr; /* menu jadi 1 kolom */
                gap: 15px;
            }
            .card {
                font-size: 15px;
                padding: 18px 14px;
            }
            .logout {
                display: inline-block;  /* tetap kecil di HP */
                width: auto;            /* jangan full */
                margin-top: 18px;
                padding: 9px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Dashboard Admin Pertashop</h1>
        <p>Selamat datang, <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']) ?> 👋</p>

        <div class="menu">
            <a href="input_penjualan.php" class="card">➕ Input Penjualan</a>
            <a href="laporan_admin.php" class="card">📅 Laporan Penjualan</a>
            <a href="rekap_bulanan.php" class="card">📆 Rekap Bulanan</a>
            <a href="kelola_operator.php" class="card">👥 Kelola Operator</a>
            <a href="kelola_admin.php" class="card">🛠️ Kelola Admin</a>
        </div>

        <a href="../logout.php" class="logout">🚪 Logout</a>
    </div>
</body>
</html>