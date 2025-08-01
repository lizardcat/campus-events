<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/head.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT e.title, e.event_date, c.comment, c.posted_at 
    FROM comments c 
    JOIN events e ON c.event_id = e.id 
    WHERE c.user_id = ? 
    ORDER BY c.posted_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($title, $event_date, $comment, $posted_at);

$comments = [];
while ($stmt->fetch()) {
    $comments[] = [
        'title' => $title,
        'event_date' => $event_date,
        'comment' => $comment,
        'posted_at' => $posted_at
    ];
}
$stmt->close();
?>

<div class="container my-5">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Your Comment History</h4>
        </div>
        <div class="card-body">
            <?php if (count($comments) === 0): ?>
                <p class="text-muted">You haven't posted any comments yet.</p>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($comments as $c): ?>
                        <li class="list-group-item">
                            <div class="fw-bold"><?= htmlspecialchars($c['title']) ?> <span
                                    class="text-muted small">(<?= htmlspecialchars($c['event_date']) ?>)</span></div>
                            <div class="mt-1"><?= nl2br(htmlspecialchars($c['comment'])) ?></div>
                            <div class="text-muted small mt-1"><?= htmlspecialchars($c['posted_at']) ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>