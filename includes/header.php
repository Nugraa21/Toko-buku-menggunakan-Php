<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore - Jelajahi Dunia Literasi</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                    },
                    colors: {
                        primary: '#b45309', // Amber 700
                        secondary: '#1e293b', // Slate 800
                        accent: '#f59e0b', // Amber 500
                        paper: '#fdfbf7', // Warm Paper White
                    },
                    boxShadow: {
                        'book': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 4px 0 8px rgba(0,0,0,0.05)',
                        'glossy': '0 0 20px rgba(255,255,255,0.5) inset, 0 4px 6px rgba(0,0,0,0.1)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fdfbf7;
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 24px 24px;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Playfair Display', serif;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Glass Effects */
        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .nav-link-active {
            background-color: #b45309;
            color: white;
            box-shadow: 0 4px 12px rgba(180, 83, 9, 0.2);
        }
    </style>
</head>

<body class="text-slate-800 antialiased min-h-screen flex flex-col overflow-x-hidden">

    <?php
    $current_tokens = 0;
    if (isLoggedIn() && isset($pdo)) {
        $current_tokens = getUserTokenBalance($pdo, $_SESSION['user_id']);
    }
    $page = $_GET['page'] ?? 'home';
    ?>

    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-[60] transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">

                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center z-50">
                    <a href="index.php" class="flex items-center gap-3 group">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-primary to-amber-600 text-white rounded-xl flex items-center justify-center font-serif font-bold text-xl shadow-lg shadow-primary/30 group-hover:scale-105 transition-transform duration-300">
                            B.
                        </div>
                        <span
                            class="text-2xl font-serif font-bold text-slate-900 tracking-tight group-hover:text-primary transition-colors">BookStore</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div
                    class="hidden md:flex space-x-1 items-center bg-white/60 p-1.5 rounded-full border border-white/50 backdrop-blur-md shadow-sm">
                    <a href="index.php"
                        class="px-5 py-2 rounded-full text-sm font-bold transition-all duration-300 <?= ($page == 'home') ? 'nav-link-active' : 'text-slate-600 hover:text-primary hover:bg-white' ?>">Beranda</a>

                    <?php if (isAdmin()): ?>
                        <a href="index.php?page=admin"
                            class="px-5 py-2 rounded-full text-sm font-bold transition-all duration-300 <?= ($page == 'admin') ? 'nav-link-active' : 'text-slate-600 hover:text-primary hover:bg-white' ?>">Dashboard</a>
                        <a href="index.php?page=admin_transactions"
                            class="px-5 py-2 rounded-full text-sm font-bold transition-all duration-300 <?= ($page == 'admin_transactions') ? 'nav-link-active' : 'text-slate-600 hover:text-primary hover:bg-white' ?>">Penjualan</a>
                    <?php else: ?>
                        <a href="index.php?page=catalog"
                            class="px-5 py-2 rounded-full text-sm font-bold transition-all duration-300 <?= ($page == 'catalog' || $page == 'detail') ? 'nav-link-active' : 'text-slate-600 hover:text-primary hover:bg-white' ?>">Katalog</a>
                        <?php if (isLoggedIn()): ?>
                            <a href="index.php?page=library"
                                class="px-5 py-2 rounded-full text-sm font-bold transition-all duration-300 <?= ($page == 'library') ? 'nav-link-active' : 'text-slate-600 hover:text-primary hover:bg-white' ?>">Pustakaku</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-3 md:gap-5 z-50">

                    <!-- Search Icon (Optional Placeholder) -->
                    <button
                        class="p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-colors hidden sm:block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                    <!-- Cart -->
                    <?php if (!isAdmin()): ?>
                        <!-- Wishlist -->
                        <?php if (isLoggedIn()): ?>
                            <a href="index.php?page=wishlist"
                                class="relative group p-2 rounded-full text-slate-600 hover:bg-rose-50 hover:text-rose-500 transition-all hidden sm:block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </a>
                        <?php endif; ?>

                        <a href="index.php?page=cart"
                            class="relative group p-2 rounded-full text-slate-600 hover:bg-primary/10 hover:text-primary transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <?php if (getCartCount() > 0): ?>
                                <span
                                    class="absolute top-1 right-0 bg-rose-500 text-white text-[10px] font-bold h-4 w-4 flex items-center justify-center rounded-full shadow-md ring-2 ring-white">
                                    <?= getCartCount() ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>

                    <?php if (isLoggedIn()): ?>
                        <!-- User Profile Dropdown -->
                        <div class="relative group hidden md:block">
                            <button class="flex items-center gap-2 focus:outline-none">
                                <div
                                    class="w-10 h-10 rounded-full bg-slate-100 border-2 border-white shadow-md flex items-center justify-center text-primary font-bold font-serif overflow-hidden">
                                    <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all transform translate-y-2 group-hover:translate-y-0 p-2 z-50 origin-top-right">
                                <div class="px-4 py-3 border-b border-slate-100 mb-2 bg-slate-50/50 rounded-xl">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Signed in as
                                    </p>
                                    <p class="text-sm font-bold text-slate-900 truncate font-serif">
                                        <?= $_SESSION['user_name'] ?>
                                    </p>
                                    <?php if (!isAdmin()): ?>
                                        <div
                                            class="mt-2 flex items-center gap-2 text-primary text-xs font-bold bg-white px-2 py-1 rounded-lg border border-primary/10 inline-flex">
                                            <span>🪙</span> <?= number_format($current_tokens) ?> Token
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <a href="<?= isAdmin() ? 'index.php?page=admin' : 'index.php?page=profile' ?>"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-50" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profil Saya
                                </a>
                                <?php if (!isAdmin()): ?>
                                    <a href="index.php?page=topup"
                                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-50" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Top Up Token
                                    </a>
                                <?php endif; ?>
                                <div class="border-t border-slate-100 my-1"></div>
                                <a href="index.php?page=logout"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-rose-600 hover:bg-rose-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-50" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="hidden md:flex items-center gap-3">
                            <a href="index.php?page=login"
                                class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-primary transition-colors">Masuk</a>
                            <a href="index.php?page=register"
                                class="px-6 py-2.5 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-primary shadow-lg shadow-slate-900/20 transition-all hover:-translate-y-0.5">Daftar</a>
                        </div>
                    <?php endif; ?>

                    <!-- Mobile Hamburger -->
                    <button id="mobile-menu-btn"
                        class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors group">
                        <div class="w-6 flex flex-col items-end gap-1.5 ">
                            <span class="block w-6 h-0.5 bg-current rounded-full transition-all group-hover:w-6"></span>
                            <span class="block w-4 h-0.5 bg-current rounded-full transition-all group-hover:w-6"></span>
                            <span class="block w-5 h-0.5 bg-current rounded-full transition-all group-hover:w-6"></span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Container (Outside Nav for better Z-Indexing) -->
    <!-- Overlay -->
    <div id="mobile-menu-overlay"
        class="fixed inset-0 z-[70] bg-slate-900/40 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 md:hidden">
    </div>

    <!-- Slide-in Panel -->
    <div id="mobile-menu"
        class="fixed inset-y-0 right-0 z-[80] w-[85%] max-w-sm bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out md:hidden flex flex-col border-l border-slate-100">

        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div
                    class="w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center font-serif font-bold text-lg">
                    B.</div>
                <span class="font-serif font-bold text-xl text-slate-900">Menu</span>
            </div>
            <button id="close-menu-btn"
                class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-full transition-all transform hover:rotate-90">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="space-y-6">
                <!-- Navigation -->
                <div class="space-y-2">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 px-2">Navigasi Utama</p>

                    <a href="index.php"
                        class="block px-4 py-3 rounded-xl text-base font-bold flex items-center gap-3 transition-colors <?= $page == 'home' ? 'bg-primary/10 text-primary' : 'text-slate-600 hover:bg-slate-50' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-70" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Beranda
                    </a>

                    <?php if (isAdmin()): ?>
                        <a href="index.php?page=admin"
                            class="block px-4 py-3 rounded-xl text-base font-bold flex items-center gap-3 transition-colors <?= $page == 'admin' ? 'bg-primary/10 text-primary' : 'text-slate-600 hover:bg-slate-50' ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-70" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Dashboard Admin
                        </a>
                        <a href="index.php?page=admin_transactions"
                            class="block px-4 py-3 rounded-xl text-base font-bold flex items-center gap-3 transition-colors <?= $page == 'admin_transactions' ? 'bg-primary/10 text-primary' : 'text-slate-600 hover:bg-slate-50' ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-70" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Laporan Penjualan
                        </a>
                    <?php else: ?>
                        <a href="index.php?page=catalog"
                            class="block px-4 py-3 rounded-xl text-base font-bold flex items-center gap-3 transition-colors <?= $page == 'catalog' ? 'bg-primary/10 text-primary' : 'text-slate-600 hover:bg-slate-50' ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-70" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Katalog Buku
                        </a>
                        <?php if (isLoggedIn()): ?>
                            <a href="index.php?page=wishlist"
                                class="block px-4 py-3 rounded-xl text-base font-bold flex items-center gap-3 transition-colors <?= $page == 'wishlist' ? 'bg-rose-50 text-rose-500' : 'text-slate-600 hover:bg-slate-50' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-70" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                Buku Disukai
                            </a>
                            <a href="index.php?page=library"
                                class="block px-4 py-3 rounded-xl text-base font-bold flex items-center gap-3 transition-colors <?= $page == 'library' ? 'bg-primary/10 text-primary' : 'text-slate-600 hover:bg-slate-50' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-70" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                                </svg>
                                Pustakaku
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- User Section -->
                <div class="pt-6 border-t border-slate-100">
                    <?php if (isLoggedIn()): ?>
                        <div class="flex items-center gap-4 px-4 mb-6">
                            <div
                                class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-primary font-bold text-lg font-serif">
                                <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div>
                                <div class="font-bold text-slate-900"><?= $_SESSION['user_name'] ?></div>
                                <?php if (!isAdmin()): ?>
                                    <div class="text-xs text-primary font-bold mt-0.5">Saldo:
                                        <?= number_format($current_tokens) ?> Token
                                    </div>
                                <?php else: ?>
                                    <div class="text-xs text-slate-500">Administrator</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 px-2">
                            <?php if (!isAdmin()): ?>
                                <a href="index.php?page=topup"
                                    class="py-3 text-center bg-primary/5 text-primary font-bold rounded-xl hover:bg-primary/10 text-sm">Top
                                    Up</a>
                            <?php endif; ?>
                            <a href="index.php?page=profile"
                                class="py-3 text-center bg-slate-50 text-slate-600 font-bold rounded-xl hover:bg-slate-100 text-sm">Profil</a>
                            <a href="index.php?page=logout"
                                class="col-span-2 py-3 text-center border border-rose-100 text-rose-600 font-bold rounded-xl hover:bg-rose-50 text-sm mt-2">Keluar</a>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 gap-4 px-2">
                            <a href="index.php?page=login"
                                class="block w-full text-center py-3.5 bg-slate-50 text-slate-700 font-bold rounded-xl hover:bg-slate-100">Masuk</a>
                            <a href="index.php?page=register"
                                class="block w-full text-center py-3.5 bg-slate-900 text-white font-bold rounded-xl shadow-lg hover:bg-primary">Daftar
                                Sekarang</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Spacer/Wrapper (Opened here, closed in footer.php) -->
    <div class="pt-32 min-h-[85vh] w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex-grow">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="mb-8 animate-fade-in-down w-full max-w-2xl mx-auto">
                <div
                    class="<?= $_SESSION['flash_type'] == 'success' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' ?> border rounded-2xl px-6 py-4 shadow-sm flex items-start gap-4">
                    <div class="flex-shrink-0 mt-0.5">
                        <?php if ($_SESSION['flash_type'] == 'success'): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="font-bold text-lg mb-1">
                            <?= $_SESSION['flash_type'] == 'success' ? 'Berhasil' : 'Perhatian' ?>
                        </p>
                        <p class="text-sm opacity-90"><?= $_SESSION['flash_message'];
                        unset($_SESSION['flash_message']); ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Global Scripts -->
        <script>
                // Use IIFE to avoid global scope pollution
                (function () {
                    const navbar = document.getElementById('navbar');
                    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
                    const closeMenuBtn = document.getElementById('close-menu-btn');
                    const mobileMenu = document.getElementById('mobile-menu');
                    const overlay = document.getElementById('mobile-menu-overlay');

                    // Navbar Scroll Effect
                    window.addEventListener('scroll', () => {
                        if (window.scrollY > 10) {
                            navbar.classList.add('glass-nav', 'shadow-sm');
                            navbar.classList.remove('bg-transparent');
                        } else {
                            navbar.classList.remove('glass-nav', 'shadow-sm');
                            navbar.classList.add('bg-transparent');
                        }
                    });

                    // Initial state check
                    if (window.scrollY > 10) {
                        navbar.classList.add('glass-nav', 'shadow-sm');
                    }

                    // Mobile Menu Logic
                    function openMenu() {
                        mobileMenu.classList.remove('translate-x-full');
                        overlay.classList.remove('opacity-0', 'pointer-events-none');
                        document.body.style.overflow = 'hidden';
                    }

                    function closeMenu() {
                        mobileMenu.classList.add('translate-x-full');
                        overlay.classList.add('opacity-0', 'pointer-events-none');
                        document.body.style.overflow = '';
                    }

                    if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openMenu);
                    if (closeMenuBtn) closeMenuBtn.addEventListener('click', closeMenu);
                    if (overlay) overlay.addEventListener('click', closeMenu);

                    // Close on resize to large screen
                    window.addEventListener('resize', () => {
                        if (window.innerWidth >= 768) closeMenu();
                    });
                })();
        </script>