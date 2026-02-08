<?php
if (!isLoggedIn()) {
    redirect('index.php?page=login');
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email)) {
        $error = "Nama dan Email tidak boleh kosong.";
    } else {
        // Check if email taken by another user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            $error = "Email sudah digunakan user lain.";
        } else {
            // Update Data
            if (!empty($password)) {
                if ($password !== $confirm_password) {
                    $error = "Password baru tidak cocok.";
                } elseif (strlen($password) < 5) {
                    $error = "Password minimal 5 karakter.";
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $hash, $user_id]);
                    $success = "Profil dan Password berhasil diperbarui.";
                    $_SESSION['user_name'] = $name;
                }
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                $stmt->execute([$name, $email, $user_id]);
                $success = "Profil berhasil diperbarui.";
                $_SESSION['user_name'] = $name;
            }
        }
    }
}

// Fetch Current Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Determine User Role for UI customization
$role_badge_color = ($user['role'] === 'admin') ? 'bg-slate-900 text-white' : 'bg-gradient-to-tr from-amber-400 to-orange-500 text-white';
$role_label = ($user['role'] === 'admin') ? 'Administrator' : 'Member';

// Get simple stats
$txn_count = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE user_id = ?");
$txn_count->execute([$user_id]);
$total_txns = $txn_count->fetchColumn();
?>

<div class="max-w-5xl mx-auto mb-20">
    <!-- Breadcrumb (Optional) -->
    <div class="mb-8">
        <a href="<?= isAdmin() ? 'index.php?page=admin' : 'index.php' ?>"
            class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-primary transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">

        <!-- Sidebar Profile Card -->
        <div class="md:col-span-1 sticky top-24">
            <div
                class="bg-white rounded-3xl shadow-lg border border-slate-100 p-8 text-center relative overflow-hidden group">
                <!-- Decorative background for admin -->
                <?php if ($user['role'] === 'admin'): ?>
                    <div class="absolute top-0 left-0 w-full h-24 bg-slate-900"></div>
                <?php else: ?>
                    <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-amber-100 to-orange-50"></div>
                <?php endif; ?>

                <div class="relative z-10">
                    <div
                        class="w-28 h-28 mx-auto -mt-4 mb-4 <?= $role_badge_color ?> rounded-full flex items-center justify-center text-4xl font-serif font-bold shadow-xl border-4 border-white">
                        <?= substr($user['name'], 0, 1) ?>
                    </div>

                    <h2 class="text-2xl font-bold text-slate-900 mb-1"><?= htmlspecialchars($user['name']) ?></h2>
                    <span
                        class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 <?= ($user['role'] === 'admin') ? 'bg-slate-100 text-slate-600' : 'bg-amber-50 text-amber-600' ?>">
                        <?= $role_label ?>
                    </span>
                    <p class="text-sm text-slate-500 mb-6"><?= htmlspecialchars($user['email']) ?></p>

                    <!-- Stats -->
                    <?php if ($user['role'] !== 'admin'): ?>
                        <div class="bg-slate-50 rounded-2xl p-5 mb-6 border border-slate-100">
                            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider mb-2">Saldo Token</p>
                            <p class="text-3xl font-extrabold text-primary flex items-center justify-center gap-2">
                                <span class="text-2xl">🪙</span> <?= number_format($user['tokens']) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <div class="text-left space-y-3 pt-4 border-t border-slate-100">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-medium">Bergabung</span>
                            <span
                                class="font-bold text-slate-800"><?= date('M Y', strtotime($user['created_at'])) ?></span>
                        </div>
                        <?php if ($user['role'] !== 'admin'): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500 font-medium">Total Transaksi</span>
                                <span class="font-bold text-slate-800"><?= $total_txns ?> Order</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-50">
                    <h3 class="text-2xl font-serif font-bold text-slate-900 flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Informasi Profil
                    </h3>
                    <p class="text-slate-500 mt-1">Perbarui detail akun dan keamanan Anda.</p>
                </div>

                <div class="p-8">
                    <?php if ($success): ?>
                        <div
                            class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl mb-6 flex items-start gap-3 border border-emerald-100 shadow-sm animate-fade-in-down">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="font-bold">Berhasil!</p>
                                <p class="text-sm opacity-90"><?= $success ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div
                            class="bg-rose-50 text-rose-700 px-4 py-3 rounded-xl mb-6 flex items-start gap-3 border border-rose-100 shadow-sm animate-fade-in-down">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="font-bold">Gagal Menyimpan</p>
                                <p class="text-sm opacity-90"><?= $error ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-slate-700">Nama Lengkap</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"
                                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all font-medium text-slate-800"
                                        required placeholder="Masukkan nama lengkap">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-slate-700">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all font-medium text-slate-800"
                                        required placeholder="nama@email.com">
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                            <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Keamanan Akun
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Password
                                        Baru</label>
                                    <input type="password" name="password"
                                        class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all"
                                        placeholder="••••••••">
                                    <p class="text-[10px] text-slate-400">Minimal 5 karakter. Kosongkan jika tidak ubah.
                                    </p>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Konfirmasi
                                        Password</label>
                                    <input type="password" name="confirm_password"
                                        class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all"
                                        placeholder="••••••••">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                class="px-8 py-3.5 bg-slate-900 hover:bg-primary text-white font-bold rounded-xl shadow-lg shadow-slate-900/20 hover:shadow-primary/30 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>