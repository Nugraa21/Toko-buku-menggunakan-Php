<?php
if (!isLoggedIn()) {
    redirect('index.php?page=login');
}

// Handle Simulated Payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simulate_payment'])) {

    // Simulate Processing Delay
    sleep(1);

    $amount = intval($_POST['amount']);
    if ($amount > 0) {
        try {
            // 1. Insert into Topups with 'pending' status for admin approval
            // REAL APPLICATION NOTE: In a real app, you would integrate a Payment Gateway API here (Midtrans, Xendit, Stripe)
            // status would be set based on the callback from the gateway. 
            // For this simulator, we set it to 'pending' because it requires manual admin approval as per previous logic.

            $stmt = $pdo->prepare("INSERT INTO topups (user_id, amount, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$_SESSION['user_id'], $amount]);

            $_SESSION['flash_message'] = "Permintaan Top Up berhasil dibuat. Silakan tunggu konfirmasi Admin.";
            $_SESSION['flash_type'] = "success";

            // Refresh to see update
            redirect('index.php?page=topup');

        } catch (Exception $e) {
            $_SESSION['flash_message'] = "Terjadi kesalahan: " . $e->getMessage();
            $_SESSION['flash_type'] = "error";
        }
    } else {
        $_SESSION['flash_message'] = "Jumlah token tidak valid.";
        $_SESSION['flash_type'] = "error";
    }
}
?>

