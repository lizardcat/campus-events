<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$event_id = (int) $_POST['event_id'];
$action = $_POST['action'];

header('Content-Type: application/json');

if ($action === 'add') {
    $stmt = $conn->prepare("INSERT IGNORE INTO bookmarks (user_id, event_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $event_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'Event bookmarked']);
} elseif ($action === 'remove') {
    $stmt = $conn->prepare("DELETE FROM bookmarks WHERE user_id = ? AND event_id = ?");
    $stmt->bind_param("ii", $user_id, $event_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'Bookmark removed']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
exit;
