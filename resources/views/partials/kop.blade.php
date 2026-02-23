{{-- Komponen KOP Surat: gunakan @include('partials.kop', ['opd' => $opd]) --}}
{{-- Parameter: $opd (object/array) dengan nama_opd, opsional logo di public/images/bolsel.png --}}
<style>
    .kop { width: 100%; table-layout: fixed; }
    .kop td { vertical-align: middle; }
    .kop-logo { width: 80px; text-align: center; }
    .kop-logo img { width: 70px; height: 70px; object-fit: contain; }
    .kop-text { text-align: center; }
    .kop-text .line1 { font-weight: 800; font-size: 16px; letter-spacing: .4px; text-transform: uppercase; line-height: 1.1; margin: 0; }
    .kop-text .line2 { font-weight: 800; font-size: 22px; text-transform: uppercase; line-height: 1.1; margin: 2px 0 0 0; }
    .kop-text .line3, .kop-text .line4 { font-style: italic; font-size: 13px; line-height: 1.1; margin: 0; }
    @media print {
        .kop-logo { width: 100px; }
        .kop-logo img { width: 90px; height: 90px; }
        .kop-text .line2 { font-size: 22px; }
    }
</style>
<table class="kop">
    <tr>
        <td class="kop-logo">
            @if (file_exists(public_path('images/bolsel.png')))
                <img src="{{ asset('images/bolsel.png') }}" alt="Logo Bolsel">
            @endif
        </td>
        <td class="kop-text">
            <div class="line1">PEMERINTAH KABUPATEN BOLAANG MONGONDOW SELATAN</div>
            <div class="line2">{{ \Illuminate\Support\Str::upper($opd->nama_opd ?? 'Dinas Komunikasi dan Informatika') }}</div>
            <div class="line3">{{ ($opd->alamat_opd ?? null) ?: 'Jln. Ir. Soekarno Komplek Perkantoran Panango Desa Tabilaa, Kec. Bolaang Uki' }}</div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="border-b-4 border-black mt-2 mb-2"></div>
        </td>
    </tr>
</table>
