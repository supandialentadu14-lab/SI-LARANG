@extends('layouts.admin')

@section('header', 'Penyedia')
@section('content')

<div x-data="{
    selected: [],
    allSelected: false,
    toggleAll() {
        this.allSelected = !this.allSelected;
        if (this.allSelected) {
            this.selected = [
                @foreach ($suppliers as $supplier)
                    '{{ $supplier->id }}',
                @endforeach
            ];
        } else {
            this.selected = [];
        }
    },
    updateSelectAll() {
        this.allSelected = this.selected.length === {{ count($suppliers) }};
    }
}" class="bg-white rounded-lg shadow p-6 mb-6">

    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('suppliers.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold shadow">
                <i class="fas fa-plus"></i> Tambah Penyedia
            </a>
            <form x-show="selected.length > 0" method="POST" action="{{ route('suppliers.bulk_delete') }}" class="inline-block" onsubmit="return confirm('Hapus ' + this.closest('div').querySelector('[x-text]').innerText + ' item terpilih?')">
                @csrf
                <input type="hidden" name="ids[]" x-model="selected">
                <button type="submit" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-bold shadow">
                    <i class="fas fa-trash"></i> Hapus (<span x-text="selected.length"></span>)
                </button>
            </form>
        </div>
        
        {{-- Search --}}
        <form action="{{ route('suppliers.index') }}" method="GET" class="relative w-full max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="     Cari penyedia..."
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
                    <th class="px-3 py-2">Nama Toko/CV/PT</th>
                    <th class="px-3 py-2">Direktur/Pemilik</th>
                    <th class="px-3 py-2">Alamat</th>
                    <th class="px-3 py-2">NPWP</th>
                    <th class="px-3 py-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr class="border-t hover:bg-gray-50 transition" :class="{ 'bg-indigo-50': selected.includes('{{ $supplier->id }}') }">
                        <td class="px-3 py-2">
                            <input type="checkbox" value="{{ $supplier->id }}" x-model="selected" @click="updateSelectAll()" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </td>
                        <td class="px-3 py-2 font-bold text-gray-800">{{ $supplier->name }}</td>
                        <td class="px-3 py-2">{{ $supplier->dir ?: '-' }}</td>
                        <td class="px-3 py-2">{{ Str::limit($supplier->address, 40) ?: '-' }}</td>
                        <td class="px-3 py-2 font-mono text-xs text-gray-600">{{ $supplier->npwp ?: '-' }}</td>
                        <td class="px-3 py-2 text-right">
                            @include('partials.action_buttons', [
                                'edit' => route('suppliers.edit', $supplier->id),
                                'delete' => route('suppliers.destroy', $supplier->id),
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada data penyedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection
