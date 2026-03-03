@extends('layouts.admin')

@section('header', 'Dashboard Overview')

@section('actions')
    <form action="{{ route('dashboard') }}" method="GET" class="flex items-center gap-3 bg-white p-1.5 rounded-lg shadow-sm border border-gray-200">
        <div class="flex items-center px-2 gap-2">
            <i class="fas fa-calendar-alt text-gray-400"></i>
            <input type="date" id="date" name="date" value="{{ request('date') }}" 
                class="text-sm font-medium text-gray-700 border-none focus:ring-0 p-0 bg-transparent"
                onchange="this.form.submit()">
        </div>
        @if(request('date'))
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center w-8 h-8 text-gray-500 hover:text-red-500 hover:bg-red-50 rounded-md transition" title="Reset Filter">
                <i class="fas fa-times"></i>
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

    <!-- Welcome Banner -->

    <!-- Main Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Barang</p>
                    <h3 class="text-2xl font-extrabold text-gray-800 mt-1">{{ $totalProducts }}</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="fas fa-box"></i>
                </div>
            </div>
            <div class="flex items-center text-xs text-gray-500">
                <span class="text-blue-600 font-semibold mr-1">{{ $totalCategories }}</span> Kategori
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Stok</p>
                    <h3 class="text-2xl font-extrabold text-gray-800 mt-1">{{ $totalStock }}</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center group-hover:bg-violet-600 group-hover:text-white transition-colors">
                    <i class="fas fa-cubes"></i>
                </div>
            </div>
            <div class="flex items-center text-xs text-gray-500">
                <span class="text-violet-600 font-semibold mr-1">Unit</span> Tersedia
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Stok Menipis</p>
                    <h3 class="text-2xl font-extrabold text-gray-800 mt-1">{{ $lowStockCount }}</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-colors">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <div class="flex items-center text-xs text-gray-500">
                Perlu <span class="text-orange-600 font-semibold mx-1">Restock</span> segera
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Penyedia</p>
                    <h3 class="text-2xl font-extrabold text-gray-800 mt-1">{{ $supplierCount }}</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="fas fa-store"></i>
                </div>
            </div>
            <div class="flex items-center text-xs text-gray-500">
                Partner aktif
            </div>
        </div>
    </div>

    <!-- Today's Activity Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Masuk -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-green-500"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Barang Masuk</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <h3 class="text-2xl font-bold text-gray-800">{{ $inToday }}</h3>
                        <span class="text-xs text-gray-500">Item</span>
                    </div>
                    <p class="text-xs text-green-600 font-semibold mt-2">
                        + Rp {{ number_format($valueInToday, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center text-green-600">
                    <i class="fas fa-arrow-down text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Keluar -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-red-500"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Barang Keluar</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <h3 class="text-2xl font-bold text-gray-800">{{ $outToday }}</h3>
                        <span class="text-xs text-gray-500">Item</span>
                    </div>
                    <p class="text-xs text-red-600 font-semibold mt-2">
                        - Rp {{ number_format($valueOutToday, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center text-red-600">
                    <i class="fas fa-arrow-up text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Transaksi -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-indigo-500"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Total Transaksi</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <h3 class="text-2xl font-bold text-gray-800">{{ $transactionsToday }}</h3>
                        <span class="text-xs text-gray-500">Aktivitas</span>
                    </div>
                    <p class="text-xs text-indigo-600 font-semibold mt-2">
                        {{ $pinjamCount }} Dokumen Pinjam Pakai
                    </p>
                </div>
                <div class="w-12 h-12 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600">
                    <i class="fas fa-exchange-alt text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid: Charts & Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <!-- Left Column: Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h6 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-6 bg-indigo-500 rounded-full"></span>
                    Pergerakan Stok Hari Ini
                </h6>
                <span class="text-xs px-2 py-1 bg-gray-100 rounded text-gray-500">Per Jam</span>
            </div>
            <div class="p-6 flex-1">
                <div class="relative h-80 w-full">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Column: Recent Activity & Critical Stock -->
        <div class="space-y-8">
            
            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h6 class="font-bold text-gray-800 text-sm">Aktivitas Terbaru</h6>
                    <a href="{{ route('stock.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Lihat Semua</a>
                </div>
                <div class="divide-y divide-gray-50 max-h-[300px] overflow-y-auto custom-scrollbar">
                    @forelse($recentTransactions as $transaction)
                        <div class="px-5 py-3 hover:bg-gray-50 transition-colors flex items-center gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $transaction->type == 'in' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                <i class="fas {{ $transaction->type == 'in' ? 'fa-arrow-down' : 'fa-arrow-up' }} text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $transaction->product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->date->format('H:i') }} • {{ $transaction->type == 'in' ? 'Masuk' : 'Keluar' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold {{ $transaction->type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type == 'in' ? '+' : '-' }}{{ $transaction->quantity }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-400 text-xs">Belum ada aktivitas hari ini.</div>
                    @endforelse
                </div>
            </div>

            <!-- Critical Stock -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-red-50/30">
                    <h6 class="font-bold text-red-700 text-sm flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i> Stok Menipis
                    </h6>
                </div>
                <div class="divide-y divide-gray-50 max-h-[250px] overflow-y-auto custom-scrollbar">
                    @forelse($criticalProducts as $p)
                        <div class="px-5 py-3 hover:bg-gray-50 transition-colors flex justify-between items-center">
                            <div class="min-w-0 flex-1 pr-4">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $p->name }}</p>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                                    @php $percent = $p->min_stock > 0 ? min(100, ($p->stock_on_date / $p->min_stock) * 100) : 0; @endphp
                                    <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="block text-sm font-bold text-red-600">{{ $p->stock_on_date }}</span>
                                <span class="text-xs text-gray-400">Min: {{ $p->min_stock }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-400 text-xs">
                            <i class="fas fa-check-circle text-green-400 text-2xl mb-2 block"></i>
                            Stok aman terkendali
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <!-- Chart Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('stockChart');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            const labels = {!! json_encode($labels) !!};
            const dataIn = {!! json_encode($dataIn) !!};
            const dataOut = {!! json_encode($dataOut) !!};
            const netData = dataIn.map((v, i) => v - (dataOut[i] || 0));
            
            // Gradients
            const gradIn = ctx.createLinearGradient(0, 0, 0, 300);
            gradIn.addColorStop(0, 'rgba(34, 197, 94, 0.7)'); // Green
            gradIn.addColorStop(1, 'rgba(34, 197, 94, 0.1)');

            const gradOut = ctx.createLinearGradient(0, 0, 0, 300);
            gradOut.addColorStop(0, 'rgba(239, 68, 68, 0.7)'); // Red
            gradOut.addColorStop(1, 'rgba(239, 68, 68, 0.1)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Masuk',
                            data: dataIn,
                            backgroundColor: gradIn,
                            borderColor: '#22c55e',
                            borderWidth: 2,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#22c55e',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Keluar',
                            data: dataOut,
                            backgroundColor: gradOut,
                            borderColor: '#ef4444',
                            borderWidth: 2,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#ef4444',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'top',
                            align: 'end',
                            labels: { 
                                usePointStyle: true, 
                                boxWidth: 8,
                                font: { family: "'Inter', sans-serif", size: 12 }
                            }
                        },
                        tooltip: { 
                            mode: 'index', 
                            intersect: false,
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            titleColor: '#111827',
                            bodyColor: '#4b5563',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 4,
                            usePointStyle: true,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y + ' Unit';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { 
                            grid: { display: false },
                            ticks: { 
                                font: { size: 11, family: "'Inter', sans-serif" },
                                color: '#9ca3af'
                            }
                        },
                        y: { 
                            beginAtZero: true, 
                            grid: { 
                                color: '#f3f4f6', 
                                borderDash: [4, 4],
                                drawBorder: false
                            },
                            ticks: { 
                                precision: 0,
                                font: { size: 11, family: "'Inter', sans-serif" },
                                color: '#9ca3af',
                                padding: 10
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    },
                    elements: {
                        line: {
                            tension: 0.4 // Smooth curve
                        }
                    }
                }
            });
        });
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    </style>
@endsection