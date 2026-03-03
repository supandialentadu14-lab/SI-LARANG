@extends('layouts.admin')

@section('header', 'Kwitansi')
@section('subheader', 'Ringkasan pembayaran berdasarkan BAP Penerimaan')

@section('actions')
    <button onclick="window.print()" class="no-print btn btn-neutral"><i class="fas fa-print"></i> Cetak</button>
    <form method="POST" action="{{ route('reports.kwitansi.save') }}" class="no-print inline-block ml-2">
        @csrf
        <input type="hidden" name="id" value="{{ session('kwitansi_current_id') ?? ($saved_id ?? '') }}">
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
    </form>
    @if(session('kwitansi_current_id') || isset($saved_id))
        <a href="{{ route('reports.kwitansi.edit', session('kwitansi_current_id') ?? ($saved_id ?? '')) }}" class="no-print btn btn-outline ml-2">Edit</a>
    @else
        <a href="{{ route('reports.kwitansi.form') }}" class="no-print btn btn-outline ml-2">Edit</a>
    @endif
    @if (!empty($data['penerimaan_nomor']))
        <a href="{{ route('reports.kwitansi.print_all', ['penerimaan_nomor' => $data['penerimaan_nomor']]) }}" class="no-print btn btn-neutral ml-2">
            <i class="fas fa-print"></i> Cetak Berkas Full
        </a>
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
        }
        @media screen {
            html, body { background: #f3f4f6; }
            #print-area { width: 210mm; margin: 0 auto; }
            .preview-paper { width: 210mm; min-height: 330mm; margin: 16px auto; background: #fff; box-shadow: 0 10px 25px rgba(0,0,0,.08); padding: 5mm 15mm; }
        }
    </style>
    <div id="print-area" class="preview-paper bg-white rounded-lg shadow p-6">
        <div class="text-center font-bold text-xl mb-4 uppercase italic">KWITANSI</div>
        <div class="border border-black">
            <table class="w-full text-sm">
                <tr>
                    <td class="px-2 py-1 w-1/3 italic">TAHUN ANGGARAN</td>
                    <td class="px-2 py-1 w-4 text-center">:</td>
                    <td class="px-2 py-1">{{ $data['tahun'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="px-2 py-1 italic">KODE REKENING</td>
                    <td class="px-2 py-1 w-4 text-center">:</td>
                    <td class="px-2 py-1">{{ $data['rekening'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="px-2 py-1 italic">NO. KWT</td>
                    <td class="px-2 py-1 w-4 text-center">:</td>
                    <td class="px-2 py-1">{{ $data['nomor_kwt'] ?? '' }}</td>
                </tr>
            </table>
            
            <table class="w-full text-sm border-t border-black mt-1">
                <tr>
                    <td class="px-2 py-1 w-1/3 align-top italic">Sudah Terima Dari</td>
                    <td class="px-2 py-1 align-top w-4 text-center">:</td>
                    <td class="px-2 py-1 align-top italic">Bendahara Pengeluaran {{ $data['opd_nama'] ?? ($opd->nama_opd ?? '') }} Kabupaten Bolaang Mongondow Selatan</td>
                </tr>
                <tr>
                    <td class="px-2 py-1 align-top italic">Banyaknya Uang</td>
                    <td class="px-2 py-1 align-top w-4 text-center">:</td>
                    <td class="px-2 py-1 align-top italic">{{ $data['terbilang'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="px-2 py-1 align-top italic">Untuk Pembayaran</td>
                    <td class="px-2 py-1 align-top w-4 text-center">:</td>
                    <td class="px-2 py-1 align-top italic">{{ $data['pembayaran_uraian'] ?? '' }}</td>
                </tr>
            </table>

            <div class="border-t border-black flex text-sm font-bold italic">
                <div class="px-2 py-1 border-r border-black w-32">Rp</div>
                <div class="px-2 py-1 flex-1">{{ number_format($data['jumlah'] ?? 0, 0, ',', '.') }}</div>
            </div>

            <div class="p-4 text-sm">
                <div class="flex justify-end mb-8">
                    <div class="text-right">{{ $data['lokasi_tanggal'] ?? '' }}</div>
                </div>
                
                <div class="grid grid-cols-3 gap-4 text-center mb-8">
                    <div>
                        <div class="italic font-bold">PPTK</div>
                        <div class="h-20"></div>
                        <div class="font-bold underline uppercase">{{ $data['pejabat']['pptk'] ?? '' }}</div>
                        <div>NIP. {{ $data['pptk_nip'] ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="italic font-bold">Bendahara Pengeluaran,</div>
                        <div class="h-20"></div>
                        <div class="font-bold underline uppercase">{{ $data['pejabat']['bendahara'] ?? '' }}</div>
                        <div>NIP. {{ $data['bendahara_nip'] ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="italic font-bold">Yang Menerima,</div>
                        <div class="italic font-bold">Pihak Ketiga</div>
                        <div class="h-16"></div>
                        <div class="font-bold underline uppercase">{{ $data['pejabat']['pihak_ketiga'] ?? '' }}</div>
                    </div>
                </div>

                <div class="text-center">
                    <div class="italic font-bold">Mengetahui,</div>
                    <div class="italic font-bold">Pengguna Anggaran</div>
                    <div class="h-20"></div>
                    <div class="font-bold underline uppercase">{{ $data['pejabat']['pengguna'] ?? '' }}</div>
                    <div>NIP. {{ $data['ppk_nip'] ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
