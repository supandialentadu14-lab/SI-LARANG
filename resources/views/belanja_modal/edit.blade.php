@extends('layouts.admin')

@section('title', 'Edit Kontrak Belanja Modal')
@section('header', 'Belanja Modal')

@section('content')
    <script>
        window.belanjaModalForm = function () {
            return {
                tahun: '{{ $data['tahun'] ?? now()->year }}',
                items: {!! json_encode(($data['items'] ?? [])) !!},
                init() {
                    const seen = new Set();
                    this.items = (this.items || []).filter((row) => {
                        const key = `${row.nama_kegiatan || ''}|${row.pekerjaan || ''}|${row.nilai_kontrak || ''}|${row.tanggal_mulai || ''}|${row.tanggal_akhir || ''}|${row.uang_muka || ''}|${row.termin1 || ''}|${row.termin2 || ''}|${row.termin3 || ''}|${row.termin4 || ''}|${row.status || ''}`;
                        if (seen.has(key)) return false;
                        seen.add(key);
                        return true;
                    });
                },
                addItem() {
                    this.items.push({
                        nama_kegiatan: '',
                        pekerjaan: '',
                        nilai_kontrak: 0,
                        tanggal_mulai: '',
                        tanggal_akhir: '',
                        uang_muka: 0,
                        termin1: 0,
                        termin2: 0,
                        termin3: 0,
                        termin4: 0,
                        total: 0,
                        status: ''
                    });
                },
                removeItem(i) { this.items.splice(i, 1); },
                focusRow(i) {
                    this.$nextTick(() => {
                        const el = this.$refs[`row_${i}_kegiatan`];
                        if (el) el.focus();
                    });
                },
                recalc(i) {
                    const it = this.items[i] || {};
                    const toInt = v => parseInt(v || 0, 10);
                    it.total = toInt(it.uang_muka) + toInt(it.termin1) + toInt(it.termin2) + toInt(it.termin3) + toInt(it.termin4);
                    this.items[i] = it;
                }
            }
        }
    </script>

    <div class="max-w-full mx-auto">
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-orange-50">
                <h6 class="font-bold text-blue-700">Form Belanja Modal</h6>
            </div>

            <form method="POST" action="{{ route('reports.belanja.modal.save') }}" x-data="belanjaModalForm()" x-init="init()" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="id" value="{{ session('belanja_modal_current_id') }}">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tahun</label>
                        <input type="number" min="2000" max="2100" name="tahun" x-model="tahun" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">OPD</label>
                        <input type="text" name="opd" value="{{ $master['opd']['nama'] ?? ($opd->nama_opd ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                    </div>
                    <div class="flex justify-end">
                        <button type="button" @click="addItem()" class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-bold shadow hover:bg-indigo-700 transition">
                            <i class="fas fa-plus mr-1"></i> Tambah Baris
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto border rounded-lg">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-100 text-xs uppercase font-bold">
                            <tr>
                                <th class="px-3 py-2">Nama Kegiatan</th>
                                <th class="px-3 py-2">Pekerjaan</th>
                                <th class="px-3 py-2 w-32">Nilai Kontrak</th>
                                <th class="px-3 py-2 w-32">Mulai</th>
                                <th class="px-3 py-2 w-32">Akhir</th>
                                <th class="px-3 py-2 w-24">Uang Muka</th>
                                <th class="px-3 py-2 w-24">Termin 1</th>
                                <th class="px-3 py-2 w-24">Termin 2</th>
                                <th class="px-3 py-2 w-24">Termin 3</th>
                                <th class="px-3 py-2 w-24">Termin 4</th>
                                <th class="px-3 py-2 w-24">Total</th>
                                <th class="px-3 py-2 w-24">Status</th>
                                <th class="px-3 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, i) in items" :key="i">
                                <tr class="border-t hover:bg-gray-50 transition">
                                    <td class="p-2"><input type="text" :name="`items[${i}][nama_kegiatan]`" x-model="item.nama_kegiatan" :x-ref="`row_${i}_kegiatan`" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500"></td>
                                    <td class="p-2"><input type="text" :name="`items[${i}][pekerjaan]`" x-model="item.pekerjaan" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500"></td>
                                    <td class="p-2"><input type="number" :name="`items[${i}][nilai_kontrak]`" x-model="item.nilai_kontrak" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 text-right"></td>
                                    <td class="p-2"><input type="date" :name="`items[${i}][tanggal_mulai]`" x-model="item.tanggal_mulai" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500"></td>
                                    <td class="p-2"><input type="date" :name="`items[${i}][tanggal_akhir]`" x-model="item.tanggal_akhir" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500"></td>
                                    <td class="p-2"><input type="number" :name="`items[${i}][uang_muka]`" x-model="item.uang_muka" @input="recalc(i)" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 text-right"></td>
                                    <td class="p-2"><input type="number" :name="`items[${i}][termin1]`" x-model="item.termin1" @input="recalc(i)" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 text-right"></td>
                                    <td class="p-2"><input type="number" :name="`items[${i}][termin2]`" x-model="item.termin2" @input="recalc(i)" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 text-right"></td>
                                    <td class="p-2"><input type="number" :name="`items[${i}][termin3]`" x-model="item.termin3" @input="recalc(i)" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 text-right"></td>
                                    <td class="p-2"><input type="number" :name="`items[${i}][termin4]`" x-model="item.termin4" @input="recalc(i)" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 text-right"></td>
                                    <td class="p-2 text-right font-mono text-xs" x-text="item.total"></td>
                                    <td class="p-2"><input type="text" :name="`items[${i}][status]`" x-model="item.status" class="w-full rounded border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500"></td>
                                    <td class="p-2 text-center">
                                        <button type="button" @click="removeItem(i)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                @include('partials.form-actions', [
                    'backRoute' => route('reports.belanja.modal.list'),
                    'previewRoute' => route('reports.belanja.modal.report'),
                ])
            </form>
        </div>
    </div>
@endsection