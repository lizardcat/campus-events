<?php
session_start();
header('Content-Type: application/json');
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to register.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$event_id = (int) ($_POST['event_id'] ?? 0);

if ($event_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid event.']);
    exit;
}

// Check if already registered
$stmt = $conn->prepare("SELECT 1 FROM event_registrations WHERE user_id = ? AND event_id = ?");
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'You are already registered for this event.']);
    exit;
}
$stmt->close();

// Insert registration
$stmt = $conn->prepare("INSERT INTO event_registrations (user_id, event_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $event_id);
if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to register.']);
    exit;
}
$stmt->close();

// Get user email and name
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

// Get event title
$stmt = $conn->prepare("SELECT title FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$stmt->bind_result($event_title);
$stmt->fetch();
$stmt->close();

// Send confirmation email
$subject = "Registration Confirmation: $event_title";
$message = "Hello $username,\n\nYou have successfully registered for \"$event_title\".\nWe look forward to seeing you there!\n\nUSIU Campus Events Team";
$headers = "From: no-reply@localhost\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

mail($email, $subject, $message, $headers);

echo json_encode(['status' => 'success', 'message' => 'Registration confirmed!']);
