<?php
$stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC");
$books = $stmt->fetchAll();
?>

<div class="hero">
    <h1>Temukan Buku Favoritmu<br><span style="color: var(--secondary);">Jelajahi Dunia Baru</span></h1>
    <p>Koleksi buku terlengkap dengan harga terbaik. Mulai petualangan membaaca Anda hari ini.</p>
    <a href="#katalog" class="btn btn-primary">Lihat Katalog</a>
</div>

<h2 id="katalog" style="margin-bottom: 2rem; font-size: 2rem;">Katalog Buku</h2>

<div class="books-grid">
    <?php foreach ($books as $book): ?>
        <div class="book-card glass">
            <img src="assets/images/<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>"
                class="book-image">
            <div class="book-info">
                <h3 class="book-title">
                    <?= htmlspecialchars($book['title']) ?>
                </h3>
                <p class="book-author">by
                    <?= htmlspecialchars($book['author']) ?>
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <p class="book-price">
                        <?= formatPrice($book['price']) ?>
                    </p>
                    <span style="color: #f59e0b;">★
                        <?= $book['rating'] ?>
                    </span>
                </div>
                <form action="index.php?page=cart_action" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                    <button type="submit" class="btn btn-outline" style="width: 100%;">Tambah ke Keranjang</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>