<?php
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Handle Filters
$status_filter = $_GET['status'] ?? 'all';
$page_num = $_GET['p'] ?? 1;
$limit = 10;
$offset = ($page_num - 1) * $limit;

// Handle Actions (Approve/Reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['refund_action'])) {
    $refund_id = $_POST['refund_id'];
    $action = $_POST['refund_action'];
    $note = $_POST['admin_note'] ?? '';

    if ($action === 'approve') {
        try {
            $pdo->beginTransaction();

            // 1. Get Refund Details
            $stmt = $pdo->prepare("SELECT user_id, amount FROM refunds WHERE id = ?");
            $stmt->execute([$refund_id]);
            $refund = $stmt->fetch();

            if ($refund) {
                // 2. Calculate Token Deduction (Amount IS Token now)
                $tokens_to_deduct = $refund['amount'];
                $estimate_money = $tokens_to_deduct * 1000;

                // 3. Check User Balance
                $stmt = $pdo->prepare("SELECT tokens FROM users WHERE id = ?");
                $stmt->execute([$refund['user_id']]);
                $current_tokens = $stmt->fetchColumn();

                if ($current_tokens >= $tokens_to_deduct) {
                    // 4. Deduct Tokens
                    $updateUser = $pdo->prepare("UPDATE users SET tokens = tokens - ? WHERE id = ?");
                    $updateUser->execute([$tokens_to_deduct, $refund['user_id']]);

                    // 5. Update Refund Status
                    $updateRefund = $pdo->prepare("UPDATE refunds SET status = 'approved', admin_note = ? WHERE id = ?");
                    $updateRefund->execute([$note, $refund_id]);

                    $pdo->commit();
                    $_SESSION['flash_message'] = "Penukaran disetujui. " . number_format($tokens_to_deduct) . " Token telah ditarik. Silakan transfer Rp " . number_format($estimate_money);
                    $_SESSION['flash_type'] = "success";
                } else {
                    $pdo->rollBack();
                    $_SESSION['flash_message'] = "Gagal! User hanya memiliki " . number_format($current_tokens) . " Token (Butuh: " . number_format($tokens_to_deduct) . ").";
                    $_SESSION['flash_type'] = "error";
                }
            } else {
                $pdo->rollBack();
                $_SESSION['flash_message'] = "Data refund tidak ditemukan.";
                $_SESSION['flash_type'] = "error";
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash_message'] = "Terjadi kesalahan: " . $e->getMessage();
            $_SESSION['flash_type'] = "error";
        }

    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE refunds SET status = 'rejected', admin_note = ? WHERE id = ?");
        $stmt->execute([$note, $refund_id]);
        $_SESSION['flash_message'] = "Pengajuan Refund ditolak.";
        $_SESSION['flash_type'] = "success";
    }
    redirect('index.php?page=admin_refunds');
}

// Build Query
$query = "SELECT r.*, u.name as user_name, u.email as user_email 
          FROM refunds r 
          JOIN users u ON r.user_id = u.id";
$params = [];

