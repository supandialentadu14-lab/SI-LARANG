@extends('layouts.admin')

@section('title', 'Cetak Berita Acara Pinjam Pakai')
@section('header', 'Cetak Berita Acara Pinjam Pakai')
@section('subheader', 'Pratinjau & cetak')

@section('actions')
    <button onclick="window.print()" class="no-print btn btn-neutral"><i class="fas fa-print"></i> Cetak</button>
    <form method="POST" action="{{ route('reports.pinjam.save') }}" class="no-print inline-block ml-2">
        @csrf
        <input type="hidden" name="id" value="{{ session('pinjam_pakai_current_id') ?? ($saved_id ?? '') }}">
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
    </form>
    @if(isset($saved_id))
        <a href="{{ route('reports.pinjam.edit', $saved_id) }}" class="no-print btn btn-outline ml-2">Edit</a>
    @else
        <a href="{{ route('reports.pinjam.form') }}" class="no-print btn btn-outline ml-2">Edit</a>
    @endif
@endsection

@section('content')
    <div id="print-area" class="preview-paper bg-white text-black">
        @if(isset($status))
            <div class="no-print mb-4 px-4 py-3 bg-green-50 text-green-700 border border-green-200 rounded">
                {{ $status }}
            </div>
        @endif
        @if(isset($error))
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
                @media print {
                    html, body { background: #ffffff !important; }
                    body * { visibility: hidden; }
                    #print-area, #print-area * { visibility: visible; }
                    #print-area { position: static !important; width: auto !important; overflow: visible !important; }
                    @page { size: 210mm 330mm; margin: 5mm 15mm; }
                    body { margin: 0; }
                    .kop { margin-top: 0 !important; }
                    .preview-paper { 
                        width: 100% !important; 
                        min-height: auto !important; 
                        padding: 0 !important; 
                        margin: 0 !important; 
                        box-sizing: border-box; 
                        background: #ffffff !important; 
                        box-shadow: none !important; 
                        line-height: 1.4;
                    }
                    .preview-paper p { margin: 5px 0; }
                    .preview-paper h2 { margin: 5px 0; }
                    .preview-paper table { margin-top: 6px; }
                    #print-area { background: #ffffff !important; box-shadow: none !important; }
                    .bg-gray-50, .bg-gray-100, .bg-gray-200 { background: #ffffff !important; }
                    thead, tbody, tfoot, tr, th, td { background: #ffffff !important; }
                    .shadow, .shadow-sm, .shadow-md, .shadow-lg, .shadow-xl, .ring-1, .ring-2, .ring { box-shadow: none !important; }
                    * { background: #ffffff !important; }
                    * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    .kop-logo { width: 100px; }
                    .kop-text .line2 { font-size: 22px; }
                }
                @media screen {
                    html, body { background: #ffffff !important; }
                    #print-area { box-shadow: none !important; background: #ffffff !important; }
                    .preview-paper { box-shadow: none !important; background: #ffffff !important; }
                }
                .preview-paper {
                    width: 210mm;
                    min-height: 330mm;
                    margin: 0 auto;
                    background: #fff;
                    padding: 5mm 15mm;
                    line-height: 1.4;
                }
                .preview-paper p { margin: 5px 0; }
                .preview-paper h2 { margin: 5px 0; }
                .preview-paper table { margin-top: 6px; }
            </style>
            @include('partials.kop', ['opd' => $opd])
        </div>

        <div class="text-center mb-1">
            <h2 class="font-extrabold text-lg underline ">BERITA ACARA SERAH TERIMA BARANG INVENTARIS</h2>
            <p class="text-sm">NO: {{ $data['nomor'] }}</p>
        </div>

        <p class="mb-1 text-sm">
                {{ $data['pembuka'] ?? ('Pada hari ini ' . \Illuminate\Support\Carbon::parse($data['tanggal'])->translatedFormat('l d F Y') . ', bertempat di ' . ucwords(strtolower(($opd->nama_opd ?? null) ?: ($data['tempat'] ?? '-'))) . ' Kabupaten Bolaang Mongondow Selatan, yang bertanda tangan dibawah ini:') }}
        </p>

        <div class="mb-2">
            <table class="w-full text-sm">
                <tr>
                    <td class="w-28 align-top">N a m a</td>
                    <td class="w-4 align-top">:</td>
                    <td class="align-top"><span class="font-bold">{{ $data['pihak_pertama']['nama'] }}</span></td>
                </tr>
                <tr>
                    <td class="align-top">N I P</td>
                    <td class="align-top">:</td>
                    <td class="align-top">{{ $data['pihak_pertama']['nip'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="align-top">Jabatan</td>
                    <td class="align-top">:</td>
                    <td class="align-top">{{ $data['pihak_pertama']['jabatan'] }}</td>
                </tr>
            </table>
            <p class="mt-1 text-sm">Selanjutnya disebut <span class="font-bold">PIHAK PERTAMA</span></p>
        </div>

        <div class="mb-2">
            <table class="w-full text-sm">
                <tr>
                    <td class="w-28 align-top">N a m a</td>
                    <td class="w-4 align-top">:</td>
                    <td class="align-top"><span class="font-bold">{{ $data['pihak_kedua']['nama'] }}</span></td>
                </tr>
                <tr>
                    <td class="align-top">N I P</td>
                    <td class="align-top">:</td>
                    <td class="align-top">{{ $data['pihak_kedua']['nip'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="align-top">Jabatan</td>
                    <td class="align-top">:</td>
                    <td class="align-top">{{ $data['pihak_kedua']['jabatan'] }}</td>
                </tr>
            </table>
            <p class="mt-1 text-sm">Selanjutnya disebut <span class="font-bold">PIHAK KEDUA</span></p>
        </div>

        <p class="mb-1 text-sm">
            Bahwa kedua belah pihak sepakat mengadakan perjanjian serah terima barang inventaris kantor/kendaraan milik Pemerintah Kabupaten Bolaang Mongondow Selatan : 
        </p>

        <div class="overflow-x-auto mb-2">
            <table class="w-full text-xs border border-black print:text-[10px]">
                <thead>
                    <tr class="text-center font-bold">
                        <th class="border border-black px-2 py-1">No</th>
                        <th class="border border-black px-2 py-1">Nama Barang</th>
                        <th class="border border-black px-2 py-1">Merk</th>
                        <th class="border border-black px-2 py-1">Type</th>
                        <th class="border border-black px-2 py-1">Nomor Polisi (Khusus Kendaraan)</th>
                        <th class="border border-black px-2 py-1">Tahun Pembelian</th>
                        <th class="border border-black px-2 py-1">Kondisi Barang</th>
                        <th class="border border-black px-2 py-1">Jumlah Barang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['items'] as $i => $item)
                        <tr>
                            <td class="border border-black px-2 py-1 text-center">{{ $i + 1 }}</td>
                            <td class="border border-black px-2 py-1">{{ $item['nama'] }}</td>
                            <td class="border border-black px-2 py-1">{{ $item['merk'] ?? '-' }}</td>
                            <td class="border border-black px-2 py-1">{{ $item['tipe'] ?? '-' }}</td>
                            <td class="border border-black px-2 py-1 text-center">{{ $item['identitas'] ?? '-' }}</td>
                            <td class="border border-black px-2 py-1 text-center">{{ $item['tahun'] ?? '-' }}</td>
                            <td class="border border-black px-2 py-1 text-center">{{ $item['kondisi'] ?? '-' }}</td>
                            <td class="border border-black px-2 py-1 text-center">{{ $item['jumlah'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p class="mb-1 text-sm">Dengan ketentuan sebagai berikut:</p>
        @php
            $rulesLines = preg_split("/\r\n|\n|\r/", $data['ketentuan'] ?? '');
            $rulesLines = array_values(array_filter($rulesLines, fn($l) => trim($l) !== ''));
            $defaultRules = [
                'PIHAK PERTAMA selaku Pengguna Barang adalah pejabat pemegang kewenangan penggunaan Barang Milik Daerah, meminjamkan Barang Milik Daerah tersebut di atas kepada PIHAK KEDUA untuk mendukung kegiatan dan kelancaran pelaksanaan tugas pada Dinas Komunikasi dan Informatika.',
                'PIHAK KEDUA bertanggung jawab dalam hal penggunaan, pemeliharaan dan pengamanan barang tersebut sejak tanggal serah terima ini.',
                'PIHAK KEDUA dilarang memindahtangankan barang tersebut kepada pihak lain tanpa seizin PIHAK PERTAMA;',
                'PIHAK KEDUA sanggup mengganti rugi apabila barang yang dipinjamkan hilang;',
                'PIHAK KEDUA wajib mengembalikan Barang Milik Daerah tersebut kepada PIHAK PERTAMA apabila telah pensiun/dimutasi/dipindahtugaskan ke Instansi lain, tanpa ada Tuntutan Ganti Rugi dan lain sebagainya yang berkaitan dengan Penyerahan Barang Milik Daerah.',
                'Berita Acara Serah Terima ini berlaku hingga 31 Desember 2026.',
            ];
            $list = count($rulesLines) ? $rulesLines : $defaultRules;
        @endphp
        <style>
            .rules-table td:first-child { width: 18px; vertical-align: top; }
            .rules-table td:last-child { padding-left: 6px; }
        </style>
        <table class="rules-table text-sm mb-3">
            @foreach ($list as $i => $line)
                @php
                    $m = [];
                    $letter = '';
                    $content = trim($line);
                    if (preg_match('/^\s*([a-zA-Z])\.\s*(.*)$/', $line, $m)) {
                        $letter = strtolower($m[1]);
                        $content = $m[2];
                    } else {
                        $letter = chr(97 + $i);
                    }
                @endphp
                <tr>
                    <td class=" text-black">{{ $letter }}.</td>
                    <td class="text-justify">{!! $content !!}</td>
                </tr>
            @endforeach
        </table>
        <p class="mb-1 text-sm">Demikian Berita Acara Serah Terima Barang Inventaris ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        <div class="text-right text-sm mb-1">
            {{ ucwords(strtolower($data['tempat'])) }}, {{ \Illuminate\Support\Carbon::parse($data['tanggal'])->translatedFormat('d F Y') }}
        </div>
        <div class="grid grid-cols-2 gap-6 mt-2">
            <div class="text-center">
                <p class="mb-1">PIHAK KEDUA</p>
                <div class="h-24"></div>
                <p class="font-bold underline">{{ $data['pihak_kedua']['nama'] }}</p>
                <p class="text-sm">NIP. {{ $data['pihak_kedua']['nip'] ?? '-' }}</p>
            </div>
            <div class="text-center">
                <p class="mb-1">PIHAK PERTAMA</p>
                <div class="h-24"></div>
                <p class="font-bold underline">{{ $data['pihak_pertama']['nama'] }}</p>
                <p class="text-sm">NIP. {{ $data['pihak_pertama']['nip'] ?? '-' }}</p>
            </div>
        </div>
    </div>
@endsection
