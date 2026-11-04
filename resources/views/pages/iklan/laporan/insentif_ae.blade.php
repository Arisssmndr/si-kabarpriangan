<x-app-layout>
    <div class="space-y-5">

        <!-- Navigasi Terpadu Pusat Laporan Iklan -->
        @include('pages.iklan.laporan._nav_tabs')

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Laporan Insentif &amp; Komisi Sales AE
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Kalkulasi hak insentif AE struktural dan koresponden untuk payroll ({{ $periodeLabel }}).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('laporan.insentif-ae.cetak', request()->query()) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Slip Insentif</span>
                </a>
            </div>
        </div>

        <!-- Filter Periode Fleksibel (Selaras /transaksipriangan) -->
        @include('pages.iklan.laporan._period_filter', [
            'actionRoute'    => route('laporan.insentif-ae'),
            'submitColClass' => 'sm:col-span-3',
        ])

        <!-- Tabel Perhitungan Insentif (Selaras /transaksipriangan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Rekapitulasi Hak Insentif &amp; Komisi Sales AE</h2>
                    <p class="text-xs text-slate-500 font-normal">Periode: {{ $hariTanggal }}</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    Total Hak: Rp {{ number_format($grandTotal['total_hak_sales'] ?? 0, 0, ',', '.') }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Kode AE</th>
                            <th class="py-3 px-3.5">Nama Sales / AE</th>
                            <th class="py-3 px-3.5">Klasifikasi</th>
                            <th class="py-3 px-3.5 text-center">Order</th>
                            <th class="py-3 px-3.5 text-right">Total Mili</th>
                            <th class="py-3 px-3.5 text-right">Omzet Tagihan</th>
                            <th class="py-3 px-3.5 text-right">Omzet Lunas</th>
                            <th class="py-3 px-3.5 text-right">Omzet Piutang</th>
                            <th class="py-3 px-3.5 text-right">Hak Insentif</th>
                            <th class="py-3 px-3.5 text-right">Hak Komisi</th>
                            <th class="py-3 px-3.5 text-right">Total Diterima</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($dataInsentif as $idx => $row)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $loop->iteration }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">{{ $row['ae']->kode_ae }}</td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $row['ae']->nama_ae }}</td>
                                <td class="py-3.5 px-3.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $row['ae']->jenis_kode }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono font-normal text-slate-600">{{ $row['total_order'] }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-normal text-slate-700">{{ number_format($row['total_mili'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-normal text-slate-700">Rp {{ number_format($row['omzet_total'], 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-normal text-slate-700">Rp {{ number_format($row['omzet_lunas'], 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-normal text-slate-600">Rp {{ number_format($row['omzet_piutang'], 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-normal text-slate-700">Rp {{ number_format($row['insentif_nominal'], 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-normal text-slate-700">Rp {{ number_format($row['komisi_nominal'], 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900">Rp {{ number_format($row['total_hak_sales'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada data perolehan sales pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($dataInsentif) > 0)
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                            <tr>
                                <td colspan="4" class="py-3 px-3.5 font-sans">Jumlah...</td>
                                <td class="py-3 px-3.5 text-center">{{ number_format($grandTotal['total_order'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right">{{ number_format($grandTotal['total_mili'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right">Rp {{ number_format($grandTotal['omzet_total'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right">Rp {{ number_format($grandTotal['omzet_lunas'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right">Rp {{ number_format($grandTotal['omzet_piutang'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right">Rp {{ number_format($grandTotal['insentif_nominal'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right">Rp {{ number_format($grandTotal['komisi_nominal'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right text-kp-blue-700 font-bold">Rp {{ number_format($grandTotal['total_hak_sales'] ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
