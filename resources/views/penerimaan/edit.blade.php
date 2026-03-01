@extends('layouts.admin')

@section('header', 'Edit Berita Acara Penerimaan')
@section('subheader', 'Perbarui data dan buat laporan penerimaan')

@section('content')
    <form method="POST" action="{{ route('reports.penerimaan.report') }}" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf
        <input type="hidden" name="id" value="{{ session('penerimaan_current_id') }}">
        <div class="rounded-xl shadow-md border border-gray-200 bg-white p-6">
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="p-3 border border-gray-200 rounded-lg bg-white">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nomor Berita Acara Penerimaan</label>
                        <input type="text" name="nomor" value="{{ old('nomor', $data['nomor'] ?? '') }}" class="w-full rounded-lg border-2 border-indigo-500 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div class="p-3 border border-gray-200 rounded-lg bg-white">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $data['tanggal'] ?? now()->toDateString()) }}" class="w-full rounded-lg border-2 border-indigo-500 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div class="p-3 border border-gray-200 rounded-lg bg-white">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tempat</label>
                        <input type="text" name="tempat" value="{{ old('tempat', $data['tempat'] ?? ($opd->nama_opd ?? '')) }}" class="w-full rounded-lg border-2 border-indigo-500 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="p-3 border border-gray-200 rounded-lg bg-white">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nomor BAP Pemeriksaan (Prefill)</label>
                        <input type="text" name="pemeriksaan_nomor" list="opt-pemeriksaan" value="{{ old('pemeriksaan_nomor', ($data['pemeriksaan_nomor'] ?? '') ?: (request('pemeriksaan_nomor') ?? '')) }}" class="w-full rounded-lg border-2 border-indigo-500 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                        <datalist id="opt-pemeriksaan">
                            @foreach($docs as $doc)
                                <option value="{{ $doc['nomor'] }}">{{ $doc['nomor'] }}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="p-3 border border-gray-200 rounded-lg bg-white">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Catatan</label>
                        <input type="text" name="catatan" class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 pt-2">
            <button type="submit" formaction="{{ route('reports.penerimaan.save') }}" class="btn btn-success text-white">
                <i class="fas fa-save"></i> Perbarui
            </button>
            <button type="submit" formaction="{{ route('reports.penerimaan.report') }}" class="btn btn-warning">
                <i class="fas fa-file-alt"></i> Preview Laporan
            </button>
        </div>
    </form>
@endsection
