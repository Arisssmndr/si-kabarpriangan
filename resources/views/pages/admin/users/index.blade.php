<x-app-layout>
    <div x-data="{ 
        addModalOpen: false, 
        editModalOpen: false, 
        editUser: { id: '', name: '', email: '', role: 'iklan' } 
    }" class="space-y-6">

        <!-- 1. Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Manajemen Pengguna & Divisi
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Akses Internal
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Kelola akun karyawan dan wewenang operasional lintas 5 divisi Kabar Priangan
                    </p>
                </div>
            </div>

            <button type="button" 
                    @click="addModalOpen = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs transition duration-150 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Anggota Tim</span>
            </button>
        </div>

        <!-- 2. Ringkasan Divisi (Stats Cards) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="{{ route('admin.users.index') }}" 
               class="p-3.5 bg-white border rounded-xl transition shadow-xs hover:border-kp-blue-300 {{ empty($roleFilter) || $roleFilter === 'all' ? 'border-kp-blue-500 ring-2 ring-kp-blue-100' : 'border-slate-200' }}">
                <span class="text-[11px] font-semibold text-slate-500 block">Total Karyawan</span>
                <span class="text-xl font-extrabold text-slate-900 mt-0.5 block">{{ $stats['total'] }}</span>
                <span class="text-[10px] text-kp-blue-600 font-medium">Semua Divisi</span>
            </a>

            <a href="{{ route('admin.users.index', ['role' => 'iklan']) }}" 
               class="p-3.5 bg-white border rounded-xl transition shadow-xs hover:border-blue-300 {{ $roleFilter === 'iklan' ? 'border-blue-500 ring-2 ring-blue-100' : 'border-slate-200' }}">
                <span class="text-[11px] font-semibold text-slate-500 block">Divisi Iklan</span>
                <span class="text-xl font-extrabold text-blue-700 mt-0.5 block">{{ $stats['iklan'] }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Koran & Online</span>
            </a>

            <a href="{{ route('admin.users.index', ['role' => 'keuangan']) }}" 
               class="p-3.5 bg-white border rounded-xl transition shadow-xs hover:border-emerald-300 {{ $roleFilter === 'keuangan' ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-slate-200' }}">
                <span class="text-[11px] font-semibold text-slate-500 block">Divisi Keuangan</span>
                <span class="text-xl font-extrabold text-emerald-700 mt-0.5 block">{{ $stats['keuangan'] }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Faktur & Inkaso</span>
            </a>

            <a href="{{ route('admin.users.index', ['role' => 'accounting']) }}" 
               class="p-3.5 bg-white border rounded-xl transition shadow-xs hover:border-purple-300 {{ $roleFilter === 'accounting' ? 'border-purple-500 ring-2 ring-purple-100' : 'border-slate-200' }}">
                <span class="text-[11px] font-semibold text-slate-500 block">Accounting</span>
                <span class="text-xl font-extrabold text-purple-700 mt-0.5 block">{{ $stats['accounting'] }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Buku Besar & Pajak</span>
            </a>

            <a href="{{ route('admin.users.index', ['role' => 'sirkulasi']) }}" 
               class="p-3.5 bg-white border rounded-xl transition shadow-xs hover:border-amber-300 {{ $roleFilter === 'sirkulasi' ? 'border-amber-500 ring-2 ring-amber-100' : 'border-slate-200' }}">
                <span class="text-[11px] font-semibold text-slate-500 block">Sirkulasi</span>
                <span class="text-xl font-extrabold text-amber-700 mt-0.5 block">{{ $stats['sirkulasi'] }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Distribusi & Agen</span>
            </a>

            <a href="{{ route('admin.users.index', ['role' => 'kasir']) }}" 
               class="p-3.5 bg-white border rounded-xl transition shadow-xs hover:border-teal-300 {{ $roleFilter === 'kasir' ? 'border-teal-500 ring-2 ring-teal-100' : 'border-slate-200' }}">
                <span class="text-[11px] font-semibold text-slate-500 block">Kasir</span>
                <span class="text-xl font-extrabold text-teal-700 mt-0.5 block">{{ $stats['kasir'] }}</span>
                <span class="text-[10px] text-slate-400 font-medium">Penerimaan Tunai</span>
            </a>
        </div>

        <!-- 3. Tabel Data Pengguna & Filter Bar -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <!-- Filter Bar -->
            <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
                    <div class="relative flex-1 min-w-[200px] max-w-sm">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari nama atau email staf..." 
                               class="w-full pl-9 pr-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600" />
                    </div>

                    <select name="role" 
                            onchange="this.form.submit()" 
                            class="py-1.5 pl-3 pr-8 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                        <option value="all">Semua Divisi</option>
                        @foreach($rolesList as $key => $label)
                            <option value="{{ $key }}" {{ $roleFilter === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-lg transition cursor-pointer">
                        Filter
                    </button>

                    @if($search || ($roleFilter && $roleFilter !== 'all'))
                        <a href="{{ route('admin.users.index') }}" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium text-xs rounded-lg transition">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="text-xs text-slate-500 font-medium">
                    Menampilkan <span class="font-bold text-slate-800">{{ $users->total() }}</span> staf
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-100/70 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <th class="py-3 px-4">Nama Staf</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Divisi / Wewenang</th>
                            <th class="py-3 px-4">Terdaftar</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($users as $u)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Nama & Avatar -->
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-kp-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                            @if($u->id === auth()->id())
                                                <span class="inline-block mt-0.5 px-1.5 py-0.2 text-[9px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Akun Anda Saat Ini
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                                    {{ $u->email }}
                                </td>

                                <!-- Divisi Badge -->
                                <td class="py-3 px-4">
                                    @php
                                        $r = strtolower($u->role);
                                        $badgeColor = match($r) {
                                            'administrator', 'admin' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'iklan' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'keuangan', 'admin_keuangan' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'accounting' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'sirkulasi' => 'bg-amber-50 text-amber-800 border-amber-200',
                                            'kasir' => 'bg-teal-50 text-teal-700 border-teal-200',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold border {{ $badgeColor }}">
                                        {{ $u->role_label }}
                                    </span>
                                </td>

                                <!-- Tanggal Terdaftar -->
                                <td class="py-3 px-4 text-slate-500 font-medium">
                                    {{ $u->created_at ? $u->created_at->translatedFormat('d M Y') : '-' }}
                                </td>

                                <!-- Tombol Aksi -->
                                <td class="py-3 px-4 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Edit Button -->
                                        <button type="button" 
                                                @click="editUser = { id: '{{ $u->id }}', name: '{{ addslashes($u->name) }}', email: '{{ $u->email }}', role: '{{ $u->role }}' }; editModalOpen = true;"
                                                class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-kp-blue-600 hover:bg-kp-blue-50 hover:border-kp-blue-200 transition cursor-pointer"
                                                title="Edit Data Staf">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        @if($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun staf {{ $u->name }}? Tindakan ini tidak dapat dibatalkan.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition cursor-pointer"
                                                        title="Hapus Staf">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    <p class="font-medium text-xs">Tidak ada data anggota staf ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- 4. MODAL TAMBAH ANGGOTA TIM -->
        <div x-cloak x-show="addModalOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="addModalOpen = false"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Tambah Anggota Tim Baru</h3>
                            <p class="text-[11px] text-slate-500">Buat akun staf dan tentukan divisi akses operasional</p>
                        </div>
                        <button type="button" @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-3.5">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" required placeholder="Contoh: Asep Sunandar" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email (Internal)</label>
                            <input type="email" name="email" required placeholder="staf@kabarpriangan.co.id" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Divisi & Hak Akses</label>
                            <select name="role" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600">
                                @foreach($rolesList as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Password Baru</label>
                            <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600" />
                        </div>

                        <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                            <button type="button" @click="addModalOpen = false" class="px-3.5 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-xs font-bold text-white shadow-xs">
                                Simpan Akun
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 5. MODAL EDIT ANGGOTA TIM -->
        <div x-cloak x-show="editModalOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="editModalOpen = false"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Perbarui Data Staf</h3>
                            <p class="text-[11px] text-slate-500">Ubah informasi staf atau pindahkan wewenang divisi</p>
                        </div>
                        <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="'{{ url('admin/users') }}/' + editUser.id" method="POST" class="space-y-3.5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" x-model="editUser.name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                            <input type="email" name="email" x-model="editUser.email" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Divisi & Hak Akses</label>
                            <select name="role" x-model="editUser.role" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600">
                                @foreach($rolesList as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Password Baru (Opsional)</label>
                            <input type="password" name="password" minlength="6" placeholder="Kosongkan jika tidak ingin mengubah password" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600" />
                        </div>

                        <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                            <button type="button" @click="editModalOpen = false" class="px-3.5 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-xs font-bold text-white shadow-xs">
                                Perbarui Akun
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
