<?php if (session_status() === PHP_SESSION_NONE) session_start();
?>
<header >
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <nav style="display: flex; justify-content: space-between; align-items: center; margin: 0 auto; height: 30px; padding: 0%;">
        <a href="index.php">
            <i class="fas fa-home"></i> Home
        </a> |
        <a href="index.php?page=products">
            <i class="fas fa-bars"></i> Products
        </a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            |
            <a href="index.php?page=basket">
                <i class="fas fa-shopping-cart"></i> Cart
            </a>
            |
            <a href="index.php?page=profile">
                <i class="fas fa-user"></i> Profile
            </a>
        <?php endif; ?>
        |
        <?php if (empty($_SESSION['user_id'])): ?>
            <a href="index.php?page=login"><i class="fas fa-sign-in-alt"></i> Login</a>
        <?php else: ?>
            <a href="index.php?page=logout"><i class="fas fa-sign-out-alt"></i> Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a>
        <?php endif; ?>
    </nav>
    <hr>
</header> 