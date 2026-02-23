@extends('layouts.admin') {{-- Menggunakan layout utama admin --}}

@section('header', 'Transaksi') {{-- Judul halaman transaksi --}}

@section('content')

    {{-- Container utama dengan lebar maksimal --}}
    <div class=" mx-auto">

        {{-- Card pembungkus form --}}
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">

            {{-- Header card --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-orange-50">
                <h6 class="font-bold text-blue-700">Uraian Transaksi</h6>
            </div>

            {{-- Form untuk menyimpan transaksi stok --}}
            <form action="{{ route('stock.store') }}" method="POST" class="p-6 space-y-6">

                @csrf {{-- Token keamanan untuk mencegah CSRF --}}

                {{-- PILIH BARANG --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Barang <span class="text-red-500">*</span>
                    </label>

                    {{-- Dropdown daftar barang --}}
                    <select name="product_id"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition bg-white"
                        required>

                        {{-- Placeholder option --}}
                        <option value="">-- Pilih Barang --</option>

                        {{-- Loop semua produk --}}
                        @foreach ($products as $product)
                            {{-- Ambil stok saat ini dari accessor --}}
                            @php
                                $currentStock = $product->calculated_stock ?? 0;
                            @endphp

                            {{-- Option produk --}}
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} (Current: {{ $currentStock }})
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- GRID 2 KOLOM: JENIS TRANSAKSI & JUMLAH --}}
                <div class="grid grid-cols-2 gap-4">

                    {{-- JENIS TRANSAKSI --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Jenis Transaksi <span class="text-red-500">*</span>
                        </label>

                        {{-- Radio button custom dengan peer --}}
                        <div class="flex space-x-2">

                            {{-- Transaksi Masuk --}}
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="type" value="in" class="peer hidden"
                                    {{ old('type') == 'in' ? 'checked' : '' }} required>

                                {{-- Tampilan visual radio --}}
                                <div
                                    class="text-center py-2 rounded-lg border border-gray-200 bg-gray-50 
                                           peer-checked:bg-green-500 peer-checked:text-white 
                                           peer-checked:border-green-600 transition font-bold text-sm">
                                    <i class="fas fa-arrow-down mr-1"></i> Masuk
                                </div>
                            </label>

                            {{-- Transaksi Keluar --}}
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="type" value="out" class="peer hidden"
                                    {{ old('type') == 'out' ? 'checked' : '' }}>

                                {{-- Tampilan visual radio --}}
                                <div
                                    class="text-center py-2 rounded-lg border border-gray-200 bg-gray-50 
                                           peer-checked:bg-red-500 peer-checked:text-white 
                                           peer-checked:border-red-600 transition font-bold text-sm">
                                    <i class="fas fa-arrow-up mr-1"></i> Keluar
                                </div>
                            </label>

                        </div>
                    </div>

                    {{-- JUMLAH BARANG --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Jumlah <span class="text-red-500">*</span>
                        </label>

                        {{-- Input jumlah minimal 1 --}}
                        <input type="number" name="quantity" min="1" value="{{ old('quantity') }}" placeholder="10"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 
                                   focus:border-orange-500 focus:ring-2 focus:ring-orange-200 
                                   outline-none transition"
                            required>
                    </div>

                </div>

                {{-- TANGGAL TRANSAKSI --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Tanggal
                    </label>

                    {{-- Default tanggal hari ini --}}
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 
                               focus:border-orange-500 focus:ring-2 focus:ring-orange-200 
                               outline-none transition"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Nomor Surat Penerimaan/Pengeluaran
                    </label>

                    {{-- Default tanggal hari ini --}}
                    <textarea name="nosur" rows="1" placeholder="Masukkan Nomor Surat Penerimaan/Penegeluaran"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 
                               focus:border-orange-500 focus:ring-2 focus:ring-orange-200 
                               outline-none transition">{{ old('nosur') }}</textarea>
                </div>
                {{-- KETERANGAN --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Belanja
                    </label>

                    {{-- Textarea untuk catatan transaksi --}}
                    <textarea name="notes" rows="3" placeholder="Keterangan pemasukan atau pengeluaran untuk apa"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 
                               focus:border-orange-500 focus:ring-2 focus:ring-orange-200 
                               outline-none transition">{{ old('notes') }}</textarea>
                </div>

                {{-- TOMBOL AKSI --}}
                <div class="flex justify-end space-x-3 pt-4">

                    {{-- Tombol batal kembali ke halaman index --}}
                    <a href="{{ route('stock.index') }}"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 
                               font-bold hover:bg-gray-100 transition">
                        Batal
                    </a>

                    {{-- Tombol submit simpan --}}
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white 
                               font-bold shadow hover:bg-blue-700 transition">
                        Simpan
                    </button>

                </div>

            </form>
        </div>
    </div>

@endsection
