<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Bersih (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Hitung Pembayaran Iklan
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Kartu Iklan
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 truncate">
                        Penarikan kwitansi pelunasan kasir dan pembentukan jurnal kas/bank.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if($sudahDihitung)
                    <div class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 px-3 py-2 rounded-xl border border-kp-blue-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Sudah Dihitung ({{ $logHitung?->nomor_jurnal ?? 'Terekam GL' }})</span>
                    </div>
                @else
                    <div class="text-xs font-semibold text-amber-700 bg-amber-50 px-3 py-2 rounded-xl border border-amber-200 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-[#FFB900]"></span>
                        <span>Belum Dihitung</span>
                    </div>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filter Tanggal & Action Bar (Terpisah Bersih) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <form method="GET" action="{{ route('accounting.iklan.hitung-pembayaran') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Pembayaran</label>
                    <input type="date" 
                           name="tanggal" 
                           value="{{ $tanggalPilihan }}" 
                           class="h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <button type="submit" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center cursor-pointer">
                    Filter Data
                </button>
            </form>

            <div>
                @if(!$sudahDihitung)
                    <form method="POST" action="{{ route('accounting.iklan.proses-hitung-pembayaran') }}">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                        <button type="submit" 
                                onclick="return confirm('Proses hitung pelunasan piutang iklan tanggal {{ $tanggalPilihan }}? Data jurnal kas/bank akan dibentuk.')"
                                class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            <span>Hitung & Posting Kas/Bank</span>
                        </button>
                    </form>
                @else
                    <button type="button" disabled class="h-10 px-4 bg-slate-100 border border-slate-200 text-slate-400 font-semibold text-xs rounded-xl cursor-not-allowed">
                        Data Sudah Dihitung
                    </button>
                @endif
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Jumlah Bukti</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($totalCount) }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Pelunasan</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalBayar, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">PPh 23 Dipotong</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalPPh, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Kas Masuk Netto</span>
                <div class="text-lg font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($totalNettoKas, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Tabel Pembayaran -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] font-bold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">No. Kwitansi / BKM</th>
                            <th class="py-3 px-3.5">No. Faktur Ref</th>
                            <th class="py-3 px-3.5">Pemasang / Pembayar</th>
                            <th class="py-3 px-3.5">Metode Bayar</th>
                            <th class="py-3 px-3.5 text-right">Potongan PPh 23</th>
                            <th class="py-3 px-3.5 text-right">Nominal Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($daftarBayar as $key => $b)
                            @php
                                $noKwitansi = $b->no_kuitansi ?? $b->no_bkm ?? ('BKM-' . $b->id);
                                $noFakturRef = $b->no_faktur_ref ?? $b->transaksiKoran?->nofakturkoran ?? '-';
                                $pembayar = $b->nama_pembayar ?? $b->terima_dari ?? $b->transaksiKoran?->nama_pemasangkoran ?? '-';
                                $metode = $b->metode_bayar ?? 'tunai';
                                $pph = (float) ($b->potongan_pajak ?? 0);
                                $nominal = (float) ($b->jumlah_dibayar ?? $b->jumlah_diterima ?? 0);
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-slate-400 font-mono">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $noKwitansi }}</td>
                                <td class="py-3 px-3.5 text-slate-600 font-mono">{{ $noFakturRef }}</td>
                                <td class="py-3 px-3.5 max-w-xs truncate text-slate-800">{{ $pembayar }}</td>
                                <td class="py-3 px-3.5">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase {{ $metode === 'transfer' ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $metode }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono text-slate-700">Rp {{ number_format($pph, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                    Tidak ada data pembayaran iklan pada tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
