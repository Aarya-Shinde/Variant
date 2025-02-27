<?php

header("Content-Type: application/json");

require_once "../../../db/dbconnect.php"; // Ensure correct path

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit;
}

// Get POST data
$novel_id = isset($_POST["novel_id"]) ? (int) $_POST["novel_id"] : null;
$user_id = isset($_POST["user_id"]) ? (int) $_POST["user_id"] : null;
$review_text = isset($_POST["review_text"]) ? trim($_POST["review_text"]) : null;
$rating = isset($_POST["rating"]) ? (int) $_POST["rating"] : null;

// Validate input
if (!$novel_id || !$user_id || !$review_text || !isset($rating)) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

// Check if user_id exists in Users table
$user_check_sql = "SELECT username FROM Users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_check_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_stmt->store_result();

if ($user_stmt->num_rows === 0) {
    echo json_encode(["success" => false, "error" => "User does not exist"]);
    exit;
}

$user_stmt->bind_result($username);
$user_stmt->fetch();
$user_stmt->close();

// Insert into database
$sql = "INSERT INTO Reviews (novel_id, user_id, reviewer_name, review_text, rating) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iissi", $novel_id, $user_id, $username, $review_text, $rating);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "SQL Execution failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();

?>
