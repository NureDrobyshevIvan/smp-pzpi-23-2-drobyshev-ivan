<?php
$dbFile = __DIR__ . '/products.db';

if (!file_exists($dbFile)) {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        price INTEGER NOT NULL
    )");

    $products = [
        ['name' => 'Молоко пастеризоване', 'price' => 12],
        ['name' => 'Сир білий', 'price' => 21],
        ['name' => 'Яйця курячі', 'price' => 7],
        ['name' => 'Кефір', 'price' => 15],
        ['name' => 'Сметана', 'price' => 18],
        ['name' => 'Масло вершкове', 'price' => 35],
        ['name' => 'Йогурт натуральний', 'price' => 14],
        ['name' => 'Сир твердий', 'price' => 45],
        ['name' => 'Ряжанка', 'price' => 13],
        ['name' => 'Вершки', 'price' => 22]
    ];

    $stmt = $pdo->prepare("INSERT INTO products (name, price) VALUES (?, ?)");
    foreach ($products as $p) {
        $stmt->execute([$p['name'], $p['price']]);
    }
} else {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}