@extends('layouts.admin')

@section('title', 'Cetak Berita Acara Pemeriksaan')
@section('header', 'Berita Acara Pemeriksaan Barang/Pekerjaan')
@section('subheader', 'Pratinjau & cetak')

@section('actions')
    <button onclick="window.print()" class="no-print btn btn-neutral"><i class="fas fa-print"></i> Cetak</button>
    <form method="POST" action="{{ route('reports.pemeriksaan.save') }}" class="no-print inline-block ml-2">
        @csrf
        <input type="hidden" name="id" value="{{ session('bap_current_id') ?? ($saved_id ?? '') }}">
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
    </form>
    @if (isset($saved_id))
        <a href="{{ route('reports.pemeriksaan.edit', $saved_id) }}" class="no-print btn btn-outline ml-2">Edit</a>
    @else
        <a href="{{ route('reports.pemeriksaan.form') }}" class="no-print btn btn-outline ml-2">Edit</a>
    @endif
@endsection

@section('content')
    <div id="print-area" class="preview-paper bg-white text-black">
        <div class="mb-4">
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
                    #print-area { width: 210mm; margin: 0 auto; }
                    .preview-paper { width: 210mm; min-height: 330mm; margin: 16px auto; background: #fff; box-shadow: 0 10px 25px rgba(0,0,0,.08); padding: 5mm 15mm; }
                }
                table.items td, table.items th { border: 1px solid #000; }
            </style>
            @include('partials.kop', ['opd' => $opd])
        </div>
        
        <div class="text-center mb-4">
            <h2 class="font-extrabold text-lg">BERITA ACARA PEMERIKSAAN BARANG/PEKERJAAN</h2>
            <p class="text-sm">NOMOR: {{ $data['nomor'] ?? '' }}</p>
        </div>
        
        <p class="mb-3 text-sm">
            {{ $data['tanggal_kata'] ?? ('Pada hari ' . \Carbon\Carbon::parse($data['tanggal'])->locale('id')->translatedFormat('l') . ' tanggal ' . \Carbon\Carbon::parse($data['tanggal'])->translatedFormat('d F Y')) }},
            kami yang bertanda tangan di bawah ini:
        </p>
        
        <div class="mb-4">
            <table class="w-full text-sm">
                <tr>
                    <td class="w-28 pl-6">Nama</td>
                    <td class="w-4 pl-6">:</td>
                    <td><span class="font-bold pl-6">{{ $data['ppk']['nama'] ?? '' }}</span></td>
                </tr>
                <tr>
                    <td class="pl-6">Jabatan</td>
                    <td class="pl-6">:</td>
                    <td class="pl-6">Pejabat Pembuat Komitmen</td>
                </tr>
                <tr>
                    <td class="pl-6">Alamat</td>
                    <td class="pl-6">:</td>
                    <td class="pl-6">{{ $data['ppk']['alamat'] ?? '' }}</td>
                </tr>
            </table>
        </div>
 
        @php
            $pekerjaan = $data['nota']['belanja'] ?? '';
        @endphp
        <p class="mb-1 text-sm">Menerangkan dengan benar bahwa Pihak Pertama telah menyerahkan pekerjaan : <span class="font-bold">{{ $pekerjaan }}</span></p>
        <table class="w-full text-sm mb-3">
            <tr>
                <td class="w-40 pl-6">Nama Penyedia Jasa</td>
                <td class="w-4 pl-6">:</td>
                <td class="font-bold pl-6">{{ $data['nota']['penyedia']['toko'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="pl-6 align-top">Alamat</td>
                <td class="pl-6 align-top">:</td>
                <td class="pl-6">{{ $data['nota']['penyedia']['alamat'] ?? '' }}</td>
            </tr>
        </table>
        
        <p class="mb-3 text-sm">
            Sebagai realisasi Nota Pesanan Nomor : {{ $data['nota']['nomor'] ?? '-' }} tanggal {{ \Carbon\Carbon::parse($data['nota']['tanggal'] ?? now())->locale('id')->translatedFormat('d F Y') }},
            dengan jumlah/jenis daftar barang terlampir dan berkesimpulan bahwa barang/pekerjaan dapat diterima sesuai mestinya:
        </p>
        
        <div class="overflow-x-auto mb-4">
            <table class="items w-full text-xs border border-black">
                <thead>
                    <tr class="text-center font-bold">
                        <th class="px-2 py-1">No</th>
                        <th class="px-2 py-1">Jenis Bahan/Alat (Barang)</th>
                        <th class="px-2 py-1">Kuantitas</th>
                        <th class="px-2 py-1">Satuan</th>
                        <th class="px-2 py-1">Harga Satuan</th>
                        <th class="px-2 py-1">Total</th>
                        <th class="px-2 py-1">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach ($data['items'] as $i => $item)
                        @php $total += (int)($item['jumlah'] ?? 0); @endphp
                        <tr>
                            <td class="px-2 py-1 text-center">{{ $i + 1 }}</td>
                            <td class="px-2 py-1">{{ $item['nama'] }}</td>
                            <td class="px-2 py-1 text-center">{{ $item['kuantitas'] }}</td>
                            <td class="px-2 py-1 text-center">{{ $item['satuan'] ?? '-' }}</td>
                            <td class="px-2 py-1 text-right">{{ number_format($item['harga'] ?? 0, 0, ',', '.') }}</td>
                            <td class="px-2 py-1 text-right">{{ number_format($item['jumlah'] ?? 0, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="5" class="px-2 py-1 text-right font-bold">Jumlah</td>
                        <td class="px-2 py-1 text-right font-bold">{{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="7" class="mb-3 text-sm text-center font-extrabold">Terbilang {{ \Illuminate\Support\Str::upper($data['terbilang'] ?? '') }} rupiah</td>

                    </tr>
                </tbody>
            </table>
        </div>
        
        <p </p>
        
        <p class="mb-3 text-sm">1. Barang Baik (V)</p>
        <p class="mb-6 text-sm">2. Barang Tidak Baik (X)</p>
        
        <div class="grid grid-cols-2 gap-6 mt-6">
            <div class="text-center">
                <p class="mb-1">Penyedia</p>
                <div class="h-24"></div>
                <p class="font-bold underline">{{ $data['nota']['penyedia']['toko'] ?? '' }}</p>
            </div>
            <div class="text-center">
                <p class="mb-1">Pejabat Pembuat Komitmen</p>
                <div class="h-24"></div>
                <p class="font-bold underline">{{ $data['ppk']['nama'] ?? '' }}</p>
                <p class="text-sm">NIP. {{ $data['ppk']['nip'] ?? '' }}</p>
            </div>
        </div>

        <div class="text-center mt-8">
            <p class="mb-1">MENGETAHUI,</p>
            <p class="mb-1">PENGGUNA ANGGARAN SELAKU PPK</p>
            <div class="h-24"></div>
            <p class="font-bold underline">{{ $data['ppk']['nama'] ?? '' }}</p>
            <p class="text-sm">NIP. {{ $data['ppk']['nip'] ?? '' }}</p>
        </div>
    </div>
@endsection
