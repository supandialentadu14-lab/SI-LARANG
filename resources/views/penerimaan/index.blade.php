@extends('layouts.admin')

@section('title', 'Daftar BA Penerimaan')
@section('header', 'Daftar Berita Acara Penerimaan Barang/Pekerjaan')
@section('subheader', 'Lihat dan edit BA Penerimaan yang telah disimpan')

@section('actions')
    <a href="{{ route('reports.penerimaan.form') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow">
        Buat Baru
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
        @php $flash = session('status') ?? ($status ?? null); @endphp
        @if($flash)
            <div class="mb-4 px-4 py-3 bg-green-50 text-green-700 border border-green-200 rounded">
                {{ $flash }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-3">Nomor</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Nomor BAP Pemeriksaan</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($items as $item)
                        <tr class="hover:bg-orange-50 transition">
                            <td class="px-6 py-3">{{ $item['nomor'] ?: '-' }}</td>
                            <td class="px-6 py-3">{{ $item['tanggal'] ?: '-' }}</td>
                            <td class="px-6 py-3">{{ $item['pemeriksaan_nomor'] ?: '-' }}</td>
                            <td class="px-6 py-3 text-right">{{ number_format((int)($item['total'] ?? 0), 0, ',', '.') }}</td>
                            <td class="px-6 py-3 text-right">
                                @include('partials.action_buttons', [
                                    'show' => route('reports.penerimaan.show', $item['id']),
                                    'edit' => route('reports.penerimaan.edit', $item['id']),
                                    'delete' => route('reports.penerimaan.delete', $item['id']),
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-3 py-6 text-center text-gray-500" colspan="5">Belum ada data penerimaan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

