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
                        Umur Piutang Iklan (Aging Schedule)
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl">
                        Klasifikasi penagihan piutang iklan lintas 3 media per kategori umur tempo waktu.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('laporan.aging-piutang.cetak', request()->query()) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Aging Piutang (Continuous Form)</span>
                </a>
            </div>
        </div>

        <!-- Ringkasan Aging Cards (60-30-10 Palette) -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Outstanding Piutang</span>
                <span class="text-lg xl:text-xl font-bold text-kp-blue-700 block mt-2 font-mono">Rp {{ number_format($totalPiutangKeseluruhan, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Seluruh tagihan belum tertagih</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-600 block">0 - 30 Hari (Lancar)</span>
                <span class="text-lg xl:text-xl font-bold text-slate-900 block mt-2 font-mono">Rp {{ number_format($agingBuckets['0_30']['total'], 0, ',', '.') }}</span>
                <span class="text-xs font-medium text-kp-blue-700 mt-1 block">{{ count($agingBuckets['0_30']['items']) }} Faktur Belum Jatuh Tempo</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-600 block">31 - 60 Hari (Perhatian)</span>
                <span class="text-lg xl:text-xl font-bold text-slate-900 block mt-2 font-mono">Rp {{ number_format($agingBuckets['31_60']['total'], 0, ',', '.') }}</span>
                <span class="text-xs font-medium text-slate-500 mt-1 block">{{ count($agingBuckets['31_60']['items']) }} Faktur Lewat Tempo</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-600 block">61 - 90 Hari (Diragukan)</span>
                <span class="text-lg xl:text-xl font-bold text-slate-900 block mt-2 font-mono">Rp {{ number_format($agingBuckets['61_90']['total'], 0, ',', '.') }}</span>
                <span class="text-xs font-medium text-slate-500 mt-1 block">{{ count($agingBuckets['61_90']['items']) }} Faktur SP-1 &amp; SP-2</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-300 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-700 block">&gt; 90 Hari (Macet/Kritis)</span>
                <span class="text-lg xl:text-xl font-bold text-slate-900 block mt-2 font-mono">Rp {{ number_format($agingBuckets['gt_90']['total'], 0, ',', '.') }}</span>
                <span class="text-xs font-medium text-slate-600 mt-1 block">{{ count($agingBuckets['gt_90']['items']) }} Faktur Somasi Hukum</span>
            </div>
        </div>

        <!-- Tabel per Bucket -->
        @foreach($agingBuckets as $key => $bucket)
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
                <div class="px-5 py-3.5 border-b border-slate-200 flex justify-between items-center bg-slate-50/70">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-tight">
                            {{ $bucket['label'] }}
                        </h2>
                        <span class="text-xs text-slate-500">Toleransi Kolektibilitas Standar HU. Kabar Priangan</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Subtotal Kategori</span>
                        <span class="text-base font-bold font-mono text-kp-blue-700">Rp {{ number_format($bucket['total'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-700 border-collapse">
                        <thead class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50/80 border-b border-slate-200 font-semibold whitespace-nowrap">
                            <tr>
                                <th class="py-3.5 px-4 text-center w-12">No</th>
                                <th class="py-3.5 px-4">No. Faktur</th>
                                <th class="py-3.5 px-4">Saluran</th>
                                <th class="py-3.5 px-4">Tgl. Terbit</th>
                                <th class="py-3.5 px-4">Pemasang</th>
                                <th class="py-3.5 px-4">Sales</th>
                                <th class="py-3.5 px-4 text-center">Umur Piutang</th>
                                <th class="py-3.5 px-4 text-right">Nilai Faktur</th>
                                <th class="py-3.5 px-4 text-right">Terbayar</th>
                                <th class="py-3.5 px-4 text-right">Sisa Piutang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($bucket['items'] as $index => $item)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3.5 px-4 text-center text-slate-400 font-normal">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap font-mono font-bold text-kp-blue-700">
                                        {{ $item['faktur'] }}
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap font-normal">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $item['media'] ?? 'Koran' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 font-normal whitespace-nowrap">{{ $item['tanggal'] }}</td>
                                    <td class="py-3.5 px-4 font-semibold text-slate-900 whitespace-nowrap">{{ $item['pelanggan'] }}</td>
                                    <td class="py-3.5 px-4 font-normal text-slate-600 whitespace-nowrap">{{ $item['sales'] }}</td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium border border-slate-200 bg-slate-100 text-slate-700">
                                            {{ $item['umur_hari'] }} Hari
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-normal text-slate-700 whitespace-nowrap">
                                        Rp {{ number_format($item['total_tagihan'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-normal text-slate-600 whitespace-nowrap">
                                        Rp {{ number_format($item['terbayar'] ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                        Rp {{ number_format($item['piutang'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-8 text-center text-slate-500 text-sm">
                                        Tidak ada piutang tertahan pada kategori umur ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

    </div>
</x-app-layout>
