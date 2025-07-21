<?php include 'includes/db.php'; ?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>Submit Event</h2>
    <form method="POST" action="submit.php">
        <input type="text" name="title" placeholder="Event Title" required><br>
        <textarea name="description" placeholder="Event Description"></textarea>
        <input type="date" name="event_date" required><br>
        <button type="submit">SUBMIT</button>
    </form>
</div>

<div class="container">
    <h2>Upcoming Events</h2>
    <ol>
        <?php
        $result = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
        while ($row = $result->fetch_assoc()):
            $event_id = $row['id'];
        ?>
            <li class="event-block">
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <p><strong>Date:</strong> <?= $row['event_date'] ?></p>
                <p><?= htmlspecialchars($row['description']) ?></p>

                <?php if (isset($_SESSION["user_id"])): ?>
                    <form method="POST" action="comment.php" class="comment-form">
                        <input type="hidden" name="event_id" value="<?= $event_id ?>">
                        <textarea name="comment" required placeholder="Write a comment..."></textarea>
                        <button type="submit">COMMENT</button>
                    </form>
                <?php endif; ?>

                <?php
                // Check if there are any comments for this event
                $cstmt = $conn->prepare("
                    SELECT COUNT(*) 
                    FROM comments 
                    WHERE event_id = ?
                ");
                $cstmt->bind_param("i", $event_id);
                $cstmt->execute();
                $cstmt->bind_result($comment_count);
                $cstmt->fetch();
                $cstmt->close();

                // Only display the comment section if there are comments
                if ($comment_count > 0):
                ?>
                    <div class="comment-section">
                        <h4>Comments:</h4>
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
                            <div class="comment-box">
                                <div class="comment-meta">
                                    <strong><?= htmlspecialchars($username) ?></strong>
                                    <span><?= $posted_at ?></span>
                                </div>
                                <div class="comment-body">
                                    <?= nl2br(htmlspecialchars($comment)) ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        <?php $cstmt->close(); ?>
                    </div>
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
    </ol>
</div>


<?php include 'includes/footer.php'; ?>

</body>
</html>