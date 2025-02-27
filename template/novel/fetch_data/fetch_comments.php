<?php
require "../../../db/dbconnect.php";
header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$novel_id = $_GET['novel_id'] ?? 0;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

if (!$novel_id) {
    echo json_encode(["success" => false, "error" => "Missing novel_id"]);
    exit;
}

$sql = "SELECT commenter_name, comment_text, created_at FROM Comments WHERE novel_id = ? ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "SQL error: " . $conn->error]);
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

echo json_encode(["success" => true, "comments" => $comments, "has_more" => count($comments) === $limit], JSON_PRETTY_PRINT);
?>
