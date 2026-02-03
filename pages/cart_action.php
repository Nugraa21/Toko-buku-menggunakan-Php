<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $book_id = $_POST['book_id'] ?? 0;

    if ($action === 'add') {
        addToCart($book_id, 1);
        $_SESSION['flash_message'] = "Buku berhasil ditambahkan ke keranjang!";
        $_SESSION['flash_type'] = "success";
    } elseif ($action === 'update') {
        $qty = max(1, intval($_POST['qty']));
        if (isset($_SESSION['cart'][$book_id])) {
            $_SESSION['cart'][$book_id] = $qty;
            $_SESSION['flash_message'] = "Keranjang diperbarui.";
            $_SESSION['flash_type'] = "success";
        }
    } elseif ($action === 'delete') {
        unset($_SESSION['cart'][$book_id]);
        $_SESSION['flash_message'] = "Item dihapus.";
        $_SESSION['flash_type'] = "success";
    }

    // Redirect back
    $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?page=cart';
    header("Location: $referer");
    exit();
}
?>