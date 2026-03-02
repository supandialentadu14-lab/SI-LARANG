@extends('layouts.admin')

@section('header', 'Buat Kwitansi')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-orange-50">
                <h6 class="font-bold text-blue-700">Form Kwitansi</h6>
            </div>
            
            <form action="{{ route('reports.kwitansi.save') }}" method="POST" class="p-6 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tahun Anggaran</label>
                        <input type="number" name="tahun" value="{{ $data['tahun'] ?? now()->year }}" min="2000" max="2100" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kode Rekening</label>
                        <input type="text" name="rekening" value="{{ $data['rekening'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nomor KWT <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_kwt" value="{{ $data['nomor_kwt'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" value="{{ $data['tanggal'] ?? now()->toDateString() }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nomor BAP Penerimaan <span class="text-red-500">*</span></label>
                        <select name="penerimaan_nomor" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                            <option value="">-- pilih nomor --</option>
                            @foreach ($docs as $n)
                                <option value="{{ $n['nomor'] }}" {{ ($data['penerimaan_nomor'] ?? '') === ($n['nomor'] ?? '') ? 'selected' : '' }}>
                                    {{ $n['nomor'] }} • {{ \Carbon\Carbon::parse($n['tanggal'] ?? now())->translatedFormat('d F Y') }} • Rp {{ number_format($n['total'] ?? 0, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Jumlah uang dan uraian belanja akan diambil dari dokumen BAP Penerimaan.</p>
                    </div>
                </div>
                
                @include('partials.form-actions', [
                    'backRoute' => route('reports.kwitansi.list'),
                    'previewRoute' => route('reports.kwitansi.report'),
                ])
            </form>
        </div>
    </div>
@endsection