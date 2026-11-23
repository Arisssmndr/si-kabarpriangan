<x-app-layout>
    <div class="space-y-5">
        <!-- 1. Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Jurnal Umum &amp; General Ledger (GL)</h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Double-Entry</span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Posting entri debit-kredit transaksi penarikan iklan, sirkulasi, dan kas.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if($isBalanced)
                    <span class="px-3 py-2 text-xs font-bold rounded-xl bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-kp-blue-600"></span>
                        JURNAL BALANCE (SEIMBANG)
                    </span>
                @else
                    <span class="px-3 py-2 text-xs font-bold rounded-xl bg-slate-100 text-slate-700 border border-slate-300">
                        TIDAK SEIMBANG
                    </span>
                @endif
            </div>
        </div>

        <!-- 2. Filter Periode Mandiri -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <form method="GET" action="{{ route('accounting.gl.index') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Periode Pembukuan</label>
                    <input type="month" 
                           name="bulan" 
                           value="{{ $bulanPilihan }}" 
                           class="h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Filter Jurnal
                    </button>
                    <a href="{{ route('accounting.gl.index') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 3. Pure Tabel Jurnal Umum -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-sm text-left border-collapse min-w-[950px]">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="px-4 py-3 whitespace-nowrap w-28">Tanggal</th>
                            <th class="px-4 py-3 whitespace-nowrap w-36">No. Jurnal</th>
                            <th class="px-4 py-3 whitespace-nowrap w-32 font-mono">Kode Akun</th>
                            <th class="px-4 py-3 min-w-[200px]">Nama Rekening</th>
                            <th class="px-4 py-3 min-w-[220px]">Keterangan Transaksi</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap w-36">Debet (Rp)</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap w-36">Kredit (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                        @foreach($jurnalList as $j)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono text-slate-600 whitespace-nowrap text-xs">
                                {{ $j->tanggal }}
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                                {{ $j->no_jurnal }}
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-kp-blue-700 whitespace-nowrap text-xs">
                                {{ $j->kode_akun }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-900 {{ $j->kredit > 0 ? 'pl-8 text-slate-700' : '' }}">
                                {{ $j->nama_akun }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 text-xs {{ $j->kredit > 0 ? 'pl-8' : '' }}">
                                {{ $j->keterangan }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                {{ $j->debet > 0 ? 'Rp ' . number_format($j->debet, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                {{ $j->kredit > 0 ? 'Rp ' . number_format($j->kredit, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-200 text-xs text-slate-900">
                        <tr>
                            <td colspan="5" class="px-5 py-3 text-right uppercase">Total Debet &amp; Kredit:</td>
                            <td class="px-4 py-3 text-right font-bold text-slate-900 font-mono text-sm">
                                Rp {{ number_format($totalDebet, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-slate-900 font-mono text-sm">
                                Rp {{ number_format($totalKredit, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>