<x-app-layout>
    <div class="space-y-5" x-data="{ tab: 'laporan' }">

        <!-- 1. Header Halaman (Palet 60-30-10 & Action Cetak) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Laporan Arus Kas (Cash Flow)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Metode Langsung
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Penerimaan dan pengeluaran kas riil aktivitas operasi, investasi, dan pendanaan.
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi: Export & Cetak Dokumen Resmi -->
            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button" onclick="exportCSV()" 
                        class="inline-flex items-center gap-2 h-10 px-3.5 bg-white hover:bg-slate-50 active:bg-slate-100 border border-slate-200 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Export Excel</span>
                </button>

                <a href="{{ route('accounting.laporan.arus-kas.cetak', ['periode' => $periode]) }}" target="_blank" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Dokumen Resmi</span>
                </a>
            </div>
        </div>

        <!-- 2. KPI Summary Cards (Palet 60-30-10) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Penerimaan Kas</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($penerimaanPelanggan, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Bulan lalu: Rp {{ number_format($prevTotalKasMasuk, 0, ',', '.') }}</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pengeluaran Kas</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($pembayaranOperasional, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Bulan lalu: Rp {{ number_format($prevTotalKasKeluar, 0, ',', '.') }}</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kas Bersih Operasi</span>
                <div class="text-xl font-bold {{ $arusKasOperasi >= 0 ? 'text-kp-blue-700' : 'text-rose-600' }} mt-1 font-mono">Rp {{ number_format($arusKasOperasi, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Net OCF periode berjalan</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Akhir Kas &amp; Bank</span>
                <div class="text-xl font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($saldoAkhirKas, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Awal: Rp {{ number_format($saldoAwalKas, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- 3. Filter Periode Mandiri (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <form method="GET" action="{{ route('accounting.laporan.arus-kas') }}" id="filterForm" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Periode Laporan</label>
                    <input type="month" 
                           name="periode" 
                           id="inputPeriode"
                           value="{{ $periode }}" 
                           class="h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Terapkan
                    </button>
                    <button type="button" onclick="setBulan('{{ date('Y-m') }}')"
                            class="h-10 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer {{ $periode === date('Y-m') ? 'font-bold bg-slate-200' : '' }}">
                        Bulan Ini
                    </button>
                    <button type="button" onclick="setBulan('{{ date('Y-m', strtotime('-1 month')) }}')"
                            class="h-10 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer {{ $periode === date('Y-m', strtotime('-1 month')) ? 'font-bold bg-slate-200' : '' }}">
                        Bulan Lalu
                    </button>
                    <a href="{{ route('accounting.laporan.arus-kas') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>

            <!-- Navigasi Tab Sederhana -->
            <div class="flex items-center bg-slate-100 p-1 rounded-xl">
                <button type="button" @click="tab = 'laporan'" 
                        :class="tab === 'laporan' ? 'bg-white text-slate-900 font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                        class="px-3.5 py-1.5 text-xs rounded-lg transition cursor-pointer">
                    Laporan Komparatif
                </button>
                <button type="button" @click="tab = 'transaksi'" 
                        :class="tab === 'transaksi' ? 'bg-white text-slate-900 font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                        class="px-3.5 py-1.5 text-xs rounded-lg transition cursor-pointer flex items-center gap-1.5">
                    <span>Mutasi Voucher</span>
                    <span class="px-1.5 py-0.2 rounded-full text-3xs font-bold bg-slate-200 text-slate-700">
                        {{ count($transaksiMasuk) + count($transaksiKeluar) }}
                    </span>
                </button>
            </div>
        </div>

        <!-- TAB 1: PURE TABEL LAPORAN ARUS KAS (PALET 60-30-10 & KOMPARATIF) -->
        <div x-show="tab === 'laporan'" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden p-5 sm:p-6 space-y-6">
            
            <!-- I. AKTIVITAS OPERASI -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="p-3 bg-slate-50 border-b border-slate-200">
                    <h4 class="font-bold uppercase tracking-wider text-slate-800 text-xs">
                        I. Arus Kas dari Aktivitas Operasi
                    </h4>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200 text-2xs text-slate-500 uppercase tracking-wider">
                            <th class="py-2 px-4 text-left font-semibold">Uraian Transaksi</th>
                            <th class="py-2 px-4 text-right font-semibold w-44 font-mono">{{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('M Y') }}</th>
                            <th class="py-2 px-4 text-right font-semibold w-44 font-mono text-slate-400">{{ \Carbon\Carbon::parse($prevPeriode . '-01')->translatedFormat('M Y') }}</th>
                            <th class="py-2 px-4 text-right font-semibold w-36 font-mono text-slate-400">MoM</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 text-slate-700">Penerimaan Kas dari Pelanggan Iklan</td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-slate-900">
                                Rp {{ number_format($penerimaanIklan, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono text-slate-500">
                                Rp {{ number_format($prevPenerimaanIklan, 0, ',', '.') }}
                            </td>
                            @php $deltaIklan = $penerimaanIklan - $prevPenerimaanIklan; @endphp
                            <td class="py-2.5 px-4 text-right font-mono text-xs {{ $deltaIklan >= 0 ? 'text-kp-blue-700' : 'text-rose-600' }}">
                                {{ $deltaIklan >= 0 ? '+' : '' }}Rp {{ number_format($deltaIklan, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 text-slate-700">Penerimaan Kas dari Pelanggan Sirkulasi &amp; Agen</td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-slate-900">
                                Rp {{ number_format($penerimaanSirkulasi, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono text-slate-500">
                                Rp {{ number_format($prevPenerimaanSirkulasi, 0, ',', '.') }}
                            </td>
                            @php $deltaSirk = $penerimaanSirkulasi - $prevPenerimaanSirkulasi; @endphp
                            <td class="py-2.5 px-4 text-right font-mono text-xs {{ $deltaSirk >= 0 ? 'text-kp-blue-700' : 'text-rose-600' }}">
                                {{ $deltaSirk >= 0 ? '+' : '' }}Rp {{ number_format($deltaSirk, 0, ',', '.') }}
                            </td>
                        </tr>
                        @if($penerimaanLainnya > 0 || $prevPenerimaanLainnya > 0)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 text-slate-700">Penerimaan Kas / Bank Operasional Lainnya</td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-slate-900">
                                Rp {{ number_format($penerimaanLainnya, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono text-slate-500">
                                Rp {{ number_format($prevPenerimaanLainnya, 0, ',', '.') }}
                            </td>
                            @php $deltaLain = $penerimaanLainnya - $prevPenerimaanLainnya; @endphp
                            <td class="py-2.5 px-4 text-right font-mono text-xs {{ $deltaLain >= 0 ? 'text-kp-blue-700' : 'text-rose-600' }}">
                                {{ $deltaLain >= 0 ? '+' : '' }}Rp {{ number_format($deltaLain, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 text-slate-700">Pembayaran Komisi Humas (KH) &amp; Insentif</td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-slate-900">
                                @if($pengeluaranKomisi > 0)
                                    (Rp {{ number_format($pengeluaranKomisi, 0, ',', '.') }})
                                @else
                                    Rp 0
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono text-slate-500">
                                @if($prevPengeluaranKomisi > 0)
                                    (Rp {{ number_format($prevPengeluaranKomisi, 0, ',', '.') }})
                                @else
                                    Rp 0
                                @endif
                            </td>
                            @php $deltaKomisi = $pengeluaranKomisi - $prevPengeluaranKomisi; @endphp
                            <td class="py-2.5 px-4 text-right font-mono text-xs text-slate-500">
                                {{ $deltaKomisi != 0 ? ($deltaKomisi > 0 ? '+' : '') . 'Rp ' . number_format($deltaKomisi, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 text-slate-700">Pengeluaran Kas untuk Beban Operasional Kasir (BKK)</td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-slate-900">
                                @if($pengeluaranOperasionalLain > 0)
                                    (Rp {{ number_format($pengeluaranOperasionalLain, 0, ',', '.') }})
                                @else
                                    Rp 0
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono text-slate-500">
                                @if($prevPengeluaranOps > 0)
                                    (Rp {{ number_format($prevPengeluaranOps, 0, ',', '.') }})
                                @else
                                    Rp 0
                                @endif
                            </td>
                            @php $deltaOps = $pengeluaranOperasionalLain - $prevPengeluaranOps; @endphp
                            <td class="py-2.5 px-4 text-right font-mono text-xs text-slate-500">
                                {{ $deltaOps != 0 ? ($deltaOps > 0 ? '+' : '') . 'Rp ' . number_format($deltaOps, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        <tr class="bg-kp-blue-50/70 font-bold border-t border-kp-blue-200 text-slate-900">
                            <td class="py-3 px-4 uppercase text-xs">Arus Kas Bersih dari Aktivitas Operasi</td>
                            <td class="py-3 px-4 text-right font-mono text-sm text-kp-blue-700">
                                Rp {{ number_format($arusKasOperasi, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-sm text-slate-700">
                                Rp {{ number_format($prevArusKasOperasi, 0, ',', '.') }}
                            </td>
                            @php $deltaOCF = $arusKasOperasi - $prevArusKasOperasi; @endphp
                            <td class="py-3 px-4 text-right font-mono text-xs font-bold {{ $deltaOCF >= 0 ? 'text-kp-blue-700' : 'text-rose-700' }}">
                                {{ $deltaOCF >= 0 ? '+' : '' }}Rp {{ number_format($deltaOCF, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- II. AKTIVITAS INVESTASI & PENDANAAN -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="p-3 bg-slate-50 border-b border-slate-200">
                    <h4 class="font-bold uppercase tracking-wider text-slate-800 text-xs">
                        II. Arus Kas dari Aktivitas Investasi &amp; Pendanaan
                    </h4>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200 text-2xs text-slate-500 uppercase tracking-wider">
                            <th class="py-2 px-4 text-left font-semibold">Uraian Transaksi</th>
                            <th class="py-2 px-4 text-right font-semibold w-44 font-mono">{{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('M Y') }}</th>
                            <th class="py-2 px-4 text-right font-semibold w-44 font-mono text-slate-400">{{ \Carbon\Carbon::parse($prevPeriode . '-01')->translatedFormat('M Y') }}</th>
                            <th class="py-2 px-4 text-right font-semibold w-36 font-mono text-slate-400">MoM</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 text-slate-700">Pengeluaran Pembelian Aset Tetap &amp; Mesin Cetak</td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-slate-900">
                                @if($pembelianAsetTetap > 0)
                                    (Rp {{ number_format($pembelianAsetTetap, 0, ',', '.') }})
                                @else
                                    Rp 0
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono text-slate-500">
                                @if($prevPembelianAset > 0)
                                    (Rp {{ number_format($prevPembelianAset, 0, ',', '.') }})
                                @else
                                    Rp 0
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono text-xs text-slate-500">-</td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 text-slate-700">Aktivitas Modal Saham &amp; Pendanaan</td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-slate-900">Rp 0</td>
                            <td class="py-2.5 px-4 text-right font-mono text-slate-500">Rp 0</td>
                            <td class="py-2.5 px-4 text-right font-mono text-xs text-slate-500">-</td>
                        </tr>
                        <tr class="bg-slate-50 font-bold border-t border-slate-200 text-slate-900">
                            <td class="py-3 px-4 uppercase text-xs">Arus Kas Bersih Investasi &amp; Pendanaan</td>
                            <td class="py-3 px-4 text-right font-mono text-sm text-slate-900">
                                Rp {{ number_format($arusKasInvestasi + $arusKasPendanaan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-sm text-slate-700">
                                Rp {{ number_format($prevArusKasInvestasi + $prevArusKasPendanaan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-xs text-slate-500">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- III. REKONSILIASI SALDO KAS & BANK (PALET 60-30-10) -->
            <div class="p-4 bg-kp-blue-50/70 border border-kp-blue-200 rounded-xl space-y-2 text-sm">
                <div class="flex items-center justify-between font-semibold text-slate-700">
                    <span>Saldo Kas Awal Periode</span>
                    <span class="font-mono">Rp {{ number_format($saldoAwalKas, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between font-semibold text-slate-700">
                    <span>Kenaikan (Penurunan) Bersih Kas</span>
                    <span class="font-mono font-bold text-slate-900">
                        {{ $kenaikanBersihKas >= 0 ? '+' : '' }}Rp {{ number_format($kenaikanBersihKas, 0, ',', '.') }}
                    </span>
                </div>
                <div class="border-t border-kp-blue-200 pt-2.5 flex items-center justify-between font-black text-slate-900">
                    <span class="uppercase tracking-wider text-xs sm:text-sm">Saldo Kas Akhir Periode (Kas &amp; Setara Kas)</span>
                    <span class="font-mono text-base text-kp-blue-700">Rp {{ number_format($saldoAkhirKas, 0, ',', '.') }}</span>
                </div>
            </div>

        </div>

        <!-- TAB 2: DAFTAR TRANSAKSI (MUTASI VOUCHER ASLI) -->
        <div x-show="tab === 'transaksi'" class="space-y-4" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                
                <!-- Voucher Kas Masuk (BKM) -->
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                    <div class="p-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Voucher Kas Masuk (BKM)</span>
                        <span class="text-xs font-mono font-bold text-kp-blue-700">{{ count($transaksiMasuk) }} Dokumen</span>
                    </div>
                    <div class="overflow-x-auto max-h-96">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50/50 text-slate-600 border-b border-slate-200 sticky top-0">
                                <tr>
                                    <th class="py-2 px-3 text-left font-semibold">No. BKM / Tgl</th>
                                    <th class="py-2 px-3 text-left font-semibold">Pembayar / Sumber</th>
                                    <th class="py-2 px-3 text-center font-semibold">Metode</th>
                                    <th class="py-2 px-3 text-right font-semibold">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($transaksiMasuk as $km)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-2.5 px-3">
                                            <div class="font-mono font-bold text-slate-900">{{ $km->no_bkm ?? $km->no_kuitansi ?? '-' }}</div>
                                            <div class="text-3xs text-slate-400">{{ \Carbon\Carbon::parse($km->tgl_transaksi ?? $km->tanggal_terima)->format('d/m/Y') }}</div>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <div class="font-medium text-slate-800">{{ $km->terima_dari ?? $km->nama_pembayar ?? 'Pelanggan' }}</div>
                                            <div class="text-3xs text-slate-400 uppercase">{{ $km->tipe_transaksi ?? '-' }}</div>
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            <span class="px-2 py-0.5 rounded text-xs font-semibold uppercase {{ $km->metode_bayar === 'tunai' ? 'bg-slate-100 text-slate-700 border border-slate-200' : 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' }}">
                                                {{ $km->metode_bayar ?? 'tunai' }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-kp-blue-700">
                                            +Rp {{ number_format($km->jumlah_dibayar ?? $km->jumlah_diterima, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-slate-400">Tidak ada transaksi kas masuk pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Voucher Kas Keluar (BKK) -->
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                    <div class="p-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Voucher Kas Keluar (BKK)</span>
                        <span class="text-xs font-mono font-bold text-rose-700">{{ count($transaksiKeluar) }} Dokumen</span>
                    </div>
                    <div class="overflow-x-auto max-h-96">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50/50 text-slate-600 border-b border-slate-200 sticky top-0">
                                <tr>
                                    <th class="py-2 px-3 text-left font-semibold">No. BKK / Tgl</th>
                                    <th class="py-2 px-3 text-left font-semibold">Penerima / Keterangan</th>
                                    <th class="py-2 px-3 text-center font-semibold">Kategori</th>
                                    <th class="py-2 px-3 text-right font-semibold">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($transaksiKeluar as $kk)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-2.5 px-3">
                                            <div class="font-mono font-bold text-slate-900">{{ $kk->no_bkk ?? '-' }}</div>
                                            <div class="text-3xs text-slate-400">{{ \Carbon\Carbon::parse($kk->tanggal_keluar)->format('d/m/Y') }}</div>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <div class="font-medium text-slate-800 truncate max-w-xs">{{ $kk->diberikan_kepada ?? $kk->nama_penerima ?? 'Penerima' }}</div>
                                            <div class="text-3xs text-slate-400 truncate max-w-xs">{{ $kk->keterangan ?? '-' }}</div>
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            <span class="px-2 py-0.5 rounded text-3xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $kk->kategori_beban ?? $kk->kategori ?? 'Operasional' }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-rose-600">
                                            -Rp {{ number_format($kk->nominal_keluar ?? $kk->jumlah_keluar, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-slate-400">Tidak ada transaksi kas keluar pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Script Sederhana -->
    <script>
        function setBulan(val) {
            const input = document.getElementById('inputPeriode');
            if (input) {
                input.value = val;
                document.getElementById('filterForm').submit();
            }
        }

        function exportCSV() {
            const rows = [
                ['PT BERKAH PIKIRAN RAKYAT - HARIAN UMUM KABAR PRIANGAN'],
                ['LAPORAN ARUS KAS (KOMPARATIF)'],
                ['Periode', '{{ $periode }}', 'Bulan Lalu', '{{ $prevPeriode }}'],
                [''],
                ['Pos Akun / Keterangan', '{{ $periode }}', '{{ $prevPeriode }}', 'Perubahan (MoM)'],
                ['I. ARUS KAS DARI AKTIVITAS OPERASI', '', '', ''],
                ['Penerimaan dari Iklan', '{{ $penerimaanIklan }}', '{{ $prevPenerimaanIklan }}', '{{ $penerimaanIklan - $prevPenerimaanIklan }}'],
                ['Penerimaan dari Sirkulasi & Agen', '{{ $penerimaanSirkulasi }}', '{{ $prevPenerimaanSirkulasi }}', '{{ $penerimaanSirkulasi - $prevPenerimaanSirkulasi }}'],
                ['Penerimaan Operasional Lainnya', '{{ $penerimaanLainnya }}', '{{ $prevPenerimaanLainnya }}', '{{ $penerimaanLainnya - $prevPenerimaanLainnya }}'],
                ['Pembayaran Komisi Humas & Insentif', '-{{ $pengeluaranKomisi }}', '-{{ $prevPengeluaranKomisi }}', '{{ $pengeluaranKomisi - $prevPengeluaranKomisi }}'],
                ['Pengeluaran Beban Operasional Kasir', '-{{ $pengeluaranOperasionalLain }}', '-{{ $prevPengeluaranOps }}', '{{ $pengeluaranOperasionalLain - $prevPengeluaranOps }}'],
                ['Arus Kas Bersih Aktivitas Operasi', '{{ $arusKasOperasi }}', '{{ $prevArusKasOperasi }}', '{{ $arusKasOperasi - $prevArusKasOperasi }}'],
                [''],
                ['II. ARUS KAS DARI AKTIVITAS INVESTASI & PENDANAAN', '', '', ''],
                ['Pembelian Aset Tetap & Mesin Cetak', '-{{ $pembelianAsetTetap }}', '-{{ $prevPembelianAset }}', '0'],
                ['Aktivitas Modal Saham & Pendanaan', '0', '0', '0'],
                ['Arus Kas Bersih Investasi & Pendanaan', '{{ $arusKasInvestasi }}', '{{ $prevArusKasInvestasi }}', '0'],
                [''],
                ['III. REKONSILIASI KAS & BANK', '', '', ''],
                ['Saldo Kas Awal Periode', '{{ $saldoAwalKas }}', '{{ $prevSaldoAwalKas }}', '0'],
                ['Kenaikan (Penurunan) Bersih Kas', '{{ $kenaikanBersihKas }}', '{{ $prevKenaikanBersihKas }}', '{{ $kenaikanBersihKas - $prevKenaikanBersihKas }}'],
                ['SALDO KAS AKHIR PERIODE (KAS & BANK)', '{{ $saldoAkhirKas }}', '{{ $prevSaldoAkhirKas }}', '{{ $saldoAkhirKas - $prevSaldoAkhirKas }}']
            ];

            let csv = 'data:text/csv;charset=utf-8,' + rows.map(r => r.map(c => `"${c}"`).join(',')).join('\n');
            const link = document.createElement('a');
            link.href = encodeURI(csv);
            link.download = 'Laporan_Arus_Kas_Komparatif_{{ $periode }}.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</x-app-layout>
