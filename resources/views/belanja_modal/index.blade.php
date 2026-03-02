@extends('layouts.admin')

@section('header', 'Daftar Kontrak Belanja Modal')
@section('content')

<div x-data="{
    selected: [],
    allSelected: false,
    toggleAll() {
        this.allSelected = !this.allSelected;
        if (this.allSelected) {
            this.selected = [
                @foreach ($items as $row)
                    '{{ $row['id'] }}',
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
            <a href="{{ route('reports.belanja.modal.form') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold shadow">
                <i class="fas fa-plus"></i> Tambah Kontrak
            </a>
            <a href="{{ route('reports.belanja.modal.preview_all') }}" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-bold shadow">
                <i class="fas fa-print"></i> Print
            </a>
            <form x-show="selected.length > 0" method="POST" action="{{ route('reports.belanja.modal.bulk_delete') }}" class="inline-block" onsubmit="return confirm('Hapus ' + this.closest('div').querySelector('[x-text]').innerText + ' item terpilih?')">
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
                    <th class="px-3 py-2">No.</th>
                    <th class="px-3 py-2">Tahun</th>
                    <th class="px-3 py-2 text-center">Jumlah Kontrak</th>
                    <th class="px-3 py-2 text-right">Total Nilai Kontrak (Rp)</th>
                    <th class="px-3 py-2">Terakhir Diperbarui</th>
                    <th class="px-3 py-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $i => $row)
                    @php $hl = request('highlight'); @endphp
                    <tr class="border-t hover:bg-gray-50 transition {{ $hl === ($row['id'] ?? null) ? 'bg-orange-50' : '' }}" :class="{ 'bg-indigo-50': selected.includes('{{ $row['id'] }}') }">
                        <td class="px-3 py-2">
                            <input type="checkbox" value="{{ $row['id'] }}" x-model="selected" @click="updateSelectAll()" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </td>
                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                        <td class="px-3 py-2">{{ $row['tahun'] ?: '-' }}</td>
                        <td class="px-3 py-2 text-center">{{ $row['kontrak_count'] }}</td>
                        <td class="px-3 py-2 text-right font-medium">{{ number_format($row['nilai_total'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-gray-500 text-xs">{{ \Carbon\Carbon::createFromTimestamp($row['updated'])->translatedFormat('d F Y H:i') }}</td>
                        <td class="px-3 py-2 text-right">
                            @include('partials.action_buttons', [
                                'show' => route('reports.belanja.modal.show', $row['id']),
                                'edit' => route('reports.belanja.modal.edit', $row['id']),
                                'delete' => route('reports.belanja.modal.delete', $row['id']),
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-3 py-6 text-center text-gray-500" colspan="7">Belum ada data belanja modal</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
