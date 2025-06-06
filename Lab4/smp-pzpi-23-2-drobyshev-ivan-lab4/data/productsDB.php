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

    $pdo->exec("CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        first_name TEXT,
        last_name TEXT,
        birth_date TEXT,
        description TEXT,
        photo TEXT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL
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

    $stmtUser = $pdo->prepare("INSERT INTO users (first_name, last_name, birth_date, description, photo, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmtUser->execute([
        'test',
        'test',
        '2000-01-01',
        'test',
        '',
        'test',
        password_hash('test1', PASSWORD_DEFAULT)
    ]);
} else {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}