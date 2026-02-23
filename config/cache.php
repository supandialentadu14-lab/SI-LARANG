<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | Menentukan driver cache default yang digunakan aplikasi.
    |
    | Diambil dari .env → CACHE_STORE
    |
    | Saat ini default: database
    | Artinya cache disimpan di tabel database.
    |
    */
    'default' => env('CACHE_STORE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Di sini didefinisikan semua jenis cache driver yang tersedia.
    | Kamu bisa memilih salah satu sesuai kebutuhan server.
    |
    | Supported drivers:
    | array, database, file, memcached, redis, dynamodb, octane, failover, null
    |
    */
    'stores' => [

        /*
        |--------------------------------------------------------------------------
        | Array Cache
        |--------------------------------------------------------------------------
        |
        | Cache hanya tersimpan selama request berlangsung.
        | Tidak disimpan permanen.
        | Cocok untuk testing.
        |
        */
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | Database Cache
        |--------------------------------------------------------------------------
        |
        | Cache disimpan dalam tabel database.
        | Perlu menjalankan:
        | php artisan cache:table
        | php artisan migrate
        |
        */
        'database' => [
            'driver' => 'database',
            'connection' => env('DB_CACHE_CONNECTION'),
            'table' => env('DB_CACHE_TABLE', 'cache'),
            'lock_connection' => env('DB_CACHE_LOCK_CONNECTION'),
            'lock_table' => env('DB_CACHE_LOCK_TABLE'),
        ],

        /*
        |--------------------------------------------------------------------------
        | File Cache
        |--------------------------------------------------------------------------
        |
        | Cache disimpan dalam folder:
        | storage/framework/cache/data
        |
        | Cocok untuk shared hosting.
        |
        */
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        /*
        |--------------------------------------------------------------------------
        | Memcached
        |--------------------------------------------------------------------------
        |
        | Digunakan untuk high-performance caching.
        | Biasanya untuk aplikasi besar.
        |
        */
        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Opsi tambahan jika diperlukan
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Redis
        |--------------------------------------------------------------------------
        |
        | Cache menggunakan Redis server.
        | Sangat cepat dan cocok untuk production skala besar.
        |
        */
        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
        ],

        /*
        |--------------------------------------------------------------------------
        | DynamoDB
        |--------------------------------------------------------------------------
        |
        | Digunakan jika hosting di AWS dan ingin cache di DynamoDB.
        |
        */
        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        /*
        |--------------------------------------------------------------------------
        | Octane
        |--------------------------------------------------------------------------
        |
        | Digunakan jika memakai Laravel Octane.
        |
        */
        'octane' => [
            'driver' => 'octane',
        ],

        /*
        |--------------------------------------------------------------------------
        | Failover Cache
        |--------------------------------------------------------------------------
        |
        | Jika store pertama gagal, akan otomatis pindah ke store berikutnya.
        | Di sini: database → array
        |
        */
        'failover' => [
            'driver' => 'failover',
            'stores' => [
                'database',
                'array',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix untuk semua cache key.
    | Berguna jika:
    | - Satu server dipakai beberapa aplikasi
    | - Menghindari bentrok cache
    |
    */
    'prefix' => env(
        'CACHE_PREFIX',
        Str::slug((string) env('APP_NAME', 'laravel')).'-cache-'
    ),

];
