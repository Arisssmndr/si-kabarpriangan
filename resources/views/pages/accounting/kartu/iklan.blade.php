<x-app-layout>
    <div class="space-y-5">
        <!-- 1. Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Kartu Piutang Iklan (Sub-Ledger)</h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Kartu Piutang</span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Buku pembantu piutang per klien/instansi. Saat kasir mencatat pelunasan, saldo otomatis nol.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('accounting.cetak.iklan.cetak', ['tab' => 'piutang', 'bulan' => request('bulan', date('Y-m'))]) }}" target="_blank" 
                   class="inline-flex items-center gap-2 h-10 px-4 text-xs font-semibold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Rekening Koran Klien</span>
                </a>
            </div>
        </div>

        <!-- 2. Detail Klien & Status Saldo (Report Info) -->
        <div class="bg-kp-blue-50/70 border border-kp-blue-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
            <div>
                <span class="text-slate-500 uppercase font-semibold text-xs">Nama Klien Terpilih</span>
                <div class="text-base font-bold text-slate-900 mt-0.5">{{ $klienPilihan }}</div>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white px-3.5 py-2 rounded-xl border border-kp-blue-200 text-right">
                    <span class="text-xs text-slate-500 uppercase font-semibold">Sisa Saldo Piutang</span>
                    <div class="text-base font-bold text-kp-blue-700 font-mono">Rp 0 (LUNAS)</div>
                </div>
            </div>
        </div>

        <!-- 3. Filter Klien Mandiri -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.kartu.iklan') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="flex-1 min-w-[280px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Klien / Instansi</label>
                    <select name="klien" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                        @foreach($daftarKlien as $klien)
                            <option value="{{ $klien }}" {{ $klienPilihan == $klien ? 'selected' : '' }}>{{ $klien }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        Buka Kartu Piutang
                    </button>
                    <a href="{{ route('accounting.kartu.iklan') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition inline-flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 4. Pure Tabel Mutasi Kartu Piutang Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">No. Referensi Faktur</th>
                            <th class="px-4 py-3 font-semibold">Uraian Transaksi</th>
                            <th class="px-4 py-3 font-semibold text-right">Debet (Penambahan)</th>
                            <th class="px-4 py-3 font-semibold text-right">Kredit (Pelunasan Kas)</th>
                            <th class="px-4 py-3 font-semibold text-right">Saldo Piutang</th>
                            <th class="px-4 py-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                        @foreach($mutasiIklan as $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 text-slate-600">{{ $m->tanggal }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-kp-blue-700">{{ $m->no_ref }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ $m->uraian }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">
                                {{ $m->debet_piutang > 0 ? 'Rp ' . number_format($m->debet_piutang, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">
                                {{ $m->kredit_bayar > 0 ? 'Rp ' . number_format($m->kredit_bayar, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-kp-blue-700">
                                Rp {{ number_format($m->saldo_akhir, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(str_contains($m->status, 'LUNAS'))
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        {{ $m->status }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-md bg-slate-100 text-slate-700 border border-slate-300">
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
    </div>
</x-app-layout>