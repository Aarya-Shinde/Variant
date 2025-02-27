<?php
header("Content-Type: application/json");
require_once "../../../db/dbconnect.php"; // Ensure correct database connection
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit;
}

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION["user_id"];
$novel_id = isset($_POST["novel_id"]) ? (int) $_POST["novel_id"] : null;
$comment_text = isset($_POST["comment_text"]) ? trim($_POST["comment_text"]) : null;

// Validate input
if (!$novel_id || !$comment_text) {
    echo json_encode(["success" => false, "error" => "Invalid input data"]);
    exit;
}

// Get the username of the commenter
$query = "SELECT username FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(["success" => false, "error" => "User does not exist"]);
    exit;
}

$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

// Insert the comment into the database
$sql = "INSERT INTO Comments (novel_id, user_id, commenter_name, comment_text) 
        VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $novel_id, $user_id, $username, $comment_text);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "SQL Execution failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
