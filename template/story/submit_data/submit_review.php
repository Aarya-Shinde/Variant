<?php
header("Content-Type: application/json");
require_once "../../../db/dbconnect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Invalid JSON received"]);
    exit;
}

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION["user_id"];
$novel_id = isset($data["novel_id"]) ? (int) $data["novel_id"] : null;
$review_text = isset($data["review_text"]) ? trim($data["review_text"]) : null;
$rating = isset($data["rating"]) ? (int) $data["rating"] : null;

if (!$novel_id || empty($review_text) || !isset($rating) || $rating < 1 || $rating > 5) {
    echo json_encode(["success" => false, "error" => "Invalid input data"]);
    exit;
}

$query = "SELECT username FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(["success" => false, "error" => "User does not exist"]);
    exit;
}

$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

$sql = "INSERT INTO Reviews (novel_id, user_id, reviewer_name, review_text, rating, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iissi", $novel_id, $user_id, $username, $review_text, $rating);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Review added"]);
} else {
    echo json_encode(["success" => false, "error" => "Insert failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>