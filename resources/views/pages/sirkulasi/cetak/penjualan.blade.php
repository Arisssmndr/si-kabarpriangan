<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Menu Cetak</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Laporan Penjualan Sirkulasi</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi penjualan bulanan terhubung ke Divisi Keuangan / Faktur: Kirim, Retur, Nett Sales, Nilai Tagihan, & Saldo Piutang
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('sirkulasi.cetak.penjualan') }}" class="flex items-center gap-2">
                    <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="text-xs rounded-lg border-slate-200 py-1.5 focus:border-emerald-500 focus:ring-emerald-500">
                    <button type="submit" class="py-1.5 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition-colors cursor-pointer">
                        Filter
                    </button>
                </form>
                <button type="button" onclick="window.print()" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Faktur Penjualan</span>
                </button>
            </div>
        </div>

        <!-- KPI Ringkasan Keuangan Sirkulasi -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Koran Dikirim</span>
                <div class="text-xl font-extrabold text-slate-900 mt-1">{{ number_format($totalKirim, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">eks</span></div>
                <div class="text-[11px] text-slate-400 mt-0.5">Seluruh Agen Terdaftar</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-rose-600 uppercase tracking-wider">Total Koran Diretur</span>
                <div class="text-xl font-extrabold text-rose-700 mt-1">{{ number_format($totalRetur, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">eks</span></div>
                <div class="text-[11px] text-slate-400 mt-0.5">Rasio Retur: 2.2% (Toleransi &le; 10%)</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Nett Sales Terjual</span>
                <div class="text-xl font-extrabold text-emerald-700 mt-1">{{ number_format($totalNett, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">eks</span></div>
                <div class="text-[11px] text-slate-400 mt-0.5">Kirim Dikurangi Retur</div>
            </div>

            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-emerald-800 uppercase tracking-wider">Total Nilai Tagihan</span>
                <div class="text-xl font-extrabold text-emerald-900 mt-1">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                <div class="text-[11px] text-emerald-700 mt-0.5">Disinkronkan ke Modul Faktur</div>
            </div>
        </div>

        <!-- Tabel Rekapitulasi Penjualan Agen -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Rekapitulasi Tagihan & Piutang Agen Bulanan</h3>
                    <p class="text-[11px] text-slate-500">Periode: {{ \Carbon\Carbon::parse($bulanPilihan . '-01')->translatedFormat('F Y') }} &bull; Hubungan Akuntansi: Debit Tagihan & Saldo Berjalan</p>
                </div>
                <div class="text-right text-xs text-slate-500">
                    Tarif Agen Besar: <strong class="text-slate-800">Rp2.800</strong> &bull; Sub Agen: <strong class="text-slate-800">Rp3.000</strong>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 min-w-[1100px]">
                    <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3.5 whitespace-nowrap w-32">Kode Agen</th>
                            <th class="py-3 px-4 min-w-[200px]">Nama Agen & Kios</th>
                            <th class="py-3 px-3 whitespace-nowrap w-28">Wilayah</th>
                            <th class="py-3 px-3 text-right whitespace-nowrap w-28">Kirim (Eks)</th>
                            <th class="py-3 px-3 text-right text-rose-600 whitespace-nowrap w-24">Retur (Eks)</th>
                            <th class="py-3 px-3 text-right font-extrabold text-slate-900 whitespace-nowrap w-24">Nett Sales</th>
                            <th class="py-3 px-3.5 text-right whitespace-nowrap w-28">Harga</th>
                            <th class="py-3 px-4 text-right font-bold text-emerald-800 whitespace-nowrap w-36">Nilai Tagihan</th>
                            <th class="py-3 px-4 text-right whitespace-nowrap w-36">Saldo Piutang</th>
                            <th class="py-3 px-3 text-center whitespace-nowrap w-24">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($rekapPenjualan as $rp)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md font-mono font-bold text-slate-800 bg-slate-100 text-xs border border-slate-200/90 tracking-wide whitespace-nowrap">
                                        {{ $rp->kode_agen }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900 text-xs">
                                    {{ $rp->nama_agen }}
                                </td>
                                <td class="py-3 px-3 text-xs text-slate-600 whitespace-nowrap">
                                    {{ $rp->wilayah }}
                                </td>
                                <td class="py-3 px-3 text-right whitespace-nowrap font-semibold text-slate-800 tabular-nums">
                                    {{ number_format($rp->kirim, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right text-rose-600 font-semibold whitespace-nowrap tabular-nums">
                                    {{ number_format($rp->retur, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-slate-900 whitespace-nowrap tabular-nums">
                                    {{ number_format($rp->nett_sales, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono text-xs text-slate-700 whitespace-nowrap tabular-nums">
                                    <span class="text-slate-400 text-[11px] mr-1">Rp</span>{{ number_format($rp->harga_satuan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-emerald-800 whitespace-nowrap tabular-nums font-mono">
                                    <span class="text-emerald-600/70 text-[11px] mr-1">Rp</span>{{ number_format($rp->nilai_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-extrabold whitespace-nowrap tabular-nums font-mono text-xs {{ $rp->saldo_piutang > 0 ? 'text-rose-600' : 'text-slate-600' }}">
                                    <span class="{{ $rp->saldo_piutang > 0 ? 'text-rose-400' : 'text-slate-400' }} text-[11px] mr-1">Rp</span>{{ number_format($rp->saldo_piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border whitespace-nowrap shadow-2xs {{ $rp->status_bayar === 'Lancar' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $rp->status_bayar === 'Lancar' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ $rp->status_bayar }}</span>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-100 font-bold text-slate-800 border-t-2 border-slate-200">
                        <tr>
                            <td colspan="3" class="py-3 px-4 text-right uppercase text-[11px] whitespace-nowrap">Akumulasi Total:</td>
                            <td class="py-3 px-3 text-right whitespace-nowrap tabular-nums">{{ number_format($totalKirim, 0, ',', '.') }} eks</td>
                            <td class="py-3 px-3 text-right text-rose-600 whitespace-nowrap tabular-nums">{{ number_format($totalRetur, 0, ',', '.') }} eks</td>
                            <td class="py-3 px-3 text-right text-slate-900 whitespace-nowrap tabular-nums">{{ number_format($totalNett, 0, ',', '.') }} eks</td>
                            <td class="py-3 px-3.5 text-right text-slate-400 whitespace-nowrap">-</td>
                            <td class="py-3 px-4 text-right text-emerald-900 text-sm font-extrabold whitespace-nowrap tabular-nums font-mono">
                                <span class="text-emerald-700 text-xs mr-0.5">Rp</span>{{ number_format($totalTagihan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right text-rose-600 text-xs font-extrabold whitespace-nowrap tabular-nums font-mono">
                                <span class="text-rose-400 text-[11px] mr-0.5">Rp</span>{{ number_format($totalSaldoPiutang, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center text-slate-400 whitespace-nowrap">-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
                <span>Data penjualan otomatis tersinkronisasi dengan Surat Jalan Pengepakan & Faktur Akuntansi.</span>
                <span>Harian Umum Kabar Priangan &bull; Divisi Sirkulasi</span>
            </div>
        </div>

    </div>
</x-app-layout>
