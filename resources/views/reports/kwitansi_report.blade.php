@extends('layouts.admin')

@section('header', 'Kwitansi')
@section('subheader', 'Ringkasan pembayaran berdasarkan BAP Penerimaan')

@section('actions')
    <a href="{{ route('reports.kwitansi.export') }}" class="btn btn-primary">
        <i class="fas fa-file-excel"></i> Export Excel
    </a>
    @if (!empty($data['penerimaan_nomor']))
        <a href="{{ route('reports.kwitansi.print_all', ['penerimaan_nomor' => $data['penerimaan_nomor']]) }}" class="btn btn-neutral ml-2">
            <i class="fas fa-print"></i> Cetak Berkas Full
        </a>
    @endif
@endsection

@section('content')
    <div id="print-area" class="bg-white rounded-lg shadow p-6">
        <div class="text-center font-bold text-xl mb-4">KWITANSI</div>
        <table class="w-full border border-black text-sm">
            <tr>
                <td class="px-2 py-1 w-1/3">TAHUN ANGGARAN</td>
                <td class="px-2 py-1">{{ $data['tahun'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="px-2 py-1">KODE REKENING</td>
                <td class="px-2 py-1">{{ $data['rekening'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="px-2 py-1">NO. KWT</td>
                <td class="px-2 py-1">{{ $data['nomor_kwt'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="px-2 py-1 align-top">Sudah Terima Dari</td>
                <td class="px-2 py-1">Bendahara Pengeluaran {{ $data['opd_nama'] ?? ($opd->nama_opd ?? '') }}</td>
            </tr>
            <tr>
                <td class="px-2 py-1 align-top">Banyaknya Uang</td>
                <td class="px-2 py-1">{{ $data['terbilang'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="px-2 py-1 align-top">Untuk Pembayaran</td>
                <td class="px-2 py-1">{{ $data['pembayaran_uraian'] ?? '' }}</td>
            </tr>
        </table>
        <table class="w-full border border-black mt-2 text-sm">
            <tr>
                <td class="px-2 py-2 w-20">Rp</td>
                <td class="px-2 py-2 text-right font-bold">{{ number_format($data['jumlah'] ?? 0, 0, ',', '.') }}</td>
            </tr>
        </table>
        <div class="mt-6 text-sm">
            <div class="flex justify-between">
                <div></div>
                <div class="text-right">{{ $data['lokasi_tanggal'] ?? '' }}</div>
            </div>
            <div class="grid grid-cols-3 gap-4 mt-6 text-center">
                <div>
                    <div>Pejabat Pelaksana Teknis Kegiatan</div>
                    <div class="h-16"></div>
                    <div class="font-bold underline">{{ $data['pejabat']['pptk'] ?? '' }}</div>
                    <div class="text-xs">NIP. {{ $data['pptk_nip'] ?? '-' }}</div>
                </div>
                <div>
                    <div>Bendahara Pengeluaran,</div>
                    <div class="h-16"></div>
                    <div class="font-bold underline">{{ $data['pejabat']['bendahara'] ?? '' }}</div>
                    <div class="text-xs">NIP. {{ $data['bendahara_nip'] ?? '-' }}</div>
                </div>
                <div>
                    <div>Yang Menerima,</div>
                    <div>Pihak Ketiga</div>
                    <div class="h-16"></div>
                    <div class="font-bold underline">{{ $data['pejabat']['pihak_ketiga'] ?? '' }}</div>
                </div>
            </div>
            <div class="mt-10 text-center">
                <div>Mengetahui,</div>
                <div>Pengguna Anggaran</div>
                <div class="h-16"></div>
                <div class="font-bold underline">{{ $data['pejabat']['pengguna'] ?? '' }}</div>
                <div class="text-xs">NIP. {{ $data['ppk_nip'] ?? '-' }}</div>
            </div>
        </div>
    </div>
@endsection
