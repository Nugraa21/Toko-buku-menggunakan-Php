<?php
if (!isLoggedIn()) {
    redirect('index.php?page=login');
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT t.*, 
           (SELECT COUNT(*) FROM transaction_items WHERE transaction_id = t.id) as item_count
    FROM transactions t 
    WHERE user_id = ? 
    ORDER BY transaction_date DESC
");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll();
?>

<h2 style="margin-bottom: 2rem;">Riwayat Transaksi</h2>

<?php if (empty($transactions)): ?>
    <div class="glass" style="padding: 2rem; text-align: center;">
        <p>Belum ada transaksi pembelian buku.</p>
    </div>
<?php else: ?>
    <div class="glass" style="padding: 2rem; overflow-x: auto;">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Total Token</th>
                    <th>Status</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $t): ?>
                    <tr>
                        <td>#<?= $t['id'] ?></td>
                        <td><?= date('d M Y H:i', strtotime($t['transaction_date'])) ?></td>
                        <td>🪙 <?= number_format($t['total_tokens']) ?></td>
                        <td>
                            <span style="
                                padding: 5px 10px; 
                                border-radius: 20px; 
                                font-size: 0.8rem;
                                background: rgba(16, 185, 129, 0.2);
                                color: #10b981;
                            ">
                                Completed
                            </span>
                        </td>
                        <td><?= $t['item_count'] ?> Buku</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>