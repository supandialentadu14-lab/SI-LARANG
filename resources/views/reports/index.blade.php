@extends('layouts.admin')

@section('header', 'Laporan Persediaan Barang Habis Pakai')

@section('content')

    <div class="bg-white rounded-lg shadow p-6 mb-6 print:hidden">
        <form action="{{ route('reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
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
                <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm bg-orange-500 text-white hover:bg-orange-600">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm bg-black text-white hover:bg-black">
                    <i class="fas fa-print"></i>
                    Print
                </button>
                <a href="{{ route('reports.index.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm bg-indigo-600 text-white hover:bg-indigo-700">
                    <i class="fas fa-file-excel"></i>
                    Export Excel
                </a>
            </div>
        </form>
    </div>

    <style>

    /* ============================= */
    /* HILANGKAN BORDER LUAR        */
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

    /* ============================= */
    /* TABEL REPORT                 */
    /* ============================= */
    .report-table {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
    }

    .report-table th,
    .report-table td {
        border: 1px solid #000; /* Border hanya di dalam tabel */
        padding: 5px;
        font-size: 12px;
    }

    .report-table thead {
        display: table-header-group;
    }

    .report-table tr {
        page-break-inside: avoid;
    }
    .split-cell { position: relative; display: flex; width: 100%; height: 100%; }
    .split-cell .left { flex: 1; text-align: center; padding: 6px 8px; }
    .split-cell .right { flex: 1; text-align: right; padding: 6px 8px; }
    td.split-col { position: relative; padding: 0 !important; }
    td.split-col::after { content: ''; position: absolute; left: 50%; top: -1px; bottom: -1px; width: 1px; background: #9ca3af; pointer-events: none; }

    /* ============================= */
    /* PRINT SETTING                */
    /* ============================= */
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
            position: static !important;
            width: auto !important;
            overflow: visible !important;
            border: none !important;
        }

        .print\:hidden {
            display: none !important;
        }

        @page {
            size: 330mm 210mm;
            margin: 12mm;
        }

        body {
            margin: 0;
        }
    }

