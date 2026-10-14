<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Jurnal Umum & General Ledger (GL)</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-purple-50 text-purple-700 border border-purple-200">Double-Entry</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Daftar posting otomatis entri debit-kredit dari seluruh transaksi penarikan iklan, sirkulasi, BAPR, dan rekonsiliasi kas.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($isBalanced)
                    <span class="px-3 py-1.5 text-xs font-bold rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        JURNAL BALANCE (SEIMBANG)
                    </span>
                @else
                    <span class="px-3 py-1.5 text-xs font-bold rounded-lg bg-rose-50 text-rose-800 border border-rose-200">
                        TIDAK SEIMBANG
                    </span>
                @endif
            </div>
        </div>

        <!-- Filter Periode -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.gl.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Periode Pembukuan</label>
                    <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="text-xs rounded-lg border-slate-200 focus:border-purple-500 focus:ring-purple-500">
                </div>
                <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition">
                    Filter Jurnal
                </button>
            </form>
        </div>

        <!-- Tabel Jurnal Umum -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Tanggal & No. Jurnal</th>
                            <th class="px-4 py-3 font-semibold">Kode Akun</th>
                            <th class="px-5 py-3 font-semibold">Nama Rekening & Keterangan</th>
                            <th class="px-5 py-3 font-semibold text-right">Debet (Rp)</th>
                            <th class="px-5 py-3 font-semibold text-right">Kredit (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($jurnalList as $j)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3">
                                <div class="font-bold text-slate-900">{{ $j->tanggal }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $j->no_jurnal }}</div>
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-purple-700">
                                {{ $j->kode_akun }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-bold text-slate-900 {{ $j->kredit > 0 ? 'pl-6' : '' }}">{{ $j->nama_akun }}</div>
                                <div class="text-[11px] text-slate-500 {{ $j->kredit > 0 ? 'pl-6' : '' }}">{{ $j->keterangan }}</div>
                            </td>
                            <td class="px-5 py-3 text-right font-extrabold text-slate-900">
                                {{ $j->debet > 0 ? 'Rp ' . number_format($j->debet, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3 text-right font-extrabold text-slate-900">
                                {{ $j->kredit > 0 ? 'Rp ' . number_format($j->kredit, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-200 text-xs text-slate-900">
                        <tr>
                            <td colspan="3" class="px-5 py-3 text-right uppercase">Total Debet & Kredit:</td>
                            <td class="px-5 py-3 text-right font-black text-purple-900 text-sm">
                                Rp {{ number_format($totalDebet, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-right font-black text-purple-900 text-sm">
                                Rp {{ number_format($totalKredit, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
