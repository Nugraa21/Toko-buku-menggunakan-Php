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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-8 gap-y-12">
            <?php foreach ($books as $book): ?>
                <div class="group relative">
                    <!-- Book Card -->
                    <div
                        class="bg-white rounded-[2rem] p-4 shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-500 flex flex-col h-full relative z-10">

                        <!-- Image Container -->
                        <div
                            class="relative aspect-[2/3] rounded-2xl overflow-hidden mb-6 shadow-book group-hover:shadow-2xl transition-all duration-500">
                            <img src="assets/images/<?= htmlspecialchars($book['image']) ?>"
                                alt="<?= htmlspecialchars($book['title']) ?>"
                                class="absolute inset-0 w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-110">

                            <!-- Overlay Gradient -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent opacity-60 group-hover:opacity-40 transition-opacity duration-500">
                            </div>

                            <!-- Read Button Overlay -->
                            <div
                                class="absolute inset-0 z-10 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 gap-3">
                                <button onclick="alert('Membuka reader untuk: <?= htmlspecialchars($book['title']) ?>')"
                                    class="px-6 py-3 bg-white text-slate-900 font-bold rounded-xl hover:bg-primary hover:text-white transition-all shadow-xl transform translate-y-4 group-hover:translate-y-0 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    Baca Sekarang
                                </button>
                            </div>

                            <!-- Owned Badge -->
                            <div
                                class="absolute top-3 right-3 px-3 py-1 bg-green-500/90 backdrop-blur text-white text-[10px] font-bold uppercase tracking-wider rounded-lg shadow-lg">
                                Milik Anda
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex flex-col flex-1 px-2 text-center">
                            <h3
                                class="font-serif font-bold text-xl text-slate-900 leading-tight mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                                <?= htmlspecialchars($book['title']) ?>
                            </h3>
                            <p class="text-slate-500 text-sm italic font-sans mb-4">
                                by <?= htmlspecialchars($book['author']) ?>
                            </p>

                            <div class="mt-auto pt-4 border-t border-slate-50 w-full">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Digital Copy</span>
                            </div>
                        </div>
                    </div>

                    <!-- Decorative Background Blob -->
                    <div
                        class="absolute inset-0 bg-accent/5 rounded-[2.5rem] transform -rotate-2 scale-95 -z-10 group-hover:-rotate-3 transition-transform duration-500 opacity-0 group-hover:opacity-100">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>