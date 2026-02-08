<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore Token - Jelajahi Dunia Literasi</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Playfair Display & Plus Jakarta Sans -->
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
                        primary: '#b45309', // Amber 700 (Warm Wood)
                        secondary: '#1e293b', // Slate 800 (Ink)
                        accent: '#f59e0b', // Amber 500 (Gold Leaf)
                        paper: '#fdfbf7', // Warm Paper White
                        surface: '#ffffff',
                    },
                    boxShadow: {
                        'book': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 4px 0 8px rgba(0,0,0,0.05)',
                        'book-hover': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04), 8px 0 16px rgba(0,0,0,0.05)',
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
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 5px;
            border: 2px solid #f1f5f9;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="text-slate-800 antialiased min-h-screen flex flex-col">

    <?php
    $current_tokens = 0;
    if (isLoggedIn() && isset($pdo)) {
        $current_tokens = getUserTokenBalance($pdo, $_SESSION['user_id']);
    }
    ?>

    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 glass-nav border-b border-primary/10 shadow-sm transition-all duration-300"
        id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="index.php" class="flex items-center gap-3 group">
                        <div
                            class="w-10 h-10 bg-primary text-white rounded-lg flex items-center justify-center font-serif font-bold text-xl shadow-lg shadow-primary/30 group-hover:scale-105 transition-transform duration-300">
                            B.
                        </div>
                        <span
                            class="text-2xl font-serif font-bold text-slate-900 tracking-tight group-hover:text-primary transition-colors">BookStore</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div
                    class="hidden md:flex space-x-1 items-center bg-white/50 p-1.5 rounded-full border border-primary/10 backdrop-blur-md">
                    <a href="index.php"
                        class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 <?= (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'bg-primary text-white shadow-md' : 'text-slate-600 hover:text-primary hover:bg-primary/5' ?>">Beranda</a>

                    <?php if (isAdmin()): ?>
                        <a href="index.php?page=admin"
                            class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 <?= ($_GET['page'] ?? '') == 'admin' ? 'bg-primary text-white shadow-md' : 'text-slate-600 hover:text-primary hover:bg-primary/5' ?>">Dashboard</a>
                        <a href="index.php?page=admin_transactions"
                            class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 <?= ($_GET['page'] ?? '') == 'admin_transactions' ? 'bg-primary text-white shadow-md' : 'text-slate-600 hover:text-primary hover:bg-primary/5' ?>">Penjualan</a>
                    <?php else: ?>
                        <a href="index.php?page=catalog"
                            class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 <?= ($_GET['page'] ?? '') == 'catalog' ? 'bg-primary text-white shadow-md' : 'text-slate-600 hover:text-primary hover:bg-primary/5' ?>">Katalog</a>
                        <?php if (isLoggedIn()): ?>
                            <a href="index.php?page=library"
                                class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 <?= ($_GET['page'] ?? '') == 'library' ? 'bg-primary text-white shadow-md' : 'text-slate-600 hover:text-primary hover:bg-primary/5' ?>">Pustakaku</a>
                            <a href="index.php?page=history"
                                class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 <?= ($_GET['page'] ?? '') == 'history' ? 'bg-primary text-white shadow-md' : 'text-slate-600 hover:text-primary hover:bg-primary/5' ?>">Riwayat</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Right Side (Cart, Auth, Mobile Toggle) -->
                <div class="flex items-center gap-3 sm:gap-5">
                    <!-- Cart (Mobile & Desktop) -->
                    <?php if (!isAdmin()): ?>
                        <a href="index.php?page=cart"
                            class="relative group p-2.5 rounded-full text-slate-500 hover:bg-primary/5 hover:text-primary transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <?php if (getCartCount() > 0): ?>
                                <span
                                    class="absolute top-1 right-1 bg-accent text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm ring-2 ring-white">
                                    <?= getCartCount() ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>

                    <?php if (isLoggedIn()): ?>
                        <!-- User Token / Admin Revenue -->
                        <div class="hidden sm:flex items-center gap-4 pl-4 border-l border-primary/10">
                            <div class="flex flex-col items-end">
                                <?php if (isAdmin()): ?>
                                    <?php $revenue = $pdo->query("SELECT SUM(total_tokens) FROM transactions")->fetchColumn() ?: 0; ?>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-sans">Pendapatan</span>
                                    <div class="flex items-center gap-1 text-green-700">
                                        <span class="text-sm font-bold font-serif"><?= number_format($revenue) ?> TKN</span>
                                    </div>
                                <?php else: ?>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-sans">Saldo</span>
                                    <a href="index.php?page=topup"
                                        class="flex items-center gap-1 text-primary hover:text-amber-700 transition-colors group">
                                        <div class="p-1 bg-accent/10 rounded-full group-hover:bg-accent/20 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-accent"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-bold font-serif"><?= number_format($current_tokens) ?></span>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- Profile Dropdown (Desktop) -->
                            <div class="relative group">
                                <a href="<?= isAdmin() ? 'index.php?page=admin' : 'index.php?page=profile' ?>">
                                    <div
                                        class="w-10 h-10 rounded-full bg-paper flex items-center justify-center text-primary font-serif font-bold border-2 border-white ring-1 ring-primary/20 shadow-sm cursor-pointer hover:ring-primary transition-all">
                                        <?= substr($_SESSION['user_name'] ?? 'U', 0, 1) ?>
                                    </div>
                                </a>
                                <div
                                    class="absolute right-0 mt-4 w-60 bg-white rounded-xl shadow-book border border-primary/10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all transform translate-y-2 group-hover:translate-y-0 p-2 z-50">
                                    <div class="px-4 py-3 border-b border-dashed border-slate-100 mb-1">
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Hi, There!</p>
                                        <p class="text-sm font-bold text-slate-800 truncate font-serif">
                                            <?= $_SESSION['user_name'] ?? 'Guest' ?>
                                        </p>
                                    </div>
                                    <div class="py-1 space-y-1">
                                        <?php if (isAdmin()): ?>
                                            <a href="index.php?page=admin"
                                                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-primary/5 hover:text-primary transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                                                </svg>
                                                Dashboard
                                            </a>
                                        <?php else: ?>
                                            <a href="index.php?page=profile"
                                                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-primary/5 hover:text-primary transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Profil Saya
                                            </a>
                                            <a href="index.php?page=library"
                                                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-primary/5 hover:text-primary transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                                Pustakaku
                                            </a>
                                        <?php endif; ?>
                                        <a href="index.php?page=logout"
                                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-rose-600 hover:bg-rose-50 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Keluar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Hamburger Button -->
                        <button id="mobile-menu-btn"
                            class="md:hidden p-2 text-slate-600 rounded-lg hover:bg-slate-100 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                        </button>
                    <?php else: ?>
                        <div class="hidden sm:flex items-center gap-3">
                            <a href="index.php?page=login"
                                class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-primary transition-colors">Masuk</a>
                            <a href="index.php?page=register"
                                class="px-6 py-2.5 text-sm font-bold text-white bg-slate-900 rounded-lg hover:bg-primary shadow-lg shadow-slate-900/20 transition-all hover:-translate-y-0.5">Daftar</a>
                        </div>
                        <!-- Mobile Hamburger Button (Logged Out) -->
                        <button id="mobile-menu-btn"
                            class="md:hidden p-2 text-slate-600 rounded-lg hover:bg-slate-100 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (Hidden by default) -->
        <div id="mobile-menu"
            class="hidden md:hidden fixed top-20 left-0 w-full h-[calc(100vh-5rem)] bg-white/95 backdrop-blur-xl border-t border-slate-100 shadow-2xl z-40 overflow-y-auto transition-all duration-300">
            <div class="px-6 py-8 space-y-6 pb-20">
                <div class="space-y-2">
                    <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 font-sans">Menu Utama
                    </p>
                    <a href="index.php"
                        class="block px-4 py-3 rounded-xl text-lg font-serif font-medium text-slate-600 hover:bg-primary/5 hover:text-primary transition-colors flex items-center gap-3">
                        Beranda
                    </a>

                    <?php if (isAdmin()): ?>
                        <a href="index.php?page=admin"
                            class="block px-4 py-3 rounded-xl text-lg font-serif font-medium text-slate-600 hover:bg-primary/5 hover:text-primary transition-colors flex items-center gap-3">
                            Dashboard
                        </a>
                        <a href="index.php?page=admin_transactions"
                            class="block px-4 py-3 rounded-xl text-lg font-serif font-medium text-slate-600 hover:bg-primary/5 hover:text-primary transition-colors flex items-center gap-3">
                            Penjualan
                        </a>
                    <?php else: ?>
                        <a href="index.php?page=catalog"
                            class="block px-4 py-3 rounded-xl text-lg font-serif font-medium text-slate-600 hover:bg-primary/5 hover:text-primary transition-colors flex items-center gap-3">
                            Katalog
                        </a>
                        <?php if (isLoggedIn()): ?>
                            <a href="index.php?page=library"
                                class="block px-4 py-3 rounded-xl text-lg font-serif font-medium text-slate-600 hover:bg-primary/5 hover:text-primary transition-colors flex items-center gap-3">
                                Pustakaku
                            </a>
                            <a href="index.php?page=history"
                                class="block px-4 py-3 rounded-xl text-lg font-serif font-medium text-slate-600 hover:bg-primary/5 hover:text-primary transition-colors flex items-center gap-3">
                                Riwayat
                            </a>

                            <!-- Mobile Balance -->
                            <div
                                class="mx-4 mt-6 p-6 bg-slate-900 rounded-2xl text-white shadow-xl relative overflow-hidden group">
                                <div
                                    class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2">
                                </div>
                                <div class="relative z-10">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-slate-400 text-sm font-medium">Saldo Token</span>
                                        <div class="h-8 w-8 bg-white/10 rounded-full flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-accent"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-3xl font-serif font-bold mb-5 flex items-center gap-2 text-white">
                                        <?= number_format($current_tokens) ?>
                                    </div>
                                    <a href="index.php?page=topup"
                                        class="block w-full py-3 bg-accent text-white text-center font-bold rounded-xl hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/20">
                                        Top Up Token
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="border-t border-slate-100 pt-6">
                    <?php if (isLoggedIn()): ?>
                        <div class="flex items-center px-4 mb-6 gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-paper border-2 border-white shadow-md flex items-center justify-center text-primary font-bold text-lg font-serif">
                                <?= substr($_SESSION['user_name'] ?? 'U', 0, 1) ?>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-slate-800 font-serif"><?= $_SESSION['user_name'] ?></div>
                                <div class="text-xs text-slate-400 font-medium">Logged In</div>
                            </div>
                        </div>
                        <a href="<?= isAdmin() ? 'index.php?page=admin' : 'index.php?page=profile' ?>"
                            class="block px-4 py-3 rounded-xl text-lg font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors flex items-center gap-3">
                            Profil Saya
                        </a>
                        <a href="index.php?page=logout"
                            class="block px-4 py-3 rounded-xl text-lg font-bold text-rose-600 hover:bg-rose-50 transition-colors flex items-center gap-3 mt-2">
                            Keluar
                        </a>
                    <?php else: ?>
                        <div class="grid grid-cols-1 gap-4 px-4">
                            <a href="index.php?page=login"
                                class="block w-full text-center py-4 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition-colors">Masuk</a>
                            <a href="index.php?page=register"
                                class="block w-full text-center py-4 bg-primary text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:bg-amber-700 transition-all">Daftar
                                Sekarang</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </nav>

    <!-- Content Wrapper with Spacer for Fixed Navbar -->
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-20 min-h-[85vh] flex-grow">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="mb-8 animate-fade-in-down">
                <div
                    class="<?= $_SESSION['flash_type'] == 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-rose-50 text-rose-700 border-rose-200' ?> border rounded-2xl px-6 py-4 shadow-sm flex items-center gap-3">
                    <span class="text-xl">
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
                    </span>
                    <span class="font-medium"><?= $_SESSION['flash_message'];
                    unset($_SESSION['flash_message']); ?></span>
                </div>
            </div>
        <?php endif; ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const mobileMenuBtn = document.getElementById('mobile-menu-btn');
                const mobileMenu = document.getElementById('mobile-menu');

                if (mobileMenuBtn && mobileMenu) {
                    mobileMenuBtn.addEventListener('click', function () {
                        mobileMenu.classList.toggle('hidden');
                    });
                }

                // Optional: Close mobile menu when clicking outside or resizing
                window.addEventListener('resize', function () {
                    if (window.innerWidth >= 768) { // md breakpoint
                        mobileMenu.classList.add('hidden');
                    }
                });
            });
        </script>
</body>

</html>