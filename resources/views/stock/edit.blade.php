@extends('layouts.admin')

@section('header', 'Edit Transaksi')

@section('content')

{{-- Wrapper agar form berada di tengah --}}
<div class="flex justify-center">

    {{-- Card Form --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-2 w-full">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-100 bg-orange-50">
                <h6 class="font-bold text-blue-700 flex items-center">
                    {{-- Icon edit --}}
                    <i class="fas fa-edit mr-2"></i> 
                    Edit Transaksi
                </h6>
            </div>

        <form action="{{ route('stock.update', $transaction->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- PRODUK --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">
                        Produk <span class="text-red-500">*</span>
                    </label>
                    <select name="product_id"
                        class="w-full border border-gray-400 rounded-lg px-4 py-2 
                        focus:border-blue-500 focus:ring-1 focus:ring-blue-300 outline-none">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                {{ $transaction->product_id == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- JENIS TRANSAKSI --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">
                        Jenis Transaksi <span class="text-red-500">*</span>
                    </label>
                    <select name="type"
                        class="w-full border border-gray-400 rounded-lg px-4 py-2 
                        focus:border-blue-500 focus:ring-1 focus:ring-blue-300 outline-none">
                        <option value="in" {{ $transaction->type == 'in' ? 'selected' : '' }}>
                            Masuk
                        </option>
                        <option value="out" {{ $transaction->type == 'out' ? 'selected' : '' }}>
                            Keluar
                        </option>
                    </select>
                </div>

                {{-- TANGGAL --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                        name="date"
                        value="{{ $transaction->date }}"
                        class="w-full border border-gray-400 rounded-lg px-4 py-2 
                        focus:border-blue-500 focus:ring-1 focus:ring-blue-300 outline-none">
                </div>

                {{-- JUMLAH --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                        name="quantity"
                        value="{{ $transaction->quantity }}"
                        class="w-full border border-gray-400 rounded-lg px-4 py-2 
                        focus:border-blue-500 focus:ring-1 focus:ring-blue-300 outline-none">
                    @error('quantity')
                        <div class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>

            {{-- NOMOR SURAT --}}
            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-600 mb-2">
                    Nomor Surat
                </label>
                <input type="text"
                    name="nosur"
                    value="{{ $transaction->nosur }}"
                    class="w-full border border-gray-400 rounded-lg px-4 py-2 
                    focus:border-blue-500 focus:ring-1 focus:ring-blue-300 outline-none">
            </div>

            {{-- CATATAN --}}
            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-600 mb-2">
                    Catatan
                </label>
                <textarea name="notes"
                    rows="4"
                    class="w-full border border-gray-400 rounded-lg px-4 py-2 
                    focus:border-blue-500 focus:ring-1 focus:ring-blue-300 outline-none">{{ $transaction->notes }}</textarea>
            </div>

            {{-- BUTTON --}}
            <div class="flex justify-end mt-8 space-x-3">
                <a href="{{ route('stock.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg">
                    Batal
                </a>

                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold shadow">
                    Perbarui
                </button>
            </div>

        </form>

    </div>

</div>

@endsection
