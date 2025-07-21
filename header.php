<?php session_start(); ?>

<header>
    <h1>USIU Campus Events</h1>
    <nav>
        <a href="index.php">Home</a> | 
        <?php if (isset($_SESSION['username'])): ?>
            <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a>
        <?php else: ?>
            <a href="login.php">Login</a> | 
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>