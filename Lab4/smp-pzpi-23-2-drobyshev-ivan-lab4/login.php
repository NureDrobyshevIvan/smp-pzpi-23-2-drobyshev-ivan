<?php

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $password === '') {
        $error = 'Введіть логін і пароль.';
    } else {
        require __DIR__ . '/data/productsDB.php';
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_time'] = date('Y-m-d H:i:s');
            header('Location: index.php?page=products');
            exit;
        } else {
            $error = 'Невірний логін або пароль.';
        }
    }
}
?>
<div class="main-content">
    <h2>Вхід</h2>
    <form method="post">
        <label>Логін:<br>
            <input type="text" name="username" required>
        </label><br><br>
        <label>Пароль:<br>
            <input type="password" name="password" required>
        </label><br><br>
        <button type="submit">Увійти</button>
    </form>
    <?php if ($error): ?>
        <div style="color:red; margin-top:10px;"> <?= htmlspecialchars($error) ?> </div>
    <?php endif; ?>
</div> 