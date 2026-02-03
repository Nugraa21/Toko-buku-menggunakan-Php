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
    <!-- Users -->
    <div
        class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Total User</p>
            <p class="text-2xl font-extrabold text-slate-800"><?= number_format($stats['users']) ?></p>
        </div>
    </div>

    <!-- Books -->
    <div
        class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Katalog Buku</p>
            <p class="text-2xl font-extrabold text-slate-800"><?= number_format($stats['books']) ?></p>
        </div>
    </div>

    <!-- Revenue -->
    <div
        class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Pendapatan</p>
            <p class="text-xl font-extrabold text-slate-800 flex items-center gap-1">
                <span class="text-green-500">🪙</span>
                <?= number_format($stats['revenue']) ?>
            </p>
        </div>
    </div>

    <!-- Pending (Clickable) -->
    <a href="#pending-list"
        class="bg-white p-6 rounded-3xl shadow-sm border border-rose-100 flex items-center gap-4 hover:shadow-md transition-all cursor-pointer ring-2 ring-transparent hover:ring-rose-500/20 group">
        <div
            class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Pending Topup</p>
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
            <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Manajemen Data
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="index.php?page=admin_users"
                    class="group bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-primary/50 transition-all hover:shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl group-hover:bg-primary group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <span class="text-slate-300 group-hover:text-primary transition-colors">&rarr;</span>
                    </div>
                    <h4 class="text-lg font-bold text-slate-800 mb-1">Users & Saldo</h4>
                    <p class="text-sm text-slate-500">Edit data user, reset password, dan atur saldo token manual.</p>
                </a>

                <a href="index.php?page=admin_books"
                    class="group bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-primary/50 transition-all hover:shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl group-hover:bg-primary group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="text-slate-300 group-hover:text-primary transition-colors">&rarr;</span>
                    </div>
                    <h4 class="text-lg font-bold text-slate-800 mb-1">Katalog Buku</h4>
                    <p class="text-sm text-slate-500">Tambah buku baru, upload cover, update stok dan harga.</p>
                </a>

                <a href="index.php?page=admin_transactions"
                    class="group bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-primary/50 transition-all hover:shadow-md sm:col-span-2">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl group-hover:bg-primary group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                        </div>
                        <span class="text-slate-300 group-hover:text-primary transition-colors">&rarr;</span>
                    </div>
                    <h4 class="text-lg font-bold text-slate-800 mb-1">Riwayat Penjualan</h4>
                    <p class="text-sm text-slate-500">Lihat semua transaksi pembelian user dan statistik buku terlaris.
                    </p>
                </a>
            </div>
        </div>
    </div>

    <!-- Sidebar: Pending Topups -->
    <div id="pending-list" class="lg:col-span-1">
        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-6 sticky top-24">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-rose-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Request Top Up
                </h3>
                <?php if (!empty($topups)): ?>
                    <span
                        class="bg-rose-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow-lg shadow-rose-200 animation-pulse"><?= count($topups) ?></span>
                <?php endif; ?>
            </div>

            <?php if (empty($topups)): ?>
                <div class="text-center py-12">
                    <div
                        class="w-16 h-16 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Semua permintaan selesai!</p>
                </div>
            <?php else: ?>
                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                    <?php foreach ($topups as $t): ?>
                        <div
                            class="p-4 bg-slate-50 rounded-2xl border border-slate-100 group hover:border-primary/30 transition-colors">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-slate-500 font-bold text-xs shadow-sm">
                                        <?= substr($t['name'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-xs"><?= htmlspecialchars($t['name']) ?></p>
                                        <p class="text-[10px] text-slate-400"><?= htmlspecialchars($t['email']) ?></p>
                                    </div>
                                </div>
                                <span class="font-extrabold text-primary text-sm">🪙 <?= number_format($t['amount']) ?></span>
                            </div>
                            <div class="flex gap-2">
                                <form method="POST" action="index.php?page=admin_action" class="flex-1">
                                    <input type="hidden" name="action" value="approve_topup">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <button type="submit"
                                        class="w-full py-2 bg-slate-900 hover:bg-green-600 text-white rounded-xl text-xs font-bold transition-all shadow-sm">Terima</button>
                                </form>
                                <form method="POST" action="index.php?page=admin_action" class="flex-1">
                                    <input type="hidden" name="action" value="reject_topup">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <button type="submit"
                                        class="w-full py-2 bg-white border border-slate-200 hover:bg-red-50 hover:text-red-600 hover:border-red-100 text-slate-500 rounded-xl text-xs font-bold transition-all">Tolak</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>