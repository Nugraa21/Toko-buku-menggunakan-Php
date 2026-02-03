<?php
if (!isLoggedIn()) {
    // Optional: allow guest cart, but force login at checkout? 
    // For now, let's allow guest cart viewing.
}

$cartItems = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    // Safe because keys are integers from DB, but let's sanitize strictly if real app.
    // For this simple logic:
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($_SESSION['cart']));
    $books = $stmt->fetchAll();

    foreach ($books as $book) {
        $qty = $_SESSION['cart'][$book['id']];
        $subtotal = $book['price'] * $qty;
        $total += $subtotal;
        $cartItems[] = [
            'book' => $book,
            'qty' => $qty,
            'subtotal' => $subtotal
        ];
    }
}
?>

<h2 style="margin-bottom: 2rem;">Keranjang Belanja</h2>

<?php if (empty($cartItems)): ?>
    <div class="glass" style="padding: 2rem; text-align: center;">
        <p>Keranjang Anda kosong.</p>
        <a href="index.php" class="btn btn-primary" style="margin-top: 1rem; display: inline-block; width: auto;">Belanja
            Sekarang</a>
    </div>
<?php else: ?>
    <div class="glass" style="padding: 2rem; overflow-x: auto;">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td>
                            <div class="cart-item-info">
                                <img src="assets/images/<?= $item['book']['image'] ?>" alt="" class="cart-item-img">
                                <div>
                                    <strong>
                                        <?= htmlspecialchars($item['book']['title']) ?>
                                    </strong><br>
                                    <small>
                                        <?= htmlspecialchars($item['book']['author']) ?>
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?= formatPrice($item['book']['price']) ?>
                        </td>
                        <td>
                            <!-- Minimal change qty form -->
                            <form action="index.php?page=cart_action" method="POST" style="display: inline-flex; gap: 5px;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="book_id" value="<?= $item['book']['id'] ?>">
                                <input type="number" name="qty" value="<?= $item['qty'] ?>" min="1"
                                    style="width: 60px; padding: 5px; border-radius: 4px; border: 1px solid #ccc;">
                                <button type="submit" class="btn btn-primary" style="padding: 5px 10px;">Update</button>
                            </form>
                        </td>
                        <td>
                            <?= formatPrice($item['subtotal']) ?>
                        </td>
                        <td>
                            <form action="index.php?page=cart_action" method="POST"
                                onsubmit="return confirmAction('Hapus item ini?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="book_id" value="<?= $item['book']['id'] ?>">
                                <button type="submit"
                                    style="background: none; border: none; color: #ef4444; cursor: pointer;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="text-align: right; margin-top: 2rem;">
            <h3>Total: <span style="color: var(--primary);">
                    <?= formatPrice($total) ?>
                </span></h3>
            <br>
            <?php if (isLoggedIn()): ?>
                <form action="index.php?page=checkout" method="POST">
                    <button type="submit" class="btn btn-primary" style="width: 200px;">Checkout</button>
                </form>
            <?php else: ?>
                <a href="index.php?page=login" class="btn btn-primary" style="width: auto; display: inline-block;">Login untuk
                    Checkout</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>