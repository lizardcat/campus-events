<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$event_id = (int) ($_POST['event_id'] ?? 0);

$exists = $conn->prepare("SELECT 1 FROM event_bookmarks WHERE user_id = ? AND event_id = ?");
$exists->bind_param("ii", $user_id, $event_id);
$exists->execute();
$exists->store_result();

if ($exists->num_rows > 0) {
    $del = $conn->prepare("DELETE FROM event_bookmarks WHERE user_id = ? AND event_id = ?");
    $del->bind_param("ii", $user_id, $event_id);
    $del->execute();
    echo json_encode(['bookmarked' => false]);
} else {
    $ins = $conn->prepare("INSERT INTO event_bookmarks (user_id, event_id) VALUES (?, ?)");
    $ins->bind_param("ii", $user_id, $event_id);
    $ins->execute();
    echo json_encode(['bookmarked' => true]);
}