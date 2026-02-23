<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Inventory') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#EEF2FF', 100: '#E0E7FF', 200: '#C7D2FE', 300: '#A5B4FC',
                            400: '#818CF8', 500: '#6366F1', 600: '#4F46E5', 700: '#4338CA',
                            800: '#3730A3', 900: '#312E81',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50 flex min-h-screen">
    <div class="hidden lg:flex w-1/2 items-center justify-center p-8 bg-white">
        <img src="{{ asset('images/login-bg-new.jpg') }}" class="w-full h-full object-contain rounded-2xl shadow-xl">
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 bg-white">
        <div class="w-full max-w-md">
            <div class="text-center lg:hidden mb-8">
                <div class="inline-flex items-center justify-center mb-3">
                    <img src="{{ asset('images/silarang-logo.png') }}" alt="Logo SI-LARANG" class="h-14 w-14 rounded-md ring-2 ring-indigo-200" onerror="this.style.display='none'">
                </div>
                <h2 class="text-2xl font-bold text-gray-800">SI-LARANG</h2>
                <p class="text-sm text-gray-500">Sistem Informasi Persediaan Barang</p>
            </div>

            {{ $slot }}

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-400">Sistem Informasi Pengelolaan Persediaan Barang.</p>
                <p class="text-xs text-gray-400 font-medium">Copyright © 2026 Emon Alentadu. Seluruh Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </div>
</body>
</html>
