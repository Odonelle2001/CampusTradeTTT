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

// already liked?
$stmt = $db->prepare("SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?");
$stmt->bind_param("ii", $postId, $userId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $del = $db->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?");
    $del->bind_param("ii", $postId, $userId);
    $del->execute();
    $del->close();
    $liked = false;
} else {
    $stmt->close();
    $ins = $db->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)");
    $ins->bind_param("ii", $postId, $userId);
    $ins->execute();
    $ins->close();
    $liked = true;
}

// count likes
$cStmt = $db->prepare("SELECT COUNT(*) AS c FROM post_likes WHERE post_id = ?");
$cStmt->bind_param("i", $postId);
$cStmt->execute();
$cRes  = $cStmt->get_result();
$count = 0;
if ($row = $cRes->fetch_assoc()) {
    $count = (int) $row['c'];
}
$cStmt->close();

echo json_encode([
    'success' => true,
    'post_id' => $postId,
    'liked'   => $liked,
    'likes'   => $count,
]);
