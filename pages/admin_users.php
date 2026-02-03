<?php
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Handle Actions (Only Delete & Edit needed, as per request 'g usah beli admin')
// But wait, user said "nambahkan buku" is for admin. So Add Book is still needed.
// User said "g usah beli admin" (meaning admin doesn't buy books).
// "edit semumay a dari stok buku lah dari users manajemen lainya semuany lengkap"

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ... (Keep existing logic for Add/Edit/Delete Books - reusing the robust logic from previous turn)
    if ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$id]);
        $_SESSION['flash_message'] = "User berhasil dihapus.";
        $_SESSION['flash_type'] = "success";
        redirect('index.php?page=admin_users');
    }

    if ($action === 'edit') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $tokens = $_POST['tokens']; // Admin can manual edit tokens

        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, tokens = ? WHERE id = ?");
        $stmt->execute([$name, $email, $tokens, $id]);
        $_SESSION['flash_message'] = "Data user diperbarui.";
        $_SESSION['flash_type'] = "success";
        redirect('index.php?page=admin_users');
    }
}

$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-3xl font-bold text-slate-800">Manajemen Users</h2>
        <p class="text-slate-500">Kelola data pengguna dan saldo token mereka.</p>
    </div>
    <a href="index.php?page=admin"
        class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 font-medium transition-colors">
        &larr; Dashboard
    </a>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500">
                    <th class="p-6 font-bold">User Info</th>
                    <th class="p-6 font-bold">Role</th>
                    <th class="p-6 font-bold">Saldo Token</th>
                    <th class="p-6 font-bold">Bergabung</th>
                    <th class="p-6 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <form method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <td class="p-6">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold">
                                        <?= substr($u['name'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <input type="text" name="name" value="<?= htmlspecialchars($u['name']) ?>"
                                            class="block w-full font-bold text-slate-800 bg-transparent border-b border-transparent focus:border-primary outline-none text-sm mb-0.5">
                                        <input type="text" name="email" value="<?= htmlspecialchars($u['email']) ?>"
                                            class="block w-full text-xs text-slate-500 bg-transparent border-b border-transparent focus:border-primary outline-none">
                                    </div>
                                </div>
                            </td>
                            <td class="p-6">
                                <span
                                    class="px-2 py-1 rounded text-xs font-bold <?= $u['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600' ?>">
                                    <?= strtoupper($u['role']) ?>
                                </span>
                            </td>
                            <td class="p-6">
                                <div class="flex items-center gap-1">
                                    <span>🪙</span>
                                    <input type="number" name="tokens" value="<?= $u['tokens'] ?>"
                                        class="w-24 bg-transparent border-b border-slate-200 focus:border-primary outline-none font-bold text-slate-700">
                                </div>
                            </td>
                            <td class="p-6 text-sm text-slate-500">
                                <?= date('d M Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td class="p-6 text-right">
                                <div
                                    class="flex justify-end gap-2 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="submit" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"
                                        title="Simpan Perubahan">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>

                                    <?php if ($u['role'] !== 'admin'): ?>
                                        <button type="button"
                                            onclick="if(confirm('Hapus user ini selamanya?')) { this.nextElementSibling.submit(); }"
                                            class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100" title="Hapus User">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <form method="POST" style="display: none;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>