<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Dashboard Akuntansi
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                            Terpadu
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Ringkasan pembukuan dan status proses data.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <div class="text-xs font-medium text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-200 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Periode: <strong class="text-slate-900">{{ $periodeBerjalan?->bulan_tahun ?? date('Y-m') }}</strong></span>
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

        <!-- 4 KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Pendapatan</span>
                    <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-xl font-bold text-slate-900">
                    Rp {{ number_format($omsetUsaha, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-slate-400">Total penjualan diakui</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Penerimaan Kas</span>
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-xl font-bold text-emerald-600">
                    Rp {{ number_format($penerimaanKas, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-slate-400">Kas & bank masuk</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Piutang Usaha</span>
                    <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-xl font-bold text-slate-900">
                    Rp {{ number_format($piutangUsaha, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-slate-400">Saldo piutang berjalan</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Laba Kotor</span>
                    <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-xl font-bold text-indigo-700">
                    Rp {{ number_format($estimasiLabaBersih, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-slate-400">Estimasi laba berjalan</div>
            </div>
        </div>

        <!-- Status Hitung Harian & Bulanan (Anti-Dobel) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-slate-900">Hitung Iklan Harian</span>
                        @if($statusHitungIklanHariIni)
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Sudah Dihitung</span>
                        @else
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200">Belum Dihitung</span>
                        @endif
                    </div>
                    <div class="text-xs text-slate-500 mt-1">Tanggal: {{ date('d/m/Y') }}</div>
                </div>
                <a href="{{ route('accounting.iklan.hitung-penjualan') }}" class="h-9 px-3.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl flex items-center justify-center transition">
                    Buka Iklan
                </a>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-slate-900">Hitung Sirkulasi Bulanan</span>
                        @if($statusHitungSirkulasiBulanIni)
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Sudah Dihitung</span>
                        @else
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200">Belum Dihitung</span>
                        @endif
                    </div>
                    <div class="text-xs text-slate-500 mt-1">Bulan: {{ date('m/Y') }}</div>
                </div>
                <a href="{{ route('accounting.sirkulasi.hitung-penjualan') }}" class="h-9 px-3.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl flex items-center justify-center transition">
                    Buka Sirkulasi
                </a>
            </div>
        </div>

        <!-- Tabel Jurnal Umum Terakhir -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Jurnal Umum Terakhir</h2>
                    <p class="text-xs text-slate-500">Mutasi transaksi general ledger terbaru.</p>
                </div>
                <a href="{{ route('accounting.gl.jurnal') }}" class="text-xs font-semibold text-kp-blue-600 hover:text-kp-blue-700">
                    Lihat Semua &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">No. Jurnal</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">No. Bukti</th>
                            <th class="py-3 px-3.5">Keterangan</th>
                            <th class="py-3 px-3.5 text-right w-36">Total</th>
                            <th class="py-3 px-3.5 text-center w-28">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($jurnalTerakhir as $key => $j)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900">{{ $j->nomor_jurnal }}</td>
                                <td class="py-3 px-3.5">{{ \Carbon\Carbon::parse($j->tanggal)->format('d/m/Y') }}</td>
                                <td class="py-3 px-3.5 font-mono text-xs">{{ $j->nomor_bukti ?: '-' }}</td>
                                <td class="py-3 px-3.5 max-w-xs truncate">{{ $j->keterangan }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($j->total_debet, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md {{ $j->is_posted ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $j->is_posted ? 'Posted' : 'Draft' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada jurnal umum yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
