@extends('layouts.admin')

@section('header', 'Cetak Berkas Full')
@section('subheader', 'Nota Pesanan • BAP Pemeriksaan • BAP Penerimaan • Kwitansi')

@section('actions')
    <button type="button" onclick="window.print()" class="btn btn-neutral"><i class="fas fa-print"></i> Cetak</button>
@endsection

@section('content')
    <style>
        @media print {
            #print-area { position: static !important; width: auto !important; overflow: visible !important; }
            @page { size: auto; margin: 10mm; }
            .doc { display: block !important; page-break-after: always !important; break-after: page !important; }
            .doc:last-of-type { page-break-after: auto !important; break-after: auto !important; }
        }
        @media screen {
            #print-area { width: 210mm; margin: 0 auto; }
            .doc { width: 210mm; min-height: 330mm; margin: 16px auto; background: #fff; box-shadow: 0 10px 25px rgba(0,0,0,.08); padding: 10mm; }
        }
    </style>
    <div id="print-area" class="bg-white rounded-lg shadow p-6">
        <div class="doc">{!! $notaHtml !!}</div>
        <div class="doc">{!! $pemeriksaanHtml !!}</div>
        <div class="doc">{!! $penerimaanHtml !!}</div>
        <div class="doc">{!! $kwitansiHtml !!}</div>
    </div>
@endsection
