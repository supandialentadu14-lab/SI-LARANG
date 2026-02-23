<?php

// Menentukan namespace controller
namespace App\Http\Controllers;

// Mengimpor model User
use App\Models\User;

// Digunakan untuk tipe return redirect
use Illuminate\Http\RedirectResponse;

// Digunakan untuk menangkap request dari form
use Illuminate\Http\Request;

// Digunakan untuk mengenkripsi password
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// Digunakan untuk validasi unik saat update (ignore ID tertentu)
use Illuminate\Validation\Rule;

// Digunakan untuk tipe return View
use Illuminate\View\View;

// Controller untuk mengelola data user (CRUD + role)
class UserController extends Controller
{
    /**
     * Menampilkan daftar user
     */
    public function index(): View
    {
        // Mengambil data user terbaru
        // paginate(10) → tampilkan 10 data per halaman
        $users = User::latest()->paginate(10);

        // Kirim data ke view
        return view('users.index', compact('users'));
    }

    /**
     * Menampilkan form tambah user
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Menyimpan user baru ke database
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255', // Nama wajib
            'email' => 'required|string|email|max:255|unique:users', // Email wajib & unik
            'password' => 'required|string|min:8|confirmed', // Password min 8 & harus ada konfirmasi
            'role' => 'required|in:admin,staff', // Role hanya boleh admin atau staff
        ]);

        // Membuat user baru
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],

            // Password dienkripsi sebelum disimpan
            'password' => Hash::make($validated['password']),

            'role' => $validated['role'],
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Menampilkan form edit user
     */
    public function edit(User $user): View
    {
        // Route Model Binding otomatis mengambil user berdasarkan ID
        return view('users.edit', compact('user'));
    }

    /**
     * Menampilkan detail user (dialihkan ke tampilan edit)
     */
    public function show(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Mengupdate data user
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            // Email harus unik kecuali untuk user ini sendiri
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],

            'role' => 'required|in:admin,staff',

            // Password boleh kosong (nullable)
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|max:2048',
        ]);

        // Data dasar yang akan diupdate
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        // Jika password diisi, maka update password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        // Update user
        $user->update($data);

        // Redirect dengan pesan sukses
        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function editSelf(): View
    {
        $user = Auth::user();
        return view('users.edit', compact('user'));
    }

    /**
     * Menghapus user
     */
    public function destroy(User $user): RedirectResponse
    {
        // Mencegah user menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        // Hapus user
        $user->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
