<x-app-layout>
    <div class="space-y-6">

        <!-- Header Dashboard Divisi Kasir -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Dashboard Divisi Kasir
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-teal-50 text-teal-700 border border-teal-200">
                            Kasir
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Meja kasir, penerimaan setoran harian, dan rekonsiliasi kas masuk multi-kanal
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 px-3.5 py-2 rounded-lg border border-slate-200">
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                <span>Shift Kasir Aktif &bull; {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</span>
            </div>
        </div>

        <!-- 4 KPI Cards Kasir -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card 1: Penerimaan Hari Ini -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Penerimaan Kasir Hari Ini</span>
                    <span class="w-7 h-7 rounded-lg bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-teal-700">
                    Rp {{ number_format($bayarHariIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Tunai & transfer tercatat
                </div>
            </div>

            <!-- Card 2: Jumlah Transaksi Hari Ini -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Transaksi Hari Ini</span>
                    <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-slate-900">
                    {{ $txHariIni }} <span class="text-xs font-normal text-slate-500">Struk / Kwitansi</span>
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Total transaksi terproses
                </div>
            </div>

            <!-- Card 3: Akumulasi Bulan Ini -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Penerimaan Bulan Ini</span>
                    <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-slate-900">
                    Rp {{ number_format($totalPenerimaanBulan, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Periode {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                </div>
            </div>

            <!-- Card 4: Status Shift Kasir -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Status Meja Kasir</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-emerald-600">
                    Shift Aktif
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Kasir: {{ Auth::user()->name }}
                </div>
            </div>

        </div>

        <!-- Tabel Transaksi Pembayaran Kasir Terbaru -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                    Transaksi Pembayaran Terkini
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">Rekap Real-Time</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-semibold">No. Faktur</th>
                            <th class="px-5 py-3 font-semibold">Media Transaksi</th>
                            <th class="px-5 py-3 font-semibold">Nama Pemasang / Klien</th>
                            <th class="px-5 py-3 font-semibold text-right">Jumlah Dibayar</th>
                            <th class="px-5 py-3 font-semibold text-right">Sisa Piutang</th>
                            <th class="px-5 py-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentKasir as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3 font-mono font-bold text-slate-900">{{ $item->faktur }}</td>
                            <td class="px-5 py-3 text-slate-600 font-medium">{{ $item->media }}</td>
                            <td class="px-5 py-3 font-semibold text-slate-800">{{ $item->customer }}</td>
                            <td class="px-5 py-3 text-right font-extrabold text-emerald-600">Rp {{ number_format($item->bayar, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-semibold {{ $item->sisa > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                Rp {{ number_format($item->sisa, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($item->sisa <= 0)
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Lunas
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-50 text-amber-700 border border-amber-200">
                                        Uang Muka (DP)
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-6 text-center text-slate-400">Belum ada transaksi pembayaran hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Banner Roadmap Menu Operasional Divisi Kasir -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-slate-900">Menu Operasional Divisi Kasir</h3>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-50 text-amber-700 border border-amber-200">
                            Dalam Pengembangan Tahap Berikutnya
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 max-w-3xl leading-relaxed">
                        Fitur POS (Point of Sales) meja kasir terpadu sedang disiapkan untuk mempercepat proses pelayanan kasir:
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4">
                        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
                            <span class="text-xs font-bold text-slate-800 block">1. POS Kasir & Cetak Struk</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">Penerimaan pembayaran tunai/QRIS/kartu dan pencetakan struk termal resmi.</span>
                        </div>
                        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
                            <span class="text-xs font-bold text-slate-800 block">2. Kwitansi Resmi Perusahaan</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">Format cetak kwitansi tanda terima standar Harian Umum Kabar Priangan.</span>
                        </div>
                        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
                            <span class="text-xs font-bold text-slate-800 block">3. Rekap Tutup Shift & Kas Harian</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">Penyusunan berita acara serah terima uang fisik kasir kepada divisi keuangan.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
