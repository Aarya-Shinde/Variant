<?php
session_start();
require '../../../db/dbconnect.php'; // adjust as needed

$user_id = $_SESSION['user_id'] ?? 1; // fallback for testing
$page = $_GET['page'] ?? 1;

$sql = "SELECT * FROM diary WHERE user_id = ? AND page_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $user_id, $page);
$stmt->execute();
$result = $stmt->get_result();

$data = $result->fetch_assoc() ?? [];
echo json_encode($data);
