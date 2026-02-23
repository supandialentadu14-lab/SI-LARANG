@extends('layouts.admin')

@section('header', 'Daftar Berita Acara Pemeriksaan')
@section('content')
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('reports.pemeriksaan.form') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold shadow">
                <i class="fas fa-plus"></i> Buat Berita Acara Pemeriksaan
            </a>
        </div>
        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-xs uppercase font-bold">
                    <tr>
                        <th class="px-3 py-2">Nomor BAP</th>
                        <th class="px-3 py-2">Tanggal</th>
                        <th class="px-3 py-2">Nomor Nota</th>
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
                            <td class="px-3 py-2">{{ $item['nota_nomor'] }}</td>
                            <td class="px-3 py-2">{{ number_format($item['total'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2">{{ \Carbon\Carbon::createFromTimestamp($item['updated'])->translatedFormat('d F Y H:i') }}</td>
                            <td class="px-3 py-2 text-right">
                                @include('partials.action_buttons', [
                                    'show' => route('reports.pemeriksaan.show', $item['id']),
                                    'edit' => route('reports.pemeriksaan.edit', $item['id']),
                                    'delete' => route('reports.pemeriksaan.delete', $item['id']),
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada BAP tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

