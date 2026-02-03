<?php
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_message'] = "Buku berhasil dihapus.";
        $_SESSION['flash_type'] = "success";
        redirect('index.php?page=admin_books');
    }

    if ($action === 'edit') {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $author = $_POST['author'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];

        $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, price = ?, stock = ? WHERE id = ?");
        $stmt->execute([$title, $author, $price, $stock, $id]);
        $_SESSION['flash_message'] = "Buku berhasil diupdate.";
        $_SESSION['flash_type'] = "success";
        redirect('index.php?page=admin_books');
    }

    if ($action === 'add') {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $description = $_POST['description'];

        $image = 'default_book.png';

        // Handle File Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/images/';
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;

            // Validate image type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array($_FILES['image']['type'], $allowedTypes)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $image = $fileName;
                }
            }
        }

        $stmt = $pdo->prepare("INSERT INTO books (title, author, price, stock, description, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $author, $price, $stock, $description, $image]);
        $_SESSION['flash_message'] = "Buku berhasil ditambahkan.";
        $_SESSION['flash_type'] = "success";
        redirect('index.php?page=admin_books');
    }
}

// Fetch Books
$stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC");
$books = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-3xl font-bold text-slate-800">Manajemen Buku</h2>
        <p class="text-slate-500">Tambah dan kelola katalog buku.</p>
    </div>
    <a href="index.php?page=admin"
        class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 font-medium transition-colors">
        &larr; Kembali
    </a>
</div>

<!-- Add Book Form -->
<div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-8 mb-8">
    <h4 class="text-xl font-bold text-slate-800 mb-6">Tambah Buku Baru</h4>
    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <input type="hidden" name="action" value="add">

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Judul Buku</label>
            <input type="text" name="title"
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                required>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Penulis</label>
            <input type="text" name="author"
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                required>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Harga (Token)</label>
            <input type="number" name="price"
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                required>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Stok</label>
            <input type="number" name="stock"
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                required>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-2">Cover Image</label>
            <input type="file" name="image"
                class="w-full p-2 border border-slate-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-amber-600">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi / Sinopsis</label>
            <textarea name="description" rows="3"
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"></textarea>
        </div>

        <div class="md:col-span-2">
            <button type="submit"
                class="w-full md:w-auto px-8 py-3 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition-colors">
                Tambah Buku
            </button>
        </div>
    </form>
</div>

<!-- Books List -->
<div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="p-4 font-bold text-slate-600">Cover</th>
                    <th class="p-4 font-bold text-slate-600">Judul & Penulis</th>
                    <th class="p-4 font-bold text-slate-600">Harga</th>
                    <th class="p-4 font-bold text-slate-600">Stok</th>
                    <th class="p-4 font-bold text-slate-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($books as $b): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <form method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= $b['id'] ?>">
                            <td class="p-4">
                                <img src="assets/images/<?= htmlspecialchars($b['image']) ?>" alt="cover"
                                    class="w-12 h-16 object-cover rounded shadow-sm bg-slate-200">
                            </td>
                            <td class="p-4">
                                <input type="text" name="title" value="<?= htmlspecialchars($b['title']) ?>"
                                    class="block w-full text-sm font-bold text-slate-800 bg-transparent border-b border-transparent focus:border-primary outline-none mb-1">
                                <input type="text" name="author" value="<?= htmlspecialchars($b['author']) ?>"
                                    class="block w-full text-xs text-slate-500 bg-transparent border-b border-transparent focus:border-primary outline-none">
                            </td>
                            <td class="p-4">
                                <div class="flex items-center">
                                    <span class="mr-1">🪙</span>
                                    <input type="number" name="price" value="<?= $b['price'] ?>"
                                        class="w-20 bg-transparent border-b border-slate-300 focus:border-primary outline-none text-sm">
                                </div>
                            </td>
                            <td class="p-4">
                                <input type="number" name="stock" value="<?= $b['stock'] ?>"
                                    class="w-16 bg-transparent border-b border-slate-300 focus:border-primary outline-none text-sm text-center">
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <button type="submit"
                                        class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold roundedHover:bg-blue-200">Save</button>
                                    <button type="button"
                                        onclick="if(confirm('Hapus buku ini?')) { this.nextElementSibling.submit(); }"
                                        class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded hover:bg-red-200 cursor-pointer">Del</button>
                                    <form method="POST" style="display: none;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                    </form>
                                </div>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>