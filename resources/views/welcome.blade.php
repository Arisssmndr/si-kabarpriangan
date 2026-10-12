<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kabar Priangan - Portal Transaksi Kasir Iklan</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-kp-canvas text-kp-text flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col font-sans">

    <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow">
        <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row shadow-xl rounded-2xl overflow-hidden bg-white border border-kp-border">

            <div class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-14 flex flex-col justify-center">

                <div class="mb-6 flex items-center gap-3">
                    <img src="{{ asset('images/kabarpriangan.png') }}" 
                         alt="Logo Kabar Priangan" 
                         class="h-10 w-10 object-contain rounded-full bg-white p-0.5 shadow-xs border border-slate-200"
                         onerror="this.src='{{ asset('logo.png') }}'">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-800 tracking-tight leading-tight">Kabar Priangan</h2>
                        <span class="text-[11px] font-bold tracking-wider text-kp-blue-600 uppercase">Aplikasi Kasir Iklan</span>
                    </div>
                </div>

                <h1 class="mb-4 text-3xl sm:text-4xl font-bold text-slate-900 leading-tight">
                    Selamat Datang di <br>
                    <span class="text-kp-blue-600">Portal Kasir Iklan</span>
                </h1>

                <p class="mb-8 text-slate-600 text-sm sm:text-base leading-relaxed">
                    Kelola transaksi iklan koran cetak, online portal, dan Priangan TV, cetak faktur, serta pantau laporan keuangan dalam satu sistem terintegrasi.
                </p>

                <div class="flex flex-col sm:flex-row gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="inline-flex items-center justify-center px-7 py-3 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-sm rounded-xl shadow-sm transition">
                            Buka Dashboard &rarr;
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center px-8 py-3 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-sm rounded-xl shadow-sm transition">
                            Login ke Sistem &rarr;
                        </a>
                    @endauth
                </div>

                <p class="mt-10 text-xs text-slate-400 border-t border-slate-100 pt-4">
                    &copy; {{ date('Y') }} Harian Umum Kabar Priangan. All rights reserved.
                </p>
            </div>

            <div class="relative lg:w-[460px] shrink-0 overflow-hidden hidden lg:block bg-slate-50">
                <img src="{{ asset('images/text.jpg') }}" alt="Dashboard Preview"
                    class="w-full h-full object-cover object-center hover:scale-105 transition-transform duration-700 ease-in-out"
                    onerror="this.src='{{ asset('images/kabarpriangan.png') }}'" />

                <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent"></div>
                <div class="absolute inset-0 bg-kp-blue-600/10 mix-blend-multiply"></div>
            </div>
        </main>
    </div>

</body>

</html>
