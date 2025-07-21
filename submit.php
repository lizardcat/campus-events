<?php 

include 'db.php';

$tiitle = $_POST['title'];
$description = $_POST['description'];
$event_date = $_POST['event_date'];

$stmt = $conn->prepare("INSERT INTO events (title, description, event_date) VALUES (?, ?, ?)");

$stmt->bind_param("sss", $title, $description, $event_date);

$stmt->execute();

header("Location: index.php");
exit;