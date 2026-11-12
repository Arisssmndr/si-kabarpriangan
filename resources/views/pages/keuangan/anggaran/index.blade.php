<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman Anggaran Terpadu -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <h1 class="text-xl font-extrabold text-blue-950 tracking-tight">
                            PENGAJUAN ANGGARAN &amp; REALISASI KAS (BP PRIANGAN)
                        </h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Alokasi pos bidang, persetujuan register anggaran (PR), dan realisasi kas keluar (BKK KO) terintegrasi ke buku kas
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <!-- Tombol Cetak Rekap BP Priangan (Sesuai Menu DOS CETAK) -->
                <a href="{{ route('keuangan.anggaran.rekap-bp') }}" target="_blank"
                   class="h-9 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold flex items-center shadow-xs transition">
                    <span>Cetak Rekap BP Priangan</span>
                </a>

                <!-- Tombol Entry BP Priangan Baru (Aksen Kuning 10% - Call to Action Utama) -->
                <a href="{{ route('keuangan.anggaran.create') }}" 
                   class="h-9 px-4 rounded-xl bg-amber-400 hover:bg-amber-300 text-amber-950 border border-amber-500/30 text-xs font-black flex items-center shadow-xs transition cursor-pointer">
                    <span>+ Entry BP Priangan</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between">
                <span>✕ {{ session('error') }}</span>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- KPI Ringkas Anggaran & Kas Keluar (Aturan 60% Putih, 30% Biru, 10% Kuning) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1 (60% Putih/Netral) -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono">Total Usulan BP Diajukan</span>
                <div class="mt-2 text-2xl font-black text-slate-900 tracking-tight font-mono">
                    Rp {{ number_format($totalDiajukan, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    Seluruh usulan dari unit bidang Priangan
                </div>
            </div>

            <!-- Card 2 (30% Biru) -->
            <div class="bg-white border border-blue-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 font-mono">Total Dana Cair (Keluar Kas)</span>
                <div class="mt-2 text-2xl font-black text-blue-700 tracking-tight font-mono">
                    Rp {{ number_format($totalDicairkan, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-blue-800 font-semibold">
                    BKK terbit &amp; saldo kas terpotong
                </div>
            </div>

            <!-- Card 3 (10% Kuning Aksen - Highlight Status Antrean/Pending) -->
            <div class="bg-white border border-amber-300 bg-amber-50/40 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-800 font-mono">Status Antrean Pencairan</span>
                <div class="mt-2 text-2xl font-black text-amber-600 tracking-tight font-mono">
                    {{ $approvedCount }} <span class="text-xs font-bold text-amber-950">Siap Cair</span> &bull; {{ $pendingCount }} <span class="text-xs font-normal text-amber-800">Pending</span>
                </div>
                <div class="mt-2 text-xs text-amber-900 font-semibold">
                    Register PR aktif siap diproses Kasir
                </div>
            </div>

            <!-- Card 4 (30% Biru) -->
            <div class="bg-white border border-blue-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 font-mono">Saldo Kas Tersedia (Treasury)</span>
                <div class="mt-2 text-2xl font-black text-blue-600 tracking-tight font-mono">
                    Rp {{ number_format($currentKasSaldo, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                    <span>Likuiditas Kasir</span>
                    <span class="text-blue-700 font-bold">Kas Terbuka</span>
                </div>
            </div>
        </div>

        <!-- Filter & Submenu Bar (Sesuai Menu DOS ANGGARAN.EXE) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs space-y-4">
            <!-- Navigasi Tab Sesuai Fungsi DOS -->
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3 overflow-x-auto text-xs font-bold">
                <a href="{{ route('keuangan.anggaran.index', ['tab' => 'semua']) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ $tab === 'semua' ? 'bg-blue-600 text-white shadow-xs font-extrabold' : 'bg-slate-100 text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                    <span>Semua Anggaran &amp; BP</span>
                </a>
                <a href="{{ route('keuangan.anggaran.index', ['tab' => 'pending']) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ $tab === 'pending' ? 'bg-amber-400 text-amber-950 border border-amber-500/30 shadow-xs font-black' : 'bg-slate-100 text-slate-600 hover:bg-amber-50 hover:text-amber-800' }}">
                    <span>Menunggu Review / ACC</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'pending' ? 'bg-amber-950 text-amber-100' : 'bg-amber-100 text-amber-950 border border-amber-300' }}">{{ $pendingCount }}</span>
                </a>
                <a href="{{ route('keuangan.anggaran.index', ['tab' => 'approved']) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ $tab === 'approved' ? 'bg-blue-600 text-white shadow-xs font-extrabold' : 'bg-slate-100 text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                    <span>Siap Dicairkan ke Kas</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'approved' ? 'bg-blue-800 text-white' : 'bg-blue-100 text-blue-800' }}">{{ $approvedCount }}</span>
                </a>
                <a href="{{ route('keuangan.anggaran.index', ['tab' => 'cair']) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ $tab === 'cair' ? 'bg-blue-600 text-white shadow-xs font-extrabold' : 'bg-slate-100 text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                    <span>Sudah Cair (BKK Terbit)</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'cair' ? 'bg-blue-800 text-white' : 'bg-blue-100 text-blue-800' }}">{{ $cairCount }}</span>
                </a>
                <a href="{{ route('keuangan.anggaran.index', ['tab' => 'cek_pencairan']) }}" 
                   class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ $tab === 'cek_pencairan' ? 'bg-blue-600 text-white shadow-xs font-extrabold' : 'bg-slate-100 text-slate-700 hover:bg-blue-50 hover:text-blue-700' }}">
                    <span>Cek Pencairan BP (DOS FUNGSI)</span>
                </a>
            </div>

            <!-- Form Pencarian & Filter Divisi -->
            <form method="GET" action="{{ route('keuangan.anggaran.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div class="sm:col-span-4">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Cari No. BP / Register PR / Keterangan</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik kata kunci..." 
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Filter Bidang (01 s/d 07)</label>
                    <select name="divisi" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 font-semibold text-slate-800">
                        <option value="">Semua Bidang Priangan</option>
                        @foreach($daftarDivisi as $key => $label)
                            <option value="{{ $key }}" {{ request('divisi') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-4 flex items-center gap-2">
                    <button type="submit" class="w-full h-9 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer">
                        Tampilkan Data
                    </button>
                    @if(request()->hasAny(['q', 'divisi']))
                        <a href="{{ route('keuangan.anggaran.index', ['tab' => $tab]) }}" class="h-9 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center justify-center transition" title="Reset Filter">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Pengajuan Anggaran & Bukti Pengeluaran (BP) -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div class="text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                    DAFTAR ANGGARAN &amp; BUKTI PENGELUARAN (BP) &bull; {{ strtoupper(str_replace('_', ' ', $tab)) }}
                </div>
                <div class="text-xs text-slate-500 font-medium">
                    Total: <strong class="text-slate-900">{{ $pengajuans->total() }}</strong> dokumen ditemukan
                </div>
            </div>

            @php
                $canApproveAnggaran = Auth::user()?->hasRole('keuangan', 'admin_keuangan', 'administrator') ?? false;
            @endphp
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-blue-50/70 text-[10px] uppercase tracking-wider text-blue-950 font-black border-b border-blue-200">
                        <tr>
                            <th class="py-3 px-4">No. Dokumen &amp; BP</th>
                            <th class="py-3 px-4">Bidang &amp; Perkiraan</th>
                            <th class="py-3 px-4">Pemohon &amp; Keperluan</th>
                            <th class="py-3 px-4 text-right">Nominal Diajukan</th>
                            <th class="py-3 px-4 text-right">Nominal Disetujui (ACC)</th>
                            <th class="py-3 px-4 text-center">Realisasi &amp; Kas (BKK)</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">
                                {{ $canApproveAnggaran ? 'Otorisasi (ACC) & Aksi' : 'Pencairan Kasir & BKK' }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($pengajuans as $anggaran)
                            @php
                                $bp = $anggaran->buktiPengeluaran;
                                $kk = $bp?->kasKeluar;
                            @endphp
                            <tr id="row-anggaran-{{ $anggaran->id }}" class="hover:bg-blue-50/20 transition duration-150">
                                <td class="py-3.5 px-4">
                                    <div class="font-mono font-bold text-slate-900">
                                        {{ $anggaran->no_pengajuan }}
                                    </div>
                                    @if($bp)
                                        <div class="flex items-center gap-1 mt-1 flex-wrap">
                                            <span class="inline-block px-2 py-0.2 rounded text-[10px] font-mono font-bold bg-blue-50 text-blue-800 border border-blue-200" title="Nomor Bukti Pengeluaran DOS">
                                                {{ $bp->no_bp }}
                                            </span>
                                            @if($bp->no_register_anggaran)
                                                <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-mono font-semibold bg-blue-50 text-blue-700 border border-blue-200" title="Nomor Register Anggaran">
                                                    {{ $bp->no_register_anggaran }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">
                                        {{ $anggaran->nama_divisi }}
                                    </div>
                                    <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                                        COA: {{ $bp?->kode_perkiraan ?: '6.00.01.00.01.04' }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="text-[11px] text-slate-400 font-mono">
                                        Tgl: {{ $anggaran->tanggal_pengajuan->format('d/m/Y') }} &bull; Oleh: <strong>{{ $bp?->pemohon ?: ($anggaran->creator->name ?? 'Staf') }}</strong>
                                    </div>
                                    <p class="text-xs text-slate-800 mt-1 line-clamp-2 leading-relaxed font-normal">
                                        {{ $anggaran->keterangan }}
                                    </p>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($anggaran->nominal_diajukan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold" id="nominal-disetujui-{{ $anggaran->id }}">
                                    @if($anggaran->nominal_disetujui)
                                        <span class="text-blue-950 font-black">Rp {{ number_format($anggaran->nominal_disetujui, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($anggaran->status === 'cair')
                                        <div class="inline-flex flex-col items-center">
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-900 border border-blue-200 font-mono">
                                                BKK: {{ $kk?->no_bkk ?: ('KO-' . date('Ym') . str_pad($anggaran->id, 4, '0', STR_PAD_LEFT)) }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-mono mt-0.5">
                                                Cair: {{ $anggaran->tanggal_cair ? $anggaran->tanggal_cair->format('d/m/Y') : date('d/m/Y') }}
                                            </span>
                                        </div>
                                    @elseif($anggaran->status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                            Siap Cair ke Kas
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400 font-mono">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center" id="status-cell-{{ $anggaran->id }}">
                                    @if($anggaran->status === 'pending')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-950 border border-amber-300">
                                            Pending (ACC)
                                        </span>
                                    @elseif($anggaran->status === 'approved')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                            Approved
                                        </span>
                                    @elseif($anggaran->status === 'cair')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                            Cair (Keluar Kas)
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center" id="action-cell-{{ $anggaran->id }}">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        @if($anggaran->status === 'pending')
                                            @if($canApproveAnggaran)
                                                <!-- Tombol ACC Khusus Manajer Keuangan / Admin -->
                                                <form id="form-approve-{{ $anggaran->id }}" action="{{ route('keuangan.anggaran.approve', $anggaran->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="nominal_disetujui" id="input-approve-nominal-{{ $anggaran->id }}" value="{{ (int)$anggaran->nominal_diajukan }}">
                                                    <button type="button"
                                                            onclick="openAnggaranModal({
                                                                type: 'approve',
                                                                formId: 'form-approve-{{ $anggaran->id }}',
                                                                anggaranId: '{{ $anggaran->id }}',
                                                                noPengajuan: '{{ $anggaran->no_pengajuan }}',
                                                                noBp: '{{ $bp?->no_bp ?? '-' }}',
                                                                divisi: '{{ addslashes($anggaran->nama_divisi) }}',
                                                                nominalFormatted: 'Rp {{ number_format($anggaran->nominal_diajukan, 0, ',', '.') }}',
                                                                nominalRaw: {{ (int)$anggaran->nominal_diajukan }}
                                                            })"
                                                            class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold transition shadow-2xs cursor-pointer">
                                                        Setujui (ACC)
                                                    </button>
                                                </form>

                                                <!-- Tombol Tolak Khusus Manajer Keuangan / Admin -->
                                                <form id="form-reject-{{ $anggaran->id }}" action="{{ route('keuangan.anggaran.reject', $anggaran->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="button"
                                                            onclick="openAnggaranModal({
                                                                type: 'reject',
                                                                formId: 'form-reject-{{ $anggaran->id }}',
                                                                anggaranId: '{{ $anggaran->id }}',
                                                                noPengajuan: '{{ $anggaran->no_pengajuan }}',
                                                                noBp: '{{ $bp?->no_bp ?? '-' }}',
                                                                divisi: '{{ addslashes($anggaran->nama_divisi) }}',
                                                                nominalFormatted: 'Rp {{ number_format($anggaran->nominal_diajukan, 0, ',', '.') }}',
                                                                nominalRaw: {{ (int)$anggaran->nominal_diajukan }}
                                                            })"
                                                            class="px-2.5 py-1.5 rounded-lg border border-slate-300 hover:bg-rose-50 text-rose-700 text-[11px] font-bold transition cursor-pointer">
                                                        Tolak
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 text-[11px] font-semibold">
                                                    Menunggu ACC Keuangan
                                                </span>
                                            @endif
                                        @elseif($anggaran->status === 'approved')
                                            <!-- Tombol Pencairan ke Kasir & Terbitkan BKK -->
                                            <form id="form-cairkan-{{ $anggaran->id }}" action="{{ route('keuangan.anggaran.cairkan', $anggaran->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="tanggal_cair" id="input-cair-tanggal-{{ $anggaran->id }}" value="{{ date('Y-m-d') }}">
                                                <input type="hidden" name="metode_bayar" id="input-cair-metode-{{ $anggaran->id }}" value="kas_fisik">
                                                <button type="button"
                                                        onclick="openAnggaranModal({
                                                            type: 'cairkan',
                                                            formId: 'form-cairkan-{{ $anggaran->id }}',
                                                            anggaranId: '{{ $anggaran->id }}',
                                                            noPengajuan: '{{ $anggaran->no_pengajuan }}',
                                                            noBp: '{{ $bp?->no_bp ?? '-' }}',
                                                            divisi: '{{ addslashes($anggaran->nama_divisi) }}',
                                                            nominalFormatted: 'Rp {{ number_format($anggaran->nominal_disetujui ?: $anggaran->nominal_diajukan, 0, ',', '.') }}',
                                                            nominalRaw: {{ (int)($anggaran->nominal_disetujui ?: $anggaran->nominal_diajukan) }}
                                                        })"
                                                        class="px-3 py-1.5 rounded-lg bg-amber-400 hover:bg-amber-300 text-amber-950 border border-amber-500/30 text-[11px] font-black transition shadow-2xs cursor-pointer">
                                                    Cairkan ke Kasir &amp; Terbitkan BKK
                                                </button>
                                            </form>
                                        @elseif($anggaran->status === 'cair')
                                            <!-- Tombol Cetak Bukti Voucher Kas Keluar / BKK -->
                                            <a href="{{ route('keuangan.anggaran.voucher', $anggaran->id) }}" target="_blank"
                                               class="px-2.5 py-1.5 rounded-lg border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-800 text-[11px] font-bold inline-flex items-center transition shadow-2xs">
                                                Cetak BKK
                                            </a>
                                        @else
                                            <span class="text-[11px] text-slate-400">Ditolak Keuangan</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <p class="text-sm font-semibold text-slate-600">Belum ada pengajuan anggaran / BP Priangan.</p>
                                    <p class="text-xs text-slate-400 mt-1">Gunakan tombol "+ Entry BP Priangan" untuk mendaftarkan permohonan baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pengajuans->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $pengajuans->links() }}
                </div>
            @endif
        </div>

        <!-- ==========================================================
             CUSTOM MODAL KONFIRMASI WORKFLOW ANGGARAN (ACC / TOLAK / CAIRKAN)
             ========================================================== -->
        <div id="anggaran-confirm-modal"
             style="display: none; position: fixed; inset: 0; z-index: 9999; background-color: rgba(15, 23, 42, 0.55); backdrop-filter: blur(2px); align-items: center; justify-content: center; padding: 1rem;"
             onclick="if(event.target === this) closeAnggaranModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-lg w-full max-w-md overflow-hidden" style="max-width: 28rem; width: 100%;">
                <!-- Header Modal -->
                <div id="modal-header-bar" class="px-6 py-4 bg-blue-50/70 border-b border-blue-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span id="modal-badge-dot" class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        <h3 id="modal-title" class="text-sm font-black text-blue-950 uppercase tracking-wider">
                            Konfirmasi Tindakan
                        </h3>
                    </div>
                    <button type="button" onclick="closeAnggaranModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold leading-none cursor-pointer">&times;</button>
                </div>

                <!-- Body Modal -->
                <div class="p-6 space-y-4">
                    <p id="modal-desc" class="text-xs text-slate-600 leading-relaxed font-medium"></p>

                    <!-- Ringkasan Dokumen -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-semibold">No. Pengajuan / BP:</span>
                            <span id="modal-doc-no" class="font-mono font-bold text-slate-900">-</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-semibold">Bidang / Bagian:</span>
                            <span id="modal-divisi" class="font-bold text-blue-950">-</span>
                        </div>
                        <div class="flex items-center justify-between pt-1.5 border-t border-slate-200">
                            <span class="text-slate-500 font-semibold">Nominal Usulan:</span>
                            <span id="modal-nominal" class="font-mono font-black text-blue-700 text-sm">-</span>
                        </div>
                    </div>

                    <!-- Field Tambahan Khusus ACC (Nominal Disetujui) -->
                    <div id="modal-section-approve" style="display: none;" class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            Nominal Disetujui / ACC (Rp)
                        </label>
                        <input type="number" id="modal-input-nominal-acc" min="1000" step="any"
                               class="w-full rounded-xl border-slate-300 text-sm font-mono font-bold text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-[11px] text-slate-400">Sistem akan otomatis menerbitkan Nomor Register Anggaran (PR).</p>
                    </div>

                    <!-- Field Tambahan Khusus Pencairan BKK -->
                    <div id="modal-section-cairkan" style="display: none;" class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Realisasi Kas (BKK)</label>
                            <input type="date" id="modal-input-tanggal-cair" value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-slate-300 text-xs font-mono font-bold text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pengeluaran Kas</label>
                            <select id="modal-input-metode-cair"
                                    class="w-full rounded-xl border-slate-300 text-xs font-bold text-slate-800 focus:border-blue-500 focus:ring-blue-500">
                                <option value="kas_fisik">Kas Fisik / Kas Tunai Kasir</option>
                                <option value="transfer_bank">Transfer Rekening Bank</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeAnggaranModal()"
                            class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" id="modal-confirm-btn" onclick="submitAnggaranModal()"
                            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                        Ya, Konfirmasi
                    </button>
                </div>
            </div>
        </div>

        <script>
            let activeAnggaranModal = null;

            function openAnggaranModal(data) {
                activeAnggaranModal = data;
                const modal = document.getElementById('anggaran-confirm-modal');
                const title = document.getElementById('modal-title');
                const desc = document.getElementById('modal-desc');
                const docNo = document.getElementById('modal-doc-no');
                const divisi = document.getElementById('modal-divisi');
                const nominal = document.getElementById('modal-nominal');
                const secApprove = document.getElementById('modal-section-approve');
                const secCairkan = document.getElementById('modal-section-cairkan');
                const confirmBtn = document.getElementById('modal-confirm-btn');
                const headerBar = document.getElementById('modal-header-bar');
                const badgeDot = document.getElementById('modal-badge-dot');

                docNo.textContent = data.noPengajuan + (data.noBp && data.noBp !== '-' ? ' / ' + data.noBp : '');
                divisi.textContent = data.divisi;
                nominal.textContent = data.nominalFormatted;

                secApprove.style.display = 'none';
                secCairkan.style.display = 'none';

                if (data.type === 'approve') {
                    title.textContent = 'Setujui Pengajuan (ACC) & Terbitkan PR';
                    desc.textContent = 'Apakah Anda yakin menyetujui pengajuan anggaran ini? Sistem akan otomatis menerbitkan Nomor Register Anggaran (PR) agar siap dicairkan oleh Kasir.';
                    secApprove.style.display = 'block';
                    document.getElementById('modal-input-nominal-acc').value = data.nominalRaw;
                    headerBar.className = 'px-6 py-4 bg-blue-50/70 border-b border-blue-200 flex items-center justify-between';
                    badgeDot.className = 'w-2.5 h-2.5 rounded-full bg-blue-600';
                    confirmBtn.className = 'px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-xs cursor-pointer';
                    confirmBtn.textContent = 'Ya, Setujui & Terbitkan PR';
                } else if (data.type === 'cairkan') {
                    title.textContent = 'Pencairan Kas & Terbitkan BKK (KO)';
                    desc.textContent = 'Konfirmasi pencairan dana ke pemohon. Sistem akan otomatis menerbitkan Bukti Kas Keluar (BKK) dan memotong saldo Buku Kas secara real-time.';
                    secCairkan.style.display = 'block';
                    headerBar.className = 'px-6 py-4 bg-amber-50 border-b border-amber-200 flex items-center justify-between';
                    badgeDot.className = 'w-2.5 h-2.5 rounded-full bg-amber-500';
                    confirmBtn.className = 'px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-amber-950 border border-amber-500/30 text-xs font-black transition shadow-xs cursor-pointer';
                    confirmBtn.textContent = 'Ya, Cairkan & Terbitkan BKK';
                } else if (data.type === 'reject') {
                    title.textContent = 'Tolak Pengajuan Anggaran';
                    desc.textContent = 'Apakah Anda yakin ingin menolak permohonan anggaran ini? Dokumen yang ditolak tidak dapat dicairkan ke Kas.';
                    headerBar.className = 'px-6 py-4 bg-rose-50 border-b border-rose-200 flex items-center justify-between';
                    badgeDot.className = 'w-2.5 h-2.5 rounded-full bg-rose-600';
                    confirmBtn.className = 'px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs cursor-pointer';
                    confirmBtn.textContent = 'Ya, Tolak Pengajuan';
                }

                modal.style.display = 'flex';
            }

            function closeAnggaranModal() {
                const modal = document.getElementById('anggaran-confirm-modal');
                if (modal) modal.style.display = 'none';
                activeAnggaranModal = null;
            }

            function submitAnggaranModal() {
                if (!activeAnggaranModal) return;
                const form = document.getElementById(activeAnggaranModal.formId);
                if (!form) return;

                if (activeAnggaranModal.type === 'approve') {
                    const accInput = document.getElementById('input-approve-nominal-' + activeAnggaranModal.anggaranId);
                    const modalAccVal = document.getElementById('modal-input-nominal-acc').value;
                    if (accInput && modalAccVal) {
                        accInput.value = modalAccVal;
                    }
                } else if (activeAnggaranModal.type === 'cairkan') {
                    const tglInput = document.getElementById('input-cair-tanggal-' + activeAnggaranModal.anggaranId);
                    const metodeInput = document.getElementById('input-cair-metode-' + activeAnggaranModal.anggaranId);
                    if (tglInput) tglInput.value = document.getElementById('modal-input-tanggal-cair').value;
                    if (metodeInput) metodeInput.value = document.getElementById('modal-input-metode-cair').value;
                }

                form.submit();
            }
        </script>
    </div>
</x-app-layout>
