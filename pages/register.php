<?php
if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Semua kolom harus diisi.";
    } elseif ($password !== $confirm_password) {
        $error = "Password tidak cocok.";
    } elseif (strlen($password) < 5) {
        $error = "Password minimal 5 karakter.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email sudah terdaftar.";
        } else {
            // New user gets 0 tokens
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, tokens) VALUES (?, ?, ?, 0)");
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $_SESSION['flash_message'] = "Registrasi berhasil! Silakan login.";
                $_SESSION['flash_type'] = "success";
                redirect('index.php?page=login');
            } else {
                $error = "Gagal mendaftar. Silakan coba lagi.";
            }
        }
    }
}
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-2xl border border-slate-100">
        <div class="text-center">
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900">
                Buat Akun Baru
            </h2>
            <p class="mt-2 text-sm text-slate-500">
                Sudah punya akun? <a href="index.php?page=login"
                    class="font-medium text-primary hover:text-amber-600 transition-colors">Login disini</a>
            </p>
        </div>

        <?php if (isset($error)): ?>
            <div
                class="bg-red-50 text-red-700 px-4 py-3 rounded-xl border border-red-100 flex items-center gap-2 text-sm font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" method="POST">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                    <input name="name" type="text" required
                        class="appearance-none relative block w-full px-4 py-3 border border-slate-300 placeholder-slate-400 text-slate-900 rounded-xl focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm transition-all shadow-sm"
                        placeholder="John Doe" value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                    <input name="email" type="email" required
                        class="appearance-none relative block w-full px-4 py-3 border border-slate-300 placeholder-slate-400 text-slate-900 rounded-xl focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm transition-all shadow-sm"
                        placeholder="user@example.com" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                        <input name="password" type="password" required
                            class="appearance-none relative block w-full px-4 py-3 border border-slate-300 placeholder-slate-400 text-slate-900 rounded-xl focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm transition-all shadow-sm"
                            placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Confirm</label>
                        <input name="confirm_password" type="password" required
                            class="appearance-none relative block w-full px-4 py-3 border border-slate-300 placeholder-slate-400 text-slate-900 rounded-xl focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm transition-all shadow-sm"
                            placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-primary hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all transform hover:-translate-y-0.5 shadow-lg">
                    Daftar Akun
                </button>
            </div>

            <p class="text-xs text-center text-slate-400 mt-4">
                Dengan mendaftar, Anda menyetujui Syarat & Ketentuan kami.
            </p>
        </form>
    </div>
</div>