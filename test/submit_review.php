<?php
include '../db/dbconnect.php';

session_start(); // Start session to get user_id if logged in

$novel_id = 14; // Change dynamically if needed
$review_text = $_POST['review_text'];
$rating = intval($_POST['rating']);
$user_id = $_SESSION['user_id'] ?? NULL; // Get logged-in user ID, default to NULL
$reviewer_name = $user_id ? NULL : "Anonymous"; // If user is logged in, use NULL to default to username

// Insert review
$sql = "INSERT INTO Reviews (novel_id, user_id, reviewer_name, review_text, rating) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iissi", $novel_id, $user_id, $reviewer_name, $review_text, $rating);
$response = $stmt->execute();

$stmt->close();
$conn->close();

echo json_encode(["success" => $response]);
?>
