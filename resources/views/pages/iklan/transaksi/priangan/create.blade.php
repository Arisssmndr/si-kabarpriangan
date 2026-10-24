<x-app-layout>
    <div class="space-y-6">

        <!-- ============================================================== -->
        <!-- HEADER HALAMAN: STANDAR SELARAS KORAN & ONLINE                -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        {{ isset($transaksi) ? 'Ubah Data & Jadwal Order Siaran Priangan TV' : 'Entri Order Penayangan Priangan TV' }}
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5 font-normal">
                        {{ isset($transaksi) ? 'Formulir koreksi dan reschedule penayangan siaran televisi Priangan TV.' : 'Formulir booking penayangan siaran iklan, blocking time, talkshow, dan spot TV.' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <div class="text-[11px] font-medium text-slate-500">No. Faktur</div>
                    <div class="font-mono text-sm font-bold text-kp-blue-700">{{ $nofakturpriangan }}</div>
                </div>
                <a href="{{ route('transaksipriangan.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-medium rounded-lg shadow-xs transition cursor-pointer">
                    &larr; <span>Kembali ke Transaksi TV</span>
                </a>
            </div>
        </div>

        <!-- Notice Deadline & Cut-Off Siar Priangan TV (3 Status: Netral / Kuning / Merah) -->
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
                'passed'  => 'Lewat Batas Produksi & Siar Hari Ini (' . $jamCutoffStr . ' WIB)',
                'warning' => '1 Jam Menjelang Batas Produksi & Siar Hari Ini (' . $jamCutoffStr . ' WIB)',
                default   => 'Batas Siar & Tayang Besok: ' . $jamCutoffStr . ' WIB',
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
                            Slot Siar Terdekat: <span class="{{ $bannerEdisiClass }}">{{ $edisiTerdekat }}</span>
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 self-end sm:self-center font-mono text-[11px] font-semibold px-2.5 py-1 rounded-md border shadow-2xs {{ $bannerClockClass }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ now()->format('H:i') }} WIB</span>
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
        <form id="formTransaksiPriangan" method="POST" action="{{ isset($transaksi) ? route('transaksipriangan.update', $transaksi->id) : route('transaksipriangan.store') }}" class="space-y-6">
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

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- ============================================================== -->
                <!-- PANEL KIRI: INPUT DATA TRANSAKSI                               -->
                <!-- ============================================================== -->
                <div class="lg:col-span-7 space-y-5">

                    <!-- SEKSI 1: DATA PEMESAN & INSTANSI (SERAGAM DENGAN KORAN & ONLINE) -->
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

                        <!-- Baris Input Pemasang: 2 Kolom Sejajar (Autocomplete vs Input Manual) -->
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

                                    <!-- Card Master Terpilih -->
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
                                                    <span id="textTelpPelanggan">{{ $selectedOldPelanggan->no_telp ?? '' }}</span>
                                                </span>
                                                <span id="labelAlamatPelanggan" class="inline-flex items-center gap-1 truncate {{ empty($selectedOldPelanggan?->alamat) ? 'hidden' : '' }}">
                                                    <span id="textAlamatPelanggan">{{ $selectedOldPelanggan->alamat ?? '' }}</span>
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

                                    <!-- Autocomplete Pencarian Typeahead -->
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
                                    <span>Email Tagihan / e-Faktur</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Kirim Invoice / Bukti Tayang</span>
                                </label>
                                <input type="email" name="email_pemasang" id="email_pemasang"
                                       placeholder="email@instansi.com"
                                       value="{{ old('email_pemasang', isset($transaksi) ? ($transaksi->pelanggan?->email ?? '') : ($selectedOldPelanggan?->email ?? '')) }}"
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-normal text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>

                        <!-- Baris 2: Produk / Jasa & No. SPK / PO / Kontrak -->
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

                        <!-- Baris 3: Nama Penanggung Jawab (PIC) & Jabatan PIC -->
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

                        <!-- Baris 4: Alamat & Telepon -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-slate-700 mb-1.5 block">
                                    Alamat Lengkap
                                </label>
                                <textarea name="alamat_pemasangpriangan" id="alamat_pemasangpriangan" rows="2"
                                          placeholder="Alamat pemesan"
                                          oninput="updateSimulatorLive()"
                                          class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-normal text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs resize-none">{{ old('alamat_pemasangpriangan', isset($transaksi) ? $transaksi->alamat_pemasangpriangan : ($selectedOldPelanggan?->alamat ?? '')) }}</textarea>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-700 mb-1.5 block">
                                    No. Telepon / WhatsApp
                                </label>
                                <div class="relative flex items-center">
                                    <input type="text" name="no_telp" id="no_telp"
                                           placeholder="08xxxxxxxxxx"
                                           value="{{ old('no_telp', isset($transaksi) ? $transaksi->no_telp : ($selectedOldPelanggan?->no_telp ?? '')) }}"
                                           oninput="updateSimulatorLive()"
                                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-normal text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- Sales AE & Komisioner (Identik & Selaras) -->
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
                                    <input type="text" id="display_sales_name" readonly
                                           value="{{ $salesAes->firstWhere('id', $defaultAeId)?->nama_ae ?? 'Front Office (Loket Langsung)' }}"
                                           class="w-full h-7 px-2.5 bg-slate-100 border border-slate-200 rounded-md text-[11px] text-slate-600 font-medium">
                                </div>

                                <!-- Card 2: Komisioner / Perantara -->
                                <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/90 space-y-2.5 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <label class="text-xs font-medium text-slate-700 whitespace-nowrap">
                                            Perantara / Komisioner
                                        </label>
                                        <span class="text-[10px] text-slate-400 font-normal shrink-0">Opsional</span>
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
                                                <input type="text" name="komisi_transaksipriangan" id="komisi_transaksipriangan" 
                                                       value="{{ old('komisi_transaksipriangan', isset($transaksi) && $transaksi->komisi_transaksipriangan ? number_format($transaksi->komisi_transaksipriangan, 0, ',', '.') : 0) }}"
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

                    <!-- SEKSI 2: SPESIFIKASI PENAYANGAN PRIANGAN TV -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-xs font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-[11px] font-bold">2</span>
                                <span>Spesifikasi Penayangan Siaran TV</span>
                            </span>
                        </div>

                        <!-- 1. PROGRAM ACARA & JENIS IKLAN TV -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Jenis Iklan / Format Siaran <span class="text-rose-500">*</span>
                                </label>
                                <select name="id_iklanpriangan" id="id_iklanpriangan" required onchange="handleJenisIklanTvChange(this)"
                                        class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                    @foreach($iklanpriangan as $ip)
                                        <option value="{{ $ip->id }}" 
                                                data-harga="{{ $ip->harga ?? 0 }}"
                                                data-nama="{{ $ip->jenis_iklanpriangan ?? $ip->nama_jenis ?? 'Iklan TV' }}"
                                                {{ (old('id_iklanpriangan', isset($transaksi) ? $transaksi->id_iklanpriangan : '') == $ip->id) ? 'selected' : '' }}>
                                            {{ $ip->jenis_iklanpriangan ?? $ip->nama_jenis ?? 'Iklan TV' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Durasi Tayang per Spot <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" name="durasi_detik" id="durasi_detik"
                                           value="{{ old('durasi_detik', isset($transaksi) ? $transaksi->durasi_detik : 30) }}" min="5" max="3600" required
                                           oninput="updateSimulatorLive()"
                                           class="w-full h-10 px-3 pr-16 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                    <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">Detik</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. JUDUL / MATERI IKLAN & JAM TAYANG -->
                        <div class="pt-3 border-t border-slate-100 space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Judul / Materi Iklan TV <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="materi_iklan" id="materi_iklan" 
                                       value="{{ old('materi_iklan', isset($transaksi) ? $transaksi->materi_iklan : '') }}" required 
                                       placeholder="Judul / materi iklan"
                                       oninput="updateSimulatorLive()"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">
                                        Tanggal Penayangan <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="date" name="tanggal_muatiklanpriangan" id="tanggal_muatiklanpriangan" 
                                           value="{{ old('tanggal_muatiklanpriangan', isset($transaksi) ? $transaksi->tanggal_muatiklanpriangan?->format('Y-m-d') : date('Y-m-d')) }}" required
                                           onchange="updateSimulatorLive()"
                                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">
                                        Jam Tayang / Slot <span class="text-[10px] text-slate-400 font-normal">(Opsional)</span>
                                    </label>
                                    <input type="text" name="jam_tayang" id="jam_tayang" 
                                           value="{{ old('jam_tayang', isset($transaksi) ? $transaksi->jam_tayang : '19:00 WIB (Prime Time)') }}" 
                                           placeholder="19:00 WIB / Prime Time"
                                           oninput="updateSimulatorLive()"
                                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">
                                        Jumlah Tayang / Spot <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" id="in_qty" name="total_muatiklanpriangan" 
                                               value="{{ old('total_muatiklanpriangan', isset($transaksi) ? $transaksi->total_muatiklanpriangan : '1') }}" min="1" required 
                                               oninput="hitungSemua()"
                                               class="w-full h-10 px-3 pr-16 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                        <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">Spot</span>
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
                            <span class="text-xs font-semibold text-slate-900 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span>Ringkasan Order Priangan TV</span>
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border bg-purple-100 text-purple-800 border-purple-300">
                                Broadcast TV
                            </span>
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 space-y-2 text-xs">
                            <div class="flex">
                                <span class="w-24 font-medium text-slate-500 shrink-0">Klien</span>
                                <span class="w-2">:</span>
                                <span id="prev_klien" class="font-bold text-slate-900 truncate">Belum dipilih</span>
                            </div>
                            <div class="flex">
                                <span class="w-24 font-medium text-slate-500 shrink-0">Jenis Iklan</span>
                                <span class="w-2">:</span>
                                <span id="prev_jenis" class="font-semibold text-slate-800 truncate">-</span>
                            </div>
                            <div class="flex">
                                <span class="w-24 font-medium text-slate-500 shrink-0">Durasi / Jam</span>
                                <span class="w-2">:</span>
                                <span id="prev_durasi_jam" class="font-normal text-slate-700">30 Detik • 19:00 WIB</span>
                            </div>
                            <div class="flex">
                                <span class="w-24 font-medium text-slate-500 shrink-0">Tgl Tayang</span>
                                <span class="w-2">:</span>
                                <span id="prev_tgl" class="font-mono text-slate-700">-</span>
                            </div>
                            <div class="flex">
                                <span class="w-24 font-medium text-slate-500 shrink-0">Total Spot</span>
                                <span class="w-2">:</span>
                                <span id="prev_qty" class="font-semibold text-kp-blue-700">1 Spot Tayang</span>
                            </div>
                            <div class="pt-1.5 border-t border-slate-200 text-[10px] text-slate-500 italic truncate" id="prev_materi">
                                Materi: Belum diisi
                            </div>
                        </div>
                    </div>

                    <!-- SEKSI RINCIAN BIAYA & PEMBAYARAN -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-xs font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-[11px] font-bold">3</span>
                                <span>Rincian Biaya &amp; Pembayaran</span>
                            </span>
                        </div>

                        <!-- Baris Input Keuangan -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Tarif Iklan TV (Per Spot) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">Rp</span>
                                    <input type="text" id="in_harga" name="harga_transaksipriangan" 
                                           value="{{ number_format(old('harga_transaksipriangan', isset($transaksi) ? $transaksi->harga_transaksipriangan : 0), 0, ',', '.') }}" 
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
                                    <input type="text" id="in_diskon" name="diskon_transaksipriangan" 
                                           value="{{ number_format(old('diskon_transaksipriangan', isset($transaksi) ? $transaksi->diskon_transaksipriangan : 0), 0, ',', '.') }}" 
                                           onkeyup="formatInput(this)"
                                           class="w-full h-10 pl-9 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                            </div>

                            <!-- Rincian Subtotal & PPN -->
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5 text-xs">
                                <div class="flex justify-between text-slate-600">
                                    <span>Subtotal:</span>
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
                                    <input type="text" id="in_bayar" name="jumlahbayar_transaksipriangan" 
                                           value="{{ number_format(old('jumlahbayar_transaksipriangan', isset($transaksi) ? $transaksi->jumlahbayar_transaksipriangan : 0), 0, ',', '.') }}" 
                                           required onkeyup="formatInput(this)"
                                           class="w-full h-10 pl-9 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                                <span class="font-medium text-slate-600">Sisa Piutang:</span>
                                <span id="lbl_piutang" class="font-mono font-bold text-slate-900 text-sm">Rp 0</span>
                            </div>

                            <!-- Tombol Simpan -->
                            <div class="pt-2">
                                <button type="submit" id="btnSubmitOrder"
                                        class="w-full h-11 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-xl text-xs font-bold tracking-wide shadow-md hover:shadow-lg transition flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    <span>{{ isset($transaksi) ? 'Simpan Perubahan Order TV' : 'Simpan Order Priangan TV' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </form>

        <!-- ============================================================== -->
        <!-- MODAL TAMBAH PELANGGAN BARU                                    -->
        <!-- ============================================================== -->
        <div id="modalPelangganBaru" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
                <div class="px-6 py-4 bg-gradient-to-r from-kp-blue-50/90 via-white to-blue-50/30 border-b border-kp-blue-100/80 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-kp-blue-600 text-white flex items-center justify-center shadow-xs shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Tambah Pelanggan Baru</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5 font-normal">Daftarkan instansi atau perorangan baru langsung ke sistem</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModalPelangganBaru()" 
                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer text-lg leading-none">&times;</button>
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
        <!-- MODAL TAMBAH SALES / AE BARU                                   -->
        <!-- ============================================================== -->
        <div id="modalSalesAeBaru" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
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
                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer text-lg leading-none">&times;</button>
                </div>

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

                // Default lunas untuk tunai
                const totalText = document.getElementById('lbl_total_text').innerText;
                const totalNum = cleanNumber(totalText);
                if (totalNum > 0) {
                    document.getElementById('in_bayar').value = formatRupiah(totalNum);
                }
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
            }, 120);
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
                        Tidak ada pelanggan yang cocok dengan "<strong>${keyword}</strong>"
                        <div class="mt-2">
                            <button type="button" onclick="pilihSebagaiManual('${keyword.replace(/'/g, "\\'")}')" class="px-2.5 py-1 bg-kp-blue-50 text-kp-blue-700 hover:bg-kp-blue-100 rounded-lg font-semibold text-[11px] cursor-pointer">
                                Gunakan "${keyword}" sebagai Pemasang Manual
                            </button>
                        </div>
                    </div>
                `;
                return;
            }

            let html = '';
            matched.slice(0, 10).forEach(p => {
                const escapedName = (p.nama_instansi || '').replace(/'/g, "\\'");
                html += `
                    <div onclick="selectPelangganItemById(${p.id})"
                         class="p-2.5 hover:bg-slate-50 rounded-xl cursor-pointer transition flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 truncate">${p.nama_instansi}</span>
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 uppercase">${p.jenis_klien || 'Perorangan'}</span>
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5 truncate flex items-center gap-2">
                                <span class="font-mono text-slate-500">${p.kode_pelanggan}</span>
                                ${p.no_telp ? `<span>• ${p.no_telp}</span>` : ''}
                                ${p.alamat ? `<span>• ${p.alamat}</span>` : ''}
                            </div>
                        </div>
                        <span class="text-[11px] text-kp-blue-600 font-semibold shrink-0">Pilih &rarr;</span>
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

            if (p.alamat) document.getElementById('alamat_pemasangpriangan').value = p.alamat;
            if (p.no_telp) document.getElementById('no_telp').value = p.no_telp;
            if (p.kontak_pic) document.getElementById('nama_pic').value = p.kontak_pic;
            if (p.npwp_nik && document.getElementById('npwp_nik')) document.getElementById('npwp_nik').value = p.npwp_nik;
            if (p.email && document.getElementById('email_pemasang')) document.getElementById('email_pemasang').value = p.email;

            document.getElementById('labelNamaPelanggan').innerText = p.nama_instansi;
            document.getElementById('badgeJenisPelanggan').innerText = p.jenis_klien || 'Perorangan';
            document.getElementById('labelKodePelanggan').innerText = '[' + p.kode_pelanggan + ']';

            const npwpEl = document.getElementById('labelNpwpPelanggan');
            const textNpwp = document.getElementById('textNpwpPelanggan');
            if (npwpEl && textNpwp) {
                if (p.npwp_nik) {
                    textNpwp.innerText = p.npwp_nik;
                    npwpEl.classList.remove('hidden');
                } else {
                    npwpEl.classList.add('hidden');
                }
            }

            const emailEl = document.getElementById('labelEmailPelanggan');
            const textEmail = document.getElementById('textEmailPelanggan');
            if (emailEl && textEmail) {
                if (p.email) {
                    textEmail.innerText = p.email;
                    emailEl.classList.remove('hidden');
                } else {
                    emailEl.classList.add('hidden');
                }
            }
            
            const telpEl = document.getElementById('labelTelpPelanggan');
            if (telpEl) {
                const telpSpan = document.getElementById('textTelpPelanggan') || telpEl.querySelector('span');
                if (telpSpan) telpSpan.innerText = p.no_telp || '';
                telpEl.className = p.no_telp ? 'inline-flex items-center gap-1 truncate' : 'hidden';
            }

            const alamatEl = document.getElementById('labelAlamatPelanggan');
            if (alamatEl) {
                const almtSpan = document.getElementById('textAlamatPelanggan') || alamatEl.querySelector('span');
                if (almtSpan) almtSpan.innerText = p.alamat || '';
                alamatEl.className = p.alamat ? 'inline-flex items-center gap-1 truncate' : 'hidden';
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

        // --- SALES AE CHANGE ---
        function handleSalesAeChange(select) {
            const opt = select.options[select.selectedIndex];
            if (opt && opt.value) {
                const nama = opt.getAttribute('data-nama') || '';
                document.getElementById('sales_iklanpriangan').value = nama;
                document.getElementById('display_sales_name').value = nama;
            }
        }

        function handleJenisIklanTvChange(select) {
            const opt = select.options[select.selectedIndex];
            if (opt) {
                const harga = parseFloat(opt.getAttribute('data-harga')) || 0;
                if (harga > 0 && cleanNumber(document.getElementById('in_harga').value) === 0) {
                    document.getElementById('in_harga').value = formatRupiah(harga);
                    hitungSemua();
                }
            }
            updateSimulatorLive();
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
            const qty = Math.max(1, parseInt(document.getElementById('in_qty').value) || 1);
            const ppnPersen = parseFloat(document.getElementById('ppn_persen').value) || 12;

            const totalOmset = harga * qty;
            const subtotal = Math.max(0, totalOmset - diskon);
            const ppn = Math.round(subtotal * (ppnPersen / 100));
            const totalTagihan = subtotal + ppn;

            const jenisBayar = document.getElementById('jenis_pembayaran').value;
            let bayar = cleanNumber(document.getElementById('in_bayar').value);

            if (jenisBayar === 'Tunai' && bayar === 0 && totalTagihan > 0) {
                bayar = totalTagihan;
                document.getElementById('in_bayar').value = formatRupiah(bayar);
            }

            const piutang = Math.max(0, totalTagihan - bayar);

            document.getElementById('lbl_subtotal').innerText = 'Rp ' + formatRupiah(subtotal);
            document.getElementById('lbl_ppn').innerText = 'Rp ' + formatRupiah(ppn);
            document.getElementById('lbl_total_text').innerText = 'Rp ' + formatRupiah(totalTagihan);
            document.getElementById('lbl_piutang').innerText = 'Rp ' + formatRupiah(piutang);

            const badge = document.getElementById('badge_status_bayar');
            if (badge) {
                if (piutang <= 0 && totalTagihan > 0) {
                    badge.className = 'px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-100 text-emerald-800';
                    badge.innerText = 'Lunas';
                } else if (bayar > 0) {
                    badge.className = 'px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-amber-100 text-amber-800';
                    badge.innerText = 'Uang Muka (DP)';
                } else {
                    badge.className = 'px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-rose-100 text-rose-800';
                    badge.innerText = 'Belum Bayar';
                }
            }

            updateSimulatorLive();
        }

        function handleKomisiPersenChange(persen) {
            const p = parseFloat(persen) || 0;
            const harga = cleanNumber(document.getElementById('in_harga').value);
            const diskon = cleanNumber(document.getElementById('in_diskon').value);
            const qty = Math.max(1, parseInt(document.getElementById('in_qty').value) || 1);
            const subtotal = Math.max(0, (harga * qty) - diskon);

            const nominal = Math.round((subtotal * p) / 100);
            document.getElementById('komisi_transaksipriangan').value = formatRupiah(nominal);
        }

        function handleKomisiNominalInput(el) {
            const nominal = cleanNumber(el.value);
            el.value = formatRupiah(nominal);

            const harga = cleanNumber(document.getElementById('in_harga').value);
            const diskon = cleanNumber(document.getElementById('in_diskon').value);
            const qty = Math.max(1, parseInt(document.getElementById('in_qty').value) || 1);
            const subtotal = Math.max(0, (harga * qty) - diskon);

            if (subtotal > 0) {
                const persen = ((nominal / subtotal) * 100).toFixed(1);
                document.getElementById('komisi_persen').value = persen;
            }
        }

        function updateSimulatorLive() {
            const namaKlien = document.getElementById('nama_pemasangpriangan').value.trim() || document.getElementById('inputManualNamaPemasang').value.trim() || 'Belum dipilih';
            document.getElementById('prev_klien').innerText = namaKlien;

            const selJenis = document.getElementById('id_iklanpriangan');
            const opt = selJenis.options[selJenis.selectedIndex];
            document.getElementById('prev_jenis').innerText = opt ? opt.text : '-';

            const durasi = document.getElementById('durasi_detik').value || '30';
            const jam = document.getElementById('jam_tayang').value || '19:00 WIB';
            document.getElementById('prev_durasi_jam').innerText = durasi + ' Detik • ' + jam;

            const tgl = document.getElementById('tanggal_muatiklanpriangan').value || '-';
            document.getElementById('prev_tgl').innerText = tgl;

            const qty = document.getElementById('in_qty').value || '1';
            document.getElementById('prev_qty').innerText = qty + ' Spot Tayang';

            const materi = document.getElementById('materi_iklan').value.trim();
            document.getElementById('prev_materi').innerText = materi ? 'Materi: ' + materi : 'Materi: Belum diisi';
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
                        total_piutang: 0
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

        // --- MODAL TAMBAH SALES / AE BARU ---
        function openModalSalesAeBaru() {
            document.getElementById('modalSalesAeBaru').classList.remove('hidden');
        }

        function closeModalSalesAeBaru() {
            document.getElementById('modalSalesAeBaru').classList.add('hidden');
        }

        function autoGenerateKodeSalesModal() {
            const jenis = document.getElementById('modal_sales_jenis').value;
            fetch("{{ route('sales-ae.generate-kode') }}?jenis=" + encodeURIComponent(jenis))
                .then(r => r.json())
                .then(d => {
                    if (d.kode) {
                        document.getElementById('modal_sales_kode').value = d.kode;
                    }
                })
                .catch(e => console.error(e));
        }

        function simpanSalesAeOnTheFly() {
            const kode = document.getElementById('modal_sales_kode').value.trim();
            const nama = document.getElementById('modal_sales_nama').value.trim();
            const jenis = document.getElementById('modal_sales_jenis').value;
            const btn = document.getElementById('btnSimpanSalesAeModal');

            if (!kode || !nama) {
                alert('Kode sales dan Nama sales wajib diisi.');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span>Menyimpan...</span>';

            fetch("{{ route('sales-ae.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    kode_ae: kode,
                    nama_ae: nama,
                    jenis_kode: jenis
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

                if (data.success && data.sales_ae) {
                    const newAe = data.sales_ae;
                    const select = document.getElementById('sales_ae_id');
                    const opt = document.createElement('option');
                    opt.value = newAe.id;
                    opt.setAttribute('data-kode', newAe.kode_ae);
                    opt.setAttribute('data-nama', newAe.nama_ae);
                    opt.text = newAe.nama_ae + ' — ' + (newAe.jenis_kode || 'AE');
                    select.appendChild(opt);
                    select.value = newAe.id;

                    handleSalesAeChange(select);
                    closeModalSalesAeBaru();
                } else {
                    alert('Gagal mendaftarkan sales baru.');
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

        document.addEventListener('DOMContentLoaded', function() {
            hitungSemua();
            updateSimulatorLive();
        });
    </script>
    @endpush
</x-app-layout>