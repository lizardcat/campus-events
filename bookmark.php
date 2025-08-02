<?php
session_start();
header('Content-Type: application/json');
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$event_id = isset($_POST['event_id']) ? (int) $_POST['event_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($event_id <= 0 || ($action !== 'add' && $action !== 'remove')) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

// Verify event exists
$stmt = $conn->prepare("SELECT id FROM events WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    echo json_encode(['status' => 'error', 'message' => 'Event not found']);
    exit;
}
$stmt->close();

if ($action === 'add') {
    $stmt = $conn->prepare("INSERT IGNORE INTO bookmarks (user_id, event_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $event_id);
    if (!$stmt->execute()) {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Database insert failed']);
        exit;
    }
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'Event bookmarked']);
    exit;
}

if ($action === 'remove') {
    $stmt = $conn->prepare("DELETE FROM bookmarks WHERE user_id = ? AND event_id = ? LIMIT 1");
    $stmt->bind_param("ii", $user_id, $event_id);
    if (!$stmt->execute()) {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Database delete failed']);
        exit;
    }
    if ($stmt->affected_rows === 0) {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Bookmark not found']);
        exit;
    }
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'Bookmark removed']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Unhandled action']);
