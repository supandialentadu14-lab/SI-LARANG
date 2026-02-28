@extends('layouts.admin')

@section('title', 'Edit Berita Acara Pinjam Pakai')
@section('header', 'Berita Acara Pinjam Pakai')
@section('subheader', 'Edit data berita acara')

@section('content')
    <script>
        window.formData = function () {
            return {
                items: {!! json_encode(($data['items'] ?? [])) !!},
                nextKey: 1,
                ensureKeys() {
                    const withKeys = (this.items || []).map(it => ({ ...it, _key: it._key || (this.nextKey++) }));
                    this.items = withKeys;
                },
                init() {
                    const raw = (this.items || []).filter((row) => {
                        const vals = Object.values(row || {});
                        return vals.some(v => String(v ?? '').trim() !== '');
                    });
                    this.items = raw;
                    this.dedupe();
                    this.ensureKeys();
                    this.updatePembuka();
                },
                prefill() { this.items = {!! json_encode(($data['items'] ?? [])) !!}; this.dedupe(); this.ensureKeys(); },
                addItem() { this.items.push({ _key: this.nextKey++ }); },
                removeItem(i) { this.items.splice(i, 1); },
                dedupe() {
                    const seen = new Set();
                    this.items = this.items.filter((row) => {
                        const key = `${row.nama || ''}|${row.merk || ''}|${row.tipe || ''}|${row.identitas || ''}|${row.tahun || ''}|${row.kondisi || ''}|${row.jumlah || ''}`;
                        if (seen.has(key)) return false;
                        seen.add(key);
                        return true;
                    });
                },
                updatePembuka() {
                    try {
                        const v = this.$refs.tanggal?.value;
                        const tempat = this.$refs.tempat?.value || '-';
                        if (!v) return;
                        const d = new Date(v);
                        const hari = d.toLocaleDateString('id-ID', { weekday: 'long' });
                        const bulan = d.toLocaleDateString('id-ID', { month: 'long' });
                        const tanggal = d.getDate();
                        const tahun = d.getFullYear();
                        const toWords = (n) => {
                            n = parseInt(n, 10);
                            const h = ["","satu","dua","tiga","empat","lima","enam","tujuh","delapan","sembilan","sepuluh","sebelas"];
                            const cap = s => s.replace(/\b\w/g, c => c.toUpperCase());
                            const w = (v) => {
                                if (v < 12) return h[v];
                                if (v < 20) return w(v-10) + " belas";
                                if (v < 100) return w(Math.floor(v/10)) + " puluh " + w(v%10);
                                if (v < 200) return "seratus " + w(v-100);
                                if (v < 1000) return w(Math.floor(v/100)) + " ratus " + w(v%100);
                                if (v < 2000) return "seribu " + w(v-1000);
                                if (v < 1000000) return w(Math.floor(v/1000)) + " ribu " + w(v%1000);
                                if (v < 1000000000) return w(Math.floor(v/1000000)) + " juta " + w(v%1000000);
                                return String(v);
                            };
                            return cap(w(n).trim());
                        };
                        const tanggalKata = toWords(tanggal);
                        const tahunKata = toWords(tahun);
                        this.$refs.pembuka.value =
                            `Pada hari ini ${hari} Tanggal ${tanggalKata} Bulan ${bulan} Tahun ${tahunKata}, yang bertanda tangan di bawah ini:`;
                    } catch (e) {}
                }
            }
        }
    </script>
    <form method="POST" action="{{ route('reports.pinjam.report') }}" x-data="formData()" x-init="init()" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
           <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nomor</label>
                <input type="text" name="nomor" value="{{ $data['nomor'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal</label>
                <input x-ref="tanggal" @change="updatePembuka()" type="date" name="tanggal" value="{{ $data['tanggal'] ?? now()->toDateString() }}" class="w-full px-4 py-2 rounded-lg border border-gray-300" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tempat</label>
                <input x-ref="tempat" @input="updatePembuka()" type="text" name="tempat" value="{{ $data['tempat'] ?? ($opd->nama_opd ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300" required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Narasi Pembuka</label>
            <textarea x-ref="pembuka" name="pembuka" rows="4" class="w-full px-4 py-2 rounded-lg border border-gray-300">{{ $data['pembuka'] ?? ('Pada hari ini ' . \Illuminate\Support\Carbon::parse($data['tanggal'] ?? now())->translatedFormat('l d F Y') . ', bertempat di ' . (($opd->nama_opd ?? null) ?: ($data['tempat'] ?? '-')) . ', yang bertanda tangan di bawah ini:') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <h3 class="font-bold text-gray-800">PIHAK PERTAMA</h3>
                @if(isset($opd) && $opd->kepala_nama)
                    <div class="text-xs text-gray-500 mb-2">Ambil dari Data OPD</div>
                    <div class="flex gap-2 mb-2">
                        <button type="button" class="px-3 py-1 rounded bg-indigo-600 text-white" @click="
                            $refs.pp_nama.value='{{ $opd->kepala_nama }}';
                            $refs.pp_nip.value='{{ $opd->kepala_nip }}';
                            $refs.pp_jabatan.value='{{ $opd->kepala_jabatan }}';
                        ">Gunakan Kepala OPD</button>
                    </div>
                @endif
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama</label>
                    <input x-ref="pp_nama" type="text" name="pihak_pertama[nama]" value="{{ $data['pihak_pertama']['nama'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">NIP</label>
                    <input x-ref="pp_nip" type="text" name="pihak_pertama[nip]" value="{{ $data['pihak_pertama']['nip'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Jabatan</label>
                    <input x-ref="pp_jabatan" type="text" name="pihak_pertama[jabatan]" value="{{ $data['pihak_pertama']['jabatan'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300" required>
                </div>
            </div>
            <div class="space-y-3">
                <h3 class="font-bold text-gray-800">PIHAK KEDUA</h3>
                @if(isset($opd) && $opd->pengurus_nama)
                    <div class="text-xs text-gray-500 mb-2">Ambil dari Data OPD</div>
                    <div class="flex gap-2 mb-2">
                        <button type="button" class="px-3 py-1 rounded bg-indigo-600 text-white" @click="
                            $refs.pk_nama.value='{{ $opd->pengurus_nama }}';
                            $refs.pk_nip.value='{{ $opd->pengurus_nip }}';
                            $refs.pk_jabatan.value='{{ $opd->pengurus_jabatan }}';
                        ">Gunakan Pengurus OPD</button>
                        @if(isset($opd->pengguna_nama) && $opd->pengguna_nama)
                        <button type="button" class="px-3 py-1 rounded bg-indigo-600 text-white" @click="
                            $refs.pk_nama.value='{{ $opd->pengguna_nama }}';
                            $refs.pk_nip.value='{{ $opd->pengguna_nip }}';
                            $refs.pk_jabatan.value='{{ $opd->pengguna_jabatan }}';
                        ">Gunakan Pengurus Barang Pengguna OPD</button>
                        @endif
                    </div>
                @endif
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama</label>
                    <input x-ref="pk_nama" type="text" name="pihak_kedua[nama]" value="{{ $data['pihak_kedua']['nama'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">NIP</label>
                    <input x-ref="pk_nip" type="text" name="pihak_kedua[nip]" value="{{ $data['pihak_kedua']['nip'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Jabatan</label>
                    <input x-ref="pk_jabatan" type="text" name="pihak_kedua[jabatan]" value="{{ $data['pihak_kedua']['jabatan'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300" required>
                </div>
            </div>
        </div>
        
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800">DAFTAR BARANG</h3>
                <div class="flex gap-2">
                    <button type="button" class="px-3 py-1 rounded bg-indigo-600 text-white" @click="addItem()">Tambah Baris</button>
                    <button type="button" class="px-3 py-1 rounded bg-gray-200 text-gray-800" @click="prefill()">Prefill</button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="report-table">
                    <thead>
                        <tr class="text-center font-bold">
                            <th class="px-2 py-1" style="width:32px;">No.</th>
                            <th class="px-2 py-1">Nama Barang</th>
                            <th class="px-2 py-1">Merk</th>
                            <th class="px-2 py-1">Type</th>
                            <th class="px-2 py-1">Nomor Polisi (Khusus Kendaraan)</th>
                            <th class="px-2 py-1">Tahun Pembelian</th>
                            <th class="px-2 py-1">Kondisi Barang</th>
                            <th class="px-2 py-1">Jumlah Barang</th>
                            <th class="px-2 py-1" style="width:48px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row,i) in items" :key="row._key">
                            <tr>
                                <td class="px-2 py-1 text-center" x-text="i+1"></td>
                                <td class="px-2 py-1">
                                    <input type="text" :name="'items['+i+'][nama]'" x-model="row.nama" class="w-full px-3 py-2 border rounded" required>
                                </td>
                                <td class="px-2 py-1">
                                    <input type="text" :name="'items['+i+'][merk]'" x-model="row.merk" class="w-full px-3 py-2 border rounded">
                                </td>
                                <td class="px-2 py-1">
                                    <input type="text" :name="'items['+i+'][tipe]'" x-model="row.tipe" class="w-full px-3 py-2 border rounded">
                                </td>
                                <td class="px-2 py-1">
                                    <input type="text" :name="'items['+i+'][identitas]'" x-model="row.identitas" class="w-full px-3 py-2 border rounded">
                                </td>
                                <td class="px-2 py-1">
                                    <input type="text" :name="'items['+i+'][tahun]'" x-model="row.tahun" class="w-full px-3 py-2 border rounded">
                                </td>
                                <td class="px-2 py-1">
                                    <input type="text" :name="'items['+i+'][kondisi]'" x-model="row.kondisi" class="w-full px-3 py-2 border rounded">
                                </td>
                                <td class="px-2 py-1">
                                    <input type="number" min="1" :name="'items['+i+'][jumlah]'" x-model="row.jumlah" class="w-full px-3 py-2 border rounded text-right" required>
                                </td>
                                <td class="px-2 py-1 text-right">
                                    <button type="button" class="px-3 py-1 rounded bg-red-500 text-white" @click="removeItem(i)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="flex items-center justify-end gap-2 pt-2">
            <button type="submit" formaction="{{ route('reports.pinjam.report') }}" formmethod="POST" class="btn btn-warning">
                <i class="fas fa-eye"></i> Preview Laporan
            </button>
            <button type="submit" formaction="{{ route('reports.pinjam.save') }}" formmethod="POST" class="btn btn-success">
                <i class="fas fa-save"></i> Perbarui
            </button>
        </div>
    </form>
@endsection
