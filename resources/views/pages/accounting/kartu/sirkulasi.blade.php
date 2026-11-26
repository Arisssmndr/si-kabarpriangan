<x-app-layout>
    <div class="space-y-5">
        <!-- 1. Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Kartu Piutang Agen Sirkulasi (Sub-Ledger)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Kartu Piutang Agen
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Mutasi jatah koran, pembayaran setoran kasir, dan penyesuaian retur (BAPR) agar saldo piutang seimbang (balance).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('accounting.cetak.sirkulasi.cetak', ['tab' => 'saldo-awal', 'bulan' => request('bulan', date('Y-m'))]) }}" target="_blank" class="inline-flex items-center gap-2 h-10 px-4 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Subledger Agen</span>
                </a>
                <a href="{{ route('accounting.kartu.retur') }}" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>+ Entri Penyesuaian Retur (BAPR)</span>
                </a>
            </div>
        </div>

        <!-- 2. Detail Agen & Saldo (Report Info) -->
        <div class="bg-kp-blue-50/60 border border-kp-blue-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm">
            <div>
                <span class="text-xs text-kp-blue-800 uppercase font-semibold block">Agen Terpilih</span>
                <div class="text-base font-bold text-slate-900 mt-0.5">{{ $activeAgen->nama }}</div>
                <div class="text-slate-600 text-xs font-mono mt-0.5">{{ $activeAgen->kode }} &bull; {{ $activeAgen->rute }}</div>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white px-4 py-2.5 rounded-xl border border-kp-blue-200 text-right shadow-2xs">
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Saldo Akhir Piutang</span>
                    <div class="text-lg font-bold font-mono text-kp-blue-700">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- 3. Filter Bar Mandiri -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.kartu.sirkulasi') }}" class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div class="flex-1 max-w-md">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Agen Koran</label>
                    <select name="agen" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        @foreach($daftarAgen as $ag)
                            <option value="{{ $ag->kode }}" {{ $agenPilihan == $ag->kode ? 'selected' : '' }}>
                                {{ $ag->kode }} - {{ $ag->nama }} ({{ $ag->rute }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        Buka Kartu Piutang Agen
                    </button>
                    <a href="{{ route('accounting.kartu.sirkulasi') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 4. Pure Tabel Mutasi Kartu Piutang Agen -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">No. Bukti</th>
                            <th class="py-3 px-3.5">Uraian Mutasi</th>
                            <th class="py-3 px-3.5 text-right">Debet (Jatah Koran)</th>
                            <th class="py-3 px-3.5 text-right">Kredit (Setoran Kas)</th>
                            <th class="py-3 px-3.5 text-right">Retur BAPR</th>
                            <th class="py-3 px-3.5 text-right">Saldo Berjalan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                        @forelse($mutasiPiutang as $m)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-3.5 font-bold text-slate-800">{{ $m->tanggal }}</td>
                            <td class="py-3 px-3.5 font-mono font-semibold text-slate-800">{{ $m->no_bukti }}</td>
                            <td class="py-3 px-3.5 text-slate-700">{{ $m->uraian }}</td>
                            <td class="py-3 px-3.5 text-right font-mono font-medium text-slate-900">
                                {{ $m->debet > 0 ? 'Rp ' . number_format($m->debet, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-3.5 text-right font-mono font-medium text-slate-900">
                                {{ $m->kredit > 0 ? 'Rp ' . number_format($m->kredit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-3.5 text-right font-mono font-medium text-slate-900">
                                {{ $m->retur > 0 ? 'Rp ' . number_format($m->retur, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-kp-blue-700">
                                Rp {{ number_format($m->saldo, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                Belum ada mutasi kartu piutang untuk agen ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
