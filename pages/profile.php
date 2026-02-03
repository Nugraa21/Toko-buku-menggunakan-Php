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

// Get simple stats
$txn_count = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE user_id = ?");
$txn_count->execute([$user_id]);
$total_txns = $txn_count->fetchColumn();
?>

<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Sidebar Profile Card -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 text-center sticky top-24">
                <div
                    class="w-24 h-24 mx-auto bg-gradient-to-tr from-amber-400 to-orange-500 rounded-full flex items-center justify-center text-4xl text-white font-bold mb-4 shadow-md">
                    <?= substr($user['name'], 0, 1) ?>
                </div>
                <h2 class="text-xl font-bold text-slate-800"><?= htmlspecialchars($user['name']) ?></h2>
                <p class="text-sm text-slate-500 mb-6"><?= htmlspecialchars($user['email']) ?></p>

                <div class="bg-slate-50 rounded-xl p-4 mb-6 border border-slate-100">
                    <p class="text-xs text-slate-400 uppercase font-bold tracking-wider mb-1">Saldo Token</p>
                    <p class="text-2xl font-extrabold text-primary">🪙 <?= number_format($user['tokens']) ?></p>
                </div>

                <div class="text-left space-y-2">
                    <div class="flex justify-between text-sm py-2 border-b border-slate-50">
                        <span class="text-slate-500">Member Sejak</span>
                        <span
                            class="font-medium text-slate-700"><?= date('M Y', strtotime($user['created_at'])) ?></span>
                    </div>
                    <div class="flex justify-between text-sm py-2 border-b border-slate-50">
                        <span class="text-slate-500">Total Transaksi</span>
                        <span class="font-medium text-slate-700"><?= $total_txns ?>x</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-8">
                <h3 class="text-2xl font-bold text-slate-800 mb-6 pb-4 border-b border-slate-100">Edit Profil</h3>

                <?php if ($success): ?>
                    <div
                        class="bg-green-50 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 border border-green-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <?= $success ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div
                        class="bg-red-50 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 border border-red-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                required>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-6 mt-6">
                        <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Ganti Password
                        </h4>
                        <p class="text-xs text-slate-500 mb-4">Kosongkan jika tidak ingin mengubah password.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Password
                                    Baru</label>
                                <input type="password" name="password"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                    placeholder="••••••••">
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Konfirmasi</label>
                                <input type="password" name="confirm_password"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit"
                            class="px-8 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-lg transition-transform hover:-translate-y-1">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>