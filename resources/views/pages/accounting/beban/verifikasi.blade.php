<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Verifikasi Beban & Kas Kecil
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            BKK Kasir
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Pemeriksaan bukti kas keluar dari kasir dan pembebanan ke pos bagan akun GL.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <div class="text-xs font-medium text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-200">
                    Belum Posting: <strong class="text-slate-900 font-mono">Rp {{ number_format($totalBelumPosting, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- 2. KPI / Metrics Ribbon (Palet 60-30-10) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Beban Belum Posting</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($totalBelumPosting, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Antrean verifikasi kasir</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Jumlah Bukti Pending</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($countBelumPosting ?? 0, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Voucher BKK menunggu posting</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Beban Sudah Posting</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($totalSudahPosting, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Terkonfirmasi & tercatat GL</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Jumlah Bukti Posted</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($countSudahPosting ?? 0, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Voucher BKK terverifikasi</span>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter Mandiri (12-Col Grid) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('accounting.beban.verifikasi') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pencarian Beban (BKK)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="No. BKK, penerima, keperluan..." 
                               class="w-full h-11 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Posting GL</label>
                    <select name="status" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="">Semua Status Posting</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Belum Posting)</option>
                        <option value="posted" {{ request('status') === 'posted' ? 'selected' : '' }}>Posted (Sudah Posting)</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari') }}" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="sm:col-span-1 flex items-center gap-2">
                    <button type="submit" class="w-full h-11 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer" title="Cari Data">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <a href="{{ route('accounting.beban.verifikasi') }}" class="h-11 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center" title="Reset Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.033 8.033 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                </div>
            </form>
        </div>

        <!-- Pure Tabel Bukti Kas Keluar (BKK) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">No. BKK</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Diberikan Kepada</th>
                            <th class="py-3 px-3.5">Keterangan</th>
                            <th class="py-3 px-3.5 text-right">Nominal</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-48">Alokasi Beban (GL)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($daftarBeban as $key => $b)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $b->no_bkk ?: ('BKK-' . $b->id) }}</td>
                                <td class="py-3 px-3.5">{{ \Carbon\Carbon::parse($b->tanggal_keluar)->format('d/m/Y') }}</td>
                                <td class="py-3 px-3.5">{{ $b->diberikan_kepada ?: ($b->nama_penerima ?: '-') }}</td>
                                <td class="py-3 px-3.5 max-w-xs truncate">{{ $b->keterangan }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($b->nominal_keluar ?: $b->jumlah_keluar ?: 0, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md {{ $b->is_pulled_accounting ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                                        {{ $b->is_pulled_accounting ? 'Posted' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if(!$b->is_pulled_accounting)
                                        <form method="POST" action="{{ route('accounting.beban.verifikasi.post', $b->id) }}" class="flex items-center gap-1.5">
                                            @csrf
                                            <select name="kode_perkiraan" required class="h-8 px-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 transition">
                                                <option value="">Pilih Akun...</option>
                                                @foreach($daftarAkunBeban as $akun)
                                                    <option value="{{ $akun->kode_perkiraan }}">
                                                        {{ $akun->kode_perkiraan }} - {{ $akun->nama_perkiraan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" 
                                                    title="Post ke Buku Besar"
                                                    class="h-8 px-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-lg shadow-2xs transition cursor-pointer">
                                                Post
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs font-mono text-slate-500">{{ $b->kode_perkiraan ?: '6.00.01' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada data pengeluaran kasir.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-t border-slate-200">
                {{ $daftarBeban->links() }}
            </div>
        </div>

    </div>
</x-app-layout>

