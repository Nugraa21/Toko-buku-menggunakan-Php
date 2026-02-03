<?php
if (!isLoggedIn()) {
    redirect('index.php?page=login');
}

// Fetch owned books (Library)
// We use DISTINCT to avoid showing the same book twice if bought multiple times
$stmt = $pdo->prepare("
    SELECT DISTINCT b.* 
    FROM transaction_items ti
    JOIN transactions t ON ti.transaction_id = t.id
    JOIN books b ON ti.book_id = b.id
    WHERE t.user_id = ? AND t.status = 'completed'
    ORDER BY t.transaction_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$books = $stmt->fetchAll();
?>

<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-bold text-slate-900">Pustakaku</h2>
            <p class="text-slate-500">Koleksi buku digital yang telah Anda miliki.</p>
        </div>
        <div class="bg-amber-50 text-amber-700 px-4 py-2 rounded-lg border border-amber-100 font-bold text-sm">
            <?= count($books) ?> Buku
        </div>
    </div>

    <?php if (empty($books)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-20 text-center">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-5xl">📚</span>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Perpustakaan Kosong</h3>
            <p class="text-slate-500 mb-8 max-w-sm mx-auto">Anda belum memiliki koleksi buku. Yuk jelajahi katalog kami yang
                menarik!</p>
            <a href="index.php?page=catalog"
                class="inline-block px-8 py-3 bg-primary text-white font-bold rounded-full hover:bg-amber-600 transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                Lihat Katalog Buku
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <?php foreach ($books as $book): ?>
                <div
                    class="group bg-white rounded-xl border border-slate-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="relative aspect-[3/4] bg-slate-100 overflow-hidden">
                        <img src="assets/images/<?= htmlspecialchars($book['image']) ?>"
                            alt="<?= htmlspecialchars($book['title']) ?>"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                        <!-- Overlay Action -->
                        <div
                            class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button onclick="alert('Ini simulasi membaca buku <?= htmlspecialchars($book['title']) ?>.')"
                                class="px-6 py-2 bg-white text-slate-900 font-bold rounded-full hover:bg-primary hover:text-white transition-colors">
                                Baca Sekarang
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-slate-800 line-clamp-1 mb-1" title="<?= htmlspecialchars($book['title']) ?>">
                            <?= htmlspecialchars($book['title']) ?>
                        </h4>
                        <p class="text-xs text-slate-500">
                            <?= htmlspecialchars($book['author']) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>