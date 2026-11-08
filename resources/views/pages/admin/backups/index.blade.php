<x-app-layout>
    <div class="space-y-6">

        <!-- 1. Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-200 text-teal-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7zm0 5h16" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Cadangan Data
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Simpan dan pulihkan data sistem secara berkala untuk keamanan data.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <a href="{{ route('admin.users.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-black font-bold text-xs rounded-xl border border-slate-300 transition">
                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Manajemen Staf</span>
                </a>

                <a href="{{ route('admin.activity-logs.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-black font-bold text-xs rounded-xl border border-slate-300 transition">
                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Audit Log</span>
                </a>

                <form method="POST" action="{{ route('admin.backups.manual') }}" onsubmit="return confirm('Jalankan proses pencadangan database manual sekarang?');">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition duration-150 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>Buat Cadangan Manual Sekarang</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- 2. Statistik Cadangan Data -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-xs">
                <span class="text-xs font-bold text-black block">Total File Cadangan</span>
                <span class="text-2xl font-black text-black mt-1 block">{{ $stats['total'] }} File</span>
                <span class="text-[11px] text-teal-700 font-bold">Terarsip Aman</span>
            </div>

            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-xs">
                <span class="text-xs font-bold text-black block">Total Kapasitas Terpakai</span>
                <span class="text-2xl font-black text-black mt-1 block">
                    {{ $stats['total_size'] >= 1048576 ? number_format($stats['total_size'] / 1048576, 2) . ' MB' : number_format($stats['total_size'] / 1024, 2) . ' KB' }}
                </span>
                <span class="text-[11px] text-black font-medium">Kompresi Gzip</span>
            </div>

            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-xs">
                <span class="text-xs font-bold text-black block">Cadangan Terakhir</span>
                <span class="text-sm font-extrabold text-black mt-1.5 block">
                    {{ $stats['last_backup'] ? $stats['last_backup']->created_at->format('d/m/Y H:i') : '-' }}
                </span>
                <span class="text-[11px] text-emerald-700 font-bold">Status: Berhasil</span>
            </div>

            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-xs">
                <span class="text-xs font-bold text-black block">Integritas Terverifikasi</span>
                <span class="text-2xl font-black text-emerald-800 mt-1 block">{{ $stats['verified_count'] }} File</span>
                <span class="text-[11px] text-emerald-700 font-bold">SHA-256 Valid</span>
            </div>
        </div>

        <!-- 3. Tabel Riwayat Cadangan Data -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-extrabold text-black">Daftar Arsip Cadangan Database (Gzip SQL Dump)</h2>
                    <p class="text-xs text-black font-medium mt-0.5">Disimpan terisolasi di direktori server dan siap dipulihkan saat pemulihan bencana (*Disaster Recovery*).</p>
                </div>
                <div class="text-xs text-black font-semibold">
                    Total: <span class="font-extrabold text-black">{{ $backups->total() }}</span> file
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-100/90 text-xs font-extrabold text-black uppercase tracking-wider">
                            <th class="py-3.5 px-4">Nama Berkas Cadangan</th>
                            <th class="py-3.5 px-4">Frekuensi</th>
                            <th class="py-3.5 px-4">Ukuran Gzip</th>
                            <th class="py-3.5 px-4">Checksum SHA-256</th>
                            <th class="py-3.5 px-4 text-center">Status Integritas</th>
                            <th class="py-3.5 px-4">Waktu Eksekusi</th>
                            <th class="py-3.5 px-4 text-center">Aksi Pemulihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($backups as $b)
                            <tr class="hover:bg-slate-50/90 transition-colors">
                                <!-- Nama Berkas -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <span class="font-mono font-bold text-black text-xs">
                                            {{ $b->filename }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Frekuensi -->
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase bg-slate-100 text-black border border-slate-300">
                                        {{ $b->frequency }}
                                    </span>
                                </td>

                                <!-- Ukuran -->
                                <td class="py-3.5 px-4 font-mono font-extrabold text-black">
                                    {{ $b->formatted_size }}
                                </td>

                                <!-- Checksum SHA-256 -->
                                <td class="py-3.5 px-4">
                                    <span class="font-mono text-[11px] text-black bg-slate-100 px-2 py-1 rounded border border-slate-200" title="{{ $b->checksum_sha256 }}">
                                        {{ substr($b->checksum_sha256, 0, 16) }}...
                                    </span>
                                </td>

                                <!-- Status Integritas -->
                                <td class="py-3.5 px-4 text-center">
                                    @if($b->status === 'verified')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                            <span>Terverifikasi Valid</span>
                                        </span>
                                    @elseif($b->status === 'failed')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-red-100 text-red-900 border border-red-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                            <span>Korup / Gagal</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-black border border-slate-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                            <span>Belum Diuji</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Waktu -->
                                <td class="py-3.5 px-4 font-mono text-black whitespace-nowrap">
                                    {{ $b->created_at->format('d/m/Y H:i') }}
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <!-- Unduh -->
                                        <a href="{{ route('admin.backups.download', $b->id) }}" 
                                           class="px-2.5 py-1.5 rounded-lg border border-slate-300 text-black hover:bg-slate-100 font-bold transition flex items-center gap-1"
                                           title="Unduh Berkas Gzip Cadangan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            <span>Unduh</span>
                                        </a>

                                        <!-- Uji Integritas SHA-256 (Restore Test) -->
                                        <form method="POST" action="{{ route('admin.backups.verify', $b->id) }}">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-2.5 py-1.5 rounded-lg border border-teal-300 text-teal-800 hover:bg-teal-50 font-bold transition flex items-center gap-1 cursor-pointer"
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
                                <td colspan="7" class="py-10 text-center text-black">
                                    <p class="font-bold text-sm">Belum ada berkas cadangan database yang tercatat.</p>
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
