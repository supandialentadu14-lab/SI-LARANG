@extends('layouts.admin')

@section('title', 'Daftar BA Stock Opname')
@section('header', 'Daftar Berita Acara Stock Opname')
@section('subheader', 'Lihat dan edit BA Stock Opname yang telah disimpan')

@section('actions')
    <a href="{{ route('reports.opname.form') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow">
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
                        <th class="px-6 py-3">Tempat</th>
                        <th class="px-6 py-3">Pelaksana</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($items as $item)
                        <tr class="hover:bg-orange-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $item['nomor'] }}</td>
                            <td class="px-6 py-4">{{ \Illuminate\Support\Carbon::parse($item['tanggal'])->translatedFormat('d F Y') }}</td>
                            <td class="px-6 py-4">{{ $item['tempat'] }}</td>
                            <td class="px-6 py-4">{{ $item['pihak_kedua'] }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('reports.opname.show', $item['id']) }}" class="inline-block text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded transition">
                                    <i class="fas fa-eye"></i></a>
                                <a href="{{ route('reports.opname.edit', $item['id']) }}" class="inline-block text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded transition">
                                    <i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('reports.opname.delete', $item['id']) }}" class="inline-block" onsubmit="return confirm('Hapus berita acara ini?')">
                                    @csrf
                                    <button type="submit"
                                        class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                Belum ada BA Stock Opname disimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

