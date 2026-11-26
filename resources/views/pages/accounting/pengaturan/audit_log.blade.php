<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Log Aktivitas Akuntansi (Audit Trail)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Keamanan &amp; Audit
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Rekam jejak setiap aksi krusial, pembentukan jurnal, dan koreksi piutang.
                    </p>
                </div>
            </div>
        </div>

        <!-- 2. KPI / STATISTIK CEPAT AUDIT TRAIL -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Log Tercatat</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($stats['total_log'] ?? 0) }} Aktivitas</div>
                <p class="text-xs text-slate-400 mt-0.5">Sejak sistem aktif</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Aktivitas Hari Ini</span>
                <div class="text-lg font-bold text-kp-blue-700 mt-1 font-mono">{{ number_format($stats['hari_ini'] ?? 0) }} Aksi</div>
                <p class="text-xs text-slate-400 mt-0.5">{{ date('d M Y') }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Modul Terlindungi</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($stats['total_modul'] ?? 0) }} Modul</div>
                <p class="text-xs text-slate-400 mt-0.5">Cakupan audit trail</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Operator Tercatat</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($stats['user_tercatat'] ?? 0) }} User</div>
                <p class="text-xs text-slate-400 mt-0.5">Otentikasi tercatat</p>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter Mandiri (Standar Divisi Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.pengaturan.audit-log') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Aktivitas</label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik aksi, keterangan, atau nama staf..." 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Modul Sistem</label>
                    <select name="modul" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ request('modul', 'all') === 'all' ? 'selected' : '' }}>Semua Modul</option>
                        <option value="IKLAN" {{ request('modul') === 'IKLAN' ? 'selected' : '' }}>IKLAN</option>
                        <option value="SIRKULASI" {{ request('modul') === 'SIRKULASI' ? 'selected' : '' }}>SIRKULASI</option>
                        <option value="GL" {{ request('modul') === 'GL' ? 'selected' : '' }}>GL</option>
                        <option value="ASET_TETAP" {{ request('modul') === 'ASET_TETAP' ? 'selected' : '' }}>ASET_TETAP</option>
                        <option value="PERIODE" {{ request('modul') === 'PERIODE' ? 'selected' : '' }}>PERIODE</option>
                        <option value="MASTER" {{ request('modul') === 'MASTER' ? 'selected' : '' }}>MASTER</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Dari Tanggal</label>
                    <input type="date" 
                           name="dari" 
                           value="{{ request('dari') }}" 
                           class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sampai Tanggal</label>
                    <input type="date" 
                           name="sampai" 
                           value="{{ request('sampai') }}" 
                           class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'modul', 'dari', 'sampai']))
                        <a href="{{ route('accounting.pengaturan.audit-log') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Pure Tabel Log Audit -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">Waktu Transaksi</th>
                            <th class="py-3 px-3.5">Pengguna</th>
                            <th class="py-3 px-3.5">Aksi</th>
                            <th class="py-3 px-3.5">Modul / Target</th>
                            <th class="py-3 px-3.5">Keterangan Aktivitas</th>
                            <th class="py-3 px-3.5 text-right font-mono text-xs">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($logs as $key => $log)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400">{{ $logs->firstItem() + $key }}</td>
                                <td class="py-3 px-3.5 whitespace-nowrap text-xs text-slate-600 font-mono">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="py-3 px-3.5 font-bold text-slate-900">{{ $log->user?->name ?? 'Sistem / Otomatis' }}</td>
                                <td class="py-3 px-3.5">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md uppercase {{ str_contains($log->aksi, 'HITUNG') ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                                        {{ $log->aksi }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 font-semibold text-xs text-slate-700">{{ $log->modul ?: 'AKUNTANSI' }}</td>
                                <td class="py-3 px-3.5 max-w-md truncate text-xs">{{ $log->keterangan }}</td>
                                <td class="py-3 px-3.5 text-right font-mono text-xs text-slate-400">{{ $log->ip_address ?: '127.0.0.1' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada catatan aktivitas akuntansi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        </div>

    </div>
</x-app-layout>

