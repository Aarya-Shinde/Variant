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
if (!$novel_id || !$user_id || !$review_text || $rating < 1 || $rating > 5) {
    echo json_encode(["success" => false, "error" => "Invalid input data"]);
    exit;
}

// Insert into database
$sql = "INSERT INTO Reviews (novel_id, user_id, reviewer_name, review_text, rating) 
        VALUES (?, ?, (SELECT username FROM Users WHERE user_id = ?), ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iissi", $novel_id, $user_id, $user_id, $review_text, $rating);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Database error"]);
}

$stmt->close();
$conn->close();
?>
