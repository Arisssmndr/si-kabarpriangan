<x-app-layout>
    <div class="space-y-5" x-data="{ showMemoModal: false }">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Input &amp; Edit Oplah Harian
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl" title="Pengaturan alokasi jatah koran harian per agen untuk distribusi langganan, konsinyasi, dan eceran.">
                        Pengaturan alokasi jatah koran harian per agen untuk distribusi langganan, konsinyasi, dan eceran.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <!-- Status Cut-off Harian -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border bg-slate-50 border-slate-200 text-slate-700 text-xs font-semibold shadow-2xs">
                    <span>Cut-Off Harian: <strong class="font-mono text-slate-900">{{ $jamCutoffSirkulasi ?? '17:00' }} WIB</strong></span>
                </div>

                @if(empty($isCutoffLocked) && !empty($canManageCutoff))
                    @if(!empty($hasJatahData))
                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}" onsubmit="return confirmSubmit(this, event, {
                            title: 'Cut-Off Sekarang?',
                            text: 'Kunci data oplah edisi ini sekarang? Setelah dikunci, data tidak dapat diubah.',
                            theme: 'blue',
                            icon: 'clock',
                            confirmText: 'Ya, Cut-Off'
                        })">
                            @csrf
                            <input type="hidden" name="mode" value="harian">
                            <input type="hidden" name="tanggal" value="{{ $targetTanggal }}">
                            <input type="hidden" name="action" value="lock">
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap"
                                    title="Kunci edisi oplah hari ini sekarang">
                                <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span>Cut-Off Sekarang</span>
                            </button>
                        </form>
                    @else
                        <button type="button" disabled
                                title="Data jatah edisi ini masih kosong. Silakan salin (Copy Jatah) atau input jatah edisi ini terlebih dahulu sebelum melakukan Cut-Off."
                                class="inline-flex items-center gap-1.5 h-10 px-4 bg-slate-100 border border-slate-300 text-slate-400 text-xs sm:text-sm font-semibold rounded-xl cursor-not-allowed whitespace-nowrap">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Cut-Off (Data Belum Tersedia)</span>
                        </button>
                    @endif
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-sm font-semibold text-kp-blue-700 flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-slate-100 border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(!empty($isCutoffLocked))
            @if($activeMemo && $activeMemo->status_akuntansi === 'PENDING')
                <!-- Kondisi: Permohonan Memo Menunggu Persetujuan Akuntansi -->
                <div class="p-4 bg-sky-50 border border-sky-200 rounded-xl text-xs sm:text-sm text-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-9 h-9 rounded-xl bg-sky-100 border border-sky-200 text-kp-blue-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <strong class="text-slate-900 font-bold">Memo Buka Kunci Sedang Diproses Akuntansi</strong>
                                <span class="px-2 py-0.5 rounded-md text-xs font-mono font-bold bg-white text-kp-blue-700 border border-sky-200">{{ $activeMemo->nomor_memo }}</span>
                            </div>
                            <span class="text-slate-600 block mt-0.5">Alasan: {{ $activeMemo->alasan }} (Menunggu verifikasi Divisi Akuntansi)</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('sirkulasi.jatah.memo.cetak', $activeMemo->id) }}" target="_blank"
                           class="inline-flex items-center justify-center h-9 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-2xs transition whitespace-nowrap">
                            Cetak Memo
                        </a>
                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.cancel-request-unlock') }}" onsubmit="return confirmSubmit(this, event, {
                            title: 'Batalkan Memo?',
                            text: 'Batalkan permohonan buka kunci memo ini?',
                            theme: 'rose',
                            icon: 'cancel',
                            confirmText: 'Ya, Batalkan'
                        })">
                            @csrf
                            <input type="hidden" name="mode" value="harian">
                            <input type="hidden" name="tanggal" value="{{ $targetTanggal }}">
                            <button type="submit" class="h-9 px-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition cursor-pointer">
                                Batalkan Memo
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($activeMemo && in_array(strtoupper((string) $activeMemo->status_akuntansi), ['REJECTED', 'DITOLAK'], true))
                <!-- Kondisi: Permohonan Memo Ditolak Akuntansi -->
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs sm:text-sm text-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-9 h-9 rounded-xl bg-rose-100 border border-rose-200 text-rose-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </span>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <strong class="text-slate-900 font-bold">Permohonan Buka Kunci Ditolak Akuntansi</strong>
                                <span class="px-2 py-0.5 rounded-md text-xs font-mono font-bold bg-white text-rose-700 border border-rose-200">{{ $activeMemo->nomor_memo }}</span>
                            </div>
                            <span class="text-rose-800 block mt-0.5 font-medium">Catatan Akuntansi: {{ $activeMemo->catatan_akuntansi ?? 'Permohonan tidak disetujui.' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('sirkulasi.jatah.memo.cetak', $activeMemo->id) }}" target="_blank"
                           class="inline-flex items-center justify-center h-9 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-2xs transition whitespace-nowrap">
                            Cetak Bukti Penolakan
                        </a>
                        <button type="button" @click="showMemoModal = true"
                                class="inline-flex items-center justify-center h-9 px-3.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-lg shadow-xs transition whitespace-nowrap cursor-pointer">
                            Ajukan Memo Baru
                        </button>
                    </div>
                </div>
            @else
                <!-- Kondisi: Terkunci Normal / Cut-off Tercapai -->
                <div class="p-4 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-8 h-8 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </span>
                        <div>
                            <strong class="text-slate-900 font-bold block">Data Oplah Terkunci</strong>
                            <span class="text-slate-600">Data edisi ini telah melewati batas cut-off dan tidak dapat diubah.</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" @click="showMemoModal = true"
                                class="inline-flex items-center justify-center h-9 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-lg shadow-xs transition whitespace-nowrap cursor-pointer">
                            Ajukan Memo Buka Data
                        </button>
                    </div>
                </div>
            @endif
        @else
            @if($activeMemo && $activeMemo->status === 'OPEN')
                <!-- Kondisi: Kunci Edisi Dibuka Sementara via Memo Otorisasi Akuntansi -->
                <div class="p-4 bg-kp-blue-50/80 border border-kp-blue-200 rounded-xl text-xs sm:text-sm text-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-9 h-9 rounded-xl bg-kp-blue-600 text-white flex items-center justify-center shrink-0 font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                        </span>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <strong class="text-slate-900 font-bold">Kunci Edisi Dibuka Sementara</strong>
                                <span class="px-2 py-0.5 rounded-md text-xs font-mono font-bold bg-white text-kp-blue-700 border border-kp-blue-300">{{ $activeMemo->nomor_memo }}</span>
                            </div>
                            <span class="text-slate-600 block mt-0.5">Otorisasi Divisi Akuntansi aktif. Silakan lakukan perubahan oplah lalu kunci kembali setelah selesai.</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('sirkulasi.jatah.memo.cetak', $activeMemo->id) }}" target="_blank"
                           class="inline-flex items-center justify-center h-9 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-2xs transition whitespace-nowrap">
                            Cetak Memo
                        </a>
                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}" onsubmit="return confirmSubmit(this, event, {
                            title: 'Kunci Kembali?',
                            text: 'Kunci kembali data oplah edisi ini?',
                            theme: 'blue',
                            icon: 'lock',
                            confirmText: 'Ya, Kunci'
                        })">
                            @csrf
                            <input type="hidden" name="mode" value="harian">
                            <input type="hidden" name="tanggal" value="{{ $targetTanggal }}">
                            <input type="hidden" name="action" value="lock">
                            <button type="submit" class="h-9 px-3.5 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-lg shadow-xs transition cursor-pointer">
                                Kunci Kembali
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        @endif

        <!-- Filter Tanggal & Rute -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.jatah.edit') }}" class="flex flex-col lg:flex-row lg:items-end gap-3">
                <div class="w-full lg:w-52 shrink-0">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal</label>
                    <input type="date"
                           name="tanggal"
                           value="{{ $targetTanggal }}"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>
                <div class="w-full lg:flex-1 min-w-0">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Rute</label>
                    <select name="jurusan"
                            class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Rute</option>
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->kode }}" {{ ($jurusanPilihan ?? '') === $jur->kode ? 'selected' : '' }}>
                                {{ $jur->kode }} - {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full lg:w-auto shrink-0 flex items-center gap-2">
                    <button type="submit"
                            class="flex-1 lg:flex-none h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer whitespace-nowrap">
                        Terapkan
                    </button>
                    @if(request()->hasAny(['tanggal', 'jurusan']))
                        <a href="{{ route('sirkulasi.jatah.edit') }}"
                           title="Reset Filter"
                           class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center whitespace-nowrap">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if($hasJatahData)
            <!-- KPI Ringkasan Jatah (60-30-10) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Langganan</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums"><span id="kpi-langganan">{{ number_format($totalLangganan, 0, ',', '.') }}</span> <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Konsinyasi</span>
                    <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums"><span id="kpi-konsinyasi">{{ number_format($totalKonsinyasi, 0, ',', '.') }}</span> <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Gratis</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums"><span id="kpi-gratis">{{ number_format($totalGratis, 0, ',', '.') }}</span> <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pengenalan</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums"><span id="kpi-pengenalan">{{ number_format($totalPengenalan ?? 0, 0, ',', '.') }}</span> <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Insidentil</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums"><span id="kpi-insidentil">{{ number_format($totalInsidentil, 0, ',', '.') }}</span> <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-kp-blue-50 border border-kp-blue-200 rounded-xl p-4 shadow-xs col-span-2 sm:col-span-1">
                    <span class="text-xs font-semibold text-kp-blue-700 uppercase tracking-wider block">Total Jatah</span>
                    <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums"><span id="kpi-grand-total">{{ number_format($grandTotalJatah, 0, ',', '.') }}</span> <span class="text-xs font-sans font-normal text-kp-blue-600">eks</span></p>
                </div>
            </div>
        @endif

        <!-- Form Daftar Jatah Agen (60-30-10) -->
        <form id="formEditJatah" method="POST" action="{{ route('sirkulasi.jatah.edit.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="tanggal_target" value="{{ $targetTanggal }}">

            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                @if(!$hasJatahData)
                    <div class="p-8 sm:p-14 text-center bg-white">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-b from-kp-blue-50 to-kp-blue-100/70 border border-kp-blue-200/80 text-kp-blue-700 mx-auto flex items-center justify-center mb-4 shadow-2xs">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                            </svg>
                        </div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 mb-2.5">
                            <span>Distribusi Harian</span>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Alokasi Oplah Edisi Belum Tersedia</h3>
                        <p class="text-xs sm:text-sm text-slate-500 max-w-lg mx-auto mt-1.5 leading-relaxed">
                            Edisi <strong class="text-slate-800 font-semibold">{{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('d F Y') }}</strong> belum memiliki rincian alokasi oplah koran per agen. Salin jatah dari edisi sebelumnya atau terapkan pola distribusi untuk memulai operasional agen.
                        </p>
                        <div class="mt-5">
                            <a href="{{ route('sirkulasi.jatah.copy', ['sumber_tanggal' => $prevTanggal, 'target_tanggal' => $targetTanggal, 'jurusan' => $jurusanPilihan]) }}"
                               class="inline-flex items-center justify-center h-11 px-6 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition duration-150 cursor-pointer">
                                <span>Salin Jatah Edisi Sebelumnya</span>
                            </a>
                        </div>
                    </div>
                @else
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 w-12 text-center">No</th>
                                <th class="py-3 px-3.5">Kode Agen</th>
                                <th class="py-3 px-3.5">Nama Agen</th>
                                <th class="py-3 px-3.5 text-center">Kode Rute</th>
                                <th class="py-3 px-3.5">Nama Rute</th>
                                <th class="py-3 px-3.5 text-center w-28">Langganan</th>
                                <th class="py-3 px-3.5 text-center w-28">Konsinyasi</th>
                                <th class="py-3 px-3.5 text-center w-28">Gratis</th>
                                <th class="py-3 px-3.5 text-center w-28">Pengenalan</th>
                                <th class="py-3 px-3.5 text-center w-28">Insidentil</th>
                                <th class="py-3 px-3.5 text-right">Total</th>
                                <th class="py-3 px-3.5 text-right w-36">Omset</th>
                                <th class="py-3 px-3.5 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @foreach($matrixJatah->values() as $idx => $item)
                                <tr class="jatah-row hover:bg-slate-50/80 transition" data-agen-id="{{ $item->agen_id }}">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                        {{ $item->kode_agen }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                        {{ $item->nama_agen }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center font-mono text-slate-600 font-normal">
                                        R-{{ $item->jurusan_kode }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                        {{ $item->jurusan_nama }}
                                    </td>
                                    <td class="py-3 px-2.5 text-center">
                                        @if(!empty($isCutoffLocked))
                                            <span class="font-mono font-semibold text-slate-800">{{ number_format($item->langganan, 0, ',', '.') }}</span>
                                        @else
                                            <input type="number" name="jatah[{{ $item->agen_id }}][langganan]" value="{{ $item->langganan }}" data-field="langganan" data-original="{{ $item->langganan }}" data-base-border="border-slate-300" min="0" class="jatah-input w-20 h-9 text-center text-sm rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono font-semibold text-slate-900 transition">
                                        @endif
                                    </td>
                                    <td class="py-3 px-2.5 text-center bg-kp-blue-50/40">
                                        @if(!empty($isCutoffLocked))
                                            <span class="font-mono font-bold text-kp-blue-700">{{ number_format($item->konsinyasi, 0, ',', '.') }}</span>
                                        @else
                                            <input type="number" name="jatah[{{ $item->agen_id }}][konsinyasi]" value="{{ $item->konsinyasi }}" data-field="konsinyasi" data-original="{{ $item->konsinyasi }}" data-base-border="border-kp-blue-200" min="0" class="jatah-input w-20 h-9 text-center text-sm rounded-lg border-kp-blue-200 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono font-bold text-kp-blue-700 transition">
                                        @endif
                                    </td>
                                    <td class="py-3 px-2.5 text-center">
                                        @if(!empty($isCutoffLocked))
                                            <span class="font-mono font-semibold text-slate-800">{{ number_format($item->gratis, 0, ',', '.') }}</span>
                                        @else
                                            <input type="number" name="jatah[{{ $item->agen_id }}][gratis]" value="{{ $item->gratis }}" data-field="gratis" data-original="{{ $item->gratis }}" data-base-border="border-slate-300" min="0" class="jatah-input w-20 h-9 text-center text-sm rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono font-semibold text-slate-900 transition">
                                        @endif
                                    </td>
                                    <td class="py-3 px-2.5 text-center">
                                        @if(!empty($isCutoffLocked))
                                            <span class="font-mono font-semibold text-slate-800">{{ number_format($item->pengenalan, 0, ',', '.') }}</span>
                                        @else
                                            <input type="number" name="jatah[{{ $item->agen_id }}][pengenalan]" value="{{ $item->pengenalan }}" data-field="pengenalan" data-original="{{ $item->pengenalan }}" data-base-border="border-slate-300" min="0" class="jatah-input w-20 h-9 text-center text-sm rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono font-semibold text-slate-900 transition">
                                        @endif
                                    </td>
                                    <td class="py-3 px-2.5 text-center">
                                        @if(!empty($isCutoffLocked))
                                            <span class="font-mono font-semibold text-slate-800">{{ number_format($item->insidentil, 0, ',', '.') }}</span>
                                        @else
                                            <input type="number" name="jatah[{{ $item->agen_id }}][insidentil]" value="{{ $item->insidentil }}" data-field="insidentil" data-original="{{ $item->insidentil }}" data-base-border="border-slate-300" min="0" class="jatah-input w-20 h-9 text-center text-sm rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono font-semibold text-slate-900 transition">
                                        @endif
                                    </td>
                                    <td class="row-total-eks py-3.5 px-3.5 text-right font-mono font-bold text-slate-900 tabular-nums">
                                        {{ number_format($item->total_jatah, 0, ',', '.') }} eks
                                    </td>
                                    <td class="py-3.5 px-3.5">
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="row-omset-val font-semibold text-kp-blue-700 tabular-nums">{{ number_format($item->estimasi_omset, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center"
                                        x-data="{ open: false }"
                                        :class="{ 'relative z-50': open }">
                                        <div class="relative inline-block text-left" @click.outside="open = false">
                                            <button type="button"
                                                    @click="open = !open"
                                                    class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                    :class="{ 'text-slate-900 bg-slate-100': open }"
                                                    title="Pilihan Aksi">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="5" r="1.5" />
                                                    <circle cx="12" cy="12" r="1.5" />
                                                    <circle cx="12" cy="19" r="1.5" />
                                                </svg>
                                            </button>

                                            <div x-cloak
                                                 x-show="open"
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 scale-100"
                                                 x-transition:leave-end="opacity-0 scale-95"
                                                 class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">

                                                <a href="{{ route('sirkulasi.tabel.agen', ['search' => $item->kode_agen]) }}"
                                                   @click="open = false"
                                                   class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Detail Agen</span>
                                                </a>

                                                <a href="{{ route('sirkulasi.cetak.nota-distribusi', ['jurusan' => $item->jurusan_kode, 'tanggal' => $targetTanggal]) }}"
                                                   @click="open = false"
                                                   class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span>Surat Jalan Rute</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-semibold text-slate-900 text-sm whitespace-nowrap">
                            <tr>
                                <td colspan="5" class="py-3.5 px-3.5 text-right uppercase text-xs font-bold text-slate-700">Total</td>
                                <td id="foot-total-langganan" class="py-3.5 px-3 text-center font-mono tabular-nums">{{ number_format($totalLangganan, 0, ',', '.') }}</td>
                                <td id="foot-total-konsinyasi" class="py-3.5 px-3 text-center font-mono text-kp-blue-700 tabular-nums">{{ number_format($totalKonsinyasi, 0, ',', '.') }}</td>
                                <td id="foot-total-gratis" class="py-3.5 px-3 text-center font-mono tabular-nums">{{ number_format($totalGratis, 0, ',', '.') }}</td>
                                <td id="foot-total-pengenalan" class="py-3.5 px-3 text-center font-mono tabular-nums">{{ number_format($totalPengenalan ?? 0, 0, ',', '.') }}</td>
                                <td id="foot-total-insidentil" class="py-3.5 px-3 text-center font-mono tabular-nums">{{ number_format($totalInsidentil, 0, ',', '.') }}</td>
                                <td id="foot-grand-total-eks" class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-900 tabular-nums">{{ number_format($grandTotalJatah, 0, ',', '.') }} eks</td>
                                <td id="foot-grand-total-omset" class="py-3.5 px-3.5 text-right font-mono font-bold text-kp-blue-700 tabular-nums">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
            </div>

            @if($hasJatahData && empty($isCutoffLocked))
                <!-- Bilah Simpan Kontekstual (Simpel & Selaras 60-30-10, Muncul Hanya Saat Ada Perubahan) -->
                <div id="dirtySaveBar" class="hidden sticky bottom-4 z-30 bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-lg flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="text-sm text-slate-600">
                        <span id="dirtyAgentCountText" class="font-semibold text-slate-900">0 agen diubah</span>
                        <span class="text-slate-300 mx-1.5">&bull;</span>
                        <span class="text-xs text-slate-500">Belum disimpan</span>
                    </div>
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button type="button"
                                id="btnBatalOplah"
                                class="inline-flex items-center justify-center h-9 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-lg text-sm font-semibold transition cursor-pointer">
                            Batalkan
                        </button>
                        <button type="submit"
                                id="btnSimpanOplah"
                                class="inline-flex items-center justify-center gap-2 h-9 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-lg text-sm font-bold shadow-xs transition cursor-pointer">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span id="btnSimpanLabel">Simpan Perubahan</span>
                        </button>
                    </div>
                </div>
            @endif
        </form>

        <!-- Modal Dialog: Pengajuan Memo Buka Data Sirkulasi (60-30-10) -->
        <div x-cloak x-show="showMemoModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="showMemoModal = false" class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Ajukan Memo Buka Data Oplah</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Permohonan otorisasi pembukaan kunci edisi ke Divisi Akuntansi</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-slate-100 text-kp-blue-700 border border-slate-200">
                        {{ \App\Models\MemoUnlock::generateNomorMemoSirkulasi() }}
                    </span>
                </div>

                <form method="POST" action="{{ route('sirkulasi.jatah.tutup.request-unlock') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="mode" value="harian">
                    <input type="hidden" name="tanggal" value="{{ $targetTanggal }}">
                    <input type="hidden" name="redirect_to" value="{{ url()->full() }}">

                    <!-- Ringkasan Metadata Pengajuan -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Tujuan Otorisasi</span>
                            <span class="font-semibold text-slate-900">Divisi Akuntansi</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Divisi Pemohon</span>
                            <span class="font-semibold text-slate-900">Divisi Sirkulasi &amp; Distribusi</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Target Tanggal Edisi</span>
                            <span class="font-bold text-kp-blue-700 font-mono">{{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori Urgensi / Alasan Buka</label>
                        <select name="kategori_alasan" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="Penjualan Baru / Susulan Masuk">Penjualan Susulan / Tambahan Oplah Agen</option>
                            <option value="Koreksi Alokasi Agen / Rute">Koreksi Alokasi Eksemplar Agen / Rute</option>
                            <option value="Retur Khusus / Rusak Cetak">Koreksi Retur / Eksemplar Rusak</option>
                            <option value="Oplah Acara / Even Daerah">Oplah Tambahan Even / Suplemen Khusus</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Estimasi Eksemplar</label>
                            <input type="number" name="estimasi_eksemplar" min="0" value="0"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Estimasi Nilai (Rp)</label>
                            <input type="number" name="estimasi_nominal" min="0" value="0"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Alasan Rinci Permohonan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="alasan" rows="3" required minlength="5"
                                  placeholder="Jelaskan alasan detail mengapa edisi yang sudah cut-off ini harus dibuka kembali..."
                                  class="w-full p-3 text-xs font-normal text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                    </div>

                    <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="showMemoModal = false"
                                class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs">
                            Kirim Memo ke Akuntansi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formEditJatah');
            const inputs = form ? Array.from(form.querySelectorAll('input.jatah-input')) : [];
            if (!form || inputs.length === 0) return;

            const hargaEceran = {{ (int) ($hargaEceran ?? 3000) }};
            const rows = Array.from(form.querySelectorAll('tr.jatah-row'));
            const dirtySaveBar = document.getElementById('dirtySaveBar');
            const dirtyAgentCountText = document.getElementById('dirtyAgentCountText');
            const btnBatal = document.getElementById('btnBatalOplah');
            const btnSimpan = document.getElementById('btnSimpanOplah');
            const btnSimpanLabel = document.getElementById('btnSimpanLabel');

            let dirtyAgentsCount = 0;
            let isSubmitting = false;

            const formatId = (num) => new Intl.NumberFormat('id-ID').format(num);

            const parsePositiveInt = (val) => {
                const parsed = parseInt(String(val ?? '').trim(), 10);
                return Number.isNaN(parsed) || parsed < 0 ? 0 : parsed;
            };

            function recalculateAndTrack() {
                const totals = {
                    langganan: 0,
                    konsinyasi: 0,
                    gratis: 0,
                    pengenalan: 0,
                    insidentil: 0,
                };
                let grandTotalEks = 0;
                let grandTotalOmset = 0;
                let changedRowCount = 0;

                rows.forEach((row) => {
                    let rowEks = 0;
                    let rowKonsinyasi = 0;
                    let rowHasChange = false;

                    const rowInputs = row.querySelectorAll('input.jatah-input');
                    rowInputs.forEach((inp) => {
                        const field = inp.dataset.field;
                        const original = parsePositiveInt(inp.dataset.original);
                        const current = parsePositiveInt(inp.value);
                        const baseBorder = inp.dataset.baseBorder || 'border-slate-300';

                        // Deteksi perubahan karakter/nilai terhadap data awal
                        const isRawEmptyOrChanged = inp.value.trim() === ''
                            ? original !== 0
                            : current !== original;

                        if (isRawEmptyOrChanged) {
                            rowHasChange = true;
                            inp.classList.remove(baseBorder, 'bg-white');
                            inp.classList.add('bg-kp-blue-50/70', 'border-kp-blue-500', 'ring-1', 'ring-kp-blue-500/30');
                        } else {
                            inp.classList.remove('bg-kp-blue-50/70', 'border-kp-blue-500', 'ring-1', 'ring-kp-blue-500/30');
                            inp.classList.add(baseBorder);
                        }

                        if (field && Object.prototype.hasOwnProperty.call(totals, field)) {
                            totals[field] += current;
                        }
                        if (field === 'konsinyasi') {
                            rowKonsinyasi = current;
                        }
                        rowEks += current;
                    });

                    if (rowHasChange) {
                        changedRowCount++;
                    }

                    const rowOmset = rowKonsinyasi * hargaEceran;
                    grandTotalEks += rowEks;
                    grandTotalOmset += rowOmset;

                    const rowTotalEl = row.querySelector('.row-total-eks');
                    const rowOmsetEl = row.querySelector('.row-omset-val');
                    if (rowTotalEl) rowTotalEl.textContent = formatId(rowEks) + ' eks';
                    if (rowOmsetEl) rowOmsetEl.textContent = formatId(rowOmset);
                });

                // Update Footer Tabel
                const setText = (id, text) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = text;
                };

                setText('foot-total-langganan', formatId(totals.langganan));
                setText('foot-total-konsinyasi', formatId(totals.konsinyasi));
                setText('foot-total-gratis', formatId(totals.gratis));
                setText('foot-total-pengenalan', formatId(totals.pengenalan));
                setText('foot-total-insidentil', formatId(totals.insidentil));
                setText('foot-grand-total-eks', formatId(grandTotalEks) + ' eks');
                setText('foot-grand-total-omset', 'Rp ' + formatId(grandTotalOmset));

                // Update KPI Cards di Atas Tabel
                setText('kpi-langganan', formatId(totals.langganan));
                setText('kpi-konsinyasi', formatId(totals.konsinyasi));
                setText('kpi-gratis', formatId(totals.gratis));
                setText('kpi-pengenalan', formatId(totals.pengenalan));
                setText('kpi-insidentil', formatId(totals.insidentil));
                setText('kpi-grand-total', formatId(grandTotalEks));

                // Update Sticky Save Bar
                dirtyAgentsCount = changedRowCount;
                if (dirtySaveBar) {
                    if (changedRowCount > 0) {
                        dirtySaveBar.classList.remove('hidden');
                        dirtySaveBar.classList.add('flex');
                    } else {
                        dirtySaveBar.classList.add('hidden');
                        dirtySaveBar.classList.remove('flex');
                    }
                }
                if (dirtyAgentCountText) {
                    dirtyAgentCountText.textContent = changedRowCount + ' agen diubah';
                }
            }

            inputs.forEach((inp) => {
                inp.addEventListener('input', recalculateAndTrack);
                inp.addEventListener('blur', function () {
                    const normalized = parsePositiveInt(this.value);
                    this.value = String(normalized);
                    recalculateAndTrack();
                });
            });

            if (btnBatal) {
                btnBatal.addEventListener('click', function () {
                    inputs.forEach((inp) => {
                        inp.value = inp.dataset.original ?? '0';
                    });
                    recalculateAndTrack();
                });
            }

            form.addEventListener('submit', function () {
                inputs.forEach((inp) => {
                    inp.value = String(parsePositiveInt(inp.value));
                });
                isSubmitting = true;
                if (btnSimpan) {
                    btnSimpan.disabled = true;
                    btnSimpan.classList.add('opacity-75', 'cursor-not-allowed');
                }
                if (btnSimpanLabel) {
                    btnSimpanLabel.textContent = 'Menyimpan...';
                }
            });

            window.addEventListener('beforeunload', function (e) {
                if (dirtyAgentsCount > 0 && !isSubmitting) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
