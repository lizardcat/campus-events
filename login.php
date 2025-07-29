<?php
session_start();
include 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['username']; // can be username or email
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $stmt->bind_result($id, $username, $hashed);
    if ($stmt->fetch() && password_verify($password, $hashed)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}

if (isset($_GET['registered'])) {
    $success = 'Registration successful. Please log in.';
}
?>

<div class="container my-5" style="max-width: 500px;">
    <div class="card shadow">
        <div class="card-body">
            <h2 class="mb-3 text-center text-primary">Login</h2>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username or Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-warning w-100 fw-bold text-dark">Login</button>
            </form>
            <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>