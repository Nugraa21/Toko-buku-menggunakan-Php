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
<section class="relative bg-slate-900 rounded-[2.5rem] overflow-hidden shadow-2xl mb-24 min-h-[600px] flex items-center">
    <!-- Abstract Background -->
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-primary/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] opacity-30"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="text-center lg:text-left pt-10 lg:pt-0">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/10 text-amber-400 font-medium text-sm mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                </svg>
                <span>Platform Buku Digital #1 Indonesia</span>
            </div>
            
            <h1 class="text-5xl lg:text-7xl font-extrabold text-white tracking-tight leading-tight mb-8">
                Jelajahi Dunia <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">Tanpa Batas.</span>
            </h1>
            
            <p class="text-lg text-slate-400 mb-10 leading-relaxed max-w-xl mx-auto lg:mx-0">
                Akses ribuan buku premium secara instan. Tingkatkan wawasan dan imajinasi Anda dengan perpustakaan digital tercanggih.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                <a href="index.php?page=catalog" class="px-8 py-4 bg-primary hover:bg-amber-600 text-white font-bold rounded-2xl shadow-lg shadow-amber-500/20 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2">
                    <span>Mulai Membaca</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-0l0-1.414-0 011.414-1.414 7-7 0 010-0zM10 18a1 1 0 000-0V2a1 1 0 010 0z" /> <!-- Arrow icon approximation -->
                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </a>
                <?php if (!isLoggedIn()): ?>
                        <a href="index.php?page=register" class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white border border-white/10 font-bold rounded-2xl backdrop-blur-md transition-all flex items-center justify-center gap-2">
                            Daftar Akun
                        </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- 3D Book Presentation / Illustration Replacement -->
        <div class="relative hidden lg:block">
             <div class="relative w-full aspect-square max-w-lg mx-auto perspective-1000">
                <!-- Floating Icons Composition -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-gradient-to-tr from-amber-500/20 to-purple-500/20 rounded-full blur-[100px]"></div>
                
                <div class="grid grid-cols-2 gap-6 rotate-[-10deg] hover:rotate-0 transition-transform duration-700 ease-out">
                    <div class="bg-white p-6 rounded-3xl shadow-2xl flex flex-col items-center gap-4 transform translate-y-12">
                         <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                         </div>
                         <div class="h-2 w-24 bg-slate-100 rounded-full"></div>
                         <div class="h-2 w-16 bg-slate-100 rounded-full"></div>
                    </div>
                    <div class="bg-gradient-to-br from-primary to-amber-600 p-6 rounded-3xl shadow-2xl flex flex-col items-center gap-4 text-white transform -translate-y-8">
                         <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                         </div>
                         <div class="h-2 w-24 bg-white/30 rounded-full"></div>
                         <div class="h-2 w-16 bg-white/30 rounded-full"></div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-2xl flex flex-col items-center gap-4 col-span-2 mx-12">
                         <div class="w-full flex justify-between items-center px-4">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 ml-4">
                                <div class="h-2 w-full bg-slate-100 rounded-full mb-2"></div>
                                <div class="h-2 w-2/3 bg-slate-100 rounded-full"></div>
                            </div>
                         </div>
                    </div>
                </div>
             </div>
        </div>
    </div>
</section>

<!-- Features Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-24 px-4">
    <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 hover:-translate-y-2 transition-transform duration-300 group">
        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">Akses Instan</h3>
        <p class="text-slate-500 leading-relaxed">Beli dan langsung baca detik itu juga. Perpustakaan digital di genggaman Anda.</p>
    </div>
    
    <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 hover:-translate-y-2 transition-transform duration-300 group">
        <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">Transaksi Aman</h3>
        <p class="text-slate-500 leading-relaxed">Sistem token terenkripsi menjamin keamanan dan kemudahan setiap transaksi.</p>
    </div>
    
    <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 hover:-translate-y-2 transition-transform duration-300 group">
        <div class="w-16 h-16 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-green-600 group-hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">Multi Perangkat</h3>
        <p class="text-slate-500 leading-relaxed">Sinkronisasi otomatis antar perangkat. Lanjutkan membaca di mana saja.</p>
    </div>
</div>

<!-- Best Sellers Section -->
<section class="mb-24">
    <div class="flex justify-between items-end mb-12">
        <div>
            <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block">Sedang Tren</span>
            <h2 class="text-4xl font-extrabold text-slate-900">Paling Diminati</h2>
        </div>
        <a href="index.php?page=catalog" class="group flex items-center gap-2 text-slate-600 font-bold hover:text-primary transition-colors">
            Lihat Semua 
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <?php foreach ($best_sellers as $book): ?>
                <div class="group relative">
                    <div class="relative aspect-[4/5] bg-slate-100 rounded-3xl overflow-hidden mb-6 shadow-lg group-hover:shadow-2xl transition-all duration-500">
                        <img src="assets/images/<?= htmlspecialchars($book['image']) ?>" class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110" alt="<?= htmlspecialchars($book['title']) ?>">
                    
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-8">
                            <a href="index.php?page=detail&id=<?= $book['id'] ?>" class="w-full py-4 bg-white text-slate-900 font-bold rounded-xl text-center hover:bg-primary hover:text-white transition-colors shadow-lg">
                                Lihat Detail
                            </a>
                        </div>
                    
                        <span class="absolute top-4 left-4 bg-amber-500/90 backdrop-blur text-white text-[10px] font-bold px-3 py-1.5 rounded-full uppercase tracking-wider shadow-sm">
                            Best Seller
                        </span>
                    </div>
                
                    <h3 class="font-bold text-xl text-slate-900 mb-1 leading-tight group-hover:text-primary transition-colors cursor-pointer">
                        <a href="index.php?page=detail&id=<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></a>
                    </h3>
                    <p class="text-slate-500 text-sm mb-3"><?= htmlspecialchars($book['author']) ?></p>
                
                    <div class="flex items-center gap-2">
                        <div class="bg-amber-50 p-1.5 rounded-lg">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                               <path d="M10.7 2.1a.5.5 0 011 .1l1.4 3.4a1 1 0 00.9.6l3.5.4a.5.5 0 01.3.9l-2.6 2.4a1 1 0 00-.2 1l.7 3.5a.5.5 0 01-.7.5l-3.2-1.8a1 1 0 00-1 0L6 14.8a.5.5 0 01-.7-.5l.7-3.5a1 1 0 00-.2-1L3.1 7.5a.5.5 0 01.3-.9l3.5-.4a1 1 0 00.9-.6L9.7 2.2a.5.5 0 011 0z" />
                           </svg>
                        </div>
                        <span class="font-extrabold text-slate-900 text-lg"><?= number_format($book['price']) ?></span>
                    </div>
                </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Call to Action -->
<section class="relative bg-slate-900 rounded-[3rem] p-12 md:p-24 text-center overflow-hidden mb-12">
    <!-- Decor -->
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-gradient-to-b from-primary/20 to-transparent rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>

    <div class="relative z-10 max-w-3xl mx-auto">
        <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-8 leading-tight">Mulai Petualangan Literasi Anda Hari Ini.</h2>
        <p class="text-slate-400 text-lg mb-12">Buka akses ke ribuan judul buku terbaik. Ilmu pengetahuan dan hiburan kini hanya sejauh satu klik.</p>
        <a href="index.php?page=catalog" class="inline-flex items-center gap-3 px-10 py-5 bg-white text-slate-900 font-bold rounded-2xl shadow-xl hover:bg-amber-50 transition-all transform hover:-translate-y-1 text-lg group">
            <span>Jelajahi Katalog</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </a>
    </div>
</section>