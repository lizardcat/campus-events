<?php
session_start();
header('Content-Type: application/json');
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$event_id = (int) ($_POST['event_id'] ?? 0);

if ($event_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid event ID']);
    exit;
}

// Remove the registration
$stmt = $conn->prepare("DELETE FROM event_registrations WHERE user_id = ? AND event_id = ?");
$stmt->bind_param("ii", $user_id, $event_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Registration canceled successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No registration found for this event']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to cancel registration']);
}

$stmt->close();
