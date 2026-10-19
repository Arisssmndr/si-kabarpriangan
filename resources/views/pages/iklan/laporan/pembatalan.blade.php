<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-black tracking-tight">
                        Laporan Pembatalan Iklan (Audit Trail)
                    </h1>
                    <p class="text-xs text-black mt-0.5">
                        Rekam jejak transaksi yang dibatalkan, alasan pembatalan, dan petugas yang membatalkan.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('laporan.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-black text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    &larr; <span>Kembali ke Laporan</span>
                </a>
                <button onclick="window.print()" class="px-3.5 py-2 bg-black hover:bg-neutral-800 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan Batal</span>
                </button>
            </div>
        </div>

        <!-- Filter Periode Bulan & Tahun -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            <form method="GET" action="{{ route('laporan.pembatalan') }}" class="flex flex-wrap gap-3 items-end">
                <div class="w-48">
                    <label class="block text-xs font-bold text-black mb-1">Bulan:</label>
                    <select name="bulan" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-black">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="w-36">
                    <label class="block text-xs font-bold text-black mb-1">Tahun:</label>
                    <input type="number" name="tahun" value="{{ $tahun }}" min="2020" max="2035"
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-black font-mono">
                </div>

                <div>
                    <button type="submit" class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Tampilkan Data
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Pembatalan -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <h2 class="text-sm font-bold text-black">Daftar Order Dibatalkan — Periode {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}</h2>
                <span class="text-xs font-bold text-rose-800">{{ $pembatalanList->count() }} Order Dibatalkan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-black">
                    <thead class="bg-slate-100 border-b border-slate-200 text-black">
                        <tr>
                            <th class="py-3 px-3.5 font-bold">No Faktur</th>
                            <th class="py-3 px-3.5 font-bold">Tgl Order</th>
                            <th class="py-3 px-3.5 font-bold">Pengiklan / Klien</th>
                            <th class="py-3 px-3.5 font-bold">Kategori &amp; Ukuran</th>
                            <th class="py-3 px-3.5 text-right font-bold">Nilai Tagihan</th>
                            <th class="py-3 px-3.5 font-bold">Alasan Pembatalan</th>
                            <th class="py-3 px-3.5 font-bold">Dibatalkan Oleh</th>
                            <th class="py-3 px-3.5 font-bold">Waktu Batal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pembatalanList as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-3.5 font-bold font-mono text-rose-700">
                                    <a href="{{ route('transaksikoran.show', $item->id) }}" class="underline">
                                        {{ $item->nofakturkoran }}
                                    </a>
                                </td>
                                <td class="py-3 px-3.5 font-bold font-mono text-black">
                                    {{ $item->tanggal_transaksikoran->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-3.5 font-bold text-black">
                                    {{ $item->nama_pemasangkoran }}
                                </td>
                                <td class="py-3 px-3.5 font-semibold text-black">
                                    {{ $item->kategori_form }} ({{ $item->ukuran_iklan ?: ($item->jumlah_baris . ' Baris') }})
                                </td>
                                <td class="py-3 px-3.5 text-right font-bold font-mono text-black">
                                    Rp {{ number_format($item->totaltagihan_transaksikoran, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 max-w-xs font-bold text-rose-800">
                                    {{ $item->alasan_batal ?: 'Tanpa keterangan tertulis.' }}
                                </td>
                                <td class="py-3 px-3.5 font-bold text-black">
                                    {{ $item->dibatalkanOleh->name ?? 'Petugas Iklan' }}
                                </td>
                                <td class="py-3 px-3.5 font-bold text-black font-mono">
                                    {{ $item->dibatalkan_at ? $item->dibatalkan_at->format('d/m/Y H:i') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-xs font-bold text-black">
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
