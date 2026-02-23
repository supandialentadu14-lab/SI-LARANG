@extends('layouts.admin') {{-- Menggunakan layout utama admin --}}

@section('header', 'New Supplier') {{-- Judul halaman yang ditampilkan di header --}}

@section('content')

    {{-- Container utama dengan lebar maksimal dan posisi di tengah --}}
    <div class=" mx-auto">

        {{-- Card pembungkus form --}}
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">

            {{-- Header card --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-orange-50">
                <h6 class="font-bold text-blue-700">Informasi Penyedia</h6> {{-- Judul bagian form --}}
            </div>

            {{-- Form untuk menyimpan data supplier --}}
            <form action="{{ route('suppliers.store') }}" method="POST" class="p-6 space-y-6">

                @csrf {{-- Token keamanan untuk mencegah CSRF attack --}}

                {{-- Input Nama Perusahaan --}}
                <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Nama Perusahaan 
                        <span class="text-red-500">*</span> {{-- Tanda wajib diisi --}}
                    </label>

                    <input type="text" 
                        name="name" {{-- Nama field untuk dikirim ke server --}}
                        value="{{ old('name') }}" {{-- Mengisi kembali input jika validasi gagal --}}
                        placeholder="contoh: CV. ABCD"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                        required> {{-- Field wajib diisi --}}
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Nama Direktur 
                        <span class="text-red-500">*</span> {{-- Tanda wajib diisi --}}
                    </label>

                    <input type="text" 
                        name="dir" {{-- Nama field untuk dikirim ke server --}}
                        value="{{ old('dir') }}" {{-- Mengisi kembali input jika validasi gagal --}}
                        placeholder="contoh: Emon Alentadu"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                        required> {{-- Field wajib diisi --}}
                </div>
                {{-- Grid 2 kolom untuk Email dan Nomor HP --}}
                <div class="grid grid-cols-2 gap-4">

                    {{-- Input Email --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Email 
                            <span class="text-red-500">*</span> {{-- Tanda wajib --}}
                        </label>

                        <input type="email" 
                            name="email" 
                            value="{{ old('email') }}" {{-- Menyimpan input lama jika error --}}
                            placeholder="...........@gmail.com"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                    </div>

                    {{-- Input Nomor HP --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Nomor Hp
                        </label>

                        <input type="text" 
                            name="phone" 
                            value="{{ old('phone') }}" {{-- Mengisi ulang jika validasi gagal --}}
                            placeholder="+62 .........."
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                    </div>

                </div>

                {{-- Input Alamat --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Alamat
                    </label>

                    <textarea 
                        name="address" {{-- Field alamat --}}
                        rows="3" {{-- Tinggi textarea --}}
                        placeholder="Masukkan Alamat Lengkap Perusahaan"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">{{ old('address') }}</textarea> {{-- Isi kembali jika gagal validasi --}}
                </div>
                </div>
                {{-- Tombol aksi (Cancel & Simpan) --}}
                <div class="flex justify-end space-x-3 pt-4">

                    {{-- Tombol kembali ke halaman index --}}
                    <a href="{{ route('suppliers.index') }}"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 font-bold hover:bg-gray-100 transition">
                        Cancel
                    </a>

                    {{-- Tombol submit untuk menyimpan data --}}
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white font-bold shadow hover:bg-blue-700 transition">
                        Simpan
                    </button>

                </div>

            </form>
        </div>
    </div>

@endsection
