<?php
session_start();
header('Content-Type: application/json');
require_once 'includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
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

// Image upload handling
$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $err = $_FILES['image']['error'];
    if ($err === UPLOAD_ERR_OK) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['image']['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($allowed[$mime])) {
            http_response_code(415);
            echo json_encode(['error' => 'Unsupported image type. Use JPG/PNG/WebP.']);
            exit;
        }
        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            http_response_code(413);
            echo json_encode(['error' => 'Image too large (max 2MB).']);
            exit;
        }
        $dir = __DIR__ . '/images/events';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
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

// Insert event
$stmt = $conn->prepare("INSERT INTO events (title, description, event_date, image_path, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("ssss", $title, $description, $event_date, $image_path);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'DB insert failed.']);
    exit;
}
$event_id = $stmt->insert_id;
$stmt->close();

// Resolve image path
$img_src = ($image_path && file_exists(__DIR__ . '/' . $image_path)) ? $image_path : 'images/default_event.jpg';

// Bookmark check helper
function is_event_bookmarked($conn, $user_id, $event_id)
{
    $stmt = $conn->prepare("SELECT 1 FROM bookmarks WHERE user_id = ? AND event_id = ? LIMIT 1");
    $stmt->bind_param("ii", $user_id, $event_id);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}
$bookmarked = isset($_SESSION['user_id']) && is_event_bookmarked($conn, $_SESSION['user_id'], $event_id);

// Build card HTML
ob_start();
?>
<div class="col-md-6 col-lg-4">
    <div class="card shadow h-100">
        <div class="position-relative clickable-area">
            <img src="<?= htmlspecialchars($img_src) ?>" class="card-img-top" alt="Event Image"
                style="height:180px;object-fit:cover;">
            <div class="card-body pb-2">
                <h5 class="card-title mb-1"><?= htmlspecialchars($title) ?></h5>
                <p class="card-text mb-2"><strong>Date:</strong> <?= htmlspecialchars($event_date) ?></p>
                <p class="card-text text-truncate m-0"><?= htmlspecialchars($description) ?></p>
            </div>
            <a href="#" class="stretched-link" data-bs-toggle="modal" data-bs-target="#detailsModal" data-type="event"
                data-title="<?= htmlspecialchars($title) ?>" data-date="<?= htmlspecialchars($event_date) ?>"
                data-desc="<?= htmlspecialchars($description) ?>" data-image="<?= htmlspecialchars($img_src) ?>"
                data-event-id="<?= $event_id ?>"
                data-is-admin="<?= ($_SESSION['role'] ?? '') === 'admin' ? '1' : '0' ?>"
                data-bookmarked="<?= $bookmarked ? 'true' : 'false' ?>">
            </a>
        </div>

        <?php if (isset($_SESSION["user_id"])): ?>
            <div class="px-3 pb-2 d-flex gap-2 justify-content-end">
                <button class="btn btn-sm btn-warning bookmark-btn" data-event-id="<?= $event_id ?>"
                    data-action="<?= $bookmarked ? 'remove' : 'add' ?>">
                    <?= $bookmarked ? 'Unbookmark' : 'Bookmark' ?>
                </button>
            </div>
            <div class="px-3 pb-3 pt-2 comment-interactive position-relative">
                <form method="POST" action="comment.php" class="comment-form" data-event-id="<?= $event_id ?>">
                    <input type="hidden" name="event_id" value="<?= $event_id ?>">
                    <div class="mb-2">
                        <textarea name="comment" class="form-control" rows="2" placeholder="Write a comment..."
                            required></textarea>
                    </div>
                    <div class="comment-error text-danger small mb-2 d-none"></div>
                    <button type="submit" class="btn btn-sm btn-outline-primary">Comment</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if (($_SESSION["role"] ?? '') === 'admin'): ?>
            <div class="d-flex justify-content-between px-3 pb-2">
                <a href="edit_event.php?id=<?= $event_id ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                <form method="POST" action="delete_event.php" onsubmit="return confirm('Delete this event?');">
                    <input type="hidden" name="event_id" value="<?= $event_id ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="card-footer bg-light">
            <h6 class="mb-2">Comments:</h6>
            <div class="comment-list" data-event-id="<?= $event_id ?>">
                <div class="text-muted small">No comments yet.</div>
            </div>
        </div>
    </div>
</div>
<?php
$card_html = ob_get_clean();

// Return JSON
echo json_encode([
    'status' => 'success',
    'card_html' => $card_html
]);
