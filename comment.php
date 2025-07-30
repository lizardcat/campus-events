<?php
session_start();
header('Content-Type: application/json');
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$comment = trim($_POST['comment']);
$event_id = intval($_POST['event_id']);
$user_id = $_SESSION['user_id'];

if ($comment === '') {
    echo json_encode(['error' => 'Empty comment']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO comments (event_id, user_id, comment, posted_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $event_id, $user_id, $comment);
$stmt->execute();

// Retrieve the new comment with username and timestamp
$stmt = $conn->prepare("
    SELECT c.comment, c.posted_at, u.username 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.id = ?
");
$last_id = $conn->insert_id;
$stmt->bind_param("i", $last_id);
$stmt->execute();
$stmt->bind_result($fetched_comment, $posted_at, $username);
$stmt->fetch();
$stmt->close();

echo json_encode([
    'comment' => nl2br(htmlspecialchars($fetched_comment)),
    'posted_at' => $posted_at,
    'username' => htmlspecialchars($username)
]);
exit;
