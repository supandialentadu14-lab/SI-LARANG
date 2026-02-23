# 📦 Sistem Manajemen Inventaris

Aplikasi Manajemen Inventaris Modern yang dibangun dengan **Laravel 12**, dirancang untuk membantu bisnis mengelola stok, produk, dan laporan secara efisien. Sistem ini memiliki antarmuka yang responsif, fitur pelaporan lengkap, dan notifikasi stok real-time.

![Dashboard Preview](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)
*(Ganti gambar ini dengan screenshot aplikasi Anda nanti)*

## ✨ Fitur Utama

-   **Dashboard Interaktif**: Ringkasan stok, total produk, kategori, dan grafik pergerakan stok (6 bulan terakhir).
-   **Manajemen Produk**: CRUD Produk lengkap dengan upload gambar, scan barcode (SKU), dan harga.
-   **Kontrol Stok Otomatis**:
    -   Transaksi masuk/keluar tercatat otomatis saat produk dibuat atau diedit.
    -   Input manual untuk stok masuk (pembelian) dan keluar (penjualan/rusak).
-   **Notifikasi Cerdas**: Pemberitahuan real-time (ikon lonceng berkedip) jika ada produk yang stoknya menipis (di bawah batas minimum).
-   **Laporan Siap Cetak**: Halaman laporan khusus yang diformat rapi untuk dicetak (Print Friendly).
-   **Manajemen Kategori & Supplier**: Pengelompokan produk dan data pemasok.
-   **Manajemen Pengguna (ACL)**:
    -   **Admin**: Akses penuh (termasuk kelola user lain).
    -   **Staff**: Akses operasional (produk, stok, laporan).
-   **Antarmuka Modern**: Menggunakan Tailwind CSS dengan desain "SB Admin" yang bersih dan responsif (Mobile Friendly).
-   **Bahasa Indonesia**: Antarmuka sepenuhnya menggunakan Bahasa Indonesia.

## 🛠️ Teknologi yang Digunakan

-   **Backend**: [Laravel 12](https://laravel.com)
-   **Frontend**: [Blade Templates](https://laravel.com/docs/blade)
-   **Styling**: [Tailwind CSS](https://tailwindcss.com) (CDN)
-   **Interaktivitas**: [Alpine.js](https://alpinejs.dev)
-   **Grafik**: [Chart.js](https://www.chartjs.org)
-   **Database**: MySQL

## 🚀 Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek di komputer lokal Anda:

### Prasyarat
-   PHP >= 8.2
-   Composer
-   MySQL

### Langkah Instalasi

1.  **ekstrak file jika dari zip** 
    ```bash

    cd inventory
    ```

2.  **Instal Dependensi PHP**
    ```bash
    composer install
    ```

3.  **Konfigurasi Environment**
    -   Salin file `.env.example` menjadi `.env`:
        ```bash
        cp .env.example .env
        ```
    -   Buka file `.env` dan sesuaikan konfigurasi database Anda:
        ```env
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=inventory
        DB_USERNAME=root
        DB_PASSWORD=
        ```

4.  **Generate Key Aplikasi**
    ```bash
    php artisan key:generate
    ```

5.  **Jalankan Migrasi Database**
    ```bash
    php artisan migrate
    ```

6.  **Tautkan Penyimpanan (PENTING untuk Gambar)**
    Agar gambar produk muncul, Anda wajib menjalankan perintah ini:
    ```bash
    php artisan storage:link
    ```

7.  **Jalankan Server Lokal**
    ```bash
    php artisan serve
    ```

    Akses aplikasi di: `http://127.0.0.1:8000`

## 👤 Akun Demo (Jika Menggunakan Data Dummy)

Jika Anda melakukan seeding database, gunakan akun berikut:

-   **Email**: `admin@example.com`
-   **Password**: `password`

## 📂 Struktur Folder Penting

-   `app/Http/Controllers`: Logika backend (ProductController, StockController, dll).
-   `resources/views`: Tampilan antarmuka (Blade templates).
-   `resources/views/layouts/admin.blade.php`: Layout utama dashboard.
-   `routes/web.php`: Definisi rute aplikasi.
-   `public/storage`: Tempat penyimpanan gambar produk (setelah di-link).

## 📄 Lisensi

**Hak Cipta Dilindungi.** Aplikasi ini **BUKAN** perangkat lunak open-source.

🚫 **DILARANG KERAS** untuk:
1.  Memperjualbelikan ulang aplikasi atau kode sumber (source code) ini dalam bentuk apapun.
2.  Mendistribusikan ulang kepada pihak lain tanpa izin tertulis.
3.  Menggunakan aset atau bagian dari kode untuk produk komersial lain tanpa lisensi resmi.

Penggunaan hanya diizinkan untuk tujuan pribadi atau internal instansi pengguna yang sah.
