<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Laporan Klasifikasi Iklan
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl">
                        Distribusi omzet dan volume berdasarkan klasifikasi produk iklan koran.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('laporan.index') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    &larr; <span>Kembali ke Laporan</span>
                </a>
                <button onclick="window.print()" class="inline-flex items-center gap-1.5 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Klasifikasi</span>
                </button>
            </div>
        </div>

        <!-- Filter Periode Bulan & Tahun -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('laporan.klasifikasi') }}" class="flex flex-wrap gap-3 items-end">
                <div class="w-52">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bulan:</label>
                    <select name="bulan" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="w-40">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun:</label>
                    <input type="number" name="tahun" value="{{ $tahun }}" min="2020" max="2035"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 font-mono focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                </div>

                <div>
                    <button type="submit" class="h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-sm rounded-xl shadow-2xs transition cursor-pointer">
                        Tampilkan Klasifikasi
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Klasifikasi Produk -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 bg-slate-50/70 border-b border-slate-200">
                <h2 class="text-base font-bold text-slate-900">Klasifikasi Pemuatan Iklan — Periode {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50/50 border-b border-slate-200 text-slate-600 font-bold">
                        <tr>
                            <th class="py-3 px-4 whitespace-nowrap">Klasifikasi Iklan</th>
                            <th class="py-3 px-4 text-center whitespace-nowrap">Jumlah Pemuatan</th>
                            <th class="py-3 px-4 text-right whitespace-nowrap">Total Nilai Tagihan</th>
                            <th class="py-3 px-4 text-right whitespace-nowrap">Porsi Kontribusi (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @foreach($rekapKlasifikasi as $klas)
                            @php
                                $persen = ($totalSemua > 0) ? round(($klas['nominal'] / $totalSemua) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $klas['nama'] }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-tabular font-medium">{{ $klas['count'] }} Order</td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[140px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-bold text-slate-900 tabular-nums">{{ number_format($klas['nominal'], 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-right font-semibold font-tabular text-slate-800">
                                    {{ $persen }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50/80 border-t border-slate-200 text-sm">
                        <tr class="font-bold text-slate-900">
                            <td class="py-3.5 px-4">TOTAL KESELURUHAN</td>
                            <td class="py-3.5 px-4 text-center font-tabular">{{ array_sum(array_column($rekapKlasifikasi, 'count')) }} Order</td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex justify-between items-center gap-2 max-w-[140px] ml-auto font-mono text-sm">
                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                    <span class="tabular-nums font-bold">{{ number_format($totalSemua, 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-tabular text-slate-900">100.0%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
