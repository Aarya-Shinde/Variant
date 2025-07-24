<?php
session_start();
require '../../../db/dbconnect.php'; // adjust path to your DB config

$user_id = $_SESSION['user_id'] ?? 1; // fallback for testing
$page = $_POST['page'] ?? 1;
$content = $_POST['content'] ?? '';
$stickers = $_POST['stickers'] ?? '[]';
$font = $_POST['font'] ?? 'Georgia';
$skin = $_POST['skin'] ?? 'vintage';

$sql = "INSERT INTO diary (user_id, page_number, content, stickers, font_style, skin_style)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        content = VALUES(content), stickers = VALUES(stickers),
        font_style = VALUES(font_style), skin_style = VALUES(skin_style)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iissss', $user_id, $page, $content, $stickers, $font, $skin);
$stmt->execute();

echo "Saved page $page";
