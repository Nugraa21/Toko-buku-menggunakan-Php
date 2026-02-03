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

<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <!-- Left: Request Form -->
        <div class="md:col-span-2">
            <div
                class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden mb-8">
                <!-- Decor Circles -->
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-5"></div>
                <div
                    class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 rounded-full bg-amber-500 opacity-20 blur-xl">
                </div>

                <h2 class="text-2xl font-bold mb-2 relative z-10">Isi Ulang Token</h2>
                <p class="text-slate-300 mb-8 relative z-10">Dapatkan token untuk mulai membeli buku favoritmu.</p>

                <form method="POST" class="relative z-10">
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Pilih
                            Nominal</label>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" onclick="document.getElementById('amountInput').value=50"
                                class="px-4 py-3 bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl font-bold transition-all text-sm">50</button>
                            <button type="button" onclick="document.getElementById('amountInput').value=100"
                                class="px-4 py-3 bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl font-bold transition-all text-sm">100</button>
                            <button type="button" onclick="document.getElementById('amountInput').value=200"
                                class="px-4 py-3 bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl font-bold transition-all text-sm">200</button>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Atau Input
                            Manual</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-slate-400 font-bold">🪙</span>
                            </div>
                            <input type="number" name="amount" id="amountInput"
                                class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-white font-bold text-lg placeholder-slate-500 transition-all"
                                placeholder="0" min="10" required>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-xl shadow-lg shadow-orange-900/20 transition-all transform hover:-translate-y-1">
                        Kirim Permintaan Top Up
                    </button>
                </form>
            </div>

            <div class="bg-amber-50 border border-amber-100 rounded-xl p-6 flex gap-4 items-start">
                <div
                    class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 font-bold">
                    i</div>
                <div>
                    <h4 class="font-bold text-amber-900 mb-1">Informasi</h4>
                    <p class="text-sm text-amber-800 leading-relaxed">
                        Top up Anda akan berstatus <strong>Pending</strong> sampai Admin menyetujuinya. Silakan hubungi
                        Admin jika butuh proses cepat.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right: History -->
        <div class="md:col-span-1">
            <h3 class="text-lg font-bold text-slate-800 mb-4 px-2">Riwayat Top Up</h3>
            <div
                class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden max-h-[600px] overflow-y-auto">
                <?php
                $stmt = $pdo->prepare("SELECT * FROM topups WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
                $stmt->execute([$_SESSION['user_id']]);
                $history = $stmt->fetchAll();
                ?>

                <?php if (empty($history)): ?>
                    <div class="p-8 text-center text-slate-400 text-sm">Belum ada riwayat top up.</div>
                <?php else: ?>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($history as $h): ?>
                            <div class="p-4 hover:bg-slate-50 transition-colors">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-bold text-slate-700">🪙 <?= number_format($h['amount']) ?></span>
                                    <?php
                                    $statusClass = match ($h['status']) {
                                        'approved' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-amber-100 text-amber-700'
                                    };
                                    $statusLabel = ucfirst($h['status']);
                                    ?>
                                    <span class="px-2 py-0.5 rounded text-xs font-bold <?= $statusClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400"><?= date('d M Y • H:i', strtotime($h['created_at'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>