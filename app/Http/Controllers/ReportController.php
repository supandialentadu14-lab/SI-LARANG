<?php

// Menentukan namespace controller

namespace App\Http\Controllers;

// Mengimpor model StockTransaction
use App\Models\StockTransaction;
// Digunakan untuk menangkap request (filter tanggal)
use Illuminate\Http\Request;
// Digunakan untuk tipe return View
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\OpdSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\NotaMaster;
use App\Models\NotaPesanan;
use App\Models\NotaItem;
use App\Models\BapPemeriksaan;
use App\Models\BapItem;
use App\Models\BelanjaModal;
use App\Models\BelanjaModalItem;

// Controller untuk menampilkan laporan stok
class ReportController extends Controller
{
    public function pinjamPakaiForm(): View
    {
        session()->forget('pinjam_pakai_current');
        session()->forget('pinjam_pakai_current_id');
        $data = null;
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        return view('pinjam_pakai.create', compact('data', 'opd'));
    }

    public function pinjamPakaiReport(Request $request): View
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

    public function pinjamPakaiSave(Request $request): View|RedirectResponse
    {
        $data = session('pinjam_pakai_current');
        if (! $data) {
            // Jika tidak ada data di session, validasi dari request sebagai fallback
            $data = $request->all();
        }
        $currentId = session('pinjam_pakai_current_id') ?? $request->input('id');

        // Cek duplikasi berdasarkan nomor
        $disk = Storage::disk('local');
        $userDir = 'users/'.Auth::id().'/pinjam_pakai';
        $files = $disk->files($userDir);
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $json = $disk->get($file);
            $existing = json_decode($json, true) ?: [];
            $fid = basename($file, '.json');
            if ($fid === $currentId) {
                continue;
            }
            if (($existing['nomor'] ?? null) === ($data['nomor'] ?? null)) {
                return view('reports.pinjam_pakai_report', [
                    'data' => $data,
                    'error' => 'Nomor berita acara sudah ada. Tidak bisa menyimpan.',
                ]);
            }
        }

        $id = $currentId ?: (string) Str::uuid();
        $path = "{$userDir}/{$id}.json";
        $disk->put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Bersihkan session ID agar tidak menempel untuk transaksi berikutnya
        session()->forget('pinjam_pakai_current_id');

