<?php
require "../../../db/dbconnect.php";
session_start();

header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['novel_id'])) {
    echo json_encode(["success" => false, "error" => "Missing novel_id"]);
    exit;
}

$novel_id = intval($_GET['novel_id']);
if ($novel_id <= 0) {
    echo json_encode(["success" => false, "error" => "Invalid novel_id"]);
    exit;
}

$sql = "SELECT * FROM Reviews WHERE novel_id = ?";
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

echo json_encode([
    "success" => true,
    "reviews" => $reviews,
    "query_debug" => $sql, // Debugging purpose
    "novel_id_received" => $novel_id // Debugging purpose
], JSON_PRETTY_PRINT);

$stmt->close();
$conn->close();
?>