<?php
session_start();
include 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';

$q = "SELECT * FROM clubs ORDER BY name ASC";
$result = mysqli_query($conn, $q);
?>

<div id="clubsCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="images/school4.jpg" class="d-block w-100 carousel-img" alt="Club Gathering">
            <div class="carousel-caption caption-elevated d-none d-md-block">
                <h2>Join a Community That Shares Your Passion</h2>
            </div>
        </div>
        <div class="carousel-item">
            <img src="images/school5.jpg" class="d-block w-100 carousel-img" alt="Workshop">
            <div class="carousel-caption caption-elevated d-none d-md-block">
                <h2>Develop Skills Through Club Activities</h2>
            </div>
        </div>
        <div class="carousel-item">
            <img src="images/school6.jpg" class="d-block w-100 carousel-img" alt="Club Event">
            <div class="carousel-caption caption-elevated d-none d-md-block">
                <h2>Lead and Inspire in Student Organizations</h2>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#clubsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#clubsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>


<div class="container mt-5">
    <h1 class="mb-4 text-primary">University Clubs</h1>

    <div class="row g-4">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($club = mysqli_fetch_assoc($result)): ?>
                <?php
                $img_path = $club['image_url'] ?? '';
                $img_src = ($img_path !== '' && file_exists($img_path) && is_file($img_path))
                    ? $img_path
                    : 'images/default_club.jpg';
                ?>
                <div class="col-sm-6 col-lg-4">
                    <div class="card h-100 shadow">
                        <!-- Clickable area (image + body) -->
                        <div class="position-relative clickable-area">
                            <img src="<?= htmlspecialchars($img_src) ?>" class="card-img-top"
                                alt="<?= htmlspecialchars($club['name']) ?>" style="height:180px; object-fit:cover;">
                            <div class="card-body">
                                <h5 class="card-title mb-2"><?= htmlspecialchars($club['name']) ?></h5>
                                <p class="card-text text-truncate"><?= htmlspecialchars($club['description']) ?></p>
                            </div>

                            <!-- Stretched link covers only .clickable-area -->
                            <a href="#" class="stretched-link" data-bs-toggle="modal" data-bs-target="#detailsModal"
                                data-type="club" data-title="<?= htmlspecialchars($club['name']) ?>"
                                data-desc="<?= htmlspecialchars($club['description']) ?>"
                                data-image="<?= htmlspecialchars($img_src) ?>"
                                data-contact="<?= htmlspecialchars($club['contact_info'] ?? '') ?>"
                                data-founded="<?= htmlspecialchars($club['founded_year'] ?? '') ?>"
                                data-president="<?= htmlspecialchars($club['president_name'] ?? '') ?>"></a>
                        </div>

                        <!-- Non-clickable footer (keeps links usable if any) -->
                        <div class="card-footer bg-light">
                            <small class="text-muted"><?= htmlspecialchars($club['contact_info'] ?? '') ?></small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info mb-0">No clubs available.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Reusable details modal -->
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
                <div id="clubMeta" class="mb-2">
                    <p class="mb-1 d-none" id="clubPresident"><strong>President:</strong> <span></span></p>
                    <p class="mb-1 d-none" id="clubFounded"><strong>Founded:</strong> <span></span></p>
                    <p class="mb-1 d-none" id="clubContact"><strong>Contact:</strong> <span></span></p>
                </div>
                <p id="detailsModalDesc" style="white-space:pre-line;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Populate modal for club cards
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-bs-target="#detailsModal"]');
        if (!trigger) return;

        const title = trigger.getAttribute('data-title') || '';
        const desc = trigger.getAttribute('data-desc') || '';
        const image = trigger.getAttribute('data-image') || 'images/default_club.jpg';
        const contact = trigger.getAttribute('data-contact') || '';
        const founded = trigger.getAttribute('data-founded') || '';
        const president = trigger.getAttribute('data-president') || '';

        // Core fields
        document.getElementById('detailsModalTitle').textContent = title;
        document.getElementById('detailsModalDesc').textContent = desc;

        const modalImg = document.getElementById('detailsModalImage');
        modalImg.src = image;
        modalImg.alt = title;
        modalImg.onerror = function () { this.onerror = null; this.src = 'images/default_club.jpg'; };

        // Meta rows
        const prezRow = document.getElementById('clubPresident');
        const prezSpan = prezRow.querySelector('span');
        prezSpan.textContent = president;
        prezRow.classList.toggle('d-none', !president);

        const foundedRow = document.getElementById('clubFounded');
        const foundedSpan = foundedRow.querySelector('span');
        foundedSpan.textContent = founded;
        foundedRow.classList.toggle('d-none', !founded);

        const contactRow = document.getElementById('clubContact');
        const contactSpan = contactRow.querySelector('span');
        contactSpan.textContent = contact;
        contactRow.classList.toggle('d-none', !contact);
    });
</script>

<?php
require_once 'includes/footer.php';
?>