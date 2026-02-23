@extends('layouts.admin') {{-- Menggunakan layout utama admin --}}

@section('header', 'Transaksi Masuk/Keluar') {{-- Judul halaman --}}

@section('actions')
    {{-- Tombol untuk menuju halaman tambah transaksi --}}
    <a href="{{ route('stock.create') }}"
        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition transform hover:-translate-y-0.5 flex items-center">
        <i class="fas fa-exchange-alt mr-2"></i> Tambah Transaksi
    </a>
@endsection

@section('content')

    <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left text-gray-600">

                <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Barang</th>
                        <th class="px-6 py-3">Nomor Surat</th>
                        <th class="px-6 py-3 text-center">Masuk/Keluar</th>
                        <th class="px-6 py-3">Jumlah</th>
                        <th class="px-6 py-3">Saldo Akhir</th>
                        <th class="px-6 py-3">Nilai Saldo</th>
                        <th class="px-6 py-3">Belanja</th>
                        <th class="px-6 py-3">Diinput oleh</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @php
                        // Array untuk menyimpan saldo berjalan setiap produk
                        $runningStock = [];
                    @endphp

                    @forelse($transactions->sortBy('date') as $transaction)

                        @php
                            $productId = $transaction->product->id;

                            if (!isset($runningStock[$productId])) {
                                $runningStock[$productId] = 0;
                            }

                            if ($transaction->type === 'in') {
                                $runningStock[$productId] += $transaction->quantity;
                            } else {
                                $runningStock[$productId] -= $transaction->quantity;
                            }

                            $saldoAkhir = $runningStock[$productId];
                            $nilaiSaldo = $saldoAkhir * $transaction->product->price;
                        @endphp

                        <tr class="hover:bg-orange-50 transition">

                            <td class="px-6 py-4 font-bold text-gray-800">
                                {{ \Carbon\Carbon::parse($transaction->date)->format('Y-m-d') }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-700">
                                    {{ $transaction->product->name }}
                                </span>
                                <div class="text-xs text-gray-400 font-mono">
                                    {{ $transaction->product->sku }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-gray-500 text-xs italic">
                                {{ $transaction->nosur ?: '-' }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-xs font-bold 
                                    {{ $transaction->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ strtoupper($transaction->type) }}
                                </span>
                            </td>

                            <td
                                class="px-6 py-4 font-bold 
                                {{ $transaction->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type === 'in' ? '+' : '-' }}
                                {{ $transaction->quantity }}
                            </td>

                            <td class="px-6 py-4 font-bold text-blue-600">
                                {{ $saldoAkhir }}
                            </td>

                            <td class="px-6 py-4 font-bold text-orange-600">
                                Rp {{ number_format($nilaiSaldo, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-gray-500 text-xs italic">
                                {{ $transaction->notes ?: 'No notes' }}
                            </td>

                            <td class="px-6 py-4 text-xs font-bold text-gray-600">
                                {{ $transaction->user->name ?? 'System' }}
                            </td>

                            {{-- ========================= --}}
                            {{-- BAGIAN YANG DIPERBAIKI --}}
                            {{-- ========================= --}}
                            <td class="px-6 py-4 text-center space-x-2">
                                
                                {{-- ============================= --}}
                                {{-- TOMBOL EDIT DITAMBAHKAN DI SINI --}}
                                {{-- ============================= --}}
                                <a href="{{ route('stock.edit', $transaction->id) }}"
                                    class="inline-block text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- 
                                    Penjelasan:
                                    - route('stock.edit', $transaction->id) 
                                      menuju ke halaman edit transaksi.
                                    - Pastikan di web.php ada route resource:
                                      Route::resource('stock', StockController::class);
                                --}}

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('stock.destroy', $transaction->id) }}"
                                    method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </td>
                            {{-- ========================= --}}
                            {{-- END PERUBAHAN --}}
                            {{-- ========================= --}}

                        </tr>

                    @empty

                        <tr>
                            {{-- 
                                DIPERBAIKI:
                                colspan diubah dari 9 menjadi 10
                                karena sekarang ada 10 kolom termasuk kolom Aksi
                            --}}
                            <td colspan="10" class="px-6 py-8 text-center text-gray-400">
                                Tidak Ada Transaksi.
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>

    </div>

@endsection
