<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | Nama aplikasi kamu.
    | Biasanya akan tampil di:
    | - Email notifikasi
    | - Judul halaman
    | - UI tertentu
    |
    | Diambil dari file .env → APP_NAME
    |
    */
    'name' => env('APP_NAME', 'SI-LARANG'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | Menentukan environment aplikasi:
    | - local
    | - development
    | - staging
    | - production
    |
    | Biasanya diatur di file .env:
    | APP_ENV=local
    |
    */
    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | Jika true:
    | - Error detail akan ditampilkan
    | - Stack trace terlihat
    |
    | Jika false:
    | - Error hanya menampilkan halaman 500 biasa
    |
    | ⚠️ Jangan aktifkan di production!
    |
    */
    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | URL utama aplikasi kamu.
    | Digunakan untuk:
    | - Generate link di Artisan
    | - Email verification link
    | - Reset password linkity
    |
    */
    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Timezone default aplikasi.
    | Default Laravel = UTC
    |
    | Jika aplikasi kamu di Indonesia,
    | sebaiknya ubah ke:
    | 'Asia/Jakarta'
    |
    */
    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | Bahasa default aplikasi.
    | Default: en (English)
    |
    | Jika ingin Bahasa Indonesia:
    | APP_LOCALE=id
    |
    */
    'locale' => env('APP_LOCALE', 'en'),

    /*
    | Bahasa fallback jika terjemahan tidak ditemukan
    */
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    /*
    | Locale untuk Faker (digunakan saat seeding database)
    */
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | Kunci enkripsi aplikasi.
    |
    | Digunakan untuk:
    | - Encrypt data
    | - Session
    | - Cookie
    | - Password reset token
    |
    | Dibuat menggunakan:
    | php artisan key:generate
    |
    */
    'cipher' => 'AES-256-CBC',

    // Mengambil APP_KEY dari file .env
    'key' => env('APP_KEY'),

    /*
    | Previous encryption keys
    | Digunakan jika kamu melakukan rotasi key
    */
    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | Mengatur bagaimana maintenance mode disimpan.
    |
    | Driver:
    | - file  → default
    | - cache → cocok untuk multi server
    |
    | Contoh aktifkan maintenance:
    | php artisan down
    |
    | Nonaktifkan:
    | php artisan up
    |
    */
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
