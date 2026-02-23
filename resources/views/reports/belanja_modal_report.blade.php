@extends('layouts.admin')

@section('header', 'Daftar Kontrak Belanja Modal')
@section('content')

    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            border: none;
        }

        .print-area {
            border: none !important;
            box-shadow: none !important;
        }

        .report-table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
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
            text-align: center;
            margin-bottom: 12px;
        }

        .kop h1 {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            margin: 0;
        }

        .kop h2 {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            margin: 4px 0 0;
        }

        .kop h3 {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            margin: 4px 0 0;
        }

        @media screen {
            #print-area {
                width: 330mm;
                min-height: 210mm;
                margin: 16px auto;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
                background: #ffffff;
            }

            .report-table {
                width: 100%;
            }
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .print-area,
            .print-area * {
                visibility: visible;
            }

            .print-area {
                position: static !important;
                width: auto !important;
                overflow: visible !important;
                border: none !important;
            }

            .print\:hidden {
                display: none !important;
            }

            @page {
                size: auto;
                margin: 12mm;
            }

            body {
                margin: 0;
            }
        }
    </style>

    <div class="bg-white rounded-lg shadow p-6 mb-6 print:hidden">
        <button type="button" onclick="window.print()"
            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm bg-black text-white hover:bg-black">
            <i class="fas fa-print"></i>
            Print
        </button>
        <a href="{{ route('reports.belanja.modal.export') }}"
            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm bg-indigo-600 text-white hover:bg-indigo-700">
            <i class="fas fa-file-excel"></i>
            Export Excel
        </a>
    </div>

    <div id="print-area" class="print-area bg-white shadow-lg border p-8 rounded-lg">
        <div class="kop">
            <h1>DAFTAR KONTRAK BELANJA MODAL</h1>
            <h2>{{ $master['opd']['nama'] ?? null ?: $opd->nama_opd ?? '' }} KABUPATEN BOLAANG MONGONDOW SELATAN</h2>
            <h3>TAHUN {{ $data['tahun'] }}</h3>
        </div>

        <table class="report-table">
            <colgroup>
                <col style="width:3%">
                <col style="width:14%">
                <col style="width:16%">
                <col style="width:10%">
                <col style="width:8%">
                <col style="width:10%">
                <col style="width:8%">
                <col style="width:8%">
                <col style="width:8%">
                <col style="width:8%">
                <col style="width:8%">
                <col style="width:10%">
                <col style="width:8%">
            </colgroup>
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Nama Kegiatan</th>
                    <th rowspan="2">Pekerjaan</th>
                    <th rowspan="2">Nilai Kontrak (Rp)</th>
                    <th rowspan="2">Tanggal Mulai</th>
                    <th rowspan="2">Tanggal Akhir Pekerjaan</th>
                    <th colspan="5">SP2D Pembayaran</th>
                    <th rowspan="2">Total Pembayaran (Rp)</th>
                    <th rowspan="2">Status Pekerjaan</th>
                </tr>
                <tr>
                    <th>Uang Muka (Rp)</th>
                    <th>Termin I (Rp)</th>
                    <th>Termin II (Rp)</th>
                    <th>Termin III (Rp)</th>
                    <th>Termin IV (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($data['items'] as $row)
                    <tr>
                        <td align="center">{{ $no++ }}</td>
                        <td>{{ $row['nm'] }}</td>
                        <td>{{ $row['pk'] }}</td>
                        <td align="right">{{ number_format($row['nk'], 0, ',', '.') }}</td>
                        <td align="center">
                            {{ $row['tm'] ? \Carbon\Carbon::parse($row['tm'])->translatedFormat('d F Y') : '-' }}</td>
                        <td align="center">
                            {{ $row['ta'] ? \Carbon\Carbon::parse($row['ta'])->translatedFormat('d F Y') : '-' }}</td>
                        <td align="right">{{ $row['um'] ? ' ' . number_format($row['um'], 0, ',', '.') : '-' }}</td>
                        <td align="right">{{ $row['t1'] ? ' ' . number_format($row['t1'], 0, ',', '.') : '-' }}</td>
                        <td align="right">{{ $row['t2'] ? ' ' . number_format($row['t2'], 0, ',', '.') : '-' }}</td>
                        <td align="right">{{ $row['t3'] ? ' ' . number_format($row['t3'], 0, ',', '.') : '-' }}</td>
                        <td align="right">{{ $row['t4'] ? ' ' . number_format($row['t4'], 0, ',', '.') : '-' }}</td>
                        <td align="right">{{ number_format($row['ttl'], 0, ',', '.') }}</td>
                        <td align="center">{{ $row['st'] ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
