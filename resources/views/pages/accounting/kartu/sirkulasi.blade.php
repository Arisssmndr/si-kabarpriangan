<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Kartu Piutang Agen Sirkulasi (Sub-Ledger)</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-50 text-amber-700 border border-amber-200">Kartu Piutang Agen</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Mutasi jatah koran, pembayaran setoran kasir, dan penyesuaian retur (BAPR) agar saldo piutang seimbang (balance).
                    </p>
                </div>
            </div>

            <a href="{{ route('accounting.kartu.retur') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>+ Entri Penyesuaian Retur (BAPR)</span>
            </a>
        </div>

        <!-- Filter Agen -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.kartu.sirkulasi') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[280px]">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Pilih Agen Koran</label>
                    <select name="agen" class="w-full text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                        @foreach($daftarAgen as $ag)
                            <option value="{{ $ag->kode }}" {{ $agenPilihan == $ag->kode ? 'selected' : '' }}>
                                {{ $ag->kode }} - {{ $ag->nama }} ({{ $ag->rute }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition">
                    Buka Kartu Piutang Agen
                </button>
            </form>
        </div>

        <!-- Detail Agen & Saldo -->
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
            <div>
                <span class="text-amber-800 uppercase font-semibold text-[10px]">Agen Terpilih</span>
                <div class="text-base font-extrabold text-amber-950 mt-0.5">{{ $activeAgen->nama }}</div>
                <div class="text-amber-800 text-[11px] font-mono mt-0.5">{{ $activeAgen->kode }} &bull; {{ $activeAgen->rute }}</div>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white px-3.5 py-2 rounded-lg border border-amber-200 text-right">
                    <span class="text-[10px] text-slate-500 uppercase font-semibold">Saldo Akhir Piutang</span>
                    <div class="text-base font-extrabold text-slate-900">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Tabel Mutasi Kartu Piutang Agen -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">No. Bukti</th>
                            <th class="px-5 py-3 font-semibold">Uraian Mutasi</th>
                            <th class="px-4 py-3 font-semibold text-right">Debet (Jatah Koran)</th>
                            <th class="px-4 py-3 font-semibold text-right">Kredit (Setoran Kas)</th>
                            <th class="px-4 py-3 font-semibold text-right">Retur BAPR</th>
                            <th class="px-5 py-3 font-semibold text-right">Saldo Berjalan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($mutasiPiutang as $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3 font-bold text-slate-800">{{ $m->tanggal }}</td>
                            <td class="px-4 py-3 font-mono font-semibold text-amber-800">{{ $m->no_bukti }}</td>
                            <td class="px-5 py-3 text-slate-700">{{ $m->uraian }}</td>
                            <td class="px-4 py-3 text-right font-extrabold text-slate-900">
                                {{ $m->debet > 0 ? 'Rp ' . number_format($m->debet, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-extrabold text-emerald-700">
                                {{ $m->kredit > 0 ? 'Rp ' . number_format($m->kredit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-extrabold text-rose-600">
                                {{ $m->retur > 0 ? 'Rp ' . number_format($m->retur, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3 text-right font-black text-slate-900 text-sm">
                                Rp {{ number_format($m->saldo, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
