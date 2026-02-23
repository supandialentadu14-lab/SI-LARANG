<?php

namespace App\Http\Controllers;

use App\Models\NotaMaster;
use App\Models\OpdSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KwitansiController extends Controller
{
    protected function loadNotaMaster(): array
    {
        $row = NotaMaster::where('user_id', Auth::id())->first();
        if ($row) {
            return [
                'opd' => ['nama' => $row->opd_nama ?? '', 'alamat' => $row->opd_alamat ?? ''],
                'ppk' => ['nama' => $row->ppk_nama ?? '', 'nip' => $row->ppk_nip ?? '', 'alamat' => $row->ppk_alamat ?? ''],
                'penyedia' => ['toko' => $row->penyedia_toko ?? '', 'pemilik' => $row->penyedia_pemilik ?? '', 'alamat' => $row->penyedia_alamat ?? ''],
                'pejabat' => ['nama' => $row->pejabat_nama ?? '', 'nip' => $row->pejabat_nip ?? ''],
                'pptk' => ['nama' => $row->pptk_nama ?? '', 'nip' => $row->pptk_nip ?? ''],
                'pengurus_barang' => ['nama' => $row->pengurus_barang_nama ?? '', 'nip' => $row->pengurus_barang_nip ?? ''],
                'pengurus_pengguna' => ['nama' => $row->pengurus_pengguna_nama ?? '', 'nip' => $row->pengurus_pengguna_nip ?? ''],
                'bendahara' => ['nama' => $row->bendahara_nama ?? '', 'nip' => $row->bendahara_nip ?? ''],
            ];
        }
        return [
            'opd' => ['nama' => '', 'alamat' => ''],
            'ppk' => ['nama' => '', 'nip' => '', 'alamat' => ''],
            'penyedia' => ['toko' => '', 'pemilik' => '', 'alamat' => ''],
            'pejabat' => ['nama' => '', 'nip' => ''],
            'pptk' => ['nama' => '', 'nip' => ''],
            'pengurus_barang' => ['nama' => '', 'nip' => ''],
            'pengurus_pengguna' => ['nama' => '', 'nip' => ''],
            'bendahara' => ['nama' => '', 'nip' => ''],
        ];
    }

    protected function toWordsId(int $value): string
    {
        $huruf = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        if ($value < 12) return $huruf[$value];
        if ($value < 20) return $this->toWordsId($value - 10) . " belas";
        if ($value < 100) return $this->toWordsId(intval($value / 10)) . " puluh " . $this->toWordsId($value % 10);
        if ($value < 200) return "seratus " . $this->toWordsId($value - 100);
        if ($value < 1000) return $this->toWordsId(intval($value / 100)) . " ratus " . $this->toWordsId($value % 100);
        if ($value < 2000) return "seribu " . $this->toWordsId($value - 1000);
        if ($value < 1000000) return $this->toWordsId(intval($value / 1000)) . " ribu " . $this->toWordsId($value % 1000);
        if ($value < 1000000000) return $this->toWordsId(intval($value / 1000000)) . " juta " . $this->toWordsId($value % 1000000);
        return (string) $value;
    }

    protected function listPenerimaanDocs(): array
    {
        $disk = Storage::disk('local');
        $dir = 'users/'.Auth::id().'/bap-penerimaan';
        $files = $disk->exists($dir) ? $disk->files($dir) : [];
        $items = [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $data = json_decode($disk->get($file), true) ?: [];
            $items[] = [
                'id' => basename($file, '.json'),
                'nomor' => $data['nomor'] ?? '',
                'tanggal' => $data['tanggal'] ?? '',
                'total' => (int)($data['total'] ?? 0),
                'nota' => $data['nota'] ?? [],
                'belanja' => $data['nota']['belanja'] ?? '',
            ];
        }
        usort($items, fn($a, $b) => ($b['tanggal'] ?? '') <=> ($a['tanggal'] ?? ''));
        return $items;
    }

    protected function findPenerimaanByNomor(?string $nomor): ?array
    {
        if (!$nomor) return null;
        foreach ($this->listPenerimaanDocs() as $doc) {
            if (trim($doc['nomor'] ?? '') === trim($nomor)) return $doc;
        }
        return null;
    }

    protected function listPemeriksaanDocs(): array
    {
        $disk = Storage::disk('local');
        $dir = 'users/'.Auth::id().'/bap-pemeriksaan';
        $files = $disk->exists($dir) ? $disk->files($dir) : [];
        $items = [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $data = json_decode($disk->get($file), true) ?: [];
            $items[] = [
                'id' => basename($file, '.json'),
                'nomor' => $data['nomor'] ?? '',
                'tanggal' => $data['tanggal'] ?? '',
                'nota' => $data['nota'] ?? [],
            ];
        }
        usort($items, fn($a, $b) => ($b['tanggal'] ?? '') <=> ($a['tanggal'] ?? ''));
        return $items;
    }

    protected function findPemeriksaanByNomor(?string $nomor): ?array
    {
        if (!$nomor) return null;
        foreach ($this->listPemeriksaanDocs() as $doc) {
            if (trim($doc['nomor'] ?? '') === trim($nomor)) return $doc;
        }
        return null;
    }

    protected function findNotaByNomor(?string $nomor): ?array
    {
        if (!$nomor) return null;
        $disk = Storage::disk('local');
        $dir = 'users/'.Auth::id().'/nota-pesanan';
        $files = $disk->exists($dir) ? $disk->files($dir) : [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $data = json_decode($disk->get($file), true) ?: [];
            if (trim(($data['nomor'] ?? '')) === trim($nomor)) {
                return [
                    'id' => basename($file, '.json'),
                    'nomor' => $data['nomor'] ?? '',
                    'tanggal' => $data['tanggal'] ?? '',
                ];
            }
        }
        return null;
    }

    public function form(Request $request)
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $docs = $this->listPenerimaanDocs();
        $data = [
            'tahun' => now()->year,
            'rekening' => '',
            'nomor_kwt' => '',
            'tanggal' => now()->toDateString(),
            'penerimaan_nomor' => '',
        ];
        return view('kwitansi.create', compact('data', 'opd', 'docs'));
    }

    public function report(Request $request)
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $master = $this->loadNotaMaster();
        $payload = $request->all();
        $selected = null;
        foreach ($this->listPenerimaanDocs() as $doc) {
            if (($doc['nomor'] ?? '') === ($payload['penerimaan_nomor'] ?? '')) { $selected = $doc; break; }
        }
        $total = (int)($selected['total'] ?? 0);
        $bulanNama = \Carbon\Carbon::parse($payload['tanggal'] ?? now()->toDateString())->locale('id')->translatedFormat('F Y');
        $data = [
            'tahun' => $payload['tahun'] ?? now()->year,
            'rekening' => $payload['rekening'] ?? '',
            'nomor_kwt' => $payload['nomor_kwt'] ?? '',
            'tanggal' => $payload['tanggal'] ?? now()->toDateString(),
            'penerimaan_nomor' => $payload['penerimaan_nomor'] ?? '',
            'jumlah' => $total,
            'terbilang' => ucwords($this->toWordsId((int)$total)),
            'pembayaran_uraian' => 'Belanja ' . (($selected['belanja'] ?? '') ?: ''),
            'lokasi_tanggal' => ($opd->nama_opd ?? 'Bolaang Uki') . ', ' . $bulanNama,
            'pejabat' => [
                'pptk' => $master['pptk']['nama'] ?? '',
                'bendahara' => $master['bendahara']['nama'] ?? '',
                'pihak_ketiga' => ($selected['nota']['penyedia']['pemilik'] ?? ($selected['nota']['penyedia']['toko'] ?? '')),
                'pengguna' => $master['ppk']['nama'] ?? '',
            ],
            'opd_nama' => $opd->nama_opd ?? '',
            'bendahara_nip' => $master['bendahara']['nip'] ?? '',
            'pptk_nip' => $master['pptk']['nip'] ?? '',
            'ppk_nip' => $master['ppk']['nip'] ?? '',
        ];
        session(['kwitansi_current' => $data]);
        return view('reports.kwitansi_report', compact('data', 'opd'));
    }

    public function export()
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $data = session('kwitansi_current');
        if (!$data) abort(400, 'Tidak ada data untuk diekspor');
        return $this->exportExcel('reports.kwitansi_report', compact('data', 'opd'), 'kwitansi.xls');
    }

    public function printAll(Request $request)
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $kwt = session('kwitansi_current') ?? [];
        $penerimaanNomor = $request->input('penerimaan_nomor') ?? ($kwt['penerimaan_nomor'] ?? null);
        $penerimaan = $this->findPenerimaanByNomor($penerimaanNomor);
        if (!$penerimaan) {
            abort(404, 'BAP Penerimaan tidak ditemukan');
        }
        $disk = Storage::disk('local');
        $penerimaanPath = "users/".Auth::id()."/bap-penerimaan/{$penerimaan['id']}.json";
        $penerimaanData = json_decode($disk->get($penerimaanPath), true) ?: [];

        $pemeriksaanNomor = $penerimaanData['pemeriksaan_nomor'] ?? null;
        $pemeriksaan = $this->findPemeriksaanByNomor($pemeriksaanNomor);
        $pemeriksaanData = [];
        if ($pemeriksaan) {
            $pemeriksaanPath = "users/".Auth::id()."/bap-pemeriksaan/{$pemeriksaan['id']}.json";
            $pemeriksaanData = json_decode($disk->get($pemeriksaanPath), true) ?: [];
        }

        $notaData = [];
        $notaNomor = $pemeriksaanData['nota']['nomor'] ?? null;
        if ($notaNomor) {
            $nota = $this->findNotaByNomor($notaNomor);
            if ($nota) {
                $notaPath = "users/".Auth::id()."/nota-pesanan/{$nota['id']}.json";
                $notaData = json_decode($disk->get($notaPath), true) ?: [];
            } else {
                $notaData = $pemeriksaanData['nota'] ?? [];
            }
        } else {
            $notaData = $pemeriksaanData['nota'] ?? [];
        }

        $kwitansiData = $kwt ?: (app(self::class)->report(new Request([
            'tahun' => now()->year,
            'rekening' => '',
            'nomor_kwt' => '',
            'tanggal' => now()->toDateString(),
            'penerimaan_nomor' => $penerimaanNomor,
        ]))->getData()['data'] ?? []);

        $extract = function (string $html): string {
            try {
                libxml_use_internal_errors(true);
                $dom = new \DOMDocument('1.0', 'UTF-8');
                $dom->loadHTML($html);
                $xpath = new \DOMXPath($dom);
                $nodes = $xpath->query("//*[@id='print-area']");
                if ($nodes && $nodes->length > 0) {
                    $node = $nodes->item(0);
                    $inner = '';
                    foreach ($node->childNodes as $child) {
                        $inner .= $dom->saveHTML($child);
                    }
                    libxml_clear_errors();
                    return $inner;
                }
                libxml_clear_errors();
            } catch (\Throwable $e) {}
            return $html;
        };

        $htmlNota = view('reports.nota_pesanan_report', ['data' => $notaData, 'opd' => $opd])->render();
        $htmlPemeriksaan = view('reports.pemeriksaan_report', ['data' => $pemeriksaanData, 'opd' => $opd])->render();
        $htmlPenerimaan = view('reports.penerimaan_report', ['data' => $penerimaanData, 'opd' => $opd])->render();
        $htmlKwitansi = view('reports.kwitansi_report', ['data' => $kwitansiData, 'opd' => $opd])->render();

        $notaInner = $extract($htmlNota);
        $pemeriksaanInner = $extract($htmlPemeriksaan);
        $penerimaanInner = $extract($htmlPenerimaan);
        $kwitansiInner = $extract($htmlKwitansi);

        return view('reports.print_full', [
            'notaHtml' => $notaInner,
            'pemeriksaanHtml' => $pemeriksaanInner,
            'penerimaanHtml' => $penerimaanInner,
            'kwitansiHtml' => $kwitansiInner,
        ]);
    }

    protected function exportExcel(string $view, array $params, string $filename)
    {
        $content = view($view, $params)->render();
        try {
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->loadHTML($content);
            $xpath = new \DOMXPath($dom);
            $styles = '';
            foreach ($xpath->query('//style') as $styleNode) {
                $styles .= $styleNode->nodeValue."\n";
            }
            $nodes = $xpath->query("//*[@id='print-area']");
            if ($nodes && $nodes->length > 0) {
                $node = $nodes->item(0);
                $inner = '';
                foreach ($node->childNodes as $child) {
                    $inner .= $dom->saveHTML($child);
                }
                $content = '<div id="print-area">'.$inner.'</div>';
                if ($styles) {
                    $content = '<style>'.$styles.'</style>'.$content;
                }
            }
            libxml_clear_errors();
        } catch (\Throwable $e) {
        }
        $injectCss = '<style>
            .no-print{display:none !important;}
            body{background:#ffffff !important;}
            body *{display:none !important;}
            #print-area, #print-area *{display:block !important;}
            .w-full{width:100% !important;}
            .border-collapse{border-collapse:collapse !important;}
            .text-xs{font-size:12px !important;}
            .text-sm{font-size:13px !important;}
            .text-center{text-align:center !important;}
            .font-bold{font-weight:700 !important;}
        </style>';
        $content = $injectCss.$content;
        return response($content)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }
}

