<x-app-layout>
    <div class="space-y-6">

        <!-- ============================================================== -->
        <!-- HEADER HALAMAN: STANDAR SELARAS KORAN & ONLINE                -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        {{ isset($transaksi) ? 'Ubah Data & Jadwal Order Siaran Priangan TV' : 'Entri Order Penayangan Priangan TV' }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl font-normal">
                        {{ isset($transaksi) ? 'Formulir koreksi dan reschedule penayangan siaran televisi Priangan TV.' : 'Formulir booking siaran iklan, podcast, dan multimedia Priangan TV.' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0 flex-wrap sm:flex-nowrap">
                <div class="text-right hidden sm:block">
                    <div class="text-[11px] font-semibold text-slate-500">No. Faktur</div>
                    <div class="font-mono text-base font-bold text-kp-blue-700 leading-tight">{{ $nofakturpriangan }}</div>
                </div>
                <a href="{{ route('transaksipriangan.index') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                    &larr; <span>Transaksi TV</span>
                </a>
            </div>
        </div>

        <!-- Notice Deadline & Cut-Off Siar Priangan TV (3 Status: Normal / Kuning / Merah) -->
        @php
            \Carbon\Carbon::setLocale('id');
            $jamCutoffStr = $configs['jam_cutoff'] ?? '14:00';
            $nowTime = \Carbon\Carbon::now('Asia/Jakarta');
            $cutoffToday = \Carbon\Carbon::today('Asia/Jakarta')->setTimeFromTimeString($jamCutoffStr);
            $warningToday = $cutoffToday->copy()->subHour();

            if ($nowTime->greaterThanOrEqualTo($cutoffToday)) {
                $cutoffState = 'passed';
            } elseif ($nowTime->greaterThanOrEqualTo($warningToday)) {
                $cutoffState = 'warning';
            } else {
                $cutoffState = 'normal';
            }

            $besok = $nowTime->copy()->addDay()->locale('id')->translatedFormat('l, d F Y');
            $lusa  = $nowTime->copy()->addDays(2)->locale('id')->translatedFormat('l, d F Y');
            $edisiTerdekat = ($cutoffState === 'passed') ? $lusa : $besok;

            $bannerBoxClass = match($cutoffState) {
                'passed'  => 'bg-rose-50 border-rose-300 text-rose-950',
                'warning' => 'bg-amber-50 border-amber-300 text-amber-950',
                default   => 'bg-white border-slate-200 text-slate-800',
            };

            $bannerIconBoxClass = match($cutoffState) {
                'passed'  => 'bg-rose-100 text-rose-700 border border-rose-300',
                'warning' => 'bg-amber-100 text-amber-800 border border-amber-300',
                default   => 'bg-slate-100 text-slate-600 border border-slate-200',
            };

            $bannerTitleClass = match($cutoffState) {
                'passed'  => 'text-rose-800 font-bold',
                'warning' => 'text-amber-800 font-bold',
                default   => 'text-slate-900 font-bold',
            };

            $bannerEdisiClass = match($cutoffState) {
                'passed'  => 'text-rose-700 font-bold',
                'warning' => 'text-amber-700 font-bold',
                default   => 'text-kp-blue-700 font-semibold',
            };

            $bannerClockClass = match($cutoffState) {
                'passed'  => 'bg-white border-rose-200 text-rose-700',
                'warning' => 'bg-white border-amber-200 text-amber-700',
                default   => 'bg-white border-slate-200 text-slate-600',
            };

            $bannerTitleText = match($cutoffState) {
                'passed'  => 'Lewat Batas Siar: ' . $jamCutoffStr . ' WIB',
                'warning' => 'Mendekati Batas Siar: ' . $jamCutoffStr . ' WIB',
                default   => 'Batas Siar Hari Ini: ' . $jamCutoffStr . ' WIB',
            };
        @endphp
        <div class="rounded-xl border {{ $bannerBoxClass }} px-4 py-2.5 shadow-2xs transition">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg {{ $bannerIconBoxClass }} flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm">
                        <span class="{{ $bannerTitleClass }}">
                            {{ $bannerTitleText }}
                        </span>
                        <span class="text-slate-300 hidden sm:inline">|</span>
                        <span class="text-slate-600 font-medium">
                            Slot Siar Terdekat: <span class="{{ $bannerEdisiClass }}">{{ $edisiTerdekat }}</span>
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 self-end sm:self-center font-mono text-xs font-semibold px-2.5 py-1 rounded-md border shadow-2xs {{ $bannerClockClass }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ $nowTime->format('H:i') }} WIB</span>
                </div>
            </div>
        </div>

        <!-- Feedback Validasi Error Global -->
        @if (isset($errors) && $errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1">
                <div class="font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span>Mohon periksa kembali formulir Anda:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-2 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- FORM UTAMA DENGAN DUA KOLOM (KIRI: INPUT, KANAN: SIMULATOR)    -->
        <!-- ============================================================== -->
        <form id="formTransaksiPriangan" method="POST" action="{{ isset($transaksi) ? route('transaksipriangan.update', $transaksi->id) : route('transaksipriangan.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($transaksi))
                @method('PUT')
            @endif

            <!-- Hidden Technical Fields -->
            <input type="hidden" name="nofakturpriangan" value="{{ $nofakturpriangan }}">
            <input type="hidden" name="tanggal_transaksipriangan" value="{{ old('tanggal_transaksipriangan', isset($transaksi) ? $transaksi->tanggal_transaksipriangan?->format('Y-m-d') : date('Y-m-d')) }}">
            <input type="hidden" name="ppn_persen" id="ppn_persen" value="{{ old('ppn_persen', isset($transaksi) ? $transaksi->ppn_persen : ($ppnPersen ?? 12)) }}">
            <input type="hidden" name="jenis_pembayaran" id="jenis_pembayaran" value="{{ old('jenis_pembayaran', isset($transaksi) ? ($transaksi->piutang_transaksipriangan > 0 ? 'Kredit' : 'Tunai') : 'Tunai') }}">
            <input type="hidden" name="pelanggan_id" id="pelanggan_id" value="{{ old('pelanggan_id', isset($transaksi) ? $transaksi->pelanggan_id : '') }}">
            <input type="hidden" name="nama_pemasangpriangan" id="nama_pemasangpriangan" value="{{ old('nama_pemasangpriangan', isset($transaksi) ? $transaksi->nama_pemasangpriangan : '') }}">
            <input type="hidden" name="sales_iklanpriangan" id="sales_iklanpriangan" value="{{ old('sales_iklanpriangan', isset($transaksi) ? $transaksi->sales_iklanpriangan : 'Front Office (Loket Langsung)') }}">
            <input type="hidden" name="tanggal_muatiklanpriangan" id="tanggal_muatiklanpriangan" value="{{ old('tanggal_muatiklanpriangan', isset($transaksi) ? $transaksi->tanggal_muatiklanpriangan?->format('Y-m-d') : date('Y-m-d')) }}">
            <input type="hidden" name="tanggal_muat_list" id="tanggal_muat_list" value="{{ old('tanggal_muat_list', isset($transaksi) ? $transaksi->tanggal_muat_list : date('Y-m-d')) }}">
            <input type="hidden" name="kategori_tv_form" id="kategori_tv_form" value="{{ old('kategori_tv_form', 'PODCAST') }}">
            <input type="hidden" name="skema_pembayaran" id="skema_pembayaran" value="{{ old('skema_pembayaran', 'sekali_tayang') }}">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- ============================================================== -->
                <!-- PANEL KIRI: INPUT DATA TRANSAKSI                               -->
                <!-- ============================================================== -->
                <div class="lg:col-span-7 space-y-5">

                    <!-- SEKSI 1: CARA BAYAR & DATA PEMASANG (PERSIS KORAN & ONLINE) -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">1</span>
                                <span>Data Pemesan &amp; Instansi</span>
                            </span>
                        </div>

                        <!-- PILIHAN SKEMA PEMBAYARAN: TUNAI vs KREDIT -->
                        <div>
                            <label class="text-sm font-semibold text-slate-700 mb-2 block">
                                Skema Pembayaran <span class="text-rose-500">*</span>
                            </label>

                            @php
                                $currentJenisBayar = old('jenis_pembayaran', isset($transaksi) ? ($transaksi->piutang_transaksipriangan > 0 ? 'Kredit' : 'Tunai') : 'Tunai');
                                $isTunai = ($currentJenisBayar === 'Tunai');
                            @endphp
                            <div class="grid grid-cols-2 gap-2.5">
                                <!-- Card 1: TUNAI -->
                                <button type="button" id="cardBayarTunai" onclick="setJenisPembayaran('Tunai')"
                                        class="px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between {{ $isTunai ? 'bg-emerald-50/70 border-emerald-500 text-emerald-950 font-semibold shadow-2xs' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 font-medium' }}">
                                    <div class="flex items-center gap-2.5">
                                        <div id="iconBoxTunai" class="w-6 h-6 rounded-lg {{ $isTunai ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <rect x="2" y="6" width="20" height="12" rx="2" />
                                                <circle cx="12" cy="12" r="2" />
                                                <path d="M6 12h.01M18 12h.01" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-semibold tracking-tight">Tunai</span>
                                    </div>
                                    <span id="dotBayarTunai" class="{{ $isTunai ? 'text-emerald-600' : 'text-transparent' }} flex items-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </button>

                                <!-- Card 2: KREDIT -->
                                <button type="button" id="cardBayarKredit" onclick="setJenisPembayaran('Kredit')"
                                        class="px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between {{ !$isTunai ? 'bg-amber-50/70 border-amber-500 text-amber-950 font-semibold shadow-2xs' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 font-medium' }}">
                                    <div class="flex items-center gap-2.5">
                                        <div id="iconBoxKredit" class="w-6 h-6 rounded-lg {{ !$isTunai ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <rect x="2" y="5" width="20" height="14" rx="2" />
                                                <line x1="2" y1="10" x2="22" y2="10" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-semibold tracking-tight">Kredit</span>
                                    </div>
                                    <span id="dotBayarKredit" class="{{ !$isTunai ? 'text-amber-600' : 'text-transparent' }} flex items-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Baris Input Pemasang: 2 Kolom Sejajar (Pilih Pelanggan vs Input Nama Langsung) -->
                        <div class="space-y-3 pt-3 border-t border-slate-100">
                            @php
                                $selectedOldPelanggan = old('pelanggan_id') 
                                    ? $pelanggans->firstWhere('id', old('pelanggan_id')) 
                                    : (isset($transaksi) ? ($transaksi->pelanggan ?? null) : null);
                                $oldNamaPemasang = old('nama_pemasangpriangan', $selectedOldPelanggan->nama_instansi ?? (isset($transaksi) ? $transaksi->nama_pemasangpriangan : ''));
                            @endphp

                            <!-- Grid 2 Kolom Input: Data Pelanggan & Input Manual Langsung -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                
                                <!-- KOLOM 1: PILIH DARI DATA PELANGGAN -->
                                <div class="space-y-1.5 relative min-w-0">
                                    <div class="flex items-center justify-between gap-2 h-7">
                                        <label onclick="handleSearchPelangganFocus(true); document.getElementById('inputSearchPelanggan')?.focus();" 
                                               class="text-sm font-semibold text-slate-700 whitespace-nowrap cursor-pointer hover:text-kp-blue-600 transition"
                                               title="Klik untuk mencari atau memilih pelanggan">
                                            Pelanggan Terdaftar
                                        </label>
                                        <button type="button" onclick="openModalPelangganBaru()" 
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 border border-kp-blue-200/90 hover:border-kp-blue-300 transition shadow-2xs active:scale-95 cursor-pointer whitespace-nowrap shrink-0"
                                                title="Tambah Data Pelanggan Baru">
                                            <svg class="w-3 h-3 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            <span>Tambah Baru</span>
                                        </button>
                                    </div>

                                    <!-- A. Kondisi Master Terpilih: Card Klien Terpilih -->
                                    <div id="cardPelangganTerpilih" 
                                         class="{{ $selectedOldPelanggan ? '' : 'hidden' }} px-3 py-2 rounded-xl bg-kp-blue-50/70 border border-kp-blue-200/80 flex items-center justify-between gap-3 shadow-2xs min-h-[42px]">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span id="labelNamaPelanggan" class="font-bold text-slate-900 text-sm truncate">{{ $selectedOldPelanggan->nama_instansi ?? '' }}</span>
                                                <span id="badgeJenisPelanggan" class="px-2 py-0.5 text-xs font-semibold rounded bg-white text-slate-700 border border-slate-200 uppercase">{{ $selectedOldPelanggan->jenis_klien ?? '' }}</span>
                                                <span id="labelKodePelanggan" class="text-xs font-mono text-slate-400">[{{ $selectedOldPelanggan->kode_pelanggan ?? '' }}]</span>
                                            </div>
                                            <div class="text-xs text-slate-500 flex items-center gap-3 mt-1 flex-wrap">
                                                <span id="labelNpwpPelanggan" class="inline-flex items-center gap-1 truncate {{ empty($selectedOldPelanggan?->npwp_nik) ? 'hidden' : '' }}">
                                                    <span class="font-mono text-xs bg-white text-slate-700 px-1.5 py-0.5 rounded border border-slate-200">NPWP: <span id="textNpwpPelanggan">{{ $selectedOldPelanggan->npwp_nik ?? '' }}</span></span>
                                                </span>
                                                <span id="labelTelpPelanggan" class="inline-flex items-center gap-1 truncate {{ empty($selectedOldPelanggan?->no_telp) ? 'hidden' : '' }}">
                                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    <span id="textTelpPelanggan">{{ $selectedOldPelanggan->no_telp ?? '' }}</span>
                                                </span>
                                                <span id="labelAlamatPelanggan" class="inline-flex items-center gap-1 truncate {{ empty($selectedOldPelanggan?->alamat) ? 'hidden' : '' }}">
                                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <span id="textAlamatPelanggan">{{ $selectedOldPelanggan->alamat ?? '' }}</span>
                                                </span>
                                            </div>
                                        </div>
                                        <button type="button" onclick="resetPilihanPelanggan()"
                                                class="px-2.5 py-1 text-xs font-semibold text-slate-600 hover:text-rose-600 bg-white hover:bg-rose-50 rounded-lg border border-slate-200 hover:border-rose-200 transition cursor-pointer shrink-0 flex items-center gap-1 shadow-2xs"
                                                title="Batalkan pilihan pelanggan">
                                            <span>Batal</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>

                                    <!-- B. Autocomplete Pencarian Typeahead Master -->
                                    <div id="containerSearchPelanggan" class="{{ $selectedOldPelanggan ? 'hidden' : '' }} relative">
                                        <div class="relative flex items-center">
                                            <div class="absolute pointer-events-none text-slate-400 flex items-center justify-center" style="left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px;">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            </div>
                                            <input type="text" id="inputSearchPelanggan"
                                                   autocomplete="off"
                                                   onfocus="handleSearchPelangganFocus()"
                                                   oninput="handleSearchPelangganInput(this.value)"
                                                   onkeydown="handleSearchPelangganKeydown(event)"
                                                   placeholder="Cari nama pelanggan..."
                                                   style="padding-left: 36px; padding-right: 44px;"
                                                   class="w-full h-11 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                                            <div class="absolute flex items-center gap-1" style="right: 10px; top: 50%; transform: translateY(-50%);">
                                                <button type="button" onclick="handleSearchPelangganFocus(true)"
                                                        class="p-1 text-slate-400 hover:text-slate-600 transition cursor-pointer" title="Buka Daftar Pelanggan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Floating Dropdown Hasil Pencarian -->
                                        <div id="dropdownHasilPelanggan" 
                                             class="absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 max-h-72 overflow-y-auto divide-y divide-slate-100 hidden">
                                            <div id="listPelangganItem" class="p-1.5 space-y-0.5"></div>
                                            <div class="p-2.5 bg-slate-50 text-center flex items-center justify-between border-t border-slate-100 text-xs">
                                                <span class="text-xs text-slate-500">Pelanggan baru?</span>
                                                <button type="button" onclick="openModalPelangganBaru()" class="text-xs font-semibold text-kp-blue-600 hover:underline cursor-pointer">
                                                    + Tambah Pelanggan Baru
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- KOLOM 2: INPUT NAMA PEMASANG MANUAL -->
                                <div class="space-y-1.5 min-w-0">
                                    <div class="flex items-center h-7">
                                        <label class="text-sm font-semibold text-slate-700 whitespace-nowrap">
                                            Nama Pemesan / Instansi <span class="text-rose-500">*</span>
                                        </label>
                                    </div>
                                    <div class="relative flex items-center">
                                        <input type="text" id="inputManualNamaPemasang"
                                               oninput="handleManualNamaInput(this.value)"
                                               placeholder="Nama instansi / pemesan"
                                               value="{{ $oldNamaPemasang }}"
                                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Baris: No. Telepon / WA & Nama PIC -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="text-sm font-semibold text-slate-700 mb-1.5 block">
                                    No. Telepon / WA
                                </label>
                                <div class="relative flex items-center">
                                    <input type="text" name="no_telp" id="no_telp"
                                           placeholder="08xxxxxxxxxx"
                                           value="{{ old('no_telp', isset($transaksi) ? $transaksi->no_telp : ($selectedOldPelanggan?->no_telp ?? '')) }}"
                                           oninput="updateSimulatorLive()"
                                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                                    <span>Nama PIC</span>
                                    <span class="text-xs text-slate-400 font-normal">Opsional</span>
                                </label>
                                <input type="text" name="nama_pic" id="nama_pic"
                                       placeholder="Nama narahubung (jika ada)"
                                       value="{{ old('nama_pic', isset($transaksi) ? $transaksi->nama_pic : '') }}"
                                       oninput="updateSimulatorLive()"
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>

                        <!-- Baris: Alamat Lengkap -->
                        <div class="pt-1">
                            <label class="text-sm font-semibold text-slate-700 mb-1.5 block">
                                Alamat
                            </label>
                            <textarea name="alamat_pemasangpriangan" id="alamat_pemasangpriangan" rows="2"
                                      placeholder="Alamat lengkap pemesan..."
                                      oninput="updateSimulatorLive()"
                                      class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs resize-none">{{ old('alamat_pemasangpriangan', isset($transaksi) ? $transaksi->alamat_pemasangpriangan : ($selectedOldPelanggan?->alamat ?? '')) }}</textarea>
                        </div>

                        <!-- Sales & Komisioner -->
                        <div class="pt-4 border-t border-slate-100 space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>Sales &amp; Komisioner</span>
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                <!-- Card 1: Sales / AE -->
                                <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/90 space-y-2.5 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <label class="text-sm font-semibold text-slate-700 whitespace-nowrap">
                                            Sales / AE <span class="text-rose-500">*</span>
                                        </label>
                                        <button type="button" onclick="openModalSalesAeBaru()"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 border border-kp-blue-200/90 hover:border-kp-blue-300 transition shadow-2xs active:scale-95 cursor-pointer whitespace-nowrap shrink-0"
                                                title="Tambah Sales / AE Baru">
                                            <svg class="w-3 h-3 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            <span>Tambah Baru</span>
                                        </button>
                                    </div>
                                    @php
                                        $defaultAeId = old('sales_ae_id', isset($transaksi) 
                                            ? $transaksi->sales_ae_id 
                                            : ($salesAes->firstWhere('jenis_kode', 'FO')->id ?? $salesAes->first()?->id ?? ''));
                                        $selectedAe = $salesAes->firstWhere('id', $defaultAeId);
                                        $initialSalesName = $selectedAe 
                                            ? (($selectedAe->kode_ae === 'FO') ? 'Front Office (Loket Langsung)' : ($selectedAe->kode_ae . ' - ' . $selectedAe->nama_ae))
                                            : 'Front Office (Loket Langsung)';
                                    @endphp
                                    <select name="sales_ae_id" id="sales_ae_id" onchange="handleSalesAeChange(this)"
                                            class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                        @foreach($salesAes as $ae)
                                            @php
                                                $roleLabel = match($ae->jenis_kode) {
                                                    'FO' => 'Loket Kantor',
                                                    'STRUKTURAL' => 'Account Executive',
                                                    'KORESPONDEN' => 'Koresponden',
                                                    'AGEN' => 'Keagenan',
                                                    default => ($ae->jenis_kode ?? 'AE')
                                                };
                                                $displayNama = ($ae->kode_ae === 'FO') 
                                                    ? 'Front Office (Loket Langsung)' 
                                                    : $ae->nama_ae . ' — ' . $roleLabel;
                                            @endphp
                                            <option value="{{ $ae->id }}"
                                                    data-kode="{{ $ae->kode_ae }}"
                                                    data-nama="{{ $ae->nama_ae }}"
                                                    {{ $defaultAeId == $ae->id ? 'selected' : '' }}>
                                                {{ $displayNama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="sales_iklanpriangan" id="sales_iklanpriangan" value="{{ old('sales_iklanpriangan', isset($transaksi) ? $transaksi->sales_iklanpriangan : $initialSalesName) }}">

                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                            Insentif Sales
                                        </label>
                                        <div class="grid grid-cols-5 gap-2">
                                            <div class="col-span-2 relative">
                                                <input type="number" name="insentif_persen" id="insentif_persen" 
                                                       value="{{ old('insentif_persen', isset($transaksi) ? $transaksi->insentif_persen : 0) }}" step="0.5" min="0" max="100"
                                                       placeholder="0"
                                                       oninput="handleInsentifPersenChange(this.value)"
                                                       class="w-full h-10 px-2.5 pr-6 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                                <span class="absolute right-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">%</span>
                                            </div>
                                            <div class="col-span-3 relative">
                                                <span class="absolute left-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">Rp</span>
                                                <input type="text" name="insentif_transaksipriangan" id="insentif_transaksipriangan" 
                                                       value="{{ old('insentif_transaksipriangan', isset($transaksi) && $transaksi->insentif_transaksipriangan ? number_format($transaksi->insentif_transaksipriangan, 0, ',', '.') : 0) }}"
                                                       placeholder="0"
                                                       onkeyup="handleInsentifNominalInput(this)"
                                                       class="w-full h-10 pl-8 pr-2.5 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2: Komisioner -->
                                <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/90 space-y-2.5 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <label class="text-sm font-semibold text-slate-700 whitespace-nowrap">
                                            Komisioner
                                        </label>
                                        <span class="text-xs text-slate-400 font-normal shrink-0">Opsional</span>
                                    </div>
                                    <div>
                                        <input type="text" name="komisioner" id="komisioner" 
                                               value="{{ old('komisioner', isset($transaksi) ? $transaksi->komisioner : '') }}"
                                               placeholder="Nama komisioner (jika ada)"
                                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                            Komisi Komisioner
                                        </label>
                                        <div class="grid grid-cols-5 gap-2">
                                            <div class="col-span-2 relative">
                                                <input type="number" name="komisi_persen" id="komisi_persen" 
                                                       value="{{ old('komisi_persen', isset($transaksi) ? $transaksi->komisi_persen : 0) }}" step="0.5" min="0" max="100"
                                                       placeholder="0"
                                                       oninput="handleKomisiPersenChange(this.value)"
                                                       class="w-full h-10 px-2.5 pr-6 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                                <span class="absolute right-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">%</span>
                                            </div>
                                            <div class="col-span-3 relative">
                                                <span class="absolute left-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">Rp</span>
                                                <input type="text" name="komisi_transaksipriangan" id="komisi_transaksipriangan" 
                                                       value="{{ old('komisi_transaksipriangan', isset($transaksi) && $transaksi->komisi_transaksipriangan ? number_format($transaksi->komisi_transaksipriangan, 0, ',', '.') : 0) }}"
                                                       placeholder="0"
                                                       onkeyup="handleKomisiNominalInput(this)"
                                                       class="w-full h-10 pl-8 pr-2.5 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================== -->
                    <!-- SEKSI 2: JENIS & PLATFORM PRIANGAN TV (4 KATEGORI RESMI)       -->
                    <!-- ============================================================== -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">2</span>
                                <span>Jenis Iklan &amp; Penempatan Priangan TV</span>
                            </span>
                            <span id="badge_tv_skema" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-100 text-kp-blue-800 border border-kp-blue-200">
                                1 Kali Tayang / Spot
                            </span>
                        </div>

                        @php
                            $tabOrderTv = ['PODCAST', 'INSTAGRAM', 'TIKTOK', 'BANNER_WEB', 'VIDEO_LIPSUS'];
                            $tabLabelsTv = [
                                'PODCAST'      => 'Podcast TV',
                                'INSTAGRAM'    => 'Instagram',
                                'TIKTOK'       => 'TikTok',
                                'BANNER_WEB'   => 'Banner Web',
                                'VIDEO_LIPSUS' => 'Video Lipsus',
                            ];
                            $tabPlatformNames = [
                                'PODCAST'      => 'Studio Podcast TV',
                                'INSTAGRAM'    => 'Instagram Priangan TV',
                                'TIKTOK'       => 'TikTok Priangan TV',
                                'BANNER_WEB'   => 'Website Priangan TV',
                                'VIDEO_LIPSUS' => 'Liputan Khusus TV',
                            ];
                            $tabSubs = [
                                'PODCAST'      => 'Studio & Tapping',
                                'INSTAGRAM'    => '@priangantv_official',
                                'TIKTOK'       => '@priangantv',
                                'BANNER_WEB'   => 'priangantv.com',
                                'VIDEO_LIPSUS' => 'Liputan & News',
                            ];

                            // Mapping items per tab
                            $itemsByTab = [
                                'PODCAST'      => $iklanpriangan->where('tipe_dasar', 'PODCAST'),
                                'INSTAGRAM'    => $iklanpriangan->filter(fn($p) => $p->tipe_dasar === 'INSTAGRAM' || str_contains(strtolower($p->nama_jenis), 'instagram')),
                                'TIKTOK'       => $iklanpriangan->filter(fn($p) => $p->tipe_dasar === 'TIKTOK' || str_contains(strtolower($p->nama_jenis), 'tiktok')),
                                'BANNER_WEB'   => $iklanpriangan->where('tipe_dasar', 'BANNER_WEB'),
                                'VIDEO_LIPSUS' => $iklanpriangan->where('tipe_dasar', 'VIDEO_LIPSUS'),
                            ];

                            $defaultTabTv = old('kategori_tv_form', 'PODCAST');
                            if (!isset($itemsByTab[$defaultTabTv])) $defaultTabTv = 'PODCAST';

                            $targetTvProductId = old('id_iklanpriangan', isset($transaksi) ? $transaksi->id_iklanpriangan : null);
                            $initialTvProduct = $targetTvProductId ? $iklanpriangan->firstWhere('id', $targetTvProductId) : null;

                            // Old platforms selection (mendukung multi platform seperti Podcast + Medsos TV)
                            $oldTvPlatforms = old('platforms_publikasi', isset($transaksi) ? ($transaksi->platforms_publikasi ?? []) : []);
                            if (is_string($oldTvPlatforms)) {
                                $oldTvPlatforms = array_values(array_filter(array_map('trim', explode(',', $oldTvPlatforms))));
                            }
                            if (!is_array($oldTvPlatforms)) {
                                $oldTvPlatforms = [];
                            }
                        @endphp

                        <!-- Hidden Product ID & Active Format Tab Field -->
                        <input type="hidden" name="id_iklanpriangan" id="id_iklanpriangan" value="{{ $initialTvProduct?->id ?? '' }}">
                        <input type="hidden" name="kategori_tv_form" id="kategori_tv_form" value="{{ $defaultTabTv }}">
                        
                        <!-- Multi-select Hidden Containers for Products & Platforms -->
                        <div id="containerTvSelectedProductIds">
                            @if($initialTvProduct)
                                <input type="hidden" name="selected_produk_ids[]" value="{{ $initialTvProduct->id }}">
                            @endif
                        </div>
                        <div id="containerTvPlatformsPublikasi">
                            @foreach($oldTvPlatforms as $plat)
                                <input type="hidden" name="platforms_publikasi[]" value="{{ $plat }}">
                            @endforeach
                        </div>
                        <input type="hidden" name="produk_jasa" id="in_tv_produk_jasa" value="{{ $initialTvProduct?->nama_jenis ?? '' }}">

                        <!-- Tab Platform Pills (Selaras Foto 1 & Transaksi Koran) -->
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($tabOrderTv as $tabKey)
                                @php
                                    $tabName = $tabLabelsTv[$tabKey] ?? $tabKey;
                                    $countItems = ($itemsByTab[$tabKey] ?? collect())->count();
                                    $isTabActive = ($defaultTabTv === $tabKey);
                                @endphp
                                <button type="button" 
                                        onclick="switchTvTab('{{ $tabKey }}')"
                                        id="tabTvBtn_{{ $tabKey }}"
                                        class="tab-tv-btn px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 {{ $isTabActive ? 'bg-kp-blue-600 text-white font-semibold shadow-xs border border-kp-blue-600' : 'bg-white text-slate-700 font-medium border border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                                    <span>{{ $tabName }}</span>
                                    <span class="px-1.5 py-0.5 rounded-full text-xs font-semibold {{ $isTabActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $countItems }}
                                    </span>
                                </button>
                            @endforeach
                        </div>

                        <!-- TAB CONTENTS (Kartu Bersih Sesuai Foto 1, Ceklist Lancip Sesuai Foto 2) -->
                        @foreach($tabOrderTv as $tabKey)
                            @php
                                $tabItems = $itemsByTab[$tabKey] ?? collect();
                                $isTabActive = ($defaultTabTv === $tabKey);
                            @endphp
                            <div id="tvTabContent_{{ $tabKey }}" class="tv-tab-content space-y-3 {{ $isTabActive ? '' : 'hidden' }}">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($tabItems as $prod)
                                        @php $isSel = ($initialTvProduct && $initialTvProduct->id == $prod->id); @endphp
                                        <button type="button" 
                                                onclick="toggleProdukTv({{ $prod->id }}, '{{ $prod->kode_jenis }}', '{{ addslashes($prod->nama_jenis) }}', {{ $prod->tarif_dasar }}, '{{ $tabKey }}')"
                                                id="cardTvProduk_{{ $prod->id }}"
                                                data-id="{{ $prod->id }}"
                                                data-tarif="{{ $prod->tarif_dasar }}"
                                                data-nama="{{ $prod->nama_jenis }}"
                                                data-tab="{{ $tabKey }}"
                                                class="tv-produk-card text-left p-3.5 rounded-xl border transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2.5 min-h-[92px] {{ $isSel ? 'border-2 border-kp-blue-600 bg-sky-50/20 ring-1 ring-kp-blue-500/20 shadow-xs' : 'border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50' }}">
                                            
                                            <!-- Badge Ceklist Lancip Segitiga Sudut Kiri Atas (Foto 2 Style) -->
                                            <span class="badge-corner-check absolute top-0 left-0 w-7 h-7 pointer-events-none z-10 {{ $isSel ? '' : 'hidden' }}">
                                                <svg class="w-7 h-7" viewBox="0 0 28 28">
                                                    <path d="M0 0 L28 0 L0 28 Z" fill="#0A72AC" />
                                                    <path d="M4 9 L8 13 L14 6" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </span>

                                            <div class="text-sm font-bold text-slate-900 leading-snug">
                                                {{ $prod->nama_jenis }}
                                            </div>
                                            <div class="text-xs font-bold text-kp-blue-700 font-mono">
                                                Rp {{ number_format($prod->tarif_dasar, 0, ',', '.') }}
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                    </div>

                    <!-- ============================================================== -->
                    <!-- SEKSI 3: MATERI IKLAN & DESKRIPSI                              -->
                    <!-- ============================================================== -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">3</span>
                                <span>Materi Iklan &amp; Deskripsi Siar</span>
                            </span>
                        </div>

                        <!-- Baris: Judul Iklan TV -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Judul / Materi Iklan TV <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="materi_iklan" id="materi_iklan" 
                                   value="{{ old('materi_iklan', isset($transaksi) ? $transaksi->materi_iklan : '') }}" required 
                                   placeholder="Contoh: Talkshow Bincang Bisnis Priangan / Spot Iklan Peluncuran Produk"
                                   oninput="updateSimulatorLive()"
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                        </div>

                        <!-- Baris: Durasi Spot & Jam Tayang Program -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Durasi Siar / Spot <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" name="durasi_detik" id="durasi_detik"
                                           value="{{ old('durasi_detik', isset($transaksi) ? $transaksi->durasi_detik : 30) }}" min="5" max="7200" required
                                           oninput="updateSimulatorLive()"
                                           class="w-full h-10 px-3.5 pr-16 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                    <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">Detik</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Jam Tayang / Slot Program <span class="text-xs text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <input type="text" name="jam_tayang" id="jam_tayang" 
                                       value="{{ old('jam_tayang', isset($transaksi) ? $transaksi->jam_tayang : '19:00 WIB (Prime Time)') }}" 
                                       placeholder="Contoh: 19:00 WIB (Prime Time)"
                                       oninput="updateSimulatorLive()"
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>

                        <!-- Upload File Materi Siar TV (Dropzone Elegan) -->
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5 flex items-center justify-between">
                                <span>Upload File Video / Audio / Flyer Materi TV</span>
                                <span class="text-xs text-slate-400 font-normal">MP4, MOV, MP3, PNG, JPG, PDF</span>
                            </label>
                            <div class="p-3 bg-slate-50 border-2 border-dashed border-slate-300 rounded-xl text-center space-y-1.5 hover:border-kp-blue-400 transition cursor-pointer relative">
                                <input type="file" name="file_materi" id="file_materi" accept="video/*,audio/*,image/*,.pdf,.zip"
                                       class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                       onchange="handleTvFileMateriChange(this)">
                                <div class="flex items-center justify-center gap-2 text-kp-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span class="text-xs font-semibold" id="labelTvFileNama">Pilih berkas siar atau tarik ke sini</span>
                                </div>
                                <p class="text-xs text-slate-400">Format video MP4/MOV atau audio/gambar hingga 50MB</p>
                            </div>
                            @if(isset($transaksi) && $transaksi->file_materi)
                                <div class="mt-1.5 flex items-center gap-2 text-xs text-kp-blue-700 bg-kp-blue-50/70 px-2.5 py-1 rounded-lg border border-kp-blue-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    <span>File tersimpan:</span>
                                    <a href="{{ asset('storage/' . $transaksi->file_materi) }}" target="_blank" class="font-semibold underline">Lihat / Unduh File</a>
                                </div>
                            @endif
                        </div>

                        <!-- Baris: Produk / Jasa (Full Width) & Hidden no_spk -->
                        <div class="pt-1">
                            <label class="text-sm font-semibold text-slate-700 mb-1.5 block">
                                Produk / Jasa <span class="text-xs text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" name="produk_jasa" id="produk_jasa"
                                   placeholder="Jenis produk / jasa..."
                                   value="{{ old('produk_jasa', isset($transaksi) ? $transaksi->produk_jasa : '') }}"
                                   oninput="updateSimulatorLive()"
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-normal text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                            <input type="hidden" name="no_spk" id="no_spk" value="{{ old('no_spk', isset($transaksi) ? $transaksi->no_spk : '') }}">
                        </div>

                    </div>

                    <!-- ============================================================== -->
                    <!-- SEKSI 4: JADWAL TAYANG TV (KALENDER KORAN LENGKAP)             -->
                    <!-- ============================================================== -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">4</span>
                                <span>Jadwal Tayang / Siaran Priangan TV</span>
                            </span>
                        </div>

                        <!-- Dua Kolom: Kiri (Frekuensi & Chips Tanggal), Kanan (Kalender Kompak) -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-stretch">
                            
                            <!-- Kolom Kiri: Frekuensi Stepper & Chips Tanggal Terpilih -->
                            <div class="md:col-span-5 flex flex-col justify-between space-y-3">
                                
                                <!-- Stepper Jumlah Spot / Tayang -->
                                <div>
                                    <label class="text-xs font-medium text-slate-700 mb-1.5 block">
                                        Frekuensi Siaran / Spot <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center bg-white border border-slate-300 rounded-xl p-1 shadow-2xs flex-1">
                                            <button type="button" onclick="stepJumlahTayangTv(-1)"
                                                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition cursor-pointer select-none">
                                                &minus;
                                            </button>
                                            <input type="text" inputmode="numeric" id="in_qty" name="total_muatiklanpriangan" 
                                                   value="{{ old('total_muatiklanpriangan', isset($transaksi) ? $transaksi->total_muatiklanpriangan : '1') }}" min="1" required 
                                                   oninput="handleJumlahMuatTvChange(this.value)"
                                                   class="w-full h-8 px-1 text-center font-bold text-sm text-slate-900 border-0 focus:ring-0 focus:outline-none">
                                            <button type="button" onclick="stepJumlahTayangTv(1)"
                                                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition cursor-pointer select-none">
                                                &#43;
                                            </button>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-700 whitespace-nowrap">Spot / Kali</span>
                                    </div>
                                </div>

                                <!-- Box Daftar Tanggal Terpilih -->
                                <div class="flex-1 flex flex-col p-3 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-2">
                                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/60 whitespace-nowrap gap-1.5">
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <span class="text-sm font-semibold text-slate-700 whitespace-nowrap">Tanggal Siaran:</span>
                                            <span id="labelTotalDatesCount" class="px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-100 text-kp-blue-800 whitespace-nowrap">1 Tanggal</span>
                                        </div>
                                        <button type="button" onclick="resetSemuaTanggalTv()"
                                                class="p-1 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer flex items-center justify-center shrink-0"
                                                title="Reset semua tanggal terpilih">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                    <div id="daftarTanggalChips" class="space-y-1.5 overflow-y-auto max-h-[195px] pr-0.5">
                                        <!-- Rendered via JS -->
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Kalender Kompak Standar -->
                            <div class="md:col-span-7 flex flex-col justify-between space-y-2.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium text-slate-700">Pilih Tanggal:</span>
                                </div>

                                <!-- Container Kalender Kompak -->
                                <div class="p-3 bg-white border border-slate-200/90 rounded-xl space-y-2 shadow-2xs">
                                    <!-- Header Kalender: Bulan Tahun & Navigasi Panah Kompak -->
                                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                                        <div class="flex items-center gap-2">
                                            <span id="calendarMonthTitle" class="text-sm font-bold text-slate-900"></span>
                                            <button type="button" onclick="goToTodayCalendarTv()"
                                                    class="text-xs font-semibold text-kp-blue-600 hover:text-kp-blue-800 cursor-pointer underline">
                                                Hari Ini
                                            </button>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button type="button" onclick="changeCalendarMonthTv(-1)"
                                                    class="w-6 h-6 rounded hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer"
                                                    title="Bulan Sebelumnya">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                            </button>
                                            <button type="button" onclick="changeCalendarMonthTv(1)"
                                                    class="w-6 h-6 rounded hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer"
                                                    title="Bulan Berikutnya">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Nama Hari Kompak -->
                                    <div class="grid grid-cols-7 gap-1 text-center text-xs font-bold text-slate-500">
                                        <span class="text-rose-500">Min</span>
                                        <span>Sen</span>
                                        <span>Sel</span>
                                        <span>Rab</span>
                                        <span>Kam</span>
                                        <span>Jum</span>
                                        <span>Sab</span>
                                    </div>

                                    <!-- Grid Hari Kalender -->
                                    <div id="calendarDaysGrid" class="grid grid-cols-7 gap-1 text-center text-sm font-medium">
                                        <!-- Rendered via JS -->
                                    </div>

                                    <!-- Legenda Simpel Ringkas -->
                                    <div class="flex items-center justify-between text-xs text-slate-500 border-t border-slate-100 pt-2 px-0.5">
                                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-600 inline-block shadow-2xs"></span> Tanggal Siaran</span>
                                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-rose-50 border border-rose-300 inline-block"></span> Libur / Minggu</span>
                                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-amber-50 border border-amber-300 inline-block"></span> Peringatan</span>
                                    </div>
                                </div>

                                <!-- Panel Keterangan Hari -->
                                <div id="panelInfoTanggal" class="p-2.5 rounded-xl border transition-all text-xs bg-slate-50 border-slate-200 space-y-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span id="infoTanggalLabel" class="font-bold text-xs text-slate-900 whitespace-nowrap">
                                            Klik tanggal di kalender
                                        </span>
                                    </div>
                                    <div id="infoTanggalBadgeContainer" class="flex items-center gap-1.5 flex-wrap">
                                        <span id="infoTanggalBadge" class="text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-200 text-slate-700 whitespace-nowrap">
                                            Status Hari
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ============================================================== -->
                <!-- PANEL KANAN: PRATINJAU & KALKULASI TAGIHAN                     -->
                <!-- ============================================================== -->
                <div class="lg:col-span-5 space-y-5 lg:sticky lg:top-4">

                    <!-- SEKSI PRATINJAU RINGKASAN ORDER TV -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-3.5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span>Ringkasan Order Priangan TV</span>
                            </span>
                            <span class="px-2 py-0.5 rounded text-xs font-bold border bg-purple-100 text-purple-800 border-purple-300">
                                Broadcast TV
                            </span>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/80 space-y-2.5 text-sm">
                            <div class="flex">
                                <span class="w-28 font-medium text-slate-500 shrink-0">Klien</span>
                                <span class="w-2">:</span>
                                <span id="prev_klien" class="font-bold text-slate-900 truncate">Belum dipilih</span>
                            </div>
                            <div class="flex">
                                <span class="w-28 font-medium text-slate-500 shrink-0">Program / Paket</span>
                                <span class="w-2">:</span>
                                <span id="prev_jenis" class="font-semibold text-kp-blue-700 truncate">{{ $initialTvProduct?->nama_jenis ?? 'Belum ada produk dipilih' }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-28 font-medium text-slate-500 shrink-0">Skema</span>
                                <span class="w-2">:</span>
                                <span id="prev_tv_skema_badge" class="px-1.5 py-0.2 rounded text-xs font-bold {{ $initialTvProduct ? 'bg-sky-100 text-sky-800' : 'bg-slate-100 text-slate-600' }}">{{ $initialTvProduct ? '1 Kali Tayang' : 'Belum dipilih' }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-28 font-medium text-slate-500 shrink-0">Durasi / Jam</span>
                                <span class="w-2">:</span>
                                <span id="prev_durasi_jam" class="font-normal text-slate-700">30 Detik • 19:00 WIB</span>
                            </div>
                            <div class="flex">
                                <span class="w-28 font-medium text-slate-500 shrink-0">Tgl Tayang</span>
                                <span class="w-2">:</span>
                                <span id="prev_tgl" class="font-mono text-slate-700">{{ date('d M Y') }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-28 font-medium text-slate-500 shrink-0">Total Spot</span>
                                <span class="w-2">:</span>
                                <span id="prev_qty" class="font-semibold text-slate-900">1 Spot Tayang</span>
                            </div>

                            <div class="pt-2 border-t border-slate-200 space-y-1">
                                <span class="text-xs font-semibold text-slate-500">Saluran Penayangan:</span>
                                <div id="prev_tv_platform_tags" class="flex flex-wrap gap-1 mt-0.5">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-xs font-medium">Belum ada platform dipilih</span>
                                </div>
                            </div>

                            <div class="pt-1.5 border-t border-slate-200 text-xs text-slate-500 italic truncate" id="prev_materi">
                                Materi: Belum diisi
                            </div>
                        </div>
                    </div>

                    <!-- SEKSI RINCIAN BIAYA & PEMBAYARAN -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900">Rincian Biaya Transaksi</span>
                            <span id="badge_status_bayar" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                Menunggu Pembayaran
                            </span>
                        </div>

                        <!-- Baris Rincian Biaya Transparan Standar Akuntansi Pers -->
                        <div class="text-xs sm:text-sm">
                            <table class="w-full border-collapse" style="table-layout: fixed;">
                                <tbody>
                                    <!-- 1. Baris Tarif & Dimensi -->
                                    <tr>
                                        <td class="py-1 text-slate-600 font-medium whitespace-nowrap" style="width: 125px;">Tarif Satuan</td>
                                        <td class="py-1 text-center font-bold text-slate-400" style="width: 16px;">:</td>
                                        <td class="py-1 text-right font-mono font-normal text-slate-700">
                                            <div class="relative inline-flex items-center justify-end w-full max-w-[170px]">
                                                <span class="absolute left-2.5 text-xs text-slate-400 font-bold pointer-events-none select-none">Rp</span>
                                                <input type="text" id="in_harga" name="harga_transaksipriangan" 
                                                       value="{{ number_format(old('harga_transaksipriangan', isset($transaksi) ? $transaksi->harga_transaksipriangan : ($initialTvProduct?->tarif_dasar ?? 0)), 0, ',', '.') }}" 
                                                       required onkeyup="formatInput(this)" oninput="hitungSemua()"
                                                       style="padding-left: 2rem !important;"
                                                       class="w-full h-8 px-2 text-right bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs format-rupiah">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-1 text-slate-600 font-medium whitespace-nowrap">Format / Durasi</td>
                                        <td class="py-1 text-center font-bold text-slate-400">:</td>
                                        <td class="py-1 text-right font-normal text-slate-700 truncate" id="strukDimensi">-</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1 text-slate-600 font-medium whitespace-nowrap">Frekuensi</td>
                                        <td class="py-1 text-center font-bold text-slate-400">:</td>
                                        <td class="py-1 text-right font-normal text-slate-700" id="strukFrekuensi">1 Spot Tayang</td>
                                    </tr>
                                    <tr class="border-t border-slate-200">
                                        <td class="pt-2 pb-1 text-slate-800 font-bold whitespace-nowrap">Brutto</td>
                                        <td class="pt-2 pb-1 text-center font-bold text-slate-400">:</td>
                                        <td class="pt-2 pb-1 text-right font-mono font-bold text-slate-900" id="lbl_brutto">Rp 0</td>
                                    </tr>

                                    <!-- 2. Section Input Diskon (Colspan 3) -->
                                    <tr class="border-t border-slate-200">
                                        <td colspan="3" class="pt-2.5 pb-2">
                                            <div class="space-y-1.5">
                                                <div class="flex items-center justify-between">
                                                    <label class="text-xs font-semibold text-slate-700">Diskon Transaksi</label>
                                                    <span class="text-[11px] text-slate-400 font-normal">Isi % atau nominal Rp</span>
                                                </div>
                                                <div class="grid grid-cols-5 gap-2">
                                                    <div class="col-span-2 relative">
                                                        <input type="number" name="diskon_persen" id="diskon_persen" 
                                                               value="{{ old('diskon_persen', isset($transaksi) && $transaksi->harga_transaksipriangan > 0 && $transaksi->diskon_transaksipriangan > 0 ? round(($transaksi->diskon_transaksipriangan / ($transaksi->harga_transaksipriangan * ($transaksi->total_muatiklanpriangan ?: 1))) * 100, 1) : 0) }}" 
                                                               step="0.5" min="0" max="100"
                                                               placeholder="0"
                                                               oninput="handleDiskonPersenChange(this.value)"
                                                               class="w-full h-10 px-2.5 pr-6 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                                        <span class="absolute right-2 top-2.5 text-xs text-slate-400 font-bold pointer-events-none">%</span>
                                                    </div>
                                                    <div class="col-span-3 relative">
                                                        <span class="absolute left-2.5 top-2.5 text-xs text-slate-400 font-bold pointer-events-none">Rp</span>
                                                        <input type="text" id="in_diskon" name="diskon_transaksipriangan" 
                                                               value="{{ number_format(old('diskon_transaksipriangan', isset($transaksi) ? $transaksi->diskon_transaksipriangan : 0), 0, ',', '.') }}" 
                                                               placeholder="0"
                                                               onkeyup="handleDiskonNominalInput(this)"
                                                               style="padding-left: 2.25rem !important;"
                                                               class="w-full h-10 pr-2.5 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs format-rupiah">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- 3. Netto (DPP), PPN -->
                                    <tr class="border-t border-slate-200">
                                        <td class="pt-2 pb-1 text-slate-800 font-semibold whitespace-nowrap">Netto (DPP)</td>
                                        <td class="pt-2 pb-1 text-center font-bold text-slate-400">:</td>
                                        <td class="pt-2 pb-1 text-right font-mono font-semibold text-slate-800" id="lbl_netto">Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1 text-slate-600 font-medium whitespace-nowrap">PPN ({{ $ppnPersen ?? 12 }}%)</td>
                                        <td class="py-1 text-center font-bold text-slate-400">:</td>
                                        <td class="py-1 text-right font-mono font-normal text-slate-700" id="lbl_ppn">Rp 0</td>
                                    </tr>

                                    <!-- 4. TOTAL TAGIHAN -->
                                    <tr class="border-t-2 border-slate-800">
                                        <td class="py-2.5 text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide whitespace-nowrap">Total Tagihan</td>
                                        <td class="py-2.5 text-center font-bold text-slate-400">:</td>
                                        <td class="py-2.5 text-right font-mono text-base sm:text-lg font-extrabold text-kp-blue-700" id="lbl_total_text">Rp 0</td>
                                    </tr>

                                    <!-- 5. Input Nominal Bayar (Colspan 3) -->
                                    <tr class="border-t border-slate-100">
                                        <td colspan="3" class="pt-3 pb-2">
                                            <div class="space-y-1.5">
                                                <div class="flex items-center justify-between">
                                                    <label class="text-xs font-semibold text-slate-700">Nominal Bayar</label>
                                                    <button type="button" onclick="setBayarLunasPriangan()" class="text-xs font-semibold text-kp-blue-600 hover:underline cursor-pointer">
                                                        Bayar Lunas
                                                    </button>
                                                </div>
                                                <input type="text" inputmode="numeric" name="jumlahbayar_transaksipriangan" id="in_bayar" 
                                                       value="{{ old('jumlahbayar_transaksipriangan') ? number_format((int) str_replace(['.', ','], '', old('jumlahbayar_transaksipriangan')), 0, ',', '.') : (isset($transaksi) && $transaksi->jumlahbayar_transaksipriangan ? number_format($transaksi->jumlahbayar_transaksipriangan, 0, ',', '.') : '0') }}"
                                                       oninput="hitungSemua()"
                                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition format-rupiah">
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- 6. Sisa Tagihan -->
                                    <tr class="border-t border-slate-100">
                                        <td class="pt-2 pb-1 text-slate-600 font-semibold whitespace-nowrap">Sisa Tagihan</td>
                                        <td class="pt-2 pb-1 text-center font-bold text-slate-400">:</td>
                                        <td class="pt-2 pb-1 text-right font-mono font-bold text-slate-900" id="lbl_piutang">Rp 0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Tombol Aksi Submit Transaksi -->
                        <div class="pt-4 border-t border-slate-100 space-y-2">
                            <button type="button" onclick="konfirmasiSimpanTransaksi()" id="btnSubmitOrder"
                                    class="w-full h-12 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer tracking-wide">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                <span>{{ isset($transaksi) ? 'Simpan Perubahan Order TV' : 'Simpan Order Priangan TV' }}</span>
                            </button>
                            <a href="{{ route('transaksipriangan.index') }}" 
                                class="w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-xl transition flex items-center justify-center text-center">
                                Batal
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </form>

        <!-- ============================================================== -->
        <!-- SHARED MODALS: TAMBAH PELANGGAN & SALES (STANDAR 3 MEDIA)       -->
        <!-- ============================================================== -->
        @include('pages.iklan.transaksi.partials.modal_pelanggan_baru')
        @include('pages.iklan.transaksi.partials.modal_sales_ae_baru')

        <!-- ============================================================== -->
        <!-- MODAL KONFIRMASI SIMPAN TRANSAKSI TV                           -->
        <!-- ============================================================== -->
        <div id="modalKonfirmasiSimpan" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all animate-in fade-in zoom-in-95 duration-150">
                <div class="p-6 text-center space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center mx-auto shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            {{ isset($transaksi) ? 'Konfirmasi Perubahan Order TV' : 'Konfirmasi Simpan Order TV' }}
                        </h3>
                        <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">
                            Pastikan data pemesan, program acara TV, placement, dan jadwal siaran telah sesuai sebelum menyimpan transaksi ini.
                        </p>
                    </div>

                    <!-- Ringkasan Singkat -->
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/80 text-left text-sm space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nama Pemesan:</span>
                            <span id="konfirmasiNamaPemesan" class="font-bold text-slate-900 truncate max-w-[200px]">-</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Program / Format:</span>
                            <span id="konfirmasiProdukNama" class="font-semibold text-slate-800 truncate max-w-[200px]">-</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Jadwal Siar:</span>
                            <span id="konfirmasiJadwalTayang" class="font-medium text-slate-800">-</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-200/80 pt-2">
                            <span class="font-medium text-slate-700">Total Tagihan:</span>
                            <span id="konfirmasiTotalBiaya" class="font-mono font-extrabold text-base text-kp-blue-700">-</span>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="grid grid-cols-2 gap-2.5 pt-2">
                        <button type="button" onclick="closeModalKonfirmasiSimpan()"
                                class="w-full h-11 px-4 bg-white hover:bg-slate-100 active:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition shadow-2xs cursor-pointer flex items-center justify-center">
                            Periksa Kembali
                        </button>
                        <button type="button" onclick="eksekusiSubmitTransaksi()" id="btnSubmitFinal"
                                class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span>{{ isset($transaksi) ? 'Ya, Perbarui' : 'Ya, Simpan' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ============================================================== -->
    <!-- JAVASCRIPT ENGINE LENGKAP TRANSAKSI PRIANGAN TV                 -->
    <!-- ============================================================== -->
    @push('scripts')
    <script>
        // State Pelanggan List
        window.currentPelangganList = {!! json_encode($pelanggans->map(function($p) {
            return [
                'id' => $p->id,
                'kode_pelanggan' => $p->kode_pelanggan,
                'nama_instansi' => $p->nama_instansi,
                'jenis_klien' => $p->jenis_klien,
                'npwp_nik' => $p->npwp_nik,
                'email' => $p->email,
                'no_telp' => $p->no_telp,
                'alamat' => $p->alamat,
                'kontak_pic' => $p->kontak_pic,
                'total_piutang' => $p->total_piutang ?? 0,
            ];
        })) !!};

        let searchTimeout = null;
        const configPpnPersen = {{ $ppnPersen ?? 12 }};

        // State Kalender Nasional & Tanggal Terpilih TV
        let selectedDates = {!! json_encode(array_values(array_filter(explode(',', old('tanggal_muat_list', isset($transaksi) ? ($transaksi->tanggal_muat_list ?: ($transaksi->tanggal_muatiklanpriangan?->format('Y-m-d') ?: date('Y-m-d'))) : date('Y-m-d')))))) !!};
        if (!Array.isArray(selectedDates) || selectedDates.length === 0) {
            selectedDates = ["{{ date('Y-m-d') }}"];
        }

        let currentCalYear = new Date().getFullYear();
        let currentCalMonth = new Date().getMonth();
        let activeDetailDate = selectedDates[0] || "{{ date('Y-m-d') }}";
        const kalenderDataCache = {
            [`${currentCalYear}-${currentCalMonth + 1}`]: @json($kalenderAwal ?? ['libur' => [], 'peringatan' => []])
        };
        let isKalenderLoading = false;

        const monthNamesIndo = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // --- SKEMA PEMBAYARAN: TUNAI vs KREDIT ---
        function setJenisPembayaran(jenis) {
            document.getElementById('jenis_pembayaran').value = jenis;

            const cardTunai = document.getElementById('cardBayarTunai');
            const cardKredit = document.getElementById('cardBayarKredit');
            const iconTunai = document.getElementById('iconBoxTunai');
            const iconKredit = document.getElementById('iconBoxKredit');
            const dotTunai = document.getElementById('dotBayarTunai');
            const dotKredit = document.getElementById('dotBayarKredit');

            if (jenis === 'Tunai') {
                cardTunai.className = 'px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-emerald-50/70 border-emerald-500 text-emerald-950 font-semibold shadow-2xs';
                cardKredit.className = 'px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-white border-slate-200 text-slate-600 hover:border-slate-300 font-medium';
                iconTunai.className = 'w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0';
                iconKredit.className = 'w-6 h-6 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0';
                dotTunai.className = 'text-emerald-600 flex items-center';
                dotKredit.className = 'text-transparent flex items-center';
            } else {
                cardKredit.className = 'px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-amber-50/70 border-amber-500 text-amber-950 font-semibold shadow-2xs';
                cardTunai.className = 'px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-white border-slate-200 text-slate-600 hover:border-slate-300 font-medium';
                iconKredit.className = 'w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0';
                iconTunai.className = 'w-6 h-6 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center shrink-0';
                dotKredit.className = 'text-amber-600 flex items-center';
                dotTunai.className = 'text-transparent flex items-center';
            }
            hitungSemua();
        }

        // --- TYPEAHEAD SEARCH PELANGGAN ---
        function handleSearchPelangganFocus(forceOpen = false) {
            const input = document.getElementById('inputSearchPelanggan');
            renderDropdownPelanggan(input.value.trim());
            document.getElementById('dropdownHasilPelanggan').classList.remove('hidden');
        }

        function handleSearchPelangganInput(val) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                renderDropdownPelanggan(val.trim());
                document.getElementById('dropdownHasilPelanggan').classList.remove('hidden');
            }, 100);
        }

        function handleSearchPelangganKeydown(e) {
            if (e.key === 'Escape') {
                document.getElementById('dropdownHasilPelanggan').classList.add('hidden');
            }
        }

        function renderDropdownPelanggan(keyword) {
            const listCont = document.getElementById('listPelangganItem');
            const q = (keyword || '').toLowerCase();

            let matched = window.currentPelangganList;
            if (q.length > 0) {
                matched = window.currentPelangganList.filter(p => {
                    return (p.nama_instansi && p.nama_instansi.toLowerCase().includes(q)) ||
                           (p.kode_pelanggan && p.kode_pelanggan.toLowerCase().includes(q)) ||
                           (p.no_telp && p.no_telp.includes(q));
                });
            }

            if (matched.length === 0) {
                listCont.innerHTML = `
                    <div class="p-3 text-center text-xs text-slate-400">
                        Tidak ada pelanggan yang cocok.
                        ${keyword ? `
                            <div class="mt-2">
                                <button type="button" onclick="pilihSebagaiManual('${keyword.replace(/'/g, "\\'")}')" class="px-2.5 py-1 bg-kp-blue-50 text-kp-blue-700 hover:bg-kp-blue-100 rounded-lg font-semibold text-xs cursor-pointer">
                                    Gunakan "${keyword}" sebagai Pemesan Manual
                                </button>
                            </div>
                        ` : ''}
                    </div>
                `;
                return;
            }

            let html = '';
            matched.slice(0, 10).forEach(p => {
                html += `
                    <div onclick="selectPelangganItemById(${p.id})"
                         class="p-2.5 hover:bg-slate-50 rounded-xl cursor-pointer transition flex items-center justify-between gap-3 text-xs">
                         <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-semibold text-slate-900">${p.nama_instansi}</span>
                                <span class="px-1.5 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 uppercase">${p.jenis_klien || 'Umum'}</span>
                                <span class="text-xs font-mono text-slate-400">[${p.kode_pelanggan || 'PLG'}]</span>
                            </div>
                            <div class="text-xs text-slate-500 truncate mt-0.5">
                                ${p.no_telp || '-'} ${p.alamat ? '• ' + p.alamat : ''}
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-600 shrink-0">Pilih &rarr;</span>
                    </div>
                `;
            });
            listCont.innerHTML = html;
        }

        function selectPelangganItemById(id) {
            const p = window.currentPelangganList.find(x => x.id == id);
            if (!p) return;

            document.getElementById('pelanggan_id').value = p.id;
            document.getElementById('nama_pemasangpriangan').value = p.nama_instansi;
            document.getElementById('inputManualNamaPemasang').value = p.nama_instansi;

            if (p.no_telp) document.getElementById('no_telp').value = p.no_telp;
            if (p.alamat) document.getElementById('alamat_pemasangpriangan').value = p.alamat;
            if (p.kontak_pic) document.getElementById('nama_pic').value = p.kontak_pic;

            // Update Card Pelanggan Terpilih
            document.getElementById('labelNamaPelanggan').textContent = p.nama_instansi;
            document.getElementById('badgeJenisPelanggan').textContent = p.jenis_klien || 'Umum';
            document.getElementById('labelKodePelanggan').textContent = `[${p.kode_pelanggan || 'PLG'}]`;

            const telpEl = document.getElementById('labelTelpPelanggan');
            const textTelp = document.getElementById('textTelpPelanggan');
            if (telpEl && textTelp) {
                if (p.no_telp) {
                    textTelp.textContent = p.no_telp;
                    telpEl.classList.remove('hidden');
                } else {
                    telpEl.classList.add('hidden');
                }
            }

            const almtEl = document.getElementById('labelAlamatPelanggan');
            const textAlmt = document.getElementById('textAlamatPelanggan');
            if (almtEl && textAlmt) {
                if (p.alamat) {
                    textAlmt.textContent = p.alamat;
                    almtEl.classList.remove('hidden');
                } else {
                    almtEl.classList.add('hidden');
                }
            }

            document.getElementById('cardPelangganTerpilih').classList.remove('hidden');
            document.getElementById('containerSearchPelanggan').classList.add('hidden');
            document.getElementById('dropdownHasilPelanggan').classList.add('hidden');

            updateSimulatorLive();
        }

        function resetPilihanPelanggan() {
            document.getElementById('pelanggan_id').value = '';
            document.getElementById('cardPelangganTerpilih').classList.add('hidden');
            document.getElementById('containerSearchPelanggan').classList.remove('hidden');

            const hiddenNama = document.getElementById('nama_pemasangpriangan');
            if (hiddenNama) hiddenNama.value = '';
            const manualInput = document.getElementById('inputManualNamaPemasang');
            if (manualInput) manualInput.value = '';
            const telpInput = document.getElementById('no_telp');
            if (telpInput) telpInput.value = '';
            const alamatInput = document.getElementById('alamat_pemasangpriangan');
            if (alamatInput) alamatInput.value = '';
            const picInput = document.getElementById('nama_pic');
            if (picInput) picInput.value = '';

            const searchInput = document.getElementById('inputSearchPelanggan');
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
                handleSearchPelangganFocus(true);
            }
            updateSimulatorLive();
        }

        function pilihSebagaiManual(nama) {
            document.getElementById('pelanggan_id').value = '';
            document.getElementById('nama_pemasangpriangan').value = nama;
            document.getElementById('inputManualNamaPemasang').value = nama;
            document.getElementById('dropdownHasilPelanggan').classList.add('hidden');
            updateSimulatorLive();
        }

        function handleManualNamaInput(val) {
            document.getElementById('nama_pemasangpriangan').value = val;
            document.getElementById('pelanggan_id').value = '';
            updateSimulatorLive();

            const cardSelected = document.getElementById('cardPelangganTerpilih');
            const contSearch = document.getElementById('containerSearchPelanggan');
            if (cardSelected && !cardSelected.classList.contains('hidden') && val.trim().length > 0) {
                cardSelected.classList.add('hidden');
                if (contSearch) contSearch.classList.remove('hidden');
            }
        }

        // --- TAB PRIANGAN TV SWITCHING (SELARAS FOTO 1 & TRANS KORAN) ---
        function switchTvTab(tabKey) {
            const hiddenKategori = document.getElementById('kategori_tv_form');
            if (hiddenKategori) hiddenKategori.value = tabKey;

            // Update tab button UI
            document.querySelectorAll('.tab-tv-btn').forEach(btn => {
                btn.className = "tab-tv-btn px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 bg-white text-slate-700 font-medium border border-slate-200 hover:border-slate-300 hover:bg-slate-50";
                const badge = btn.querySelector('span:nth-child(2)');
                if (badge) badge.className = "px-1.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600";
            });
            const activeBtn = document.getElementById('tabTvBtn_' + tabKey);
            if (activeBtn) {
                activeBtn.className = "tab-tv-btn px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 bg-kp-blue-600 text-white font-semibold shadow-xs border border-kp-blue-600";
                const badge = activeBtn.querySelector('span:nth-child(2)');
                if (badge) badge.className = "px-1.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white";
            }

            // Switch content panels (tanpa mereset pilihan yang sudah terseleksi di tab lain)
            document.querySelectorAll('.tv-tab-content').forEach(panel => {
                panel.classList.add('hidden');
            });
            const activePanel = document.getElementById('tvTabContent_' + tabKey);
            if (activePanel) {
                activePanel.classList.remove('hidden');
            }

            updateSimulatorLive();
        }

        // --- MULTI-PRODUCT STATE ENGINE PRIANGAN TV ---
        let selectedTvProducts = {};

        function toggleProdukTv(id, kode, nama, tarif, tabKey) {
            const targetCard = document.getElementById('cardTvProduk_' + id);
            const badge = targetCard ? targetCard.querySelector('.badge-corner-check') : null;

            if (selectedTvProducts[id]) {
                delete selectedTvProducts[id];
                if (targetCard) {
                    targetCard.className = "tv-produk-card text-left p-3.5 rounded-xl border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50 transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2.5 min-h-[92px]";
                }
                if (badge) badge.classList.add('hidden');
            } else {
                selectedTvProducts[id] = { id: id, kode: kode, nama: nama, tarif: Number(tarif), tabKey: tabKey };
                if (targetCard) {
                    targetCard.className = "tv-produk-card text-left p-3.5 rounded-xl border-2 border-kp-blue-600 bg-sky-50/20 ring-1 ring-kp-blue-500/20 shadow-xs transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2.5 min-h-[92px]";
                }
                if (badge) badge.classList.remove('hidden');
            }

            recalculateTvSelection();
        }

        function recalculateTvSelection() {
            const items = Object.values(selectedTvProducts);
            let totalTarif = 0;
            items.forEach(item => {
                totalTarif += Number(item.tarif);
            });

            // Update input harga
            const inputHarga = document.getElementById('in_harga');
            if (inputHarga) {
                inputHarga.value = formatRupiah(totalTarif);
            }

            // Update hidden container produk IDs
            const contProd = document.getElementById('containerTvSelectedProductIds');
            if (contProd) {
                contProd.innerHTML = '';
                items.forEach(item => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'selected_produk_ids[]';
                    inp.value = item.id;
                    contProd.appendChild(inp);
                });
            }

            const idInput = document.getElementById('id_iklanpriangan');
            if (idInput) {
                idInput.value = items.length > 0 ? items[0].id : '';
            }

            // Update platforms
            const contPlat = document.getElementById('containerTvPlatformsPublikasi');
            if (contPlat) {
                contPlat.innerHTML = '';
                const activePlatforms = new Set();
                items.forEach(item => {
                    const mapPlat = {
                        'PODCAST':      'Studio Podcast TV',
                        'INSTAGRAM':    'Instagram Priangan TV',
                        'TIKTOK':       'TikTok Priangan TV',
                        'BANNER_WEB':   'Website Priangan TV',
                        'VIDEO_LIPSUS': 'Liputan Khusus TV'
                    };
                    activePlatforms.add(mapPlat[item.tabKey] || item.tabKey);
                });

                activePlatforms.forEach(plat => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'platforms_publikasi[]';
                    inp.value = plat;
                    contPlat.appendChild(inp);
                });
            }

            // Text summary
            const parts = items.map(item => item.nama);
            const summaryText = parts.length > 0 ? parts.join(' + ') : 'Belum ada produk dipilih';

            const inProdukJasa = document.getElementById('in_tv_produk_jasa');
            if (inProdukJasa) inProdukJasa.value = summaryText;

            const prevJenis = document.getElementById('prev_jenis');
            if (prevJenis) prevJenis.innerText = summaryText;

            const elDimensi = document.getElementById('strukDimensi');
            if (elDimensi) {
                elDimensi.innerText = summaryText !== 'Belum ada produk dipilih' ? summaryText : '-';
                elDimensi.title = summaryText;
            }

            // Update status skema badge
            const badgeSkema = document.getElementById('badge_tv_skema');
            if (badgeSkema) {
                if (items.length > 1) {
                    badgeSkema.innerText = `Multi-Program (${items.length} Format)`;
                    badgeSkema.className = "px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300";
                } else if (items.length === 1) {
                    badgeSkema.innerText = "1 Kali Tayang / Spot";
                    badgeSkema.className = "px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-100 text-kp-blue-800 border border-kp-blue-200";
                } else {
                    badgeSkema.innerText = "Belum Ada Format Dipilih";
                    badgeSkema.className = "px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200";
                }
            }

            hitungSemua();
            updateSimulatorLive();
        }

        function handleTvPlacementChange() {
            const checkedPlacements = Array.from(document.querySelectorAll('.tv-placement-cb:checked')).map(cb => cb.value);

            document.querySelectorAll('.tv-placement-card').forEach(card => {
                const cb = card.querySelector('.tv-placement-cb');
                const badge = card.querySelector('.badge-corner-check');
                if (cb && cb.checked) {
                    card.className = "tv-placement-card relative overflow-visible flex items-center justify-between p-2.5 rounded-xl border-2 border-kp-blue-600 bg-sky-50 text-slate-900 font-semibold ring-1 ring-kp-blue-500/20 shadow-2xs transition cursor-pointer select-none";
                    if (badge) badge.classList.remove('hidden');
                } else {
                    card.className = "tv-placement-card relative overflow-visible flex items-center justify-between p-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 transition cursor-pointer select-none";
                    if (badge) badge.classList.add('hidden');
                }
            });


            const badgeSkema = document.getElementById('badge_tv_skema');
            const prevSkemaBadge = document.getElementById('prev_tv_skema_badge');
            const skemaInput = document.getElementById('skema_pembayaran');

            if (checkedPlacements.length > 1) {
                if (badgeSkema) {
                    badgeSkema.innerText = `Paket Medsos TV (${checkedPlacements.length} Platform)`;
                    badgeSkema.className = "px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300";
                }
                if (prevSkemaBadge) {
                    prevSkemaBadge.innerText = `Paket (${checkedPlacements.length} Platform)`;
                    prevSkemaBadge.className = "px-1.5 py-0.2 rounded text-xs font-bold bg-emerald-100 text-emerald-800";
                }
                if (skemaInput) skemaInput.value = 'paket';
            } else {
                if (badgeSkema) {
                    badgeSkema.innerText = "1 Kali Tayang / Spot";
                    badgeSkema.className = "px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-100 text-kp-blue-800 border border-kp-blue-200";
                }
                if (prevSkemaBadge) {
                    prevSkemaBadge.innerText = "1 Kali Tayang";
                    prevSkemaBadge.className = "px-1.5 py-0.2 rounded text-xs font-bold bg-sky-100 text-sky-800";
                }
                if (skemaInput) skemaInput.value = 'sekali_tayang';
            }

            updateSimulatorLive();
        }

        function handleTvFileMateriChange(input) {
            const label = document.getElementById('labelTvFileNama');
            if (input.files && input.files[0]) {
                label.textContent = input.files[0].name;
            } else {
                label.textContent = 'Pilih berkas siar atau tarik ke sini';
            }
        }

        // --- KALENDER ENGINE DARI TRANSAKSI KORAN UNTUK PRIANGAN TV ---
        function goToTodayCalendarTv() {
            const today = new Date();
            currentCalYear = today.getFullYear();
            currentCalMonth = today.getMonth();
            fetchKalenderBulanTv(currentCalYear, currentCalMonth + 1, () => {
                renderCalendarTv();
            });
        }

        function changeCalendarMonthTv(delta) {
            currentCalMonth += delta;
            if (currentCalMonth < 0) {
                currentCalMonth = 11;
                currentCalYear -= 1;
            } else if (currentCalMonth > 11) {
                currentCalMonth = 0;
                currentCalYear += 1;
            }

            fetchKalenderBulanTv(currentCalYear, currentCalMonth + 1, () => {
                renderCalendarTv();
            });
        }

        function fetchKalenderBulanTv(year, month, callback) {
            const cacheKey = `${year}-${month}`;
            if (kalenderDataCache[cacheKey]) {
                if (callback) callback();
                return;
            }

            isKalenderLoading = true;
            const grid = document.getElementById('calendarDaysGrid');
            if (grid) grid.classList.add('opacity-40', 'pointer-events-none');

            fetch(`/api/kalender-nasional?year=${year}&month=${month}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => {
                if (!res.ok) throw new Error('Gagal kalender');
                return res.json();
            })
            .then(data => {
                kalenderDataCache[cacheKey] = {
                    libur: data.libur || {},
                    peringatan: data.peringatan || {}
                };
            })
            .catch(() => {
                kalenderDataCache[cacheKey] = { libur: {}, peringatan: {} };
            })
            .finally(() => {
                isKalenderLoading = false;
                if (grid) grid.classList.remove('opacity-40', 'pointer-events-none');
                if (callback) callback();
            });
        }

        function extractEventText(entry) {
            if (!entry) return null;
            if (typeof entry === 'string') return entry;
            if (Array.isArray(entry) && entry.length > 0) {
                const first = entry[0];
                return (typeof first === 'object') ? (first.nama || first.keterangan || first.summary || String(first)) : String(first);
            }
            if (typeof entry === 'object') {
                return entry.nama || entry.keterangan || entry.summary || null;
            }
            return null;
        }

        function formatIndoDate(dateStr, includeDayName = true) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            const y = parseInt(parts[0]);
            const m = parseInt(parts[1]) - 1;
            const d = parseInt(parts[2]);
            const dateObj = new Date(y, m, d);
            const dayName = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][dateObj.getDay()];
            const monthName = monthNamesIndo[m] || '';
            return includeDayName ? `${dayName}, ${d} ${monthName} ${y}` : `${d} ${monthName} ${y}`;
        }

        function formatIndoChip(dateStr) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            const y = parseInt(parts[0]);
            const m = parseInt(parts[1]) - 1;
            const d = parseInt(parts[2]);
            const dateObj = new Date(y, m, d);
            const shortDays = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            const shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${shortDays[dateObj.getDay()]}, ${d} ${shortMonths[m]} ${y}`;
        }

        function renderCalendarTv() {
            const monthTitleEl = document.getElementById('calendarMonthTitle');
            if (monthTitleEl) {
                monthTitleEl.textContent = `${monthNamesIndo[currentCalMonth]} ${currentCalYear}`;
            }

            const grid = document.getElementById('calendarDaysGrid');
            if (!grid) return;
            grid.innerHTML = '';

            const cacheKey = `${currentCalYear}-${currentCalMonth + 1}`;
            const monthData = kalenderDataCache[cacheKey] || { libur: {}, peringatan: {} };
            const liburMap = monthData.libur || {};
            const peringatanMap = monthData.peringatan || {};

            const firstDayOfWeek = new Date(currentCalYear, currentCalMonth, 1).getDay();
            const daysInMonth = new Date(currentCalYear, currentCalMonth + 1, 0).getDate();

            for (let i = 0; i < firstDayOfWeek; i++) {
                const empty = document.createElement('div');
                empty.className = 'h-7.5 sm:h-8 pointer-events-none';
                grid.appendChild(empty);
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            for (let day = 1; day <= daysInMonth; day++) {
                const dateObj = new Date(currentCalYear, currentCalMonth, day);
                dateObj.setHours(0, 0, 0, 0);
                const dateStr = `${currentCalYear}-${String(currentCalMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                
                const isSelected = selectedDates.includes(dateStr);
                const isPast = dateObj < today;
                const isToday = (dateObj.getTime() === today.getTime());
                const isSunday = (dateObj.getDay() === 0);
                const isActiveDetail = (activeDetailDate === dateStr);

                const liburDesc = extractEventText(liburMap[dateStr]);
                const peringatanDesc = extractEventText(peringatanMap[dateStr]);

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.setAttribute('data-date', dateStr);

                let btnClass = "h-7.5 sm:h-8 rounded-md p-0.5 transition cursor-pointer flex flex-col items-center justify-center relative select-none text-xs border ";

                if (isSelected) {
                    btnClass += "bg-kp-blue-600 text-white font-bold border-kp-blue-700 shadow-2xs ";
                } else if (isPast) {
                    btnClass += "text-slate-300 border-transparent cursor-not-allowed hover:bg-transparent ";
                } else if (liburDesc) {
                    btnClass += "text-rose-700 font-bold bg-rose-50 border-rose-200 hover:bg-rose-100 ";
                } else if (peringatanDesc) {
                    btnClass += "text-amber-800 font-bold bg-amber-50 border-amber-200 hover:bg-amber-100 ";
                } else if (isSunday) {
                    btnClass += "text-rose-600 font-semibold bg-rose-50/70 border-rose-200/80 hover:bg-rose-100 ";
                } else {
                    btnClass += "text-slate-700 font-medium border-transparent hover:bg-slate-100 ";
                }

                if (isActiveDetail && !isSelected) btnClass += "ring-2 ring-kp-blue-500/80 ";
                if (isToday && !isSelected) btnClass += "ring-1 ring-slate-400 font-bold ";

                btn.className = btnClass;
                btn.innerHTML = `<span class="leading-none text-xs font-semibold ${isSunday && !isSelected && !liburDesc ? 'text-rose-600' : ''}">${day}</span>`;
                btn.onclick = () => onCalendarCellClickTv(dateStr, isPast);
                grid.appendChild(btn);
            }
        }

        function onCalendarCellClickTv(dateStr, isPast) {
            activeDetailDate = dateStr;
            updatePanelInfoTanggalTv(dateStr);

            if (isPast) {
                renderCalendarTv();
                return;
            }

            const totalMuatInput = document.getElementById('in_qty');
            const targetQuota = parseInt(totalMuatInput ? totalMuatInput.value : 1) || 1;
            const index = selectedDates.indexOf(dateStr);

            if (index > -1) {
                selectedDates.splice(index, 1);
                selectedDates.sort();
                syncTanggalMuatTv();
                updatePanelInfoTanggalTv(dateStr);
            } else {
                if (selectedDates.length >= targetQuota) {
                    alert(`Seluruh ${targetQuota} tanggal siaran telah terisi. Tambah angka pada frekuensi tayang jika ingin memilih tanggal lagi.`);
                    renderCalendarTv();
                    return;
                }
                selectedDates.push(dateStr);
                selectedDates.sort();
                syncTanggalMuatTv();
                updatePanelInfoTanggalTv(dateStr);
            }
        }

        function updatePanelInfoTanggalTv(dateStr) {
            if (!dateStr) return;
            const panel = document.getElementById('panelInfoTanggal');
            const labelTanggal = document.getElementById('infoTanggalLabel');
            const badgeContainer = document.getElementById('infoTanggalBadgeContainer');
            if (!panel || !labelTanggal || !badgeContainer) return;

            labelTanggal.textContent = formatIndoDate(dateStr, true);

            const parts = dateStr.split('-');
            const year = parseInt(parts[0]);
            const month = parseInt(parts[1]);
            const cacheKey = `${year}-${month}`;
            const monthData = kalenderDataCache[cacheKey] || { libur: {}, peringatan: {} };

            const liburDesc = extractEventText(monthData.libur[dateStr]);
            const peringatanDesc = extractEventText(monthData.peringatan[dateStr]);

            const dateObj = new Date(year, month - 1, parseInt(parts[2]));
            const isSunday = (dateObj.getDay() === 0);
            const isSelectedThisDate = selectedDates.includes(dateStr);

            let selectedTagHtml = isSelectedThisDate ? `
                <span class="inline-flex items-center text-xs font-bold px-2 py-0.5 rounded-md bg-kp-blue-600 text-white shadow-2xs">
                    Terjadwal Siar TV
                </span>` : '';

            let badgesHtml = '';
            if (liburDesc && peringatanDesc) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-rose-50/80 border-rose-200 text-rose-900 space-y-1.5";
                badgesHtml = `<span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-rose-200 text-rose-700 shadow-2xs">Libur: ${liburDesc}</span> <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-amber-200 text-amber-800 shadow-2xs">Peringatan: ${peringatanDesc}</span> ${selectedTagHtml}`;
            } else if (liburDesc) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-rose-50/80 border-rose-200 text-rose-900 space-y-1.5";
                badgesHtml = `<span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-rose-200 text-rose-700 shadow-2xs">Libur: ${liburDesc}</span> ${selectedTagHtml}`;
            } else if (peringatanDesc) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-amber-50/80 border-amber-200 text-amber-900 space-y-1.5";
                badgesHtml = `<span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-amber-200 text-amber-800 shadow-2xs">Peringatan: ${peringatanDesc}</span> ${selectedTagHtml}`;
            } else if (isSunday) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-rose-50/70 border-rose-200 text-rose-900 space-y-1.5";
                badgesHtml = `<span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-rose-200 text-rose-700 shadow-2xs">Akhir Pekan (Minggu)</span> ${selectedTagHtml}`;
            } else {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-sky-50/60 border-sky-200 text-sky-900 space-y-1.5";
                badgesHtml = `<span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-sky-200 text-sky-800 shadow-2xs">Hari Siaran Reguler</span> ${selectedTagHtml}`;
            }

            badgeContainer.innerHTML = badgesHtml;
        }

        function renderSelectedDatesChipsTv() {
            const container = document.getElementById('daftarTanggalChips');
            if (!container) return;
            container.innerHTML = '';

            const totalMuatInput = document.getElementById('in_qty');
            const targetQuota = parseInt(totalMuatInput ? totalMuatInput.value : 1) || 1;

            if (selectedDates.length === 0) {
                container.innerHTML = `
                    <div class="p-3 text-center rounded-xl border border-dashed border-slate-300 bg-white text-xs text-slate-400">
                        Belum ada tanggal dipilih. Klik tanggal di kalender.
                    </div>
                `;
            } else {
                selectedDates.forEach((d, i) => {
                    const chip = document.createElement('div');
                    const isActive = (activeDetailDate === d);
                    chip.className = `w-full flex items-center justify-between pl-3 pr-2 py-1.5 rounded-xl transition shadow-2xs group overflow-hidden ${
                        isActive ? 'bg-sky-50/50 border border-sky-400' : 'bg-white border border-slate-200 hover:border-kp-blue-300'
                    }`;
                    chip.innerHTML = `
                        <div class="flex items-center gap-2 cursor-pointer select-none whitespace-nowrap min-w-0" onclick="inspectSelectedDateTv('${d}')">
                            <span class="text-xs font-bold text-slate-500 font-mono shrink-0">${i+1}.</span>
                            <span class="text-xs font-bold text-slate-800 group-hover:text-kp-blue-700 transition whitespace-nowrap">${formatIndoChip(d)}</span>
                        </div>
                        <button type="button" onclick="removeSingleDateTv('${d}')"
                                title="Hapus tanggal ${d}"
                                class="w-5 h-5 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition cursor-pointer text-xs font-bold shrink-0 ml-2">
                            &times;
                        </button>
                    `;
                    container.appendChild(chip);
                });
            }

            const remaining = targetQuota - selectedDates.length;
            if (remaining > 0) {
                const emptySlot = document.createElement('div');
                emptySlot.className = "p-2 rounded-xl border border-dashed border-kp-blue-300 bg-kp-blue-50/50 hover:bg-kp-blue-100/50 transition-all flex items-center justify-between text-xs text-kp-blue-800 gap-2";
                emptySlot.innerHTML = `
                    <div class="flex items-center gap-1.5 min-w-0">
                        <svg class="w-3.5 h-3.5 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span class="font-medium text-xs truncate">Pilih Tanggal</span>
                    </div>
                    <span class="text-xs font-bold font-mono px-2 py-0.5 rounded-md bg-white text-kp-blue-700 border border-kp-blue-200 shrink-0 shadow-2xs">
                        Sisa ${remaining}
                    </span>
                `;
                container.appendChild(emptySlot);
            }

            const labelTotal = document.getElementById('labelTotalDatesCount');
            if (labelTotal) labelTotal.textContent = `${selectedDates.length} Tanggal`;
        }

        function inspectSelectedDateTv(dateStr) {
            activeDetailDate = dateStr;
            const parts = dateStr.split('-');
            currentCalYear = parseInt(parts[0]);
            currentCalMonth = parseInt(parts[1]) - 1;
            fetchKalenderBulanTv(currentCalYear, currentCalMonth + 1, () => {
                renderCalendarTv();
                renderSelectedDatesChipsTv();
                updatePanelInfoTanggalTv(dateStr);
            });
        }

        function removeSingleDateTv(dateStr) {
            selectedDates = selectedDates.filter(d => d !== dateStr);
            syncTanggalMuatTv();
            if (activeDetailDate === dateStr) {
                updatePanelInfoTanggalTv(dateStr);
            }
        }

        function resetSemuaTanggalTv() {
            selectedDates = [];
            syncTanggalMuatTv();
        }

        function syncTanggalMuatTv() {
            const hiddenPrimary = document.getElementById('tanggal_muatiklanpriangan');
            const hiddenList = document.getElementById('tanggal_muat_list');

            if (selectedDates.length > 0) {
                if (hiddenPrimary) hiddenPrimary.value = selectedDates[0];
                if (hiddenList) hiddenList.value = selectedDates.join(',');
            } else {
                if (hiddenPrimary) hiddenPrimary.value = '';
                if (hiddenList) hiddenList.value = '';
            }

            renderCalendarTv();
            renderSelectedDatesChipsTv();
            updateSimulatorLive();
        }

        function stepJumlahTayangTv(delta) {
            const input = document.getElementById('in_qty');
            if (!input) return;
            let val = parseInt(input.value) || 1;
            val = Math.max(1, val + delta);
            input.value = val;
            handleJumlahMuatTvChange(val);
        }

        function handleJumlahMuatTvChange(val) {
            renderSelectedDatesChipsTv();
            hitungSemua();
        }

        // --- KALKULASI UANG & KASIR TV ---
        function formatRupiah(num) {
            if (!num) return '0';
            return new Intl.NumberFormat('id-ID').format(num);
        }

        function cleanNumber(str) {
            if (!str) return 0;
            return parseFloat(str.toString().replace(/[^0-9]/g, '')) || 0;
        }

        function formatInput(el) {
            if (typeof window.formatRupiahInput === 'function') {
                window.formatRupiahInput(el);
            } else {
                const v = cleanNumber(el.value);
                el.value = formatRupiah(v);
            }
            hitungSemua();
        }

        function handleDiskonPersenChange(val) {
            const harga = cleanNumber(document.getElementById('in_harga')?.value);
            const qty = parseFloat(document.getElementById('in_qty')?.value) || 1;
            const omset = harga * qty;
            const persen = parseFloat(val) || 0;
            const nominal = Math.round((omset * persen) / 100);

            const inputDiskon = document.getElementById('in_diskon');
            if (inputDiskon) inputDiskon.value = nominal > 0 ? formatRupiah(nominal) : '0';

            hitungSemua();
        }

        function handleDiskonNominalInput(el) {
            if (typeof window.formatRupiahInput === 'function') {
                window.formatRupiahInput(el);
            }
            const harga = cleanNumber(document.getElementById('in_harga')?.value);
            const qty = parseFloat(document.getElementById('in_qty')?.value) || 1;
            const omset = harga * qty;
            const nominal = cleanNumber(el.value);

            const inputPersen = document.getElementById('diskon_persen');
            if (omset > 0 && nominal > 0) {
                const persen = Math.round((nominal / omset) * 1000) / 10;
                if (inputPersen) inputPersen.value = persen;
            } else if (nominal === 0 && inputPersen) {
                inputPersen.value = 0;
            }

            hitungSemua();
        }

        function setBayarLunasPriangan() {
            const grandTotal = cleanNumber(document.getElementById('lbl_total_text')?.textContent);
            const inBayar = document.getElementById('in_bayar');
            if (inBayar) {
                inBayar.value = grandTotal > 0 ? formatRupiah(grandTotal) : '0';
                hitungSemua();
            }
        }

        function hitungSemua() {
            const harga = cleanNumber(document.getElementById('in_harga')?.value);
            const diskon = cleanNumber(document.getElementById('in_diskon')?.value);
            const bayar = cleanNumber(document.getElementById('in_bayar')?.value);
            const qty = parseFloat(document.getElementById('in_qty')?.value) || 1;

            const omset = harga * qty;
            const subtotal = Math.max(0, omset - diskon);
            const ppn = Math.round(subtotal * (configPpnPersen / 100));
            const total = subtotal + ppn;
            const piutang = Math.max(0, total - bayar);

            const lblDesc = document.getElementById('lbl_subtotal_desc');
            if (lblDesc) {
                lblDesc.innerText = qty > 1 ? `Subtotal (${qty}x spot):` : 'Subtotal:';
            }

            const lblBrutto = document.getElementById('lbl_brutto');
            if (lblBrutto) lblBrutto.innerText = 'Rp ' + formatRupiah(omset);
            const lblDiskon = document.getElementById('lbl_diskon');
            if (lblDiskon) lblDiskon.innerText = (diskon > 0 ? '- Rp ' : 'Rp ') + formatRupiah(diskon);
            const lblNetto = document.getElementById('lbl_netto');
            if (lblNetto) lblNetto.innerText = 'Rp ' + formatRupiah(subtotal);

            const lblSubtotal = document.getElementById('lbl_subtotal');
            if (lblSubtotal) lblSubtotal.innerText = 'Rp ' + formatRupiah(subtotal);
            const lblPpn = document.getElementById('lbl_ppn');
            if (lblPpn) lblPpn.innerText = 'Rp ' + formatRupiah(ppn);
            const lblTotal = document.getElementById('lbl_total_text');
            if (lblTotal) lblTotal.innerText = 'Rp ' + formatRupiah(total);

            const elPiutang = document.getElementById('lbl_piutang');
            if (elPiutang) elPiutang.innerText = 'Rp ' + formatRupiah(piutang);

            const elFrekuensi = document.getElementById('strukFrekuensi');
            if (elFrekuensi) {
                elFrekuensi.innerText = qty + ' Spot Tayang';
            }

            const badgeStatus = document.getElementById('badge_status_bayar');
            if (badgeStatus) {
                if (total === 0) {
                    badgeStatus.className = 'text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200';
                    badgeStatus.innerText = 'Draft Order';
                } else if (bayar === 0) {
                    badgeStatus.className = 'text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200';
                    badgeStatus.innerText = 'Menunggu Pembayaran';
                } else if (piutang > 0) {
                    badgeStatus.className = 'text-xs font-semibold px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 border border-sky-200';
                    badgeStatus.innerText = 'Sebagian (DP)';
                } else {
                    badgeStatus.className = 'text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200';
                    badgeStatus.innerText = 'Lunas';
                }
            }

            const prevQty = document.getElementById('prev_qty');
            if (prevQty) prevQty.innerText = qty + ' Spot Tayang';

            updateInsentifFromDpp(subtotal);
            updateKomisiFromDpp(subtotal);
        }

        function updateInsentifFromDpp(dpp) {
            const inputPersen = document.getElementById('insentif_persen');
            const inputNominal = document.getElementById('insentif_transaksipriangan');
            if (!inputPersen || !inputNominal) return;
            const persen = parseFloat(inputPersen.value) || 0;
            if (persen > 0) {
                const nominal = Math.round((dpp * persen) / 100);
                inputNominal.value = formatRupiah(nominal);
            }
        }

        function handleInsentifPersenChange(val) {
            const harga = cleanNumber(document.getElementById('in_harga')?.value);
            const diskon = cleanNumber(document.getElementById('in_diskon')?.value);
            const qty = parseFloat(document.getElementById('in_qty')?.value) || 1;
            const dpp = Math.max(0, (harga * qty) - diskon);

            const persen = parseFloat(val) || 0;
            const nominal = Math.round((dpp * persen) / 100);
            const inputNominal = document.getElementById('insentif_transaksipriangan');
            if (inputNominal) inputNominal.value = formatRupiah(nominal);
        }

        function handleInsentifNominalInput(el) {
            const raw = cleanNumber(el.value);
            el.value = raw > 0 ? formatRupiah(raw) : '';
            const harga = cleanNumber(document.getElementById('in_harga')?.value);
            const diskon = cleanNumber(document.getElementById('in_diskon')?.value);
            const qty = parseFloat(document.getElementById('in_qty')?.value) || 1;
            const dpp = Math.max(0, (harga * qty) - diskon);

            if (dpp > 0) {
                const persen = Math.round((raw / dpp) * 1000) / 10;
                const inputPersen = document.getElementById('insentif_persen');
                if (inputPersen) inputPersen.value = persen;
            }
        }

        function updateKomisiFromDpp(dpp) {
            const inputPersen = document.getElementById('komisi_persen');
            const inputNominal = document.getElementById('komisi_transaksipriangan');
            if (!inputPersen || !inputNominal) return;
            const persen = parseFloat(inputPersen.value) || 0;
            if (persen > 0) {
                const nominal = Math.round((dpp * persen) / 100);
                inputNominal.value = formatRupiah(nominal);
            }
        }

        function handleKomisiPersenChange(val) {
            const harga = cleanNumber(document.getElementById('in_harga')?.value);
            const diskon = cleanNumber(document.getElementById('in_diskon')?.value);
            const qty = parseFloat(document.getElementById('in_qty')?.value) || 1;
            const dpp = Math.max(0, (harga * qty) - diskon);

            const persen = parseFloat(val) || 0;
            const nominal = Math.round((dpp * persen) / 100);
            const inputNominal = document.getElementById('komisi_transaksipriangan');
            if (inputNominal) inputNominal.value = formatRupiah(nominal);
        }

        function handleKomisiNominalInput(el) {
            const raw = cleanNumber(el.value);
            el.value = raw > 0 ? formatRupiah(raw) : '';
            const harga = cleanNumber(document.getElementById('in_harga')?.value);
            const diskon = cleanNumber(document.getElementById('in_diskon')?.value);
            const qty = parseFloat(document.getElementById('in_qty')?.value) || 1;
            const dpp = Math.max(0, (harga * qty) - diskon);

            if (dpp > 0) {
                const persen = Math.round((raw / dpp) * 1000) / 10;
                const inputPersen = document.getElementById('komisi_persen');
                if (inputPersen) inputPersen.value = persen;
            }
        }

        function handleSalesAeChange(select) {
            const selected = select.options[select.selectedIndex];
            if (!selected) return;
            const kode = selected.getAttribute('data-kode') || '';
            const nama = selected.getAttribute('data-nama') || selected.text;
            const fullSalesName = (kode === 'FO') ? 'Front Office (Loket Langsung)' : (kode ? `${kode} - ${nama}` : nama);

            const hiddenSales = document.getElementById('sales_iklanpriangan');
            if (hiddenSales) hiddenSales.value = fullSalesName;
        }

        // --- SIMULASI LIVE ORDER PRIANGAN TV ---
        function updateSimulatorLive() {
            const instansi = document.getElementById('inputManualNamaPemasang')?.value.trim() || 'Belum dipilih';
            const materi = document.getElementById('materi_iklan')?.value.trim() || 'Belum diisi';
            const durasi = document.getElementById('durasi_detik')?.value || '30';
            const jam = document.getElementById('jam_tayang')?.value || '19:00 WIB';
            const activeTab = document.getElementById('kategori_tv_form')?.value || 'PODCAST';

            const prevKlien = document.getElementById('prev_klien');
            if (prevKlien) prevKlien.innerText = instansi;

            const prevDurasiJam = document.getElementById('prev_durasi_jam');
            if (prevDurasiJam) prevDurasiJam.innerText = `${durasi} Detik • ${jam}`;

            const prevMateri = document.getElementById('prev_materi');
            if (prevMateri) prevMateri.innerText = 'Materi: ' + materi;

            const prevTgl = document.getElementById('prev_tgl');
            if (prevTgl) {
                if (selectedDates.length > 0) {
                    prevTgl.innerText = `${formatIndoChip(selectedDates[0])} (${selectedDates.length} spot)`;
                } else {
                    prevTgl.innerText = '-';
                }
            }

            // Platform Tags di Simulator Kanan
            const tagsCont = document.getElementById('prev_tv_platform_tags');
            if (tagsCont) {
                const colorMap = {
                    'PODCAST':      { text: 'Podcast TV', cls: 'bg-purple-100 text-purple-800 border-purple-200' },
                    'INSTAGRAM':    { text: 'Instagram', cls: 'bg-pink-100 text-pink-800 border-pink-200' },
                    'TIKTOK':       { text: 'TikTok', cls: 'bg-slate-900 text-white border-slate-700' },
                    'BANNER_WEB':   { text: 'Banner Web', cls: 'bg-blue-100 text-blue-800 border-blue-200' },
                    'VIDEO_LIPSUS': { text: 'Video Lipsus', cls: 'bg-amber-100 text-amber-800 border-amber-200' }
                };

                const badges = [];
                const items = Object.values(selectedTvProducts || {});
                const seen = new Set();
                items.forEach(it => {
                    if (!seen.has(it.tabKey) && colorMap[it.tabKey]) {
                        seen.add(it.tabKey);
                        const cfg = colorMap[it.tabKey];
                        badges.push(`<span class="px-2 py-0.5 rounded text-xs font-semibold border ${cfg.cls}">${cfg.text}</span>`);
                    }
                });

                if (badges.length === 0) {
                    badges.push(`<span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">Belum ada platform dipilih</span>`);
                }

                tagsCont.innerHTML = badges.join(' ');
            }
        }

        // --- SUBMIT PARITY VALIDATION & MODAL ---
        function konfirmasiSimpanTransaksi() {
            const hiddenNamaInput = document.getElementById('nama_pemasangpriangan');
            const manualInput = document.getElementById('inputManualNamaPemasang');
            const searchInput = document.getElementById('inputSearchPelanggan');

            let finalNama = (manualInput && manualInput.value.trim()) || (hiddenNamaInput && hiddenNamaInput.value.trim()) || (searchInput && searchInput.value.trim());

            if (!finalNama) {
                alert('Silakan pilih pelanggan dari daftar atau ketik nama pemesan di kolom yang tersedia.');
                if (manualInput) {
                    manualInput.focus();
                    manualInput.classList.add('ring-2', 'ring-rose-500');
                    setTimeout(() => manualInput.classList.remove('ring-2', 'ring-rose-500'), 2500);
                }
                return false;
            }

            if (hiddenNamaInput) hiddenNamaInput.value = finalNama;

            const materiInput = document.getElementById('materi_iklan');
            if (!materiInput || !materiInput.value.trim()) {
                alert('Judul / Materi Iklan TV wajib diisi.');
                materiInput?.focus();
                return false;
            }

            // Parity Check: Selected Dates vs Frekuensi
            const totalMuatInput = document.getElementById('in_qty');
            const targetQuota = parseInt(totalMuatInput ? totalMuatInput.value : 1) || 1;

            if (selectedDates.length !== targetQuota) {
                alert(`Perhatian: Anda menentukan Frekuensi Siaran sebanyak ${targetQuota} spot, namun baru memilih ${selectedDates.length} tanggal di kalender. Mohon sesuaikan tanggal di kalender.`);
                return false;
            }

            const namaLabel = document.getElementById('konfirmasiNamaPemesan');
            if (namaLabel) namaLabel.textContent = finalNama;

            const produkLabel = document.getElementById('konfirmasiProdukNama');
            const currentProdukText = document.getElementById('prev_jenis')?.textContent || '-';
            if (produkLabel) produkLabel.textContent = currentProdukText;

            const jadwalLabel = document.getElementById('konfirmasiJadwalTayang');
            if (jadwalLabel) jadwalLabel.textContent = `${selectedDates.length} Spot Siaran TV`;

            const totalNettoStr = document.getElementById('lbl_total_text')?.textContent || 'Rp 0';
            const totalBiayaLabel = document.getElementById('konfirmasiTotalBiaya');
            if (totalBiayaLabel) totalBiayaLabel.textContent = totalNettoStr;

            const modal = document.getElementById('modalKonfirmasiSimpan');
            if (modal) modal.classList.remove('hidden');
        }

        function closeModalKonfirmasiSimpan() {
            const modal = document.getElementById('modalKonfirmasiSimpan');
            if (modal) modal.classList.add('hidden');
        }

        function eksekusiSubmitTransaksi() {
            const btn = document.getElementById('btnSubmitFinal');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Menyimpan...</span>
                `;
            }
            document.getElementById('formTransaksiPriangan').submit();
        }

        // --- MODAL SALES & PELANGGAN ON-THE-FLY ---
        function openModalPelangganBaru() {
            closeModalSalesAeBaru();
            const modal = document.getElementById('modalPelangganBaru');
            if (modal) modal.classList.remove('hidden');
            const cont = document.getElementById('containerModalPelangganExtra');
            const lbl = document.getElementById('labelToggleExtra');
            const icon = document.getElementById('iconToggleExtra');
            if (cont) cont.classList.add('hidden');
            if (lbl) lbl.textContent = 'Informasi Tambahan';
            if (icon) icon.style.transform = 'rotate(0deg)';
        }

        function closeModalPelangganBaru() {
            const modal = document.getElementById('modalPelangganBaru');
            if (modal) modal.classList.add('hidden');
            const cont = document.getElementById('containerModalPelangganExtra');
            const lbl = document.getElementById('labelToggleExtra');
            const icon = document.getElementById('iconToggleExtra');
            if (cont) cont.classList.add('hidden');
            if (lbl) lbl.textContent = 'Informasi Tambahan';
            if (icon) icon.style.transform = 'rotate(0deg)';
        }

        function toggleModalPelangganExtra() {
            const cont = document.getElementById('containerModalPelangganExtra');
            const lbl = document.getElementById('labelToggleExtra');
            const icon = document.getElementById('iconToggleExtra');
            if (!cont || !lbl) return;
            if (cont.classList.contains('hidden')) {
                cont.classList.remove('hidden');
                lbl.textContent = 'Sembunyikan Informasi Tambahan';
                if (icon) icon.style.transform = 'rotate(180deg)';
            } else {
                cont.classList.add('hidden');
                lbl.textContent = 'Informasi Tambahan';
                if (icon) icon.style.transform = 'rotate(0deg)';
            }
        }

        function simpanPelangganOnTheFly() {
            const nama = document.getElementById('modal_nama_instansi').value.trim();
            const jenis = document.getElementById('modal_jenis_klien').value;
            const npwp = document.getElementById('modal_npwp_nik')?.value.trim() || '';
            const email = document.getElementById('modal_email')?.value.trim() || '';
            const telp = document.getElementById('modal_no_telp').value.trim();
            const alamat = document.getElementById('modal_alamat').value.trim();
            const creditLimit = document.getElementById('modal_credit_limit')?.value || 0;
            const btn = document.getElementById('btnSimpanPelangganModal');

            if (!nama) {
                alert('Nama Instansi / Pemesan wajib diisi.');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span>Menyimpan...</span>';

            fetch("{{ route('pelanggan.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    nama_instansi: nama,
                    jenis_klien: jenis,
                    npwp_nik: npwp,
                    email: email,
                    no_telp: telp,
                    alamat: alamat,
                    credit_limit: creditLimit
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<span>Simpan &amp; Pilih</span>';

                const isSuccess = (data.status === 'success' || data.success);
                const newP = data.data || data.pelanggan;

                if (isSuccess && newP) {
                    window.currentPelangganList.unshift({
                        id: newP.id,
                        kode_pelanggan: newP.kode_pelanggan,
                        nama_instansi: newP.nama_instansi,
                        jenis_klien: newP.jenis_klien,
                        npwp_nik: newP.npwp_nik,
                        email: newP.email,
                        no_telp: newP.no_telp,
                        alamat: newP.alamat,
                        kontak_pic: null,
                        total_piutang: 0
                    });

                    // Bersihkan formulir modal
                    document.getElementById('modal_nama_instansi').value = '';
                    document.getElementById('modal_no_telp').value = '';
                    document.getElementById('modal_alamat').value = '';
                    if (document.getElementById('modal_npwp_nik')) document.getElementById('modal_npwp_nik').value = '';
                    if (document.getElementById('modal_credit_limit')) document.getElementById('modal_credit_limit').value = '';
                    if (document.getElementById('modal_email')) document.getElementById('modal_email').value = '';

                    closeModalPelangganBaru();
                    selectPelangganItemById(newP.id);
                } else {
                    alert('Gagal menyimpan pelanggan baru: ' + (data.message || 'Coba lagi'));
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<span>Simpan &amp; Pilih</span>';
                alert('Terjadi kesalahan koneksi.');
            });
        }

        function openModalSalesAeBaru() {
            closeModalPelangganBaru();
            document.getElementById('modalSalesAeBaru').classList.remove('hidden');
            autoGenerateKodeSalesModal();
        }

        function closeModalSalesAeBaru() {
            document.getElementById('modalSalesAeBaru').classList.add('hidden');
        }

        function autoGenerateKodeSalesModal() {
            const jenis = document.getElementById('modal_sales_jenis').value;
            fetch(`{{ route('sales-ae.generate-kode') }}?jenis=${jenis}`)
                .then(res => res.json())
                .then(data => {
                    if (data.kode) {
                        document.getElementById('modal_sales_kode').value = data.kode;
                    }
                })
                .catch(() => {});
        }

        function simpanSalesAeOnTheFly() {
            const jenis = document.getElementById('modal_sales_jenis').value;
            const kode = document.getElementById('modal_sales_kode').value.trim();
            const nama = document.getElementById('modal_sales_nama').value.trim();

            if (!kode || !nama) {
                alert('Kode AE dan Nama Lengkap Sales wajib diisi!');
                return;
            }

            const btn = document.getElementById('btnSimpanSalesAeModal');
            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            fetch('{{ route("sales-ae.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    jenis_kode: jenis,
                    kode_ae: kode,
                    nama_ae: nama
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<span>Simpan &amp; Pilih</span>';

                if (data.success && data.salesAe) {
                    const select = document.getElementById('sales_ae_id');
                    const opt = document.createElement('option');
                    opt.value = data.salesAe.id;
                    opt.setAttribute('data-kode', data.salesAe.kode_ae);
                    opt.setAttribute('data-nama', data.salesAe.nama_ae);
                    opt.text = data.salesAe.nama_ae + ' — ' + (data.salesAe.jenis_kode || 'AE');
                    opt.selected = true;
                    select.appendChild(opt);

                    handleSalesAeChange(select);
                    closeModalSalesAeBaru();
                } else {
                    alert('Gagal menyimpan Sales / AE baru: ' + (data.message || 'Coba lagi'));
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<span>Simpan &amp; Pilih</span>';
                alert('Terjadi kesalahan koneksi.');
            });
        }

        // Close dropdown when clicked outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('containerSearchPelanggan');
            const dropdown = document.getElementById('dropdownHasilPelanggan');
            if (container && dropdown && !container.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // --- INITIALIZATION ON LOAD ---
        document.addEventListener('DOMContentLoaded', function() {
            renderCalendarTv();
            renderSelectedDatesChipsTv();
            if (selectedDates.length > 0) {
                updatePanelInfoTanggalTv(selectedDates[0]);
            }

            fetchKalenderBulanTv(currentCalYear, currentCalMonth + 1, () => {
                renderCalendarTv();
                renderSelectedDatesChipsTv();
                if (selectedDates.length > 0) {
                    updatePanelInfoTanggalTv(selectedDates[0]);
                }
            });

            hitungSemua();
            updateSimulatorLive();
        });
    </script>
    @endpush
</x-app-layout>