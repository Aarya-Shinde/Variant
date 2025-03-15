<?php
// Enable error reporting for debugging
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

// Database connection
require '../../../db/dbconnect.php';

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Ensure novel_id is provided
if (!isset($_GET["novel_id"]) || empty($_GET["novel_id"])) {
    die(json_encode(["error" => "Missing novel_id"]));
}

$novel_id = $_GET["novel_id"];

// Prepare the query
$query = "SELECT title FROM novels WHERE novel_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die(json_encode(["error" => "SQL error: " . $conn->error]));
}

$stmt->bind_param("i", $novel_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["title" => $row["title"]]);
} else {
    echo json_encode(["error" => "Novel not found"]);
}

$stmt->close();
$conn->close();
?>
