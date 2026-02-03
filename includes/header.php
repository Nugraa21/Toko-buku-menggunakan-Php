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
                    <a href="index.php?page=home#katalog"
                        class="text-slate-600 hover:text-primary font-medium transition-colors">Katalog</a>

                    <?php if (isLoggedIn()): ?>
                        <a href="index.php?page=history"
                            class="text-slate-600 hover:text-primary font-medium transition-colors">Riwayat</a>
                        <a href="index.php?page=topup"
                            class="text-slate-600 hover:text-primary font-medium transition-colors">Top Up</a>

                        <?php if (isAdmin()): ?>
                            <a href="index.php?page=admin"
                                class="text-rose-600 hover:text-rose-700 font-medium transition-colors">Admin Panel</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Right Side (Cart & Auth) -->
                <div class="flex items-center gap-4">
                    <!-- Cart -->
                    <a href="index.php?page=cart"
                        class="relative group p-2 rounded-full hover:bg-slate-100 transition-colors text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <?php if (getCartCount() > 0): ?>
                            <span
                                class="absolute -top-1 -right-1 bg-rose-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full shadow-sm">
                                <?= getCartCount() ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <?php if (isLoggedIn()): ?>
                        <!-- User Token & Profile -->
                        <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                            <div class="hidden sm:flex flex-col items-end">
                                <span class="text-xs text-slate-500">Saldo Token</span>
                                <span class="text-sm font-bold text-primary">🪙 <?= number_format($current_tokens) ?></span>
                            </div>
                            <div class="relative group">
                                <a href="index.php?page=profile">
                                    <div
                                        class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-700 font-bold border-2 border-white shadow-sm cursor-pointer hover:bg-slate-300 transition-colors">
                                        <?= substr($_SESSION['user_name'] ?? 'U', 0, 1) ?>
                                    </div>
                                </a>
                                <!-- Simple Dropdown for Logout -->
                                <div
                                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all transform translate-y-2 group-hover:translate-y-0">
                                    <div class="py-1">
                                        <a href="index.php?page=profile"
                                            class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Profile</a>
                                        <a href="index.php?page=logout"
                                            class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center gap-2">
                            <a href="index.php?page=login"
                                class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary transition-colors">Masuk</a>
                            <a href="index.php?page=register"
                                class="px-4 py-2 text-sm font-bold text-white bg-primary rounded-full hover:bg-amber-600 shadow-md transition-all hover:-translate-y-0.5">Daftar</a>
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