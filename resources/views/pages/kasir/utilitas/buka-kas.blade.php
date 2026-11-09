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

        {{-- Flash Message Alert --}}
        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-kp-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-kp-blue-600 hover:text-kp-blue-800 text-base font-bold leading-none cursor-pointer">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-slate-500 hover:text-slate-800 text-base font-bold leading-none cursor-pointer">&times;</button>
            </div>
        @endif

        {{-- Header Halaman --}}
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Buka Kembali Kas
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Pengajuan dan otorisasi pembukaan kembali shift kasir yang telah ditutup.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('kasir.utilitas.closing') }}"
                   class="inline-flex items-center gap-2 px-4 h-10 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Tutup Kas Harian</span>
                </a>
            </div>
        </div>

        {{-- Filter & Pencarian --}}
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('kasir.utilitas.buka-kas') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Cari Shift Kasir</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Tanggal (YYYY-MM-DD), nama kasir, atau alasan..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Shift / Izin</label>
                    <select name="status" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Shift Kasir</option>
                        <option value="requested" {{ ($statusFilter ?? '') === 'requested' ? 'selected' : '' }}>Menunggu Izin Keuangan</option>
                        <option value="closed" {{ ($statusFilter ?? '') === 'closed' ? 'selected' : '' }}>Shift Ditutup (Closed)</option>
                        <option value="open" {{ ($statusFilter ?? '') === 'open' ? 'selected' : '' }}>Shift Terbuka (Open)</option>
                    </select>
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <span>Filter</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabel Daftar Shift Kasir --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Daftar Shift Kasir & Riwayat Izin Buka Kas</h2>
                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $shifts->total() }} Shift
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Shift & Tanggal</th>
                            <th class="py-3 px-3.5">Kasir Pemegang</th>
                            <th class="py-3 px-3.5 text-right">Saldo Akhir Fisik</th>
                            <th class="py-3 px-3.5 text-center">Status Shift</th>
                            <th class="py-3 px-3.5 text-center">Izin Buka Kas</th>
                            <th class="py-3 px-3.5">Alasan & Catatan</th>
                            <th class="py-3 px-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
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
                            <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                {{ ($shifts->currentPage() - 1) * $shifts->perPage() + $loop->iteration }}
                            </td>
                            <td class="py-3.5 px-3.5 whitespace-nowrap">
                                <span class="font-mono font-bold text-kp-blue-700 text-sm">#{{ $s->id }}</span>
                                <span class="font-mono text-xs text-slate-600 ml-1.5">{{ $tglFmt }}</span>
                                @if($s->closed_at)
                                    <div class="text-xs text-slate-400 mt-0.5">Tutup {{ \Carbon\Carbon::parse($s->closed_at)->format('H:i') }} WIB</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                {{ $s->kasir->name ?? 'Kasir Priangan' }}
                            </td>
                            <td class="py-3.5 px-3.5 text-right font-mono tabular-nums font-semibold text-slate-900 whitespace-nowrap">
                                Rp {{ number_format($s->saldo_akhir_fisik ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                @if($s->status === 'open' || $s->status === 'aktif')
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        Terbuka
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        Ditutup
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                @if($s->reopen_status === 'requested')
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        Menunggu Izin
                                    </span>
                                @elseif($s->reopen_status === 'approved')
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        Disetujui
                                    </span>
                                @elseif($s->reopen_status === 'rejected')
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-400 border border-slate-200">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3.5 max-w-xs">
                                @if($s->reopen_reason)
                                    <div class="text-xs font-semibold text-slate-800 truncate" title="{{ $s->reopen_reason }}">
                                        {{ $s->reopen_reason }}
                                    </div>
                                    <div class="text-xs text-slate-400 mt-0.5">
                                        {{ $s->reopenRequester->name ?? 'Kasir' }}{{ $s->reopen_requested_at ? ' • ' . \Carbon\Carbon::parse($s->reopen_requested_at)->format('d/m/Y H:i') : '' }}
                                    </div>
                                @elseif(!$s->reopen_notes)
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                                @if($s->reopen_notes)
                                    <div class="text-xs text-kp-blue-700 font-medium mt-0.5">
                                        ACC {{ $s->reopenApprover->name ?? 'Keuangan' }}: {{ $s->reopen_notes }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                @if($s->status === 'closed' || $s->status === 'ditutup')
                                    @if($s->reopen_status === 'requested')
                                        <div class="inline-flex items-center justify-center gap-1.5">
                                            <button type="button"
                                                    @click="openApprove({{ json_encode($shiftData) }})"
                                                    class="px-3 py-1.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer inline-flex items-center gap-1.5">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>{{ $isFinanceManager ? 'Setujui' : 'Otorisasi' }}</span>
                                            </button>
                                            @if($isFinanceManager)
                                            <button type="button"
                                                    @click="openReject({{ json_encode($shiftData) }})"
                                                    class="px-3 py-1.5 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 font-semibold text-xs rounded-xl transition cursor-pointer">
                                                Tolak
                                            </button>
                                            @endif
                                        </div>
                                    @else
                                        <button type="button"
                                                @click="openAjukan({{ json_encode($shiftData) }})"
                                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer inline-flex items-center gap-1.5">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                            <span>Ajukan Buka Kas</span>
                                        </button>
                                    @endif
                                @else
                                    <span class="text-xs font-medium text-kp-blue-700">Aktif</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 px-4 text-center text-sm text-slate-400">Belum ada data shift kasir.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($shifts->hasPages())
            <div class="px-5 py-3.5 border-t border-slate-200">
                {{ $shifts->links() }}
            </div>
            @endif
        </div>

        {{-- MODAL 1: FORM PENGAJUAN BUKA KAS --}}
        <div x-cloak x-show="showAjukanModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.away="showAjukanModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Pengajuan Buka Kembali Kas</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Shift #<span class="font-mono font-bold text-kp-blue-700" x-text="selectedShift.id"></span> • <span class="font-mono" x-text="selectedShift.tanggal"></span></p>
                    </div>
                    <button type="button" @click="showAjukanModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold leading-none cursor-pointer">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/utilitas/buka-kas') }}/' + selectedShift.id + '/ajukan'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Kasir Pemegang</span>
                            <span class="font-semibold text-slate-900" x-text="selectedShift.kasir"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Saldo Akhir Fisik</span>
                            <span class="font-mono font-bold text-kp-blue-700" x-text="'Rp ' + (selectedShift.saldo_fisik ? selectedShift.saldo_fisik.toLocaleString('id-ID') : '0')"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Alasan Pembukaan Kas <span class="text-rose-600">*</span>
                        </label>
                        <textarea name="alasan" required rows="3" placeholder="Tuliskan alasan pengajuan buka kembali shift..."
                                  class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200">
                        <button type="button" @click="showAjukanModal = false" class="px-4 h-10 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 h-10 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition cursor-pointer">
                            Kirim Permohonan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL 2: OTORISASI MANAGER KEUANGAN --}}
        <div x-cloak x-show="showApproveModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.away="showApproveModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Otorisasi Buka Kembali Kas</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Shift #<span class="font-mono font-bold text-kp-blue-700" x-text="selectedShift.id"></span> • <span class="font-mono" x-text="selectedShift.tanggal"></span></p>
                    </div>
                    <button type="button" @click="showApproveModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold leading-none cursor-pointer">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/utilitas/buka-kas') }}/' + selectedShift.id + '/setujui'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1">
                        <span class="font-semibold text-slate-600 block">Alasan Pengajuan:</span>
                        <p class="text-slate-900 font-medium" x-text="selectedShift.alasan || 'Tidak ada catatan alasan.'"></p>
                    </div>

                    @if($isFinanceManager)
                        <div class="p-3.5 bg-kp-blue-50 rounded-xl border border-kp-blue-200 text-xs text-kp-blue-800 space-y-0.5">
                            <span class="font-semibold block">Otorisasi Langsung:</span>
                            <div class="font-bold">{{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})</div>
                        </div>
                    @else
                        <div class="space-y-3 p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="text-xs font-bold text-slate-800">Verifikasi Kredensial Keuangan</div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Email Manager Keuangan</label>
                                <input type="email" name="manager_email" required placeholder="keuangan@kabarpriangan.co.id"
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Password Manager Keuangan</label>
                                <input type="password" name="manager_password" required placeholder="••••••••"
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Catatan Persetujuan (Opsional)</label>
                        <input type="text" name="catatan" placeholder="Catatan otorisasi pembukaan kas..."
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200">
                        <button type="button" @click="showApproveModal = false" class="px-4 h-10 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 h-10 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition cursor-pointer inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Setujui & Buka Kas</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL 3: PENOLAKAN PERMOHONAN --}}
        <div x-cloak x-show="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.away="showRejectModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tolak Permohonan Buka Kas</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Shift #<span class="font-mono font-bold text-kp-blue-700" x-text="selectedShift.id"></span> • <span class="font-mono" x-text="selectedShift.tanggal"></span></p>
                    </div>
                    <button type="button" @click="showRejectModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold leading-none cursor-pointer">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/utilitas/buka-kas') }}/' + selectedShift.id + '/tolak'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Alasan Penolakan <span class="text-rose-600">*</span>
                        </label>
                        <textarea name="alasan_penolakan" required rows="3" placeholder="Tuliskan alasan penolakan permohonan..."
                                  class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200">
                        <button type="button" @click="showRejectModal = false" class="px-4 h-10 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 h-10 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition cursor-pointer">
                            Tolak Permohonan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
