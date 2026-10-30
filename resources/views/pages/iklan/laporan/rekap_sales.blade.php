<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Rekap Perolehan Sales &amp; AE
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl">
                        Perolehan omzet iklan koran per sales struktural, koresponden, dan loket.
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
                    <span>Cetak Rekap</span>
                </button>
            </div>
        </div>

        <!-- Filter Periode Bulan & Sales AE -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('laporan.rekap-sales') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Bulan:</label>
                    <select name="bulan" class="w-full h-10 px-3.5 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tahun:</label>
                    <input type="number" name="tahun" value="{{ $tahun }}" min="2020" max="2035"
                           class="w-full h-10 px-3.5 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-800 font-mono focus:outline-none focus:border-kp-blue-600 transition">
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Filter Sales / AE:</label>
                    <select name="sales_ae_id" class="w-full h-10 px-3.5 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition">
                        <option value="all">Semua Sales / AE</option>
                        @foreach($salesAes as $ae)
                            <option value="{{ $ae->id }}" {{ $salesAeId == $ae->id ? 'selected' : '' }}>
                                [{{ $ae->kode_ae }}] {{ $ae->nama_ae }} ({{ $ae->jenis_kode }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="w-full h-10 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-sm rounded-lg shadow-2xs transition cursor-pointer">
                        Terapkan
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Ringkasan per Sales AE -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50/50 border-b border-slate-200 text-slate-600 font-semibold">
                        <tr>
                            <th class="py-3 px-3.5 whitespace-nowrap">Kode AE</th>
                            <th class="py-3 px-3.5 whitespace-nowrap">Nama Sales / AE</th>
                            <th class="py-3 px-3.5 whitespace-nowrap">Jenis</th>
                            <th class="py-3 px-3.5 text-center whitespace-nowrap">Total Order</th>
                            <th class="py-3 px-3.5 text-right whitespace-nowrap">Total Omzet Tagihan</th>
                            <th class="py-3 px-3.5 text-right whitespace-nowrap">Terbayar Kasir</th>
                            <th class="py-3 px-3.5 text-right whitespace-nowrap">Sisa Piutang</th>
                            <th class="py-3 px-3.5 text-right whitespace-nowrap">Estimasi Insentif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @foreach($rekapPerAe as $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3 px-3.5 font-mono font-semibold text-kp-blue-700">{{ $item['ae']->kode_ae }}</td>
                                <td class="py-3 px-3.5 font-medium text-slate-900">{{ $item['ae']->nama_ae }}</td>
                                <td class="py-3 px-3.5 text-slate-600">{{ $item['ae']->jenis_kode }}</td>
                                <td class="py-3 px-3.5 text-center font-tabular">{{ $item['count'] }}</td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-medium text-slate-900 tabular-nums">{{ number_format($item['total_tagihan'], 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-medium text-slate-900 tabular-nums">{{ number_format($item['total_bayar'], 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-medium text-slate-900 tabular-nums">{{ number_format($item['total_piutang'], 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-medium text-slate-900 tabular-nums">{{ number_format($item['total_insentif'], 0, ',', '.') }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50/80 border-t border-slate-200 text-sm">
                        <tr class="font-semibold text-slate-900">
                            <td colspan="3" class="py-3 px-3.5">TOTAL KESELURUHAN</td>
                            <td class="py-3 px-3.5 text-center font-tabular">{{ $grandTotal['count'] }}</td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                    <span class="tabular-nums">{{ number_format($grandTotal['total_tagihan'], 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                    <span class="tabular-nums">{{ number_format($grandTotal['total_bayar'], 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                    <span class="tabular-nums">{{ number_format($grandTotal['total_piutang'], 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                    <span class="tabular-nums">{{ number_format($grandTotal['total_insentif'], 0, ',', '.') }}</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
