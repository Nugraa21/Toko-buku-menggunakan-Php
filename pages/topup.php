<?php
if (!isLoggedIn()) {
    redirect('index.php?page=login');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = intval($_POST['amount']);
    if ($amount > 0) {
        $stmt = $pdo->prepare("INSERT INTO topups (user_id, amount, status) VALUES (?, ?, 'pending')");
        if ($stmt->execute([$_SESSION['user_id'], $amount])) {
            $_SESSION['flash_message'] = "Permintaan Top Up sebesar $amount Token berhasil dikirim. Menunggu konfirmasi Admin.";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Gagal mengirim permintaan.";
            $_SESSION['flash_type'] = "error";
        }
    } else {
        $_SESSION['flash_message'] = "Jumlah token tidak valid.";
        $_SESSION['flash_type'] = "error";
    }
}
?>

<div class="auth-container glass">
    <h2 style="text-align: center; margin-bottom: 2rem;">Top Up Token</h2>
    <p style="text-align: center; color: var(--text-muted); margin-bottom: 2rem;">
        Isi ulang saldo token Anda untuk membeli buku favorit.
    </p>

    <form method="POST">
        <div class="form-group">
            <label class="form-label">Jumlah Token</label>
            <input type="number" name="amount" class="form-input" min="10" step="10" placeholder="Contoh: 100" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Minta Top Up</button>
    </form>
</div>

<div class="glass" style="margin-top: 2rem; padding: 2rem;">
    <h3>Riwayat Top Up</h3>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM topups WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$_SESSION['user_id']]);
    $history = $stmt->fetchAll();
    ?>
    <table class="cart-table" style="margin-top: 1rem;">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $h): ?>
                <tr>
                    <td>
                        <?= date('d M Y', strtotime($h['created_at'])) ?>
                    </td>
                    <td>🪙
                        <?= number_format($h['amount']) ?>
                    </td>
                    <td>
                        <span
                            style="color: <?= $h['status'] == 'approved' ? '#10b981' : ($h['status'] == 'pending' ? '#f59e0b' : '#ef4444') ?>">
                            <?= ucfirst($h['status']) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>