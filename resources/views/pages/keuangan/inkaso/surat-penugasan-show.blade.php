@php
    $layoutTag = ($isFakturView ?? false) ? 'app-layout' : 'mobile-inkaso-layout';
@endphp

<x-dynamic-component :component="$layoutTag">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-shadow { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
        }
    </style>

    <div class="{{ ($isFakturView ?? false) ? 'max-w-5xl mx-auto p-4 sm:p-6 space-y-5' : 'space-y-4 pb-16' }}">
        <!-- Bar Navigasi & Cetak Surat Resmi -->
        <div class="flex items-center justify-between gap-2 flex-wrap no-print">
            <a href="{{ $backUrl }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-white border border-slate-200 text-xs font-extrabold text-[#0E527B] hover:bg-slate-50 shadow-2xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>{{ $backLabel }}</span>
            </a>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $isSelesai ? 'bg-[#0E527B] text-white' : ($waitingTasks->count() > 0 ? 'bg-amber-100 text-[#07334F] border border-amber-300' : 'bg-[#FFB900] text-[#07334F]') }}">
                    {{ $isSelesai ? 'Selesai' : ($waitingTasks->count() > 0 ? 'Menunggu Persetujuan' : 'Aktif') }}
                </span>

                <button type="button"
                        onclick="window.print()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-bold shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Surat</span>
                </button>
            </div>
        </div>

        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-sky-50 border border-sky-200 text-[#0E527B] text-xs font-bold flex items-center gap-2 shadow-xs no-print">
                <svg class="w-4 h-4 text-[#0E527B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-bold flex items-center gap-2 shadow-xs no-print">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-300 text-[#07334F] text-xs font-bold flex items-center gap-2 shadow-xs no-print">
                <svg class="w-4 h-4 text-[#FFB900] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- LEMBAR SURAT PENUGASAN RESMI (KOP, LOGO, NOMOR SURAT, TABEL FAKTUR, TTD) -->
        @include('pages.keuangan.inkaso.partials.surat-resmi-paper')

        <!-- PANEL AKSI PETUGAS INKASO (HANYA MUNCUL UNTUK PETUGAS LAPANGAN) -->
        @if(!($isFakturView ?? false))
            <div class="space-y-3 pt-1 no-print">
                @if($waitingTasks->count() > 0)
                    <!-- Aksi Persetujuan Surat (Approve -> Otomatis pindah ke Halaman Penagihan) -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs space-y-3">
                        <div>
                            <h3 class="text-xs font-extrabold text-[#0E527B] uppercase tracking-wider">Konfirmasi Penerimaan Surat</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Setujui surat penugasan untuk memindahkan faktur ke halaman Penagihan, atau tolak faktur spesifik bila tidak sesuai rute.
                            </p>
                        </div>

                        @if($isGrupSurat && $waitingTasks->count() > 1)
                            <form action="{{ route('keuangan.inkaso.penugasan.grup.konfirmasi', $noSuratLabel) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full h-11 rounded-2xl bg-[#0E527B] hover:bg-[#07334F] active:scale-98 text-white text-xs font-black flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <span>Terima Semua Surat Ini &amp; Masuk Penagihan</span>
                                </button>
                            </form>
                        @elseif($waitingTasks->count() === 1)
                            @php $singleWait = $waitingTasks->first(); @endphp
                            <form action="{{ route('keuangan.inkaso.penugasan.konfirmasi', $singleWait->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="aksi" value="terima">
                                <button type="submit"
                                        class="w-full h-11 rounded-2xl bg-[#0E527B] hover:bg-[#07334F] active:scale-98 text-white text-xs font-black flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <span>Setujui Surat &amp; Masuk Halaman Penagihan</span>
                                </button>
                            </form>
                        @endif

                        <!-- Daftar Faktur Menunggu (Jika Ingin Tolak Faktur Spesifik) -->
                        <div class="pt-2 border-t border-slate-100 space-y-2">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">
                                Tolak Faktur Spesifik (Opsional)
                            </span>
                            @foreach($waitingTasks as $wt)
                                <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                                    <div class="min-w-0">
                                        <span class="font-mono font-bold text-[#0E527B] block">{{ $wt->faktur->no_faktur ?? '-' }}</span>
                                        <span class="text-[11px] text-slate-700 truncate block">{{ $wt->faktur->customer->nama_customer ?? '-' }}</span>
                                    </div>
                                    <button type="button"
                                            onclick="bukaModalTolakDetail('{{ $wt->id }}', '{{ $wt->faktur->no_faktur ?? '-' }}', '{{ addslashes($wt->faktur->customer->nama_customer ?? '-') }}')"
                                            class="px-3 py-1.5 rounded-lg border border-amber-300 bg-amber-50 hover:bg-amber-100 text-[#07334F] text-[11px] font-bold shrink-0 cursor-pointer transition">
                                        Tolak Faktur Ini
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!$isSelesai && $activeTasks->count() > 0)
                    <a href="{{ route('keuangan.inkaso.mobile', ['tab' => 'penagihan', 'sub' => 'semua_faktur']) }}"
                       class="w-full h-11 rounded-2xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-extrabold flex items-center justify-center gap-2 shadow-sm transition">
                        <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Kelola Penagihan Faktur</span>
                    </a>

                    @if($isGrupSurat)
                        @php
                            $belumBayarCountDetail = $tasks->filter(function($t) {
                                return !($t->faktur && $t->faktur->sisa_piutang <= 0);
                            })->count();
                        @endphp
                        <form action="{{ route('keuangan.inkaso.penugasan.grup.selesai', $noSuratLabel) }}" method="POST"
                              onsubmit="return konfirmasiSelesaiPenugasan(this, '{{ $noSuratLabel }}', {{ $tasks->count() }}, {{ $belumBayarCountDetail }})">
                            @csrf
                            <button type="submit"
                                    class="w-full h-11 rounded-2xl bg-[#FFB900] hover:bg-amber-400 active:scale-98 text-[#07334F] text-xs font-black flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                                <svg class="w-4 h-4 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Selesai Penagihan Surat Ini</span>
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        @endif

        <a href="{{ $backUrl }}"
           class="w-full h-11 rounded-2xl border border-slate-300 bg-white hover:bg-slate-50 text-[#0E527B] text-xs font-extrabold flex items-center justify-center gap-1.5 shadow-2xs transition no-print">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>{{ $backLabel }}</span>
        </a>
    </div>

    <!-- Modal Tolak Faktur Spesifik -->
    <div id="modalTolakDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-[2px] p-4 hidden no-print">
        <div class="bg-white rounded-[1.75rem] max-w-md w-full p-5 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-extrabold text-[#0E527B] uppercase tracking-tight">Tolak Faktur Ini</h3>
                <button type="button" onclick="document.getElementById('modalTolakDetail').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="formTolakDetail" method="POST" class="space-y-3.5">
                @csrf
                <input type="hidden" name="aksi" value="tolak">
                <p class="text-xs text-slate-600">Tolak faktur <strong id="dtlTolakFakturNo" class="font-mono">-</strong> (<strong id="dtlTolakCustomer">-</strong>).</p>
                <textarea name="alasan_penolakan" required rows="3" placeholder="Tuliskan alasan penolakan faktur ini..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800"></textarea>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-[#FFB900] hover:bg-amber-400 text-[#07334F] text-xs font-black cursor-pointer">Konfirmasi Tolak Faktur</button>
            </form>
        </div>
    </div>

    <script>
        function bukaModalTolakDetail(tugasId, fakturNo, customer) {
            document.getElementById('dtlTolakFakturNo').innerText = fakturNo;
            document.getElementById('dtlTolakCustomer').innerText = customer;
            document.getElementById('formTolakDetail').action = "{{ url('keuangan/inkaso/penugasan') }}/" + tugasId + "/konfirmasi";
            document.getElementById('modalTolakDetail').classList.remove('hidden');
        }

        function konfirmasiSelesaiPenugasan(formEl, noSurat, totalFaktur, belumBayar) {
            let pesan = 'Apakah Anda yakin ingin menyelesaikan penagihan surat ini?';
            if (belumBayar > 0) {
                pesan = 'Ada ' + belumBayar + ' faktur yang belum terbayar. Tetap selesaikan penagihan surat ini?';
            }

            if (window.Swal) {
                Swal.fire({
                    title: 'Selesaikan Surat Penugasan?',
                    text: pesan,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0E527B',
                    cancelButtonColor: '#94A3B8',
                    confirmButtonText: 'Ya, Selesaikan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-3xl',
                        confirmButton: 'rounded-xl px-5 py-2.5 font-bold text-xs',
                        cancelButton: 'rounded-xl px-5 py-2.5 font-bold text-xs'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        formEl.submit();
                    }
                });
                return false;
            }
            return confirm(pesan);
        }
    </script>

    @if(!($isFakturView ?? false))
        <!-- FLOATING BOTTOM DOCK NAVIGATION (MOBILE COLLECTOR PERSIS PORTAL INKASO) -->
        <nav class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[60] inline-flex items-center gap-2 sm:gap-2.5 p-2 px-2.5 rounded-full bg-[#0E527B] shadow-[0_16px_40px_rgba(14,82,123,0.50)] border border-white/20 select-none backdrop-blur-xl transition-all duration-300 no-print">
            {{-- 1. Beranda --}}
            <a href="{{ route('keuangan.inkaso.mobile', ['tab' => 'beranda']) }}" 
               class="w-11 h-11 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 flex items-center justify-center text-white cursor-pointer transition select-none shrink-0"
               title="Beranda">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
            </a>

            {{-- 2. Penagihan --}}
            <a href="{{ route('keuangan.inkaso.mobile', ['tab' => 'penagihan', 'sub' => 'surat_penugasan']) }}" 
               class="bg-white text-[#0E527B] px-5 py-2.5 rounded-full font-black text-sm shadow-md flex items-center gap-2.5 cursor-pointer transition select-none shrink-0">
                <svg class="w-5 h-5 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span class="whitespace-nowrap font-extrabold text-xs sm:text-sm tracking-tight">Penagihan</span>
            </a>

            {{-- 3. Riwayat --}}
            <a href="{{ route('keuangan.inkaso.mobile', ['tab' => 'riwayat']) }}" 
               class="w-11 h-11 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 flex items-center justify-center text-white cursor-pointer transition select-none shrink-0"
               title="Riwayat">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </a>

            {{-- 4. Profil --}}
            <a href="{{ route('keuangan.inkaso.mobile', ['tab' => 'profil']) }}" 
               class="w-11 h-11 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 flex items-center justify-center text-white cursor-pointer transition select-none shrink-0"
               title="Profil">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </a>
        </nav>
    @endif
</x-dynamic-component>
