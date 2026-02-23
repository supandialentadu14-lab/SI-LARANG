<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Menentukan disk default yang akan digunakan Laravel
    | untuk menyimpan file.
    |
    | Diambil dari .env → FILESYSTEM_DISK
    |
    | Biasanya untuk sistem inventory:
    | - local  → untuk file private
    | - public → untuk gambar produk
    |
    */
    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Di sini kita bisa mendefinisikan berbagai jenis penyimpanan file.
    | Laravel mendukung:
    | - local (server)
    | - ftp
    | - sftp
    | - s3 (cloud storage seperti AWS)
    |
    */

    'disks' => [

        /*
        |--------------------------------------------------------------------------
        | Local Disk (Private Storage)
        |--------------------------------------------------------------------------
        |
        | Digunakan untuk menyimpan file yang tidak bisa diakses publik.
        | Lokasi: storage/app/private
        |
        | Cocok untuk:
        | - Backup database
        | - Export laporan
        | - File internal sistem
        |
        */
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | Public Disk (Untuk Akses Publik)
        |--------------------------------------------------------------------------
        |
        | Digunakan untuk file yang bisa diakses melalui browser.
        | Biasanya untuk:
        | - Foto produk
        | - Logo supplier
        | - File yang ditampilkan di website
        |
        | Lokasi asli: storage/app/public
        | Diakses via: public/storage (setelah jalankan storage:link)
        |
        */
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | S3 (Cloud Storage)
        |--------------------------------------------------------------------------
        |
        | Digunakan jika ingin menyimpan file di cloud (AWS S3).
        | Cocok untuk production skala besar.
        |
        | Biasanya digunakan jika:
        | - Server kecil
        | - Banyak upload gambar produk
        | - Butuh performa tinggi
        |
        */
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Konfigurasi ini akan digunakan saat menjalankan:
    |
    | php artisan storage:link
    |
    | Perintah ini membuat shortcut:
    | public/storage → storage/app/public
    |
    | Tanpa ini, gambar produk tidak akan tampil di browser.
    |
    */
    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
