<?php
if (!isLoggedIn()) {
    redirect('index.php?page=login');
}

// Fetch Transactions
$stmt = $pdo->prepare("
    SELECT t.*, 
    (SELECT COUNT(*) FROM transaction_items WHERE transaction_id = t.id) as item_count
    FROM transactions t 
    WHERE user_id = ? 
    ORDER BY transaction_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$transactions = $stmt->fetchAll();
?>

<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-bold text-slate-900">Riwayat Pembelian</h2>
            <p class="text-slate-500 mt-1">Daftar semua buku yang telah Anda beli.</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm text-sm text-slate-600">
            Total Transaksi: <span class="font-bold text-slate-900"><?= count($transactions) ?></span>
        </div>
    </div>

    <?php if (empty($transactions)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-16 text-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Belum Ada Riwayat</h3>
            <p class="text-slate-500 mb-8 max-w-sm mx-auto">Anda belum melakukan pembelian buku apapun. Yuk mulai koleksi
                buku favoritmu!</p>
            <a href="index.php"
                class="inline-block px-6 py-3 bg-primary text-white font-bold rounded-full hover:bg-amber-600 transition-colors">
                Jelajahi Katalog
            </a>
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($transactions as $t): ?>
                <div
                    class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Header -->
                    <div
                        class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Order ID #<?= $t['id'] ?>
                                </p>
                                <p class="text-sm font-semibold text-slate-800">
                                    <?= date('d F Y • H:i', strtotime($t['transaction_date'])) ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Pembayaran</p>
                            <p class="text-xl font-extrabold text-primary">🪙 <?= number_format($t['total_tokens']) ?></p>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="p-6">
                        <?php
                        $stmt_items = $pdo->prepare("
                                SELECT Ti.*, b.title, b.author, b.image 
                                FROM transaction_items Ti
                                JOIN books b ON Ti.book_id = b.id
                                WHERE Ti.transaction_id = ?
                            ");
                        $stmt_items->execute([$t['id']]);
                        $items = $stmt_items->fetchAll();
                        ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($items as $item): ?>
                                <div class="flex gap-4 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors">
                                    <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" alt="cover"
                                        class="w-16 h-24 object-cover rounded-md shadow-sm bg-slate-200">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-slate-900 text-sm line-clamp-2 mb-1">
                                            <?= htmlspecialchars($item['title']) ?></h4>
                                        <p class="text-xs text-slate-500 mb-2"><?= htmlspecialchars($item['author']) ?></p>
                                        <div class="flex justify-between items-center text-xs">
                                            <span
                                                class="bg-slate-100 px-2 py-1 rounded text-slate-600 font-medium">x<?= $item['quantity'] ?></span>
                                            <span class="font-bold text-slate-700">🪙
                                                <?= number_format($item['price_per_token']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>