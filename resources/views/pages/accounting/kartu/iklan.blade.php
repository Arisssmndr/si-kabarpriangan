<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="print:hidden no-print bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Kartu Piutang Iklan (Sub-Ledger)</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-purple-50 text-purple-700 border border-purple-200">Kartu Piutang</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Buku pembantu piutang per klien/instansi. Saat kasir mencatat pelunasan dan ditarik akunting, saldo piutang otomatis nol.
                    </p>
                </div>
            </div>

            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-900 text-white shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                <span>Cetak Rekening Koran Klien</span>
            </button>
        </div>

        <!-- Filter Klien -->
        <div class="print:hidden no-print bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.kartu.iklan') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[280px]">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Pilih Klien / Instansi</label>
                    <select name="klien" class="w-full text-xs rounded-lg border-slate-200 focus:border-purple-500 focus:ring-purple-500">
                        @foreach($daftarKlien as $klien)
                            <option value="{{ $klien }}" {{ $klienPilihan == $klien ? 'selected' : '' }}>{{ $klien }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition">
                    Buka Kartu Piutang
                </button>
            </form>
        </div>

        <!-- Kop Cetak Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-3 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                    <p class="text-[10px] text-slate-600 font-semibold">DIVISI KEUANGAN & AKUNTANSI - KARTU PIUTANG KLIEN</p>
                    <p class="text-[9px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</p>
                </div>
                <div class="text-right">
                    <div class="text-xs font-black uppercase text-slate-900">KARTU PIUTANG & REKENING KORAN</div>
                    <div class="text-[10px] font-semibold text-slate-700">Klien: {{ $klienPilihan }}</div>
                    <div class="text-[9px] text-slate-400 font-mono">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- Detail Klien & Status Saldo -->
        <div class="report-print-container bg-purple-50 border border-purple-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs mb-4">
            <div>
                <span class="text-slate-500 uppercase font-semibold text-[10px]">Nama Klien Terpilih</span>
                <div class="text-base font-extrabold text-purple-950 mt-0.5">{{ $klienPilihan }}</div>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white px-3.5 py-2 rounded-lg border border-purple-200 text-right">
                    <span class="text-[10px] text-slate-500 uppercase font-semibold">Sisa Saldo Piutang</span>
                    <div class="text-base font-extrabold text-emerald-700">Rp 0 (LUNAS)</div>
                </div>
            </div>
        </div>

        <!-- Tabel Mutasi Kartu Piutang Iklan -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">No. Referensi / Faktur</th>
                            <th class="px-5 py-3 font-semibold">Uraian Transaksi</th>
                            <th class="px-4 py-3 font-semibold text-right">Debet (Penambahan)</th>
                            <th class="px-4 py-3 font-semibold text-right">Kredit (Pelunasan Kas)</th>
                            <th class="px-4 py-3 font-semibold text-right">Saldo Piutang</th>
                            <th class="px-5 py-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($mutasiIklan as $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3 font-bold text-slate-800">{{ $m->tanggal }}</td>
                            <td class="px-4 py-3 font-mono font-semibold text-purple-700">{{ $m->no_ref }}</td>
                            <td class="px-5 py-3 text-slate-700">{{ $m->uraian }}</td>
                            <td class="px-4 py-3 text-right font-extrabold text-slate-900">
                                {{ $m->debet_piutang > 0 ? 'Rp ' . number_format($m->debet_piutang, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-extrabold text-emerald-700">
                                {{ $m->kredit_bayar > 0 ? 'Rp ' . number_format($m->kredit_bayar, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-black text-slate-900 text-sm">
                                Rp {{ number_format($m->saldo_akhir, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if(str_contains($m->status, 'LUNAS'))
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $m->status }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                        {{ $m->status }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tanda Tangan Kartu Piutang (Hanya Muncul Saat Print) -->
        <div class="hidden print:grid grid-cols-2 gap-8 text-center text-xs pt-6 mt-6 border-t border-slate-300">
            <div>
                <div class="text-slate-500">Klien / Pemasang Iklan:</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">{{ $klienPilihan }}</div>
                <div class="text-[10px] text-slate-500">Konfirmasi Saldo Piutang</div>
            </div>
            <div>
                <div class="text-slate-500">Petugas Akuntansi Piutang:</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Staf AR & Piutang Iklan</div>
            </div>
        </div>
    </div>
</x-app-layout>
