<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];
$event_id = (int) ($_POST['event_id'] ?? 0);

// Confirm admin role
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

if ($role !== 'admin') {
    http_response_code(403);
    exit('Insufficient permissions');
}

// Delete comments
$stmt = $conn->prepare("DELETE FROM comments WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$stmt->close();

// Proceed to delete event
$stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$stmt->close();

header('Location: index.php');
