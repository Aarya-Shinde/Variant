<?php
require "../../../db/dbconnect.php";
// Ensure this file is correct

header("Content-Type: application/json"); // Always return JSON
error_reporting(E_ALL);
ini_set('display_errors', 1);

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

$sql = "SELECT commenter_name, comment_text, created_at FROM Comments ORDER BY created_at DESC LIMIT ?, ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "SQL error: " . $conn->error]);
    exit;
}
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

// Debugging: If no data, log errors
if (empty($comments)) {
    echo json_encode(["error" => "No comments found or database issue"]);
    exit;
}

echo json_encode(["comments" => $comments, "has_more" => count($comments) === $limit]);

$stmt->close();
$conn->close();
?>

