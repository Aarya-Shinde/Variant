<?php
require "../../../db/dbconnect.php";
session_start();

header("Content-Type: application/json");

if (!isset($_GET['novel_id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Missing novel_id"]);
    exit;
}

$novel_id = (int) $_GET['novel_id'];
$offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;
$limit = isset($_GET['limit']) ? min(max(1, (int)$_GET['limit']), 50) : 5; // Limit max to 50

if ($novel_id <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Invalid novel_id"]);
    exit;
}

$sql = "SELECT commenter_name, comment_text, created_at FROM Comments WHERE novel_id = ? ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("SQL Error: " . $conn->error);
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Server error"]);
    exit;
}

$stmt->bind_param("iii", $novel_id, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode([
    "success" => true,
    "comments" => $comments,
    "has_more" => count($comments) === $limit
], JSON_PRETTY_PRINT);
?>
