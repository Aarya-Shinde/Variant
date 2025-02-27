<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db/dbconnect.php'; 

$novel_id = $_GET['novel_id'] ?? 14; // Change dynamically if needed

// Fetch novel details
$novel_sql = "SELECT * FROM Novels WHERE novel_id = ?";
$stmt = $conn->prepare($novel_sql);
$stmt->bind_param("i", $novel_id);
$stmt->execute();
$novel_result = $stmt->get_result();
$novel = $novel_result->fetch_assoc();

// Fetch tags
$tags_sql = "SELECT tag FROM Tags WHERE novel_id = ?";
$stmt = $conn->prepare($tags_sql);
$stmt->bind_param("i", $novel_id);
$stmt->execute();
$tags_result = $stmt->get_result();
$tags = [];
while ($row = $tags_result->fetch_assoc()) {
    $tags[] = $row['tag'];
}

// Fetch chapters
$chapters_sql = "SELECT chapter_number, title FROM Chapters WHERE novel_id = ? ORDER BY chapter_number ASC";
$stmt = $conn->prepare($chapters_sql);
$stmt->bind_param("i", $novel_id);
$stmt->execute();
$chapters_result = $stmt->get_result();
$chapters = [];
while ($row = $chapters_result->fetch_assoc()) {
    $chapters[] = $row;
}

// Fetch reviews
$reviews_sql = "SELECT reviewer_name, review_text, rating, created_at FROM Reviews WHERE novel_id = ? ORDER BY review_id DESC";
$stmt = $conn->prepare($reviews_sql);
$stmt->bind_param("i", $novel_id);
$stmt->execute();
$reviews_result = $stmt->get_result();

$reviews = [];
while ($row = $reviews_result->fetch_assoc()) {
    $reviews[] = $row;
}

$stmt->close();
$conn->close();

// Ensure correct JSON output
header('Content-Type: application/json');
echo json_encode([
    "novel" => $novel,
    "tags" => $tags,
    "chapters" => $chapters,
    "reviews" => $reviews
], JSON_PRETTY_PRINT);

?>
