# BookStore Token

Aplikasi toko buku online berbasis token dengan sistem autentikasi, manajemen keranjang, dan panel admin lengkap. Dibuat dengan PHP Native, MySQL, dan CSS Modern (Saweria Theme).

## 🚀 Fitur

### User (Pengguna)
-   **Autentikasi**: Login, Register, Logout.
-   **Dashboard**: Melihat katalog buku.
-   **Profile User**: Edit Nama, Email, dan Password.
-   **Transaksi**:
    -   Topup Token (Request ke Admin).
    -   Beli Buku (Add to Cart, Checkout).
    -   Riwayat Transaksi & Topup.

### Admin
-   **Dashboard**: Ringkasan transaksi dan request topup.
-   **Manajemen User**: Tambah, Edit, Hapus User.
-   **Manajemen Buku**: Tambah, Edit, Hapus Buku, Update Stok/Harga.
-   **Manajemen Topup**: Approve/Reject permintaan topup user.

## 🛠️ Instalasi

1.  **Clone / Download** repository ini.
2.  **Pindahkan** folder ke `c:\xampp\htdocs\autentikasi`.
3.  **Import Database**:
    -   Buka PHPMyAdmin (`http://localhost/phpmyadmin`).
    -   Buat database baru bernama `autentikasi`.
    -   Import file `data.sql` yang ada di folder project.
4.  **Jalankan**:
    -   Buka Browser dan akses `http://localhost/autentikasi`.

## 📂 Struktur Folder

-   `assets/`: File CSS, Gambar, JS.
    -   `css/style.css`: Styling utama (Light Theme).
-   `config/`: Konfigurasi Database.
-   `includes/`: Header, Footer, Functions.
-   `pages/`: Semua halaman fungsional (Home, Login, Admin, dll).
-   `index.php`: Router utama.

## 🔑 Akun Default

| Role  | Email           | Password |
| :---: | :-------------- | :------- |
| Admin | admin@toko.com  | password |
| User  | user@toko.com   | password |

## 🎨 UI/UX Design

Desain menggunakan tema "Light Glassmorphism" yang terinspirasi dari Saweria.co.
-   **Primary Color**: Yellow/Orange/Amber.
-   **Background**: Clean White/Light Gray.
-   **Font**: Outfit (Google Fonts).

---
Developed for learning purposes.
