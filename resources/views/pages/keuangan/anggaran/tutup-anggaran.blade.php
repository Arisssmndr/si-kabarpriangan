<x-app-layout>
    <div class="space-y-6">

        <!-- Header Tutup Anggaran Sesuai DOS ANGGARAN.EXE -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 shadow-xs shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">
                            TUTUP ANGGARAN (CLOSING ANGGARAN BULANAN)
                        </h1>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                            Menu Entry DOS
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Penguncian pembukuan anggaran, rekonsiliasi total pengajuan vs realisasi pencairan kasir, dan penutupan saldo sisa
                    </p>
                </div>
            </div>

            <!-- Filter Periode -->
            <form method="GET" action="{{ route('keuangan.anggaran.entry.tutup-anggaran') }}" class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-600">Periode:</label>
                <input type="month" name="periode" value="{{ $periode }}" 
                       class="text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2 bg-slate-50 text-slate-800 focus:ring-2 focus:ring-purple-500">
                <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Cek
                </button>
            </form>
        </div>

        <!-- Status Card Periode -->
        @if($isClosed)
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 font-bold">
                        ✓
                    </div>
                    <div>
                        <div class="text-sm font-black text-emerald-950">
                            PERIODE {{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }} TELAH RESMI DITUTUP (CLOSED)
                        </div>
                        <p class="text-xs text-emerald-800 mt-0.5">
                            Ditutup pada tanggal <strong>{{ $isClosed->tanggal_tutup->format('d/m/Y') }}</strong> oleh <strong>{{ $isClosed->closedBy->name ?? 'Staf Anggaran' }}</strong>. Catatan: "{{ $isClosed->catatan ?? '-' }}"
                        </p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-emerald-600 text-white text-xs font-bold rounded-xl shadow-xs">
                    STATUS: LOCKED
                </span>
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 font-bold">
                        !
                    </div>
                    <div>
                        <div class="text-sm font-black text-amber-950">
                            PERIODE {{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }} MASIH BERJALAN (OPEN / DRAFT)
                        </div>
                        <p class="text-xs text-amber-800 mt-0.5">
                            Periksa total akumulasi realisasi kasir sebelum melakukan penutupan buku anggaran resmi.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- 4 Stat Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Plafon Anggaran</span>
                <div class="mt-1 text-xl font-black font-mono text-slate-900">
                    Rp {{ number_format($totalPlafonRAB, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-500">Plafon RAB teralokasi</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total BP Disetujui (ACC)</span>
                <div class="mt-1 text-xl font-black font-mono text-purple-700">
                    Rp {{ number_format($totalAcc, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-purple-600 font-semibold">Usulan yang disetujui pimpinan</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Cair Kasir (BKK)</span>
                <div class="mt-1 text-xl font-black font-mono text-emerald-600">
                    Rp {{ number_format($totalCair, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-emerald-600 font-semibold">Uang fisik brankas yang telah keluar</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sisa Plafon Anggaran</span>
                <div class="mt-1 text-xl font-black font-mono text-blue-600">
                    Rp {{ number_format($sisaAnggaran, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-500">Efisiensi / anggaran tidak terpakai</div>
            </div>
        </div>

        <!-- FORM KONFIRMASI TUTUP ANGGARAN -->
        @if(!$isClosed)
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                    Konfirmasi Penutupan Buku Anggaran Periode {{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }}
                </h2>

                <form action="{{ route('keuangan.anggaran.entry.tutup-anggaran.proses') }}" method="POST" class="mt-4 space-y-4" onsubmit="return confirm('Apakah Anda yakin ingin MENUTUP & MENGUNCI periode anggaran ini? Setelah ditutup data akan diarsipkan.');">
                    @csrf
                    <input type="hidden" name="periode" value="{{ $periode }}">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Berita Acara Tutup Anggaran</label>
                        <textarea name="catatan" rows="3" placeholder="Masukkan catatan penutupan buku anggaran bulanan, ringkasan sisa anggaran, dll..." 
                                  class="w-full text-xs rounded-xl border border-slate-300 p-3 focus:ring-2 focus:ring-rose-500 leading-relaxed"></textarea>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="text-[11px] text-slate-500">
                            ⚡ Penguncian anggaran akan mengabadikan rekapitulasi realisasi BP untuk pelaporan ke pimpinan.
                        </div>
                        <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs hover:shadow transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            <span>Tutup Anggaran Periode Ini</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- TABEL RIWAYAT CLOSING PERIODE LAMPAU -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-600"></span>
                        Riwayat Tutup Anggaran (Arsip Closing Periode)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar periode anggaran yang telah berhasil dikunci dan ditutup</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-700 text-[11px] font-extrabold uppercase tracking-wider">
                            <th class="py-3 px-4 w-32">Periode</th>
                            <th class="py-3 px-4 w-36">Tanggal Tutup</th>
                            <th class="py-3 px-4 text-right">Plafon RAB</th>
                            <th class="py-3 px-4 text-right">Total ACC (BP)</th>
                            <th class="py-3 px-4 text-right">Dicairkan Kasir</th>
                            <th class="py-3 px-4 text-right">Sisa Anggaran</th>
                            <th class="py-3 px-4">Ditutup Oleh</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/80 text-slate-700">
                        @forelse($riwayatClosing as $row)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 text-xs">
                                    {{ $row->periode }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-[11px] text-slate-600">
                                    {{ $row->tanggal_tutup->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-slate-700">
                                    Rp {{ number_format($row->total_plafon, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-purple-700">
                                    Rp {{ number_format($row->total_bp_acc, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-700">
                                    Rp {{ number_format($row->total_cair_kasir, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-slate-700">
                                    Rp {{ number_format($row->sisa_anggaran, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-800">
                                    {{ $row->closedBy->name ?? 'Staf' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Closed
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-slate-400 italic">
                                    Belum ada arsip penutupan anggaran periode lampau.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
