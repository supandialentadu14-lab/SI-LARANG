@extends('layouts.admin')

@section('header', 'Pengaturan Hak Akses')
@section('content')

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold shadow">
            <i class="fas fa-user-plus"></i> Tambah Pengguna
        </a>
    </div>

    <div class="overflow-x-auto border rounded-lg">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-100 text-xs uppercase font-bold">
                <tr>
                    <th class="px-3 py-2">Nama</th>
                    <th class="px-3 py-2">Email</th>
                    <th class="px-3 py-2">Hak Akses</th>
                    <th class="px-3 py-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-t hover:bg-gray-50 transition">
                        <td class="px-3 py-2 font-bold text-gray-800">
                            <div class="flex items-center">
                                <img class="w-8 h-8 rounded-full object-cover ring-2 ring-indigo-200 mr-3"
                                    src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=4F46E5&color=ffffff' }}"
                                    alt="{{ $user->name }}">
                                {{ $user->name }}
                            </div>
                        </td>
                        <td class="px-3 py-2">{{ $user->email }}</td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            @if (auth()->id() !== $user->id)
                                @include('partials.action_buttons', [
                                    'edit' => route('users.edit', $user),
                                    'delete' => route('users.destroy', $user),
                                ])
                            @else
                                <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 text-xs font-bold">
                                    <i class="fas fa-edit"></i> Edit Profil
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-6 text-center text-gray-500">Belum ada data pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
