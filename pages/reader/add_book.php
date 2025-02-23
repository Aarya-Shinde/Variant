<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];  // Assuming user is logged in
    $novel_id = $_POST['novel_id'];
    $read_status = $_POST['read_status'];

    $sql = "INSERT INTO Library (user_id, novel_id, read_status) VALUES ('$user_id', '$novel_id', '$read_status')";
    if (mysqli_query($conn, $sql)) {
        echo "Book added successfully!";
        header("Location: library.php"); // Redirect back to library page
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
