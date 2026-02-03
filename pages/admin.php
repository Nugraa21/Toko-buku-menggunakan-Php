<?php
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Fetch Pending Topups
$stmt = $pdo->query("
    SELECT t.*, u.name as user_name, u.email as user_email
    FROM topups t
    JOIN users u ON t.user_id = u.id
    WHERE t.status = 'pending'
    ORDER BY created_at ASC
");
$topups = $stmt->fetchAll();

// Fetch Recent Transactions
$stmt = $pdo->query("
    SELECT t.*, u.name as user_name, u.email as user_email,
           (SELECT COUNT(*) FROM transaction_items WHERE transaction_id = t.id) as item_count
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    ORDER BY transaction_date DESC
    LIMIT 10
");
$transactions = $stmt->fetchAll();
?>

<h2 style="margin-bottom: 2rem;">Dashboard Admin</h2>

<div class="glass" style="padding: 2rem; margin-bottom: 2rem;">
    <h3 style="margin-bottom: 1rem;">Permintaan Top Up (Pending)</h3>
    <?php if (empty($topups)): ?>
        <p>Tidak ada permintaan top up baru.</p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topups as $t): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($t['user_name']) ?></strong><br>
                            <small><?= htmlspecialchars($t['user_email']) ?></small>
                        </td>
                        <td>🪙 <?= number_format($t['amount']) ?></td>
                        <td><?= date('d M H:i', strtotime($t['created_at'])) ?></td>
                        <td>
                            <form method="POST" action="index.php?page=admin_action" style="display:inline;">
                                <input type="hidden" name="action" value="approve_topup">
                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                <button type="submit" class="btn btn-primary"
                                    style="padding: 5px 10px; font-size: 0.8rem; background: #10b981;">Terima</button>
                            </form>
                            <form method="POST" action="index.php?page=admin_action" style="display:inline;">
                                <input type="hidden" name="action" value="reject_topup">
                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                <button type="submit" class="btn btn-primary"
                                    style="padding: 5px 10px; font-size: 0.8rem; background: #ef4444;">Tolak</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="glass" style="padding: 2rem; overflow-x: auto;">
    <h3 style="margin-bottom: 1rem;">Transaksi Terakhir</h3>
    <table class="cart-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $t): ?>
                <tr>
                    <td>#<?= $t['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($t['user_name']) ?></strong><br>
                        <small><?= htmlspecialchars($t['user_email']) ?></small>
                    </td>
                    <td><?= date('d M Y H:i', strtotime($t['transaction_date'])) ?></td>
                    <td>🪙 <?= number_format($t['total_tokens']) ?></td>
                    <td>
                        <span style="color: #10b981;">Completed</span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>