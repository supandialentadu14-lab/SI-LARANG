@extends('layouts.admin')

@section('header', 'Kartu Persediaan Barang Tahunan')

@section('content')

    <style>
        /* ============================= */
        /* TABEL UTAMA                  */
        /* ============================= */
        .report-table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
            /* Agar kolom stabil saat print */
        }

        .report-table th,
        .report-table td {
            border: 1px solid black;
            padding: 4px;
            font-size: 12px;
            /* Diperkecil agar muat 1 halaman */
            word-wrap: break-word;
        }

        .report-table th {
            text-align: center;
            font-weight: bold;
        }

        /* Agar header tabel muncul lagi jika pindah halaman */
        .report-table thead {
            display: table-header-group;
        }

        .report-table tfoot {
            display: table-footer-group;
        }

        .report-table tr {
            page-break-inside: avoid;
            /* Jangan pecah baris di tengah halaman */
        }

        /* ============================= */
        /* INFO HEADER                  */
        /* ============================= */
        .info-table {
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 13px;
        }

        .info-table td {
            border: none;
            padding: 2px 4px;
        }

        /* ============================= */
        /* TANDA TANGAN                 */
        /* ============================= */
        .ttd-table {
            width: 100%;
            margin-top: 50px;
            page-break-inside: avoid;
            /* Jangan pindah halaman */
        }

        .ttd-table td {
            border: none !important;
            font-size: 14px;
            padding: 4px;
        }

        .ttd-table strong {
            font-size: 14px;
        }

        /* ============================= */
        /* PRINT SETTING                */
        /* ============================= */
        html, body {
            margin: 0;
            padding: 0;
            border: none;
        }
        .print-area {
            border: none !important;
            box-shadow: none !important;
        }
        @media screen {
            #print-area {
                width: 330mm;
                min-height: 210mm;
                margin: 16px auto;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
                background: #ffffff;
            }
            .report-table { width: 100%; }
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
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none !important;
            }

            .print\:hidden {
                display: none !important;
            }

            @page {
                size: 330mm 210mm;
                margin: 12mm;
            }
            body { margin: 0; }
        }
    </style>


    <div class="print:hidden rounded-xl shadow-md border border-indigo-200 bg-gradient-to-r from-indigo-50 via-white to-orange-50 p-4 mb-6">
        <form method="GET" action="{{ route('reports.kartu.tahunan') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-[10px] font-bold text-indigo-700 uppercase mb-1">From Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-indigo-200 text-sm bg-white shadow-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-indigo-700 uppercase mb-1">To Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-indigo-200 text-sm bg-white shadow-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-500">
            </div>
            <div class="md:col-span-2"></div>
            <div class="flex gap-1 justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-orange-500 text-white hover:bg-orange-600">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-black text-white hover:bg-black">
                    <i class="fas fa-print"></i>
                    Print
                </button>
                <a href="{{ route('reports.kartu.tahunan.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                    <i class="fas fa-file-excel"></i>
                    Export Excel
                </a>
            </div>
        </form>
    </div>

    <div id="print-area" class="print-area bg-white shadow-lg border p-8 rounded-lg">

        @php
            $grouped = $grouped ?? [];
            $lastIndex = count($grouped);
            $current = 1;
        @endphp

        <div class="text-center mb-4">
            <h2 class="text-xl font-bold uppercase">
                KARTU PERSEDIAAN BARANG
            </h2>
            <div class="text-xl font-bold uppercase">
                DI LINGKUNGAN PEMERINTAH KABUPATEN BOLAANG MONGONDOW SELATAN
            </div>
            <h5 class="text-sm font-semibold mt-1">
                    Per {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                </h5>
        </div>

        {{-- HEADER SKPD --}}
        <table class="info-table">
            <tr>
                <td width="140"><strong>SKPD</strong></td>
                <td width="10">:</td>
                <td>{{ ($master['opd']['nama'] ?? null) ?: ($opd->nama_opd ?? '-') }}</td>
            </tr>
            <tr>
                <td><strong>Kabupaten</strong></td>
                <td>:</td>
                <td>Bolaang Mongondow Selatan</td>
            </tr>
        </table>

        @foreach ($grouped as $data)
            @php
                $product = $data['product'];
                $rows = $data['rows'];
                $saldo = 0;
                $harga = 0;
            @endphp
            <table class="info-table">
                <tr>
                    <td width="140"><strong>Nama barang</strong></td>
                    <td width="10">:</td>
                    <td>{{ $product->name }}</td>
                </tr>
                <tr>
                    <td><strong>Satuan</strong></td>
                    <td>:</td>
                    <td>{{ $product->unit }}</td>
                </tr>
            </table>
            <div class="mb-8">

                <hr class="mb-2">

                {{-- HEADER PRODUK --}}
                <table class="report-table">
                    <colgroup>
                        <col style="width:2%"> {{-- No --}}
                        <col style="width:9%"> {{-- Tanggal --}}
                        <col style="width:18%"> {{-- Nomor Surat --}}
                        <col style="width:16%"> {{-- Uraian --}}
                        <col style="width:4%"> {{-- Masuk --}}
                        <col style="width:4%"> {{-- Keluar --}}
                        <col style="width:4%"> {{-- Sisa --}}
                        <col style="width:7%"> {{-- Harga --}}
                        <col style="width:7%"> {{-- Jml Masuk --}}
                        <col style="width:7%"> {{-- Jml Keluar --}}
                        <col style="width:7%"> {{-- Jml Sisa --}}
                        <col style="width:10%"> {{-- Keterangan --}}
                    </colgroup <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Tanggal</th>
                        <th rowspan="2">Nomor Surat Dasar Penerimaan/Pengeluaran</th>
                        <th rowspan="2">Uraian</th>
                        <th colspan="3">Barang-Barang</th>
                        <th rowspan="2">Harga Satuan (Rp)</th>
                        <th colspan="3">Jumlah Harga (Rp)</th>
                        <th rowspan="2">Keterangan</th>
                    </tr>
                    <tr>
                        <th>Masuk</th>
                        <th>Keluar</th>
                        <th>Sisa</th>
                        <th>Masuk</th>
                        <th>Keluar</th>
                        <th>Sisa</th>
                    </tr>
                    </thead>
                    <tbody>

                        @php $no = 1; @endphp

                        @foreach ($rows as $date => $row)
                            @php
                                $saldo += $row['masuk'] - $row['keluar'];
                                $harga = $row['harga'] ?? 0;
                            @endphp

                            <tr align="center">
                                <td>{{ $no++ }}</td>
                                <td align="center">
                                    {{ $row['date'] ? \Carbon\Carbon::parse($row['date'])->format('d F Y') : '-' }}
                                </td>
                                <td>{{ $row['nosur'] }}</td>
                                <td align="center">{{ $product->name }}</td>
                                <td>{{ $row['masuk'] ?: '' }}</td>
                                <td>{{ $row['keluar'] ?: '' }}</td>
                                <td><strong>{{ $saldo }}</strong></td>
                                <td align="right">{{ number_format($harga, 0, ',', '.') }}</td>
                                <td align="right">
                                    {{ $row['masuk'] ? number_format($row['masuk'] * $harga, 0, ',', '.') : '' }}</td>
                                <td align="right">
                                    {{ $row['keluar'] ? number_format($row['keluar'] * $harga, 0, ',', '.') : '' }}</td>
                                <td align="right"><strong>{{ number_format($saldo * $harga, 0, ',', '.') }}</strong></td>
                                <td></td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="6" align="center">
                                <strong>Saldo Per {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</strong>
                            </td>
                            <td align="center">
                                <strong>{{ $saldo == 0 ? 'Nihil' : $saldo }}</strong>
                            </td>
                            <td colspan="3"></td>
                            <td align="right">
                                <strong>
                                    {{ $saldo == 0 ? 'Nihil' : number_format($saldo * $harga, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($current == $lastIndex)
                <table class="ttd-table">
                    <tr>
                        <td align="center">
                            Mengetahui<br>
                            Kepala Dinas<br><br><br><br><br>
                            <strong><u>{{ $opd->kepala_nama ?? '' }}</u></strong><br>
                            <strong>{{ $opd->kepala_nip ?? '' }}</strong>
                        </td>
                        <td align="center">
                            Dibuat Oleh<br>
                            Pengurus Barang<br><br><br><br><br>
                            <strong><u>{{ $opd->pengurus_nama ?? '' }}</u></strong><br>
                            <strong>{{ $opd->pengurus_nip ?? '' }}</strong>
                        </td>
                    </tr>
                </table>
            @endif

            @php $current++; @endphp
        @endforeach
    @endsection
