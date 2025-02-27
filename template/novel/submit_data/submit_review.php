<?php

session_start();  // Start session to access logged-in user

header("Content-Type: application/json");
require_once "../../../db/dbconnect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit;
}

// Get `user_id` from session
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION["user_id"];  // Automatically assigned from session

// Get other form data
$novel_id = isset($_POST["novel_id"]) ? (int) $_POST["novel_id"] : null;
$review_text = isset($_POST["review_text"]) ? trim($_POST["review_text"]) : null;
$rating = isset($_POST["rating"]) ? (int) $_POST["rating"] : null;

if (!$novel_id || !$review_text || !isset($rating)) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

// Check if user exists
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

// Insert into Reviews table
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
