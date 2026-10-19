<x-app-layout>
    <div x-data="{ 
        addModalOpen: false, 
        editModalOpen: false, 
        statusModalOpen: false,
        selectedUser: { id: '', name: '', status: 'active' },
        editUser: { id: '', name: '', email: '', role: 'iklan', status: 'active' } 
    }" class="space-y-6">

        <!-- 1. Header Halaman & Navigasi Cepat Pusat Keamanan Admin -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl font-extrabold text-black tracking-tight">
                            Manajemen Pengguna & Hak Akses Divisi
                        </h1>
                        <span class="px-2 py-0.5 text-[11px] font-bold uppercase rounded-md bg-kp-blue-100 text-kp-blue-800 border border-kp-blue-300">
                            Area 3 RBAC
                        </span>
                    </div>
                    <p class="text-xs text-black font-medium mt-1">
                        Kendali otorisasi akun staf pers, pemantauan status (Active, Review, Disable), dan proteksi login sesuai arahan pembimbing.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <!-- Link ke Audit Logs (Area 2) -->
                <a href="{{ route('admin.activity-logs.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-black font-bold text-xs rounded-xl border border-slate-300 transition">
                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Audit Log Keamanan</span>
                </a>

                <!-- Link ke Backups (Area 1) -->
                <a href="{{ route('admin.backups.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-black font-bold text-xs rounded-xl border border-slate-300 transition">
                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7zm0 5h16" />
                    </svg>
                    <span>Cadangan Data (Backup)</span>
                </a>

                <button type="button" 
                        @click="addModalOpen = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition duration-150 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Staf Divisi</span>
                </button>
            </div>
        </div>

        <!-- 2. Ringkasan Status Akun & Statistik Divisi -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
            <!-- Total Staf -->
            <a href="{{ route('admin.users.index') }}" 
               class="p-4 bg-white border rounded-2xl transition shadow-xs hover:border-kp-blue-400 {{ empty($roleFilter) && empty($statusFilter) ? 'border-kp-blue-600 ring-2 ring-kp-blue-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-black block">Total Pengguna</span>
                <span class="text-2xl font-black text-black mt-1 block">{{ $stats['total'] }}</span>
                <span class="text-[11px] text-kp-blue-700 font-bold">Semua Akun</span>
            </a>

            <!-- Status: Active -->
            <a href="{{ route('admin.users.index', ['status' => 'active']) }}" 
               class="p-4 bg-white border rounded-2xl transition shadow-xs hover:border-emerald-400 {{ $statusFilter === 'active' ? 'border-emerald-600 ring-2 ring-emerald-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-black block">Akun Aktif (Active)</span>
                <span class="text-2xl font-black text-emerald-800 mt-1 block">{{ $stats['active'] }}</span>
                <span class="text-[11px] text-emerald-700 font-bold">Akses Penuh</span>
            </a>

            <!-- Status: Review -->
            <a href="{{ route('admin.users.index', ['status' => 'review']) }}" 
               class="p-4 bg-white border rounded-2xl transition shadow-xs hover:border-amber-400 {{ $statusFilter === 'review' ? 'border-amber-600 ring-2 ring-amber-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-black block">Dalam Review</span>
                <span class="text-2xl font-black text-amber-800 mt-1 block">{{ $stats['review'] }}</span>
                <span class="text-[11px] text-amber-700 font-bold">Perlu Verifikasi</span>
            </a>

            <!-- Status: Disable -->
            <a href="{{ route('admin.users.index', ['status' => 'disable']) }}" 
               class="p-4 bg-white border rounded-2xl transition shadow-xs hover:border-rose-400 {{ $statusFilter === 'disable' ? 'border-rose-600 ring-2 ring-rose-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-black block">Dinonaktifkan (Disable)</span>
                <span class="text-2xl font-black text-red-700 mt-1 block">{{ $stats['disable'] }}</span>
                <span class="text-[11px] text-red-600 font-bold">Akses Ditolak</span>
            </a>

            <!-- Divisi Iklan -->
            <a href="{{ route('admin.users.index', ['role' => 'iklan']) }}" 
               class="p-4 bg-white border rounded-2xl transition shadow-xs hover:border-blue-400 {{ $roleFilter === 'iklan' ? 'border-blue-600 ring-2 ring-blue-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-black block">Divisi Iklan</span>
                <span class="text-2xl font-black text-blue-800 mt-1 block">{{ $stats['iklan'] }}</span>
                <span class="text-[11px] text-black font-semibold">Koran, TV & Online</span>
            </a>

            <!-- Divisi Sirkulasi -->
            <a href="{{ route('admin.users.index', ['role' => 'sirkulasi']) }}" 
               class="p-4 bg-white border rounded-2xl transition shadow-xs hover:border-amber-400 {{ $roleFilter === 'sirkulasi' ? 'border-amber-600 ring-2 ring-amber-100' : 'border-slate-200' }}">
                <span class="text-xs font-bold text-black block">Divisi Sirkulasi</span>
                <span class="text-2xl font-black text-amber-800 mt-1 block">{{ $stats['sirkulasi'] }}</span>
                <span class="text-[11px] text-black font-semibold">Distribusi & Agen</span>
            </a>
        </div>

        <!-- 3. Tabel Data Pengguna & Filter Bar -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <!-- Filter Bar -->
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/70 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
                    <div class="relative flex-1 min-w-[220px] max-w-sm">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-black/60">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari nama atau email staf..." 
                               class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-black placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 font-medium" />
                    </div>

                    <select name="role" 
                            onchange="this.form.submit()" 
                            class="py-2 pl-3 pr-8 bg-white border border-slate-300 rounded-xl text-xs text-black font-semibold focus:outline-none focus:border-kp-blue-600">
                        <option value="all">Semua Divisi</option>
                        @foreach($rolesList as $key => $label)
                            <option value="{{ $key }}" {{ $roleFilter === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    <select name="status" 
                            onchange="this.form.submit()" 
                            class="py-2 pl-3 pr-8 bg-white border border-slate-300 rounded-xl text-xs text-black font-semibold focus:outline-none focus:border-kp-blue-600">
                        <option value="all">Semua Status</option>
                        <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>🟢 Active (Aktif)</option>
                        <option value="review" {{ $statusFilter === 'review' ? 'selected' : '' }}>🟡 Review (Audit)</option>
                        <option value="disable" {{ $statusFilter === 'disable' ? 'selected' : '' }}>🔴 Disable (Nonaktif)</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-black hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition cursor-pointer">
                        Filter Data
                    </button>

                    @if($search || ($roleFilter && $roleFilter !== 'all') || ($statusFilter && $statusFilter !== 'all'))
                        <a href="{{ route('admin.users.index') }}" class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-black font-bold text-xs rounded-xl transition">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="text-xs text-black font-semibold">
                    Total: <span class="font-extrabold text-black">{{ $users->total() }}</span> staf terdaftar
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-100/90 text-xs font-extrabold text-black uppercase tracking-wider">
                            <th class="py-3.5 px-4">Nama Staf</th>
                            <th class="py-3.5 px-4">Alamat Email</th>
                            <th class="py-3.5 px-4">Divisi & Wewenang</th>
                            <th class="py-3.5 px-4 text-center">Status Akun</th>
                            <th class="py-3.5 px-4">Jejak Keamanan</th>
                            <th class="py-3.5 px-4 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($users as $u)
                            <tr class="hover:bg-slate-50/90 transition-colors">
                                <!-- Nama & Avatar -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-kp-blue-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-xs">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-extrabold text-black text-sm">{{ $u->name }}</div>
                                            @if($u->id === auth()->id())
                                                <span class="inline-block mt-0.5 px-2 py-0.5 text-[10px] font-bold rounded-md bg-emerald-100 text-emerald-900 border border-emerald-300">
                                                    Akun Anda Saat Ini
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="py-3.5 px-4 text-black font-mono font-medium text-xs">
                                    {{ $u->email }}
                                </td>

                                <!-- Divisi Badge -->
                                <td class="py-3.5 px-4">
                                    @php
                                        $r = strtolower($u->role);
                                        $badgeColor = match($r) {
                                            'administrator', 'admin' => 'bg-rose-50 text-rose-800 border-rose-300',
                                            'iklan' => 'bg-blue-50 text-blue-800 border-blue-300',
                                            'sirkulasi' => 'bg-amber-50 text-amber-900 border-amber-300',
                                            'keuangan', 'admin_keuangan' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                                            'kasir' => 'bg-teal-50 text-teal-800 border-teal-300',
                                            'billing' => 'bg-indigo-50 text-indigo-800 border-indigo-300',
                                            'accounting' => 'bg-purple-50 text-purple-800 border-purple-300',
                                            default => 'bg-slate-100 text-black border-slate-300',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold border {{ $badgeColor }}">
                                        {{ $u->role_label }}
                                    </span>
                                </td>

                                <!-- Status Akun (Active, Review, Disable) -->
                                <td class="py-3.5 px-4 text-center">
                                    @php
                                        $status = $u->status ?? 'active';
                                        $statusBadge = match($status) {
                                            'active' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                                            'review' => 'bg-amber-100 text-amber-900 border-amber-300',
                                            'disable' => 'bg-red-100 text-red-900 border-red-300',
                                            default => 'bg-slate-100 text-black border-slate-300',
                                        };
                                    @endphp
                                    <div class="inline-flex flex-col items-center">
                                        <button type="button" 
                                                @click="selectedUser = { id: '{{ $u->id }}', name: '{{ addslashes($u->name) }}', status: '{{ $status }}' }; statusModalOpen = true;"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold border {{ $statusBadge }} hover:opacity-85 transition cursor-pointer"
                                                title="Klik untuk mengubah status akun">
                                            @if($status === 'active')
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                <span>Active</span>
                                            @elseif($status === 'review')
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                <span>Review</span>
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                                <span>Disable</span>
                                            @endif
                                        </button>

                                        @if($u->isLocked())
                                            <span class="mt-1 px-1.5 py-0.5 rounded text-[9px] font-black bg-rose-600 text-white">
                                                Terkunci 15m
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Jejak Keamanan / Login Terakhir -->
                                <td class="py-3.5 px-4">
                                    <div class="space-y-0.5">
                                        <div class="text-[11px] font-bold text-black">
                                            {{ $u->last_login_at ? $u->last_login_at->translatedFormat('d M Y, H:i') : 'Belum pernah login' }}
                                        </div>
                                        <div class="text-[10px] font-mono text-black font-medium">
                                            IP: {{ $u->last_login_ip ?: '-' }}
                                        </div>
                                    </div>
                                </td>

                                <!-- Tombol Aksi -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <!-- Edit Button -->
                                        <button type="button" 
                                                @click="editUser = { id: '{{ $u->id }}', name: '{{ addslashes($u->name) }}', email: '{{ $u->email }}', role: '{{ $u->role }}', status: '{{ $u->status ?? 'active' }}' }; editModalOpen = true;"
                                                class="px-2.5 py-1.5 rounded-lg border border-slate-300 text-black hover:text-kp-blue-700 hover:bg-kp-blue-50 hover:border-kp-blue-400 font-bold transition cursor-pointer flex items-center gap-1"
                                                title="Edit Data Staf">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                            <span>Edit</span>
                                        </button>

                                        <!-- Delete / Disable Button (Zero Hard Delete Enforcement) -->
                                        @if($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" 
                                                  onsubmit="return confirm('Peringatan Keamanan: Jika akun staf ini memiliki riwayat transaksi finansial, sistem akan otomatis mengalihkannya menjadi status DISABLE (nonaktif) demi integritas data pers. Lanjutkan?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="px-2.5 py-1.5 rounded-lg border border-slate-300 text-red-700 hover:bg-red-50 hover:border-red-400 font-bold transition cursor-pointer flex items-center gap-1"
                                                        title="Hapus / Nonaktifkan Akun">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                    <span>Hapus/Disable</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-black">
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
                            <label class="block text-xs font-bold text-black">Pilih Status Baru:</label>
                            
                            <label class="flex items-start gap-3 p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition"
                                   :class="selectedUser.status === 'active' ? 'border-emerald-600 bg-emerald-50/40 ring-1 ring-emerald-500' : 'border-slate-300'">
                                <input type="radio" name="status" value="active" x-model="selectedUser.status" class="mt-0.5 text-emerald-600">
                                <div>
                                    <div class="font-extrabold text-xs text-black">🟢 Active (Aktif Normal)</div>
                                    <div class="text-[11px] text-black font-medium">Staf dapat login dan mengakses seluruh modul sesuai divisi.</div>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition"
                                   :class="selectedUser.status === 'review' ? 'border-amber-600 bg-amber-50/40 ring-1 ring-amber-500' : 'border-slate-300'">
                                <input type="radio" name="status" value="review" x-model="selectedUser.status" class="mt-0.5 text-amber-600">
                                <div>
                                    <div class="font-extrabold text-xs text-black">🟡 Review (Dalam Pengawasan/Audit)</div>
                                    <div class="text-[11px] text-black font-medium">Akun ditandai untuk pengawasan audit transaksi tidak wajar.</div>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition"
                                   :class="selectedUser.status === 'disable' ? 'border-red-600 bg-red-50/40 ring-1 ring-red-500' : 'border-slate-300'">
                                <input type="radio" name="status" value="disable" x-model="selectedUser.status" class="mt-0.5 text-red-600">
                                <div>
                                    <div class="font-extrabold text-xs text-black">🔴 Disable (Nonaktifkan Total)</div>
                                    <div class="text-[11px] text-black font-medium">Akses login ditolak seketika dan sesi aktif diputus. Riwayat audit tetap aman.</div>
                                </div>
                            </label>
                        </div>

                        <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                            <button type="button" @click="statusModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-black hover:bg-slate-100">
                                Batalkan
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-xs font-bold text-white shadow-xs">
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
                                <label class="block text-xs font-bold text-black mb-1">Divisi & Hak Akses (RBAC)</label>
                                <select name="role" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-black font-bold focus:outline-none focus:border-kp-blue-600">
                                    @foreach($rolesList as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-black mb-1">Status Awal Akun</label>
                                <select name="status" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-black font-bold focus:outline-none focus:border-kp-blue-600">
                                    <option value="active">🟢 Active (Aktif)</option>
                                    <option value="review">🟡 Review (Audit)</option>
                                    <option value="disable">🔴 Disable (Nonaktif)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-black mb-1">
                                Kata Sandi (Password Standar Industri)
                            </label>
                            <input type="password" 
                                   name="password" 
                                   required 
                                   minlength="8" 
                                   placeholder="Minimal 8 karakter (huruf, angka, simbol)" 
                                   class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-black font-semibold focus:outline-none focus:border-kp-blue-600" />
                            <p class="text-[11px] text-black font-medium mt-1">
                                💡 Contoh pola substitusi rumit yang dianjurkan pembimbing: <span class="font-mono font-bold text-kp-blue-700">1r12skabar17riangan</span>
                            </p>
                        </div>

                        <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                            <button type="button" @click="addModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-black hover:bg-slate-100">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-xs font-bold text-white shadow-sm">
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
                            <label class="block text-xs font-bold text-black mb-1">Nama Lengkap Staf</label>
                            <input type="text" name="name" x-model="editUser.name" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-black font-bold focus:outline-none focus:border-kp-blue-600" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-black mb-1">Alamat Email</label>
                            <input type="email" name="email" x-model="editUser.email" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-black font-bold focus:outline-none focus:border-kp-blue-600" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-black mb-1">Divisi & Hak Akses</label>
                                <select name="role" x-model="editUser.role" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-black font-bold focus:outline-none focus:border-kp-blue-600">
                                    @foreach($rolesList as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-black mb-1">Status Akun</label>
                                <select name="status" x-model="editUser.status" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-black font-bold focus:outline-none focus:border-kp-blue-600">
                                    <option value="active">🟢 Active (Aktif)</option>
                                    <option value="review">🟡 Review (Audit)</option>
                                    <option value="disable">🔴 Disable (Nonaktif)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-black mb-1">
                                Password Baru (Kosongkan jika tidak diubah)
                            </label>
                            <input type="password" 
                                   name="password" 
                                   minlength="8" 
                                   placeholder="Minimal 8 karakter kombinasi jika ingin ganti" 
                                   class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-black font-bold focus:outline-none focus:border-kp-blue-600" />
                        </div>

                        <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-black hover:bg-slate-100">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-xs font-bold text-white shadow-sm">
                                Perbarui Akun Staf
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

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