if ($status_filter !== 'all') {
    $query .= " WHERE r.status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY r.created_at DESC LIMIT $limit OFFSET $offset";

// Fetch Data
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$refunds = $stmt->fetchAll();

// Get Total Count for Pagination
$count_query = "SELECT COUNT(*) FROM refunds r";
if ($status_filter !== 'all') {
    $count_query .= " WHERE r.status = ?";
}
$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($status_filter !== 'all' ? [$status_filter] : []);
$total_items = $count_stmt->fetchColumn();
$total_pages = ceil($total_items / $limit);

?>

<div class="max-w-7xl mx-auto mb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h2 class="text-4xl font-serif font-bold text-slate-900 mb-2">Manajemen Refund</h2>
            <p class="text-slate-500 font-sans">Kelola permintaan pengembalian dana pengguna.</p>
        </div>
        <div class="flex gap-3">
            <a href="index.php?page=admin"
                class="px-5 py-2.5 bg-white text-slate-600 rounded-xl border border-slate-200 hover:bg-slate-50 hover:text-primary font-medium transition-all shadow-sm flex items-center gap-2">
                Kembali
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
        <a href="index.php?page=admin_refunds&status=all"
            class="px-4 py-2 rounded-xl font-bold text-sm transition-colors whitespace-nowrap <?= $status_filter === 'all' ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
            Semua
        </a>
        <a href="index.php?page=admin_refunds&status=pending"
            class="px-4 py-2 rounded-xl font-bold text-sm transition-colors whitespace-nowrap <?= $status_filter === 'pending' ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/30' : 'bg-white text-slate-600 border border-slate-200 hover:bg-amber-50 hover:text-amber-600' ?>">
            Menunggu
        </a>
        <a href="index.php?page=admin_refunds&status=approved"
            class="px-4 py-2 rounded-xl font-bold text-sm transition-colors whitespace-nowrap <?= $status_filter === 'approved' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-white text-slate-600 border border-slate-200 hover:bg-emerald-50 hover:text-emerald-600' ?>">
            Disetujui
        </a>
        <a href="index.php?page=admin_refunds&status=rejected"
            class="px-4 py-2 rounded-xl font-bold text-sm transition-colors whitespace-nowrap <?= $status_filter === 'rejected' ? 'bg-rose-600 text-white shadow-lg shadow-rose-600/30' : 'bg-white text-slate-600 border border-slate-200 hover:bg-rose-50 hover:text-rose-600' ?>">
            Ditolak
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50/50 border-b border-slate-100 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="p-6">ID & Tanggal</th>
                        <th class="p-6">User & Rekening</th>
                        <th class="p-6">Token Penukaran</th>
                        <th class="p-6">Status</th>
                        <th class="p-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($refunds)): ?>
                        <tr>
                            <td colspan="5" class="p-12 text-center text-slate-400">
                                Tidak ada pengajuan penukaran.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($refunds as $r): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-6 align-top w-40">
                                    <div class="font-bold text-slate-700">#<?= str_pad($r['id'], 6, '0', STR_PAD_LEFT) ?></div>
                                    <div class="text-xs text-slate-400 font-mono mt-1"><?= date('d M Y', strtotime($r['created_at'])) ?></div>
                                    <div class="text-[10px] text-slate-400 font-mono"><?= date('H:i', strtotime($r['created_at'])) ?></div>
                                </td>
                                <td class="p-6 align-top">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500 flex-shrink-0">
                                            <?= strtoupper(substr($r['user_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900"><?= htmlspecialchars($r['user_name']) ?></div>
                                            <div class="text-xs text-slate-500 mb-2"><?= htmlspecialchars($r['user_email']) ?></div>
                                            
                                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 text-xs">
                                                <div class="font-bold text-slate-700 mb-0.5"><?= htmlspecialchars($r['bank_name']) ?></div>
                                                <div class="font-mono text-slate-600 mb-0.5"><?= htmlspecialchars($r['bank_account']) ?></div>
                                                <div class="text-slate-500 uppercase"><?= htmlspecialchars($r['account_holder']) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6 align-top">
                                    <div class="font-black text-rose-500 text-lg mb-1">
                                        🪙 <?= number_format($r['amount']) ?>
                                    </div>
                                    <div class="text-xs font-bold text-slate-400 mb-2">
                                        Est: Rp <?= number_format($r['amount'] * 1000) ?>
                                    </div>
                                    <div class="text-sm text-slate-600 italic bg-amber-50 p-2 rounded border border-amber-100">
                                        "<?= htmlspecialchars($r['reason']) ?>"
                                    </div>
                                </td>
                                <td class="p-6 align-top">
                                    <?php
                                    $statusClasses = [
                                        'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'rejected' => 'bg-rose-100 text-rose-700 border-rose-200',
                                    ];
                                    $label = ucfirst($r['status']);
                                    $class = $statusClasses[$r['status']] ?? 'bg-slate-100 text-slate-700';
                                    ?>
                                    <span class="px-3 py-1 rounded-lg text-xs font-bold border <?= $class ?>">
                                        <?= $label ?>
                                    </span>
                                </td>
                                <td class="p-6 text-right w-48 align-top">
                                    <?php if ($r['status'] === 'pending'): ?>
                                        <div class="flex flex-col gap-2">
                                            <form method="POST"
                                                onsubmit="return confirm('Setujui refund ini? Pastikan Anda sudah mentransfer manual ke rekening user.');">
                                                <input type="hidden" name="refund_id" value="<?= $r['id'] ?>">
                                                <input type="hidden" name="refund_action" value="approve">
                                                <input type="hidden" name="admin_note" value="Refund disetujui dan ditransfer.">
                                                <button type="submit"
                                                    class="w-full px-4 py-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white border border-emerald-200 shadow-sm transition-all text-xs font-bold flex items-center justify-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Setujui (Transfer)
                                                </button>
                                            </form>
                                            <form method="POST" onsubmit="return confirm('Tolak pengajuan refund ini?');">
                                                <input type="hidden" name="refund_id" value="<?= $r['id'] ?>">
                                                <input type="hidden" name="refund_action" value="reject">
                                                <input type="hidden" name="admin_note"
                                                    value="Data tidak valid atau tidak ditemukan.">
                                                <button type="submit"
                                                    class="w-full px-4 py-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white border border-rose-200 shadow-sm transition-all text-xs font-bold flex items-center justify-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 italic block mb-1">Selesai dieksekusi</span>
                                        <?php if ($r['status'] === 'approved'): ?>
                                            <div
                                                class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-2 py-1 rounded border border-emerald-100 inline-block">
                                                Transfer Done</div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-center gap-2">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="index.php?page=admin_refunds&status=<?= $status_filter ?>&p=<?= $i ?>"
                        class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-bold transition-all <?= $i == $page_num ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>