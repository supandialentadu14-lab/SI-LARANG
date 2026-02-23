@extends('layouts.admin') {{-- Menggunakan layout utama admin --}}

@section('header', 'Daftar Penyedia') {{-- Judul halaman manajemen supplier --}}

@section('actions')
    {{-- Tombol untuk menambah supplier baru --}}
    <a href="{{ route('suppliers.create') }}"
        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition transform hover:-translate-y-0.5 flex items-center">
        <i class="fas fa-plus mr-2"></i> {{-- Icon tambah --}}
        Tambah Penyedia
    </a>
@endsection

@section('content')

    {{-- Card pembungkus tabel supplier --}}
    <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">

        {{-- Membuat tabel responsive jika layar kecil --}}
        <div class="overflow-x-auto">

            {{-- Tabel daftar supplier --}}
            <table class="w-full text-sm text-left text-gray-600">

                {{-- Header tabel --}}
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-3">Nama Perushaan</th> {{-- Kolom nama supplier --}}
                        <th class="px-6 py-3">Nama Pemilik</th> {{-- Kolom nama supplier --}}
                        <th class="px-6 py-3">Kontak</th> {{-- Kolom email & phone --}}
                        <th class="px-6 py-3">Alamat</th> {{-- Kolom alamat --}}
                        <th class="px-6 py-3 text-right">Aksi</th> {{-- Kolom tombol aksi --}}
                    </tr>
                </thead>

                {{-- Isi tabel --}}
                <tbody class="divide-y divide-gray-100">

                    {{-- Looping data supplier --}}
                    @forelse($suppliers as $supplier)
                        {{-- Baris data supplier --}}
                        <tr class="hover:bg-orange-50 transition">

                            {{-- Nama supplier --}}
                            <td class="px-6 py-4 font-bold text-gray-800">
                                {{ $supplier->name }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">

                                    {{-- Email (jika kosong tampilkan '-') --}}
                                    <span class="font-semibold text-gray-700">
                                        {{ $supplier->dir ?? '-' }}
                                    </span>
                                    {{-- Kontak supplier (email & phone) --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">

                                    {{-- Email (jika kosong tampilkan '-') --}}
                                    <span class="font-semibold text-gray-700">
                                        {{ $supplier->email ?? '-' }}
                                    </span>

                                    {{-- Nomor telepon (jika kosong tampilkan '-') --}}
                                    <span class="text-xs text-orange-600">
                                        {{ $supplier->phone ?? '-' }}
                                    </span>

                                </div>
                            </td>

                            {{-- Alamat supplier (dibatasi 50 karakter) --}}
                            <td class="px-6 py-4 font-bold text-gray-800 text-xs">
                                {{ Str::limit($supplier->address, 50) ?: '-' }}
                                {{-- Jika kosong tampilkan '-' --}}
                            </td>

                            {{-- Kolom aksi (Edit & Delete) --}}
                            <td class="px-6 py-4 text-right space-x-2">
                                {{-- Tombol edit --}}
                                <a href="{{ route('suppliers.edit', $supplier) }}"
                                    class="inline-block text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Form delete supplier --}}
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('Delete supplier?');">

                                    @csrf {{-- Token keamanan --}}
                                    @method('DELETE') {{-- Method spoofing untuk delete --}}

                                    {{-- Tombol hapus --}}
                                    <button type="submit"
                                        class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>

                        {{-- Jika tidak ada data supplier --}}
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                No suppliers found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $suppliers->links() }} {{-- Menampilkan link pagination --}}
        </div>

    </div>

@endsection
