@extends('layouts.admin')

@section('title', 'Cetak Berita Acara Pinjam Pakai')
@section('header', 'Cetak Berita Acara Pinjam Pakai')
@section('subheader', 'Pratinjau & cetak')

@section('actions')
    <button onclick="window.print()" class="no-print btn btn-neutral"><i class="fas fa-print"></i> Cetak</button>
    <a href="{{ route('reports.pinjam.export') }}" class="no-print btn btn-primary ml-2"><i class="fas fa-file-excel"></i> Export Excel</a>
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
    <div id="print-area" class="preview-paper bg-white shadow border border-gray-100 text-black">
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
                    @page { size: auto; margin: 10mm; }
                }
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

        <div class="text-center mb-4">
            <h2 class="font-extrabold text-lg underline ">BERITA ACARA SERAH TERIMA BARANG INVENTARIS</h2>
            <p class="text-sm">NO: {{ $data['nomor'] }}</p>
        </div>

        <p class="mb-3 text-sm">
                {{ $data['pembuka'] ?? ('Pada hari ini ' . \Illuminate\Support\Carbon::parse($data['tanggal'])->translatedFormat('l d F Y') . ', bertempat di ' . ucwords(strtolower(($opd->nama_opd ?? null) ?: ($data['tempat'] ?? '-'))) . ' Kabupaten Bolaang Mongondow Selatan, yang bertanda tangan dibawah ini:') }}
        </p>

        <div class="mb-4">
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
            <p class="mt-2 text-sm">Selanjutnya disebut <span class="font-bold">PIHAK PERTAMA</span></p>
        </div>

        <div class="mb-4">
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
            <p class="mt-2 text-sm">Selanjutnya disebut <span class="font-bold">PIHAK KEDUA</span></p>
        </div>

        <p class="mb-4 text-sm">
            Bahwa kedua belah pihak sepakat melakukan penyerahan/terima barang inventaris sebagai berikut:
        </p>

        <div class="overflow-x-auto mb-4">
            <table class="w-full text-xs border border-black print:text-[10px]">
                <thead>
                    <tr class="text-center font-bold">
                        <th class="border border-black px-2 py-1">No</th>
                        <th class="border border-black px-2 py-1">Nama Barang</th>
                        <th class="border border-black px-2 py-1">Merk</th>
                        <th class="border border-black px-2 py-1">Type</th>
                        <th class="border border-black px-2 py-1">Nomor Polisi (Khusus Kendaraan)</th>
                        <th class="border border-black px-2 py-1">Tahun</th>
                        <th class="border border-black px-2 py-1">Kondisi</th>
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
                            <td class="border border-black px-2 py-1">{{ $item['identitas'] ?? '-' }}</td>
                            <td class="border border-black px-2 py-1 text-center">{{ $item['tahun'] ?? '-' }}</td>
                            <td class="border border-black px-2 py-1 text-center">{{ $item['kondisi'] ?? '-' }}</td>
                            <td class="border border-black px-2 py-1 text-center">{{ $item['jumlah'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p class="mb-3 text-sm">Dengan ketentuan sebagai berikut:</p>
        @php
            $rulesLines = preg_split("/\r\n|\n|\r/", $data['ketentuan'] ?? '');
            $rulesLines = array_values(array_filter($rulesLines, fn($l) => trim($l) !== ''));
        @endphp
        <style>
            .rules-table td:first-child { width: 18px; vertical-align: top; }
            .rules-table td:last-child { padding-left: 6px; }
        </style>
        <table class="rules-table text-sm mb-6">
            @foreach ($rulesLines as $i => $line)
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
                    <td class="font-bold">{{ $letter }}.</td>
                    <td class="text-justify">{{ $content }}</td>
                </tr>
            @endforeach
        </table>
<p class="mb-3 text-sm">Demikian Berita Acara Serah Terima Barang Inventaris ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        <div class="grid grid-cols-2 gap-6 mt-8">
            {{-- <div class="text-left text-sm mb-2 col-start-2">
                {{ $data['tempat'] }}, {{ \Illuminate\Support\Carbon::parse($data['tanggal'])->translatedFormat('d F Y') }}
            </div> --}}
            
            <div class="text-center">
                {{-- {{ $data['tempat'] }}, {{ \Illuminate\Support\Carbon::parse($data['tanggal'])->locale('id')->translatedFormat('d F Y')}} --}}
                <p class="mb-1">PIHAK PERTAMA</p>
                <div class="h-24"></div>
                <p class="font-bold underline">{{ $data['pihak_pertama']['nama'] }}</p>
                <p class="text-sm">NIP. {{ $data['pihak_pertama']['nip'] ?? '-' }}</p>
            </div>
            <div class="text-center">
                <p class="mb-1">PIHAK KEDUA</p>
                <div class="h-24"></div>
                <p class="font-bold underline">{{ $data['pihak_kedua']['nama'] }}</p>
                <p class="text-sm">NIP. {{ $data['pihak_kedua']['nip'] ?? '-' }}</p>
            </div>
        </div>
    </div>
@endsection
