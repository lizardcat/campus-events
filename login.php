<?php 

include 'db.php';
include 'head.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $hashed);
    if ($stmt->fetch() && password_verify($password, $hashed)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        echo "Invalid credentials!";
    }
}

if (isset($_GET['registered'])) {
    echo '<div class="message">Registration successful. Please log in.</div>';
}

?>

<div class="container">
    <h1>LOGIN</h1>
    <p>Don't have an account? <a href="register.php">Register!</a></p>

    <form method="post">
        <input type="text" name="username" placeholder="Username"><br>
        <input type="password" name="password" placeholder="Password"><br>
        <button type="submit">LOGIN</button>
    </form>
</div>

<?php include 'footer.php'; ?>

</body>
</html>