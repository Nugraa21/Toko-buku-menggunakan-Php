<?php
// Get featured books (just taking 3 latest for "New Arrivals")
$stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 3");
$new_books = $stmt->fetchAll();

// Get "Best Sellers" (Simulated by random or manual pick if no sales data yet, but let's try to query sales)
// If sales table exists, we could use that. For landing page cache/speed, simple query is fine.
$best_sellers = $pdo->query("
    SELECT b.* FROM books b 
    LEFT JOIN transaction_items ti ON b.id = ti.book_id 
    GROUP BY b.id 
    ORDER BY SUM(ti.quantity) DESC 
    LIMIT 3
")->fetchAll();
if (empty($best_sellers))
    $best_sellers = $new_books; // Fallback
?>

<!-- Professional Hero Section -->
<section class="relative bg-white rounded-3xl overflow-hidden shadow-2xl mb-20 border border-slate-100">
    <div class="grid grid-cols-1 lg:grid-cols-2">
        <div class="p-12 lg:p-20 flex flex-col justify-center relative z-10">
            <span
                class="inline-block px-4 py-1.5 rounded-full bg-amber-50 text-amber-600 font-bold text-sm mb-6 border border-amber-100 w-fit">
                ✨ Platform Buku Digital #1
            </span>
            <h1 class="text-5xl lg:text-7xl font-extrabold text-slate-900 tracking-tight mb-8 leading-[1.1]">
                Buka Jendela <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">Dunia
                    Baru.</span>
            </h1>
            <p class="text-lg text-slate-500 mb-10 leading-relaxed max-w-lg">
                Temukan ribuan buku berkualitas, dari pengembangan diri hingga fiksi terbaik. Akses instan, baca kapan
                saja.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="index.php?page=catalog"
                    class="px-8 py-4 bg-primary text-white text-lg font-bold rounded-full shadow-lg hover:bg-amber-600 hover:shadow-xl transition-all transform hover:-translate-y-1 text-center">
                    Mulai Membaca
                </a>
                <?php if (!isLoggedIn()): ?>
                    <a href="index.php?page=register"
                        class="px-8 py-4 bg-white text-slate-700 border-2 border-slate-200 text-lg font-bold rounded-full hover:border-slate-800 hover:text-slate-900 transition-all text-center">
                        Daftar Gratis
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div
            class="bg-gradient-to-br from-amber-50 to-white relative overflow-hidden flex items-center justify-center p-12">
            <!-- Decorative blobs -->
            <div class="absolute top-10 right-10 w-64 h-64 bg-amber-200 rounded-full blur-3xl opacity-30"></div>
            <div class="absolute bottom-10 left-10 w-64 h-64 bg-orange-200 rounded-full blur-3xl opacity-30"></div>

            <img src="https://cdni.iconscout.com/illustration/premium/thumb/online-library-4354728-3611369.png"
                alt="Library Illustration"
                class="relative z-10 w-full max-w-md drop-shadow-2xl hover:scale-105 transition-transform duration-500">
        </div>
    </div>
</section>

<!-- Features Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20 px-4">
    <div
        class="bg-white p-8 rounded-3xl shadow-lg border border-slate-100 text-left hover:-translate-y-2 transition-transform duration-300">
        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl mb-6">⚡
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">Akses Instan</h3>
        <p class="text-slate-500 leading-relaxed">Beli dan langsung baca detik itu juga. Tanpa pengiriman fisik, tanpa
            menunggu.</p>
    </div>
    <div
        class="bg-white p-8 rounded-3xl shadow-lg border border-slate-100 text-left hover:-translate-y-2 transition-transform duration-300">
        <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-2xl mb-6">🔒
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">Transaksi Aman</h3>
        <p class="text-slate-500 leading-relaxed">Sistem token yang aman dan terenkripsi menjamin kenyamanan Anda
            bertransaksi.</p>
    </div>
    <div
        class="bg-white p-8 rounded-3xl shadow-lg border border-slate-100 text-left hover:-translate-y-2 transition-transform duration-300">
        <div class="w-14 h-14 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center text-2xl mb-6">📱
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">Multi Perangkat</h3>
        <p class="text-slate-500 leading-relaxed">Akses perpustakaan Anda dari laptop, tablet, atau smartphone dengan
            mudah.</p>
    </div>
</div>

<!-- Best Sellers Section -->
<section class="mb-20">
    <div class="flex justify-between items-end mb-10">
        <div>
            <h2 class="text-3xl font-bold text-slate-900 mb-2">Paling Diminati</h2>
            <p class="text-slate-500">Buku-buku yang sedang hangat dibicarakan minggu ini.</p>
        </div>
        <a href="index.php?page=catalog" class="text-primary font-bold hover:text-amber-600 transition-colors">Lihat
            Semua &rarr;</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php foreach ($best_sellers as $book): ?>
            <div
                class="group bg-white rounded-2xl border border-slate-100 overflow-hidden hover:shadow-2xl transition-all flex flex-col">
                <div class="relative aspect-[4/3] bg-slate-100 overflow-hidden">
                    <img src="assets/images/<?= htmlspecialchars($book['image']) ?>"
                        class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105"
                        alt="<?= htmlspecialchars($book['title']) ?>">
                    <span
                        class="absolute top-4 left-4 bg-amber-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">Best
                        Seller</span>
                </div>
                <div class="p-6 flex-1 flex flex-col">
                    <h3 class="font-bold text-xl text-slate-900 mb-2 line-clamp-1"><?= htmlspecialchars($book['title']) ?>
                    </h3>
                    <p class="text-slate-500 text-sm mb-4 line-clamp-2"><?= htmlspecialchars($book['description']) ?></p>
                    <div class="mt-auto flex justify-between items-center">
                        <span class="font-extrabold text-primary text-lg">🪙 <?= number_format($book['price']) ?></span>
                        <a href="index.php?page=detail&id=<?= $book['id'] ?>"
                            class="text-sm font-bold text-slate-900 underline decoration-2 decoration-primary hover:decoration-amber-600">Detail</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Call to Action -->
<section class="bg-slate-900 rounded-3xl p-12 md:p-20 text-center relative overflow-hidden">
    <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
    <div class="relative z-10 max-w-2xl mx-auto">
        <h2 class="text-4xl font-extrabold text-white mb-6">Siap Memulai Petualangan?</h2>
        <p class="text-slate-400 text-lg mb-10">Bergabunglah dengan ribuan pembaca lainnya dan bangun perpustakaan
            digital impianmu sekarang juga.</p>
        <a href="index.php?page=catalog"
            class="inline-block px-10 py-4 bg-white text-slate-900 font-bold rounded-full shadow-xl hover:bg-amber-50 transition-colors text-lg">
            Jelajahi Sekarang
        </a>
    </div>
</section>