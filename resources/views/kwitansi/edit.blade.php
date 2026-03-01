@extends('layouts.admin')

@section('header', 'Edit Kwitansi')
@section('subheader', 'Perbarui kwitansi berdasarkan BAP Penerimaan Barang')

@section('content')
    <form action="{{ route('reports.kwitansi.update', $id) }}" method="POST" class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tahun Anggaran</label>
                <input type="number" name="tahun" value="{{ $data['tahun'] ?? now()->year }}" min="2000" max="2100" class="w-full px-4 py-2 rounded-lg border border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Kode Rekening</label>
                <input type="text" name="rekening" value="{{ $data['rekening'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nomor KWT</label>
                <input type="text" name="nomor_kwt" value="{{ $data['nomor_kwt'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $data['tanggal'] ?? now()->toDateString() }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-1">Nomor BAP Penerimaan</label>
                <select name="penerimaan_nomor" class="w-full px-4 py-2 rounded-lg border border-gray-300" required>
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
        <div class="flex items-center justify-end gap-2">
            <button type="submit" class="btn btn-success text-white">
                <i class="fas fa-save"></i> Perbarui
            </button>
            <button type="submit" formaction="{{ route('reports.kwitansi.report') }}" class="btn btn-warning">
                <i class="fas fa-file-alt"></i> Preview Laporan
            </button>
        </div>
    </form>
@endsection
