<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | Menentukan mailer default yang digunakan untuk mengirim email.
    | Biasanya diatur melalui file .env
    |
    | Contoh:
    | MAIL_MAILER=smtp   → kirim email sungguhan
    | MAIL_MAILER=log    → hanya disimpan di log (untuk testing)
    |
    */
    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Di sini kita mendefinisikan semua jenis mailer yang bisa digunakan.
    | Laravel mendukung berbagai driver pengiriman email.
    |
    | Driver yang tersedia:
    | - smtp
    | - sendmail
    | - mailgun
    | - ses
    | - postmark
    | - resend
    | - log
    | - array
    | - failover
    | - roundrobin
    |
    */

    'mailers' => [

        /*
        |--------------------------------------------------------------------------
        | SMTP Mailer
        |--------------------------------------------------------------------------
        |
        | Digunakan untuk mengirim email melalui server SMTP.
        | Contoh: Gmail, Mailtrap, hosting server, dll.
        |
        */
        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'), // ssl / tls
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'), // server email
            'port' => env('MAIL_PORT', 2525), // port SMTP
            'username' => env('MAIL_USERNAME'), // username email
            'password' => env('MAIL_PASSWORD'), // password email
            'timeout' => null,
            'local_domain' => env(
                'MAIL_EHLO_DOMAIN',
                parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)
            ),
        ],

        /*
        |--------------------------------------------------------------------------
        | Amazon SES
        |--------------------------------------------------------------------------
        |
        | Digunakan jika menggunakan layanan email Amazon SES.
        |
        */
        'ses' => [
            'transport' => 'ses',
        ],

        /*
        |--------------------------------------------------------------------------
        | Postmark
        |--------------------------------------------------------------------------
        |
        | Digunakan jika menggunakan layanan Postmark.
        |
        */
        'postmark' => [
            'transport' => 'postmark',
        ],

        /*
        |--------------------------------------------------------------------------
        | Resend
        |--------------------------------------------------------------------------
        |
        | Digunakan jika menggunakan layanan Resend.
        |
        */
        'resend' => [
            'transport' => 'resend',
        ],

        /*
        |--------------------------------------------------------------------------
        | Sendmail
        |--------------------------------------------------------------------------
        |
        | Menggunakan program sendmail bawaan server Linux.
        |
        */
        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        /*
        |--------------------------------------------------------------------------
        | Log Mailer
        |--------------------------------------------------------------------------
        |
        | Email tidak benar-benar dikirim.
        | Isinya hanya dicatat di file log.
        | Cocok untuk development/testing.
        |
        */
        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        /*
        |--------------------------------------------------------------------------
        | Array Mailer
        |--------------------------------------------------------------------------
        |
        | Email disimpan dalam array (tidak dikirim).
        | Biasanya digunakan untuk testing otomatis.
        |
        */
        'array' => [
            'transport' => 'array',
        ],

        /*
        |--------------------------------------------------------------------------
        | Failover Mailer
        |--------------------------------------------------------------------------
        |
        | Jika mailer pertama gagal, maka Laravel akan mencoba
        | mailer berikutnya.
        |
        | Contoh:
        | Coba smtp → jika gagal → simpan ke log
        |
        */
        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
            'retry_after' => 60,
        ],

        /*
        |--------------------------------------------------------------------------
        | Round Robin Mailer
        |--------------------------------------------------------------------------
        |
        | Digunakan untuk membagi pengiriman email secara bergantian
        | ke beberapa layanan (load balancing).
        |
        */
        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
            'retry_after' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | Semua email yang dikirim dari aplikasi akan menggunakan
    | alamat dan nama ini sebagai pengirim default.
    |
    | Diatur melalui .env:
    | MAIL_FROM_ADDRESS=
    | MAIL_FROM_NAME=
    |
    */
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

];
