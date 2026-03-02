@extends('layouts.admin')

@section('title', 'Edit Berita Acara Pemeriksaan')
@section('header', 'Berita Acara Pemeriksaan')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-orange-50">
                <h6 class="font-bold text-blue-700">Form Edit Berita Acara Pemeriksaan</h6>
            </div>

            <form action="{{ route('reports.pemeriksaan.report') }}" method="POST" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="id" value="{{ session('bap_current_id') }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Nota Pesanan</label>
                        <select name="nota_nomor" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
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
                        <input type="text" name="nomor" value="{{ old('nomor', $data['nomor'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" placeholder="contoh: 0004/BAP/KOMINFO/XI/2025" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tempat Surat</label>
                        <input type="text" name="tempat" value="{{ old('tempat', $data['tempat'] ?? ($opd->nama_opd ?? '')) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Surat</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $data['tanggal'] ?? now()->toDateString()) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                    </div>
                </div>

                @include('partials.form-actions', [
                    'backRoute' => route('reports.pemeriksaan.list'),
                    'previewRoute' => route('reports.pemeriksaan.report'),
                    'saveRoute' => route('reports.pemeriksaan.save'),
                    'saveText' => 'Perbarui',
                ])
            </form>
        </div>
    </div>
@endsection
