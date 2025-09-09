<?php
require __DIR__ . '/../auth_operator.php';

$host = "localhost";
$user = "root";
$pass = "";
$db   = "pertashop";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

$id = $_GET['id'] ?? 0;
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

if ($id > 0) {
    $sql = "DELETE FROM penjualan_harian WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: laporan_operator.php?tanggal=$tanggal");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    header("Location: laporan_operator.php?tanggal=$tanggal");
    exit();
}
