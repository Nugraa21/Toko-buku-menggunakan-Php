<?php
if (!isLoggedIn()) {
    redirect('index.php?page=login');
}

// Handle Refund Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_refund'])) {
    $amount = intval($_POST['amount']);
    $bank_name = $_POST['bank_name'];
    $bank_account = $_POST['bank_account'];
    $account_holder = $_POST['account_holder'];
    $reason = $_POST['reason'];

    if ($amount > 0 && !empty($bank_name) && !empty($bank_account) && !empty($account_holder)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO refunds (user_id, amount, bank_name, bank_account, account_holder, reason) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $amount, $bank_name, $bank_account, $account_holder, $reason]);

            $_SESSION['flash_message'] = "Permintaan pengembalian dana berhasil dikirim. Admin akan segera memprosesnya.";
            $_SESSION['flash_type'] = "success";
            redirect('index.php?page=topup_history'); // Refresh to show in history
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "Gagal mengirim permintaan: " . $e->getMessage();
            $_SESSION['flash_type'] = "error";
        }
    } else {
        $_SESSION['flash_message'] = "Mohon lengkapi semua data.";
        $_SESSION['flash_type'] = "error";
    }
}
?>

<div class="max-w-6xl mx-auto mb-12 px-4">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm font-bold text-slate-400 mb-6">
        <a href="index.php?page=topup" class="hover:text-primary transition-colors">Top Up</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd" />
        </svg>
        <span class="text-slate-800">Riwayat & Pengembalian</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Left: Refund Form -->
        <div class="lg:col-span-4 order-2 lg:order-1">
            <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100 sticky top-4">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-lg">Ajukan Pengembalian</h3>
                        <p class="text-xs text-slate-500">Kelebihan transfer? Ajukan refund.</p>
                    </div>
                </div>

                <form method="POST" class="space-y-4">
                    <input type="hidden" name="submit_refund" value="true">

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Jumlah
                            Token</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">🪙</span>
                            <input type="number" name="amount" id="refundTokenInput"
                                class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-rose-500 font-bold text-slate-800"
                                placeholder="0" required oninput="calculateRefundMoney(this.value)">
                        </div>
                        <p class="text-xs text-slate-500 mt-2 text-right" id="refundMoneyPreview">Estimasi: Rp 0</p>
                    </div>

                    <script>
                        function calculateRefundMoney(val) {
                            const money = val * 1000;
                            document.getElementById('refundMoneyPreview').innerText = 'Estimasi: Rp ' + money.toLocaleString('id-ID');
                        }
                    </script>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Bank
                            Tujuan</label>
                        <select name="bank_name"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-rose-500 text-slate-800 font-bold"
                            required>
                            <option value="">Pilih Bank</option>
                            <option value="BCA">BCA</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="BNI">BNI</option>
                            <option value="BRI">BRI</option>
                            <option value="Gopay">Gopay</option>
                            <option value="OVO">OVO</option>
                            <option value="Dana">Dana</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">No.
                                Rekening</label>
                            <input type="text" name="bank_account"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-rose-500 font-bold text-slate-800"
                                placeholder="123xxx" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Atas
                                Nama</label>
                            <input type="text" name="account_holder"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-rose-500 font-bold text-slate-800"
                                placeholder="Nama" required>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Alasan</label>
                        <textarea name="reason"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-rose-500 font-medium text-slate-800 h-24 resize-none"
                            placeholder="Contoh: Kelebihan transfer saat top up..." required></textarea>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-rose-600 text-white font-bold rounded-xl shadow-lg shadow-rose-600/20 hover:bg-rose-700 transition-all flex items-center justify-center gap-2">
                        <span>Kirim Pengajuan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                </form>
            </div>
        </div>

        <!-- Right: History Columns -->
        <div class="lg:col-span-8 order-1 lg:order-2 space-y-8">

            <!-- Top Up History -->
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                <h2 class="text-2xl font-serif font-bold text-slate-900 mb-6 flex items-center gap-3">
                    <span class="text-2xl">🪙</span> Riwayat Top Up
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                <th class="pb-4 pl-4">ID & Tanggal</th>
                                <th class="pb-4">Nominal</th>
                                <th class="pb-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php
                            $stmt = $pdo->prepare("SELECT * FROM topups WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
                            $stmt->execute([$_SESSION['user_id']]);
                            $topups = $stmt->fetchAll();
                            ?>
                            <?php if (empty($topups)): ?>
                                <tr>
                                    <td colspan="3" class="p-8 text-center text-slate-400 italic">Belum ada riwayat top up.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($topups as $t): ?>
                                    <tr class="group hover:bg-slate-50 transition-colors">
                                        <td class="p-4">
                                            <div class="font-bold text-slate-700">#
                                                <?= str_pad($t['id'], 6, '0', STR_PAD_LEFT) ?>
                                            </div>
                                            <div class="text-xs text-slate-400">
                                                <?= date('d M Y, H:i', strtotime($t['created_at'])) ?>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <span class="font-black text-slate-800 text-lg">
                                                <?= number_format($t['amount']) ?>
                                            </span>
                                            <span class="text-xs font-bold text-slate-400">TKN</span>
                                        </td>
                                        <td class="p-4">
                                            <?php
                                            $statusClass = match ($t['status']) {
                                                'approved' => 'bg-emerald-100 text-emerald-700',
                                                'rejected' => 'bg-rose-100 text-rose-700',
                                                default => 'bg-amber-100 text-amber-700'
                                            };
                                            ?>
                                            <span
                                                class="px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wide <?= $statusClass ?>">
                                                <?= $t['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Refund History -->
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                <h2 class="text-2xl font-serif font-bold text-slate-900 mb-6 flex items-center gap-3">
                    <span
                        class="flex items-center justify-center w-8 h-8 rounded-full bg-rose-100 text-rose-500 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                    </span>
                    Riwayat Pengembalian
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                <th class="pb-4 pl-4">Tanggal</th>
                                <th class="pb-4">Bank & Rekening</th>
                                <th class="pb-4">Jumlah Token</th>
                                <th class="pb-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php
                            $stmt = $pdo->prepare("SELECT * FROM refunds WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
                            $stmt->execute([$_SESSION['user_id']]);
                            $refunds = $stmt->fetchAll();
                            ?>
                            <?php if (empty($refunds)): ?>
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-slate-400 italic">Belum ada pengajuan
                                        pengembalian dana.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($refunds as $r): ?>
                                    <tr class="group hover:bg-slate-50 transition-colors">
                                        <td class="p-4 text-sm font-bold text-slate-600">
                                            <?= date('d M Y', strtotime($r['created_at'])) ?>
                                        </td>
                                        <td class="p-4">
                                            <div class="font-bold text-slate-800 text-sm">
                                                <?= htmlspecialchars($r['bank_name']) ?>
                                            </div>
                                            <div class="text-xs text-slate-500 font-mono">
                                                <?= htmlspecialchars($r['bank_account']) ?>
                                            </div>
                                            <div class="text-[10px] text-slate-400">
                                                <?= htmlspecialchars($r['account_holder']) ?>
                                            </div>
                                        </td>
                                        <td class="p-4 font-bold text-slate-800">
                                            🪙 <?= number_format($r['amount']) ?>
                                            <div class="text-[10px] text-slate-500 font-normal">Est: Rp <?= number_format($r['amount'] * 1000) ?></div>
                                        </td>
                                        <td class="p-4">
                                            <?php
                                            $rStatusClass = match ($r['status']) {
                                                'approved' => 'bg-emerald-100 text-emerald-700',
                                                'rejected' => 'bg-rose-100 text-rose-700',
                                                default => 'bg-amber-100 text-amber-700'
                                            };
                                            ?>
                                            <span
                                                class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide <?= $rStatusClass ?>">
                                                <?= $r['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>