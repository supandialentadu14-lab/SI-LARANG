@extends('layouts.admin') {{-- Menggunakan layout utama admin --}}

@section('header', 'Edit Penyedia') {{-- Judul halaman edit supplier --}}

@section('content')

    {{-- Container utama dengan lebar maksimal dan posisi di tengah --}}
    <div class=" mx-auto">

        {{-- Card pembungkus form edit --}}
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">

            {{-- Header card --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-orange-50">
                <h6 class="font-bold text-orange-700">
                    Informasi Penyedia {{-- Judul bagian informasi supplier --}}
                </h6>
            </div>

            {{-- Form untuk mengupdate data supplier --}}
            <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="p-6 space-y-6">

                @csrf {{-- Token keamanan untuk mencegah CSRF attack --}}
                @method('PUT') {{-- Method spoofing untuk mengubah method POST menjadi PUT (update data) --}}

                {{-- Input Nama Perusahaan --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Nama Perusahaan 
                        <span class="text-red-500">*</span> {{-- Tanda field wajib --}}
                    </label>

                    <input type="text" 
                        name="name" {{-- Nama field yang dikirim ke controller --}}
                        value="{{ old('name', $supplier->name) }}" {{-- Jika validasi gagal pakai old(), jika tidak tampilkan data lama --}}
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                        required> {{-- Field wajib diisi --}}
                </div>

                {{-- Grid 2 kolom untuk Email dan Phone --}}
                <div class="grid grid-cols-2 gap-4">

                    {{-- Input Email --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Email 
                            <span class="text-red-500">*</span> {{-- Field wajib --}}
                        </label>

                        <input type="email" 
                            name="email" 
                            value="{{ old('email', $supplier->email) }}" {{-- Mengisi ulang jika error atau menampilkan data lama --}}
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                    </div>

                    {{-- Input Nomor Telepon --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Nomor Telp.
                        </label>

                        <input type="text" 
                            name="phone" 
                            value="{{ old('phone', $supplier->phone) }}" {{-- Menampilkan data lama supplier --}}
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                    </div>

                </div>

                {{-- Input Alamat Supplier --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Alamat
                    </label>

                    <textarea 
                        name="address" {{-- Field alamat --}}
                        rows="3" {{-- Tinggi textarea --}}
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">{{ old('address', $supplier->address) }}</textarea> {{-- Menampilkan data lama atau old input --}}
                </div>

                {{-- Tombol aksi (Cancel & Update) --}}
                <div class="flex justify-end space-x-3 pt-4">

                    {{-- Tombol kembali ke halaman index supplier --}}
                    <a href="{{ route('suppliers.index') }}"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 font-bold hover:bg-gray-100 transition">
                        Batal
                    </a>

                    {{-- Tombol submit untuk memperbarui data supplier --}}
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-orange-600 text-white font-bold shadow hover:bg-orange-700 transition">
                        Perbarui
                    </button>

                </div>

            </form>
        </div>
    </div>

@endsection
