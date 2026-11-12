<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Kontrol Anggaran & Realisasi
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Budgeting
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Monitoring serapan pagu anggaran belanja terhadap realisasi pengeluaran kasir.
                    </p>
                </div>
            </div>

            <!-- Filter Bulan -->
            <form method="GET" action="{{ route('accounting.beban.anggaran') }}" class="flex items-center gap-2">
                <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 transition" />
                <button type="submit" class="h-9 px-3.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                    Filter
                </button>
            </form>
        </div>

        <!-- 3 KPI Cards Anggaran -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Pagu Anggaran Disetujui</span>
                <div class="text-xl font-bold text-slate-900 mt-1">Rp {{ number_format($totalPagu, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Realisasi Beban Kas Keluar</span>
                <div class="text-xl font-bold text-slate-900 mt-1">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Sisa Pagu Tersedia</span>
                <div class="text-xl font-bold text-kp-blue-700 mt-1">Rp {{ number_format($sisaAnggaran, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Tabel Daftar Pengajuan Anggaran -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Pengajuan Anggaran Divisi ({{ $bulanPilihan }})</h2>
                    <p class="text-xs text-slate-500">Pagu pengajuan operasional redaksi, sirkulasi, percetakan, dan umum.</p>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] font-bold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">No. Pengajuan</th>
                            <th class="py-3 px-3.5">Divisi</th>
                            <th class="py-3 px-3.5">Pemohon</th>
                            <th class="py-3 px-3.5">Keperluan Belanja</th>
                            <th class="py-3 px-3.5 text-right">Pagu Diajukan</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($daftarAnggaran as $key => $ang)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $ang->nomor_pengajuan ?: ('PA-' . $ang->id) }}</td>
                                <td class="py-3 px-3.5">{{ ucfirst($ang->divisi) }}</td>
                                <td class="py-3 px-3.5">{{ $ang->pemohon }}</td>
                                <td class="py-3 px-3.5 max-w-xs truncate">{{ $ang->keperluan }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($ang->nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md {{ $ang->status === 'approved' ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst($ang->status ?: 'Diajukan') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada data pengajuan anggaran pada bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>

