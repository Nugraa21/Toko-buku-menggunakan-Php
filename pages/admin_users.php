<?php
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = $_POST['id'];
        // Prevent deleting self or main admin if needed, but basic check for now
        if ($id == $_SESSION['user_id']) {
            $_SESSION['flash_message'] = "Tidak dapat menghapus akun sendiri.";
            $_SESSION['flash_type'] = "error";
        } else {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['flash_message'] = "User berhasil dihapus.";
            $_SESSION['flash_type'] = "success";
        }
        redirect('index.php?page=admin_users');
    }

    if ($action === 'add' || $action === 'edit') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $role = $_POST['role'];
        $tokens = intval($_POST['tokens']);
        $password = $_POST['password'];

        if ($action === 'add') {
            // Check if email exists
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);
            if ($check->rowCount() > 0) {
                $_SESSION['flash_message'] = "Email sudah terdaftar.";
                $_SESSION['flash_type'] = "error";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, tokens) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $hashed_password, $role, $tokens]);
                $_SESSION['flash_message'] = "User baru berhasil ditambahkan.";
                $_SESSION['flash_type'] = "success";
            }
        } else if ($action === 'edit') {
            $id = $_POST['id'];

            // Basic query parts
            $query = "UPDATE users SET name = ?, email = ?, role = ?, tokens = ?";
            $params = [$name, $email, $role, $tokens];

            // Update password only if provided
            if (!empty($password)) {
                $query .= ", password = ?";
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }

            $query .= " WHERE id = ?";
            $params[] = $id;

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);

            $_SESSION['flash_message'] = "Data user berhasil diperbarui.";
            $_SESSION['flash_type'] = "success";
        }
        redirect('index.php?page=admin_users');
    }
}

// Fetch Users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="max-w-7xl mx-auto mb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h2 class="text-4xl font-serif font-bold text-slate-900 mb-2">Manajemen User</h2>
            <p class="text-slate-500 font-sans">Kelola akun pengguna, saldo, dan hak akses.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="openModal('add')"
                class="px-5 py-2.5 bg-slate-900 text-white rounded-xl shadow-lg hover:shadow-xl hover:bg-primary transition-all transform hover:-translate-y-0.5 flex items-center gap-2 font-bold text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Tambah User
            </button>
            <a href="index.php?page=admin"
                class="px-5 py-2.5 bg-white text-slate-600 rounded-xl border border-slate-200 hover:bg-slate-50 hover:text-primary font-medium transition-all shadow-sm flex items-center gap-2">
                Kembali
            </a>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50/50 border-b border-slate-100 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="p-6">User Profile</th>
                        <th class="p-6">Role / Akses</th>
                        <th class="p-6">Saldo Token</th>
                        <th class="p-6">Status</th>
                        <th class="p-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="p-6">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-600 font-serif font-bold text-lg shadow-inner">
                                        <?= substr($u['name'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900"><?= htmlspecialchars($u['name']) ?></div>
                                        <div class="text-xs text-slate-500 font-mono"><?= htmlspecialchars($u['email']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-6">
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span
                                        class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold border border-purple-200 shadow-sm">
                                        ADMINISTRATOR
                                    </span>
                                <?php else: ?>
                                    <span
                                        class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold border border-slate-200">
                                        MEMBER
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-6">
                                <div class="flex items-center gap-2 font-bold text-slate-700">
                                    <span class="text-lg">🪙</span>
                                    <span><?= number_format($u['tokens']) ?></span>
                                </div>
                            </td>
                            <td class="p-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                                    <span class="text-sm text-slate-600 font-medium">Aktif</span>
                                </div>
                            </td>
                            <td class="p-6 text-right">
                                <div
                                    class="flex justify-end gap-2 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick='openModal("edit", <?= json_encode($u) ?>)'
                                        class="p-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50 border border-slate-200 shadow-sm transition-all hover:scale-105"
                                        title="Edit User">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                        <form method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak bisa dibatalkan.');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                            <button type="submit"
                                                class="p-2 bg-white text-rose-600 rounded-lg hover:bg-rose-50 border border-slate-200 shadow-sm transition-all hover:scale-105"
                                                title="Hapus User">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    <?php endif; ?>
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
<div id="userModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-[2rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">

                <!-- Modal Header -->
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-900" id="modalTitle">Tambah User Baru</h3>
                    <button type="button" onclick="closeModal()"
                        class="text-slate-400 hover:text-slate-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form method="POST" id="userForm">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="userId" value="">

                    <div class="px-6 py-6 space-y-5">
                        <!-- Name & Email -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="name" id="userName" required
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-slate-400">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Alamat Email</label>
                                <input type="email" name="email" id="userEmail" required
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-slate-400">
                            </div>
                        </div>

                        <!-- Role & Tokens -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Role / Peran</label>
                                <select name="role" id="userRole"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all cursor-pointer">
                                    <option value="user">User / Member</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Saldo Token</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🪙</span>
                                    <input type="number" name="tokens" id="userTokens" value="0" min="0" required
                                        class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-mono font-bold text-slate-700">
                                </div>
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">
                                Password <span id="passHint"
                                    class="text-xs font-normal text-slate-400 italic hidden">(Kosongkan jika tidak ingin
                                    mengubah)</span>
                            </label>
                            <input type="password" name="password" id="userPassword"
                                class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-slate-400"
                                placeholder="******">
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100">
                        <button type="submit"
                            class="px-5 py-2.5 bg-slate-900 text-white font-bold rounded-xl hover:bg-primary transition-colors shadow-lg shadow-slate-200">
                            Simpan Data
                        </button>
                        <button type="button" onclick="closeModal()"
                            class="px-5 py-2.5 bg-white text-slate-600 font-bold rounded-xl border border-slate-300 hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('userModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('userForm');
    const formAction = document.getElementById('formAction');
    const userIdInput = document.getElementById('userId');
    const passHint = document.getElementById('passHint');

    function openModal(mode, data = null) {
        modal.classList.remove('hidden');

        if (mode === 'edit' && data) {
            modalTitle.textContent = 'Edit Data User';
            formAction.value = 'edit';
            userIdInput.value = data.id;

            // Fill Data
            document.getElementById('userName').value = data.name;
            document.getElementById('userEmail').value = data.email;
            document.getElementById('userRole').value = data.role;
            document.getElementById('userTokens').value = data.tokens;

            // Password handling
            document.getElementById('userPassword').removeAttribute('required');
            passHint.classList.remove('hidden');
        } else {
            modalTitle.textContent = 'Tambah User Baru';
            formAction.value = 'add';
            userIdInput.value = '';
            form.reset();

            // Reset fields defaults
            document.getElementById('userTokens').value = 0;
            document.getElementById('userRole').value = 'user';

            // Password required for new user
            document.getElementById('userPassword').setAttribute('required', 'required');
            passHint.classList.add('hidden');
        }
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    // Close on Escape key
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
</script>