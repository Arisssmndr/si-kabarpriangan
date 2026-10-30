<x-app-layout>
    <div class="space-y-6">

        <!-- ============================================================== -->
        <!-- HEADER HALAMAN: STANDAR SELARAS TRANSAKSI KORAN                -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        {{ isset($transaksi) ? 'Ubah Data & Jadwal Order Publikasi Online' : 'Entri Order Publikasi Online' }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl font-normal">
                        {{ isset($transaksi) ? 'Formulir koreksi dan reschedule publikasi multimedia Kabar Priangan Grup.' : 'Formulir pemesanan publikasi portal online, medsos, podcast, dan Priangan TV.' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0 flex-wrap sm:flex-nowrap">
                <div class="text-right hidden sm:block">
                    <div class="text-[11px] font-semibold text-slate-500">No. Faktur</div>
                    <div class="font-mono text-base font-bold text-kp-blue-700 leading-tight">{{ $nofakturonline }}</div>
                </div>
                <a href="{{ route('transaksionline.index') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                    &larr; <span>Transaksi Online</span>
                </a>
            </div>
        </div>

        <!-- Notice Deadline & Cut-Off Tayang Online & Medsos (3 Status: Normal / Kuning / Merah) -->
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
                'passed'  => 'Lewat Batas Order: ' . $jamCutoffStr . ' WIB',
                'warning' => 'Mendekati Batas Order: ' . $jamCutoffStr . ' WIB',
                default   => 'Batas Order Hari Ini: ' . $jamCutoffStr . ' WIB',
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
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs">
                        <span class="{{ $bannerTitleClass }}">
                            {{ $bannerTitleText }}
                        </span>
                        <span class="text-slate-300 hidden sm:inline">|</span>
                        <span class="text-slate-600 font-medium">
                            Jadwal Tayang Terdekat: <span class="{{ $bannerEdisiClass }}">{{ $edisiTerdekat }}</span>
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 self-end sm:self-center font-mono text-xs font-semibold px-2.5 py-1 rounded-md border shadow-2xs {{ $bannerClockClass }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ $nowTime->format('H:i') }} WIB</span>
                </div>
            </div>
        </div>

        <!-- Feedback Validasi Error Global jika ada -->
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
        <!-- FORM UTAMA DENGAN DUA KOLOM (KIRI: INPUT, KANAN: SIMULASI)     -->
        <!-- ============================================================== -->
        <form id="formTransaksiOnline" method="POST" action="{{ isset($transaksi) ? route('transaksionline.update', $transaksi->id) : route('transaksionline.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($transaksi))
                @method('PUT')
            @endif

            <!-- Hidden Technical Fields -->
            <input type="hidden" name="nofakturonline" value="{{ $nofakturonline }}">
            <input type="hidden" name="tanggal_transaksionline" value="{{ old('tanggal_transaksionline', isset($transaksi) ? $transaksi->tanggal_transaksionline?->format('Y-m-d') : date('Y-m-d')) }}">
            <input type="hidden" name="ppn_persen" id="ppn_persen" value="{{ old('ppn_persen', isset($transaksi) ? $transaksi->ppn_persen : ($ppnPersen ?? 12)) }}">
            <input type="hidden" name="jenis_pembayaran" id="jenis_pembayaran" value="{{ old('jenis_pembayaran', isset($transaksi) ? ($transaksi->piutang_transaksionline > 0 ? 'Kredit' : 'Tunai') : 'Tunai') }}">
            <input type="hidden" name="pelanggan_id" id="pelanggan_id" value="{{ old('pelanggan_id', isset($transaksi) ? $transaksi->pelanggan_id : '') }}">
            <input type="hidden" name="nama_pemasangonline" id="nama_pemasangonline" value="{{ old('nama_pemasangonline', isset($transaksi) ? $transaksi->nama_pemasangonline : '') }}">
            <input type="hidden" name="tanggal_muatiklanonline" id="tanggal_muatiklanonline" value="{{ old('tanggal_muatiklanonline', isset($transaksi) ? $transaksi->tanggal_muatiklanonline?->format('Y-m-d') : date('Y-m-d')) }}">
            <input type="hidden" name="tanggal_muat_list" id="tanggal_muat_list" value="{{ old('tanggal_muat_list', isset($transaksi) ? $transaksi->tanggal_muat_list : date('Y-m-d')) }}">
            <input type="hidden" name="kategori_form" id="kategori_form" value="{{ old('kategori_form', 'PORTAL_BERITA') }}">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- ============================================================== -->
                <!-- PANEL KIRI: INPUT DATA TRANSAKSI                               -->
                <!-- ============================================================== -->
                <div class="lg:col-span-7 space-y-5">

                    <!-- SEKSI 1: CARA BAYAR & DATA PEMASANG (PERSIS TRANSAKSI KORAN) -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">1</span>
                                <span>Data Pemesan &amp; Instansi</span>
                            </span>
                        </div>

                        <!-- PILIHAN SKEMA PEMBAYARAN: TUNAI vs KREDIT -->
                        <div>
                            <label class="text-sm font-semibold text-slate-700 mb-2 block">
                                Skema Pembayaran <span class="text-rose-500">*</span>
                            </label>

                            @php
                                $currentJenisBayar = old('jenis_pembayaran', isset($transaksi) ? ($transaksi->piutang_transaksionline > 0 ? 'Kredit' : 'Tunai') : 'Tunai');
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
                                $oldNamaPemasang = old('nama_pemasangonline', $selectedOldPelanggan->nama_instansi ?? (isset($transaksi) ? $transaksi->nama_pemasangonline : ''));
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
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 border border-kp-blue-200/90 hover:border-kp-blue-300 transition shadow-2xs active:scale-95 cursor-pointer whitespace-nowrap shrink-0"
                                                title="Tambah Data Pelanggan Baru">
                                            <svg class="w-3.5 h-3.5 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            <span>Tambah Baru</span>
                                        </button>
                                    </div>

                                    <!-- A. Kondisi Master Terpilih: Card Klien Terpilih -->
                                    <div id="cardPelangganTerpilih" 
                                         class="{{ $selectedOldPelanggan ? '' : 'hidden' }} px-3 py-2 rounded-xl bg-kp-blue-50/70 border border-kp-blue-200/80 flex items-center justify-between gap-3 shadow-2xs min-h-[44px]">
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
                                                <span id="labelEmailPelanggan" class="inline-flex items-center gap-1 truncate {{ empty($selectedOldPelanggan?->email) ? 'hidden' : '' }}">
                                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                    <span id="textEmailPelanggan">{{ $selectedOldPelanggan->email ?? '' }}</span>
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
                                               placeholder="Nama instansi atau perorangan..."
                                               value="{{ $oldNamaPemasang }}"
                                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Baris: No. Telepon / WA & Nama PIC -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                            <div>
                                <label class="text-sm font-semibold text-slate-700 mb-1.5 block">
                                    No. Telepon / WA
                                </label>
                                <div class="relative flex items-center">
                                    <input type="text" name="no_kontak_pemasang" id="no_kontak_pemasang"
                                           placeholder="08xxxxxxxxxx"
                                           value="{{ old('no_kontak_pemasang', isset($transaksi) ? ($transaksi->no_kontak_pemasang ?: $transaksi->no_telp) : ($selectedOldPelanggan?->no_telp ?? '')) }}"
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
                            <textarea name="alamat_pemasangonline" id="alamat_pemasangonline" rows="2"
                                      placeholder="Alamat lengkap pemesan..."
                                      oninput="updateSimulatorLive()"
                                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs resize-none">{{ old('alamat_pemasangonline', isset($transaksi) ? $transaksi->alamat_pemasangonline : ($selectedOldPelanggan?->alamat ?? '')) }}</textarea>
                        </div>

                        <!-- Sales & Komisioner -->
                        <div class="pt-4 border-t border-slate-100 space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
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
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 border border-kp-blue-200/90 hover:border-kp-blue-300 transition shadow-2xs active:scale-95 cursor-pointer whitespace-nowrap shrink-0"
                                                title="Tambah Sales / AE Baru">
                                            <svg class="w-3.5 h-3.5 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
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
                                    <input type="hidden" name="sales_iklanonline" id="sales_iklanonline" value="{{ old('sales_iklanonline', isset($transaksi) ? $transaksi->sales_iklanonline : $initialSalesName) }}">

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
                                                <span class="absolute right-2 top-2.5 text-xs text-slate-400 font-medium pointer-events-none">%</span>
                                            </div>
                                            <div class="col-span-3 relative">
                                                <span class="absolute left-2.5 top-2.5 text-xs text-slate-400 font-medium pointer-events-none">Rp</span>
                                                <input type="text" name="insentif_transaksionline" id="insentif_transaksionline" 
                                                       value="{{ old('insentif_transaksionline', isset($transaksi) && $transaksi->insentif_transaksionline ? number_format($transaksi->insentif_transaksionline, 0, ',', '.') : 0) }}"
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
                                        <span class="text-xs text-slate-400 font-normal shrink-0">
                                            Opsional
                                        </span>
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
                                                <span class="absolute right-2 top-2.5 text-xs text-slate-400 font-medium pointer-events-none">%</span>
                                            </div>
                                            <div class="col-span-3 relative">
                                                <span class="absolute left-2.5 top-2.5 text-xs text-slate-400 font-medium pointer-events-none">Rp</span>
                                                <input type="text" name="komisi_transaksionline" id="komisi_transaksionline" 
                                                       value="{{ old('komisi_transaksionline', isset($transaksi) && $transaksi->komisi_transaksionline ? number_format($transaksi->komisi_transaksionline, 0, ',', '.') : 0) }}"
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
                    <!-- SEKSI 2: JENIS IKLAN ONLINE & PILIHAN PLATFORM                 -->
                    <!-- ============================================================== -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">2</span>
                                <span>Platform &amp; Format Iklan</span>
                            </span>
                            <span id="badge_status_paket" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-100 text-kp-blue-800 border border-kp-blue-200">
                                1x Tayang
                            </span>
                        </div>

                        @php
                            $groupedOnline = $iklanonline->groupBy('tipe_dasar');
                            $tabOrder = ['PORTAL_BERITA', 'INSTAGRAM', 'TIKTOK', 'YOUTUBE', 'PODCAST'];
                            $defaultTab = old('kategori_form', 'PORTAL_BERITA');
                            
                            $targetProductId = old('id_iklanonline', isset($transaksi) ? $transaksi->id_iklanonline : null);
                            $initialProduct = $targetProductId ? $iklanonline->firstWhere('id', $targetProductId) : null;

                            // Old platforms selection (mendukung multi platform seperti Portal + Medsos)
                            $oldPlatforms = old('platforms_publikasi', isset($transaksi) ? ($transaksi->platforms_publikasi ?? []) : []);
                            if (is_string($oldPlatforms)) {
                                $oldPlatforms = array_values(array_filter(array_map('trim', explode(',', $oldPlatforms))));
                            }
                            if (!is_array($oldPlatforms)) {
                                $oldPlatforms = [];
                            }

                            // Selected portals (default empty array or array from old/transaksi)
                            $oldPortals = old('portal_iklanonline', isset($transaksi) 
                                ? (is_array($transaksi->portal_iklanonline) ? $transaksi->portal_iklanonline : [$transaksi->portal_iklanonline]) 
                                : []);
                            if (!is_array($oldPortals)) {
                                $oldPortals = [$oldPortals];
                            }
                        @endphp

                        <!-- Hidden Product ID, Kategori Tab & Platform Hidden Fields -->
                        <input type="hidden" name="id_iklanonline" id="id_iklanonline" value="{{ $initialProduct?->id ?? '' }}">
                        <input type="hidden" name="kategori_form" id="kategori_form" value="{{ $defaultTab }}">
                        
                        <!-- Multi-select Hidden Containers for Products & Platforms -->
                        <div id="containerSelectedProductIds">
                            @if($initialProduct)
                                <input type="hidden" name="selected_produk_ids[]" value="{{ $initialProduct->id }}">
                            @endif
                        </div>
                        <div id="containerPlatformsPublikasi">
                            @foreach($oldPlatforms as $plat)
                                <input type="hidden" name="platforms_publikasi[]" value="{{ $plat }}">
                            @endforeach
                        </div>
                        <input type="hidden" name="produk_jasa" id="in_produk_jasa" value="{{ $initialProduct ? $initialProduct->nama_jenis : '' }}">

                        <!-- Tab Platform Pills (Selaras Foto 1 & Transaksi Koran) -->
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($tabOrder as $tabKey)
                                @php
                                    $tabName = match($tabKey) {
                                        'PORTAL_BERITA' => 'Portal Berita',
                                        'INSTAGRAM'     => 'Instagram',
                                        'TIKTOK'        => 'TikTok',
                                        'YOUTUBE'       => 'YouTube',
                                        'PODCAST'       => 'Podcast',
                                        default         => $tabKey
                                    };
                                    $countItems = ($tabKey === 'PORTAL_BERITA') ? count($portalList) : ($groupedOnline->get($tabKey, collect())->count());
                                    $isTabActive = ($defaultTab === $tabKey);
                                @endphp
                                <button type="button" 
                                        onclick="switchPlatformTab('{{ $tabKey }}', '{{ $tabName }}')"
                                        id="tabBtn_{{ $tabKey }}"
                                        class="tab-platform-btn px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 {{ $isTabActive ? 'bg-kp-blue-600 text-white font-semibold shadow-xs border border-kp-blue-600' : 'bg-white text-slate-700 font-medium border border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                                    <span>{{ $tabName }}</span>
                                    <span class="px-1.5 py-0.5 rounded-full text-xs font-semibold {{ $isTabActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $countItems }}
                                    </span>
                                </button>
                            @endforeach
                        </div>

                        <!-- TAB CONTENT 1: PORTAL BERITA (9 WEBSITE RESMI KABAR PRIANGAN) -->
                        <div id="tabContent_PORTAL_BERITA" class="tab-platform-content space-y-3 {{ $defaultTab === 'PORTAL_BERITA' ? '' : 'hidden' }}">
                            <div class="flex items-center justify-end pb-1">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" onclick="pilihSemuaPortal(true)"
                                            class="px-2.5 py-1 text-xs font-semibold text-kp-blue-700 bg-white hover:bg-kp-blue-50 border border-slate-200 rounded-lg transition shadow-2xs cursor-pointer">
                                        Pilih Semua (9 Portal)
                                    </button>
                                    <button type="button" onclick="pilihSemuaPortal(false)"
                                            class="px-2.5 py-1 text-xs font-semibold text-slate-600 hover:text-rose-600 bg-white hover:bg-rose-50 border border-slate-200 rounded-lg transition shadow-2xs cursor-pointer">
                                        Reset
                                    </button>
                                </div>
                            </div>

                            <!-- Grid 9 Portal Berita: Ceklist Lancip & Nama Produk + Harga Saja -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                @foreach($portalList as $pIdx => $portal)
                                    @php
                                        $isPortChecked = in_array($portal['id'], $oldPortals);
                                    @endphp
                                    <label class="portal-checkbox-card relative overflow-hidden flex flex-col justify-center p-3 rounded-xl border transition cursor-pointer select-none {{ $isPortChecked ? 'border-2 border-kp-blue-600 bg-sky-50 text-kp-blue-900 font-semibold ring-1 ring-kp-blue-500/20 shadow-2xs' : 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 font-medium' }}">
                                        <input type="checkbox" name="portal_iklanonline[]" value="{{ $portal['id'] }}"
                                               class="portal-checkbox sr-only"
                                               {{ $isPortChecked ? 'checked' : '' }}
                                               onchange="handlePortalCheckboxChange()">

                                        <!-- Badge Ceklist Lancip Segitiga Sudut Kiri Atas (Foto 2 Style) -->
                                        <span class="badge-corner-check absolute top-0 left-0 w-6 h-6 pointer-events-none z-10 {{ $isPortChecked ? '' : 'hidden' }}">
                                            <svg class="w-6 h-6" viewBox="0 0 24 24">
                                                <path d="M0 0 L24 0 L0 24 Z" fill="#0A72AC" />
                                                <path d="M3.5 7.5 L7 11 L13 5" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>

                                        <div class="pl-1">
                                            <div class="font-bold text-slate-900 text-sm truncate">{{ $portal['nama'] }}</div>
                                            <div class="font-mono font-bold text-xs text-kp-blue-700 mt-1">Rp 750.000</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- TAB CONTENT 2-5: INSTAGRAM, TIKTOK, YOUTUBE, PODCAST (Foto 1 Style, Ceklist Lancip Foto 2) -->
                        @foreach(['INSTAGRAM', 'TIKTOK', 'YOUTUBE', 'PODCAST'] as $otherTab)
                            <div id="tabContent_{{ $otherTab }}" class="tab-platform-content space-y-3 {{ $defaultTab === $otherTab ? '' : 'hidden' }}">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($groupedOnline->get($otherTab, collect()) as $prod)
                                        <button type="button" 
                                                onclick="toggleProdukIklanOnline({{ $prod->id }}, '{{ $prod->kode_jenis }}', '{{ addslashes($prod->nama_jenis) }}', {{ $prod->tarif_dasar }}, '{{ $otherTab }}')"
                                                id="cardProduk_{{ $prod->id }}"
                                                data-id="{{ $prod->id }}"
                                                data-tarif="{{ $prod->tarif_dasar }}"
                                                data-nama="{{ $prod->nama_jenis }}"
                                                data-tab="{{ $otherTab }}"
                                                class="produk-card text-left p-3.5 rounded-xl border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50 transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2.5 min-h-[92px]">
                                            
                                            <!-- Badge Ceklist Lancip Segitiga Sudut Kiri Atas (Foto 2 Style) -->
                                            <span class="badge-corner-check absolute top-0 left-0 w-7 h-7 pointer-events-none z-10 hidden">
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
                    <!-- SEKSI 3: MATERI IKLAN                                          -->
                    <!-- ============================================================== -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">3</span>
                                <span>Materi Iklan</span>
                            </span>
                        </div>

                        <!-- Judul Iklan -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Judul Iklan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="materi_iklan" id="materi_iklan" 
                                   value="{{ old('materi_iklan', isset($transaksi) ? $transaksi->materi_iklan : '') }}" required 
                                   placeholder="Judul atau materi iklan..."
                                   oninput="updateSimulatorLive()"
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                        </div>

                        <!-- Upload File Materi -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                                <span>Upload File Materi</span>
                                <span class="text-xs text-slate-400 font-normal">Maks. 25MB</span>
                            </label>
                            <div class="p-3 bg-slate-50 border-2 border-dashed border-slate-300 rounded-xl text-center space-y-1.5 hover:border-kp-blue-400 transition cursor-pointer relative">
                                <input type="file" name="file_materi" id="file_materi" accept="image/*,video/*,.pdf,.zip,.rar,.doc,.docx"
                                       class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                       onchange="handleFileMateriChange(this)">
                                <div class="flex items-center justify-center gap-2 text-kp-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <span class="text-xs font-semibold" id="labelFileMateriNama">Pilih file atau tarik ke sini</span>
                                </div>
                                <p class="text-xs text-slate-400">Format: JPG, PNG, MP4, PDF, ZIP</p>
                            </div>
                            @if(isset($transaksi) && $transaksi->file_materi)
                                <div class="mt-1.5 flex items-center gap-2 text-xs text-kp-blue-700 bg-kp-blue-50/70 px-2.5 py-1 rounded-lg border border-kp-blue-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    <span>File tersimpan:</span>
                                    <a href="{{ asset('storage/' . $transaksi->file_materi) }}" target="_blank" class="font-semibold underline">Lihat File</a>
                                </div>
                            @endif
                        </div>

                        <!-- Deskripsi / Naskah Iklan -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                                <span>Deskripsi / Naskah Iklan</span>
                                <span class="text-xs text-slate-400 font-normal">Opsional</span>
                            </label>
                            <textarea name="catatan_redaksi" id="catatan_redaksi" rows="3"
                                      placeholder="Naskah advertorial, caption medsos, atau catatan teknis..."
                                      class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs resize-none">{{ old('catatan_redaksi', isset($transaksi) ? $transaksi->catatan_redaksi : '') }}</textarea>
                        </div>

                        <!-- Link Drive (Full Width) & Hidden no_spk -->
                        <div class="pt-1">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Link Materi (Google Drive) <span class="text-xs text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="url" name="url_materi_iklan" id="url_materi_iklan" 
                                   value="{{ old('url_materi_iklan', isset($transaksi) ? $transaksi->url_materi_iklan : '') }}" 
                                   placeholder="https://drive.google.com/..."
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                            <input type="hidden" name="no_spk" id="no_spk" value="{{ old('no_spk', isset($transaksi) ? $transaksi->no_spk : '') }}">
                        </div>

                    </div>

                    <!-- ============================================================== -->
                    <!-- SEKSI 4: JADWAL TAYANG                                         -->
                    <!-- ============================================================== -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">4</span>
                                <span>Jadwal Tayang</span>
                            </span>
                        </div>

                        <!-- Dua Kolom: Kiri (Frekuensi & Chips Tanggal), Kanan (Kalender Kompak) -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-stretch">
                            
                            <!-- Kolom Kiri: Frekuensi Stepper & Chips Tanggal Terpilih -->
                            <div class="md:col-span-5 flex flex-col justify-between space-y-3">
                                
                                <!-- Stepper Frekuensi Tayang -->
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 mb-1.5 block">
                                        Frekuensi <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center bg-white border border-slate-300 rounded-xl p-1 shadow-2xs flex-1">
                                            <button type="button" onclick="stepJumlahTayang(-1)"
                                                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition cursor-pointer select-none">
                                                &minus;
                                            </button>
                                            <input type="text" inputmode="numeric" id="in_qty" name="total_muatiklanonline" 
                                                   value="{{ old('total_muatiklanonline', isset($transaksi) ? $transaksi->total_muatiklanonline : '1') }}" min="1" required 
                                                   oninput="handleJumlahMuatChange(this.value)"
                                                   class="w-full h-8 px-1 text-center font-bold text-sm text-slate-900 border-0 focus:ring-0 focus:outline-none">
                                            <button type="button" onclick="stepJumlahTayang(1)"
                                                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition cursor-pointer select-none">
                                                &#43;
                                            </button>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-700 whitespace-nowrap">Hari</span>
                                    </div>
                                </div>

                                <!-- Box Daftar Tanggal Terpilih -->
                                <div class="flex-1 flex flex-col p-3.5 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-2">
                                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/60 whitespace-nowrap gap-1.5">
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <span class="text-sm font-semibold text-slate-700 whitespace-nowrap">Tanggal Dipilih:</span>
                                            <span id="labelTotalDatesCount" class="px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-100 text-kp-blue-800 whitespace-nowrap">1 Tanggal</span>
                                        </div>
                                        <button type="button" onclick="resetSemuaTanggal()"
                                                class="p-1 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer flex items-center justify-center shrink-0"
                                                title="Reset semua tanggal">
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
                                    <span class="font-semibold text-sm text-slate-700">Pilih Tanggal:</span>
                                </div>

                                <!-- Container Kalender Kompak -->
                                <div class="p-3 bg-white border border-slate-200/90 rounded-xl space-y-2 shadow-2xs">
                                    <!-- Header Kalender: Bulan Tahun & Navigasi Panah Kompak -->
                                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                                        <div class="flex items-center gap-2">
                                            <span id="calendarMonthTitle" class="text-sm font-bold text-slate-900"></span>
                                            <button type="button" onclick="goToTodayCalendar()"
                                                    class="text-xs font-semibold text-kp-blue-600 hover:text-kp-blue-800 cursor-pointer underline">
                                                Hari Ini
                                            </button>
                                        </div>
                                        <!-- Navigasi Panah < > -->
                                        <div class="flex items-center gap-1">
                                            <button type="button" onclick="changeCalendarMonth(-1)"
                                                    class="w-6 h-6 rounded hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer"
                                                    title="Bulan Sebelumnya">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                            </button>
                                            <button type="button" onclick="changeCalendarMonth(1)"
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
                                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-600 inline-block shadow-2xs"></span> Tanggal Tayang</span>
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

                    <!-- ============================================================== -->
                    <!-- SEKSI 5: STATUS PENGERJAAN & BUKTI TAYANG                      -->
                    <!-- ============================================================== -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">5</span>
                                <span>Status Produksi &amp; Bukti Tayang</span>
                            </span>
                        </div>

                        <!-- 1. STATUS PENGERJAAN -->
                        <div>
                            <label class="text-sm font-semibold text-slate-700 mb-2 block">
                                Status Alur Pengerjaan <span class="text-rose-500">*</span>
                            </label>

                            @php
                                $currStatusProduksi = old('status_produksi', isset($transaksi) ? $transaksi->status_produksi : 'menunggu_bahan');
                                $statusOptions = [
                                    ['id' => 'menunggu_bahan', 'label' => 'Menunggu Bahan'],
                                    ['id' => 'antrean_redaksi', 'label' => 'Antrean Redaksi'],
                                    ['id' => 'tayang', 'label' => 'Tayang (Live)'],
                                    ['id' => 'selesai', 'label' => 'Selesai (Archived)'],
                                ];
                            @endphp

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @foreach($statusOptions as $st)
                                    @php $isStActive = ($currStatusProduksi === $st['id']); @endphp
                                    <label class="status-prod-card flex items-center justify-center py-2.5 px-3 rounded-xl border-2 transition cursor-pointer select-none text-center {{ $isStActive ? 'border-kp-blue-600 bg-sky-50 text-kp-blue-950 font-bold shadow-2xs' : 'border-slate-200 bg-white text-slate-700 hover:border-kp-blue-300 hover:bg-slate-50 font-medium' }}">
                                        <input type="radio" name="status_produksi" value="{{ $st['id'] }}" {{ $isStActive ? 'checked' : '' }} class="sr-only" onchange="toggleStatusProduksi(this)">
                                        <span class="text-xs whitespace-normal leading-tight">{{ $st['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- 2. BUKTI TAYANG (OPSIONAL) -->
                        <div class="pt-3 border-t border-slate-100 space-y-3">
                            <label class="text-sm font-semibold text-slate-700 block">
                                Tautan Bukti Publikasi <span class="text-xs text-slate-400 font-normal">(Bisa dilengkapi sekarang atau setelah live)</span>
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                        Link Berita Portal
                                    </label>
                                    <input type="url" name="url_berita_tayang" id="url_berita_tayang"
                                           value="{{ old('url_berita_tayang', isset($transaksi) ? $transaksi->url_berita_tayang : '') }}"
                                           placeholder="https://kabar-priangan.com/..."
                                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                        Link Medsos (IG / TikTok / YouTube)
                                    </label>
                                    <input type="url" name="url_medsos_tayang" id="url_medsos_tayang"
                                           value="{{ old('url_medsos_tayang', isset($transaksi) ? $transaksi->url_medsos_tayang : '') }}"
                                           placeholder="https://instagram.com/..."
                                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- ============================================================== -->
                <!-- PANEL KANAN: RINGKASAN ORDER & RINCIAN KASIR                   -->
                <!-- ============================================================== -->
                <div class="lg:col-span-5 space-y-5 lg:sticky lg:top-6">                    <!-- 1. RINGKASAN ORDER -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                            <span class="text-sm font-bold text-slate-900">Ringkasan Order</span>
                        </div>

                        <!-- Miniatur Bersih -->
                        <div class="border border-slate-200 rounded-xl p-3 bg-slate-50/60 space-y-2 text-xs">
                            <div class="flex justify-between items-center pb-2 border-b border-slate-200 text-xs">
                                <span class="font-bold text-slate-800">Iklan Online</span>
                                <span id="prev_status_badge" class="px-2 py-0.5 rounded text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-300">
                                    Menunggu Bahan
                                </span>
                            </div>

                            <div class="space-y-1.5 text-slate-600 text-xs">
                                <div class="flex">
                                    <span class="w-20 font-medium text-slate-500 shrink-0">Pemesan</span>
                                    <span class="w-2">:</span>
                                    <span id="prev_instansi" class="font-medium text-slate-800 truncate">Belum diisi</span>
                                </div>
                                <div class="flex">
                                    <span class="w-20 font-medium text-slate-500 shrink-0">Produk</span>
                                    <span class="w-2">:</span>
                                    <span id="prev_produk" class="font-semibold text-kp-blue-700 truncate">{{ $initialProduct?->nama_jenis ?? 'Belum ada produk dipilih' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-20 font-medium text-slate-500 shrink-0">Skema</span>
                                    <span class="w-2">:</span>
                                    <span id="prev_skema_badge" class="px-1.5 py-0.2 rounded text-xs font-semibold bg-sky-100 text-sky-800">1x Tayang</span>
                                </div>
                                <div class="flex">
                                    <span class="w-20 font-medium text-slate-500 shrink-0">Frekuensi</span>
                                    <span class="w-2">:</span>
                                    <span id="prev_qty" class="font-medium text-slate-800">1 Hari</span>
                                </div>
                                <div class="flex">
                                    <span class="w-20 font-medium text-slate-500 shrink-0">Tgl Tayang</span>
                                    <span class="w-2">:</span>
                                    <span id="prev_tgl" class="font-mono text-slate-700">{{ date('d M Y') }}</span>
                                </div>
                            </div>

                            <!-- Pilihan Saluran / Portal Tags -->
                            <div class="pt-2 border-t border-slate-200 space-y-1">
                                <span class="text-xs font-semibold text-slate-500">Penempatan:</span>
                                <div id="prev_platform_tags" class="flex flex-wrap gap-1 mt-0.5">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-xs font-medium">Belum ada platform dipilih</span>
                                </div>
                            </div>

                            <div class="pt-1.5 border-t border-slate-200 text-xs text-slate-500 italic truncate" id="prev_materi">
                                Materi: Belum diisi
                            </div>
                        </div>
                    </div>

                    <!-- 2. RINCIAN BIAYA & PEMBAYARAN -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900">Rincian Biaya Transaksi</span>
                            <span id="badge_status_bayar" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                Menunggu Pembayaran
                            </span>
                        </div>

                        @php
                            $currentSkema = old('skema_pembayaran', isset($transaksi) ? $transaksi->skema_pembayaran : 'sekali_tayang');
                        @endphp
                        <!-- Pilihan Skema Tarif -->
                        <div>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="skema-card flex items-center justify-center p-2 rounded-xl border-2 transition cursor-pointer select-none text-center {{ $currentSkema === 'sekali_tayang' ? 'bg-sky-50 border-kp-blue-600 text-kp-blue-950 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-700 hover:border-kp-blue-300 hover:bg-slate-50 font-medium' }}">
                                    <input type="radio" name="skema_pembayaran" value="sekali_tayang" {{ $currentSkema === 'sekali_tayang' ? 'checked' : '' }} class="sr-only" onchange="toggleSkema(this)">
                                    <span class="text-xs font-semibold">Per Tayang</span>
                                </label>

                                <label class="skema-card flex items-center justify-center p-2 rounded-xl border-2 transition cursor-pointer select-none text-center {{ $currentSkema === 'paket' ? 'bg-sky-50 border-kp-blue-600 text-kp-blue-950 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-700 hover:border-kp-blue-300 hover:bg-slate-50 font-medium' }}">
                                    <input type="radio" name="skema_pembayaran" value="paket" {{ $currentSkema === 'paket' ? 'checked' : '' }} class="sr-only" onchange="toggleSkema(this)">
                                    <span class="text-xs font-semibold">Paket</span>
                                </label>
                            </div>
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
                                                <input type="text" id="in_harga" name="harga_transaksionline" 
                                                       value="{{ number_format(old('harga_transaksionline', isset($transaksi) ? $transaksi->harga_transaksionline : ($initialProduct?->tarif_dasar ?? 0)), 0, ',', '.') }}" 
                                                       required onkeyup="formatInput(this)" oninput="hitungSemua()"
                                                       style="padding-left: 2rem !important;"
                                                       class="w-full h-8 px-2 text-right bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs format-rupiah">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-1 text-slate-600 font-medium whitespace-nowrap">Format / Produk</td>
                                        <td class="py-1 text-center font-bold text-slate-400">:</td>
                                        <td class="py-1 text-right font-normal text-slate-700 truncate" id="strukDimensi">-</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1 text-slate-600 font-medium whitespace-nowrap">Frekuensi</td>
                                        <td class="py-1 text-center font-bold text-slate-400">:</td>
                                        <td class="py-1 text-right font-normal text-slate-700" id="strukFrekuensi">1 Kali Tayang</td>
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
                                                               value="{{ old('diskon_persen', isset($transaksi) && $transaksi->harga_transaksionline > 0 && $transaksi->diskon_transaksionline > 0 ? round(($transaksi->diskon_transaksionline / ($transaksi->harga_transaksionline * ($transaksi->total_muatiklanonline ?: 1))) * 100, 1) : 0) }}" 
                                                               step="0.5" min="0" max="100"
                                                               placeholder="0"
                                                               oninput="handleDiskonPersenChange(this.value)"
                                                               class="w-full h-10 px-2.5 pr-6 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                                        <span class="absolute right-2 top-2.5 text-xs text-slate-400 font-bold pointer-events-none">%</span>
                                                    </div>
                                                    <div class="col-span-3 relative">
                                                        <span class="absolute left-2.5 top-2.5 text-xs text-slate-400 font-bold pointer-events-none">Rp</span>
                                                        <input type="text" id="in_diskon" name="diskon_transaksionline" 
                                                               value="{{ number_format(old('diskon_transaksionline', isset($transaksi) ? $transaksi->diskon_transaksionline : 0), 0, ',', '.') }}" 
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
                                                    <button type="button" onclick="setBayarLunasOnline()" class="text-xs font-semibold text-kp-blue-600 hover:underline cursor-pointer">
                                                        Bayar Lunas
                                                    </button>
                                                </div>
                                                <input type="text" inputmode="numeric" name="jumlahbayar_transaksionline" id="in_bayar" 
                                                       value="{{ old('jumlahbayar_transaksionline') ? number_format((int) str_replace(['.', ','], '', old('jumlahbayar_transaksionline')), 0, ',', '.') : (isset($transaksi) && $transaksi->jumlahbayar_transaksionline ? number_format($transaksi->jumlahbayar_transaksionline, 0, ',', '.') : '0') }}"
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
                            <button type="button" onclick="konfirmasiSimpanTransaksi()" id="btnSimpanOrder"
                                    class="w-full h-12 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer tracking-wide">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                <span>{{ isset($transaksi) ? 'Simpan Perubahan Order Online' : 'Simpan Transaksi Online' }}</span>
                            </button>
                            <a href="{{ route('transaksionline.index') }}" 
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
        <!-- MODAL KONFIRMASI SIMPAN TRANSAKSI                              -->
        <!-- ============================================================== -->
        <div id="modalKonfirmasiSimpan" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all animate-in fade-in zoom-in-95 duration-150">
                <div class="p-6 text-center space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center mx-auto shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            {{ isset($transaksi) ? 'Konfirmasi Perubahan Order' : 'Konfirmasi Simpan Transaksi Online' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Pastikan data pemesan, pilihan platform portal/medsos, materi iklan, dan jadwal penayangan telah sesuai sebelum menyimpan transaksi ini.
                        </p>
                    </div>

                    <!-- Ringkasan Singkat -->
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 text-left text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nama Pemesan:</span>
                            <span id="konfirmasiNamaPemesan" class="font-bold text-slate-900 truncate max-w-[200px]">-</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Produk / Paket:</span>
                            <span id="konfirmasiProdukNama" class="font-semibold text-slate-800 truncate max-w-[200px]">-</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Jadwal Tayang:</span>
                            <span id="konfirmasiJadwalTayang" class="font-medium text-slate-800">-</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-200/80 pt-2">
                            <span class="font-medium text-slate-700">Total Tagihan:</span>
                            <span id="konfirmasiTotalBiaya" class="font-mono font-bold text-sm text-kp-blue-700">-</span>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="grid grid-cols-2 gap-2.5 pt-2">
                        <button type="button" onclick="closeModalKonfirmasiSimpan()"
                                class="w-full py-2.5 px-4 bg-white hover:bg-slate-100 active:bg-slate-200 border border-slate-300 text-slate-700 font-medium text-xs rounded-xl transition shadow-2xs cursor-pointer">
                            Periksa Kembali
                        </button>
                        <button type="button" onclick="eksekusiSubmitTransaksi()" id="btnSubmitFinal"
                                class="w-full py-2.5 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
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
    <!-- JAVASCRIPT ENGINE LENGKAP TRANSAKSI ONLINE                     -->
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
                'total_piutang_koran' => $p->total_piutang_koran ?? 0,
            ];
        })->values()) !!};

        let searchTimeout = null;
        const configPpnPersen = {{ $ppnPersen ?? 12 }};

        // State Kalender Nasional & Tanggal Terpilih (Persis Koran)
        let selectedDates = {!! json_encode(array_values(array_filter(explode(',', old('tanggal_muat_list', isset($transaksi) ? ($transaksi->tanggal_muat_list ?: ($transaksi->tanggal_muatiklanonline?->format('Y-m-d') ?: date('Y-m-d'))) : date('Y-m-d')))))) !!};
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
        const dayNamesShortIndo = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

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
                cardTunai.className = "px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-emerald-50/70 border-emerald-500 text-emerald-950 font-semibold shadow-2xs";
                iconTunai.className = "w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0";
                dotTunai.className = "text-emerald-600 flex items-center";

                cardKredit.className = "px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-white border-slate-200 text-slate-600 hover:border-slate-300 font-medium";
                iconKredit.className = "w-6 h-6 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0";
                dotKredit.className = "text-transparent flex items-center";
            } else {
                cardKredit.className = "px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-amber-50/70 border-amber-500 text-amber-950 font-semibold shadow-2xs";
                iconKredit.className = "w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0";
                dotKredit.className = "text-amber-600 flex items-center";

                cardTunai.className = "px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-white border-slate-200 text-slate-600 hover:border-slate-300 font-medium";
                iconTunai.className = "w-6 h-6 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center shrink-0";
                dotTunai.className = "text-transparent flex items-center";
            }
        }

        // --- TYPEAHEAD SEARCH PELANGGAN ---
        function handleSearchPelangganFocus(forceOpen = false) {
            const input = document.getElementById('inputSearchPelanggan');
            const query = input.value.trim();
            if (forceOpen || query.length > 0 || window.currentPelangganList.length > 0) {
                renderDropdownPelanggan(filterPelangganList(query), query);
                document.getElementById('dropdownHasilPelanggan').classList.remove('hidden');
            }
        }

        function handleSearchPelangganInput(query) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const filtered = filterPelangganList(query.trim());
                renderDropdownPelanggan(filtered, query.trim());
                document.getElementById('dropdownHasilPelanggan').classList.remove('hidden');
            }, 100);
        }

        function handleSearchPelangganKeydown(event) {
            if (event.key === 'Escape') {
                document.getElementById('dropdownHasilPelanggan').classList.add('hidden');
            }
        }

        function filterPelangganList(query) {
            if (!query) return window.currentPelangganList.slice(0, 8);
            const q = query.toLowerCase();
            return window.currentPelangganList.filter(item => {
                return (item.nama_instansi && item.nama_instansi.toLowerCase().includes(q)) ||
                       (item.kode_pelanggan && item.kode_pelanggan.toLowerCase().includes(q)) ||
                       (item.no_telp && item.no_telp.toLowerCase().includes(q));
            }).slice(0, 10);
        }

        function renderDropdownPelanggan(list, query) {
            const container = document.getElementById('listPelangganItem');
            if (!container) return;

            if (!list || list.length === 0) {
                container.innerHTML = `
                    <div class="p-3 text-center space-y-2">
                        <p class="text-xs text-slate-500">Tidak ada pelanggan terdaftar yang cocok.</p>
                        ${query ? `
                            <button type="button" onclick="pilihSebagaiManual('${query.replace(/'/g, "\\'")}')"
                                    class="w-full px-3 py-2 bg-kp-blue-50 hover:bg-kp-blue-100 text-kp-blue-700 text-xs font-semibold rounded-xl border border-kp-blue-200 transition cursor-pointer flex items-center justify-center gap-1.5">
                                <span>Gunakan "${query}" sebagai Pemesan Manual</span>
                            </button>
                        ` : ''}
                    </div>`;
                return;
            }

            let html = list.map((item) => {
                return `
                    <button type="button" onclick="selectPelangganItemById(${item.id})"
                            class="w-full text-left p-2.5 rounded-xl hover:bg-kp-blue-50/70 transition flex items-center justify-between gap-3 group cursor-pointer border border-transparent hover:border-kp-blue-200">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-semibold text-xs text-slate-900 group-hover:text-kp-blue-700">${item.nama_instansi}</span>
                                <span class="px-1.5 py-0.5 text-xs font-semibold rounded bg-slate-100 text-slate-700 border border-slate-200 uppercase">${item.jenis_klien || 'Umum'}</span>
                                <span class="text-xs font-mono text-slate-400 font-medium">[${item.kode_pelanggan || 'PLG'}]</span>
                            </div>
                            <div class="text-xs text-slate-500 truncate mt-0.5 flex items-center gap-2">
                                ${item.no_telp ? `<span>${item.no_telp}</span>` : ''}
                                ${item.alamat ? `<span class="truncate max-w-[200px]">| ${item.alamat}</span>` : ''}
                            </div>
                        </div>
                        <span class="text-xs text-kp-blue-600 font-semibold group-hover:underline shrink-0">Pilih ↵</span>
                    </button>
                `;
            }).join('');

            container.innerHTML = html;
        }

        function selectPelangganItemById(id) {
            const item = window.currentPelangganList.find(p => p.id == id);
            if (!item) return;

            document.getElementById('pelanggan_id').value = item.id;
            document.getElementById('nama_pemasangonline').value = item.nama_instansi || '';
            document.getElementById('inputManualNamaPemasang').value = item.nama_instansi || '';

            if (item.alamat) document.getElementById('alamat_pemasangonline').value = item.alamat;
            if (item.no_telp) document.getElementById('no_kontak_pemasang').value = item.no_telp;
            if (item.kontak_pic) document.getElementById('nama_pic').value = item.kontak_pic;

            // Update Card Pelanggan Terpilih
            document.getElementById('labelNamaPelanggan').textContent = item.nama_instansi || '';
            document.getElementById('badgeJenisPelanggan').textContent = item.jenis_klien || 'Umum';
            document.getElementById('labelKodePelanggan').textContent = `[${item.kode_pelanggan || 'PLG'}]`;

            const npwpEl = document.getElementById('labelNpwpPelanggan');
            const textNpwp = document.getElementById('textNpwpPelanggan');
            if (npwpEl && textNpwp) {
                if (item.npwp_nik) {
                    textNpwp.textContent = item.npwp_nik;
                    npwpEl.classList.remove('hidden');
                } else {
                    npwpEl.classList.add('hidden');
                }
            }

            const emailEl = document.getElementById('labelEmailPelanggan');
            const textEmail = document.getElementById('textEmailPelanggan');
            if (emailEl && textEmail) {
                if (item.email) {
                    textEmail.textContent = item.email;
                    emailEl.classList.remove('hidden');
                } else {
                    emailEl.classList.add('hidden');
                }
            }

            const telpEl = document.getElementById('labelTelpPelanggan');
            const textTelp = document.getElementById('textTelpPelanggan');
            if (telpEl && textTelp) {
                if (item.no_telp) {
                    textTelp.textContent = item.no_telp;
                    telpEl.classList.remove('hidden');
                } else {
                    telpEl.classList.add('hidden');
                }
            }

            const alamatEl = document.getElementById('labelAlamatPelanggan');
            const textAlamat = document.getElementById('textAlamatPelanggan');
            if (alamatEl && textAlamat) {
                if (item.alamat) {
                    textAlamat.textContent = item.alamat;
                    alamatEl.classList.remove('hidden');
                } else {
                    alamatEl.classList.add('hidden');
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

            const hiddenNama = document.getElementById('nama_pemasangonline');
            if (hiddenNama) hiddenNama.value = '';
            const manualInput = document.getElementById('inputManualNamaPemasang');
            if (manualInput) manualInput.value = '';
            const telpInput = document.getElementById('no_kontak_pemasang');
            if (telpInput) telpInput.value = '';
            const alamatInput = document.getElementById('alamat_pemasangonline');
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
            document.getElementById('nama_pemasangonline').value = nama;
            document.getElementById('inputManualNamaPemasang').value = nama;
            document.getElementById('dropdownHasilPelanggan').classList.add('hidden');
            updateSimulatorLive();
        }

        function handleManualNamaInput(val) {
            document.getElementById('nama_pemasangonline').value = val;
            document.getElementById('pelanggan_id').value = '';
            updateSimulatorLive();

            const cardSelected = document.getElementById('cardPelangganTerpilih');
            const contSearch = document.getElementById('containerSearchPelanggan');
            if (cardSelected && !cardSelected.classList.contains('hidden') && val.trim().length > 0) {
                cardSelected.classList.add('hidden');
                if (contSearch) contSearch.classList.remove('hidden');
            }
        }

        // --- PLATFORM TAB SWITCHING (SELARAS FOTO 1) ---
        function switchPlatformTab(tabKey, tabName) {
            document.getElementById('kategori_form').value = tabKey;

            const platNames = {
                'PORTAL_BERITA': 'Portal Berita',
                'INSTAGRAM':     'Instagram',
                'TIKTOK':        'TikTok',
                'YOUTUBE':       'YouTube',
                'PODCAST':       'Podcast'
            };

            const hiddenPlat = document.getElementById('platforms_publikasi_hidden');
            if (hiddenPlat) {
                hiddenPlat.value = platNames[tabKey] || tabName;
            }

            // Update tab button UI (Selaras Transaksi Koran & Foto 2)
            document.querySelectorAll('.tab-platform-btn').forEach(btn => {
                btn.className = "tab-platform-btn px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 bg-white text-slate-700 font-medium border border-slate-200 hover:border-slate-300 hover:bg-slate-50";
                const badge = btn.querySelector('span:nth-child(2)');
                if (badge) badge.className = "px-1.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600";
            });
            const activeBtn = document.getElementById('tabBtn_' + tabKey);
            if (activeBtn) {
                activeBtn.className = "tab-platform-btn px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 bg-kp-blue-600 text-white font-semibold shadow-xs border border-kp-blue-600";
                const badge = activeBtn.querySelector('span:nth-child(2)');
                if (badge) badge.className = "px-1.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white";
            }

            // Switch content panels (tanpa mereset pilihan yang sudah terseleksi di tab lain)
            document.querySelectorAll('.tab-platform-content').forEach(panel => {
                panel.classList.add('hidden');
            });
            const activePanel = document.getElementById('tabContent_' + tabKey);
            if (activePanel) {
                activePanel.classList.remove('hidden');
            }

            updateSimulatorLive();
        }

        // --- MULTI-PRODUCT STATE ENGINE (SELARAS ALUR BISNIS MULTI-PLATFORM) ---
        let selectedMedsosProducts = {};
        const PORTAL_BASE_TARIF = 750000;
        const PORTAL_PRODUCT_ID = 9;

        // Toggle produk media sosial (Instagram, TikTok, YouTube, Podcast)
        function toggleProdukIklanOnline(id, kode, nama, tarif, tabKey) {
            const targetCard = document.getElementById('cardProduk_' + id);
            const badge = targetCard ? targetCard.querySelector('.badge-corner-check') : null;

            if (selectedMedsosProducts[id]) {
                delete selectedMedsosProducts[id];
                if (targetCard) {
                    targetCard.className = "produk-card text-left p-3.5 rounded-xl border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50 transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2.5 min-h-[92px]";
                }
                if (badge) badge.classList.add('hidden');
            } else {
                selectedMedsosProducts[id] = { id: id, kode: kode, nama: nama, tarif: Number(tarif), tabKey: tabKey };
                if (targetCard) {
                    targetCard.className = "produk-card text-left p-3.5 rounded-xl border-2 border-kp-blue-600 bg-sky-50/20 ring-1 ring-kp-blue-500/20 shadow-xs transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2.5 min-h-[92px]";
                }
                if (badge) badge.classList.remove('hidden');
            }

            recalculateOnlineSelection();
        }

        // 9 Portal Checkbox Handler (Dengan Ceklist Lancip Foto 2 & Tarif Perkalian Portal)
        function handlePortalCheckboxChange() {
            document.querySelectorAll('.portal-checkbox-card').forEach(card => {
                const cb = card.querySelector('.portal-checkbox');
                const badge = card.querySelector('.badge-corner-check');
                if (cb && cb.checked) {
                    card.className = "portal-checkbox-card relative overflow-hidden flex flex-col justify-between p-3 rounded-xl border-2 border-kp-blue-600 bg-sky-50 text-kp-blue-900 font-semibold ring-1 ring-kp-blue-500/20 shadow-2xs transition cursor-pointer select-none min-h-[78px]";
                    if (badge) badge.classList.remove('hidden');
                } else {
                    card.className = "portal-checkbox-card relative overflow-hidden flex flex-col justify-between p-3 rounded-xl border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 font-medium transition cursor-pointer select-none min-h-[78px]";
                    if (badge) badge.classList.add('hidden');
                }
            });

            recalculateOnlineSelection();
        }

        function pilihSemuaPortal(checked) {
            document.querySelectorAll('.portal-checkbox').forEach(cb => {
                cb.checked = checked;
            });
            handlePortalCheckboxChange();
        }

        // Kalkulasi Total Multi-Pilihan: Portal (x tarif) + Semua Produk Medsos Terpilih
        function recalculateOnlineSelection() {
            const checkedPortals = Array.from(document.querySelectorAll('.portal-checkbox:checked')).map(cb => cb.value);
            const portalCount = checkedPortals.length;
            const portalCost = portalCount * PORTAL_BASE_TARIF;

            const medsosItems = Object.values(selectedMedsosProducts);
            let medsosCost = 0;
            medsosItems.forEach(item => {
                medsosCost += Number(item.tarif);
            });

            const grandTarif = portalCost + medsosCost;

            // Update input tarif iklan
            const inputHarga = document.getElementById('in_harga');
            if (inputHarga) {
                inputHarga.value = formatRupiah(grandTarif);
            }

            // Update hidden container produk IDs
            const contProd = document.getElementById('containerSelectedProductIds');
            if (contProd) {
                contProd.innerHTML = '';
                medsosItems.forEach(item => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'selected_produk_ids[]';
                    inp.value = item.id;
                    contProd.appendChild(inp);
                });
                if (portalCount > 0) {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'selected_produk_ids[]';
                    inp.value = PORTAL_PRODUCT_ID;
                    contProd.appendChild(inp);
                }
            }

            const idInput = document.getElementById('id_iklanonline');
            if (idInput) {
                idInput.value = medsosItems.length > 0 ? medsosItems[0].id : (portalCount > 0 ? PORTAL_PRODUCT_ID : '');
            }

            // Update hidden container platforms publikasi
            const contPlat = document.getElementById('containerPlatformsPublikasi');
            if (contPlat) {
                contPlat.innerHTML = '';
                const activePlatforms = new Set();
                if (portalCount > 0) activePlatforms.add('Portal Berita');
                medsosItems.forEach(item => {
                    if (item.tabKey === 'INSTAGRAM') activePlatforms.add('Instagram');
                    if (item.tabKey === 'TIKTOK') activePlatforms.add('TikTok');
                    if (item.tabKey === 'YOUTUBE') activePlatforms.add('YouTube');
                    if (item.tabKey === 'PODCAST') activePlatforms.add('Podcast');
                });

                activePlatforms.forEach(plat => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'platforms_publikasi[]';
                    inp.value = plat;
                    contPlat.appendChild(inp);
                });
            }

            // Format ringkasan nama produk jasa terpilih
            const parts = [];
            if (portalCount > 0) {
                parts.push(portalCount === 1 ? `Portal Berita (${checkedPortals[0]})` : `Portal Berita (${portalCount} Portal Network)`);
            }
            medsosItems.forEach(item => {
                parts.push(item.nama);
            });

            const summaryText = parts.length > 0 ? parts.join(' + ') : 'Belum ada produk dipilih';
            const inProdukJasa = document.getElementById('in_produk_jasa');
            if (inProdukJasa) inProdukJasa.value = summaryText;

            const prevProduk = document.getElementById('prev_produk');
            if (prevProduk) prevProduk.innerText = summaryText;

            const elDimensi = document.getElementById('strukDimensi');
            if (elDimensi) {
                elDimensi.innerText = summaryText !== 'Belum ada produk dipilih' ? summaryText : '-';
                elDimensi.title = summaryText;
            }

            // Update status paket / frekuensi badge
            const totalUnits = portalCount + medsosItems.length;
            const badgePaket = document.getElementById('badge_status_paket');
            const prevSkemaBadge = document.getElementById('prev_skema_badge');
            if (totalUnits > 1) {
                if (badgePaket) {
                    badgePaket.innerText = `Multi-Platform (${totalUnits} Item)`;
                    badgePaket.className = "px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300";
                }
                if (prevSkemaBadge) {
                    prevSkemaBadge.innerText = `Multi (${totalUnits})`;
                    prevSkemaBadge.className = "px-1.5 py-0.2 rounded text-xs font-semibold bg-emerald-100 text-emerald-800";
                }
            } else if (totalUnits === 1) {
                if (badgePaket) {
                    badgePaket.innerText = "1x Tayang";
                    badgePaket.className = "px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-100 text-kp-blue-800 border border-kp-blue-200";
                }
                if (prevSkemaBadge) {
                    prevSkemaBadge.innerText = "1x Tayang";
                    prevSkemaBadge.className = "px-1.5 py-0.2 rounded text-xs font-semibold bg-sky-100 text-sky-800";
                }
            } else {
                if (badgePaket) {
                    badgePaket.innerText = "Belum Dipilih";
                    badgePaket.className = "px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200";
                }
                if (prevSkemaBadge) {
                    prevSkemaBadge.innerText = "-";
                    prevSkemaBadge.className = "px-1.5 py-0.2 rounded text-xs font-medium bg-slate-100 text-slate-600";
                }
            }

            hitungSemua();
            updateSimulatorLive();
        }


        function handleFileMateriChange(input) {
            const label = document.getElementById('labelFileMateriNama');
            if (input.files && input.files[0]) {
                label.textContent = input.files[0].name;
            } else {
                label.textContent = 'Pilih file atau tarik ke sini';
            }
        }

        // --- ENGINE KALENDER NASIONAL (PERSIS TRANSAKSI KORAN) ---
        function goToTodayCalendar() {
            const today = new Date();
            currentCalYear = today.getFullYear();
            currentCalMonth = today.getMonth();
            fetchKalenderBulan(currentCalYear, currentCalMonth + 1, () => {
                renderCalendar();
            });
        }

        function changeCalendarMonth(delta) {
            currentCalMonth += delta;
            if (currentCalMonth < 0) {
                currentCalMonth = 11;
                currentCalYear -= 1;
            } else if (currentCalMonth > 11) {
                currentCalMonth = 0;
                currentCalYear += 1;
            }

            fetchKalenderBulan(currentCalYear, currentCalMonth + 1, () => {
                renderCalendar();
            });
        }

        function fetchKalenderBulan(year, month, callback) {
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
            if (typeof entry === 'object') return entry.nama || entry.keterangan || entry.summary || null;
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

        function renderCalendar() {
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
                btn.onclick = () => onCalendarCellClick(dateStr, isPast);
                grid.appendChild(btn);
            }
        }

        function onCalendarCellClick(dateStr, isPast) {
            activeDetailDate = dateStr;
            updatePanelInfoTanggal(dateStr);

            if (isPast) {
                renderCalendar();
                return;
            }

            const totalMuatInput = document.getElementById('in_qty');
            const targetQuota = parseInt(totalMuatInput ? totalMuatInput.value : 1) || 1;
            const index = selectedDates.indexOf(dateStr);

            if (index > -1) {
                selectedDates.splice(index, 1);
                selectedDates.sort();
                syncTanggalMuat();
                updatePanelInfoTanggal(dateStr);
            } else {
                if (selectedDates.length >= targetQuota) {
                    alert(`Seluruh ${targetQuota} tanggal tayang telah terisi. Tambah angka pada frekuensi tayang jika ingin memilih tanggal lagi.`);
                    renderCalendar();
                    return;
                }
                selectedDates.push(dateStr);
                selectedDates.sort();
                syncTanggalMuat();
                updatePanelInfoTanggal(dateStr);
            }
        }

        function updatePanelInfoTanggal(dateStr) {
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
                    Terjadwal Tayang
                </span>` : '';

            let badgesHtml = '';
            if (liburDesc && peringatanDesc) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-rose-50/80 border-rose-200 text-rose-900 space-y-1.5";
                badgesHtml = `<span class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-md bg-white border border-rose-200 text-rose-700 shadow-2xs">Libur: ${liburDesc}</span> <span class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-md bg-white border border-amber-200 text-amber-800 shadow-2xs">Peringatan: ${peringatanDesc}</span> ${selectedTagHtml}`;
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
                badgesHtml = `<span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-sky-200 text-sky-800 shadow-2xs">Hari Kerja Aktif</span> ${selectedTagHtml}`;
            }

            badgeContainer.innerHTML = badgesHtml;
        }

        function renderSelectedDatesChips() {
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
                        <div class="flex items-center gap-2 cursor-pointer select-none whitespace-nowrap min-w-0" onclick="inspectSelectedDate('${d}')">
                            <span class="text-xs font-bold text-slate-500 font-mono shrink-0">${i+1}.</span>
                            <span class="text-xs font-bold text-slate-800 group-hover:text-kp-blue-700 transition whitespace-nowrap">${formatIndoChip(d)}</span>
                        </div>
                        <button type="button" onclick="removeSingleDate('${d}')"
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

        function inspectSelectedDate(dateStr) {
            activeDetailDate = dateStr;
            const parts = dateStr.split('-');
            currentCalYear = parseInt(parts[0]);
            currentCalMonth = parseInt(parts[1]) - 1;
            fetchKalenderBulan(currentCalYear, currentCalMonth + 1, () => {
                renderCalendar();
                renderSelectedDatesChips();
                updatePanelInfoTanggal(dateStr);
            });
        }

        function removeSingleDate(dateStr) {
            selectedDates = selectedDates.filter(d => d !== dateStr);
            syncTanggalMuat();
            if (activeDetailDate === dateStr) {
                updatePanelInfoTanggal(dateStr);
            }
        }

        function resetSemuaTanggal() {
            selectedDates = [];
            syncTanggalMuat();
        }

        function syncTanggalMuat() {
            const hiddenPrimary = document.getElementById('tanggal_muatiklanonline');
            const hiddenList = document.getElementById('tanggal_muat_list');

            if (selectedDates.length > 0) {
                if (hiddenPrimary) hiddenPrimary.value = selectedDates[0];
                if (hiddenList) hiddenList.value = selectedDates.join(',');
            } else {
                if (hiddenPrimary) hiddenPrimary.value = '';
                if (hiddenList) hiddenList.value = '';
            }

            renderCalendar();
            renderSelectedDatesChips();
            updateSimulatorLive();
        }

        function stepJumlahTayang(delta) {
            const input = document.getElementById('in_qty');
            if (!input) return;
            let val = parseInt(input.value) || 1;
            val = Math.max(1, val + delta);
            input.value = val;
            handleJumlahMuatChange(val);
        }

        function handleJumlahMuatChange(val) {
            const parsed = parseInt(val) || 1;
            renderSelectedDatesChips();
            hitungSemua();
        }

        // --- SKEMA & KASIR FORMULIR ---
        function toggleSkema(radio) {
            document.querySelectorAll('.skema-card').forEach(card => {
                card.classList.remove('bg-sky-50', 'border-kp-blue-600', 'text-kp-blue-950', 'font-bold', 'shadow-2xs');
                card.classList.add('bg-white', 'border-slate-200', 'text-slate-700', 'font-medium');
            });
            const activeCard = radio.closest('.skema-card');
            if (activeCard) {
                activeCard.classList.remove('bg-white', 'border-slate-200', 'text-slate-700', 'font-medium');
                activeCard.classList.add('bg-sky-50', 'border-kp-blue-600', 'text-kp-blue-950', 'font-bold', 'shadow-2xs');
            }

            const lblTitle = document.getElementById('lbl_tarif_title');
            if (radio.value === 'paket') {
                if (lblTitle) lblTitle.innerHTML = 'Tarif Total Paket Iklan <span class="text-rose-500">*</span>';
            } else {
                if (lblTitle) lblTitle.innerHTML = 'Tarif Iklan (Per Tayang / Hari) <span class="text-rose-500">*</span>';
            }
            hitungSemua();
        }

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
            const skema = document.querySelector('input[name="skema_pembayaran"]:checked')?.value || 'sekali_tayang';
            const qty = (skema === 'paket') ? 1 : (parseFloat(document.getElementById('in_qty')?.value) || 1);
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
            const skema = document.querySelector('input[name="skema_pembayaran"]:checked')?.value || 'sekali_tayang';
            const qty = (skema === 'paket') ? 1 : (parseFloat(document.getElementById('in_qty')?.value) || 1);
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

        function setBayarLunasOnline() {
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
            const skema = document.querySelector('input[name="skema_pembayaran"]:checked')?.value || 'sekali_tayang';
            const qty = (skema === 'paket') ? 1 : (parseFloat(document.getElementById('in_qty')?.value) || 1);

            const omset = harga * qty;
            const subtotal = Math.max(0, omset - diskon);
            const ppn = Math.round(subtotal * (configPpnPersen / 100));
            const total = subtotal + ppn;
            const piutang = Math.max(0, total - bayar);

            const lblDesc = document.getElementById('lbl_subtotal_desc');
            const hintEl = document.getElementById('lbl_per_tayang_hint');
            if (skema === 'sekali_tayang' && qty > 1) {
                if (lblDesc) lblDesc.innerText = 'Subtotal (' + qty + 'x tayang):';
                if (hintEl) hintEl.innerText = 'x ' + qty + ' kali = Rp ' + formatRupiah(omset);
            } else {
                if (lblDesc) lblDesc.innerText = 'Subtotal:';
                if (hintEl) hintEl.innerText = '';
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
                elFrekuensi.innerText = (skema === 'paket') ? 'Paket Publikasi' : (qty + ' Kali Tayang');
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
            if (prevQty) {
                prevQty.innerText = (skema === 'paket') ? 'Paket Publikasi' : (qty + ' Hari / Kali');
            }

            updateInsentifFromDpp(subtotal);
            updateKomisiFromDpp(subtotal);
        }

        function updateInsentifFromDpp(dpp) {
            const inputPersen = document.getElementById('insentif_persen');
            const inputNominal = document.getElementById('insentif_transaksionline');
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
            const skema = document.querySelector('input[name="skema_pembayaran"]:checked')?.value || 'sekali_tayang';
            const qty = (skema === 'paket') ? 1 : (parseFloat(document.getElementById('in_qty')?.value) || 1);
            const dpp = Math.max(0, (harga * qty) - diskon);

            const persen = parseFloat(val) || 0;
            const nominal = Math.round((dpp * persen) / 100);
            const inputNominal = document.getElementById('insentif_transaksionline');
            if (inputNominal) inputNominal.value = formatRupiah(nominal);
        }

        function handleInsentifNominalInput(el) {
            const raw = cleanNumber(el.value);
            el.value = raw > 0 ? formatRupiah(raw) : '';
            const harga = cleanNumber(document.getElementById('in_harga')?.value);
            const diskon = cleanNumber(document.getElementById('in_diskon')?.value);
            const skema = document.querySelector('input[name="skema_pembayaran"]:checked')?.value || 'sekali_tayang';
            const qty = (skema === 'paket') ? 1 : (parseFloat(document.getElementById('in_qty')?.value) || 1);
            const dpp = Math.max(0, (harga * qty) - diskon);

            if (dpp > 0) {
                const persen = Math.round((raw / dpp) * 1000) / 10;
                const inputPersen = document.getElementById('insentif_persen');
                if (inputPersen) inputPersen.value = persen;
            }
        }

        function updateKomisiFromDpp(dpp) {
            const inputPersen = document.getElementById('komisi_persen');
            const inputNominal = document.getElementById('komisi_transaksionline');
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
            const skema = document.querySelector('input[name="skema_pembayaran"]:checked')?.value || 'sekali_tayang';
            const qty = (skema === 'paket') ? 1 : (parseFloat(document.getElementById('in_qty')?.value) || 1);
            const dpp = Math.max(0, (harga * qty) - diskon);

            const persen = parseFloat(val) || 0;
            const nominal = Math.round((dpp * persen) / 100);
            const inputNominal = document.getElementById('komisi_transaksionline');
            if (inputNominal) inputNominal.value = formatRupiah(nominal);
        }

        function handleKomisiNominalInput(el) {
            const raw = cleanNumber(el.value);
            el.value = raw > 0 ? formatRupiah(raw) : '';
            const harga = cleanNumber(document.getElementById('in_harga')?.value);
            const diskon = cleanNumber(document.getElementById('in_diskon')?.value);
            const skema = document.querySelector('input[name="skema_pembayaran"]:checked')?.value || 'sekali_tayang';
            const qty = (skema === 'paket') ? 1 : (parseFloat(document.getElementById('in_qty')?.value) || 1);
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

            const hiddenSales = document.getElementById('sales_iklanonline');
            if (hiddenSales) hiddenSales.value = fullSalesName;
        }

        function toggleStatusProduksi(radio) {
            document.querySelectorAll('.status-prod-card').forEach(card => {
                card.classList.remove('border-kp-blue-600', 'bg-sky-50', 'text-kp-blue-950', 'font-bold', 'shadow-2xs');
                card.classList.add('border-slate-200', 'bg-white', 'text-slate-700', 'font-medium');
            });
            const activeCard = radio.closest('.status-prod-card');
            if (activeCard) {
                activeCard.classList.remove('border-slate-200', 'bg-white', 'text-slate-700', 'font-medium');
                activeCard.classList.add('border-kp-blue-600', 'bg-sky-50', 'text-kp-blue-950', 'font-bold', 'shadow-2xs');
            }
            updateSimulatorLive();
        }

        // --- SIMULATOR LIVE PREVIEW ---
        function updateSimulatorLive() {
            const instansi = document.getElementById('inputManualNamaPemasang')?.value.trim() || 'Belum diisi';
            const materi = document.getElementById('materi_iklan')?.value.trim() || 'Belum diisi';
            const activeTab = document.getElementById('kategori_form')?.value || 'PORTAL_BERITA';

            const prevInstansi = document.getElementById('prev_instansi');
            if (prevInstansi) prevInstansi.innerText = instansi;

            const prevMateri = document.getElementById('prev_materi');
            if (prevMateri) prevMateri.innerText = 'Materi: ' + materi;

            const prevTgl = document.getElementById('prev_tgl');
            if (prevTgl) {
                if (selectedDates.length > 0) {
                    prevTgl.innerText = `${formatIndoChip(selectedDates[0])} (${selectedDates.length} tgl)`;
                } else {
                    prevTgl.innerText = '-';
                }
            }

            // Platform & Penempatan Tags (Menampilkan Semua Platform Terpilih Secara Dinamis)
            const tagsCont = document.getElementById('prev_platform_tags');
            if (tagsCont) {
                let badges = [];
                const checkedPortals = Array.from(document.querySelectorAll('.portal-checkbox:checked')).map(cb => cb.value);
                const medsosItems = Object.values(selectedMedsosProducts || {});

                if (checkedPortals.length > 0) {
                    if (checkedPortals.length <= 3) {
                        checkedPortals.forEach(p => {
                            badges.push(`<span class="px-2 py-0.5 rounded-md bg-sky-100 text-sky-800 text-xs font-semibold border border-sky-200">${p}</span>`);
                        });
                    } else {
                        badges.push(`<span class="px-2 py-0.5 rounded-md bg-sky-100 text-sky-800 text-xs font-semibold border border-sky-200">Jaringan ${checkedPortals.length} Portal Web</span>`);
                    }
                }

                const socialBadges = {
                    'INSTAGRAM': { text: 'Instagram @kabarpriangan', cls: 'bg-purple-100 text-purple-800 border-purple-200' },
                    'TIKTOK':    { text: 'TikTok @kabarpriangan', cls: 'bg-slate-900 text-white border-slate-700' },
                    'YOUTUBE':   { text: 'YouTube Kabar Priangan', cls: 'bg-rose-100 text-rose-800 border-rose-200' },
                    'PODCAST':   { text: 'Priangan Podcast', cls: 'bg-emerald-100 text-emerald-800 border-emerald-200' }
                };

                const seenSocial = new Set();
                medsosItems.forEach(item => {
                    if (!seenSocial.has(item.tabKey) && socialBadges[item.tabKey]) {
                        seenSocial.add(item.tabKey);
                        const cfg = socialBadges[item.tabKey];
                        badges.push(`<span class="px-2 py-0.5 rounded-md text-xs font-semibold border ${cfg.cls}">${cfg.text}</span>`);
                    }
                });

                if (badges.length === 0) {
                    badges.push(`<span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 text-xs font-medium border border-slate-200">Belum ada platform dipilih</span>`);
                }

                tagsCont.innerHTML = badges.join(' ');
            }


            // Status Produksi
            const stVal = document.querySelector('input[name="status_produksi"]:checked')?.value || 'menunggu_bahan';
            const stBadge = document.getElementById('prev_status_badge');
            if (stBadge) {
                const stMap = {
                    'menunggu_bahan': { text: 'Menunggu Bahan', cls: 'bg-rose-100 text-rose-800 border-rose-300' },
                    'antrean_redaksi': { text: 'Antrean Redaksi', cls: 'bg-amber-100 text-amber-800 border-amber-300' },
                    'tayang': { text: 'Tayang (Live)', cls: 'bg-emerald-100 text-emerald-800 border-emerald-300' },
                    'selesai': { text: 'Selesai (Archived)', cls: 'bg-slate-100 text-slate-800 border-slate-300' },
                };
                const config = stMap[stVal] || stMap['menunggu_bahan'];
                stBadge.innerText = config.text;
                stBadge.className = 'px-2 py-0.5 rounded text-xs font-bold border ' + config.cls;
            }
        }

        // --- SUBMIT PARITY VALIDATION & MODAL ---
        function konfirmasiSimpanTransaksi() {
            const hiddenNamaInput = document.getElementById('nama_pemasangonline');
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
                alert('Judul / Materi Iklan wajib diisi.');
                materiInput?.focus();
                return false;
            }

            // Parity Check: Selected Dates vs Frekuensi
            const totalMuatInput = document.getElementById('in_qty');
            const targetQuota = parseInt(totalMuatInput ? totalMuatInput.value : 1) || 1;

            if (selectedDates.length !== targetQuota) {
                alert(`Perhatian: Anda menentukan Frekuensi Tayang sebanyak ${targetQuota} kali, namun baru memilih ${selectedDates.length} tanggal di kalender. Mohon sesuaikan tanggal di kalender.`);
                return false;
            }

            // Set modal values
            const namaLabel = document.getElementById('konfirmasiNamaPemesan');
            if (namaLabel) namaLabel.textContent = finalNama;

            const produkLabel = document.getElementById('konfirmasiProdukNama');
            const currentProdukText = document.getElementById('prev_produk')?.textContent || '-';
            if (produkLabel) produkLabel.textContent = currentProdukText;

            const jadwalLabel = document.getElementById('konfirmasiJadwalTayang');
            if (jadwalLabel) jadwalLabel.textContent = `${selectedDates.length} Hari / Tanggal`;

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
            document.getElementById('formTransaksiOnline').submit();
        }

        // --- MODAL SALES & PELANGGAN ON-THE-FLY ---
        function openModalPelangganBaru() {
            closeModalSalesAeBaru();
            const modal = document.getElementById('modalPelangganBaru');
            if (modal) modal.classList.remove('hidden');
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
                        total_piutang_koran: 0
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
            // Render dari cache server awal jika sudah tersedia
            renderCalendar();
            renderSelectedDatesChips();
            if (selectedDates.length > 0) {
                updatePanelInfoTanggal(selectedDates[0]);
            }

            fetchKalenderBulan(currentCalYear, currentCalMonth + 1, () => {
                renderCalendar();
                renderSelectedDatesChips();
                if (selectedDates.length > 0) {
                    updatePanelInfoTanggal(selectedDates[0]);
                }
            });

            handlePortalCheckboxChange();
            hitungSemua();
            updateSimulatorLive();
        });
    </script>
    @endpush
</x-app-layout>
