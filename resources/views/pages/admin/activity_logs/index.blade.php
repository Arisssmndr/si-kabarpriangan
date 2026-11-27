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

        <!-- 3. Toolbar Pencarian & Filter Log (Standar Iklan 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pencarian Log
                    </label>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Ketik email, IP address, atau peristiwa..." 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Jenis Peristiwa
                    </label>
                    <select name="action" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Jenis Peristiwa</option>
                        @foreach($actionsList as $key => $label)
                            <option value="{{ $key }}" {{ $actionFilter === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if($search || ($actionFilter && $actionFilter !== 'all'))
                        <a href="{{ route('admin.activity-logs.index') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 4. Tabel Data Log (Standar Iklan: Teks Jelas 14px, Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900]">
                        <tr class="text-xs font-bold uppercase tracking-wider text-white whitespace-nowrap">
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Waktu (WIB)</th>
                            <th class="py-3 px-3.5">Jenis Peristiwa</th>
                            <th class="py-3 px-3.5">Akun Terkait</th>
                            <th class="py-3 px-3.5">Alamat IP</th>
                            <th class="py-3 px-3.5">Perangkat &amp; Browser</th>
                            <th class="py-3 px-3.5">Deskripsi Rinci</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($logs as $key => $log)
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- No -->
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $logs->firstItem() + $key }}
                                </td>

                                <!-- 1. Waktu -->
                                <td class="py-3.5 px-3.5 font-mono text-slate-600 font-normal">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>

                                <!-- 2. Jenis Peristiwa Badge -->
                                <td class="py-3.5 px-3.5">
                                    @php
                                        $act = $log->action;
                                        $badge = match($act) {
                                            'LOGIN_SUCCESS'           => 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200',
                                            'LOGIN_FAILED_1ST'        => 'bg-amber-50 text-amber-800 border-amber-300',
                                            'LOGIN_FAILED_2ND'        => 'bg-amber-50 text-amber-800 border-amber-300',
                                            'ACCOUNT_LOCKED_15M'      => 'bg-slate-900 text-white border-slate-900 font-bold',
                                            'LOGIN_REJECTED_DISABLED' => 'bg-slate-100 text-slate-700 border-slate-200',
                                            'BACKUP_DATABASE_SUCCESS' => 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200',
                                            default                   => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold border {{ $badge }}">
                                        {{ $log->action }}
                                    </span>
                                </td>

                                <!-- 3. Akun Terkait -->
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $log->email_attempted ?: ($log->user ? $log->user->email : '-') }}
                                </td>

                                <!-- 4. Alamat IP -->
                                <td class="py-3.5 px-3.5 font-mono text-slate-700 font-normal">
                                    {{ $log->resolved_ip }}
                                </td>

                                <!-- 5. Perangkat & Browser (1 Baris Bersih) -->
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    @php
                                        $dev = $log->device_name !== '-' ? $log->device_name : '';
                                        $brw = $log->browser_name !== '-' ? $log->browser_name : '';
                                        $devBrowser = trim(($dev ? $dev : '') . ($dev && $brw ? ' • ' : '') . ($brw ? $brw : ''));
                                    @endphp
                                    {{ $devBrowser ?: '-' }}
                                </td>

                                <!-- 6. Deskripsi Rinci -->
                                <td class="py-3.5 px-3.5 text-slate-700 font-normal">
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
