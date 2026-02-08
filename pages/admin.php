<?php
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Stats Overview
$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'books' => $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn(),
    'revenue' => $pdo->query("SELECT SUM(total_tokens) FROM transactions")->fetchColumn() ?: 0,
    'pending_topups' => $pdo->query("SELECT COUNT(*) FROM topups WHERE status = 'pending'")->fetchColumn()
];

// Recent Topups (Pending)
$pending_topups = $pdo->query("
    SELECT t.*, u.name, u.email 
    FROM topups t JOIN users u ON t.user_id = u.id 
    WHERE t.status = 'pending' 
    ORDER BY created_at ASC
    LIMIT 5
")->fetchAll();

// Recent Sales (Completed Transactions)
$recent_sales = $pdo->query("
    SELECT t.*, u.name as user_name
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    WHERE t.status = 'completed'
    ORDER BY t.transaction_date DESC
    LIMIT 5
")->fetchAll();

// Get Top Selling Books for quick insight
$top_books = $pdo->query("
    SELECT b.title, SUM(ti.quantity) as total_sold
    FROM transaction_items ti
    JOIN books b ON ti.book_id = b.id
    GROUP BY b.id
    ORDER BY total_sold DESC
    LIMIT 3
")->fetchAll();
?>

<!-- Dashboard Header -->
<div class="mb-10 relative">
    <div class="absolute -top-10 -left-10 w-64 h-64 bg-amber-50 rounded-full blur-3xl -z-10 opacity-50"></div>
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900 text-white text-[10px] font-bold uppercase tracking-widest mb-3 shadow-lg shadow-slate-900/20">
                <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                Admin Console
            </div>
            <h2 class="text-4xl font-serif font-bold text-slate-900 mb-2">Selamat Datang, Admin</h2>
            <p class="text-slate-500 font-sans">Ringkasan aktivitas dan performa toko buku Anda hari ini.</p>
        </div>
        
        <?php if ($stats['pending_topups'] > 0): ?>
            <div class="animate-bounce bg-rose-50 border border-rose-100 text-rose-600 px-6 py-3 rounded-2xl flex items-center gap-3 shadow-sm">
                <div class="relative">
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-sm block">Perlu Tindakan</span>
                    <span class="text-xs text-rose-500 font-medium"><?= $stats['pending_topups'] ?> permintaan topup baru</span>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    <!-- Revenue Card -->
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300 group relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-green-50 to-transparent rounded-bl-[4rem] -z-0 group-hover:scale-110 transition-transform"></div>
        <div class="relative z-10 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center shadow-sm group-hover:bg-green-600 group-hover:text-white transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Total Pendapatan</p>
                <p class="text-2xl font-serif font-bold text-slate-900 group-hover:text-primary transition-colors">🪙 <?= number_format($stats['revenue']) ?></p>
            </div>
        </div>
    </div>

    <!-- Users Card -->
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300 group relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-50 to-transparent rounded-bl-[4rem] -z-0 group-hover:scale-110 transition-transform"></div>
        <div class="relative z-10 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Pengguna Aktif</p>
                <p class="text-2xl font-serif font-bold text-slate-900 group-hover:text-primary transition-colors"><?= number_format($stats['users']) ?></p>
            </div>
        </div>
    </div>

    <!-- Books Card -->
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300 group relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-amber-50 to-transparent rounded-bl-[4rem] -z-0 group-hover:scale-110 transition-transform"></div>
        <div class="relative z-10 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-sm group-hover:bg-amber-600 group-hover:text-white transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Total Buku</p>
                <p class="text-2xl font-serif font-bold text-slate-900 group-hover:text-primary transition-colors"><?= number_format($stats['books']) ?></p>
            </div>
        </div>
    </div>

    <!-- Pending Topup Card (Actionable) -->
    <a href="#pending-section" class="bg-white p-6 rounded-[2rem] shadow-sm border border-rose-100 hover:shadow-book-hover transition-all duration-300 group relative overflow-hidden ring-2 ring-transparent hover:ring-rose-200">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-rose-50 to-transparent rounded-bl-[4rem] -z-0 group-hover:scale-110 transition-transform"></div>
        <div class="relative z-10 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shadow-sm group-hover:bg-rose-600 group-hover:text-white transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Pending Topup</p>
                <p class="text-2xl font-serif font-bold text-rose-600"><?= number_format($stats['pending_topups']) ?></p>
            </div>
        </div>
    </a>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Left Column: Quick Actions & Recent Sales -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- Quick Actions -->
        <div>
            <h3 class="text-xl font-serif font-bold text-slate-900 mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
                Menu Cepat
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="index.php?page=admin_books" class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-primary/30 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-primary transition-colors">Kelola Buku</h4>
                        <p class="text-xs text-slate-400">Tambah & Edit Stok</p>
                    </div>
                </a>

                <a href="index.php?page=admin_transactions" class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-primary/30 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-primary transition-colors">Riwayat Transaksi</h4>
                        <p class="text-xs text-slate-400">Cek Penjualan</p>
                    </div>
                </a>

                <a href="index.php?page=admin_users" class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-primary/30 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-primary transition-colors">Kelola User</h4>
                        <p class="text-xs text-slate-400">Atur Akun & Password</p>
                    </div>
                </a>

                <a href="index.php?page=admin_categories" class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-primary/30 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center group-hover:bg-violet-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-primary transition-colors">Kategori</h4>
                        <p class="text-xs text-slate-400">Atur Genre Buku</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Sales Table -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-serif font-bold text-lg text-slate-900">Penjualan Terakhir</h3>
                <a href="index.php?page=admin_transactions" class="text-xs font-bold text-primary hover:text-amber-600 transition-colors">Lihat Semua Link &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 text-xs text-slate-400 uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-8 py-4">User</th>
                            <th class="px-8 py-4">Total</th>
                            <th class="px-8 py-4 text-right">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (empty($recent_sales)): ?>
                            <tr><td colspan="3" class="px-8 py-6 text-center text-slate-400 text-sm">Belum ada penjualan.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_sales as $sale): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-8 py-4">
                                        <div class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($sale['user_name']) ?></div>
                                        <div class="text-[10px] text-slate-400">Order #<?= $sale['id'] ?></div>
                                    </td>
                                    <td class="px-8 py-4">
                                        <span class="inline-flex items-center gap-1 font-bold text-emerald-600 text-sm bg-emerald-50 px-2 py-1 rounded-lg">
                                            <span>🪙</span>
                                            <?= number_format($sale['total_tokens']) ?>
                                        </span>
                                    </td>
                                    <td class="px-8 py-4 text-right text-xs text-slate-400 font-mono">
                                        <?= date('d/m/Y', strtotime($sale['transaction_date'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Pending Actions & Top Books -->
    <div class="space-y-8">
        
        <!-- Pending Topups Widget -->
        <div id="pending-section" class="bg-white rounded-[2rem] shadow-lg border border-slate-100 overflow-hidden sticky top-24">
            <div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-serif font-bold text-lg text-slate-900 flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                    </span>
                    Permintaan Topup
                </h3>
                <span class="bg-rose-100 text-rose-600 text-xs font-bold px-2.5 py-1 rounded-lg">
                    <?= count($pending_topups) ?> Pending
                </span>
            </div>
            
            <div class="p-4 max-h-[400px] overflow-y-auto custom-scrollbar space-y-3">
                <?php if (empty($pending_topups)): ?>
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-sm text-slate-400">Semua aman! Tidak ada permintaan pending.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($pending_topups as $t): ?>
                        <div class="bg-slate-50/50 rounded-xl p-4 border border-slate-100 hover:border-rose-200 hover:shadow-sm transition-all group">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <div class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($t['name']) ?></div>
                                    <div class="text-xs text-slate-400"><?= htmlspecialchars($t['email']) ?></div>
                                </div>
                                <div class="font-bold text-primary text-sm flex items-center gap-1">
                                    <span>🪙</span> <?= number_format($t['amount']) ?>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <form method="POST" action="index.php?page=admin_action">
                                    <input type="hidden" name="action" value="approve_topup">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <button class="w-full py-1.5 bg-slate-900 text-white text-xs font-bold rounded-lg hover:bg-emerald-600 transition-colors">Terima</button>
                                </form>
                                <form method="POST" action="index.php?page=admin_action">
                                    <input type="hidden" name="action" value="reject_topup">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <button class="w-full py-1.5 bg-white border border-slate-200 text-slate-500 text-xs font-bold rounded-lg hover:text-rose-600 hover:border-rose-200 transition-colors">Tolak</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Books Mini Widget -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6">
            <h3 class="font-serif font-bold text-lg text-slate-900 mb-4">Top 3 Buku Terlaris</h3>
            <div class="space-y-4">
                <?php foreach ($top_books as $idx => $tb): ?>
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm shadow-sm flex-shrink-0">
                            #<?= $idx + 1 ?>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-sm truncate"><?= htmlspecialchars($tb['title']) ?></p>
                            <p class="text-xs text-slate-400"><?= $tb['total_sold'] ?> Terjual</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>
<div class="mb-48"></div> <!-- Admin Dashboard Spacer -->