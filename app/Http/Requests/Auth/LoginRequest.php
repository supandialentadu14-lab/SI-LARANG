<?php

// Menentukan namespace lokasi file ini
namespace App\Http\Requests\Auth;

// Event yang dipanggil saat user terkena lockout (terlalu banyak percobaan login)
use Illuminate\Auth\Events\Lockout;

// FormRequest adalah class khusus Laravel untuk validasi request
use Illuminate\Foundation\Http\FormRequest;

// Digunakan untuk proses autentikasi login
use Illuminate\Support\Facades\Auth;

// Digunakan untuk membatasi jumlah percobaan login (rate limit)
use Illuminate\Support\Facades\RateLimiter;

// Digunakan untuk manipulasi string (lowercase, transliterate, dll)
use Illuminate\Support\Str;

// Digunakan untuk melempar error validasi
use Illuminate\Validation\ValidationException;

// Class LoginRequest menangani validasi dan proses login
class LoginRequest extends FormRequest
{
    /**
     * Menentukan apakah request ini diizinkan
     * true berarti semua user boleh mengakses request login
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk form login
     */
    public function rules(): array
    {
        return [
            // Email wajib diisi, berupa string dan format email
            'email' => ['required', 'string', 'email'],

            // Password wajib diisi dan berupa string
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Proses autentikasi login
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        // Pastikan user belum melebihi batas percobaan login
        $this->ensureIsNotRateLimited();

        // Coba login menggunakan email dan password
        // $this->only(...) → ambil hanya field email & password
        // $this->boolean('remember') → cek apakah remember me dicentang
        if (! Auth::attempt(
            $this->only('email', 'password'),
            $this->boolean('remember')
        )) {

            // Jika login gagal, tambahkan hit rate limiter
            RateLimiter::hit($this->throttleKey());

            // Lempar error validasi
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'), // Pesan: Email atau password salah
            ]);
        }

        // Jika login berhasil, reset hit rate limiter
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Memastikan login tidak melebihi batas percobaan
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Jika percobaan login masih di bawah 5 kali, lanjutkan
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        // Jika melebihi 5 kali, trigger event Lockout
        event(new Lockout($this));

        // Hitung sisa waktu tunggu sebelum bisa login lagi
        $seconds = RateLimiter::availableIn($this->throttleKey());

        // Lempar error validasi dengan pesan waktu tunggu
        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Membuat key unik untuk rate limiting
     * Berdasarkan email + IP address
     */
    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->input('email')) . '|' . $this->ip()
        );
    }
}
