<?php
// pakai session khusus admin
session_name("PERTASHOP_ADMIN");
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
