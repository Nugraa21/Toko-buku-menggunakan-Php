<?php
// Get featured books
$stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 3");
$new_books = $stmt->fetchAll();

// Best Sellers logic
$best_sellers = $pdo->query("
    SELECT b.* FROM books b 
    LEFT JOIN transaction_items ti ON b.id = ti.book_id 
    GROUP BY b.id 
    ORDER BY SUM(ti.quantity) DESC 
    LIMIT 3
")->fetchAll();
if (empty($best_sellers))
    $best_sellers = $new_books;
?>

<!-- Hero Section -->
<section class="relative mb-32 pt-10">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <!-- Text Content -->
        <div class="space-y-8 relative z-10 text-center lg:text-left">
            <div
                class="inline-flex items-center gap-3 px-4 py-2 bg-primary/5 border border-primary/20 rounded-full text-primary text-sm font-semibold tracking-wide uppercase">
                <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                <span>The #1 Digital Bookstore</span>
            </div>

            <h1 class="text-5xl lg:text-7xl font-serif font-bold text-slate-900 leading-[1.1]">
                Temukan <span class="italic text-primary">Jendela Dunia</span> di Genggaman Anda.
            </h1>

            <p class="text-lg text-slate-600 leading-loose max-w-xl mx-auto lg:mx-0 font-sans">
                Akses ribuan buku premium dari penulis ternama. Nikmati pengalaman membaca tanpa batas dengan koleksi
                yang dikurasi khusus untuk para inisiator perubahan.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-4">
                <a href="index.php?page=catalog"
                    class="px-8 py-4 bg-primary text-white font-bold rounded-xl shadow-xl shadow-primary/20 hover:bg-accent hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                    <span>Mulai Membaca</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-0l0-1.414-0 011.414-1.414 7-7 0 010-0zM10 18a1 1 0 000-0V2a1 1 0 010 0z" />
                    </svg>
                </a>
                <?php if (!isLoggedIn()): ?>
                    <a href="index.php?page=register"
                        class="px-8 py-4 bg-white text-slate-700 border border-slate-200 font-bold rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all flex items-center justify-center gap-3">
                        Buat Akun Gratis
                    </a>
                <?php endif; ?>
            </div>

            <!-- Trust Indicators -->
            <div class="pt-8 flex items-center justify-center lg:justify-start gap-8 text-slate-400">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium">Original Books</span>
                </div>
                <div class="h-4 w-px bg-slate-200"></div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium">Secure Token Payment</span>
                </div>
            </div>
        </div>

        <!-- Decorative Image Area -->
        <div class="relative hidden lg:block">
            <!-- decorative circles -->
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-amber-100/40 rounded-full blur-[100px] -z-10">
            </div>

            <div class="grid grid-cols-2 gap-6 items-center">
                <div class="space-y-6 transform translate-y-12">
                    <div
                        class="bg-white p-6 rounded-2xl shadow-book hover:shadow-book-hover transition-all duration-500 border border-slate-100">
                        <div
                            class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center text-primary mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="font-serif font-bold text-lg text-slate-900">Koleksi Lengkap</h3>
                        <p class="text-sm text-slate-500 mt-2">Dari fiksi hingga sains, temukan semuanya.</p>
                    </div>
                    <div
                        class="bg-white p-6 rounded-2xl shadow-book hover:shadow-book-hover transition-all duration-500 border border-slate-100">
                        <div
                            class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center text-accent mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="font-serif font-bold text-lg text-slate-900">Akses Cepat</h3>
                        <p class="text-sm text-slate-500 mt-2">Baca buku favoritmu dalam hitungan detik.</p>
                    </div>
                </div>
                <div class="transform -translate-y-4">
                    <!-- Featured Book "Mockup" -->
                    <div class="relative bg-secondary p-8 rounded-[2rem] shadow-2xl text-center border-4 border-white">
                        <div class="absolute inset-0 bg-gradient-to-tr from-primary/20 to-transparent rounded-[2rem]">
                        </div>
                        <span
                            class="inline-block px-3 py-1 bg-white/20 backdrop-blur text-white text-[10px] uppercase font-bold tracking-widest rounded-full mb-6">Featured</span>
                        <div
                            class="w-32 h-48 bg-white mx-auto shadow-2xl rounded-r-lg rounded-l-sm mb-6 flex items-center justify-center relative overflow-hidden">
                            <div
                                class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-r from-black/20 to-transparent">
                            </div>
                            <span
                                class="font-serif font-bold text-slate-300 text-4xl opacity-20 transform -rotate-90">BOOK</span>
                        </div>
                        <h3 class="text-white font-serif font-bold text-xl mb-1">Modern Design</h3>
                        <p class="text-slate-400 text-sm">Best Seller 2024</p>

                        <div class="mt-6 flex justify-center">
                            <button
                                class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center hover:scale-110 transition-transform shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-0l0-1.414-0 011.414-1.414 7-7 0 010-0zM10 18a1 1 0 000-0V2a1 1 0 010 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="mb-32">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div
            class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300 group">
            <div
                class="w-14 h-14 bg-blue-50 text-secondary rounded-2xl flex items-center justify-center mb-6 border border-blue-100 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h3 class="text-xl font-serif font-bold text-slate-900 mb-3">Literasi Tanpa Batas</h3>
            <p class="text-slate-500 leading-relaxed font-sans">Perpustakaan digital yang bisa Anda bawa kemana saja.
                Membaca jadi lebih mudah.</p>
        </div>

        <div
            class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300 group relative overflow-hidden">
            <div
                class="absolute top-0 right-0 w-24 h-24 bg-primary/5 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2">
            </div>
            <div
                class="w-14 h-14 bg-amber-50 text-primary rounded-2xl flex items-center justify-center mb-6 border border-amber-100 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-serif font-bold text-slate-900 mb-3">Transaksi Instan</h3>
            <p class="text-slate-500 leading-relaxed font-sans">Sistem token yang aman dan cepat. Tidak perlu menunggu
                konfirmasi lama.</p>
        </div>

        <div
            class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300 group">
            <div
                class="w-14 h-14 bg-green-50 text-green-700 rounded-2xl flex items-center justify-center mb-6 border border-green-100 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            <h3 class="text-xl font-serif font-bold text-slate-900 mb-3">Kualitas Premium</h3>
            <p class="text-slate-500 leading-relaxed font-sans">Hanya buku berkualitas tinggi dengan format yang nyaman
                dibaca di semua perangkat.</p>
        </div>
    </div>
</section>

<!-- Curated Collections / Best Sellers -->
<section class="mb-32">
    <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
        <div>
            <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block font-sans">Koleksi
                Terpopuler</span>
            <h2 class="text-4xl font-serif font-bold text-slate-900">Buku Pilihan Bulan Ini</h2>
        </div>
        <a href="index.php?page=catalog"
            class="group flex items-center gap-2 text-slate-600 font-bold hover:text-primary transition-colors py-2 border-b-2 border-transparent hover:border-primary">
            Lihat Semua Koleksi
            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <?php foreach ($best_sellers as $book): ?>
            <div class="group">
                <!-- Book Card -->
                <div
                    class="relative bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-book-hover transition-all duration-300">
                    <div
                        class="relative aspect-[3/4] rounded-2xl overflow-hidden mb-6 shadow-book group-hover:shadow-xl transition-all">
                        <img src="assets/images/<?= htmlspecialchars($book['image']) ?>"
                            class="object-cover w-full h-full transform transition-transform duration-700 group-hover:scale-105"
                            alt="<?= htmlspecialchars($book['title']) ?>">

                        <!-- Overlay Action -->
                        <div
                            class="absolute inset-0 bg-secondary/80 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center p-6">
                            <a href="index.php?page=detail&id=<?= $book['id'] ?>"
                                class="px-6 py-3 bg-white text-secondary font-bold rounded-xl shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 hover:bg-primary hover:text-white">
                                Lihat Detail
                            </a>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-start gap-2">
                            <h3
                                class="font-serif font-bold text-xl text-slate-900 leading-tight group-hover:text-primary transition-colors line-clamp-2">
                                <a
                                    href="index.php?page=detail&id=<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></a>
                            </h3>
                            <span class="flex-shrink-0 bg-green-50 text-green-700 text-xs font-bold px-2 py-1 rounded-lg">
                                <?= number_format($book['price']) ?>
                            </span>
                        </div>
                        <p class="text-slate-500 text-sm italic"><?= htmlspecialchars($book['author']) ?></p>

                        <div class="pt-4 border-t border-slate-50 flex items-center justify-between text-sm text-slate-400">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-400" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="text-slate-700 font-bold">
                                    <?= number_format(getBookRating($pdo, $book['id']), 1) ?>
                                </span>
                            </div>
                            <span>Terjual 100+</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Final CTA -->
<section class="relative bg-secondary rounded-[3rem] overflow-hidden mb-12">
    <div class="absolute inset-0">
        <div
            class="absolute top-0 right-0 w-[600px] h-[600px] bg-primary/20 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/2">
        </div>
        <div
            class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-accent/10 rounded-full blur-[80px] translate-y-1/2 -translate-x-1/4">
        </div>
        <!-- Texture Pattern -->
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
        </div>
    </div>

    <div class="relative z-10 px-8 py-20 text-center max-w-4xl mx-auto">
        <h2 class="text-4xl md:text-5xl font-serif font-bold text-white mb-8 leading-tight">
            Bagian dari Komunitas Pembaca Cerdas.
        </h2>
        <p class="text-slate-300 text-lg mb-12 leading-relaxed">
            Bergabunglah dengan ribuan pembaca lainnya. Nikmati akses eksklusif ke buku-buku terbaik dengan harga yang
            adil bagi penulis dan pembaca.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="index.php?page=catalog"
                class="px-10 py-4 bg-white text-secondary font-bold rounded-xl hover:bg-paper transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 flex items-center justify-center gap-2">
                Jelajahi Katalog
            </a>
            <a href="#"
                class="px-10 py-4 bg-transparent border border-white/20 text-white font-bold rounded-xl hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                Pelajari Lebih Lanjut
            </a>
        </div>
    </div>
</section>