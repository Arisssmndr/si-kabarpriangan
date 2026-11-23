<x-app-layout>
    <div class="space-y-5">

        <!-- 1. Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Kontrol Anggaran &amp; Realisasi
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
        </div>

        <!-- 2. KPI Cards Anggaran (Report Info) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pagu Anggaran Disetujui</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalPagu, 0, ',', '.') }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Alokasi bulan {{ $bulanPilihan }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Realisasi Beban Kas Keluar</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Pengeluaran terverifikasi kasir</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sisa Pagu Tersedia</span>
                <div class="text-xl font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($sisaAnggaran, 0, ',', '.') }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Sisa saldo pagu operasional</p>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter Mandiri (Standar Divisi Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.beban.anggaran') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Data</label>
                    <input type="text" 
                           name="q" 
                           value="{{ $q ?? '' }}" 
                           placeholder="Ketik no pengajuan, pemohon, atau keperluan..." 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Divisi Pemohon</label>
                    <select name="divisi" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ ($divisi ?? 'all') === 'all' ? 'selected' : '' }}>Semua Divisi</option>
                        <option value="redaksi" {{ ($divisi ?? '') === 'redaksi' ? 'selected' : '' }}>Redaksi</option>
                        <option value="percetakan" {{ ($divisi ?? '') === 'percetakan' ? 'selected' : '' }}>Percetakan &amp; Produksi</option>
                        <option value="iklan" {{ ($divisi ?? '') === 'iklan' ? 'selected' : '' }}>Divisi Iklan</option>
                        <option value="sirkulasi" {{ ($divisi ?? '') === 'sirkulasi' ? 'selected' : '' }}>Divisi Sirkulasi</option>
                        <option value="keuangan" {{ ($divisi ?? '') === 'keuangan' ? 'selected' : '' }}>Keuangan &amp; Akuntansi</option>
                        <option value="umum" {{ ($divisi ?? '') === 'umum' ? 'selected' : '' }}>Umum &amp; SDM</option>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Periode Bulan</label>
                    <input type="month" 
                           name="bulan" 
                           value="{{ $bulanPilihan }}" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'divisi']) || request('bulan') != date('Y-m'))
                        <a href="{{ route('accounting.beban.anggaran') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 4. Pure Tabel Daftar Pengajuan Anggaran -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">No. Pengajuan</th>
                            <th class="py-3 px-3.5">Divisi</th>
                            <th class="py-3 px-3.5">Pemohon</th>
                            <th class="py-3 px-3.5">Keperluan Belanja</th>
                            <th class="py-3 px-3.5 text-right">Pagu Diajukan</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 text-sm">
                        @forelse($daftarAnggaran as $key => $ang)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400 font-mono">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $ang->nomor_pengajuan ?: ('PA-' . $ang->id) }}</td>
                                <td class="py-3 px-3.5 font-medium text-slate-800">{{ ucfirst($ang->divisi) }}</td>
                                <td class="py-3 px-3.5">{{ $ang->pemohon }}</td>
                                <td class="py-3 px-3.5 max-w-xs truncate text-slate-600">{{ $ang->keperluan }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($ang->nominal ?? $ang->nominal_diajukan ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md {{ $ang->status === 'approved' ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst($ang->status ?: 'Diajukan') }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <x-action-dropdown align="right">
                                        <a href="{{ route('accounting.beban.verifikasi', ['q' => $ang->pemohon]) }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <span>Verifikasi BKK Terkait</span>
                                        </a>
                                    </x-action-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400 text-sm">
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