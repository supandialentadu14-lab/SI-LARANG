@extends('layouts.admin')

@section('header', 'Nota Pesanan')
@section('content')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: static !important;
                width: auto !important;
                overflow: visible !important;
            }

            @page {
                size: auto;
                margin: 12mm;
            }
        }

        @media screen {
            html, body { background: #f3f4f6; }
            #print-area { width: 210mm; margin: 0 auto; }
            .preview-paper { width: 210mm; min-height: 330mm; margin: 16px auto; background: #ffffff; box-shadow: 0 10px 25px rgba(0,0,0,.08); padding: 10mm; }
        }

        .kop {
            width: 100%;
        }

        .kop-logo {
            width: 80px;
            text-align: center;
            vertical-align: top;
        }

        .kop-logo img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .kop-text {
            text-align: center;
        }

        .kop-text .line1 {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.3px;
        }

        .kop-text .line2 {
            font-size: 15px;
            font-weight: 700;
            margin-top: 0.1px;
        }

        .kop-text .line3 {
            font-size: 12px;
            margin-top: 0.1px;
        }

        .kop-text .line4 {
            font-size: 12px;
        }

        .report-table {
            border-collapse: collapse;
            width: 100%;
        }

        .report-table th,
        .report-table td {
            border: 1px solid black;
            padding: 6px;
            font-size: 12px;
        }

        .report-table th {
            text-align: center;
            font-weight: bold;
        }

        .kop {
            margin-bottom: 10px;
        }

        .kop h1 {
            text-align: center;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 16px;
            margin: 6px 0;
        }

        .bold {
            font-weight: 700;
        }

        .rules {
            padding-left: 20px;
            margin-left: 6px;
        }

        .rules li {
            margin: 2px 0;
            line-height: 1.4;
        }

        .italic {
            font-style: italic;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            padding: 0;
            vertical-align: top;
            font-size: 12px;
            line-height: 1.2;
        }

        .header-table .label {
            width: 40px;
            font-weight: 700;
        }

        .header-table .colon {
            width: 8px;
        }

        .header-table .spacer {
            width: 100px;
        }

        .header-table .content {
            width: 300px;
        }

        .header-table .city {
            width: 80px;
            text-align: left;
        }

        .header-table .date {
            width: 80px;
            text-align: left;
        }

        /*   */
    </style>

    <div class="bg-white rounded-lg shadow p-6 mb-6 print:hidden">
        <button type="button" onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-lg font-bold shadow">
            Print
        </button>
    </div>

    <div id="print-area" class="preview-paper">
        @php
            function toWordsId($value)
            {
                $huruf = [
                    '',
                    'satu',
                    'dua',
                    'tiga',
                    'empat',
                    'lima',
                    'enam',
                    'tujuh',
                    'delapan',
                    'sembilan',
                    'sepuluh',
                    'sebelas',
                ];
                $value = intval($value);
                if ($value < 12) {
                    return $huruf[$value];
                }
                if ($value < 20) {
                    return toWordsId($value - 10) . ' belas';
                }
                if ($value < 100) {
                    return toWordsId(intval($value / 10)) . ' puluh ' . toWordsId($value % 10);
                }
                if ($value < 200) {
                    return 'seratus ' . toWordsId($value - 100);
                }
                if ($value < 1000) {
                    return toWordsId(intval($value / 100)) . ' ratus ' . toWordsId($value % 100);
                }
                if ($value < 2000) {
                    return 'seribu ' . toWordsId($value - 1000);
                }
                if ($value < 1000000) {
                    return toWordsId(intval($value / 1000)) . ' ribu ' . toWordsId($value % 1000);
                }
                if ($value < 1000000000) {
                    return toWordsId(intval($value / 1000000)) . ' juta ' . toWordsId($value % 1000000);
                }
                return (string) $value;
            }
        @endphp

        {{-- KOP Surat reusable --}}
        @include('partials.kop', ['opd' => $opd])
        <table class="header-table mb-4">
            <td class="date" style="text-align: right">Bolaang Uki, {{ \Carbon\Carbon::parse($data['tanggal'])->translatedFormat('d F Y') }}</td>
        </table>
        <table class="header-table mb-2">
            <tr>
                <td class="label">Nomor</td>
                <td class="colon">:</td>
                <td class="bold content">{{ $data['nomor'] }}</td>
                <td class="spacer"></td>
                <td class="city">Kepada Yth.</td>
                {{-- <td class="date">{{ \Carbon\Carbon::parse($data['tanggal'])->translatedFormat('F Y') }}</td> --}}
            </tr>
            <tr>
                <td class="label">Lampiran</td>
                <td class="colon">:</td>
                <td class="content">-</td>
                <td class="spacer"></td>
                <td class="city"><span class="bold">{{ $data['penyedia']['toko'] ?? '' }}</span><br></td>
                <td class="date"></td>
            </tr>
            <tr>
                <td class="label">Perihal</td>
                <td class="colon">:</td>
                <td class="content bold">
                    Belanja {{ $data['belanja'] }}
                    Pada Keg. {{ $data['kegiatan'] }}
                    Sub Keg. {{ $data['sub_kegiatan'] }} Tahun 
                        {{ $data['tahun'] }}
                </td>
                <td class="spacer"></td>
                <td class="city">

                    di-<br>
                    <span class="indent">Tempat</span>
                </td>
                <td class="date"></td>
            </tr>
        </table>
        <br>
        <div class="kop">
            <h1 class="text-center font-extrabold mb-4">NOTA PESANAN BARANG / BAHAN</h1>
        </div>

        <p class="text-sm mb-2">Dengan hormat,</p>
        <p class="text-sm mb-2 justify-between">
            Untuk keperluan pengadaan {{ $data['belanja'] }} dalam Kegiatan {{ $data['kegiatan'] }},
            Sub Kegiatan {{ $data['sub_kegiatan'] }} pada Tahun {{ $data['tahun'] }},
            harap dapat diberikan barang/bahan di bawah ini:
        </p>

        <table class="report-table mb-2">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Bahan/Alat (Barang)</th>
                    <th>Kuantitas</th>
                    <th>Satuan</th>
                    <th>Harga Satuan (Rp)</th>
                    <th>Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $grand = 0;
                @endphp
                @foreach ($data['items'] as $row)
                    @php $grand += $row['total']; @endphp
                    <tr>
                        <td align="center">{{ $no++ }}</td>
                        <td>{{ $row['name'] }}</td>
                        <td align="center">{{ $row['qty'] }}</td>
                        <td align="center">{{ $row['unit'] }}</td>
                        <td align="right">{{ number_format($row['price'], 0, ',', '.') }}</td>
                        <td align="right">{{ number_format($row['total'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="5" align="right" class="bold">Jumlah</td>
                    <td align="right" class="bold">{{ number_format($grand, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="6" align="center" class="bold">{{ ucwords(toWordsId((int) $grand)) }} Rupiah</td>
                </tr>
            </tbody>
        </table>

        <p class="text-sm mb-2"><span class="bold">Dengan Ketentuan :</span></p>
        <ol class="text-sm mb-4 rules">
            <li>1. Pembayaran melalui bendahara pengeluaran
                {{ \Illuminate\Support\Str::title($opd->nama_opd ?? 'Dinas Komunikasi dan Informatika') }}.</li>
            <li>2. Pembayaran dilaksanakan apabila barang-bahan tersebut telah diperiksa oleh Panitia Pemeriksa Barang
                sesuai dengan kualitas dan kuantitas barang yang diperiksa.</li>
        </ol>

        <div class="grid grid-cols-2 gap-6 mt-6">
            <div class="text-center text-sm">
                <p class="mb-1">&nbsp;</p>
                <p class="mb-1">Setuju Untuk Melaksanakan Pekerjaan</p>
                <div class="h-20"></div>
                <p class="font-bold underline">{{ $data['penyedia']['pemilik'] ?? '' }}</p>
            </div>
            <div class="text-center text-sm">
                <p class="mb-1">Bolaang Uki, {{ \Carbon\Carbon::parse($data['tanggal'])->translatedFormat('d F Y') }}</p>

                <p class="mb-1">Pejabat Pengadaan</p>
                <div class="h-20"></div>
                <p class="font-bold underline">{{ $data['pejabat']['nama'] ?? '' }}</p>
                <p class="text-sm">NIP. {{ $data['pejabat']['nip'] ?? '-' }}</p>
            </div>

        </div>

        <div class="grid grid-cols-1 mt-8 text-sm">
            <div class="text-center">
                <p class="mb-1">MENGETAHUI,</p>
                <p class="mb-1">PENGGUNA ANGGARAN SELAKU PPK</p>
                <div class="h-20"></div>
                <p class="font-bold underline">{{ $data['ppk']['nama'] ?? '' }}</p>
                <p class="text-sm">NIP. {{ $data['ppk']['nip'] ?? '-' }}</p>
            </div>
        </div>
    </div>
@endsection
