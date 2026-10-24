<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">

    <title>Kabar Priangan &bull; Portal Operasional Lintas Divisi</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">

    <!-- Tipografi Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* 60% Dominan: Latar Bersih Halus Tanpa Pola Apapun */
        .kp-canvas-clean {
            background-color: #F8FAFC;
            background: radial-gradient(circle at 10% 15%, rgba(10, 114, 172, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 90% 85%, rgba(14, 82, 123, 0.06) 0%, transparent 50%),
                        #F8FAFC;
        }

        /* 10% Aksen: Tombol Aksi Utama Biru Kabar Priangan */
        .kp-btn-action {
            background: linear-gradient(135deg, #0A72AC 0%, #085C8D 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 10px 25px -5px rgba(10, 114, 172, 0.35);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .kp-btn-action:hover {
            background: linear-gradient(135deg, #085C8D 0%, #06456B 100%) !important;
            box-shadow: 0 14px 28px -5px rgba(10, 114, 172, 0.45);
            transform: translateY(-2px);
        }
        .kp-btn-action:active {
            transform: translateY(0);
        }
    </style>
</head>

<body class="kp-canvas-clean text-slate-900 min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 lg:p-10 font-sans antialiased selection:bg-[#0A72AC] selection:text-white relative">

    <!-- Garis Halus Identitas Biru di Bagian Atas Halaman -->
    <div class="fixed top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#0A72AC] to-transparent pointer-events-none"></div>

    <!-- =====================================================================
         30% SEKUNDER: Main Dual-Pane Landing Page Container (Bersih, Visual Kuat)
         ===================================================================== -->
    <div class="w-full max-w-5xl my-auto transition-all duration-300">
        <main class="flex flex-col-reverse lg:flex-row bg-white rounded-3xl shadow-[0_20px_50px_-15px_rgba(15,23,42,0.1),0_1px_3px_rgba(0,0,0,0.04)] border border-slate-200/90 overflow-hidden">

            <!-- PANEL KIRI: Ringkas, Bersih, Tanpa Tumpukan Teks -->
            <div class="flex-1 p-8 sm:p-12 lg:p-14 flex flex-col justify-between">
                
                <div>
                    <!-- Header Logo & Identitas Perusahaan -->
                    <div class="flex items-center gap-3.5 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-white p-1 shadow-xs border border-slate-200 flex items-center justify-center shrink-0">
                            <img src="{{ asset('images/kabarpriangan.png') }}" 
                                 alt="Logo Kabar Priangan" 
                                 class="w-full h-full object-contain"
                                 onerror="this.src='{{ asset('logo.png') }}'">
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-black tracking-tight leading-tight">
                                Kabar Priangan
                            </h2>
                            <p class="text-xs font-bold text-[#0A72AC] tracking-wider uppercase">
                                SiKAPRI &bull; Portal Internal
                            </p>
                        </div>
                    </div>

                    <!-- Judul Utama (Modern, Berbobot, Lintas Divisi) -->
                    <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-black text-black leading-tight tracking-tight mb-4">
                        Portal Operasional <br>
                        <span class="text-[#0A72AC]">Lintas Divisi</span>
                    </h1>

                    <!-- Deskripsi Ringkas 1 Kalimat (Tanpa Paragraf Panjang) -->
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed mb-8 max-w-md font-medium">
                        Sistem informasi manajemen terintegrasi untuk staf seluruh divisi Harian Umum Kabar Priangan.
                    </p>

                    <!-- Tag Divisi Bersih & Simpel (Tanpa Teks Penjelasan Panjang) -->
                    <div class="flex flex-wrap gap-2 mb-8 max-w-md">
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 text-xs font-bold border border-slate-200/80">
                            Divisi Iklan & TV
                        </span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 text-xs font-bold border border-slate-200/80">
                            Divisi Sirkulasi
                        </span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 text-xs font-bold border border-slate-200/80">
                            Divisi Keuangan
                        </span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 text-xs font-bold border border-slate-200/80">
                            Redaksi & Pracetak
                        </span>
                    </div>

                    <!-- =====================================================================
                         10% AKSEN: Tombol Aksi Utama
                         ===================================================================== -->
                    <div>
                        @auth
                            <a href="{{ route('dashboard') }}" 
                               class="kp-btn-action inline-flex items-center justify-center gap-3 px-8 py-3.5 font-bold text-sm rounded-xl">
                                <span>Buka Dashboard Sistem</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="kp-btn-action inline-flex items-center justify-center gap-3 px-8 py-3.5 font-bold text-sm rounded-xl">
                                <span>Masuk ke Sistem</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Footer Bersih & Minimalis -->
                <div class="mt-10 pt-5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <p class="font-medium">
                        &copy; {{ date('Y') }} <strong class="text-slate-800 font-bold">Harian Umum Kabar Priangan</strong>
                    </p>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        PT Berkah Pikiran Rakyat
                    </span>
                </div>

            </div>

            <!-- PANEL KANAN: Visual Kantor Pusat & Redaksi yang Ditonjolkan -->
            <div class="relative lg:w-[480px] shrink-0 overflow-hidden bg-slate-900 min-h-[300px] lg:min-h-full flex flex-col justify-end p-6 sm:p-8">
                
                <!-- Foto Gedung Kabar Priangan -->
                <img src="{{ asset('images/text.jpg') }}" 
                     alt="Kantor Kabar Priangan"
                     class="absolute inset-0 w-full h-full object-cover object-center scale-100 hover:scale-105 transition-transform duration-700 ease-in-out"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'" />

                <!-- Gradien Halus Menegaskan Visual Tanpa Menghalangi Bangunan -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent"></div>
                <div class="absolute inset-0 bg-[#0A72AC]/10 mix-blend-multiply"></div>

                <!-- Kartu Identitas Gedung Modern Glassmorphism di Bagian Bawah -->
                <div class="relative z-10 p-5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white shadow-xl">
                    <h3 class="text-base font-extrabold text-white tracking-tight">
                        Kantor Pusat & Graha Redaksi
                    </h3>
                    <p class="text-xs text-slate-200 mt-1 leading-snug font-medium">
                        Harian Umum Kabar Priangan &bull; PT Berkah Pikiran Rakyat
                    </p>
                    <p class="text-[11px] text-sky-200/90 mt-1 font-medium">
                        Tasikmalaya, Jawa Barat
                    </p>
                </div>

            </div>

        </main>
    </div>

</body>

</html>
