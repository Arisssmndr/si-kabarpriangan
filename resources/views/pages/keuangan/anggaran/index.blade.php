<x-app-layout>
    <div class="space-y-5">

        <!-- 1. Header Halaman (Standar Divisi Iklan 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-9 sm:h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Pengajuan Anggaran &amp; Realisasi Kas (BP Priangan)
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 shrink-0">
                            Sistem DOS ANGGARAN.EXE
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Alokasi pos bidang, persetujuan register anggaran (PR), dan realisasi kas keluar (BKK KO) terintegrasi ke buku kas
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi Sejajar 1 Baris -->
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('keuangan.anggaran.rekap-bp') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Rekap BP</span>
                </a>

                <a href="{{ route('keuangan.anggaran.create') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Entry BP Priangan</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 font-semibold rounded-xl text-sm flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-slate-100 border border-slate-300 text-slate-800 font-semibold rounded-xl text-sm flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- 2. KPI Summary Cards (Standar Divisi Iklan 60-30-10) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Usulan BP Diajukan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalDiajukan, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    Seluruh usulan dari 21 bidang
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Dana Cair (Kas Keluar)</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalDicairkan, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    BKK terbit &amp; kas terpotong
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Status Antrean Pencairan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ $approvedCount }} <span class="text-xs font-normal text-slate-400 font-sans">Siap Cair</span> &bull; {{ $pendingCount }} <span class="text-xs font-normal text-slate-400 font-sans">Pending</span>
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    Register PR aktif siap diproses
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Saldo Kas Tersedia</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($currentKasSaldo, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                    <span>Likuiditas Kasir</span>
                    <span class="text-kp-blue-700 font-semibold">Kas Terbuka</span>
                </div>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter (h-11 rounded-xl) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs space-y-3">
            <!-- Navigasi Tab Sesuai Alur Workflow -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <a href="{{ route('keuangan.anggaran.index', ['tab' => 'semua']) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ $tab === 'semua' ? 'bg-kp-blue-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50 font-semibold' }}">
                    <span>Semua Anggaran &amp; BP</span>
                </a>
                <a href="{{ route('keuangan.anggaran.index', ['tab' => 'pending']) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ $tab === 'pending' ? 'bg-kp-blue-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50 font-semibold' }}">
                    <span>Menunggu Review (ACC)</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'pending' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800 font-bold' }}">{{ $pendingCount }}</span>
                </a>
                <a href="{{ route('keuangan.anggaran.index', ['tab' => 'approved']) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ $tab === 'approved' ? 'bg-kp-blue-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50 font-semibold' }}">
                    <span>Siap Dicairkan ke Kas</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'approved' ? 'bg-white/20 text-white' : 'bg-kp-blue-100 text-kp-blue-800 font-bold' }}">{{ $approvedCount }}</span>
                </a>
                <a href="{{ route('keuangan.anggaran.index', ['tab' => 'cair']) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ $tab === 'cair' ? 'bg-kp-blue-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50 font-semibold' }}">
                    <span>Sudah Cair (BKK Terbit)</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'cair' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700 font-bold' }}">{{ $cairCount }}</span>
                </a>
                <a href="{{ route('keuangan.anggaran.index', ['tab' => 'cek_pencairan']) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ $tab === 'cek_pencairan' ? 'bg-kp-blue-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50 font-semibold' }}">
                    <span>Cek Pencairan BP (DOS)</span>
                </a>
            </div>

            <!-- Form Filter & Pencarian -->
            <form method="GET" action="{{ route('keuangan.anggaran.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div class="sm:col-span-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Data</label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik no. BP, register PR, atau keperluan..." 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Filter Bidang (01 s/d 21)</label>
                    <select name="divisi" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs font-semibold">
                        <option value="">Semua Bidang Priangan</option>
                        @foreach($daftarDivisi as $key => $label)
                            <option value="{{ $key }}" {{ request('divisi') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3 flex items-center gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'divisi']))
                        <a href="{{ route('keuangan.anggaran.index', ['tab' => $tab]) }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center" title="Reset Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 4. Pure Data Table (Kolom Terpisah Standar Divisi Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            @php
                $canApproveAnggaran = Auth::user()?->hasRole('keuangan', 'admin_keuangan', 'administrator') ?? false;
            @endphp
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Pengajuan</th>
                            <th class="py-3 px-3.5">No. BP</th>
                            <th class="py-3 px-3.5">No. Register</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Bidang</th>
                            <th class="py-3 px-3.5">Kode COA</th>
                            <th class="py-3 px-3.5">Pemohon</th>
                            <th class="py-3 px-3.5">Keperluan</th>
                            <th class="py-3 px-3.5 text-right w-32">Diajukan</th>
                            <th class="py-3 px-3.5 text-right w-32">Disetujui (ACC)</th>
                            <th class="py-3 px-3.5 text-center">No. BKK</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($pengajuans as $key => $anggaran)
                            @php
                                $bp = $anggaran->buktiPengeluaran;
                                $kk = $bp?->kasKeluar;
                            @endphp
                            <tr id="row-anggaran-{{ $anggaran->id }}" class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ method_exists($pengajuans, 'firstItem') && $pengajuans->firstItem() ? ($pengajuans->firstItem() + $key) : ($key + 1) }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $anggaran->no_pengajuan }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-semibold text-slate-900">
                                    {{ $bp?->no_bp ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-600">
                                    {{ $bp?->no_register_anggaran ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-mono font-normal">
                                    {{ optional($anggaran->tanggal_pengajuan)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $anggaran->nama_divisi }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-600">
                                    {{ $bp?->kode_perkiraan ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-700 font-normal">
                                    {{ $bp?->pemohon ?: ($anggaran->creator->name ?? '-') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal truncate max-w-xs" title="{{ $anggaran->keterangan }}">
                                    {{ $anggaran->keterangan }}
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($anggaran->nominal_diajukan, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap" id="nominal-disetujui-{{ $anggaran->id }}">
                                    @if($anggaran->nominal_disetujui)
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($anggaran->nominal_disetujui, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="text-right font-mono text-sm text-slate-400 font-normal">-</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono text-xs">
                                    @if($anggaran->status === 'cair')
                                        <span class="text-kp-blue-700 font-semibold">{{ $kk?->no_bkk ?: ('KO-' . date('Ym') . str_pad($anggaran->id, 4, '0', STR_PAD_LEFT)) }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center" id="badge-status-{{ $anggaran->id }}">
                                    @if($anggaran->status === 'pending')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-amber-50 text-amber-800 border-amber-300">
                                            Menunggu ACC
                                        </span>
                                    @elseif($anggaran->status === 'approved')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200">
                                            Disetujui
                                        </span>
                                    @elseif($anggaran->status === 'rejected')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-slate-100 text-slate-700 border-slate-200">
                                            Ditolak
                                        </span>
                                    @elseif($anggaran->status === 'cair')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200">
                                            Selesai / Cair
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        @if(in_array($anggaran->status, ['approved', 'cair']))
                                            <a href="{{ route('keuangan.anggaran.voucher', $anggaran->id) }}" target="_blank"
                                               class="px-2.5 py-1 text-xs font-semibold text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg border border-slate-200 transition" 
                                               title="Cetak Bukti Pengeluaran Kas (Voucher BP)">
                                                Cetak BP
                                            </a>
                                        @endif

                                        @if($canApproveAnggaran && $anggaran->status === 'pending')
                                            <button type="button" 
                                                    onclick="bukaModalApprove({{ $anggaran->id }}, '{{ $anggaran->no_pengajuan }}', {{ $anggaran->nominal_diajukan }}, '{{ addslashes($anggaran->keterangan) }}')"
                                                    class="px-2.5 py-1 text-xs font-bold text-white bg-kp-blue-600 hover:bg-kp-blue-700 rounded-lg transition shadow-2xs cursor-pointer">
                                                Review ACC
                                            </button>
                                        @endif

                                        @if($anggaran->status === 'approved')
                                            @php
                                                $canCairkan = Auth::user()?->hasRole('kasir', 'anggaran', 'keuangan', 'admin_keuangan', 'administrator') ?? false;
                                            @endphp
                                            @if($canCairkan)
                                                <button type="button" 
                                                        onclick="bukaModalCairkan({{ $anggaran->id }}, '{{ $anggaran->no_pengajuan }}', {{ $anggaran->nominal_disetujui }}, '{{ $bp?->no_bp }}')"
                                                        class="px-2.5 py-1 text-xs font-bold text-slate-900 bg-amber-400 hover:bg-amber-500 rounded-lg transition shadow-2xs cursor-pointer">
                                                    Cairkan Kas
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="py-12 text-center text-slate-400">
                                    <p class="text-sm font-semibold text-slate-600">Tidak ada pengajuan anggaran pada kriteria ini.</p>
                                    <p class="text-xs text-slate-400 mt-1">Gunakan tombol "+ Entry BP Priangan" di kanan atas untuk membuat usulan anggaran baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($pengajuans, 'hasPages') && $pengajuans->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $pengajuans->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Persetujuan (Approve / Reject) -->
    <div id="modalApprove" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in duration-150">
            <h3 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">
                Persetujuan Otorisasi Anggaran (ACC)
            </h3>
            <p class="text-xs text-slate-500 mt-2">
                Verifikasi nomor usulan <strong id="modalNoPengajuan" class="text-kp-blue-700 font-mono"></strong>
            </p>

            <form id="formApprove" method="POST" action="" class="mt-4 space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Diajukan:</label>
                    <div id="modalNominalDiajukan" class="text-lg font-bold font-mono text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                        Rp 0
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Disetujui (ACC):</label>
                    <input type="number" name="nominal_disetujui" id="inputNominalDisetujui" required min="1000" step="1000"
                           class="w-full h-11 text-sm font-bold font-mono rounded-xl border border-slate-300 px-3.5 bg-white text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Persetujuan (Opsional):</label>
                    <textarea name="catatan_approval" rows="2" placeholder="Keterangan persetujuan..." 
                              class="w-full text-xs rounded-xl border border-slate-300 p-2.5 bg-white text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600"></textarea>
                </div>

                <div class="pt-3 flex items-center justify-between gap-2 border-t border-slate-100">
                    <button type="button" onclick="prosesTolakAnggaran()" class="px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 rounded-xl transition border border-slate-300 cursor-pointer">
                        ✕ Tolak Usulan
                    </button>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="tutupModalApprove()" class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-kp-blue-600 hover:bg-kp-blue-700 rounded-xl transition shadow-xs cursor-pointer">
                            ✓ Setujui (ACC)
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pencairan Kasir (BKK) -->
    <div id="modalCairkan" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in duration-150">
            <h3 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">
                Pencairan Kasir &amp; Terbitkan BKK
            </h3>
            <p class="text-xs text-slate-500 mt-2">
                Realisasi kas keluar untuk Bukti Pengeluaran <strong id="modalCairBpNo" class="text-kp-blue-700 font-mono"></strong>
            </p>

            <form id="formCairkan" method="POST" action="" class="mt-4 space-y-3.5">
                @csrf
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <span class="text-xs text-slate-600 font-medium block">Nominal yang Dicairkan Kasir:</span>
                    <span id="modalCairNominal" class="text-xl font-bold font-mono text-kp-blue-700 block mt-0.5">Rp 0</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Pengeluaran Kas:</label>
                    <select name="metode_bayar" class="w-full h-11 text-xs font-semibold rounded-xl border border-slate-300 px-3 bg-white text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                        <option value="tunai">Tunai (Kas Fisik Kasir / KO Tunai)</option>
                        <option value="transfer">Transfer Bank (BJB Operasional / Mutasi Bank)</option>
                    </select>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="tutupModalCairkan()" class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-slate-900 bg-amber-400 hover:bg-amber-500 rounded-xl transition shadow-xs cursor-pointer">
                        ✓ Terbitkan BKK &amp; Cairkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function formatRupiah(num) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
        }

        let currentAnggaranId = null;

        function bukaModalApprove(id, noPengajuan, nominalDiajukan, keterangan) {
            currentAnggaranId = id;
            document.getElementById('modalNoPengajuan').textContent = noPengajuan;
            document.getElementById('modalNominalDiajukan').textContent = formatRupiah(nominalDiajukan);
            document.getElementById('inputNominalDisetujui').value = Math.round(nominalDiajukan);
            document.getElementById('formApprove').action = "{{ url('keuangan/anggaran') }}/" + id + "/approve";
            document.getElementById('modalApprove').classList.remove('hidden');
        }

        function tutupModalApprove() {
            document.getElementById('modalApprove').classList.add('hidden');
            currentAnggaranId = null;
        }

        function prosesTolakAnggaran() {
            if (!currentAnggaranId) return;
            AppAlert.confirm({
                title: 'Tolak Pengajuan Anggaran?',
                text: 'Apakah Anda yakin ingin menolak permohonan pengajuan anggaran ini?',
                theme: 'rose',
                icon: 'cancel',
                confirmText: 'Ya, Tolak Anggaran',
                onConfirm: function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ url('keuangan/anggaran') }}/" + currentAnggaranId + "/reject";
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = "{{ csrf_token() }}";
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function bukaModalCairkan(id, noPengajuan, nominalDisetujui, noBp) {
            document.getElementById('modalCairBpNo').textContent = (noBp && noBp !== '') ? noBp : noPengajuan;
            document.getElementById('modalCairNominal').textContent = formatRupiah(nominalDisetujui);
            document.getElementById('formCairkan').action = "{{ url('keuangan/anggaran') }}/" + id + "/cairkan";
            document.getElementById('modalCairkan').classList.remove('hidden');
        }

        function tutupModalCairkan() {
            document.getElementById('modalCairkan').classList.add('hidden');
        }
    </script>
</x-app-layout>
