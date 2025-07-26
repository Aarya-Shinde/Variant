<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require "../../../db/dbconnect.php";

if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Validate and sanitize novel_id
if (!isset($_GET['novel_id']) || !ctype_digit($_GET['novel_id'])) {
    echo json_encode(["error" => "Missing or invalid novel_id"]);
    exit;
}

$novel_id = intval($_GET['novel_id']);
if ($novel_id <= 0) {
    echo json_encode(["error" => "Invalid novel_id"]);
    exit;
}

$response = [
    "novel" => null,
    "tags" => [],
    "reviews" => []
];

// Fetch novel details
$novel_sql = "SELECT * FROM Novels WHERE novel_id = ?";
if ($stmt = $conn->prepare($novel_sql)) {
    $stmt->bind_param("i", $novel_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $response['novel'] = $result->fetch_assoc();
    } else {
        echo json_encode(["error" => "SQL execution error: " . $stmt->error]);
        exit;
    }
    $stmt->close();
} else {
    echo json_encode(["error" => "SQL prepare error: " . $conn->error]);
    exit;
}

// If novel not found, return an error
if (!$response['novel']) {
    echo json_encode(["error" => "Novel not found"]);
    exit;
}

// Fetch tags
$tags_sql = "SELECT tag FROM Tags WHERE novel_id = ?";
if ($stmt = $conn->prepare($tags_sql)) {
    $stmt->bind_param("i", $novel_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $response['tags'][] = $row['tag'];
        }
    } else {
        echo json_encode(["error" => "SQL execution error: " . $stmt->error]);
        exit;
    }
    $stmt->close();
}

// Fetch reviews
$reviews_sql = "SELECT reviewer_name, review_text, rating, created_at FROM Reviews WHERE novel_id = ? ORDER BY review_id DESC";
if ($stmt = $conn->prepare($reviews_sql)) {
    $stmt->bind_param("i", $novel_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $response['reviews'][] = $row;
        }
    } else {
        echo json_encode(["error" => "SQL execution error: " . $stmt->error]);
        exit;
    }
    $stmt->close();
}



$conn->close();

// Output JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
exit;
?>
