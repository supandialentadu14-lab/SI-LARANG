@extends('layouts.admin')

@section('header', 'Edit Nota Pesanan')
@section('subheader', 'Perbarui data nota dan buat laporan')

@section('content')
    <script>
        window.notaForm = function () {
            return {
                tahun: '{{ $data['tahun'] ?? now()->year }}',
                tanggal: '{{ $data['tanggal'] ?? now()->toDateString() }}',
                belanja: '{{ $data['belanja'] ?? ($categories->first()->name ?? '') }}',
                items: {!! json_encode(($data['items'] ?? [])) !!},
                nextKey: 1,
                products: {!! json_encode($products->map(fn($p) => ['id'=>$p->id,'name'=>$p->name,'unit'=>$p->unit,'price'=>$p->price ?? 0,'category_id'=>$p->category_id,'category_name'=>optional($p->category)->name])) !!},
                ensureKeys() {
                    this.items = (this.items || []).map(it => ({ ...it, _key: it._key || (this.nextKey++) }));
                },
                init() {
                    const normalizeName = (val) => {
                        const s = String(val ?? '').trim();
                        if (!s) return '';
                        if (/^\d+$/.test(s)) {
                            const pid = parseInt(s, 10);
                            const p = this.products.find(x => String(x.id) === String(pid));
                            return p ? (p.name || '') : '';
                        }
                        return s;
                    };
                    this.items = (this.items || []).map(it => {
                        const name = normalizeName(it.name ?? it.product_id ?? '');
                        let unit = it.unit ?? '';
                        let priceRaw = it.price ?? '';
                        const prod = this.products.find(x => (x.name || '') === name);
                        if (prod) {
                            unit = unit || (prod.unit || '');
                            if (priceRaw === '' || priceRaw === null || priceRaw === undefined) {
                                priceRaw = prod.price ?? '';
                            }
                        }
                        const p = Number(priceRaw ?? 0);
                        const q = Number(it.qty ?? 0);
                        const price = Number.isFinite(p) ? Math.round(p) : (parseInt(String(priceRaw).replace(/\D+/g,''),10) || '');
                        const qty = Number.isFinite(q) ? Math.round(q) : (parseInt(String(it.qty).replace(/\D+/g,''),10) || '');
                        const total = (Number.isFinite(qty) && Number.isFinite(price)) ? qty * price : '';
                        return { ...it, name, unit, price, qty, total };
                    }).filter(it => (String(it.name || '').trim() !== ''));
                    const by = {};
                    (this.items || []).forEach(it => {
                        const nm = String(it.name || '').trim();
                        if (!nm || /^\d+$/.test(nm)) return;
                        const qty = parseInt(it.qty,10) || 0;
                        const price = parseInt(it.price,10) || 0;
                        const unit = String(it.unit || '').trim().toLowerCase();
                        const key = `${nm.toLowerCase()}|${qty}|${unit}|${price}`;
                        if (!by[key]) {
                            by[key] = it;
                        }
                    });
                    this.items = Object.values(by);
                    this.ensureKeys();
                },
                addItem() {
                    this.items.push({ _key: this.nextKey++, name: '', qty: '', unit: '', price: '', total: '' });
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
                visibleIdxs() {
                    const out = [];
                    (this.items || []).forEach((it, i) => {
                        if (String(it.name || '').trim() !== '') out.push(i);
                    });
                    return out;
                },
                rows() {
                    const idxs = this.visibleIdxs();
                    return idxs.map(i => ({ i, it: this.items[i] }));
                },
                optionsForRow(i) {
                    let list = this.productsByBelanja();
                    const nm = (this.items[i] || {}).name || '';
                    if (nm && !list.some(p => (p.name || '') === nm)) {
                        list = [{ name: nm }].concat(list);
                    }
                    return list.filter((p, idx, arr) => idx === arr.findIndex(q => (q.name || '') === (p.name || '')));
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
                    <template x-for="(row, ridx) in rows()" :key="row.it._key">
                        <tr class="border-t">
                            <td class="px-3 py-2 text-center font-bold" x-text="ridx + 1"></td>
                            <td class="px-3 py-2">
                                <select :name="`items[${row.i}][name]`" x-model="items[row.i].name" @change="onProductChange(row.i, items[row.i].name)" class="w-full px-3 py-2 border rounded">
                                    <option value="">-- Pilih Produk --</option>
                                    <template x-for="p in optionsForRow(row.i)">
                                        <option :value="p.name" x-text="p.name"></option>
                                    </template>
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" min="1" :name="`items[${row.i}][qty]`" x-model="items[row.i].qty" @input="recalc(row.i)" placeholder="-" class="w-full px-3 py-2 border rounded">
                            </td>
                            <td class="px-3 py-2">
                                <input type="text" :name="`items[${row.i}][unit]`" x-model="items[row.i].unit" placeholder="-" class="w-full px-3 py-2 border rounded" readonly>
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" min="0" :name="`items[${row.i}][price]`" x-model="items[row.i].price" @input="recalc(row.i)" placeholder="-" class="w-full px-3 py-2 border rounded">
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" min="0" :name="`items[${row.i}][total]`" :value="items[row.i].total" placeholder="-" class="w-full px-3 py-2 border rounded" readonly>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <button type="button" @click="removeItem(row.i)" class="inline-flex items-center gap-1 text-red-600 hover:text-red-700">
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
                <button type="submit" class="btn btn-success text-white">
                    <i class="fas fa-save"></i> Perbarui
                </button>
                <button type="submit" formmethod="POST" formaction="{{ route('reports.nota.report') }}" class="btn btn-warning">
                    <i class="fas fa-file-alt"></i> Preview Laporan
                </button>
            </div>
        </div>
    </form>
@endsection
