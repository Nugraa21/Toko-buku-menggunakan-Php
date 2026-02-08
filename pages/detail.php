<?php
if (!isLoggedIn()) {
    redirect('index.php');
}

$id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Handle Wishlist Toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_wishlist'])) {
    // Check if exists
    $check = $pdo->prepare("SELECT id FROM wishlists WHERE user_id = ? AND book_id = ?");
    $check->execute([$user_id, $id]);
    $exists = $check->fetch();

    if ($exists) {
        $del = $pdo->prepare("DELETE FROM wishlists WHERE user_id = ? AND book_id = ?");
        $del->execute([$user_id, $id]);
        $_SESSION['flash_message'] = "Dihapus dari Wishlist";
        $_SESSION['flash_type'] = "info";
    } else {
        $add = $pdo->prepare("INSERT INTO wishlists (user_id, book_id) VALUES (?, ?)");
        $add->execute([$user_id, $id]);
        $_SESSION['flash_message'] = "Ditambahkan ke Wishlist";
        $_SESSION['flash_type'] = "success";
    }
    // Refresh to show state change
    header("Location: index.php?page=detail&id=$id");
    exit();
}

// Fetch Book Details with Category
$stmt = $pdo->prepare("
    SELECT b.*, c.name as category_name 
    FROM books b 
    LEFT JOIN categories c ON b.category_id = c.id 
    WHERE b.id = ?
");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    echo "<div class='text-center py-20 text-slate-500'>Buku tidak ditemukan.</div>";
    return;
}

// Check Wishlist Status
$wCheck = $pdo->prepare("SELECT id FROM wishlists WHERE user_id = ? AND book_id = ?");
$wCheck->execute([$user_id, $id]);
$is_wishlisted = $wCheck->fetch();

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review']) && !isAdmin()) {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
        $stmtr = $pdo->prepare("INSERT INTO reviews (user_id, book_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmtr->execute([$_SESSION['user_id'], $id, $rating, $comment]);
        $_SESSION['flash_message'] = "Ulasan berhasil dikirim!";
        $_SESSION['flash_type'] = "success";

        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }
}

