<?php
session_start();
header('Content-Type: application/json');
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$comment_id = isset($_POST['comment_id']) ? (int) $_POST['comment_id'] : 0;
$new_comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

if ($comment_id <= 0 || $new_comment === '') {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Confirm ownership
$stmt = $conn->prepare("SELECT user_id FROM comments WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$stmt->bind_result($owner_id);
if (!$stmt->fetch()) {
    $stmt->close();
    echo json_encode(['error' => 'Comment not found']);
    exit;
}
$stmt->close();

if ($owner_id !== $_SESSION['user_id']) {
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// Update comment
$stmt = $conn->prepare("UPDATE comments SET comment = ? WHERE id = ? LIMIT 1");
$stmt->bind_param("si", $new_comment, $comment_id);
if (!$stmt->execute()) {
    $stmt->close();
    echo json_encode(['error' => 'Database update failed']);
    exit;
}
if ($stmt->affected_rows === 0) {
    $stmt->close();
    echo json_encode(['error' => 'No changes made']);
    exit;
}
$stmt->close();

echo json_encode([
    'status' => 'success',
    'updated_comment' => nl2br(htmlspecialchars($new_comment))
]);
