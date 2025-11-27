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

if ($postId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid post']);
    exit;
}

// check ownership
$stmt = $db->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->bind_param("i", $postId);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row || (int) $row['user_id'] !== $userId) {
    http_response_code(403);
    echo json_encode(['error' => 'Not allowed']);
    exit;
}

// delete post (likes/comments removed by foreign key cascade)
$dStmt = $db->prepare("DELETE FROM posts WHERE id = ?");
$dStmt->bind_param("i", $postId);
$dStmt->execute();
$dStmt->close();

echo json_encode(['success' => true]);
