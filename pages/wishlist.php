<?php
if (!isLoggedIn()) {
    redirect('index.php?page=login');
}

$user_id = $_SESSION['user_id'];

// Remove Action (if clicked from list)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_wishlist'])) {
    $book_id = $_POST['book_id'];
    $stmt = $pdo->prepare("DELETE FROM wishlists WHERE user_id = ? AND book_id = ?");
    $stmt->execute([$user_id, $book_id]);
    $_SESSION['flash_message'] = "Buku dihapus dari wishlist.";
    $_SESSION['flash_type'] = "success";
    header("Location: index.php?page=wishlist");
    exit();
}

// Fetch Wishlist Items
$stmt = $pdo->prepare("
    SELECT w.*, b.id as book_id, b.title, b.author, b.price, b.image, b.stock, c.name as category_name
    FROM wishlists w
    JOIN books b ON w.book_id = b.id
    LEFT JOIN categories c ON b.category_id = c.id
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$stmt->execute([$user_id]);
$wishlist = $stmt->fetchAll();
?>

<div class="max-w-7xl mx-auto min-h-[60vh]">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-slate-900 mb-2">Wishlist Saya</h2>
            <p class="text-slate-500">Buku-buku yang ingin Anda miliki.</p>
        </div>
        <div
            class="bg-white px-5 py-2 rounded-full border border-slate-200 shadow-sm text-sm text-slate-600 font-medium">
            <?= count($wishlist) ?> Item Disimpan
        </div>
    </div>

    <?php if (empty($wishlist)): ?>
        <div class="text-center py-24 bg-slate-50 rounded-[3rem] border-2 border-dashed border-slate-200">
            <div class="w-24 h-24 bg-rose-50 text-rose-300 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 mb-2">Wishlist Masih Kosong</h3>
            <p class="text-slate-500 mb-8 max-w-md mx-auto">
                Anda belum menyimpan buku apapun. Jelajahi katalog kami dan simpan buku favorit Anda di sini.
            </p>
            <a href="index.php?page=catalog"
                class="px-8 py-3 bg-primary text-white font-bold rounded-full hover:bg-amber-600 transition-colors shadow-lg shadow-amber-200">
                Jelajahi Katalog
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php foreach ($wishlist as $item): ?>
                <div
                    class="group relative bg-white rounded-[2rem] p-4 shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300">
                    <!-- Remove Button -->
                    <form method="POST" class="absolute top-2 right-2 z-20">
                        <input type="hidden" name="remove_wishlist" value="1">
                        <input type="hidden" name="book_id" value="<?= $item['book_id'] ?>">
                        <button type="button" onclick="if(confirm('Hapus dari wishlist?')) this.form.submit()"
                            class="w-8 h-8 rounded-full bg-white/80 backdrop-blur text-slate-400 hover:bg-rose-50 hover:text-rose-500 flex items-center justify-center transition-colors shadow-sm"
                            title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>

                    <!-- Image -->
                    <div
                        class="relative aspect-[2/3] rounded-2xl overflow-hidden mb-5 shadow-book group-hover:shadow-lg transition-all bg-slate-100">
                        <?php if (!empty($item['category_name'])): ?>
                            <div
                                class="absolute top-3 left-3 px-2 py-1 bg-slate-900/60 backdrop-blur-sm text-white text-[10px] font-bold uppercase tracking-wider rounded-md z-10">
                                <?= htmlspecialchars($item['category_name']) ?>
                            </div>
                        <?php endif; ?>

                        <a href="index.php?page=detail&id=<?= $item['book_id'] ?>">
                            <img src="assets/images/<?= htmlspecialchars($item['image']) ?>"
                                alt="<?= htmlspecialchars($item['title']) ?>"
                                class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        </a>
                    </div>

                    <!-- Content -->
                    <div class="px-2">
                        <h3 class="font-serif font-bold text-lg text-slate-900 leading-tight mb-1 truncate">
                            <a href="index.php?page=detail&id=<?= $item['book_id'] ?>"
                                class="hover:text-primary transition-colors">
                                <?= htmlspecialchars($item['title']) ?>
                            </a>
                        </h3>
                        <p class="text-slate-500 text-sm italic mb-4 truncate">
                            <?= htmlspecialchars($item['author']) ?>
                        </p>

                        <div class="flex items-center justify-between mt-auto">
                            <div class="flex items-center gap-1 text-primary font-bold">
                                <span class="text-xs">🪙</span>
                                <span class="text-lg">
                                    <?= number_format($item['price']) ?>
                                </span>
                            </div>

                            <form action="index.php?page=cart_action" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="book_id" value="<?= $item['book_id'] ?>">
                                <button type="submit"
                                    class="px-4 py-2 bg-slate-900 text-white text-xs font-bold rounded-full hover:bg-primary transition-colors shadow-lg shadow-slate-200">
                                    + Keranjang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>