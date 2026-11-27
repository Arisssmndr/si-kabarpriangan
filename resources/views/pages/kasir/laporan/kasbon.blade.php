<x-app-layout>
    <div class="space-y-5">

        <!-- ============================================================== -->
        <!-- 1. HEADER HALAMAN (PALET 60-30-10 & ACTION BAR)                -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Monitoring Kasbon & Uang Muka (Titipan)
                        </h1>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-sky-50 text-kp-blue-700 border border-kp-blue-200">
                            COA 2.00.01.01.01
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Pengawasan uang muka pemasangan iklan, titipan deposit agen koran sirkulasi, serta kasbon berjalan.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0 flex-wrap print:hidden no-print">
                <button type="button" 
                        onclick="window.print()" 
                        class="inline-flex items-center gap-2 h-10 px-4 bg-slate-800 hover:bg-slate-900 active:bg-slate-950 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Daftar Titipan</span>
                </button>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 2. KPI / STATISTIK CEPAT (METRICS RIBBON 60-30-10)             -->
        <!-- ============================================================== -->
        <div class="print:hidden no-print grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Record Titipan</span>
                <span class="text-2xl font-bold font-mono text-slate-900 mt-1 block">{{ number_format($daftarUangMuka->total(), 0, ',', '.') }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Jumlah transaksi aktif terdata</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akun Kontrol</span>
                <span class="text-2xl font-bold font-mono text-kp-blue-700 mt-1 block">2.00.01.01.01</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Kewajiban Lancar - Uang Muka Pelanggan</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Status Verifikasi</span>
                <span class="text-2xl font-bold font-mono text-slate-900 mt-1 block">Aktif Berjalan</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Terintegrasi otomatis ke buku besar kas</span>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 3. FILTER BAR TERPISAH (H-11 STANDAR IKLAN)                    -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden no-print">
            <form method="GET" action="{{ route('kasir.laporan.kasbon') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Pencarian Data</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari nomor kuitansi, nama pemasang, atau agen penagih..."
                               class="w-full h-11 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Transaksi</label>
                    <input type="date" 
                           name="tanggal" 
                           value="{{ $tanggal ?? request('tanggal') }}"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" 
                            class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Cari</span>
                    </button>
                    @if(!empty($search) || !empty($tanggal))
                        <a href="{{ route('kasir.laporan.kasbon') }}" 
                           title="Reset Filter" 
                           class="h-11 w-11 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Kop Cetak Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-3 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                    <p class="text-[10px] text-slate-600 font-semibold">DIVISI KASIR & KEUANGAN - MONITORING KASBON & TITIPAN</p>
                    <p class="text-[9px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</p>
                </div>
                <div class="text-right">
                    <div class="text-xs font-black uppercase text-slate-900">DAFTAR KASBON & UANG MUKA (TITIPAN)</div>
                    <div class="text-[10px] font-semibold text-slate-700">Tanggal: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
                    <div class="text-[9px] text-slate-400 font-mono">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 4. PURE DATA TABLE (STANDAR DIVISI IKLAN)                      -->
        <!-- ============================================================== -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] border-b-2 border-[#FFB900] text-white font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="px-4 py-3">No. Kuitansi</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Divisi & COA</th>
                            <th class="px-4 py-3">Pemasang / Penyetor</th>
                            <th class="px-4 py-3 text-right">Uang Muka (Rp)</th>
                            <th class="px-4 py-3 text-right">Sisa Tagihan (Rp)</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($daftarUangMuka as $um)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-kp-blue-700">{{ $um->no_kuitansi ?? $um->no_bkm }}</td>
                            <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $um->tanggal_terima ? \Carbon\Carbon::parse($um->tanggal_terima)->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 font-bold text-xs text-slate-700">{{ $um->nama_divisi }}</span>
                                <div class="font-mono text-xs text-slate-400 mt-0.5">{{ $um->kode_perkiraan }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $um->terima_dari ?? $um->nama_pembayar }}</div>
                                <div class="text-xs text-slate-500">{{ $um->keterangan }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-kp-blue-700">
                                Rp {{ number_format($um->jumlah_diterima ?? $um->jumlah_dibayar, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-700">
                                Rp {{ number_format($um->sisa_piutang, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-kp-blue-700 border border-sky-200">
                                    {{ strtoupper($um->status_bayar ?? 'UANG MUKA') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400 text-sm">
                                Tidak ada saldo uang muka atau titipan yang sesuai filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($daftarUangMuka->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 print:hidden bg-slate-50/50">
                    {{ $daftarUangMuka->links() }}
                </div>
            @endif
        </div>

        <!-- Tanda Tangan Laporan Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:grid grid-cols-3 gap-6 text-center text-xs pt-6 mt-6 border-t border-slate-300">
            <div>
                <div class="text-slate-500">Dibuat Oleh:</div>
                <div class="text-slate-700 font-semibold mt-1">Petugas Kasir</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">{{ Auth::user()->name }}</div>
                <div class="text-[10px] text-slate-500">Kasir Priangan</div>
            </div>
            <div>
                <div class="text-slate-500">Diperiksa Oleh:</div>
                <div class="text-slate-700 font-semibold mt-1">Bagian Akuntansi / Pajak</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Kabag Akuntansi</div>
            </div>
            <div>
                <div class="text-slate-500">Mengetahui:</div>
                <div class="text-slate-700 font-semibold mt-1">Pemimpin Perusahaan</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Direktur / GM</div>
            </div>
        </div>

    </div>
</x-app-layout>