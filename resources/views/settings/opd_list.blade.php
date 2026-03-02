@extends('layouts.admin')

@section('header', 'Daftar OPD')
@section('subheader', 'Data OPD yang tersimpan')

@section('content')

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('settings.opd.edit') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold shadow">
            <i class="fas fa-plus"></i>Edit Data OPD
        </a>
    </div>

    <div class="overflow-x-auto border rounded-lg">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-100 text-xs uppercase font-bold">
                <tr>
                    <th class="px-3 py-2">Nama OPD</th>
                    <th class="px-3 py-2">Alamat OPD</th>
                    <th class="px-3 py-2">Kepala OPD</th>
                    <th class="px-3 py-2">Pengurus Barang OPD</th>
                    <th class="px-3 py-2">Pengurus Barang Pembantu OPD</th>
                    <th class="px-3 py-2">Update</th>
                    <th class="px-3 py-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr class="border-t hover:bg-gray-50 transition">
                        <td class="px-3 py-2 font-bold text-gray-800">{{ $item->nama_opd }}</td>
                        <td class="px-3 py-2">{{ $item->alamat_opd }}</td>
                        <td class="px-3 py-2">
                            <div class="text-xs">
                                <div class="font-bold">{{ $item->kepala_nama }}</div>
                                <div class="text-gray-500">{{ $item->kepala_nip }}</div>
                            </div>
                        </td>
                        <td class="px-3 py-2">
                            <div class="text-xs">
                                <div class="font-bold">{{ $item->pengurus_nama }}</div>
                                <div class="text-gray-500">{{ $item->pengurus_nip }}</div>
                            </div>
                        </td>
                        <td class="px-3 py-2">{{ $item->pengguna_nama }}</td>
                        <td class="px-3 py-2 text-xs text-gray-500">
                            {{ $item->updated_at?->translatedFormat('d F Y H:i') }}
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('settings.opd.edit') }}" class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 text-xs font-bold transition">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-gray-500">
                            Belum ada data OPD tersimpan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
