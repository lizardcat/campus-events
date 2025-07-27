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
            <div class="carousel-caption d-none d-md-block">
                <h2>Main Campus</h2>
                <h5>Explore academic excellence and innovation.</h5>
            </div>
        </div>
        <div class="carousel-item">
            <img src="images/school2.jpg" class="d-block w-100 carousel-img" alt="Library">
            <div class="carousel-caption d-none d-md-block">
                <h2>Library Hub</h2>
                <h5>Thousands of resources at your fingertips.</h5>
            </div>
        </div>
        <div class="carousel-item">
            <img src="images/school3.jpg" class="d-block w-100 carousel-img" alt="Event">
            <div class="carousel-caption d-none d-md-block">
                <h2>Student Life</h2>
                <h5>Clubs, events, and community engagement.</h5>
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
        <form method="POST" action="submit.php">
            <div class="mb-3">
                <input type="text" class="form-control" name="title" placeholder="Event Title" required>
            </div>
            <div class="mb-3">
                <textarea class="form-control" name="description" placeholder="Event Description" rows="4"></textarea>
            </div>
            <div class="mb-3">
                <input type="date" class="form-control" name="event_date" required>
            </div>
            <button type="submit" class="btn btn-warning text-dark fw-bold">SUBMIT</button>
        </form>
    </div>
<?php endif; ?>

<div class="container my-5">
    <h2 class="text-primary mb-4">Upcoming Events</h2>
    <div class="row g-4">
        <?php
        $result = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
        while ($row = $result->fetch_assoc()):
            $event_id = $row['id'];
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow h-100">
                    <?php
                    $img_path = $row['image_path'];
                    $img_src = (file_exists($img_path) && is_file($img_path)) ? $img_path : 'images/default_event.jpg';
                    ?>
                    <img src="<?= htmlspecialchars($img_src) ?>" class="card-img-top" alt="Event Image"
                        style="height: 180px; object-fit: cover;">

                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text"><strong>Date:</strong> <?= $row['event_date'] ?></p>
                        <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>

                        <?php if (isset($_SESSION["user_id"])): ?>
                            <form method="POST" action="comment.php">
                                <input type="hidden" name="event_id" value="<?= $event_id ?>">
                                <div class="mb-2">
                                    <textarea name="comment" class="form-control" rows="2" placeholder="Write a comment..."
                                        required></textarea>
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Comment</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <?php
                    $cstmt = $conn->prepare("SELECT COUNT(*) FROM comments WHERE event_id = ?");
                    $cstmt->bind_param("i", $event_id);
                    $cstmt->execute();
                    $cstmt->bind_result($comment_count);
                    $cstmt->fetch();
                    $cstmt->close();
                    if ($comment_count > 0):
                        ?>
                        <div class="card-footer bg-light">
                            <h6 class="mb-2">Comments:</h6>
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
                            while ($cstmt->fetch()):
                                ?>
                                <div class="border rounded p-2 mb-2 bg-white">
                                    <div class="mb-1">
                                        <strong><?= htmlspecialchars($username) ?></strong>
                                        <small class="text-muted"><?= $posted_at ?></small>
                                    </div>
                                    <div><?= nl2br(htmlspecialchars($comment)) ?></div>
                                </div>
                            <?php endwhile;
                            $cstmt->close(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>



<?php include 'includes/footer.php'; ?>