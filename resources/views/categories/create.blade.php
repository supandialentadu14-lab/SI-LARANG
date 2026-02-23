{{-- Menggunakan layout admin --}}
@extends('layouts.admin')

{{-- Mengisi section header dengan judul halaman --}}
@section('header', 'Tambah Jenis Belanja')

{{-- Section utama untuk isi halaman --}}
@section('content')

    {{-- Container dengan lebar maksimal xl dan posisi tengah --}}
    <div class="max-w-xl mx-auto">

        {{-- Card utama --}}
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">

            {{-- Header card --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-orange-50">
                <h6 class="font-bold text-blue-700">
                    Uraian Jenis Belanja {{-- Judul form --}}
                </h6>
            </div>

            {{-- Form untuk menyimpan data ke route categories.store --}}
            <form action="{{ route('categories.store') }}" method="POST" class="p-6 space-y-6">

                {{-- Token keamanan CSRF (wajib di Laravel) --}}
                @csrf

                {{-- ================= INPUT NAMA JENIS BELANJA ================= --}}
                <div>
                    {{-- Label input --}}
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Jenis Belanja 
                        <span class="text-red-500">*</span> {{-- Tanda wajib diisi --}}
                    </label>

                    {{-- Input text nama kategori --}}
                    <input 
                        type="text" {{-- tipe input text --}}
                        name="name" {{-- nama field --}}
                        value="{{ old('name') }}" {{-- isi ulang jika validasi gagal --}}
                        placeholder="contoh: Belanja Modal" {{-- contoh isi --}}
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                        required> {{-- wajib diisi --}}
                </div>

                {{-- ================= INPUT KETERANGAN ================= --}}
                <div>
                    {{-- Label textarea --}}
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Keterangan
                    </label>

                    {{-- Textarea deskripsi --}}
                    <textarea 
                        name="description" {{-- nama field --}}
                        rows="3" {{-- tinggi 3 baris --}}
                        placeholder="Untuk kegiatan apa"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                        {{ old('description') }} {{-- isi ulang jika validasi gagal --}}
                    </textarea>
                </div>

                {{-- ================= TOMBOL AKSI ================= --}}
                <div class="flex justify-end space-x-3 pt-4">

                    {{-- Tombol batal kembali ke halaman index --}}
                    <a 
                        href="{{ route('categories.index') }}"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 font-bold hover:bg-gray-100 transition">
                        Batal
                    </a>

                    {{-- Tombol submit untuk menyimpan data --}}
                    <button 
                        type="submit"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white font-bold shadow hover:bg-blue-700 transition">
                        Simpan
                    </button>

                </div>

            </form>
        </div>
    </div>

@endsection
