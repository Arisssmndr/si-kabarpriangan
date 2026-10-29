<x-app-layout>
    <div class="space-y-6">

        <!-- Top Header & Breadcrumb -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.anggaran.cetak.hub') }}" class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition" title="Kembali ke Menu Cetak">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-800 text-[10px] font-black uppercase tracking-wider">
                            CETAK #3 &bull; ANGGARAN.EXE
                        </span>
                        <span class="text-xs text-slate-400 font-medium">HU. KABAR PRIANGAN</span>
                    </div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight mt-0.5">
                        REKAP BP PRIANGAN PER TANGGAL
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi bukti pengeluaran periode {{ $periodeJudul }}
                    </p>
                </div>
            </div>

            <!-- Filter Rentang Tanggal & Print Action -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <form method="GET" action="{{ route('keuangan.anggaran.cetak.bp-tanggal') }}" class="flex items-center gap-2 flex-wrap">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs text-slate-500 font-semibold">Dari:</span>
                        <input type="date" name="dari_tanggal" value="{{ $dariTanggal }}" 
                               class="h-9 text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 font-semibold focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs text-slate-500 font-semibold">s/d</span>
                        <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal }}" 
                               class="h-9 text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 font-semibold focus:ring-2 focus:ring-purple-500">
                    </div>
                    <button type="submit" class="h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Filter
                    </button>
                </form>

                <a href="{{ route('keuangan.anggaran.cetak.bp-tanggal', ['dari_tanggal' => $dariTanggal, 'sampai_tanggal' => $sampaiTanggal, 'cetak' => 1]) }}" 
                   target="_blank"
                   class="h-9 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold inline-flex items-center gap-2 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Rekap (Print / PDF)</span>
                </a>
            </div>
        </div>

        <!-- 4 Summary KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Usulan Pengajuan</div>
                <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1 font-mono">
                    Rp {{ number_format($totalPengajuan, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    <span>{{ $bpList->count() }} Dokumen Terdaftar</span>
                </div>
            </div>

            <div class="bg-white border border-purple-200/80 rounded-2xl p-4 sm:p-5 shadow-xs bg-purple-50/20">
                <div class="text-[11px] font-bold text-purple-700 uppercase tracking-wider">Total Disetujui (ACC)</div>
                <div class="text-xl sm:text-2xl font-black text-purple-700 mt-1 font-mono">
                    Rp {{ number_format($totalAcc, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-purple-600 mt-1 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    <span>Nominal Disahkan</span>
                </div>
            </div>

            <div class="bg-white border border-emerald-200/80 rounded-2xl p-4 sm:p-5 shadow-xs bg-emerald-50/20">
                <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Realisasi Kas (Cair)</div>
                <div class="text-xl sm:text-2xl font-black text-emerald-700 mt-1 font-mono">
                    Rp {{ number_format($totalCair, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-emerald-600 mt-1 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ $bpList->where('status', 'dicairkan_kasir')->count() }} BP Diterbitkan BKK</span>
                </div>
            </div>

            <div class="bg-white border border-amber-200/80 rounded-2xl p-4 sm:p-5 shadow-xs bg-amber-50/20">
                <div class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Belum Cair / Antrean</div>
                <div class="text-xl sm:text-2xl font-black text-amber-700 mt-1 font-mono">
                    Rp {{ number_format($totalPending, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-amber-600 mt-1 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>{{ $bpList->where('status', '!=', 'dicairkan_kasir')->count() }} BP Belum Diambil</span>
                </div>
            </div>
        </div>

        <!-- Tabel Rekapitulasi BP Per Tanggal -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-tight">
                        Daftar Bukti Pengeluaran (BP) Terbit
                    </h3>
                    <p class="text-xs text-slate-500">
                        Menampilkan transaksi dari <strong>{{ $dariTanggal }}</strong> s/d <strong>{{ $sampaiTanggal }}</strong>
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-black tracking-wider">
                            <th class="py-3 px-4 w-10 text-center">No</th>
                            <th class="py-3 px-4 w-28">Tanggal</th>
                            <th class="py-3 px-4 w-32">No BP / Reg</th>
                            <th class="py-3 px-4 w-28">Bidang</th>
                            <th class="py-3 px-4">Pemohon &amp; Keperluan</th>
                            <th class="py-3 px-4 text-right w-28">Nominal ACC</th>
                            <th class="py-3 px-4 text-center w-28">Status</th>
                            <th class="py-3 px-4 text-center w-24">Voucher</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($bpList as $idx => $bp)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 text-center font-mono text-slate-500">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-700">
                                    {{ \Carbon\Carbon::parse($bp->tanggal_pengajuan)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-mono">
                                    <div class="font-black text-purple-700">{{ $bp->no_bp }}</div>
                                    <div class="text-[10px] text-slate-400">Reg: {{ $bp->no_register_anggaran ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-700">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[11px]">
                                        {{ $bp->kode_bagian ?? '03' }} - {{ ucfirst($bp->divisi ?? 'Umum') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $bp->pemohon }}</div>
                                    <div class="text-slate-500 text-[11px] line-clamp-1 mt-0.5">{{ $bp->keperluan }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                    {{ number_format($bp->nominal_acc ?? $bp->nominal_pengajuan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($bp->status === 'dicairkan_kasir')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                            Cair Kasir
                                        </span>
                                    @elseif($bp->status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold">
                                            ACC
                                        </span>
                                    @elseif($bp->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">
                                            Pengajuan
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('keuangan.anggaran.voucher', $bp->pengajuan_anggaran_id ?? $bp->id) }}" 
                                       target="_blank"
                                       class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-purple-100 hover:text-purple-700 text-slate-700 font-bold text-[11px] transition inline-block">
                                        Cetak BP
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    Tidak ada dokumen BP pada rentang tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-300 font-mono text-xs">
                        <tr class="font-black text-slate-900">
                            <td colspan="5" class="py-3 px-4 text-center uppercase">
                                TOTAL REKAP TANGGAL
                            </td>
                            <td class="py-3 px-4 text-right text-purple-700">
                                Rp {{ number_format($totalAcc, 0, ',', '.') }}
                            </td>
                            <td colspan="2" class="py-3 px-4 text-center text-slate-500 font-sans font-normal text-[11px]">
                                {{ $bpList->count() }} Dokumen BP
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
