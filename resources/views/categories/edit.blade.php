{{-- Menggunakan layout admin --}}
@extends('layouts.admin')

{{-- Mengisi bagian header halaman --}}
@section('header', 'Edit Jenis Belanja')

{{-- Section utama konten --}}
@section('content')

    {{-- Container dengan lebar maksimal xl dan posisi di tengah --}}
    <div class=" mx-auto">

        {{-- Card utama --}}
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">

            {{-- Header card --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-slate-800">
                <h6 class="font-bold text-orange-700">
                    Uraian Jenis Belanja {{-- Judul form edit --}}
                </h6>
            </div>

            {{-- Form untuk update data kategori --}}
            {{-- Mengarah ke route categories.update dengan parameter $category --}}
            <form action="{{ route('categories.update', $category) }}" method="POST" class="p-6 space-y-6">

                {{-- Token keamanan CSRF --}}
                @csrf

                {{-- Mengubah method POST menjadi PUT (karena update menggunakan PUT/PATCH) --}}
                @method('PUT')

                {{-- ================= INPUT NAMA JENIS BELANJA ================= --}}
                <div>
                    {{-- Label input --}}
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Jenis Belanja 
                        <span class="text-red-500">*</span> {{-- Tanda wajib diisi --}}
                    </label>

                    {{-- Input nama kategori --}}
                    <input 
                        type="text"
                        name="name"
                        value="{{ old('name', $category->name) }}" 
                        {{-- Jika validasi gagal pakai old('name'),
                             jika tidak pakai data dari database ($category->name) --}}
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                        required>
                </div>

                {{-- ================= INPUT KETERANGAN ================= --}}
                <div>
                    {{-- Label textarea --}}
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Keterangan
                    </label>

                    {{-- Textarea deskripsi --}}
                    <textarea 
                        name="description"
                        rows="3"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                        {{ old('description', $category->description) }}
                        {{-- Jika validasi gagal pakai old(),
                             jika tidak pakai data dari database --}}
                    </textarea>
                </div>

                @include('partials.form-actions', [
                    'backRoute' => route('categories.index'),
                    'saveText' => 'Perbarui',
                ])
            </form>
        </div>
    </div>

@endsection
