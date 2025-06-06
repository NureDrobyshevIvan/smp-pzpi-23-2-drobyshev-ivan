<?php
session_start();

include __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/functions.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    case 'home':
        require_once __DIR__ . '/home.php';
        break;
    case 'products':
        require_once __DIR__ . '/products.php';
        break;
    case 'basket':
        require_once __DIR__ . '/basket.php';
        break;
    case 'login':
        require_once __DIR__ . '/login.php';
        break;
    case 'profile':
        require_once __DIR__ . '/profile.php';
        break;
    case 'logout':
        logoutUser();
        header('Location: index.php?page=login');
        exit;
    default:
        require_once __DIR__ . '/404.php';
        break;
}

include __DIR__ . '/includes/footer.php';
?> 