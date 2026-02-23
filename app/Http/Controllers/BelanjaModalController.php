<?php

namespace App\Http\Controllers;

use App\Models\BelanjaModal;
use App\Models\OpdSetting;
use App\Models\NotaMaster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BelanjaModalController extends Controller
{
    public function form(Request $request): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $master = $this->loadNotaMaster();
        $data = session('belanja_modal_current') ?? [
            'tahun' => now()->year,
            'items' => [],
        ];
        if (empty($data['items'])) {
            $data['items'] = [[
                'nama_kegiatan' => '',
                'pekerjaan' => '',
                'nilai_kontrak' => 0,
                'tanggal_mulai' => '',
                'tanggal_akhir' => '',
                'uang_muka' => 0,
                'termin1' => 0,
                'termin2' => 0,
                'termin3' => 0,
                'termin4' => 0,
                'total' => 0,
                'status' => ''
            ]];
        }
        session()->forget('belanja_modal_current_id');
        return view('belanja_modal.create', compact('data', 'opd', 'master'));
    }

    public function report(Request $request): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $master = $this->loadNotaMaster();
        $tahun = (string)($request->input('tahun') ?? now()->year);
        $itemsRaw = $request->input('items', []);
        $items = is_array($itemsRaw) ? array_values(array_filter($itemsRaw, function ($it) {
            return (isset($it['nama_kegiatan']) && trim((string)$it['nama_kegiatan']) !== '') ||
                   (isset($it['pekerjaan']) && trim((string)$it['pekerjaan']) !== '');
        })) : [];
        $clean = [];
        foreach ($items as $it) {
            $nm = $it['nama_kegiatan'] ?? '';
            $pk = $it['pekerjaan'] ?? '';
            $nk = (int)preg_replace('/\D+/', '', (string)($it['nilai_kontrak'] ?? '0'));
            $tm = $it['tanggal_mulai'] ?? '';
            $ta = $it['tanggal_akhir'] ?? '';
            $um = (int)preg_replace('/\D+/', '', (string)($it['uang_muka'] ?? '0'));
            $t1 = (int)preg_replace('/\D+/', '', (string)($it['termin1'] ?? '0'));
            $t2 = (int)preg_replace('/\D+/', '', (string)($it['termin2'] ?? '0'));
            $t3 = (int)preg_replace('/\D+/', '', (string)($it['termin3'] ?? '0'));
            $t4 = (int)preg_replace('/\D+/', '', (string)($it['termin4'] ?? '0'));
            $ttl = $um + $t1 + $t2 + $t3 + $t4;
            $st = $it['status'] ?? '';
            $clean[] = compact('nm','pk','nk','tm','ta','um','t1','t2','t3','t4','ttl','st');
        }
        usort($clean, function($a, $b) {
            $da = $a['tm'] ? strtotime($a['tm']) : 0;
            $db = $b['tm'] ? strtotime($b['tm']) : 0;
            return $da <=> $db;
        });
        $data = [
            'tahun' => $tahun,
            'items' => $clean,
        ];
        $currentId = $request->input('id');
        $id = $currentId ?: (string) Str::uuid();
        $path = "users/".Auth::id()."/belanja-modal/{$id}.json";
        Storage::disk('local')->put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $sumNk = array_sum(array_map(fn($r) => (int)($r['nk'] ?? 0), $clean));
        $bm = BelanjaModal::updateOrCreate(
            ['user_id' => Auth::id(), 'dataset_id' => $id],
            ['tahun' => (int)$tahun, 'nilai_total' => $sumNk]
        );
        $bm->items()->delete();
        foreach ($clean as $row) {
            $bm->items()->create([
                'nama_kegiatan' => $row['nm'] ?? '',
                'pekerjaan' => $row['pk'] ?? '',
                'nilai_kontrak' => (int)($row['nk'] ?? 0),
                'tanggal_mulai' => $row['tm'] ?? null,
                'tanggal_akhir' => $row['ta'] ?? null,
                'uang_muka' => (int)($row['um'] ?? 0),
                'termin1' => (int)($row['t1'] ?? 0),
                'termin2' => (int)($row['t2'] ?? 0),
                'termin3' => (int)($row['t3'] ?? 0),
                'termin4' => (int)($row['t4'] ?? 0),
                'total' => (int)($row['ttl'] ?? 0),
                'status' => $row['st'] ?? '',
            ]);
        }
        session(['belanja_modal_current' => $data, 'belanja_modal_current_id' => $id]);
        return view('reports.belanja_modal_report', compact('data', 'opd', 'master'));
    }

    public function save(Request $request): RedirectResponse|View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $master = $this->loadNotaMaster();
        $tahun = (string)($request->input('tahun') ?? now()->year);
        $itemsRaw = $request->input('items', []);
        $items = is_array($itemsRaw) ? array_values(array_filter($itemsRaw, function ($it) {
            return (isset($it['nama_kegiatan']) && trim((string)$it['nama_kegiatan']) !== '') ||
                   (isset($it['pekerjaan']) && trim((string)$it['pekerjaan']) !== '');
        })) : [];
        $clean = [];
        foreach ($items as $it) {
            $nm = $it['nama_kegiatan'] ?? '';
            $pk = $it['pekerjaan'] ?? '';
            $nk = (int)preg_replace('/\D+/', '', (string)($it['nilai_kontrak'] ?? '0'));
            $tm = $it['tanggal_mulai'] ?? '';
            $ta = $it['tanggal_akhir'] ?? '';
            $um = (int)preg_replace('/\D+/', '', (string)($it['uang_muka'] ?? '0'));
            $t1 = (int)preg_replace('/\D+/', '', (string)($it['termin1'] ?? '0'));
            $t2 = (int)preg_replace('/\D+/', '', (string)($it['termin2'] ?? '0'));
            $t3 = (int)preg_replace('/\D+/', '', (string)($it['termin3'] ?? '0'));
            $t4 = (int)preg_replace('/\D+/', '', (string)($it['termin4'] ?? '0'));
            $ttl = $um + $t1 + $t2 + $t3 + $t4;
            $st = $it['status'] ?? '';
            $clean[] = compact('nm','pk','nk','tm','ta','um','t1','t2','t3','t4','ttl','st');
        }
        usort($clean, function($a, $b) {
            $da = $a['tm'] ? strtotime($a['tm']) : 0;
            $db = $b['tm'] ? strtotime($b['tm']) : 0;
            return $da <=> $db;
        });
        $data = [
            'tahun' => $tahun,
            'items' => $clean,
        ];
        $currentId = session('belanja_modal_current_id') ?? $request->input('id');
        $id = $currentId ?: (string) Str::uuid();
        $path = "users/".Auth::id()."/belanja-modal/{$id}.json";
        Storage::disk('local')->put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $sumNk = array_sum(array_map(fn($r) => (int)($r['nk'] ?? 0), $clean));
        $bm = BelanjaModal::updateOrCreate(
            ['user_id' => Auth::id(), 'dataset_id' => $id],
            ['tahun' => (int)$tahun, 'nilai_total' => $sumNk]
        );
        $bm->items()->delete();
        foreach ($clean as $row) {
            $bm->items()->create([
                'nama_kegiatan' => $row['nm'] ?? '',
                'pekerjaan' => $row['pk'] ?? '',
                'nilai_kontrak' => (int)($row['nk'] ?? 0),
                'tanggal_mulai' => $row['tm'] ?? null,
                'tanggal_akhir' => $row['ta'] ?? null,
                'uang_muka' => (int)($row['um'] ?? 0),
                'termin1' => (int)($row['t1'] ?? 0),
                'termin2' => (int)($row['t2'] ?? 0),
                'termin3' => (int)($row['t3'] ?? 0),
                'termin4' => (int)($row['t4'] ?? 0),
                'total' => (int)($row['ttl'] ?? 0),
                'status' => $row['st'] ?? '',
            ]);
        }
        session()->forget('belanja_modal_current_id');
        session()->forget('belanja_modal_current');
        return redirect()
            ->route('reports.belanja.modal.list', ['highlight' => $id])
            ->with('status', $currentId ? 'Belanja modal diperbarui' : 'Belanja modal disimpan');
    }

    public function index(): View
    {
        $disk = Storage::disk('local');
        $dir = 'users/'.Auth::id().'/belanja-modal';
        $files = $disk->exists($dir) ? $disk->files($dir) : [];
        $items = [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $json = $disk->get($file);
            $data = json_decode($json, true) ?: [];
            $total = 0;
            foreach (($data['items'] ?? []) as $row) {
                $total += (int)($row['nk'] ?? 0);
            }
            $items[] = [
                'id' => basename($file, '.json'),
                'updated' => $disk->lastModified($file),
                'tahun' => $data['tahun'] ?? '',
                'kontrak_count' => count($data['items'] ?? []),
                'nilai_total' => $total,
            ];
        }
        usort($items, fn($a, $b) => $b['updated'] <=> $a['updated']);
        return view('belanja_modal.index', compact('items'));
    }

    public function show(string $id): View
    {
        $path = "users/".Auth::id()."/belanja-modal/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true) ?: [];
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $master = $this->loadNotaMaster();
        session(['belanja_modal_current' => $data, 'belanja_modal_current_id' => $id]);
        return view('reports.belanja_modal_report', compact('data', 'opd', 'master'));
    }

    public function edit(string $id): View
    {
        $path = "users/".Auth::id()."/belanja-modal/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        $json = Storage::disk('local')->get($path);
        $saved = json_decode($json, true) ?: [];
        $items = [];
        foreach (($saved['items'] ?? []) as $it) {
            $items[] = [
                'nama_kegiatan' => $it['nm'] ?? '',
                'pekerjaan' => $it['pk'] ?? '',
                'nilai_kontrak' => $it['nk'] ?? 0,
                'tanggal_mulai' => $it['tm'] ?? '',
                'tanggal_akhir' => $it['ta'] ?? '',
                'uang_muka' => $it['um'] ?? 0,
                'termin1' => $it['t1'] ?? 0,
                'termin2' => $it['t2'] ?? 0,
                'termin3' => $it['t3'] ?? 0,
                'termin4' => $it['t4'] ?? 0,
                'total' => $it['ttl'] ?? 0,
                'status' => $it['st'] ?? '',
            ];
        }
        $prefill = [
            'tahun' => $saved['tahun'] ?? now()->year,
            'items' => $items,
        ];
        session(['belanja_modal_current' => $prefill, 'belanja_modal_current_id' => $id]);
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $master = $this->loadNotaMaster();
        $data = $prefill;
        return view('belanja_modal.edit', compact('data', 'opd', 'master'));
    }

    public function delete(string $id): RedirectResponse
    {
        $path = "users/".Auth::id()."/belanja-modal/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        Storage::disk('local')->delete($path);
        return redirect()->route('reports.belanja.modal.list')->with('status', 'Data belanja modal dihapus');
    }

    public function previewAll(): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $master = $this->loadNotaMaster();
        $disk = Storage::disk('local');
        $dir = 'users/'.Auth::id().'/belanja-modal';
        $files = $disk->exists($dir) ? $disk->files($dir) : [];
        $clean = [];
        $years = [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $json = $disk->get($file);
            $data = json_decode($json, true) ?: [];
            if (isset($data['tahun']) && is_numeric($data['tahun'])) {
                $years[] = (int)$data['tahun'];
            }
            foreach (($data['items'] ?? []) as $it) {
                $nm = $it['nm'] ?? '';
                $pk = $it['pk'] ?? '';
                $nk = (int)($it['nk'] ?? 0);
                $tm = $it['tm'] ?? '';
                $ta = $it['ta'] ?? '';
                $um = (int)($it['um'] ?? 0);
                $t1 = (int)($it['t1'] ?? 0);
                $t2 = (int)($it['t2'] ?? 0);
                $t3 = (int)($it['t3'] ?? 0);
                $t4 = (int)($it['t4'] ?? 0);
                $ttl = (int)($it['ttl'] ?? ($um + $t1 + $t2 + $t3 + $t4));
                $st = $it['st'] ?? '';
                $clean[] = compact('nm','pk','nk','tm','ta','um','t1','t2','t3','t4','ttl','st');
            }
        }
        usort($clean, function($a, $b) {
            $da = $a['tm'] ? strtotime($a['tm']) : 0;
            $db = $b['tm'] ? strtotime($b['tm']) : 0;
            return $da <=> $db;
        });
        $tahun = null;
        if (!empty($years)) {
            sort($years);
            $tahun = end($years);
        }
        $data = [
            'tahun' => $tahun ?: now()->year,
            'items' => $clean,
        ];
        session(['belanja_modal_current' => $data]);
        return view('reports.belanja_modal_report', compact('data', 'opd', 'master'));
    }

    public function export()
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $master = $this->loadNotaMaster();
        $data = session('belanja_modal_current');
        if (!$data) abort(400, 'Tidak ada data untuk diekspor');
        return $this->exportExcel('reports.belanja_modal_report', compact('data', 'opd', 'master'), 'belanja-modal.xls');
    }

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
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];
        return response($content, 200, $headers);
    }
}
