<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Laporan Pembatalan Iklan (Audit Trail)
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl">
                        Audit log pembatalan transaksi, alasan pembatalan, dan otorisasi petugas.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('laporan.index') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer">
                    &larr; <span>Kembali ke Laporan</span>
                </a>
                <button onclick="window.print()" class="inline-flex items-center gap-1.5 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan Batal</span>
                </button>
            </div>
        </div>

        <!-- Filter Periode Bulan & Tahun -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('laporan.pembatalan') }}" class="flex flex-wrap gap-3 items-end">
                <div class="w-48">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Bulan:</label>
                    <select name="bulan" class="w-full h-10 px-3.5 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="w-36">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tahun:</label>
                    <input type="number" name="tahun" value="{{ $tahun }}" min="2020" max="2035"
                           class="w-full h-10 px-3.5 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-800 font-mono focus:outline-none focus:border-kp-blue-600 transition">
                </div>

                <div>
                    <button type="submit" class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-sm rounded-lg shadow-2xs transition cursor-pointer">
                        Tampilkan Data
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Pembatalan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50/50 border-b border-slate-200 text-slate-600 font-semibold">
                        <tr>
                            <th class="py-3 px-3.5 whitespace-nowrap">No Faktur</th>
                            <th class="py-3 px-3.5 whitespace-nowrap">Tgl Order</th>
                            <th class="py-3 px-3.5 whitespace-nowrap">Pengiklan / Klien</th>
                            <th class="py-3 px-3.5 whitespace-nowrap">Kategori &amp; Ukuran</th>
                            <th class="py-3 px-3.5 text-right whitespace-nowrap">Nilai Tagihan</th>
                            <th class="py-3 px-3.5 whitespace-nowrap">Alasan Pembatalan</th>
                            <th class="py-3 px-3.5 whitespace-nowrap">Dibatalkan Oleh</th>
                            <th class="py-3 px-3.5 whitespace-nowrap">Waktu Batal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pembatalanList as $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3 px-3.5 font-mono font-semibold text-kp-blue-700 whitespace-nowrap">
                                    <a href="{{ route('transaksikoran.show', $item->id) }}" class="hover:underline">
                                        {{ $item->nofakturkoran }}
                                    </a>
                                </td>
                                <td class="py-3 px-3.5 font-mono text-slate-600 whitespace-nowrap">
                                    {{ $item->tanggal_transaksikoran->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-3.5 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $item->nama_pemasangkoran }}
                                </td>
                                <td class="py-3 px-3.5 text-slate-700 whitespace-nowrap">
                                    {{ $item->kategori_form }} ({{ $item->ukuran_iklan ?: ($item->jumlah_baris . ' Baris') }})
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-medium text-slate-900 tabular-nums">{{ number_format($item->totaltagihan_transaksikoran, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 max-w-xs text-slate-600 text-sm">
                                    {{ $item->alasan_batal ?: 'Tanpa keterangan tertulis.' }}
                                </td>
                                <td class="py-3 px-3.5 text-slate-800 whitespace-nowrap font-medium">
                                    {{ $item->dibatalkanOleh->name ?? 'Petugas Iklan' }}
                                </td>
                                <td class="py-3 px-3.5 text-slate-600 font-mono text-xs">
                                    {{ $item->dibatalkan_at ? $item->dibatalkan_at->format('d/m/Y H:i') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-sm font-medium text-slate-400">
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
