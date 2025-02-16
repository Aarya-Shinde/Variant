<?php
session_start();
include '../../db/dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $novel_id = $_POST['novel_id'];

    $query = "INSERT INTO User_Books (user_id, novel_id, read_status) VALUES (?, ?, 'Want to Read')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $novel_id);

    if ($stmt->execute()) {
        echo "<script>alert('Book added to library!'); window.location.href='library.php';</script>";
    } else {
        echo "<script>alert('Failed to add book.');</script>";
    }
}
?>
