<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore Token</title>
    <!-- Tailwind CSS (via CDN for simplicity) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f59e0b', // Amber 500
                        secondary: '#0f172a', // Slate 900
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <?php
    $current_tokens = 0;
    if (isLoggedIn() && isset($pdo)) {
        $current_tokens = getUserTokenBalance($pdo, $_SESSION['user_id']);
    }
    ?>

    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 glass-nav border-b border-slate-200 shadow-sm transition-all duration-300"
        id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="index.php" class="flex items-center gap-2 group">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-primary to-amber-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg group-hover:shadow-amber-500/30 transition-all transform group-hover:scale-110 duration-300">
                            B
                        </div>
                        <span
                            class="text-xl font-bold text-slate-900 tracking-tight group-hover:text-amber-600 transition-colors">BookStore</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div
                    class="hidden md:flex space-x-1 items-center bg-slate-100/50 p-1.5 rounded-full border border-slate-200/50">
                    <a href="index.php"
                        class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-300 <?= (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'bg-white text-primary shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50' ?>">Beranda</a>

                    <?php if (isAdmin()): ?>
                        <a href="index.php?page=admin"
                            class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-300 <?= ($_GET['page'] ?? '') == 'admin' ? 'bg-white text-primary shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50' ?>">Dashboard</a>
                        <a href="index.php?page=admin_transactions"
                            class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-300 <?= ($_GET['page'] ?? '') == 'admin_transactions' ? 'bg-white text-primary shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50' ?>">Penjualan</a>
                    <?php else: ?>
                        <a href="index.php?page=catalog"
                            class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-300 <?= ($_GET['page'] ?? '') == 'catalog' ? 'bg-white text-primary shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50' ?>">Katalog</a>
                        <?php if (isLoggedIn()): ?>
                            <a href="index.php?page=library"
                                class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-300 <?= ($_GET['page'] ?? '') == 'library' ? 'bg-white text-primary shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50' ?>">Pustakaku</a>
                            <a href="index.php?page=history"
                                class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-300 <?= ($_GET['page'] ?? '') == 'history' ? 'bg-white text-primary shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50' ?>">Riwayat</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Right Side (Cart, Auth, Mobile Toggle) -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <!-- Cart (Mobile & Desktop) -->
                    <?php if (!isAdmin()): ?>
                        <a href="index.php?page=cart"
                            class="relative group p-2.5 rounded-full text-slate-500 hover:bg-slate-100 hover:text-primary transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <?php if (getCartCount() > 0): ?>
                                <span
                                    class="absolute top-0 right-0 bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm ring-2 ring-white">
                                    <?= getCartCount() ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>

                    <?php if (isLoggedIn()): ?>
                        <!-- User Token / Admin Revenue -->
                        <div class="hidden sm:flex items-center gap-4 pl-4 border-l border-slate-200">
                            <div class="flex flex-col items-end">
                                <?php if (isAdmin()): ?>
                                    <?php $revenue = $pdo->query("SELECT SUM(total_tokens) FROM transactions")->fetchColumn() ?: 0; ?>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pendapatan</span>
                                    <div class="flex items-center gap-1 text-green-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a1 1 0 100-2 1 1 0 000 2z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-extrabold"><?= number_format($revenue) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Saldo</span>
                                    <a href="index.php?page=topup"
                                        class="flex items-center gap-1 text-primary hover:text-amber-600 transition-colors group">
                                        <div class="p-0.5 bg-amber-100 rounded-full group-hover:bg-amber-200 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-amber-600"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-extrabold"><?= number_format($current_tokens) ?></span>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- Profile Dropdown (Desktop) -->
                            <div class="relative group">
                                <a href="<?= isAdmin() ? 'index.php?page=admin' : 'index.php?page=profile' ?>">
                                    <div
                                        class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold border-2 border-white ring-1 ring-slate-200 shadow-sm cursor-pointer hover:ring-primary transition-all">
                                        <?= substr($_SESSION['user_name'] ?? 'U', 0, 1) ?>
                                    </div>
                                </a>
                                <div
                                    class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all transform translate-y-2 group-hover:translate-y-0 p-2 z-50">
                                    <div class="px-3 py-2 border-b border-slate-50 mb-1">
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hi, There!</p>
                                        <p class="text-sm font-bold text-slate-800 truncate">
                                            <?= $_SESSION['user_name'] ?? 'Guest' ?>
                                        </p>
                                    </div>
                                    <div class="py-1 space-y-1">
                                        <?php if (isAdmin()): ?>
                                            <a href="index.php?page=admin"
                                                class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors">
                                                <span>📊</span> Dashboard
                                            </a>
                                        <?php else: ?>
                                            <a href="index.php?page=profile"
                                                class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors">
                                                <span>👤</span> Profil Saya
                                            </a>
                                            <a href="index.php?page=library"
                                                class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors">
                                                <span>📚</span> Pustakaku
                                            </a>
                                        <?php endif; ?>
                                        <a href="index.php?page=logout"
                                            class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                            <span>🚪</span> Keluar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Hamburger Button -->
                        <button id="mobile-menu-btn"
                            class="md:hidden p-2 text-slate-600 rounded-lg hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                        </button>
                    <?php else: ?>
                        <div class="hidden sm:flex items-center gap-2">
                            <a href="index.php?page=login"
                                class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-primary transition-colors">Masuk</a>
                            <a href="index.php?page=register"
                                class="px-5 py-2.5 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-800 shadow-lg transition-all hover:-translate-y-0.5">Daftar</a>
                        </div>
                        <!-- Mobile Hamburger Button (Logged Out) -->
                        <button id="mobile-menu-btn"
                            class="md:hidden p-2 text-slate-600 rounded-lg hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-primary">
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
            class="hidden md:hidden fixed top-20 left-0 w-full h-[calc(100vh-5rem)] bg-white/95 backdrop-blur-xl border-t border-slate-200/50 shadow-2xl z-40 overflow-y-auto transition-all duration-300 ease-in-out">
            <div class="px-6 py-8 space-y-6 pb-20">
                <div class="space-y-2">
                    <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Menu Utama</p>
                    <a href="index.php"
                        class="block px-4 py-3 rounded-2xl text-lg font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Beranda
                    </a>

                    <?php if (isAdmin()): ?>
                        <a href="index.php?page=admin"
                            class="block px-4 py-3 rounded-2xl text-lg font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="index.php?page=admin_transactions"
                            class="block px-4 py-3 rounded-2xl text-lg font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 3.666A5.002 5.002 0 0115 17m7-10h2a2 2 0 002-2V4a2 2 0 00-2-2h-2a2 2 0 00-2 2v1m-6 0h6" />
                            </svg>
                            Penjualan
                        </a>
                    <?php else: ?>
                        <a href="index.php?page=catalog"
                            class="block px-4 py-3 rounded-2xl text-lg font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Katalog
                        </a>
                        <?php if (isLoggedIn()): ?>
                            <a href="index.php?page=library"
                                class="block px-4 py-3 rounded-2xl text-lg font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                </svg>
                                Pustakaku
                            </a>
                            <a href="index.php?page=history"
                                class="block px-4 py-3 rounded-2xl text-lg font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Riwayat
                            </a>

                            <!-- Mobile Balance -->
                            <div
                                class="mx-4 mt-6 p-5 bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl text-white shadow-xl">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-slate-400 text-sm font-medium">Saldo Token</span>
                                    <div class="h-8 w-8 bg-white/10 rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-400"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-3xl font-extrabold mb-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-400" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <?= number_format($current_tokens) ?>
                                </div>
                                <a href="index.php?page=topup"
                                    class="block w-full py-3 bg-primary text-white text-center font-bold rounded-xl hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/20">
                                    Top Up Token
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="border-t border-slate-100/50 pt-6">
                    <?php if (isLoggedIn()): ?>
                        <div class="flex items-center px-4 mb-6 gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-slate-100 border-2 border-white shadow-md flex items-center justify-center text-slate-700 font-bold text-lg">
                                <?= substr($_SESSION['user_name'] ?? 'U', 0, 1) ?>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-slate-800"><?= $_SESSION['user_name'] ?></div>
                                <div class="text-xs text-slate-400 font-medium">Logged In</div>
                            </div>
                        </div>
                        <a href="<?= isAdmin() ? 'index.php?page=admin' : 'index.php?page=profile' ?>"
                            class="block px-4 py-3 rounded-2xl text-lg font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profil Saya
                        </a>
                        <a href="index.php?page=logout"
                            class="block px-4 py-3 rounded-2xl text-lg font-bold text-rose-600 hover:bg-rose-50 transition-colors flex items-center gap-3 mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Keluar
                        </a>
                    <?php else: ?>
                        <div class="grid grid-cols-1 gap-4 px-4">
                            <a href="index.php?page=login"
                                class="block w-full text-center py-4 bg-slate-100 text-slate-700 font-bold rounded-2xl hover:bg-slate-200 transition-colors">Masuk</a>
                            <a href="index.php?page=register"
                                class="block w-full text-center py-4 bg-primary text-white font-bold rounded-2xl shadow-lg hover:shadow-xl hover:bg-amber-600 transition-all">Daftar
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
                    <span class="text-xl"><?= $_SESSION['flash_type'] == 'success' ? '✅' : '⚠️' ?></span>
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