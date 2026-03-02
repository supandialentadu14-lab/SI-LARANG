@extends('layouts.admin')

@section('title', 'Buat Berita Acara Penerimaan')
@section('header', 'Berita Acara Penerimaan')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-orange-50">
                <h6 class="font-bold text-blue-700">Form Berita Acara Penerimaan</h6>
            </div>

            <form method="POST" action="{{ route('reports.penerimaan.save') }}" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="id" value="{{ session('penerimaan_current_id') }}">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Berita Acara</label>
                        <input type="text" name="nomor" value="{{ old('nomor', $data['nomor'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $data['tanggal'] ?? now()->toDateString()) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tempat</label>
                        <input type="text" name="tempat" value="{{ old('tempat', $data['tempat'] ?? ($opd->nama_opd ?? '')) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nomor BAP Pemeriksaan (Prefill)</label>
                        <input type="text" name="pemeriksaan_nomor" list="opt-pemeriksaan" value="{{ old('pemeriksaan_nomor', ($data['pemeriksaan_nomor'] ?? '') ?: (request('pemeriksaan_nomor') ?? '')) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" placeholder="Pilih atau ketik nomor...">
                        <datalist id="opt-pemeriksaan">
                            @foreach($docs as $doc)
                                <option value="{{ $doc['nomor'] }}">{{ $doc['nomor'] }}</option>
                            @endforeach
                        </datalist>
                        <p class="text-xs text-gray-500 mt-1">Isi otomatis data dari BAP Pemeriksaan</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Catatan</label>
                        <input type="text" name="catatan" value="{{ old('catatan', $data['catatan'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                    </div>
                </div>

                @include('partials.form-actions', [
                    'backRoute' => route('reports.penerimaan.list'),
                    'previewRoute' => route('reports.penerimaan.report'),
                ])
            </form>
        </div>
    </div>
@endsection
