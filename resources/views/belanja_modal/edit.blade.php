@extends('layouts.admin')

@section('title', 'Edit Kontrak Belanja Modal')
@section('header', 'Belanja Modal')
@section('subheader', 'Edit data kontrak belanja modal')

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

    <form method="POST" action="{{ route('reports.belanja.modal.save') }}" x-data="belanjaModalForm()" x-init="init()" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf
        <input type="hidden" name="id" value="{{ session('belanja_modal_current_id') }}">

        <div class="rounded-xl shadow-md border border-gray-200 bg-white p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tahun</label>
                    <input type="number" min="2000" max="2100" name="tahun" x-model="tahun" class="w-full rounded-lg border-2 border-indigo-500 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">OPD</label>
                    <input type="text" name="opd" value="{{ $master['opd']['nama'] ?? ($opd->nama_opd ?? '') }}" class="w-full rounded-lg border-2 border-indigo-500 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" @click="addItem()" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-indigo-600 text-white hover:bg-indigo-700">
                        <i class="fas fa-plus"></i>
                        Tambah Baris
                    </button>
                    <button type="submit" formmethod="POST" class="btn btn-success text-white">
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>
                    <button type="submit" formmethod="POST" formaction="{{ route('reports.belanja.modal.report') }}" class="btn btn-warning">
                        <i class="fas fa-file-alt"></i>
                        Preview Laporan
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-xs uppercase font-bold">
                    <tr>
                        <th class="px-3 py-2 text-center">No</th>
                        <th class="px-3 py-2">Nama Kegiatan</th>
                        <th class="px-3 py-2">Pekerjaan</th>
                        <th class="px-3 py-2">Nilai Kontrak (Rp)</th>
                        <th class="px-3 py-2">Tanggal Mulai</th>
                        <th class="px-3 py-2">Tanggal Akhir Pekerjaan</th>
                        <th class="px-3 py-2">Uang Muka (Rp)</th>
                        <th class="px-3 py-2">Termin I (Rp)</th>
                        <th class="px-3 py-2">Termin II (Rp)</th>
                        <th class="px-3 py-2">Termin III (Rp)</th>
                        <th class="px-3 py-2">Termin IV (Rp)</th>
                        <th class="px-3 py-2">Total Pembayaran (Rp)</th>
                        <th class="px-3 py-2">Status Pekerjaan</th>
                        <th class="px-3 py-2 text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, idx) in items" :key="idx">
                        <tr class="border-t">
                            <td class="px-3 py-2 text-center font-bold" x-text="idx + 1"></td>
                            <td class="px-3 py-2"><input x-ref="row_${idx}_kegiatan" type="text" :name="`items[${idx}][nama_kegiatan]`" x-model="item.nama_kegiatan" class="w-full px-3 py-2 border rounded" required></td>
                            <td class="px-3 py-2"><input type="text" :name="`items[${idx}][pekerjaan]`" x-model="item.pekerjaan" class="w-full px-3 py-2 border rounded" required></td>
                            <td class="px-3 py-2"><input type="number" min="0" :name="`items[${idx}][nilai_kontrak]`" x-model="item.nilai_kontrak" class="w-full px-3 py-2 border rounded"></td>
                            <td class="px-3 py-2"><input type="date" :name="`items[${idx}][tanggal_mulai]`" x-model="item.tanggal_mulai" class="w-full px-3 py-2 border rounded"></td>
                            <td class="px-3 py-2"><input type="date" :name="`items[${idx}][tanggal_akhir]`" x-model="item.tanggal_akhir" class="w-full px-3 py-2 border rounded"></td>
                            <td class="px-3 py-2"><input type="number" min="0" :name="`items[${idx}][uang_muka]`" x-model="item.uang_muka" @input="recalc(idx)" class="w-full px-3 py-2 border rounded"></td>
                            <td class="px-3 py-2"><input type="number" min="0" :name="`items[${idx}][termin1]`" x-model="item.termin1" @input="recalc(idx)" class="w-full px-3 py-2 border rounded"></td>
                            <td class="px-3 py-2"><input type="number" min="0" :name="`items[${idx}][termin2]`" x-model="item.termin2" @input="recalc(idx)" class="w-full px-3 py-2 border rounded"></td>
                            <td class="px-3 py-2"><input type="number" min="0" :name="`items[${idx}][termin3]`" x-model="item.termin3" @input="recalc(idx)" class="w-full px-3 py-2 border rounded"></td>
                            <td class="px-3 py-2"><input type="number" min="0" :name="`items[${idx}][termin4]`" x-model="item.termin4" @input="recalc(idx)" class="w-full px-3 py-2 border rounded"></td>
                            <td class="px-3 py-2"><input type="number" min="0" :name="`items[${idx}][total]`" :value="item.total" class="w-full px-3 py-2 border rounded" readonly></td>
                            <td class="px-3 py-2">
                                <select :name="`items[${idx}][status]`" x-model="item.status" class="w-full px-3 py-2 border rounded">
                                    <option value="">-</option>
                                    <option value="Proses">Proses</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <button type="button" @click="removeItem(idx)" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs text-red-600 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </form>
@endsection
