{{-- Menggunakan layout utama admin --}} @extends('layouts.admin') {{-- Mengisi section header pada layout --}} @section('header', 'Tambah Barang') {{-- Mengisi section content pada layout --}}
@section('content') {{-- Container utama dengan lebar maksimal 4xl dan posisi tengah --}} <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden"> {{-- Header Card --}} <div
                class="px-6 py-4 border-b border-gray-100 bg-orange-50">
                <h6 class="font-bold text-blue-700 flex items-center"> {{-- Icon tambah --}} <i
                        class="fas fa-plus-circle mr-2"></i> Informasi Barang </h6>
            </div> {{-- Form untuk menyimpan data produk --}} <form action="{{ route('products.store') }}" method="POST"
                enctype="multipart/form-data" class="p-6"> {{-- Token keamanan Laravel (wajib pada form POST) --}} @csrf {{-- Grid 2 kolom untuk layout form --}} <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-8"> <!-- ================= LEFT COLUMN ================= -->
                    <div class="space-y-6"> {{-- Input Nama Barang --}} <div> <label
                                class="block text-sm font-bold text-gray-700 mb-1"> Nama Barang <span
                                    class="text-red-500">*</span> {{-- Wajib diisi --}} </label> <input type="text"
                                name="name" value="{{ old('name') }}" {{-- Mengambil input lama jika validasi gagal --}}
                                placeholder="e.g. Kertas HVS"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                                required> </div> {{-- Input Kode Barang / SKU --}} <div> <label
                                class="block text-sm font-bold text-gray-700 mb-1"> Kode Barang <span
                                    class="text-red-500">*</span> </label>
                            <div class="flex"> {{-- Icon barcode --}} <span
                                    class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    <i class="fas fa-barcode"></i> </span> <input type="text" name="sku"
                                    value="{{ old('sku', $newSku) }}" readonly
                                    class="w-full px-4 py-2 rounded-r-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                                    required> </div>
                        </div> {{-- Harga + Satuan dalam Grid --}} <div class="grid grid-cols-2 gap-4"> {{-- Input Harga Satuan --}} <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1"> Harga Satuan <span
                                        class="text-red-500">*</span> </label>
                                <div class="relative"> {{-- Prefix mata uang --}} <span
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm font-bold">
                                        Rp </span> <input type="number" step="0.01" name="price"
                                        value="{{ old('price') }}" placeholder="0"
                                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                                        required> </div>
                            </div> {{-- Dropdown Satuan --}} <div> <label class="block text-sm font-bold text-gray-700 mb-1">
                                    Satuan <span class="text-red-500">*</span> </label> <select name="unit"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition bg-white"
                                    required>
                                    <option value="">-- Pilih Satuan --</option> {{-- Menentukan selected jika sama dengan input sebelumnya --}} <option
                                        value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                    <option value="buah" {{ old('unit') == 'buah' ? 'selected' : '' }}>Buah</option>
                                    <option value="box" {{ old('unit') == 'box' ? 'selected' : '' }}>Box</option>
                                    <option value="pak" {{ old('unit') == 'pak' ? 'selected' : '' }}>Pak</option>
                                    <option value="rim" {{ old('unit') == 'rim' ? 'selected' : '' }}>Rim</option>
                                    <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kg</option>
                                    <option value="galon" {{ old('unit') == 'galon' ? 'selected' : '' }}>Galon</option>
                                    <option value="paket" {{ old('unit') == 'paket' ? 'selected' : '' }}>Paket</option>
                                    <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter</option>
                                </select> </div>
                        </div> {{-- Textarea Deskripsi Produk --}} <div> <label class="block text-sm font-bold text-gray-700 mb-1">
                                Keterangan </label>
                            <textarea name="description" rows="4" placeholder="Product details..."
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"> {{ old('description') }} </textarea>
                        </div>
                    </div> <!-- ================= RIGHT COLUMN ================= -->
                    <div class="space-y-6"> {{-- Dropdown Jenis Belanja --}} <div> <label
                                class="block text-sm font-bold text-gray-700 mb-1"> Jenis Belanja <span
                                    class="text-red-500">*</span> </label> <select name="category_id"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition bg-white"
                                required>
                                <option value="">-- Pilih Jenis Belanja --</option> {{-- Looping data kategori dari controller --}}
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}> {{ $category->name }}
                                    </option>
                                @endforeach
                            </select> </div> {{-- Dropdown Supplier --}} <div> <label
                                class="block text-sm font-bold text-gray-700 mb-1"> Penyedia </label> <select
                                name="supplier_id"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition bg-white">
                                <option value="">-- Pilih Penyedia --</option> {{-- Looping data supplier --}} @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}> {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select> </div>
                    </div>
                </div> @include('partials.form-actions', [
                    'backRoute' => route('products.index'),
                    'saveText' => 'Simpan',
                ])
            </form>
        </div>
</div> @endsection
