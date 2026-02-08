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
    <!-- Search Header -->
    <div
        class="bg-secondary rounded-[2.5rem] p-10 md:p-14 mb-16 text-center relative overflow-hidden shadow-2xl shadow-secondary/20">
        <!-- Abstract Decoration -->
        <div
            class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl pointer-events-none">
        </div>
        <div
            class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-accent/5 rounded-full translate-y-1/2 -translate-x-1/3 blur-3xl pointer-events-none">
        </div>

        <h2 class="text-3xl md:text-5xl font-serif font-bold text-white mb-8 relative z-10 leading-tight">
            Katalog Buku Lengkap
        </h2>
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-8 gap-y-12">
            <?php foreach ($books as $book): ?>
                <div class="group relative">
                    <!-- Book Card -->
                    <div
                        class="bg-white rounded-[2rem] p-4 shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-500 flex flex-col h-full relative z-10">

                        <!-- Image Container with 3D Effect -->
                        <div
                            class="relative aspect-[2/3] rounded-2xl overflow-hidden mb-6 shadow-book group-hover:shadow-2xl transition-all duration-500">
                            <!-- Badge -->
                            <?php if ($book['stock'] < 5): ?>
                                <div
                                    class="absolute top-3 left-3 px-3 py-1 bg-rose-500/90 backdrop-blur text-white text-[10px] font-bold uppercase tracking-wider rounded-lg shadow-lg z-20">
                                    Sisa <?= $book['stock'] ?>
                                </div>
                            <?php endif; ?>

                            <img src="assets/images/<?= htmlspecialchars($book['image']) ?>"
                                alt="<?= htmlspecialchars($book['title']) ?>"
                                class="absolute inset-0 w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-110">

                            <!-- Overlay Gradient -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity duration-500">
                            </div>

                            <!-- Floating Action Button (Quick View) -->
                            <a href="index.php?page=detail&id=<?= $book['id'] ?>"
                                class="absolute inset-0 z-10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <div
                                    class="w-14 h-14 bg-white/20 backdrop-blur-md border border-white/30 rounded-full flex items-center justify-center text-white transform translate-y-4 group-hover:translate-y-0 transition-all hover:bg-white hover:text-primary shadow-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                            </a>
                        </div>

                        <!-- Content -->
                        <div class="flex flex-col flex-1 px-2">
                            <div class="mb-4">
                                <h3
                                    class="font-serif font-bold text-xl text-slate-900 leading-tight mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                                    <a href="index.php?page=detail&id=<?= $book['id'] ?>">
                                        <?= htmlspecialchars($book['title']) ?>
                                    </a>
                                </h3>
                                <p class="text-slate-500 text-sm italic font-sans">
                                    by <?= htmlspecialchars($book['author']) ?>
                                </p>
                            </div>

                            <div class="mt-auto pt-4 border-t border-slate-50 flex items-end justify-between gap-4">
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Harga</p>
                                    <div class="flex items-center gap-1.5 text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path
                                                d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-xl font-serif font-bold"><?= number_format($book['price']) ?></span>
                                    </div>
                                </div>

                                <?php if (!isAdmin()): ?>
                                    <form action="index.php?page=cart_action" method="POST">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                        <button type="submit"
                                            class="w-12 h-12 rounded-full bg-slate-900 text-white flex items-center justify-center hover:bg-accent transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1 group/btn">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 transform group-hover/btn:scale-110 transition-transform" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Decorative Background Blob -->
                    <div
                        class="absolute inset-0 bg-primary/5 rounded-[2.5rem] transform rotate-3 scale-95 -z-10 group-hover:rotate-6 transition-transform duration-500 opacity-0 group-hover:opacity-100">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>