<?php
session_start();
header('Content-Type: application/json');
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
$event_id = isset($_POST['event_id']) ? (int) $_POST['event_id'] : 0;
$user_id = (int) $_SESSION['user_id'];

if ($event_id <= 0 || $comment === '') {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO comments (event_id, user_id, comment, posted_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $event_id, $user_id, $comment);
$stmt->execute();
$last_id = $conn->insert_id;
$stmt->close();

// fetch newly inserted comment with username
$stmt = $conn->prepare("
    SELECT c.comment, c.posted_at, u.username
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.id = ?
");
$stmt->bind_param("i", $last_id);
$stmt->execute();
$stmt->bind_result($f_comment, $posted_at, $username);
$stmt->fetch();
$stmt->close();

echo json_encode([
    'comment' => nl2br(htmlspecialchars($f_comment ?? '')),
    'posted_at' => $posted_at ?? '',
    'username' => htmlspecialchars($username ?? '')
]);
