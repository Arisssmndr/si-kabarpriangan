<x-app-layout>
    <div class="space-y-6">

        <!-- Header Rekapitulasi BP Priangan -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.anggaran.index') }}" class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition" title="Kembali ke Daftar Anggaran">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-800 text-[10px] font-extrabold uppercase tracking-wider">
                            ANGGARAN.EXE &bull; CETAK
                        </span>
                        <span class="text-xs text-slate-400 font-medium">HU. KABAR PRIANGAN</span>
                    </div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight mt-0.5">
                        REKAP BUKTI PENGELUARAN (BP) PENERBITAN
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Laporan rekapitulasi realisasi anggaran terintegrasi Buku Kas Mutasi &amp; Register PR
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi Cepat & Cetak -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('keuangan.anggaran.create') }}" 
                   class="h-9 px-3.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs transition">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Entry BP Baru</span>
                </a>

                <a href="{{ route('keuangan.anggaran.rekap-bp', array_merge(request()->all(), ['cetak' => 1])) }}" 
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
                <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                    Rp {{ number_format($totalPengajuan, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    <span>{{ $bpList->count() }} Dokumen BP Terbit</span>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Disetujui (ACC)</div>
                <div class="text-xl sm:text-2xl font-black text-indigo-600 mt-1">
                    Rp {{ number_format($totalAcc, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span>Register PR Terbit</span>
                </div>
            </div>

            <div class="bg-white border border-emerald-200/80 rounded-2xl p-4 sm:p-5 shadow-xs bg-emerald-50/20">
                <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Realisasi Kas (Cair)</div>
                <div class="text-xl sm:text-2xl font-black text-emerald-600 mt-1">
                    Rp {{ number_format($totalCair, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-emerald-600 mt-1 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ $bpList->where('status', 'dicairkan_kasir')->count() }} BP Diterbitkan BKK Kasir</span>
                </div>
            </div>

            <div class="bg-white border border-amber-200/80 rounded-2xl p-4 sm:p-5 shadow-xs bg-amber-50/20">
                <div class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Belum Cair / Antrean</div>
                <div class="text-xl sm:text-2xl font-black text-amber-600 mt-1">
                    Rp {{ number_format($totalPending, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-amber-600 mt-1 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>{{ $bpList->where('status', '!=', 'dicairkan_kasir')->count() }} BP Siap / Menunggu</span>
                </div>
            </div>
        </div>

        <!-- Filter Bar Interaktif -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('keuangan.anggaran.rekap-bp') }}" class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-700">Mode Periode:</span>
                        <div class="inline-flex rounded-lg p-0.5 bg-slate-100 border border-slate-200 text-xs">
                            <label class="px-3 py-1 rounded-md font-semibold cursor-pointer transition {{ $mode === 'bulan' ? 'bg-white text-purple-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="mode" value="bulan" onchange="this.form.submit()" class="hidden" {{ $mode === 'bulan' ? 'checked' : '' }}>
                                Per Bulan
                            </label>
                            <label class="px-3 py-1 rounded-md font-semibold cursor-pointer transition {{ $mode === 'tanggal' ? 'bg-white text-purple-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="mode" value="tanggal" onchange="this.form.submit()" class="hidden" {{ $mode === 'tanggal' ? 'checked' : '' }}>
                                Rentang Tanggal
                            </label>
                        </div>
                    </div>

                    <div class="text-xs font-semibold text-purple-800 bg-purple-50 px-3 py-1 rounded-lg border border-purple-200">
                        Periode Aktif: <strong>{{ $periodeJudul }}</strong>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    @if($mode === 'bulan')
                        <div class="sm:col-span-5">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Bulan</label>
                            <select name="bulan" class="w-full text-xs rounded-xl border-slate-300 focus:border-purple-500 focus:ring-purple-500">
                                @for($m = 1; $m <= 12; $m++)
                                    @php $val = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $val }}" {{ (int)$bulan === $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tahun</label>
                            <select name="tahun" class="w-full text-xs rounded-xl border-slate-300 focus:border-purple-500 focus:ring-purple-500">
                                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ (int)$tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    @else
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Dari Tanggal</label>
                            <input type="date" name="dari_tanggal" value="{{ $dariTanggal }}" class="w-full text-xs rounded-xl border-slate-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Sampai Tanggal</label>
                            <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal }}" class="w-full text-xs rounded-xl border-slate-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                    @endif

                    <div class="sm:col-span-3 flex items-center gap-2">
                        <button type="submit" class="flex-1 h-9 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            <span>Terapkan</span>
                        </button>
                        <a href="{{ route('keuangan.anggaran.rekap-bp') }}" class="h-9 px-3 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-600 text-xs font-bold flex items-center justify-center transition" title="Reset Filter">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Rekapitulasi BP Priangan -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">
                        Daftar Transaksi Rekap BP Priangan
                    </h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Menampilkan {{ $bpList->count() }} data pengeluaran bukti anggaran
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 font-mono">Format DOS ANGGARAN.EXE</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No BP &amp; Register PR</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Bidang / Divisi</th>
                            <th class="py-3 px-3.5">Perkiraan (COA)</th>
                            <th class="py-3 px-3.5">Pemohon &amp; Keperluan</th>
                            <th class="py-3 px-3.5 text-right">Pengajuan (Rp)</th>
                            <th class="py-3 px-3.5 text-right">ACC / Cair (Rp)</th>
                            <th class="py-3 px-3.5 text-center">Status &amp; BKK</th>
                            <th class="py-3 px-3.5 text-center w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($bpList as $idx => $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3 px-3.5 text-center text-slate-400 font-mono text-[11px]">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-3 px-3.5 font-mono text-slate-800">
                                    <div class="font-bold text-slate-900">{{ $item->no_bp }}</div>
                                    @if($item->no_register_anggaran)
                                        <div class="text-[10px] text-indigo-700 bg-indigo-50 border border-indigo-200 px-1.5 py-0.2 rounded inline-block mt-0.5">
                                            Reg: {{ $item->no_register_anggaran }}
                                        </div>
                                    @else
                                        <div class="text-[10px] text-slate-400 italic">Belum ACC</div>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 font-mono text-slate-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-3.5">
                                    <span class="inline-flex items-center gap-1 font-semibold text-slate-800">
                                        <span class="px-1.5 py-0.2 bg-slate-100 text-slate-700 rounded font-mono text-[10px]">
                                            {{ $item->kode_bagian ?? '-' }}
                                        </span>
                                        <span>{{ $item->divisi }}</span>
                                    </span>
                                </td>
                                <td class="py-3 px-3.5">
                                    @if($item->kode_perkiraan)
                                        <div class="font-mono text-[11px] font-bold text-slate-800">{{ $item->kode_perkiraan }}</div>
                                        <div class="text-[11px] text-slate-500 truncate max-w-[160px]">{{ $item->nama_perkiraan ?? '-' }}</div>
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 max-w-xs">
                                    <div class="font-bold text-slate-900">{{ $item->pemohon }}</div>
                                    <div class="text-[11px] text-slate-500 line-clamp-2 mt-0.5">
                                        {{ $item->keperluan }}
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono font-medium text-slate-700 whitespace-nowrap">
                                    {{ number_format($item->nominal_pengajuan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                    {{ number_format($item->nominal_acc ?? $item->nominal_pengajuan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    @if($item->status === 'dicairkan_kasir')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Cair di Kasir
                                        </span>
                                        @if($item->kasKeluar)
                                            <div class="text-[10px] font-mono text-emerald-700 mt-0.5 font-bold">
                                                BKK: {{ $item->kasKeluar->no_bkk }}
                                            </div>
                                        @endif
                                    @elseif($item->status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-bold text-[10px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            Disetujui (ACC)
                                        </span>
                                    @elseif($item->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">
                                            Menunggu ACC
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    @if($item->pengajuan_anggaran_id)
                                        <a href="{{ route('keuangan.anggaran.voucher', $item->pengajuan_anggaran_id) }}" 
                                           target="_blank" 
                                           class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition"
                                           title="Cetak Bukti Voucher Kas Keluar">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            <span>Voucher</span>
                                        </a>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="font-bold text-slate-600">Tidak ada data bukti pengeluaran (BP) pada periode ini.</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Silakan pilih periode lain atau masukkan pengajuan BP baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($bpList->count() > 0)
                        <tfoot>
                            <tr class="bg-slate-50 border-t-2 border-slate-300 font-mono font-bold text-slate-900 text-xs">
                                <td colspan="6" class="py-3 px-3.5 text-right uppercase tracking-wider font-sans">
                                    TOTAL KESELURUHAN ({{ $bpList->count() }} Transaksi):
                                </td>
                                <td class="py-3 px-3.5 text-right font-black text-slate-800">
                                    {{ number_format($totalPengajuan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-black text-purple-700">
                                    {{ number_format($totalAcc, 0, ',', '.') }}
                                </td>
                                <td colspan="2" class="py-3 px-3.5 text-center text-[11px] font-sans text-emerald-700">
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
