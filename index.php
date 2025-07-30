<?php
session_start();
include 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';
?>

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

<?php if (isset($_SESSION["user_id"])): ?>
    <div class="container my-5 p-4 border rounded bg-light shadow">
        <h2 class="text-primary mb-4">Submit Event</h2>
        <!-- AJAX form: NO action, uses fetch -->
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
        while ($row = $result->fetch_assoc()):
            $event_id = (int) $row['id'];
            $img_path = $row['image_path'];
            $img_src = (is_string($img_path) && $img_path !== '' && file_exists($img_path) && is_file($img_path))
                ? $img_path
                : 'images/default_event.jpg';
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
                            data-image="<?= htmlspecialchars($img_src) ?>"></a>
                    </div>

                    <?php if (isset($_SESSION["user_id"])): ?>
                        <div class="px-3 pb-3 pt-2 comment-interactive position-relative">
                            <form method="POST" action="comment.php" class="comment-form" data-event-id="<?= $event_id ?>">
                                <input type="hidden" name="event_id" value="<?= $event_id ?>">
                                <div class="mb-2">
                                    <textarea name="comment" class="form-control" rows="2" placeholder="Write a comment..."
                                        required></textarea>
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Comment</button>
                            </form>
                        </div>
                    <?php endif; ?>

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
                    ?>
                    <div class="card-footer bg-light">
                        <h6 class="mb-2">Comments:</h6>
                        <div class="comment-list" data-event-id="<?= $event_id ?>">
                            <?php while ($cstmt->fetch()):
                                $has_any = true; ?>
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
                        </div>
                    </div>
                    <?php $cstmt->close(); ?>

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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // AJAX: Submit Event with optional image
    const eventForm = document.getElementById('eventForm');
    if (eventForm) {
        eventForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const alertBox = document.getElementById('eventFormAlert');
            alertBox.className = 'mt-3 d-none';
            alertBox.textContent = '';

            const formData = new FormData(eventForm);

            try {
                const res = await fetch('submit.php', { method: 'POST', body: formData });
                const data = await res.json();

                if (!res.ok || data.error) {
                    alertBox.className = 'alert alert-danger mt-3';
                    alertBox.textContent = data.error || 'Failed to submit.';
                    return;
                }

                const grid = document.querySelector('.row.g-4');
                if (grid) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'col-md-6 col-lg-4';
                    wrapper.innerHTML = renderEventCard(data);
                    grid.prepend(wrapper);

                    const newForm = wrapper.querySelector('.comment-form');
                    if (newForm) {
                        newForm.addEventListener('click', ev => ev.stopPropagation());
                        newForm.querySelectorAll('textarea, input, button').forEach(el => {
                            el.addEventListener('click', ev => ev.stopPropagation());
                        });
                        wireAjaxCommentForm(newForm);
                    }
                }

                eventForm.reset();
                alertBox.className = 'alert alert-success mt-3';
                alertBox.textContent = 'Event created.';
            } catch (err) {
                alertBox.className = 'alert alert-danger mt-3';
                alertBox.textContent = 'Network error.';
            }
        });
    }

    // Build new event card HTML to match existing structure
    function renderEventCard(ev) {
        const img = ev.image_path_resolved || 'images/default_event.jpg';
        const id = ev.id;
        const title = escapeHtml(ev.title);
        const date = escapeHtml(ev.event_date);
        const desc = escapeHtml(ev.description || '');

        return `
    <div class="card shadow h-100">
        <div class="position-relative clickable-area">
        <img src="${escapeAttr(img)}" class="card-img-top" alt="Event Image" style="height:180px;object-fit:cover;">
        <div class="card-body pb-2">
            <h5 class="card-title mb-1">${title}</h5>
            <p class="card-text mb-2"><strong>Date:</strong> ${date}</p>
            <p class="card-text text-truncate m-0">${desc}</p>
        </div>
        <a href="#"
            class="stretched-link"
            data-bs-toggle="modal"
            data-bs-target="#detailsModal"
            data-type="event"
            data-title="${escapeAttr(ev.title)}"
            data-date="${escapeAttr(ev.event_date)}"
            data-desc="${escapeAttr(ev.description || '')}"
            data-image="${escapeAttr(img)}"></a>
        </div>

        <div class="px-3 pb-3 pt-2 comment-interactive position-relative">
        <form method="POST" action="comment.php" class="comment-form" data-event-id="${id}">
            <input type="hidden" name="event_id" value="${id}">
            <div class="mb-2">
            <textarea name="comment" class="form-control" rows="2" placeholder="Write a comment..." required></textarea>
            </div>
            <button type="submit" class="btn btn-sm btn-outline-primary">Comment</button>
        </form>
        </div>

        <div class="card-footer bg-light">
        <h6 class="mb-2">Comments:</h6>
        <div class="comment-list" data-event-id="${id}">
            <div class="text-muted small">No comments yet.</div>
        </div>
        </div>
    </div>`;
    }

    // Escape helpers
    function escapeHtml(s) { return (s ?? '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m])); }
    function escapeAttr(s) { return escapeHtml(s).replace(/"/g, '&quot;'); }

    // AJAX comments (existing + newly added)
    function wireAjaxCommentForm(form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const eventId = form.dataset.eventId;
            const formData = new FormData(form);

            try {
                const res = await fetch('comment.php', { method: 'POST', body: formData });
                const data = await res.json();

                if (data.error) { alert(data.error); return; }

                const box = document.createElement('div');
                box.className = 'border rounded p-2 mb-2 bg-white';
                box.innerHTML = `
        <div class="mb-1">
            <strong>${data.username}</strong>
            <small class="text-muted">${data.posted_at}</small>
            </div>
            <div>${data.comment}</div>
        `;

                const container = document.querySelector(`.comment-list[data-event-id='${eventId}']`);
                if (!container) return;

                const placeholder = container.querySelector('.text-muted.small');
                if (placeholder) placeholder.remove();

                container.prepend(box);
                form.reset();
            } catch (err) {
                alert('Failed to post comment.');
            }
        });
    }

    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('click', e => e.stopPropagation());
        form.querySelectorAll('textarea, input, button').forEach(el => {
            el.addEventListener('click', e => e.stopPropagation());
        });
        wireAjaxCommentForm(form);
    });

    // Modal population
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-bs-target="#detailsModal"]');
        if (!trigger) return;

        const title = trigger.getAttribute('data-title') || '';
        const date = trigger.getAttribute('data-date') || '';
        const desc = trigger.getAttribute('data-desc') || '';
        const image = trigger.getAttribute('data-image') || '';

        document.getElementById('detailsModalTitle').textContent = title;
        document.getElementById('detailsModalDate').textContent = date;
        document.getElementById('detailsModalDateLabel').style.display = date ? '' : 'none';
        document.getElementById('detailsModalDesc').textContent = desc;

        const modalImg = document.getElementById('detailsModalImage');
        modalImg.src = image || 'images/default_event.jpg';
        modalImg.alt = title;
        modalImg.onerror = function () { this.onerror = null; this.src = 'images/default_event.jpg'; };
    });
</script>

<?php include 'includes/footer.php'; ?>