<?php
include __DIR__ . '/includes/functions.php';
$products = getProducts();
$cart = getCart();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove'])) {
    removeFromCart((int)$_POST['remove']);
    header('Location: basket.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    clearCart();
    $paid = true;
}

include __DIR__ . '/includes/header.php';
?>
<div class="main-content">
    <h2>Кошик</h2>
    <?php if (!empty(
        $paid ?? false
    )): ?>
        <div style="color: green; font-weight: bold;">Оплата успішна! Дякуємо за покупку.</div>
        <p><a href="index.php">Повернутися до магазину</a></p>
    <?php elseif (empty($cart)): ?>
        <p>Кошик порожній. <a href="products.php">Перейти до покупок</a></p>
    <?php else: ?>
        <table border="1" cellpadding="5">
            <tr>
                <th>ID</th><th>Назва</th><th>Ціна</th><th>Кількість</th><th>Сума</th><th></th>
            </tr>
            <?php $total = 0; foreach ($cart as $item):
                $product = findProduct($item['id'], $products);
                if (!$product) continue;
                $sum = $product['price'] * $item['count'];
                $total += $sum;
            ?>
            <tr>
                <td><?= $product['id'] ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td>$<?= $product['price'] ?></td>
                <td><?= $item['count'] ?></td>
                <td>$<?= $sum ?></td>
                <td>
                    <form method="post" name="remove" style="display: inline;">
                        <input type="hidden" name="remove" value="<?= $product['id'] ?>">
                        <button type="submit" class="remove-button">Видалити</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4"><b>Разом до сплати:</b></td>
                <td colspan="2"><b>$<?= $total ?></b></td>
            </tr>
        </table>
        <form method="post" name="pay">
            <div class="button-center">
            <button name="pay">Оплатити</button>
        </div>
        </form>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?> 