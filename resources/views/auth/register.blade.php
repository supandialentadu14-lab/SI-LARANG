{{-- Menggunakan layout untuk user yang belum login (guest) --}}
<x-guest-layout>

    {{-- Bagian Judul Halaman Register --}}
    <div class="mb-10">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/silarang-logo.png') }}" alt="Logo SI-LARANG" class="h-20 w-20 rounded-md ring-2 ring-indigo-200" onerror="this.style.display='none'">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Daftar Akun SI-LARANG</h2>
                <p class="text-gray-500 mt-1">Buat akun untuk mengelola persediaan.</p>
            </div>
        </div>
    </div>

    {{-- Form register, method POST ke route 'register' --}}
    <form method="POST" action="{{ route('register') }}" class="space-y-5">

        {{-- Token keamanan CSRF Laravel (wajib) --}}
        @csrf

        {{-- ================= INPUT NAMA ================= --}}
        <div>
            {{-- Label Nama Lengkap --}}
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Nama Lengkap
            </label>

            <div class="relative"> {{-- Agar icon bisa di posisi absolute --}}
                
                {{-- Icon user --}}
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                </div>

                {{-- Input Nama --}}
                <input 
                    id="name"
                    type="text" {{-- tipe text --}}
                    name="name" {{-- nama field --}}
                    :value="old('name')" {{-- isi ulang jika validasi gagal --}}
                    required {{-- wajib diisi --}}
                    autofocus {{-- fokus otomatis saat halaman dibuka --}}
                    class="pl-10 block w-full rounded-lg border-gray-300 border py-3 px-4 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition sm:text-sm"
                    placeholder="Nama Lengkap">
            </div>

            {{-- Menampilkan pesan error validasi name --}}
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- ================= INPUT EMAIL ================= --}}
        <div>
            {{-- Label Email --}}
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Alamat Email
            </label>

            <div class="relative">
                
                {{-- Icon email --}}
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                </div>

                {{-- Input Email --}}
                <input 
                    id="email"
                    type="email" {{-- tipe email --}}
                    name="email"
                    :value="old('email')" {{-- isi ulang jika gagal --}}
                    required {{-- wajib diisi --}}
                    class="pl-10 block w-full rounded-lg border-gray-300 border py-3 px-4 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition sm:text-sm"
                    placeholder="nama@perusahaan.com">
            </div>

            {{-- Menampilkan pesan error validasi email --}}
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- ================= INPUT PASSWORD ================= --}}
        <div>
            {{-- Label Password --}}
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Kata Sandi
            </label>

            <div class="relative">
                
                {{-- Icon gembok --}}
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>

                {{-- Input Password --}}
                <input 
                    id="password"
                    type="password" {{-- tipe password --}}
                    name="password"
                    required {{-- wajib diisi --}}
                    autocomplete="new-password" {{-- bantu browser isi password baru --}}
                    class="pl-10 block w-full rounded-lg border-gray-300 border py-3 px-4 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition sm:text-sm"
                    placeholder="Minimal 8 karakter">
            </div>

            {{-- Menampilkan pesan error validasi password --}}
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- ================= KONFIRMASI PASSWORD ================= --}}
        <div>
            {{-- Label Konfirmasi Password --}}
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                Konfirmasi Kata Sandi
            </label>

            <div class="relative">
                
                {{-- Icon gembok --}}
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>

                {{-- Input Konfirmasi Password --}}
                <input 
                    id="password_confirmation"
                    type="password" {{-- tipe password --}}
                    name="password_confirmation" {{-- harus sama dengan validasi Laravel --}}
                    required {{-- wajib diisi --}}
                    class="pl-10 block w-full rounded-lg border-gray-300 border py-3 px-4 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition sm:text-sm"
                    placeholder="Ulangi kata sandi">
            </div>
        </div>

        {{-- ================= TOMBOL SUBMIT ================= --}}
        <div>
            <button 
                type="submit" {{-- tombol kirim form --}}
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition duration-150">
                Daftar
            </button>
        </div>

    </form>

    {{-- ================= LINK KE LOGIN ================= --}}
    <p class="mt-8 text-center text-sm text-gray-500">
        Sudah punya akun?
        <a 
            href="{{ route('login') }}" {{-- arah ke halaman login --}}
            class="font-medium text-brand-600 hover:text-brand-500">
            Masuk
        </a>
    </p>

</x-guest-layout>
