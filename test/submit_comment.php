<?php
require "../db/dbconnect.php"; // Include database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $comment_text = trim($_POST["comment_text"]);
    $commenter_name = "Anonymous"; // Default, modify as needed

    if (empty($comment_text)) {
        echo json_encode(["success" => false, "message" => "Empty comment"]);
        exit;
    }

    $sql = "INSERT INTO Comments (commenter_name, comment_text) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $commenter_name, $comment_text);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to insert"]);
    }

    $stmt->close();
    $conn->close();
}
?>
