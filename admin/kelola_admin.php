<?php
require __DIR__ . '/../auth_admin.php';

// pastikan hanya admin yang boleh akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../operator/dashboard_operator.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "pertashop";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

// Tambah Admin
if (isset($_POST['tambah'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = $_POST['nama'];

    $sql = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password', '$nama', 'admin')";
    $conn->query($sql);
    header("Location: kelola_admin.php");
    exit();
}

// Edit Admin
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $nama = $_POST['nama'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username='$username', nama_lengkap='$nama', password='$password' WHERE id=$id AND role='admin'";
    } else {
        $sql = "UPDATE users SET username='$username', nama_lengkap='$nama' WHERE id=$id AND role='admin'";
    }
    $conn->query($sql);
    header("Location: kelola_admin.php");
    exit();
}

// Hapus Admin (tidak bisa hapus diri sendiri)
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    if ($id == $_SESSION['user_id']) {
        echo "<script>alert('❌ Anda tidak bisa menghapus akun sendiri!'); window.location='kelola_admin.php';</script>";
        exit();
    }
    $sql = "DELETE FROM users WHERE id=$id AND role='admin'";
    $conn->query($sql);
    header("Location: kelola_admin.php");
    exit();
}

// Ambil data admin
$admins = $conn->query("SELECT * FROM users WHERE role='admin' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Admin</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f8f9fa; }
        .container { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #007bff; margin-bottom: 20px; }
        h3 { color: #444; margin-top: 20px; }
        form { margin-top: 15px; display: flex; flex-wrap: wrap; gap: 10px; }
        input, button { padding: 10px; border-radius: 6px; border: 1px solid #ccc; }
        input { flex: 1; min-width: 180px; }
        input:focus { border-color: #007bff; outline: none; }
        button { background: #007bff; color: white; font-weight: bold; cursor: pointer; border: none; }
        button:hover { background: #0056b3; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .edit-btn, .delete-btn {
            display: inline-block; padding: 6px 12px; border-radius: 6px; color: white; text-decoration: none; margin: 2px;
        }
        .edit-btn { background: #17a2b8; }
        .edit-btn:hover { background: #11707f; }
        .delete-btn { background: #dc3545; }
        .delete-btn:hover { background: #a71d2a; }
        .note { font-size: 12px; color: #777; }
        .back-btn {
            display: inline-block; margin-top: 20px; padding: 10px 20px;
            background: #6c757d; color: white; border-radius: 6px; text-decoration: none; font-weight: bold;
        }
        .back-btn:hover { background: #495057; }
        .badge { background: #28a745; color: white; padding: 3px 7px; border-radius: 5px; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <h2>👨‍💼 Kelola Admin</h2>

    <!-- Form Tambah Admin -->
    <h3>Tambah Admin Baru</h3>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="nama" placeholder="Nama Lengkap" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="tambah">➕ Tambah</button>
    </form>

    <!-- Daftar Admin -->
    <h3>Daftar Admin</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Nama Lengkap</th>
            <th>Dibuat</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $admins->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td>
                <?= $row['username'] ?>
                <?php if ($row['id'] == $_SESSION['user_id']): ?>
                    <span class="badge">Anda</span>
                <?php endif; ?>
            </td>
            <td><?= $row['nama_lengkap'] ?></td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <!-- Form Edit -->
                <form method="POST" style="display:inline-block;">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="text" name="username" value="<?= $row['username'] ?>" required>
                    <input type="text" name="nama" value="<?= $row['nama_lengkap'] ?>" required>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ganti">
                    <button type="submit" name="edit" class="edit-btn">✏️ Edit</button>
                </form>
                <?php if ($row['id'] != $_SESSION['user_id']): ?>
                    <a class="delete-btn" href="kelola_admin.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus admin ini?')">🗑️ Hapus</a>
                <?php else: ?>
                    <span class="note">(Tidak bisa hapus diri sendiri)</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="dashboard.php" class="back-btn">⬅️ Kembali ke Dashboard</a>
</div>
</body>
</html>
