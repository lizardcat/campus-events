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
    SELECT e.id, e.title, e.event_date 
    FROM bookmarks b 
    JOIN events e ON b.event_id = e.id 
    WHERE b.user_id = ? 
    ORDER BY e.event_date ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($event_id, $title, $event_date);
$bookmarks = [];
while ($stmt->fetch()) {
    $bookmarks[] = [
        'event_id' => $event_id,
        'title' => $title,
        'event_date' => $event_date
    ];
}
$stmt->close();

$stmt = $conn->prepare("
    SELECT c.id, e.title, e.event_date, c.comment, c.posted_at 
    FROM comments c 
    JOIN events e ON c.event_id = e.id 
    WHERE c.user_id = ? 
    ORDER BY c.posted_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($comment_id, $title, $event_date, $comment, $posted_at);
$comments = [];
while ($stmt->fetch()) {
    $comments[] = [
        'comment_id' => $comment_id,
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
            <h4 class="mb-0">Your Bookmarked Events</h4>
        </div>
        <div class="card-body">
            <?php if (count($bookmarks) === 0): ?>
                <p class="text-muted">You haven't bookmarked any events yet.</p>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($bookmarks as $b): ?>
                        <li class="list-group-item" id="bookmark-<?= $b['event_id'] ?>">
                            <div class="fw-bold">
                                <?= htmlspecialchars($b['title']) ?>
                                <span class="text-muted small">(<?= htmlspecialchars($b['event_date']) ?>)</span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger remove-bookmark-btn"
                                data-event-id="<?= $b['event_id'] ?>">
                                Remove Bookmark
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

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
                        <li class="list-group-item comment-item" data-comment-id="<?= $c['comment_id'] ?>">
                            <div class="fw-bold"><?= htmlspecialchars($c['title']) ?>
                                <span class="text-muted small">(<?= htmlspecialchars($c['event_date']) ?>)</span>
                            </div>
                            <div class="mt-1 comment-text"><?= nl2br(htmlspecialchars($c['comment'])) ?></div>
                            <div class="text-muted small mt-1"><?= htmlspecialchars($c['posted_at']) ?></div>
                            <div class="mt-2">
                                <a href="#" class="btn btn-sm btn-outline-secondary edit-comment-btn">Edit</a>
                                <form method="POST" action="delete_comment.php" class="d-inline delete-comment-form">
                                    <input type="hidden" name="comment_id" value="<?= $c['comment_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        document.querySelectorAll('.remove-bookmark-btn').forEach(button => {
            button.addEventListener('click', function () {
                const eventId = this.dataset.eventId;
                fetch('bookmark.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ event_id: eventId, action: 'remove' })
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const li = document.getElementById(`bookmark-${eventId}`);
                            li.classList.add('text-success');
                            li.innerHTML = 'Bookmark removed.';
                        } else {
                            alert('Failed to remove bookmark.');
                        }
                    });
            });
        });

        document.querySelectorAll('.comment-item').forEach(item => {
            const editBtn = item.querySelector('.edit-comment-btn');
            const deleteForm = item.querySelector('.delete-comment-form');
            const commentId = item.dataset.commentId;
            const commentDiv = item.querySelector('.comment-text');

            bindEdit(editBtn, item, commentDiv, commentId);

            deleteForm.addEventListener('submit', e => {
                e.preventDefault();
                fetch('delete_comment.php', {
                    method: 'POST',
                    body: new FormData(deleteForm)
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            item.classList.add('text-danger');
                            item.innerHTML = 'Comment deleted.';
                        } else {
                            alert(data.error || 'Delete failed');
                        }
                    });
            });
        });

        function bindEdit(editBtn, item, commentDiv, commentId) {
            editBtn.addEventListener('click', e => {
                e.preventDefault();
                if (item.querySelector('textarea')) return;

                const textarea = document.createElement('textarea');
                textarea.className = 'form-control';
                textarea.value = commentDiv.innerHTML.replace(/<br\s*\/?>/gi, '\n');

                const saveBtn = document.createElement('button');
                saveBtn.className = 'btn btn-sm btn-success mt-2';
                saveBtn.textContent = 'Save';

                const cancelBtn = document.createElement('button');
                cancelBtn.className = 'btn btn-sm btn-secondary mt-2 ms-2';
                cancelBtn.textContent = 'Cancel';

                commentDiv.replaceWith(textarea);
                editBtn.replaceWith(saveBtn);
                item.appendChild(cancelBtn);

                cancelBtn.addEventListener('click', () => {
                    textarea.replaceWith(commentDiv);
                    saveBtn.replaceWith(editBtn);
                    cancelBtn.remove();
                    bindEdit(editBtn, item, commentDiv, commentId);
                });

                saveBtn.addEventListener('click', () => {
                    fetch('edit_comment.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            comment_id: commentId,
                            comment: textarea.value.trim()
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success' && data.updated_comment) {
                                const newCommentDiv = document.createElement('div');
                                newCommentDiv.className = 'mt-1 comment-text';
                                newCommentDiv.innerHTML = data.updated_comment;

                                textarea.replaceWith(newCommentDiv);
                                saveBtn.replaceWith(editBtn);
                                cancelBtn.remove();
                                bindEdit(editBtn, item, newCommentDiv, commentId);
                            } else {
                                alert(data.error || 'Edit failed');
                            }
                        });
                });
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>