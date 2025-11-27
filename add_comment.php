<?php
session_start();

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

header('Content-Type: application/json');

$db     = require __DIR__ . '/Database.php';
$userId = (int) $_SESSION['user_id'];

$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$postId = isset($input['post_id']) ? (int) $input['post_id'] : 0;
$text   = trim((string) ($input['text'] ?? ''));

if ($postId <= 0 || $text === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}
if (mb_strlen($text) > 200) {
    $text = mb_substr($text, 0, 200);
}

$stmt = $db->prepare("
    INSERT INTO post_comments (post_id, user_id, comment_text)
    VALUES (?, ?, ?)
");
$stmt->bind_param("iis", $postId, $userId, $text);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to add comment']);
    exit;
}

$stmt->close();
echo json_encode(['success' => true]);
