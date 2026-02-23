<?php

namespace App\Http\Controllers;

use App\Models\OpdSetting;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OpnameController extends Controller
{
    protected function buildPembuka(string $tanggal, string $opdNama): string
    {
        $dt = \Carbon\Carbon::parse($tanggal)->locale('id');
        $hari = $dt->translatedFormat('l');
        $bulan = $dt->translatedFormat('F');
        $tanggalKata = ucwords($this->toWordsId((int) $dt->format('d')));
        $tahunKata = ucwords($this->toWordsId((int) $dt->format('Y')));
        return "Pada hari ini {$hari} Tanggal {$tanggalKata} Bulan {$bulan} Tahun {$tahunKata}, bertempat di {$opdNama} Kabupaten Bolaang Mongondow Selatan, yang bertanda tangan dibawah ini:";
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

    protected function prefillOpnameItemsByDate(string $date): array
    {
        $items = [];
        $products = Product::orderBy('name')->get();
        foreach ($products as $p) {
            $in = \App\Models\Transaction::where('product_id', $p->id)
                ->where('type', 'in')
                ->whereDate('date', '<=', $date)
                ->sum('quantity');
            $out = \App\Models\Transaction::where('product_id', $p->id)
                ->where('type', 'out')
                ->whereDate('date', '<=', $date)
                ->sum('quantity');
            $qty = (int) ($in - $out);
            if ($qty <= 0) continue;
            $price = (int) ($p->price ?? 0);
            $items[] = [
                'nama' => $p->name,
                'kuantitas' => $qty,
                'satuan' => $p->unit ?? '',
                'harga' => $price,
                'jumlah' => $qty * $price,
                'kondisi' => 'B',
            ];
        }
        return $items;
    }

    public function form(Request $request): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $data = session('opname_current');
        if (! $data) {
            $data = [
                'tanggal' => now()->toDateString(),
                'items' => $this->prefillOpnameItemsByDate(now()->toDateString()),
                'tempat' => $opd->nama_opd ?? '',
            ];
        }
        return view('opname.create', compact('data', 'opd'));
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
            'items.*.kuantitas' => 'required|integer|min:1',
            'items.*.satuan' => 'nullable|string|max:50',
            'items.*.harga' => 'nullable|integer|min:0',
            'items.*.jumlah' => 'nullable|integer|min:0',
            'items.*.kondisi' => 'nullable|string|max:3',
        ]);
        $validated['items'] = $this->prefillOpnameItemsByDate($validated['tanggal']);
        $validated['user_id'] = Auth::id();
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        if (empty($validated['pembuka'])) {
            $opdNama = $opd->nama_opd ?? ($validated['tempat'] ?? '-');
            $validated['pembuka'] = $this->buildPembuka($validated['tanggal'], $opdNama);
        }
        session(['opname_current' => $validated]);
        return view('reports.opname_report', [
            'data' => $validated,
            'opd' => $opd,
        ]);
    }

    public function save(Request $request): RedirectResponse|View
    {
        $data = session('opname_current') ?? $request->all();
        $currentId = session('opname_current_id') ?? $request->input('id');
        $disk = Storage::disk('local');
        $userDir = 'users/'.Auth::id().'/opname';
        $files = $disk->exists($userDir) ? $disk->files($userDir) : [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $json = $disk->get($file);
            $existing = json_decode($json, true) ?: [];
            $fid = basename($file, '.json');
            if ($fid === $currentId) {
                continue;
            }
            if (($existing['nomor'] ?? null) === ($data['nomor'] ?? null)) {
                return view('reports.opname_report', [
                    'data' => $data,
                    'error' => 'Nomor berita acara sudah ada. Tidak bisa menyimpan.',
                ]);
            }
        }
        $id = $currentId ?: (string) Str::uuid();
        $path = "{$userDir}/{$id}.json";
        $disk->put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        session()->forget('opname_current_id');
        return redirect()->route('reports.opname.list')
            ->with('status', $currentId ? 'Berita acara berhasil diperbarui' : 'Berita acara berhasil disimpan');
    }

    public function edit(string $id): View
    {
        $path = "users/".Auth::id()."/opname/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true) ?: [];
        session([
            'opname_current' => $data,
            'opname_current_id' => $id,
        ]);
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        return view('opname.edit', compact('data', 'opd'));
    }

    public function list(): View
    {
        $disk = Storage::disk('local');
        $files = $disk->files('users/'.Auth::id().'/opname');
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
        return view('opname.index', compact('items'));
    }

    public function show(string $id): View
    {
        $path = "users/".Auth::id()."/opname/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true) ?: [];
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        if (empty($data['pembuka'])) {
            $opdNama = $opd->nama_opd ?? ($data['tempat'] ?? '-');
            $data['pembuka'] = $this->buildPembuka($data['tanggal'] ?? now()->toDateString(), $opdNama);
        }
        session(['opname_current' => $data, 'opname_current_id' => $id]);
        return view('reports.opname_report', [
            'data' => $data,
            'saved_id' => $id,
            'opd' => $opd,
        ]);
    }

    public function prefill(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $items = $this->prefillOpnameItemsByDate($date);
        return response()->json(['items' => $items]);
    }

    public function delete(string $id): View
    {
        $disk = Storage::disk('local');
        $path = "users/".Auth::id()."/opname/{$id}.json";
        if ($disk->exists($path)) {
            $disk->delete($path);
            $status = 'Berita acara berhasil dihapus';
        } else {
            $status = 'Berita acara tidak ditemukan';
        }
        $files = $disk->files('users/'.Auth::id().'/opname');
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
        return view('opname.index', [
            'items' => $items,
            'status' => $status,
        ]);
    }
}

