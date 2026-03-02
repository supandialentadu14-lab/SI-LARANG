<?php

namespace App\Http\Controllers;

use App\Models\NotaMaster;
use App\Models\OpdSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PenerimaanController extends Controller
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
                'total' => (int)($data['total'] ?? 0),
                'belanja' => $data['nota']['belanja'] ?? '',
            ];
        }
        usort($items, fn($a, $b) => ($b['tanggal'] ?? '') <=> ($a['tanggal'] ?? ''));
        return $items;
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

    public function form(Request $request): View
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

    protected function getBapNomorMap(): array
    {
        $pemeriksaanDocs = $this->listPemeriksaanDocs();
        $bapMap = [];
        foreach ($pemeriksaanDocs as $doc) {
            $bapNomor = $doc['nomor'] ?? '';
            $notaNomor = $doc['nota']['nomor'] ?? '';
            
            // Map BAP Nomor to itself (if stored correctly)
            if ($bapNomor) $bapMap[$bapNomor] = $bapNomor;
            
            // Map Nota Nomor to BAP Nomor (fix for when Nota Nomor was stored instead)
            if ($notaNomor) $bapMap[$notaNomor] = $bapNomor;
        }
        return $bapMap;
    }

    public function report(Request $request): View
    {
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $master = $this->loadNotaMaster();
        $payload = $request->all();
        
        // Use mapping to find correct BAP Doc
        $bapMap = $this->getBapNomorMap();
        $inputRef = $payload['pemeriksaan_nomor'] ?? '';
        $realBapNomor = $bapMap[$inputRef] ?? $inputRef;
        
        $selected = null;
        foreach ($this->listPemeriksaanDocs() as $doc) {
            if (($doc['nomor'] ?? '') === $realBapNomor) { $selected = $doc; break; }
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
        
        $tanggalObj = \Carbon\Carbon::parse($payload['tanggal'] ?? now()->toDateString());
        $tahunAnggaran = $tanggalObj->year;
        
        // Format Nomor Otomatis: [Input]/BASTB/DISKOMINFO/[BulanRomawi]/[Tahun]
        $inputNomor = trim((string)($payload['nomor'] ?? ''));
        if (preg_match('/^\d+$/', $inputNomor)) {
            $bulanRomawi = $this->formatRomawi($tanggalObj->month);
            $nomorFormatted = "{$inputNomor}/BASTB/DISKOMINFO/{$bulanRomawi}/{$tahunAnggaran}";
        } else {
            $nomorFormatted = $inputNomor;
        }

        $data = [
            'nomor' => $nomorFormatted,
            'tanggal' => $payload['tanggal'] ?? now()->toDateString(),
            'tempat' => $payload['tempat'] ?? ($opd->nama_opd ?? ''),
            'pemeriksaan_nomor' => $realBapNomor, // Use corrected BAP Number
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

    public function save(Request $request): RedirectResponse
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

    public function list(): View
    {
        $disk = Storage::disk('local');
        $bapMap = $this->getBapNomorMap(); // Use helper

        $dir = 'users/'.Auth::id().'/bap-penerimaan';
        $files = $disk->exists($dir) ? $disk->files($dir) : [];
        $items = [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $data = json_decode($disk->get($file), true) ?: [];
            
            $storedRef = $data['pemeriksaan_nomor'] ?? '';
            // Try to find the correct BAP Number from the map
            $realBapNomor = $bapMap[$storedRef] ?? $storedRef;

            $items[] = [
                'id' => basename($file, '.json'),
                'updated' => $disk->lastModified($file),
                'nomor' => $data['nomor'] ?? '',
                'tanggal' => $data['tanggal'] ?? '',
                'total' => $data['total'] ?? 0,
                'pemeriksaan_nomor' => $realBapNomor,
            ];
        }
        usort($items, fn($a, $b) => $b['updated'] <=> $a['updated']);
        return view('penerimaan.index', compact('items'));
    }

    public function edit(string $id): View
    {
        $path = "users/".Auth::id()."/bap-penerimaan/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true) ?: [];
        
        // Fix BAP Nomor in loaded data for editing
        $bapMap = $this->getBapNomorMap();
        $storedRef = $data['pemeriksaan_nomor'] ?? '';
        $data['pemeriksaan_nomor'] = $bapMap[$storedRef] ?? $storedRef;
        
        session([
            'penerimaan_current' => $data,
            'penerimaan_current_id' => $id,
        ]);
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $docs = $this->listPemeriksaanDocs();
        return view('penerimaan.edit', compact('data', 'opd', 'docs'));
    }

    public function show(string $id): View
    {
        $path = "users/".Auth::id()."/bap-penerimaan/{$id}.json";
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        $json = Storage::disk('local')->get($path);
        $data = json_decode($json, true) ?: [];
        
        // Fix BAP Nomor in loaded data for viewing
        $bapMap = $this->getBapNomorMap();
        $storedRef = $data['pemeriksaan_nomor'] ?? '';
        $data['pemeriksaan_nomor'] = $bapMap[$storedRef] ?? $storedRef;
        
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        session(['penerimaan_current' => $data, 'penerimaan_current_id' => $id]);
        return view('reports.penerimaan_report', compact('data', 'opd'));
    }

    public function delete(string $id): RedirectResponse
    {
        $path = "users/".Auth::id()."/bap-penerimaan/{$id}.json";
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
        return redirect()->route('reports.penerimaan.list')->with('status', 'BAP Penerimaan dihapus');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        $count = 0;
        foreach ($ids as $id) {
            $path = "users/".Auth::id()."/bap-penerimaan/{$id}.json";
            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
                $count++;
            }
        }
        return redirect()->route('reports.penerimaan.list')->with('status', "{$count} BAP Penerimaan dihapus");
    }
}

