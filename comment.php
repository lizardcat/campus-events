<?php 

session_start();
include 'db.php';

if (!isset($_SESSION["user_id"], $_POST["event_id"], $_POST["comment"])) {
    die("Invalid request.");
}

$user_id = $_SESSION["user_id"];
$event_id = $_POST["event_id"];
$comment = trim($_POST['comment']);

if ($comment === '') {
    die("Comment is empty.");
}

$stmt = $conn->prepare("INSERT INTO comments (user_id, event_id, comment) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $user_id, $event_id, $comment);
$stmt->execute();

header("Location: index.php");
exit;

