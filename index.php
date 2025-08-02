<?php
session_start();
include 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';
?>

<div id="schoolCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
    <div id="schoolCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/school1.jpg" class="d-block w-100 carousel-img" alt="Campus">
                <div class="carousel-caption caption-elevated d-none d-md-block">
                    <h2>Discover Upcoming Events at USIU</h2>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/school2.jpg" class="d-block w-100 carousel-img" alt="Library">
                <div class="carousel-caption caption-elevated d-none d-md-block">
                    <h2>Clubs and Activities for Every Interest</h2>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/school3.jpg" class="d-block w-100 carousel-img" alt="Event">
                <div class="carousel-caption caption-elevated d-none d-md-block">
                    <h2>Learn, Connect, and Grow Together</h2>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#schoolCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#schoolCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <div class="container my-5 p-4 border rounded bg-light shadow">
        <h2 class="text-primary mb-4">Submit Event</h2>
        <form id="eventForm" enctype="multipart/form-data">
            <div class="mb-3">
                <input type="text" class="form-control" name="title" placeholder="Event Title" required>
            </div>
            <div class="mb-3">
                <textarea class="form-control" name="description" placeholder="Event Description" rows="4"></textarea>
            </div>
            <div class="mb-3">
                <input type="date" class="form-control" name="event_date" required>
            </div>
            <div class="mb-3">
                <input type="file" class="form-control" name="image" accept="image/*">
                <div class="form-text">Optional. JPG/PNG/WebP ≤ 2MB.</div>
            </div>
            <button type="submit" class="btn btn-warning text-dark fw-bold">SUBMIT</button>
        </form>
        <div id="eventFormAlert" class="mt-3 d-none"></div>
    </div>
<?php endif; ?>

