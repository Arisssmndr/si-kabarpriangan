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
                        Laporan Pembatalan Iklan (Audit Trail)
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl">
                        Audit log pembatalan transaksi, alasan pembatalan, dan otorisasi petugas ({{ $periodeLabel }}).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('laporan.pembatalan.cetak', request()->query()) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan Batal (Continuous Form)</span>
                </a>
            </div>
        </div>

        <!-- Filter Periode Fleksibel (Harian, Mingguan, Bulanan, Tahunan, Kustom) -->
        @include('pages.iklan.laporan._period_filter', [
            'actionRoute'    => route('laporan.pembatalan'),
            'submitColClass' => 'sm:col-span-3',
        ])

        <!-- Tabel Pembatalan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 bg-slate-50/70 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-tight">Daftar Audit Pembatalan Order Iklan</h2>
                    <p class="text-xs text-slate-500">Periode: {{ $hariTanggal }}</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-lg font-mono">
                    {{ count($pembatalanList) }} Faktur Batal (Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }})
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-700 border-collapse">
                    <thead class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50/80 border-b border-slate-200 font-semibold whitespace-nowrap">
                        <tr>
                            <th class="py-3.5 px-4 text-center w-12">No</th>
                            <th class="py-3.5 px-4">No. Faktur</th>
                            <th class="py-3.5 px-4">Tgl. Order</th>
                            <th class="py-3.5 px-4">Pemasang</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4">Ukuran</th>
                            <th class="py-3.5 px-4 text-right">Nilai Tagihan</th>
                            <th class="py-3.5 px-4">Alasan Pembatalan</th>
                            <th class="py-3.5 px-4">Dibatalkan Oleh</th>
                            <th class="py-3.5 px-4">Waktu Batal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pembatalanList as $index => $item)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 text-center text-slate-400 font-normal">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-kp-blue-700 whitespace-nowrap">
                                    <a href="{{ route('transaksikoran.show', $item->id) }}" class="hover:underline">
                                        {{ $item->nofakturkoran }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-normal whitespace-nowrap">
                                    {{ $item->tanggal_transaksikoran->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $item->nama_pemasangkoran }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-normal whitespace-nowrap">
                                    {{ $item->kategori_form ?: '-' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-600 font-normal whitespace-nowrap">
                                    {{ $item->ukuran_iklan ?: ($item->jumlah_baris ? $item->jumlah_baris . ' Baris' : '-') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format($item->totaltagihan_transaksikoran, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 max-w-xs text-slate-600 font-normal">
                                    {{ $item->alasan_batal ?: 'Tanpa keterangan tertulis.' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap font-normal">
                                    {{ $item->dibatalkanOleh->name ?? 'Petugas Iklan' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 font-mono font-normal whitespace-nowrap">
                                    {{ $item->dibatalkan_at ? $item->dibatalkan_at->format('d/m/Y H:i') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-500 text-sm">
                                    Tidak ada catatan pembatalan order pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
