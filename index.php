<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Campus Events | USIU</title>
  <link rel="stylesheet" href="styles.css">
</head>~
<body>

  <?php include 'components/navbar.php'; ?>

  <header class="hero">
    <h1>Upcoming Campus Events</h1>
  </header>

  <main>
    <section id="events-container" class="event-grid">
      <!-- Dynamic Event Cards will be injected here -->
    </section>
  </main>

  <?php include 'components/footer.php'; ?>

  <script src="js/main.js"></script>
</body>
</html>
