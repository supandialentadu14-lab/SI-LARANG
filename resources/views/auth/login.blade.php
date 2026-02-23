{{-- Menggunakan layout khusus untuk halaman guest (belum login) --}}
<x-guest-layout>

    {{-- Bagian Header / Judul halaman login --}}
    <div class="mb-10">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/silarang-logo.png') }}" alt="Logo SI-LARANG" class="h-20 w-20 rounded-md ring-2 ring-indigo-200" onerror="this.style.display='none'">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Masuk ke SI-LARANG</h2>
                <p class="text-gray-500 mt-1">Autentikasi untuk mengelola persediaan.</p>
            </div>
        </div>
    </div>

    {{-- Form login, method POST, dikirim ke route bernama 'login' --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-6">

        {{-- Token keamanan CSRF (wajib di Laravel) --}}
        @csrf

        {{-- ================= INPUT EMAIL ================= --}}
        <div>
            {{-- Label untuk input email --}}
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Alamat Email
            </label>

            <div class="relative"> {{-- Supaya icon bisa diposisikan absolute --}}
                
                {{-- Icon email di dalam input --}}
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="far fa-envelope text-gray-400"></i>
                </div>

                {{-- Input email --}}
                <input 
                    id="email" {{-- ID input --}}
                    type="email" {{-- tipe email --}}
                    name="email" {{-- nama field --}}
                    :value="old('email')" {{-- isi ulang jika validasi gagal --}}
                    required {{-- wajib diisi --}}
                    autofocus {{-- langsung fokus saat halaman dibuka --}}
                    class="pl-10 block w-full rounded-lg border-gray-300 border py-3 px-4 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition sm:text-sm"
                    placeholder="nama@gmail.com"> {{-- teks contoh --}}
            </div>

            {{-- Menampilkan pesan error validasi email --}}
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- ================= INPUT PASSWORD ================= --}}
        <div>
            {{-- Label password --}}
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Kata Sandi
            </label>

            <div class="relative">
                
                {{-- Icon gembok --}}
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>

                {{-- Input password --}}
                <input 
                    id="password"
                    type="password" {{-- tipe password --}}
                    name="password"
                    required {{-- wajib diisi --}}
                    autocomplete="current-password" {{-- bantu browser auto-fill --}}
                    class="pl-10 block w-full rounded-lg border-gray-300 border py-3 px-4 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition sm:text-sm"
                    placeholder="••••••••">
            </div>

            {{-- Menampilkan pesan error password --}}
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- ================= REMEMBER ME & FORGOT PASSWORD ================= --}}
        <div class="flex items-center justify-between">

            {{-- Checkbox untuk mengingat login --}}
            <div class="flex items-center">
                <input 
                    id="remember_me"
                    name="remember"
                    type="checkbox" {{-- tipe checkbox --}}
                    class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded">
                <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                    Ingat Saya
                </label>
            </div>

            {{-- Menampilkan link lupa password jika route tersedia --}}
            @if (Route::has('password.request'))
                <a 
                    href="{{ route('password.request') }}" {{-- arah ke halaman reset password --}}
                    class="text-sm font-medium text-brand-600 hover:text-brand-500">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        {{-- ================= TOMBOL SUBMIT ================= --}}
        <div>
            <button 
                type="submit" {{-- tombol kirim form --}}
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition duration-150">
                Masuk
            </button>
        </div>

    </form>

    {{-- ================= LINK REGISTER ================= --}}
    <p class="mt-8 text-center text-sm text-gray-500">
        Belum punya akun?
        <a 
            href="{{ route('register') }}" {{-- arah ke halaman daftar --}}
            class="font-medium text-blue-600 hover:text-blue-500">
            Daftar Sekarang
        </a>
    </p>

</x-guest-layout>
