<footer class="bg-white border-t border-slate-200 pt-16 pb-8 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
            <!-- Brand -->
            <div>
                <h2 class="text-2xl font-bold text-slate-900 mb-4">BookStore<span class="text-primary">.</span></h2>
                <p class="text-slate-500 leading-relaxed">
                    Platform buku digital terpercaya dengan sistem pembayaran token yang aman dan instan. Akses
                    pengetahuan tanpa batas.
                </p>
                <div class="flex gap-4 mt-6">
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-primary hover:text-white transition-colors">
                        IG
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-primary hover:text-white transition-colors">
                        TW
                    </a>
                </div>
            </div>

            <!-- Links -->
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-4">Navigasi</h3>
                <ul class="space-y-3">
                    <li><a href="index.php" class="text-slate-500 hover:text-primary transition-colors">Beranda</a></li>
                    <li><a href="index.php?page=home#katalog"
                            class="text-slate-500 hover:text-primary transition-colors">Katalog Buku</a></li>
                    <li><a href="index.php?page=profile"
                            class="text-slate-500 hover:text-primary transition-colors">Akun Saya</a></li>
                    <li><a href="index.php?page=topup" class="text-slate-500 hover:text-primary transition-colors">Top
                            Up Token</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-4">Bantuan</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-slate-500 hover:text-primary transition-colors">Pusat Bantuan</a></li>
                    <li><a href="#" class="text-slate-500 hover:text-primary transition-colors">Syarat & Ketentuan</a>
                    </li>
                    <li><a href="#" class="text-slate-500 hover:text-primary transition-colors">Kebijakan Privasi</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-8 text-center">
            <p class="text-slate-400 text-sm">
                &copy; <?= date('Y') ?> BookStore Token App. Built with Tailwind CSS & PHP.
            </p>
        </div>
    </div>
</footer>

<script>
    // Simple script to auto-dismiss alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 4000);
</script>
</body>

</html>