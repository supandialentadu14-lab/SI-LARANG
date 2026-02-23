@extends('layouts.admin') {{-- Menggunakan layout utama admin --}}

@section('header', 'Pengaturan Hak Akses') {{-- Judul halaman daftar user sistem --}}

@section('actions')
    {{-- Tombol untuk menambahkan user baru --}}
    <a href="{{ route('users.create') }}"
        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition transform hover:-translate-y-0.5 flex items-center">
        <i class="fas fa-user-plus mr-2"></i> {{-- Icon tambah user --}}
        Tambah Pengguna
    </a>
@endsection

@section('content')

    {{-- Card pembungkus tabel --}}
    <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">

        {{-- Agar tabel responsive saat layar kecil --}}
        <div class="overflow-x-auto">

            {{-- Tabel daftar user --}}
            <table class="w-full text-sm text-left text-gray-600">

                {{-- Header tabel --}}
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-3">Nama</th> {{-- Kolom nama --}}
                        <th class="px-6 py-3">Email</th> {{-- Kolom email --}}
                        <th class="px-6 py-3">Hak Akses</th> {{-- Kolom role --}}
                        <th class="px-6 py-3 text-right">Aksi</th> {{-- Kolom aksi --}}
                    </tr>
                </thead>

                {{-- Body tabel --}}
                <tbody class="divide-y divide-gray-100">

                    {{-- Loop data users --}}
                    @forelse($users as $user)

                        {{-- Baris data user --}}
                        <tr class="hover:bg-orange-50 transition">

                            {{-- Kolom Nama --}}
                            <td class="px-6 py-4 font-bold text-gray-800">
                                <div class="flex items-center">
                                    <img
                                        class="w-8 h-8 rounded-full object-cover ring-2 ring-indigo-200 mr-3"
                                        src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=4F46E5&color=ffffff' }}"
                                        alt="{{ $user->name }}">
                                    {{ $user->name }}
                                </div>
                            </td>

                            {{-- Kolom Email --}}
                            <td class="px-6 py-4">
                                {{ $user->email }}
                            </td>

                            {{-- Kolom Role --}}
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded text-xs font-bold 
                                    {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                                    
                                    {{-- Menampilkan role dalam huruf besar --}}
                                    {{ strtoupper($user->role) }}
                                </span>
                            </td>

                            {{-- Kolom Aksi --}}
                            <td class="px-6 py-4 text-right space-x-2">

                                {{-- Tombol Edit --}}
                                <a href="{{ route('users.edit', $user) }}"
                                    class="inline-block text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded transition">
                                    <i class="fas fa-edit"></i></a>
                                </a>

                                {{-- Tombol Delete hanya muncul jika bukan user yang sedang login --}}
                                @if (auth()->id() !== $user->id)

                                    <form action="{{ route('users.destroy', $user) }}" 
                                        method="POST" 
                                        class="inline-block"
                                        onsubmit="return confirm('Delete user?');">

                                        @csrf {{-- Token keamanan --}}
                                        @method('DELETE') {{-- Method spoofing untuk hapus data --}}

                                        {{-- Tombol hapus --}}
                                       <button type="submit"
                                        class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    </form>

                                @endif

                            </td>
                        </tr>

                    {{-- Jika tidak ada user --}}
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                No users found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $users->links() }} {{-- Menampilkan link pagination --}}
        </div>

    </div>

@endsection
