<x-app-layout>
    <div class="space-y-6">

        <!-- Tier 1: Header Halaman Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-9 sm:h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Rekap Bukti Pengeluaran (BP) Penerbitan
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            ANGGARAN.EXE
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Laporan rekapitulasi realisasi anggaran terintegrasi Buku Kas Mutasi &amp; Register PR
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('keuangan.anggaran.rekap-bp', array_merge(request()->all(), ['cetak' => 1])) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Rekap (Print / PDF)</span>
                </a>
                <a href="{{ route('keuangan.anggaran.create') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Entry BP Baru</span>
                </a>
            </div>
        </div>

        <!-- Tier 2: 4 KPI Summary Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Usulan Pengajuan</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalPengajuan, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $bpList->count() }} Dokumen BP Terbit
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Disetujui (ACC)</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalAcc, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Register PR Terbit
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Realisasi Kas (Cair)</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalCair, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $bpList->where('status', 'dicairkan_kasir')->count() }} BP Diterbitkan BKK Kasir
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Belum Cair / Antrean</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalPending, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $bpList->where('status', '!=', 'dicairkan_kasir')->count() }} BP Siap / Menunggu
                </div>
            </div>
        </div>

        <!-- Tier 3: Toolbar Pencarian & Filter Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs space-y-3">
            <form method="GET" action="{{ route('keuangan.anggaran.rekap-bp') }}" class="space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-slate-700">Mode Periode:</span>
                        <div class="inline-flex rounded-xl p-0.5 bg-slate-100 border border-slate-200 text-xs">
                            <label class="px-3 py-1.5 rounded-lg font-semibold cursor-pointer transition {{ $mode === 'bulan' ? 'bg-white text-kp-blue-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="mode" value="bulan" onchange="this.form.submit()" class="hidden" {{ $mode === 'bulan' ? 'checked' : '' }}>
                                Per Bulan
                            </label>
                            <label class="px-3 py-1.5 rounded-lg font-semibold cursor-pointer transition {{ $mode === 'tanggal' ? 'bg-white text-kp-blue-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="mode" value="tanggal" onchange="this.form.submit()" class="hidden" {{ $mode === 'tanggal' ? 'checked' : '' }}>
                                Rentang Tanggal
                            </label>
                        </div>
                    </div>

                    <div class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 px-3 py-1.5 rounded-lg border border-kp-blue-200">
                        Periode Aktif: <strong>{{ $periodeJudul }}</strong>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    @if($mode === 'bulan')
                        <div class="sm:col-span-5">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Bulan</label>
                            <select name="bulan" class="w-full h-11 text-xs rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 font-semibold text-slate-800">
                                @for($m = 1; $m <= 12; $m++)
                                    @php $val = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $val }}" {{ (int)$bulan === $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun</label>
                            <select name="tahun" class="w-full h-11 text-xs rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 font-semibold text-slate-800">
                                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ (int)$tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    @else
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                            <input type="date" name="dari_tanggal" value="{{ $dariTanggal }}" class="w-full h-11 text-xs rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 font-semibold text-slate-800">
                        </div>
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                            <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal }}" class="w-full h-11 text-xs rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 font-semibold text-slate-800">
                        </div>
                    @endif

                    <div class="sm:col-span-3 flex items-center gap-2">
                        <button type="submit" class="flex-1 h-11 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold flex items-center justify-center shadow-xs transition cursor-pointer">
                            <span>Terapkan</span>
                        </button>
                        <a href="{{ route('keuangan.anggaran.rekap-bp') }}" class="h-11 px-4 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center justify-center transition" title="Reset Filter">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tier 4: Pure Data Table Standar Divisi Iklan (Kolom Terpisah 100%) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3.5 px-3.5 text-center w-12">No</th>
                            <th class="py-3.5 px-3.5 min-w-[140px]">No. BP</th>
                            <th class="py-3.5 px-3.5 min-w-[140px]">No. Register</th>
                            <th class="py-3.5 px-3.5 min-w-[110px]">Tanggal</th>
                            <th class="py-3.5 px-3.5 text-center w-20">Kode Bagian</th>
                            <th class="py-3.5 px-3.5 min-w-[140px]">Bidang / Divisi</th>
                            <th class="py-3.5 px-3.5 min-w-[140px]">Kode COA</th>
                            <th class="py-3.5 px-3.5 min-w-[180px]">Nama Perkiraan</th>
                            <th class="py-3.5 px-3.5 min-w-[150px]">Pemohon</th>
                            <th class="py-3.5 px-3.5 min-w-[240px]">Keperluan</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[130px]">Pengajuan</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[130px]">Disetujui (ACC)</th>
                            <th class="py-3.5 px-3.5 min-w-[130px]">No. BKK</th>
                            <th class="py-3.5 px-3.5 text-center w-28">Status</th>
                            <th class="py-3.5 px-3.5 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($bpList as $idx => $item)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">
                                    {{ $item->no_bp }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-700">
                                    {{ $item->no_register_anggaran ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-700">
                                    {{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded font-mono text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        {{ $item->kode_bagian ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 font-bold text-slate-900">
                                    {{ $item->divisi }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs font-semibold text-kp-blue-700">
                                    {{ $item->kode_perkiraan ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-xs text-slate-700">
                                    {{ $item->nama_perkiraan ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-medium text-slate-800">
                                    {{ $item->pemohon }}
                                </td>
                                <td class="py-3.5 px-3.5 text-xs text-slate-600 whitespace-normal min-w-[240px] max-w-sm leading-relaxed">
                                    {{ $item->keperluan }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($item->nominal_pengajuan, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($item->nominal_acc ?? $item->nominal_pengajuan, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-700">
                                    {{ $item->kasKeluar->no_bkk ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($item->status === 'dicairkan_kasir')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Cair Kasir
                                        </span>
                                    @elseif($item->status === 'approved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            ACC
                                        </span>
                                    @elseif($item->status === 'rejected')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-200 text-slate-700">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            Antrean
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($item->pengajuan_anggaran_id)
                                        <a href="{{ route('keuangan.anggaran.voucher', $item->pengajuan_anggaran_id) }}" 
                                           target="_blank" 
                                           class="inline-flex items-center px-2.5 py-1 rounded-lg border border-slate-300 hover:border-kp-blue-500 hover:bg-kp-blue-50 text-kp-blue-700 text-xs font-semibold transition shadow-2xs"
                                           title="Cetak Bukti Voucher Kas Keluar">
                                            Voucher
                                        </a>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="py-12 text-center text-slate-400 italic">
                                    Tidak ada data bukti pengeluaran (BP) pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($bpList->count() > 0)
                        <tfoot class="bg-slate-50 border-t border-slate-200 font-semibold text-xs whitespace-nowrap text-slate-900">
                            <tr>
                                <td colspan="10" class="py-3.5 px-3.5 text-right uppercase font-bold text-slate-900">
                                    TOTAL KESELURUHAN ({{ $bpList->count() }} Transaksi):
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-bold text-slate-900 tabular-nums">{{ number_format($totalPengajuan, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-bold text-kp-blue-700 tabular-nums">{{ number_format($totalAcc, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td colspan="3" class="py-3.5 px-3.5 text-center text-xs text-kp-blue-700 font-bold">
                                    Cair Kasir: Rp {{ number_format($totalCair, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