        return redirect()->route('reports.pinjam.list')
            ->with('status', $currentId ? 'Berita acara berhasil diperbarui' : 'Berita acara berhasil disimpan');
    }

    public function pinjamPakaiEdit(string $id): View
    {
        $path = "users/".Auth::id()."/pinjam_pakai/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
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

    public function pinjamPakaiList(): View
    {
        $disk = Storage::disk('local');
        $files = $disk->files('users/'.Auth::id().'/pinjam_pakai');
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

    public function pinjamPakaiShow(string $id): View
    {
        $path = "users/".Auth::id()."/pinjam_pakai/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
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

    public function pinjamPakaiDelete(string $id): View
    {
        $disk = Storage::disk('local');
        $path = "users/".Auth::id()."/pinjam_pakai/{$id}.json";
        if ($disk->exists($path)) {
            $disk->delete($path);
            $status = 'Berita acara berhasil dihapus';
        } else {
            $status = 'Berita acara tidak ditemukan';
        }
        $files = $disk->files('users/'.Auth::id().'/pinjam_pakai');
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
        return view('pinjam_pakai.index', [
            'items' => $items,
            'status' => $status,
        ]);
    }

    /**
     * Menampilkan laporan berdasarkan rentang tanggal
     */
    public function kartuTahunan(Request $request): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $master = $this->loadNotaMaster();
        $startDate = $request->input(
    'start_date',
    now()->startOfYear()->toDateString()
);

$endDate = $request->input(
    'end_date',
    now()->endOfYear()->toDateString()
);


        $transactions = StockTransaction::with('product')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('product_id', 'asc')
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $grouped = [];

        foreach ($transactions as $trx) {

            if (! $trx->product) {
                continue;
            }

            $productId = (int) $trx->product_id;
            $date = $trx->date;

            if (! isset($grouped[$productId])) {
                $grouped[$productId] = [
                    'product' => $trx->product,
                    'rows' => [],
                    'saldo' => 0,
                ];
            }

            $masuk = 0;
            $keluar = 0;

            if ($trx->type === 'in') {
                $masuk = $trx->quantity;
                $grouped[$productId]['saldo'] += $trx->quantity;
            }

            if ($trx->type === 'out') {
                $keluar = $trx->quantity;
                $grouped[$productId]['saldo'] -= $trx->quantity;
            }
            
            $grouped[$productId]['rows'][] = [
                'date' => $date,
                'nosur' => $trx->nosur ?? '-',
                'masuk' => $masuk,
                'keluar' => $keluar,
                'harga' => $trx->product->price ?? 0,
                'sisa' => $grouped[$productId]['saldo'],
                'keterangan' => $trx->notes ?? '-',
            ];
        }

        return view('reports.kartu_tahunan', compact(
    'grouped',
    'startDate',
    'endDate',
    'opd',
    'master'
));

    }

    public function index(Request $request): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $master = $this->loadNotaMaster();
        // Mengambil tanggal mulai dari input
        // Jika tidak ada input, default ke awal bulan sekarang
        $startDate = $request->input(
    'start_date',
    now()->startOfYear()->toDateString()
);

$endDate = $request->input(
    'end_date',
    now()->endOfYear()->toDateString()
);


        // Mengambil data transaksi stok berdasarkan rentang tanggal
        $transactions = StockTransaction::with('product') // Eager loading relasi product
            ->whereBetween('date', [$startDate, $endDate]) // Filter tanggal
            ->orderBy('date', 'asc')        // Urutkan berdasarkan tanggal
            ->orderBy('created_at', 'asc')  // Jika tanggal sama, urutkan berdasarkan waktu input
            ->get();

        // 🔥 Mengelompokkan data berdasarkan kombinasi tanggal dan produk
        // Format key: 2026-02-17-3 (tanggal + product_id)
        $grouped = $transactions->groupBy(function ($item) {
            return $item->date.'-'.$item->product_id;
        });

        // Menyimpan hasil akhir laporan
        $reportData = [];

        // Loop setiap grup (per tanggal + per produk)
        foreach ($grouped as $items) {

            // Ambil data pertama sebagai referensi (tanggal & produk sama)
            $first = $items->first();

            // Hitung total stok masuk (type = in)
            $masuk = $items->where('type', 'in')->sum('quantity');

            // Hitung total stok keluar (type = out)
            $keluar = $items->where('type', 'out')->sum('quantity');

            // Simpan hasil ke array laporan
            $reportData[] = [
                'date' => $first->date, // Tanggal transaksi

                'product_id' => $first->product_id, // ID produk

                'name' => $first->product->name, // Nama produk

                // Harga produk (jika null, default 0)
                'harga' => $first->product->price ?? 0,

                // Satuan produk (pcs, box, dll)
                'satuan' => $first->product->unit ?? '',

                // Total barang masuk
                'masuk' => $masuk,

                // Total barang keluar
                'keluar' => $keluar,
            ];
        }

        // Mengirim data laporan dan tanggal filter ke view
        return view('reports.index', compact(
            'reportData',
            'startDate',
            'endDate',
            'opd',
            'master'
        ));
    }

    protected function prefillOpnameItems(): array
    {
        $items = [];
        $products = Product::orderBy('name')->get();
        foreach ($products as $p) {
            $qty = (int) ($p->stock ?? 0);
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

    public function opnameForm(Request $request): View
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

    public function opnameReport(Request $request): View
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

    public function opnameSave(Request $request): RedirectResponse|View
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

    public function opnameEdit(string $id): View
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

    public function opnameList(): View
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

    public function opnameShow(string $id): View
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
    
    public function opnamePrefill(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $items = $this->prefillOpnameItemsByDate($date);
        return response()->json(['items' => $items]);
    }
    
    public function belanjaModalForm(Request $request): View
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
        return view('belanja_modal.create', compact('data', 'opd', 'master'));
    }
    
    public function belanjaModalReport(Request $request): View
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
        session(['belanja_modal_current' => $data, 'belanja_modal_current_id' => $id]);
        return view('reports.belanja_modal_report', compact('data', 'opd', 'master'));
    }
    
    public function belanjaModalSave(Request $request): RedirectResponse|View
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
        if ($currentId) {
            session()->forget('belanja_modal_current_id');
        }
        return redirect()
            ->route('reports.belanja.modal.list', ['highlight' => $id])
            ->with('status', $currentId ? 'Belanja modal diperbarui' : 'Belanja modal disimpan');
    }
    
    public function belanjaModalList(): View
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
    
    public function belanjaModalShow(string $id): View
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
    
    public function belanjaModalEdit(string $id): View
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
    
    public function belanjaModalDelete(string $id): RedirectResponse
    {
        $path = "users/".Auth::id()."/belanja-modal/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        Storage::disk('local')->delete($path);
        return redirect()->route('reports.belanja.modal.list')->with('status', 'Data belanja modal dihapus');
    }
    
    public function belanjaModalPreviewAll(): View
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
            $da = $a['tm'] ? strtotime($a['tm']) : ($a['ta'] ? strtotime($a['ta']) : 0);
            $db = $b['tm'] ? strtotime($b['tm']) : ($b['ta'] ? strtotime($b['ta']) : 0);
            return $da <=> $db;
        });
        $year = !empty($years) ? max($years) : (int)now()->year;
        $data = [
            'tahun' => $year,
            'items' => $clean,
        ];
        return view('reports.belanja_modal_report', compact('data', 'opd', 'master'));
    }
    
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
            // String-based fields
            $push($opt['kegiatan'], $data['kegiatan'] ?? null);
            $push($opt['sub_kegiatan'], $data['sub_kegiatan'] ?? null);
            $push($opt['rekening'], $data['rekening'] ?? null);
            // Name-based composite fields: use 'nama'
            $push($opt['pejabat'], $data['pejabat']['nama'] ?? null);
            $push($opt['pptk'], $data['pptk']['nama'] ?? null);
            $push($opt['pptk_nip'], $data['pptk']['nip'] ?? null);
            $push($opt['pengurus_barang'], $data['pengurus_barang']['nama'] ?? null);
            $push($opt['pengurus_pengguna'], $data['pengurus_pengguna']['nama'] ?? null);
            $push($opt['ppk'], $data['ppk']['nama'] ?? null);
            $push($opt['bendahara'], $data['bendahara']['nama'] ?? null);
            $push($opt['bendahara_nip'], $data['bendahara']['nip'] ?? null);
            // Penyedia: use 'toko' name
            $push($opt['penyedia'], $data['penyedia']['toko'] ?? null);
        }
        return $opt;
    }
    
    public function notaPesananForm(Request $request): View
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
    

    
    public function notaMasterForm(): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $data = $this->loadNotaMaster();
        return view('settings.nota_master', compact('data', 'opd'));
    }
    
    public function notaMasterSave(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'opd.nama' => 'nullable|string|max:255',
            'opd.alamat' => 'nullable|string|max:255',
            'ppk.nama' => 'nullable|string|max:255',
            'ppk.nip' => 'nullable|string|max:50',
            'ppk.alamat' => 'nullable|string|max:255',
            'pejabat.nama' => 'nullable|string|max:255',
            'pejabat.nip' => 'nullable|string|max:50',
            'pptk.nama' => 'nullable|string|max:255',
            'pptk.nip' => 'nullable|string|max:50',
            'pengurus_barang.nama' => 'nullable|string|max:255',
            'pengurus_barang.nip' => 'nullable|string|max:50',
            'pengurus_pengguna.nama' => 'nullable|string|max:255',
            'pengurus_pengguna.nip' => 'nullable|string|max:50',
            'bendahara.nama' => 'nullable|string|max:255',
            'bendahara.nip' => 'nullable|string|max:50',
        ]);
        NotaMaster::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'opd_nama' => $payload['opd']['nama'] ?? '',
                'opd_alamat' => $payload['opd']['alamat'] ?? '',
                'ppk_nama' => $payload['ppk']['nama'] ?? '',
                'ppk_nip' => $payload['ppk']['nip'] ?? '',
                'ppk_alamat' => $payload['ppk']['alamat'] ?? '',
                'pejabat_nama' => $payload['pejabat']['nama'] ?? '',
                'pejabat_nip' => $payload['pejabat']['nip'] ?? '',
                'pptk_nama' => $payload['pptk']['nama'] ?? '',
                'pptk_nip' => $payload['pptk']['nip'] ?? '',
                'pengurus_barang_nama' => $payload['pengurus_barang']['nama'] ?? '',
                'pengurus_barang_nip' => $payload['pengurus_barang']['nip'] ?? '',
                'pengurus_pengguna_nama' => $payload['pengurus_pengguna']['nama'] ?? '',
                'pengurus_pengguna_nip' => $payload['pengurus_pengguna']['nip'] ?? '',
                'bendahara_nama' => $payload['bendahara']['nama'] ?? '',
                'bendahara_nip' => $payload['bendahara']['nip'] ?? '',
            ]
        );
        return redirect()->route('settings.nota.master.list')->with('status', 'Data master pengadaan disimpan');
    }
    
    public function notaMasterList(): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $data = $this->loadNotaMaster();
        return view('settings.nota_master_list', compact('data', 'opd'));
    }
    
    public function notaPesananReport(Request $request): View
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
    
    public function notaPesananSave(Request $request): RedirectResponse
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
        $currentId = session('nota_current_id') ?? $request->input('id');
        $id = $currentId ?: (string) Str::uuid();
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
        return redirect()->route('reports.nota.list')->with('status', $currentId ? 'Nota pesanan diperbarui' : 'Nota pesanan disimpan');
    }
    
    public function notaPesananUpdate(Request $request, string $id): RedirectResponse
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
        Storage::disk('local')->put("users/".Auth::id()."/nota-pesanan/{$id}.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

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
    
    public function notaPesananList(): View
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
        return view('nota_pesanan.index', compact('items'));
    }
    
    public function notaPesananShow(string $id): View
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
    
    public function notaPesananEdit(string $id): View
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
    
    public function notaPesananDelete(string $id): RedirectResponse
    {
        $path = "users/".Auth::id()."/nota-pesanan/{$id}.json";
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
        return redirect()->route('reports.nota.list')->with('status', 'Nota pesanan dihapus');
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

    protected function buildPembuka(string $tanggal, string $opdNama): string
    {
        $dt = \Carbon\Carbon::parse($tanggal)->locale('id');
        $hari = $dt->translatedFormat('l');
        $bulan = $dt->translatedFormat('F');
        $tanggalKata = ucwords($this->toWordsId((int) $dt->format('d')));
        $tahunKata = ucwords($this->toWordsId((int) $dt->format('Y')));
        return "Pada hari ini {$hari} Tanggal {$tanggalKata} Bulan {$bulan} Tahun {$tahunKata}, bertempat di {$opdNama} Kabupaten Bolaang Mongondow Selatan, yang bertanda tangan dibawah ini:";
    }

    // Berita Acara Pemeriksaan Barang/Pekerjaan (BAP) - berdasarkan Nota Pesanan
    protected function listNotaDocs(): array
    {
        $disk = Storage::disk('local');
        $dir = 'users/'.Auth::id().'/nota-pesanan';
        $files = $disk->exists($dir) ? $disk->files($dir) : [];
        $items = [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $data = json_decode($disk->get($file), true) ?: [];
            $items[] = [
                'id' => basename($file, '.json'),
                'nomor' => $data['nomor'] ?? '',
                'tanggal' => $data['tanggal'] ?? '',
                'belanja' => $data['belanja'] ?? '',
                'penyedia' => $data['penyedia'] ?? [],
                'items' => $data['items'] ?? [],
            ];
        }
        usort($items, fn($a, $b) => ($b['tanggal'] ?? '') <=> ($a['tanggal'] ?? ''));
        return $items;
    }

    protected function findNotaByNomor(?string $nomor): ?array
    {
        if (!$nomor) return null;
        foreach ($this->listNotaDocs() as $doc) {
            if (trim($doc['nomor'] ?? '') === trim($nomor)) return $doc;
        }
        return null;
    }

    public function pemeriksaanForm(Request $request): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $notaDocs = $this->listNotaDocs();
        $data = session('bap_current') ?? [
            'tanggal' => now()->toDateString(),
            'tempat' => $opd->nama_opd ?? '',
            'nomor' => '',
            'nota_nomor' => '',
        ];
        return view('pemeriksaan.create', compact('data', 'opd', 'notaDocs'));
    }

    public function pemeriksaanReport(Request $request): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $master = $this->loadNotaMaster();
        $payload = $request->all();
        $nota = null;
        if ($nid = $request->input('nota_id')) {
            $nid = trim($nid);
            foreach ($this->listNotaDocs() as $doc) {
                if ($doc['id'] === $nid) { $nota = $doc; break; }
            }
        }
        if (!$nota) {
            $nota = $this->findNotaByNomor($request->input('nota_nomor'));
        }
        $cleanItems = [];
        foreach (($nota['items'] ?? []) as $it) {
            $name = $it['name'] ?? '';
            $qty = (int)($it['qty'] ?? 0);
            $unit = $it['unit'] ?? '';
            $price = (int)($it['price'] ?? 0);
            $total = $qty * $price;
            $cleanItems[] = [
                'nama' => $name,
                'kuantitas' => $qty,
                'satuan' => $unit,
                'harga' => $price,
                'jumlah' => $total,
            ];
        }
        $totalSum = 0;
        foreach ($cleanItems as $row) { $totalSum += (int)($row['jumlah'] ?? 0); }
        $dt = \Carbon\Carbon::parse($payload['tanggal'] ?? now()->toDateString())->locale('id');
        $hari = $dt->translatedFormat('l');
        $bulan = $dt->translatedFormat('F');
        $tanggalKata = ucwords($this->toWordsId((int) $dt->format('d')));
        $tahunKata = ucwords($this->toWordsId((int) $dt->format('Y')));
        $data = [
            'nomor' => $payload['nomor'] ?? '',
            'tanggal' => $payload['tanggal'] ?? now()->toDateString(),
            'tempat' => $payload['tempat'] ?? ($opd->nama_opd ?? ''),
            'nota' => [
                'id' => $nota['id'] ?? null,
                'nomor' => $nota['nomor'] ?? '',
                'tanggal' => $nota['tanggal'] ?? '',
                'belanja' => $nota['belanja'] ?? '',
                'penyedia' => $nota['penyedia'] ?? [],
            ],
            'items' => $cleanItems,
            'terbilang' => ucwords($this->toWordsId((int) $totalSum)),
            'total' => $totalSum,
            'ppk' => [
                'nama' => ($master['ppk']['nama'] ?? '') ?: ($opd->kepala_nama ?? ''),
                'nip' => ($master['ppk']['nip'] ?? '') ?: ($opd->kepala_nip ?? ''),
                'jabatan' => 'Pejabat Pembuat Komitmen',
                'alamat' => ($master['ppk']['alamat'] ?? '') ?: (($master['opd']['alamat'] ?? '') ?: ($opd->alamat_opd ?? '')),
            ],
            'tanggal_kata' => "Pada hari {$hari} Tanggal {$tanggalKata} Bulan {$bulan} Tahun {$tahunKata}",
        ];
        session([
            'bap_current' => $data,
        ]);
        return view('reports.pemeriksaan_report', compact('data', 'opd'));
    }

    public function pemeriksaanSave(Request $request): RedirectResponse
    {
        $currentId = session('bap_current_id') ?? $request->input('id');
        $id = $currentId ?: (string) Str::uuid();
        $data = session('bap_current') ?? $request->all();
        Storage::disk('local')->put("users/".Auth::id()."/bap-pemeriksaan/{$id}.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        session()->forget('bap_current_id');
        $bap = BapPemeriksaan::updateOrCreate(
            ['user_id' => Auth::id(), 'nomor' => $data['nomor'] ?? ''],
            [
                'tanggal' => $data['tanggal'] ?? now()->toDateString(),
                'tempat' => $data['tempat'] ?? '',
                'nota_nomor' => $data['nota']['nomor'] ?? '',
                'nota_tanggal' => $data['nota']['tanggal'] ?? null,
                'belanja' => $data['nota']['belanja'] ?? '',
                'penyedia_toko' => $data['nota']['penyedia']['toko'] ?? '',
                'penyedia_alamat' => $data['nota']['penyedia']['alamat'] ?? '',
                'ppk_nama' => $data['ppk']['nama'] ?? '',
                'ppk_nip' => $data['ppk']['nip'] ?? '',
                'ppk_alamat' => $data['ppk']['alamat'] ?? '',
                'total' => (int)($data['total'] ?? 0),
                'terbilang' => $data['terbilang'] ?? '',
            ]
        );
        if (!empty($data['items']) && $bap) {
            $bap->items()->delete();
            foreach ($data['items'] as $row) {
                BapItem::create([
                    'bap_id' => $bap->id,
                    'nama' => $row['nama'] ?? '',
                    'kuantitas' => (int)($row['kuantitas'] ?? 0),
                    'satuan' => $row['satuan'] ?? '',
                    'harga' => (int)($row['harga'] ?? 0),
                    'jumlah' => (int)($row['jumlah'] ?? 0),
                ]);
            }
        }
        return redirect()->route('reports.pemeriksaan.list')->with('status', $currentId ? 'Berita acara diperbarui' : 'Berita acara disimpan');
    }

    public function pemeriksaanList(): View
    {
        $disk = Storage::disk('local');
        $dir = 'users/'.Auth::id().'/bap-pemeriksaan';
        $files = $disk->exists($dir) ? $disk->files($dir) : [];
        $items = [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $data = json_decode($disk->get($file), true) ?: [];
            $total = 0;
            foreach (($data['items'] ?? []) as $row) {
                $total += (int)($row['jumlah'] ?? 0);
            }
            $items[] = [
                'id' => basename($file, '.json'),
                'updated' => $disk->lastModified($file),
                'nomor' => $data['nomor'] ?? '',
                'tanggal' => $data['tanggal'] ?? '',
                'nota_nomor' => $data['nota']['nomor'] ?? '',
                'total' => $total,
            ];
        }
        usort($items, fn($a, $b) => $b['updated'] <=> $a['updated']);
        return view('pemeriksaan.index', compact('items'));
    }

    public function pemeriksaanShow(string $id): View
    {
        $path = "users/".Auth::id()."/bap-pemeriksaan/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true) ?: [];
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $master = $this->loadNotaMaster();
        $data['ppk'] = $data['ppk'] ?? [];
        $data['ppk']['nama'] = $data['ppk']['nama'] ?? (($master['ppk']['nama'] ?? '') ?: ($opd->kepala_nama ?? ''));
        $data['ppk']['nip'] = $data['ppk']['nip'] ?? (($master['ppk']['nip'] ?? '') ?: ($opd->kepala_nip ?? ''));
        $data['ppk']['alamat'] = $data['ppk']['alamat'] ?? (($master['ppk']['alamat'] ?? '') ?: ($opd->alamat_opd ?? ''));
        session(['bap_current' => $data, 'bap_current_id' => $id]);
        $saved_id = $id;
        return view('reports.pemeriksaan_report', compact('data', 'opd', 'saved_id'));
    }

    public function pemeriksaanEdit(string $id): View
    {
        $path = "users/".Auth::id()."/bap-pemeriksaan/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true) ?: [];
        session([
            'bap_current' => $data,
            'bap_current_id' => $id,
        ]);
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $notaDocs = $this->listNotaDocs();
        return view('pemeriksaan.edit', compact('data', 'opd', 'notaDocs'));
    }

    public function pemeriksaanDelete(string $id): RedirectResponse
    {
        $path = "users/".Auth::id()."/bap-pemeriksaan/{$id}.json";
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
        return redirect()->route('reports.pemeriksaan.list')->with('status', 'Berita acara dihapus');
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
                'items' => $data['items'] ?? [],
                'ppk' => $data['ppk'] ?? [],
            ];
        }
        usort($items, fn($a, $b) => ($b['tanggal'] ?? '') <=> ($a['tanggal'] ?? ''));
        return $items;
    }
    
    public function penerimaanForm(Request $request): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $docs = $this->listPemeriksaanDocs();
        $data = [
            'nomor' => '',
            'tanggal' => now()->toDateString(),
            'tempat' => $opd->nama_opd ?? '',
            'pemeriksaan_nomor' => '',
        ];
        return view('penerimaan.create', [
            'data' => $data,
            'opd' => $opd,
            'docs' => $docs,
        ]);
    }
    
    public function penerimaanReport(Request $request): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $master = $this->loadNotaMaster();
        $payload = $request->all();
        $selected = null;
        foreach ($this->listPemeriksaanDocs() as $doc) {
            if (($doc['nomor'] ?? '') === ($payload['pemeriksaan_nomor'] ?? '')) { $selected = $doc; break; }
        }
        $items = $selected['items'] ?? [];
        $cleanItems = [];
        foreach ($items as $it) {
            $name = $it['nama'] ?? '';
            $qty = (int)($it['kuantitas'] ?? 0);
            $unit = $it['satuan'] ?? '';
            $price = (int)($it['harga'] ?? 0);
            $total = (int)($it['jumlah'] ?? ($qty * $price));
            $cleanItems[] = [
                'nama' => $name,
                'kuantitas' => $qty,
                'satuan' => $unit,
                'harga' => $price,
                'jumlah' => $total,
            ];
        }
        $totalSum = 0;
        foreach ($cleanItems as $row) { $totalSum += (int)($row['jumlah'] ?? 0); }
        $dt = \Carbon\Carbon::parse($payload['tanggal'] ?? now()->toDateString())->locale('id');
        $hari = $dt->translatedFormat('l');
        $bulan = $dt->translatedFormat('F');
        $tanggalKata = ucwords($this->toWordsId((int) $dt->format('d')));
        $tahunKata = ucwords($this->toWordsId((int) $dt->format('Y')));
        $data = [
            'nomor' => $payload['nomor'] ?? '',
            'tanggal' => $payload['tanggal'] ?? now()->toDateString(),
            'tempat' => $payload['tempat'] ?? ($opd->nama_opd ?? ''),
            'pemeriksaan_nomor' => $payload['pemeriksaan_nomor'] ?? '',
            'nota' => $selected['nota'] ?? [],
            'items' => $cleanItems,
            'terbilang' => ucwords($this->toWordsId((int) $totalSum)),
            'total' => $totalSum,
            'ppk' => [
                'nama' => ($master['ppk']['nama'] ?? '') ?: ($opd->kepala_nama ?? ''),
                'nip' => ($master['ppk']['nip'] ?? '') ?: ($opd->kepala_nip ?? ''),
            ],
            'pengguna' => [
                'nama' => $master['pengurus_pengguna']['nama'] ?? '',
                'nip' => $master['pengurus_pengguna']['nip'] ?? '',
                'jabatan' => 'Pengurus Barang Pengguna',
            ],
            'tanggal_kata' => "Pada hari ini {$hari} Tanggal {$tanggalKata} Bulan {$bulan} Tahun {$tahunKata}, kami yang bertanda tangan di bawah ini:",
        ];
        session(['penerimaan_current' => $data]);
        return view('reports.penerimaan_report', compact('data', 'opd'));
    }
    
    public function penerimaanSave(Request $request): RedirectResponse
    {
        $data = session('penerimaan_current') ?? [];
        if (!$data) {
            return redirect()->route('reports.penerimaan.form')->with('status', 'Data penerimaan tidak ditemukan');
        }
        $currentId = session('penerimaan_current_id') ?? $request->input('id');
        $id = $currentId ?: (string) Str::uuid();
        Storage::disk('local')->put("users/".Auth::id()."/bap-penerimaan/{$id}.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        session()->forget('penerimaan_current_id');
        return redirect()->route('reports.penerimaan.list')->with('status', $currentId ? 'BAP Penerimaan diperbarui' : 'BAP Penerimaan disimpan');
    }
    
    public function penerimaanList(): View
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
                'updated' => $disk->lastModified($file),
                'nomor' => $data['nomor'] ?? '',
                'tanggal' => $data['tanggal'] ?? '',
                'total' => $data['total'] ?? 0,
                'pemeriksaan_nomor' => $data['pemeriksaan_nomor'] ?? '',
            ];
        }
        usort($items, fn($a, $b) => $b['updated'] <=> $a['updated']);
        return view('penerimaan.index', compact('items'));
    }
    
    public function penerimaanEdit(string $id): View
    {
        $path = "users/".Auth::id()."/bap-penerimaan/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true) ?: [];
        session([
            'penerimaan_current' => $data,
            'penerimaan_current_id' => $id,
        ]);
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $docs = $this->listPemeriksaanDocs();
        return view('penerimaan.edit', compact('data', 'opd', 'docs'));
    }
    
    public function penerimaanShow(string $id): View
    {
        $path = "users/".Auth::id()."/bap-penerimaan/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true) ?: [];
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        session(['penerimaan_current' => $data, 'penerimaan_current_id' => $id]);
        return view('reports.penerimaan_report', compact('data', 'opd'));
    }
    
    public function penerimaanDelete(string $id): RedirectResponse
    {
        $path = "users/".Auth::id()."/bap-penerimaan/{$id}.json";
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
        return redirect()->route('reports.penerimaan.list')->with('status', 'BAP Penerimaan dihapus');
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
                'total' => $data['total'] ?? 0,
                'belanja' => $data['nota']['belanja'] ?? ($data['belanja'] ?? ''),
                'nota' => $data['nota'] ?? [],
            ];
        }
        usort($items, fn($a, $b) => ($b['tanggal'] ?? '') <=> ($a['tanggal'] ?? ''));
        return $items;
    }
    
    public function kwitansiForm(Request $request): View
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
    
    public function kwitansiReport(Request $request): View
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
    

    
    protected function findPenerimaanByNomor(?string $nomor): ?array
    {
        if (!$nomor) return null;
        foreach ($this->listPenerimaanDocs() as $doc) {
            if (trim($doc['nomor'] ?? '') === trim($nomor)) return $doc;
        }
        return null;
    }
    
    
    protected function findPemeriksaanByNomor(?string $nomor): ?array
    {
        if (!$nomor) return null;
        foreach ($this->listPemeriksaanDocs() as $doc) {
            if (trim($doc['nomor'] ?? '') === trim($nomor)) return $doc;
        }
        return null;
    }
    
    public function kwitansiPrintAll(Request $request)
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
        
        $kwitansiData = $kwt ?: ($this->kwitansiReport(new Request([
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
    

    public function opnameDelete(string $id): View
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
}