<div class="max-w-7xl mx-auto mb-20 px-4 sm:px-6 lg:px-8">
    
    <!-- Header Hero -->
    <div class="text-center mb-16 relative">
        <h1 class="text-5xl md:text-6xl font-serif font-bold text-slate-900 mb-6 tracking-tight">
            Isi Ulang 
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-amber-600">Token</span>
        </h1>
        <p class="text-lg md:text-xl text-slate-500 max-w-2xl mx-auto font-sans leading-relaxed">
            Dapatkan akses eksklusif ke ribuan buku premium. Pilih paket token yang sesuai dengan kebutuhan literasimu.
        </p>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

        <!-- Left Column: Token Selection -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- Token Packages Card -->
            <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-[10rem] -z-0"></div>
                
                <h2 class="text-2xl font-bold text-slate-900 mb-8 relative z-10 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center shadow-lg shadow-slate-900/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Pilih Paket Token
                </h2>

                <form id="topupForm" class="relative z-10" onsubmit="event.preventDefault(); showPaymentModal();">
                    
                    <!-- Packages Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-5 mb-8">
                        <?php
                        $packages = [
                            ['amount' => 50, 'price' => 50000, 'popular' => false, 'tier' => 'STARTER', 'color' => 'slate'],
                            ['amount' => 100, 'price' => 100000, 'popular' => true, 'tier' => 'READER', 'color' => 'blue'],
                            ['amount' => 250, 'price' => 250000, 'popular' => false, 'tier' => 'SCHOLAR', 'color' => 'indigo'],
                            ['amount' => 500, 'price' => 500000, 'popular' => false, 'tier' => 'MASTER', 'color' => 'purple'],
                            ['amount' => 1000, 'price' => 1000000, 'popular' => false, 'tier' => 'ELITE', 'color' => 'rose'],
                            ['amount' => 2000, 'price' => 2000000, 'popular' => false, 'tier' => 'ULTIMATE', 'color' => 'amber'],
                        ];

                        $colors = [
                            'slate' => 'bg-slate-50 border-slate-100 peer-checked:bg-slate-900 peer-checked:text-white',
                            'blue' => 'bg-blue-50/50 border-blue-100 peer-checked:bg-blue-600 peer-checked:text-white',
                            'indigo' => 'bg-indigo-50/50 border-indigo-100 peer-checked:bg-indigo-600 peer-checked:text-white',
                            'purple' => 'bg-purple-50/50 border-purple-100 peer-checked:bg-purple-600 peer-checked:text-white',
                            'rose' => 'bg-rose-50/50 border-rose-100 peer-checked:bg-rose-600 peer-checked:text-white',
                            'amber' => 'bg-amber-50/50 border-amber-100 peer-checked:bg-amber-500 peer-checked:text-white',
                        ];

                        foreach ($packages as $pkg):
                            $activeClass = $colors[$pkg['color']];
                            ?>
                                <label class="cursor-pointer group relative">
                                    <input type="radio" name="selected_amount" value="<?= $pkg['amount'] ?>" class="peer sr-only" onchange="updateCustomAmount(this.value)">
                                
                                    <div class="h-full p-6 rounded-3xl border-2 transition-all duration-300 flex flex-col items-center justify-center text-center gap-2 hover:shadow-xl hover:-translate-y-1 <?= $activeClass ?>">
                                    
                                        <?php if ($pkg['popular']): ?>
                                                <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-amber-500 to-orange-600 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-lg shadow-orange-500/30 uppercase tracking-widest z-10 w-max">
                                                    Best Seller
                                                </div>
                                        <?php endif; ?>

                                        <div class="text-[10px] font-bold border border-current rounded-full px-2 py-0.5 opacity-60 uppercase tracking-widest mb-1 group-hover:opacity-100 transition-opacity">
                                            <?= $pkg['tier'] ?>
                                        </div>
                                    
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-4xl font-serif font-bold tracking-tight"><?= $pkg['amount'] ?></span>
                                            <span class="text-xs font-bold opacity-60">TKN</span>
                                        </div>
                                    
                                        <div class="mt-2 text-sm font-bold bg-white/20 backdrop-blur-sm rounded-lg px-3 py-1.5 w-full">
                                            Rp <?= number_format($pkg['price'], 0, ',', '.') ?>
                                        </div>
                                    </div>
                                </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Manual Input -->
                    <div class="relative group mb-8">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-slate-200 to-slate-300 rounded-2xl opacity-50 group-hover:opacity-100 transition duration-300 blur-sm"></div>
                        <div class="relative bg-white p-2 rounded-2xl flex items-center shadow-sm">
                            <div class="pl-4 pr-3 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <input type="number" name="amount" id="amountInput" min="10" 
                                class="w-full py-4 bg-transparent outline-none text-slate-900 font-bold text-lg placeholder-slate-400"
                                placeholder="Masukkan jumlah token manual..." required>
                            <div class="pr-6 text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:block">
                                Min. 10 Token
                            </div>
                        </div>
                    </div>

                    <!-- Submit Action -->
                    <button type="submit" 
                        class="w-full py-5 bg-slate-900 text-white font-bold text-lg rounded-2xl shadow-xl shadow-slate-900/20 hover:shadow-2xl hover:bg-black transition-all hover:-translate-y-1 flex items-center justify-center gap-3 group">
                        <span>Lanjut ke Pembayaran</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                </form>
            </div>
            
            <!-- Security Badge -->
            <div class="flex flex-wrap justify-center gap-6 opacity-60 grayscale hover:grayscale-0 transition-all duration-500 py-4">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-xs font-bold text-slate-600">Enkripsi 256-bit SSL</span>
                </div>
                 <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-xs font-bold text-slate-600">Verifikasi Otomatis</span>
                </div>
            </div>

        </div>

        <!-- Right Column: History -->
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col h-full min-h-[500px]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-lg text-slate-900">Riwayat Transaksi</h3>
                    <a href="index.php?page=history" class="p-2 bg-slate-50 text-slate-500 hover:text-primary rounded-xl transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                
                <div class="flex-grow overflow-y-auto space-y-3 pr-2 custom-scrollbar">
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM topups WHERE user_id = ? ORDER BY created_at DESC LIMIT 6");
                    $stmt->execute([$_SESSION['user_id']]);
                    $history = $stmt->fetchAll();
                    ?>

                    <?php if (empty($history)): ?>
                             <div class="h-full flex flex-col items-center justify-center text-center opacity-40">
                                 <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                 </div>
                                 <p class="font-bold text-slate-500">Belum ada aktivitas</p>
                                 <p class="text-xs text-slate-400 mt-1">Mulai top up token pertamamu!</p>
                            </div>
                    <?php else: ?>
                            <?php foreach ($history as $h): ?>
                                     <?php
                                     $statusStyle = match ($h['status']) {
                                         'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                         'rejected' => 'bg-rose-50 text-rose-600 border-rose-100',
                                         default => 'bg-amber-50 text-amber-600 border-amber-100'
                                     };
                                     $statusIcon = match ($h['status']) {
                                         'approved' => '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>',
                                         'rejected' => '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>',
                                         default => '<svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>'
                                     };
                                     ?>
                                    <div class="group relative bg-white border border-slate-100 rounded-2xl p-4 hover:border-slate-300 transition-colors cursor-default">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Top Up Token</p>
                                                <div class="font-black text-slate-900 text-lg flex items-center gap-1">
                                                    <?= number_format($h['amount']) ?>
                                                    <span class="text-sm font-normal text-slate-500">TKN</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide border <?= $statusStyle ?>">
                                                <?= $statusIcon ?>
                                                <?= $h['status'] ?>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center pt-2 border-t border-slate-50">
                                            <span class="text-[10px] font-mono text-slate-400 font-bold">#<?= str_pad($h['id'], 8, '0', STR_PAD_LEFT) ?></span>
                                            <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded"><?= date('d M, H:i', strtotime($h['created_at'])) ?></span>
                                        </div>
                                    </div>
                            <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Professional Payment Gateway Modal -->
