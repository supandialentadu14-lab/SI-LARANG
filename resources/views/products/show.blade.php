@extends('layouts.admin')

@section('header', 'Detail Barang')
@section('subheader', 'Informasi lengkap barang')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
            <div class="mt-6 text-gray-500">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-label for="name" value="{{ __('Nama Barang') }}" />
                        <div class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm p-2 bg-gray-50">
                            {{ $product->name }}
                        </div>
                    </div>

                    <div>
                        <x-label for="sku" value="{{ __('Kode Barang (SKU)') }}" />
                        <div class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm p-2 bg-gray-50">
                            {{ $product->sku }}
                        </div>
                    </div>

                    <div>
                        <x-label for="category" value="{{ __('Kategori') }}" />
                        <div class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm p-2 bg-gray-50">
                            {{ $product->category->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <x-label for="price" value="{{ __('Harga') }}" />
                        <div class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm p-2 bg-gray-50">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </div>
                    </div>

                    <div>
                        <x-label for="unit" value="{{ __('Satuan') }}" />
                        <div class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm p-2 bg-gray-50">
                            {{ $product->unit }}
                        </div>
                    </div>

                    <div>
                        <x-label for="supplier" value="{{ __('Penyedia') }}" />
                        <div class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm p-2 bg-gray-50">
                            {{ $product->supplier->name ?? '-' }}
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <x-label for="description" value="{{ __('Deskripsi') }}" />
                        <div class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm p-2 bg-gray-50">
                            {{ $product->description ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('products.edit', $product->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                        {{ __('Edit') }}
                    </a>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Kembali') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
