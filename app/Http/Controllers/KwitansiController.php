<?php

namespace App\Http\Controllers;

use App\Models\NotaMaster;
use App\Models\OpdSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

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

    protected function findFullNotaByNomor(?string $nomor): array
    {
        if (!$nomor) return [];
        $disk = Storage::disk('local');
        $dir = 'users/'.Auth::id().'/nota-pesanan';
        $files = $disk->exists($dir) ? $disk->files($dir) : [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $data = json_decode($disk->get($file), true) ?: [];
            if (trim(($data['nomor'] ?? '')) === trim($nomor)) {
                return $data;
            }
        }
        return [];
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

    public function save(Request $request)
    {
        return $this->store($request);
    }

    public function update(Request $request, $id)
    {
        return $this->store($request, $id);
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

    protected function store(Request $request, $oldId = null)
    {
        try {
            $opd = OpdSetting::where('user_id', Auth::id())->first();
            $master = $this->loadNotaMaster();
            
            // Fallback to session if request data is empty (e.g. from preview save)
            $payload = $request->all();
            if (empty($payload['penerimaan_nomor']) && session('kwitansi_current')) {
                $payload = array_merge(session('kwitansi_current'), $payload);
            }
            
            \Illuminate\Support\Facades\Log::info('Kwitansi Store Payload:', $payload);

            $selected = null;
            $penerimaanNomor = $payload['penerimaan_nomor'] ?? '';
            
            if ($penerimaanNomor) {
                foreach ($this->listPenerimaanDocs() as $doc) {
                    if (trim($doc['nomor'] ?? '') === trim($penerimaanNomor)) { 
                        $selected = $doc; 
                        break; 
                    }
                }
            }
            
            $total = (int)($selected['total'] ?? 0);
            $tanggalObj = \Carbon\Carbon::parse($payload['tanggal'] ?? now()->toDateString());
            $tanggalStr = $tanggalObj->locale('id')->translatedFormat('d F Y');
            
            $uraianBelanja = (($selected['belanja'] ?? '') ?: '');
            
            $notaData = $selected['nota'] ?? [];
            
            // Ambil data nota pesanan terbaru jika ada
            if (!empty($notaData['nomor'])) {
                $freshNota = $this->findFullNotaByNomor($notaData['nomor']);
                if (!empty($freshNota)) {
                    $notaData = array_merge($notaData, $freshNota);
                }
            }
            
            $kegiatan = $notaData['kegiatan'] ?? '...';
            $subKegiatan = $notaData['sub_kegiatan'] ?? '...';
            $namaPekerjaan = $notaData['pekerjaan'] ?? $uraianBelanja;
            $tahunAnggaran = $payload['tahun'] ?? now()->year;
            
            // Format yang diminta user:
            // "[Nama Pekerjaan] Pada Keg. [Sub Kegiatan] [Kegiatan] Tahun [Tahun Anggaran]"
            // Pastikan jika pekerjaan/belanja sudah mengandung kata "Belanja", tidak perlu ditambah prefix.
            
            $uraianFull = "Belanja {$namaPekerjaan} Pada Keg. {$subKegiatan} {$kegiatan} Tahun {$tahunAnggaran}";
            
            // Format Nomor KWT Otomatis: [Input]/KW/KOMINFO/[BulanRomawi]/[Tahun]
            $inputNomor = trim((string)($payload['nomor_kwt'] ?? ''));
            // Jika input hanya angka, format ulang
            if (preg_match('/^\d+$/', $inputNomor)) {
                $bulanRomawi = $this->formatRomawi($tanggalObj->month);
                $nomorKwtFormatted = "{$inputNomor}/KW/KOMINFO/{$bulanRomawi}/{$tahunAnggaran}";
            } else {
                // Jika sudah ada format atau kosong, gunakan apa adanya
                $nomorKwtFormatted = $inputNomor;
            }

            $data = [
                'tahun' => $payload['tahun'] ?? now()->year,
                'rekening' => $payload['rekening'] ?? '',
                'nomor_kwt' => $nomorKwtFormatted,
                'tanggal' => $payload['tanggal'] ?? now()->toDateString(),
                'penerimaan_nomor' => $penerimaanNomor,
                'jumlah' => $total,
                'terbilang' => ucwords($this->toWordsId((int)$total)) . ' Rupiah',
                'pembayaran_uraian' => $uraianFull,
                'lokasi_tanggal' => 'Bolaang Uki, ' . $tanggalStr,
                'pejabat' => [
                    'pptk' => $master['pptk']['nama'] ?? '',
                    'bendahara' => $master['bendahara']['nama'] ?? '',
                    'pihak_ketiga' => $payload['pihak_ketiga_nama'] ?? ($selected['nota']['penyedia']['pemilik'] ?? ($selected['nota']['penyedia']['toko'] ?? '')),
                    'pengguna' => $master['ppk']['nama'] ?? '',
                ],
                'opd_nama' => $opd->nama_opd ?? '',
                'bendahara_nip' => $master['bendahara']['nip'] ?? '',
                'pptk_nip' => $master['pptk']['nip'] ?? '',
                'ppk_nip' => $master['ppk']['nip'] ?? '',
            ];

            // Sanitasi nomor untuk nama file
            $rawNomor = trim((string)($data['nomor_kwt'] ?? ''));
            if ($rawNomor === '') {
                $rawNomor = 'KWT-'.uniqid();
            }
            // Ganti slash dengan dash, hapus karakter aneh
            $nomorSafe = str_replace(['/', '\\'], '-', $rawNomor);
            $nomorSafe = preg_replace('/[^a-zA-Z0-9\-\_\.]/', '', $nomorSafe);
            
            if (trim($nomorSafe) === '') {
                 $nomorSafe = 'KWT-'.uniqid();
            }
            $newId = $nomorSafe;
            
            // Pastikan direktori ada
            $dirPath = "users/".Auth::id()."/kwitansi";
            if (!Storage::disk('local')->exists($dirPath)) {
                Storage::disk('local')->makeDirectory($dirPath);
            }

            // Jika update dan ID berubah, hapus file lama
            if ($oldId && $oldId !== $newId) {
                $oldPath = "{$dirPath}/{$oldId}.json";
                if (Storage::disk('local')->exists($oldPath)) {
                    Storage::disk('local')->delete($oldPath);
                }
            }

            $fullPath = "{$dirPath}/{$newId}.json";
            \Illuminate\Support\Facades\Log::info("Saving Kwitansi to: {$fullPath}");

            // Simpan ke JSON
            Storage::disk('local')->put($fullPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            if (!Storage::disk('local')->exists($fullPath)) {
                throw new \Exception("File gagal ditulis ke disk: {$fullPath}");
            }

            session()->forget('kwitansi_current');
            $msg = $oldId ? 'Kwitansi berhasil diperbarui' : 'Kwitansi berhasil disimpan';
            return redirect()->route('reports.kwitansi.list')->with('success', $msg);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Gagal simpan kwitansi: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function list(Request $request)
    {
        $disk = Storage::disk('local');
        $dir = 'users/'.Auth::id().'/kwitansi';
        $files = $disk->exists($dir) ? $disk->files($dir) : [];
        $items = [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.json')) continue;
            $data = json_decode($disk->get($file), true) ?: [];
            $items[] = [
                'id' => basename($file, '.json'),
                'nomor_kwt' => $data['nomor_kwt'] ?? '',
                'tanggal' => $data['tanggal'] ?? '',
                'penerimaan_nomor' => $data['penerimaan_nomor'] ?? '',
                'jumlah' => $data['jumlah'] ?? 0,
                'uraian' => $data['pembayaran_uraian'] ?? '',
            ];
        }
        usort($items, fn($a, $b) => ($b['tanggal'] ?? '') <=> ($a['tanggal'] ?? ''));

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

        return view('kwitansi.index', compact('items'));
    }

    public function show($id)
    {
        $disk = Storage::disk('local');
        $path = "users/".Auth::id()."/kwitansi/{$id}.json";
        if (! $disk->exists($path)) abort(404);
        
        $data = json_decode($disk->get($path), true);
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $saved_id = $id;
        
        return view('reports.kwitansi_report', compact('data', 'opd', 'saved_id'));
    }

    public function edit($id)
    {
        $disk = Storage::disk('local');
        $path = "users/".Auth::id()."/kwitansi/{$id}.json";
        if (! $disk->exists($path)) abort(404);
        
        $data = json_decode($disk->get($path), true);
        $opd = OpdSetting::where('user_id', Auth::id())->first();
        $docs = $this->listPenerimaanDocs();
        
        return view('kwitansi.edit', compact('data', 'opd', 'docs', 'id'));
    }



    public function delete($id)
    {
        $disk = Storage::disk('local');
        $path = "users/".Auth::id()."/kwitansi/{$id}.json";
        if ($disk->exists($path)) {
            $disk->delete($path);
        }
        return redirect()->route('reports.kwitansi.list')->with('status', 'Kwitansi dihapus');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        $count = 0;
        foreach ($ids as $id) {
            $path = "users/".Auth::id()."/kwitansi/{$id}.json";
            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
                $count++;
            }
        }
        return redirect()->route('reports.kwitansi.list')->with('status', "{$count} Kwitansi dihapus");
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


}

