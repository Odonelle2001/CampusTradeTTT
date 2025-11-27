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

$sql = "
    SELECT
        p.id,
        p.user_id,
        p.type,
        p.text,
        p.image_data,
        p.event_datetime,
        p.created_at,
        a.first_name,
        a.last_name,
        a.email,
        u.profile_image,
        EXISTS(
            SELECT 1
            FROM post_likes pl
            WHERE pl.post_id = p.id
              AND pl.user_id = ?
        ) AS liked_by_me,
        (
            SELECT COUNT(*)
            FROM post_likes pl2
            WHERE pl2.post_id = p.id
        ) AS like_count
    FROM posts p
    JOIN accounts a ON a.id = p.user_id
    LEFT JOIN userprofile u ON u.user_id = a.id
    ORDER BY p.created_at DESC
";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();

$posts = [];

while ($row = $res->fetch_assoc()) {
    $postId   = (int) $row['id'];
    $fullName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
    if ($fullName === '') {
        $fullName = $row['email'] ?? 'Student User';
    }

    $avatar = !empty($row['profile_image']) ? $row['profile_image'] : 'Images/ProfileIcon.png';

    // Comments for this post
    $cStmt = $db->prepare("
        SELECT
            c.comment_text,
            c.created_at,
            a.first_name,
            a.last_name,
            a.email
        FROM post_comments c
        JOIN accounts a ON a.id = c.user_id
        WHERE c.post_id = ?
        ORDER BY c.created_at ASC
    ");
    $cStmt->bind_param("i", $postId);
    $cStmt->execute();
    $cRes = $cStmt->get_result();

    $comments = [];
    while ($cRow = $cRes->fetch_assoc()) {
        $cName = trim(($cRow['first_name'] ?? '') . ' ' . ($cRow['last_name'] ?? ''));
        if ($cName === '') {
            $cName = $cRow['email'] ?? 'Student';
        }
        $comments[] = [
            'author'    => $cName,
            'text'      => $cRow['comment_text'],
            'createdAt' => $cRow['created_at'],
        ];
    }
    $cStmt->close();

    $posts[] = [
        'id'            => $postId,
        'type'          => $row['type'],
        'text'          => $row['text'],
        'imageData'     => $row['image_data'],
        'eventDateTime' => $row['event_datetime'],
        'createdAt'     => $row['created_at'],
        'author'        => $fullName,
        'authorEmail'   => $row['email'],
        'authorAvatar'  => $avatar,
        'likes'         => (int) $row['like_count'],
        'liked'         => (bool) $row['liked_by_me'],
        'comments'      => $comments,
    ];
}

$stmt->close();
echo json_encode($posts);