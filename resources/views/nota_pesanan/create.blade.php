@extends('layouts.admin')

@section('header', 'Nota Pesanan')

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
                },
                removeItem(i) { this.items.splice(i, 1); },
                onProductChange(i, name) {
                    const p = this.products.find(x => x.name === name);
                    if (p) {
                        const raw = Number(p.price ?? 0);
                        const price = Number.isFinite(raw) ? Math.round(raw) : (parseInt(String(p.price).replace(/\D+/g,''),10) || '');
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

    <div class="max-w-full mx-auto">
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-slate-800">
                <h6 class="font-bold text-white">Form Nota Pesanan</h6>
            </div>

            <form method="POST" action="{{ session('nota_current_id') ? route('reports.nota.update', session('nota_current_id')) : route('reports.nota.save') }}" x-data="notaForm()" x-init="init()" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="id" value="{{ session('nota_current_id') }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kegiatan</label>
                        <input type="text" name="kegiatan" list="opt-kegiatan" value="{{ old('kegiatan', $data['kegiatan'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                        <datalist id="opt-kegiatan">
                            @foreach(($options['kegiatan'] ?? []) as $v)
                                <option value="{{ $v }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Sub. Kegiatan</label>
                        <input type="text" name="sub_kegiatan" list="opt-subkegiatan" value="{{ old('sub_kegiatan', $data['sub_kegiatan'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                        <datalist id="opt-subkegiatan">
                            @foreach(($options['sub_kegiatan'] ?? []) as $v)
                                <option value="{{ $v }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kode Rekening</label>
                        <input type="text" name="rekening" list="opt-rekening" value="{{ old('rekening', $data['rekening'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition">
                        <datalist id="opt-rekening">
                            @foreach(($options['rekening'] ?? []) as $v)
                                <option value="{{ $v }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Nota</label>
                        <input type="text" name="nomor" value="{{ old('nomor', $data['nomor'] ?? '') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Nota</label>
                        <input type="date" name="tanggal" x-model="tanggal" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Jenis Belanja</label>
                        <select name="belanja" x-model="belanja" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                            <option value="">-- Pilih Jenis Belanja --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Penyedia</label>
                        <select name="supplier_id" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" required>
                            <option value="">-- Pilih Penyedia --</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ (old('supplier_id', $data['supplier_id'] ?? '') == $s->id) ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-bold text-gray-700">Rincian Barang</label>
                        <button type="button" @click="addItem()" class="px-3 py-1 rounded bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 transition">
                            <i class="fas fa-plus mr-1"></i> Tambah Barang
                        </button>
                    </div>
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="w-full text-sm text-left text-gray-700">
                            <thead class="bg-gray-100 text-xs uppercase font-bold">
                                <tr>
                                    <th class="px-3 py-2">Nama Barang</th>
                                    <th class="px-3 py-2 w-24">Volume</th>
                                    <th class="px-3 py-2 w-24">Satuan</th>
                                    <th class="px-3 py-2 w-32">Harga (Rp)</th>
                                    <th class="px-3 py-2 w-32">Jumlah (Rp)</th>
                                    <th class="px-3 py-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, i) in items" :key="i">
                                    <tr class="border-t hover:bg-gray-50 transition">
                                        <td class="p-2">
                                            <input type="text" :name="`items[${i}][name]`" x-model="item.name" list="list-products" @change="onProductChange(i, $event.target.value)" class="w-full rounded border border-gray-400 bg-white text-xs focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ketik nama barang...">
                                            <datalist id="list-products">
                                                <template x-for="p in productsByBelanja()" :key="p.id">
                                                    <option :value="p.name"></option>
                                                </template>
                                            </datalist>
                                        </td>
                                        <td class="p-2"><input type="number" :name="`items[${i}][qty]`" x-model="item.qty" @input="recalc(i)" class="w-full rounded border border-gray-400 bg-white text-xs focus:ring-indigo-500 focus:border-indigo-500 text-right py-2"></td>
                                        <td class="p-2"><input type="text" :name="`items[${i}][unit]`" x-model="item.unit" class="w-full rounded border border-gray-400 bg-white text-xs focus:ring-indigo-500 focus:border-indigo-500 py-2"></td>
                                        <td class="p-2"><input type="number" :name="`items[${i}][price]`" x-model="item.price" @input="recalc(i)" class="w-full rounded border border-gray-400 bg-white text-xs focus:ring-indigo-500 focus:border-indigo-500 text-right py-2"></td>
                                        <td class="p-2"><input type="text" :name="`items[${i}][total]`" x-model="item.total" readonly class="w-full rounded bg-gray-100 border border-gray-400 bg-white text-xs text-right py-2"></td>
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
                    'backRoute' => route('reports.nota.list'),
                    'previewRoute' => route('reports.nota.report'),
                ])
            </form>
        </div>
    </div>
@endsection
