<?php
if (!isLoggedIn()) {
    redirect('index.php?page=login');
}

// Handle Simulated Payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simulate_payment'])) {
    $amount = intval($_POST['amount']);
    if ($amount > 0) {
        try {
            $pdo->beginTransaction();

            // 1. Insert into Topups with 'approved' status
            // Note: We use 'approved' because the payment is successful
            $stmt = $pdo->prepare("INSERT INTO topups (user_id, amount, status) VALUES (?, ?, 'approved')");
            $stmt->execute([$_SESSION['user_id'], $amount]);

            // 2. Update User's Token Balance immediately
            $updateStmt = $pdo->prepare("UPDATE users SET tokens = tokens + ? WHERE id = ?");
            $updateStmt->execute([$amount, $_SESSION['user_id']]);

            $pdo->commit();

            $_SESSION['flash_message'] = "Pembayaran Berhasil! $amount Token telah ditambahkan ke akun Anda.";
            $_SESSION['flash_type'] = "success";

            // Refresh to see update
            redirect('index.php?page=topup');

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash_message'] = "Terjadi kesalahan saat memproses pembayaran: " . $e->getMessage();
            $_SESSION['flash_type'] = "error";
        }
    } else {
        $_SESSION['flash_message'] = "Jumlah token tidak valid.";
        $_SESSION['flash_type'] = "error";
    }
}
?>

<div class="max-w-5xl mx-auto">
    
    <!-- Header Hero -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-serif font-bold text-slate-900 mb-4">Isi Ulang Token</h1>
        <p class="text-lg text-slate-500 max-w-2xl mx-auto">Dapatkan token dengan mudah dan aman untuk mengakses ribuan koleksi buku digital kami.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left: Top Up Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden">
                
                <!-- Decorative BG for Form -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-full -z-0"></div>
                
                <h2 class="text-2xl font-bold text-slate-800 mb-6 relative z-10 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    Pilih Nominal
                </h2>

                <!-- Form wrapper (prevent default submit to show modal) -->
                <form id="topupForm" class="relative z-10 space-y-8" onsubmit="event.preventDefault(); showPaymentModal();">
                    
                    <!-- Quick Select Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php
                        $packages = [
                            ['amount' => 50, 'price' => 50000, 'popular' => false],
                            ['amount' => 100, 'price' => 100000, 'popular' => true],
                            ['amount' => 250, 'price' => 250000, 'popular' => false],
                            ['amount' => 500, 'price' => 500000, 'popular' => false],
                            ['amount' => 1000, 'price' => 1000000, 'popular' => false],
                            ['amount' => 2000, 'price' => 2000000, 'popular' => false],
                        ];
                        foreach ($packages as $pkg):
                            ?>
                            <label class="cursor-pointer group relative">
                                <input type="radio" name="selected_amount" value="<?= $pkg['amount'] ?>" class="peer sr-only" onchange="updateCustomAmount(this.value)">
                                <div class="p-6 rounded-2xl border-2 border-slate-100 bg-white hover:border-primary/50 peer-checked:border-primary peer-checked:bg-primary/5 transition-all text-center h-full flex flex-col items-center justify-center">
                                    <?php if ($pkg['popular']): ?>
                                            <div class="absolute -top-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[10px] uppercase font-bold px-3 py-1 rounded-full shadow-lg shadow-orange-500/30 tracking-wider">Terlaris</div>
                                    <?php endif; ?>
                                    <span class="text-3xl font-serif font-bold text-slate-800 group-hover:text-primary transition-colors"><?= $pkg['amount'] ?></span>
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Token</span>
                                    <span class="mt-3 text-sm font-semibold text-slate-500">Rp <?= number_format($pkg['price'], 0, ',', '.') ?></span>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Manual Input -->
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Atau Masukkan Nominal Lain</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                                <span class="text-slate-400 font-bold text-xl">🪙</span>
                            </div>
                            <input type="number" name="amount" id="amountInput" min="10" 
                                class="w-full pl-14 pr-6 py-4 bg-white border-2 border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none text-slate-900 font-bold text-2xl placeholder-slate-300 transition-all"
                                placeholder="0" required>
                            <div class="absolute inset-y-0 right-0 pr-6 flex items-center pointer-events-none">
                                <span class="text-slate-400 font-bold text-sm">TOKEN</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                        class="w-full py-5 bg-slate-900 hover:bg-primary text-white font-bold text-lg rounded-2xl shadow-xl shadow-slate-900/20 hover:shadow-primary/30 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Lanjut ke Pembayaran
                    </button>
                    <p class="text-center text-xs text-slate-400 mt-4 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Transaksi aman & terenskripsi
                    </p>
                </form>
            </div>
        </div>

        <!-- Right: Recent History -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-[2.5rem] p-6 shadow-xl shadow-slate-200/50 border border-slate-100 h-full">
                <h3 class="font-serif font-bold text-xl text-slate-900 mb-6 px-2">Riwayat Transaksi</h3>
                
                <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM topups WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
                    $stmt->execute([$_SESSION['user_id']]);
                    $history = $stmt->fetchAll();
                    ?>

                    <?php if (empty($history)): ?>
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-slate-400 text-sm">Belum ada riwayat transaksi.</p>
                            </div>
                    <?php else: ?>
                            <?php foreach ($history as $h): ?>
                                    <div class="group p-4 rounded-2xl border border-slate-100 hover:border-primary/30 hover:bg-slate-50 transition-all">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-bold text-slate-800 text-lg">🪙 <?= number_format($h['amount']) ?></span>
                                            <?php
                                            $statusConfig = match ($h['status']) {
                                                'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' => 'Berhasil'],
                                                'rejected' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'label' => 'Gagal'],
                                                default => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Pending']
                                            };
                                            ?>
                                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide <?= $statusConfig['bg'] ?> <?= $statusConfig['text'] ?>">
                                                <?= $statusConfig['label'] ?>
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center text-xs text-slate-400">
                                            <span>ID: #<?= str_pad($h['id'], 6, '0', STR_PAD_LEFT) ?></span>
                                            <span><?= date('d M, H:i', strtotime($h['created_at'])) ?></span>
                                        </div>
                                    </div>
                            <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Simulated Payment Modal -->
