<?php
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Fetch all completed transactions (sales)
$stmt = $pdo->query("
    SELECT t.*, u.name as user_name, u.email as user_email,
    (SELECT GROUP_CONCAT(b.title SEPARATOR ', ') 
     FROM transaction_items ti 
     JOIN books b ON ti.book_id = b.id 
     WHERE ti.transaction_id = t.id) as book_titles
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    WHERE t.status = 'completed'
    ORDER BY t.transaction_date DESC
");
$sales = $stmt->fetchAll();

// Top Selling Books Calculation
$stmt_top = $pdo->query("
    SELECT b.title, SUM(ti.quantity) as total_sold, SUM(ti.quantity * ti.price_per_token) as revenue
    FROM transaction_items ti
    JOIN books b ON ti.book_id = b.id
    GROUP BY b.id
    ORDER BY total_sold DESC
    LIMIT 5
");
$top_books = $stmt_top->fetchAll();
?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-3xl font-bold text-slate-800">Riwayat Penjualan</h2>
        <p class="text-slate-500">Pantau transaksi dan performa penjualan buku.</p>
    </div>
    <a href="index.php?page=admin" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 font-medium transition-colors">
        &larr; Dashboard
    </a>
</div>

<!-- Top Books Stats -->
<div class="mb-10">
    <h3 class="text-lg font-bold text-slate-800 mb-4">📚 Buku Terlaris</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($top_books as $idx => $tb): ?>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 text-9xl font-black text-slate-50 opacity-20 pointer-events-none">#<?= $idx+1 ?></div>
                <div class="w-12 h-12 rounded-xl <?= $idx === 0 ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-600' ?> flex items-center justify-center font-bold text-lg relative z-10">
                    <?= $idx + 1 ?>
                </div>
                <div class="relative z-10">
                    <h4 class="font-bold text-slate-800 line-clamp-1" title="<?= $tb['title'] ?>"><?= htmlspecialchars($tb['title']) ?></h4>
                    <p class="text-sm text-slate-500">Terjual: <strong class="text-slate-800"><?= $tb['total_sold'] ?></strong> unit</p>
                    <p class="text-xs text-primary font-bold">Rev: 🪙 <?= number_format($tb['revenue']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Transactions Table -->
<div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-slate-800">Log Transaksi Terbaru</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500">
                    <th class="p-4 font-bold">ID</th>
                    <th class="p-4 font-bold">Pembeli</th>
                    <th class="p-4 font-bold">Buku yang Dibeli</th>
                    <th class="p-4 font-bold">Total Token</th>
                    <th class="p-4 font-bold">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($sales)): ?>
                    <tr><td colspan="5" class="p-8 text-center text-slate-500">Belum ada transaksi penjualan.</td></tr>
                <?php else: ?>
                    <?php foreach ($sales as $s): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 text-slate-400 font-mono text-xs">#<?= $s['id'] ?></td>
                            <td class="p-4">
                                <p class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($s['user_name']) ?></p>
                                <p class="text-xs text-slate-500"><?= htmlspecialchars($s['user_email']) ?></p>
                            </td>
                            <td class="p-4">
                                <p class="text-sm text-slate-700 line-clamp-2" title="<?= htmlspecialchars($s['book_titles']) ?>">
                                    <?= htmlspecialchars($s['book_titles']) ?>
                                </p>
                            </td>
                            <td class="p-4 font-bold text-primary">
                                🪙 <?= number_format($s['total_tokens']) ?>
                            </td>
                            <td class="p-4 text-xs text-slate-500">
                                <?= date('d M Y • H:i', strtotime($s['transaction_date'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
