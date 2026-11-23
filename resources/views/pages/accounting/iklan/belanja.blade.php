<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Riwayat Belanja Pemasang
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Kartu Iklan
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Pencarian akumulasi transaksi belanja dan status piutang per pemasang.
                    </p>
                </div>
            </div>
        </div>

        <!-- 2. KPI / STATISTIK CEPAT (METRICS RIBBON 60-30-10) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Transaksi</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($stats['total_transaksi'] ?? 0) }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Semua order iklan</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akumulasi Belanja</span>
                <div class="text-lg font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($stats['total_omset'] ?? 0, 0, ',', '.') }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Nilai bruto & neto</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Piutang Belum Lunas</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($stats['total_piutang'] ?? 0, 0, ',', '.') }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Sisa tagihan klien</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Transaksi Lunas</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($stats['total_lunas'] ?? 0) }} Order</div>
                <p class="text-xs text-slate-400 mt-0.5">Selesai terbayar</p>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter Mandiri -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.iklan.belanja') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Data</label>
                    <input type="text" 
                           name="q" 
                           value="{{ $query }}" 
                           placeholder="Ketik nama klien, biro, atau no faktur..." 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori Iklan</label>
                    <select name="kategori" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ ($kategori ?? 'all') === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                        <option value="BARIS" {{ ($kategori ?? '') === 'BARIS' ? 'selected' : '' }}>Iklan Baris</option>
                        <option value="KOLOM" {{ ($kategori ?? '') === 'KOLOM' ? 'selected' : '' }}>Iklan Kolom</option>
                        <option value="DISPLAY" {{ ($kategori ?? '') === 'DISPLAY' ? 'selected' : '' }}>Iklan Display</option>
                        <option value="ADVERTORIAL" {{ ($kategori ?? '') === 'ADVERTORIAL' ? 'selected' : '' }}>Advertorial</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Pembayaran</label>
                    <select name="status_bayar" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ ($statusBayar ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="lunas" {{ ($statusBayar ?? '') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="piutang" {{ ($statusBayar ?? '') === 'piutang' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Rentang Tanggal</label>
                    <input type="date" 
                           name="dari" 
                           value="{{ $dari ?? '' }}" 
                           class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'kategori', 'status_bayar', 'dari', 'sampai']))
                        <a href="{{ route('accounting.iklan.belanja') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 4. Pure Data Tabel Riwayat Transaksi -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">No. Faktur</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Pemasang</th>
                            <th class="py-3 px-3.5">Jenis Iklan</th>
                            <th class="py-3 px-3.5 text-right">Nilai Tagihan</th>
                            <th class="py-3 px-3.5 text-right">Sisa Piutang</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 text-sm">
                        @forelse($riwayat as $key => $r)
                            @php
                                $noFaktur = $r->nofakturkoran ?? $r->no_faktur ?? ('FAK-' . $r->id);
                                $tgl = $r->tanggal_muatkoran ?? $r->tanggal_transaksikoran ?? $r->tanggal;
                                $pemasang = $r->nama_pemasangkoran ?? $r->pemasang?->nama_pemasang ?? $r->nama_pemohon ?? '-';
                                $kategori = match($r->kategori_form ?? '') {
                                    'BARIS'       => 'Iklan Baris',
                                    'KOLOM'       => 'Iklan Kolom',
                                    'DISPLAY'     => 'Iklan Display',
                                    'ADVERTORIAL' => 'Advertorial',
                                    default       => ($r->kategori_form ?: 'Koran')
                                };
                                $tagihan = (float) ($r->totaltagihan_transaksikoran ?? $r->total_akhir ?? $r->subtotal ?? 0);
                                $piutang = (float) ($r->piutang_transaksikoran ?? 0);
                                $isLunas = ($piutang <= 0);
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400 font-mono">
                                    {{ method_exists($riwayat, 'firstItem') ? ($riwayat->firstItem() + $key) : ($key + 1) }}
                                </td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $noFaktur }}</td>
                                <td class="py-3 px-3.5 text-slate-600">{{ $tgl ? \Carbon\Carbon::parse($tgl)->format('d/m/Y') : '-' }}</td>
                                <td class="py-3 px-3.5 max-w-xs truncate text-slate-900 font-medium">{{ $pemasang }}</td>
                                <td class="py-3 px-3.5 text-slate-600">{{ $kategori }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono {{ $piutang > 0 ? 'text-slate-900 font-semibold' : 'text-slate-400' }}">
                                    Rp {{ number_format($piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md {{ $isLunas ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                                        {{ $isLunas ? 'Lunas' : 'Belum Lunas' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <x-action-dropdown align="right">
                                        <a href="{{ route('accounting.iklan.hitung-penjualan', ['q' => $noFaktur]) }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <span>Hitung Penjualan</span>
                                        </a>
                                        @if(isset($r->id))
                                            <a href="{{ route('transaksikoran.show', $r->id) }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail Faktur Koran</span>
                                            </a>
                                        @endif
                                    </x-action-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400 text-sm">
                                    {{ $query ? 'Tidak ditemukan riwayat untuk pencarian tersebut.' : 'Belum ada transaksi belanja yang sesuai filter.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($riwayat, 'hasPages') && $riwayat->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $riwayat->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>

