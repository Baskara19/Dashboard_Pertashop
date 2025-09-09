<?php
// pakai session khusus operator
session_name("PERTASHOP_OPERATOR");
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'operator') {
    header("Location: ../login.php");
    exit();
}
?>
