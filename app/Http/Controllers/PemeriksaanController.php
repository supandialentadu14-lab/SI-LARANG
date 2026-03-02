<?php

namespace App\Http\Controllers;

use App\Models\BapItem;
use App\Models\BapPemeriksaan;
use App\Models\NotaMaster;
use App\Models\OpdSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class PemeriksaanController extends Controller
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

    public function form(Request $request): View
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

    public function report(Request $request): View
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
        
        $tanggalObj = \Carbon\Carbon::parse($payload['tanggal'] ?? now()->toDateString());
        $tahunAnggaran = $tanggalObj->year;
        
        // Format Nomor Otomatis: [Input]/BAPB/DISKOMINFO/[BulanRomawi]/[Tahun]
        $inputNomor = trim((string)($payload['nomor'] ?? ''));
        if (preg_match('/^\d+$/', $inputNomor)) {
            $bulanRomawi = $this->formatRomawi($tanggalObj->month);
            $nomorFormatted = "{$inputNomor}/BAPB/DISKOMINFO/{$bulanRomawi}/{$tahunAnggaran}";
        } else {
            $nomorFormatted = $inputNomor;
        }

        $data = [
            'nomor' => $nomorFormatted,
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
        session(['bap_current' => $data]);
        return view('reports.pemeriksaan_report', compact('data', 'opd'));
    }

    public function save(Request $request): RedirectResponse
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

    public function list(Request $request): View
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

        return view('pemeriksaan.index', compact('items'));
    }

    public function show(string $id): View
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

    public function edit(string $id): View
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

    public function delete(string $id): RedirectResponse
    {
        $path = "users/".Auth::id()."/bap-pemeriksaan/{$id}.json";
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
        return redirect()->route('reports.pemeriksaan.list')->with('status', 'Berita acara dihapus');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        $count = 0;
        foreach ($ids as $id) {
            $path = "users/".Auth::id()."/bap-pemeriksaan/{$id}.json";
            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
                $count++;
            }
        }
        return redirect()->route('reports.pemeriksaan.list')->with('status', "{$count} Berita acara dihapus");
    }
}
