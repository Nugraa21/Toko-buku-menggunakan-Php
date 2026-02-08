<?php
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Handle Filters
$status_filter = $_GET['status'] ?? 'all';
$page_num = $_GET['p'] ?? 1;
$limit = 10;
$offset = ($page_num - 1) * $limit;

// Build Query
$query = "SELECT t.*, u.name as user_name, u.email as user_email 
          FROM topups t 
          JOIN users u ON t.user_id = u.id";
$params = [];

if ($status_filter !== 'all') {
    $query .= " WHERE t.status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY t.created_at DESC LIMIT $limit OFFSET $offset";

// Fetch Data
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$topups = $stmt->fetchAll();

// Get Total Count for Pagination
$count_query = "SELECT COUNT(*) FROM topups t";
if ($status_filter !== 'all') {
    $count_query .= " WHERE t.status = ?";
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
            <h2 class="text-4xl font-serif font-bold text-slate-900 mb-2">Manajemen Top Up</h2>
            <p class="text-slate-500 font-sans">Kelola permintaan isi ulang token pengguna.</p>
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
        <a href="index.php?page=admin_topup&status=all"
            class="px-4 py-2 rounded-xl font-bold text-sm transition-colors whitespace-nowrap <?= $status_filter === 'all' ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
            Semua
        </a>
        <a href="index.php?page=admin_topup&status=pending"
            class="px-4 py-2 rounded-xl font-bold text-sm transition-colors whitespace-nowrap <?= $status_filter === 'pending' ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/30' : 'bg-white text-slate-600 border border-slate-200 hover:bg-amber-50 hover:text-amber-600' ?>">
            Menunggu (Pending)
        </a>
        <a href="index.php?page=admin_topup&status=approved"
            class="px-4 py-2 rounded-xl font-bold text-sm transition-colors whitespace-nowrap <?= $status_filter === 'approved' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-white text-slate-600 border border-slate-200 hover:bg-emerald-50 hover:text-emerald-600' ?>">
            Diterima (Approved)
        </a>
        <a href="index.php?page=admin_topup&status=rejected"
            class="px-4 py-2 rounded-xl font-bold text-sm transition-colors whitespace-nowrap <?= $status_filter === 'rejected' ? 'bg-rose-600 text-white shadow-lg shadow-rose-600/30' : 'bg-white text-slate-600 border border-slate-200 hover:bg-rose-50 hover:text-rose-600' ?>">
            Ditolak (Rejected)
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
                        <th class="p-6">User</th>
                        <th class="p-6">Nominal</th>
                        <th class="p-6">Status</th>
                        <th class="p-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($topups)): ?>
                        <tr>
                            <td colspan="5" class="p-12 text-center text-slate-400">
                                Tidak ada data yang ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($topups as $t): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-6">
                                    <div class="font-bold text-slate-700">#
                                        <?= str_pad($t['id'], 6, '0', STR_PAD_LEFT) ?>
                                    </div>
                                    <div class="text-xs text-slate-400 font-mono mt-1">
                                        <?= date('d M Y, H:i', strtotime($t['created_at'])) ?>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">
                                            <?= strtoupper(substr($t['user_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">
                                                <?= htmlspecialchars($t['user_name']) ?>
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                <?= htmlspecialchars($t['user_email']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span class="font-bold text-lg text-primary flex items-center gap-1">
                                        <span>🪙</span>
                                        <?= number_format($t['amount']) ?>
                                    </span>
                                </td>
                                <td class="p-6">
                                    <?php
                                    $statusClasses = [
                                        'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'rejected' => 'bg-rose-100 text-rose-700 border-rose-200',
                                    ];
                                    $label = ucfirst($t['status']);
                                    $class = $statusClasses[$t['status']] ?? 'bg-slate-100 text-slate-700';
                                    ?>
                                    <span class="px-3 py-1 rounded-lg text-xs font-bold border <?= $class ?>">
                                        <?= $label ?>
                                    </span>
                                </td>
                                <td class="p-6 text-right">
                                    <?php if ($t['status'] === 'pending'): ?>
                                        <div class="flex justify-end gap-2">
                                            <form method="POST" action="index.php?page=admin_action"
                                                onsubmit="return confirm('Setujui Top Up ini? Token akan ditambahkan ke user.');">
                                                <input type="hidden" name="action" value="approve_topup">
                                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                                <input type="hidden" name="redirect" value="admin_topup">
                                                <button type="submit"
                                                    class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 border border-emerald-200 shadow-sm transition-all"
                                                    title="Terima">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                            <form method="POST" action="index.php?page=admin_action"
                                                onsubmit="return confirm('Tolak Top Up ini?');">
                                                <input type="hidden" name="action" value="reject_topup">
                                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                                <input type="hidden" name="redirect" value="admin_topup">
                                                <button type="submit"
                                                    class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 border border-rose-200 shadow-sm transition-all"
                                                    title="Tolak">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 italic">Selesai</span>
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
                    <a href="index.php?page=admin_topup&status=<?= $status_filter ?>&p=<?= $i ?>"
                        class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-bold transition-all <?= $i == $page_num ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>