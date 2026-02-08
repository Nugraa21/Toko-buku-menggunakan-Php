<?php
// Simple Search
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ? ORDER BY created_at DESC";
$params = ["%$search%", "%$search%"];

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll();
?>

<div class="max-w-7xl mx-auto">
    <!-- Search Header -->
    <div class="bg-slate-900 rounded-3xl p-8 md:p-12 mb-10 text-center relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-10 -mt-10 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-primary opacity-10 rounded-full -ml-10 -mb-10 blur-2xl"></div>

        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-6 relative z-10">Katalog Buku Lengkap</h2>
        <form method="GET" class="max-w-xl mx-auto relative z-10 flex gap-2">
            <input type="hidden" name="page" value="catalog">
            <div class="relative flex-1">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                    placeholder="Cari judul buku atau penulis..."
                    class="w-full pl-12 pr-4 py-4 rounded-full border-none focus:ring-2 focus:ring-primary text-slate-800 font-medium placeholder-slate-400 shadow-xl">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <button type="submit"
                class="px-6 py-4 bg-primary text-white font-bold rounded-full hover:bg-amber-600 transition-colors shadow-lg">
                Cari
            </button>
        </form>
    </div>

    <!-- Results -->
    <?php if (empty($books)): ?>
        <div class="text-center py-20">
            <p class="text-2xl text-slate-300 font-bold mb-2">Oops!</p>
            <p class="text-slate-500">Tidak ada buku yang cocok dengan pencarian "
                <?= htmlspecialchars($search) ?>"
            </p>
            <a href="index.php?page=catalog" class="inline-block mt-4 text-primary font-bold hover:underline">Reset
                Pencarian</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php foreach ($books as $book): ?>
                <div
                    class="group bg-white rounded-2xl border border-slate-100 overflow-hidden hover:shadow-2xl hover:border-amber-200 transition-all duration-300 flex flex-col h-full">
                    <a href="index.php?page=detail&id=<?= $book['id'] ?>"
                        class="relative block overflow-hidden aspect-[3/4] bg-slate-100">
                        <img src="assets/images/<?= htmlspecialchars($book['image']) ?>"
                            alt="<?= htmlspecialchars($book['title']) ?>"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                        <!-- Stock Badge -->
                        <?php if ($book['stock'] < 5): ?>
                            <div
                                class="absolute top-3 left-3 px-3 py-1 bg-rose-500 text-white text-xs font-bold rounded-full shadow-md z-10">
                                Sisa
                                <?= $book['stock'] ?>
                            </div>
                        <?php endif; ?>

                        <!-- Overlay -->
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>

                        <div
                            class="absolute bottom-4 left-4 right-4 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                            <span
                                class="inline-block px-4 py-2 bg-white/90 backdrop-blur text-slate-900 text-sm font-bold rounded-full">
                                Lihat Detail
                            </span>
                        </div>
                    </a>

                    <div class="p-6 flex flex-col flex-1">
                        <div class="mb-4">
                            <h3
                                class="text-lg font-bold text-slate-900 leading-tight mb-1 line-clamp-1 group-hover:text-primary transition-colors">
                                <a href="index.php?page=detail&id=<?= $book['id'] ?>">
                                    <?= htmlspecialchars($book['title']) ?>
                                </a>
                            </h3>
                            <p class="text-slate-500 text-sm">
                                <?= htmlspecialchars($book['author']) ?>
                            </p>
                        </div>

                        <div class="mt-auto flex items-center justify-between pt-4 border-t border-slate-50">
                            <div>
                                <span class="block text-xs text-slate-400 uppercase font-bold tracking-wider">Harga</span>
                                <span class="text-lg font-extrabold text-primary flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                    </svg>
                                    <?= number_format($book['price']) ?>
                                </span>
                            </div>
                            <?php if (!isAdmin()): ?>
                                <form action="index.php?page=cart_action" method="POST">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                    <button type="submit"
                                        class="w-10 h-10 rounded-full bg-slate-50 text-slate-700 flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>