<x-app-layout>
    <div class="space-y-6" x-data="{
        showApproveModal: false,
        showRejectModal: false,
        showCairModal: false,
        actionId: '',
        actionNo: '',
        actionDivisi: '',
        actionNominalDiajukan: 0,
        actionNominalDisetujui: 0,
        openApprove(id, no, divisi, nominal) {
            this.actionId = id;
            this.actionNo = no;
            this.actionDivisi = divisi;
            this.actionNominalDiajukan = nominal;
            this.actionNominalDisetujui = nominal;
            this.showApproveModal = true;
        },
        openReject(id, no, divisi) {
            this.actionId = id;
            this.actionNo = no;
            this.actionDivisi = divisi;
            this.showRejectModal = true;
        },
        openCair(id, no, divisi, nominalDisetujui) {
            this.actionId = id;
            this.actionNo = no;
            this.actionDivisi = divisi;
            this.actionNominalDisetujui = nominalDisetujui;
            this.showCairModal = true;
        },
        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    }">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            PENGAJUAN ANGGARAN &amp; CASH OUT
                        </h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Alokasi pos divisi, workflow persetujuan (approval), dan pencairan kas keluar operasional
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('keuangan.alur') }}" 
                   class="h-9 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center gap-1.5 shadow-2xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                    </svg>
                    <span>Diagram Alur SOP</span>
                </a>

                <a href="{{ route('keuangan.anggaran.create') }}" 
                   class="h-9 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Buat Pengajuan Baru</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <!-- KPI Ringkas Anggaran -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-purple-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-700 font-mono">Total Nominal Diajukan</span>
                <div class="mt-2 text-2xl font-black text-slate-900 tracking-tight font-mono">
                    Rp {{ number_format($totalDiajukan, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    Seluruh usulan dari 8 unit divisi
                </div>
            </div>

            <div class="bg-white border border-emerald-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 font-mono">Total Dana Dicairkan (Kas Keluar)</span>
                <div class="mt-2 text-2xl font-black text-emerald-600 tracking-tight font-mono">
                    Rp {{ number_format($totalDicairkan, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-emerald-700 font-semibold">
                    ✓ Telah dipotong dari buku kas
                </div>
            </div>

            <div class="bg-white border border-amber-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700 font-mono">Menunggu Persetujuan / Cair</span>
                <div class="mt-2 text-2xl font-black text-amber-600 tracking-tight">
                    {{ $pendingCount }} <span class="text-xs font-normal text-slate-500">Pending</span> &bull; {{ $approvedCount }} <span class="text-xs font-normal text-slate-500">Disetujui</span>
                </div>
                <div class="mt-2 text-xs text-amber-800 font-semibold">
                    Workflow verifikasi aktif
                </div>
            </div>

            <div class="bg-white border border-sky-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-sky-700 font-mono">Saldo Kas Saat Ini (Tersedia)</span>
                <div class="mt-2 text-2xl font-black text-sky-700 tracking-tight font-mono">
                    Rp {{ number_format($currentKasSaldo, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                    <span>Likuiditas Kasir</span>
                    <span class="text-emerald-600 font-bold">Siap Pakai</span>
                </div>
            </div>
        </div>

        <!-- Filter Pengajuan -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('keuangan.anggaran.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-5">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Filter Divisi / Unit Kerja</label>
                    <select name="divisi" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-purple-500">
                        <option value="">Semua Divisi (8 Pos Anggaran)</option>
                        @foreach($daftarDivisi as $key => $label)
                            <option value="{{ $key }}" {{ request('divisi') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Status Workflow</label>
                    <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-purple-500">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Menunggu Review)</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved (Siap Dicairkan Kasir)</option>
                        <option value="cair" {{ request('status') === 'cair' ? 'selected' : '' }}>Cair (Dana Sudah Keluar)</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected (Ditolak)</option>
                    </select>
                </div>

                <div class="sm:col-span-3 flex items-end gap-2">
                    <button type="submit" class="w-full h-9 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                        Terapkan Filter
                    </button>
                    @if(request()->hasAny(['divisi', 'status']))
                        <a href="{{ route('keuangan.anggaran.index') }}" class="h-9 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center justify-center transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Pengajuan Anggaran -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Pengajuan</th>
                            <th class="py-3 px-4">Divisi Pengusul</th>
                            <th class="py-3 px-4">Tanggal &amp; Keterangan</th>
                            <th class="py-3 px-4 text-right">Nominal Diajukan</th>
                            <th class="py-3 px-4 text-right">Nominal Disetujui</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi Workflow</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($pengajuans as $anggaran)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                    {{ $anggaran->no_pengajuan }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono tracking-wide bg-purple-50 text-purple-800 border border-purple-200">
                                        {{ $anggaran->nama_divisi }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="text-[11px] text-slate-400 font-mono">
                                        Tgl: {{ $anggaran->tanggal_pengajuan->format('d/m/Y') }}
                                        &bull; Oleh: {{ $anggaran->creator->name ?? 'Staf' }}
                                    </div>
                                    <p class="text-xs text-slate-800 mt-1 line-clamp-2 leading-relaxed font-normal">
                                        {{ $anggaran->keterangan }}
                                    </p>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($anggaran->nominal_diajukan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold">
                                    @if($anggaran->nominal_disetujui)
                                        <span class="text-emerald-700">Rp {{ number_format($anggaran->nominal_disetujui, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($anggaran->status === 'pending')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            ⏳ Pending
                                        </span>
                                    @elseif($anggaran->status === 'approved')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                            ✓ Disetujui
                                        </span>
                                    @elseif($anggaran->status === 'cair')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            💰 Cair (Keluar Kas)
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            ✕ Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        @if($anggaran->status === 'pending')
                                            <!-- Tombol Approve & Reject -->
                                            <button type="button" 
                                                    @click="openApprove({{ $anggaran->id }}, '{{ $anggaran->no_pengajuan }}', '{{ $anggaran->nama_divisi }}', {{ $anggaran->nominal_diajukan }})"
                                                    class="px-2.5 py-1 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold transition shadow-2xs">
                                                ✓ Setujui
                                            </button>
                                            <button type="button" 
                                                    @click="openReject({{ $anggaran->id }}, '{{ $anggaran->no_pengajuan }}', '{{ $anggaran->nama_divisi }}')"
                                                    class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-bold transition border border-rose-200">
                                                ✕ Tolak
                                            </button>
                                        @elseif($anggaran->status === 'approved')
                                            <!-- Tombol Pencairan Kasir -->
                                            <button type="button" 
                                                    @click="openCair({{ $anggaran->id }}, '{{ $anggaran->no_pengajuan }}', '{{ $anggaran->nama_divisi }}', {{ $anggaran->nominal_disetujui }})"
                                                    class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold transition shadow-2xs">
                                                💰 Cairkan Kasir ➔
                                            </button>
                                        @elseif($anggaran->status === 'cair')
                                            <!-- Tombol Cetak Bukti Voucher Kas Keluar -->
                                            <a href="{{ route('keuangan.anggaran.voucher', $anggaran->id) }}" target="_blank"
                                               class="px-2.5 py-1 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-700 text-[11px] font-bold inline-flex items-center gap-1 transition">
                                                🖨 Voucher Kas Keluar
                                            </a>
                                        @else
                                            <span class="text-[11px] text-slate-400">Tidak ada aksi</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">📊</div>
                                    <p class="text-sm font-semibold text-slate-600">Belum ada pengajuan anggaran.</p>
                                    <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Buat Pengajuan Baru" untuk mengusulkan alokasi.</p>
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
        <!-- MODAL 1: PERSETUJUAN (APPROVE)                -->
        <!-- ============================================== -->
        <div x-cloak x-show="showApproveModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.outside="showApproveModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <span class="text-blue-600">✓</span>
                        <span>PERSETUJUAN PENGAJUAN ANGGARAN</span>
                    </h3>
                    <button type="button" @click="showApproveModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>

                <form :action="'{{ url('keuangan/anggaran') }}/' + actionId + '/approve'" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nomor Pengajuan:</span>
                            <span class="font-mono font-bold text-slate-800" x-text="actionNo"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Divisi Pengusul:</span>
                            <span class="font-bold text-purple-700" x-text="actionDivisi"></span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-slate-200">
                            <span class="text-slate-500">Nominal Diajukan:</span>
                            <span class="font-mono font-bold text-slate-900">Rp <span x-text="formatRupiah(actionNominalDiajukan)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal yang Disetujui (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="nominal_disetujui" x-model="actionNominalDisetujui" min="1000" step="1000" required
                               class="w-full text-sm font-mono font-black rounded-xl border border-blue-400 px-3 py-2.5 bg-blue-50/40 focus:ring-2 focus:ring-blue-500 text-slate-900">
                        <span class="text-[10px] text-slate-500 mt-1 block">Pejabat dapat menyetujui penuh atau menyesuaikan nominal yang disetujui.</span>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showApproveModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition">
                            Konfirmasi Setujui ➔
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL 2: PENOLAKAN (REJECT)                   -->
        <!-- ============================================== -->
        <div x-cloak x-show="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.outside="showRejectModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-rose-600 flex items-center gap-2">
                        <span>✕</span>
                        <span>TOLAK PENGAJUAN ANGGARAN</span>
                    </h3>
                    <button type="button" @click="showRejectModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>

                <form :action="'{{ url('keuangan/anggaran') }}/' + actionId + '/reject'" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Apakah Anda yakin ingin menolak pengajuan anggaran <strong class="font-mono text-slate-900" x-text="actionNo"></strong> untuk divisi <strong class="text-purple-700" x-text="actionDivisi"></strong>? Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showRejectModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition">
                            Ya, Tolak Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL 3: PENCAIRAN KASIR (CASH OUT)           -->
        <!-- ============================================== -->
        <div x-cloak x-show="showCairModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.outside="showCairModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <span>💰</span>
                        <span>PENCAIRAN DANA KAS KELUAR</span>
                    </h3>
                    <button type="button" @click="showCairModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>

                <form :action="'{{ url('keuangan/anggaran') }}/' + actionId + '/cairkan'" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nomor Pengajuan:</span>
                            <span class="font-mono font-bold text-slate-800" x-text="actionNo"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Divisi Pengusul:</span>
                            <span class="font-bold text-purple-700" x-text="actionDivisi"></span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-slate-200">
                            <span class="text-slate-500">Dana yang Dicairkan:</span>
                            <span class="font-mono font-black text-emerald-700 text-sm">Rp <span x-text="formatRupiah(actionNominalDisetujui)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pencairan Kas</label>
                        <input type="date" name="tanggal_cair" value="{{ date('Y-m-d') }}" required
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900">
                        ⚠️ <strong>Pemberitahuan Kasir:</strong> Setelah Anda menekan tombol "Konfirmasi Cairkan", sistem akan secara otomatis memotong saldo Kas dan mencatat mutasi <strong>Kas Keluar</strong> sebesar nominal di atas pada Buku Kas.
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showCairModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                            Konfirmasi Cairkan Kas ➔
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
