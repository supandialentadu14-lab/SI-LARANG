@extends('layouts.admin')

@section('title', 'Berita Acara Stock Opname')
@section('header', 'Berita Acara Stock Opname Persediaan Barang Habis Pakai')
@section('subheader', 'Isi data atau gunakan prefill dari persediaan')

@section('content')
    <script>
        window.opnameForm = function () {
            return {
                items: {!! json_encode(($data['items'] ?? [])) !!},
                onDateChange() { this.updatePembuka(); },
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
                            const h = [\"\",\"satu\",\"dua\",\"tiga\",\"empat\",\"lima\",\"enam\",\"tujuh\",\"delapan\",\"sembilan\",\"sepuluh\",\"sebelas\"];
                            const cap = s => s.replace(/\\b\\w/g, c => c.toUpperCase());
                            const w = (v) => {
                                if (v < 12) return h[v];
                                if (v < 20) return w(v-10) + \" belas\";
                                if (v < 100) return w(Math.floor(v/10)) + \" puluh \" + w(v%10);
                                if (v < 200) return \"seratus \" + w(v-100);
                                if (v < 1000) return w(Math.floor(v/100)) + \" ratus \" + w(v%100);
                                if (v < 2000) return \"seribu \" + w(v-1000);
                                if (v < 1000000) return w(Math.floor(v/1000)) + \" ribu \" + w(v%1000);
                                if (v < 1000000000) return w(Math.floor(v/1000000)) + \" juta \" + w(v%1000000);
                                return String(v);
                            };
                            return cap(w(n).trim());
                        };
                        const tanggalKata = toWords(tanggal);
                        const tahunKata = toWords(tahun);
                        this.$refs.pembuka.value =
                            `Pada hari ini ${hari} Tanggal ${tanggalKata} Bulan ${bulan} Tahun ${tahunKata}, bertempat di ${tempat} Kabupaten Bolaang Mongondow Selatan, yang bertanda tangan dibawah ini:`;
                    } catch (e) {}
                }
            }
        }
    </script>
    <form method="POST" action="{{ route('reports.opname.report') }}" x-data="opnameForm()" x-init="$nextTick(() => updatePembuka())" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nomor</label>
                <input type="text" name="nomor" value="{{ $data['nomor'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal</label>
                <input x-ref="tanggal" @change="onDateChange()" type="date" name="tanggal" value="{{ $data['tanggal'] ?? now()->toDateString() }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tempat</label>
                <input x-ref="tempat" @input="updatePembuka()" type="text" name="tempat" value="{{ $data['tempat'] ?? ($opd->nama_opd ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Narasi Pembuka</label>
            <textarea x-ref="pembuka" name="pembuka" rows="4" class="w-full px-4 py-2 rounded-lg border border-gray-300">{{ $data['pembuka'] ?? ('Pada hari ini ' . \Illuminate\Support\Carbon::parse($data['tanggal'] ?? now())->translatedFormat('l d F Y') . ', bertempat di ' . (($opd->nama_opd ?? null) ?: ($data['tempat'] ?? '-')) . ' Kabupaten Bolaang Mongondow Selatan, yang bertanda tangan dibawah ini:') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <h3 class="font-bold text-gray-800">Kepala Dinas</h3>
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
                    <input x-ref="pp_nama" type="text" name="pihak_pertama[nama]" value="{{ $data['pihak_pertama']['nama'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">NIP</label>
                    <input x-ref="pp_nip" type="text" name="pihak_pertama[nip]" value="{{ $data['pihak_pertama']['nip'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Jabatan</label>
                    <input x-ref="pp_jabatan" type="text" name="pihak_pertama[jabatan]" value="{{ $data['pihak_pertama']['jabatan'] ?? 'Mengetahui, Kepala Dinas Komunikasi dan Informatika' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
            </div>
            <div class="space-y-3">
                <h3 class="font-bold text-gray-800">Pengurus Barang Pengguna</h3>
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
                    <input x-ref="pk_nama" type="text" name="pihak_kedua[nama]" value="{{ $data['pihak_kedua']['nama'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">NIP</label>
                    <input x-ref="pk_nip" type="text" name="pihak_kedua[nip]" value="{{ $data['pihak_kedua']['nip'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Jabatan</label>
                    <input x-ref="pk_jabatan" type="text" name="pihak_kedua[jabatan]" value="{{ $data['pihak_kedua']['jabatan'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2">
            <button type="submit" formaction="{{ route('reports.opname.report') }}" formmethod="POST" class="btn btn-warning">
                <i class="fas fa-eye"></i> Preview Laporan
            </button>
            <button type="submit" formaction="{{ route('reports.opname.save') }}" formmethod="POST" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </form>
@endsection
