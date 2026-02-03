<?php
if (!isset($_GET['id'])) {
    redirect('index.php');
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    echo "<div class='text-center py-20 text-slate-500'>Buku tidak ditemukan.</div>";
} else {
    ?>

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
        <div class="md:flex">
            <!-- Image Section -->
            <div class="md:w-1/3 bg-slate-50 p-8 flex items-center justify-center">
                <div
                    class="relative w-full aspect-[3/4] max-w-sm shadow-2xl rounded-2xl overflow-hidden transform transition duration-500 hover:scale-105">
                    <img src="assets/images/<?= htmlspecialchars($book['image']) ?>"
                        alt="<?= htmlspecialchars($book['title']) ?>" class="absolute inset-0 w-full h-full object-cover">
                </div>
            </div>

            <!-- Content Section -->
            <div class="md:w-2/3 p-8 md:p-12 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span
                            class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold uppercase tracking-wide rounded-full">Buku
                            Digital</span>
                        <?php if ($book['stock'] < 5): ?>
                            <span
                                class="px-3 py-1 bg-rose-100 text-rose-700 text-xs font-bold uppercase tracking-wide rounded-full">Stok
                                Terbatas</span>
                        <?php endif; ?>
                    </div>

                    <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-2 leading-tight">
                        <?= htmlspecialchars($book['title']) ?>
                    </h1>
                    <p class="text-xl text-slate-500 font-medium mb-8">
                        Oleh <span class="text-slate-800">
                            <?= htmlspecialchars($book['author']) ?>
                        </span>
                    </p>

                    <div class="prose prose-slate max-w-none text-slate-600 mb-8 leading-relaxed">
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Sinopsis</h3>
                        <p>
                            <?= nl2br(htmlspecialchars($book['description'])) ?>
                        </p>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-8 mt-auto">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div>
                            <p class="text-sm text-slate-400 font-medium uppercase tracking-wider">Harga Buku</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-primary">
                                    <?= number_format($book['price']) ?>
                                </span>
                                <span class="text-lg text-slate-600 font-bold">Token</span>
                            </div>
                        </div>

                        <form action="index.php?page=cart_action" method="POST" class="w-full sm:w-auto">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                            <button type="submit"
                                class="w-full sm:w-auto px-8 py-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Tambah ke Keranjang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
}
?>