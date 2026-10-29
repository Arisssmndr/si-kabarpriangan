<x-app-layout>
    <div class="space-y-6">

        <!-- 1. Header Halaman Konsisten -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-kp-blue-600 uppercase tracking-wider">Pengaturan Akun</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Profil & Keamanan Pengguna
                        </h1>
                    </div>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Kelola data pribadi, alamat email kedinasan, dan kata sandi akses sistem internal Kabar Priangan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-medium">Sesi Terautentikasi &bull; {{ $user->role_label }}</span>
            </div>
        </div>

        <!-- 2. Hero Card Profil Pengguna (Tampilan Modern & Dinamis) -->
        <div class="relative overflow-hidden bg-gradient-to-r from-slate-950 via-slate-900 to-kp-blue-950 text-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-800">
            <!-- Elemen Dekoratif Latar Belakang -->
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-kp-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-32 -bottom-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <!-- Info Utama & Avatar -->
                <div class="flex items-center gap-5 sm:gap-6">
                    <div class="relative shrink-0">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-tr from-kp-blue-600 via-sky-500 to-teal-400 text-white font-black text-3xl sm:text-4xl flex items-center justify-center shadow-xl border-2 border-white/20">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-slate-900 flex items-center justify-center shadow-xs" title="Akun Aktif">
                            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                        </span>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                                {{ $user->name }}
                            </h2>
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-white/10 backdrop-blur-md text-sky-200 border border-white/15">
                                {{ $user->role_label }}
                            </span>
                        </div>

                        <p class="text-xs sm:text-sm text-slate-300 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <span>{{ $user->email }}</span>
                        </p>

                        <div class="flex flex-wrap items-center gap-3 pt-1 text-[11px] text-slate-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>Status Akun Aktif</span>
                            </span>
                            <span>&bull;</span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <span>Terdaftar: {{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Keamanan Singkat -->
                <div class="flex md:flex-col items-center md:items-end justify-between border-t md:border-t-0 border-white/10 pt-4 md:pt-0 gap-3">
                    <div class="text-left md:text-right">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Level Hak Akses</span>
                        <div class="text-sm font-bold text-emerald-400">
                            {{ $user->isAdmin() ? 'Super Administrator' : 'Staf Operasional' }}
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Perlindungan Akun</span>
                        <div class="text-xs font-semibold text-slate-300 flex items-center gap-1 justify-end">
                            <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            <span>Terenkripsi Standar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Grid Tata Letak 2 Kolom -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- Kolom Kiri: Ringkasan Hak Akses Divisi & Bantuan (4 Kolom) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Kartu Status Akses 5 Divisi -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 text-kp-blue-700 border border-sky-200 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Hak Akses Modul Divisi</h3>
                            <p class="text-[11px] text-slate-500">Otoritas menu sesuai penugasan peran</p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2.5">
                        @php
                            $divisiList = [
                                ['key' => 'sirkulasi', 'name' => 'Divisi Sirkulasi', 'desc' => 'Jatah Harian, Hitung, Agen, Surat Jalan'],
                                ['key' => 'keuangan', 'name' => 'Divisi Keuangan', 'desc' => 'Faktur, Inkaso, Anggaran, Buku Kas'],
                                ['key' => 'kasir', 'name' => 'Divisi Kasir', 'desc' => 'LHK Harian, Kwitansi, Penerimaan'],
                                ['key' => 'iklan', 'name' => 'Divisi Iklan', 'desc' => 'Order Iklan, Kolom, Baris, Display'],
                                ['key' => 'accounting', 'name' => 'Divisi Accounting', 'desc' => 'Jurnal, Buku Besar, Neraca, Laba Rugi'],
                            ];
                        @endphp

                        @foreach($divisiList as $div)
                            @php
                                $hasAccess = $user->canAccessDivision($div['key']);
                            @endphp
                            <div class="flex items-center justify-between p-2.5 rounded-xl border transition-colors {{ $hasAccess ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200 opacity-60' }}">
                                <div class="min-w-0 pr-2">
                                    <div class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                        <span>{{ $div['name'] }}</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 truncate">{{ $div['desc'] }}</p>
                                </div>
                                <span class="shrink-0 px-2 py-0.5 rounded text-[10px] font-bold {{ $hasAccess ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $hasAccess ? 'Diizinkan' : 'Terkunci' }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if($user->isAdmin())
                        <div class="mt-4 p-2.5 rounded-xl bg-kp-blue-50 border border-kp-blue-200 text-[11px] text-kp-blue-800 font-semibold flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>Akun Administrator memiliki akses penuh ke seluruh 5 divisi operasional.</span>
                        </div>
                    @endif
                </div>

                <!-- Kartu Bantuan & Helpdesk IT Kantor -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        <span>Pusat Bantuan & IT</span>
                    </h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Jika Anda membutuhkan perubahan peran divisi, izin akses khusus, atau kendala login, silakan hubungi bagian IT / Tata Usaha Kabar Priangan.
                    </p>
                    <div class="text-xs text-slate-700 space-y-1.5 pt-2 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="text-slate-400 w-16">Kantor:</span>
                            <span class="font-semibold text-slate-800">Jl. Dr. Sukardjo No. 84 Tasikmalaya</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-slate-400 w-16">Telepon:</span>
                            <span class="font-semibold text-slate-800">(0265) 331234</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Formulir Pembaruan Profil & Keamanan (8 Kolom) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- 1. Formulir Informasi Profil & Kontak -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-7 shadow-xs">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <!-- 2. Formulir Ganti Kata Sandi -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-7 shadow-xs">
                    @include('profile.partials.update-password-form')
                </div>

                <!-- 3. Formulir Hapus / Nonaktifkan Akun (Zona Bahaya) -->
                <div class="bg-white border border-rose-200 rounded-2xl p-6 sm:p-7 shadow-xs">
                    @include('profile.partials.delete-user-form')
                </div>

            </div>

        </div>

    </div>
</x-app-layout>
