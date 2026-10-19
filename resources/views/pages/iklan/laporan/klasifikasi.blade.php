<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Laporan Klasifikasi Iklan (Legacy IKL.EXE)
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Distribusi omzet dan volume berdasarkan klasifikasi produk (Baris, Kolom, Display, Advertorial, Bonus).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('laporan.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    &larr; <span>Kembali ke Laporan</span>
                </a>
                <button onclick="window.print()" class="px-3.5 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Klasifikasi</span>
                </button>
            </div>
        </div>

        <!-- Filter Periode Bulan & Tahun -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('laporan.klasifikasi') }}" class="flex flex-wrap gap-3 items-end">
                <div class="w-48">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Bulan:</label>
                    <select name="bulan" class="w-full h-10 px-3.5 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="w-36">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun:</label>
                    <input type="number" name="tahun" value="{{ $tahun }}" min="2020" max="2035"
                           class="w-full h-10 px-3.5 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-800 font-mono focus:outline-none focus:border-kp-blue-600 transition">
                </div>

                <div>
                    <button type="submit" class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-lg shadow-2xs transition">
                        Tampilkan Klasifikasi
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Klasifikasi Produk -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 bg-slate-50/70 border-b border-slate-200">
                <h2 class="text-sm font-bold text-slate-900">Klasifikasi Pemuatan Iklan — Periode {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50/50 border-b border-slate-200 text-slate-600 font-semibold">
                        <tr>
                            <th class="py-3 px-4">Klasifikasi Iklan</th>
                            <th class="py-3 px-4 text-center">Jumlah Pemuatan</th>
                            <th class="py-3 px-4 text-right">Total Nilai Tagihan</th>
                            <th class="py-3 px-4 text-right">Porsi Kontribusi (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rekapKlasifikasi as $klas)
                            @php
                                $persen = ($totalSemua > 0) ? round(($klas['nominal'] / $totalSemua) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3 px-4 font-semibold text-slate-900">
                                    {{ $klas['nama'] }}
                                </td>
                                <td class="py-3 px-4 text-center font-tabular">{{ $klas['count'] }} Order</td>
                                <td class="py-3 px-4 text-right font-bold font-tabular text-slate-900">
                                    Rp {{ number_format($klas['nominal'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold font-tabular text-kp-blue-600">
                                    {{ $persen }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50/80 border-t border-slate-200 font-bold text-xs">
                        <tr>
                            <td class="py-3 px-4 text-slate-900">TOTAL KESELURUHAN</td>
                            <td class="py-3 px-4 text-center font-tabular">{{ array_sum(array_column($rekapKlasifikasi, 'count')) }} Order</td>
                            <td class="py-3 px-4 text-right font-tabular text-slate-900">Rp {{ number_format($totalSemua, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-tabular text-kp-blue-600">100.0%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
