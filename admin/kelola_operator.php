<?php
require __DIR__ . '/../auth_admin.php'; // hanya admin yang bisa akses

$host = "localhost";
$user = "root";
$pass = "";
$db   = "pertashop";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

// Tambah Operator
if (isset($_POST['tambah'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = $_POST['nama'];

    $sql = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password', '$nama', 'operator')";
    $conn->query($sql);
    header("Location: kelola_operator.php");
    exit();
}

// Edit Operator
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $nama = $_POST['nama'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username='$username', nama_lengkap='$nama', password='$password' WHERE id=$id";
    } else {
        $sql = "UPDATE users SET username='$username', nama_lengkap='$nama' WHERE id=$id";
    }
    $conn->query($sql);
    header("Location: kelola_operator.php");
    exit();
}

// Hapus Operator
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $sql = "DELETE FROM users WHERE id=$id AND role='operator'";
    $conn->query($sql);
    header("Location: kelola_operator.php");
    exit();
}

// Ambil data operator
$operators = $conn->query("SELECT * FROM users WHERE role='operator' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Operator</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 30px;
            background: #f9f9f9;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .card h3 {
            margin-top: 0;
            color: #27ae60;
        }
        form input, form button {
            padding: 10px;
            margin: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        form button {
            background: #27ae60;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        form button:hover {
            background: #1e8449;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            font-size: 14px;
        }
        th {
            background: #27ae60;
            color: white;
        }
        tr:nth-child(even) {
            background: #f4f4f4;
        }
        .btn-delete {
            padding: 6px 12px;
            background: #e74c3c;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-delete:hover {
            background: #c0392b;
        }
        .btn-dashboard {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background: #2980b9;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }
        .btn-dashboard:hover {
            background: #1c5985;
        }
        .edit-form {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .edit-form input {
            min-width: 120px;
        }
        .edit-form button {
            background: #f39c12;
        }
        .edit-form button:hover {
            background: #d68910;
        }
    </style>
</head>
<body>
    <h2>👥 Kelola Operator</h2>

    <div class="card">
        <h3>➕ Tambah Operator Baru</h3>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="tambah">Tambah</button>
        </form>
    </div>

    <div class="card">
        <h3>📋 Daftar Operator</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = $operators->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['username'] ?></td>
                <td><?= $row['nama_lengkap'] ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <!-- Form Edit -->
                    <form method="POST" class="edit-form">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="text" name="username" value="<?= $row['username'] ?>" required>
                        <input type="text" name="nama" value="<?= $row['nama_lengkap'] ?>" required>
                        <input type="password" name="password" placeholder="Password baru (opsional)">
                        <button type="submit" name="edit">✏️ Simpan</button>
                    </form>
                    <a class="btn-delete" href="kelola_operator.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus operator ini?')">🗑️ Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div style="text-align:center;">
        <a href="dashboard.php" class="btn-dashboard">⬅️ Kembali ke Dashboard</a>
    </div>
</body>
</html>
