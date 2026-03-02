{{-- Menggunakan layout untuk user yang belum login (guest) --}}
<x-guest-layout>

    {{-- Header Form --}}
    <div class="mb-8 text-center">
        <h3 class="text-2xl font-bold text-gray-800">Buat Akun Baru</h3>
        <p class="text-gray-500 text-sm mt-1">Daftar untuk mulai mengelola persediaan</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        {{-- Input Nama --}}
        <div class="group">
            <label for="name" class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1 tracking-wide">
                Nama Lengkap
            </label>
            <div class="relative transition-all duration-300 transform group-hover:-translate-y-0.5">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-600 transition-colors">
                    <i class="far fa-user text-lg"></i>
                </div>
                <input 
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    class="form-input pl-12 block w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white py-3.5 px-4 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-0 transition-all duration-200 shadow-sm"
                    placeholder="Nama Lengkap">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Input Email --}}
        <div class="group">
            <label for="email" class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1 tracking-wide">
                Alamat Email
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
                    class="form-input pl-12 block w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white py-3.5 px-4 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-0 transition-all duration-200 shadow-sm"
                    placeholder="nama@email.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Input Password --}}
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
                    autocomplete="new-password"
                    class="form-input pl-12 block w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white py-3.5 px-4 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-0 transition-all duration-200 shadow-sm"
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Input Confirm Password --}}
        <div class="group">
            <label for="password_confirmation" class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1 tracking-wide">
                Konfirmasi Password
            </label>
            <div class="relative transition-all duration-300 transform group-hover:-translate-y-0.5">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-brand-600 transition-colors">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
                <input 
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    class="form-input pl-12 block w-full rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white py-3.5 px-4 text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-0 transition-all duration-200 shadow-sm"
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-4">
            <button 
                type="submit"
                class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg shadow-brand-500/40 text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-700 hover:to-brand-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl active:scale-95 tracking-wide uppercase">
                Daftar Sekarang <i class="fas fa-user-plus ml-2 mt-0.5"></i>
            </button>
        </div>
    </form>

    <div class="mt-8 text-center">
        <p class="text-sm text-gray-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:text-brand-700 hover:underline transition-all">
                Masuk disini
            </a>
        </p>
    </div>
</x-guest-layout>
