<?php
session_start();
include 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $confirm = $_POST['confirmPass'];

    if ($pass !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        $role = 'user';
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashed, $role);

        if ($stmt->execute()) {
            // Send welcome email (local dev with Papercut SMTP)
            $to = $email; // Can be a fake email for local dev
            $subject = "Welcome to USIU Campus Events";
            $message = "Hello $username,\n\n" .
                "Thank you for registering at USIU Campus Events.\n" .
                "You can now log in and start exploring events.\n\n" .
                "Regards,\nUSIU Campus Events Team";
            $headers = "From: no-reply@localhost\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();

            // Send email
            mail($to, $subject, $message, $headers);

            header("Location: login.php?registered=1");
            exit;
        } else {
            $error = "Error registering user: " . $stmt->error;
        }
    }
}
?>

<div class="container my-5" style="max-width: 500px;">
    <div class="card shadow">
        <div class="card-body">
            <h2 class="mb-3 text-center text-primary">Register</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="confirmPass" class="form-control" placeholder="Confirm Password"
                        required>
                </div>
                <button type="submit" name="submit" class="btn btn-warning w-100 fw-bold text-dark">Register</button>
            </form>
            <p class="mt-3 text-center">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>