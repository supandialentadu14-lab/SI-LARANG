<!DOCTYPE html>
{{-- Mengatur bahasa halaman sesuai dengan locale aplikasi --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- Encoding karakter --}}
    <meta charset="utf-8">

    {{-- Agar responsive di semua device --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Token CSRF untuk keamanan request AJAX/form --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Judul halaman --}}
    <title>{{ config('app.name', 'Inventory') }} - Login</title>

    <!-- ================= FONT & ICON ================= -->

    {{-- Import Google Font Inter --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Import Font Awesome untuk icon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- ================= TAILWIND CSS ================= -->

    {{-- Menggunakan Tailwind via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Custom konfigurasi Tailwind --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    // Menambahkan font default
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    // Menambahkan custom warna brand (orange)
                    colors: {
                        brand: {
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
                        }
                    }
                },
            },
        }
    </script>
</head>

{{-- Body utama dengan flex layout full screen --}}
<body class="font-sans antialiased text-gray-900 bg-gray-50 flex min-h-screen">

    <!-- ================= LEFT SIDE (DESKTOP ONLY) ================= -->

    {{-- 
        Bagian kiri hanya tampil di layar besar (lg).
        Berisi gambar branding.
    --}}
    <div class="hidden lg:flex w-1/2 items-center justify-center p-8 bg-white">

        {{-- Gambar background/login illustration --}}
        <img src="{{ asset('images/login-bg-new.jpg') }}"
             class="w-full h-full object-contain rounded-2xl shadow-xl">

    </div>

    <!-- ================= RIGHT SIDE (FORM LOGIN/REGISTER) ================= -->

    {{-- 
        Bagian kanan berisi form.
        Di mobile akan full width.
    --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 bg-white">

        {{-- Container maksimal lebar form --}}
        <div class="w-full max-w-md">

            <!-- ========== Logo Mobile (Hanya tampil di layar kecil) ========== -->
            <div class="text-center lg:hidden mb-8">
                <div class="inline-flex items-center justify-center mb-3">
                    <img src="{{ asset('images/silarang-logo.png') }}" alt="Logo SI-LARANG" class="h-14 w-14 rounded-md ring-2 ring-indigo-200" onerror="this.style.display='none'">
                </div>
                <h2 class="text-2xl font-bold text-gray-800">SI-LARANG</h2>
                <p class="text-sm text-gray-500">Sistem Informasi Persediaan Barang</p>
            </div>

            {{-- 
                Slot untuk menampilkan konten halaman 
                (misalnya form login atau register)
            --}}
            {{ $slot }}

            <!-- ========== Footer kecil ========== -->
            <div class="mt-8 pt-6 border-t border-gray-100 text-center">

                {{-- Nama sistem --}}
                <p class="text-xs text-gray-400">
                    Sistem Informasi Pengelolaan Persediaan Barang.
                </p>

                {{-- Copyright --}}
                <p class="text-xs text-gray-400 font-medium">
                    Copyright © 2026 Emon Alentadu. Seluruh Hak Cipta Dilindungi.
                </p>

            </div>
        </div>
    </div>

</body>
</html>
