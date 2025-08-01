<?php
include 'includes/head.php';
?>

<body class="bg-light">
    <header class="bg-dark text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <img src="images/usiu_logo.png" alt="USIU Logo" height="50" width="100">
                <h1 class="h4 m-0">USIU Campus Events</h1>
            </div>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a href="index.php" class="nav-link text-white">Home</a></li>
                    <li class="nav-item"><a href="clubs.php" class="nav-link text-white">Clubs</a></li>
                    <?php if (isset($_SESSION['username'])): ?>
                        <li class="nav-item"><a href="logout.php" class="nav-link text-warning">Logout
                                (<?= htmlspecialchars($_SESSION['username']) ?>)</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a href="login.php" class="nav-link text-white">Login</a></li>
                        <li class="nav-item"><a href="register.php" class="nav-link text-white">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>