<x-app-layout>
    <div class="space-y-6">

        <!-- Tier 1: Header Halaman Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-9 sm:h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Pusat Cetak Laporan Anggaran &amp; Realisasi BP
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            ANGGARAN.EXE
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        7 Modul Laporan Resmi Sistem DOS ANGGARAN.EXE HU. Kabar Priangan Tasikmalaya
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('keuangan.anggaran.index') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
                    &larr; Kembali ke Daftar BP
                </a>
            </div>
        </div>

        <!-- Tier 2: 4 KPI Summary Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Periode Aktif</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono mt-1">
                    {{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Buku Anggaran Berjalan
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total BP Cair</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalNominalCair, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Realisasi Kasir Priangan
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Modul Cetak DOS</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">
                    7 <span class="text-sm font-normal text-slate-500">Laporan Resmi</span>
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Format Dot-Matrix &amp; PDF
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Status Sistem</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 mt-1">
                    Terintegrasi
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Real-Time ke Modul Kasir
                </div>
            </div>
        </div>

        <!-- Tier 4: Grid 7 Modul Laporan Resmi Sistem DOS ANGGARAN.EXE -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            <!-- 1. Realisasi Anggaran Seluruh Bidang Priangan -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs hover:border-kp-blue-500 hover:shadow-md transition group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-700 flex items-center justify-center font-mono font-bold text-xs border border-kp-blue-200">
                            01
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            Rekapitulasi 7 Divisi
                        </span>
                    </div>
                    <h2 class="text-base font-bold text-slate-900 group-hover:text-kp-blue-700 transition">
                        Realisasi Seluruh Bidang
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Laporan matriks plafon RAB, penyerapan anggaran, sisa dana, dan persentase untuk seluruh bidang kerja HU. Kabar Priangan.
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-mono font-semibold text-slate-400">Bulanan</span>
                    <a href="{{ route('keuangan.anggaran.cetak.seluruh-bidang') }}" class="inline-flex items-center gap-1.5 h-9 px-3.5 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- 2. Realisasi Anggaran Per Bidang Priangan -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs hover:border-kp-blue-500 hover:shadow-md transition group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-700 flex items-center justify-center font-mono font-bold text-xs border border-kp-blue-200">
                            02
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            Rincian Per Divisi
                        </span>
                    </div>
                    <h2 class="text-base font-bold text-slate-900 group-hover:text-kp-blue-700 transition">
                        Realisasi Per Bidang
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Detail pengeluaran dan rincian transaksi BP per divisi (Iklan, Sirkulasi, Sekum, Akuntansi, Keuangan, Redaksi, Pimpinan).
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-mono font-semibold text-slate-400">Filter Bidang</span>
                    <a href="{{ route('keuangan.anggaran.cetak.per-bidang') }}" class="inline-flex items-center gap-1.5 h-9 px-3.5 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- 3. Rekap BP Priangan Per Tanggal -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs hover:border-kp-blue-500 hover:shadow-md transition group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-700 flex items-center justify-center font-mono font-bold text-xs border border-kp-blue-200">
                            03
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            Rentang Tanggal
                        </span>
                    </div>
                    <h2 class="text-base font-bold text-slate-900 group-hover:text-kp-blue-700 transition">
                        Rekap BP Per Tanggal
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Rekapitulasi bukti pengeluaran berdasarkan rentang tanggal penerbitan, status persetujuan, dan pencairan kasir.
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-mono font-semibold text-slate-400">Harian / Rentang</span>
                    <a href="{{ route('keuangan.anggaran.cetak.bp-tanggal') }}" class="inline-flex items-center gap-1.5 h-9 px-3.5 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- 4. Rekap BP Priangan Per Bulan -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs hover:border-kp-blue-500 hover:shadow-md transition group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-700 flex items-center justify-center font-mono font-bold text-xs border border-kp-blue-200">
                            04
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            Buku Bulanan
                        </span>
                    </div>
                    <h2 class="text-base font-bold text-slate-900 group-hover:text-kp-blue-700 transition">
                        Rekap BP Per Bulan
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Arsip komprehensif buku pengeluaran kas bulanan beserta register anggaran dan bukti nomor kas keluar (BKK).
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-mono font-semibold text-slate-400">Per Bulan</span>
                    <a href="{{ route('keuangan.anggaran.cetak.bp-bulan') }}" class="inline-flex items-center gap-1.5 h-9 px-3.5 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- 5. Rekap Realisasi Anggaran Per Bidang (COA) -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs hover:border-kp-blue-500 hover:shadow-md transition group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-700 flex items-center justify-center font-mono font-bold text-xs border border-kp-blue-200">
                            05
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            Bagan Akun COA
                        </span>
                    </div>
                    <h2 class="text-base font-bold text-slate-900 group-hover:text-kp-blue-700 transition">
                        Rekap Realisasi Per Akun (COA)
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Rekapitulasi akun buku besar akuntansi (Beban Operasional Iklan, Sirkulasi, Sekum, Akuntansi, Redaksi, Pajak &amp; Pimpinan).
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-mono font-semibold text-slate-400">Klasifikasi COA</span>
                    <a href="{{ route('keuangan.anggaran.cetak.rekap-bidang-coa') }}" class="inline-flex items-center gap-1.5 h-9 px-3.5 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- 6. Cetak Ulang BP Per Tanggal -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs hover:border-kp-blue-500 hover:shadow-md transition group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-700 flex items-center justify-center font-mono font-bold text-xs border border-kp-blue-200">
                            06
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            Reprint Voucher
                        </span>
                    </div>
                    <h2 class="text-base font-bold text-slate-900 group-hover:text-kp-blue-700 transition">
                        Cetak Ulang BP Per Tanggal
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Cari cepat dokumen Bukti Pengeluaran berdasarkan tanggal untuk mencetak ulang Voucher Kasir / Lembar Pengajuan resmi.
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-mono font-semibold text-slate-400">Voucher Fisik</span>
                    <a href="{{ route('keuangan.anggaran.cetak.cetak-ulang-bp') }}" class="inline-flex items-center gap-1.5 h-9 px-3.5 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- 7. Rekap Realisasi Anggaran Per Tahun (12 Bulan Komparasi) -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs hover:border-kp-blue-500 hover:shadow-md transition group flex flex-col justify-between md:col-span-2 lg:col-span-3">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-700 flex items-center justify-center font-mono font-bold text-xs border border-kp-blue-200">
                                07
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                Matriks Tahunan (Januari - Desember)
                            </span>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-kp-blue-700 transition">
                            Rekap Realisasi Anggaran Per Tahun (12 Bulan Komparasi)
                        </h2>
                        <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                            Matriks komparasi tahunan menyeluruh menampilkan penyerapan anggaran per bulan (Jan s.d Des) untuk seluruh bidang kerja HU. Kabar Priangan beserta grand total per bulan dan per tahun.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('keuangan.anggaran.cetak.tahunan') }}" class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                            <span>Buka Matriks Tahunan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Identitas Resmi HU Kabar Priangan -->
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-700 flex items-center justify-center font-bold text-xs border border-kp-blue-200">
                    KP
                </div>
                <div>
                    <span class="font-bold text-slate-900 block">HU. KABAR PRIANGAN TASIKMALAYA</span>
                    <span class="text-slate-500 text-xs">Jl. Dindingari Raya No.12, Perum Bumi Resik Panglayungan, Tasikmalaya 46134 | Tlp. (0265)-335300, Fax. 335677</span>
                </div>
            </div>
            <div class="text-right">
                <span class="text-kp-blue-700 font-mono text-xs font-semibold">STANDAR SISTEM DOS ANGGARAN.EXE</span>
            </div>
        </div>

    </div>
</x-app-layout>
