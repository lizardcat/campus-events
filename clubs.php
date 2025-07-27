<?php
session_start();
include 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';

$query = "SELECT * FROM clubs";
$result = mysqli_query($conn, $query);

?>

<div class="container mt-5">
    <h1 class="mb-4">University Clubs</h1>
    <div class="row">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($club = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php
                        $img_path = $club['image_url'];
                        $img_src = (file_exists($img_path) && is_file($img_path)) ? $img_path : 'images/default_club.jpg';
                        ?>
                        <img src="<?= htmlspecialchars($img_src) ?>" class="card-img-top"
                            alt="<?= htmlspecialchars($club['name']) ?>" style="height: 180px; object-fit: cover;">


                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($club['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($club['description']) ?></p>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted"><?= htmlspecialchars($club['contact_info']) ?></small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p>No clubs available.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>