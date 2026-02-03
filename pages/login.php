<?php
if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['flash_message'] = "Selamat datang, " . $user['name'];
        $_SESSION['flash_type'] = "success";
        redirect('index.php');
    } else {
        $error = "Email atau password salah.";
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2 class="text-center" style="margin-bottom: 2rem;">Masuk ke Akun</h2>

        <?php if (isset($error)): ?>
            <div class="badge badge-danger"
                style="display: block; text-align: center; margin-bottom: 1.5rem; padding: 0.75rem;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="badge badge-success"
                style="display: block; text-align: center; margin-bottom: 1.5rem; padding: 0.75rem;">
                <?= $_SESSION['flash_message'];
                unset($_SESSION['flash_message']); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="user@example.com">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Masuk Sekarang</button>
        </form>

        <p class="text-center" style="margin-top: 1.5rem; color: var(--text-muted);">
            Belum punya akun? <a href="index.php?page=register" style="font-weight: 600; color: var(--primary);">Daftar
                disini</a>
        </p>
    </div>
</div>