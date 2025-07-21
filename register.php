<?php 
include 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();

    header("Location: login.php?registered=1");
    exit;
}
?> 

<div class="container">
    <h1>REGISTER</h1>
    <p>Already have an account? <a href="login.php">Login!</a></p>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="password" name="confirmPass" placeholder="Confirm Password" required><br>
        <button type="submit" name="submit">REGISTER</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>