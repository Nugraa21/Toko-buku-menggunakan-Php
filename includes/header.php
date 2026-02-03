<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore - Toko Buku Token</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: '#f59e0b', // Amber 500
                        secondary: '#1e293b', // Slate 800
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom scrollbar for better UX */
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
    </style>
</head>

<body>

    <?php
    $current_tokens = 0;
    if (isLoggedIn() && isset($pdo)) {
        $current_tokens = getUserTokenBalance($pdo, $_SESSION['user_id']);
    }
    ?>

    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <div
                        class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white font-bold text-lg">
                        B</div>
                    <a href="index.php" class="font-bold text-xl text-slate-800 tracking-tight">BookStore<span
                            class="text-primary">.</span></a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="index.php"
                        class="text-slate-600 hover:text-primary font-medium transition-colors">Beranda</a>

                    <?php if (isAdmin()): ?>
                        <!-- Admin Menu -->
                        <a href="index.php?page=admin"
                            class="text-slate-600 hover:text-primary font-medium transition-colors">Dashboard</a>
                        <a href="index.php?page=admin_transactions"
                            class="text-slate-600 hover:text-primary font-medium transition-colors">Penjualan</a>
                    <?php else: ?>
                        <!-- User Menu -->
                        <a href="index.php?page=catalog"
                            class="text-slate-600 hover:text-primary font-medium transition-colors">Katalog</a>
                        <?php if (isLoggedIn()): ?>
                            <a href="index.php?page=library"
                                class="text-slate-600 hover:text-primary font-medium transition-colors">Pustakaku</a>
                            <a href="index.php?page=history"
                                class="text-slate-600 hover:text-primary font-medium transition-colors">Riwayat</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Right Side (Cart & Auth) -->
                <div class="flex items-center gap-4">
                    <!-- Cart (Only for Users) -->
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
                        <!-- User Token / Admin Revenue & Profile -->
                        <div class="flex items-center gap-4 pl-4 border-l border-slate-200">
                            <div class="hidden sm:flex flex-col items-end">
                                <?php if (isAdmin()): ?>
                                    <?php
                                    // Calculate Revenue for display
                                    $revenue = $pdo->query("SELECT SUM(total_tokens) FROM transactions")->fetchColumn() ?: 0;
                                    ?>
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
                            <div class="relative group">
                                <a href="<?= isAdmin() ? 'index.php?page=admin' : 'index.php?page=profile' ?>">
                                    <div
                                        class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold border-2 border-white ring-1 ring-slate-200 shadow-sm cursor-pointer hover:ring-primary transition-all">
                                        <?= substr($_SESSION['user_name'] ?? 'U', 0, 1) ?>
                                    </div>
                                </a>
                                <!-- Simple Dropdown for Logout -->
                                <div
                                    class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all transform translate-y-2 group-hover:translate-y-0 p-2">
                                    <div class="px-3 py-2 border-b border-slate-50 mb-1">
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Logged in as
                                        </p>
                                        <p class="text-sm font-bold text-slate-800 truncate">
                                            <?= $_SESSION['user_name'] ?? 'Guest' ?></p>
                                    </div>
                                    <div class="py-1 space-y-1">
                                        <?php if (isAdmin()): ?>
                                            <a href="index.php?page=admin"
                                                class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                                </svg>
                                                Dashboard
                                            </a>
                                        <?php else: ?>
                                            <a href="index.php?page=profile"
                                                class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Profil Saya
                                            </a>
                                            <a href="index.php?page=library"
                                                class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                                Pustakaku
                                            </a>
                                        <?php endif; ?>
                                        <a href="index.php?page=logout"
                                            class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
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
                    <?php else: ?>
                        <div class="flex items-center gap-2">
                            <a href="index.php?page=login"
                                class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-primary transition-colors">Masuk</a>
                            <a href="index.php?page=register"
                                class="px-5 py-2.5 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-800 shadow-lg transition-all hover:-translate-y-0.5">Daftar</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-screen">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert <?= $_SESSION['flash_type'] == 'error' ? 'alert-error' : '' ?>">
                <?= $_SESSION['flash_message'];
                unset($_SESSION['flash_message']);
                unset($_SESSION['flash_type']); ?>
            </div>
        <?php endif; ?>