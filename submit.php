<?php
session_start();
header('Content-Type: application/json');
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$event_date = trim($_POST['event_date'] ?? '');

if ($title === '' || $event_date === '') {
    http_response_code(422);
    echo json_encode(['error' => 'Title and date are required.']);
    exit;
}

// Upload handling
$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $err = $_FILES['image']['error'];
    if ($err === UPLOAD_ERR_OK) {
        // Validate mime
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['image']['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($allowed[$mime])) {
            http_response_code(415);
            echo json_encode(['error' => 'Unsupported image type. Use JPG/PNG/WebP.']);
            exit;
        }

        // Size limit ~2MB
        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            http_response_code(413);
            echo json_encode(['error' => 'Image too large (max 2MB).']);
            exit;
        }

        // Ensure target directory
        $dir = __DIR__ . '/images/events';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        // Generate unique filename
        $ext = $allowed[$mime];
        $basename = bin2hex(random_bytes(8)) . '.' . $ext;
        $destFs = $dir . '/' . $basename;
        $destRel = 'images/events/' . $basename;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $destFs)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save image.']);
            exit;
        }
        $image_path = $destRel;
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Upload error.']);
        exit;
    }
}

// Insert event (adjust columns to your schema)
$stmt = $conn->prepare("INSERT INTO events (title, description, event_date, image_path, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("ssss", $title, $description, $event_date, $image_path);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'DB insert failed.']);
    exit;
}
$event_id = $stmt->insert_id;
$stmt->close();

// Resolve image for immediate UI
$resolved = ($image_path && file_exists(__DIR__ . '/' . $image_path)) ? $image_path : 'images/default_event.jpg';

// Response payload used by renderEventCard()
echo json_encode([
    'id' => $event_id,
    'title' => $title,
    'description' => $description,
    'event_date' => $event_date,
    'image_path_resolved' => $resolved
]);
