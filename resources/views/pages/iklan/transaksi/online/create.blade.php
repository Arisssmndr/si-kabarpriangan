<x-app-layout>
    <div class="space-y-6">

        <!-- ============================================================== -->
        <!-- HEADER HALAMAN: STANDAR SELARAS TRANSAKSI KORAN                -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        {{ isset($transaksi) ? 'Ubah Data & Jadwal Order Publikasi Online' : 'Entri Order Publikasi Online' }}
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5 font-normal">
                        {{ isset($transaksi) ? 'Formulir koreksi dan reschedule publikasi multimedia Kabar Priangan Grup.' : 'Formulir pemesanan publikasi portal online, medsos, podcast, dan Priangan TV.' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <div class="text-[11px] font-medium text-slate-500">No. Faktur</div>
                    <div class="font-mono text-sm font-bold text-kp-blue-700">{{ $nofakturonline }}</div>
                </div>
                <a href="{{ route('transaksionline.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-medium rounded-lg shadow-xs transition cursor-pointer">
                    &larr; <span>Kembali ke Transaksi Online</span>
                </a>
            </div>
        </div>

        <!-- Notice Deadline & Cut-Off Tayang Online & Medsos (3 Status: Netral / Kuning / Merah) -->
        @php
            \Carbon\Carbon::setLocale('id');
            $jamCutoffStr = $configs['jam_cutoff'] ?? '14:00';
            $cutoffToday = \Carbon\Carbon::today()->setTimeFromTimeString($jamCutoffStr);
            $warningToday = $cutoffToday->copy()->subHour(); // 1 jam sebelum batas cutoff
            $nowTime = now();

            if ($nowTime->greaterThanOrEqualTo($cutoffToday)) {
                $cutoffState = 'passed'; // Lewat batas cutoff -> MERAH
            } elseif ($nowTime->greaterThanOrEqualTo($warningToday)) {
                $cutoffState = 'warning'; // 1 jam sebelum batas cutoff -> KUNING
            } else {
                $cutoffState = 'normal'; // Jam biasa sebelum 1 jam cutoff -> NETRAL
            }

            $besok = now()->addDay()->locale('id')->translatedFormat('l, d F Y');
            $lusa  = now()->addDays(2)->locale('id')->translatedFormat('l, d F Y');
            $edisiTerdekat = ($cutoffState === 'passed') ? $lusa : $besok;

            $bannerBoxClass = match($cutoffState) {
                'passed'  => 'bg-rose-50 border-rose-300 text-rose-950',
                'warning' => 'bg-amber-50 border-amber-300 text-amber-950',
                default   => 'bg-gradient-to-r from-sky-50/90 via-white to-slate-50 border-sky-100 text-slate-800',
            };

            $bannerIconBoxClass = match($cutoffState) {
                'passed'  => 'bg-rose-100 text-rose-700 border border-rose-300',
                'warning' => 'bg-amber-100 text-amber-700 border border-amber-300',
                default   => 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200',
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
                'passed'  => 'Lewat Batas Input Antrean Redaksi Hari Ini (' . $jamCutoffStr . ' WIB)',
                'warning' => '1 Jam Menjelang Batas Antrean Redaksi Hari Ini (' . $jamCutoffStr . ' WIB)',
                default   => 'Batas Antrean Tayang Hari Ini: ' . $jamCutoffStr . ' WIB',
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
                <div class="flex items-center gap-1.5 self-end sm:self-center font-mono text-[11px] font-semibold px-2.5 py-1 rounded-md border shadow-2xs {{ $bannerClockClass }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ now()->format('H:i') }} WIB</span>
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

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- ============================================================== -->
                <!-- PANEL KIRI: INPUT DATA TRANSAKSI                               -->
                <!-- ============================================================== -->
                <div class="lg:col-span-7 space-y-5">

                    <!-- SEKSI 1: CARA BAYAR & DATA PEMASANG (PERSIS TRANSAKSI KORAN) -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-xs font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-[11px] font-bold">1</span>
                                <span>Data Pemesan &amp; Instansi</span>
                            </span>
                        </div>

                        <!-- PILIHAN SKEMA PEMBAYARAN: TUNAI vs KREDIT -->
                        <div>
                            <label class="text-xs font-medium text-slate-700 mb-2 block">
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
                                        <span class="text-xs tracking-tight">Tunai</span>
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
                                        <span class="text-xs tracking-tight">Kredit</span>
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
                                    <div class="flex items-center justify-between gap-2">
                                        <label class="text-xs font-medium text-slate-700 whitespace-nowrap">
                                            Pelanggan Terdaftar
                                        </label>
                                        <button type="button" onclick="openModalPelangganBaru()" 
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 border border-kp-blue-200/90 hover:border-kp-blue-300 transition shadow-2xs active:scale-95 cursor-pointer whitespace-nowrap shrink-0"
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
                                                <span id="labelNamaPelanggan" class="font-bold text-slate-900 text-xs truncate">{{ $selectedOldPelanggan->nama_instansi ?? '' }}</span>
                                                <span id="badgeJenisPelanggan" class="px-1.5 py-0.5 text-[9px] font-medium rounded bg-white text-slate-700 border border-slate-200 uppercase">{{ $selectedOldPelanggan->jenis_klien ?? '' }}</span>
                                                <span id="labelKodePelanggan" class="text-[10px] font-mono text-slate-400">[{{ $selectedOldPelanggan->kode_pelanggan ?? '' }}]</span>
                                            </div>
                                            <div class="text-[10px] text-slate-500 flex items-center gap-3 mt-1 flex-wrap">
                                                <span id="labelNpwpPelanggan" class="inline-flex items-center gap-1 truncate {{ empty($selectedOldPelanggan?->npwp_nik) ? 'hidden' : '' }}">
                                                    <span class="font-mono text-[9px] bg-white text-slate-700 px-1.5 py-0.5 rounded border border-slate-200">NPWP: <span id="textNpwpPelanggan">{{ $selectedOldPelanggan->npwp_nik ?? '' }}</span></span>
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
                                                <span id="labelPiutangPelanggan" class="font-medium text-rose-600 {{ ($selectedOldPelanggan && ($selectedOldPelanggan->total_piutang_koran ?? 0) > 0) ? '' : 'hidden' }}">
                                                    Piutang: Rp {{ number_format($selectedOldPelanggan->total_piutang_koran ?? 0, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                        <button type="button" onclick="resetPilihanPelanggan()"
                                                class="px-2.5 py-1 text-[11px] font-medium text-slate-600 hover:text-rose-600 bg-white hover:bg-rose-50 rounded-lg border border-slate-200 hover:border-rose-200 transition cursor-pointer shrink-0 flex items-center gap-1 shadow-2xs"
                                                title="Ganti pilihan pelanggan">
                                            <span>Ganti</span>
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
                                                   class="w-full h-10 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                                            <div class="absolute flex items-center gap-1" style="right: 10px; top: 50%; transform: translateY(-50%);">
                                                <div id="spinnerCariPelanggan" class="hidden">
                                                    <svg class="animate-spin h-3.5 w-3.5 text-kp-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                                </div>
                                                <button type="button" onclick="handleSearchPelangganFocus(true)"
                                                        class="p-1 text-slate-400 hover:text-slate-600 transition cursor-pointer" title="Buka Daftar Pelanggan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Floating Dropdown Hasil Pencarian -->
                                        <div id="dropdownHasilPelanggan" 
                                             class="absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 max-h-72 overflow-y-auto divide-y divide-slate-100 hidden">
                                            <div id="listPelangganItem" class="p-1.5 space-y-0.5">
                                                <!-- Diisi oleh JS -->
                                            </div>
                                            <div class="p-2.5 bg-slate-50 text-center flex items-center justify-between border-t border-slate-100 text-xs">
                                                <span class="text-[11px] text-slate-500">Pelanggan baru?</span>
                                                <button type="button" onclick="openModalPelangganBaru()" class="text-xs font-semibold text-kp-blue-600 hover:underline cursor-pointer">
                                                    + Tambah Pelanggan Baru
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- KOLOM 2: INPUT NAMA PEMASANG MANUAL -->
                                <div class="space-y-1.5 min-w-0">
                                    <label class="text-xs font-medium text-slate-700 whitespace-nowrap block">
                                        Nama Pemesan / Instansi <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative flex items-center">
                                        <input type="text" id="inputManualNamaPemasang"
                                               oninput="handleManualNamaInput(this.value)"
                                               placeholder="Nama instansi / pemesan"
                                               value="{{ $oldNamaPemasang }}"
                                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Baris Tambahan: NPWP / NIK & Email Faktur / Penagihan (Seragam Enterprise) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label class="text-xs font-medium text-slate-700 mb-1.5 flex items-center justify-between">
                                    <span>NPWP / NIK Pemasang</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Faktur Pajak 16 Digit</span>
                                </label>
                                <input type="text" name="npwp_nik" id="npwp_nik"
                                       placeholder="NPWP atau NIK pemesan"
                                       value="{{ old('npwp_nik', isset($transaksi) ? ($transaksi->pelanggan?->npwp_nik ?? '') : ($selectedOldPelanggan?->npwp_nik ?? '')) }}"
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-mono font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-700 mb-1.5 flex items-center justify-between">
                                    <span>Email Tagihan / Bukti Tayang</span>
                                    <span class="text-[10px] text-slate-400 font-normal">e-Faktur &amp; SPPI</span>
                                </label>
                                <input type="email" name="email_pemasang" id="email_pemasang"
                                       placeholder="email@instansi.com"
                                       value="{{ old('email_pemasang', isset($transaksi) ? ($transaksi->pelanggan?->email ?? '') : ($selectedOldPelanggan?->email ?? '')) }}"
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-normal text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>

                        <!-- Baris 2: Produk / Jasa & No. SPK / PO / Kontrak (2 Kolom Lega) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label class="text-xs font-medium text-slate-700 mb-1.5 block">
                                    Produk / Jasa
                                </label>
                                <input type="text" name="produk_jasa" id="produk_jasa"
                                       placeholder="Jenis produk / jasa"
                                       value="{{ old('produk_jasa', isset($transaksi) ? $transaksi->produk_jasa : '') }}"
                                       oninput="updateSimulatorLive()"
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-normal text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-700 mb-1.5 block">
                                    No. SPK / PO / Kontrak <span class="text-[10px] text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <input type="text" name="no_spk" id="no_spk"
                                       placeholder="Nomor SPK / PO"
                                       value="{{ old('no_spk', isset($transaksi) ? $transaksi->no_spk : '') }}"
                                       oninput="updateSimulatorLive()"
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-normal text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>

                        <!-- Baris 3: Nama Penanggung Jawab (PIC) & Jabatan PIC (2 Kolom Lega) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-slate-700 mb-1.5 block">
                                    Nama Penanggung Jawab (PIC)
                                </label>
                                <input type="text" name="nama_pic" id="nama_pic"
                                       placeholder="Nama PIC"
                                       value="{{ old('nama_pic', isset($transaksi) ? $transaksi->nama_pic : ($selectedOldPelanggan?->kontak_pic ?? '')) }}"
                                       oninput="updateSimulatorLive()"
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-normal text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-700 mb-1.5 block">
                                    Jabatan PIC
                                </label>
                                <input type="text" name="jabatan_pic" id="jabatan_pic"
                                       placeholder="Jabatan PIC"
                                       value="{{ old('jabatan_pic', isset($transaksi) ? $transaksi->jabatan_pic : '') }}"
                                       oninput="updateSimulatorLive()"
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-normal text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>

                        <!-- Baris 4: Alamat & Telepon (2 Kolom Lega) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-slate-700 mb-1.5 block">
                                    Alamat Lengkap
                                </label>
                                <textarea name="alamat_pemasangonline" id="alamat_pemasangonline" rows="2"
                                          placeholder="Alamat pemesan"
                                          oninput="updateSimulatorLive()"
                                          class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-normal text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs resize-none">{{ old('alamat_pemasangonline', isset($transaksi) ? $transaksi->alamat_pemasangonline : ($selectedOldPelanggan?->alamat ?? '')) }}</textarea>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-700 mb-1.5 block">
                                    No. Telepon / WhatsApp
                                </label>
                                <div class="relative flex items-center">
                                    <input type="text" name="no_kontak_pemasang" id="no_kontak_pemasang"
                                           placeholder="08xxxxxxxxxx"
                                           value="{{ old('no_kontak_pemasang', isset($transaksi) ? ($transaksi->no_kontak_pemasang ?: $transaksi->no_telp) : ($selectedOldPelanggan?->no_telp ?? '')) }}"
                                           oninput="updateSimulatorLive()"
                                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-normal text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- Sales & Komisioner (Identik & Selaras dengan Transaksi Koran) -->
                        <div class="pt-4 border-t border-slate-100 space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-semibold text-slate-900 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>Sales &amp; Komisioner</span>
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                <!-- Card 1: Sales / AE -->
                                <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/90 space-y-2.5 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <label class="text-xs font-medium text-slate-700 whitespace-nowrap">
                                            Sales / AE <span class="text-rose-500">*</span>
                                        </label>
                                        <button type="button" onclick="openModalSalesAeBaru()"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 border border-kp-blue-200/90 hover:border-kp-blue-300 transition shadow-2xs active:scale-95 cursor-pointer whitespace-nowrap shrink-0"
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
                                            class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
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
                                        <label class="block text-[11px] font-medium text-slate-600 mb-1">
                                            Insentif Sales
                                        </label>
                                        <div class="grid grid-cols-5 gap-2">
                                            <div class="col-span-2 relative">
                                                <input type="number" name="insentif_persen" id="insentif_persen" 
                                                       value="{{ old('insentif_persen', isset($transaksi) ? $transaksi->insentif_persen : 0) }}" step="0.5" min="0" max="100"
                                                       placeholder="0"
                                                       oninput="handleInsentifPersenChange(this.value)"
                                                       class="w-full h-8 px-2.5 pr-6 bg-white border border-slate-200 rounded-lg text-xs font-mono font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                                <span class="absolute right-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">%</span>
                                            </div>
                                            <div class="col-span-3 relative">
                                                <span class="absolute left-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">Rp</span>
                                                <input type="text" name="insentif_transaksionline" id="insentif_transaksionline" 
                                                       value="{{ old('insentif_transaksionline', isset($transaksi) && $transaksi->insentif_transaksionline ? number_format($transaksi->insentif_transaksionline, 0, ',', '.') : 0) }}"
                                                       placeholder="0"
                                                       onkeyup="handleInsentifNominalInput(this)"
                                                       class="w-full h-8 pl-7 pr-2.5 bg-white border border-slate-200 rounded-lg text-xs font-mono font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2: Komisioner / Perantara -->
                                <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/90 space-y-2.5 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <label class="text-xs font-medium text-slate-700 whitespace-nowrap">
                                            Perantara / Komisioner
                                        </label>
                                        <span class="text-[10px] text-slate-400 font-normal shrink-0">
                                            Opsional
                                        </span>
                                    </div>
                                    <div>
                                        <input type="text" name="komisioner" id="komisioner" 
                                               value="{{ old('komisioner', isset($transaksi) ? $transaksi->komisioner : '') }}"
                                               placeholder="Nama perantara"
                                               class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs font-normal text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-medium text-slate-600 mb-1">
                                            Komisi Perantara
                                        </label>
                                        <div class="grid grid-cols-5 gap-2">
                                            <div class="col-span-2 relative">
                                                <input type="number" name="komisi_persen" id="komisi_persen" 
                                                       value="{{ old('komisi_persen', isset($transaksi) ? $transaksi->komisi_persen : 0) }}" step="0.5" min="0" max="100"
                                                       placeholder="0"
                                                       oninput="handleKomisiPersenChange(this.value)"
                                                       class="w-full h-8 px-2.5 pr-6 bg-white border border-slate-200 rounded-lg text-xs font-mono font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                                <span class="absolute right-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">%</span>
                                            </div>
                                            <div class="col-span-3 relative">
                                                <span class="absolute left-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">Rp</span>
                                                <input type="text" name="komisi_transaksionline" id="komisi_transaksionline" 
                                                       value="{{ old('komisi_transaksionline', isset($transaksi) && $transaksi->komisi_transaksionline ? number_format($transaksi->komisi_transaksionline, 0, ',', '.') : 0) }}"
                                                       placeholder="0"
                                                       onkeyup="handleKomisiNominalInput(this)"
                                                       class="w-full h-8 pl-7 pr-2.5 bg-white border border-slate-200 rounded-lg text-xs font-mono font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEKSI 2: MEDIA & MATERI IKLAN -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-xs font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-[11px] font-bold">2</span>
                                <span>Media &amp; Materi Iklan</span>
                            </span>
                            <span id="badge_total_platform" class="px-2 py-0.5 bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 rounded-md text-[11px] font-semibold">
                                1 Media Dipilih
                            </span>
                        </div>

                        <!-- 1. PILIHAN MEDIA -->
                        <div>
                            <label class="text-xs font-medium text-slate-700 mb-2 block">
                                Pilihan Media <span class="text-rose-500">*</span>
                            </label>

                            @php
                                $selectedPlatforms = old('platforms_publikasi', isset($transaksi) ? ($transaksi->platforms_publikasi ?: [$transaksi->portal_iklanonline]) : ['Portal Berita']);
                                if (!is_array($selectedPlatforms)) {
                                    $selectedPlatforms = [$selectedPlatforms];
                                }

                                $platformList = [
                                    ['id' => 'Portal Berita', 'title' => 'Portal Berita', 'desc' => 'Website'],
                                    ['id' => 'Instagram', 'title' => 'Instagram', 'desc' => '@kabarpriangan'],
                                    ['id' => 'TikTok', 'title' => 'TikTok', 'desc' => '@kabarpriangan'],
                                    ['id' => 'YouTube', 'title' => 'YouTube', 'desc' => 'Channel Resmi'],
                                    ['id' => 'Podcast', 'title' => 'Podcast', 'desc' => 'Studio Priangan'],
                                    ['id' => 'Priangan TV', 'title' => 'Priangan TV', 'desc' => 'Web TV / Live'],
                                ];
                            @endphp                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach($platformList as $pf)
                                    @php
                                        $isChecked = in_array($pf['id'], $selectedPlatforms);
                                    @endphp
                                    <label class="platform-card relative flex flex-col justify-center p-2.5 rounded-xl border-2 transition cursor-pointer select-none text-center {{ $isChecked ? 'bg-sky-50 border-kp-blue-600 text-kp-blue-950 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-800 hover:border-kp-blue-300 hover:bg-slate-50 font-medium' }}">
                                        <input type="checkbox" name="platforms_publikasi[]" value="{{ $pf['id'] }}" 
                                               class="sr-only platform-checkbox" 
                                               {{ $isChecked ? 'checked' : '' }} 
                                               onchange="togglePlatformCard(this)">
                                        <div class="text-xs truncate platform-title">{{ $pf['title'] }}</div>
                                        <div class="text-[10px] truncate platform-desc {{ $isChecked ? 'text-kp-blue-700 font-medium' : 'text-slate-400' }}">{{ $pf['desc'] }}</div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- 2. JENIS / FORMAT IKLAN -->
                        <div class="pt-3 border-t border-slate-100">
                            <label class="text-xs font-medium text-slate-700 mb-2 block">
                                Jenis / Format Iklan <span class="text-rose-500">*</span>
                            </label>

                            @php
                                $selectedKategori = old('kategori_produk_publikasi', isset($transaksi) ? ($transaksi->kategori_produk_publikasi ?: ['Advertorial Berita']) : ['Advertorial Berita']);
                                if (!is_array($selectedKategori)) {
                                    $selectedKategori = [$selectedKategori];
                                }

                                $kategoriList = [
                                    ['id' => 'Advertorial Berita', 'title' => 'Advertorial / Berita', 'desc' => 'Liputan & rilis berita'],
                                    ['id' => 'Display Banner Ads', 'title' => 'Banner Website', 'desc' => 'Banner gambar di web'],
                                    ['id' => 'Social Media Feed / Reels', 'title' => 'Media Sosial', 'desc' => 'Postingan feed / reels / story'],
                                    ['id' => 'Talkshow / Podcast Taping', 'title' => 'Video / Podcast', 'desc' => 'Bincang & rekam studio'],
                                    ['id' => 'Ucapan Selamat / Duka Cita', 'title' => 'Ucapan Selamat', 'desc' => 'Flyer ucapan & greeting'],
                                ];
                            @endphp

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach($kategoriList as $kat)
                                    @php
                                        $isKatChecked = in_array($kat['id'], $selectedKategori);
                                    @endphp
                                    <label class="kategori-card relative flex flex-col justify-center p-2.5 rounded-xl border-2 transition cursor-pointer select-none text-center {{ $isKatChecked ? 'bg-sky-50 border-kp-blue-600 text-kp-blue-950 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-800 hover:border-kp-blue-300 hover:bg-slate-50 font-medium' }}">
                                        <input type="checkbox" name="kategori_produk_publikasi[]" value="{{ $kat['id'] }}" 
                                               class="sr-only kategori-checkbox" 
                                               {{ $isKatChecked ? 'checked' : '' }} 
                                               onchange="toggleKategoriCard(this)">
                                        <div class="text-xs truncate kategori-title">{{ $kat['title'] }}</div>
                                        <div class="text-[10px] truncate kategori-desc {{ $isKatChecked ? 'text-kp-blue-700 font-medium' : 'text-slate-400' }}">{{ $kat['desc'] }}</div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- 3. BENTUK MATERI -->
                        @php
                            $currentBentuk = old('bentuk_publikasi', isset($transaksi) ? $transaksi->bentuk_publikasi : 'display');
                        @endphp
                        <div class="pt-3 border-t border-slate-100">
                            <label class="text-xs font-medium text-slate-700 mb-2 block">
                                Bentuk Materi <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="bentuk-card flex items-center justify-center p-2.5 rounded-xl border-2 transition cursor-pointer select-none text-center {{ $currentBentuk === 'video' ? 'bg-sky-50 border-kp-blue-600 text-kp-blue-950 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-700 hover:border-kp-blue-300 hover:bg-slate-50 font-medium' }}">
                                    <input type="radio" name="bentuk_publikasi" value="video" {{ $currentBentuk === 'video' ? 'checked' : '' }} class="sr-only" onchange="toggleBentuk(this)">
                                    <span class="text-xs">Video</span>
                                </label>

                                <label class="bentuk-card flex items-center justify-center p-2.5 rounded-xl border-2 transition cursor-pointer select-none text-center {{ $currentBentuk === 'display' ? 'bg-sky-50 border-kp-blue-600 text-kp-blue-950 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-700 hover:border-kp-blue-300 hover:bg-slate-50 font-medium' }}">
                                    <input type="radio" name="bentuk_publikasi" value="display" {{ $currentBentuk === 'display' ? 'checked' : '' }} class="sr-only" onchange="toggleBentuk(this)">
                                    <span class="text-xs">Gambar / Foto</span>
                                </label>

                                <label class="bentuk-card flex items-center justify-center p-2.5 rounded-xl border-2 transition cursor-pointer select-none text-center {{ $currentBentuk === 'lainnya' ? 'bg-sky-50 border-kp-blue-600 text-kp-blue-950 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-700 hover:border-kp-blue-300 hover:bg-slate-50 font-medium' }}">
                                    <input type="radio" name="bentuk_publikasi" value="lainnya" {{ $currentBentuk === 'lainnya' ? 'checked' : '' }} class="sr-only" onchange="toggleBentuk(this)">
                                    <span class="text-xs">Lainnya</span>
                                </label>
                            </div>
                        </div>

                        <!-- 4. MATERI IKLAN (JUDUL & LINK ASET) -->
                        <div class="pt-3 border-t border-slate-100 space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Judul / Materi Iklan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="materi_iklan" id="materi_iklan" 
                                       value="{{ old('materi_iklan', isset($transaksi) ? $transaksi->materi_iklan : '') }}" required 
                                       placeholder="Judul / materi iklan"
                                       oninput="updateSimulatorLive()"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">
                                        Link Google Drive / Materi <span class="text-[10px] text-slate-400 font-normal">(Opsional)</span>
                                    </label>
                                    <input type="url" name="url_materi_iklan" id="url_materi_iklan" 
                                           value="{{ old('url_materi_iklan', isset($transaksi) ? $transaksi->url_materi_iklan : '') }}" 
                                           placeholder="Link Google Drive / materi"
                                           oninput="updateSimulatorLive()"
                                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">
                                        Kategori Iklan
                                    </label>
                                    <select name="id_iklanonline" 
                                            class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                        @foreach($iklanonline as $io)
                                            <option value="{{ $io->id }}" {{ (old('id_iklanonline', isset($transaksi) ? $transaksi->id_iklanonline : '') == $io->id) ? 'selected' : '' }}>
                                                {{ $io->jenis_iklanonline ?? $io->nama_jenis_iklan ?? 'Iklan Online' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 5. JADWAL TAYANG & JUMLAH TERBIT -->
                        <div class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Tanggal Mulai Tayang <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="tanggal_muatiklanonline" id="tanggal_muatiklanonline" 
                                       value="{{ old('tanggal_muatiklanonline', isset($transaksi) ? $transaksi->tanggal_muatiklanonline?->format('Y-m-d') : date('Y-m-d')) }}" required
                                       onchange="syncDateIndoOnline(); updateSimulatorLive();"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                <div id="labelTglMulaiIndo" class="text-[11px] font-semibold text-kp-blue-700 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span id="textTglMulaiIndo"></span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Tanggal Selesai <span class="text-[10px] text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <input type="date" name="tanggal_selesai_tayang" id="tanggal_selesai_tayang" 
                                       value="{{ old('tanggal_selesai_tayang', isset($transaksi) ? $transaksi->tanggal_selesai_tayang?->format('Y-m-d') : '') }}"
                                       onchange="syncDateIndoOnline(); updateSimulatorLive();"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                <div id="labelTglSelesaiIndo" class="text-[11px] font-semibold text-kp-blue-700 mt-1 flex items-center gap-1">
                                    <span id="textTglSelesaiIndo"></span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Jumlah Tayang / Terbit <span class="text-rose-500">*</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center bg-white border border-slate-300 rounded-xl p-1 shadow-2xs flex-1">
                                        <button type="button" onclick="stepJumlahTayang(-1)"
                                                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition cursor-pointer select-none">
                                            −
                                        </button>
                                        <input type="text" inputmode="numeric" id="in_qty" name="total_muatiklanonline" 
                                               value="{{ old('total_muatiklanonline', isset($transaksi) ? $transaksi->total_muatiklanonline : '1') }}" min="1" required 
                                               oninput="hitungSemua()"
                                               class="w-full h-8 px-1 text-center font-bold text-sm text-slate-900 border-0 focus:ring-0 focus:outline-none">
                                        <button type="button" onclick="stepJumlahTayang(1)"
                                                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition cursor-pointer select-none">
                                            +
                                        </button>
                                    </div>
                                    <span class="text-xs font-medium text-slate-700 whitespace-nowrap">Hari / Kali</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- SEKSI 3: STATUS & BUKTI TAYANG -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-xs font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-[11px] font-bold">3</span>
                                <span>Status &amp; Bukti Tayang</span>
                            </span>
                        </div>

                        <!-- 1. STATUS PENGERJAAN -->
                        <div>
                            <label class="text-xs font-medium text-slate-700 mb-2 block">
                                Status Pengerjaan <span class="text-rose-500">*</span>
                            </label>

                            @php
                                $currStatusProduksi = old('status_produksi', isset($transaksi) ? $transaksi->status_produksi : 'menunggu_bahan');
                                $statusOptions = [
                                    ['id' => 'menunggu_bahan', 'label' => 'Menunggu Bahan'],
                                    ['id' => 'antrean_redaksi', 'label' => 'Antrean Redaksi'],
                                    ['id' => 'tayang', 'label' => 'Tayang (Live)'],
                                    ['id' => 'selesai', 'label' => 'Selesai'],
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

                        <!-- 2. CATATAN REDAKSI -->
                        <div class="pt-3 border-t border-slate-100">
                            <label class="block text-xs font-medium text-slate-700 mb-1">
                                Catatan Redaksi <span class="text-[10px] text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" name="catatan_redaksi" id="catatan_redaksi"
                                   placeholder="Catatan redaksi"
                                   value="{{ old('catatan_redaksi', isset($transaksi) ? $transaksi->catatan_redaksi : '') }}"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                        </div>

                        <!-- 3. INPUT BUKTI TAYANG (OPSIONAL) -->
                        <div class="pt-3 border-t border-slate-100 space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-medium text-slate-700 block">
                                    Bukti Tayang <span class="text-[10px] text-slate-400 font-normal">(Bisa diisi sekarang atau setelah tayang)</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">
                                        Link Berita Portal
                                    </label>
                                    <input type="url" name="url_berita_tayang" id="url_berita_tayang"
                                           value="{{ old('url_berita_tayang', isset($transaksi) ? $transaksi->url_berita_tayang : '') }}"
                                           placeholder="https://kabarpriangan.com/..."
                                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">
                                        Link Medsos (IG / TikTok / YouTube)
                                    </label>
                                    <input type="url" name="url_medsos_tayang" id="url_medsos_tayang"
                                           value="{{ old('url_medsos_tayang', isset($transaksi) ? $transaksi->url_medsos_tayang : '') }}"
                                           placeholder="https://instagram.com/..."
                                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-start">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">
                                        Upload Foto / Bukti Tayang
                                    </label>
                                    <input type="file" name="file_bukti_tayang" id="file_bukti_tayang" accept="image/*,.pdf"
                                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-kp-blue-50 file:text-kp-blue-700 hover:file:bg-kp-blue-100 cursor-pointer border border-slate-300 rounded-xl bg-white">
                                    @if(isset($transaksi) && $transaksi->file_bukti_tayang)
                                        <div class="mt-1.5 flex items-center gap-2 text-[11px] text-kp-blue-600">
                                            <span>File ada:</span>
                                            <a href="{{ asset('storage/' . $transaksi->file_bukti_tayang) }}" target="_blank" class="font-semibold underline">Lihat Foto</a>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">
                                        Tanggal Tayang Riil
                                    </label>
                                    <input type="date" name="tanggal_realisasi_tayang" id="tanggal_realisasi_tayang"
                                           value="{{ old('tanggal_realisasi_tayang', isset($transaksi) ? $transaksi->tanggal_realisasi_tayang?->format('Y-m-d') : '') }}"
                                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- ============================================================== -->
                <!-- PANEL KANAN: RINGKASAN ORDER & RINCIAN KASIR                   -->
                <!-- ============================================================== -->
                <div class="lg:col-span-5 space-y-5 lg:sticky lg:top-6">

                    <!-- 1. RINGKASAN ORDER LIVE PREVIEW -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                            <span class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Ringkasan Order</span>
                            </span>
                            <span class="text-[10px] font-mono text-slate-400">Live Preview</span>
                        </div>

                        <!-- Miniatur Bersih -->
                        <div class="border border-slate-200 rounded-xl p-3 bg-slate-50/60 space-y-2 text-xs">
                            <div class="flex justify-between items-center pb-2 border-b border-slate-200 text-xs">
                                <span class="font-semibold text-slate-900">KABAR PRIANGAN</span>
                                <span id="prev_status_badge" class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-100 text-rose-800 border border-rose-300">
                                    Menunggu Bahan
                                </span>
                            </div>

                            <div class="space-y-1.5 text-slate-600 text-[11px]">
                                <div class="flex">
                                    <span class="w-20 font-medium text-slate-500 shrink-0">Pemesan</span>
                                    <span class="w-2">:</span>
                                    <span id="prev_instansi" class="font-semibold text-slate-900 truncate">Belum diisi</span>
                                </div>
                                <div class="flex">
                                    <span class="w-20 font-medium text-slate-500 shrink-0">Produk</span>
                                    <span class="w-2">:</span>
                                    <span id="prev_produk" class="font-normal text-slate-800 truncate">-</span>
                                </div>
                                <div class="flex">
                                    <span class="w-20 font-medium text-slate-500 shrink-0">PIC</span>
                                    <span class="w-2">:</span>
                                    <span id="prev_pic" class="font-normal text-slate-800 truncate">-</span>
                                </div>
                                <div class="flex">
                                    <span class="w-20 font-medium text-slate-500 shrink-0">SPK / PO</span>
                                    <span class="w-2">:</span>
                                    <span id="prev_spk" class="font-mono text-slate-700 truncate">-</span>
                                </div>
                                <div class="flex">
                                    <span class="w-20 font-medium text-slate-500 shrink-0">Mulai Tayang</span>
                                    <span class="w-2">:</span>
                                    <span id="prev_tgl" class="font-mono text-slate-700">-</span>
                                </div>
                                <div class="flex">
                                    <span class="w-20 font-medium text-slate-500 shrink-0">Jml Tayang</span>
                                    <span class="w-2">:</span>
                                    <span id="prev_qty" class="font-semibold text-kp-blue-700">1 Hari / Kali</span>
                                </div>
                            </div>

                            <!-- Pilihan Media & Format Tags -->
                            <div class="pt-2 border-t border-slate-200 space-y-1.5">
                                <div>
                                    <span class="text-[10px] font-medium text-slate-500">Media:</span>
                                    <div id="prev_platform_tags" class="flex flex-wrap gap-1 mt-0.5">
                                        <span class="px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 text-[10px] font-medium">Portal Berita</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-[10px] font-medium text-slate-500">Format:</span>
                                    <div id="prev_kategori_tags" class="flex flex-wrap gap-1 mt-0.5">
                                        <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[10px] font-medium">Advertorial / Berita</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-1.5 border-t border-slate-200 text-[10px] text-slate-500 italic truncate" id="prev_materi">
                                Materi: Belum diisi
                            </div>
                        </div>
                    </div>

                    <!-- SEKSI 4: RINCIAN BIAYA & PEMBAYARAN -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-xs font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-[11px] font-bold">4</span>
                                <span>Rincian Biaya &amp; Pembayaran</span>
                            </span>
                        </div>

                        @php
                            $currentSkema = old('skema_pembayaran', isset($transaksi) ? $transaksi->skema_pembayaran : 'sekali_tayang');
                        @endphp
                        <!-- Pilihan Skema Tarif -->
                        <div>
                            <label class="text-xs font-medium text-slate-700 mb-2 block">
                                Skema Tarif Iklan <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="skema-card flex items-center justify-center p-2.5 rounded-xl border-2 transition cursor-pointer select-none text-center {{ $currentSkema === 'sekali_tayang' ? 'bg-sky-50 border-kp-blue-600 text-kp-blue-950 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-700 hover:border-kp-blue-300 hover:bg-slate-50 font-medium' }}">
                                    <input type="radio" name="skema_pembayaran" value="sekali_tayang" {{ $currentSkema === 'sekali_tayang' ? 'checked' : '' }} class="sr-only" onchange="toggleSkema(this)">
                                    <span class="text-xs">Per Kali / Hari</span>
                                </label>

                                <label class="skema-card flex items-center justify-center p-2.5 rounded-xl border-2 transition cursor-pointer select-none text-center {{ $currentSkema === 'paket' ? 'bg-sky-50 border-kp-blue-600 text-kp-blue-950 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-700 hover:border-kp-blue-300 hover:bg-slate-50 font-medium' }}">
                                    <input type="radio" name="skema_pembayaran" value="paket" {{ $currentSkema === 'paket' ? 'checked' : '' }} class="sr-only" onchange="toggleSkema(this)">
                                    <span class="text-xs">Paket Iklan</span>
                                </label>
                            </div>
                        </div>

                        <!-- Baris Input Keuangan -->
                        <div class="space-y-3 pt-2">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-medium text-slate-700" id="lbl_tarif_title">
                                        Tarif Iklan <span class="text-rose-500">*</span>
                                    </label>
                                    <span id="lbl_per_tayang_hint" class="text-[11px] text-kp-blue-600 font-medium"></span>
                                </div>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">Rp</span>
                                    <input type="text" id="in_harga" name="harga_transaksionline" 
                                           value="{{ number_format(old('harga_transaksionline', isset($transaksi) ? $transaksi->harga_transaksionline : 0), 0, ',', '.') }}" 
                                           required onkeyup="formatInput(this)"
                                           class="w-full h-10 pl-9 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Potongan Diskon (Rp)
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">Rp</span>
                                    <input type="text" id="in_diskon" name="diskon_transaksionline" 
                                           value="{{ number_format(old('diskon_transaksionline', isset($transaksi) ? $transaksi->diskon_transaksionline : 0), 0, ',', '.') }}" 
                                           onkeyup="formatInput(this)"
                                           class="w-full h-10 pl-9 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                            </div>

                            <!-- Rincian Subtotal & PPN -->
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5 text-xs">
                                <div class="flex justify-between text-slate-600">
                                    <span id="lbl_subtotal_desc">Subtotal:</span>
                                    <span id="lbl_subtotal" class="font-mono font-medium text-slate-900">Rp 0</span>
                                </div>
                                <div class="flex justify-between text-slate-600">
                                    <span>PPN ({{ $ppnPersen ?? 12 }}%):</span>
                                    <span id="lbl_ppn" class="font-mono font-medium text-slate-900">Rp 0</span>
                                </div>
                                <div class="flex justify-between text-slate-900 font-bold border-t border-slate-200 pt-1.5 text-sm">
                                    <span>Total Tagihan:</span>
                                    <span id="lbl_total_text" class="font-mono text-kp-blue-700">Rp 0</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Jumlah Dibayar / DP <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">Rp</span>
                                    <input type="text" id="in_bayar" name="jumlahbayar_transaksionline" 
                                           value="{{ number_format(old('jumlahbayar_transaksionline', isset($transaksi) ? $transaksi->jumlahbayar_transaksionline : 0), 0, ',', '.') }}" 
                                           required onkeyup="formatInput(this)"
                                           class="w-full h-10 pl-9 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                                <span class="font-medium text-slate-600">Sisa Piutang:</span>
                                <span id="lbl_piutang" class="font-mono font-bold text-slate-900 text-sm">Rp 0</span>
                            </div>
                        </div>

                        <!-- Tombol Aksi Submit Transaksi -->
                        <div class="pt-3 border-t border-slate-100 space-y-2">
                            <button type="submit" id="btnSimpanOrder"
                                    class="w-full py-2.5 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                <span>{{ isset($transaksi) ? 'Simpan Perubahan Order' : 'Simpan Transaksi Order' }}</span>
                            </button>
                            <a href="{{ route('transaksionline.index') }}" 
                               class="w-full py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-xl transition flex items-center justify-center text-center">
                                Batal
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </form>

        <!-- ============================================================== -->
        <!-- MODAL TAMBAH PELANGGAN BARU (PERSIS TRANSAKSI KORAN)           -->
        <!-- ============================================================== -->
        <div id="modalPelangganBaru" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
                
                <div class="px-6 py-4 bg-gradient-to-r from-kp-blue-50/90 via-white to-blue-50/30 border-b border-kp-blue-100/80 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-kp-blue-600 text-white flex items-center justify-center shadow-xs shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Tambah Pelanggan Baru</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5 font-normal">Daftarkan data pemesan ke buku master pelanggan (CRM)</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModalPelangganBaru()" 
                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer text-lg leading-none"
                            title="Tutup Modal">&times;</button>
                </div>

                <div class="p-5 sm:p-6 space-y-3.5 text-xs">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Nama Pemesan / Instansi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="modal_nama_instansi" placeholder="Nama instansi / pemesan"
                               class="w-full h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-normal text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Kategori Pelanggan <span class="text-rose-500">*</span>
                            </label>
                            <select id="modal_jenis_klien" 
                                    class="w-full h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-normal text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                <option value="Perorangan">Perorangan</option>
                                <option value="Swasta" selected>Perusahaan / Swasta</option>
                                <option value="Pemda">Instansi / Dinas Pemda</option>
                                <option value="BUMN">BUMN / BUMD</option>
                                <option value="Agensi">Biro Iklan / Agensi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                NPWP / NIK (16 Digit)
                            </label>
                            <input type="text" id="modal_npwp_nik" placeholder="Nomor NPWP atau NIK" 
                                   class="w-full h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-mono font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Nama PIC (Penanggung Jawab)
                            </label>
                            <input type="text" id="modal_kontak_pic" placeholder="Nama kontak PIC" 
                                   class="w-full h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-normal text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Nomor WhatsApp / HP
                            </label>
                            <input type="text" id="modal_no_telp" placeholder="08xxxxxxxxxx" 
                                   class="w-full h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-normal text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Email Billing / e-Faktur
                            </label>
                            <input type="email" id="modal_email" placeholder="email@instansi.com" 
                                   class="w-full h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-normal text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Plafon Kredit (Opsional)
                            </label>
                            <div class="relative">
                                <span class="absolute left-2.5 top-2 text-xs text-slate-400 font-normal pointer-events-none">Rp</span>
                                <input type="text" id="modal_credit_limit" placeholder="0" 
                                       class="w-full h-9 pl-8 pr-3 bg-white border border-slate-300 rounded-lg text-xs font-mono font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Alamat Lengkap Domisili / Kantor
                        </label>
                        <textarea id="modal_alamat" rows="2" placeholder="Alamat lengkap instansi / kantor pemesan" 
                                  class="w-full p-2.5 bg-white border border-slate-300 rounded-lg text-xs font-normal text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeModalPelangganBaru()" 
                            class="px-4 py-2 bg-white hover:bg-slate-100 active:bg-slate-200 border border-slate-300 text-slate-700 hover:text-slate-900 rounded-lg text-xs font-medium transition shadow-2xs cursor-pointer">
                        Batal
                    </button>
                    <button type="button" onclick="simpanPelangganOnTheFly()" id="btnSimpanPelangganModal" 
                            class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-lg text-xs font-medium shadow-xs hover:shadow transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Simpan &amp; Pilih</span>
                    </button>
                </div>

            </div>
        </div>

        <!-- ============================================================== -->
        <!-- MODAL TAMBAH SALES / AE BARU (PERSIS TRANSAKSI KORAN)          -->
        <!-- ============================================================== -->
        <div id="modalSalesAeBaru" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
                
                <!-- Header Modal -->
                <div class="px-6 py-4 bg-gradient-to-r from-kp-blue-50/90 via-white to-blue-50/30 border-b border-kp-blue-100/80 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-kp-blue-600 text-white flex items-center justify-center shadow-xs shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Tambah Sales / AE</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5 font-normal">Daftarkan petugas Account Executive atau jalur pemasaran</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModalSalesAeBaru()" 
                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer text-lg leading-none"
                            title="Tutup Modal">&times;</button>
                </div>

                <!-- Form Body -->
                <div class="p-5 sm:p-6 space-y-3.5 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Kategori Sales <span class="text-rose-500">*</span>
                            </label>
                            <select id="modal_sales_jenis" onchange="autoGenerateKodeSalesModal()"
                                    class="w-full h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-normal text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                <option value="STRUKTURAL">Account Executive (Kode S)</option>
                                <option value="KORESPONDEN">Koresponden (Kode K)</option>
                                <option value="FO">Loket Kantor (FO)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Kode Sales <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex gap-1.5">
                                <input type="text" id="modal_sales_kode" required maxlength="20"
                                       class="w-full h-9 px-3 bg-white border border-slate-300 rounded-lg font-mono font-medium text-xs uppercase text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"
                                       placeholder="Kode sales">
                                <button type="button" onclick="autoGenerateKodeSalesModal()" title="Buat Kode Otomatis"
                                        class="h-9 px-2.5 bg-kp-blue-50 hover:bg-kp-blue-100 text-kp-blue-700 border border-kp-blue-200 rounded-lg transition cursor-pointer flex items-center justify-center shadow-2xs shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Nama Lengkap Sales / AE <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="modal_sales_nama" placeholder="Nama sales / AE"
                               class="w-full h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-normal text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeModalSalesAeBaru()" 
                            class="px-4 py-2 bg-white hover:bg-slate-100 active:bg-slate-200 border border-slate-300 text-slate-700 hover:text-slate-900 rounded-lg text-xs font-medium transition shadow-2xs cursor-pointer">
                        Batal
                    </button>
                    <button type="button" onclick="simpanSalesAeOnTheFly()" id="btnSimpanSalesAeModal" 
                            class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-lg text-xs font-medium shadow-xs hover:shadow transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Simpan &amp; Pilih</span>
                    </button>
                </div>

            </div>
        </div>

    </div>

    <!-- ============================================================== -->
    <!-- JAVASCRIPT ENGINE LENGKAP UNTUK AUTOCOMPLETE & CALCULATOR     -->
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

            let html = list.map((item, idx) => {
                return `
                    <button type="button" onclick="selectPelangganItemById(${item.id})"
                            class="w-full text-left p-2.5 rounded-xl hover:bg-kp-blue-50/70 transition flex items-center justify-between gap-3 group cursor-pointer border border-transparent hover:border-kp-blue-200">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-semibold text-xs text-slate-900 group-hover:text-kp-blue-700">${item.nama_instansi}</span>
                                <span class="px-1.5 py-0.2 text-[9px] font-semibold rounded bg-slate-100 text-slate-700 border border-slate-200 uppercase">${item.jenis_klien || 'Umum'}</span>
                                <span class="text-[10px] font-mono text-slate-400 font-medium">[${item.kode_pelanggan || 'PLG'}]</span>
                            </div>
                            <div class="text-[11px] text-slate-500 truncate mt-0.5 flex items-center gap-2">
                                ${item.no_telp ? `<span>${item.no_telp}</span>` : ''}
                                ${item.alamat ? `<span class="truncate max-w-[200px]">| ${item.alamat}</span>` : ''}
                            </div>
                        </div>
                        <span class="text-[10px] text-kp-blue-600 font-semibold group-hover:underline shrink-0">Pilih ↵</span>
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
            if (item.npwp_nik && document.getElementById('npwp_nik')) document.getElementById('npwp_nik').value = item.npwp_nik;
            if (item.email && document.getElementById('email_pemasang')) document.getElementById('email_pemasang').value = item.email;

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
            if (item.no_telp) {
                const telpSpan = document.getElementById('textTelpPelanggan') || telpEl.querySelector('span');
                if (telpSpan) telpSpan.textContent = item.no_telp;
                telpEl.classList.remove('hidden');
            } else {
                telpEl.classList.add('hidden');
            }

            const alamatEl = document.getElementById('labelAlamatPelanggan');
            if (item.alamat) {
                const almtSpan = document.getElementById('textAlamatPelanggan') || alamatEl.querySelector('span');
                if (almtSpan) almtSpan.textContent = item.alamat;
                alamatEl.classList.remove('hidden');
            } else {
                alamatEl.classList.add('hidden');
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

        // --- SALES AE CHANGE ---
        function handleSalesAeChange(select) {
            const opt = select.options[select.selectedIndex];
            if (opt && opt.value) {
                const nama = opt.getAttribute('data-nama') || '';
                document.getElementById('sales_iklanonline').value = nama;
                document.getElementById('display_sales_name').value = nama;
            }
        }

        // --- PLATFORM CHECKBOX & BENTUK ---
        function togglePlatformCard(checkbox) {
            const card = checkbox.closest('.platform-card');
            const desc = card.querySelector('.platform-desc');

            if (checkbox.checked) {
                card.classList.remove('bg-white', 'border-slate-200', 'text-slate-800');
                card.classList.add('bg-sky-50', 'border-kp-blue-600', 'text-kp-blue-950', 'font-bold', 'shadow-2xs');
                if (desc) {
                    desc.classList.remove('text-slate-400');
                    desc.classList.add('text-kp-blue-700', 'font-medium');
                }
            } else {
                card.classList.add('bg-white', 'border-slate-200', 'text-slate-800');
                card.classList.remove('bg-sky-50', 'border-kp-blue-600', 'text-kp-blue-950', 'font-bold', 'shadow-2xs');
                if (desc) {
                    desc.classList.add('text-slate-400');
                    desc.classList.remove('text-kp-blue-700', 'font-medium');
                }
            }
            updateTotalPlatformBadge();
            updateSimulatorLive();
        }

        function toggleKategoriCard(checkbox) {
            const card = checkbox.closest('.kategori-card');
            const desc = card.querySelector('.kategori-desc');

            if (checkbox.checked) {
                card.classList.remove('bg-white', 'border-slate-200', 'text-slate-800');
                card.classList.add('bg-sky-50', 'border-kp-blue-600', 'text-kp-blue-950', 'font-bold', 'shadow-2xs');
                if (desc) {
                    desc.classList.remove('text-slate-400');
                    desc.classList.add('text-kp-blue-700', 'font-medium');
                }
            } else {
                card.classList.add('bg-white', 'border-slate-200', 'text-slate-800');
                card.classList.remove('bg-sky-50', 'border-kp-blue-600', 'text-kp-blue-950', 'font-bold', 'shadow-2xs');
                if (desc) {
                    desc.classList.add('text-slate-400');
                    desc.classList.remove('text-kp-blue-700', 'font-medium');
                }
            }
            updateSimulatorLive();
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

        function updateTotalPlatformBadge() {
            const checkedBoxes = document.querySelectorAll('.platform-checkbox:checked');
            const badge = document.getElementById('badge_total_platform');
            if (badge) {
                if (checkedBoxes.length <= 1) {
                    badge.innerText = checkedBoxes.length + ' Media Dipilih';
                    badge.className = 'px-2 py-0.5 bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 rounded-md text-[11px] font-semibold';
                } else {
                    badge.innerText = checkedBoxes.length + ' Media (Paket)';
                    badge.className = 'px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md text-[11px] font-semibold';
                }
            }
        }

        function toggleBentuk(radio) {
            document.querySelectorAll('.bentuk-card').forEach(card => {
                card.classList.remove('bg-sky-50', 'border-kp-blue-600', 'text-kp-blue-950', 'font-bold', 'shadow-2xs');
                card.classList.add('bg-white', 'border-slate-200', 'text-slate-700', 'font-medium');
            });
            const activeCard = radio.closest('.bentuk-card');
            if (activeCard) {
                activeCard.classList.remove('bg-white', 'border-slate-200', 'text-slate-700', 'font-medium');
                activeCard.classList.add('bg-sky-50', 'border-kp-blue-600', 'text-kp-blue-950', 'font-bold', 'shadow-2xs');
            }
            updateSimulatorLive();
        }

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
                lblTitle.innerHTML = 'Tarif Total Paket Iklan <span class="text-rose-500">*</span>';
            } else {
                lblTitle.innerHTML = 'Tarif Iklan (Per Tayang / Hari) <span class="text-rose-500">*</span>';
            }
            hitungSemua();
        }

        // --- KALKULASI UANG & KASIR ---
        function formatRupiah(num) {
            if (!num) return '0';
            return new Intl.NumberFormat('id-ID').format(num);
        }

        function cleanNumber(str) {
            if (!str) return 0;
            return parseFloat(str.toString().replace(/[^0-9]/g, '')) || 0;
        }

        function formatInput(el) {
            const v = cleanNumber(el.value);
            el.value = formatRupiah(v);
            hitungSemua();
        }

        function hitungSemua() {
            const harga = cleanNumber(document.getElementById('in_harga').value);
            const diskon = cleanNumber(document.getElementById('in_diskon').value);
            const bayar = cleanNumber(document.getElementById('in_bayar').value);
            const skema = document.querySelector('input[name="skema_pembayaran"]:checked')?.value || 'sekali_tayang';
            const qty = (skema === 'paket') ? 1 : (parseFloat(document.getElementById('in_qty').value) || 1);

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

            document.getElementById('lbl_subtotal').innerText = 'Rp ' + formatRupiah(subtotal);
            document.getElementById('lbl_ppn').innerText = 'Rp ' + formatRupiah(ppn);
            document.getElementById('lbl_total_text').innerText = 'Rp ' + formatRupiah(total);

            const elPiutang = document.getElementById('lbl_piutang');
            elPiutang.innerText = 'Rp ' + formatRupiah(piutang);

            const badgeStatus = document.getElementById('badge_status_bayar');
            if (badgeStatus) {
                if (piutang > 0) {
                    badgeStatus.className = 'px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-amber-100 text-amber-800';
                    badgeStatus.innerText = 'Piutang / Tempo';
                } else {
                    badgeStatus.className = 'px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-100 text-emerald-800';
                    badgeStatus.innerText = 'Lunas';
                }
            }

            const prevQty = document.getElementById('prev_qty');
            if (prevQty) {
                prevQty.innerText = (skema === 'paket') ? 'Paket Publikasi' : (qty + ' Hari / Kali');
            }

            // Sinkronisasi Insentif & Komisi Fleksibel
            updateInsentifFromDpp(subtotal);
            updateKomisiFromDpp(subtotal);
        }

        function updateInsentifFromDpp(dpp) {
            const dppVal = dpp !== undefined ? dpp : (cleanNumber(document.getElementById('lbl_subtotal')?.innerText) || 0);
            const inputPersen = document.getElementById('insentif_persen');
            const inputNominal = document.getElementById('insentif_transaksionline');
            if (!inputPersen || !inputNominal) return;
            const persen = parseFloat(inputPersen.value) || 0;
            if (persen > 0) {
                const nominal = Math.round((dppVal * persen) / 100);
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
            if (inputNominal) {
                inputNominal.value = formatRupiah(nominal);
            }
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
            const dppVal = dpp !== undefined ? dpp : (cleanNumber(document.getElementById('lbl_subtotal')?.innerText) || 0);
            const inputPersen = document.getElementById('komisi_persen');
            const inputNominal = document.getElementById('komisi_transaksionline');
            if (!inputPersen || !inputNominal) return;
            const persen = parseFloat(inputPersen.value) || 0;
            if (persen > 0) {
                const nominal = Math.round((dppVal * persen) / 100);
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
            if (inputNominal) {
                inputNominal.value = formatRupiah(nominal);
            }
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

        // --- MODAL SALES / AE ON-THE-FLY ---
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
                btn.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <span>Simpan &amp; Pilih</span>
                `;

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
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <span>Simpan &amp; Pilih</span>
                `;
                alert('Terjadi kesalahan koneksi.');
            });
        }

        // --- KATEGORI FORMAT PRODUK CHECKBOX ---
        function toggleKategoriCard(checkbox) {
            const card = checkbox.closest('.kategori-card');
            const desc = card.querySelector('.kategori-desc');

            if (checkbox.checked) {
                card.classList.remove('bg-white', 'border-slate-200', 'text-slate-800');
                card.classList.add('bg-sky-50', 'border-kp-blue-600', 'text-kp-blue-950', 'font-bold', 'shadow-2xs');
                if (desc) {
                    desc.classList.remove('text-slate-400');
                    desc.classList.add('text-kp-blue-700', 'font-medium');
                }
            } else {
                card.classList.add('bg-white', 'border-slate-200', 'text-slate-800');
                card.classList.remove('bg-sky-50', 'border-kp-blue-600', 'text-kp-blue-950', 'font-bold', 'shadow-2xs');
                if (desc) {
                    desc.classList.add('text-slate-400');
                    desc.classList.remove('text-kp-blue-700', 'font-medium');
                }
            }
            updateSimulatorLive();
        }

        // --- STATUS ALUR PRODUKSI ---
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

        // --- SIMULASI LIVE MINIATUR ORDER PUBLIKASI ---
        function updateSimulatorLive() {
            const instansi = document.getElementById('inputManualNamaPemasang').value.trim() || 'Belum diisi';
            const spk = document.getElementById('no_spk')?.value.trim() || '-';
            const produk = document.getElementById('produk_jasa').value.trim() || '-';
            const pic = document.getElementById('nama_pic').value.trim();
            const jabatan = document.getElementById('jabatan_pic').value.trim();
            const tglMuat = document.getElementById('tanggal_muatiklanonline').value;
            const materi = document.getElementById('materi_iklan').value.trim() || 'Belum diisi';
            const qty = parseFloat(document.getElementById('in_qty')?.value) || 1;
            const skema = document.querySelector('input[name="skema_pembayaran"]:checked')?.value || 'sekali_tayang';

            document.getElementById('prev_instansi').innerText = instansi;
            const prevSpk = document.getElementById('prev_spk');
            if (prevSpk) prevSpk.innerText = spk;

            document.getElementById('prev_produk').innerText = produk;
            document.getElementById('prev_pic').innerText = pic ? (pic + (jabatan ? ` (${jabatan})` : '')) : '-';
            document.getElementById('prev_tgl').innerText = tglMuat || '-';
            document.getElementById('prev_materi').innerText = 'Materi: ' + materi;

            const prevQty = document.getElementById('prev_qty');
            if (prevQty) {
                prevQty.innerText = (skema === 'paket') ? 'Paket Publikasi' : (qty + ' Hari / Kali');
            }

            // Render Kategori Format tags
            const katCont = document.getElementById('prev_kategori_tags');
            if (katCont) {
                const checkedKats = Array.from(document.querySelectorAll('.kategori-checkbox:checked')).map(cb => cb.value);
                if (checkedKats.length > 0) {
                    katCont.innerHTML = checkedKats.map(k => `
                        <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[10px] font-semibold">${k}</span>
                    `).join('');
                } else {
                    katCont.innerHTML = `<span class="text-slate-400 italic text-[10px]">Belum ada format dipilih</span>`;
                }
            }

            // Render platform tags
            const tagsCont = document.getElementById('prev_platform_tags');
            const checkedBoxes = Array.from(document.querySelectorAll('.platform-checkbox:checked')).map(cb => cb.value);
            if (checkedBoxes.length > 0) {
                tagsCont.innerHTML = checkedBoxes.map(p => `
                    <span class="px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 text-[10px] font-semibold">${p}</span>
                `).join('');
            } else {
                tagsCont.innerHTML = `<span class="text-slate-400 italic text-[10px]">Belum ada saluran terpilih</span>`;
            }

            // Status Produksi Badge
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
                stBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold border ' + config.cls;
            }
        }

        // --- MODAL TAMBAH PELANGGAN BARU ---
        function openModalPelangganBaru() {
            document.getElementById('modalPelangganBaru').classList.remove('hidden');
        }

        function closeModalPelangganBaru() {
            document.getElementById('modalPelangganBaru').classList.add('hidden');
        }

        function simpanPelangganOnTheFly() {
            const nama = document.getElementById('modal_nama_instansi').value.trim();
            const jenis = document.getElementById('modal_jenis_klien').value;
            const npwp = document.getElementById('modal_npwp_nik')?.value.trim() || '';
            const email = document.getElementById('modal_email')?.value.trim() || '';
            const pic = document.getElementById('modal_kontak_pic')?.value.trim() || '';
            const telp = document.getElementById('modal_no_telp').value.trim();
            const alamat = document.getElementById('modal_alamat').value.trim();
            const limit = document.getElementById('modal_credit_limit')?.value.trim() || '';
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
                    kontak_pic: pic,
                    no_telp: telp,
                    alamat: alamat,
                    credit_limit: limit
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <span>Simpan &amp; Pilih</span>
                `;

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
                        kontak_pic: newP.kontak_pic,
                        total_piutang_koran: 0
                    });

                    closeModalPelangganBaru();
                    selectPelangganItemById(newP.id);
                } else {
                    alert('Gagal menyimpan pelanggan baru: ' + (data.message || 'Coba lagi'));
                }
            })
            .catch(err => {
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

        function formatIndoDateStr(dateStr) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            const y = parseInt(parts[0]);
            const m = parseInt(parts[1]) - 1;
            const d = parseInt(parts[2]);
            const dateObj = new Date(y, m, d);
            const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            return `${dayNames[dateObj.getDay()]}, ${d} ${monthNames[m]} ${y}`;
        }

        function syncDateIndoOnline() {
            const tglMulai = document.getElementById('tanggal_muatiklanonline')?.value;
            const textMulai = document.getElementById('textTglMulaiIndo');
            if (textMulai) {
                textMulai.innerText = tglMulai ? formatIndoDateStr(tglMulai) : '';
            }

            const tglSelesai = document.getElementById('tanggal_selesai_tayang')?.value;
            const textSelesai = document.getElementById('textTglSelesaiIndo');
            if (textSelesai) {
                textSelesai.innerText = tglSelesai ? 'Selesai: ' + formatIndoDateStr(tglSelesai) : '';
            }
        }

        function stepJumlahTayang(delta) {
            const input = document.getElementById('in_qty');
            if (!input) return;
            let val = parseInt(input.value) || 1;
            val = Math.max(1, val + delta);
            input.value = val;
            hitungSemua();
        }

        document.addEventListener('DOMContentLoaded', function() {
            hitungSemua();
            updateTotalPlatformBadge();
            syncDateIndoOnline();
            updateSimulatorLive();
        });
    </script>
    @endpush
</x-app-layout>
