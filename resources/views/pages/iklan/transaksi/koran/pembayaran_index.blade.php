<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman -->
        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Riwayat Pembayaran &amp; Kasir
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl">
                        Pencatatan setoran kasir dan riwayat pelunasan faktur iklan cetak.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('transaksikoran.index') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    &larr; <span>Transaksi Koran</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter & Search -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('pembayaran-iklan.index') }}" class="flex gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari No. Kwitansi, No. Faktur, atau nama penyetor..."
                       class="flex-1 h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                <button type="submit" class="h-11 px-5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl transition cursor-pointer shadow-xs">
                    Cari
                </button>
            </form>
        </div>

        <!-- Tabel Pembayaran -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs">
                        <tr>
                            <th class="py-3 px-4 whitespace-nowrap">No. Kwitansi</th>
                            <th class="py-3 px-4 whitespace-nowrap">Tanggal</th>
                            <th class="py-3 px-4 whitespace-nowrap">No. Faktur</th>
                            <th class="py-3 px-4 whitespace-nowrap">Nama Penyetor</th>
                            <th class="py-3 px-4 whitespace-nowrap">Metode Bayar</th>
                            <th class="py-3 px-4 text-right whitespace-nowrap">Nominal Bayar</th>
                            <th class="py-3 px-4 whitespace-nowrap">Petugas Kasir</th>
                            <th class="py-3 px-4 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($pembayaran as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-kp-blue-700 text-base whitespace-nowrap">
                                    {{ $item->nokwitansi }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-medium text-slate-900 whitespace-nowrap">
                                    {{ $item->nofaktur }}
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $item->nama_penyetor }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ str_replace('_', ' ', $item->metode_bayar) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[140px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-bold text-slate-900 tabular-nums">{{ number_format($item->nominal_bayar, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 text-xs whitespace-nowrap">
                                    {{ $item->kasir->name ?? 'Kasir' }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <a href="{{ route('pembayaran-iklan.kwitansi', $item->id) }}" target="_blank"
                                       class="px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition cursor-pointer shadow-2xs">
                                        Cetak Kwitansi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    Belum ada transaksi pembayaran kasir yang dicatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pembayaran->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    {{ $pembayaran->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
