<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-xl shrink-0">
                    💵
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            REKAP FAKTUR IKLAN TUNAI
                        </h1>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            Kas / Lunas Langsung
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Daftar faktur iklan yang dibayar lunas seketika (100% tunai/transfer kasir tanpa piutang berjalan)
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('keuangan.faktur.create', ['kategori' => 'iklan', 'tipe_pembayaran' => 'lunas_langsung']) }}" 
                   class="h-9 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Terbitkan Faktur Tunai</span>
                </a>
            </div>
        </div>


        <!-- KPI Ringkas Faktur Tunai -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-emerald-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 font-mono">Total Penerimaan Kas Tunai</span>
                <div class="mt-2 text-3xl font-black text-emerald-600 tracking-tight font-mono">
                    Rp {{ number_format($totalNominalTunai, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-emerald-800 font-semibold flex items-center gap-1">
                    <span>✓ Dana tercatat otomatis di Buku Kas</span>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono">Jumlah Faktur Tunai</span>
                <div class="mt-2 text-3xl font-black text-slate-900 tracking-tight">
                    {{ $totalTunaiCount }} <span class="text-sm font-medium text-slate-500">Transaksi</span>
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    Pelunasan 100% tanpa inkaso
                </div>
            </div>

            <div class="bg-white border border-sky-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-sky-700 font-mono">Rata-Rata Nilai Iklan Tunai</span>
                <div class="mt-2 text-3xl font-black text-sky-700 tracking-tight font-mono">
                    @php
                        $avgTunai = $totalTunaiCount > 0 ? $totalNominalTunai / $totalTunaiCount : 0;
                    @endphp
                    Rp {{ number_format($avgTunai, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    Per transaksi faktur tunai
                </div>
            </div>
        </div>

        <!-- Filter Pencarian -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('keuangan.faktur.iklan.tunai') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-5">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Cari Faktur Tunai</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor faktur atau customer..." 
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" 
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" 
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                </div>

                <div class="sm:col-span-1 flex items-end">
                    <button type="submit" class="w-full h-9 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Faktur Tunai -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-emerald-50/70 text-[11px] uppercase tracking-wider text-emerald-900 font-bold border-b border-emerald-200">
                        <tr>
                            <th class="py-3 px-4">No. Faktur</th>
                            <th class="py-3 px-4">No. Kwitansi Kasir</th>
                            <th class="py-3 px-4">Pengiklan (Customer)</th>
                            <th class="py-3 px-4">Tanggal Pelunasan</th>
                            <th class="py-3 px-4 text-center">Metode Bayar</th>
                            <th class="py-3 px-4 text-right">Nominal Lunas</th>
                            <th class="py-3 px-4">Kasir / Penerima</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($fakturs as $faktur)
                            @php
                                $bayar = $faktur->pembayaran->first();
                            @endphp
                            <tr class="hover:bg-emerald-50/20 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                    <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" class="text-blue-600 hover:underline">
                                        {{ $faktur->no_faktur }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-semibold text-emerald-700">
                                    {{ $bayar->no_kwitansi ?? 'KW-LANGSUNG' }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $faktur->customer->nama_customer ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    {{ $faktur->tanggal_faktur->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ ($bayar->metode_bayar ?? 'tunai') === 'tunai' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ strtoupper($bayar->metode_bayar ?? 'TUNAI') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-emerald-600 font-mono text-sm">
                                    Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-xs text-slate-500">
                                    {{ $bayar->penerima->name ?? ($faktur->creator->name ?? 'Kasir') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" 
                                           class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition" 
                                           title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('keuangan.faktur.cetak', $faktur->id) }}" 
                                           target="_blank"
                                           class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-slate-100 rounded-lg transition" 
                                           title="Cetak Kwitansi & Faktur">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">💵</div>
                                    <p class="text-sm font-semibold text-slate-600">Belum ada faktur iklan tunai tercatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($fakturs->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $fakturs->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
