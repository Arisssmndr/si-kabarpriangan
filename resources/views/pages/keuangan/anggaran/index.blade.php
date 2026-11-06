<x-app-layout>
    <div class="space-y-6" x-data="{
        showCairModal: false,
        actionId: '',
        actionNo: '',
        actionNoBp: '',
        actionNoRegister: '',
        actionDivisi: '',
        actionNominalDisetujui: 0,
        openCair(id, no, noBp, noRegister, divisi, nominalDisetujui) {
            this.actionId = id;
            this.actionNo = no;
            this.actionNoBp = noBp;
            this.actionNoRegister = noRegister;
            this.actionDivisi = divisi;
            this.actionNominalDisetujui = nominalDisetujui;
            this.showCairModal = true;
            this.$nextTick(() => {
                const f = document.getElementById('form-cairkan-kas');
                if (f) f.action = '{{ url('keuangan/anggaran') }}/' + id + '/cairkan';
            });
        },
        formatRupiah(angka) {
            if (!angka) return '0';
            return new Intl.NumberFormat('id-ID').format(angka);
        }
    }">

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
                            <th class="py-3 px-4 text-center">Aksi Workflow</th>
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
                                            <!-- Tombol ACC Langsung (Form POST Resmi) -->
                                            <form action="{{ route('keuangan.anggaran.approve', $anggaran->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Konfirmasi Setujui (ACC) Pengajuan Anggaran {{ $anggaran->no_pengajuan }} (Rp {{ number_format($anggaran->nominal_diajukan, 0, ',', '.') }}) dan terbitkan Register PR?');">
                                                @csrf
                                                <input type="hidden" name="nominal_disetujui" value="{{ (int)$anggaran->nominal_diajukan }}">
                                                <button type="submit" 
                                                        class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold transition shadow-2xs cursor-pointer">
                                                    Setujui (ACC)
                                                </button>
                                            </form>

                                            <!-- Tombol Tolak Langsung -->
                                            <form action="{{ route('keuangan.anggaran.reject', $anggaran->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menolak pengajuan anggaran {{ $anggaran->no_pengajuan }}?');">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-2.5 py-1.5 rounded-lg border border-slate-300 hover:bg-rose-50 text-rose-700 text-[11px] font-bold transition cursor-pointer">
                                                    Tolak
                                                </button>
                                            </form>
                                        @elseif($anggaran->status === 'approved')
                                            <!-- Tombol Pencairan ke Kas (Aksen Kuning 10% untuk Aksi Penting Kasir) -->
                                            <button type="button" 
                                                    @click="openCair({{ $anggaran->id }}, '{{ $anggaran->no_pengajuan }}', '{{ $bp?->no_bp }}', '{{ $bp?->no_register_anggaran }}', '{{ $anggaran->nama_divisi }}', {{ $anggaran->nominal_disetujui }})"
                                                    class="px-3 py-1 rounded-lg bg-amber-400 hover:bg-amber-300 text-amber-950 border border-amber-500/30 text-[11px] font-black transition shadow-2xs cursor-pointer">
                                                Cairkan ke Kas
                                            </button>
                                        @elseif($anggaran->status === 'cair')
                                            <!-- Tombol Cetak Bukti Voucher Kas Keluar / BKK -->
                                            <a href="{{ route('keuangan.anggaran.voucher', $anggaran->id) }}" target="_blank"
                                               class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-blue-50 text-blue-700 hover:border-blue-300 text-[11px] font-bold inline-flex items-center transition shadow-2xs">
                                                Voucher BKK
                                            </a>
                                        @else
                                            <span class="text-[11px] text-slate-400">Tidak ada aksi</span>
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

        <!-- ============================================== -->
        <!-- MODAL PENCAIRAN KE KAS (BKK & CASH OUT)       -->
        <!-- ============================================== -->
        <div x-cloak x-show="showCairModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.outside="showCairModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-blue-950">
                        REALISASI PENCAIRAN KAS KELUAR (BKK)
                    </h3>
                    <button type="button" @click="showCairModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>

                <form id="form-cairkan-kas" :action="'{{ url('keuangan/anggaran') }}/' + actionId + '/cairkan'" 
                      @submit="if (!$el.action || $el.action.endsWith('/anggaran')) { $el.action = '{{ url('keuangan/anggaran') }}/' + actionId + '/cairkan'; }" 
                      method="POST" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="anggaran_id" :value="actionId" id="cair-anggaran-id">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nomor Dokumen:</span>
                            <span class="font-mono font-bold text-slate-900" x-text="actionNo"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">No. BP &amp; Register PR:</span>
                            <span class="font-mono font-bold text-blue-700"><span x-text="actionNoBp"></span> &bull; <span x-text="actionNoRegister"></span></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Bidang Pengusul:</span>
                            <span class="font-bold text-slate-800" x-text="actionDivisi"></span>
                        </div>
                        <div class="flex items-center justify-between pt-1.5 border-t border-slate-200">
                            <span class="text-slate-500 font-bold">Dana yang Dicairkan:</span>
                            <span class="font-mono font-black text-blue-700 text-base">Rp <span x-text="formatRupiah(actionNominalDisetujui)"></span></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pencairan</label>
                            <input type="date" name="tanggal_cair" value="{{ date('Y-m-d') }}" required
                                   class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pencairan</label>
                            <select name="metode_bayar" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 font-bold">
                                <option value="kas_fisik">Kas Fisik (Tunai)</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900 leading-relaxed">
                        <strong>Otomatisasi Sistem KAS:</strong> Konfirmasi ini akan otomatis menerbitkan <strong>Nomor Bukti Kas Keluar (BKK KO-YYYYMMXXXX)</strong>, memotong saldo kas, dan mencatat mutasi <strong>Kas Keluar</strong> pada Buku Kas Treasury.
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showCairModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-amber-950 border border-amber-500/30 text-xs font-black shadow-xs transition cursor-pointer">
                            Konfirmasi Cairkan Kas (Terbitkan BKK)
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
