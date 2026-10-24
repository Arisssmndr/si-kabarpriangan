<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Proteksi Ketat Anti-Index Mesin Pencari (Internal Enterprise Security) -->
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">
    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Google reCAPTCHA v2 Checkbox ("Saya bukan robot") -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        .select2-container .select2-selection--single {
            width: 100% !important;
            background-color: #f9fafb;
            border: 1px solid #d1d5db !important;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            height: 43px;
            border-radius: 0.4rem;
            color: #1f2937;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            top: 20% !important;
            right: 8px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            font-size: 14px !important;
            top: -2px;
            left: -6px;
            position: relative;
            color: #1f2937;
        }

        .select2-search__field {
            font-size: 14px !important;
            border-radius: 0.5rem;
        }

        .select2-results {
            font-size: 14px !important;
            border-radius: 0px 10px 0px 10px;
        }
    </style>

</head>

<body class="font-sans antialiased bg-[#F8FAFC] text-slate-900 min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden selection:bg-[#0A72AC] selection:text-white">

    <!-- 60% DOMINANT: Clean Architectural Background -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden select-none">
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[650px] h-[420px] bg-gradient-to-tr from-[#0A72AC]/12 via-sky-400/8 to-transparent rounded-full blur-[110px] pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-16 w-[400px] h-[400px] bg-sky-200/25 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute -top-20 -left-16 w-[350px] h-[350px] bg-[#0A72AC]/8 rounded-full blur-[90px] pointer-events-none"></div>
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#0A72AC] to-transparent"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        {{ $slot }}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('partials.alert-scripts')
</body>

</html>
