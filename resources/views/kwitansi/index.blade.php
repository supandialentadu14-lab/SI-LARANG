@extends('layouts.admin')

@section('header', 'Daftar Kwitansi')
@section('subheader', 'Kelola dokumen kwitansi yang tersimpan')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">List Dokumen</h3>
        <a href="{{ route('reports.kwitansi.form') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
            <i class="fas fa-plus mr-2"></i>Buat Baru
        </a>
    </div>
    
    @if(session('status'))
        <div class="p-4 bg-green-50 text-green-700 border-l-4 border-green-500">
            {{ session('status') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 w-12 text-center">NO</th>
                    <th class="px-6 py-3">NOMOR KWITANSI</th>
                    <th class="px-6 py-3">TANGGAL</th>
                    <th class="px-6 py-3">NOMOR BAP PENERIMAAN</th>
                    <th class="px-6 py-3">JUMLAH (RP)</th>
                    <th class="px-6 py-3 text-right">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $idx => $item)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-center">{{ $idx + 1 }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $item['nomor_kwt'] ?: '-' }}</td>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('d F Y') }}
                        </td>
                        <td class="px-6 py-4">{{ $item['penerimaan_nomor'] ?: '-' }}</td>
                        <td class="px-6 py-4 font-mono">
                            {{ number_format($item['jumlah'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('reports.kwitansi.show', $item['id']) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('reports.kwitansi.edit', $item['id']) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('reports.kwitansi.delete', $item['id']) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus dokumen ini?')">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Belum ada dokumen tersimpan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection