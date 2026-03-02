@extends('layouts.admin') {{-- Menggunakan layout utama admin --}}

@section('header', 'Edit Pengguna') {{-- Judul halaman edit user --}}

@section('content')

    {{-- Container utama dengan lebar maksimal dan posisi di tengah --}}
    <div class="max-w-xl mx-auto">

        {{-- Card pembungkus form --}}
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">

            {{-- Header card --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-orange-50">
                <h6 class="font-bold text-blue-700">
                    Kredensial Pengguna {{-- Judul bagian kredensial akun --}}
                </h6>
            </div>

            {{-- Form untuk mengupdate data user --}}
            <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">

                @csrf {{-- Token keamanan CSRF --}}
                @method('PUT') {{-- Mengubah method POST menjadi PUT untuk proses update --}}

                {{-- Input Nama Lengkap --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span> {{-- Field wajib --}}
                    </label>

                    {{-- Input nama dengan fallback old() jika validasi gagal --}}
                    <input type="text" 
                        name="name" 
                        value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                        required>
                </div>

                {{-- Input Email --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>

                    {{-- Input email dengan fallback data lama --}}
                    <input type="email" 
                        name="email" 
                        value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                        required>
                </div>

                {{-- Dropdown Role --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Hak Akses <span class="text-red-500">*</span>
                    </label>

                    {{-- Select role dengan kondisi selected otomatis --}}
                    <select name="role"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition bg-white"
                        required>

                        {{-- Jika role staff maka otomatis selected --}}
                        <option value="staff" 
                            {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>
                            Staff
                        </option>

                        {{-- Jika role admin maka otomatis selected --}}
                        <option value="admin" 
                            {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                            Admin
                        </option>

                    </select>
                </div>

                {{-- Section Ganti Password --}}
                <div class="border-t border-gray-100 pt-4">

                    {{-- Keterangan bahwa password boleh dikosongkan --}}
                    <p class="text-xs text-gray-400 mb-4 font-bold uppercase tracking-wider">
                        Change Password (Leave blank to keep current)
                    </p>

                    {{-- Grid 2 kolom untuk password baru --}}
                    <div class="grid grid-cols-2 gap-4">

                        {{-- Input Password Baru --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                Kata Sandi Baru
                            </label>

                            {{-- Jika kosong, password lama tetap digunakan --}}
                            <input type="password" 
                                name="password"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                        </div>

                        {{-- Input Konfirmasi Password Baru --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                Konfirmasi Kata Sandi Baru
                            </label>

                            {{-- Harus sama dengan password jika diisi --}}
                            <input type="password" 
                                name="password_confirmation"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                        </div>

                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs text-gray-400 mb-4 font-bold uppercase tracking-wider">Foto Profil</p>
                    <div class="flex items-center gap-4">
                        <img class="h-12 w-12 rounded-full object-cover border border-gray-200"
                             src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0000CD&color=ffffff' }}"
                             alt="Avatar">
                        <input type="file" name="avatar" accept="image/*" class="px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition bg-white">
                    </div>
                </div>

                @include('partials.form-actions', [
                    'backRoute' => route('users.index'),
                    'saveText' => 'Perbarui',
                ])
            </form>
        </div>
    </div>

@endsection
