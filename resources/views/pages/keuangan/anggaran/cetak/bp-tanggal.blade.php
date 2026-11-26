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
                            Rekap BP Priangan Per Tanggal
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            CETAK #3 &bull; ANGGARAN.EXE
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Rekapitulasi bukti pengeluaran periode {{ $periodeJudul }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('keuangan.anggaran.cetak.hub') }}" class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
                    &larr; Menu Cetak
                </a>
                <a href="{{ route('keuangan.anggaran.cetak.bp-tanggal', ['dari_tanggal' => $dariTanggal, 'sampai_tanggal' => $sampaiTanggal, 'cetak' => 1]) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Rekap (Dot-Matrix / PDF)</span>
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
                    {{ $bpList->count() }} Dokumen Terdaftar
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Disetujui (ACC)</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalAcc, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Nominal Disahkan
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Realisasi Kas (Cair)</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalCair, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $bpList->where('status', 'dicairkan_kasir')->count() }} BP Diterbitkan BKK
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Belum Cair / Antrean</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalPending, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $bpList->where('status', '!=', 'dicairkan_kasir')->count() }} BP Belum Diambil
                </div>
            </div>
        </div>

        <!-- Tier 3: Toolbar Filter Periode Tanggal Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <form method="GET" action="{{ route('keuangan.anggaran.cetak.bp-tanggal') }}" class="flex items-center gap-2 flex-wrap">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-semibold text-slate-700">Dari:</span>
                    <input type="date" name="dari_tanggal" value="{{ $dariTanggal }}" 
                           class="h-11 text-xs font-semibold rounded-xl border border-slate-300 px-3 bg-white text-slate-800 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-semibold text-slate-700">s/d:</span>
                    <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal }}" 
                           class="h-11 text-xs font-semibold rounded-xl border border-slate-300 px-3 bg-white text-slate-800 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                </div>
                <button type="submit" class="h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl transition shadow-xs cursor-pointer">
                    Filter
                </button>
            </form>

            <div class="text-xs text-slate-500 font-medium">
                Periode: <strong class="text-slate-900">{{ $periodeJudul }}</strong>
            </div>
        </div>

        <!-- Tier 4: Pure Data Table Standar Divisi Iklan (Kolom Terpisah 100%) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3.5 px-3.5 text-center w-12">No</th>
                            <th class="py-3.5 px-3.5 min-w-[110px]">Tanggal</th>
                            <th class="py-3.5 px-3.5 min-w-[140px]">No. BP</th>
                            <th class="py-3.5 px-3.5 min-w-[140px]">No. Register</th>
                            <th class="py-3.5 px-3.5 text-center w-24">Kode Bagian</th>
                            <th class="py-3.5 px-3.5 min-w-[140px]">Bidang</th>
                            <th class="py-3.5 px-3.5 min-w-[150px]">Pemohon</th>
                            <th class="py-3.5 px-3.5 min-w-[240px]">Keperluan</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Nominal ACC</th>
                            <th class="py-3.5 px-3.5 text-center w-28">Status</th>
                            <th class="py-3.5 px-3.5 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($bpList as $idx => $bp)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-700">
                                    {{ \Carbon\Carbon::parse($bp->tanggal_pengajuan)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">
                                    {{ $bp->no_bp }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-700">
                                    {{ $bp->no_register_anggaran ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded font-mono text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        {{ $bp->kode_bagian ?? '03' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 font-bold text-slate-900 uppercase">
                                    {{ $bp->divisi ?? 'Umum' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-medium text-slate-800">
                                    {{ $bp->pemohon }}
                                </td>
                                <td class="py-3.5 px-3.5 text-xs text-slate-600 whitespace-normal min-w-[240px] max-w-sm leading-relaxed">
                                    {{ $bp->keperluan }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($bp->nominal_acc ?? $bp->nominal_pengajuan, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($bp->status === 'dicairkan_kasir')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Cair Kasir
                                        </span>
                                    @elseif($bp->status === 'approved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            ACC
                                        </span>
                                    @elseif($bp->status === 'rejected')
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
                                    <a href="{{ route('keuangan.anggaran.voucher', $bp->pengajuan_anggaran_id ?? $bp->id) }}" 
                                       target="_blank"
                                       class="px-2.5 py-1 rounded-lg border border-slate-300 hover:border-kp-blue-500 hover:bg-kp-blue-50 text-kp-blue-700 text-xs font-semibold transition shadow-2xs">
                                        Voucher
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-12 text-center text-slate-400 italic">
                                    Tidak ada data bukti pengeluaran (BP) pada rentang tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($bpList->count() > 0)
                        <tfoot class="bg-slate-50 border-t border-slate-200 font-semibold text-xs whitespace-nowrap text-slate-900">
                            <tr>
                                <td colspan="8" class="py-3.5 px-3.5 text-right uppercase font-bold text-slate-900">
                                    TOTAL KESELURUHAN ({{ $bpList->count() }} Transaksi):
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-bold text-kp-blue-700 tabular-nums">{{ number_format($totalAcc, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td colspan="2" class="py-3.5 px-3.5 text-center text-slate-500 font-normal text-xs">
                                    Cair: Rp {{ number_format($totalCair, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
