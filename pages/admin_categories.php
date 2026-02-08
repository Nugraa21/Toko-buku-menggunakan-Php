<?php
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = $_POST['id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['flash_message'] = "Kategori berhasil dihapus.";
            $_SESSION['flash_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = "Gagal menghapus kategori. Mungkin sedang digunakan oleh buku.";
            $_SESSION['flash_type'] = "error";
        }
        redirect('index.php?page=admin_categories');
    }

    if ($action === 'edit') {
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

        try {
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $id]);
            $_SESSION['flash_message'] = "Kategori berhasil diupdate.";
            $_SESSION['flash_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = "Gagal update kategori. Nama mungkin duplikat.";
            $_SESSION['flash_type'] = "error";
        }
        redirect('index.php?page=admin_categories');
    }

    if ($action === 'add') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

        if (!empty($name)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
                $stmt->execute([$name, $slug, $description]);
                $_SESSION['flash_message'] = "Kategori berhasil ditambahkan.";
                $_SESSION['flash_type'] = "success";
            } catch (PDOException $e) {
                $_SESSION['flash_message'] = "Gagal menambah kategori. Nama mungkin duplikat.";
                $_SESSION['flash_type'] = "error";
            }
        } else {
            $_SESSION['flash_message'] = "Nama kategori tidak boleh kosong.";
            $_SESSION['flash_type'] = "error";
        }
        redirect('index.php?page=admin_categories');
    }
}

// Fetch Categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>

<div class="max-w-7xl mx-auto mb-12">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-4xl font-serif font-bold text-slate-900 mb-2">Manajemen Kategori</h2>
            <p class="text-slate-500 font-sans">Kelola genre dan kategori buku.</p>
        </div>
        <a href="index.php?page=admin"
            class="px-5 py-2.5 bg-white text-slate-600 rounded-full border border-slate-200 hover:bg-slate-50 hover:text-primary font-medium transition-all shadow-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-[2rem] shadow-lg border border-slate-100 p-8 sticky top-24">
                <h4 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tambah Kategori
                </h4>
                <form method="POST" class="space-y-5">
                    <input type="hidden" name="action" value="add">

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Kategori</label>
                        <input type="text" name="name"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-slate-400"
                            placeholder="Contoh: Fiksi Ilmiah" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Singkat</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all placeholder:text-slate-400"
                            placeholder="Deskripsi kategori ini..."></textarea>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-primary transition-colors shadow-lg shadow-slate-200">
                        Simpan Kategori
                    </button>
                </form>
            </div>
        </div>

        <!-- List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2rem] shadow-lg border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 bg-slate-50/50">
                    <h4 class="font-bold text-slate-800">Daftar Kategori <span class="text-slate-400 font-normal">(
                            <?= count($categories) ?>)
                        </span></h4>
                </div>

                <?php if (empty($categories)): ?>
                    <div class="p-12 text-center text-slate-500">
                        Belum ada kategori yang ditambahkan.
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($categories as $cat): ?>
                            <div class="p-6 hover:bg-slate-50 transition-colors group">
                                <form method="POST"
                                    class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">

                                    <div class="flex-1 w-full space-y-2">
                                        <div class="flex items-center gap-3">
                                            <input type="text" name="name" value="<?= htmlspecialchars($cat['name']) ?>"
                                                class="font-bold text-slate-900 bg-transparent border-b border-transparent focus:border-primary focus:bg-white outline-none px-1 transition-all w-full sm:w-auto">
                                            <span class="text-xs font-mono text-slate-400 bg-slate-100 px-2 py-0.5 rounded">/
                                                <?= htmlspecialchars($cat['slug']) ?>
                                            </span>
                                        </div>
                                        <input type="text" name="description"
                                            value="<?= htmlspecialchars($cat['description']) ?>"
                                            class="text-sm text-slate-500 bg-transparent border-b border-transparent focus:border-primary focus:bg-white outline-none w-full px-1 transition-all">
                                    </div>

                                    <div
                                        class="flex items-center gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                        <button type="submit"
                                            class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors"
                                            title="Simpan Perubahan">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>

                                        <button type="button"
                                            onclick="if(confirm('Hapus kategori ini? Buku dalam kategori ini akan kehilangan kategorinya.')) { const form = this.closest('form'); const input = document.createElement('input'); input.type='hidden'; input.name='action'; input.value='delete'; form.appendChild(input); form.submit(); }"
                                            class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition-colors"
                                            title="Hapus Kategori">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>