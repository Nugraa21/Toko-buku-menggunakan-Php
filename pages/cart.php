<?php
if (!isLoggedIn()) {
    redirect('index.php?page=login');
}

$user_id = $_SESSION['user_id'];

// Get cart items with book details
$stmt = $pdo->prepare("
    SELECT b.*, c.quantity 
    FROM books b 
    JOIN (
        SELECT 1 as dummy 
    ) d
    LEFT JOIN (
        SELECT * FROM transaction_items WHERE 0=1
    ) t ON 1=1
    WHERE b.id IN (" . (empty($_SESSION['cart']) ? 0 : implode(',', array_keys($_SESSION['cart']))) . ")
");

// Since session cart is key-value [id => qty], we construct the array manually because SQL query handling for session array is tricky directly
$cartItems = [];
$totalPrice = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM books WHERE id IN ($ids)");
    $books = $stmt->fetchAll();

    foreach ($books as $book) {
        $qty = $_SESSION['cart'][$book['id']];
        $book['quantity'] = $qty;
        $book['subtotal'] = $book['price'] * $qty;
        $totalPrice += $book['subtotal'];
        $cartItems[] = $book;
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold text-slate-800 mb-8">Keranjang Belanja</h2>

    <?php if (empty($cartItems)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
            <div class="text-6xl mb-4">🛒</div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Keranjang Kosong</h3>
            <p class="text-slate-500 mb-8">Anda belum menambahkan buku apapun.</p>
            <a href="index.php"
                class="px-6 py-3 bg-primary text-white font-bold rounded-full hover:bg-amber-600 transition-colors">
                Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                <?php foreach ($cartItems as $item): ?>
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex gap-4 transition-transform hover:translate-x-1">
                        <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" alt="cover"
                            class="w-20 h-28 object-cover rounded-lg shadow-sm bg-slate-100">

                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="text-lg font-bold text-slate-800 line-clamp-1">
                                    <?= htmlspecialchars($item['title']) ?></h4>
                                <p class="text-sm text-slate-500"><?= htmlspecialchars($item['author']) ?></p>
                            </div>

                            <div class="flex items-center justify-between mt-4">
                                <div class="font-bold text-primary">
                                    🪙 <?= number_format($item['price']) ?> <span class="text-xs text-slate-400 font-normal">/
                                        unit</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <form action="index.php?page=cart_action" method="POST" class="flex items-center">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="book_id" value="<?= $item['id'] ?>">
                                        <input type="number" name="qty" value="<?= $item['quantity'] ?>" min="1"
                                            class="w-12 text-center border border-slate-200 rounded-lg text-sm focus:border-primary outline-none"
                                            onchange="this.form.submit()">
                                    </form>
                                    <form action="index.php?page=cart_action" method="POST">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="book_id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="text-red-400 hover:text-red-600 p-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Ringkasan</h3>

                    <div class="space-y-2 mb-6 text-sm text-slate-600">
                        <div class="flex justify-between">
                            <span>Total Item</span>
                            <span><?= array_sum(array_column($cartItems, 'quantity')) ?> buku</span>
                        </div>
                        <div class="flex justify-between font-bold text-slate-900 pt-4 border-t border-slate-100">
                            <span>Total Harga</span>
                            <span class="text-primary text-xl">🪙 <?= number_format($totalPrice) ?></span>
                        </div>

                        <!-- Balance Check -->
                        <?php
                        $userBalance = getUserTokenBalance($pdo, $_SESSION['user_id']);
                        $isEnough = $userBalance >= $totalPrice;
                        ?>
                        <div class="flex justify-between items-center text-xs mt-2 bg-slate-50 p-2 rounded">
                            <span>Saldo Anda:</span>
                            <span class="<?= $isEnough ? 'text-green-600' : 'text-red-600' ?> font-bold">
                                🪙 <?= number_format($userBalance) ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($isEnough): ?>
                        <a href="index.php?page=checkout"
                            class="block w-full py-3 bg-slate-900 hover:bg-slate-800 text-white text-center font-bold rounded-xl shadow-lg transition-transform hover:-translate-y-1">
                            Checkout Sekarang
                        </a>
                    <?php else: ?>
                        <div class="space-y-3">
                            <button disabled
                                class="block w-full py-3 bg-slate-200 text-slate-400 font-bold rounded-xl cursor-not-allowed">
                                Saldo Tidak Cukup
                            </button>
                            <a href="index.php?page=topup"
                                class="block w-full py-2 border-2 border-primary text-primary text-center font-bold rounded-xl hover:bg-amber-50">
                                Isi Ulang Token
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>