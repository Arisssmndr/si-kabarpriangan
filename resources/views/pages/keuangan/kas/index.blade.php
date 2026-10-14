<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            BUKU KAS &amp; TREASURY REAL-TIME
                        </h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Mutasi kas terpadu (pelunasan langsung &amp; setoran inkaso masuk, pencairan anggaran keluar) dengan saldo berjalan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('keuangan.kas.harian') }}" 
                   class="h-9 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center gap-1.5 shadow-2xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Laporan Kas Harian</span>
                </a>

                <a href="{{ route('keuangan.kas.bulanan') }}" 
                   class="h-9 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center gap-1.5 shadow-2xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Laporan Kas Bulanan</span>
                </a>

                <a href="{{ route('keuangan.alur') }}" 
                   class="h-9 px-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                    </svg>
                    <span>Diagram Alur</span>
                </a>
            </div>
        </div>

        <!-- 3 KPI Utama Saldo Kas Real-Time -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Saldo Akhir Berjalan -->
            <div class="bg-gradient-to-br from-blue-900 to-slate-900 text-white rounded-2xl p-6 shadow-md relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-200 font-mono">Saldo Kas Berjalan (Saat Ini)</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    </div>
                    <div class="mt-3 text-3xl font-black tracking-tight font-mono text-emerald-400">
                        Rp {{ number_format($saldoSaatIni, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-blue-200/80 mt-2">
                        Pusat likuiditas keuangan aktif PT Berkah Kabar Priangan
                    </p>
                </div>
            </div>

            <!-- Total Kas Masuk Kumulatif -->
            <div class="bg-white border border-emerald-200 rounded-2xl p-6 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 font-mono">Total Kas Masuk (Inflow)</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs border border-emerald-200">
                        ↓
                    </span>
                </div>
                <div class="mt-3 text-2xl font-black text-slate-900 font-mono tracking-tight">
                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-emerald-700 mt-2 flex items-center gap-1 font-semibold">
                    <span>✓ Pelunasan langsung kasir &amp; setoran inkaso</span>
                </p>
            </div>

            <!-- Total Kas Keluar Kumulatif -->
            <div class="bg-white border border-rose-200 rounded-2xl p-6 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-700 font-mono">Total Kas Keluar (Outflow)</span>
                    <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xs border border-rose-200">
                        ↑
                    </span>
                </div>
                <div class="mt-3 text-2xl font-black text-rose-600 font-mono tracking-tight">
                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-rose-700 mt-2 flex items-center gap-1 font-semibold">
                    <span>⚠ Pencairan anggaran operasional multi-divisi</span>
                </p>
            </div>
        </div>

        <!-- Filter Mutasi Kas -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('keuangan.kas.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari') }}" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Jenis Mutasi</label>
                    <select name="jenis" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua (Masuk &amp; Keluar)</option>
                        <option value="masuk" {{ request('jenis') === 'masuk' ? 'selected' : '' }}>Kas Masuk (+)</option>
                        <option value="keluar" {{ request('jenis') === 'keluar' ? 'selected' : '' }}>Kas Keluar (-)</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Kategori Kas</label>
                    <select name="kategori" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-end gap-2">
                    <button type="submit" class="w-full h-9 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['dari', 'sampai', 'jenis', 'kategori']))
                        <a href="{{ route('keuangan.kas.index') }}" class="h-9 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center justify-center transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Mutasi Kas Real-Time -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                        Buku Mutasi Kas &amp; Saldo Berjalan (Ledger)
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Menampilkan seluruh aliran dana keluar masuk secara kronologis
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('keuangan.kas.cetak', ['mode' => 'harian', 'tanggal' => date('Y-m-d')]) }}" target="_blank"
                       class="px-3 py-1.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold flex items-center gap-1 transition">
                        🖨 Cetak Ledger
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Transaksi</th>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Kategori Mutasi</th>
                            <th class="py-3 px-4">Keterangan Arus Kas</th>
                            <th class="py-3 px-4 text-right">Kas Masuk (Rp)</th>
                            <th class="py-3 px-4 text-right">Kas Keluar (Rp)</th>
                            <th class="py-3 px-4 text-right">Saldo Berjalan (Rp)</th>
                            <th class="py-3 px-4 text-center">Kasir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($transaksis as $kas)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-mono font-bold {{ $kas->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $kas->no_transaksi }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-[11px]">
                                    {{ $kas->tanggal->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $kas->jenis === 'masuk' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                                        {{ $kas->kategori_kas }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs text-slate-800">
                                    <div class="line-clamp-2 leading-relaxed font-normal">
                                        {{ $kas->keterangan }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-600">
                                    @if($kas->jenis === 'masuk')
                                        + {{ number_format($kas->nominal, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-rose-600">
                                    @if($kas->jenis === 'keluar')
                                        - {{ number_format($kas->nominal, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-black text-slate-900">
                                    Rp {{ number_format($kas->saldo_akhir, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center text-slate-400 text-[11px]">
                                    {{ $kas->creator->name ?? 'Kasir' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">🏦</div>
                                    <p class="text-sm font-semibold text-slate-600">Belum ada mutasi kas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transaksis->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $transaksis->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
