<?php
/**********************************************************************
 * fetch_work.php
 * -------------------------------------------------
 * Returns one work (novel OR fan-fic) plus its tags and reviews.
 *
 * Accepted query params  (exactly ONE required)
 *   • novel_id   – integer > 0
 *   • fanfic_id  – integer > 0
 *
 * Output JSON:
 *   {
 *     "work"    : { ...columns... },
 *     "tags"    : [ "tag1", "tag2", ... ],
 *     "reviews" : [ { reviewer_name, review_text, rating, created_at }, ... ]
 *   }
 *********************************************************************/

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require "../../../db/dbconnect.php";
if (!$conn) { echo json_encode(["error"=>"DB connection failed"]); exit; }

/* ---------- 1. validate ID ---------- */
$novel_id  = isset($_GET['novel_id'])  ? intval($_GET['novel_id'])  : 0;
$fanfic_id = isset($_GET['fanfic_id']) ? intval($_GET['fanfic_id']) : 0;

/* XOR check – must supply exactly one ID */
if (($novel_id > 0) == ($fanfic_id > 0)) {
    echo json_encode(["error"=>"Provide novel_id OR fanfic_id"]); exit;
}

$isNovel  = $novel_id > 0;
$workId   = $isNovel ? $novel_id : $fanfic_id;       // unified variable
$idField  = $isNovel ? 'novel_id' : 'fanfic_id';     // which FK column

/* ---------- 2. fetch work row ---------- */
$response = ["work"=>null, "tags"=>[], "reviews"=>[]];

if ($isNovel) {
    $sql = "SELECT * FROM Novels WHERE novel_id = ?";
} else {
    /* map fanfic columns to same keys where possible */
    $sql = "SELECT fanfic_id       AS novel_id,   -- alias keeps front-end happy
                   title,
                   author           AS author_name,
                   url,
                   language,
                   word_count,
                   chapter_count,
                   summary,
                   tags,
                   'fanfic'        AS work_type
            FROM   Fanfic
            WHERE  fanfic_id = ?";
}

$stmt = $conn->prepare($sql) or die(json_encode(["error"=>$conn->error]));
$stmt->bind_param("i", $workId);
$stmt->execute();
$response['work'] = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$response['work']) { echo json_encode(["error"=>"Work not found"]); exit; }

/* ---------- 3. fetch tags ---------- */
if ($isNovel) {
    /* Novel tags live in separate Tags table */
    $tg = $conn->prepare("SELECT tag FROM Tags WHERE novel_id = ?");
    $tg->bind_param("i", $workId);
    $tg->execute();
    $res = $tg->get_result();
    while ($row = $res->fetch_assoc()) { $response['tags'][] = $row['tag']; }
    $tg->close();
} else {
    /* Fan-fic tags already sit in the Fanfic.tags column (comma-sep) */
    if (!empty($response['work']['tags'])) {
        $response['tags'] = array_map('trim',
                            explode(',', $response['work']['tags']));
    }
}

/* ---------- 4. fetch reviews ---------- */
$revSQL = "SELECT reviewer_name, review_text, rating, created_at
           FROM   Reviews
           WHERE  $idField = ?
           ORDER  BY review_id DESC";
$rv = $conn->prepare($revSQL);
$rv->bind_param("i", $workId);
$rv->execute();
$revRes = $rv->get_result();
while ($r = $revRes->fetch_assoc()) { $response['reviews'][] = $r; }
$rv->close();

/* ---------- 5. done ---------- */
$conn->close();
echo json_encode($response, JSON_PRETTY_PRINT);
?>
