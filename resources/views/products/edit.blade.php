{{-- Menggunakan layout utama admin --}}
@extends('layouts.admin')

{{-- Mengisi bagian header pada layout --}}
@section('header', 'Edit Barang')

{{-- Mengisi bagian content pada layout --}}
@section('content')

    {{-- Container utama dengan lebar maksimal 4xl dan posisi di tengah --}}
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">

            {{-- Header card --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-slate-800">
                <h6 class="font-bold text-white flex items-center">
                    {{-- Icon edit --}}
                    <i class="fas fa-edit mr-2"></i>
                    Edit Barang
                </h6>
            </div>

            {{-- Form untuk update data produk --}}
            <form action="{{ route('products.update', $product) }}" method="POST" class="p-6">

                {{-- Token keamanan Laravel --}}
                @csrf

                {{-- Method spoofing karena HTML tidak mendukung PUT --}}
                @method('PUT')

                {{-- Grid 2 kolom --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <!-- ================= LEFT COLUMN ================= -->
                    <div class="space-y-6">

                        {{-- Input Nama Barang --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                Nama Barang <span class="text-red-500">*</span>
                            </label>

                            {{-- 
                                old('name', $product->name) 
                                → Jika validasi gagal, tampilkan input sebelumnya
                                → Jika tidak, tampilkan data lama dari database
                            --}}
                            <input type="text" name="name" value="{{ old('name', $product->name) }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                        </div>

                        {{-- Input Kode Barang / SKU --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                Kode Barang <span class="text-red-500">*</span>
                            </label>

                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 focus:outline-none"
                                readonly>
                        </div>

                        {{-- Grid Harga + Jumlah + Satuan --}}
                        <div class="grid grid-cols-3 gap-4">

                            {{-- Input Harga --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">
                                    Harga
                                </label>

                                <input type="number" step="0.01" name="price"
                                    value="{{ old('price', $product->price) }}"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                            </div>

                            {{-- Input Jumlah Barang (DISABLE saat edit) --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">
                                    Jumlah Barang
                                </label>

                                {{-- Input tampil tapi tidak bisa diedit --}}
                                <input type="number" value="{{ old('jumlah_barang', $product->stock) }}"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 cursor-not-allowed"
                                    disabled>

                                {{-- Hidden input agar tetap terkirim ke controller --}}
                                <input type="hidden" name="jumlah_barang"
                                    value="{{ old('jumlah_barang', $product->stock) }}">
                            </div>


                            {{-- Dropdown Satuan --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">
                                    Satuan
                                </label>

                                <select name="unit" class="w-full px-4 py-2 rounded-lg border border-gray-300" required>

                                    {{-- 
                                        Mengecek apakah value lama atau value dari database
                                        sama dengan option, jika ya maka selected
                                    --}}
                                    <option value="pcs" {{ old('unit', $product->unit) == 'pcs' ? 'selected' : '' }}>PCS
                                    </option>
                                    <option value="buah" {{ old('unit', $product->unit) == 'buah' ? 'selected' : '' }}>
                                        Buah</option>
                                    <option value="box" {{ old('unit', $product->unit) == 'box' ? 'selected' : '' }}>Box
                                    </option>
                                    <option value="pak" {{ old('unit', $product->unit) == 'pak' ? 'selected' : '' }}>Pak
                                    </option>
                                    <option value="rim" {{ old('unit', $product->unit) == 'rim' ? 'selected' : '' }}>Rim
                                    </option>
                                    <option value="kg" {{ old('unit', $product->unit) == 'kg' ? 'selected' : '' }}>Kg
                                    </option>
                                    <option value="galon" {{ old('unit') == 'galon' ? 'selected' : '' }}>Galon</option>
                                    <option value="paket" {{ old('unit') == 'paket' ? 'selected' : '' }}>Paket</option>
                                    <option value="liter" {{ old('unit', $product->unit) == 'liter' ? 'selected' : '' }}>
                                        Liter</option>
                                </select>
                            </div>

                        </div>

                        {{-- Textarea Keterangan --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                Keterangan
                            </label>

                            {{-- 
                                Menampilkan deskripsi lama jika tidak ada error
                                atau input sebelumnya jika validasi gagal
                            --}}
                            <textarea name="description" rows="4" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                                {{ old('description', $product->description) }}
                            </textarea>
                        </div>

                    </div>

                    <!-- ================= RIGHT COLUMN ================= -->
                    <div class="space-y-6">

                        {{-- Dropdown Jenis Belanja --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                Jenis Belanja
                            </label>

                            <select name="category_id" class="w-full px-4 py-2 rounded-lg border border-gray-300" required>

                                {{-- Looping data kategori --}}
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Dropdown Penyedia --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                Penyedia
                            </label>

                            <select name="supplier_id" class="w-full px-4 py-2 rounded-lg border border-gray-300">

                                <option value="">-- Pilih Penyedia --</option>

                                {{-- Looping data supplier --}}
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                </div>

                @include('partials.form-actions', [
                    'backRoute' => route('products.index'),
                    'saveText' => 'Perbarui',
                ])
            </form>
        </div>
    </div>

@endsection
