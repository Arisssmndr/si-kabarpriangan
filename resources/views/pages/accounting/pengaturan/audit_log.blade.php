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

        <!-- Tabel Log Audit -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Riwayat Audit Trail Sistem</h2>
                    <p class="text-xs text-slate-500">Catatan aktivitas pengguna yang tidak dapat diubah (immutable log).</p>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] font-bold text-xs uppercase tracking-wider whitespace-nowrap">
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
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md uppercase {{ str_contains($log->aksi, 'HITUNG') ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : (str_contains($log->aksi, 'KOREKSI') ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700') }}">
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

