{{-- Menggunakan layout khusus untuk halaman guest (belum login) --}}
<x-guest-layout>

    {{-- Header Form --}}
    <div class="mb-8 text-center">
        <h3 class="text-2xl font-bold text-gray-800">Selamat Datang</h3>
        <p class="text-gray-500 text-sm mt-1">Silakan masuk ke akun Anda</p>
    </div>

    {{-- Form login, method POST, dikirim ke route bernama 'login' --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-5">

        {{-- Token keamanan CSRF (wajib di Laravel) --}}
        @csrf

        {{-- ================= INPUT EMAIL ================= --}}
        <div class="group">
            <label for="email" class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1 tracking-wide">
                Email
            </label>
            <div class="relative transition-all duration-300 transform group-hover:-translate-y-0.5"> 
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-600 transition-colors">
                    <i class="far fa-envelope text-lg"></i>
                </div>
                <input 
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="form-input pl-12 block w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white py-3.5 px-4 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-0 transition-all duration-200 shadow-sm"
                    placeholder="nama@email.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- ================= INPUT PASSWORD ================= --}}
        <div class="group">
            <label for="password" class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1 tracking-wide">
                Password
            </label>
            <div class="relative transition-all duration-300 transform group-hover:-translate-y-0.5">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-600 transition-colors">
                    <i class="fas fa-lock text-lg"></i>
                </div>
                <input 
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="form-input pl-12 block w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white py-3.5 px-4 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-0 transition-all duration-200 shadow-sm"
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- ================= REMEMBER ME & FORGOT PASSWORD ================= --}}
        <div class="flex items-center justify-between pt-2">
            <div class="flex items-center">
                <input 
                    id="remember_me"
                    name="remember"
                    type="checkbox"
                    class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded cursor-pointer transition-colors">
                <label for="remember_me" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none font-medium">
                    Ingat Saya
                </label>
            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-bold text-brand-600 hover:text-brand-700 transition-colors">
                    Lupa Password?
                </a>
            @endif
        </div>

        {{-- ================= TOMBOL SUBMIT ================= --}}
        <div class="pt-4">
            <button 
                type="submit"
                class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg shadow-brand-500/40 text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-700 hover:to-brand-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl active:scale-95 tracking-wide uppercase">
                Masuk Sekarang <i class="fas fa-arrow-right ml-2 mt-0.5"></i>
            </button>
        </div>

    </form>

    {{-- ================= LINK REGISTER ================= --}}
    <div class="mt-8 text-center">
        <p class="text-sm text-gray-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:text-brand-700 hover:underline transition-all">
                Daftar Akun Baru
            </a>
        </p>
    </div>

</x-guest-layout>
