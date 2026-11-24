<x-app-layout>
    <div x-data="userManagement({ 
        initialOnlineCount: {{ $stats['online'] ?? 0 }}, 
        pollUrl: '{{ route('admin.users.online-status') }}' 
    })" class="space-y-6">

        <!-- 1. Header Halaman & Navigasi Cepat Pusat Keamanan Admin -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-10 w-auto object-contain shrink-0" onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Manajemen Pengguna &amp; Hak Akses
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Kelola akun pengguna, wewenang RBAC seluruh divisi, status keamanan, dan pemantauan sesi aktif.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <!-- Link ke Audit Logs (Area 2) -->
                <a href="{{ route('admin.activity-logs.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl border border-slate-300 transition">
                    <svg class="w-4 h-4 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Log Aktivitas</span>
                </a>

                <!-- Link ke Backups (Area 1) -->
                <a href="{{ route('admin.backups.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl border border-slate-300 transition">
                    <svg class="w-4 h-4 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7zm0 5h16" />
                    </svg>
                    <span>Cadangan Data</span>
                </a>

                <button type="button" 
                        @click="addModalOpen = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0E527B] hover:bg-[#07334F] text-white font-bold text-xs rounded-xl shadow-xs transition duration-150 cursor-pointer">
                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Staf Divisi</span>
                </button>
            </div>
        </div>

        <!-- 2. Ringkasan Status Akun & Keamanan (Baris 1) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
            <!-- Total Staf -->
            <a href="{{ route('admin.users.index') }}" 
               class="p-4 bg-white border rounded-2xl transition shadow-xs hover:border-[#0E527B] {{ empty($roleFilter) && empty($statusFilter) && empty($presenceFilter) ? 'border-[#0E527B] ring-2 ring-sky-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-slate-500 block uppercase tracking-wider">Total Staf</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block font-mono">{{ $stats['total'] }}</span>
                <span class="text-xs text-[#0E527B] font-bold">Semua Akun Sistem</span>
            </a>

            <!-- Sedang Online Sekarang (Real-time, tanpa bulat kecil/ping) -->
            <a href="{{ route('admin.users.index', ['presence' => 'online']) }}" 
               class="p-4 bg-white border rounded-2xl transition shadow-xs hover:border-[#0E527B] {{ ($presenceFilter ?? '') === 'online' ? 'border-[#0E527B] ring-2 ring-sky-100' : 'border-slate-200' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 block uppercase tracking-wider">Sedang Online</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">LIVE</span>
                </div>
                <span class="text-2xl font-black text-kp-blue-700 mt-1 block font-mono" x-text="onlineCount">{{ $stats['online'] }}</span>
                <span class="text-xs text-kp-blue-700 font-bold">Aktif di Aplikasi</span>
            </a>

            <!-- Status: Active -->
            <a href="{{ route('admin.users.index', ['status' => 'active']) }}" 
               class="p-4 bg-white border rounded-2xl transition shadow-xs hover:border-[#0E527B] {{ ($statusFilter ?? '') === 'active' ? 'border-[#0E527B] ring-2 ring-sky-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-slate-500 block uppercase tracking-wider">Akun Aktif</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block font-mono">{{ $stats['active'] }}</span>
                <span class="text-xs text-slate-600 font-bold">Akses Penuh Normal</span>
            </a>

            <!-- Status: Review -->
            <a href="{{ route('admin.users.index', ['status' => 'review']) }}" 
               class="p-4 bg-white border rounded-2xl transition shadow-xs hover:border-amber-400 {{ ($statusFilter ?? '') === 'review' ? 'border-amber-600 ring-2 ring-amber-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-slate-500 block uppercase tracking-wider">Dalam Review</span>
                <span class="text-2xl font-black text-amber-800 mt-1 block font-mono">{{ $stats['review'] }}</span>
                <span class="text-xs text-amber-700 font-bold">Perlu Verifikasi</span>
            </a>

            <!-- Status: Disable -->
            <a href="{{ route('admin.users.index', ['status' => 'disable']) }}" 
               class="p-4 bg-white border rounded-2xl transition shadow-xs hover:border-rose-400 {{ ($statusFilter ?? '') === 'disable' ? 'border-rose-600 ring-2 ring-rose-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-slate-500 block uppercase tracking-wider">Dinonaktifkan</span>
                <span class="text-2xl font-black text-rose-700 mt-1 block font-mono">{{ $stats['disable'] }}</span>
                <span class="text-xs text-rose-600 font-bold">Akses Ditolak</span>
            </a>
        </div>

        <!-- 2b. Komposisi Wewenang Divisi (Baris 2) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <!-- Divisi Iklan -->
            <a href="{{ route('admin.users.index', ['role' => 'iklan']) }}" 
               class="p-3.5 bg-white border rounded-xl transition shadow-xs hover:border-sky-400 {{ ($roleFilter ?? '') === 'iklan' ? 'border-sky-600 ring-2 ring-sky-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-slate-600 block">Iklan</span>
                <span class="text-xl font-black text-[#0E527B] mt-0.5 block font-mono">{{ $stats['iklan'] }}</span>
                <span class="text-[11px] text-slate-500 font-medium">Koran &amp; Online</span>
            </a>

            <!-- Divisi Sirkulasi -->
            <a href="{{ route('admin.users.index', ['role' => 'sirkulasi']) }}" 
               class="p-3.5 bg-white border rounded-xl transition shadow-xs hover:border-amber-400 {{ ($roleFilter ?? '') === 'sirkulasi' ? 'border-amber-600 ring-2 ring-amber-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-slate-600 block">Sirkulasi</span>
                <span class="text-xl font-black text-amber-800 mt-0.5 block font-mono">{{ $stats['sirkulasi'] }}</span>
                <span class="text-[11px] text-slate-500 font-medium">Distribusi &amp; Agen</span>
            </a>

            <!-- Divisi Faktur -->
            <a href="{{ route('admin.users.index', ['role' => 'faktur']) }}" 
               class="p-3.5 bg-white border rounded-xl transition shadow-xs hover:border-indigo-400 {{ in_array(($roleFilter ?? ''), ['faktur', 'billing']) ? 'border-indigo-600 ring-2 ring-indigo-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-slate-600 block">Faktur</span>
                <span class="text-xl font-black text-indigo-800 mt-0.5 block font-mono">{{ $stats['faktur'] ?? 0 }}</span>
                <span class="text-[11px] text-slate-500 font-medium">Penagihan Piutang</span>
            </a>

            <!-- Petugas Inkaso (Kolektor Lapangan) -->
            <a href="{{ route('admin.users.index', ['role' => 'petugas_inkaso']) }}" 
               class="p-3.5 bg-white border rounded-xl transition shadow-xs hover:border-amber-500 {{ in_array(($roleFilter ?? ''), ['petugas_inkaso', 'inkaso']) ? 'border-[#0E527B] ring-2 ring-sky-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-slate-600 block">Petugas Inkaso</span>
                <span class="text-xl font-black text-[#0E527B] mt-0.5 block font-mono">{{ $stats['petugas_inkaso'] ?? 0 }}</span>
                <span class="text-[11px] text-[#0E527B] font-bold">Kolektor Lapangan</span>
            </a>

            <!-- Divisi Kasir -->
            <a href="{{ route('admin.users.index', ['role' => 'kasir']) }}" 
               class="p-3.5 bg-white border rounded-xl transition shadow-xs hover:border-teal-400 {{ ($roleFilter ?? '') === 'kasir' ? 'border-teal-600 ring-2 ring-teal-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-slate-600 block">Kasir</span>
                <span class="text-xl font-black text-teal-800 mt-0.5 block font-mono">{{ $stats['kasir'] ?? 0 }}</span>
                <span class="text-[11px] text-slate-500 font-medium">Kas &amp; Setoran</span>
            </a>

            <!-- Divisi Anggaran / Keuangan -->
            <a href="{{ route('admin.users.index', ['role' => 'anggaran']) }}" 
               class="p-3.5 bg-white border rounded-xl transition shadow-xs hover:border-[#0E527B] {{ in_array(($roleFilter ?? ''), ['anggaran', 'keuangan']) ? 'border-[#0E527B] ring-2 ring-sky-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-slate-600 block">Anggaran &amp; Keu</span>
                <span class="text-xl font-black text-slate-900 mt-0.5 block font-mono">{{ $stats['anggaran'] ?? 0 }}</span>
                <span class="text-[11px] text-slate-500 font-medium">RAB &amp; Kasbon</span>
            </a>
        </div>

        <!-- 3. Tabel Data Pengguna & Filter Bar -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <!-- Filter Bar -->
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/70 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
                    <div class="relative flex-1 min-w-[200px] max-w-xs">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-black/60">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari nama atau email..." 
                               class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-black placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 font-medium" />
                    </div>

                    <!-- Filter Divisi -->
                    <select name="role" 
                            onchange="this.form.submit()" 
                            class="py-2 pl-3 pr-8 bg-white border border-slate-300 rounded-xl text-xs text-black font-semibold focus:outline-none focus:border-kp-blue-600">
                        <option value="all">Semua Divisi</option>
                        @foreach($rolesList as $key => $label)
                            <option value="{{ $key }}" {{ ($roleFilter ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Status Akun -->
                    <select name="status" 
                            onchange="this.form.submit()" 
                            class="py-2 pl-3 pr-8 bg-white border border-slate-300 rounded-xl text-xs text-black font-semibold focus:outline-none focus:border-kp-blue-600">
                        <option value="all">Semua Status</option>
                        <option value="active" {{ ($statusFilter ?? '') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="review" {{ ($statusFilter ?? '') === 'review' ? 'selected' : '' }}>Perlu Verifikasi</option>
                        <option value="disable" {{ ($statusFilter ?? '') === 'disable' ? 'selected' : '' }}>Nonaktif</option>
                    </select>

                    <!-- Filter Kehadiran -->
                    <select name="presence" 
                            onchange="this.form.submit()" 
                            class="py-2 pl-3 pr-8 bg-white border border-slate-300 rounded-xl text-xs text-black font-semibold focus:outline-none focus:border-kp-blue-600">
                        <option value="all">Semua Kehadiran</option>
                        <option value="online" {{ ($presenceFilter ?? '') === 'online' ? 'selected' : '' }}>Sedang Online</option>
                        <option value="offline" {{ ($presenceFilter ?? '') === 'offline' ? 'selected' : '' }}>Offline</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-black hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition cursor-pointer">
                        Filter
                    </button>

                    @if($search || ($roleFilter && $roleFilter !== 'all') || ($statusFilter && $statusFilter !== 'all') || ($presenceFilter && $presenceFilter !== 'all'))
                        <a href="{{ route('admin.users.index') }}" class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-black font-bold text-xs rounded-xl transition">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="text-xs text-black font-semibold">
                    Total: <span class="font-extrabold text-black">{{ $users->total() }}</span> staf terdaftar
                </div>
            </div>

            <!-- Tabel Data: Format Standar Korporat 1 Data 1 Kolom Bersih Tanpa Bulatan Mengambang -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-100/90 text-xs font-extrabold text-slate-800 uppercase tracking-wider whitespace-nowrap">
                            <th class="py-3.5 px-4">Nama Staf</th>
                            <th class="py-3.5 px-4">Alamat Email</th>
                            <th class="py-3.5 px-4">Divisi &amp; Wewenang</th>
                            <th class="py-3.5 px-4 text-center">Status Akun</th>
                            <th class="py-3.5 px-4 text-center">Kehadiran</th>
                            <th class="py-3.5 px-4">Login Terakhir</th>
                            <th class="py-3.5 px-4">Alamat IP</th>
                            <th class="py-3.5 px-4">Perangkat &amp; Browser</th>
                            <th class="py-3.5 px-4 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $u)
                            <tr class="hover:bg-slate-50/90 transition-colors">
                                <!-- 1. Nama Staf & Avatar Inisial -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-[#0E527B] text-white font-black text-sm flex items-center justify-center shrink-0 shadow-xs">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 text-sm">{{ $u->name }}</div>
                                            @if($u->id === auth()->id())
                                                <span class="inline-block mt-0.5 px-2 py-0.5 text-[10px] font-bold rounded-md bg-sky-100 text-[#0E527B] border border-sky-300">
                                                    Akun Anda Saat Ini
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. Alamat Email -->
                                <td class="py-3.5 px-4 text-slate-800 font-mono font-medium whitespace-nowrap">
                                    {{ $u->email }}
                                </td>

                                <!-- 3. Divisi & Wewenang RBAC -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @php
                                        $r = strtolower($u->role);
                                        $badgeColor = match($r) {
                                            'administrator', 'admin' => 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200 font-bold',
                                            'petugas_inkaso', 'inkaso' => 'bg-slate-100 text-[#0E527B] border-slate-300 font-bold',
                                            default => 'bg-slate-100 text-slate-700 border-slate-300',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold border {{ $badgeColor }}">
                                        {{ $u->role_label }}
                                    </span>
                                </td>

                                <!-- 4. Status Akun (Clean Badge Tanpa Bulat Kecil) -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @php
                                        $status = $u->status ?? 'active';
                                        $statusBadge = match($status) {
                                            'active' => 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200 hover:bg-kp-blue-100',
                                            'review' => 'bg-amber-50 text-amber-800 border-amber-300 hover:bg-amber-100',
                                            'disable' => 'bg-slate-100 text-slate-700 border-slate-300 hover:bg-slate-200',
                                            default => 'bg-slate-100 text-slate-800 border-slate-300',
                                        };
                                    @endphp
                                    <div class="inline-flex flex-col items-center">
                                        <button type="button" 
                                                @click="selectedUser = { id: '{{ $u->id }}', name: '{{ addslashes($u->name) }}', status: '{{ $status }}' }; statusModalOpen = true;"
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border {{ $statusBadge }} transition cursor-pointer select-none"
                                                title="Klik untuk mengubah status akun">
                                            @if($status === 'active')
                                                <span>Active</span>
                                            @elseif($status === 'review')
                                                <span>Review</span>
                                            @else
                                                <span>Disabled</span>
                                            @endif
                                        </button>

                                        @if($u->isLocked())
                                            <span class="mt-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-900 text-white">
                                                Terkunci 15m
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- 5. Kehadiran Sesi (Solid Badge Tanpa Animasi Ping Bulat Kecil) -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <template x-if="presenceMap[{{ $u->id }}]">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold border transition"
                                                  :class="presenceMap[{{ $u->id }}]?.is_online ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-slate-100 text-slate-600 border-slate-200'"
                                                  x-text="presenceMap[{{ $u->id }}]?.is_online ? 'Online' : 'Offline'">
                                            </span>
                                            <span class="text-[10px] text-slate-500 font-medium mt-0.5" x-text="presenceMap[{{ $u->id }}]?.last_seen_text"></span>
                                        </div>
                                    </template>
                                    <template x-if="!presenceMap[{{ $u->id }}]">
                                        <div class="inline-flex flex-col items-center">
                                            @if($u->isOnline())
                                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold border bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200">
                                                    Online
                                                </span>
                                                <span class="text-[10px] text-slate-500 font-medium mt-0.5">Aktif di web/mobile</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold border bg-slate-100 text-slate-600 border-slate-200">
                                                    Offline
                                                </span>
                                                <span class="text-[10px] text-slate-500 font-medium mt-0.5">{{ $u->last_seen_text }}</span>
                                            @endif
                                        </div>
                                    </template>
                                </td>

                                <!-- 6. Login Terakhir (1 Kolom Mandiri) -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-mono text-xs font-semibold text-slate-900" 
                                         x-text="presenceMap[{{ $u->id }}]?.last_login ? presenceMap[{{ $u->id }}]?.last_login : '{{ $u->formatted_last_login }}'">
                                        {{ $u->formatted_last_login }}
                                    </div>
                                </td>

                                <!-- 7. Alamat IP (1 Kolom Mandiri) -->
                                <td class="py-3.5 px-4 whitespace-nowrap font-mono text-xs font-bold text-slate-700">
                                    <span x-text="presenceMap[{{ $u->id }}]?.last_login_ip ? presenceMap[{{ $u->id }}]?.last_login_ip : '{{ $u->resolved_last_login_ip }}'">{{ $u->resolved_last_login_ip }}</span>
                                </td>

                                <!-- 8. Perangkat & Browser (1 Kolom Mandiri) -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="text-xs font-medium text-slate-800"
                                         x-text="(presenceMap[{{ $u->id }}]?.last_login_device || '{{ $u->resolved_last_login_device }}') !== '-' ? (presenceMap[{{ $u->id }}]?.last_login_device || '{{ $u->resolved_last_login_device }}') : '-'">
                                        {{ $u->resolved_last_login_device !== '-' ? $u->resolved_last_login_device : '-' }}
                                    </div>
                                    <div class="text-[11px] font-bold text-[#0E527B]"
                                         x-show="(presenceMap[{{ $u->id }}]?.last_login_browser || '{{ $u->resolved_last_login_browser }}') !== '-'"
                                         x-text="presenceMap[{{ $u->id }}]?.last_login_browser ? presenceMap[{{ $u->id }}]?.last_login_browser : '{{ $u->resolved_last_login_browser }}'">
                                        {{ $u->resolved_last_login_browser !== '-' ? $u->resolved_last_login_browser : '' }}
                                    </div>
                                </td>

                                <!-- 9. Tombol Tindakan -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 justify-center">
                                        <!-- Edit Button -->
                                        <button type="button" 
                                                @click="editUser = { id: '{{ $u->id }}', name: '{{ addslashes($u->name) }}', email: '{{ $u->email }}', role: '{{ $u->role }}', status: '{{ $u->status ?? 'active' }}' }; editModalOpen = true;"
                                                class="px-2.5 py-1.5 rounded-lg border border-slate-300 text-slate-800 hover:text-[#0E527B] hover:bg-sky-50 hover:border-[#0E527B] font-bold transition cursor-pointer flex items-center gap-1 text-xs"
                                                title="Edit Data Staf">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                            <span>Edit</span>
                                        </button>

                                        <!-- Tombol Putuskan Sesi (Force Logout) jika staf sedang online -->
                                        @if($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.force-logout', $u->id) }}" 
                                                  x-show="presenceMap[{{ $u->id }}]?.is_online ?? {{ $u->isOnline() ? 'true' : 'false' }}"
                                                  onsubmit="return confirmSubmit(this, event, {
                                                      title: 'Putuskan Sesi Staf?',
                                                      text: 'Apakah Anda yakin ingin memutuskan sesi aktif staf <strong>{{ addslashes($u->name) }}</strong>? Pengguna akan langsung dikeluarkan ke halaman login.',
                                                      theme: 'amber',
                                                      icon: 'cancel',
                                                      confirmText: 'Ya, Putuskan Sesi'
                                                  });">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-2.5 py-1.5 rounded-lg border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold transition cursor-pointer flex items-center gap-1 text-xs"
                                                        title="Keluarkan staf dari sesi aktif aplikasi">
                                                    <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                                    </svg>
                                                    <span>Putuskan</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Delete / Disable Button -->
                                        @if($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" 
                                                  onsubmit="return confirmSubmit(this, event, {
                                                      title: 'Hapus / Nonaktifkan Akun?',
                                                      target: '{{ addslashes($u->name) }}',
                                                      text: 'Peringatan Keamanan: Jika akun staf <strong>{{ addslashes($u->name) }}</strong> memiliki riwayat transaksi finansial, sistem akan otomatis mengalihkannya menjadi status DISABLE (nonaktif) demi integritas data pers. Lanjutkan?',
                                                      theme: 'rose',
                                                      icon: 'trash',
                                                      confirmText: 'Ya, Lanjutkan'
                                                  });">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="px-2.5 py-1.5 rounded-lg border border-slate-300 text-rose-700 hover:bg-rose-50 hover:border-rose-400 font-bold transition cursor-pointer flex items-center gap-1 text-xs"
                                                        title="Hapus / Nonaktifkan Akun">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-10 text-center text-slate-600">
                                    <p class="font-bold text-sm">Tidak ada data staf yang sesuai kriteria pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50/70">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- 4. MODAL UBAH STATUS AKUN SECARA CEPAT (Active, Review, Disable) -->
        <div x-cloak x-show="statusModalOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="statusModalOpen = false"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-white rounded-2xl border border-slate-300 shadow-2xl p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-sm font-extrabold text-black">Ubah Status Akun Staf</h3>
                            <p class="text-xs text-black font-medium mt-0.5" x-text="'Staf: ' + selectedUser.name"></p>
                        </div>
                        <button type="button" @click="statusModalOpen = false" class="text-black hover:text-slate-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="'{{ url('admin/users') }}/' + selectedUser.id + '/status'" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">Pilih Status Baru:</label>
                            
                            <label class="flex items-start gap-3 p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition"
                                   :class="selectedUser.status === 'active' ? 'border-[#0E527B] bg-sky-50/40 ring-1 ring-[#0E527B]' : 'border-slate-300'">
                                <input type="radio" name="status" value="active" x-model="selectedUser.status" class="mt-0.5 text-[#0E527B]">
                                <div>
                                    <div class="font-bold text-xs text-slate-900">Active (Aktif Normal)</div>
                                    <div class="text-[11px] text-slate-600 font-medium">Staf dapat login dan mengakses seluruh modul sesuai divisi.</div>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition"
                                   :class="selectedUser.status === 'review' ? 'border-amber-600 bg-amber-50/40 ring-1 ring-amber-500' : 'border-slate-300'">
                                <input type="radio" name="status" value="review" x-model="selectedUser.status" class="mt-0.5 text-amber-600">
                                <div>
                                    <div class="font-bold text-xs text-slate-900">Review (Dalam Pengawasan/Audit)</div>
                                    <div class="text-[11px] text-slate-600 font-medium">Akun ditandai untuk pengawasan audit transaksi tidak wajar.</div>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition"
                                   :class="selectedUser.status === 'disable' ? 'border-rose-600 bg-rose-50/40 ring-1 ring-rose-500' : 'border-slate-300'">
                                <input type="radio" name="status" value="disable" x-model="selectedUser.status" class="mt-0.5 text-rose-600">
                                <div>
                                    <div class="font-bold text-xs text-slate-900">Disable (Nonaktifkan Total)</div>
                                    <div class="text-[11px] text-slate-600 font-medium">Akses login ditolak seketika dan sesi aktif diputus. Riwayat audit tetap aman.</div>
                                </div>
                            </label>
                        </div>

                        <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                            <button type="button" @click="statusModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-100">
                                Batalkan
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-xs font-bold text-white shadow-xs">
                                Terapkan Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 5. MODAL TAMBAH ANGGOTA TIM -->
        <div x-cloak x-show="addModalOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="addModalOpen = false"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-white rounded-2xl border border-slate-300 shadow-2xl p-6 sm:p-7 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-base font-extrabold text-black">Tambah Staf Divisi Baru</h3>
                            <p class="text-xs text-black font-medium">Buat akun staf dengan standar keamanan kata sandi industri pers</p>
                        </div>
                        <button type="button" @click="addModalOpen = false" class="text-black hover:text-slate-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-black mb-1">Nama Lengkap Staf</label>
                            <input type="text" name="name" required placeholder="Contoh: Asep Sunandar" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-black font-semibold focus:outline-none focus:border-kp-blue-600" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-black mb-1">Alamat Email Resmi (Internal)</label>
                            <input type="email" name="email" required placeholder="staf@kabarpriangan.co.id" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-black font-semibold focus:outline-none focus:border-kp-blue-600" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Divisi &amp; Hak Akses (RBAC)</label>
                                <select name="role" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:outline-none focus:border-[#0E527B]">
                                    @foreach($rolesList as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Status Awal Akun</label>
                                <select name="status" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:outline-none focus:border-[#0E527B]">
                                    <option value="active">Active (Aktif)</option>
                                    <option value="review">Review (Audit)</option>
                                    <option value="disable">Disable (Nonaktif)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Kata Sandi (Password Standar Industri)
                            </label>
                            <input type="password" 
                                   name="password" 
                                   required 
                                   minlength="8" 
                                   placeholder="Minimal 8 karakter (huruf, angka, simbol)" 
                                   class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-900 font-semibold focus:outline-none focus:border-[#0E527B]" />
                            <p class="text-[11px] text-slate-600 font-medium mt-1">
                                Rekomendasi pola kata sandi aman: <span class="font-mono font-bold text-[#0E527B]">1r12skabar17riangan</span>
                            </p>
                        </div>

                        <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                            <button type="button" @click="addModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-100">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-xs font-bold text-white shadow-xs">
                                Simpan Akun Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 6. MODAL EDIT ANGGOTA TIM -->
        <div x-cloak x-show="editModalOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="editModalOpen = false"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-white rounded-2xl border border-slate-300 shadow-2xl p-6 sm:p-7 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-base font-extrabold text-black">Perbarui Data & Wewenang Staf</h3>
                            <p class="text-xs text-black font-medium">Ubah informasi identitas, divisi, status, atau kata sandi</p>
                        </div>
                        <button type="button" @click="editModalOpen = false" class="text-black hover:text-slate-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="'{{ url('admin/users') }}/' + editUser.id" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Staf</label>
                            <input type="text" name="name" x-model="editUser.name" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:outline-none focus:border-[#0E527B]" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                            <input type="email" name="email" x-model="editUser.email" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:outline-none focus:border-[#0E527B]" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Divisi &amp; Hak Akses</label>
                                <select name="role" x-model="editUser.role" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:outline-none focus:border-[#0E527B]">
                                    @foreach($rolesList as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Status Akun</label>
                                <select name="status" x-model="editUser.status" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:outline-none focus:border-[#0E527B]">
                                    <option value="active">Active (Aktif)</option>
                                    <option value="review">Review (Audit)</option>
                                    <option value="disable">Disable (Nonaktif)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Password Baru (Kosongkan jika tidak diubah)
                            </label>
                            <input type="password" 
                                   name="password" 
                                   minlength="8" 
                                   placeholder="Minimal 8 karakter kombinasi jika ingin ganti" 
                                   class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:outline-none focus:border-[#0E527B]" />
                        </div>

                        <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-100">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-xs font-bold text-white shadow-xs">
                                Perbarui Akun Staf
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Script Logika Alpine.js & Live Real-Time Polling Status Kehadiran Staf -->
    <script>
        function userManagement(config) {
            return {
                addModalOpen: false,
                editModalOpen: false,
                statusModalOpen: false,
                selectedUser: { id: '', name: '', status: 'active' },
                editUser: { id: '', name: '', email: '', role: 'iklan', status: 'active' },
                onlineCount: parseInt(config.initialOnlineCount) || 0,
                presenceMap: {},
                pollTimer: null,

                init() {
                    this.fetchPresence();
                    // Live polling status online setiap 5 detik (Real-time monitoring)
                    this.pollTimer = setInterval(() => {
                        this.fetchPresence();
                    }, 5000);
                },

                fetchPresence() {
                    if (!config.pollUrl) return;
                    fetch(config.pollUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.users) {
                            this.presenceMap = data.users;
                            if (typeof data.online_count !== 'undefined') {
                                this.onlineCount = data.online_count;
                            }
                        }
                    })
                    .catch(() => {});
                }
            };
        }
    </script>

    <!-- Feedback Notifikasi -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.AppAlert) {
                    AppAlert.success({ title: 'Berhasil', message: "{{ session('success') }}" });
                }
            });
        </script>
    @endif
    @if(session('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.AppAlert) {
                    AppAlert.warning({ title: 'Perhatian', message: "{{ session('warning') }}" });
                }
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.AppAlert) {
                    AppAlert.error({ title: 'Gagal', message: "{{ session('error') }}" });
                }
            });
        </script>
    @endif
</x-app-layout>
