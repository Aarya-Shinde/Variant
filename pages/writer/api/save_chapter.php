<?php
session_start();
header('Content-Type: application/json');
require_once dirname(__DIR__).'../../../db/dbconnect.php';

$author = $_SESSION['user_id'] ?? 0;
$story  = (int)($_POST['story']   ?? 0);
$chap   = (int)($_POST['chapter'] ?? 0);
$title  = $_POST['title']   ?? '';
$cont   = $_POST['content'] ?? '';

/* ownership check */
$own = $conn->prepare("SELECT 1 FROM WriterStories WHERE story_id=? AND author_id=?");
$own->bind_param("ii",$story,$author); $own->execute();
if(!$own->get_result()->num_rows){ echo json_encode(['status'=>'error','msg'=>'no access']); exit; }

if($chap){
    $u = $conn->prepare("UPDATE WriterChapters SET title=?,content=?,status='DRAFT' WHERE chapter_id=? AND story_id=?");
    $u->bind_param("ssii",$title,$cont,$chap,$story);
    echo json_encode($u->execute()?['status'=>'ok']:['status'=>'error','msg'=>$u->error]);
} else {
    $i = $conn->prepare("INSERT INTO WriterChapters (story_id,title,content,status) VALUES (?,?,?,'DRAFT')");
    $i->bind_param("iss",$story,$title,$cont);
    echo json_encode($i->execute()?['status'=>'ok','id'=>$conn->insert_id]:['status'=>'error','msg'=>$i->error]);
}
