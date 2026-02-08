<?php
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Fetch Categories for Dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

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
        $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

        // New Fields
        $isbn = $_POST['isbn'];
        $publisher = $_POST['publisher'];
        $pages = $_POST['pages'];

        $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, price = ?, stock = ?, category_id = ?, isbn = ?, publisher = ?, pages = ? WHERE id = ?");
        $stmt->execute([$title, $author, $price, $stock, $category_id, $isbn, $publisher, $pages, $id]);
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
        $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

        // New Fields
        $isbn = $_POST['isbn'];
        $publisher = $_POST['publisher'];
        $pages = $_POST['pages'];

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

        $stmt = $pdo->prepare("INSERT INTO books (title, author, price, stock, description, image, category_id, isbn, publisher, pages) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $author, $price, $stock, $description, $image, $category_id, $isbn, $publisher, $pages]);
        $_SESSION['flash_message'] = "Buku berhasil ditambahkan.";
        $_SESSION['flash_type'] = "success";
        redirect('index.php?page=admin_books');
    }
}

// Fetch Books with Category Name
$stmt = $pdo->query("
    SELECT b.*, c.name as category_name 
    FROM books b 
    LEFT JOIN categories c ON b.category_id = c.id 
    ORDER BY b.created_at DESC
");
$books = $stmt->fetchAll();
?>

<div class="max-w-7xl mx-auto mb-12">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-4xl font-serif font-bold text-slate-900 mb-2">Manajemen Buku</h2>
            <p class="text-slate-500 font-sans">Tambah, edit, dan kelola inventaris buku.</p>
        </div>
        <a href="index.php?page=admin"
            class="px-5 py-2.5 bg-white text-slate-600 rounded-full border border-slate-200 hover:bg-slate-50 hover:text-primary font-medium transition-all shadow-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Add Book Form -->
    <div class="bg-white rounded-[2rem] shadow-lg border border-slate-100 p-8 mb-12">
        <h4 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
            Tambah Buku Baru
        </h4>
        
        <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <input type="hidden" name="action" value="add">

            <!-- Left Column: Details -->
            <div class="md:col-span-8 space-y-6">
                <!-- Title & Author -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Judul Buku</label>
                        <input type="text" name="title"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            placeholder="Contoh: Atomic Habits" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Penulis</label>
                        <input type="text" name="author"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            placeholder="Nama Penulis" required>
                    </div>
                </div>

                <!-- Category & Publisher -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                        <div class="relative">
                            <select name="category_id"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all appearance-none cursor-pointer">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                     <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Penerbit</label>
                        <input type="text" name="publisher"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            placeholder="Nama Penerbit">
                    </div>
                </div>

                 <!-- ISBN & Pages -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                     <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ISBN</label>
                        <input type="text" name="isbn"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            placeholder="ISBN-13">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Jumlah Halaman</label>
                        <input type="number" name="pages"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            placeholder="Contoh: 300">
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi / Sinopsis</label>
                    <textarea name="description" rows="4"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                        placeholder="Tulis deskripsi menarik tentang buku ini..."></textarea>
                </div>
            </div>

            <!-- Right Column: Pricing & Image -->
            <div class="md:col-span-4 space-y-6">
                 <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                    <h5 class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wider">Inventaris & Harga</h5>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-500 mb-2">Harga (Token)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-xl">🪙</span>
                            </div>
                            <input type="number" name="price"
                                class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-bold text-slate-900"
                                placeholder="0" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2">Stok Tersedia</label>
                        <input type="number" name="stock"
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-bold text-slate-900"
                            placeholder="0" required>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                    <h5 class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wider">Cover Buku</h5>
                    
                    <div class="relative w-full aspect-[3/4] bg-white border-2 border-dashed border-slate-300 rounded-xl flex flex-col items-center justify-center text-slate-400 hover:border-primary hover:text-primary transition-colors cursor-pointer group overflow-hidden" id="upload-preview-container">
                        <input type="file" name="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(this)">
                        <div class="flex flex-col items-center pointer-events-none group-hover:scale-110 transition-transform duration-300" id="upload-placeholder">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-xs font-bold">Upload Cover</span>
                        </div>
                        <img id="image-preview" src="#" alt="Preview" class="absolute inset-0 w-full h-full object-cover hidden">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-4 bg-primary text-white font-bold rounded-xl hover:bg-amber-600 transition-colors shadow-lg shadow-amber-200 hover:shadow-xl transform hover:-translate-y-1">
                    Simpan Buku
                </button>
            </div>
        </form>
    </div>

    <!-- Books List -->
    <div class="bg-white rounded-[2rem] shadow-lg border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-slate-50/50">
            <h4 class="font-bold text-slate-800">Daftar Katalog Buku <span class="text-slate-400 font-normal">(<?= count($books) ?>)</span></h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500">
                        <th class="p-6 font-bold">Buku</th>
                        <th class="p-6 font-bold">Kategori</th>
                        <th class="p-6 font-bold">Detail</th>
                        <th class="p-6 font-bold">Harga & Stok</th>
                        <th class="p-6 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($books as $b): ?>
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <form method="POST">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                
                                    <td class="p-6">
                                        <div class="flex gap-4 items-center">
                                            <div class="w-12 h-16 rounded-lg overflow-hidden shadow-sm bg-slate-200 flex-shrink-0">
                                                <img src="assets/images/<?= htmlspecialchars($b['image']) ?>" alt="cover" class="w-full h-full object-cover">
                                            </div>
                                            <div class="min-w-[180px]">
                                                <input type="text" name="title" value="<?= htmlspecialchars($b['title']) ?>"
                                                    class="block w-full text-sm font-bold text-slate-900 bg-transparent border-b border-transparent focus:border-primary focus:bg-white outline-none mb-1 transition-all rounded px-1">
                                                <input type="text" name="author" value="<?= htmlspecialchars($b['author']) ?>"
                                                    class="block w-full text-xs text-slate-500 italic bg-transparent border-b border-transparent focus:border-primary focus:bg-white outline-none transition-all rounded px-1">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                         <select name="category_id"
                                            class="text-xs font-semibold text-slate-600 bg-slate-100 border border-transparent focus:bg-white focus:border-primary rounded-lg px-2 py-1 outline-none cursor-pointer">
                                            <option value="">Uncategorized</option>
                                            <?php foreach ($categories as $cat): ?>
                                                    <option value="<?= $cat['id'] ?>" <?= $b['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($cat['name']) ?>
                                                    </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="p-6">
                                        <div class="space-y-1">
                                             <input type="text" name="isbn" value="<?= htmlspecialchars($b['isbn'] ?? '') ?>" placeholder="ISBN"
                                                class="block w-24 text-[10px] text-slate-400 bg-transparent border-b border-transparent focus:border-primary outline-none">
                                              <input type="text" name="publisher" value="<?= htmlspecialchars($b['publisher'] ?? '') ?>" placeholder="Publisher"
                                                class="block w-24 text-[10px] text-slate-400 bg-transparent border-b border-transparent focus:border-primary outline-none">
                                                <input type="number" name="pages" value="<?= htmlspecialchars($b['pages'] ?? '') ?>" placeholder="Pages"
                                                class="block w-16 text-[10px] text-slate-400 bg-transparent border-b border-transparent focus:border-primary outline-none">
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <div class="flex flex-col gap-2">
                                            <div class="flex items-center text-primary font-bold text-sm">
                                                <span class="mr-1 text-xs">🪙</span>
                                                <input type="number" name="price" value="<?= $b['price'] ?>"
                                                    class="w-16 bg-transparent border-b border-dashed border-slate-300 focus:border-primary outline-none">
                                            </div>
                                            <div class="flex items-center text-slate-500 text-xs">
                                                <span class="mr-1">Stok:</span>
                                                <input type="number" name="stock" value="<?= $b['stock'] ?>"
                                                    class="w-12 bg-transparent border-b border-dashed border-slate-300 focus:border-primary outline-none text-center font-semibold">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-6 text-center">
                                        <div class="flex flex-col gap-2 items-center opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                            <button type="submit"
                                                class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-100 transition-colors" title="Simpan">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        
                                            <button type="button"
                                                onclick="if(confirm('Hapus buku ini?')) { const form = this.closest('tr').querySelector('form'); const input = document.createElement('input'); input.type='hidden'; input.name='action'; input.value='delete'; input.name='id'; input.value='<?= $b['id'] ?>'; const delForm = document.createElement('form'); delForm.method='POST'; delForm.innerHTML = `<input type='hidden' name='action' value='delete'><input type='hidden' name='id' value='<?= $b['id'] ?>'>`; document.body.appendChild(delForm); delForm.submit(); }"
                                                class="w-8 h-8 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-100 transition-colors" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </form>
                            </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image-preview').src = e.target.result;
            document.getElementById('image-preview').classList.remove('hidden');
            document.getElementById('upload-placeholder').classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>