<?php
require __DIR__ . '/../auth_admin.php';
$conn = new mysqli("localhost","root","","pertashop");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password, nama, role) VALUES ('$username','$password','$nama','operator')";
    if ($conn->query($sql)) {
        header("Location: kelola_operator.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Operator</title>
</head>
<body>
    <h2>Tambah Operator Baru</h2>
    <form method="POST">
        <label>Username</label><br>
        <input type="text" name="username" required><br><br>

        <label>Nama Lengkap</label><br>
        <input type="text" name="nama" required><br><br>

        <label>Password</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">💾 Simpan</button>
    </form>
</body>
</html>
