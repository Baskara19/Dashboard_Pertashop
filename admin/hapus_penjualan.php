<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pertashop";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

$id = $_GET['id'] ?? 0;
$tanggal = $_GET['tanggal'] ?? '';

if ($id > 0) {
    $sql = "DELETE FROM penjualan_harian WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // tetap redirect ke tanggal yang sama
        header("Location: laporan_admin.php?tanggal=" . urlencode($tanggal));
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    // fallback, kalau id tidak ada
    header("Location: laporan_admin.php?tanggal=" . urlencode($tanggal));
    exit();
}
?>
