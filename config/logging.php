<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | Menentukan channel log default yang digunakan Laravel.
    | Biasanya menggunakan "stack", yang berarti bisa menggabungkan
    | beberapa channel sekaligus.
    |
    | Diatur melalui .env → LOG_CHANNEL
    |
    */
    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | Digunakan untuk mencatat peringatan fitur PHP/library yang deprecated.
    | Berguna saat upgrade Laravel atau PHP.
    |
    */
    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Di sini kita mendefinisikan semua jenis logging yang tersedia.
    | Laravel menggunakan library Monolog untuk menangani logging.
    |
    | Driver yang tersedia:
    | - single   → satu file log
    | - daily    → log dipisah per hari
    | - slack    → kirim notifikasi ke Slack
    | - syslog   → sistem log server
    | - errorlog → log bawaan PHP
    | - stack    → gabungan beberapa channel
    |
    */

    'channels' => [

        /*
        |--------------------------------------------------------------------------
        | Stack Channel
        |--------------------------------------------------------------------------
        |
        | Menggabungkan beberapa channel sekaligus.
        | Misalnya: log ke file + kirim ke Slack.
        |
        */
        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', (string) env('LOG_STACK', 'single')),
            'ignore_exceptions' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | Single File Log
        |--------------------------------------------------------------------------
        |
        | Semua log disimpan dalam satu file:
        | storage/logs/laravel.log
        |
        | Cocok untuk development.
        |
        */
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Daily Log
        |--------------------------------------------------------------------------
        |
        | Log dibuat per hari.
        | File lama akan dihapus sesuai jumlah hari (default 14).
        |
        | Cocok untuk production agar file log tidak terlalu besar.
        |
        */
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => env('LOG_DAILY_DAYS', 14),
            'replace_placeholders' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Slack Log
        |--------------------------------------------------------------------------
        |
        | Mengirim log error penting langsung ke Slack.
        | Biasanya digunakan untuk level: critical atau error.
        |
        */
        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),
            'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Papertrail (Remote Logging)
        |--------------------------------------------------------------------------
        |
        | Mengirim log ke server eksternal seperti Papertrail.
        | Cocok untuk monitoring server production.
        |
        */
        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        /*
        |--------------------------------------------------------------------------
        | STDERR (Biasanya untuk Docker)
        |--------------------------------------------------------------------------
        |
        | Mengirim log ke standard error.
        | Cocok untuk container seperti Docker.
        |
        */
        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stderr',
            ],
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'processors' => [PsrLogMessageProcessor::class],
        ],

        /*
        |--------------------------------------------------------------------------
        | Syslog
        |--------------------------------------------------------------------------
        |
        | Mengirim log ke sistem logging server (Linux syslog).
        |
        */
        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
            'replace_placeholders' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Error Log (PHP Native)
        |--------------------------------------------------------------------------
        |
        | Menggunakan error log bawaan PHP.
        |
        */
        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Null Log
        |--------------------------------------------------------------------------
        |
        | Tidak menyimpan log sama sekali.
        | Biasanya untuk menonaktifkan logging tertentu.
        |
        */
        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        /*
        |--------------------------------------------------------------------------
        | Emergency Log
        |--------------------------------------------------------------------------
        |
        | Digunakan jika semua channel gagal.
        | Laravel akan tetap menyimpan log di file ini.
        |
        */
        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

    ],

];
