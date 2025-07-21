<!-- login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Campus Events</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include 'components/navbar.php'; ?>

  <main class="auth-container">
    <h2>Login to Your Account</h2>
    <form id="login-form" class="auth-form">
      <input type="email" placeholder="Email" required />
      <input type="password" placeholder="Password" required />
      <button type="submit">Login</button>
      <p>Don't have an account? <a href="register.php">Register here</a></p>
    </form>
  </main>

</body>
</html>
