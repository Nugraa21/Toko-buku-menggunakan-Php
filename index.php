<?php
ob_start(); // Start output buffering to prevent header errors

// Main Router
require_once 'includes/functions.php';

$page = $_GET['page'] ?? 'home';
$allowed_pages = ['home', 'login', 'register', 'cart', 'cart_action', 'checkout', 'history', 'logout', 'admin', 'admin_action', 'topup', 'profile', 'admin_users', 'admin_books', 'detail', 'admin_transactions'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// List of pages that are purely logic (actions) and should NOT have header/footer
$action_pages = ['cart_action', 'admin_action', 'logout', 'checkout'];

if (!in_array($page, $action_pages)) {
    require_once 'includes/header.php';
}

// Include the page content
$pageFile = "pages/$page.php";
if (file_exists($pageFile)) {
    require_once $pageFile;
} else {
    // Only show 404 if it's not an action page (though actions shouldn't fail ideally)
    if (!in_array($page, $action_pages)) {
        echo "<div class='flex flex-col items-center justify-center min-h-[50vh]'>
                <h2 class='text-4xl font-bold text-slate-800 mb-2'>404</h2>
                <p class='text-slate-500'>Halaman tidak ditemukan.</p>
                <a href='index.php' class='mt-4 px-6 py-2 bg-primary text-white rounded-full'>Kembali ke Beranda</a>
              </div>";
    }
}

if (!in_array($page, $action_pages)) {
    require_once 'includes/footer.php';
}

ob_end_flush(); // Flush the output
?>