<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<footer>
<link rel="stylesheet" href="style.css">
    <div class="footer-links">
        <a href="index.php">Home</a>
        <span class="footer-separator">|</span>
        <a href="index.php?page=products">Products</a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <span class="footer-separator">|</span>
            <a href="index.php?page=basket">Cart</a>
        <?php endif; ?>
        <span class="footer-separator">|</span>
        <a href="about.php">About Us</a>
        <span class="footer-separator">|</span>
        <?php if (empty($_SESSION['user_id'])): ?>
            <a href="index.php?page=login">Login</a>
        <?php else: ?>
            <a href="index.php?page=logout">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a>
        <?php endif; ?>
    </div>
</footer> 