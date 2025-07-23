<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || empty($_POST['novel_id'])) {
    http_response_code(400);
    echo json_encode(['status'=>'error','msg'=>'not_logged_in_or_bad_request']);
    exit;
}

require_once '../../db/dbconnect.php';

$user  = (int)$_SESSION['user_id'];
$novel = (int)$_POST['novel_id'];

/* already in library? */
$check = $conn->prepare("SELECT 1 FROM Library WHERE user_id=? AND novel_id=?");
$check->bind_param("ii", $user, $novel);
$check->execute();
$inLib = $check->get_result()->num_rows;

if ($inLib) {
    $stmt = $conn->prepare(
        "DELETE FROM Library WHERE user_id=? AND novel_id=?"
    );
    $stmt->bind_param("ii", $user, $novel);
    $ok = $stmt->execute();
    echo json_encode($ok
        ? ['status'=>'removed']
        : ['status'=>'error','msg'=>$stmt->error]);
} else {
    $stmt = $conn->prepare(
        "INSERT INTO Library (user_id, novel_id) VALUES (?, ?)"
    );
    $stmt->bind_param("ii", $user, $novel);
    $ok = $stmt->execute();
    echo json_encode($ok
        ? ['status'=>'added']
        : ['status'=>'error','msg'=>$stmt->error]);
}
