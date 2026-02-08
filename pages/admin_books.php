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

    if ($action === 'add' || $action === 'edit') {
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $price = intval($_POST['price']);
        $stock = intval($_POST['stock']);
        $description = $_POST['description'];
        $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
        $isbn = $_POST['isbn'];
        $publisher = $_POST['publisher'];
        $pages = intval($_POST['pages']);

        // Handle File Upload
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/images/';
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;

            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array($_FILES['image']['type'], $allowedTypes)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $image = $fileName;
                }
            }
        }

        if ($action === 'add') {
            $image = $image ?? 'default_book.png';
            $stmt = $pdo->prepare("INSERT INTO books (title, author, price, stock, description, image, category_id, isbn, publisher, pages) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $author, $price, $stock, $description, $image, $category_id, $isbn, $publisher, $pages]);
            $_SESSION['flash_message'] = "Buku baru berhasil ditambahkan.";
            $_SESSION['flash_type'] = "success";
        } else if ($action === 'edit') {
            $id = $_POST['id'];

            // Construct Query dynamically based on whether image is updated
            $query = "UPDATE books SET title=?, author=?, price=?, stock=?, description=?, category_id=?, isbn=?, publisher=?, pages=?";
            $params = [$title, $author, $price, $stock, $description, $category_id, $isbn, $publisher, $pages];

            if ($image) {
                $query .= ", image=?";
                $params[] = $image;
            }

            $query .= " WHERE id=?";
            $params[] = $id;

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);

            $_SESSION['flash_message'] = "Data buku berhasil diperbarui.";
            $_SESSION['flash_type'] = "success";
        }
        redirect('index.php?page=admin_books');
    }
}

