<x-app-layout>
    <div class="space-y-6">

        <!-- 1. Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-10 w-auto object-contain shrink-0" onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Log Aktivitas &amp; Keamanan
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Jejak audit login, percobaan akses, dan peristiwa keamanan sistem informasi terpadu.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <a href="{{ route('admin.users.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl border border-slate-300 transition">
                    <svg class="w-4 h-4 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Manajemen Pengguna</span>
                </a>

                <a href="{{ route('admin.backups.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl border border-slate-300 transition">
                    <svg class="w-4 h-4 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7zm0 5h16" />
                    </svg>
                    <span>Cadangan Data</span>
                </a>
            </div>
        </div>

        <!-- 2. Statistik Aktivitas Log -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-xs">
                <span class="text-xs font-bold text-black block">Total Rekaman Log</span>
                <span class="text-2xl font-black text-black mt-1 block">{{ $stats['total'] }}</span>
                <span class="text-[11px] text-black font-medium">Sepanjang Waktu</span>
            </div>

            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-xs">
                <span class="text-xs font-bold text-black block">Login Sukses Hari Ini</span>
                <span class="text-2xl font-black text-emerald-800 mt-1 block">{{ $stats['logins_today'] }}</span>
                <span class="text-[11px] text-emerald-700 font-bold">Autentikasi Sah</span>
            </div>

            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-xs">
                <span class="text-xs font-bold text-black block">Percobaan Gagal Hari Ini</span>
                <span class="text-2xl font-black text-amber-800 mt-1 block">{{ $stats['failed_today'] }}</span>
                <span class="text-[11px] text-amber-700 font-bold">Salah Password</span>
            </div>

            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-xs">
                <span class="text-xs font-bold text-black block">Penguncian Sementara (15m)</span>
                <span class="text-2xl font-black text-red-700 mt-1 block">{{ $stats['lockouts'] }}</span>
                <span class="text-[11px] text-red-600 font-bold">Batas 3x Gagal</span>
            </div>
        </div>

        <!-- 3. Tabel Data Log & Filter Bar -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/70 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
                    <div class="relative flex-1 min-w-[240px] max-w-sm">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-black/60">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari email, IP address, atau deskripsi..." 
                               class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-black placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 font-medium" />
                    </div>

                    <select name="action" 
                            onchange="this.form.submit()" 
                            class="py-2 pl-3 pr-8 bg-white border border-slate-300 rounded-xl text-xs text-black font-semibold focus:outline-none focus:border-kp-blue-600">
                        <option value="all">Semua Jenis Peristiwa</option>
                        @foreach($actionsList as $key => $label)
                            <option value="{{ $key }}" {{ $actionFilter === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 bg-[#0E527B] hover:bg-[#07334F] text-white font-bold text-xs rounded-xl transition cursor-pointer shadow-xs">
                        Filter Log
                    </button>

                    @if($search || ($actionFilter && $actionFilter !== 'all'))
                        <a href="{{ route('admin.activity-logs.index') }}" class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-black font-bold text-xs rounded-xl transition">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="text-xs text-black font-semibold">
                    Total: <span class="font-extrabold text-black">{{ $logs->total() }}</span> rekaman audit
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-100/90 text-xs font-extrabold text-slate-800 uppercase tracking-wider whitespace-nowrap">
                            <th class="py-3.5 px-4">Waktu (WIB)</th>
                            <th class="py-3.5 px-4">Jenis Peristiwa</th>
                            <th class="py-3.5 px-4">Akun Terkait</th>
                            <th class="py-3.5 px-4">Alamat IP</th>
                            <th class="py-3.5 px-4">Perangkat &amp; Browser</th>
                            <th class="py-3.5 px-4">Deskripsi Rinci</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/90 transition-colors">
                                <!-- 1. Waktu -->
                                <td class="py-3.5 px-4 font-mono font-medium text-slate-900 whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>

                                <!-- 2. Jenis Peristiwa Badge -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @php
                                        $act = $log->action;
                                        $badge = match($act) {
                                            'LOGIN_SUCCESS' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                                            'LOGIN_FAILED_1ST' => 'bg-amber-50 text-amber-800 border-amber-300',
                                            'LOGIN_FAILED_2ND' => 'bg-orange-50 text-orange-900 border-orange-300',
                                            'ACCOUNT_LOCKED_15M' => 'bg-rose-100 text-rose-900 border-rose-400 font-black',
                                            'LOGIN_REJECTED_DISABLED' => 'bg-rose-50 text-rose-800 border-rose-300',
                                            'BACKUP_DATABASE_SUCCESS' => 'bg-sky-50 text-[#0E527B] border-sky-300',
                                            default => 'bg-slate-100 text-slate-800 border-slate-300',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold border {{ $badge }}">
                                        {{ $log->action }}
                                    </span>
                                </td>

                                <!-- 3. Akun Terkait -->
                                <td class="py-3.5 px-4 font-mono text-slate-900 font-semibold whitespace-nowrap">
                                    {{ $log->email_attempted ?: ($log->user ? $log->user->email : '-') }}
                                </td>

                                <!-- 4. Alamat IP (1 Kolom Mandiri) -->
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-800 text-xs whitespace-nowrap">
                                    {{ $log->resolved_ip }}
                                </td>

                                <!-- 5. Perangkat & Browser (1 Kolom Mandiri) -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="text-xs font-semibold text-slate-800">
                                        {{ $log->device_name }}
                                    </div>
                                    <div class="text-[11px] font-bold text-[#0E527B]">
                                        {{ $log->browser_name }}
                                    </div>
                                </td>

                                <!-- 6. Deskripsi Rinci -->
                                <td class="py-3.5 px-4 text-slate-700 font-medium">
                                    {{ $log->description ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-600">
                                    <p class="font-bold text-sm">Tidak ada rekam log aktivitas yang tercatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                                    <p class="font-bold text-sm">Tidak ada rekam log aktivitas yang tercatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50/70">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
