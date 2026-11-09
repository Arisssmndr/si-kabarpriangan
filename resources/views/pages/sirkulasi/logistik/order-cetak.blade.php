<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        SPK Order Cetak Percetakan
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Surat Perintah Kerja kuota oplah naik cetak harian ke pabrik percetakan.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button"
                        onclick="window.print()"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak SPK Percetakan</span>
                </button>
            </div>
        </div>

        <!-- 4 KPI Kuota Cetak Harian -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Oplah Wajib Cetak</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    {{ number_format($orderCetak->total_oplah_cetak, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block font-mono">{{ $orderCetak->no_spk }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Oplah Reguler Agen</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($orderCetak->oplah_reguler, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Agen besar &amp; sub-agen</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jatah Gratis</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($orderCetak->oplah_gratis, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Bukti iklan &amp; legal redaksi</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Cadangan Sirkulasi</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($orderCetak->oplah_cadangan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Antisipasi susulan &amp; rusak</span>
            </div>
        </div>

        <!-- Detail Dokumen SPK Cetak -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Dokumen Surat Perintah Kerja (SPK) Naik Cetak</h2>
                    <p class="text-xs text-slate-500 font-mono">{{ $orderCetak->no_spk }} &bull; {{ $orderCetak->edisi_terbit }}</p>
                </div>
                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                    {{ $orderCetak->status }}
                </span>
            </div>

            <div class="p-5 border-b border-slate-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-xs text-slate-500 block">Pabrik Percetakan</span>
                    <strong class="text-slate-900 font-semibold mt-0.5 block">{{ $orderCetak->pabrik_percetakan }}</strong>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Edisi Koran Terbit</span>
                    <strong class="text-slate-900 font-semibold mt-0.5 block">{{ $orderCetak->edisi_terbit }}</strong>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Jadwal Jam Cetak</span>
                    <strong class="text-slate-900 font-mono font-semibold mt-0.5 block">{{ $orderCetak->jam_cetak_mulai }} s/d {{ $orderCetak->jam_cetak_selesai }}</strong>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Spesifikasi Kertas</span>
                    <strong class="text-slate-900 font-semibold mt-0.5 block">{{ $orderCetak->tipe_kertas }} &bull; {{ $orderCetak->jumlah_halaman }} Halaman</strong>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Komponen Alokasi Oplah Naik Cetak</th>
                            <th class="py-3 px-3.5">Keterangan Distribusi</th>
                            <th class="py-3 px-3.5 text-right w-44">Jumlah Kuota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">1</td>
                            <td class="py-3.5 px-3.5 font-semibold text-slate-900">Oplah Reguler Agen &amp; Sub-Agen</td>
                            <td class="py-3.5 px-3.5 text-slate-600">Distribusi komersial seluruh rute armada subuh</td>
                            <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($orderCetak->oplah_reguler, 0, ',', '.') }} eks</td>
                        </tr>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">2</td>
                            <td class="py-3.5 px-3.5 font-semibold text-slate-900">Jatah Gratis (Cuma-cuma)</td>
                            <td class="py-3.5 px-3.5 text-slate-600">Bukti muat pemasang iklan, arsip redaksi, dan instansi mitra</td>
                            <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">{{ number_format($orderCetak->oplah_gratis, 0, ',', '.') }} eks</td>
                        </tr>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">3</td>
                            <td class="py-3.5 px-3.5 font-semibold text-slate-900">Cadangan Sirkulasi &amp; Kerusakan</td>
                            <td class="py-3.5 px-3.5 text-slate-600">Stok pengganti cetakan cacat dan permintaan susulan</td>
                            <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">{{ number_format($orderCetak->oplah_cadangan, 0, ',', '.') }} eks</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900">
                        <tr>
                            <td colspan="3" class="py-3 px-3.5 text-right font-sans text-xs uppercase tracking-wider text-slate-600">Total Oplah Wajib Cetak:</td>
                            <td class="py-3 px-3.5 text-right font-bold text-kp-blue-700 tabular-nums">{{ number_format($orderCetak->total_oplah_cetak, 0, ',', '.') }} eks</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
