@extends('layouts.admin')

@section('header', 'Edit Berita Acara Pemeriksaan')
@section('subheader', 'Perbarui data berdasarkan Nota Pesanan')

@section('content')
    <form action="{{ route('reports.pemeriksaan.report') }}" method="POST" class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md-grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Nota Pesanan</label>
                <select name="nota_nomor" class="w-full px-4 py-2 rounded-lg border border-gray-300" required>
                    <option value="">-- pilih nomor --</option>
                    @foreach ($notaDocs as $n)
                        <option value="{{ $n['nomor'] }}" {{ ($data['nota_nomor'] ?? '') === ($n['nomor'] ?? '') ? 'selected' : '' }}>
                            {{ $n['nomor'] }} • {{ \Carbon\Carbon::parse($n['tanggal'] ?? now())->translatedFormat('d F Y') }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Data barang & penyedia akan diprefill dari Nota Pesanan tersebut.</p>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Berita Acara Pemeriksaan</label>
                <input type="text" name="nomor" value="{{ old('nomor', $data['nomor'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300" placeholder="contoh: 0004/BAP/KOMINFO/XI/2025" required>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tempat Surat</label>
                <input type="text" name="tempat" value="{{ old('tempat', $data['tempat'] ?? ($opd->nama_opd ?? '')) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Surat</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $data['tanggal'] ?? now()->toDateString()) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300" required>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 pt-2">
            <button type="submit" formaction="{{ route('reports.pemeriksaan.save') }}" class="btn btn-success text-white">
                <i class="fas fa-save"></i> Perbarui
            </button>
            <button type="submit" formaction="{{ route('reports.pemeriksaan.report') }}" class="btn btn-warning">
                <i class="fas fa-file-alt"></i> Preview Laporan
            </button>
        </div>
    </form>
@endsection
