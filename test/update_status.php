<?php
session_start();
include '../../db/dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $novel_id = $_POST['novel_id'];
    $status = $_POST['status'];

    $query = "UPDATE User_Books SET read_status = ? WHERE user_id = ? AND novel_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $status, $user_id, $novel_id);

    if ($stmt->execute()) {
        echo "<script>alert('Reading status updated!'); window.location.href='library.php';</script>";
    } else {
        echo "<script>alert('Failed to update status.');</script>";
    }
}
?>
