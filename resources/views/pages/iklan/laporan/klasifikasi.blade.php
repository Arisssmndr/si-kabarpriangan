<x-app-layout>
    <div class="space-y-5">

        <!-- Navigasi Terpadu Pusat Laporan Iklan -->
        @include('pages.iklan.laporan._nav_tabs')

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight truncate">
                        Laporan Klasifikasi Produk Iklan
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl">
                        Distribusi omzet, volume mili, kas masuk, dan piutang berdasarkan klasifikasi produk iklan ({{ $periodeLabel }}).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('laporan.klasifikasi.cetak', request()->query()) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Klasifikasi (Continuous Form)</span>
                </a>
            </div>
        </div>

        <!-- Filter Periode Fleksibel (Harian, Mingguan, Bulanan, Tahunan, Kustom) -->
        @include('pages.iklan.laporan._period_filter', [
            'actionRoute'    => route('laporan.klasifikasi'),
            'submitColClass' => 'sm:col-span-3',
        ])

        <!-- Tabel Klasifikasi Produk -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 bg-slate-50/70 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-tight">Klasifikasi Pemuatan Iklan HU. Priangan</h2>
                    <p class="text-xs text-slate-500">Periode: {{ $hariTanggal }}</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-lg font-mono">
                    Total Order: {{ number_format($grandTotal['count'] ?? 0, 0, ',', '.') }} Iklan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-700 border-collapse">
                    <thead class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50/80 border-b border-slate-200 font-semibold whitespace-nowrap">
                        <tr>
                            <th class="py-3.5 px-4 text-center w-12">No</th>
                            <th class="py-3.5 px-4">Klasifikasi Iklan</th>
                            <th class="py-3.5 px-3 text-right">Order</th>
                            <th class="py-3.5 px-3 text-right">Baris</th>
                            <th class="py-3.5 px-3 text-right">Total Mili</th>
                            <th class="py-3.5 px-3 text-right">Brutto</th>
                            <th class="py-3.5 px-3 text-right">Reduksi</th>
                            <th class="py-3.5 px-3 text-right">Netto</th>
                            <th class="py-3.5 px-3 text-right">PPn</th>
                            <th class="py-3.5 px-3 text-right">Jumlah Tagihan</th>
                            <th class="py-3.5 px-3 text-right">Kas Masuk</th>
                            <th class="py-3.5 px-3 text-right">Piutang</th>
                            <th class="py-3.5 px-4 text-right">% Porsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 font-mono">
                        @foreach($rekapKlasifikasi as $index => $klas)
                            @php
                                $persen = ($totalSemua > 0) ? round(($klas['nominal'] / $totalSemua) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 text-center text-slate-400 font-normal">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="py-3.5 px-4 font-sans font-semibold text-slate-900">
                                    {{ $klas['nama'] }}
                                </td>
                                <td class="py-3.5 px-3 text-right font-normal text-slate-600">{{ number_format($klas['count'], 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-normal text-slate-600">{{ number_format($klas['vol_baris'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-normal text-slate-700">{{ number_format($klas['total_mili'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-normal text-slate-700">{{ number_format($klas['brutto'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-normal text-slate-500">{{ number_format($klas['reduksi'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-normal text-slate-700">{{ number_format($klas['netto'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-normal text-slate-600">{{ number_format($klas['ppn'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-bold text-slate-900">Rp {{ number_format($klas['nominal'], 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-normal text-slate-600">Rp {{ number_format($klas['bayar'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-normal text-slate-700">Rp {{ number_format($klas['piutang'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-4 text-right font-normal text-kp-blue-700">{{ number_format($persen, 1, ',', '.') }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50/90 border-t-2 border-slate-200 text-sm font-mono font-bold text-slate-900 whitespace-nowrap">
                        <tr>
                            <td colspan="2" class="py-3.5 px-4 font-sans">Jumlah...</td>
                            <td class="py-3.5 px-3 text-right">{{ number_format($grandTotal['count'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3 text-right">{{ number_format($grandTotal['vol_baris'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3 text-right">{{ number_format($grandTotal['total_mili'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3 text-right">{{ number_format($grandTotal['brutto'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3 text-right text-slate-700">{{ number_format($grandTotal['reduksi'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3 text-right">{{ number_format($grandTotal['netto'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3 text-right">{{ number_format($grandTotal['ppn'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3 text-right text-kp-blue-700">Rp {{ number_format($totalSemua, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3 text-right">Rp {{ number_format($grandTotal['bayar'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3 text-right">Rp {{ number_format($grandTotal['piutang'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right text-kp-blue-700">100,0%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
