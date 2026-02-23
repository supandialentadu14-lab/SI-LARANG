{{-- Menggunakan layout admin --}}
@extends('layouts.admin')

{{-- Mengisi bagian header halaman --}}
@section('header', 'Pengaturan Jenis Belanja')

{{-- Section tombol aksi (biasanya di pojok kanan header) --}}
@section('actions')

    {{-- Tombol menuju halaman tambah kategori --}}
    <a href="{{ route('categories.create') }}"
        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition transform hover:-translate-y-0.5 flex items-center">
        
        {{-- Icon tambah --}}
        <i class="fas fa-plus mr-2"></i> 
        
        Tambah Jenis Belanja
    </a>
@endsection

{{-- Section utama konten --}}
@section('content')

    {{-- Card utama --}}
    <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">

        {{-- Wrapper agar tabel bisa discroll horizontal di layar kecil --}}
        <div class="overflow-x-auto">

            {{-- Tabel daftar kategori --}}
            <form action="{{ route('categories.bulk_delete') }}" method="POST" class="no-soft" onsubmit="return confirm('Hapus semua jenis belanja terpilih?');">
                @csrf
            <table class="w-full text-sm text-left text-gray-600">

                {{-- Header tabel --}}
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-3 w-10">
                            <input type="checkbox" id="select-all-categories">
                        </th>
                        <th class="px-6 py-3">Nama Jenis Belanja</th>
                        <th class="px-6 py-3">Keterangan</th>
                        <th class="px-6 py-3 text-center">Barang</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                {{-- Isi tabel --}}
                <tbody class="divide-y divide-gray-100">

                    {{-- Loop data kategori --}}
                    @forelse($categories as $category)

                        <tr class="hover:bg-orange-50 transition">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="ids[]" value="{{ $category->id }}" class="row-check">
                            </td>

                            {{-- Nama kategori --}}
                            <td class="px-6 py-4 font-bold text-gray-800">
                                {{ $category->name }}
                            </td>

                            {{-- Deskripsi kategori (dibatasi 60 karakter) --}}
                            <td class="px-6 py-4 text-gray-500">
                                {{ Str::limit($category->description, 60) ?: '-' }}
                                {{-- Jika deskripsi kosong tampilkan "-" --}}
                            </td>

                            {{-- Jumlah produk dalam kategori --}}
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-block bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold">
                                    
                                    {{ $category->products_count ?? 0 }} Items
                                    {{-- Menggunakan products_count (biasanya dari withCount()) --}}
                                </span>
                            </td>

                            {{-- Tombol aksi --}}
                            <td class="px-6 py-4 text-right space-x-2">

                                {{-- Tombol edit --}}
                                <a href="{{ route('categories.edit', $category) }}"
                                    class="inline-block text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded transition">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Form hapus kategori --}}
                                <form action="{{ route('categories.destroy', $category) }}" 
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Delete this category?');">
                                    
                                    {{-- CSRF protection --}}
                                    @csrf

                                    {{-- Method spoofing DELETE --}}
                                    @method('DELETE')

                                    {{-- Tombol delete --}}
                                    <button type="submit"
                                        class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>

                    {{-- Jika tidak ada data --}}
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                No categories found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <div class="text-xs text-gray-500">Pilih baris untuk menghapus sekaligus</div>
                <button type="submit" id="bulk-delete-categories-btn" disabled
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 hover:border-gray-400 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-trash"></i> Hapus Terpilih
                </button>
            </div>
            </form>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $categories->links() }}
            {{-- Menampilkan link pagination Laravel --}}
        </div>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const all = document.getElementById('select-all-categories');
            const checks = Array.from(document.querySelectorAll('.row-check'));
            const btn = document.getElementById('bulk-delete-categories-btn');
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
