# 📚 BookStore Token App

Aplikasi Web Toko Buku Modern berbasis PHP Native dengan sistem pembayaran menggunakan **Token**.

## ✨ Fitur Utama
*   **Sistem Token**: Pengguna harus melakukan Top Up token sebelum bisa membeli buku.
*   **Admin Dashboard**:
    *   Konfirmasi Top Up Token (Approve/Reject).
    *   Melihat riwayat transaksi.
*   **User Features**:
    *   Login & Register.
    *   Katalog Buku dengan harga dalam Token.
    *   Shopping Cart (Keranjang).
    *   Checkout Otomatis (Saldo Token langsung terpotong).
    *   Riwayat Pembelian & Riwayat Top Up.
*   **UI Modern**: Tema gelap dengan gaya Glassmorphism.

## ⚙️ Instalasi
1.  **Requirement**: XAMPP (Apache & MySQL).
2.  **Database**:
    *   Buka phpMyAdmin (`http://localhost/phpmyadmin`).
    *   Import file `data.sql` yang ada di folder project.
    *   Atau jalankan query SQL di dalam `data.sql` secara manual. ini akan membuat DB `autentikasi` (atau `bookstore_db` sesuai edit terakhir).
3.  **Konfigurasi**:
    *   Pastikan file `config/database.php` sesuai dengan kredensial database Anda (default: root, tanpa password).
4.  **Jalankan**:
    *   Buka browser: `http://localhost/autentikasi`

## 👤 Akun Default
**Admin** (Bisa Approve Top Up)
*   Email: `admin@toko.com`
*   Pass: `12345`

**User Demo**
*   Email: `user@toko.com`
*   Pass: `12345`

## 🪙 Cara Menggunakan (User)
1.  Daftar akun baru.
2.  Login.
3.  Masuk ke menu **Top Up** (ikon koin kuning).
4.  Masukkan jumlah token yang diinginkan, klik Minta Top Up.
5.  Status akan *Pending* sampai Admin menyetujui.
6.  Setelah disetujui, saldo bertambah dan bisa belanja buku!

## 🛡️ Cara Menggunakan (Admin)
1.  Login dengan akun Admin.
2.  Masuk ke menu **Admin**.
3.  Di tabel "Permintaan Top Up", klik **Terima** untuk menambah saldo user.
