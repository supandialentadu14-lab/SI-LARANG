<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Inventory') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Nunito', 'sans-serif'],
                        },
                        colors: {
                            orange: {
                                50: '#FFF1E6',
                                100: '#FFE3CC',
                                200: '#FFD0A3',
                                300: '#FFB875',
                                400: '#FF9E47',
                                500: '#FF7F1A',
                                600: '#E76A09',
                                700: '#C45508',
                                800: '#9A4407',
                                900: '#7A3606',
                            },
                            indigo: {
                                50: '#EEF2FF',
                                100: '#E0E7FF',
                                200: '#C7D2FE',
                                300: '#A5B4FC',
                                400: '#818CF8',
                                500: '#6366F1',
                                600: '#4F46E5',
                                700: '#4338CA',
                                800: '#3730A3',
                                900: '#312E81',
                            },
                        }
                    },
                },
            }
        </script>
    @endif
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- CSS laporan global untuk preview F4, KOP, dan tabel --}}
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">

    <style>
        :root {
            --body-bg: #F3F4F6;
            --body-text: #111827;
            --sidebar-bg: #111827;
            --sidebar-text: #E5E7EB;
            --sidebar-muted: #9CA3AF;
            --sidebar-hover: #1F2937;
            --sidebar-active: #374151;
            --accent: #4F46E5;
        }
        .theme-light {
            --body-bg: #F3F4F6;
            --body-text: #111827;
            --sidebar-bg: #F8FAFC;
            --sidebar-text: #111827;
            --sidebar-muted: #6B7280;
            --sidebar-hover: #E5E7EB;
            --sidebar-active: #D1D5DB;
            --accent: #4F46E5;
        }
        .theme-dark {
            --body-bg: #0F131A;
            --body-text: #E5E7EB;
            --sidebar-bg: #12171F;
            --sidebar-text: #E5E7EB;
            --sidebar-muted: #94A3B8;
            --sidebar-hover: #1B2230;
            --sidebar-active: #253047;
            --accent: #60A5FA;
            --marquee-start: #93C5FD;
            --marquee-end: #C4B5FD;
        }
        .theme-light { --marquee-start: #60A5FA; --marquee-end: #A78BFA; }
        [x-cloak] {
            display: none !important;
        }

        .sidebar-modern {
            background: radial-gradient(1200px 600px at -10% 10%, rgba(124, 58, 237, .25) 0%, rgba(14, 20, 40, 0) 40%) , 
                        radial-gradient(1200px 600px at 120% 40%, rgba(14, 165, 233, .25) 0%, rgba(14, 20, 40, 0) 40%) , 
                        var(--sidebar-bg);
            color: var(--sidebar-text);
            min-height: 100vh;
            will-change: transform, opacity;
            backface-visibility: hidden;
            transform: translateZ(0);
        }

        .nav-link { display:flex; align-items:center; padding:.9rem 1rem; color: var(--sidebar-text); transition: all .2s; border-left: 3px solid transparent; border-radius:.75rem; }

        .nav-link:hover { color: var(--sidebar-text); background-color: var(--sidebar-hover); }

        .nav-link.active { 
            color: var(--sidebar-text); 
            background: linear-gradient(90deg, rgba(125, 211, 252, .25), rgba(192, 132, 252, .25));
            box-shadow: 0 0 0 1px rgba(125, 211, 252, .25) inset, 0 6px 18px rgba(125, 211, 252, .22);
            font-weight: 700; 
            border-left: 3px solid var(--accent); 
        }
        .bg-indigo-800 {
            background: linear-gradient(90deg, rgba(125, 211, 252, .22), rgba(192, 132, 252, .22));
            box-shadow: 0 0 0 1px rgba(125, 211, 252, .18) inset, 0 6px 18px rgba(192, 132, 252, .18);
        }
        #page-header h2 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #111827;
            letter-spacing: .2px;
        }
        #page-header p {
            color: #6b7280;
        }
        .nav-trail { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; }
        .nav-trail .crumb { font-size: 12px; color: #374151; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 999px; padding: 4px 10px; }
        .card {
            border: 1px solid #f3f4f6;
            border-radius: .75rem;
            background: #ffffff;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,.05), 0 4px 6px -4px rgba(0,0,0,.05);
        }
        .table-clean thead {
            background: #f3f4f6;
        }
        .table-clean tbody tr:hover {
            background: #fff7ed;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="date"], textarea, select {
            border-color: #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.6rem 0.9rem;
            transition: all .2s;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
            border-color: #6366f1;
        }
        button {
            transition: transform .05s ease, box-shadow .2s ease;
        }
        button:hover {
            transform: translateY(-1px);
        }
        .submenu-stagger a {
            opacity: 0;
            transform: translateY(4px);
            transition: opacity .18s ease, transform .18s ease;
            will-change: opacity, transform;
        }
        .submenu-open a { opacity: 1; transform: translateY(0); }
        .submenu-open a:nth-child(1) { transition-delay: 30ms; }
        .submenu-open a:nth-child(2) { transition-delay: 60ms; }
        .submenu-open a:nth-child(3) { transition-delay: 90ms; }
        .submenu-open a:nth-child(4) { transition-delay: 120ms; }
        .submenu-open a:nth-child(5) { transition-delay: 150ms; }
        .submenu-open a:nth-child(6) { transition-delay: 180ms; }
        .submenu-open a:nth-child(7) { transition-delay: 210ms; }
        .submenu-open a:nth-child(8) { transition-delay: 240ms; }
        .submenu-open a:nth-child(9) { transition-delay: 270ms; }
        .submenu-open a:nth-child(10) { transition-delay: 300ms; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .5rem .75rem;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            font-weight: 600;
            line-height: 1.25rem;
            background: #ffffff;
            color: #374151;
            box-shadow: none;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease;
        }
        .btn:hover { background: #f9fafb; }
        .btn:focus { outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,.25); }
        .btn i { font-size: .9rem; }
        .btn-primary { background: linear-gradient(90deg, #4F46E5, #7DD3FC); color: #ffffff; border-color: transparent; }
        .btn-primary:hover { background: linear-gradient(90deg, #3F37E0, #60A5FA); color: #ffffff; border-color: transparent; box-shadow: 0 6px 16px rgba(96,165,250,.35); }
        .btn-success { background: #16a34a; color: #ffffff; border-color: transparent; }
        .btn-success:hover { background: #15803d; }
        .btn-warning { background: #f97316; color: #ffffff; border-color: transparent; }
        .btn-warning:hover { background: #ea580c; }
        .btn-neutral { background: #111827; color: #ffffff; border-color: transparent; }
        .btn-neutral:hover { background: #0f172a; }
        .btn-outline { background: transparent; color: #374151; border-color: #d1d5db; }
        .btn-outline:hover { background: #f9fafb; }

        /* MARQUEE */
        .marquee-container {
            position: relative;
            overflow: hidden;
            white-space: nowrap;
            width: 100%;
            height: 36px;
            border-radius: 0.5rem;
            background: linear-gradient(90deg, rgba(99,102,241,.12), rgba(124,58,237,.12));
            border: 1px solid rgba(99,102,241,.15);
        }

        .marquee-text {
            font-size: 18px;
            display: inline-block;
            padding-left: 100%;
            font-weight: 800;
            background: linear-gradient(90deg, var(--marquee-start), var(--marquee-end));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: .4px;
            animation: marqueeMove 18s linear infinite;
            text-shadow: 0 0 1px rgba(255,255,255,.2);
        }
        .marquee-container:hover .marquee-text {
            animation-play-state: paused;
        }

        @keyframes marqueeMove {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }
        @media print {
            aside,
            header,
            #page-header,
            .no-print {
                display: none !important;
            }
            marquee, .marquee-container, .marquee-text, [data-marquee] {
                display: none !important;
            }
            * {
                animation: none !important;
                transition: none !important;
            }
            .h-screen { height: auto !important; }
            .overflow-hidden, .overflow-y-auto, .overflow-x-hidden { overflow: visible !important; }
            main {
                padding: 0 !important;
                background: #ffffff !important;
            }
            body {
                background: #ffffff !important;
            }
            * {
                box-shadow: none !important;
            }
        }
        #print-area, #print-area * {
            animation: none !important;
            transition: none !important;
        }
        html, body { background-color: var(--body-bg); color: var(--body-text); }
        body {
            overscroll-behavior: none;
        }
        .no-marquee .marquee-container { display: none !important; }
        .no-marquee .marquee-text { animation: none !important; }
        main {
            content-visibility: auto;
            contain-intrinsic-size: 800px;
            animation: none !important;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(2px); }
            to { opacity: 1; transform: translateY(0); }
        }
        main.fade-out { animation: none !important; }
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(2px); }
        }
        @media (prefers-reduced-motion: reduce) {
            * {
                transition: none !important;
                animation: none !important;
            }
        }
        .no-anim * {
            animation: none !important;
            transition: none !important;
        }
        .no-anim main { animation: none !important; }
        .no-anim .sidebar-gradient,
        .no-anim .overflow-y-auto { scroll-behavior: auto; }
        
    </style>
    <style>
        html {
            scrollbar-width: thin;
            scrollbar-color: #6366F1 rgba(99,102,241,.12);
        }
        .sidebar-gradient,
        .overflow-y-auto {
            scroll-behavior: smooth;
        }
        .sidebar-gradient::-webkit-scrollbar,
        .overflow-y-auto::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .sidebar-gradient::-webkit-scrollbar-track,
        .overflow-y-auto::-webkit-scrollbar-track {
            background: rgba(255,255,255,.08);
            border-radius: 999px;
        }
        .sidebar-gradient::-webkit-scrollbar-thumb,
        .overflow-y-auto::-webkit-scrollbar-thumb {
            background-image: linear-gradient(180deg, #6366F1 0%, #7C3AED 100%);
            border-radius: 999px;
            border: 2px solid rgba(255,255,255,.25);
        }
        .sidebar-gradient::-webkit-scrollbar-thumb:hover,
        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background-image: linear-gradient(180deg, #4F46E5 0%, #7C3AED 100%);
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (document.querySelector('#print-area')) {
                document.body.classList.add('no-marquee');
                document.body.classList.add('no-anim');
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var collapse = {!! json_encode(session('collapse_submenus') ? true : false) !!};
            if (collapse) {
                try {
                    localStorage.setItem('sidebarOpenGroups', JSON.stringify({master:false,pengadaan:false,transaksi:false,berita:false,settings:false}));
                } catch (e) {}
            }
        });
    </script>
</head>

<body class="font-sans antialiased theme-light" x-data="{ sidebarOpen: true, theme: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light') }">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside
            class="sidebar-modern flex-shrink-0 flex flex-col transition-all duration-300 shadow-xl z-30 overflow-x-hidden"
            :class="[ sidebarOpen ? 'w-64' : 'w-20', (theme === 'dark' ? 'theme-dark' : 'theme-light') ]">

            <div class="h-16 flex items-center justify-center border-b border-white/20">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group cursor-pointer" style="color: var(--sidebar-text)">
                    <img src="{{ asset('images/silarang-logo.png') }}" alt="Logo SI-LARANG"
                         class="h-8 w-8 rounded-md ring-2 ring-white/40" onerror="this.style.display='none'">
                    <span class="text-xl font-bold tracking-wider" x-show="sidebarOpen" x-cloak>SI-LARANG</span>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">

                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-4 py-3 text-sm font-semibold transition rounded-lg {{ request()->routeIs('dashboard') ? 'bg-indigo-800' : '' }}"
                    :class="sidebarOpen ? 'justify-between' : 'justify-center'">

                    <span class="flex items-center gap-2">
                        <i class="fas fa-tachometer-alt"></i>
                        <span x-show="sidebarOpen" x-cloak>Dashboard</span>
                    </span>
                </a>

                <div class="w-full space-y-2">
                    <div x-data="{ key: 'master', open: false, popover:false }" class="relative" x-init="(() => { const s = JSON.parse(localStorage.getItem('sidebarOpenGroups') || '{}'); open = s[key] ?? ({{ request()->routeIs('products.*') || request()->routeIs('categories.*') || request()->routeIs('suppliers.*') ? 'true' : 'false' }}); })()">
                        <button @click="sidebarOpen ? (open = !open, (() => { const s = JSON.parse(localStorage.getItem('sidebarOpenGroups') || '{}'); s[key] = open; localStorage.setItem('sidebarOpenGroups', JSON.stringify(s)); })()) : (popover = !popover)"
                            class="w-full flex items-center px-4 py-3 text-sm font-semibold transition rounded-lg"
                            :class="sidebarOpen ? 'justify-between' : 'justify-center'">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-database"></i>
                                <span x-show="sidebarOpen" x-cloak>Master Data</span>
                            </span>
                            <svg x-show="sidebarOpen" x-cloak :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="sidebarOpen && open" x-cloak class="mt-2 rounded-lg overflow-hidden submenu-stagger" :class="open ? 'submenu-open' : ''" style="background: var(--sidebar-hover)">
                            <a href="{{ route('products.index') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('products.*') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Barang
                            </a>
                            <a href="{{ route('categories.index') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('categories.*') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Jenis Belanja
                            </a>
                            <a href="{{ route('suppliers.index') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('suppliers.*') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Penyedia
                            </a>
                        </div>
                        <div x-show="!sidebarOpen && popover" x-cloak @click.away="popover=false"
                             class="absolute left-full ml-2 top-0 z-50 w-56 rounded-xl shadow-xl ring-1 ring-black/10 p-2"
                             :style="{ backgroundColor: (theme === 'dark' ? '#1B2230' : '#ffffff'), color: (theme === 'dark' ? '#E5E7EB' : '#111827') }">
                            <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded hover:bg-gray-700/40">Barang</a>
                            <a href="{{ route('categories.index') }}" class="block px-3 py-2 rounded hover:bg-gray-700/40">Jenis Belanja</a>
                            <a href="{{ route('suppliers.index') }}" class="block px-3 py-2 rounded hover:bg-gray-700/40">Penyedia</a>
                        </div>
                    </div>
                    
                    <div x-data="{ key: 'pengadaan', open: false, popover:false }" class="relative" x-init="(() => { const s = JSON.parse(localStorage.getItem('sidebarOpenGroups') || '{}'); open = s[key] ?? ({{ request()->routeIs('reports.belanja.*') || request()->routeIs('reports.nota.*') ? 'true' : 'false' }}); })()">
                        <button @click="sidebarOpen ? (open = !open, (() => { const s = JSON.parse(localStorage.getItem('sidebarOpenGroups') || '{}'); s[key] = open; localStorage.setItem('sidebarOpenGroups', JSON.stringify(s)); })()) : (popover = !popover)"
                            class="w-full flex items-center px-4 py-3 text-sm font-semibold transition rounded-lg"
                            :class="sidebarOpen ? 'justify-between' : 'justify-center'">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-handshake"></i>
                                <span x-show="sidebarOpen" x-cloak>Pengadaan</span>
                            </span>
                            <svg x-show="sidebarOpen" x-cloak :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="sidebarOpen && open" x-cloak class="mt-2 rounded-lg overflow-hidden submenu-stagger" :class="open ? 'submenu-open' : ''" style="background: var(--sidebar-hover)">
                            <div class="px-6 pt-2 pb-1 text-xs font-bold" style="color: var(--sidebar-muted)">Belanja Modal</div>
                            <a href="{{ route('reports.belanja.modal.form') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('reports.belanja.modal.form') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Belanja
                            </a>
                            <a href="{{ route('reports.belanja.modal.list') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('reports.belanja.modal.list') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Daftar Belanja
                            </a>
                            
                            <div class="px-6 pt-3 pb-1 text-xs font-bold" style="color: var(--sidebar-muted)">Belanja Barang dan Jasa</div>
                            <a href="{{ route('reports.nota.form') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('reports.nota.form') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Surat Pesanan
                            </a>
                            <a href="{{ route('reports.nota.list') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('reports.nota.list') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Daftar Surat Pesanan
                            </a>
                            <a href="{{ route('reports.kwitansi.form') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('reports.kwitansi.*') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Kwitansi
                            </a>
                        </div>
                        <div x-show="!sidebarOpen && popover" x-cloak @click.away="popover=false"
                             class="absolute left-full ml-2 top-0 z-50 w-56 rounded-xl shadow-xl ring-1 ring-black/10 p-2"
                             :style="{ backgroundColor: (theme === 'dark' ? '#1B2230' : '#ffffff'), color: (theme === 'dark' ? '#E5E7EB' : '#111827') }">
                            <div class="px-2 py-1 text-xs font-bold opacity-70">Belanja Modal</div>
                            <a href="{{ route('reports.belanja.modal.form') }}" class="block px-3 py-2 rounded hover:bg-gray-700/40">Belanja</a>
                            <a href="{{ route('reports.belanja.modal.list') }}" class="block px-3 py-2 rounded hover:bg-gray-700/40">Daftar Belanja</a>
                            <div class="px-2 py-1 text-xs font-bold opacity-70 mt-2">Belanja Barang dan Jasa</div>
                            <a href="{{ route('reports.nota.form') }}" class="block px-3 py-2 rounded hover:bg-gray-700/40">Surat Pesanan</a>
                            <a href="{{ route('reports.nota.list') }}" class="block px-3 py-2 rounded hover:bg-gray-700/40">Daftar Surat Pesanan</a>
                            <a href="{{ route('reports.kwitansi.form') }}" class="block px-3 py-2 rounded hover:bg-gray-700/40">Kwitansi</a>
                        </div>
                    </div>
                    
                    <div x-data="{ key: 'transaksi', open: false }" x-init="(() => { const s = JSON.parse(localStorage.getItem('sidebarOpenGroups') || '{}'); open = s[key] ?? ({{ request()->routeIs('stock.*') || request()->routeIs('reports.index') || request()->routeIs('reports.kartu.tahunan') ? 'true' : 'false' }}); })()">
                        <button @click="sidebarOpen ? (open = !open, (() => { const s = JSON.parse(localStorage.getItem('sidebarOpenGroups') || '{}'); s[key] = open; localStorage.setItem('sidebarOpenGroups', JSON.stringify(s)); })()) : (window.location.href='{{ route('stock.index') }}')"
                            class="w-full flex items-center px-4 py-3 text-sm font-semibold transition rounded-lg"
                            :class="sidebarOpen ? 'justify-between' : 'justify-center'">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-exchange-alt"></i>
                                <span x-show="sidebarOpen" x-cloak>Transaksi & Laporan</span>
                            </span>
                            <svg x-show="sidebarOpen" x-cloak :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="sidebarOpen && open" x-cloak class="mt-2 rounded-lg overflow-hidden submenu-stagger" :class="open ? 'submenu-open' : ''" style="background: var(--sidebar-hover)">
                            <a href="{{ route('stock.index') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('stock.*') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Mutasi Masuk/Keluar
                            </a>
                            <a href="{{ route('reports.index') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('reports.index') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Laporan Persediaan
                            </a>
                            <a href="{{ route('reports.kartu.tahunan') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('reports.kartu.tahunan') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Kartu Persediaan Tahunan
                            </a>
                            <a href="{{ route('reports.belanja.modal.preview_all') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('reports.belanja.modal.preview_all') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Daftar Belanja Modal
                            </a>
                        </div>
                    </div>
                    
                    <div x-data="{ key: 'berita', open: false }" x-init="(() => { const s = JSON.parse(localStorage.getItem('sidebarOpenGroups') || '{}'); open = s[key] ?? ({{ request()->routeIs('reports.pinjam.*') || request()->routeIs('reports.opname.*') ? 'true' : 'false' }}); })()">
                        <button @click="sidebarOpen ? (open = !open, (() => { const s = JSON.parse(localStorage.getItem('sidebarOpenGroups') || '{}'); s[key] = open; localStorage.setItem('sidebarOpenGroups', JSON.stringify(s)); })()) : (window.location.href='{{ route('reports.pinjam.list') }}')"
                            class="w-full flex items-center px-4 py-3 text-sm font-semibold transition rounded-lg"
                            :class="sidebarOpen ? 'justify-between' : 'justify-center'">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-file-signature"></i>
                                <span x-show="sidebarOpen" x-cloak>Berita Acara</span>
                            </span>
                            <svg x-show="sidebarOpen" x-cloak :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="sidebarOpen && open" x-cloak class="mt-2 rounded-lg overflow-hidden submenu-stagger" :class="open ? 'submenu-open' : ''" style="background: var(--sidebar-hover)">
                            <a href="{{ route('reports.pinjam.list') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('reports.pinjam.list') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Daftar Berita Acara Pinjam Pakai
                            </a>
                            <a href="{{ route('reports.opname.list') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('reports.opname.list') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Daftar BA Stock Opname
                            </a>
                            <a href="{{ route('reports.pemeriksaan.list') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('reports.pemeriksaan.*') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Daftar BA Pemeriksaan Barang/Pekerjaan
                            </a>
                            <a href="{{ route('reports.penerimaan.list') }}" class="block pl-10 pr-6 py-2 text-sm font-medium transition {{ request()->routeIs('reports.penerimaan.list') ? 'bg-indigo-500' : '' }}" style="color: var(--sidebar-text)">
                                Daftar BA Penerimaan
                            </a>
                        </div>
                    </div>
                    <div x-data="{ key: 'settings', open: false }" x-init="(() => { const s = JSON.parse(localStorage.getItem('sidebarOpenGroups') || '{}'); open = s[key] ?? ({{ request()->routeIs('settings.opd.*') || request()->routeIs('settings.nota.master.*') ? 'true' : 'false' }}); })()">
                        <button @click="sidebarOpen ? (open = !open, (() => { const s = JSON.parse(localStorage.getItem('sidebarOpenGroups') || '{}'); s[key] = open; localStorage.setItem('sidebarOpenGroups', JSON.stringify(s)); })()) : (window.location.href='{{ route('settings.opd.index') }}')"
                            class="w-full flex items-center px-4 py-3 text-white text-sm font-semibold hover:bg-indigo-700 transition rounded-lg"
                            :class="sidebarOpen ? 'justify-between' : 'justify-center'">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-gear"></i>
                                <span x-show="sidebarOpen" x-cloak>Pengaturan</span>
                            </span>
                            <svg x-show="sidebarOpen" x-cloak :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="sidebarOpen && open" x-cloak class="mt-2 bg-white/10 rounded-lg overflow-hidden submenu-stagger" :class="open ? 'submenu-open' : ''">
                            <a href="{{ route('settings.opd.index') }}" class="block pl-10 pr-6 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition {{ request()->routeIs('settings.opd.*') ? 'bg-indigo-500' : '' }}">
                                OPD
                            </a>
                            <a href="{{ route('settings.nota.master.list') }}" class="block pl-10 pr-6 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition {{ request()->routeIs('settings.nota.master.*') ? 'bg-indigo-500' : '' }}">
                                Penandatangan
                            </a>
                            
                            @if (Auth::check() && Auth::user()->isAdmin())
                            <a href="{{ route('users.index') }}" class="block pl-10 pr-6 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition {{ request()->routeIs('users.*') ? 'bg-indigo-500' : '' }}">
                                Pengguna
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

            </nav>

            <div class="p-4 text-center" style="background: var(--sidebar-hover)">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="w-8 h-8 rounded-full flex items-center justify-center transition focus:outline-none"
                    :style="{ backgroundColor: (theme === 'dark' ? '#1F2937' : '#E5E7EB'), color: (theme === 'dark' ? '#E5E7EB' : '#111827') }">
                    <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                </button>
                <div class="mt-3 flex items-center justify-center gap-2" x-show="sidebarOpen" x-cloak>
                    <button @click="theme = 'light'; localStorage.setItem('theme','light')"
                        class="px-3 py-1 rounded-full text-xs font-semibold"
                        :style="{ backgroundColor: theme === 'light' ? 'var(--sidebar-active)' : 'transparent', color: 'var(--sidebar-text)', border: '1px solid ' + (theme === 'light' ? 'transparent' : 'var(--sidebar-muted)') }">
                        <i class="fas fa-sun"></i> Light
                    </button>
                    <button @click="theme = 'dark'; localStorage.setItem('theme','dark')"
                        class="px-3 py-1 rounded-full text-xs font-semibold"
                        :style="{ backgroundColor: theme === 'dark' ? 'var(--sidebar-active)' : 'transparent', color: 'var(--sidebar-text)', border: '1px solid ' + (theme === 'dark' ? 'transparent' : 'var(--sidebar-muted)') }">
                        <i class="fas fa-moon"></i> Dark
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden" :style="{ backgroundColor: 'var(--body-bg)' }">
            <!-- Topbar -->
            <header class="shadow h-16 flex items-center justify-between px-6 z-20" :style="{ backgroundColor: '#ffffff', color: 'var(--body-text)' }">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden focus:outline-none" :style="{ color: '#374151' }">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Search Bar or Spacer -->
                <!-- MARQUEE TEXT -->
                <div class="hidden md:block flex-1 mx-6">
                    <div class="marquee-container group">
                        <div class="marquee-text">
                            <span class="inline-flex items-center">
                                <i class="fas fa-bullhorn mr-2 text-indigo-600 group-hover:text-indigo-700"></i>
                                Sistem Informasi Pengelolaan Persediaan Barang (SI-LARANG) • Dinas Komunikasi dan Informatika • Bolaang Mongondow Selatan
                            </span>
                        </div>
                    </div>
                </div>


                <div class="flex items-center space-x-4">
                    <div class="relative" x-data="{ notifyOpen: false }">
                        <button @click="notifyOpen = !notifyOpen"
                            class="text-gray-400 hover:text-blue-600 transition relative focus:outline-none">
                            <i class="fas fa-bell text-xl"></i>
                            @if (isset($lowStockProducts) && $lowStockProducts->count() > 0)
                                <span
                                    class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white bg-red-500 transform translate-x-1/2 -translate-y-1/2 animate-pulse"></span>
                            @endif
                        </button>

                        <div x-show="notifyOpen" x-cloak @click.away="notifyOpen = false"
                            class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5 overflow-hidden"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100">

                            <div
                                class="px-4 py-2 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                                <span class="text-xs font-bold text-gray-500 uppercase">Notifikasi Stok</span>
                                <span
                                    class="bg-red-100 text--600 py-0.5 px-2 rounded-full text-xs font-bold">{{ isset($lowStockProducts) ? $lowStockProducts->count() : 0 }}</span>
                            </div>

                            <div class="max-h-64 overflow-y-auto">
                                @if (isset($lowStockProducts) && $lowStockProducts->count() > 0)
                                    @foreach ($lowStockProducts as $product)
                                        <a href="{{ route('products.edit', $product) }}"
                                            class="block px-4 py-3 hover:bg-orange-50 transition border-b border-gray-50 last:border-0">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 bg-red-100 rounded-full p-1.5">
                                                    <i class="fas fa-exclamation-triangle text-red-500 text-xs"></i>
                                                </div>
                                                <div class="ml-3 w-0 flex-1">
                                                    <p class="text-sm font-bold text-gray-800 truncate">
                                                        {{ $product->name }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Sisa Stok: <span
                                                            class="font-bold text-red-600">{{ $product->stock }}</span>
                                                        <span class="text-gray-400 mx-1">|</span>
                                                        Min: {{ $product->min_stock }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                @else
                                    <div
                                        class="px-4 py-8 text-center text-gray-500 text-sm flex flex-col items-center">
                                        <div class="bg-green-100 p-3 rounded-full mb-3">
                                            <i class="fas fa-check text-green-500 text-lg"></i>
                                        </div>
                                        <p class="font-medium">Stok Aman!</p>
                                        <p class="text-xs text-gray-400 mt-1">Tidak ada produk yang menipis.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="h-6 w-px bg-gray-300 mx-2"></div>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-3 focus:outline-none">
                            <img class="h-9 w-9 rounded-full object-cover ring-2 ring-indigo-200"
                                src="{{ Auth::user()->avatar ? asset('storage/'.Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=4F46E5&color=ffffff' }}"
                                alt="User">
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-bold leading-tight" style="color:#111827">{{ Auth::user()->name }}</p>
                                <p class="text-xs" style="color:#6B7280">{{ Auth::user()->email }}</p>
                            </div>
                            <i class="fas fa-chevron-down hidden md:block" style="color:#9CA3AF"></i>
                        </button>

                        <div x-show="open" x-cloak @click.away="open = false"
                            class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl z-50 ring-1 ring-black ring-opacity-5 overflow-hidden"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100">
                            <div class="p-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                                <div class="flex items-center gap-3">
                                    <img class="h-10 w-10 rounded-full object-cover ring-2 ring-white"
                                        src="{{ Auth::user()->avatar ? asset('storage/'.Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=4F46E5&color=ffffff' }}"
                                        alt="User">
                                    <div>
                                        <p class="font-bold leading-tight">{{ Auth::user()->name }}</p>
                                        <p class="text-xs opacity-80">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="py-2">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50" style="color:#374151">
                                    <i class="fas fa-user-edit text-indigo-600"></i>
                                    Edit Profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50" style="color:#374151">
                                        <i class="fas fa-sign-out-alt text-red-600"></i>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6" style="color:#111827">
                <!-- Page Header & Actions -->
                <div id="page-header" class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold" style="color:#111827">@yield('header')</h2>
                        <p class="text-sm mt-1" style="color:#6B7280">@yield('subheader')</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if (request()->routeIs('*.edit') || request()->routeIs('*.show'))
                            <button type="button" class="btn btn-outline" onclick="history.back()"><i class="fas fa-arrow-left"></i> Back</button>
                        @endif
                        @yield('actions')
                    </div>
                </div>

                @if (session('success'))
                    <div
                        class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                            <span class="text-green-700 font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
                        <p class="font-bold text-red-700">Action Failed</p>
                        <ul class="list-disc list-inside text-sm text-red-600 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
            <footer class="no-print bg-white text-gray-600 border-t ring-1 ring-gray-100">
                <div class="px-6 py-4 flex items-center justify-between">
                    <p class="text-xs md:text-sm font-medium">
                        Copyright © 2026 Emon Alentadu. Seluruh Hak Cipta Dilindungi.
                    </p>
                    <div class="hidden md:flex items-center gap-3 text-xs text-gray-400">
                        <span class="inline-flex items-center gap-1">
                            <i class="fas fa-shield-alt text-indigo-500"></i>
                            Keamanan Data Terjaga
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <i class="fas fa-heart text-pink-500"></i>
                            Terima Kasih Telah Menggunakan SI-LARANG
                        </span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const main = document.querySelector('main');
            const sidebar = document.querySelector('aside nav');
            const isSameOrigin = (url) => {
                try { const u = new URL(url, window.location.origin); return u.origin === window.location.origin; } catch { return false; }
            };
            const ensureTrailEl = () => {
                let el = document.getElementById('nav-trail');
                if (!el) {
                    el = document.createElement('div');
                    el.id = 'nav-trail';
                    el.className = 'nav-trail';
                    main.insertBefore(el, main.firstChild);
                }
                return el;
            };
            const renderTrail = (trail) => {
                const el = ensureTrailEl();
                el.innerHTML = '';
                (trail || JSON.parse(localStorage.getItem('navTrail') || '[]'))
                    .forEach((label) => {
                        const span = document.createElement('span');
                        span.className = 'crumb';
                        span.textContent = label;
                        el.appendChild(span);
                    });
            };
            const setTrail = (labels) => {
                const trail = (labels || []).slice(0, 6);
                localStorage.setItem('navTrail', JSON.stringify(trail));
                renderTrail(trail);
            };
            const getTrail = () => {
                try { return JSON.parse(localStorage.getItem('navTrail') || '[]'); } catch { return []; }
            };
            const isActionLink = (a) => {
                const txt = (a.textContent || '').trim();
                const href = a.getAttribute('href') || '';
                return /(edit|lihat|view|show|detail)/i.test(txt) || /(\/edit|\/show|\/view|detail)/i.test(href);
            };
            const shouldSoftLink = (a) => {
                const href = a.getAttribute('href') || '';
                if (!href || href.startsWith('#')) return false;
                if (!isSameOrigin(href)) return false;
                if (a.hasAttribute('download') || a.target === '_blank') return false;
                if (a.classList.contains('no-soft')) return false;
                if (/export/i.test(href)) return false;
                return true;
            };
            const setActive = (href) => {
                const anchors = sidebar.querySelectorAll('a[href]');
                anchors.forEach(a => a.classList.remove('bg-indigo-800'));
                const target = sidebar.querySelector(`a[href="${href}"]`);
                if (target) target.classList.add('bg-indigo-800');
            };
            const initScripts = (root) => {
                const scripts = root.querySelectorAll('script');
                scripts.forEach(s => {
                    const n = document.createElement('script');
                    if (s.src) { n.src = s.src; } else { n.textContent = s.textContent; }
                    if (s.type) n.type = s.type;
                    root.appendChild(n);
                });
                if (window.Alpine && Alpine.initTree) Alpine.initTree(root);
            };
            const swapMain = async (href, push = true) => {
                try {
                    const res = await fetch(href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) throw new Error('Failed');
                    const html = await res.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newMain = doc.querySelector('main');
                    if (!newMain) { window.location.href = href; return; }
                    document.title = doc.title || document.title;
                    main.innerHTML = newMain.innerHTML;
                    ensureTrailEl();
                    setActive(href);
                    initScripts(main);
                    if (push) history.pushState({}, '', href);
                    main.scrollTop = 0;
                    renderTrail();
                } catch {
                    window.location.href = href;
                }
            };
            sidebar.addEventListener('click', (e) => {
                const a = e.target.closest('a[href]');
                if (!a) return;
                if (!shouldSoftLink(a)) return;
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
                e.preventDefault();
                const href = a.getAttribute('href');
                const groupLabel = a.closest('div[x-data]')?.querySelector('button span')?.textContent?.trim();
                const linkLabel = a.textContent?.trim() || href;
                if (groupLabel) {
                    if (groupLabel === linkLabel) {
                        setTrail([groupLabel]);
                    } else {
                        setTrail([groupLabel, linkLabel]);
                    }
                } else {
                    setTrail([linkLabel]);
                }
                swapMain(href, true);
            });
            window.addEventListener('popstate', () => swapMain(window.location.href, false));
            document.addEventListener('click', (e) => {
                const a = e.target.closest('a[href]');
                if (!a) return;
                if (a.closest('aside')) return;
                if (!shouldSoftLink(a)) return;
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
                e.preventDefault();
                const href = a.getAttribute('href');
                const linkLabel = a.textContent?.trim() || href;
                if (isActionLink(a)) {
                    const base = getTrail();
                    const head = base.slice(0, 2);
                    setTrail([...head, linkLabel]);
                } else {
                    setTrail([linkLabel]);
                }
                swapMain(href, true);
            });
            document.addEventListener('submit', async (e) => {
                const form = e.target.closest('form');
                if (!form) return;
                if (form.classList.contains('no-soft')) return;
                const action = form.getAttribute('action') || window.location.href;
                const method = (form.getAttribute('method') || 'GET').toUpperCase();
                if (!isSameOrigin(action)) return;
                e.preventDefault();
                try {
                    const fd = new FormData(form);
                    const res = await fetch(action, {
                        method,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: method === 'GET' ? null : fd
                    });
                    const html = await res.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newMain = doc.querySelector('main');
                    if (!newMain) { window.location.href = action; return; }
                    document.title = doc.title || document.title;
                    main.innerHTML = newMain.innerHTML;
                    initScripts(main);
                    history.pushState({}, '', res.url || action);
                    main.scrollTop = 0;
                    renderTrail();
                } catch {
                    window.location.href = action;
                }
            });
            renderTrail();
        });
    </script>

</body>

</html>
