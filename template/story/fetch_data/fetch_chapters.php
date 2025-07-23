<?php
/**********************************************************************
 * fetch_chapters.php
 * -------------------------------------------------
 * Returns paginated chapter metadata for ONE work:
 *     • novel_id  OR  fanfic_id   (exactly one required)
 * Response JSON: { success, chapters[], total_pages }
 *********************************************************************/

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require "../../../db/dbconnect.php";

/* ---------- 1. DB check ---------- */
if (!$conn) {
    echo json_encode(["success" => false, "error" => "DB connection failed"]);
    exit;
}

/* ---------- 2. Validate input (one of the two IDs) ---------- */
$novel_id  = isset($_GET['novel_id'])  ? intval($_GET['novel_id'])  : 0;
$fanfic_id = isset($_GET['fanfic_id']) ? intval($_GET['fanfic_id']) : 0;

if (($novel_id > 0) == ($fanfic_id > 0)) {  // XOR check
    echo json_encode(["success" => false,
                      "error"   => "Provide either novel_id OR fanfic_id"]);
    exit;
}

$column = $novel_id ? 'novel_id' : 'fanfic_id';
$workId = $novel_id ?: $fanfic_id;

/* ---------- 3. Pagination setup ---------- */
$limit  = 20;
$page   = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;
$response = ["success" => false, "chapters" => [], "total_pages" => 1];

/* ---------- 4. Count total chapters ---------- */
$countSQL = "SELECT COUNT(*) AS total FROM Chapters WHERE $column = ?";
if ($cnt = $conn->prepare($countSQL)) {
    $cnt->bind_param("i", $workId);
    $cnt->execute();
    $total = $cnt->get_result()->fetch_assoc()['total'] ?? 0;
    $response['total_pages'] = max(1, ceil($total / $limit));
    $cnt->close();
}

/* ---------- 5. Fetch chapter slice ---------- */
$listSQL = "SELECT chapter_id, chapter_number, title
            FROM   Chapters
            WHERE  $column = ?
            ORDER  BY chapter_number
            LIMIT  ? OFFSET ?";
if ($stmt = $conn->prepare($listSQL)) {
    $stmt->bind_param("iii", $workId, $limit, $offset);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $response['chapters'][] = $row;
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
    exit;
}

$conn->close();
$response['success'] = !empty($response['chapters']);
echo json_encode($response);
?>
