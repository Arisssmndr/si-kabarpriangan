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
                            Tutup Anggaran (Closing Bulanan)
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Menu Entry DOS
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Penguncian pembukuan anggaran, rekonsiliasi total pengajuan vs realisasi pencairan kasir, dan penutupan saldo sisa
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                @if($isClosed)
                    <span class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-kp-blue-600"></span>
                        Status: Terkunci (Closed)
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Status: Berjalan (Open)
                    </span>
                @endif
            </div>
        </div>

        <!-- Tier 2: 4 KPI Summary Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Plafon Anggaran</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalPlafonRAB, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Plafon RAB Teralokasi
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total BP Disetujui (ACC)</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalAcc, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Usulan Disetujui Pimpinan
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Cair Kasir (BKK)</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalCair, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Realisasi Kasir Brankas
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sisa Plafon Anggaran</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($sisaAnggaran, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Efisiensi / Belum Terpakai
                </div>
            </div>
        </div>

        <!-- Tier 3: Toolbar Filter Periode Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <form method="GET" action="{{ route('keuangan.anggaran.entry.tutup-anggaran') }}" class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-700 whitespace-nowrap">Pilih Periode:</label>
                <input type="month" name="periode" value="{{ $periode }}" 
                       class="h-11 text-xs font-mono font-semibold rounded-xl border border-slate-300 px-3 bg-white text-slate-800 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                <button type="submit" class="h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl transition shadow-xs cursor-pointer">
                    Periksa Periode
                </button>
            </form>

            <div class="text-xs text-slate-500 font-medium flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-kp-blue-600"></span>
                <span>Periode Aktif: <strong class="text-slate-900">{{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }}</strong></span>
            </div>
        </div>

        <!-- Form Konfirmasi Tutup Anggaran (Jika Belum Ditutup) -->
        @if(!$isClosed)
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-kp-blue-600"></span>
                    Konfirmasi Penutupan Buku Anggaran Periode {{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }}
                </h2>

                <form action="{{ route('keuangan.anggaran.entry.tutup-anggaran.proses') }}" method="POST" class="mt-4 space-y-4" onsubmit="return confirmSubmit(this, event, {
                    title: 'Tutup &amp; Kunci Anggaran?',
                    text: 'Apakah Anda yakin ingin <strong>MENUTUP &amp; MENGUNCI</strong> periode anggaran {{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }}? Setelah ditutup, data akan diarsipkan.',
                    theme: 'amber',
                    icon: 'warn',
                    confirmText: 'Ya, Tutup &amp; Kunci'
                });">
                    @csrf
                    <input type="hidden" name="periode" value="{{ $periode }}">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Berita Acara Tutup Anggaran</label>
                        <textarea name="catatan" rows="3" placeholder="Masukkan catatan penutupan buku anggaran bulanan, ringkasan sisa anggaran, dll..." 
                                  class="w-full text-xs rounded-xl border border-slate-300 p-3 text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600 leading-relaxed"></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                        <p class="text-xs text-slate-500">
                            Penguncian anggaran akan mengabadikan rekapitulasi realisasi BP untuk pelaporan ke pimpinan.
                        </p>
                        <button type="submit" class="h-11 px-6 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition inline-flex items-center justify-center cursor-pointer">
                            <span>Tutup &amp; Kunci Anggaran Periode Ini</span>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-kp-blue-50 text-kp-blue-700 flex items-center justify-center shrink-0 font-bold border border-kp-blue-200">
                    ✓
                </div>
                <div class="text-xs text-slate-700">
                    Periode <strong>{{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }}</strong> telah resmi ditutup pada tanggal <strong>{{ $isClosed->tanggal_tutup->format('d/m/Y') }}</strong> oleh <strong>{{ $isClosed->closedBy->name ?? 'Staf Anggaran' }}</strong>. Catatan: "{{ $isClosed->catatan ?? '-' }}".
                </div>
            </div>
        @endif

        <!-- Tier 4: Pure Data Table Standar Divisi Iklan (Riwayat Closing) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[250px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3.5 px-3.5 text-center w-12">No</th>
                            <th class="py-3.5 px-3.5 min-w-[120px]">Periode</th>
                            <th class="py-3.5 px-3.5 min-w-[120px]">Tanggal Tutup</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Plafon RAB</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Total ACC (BP)</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Dicairkan Kasir</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Sisa Anggaran</th>
                            <th class="py-3.5 px-3.5 min-w-[150px]">Ditutup Oleh</th>
                            <th class="py-3.5 px-3.5 min-w-[200px]">Catatan Berita Acara</th>
                            <th class="py-3.5 px-3.5 text-center w-24">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($riwayatClosing as $key => $row)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $key + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">
                                    {{ $row->periode }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-700">
                                    {{ $row->tanggal_tutup ? $row->tanggal_tutup->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($row->total_plafon, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($row->total_bp_acc, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($row->total_cair_kasir, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($row->sisa_anggaran, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 font-medium text-slate-800">
                                    {{ $row->closedBy->name ?? 'Staf' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-xs text-slate-600 whitespace-normal min-w-[200px] max-w-xs leading-relaxed">
                                    {{ $row->catatan ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        Closed
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400 italic">
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
