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

<div class="auth-container glass">
    <h2 style="text-align: center; margin-bottom: 2rem;">Login</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" required placeholder="user@toko.com">
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-input" required placeholder="******">
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Masuk</button>
    </form>

    <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
        Belum punya akun? <a href="index.php?page=register" style="color: var(--primary);">Daftar disini</a>
    </p>
</div>