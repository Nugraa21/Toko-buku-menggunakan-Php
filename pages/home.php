<?php
// Fetch featured/latest books
$stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC");
$books = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="relative bg-white rounded-3xl overflow-hidden shadow-2xl mb-16 border border-slate-100">
    <!-- Background Decor -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-amber-100 opacity-50 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-blue-50 opacity-50 blur-3xl"></div>

    <div class="relative px-6 py-16 md:py-24 md:px-12 text-center max-w-4xl mx-auto">
        <span
            class="inline-block px-4 py-1.5 rounded-full bg-amber-50 text-amber-600 font-bold text-sm mb-6 border border-amber-100">
            ✨ Platform Buku Token #1 di Indonesia
        </span>
        <h1 class="text-5xl md:text-6xl font-extrabold text-slate-900 tracking-tight mb-6 leading-tight">
            Jelajahi Dunia Pengetahuan <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">Tanpa
                Batas.</span>
        </h1>
        <p class="text-lg md:text-xl text-slate-500 mb-10 leading-relaxed max-w-2xl mx-auto">
            Akses ribuan buku berkualitas premium menggunakan sistem token yang mudah, cepat, dan aman. Mulai membaca
            hari ini.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#katalog"
                class="px-8 py-4 bg-primary text-white text-lg font-bold rounded-full shadow-lg hover:bg-amber-600 hover:shadow-xl transition-all transform hover:-translate-y-1">
                Jelajahi Katalog
            </a>
            <?php if (!isLoggedIn()): ?>
                <a href="index.php?page=register"
                    class="px-8 py-4 bg-white text-slate-700 border-2 border-slate-200 text-lg font-bold rounded-full hover:border-slate-800 hover:text-slate-900 transition-all">
                    Daftar Sekarang
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20 px-4">
    <div
        class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100 text-center hover:scale-105 transition-transform duration-300">
        <div class="text-4xl font-extrabold text-slate-900 mb-2">500+</div>
        <div class="text-slate-500 font-medium">Buku Premium</div>
    </div>
    <div
        class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100 text-center hover:scale-105 transition-transform duration-300">
        <div class="text-4xl font-extrabold text-slate-900 mb-2">10k+</div>
        <div class="text-slate-500 font-medium">Pengguna Aktif</div>
    </div>
    <div
        class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100 text-center hover:scale-105 transition-transform duration-300">
        <div class="text-4xl font-extrabold text-slate-900 mb-2">Instant</div>
        <div class="text-slate-500 font-medium">Akses Digital</div>
    </div>
</div>

<!-- Catalog Section -->
<section id="katalog" class="scroll-mt-24">
    <div class="flex flex-col md:flex-row justify-between items-end mb-12">
        <div>
            <h2 class="text-3xl font-bold text-slate-900 mb-4">Katalog Buku Pilihan</h2>
            <p class="text-slate-500 text-lg">Temukan buku favoritmu dari berbagai kategori menarik.</p>
        </div>
        <div class="hidden md:block">
            <!-- Optional Filter or Sort could go here -->
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <?php foreach ($books as $book): ?>
            <div
                class="group bg-white rounded-2xl border border-slate-100 overflow-hidden hover:shadow-2xl hover:border-amber-200 transition-all duration-300 flex flex-col h-full">
                <a href="index.php?page=detail&id=<?= $book['id'] ?>"
                    class="relative block overflow-hidden aspect-[3/4] bg-slate-100">
                    <img src="assets/images/<?= htmlspecialchars($book['image']) ?>"
                        alt="<?= htmlspecialchars($book['title']) ?>"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                    <!-- Overlay Gradient -->
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
                            <a
                                href="index.php?page=detail&id=<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></a>
                        </h3>
                        <p class="text-slate-500 text-sm"><?= htmlspecialchars($book['author']) ?></p>
                    </div>

                    <div class="mt-auto flex items-center justify-between pt-4 border-t border-slate-50">
                        <div>
                            <span class="block text-xs text-slate-400 uppercase font-bold tracking-wider">Harga</span>
                            <span class="text-lg font-extrabold text-primary">🪙 <?= number_format($book['price']) ?></span>
                        </div>
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
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>