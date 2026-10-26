<x-app-layout>
    <div class="space-y-6" x-data="{
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

        <!-- Top Breadcrumbs & Header Bar (OripioFin Style) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5 text-slate-400">
                    <button class="w-8 h-8 rounded-full bg-white border border-slate-200/80 flex items-center justify-center hover:bg-slate-50 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button class="w-8 h-8 rounded-full bg-white border border-slate-200/80 flex items-center justify-center hover:bg-slate-50 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                    <span class="hover:text-slate-600 cursor-pointer">OripioFin</span>
                    <span>&rsaquo;</span>
                    <span class="hover:text-slate-600 cursor-pointer">Utilitas</span>
                    <span>&rsaquo;</span>
                    <span class="text-slate-800 font-semibold">Buka Kembali Kas (Reopen Closed Shift)</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('kasir.utilitas.closing') }}" 
                   class="px-4 py-2.5 bg-[#0B6B44] hover:bg-[#095737] text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Ke Halaman Tutup Shift</span>
                </a>
            </div>
        </div>

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl text-xs font-semibold text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200/80 rounded-2xl text-xs font-semibold text-rose-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- SOP & Ketentuan Buka Kas (OripioFin Soft Alert Panel) -->
        <div class="bg-amber-50/60 border border-amber-200/80 rounded-2xl p-5 text-xs text-amber-900 flex items-start gap-3.5 shadow-xs shadow-slate-100">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="space-y-1.5">
                <div class="font-bold text-amber-950">Standar Operasional Prosedur (SOP) Pembukaan Kembali Kas:</div>
                <p class="text-[11px] text-amber-800 leading-relaxed">
                    1. Kasir tidak diperkenankan membuka kembali shift kas yang telah ditutup secara sepihak untuk mencegah manipulasi uang fisik laci dan data buku besar akuntansi.<br>
                    2. Kasir harus mengisi alasan pembukaan kembali kas (misal: keterlambatan input setoran dinas, koreksi salah posting BP).<br>
                    3. <strong>Persetujuan Resmi:</strong> Manager Keuangan atau Bagian Keuangan dapat menyetujui langsung melalui akunnya atau menginput password otorisasi saat mendampingi kasir di meja kasir.
                </p>
            </div>
        </div>

        <!-- Filter & Search Bar (OripioFin Style) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100">
            <form method="GET" action="{{ route('kasir.utilitas.buka-kas') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Cari Shift Kasir</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik tanggal (YYYY-MM-DD), nama kasir, atau alasan... ⌘K"
                           class="w-full text-xs py-2 px-3.5 rounded-xl border border-slate-200/80 focus:ring-2 focus:ring-[#0B6B44]/20 focus:border-[#0B6B44] text-slate-800 bg-slate-50/50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Shift / Perizinan</label>
                    <select name="status" class="w-full text-xs py-2 px-3.5 rounded-xl border border-slate-200/80 focus:ring-2 focus:ring-[#0B6B44]/20 focus:border-[#0B6B44] text-slate-800 font-semibold bg-slate-50/50">
                        <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Shift Kasir</option>
                        <option value="requested" {{ ($statusFilter ?? '') === 'requested' ? 'selected' : '' }}>Menunggu Izin Keuangan</option>
                        <option value="closed" {{ ($statusFilter ?? '') === 'closed' ? 'selected' : '' }}>Shift Ditutup (Closed)</option>
                        <option value="open" {{ ($statusFilter ?? '') === 'open' ? 'selected' : '' }}>Shift Sedang Terbuka (Open)</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition shadow-2xs">
                        Filter Riwayat
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Shift Kasir & Status Permohonan Buka Kas -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs shadow-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0B6B44]"></span>
                    Daftar Shift Kasir & Riwayat Izin Buka Kas
                </h3>
                <span class="text-xs text-slate-400 font-bold">Total: {{ $shifts->total() }} Shift</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Tanggal Shift</th>
                            <th class="px-4 py-3 font-semibold">Kasir Pemegang</th>
                            <th class="px-4 py-3 font-semibold text-right">Saldo Akhir Fisik (Rp)</th>
                            <th class="px-4 py-3 font-semibold text-center">Status Shift</th>
                            <th class="px-4 py-3 font-semibold text-center">Izin Buka Kas</th>
                            <th class="px-4 py-3 font-semibold">Alasan & Catatan Keuangan</th>
                            <th class="px-4 py-3 font-semibold text-center">Aksi Otorisasi</th>
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
                                'alasan'        => $s->reopen_reason ?? '',
                                'reopen_status' => $s->reopen_status ?? 'none'
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">
                                {{ $tglFmt }}
                                @if($s->closed_at)
                                    <div class="text-[10px] text-slate-400 font-normal">Tutup: {{ \Carbon\Carbon::parse($s->closed_at)->format('H:i') }} WIB</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-800">
                                {{ $s->kasir->name ?? 'Kasir Priangan' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($s->saldo_akhir_fisik ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($s->status === 'open' || $s->status === 'aktif')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        SEDANG AKTIF (OPEN)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                        DITUTUP (CLOSED)
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($s->reopen_status === 'requested')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300 flex items-center justify-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>Menunggu Izin Keuangan</span>
                                    </span>
                                @elseif($s->reopen_status === 'approved')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                        Diizinkan Keuangan
                                    </span>
                                @elseif($s->reopen_status === 'rejected')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-300">
                                        Ditolak Keuangan
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[10px]">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700 max-w-xs">
                                @if($s->reopen_reason)
                                    <div class="font-semibold text-slate-800 truncate" title="{{ $s->reopen_reason }}">
                                        "{{ $s->reopen_reason }}"
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        Diajukan: {{ $s->reopenRequester->name ?? 'Kasir' }} ({{ $s->reopen_requested_at ? \Carbon\Carbon::parse($s->reopen_requested_at)->format('d/m/Y H:i') : '' }})
                                    </div>
                                @endif
                                @if($s->reopen_notes)
                                    <div class="text-[10px] text-indigo-700 font-medium mt-0.5">
                                        ACC: {{ $s->reopenApprover->name ?? 'Manager Keuangan' }} - {{ $s->reopen_notes }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($s->status === 'closed' || $s->status === 'ditutup')
                                    @if($s->reopen_status === 'requested')
                                        <!-- Tindakan Otorisasi -->
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" 
                                                    @click="openApprove({{ json_encode($shiftData) }})"
                                                    class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] rounded-lg transition shadow-2xs flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>{{ $isFinanceManager ? 'ACC Buka Kas' : 'Otorisasi Keuangan' }}</span>
                                            </button>
                                            @if($isFinanceManager)
                                            <button type="button" 
                                                    @click="openReject({{ json_encode($shiftData) }})"
                                                    class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-[10px] rounded-lg transition shadow-2xs">
                                                Tolak
                                            </button>
                                            @endif
                                        </div>
                                    @else
                                        <!-- Tombol Ajukan Buka Kas -->
                                        <button type="button" 
                                                @click="openAjukan({{ json_encode($shiftData) }})"
                                                class="px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white font-bold text-[10px] rounded-lg transition shadow-2xs flex items-center gap-1 mx-auto">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                            <span>Ajukan Buka Kas</span>
                                        </button>
                                    @endif
                                @else
                                    <span class="text-emerald-600 font-bold text-[10px]">Telah Terbuka</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-6 text-center text-slate-400">Belum ada data shift kasir.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($shifts->hasPages())
            <div class="p-3 border-t border-slate-100">
                {{ $shifts->links() }}
            </div>
            @endif
        </div>

        <!-- ======================================================== -->
        <!-- MODAL 1: FORM PENGAJUAN BUKA KAS (KASIR)                 -->
        <!-- ======================================================== -->
        <div x-cloak x-show="showAjukanModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showAjukanModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-amber-50/50">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Form Pengajuan Buka Kembali Kas</h4>
                            <p class="text-[10px] text-slate-500">Shift Tanggal: <span class="font-bold text-slate-800" x-text="selectedShift.tanggal"></span></p>
                        </div>
                    </div>
                    <button type="button" @click="showAjukanModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/utilitas/buka-kas') }}/' + selectedShift.id + '/ajukan'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Kasir Pemegang:</span>
                            <span class="font-bold text-slate-900" x-text="selectedShift.kasir"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Saldo Akhir Fisik:</span>
                            <span class="font-mono font-bold text-slate-900" x-text="'Rp ' + (selectedShift.saldo_fisik ? selectedShift.saldo_fisik.toLocaleString('id-ID') : '0')"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Alasan Pembukaan Kembali Kas <span class="text-rose-600">*</span>
                        </label>
                        <textarea name="alasan" required rows="3" placeholder="Contoh: Ada setoran iklan dinas sore yang belum terinput sebelum shift ditutup..."
                                  class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 text-slate-900"></textarea>
                        <span class="text-[10px] text-slate-400">Jelaskan alasan secara transparan untuk ditinjau oleh Manager Keuangan.</span>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showAjukanModal = false" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-semibold text-xs rounded-xl hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl transition shadow-2xs">
                            Kirim Permohonan ke Keuangan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL 2: PERSETUJUAN / OTORISASI MANAGER KEUANGAN         -->
        <!-- ======================================================== -->
        <div x-cloak x-show="showApproveModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showApproveModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-emerald-50/50">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Otorisasi Buka Kembali Kas</h4>
                            <p class="text-[10px] text-slate-500">Izin Pembukaan Shift Tanggal: <span class="font-bold text-slate-800" x-text="selectedShift.tanggal"></span></p>
                        </div>
                    </div>
                    <button type="button" @click="showApproveModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/utilitas/buka-kas') }}/' + selectedShift.id + '/setujui'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs space-y-1">
                        <div class="font-bold text-amber-900">Alasan Kasir Mengajukan:</div>
                        <p class="italic text-slate-700" x-text="selectedShift.alasan ? '“' + selectedShift.alasan + '”' : 'Tidak ada catatan alasan.'"></p>
                    </div>

                    @if($isFinanceManager)
                        <!-- Jika Manager Keuangan Sedang Login Langsung -->
                        <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-xs text-emerald-900">
                            <div class="font-bold">Otorisasi Langsung sebagai:</div>
                            <div class="text-[11px] font-semibold">{{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})</div>
                            <p class="text-[10px] text-emerald-700 mt-1">Anda memiliki wewenang untuk menyetujui pembukaan shift kasir ini secara langsung.</p>
                        </div>
                    @else
                        <!-- Jika Kasir Biasa yang Membuka Modal (Membutuhkan Kredensial Manager Keuangan) -->
                        <div class="space-y-3 p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Verifikasi Kredensial Manager Keuangan:</span>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Email Manager Keuangan</label>
                                <input type="email" name="manager_email" required placeholder="keuangan@kabarpriangan.co.id"
                                       class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 font-semibold text-slate-900">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Password Manager Keuangan</label>
                                <input type="password" name="manager_password" required placeholder="••••••••"
                                       class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 text-slate-900">
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Persetujuan (Opsional)</label>
                        <input type="text" name="catatan" placeholder="Contoh: Diizinkan untuk perbaikan posting iklan dinas sore"
                               class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 text-slate-900">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showApproveModal = false" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-semibold text-xs rounded-xl hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Setujui & Buka Kas Sekarang</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL 3: PENOLAKAN PERMOHONAN (KHUSUS MANAGER KEUANGAN)    -->
        <!-- ======================================================== -->
        <div x-cloak x-show="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showRejectModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-rose-50/50">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                            &times;
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Tolak Permohonan Buka Kas</h4>
                            <p class="text-[10px] text-slate-500">Shift Tanggal: <span class="font-bold text-slate-800" x-text="selectedShift.tanggal"></span></p>
                        </div>
                    </div>
                    <button type="button" @click="showRejectModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/utilitas/buka-kas') }}/' + selectedShift.id + '/tolak'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Alasan Penolakan <span class="text-rose-600">*</span>
                        </label>
                        <textarea name="alasan_penolakan" required rows="3" placeholder="Contoh: Laporan bulanan telah ditutup dan ditarik akuntansi..."
                                  class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-rose-500 text-slate-900"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showRejectModal = false" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-semibold text-xs rounded-xl hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition shadow-2xs">
                            Tolak Permohonan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
