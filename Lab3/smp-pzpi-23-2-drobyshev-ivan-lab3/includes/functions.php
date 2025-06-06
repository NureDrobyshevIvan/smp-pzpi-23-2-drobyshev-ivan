<?php
session_start();

function getProducts() {
    require_once __DIR__ . '/../data/productsDB.php';
    $products = [];
    $stmt = $pdo->query("SELECT id, name, price FROM products");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $products[$row['id']] = $row;
    }
    return $products;
}

function getCart() {
    return $_SESSION['cart'] ?? [];
}

function addToCart($id, $count) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $id) {
            $item['count'] += $count;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['cart'][] = ['id' => $id, 'count' => $count];
    }
}

function removeFromCart($id) {
    if (!isset($_SESSION['cart'])) return;
    
    $newCart = [];
    foreach ($_SESSION['cart'] as $item) {
        if ($item['id'] != $id) {
            $newCart[] = $item;
        }
    }
    $_SESSION['cart'] = $newCart;
}

function clearCart() {
    unset($_SESSION['cart']);
}

function validateCount($count) {
    return is_numeric($count) && $count > 0 && $count <= 100;
} 

function findProduct($id, $products) {
    foreach ($products as $p) {
        if ($p['id'] == $id) return $p;
    }
    return null;
}
