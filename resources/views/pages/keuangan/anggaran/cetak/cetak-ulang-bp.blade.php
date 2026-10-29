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
                            CETAK #6 &bull; ANGGARAN.EXE
                        </span>
                        <span class="text-xs text-slate-400 font-medium">HU. KABAR PRIANGAN</span>
                    </div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight mt-0.5">
                        CETAK ULANG BP PER TANGGAL
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pencarian cepat dan pencetakan ulang fisik lembar Bukti Pengeluaran (BP) dan Voucher Kasir
                    </p>
                </div>
            </div>

            <!-- Filter Tanggal -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <form method="GET" action="{{ route('keuangan.anggaran.cetak.cetak-ulang-bp') }}" class="flex items-center gap-2">
                    <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" 
                           class="h-9 text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 font-semibold focus:ring-2 focus:ring-purple-500">
                    <button type="submit" class="h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Cari Tanggal
                    </button>
                </form>

                <a href="{{ route('keuangan.anggaran.cetak.bp-tanggal', ['dari_tanggal' => $tanggal, 'sampai_tanggal' => $tanggal, 'cetak' => 1]) }}" 
                   target="_blank"
                   class="h-9 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold inline-flex items-center gap-2 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar Rekap Hari Ini</span>
                </a>
            </div>
        </div>

        <!-- Banner Info Tanggal Terpilih -->
        <div class="bg-purple-50/50 border border-purple-200 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center font-black">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-purple-950">
                        Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                    </h2>
                    <p class="text-purple-700 text-[11px]">
                        Ditemukan <strong>{{ $bpList->count() }} dokumen BP</strong> diterbitkan pada tanggal ini. Klik tombol voucher untuk mencetak ulang fisik BP.
                    </p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-[11px] font-bold text-slate-500 uppercase block">Total Nominal ACC</span>
                <span class="text-base font-black text-purple-900 font-mono">
                    Rp {{ number_format($bpList->sum('nominal_acc'), 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Tabel Daftar BP yang Siap Dicetak Ulang -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-tight">
                        Dokumen Bukti Pengeluaran (BP)
                    </h3>
                    <p class="text-xs text-slate-500">
                        Pilih dokumen yang ingin dicetak ulang vouchernya
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-black tracking-wider">
                            <th class="py-3 px-4 w-10 text-center">No</th>
                            <th class="py-3 px-4 w-32">Nomor BP</th>
                            <th class="py-3 px-4 w-28">No. Register</th>
                            <th class="py-3 px-4 w-28">Bidang</th>
                            <th class="py-3 px-4">Pemohon &amp; Keperluan Pengeluaran</th>
                            <th class="py-3 px-4 text-right w-28">Nominal ACC</th>
                            <th class="py-3 px-4 text-center w-28">Status</th>
                            <th class="py-3 px-4 text-center w-36">Aksi Cetak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($bpList as $idx => $bp)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 text-center font-mono text-slate-500">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-black text-purple-700">
                                    {{ $bp->no_bp }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">
                                    {{ $bp->no_register_anggaran ?? '-' }}
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
                                    Rp {{ number_format($bp->nominal_acc ?? $bp->nominal_pengajuan, 0, ',', '.') }}
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
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-[11px] shadow-2xs transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        <span>Cetak Voucher</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p class="font-bold text-slate-600 text-xs">Tidak ada BP yang diterbitkan pada tanggal {{ $tanggal }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Silakan pilih tanggal lain pada filter di atas</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
