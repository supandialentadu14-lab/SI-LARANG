<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Menentukan guard dan password broker default.
    |
    | guard     → sistem autentikasi yang digunakan
    | passwords → konfigurasi reset password yang dipakai
    |
    */
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),          // Default guard: web (session-based login)
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'), // Default reset password config
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Guard menentukan bagaimana user diautentikasi.
    |
    | Driver:
    | - session → login berbasis session (umum untuk web)
    |
    | Provider menentukan dari mana data user diambil.
    |
    */
    'guards' => [
        'web' => [
            'driver' => 'session',   // Menggunakan session login
            'provider' => 'users',   // Mengambil data dari provider "users"
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Provider menentukan bagaimana user diambil dari database.
    |
    | driver:
    | - eloquent → menggunakan model Eloquent
    | - database → query langsung ke tabel
    |
    */
    'providers' => [
        'users' => [
            'driver' => 'eloquent', // Menggunakan Eloquent ORM
            'model' => env('AUTH_MODEL', App\Models\User::class), 
            // Model yang digunakan untuk autentikasi
        ],

        /*
        |--------------------------------------------------------------------------
        | Alternatif menggunakan query database langsung
        |--------------------------------------------------------------------------
        |
        | Tidak menggunakan model Eloquent
        |
        */
        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Mengatur sistem reset password.
    |
    | table     → tabel penyimpanan token reset
    | expire    → token berlaku berapa menit
    | throttle  → batas waktu request ulang token (detik)
    |
    */
    'passwords' => [
        'users' => [
            'provider' => 'users', 
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,    // Token berlaku 60 menit
            'throttle' => 60,  // User harus menunggu 60 detik sebelum request ulang
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Waktu (dalam detik) sebelum konfirmasi password kadaluarsa.
    |
    | Default: 10800 detik (3 jam)
    |
    | Digunakan untuk fitur seperti:
    | - Konfirmasi ulang sebelum mengubah password
    | - Akses halaman sensitif
    |
    */
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
