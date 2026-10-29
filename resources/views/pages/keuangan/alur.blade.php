<x-app-layout>
    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Header Navigasi -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <nav class="flex text-xs font-semibold text-slate-500 mb-1 space-x-2">
                        <a href="{{ route('keuangan.dashboard') }}" class="hover:text-blue-600 transition">Keuangan</a>
                        <span>&rsaquo;</span>
                        <span class="text-slate-800">SOP & Alur Kerja Bisnis</span>
                    </nav>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Alur Kerja Faktur, Inkaso & Buku Kas</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Diagram Standard Operating Procedure (SOP) pengelolaan keuangan PT Kabar Priangan</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('keuangan.faktur.index') }}" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Pemantauan Faktur &amp; Piutang
                    </a>
                    <a href="{{ route('keuangan.kas.index') }}" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg shadow-sm transition">
                        Buku Kas Utama
                    </a>
                </div>
            </div>

            <!-- Ringkasan Alur 3 Pilar Utama -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">1</span>
                        <h3 class="font-bold text-slate-900 text-sm">Faktur Otomatis dari Divisi</h3>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Faktur masuk secara <strong>otomatis</strong> dari transaksi Front Office <strong>Iklan</strong> dan <strong>Sirkulasi</strong>. Keuangan bertugas memantau piutang dan menugaskan penagihan ke <strong>Inkaso</strong>.
                    </p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">2</span>
                        <h3 class="font-bold text-slate-900 text-sm">Surat Penugasan Penagihan</h3>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Faktur piutang/tempo dicetakkan <strong>Surat Penugasan fisik</strong> untuk petugas lapangan. Hasil penagihan disetorkan ke Kasir dan memotong piutang faktur.
                    </p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">3</span>
                        <h3 class="font-bold text-slate-900 text-sm">Treasury & Anggaran</h3>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Seluruh kas masuk (faktur/inkaso) dan kas keluar (pencairan anggaran 8 divisi) tercatat real-time dengan penghitungan saldo berjalan otomatis.
                    </p>
                </div>
            </div>

            <!-- Visual Diagram Interaktif -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 lg:p-8">
                <div class="border-b border-slate-100 pb-4 mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-slate-900 text-base">Diagram Alur Proses Finansial</h2>
                        <p class="text-xs text-slate-500">Visualisasi aturan bisnis dari faktur hingga pencatatan mutasi kas</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full">SOP Real-time</span>
                </div>

                <div class="space-y-8">
                    <!-- LEVEL 1: SUMBER TRANSAKSI -->
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Langkah 1: Sumber Transaksi Divisi</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/50 flex items-start space-x-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">Divisi Iklan</h4>
                                    <p class="text-xs text-slate-600 mt-0.5">Penerbitan faktur pemasangan iklan Koran Cetak, Media Online, dan Priangan TV.</p>
                                    <span class="inline-block mt-2 text-[11px] font-semibold text-blue-700 bg-blue-100/70 px-2 py-0.5 rounded">Kategori: Iklan</span>
                                </div>
                            </div>

                            <div class="p-4 rounded-xl border border-indigo-200 bg-indigo-50/50 flex items-start space-x-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-600 text-white flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">Divisi Sirkulasi</h4>
                                    <p class="text-xs text-slate-600 mt-0.5">Penerbitan faktur tagihan distribusi koran ke Agen, Loper, dan Pelanggan Umum.</p>
                                    <span class="inline-block mt-2 text-[11px] font-semibold text-indigo-700 bg-indigo-100/70 px-2 py-0.5 rounded">Kategori: Sirkulasi</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ARROW DOWN -->
                    <div class="flex justify-center -my-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center border border-slate-200">
                            &darr;
                        </div>
                    </div>

                    <!-- LEVEL 2: PENERBITAN FAKTUR -->
                    <div class="p-5 rounded-2xl border-2 border-slate-200 bg-slate-50 text-center max-w-xl mx-auto">
                        <div class="text-xs font-bold text-blue-600 uppercase tracking-wide">Penerbitan Faktur (Modul Faktur)</div>
                        <h3 class="text-base font-bold text-slate-900 mt-1">Form Input Faktur & Pemilihan Cara Bayar</h3>
                        <p class="text-xs text-slate-500 mt-1">Mencatat No. Faktur, Customer, Jatuh Tempo, Rincian Biaya, dan Total Tagihan</p>
                    </div>

                    <!-- LOGIKA PERCABANGAN -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        <!-- JALUR A: LUNAS LANGSUNG -->
                        <div class="p-5 rounded-2xl border-2 border-emerald-300 bg-emerald-50/30 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="px-2.5 py-1 text-xs font-bold bg-emerald-600 text-white rounded-full">Jalur A</span>
                                    <span class="text-xs font-bold text-emerald-700">Bayar Lunas Langsung</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 mb-2">Pelunasan Tunai / Transfer 100%</h4>
                                <ul class="text-xs text-slate-600 space-y-2">
                                    <li class="flex items-start">
                                        <span class="text-emerald-500 font-bold mr-2">&check;</span>
                                        <span>Status faktur langsung diset <strong class="text-emerald-700">Lunas</strong> (Sisa piutang: Rp 0).</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-emerald-500 font-bold mr-2">&check;</span>
                                        <span>Mencatat transaksi pembayaran pelunasan 100%.</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-emerald-500 font-bold mr-2">&check;</span>
                                        <span><strong>Otomatis mutasi Kas Masuk</strong> ke Buku Kas.</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-emerald-500 font-bold mr-2">&times;</span>
                                        <span class="text-slate-500 italic">Faktur <strong>TIDAK</strong> masuk ke antrean petugas inkaso.</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-4 pt-3 border-t border-emerald-200">
                                <span class="text-[11px] font-bold text-emerald-800 bg-emerald-100 px-2 py-1 rounded">Hasil: Saldo Kas Bertambah Seketika</span>
                            </div>
                        </div>

                        <!-- JALUR B: CICILAN / TEMPO -->
                        <div class="p-5 rounded-2xl border-2 border-amber-300 bg-amber-50/30 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="px-2.5 py-1 text-xs font-bold bg-amber-600 text-white rounded-full">Jalur B</span>
                                    <span class="text-xs font-bold text-amber-700">Bayar Bertahap / Tempo</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 mb-2">Pelunasan Bertempo / Uang Muka (DP)</h4>
                                <ul class="text-xs text-slate-600 space-y-2">
                                    <li class="flex items-start">
                                        <span class="text-amber-500 font-bold mr-2">&bull;</span>
                                        <span>Status faktur menjadi <strong class="text-amber-700">Cicilan</strong> (jika ada DP) atau <strong class="text-rose-700">Belum Bayar</strong>.</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-amber-500 font-bold mr-2">&bull;</span>
                                        <span>Jika ada DP, langsung masuk mutasi <strong>Kas Masuk</strong> & mengurangi piutang.</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-amber-500 font-bold mr-2">&bull;</span>
                                        <span>Sisa piutang <strong>otomatis masuk antrean Penugasan Inkaso</strong>.</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-4 pt-3 border-t border-amber-200">
                                <a href="{{ route('keuangan.faktur.index', ['tab' => 'piutang']) }}" class="text-[11px] font-bold text-amber-800 bg-amber-100 hover:bg-amber-200 px-2 py-1 rounded inline-flex items-center transition">
                                    Lihat Faktur Piutang &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- MODUL PENAGIHAN LAPANGAN (INKASO) -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-white">
                        <div class="flex items-center space-x-3 mb-3">
                            <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2-2z"/></svg>
                            </span>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">Modul Penagihan Lapangan (Inkaso)</h3>
                                <p class="text-xs text-slate-500">Alur penugasan collector lapangan hingga setoran ke kasir</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                                <div class="font-bold text-slate-800 mb-1">1. Penugasan Kolektor</div>
                                <p class="text-slate-500">Admin menetapkan faktur yang belum lunas ke Petugas Inkaso dan mencetak <strong>Surat Tugas Harian</strong>.</p>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                                <div class="font-bold text-slate-800 mb-1">2. Penagihan Customer</div>
                                <p class="text-slate-500">Petugas inkaso melakukan penagihan langsung ke klien/customer sesuai daftar tagihan.</p>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                                <div class="font-bold text-slate-800 mb-1">3. Setoran Kasir</div>
                                <p class="text-slate-500">Kasir input setoran &rarr; Cetak kwitansi &rarr; Mengurangi piutang faktur &rarr; Otomatis <strong>Kas Masuk</strong>.</p>
                            </div>
                        </div>
                    </div>

                    <!-- PENGELOLAAN ANGGARAN (CASH OUT) -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-white">
                        <div class="flex items-center space-x-3 mb-3">
                            <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </span>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">Modul Pengajuan Anggaran (Cash Out)</h3>
                                <p class="text-xs text-slate-500">8 Divisi: Iklan, Sirkulasi, Sekum, Redaksi, Akuntansi, Keuangan, Bank, Management Fee</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 gap-3 text-xs">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 font-bold bg-amber-100 text-amber-800 rounded">Pending</span>
                                <span>&rarr; Pengajuan diajukan unit</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 font-bold bg-blue-100 text-blue-800 rounded">Approved</span>
                                <span>&rarr; Disetujui Kepala Divisi / Admin</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 font-bold bg-emerald-100 text-emerald-800 rounded">Cair</span>
                                <span>&rarr; Kasir bayar &rarr; <strong>Kas Keluar</strong> &amp; Voucher</span>
                            </div>
                        </div>
                    </div>

                    <!-- BUKU KAS UTAMA (TREASURY) -->
                    <div class="p-6 rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 text-white shadow-md">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <span class="px-2.5 py-1 text-[11px] font-bold bg-emerald-500/20 text-emerald-300 rounded-full border border-emerald-400/30">Pusat Pembukuan</span>
                                <h3 class="text-lg font-bold text-white mt-2">Buku Kas (Treasury) - Running Balance Terpadu</h3>
                                <p class="text-xs text-slate-300 mt-1 max-w-xl">
                                    Setiap rupiah uang masuk dari Faktur Lunas dan Setoran Inkaso, serta uang keluar dari Pencairan Anggaran terakumulasi otomatis ke dalam Saldo Berjalan (Running Balance) yang tidak dapat dimanipulasi.
                                </p>
                            </div>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                <a href="{{ route('keuangan.kas.harian') }}" class="px-3.5 py-2 text-xs font-semibold text-white bg-slate-700/80 hover:bg-slate-700 rounded-lg text-center transition">
                                    Laporan Harian
                                </a>
                                <a href="{{ route('keuangan.kas.bulanan') }}" class="px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-lg text-center transition">
                                    Laporan Bulanan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
