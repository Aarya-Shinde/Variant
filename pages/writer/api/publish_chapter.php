<?php
session_start();
header('Content-Type: application/json');
require_once dirname(__DIR__).'../../../db/dbconnect.php';

$author = $_SESSION['user_id'] ?? 0;
$story  = (int)($_POST['story']   ?? 0);
$chap   = (int)($_POST['chapter'] ?? 0);

$own = $conn->prepare("SELECT 1 FROM WriterStories WHERE story_id=? AND author_id=?");
$own->bind_param("ii",$story,$author); $own->execute();
if(!$own->get_result()->num_rows){ echo json_encode(['status'=>'error','msg'=>'no access']); exit; }

$up = $conn->prepare("UPDATE WriterChapters SET status='PUBLISHED' WHERE chapter_id=? AND story_id=?");
$up->bind_param("ii",$chap,$story);
echo json_encode($up->execute()?['status'=>'ok']:['status'=>'error','msg'=>$up->error]);
