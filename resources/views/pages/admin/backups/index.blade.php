<x-app-layout>
    <div class="space-y-6">

        <!-- 1. Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-10 w-auto object-contain shrink-0" onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Cadangan Data (Backup &amp; Restore)
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Kelola arsip cadangan database terkompresi, verifikasi checksum integritas, dan mitigasi pemulihan bencana.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <a href="{{ route('admin.users.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-lg border border-slate-300 transition">
                    <svg class="w-4 h-4 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Manajemen Pengguna</span>
                </a>

                <a href="{{ route('admin.activity-logs.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-lg border border-slate-300 transition">
                    <svg class="w-4 h-4 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Log Aktivitas</span>
                </a>

                <form method="POST" action="{{ route('admin.backups.manual') }}" onsubmit="return confirmSubmit(this, event, {
                    title: 'Cadangkan Database?',
                    text: 'Jalankan proses pencadangan database manual sekarang?',
                    theme: 'blue',
                    icon: 'save',
                    confirmText: 'Ya, Cadangkan Sekarang'
                });">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0E527B] hover:bg-[#07334F] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer">
                        <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>Buat Cadangan Manual</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- 2. Statistik Cadangan Data -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-xs">
                <span class="text-xs font-semibold text-slate-500 block">Total File Cadangan</span>
                <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ $stats['total'] }} File</span>
                <span class="text-xs text-kp-blue-700 font-semibold">Terarsip Aman</span>
            </div>

            <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-xs">
                <span class="text-xs font-semibold text-slate-500 block">Total Kapasitas Terpakai</span>
                <span class="text-2xl font-bold text-slate-900 mt-1 block">
                    {{ $stats['total_size'] >= 1048576 ? number_format($stats['total_size'] / 1048576, 2) . ' MB' : number_format($stats['total_size'] / 1024, 2) . ' KB' }}
                </span>
                <span class="text-xs text-slate-500 font-medium">Kompresi Gzip</span>
            </div>

            <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-xs">
                <span class="text-xs font-semibold text-slate-500 block">Cadangan Terakhir</span>
                <span class="text-sm font-bold text-slate-900 mt-1.5 block">
                    {{ $stats['last_backup'] ? $stats['last_backup']->created_at->format('d/m/Y H:i') : '-' }}
                </span>
                <span class="text-xs text-kp-blue-700 font-semibold">Status: Berhasil</span>
            </div>

            <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-xs">
                <span class="text-xs font-semibold text-slate-500 block">Integritas Terverifikasi</span>
                <span class="text-2xl font-bold text-kp-blue-700 mt-1 block">{{ $stats['verified_count'] }} File</span>
                <span class="text-xs text-kp-blue-700 font-semibold">SHA-256 Valid</span>
            </div>
        </div>

        <!-- 3. Tabel Riwayat Cadangan Data -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Arsip Cadangan Database (Gzip SQL Dump)</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Disimpan terisolasi di direktori server dan siap dipulihkan saat pemulihan bencana (*Disaster Recovery*).</p>
                </div>
                <div class="text-xs text-slate-500 font-semibold">
                    Total: <span class="font-bold text-slate-900">{{ $backups->total() }}</span> file
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900]">
                        <tr class="text-xs font-bold uppercase tracking-wider text-white whitespace-nowrap">
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Nama Berkas Cadangan</th>
                            <th class="py-3 px-3.5 text-center">Frekuensi</th>
                            <th class="py-3 px-3.5">Ukuran Gzip</th>
                            <th class="py-3 px-3.5">Checksum SHA-256</th>
                            <th class="py-3 px-3.5 text-center">Status Integritas</th>
                            <th class="py-3 px-3.5">Waktu Eksekusi</th>
                            <th class="py-3 px-3.5 text-center w-36">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 text-sm">
                        @forelse($backups as $key => $b)
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- No -->
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $backups->firstItem() + $key }}
                                </td>

                                <!-- Nama Berkas -->
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-kp-blue-50 text-[#0E527B] border border-kp-blue-200 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <span class="font-mono font-semibold text-slate-900 text-sm">
                                            {{ $b->filename }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Frekuensi -->
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $b->frequency }}
                                    </span>
                                </td>

                                <!-- Ukuran Gzip -->
                                <td class="py-3.5 px-3.5 font-mono font-semibold text-slate-900 text-sm">
                                    {{ $b->formatted_size }}
                                </td>

                                <!-- Checksum SHA-256 -->
                                <td class="py-3.5 px-3.5">
                                    <span class="font-mono text-xs text-slate-700 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200" title="{{ $b->checksum_sha256 }}">
                                        {{ substr($b->checksum_sha256, 0, 16) }}...
                                    </span>
                                </td>

                                <!-- Status Integritas -->
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($b->status === 'verified')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Terverifikasi Valid
                                        </span>
                                    @elseif($b->status === 'failed')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            Gagal
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            Belum Diuji
                                        </span>
                                    @endif
                                </td>

                                <!-- Waktu Eksekusi -->
                                <td class="py-3.5 px-3.5 font-mono text-slate-600 text-sm">
                                    {{ $b->created_at->format('d/m/Y H:i') }}
                                </td>

                                <!-- Aksi Pemulihan -->
                                <td class="py-3.5 px-3.5 text-center">
                                    <div class="inline-flex items-center gap-1.5 justify-center">
                                        <!-- Unduh -->
                                        <a href="{{ route('admin.backups.download', $b->id) }}" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 transition"
                                           title="Unduh Berkas Gzip Cadangan">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            <span>Unduh</span>
                                        </a>

                                        <!-- Uji Integritas SHA-256 -->
                                        <form method="POST" action="{{ route('admin.backups.verify', $b->id) }}">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg border border-kp-blue-200 bg-kp-blue-50 hover:bg-kp-blue-100 text-[#0E527B] transition cursor-pointer"
                                                    title="Uji Integritas Berkas (Restore Test)">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>Uji SHA-256</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-slate-500 text-sm font-medium">
                                    Belum ada berkas cadangan database yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($backups->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50/70">
                    {{ $backups->links() }}
                </div>
            @endif
        </div>

    </div>

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
