<?php
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Stats
$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'books' => $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn(),
    'revenue' => $pdo->query("SELECT SUM(total_tokens) FROM transactions")->fetchColumn() ?: 0,
    'pending' => $pdo->query("SELECT COUNT(*) FROM topups WHERE status = 'pending'")->fetchColumn()
];

// Recent Topups
$topups = $pdo->query("
    SELECT t.*, u.name, u.email 
    FROM topups t JOIN users u ON t.user_id = u.id 
    WHERE t.status = 'pending' 
    ORDER BY created_at ASC
")->fetchAll();
?>

<div class="mb-10">
    <h2 class="text-4xl font-extrabold text-slate-900 mb-2">Admin Dashboard</h2>
    <p class="text-slate-500">Pusat kendali aplikasi BookStore.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">👥</div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total User</p>
            <p class="text-2xl font-extrabold text-slate-800"><?= number_format($stats['users']) ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">📚</div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Katalog Buku</p>
            <p class="text-2xl font-extrabold text-slate-800"><?= number_format($stats['books']) ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl">💰</div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Pendapatan</p>
            <p class="text-2xl font-extrabold text-slate-800">🪙 <?= number_format($stats['revenue']) ?></p>
        </div>
    </div>
    <!-- Pending Card -->
    <a href="#pending-list"
        class="bg-white p-6 rounded-2xl shadow-sm border border-rose-100 flex items-center gap-4 hover:shadow-md transition-all cursor-pointer ring-2 ring-transparent hover:ring-rose-500/20">
        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">🔔</div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Pending Topup</p>
            <p class="text-2xl font-extrabold text-rose-600"><?= number_format($stats['pending']) ?></p>
        </div>
    </a>
</div>

<!-- Main Sections Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Management Shortcuts -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Quick Actions -->
        <div>
            <h3 class="text-xl font-bold text-slate-800 mb-4">Manajemen Data</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="index.php?page=admin_users"
                    class="group bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-primary/50 transition-all hover:shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xl group-hover:bg-primary group-hover:text-white transition-colors">
                            👤</div>
                        <span class="text-slate-300 group-hover:text-primary transition-colors">&rarr;</span>
                    </div>
                    <h4 class="text-lg font-bold text-slate-800 mb-1">Users & Saldo</h4>
                    <p class="text-sm text-slate-500">Edit data user, reset password, dan atur saldo token manual.</p>
                </a>

                <a href="index.php?page=admin_books"
                    class="group bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-primary/50 transition-all hover:shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xl group-hover:bg-primary group-hover:text-white transition-colors">
                            📘</div>
                        <span class="text-slate-300 group-hover:text-primary transition-colors">&rarr;</span>
                    </div>
                    <h4 class="text-lg font-bold text-slate-800 mb-1">Katalog Buku</h4>
                    <p class="text-sm text-slate-500">Tambah buku baru, upload cover, update stok dan harga.</p>
                </a>

                <a href="index.php?page=admin_transactions"
                    class="group bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-primary/50 transition-all hover:shadow-md sm:col-span-2">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xl group-hover:bg-primary group-hover:text-white transition-colors">
                            📈</div>
                        <span class="text-slate-300 group-hover:text-primary transition-colors">&rarr;</span>
                    </div>
                    <h4 class="text-lg font-bold text-slate-800 mb-1">Riwayat Penjualan</h4>
                    <p class="text-sm text-slate-500">Lihat semua transaksi pembelian user dan statistik buku terlaris.
                    </p>
                </a>
            </div>
        </div>

        <!-- Latest Transactions (Placeholder) -->
        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 border-dashed">
            <h3 class="text-slate-400 text-sm font-bold uppercase tracking-wide text-center">Area Pengecekan Transaksi
                (Coming Soon)</h3>
        </div>
    </div>

    <!-- Sidebar: Pending Topups -->
    <div id="pending-list" class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 sticky top-24">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-slate-800">Permintaan Top Up</h3>
                <span
                    class="bg-rose-100 text-rose-700 text-xs font-bold px-2 py-1 rounded-full"><?= count($topups) ?></span>
            </div>

            <?php if (empty($topups)): ?>
                <div class="text-center py-8 text-slate-400 text-sm">
                    <p>Semua permintaan selesai!</p>
                    <p class="text-4xl mt-2">✨</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($topups as $t): ?>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($t['name']) ?></p>
                                    <p class="text-xs text-slate-500 mb-1"><?= htmlspecialchars($t['email']) ?></p>
                                </div>
                                <span class="font-extrabold text-primary">🪙 <?= number_format($t['amount']) ?></span>
                            </div>
                            <div class="flex gap-2 mt-3">
                                <form method="POST" action="index.php?page=admin_action" class="flex-1">
                                    <input type="hidden" name="action" value="approve_topup">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <button type="submit"
                                        class="w-full py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs font-bold transition-colors">Terima</button>
                                </form>
                                <form method="POST" action="index.php?page=admin_action" class="flex-1">
                                    <input type="hidden" name="action" value="reject_topup">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <button type="submit"
                                        class="w-full py-1.5 bg-slate-200 hover:bg-red-500 hover:text-white text-slate-600 rounded-lg text-xs font-bold transition-colors">Tolak</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>