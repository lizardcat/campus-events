<?php
session_start();
include 'includes/db.php';
if (!isset($_SESSION["user_id"]))
    exit;

$id = (int) $_POST['id'];
$title = $_POST['title'];
$description = $_POST['description'];
$event_date = $_POST['event_date'];

$image_path = null;
if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $mime = mime_content_type($_FILES['image']['tmp_name']);
    $size = $_FILES['image']['size'];

    if (in_array($mime, $allowed_types) && $size <= 2 * 1024 * 1024) {
        if (!is_dir('images'))
            mkdir('images', 0755, true);
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $path = 'images/' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
            $image_path = $path;
        }
    }
}

if ($image_path) {
    $stmt = $conn->prepare("UPDATE events SET title=?, description=?, event_date=?, image_path=? WHERE id=?");
    $stmt->bind_param("ssssi", $title, $description, $event_date, $image_path, $id);
} else {
    $stmt = $conn->prepare("UPDATE events SET title=?, description=?, event_date=? WHERE id=?");
    $stmt->bind_param("sssi", $title, $description, $event_date, $id);
}

$stmt->execute();
$stmt->close();
header("Location: index.php");
exit;
