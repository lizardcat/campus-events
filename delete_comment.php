<?php
session_start();
header('Content-Type: application/json');
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$comment_id = isset($_POST['comment_id']) ? (int) $_POST['comment_id'] : 0;

if ($comment_id <= 0) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Confirm ownership
$stmt = $conn->prepare("SELECT user_id FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$stmt->bind_result($owner_id);
$stmt->fetch();
$stmt->close();

if ($owner_id !== $_SESSION['user_id']) {
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// Delete
$stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$stmt->close();

echo json_encode(['status' => 'success']);
