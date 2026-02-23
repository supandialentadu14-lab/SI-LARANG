<?php

// Menentukan namespace (lokasi file dalam struktur Laravel)
namespace App\Http\Controllers\Auth;

// Mengimpor Controller utama Laravel
use App\Http\Controllers\Controller;

// Mengimpor Model User untuk menyimpan data user ke database
use App\Models\User;

// Event yang dijalankan ketika user berhasil registrasi
use Illuminate\Auth\Events\Registered;

// Digunakan untuk tipe return redirect
use Illuminate\Http\RedirectResponse;

// Digunakan untuk menangkap data request dari form
use Illuminate\Http\Request;

// Facade Auth untuk proses login otomatis
use Illuminate\Support\Facades\Auth;

// Facade Hash untuk mengenkripsi password
use Illuminate\Support\Facades\Hash;

// Digunakan untuk aturan validasi password bawaan Laravel
use Illuminate\Validation\Rules;

// Digunakan untuk tipe return View (tampilan)
use Illuminate\View\View;

// Controller untuk menangani proses registrasi user
class RegisteredUserController extends Controller
{
    /**
     * Menampilkan halaman registrasi
     */
    public function create(): View
    {
        // Menampilkan view register yang berada di:
        // resources/views/auth/register.blade.php
        return view('auth.register');
    }

    /**
     * Menangani proses pendaftaran user baru
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input dari form registrasi
        $request->validate([
            // Name wajib diisi, berupa string, maksimal 255 karakter
            'name' => ['required', 'string', 'max:255'],

            // Email wajib, format email, huruf kecil, unik di tabel users, maksimal 255 karakter
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],

            // Password wajib, harus ada konfirmasi (password_confirmation),
            // dan mengikuti aturan default password Laravel
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Membuat user baru di database
        $user = User::create([
            // Mengambil nama dari input form
            'name' => $request->name,

            // Mengambil email dari input form
            'email' => $request->email,

            // Password dienkripsi terlebih dahulu sebelum disimpan
            'password' => Hash::make($request->password),

            // Mengatur role default sebagai admin
            // (Biasanya ini hanya untuk setup awal aplikasi)
            'role' => 'admin',
        ]);

        // Memicu event Registered
        // Biasanya digunakan untuk mengirim email verifikasi
        event(new Registered($user));

        // Login otomatis setelah registrasi berhasil
        Auth::login($user);

        // Redirect ke halaman dashboard setelah berhasil daftar & login
        return redirect(route('dashboard', absolute: false));
    }
}