// Fetch Reviews
$stmt_rev = $pdo->prepare("
    SELECT r.*, u.name 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.book_id = ? 
    ORDER BY r.created_at DESC
");
$stmt_rev->execute([$id]);
$reviews = $stmt_rev->fetchAll();
?>

<div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-slate-100 mb-8">
    <div class="md:flex">
        <!-- Image Section -->
        <div class="md:w-1/3 bg-slate-50 p-10 flex items-center justify-center relative">
            <div
                class="absolute top-0 right-0 w-full h-full bg-grid-slate-200/50 [mask-image:linear-gradient(to_bottom,white,transparent)]">
            </div>

            <div
                class="relative w-full aspect-[3/4] max-w-sm shadow-2xl shadow-slate-200 rounded-2xl overflow-hidden transform transition duration-500 hover:scale-[1.02] group">
                <img src="assets/images/<?= htmlspecialchars($book['image']) ?>"
                    alt="<?= htmlspecialchars($book['title']) ?>" class="absolute inset-0 w-full h-full object-cover">

                <!-- Wishlist Button Overlay on Image (Mobile) -->
                <form method="POST" class="absolute top-4 right-4 md:hidden">
                    <input type="hidden" name="toggle_wishlist" value="1">
                    <button type="submit"
                        class="w-10 h-10 rounded-full flex items-center justify-center backdrop-blur-md transition-all shadow-lg <?= $is_wishlisted ? 'bg-rose-500 text-white' : 'bg-white/30 text-white hover:bg-rose-500' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 <?= $is_wishlisted ? 'fill-current' : '' ?>" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Content Section -->
        <div class="md:w-2/3 p-8 md:p-12 flex flex-col justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-6">
                    <?php if (!empty($book['category_name'])): ?>
                        <a href="index.php?page=catalog&category=<?= strtolower($book['category_name']) ?>"
                            class="px-4 py-1.5 bg-primary/10 text-primary text-xs font-bold uppercase tracking-wide rounded-full hover:bg-primary hover:text-white transition-colors">
                            <?= htmlspecialchars($book['category_name']) ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($book['stock'] < 5): ?>
                        <span
                            class="px-4 py-1.5 bg-rose-100 text-rose-700 text-xs font-bold uppercase tracking-wide rounded-full flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Sisa <?= $book['stock'] ?>
                        </span>
                    <?php endif; ?>
                </div>

                <h1 class="text-4xl md:text-5xl font-serif font-bold text-slate-900 mb-3 leading-tight">
                    <?= htmlspecialchars($book['title']) ?>
                </h1>
                <p class="text-xl text-slate-500 font-medium mb-8 flex items-center gap-2">
                    <span class="text-slate-400">by</span>
                    <span
                        class="text-slate-800 border-b-2 border-primary/20"><?= htmlspecialchars($book['author']) ?></span>
                </p>

                <!-- Metadata Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 mb-8 py-6 border-y border-slate-100">
                    <?php if (!empty($book['rating'])): ?>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Rating</p>
                            <div class="flex items-center gap-1 text-amber-500 font-bold text-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <?= $book['rating'] ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($book['pages'])): ?>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Halaman</p>
                            <p class="font-bold text-slate-800 text-lg"><?= $book['pages'] ?> <span
                                    class="text-sm text-slate-400 font-normal">Hal</span></p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($book['language'])): ?>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Bahasa</p>
                            <p class="font-bold text-slate-800 text-lg"><?= $book['language'] ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($book['publisher'])): ?>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Penerbit</p>
                            <p class="font-bold text-slate-800 text-lg truncate"
                                title="<?= htmlspecialchars($book['publisher']) ?>">
                                <?= htmlspecialchars($book['publisher']) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="prose prose-slate max-w-none text-slate-600 mb-8 leading-relaxed">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Sinopsis</h3>
                    <p><?= nl2br(htmlspecialchars($book['description'])) ?></p>
                </div>
            </div>

            <div class="mt-auto">
                <div
                    class="flex flex-col sm:flex-row items-center justify-between gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Harga Spesial</p>
                        <div class="flex items-center gap-2 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-4xl font-serif font-bold"><?= number_format($book['price']) ?></span>
                        </div>
                    </div>

                    <?php if (!isAdmin()): ?>
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <!-- Wishlist Button (Desktop & Mobile) -->
                            <form method="POST" class="flex-shrink-0">
                                <input type="hidden" name="toggle_wishlist" value="1">
                                <button type="submit"
                                    class="w-14 h-14 rounded-2xl flex items-center justify-center border-2 transition-all <?= $is_wishlisted ? 'border-rose-500 bg-rose-50 text-rose-500' : 'border-slate-200 text-slate-400 hover:border-rose-300 hover:text-rose-500' ?>"
                                    title="<?= $is_wishlisted ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-7 w-7 <?= $is_wishlisted ? 'fill-current' : '' ?>" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>
                            </form>

                            <form action="index.php?page=cart_action" method="POST" class="flex-1 sm:flex-none">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <button type="submit"
                                    class="w-full sm:w-auto px-8 py-4 bg-slate-900 hover:bg-primary text-white rounded-2xl font-bold text-lg shadow-lg shadow-slate-200 hover:shadow-primary/30 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Beli Sekarang
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="px-8 py-3 bg-slate-200 text-slate-500 rounded-xl font-bold text-sm">
                            Mode Admin (Read Only)
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Section -->
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-8">
        <h3 class="text-2xl font-bold text-slate-900 mb-6 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-500" viewBox="0 0 20 20"
                fill="currentColor">
                <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            Ulasan Pembaca <span class="text-slate-400 text-lg font-normal">(<?= count($reviews) ?>)</span>
        </h3>

        <!-- Review Form -->
        <?php if (!isAdmin() && isLoggedIn()): ?>
            <div class="bg-white rounded-[2rem] p-8 mb-10 border border-slate-100 shadow-xl relative overflow-hidden">
                <!-- Decorative BG -->
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary/50 to-amber-300"></div>
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-amber-50 rounded-full blur-3xl pointer-events-none">
                </div>

                <form method="POST" class="relative z-10">
                    <input type="hidden" name="submit_review" value="1">

                    <div class="flex flex-col md:flex-row gap-8 md:gap-12">
                        <!-- Left: Rating Area -->
                        <div
                            class="md:w-1/3 flex flex-col items-center justify-center text-center border-b md:border-b-0 md:border-r border-slate-100 pb-8 md:pb-0 md:pr-8">
                            <h4 class="font-bold text-slate-800 text-lg mb-2">Berikan Nilai</h4>
                            <p class="text-slate-400 text-xs mb-6 px-4">Seberapa puaskah Anda dengan buku ini?</p>

                            <!-- Star Widget -->
                            <div class="flex flex-row-reverse justify-center gap-1.5 group/stars">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" class="peer hidden"
                                        required>
                                    <label for="star<?= $i ?>"
                                        class="text-4xl text-slate-200 cursor-pointer transition-all duration-200 hover:scale-110 peer-checked:text-amber-400 peer-hover:text-amber-400 peer-checked:drop-shadow-md"
                                        title="<?= $i ?> Bintang">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 fill-current"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                        </svg>
                                    </label>
                                <?php endfor; ?>
                            </div>

                            <div class="mt-4 h-6 text-sm font-bold text-primary animate-pulse" id="rating-label">
                                <!-- Dynamic label via JS or CSS could go here, simplified for now -->
                                Pilih Bintang
                            </div>
                        </div>

                        <!-- Right: Comment Area -->
                        <div class="md:w-2/3 flex flex-col">
                            <div class="mb-4">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Ulasan Anda</label>
                                <textarea name="comment" rows="4"
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all placeholder:text-slate-400 resize-none"
                                    placeholder="Ceritakan pengalaman membaca Anda... Apa yang paling menarik?"
                                    required></textarea>
                            </div>
                            <div class="flex items-center justify-between mt-auto">
                                <p class="text-xs text-slate-400 italic">*Ulasan Anda membantu pembaca lain.</p>
                                <button type="submit"
                                    class="px-8 py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-primary transition-colors shadow-lg shadow-slate-900/10 hover:shadow-primary/30 flex items-center gap-2 transform active:scale-95 duration-200">
                                    <span>Kirim</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Simple Script to Update Label -->
                <script>
                    const stars = document.querySelectorAll('input[name="rating"]');
                    const label = document.getElementById('rating-label');
                    const labels = { 1: 'Buruk 😞', 2: 'Kurang 😐', 3: 'Cukup 🙂', 4: 'Bagus 😀', 5: 'Sempurna! 😍' };

                    stars.forEach(star => {
                        star.addEventListener('change', function () {
                            label.textContent = labels[this.value];
                            label.classList.remove('animate-pulse');
                        });
                    });
                </script>
            </div>
        <?php endif; ?>

        <!-- Reviews List -->
        <div class="space-y-6">
            <?php if (empty($reviews)): ?>
                <div class="text-center py-8">
                    <div
                        class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <p class="text-slate-500 italic">Belum ada ulasan untuk buku ini. Jadilah yang pertama!</p>
                </div>
            <?php else: ?>
                <?php foreach ($reviews as $r): ?>
                    <div class="border-b border-slate-100 pb-6 last:border-0 last:pb-0">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-sm shadow-md">
                                    <?= substr($r['name'], 0, 1) ?>
                                </div>
                                <div>
                                    <h5 class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($r['name']) ?></h5>
                                    <div class="text-amber-500 text-xs flex">
                                        <?php for ($i = 0; $i < $r['rating']; $i++): ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs text-slate-400"><?= date('d M Y', strtotime($r['created_at'])) ?></span>
                        </div>
                        <p
                            class="text-slate-600 leading-relaxed text-sm pl-12 bg-slate-50 p-3 rounded-tr-xl rounded-b-xl ml-10">
                            <?= nl2br(htmlspecialchars($r['comment'])) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>