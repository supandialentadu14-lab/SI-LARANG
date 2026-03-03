@extends('layouts.admin') {{-- Menggunakan layout utama admin --}}

@section('header', 'Pengguna Baru') {{-- Judul halaman tambah user baru --}}

@section('content')

    {{-- Container utama dengan lebar maksimal dan posisi tengah --}}
    <div class="max-w-xl mx-auto">

        {{-- Card pembungkus form --}}
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">

            {{-- Header card --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-slate-800">
                <h6 class="font-bold text-white">
                    Data Pengguna {{-- Judul form --}}
                </h6>
            </div>

            {{-- Form tambah user --}}
            <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-6">

                @csrf {{-- Token keamanan Laravel untuk mencegah CSRF --}}

                {{-- Input Nama Lengkap --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span> {{-- Tanda wajib --}}
                    </label>

                    {{-- Field input nama --}}
                    <input type="text" 
                        name="name" 
                        value="{{ old('name') }}" {{-- Menyimpan input lama jika validasi gagal --}}
                        placeholder="John Doe"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                        required>
                </div>

                {{-- Input Email --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>

                    {{-- Field input email --}}
                    <input type="email" 
                        name="email" 
                        value="{{ old('email') }}" {{-- Mengisi ulang jika gagal validasi --}}
                        placeholder="john@example.com"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                        required>
                </div>

                {{-- Dropdown Role --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Hak Akses <span class="text-red-500">*</span>
                    </label>

                    {{-- Pilihan role user --}}
                    <select name="role"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition bg-white"
                        required>

                        {{-- Role Staff --}}
                        <option value="staff">Staff</option>

                        {{-- Role Administrator --}}
                        <option value="admin">
                            Administrator (Full Access)
                        </option>

                    </select>
                </div>

                {{-- Grid Password & Konfirmasi --}}
                <div class="grid grid-cols-2 gap-4">

                    {{-- Input Password --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Password <span class="text-red-500">*</span>
                        </label>

                        {{-- Field password --}}
                        <input type="password" 
                            name="password"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                            required>
                    </div>

                    {{-- Input Konfirmasi Password --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Confirm Password
                        </label>

                        {{-- Field konfirmasi password --}}
                        <input type="password" 
                            name="password_confirmation"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                            required>
                    </div>

                </div>

                @include('partials.form-actions', [
                    'backRoute' => route('users.index'),
                    'saveText' => 'Simpan',
                ])
            </form>
        </div>
    </div>

@endsection
