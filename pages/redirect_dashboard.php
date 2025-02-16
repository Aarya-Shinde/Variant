<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dashboard'])) {
    if ($_POST['dashboard'] == "reader") {
        header("Location: reader_dashboard.php");
    } elseif ($_POST['dashboard'] == "writer") {
        header("Location: writer_dashboard.php");
    } elseif ($_POST['dashboard'] == "admin") {
        header("Location: admin_dashboard.php");
    }
    exit();
}

header("Location: login.php");
exit();
?>
