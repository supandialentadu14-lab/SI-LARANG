@extends('layouts.admin')

@section('header', 'Nota Pesanan')
@section('content')
    <style>
        .preview-paper { 
            width: 210mm; 
            min-height: 330mm; 
            margin: 0 auto; 
            background: #ffffff; 
            padding: 5mm 15mm;
            line-height: 1.4;
        }
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
                size: 210mm 330mm;
                margin: 5mm 15mm;
            }
            body { margin: 0; }
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
        }

        @media screen {
            html, body { background: #f3f4f6; }
            #print-area { width: 210mm; margin: 0 auto; }
            .preview-paper { width: 210mm; min-height: 330mm; margin: 16px auto; background: #ffffff; box-shadow: 0 10px 25px rgba(0,0,0,.08); padding: 5mm 15mm; }
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

    <div class="bg-white rounded-lg shadow p-6 mb-6 print:hidden flex gap-2">
        <button type="button" onclick="window.print()" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-bold shadow">
            <i class="fas fa-print mr-2"></i> Print
        </button>
        <form action="{{ route('reports.nota.save') }}" method="POST" class="inline">
            @csrf
            {{-- Hidden inputs from $data --}}
            <input type="hidden" name="nomor" value="{{ $data['nomor'] }}">
            <input type="hidden" name="tanggal" value="{{ $data['tanggal'] }}">
            <input type="hidden" name="tahun" value="{{ $data['tahun'] }}">
            <input type="hidden" name="kegiatan" value="{{ $data['kegiatan'] }}">
            <input type="hidden" name="sub_kegiatan" value="{{ $data['sub_kegiatan'] }}">
            <input type="hidden" name="rekening" value="{{ $data['rekening'] }}">
            <input type="hidden" name="belanja" value="{{ $data['belanja'] }}">
            
            {{-- Pihak-pihak --}}
            <input type="hidden" name="pejabat_nama" value="{{ $data['pejabat']['nama'] ?? '' }}">
            <input type="hidden" name="pejabat_nip" value="{{ $data['pejabat']['nip'] ?? '' }}">
            <input type="hidden" name="pptk_nama" value="{{ $data['pptk']['nama'] ?? '' }}">
            <input type="hidden" name="pptk_nip" value="{{ $data['pptk']['nip'] ?? '' }}">
            <input type="hidden" name="pb_nama" value="{{ $data['pengurus_barang']['nama'] ?? '' }}">
            <input type="hidden" name="pb_nip" value="{{ $data['pengurus_barang']['nip'] ?? '' }}">
            <input type="hidden" name="pbp_nama" value="{{ $data['pengurus_pengguna']['nama'] ?? '' }}">
            <input type="hidden" name="pbp_nip" value="{{ $data['pengurus_pengguna']['nip'] ?? '' }}">
            <input type="hidden" name="ppk_nama" value="{{ $data['ppk']['nama'] ?? '' }}">
            <input type="hidden" name="ppk_nip" value="{{ $data['ppk']['nip'] ?? '' }}">
            <input type="hidden" name="bendahara_nama" value="{{ $data['bendahara']['nama'] ?? '' }}">
            <input type="hidden" name="bendahara_nip" value="{{ $data['bendahara']['nip'] ?? '' }}">

            {{-- Penyedia --}}
            {{-- Note: Controller prioritizes supplier_id, then session. We pass values directly to be safe or rely on session if controller supports it. 
                 But controller 'save' reads from session('nota_current') only for penyedia fallback. 
                 Since we are passing explicit data, we might need to pass penyedia details if they are editable? 
                 Actually 'save' doesn't seem to read penyedia_toko etc from request directly unless we modify it.
                 Wait, 'save' uses: 
                 $penyedia = ['toko' => '', ...];
                 if ($sid = $request->input('supplier_id')) { ... } else { $existing = session('nota_current') ... }
                 So if we don't pass supplier_id, it will look in session.
                 Since 'report' method saved to session 'nota_current', 'save' should pick it up!
            --}}
            
            {{-- Items --}}
            @foreach($data['items'] as $idx => $item)
                <input type="hidden" name="items[{{ $idx }}][name]" value="{{ $item['name'] }}">
                <input type="hidden" name="items[{{ $idx }}][qty]" value="{{ $item['qty'] }}">
                <input type="hidden" name="items[{{ $idx }}][unit]" value="{{ $item['unit'] }}">
                <input type="hidden" name="items[{{ $idx }}][price]" value="{{ $item['price'] }}">
            @endforeach

            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold shadow flex items-center gap-2">
                <i class="fas fa-save"></i> Simpan
            </button>
        </form>
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
