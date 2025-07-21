<!-- dashboard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Dashboard | Campus Events</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include 'components/navbar.php'; ?>

  <main class="dashboard-container">
    <h2>Welcome Back 👋</h2>
    <p>Here are your registered events:</p>

    <section id="user-events" class="event-grid">
      <!-- Event cards injected dynamically -->
    </section>
  </main>

  <script src="js/dashboard.js"></script>
</body>
</html>
