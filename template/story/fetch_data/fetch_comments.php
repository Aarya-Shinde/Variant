<?php
/**********************************************************************
 * fetch_comments.php
 * -------------------------------------------------
 * Returns latest comments for a novel OR fan-fic.
 * Query params:
 *   • novel_id  or fanfic_id     (one required)
 *   • offset    (optional, default 0)
 *   • limit     (1-50, default 5)
 *********************************************************************/

require "../../../db/dbconnect.php";
session_start();
header("Content-Type: application/json");

/* ---------- 1. Input validation ---------- */
$novel_id  = isset($_GET['novel_id'])  ? intval($_GET['novel_id'])  : 0;
$fanfic_id = isset($_GET['fanfic_id']) ? intval($_GET['fanfic_id']) : 0;

if (($novel_id > 0) == ($fanfic_id > 0)) {       // must be exactly one
    http_response_code(400);
    echo json_encode(["success" => false,
                      "error"   => "Provide novel_id OR fanfic_id"]);
    exit;
}

$column = $novel_id ? 'novel_id' : 'fanfic_id';
$workId = $novel_id ?: $fanfic_id;

$offset = max(0, (int)($_GET['offset'] ?? 0));
$limit  = min(max(1, (int)($_GET['limit'] ?? 5)), 50);

/* ---------- 2. Fetch comments ---------- */
$sql = "SELECT commenter_name, comment_text, created_at
        FROM   Comments
        WHERE  $column = ?
        ORDER  BY created_at DESC
        LIMIT  ?, ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("SQL Error: " . $conn->error);
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Server error"]);
    exit;
}

$stmt->bind_param("iii", $workId, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

$comments = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();

echo json_encode([
    "success"  => true,
    "comments" => $comments,
    "has_more" => count($comments) === $limit
], JSON_PRETTY_PRINT);
?>