// Fetch Books
$stmt = $pdo->query("
    SELECT b.*, c.name as category_name 
    FROM books b 
    LEFT JOIN categories c ON b.category_id = c.id 
    ORDER BY b.created_at DESC
");
$books = $stmt->fetchAll();
?>

<div class="max-w-7xl mx-auto mb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h2 class="text-4xl font-serif font-bold text-slate-900 mb-2">Manajemen Buku</h2>
            <p class="text-slate-500 font-sans">Kelola inventaris, stok, dan detail buku.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="openModal('add')"
                class="px-5 py-2.5 bg-slate-900 text-white rounded-xl shadow-lg hover:shadow-xl hover:bg-primary transition-all transform hover:-translate-y-0.5 flex items-center gap-2 font-bold text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Tambah Buku
            </button>
            <a href="index.php?page=admin"
                class="px-5 py-2.5 bg-white text-slate-600 rounded-xl border border-slate-200 hover:bg-slate-50 hover:text-primary font-medium transition-all shadow-sm flex items-center gap-2">
                Kembali
            </a>
        </div>
    </div>

    <!-- Books List Table -->
    <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50/50 border-b border-slate-100 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="p-6">Cover & Judul</th>
                        <th class="p-6">Kategori</th>
                        <th class="p-6">Harga & Stok</th>
                        <th class="p-6">Info Tambahan</th>
                        <th class="p-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($books as $b): ?>
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="p-6">
                                <div class="flex gap-4 items-center">
                                    <div class="w-12 h-16 rounded-lg overflow-hidden shadow-sm bg-slate-200 flex-shrink-0 relative group/cover cursor-pointer"
                                        onclick='openModal("edit", <?= json_encode($b) ?>)'>
                                        <img src="assets/images/<?= htmlspecialchars($b['image']) ?>" alt="cover"
                                            class="w-full h-full object-cover">
                                        <div
                                            class="absolute inset-0 bg-black/20 hidden group-hover/cover:flex items-center justify-center text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 line-clamp-1 max-w-[200px]">
                                            <?= htmlspecialchars($b['title']) ?></div>
                                        <div class="text-xs text-slate-500 italic"><?= htmlspecialchars($b['author']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-6">
                                <span
                                    class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold border border-slate-200">
                                    <?= htmlspecialchars($b['category_name'] ?? 'Uncategorized') ?>
                                </span>
                            </td>
                            <td class="p-6">
                                <div class="flex flex-col gap-1">
                                    <span class="font-bold text-slate-700 flex items-center gap-1">
                                        🪙 <?= number_format($b['price']) ?>
                                    </span>
                                    <span class="text-xs text-slate-500 font-medium">
                                        Stok: <?= $b['stock'] ?>
                                    </span>
                                </div>
                            </td>
                            <td class="p-6">
                                <div class="text-[10px] text-slate-400 space-y-0.5 font-mono">
                                    <?php if ($b['isbn']): ?>
                                        <div>ISBN: <?= htmlspecialchars($b['isbn']) ?></div><?php endif; ?>
                                    <?php if ($b['publisher']): ?>
                                        <div>Pub: <?= htmlspecialchars($b['publisher']) ?></div><?php endif; ?>
                                    <?php if ($b['pages']): ?>
                                        <div>Pg: <?= $b['pages'] ?></div><?php endif; ?>
                                </div>
                            </td>
                            <td class="p-6 text-right">
                                <div
                                    class="flex justify-end gap-2 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick='openModal("edit", <?= json_encode($b) ?>)'
                                        class="p-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50 border border-slate-200 shadow-sm transition-all hover:scale-105"
                                        title="Edit Buku">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <form method="POST" onsubmit="return confirm('Hapus buku ini secara permanen?');"
                                        class="inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                        <button type="submit"
                                            class="p-2 bg-white text-rose-600 rounded-lg hover:bg-rose-50 border border-slate-200 shadow-sm transition-all hover:scale-105"
                                            title="Hapus Buku">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Overlay -->
<div id="bookModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-[2.5rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-slate-100">

                <!-- Modal Header -->
                <div
                    class="bg-slate-50 px-8 py-5 border-b border-slate-100 flex justify-between items-center sticky top-0 z-20">
                    <h3 class="text-xl font-bold text-slate-900" id="modalTitle">Tambah Buku Baru</h3>
                    <button type="button" onclick="closeModal()"
                        class="w-8 h-8 rounded-full bg-slate-200 text-slate-500 hover:bg-rose-100 hover:text-rose-500 flex items-center justify-center transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form method="POST" id="bookForm" enctype="multipart/form-data"
                    class="max-h-[80vh] overflow-y-auto custom-scrollbar">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="bookId" value="">

                    <div class="p-8 grid grid-cols-1 lg:grid-cols-12 gap-8">

                        <!-- Left: Image Upload -->
                        <div class="lg:col-span-4">
                            <div class="sticky top-0">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Cover Buku</label>
                                <div class="relative w-full aspect-[3/4] bg-slate-100 border-2 border-dashed border-slate-300 rounded-2xl flex flex-col items-center justify-center text-slate-400 hover:border-primary hover:text-primary transition-colors cursor-pointer group overflow-hidden shadow-inner"
                                    id="upload-container">
                                    <input type="file" name="image"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        onchange="previewImage(this)">

                                    <!-- Placeholder -->
                                    <div class="flex flex-col items-center pointer-events-none group-hover:scale-110 transition-transform duration-300"
                                        id="upload-placeholder">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs font-bold uppercase tracking-widest">Upload Cover</span>
                                    </div>

                                    <!-- Preview Image -->
                                    <img id="image-preview" src="#" alt="Preview"
                                        class="absolute inset-0 w-full h-full object-cover hidden">

                                    <!-- Overlay Text on Hover (if image exists) -->
                                    <div id="change-text"
                                        class="absolute inset-0 bg-black/40 flex items-center justify-center text-white font-bold opacity-0 group-hover:opacity-100 transition-opacity hidden z-0">
                                        Ganti Foto
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-2 text-center">Format: JPG, PNG, WEBP. Max 2MB.
                                </p>
                            </div>
                        </div>

                        <!-- Right: Form Fields -->
                        <div class="lg:col-span-8 space-y-6">
                            <!-- Title & Author -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Judul Buku</label>
                                    <input type="text" name="title" id="title" required
                                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-slate-400">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Penulis</label>
                                    <input type="text" name="author" id="author" required
                                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-slate-400">
                                </div>
                            </div>

                            <!-- Category & Publisher -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Kategori</label>
                                    <div class="relative">
                                        <select name="category_id" id="category_id"
                                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all appearance-none cursor-pointer">
                                            <option value="">-- Pilih Kategori --</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Penerbit</label>
                                    <input type="text" name="publisher" id="publisher"
                                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-slate-400">
                                </div>
                            </div>

                            <!-- Price & Stock -->
                            <div class="grid grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Harga (Token)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-lg">🪙</span>
                                        <input type="number" name="price" id="price" required
                                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-bold text-slate-900">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Stok</label>
                                    <input type="number" name="stock" id="stock" required
                                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-bold text-slate-900 text-center">
                                </div>
                            </div>

                            <!-- Meta (ISBN, Pages) -->
                            <div class="grid grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">ISBN</label>
                                    <input type="text" name="isbn" id="isbn"
                                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-slate-400 font-mono text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Halaman</label>
                                    <input type="number" name="pages" id="pages"
                                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-slate-400">
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Deskripsi</label>
                                <textarea name="description" id="description" rows="5"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-slate-400 resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer (Sticky Bottom) -->
                    <div
                        class="bg-slate-50 px-8 py-5 flex flex-row-reverse gap-3 border-t border-slate-100 sticky bottom-0 z-20">
                        <button type="submit"
                            class="px-6 py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-primary transition-colors shadow-lg shadow-slate-200">
                            Simpan Data
                        </button>
                        <button type="button" onclick="closeModal()"
                            class="px-6 py-3 bg-white text-slate-600 font-bold rounded-xl border border-slate-300 hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('bookModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('bookForm');
    const formAction = document.getElementById('formAction');
    const bookIdInput = document.getElementById('bookId');
    const imagePreview = document.getElementById('image-preview');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const changeText = document.getElementById('change-text');

    function openModal(mode, data = null) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling

        if (mode === 'edit' && data) {
            modalTitle.textContent = 'Edit Data Buku';
            formAction.value = 'edit';
            bookIdInput.value = data.id;

            // Fill Fields
            document.getElementById('title').value = data.title;
            document.getElementById('author').value = data.author;
            document.getElementById('category_id').value = data.category_id || '';
            document.getElementById('publisher').value = data.publisher || '';
            document.getElementById('price').value = data.price;
            document.getElementById('stock').value = data.stock;
            document.getElementById('isbn').value = data.isbn || '';
            document.getElementById('pages').value = data.pages || '';
            document.getElementById('description').value = data.description;

            // Image Preview
            if (data.image) {
                imagePreview.src = 'assets/images/' + data.image;
                imagePreview.classList.remove('hidden');
                uploadPlaceholder.classList.add('hidden');
                changeText.classList.remove('hidden');
            } else {
                resetImagePreview();
            }
        } else {
            modalTitle.textContent = 'Tambah Buku Baru';
            formAction.value = 'add';
            bookIdInput.value = '';
            form.reset();
            resetImagePreview();
        }
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore scrolling
    }

    function resetImagePreview() {
        imagePreview.src = '#';
        imagePreview.classList.add('hidden');
        uploadPlaceholder.classList.remove('hidden');
        changeText.classList.add('hidden');
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('hidden');
                uploadPlaceholder.classList.add('hidden');
                changeText.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Close on Escape key
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
</script>