<div id="paymentModal" class="fixed inset-0 z-[100] hidden overflow-hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closePaymentModal()"></div>
    
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-6xl h-[90vh] md:h-[800px] bg-white rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up flex flex-col md:flex-row border border-slate-200">
        
        <!-- Sidebar: Payment Methods (Left) -->
        <div class="w-full md:w-[350px] bg-slate-50 border-r border-slate-200 flex flex-col flex-shrink-0">
            <div class="p-6 border-b border-slate-200/60 bg-white">
                <h3 class="font-bold text-slate-900 mb-1">Metode Pembayaran</h3>
                <p class="text-xs text-slate-500 font-medium">Pilih salah satu metode yang tersedia</p>
            </div>
            
            <div class="flex-grow overflow-y-auto custom-scrollbar p-4 space-y-6">
                
                <!-- Section: Virtual Accounts -->
                <div>
                    <p class="px-2 mb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Virtual Account</p>
                    <div class="space-y-2">
                        <!-- BCA -->
                        <button onclick="selectMethod(this, 'bca')" class="w-full p-3 flex items-center gap-4 bg-white border border-slate-200 rounded-xl hover:border-primary/50 hover:shadow-md transition-all group text-left relative overflow-hidden outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            <div class="w-12 h-8 bg-blue-600 rounded flex items-center justify-center text-white font-bold text-[10px] flex-shrink-0">BCA</div>
                            <div class="flex-grow relative z-10">
                                <span class="block font-bold text-slate-700 text-sm group-hover:text-primary">BCA Virtual Account</span>
                                <span class="block text-[10px] text-slate-400">Verifikasi Otomatis</span>
                            </div>
                        </button>
                        <!-- Mandiri -->
                        <button onclick="selectMethod(this, 'mandiri')" class="w-full p-3 flex items-center gap-4 bg-white border border-slate-200 rounded-xl hover:border-primary/50 hover:shadow-md transition-all group text-left relative overflow-hidden outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            <div class="w-12 h-8 bg-blue-900 rounded flex items-center justify-center text-yellow-400 font-bold text-[10px] flex-shrink-0 border-b-4 border-yellow-400">BMRI</div>
                            <div class="flex-grow relative z-10">
                                <span class="block font-bold text-slate-700 text-sm group-hover:text-primary">Mandiri VA</span>
                                <span class="block text-[10px] text-slate-400">Verifikasi Otomatis</span>
                            </div>
                        </button>
                        <!-- BRI -->
                        <button onclick="selectMethod(this, 'bri')" class="w-full p-3 flex items-center gap-4 bg-white border border-slate-200 rounded-xl hover:border-primary/50 hover:shadow-md transition-all group text-left relative overflow-hidden outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            <div class="w-12 h-8 bg-blue-700 rounded flex items-center justify-center text-white font-bold text-[10px] flex-shrink-0">BRI</div>
                            <div class="flex-grow relative z-10">
                                <span class="block font-bold text-slate-700 text-sm group-hover:text-primary">BRIVA</span>
                                <span class="block text-[10px] text-slate-400">Verifikasi Otomatis</span>
                            </div>
                        </button>
                         <!-- BNI -->
                        <button onclick="selectMethod(this, 'bni')" class="w-full p-3 flex items-center gap-4 bg-white border border-slate-200 rounded-xl hover:border-primary/50 hover:shadow-md transition-all group text-left relative overflow-hidden outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            <div class="w-12 h-8 bg-orange-500 rounded flex items-center justify-center text-white font-bold text-[10px] flex-shrink-0">BNI</div>
                            <div class="flex-grow relative z-10">
                                <span class="block font-bold text-slate-700 text-sm group-hover:text-primary">BNI VA</span>
                                <span class="block text-[10px] text-slate-400">Verifikasi Otomatis</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Section: E-Wallets -->
                <div>
                    <p class="px-2 mb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-t border-slate-200 pt-4">E-Wallet & QRIS</p>
                    <div class="space-y-2">
                        <!-- QRIS -->
                        <button onclick="selectMethod(this, 'qris')" class="w-full p-3 flex items-center gap-4 bg-white border border-slate-200 rounded-xl hover:border-primary/50 hover:shadow-md transition-all group text-left relative overflow-hidden outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            <div class="w-12 h-8 bg-slate-800 rounded flex items-center justify-center text-white font-bold text-[10px] flex-shrink-0">QRIS</div>
                            <div class="flex-grow relative z-10">
                                <span class="block font-bold text-slate-700 text-sm group-hover:text-primary">QRIS</span>
                                <span class="block text-[10px] text-slate-400">Gopay, OVO, Dana, LinkAja</span>
                            </div>
                        </button>
                        <!-- ShopeePay -->
                         <button onclick="selectMethod(this, 'shopeepay')" class="w-full p-3 flex items-center gap-4 bg-white border border-slate-200 rounded-xl hover:border-primary/50 hover:shadow-md transition-all group text-left relative overflow-hidden outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            <div class="w-12 h-8 bg-orange-500 rounded flex items-center justify-center text-white font-bold text-[10px] flex-shrink-0">SPay</div>
                            <div class="flex-grow relative z-10">
                                <span class="block font-bold text-slate-700 text-sm group-hover:text-primary">ShopeePay</span>
                                <span class="block text-[10px] text-slate-400">App Redirect</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Section: Retail -->
                <div>
                    <p class="px-2 mb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-t border-slate-200 pt-4">Gerai Retail</p>
                    <div class="space-y-2">
                        <!-- Indomaret -->
                        <button onclick="selectMethod(this, 'indomaret')" class="w-full p-3 flex items-center gap-4 bg-white border border-slate-200 rounded-xl hover:border-primary/50 hover:shadow-md transition-all group text-left relative overflow-hidden outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            <div class="w-12 h-8 bg-blue-600 rounded flex items-center justify-center text-white font-bold text-[10px] flex-shrink-0 border-b-2 border-red-500 relative"><span class="absolute top-0 w-full h-1/3 bg-red-500"></span>Ind</div>
                            <div class="flex-grow relative z-10">
                                <span class="block font-bold text-slate-700 text-sm group-hover:text-primary">Indomaret</span>
                                <span class="block text-[10px] text-slate-400">Bayar di Kasir</span>
                            </div>
                        </button>
                        <!-- Alfamart -->
                        <button onclick="selectMethod(this, 'alfamart')" class="w-full p-3 flex items-center gap-4 bg-white border border-slate-200 rounded-xl hover:border-primary/50 hover:shadow-md transition-all group text-left relative overflow-hidden outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            <div class="w-12 h-8 bg-red-600 rounded flex items-center justify-center text-white font-bold text-[10px] flex-shrink-0 relative overflow-hidden"><span class="absolute right-0 w-1/3 h-full bg-yellow-400 transform skew-x-12"></span>Alfa</div>
                            <div class="flex-grow relative z-10">
                                <span class="block font-bold text-slate-700 text-sm group-hover:text-primary">Alfamart</span>
                                <span class="block text-[10px] text-slate-400">Bayar di Kasir</span>
                            </div>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Main Content: Details (Right) -->
        <div class="flex-grow bg-white flex flex-col relative">
            
            <!-- Top Bar -->
            <div class="h-20 border-b border-slate-100 flex items-center justify-between px-8 bg-white z-20">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Pembayaran</p>
                    <h2 class="text-2xl font-black text-slate-900 font-serif leading-none mt-1" id="gatewayAmount">Rp 0</h2>
                </div>
                <button onclick="closePaymentModal()" class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content Area -->
            <div class="flex-grow p-8 md:p-12 overflow-y-auto bg-slate-50/30" id="paymentDetails">
                <!-- Initial Empty State -->
                <div class="h-full flex flex-col items-center justify-center text-center opacity-50 select-none">
                    <div class="bg-white p-6 rounded-full shadow-sm mb-6 animate-pulse border border-slate-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Pilih metode pembayaran</h3>
                    <p class="text-sm text-slate-400 max-w-xs mx-auto">Silakan pilih metode pembayaran yang tersedia di menu sebelah kiri untuk melanjutkan transaksi Anda.</p>
                </div>
            </div>

            <!-- Bottom Action Bar -->
            <div class="p-6 border-t border-slate-100 bg-white z-30 hidden shadow-[0_-5px_20px_rgba(0,0,0,0.02)]" id="paymentAction">
                <div class="flex items-center gap-4">
                    <div class="flex-grow hidden md:block">
                        <p class="text-xs font-bold text-slate-400">Batas Waktu Pembayaran</p>
                        <p class="text-sm font-bold text-rose-500 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            23 Jam 59 Menit
                        </p>
                    </div>
                    <button onclick="confirmPayment()" class="flex-grow md:flex-grow-0 md:w-auto w-full px-8 py-4 bg-slate-900 text-white font-bold rounded-xl shadow-lg shadow-slate-900/10 hover:bg-primary hover:shadow-primary/30 transition-all flex items-center justify-center gap-3">
                        <span>Saya Sudah Membayar</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button onclick="closePaymentModal()" class="hidden md:flex px-6 py-4 border border-slate-200 text-slate-500 font-bold rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all">
                        Batal
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Hidden Form to Submit -->
<form method="POST" id="hiddenSubmitForm">
    <input type="hidden" name="amount" id="hiddenAmount">
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
        
        const price = amount * 1000;
        document.getElementById('gatewayAmount').innerText = 'Rp ' + price.toLocaleString('id-ID');
        document.getElementById('hiddenAmount').value = amount;
        
        // Reset Modal State
        document.getElementById('paymentModal').classList.remove('hidden');
        document.getElementById('paymentAction').classList.add('hidden');
        
        // Reset Active Buttons
         document.querySelectorAll('#paymentModal button').forEach(b => {
             b.classList.remove('border-primary/50', 'ring-2', 'ring-primary', 'ring-offset-2', 'bg-blue-50');
         });

        // Reset Detail View
        const defaultView = `
             <div class="h-full flex flex-col items-center justify-center text-center opacity-50 select-none animate-fade-in-up">
                <div class="bg-white p-6 rounded-full shadow-sm mb-6 border border-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Pilih metode pembayaran</h3>
                <p class="text-sm text-slate-400 max-w-xs mx-auto">Silakan pilih metode pembayaran yang tersedia di menu sebelah kiri untuk melanjutkan transaksi Anda.</p>
            </div>
        `;
        document.getElementById('paymentDetails').innerHTML = defaultView;
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    function selectMethod(btn, method) {
        // Reset all buttons
        document.querySelectorAll('#paymentModal button').forEach(b => {
             b.classList.remove('border-primary', 'ring-2', 'ring-primary', 'ring-offset-2', 'bg-blue-50');
             b.classList.add('border-slate-200', 'bg-white');
        });
        
        // Highlight active button
        btn.classList.remove('border-slate-200', 'bg-white');
        btn.classList.add('border-primary', 'ring-2', 'ring-primary', 'ring-offset-2', 'bg-blue-50');

        // Render Content based on Method
        const details = document.getElementById('paymentDetails');
        const price = document.getElementById('gatewayAmount').innerText;
        
        // Random VA Generator
        const vaMap = {
            'bca': '8277', 'mandiri': '8902', 'bri': '8881', 'bni': '8005', 'shopeepay': '112', 'indomaret': 'IND', 'alfamart': 'ALF'
        };
        const prefix = vaMap[method] || '9999';
        const suffix = Math.floor(Math.random() * 10000000000).toString().padStart(10, '0');
        // Mask the middle part
        const maskedVA = prefix + ' ' + suffix.substring(0, 3) + 'xxxx ' + suffix.substring(7);
        const fullVA = prefix + suffix; // For copy functional if needed

        let content = '';

        if (method === 'qris') {
            content = `
                <div class="h-full flex flex-col items-center justify-center animate-fade-in-up">
                    <div class="bg-white p-8 rounded-3xl shadow-2xl shadow-slate-200 border border-slate-100 text-center relative overflow-hidden max-w-sm w-full">
                        <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-red-500 via-white to-red-500 opacity-50"></div>
                        
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/QR_code_for_mobile_English_Wikipedia.svg/1200px-QR_code_for_mobile_English_Wikipedia.svg.png" 
                             class="w-48 h-48 mx-auto mb-6 p-2 border-2 border-dashed border-slate-300 rounded-xl opacity-80" alt="QR Code">
                        
                        <p class="font-black text-slate-800 text-xl tracking-tight mb-1">Nugra21 Bookstore</p>
                        <p class="text-xs font-mono text-slate-400 mb-6">NMID: ID1020038829xxx</p>

                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 flex justify-between items-center mb-4">
                            <span class="text-xs font-bold text-slate-500 uppercase">Total Bayar</span>
                            <span class="font-black text-slate-900">${price}</span>
                        </div>

                        <div class="flex justify-center gap-4 grayscale opacity-60">
                             <div class="h-5 w-8 bg-slate-300 rounded"></div>
                             <div class="h-5 w-8 bg-slate-300 rounded"></div>
                             <div class="h-5 w-8 bg-slate-300 rounded"></div>
                        </div>
                    </div>
                    <p class="mt-6 text-sm font-bold text-slate-500 animate-pulse">Menunggu pembayaran...</p>
                </div>
            `;
        } else {
            // Virtual Account & Retail Layout
            const logoColor = method === 'mandiri' ? 'text-yellow-500' : (method === 'bni' || method === 'shopeepay' ? 'text-orange-500' : (method === 'alfamart' ? 'text-red-600' : 'text-blue-600'));
            
            content = `
                <div class="max-w-2xl mx-auto animate-fade-in-up space-y-8">
                    
                    <!-- Header Step -->
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center font-bold text-lg text-slate-500 border border-slate-200">1</div>
                        <div>
                            <h4 class="font-bold text-slate-900">Selesaikan Pembayaran</h4>
                            <p class="text-sm text-slate-500">Kirim dana ke nomor tujuan di bawah ini.</p>
                        </div>
                    </div>

                    <!-- VA Card -->
                    <div class="bg-white border text-center md:text-left border-slate-200 rounded-2xl p-6 md:p-8 shadow-sm relative overflow-hidden group hover:border-primary/30 transition-all">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 ${logoColor}" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </div>

                        <div class="relative z-10">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Nomor Virtual Account / Kode Bayar</p>
                            <div class="flex flex-col md:flex-row items-center gap-4">
                                <span class="font-mono font-bold text-3xl md:text-4xl text-slate-800 tracking-tight">${maskedVA}</span>
                                <button class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors uppercase tracking-wider">
                                    Salin
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Total Card -->
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 flex justify-between items-center">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Nominal Transfer</p>
                            <p class="text-xs text-rose-500 font-bold">*Jangan dibulatkan</p>
                        </div>
                        <span class="font-black text-2xl text-slate-900">${price}</span>
                    </div>

                    <!-- Instructions -->
                    <div>
                         <div class="flex items-center gap-4 mb-4">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center font-bold text-sm text-slate-500 border border-slate-200">2</div>
                            <h4 class="font-bold text-slate-700 text-sm">Cara Membayar</h4>
                        </div>
                        <div class="bg-white border border-slate-200 rounded-xl divide-y divide-slate-100 text-sm text-slate-600">
                             <div class="p-4 hover:bg-slate-50 cursor-pointer flex justify-between group/acc">
                                <span>ATM ${method.toUpperCase()}</span>
                                <svg class="w-5 h-5 text-slate-300 group-hover/acc:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                             </div>
                             <div class="p-4 hover:bg-slate-50 cursor-pointer flex justify-between group/acc">
                                <span>Mobile Banking</span>
                                <svg class="w-5 h-5 text-slate-300 group-hover/acc:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                             </div>
                             <div class="p-4 hover:bg-slate-50 cursor-pointer flex justify-between group/acc">
                                <span>Internet Banking</span>
                                <svg class="w-5 h-5 text-slate-300 group-hover/acc:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                             </div>
                        </div>
                    </div>

                </div>
            `;
        }

        details.innerHTML = content;
        document.getElementById('paymentAction').classList.remove('hidden');
    }

    function confirmPayment() {
        const btn = document.querySelector('#paymentAction button');
        btn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
        `;
        btn.disabled = true;
        btn.classList.add('opacity-80', 'cursor-not-allowed');
        
        // Submit after visual delay
        setTimeout(() => {
            document.getElementById('hiddenSubmitForm').submit();
        }, 1500);
    }
</script>