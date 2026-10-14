<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Riwayat Kwitansi & Kasir Iklan
                    </h1>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Log penerimaan pembayaran tunai, transfer, dan cicilan pelunasan piutang iklan
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('transaksikoran.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    &larr; <span>Kembali ke Transaksi Koran</span>
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
                       class="flex-1 h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                <button type="submit" class="h-10 px-5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition">
                    Cari
                </button>
            </form>
        </div>

        <!-- Tabel Pembayaran -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">No. Kwitansi</th>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">No. Faktur</th>
                            <th class="py-3 px-4">Nama Penyetor</th>
                            <th class="py-3 px-4">Metode Bayar</th>
                            <th class="py-3 px-4 text-right">Nominal Bayar</th>
                            <th class="py-3 px-4">Kasir / Staf</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        @forelse($pembayaran as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-kp-blue-700">
                                    {{ $item->nokwitansi }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">
                                    {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-medium text-slate-900">
                                    {{ $item->nofaktur }}
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-900">
                                    {{ $item->nama_penyetor }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ str_replace('_', ' ', $item->metode_bayar) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-900 font-tabular">
                                    Rp {{ number_format($item->nominal_bayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                    {{ $item->kasir->name ?? 'Kasir' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('pembayaran-iklan.kwitansi', $item->id) }}" target="_blank"
                                       class="px-2.5 py-1 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-[11px] font-semibold rounded transition cursor-pointer">
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
