@extends('layouts.admin')

@section('header', 'Daftar OPD')
@section('subheader', 'Data OPD yang tersimpan')

@section('actions')
    <a href="{{ route('settings.opd.edit') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow">
        Input Data OPD
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-3">Nama OPD</th>
                        <th class="px-6 py-3">Alamat OPD</th>
                        <th class="px-6 py-3">Kepala OPD</th>
                        <th class="px-6 py-3">Pengurus OPD</th>
                        <th class="px-6 py-3">Pengurus Pengguna OPD</th>
                        <th class="px-6 py-3">Update</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($items as $item)
                        <tr class="hover:bg-orange-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $item->nama_opd }}</td>
                            <td class="px-6 py-4">{{ $item->alamat_opd }}</td>
                            <td class="px-6 py-4">{{ $item->kepala_nama }}</td>
                            <td class="px-6 py-4">{{ $item->pengurus_nama }}</td>
                            <td class="px-6 py-4">{{ $item->pengguna_nama }}</td>
                            <td class="px-6 py-4">{{ $item->updated_at?->translatedFormat('d F Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('settings.opd.edit') }}" class="inline-block text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded transition">
                                    <i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                Belum ada data OPD tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
