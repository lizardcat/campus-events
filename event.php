<!-- event.php -->
<?php
  $eventId = $_GET['id'] ?? null; // e.g., event.php?id=1
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event Details</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include 'components/navbar.php'; ?>

  <main class="event-detail-container">
    <section id="event-detail">
      <h2>Loading event details...</h2>
    </section>

    <section id="comments-section">
      <h3>Comments</h3>
      <ul id="comments-list">
        <!-- Comment items will be injected here -->
      </ul>
      <form id="comment-form">
        <input type="text" id="comment-input" placeholder="Leave a comment..." required />
        <button type="submit">Post</button>
      </form>
    </section>
  </main>

  <script>
    const eventId = "<?php echo $eventId; ?>";
  </script>
  <script src="js/event.js"></script>
</body>
</html>
