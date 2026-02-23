@extends('layouts.admin')

@section('header', 'Nota Pesanan')
@section('subheader', 'Isi data nota dan buat laporan')

@section('content')
    <script>
        window.notaForm = function () {
            return {
                tahun: '{{ $data['tahun'] ?? now()->year }}',
                tanggal: '{{ $data['tanggal'] ?? now()->toDateString() }}',
                belanja: '{{ $data['belanja'] ?? ($categories->first()->name ?? '') }}',
                items: {!! json_encode(($data['items'] ?? [])) !!},
                products: {!! json_encode($products->map(fn($p) => ['id'=>$p->id,'name'=>$p->name,'unit'=>$p->unit,'price'=>$p->price ?? 0,'category_id'=>$p->category_id,'category_name'=>optional($p->category)->name])) !!},
                init() {
                    this.items = (this.items || []).filter(it => (String(it.name || '').trim() !== '')).map(it => {
                        const p = Number(it.price ?? 0);
                        const q = Number(it.qty ?? 0);
                        const price = Number.isFinite(p) ? Math.round(p) : (parseInt(String(it.price).replace(/\D+/g,''),10) || '');
                        const qty = Number.isFinite(q) ? Math.round(q) : (parseInt(String(it.qty).replace(/\D+/g,''),10) || '');
                        const total = (Number.isFinite(qty) && Number.isFinite(price)) ? qty * price : '';
                        return { ...it, price, qty, total };
                    });
                },
                addItem() {
                    this.items.push({ name: '', qty: '', unit: '', price: '', total: '' });
                    requestAnimationFrame(() => {
                        const sc = document.querySelector('main');
                        if (sc) sc.scrollTo({ top: sc.scrollHeight, behavior: 'smooth' });
                        const btn = document.getElementById('btn-add-row');
                        if (btn) btn.scrollIntoView({ block: 'end', behavior: 'smooth' });
                    });
                },
                removeItem(i) { this.items.splice(i, 1); },
                onProductChange(i, name) {
                    const p = this.products.find(x => x.name === name);
                    if (p) {
                        const raw = Number(p.price ?? 0);
                        const price = Number.isFinite(raw) ? Math.round(raw) : (parseInt(String(p.price).replace(/\\D+/g,''),10) || '');
                        this.items[i].unit = p.unit || '';
                        this.items[i].price = price;
                    } else {
                        this.items[i].unit = '';
                        this.items[i].price = '';
                    }
                    this.recalc(i);
                },
                productsByBelanja() {
                    const b = (this.belanja || '').trim();
                    if (!b) return this.products;
                    return this.products.filter(p => (p.category_name || '') === b);
                },
                recalc(i) {
                    const it = this.items[i] || {};
                    const qty = parseInt(it.qty, 10);
                    const price = parseInt(it.price, 10);
                    if (Number.isFinite(qty) && Number.isFinite(price)) {
                        this.items[i].total = qty * price;
                    } else {
                        this.items[i].total = '';
                    }
                }
            }
        }
    </script>

    <form method="POST" action="{{ session('nota_current_id') ? route('reports.nota.update', session('nota_current_id')) : route('reports.nota.save') }}" x-data="notaForm()" x-init="init()" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf
        <input type="hidden" name="id" value="{{ session('nota_current_id') }}">

        <div class="rounded-xl shadow-md border border-gray-200 bg-white p-6">
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="p-3 border border-gray-200 rounded-lg bg-white">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kegiatan</label>
                        <input type="text" name="kegiatan" list="opt-kegiatan" value="{{ old('kegiatan', $data['kegiatan'] ?? '') }}" class="w-full rounded-lg border-2 border-indigo-500 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                        <datalist id="opt-kegiatan">
                            @foreach(($options['kegiatan'] ?? []) as $v)
                                <option value="{{ $v }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="p-3 border border-gray-200 rounded-lg bg-white">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Sub. Kegiatan</label>
                        <input type="text" name="sub_kegiatan" list="opt-subkegiatan" value="{{ old('sub_kegiatan', $data['sub_kegiatan'] ?? '') }}" class="w-full rounded-lg border-2 border-indigo-500 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                        <datalist id="opt-subkegiatan">
                            @foreach(($options['sub_kegiatan'] ?? []) as $v)
                                <option value="{{ $v }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                    <div class="p-3 border border-gray-200 rounded-lg bg-white">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode Rekening</label>
                        <input type="text" name="rekening" list="opt-rekening" value="{{ old('rekening', $data['rekening'] ?? '') }}" class="w-full rounded-lg border-2 border-indigo-500 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                        <datalist id="opt-rekening">
                            @foreach(($options['rekening'] ?? []) as $v)
                                <option value="{{ $v }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="p-3 border border-gray-200 rounded-lg bg-white">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tahun</label>
                        <input type="number" min="2000" max="2100" name="tahun" x-model="tahun" class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    </div>
                    <div class="p-3 border border-gray-200 rounded-lg bg-white">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nomor Nota Pesanan</label>
                        <input type="text" name="nomor" value="{{ old('nomor', $data['nomor'] ?? '') }}" class="w-full rounded-lg border-2 border-indigo-500 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div class="p-3 border border-gray-200 rounded-lg bg-white">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Nota Pesanan</label>
                        <input type="date" name="tanggal" x-model="tanggal" class="w-full rounded-lg border-2 border-indigo-500 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="p-3 border border-gray-200 rounded-lg bg-white">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Belanja</label>
                        <select name="belanja" x-model="belanja" class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            @php $names = $categories->pluck('name')->toArray(); @endphp
                            @foreach($names as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="p-3 border border-gray-200 rounded-lg bg-white">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Penyedia</label>
                        <select name="supplier_id" class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            <option value="">-- Pilih Penyedia --</option>
                            @foreach ($suppliers as $s)
                                <option value="{{ $s->id }}" {{ (($data['penyedia']['toko'] ?? '') === $s->name) ? 'selected' : '' }}>
                                    {{ $s->name }} — {{ $s->dir }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-700">Inputan Pengadaan</h3>
        </div>

        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-xs uppercase font-bold">
                    <tr>
                        <th class="px-3 py-2 text-center">No</th>
                        <th class="px-3 py-2">Jenis Bahan/Alat (Barang)</th>
                        <th class="px-3 py-2">Kuantitas</th>
                        <th class="px-3 py-2">Satuan</th>
                        <th class="px-3 py-2">Harga Satuan (Rp)</th>
                        <th class="px-3 py-2">Total Harga (Rp)</th>
                        <th class="px-3 py-2 text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, idx) in items" :key="idx">
                        <tr class="border-t">
                            <td class="px-3 py-2 text-center font-bold" x-text="idx + 1"></td>
                            <td class="px-3 py-2">
                                <select :name="`items[${idx}][name]`" x-model="item.name" @change="onProductChange(idx, item.name)" class="w-full px-3 py-2 border rounded">
                                    <option value="">-- Pilih Produk --</option>
                                    <template x-for="p in productsByBelanja()">
                                        <option :value="p.name" x-text="p.name"></option>
                                    </template>
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" min="1" :name="`items[${idx}][qty]`" x-model="item.qty" @input="recalc(idx)" placeholder="-" class="w-full px-3 py-2 border rounded">
                            </td>
                            <td class="px-3 py-2">
                                <input type="text" :name="`items[${idx}][unit]`" x-model="item.unit" placeholder="-" class="w-full px-3 py-2 border rounded" readonly>
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" min="0" :name="`items[${idx}][price]`" x-model="item.price" @input="recalc(idx)" placeholder="-" class="w-full px-3 py-2 border rounded">
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" min="0" :name="`items[${idx}][total]`" :value="item.total" placeholder="-" class="w-full px-3 py-2 border rounded" readonly>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <button type="button" @click="removeItem(idx)" class="inline-flex items-center gap-1 text-red-600 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center gap-2">
            <button type="button" id="btn-add-row" @click="addItem()" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-indigo-600 text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Tambah Baris
            </button>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-green-600 text-white hover:bg-green-700">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <button type="submit" formmethod="POST" formaction="{{ route('reports.nota.report') }}" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-orange-500 text-white hover:bg-orange-600">
                    <i class="fas fa-file-alt"></i> Preview Laporan
                </button>
            </div>
        </div>
    </form>
@endsection
