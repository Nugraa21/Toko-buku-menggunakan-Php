<?php
// Fetch Categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// Filter Logic
$search = $_GET['search'] ?? '';
$category_slug = $_GET['category'] ?? '';

$query = "SELECT b.*, c.name as category_name FROM books b LEFT JOIN categories c ON b.category_id = c.id WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (b.title LIKE ? OR b.author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category_slug)) {
    // Find category id by slug
    $catStmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
    $catStmt->execute([$category_slug]);
    $catId = $catStmt->fetchColumn();

    if ($catId) {
        $query .= " AND b.category_id = ?";
        $params[] = $catId;
    }
}

$query .= " ORDER BY b.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll();
?>

<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div
        class="bg-secondary rounded-[2.5rem] p-8 md:p-12 mb-12 text-center relative overflow-hidden shadow-2xl shadow-secondary/20">
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

        <!-- Search Form -->
        <form method="GET" class="max-w-xl mx-auto relative z-10 flex gap-2 mb-8">
            <input type="hidden" name="page" value="catalog">
            <?php if (!empty($category_slug)): ?>
                <input type="hidden" name="category" value="<?= htmlspecialchars($category_slug) ?>">
            <?php endif; ?>

            <div class="relative flex-1">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                    placeholder="Cari judul buku atau penulis..."
                    class="w-full pl-12 pr-4 py-4 rounded-full border-none focus:ring-4 focus:ring-primary/30 text-slate-800 font-medium placeholder-slate-400 shadow-xl transition-all outline-none">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <button type="submit"
                class="px-8 py-4 bg-primary text-white font-bold rounded-full hover:bg-amber-600 transition-colors shadow-lg shadow-primary/30">
                Cari
            </button>
        </form>

        <!-- Category Pills (Horizontal Scroll) -->
        <div class="relative z-10 max-w-4xl mx-auto">
            <div class="flex flex-wrap justify-center gap-3">
                <a href="index.php?page=catalog"
                    class="px-5 py-2 rounded-full text-sm font-bold transition-all border <?= empty($category_slug) ? 'bg-white text-secondary border-white shadow-lg transform scale-105' : 'bg-white/10 text-white/70 border-white/10 hover:bg-white/20 hover:text-white' ?>">
                    Semua
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="index.php?page=catalog&category=<?= $cat['slug'] ?>"
                        class="px-5 py-2 rounded-full text-sm font-bold transition-all border <?= $category_slug === $cat['slug'] ? 'bg-white text-secondary border-white shadow-lg transform scale-105' : 'bg-white/10 text-white/70 border-white/10 hover:bg-white/20 hover:text-white' ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Results -->
    <?php if (empty($books)): ?>
        <div class="text-center py-20 bg-slate-50 rounded-[3rem] border-2 border-dashed border-slate-200">
            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <p class="text-2xl text-slate-800 font-bold mb-2">Tidak ada buku ditemukan</p>
            <p class="text-slate-500 max-w-md mx-auto">
                Coba cari dengan kata kunci lain atau pilih kategori yang berbeda.
            </p>
            <a href="index.php?page=catalog"
                class="inline-block mt-6 px-6 py-2 bg-slate-900 text-white rounded-full font-bold hover:bg-slate-800 transition-colors">
                Reset Filter
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-8 gap-y-12 mb-12">
            <?php foreach ($books as $book): ?>
                <div class="group relative">
                    <!-- Book Card -->
                    <div
                        class="bg-white rounded-[2rem] p-4 shadow-sm border border-slate-100 hover:shadow-book-hover hover:-translate-y-2 transition-all duration-500 flex flex-col h-full relative z-10 group-hover:border-primary/20">

                        <!-- Image Container with 3D Effect -->
                        <div
                            class="relative aspect-[2/3] rounded-2xl overflow-hidden mb-6 shadow-book group-hover:shadow-2xl transition-all duration-500 bg-slate-100">
                            <!-- Stock Badge -->
                            <?php if ($book['stock'] < 5): ?>
                                <div
                                    class="absolute top-3 left-3 px-3 py-1 bg-rose-500/90 backdrop-blur-sm text-white text-[10px] font-bold uppercase tracking-wider rounded-lg shadow-lg z-20">
                                    Sisa <?= $book['stock'] ?>
                                </div>
                            <?php endif; ?>

                            <!-- Category Badge -->
                            <?php if (!empty($book['category_name'])): ?>
                                <div
                                    class="absolute top-3 right-3 px-3 py-1 bg-slate-900/40 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-wider rounded-lg shadow-sm z-20">
                                    <?= htmlspecialchars($book['category_name']) ?>
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
                                class="absolute inset-0 z-10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 backdrop-blur-[2px] bg-black/10">
                                <span
                                    class="px-6 py-2 bg-white text-slate-900 font-bold rounded-full shadow-xl transform translate-y-4 group-hover:translate-y-0 transition-all hover:scale-105 hover:bg-primary hover:text-white">
                                    Lihat Detail
                                </span>
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
                                <div class="flex items-center gap-2">
                                    <p class="text-slate-500 text-sm italic font-sans truncate">
                                        <?= htmlspecialchars($book['author']) ?>
                                    </p>

                                    <!-- Dynamic Rating -->
                                    <?php $avgRating = getBookRating($pdo, $book['id']); ?>
                                    <div
                                        class="flex items-center gap-1 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100 w-fit mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-amber-500 fill-current"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span
                                            class="text-xs font-bold text-amber-700"><?= number_format($avgRating, 1) ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between gap-4">
                                <div>
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
                                            class="w-10 h-10 rounded-full bg-slate-900 border border-slate-700 text-white flex items-center justify-center hover:bg-primary hover:border-primary transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1 group/btn"
                                            title="Tambah ke Keranjang">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 transform group-hover/btn:scale-110 transition-transform" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
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