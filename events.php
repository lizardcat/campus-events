<?php
session_start();
include 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';

$query = "SELECT * FROM events";
$result = mysqli_query($conn, $query);
?>

<div class="container mt-5">
    <h1 class="mb-4">University Events</h1>
    <div class="row">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($event = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php
                        $img_src = (isset($event['image_path']) && !empty($event['image_path'])) ? htmlspecialchars($event['image_path']) : 'images/default_event.jpg';
                        ?>
                        <img src="<?= htmlspecialchars($img_src) ?>" class="card-img-top"
                            alt="<?= htmlspecialchars($event['title']) ?>" style="height: 180px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted"><?= htmlspecialchars($event['event_date']) ?></small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p>No events available.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>