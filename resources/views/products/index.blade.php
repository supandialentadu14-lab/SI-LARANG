@extends('layouts.admin')

@section('header', 'Daftar Barang')
@section('content')

<div x-data="{
    selected: [],
    allSelected: false,
    toggleAll() {
        this.allSelected = !this.allSelected;
        if (this.allSelected) {
            this.selected = [
                @foreach ($products as $product)
                    '{{ $product->id }}',
                @endforeach
            ];
        } else {
            this.selected = [];
        }
    },
    updateSelectAll() {
        this.allSelected = this.selected.length === {{ count($products) }};
    }
}" class="bg-white rounded-lg shadow p-6 mb-6">

    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold shadow">
                <i class="fas fa-plus"></i> Tambah Barang
            </a>
            <form x-show="selected.length > 0" method="POST" action="{{ route('products.bulk_delete') }}" class="inline-block" onsubmit="return confirm('Hapus ' + this.closest('div').querySelector('[x-text]').innerText + ' item terpilih?')">
                @csrf
                <input type="hidden" name="ids[]" x-model="selected">
                <button type="submit" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-bold shadow">
                    <i class="fas fa-trash"></i> Hapus (<span x-text="selected.length"></span>)
                </button>
            </form>
        </div>
        
        {{-- Search --}}
        <form action="{{ route('products.index') }}" method="GET" class="relative w-full max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="     Cari barang..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 outline-none transition text-sm">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-right text-gray-400">
                <i class="fas fa-search"></i>
            </span>
        </form>
    </div>

    <div class="overflow-x-auto border rounded-lg">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-100 text-xs uppercase font-bold">
                <tr>
                    <th class="px-3 py-2 w-10">
                        <input type="checkbox" @click="toggleAll()" x-model="allSelected" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </th>
                    <th class="px-3 py-2">Nama Barang</th>
                    <th class="px-3 py-2">Kategori</th>
                    <th class="px-3 py-2 text-right">Harga</th>
                    <th class="px-3 py-2 text-center">Stok Akhir</th>
                    <th class="px-3 py-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="border-t hover:bg-gray-50 transition" :class="{ 'bg-indigo-50': selected.includes('{{ $product->id }}') }">
                        <td class="px-3 py-2">
                            <input type="checkbox" value="{{ $product->id }}" x-model="selected" @click="updateSelectAll()" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </td>
                        <td class="px-3 py-2 font-bold text-gray-800">{{ $product->name }}</td>
                        <td class="px-3 py-2">
                            @if($product->category)
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                    {{ $product->category->name }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-right">{{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-center">
                            @if($product->stock <= $product->min_stock)
                                <span class="text-red-600 font-bold">{{ $product->stock }}</span>
                            @else
                                <span class="text-green-600 font-bold">{{ $product->stock }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-right">
                            @include('partials.action_buttons', [
                                'edit' => route('products.edit', $product->id),
                                'delete' => route('products.destroy', $product->id),
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada data barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection
