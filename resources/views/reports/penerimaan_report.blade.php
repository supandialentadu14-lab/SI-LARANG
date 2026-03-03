@extends('layouts.admin')

@section('header', 'Berita Acara Penerimaan')
@section('subheader', 'Pratinjau & cetak')

@section('actions')
    <button onclick="window.print()" class="no-print btn btn-neutral"><i class="fas fa-print"></i> Cetak</button>
    <form method="POST" action="{{ route('reports.penerimaan.save') }}" class="no-print inline-block ml-2">
        @csrf
        <input type="hidden" name="id" value="{{ session('penerimaan_current_id') }}">
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
    </form>
    @if (session('penerimaan_current_id'))
        <a href="{{ route('reports.penerimaan.edit', session('penerimaan_current_id')) }}" class="no-print btn btn-outline ml-2">Edit</a>
    @else
        <a href="{{ route('reports.penerimaan.form') }}" class="no-print btn btn-outline ml-2">Edit</a>
    @endif
@endsection

@section('content')
    <style>
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
        @media print {
            body * { visibility: hidden; }
            #print-area, #print-area * { visibility: visible; }
            #print-area { position: static !important; width: auto !important; overflow: visible !important; }
            @page { size: 210mm 330mm; margin: 5mm 15mm; }
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
            .preview-paper p { margin: 5px 0; }
            .preview-paper h2 { margin: 5px 0; }
            .preview-paper table { margin-top: 6px; }
        }
        @media screen {
            html, body { background: #f3f4f6; }
            #print-area { width: 210mm; margin: 0 auto; }
            .preview-paper { width: 210mm; min-height: 330mm; margin: 16px auto; background: #fff; box-shadow: 0 10px 25px rgba(0,0,0,.08); padding: 5mm 15mm; }
        }
        .report-table { border-collapse: collapse; width: 100%; }
        .report-table th, .report-table td { border: 1px solid #000; padding: 6px; font-size: 12px; }
        .kop { margin-bottom: 10px; }
        .kop h1 { text-align: center; font-weight: 800; text-transform: uppercase; font-size: 16px; margin: 6px 0; }
    </style>

    <div id="print-area" class="preview-paper">
        @include('partials.kop', ['opd' => $opd])

        <div class="text-center mb-2">
            <h2 class="font-extrabold text-lg">BERITA ACARA PENERIMAAN BARANG/PEKERJAAN</h2>
            <p class="text-sm">NOMOR: {{ $data['nomor'] ?? '' }}</p>
        </div>

        <p class="mb-3 text-sm">
            {{ $data['tanggal_kata'] ?? '' }}
        </p>

        <table class="w-full text-sm mb-3">
            <tr>
                <td class="w-28 align-top pl-6">Nama</td>
                <td class="w-4 align-top pl-6">:</td>
                <td class="align-top pl-6"><span class="font-bold">{{ $data['pengguna']['nama'] ?? '' }}</span></td>
            </tr>
            <tr>
                <td class="align-top pl-6">NIP</td>
                <td class="align-top pl-6">:</td>
                <td class="align-top pl-6">{{ $data['pengguna']['nip'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="align-top pl-6">Jabatan</td>
                <td class="align-top pl-6">:</td>
                <td class="align-top pl-6">{{ $data['pengguna']['jabatan'] ?? 'Pengurus Barang Pengguna' }}</td>
            </tr>
        </table>

        <p class="mb-1 text-sm">Berdasarkan Berita Acara Pemeriksaan Barang Nomor: {{ $data['pemeriksaan_nomor'] ?? '-' }}. Telah menerima barang yang diserahkan oleh Pihak Ketiga sebagai berikut :</p>

        <table class="report-table items text-sm mb-3">
            <thead>
                <tr>
                    <th style="width:30px">No</th>
                    <th>Jenis Bahan/Alat (Barang)</th>
                    <th style="width:80px">Kuantitas</th>
                    <th style="width:80px">Satuan</th>
                    <th style="width:120px">Harga Satuan</th>
                    <th style="width:120px">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $i=1; @endphp
                @foreach(($data['items'] ?? []) as $row)
                <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>{{ $row['nama'] ?? '' }}</td>
                    <td class="text-center">{{ $row['kuantitas'] ?? '' }}</td>
                    <td class="text-center">{{ $row['satuan'] ?? '' }}</td>
                    <td class="text-right">Rp {{ number_format((int)($row['harga'] ?? 0), 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format((int)($row['jumlah'] ?? 0), 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="5" class="text-right font-bold">Jumlah</td>
                    <td class="text-right font-bold">Rp {{ number_format((int)($data['total'] ?? 0), 0, ',', '.') }}</td>
                    
                </tr>
                <tr>
                    <td colspan="6" class="text-center font-bold italic">Terbilang : {{ $data['terbilang'] ?? '' }} rupiah</td>
                </tr>
            </tbody>
            
        </table>

        

        <div class="grid grid-cols-2 gap-6 text-sm mt-8 mb-4">
            <div class="text-center">
                <div class="font-bold">Yang Menerima,</div>
                <div class="font-bold mb-12">Pengurus Barang Pengguna</div>
                <br>
                <div class="font-bold underline uppercase">{{ $data['pengguna']['nama'] ?? '' }}</div>
                <div>NIP: {{ $data['pengguna']['nip'] ?? '' }}</div>
            </div>
            <div class="text-center">
                <div class="font-bold">Mengetahui,</div>
                <div class="font-bold mb-12">Pejabat Pembuat Komitmen</div>
                <br>
                <div class="font-bold underline uppercase">{{ $data['ppk']['nama'] ?? '' }}</div>
                <div>NIP: {{ $data['ppk']['nip'] ?? '' }}</div>
            </div>
        </div>
<br>
        <div class="text-center text-sm mt-4">
            <div class="font-bold mb-2">MENGETAHUI,</div>
            <div class="mb-12">PENGGUNA ANGGARAN SELAKU PPK</div>
            <br>
            <div class="font-bold underline uppercase">{{ $data['ppk']['nama'] ?? '' }}</div>
            <div>NIP: {{ $data['ppk']['nip'] ?? '' }}</div>
        </div>
    </div>
@endsection
