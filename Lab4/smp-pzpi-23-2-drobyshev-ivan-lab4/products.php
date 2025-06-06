<?php
requireAuth();
$products = getProducts();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($products as $product) {
        $count = $_POST['count_' . $product['id']] ?? 0;
        if ($count > 0) {
            if (!validateCount($count)) {
                $error = 'Перевірте будь ласка введені дані. Кількість товару не може бути більшою за 100';
                break;
            }
            addToCart($product['id'], (int)$count);
        }
    }
    if (!$error) {
        header('Location: index.php?page=basket');
        exit;
    }
}
?>
<div class="main-content">
    <h2>Products</h2>
    <form method="POST" action="">
        <table>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><b><?= htmlspecialchars($product['name']) ?></b></td>
                <td><input type="number" name="count_<?= $product['id'] ?>" value="0" min="0" max="100"></td>
                <td>$<?= $product['price'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <div class="button-center">
            <button type="submit">Додати в кошик</button>
        </div>
    </form>
    <?php if ($error): ?>
        <div style="color:red;"> <?= $error ?> </div>
    <?php endif; ?>
</div>