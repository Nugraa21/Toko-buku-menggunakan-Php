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

<div class="mb-12">
    <div
        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900 text-white text-xs font-bold uppercase tracking-widest mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
        </svg>
        <span>Control Panel</span>
    </div>
    <h2 class="text-4xl md:text-5xl font-serif font-bold text-slate-900 mb-4">Admin Dashboard</h2>
    <p class="text-lg text-slate-500 max-w-2xl font-sans">Kelola pengguna, buku, dan pantau statistik penjualan dalam
        satu tempat yang terintegrasi.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <!-- Users -->
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-book-hover transition-all duration-300 group">
        <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Total User</p>
            <p class="text-3xl font-serif font-bold text-slate-900"><?= number_format($stats['users']) ?></p>
        </div>
    </div>

    <!-- Books -->
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-book-hover transition-all duration-300 group">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Katalog Buku</p>
            <p class="text-3xl font-serif font-bold text-slate-900"><?= number_format($stats['books']) ?></p>
        </div>
    </div>

    <!-- Revenue -->
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-book-hover transition-all duration-300 group">
        <div class="w-16 h-16 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Pendapatan</p>
            <div class="flex items-center gap-2 text-green-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                     <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                     <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                </svg>
                <span class="text-3xl font-serif font-bold"><?= number_format($stats['revenue']) ?></span>
            </div>
        </div>
    </div>

    <!-- Pending (Clickable) -->
    <a href="#pending-list" class="bg-white p-6 rounded-[2rem] shadow-sm border border-rose-100 flex items-center gap-4 hover:shadow-book-hover transition-all duration-300 ring-2 ring-transparent hover:ring-rose-500/20 group cursor-pointer">
        <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-colors duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Pending Topup</p>
            <p class="text-3xl font-serif font-bold text-rose-600"><?= number_format($stats['pending']) ?></p>
        </div>
    </a>
</div>

<!-- Main Sections Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Management Shortcuts -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Quick Actions -->
        <div>
            <h3 class="text-xl font-serif font-bold text-slate-900 mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Manajemen Data
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <a href="index.php?page=admin_users"
                    class="group bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300 relative overflow-hidden">
                    <!-- Decor -->
                     <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50/50 rounded-full translate-x-8 -translate-y-8 group-hover:bg-blue-100/50 transition-colors"></div>
                    
                    <div class="flex justify-between items-start mb-6 relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-primary group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="relative z-10">
                        <h4 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary transition-colors">Users & Saldo</h4>
                        <p class="text-sm text-slate-500 leading-relaxed">Edit data user, reset password, dan atur saldo token manual.</p>
                    </div>
                </a>

                <a href="index.php?page=admin_books"
                    class="group bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300 relative overflow-hidden">
                     <!-- Decor -->
                     <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50/50 rounded-full translate-x-8 -translate-y-8 group-hover:bg-amber-100/50 transition-colors"></div>

                    <div class="flex justify-between items-start mb-6 relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform duration-300">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-primary group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="relative z-10">
                         <h4 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary transition-colors">Katalog Buku</h4>
                        <p class="text-sm text-slate-500 leading-relaxed">Tambah buku baru, upload cover, update stok dan harga.</p>
                    </div>
                </a>

                <a href="index.php?page=admin_transactions"
                    class="group bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300 relative overflow-hidden sm:col-span-2">
                     <!-- Decor -->
                     <div class="absolute top-0 right-0 w-32 h-32 bg-green-50/50 rounded-full translate-x-8 -translate-y-8 group-hover:bg-green-100/50 transition-colors"></div>

                    <div class="flex justify-between items-start mb-6 relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform duration-300">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-primary group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="relative z-10">
                        <h4 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary transition-colors">Riwayat Penjualan</h4>
                        <p class="text-sm text-slate-500 leading-relaxed max-w-lg">Lihat semua transaksi pembelian user dan statistik buku terlaris dalam periode tertentu.</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Sidebar: Pending Topups -->
    <div id="pending-list" class="lg:col-span-1">
        <div class="bg-white rounded-[2rem] shadow-lg border border-slate-100 p-8 sticky top-24">
            <div class="flex justify-between items-center mb-8 pb-4 border-b border-slate-50">
                <h3 class="font-serif font-bold text-xl text-slate-900 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-rose-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Order Masuk
                </h3>
                <?php if (!empty($topups)): ?>
                    <span
                        class="bg-rose-500 text-white text-[10px] font-extrabold px-3 py-1 rounded-full shadow-lg shadow-rose-200 animate-pulse"><?= count($topups) ?></span>
                <?php endif; ?>
            </div>

            <?php if (empty($topups)): ?>
                <div class="text-center py-12">
                    <div
                        class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
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
                            class="p-5 bg-white rounded-2xl border border-slate-100 hover:border-primary/30 shadow-sm hover:shadow-md transition-all group">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-sm shadow-inner">
                                        <?= substr($t['name'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($t['name']) ?></p>
                                        <p class="text-[10px] text-slate-400"><?= htmlspecialchars($t['email']) ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="font-bold text-sm"><?= number_format($t['amount']) ?></span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <form method="POST" action="index.php?page=admin_action">
                                    <input type="hidden" name="action" value="approve_topup">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <button type="submit"
                                        class="w-full py-2.5 bg-slate-900 hover:bg-green-600 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1 group/btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Terima
                                    </button>
                                </form>
                                <form method="POST" action="index.php?page=admin_action">
                                    <input type="hidden" name="action" value="reject_topup">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <button type="submit"
                                        class="w-full py-2.5 bg-white border border-slate-200 hover:bg-red-50 hover:text-red-600 hover:border-red-100 text-slate-500 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>