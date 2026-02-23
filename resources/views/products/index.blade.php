@extends('layouts.admin')

@section('header', 'Pembelian Barang')

@section('actions')
    <a href="{{ route('products.create') }}"
        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition transform hover:-translate-y-0.5 flex items-center">
        <i class="fas fa-plus mr-2"></i> Tambah Barang
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">

        {{-- Search --}}
        <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
            <form action="{{ route('products.index') }}" method="GET" class="relative w-full max-w-sm">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-300 focus:border-orange-500 outline-none transition text-sm">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
            </form>
        </div>

        <div class="overflow-x-auto">
            <form action="{{ route('products.bulk_delete') }}" method="POST" class="no-soft" onsubmit="return confirm('Hapus semua barang terpilih?');">
                @csrf
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-3 w-10">
                            <input type="checkbox" id="select-all-products">
                        </th>
                        <th class="px-6 py-3">Nama Barang</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3">Harga</th>
                        <th class="px-6 py-3">Stok Akhir</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-orange-50 transition group">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="row-check">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 group-hover:text-orange-600 transition">
                                            {{ $product->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 font-mono">
                                            {{ $product->sku }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-600 border border-gray-200">
                                    {{ $product->category->name }}
                                </span>
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-700">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>

                            {{-- 🔥 STOK FINAL DARI TRANSAKSI --}}
                            <td class="px-6 py-4">
                                @php
                                    $stock = $product->calculated_stock ?? 0;
                                @endphp

                                @if ($stock <= 1)
                                    <span class="flex items-center text-red-600 font-bold bg-red-50 px-3 py-1 rounded">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $stock }}
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded font-bold">
                                        {{ $stock }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('products.edit', $product) }}"
                                    class="inline-block text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded transition">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('products.destroy', $product) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('Delete this product permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                No products found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <div class="text-xs text-gray-500">Pilih baris untuk menghapus sekaligus</div>
                <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 hover:border-gray-400 disabled:opacity-50 disabled:cursor-not-allowed" id="bulk-delete-btn" disabled>
                    <i class="fas fa-trash"></i> Hapus Terpilih
                </button>
            </div>
            </form>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $products->links() }}
        </div>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const all = document.getElementById('select-all-products');
            const checks = Array.from(document.querySelectorAll('.row-check'));
            const btn = document.getElementById('bulk-delete-btn');
            const refreshBtnState = () => {
                btn.disabled = !checks.some(c => c.checked);
            };
            if (all) {
                all.addEventListener('change', () => {
                    checks.forEach(c => c.checked = all.checked);
                    refreshBtnState();
                });
            }
            checks.forEach(c => c.addEventListener('change', refreshBtnState));
        });
    </script>
@endsection
