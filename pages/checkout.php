<?php
if (!isLoggedIn()) {
    redirect('index.php?page=login');
}

if (empty($_SESSION['cart'])) {
    redirect('index.php');
}

// Calculate total again
$ids = implode(',', array_keys($_SESSION['cart']));
$placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
$stmt = $pdo->prepare("SELECT * FROM books WHERE id IN ($placeholders)");
$stmt->execute(array_keys($_SESSION['cart']));
$books = $stmt->fetchAll();

$total_tokens_needed = 0;
$items = [];
foreach ($books as $book) {
    if ($book['stock'] < $_SESSION['cart'][$book['id']]) {
        $_SESSION['flash_message'] = "Stok buku '" . $book['title'] . "' tidak mencukupi.";
        $_SESSION['flash_type'] = "error";
        redirect('index.php?page=cart');
    }

    $qty = $_SESSION['cart'][$book['id']];
    $total_tokens_needed += $book['price'] * $qty;
    $items[] = [
        'id' => $book['id'],
        'price' => $book['price'],
        'qty' => $qty
    ];
}

// Check User Balance
$user_balance = getUserTokenBalance($pdo, $_SESSION['user_id']);

if ($user_balance < $total_tokens_needed) {
    $_SESSION['flash_message'] = "Saldo Token tidak mencukupi! Anda butuh " . number_format($total_tokens_needed) . " Token, tapi hanya punya " . number_format($user_balance) . " Token. Silakan Top Up.";
    $_SESSION['flash_type'] = "error";
    redirect('index.php?page=topup');
}

try {
    $pdo->beginTransaction();

    // Deduct Tokens
    $stmt = $pdo->prepare("UPDATE users SET tokens = tokens - ? WHERE id = ?");
    if (!$stmt->execute([$total_tokens_needed, $_SESSION['user_id']])) {
        throw new Exception("Gagal memotong saldo.");
    }

    // Create Transaction (Completed immediately)
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, total_tokens, status) VALUES (?, ?, 'completed')");
    $stmt->execute([$_SESSION['user_id'], $total_tokens_needed]);
    $transaction_id = $pdo->lastInsertId();

    // Create Items
    $stmt = $pdo->prepare("INSERT INTO transaction_items (transaction_id, book_id, quantity, price_per_token) VALUES (?, ?, ?, ?)");
    foreach ($items as $item) {
        $stmt->execute([$transaction_id, $item['id'], $item['qty'], $item['price']]);

        // Decrease stock
        $updateStock = $pdo->prepare("UPDATE books SET stock = stock - ? WHERE id = ?");
        $updateStock->execute([$item['qty'], $item['id']]);
    }

    $pdo->commit();
    unset($_SESSION['cart']);

    $_SESSION['flash_message'] = "Pembelian berhasil! Saldo terpotong " . number_format($total_tokens_needed) . " Token.";
    $_SESSION['flash_type'] = "success";
    header("Location: index.php?page=history");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash_message'] = "Terjadi kesalahan: " . $e->getMessage();
    $_SESSION['flash_type'] = "error";
    header("Location: index.php?page=cart");
    exit();
}
?>