{{-- Menggunakan layout admin --}}
@extends('layouts.admin')

@section('header', 'Jenis Belanja')
@section('content')

<div x-data="{
    selected: [],
    allSelected: false,
    toggleAll() {
        this.allSelected = !this.allSelected;
        if (this.allSelected) {
            this.selected = [
                @foreach ($categories as $category)
                    '{{ $category->id }}',
                @endforeach
            ];
        } else {
            this.selected = [];
        }
    },
    updateSelectAll() {
        this.allSelected = this.selected.length === {{ count($categories) }};
    }
}" class="bg-white rounded-lg shadow p-6 mb-6">

    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold shadow">
                <i class="fas fa-plus"></i> Tambah Jenis Belanja
            </a>
            <form x-show="selected.length > 0" method="POST" action="{{ route('categories.bulk_delete') }}" class="inline-block" onsubmit="return confirm('Hapus ' + this.closest('div').querySelector('[x-text]').innerText + ' item terpilih?')">
                @csrf
                <input type="hidden" name="ids[]" x-model="selected">
                <button type="submit" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-bold shadow">
                    <i class="fas fa-trash"></i> Hapus (<span x-text="selected.length"></span>)
                </button>
            </form>
        </div>
        
        {{-- Search --}}
        <form action="{{ route('categories.index') }}" method="GET" class="relative w-full max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="     Cari jenis belanja..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 outline-none transition text-sm">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
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
                    <th class="px-3 py-2">Nama Jenis Belanja</th>
                    <th class="px-3 py-2">Keterangan</th>
                    <th class="px-3 py-2 text-center">Barang</th>
                    <th class="px-3 py-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr class="border-t hover:bg-gray-50 transition" :class="{ 'bg-indigo-50': selected.includes('{{ $category->id }}') }">
                        <td class="px-3 py-2">
                            <input type="checkbox" value="{{ $category->id }}" x-model="selected" @click="updateSelectAll()" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </td>
                        <td class="px-3 py-2 font-bold text-gray-800">{{ $category->name }}</td>
                        <td class="px-3 py-2 text-gray-500">{{ Str::limit($category->description, 60) ?: '-' }}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded text-xs font-bold">
                                {{ $category->products_count }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            @include('partials.action_buttons', [
                                'edit' => route('categories.edit', $category->id),
                                'delete' => route('categories.destroy', $category->id),
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-gray-500">Belum ada data jenis belanja.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>
@endsection
