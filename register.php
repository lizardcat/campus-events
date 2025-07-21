<!-- register.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | Campus Events</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php include 'components/navbar.php'; ?>

  <main class="auth-container">
    <h2>Create an Account</h2>
    <form id="register-form" class="auth-form">
      <input type="text" placeholder="Full Name" required />
      <input type="email" placeholder="Email" required />
      <input type="password" placeholder="Password" required />
      <input type="password" placeholder="Confirm Password" required />
      <button type="submit">Register</button>
      <p>Already have an account? <a href="login.php">Login here</a></p>
    </form>
  </main>

</body>
</html>
