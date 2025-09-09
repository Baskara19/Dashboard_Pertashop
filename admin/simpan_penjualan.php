<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pertashop";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal'];
    $shift = $_POST['shift'];
    $odo_awal = (int)$_POST['odo_awal'];
    $odo_akhir = (int)$_POST['odo_akhir'];
    $ukur_awal = (float)$_POST['pengukuran_awal'];
    $ukur_akhir = (float)$_POST['pengukuran_akhir'];

    // rumus
    $penjualan_liter = $odo_akhir - $odo_awal;
    $penghasilan_rp  = $penjualan_liter * 12400;
    $stok_hari_ini   = ($ukur_awal - $ukur_akhir) * 21.23;

    // simpan ke DB
    $sql = "INSERT INTO penjualan_harian (nama, tanggal, shift, odo_awal, odo_akhir, penjualan_liter, penghasilan_rp, ukur_awal, ukur_akhir, stok_hari_ini) 
            VALUES ('$nama','$tanggal','$shift','$odo_awal','$odo_akhir','$penjualan_liter','$penghasilan_rp','$ukur_awal','$ukur_akhir','$stok_hari_ini')";

    if ($conn->query($sql) === TRUE) {
        // langsung redirect ke laporan.php dengan tanggal yang diinput
        header("Location: laporan.php?tanggal=$tanggal");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
