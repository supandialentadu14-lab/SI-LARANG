<?php

namespace App\Http\Controllers;

use App\Models\OpdSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PinjamPakaiController extends Controller
{
    public function form(): View
    {
        session()->forget('pinjam_pakai_current');
        session()->forget('pinjam_pakai_current_id');
        $data = null;
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        return view('pinjam_pakai.create', compact('data', 'opd'));
    }

    public function report(Request $request): View
    {
        $validated = $request->validate([
            'nomor' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'tempat' => 'required|string|max:255',
            'pembuka' => 'nullable|string',
            'pihak_pertama.nama' => 'required|string|max:255',
            'pihak_pertama.nip' => 'nullable|string|max:50',
            'pihak_pertama.jabatan' => 'required|string|max:255',
            'pihak_kedua.nama' => 'required|string|max:255',
            'pihak_kedua.nip' => 'nullable|string|max:50',
            'pihak_kedua.jabatan' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.nama' => 'required|string|max:255',
            'items.*.merk' => 'nullable|string|max:255',
            'items.*.tipe' => 'nullable|string|max:255',
            'items.*.identitas' => 'nullable|string|max:255',
            'items.*.tahun' => 'nullable|string|max:10',
            'items.*.kondisi' => 'nullable|string|max:50',
            'items.*.jumlah' => 'required|integer|min:1',
            'ketentuan' => 'nullable|string',
            'berlaku_hingga' => 'nullable|string',
        ]);
        $validated['user_id'] = Auth::id();
        session(['pinjam_pakai_current' => $validated]);
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        return view('reports.pinjam_pakai_report', [
            'data' => $validated,
            'opd' => $opd,
        ]);
    }

    public function save(Request $request): View|RedirectResponse
    {
        if ($request->has('items')) {
            $validated = $request->validate([
                'nomor' => 'required|string|max:100',
                'tanggal' => 'required|date',
                'tempat' => 'required|string|max:255',
                'pembuka' => 'nullable|string',
                'pihak_pertama.nama' => 'required|string|max:255',
                'pihak_pertama.nip' => 'nullable|string|max:50',
                'pihak_pertama.jabatan' => 'required|string|max:255',
                'pihak_kedua.nama' => 'required|string|max:255',
                'pihak_kedua.nip' => 'nullable|string|max:50',
                'pihak_kedua.jabatan' => 'required|string|max:255',
                'items' => 'required|array|min:1',
                'items.*.nama' => 'required|string|max:255',
                'items.*.merk' => 'nullable|string|max:255',
                'items.*.tipe' => 'nullable|string|max:255',
                'items.*.identitas' => 'nullable|string|max:255',
                'items.*.tahun' => 'nullable|string|max:10',
                'items.*.kondisi' => 'nullable|string|max:50',
                'items.*.jumlah' => 'required|integer|min:1',
                'ketentuan' => 'nullable|string',
                'berlaku_hingga' => 'nullable|string',
            ]);
            $validated['user_id'] = Auth::id();
            $data = $validated;
            session(['pinjam_pakai_current' => $data]);
        } else {
            $data = session('pinjam_pakai_current') ?: $request->all();
        }
        $currentId = session('pinjam_pakai_current_id') ?? $request->input('id');
        $disk = Storage::disk('local');
        $userDir = 'users/'.Auth::id().'/pinjam_pakai';
        $files = $disk->exists($userDir) ? $disk->files($userDir) : [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $json = $disk->get($file);
            $existing = json_decode($json, true) ?: [];
            $fid = basename($file, '.json');
            if ($fid === $currentId) continue;
            if (($existing['nomor'] ?? null) === ($data['nomor'] ?? null)) {
                return view('reports.pinjam_pakai_report', [
                    'data' => $data,
                    'error' => 'Nomor berita acara sudah ada. Tidak bisa menyimpan.',
                ]);
            }
        }
        $id = $currentId ?: (string) Str::uuid();
        $path = "{$userDir}/{$id}.json";
        if (! $disk->exists($userDir)) {
            $disk->makeDirectory($userDir);
        }
        $disk->put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        session()->forget('pinjam_pakai_current_id');
        return redirect()->route('reports.pinjam.list')
            ->with('status', $currentId ? 'Berita acara berhasil diperbarui' : 'Berita acara berhasil disimpan');
    }

    public function edit(string $id): View
    {
        $path = "users/".Auth::id()."/pinjam_pakai/{$id}.json";
        if (! Storage::disk('local')->exists($path)) abort(404);
        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true) ?: [];
        if (isset($data['items']) && is_array($data['items'])) {
            $seen = [];
            $unique = [];
            foreach ($data['items'] as $row) {
                $key = trim($row['nama'] ?? '').'|'.trim($row['merk'] ?? '').'|'.trim($row['tipe'] ?? '').'|'.trim($row['identitas'] ?? '').'|'.(string)($row['tahun'] ?? '').'|'.trim($row['kondisi'] ?? '').'|'.(string)($row['jumlah'] ?? '');
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $unique[] = $row;
                }
            }
            $data['items'] = $unique;
        }
        session([
            'pinjam_pakai_current' => $data,
            'pinjam_pakai_current_id' => $id,
        ]);
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        return view('pinjam_pakai.edit', compact('data', 'opd'));
    }

    public function list(): View
    {
        $disk = Storage::disk('local');
        $userDir = 'users/'.Auth::id().'/pinjam_pakai';
        $files = $disk->exists($userDir) ? $disk->files($userDir) : [];
        $items = [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $json = $disk->get($file);
            $data = json_decode($json, true) ?: [];
            $items[] = [
                'id' => basename($file, '.json'),
                'updated' => $disk->lastModified($file),
                'nomor' => $data['nomor'] ?? '',
                'tanggal' => $data['tanggal'] ?? '',
                'tempat' => $data['tempat'] ?? '',
                'pihak_pertama' => $data['pihak_pertama']['nama'] ?? '',
                'pihak_kedua' => $data['pihak_kedua']['nama'] ?? '',
            ];
        }
        usort($items, fn($a, $b) => $b['updated'] <=> $a['updated']);
        return view('pinjam_pakai.index', compact('items'));
    }

    public function show(string $id): View
    {
        $path = "users/".Auth::id()."/pinjam_pakai/{$id}.json";
        if (! Storage::disk('local')->exists($path)) abort(404);
        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true) ?: [];
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        session(['pinjam_pakai_current' => $data, 'pinjam_pakai_current_id' => $id]);
        return view('reports.pinjam_pakai_report', [
            'data' => $data,
            'saved_id' => $id,
            'opd' => $opd,
        ]);
    }

    public function delete(string $id): RedirectResponse
    {
        $disk = Storage::disk('local');
        $path = "users/".Auth::id()."/pinjam_pakai/{$id}.json";
        if ($disk->exists($path)) {
            $disk->delete($path);
            $status = 'Berita acara berhasil dihapus';
        } else {
            $status = 'Berita acara tidak ditemukan';
        }
        return redirect()->route('reports.pinjam.list')->with('status', $status);
    }

    public function export()
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $data = session('pinjam_pakai_current');
        if (!$data) abort(400, 'Tidak ada data untuk diekspor');
        return $this->exportExcel('reports.pinjam_pakai_report', ['data' => $data, 'opd' => $opd], 'pinjam-pakai.xls');
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
