<x-app-layout>
    <div class="space-y-6">

        <!-- Header Panel Pemantauan Kesehatan Sistem -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Status Sistem
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pantau kondisi server, database, dan aktivitas terkini.
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi Perawatan -->
            <div class="flex flex-wrap items-center gap-2">
                <form method="POST" action="{{ route('admin.sistem.clear-cache') }}" onsubmit="return confirm('Bersihkan cache aplikasi sekarang?');">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg shadow-2xs transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Bersihkan Cache</span>
                    </button>
                </form>

                <a href="{{ route('admin.backups.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7zm0 5h16" />
                    </svg>
                    <span>Cadangan Data</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- 1. SYSTEM METRICS DIAGNOSTICS (3 Kolom Utama) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Card 1: Database Status & Latency -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $dbStatus['connected'] ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Koneksi Database</h2>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $dbStatus['connected'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                            {{ $dbStatus['connected'] ? 'Terhubung' : 'Terputus' }}
                        </span>
                    </div>

                    <div class="mt-4 space-y-2.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Driver &amp; Database</span>
                            <span class="font-bold text-slate-900 font-mono">{{ $dbStatus['driver'] }} ({{ $dbStatus['database'] }})</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Query Latency (Ping)</span>
                            <span class="font-extrabold {{ $dbStatus['latency'] < 50 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $dbStatus['latency'] }} ms</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Total Tabel Terdaftar</span>
                            <span class="font-bold text-slate-900">{{ $dbStatus['tables_cnt'] }} tabel</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Estimasi Ukuran Data</span>
                            <span class="font-bold text-slate-900 font-mono">{{ $dbStatus['db_size'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Integritas:</span>
                    <span class="text-emerald-700 font-bold">Optimal &bull; No Lock</span>
                </div>
            </div>

            <!-- Card 2: Server Runtime Environment -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-kp-blue-500"></span>
                            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Lingkungan Server</h2>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-sky-50 text-kp-blue-700 border border-sky-200 uppercase">
                            {{ $serverInfo['app_env'] }}
                        </span>
                    </div>

                    <div class="mt-4 space-y-2.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">PHP Version</span>
                            <span class="font-bold text-slate-900 font-mono">v{{ $serverInfo['php_version'] }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Laravel Framework</span>
                            <span class="font-bold text-slate-900 font-mono">v{{ $serverInfo['laravel_version'] }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Penggunaan RAM</span>
                            <span class="font-bold text-slate-900 font-mono">{{ $serverInfo['memory_usage'] }} / {{ $serverInfo['memory_limit'] }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Peak Memory Limit</span>
                            <span class="font-semibold text-slate-700 font-mono">{{ $serverInfo['memory_peak'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Zona Waktu:</span>
                    <span class="font-semibold text-slate-800">{{ $serverInfo['app_timezone'] }} ({{ now()->format('H:i') }} WIB)</span>
                </div>
            </div>

            <!-- Card 3: Storage & Disk Health -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Kapasitas Penyimpanan</h2>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200">
                            {{ $diskInfo['used_pct'] }}% Terpakai
                        </span>
                    </div>

                    <div class="mt-4 space-y-2.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Sisa Ruang Bebas (Free)</span>
                            <span class="font-extrabold text-slate-900 font-mono">{{ $diskInfo['free_formatted'] }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Kapasitas Total Disk</span>
                            <span class="font-semibold text-slate-700 font-mono">{{ $diskInfo['total_formatted'] }}</span>
                        </div>

                        <!-- Progress Bar Disk Usage -->
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden mt-1">
                            <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ $diskInfo['used_pct'] }}%;"></div>
                        </div>

                        <div class="flex items-center justify-between text-xs pt-1">
                            <span class="text-slate-500">Ukuran Log Sistem</span>
                            <span class="font-bold text-slate-900 font-mono">{{ $logSize }} KB</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500 truncate" title="{{ $diskInfo['storage_path'] }}">
                    <span>Path:</span> <span class="font-mono text-[10px] text-slate-700">{{ $diskInfo['storage_path'] }}</span>
                </div>
            </div>

        </div>

        <!-- 2. LIVE ERROR LOG VIEWER (Laravel Logs Viewer) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Live System Error Log Viewer</h2>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700 font-mono">
                            laravel.log ({{ count($logEntries) }} entri terbaru)
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 font-normal mt-0.5">Pemantauan pengecualian runtime PHP, warning SQL, dan pesan kegagalan sistem</p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.sistem.download-log') }}" 
                       class="px-2.5 py-1.5 bg-white border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Unduh Log</span>
                    </a>

                    <form method="POST" action="{{ route('admin.sistem.clear-log') }}" onsubmit="return confirm('Kosongkan isi berkas log laravel.log sekarang?');">
                        @csrf
                        <button type="submit" 
                                class="px-2.5 py-1.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-lg hover:bg-rose-100 transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Kosongkan</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">
                @forelse($logEntries as $idx => $entry)
                    <div x-data="{ expanded: false }" class="p-4 hover:bg-slate-50/80 transition text-xs">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-2.5 min-w-0 flex-1">
                                <!-- Level Badge -->
                                @php
                                    $lvl = $entry['level'];
                                    $badgeClass = match($lvl) {
                                        'EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'WARNING' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'NOTICE', 'INFO' => 'bg-sky-50 text-sky-700 border-sky-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase border shrink-0 {{ $badgeClass }}">
                                    {{ $lvl }}
                                </span>

                                <div class="min-w-0 flex-1">
                                    <div class="font-mono text-slate-900 font-semibold break-all">
                                        {{ $entry['message'] }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-2">
                                        <span class="font-mono">{{ $entry['timestamp'] }}</span>
                                        <span>&bull;</span>
                                        <span class="uppercase font-bold text-[10px] text-slate-500">{{ $entry['env'] }}</span>
                                    </div>
                                </div>
                            </div>

                            @if($entry['trace'])
                                <button type="button" @click="expanded = !expanded" 
                                        class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold transition shrink-0 cursor-pointer">
                                    <span x-text="expanded ? 'Tutup Trace' : 'Lihat Trace'"></span>
                                </button>
                            @endif
                        </div>

                        <!-- Stack Trace Preview -->
                        @if($entry['trace'])
                            <div x-cloak x-show="expanded" class="mt-3 p-3 bg-slate-900 text-slate-200 font-mono text-[10px] rounded-lg overflow-x-auto whitespace-pre-wrap max-h-60 leading-relaxed">
{{ $entry['trace'] }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Tidak ditemukan error log sistem. Seluruh proses berjalan normal tanpa kendala!</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 3. AKTIVITAS & SESI PENGGUNA TERKINI -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Sesi &amp; Aktivitas Terkini Pengguna</h2>
                    <p class="text-[11px] text-slate-500 font-normal">Pengguna dengan interaksi terbaru di portal terpadu</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-[11px] text-kp-blue-600 font-semibold hover:underline">
                    Kelola Semua Pengguna &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="px-4 py-2.5">Nama &amp; Email Pengguna</th>
                            <th class="px-4 py-2.5">Divisi / Role</th>
                            <th class="px-4 py-2.5">Status Akun</th>
                            <th class="px-4 py-2.5">Aktivitas Terakhir</th>
                            <th class="px-4 py-2.5 text-center">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentUsers as $u)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-2.5">
                                    <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $u->email }}</div>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5">
                                    @if($u->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-slate-600 text-[11px]">
                                    {{ $u->updated_at ? $u->updated_at->diffForHumans() : '-' }}
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.force-logout', $u->id) }}" onsubmit="return confirm('Paksa keluar (force logout) pengguna {{ $u->name }}?');" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 text-[10px] font-bold border border-rose-200 transition cursor-pointer">
                                                Force Logout
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] font-semibold text-slate-400">Sesi Anda</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
