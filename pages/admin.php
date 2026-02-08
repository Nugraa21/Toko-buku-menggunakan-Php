<?php
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Stats Overview
$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'books' => $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn(),
    'revenue' => $pdo->query("SELECT SUM(total_tokens) FROM transactions")->fetchColumn() ?: 0,
    'total_topup' => $pdo->query("SELECT SUM(amount) FROM topups WHERE status = 'approved'")->fetchColumn() ?: 0
];

// Recent Topups (ALL)
$recent_topups = $pdo->query("
    SELECT t.*, u.name, u.email 
    FROM topups t JOIN users u ON t.user_id = u.id 
    ORDER BY created_at DESC
    LIMIT 7
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

// Top Rated Books (Logic Changed)
// Get books with highest average rating, include cover and rating count
$top_rated_books = $pdo->query("
    SELECT b.id, b.title, b.image as cover_image, b.author,
           AVG(r.rating) as avg_rating,
           COUNT(r.id) as review_count
    FROM books b
    LEFT JOIN reviews r ON b.id = r.book_id
    GROUP BY b.id
    HAVING review_count > 0
    ORDER BY avg_rating DESC, review_count DESC
    LIMIT 3
")->fetchAll();
?>

<!-- Dashboard Header -->
<div class="mb-10 relative">
    <div class="absolute -top-10 -left-10 w-64 h-64 bg-amber-50 rounded-full blur-3xl -z-10 opacity-50"></div>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900 text-white text-[10px] font-bold uppercase tracking-widest mb-3 shadow-lg shadow-slate-900/20">
                <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                Admin Console
            </div>
            <h2 class="text-4xl font-serif font-bold text-slate-900 mb-2">Selamat Datang, Admin</h2>
            <p class="text-slate-500 font-sans">Pantau transaksi token dan penjualan buku secara real-time.</p>
        </div>

    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    <!-- Revenue Card (Completed Sales) -->
    <div
        class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300 group relative overflow-hidden">
        <div
            class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-green-50 to-transparent rounded-bl-[4rem] -z-0 group-hover:scale-110 transition-transform">
        </div>
        <div class="relative z-10 flex items-center gap-4">
            <div
                class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center shadow-sm group-hover:bg-green-600 group-hover:text-white transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Penjualan Buku</p>
                <p class="text-2xl font-serif font-bold text-slate-900 group-hover:text-primary transition-colors">🪙
                    <?= number_format($stats['revenue']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Top Up Total Card -->
    <div
        class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300 group relative overflow-hidden">
        <div
            class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-50 to-transparent rounded-bl-[4rem] -z-0 group-hover:scale-110 transition-transform">
        </div>
        <div class="relative z-10 flex items-center gap-4">
            <div
                class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shadow-sm group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Total Top Up</p>
                <p class="text-2xl font-serif font-bold text-slate-900 group-hover:text-primary transition-colors">🪙
                    <?= number_format($stats['total_topup']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Users Card -->
    <div
        class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300 group relative overflow-hidden">
        <div
            class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-50 to-transparent rounded-bl-[4rem] -z-0 group-hover:scale-110 transition-transform">
        </div>
        <div class="relative z-10 flex items-center gap-4">
            <div
                class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Pengguna Aktif</p>
                <p class="text-2xl font-serif font-bold text-slate-900 group-hover:text-primary transition-colors">
                    <?= number_format($stats['users']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Books Card -->
    <div
        class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300 group relative overflow-hidden">
        <div
            class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-amber-50 to-transparent rounded-bl-[4rem] -z-0 group-hover:scale-110 transition-transform">
        </div>
        <div class="relative z-10 flex items-center gap-4">
            <div
                class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-sm group-hover:bg-amber-600 group-hover:text-white transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Total Buku</p>
                <p class="text-2xl font-serif font-bold text-slate-900 group-hover:text-primary transition-colors">
                    <?= number_format($stats['books']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Left Column: Quick Actions & Recent Sales -->
    <div class="lg:col-span-2 space-y-8">

        <!-- Quick Actions -->
        <div>
            <h3 class="text-xl font-serif font-bold text-slate-900 mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
                Menu Cepat
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="index.php?page=admin_books"
                    class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-primary/30 transition-all group">
                    <div
                        class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-primary transition-colors">Kelola Buku</h4>
                        <p class="text-xs text-slate-400">Tambah & Edit Stok</p>
                    </div>
                </a>

                <a href="index.php?page=admin_transactions"
                    class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-primary/30 transition-all group">
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-primary transition-colors">Riwayat
                            Transaksi</h4>
                        <p class="text-xs text-slate-400">Cek Penjualan</p>
                    </div>
                </a>

                <a href="index.php?page=admin_users"
                    class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-primary/30 transition-all group">
                    <div
                        class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-primary transition-colors">Kelola User</h4>
                        <p class="text-xs text-slate-400">Atur Akun & Password</p>
                    </div>
                </a>

                <a href="index.php?page=admin_categories"
                    class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-primary/30 transition-all group">
                    <div
                        class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center group-hover:bg-violet-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-primary transition-colors">Kategori</h4>
                        <p class="text-xs text-slate-400">Atur Genre Buku</p>
                    </div>
                </a>

                <a href="index.php?page=admin_reviews"
                    class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-primary/30 transition-all group">
                    <div
                        class="w-12 h-12 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center group-hover:bg-pink-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 group-hover:text-primary transition-colors">Ulasan & Rating
                        </h4>
                        <p class="text-xs text-slate-400">Moderasi Komentar</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Sales Table -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-serif font-bold text-lg text-slate-900">Penjualan Terakhir</h3>
                <a href="index.php?page=admin_transactions"
                    class="text-xs font-bold text-primary hover:text-amber-600 transition-colors">Lihat Semua Link
                    &rarr;</a>
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
                            <tr>
                                <td colspan="3" class="px-8 py-6 text-center text-slate-400 text-sm">Belum ada penjualan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_sales as $sale): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-8 py-4">
                                        <div class="font-bold text-slate-800 text-sm">
                                            <?= htmlspecialchars($sale['user_name']) ?>
                                        </div>
                                        <div class="text-[10px] text-slate-400">Order #<?= $sale['id'] ?></div>
                                    </td>
                                    <td class="px-8 py-4">
                                        <span
                                            class="inline-flex items-center gap-1 font-bold text-emerald-600 text-sm bg-emerald-50 px-2 py-1 rounded-lg">
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

    <!-- Right Column: Incoming Topups & Top Books -->
    <div class="space-y-8">

        <!-- Topup History Widget (Changed from just Pending) -->
        <div id="pending-section"
            class="bg-white rounded-[2rem] shadow-lg border border-slate-100 overflow-hidden sticky top-24">
            <div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-serif font-bold text-lg text-slate-900 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Riwayat Top Up
                </h3>
            </div>

            <div class="p-4 max-h-[400px] overflow-y-auto custom-scrollbar space-y-3">
                <?php if (empty($recent_topups)): ?>
                    <div class="text-center py-8">
                        <div
                            class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm text-slate-400">Belum ada aktivitas top up terbaru.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recent_topups as $t): ?>
                        <div class="bg-slate-50/50 rounded-xl p-4 border border-slate-100 transition-all group">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($t['name']) ?></div>
                                    <div class="text-[10px] text-slate-400"><?= htmlspecialchars($t['email']) ?></div>
                                </div>
                                <div class="font-bold text-primary text-sm flex items-center gap-1">
                                    <span>🪙</span> <?= number_format($t['amount']) ?>
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="text-slate-400"><?= date('d M H:i', strtotime($t['created_at'])) ?></span>
                                <?php if ($t['status'] == 'approved'): ?>
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded font-bold">Diterima
                                        (Auto)</span>
                                <?php elseif ($t['status'] == 'rejected'): ?>
                                    <span class="bg-rose-100 text-rose-700 px-2 py-0.5 rounded font-bold">Ditolak</span>
                                <?php else: ?>
                                    <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded font-bold">Pending</span>
                                <?php endif; ?>
                            </div>

                            <!-- Action Buttons only if Pending -->
                            <?php if ($t['status'] == 'pending'): ?>
                                <div class="grid grid-cols-2 gap-2 mt-2">
                                    <form method="POST" action="index.php?page=admin_action">
                                        <input type="hidden" name="action" value="approve_topup">
                                        <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                        <button
                                            class="w-full py-1.5 bg-slate-900 text-white text-xs font-bold rounded-lg hover:bg-emerald-600 transition-colors">Terima</button>
                                    </form>
                                    <form method="POST" action="index.php?page=admin_action">
                                        <input type="hidden" name="action" value="reject_topup">
                                        <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                        <button
                                            class="w-full py-1.5 bg-white border border-slate-200 text-slate-500 text-xs font-bold rounded-lg hover:text-rose-600 hover:border-rose-200 transition-colors">Tolak</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Rated Books Widget (Updated) -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-serif font-bold text-lg text-slate-900">Buku Rating Tertinggi</h3>
                <a href="index.php?page=admin_reviews"
                    class="text-xs font-bold text-pink-600 hover:text-pink-700 transition-colors">Lihat Review
                    &rarr;</a>
            </div>

            <div class="p-4 space-y-4">
                <?php if (empty($top_rated_books)): ?>
                    <p class="text-center text-slate-400 text-sm py-4">Belum ada rating buku.</p>
                <?php else: ?>
                    <?php foreach ($top_rated_books as $idx => $tb): ?>
                        <div class="flex items-start gap-4 p-3 hover:bg-slate-50 rounded-xl transition-colors group">
                            <!-- Number Badge -->
                            <div
                                class="w-8 h-8 rounded-lg bg-pink-50 text-pink-600 flex items-center justify-center font-serif font-bold text-sm shadow-sm flex-shrink-0 mt-1">
                                #<?= $idx + 1 ?>
                            </div>

                            <!-- Book Info -->
                            <div class="flex-grow min-w-0">
                                <p class="font-bold text-slate-800 text-sm truncate group-hover:text-primary transition-colors">
                                    <?= htmlspecialchars($tb['title']) ?>
                                </p>
                                <p class="text-xs text-slate-400 truncate mb-1"><?= htmlspecialchars($tb['author']) ?></p>

                                <!-- Rating Stars -->
                                <div class="flex items-center gap-1.5">
                                    <div class="flex text-amber-400">
                                        <?php
                                        $rating = round($tb['avg_rating']);
                                        for ($i = 0; $i < 5; $i++):
                                            ?>
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-3 w-3 <?= $i < $rating ? 'fill-current' : 'text-slate-200' ?>"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded font-bold">
                                        <?= number_format($tb['avg_rating'], 1) ?> (<?= $tb['review_count'] ?>)
                                    </span>
                                </div>
                            </div>

                            <!-- Tiny Cover -->
                            <div
                                class="w-10 h-14 rounded bg-slate-100 shadow-sm overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform">
                                <?php if ($tb['cover_image']): ?>
                                    <img src="assets/images/<?= htmlspecialchars($tb['cover_image']) ?>"
                                        class="w-full h-full object-cover">
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
<div class="mb-48"></div> <!-- Admin Dashboard Spacer -->