<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url)
{
    header("Location: $url");
    exit();
}

function formatPrice($price)
{
    return "🪙 " . number_format($price, 0, ',', '.');
}

function getUserTokenBalance($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT tokens FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn() ?: 0;
}

// Add to cart
function addToCart($book_id, $quantity = 1)
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$book_id])) {
        $_SESSION['cart'][$book_id] += $quantity;
    } else {
        $_SESSION['cart'][$book_id] = $quantity;
    }
}

function getCartCount()
{
    return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
}
?>