@extends('layouts.admin')

@section('header', 'Dashboard')

@section('actions')
    <form action="{{ route('dashboard') }}" method="GET" class="flex items-center gap-3">
        <div class="flex items-center gap-2">
            <label for="date" class="text-sm font-semibold text-gray-600">Tanggal</label>
            <input type="date" id="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()">
        </div>
        @if(request('date'))
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Reset
            </a>
        @endif
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('date');
            const params = new URLSearchParams(window.location.search);
            const qdate = params.get('date');
            if (!qdate) {
                const now = new Date();
                const yyyy = now.getFullYear();
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                const dd = String(now.getDate()).padStart(2, '0');
                const today = `${yyyy}-${mm}-${dd}`;
                if (!input.value) {
                    input.value = today;
                }
            }
        });
    </script>
@endsection

@section('content')

    <!-- ===================== -->
    <!-- STATISTIK RINGKASAN -->
    <!-- ===================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-indigo-100 p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Total Barang</p>
                <h3 class="text-2xl font-extrabold text-gray-800">{{ $totalProducts }}</h3>
            </div>
            <div class="p-3 bg-indigo-100 rounded-full text-indigo-600">
                <i class="fas fa-box fa-lg"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-indigo-100 p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Total Stok</p>
                <h3 class="text-2xl font-extrabold text-gray-800">{{ $totalStock }}</h3>
            </div>
            <div class="p-3 bg-indigo-100 rounded-full text-indigo-600">
                <i class="fas fa-cubes fa-lg"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-indigo-100 p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Total Kategori</p>
                <h3 class="text-2xl font-extrabold text-gray-800">{{ $totalCategories }}</h3>
            </div>
            <div class="p-3 bg-indigo-100 rounded-full text-indigo-600">
                <i class="fas fa-tags fa-lg"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-red-100 p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-xs font-bold text-red-500 uppercase tracking-widest mb-1">Stok Menipis</p>
                <h3 class="text-2xl font-extrabold text-gray-800">{{ $lowStockCount }}</h3>
            </div>
            <div class="p-3 bg-red-100 rounded-full text-red-500">
                <i class="fas fa-exclamation-triangle fa-lg"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-indigo-100 p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Masuk Hari Ini</p>
                <h3 class="text-2xl font-extrabold text-gray-800">{{ $inToday }}</h3>
            </div>
            <div class="p-3 bg-green-100 rounded-full text-green-600">
                <i class="fas fa-arrow-down fa-lg"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-indigo-100 p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Keluar Hari Ini</p>
                <h3 class="text-2xl font-extrabold text-gray-800">{{ $outToday }}</h3>
            </div>
            <div class="p-3 bg-red-100 rounded-full text-red-600">
                <i class="fas fa-arrow-up fa-lg"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-indigo-100 p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Nilai Masuk Hari Ini</p>
                <h3 class="text-2xl font-extrabold text-gray-800">Rp {{ number_format($valueInToday, 0, ',', '.') }}</h3>
            </div>
            <div class="p-3 bg-green-100 rounded-full text-green-600">
                <i class="fas fa-coins fa-lg"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-indigo-100 p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Nilai Keluar Hari Ini</p>
                <h3 class="text-2xl font-extrabold text-gray-800">Rp {{ number_format($valueOutToday, 0, ',', '.') }}</h3>
            </div>
            <div class="p-3 bg-red-100 rounded-full text-red-600">
                <i class="fas fa-money-bill-wave fa-lg"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-indigo-100 p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Transaksi Hari Ini</p>
                <h3 class="text-2xl font-extrabold text-gray-800">{{ $transactionsToday }}</h3>
            </div>
            <div class="p-3 bg-indigo-100 rounded-full text-indigo-600">
                <i class="fas fa-receipt fa-lg"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-indigo-100 p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Nilai Persediaan</p>
                <h3 class="text-2xl font-extrabold text-gray-800">Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}</h3>
            </div>
            <div class="p-3 bg-indigo-100 rounded-full text-indigo-600">
                <i class="fas fa-coins fa-lg"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-indigo-100 p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Perubahan vs Kemarin</p>
                <h3 class="text-2xl font-extrabold {{ ($percentageChange ?? 0) >= 0 ? 'text-green-700' : 'text-red-700' }}">{{ number_format($percentageChange ?? 0, 2) }}%</h3>
            </div>
            <div class="p-3 rounded-full {{ ($percentageChange ?? 0) >= 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                <i class="fas {{ ($percentageChange ?? 0) >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} fa-lg"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-indigo-100 p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Total Penyedia</p>
                <h3 class="text-2xl font-extrabold text-gray-800">{{ $supplierCount }}</h3>
            </div>
            <div class="p-3 bg-indigo-100 rounded-full text-indigo-600">
                <i class="fas fa-store fa-lg"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-indigo-100 p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Dokumen Pinjam Pakai</p>
                <h3 class="text-2xl font-extrabold text-gray-800">{{ $pinjamCount }}</h3>
            </div>
            <div class="p-3 bg-indigo-100 rounded-full text-indigo-600">
                <i class="fas fa-file-contract fa-lg"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm ring-1 ring-indigo-100 p-5 hover:shadow-lg transition lg:col-span-2">
            <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-3">Produk Kritis</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm table-clean">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left">Produk</th>
                            <th class="px-4 py-2 text-right">Stok</th>
                            <th class="px-4 py-2 text-right">Min</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($criticalProducts as $p)
                            <tr>
                                <td class="px-4 py-2">{{ $p->name }}</td>
                                <td class="px-4 py-2 text-right text-red-600 font-bold">{{ $p->stock_on_date }}</td>
                                <td class="px-4 py-2 text-right">{{ $p->min_stock }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-gray-400">Tidak ada produk kritis.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===================== -->
    <!-- CHART & AKTIVITAS -->
    <!-- ===================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm ring-1 ring-indigo-100">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h6 class="font-bold text-gray-700">
                    <i class="fas fa-chart-area mr-2 text-indigo-600"></i>
                    Pergerakan Stok Hari Ini (Per Jam)
                </h6>
            </div>
            <div class="p-6">
                <div class="relative h-72">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>
        </div>
        <div class="lg:col-span-1 bg-white rounded-lg shadow-sm ring-1 ring-indigo-100">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h6 class="font-bold text-gray-700">
                    <i class="fas fa-history mr-2 text-indigo-600"></i>
                    Aktivitas Terbaru
                </h6>
                <a href="{{ route('stock.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Lihat Semua</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentTransactions as $transaction)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3 {{ $transaction->type == 'in' ? 'bg-indigo-100 text-indigo-600' : 'bg-red-100 text-red-600' }}">
                                <i class="fas {{ $transaction->type == 'in' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $transaction->product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->date->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold {{ $transaction->type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type == 'in' ? '+' : '-' }} {{ $transaction->quantity }}
                            </p>
                            <p class="text-xs text-gray-400">Jumlah</p>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-400 text-sm">Tidak ada transaksi terbaru.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ===================== -->
    <!-- SCRIPT CHART.JS -->
    <!-- ===================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('stockChart');
            const ctx = canvas.getContext('2d');
            const labels = {!! json_encode($labels) !!};
            const dataIn = {!! json_encode($dataIn) !!};
            const dataOut = {!! json_encode($dataOut) !!};
            const netData = dataIn.map((v, i) => v - (dataOut[i] || 0));
            const gradIn = ctx.createLinearGradient(0, 0, 0, 300);
            gradIn.addColorStop(0, 'rgba(99,102,241,0.7)');
            gradIn.addColorStop(1, 'rgba(99,102,241,0.15)');
            const gradOut = ctx.createLinearGradient(0, 0, 0, 300);
            gradOut.addColorStop(0, 'rgba(239,68,68,0.7)');
            gradOut.addColorStop(1, 'rgba(239,68,68,0.15)');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Masuk',
                            data: dataIn,
                            backgroundColor: gradIn,
                            borderColor: '#6366F1',
                            borderWidth: 1.5,
                            borderRadius: 10,
                            maxBarThickness: 28
                        },
                        {
                            label: 'Keluar',
                            data: dataOut,
                            backgroundColor: gradOut,
                            borderColor: '#EF4444',
                            borderWidth: 1.5,
                            borderRadius: 10,
                            maxBarThickness: 28
                        },
                        {
                            type: 'line',
                            label: 'Net',
                            data: netData,
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16,185,129,0.15)',
                            borderWidth: 2,
                            tension: 0.35,
                            fill: false,
                            pointRadius: 3,
                            pointBackgroundColor: '#10B981'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        x: { stacked: false, grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)', borderDash: [4, 4] } }
                    }
                }
            });
        });
    </script>

@endsection
