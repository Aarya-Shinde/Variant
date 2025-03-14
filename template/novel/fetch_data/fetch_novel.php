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

$response = [];

// Fetch novel details
$novel_sql = "SELECT * FROM Novels WHERE novel_id = ?";
if ($stmt = $conn->prepare($novel_sql)) {
    $stmt->bind_param("i", $novel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $response['novel'] = $result->fetch_assoc();
    $stmt->close();
} else {
    echo json_encode(["error" => "SQL error: " . $conn->error]);
    exit;
}

if (!$response['novel']) {
    echo json_encode(["error" => "Novel not found"]);
    exit;
}

// Fetch tags
$tags_sql = "SELECT tag FROM Tags WHERE novel_id = ?";
if ($stmt = $conn->prepare($tags_sql)) {
    $stmt->bind_param("i", $novel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $response['tags'] = [];
    while ($row = $result->fetch_assoc()) {
        $response['tags'][] = $row['tag'];
    }
    $stmt->close();
}

// Fetch chapters
$chapters_sql = "SELECT chapter_number, title FROM Chapters WHERE novel_id = ? ORDER BY chapter_number ASC";
if ($stmt = $conn->prepare($chapters_sql)) {
    $stmt->bind_param("i", $novel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $response['chapters'] = [];
    while ($row = $result->fetch_assoc()) {
        $response['chapters'][] = $row;
    }
    $stmt->close();
}

// Fetch reviews
$reviews_sql = "SELECT reviewer_name, review_text, rating, created_at FROM Reviews WHERE novel_id = ? ORDER BY review_id DESC";
if ($stmt = $conn->prepare($reviews_sql)) {
    $stmt->bind_param("i", $novel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $response['reviews'] = [];
    while ($row = $result->fetch_assoc()) {
        $response['reviews'][] = $row;
    }
    $stmt->close();
}

$conn->close();

// Output JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
exit;
?>