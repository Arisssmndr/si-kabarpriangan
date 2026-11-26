<x-app-layout>
    <div class="space-y-5">

        <!-- 1. Header Halaman (Palet 60-30-10 & Action Cetak) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Laporan Perubahan Ekuitas
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Modal Pemilik
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Mutasi modal saham, akumulasi laba ditahan, dan ekuitas akhir pers.
                    </p>
                </div>
            </div>

            <!-- Tombol Cetak Dokumen Resmi -->
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('accounting.laporan.ekuitas.cetak', ['periode' => $periode]) }}" target="_blank" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Dokumen Resmi</span>
                </a>
            </div>
        </div>

        <!-- 2. KPI Summary Cards (Report Info) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Modal Awal Periode</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($modalAwal, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Modal disetor awal</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Laba Bersih Berjalan</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($labaBersih, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Hasil usaha bersih</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Distribusi Dividen / Prive</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($priveDividen, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Penarikan pemilik</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Ekuitas Akhir</span>
                <div class="text-xl font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($modalAkhir, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Posisi ekuitas bersih</span>
            </div>
        </div>

        <!-- 3. Filter Periode Mandiri -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <form method="GET" action="{{ route('accounting.laporan.ekuitas') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Periode Laporan</label>
                    <input type="month" 
                           name="periode" 
                           value="{{ $periode }}" 
                           class="h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Terapkan
                    </button>
                    <a href="{{ route('accounting.laporan.ekuitas') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 4. Pure Tabel Perubahan Modal -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5">Komponen Ekuitas</th>
                            <th class="py-3 px-3.5 text-right w-44">Modal Saham</th>
                            <th class="py-3 px-3.5 text-right w-44">Laba Ditahan</th>
                            <th class="py-3 px-3.5 text-right w-44">Total Ekuitas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-3.5 font-semibold text-slate-900">Saldo Awal Periode</td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">Rp {{ number_format($modalAwal, 0, ',', '.') }}</td>
                            <td class="py-3 px-3.5 text-right font-mono text-slate-500">Rp 0</td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">Rp {{ number_format($modalAwal, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-3.5 text-slate-700">Laba Bersih Berjalan</td>
                            <td class="py-3 px-3.5 text-right font-mono text-slate-400">-</td>
                            <td class="py-3 px-3.5 text-right font-mono text-slate-900 font-bold">+ Rp {{ number_format($labaBersih, 0, ',', '.') }}</td>
                            <td class="py-3 px-3.5 text-right font-mono text-slate-900 font-bold">+ Rp {{ number_format($labaBersih, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-3.5 text-slate-700">Distribusi Dividen / Penarikan Pemilik</td>
                            <td class="py-3 px-3.5 text-right font-mono text-slate-400">-</td>
                            <td class="py-3 px-3.5 text-right font-mono text-slate-900 font-bold">- Rp {{ number_format($priveDividen, 0, ',', '.') }}</td>
                            <td class="py-3 px-3.5 text-right font-mono text-slate-900 font-bold">- Rp {{ number_format($priveDividen, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-kp-blue-50/70 border-t-2 border-slate-300 font-bold text-sm">
                            <td class="py-3 px-3.5 uppercase text-xs">Saldo Akhir Ekuitas</td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">Rp {{ number_format($modalAwal, 0, ',', '.') }}</td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">Rp {{ number_format($labaBersih - $priveDividen, 0, ',', '.') }}</td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-kp-blue-700">Rp {{ number_format($modalAkhir, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>