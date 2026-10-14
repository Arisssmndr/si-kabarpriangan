<x-app-layout>
    <div class="space-y-6">

        <!-- Header Dashboard Divisi Sirkulasi -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Dashboard Divisi Sirkulasi
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-50 text-amber-700 border border-amber-200">
                            Sirkulasi
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Monitoring oplah koran harian, distribusi agen & loper, serta efektivitas pengiriman
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 px-3.5 py-2 rounded-lg border border-slate-200">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span>Edisi Cetak &bull; {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>

        <!-- 4 KPI Cards Sirkulasi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card 1: Oplah Harian -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Oplah Cetak Harian</span>
                    <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-slate-900">
                    {{ number_format($oplahHarian, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">Eksemplar</span>
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Harian Umum Kabar Priangan
                </div>
            </div>

            <!-- Card 2: Agen Aktif -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Jaringan Agen Aktif</span>
                    <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-slate-900">
                    {{ $totalAgen ?? $totalAgenAktif ?? 80 }} <span class="text-xs font-normal text-slate-500">Titik Agen</span>
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Wilayah Priangan Timur
                </div>
            </div>

            <!-- Card 3: Retur Koran -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Estimasi Retur Harian</span>
                    <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-rose-600">
                    {{ $totalRetur }} <span class="text-xs font-normal text-slate-500">Eks (2.2%)</span>
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Ambang batas aman &lt; 5%
                </div>
            </div>

            <!-- Card 4: Efektivitas Distribusi -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Efektivitas Distribusi</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-emerald-600">
                    {{ $efektivitasDistribusi }}%
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Koran terserap pembaca/pelanggan
                </div>
            </div>

        </div>

        <!-- Tabel Distribusi Per Wilayah Priangan Timur -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    Distribusi Oplah Koran per Wilayah Priangan Timur
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">5 Wilayah Cakupan</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Wilayah Distribusi</th>
                            <th class="px-5 py-3 font-semibold text-right">Alokasi Oplah</th>
                            <th class="px-5 py-3 font-semibold text-center">Jumlah Agen</th>
                            <th class="px-5 py-3 font-semibold text-right">Rasio Retur</th>
                            <th class="px-5 py-3 font-semibold text-center">Status Distribusi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($wilayah as $w)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3 font-bold text-slate-800">{{ $w['nama'] }}</td>
                            <td class="px-5 py-3 text-right font-extrabold text-slate-900">{{ number_format($w['oplah'], 0, ',', '.') }} eks</td>
                            <td class="px-5 py-3 text-center text-slate-600 font-semibold">{{ $w['agen'] }} agen</td>
                            <td class="px-5 py-3 text-right font-semibold text-rose-600">{{ $w['retur_pct'] }}%</td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Terkirim Tepat Waktu
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Akses Cepat Menu Operasional Divisi Sirkulasi -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-slate-900">Akses Cepat Modul Operasional Sirkulasi</h3>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Sistem Siap Operasional
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 max-w-3xl leading-relaxed">
                        Pilih menu operasional untuk memproses jatah cetak malam ini, manifes logistik armada, verifikasi retur, dan sinkronisasi piutang:
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 mt-4">
                        <a href="{{ route('sirkulasi.jatah.harian') }}" class="group p-3.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-amber-50/50 hover:border-amber-300 transition block">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 group-hover:text-amber-700">Entri Jatah H+1</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-amber-100 text-amber-800">Siklus H-1</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Input kuota koran per agen untuk edisi besok.</span>
                        </a>

                        <a href="{{ route('sirkulasi.logistik.surat-jalan') }}" class="group p-3.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-sky-50/50 hover:border-sky-300 transition block">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 group-hover:text-sky-700">Surat Jalan Armada</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-sky-100 text-sky-800">Subuh</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Manifes muatan dan daftar kemasan bendel per jalur.</span>
                        </a>

                        <a href="{{ route('sirkulasi.logistik.retur') }}" class="group p-3.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-rose-50/50 hover:border-rose-300 transition block">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 group-hover:text-rose-700">BAPR Koran Retur</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-rose-100 text-rose-800">Piutang</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Pencatatan koran sisa pengurang piutang resmi.</span>
                        </a>

                        <a href="{{ route('sirkulasi.laporan.closing') }}" class="group p-3.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-purple-50/50 hover:border-purple-300 transition block">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 group-hover:text-purple-700">Tutup Data Bulanan</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-purple-100 text-purple-800">Closing</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Kunci data & sinkronisasi Ready to Pull Akunting.</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
