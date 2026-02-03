<?php
// Main Router
require_once 'includes/functions.php';

$page = $_GET['page'] ?? 'home';
$allowed_pages = ['home', 'login', 'register', 'cart', 'cart_action', 'checkout', 'history', 'logout', 'admin', 'admin_action', 'topup'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

require_once 'includes/header.php';

// Include the page content
$pageFile = "pages/$page.php";
if (file_exists($pageFile)) {
    require_once $pageFile;
} else {
    echo "<div class='container'><h2 style='text-align:center; margin-top:50px;'>404 - Halaman tidak ditemukan</h2></div>";
}

require_once 'includes/footer.php';
?>