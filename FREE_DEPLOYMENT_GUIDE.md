# Panduan Deployment Gratis (Vercel + Neon)

Jika Anda ingin mengonlinekan aplikasi ini **tanpa biaya**, kombinasi **Vercel** (untuk aplikasi) dan **Neon** (untuk database) adalah pilihan terbaik saat ini.

## Persiapan

1.  Pastikan Anda memiliki akun **GitHub**.
2.  Upload/Push kode proyek ini ke repository GitHub Anda (Private atau Public).
3.  Pastikan Anda mendaftar akun di **Vercel.com** dan **Neon.tech** menggunakan akun GitHub tersebut.

---

## Langkah 1: Setup Database (Neon)

1.  Login ke **[Neon.tech](https://neon.tech)**.
2.  Klik **New Project**.
3.  Beri nama proyek (misal: `si-lapar-db`), pilih Region terdekat (misal: Singapore), lalu klik **Create Project**.
4.  Anda akan mendapatkan **Connection String**. Salin string tersebut (formatnya seperti `postgres://user:pass@host/db...`).
5.  Simpan string ini, kita akan menggunakannya nanti.

---

## Langkah 2: Setup Aplikasi (Vercel)

1.  Login ke **[Vercel.com](https://vercel.com)**.
2.  Klik **Add New...** -> **Project**.
3.  Pilih repository `si-lapar` dari GitHub Anda, lalu klik **Import**.
4.  Pada bagian **Environment Variables**, masukkan konfigurasi database dari Neon tadi:
    *   `DB_CONNECTION` : `pgsql`
    *   `DB_HOST` : (Host dari Neon, misal: `ep-mute-...aws.neon.tech`)
    *   `DB_PORT` : `5432`
    *   `DB_DATABASE` : `neondb` (atau nama database default Neon)
    *   `DB_USERNAME` : (Username dari Neon)
    *   `DB_PASSWORD` : (Password dari Neon)
    *   `APP_KEY` : (Copy dari file `.env` lokal Anda, format `base64:...`)
5.  Klik **Deploy**.

---

## Langkah 3: Migrasi Database

Karena Vercel adalah serverless (tidak ada terminal SSH yang persisten), kita perlu menjalankan migrasi database dari komputer lokal kita tetapi diarahkan ke database Neon.

1.  Buka terminal di komputer lokal Anda (di folder proyek ini).
2.  Edit file `.env` LOKAL Anda sementara (atau buat file `.env.production`):
    *   Ubah `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD` dengan kredensial dari **Neon**.
    *   Pastikan `DB_CONNECTION=pgsql`.
3.  Jalankan perintah migrasi:
    ```bash
    php artisan migrate --force
    ```
4.  (Opsional) Jika ingin mengisi data awal:
    ```bash
    php artisan db:seed --force
    ```
5.  **PENTING:** Setelah selesai, kembalikan konfigurasi `.env` lokal Anda ke database lokal (MySQL/localhost) agar Anda bisa melanjutkan development tanpa merusak data produksi.

---

## Catatan Tambahan

- **Storage:** Vercel memiliki sistem file "read-only" (kecuali folder `/tmp`). Artinya, fitur upload gambar (seperti foto profil atau bukti transaksi) **TIDAK AKAN BERTAHAN** lama.
- **Solusi Storage:** Untuk menyimpan gambar secara permanen di Vercel, Anda harus menggunakan layanan penyimpanan eksternal seperti **Cloudinary** (Gratis) atau **AWS S3**. Anda perlu menginstall library tambahan (`cloudinary-laravel`) dan mengatur driver filesystem di `config/filesystems.php`.
