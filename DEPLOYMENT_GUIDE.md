# Panduan Deployment SI-LARANG (Sistem Informasi Pengelolaan Persediaan Barang)

Panduan ini akan membantu Anda mengonlinekan aplikasi Laravel ini agar bisa diakses oleh orang lain.

## Persiapan Sebelum Upload

Sebelum mengupload file ke hosting, pastikan Anda melakukan hal berikut di komputer lokal (laptop/PC Anda):

1.  **Build Aset Frontend (CSS/JS)**
    Aplikasi ini menggunakan Vite. Anda harus men-generate file CSS dan JS produksi.
    Buka terminal di folder proyek dan jalankan:
    ```bash
    npm install
    npm run build
    ```
    *Ini akan membuat folder `public/build` yang berisi file CSS dan JS yang sudah diminifikasi.*

2.  **Siapkan Database**
    - Export database lokal Anda (misalnya dari phpMyAdmin atau TablePlus) ke file `.sql`.
    - Pastikan struktur tabel dan data awal (seperti user admin) sudah benar.

3.  **Arsipkan Proyek**
    - Compress/Zip semua file dalam folder proyek **KECUALI** folder `node_modules`.
    - Folder `vendor` BISA diikutkan jika Anda menggunakan Shared Hosting (agar tidak perlu run `composer install` di server), TAPI lebih baik jika di-install ulang di server jika memungkinkan.
    - **Rekomendasi untuk Shared Hosting:** Zip semua file termasuk `vendor`, tapi JANGAN sertakan `node_modules` dan `.git`.

---

## Opsi 1: Deployment ke Shared Hosting (cPanel) - Paling Mudah

Ini adalah cara paling umum jika Anda menyewa hosting murah (misal: Niagahoster, Domainesia, IdCloudHost).

### Langkah 1: Upload File
1.  Login ke **cPanel** -> **File Manager**.
2.  Buat folder baru di luar `public_html`, misalnya beri nama `si-lapar-app`.
3.  Upload file `.zip` proyek Anda ke dalam folder `si-lapar-app` tersebut dan ekstrak.

### Langkah 2: Mengatur Folder Public
Laravel memiliki folder `public` yang seharusnya menjadi root website.
1.  Pindahkan **SEMUA** isi dari folder `si-lapar-app/public` ke folder `public_html` (atau folder domain/subdomain Anda).
2.  Edit file `index.php` yang baru saja Anda pindahkan ke `public_html`:
    Cari baris ini:
    ```php
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    ```
    Ubah menjadi (sesuaikan jalur ke folder `si-lapar-app`):
    ```php
    require __DIR__.'/../si-lapar-app/vendor/autoload.php';
    $app = require_once __DIR__.'/../si-lapar-app/bootstrap/app.php';
    ```

### Langkah 3: Konfigurasi Database
1.  Di cPanel, buka **MySQL Databases**.
2.  Buat Database baru (misal: `u12345_silarang`).
3.  Buat User Database baru dan passwordnya.
4.  Add User to Database (beri hak akses *All Privileges*).
5.  Buka **phpMyAdmin**, pilih database yang baru dibuat, lalu **Import** file `.sql` dari komputer Anda.

### Langkah 4: Konfigurasi Environment (.env)
1.  Kembali ke File Manager, buka folder `si-lapar-app`.
2.  Cari file `.env.example`, rename menjadi `.env` (atau edit file `.env` jika sudah ada).
3.  Edit file `.env` dan sesuaikan:
    ```ini
    APP_NAME="SI-LARANG"
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://domain-anda.com

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=u12345_silarang
    DB_USERNAME=u12345_user
    DB_PASSWORD=password_database_anda
    ```
4.  **PENTING:** Jangan lupa generate key baru jika belum ada, atau copy key dari lokal. Jika di hosting tidak ada terminal, Anda bisa copy `APP_KEY` dari file `.env` lokal Anda.

### Langkah 5: Symlink Storage (Agar gambar/file bisa diakses)
Di Shared Hosting, Anda seringkali tidak memiliki akses terminal SSH. Anda bisa membuat route khusus untuk menjalankan perintah artisan symlink.
1.  Buat route sementara di `routes/web.php`:
    ```php
    Route::get('/link-storage', function () {
        Artisan::call('storage:link');
        return 'Storage Linked!';
    });
    ```
2.  Akses `https://domain-anda.com/link-storage` sekali saja.
3.  Hapus route tersebut setelah selesai.

---

## Opsi 2: Deployment ke VPS (Ubuntu/Nginx) - Lebih Fleksibel

Jika Anda menggunakan VPS (Virtual Private Server) seperti DigitalOcean, Linode, atau AWS.

### Langkah 1: Persiapan Server
Pastikan server sudah terinstall:
- PHP 8.2+
- Nginx / Apache
- MySQL / MariaDB
- Composer
- NodeJS & NPM (opsional, untuk build di server)

### Langkah 2: Setup Aplikasi
1.  Clone repository atau upload file ke `/var/www/si-lapar`.
2.  Set permission:
    ```bash
    chown -R www-data:www-data /var/www/si-lapar
    chmod -R 775 /var/www/si-lapar/storage
    chmod -R 775 /var/www/si-lapar/bootstrap/cache
    ```
3.  Install dependency:
    ```bash
    cd /var/www/si-lapar
    composer install --optimize-autoloader --no-dev
    ```
4.  Setup .env:
    ```bash
    cp .env.example .env
    nano .env
    # Isi konfigurasi database dan APP_URL
    ```
5.  Generate key & migrate:
    ```bash
    php artisan key:generate
    php artisan migrate --force
    php artisan storage:link
    ```
6.  Cache config (untuk performa):
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

### Langkah 3: Konfigurasi Nginx
Buat file config Nginx baru: `/etc/nginx/sites-available/si-lapar`
```nginx
server {
    listen 80;
    server_name domain-anda.com;
    root /var/www/si-lapar/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; # Sesuaikan versi PHP
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
Aktifkan situs:
```bash
ln -s /etc/nginx/sites-available/si-lapar /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx
```

---

## Troubleshooting Umum

1.  **Error 500 (Server Error)**
    - Cek log di `storage/logs/laravel.log`.
    - Pastikan permission folder `storage` dan `bootstrap/cache` sudah 775 atau 777.

2.  **Tampilan Berantakan (CSS Hilang)**
    - Pastikan Anda sudah menjalankan `npm run build` sebelum upload.
    - Pastikan folder `public/build` ikut ter-upload.
    - Pastikan `APP_URL` di `.env` sudah sesuai dengan domain (https vs http).

3.  **Gambar Tidak Muncul**
    - Pastikan symlink storage sudah dibuat (`php artisan storage:link`).
    - Di shared hosting, pastikan folder `public/storage` adalah shortcut yang mengarah ke `storage/app/public`.
