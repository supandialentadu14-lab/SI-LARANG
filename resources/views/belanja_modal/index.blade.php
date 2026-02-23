@extends('layouts.admin')

@section('header', 'Daftar Kontrak Belanja Modal')
@section('content')

<div class="bg-white rounded-lg shadow p-6">
    @php $flash = session('status') ?? ($status ?? null); @endphp
    @if($flash)
        <div class="mb-4 px-4 py-3 bg-green-50 text-green-700 border border-green-200 rounded">
            {{ $flash }}
        </div>
    @endif
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold">Ringkasan Belanja Modal</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.belanja.modal.form') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold shadow">
                <i class="fas fa-plus"></i>
                Tambah Kontrak
            </a>
            <a href="{{ route('reports.belanja.modal.preview_all') }}" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-bold shadow">
                <i class="fas fa-print"></i>
                Preview Semua
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2">No.</th>
                    <th class="px-3 py-2">Tahun</th>
                    <th class="px-3 py-2">Jumlah Kontrak</th>
                    <th class="px-3 py-2">Total Nilai Kontrak (Rp)</th>
                    <th class="px-3 py-2">Terakhir Diperbarui</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $i => $row)
                    @php $hl = request('highlight'); @endphp
                    <tr class="border-t {{ $hl === ($row['id'] ?? null) ? 'bg-orange-50' : '' }}">
                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                        <td class="px-3 py-2">{{ $row['tahun'] ?: '-' }}</td>
                        <td class="px-3 py-2 text-center">{{ $row['kontrak_count'] }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row['nilai_total'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2">{{ \Carbon\Carbon::createFromTimestamp($row['updated'])->translatedFormat('d F Y H:i') }}</td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('reports.belanja.modal.show', $row['id']) }}" class="inline-flex items-center gap-1 text-gray-700 hover:text-gray-900 mr-3">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('reports.belanja.modal.edit', $row['id']) }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-700 mr-3">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('reports.belanja.modal.delete', $row['id']) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-700" onclick="return confirm('Hapus data ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-3 py-6 text-center text-gray-500" colspan="6">Belum ada data belanja modal</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
