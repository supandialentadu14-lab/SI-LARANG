@extends('layouts.admin')

@section('header', 'Data OPD')
@section('subheader', 'Digunakan untuk prefilling pada form dan laporan')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50">
                <h6 class="font-bold text-indigo-700">Profil OPD</h6>
            </div>
            <form action="{{ route('settings.opd.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama OPD</label>
                    <input type="text" name="nama_opd" value="{{ old('nama_opd', $setting->nama_opd) }}" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition bg-white">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Alamat OPD</label>
                    <input type="text" name="alamat_opd" value="{{ old('alamat_opd', $setting->alamat_opd) }}" class="w-full px- py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition bg-white">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-3">
                        <h3 class="font-bold text-gray-800">Kepala OPD</h3>
                        <input type="text" name="kepala_nama" value="{{ old('kepala_nama', $setting->kepala_nama) }}" placeholder="Nama Lengkap" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                        <input type="text" name="kepala_pangkat" value="{{ old('kepala_pangkat', $setting->kepala_pangkat) }}" placeholder="Pangkat" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                        <input type="text" name="kepala_jabatan" value="{{ old('kepala_jabatan', $setting->kepala_jabatan) }}" placeholder="Jabatan" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                        <input type="text" name="kepala_nip" value="{{ old('kepala_nip', $setting->kepala_nip) }}" placeholder="NIP" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                    </div>
                    <div class="space-y-3">
                        <h3 class="font-bold text-gray-800">Pengurus Barang</h3>
                        <input type="text" name="pengurus_nama" value="{{ old('pengurus_nama', $setting->pengurus_nama) }}" placeholder="Nama Lengkap" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                        <input type="text" name="pengurus_pangkat" value="{{ old('pengurus_pangkat', $setting->pengurus_pangkat) }}" placeholder="Pangkat" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                        <input type="text" name="pengurus_jabatan" value="{{ old('pengurus_jabatan', $setting->pengurus_jabatan) }}" placeholder="Jabatan" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                        <input type="text" name="pengurus_nip" value="{{ old('pengurus_nip', $setting->pengurus_nip) }}" placeholder="NIP" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                    </div>
                    <div class="space-y-3">
                        <h3 class="font-bold text-gray-800">Pengurus Barang Pembantu</h3>
                        <input type="text" name="pengguna_nama" value="{{ old('pengguna_nama', $setting->pengguna_nama) }}" placeholder="Nama Lengkap" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                        <input type="text" name="pengguna_pangkat" value="{{ old('pengguna_pangkat', $setting->pengguna_pangkat) }}" placeholder="Pangkat" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                        <input type="text" name="pengguna_jabatan" value="{{ old('pengguna_jabatan', $setting->pengguna_jabatan) }}" placeholder="Jabatan" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                        <input type="text" name="pengguna_nip" value="{{ old('pengguna_nip', $setting->pengguna_nip) }}" placeholder="NIP" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                    </div>
                </div>

                

                @include('partials.form-actions', [
                    'saveText' => 'Simpan Data OPD',
                ])
            </form>
        </div>
    </div>
@endsection