</style>



    <div id="print-area" class="print-area bg-white shadow-lg border p-8 rounded-lg">

        <div class="border-b-2 border-black pb-4 mb-4">
            <div class="text-center mb-4">
                <h1 class="text-xl font-bold uppercase">
                    Laporan Persediaan Barang Habis Pakai
                </h1>
                <h5 class="text-sm font-semibold mt-1">
                    Per {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                </h5>
            </div>

            <div class="mb-2 text-md">
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
            </div>
        </div>

        <table class="w-full border-collapse text-xs text-center border border-gray-400">

            <thead>
                <tr class="bg-gray-200 font-bold">
                    <th rowspan="2" class="border border-gray-400 px-2 py-2">No</th>
                    <th rowspan="2" class="border border-gray-400 px-2 py-2 text-left">Nama Barang</th>

                    <th colspan="3" class="border border-gray-400 px-2 py-2">SALDO AWAL</th>
                    <th colspan="3" class="border border-gray-400 px-2 py-2">MUTASI MASUK</th>
                    <th colspan="3" class="border border-gray-400 px-2 py-2">MUTASI KELUAR</th>
                    <th colspan="3" class="border border-gray-400 px-2 py-2">SALDO AKHIR</th>
                </tr>

                <tr class="bg-gray-100 text-xs">
                    @for ($i = 0; $i < 4; $i++)
                        <th class="border border-gray-400 px-2 py-2">Jmlh Barang</th>
                        <th class="border border-gray-400 px-2 py-2">Harga Satuan (Rp)</th>
                        <th class="border border-gray-400 px-2 py-2">Jumlah (Rp)</th>
                    @endfor
                </tr>
            </thead>

            <tbody>

                @php
                    $no = 1;
                    $saldo = [];
                    $lastSaldoPerProduct = [];
                    $lastDate = null;
                @endphp

                @forelse($reportData as $item)
                    @php
                        $currentDate = \Carbon\Carbon::parse($item['date'])->format('Y-m-d');
                    @endphp

                    {{-- HEADER TANGGAL --}}
                    @if ($lastDate != $currentDate)
                        <tr class="bg-gray-100 font-bold text-left">
                            <td colspan="14" class="border border-gray-400 px-3 py-2">
                                Tanggal :
                                {{ \Carbon\Carbon::parse($item['date'])->translatedFormat('d F Y') }}
                            </td>
                        </tr>
                        @php $lastDate = $currentDate; @endphp
                    @endif

                    @php
                        $productId = $item['product_id'];
                        $harga = $item['harga'];
                        $satuan = $item['satuan'] ?? '';

                        if (!isset($saldo[$productId])) {
                            $saldo[$productId] = 0;
                        }

                        $saldoAwal = $saldo[$productId];
                        $masuk = $item['masuk'];
                        $keluar = $item['keluar'];

                        $saldoAkhir = $saldoAwal + $masuk - $keluar;
                        $saldo[$productId] = $saldoAkhir;

                        $lastSaldoPerProduct[$productId] = [
                            'saldo' => $saldoAkhir,
                            'harga' => $harga,
                        ];
                    @endphp

                    <tr>
                        <td class="border border-gray-400 px-2 py-2 text-center">
                            {{ $no++ }}
                        </td>

                        <td class="border border-gray-400 px-2 py-2 text-left">
                            {{ $item['name'] }}
                        </td>

                        {{-- SALDO AWAL --}}
                        <td class="border border-gray-400 p-0 split-col">
                            <div class="split-cell">
                                <div class="left font-semibold">{{ $saldoAwal }}</div>
                                <div class="right">{{ $satuan }}</div>
                            </div>
                        </td>
                        <td class="border border-gray-400 px-2 py-2">
                            {{ number_format($harga, 0, ',', '.') }}
                        </td>
                        <td class="border border-gray-400 px-2 py-2">
                            {{ number_format($saldoAwal * $harga, 0, ',', '.') }}
                        </td>

                        {{-- MASUK --}}
                        <td class="border border-gray-400 p-0 split-col">
                            <div class="split-cell">
                                <div class="left font-bold text-green-600">{{ $masuk }}</div>
                                <div class="right">{{ $satuan }}</div>
                            </div>
                        </td>
                        <td class="border border-gray-400 px-2 py-2">
                            {{ number_format($harga, 0, ',', '.') }}
                        </td>
                        <td class="border border-gray-400 px-2 py-2">
                            {{ number_format($masuk * $harga, 0, ',', '.') }}
                        </td>

                        {{-- KELUAR --}}
                        <td class="border border-gray-400 p-0 split-col">
                            <div class="split-cell">
                                <div class="left font-bold text-red-600">{{ $keluar }}</div>
                                <div class="right">{{ $satuan }}</div>
                            </div>
                        </td>
                        <td class="border border-gray-400 px-2 py-2">
                            {{ number_format($harga, 0, ',', '.') }}
                        </td>
                        <td class="border border-gray-400 px-2 py-2">
                            {{ number_format($keluar * $harga, 0, ',', '.') }}
                        </td>

                        {{-- SALDO AKHIR --}}
                        <td class="border border-gray-400 p-0 split-col">
                            <div class="split-cell">
                                <div class="left font-bold">{{ $saldoAkhir }}</div>
                                <div class="right">{{ $satuan }}</div>
                            </div>
                        </td>
                        <td class="border border-gray-400 px-2 py-2">
                            {{ number_format($harga, 0, ',', '.') }}
                        </td>
                        <td class="border border-gray-400 px-2 py-2 font-bold">
                            {{ number_format($saldoAkhir * $harga, 0, ',', '.') }}
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="14" class="border border-gray-400 py-6 text-gray-400 text-center">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse

                {{-- GRAND TOTAL HANYA DARI SALDO TERAKHIR PER PRODUK --}}
                @php
                    $grandTotal = 0;
                    foreach ($lastSaldoPerProduct as $data) {
                        $grandTotal += $data['saldo'] * $data['harga'];
                    }
                @endphp

                <tr class="bg-gray-200 font-bold text-right">
                    <td colspan="13" class="border border-gray-400 px-3 py-3">
                        TOTAL NILAI PERSEDIAAN
                    </td>
                    <td class="border border-gray-400 px-3 py-3 text-center">
                        {{ number_format($grandTotal, 0, ',', '.') }}
                    </td>
                </tr>

            </tbody>
        </table>
        {{-- TANDA TANGAN --}}

        <div class="mt-16 w-full text-sm">

            <div class="flex justify-between">

                {{-- KIRI --}}
                <div class="text-center w-1/2">
                    <p class="font-semibold">Mengetahui</p>
                    <p class="font-semibold">Kepala Dinas</p>

                    <div style="height:90px;"></div>

                    <p class="font-bold underline">
                        {{ $opd->kepala_nama ?? '' }}
                    </p>
                    <p class="font-semibold">
                        NIP. {{ $opd->kepala_nip ?? '' }}
                    </p>
                </div>

                {{-- KANAN --}}
                <div class="text-center w-1/2">
                    <p class="font-semibold">Dibuat Oleh</p>
                    <p class="font-semibold">Pengurus Barang</p>

                    <div style="height:90px;"></div>

                    <p class="font-bold underline">
                        {{ $opd->pengurus_nama ?? '' }}
                    </p>
                    <p class="font-semibold">
                        NIP. {{ $opd->pengurus_nip ?? '' }}
                    </p>
                </div>

            </div>

        </div>

    </div>

@endsection
