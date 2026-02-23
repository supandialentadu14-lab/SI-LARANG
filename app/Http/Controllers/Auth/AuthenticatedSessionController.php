<?php

// Menentukan namespace / lokasi class ini berada
namespace App\Http\Controllers\Auth;

// Mengimpor Controller utama Laravel
use App\Http\Controllers\Controller;

// Mengimpor LoginRequest yang berisi validasi dan proses autentikasi
use App\Http\Requests\Auth\LoginRequest;

// Digunakan untuk tipe return berupa redirect
use Illuminate\Http\RedirectResponse;

// Digunakan untuk menangkap request biasa
use Illuminate\Http\Request;

// Facade Auth digunakan untuk proses login & logout
use Illuminate\Support\Facades\Auth;

// Digunakan untuk tipe return berupa View (tampilan)
use Illuminate\View\View;

// Membuat class AuthenticatedSessionController
// Class ini mengatur login dan logout user
class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function create(): View
    {
        // Mengembalikan tampilan login yang ada di:
        // resources/views/auth/login.blade.php
        return view('auth.login');
    }

    /**
     * Menangani proses autentikasi (login)
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Menjalankan proses autentikasi
        // Mengecek email & password ke database
        // Jika gagal akan kembali ke login dengan error
        $request->authenticate();

        // Membuat session ID baru untuk keamanan
        // Mencegah serangan session fixation
        $request->session()->regenerate();
        $request->session()->flash('collapse_submenus', true);
        if (auth()->user() && method_exists(auth()->user(), 'isAdmin') && !auth()->user()->isAdmin()) {
            $request->session()->forget([
                'nota_current', 'nota_current_id',
                'bap_current', 'bap_current_id',
                'penerimaan_current', 'penerimaan_current_id',
                'belanja_modal_current', 'belanja_modal_current_id',
                'opname_current', 'opname_current_id',
                'pinjam_pakai_current', 'pinjam_pakai_current_id',
                'kwitansi_current',
            ]);
        }
        // Admin mengikuti 'intended' (jika sebelumnya akses halaman admin)
        // Staff selalu diarahkan ke dashboard agar tidak terjebak ke halaman admin-only
        if (auth()->user() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }
        return redirect()->route('dashboard');
    }

    /**
     * Proses logout (menghapus sesi login)
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logout user dari guard 'web' (default guard Laravel)
        Auth::guard('web')->logout();

        // Menghapus seluruh data session
        $request->session()->invalidate();

        // Membuat ulang CSRF token baru untuk keamanan
        $request->session()->regenerateToken();

        // Setelah logout, arahkan ke halaman utama
        return redirect('/');
    }
}