<div class="container my-5">
    <h2 class="text-primary mb-4">Upcoming Events</h2>
    <div class="row g-4">
        <?php
        $result = $conn->query("SELECT * FROM events ORDER BY event_date ASC");

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

        while ($row = $result->fetch_assoc()):
            $event_id = (int) $row['id'];
            $img_path = $row['image_path'];
            $img_src = (is_string($img_path) && $img_path !== '') ? $img_path : 'images/default_event.jpg';
            $bookmarked = isset($_SESSION['user_id']) && is_event_bookmarked($conn, $_SESSION['user_id'], $event_id);
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow h-100">
                    <div class="position-relative clickable-area">
                        <img src="<?= htmlspecialchars($img_src) ?>" class="card-img-top" alt="Event Image"
                            style="height:180px;object-fit:cover;">
                        <div class="card-body pb-2">
                            <h5 class="card-title mb-1"><?= htmlspecialchars($row['title']) ?></h5>
                            <p class="card-text mb-2"><strong>Date:</strong> <?= htmlspecialchars($row['event_date']) ?></p>
                            <p class="card-text text-truncate m-0"><?= htmlspecialchars($row['description']) ?></p>
                        </div>
                        <a href="#" class="stretched-link" data-bs-toggle="modal" data-bs-target="#detailsModal"
                            data-type="event" data-title="<?= htmlspecialchars($row['title']) ?>"
                            data-date="<?= htmlspecialchars($row['event_date']) ?>"
                            data-desc="<?= htmlspecialchars($row['description']) ?>"
                            data-image="<?= htmlspecialchars($img_src) ?>" data-event-id="<?= $event_id ?>"
                            data-is-admin="<?= isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? '1' : '0' ?>"
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

                    <?php if (isset($_SESSION["role"]) && $_SESSION['role'] === 'admin'): ?>
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
                            <?php
                            $cstmt = $conn->prepare("
                                SELECT c.comment, c.posted_at, u.username 
                                FROM comments c 
                                JOIN users u ON c.user_id = u.id 
                                WHERE c.event_id = ? 
                                ORDER BY c.posted_at DESC
                            ");
                            $cstmt->bind_param("i", $event_id);
                            $cstmt->execute();
                            $cstmt->bind_result($comment, $posted_at, $username);
                            $has_any = false;
                            while ($cstmt->fetch()):
                                $has_any = true;
                                ?>
                                <div class="border rounded p-2 mb-2 bg-white">
                                    <div class="mb-1">
                                        <strong><?= htmlspecialchars($username) ?></strong>
                                        <small class="text-muted"><?= htmlspecialchars($posted_at) ?></small>
                                    </div>
                                    <div><?= nl2br(htmlspecialchars($comment)) ?></div>
                                </div>
                            <?php endwhile; ?>
                            <?php if (!$has_any): ?>
                                <div class="text-muted small">No comments yet.</div>
                            <?php endif; ?>
                            <?php $cstmt->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="detailsModalImage" src="" alt="" class="img-fluid mb-3"
                    style="max-height:360px;object-fit:cover;width:100%;">
                <p class="mb-1"><strong id="detailsModalDateLabel">Date:</strong> <span id="detailsModalDate"></span>
                </p>
                <p id="detailsModalDesc" style="white-space:pre-line;"></p>
                <div id="detailsModalAlert" class="alert d-none" role="alert"></div>
                <div id="detailsModalActions" class="mt-3 d-flex gap-2 flex-wrap"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-bs-target="#detailsModal"]');
        if (!trigger) return;

        const title = trigger.dataset.title || '';
        const date = trigger.dataset.date || '';
        const desc = trigger.dataset.desc || '';
        const image = trigger.dataset.image || '';
        const eventId = trigger.dataset.eventId;
        const isAdmin = trigger.dataset.isAdmin === '1';
        const isBookmarked = trigger.dataset.bookmarked === 'true';

        document.getElementById('detailsModalTitle').textContent = title;
        document.getElementById('detailsModalDate').textContent = date;
        document.getElementById('detailsModalDesc').textContent = desc;

        const modalImg = document.getElementById('detailsModalImage');
        modalImg.src = image;
        modalImg.alt = title;

        const actionsContainer = document.getElementById('detailsModalActions');
        actionsContainer.innerHTML = '';

        const alertBox = document.getElementById('detailsModalAlert');
        alertBox.classList.add('d-none');

        if (eventId) {
            const bookmarkBtn = document.createElement('button');
            bookmarkBtn.className = 'btn btn-sm btn-warning';
            bookmarkBtn.textContent = isBookmarked ? 'Unbookmark' : 'Bookmark';
            bookmarkBtn.dataset.eventId = eventId;
            bookmarkBtn.dataset.action = isBookmarked ? 'remove' : 'add';
            bookmarkBtn.addEventListener('click', function () {
                const action = this.dataset.action;
                fetch('bookmark.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `event_id=${eventId}&action=${action}`
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const newAction = action === 'add' ? 'remove' : 'add';
                            this.textContent = newAction === 'add' ? 'Bookmark' : 'Unbookmark';
                            this.dataset.action = newAction;

                            const cardBtn = document.querySelector(`.bookmark-btn[data-event-id="${eventId}"]`);
                            if (cardBtn) {
                                cardBtn.textContent = this.textContent;
                                cardBtn.dataset.action = newAction;
                            }

                            alertBox.className = 'alert alert-success';
                            alertBox.textContent = data.message;
                            alertBox.classList.remove('d-none');
                        } else {
                            alertBox.className = 'alert alert-danger';
                            alertBox.textContent = data.message || 'Error occurred.';
                            alertBox.classList.remove('d-none');
                        }
                        setTimeout(() => alertBox.classList.add('d-none'), 3000);
                    });
            });
            actionsContainer.appendChild(bookmarkBtn);
        }

        if (isAdmin) {
            const editLink = document.createElement('a');
            editLink.className = 'btn btn-sm btn-outline-secondary';
            editLink.href = `edit_event.php?id=${eventId}`;
            editLink.textContent = 'Edit';
            actionsContainer.appendChild(editLink);

            const deleteForm = document.createElement('form');
            deleteForm.method = 'POST';
            deleteForm.action = 'delete_event.php';
            deleteForm.onsubmit = () => confirm('Delete this event?');
            deleteForm.innerHTML = `<input type="hidden" name="event_id" value="${eventId}"><button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>`;
            actionsContainer.appendChild(deleteForm);
        }
    });

    document.querySelectorAll('.bookmark-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const eventId = this.dataset.eventId;
            const action = this.dataset.action;
            fetch('bookmark.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `event_id=${eventId}&action=${action}`
            })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        const newAction = action === 'add' ? 'remove' : 'add';
                        this.dataset.action = newAction;
                        this.textContent = newAction === 'add' ? 'Bookmark' : 'Unbookmark';
                    }
                });
        });
    });

    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            const container = form.closest('.card').querySelector('.comment-list');
            const errorDiv = form.querySelector('.comment-error');

            fetch('comment.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        errorDiv.textContent = data.error;
                        errorDiv.classList.remove('d-none');
                        return;
                    }
                    errorDiv.classList.add('d-none');
                    const newComment = document.createElement('div');
                    newComment.className = 'border rounded p-2 mb-2 bg-white';
                    newComment.innerHTML = `<div class="mb-1"><strong>${data.username}</strong><small class="text-muted ms-2">${data.posted_at}</small></div><div>${data.comment}</div>`;
                    container.prepend(newComment);
                    form.reset();
                });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>