<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$event_id = (int) ($_POST['event_id'] ?? 0);
$status = $_POST['rsvp_status'] ?? '';

if (!in_array($status, ['Going', 'Interested', 'Not Going'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid status']);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO event_rsvps (user_id, event_id, rsvp_status) 
    VALUES (?, ?, ?) 
    ON DUPLICATE KEY UPDATE rsvp_status = VALUES(rsvp_status), timestamp = CURRENT_TIMESTAMP
");
$stmt->bind_param("iis", $user_id, $event_id, $status);
$stmt->execute();
echo json_encode(['success' => true]);
