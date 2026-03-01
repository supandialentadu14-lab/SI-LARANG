<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Inventory') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#EEF2FF', 100: '#E0E7FF', 200: '#C7D2FE', 300: '#A5B4FC',
                            400: '#818CF8', 500: '#6366F1', 600: '#4F46E5', 700: '#4338CA',
                            800: '#3730A3', 900: '#312E81',
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                        'fade-in': 'fadeIn 1s ease-out forwards',
                        'slide-in-right': 'slideInRight 0.8s ease-out forwards',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideInRight: {
                            '0%': { opacity: '0', transform: 'translateX(20px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .bg-pattern {
            background-color: #ffffff;
            background-image: radial-gradient(#4F46E5 0.5px, transparent 0.5px), radial-gradient(#4F46E5 0.5px, #ffffff 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.1;
        }
        .form-input:focus {
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }
        .login-illustration {
            mask-image: linear-gradient(to right, black 85%, transparent 100%);
        }
    </style>
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50 flex min-h-screen overflow-hidden">
    <!-- Left Side - Image/Illustration -->
    <div class="hidden lg:flex w-1/2 relative bg-brand-600 overflow-hidden items-center justify-center animate__animated animate__fadeIn">
        <!-- Abstract Background Shapes -->
        <div class="absolute inset-0 bg-gradient-to-br from-brand-600 to-brand-800"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-brand-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay: 2s"></div>
        <div class="absolute -bottom-24 left-1/2 w-80 h-80 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay: 4s"></div>
        
        <!-- Pattern Overlay -->
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

        <!-- Main Image -->
        <div class="relative z-10 p-12 w-full max-w-2xl animate__animated animate__zoomIn">
            <img src="{{ asset('images/login-bg-new.jpg') }}" class="w-full h-auto object-cover rounded-2xl shadow-2xl transform hover:scale-105 transition duration-700 ease-in-out border-4 border-white/20">
            <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg animate-bounce">
                <span class="text-brand-900 font-bold text-xl">v2.0</span>
            </div>
        </div>

        <!-- Text Overlay -->
        <div class="absolute bottom-12 left-12 z-20 text-white animate__animated animate__fadeInUp animate__delay-1s">
            <h2 class="text-4xl font-bold mb-2">SI-LARANG</h2>
            <p class="text-brand-100 text-lg max-w-md">Sistem Informasi Pengelolaan Persediaan Barang Daerah yang Modern & Terintegrasi.</p>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 relative bg-white">
        <!-- Background Pattern for Right Side -->
        <div class="absolute inset-0 bg-pattern z-0"></div>
        
        <div class="w-full max-w-md relative z-10 animate__animated animate__fadeInRight">
            <div class="text-center lg:hidden mb-8 animate__animated animate__fadeInDown">
                <div class="inline-flex items-center justify-center mb-3 p-2 bg-white rounded-xl shadow-lg">
                    <img src="{{ asset('images/silarang-logo.png') }}" alt="Logo SI-LARANG" class="h-16 w-16 rounded-lg" onerror="this.style.display='none'">
                </div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">SI-LARANG</h2>
                <p class="text-sm text-gray-500 font-medium">Sistem Informasi Persediaan Barang</p>
            </div>

            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-8 sm:p-10 transition-all hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)]">
                {{ $slot }}
            </div>

            <div class="mt-8 text-center space-y-2 animate__animated animate__fadeIn animate__delay-1s">
                <p class="text-xs text-gray-400">Sistem Informasi Pengelolaan Persediaan Barang.</p>
                <p class="text-xs text-gray-400 font-medium">Copyright © 2026 Emon Alentadu. Seluruh Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </div>
</body>
</html>
