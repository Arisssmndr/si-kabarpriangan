<x-app-layout>
    <div class="space-y-5" x-data="{
        showAjukanModal: false,
        showApproveModal: false,
        showRejectModal: false,
        selectedShift: {
            id: null,
            tanggal: '',
            kasir: '',
            saldo_fisik: 0,
            alasan: '',
            reopen_status: ''
        },
        openAjukan(shift) {
            this.selectedShift = shift;
            this.showAjukanModal = true;
        },
        openApprove(shift) {
            this.selectedShift = shift;
            this.showApproveModal = true;
        },
        openReject(shift) {
            this.selectedShift = shift;
            this.showRejectModal = true;
        }
    }">

        <!-- ============================================================== -->
        <!-- 1. HEADER HALAMAN (PALET 60-30-10 & ACTION BAR)                -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Buka Kembali Kas (Reopen Shift)
                        </h1>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-sky-50 text-kp-blue-700 border border-kp-blue-200">
                            Persetujuan Accounting
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Otorisasi pembukaan kembali shift kasir yang telah ditutup (closing) dengan verifikasi Bagian Keuangan.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                <a href="{{ route('kasir.memo.history') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98]">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Riwayat Memo Kasir</span>
                </a>
                <a href="{{ route('kasir.utilitas.closing') }}" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98]">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Ke Halaman Tutup Kas</span>
                </a>
            </div>
        </div>

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-3.5 bg-sky-50 border border-sky-200 rounded-xl text-xs font-semibold text-[#0E527B] flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-700 text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs font-semibold text-rose-800 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- SOP & Ketentuan Buka Kas (Information Box) -->
        <div class="bg-sky-50/70 border border-sky-200 rounded-xl p-4 text-xs text-slate-800 flex items-start gap-3 shadow-2xs">
            <svg class="w-5 h-5 text-kp-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="space-y-1">
                <div class="font-bold text-slate-900 text-xs uppercase tracking-wider">Standar Operasional Prosedur (SOP) Pembukaan Kembali Kas:</div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    1. Kasir tidak diperkenankan membuka kembali shift kas yang telah ditutup secara sepihak untuk mencegah manipulasi uang fisik laci dan data buku besar akuntansi.<br>
                    2. Kasir harus mengisi alasan pembukaan kembali kas (misal: keterlambatan input setoran dinas, koreksi salah posting BP).<br>
                    3. <strong>Persetujuan Resmi:</strong> Bagian Akuntansi / Manager Keuangan menyetujui langsung melalui akunnya atau menginput password otorisasi.
                </p>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 2. FILTER BAR TERPISAH (H-11 STANDAR IKLAN)                    -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs" x-data="{ periode: '{{ $periodeTipe ?? 'semua' }}' }">
            <form method="GET" action="{{ route('kasir.utilitas.buka-kas') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Cari Shift Kasir</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Ketik nama kasir atau alasan..."
                               class="w-full h-11 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Tipe Periode Filter -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Periode Waktu</label>
                    <select name="periode_tipe" 
                            x-model="periode" 
                            class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 font-semibold focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition cursor-pointer">
                        <option value="semua">Semua Waktu</option>
                        <option value="hari">Per Hari (Tanggal)</option>
                        <option value="bulan">Per Bulan</option>
                        <option value="tahun">Per Tahun</option>
                    </select>
                </div>

                <!-- Dynamic Input Tanggal / Bulan / Tahun -->
                <div class="sm:col-span-3">
                    <!-- Per Hari -->
                    <div x-show="periode === 'hari'">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Tanggal</label>
                        <input type="date" 
                               name="tanggal" 
                               value="{{ $tanggal ?? date('Y-m-d') }}" 
                               :disabled="periode !== 'hari'"
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                    </div>

                    <!-- Per Bulan -->
                    <div x-show="periode === 'bulan'" class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Bulan</label>
                            <select name="bulan" 
                                    :disabled="periode !== 'bulan'" 
                                    class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                                @foreach(['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'] as $blnNum => $blnLabel)
                                    <option value="{{ $blnNum }}" {{ (string)($bulan ?? date('m')) === (string)$blnNum ? 'selected' : '' }}>{{ $blnLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tahun</label>
                            <select name="tahun" 
                                    :disabled="periode !== 'bulan'" 
                                    class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                                @for($y = (int)date('Y') + 1; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ (int)($tahun ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Per Tahun -->
                    <div x-show="periode === 'tahun'">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Tahun</label>
                        <select name="tahun" 
                                :disabled="periode !== 'tahun'" 
                                class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                            @for($y = (int)date('Y') + 1; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ (int)($tahun ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Semua Waktu -->
                    <div x-show="periode === 'semua'">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Rentang Waktu</label>
                        <div class="h-11 px-3.5 flex items-center rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-500 italic">
                            Semua Riwayat Shift
                        </div>
                    </div>
                </div>

                <!-- Status Shift -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Status Shift</label>
                    <select name="status" 
                            class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition cursor-pointer">
                        <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="requested" {{ ($statusFilter ?? '') === 'requested' ? 'selected' : '' }}>Menunggu Izin</option>
                        <option value="closed" {{ ($statusFilter ?? '') === 'closed' ? 'selected' : '' }}>Ditutup (Closed)</option>
                        <option value="open" {{ ($statusFilter ?? '') === 'open' ? 'selected' : '' }}>Aktif (Open)</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" 
                            class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Filter</span>
                    </button>
                    @if(!empty($search) || ($periodeTipe ?? 'semua') !== 'semua' || (($statusFilter ?? 'all') !== 'all'))
                        <a href="{{ route('kasir.utilitas.buka-kas') }}" 
                           title="Reset Filter" 
                           class="h-11 w-11 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- ============================================================== -->
        <!-- 3. PURE DATA TABLE (DAFTAR SHIFT & STATUS PERMOHONAN)          -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="px-4 py-3">Tanggal Shift</th>
                            <th class="px-4 py-3">Kasir Pemegang</th>
                            <th class="px-4 py-3 text-right">Saldo Akhir Fisik (Rp)</th>
                            <th class="px-4 py-3 text-center">Status Shift</th>
                            <th class="px-4 py-3 text-center">Izin Buka Kas</th>
                            <th class="px-4 py-3">Alasan & Catatan Keuangan</th>
                            <th class="px-4 py-3 text-center">Aksi Otorisasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($shifts as $s)
                        @php
                            $tglFmt = $s->tanggal_shift ? \Carbon\Carbon::parse($s->tanggal_shift)->format('d/m/Y') : (\Carbon\Carbon::parse($s->tgl_shift)->format('d/m/Y') ?? '-');
                            $shiftData = [
                                'id'            => $s->id,
                                'tanggal'       => $tglFmt,
                                'kasir'         => $s->kasir->name ?? 'Kasir Priangan',
                                'saldo_fisik'   => (float) $s->saldo_akhir_fisik,
                                'alasan'        => $s->berita_acara_reopen ?? $s->reopen_reason ?? '',
                                'reopen_status' => $s->reopen_status ?? 'none'
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">
                                {{ $tglFmt }}
                                @if($s->closed_at)
                                    <div class="text-[11px] text-slate-400 font-normal">Tutup: {{ \Carbon\Carbon::parse($s->closed_at)->format('H:i') }} WIB</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-800">
                                {{ $s->kasir->name ?? 'Kasir Priangan' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($s->saldo_akhir_fisik ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($s->status === 'open' || $s->status === 'aktif')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-kp-blue-700 border border-sky-200">
                                        SEDANG AKTIF (OPEN)
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        DITUTUP (CLOSED)
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($s->reopen_status === 'requested')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-kp-blue-700 border border-sky-200 inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-kp-blue-600"></span>
                                        <span>Menunggu Izin</span>
                                    </span>
                                @elseif($s->reopen_status === 'approved')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-kp-blue-700 border border-sky-200">
                                        Diizinkan
                                    </span>
                                @elseif($s->reopen_status === 'rejected')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700 max-w-xs text-xs">
                                @if($s->reopen_reason)
                                    <div class="font-semibold text-slate-800 truncate" title="{{ $s->reopen_reason }}">
                                        "{{ $s->reopen_reason }}"
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        Diajukan: {{ $s->reopenRequester->name ?? 'Kasir' }} ({{ $s->reopen_requested_at ? \Carbon\Carbon::parse($s->reopen_requested_at)->format('d/m/Y H:i') : '' }})
                                    </div>
                                @endif
                                @if($s->reopen_notes)
                                    <div class="text-[10px] text-kp-blue-700 font-semibold mt-0.5">
                                        ACC: {{ $s->reopenApprover->name ?? 'Manager Keuangan' }} - {{ $s->reopen_notes }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if($s->status === 'closed' || $s->status === 'ditutup')
                                    @if($s->reopen_status === 'requested')
                                        <!-- Tindakan Otorisasi -->
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button" 
                                                    @click="openApprove({{ json_encode($shiftData) }})"
                                                    class="px-3 py-1.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-xs rounded-lg transition shadow-xs flex items-center gap-1 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>{{ $isFinanceManager ? 'ACC Buka Kas' : 'Otorisasi' }}</span>
                                            </button>
                                            @if($isFinanceManager)
                                            <button type="button" 
                                                    @click="openReject({{ json_encode($shiftData) }})"
                                                    class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold text-xs rounded-lg transition cursor-pointer">
                                                Tolak
                                            </button>
                                            @endif
                                        </div>
                                    @else
                                        <!-- Tombol Ajukan Buka Kas -->
                                        <button type="button" 
                                                @click="openAjukan({{ json_encode($shiftData) }})"
                                                class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 active:bg-slate-950 text-white font-semibold text-xs rounded-lg transition shadow-xs flex items-center gap-1 mx-auto cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                            <span>Ajukan Buka Kas</span>
                                        </button>
                                    @endif
                                @else
                                    <span class="text-kp-blue-700 font-bold text-xs">Telah Terbuka</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada data shift kasir.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($shifts->hasPages())
            <div class="p-3.5 border-t border-slate-200 bg-slate-50/50">
                {{ $shifts->links() }}
            </div>
            @endif
        </div>

        <!-- ============================================================== -->
        <!-- 4. MODAL 1: FORM PENGAJUAN BUKA KAS (KASIR)                    -->
        <!-- ============================================================== -->
        <div x-cloak x-show="showAjukanModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showAjukanModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-sky-50 text-kp-blue-700 border border-sky-200 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Form Pengajuan Buka Kembali Kas</h4>
                            <p class="text-xs text-slate-500">Shift Tanggal: <span class="font-bold text-slate-800" x-text="selectedShift.tanggal"></span></p>
                        </div>
                    </div>
                    <button type="button" @click="showAjukanModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/utilitas/buka-kas') }}/' + selectedShift.id + '/ajukan'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1.5 font-mono">
                        <div class="flex justify-between">
                            <span class="font-sans text-slate-500">Kasir Pemegang:</span>
                            <span class="font-bold text-slate-900 font-sans" x-text="selectedShift.kasir"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-sans text-slate-500">Saldo Akhir Fisik:</span>
                            <span class="font-bold text-slate-900" x-text="'Rp ' + (selectedShift.saldo_fisik ? selectedShift.saldo_fisik.toLocaleString('id-ID') : '0')"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alasan Pembukaan Kembali Kas <span class="text-rose-600">*</span>
                        </label>
                        <textarea name="alasan" 
                                  required 
                                  rows="3" 
                                  placeholder="Contoh: Ada setoran iklan dinas sore yang belum terinput sebelum shift ditutup..."
                                  class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition"></textarea>
                        <span class="text-[11px] text-slate-400 mt-1 block">Jelaskan alasan secara transparan untuk ditinjau oleh Bagian Keuangan.</span>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                        <button type="button" 
                                @click="showAjukanModal = false" 
                                class="h-10 px-4 bg-white border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer">
                            Kirim Permohonan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 5. MODAL 2: PERSETUJUAN / OTORISASI MANAGER KEUANGAN           -->
        <!-- ============================================================== -->
        <div x-cloak x-show="showApproveModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showApproveModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-sky-50 text-kp-blue-700 border border-sky-200 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Otorisasi Buka Kembali Kas</h4>
                            <p class="text-xs text-slate-500">Izin Pembukaan Shift: <span class="font-bold text-slate-800" x-text="selectedShift.tanggal"></span></p>
                        </div>
                    </div>
                    <button type="button" @click="showApproveModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/utilitas/buka-kas') }}/' + selectedShift.id + '/setujui'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-3.5 bg-sky-50 rounded-xl border border-sky-200 text-xs space-y-1">
                        <div class="font-bold text-[#0E527B]">Alasan Kasir Mengajukan:</div>
                        <p class="italic text-slate-700" x-text="selectedShift.alasan ? '“' + selectedShift.alasan + '”' : 'Tidak ada catatan alasan.'"></p>
                    </div>

                    @if($isFinanceManager)
                        <!-- Jika Manager Keuangan Sedang Login Langsung -->
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-800">
                            <div class="font-bold text-slate-900">Otorisasi Langsung sebagai:</div>
                            <div class="text-xs font-semibold text-kp-blue-700">{{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})</div>
                            <p class="text-[11px] text-slate-500 mt-1">Anda memiliki wewenang untuk menyetujui pembukaan shift kasir ini secara langsung.</p>
                        </div>
                    @else
                        <!-- Jika Kasir Biasa yang Membuka Modal -->
                        <div class="space-y-3 p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Verifikasi Kredensial Manager Keuangan:</span>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Email Manager Keuangan</label>
                                <input type="email" 
                                       name="manager_email" 
                                       required 
                                       placeholder="keuangan@kabarpriangan.co.id"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Password Manager Keuangan</label>
                                <input type="password" 
                                       name="manager_password" 
                                       required 
                                       placeholder="••••••••"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Persetujuan (Opsional)</label>
                        <input type="text" 
                               name="catatan" 
                               placeholder="Contoh: Diizinkan untuk perbaikan posting..."
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                        <button type="button" 
                                @click="showApproveModal = false" 
                                class="h-10 px-4 bg-white border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Setujui & Buka Kas</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 6. MODAL 3: PENOLAKAN PERMOHONAN                               -->
        <!-- ============================================================== -->
        <div x-cloak x-show="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showRejectModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center font-bold">
                            &times;
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Tolak Permohonan Buka Kas</h4>
                            <p class="text-xs text-slate-500">Shift Tanggal: <span class="font-bold text-slate-800" x-text="selectedShift.tanggal"></span></p>
                        </div>
                    </div>
                    <button type="button" @click="showRejectModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/utilitas/buka-kas') }}/' + selectedShift.id + '/tolak'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alasan Penolakan <span class="text-rose-600">*</span>
                        </label>
                        <textarea name="alasan_penolakan" 
                                  required 
                                  rows="3" 
                                  placeholder="Contoh: Laporan bulanan telah ditutup dan ditarik akuntansi..."
                                  class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                        <button type="button" 
                                @click="showRejectModal = false" 
                                class="h-10 px-4 bg-white border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="h-10 px-4 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer">
                            Tolak Permohonan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>