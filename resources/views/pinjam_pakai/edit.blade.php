@extends('layouts.admin')

@section('title', 'Edit Berita Acara Pinjam Pakai')
@section('header', 'Berita Acara Pinjam Pakai')

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

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-orange-50">
                <h6 class="font-bold text-blue-700">Form Berita Acara Pinjam Pakai</h6>
            </div>

            <form method="POST" action="{{ route('reports.pinjam.update', $id) }}" x-data="formData()" x-init="init()" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="id" value="{{ $id }}">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                   <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nomor</label>
                        <input type="text" name="nomor" value="{{ $data['nomor'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal</label>
                        <input x-ref="tanggal" @change="updatePembuka()" type="date" name="tanggal" value="{{ $data['tanggal'] ?? now()->toDateString() }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tempat</label>
                        <input x-ref="tempat" @input="updatePembuka()" type="text" name="tempat" value="{{ $data['tempat'] ?? ($opd->nama_opd ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Narasi Pembuka</label>
                    <textarea x-ref="pembuka" name="pembuka" rows="4" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">{{ $data['pembuka'] ?? '' }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h3 class="font-bold text-blue-700 border-b pb-2">PIHAK PERTAMA (Yang Menyerahkan)</h3>
                        @if(isset($opd) && $opd->kepala_nama)
                            <div class="flex gap-2 mb-2">
                                <button type="button" class="px-3 py-1 rounded bg-indigo-100 text-indigo-700 text-xs font-bold hover:bg-indigo-200 transition" @click="
                                    $refs.pp_nama.value='{{ $opd->kepala_nama }}';
                                    $refs.pp_nip.value='{{ $opd->kepala_nip }}';
                                    $refs.pp_jabatan.value='{{ $opd->kepala_jabatan }}';
                                ">Gunakan Kepala OPD</button>
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama</label>
                            <input x-ref="pp_nama" type="text" name="pihak_pertama[nama]" value="{{ $data['pihak_pertama']['nama'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">NIP</label>
                            <input x-ref="pp_nip" type="text" name="pihak_pertama[nip]" value="{{ $data['pihak_pertama']['nip'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Jabatan</label>
                            <input x-ref="pp_jabatan" type="text" name="pihak_pertama[jabatan]" value="{{ $data['pihak_pertama']['jabatan'] ?? 'Kepala Dinas' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h3 class="font-bold text-blue-700 border-b pb-2">PIHAK KEDUA (Peminjam)</h3>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama</label>
                            <input type="text" name="pihak_kedua[nama]" value="{{ $data['pihak_kedua']['nama'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">NIP</label>
                            <input type="text" name="pihak_kedua[nip]" value="{{ $data['pihak_kedua']['nip'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Jabatan</label>
                            <input type="text" name="pihak_kedua[jabatan]" value="{{ $data['pihak_kedua']['jabatan'] ?? 'Peminjam' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-bold text-gray-700">Daftar Barang</label>
                        <div class="flex gap-2">
                            <button type="button" @click="prefill()" class="px-3 py-1 rounded bg-green-100 text-green-700 text-xs font-bold hover:bg-green-200 transition">
                                <i class="fas fa-sync mr-1"></i> Isi Ulang
                            </button>
                            <button type="button" @click="addItem()" class="px-3 py-1 rounded bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 transition">
                                <i class="fas fa-plus mr-1"></i> Tambah
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="w-full text-sm text-left text-gray-700">
                            <thead class="bg-gray-100 text-xs uppercase font-bold">
                                <tr>
                                    <th class="px-3 py-2">Nama / Jenis Barang</th>
                                    <th class="px-3 py-2">Merk / Type</th>
                                    <th class="px-3 py-2">No. Pabrik / Chasis / Mesin</th>
                                    <th class="px-3 py-2 w-24">Tahun</th>
                                    <th class="px-3 py-2 w-32">Kondisi</th>
                                    <th class="px-3 py-2 w-24">Jumlah</th>
                                    <th class="px-3 py-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, i) in items" :key="item._key">
                                    <tr class="border-t hover:bg-gray-50 transition">
                                        <td class="p-2"><input type="text" :name="`items[${i}][nama]`" x-model="item.nama" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500"></td>
                                        <td class="p-2"><input type="text" :name="`items[${i}][merk]`" x-model="item.merk" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500"></td>
                                        <td class="p-2"><input type="text" :name="`items[${i}][identitas]`" x-model="item.identitas" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500"></td>
                                        <td class="p-2"><input type="text" :name="`items[${i}][tahun]`" x-model="item.tahun" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500"></td>
                                        <td class="p-2"><input type="text" :name="`items[${i}][kondisi]`" x-model="item.kondisi" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500"></td>
                                        <td class="p-2"><input type="text" :name="`items[${i}][jumlah]`" x-model="item.jumlah" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500"></td>
                                        <td class="p-2 text-center">
                                            <button type="button" @click="removeItem(i)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                @include('partials.form-actions', [
                    'backRoute' => route('reports.pinjam.list'),
                    'previewRoute' => route('reports.pinjam.report'),
                ])
            </form>
        </div>
    </div>
@endsection