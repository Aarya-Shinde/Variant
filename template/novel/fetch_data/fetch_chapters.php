<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require "../../../db/dbconnect.php";

if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

if (!isset($_GET['novel_id'])) {
    echo json_encode(["error" => "Missing novel_id"]);
    exit;
}

$novel_id = intval($_GET['novel_id']);
if ($novel_id <= 0) {
    echo json_encode(["error" => "Invalid novel_id"]);
    exit;
}

// Pagination settings
$limit = 20;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = max(1, $page); // Ensure page is at least 1
$offset = ($page - 1) * $limit;

$response = ["success" => false, "chapters" => [], "total_pages" => 1];

// Get total chapters count for pagination
$countQuery = "SELECT COUNT(*) AS total FROM Chapters WHERE novel_id = ?";
if ($countStmt = $conn->prepare($countQuery)) {
    $countStmt->bind_param("i", $novel_id);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalChapters = $countResult->fetch_assoc()['total'];
    $response['total_pages'] = ceil($totalChapters / $limit);
    $countStmt->close();
}

// Fetch chapters for the current page
$query = "SELECT chapter_id, chapter_number, title FROM Chapters WHERE novel_id = ? ORDER BY chapter_number ASC LIMIT ? OFFSET ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("iii", $novel_id, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response['chapters'][] = $row;
    }
    $stmt->close();
} else {
    echo json_encode(["error" => "SQL error: " . $conn->error]);
    exit;
}

$conn->close();

if (!empty($response['chapters'])) {
    $response['success'] = true;
}

echo json_encode($response);
exit;
