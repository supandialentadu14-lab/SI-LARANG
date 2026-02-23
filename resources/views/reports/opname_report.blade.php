@extends('layouts.admin')

@section('title', 'Cetak Berita Acara Stock Opname')
@section('header', 'Berita Acara Stock Opname Persediaan Barang Habis Pakai')
@section('subheader', 'Pratinjau & cetak')

@section('actions')
    <button onclick="window.print()" class="no-print btn btn-neutral"><i class="fas fa-print"></i> Cetak</button>
    <a href="{{ route('reports.opname.export') }}" class="no-print btn btn-primary ml-2"><i class="fas fa-file-excel"></i> Export Excel</a>
    <form method="POST" action="{{ route('reports.opname.save') }}" class="no-print inline-block ml-2">
        @csrf
        <input type="hidden" name="id" value="{{ session('opname_current_id') ?? ($saved_id ?? '') }}">
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
    </form>
    @if (isset($saved_id))
        <a href="{{ route('reports.opname.edit', $saved_id) }}" class="no-print btn btn-outline ml-2">Edit</a>
    @else
        <a href="{{ route('reports.opname.form') }}" class="no-print btn btn-outline ml-2">Edit</a>
    @endif
@endsection

@section('content')
    <div id="print-area" class="preview-paper bg-white shadow border border-gray-100 text-black">
        @if (isset($status))
            <div class="no-print mb-4 px-4 py-3 bg-green-50 text-green-700 border border-green-200 rounded">
                {{ $status }}
            </div>
        @endif
        @if (isset($error))
            <div class="no-print mb-4 px-4 py-3 bg-red-50 text-red-700 border border-red-200 rounded">
                {{ $error }}
            </div>
        @endif
        <div class="mb-4">
            <style>
                .kop { width: 100%; table-layout: fixed; }
                .kop td { vertical-align: middle; }
                .kop-logo { width: 75px; }
                .kop-logo img { width: 75px; height: auto; }
                .kop-text { text-align: center; }
                .kop-text .line1 { font-weight: 800; font-size: 16px; letter-spacing: .4px; }
                .kop-text .line2 { font-weight: 800; font-size: 22px; }
                .kop-text .line3, .kop-text .line4 { font-style: italic; font-size: 13px; line-height: 1.25; }
                @media print { @page { size: auto; margin: 10mm; } }
                @media print {
                    .kop-logo { width: 100px; }
                    .kop-text .line2 { font-size: 22px; }
                }
                .preview-paper {
                    width: 210mm;
                    min-height: 330mm;
                    margin: 0 auto;
                    background: #fff;
                    padding: 10mm;
                }
            </style>
            @include('partials.kop', ['opd' => $opd])
        </div>
        
            @if (isset($error))
                <div class="no-print mb-4 px-4 py-3 bg-red-50 text-red-700 border border-red-200 rounded">
                    {{ $error }}
                </div>
            @endif
            <div class="text-center mb-4">
                <h2 class="font-extrabold text-lg">BERITA ACARA</h2>
                <h2 class="font-bold text-lg underline uppercase">HASIL STOCK OPNAME PERSEDIAAN BARANG HABIS PAKAI</h2>
                <p class="text-sm">NO: {{ $data['nomor'] ?? '' }}</p>
            </div>

            <p class="mb-3 text-sm">
                {{ $data['pembuka'] ?? 'Pada hari ini ' . \Illuminate\Support\Carbon::parse($data['tanggal'])->translatedFormat('l d F Y') . ', bertempat di ' . ucwords(strtolower($opd->nama_opd ?? $data['tempat'] ?? '-')) . ' Kabupaten Bolaang Mongondow Selatan, yang bertanda tangan dibawah ini:' }}
            </p>

            <div class="mb-4">
                <div>
                    <table class="w-full text-sm">
                        <tr>
                            <td class="w-28 align-top">Nama</td>
                            <td class="w-4 align-top">:</td>
                            <td class="align-top"><span class="font-bold">{{ $data['pihak_kedua']['nama'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="align-top">NIP</td>
                            <td class="align-top">:</td>
                            <td class="align-top">{{ $data['pihak_kedua']['nip'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Jabatan</td>
                            <td class="align-top">:</td>
                            <td class="align-top">{{ $data['pihak_kedua']['jabatan'] }}</td>
                        </tr>
                    </table>
                    <p class="mt-2 text-sm">Sebagai pengurus barang pengguna berdasarkan Surat Keputusan Bupati Bolaang
                        Mongondow Selatan Nomor: 27 Tahun 2025 Tanggal 6 Januari 2025 telah melaksanakan Stock Opname
                        Persediaan Barang Habis Pakai per 
                        {{ \Illuminate\Support\Carbon::parse($data['tanggal'])->locale('id')->translatedFormat('d F Y') }},
                        dengan hasil sebagai berikut</p>
                </div>
            </div>

            <div class="overflow-x-auto mb-6">
                <table class="w-full text-xs border border-black print:text-[10px]">
                    <thead>
                        <tr class="text-center font-bold">
                            <th class="border border-black px-2 py-1" rowspan="2">No</th>
                            <th class="border border-black px-2 py-1" rowspan="2">Nama Jenis Persediaan Barang</th>
                            <th class="border border-black px-2 py-1" rowspan="2">Kwantitas</th>
                            <th class="border border-black px-2 py-1" rowspan="2">Satuan</th>
                            <th class="border border-black px-2 py-1" rowspan="2">Harga Satuan (Rp)</th>
                            <th class="border border-black px-2 py-1" rowspan="2">Jumlah Harga (Rp)</th>
                            <th class="border border-black px-2 py-1" colspan="3">Kondisi Barang</th>
                        </tr>
                        <tr class="text-center font-bold">
                            <th class="border border-black px-2 py-1">B</th>
                            <th class="border border-black px-2 py-1">RR</th>
                            <th class="border border-black px-2 py-1">RB</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach ($data['items'] as $i => $item)
                            @php $total += (int)($item['jumlah'] ?? 0); @endphp
                            <tr>
                                <td class="border border-black px-2 py-1 text-center">{{ $i + 1 }}</td>
                                <td class="border border-black px-2 py-1">{{ $item['nama'] }}</td>
                                <td class="border border-black px-2 py-1 text-center">{{ $item['kuantitas'] }}</td>
                                <td class="border border-black px-2 py-1 text-center">{{ $item['satuan'] ?? '-' }}</td>
                                <td class="border border-black px-2 py-1 text-right">
                                    {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}</td>
                                <td class="border border-black px-2 py-1 text-right">
                                    {{ number_format($item['jumlah'] ?? 0, 0, ',', '.') }}</td>
                                <td class="border border-black px-2 py-1 text-center">
                                    {{ isset($item['kondisi']) && $item['kondisi'] === 'B' ? 'V' : '' }}</td>
                                <td class="border border-black px-2 py-1 text-center">
                                    {{ isset($item['kondisi']) && $item['kondisi'] === 'RR' ? 'V' : '' }}</td>
                                <td class="border border-black px-2 py-1 text-center">
                                    {{ isset($item['kondisi']) && $item['kondisi'] === 'RB' ? 'V' : '' }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" class="border border-black px-2 py-1 text-right font-bold">Jumlah</td>
                            <td class="border border-black px-2 py-1 text-right font-bold">
                                {{ number_format($total, 0, ',', '.') }}</td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                            <td class="border border-black px-2 py-1"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mb-4 text-sm">Demikian Berita Acara Stock Opname Persediaan Barang Habis Pakai ini dibuat untuk
                diperlukan sebagaimana mestinya.</p>
            
            <div class="grid grid-cols-2 gap-6 mt-6">
                <div class="text-center">
                    <p class="mb-1">Mengetahui</p>
                    <p class="mb-1">Kepala Dinas Komunikasi dan Informatika</p>
                    <div class="h-24"></div>
                    <p class="font-bold underline">{{ $opd->kepala_nama ?? ($data['pihak_pertama']['nama'] ?? '') }}</p>
                    <p class="text-sm">NIP. {{ $opd->kepala_nip ?? ($data['pihak_pertama']['nip'] ?? '-') }}</p>
                </div>
                <div class="text-center">
                    <p class="mb-1">&nbsp;</p>
                    <p class="mb-1">Pengurus Barang Pengguna</p>
                    <div class="h-24"></div>
                    <p class="font-bold underline">{{ $opd->pengurus_nama ?? ($data['pihak_kedua']['nama'] ?? '') }}</p>
                    <p class="text-sm">NIP. {{ $opd->pengurus_nip ?? ($data['pihak_kedua']['nip'] ?? '-') }}</p>
                </div>
            </div>
        
    @endsection
