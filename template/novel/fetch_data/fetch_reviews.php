<?php
require "../../../db/dbconnect.php";
header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$novel_id = $_GET['novel_id'] ?? 0;
if (!$novel_id) {
    echo json_encode(["success" => false, "error" => "Missing novel_id"]);
    exit;
}

$sql = "SELECT reviewer_name, review_text, rating, created_at FROM Reviews WHERE novel_id = ? ORDER BY review_id DESC";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "SQL error: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $novel_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode(["success" => true, "reviews" => $reviews], JSON_PRETTY_PRINT);
?>
