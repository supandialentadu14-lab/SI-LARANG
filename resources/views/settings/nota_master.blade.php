@extends('layouts.admin')
@section('header', 'Data Master Nota Pesanan')
@section('subheader', 'Isikan data pihak-pihak untuk prefill Nota Pesanan')
@section('content')
    <form action="{{ route('settings.nota.master.update') }}" method="POST" class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg border border-gray-100 p-6 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="font-bold text-gray-800">OPD</h3>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama OPD</label>
                    <input type="text" name="opd[nama]" value="{{ old('opd.nama', $data['opd']['nama'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Alamat OPD</label>
                    <input type="text" name="opd[alamat]" value="{{ old('opd.alamat', $data['opd']['alamat'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="font-bold text-gray-800">PPK</h3>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama</label>
                    <input type="text" name="ppk[nama]" value="{{ old('ppk.nama', $data['ppk']['nama'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">NIP</label>
                    <input type="text" name="ppk[nip]" value="{{ old('ppk.nip', $data['ppk']['nip'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Alamat</label>
                    <input type="text" name="ppk[alamat]" value="{{ old('ppk.alamat', $data['ppk']['alamat'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
            </div>
            <div class="space-y-4"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-4">
                <h3 class="font-bold text-gray-800">Pejabat Pengadaan</h3>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama</label>
                    <input type="text" name="pejabat[nama]" value="{{ old('pejabat.nama', $data['pejabat']['nama'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">NIP</label>
                    <input type="text" name="pejabat[nip]" value="{{ old('pejabat.nip', $data['pejabat']['nip'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
            </div>
            <div class="space-y-4">
                <h3 class="font-bold text-gray-800">PPTK</h3>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama</label>
                    <input type="text" name="pptk[nama]" value="{{ old('pptk.nama', $data['pptk']['nama'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">NIP</label>
                    <input type="text" name="pptk[nip]" value="{{ old('pptk.nip', $data['pptk']['nip'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
            </div>
            <div class="space-y-4">
                <h3 class="font-bold text-gray-800">Bendahara</h3>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama</label>
                    <input type="text" name="bendahara[nama]" value="{{ old('bendahara.nama', $data['bendahara']['nama'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">NIP</label>
                    <input type="text" name="bendahara[nip]" value="{{ old('bendahara.nip', $data['bendahara']['nip'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="font-bold text-gray-800">Pengurus Barang</h3>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama</label>
                    <input type="text" name="pengurus_barang[nama]" value="{{ old('pengurus_barang.nama', $data['pengurus_barang']['nama'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">NIP</label>
                    <input type="text" name="pengurus_barang[nip]" value="{{ old('pengurus_barang.nip', $data['pengurus_barang']['nip'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
            </div>
            <div class="space-y-4">
                <h3 class="font-bold text-gray-800">Pengurus Barang Pengguna</h3>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama</label>
                    <input type="text" name="pengurus_pengguna[nama]" value="{{ old('pengurus_pengguna.nama', $data['pengurus_pengguna']['nama'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">NIP</label>
                    <input type="text" name="pengurus_pengguna[nip]" value="{{ old('pengurus_pengguna.nip', $data['pengurus_pengguna']['nip'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
            </div>
        </div>
        @include('partials.form-actions', [
            'saveText' => 'Simpan',
        ])
    </form>
@endsection
