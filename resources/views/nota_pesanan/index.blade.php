@extends('layouts.admin')

@section('header', 'Daftar Nota Pesanan')
@section('content')
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('reports.nota.form') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold shadow">
                <i class="fas fa-plus"></i> Buat Nota Pesanan
            </a>
        </div>
        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-xs uppercase font-bold">
                    <tr>
                        <th class="px-3 py-2">Nomor</th>
                        <th class="px-3 py-2">Tanggal</th>
                        <th class="px-3 py-2">Belanja</th>
                        <th class="px-3 py-2">Total</th>
                        <th class="px-3 py-2">Diperbarui</th>
                        <th class="px-3 py-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ $item['nomor'] }}</td>
                            <td class="px-3 py-2">{{ $item['tanggal'] ? \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('d F Y') : '-' }}</td>
                            <td class="px-3 py-2">{{ $item['belanja'] }}</td>
                            <td class="px-3 py-2">{{ number_format($item['total'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2">{{ \Carbon\Carbon::createFromTimestamp($item['updated'])->translatedFormat('d F Y H:i') }}</td>
                            <td class="px-3 py-2 text-right">
                                @include('partials.action_buttons', [
                                    'show' => route('reports.nota.show', $item['id']),
                                    'edit' => route('reports.nota.edit', $item['id']),
                                    'delete' => route('reports.nota.delete', $item['id']),
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada nota pesanan tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

