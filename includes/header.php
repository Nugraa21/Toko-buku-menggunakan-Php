<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore - Toko Buku Token</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <?php
    $current_tokens = 0;
    if (isLoggedIn() && isset($pdo)) {
        $current_tokens = getUserTokenBalance($pdo, $_SESSION['user_id']);
    }
    ?>

    <nav class="navbar glass">
        <div class="container nav-content">
            <a href="index.php" class="logo">BookStore.</a>

            <ul class="nav-links">
                <li><a href="index.php" class="nav-link">Beranda</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="index.php?page=topup" class="nav-link" style="color: #fbbf24;">🪙 Top Up</a></li>
                    <li><a href="index.php?page=history" class="nav-link">Riwayat</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="index.php?page=admin" class="nav-link">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="index.php?page=logout" class="nav-link">Logout</a></li>
                <?php else: ?>
                    <li><a href="index.php?page=login" class="nav-link">Login</a></li>
                    <li><a href="index.php?page=register" class="btn btn-primary">Daftar</a></li>
                <?php endif; ?>

                <li>
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <?php if (isLoggedIn()): ?>
                            <div
                                style="padding: 0.5rem 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 20px; font-size: 0.9rem;">
                                Saldo: <strong><?= number_format($current_tokens) ?></strong> Token
                            </div>
                        <?php endif; ?>
                        <a href="index.php?page=cart" class="nav-link cart-icon">
                            🛒
                            <?php if (getCartCount() > 0): ?>
                                <span class="cart-count"><?= getCartCount() ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container main-content">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert <?= $_SESSION['flash_type'] == 'error' ? 'alert-error' : '' ?>">
                <?= $_SESSION['flash_message'];
                unset($_SESSION['flash_message']);
                unset($_SESSION['flash_type']); ?>
            </div>
        <?php endif; ?>