{{-- Menggunakan layout khusus untuk halaman guest (belum login) --}}
<x-guest-layout>

    {{-- Bagian Header / Judul halaman login --}}
    <div class="mb-8">
        <div class="flex items-start gap-4">
            <div class="bg-white p-2 rounded-xl shadow-md border border-gray-100 hidden lg:block animate__animated animate__bounceIn">
                <img src="{{ asset('images/silarang-logo.png') }}" alt="Logo SI-LARANG" class="h-14 w-14 rounded-lg object-contain" onerror="this.style.display='none'">
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Masuk ke SI-LARANG</h2>
                <p class="text-gray-500 mt-1 text-sm">Autentikasi untuk mengelola persediaan.</p>
            </div>
        </div>
    </div>

    {{-- Form login, method POST, dikirim ke route bernama 'login' --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-5">

        {{-- Token keamanan CSRF (wajib di Laravel) --}}
        @csrf

        {{-- ================= INPUT EMAIL ================= --}}
        <div class="group">
            {{-- Label untuk input email --}}
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5 ml-1">
                Alamat Email
            </label>

            <div class="relative transition-all duration-300 transform group-hover:-translate-y-0.5"> 
                
                {{-- Icon email di dalam input --}}
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                    <i class="far fa-envelope"></i>
                </div>

                {{-- Input email --}}
                <input 
                    id="email" {{-- ID input --}}
                    type="email" {{-- tipe email --}}
                    name="email" {{-- nama field --}}
                    value="{{ old('email') }}" {{-- isi ulang jika validasi gagal --}}
                    required {{-- wajib diisi --}}
                    autofocus {{-- langsung fokus saat halaman dibuka --}}
                    class="form-input pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white py-3 px-4 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-0 transition-all duration-200 sm:text-sm"
                    placeholder="nama@gmail.com"> {{-- teks contoh --}}
            </div>

            {{-- Menampilkan pesan error validasi email --}}
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- ================= INPUT PASSWORD ================= --}}
        <div class="group">
            {{-- Label password --}}
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5 ml-1">
                Kata Sandi
            </label>

            <div class="relative transition-all duration-300 transform group-hover:-translate-y-0.5">
                
                {{-- Icon gembok --}}
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-500 transition-colors">
                    <i class="fas fa-lock"></i>
                </div>

                {{-- Input password --}}
                <input 
                    id="password"
                    type="password" {{-- tipe password --}}
                    name="password"
                    required {{-- wajib diisi --}}
                    autocomplete="current-password" {{-- bantu browser auto-fill --}}
                    class="form-input pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white py-3 px-4 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-0 transition-all duration-200 sm:text-sm"
                    placeholder="••••••••">
            </div>

            {{-- Menampilkan pesan error password --}}
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- ================= REMEMBER ME & FORGOT PASSWORD ================= --}}
        <div class="flex items-center justify-between pt-1">

            {{-- Checkbox untuk mengingat login --}}
            <div class="flex items-center">
                <input 
                    id="remember_me"
                    name="remember"
                    type="checkbox" {{-- tipe checkbox --}}
                    class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded cursor-pointer transition-colors">
                <label for="remember_me" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none">
                    Ingat Saya
                </label>
            </div>

            {{-- Menampilkan link lupa password jika route tersedia --}}
            @if (Route::has('password.request'))
                <a 
                    href="{{ route('password.request') }}" {{-- arah ke halaman reset password --}}
                    class="text-sm font-semibold text-brand-600 hover:text-brand-500 transition-colors">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        {{-- ================= TOMBOL SUBMIT ================= --}}
        <div class="pt-2">
            <button 
                type="submit" {{-- tombol kirim form --}}
                class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-brand-500/30 text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-xl active:scale-95">
                Masuk
            </button>
        </div>

    </form>

    {{-- ================= LINK REGISTER ================= --}}
    <p class="mt-8 text-center text-sm text-gray-500">
        Belum punya akun?
        <a 
            href="{{ route('register') }}" {{-- arah ke halaman daftar --}}
            class="font-bold text-brand-600 hover:text-brand-500 hover:underline transition-all">
            Daftar Sekarang
        </a>
    </p>

</x-guest-layout>
