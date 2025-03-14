<?php
header("Content-Type: application/json");
require_once "../../../db/dbconnect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit;
}

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION["user_id"];
$novel_id = isset($_POST["novel_id"]) ? (int) $_POST["novel_id"] : 0;
$comment_text = isset($_POST["comment_text"]) ? trim($_POST["comment_text"]) : "";

if ($novel_id <= 0 || empty($comment_text) || strlen($comment_text) > 255) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Invalid input data"]);
    exit;
}

$query = "SELECT username FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["success" => false, "error" => "User not found"]);
    exit;
}

$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

$sql = "INSERT INTO Comments (novel_id, user_id, commenter_name, comment_text) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $novel_id, $user_id, $username, $comment_text);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    error_log("SQL Error: " . $stmt->error);
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Failed to submit comment"]);
}

$stmt->close();
$conn->close();
?>