<div id="paymentModal" class="fixed inset-0 z-[100] hidden">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closePaymentModal()"></div>
    
    <!-- Modal Content -->
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up">
        
        <!-- Header -->
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Payment Gateway Simulator
            </h3>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-rose-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-8">
            <div class="text-center mb-8">
                <p class="text-slate-500 text-sm mb-1">Total Pembayaran</p>
                <h2 class="text-3xl font-bold text-slate-900" id="modalAmountDisplay">Rp 0</h2>
                <div class="mt-4 bg-yellow-50 text-yellow-700 text-xs px-4 py-2 rounded-lg border border-yellow-100 inline-block">
                    ⚡ Mode Simulasi: Tidak ada uang asli yang dipotong
                </div>
            </div>

            <div class="space-y-3 mb-8">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Pilih Metode Pembayaran</p>
                
                <label class="flex items-center gap-4 p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-primary hover:bg-slate-50 transition-all group">
                    <input type="radio" name="payment_method" class="peer sr-only" checked>
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold">B</div>
                    <div class="flex-grow">
                        <p class="font-bold text-slate-800 text-sm">Virtual Account Bank</p>
                        <p class="text-xs text-slate-400">BCA, Mandiri, BNI, BRI</p>
                    </div>
                    <div class="w-4 h-4 rounded-full border border-slate-300 peer-checked:bg-primary peer-checked:border-primary"></div>
                </label>

                <label class="flex items-center gap-4 p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-primary hover:bg-slate-50 transition-all group">
                    <input type="radio" name="payment_method" class="peer sr-only">
                    <div class="w-10 h-10 bg-green-50 text-green-600 rounded-lg flex items-center justify-center font-bold">QR</div>
                    <div class="flex-grow">
                        <p class="font-bold text-slate-800 text-sm">QRIS / E-Wallet</p>
                        <p class="text-xs text-slate-400">GoPay, OVO, ShopeePay</p>
                    </div>
                    <div class="w-4 h-4 rounded-full border border-slate-300 peer-checked:bg-primary peer-checked:border-primary"></div>
                </label>
            </div>

            <button onclick="processPayment()" id="payButton"
                class="w-full py-4 bg-primary hover:bg-amber-700 text-white font-bold rounded-xl shadow-lg shadow-primary/30 transition-all flex items-center justify-center gap-2">
                Bayar Sekarang
            </button>
        </div>
    </div>
</div>

<!-- Actual Hidden Form for Submission -->
<form method="POST" id="realSubmitForm">
    <input type="hidden" name="amount" id="realAmountInput">
    <input type="hidden" name="simulate_payment" value="true">
</form>

<script>
    function updateCustomAmount(value) {
        document.getElementById('amountInput').value = value;
    }

    function showPaymentModal() {
        const amount = document.getElementById('amountInput').value;
        if (!amount || amount < 10) {
            alert('Minimal top up 10 Token');
            return;
        }

        // Calculate Price (1 Token = Rp 1.000)
        const price = amount * 1000;
        document.getElementById('modalAmountDisplay').innerText = 'Rp ' + price.toLocaleString('id-ID');
        document.getElementById('realAmountInput').value = amount;
        
        document.getElementById('paymentModal').classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    function processPayment() {
        const btn = document.getElementById('payButton');
        const originalText = btn.innerHTML;
        
        // Loading State
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses Transaksi...
        `;

        // Simulate 2s delay
        setTimeout(() => {
            btn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Pembayaran Berhasil!
            `;
            btn.classList.remove('bg-primary', 'hover:bg-amber-700');
            btn.classList.add('bg-green-500');

            setTimeout(() => {
                document.getElementById('realSubmitForm').submit();
            }, 1000);
        }, 2000);
    }
</script>