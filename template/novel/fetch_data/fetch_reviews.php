<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include("../../../db/dbconnect.php");


if (!$conn) {
    die(json_encode(["error" => "Database connection failed"]));
}


$novel_id = $_GET['novel_id'] ?? 14;

$sql = "SELECT reviewer_name, review_text, rating, created_at FROM Reviews WHERE novel_id = ? ORDER BY review_id DESC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["error" => "Failed to prepare SQL statement"]));
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

echo json_encode(["reviews" => $reviews], JSON_PRETTY_PRINT);
?>
