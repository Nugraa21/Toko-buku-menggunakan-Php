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

<div class="auth-container glass">
    <h2 style="text-align: center; margin-bottom: 2rem;">Daftar Akun</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" class="form-input" required
                value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" required
                value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-input" required>
        </div>
        <div class="form-group">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="confirm_password" class="form-input" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Daftar</button>
    </form>

    <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
        Sudah punya akun? <a href="index.php?page=login" style="color: var(--primary);">Login disini</a>
    </p>
</div>