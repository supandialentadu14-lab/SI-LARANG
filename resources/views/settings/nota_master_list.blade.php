@extends('layouts.admin')
@section('header', 'Ringkasan Data Master Nota')
@section('subheader', 'Data yang dipakai untuk prefill Nota Pesanan')
@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50">
                <h6 class="font-bold text-indigo-700">PPK</h6>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                <div>
                    <div class="text-gray-500">Nama</div>
                    <div class="font-bold text-gray-800">{{ $data['ppk']['nama'] ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-gray-500">NIP</div>
                    <div class="font-bold text-gray-800">{{ $data['ppk']['nip'] ?? '-' }}</div>
                </div>
                <div class="md:col-span-3">
                    <div class="text-gray-500">Alamat</div>
                    <div class="font-bold text-gray-800">{{ $data['ppk']['alamat'] ?? '-' }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50">
                <h6 class="font-bold text-indigo-700">Pejabat Pengadaan</h6>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div>
                    <div class="text-gray-500">Nama</div>
                    <div class="font-bold text-gray-800">{{ $data['pejabat']['nama'] ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-gray-500">NIP</div>
                    <div class="font-bold text-gray-800">{{ $data['pejabat']['nip'] ?? '-' }}</div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50">
                    <h6 class="font-bold text-indigo-700">PPTK</h6>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <div class="text-gray-500">Nama</div>
                        <div class="font-bold text-gray-800">{{ $data['pptk']['nama'] ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">NIP</div>
                        <div class="font-bold text-gray-800">{{ $data['pptk']['nip'] ?? '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50">
                    <h6 class="font-bold text-indigo-700">Bendahara</h6>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <div class="text-gray-500">Nama</div>
                        <div class="font-bold text-gray-800">{{ $data['bendahara']['nama'] ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">NIP</div>
                        <div class="font-bold text-gray-800">{{ $data['bendahara']['nip'] ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50">
                    <h6 class="font-bold text-indigo-700">Pengurus Barang</h6>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <div class="text-gray-500">Nama</div>
                        <div class="font-bold text-gray-800">{{ $data['pengurus_barang']['nama'] ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">NIP</div>
                        <div class="font-bold text-gray-800">{{ $data['pengurus_barang']['nip'] ?? '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50">
                    <h6 class="font-bold text-indigo-700">Pengurus Barang Pengguna</h6>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <div class="text-gray-500">Nama</div>
                        <div class="font-bold text-gray-800">{{ $data['pengurus_pengguna']['nama'] ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">NIP</div>
                        <div class="font-bold text-gray-800">{{ $data['pengurus_pengguna']['nip'] ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end">
            <a href="{{ route('settings.nota.master.edit') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow">Ubah Data</a>
        </div>
    </div>
@endsection
