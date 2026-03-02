@extends('layouts.admin')

@section('header', 'Daftar Berita Acara Stock Opname')
@section('content')
    <div x-data="{
        selected: [],
        allSelected: false,
        toggleAll() {
            this.allSelected = !this.allSelected;
            if (this.allSelected) {
                this.selected = [
                    @foreach ($items as $item)
                        '{{ $item['id'] }}',
                    @endforeach
                ];
            } else {
                this.selected = [];
            }
        },
        updateSelectAll() {
            this.allSelected = this.selected.length === {{ count($items) }};
        }
    }" class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.opname.form') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold shadow">
                    <i class="fas fa-plus"></i> Buat Baru
                </a>
                <form x-show="selected.length > 0" method="POST" action="{{ route('reports.opname.bulk_delete') }}" class="inline-block" onsubmit="return confirm('Hapus ' + this.closest('div').querySelector('[x-text]').innerText + ' item terpilih?')">
                    @csrf
                    <input type="hidden" name="ids[]" x-model="selected">
                    <button type="submit" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-bold shadow">
                        <i class="fas fa-trash"></i> Hapus (<span x-text="selected.length"></span>)
                    </button>
                </form>
            </div>
        </div>
        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-xs uppercase font-bold">
                    <tr>
                        <th class="px-3 py-2 w-10">
                            <input type="checkbox" @click="toggleAll()" x-model="allSelected" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </th>
                        <th class="px-3 py-2">Nomor</th>
                        <th class="px-3 py-2">Tanggal</th>
                        <th class="px-3 py-2">Tempat</th>
                        <th class="px-3 py-2">Pelaksana</th>
                        <th class="px-3 py-2">Diperbarui</th>
                        <th class="px-3 py-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr class="border-t hover:bg-gray-50 transition" :class="{ 'bg-indigo-50': selected.includes('{{ $item['id'] }}') }">
                            <td class="px-3 py-2">
                                <input type="checkbox" value="{{ $item['id'] }}" x-model="selected" @click="updateSelectAll()" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </td>
                            <td class="px-3 py-2">{{ $item['nomor'] }}</td>
                            <td class="px-3 py-2">{{ \Illuminate\Support\Carbon::parse($item['tanggal'])->translatedFormat('d F Y') }}</td>
                            <td class="px-3 py-2">{{ $item['tempat'] }}</td>
                            <td class="px-3 py-2">{{ $item['pihak_kedua'] }}</td>
                            <td class="px-3 py-2 text-gray-500 text-xs">
                                {{ \Carbon\Carbon::createFromTimestamp($item['updated'])->translatedFormat('d F Y H:i') }}
                            </td>
                            <td class="px-3 py-2 text-right">
                                @include('partials.action_buttons', [
                                    'show' => route('reports.opname.show', $item['id']),
                                    'edit' => route('reports.opname.edit', $item['id']),
                                    'delete' => route('reports.opname.delete', $item['id']),
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-gray-500">Belum ada BA Stock Opname disimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

