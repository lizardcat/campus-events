<?php
session_start();
include 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== 'admin') {
    http_response_code(403);
    exit('Forbidden');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    exit('Invalid event ID');
}

$event_id = (int) $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    exit('Event not found');
}

$event = $result->fetch_assoc();
$stmt->close();
?>

<div class="container my-5 p-4 border rounded bg-light shadow">
    <h2 class="text-primary mb-4">Edit Event</h2>
    <form method="POST" action="update_event.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($event['id']) ?>">

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($event['title']) ?>"
                required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"
                rows="4"><?= htmlspecialchars($event['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="event_date" class="form-control"
                value="<?= htmlspecialchars($event['event_date']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Current Image</label><br>
            <img src="<?= htmlspecialchars($event['image_path'] ?: 'images/default_event.jpg') ?>" alt="Event Image"
                class="img-fluid mb-2" style="max-height:200px;object-fit:cover;">
        </div>

        <div class="mb-3">
            <label class="form-label">Replace Image (optional)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <div class="form-text">JPG/PNG/WebP ≤ 2MB.</div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning text-dark fw-bold">Update</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>