<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Digunakan jika ingin fitur verifikasi email
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Model autentikasi bawaan Laravel
use Illuminate\Notifications\Notifiable; // Untuk fitur notifikasi (email, dll)

/**
 * Model User
 * Digunakan untuk autentikasi (login, logout, dll)
 * Terhubung dengan tabel: users
 */
class User extends Authenticatable
{
    /**
     * Trait:
     * - HasFactory → untuk seeding & testing
     * - Notifiable → agar user bisa menerima notifikasi
     */
    use HasFactory, Notifiable;

    /**
     * Field yang boleh diisi menggunakan mass assignment
     * (create / update)
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password', // Password user
        'role',     // Role user (admin / staff)
    ];

    /**
     * Method untuk mengecek apakah user adalah admin
     * Bisa dipanggil dengan:
     * auth()->user()->isAdmin();
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Field yang disembunyikan saat model diubah menjadi array / JSON
     * (contoh: API response)
     */
    protected $hidden = [
        'password',       // Password tidak boleh terlihat
        'remember_token', // Token "remember me"
    ];

    /**
     * Casting tipe data otomatis
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // otomatis jadi object Carbon
            'password' => 'hashed',            // otomatis di-hash saat disimpan
        ];
    }
}
