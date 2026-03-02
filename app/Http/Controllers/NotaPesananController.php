<?php

namespace App\Http\Controllers;

use App\Models\NotaItem;
use App\Models\NotaMaster;
use App\Models\NotaPesanan;
use App\Models\OpdSetting;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class NotaPesananController extends Controller
{
    protected function collectNotaOptions(): array
    {
        $disk = Storage::disk('local');
        $dir = 'users/'.Auth::id().'/nota-pesanan';
        $files = $disk->exists($dir) ? $disk->files($dir) : [];
        $opt = [
            'kegiatan' => [],
            'sub_kegiatan' => [],
            'rekening' => [],
            'pejabat' => [],
            'pptk' => [],
            'pptk_nip' => [],
            'pengurus_barang' => [],
            'pengurus_pengguna' => [],
            'ppk' => [],
            'penyedia' => [],
            'bendahara' => [],
            'bendahara_nip' => [],
        ];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $data = json_decode($disk->get($file), true) ?: [];
            $push = function (&$list, $val) {
                $val = is_string($val) ? trim($val) : $val;
                if (is_string($val) && $val !== '' && !in_array($val, $list, true)) {
                    $list[] = $val;
                }
            };
            $push($opt['kegiatan'], $data['kegiatan'] ?? null);
            $push($opt['sub_kegiatan'], $data['sub_kegiatan'] ?? null);
            $push($opt['rekening'], $data['rekening'] ?? null);
            $push($opt['pejabat'], $data['pejabat']['nama'] ?? null);
            $push($opt['pptk'], $data['pptk']['nama'] ?? null);
            $push($opt['pptk_nip'], $data['pptk']['nip'] ?? null);
            $push($opt['pengurus_barang'], $data['pengurus_barang']['nama'] ?? null);
            $push($opt['pengurus_pengguna'], $data['pengurus_pengguna']['nama'] ?? null);
            $push($opt['ppk'], $data['ppk']['nama'] ?? null);
            $push($opt['bendahara'], $data['bendahara']['nama'] ?? null);
            $push($opt['bendahara_nip'], $data['bendahara']['nip'] ?? null);
            $push($opt['penyedia'], $data['penyedia']['toko'] ?? null);
        }
        return $opt;
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

    public function form(Request $request): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $options = $this->collectNotaOptions();
        $products = Product::with('category')->orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $master = $this->loadNotaMaster();
        $suppliers = Supplier::orderBy('name')->get();
        session()->forget('nota_current');
        session()->forget('nota_current_id');
        $data = [
            'tanggal' => now()->toDateString(),
            'tahun' => now()->year,
            'items' => [],
        ];
        return view('nota_pesanan.create', compact('data', 'opd', 'options', 'products', 'categories', 'master', 'suppliers'));
    }

    public function report(Request $request): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $payload = $request->all();
        $itemsRaw = $request->input('items', []);
        $items = is_array($itemsRaw) ? array_values(array_filter($itemsRaw, function ($it) {
            return isset($it['name']) && trim((string)$it['name']) !== '';
        })) : [];
        $cleanItems = [];
        foreach ($items as $it) {
            $name = $it['name'] ?? '';
            $qty = (int)preg_replace('/\D+/', '', (string)($it['qty'] ?? '0'));
            $unit = $it['unit'] ?? '';
            $price = (int)preg_replace('/\D+/', '', (string)($it['price'] ?? '0'));
            $total = $qty * $price;
            $cleanItems[] = compact('name','qty','unit','price','total');
        }
        $master = $this->loadNotaMaster();
        if (is_array($master)) {
            $master['pejabat']['nama'] = $payload['pejabat_nama'] ?? ($master['pejabat']['nama'] ?? '');
            $master['pptk']['nama'] = $payload['pptk_nama'] ?? ($master['pptk']['nama'] ?? '');
            $master['pengurus_barang']['nama'] = $payload['pb_nama'] ?? ($master['pengurus_barang']['nama'] ?? '');
            $master['pengurus_pengguna']['nama'] = $payload['pbp_nama'] ?? ($master['pengurus_pengguna']['nama'] ?? '');
            $master['ppk']['nama'] = $payload['ppk_nama'] ?? ($master['ppk']['nama'] ?? '');
            $master['bendahara']['nama'] = $payload['bendahara_nama'] ?? ($master['bendahara']['nama'] ?? '');
        }
        $penyedia = ['toko' => '', 'pemilik' => '', 'alamat' => ''];
        if ($sid = $request->input('supplier_id')) {
            $sup = Supplier::find($sid);
            if ($sup) {
                $penyedia = [
                    'toko' => $sup->name,
                    'pemilik' => $sup->dir,
                    'alamat' => $sup->address,
                ];
            }
        } else {
            $existing = session('nota_current') ?: [];
            if (!empty($existing['penyedia'])) {
                $penyedia = $existing['penyedia'];
            }
        }
        $data = [
            'kegiatan' => $payload['kegiatan'] ?? '',
            'sub_kegiatan' => $payload['sub_kegiatan'] ?? '',
            'rekening' => $payload['rekening'] ?? '',
            'tahun' => $payload['tahun'] ?? now()->year,
            'pejabat' => $master['pejabat'],
            'pptk' => $master['pptk'],
            'pengurus_barang' => $master['pengurus_barang'],
            'pengurus_pengguna' => $master['pengurus_pengguna'],
            'ppk' => $master['ppk'],
            'penyedia' => $penyedia,
            'bendahara' => $master['bendahara'],
            'nomor' => $payload['nomor'] ?? '',
            'tanggal' => $payload['tanggal'] ?? now()->toDateString(),
            'belanja' => $payload['belanja'] ?? '',
            'items' => $cleanItems,
        ];
        session(['nota_current' => $data]);
        return view('reports.nota_pesanan_report', compact('data', 'opd'));
    }

    protected function getSafeFilename(string $nomor): string
    {
        // Ganti karakter tidak aman dengan dash, tapi pertahankan keterbacaan
        // Ganti slash, backslash, spasi dengan dash
        $safe = str_replace(['/', '\\', ' '], ['-', '-', '-'], $nomor);
        // Hapus karakter lain yang tidak aman untuk filesystem (tetap izinkan alfanumerik, dash, underscore, titik)
        $safe = preg_replace('/[^a-zA-Z0-9\-\_\.]/', '', $safe);
        // Pastikan tidak kosong (fallback ke uuid jika nomor aneh/kosong)
        return $safe ?: (string) Str::uuid();
    }

    protected function formatRomawi(int $number): string
    {
        $map = [
            'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
            'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
            'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
        ];
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    public function save(Request $request): RedirectResponse
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $payload = $request->all();
        $itemsRaw = $request->input('items', []);
        $items = is_array($itemsRaw) ? array_values(array_filter($itemsRaw, function ($it) {
            return isset($it['name']) && trim((string)$it['name']) !== '';
        })) : [];
        $cleanItems = [];
        foreach ($items as $it) {
            $name = $it['name'] ?? '';
            $qty = (int)preg_replace('/\D+/', '', (string)($it['qty'] ?? '0'));
            $unit = $it['unit'] ?? '';
            $price = (int)preg_replace('/\D+/', '', (string)($it['price'] ?? '0'));
            $total = $qty * $price;
            $cleanItems[] = compact('name','qty','unit','price','total');
        }
        $master = $this->loadNotaMaster();
        if (is_array($master)) {
            $master['pejabat']['nama'] = $payload['pejabat_nama'] ?? ($master['pejabat']['nama'] ?? '');
            $master['pptk']['nama'] = $payload['pptk_nama'] ?? ($master['pptk']['nama'] ?? '');
            $master['pengurus_barang']['nama'] = $payload['pb_nama'] ?? ($master['pengurus_barang']['nama'] ?? '');
            $master['pengurus_pengguna']['nama'] = $payload['pbp_nama'] ?? ($master['pengurus_pengguna']['nama'] ?? '');
            $master['ppk']['nama'] = $payload['ppk_nama'] ?? ($master['ppk']['nama'] ?? '');
            $master['bendahara']['nama'] = $payload['bendahara_nama'] ?? ($master['bendahara']['nama'] ?? '');
        }
        $penyedia = ['toko' => '', 'pemilik' => '', 'alamat' => ''];
        if ($sid = $request->input('supplier_id')) {
            $sup = Supplier::find($sid);
            if ($sup) {
                $penyedia = [
                    'toko' => $sup->name,
                    'pemilik' => $sup->dir,
                    'alamat' => $sup->address,
                ];
            }
        } else {
            $existing = session('nota_current') ?: [];
            if (!empty($existing['penyedia'])) {
                $penyedia = $existing['penyedia'];
            }
        }
        
        $tanggalObj = \Carbon\Carbon::parse($payload['tanggal'] ?? now()->toDateString());
        $tahunAnggaran = $payload['tahun'] ?? now()->year;
        
        // Format Nomor Nota Otomatis: [Input]/NPB/KOMINFO/[BulanRomawi]/[Tahun]
        $inputNomor = trim((string)($payload['nomor'] ?? ''));
        // Jika input hanya angka, format ulang
        if (preg_match('/^\d+$/', $inputNomor)) {
            $bulanRomawi = $this->formatRomawi($tanggalObj->month);
            $nomorFormatted = "{$inputNomor}/NPB/KOMINFO/{$bulanRomawi}/{$tahunAnggaran}";
        } else {
            // Jika sudah ada format atau kosong, gunakan apa adanya
            $nomorFormatted = $inputNomor;
        }

        $data = [
            'kegiatan' => $payload['kegiatan'] ?? '',
            'sub_kegiatan' => $payload['sub_kegiatan'] ?? '',
            'rekening' => $payload['rekening'] ?? '',
            'tahun' => $tahunAnggaran,
            'pejabat' => $master['pejabat'],
            'pptk' => $master['pptk'],
            'pengurus_barang' => $master['pengurus_barang'],
            'pengurus_pengguna' => $master['pengurus_pengguna'],
            'ppk' => $master['ppk'],
            'penyedia' => $penyedia,
            'bendahara' => $master['bendahara'],
            'nomor' => $nomorFormatted,
            'tanggal' => $payload['tanggal'] ?? now()->toDateString(),
            'belanja' => $payload['belanja'] ?? '',
            'items' => $cleanItems,
        ];
        
        // Gunakan nomor sebagai ID file agar unik
        $id = $this->getSafeFilename($data['nomor']);
        Storage::disk('local')->put("users/".Auth::id()."/nota-pesanan/{$id}.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $grand = array_sum(array_map(fn($r) => (int) ($r['total'] ?? 0), $cleanItems));
        $terbilang = $this->toWordsIdInternal($grand);
        $nota = NotaPesanan::updateOrCreate(
            ['user_id' => Auth::id(), 'nomor' => $data['nomor']],
            [
                'tanggal' => $data['tanggal'],
                'kegiatan' => $data['kegiatan'],
                'sub_kegiatan' => $data['sub_kegiatan'],
                'rekening' => $data['rekening'],
                'tahun' => (int) $data['tahun'],
                'belanja' => $data['belanja'],
                'penyedia_toko' => $data['penyedia']['toko'] ?? '',
                'penyedia_pemilik' => $data['penyedia']['pemilik'] ?? '',
                'penyedia_alamat' => $data['penyedia']['alamat'] ?? '',
                'pejabat_nama' => $data['pejabat']['nama'] ?? '',
                'pejabat_nip' => $data['pejabat']['nip'] ?? '',
                'pptk_nama' => $data['pptk']['nama'] ?? '',
                'pptk_nip' => $data['pptk']['nip'] ?? '',
                'ppk_nama' => $data['ppk']['nama'] ?? '',
                'ppk_nip' => $data['ppk']['nip'] ?? '',
                'bendahara_nama' => $data['bendahara']['nama'] ?? '',
                'bendahara_nip' => $data['bendahara']['nip'] ?? '',
                'total' => $grand,
                'terbilang' => $terbilang,
            ]
        );
        $nota->items()->delete();
        foreach ($cleanItems as $row) {
            $nota->items()->create([
                'name' => $row['name'],
                'qty' => (int) $row['qty'],
                'unit' => $row['unit'],
                'price' => (int) $row['price'],
                'total' => (int) $row['total'],
            ]);
        }
        
        session()->forget('nota_current_id');
        return redirect()->route('reports.nota.list')->with('status', 'Nota pesanan disimpan');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $payload = $request->all();
        $itemsRaw = $request->input('items', []);
        $items = is_array($itemsRaw) ? array_values(array_filter($itemsRaw, function ($it) {
            return isset($it['name']) && trim((string)$it['name']) !== '';
        })) : [];
        $cleanItems = [];
        foreach ($items as $it) {
            $name = $it['name'] ?? '';
            $qty = (int)preg_replace('/\D+/', '', (string)($it['qty'] ?? '0'));
            $unit = $it['unit'] ?? '';
            $price = (int)preg_replace('/\D+/', '', (string)($it['price'] ?? '0'));
            $total = $qty * $price;
            $cleanItems[] = compact('name','qty','unit','price','total');
        }
        $master = $this->loadNotaMaster();
        $penyedia = ['toko' => '', 'pemilik' => '', 'alamat' => ''];
        if ($sid = $request->input('supplier_id')) {
            $sup = Supplier::find($sid);
            if ($sup) {
                $penyedia = [
                    'toko' => $sup->name,
                    'pemilik' => $sup->dir,
                    'alamat' => $sup->address,
                ];
            }
        } else {
            $existing = session('nota_current') ?: [];
            if (!empty($existing['penyedia'])) {
                $penyedia = $existing['penyedia'];
            }
        }
        
        $tanggalObj = \Carbon\Carbon::parse($payload['tanggal'] ?? now()->toDateString());
        $tahunAnggaran = $payload['tahun'] ?? now()->year;
        
        // Format Nomor Nota Otomatis: [Input]/NPB/KOMINFO/[BulanRomawi]/[Tahun]
        $inputNomor = trim((string)($payload['nomor'] ?? ''));
        // Jika input hanya angka, format ulang
        if (preg_match('/^\d+$/', $inputNomor)) {
            $bulanRomawi = $this->formatRomawi($tanggalObj->month);
            $nomorFormatted = "{$inputNomor}/NPB/KOMINFO/{$bulanRomawi}/{$tahunAnggaran}";
        } else {
            // Jika sudah ada format atau kosong, gunakan apa adanya
            $nomorFormatted = $inputNomor;
        }

        $data = [
            'kegiatan' => $payload['kegiatan'] ?? '',
            'sub_kegiatan' => $payload['sub_kegiatan'] ?? '',
            'rekening' => $payload['rekening'] ?? '',
            'tahun' => $tahunAnggaran,
            'pejabat' => $master['pejabat'],
            'pptk' => $master['pptk'],
            'pengurus_barang' => $master['pengurus_barang'],
            'pengurus_pengguna' => $master['pengurus_pengguna'],
            'ppk' => $master['ppk'],
            'penyedia' => $penyedia,
            'bendahara' => $master['bendahara'],
            'nomor' => $nomorFormatted,
            'tanggal' => $payload['tanggal'] ?? now()->toDateString(),
            'belanja' => $payload['belanja'] ?? '',
            'items' => $cleanItems,
        ];
        
        // Gunakan nomor sebagai ID baru
        $newId = $this->getSafeFilename($data['nomor']);
        
        // Jika ID berubah (nomor berubah), hapus file lama
        if ($newId !== $id) {
            if (Storage::disk('local')->exists("users/".Auth::id()."/nota-pesanan/{$id}.json")) {
                Storage::disk('local')->delete("users/".Auth::id()."/nota-pesanan/{$id}.json");
            }
        }
        
        Storage::disk('local')->put("users/".Auth::id()."/nota-pesanan/{$newId}.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $grand = array_sum(array_map(fn($r) => (int) ($r['total'] ?? 0), $cleanItems));
        $terbilang = $this->toWordsIdInternal($grand);
        $prev = session('nota_current') ?: [];
        $nota = NotaPesanan::where('user_id', Auth::id())->where('nomor', $prev['nomor'] ?? '')->first();
        if (! $nota) {
            $nota = NotaPesanan::updateOrCreate(['user_id' => Auth::id(), 'nomor' => $data['nomor']], []);
        }
        $nota->fill([
            'tanggal' => $data['tanggal'],
            'kegiatan' => $data['kegiatan'],
            'sub_kegiatan' => $data['sub_kegiatan'],
            'rekening' => $data['rekening'],
            'tahun' => (int) $data['tahun'],
            'belanja' => $data['belanja'],
            'penyedia_toko' => $data['penyedia']['toko'] ?? '',
            'penyedia_pemilik' => $data['penyedia']['pemilik'] ?? '',
            'penyedia_alamat' => $data['penyedia']['alamat'] ?? '',
            'pejabat_nama' => $data['pejabat']['nama'] ?? '',
            'pejabat_nip' => $data['pejabat']['nip'] ?? '',
            'pptk_nama' => $data['pptk']['nama'] ?? '',
            'pptk_nip' => $data['pptk']['nip'] ?? '',
            'ppk_nama' => $data['ppk']['nama'] ?? '',
            'ppk_nip' => $data['ppk']['nip'] ?? '',
            'bendahara_nama' => $data['bendahara']['nama'] ?? '',
            'bendahara_nip' => $data['bendahara']['nip'] ?? '',
            'total' => $grand,
            'terbilang' => $terbilang,
            'nomor' => $data['nomor'],
        ])->save();
        $nota->items()->delete();
        foreach ($cleanItems as $row) {
            $nota->items()->create([
                'name' => $row['name'],
                'qty' => (int) $row['qty'],
                'unit' => $row['unit'],
                'price' => (int) $row['price'],
                'total' => (int) $row['total'],
            ]);
        }
        session()->forget('nota_current_id');
        return redirect()->route('reports.nota.list')->with('status', 'Nota pesanan diperbarui');
    }

    public function list(Request $request): View
    {
        $disk = Storage::disk('local');
        $dir = 'users/'.Auth::id().'/nota-pesanan';
        $files = $disk->exists($dir) ? $disk->files($dir) : [];
        $items = [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $json = $disk->get($file);
            $data = json_decode($json, true) ?: [];
            $total = 0;
            foreach (($data['items'] ?? []) as $row) {
                $total += (int)($row['total'] ?? 0);
            }
            $items[] = [
                'id' => basename($file, '.json'),
                'updated' => $disk->lastModified($file),
                'nomor' => $data['nomor'] ?? '',
                'tanggal' => $data['tanggal'] ?? '',
                'belanja' => $data['belanja'] ?? '',
                'total' => $total,
            ];
        }
        usort($items, fn($a, $b) => $b['updated'] <=> $a['updated']);

        $page = $request->input('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;
        $itemsForCurrentPage = array_slice($items, $offset, $perPage);
        $items = new LengthAwarePaginator(
            $itemsForCurrentPage,
            count($items),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('nota_pesanan.index', compact('items'));
    }

    public function show(string $id): View
    {
        $path = "users/".Auth::id()."/nota-pesanan/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true) ?: [];
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        session(['nota_current' => $data, 'nota_current_id' => $id]);
        return view('reports.nota_pesanan_report', compact('data', 'opd'));
    }

    public function edit(string $id): View
    {
        $path = "users/".Auth::id()."/nota-pesanan/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true) ?: [];
        $items = ($data['items'] ?? []);
        $belanja = (string)($data['belanja'] ?? '');
        $allowedNames = [];
        $productById = [];
        try {
            $allProducts = \App\Models\Product::with('category')->get();
            $allowedNames = $allProducts
                ->filter(fn($p) => (optional($p->category)->name ?? '') === $belanja)
                ->map(fn($p) => (string)$p->name)
                ->all();
            $productById = $allProducts
                ->mapWithKeys(fn($p) => [(string)$p->id => (string)$p->name])
                ->all();
        } catch (\Throwable $e) {
            $allowedNames = [];
            $productById = [];
        }
        $unique = [];
        foreach ($items as $it) {
            $name = trim((string)($it['name'] ?? ''));
            $pid = trim((string)($it['product_id'] ?? ''));
            if ($name === '' && $pid !== '' && isset($productById[$pid])) {
                $name = $productById[$pid];
            }
            if ($name !== '' && preg_match('/^\d+$/', $name) && isset($productById[$name])) {
                $name = $productById[$name];
            }
            $qty = (int)($it['qty'] ?? 0);
            $price = (int)($it['price'] ?? 0);
            $unit = trim((string)($it['unit'] ?? ''));
            if ($name === '' || preg_match('/^\d+$/', $name)) {
                continue;
            }
            if (!empty($allowedNames) && !in_array($name, $allowedNames, true)) {
                continue;
            }
            $key = strtolower($name) . '|' . $qty . '|' . strtolower($unit) . '|' . $price;
            if (!isset($unique[$key])) {
                $it['name'] = $name;
                $unique[$key] = $it;
            }
        }
        $data['items'] = array_values($unique);
        Storage::disk('local')->put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        session([
            'nota_current' => $data,
            'nota_current_id' => $id,
        ]);
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $options = $this->collectNotaOptions();
        $products = Product::with('category')->orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $master = $this->loadNotaMaster();
        $suppliers = Supplier::orderBy('name')->get();
        return view('nota_pesanan.edit', compact('data', 'opd', 'options', 'products', 'categories', 'master', 'suppliers'));
    }

    public function delete(string $id): RedirectResponse
    {
        $disk = Storage::disk('local');
        $userId = Auth::id();
        $notaPath = "users/{$userId}/nota-pesanan/{$id}.json";
        $notaNomor = null;
        if ($disk->exists($notaPath)) {
            $json = $disk->get($notaPath);
            $data = json_decode($json, true) ?: [];
            $notaNomor = $data['nomor'] ?? null;
            $disk->delete($notaPath);
            
            // Delete database record as well
            if ($notaNomor) {
                $dbNota = NotaPesanan::where('user_id', $userId)->where('nomor', $notaNomor)->first();
                if ($dbNota) {
                    $dbNota->items()->delete();
                    $dbNota->delete();
                }
            }
        }
        if ($notaNomor) {
            $pemeriksaanDir = "users/{$userId}/bap-pemeriksaan";
            $pemeriksaanFiles = $disk->exists($pemeriksaanDir) ? $disk->files($pemeriksaanDir) : [];
            foreach ($pemeriksaanFiles as $file) {
                if (! str_ends_with($file, '.json')) continue;
                $doc = json_decode($disk->get($file), true) ?: [];
                $docNotaNomor = $doc['nota']['nomor'] ?? null;
                if ($docNotaNomor && trim($docNotaNomor) === trim($notaNomor)) {
                    $disk->delete($file);
                }
            }
            $penerimaanDir = "users/{$userId}/bap-penerimaan";
            $penerimaanFiles = $disk->exists($penerimaanDir) ? $disk->files($penerimaanDir) : [];
            foreach ($penerimaanFiles as $file) {
                if (! str_ends_with($file, '.json')) continue;
                $doc = json_decode($disk->get($file), true) ?: [];
                $docNotaNomor = $doc['nota']['nomor'] ?? null;
                if ($docNotaNomor && trim($docNotaNomor) === trim($notaNomor)) {
                    $disk->delete($file);
                }
            }
        }
        return redirect()->route('reports.nota.list')->with('status', 'Nota pesanan dan dokumen terkait dihapus');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        $count = 0;
        $disk = Storage::disk('local');
        $userId = Auth::id();
        
        foreach ($ids as $id) {
            $notaPath = "users/{$userId}/nota-pesanan/{$id}.json";
            $notaNomor = null;
            
            if ($disk->exists($notaPath)) {
                $json = $disk->get($notaPath);
                $data = json_decode($json, true) ?: [];
                $notaNomor = $data['nomor'] ?? null;
                $disk->delete($notaPath);
                
                // Delete database record
                if ($notaNomor) {
                    $dbNota = NotaPesanan::where('user_id', $userId)->where('nomor', $notaNomor)->first();
                    if ($dbNota) {
                        $dbNota->items()->delete();
                        $dbNota->delete();
                    }
                }
                
                $count++;
            }
            
            if ($notaNomor) {
                $pemeriksaanDir = "users/{$userId}/bap-pemeriksaan";
                $pemeriksaanFiles = $disk->exists($pemeriksaanDir) ? $disk->files($pemeriksaanDir) : [];
                foreach ($pemeriksaanFiles as $file) {
                    if (! str_ends_with($file, '.json')) continue;
                    $doc = json_decode($disk->get($file), true) ?: [];
                    $docNotaNomor = $doc['nota']['nomor'] ?? null;
                    if ($docNotaNomor && trim($docNotaNomor) === trim($notaNomor)) {
                        $disk->delete($file);
                    }
                }
                $penerimaanDir = "users/{$userId}/bap-penerimaan";
                $penerimaanFiles = $disk->exists($penerimaanDir) ? $disk->files($penerimaanDir) : [];
                foreach ($penerimaanFiles as $file) {
                    if (! str_ends_with($file, '.json')) continue;
                    $doc = json_decode($disk->get($file), true) ?: [];
                    $docNotaNomor = $doc['nota']['nomor'] ?? null;
                    if ($docNotaNomor && trim($docNotaNomor) === trim($notaNomor)) {
                        $disk->delete($file);
                    }
                }
            }
        }
        return redirect()->route('reports.nota.list')->with('status', "{$count} Nota pesanan dan dokumen terkait dihapus");
    }



    protected function toWordsIdInternal(int $value): string
    {
        $huruf = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        if ($value < 12) return $huruf[$value];
        if ($value < 20) return $this->toWordsIdInternal($value - 10) . ' belas';
        if ($value < 100) return $this->toWordsIdInternal(intval($value / 10)) . ' puluh ' . $this->toWordsIdInternal($value % 10);
        if ($value < 200) return 'seratus ' . $this->toWordsIdInternal($value - 100);
        if ($value < 1000) return $this->toWordsIdInternal(intval($value / 100)) . ' ratus ' . $this->toWordsIdInternal($value % 100);
        if ($value < 2000) return 'seribu ' . $this->toWordsIdInternal($value - 1000);
        if ($value < 1000000) return $this->toWordsIdInternal(intval($value / 1000)) . ' ribu ' . $this->toWordsIdInternal($value % 1000);
        if ($value < 1000000000) return $this->toWordsIdInternal(intval($value / 1000000)) . ' juta ' . $this->toWordsIdInternal($value % 1000000);
        return (string) $value;
    }
}
