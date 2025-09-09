<?php
// cek login
require __DIR__ . '/../auth_operator.php';


// kalau bukan operator, tolak akses
if ($_SESSION['role'] !== 'operator') {
    die("❌ Akses ditolak. Halaman ini hanya untuk Operator.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- ✅ Biar responsive di HP -->
    <title>Dashboard Operator Pertashop</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
              background: #800000;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0px 8px 20px rgba(0,0,0,0.15);
            width: 95%;
            max-width: 800px;
            text-align: center;
        }
        h1 {
            color: #800000;
            margin-bottom: 10px;
            font-size: 26px;
        }
        p {
            color: #555;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        .card {
            background: #d9d9d9;
            padding: 20px;
            border-radius: 12px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            font-size: 18px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            transition: 0.3s;
        }
        .card:hover {
            background: #800000;
            color: white;
            transform: translateY(-5px);
            box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
        }

        /* ✅ Tombol Logout */
        .logout {
            display: inline-block;    /* selebar teks */
            padding: 10px 18px;
            background-color: #dc3545;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 10px;
        }
        .logout:hover {
            background-color: #a71d2a;
        }

        /* ✅ Responsive fix */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                border-radius: 12px;
            }
            h1 {
                font-size: 22px;
            }
            p {
                font-size: 14px;
            }
            .menu {
                grid-template-columns: 1fr; /* jadi 1 kolom di HP */
            }
            .card {
                font-size: 16px;
                padding: 18px;
            }
            .logout {
                margin-left: auto;
                margin-right: auto; /* tetap center di HP */
                display: inline-block; /* tetap pendek sesuai teks */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>👷 Dashboard Operator Pertashop</h1>
        <p>Selamat datang, <?= $_SESSION['nama'] ?? $_SESSION['username'] ?> 👋</p>

        <div class="menu">
            <a href="input_penjualan.php" class="card">➕ Input Penjualan</a>
            <a href="laporan_operator.php" class="card">📅 Laporan Penjualan</a>
        </div>

        <a href="../logout.php" class="logout">🚪 Logout</a>
    </div>
</body>
</html>