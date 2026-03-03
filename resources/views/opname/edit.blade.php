@extends('layouts.admin')

@section('title', 'Edit Berita Acara Stock Opname')
@section('header', 'Berita Acara Stock Opname Persediaan Barang Habis Pakai')

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
                        const parts = v.split('-');
                        const year = parseInt(parts[0], 10);
                        const monthIndex = parseInt(parts[1], 10) - 1;
                        const day = parseInt(parts[2], 10);
                        const d = new Date(year, monthIndex, day);
                        
                        const hariMap = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        const bulanMap = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        
                        const hari = hariMap[d.getDay()];
                        const bulan = bulanMap[d.getMonth()];
                        const tanggal = day;
                        const tahun = year;
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
                        const cap = s => s.replace(/\b\w/g, c => c.toUpperCase());
                        this.$refs.pembuka.value =
                            `Pada hari ini ${hari} Tanggal ${cap(tanggalKata)} Bulan ${bulan} Tahun ${cap(tahunKata)}, yang bertanda tangan di bawah ini:`;
                    } catch (e) {}
                }
            }
        }
    </script>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-slate-800">
                <h6 class="font-bold text-white">Form Berita Acara Opname</h6>
            </div>

            <form method="POST" action="{{ route('reports.opname.update', $id) }}" x-data="opnameForm()" x-init="$nextTick(() => { updatePembuka(); })" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="id" value="{{ $id }}">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nomor</label>
                        <input type="text" name="nomor" value="{{ $data['nomor'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                        <p class="text-xs text-gray-500 mt-1">Masukkan nomor urut (contoh: 001). Akan otomatis diformat.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal</label>
                        <input x-ref="tanggal" @change="updatePembuka()" type="date" name="tanggal" value="{{ $data['tanggal'] ?? now()->toDateString() }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tempat</label>
                        <input x-ref="tempat" @input="updatePembuka()" type="text" name="tempat" value="{{ $data['tempat'] ?? ($opd->nama_opd ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Narasi Pembuka</label>
                    <textarea x-ref="pembuka" name="pembuka" rows="4" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">{{ $data['pembuka'] ?? '' }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h3 class="font-bold text-white border-b pb-2">Pihak Pertama (Kepala Dinas)</h3>
                        @if(isset($opd) && $opd->kepala_nama)
                            <div class="flex gap-2 mb-2">
                                <button type="button" class="px-3 py-1 rounded bg-indigo-100 text-indigo-700 text-xs font-bold hover:bg-indigo-200 transition" @click="
                                    $refs.pp_nama.value='{{ $opd->kepala_nama }}';
                                    $refs.pp_nip.value='{{ $opd->kepala_nip }}';
                                    $refs.pp_jabatan.value='{{ $opd->kepala_jabatan }}';
                                ">Isi dari Data OPD</button>
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama</label>
                            <input x-ref="pp_nama" type="text" name="pihak_pertama[nama]" value="{{ $data['pihak_pertama']['nama'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">NIP</label>
                            <input x-ref="pp_nip" type="text" name="pihak_pertama[nip]" value="{{ $data['pihak_pertama']['nip'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Jabatan</label>
                            <input x-ref="pp_jabatan" type="text" name="pihak_pertama[jabatan]" value="{{ $data['pihak_pertama']['jabatan'] ?? 'Mengetahui, Kepala Dinas Komunikasi dan Informatika' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h3 class="font-bold text-white border-b pb-2">Pihak Kedua (Pengurus Barang)</h3>
                        @if(isset($opd) && $opd->pengurus_nama)
                            <div class="flex gap-2 mb-2">
                                <button type="button" class="px-3 py-1 rounded bg-indigo-100 text-indigo-700 text-xs font-bold hover:bg-indigo-200 transition" @click="
                                    $refs.pk_nama.value='{{ $opd->pengurus_nama }}';
                                    $refs.pk_nip.value='{{ $opd->pengurus_nip }}';
                                    $refs.pk_jabatan.value='{{ $opd->pengurus_jabatan }}';
                                ">Isi dari Data OPD</button>
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama</label>
                            <input x-ref="pk_nama" type="text" name="pihak_kedua[nama]" value="{{ $data['pihak_kedua']['nama'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">NIP</label>
                            <input x-ref="pk_nip" type="text" name="pihak_kedua[nip]" value="{{ $data['pihak_kedua']['nip'] ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Jabatan</label>
                            <input x-ref="pk_jabatan" type="text" name="pihak_kedua[jabatan]" value="{{ $data['pihak_kedua']['jabatan'] ?? 'Yang Melaksanakan Stock Opname, Pengurus Barang Pengguna' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                        </div>
                    </div>
                </div>

                @include('partials.form-actions', [
                    'backRoute' => route('reports.opname.list'),
                    'previewRoute' => route('reports.opname.report'),
                ])
            </form>
        </div>
    </div>
@endsection
