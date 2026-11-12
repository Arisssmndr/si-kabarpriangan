<x-app-layout>
    <div class="space-y-6" x-data="{ modalOpen: false, selectedFaktur: null, sisaPiutang: 0, nominalKoreksi: 0 }">

        <!-- ============================================================== -->
        <!-- 1. HEADER HALAMAN (PALET 60-30-10 & ENTERPRISE ACTION BAR)    -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-12 h-12 rounded-xl bg-kp-blue-50 border border-kp-blue-200 flex items-center justify-center text-kp-blue-600 shrink-0 shadow-2xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Koreksi Piutang Iklan
                        </h1>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                            Credit Note (CN)
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Penerbitan nota kredit untuk pembatalan, komplain cetak, atau potongan tagihan piutang iklan.
                    </p>
                </div>
            </div>

            <!-- Action Toolbar Kanan -->
            <div class="flex items-center gap-3 shrink-0 flex-wrap">
                <div class="hidden md:flex items-center gap-1.5 text-xs font-medium text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-200">
                    <span class="text-slate-400">Bukti Berikutnya:</span>
                    <span class="text-slate-900 font-mono font-bold">{{ $nextNoBukti }}</span>
                </div>

                <!-- Tombol Utama: Terbitkan Nota Kredit (Buka Modal Zaman Sekarang) -->
                <button type="button" 
                        onclick="openModalKoreksi()"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Terbitkan Koreksi (CN)</span>
                </button>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 2. KPI / STATISTIK CEPAT (METRICS RIBBON 60-30-10)             -->
        <!-- ============================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Total Dokumen CN -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Dokumen CN</span>
                    <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($stats['total_count'] ?? 0) }}</span>
                    <span class="text-xs text-slate-400 font-normal">Nota Terbit</span>
                </div>
                <p class="text-xs text-slate-400 mt-1 block">Tercatat resmi di pembukuan</p>
            </div>

            <!-- Card 2: Total Nilai Koreksi -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akumulasi Koreksi</span>
                    <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-1">
                    <span class="text-2xl font-bold text-kp-blue-700 font-mono">Rp {{ number_format($stats['total_nominal'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <p class="text-xs text-slate-400 mt-1 block">Pengurang pendapatan piutang</p>
            </div>

            <!-- Card 3: Faktur Terkoreksi -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Faktur Disesuaikan</span>
                    <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($stats['total_faktur'] ?? 0) }}</span>
                    <span class="text-xs text-slate-400 font-normal">Faktur Berbeda</span>
                </div>
                <p class="text-xs text-slate-400 mt-1 block">Faktur pernah dipotong CN</p>
            </div>

            <!-- Card 4: Faktur Siap Koreksi -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Faktur Belum Lunas</span>
                    <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($stats['faktur_aktif'] ?? 0) }}</span>
                    <span class="text-xs text-slate-400 font-normal">Faktur Aktif</span>
                </div>
                <p class="text-xs text-slate-400 mt-1 block">Tersedia sisa piutang > Rp 0</p>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 3. TABEL UTAMA: RIWAYAT NOTA KREDIT (FULL WIDTH CANVAS)       -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            
            <!-- Toolbar Tabel: Search & Filter -->
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Riwayat Nota Kredit (CN-IKL)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh koreksi piutang yang telah dibukukan ke jurnal umum.</p>
                </div>

                <!-- Form Search & Filter -->
                <form method="GET" action="{{ route('accounting.iklan.koreksi') }}" class="flex items-center gap-2.5 flex-wrap">
                    <div class="relative w-full sm:w-64">
                        <input type="text" 
                               name="q" 
                               value="{{ request('q') }}"
                               placeholder="Cari No. Bukti, Faktur, Klien..." 
                               class="w-full h-10 pl-9 pr-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:bg-white transition" />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <button type="submit" 
                            class="h-10 px-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl border border-slate-300 transition cursor-pointer">
                        Filter
                    </button>

                    @if(request('q') || request('bulan') || request('tahun'))
                        <a href="{{ route('accounting.iklan.koreksi') }}" 
                           class="h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl border border-slate-300 transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] font-bold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3.5 px-4 w-12 text-center text-slate-400">No</th>
                            <th class="py-3.5 px-4">No. Bukti CN</th>
                            <th class="py-3.5 px-4">No. Faktur</th>
                            <th class="py-3.5 px-4">Pemasang / Klien</th>
                            <th class="py-3.5 px-4">Tanggal Koreksi</th>
                            <th class="py-3.5 px-4 text-right">Nilai Koreksi</th>
                            <th class="py-3.5 px-4 text-right">Sisa Piutang</th>
                            <th class="py-3.5 px-4">Alasan / Keterangan</th>
                            <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-xs whitespace-nowrap">
                        @forelse($daftarCn as $key => $cn)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- No -->
                                <td class="py-3 px-4 text-center text-slate-400 font-medium">
                                    {{ $daftarCn->firstItem() + $key }}
                                </td>

                                <!-- No Bukti CN -->
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-mono font-bold text-slate-900 bg-slate-100 border border-slate-200">
                                        {{ $cn->no_bukti }}
                                    </span>
                                </td>

                                <!-- No Faktur -->
                                <td class="py-3 px-4">
                                    <span class="font-mono font-semibold text-slate-700">
                                        {{ $cn->no_faktur }}
                                    </span>
                                </td>

                                <!-- Nama Pemasang / Klien -->
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2.5 min-w-[180px] max-w-[240px]">
                                        <div class="w-7 h-7 rounded-lg bg-kp-blue-50 text-kp-blue-700 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($cn->nama_entitas ?: 'K', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-900 truncate" title="{{ $cn->nama_entitas }}">
                                                {{ $cn->nama_entitas ?: '-' }}
                                            </p>
                                            <p class="text-[11px] text-slate-400 truncate">
                                                {{ $cn->alamat ?: 'Priangan' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Tanggal Koreksi -->
                                <td class="py-3 px-4 text-slate-600">
                                    {{ \Carbon\Carbon::parse($cn->tanggal_koreksi)->translatedFormat('d M Y') }}
                                </td>

                                <!-- Nilai Koreksi -->
                                <td class="py-3 px-4 text-right">
                                    <span class="font-mono font-bold text-slate-900">
                                        Rp {{ number_format($cn->jumlah_koreksi, 0, ',', '.') }}
                                    </span>
                                </td>

                                <!-- Sisa Piutang Setelah Koreksi -->
                                <td class="py-3 px-4 text-right font-mono font-semibold">
                                    @if($cn->saldo_setelah_koreksi <= 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Lunas (Rp 0)
                                        </span>
                                    @else
                                        <span class="text-slate-800">
                                            Rp {{ number_format($cn->saldo_setelah_koreksi, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Alasan -->
                                <td class="py-3 px-4 max-w-xs truncate text-slate-600" title="{{ $cn->keterangan }}">
                                    {{ $cn->keterangan }}
                                </td>

                                <!-- Aksi Cetak -->
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('accounting.iklan.cetak-koreksi', $cn->id) }}" 
                                       target="_blank"
                                       title="Cetak Dokumen Nota Kredit"
                                       class="inline-flex items-center gap-1.5 h-8 px-3 bg-white hover:bg-kp-blue-50 hover:text-kp-blue-700 active:bg-kp-blue-100 text-slate-700 text-xs font-semibold rounded-lg border border-slate-300 hover:border-kp-blue-300 transition shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        <span>Cetak CN</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center">
                                    <div class="max-w-xs mx-auto flex flex-col items-center justify-center text-center">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-800">Belum Ada Nota Kredit Iklan</h3>
                                        <p class="text-xs text-slate-500 mt-1">
                                            @if(request('q'))
                                                Tidak ditemukan nota kredit dengan kata kunci "<strong>{{ request('q') }}</strong>".
                                            @else
                                                Seluruh faktur iklan belum memiliki potongan atau pembatalan koreksi.
                                            @endif
                                        </p>
                                        <button type="button" 
                                                onclick="openModalKoreksi()"
                                                class="mt-4 inline-flex items-center gap-1.5 h-9 px-3.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                            <span>Terbitkan Nota Kredit Sekarang</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            @if($daftarCn->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                    {{ $daftarCn->links() }}
                </div>
            @endif
        </div>

        <!-- ============================================================== -->
        <!-- 4. MODAL DIALOG TERBITKAN NOTA KREDIT (SELARAS DIVISI IKLAN)  -->
        <!-- ============================================================== -->
        <div id="modalKoreksi" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[92vh] overflow-hidden">
                
                <!-- Modal Header (Bersih & Ramping Standar Divisi Iklan) -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <h3 class="text-base font-bold text-slate-900">Terbitkan Nota Kredit</h3>
                    <button type="button" onclick="closeModalKoreksi()" 
                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer" 
                            title="Tutup">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Form Input Koreksi -->
                <form method="POST" action="{{ route('accounting.iklan.koreksi.store') }}" id="formKoreksiCn" class="flex flex-col flex-1 min-h-0">
                    @csrf

                    <!-- Body Content -->
                    <div class="p-6 space-y-4 text-xs overflow-y-auto flex-1">

                        <!-- Baris 1: No Bukti & Tanggal -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                    No. Bukti
                                </label>
                                <input type="text" 
                                       name="nomor_bukti" 
                                       id="modal_nomor_bukti"
                                       value="{{ $nextNoBukti }}" 
                                       readonly
                                       class="w-full h-11 px-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono font-bold text-slate-600 cursor-not-allowed select-none" />
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                    Tanggal <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" 
                                       name="tanggal" 
                                       id="modal_tanggal"
                                       value="{{ date('Y-m-d') }}" 
                                       required
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                            </div>
                        </div>

                        <!-- Baris 2: Pilih Faktur -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Pilih Faktur <span class="text-rose-500">*</span>
                            </label>
                            <select name="no_faktur" 
                                    id="modal_select_faktur"
                                    onchange="handleFakturSelection(this)"
                                    required 
                                    class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                <option value="" data-piutang="0" data-penjualan="0" data-nama="-">-- Pilih Faktur --</option>
                                @foreach($daftarFaktur as $f)
                                    <option value="{{ $f->nofakturkoran }}" 
                                            data-piutang="{{ (float) $f->piutang_transaksikoran }}"
                                            data-penjualan="{{ (float) $f->totaltagihan_transaksikoran }}"
                                            data-nama="{{ $f->nama_pemasangkoran }}">
                                        {{ $f->nofakturkoran }} &bull; {{ $f->nama_pemasangkoran }} (Sisa: Rp {{ number_format($f->piutang_transaksikoran, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Ringkasan Faktur Terpilih -->
                        <div id="cardInfoFaktur" class="hidden px-3.5 py-2.5 rounded-xl bg-kp-blue-50/70 border border-kp-blue-200/80 flex items-center justify-between text-xs">
                            <div>
                                <span class="text-slate-500 block text-[11px]">Pemasang</span>
                                <span class="font-bold text-slate-900 truncate max-w-[240px] block" id="infoNamaPemasang">-</span>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-500 block text-[11px]">Sisa Piutang</span>
                                <span class="font-mono font-bold text-slate-900 text-sm" id="infoSisaPiutang">Rp 0</span>
                            </div>
                        </div>

                        <!-- Baris 3: Jumlah Koreksi (Rp) -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Jumlah Koreksi <span class="text-rose-500">*</span>
                            </label>
                            
                            <div class="flex rounded-xl border border-slate-300 focus-within:border-kp-blue-600 focus-within:ring-1 focus-within:ring-kp-blue-600 overflow-hidden bg-white shadow-2xs transition h-11">
                                <span class="inline-flex items-center px-3.5 bg-slate-50 border-r border-slate-200 text-xs text-slate-500 font-semibold select-none pointer-events-none">Rp</span>
                                <input type="text" 
                                       name="jumlah_koreksi" 
                                       id="modal_jumlah_koreksi" 
                                       oninput="handleNominalInput(this)" 
                                       placeholder="0" 
                                       required 
                                       class="flex-1 h-11 px-3.5 bg-transparent text-sm font-mono font-bold text-slate-900 border-none focus:outline-none focus:ring-0" />
                            </div>

                            <p id="alertMelebihiPiutang" class="hidden mt-1.5 text-xs text-rose-600 font-medium">
                                Nilai koreksi melebihi sisa piutang faktur.
                            </p>
                        </div>

                        <!-- Baris 4: Keterangan -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Keterangan <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="keterangan" 
                                      id="modal_keterangan" 
                                      rows="2" 
                                      required 
                                      placeholder="Alasan koreksi atau potongan piutang..." 
                                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer (Bersih & Simpel Sesuai Divisi Iklan) -->
                    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0">
                        <button type="button" 
                                onclick="closeModalKoreksi()" 
                                class="h-11 px-5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                id="btnSubmitKoreksi" 
                                class="h-11 px-6 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-xl text-sm font-bold transition cursor-pointer shadow-xs">
                            Simpan Koreksi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- ============================================================== -->
    <!-- JAVASCRIPT MODAL & UX FLOW INTERAKTIF                          -->
    <!-- ============================================================== -->
    @push('scripts')
    <script>
        let currentSisaPiutang = 0;

        function openModalKoreksi() {
            const modal = document.getElementById('modalKoreksi');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                setTimeout(() => {
                    document.getElementById('modal_select_faktur')?.focus();
                }, 100);
            }
        }

        function closeModalKoreksi() {
            const modal = document.getElementById('modalKoreksi');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalKoreksi();
            }
        });

        // Event saat Faktur Dipilih
        function handleFakturSelection(selectEl) {
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            const cardInfo = document.getElementById('cardInfoFaktur');
            const infoNama = document.getElementById('infoNamaPemasang');
            const infoTotal = document.getElementById('infoTotalAsal');
            const infoSisa = document.getElementById('infoSisaPiutang');

            if (selectedOption && selectedOption.value) {
                const nama = selectedOption.getAttribute('data-nama') || '-';
                const total = parseFloat(selectedOption.getAttribute('data-penjualan')) || 0;
                const piutang = parseFloat(selectedOption.getAttribute('data-piutang')) || 0;

                currentSisaPiutang = piutang;

                if (infoNama) infoNama.innerText = nama;
                if (infoTotal) infoTotal.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
                if (infoSisa) infoSisa.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(piutang);

                if (cardInfo) cardInfo.classList.remove('hidden');
            } else {
                currentSisaPiutang = 0;
                if (cardInfo) cardInfo.classList.add('hidden');
            }

            validateSimulation();
        }

        // Handle Input Nominal Format & Realtime Validation
        function handleNominalInput(input) {
            let val = input.value.replace(/\D/g, '');
            if (!val) {
                input.value = '';
                validateSimulation();
                return;
            }
            const num = parseInt(val, 10);
            input.value = new Intl.NumberFormat('id-ID').format(num);
            validateSimulation();
        }

        function validateSimulation() {
            const inputNominal = document.getElementById('modal_jumlah_koreksi');
            const alertWarning = document.getElementById('alertMelebihiPiutang');
            const btnSubmit = document.getElementById('btnSubmitKoreksi');

            if (!inputNominal) return;

            const val = parseInt(inputNominal.value.replace(/\D/g, '') || 0, 10);

            if (val > 0 && currentSisaPiutang > 0 && val > currentSisaPiutang) {
                if (alertWarning) alertWarning.classList.remove('hidden');
                if (btnSubmit) btnSubmit.disabled = true;
            } else {
                if (alertWarning) alertWarning.classList.add('hidden');
                if (btnSubmit) btnSubmit.disabled = false;
            }
        }

        // Form Submit Confirmation
        document.getElementById('formKoreksiCn')?.addEventListener('submit', function(e) {
            const inputNominal = document.getElementById('modal_jumlah_koreksi');
            const val = parseInt(inputNominal?.value.replace(/\D/g, '') || 0, 10);

            if (val <= 0) {
                e.preventDefault();
                alert('Jumlah koreksi harus lebih besar dari Rp 0.');
                inputNominal?.focus();
                return false;
            }

            if (currentSisaPiutang > 0 && val > currentSisaPiutang) {
                e.preventDefault();
                alert('Jumlah koreksi melebihi sisa piutang faktur.');
                inputNominal?.focus();
                return false;
            }
        });
    </script>
    @endpush
</x-app-layout>
