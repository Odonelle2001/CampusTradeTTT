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

$input = json_decode(file_get_contents('php://input'), true) ?? [];

$type = $input['type'] ?? 'post';
if (!in_array($type, ['post', 'event'], true)) {
    $type = 'post';
}

$text = trim((string) ($input['text'] ?? ''));
if ($text === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Text is required']);
    exit;
}
if (mb_strlen($text) > 500) {
    $text = mb_substr($text, 0, 500);
}

$imageData = $input['imageData'] ?? null;
if ($imageData !== null && !is_string($imageData)) {
    $imageData = null;
}

$eventDateTime   = $input['eventDateTime'] ?? null;
$eventDateTimeDb = null;
if ($type === 'event' && $eventDateTime) {
    $ts = strtotime($eventDateTime);
    if ($ts !== false) {
        $eventDateTimeDb = date('Y-m-d H:i:s', $ts);
    }
}

$stmt = $db->prepare("
    INSERT INTO posts (user_id, type, text, image_data, event_datetime)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("issss", $userId, $type, $text, $imageData, $eventDateTimeDb);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create post']);
    exit;
}

$stmt->close();
echo json_encode(['success' => true]);
