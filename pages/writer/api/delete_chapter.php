<?php
session_start();
header('Content-Type: application/json');
require_once '../../../db/dbconnect.php';

$author = $_SESSION['user_id'] ?? 0;
$story  = (int)($_POST['story_id'] ?? 0);
$chap   = (int)($_POST['chapter_id'] ?? 0);

$check = $conn->prepare("SELECT 1 FROM WriterStories WHERE story_id=? AND author_id=?");
$check->bind_param("ii", $story, $author);
$check->execute();

if (!$check->get_result()->num_rows) {
  echo json_encode(['status'=>'error','msg'=>'no access']);
  exit;
}

$trash = $conn->prepare("UPDATE WriterChapters SET status='TRASH' WHERE chapter_id=? AND story_id=?");
$trash->bind_param("ii", $chap, $story);

echo json_encode($trash->execute()
  ? ['status'=>'ok']
  : ['status'=>'error','msg'=>$trash->error]);
