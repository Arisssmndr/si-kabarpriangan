<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Umur Piutang Iklan (Aging Schedule)
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Klasifikasi penagihan piutang iklan koran per kategori tempo waktu (Standar Akuntansi Pers).
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
                    <span>Cetak Aging Schedule</span>
                </button>
            </div>
        </div>

        <!-- Ringkasan Aging Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Outstanding Piutang</span>
                <span class="text-xl font-bold text-kp-blue-600 block mt-2 font-tabular">Rp {{ number_format($totalPiutangKeseluruhan, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 mt-1 block">Seluruh tagihan belum tertagih</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-emerald-200 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-700 block">0 - 30 Hari (Lancar)</span>
                <span class="text-xl font-bold text-slate-900 block mt-2 font-tabular">Rp {{ number_format($agingBuckets['0_30']['total'], 0, ',', '.') }}</span>
                <span class="text-xs font-medium text-emerald-600 mt-1 block">{{ count($agingBuckets['0_30']['items']) }} Faktur Belum Jatuh Tempo</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-amber-200 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-700 block">31 - 60 Hari (Perhatian)</span>
                <span class="text-xl font-bold text-slate-900 block mt-2 font-tabular">Rp {{ number_format($agingBuckets['31_60']['total'], 0, ',', '.') }}</span>
                <span class="text-xs font-medium text-amber-600 mt-1 block">{{ count($agingBuckets['31_60']['items']) }} Faktur Lewat Tempo</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-orange-200 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-orange-700 block">61 - 90 Hari (Diragukan)</span>
                <span class="text-xl font-bold text-slate-900 block mt-2 font-tabular">Rp {{ number_format($agingBuckets['61_90']['total'], 0, ',', '.') }}</span>
                <span class="text-xs font-medium text-orange-600 mt-1 block">{{ count($agingBuckets['61_90']['items']) }} Faktur SP-1 &amp; SP-2</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-rose-200 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-rose-700 block">&gt; 90 Hari (Macet/Kritis)</span>
                <span class="text-xl font-bold text-rose-600 block mt-2 font-tabular">Rp {{ number_format($agingBuckets['gt_90']['total'], 0, ',', '.') }}</span>
                <span class="text-xs font-medium text-rose-600 mt-1 block">{{ count($agingBuckets['gt_90']['items']) }} Faktur Somasi Hukum</span>
            </div>
        </div>

        <!-- Tabel per Bucket -->
        @foreach($agingBuckets as $key => $bucket)
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
                <div class="p-4 border-b border-slate-200 flex justify-between items-center {{ $key === 'gt_90' ? 'bg-rose-50/70' : ($key === '61_90' ? 'bg-orange-50/70' : ($key === '31_60' ? 'bg-amber-50/70' : 'bg-slate-50/70')) }}">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-tight">
                            {{ $bucket['label'] }}
                        </h2>
                        <span class="text-xs text-slate-500">Toleransi Kolektibilitas Standar HU. Kabar Priangan</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Subtotal Kategori</span>
                        <span class="text-base font-bold font-tabular {{ $key === 'gt_90' ? 'text-rose-600' : 'text-slate-900' }}">Rp {{ number_format($bucket['total'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-600 font-semibold bg-slate-50/40">
                                <th class="py-3 px-4">No. Faktur</th>
                                <th class="py-3 px-4">Tgl. Terbit</th>
                                <th class="py-3 px-4">Pelanggan / Pemasang</th>
                                <th class="py-3 px-4">Sales / AE</th>
                                <th class="py-3 px-4 text-center">Umur Piutang</th>
                                <th class="py-3 px-4 text-right">Nilai Faktur</th>
                                <th class="py-3 px-4 text-right">Sisa Piutang</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($bucket['items'] as $item)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3 px-4 font-mono font-semibold text-kp-blue-700 whitespace-nowrap">
                                        {{ $item['faktur'] }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 font-mono whitespace-nowrap">{{ $item['tanggal'] }}</td>
                                    <td class="py-3 px-4 font-medium text-slate-900 whitespace-nowrap">{{ $item['pelanggan'] }}</td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 border border-slate-200 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                            {{ $item['sales'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border border-slate-200 bg-slate-100 text-slate-700">
                                            {{ $item['umur_hari'] }} Hari
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-xs">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-medium text-slate-900 tabular-nums">{{ number_format($item['total_tagihan'], 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-xs">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-medium text-slate-900 tabular-nums">{{ number_format($item['piutang'], 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <a href="{{ route('transaksikoran.index', ['q' => $item['faktur']]) }}" class="px-2.5 py-1 text-xs font-medium border border-slate-200 rounded-lg bg-white hover:bg-slate-50 transition text-slate-700 shadow-2xs">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-6 text-center text-slate-400 text-xs">
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
