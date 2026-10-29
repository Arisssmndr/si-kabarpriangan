<x-app-layout>
    <div class="space-y-6">

        <!-- Header Pusat Cetak Laporan DOS ANGGARAN.EXE -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                <a href="{{ route('keuangan.anggaran.index') }}" class="w-10 h-10 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition shrink-0" title="Kembali ke Anggaran">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-md bg-purple-100 text-purple-800 text-[10px] font-black uppercase tracking-wider">
                            ANGGARAN.EXE &bull; MENU CETAK
                        </span>
                        <span class="text-xs text-slate-400 font-medium">HU. KABAR PRIANGAN TASIKMALAYA</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                        Pusat Cetak Laporan Anggaran &amp; Realisasi BP
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        7 Modul Laporan Resmi Sistem DOS ANGGARAN.EXE (Jl. Dindingari Raya No. 12 Tasikmalaya)
                    </p>
                </div>
            </div>

            <!-- Periode & Statistik Cepat -->
            <div class="flex items-center gap-3 flex-wrap">
                <div class="px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-left">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Periode Aktif</span>
                    <span class="text-xs font-black text-slate-800 font-mono">{{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}</span>
                </div>
                <div class="px-3.5 py-2 rounded-xl bg-purple-50 border border-purple-200 text-left">
                    <span class="text-[10px] font-bold text-purple-600 uppercase block">Total BP Cair</span>
                    <span class="text-xs font-black text-purple-900 font-mono">Rp {{ number_format($totalNominalCair, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- 7 Menu Cetak Sesuai Tampilan DOS ANGGARAN.EXE -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            <!-- 1. Realisasi Anggaran Seluruh Bidang Priangan -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs hover:shadow-md hover:border-purple-300 transition group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-black text-xs font-mono border border-emerald-200">
                            01
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                            Rekapitulasi 7 Divisi
                        </span>
                    </div>
                    <h2 class="text-base font-extrabold text-slate-900 group-hover:text-purple-700 transition">
                        Realisasi Seluruh Bidang Priangan
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Laporan matriks plafon RAB, penyerapan anggaran, sisa dana, dan persentase untuk 7 Bidang kerja HU. Kabar Priangan.
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] font-mono font-semibold text-slate-400">Bulanan</span>
                    <a href="{{ route('keuangan.anggaran.cetak.seluruh-bidang') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs transition">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- 2. Realisasi Anggaran Per Bidang Priangan -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs hover:shadow-md hover:border-purple-300 transition group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-black text-xs font-mono border border-indigo-200">
                            02
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-[10px] font-bold">
                            Rincian Per Divisi
                        </span>
                    </div>
                    <h2 class="text-base font-extrabold text-slate-900 group-hover:text-purple-700 transition">
                        Realisasi Per Bidang Priangan
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Detail pengeluaran dan rincian transaksi BP per divisi (Iklan, Sirkulasi, Sekum, Akuntansi, Keuangan, Redaksi, Pimpinan).
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] font-mono font-semibold text-slate-400">Filter Bidang</span>
                    <a href="{{ route('keuangan.anggaran.cetak.per-bidang') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs transition">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- 3. Rekap BP Priangan Per Tanggal -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs hover:shadow-md hover:border-purple-300 transition group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center font-black text-xs font-mono border border-cyan-200">
                            03
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-cyan-100 text-cyan-800 text-[10px] font-bold">
                            Rentang Tanggal
                        </span>
                    </div>
                    <h2 class="text-base font-extrabold text-slate-900 group-hover:text-purple-700 transition">
                        Rekap BP Priangan Per Tanggal
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Rekapitulasi bukti pengeluaran berdasarkan rentang tanggal penerbitan, status persetujuan, dan pencairan kasir.
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] font-mono font-semibold text-slate-400">Harian / Range</span>
                    <a href="{{ route('keuangan.anggaran.cetak.bp-tanggal') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs transition">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- 4. Rekap BP Priangan Per Bulan -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs hover:shadow-md hover:border-purple-300 transition group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center font-black text-xs font-mono border border-sky-200">
                            04
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 text-[10px] font-bold">
                            Buku Bulanan
                        </span>
                    </div>
                    <h2 class="text-base font-extrabold text-slate-900 group-hover:text-purple-700 transition">
                        Rekap BP Priangan Per Bulan
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Arsip komprehensif buku pengeluaran kas bulanan beserta register anggaran dan bukti nomor kas keluar (BKK).
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] font-mono font-semibold text-slate-400">Per Bulan</span>
                    <a href="{{ route('keuangan.anggaran.cetak.bp-bulan') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs transition">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- 5. Rekap Realisasi Anggaran Per Bidang (COA) -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs hover:shadow-md hover:border-purple-300 transition group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-violet-50 text-violet-700 flex items-center justify-center font-black text-xs font-mono border border-violet-200">
                            05
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-violet-100 text-violet-800 text-[10px] font-bold">
                            Bagan Akun COA
                        </span>
                    </div>
                    <h2 class="text-base font-extrabold text-slate-900 group-hover:text-purple-700 transition">
                        Rekap Realisasi Per Akun (COA)
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Rekapitulasi akun buku besar akuntansi (Beban Operasional Iklan, Sirkulasi, Sekum, Akuntansi, Redaksi, Pajak &amp; Pimpinan).
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] font-mono font-semibold text-slate-400">Klasifikasi COA</span>
                    <a href="{{ route('keuangan.anggaran.cetak.rekap-bidang-coa') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs transition">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- 6. Cetak Ulang BP Per Tanggal -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs hover:shadow-md hover:border-purple-300 transition group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-black text-xs font-mono border border-amber-200">
                            06
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">
                            Reprint Voucher
                        </span>
                    </div>
                    <h2 class="text-base font-extrabold text-slate-900 group-hover:text-purple-700 transition">
                        Cetak Ulang BP Per Tanggal
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Cari cepat dokumen Bukti Pengeluaran berdasarkan tanggal untuk mencetak ulang Voucher Kasir / Lembar Pengajuan resmi.
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] font-mono font-semibold text-slate-400">Voucher Fisik</span>
                    <a href="{{ route('keuangan.anggaran.cetak.cetak-ulang-bp') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs transition">
                        <span>Buka Laporan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- 7. Rekap Realisasi Anggaran Per Tahun (12 Bulan Komparasi) -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs hover:shadow-md hover:border-purple-300 transition group flex flex-col justify-between md:col-span-2 lg:col-span-3">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center font-black text-xs font-mono border border-rose-200">
                                07
                            </span>
                            <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold">
                                Matriks Tahunan (Januari - Desember)
                            </span>
                        </div>
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-900 group-hover:text-purple-700 transition">
                            Rekap Realisasi Anggaran Per Tahun (12 Bulan Komparasi)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5 max-w-2xl leading-relaxed">
                            Matriks komparasi tahunan menyeluruh menampilkan penyerapan anggaran per bulan (Jan s.d Des) untuk seluruh bidang kerja HU. Kabar Priangan beserta grand total per bulan dan per tahun.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('keuangan.anggaran.cetak.tahunan') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs transition">
                            <span>Buka Matriks Tahunan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Catatan & Alamat Identitas Surat Resmi -->
        <div class="p-4 rounded-2xl bg-slate-900 text-white flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center font-black text-xs text-white">
                    KP
                </div>
                <div>
                    <span class="font-extrabold text-white block">HU. KABAR PRIANGAN TASIKMALAYA</span>
                    <span class="text-slate-400 text-[11px]">Jl. Dindingari Raya No.12, Perum Bumi Resik Panglayungan, Tasikmalaya 46134 | Tlp. (0265)-335300, Fax. 335677</span>
                </div>
            </div>
            <div class="text-right">
                <span class="text-purple-300 font-mono text-[11px] font-semibold">STANDAR SISTEM DOS ANGGARAN.EXE</span>
            </div>
        </div>

    </div>
</x-app-layout>
