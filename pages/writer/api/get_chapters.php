<?php
require '../../../db/dbconnect.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

function sendResponse($statusCode, $message, $success = false) {
    http_response_code($statusCode);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

switch ($method) {
    case 'GET':
        $novelId = $_GET['novel_id'] ?? null;
        if (!$novelId || !filter_var($novelId, FILTER_VALIDATE_INT)) {
            sendResponse(400, 'Invalid or missing Novel ID');
        }

        $stmt = $conn->prepare("SELECT chapter_id, title, content FROM chapters WHERE novel_id = ?");
        if (!$stmt) {
            sendResponse(500, 'Query preparation failed: ' . $conn->error);
        }

        $stmt->bind_param("i", $novelId);
        $stmt->execute();
        $result = $stmt->get_result();
        $chapters = $result->fetch_all(MYSQLI_ASSOC);

        echo json_encode($chapters);
        break;

    case 'POST':  
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['novel_id'], $data['title'], $data['content']) || !filter_var($data['novel_id'], FILTER_VALIDATE_INT)) {
            sendResponse(400, 'Missing or invalid required fields');
        }

        if (isset($data['chapter_id']) && filter_var($data['chapter_id'], FILTER_VALIDATE_INT)) {  
            $stmt = $conn->prepare("UPDATE chapters SET title = ?, content = ? WHERE chapter_id = ?");
            if (!$stmt) {
                sendResponse(500, 'Query preparation failed: ' . $conn->error);
            }
            $stmt->bind_param("ssi", $data['title'], $data['content'], $data['chapter_id']);
            $stmt->execute();
            sendResponse(200, 'Chapter updated successfully', $stmt->affected_rows > 0);
        } else {  
            $stmt = $conn->prepare("INSERT INTO chapters (novel_id, title, content) VALUES (?, ?, ?)");
            if (!$stmt) {
                sendResponse(500, 'Query preparation failed: ' . $conn->error);
            }
            $stmt->bind_param("iss", $data['novel_id'], $data['title'], $data['content']);
            $stmt->execute();
            sendResponse(201, 'Chapter added successfully', true);
        }
        break;

    case 'DELETE':  
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['chapter_id']) || !filter_var($data['chapter_id'], FILTER_VALIDATE_INT)) {
            sendResponse(400, 'Invalid or missing Chapter ID');
        }

        $stmt = $conn->prepare("DELETE FROM chapters WHERE chapter_id = ?");
        if (!$stmt) {
            sendResponse(500, 'Query preparation failed: ' . $conn->error);
        }
        $stmt->bind_param("i", $data['chapter_id']);
        $stmt->execute();
        sendResponse(200, 'Chapter deleted successfully', $stmt->affected_rows > 0);
        break;

    default:
        sendResponse(405, 'Invalid request method');
}
?>
