<x-app-layout>
    <div class="space-y-5">

        @php
            $initials = collect(explode(' ', trim($user->name)))
                ->filter()
                ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                ->take(2)
                ->implode('') ?: 'KP';

            $employeeCode = 'NIP-' . str_pad((string) $user->id, 4, '0', STR_PAD_LEFT);
            $roleLabel = $user->role_label ?? 'Divisi Iklan';
            $statusLabel = $user->isActive() ? 'Aktif' : ucfirst($user->status ?? 'Aktif');
        @endphp

        <!-- 1. Kartu Identitas Pegawai (Standar ERP / Odoo Employee Header - Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-14 h-14 rounded-xl bg-kp-blue-600 text-white font-bold text-lg flex items-center justify-center shrink-0 shadow-xs">
                        {{ $initials }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                                {{ $user->name }}
                            </h1>
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                {{ $roleLabel }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                                {{ $employeeCode }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 truncate">
                            {{ $user->email }} &bull; PT. Berkah Pikiran Rakyat (Harian Umum Kabar Priangan)
                        </p>
                    </div>
                </div>

                <!-- Ringkasan Status Akun & Sesi -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 shrink-0 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                    <div class="px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        <span class="block text-[11px] font-medium text-slate-500">Status Sesi</span>
                        <span class="text-xs font-bold text-kp-blue-700 mt-0.5 block">{{ $user->last_seen_text }}</span>
                    </div>
                    <div class="px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        <span class="block text-[11px] font-medium text-slate-500">Login Terakhir</span>
                        <span class="text-xs font-bold text-slate-900 font-mono mt-0.5 block">
                            {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    <div class="col-span-2 sm:col-span-1 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        <span class="block text-[11px] font-medium text-slate-500">Alamat IP</span>
                        <span class="text-xs font-bold text-slate-900 font-mono mt-0.5 block">
                            {{ $user->last_login_ip ?: request()->ip() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 font-semibold rounded-xl text-xs">
                Data profil pengguna berhasil diperbarui.
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 font-semibold rounded-xl text-xs">
                Kata sandi akun berhasil diperbarui.
            </div>
        @endif

        <!-- 2. Layout 2 Kolom Standar ERP (7 Kiri : 5 Kanan) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

            <!-- Kolom Kiri (7 Kolom): Pengaturan Identitas & Kata Sandi -->
            <div class="lg:col-span-7 space-y-5">

                <!-- Form Informasi Akun & Penugasan -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                    <div class="pb-3 mb-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Informasi Akun &amp; Penugasan</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Identitas resmi pegawai yang tercantum pada faktur, kwitansi, dan laporan cetak.</p>
                        </div>
                    </div>

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap Pegawai</label>
                                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name"
                                       placeholder="Masukkan nama lengkap"
                                       class="w-full h-10 px-3 text-sm text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                                @error('name')
                                    <p class="text-xs text-slate-700 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Email Kerja</label>
                                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                                       placeholder="nama@kabarpriangan.com"
                                       class="w-full h-10 px-3 text-sm text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                                @error('email')
                                    <p class="text-xs text-slate-700 font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Divisi / Hak Akses</label>
                                <input type="text" value="{{ $roleLabel }}" readonly
                                       class="w-full h-10 px-3 text-sm font-medium text-slate-600 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kantor Operasional</label>
                                <input type="text" value="Kantor Pusat Tasikmalaya" readonly
                                       class="w-full h-10 px-3 text-sm font-medium text-slate-600 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                            <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Form Ubah Kata Sandi -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                    <div class="pb-3 mb-4 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Keamanan &amp; Kata Sandi</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Gunakan kombinasi huruf dan angka minimal 8 karakter untuk menjaga keamanan otorisasi transaksi.</p>
                    </div>

                    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        @method('put')

                        <div>
                            <label for="update_password_current_password" class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi Saat Ini</label>
                            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                                   placeholder="Masukkan kata sandi saat ini"
                                   class="w-full h-10 px-3 text-sm text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                            @if($errors->updatePassword->has('current_password'))
                                <p class="text-xs text-slate-700 font-medium mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="update_password_password" class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi Baru</label>
                                <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                                       placeholder="Minimal 8 karakter"
                                       class="w-full h-10 px-3 text-sm text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                                @if($errors->updatePassword->has('password'))
                                    <p class="text-xs text-slate-700 font-medium mt-1">{{ $errors->updatePassword->first('password') }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="update_password_password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                                <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                       placeholder="Ulangi kata sandi baru"
                                       class="w-full h-10 px-3 text-sm text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                                @if($errors->updatePassword->has('password_confirmation'))
                                    <p class="text-xs text-slate-700 font-medium mt-1">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                            <button type="submit" class="h-10 px-4 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition cursor-pointer">
                                Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            <!-- Kolom Kanan (5 Kolom): Kinerja Operasional, Parameter Kerja, & Log Aktivitas -->
            <div class="lg:col-span-5 space-y-5">

                <!-- 1. Ringkasan Kinerja & Parameter Operasional -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                    <div class="pb-3 mb-4 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">Ringkasan Operasional</h2>
                        <span class="text-xs font-medium text-slate-500">{{ now()->translatedFormat('F Y') }}</span>
                    </div>

                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="block text-[11px] font-medium text-slate-500">Order Diinput</span>
                            <span class="text-lg font-bold text-slate-900 font-mono mt-0.5 block">{{ $stats['order_diinput'] ?? 0 }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="block text-[11px] font-medium text-slate-500">Kwitansi Kasir</span>
                            <span class="text-lg font-bold text-slate-900 font-mono mt-0.5 block">{{ $stats['kwitansi_dicatat'] ?? 0 }}</span>
                        </div>
                        <div class="p-3 bg-kp-blue-50/70 border border-kp-blue-200 rounded-xl">
                            <span class="block text-[11px] font-medium text-kp-blue-700">Total Order Divisi</span>
                            <span class="text-lg font-bold text-kp-blue-700 font-mono mt-0.5 block">{{ $stats['order_divisi'] ?? 0 }}</span>
                        </div>
                    </div>

                    <div class="space-y-2.5 text-xs pt-2 border-t border-slate-100">
                        <div class="flex items-center justify-between py-1">
                            <span class="text-slate-500">Batas Waktu Cut-Off Harian</span>
                            <span class="font-mono font-semibold text-slate-900">{{ $stats['jam_cutoff'] ?? '19:00' }} WIB</span>
                        </div>
                        <div class="flex items-center justify-between py-1">
                            <span class="text-slate-500">Tarif PPN Standar Sistem</span>
                            <span class="font-mono font-semibold text-slate-900">{{ $stats['ppn_persen'] ?? 11 }}%</span>
                        </div>
                        <div class="flex items-center justify-between py-1">
                            <span class="text-slate-500">Status Akun Pegawai</span>
                            <span class="font-semibold text-kp-blue-700">{{ $statusLabel }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Otorisasi Modul Sistem (Odoo Access Rights) -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                    <div class="pb-3 mb-3.5 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">Hak Akses Modul</h2>
                        <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $roleLabel }}
                        </span>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                            <span class="font-semibold text-slate-800">Transaksi Iklan (Koran, Online, TV)</span>
                            <span class="px-2 py-0.5 rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-semibold">Akses Penuh</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                            <span class="font-semibold text-slate-800">Dummy Pracetak &amp; Memo Cut-Off</span>
                            <span class="px-2 py-0.5 rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-semibold">Akses Penuh</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                            <span class="font-semibold text-slate-800">Kwitansi Kasir &amp; Pelunasan Piutang</span>
                            <span class="px-2 py-0.5 rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-semibold">Akses Penuh</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                            <span class="font-semibold text-slate-800">Laporan Pemuatan, Penjualan &amp; Piutang</span>
                            <span class="px-2 py-0.5 rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-semibold">Akses Penuh</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Riwayat Aktivitas Terakhir -->
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                    <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">Riwayat Aktivitas Terakhir</h2>
                        <span class="text-xs text-slate-500">5 Log Terakhir</span>
                    </div>

                    <div class="divide-y divide-slate-100 text-xs">
                        @forelse($recentLogs ?? [] as $log)
                            <div class="px-5 py-3 flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800 truncate">
                                        {{ $log->description ?: str_replace('_', ' ', strtoupper($log->action)) }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 mt-0.5 font-mono">
                                        IP: {{ $log->ip_address ?: '127.0.0.1' }}
                                    </p>
                                </div>
                                <span class="text-[11px] text-slate-500 font-mono whitespace-nowrap shrink-0">
                                    {{ $log->created_at ? $log->created_at->format('d/m H:i') : '-' }}
                                </span>
                            </div>
                        @empty
                            <div class="px-5 py-4 text-xs text-slate-500">
                                Sesi aktif tercatat pada {{ now()->format('d/m/Y H:i') }} ({{ request()->ip() }}).
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
