<x-app-layout>
    <style>
        .sorot-error-active {
            border-color: #f43f5e !important;
            box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.25) !important;
            background-color: rgba(255, 241, 242, 0.35) !important;
            animation: sorotPulse 1.2s ease-in-out infinite alternate;
        }
        @keyframes sorotPulse {
            from { box-shadow: 0 0 0 2px rgba(244, 63, 94, 0.2); }
            to { box-shadow: 0 0 0 5px rgba(244, 63, 94, 0.45); }
        }
    </style>

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

        $todayDateStr = $nowTime->toDateString();
        $besokDateStr = $nowTime->copy()->addDay()->toDateString();
        $lusaDateStr  = $nowTime->copy()->addDays(2)->toDateString();

        $unlockedDatesArr = $configs['unlocked_dates'] ?? [];
        $closedDatesArr   = $configs['closed_dates'] ?? [];
        $isBesokClosed    = in_array($besokDateStr, $closedDatesArr, true) && !in_array($besokDateStr, $unlockedDatesArr, true);

        $besok = $nowTime->copy()->addDay()->locale('id')->translatedFormat('l, d F Y');
        $lusa  = $nowTime->copy()->addDays(2)->locale('id')->translatedFormat('l, d F Y');
        $edisiTerdekat    = $isBesokClosed ? $lusa : $besok;
        $defaultFirstDate = $isBesokClosed ? $lusaDateStr : $besokDateStr;

        $bannerTitleText = match($cutoffState) {
            'passed'  => 'Cut-Off Terkunci (' . $jamCutoffStr . ' WIB)',
            'warning' => 'Mendekati Batas Cut-Off (' . $jamCutoffStr . ' WIB)',
            default   => 'Batas Cut-Off Harian: ' . $jamCutoffStr . ' WIB',
        };

        $selectedOldPelanggan = old('pelanggan_id') 
            ? $pelanggans->firstWhere('id', old('pelanggan_id')) 
            : null;
        $oldNamaPemasang = old('nama_pemasang', $selectedOldPelanggan->nama_instansi ?? '');
        $currentJenisBayar = old('jenis_pembayaran', 'Tunai');
        $isTunai = ($currentJenisBayar === 'Tunai');

        $oldMedia = (array) old('media_terpilih', []);
        $hasCtk = in_array('CTK', $oldMedia, true);
        $hasOnl = in_array('ONL', $oldMedia, true);
        $hasPtv = in_array('PTV', $oldMedia, true);
    @endphp

    <div class="space-y-6 pb-12">

        <!-- Header Halaman Standar Selaras Transaksi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Entri Paket Iklan Lintas Media
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl font-normal">
                        Gabungan Iklan Cetak, Online, dan Priangan TV dalam 1 Faktur Paket terpadu.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0 flex-wrap sm:flex-nowrap">
                <div class="text-right hidden sm:block">
                    <div class="text-[11px] font-semibold text-slate-500">No. Faktur Paket</div>
                    <div class="font-mono text-base font-bold text-kp-blue-700 leading-tight">{{ $nofakturpaket }}</div>
                </div>
                <a href="{{ route('transaksipaket.index') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                    &larr; <span>Daftar Paket</span>
                </a>
            </div>
        </div>

        <!-- Notice Deadline & Cut-Off Harian -->
        <div class="rounded-xl border bg-white border-slate-200 text-slate-800 px-4 py-3 shadow-2xs transition">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm">
                    <span class="text-slate-900 font-bold">{{ $bannerTitleText }}</span>
                    <span class="text-slate-300 hidden sm:inline">|</span>
                    <span class="text-slate-600 font-normal">
                        Edisi Terdekat: <span class="text-kp-blue-700 font-semibold">{{ $edisiTerdekat }}</span>
                    </span>
                </div>
                <div class="flex items-center font-mono text-xs font-semibold px-2.5 h-8 rounded-lg border bg-slate-50 border-slate-200 text-slate-600 self-end sm:self-center">
                    <span>{{ $nowTime->format('H:i') }} WIB</span>
                </div>
            </div>
        </div>

        <!-- Feedback Validasi Error Global jika ada -->
        @if (isset($errors) && $errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1">
                <div class="font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    Mohon periksa kembali formulir Anda:
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-2 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Utama Paket Iklan -->
        <form id="formPaketIklan" method="POST" action="{{ route('transaksipaket.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <!-- Hidden Technical Fields -->
            <input type="hidden" name="nofaktur_paket" value="{{ $nofakturpaket }}">
            <input type="hidden" name="tanggal_transaksi" value="{{ $todayDateStr }}">
            <input type="hidden" name="kode_template" id="kode_template" value="{{ old('kode_template', 'CUSTOM') }}">
            <input type="hidden" name="nama_paket" id="nama_paket" value="{{ old('nama_paket', 'Paket Iklan Lintas Media') }}">
            <input type="hidden" name="jenis_pembayaran" id="jenis_pembayaran" value="{{ $currentJenisBayar }}">
            <input type="hidden" name="pelanggan_id" id="pelanggan_id" value="{{ old('pelanggan_id', $selectedOldPelanggan->id ?? '') }}">
            <input type="hidden" name="ppn_persen" id="ppn_persen" value="{{ $configs['ppn_persen'] }}">

            <!-- Media Cetak Technical Fields -->
            <input type="hidden" name="ctk_kategori_form" id="ctk_kategori_form" value="{{ old('ctk_kategori_form', 'DISPLAY') }}">
            <input type="hidden" name="ctk_id_iklan" id="ctk_id_iklan" value="{{ old('ctk_id_iklan', '') }}">
            <input type="hidden" name="ctk_warna" id="ctk_warna" value="{{ old('ctk_warna', 'BW') }}">
            <input type="hidden" name="ctk_harga_satuan" id="ctk_harga_satuan" value="{{ old('ctk_harga_satuan', 0) }}">
            <input type="hidden" name="ctk_tanggal_list" id="ctk_tanggal_list" value="{{ old('ctk_tanggal_list', $defaultFirstDate) }}">
            <input type="hidden" name="ctk_lebar_kolom" id="ctk_lebar_kolom" value="{{ old('ctk_lebar_kolom', 2) }}">
            <input type="hidden" name="ctk_tinggi_mm" id="ctk_tinggi_mm" value="{{ old('ctk_tinggi_mm', 100) }}">
            <input type="hidden" name="ctk_jumlah_baris" id="ctk_jumlah_baris" value="{{ old('ctk_jumlah_baris', 2) }}">
            <input type="hidden" name="ctk_naskah_baris" id="ctk_naskah_baris" value="{{ old('ctk_naskah_baris', '') }}">

            <!-- Media Online Technical Fields -->
            <input type="hidden" name="onl_id_iklan" id="onl_id_iklan" value="{{ old('onl_id_iklan', '') }}">
            <input type="hidden" name="onl_kategori_tab" id="onl_kategori_tab" value="{{ old('onl_kategori_tab', 'PORTAL_BERITA') }}">
            <input type="hidden" name="onl_harga_satuan" id="onl_harga_satuan" value="{{ old('onl_harga_satuan', 0) }}">
            <input type="hidden" name="onl_tanggal_list" id="onl_tanggal_list" value="{{ old('onl_tanggal_list', $defaultFirstDate) }}">
            <input type="hidden" name="onl_produk_jasa" id="onl_produk_jasa" value="{{ old('onl_produk_jasa', 'Paket Online') }}">

            <!-- Media TV Technical Fields -->
            <input type="hidden" name="ptv_id_iklan" id="ptv_id_iklan" value="{{ old('ptv_id_iklan', '') }}">
            <input type="hidden" name="ptv_kategori_tab" id="ptv_kategori_tab" value="{{ old('ptv_kategori_tab', 'PORTAL_WEB') }}">
            <input type="hidden" name="ptv_harga_satuan" id="ptv_harga_satuan" value="{{ old('ptv_harga_satuan', 0) }}">
            <input type="hidden" name="ptv_tanggal_list" id="ptv_tanggal_list" value="{{ old('ptv_tanggal_list', $defaultFirstDate) }}">
            <input type="hidden" name="ptv_produk_jasa" id="ptv_produk_jasa" value="{{ old('ptv_produk_jasa', 'Paket Priangan TV') }}">
            <div id="containerTvSelectedProductIds"></div>

            <!-- Financial Totals -->
            <input type="hidden" name="nilai_diskon" id="nilai_diskon" value="{{ old('nilai_diskon', 0) }}">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- ============================================================== -->
                <!-- PANEL KIRI: INPUT DATA TRANSAKSI                               -->
                <!-- ============================================================== -->
                <div class="lg:col-span-7 space-y-5">

                    <!-- SEKSI 1: CARA BAYAR & DATA PEMASANG (COPY PERSIS DARI TRANSAKSI KORAN) -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">1</span>
                                <span>Data Pemesan</span>
                            </span>
                        </div>

                        <!-- PILIHAN SKEMA PEMBAYARAN -->
                        <div>
                            <label class="text-sm font-semibold text-slate-700 mb-2 block">
                                Skema Pembayaran <span class="text-rose-500">*</span>
                            </label>

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

                        <!-- Jatuh Tempo (Kondisional jika Kredit) -->
                        <div id="boxJatuhTempo" class="{{ $isTunai ? 'hidden' : '' }} pt-1">
                            <label class="text-sm font-semibold text-slate-700 mb-1.5 block">
                                Tanggal Jatuh Tempo Pembayaran <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="jatuh_tempo" id="jatuh_tempo"
                                   value="{{ old('jatuh_tempo', $nowTime->copy()->addDays(14)->toDateString()) }}"
                                   min="{{ $todayDateStr }}"
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                        </div>

                        <!-- Baris Input Pemasang: 2 Kolom Sejajar (Pilih dari Data Pelanggan vs Input Nama Langsung) -->
                        <div class="space-y-3 pt-3 border-t border-slate-100">
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
                                         class="{{ $selectedOldPelanggan ? '' : 'hidden' }} px-3 py-2 rounded-xl bg-kp-blue-50/70 border border-kp-blue-200/80 flex items-center justify-between gap-3 shadow-2xs min-h-[42px]">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span id="labelNamaPelanggan" class="font-bold text-slate-900 text-sm truncate">{{ $selectedOldPelanggan->nama_instansi ?? '' }}</span>
                                                <span id="badgeJenisPelanggan" class="px-2 py-0.5 text-xs font-semibold rounded bg-white text-slate-700 border border-slate-200 uppercase">{{ $selectedOldPelanggan->jenis_klien ?? '' }}</span>
                                                <span id="labelKodePelanggan" class="text-xs font-mono text-slate-500">[{{ $selectedOldPelanggan->kode_pelanggan ?? '' }}]</span>
                                            </div>
                                            <div class="text-xs text-slate-500 flex items-center gap-3 mt-0.5 flex-wrap">
                                                <span id="labelTelpPelanggan" class="inline-flex items-center gap-1 truncate {{ empty($selectedOldPelanggan?->no_telp) ? 'hidden' : '' }}">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    <span>{{ $selectedOldPelanggan->no_telp ?? '' }}</span>
                                                </span>
                                                <span id="labelAlamatPelanggan" class="inline-flex items-center gap-1 truncate {{ empty($selectedOldPelanggan?->alamat) ? 'hidden' : '' }}">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <span>{{ $selectedOldPelanggan->alamat ?? '' }}</span>
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
                                                   placeholder="Cari pelanggan terdaftar..."
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
                                                <button type="button" onclick="openModalPelangganBaru()" class="flex items-center gap-1.5 text-xs font-semibold text-kp-blue-600 hover:underline cursor-pointer">
                                                    <svg class="w-3 h-3 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                    </svg>    
                                                    <span>Pelanggan Baru</span>
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
                                        <input type="text" name="nama_pemasang" id="inputManualNamaPemasang" required
                                               oninput="handleManualNamaInput(this.value)"
                                               placeholder="Nama instansi atau perorangan..."
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
                                    <input type="text" name="telepon_pemasang" id="telepon_pemasang"
                                           placeholder="08xxxxxxxxxx"
                                           value="{{ old('telepon_pemasang', $selectedOldPelanggan?->no_telp ?? '') }}"
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
                                       value="{{ old('nama_pic', $selectedOldPelanggan?->kontak_pic ?? '') }}"
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>

                        <!-- Baris: Alamat Lengkap -->
                        <div class="pt-1">
                            <label class="text-sm font-semibold text-slate-700 mb-1.5 block">
                                Alamat
                            </label>
                            <textarea name="alamat_pemasang" id="alamat_pemasang" rows="2"
                                      placeholder="Alamat lengkap pemesan..."
                                      class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs resize-none">{{ old('alamat_pemasang', $selectedOldPelanggan?->alamat ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- SEKSI PEMILIHAN SALURAN MEDIA & TEMPLATE PAKET -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">2</span>
                                <span>Saluran Media &amp; Paket</span>
                            </span>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                Minimal 2 Media
                            </span>
                        </div>

                        <!-- 3 Pilihan Media (Sleek & Clean - Mengikuti Gaya Pilihan Jenis Foto 2) -->
                        <div id="containerPilihanMedia" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- Cetak Card Toggle -->
                            <label id="labelToggleCtk" class="relative overflow-hidden p-3.5 rounded-xl border transition cursor-pointer select-none flex flex-col justify-center min-h-[64px] {{ $hasCtk ? 'border-2 border-kp-blue-600 bg-sky-50/70 text-slate-900 ring-1 ring-kp-blue-500/20 shadow-2xs font-bold' : 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50/50 font-medium' }}">
                                <input type="checkbox" name="media_terpilih[]" value="CTK" id="chkMediaCtk" onchange="toggleMediaSection('CTK', this.checked)" {{ $hasCtk ? 'checked' : '' }} class="sr-only">
                                
                                <!-- Badge Ceklist Lancip Segitiga Sudut Kiri Atas (Foto 2 Style) -->
                                <span class="badge-corner-media-ctk absolute top-0 left-0 w-7 h-7 pointer-events-none z-10 {{ $hasCtk ? '' : 'hidden' }}">
                                    <svg class="w-7 h-7" viewBox="0 0 28 28">
                                        <path d="M0 0 L28 0 L0 28 Z" fill="#0A72AC" />
                                        <path d="M4 9 L8 13 L14 6" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>

                                <div class="pl-2">
                                    <div class="text-sm font-bold leading-tight text-slate-900">Iklan Cetak</div>
                                    <div class="text-xs text-slate-500 font-normal mt-1">Koran</div>
                                </div>
                            </label>

                            <!-- Online Card Toggle -->
                            <label id="labelToggleOnl" class="relative overflow-hidden p-3.5 rounded-xl border transition cursor-pointer select-none flex flex-col justify-center min-h-[64px] {{ $hasOnl ? 'border-2 border-kp-blue-600 bg-sky-50/70 text-slate-900 ring-1 ring-kp-blue-500/20 shadow-2xs font-bold' : 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50/50 font-medium' }}">
                                <input type="checkbox" name="media_terpilih[]" value="ONL" id="chkMediaOnl" onchange="toggleMediaSection('ONL', this.checked)" {{ $hasOnl ? 'checked' : '' }} class="sr-only">
                                
                                <!-- Badge Ceklist Lancip Segitiga Sudut Kiri Atas (Foto 2 Style) -->
                                <span class="badge-corner-media-onl absolute top-0 left-0 w-7 h-7 pointer-events-none z-10 {{ $hasOnl ? '' : 'hidden' }}">
                                    <svg class="w-7 h-7" viewBox="0 0 28 28">
                                        <path d="M0 0 L28 0 L0 28 Z" fill="#0A72AC" />
                                        <path d="M4 9 L8 13 L14 6" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>

                                <div class="pl-2">
                                    <div class="text-sm font-bold leading-tight text-slate-900">Iklan Online</div>
                                    <div class="text-xs text-slate-500 font-normal mt-1">Portal &amp; Medsos</div>
                                </div>
                            </label>

                            <!-- TV Card Toggle -->
                            <label id="labelTogglePtv" class="relative overflow-hidden p-3.5 rounded-xl border transition cursor-pointer select-none flex flex-col justify-center min-h-[64px] {{ $hasPtv ? 'border-2 border-kp-blue-600 bg-sky-50/70 text-slate-900 ring-1 ring-kp-blue-500/20 shadow-2xs font-bold' : 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50/50 font-medium' }}">
                                <input type="checkbox" name="media_terpilih[]" value="PTV" id="chkMediaPtv" onchange="toggleMediaSection('PTV', this.checked)" {{ $hasPtv ? 'checked' : '' }} class="sr-only">
                                
                                <!-- Badge Ceklist Lancip Segitiga Sudut Kiri Atas (Foto 2 Style) -->
                                <span class="badge-corner-media-ptv absolute top-0 left-0 w-7 h-7 pointer-events-none z-10 {{ $hasPtv ? '' : 'hidden' }}">
                                    <svg class="w-7 h-7" viewBox="0 0 28 28">
                                        <path d="M0 0 L28 0 L0 28 Z" fill="#0A72AC" />
                                        <path d="M4 9 L8 13 L14 6" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>

                                <div class="pl-2">
                                    <div class="text-sm font-bold leading-tight text-slate-900">Priangan TV</div>
                                    <div class="text-xs text-slate-500 font-normal mt-1">Siar &amp; Video</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- ============================================================== -->
                    <!-- BAGIAN I: FORM SPESIFIKASI & JADWAL IKLAN CETAK                -->
                    <!-- (COPY PERSIS DARI FORM TRANSAKSI KORAN)                        -->
                    <!-- ============================================================== -->
                    <div id="sectionContainerCetak" class="{{ $hasCtk ? '' : 'hidden' }} space-y-5">
                        
                        <!-- SEKSI 2 CETAK: JENIS IKLAN CETAK -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">A</span>
                                    <span>Spesifikasi Iklan Cetak (HU. Kabar Priangan)</span>
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    Edisi Koran
                                </span>
                            </div>

                            @php
                                $normalizedIklanCtk = $iklankoran->map(function($p) {
                                    if ($p->tipe_dasar === 'KHUSUS') $p->tipe_dasar = 'DISPLAY';
                                    return $p;
                                });
                                $groupedCtk = $normalizedIklanCtk->groupBy('tipe_dasar');
                                $tabOrderCtk = ['DISPLAY', 'KOLOM', 'BARIS', 'ADVERTORIAL', 'BONUS'];
                                $defaultTabCtk = old('ctk_kategori_form', 'DISPLAY');
                                $initProdCtk = old('ctk_id_iklan') ? ($normalizedIklanCtk->firstWhere('id', old('ctk_id_iklan')) ?? null) : null;
                            @endphp

                            <!-- Segmented Tab Controls Cetak -->
                            <div class="flex flex-wrap gap-2 p-1.5 bg-slate-100 rounded-2xl border border-slate-200/80">
                                @foreach($tabOrderCtk as $tabKey)
                                    @if($groupedCtk->has($tabKey))
                                        @php
                                            $countItems = $groupedCtk[$tabKey]->count();
                                            $tabLabel = match($tabKey) {
                                                'BARIS'       => 'Iklan Baris',
                                                'KOLOM'       => 'Iklan Kolom',
                                                'DISPLAY'     => 'Iklan Display',
                                                'ADVERTORIAL' => 'Iklan Advertorial',
                                                'BONUS'       => 'Layanan Masyarakat',
                                                default       => $tabKey
                                            };
                                            $isTabActive = ($defaultTabCtk === $tabKey);
                                        @endphp
                                        <button type="button" onclick="switchProductTabCtk('{{ $tabKey }}')"
                                                id="tabBtnCtk_{{ $tabKey }}"
                                                class="tab-product-btn-ctk px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 {{ $isTabActive ? 'bg-kp-blue-600 text-white font-semibold shadow-xs border border-kp-blue-600' : 'bg-white text-slate-700 font-medium border border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                                            <span>{{ $tabLabel }}</span>
                                            <span class="px-1.5 py-0.5 rounded-full text-xs font-semibold {{ $isTabActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                                                {{ $countItems }}
                                            </span>
                                        </button>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Tab Panels Cetak -->
                            @foreach($tabOrderCtk as $tabKey)
                                @if($groupedCtk->has($tabKey))
                                    @php
                                        $items = $groupedCtk[$tabKey];
                                        $isTabActive = ($defaultTabCtk === $tabKey);
                                    @endphp
                                    <div id="tabContentCtk_{{ $tabKey }}" class="tab-product-content-ctk {{ $isTabActive ? '' : 'hidden' }}">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                            @foreach($items as $item)
                                                @php
                                                    $isSelected = ($initProdCtk && $initProdCtk->id == $item->id);
                                                @endphp
                                                <button type="button" 
                                                        onclick="pilihProdukIklanCtk({{ $item->id }}, '{{ $item->kode_jenis }}', '{{ $item->tipe_dasar }}', '{{ $item->mode_warna }}', '{{ $item->cara_hitung }}', {{ $item->tarif_dasar }}, {{ $item->is_fixed_size ? 'true' : 'false' }}, {{ $item->default_kolom ?? 2 }}, {{ $item->default_tinggi_mm ?? 100 }}, {{ $item->min_order ?? 2 }})"
                                                        id="cardProdukCtk_{{ $item->id }}"
                                                        class="produk-card-ctk text-left p-3 rounded-xl border transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2 {{ $isSelected ? 'border-2 border-kp-blue-600 bg-kp-blue-50/40 ring-1 ring-kp-blue-500/20 shadow-xs font-bold' : 'border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50 font-medium' }}">
                                                    
                                                    <!-- Badge Ceklist Lancip Segitiga Sudut Kiri Atas -->
                                                    <span class="badge-corner-check-ctk absolute top-0 left-0 w-7 h-7 pointer-events-none z-10 {{ $isSelected ? '' : 'hidden' }}">
                                                        <svg class="w-7 h-7" viewBox="0 0 28 28">
                                                            <path d="M0 0 L28 0 L0 28 Z" fill="#0A72AC" />
                                                            <path d="M4 9 L8 13 L14 6" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>

                                                    <div class="text-sm text-slate-900 leading-snug line-clamp-2">
                                                        {{ $item->nama_jenis }}
                                                    </div>
                                                    <div class="text-xs text-kp-blue-700 font-mono">
                                                        @if($item->cara_hitung === 'PER_BARIS')
                                                            Rp {{ number_format($item->tarif_dasar, 0, ',', '.') }} / baris
                                                        @elseif($item->cara_hitung === 'PER_MMK')
                                                            Rp {{ number_format($item->tarif_dasar, 0, ',', '.') }} / mmk
                                                        @else
                                                            Rp {{ number_format($item->tarif_dasar, 0, ',', '.') }}
                                                        @endif
                                                    </div>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <!-- Baris Penempatan Cetak -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 mb-1.5 block">Penempatan Halaman</label>
                                    <select name="ctk_kop_kota_id" id="ctk_kop_kota_id"
                                            class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                        @foreach($kopKotas as $kota)
                                            <option value="{{ $kota->id }}">
                                                {{ $kota->nama_penempatan }} @if($kota->surcharge_persen > 0) (+{{ number_format($kota->surcharge_persen, 2) }}%) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 mb-1.5 block">Rubrik Iklan</label>
                                    <select name="ctk_kop_iklan_id" id="ctk_kop_iklan_id"
                                            class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                        @foreach($kopIklans as $kop)
                                            <option value="{{ $kop->id }}">{{ $kop->nama_kop }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Placeholder Belum Pilih Jenis Iklan Cetak -->
                            <div id="placeholderPilihProdukCtk" class="p-6 text-center rounded-xl border border-dashed border-slate-300 bg-slate-50/80 {{ $initProdCtk ? 'hidden' : '' }}">
                                <p class="font-semibold text-slate-700 text-sm">Pilih Jenis Iklan Cetak Terlebih Dahulu</p>
                                <p class="text-xs text-slate-400 mt-1">Form spesifikasi ukuran dan format naskah akan tampil setelah Anda memilih salah satu jenis iklan cetak di atas.</p>
                            </div>

                            <!-- Form Iklan Baris -->
                            <div id="sectionIklanBarisCtk" class="space-y-3 pt-2 border-t border-slate-100 {{ ($initProdCtk && $initProdCtk->tipe_dasar === 'BARIS') ? '' : 'hidden' }}">
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-semibold text-slate-800">Naskah Iklan Baris</label>
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-kp-blue-50 text-kp-blue-800 border border-kp-blue-200">
                                        <span id="labelJumlahBarisCtk">2</span> Baris
                                    </span>
                                </div>
                                <div id="containerBarisRowsCtk" class="space-y-2"></div>
                                <div class="flex items-center justify-between gap-2">
                                    <button type="button" onclick="tambahBarisCtk()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                                        + Tambah Baris
                                    </button>
                                </div>
                            </div>

                            <!-- Form Display / Kolom -->
                            <div id="sectionIklanDisplayCtk" class="space-y-3 pt-2 border-t border-slate-100 {{ ($initProdCtk && $initProdCtk->tipe_dasar !== 'BARIS') ? '' : 'hidden' }}">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Lebar Kolom</label>
                                        <input type="number" id="in_ctk_kolom" min="1" max="7" value="{{ old('ctk_lebar_kolom', 2) }}" oninput="handleDimensiCtkChange()" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tinggi (mm)</label>
                                        <input type="number" id="in_ctk_tinggi" min="20" max="540" value="{{ old('ctk_tinggi_mm', 100) }}" oninput="handleDimensiCtkChange()" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Warna Cetak</label>
                                        <select id="in_ctk_warna" onchange="handleWarnaCtkChange(this.value)" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                            <option value="BW" {{ old('ctk_warna', 'BW') === 'BW' ? 'selected' : '' }}>Hitam Putih (BW)</option>
                                            <option value="FC" {{ old('ctk_warna') === 'FC' ? 'selected' : '' }}>Full Colour (FC)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-xs text-slate-500 pt-1">
                                    <span>Total Ukuran: <strong id="labelTotalMmkCtk" class="text-slate-900 font-mono">{{ $initProdCtk ? '200 mmk' : '-' }}</strong></span>
                                    <span>Tarif Dasar Cetak: <strong id="labelTarifDasarCtk" class="text-kp-blue-700 font-mono">{{ $initProdCtk ? 'Rp 5.000.000' : 'Rp 0' }}</strong></span>
                                </div>
                            </div>

                            <!-- Materi Cetak Upload -->
                            <div id="sectionMateriCtk" class="pt-2 border-t border-slate-100 space-y-3 {{ $initProdCtk ? '' : 'hidden' }}">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">File Materi Cetak (PDF / JPG / ZIP)</label>
                                    <input type="file" name="ctk_file_materi" accept="image/*,.pdf,.zip,.rar" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-kp-blue-50 file:text-kp-blue-700 hover:file:bg-kp-blue-100 cursor-pointer border border-slate-300 rounded-xl p-1">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Keterangan / Naskah Cetak</label>
                                    <textarea name="ctk_keterangan_materi" id="ctk_keterangan_materi" rows="2" placeholder="Catatan redaksi percetakan..." class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs resize-none">{{ old('ctk_keterangan_materi') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- JADWAL EDISI PEMUATAN CETAK (INTERACTIVE MULTI-DATE CALENDAR) -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">A2</span>
                                    <span>Jadwal Terbit Cetak</span>
                                </span>
                                <div id="statusParitasTanggalCtk" class="text-xs font-semibold px-2.5 py-1 rounded-lg border bg-emerald-50 text-emerald-800 border-emerald-200 inline-flex items-center gap-1.5 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span id="badgeParitasCtk">1 Tanggal Dipilih</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-stretch">
                                <!-- Kolom Kiri: Frekuensi & Daftar Tanggal Terpilih -->
                                <div class="md:col-span-5 flex flex-col justify-between space-y-3.5">
                                    <!-- Counter Frekuensi Terbit -->
                                    <div class="p-3 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-2">
                                        <div class="flex items-center justify-between whitespace-nowrap">
                                            <label class="text-xs font-medium text-slate-700 flex items-center gap-1.5 whitespace-nowrap">
                                                <svg class="w-3.5 h-3.5 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span>Frekuensi Terbit</span>
                                                <span class="text-rose-500">*</span>
                                            </label>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center bg-white border border-slate-300 rounded-xl p-1 shadow-2xs">
                                                <button type="button" onclick="stepJumlahMuatCtk(-1)"
                                                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition cursor-pointer select-none">
                                                    −
                                                </button>
                                                <input type="text" inputmode="numeric" name="ctk_total_muat" id="in_qty_ctk"
                                                       value="{{ old('ctk_total_muat', 1) }}"
                                                       oninput="handleJumlahMuatCtkInput(this.value)"
                                                       onblur="handleJumlahMuatCtkBlur(this)"
                                                       autocomplete="off"
                                                       class="w-14 h-8 px-1 text-center font-bold text-sm text-slate-900 border-0 focus:ring-0 focus:outline-none">
                                                <button type="button" onclick="stepJumlahMuatCtk(1)"
                                                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition cursor-pointer select-none">
                                                    +
                                                </button>
                                            </div>
                                            <span class="text-xs font-medium text-slate-700">Edisi</span>
                                        </div>
                                    </div>

                                    <!-- Box Daftar Tanggal Terpilih -->
                                    <div class="flex-1 flex flex-col p-3 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-2">
                                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/60 whitespace-nowrap gap-1.5">
                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <span class="text-sm font-semibold text-slate-700 whitespace-nowrap">Tanggal Terbit:</span>
                                                <span id="labelTotalDatesCountCtk" class="px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-100 text-kp-blue-800 whitespace-nowrap">1 Tanggal</span>
                                            </div>
                                            <button type="button" onclick="resetSemuaTanggalCtk()"
                                                    class="p-1 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer flex items-center justify-center shrink-0"
                                                    title="Reset semua tanggal terpilih">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                        <div id="daftarTanggalChipsCtk" class="space-y-1.5 overflow-y-auto max-h-[195px] pr-0.5">
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
                                    <div id="containerKalenderCtk" class="p-3 bg-white border border-slate-200/90 rounded-xl space-y-2 shadow-2xs transition-all">
                                        <!-- Header Kalender: Bulan Tahun & Navigasi Panah Kompak -->
                                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                                            <div class="flex items-center gap-2">
                                                <span id="calendarMonthTitleCtk" class="text-sm font-bold text-slate-900"></span>
                                                <button type="button" onclick="goToTodayCalendarCtk()"
                                                        class="text-xs font-semibold text-kp-blue-600 hover:text-kp-blue-800 cursor-pointer underline">
                                                    Hari Ini
                                                </button>
                                            </div>
                                            <!-- Navigasi Panah < > -->
                                            <div class="flex items-center gap-1">
                                                <button type="button" onclick="changeCalendarMonthCtk(-1)"
                                                        class="w-6 h-6 rounded hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer"
                                                        title="Bulan Sebelumnya">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                                </button>
                                                <button type="button" onclick="changeCalendarMonthCtk(1)"
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
                                        <div id="calendarDaysGridCtk" class="grid grid-cols-7 gap-1 text-center text-sm font-medium">
                                            <!-- Rendered via JS -->
                                        </div>

                                        <!-- Legenda Simpel Ringkas -->
                                        <div class="flex items-center justify-between text-xs text-slate-500 border-t border-slate-100 pt-2 px-0.5">
                                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-600 inline-block shadow-2xs"></span> Tanggal Terbit</span>
                                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-rose-50 border border-rose-300 inline-block"></span> Libur / Minggu</span>
                                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-amber-50 border border-amber-300 inline-block"></span> Hari Peringatan</span>
                                        </div>
                                    </div>

                                    <!-- Panel Keterangan Hari -->
                                    <div id="panelInfoTanggalCtk" class="p-2.5 rounded-xl border transition-all text-xs bg-slate-50 border-slate-200 space-y-1.5">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span id="infoTanggalLabelCtk" class="font-bold text-xs text-slate-900 whitespace-nowrap">
                                                Klik tanggal di kalender
                                            </span>
                                        </div>
                                        <div id="infoTanggalBadgeContainerCtk" class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-200 text-slate-700 whitespace-nowrap">
                                                Status Hari
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ============================================================== -->
                    <!-- BAGIAN II: FORM SPESIFIKASI & JADWAL IKLAN ONLINE              -->
                    <!-- (COPY PERSIS DARI FORM TRANSAKSI ONLINE)                       -->
                    <!-- ============================================================== -->
                    <div id="sectionContainerOnline" class="{{ $hasOnl ? '' : 'hidden' }} space-y-5">
                        
                        <!-- SEKSI 2 ONLINE: PLATFORM & FORMAT IKLAN -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">B</span>
                                    <span>Platform &amp; Format Iklan Online</span>
                                </span>
                            </div>

                            @php
                                $groupedOnline = $iklanonline->groupBy('tipe_dasar');
                                $tabOrderOnl = ['PORTAL_BERITA', 'INSTAGRAM', 'TIKTOK', 'YOUTUBE', 'PODCAST'];
                                $defaultTabOnl = old('onl_kategori_tab', 'PORTAL_BERITA');
                            @endphp

                            <!-- Tab Platform Pills -->
                            <div class="flex flex-wrap items-center gap-2 p-1.5 bg-slate-100 rounded-2xl border border-slate-200/80">
                                @foreach($tabOrderOnl as $tabKey)
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
                                        $isTabActive = ($defaultTabOnl === $tabKey);
                                    @endphp
                                    <button type="button" onclick="switchPlatformTabOnl('{{ $tabKey }}', '{{ $tabName }}')"
                                            id="tabBtnOnl_{{ $tabKey }}"
                                            class="tab-platform-btn-onl px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 {{ $isTabActive ? 'bg-kp-blue-600 text-white font-semibold shadow-xs border border-kp-blue-600' : 'bg-white text-slate-700 font-medium border border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                                        <span>{{ $tabName }}</span>
                                        <span class="px-1.5 py-0.5 rounded-full text-xs font-semibold {{ $isTabActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $countItems }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>

                            <!-- TAB 1: 9 PORTAL BERITA KABAR PRIANGAN -->
                            <div id="tabContentOnl_PORTAL_BERITA" class="tab-platform-content-onl space-y-3 {{ $defaultTabOnl === 'PORTAL_BERITA' ? '' : 'hidden' }}">
                                <div class="flex items-center justify-end pb-1 gap-2">
                                    <button type="button" onclick="pilihSemuaPortalOnl(true)" class="px-2.5 py-1 text-xs font-semibold text-kp-blue-700 bg-white hover:bg-kp-blue-50 border border-slate-200 rounded-lg transition shadow-2xs cursor-pointer">
                                        Pilih Semua (9 Portal)
                                    </button>
                                    <button type="button" onclick="pilihSemuaPortalOnl(false)" class="px-2.5 py-1 text-xs font-semibold text-slate-600 hover:text-rose-600 bg-white hover:bg-rose-50 border border-slate-200 rounded-lg transition shadow-2xs cursor-pointer">
                                        Reset
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    @foreach($portalList as $pIdx => $portal)
                                        @php
                                            $isPortOnlChecked = is_array(old('onl_portals')) && in_array($portal['id'], old('onl_portals'));
                                        @endphp
                                        <label class="portal-checkbox-card relative overflow-hidden flex flex-col justify-center px-3 py-2.5 rounded-xl border transition cursor-pointer select-none min-h-[68px] {{ $isPortOnlChecked ? 'border-2 border-kp-blue-600 bg-sky-50 text-kp-blue-900 font-bold ring-1 ring-kp-blue-500/20 shadow-2xs' : 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 font-medium' }}">
                                            <input type="checkbox" name="onl_portals[]" value="{{ $portal['id'] }}"
                                                   class="portal-checkbox-onl sr-only"
                                                   {{ $isPortOnlChecked ? 'checked' : '' }}
                                                   onchange="handlePortalCheckboxChangeOnl()">
                                            <span class="badge-corner-check-onl absolute top-0 left-0 w-6 h-6 pointer-events-none z-10 {{ $isPortOnlChecked ? '' : 'hidden' }}">
                                                <svg class="w-6 h-6" viewBox="0 0 24 24">
                                                    <path d="M0 0 L24 0 L0 24 Z" fill="#0A72AC" />
                                                    <path d="M3.5 7.5 L7 11 L13 5" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                            <div class="pl-1.5">
                                                <div class="text-slate-900 text-[11px] sm:text-xs tracking-tight whitespace-nowrap leading-snug">{{ $portal['nama'] }}</div>
                                                <div class="font-mono text-xs text-kp-blue-700 mt-1">Rp {{ number_format($configs['tarif_portal'] ?? 750000, 0, ',', '.') }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- TAB 2-5: MEDSOS & PODCAST -->
                            @foreach(['INSTAGRAM', 'TIKTOK', 'YOUTUBE', 'PODCAST'] as $otherTab)
                                <div id="tabContentOnl_{{ $otherTab }}" class="tab-platform-content-onl space-y-3 {{ $defaultTabOnl === $otherTab ? '' : 'hidden' }}">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach($groupedOnline->get($otherTab, collect()) as $prod)
                                            <button type="button" 
                                                    onclick="toggleProdukIklanOnl({{ $prod->id }}, '{{ $prod->kode_jenis }}', '{{ addslashes($prod->nama_jenis) }}', {{ $prod->tarif_dasar }}, '{{ $otherTab }}')"
                                                    id="cardProdukOnl_{{ $prod->id }}"
                                                    data-id="{{ $prod->id }}"
                                                    data-tarif="{{ $prod->tarif_dasar }}"
                                                    data-nama="{{ $prod->nama_jenis }}"
                                                    data-tab="{{ $otherTab }}"
                                                    class="produk-card-onl text-left p-3.5 rounded-xl border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50 font-medium transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2.5 min-h-[92px]">
                                                <span class="badge-corner-check-onl-card absolute top-0 left-0 w-7 h-7 pointer-events-none z-10 hidden">
                                                    <svg class="w-7 h-7" viewBox="0 0 28 28">
                                                        <path d="M0 0 L28 0 L0 28 Z" fill="#0A72AC" />
                                                        <path d="M4 9 L8 13 L14 6" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <div class="text-sm text-slate-900 leading-snug">{{ $prod->nama_jenis }}</div>
                                                <div class="text-xs text-kp-blue-700 font-mono">Rp {{ number_format($prod->tarif_dasar, 0, ',', '.') }}</div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            <!-- Materi Iklan Online Form -->
                            <div class="pt-2 border-t border-slate-100 space-y-3">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Judul / Naskah Iklan Online</label>
                                    <input type="text" name="onl_materi_iklan" id="onl_materi_iklan" value="{{ old('onl_materi_iklan') }}" placeholder="Judul materi atau tajuk publikasi online..." class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Upload File (Gambar / Video / PDF)</label>
                                        <input type="file" name="onl_file_materi" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-kp-blue-50 file:text-kp-blue-700 hover:file:bg-kp-blue-100 cursor-pointer border border-slate-300 rounded-xl p-1">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Link Drive / Cloud Materi</label>
                                        <input type="url" name="onl_url_materi" value="{{ old('onl_url_materi') }}" placeholder="https://drive.google.com/..." class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Catatan Redaksi Online</label>
                                    <textarea name="onl_catatan" rows="2" placeholder="Catatan atau arahan penayangan redaksi online..." class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs resize-none">{{ old('onl_catatan') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- JADWAL TAYANG ONLINE (INTERACTIVE MULTI-DATE CALENDAR) -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">B2</span>
                                    <span>Jadwal Tayang Online</span>
                                </span>
                                <div id="statusParitasTanggalOnl" class="text-xs font-semibold px-2.5 py-1 rounded-lg border bg-emerald-50 text-emerald-800 border-emerald-200 inline-flex items-center gap-1.5 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span id="badgeParitasOnl">1 Tanggal Dipilih</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-stretch">
                                <!-- Kolom Kiri: Frekuensi & Daftar Tanggal Terpilih -->
                                <div class="md:col-span-5 flex flex-col justify-between space-y-3.5">
                                    <!-- Stepper Frekuensi Tayang -->
                                    <div class="p-3 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-2">
                                        <div class="flex items-center justify-between whitespace-nowrap">
                                            <label class="text-xs font-medium text-slate-700 flex items-center gap-1.5 whitespace-nowrap">
                                                <svg class="w-3.5 h-3.5 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span>Frekuensi Tayang</span>
                                                <span class="text-rose-500">*</span>
                                            </label>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center bg-white border border-slate-300 rounded-xl p-1 shadow-2xs">
                                                <button type="button" onclick="stepJumlahTayangOnl(-1)"
                                                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition cursor-pointer select-none">
                                                    −
                                                </button>
                                                <input type="text" inputmode="numeric" name="onl_total_muat" id="in_qty_onl"
                                                       value="{{ old('onl_total_muat', 1) }}"
                                                       oninput="handleJumlahTayangOnlInput(this.value)"
                                                       onblur="handleJumlahTayangOnlBlur(this)"
                                                       autocomplete="off"
                                                       class="w-14 h-8 px-1 text-center font-bold text-sm text-slate-900 border-0 focus:ring-0 focus:outline-none">
                                                <button type="button" onclick="stepJumlahTayangOnl(1)"
                                                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition cursor-pointer select-none">
                                                    +
                                                </button>
                                            </div>
                                            <span class="text-xs font-medium text-slate-700">Hari</span>
                                        </div>
                                    </div>

                                    <!-- Box Daftar Tanggal Terpilih -->
                                    <div class="flex-1 flex flex-col p-3 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-2">
                                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/60 whitespace-nowrap gap-1.5">
                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <span class="text-sm font-semibold text-slate-700 whitespace-nowrap">Tanggal Tayang:</span>
                                                <span id="labelTotalDatesCountOnl" class="px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-100 text-kp-blue-800 whitespace-nowrap">1 Tanggal</span>
                                            </div>
                                            <button type="button" onclick="resetSemuaTanggalOnl()"
                                                    class="p-1 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer flex items-center justify-center shrink-0"
                                                    title="Reset semua tanggal">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                        <div id="daftarTanggalChipsOnl" class="space-y-1.5 overflow-y-auto max-h-[195px] pr-0.5">
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
                                    <div id="containerKalenderOnl" class="p-3 bg-white border border-slate-200/90 rounded-xl space-y-2 shadow-2xs transition-all">
                                        <!-- Header Kalender: Bulan Tahun & Navigasi Panah Kompak -->
                                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                                            <div class="flex items-center gap-2">
                                                <span id="calendarMonthTitleOnl" class="text-sm font-bold text-slate-900"></span>
                                                <button type="button" onclick="goToTodayCalendarOnl()"
                                                        class="text-xs font-semibold text-kp-blue-600 hover:text-kp-blue-800 cursor-pointer underline">
                                                    Hari Ini
                                                </button>
                                            </div>
                                            <!-- Navigasi Panah < > -->
                                            <div class="flex items-center gap-1">
                                                <button type="button" onclick="changeCalendarMonthOnl(-1)"
                                                        class="w-6 h-6 rounded hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer"
                                                        title="Bulan Sebelumnya">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                                </button>
                                                <button type="button" onclick="changeCalendarMonthOnl(1)"
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
                                        <div id="calendarDaysGridOnl" class="grid grid-cols-7 gap-1 text-center text-sm font-medium">
                                            <!-- Rendered via JS -->
                                        </div>

                                        <!-- Legenda Simpel Ringkas -->
                                        <div class="flex items-center justify-between text-xs text-slate-500 border-t border-slate-100 pt-2 px-0.5">
                                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-600 inline-block shadow-2xs"></span> Tanggal Tayang</span>
                                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-rose-50 border border-rose-300 inline-block"></span> Libur / Minggu</span>
                                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-amber-50 border border-amber-300 inline-block"></span> Hari Peringatan</span>
                                        </div>
                                    </div>

                                    <!-- Panel Keterangan Hari -->
                                    <div id="panelInfoTanggalOnl" class="p-2.5 rounded-xl border transition-all text-xs bg-slate-50 border-slate-200 space-y-1.5">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span id="infoTanggalLabelOnl" class="font-bold text-xs text-slate-900 whitespace-nowrap">
                                                Klik tanggal di kalender
                                            </span>
                                        </div>
                                        <div id="infoTanggalBadgeContainerOnl" class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-200 text-slate-700 whitespace-nowrap">
                                                Status Hari
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ============================================================== -->
                    <!-- BAGIAN III: FORM SPESIFIKASI & JADWAL PRIANGAN TV              -->
                    <!-- (COPY PERSIS DARI FORM TRANSAKSI PRIANGAN)                     -->
                    <!-- ============================================================== -->
                    <div id="sectionContainerTv" class="{{ $hasPtv ? '' : 'hidden' }} space-y-5">
                        
                        <!-- SEKSI 2 TV: JENIS IKLAN & PENEMPATAN PRIANGAN TV -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">C</span>
                                    <span>Jenis Iklan &amp; Penempatan Priangan TV</span>
                                </span>
                            </div>

                            @php
                                $tabOrderTv = ['PORTAL_WEB', 'PODCAST', 'INSTAGRAM', 'TIKTOK', 'BANNER_WEB', 'VIDEO_LIPSUS'];
                                $tabLabelsTv = [
                                    'PORTAL_WEB'   => 'Portal Web',
                                    'PODCAST'      => 'Podcast TV',
                                    'INSTAGRAM'    => 'Instagram',
                                    'TIKTOK'       => 'TikTok',
                                    'BANNER_WEB'   => 'Banner Web',
                                    'VIDEO_LIPSUS' => 'Video Lipsus',
                                ];

                                // Mapping items per tab
                                $itemsByTabTv = [
                                    'PORTAL_WEB'   => collect($portalList),
                                    'PODCAST'      => $iklanpriangan->where('tipe_dasar', 'PODCAST'),
                                    'INSTAGRAM'    => $iklanpriangan->filter(fn($p) => $p->tipe_dasar === 'INSTAGRAM' || str_contains(strtolower($p->nama_jenis), 'instagram')),
                                    'TIKTOK'       => $iklanpriangan->filter(fn($p) => $p->tipe_dasar === 'TIKTOK' || str_contains(strtolower($p->nama_jenis), 'tiktok')),
                                    'BANNER_WEB'   => $iklanpriangan->where('tipe_dasar', 'BANNER_WEB'),
                                    'VIDEO_LIPSUS' => $iklanpriangan->where('tipe_dasar', 'VIDEO_LIPSUS'),
                                ];

                                $defaultTabTv = old('ptv_kategori_tab', 'PORTAL_WEB');
                                if (!isset($itemsByTabTv[$defaultTabTv])) $defaultTabTv = 'PORTAL_WEB';

                                $oldTvPortals = old('portal_iklanpriangan', []);
                                if (is_string($oldTvPortals)) {
                                    $oldTvPortals = array_values(array_filter(array_map('trim', explode(',', $oldTvPortals))));
                                }
                                if (!is_array($oldTvPortals)) $oldTvPortals = [];
                                $initProdTv = old('ptv_id_iklan') ? ($iklanpriangan->firstWhere('id', old('ptv_id_iklan')) ?? null) : null;
                            @endphp

                            <!-- Segmented Tabs TV -->
                            <div class="flex flex-wrap items-center gap-2 p-1.5 bg-slate-100 rounded-2xl border border-slate-200/80">
                                @foreach($tabOrderTv as $tabKey)
                                    @php
                                        $tabName = $tabLabelsTv[$tabKey] ?? $tabKey;
                                        $countItems = ($itemsByTabTv[$tabKey] ?? collect())->count();
                                        $isTabActive = ($defaultTabTv === $tabKey);
                                    @endphp
                                    <button type="button" onclick="switchProductTabTv('{{ $tabKey }}')"
                                            id="tabBtnTv_{{ $tabKey }}"
                                            class="tab-product-btn-tv px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 {{ $isTabActive ? 'bg-kp-blue-600 text-white font-semibold shadow-xs border border-kp-blue-600' : 'bg-white text-slate-700 font-medium border border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                                        <span>{{ $tabName }}</span>
                                        <span class="px-1.5 py-0.5 rounded-full text-xs font-semibold {{ $isTabActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $countItems }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>

                            <!-- Tab Panels TV -->
                            @foreach($tabOrderTv as $tabKey)
                                @php
                                    $tabItems = $itemsByTabTv[$tabKey] ?? collect();
                                    $isTabActive = ($defaultTabTv === $tabKey);
                                @endphp
                                <div id="tabContentTv_{{ $tabKey }}" class="tab-product-content-tv space-y-3 {{ $isTabActive ? '' : 'hidden' }}">
                                    @if($tabKey === 'PORTAL_WEB')
                                        <div class="flex items-center justify-end pb-1">
                                            <div class="flex items-center gap-1.5">
                                                <button type="button" onclick="pilihSemuaTvPortal(true)"
                                                        class="px-2.5 py-1 text-xs font-semibold text-kp-blue-700 bg-white hover:bg-kp-blue-50 border border-slate-200 rounded-lg transition shadow-2xs cursor-pointer">
                                                    Pilih Semua (10 Portal)
                                                </button>
                                                <button type="button" onclick="pilihSemuaTvPortal(false)"
                                                        class="px-2.5 py-1 text-xs font-semibold text-slate-600 hover:text-rose-600 bg-white hover:bg-rose-50 border border-slate-200 rounded-lg transition shadow-2xs cursor-pointer">
                                                    Reset
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Grid 10 Portal Web Priangan TV: Ceklist Lancip & Nama Produk + Harga Saja -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                            @foreach($portalList as $pIdx => $portal)
                                                @php
                                                    $isPortChecked = in_array($portal['id'], $oldTvPortals) || in_array($portal['nama'], $oldTvPortals);
                                                @endphp
                                                <label id="cardTvPortal_{{ $pIdx }}"
                                                       class="tv-portal-checkbox-card relative overflow-hidden flex flex-col justify-center px-3 py-2.5 rounded-xl border transition cursor-pointer select-none min-h-[68px] {{ $isPortChecked ? 'border-2 border-kp-blue-600 bg-sky-50 text-kp-blue-900 font-bold ring-1 ring-kp-blue-500/20 shadow-2xs' : 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 font-medium' }}">
                                                    <input type="checkbox" name="portal_iklanpriangan[]" value="{{ $portal['id'] }}"
                                                           class="tv-portal-checkbox sr-only"
                                                           {{ $isPortChecked ? 'checked' : '' }}
                                                           onchange="handleTvPortalCheckboxChange()">

                                                    <!-- Badge Ceklist Lancip Segitiga Sudut Kiri Atas (Foto 2 Style) -->
                                                    <span class="badge-corner-check absolute top-0 left-0 w-6 h-6 pointer-events-none z-10 {{ $isPortChecked ? '' : 'hidden' }}">
                                                        <svg class="w-6 h-6" viewBox="0 0 24 24">
                                                            <path d="M0 0 L24 0 L0 24 Z" fill="#0A72AC" />
                                                            <path d="M3.5 7.5 L7 11 L13 5" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>

                                                    <div class="pl-1.5">
                                                        <div class="text-slate-900 text-[11px] sm:text-xs tracking-tight whitespace-nowrap leading-snug">
                                                            {{ $portal['nama'] }}
                                                        </div>
                                                        <div class="font-mono text-xs text-kp-blue-700 mt-1">Rp {{ number_format($configs['tarif_portal'] ?? 750000, 0, ',', '.') }}</div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                            @foreach($tabItems as $prod)
                                                <button type="button" 
                                                        onclick="toggleProdukTv({{ $prod->id }}, '{{ $prod->kode_jenis }}', '{{ addslashes($prod->nama_jenis) }}', {{ $prod->tarif_dasar }}, '{{ $tabKey }}')"
                                                        id="cardTvProduk_{{ $tabKey }}_{{ $prod->id }}"
                                                        data-id="{{ $prod->id }}"
                                                        data-kode="{{ $prod->kode_jenis }}"
                                                        data-tarif="{{ $prod->tarif_dasar }}"
                                                        data-nama="{{ $prod->nama_jenis }}"
                                                        data-tab="{{ $tabKey }}"
                                                        class="tv-produk-card text-left p-3.5 rounded-xl border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50 font-medium transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2.5 min-h-[92px]">
                                                    
                                                    <!-- Badge Ceklist Lancip Segitiga Sudut Kiri Atas (Foto 2 Style) -->
                                                    <span class="badge-corner-check absolute top-0 left-0 w-7 h-7 pointer-events-none z-10 hidden">
                                                        <svg class="w-7 h-7" viewBox="0 0 28 28">
                                                            <path d="M0 0 L28 0 L0 28 Z" fill="#0A72AC" />
                                                            <path d="M4 9 L8 13 L14 6" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>

                                                    <div class="text-sm text-slate-900 leading-snug">
                                                        {{ $prod->nama_jenis }}
                                                    </div>
                                                    <div class="text-xs text-kp-blue-700 font-mono">
                                                        Rp {{ number_format($prod->tarif_dasar, 0, ',', '.') }}
                                                    </div>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <!-- Detail Siar & Materi TV -->
                            <div class="pt-2 border-t border-slate-100 space-y-3">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Judul / Materi Iklan TV</label>
                                    <input type="text" name="ptv_materi_iklan" id="ptv_materi_iklan" value="{{ old('ptv_materi_iklan') }}" placeholder="Contoh: Spot Peluncuran Produk / Talkshow Bisnis..." class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Durasi Siar (Detik)</label>
                                        <input type="number" name="ptv_durasi_detik" id="ptv_durasi_detik" value="{{ old('ptv_durasi_detik', 30) }}" min="5" max="7200" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Tayang / Slot Siar</label>
                                        <input type="text" name="ptv_jam_tayang" id="ptv_jam_tayang" value="{{ old('ptv_jam_tayang', '19:00 WIB (Prime Time)') }}" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Upload File Siar</label>
                                        <input type="file" name="ptv_file_materi" accept="video/*,audio/*,image/*,.pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-kp-blue-50 file:text-kp-blue-700 hover:file:bg-kp-blue-100 cursor-pointer border border-slate-300 rounded-xl p-1">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Link Materi (Google Drive / YouTube)</label>
                                        <input type="url" name="ptv_url_materi" value="{{ old('ptv_url_materi') }}" placeholder="https://drive.google.com/..." class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Naskah Siaran / Catatan Teknis</label>
                                    <textarea name="ptv_catatan" rows="2" placeholder="Catatan bumper, opening/closing, atau naskah siar..." class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs resize-none">{{ old('ptv_catatan') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- JADWAL TAYANG TV (INTERACTIVE MULTI-DATE CALENDAR) -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">C2</span>
                                    <span>Jadwal Tayang / Siaran Priangan TV</span>
                                </span>
                                <div id="statusParitasTanggalTv" class="text-xs font-semibold px-2.5 py-1 rounded-lg border bg-emerald-50 text-emerald-800 border-emerald-200 inline-flex items-center gap-1.5 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span id="badgeParitasTv">1 Tanggal Dipilih</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-stretch">
                                <!-- Kolom Kiri: Frekuensi & Daftar Tanggal Terpilih -->
                                <div class="md:col-span-5 flex flex-col justify-between space-y-3.5">
                                    <!-- Stepper Frekuensi Siar -->
                                    <div class="p-3 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-2">
                                        <div class="flex items-center justify-between whitespace-nowrap">
                                            <label class="text-xs font-medium text-slate-700 flex items-center gap-1.5 whitespace-nowrap">
                                                <svg class="w-3.5 h-3.5 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span>Frekuensi Spot / Siar TV</span>
                                                <span class="text-rose-500">*</span>
                                            </label>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center bg-white border border-slate-300 rounded-xl p-1 shadow-2xs">
                                                <button type="button" onclick="stepJumlahTayangTv(-1)"
                                                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition cursor-pointer select-none">
                                                    −
                                                </button>
                                                <input type="text" inputmode="numeric" name="ptv_total_muat" id="in_qty_ptv"
                                                       value="{{ old('ptv_total_muat', 1) }}"
                                                       oninput="handleJumlahTayangTvInput(this.value)"
                                                       onblur="handleJumlahTayangTvBlur(this)"
                                                       autocomplete="off"
                                                       class="w-14 h-8 px-1 text-center font-bold text-sm text-slate-900 border-0 focus:ring-0 focus:outline-none">
                                                <button type="button" onclick="stepJumlahTayangTv(1)"
                                                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition cursor-pointer select-none">
                                                    +
                                                </button>
                                            </div>
                                            <span class="text-xs font-medium text-slate-700">Spot</span>
                                        </div>
                                    </div>

                                    <!-- Box Daftar Tanggal Terpilih -->
                                    <div class="flex-1 flex flex-col p-3 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-2">
                                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/60 whitespace-nowrap gap-1.5">
                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <span class="text-sm font-semibold text-slate-700 whitespace-nowrap">Tanggal Siar:</span>
                                                <span id="labelTotalDatesCountPtv" class="px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-100 text-kp-blue-800 whitespace-nowrap">1 Tanggal</span>
                                            </div>
                                            <button type="button" onclick="resetSemuaTanggalTv()"
                                                    class="p-1 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer flex items-center justify-center shrink-0"
                                                    title="Reset semua tanggal">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                        <div id="daftarTanggalChipsPtv" class="space-y-1.5 overflow-y-auto max-h-[195px] pr-0.5">
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
                                    <div id="containerKalenderTv" class="p-3 bg-white border border-slate-200/90 rounded-xl space-y-2 shadow-2xs transition-all">
                                        <!-- Header Kalender: Bulan Tahun & Navigasi Panah Kompak -->
                                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                                            <div class="flex items-center gap-2">
                                                <span id="calendarMonthTitleTv" class="text-sm font-bold text-slate-900"></span>
                                                <button type="button" onclick="goToTodayCalendarTv()"
                                                        class="text-xs font-semibold text-kp-blue-600 hover:text-kp-blue-800 cursor-pointer underline">
                                                    Hari Ini
                                                </button>
                                            </div>
                                            <!-- Navigasi Panah < > -->
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
                                        <div id="calendarDaysGridTv" class="grid grid-cols-7 gap-1 text-center text-sm font-medium">
                                            <!-- Rendered via JS -->
                                        </div>

                                        <!-- Legenda Simpel Ringkas -->
                                        <div class="flex items-center justify-between text-xs text-slate-500 border-t border-slate-100 pt-2 px-0.5">
                                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-600 inline-block shadow-2xs"></span> Tanggal Tayang</span>
                                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-rose-50 border border-rose-300 inline-block"></span> Libur / Minggu</span>
                                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-amber-50 border border-amber-300 inline-block"></span> Hari Peringatan</span>
                                        </div>
                                    </div>

                                    <!-- Panel Keterangan Hari -->
                                    <div id="panelInfoTanggalTv" class="p-2.5 rounded-xl border transition-all text-xs bg-slate-50 border-slate-200 space-y-1.5">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span id="infoTanggalLabelTv" class="font-bold text-xs text-slate-900 whitespace-nowrap">
                                                Klik tanggal di kalender
                                            </span>
                                        </div>
                                        <div id="infoTanggalBadgeContainerTv" class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-200 text-slate-700 whitespace-nowrap">
                                                Status Hari
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- SEKSI SALES & KOMISIONER (PALING BAWAH FORM TRANSAKSI) -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Sales &amp; Komisioner</span>
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            <!-- Card 1: Komisioner -->
                            <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/90 space-y-2.5 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-sm font-semibold text-slate-700 whitespace-nowrap">
                                        Komisioner
                                    </label>
                                    <span class="text-xs text-slate-400 font-normal shrink-0">Opsional</span>
                                </div>
                                <div>
                                    <input type="text" name="komisioner" id="komisioner" 
                                           value="{{ old('komisioner') }}"
                                           placeholder="Nama komisioner (jika ada)"
                                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Komisi Komisioner</label>
                                    <div class="grid grid-cols-5 gap-2">
                                        <div class="col-span-2 relative">
                                            <input type="number" name="komisi_persen" id="komisi_persen" 
                                                   value="{{ old('komisi_persen', 0) }}" step="any" min="0" max="100" placeholder="0"
                                                   oninput="handleKomisiPersenChange(this.value)"
                                                   class="w-full h-10 px-2.5 pr-6 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                            <span class="absolute right-2 top-2.5 text-xs text-slate-400 font-medium pointer-events-none">%</span>
                                        </div>
                                        <div class="col-span-3 relative">
                                            <span class="absolute left-2.5 top-2.5 text-xs text-slate-400 font-medium pointer-events-none">Rp</span>
                                            <input type="text" name="komisi_nominal" id="komisi_nominal" 
                                                   value="{{ old('komisi_nominal', '0') }}" placeholder="0"
                                                   onkeyup="handleKomisiNominalInput(this)"
                                                   class="w-full h-10 pl-8 pr-2.5 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: Sales / AE -->
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
                                    $defaultAeId = old('sales_ae_id', $salesAes->firstWhere('jenis_kode', 'FO')->id ?? $salesAes->first()?->id ?? '');
                                @endphp
                                <select name="sales_ae_id" id="sales_ae_id" required
                                        class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                    @foreach($salesAes as $ae)
                                        <option value="{{ $ae->id }}" {{ $defaultAeId == $ae->id ? 'selected' : '' }}>
                                            {{ $ae->kode_ae === 'FO' ? 'Front Office (Loket Langsung)' : ($ae->kode_ae . ' - ' . $ae->nama_ae) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Insentif Sales</label>
                                    <div class="grid grid-cols-5 gap-2">
                                        <div class="col-span-2 relative">
                                            <input type="number" name="insentif_persen" id="insentif_persen" 
                                                   value="{{ old('insentif_persen', 0) }}" step="any" min="0" max="100" placeholder="0"
                                                   oninput="handleInsentifPersenChange(this.value)"
                                                   class="w-full h-10 px-2.5 pr-6 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                            <span class="absolute right-2 top-2.5 text-xs text-slate-400 font-medium pointer-events-none">%</span>
                                        </div>
                                        <div class="col-span-3 relative">
                                            <span class="absolute left-2.5 top-2.5 text-xs text-slate-400 font-medium pointer-events-none">Rp</span>
                                            <input type="text" name="insentif_nominal" id="insentif_nominal" 
                                                   value="{{ old('insentif_nominal', '0') }}" placeholder="0"
                                                   onkeyup="handleInsentifNominalInput(this)"
                                                   class="w-full h-10 pl-8 pr-2.5 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ============================================================== -->
                <!-- PANEL KANAN: STRUK KALKULASI FINANSIAL GABUNGAN                -->
                <!-- (COPY PERSIS DARI STRUK KALKULASI FINANSIAL TRANSAKSI KORAN)   -->
                <!-- ============================================================== -->
                <div class="lg:col-span-5 sticky top-6 space-y-4">
                    
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                                <span>Rincian Biaya Transaksi Paket</span>
                            </span>
                            <span id="badgeStatusBayar" class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $isTunai ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                {{ $currentJenisBayar }}
                            </span>
                        </div>

                        <!-- Baris Rincian Biaya Transparan Standar Akuntansi Pers -->
                        <div class="text-xs sm:text-sm">
                            <table class="w-full border-collapse" style="table-layout: fixed;">
                                <tbody>
                                    <!-- Breakdown Cetak -->
                                    <tr id="rowStrukCtk" class="{{ $hasCtk ? '' : 'hidden' }}">
                                        <td class="py-1 text-slate-600 font-medium whitespace-nowrap" style="width: 140px;">Subtotal Cetak</td>
                                        <td class="py-1 text-center font-bold text-slate-400" style="width: 16px;">:</td>
                                        <td class="py-1 text-right font-mono text-slate-800" id="strukSubtotalCtk">Rp 0</td>
                                    </tr>

                                    <!-- Breakdown Online -->
                                    <tr id="rowStrukOnl" class="{{ $hasOnl ? '' : 'hidden' }}">
                                        <td class="py-1 text-slate-600 font-medium whitespace-nowrap">Subtotal Online</td>
                                        <td class="py-1 text-center font-bold text-slate-400">:</td>
                                        <td class="py-1 text-right font-mono text-slate-800" id="strukSubtotalOnl">Rp 0</td>
                                    </tr>

                                    <!-- Breakdown TV -->
                                    <tr id="rowStrukPtv" class="{{ $hasPtv ? '' : 'hidden' }}">
                                        <td class="py-1 text-slate-600 font-medium whitespace-nowrap">Subtotal Priangan TV</td>
                                        <td class="py-1 text-center font-bold text-slate-400">:</td>
                                        <td class="py-1 text-right font-mono text-slate-800" id="strukSubtotalPtv">Rp 0</td>
                                    </tr>

                                    <!-- Total Brutto -->
                                    <tr class="border-t border-slate-200">
                                        <td class="pt-2 pb-1 text-slate-800 font-bold whitespace-nowrap">Total Brutto</td>
                                        <td class="pt-2 pb-1 text-center font-bold text-slate-400">:</td>
                                        <td class="pt-2 pb-1 text-right font-mono font-bold text-slate-900" id="strukBrutto">Rp 0</td>
                                    </tr>

                                    <!-- Diskon Transaksi Paket -->
                                    <tr class="border-t border-slate-200">
                                        <td colspan="3" class="pt-2.5 pb-2">
                                            <div class="space-y-1.5">
                                                <div class="flex items-center justify-between">
                                                    <label class="text-xs font-semibold text-slate-700">Diskon Paket</label>
                                                    <span class="text-[11px] text-slate-400 font-normal">Isi % atau nominal Rp</span>
                                                </div>
                                                <div class="grid grid-cols-5 gap-2">
                                                    <div class="col-span-2 relative">
                                                        <input type="number" name="diskon_persen" id="diskon_persen" 
                                                               value="{{ old('diskon_persen', 0) }}" 
                                                               step="0.5" min="0" max="100" placeholder="0"
                                                               oninput="handleDiskonPersenChange(this.value)"
                                                               class="w-full h-10 px-2.5 pr-6 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                                        <span class="absolute right-2 top-2.5 text-xs text-slate-400 font-bold pointer-events-none">%</span>
                                                    </div>
                                                    <div class="col-span-3 relative">
                                                        <span class="absolute left-2.5 top-2.5 text-xs text-slate-400 font-bold pointer-events-none">Rp</span>
                                                        <input type="text" name="diskon_nominal_display" id="diskon_nominal_display" 
                                                               value="{{ old('nilai_diskon', '0') }}" placeholder="0"
                                                               onkeyup="handleDiskonNominalInput(this)"
                                                               style="padding-left: 2.25rem !important;"
                                                               class="w-full h-10 pr-2.5 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Netto & PPN -->
                                    <tr class="border-t border-slate-200">
                                        <td class="pt-2 pb-1 text-slate-800 font-semibold whitespace-nowrap">Netto (DPP)</td>
                                        <td class="pt-2 pb-1 text-center font-bold text-slate-400">:</td>
                                        <td class="pt-2 pb-1 text-right font-mono font-semibold text-slate-800" id="strukNetto">Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1 text-slate-600 font-medium whitespace-nowrap">PPN ({{ $configs['ppn_persen'] }}%)</td>
                                        <td class="py-1 text-center font-bold text-slate-400">:</td>
                                        <td class="py-1 text-right font-mono text-slate-700" id="strukPpn">Rp 0</td>
                                    </tr>

                                    <!-- TOTAL TAGIHAN -->
                                    <tr class="border-t-2 border-slate-800">
                                        <td class="py-2.5 text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide whitespace-nowrap">Total Tagihan</td>
                                        <td class="py-2.5 text-center font-bold text-slate-400">:</td>
                                        <td class="py-2.5 text-right font-mono text-base sm:text-lg font-extrabold text-kp-blue-700" id="strukTotalTagihan">Rp 0</td>
                                    </tr>

                                    <!-- Input Pembayaran -->
                                    <tr class="border-t border-slate-100">
                                        <td colspan="3" class="pt-3 pb-2">
                                            <div class="space-y-1.5">
                                                <div class="flex items-center justify-between">
                                                    <label class="text-xs font-semibold text-slate-700">Nominal Bayar / Uang Muka</label>
                                                    <button type="button" onclick="setBayarLunasPaket()" class="text-xs font-semibold text-kp-blue-600 hover:underline cursor-pointer">
                                                        Bayar Lunas
                                                    </button>
                                                </div>
                                                <input type="text" inputmode="numeric" name="jumlah_bayar" id="jumlah_bayar" 
                                                       value="{{ old('jumlah_bayar', '0') }}"
                                                       oninput="handleBayarInput()"
                                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Sisa Piutang -->
                                    <tr id="rowStrukPiutangPaket" class="border-t border-slate-100">
                                        <td class="pt-2 text-xs font-semibold text-slate-500 whitespace-nowrap">Sisa Piutang</td>
                                        <td class="pt-2 text-center font-bold text-slate-400">:</td>
                                        <td class="pt-2 text-right font-mono font-bold text-rose-600" id="strukPiutangPaket">Rp 0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Tombol Aksi Submit Transaksi -->
                        <div class="pt-4 border-t border-slate-100 space-y-2">
                            <button type="button" onclick="konfirmasiSimpanPaket()" id="btnSimpanPaket"
                                    class="w-full h-12 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Simpan &amp; Terbitkan Faktur Paket</span>
                            </button>
                            <p class="text-[11px] text-slate-400 text-center">
                                Menghasilkan 1 Faktur Paket untuk Pemasang &amp; sinkron ke 3 transaksi anak.
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </form>

    </div>



    <!-- MODAL 1: TAMBAH PELANGGAN BARU (COPY PERSIS DARI ONLINE/KORAN) -->
    <div id="modalPelangganBaru" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Tambah Pelanggan Baru</h3>
                <button type="button" onclick="closeModalPelangganBaru()" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Instansi / Pemasang *</label>
                    <input type="text" id="modal_nama_instansi" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Klien</label>
                        <select id="modal_jenis_klien" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900">
                            <option value="Perorangan">Perorangan</option>
                            <option value="Perusahaan">Perusahaan</option>
                            <option value="Pemerintah">Pemerintah</option>
                            <option value="Agensi">Agensi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No. Telepon / WA</label>
                        <input type="text" id="modal_no_telp" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                    <textarea id="modal_alamat" rows="2" class="w-full p-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 resize-none"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModalPelangganBaru()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">Batal</button>
                <button type="button" onclick="simpanPelangganBaruOnTheFly()" id="btnSimpanPelangganModal" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-xl">Simpan &amp; Pilih</button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: TAMBAH SALES / AE BARU (COPY PERSIS DARI ONLINE/PRIANGAN) -->
    <div id="modalSalesAeBaru" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Tambah Sales / AE Baru</h3>
                <button type="button" onclick="closeModalSalesAeBaru()" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kelompok Sales</label>
                    <select id="modal_sales_jenis" onchange="autoGenerateKodeSalesModal()" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900">
                        <option value="AE">Account Executive (AE)</option>
                        <option value="AGEN">Agen Eksternal</option>
                        <option value="KHUSUS">Khusus</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode AE *</label>
                    <input type="text" id="modal_sales_kode" placeholder="Misal: AE09" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Sales / AE *</label>
                    <input type="text" id="modal_sales_nama" placeholder="Nama sales..." class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModalSalesAeBaru()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">Batal</button>
                <button type="button" onclick="simpanSalesAeOnTheFly()" id="btnSimpanSalesAeModal" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-xl">Simpan &amp; Pilih</button>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL KONFIRMASI SIMPAN TRANSAKSI (STANDAR PERSIS 3 TRANSAKSI) -->
    <!-- ============================================================== -->
    <div id="modalKonfirmasiSimpan" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto hidden">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all animate-in fade-in zoom-in-95 duration-150 my-auto">
            <div class="p-6 text-center space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center mx-auto shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-bold text-slate-900">
                        Konfirmasi Simpan Transaksi Paket
                    </h3>
                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">
                        Pastikan data pemesan, spesifikasi paket lintas media, dan skema pembayaran telah sesuai sebelum menyimpan transaksi ini.
                    </p>
                </div>

                <!-- Ringkasan Singkat Selaras 3 Transaksi -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/80 text-left text-sm space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Nama Pemesan:</span>
                        <span id="konfirmasiNamaPemesan" class="font-bold text-slate-900 truncate max-w-[200px]">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Skema Pembayaran:</span>
                        <span id="konfirmasiSkemaBayar" class="font-semibold text-slate-800">-</span>
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
                        <span>Ya, Simpan Paket</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT ENGINE GABUNGAN 3 TRANSAKSI -->
    <script>
        // Data Master dari Server
        window.currentPelangganList = @json($pelanggans);
        const configs = @json($configs);
        const portalList = @json($portalList);
        const ppnRate = {{ (float) $configs['ppn_persen'] }};

        // Standar Format Hari & Bulan Kalender Indonesia (Selaras Koran/Online/TV)
        const monthNamesIndo = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const dayNamesIndo = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        const dayShortIndo = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];
        const monthShortIndo = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];

        const configJamCutoff = '{{ $configs["jam_cutoff"] ?? "14:00" }}';
        const todayDateStr = '{{ $todayDateStr }}';
        const defaultFirstDateStr = '{{ $defaultFirstDate }}';
        let unlockedDatesSet = new Set(@json($configs['unlocked_dates'] ?? []));
        let closedDatesSet = new Set(@json($configs['closed_dates'] ?? []));

        // Cache Kalender Nasional Resmi
        let kalenderDataCache = {
            [`${new Date().getFullYear()}-${new Date().getMonth() + 1}`]: @json($kalenderAwal ?? ['libur' => [], 'peringatan' => []])
        };
        let isKalenderLoading = false;

        function fetchKalenderBulan(year, month, callback) {
            const cacheKey = `${year}-${month}`;
            if (kalenderDataCache[cacheKey]) {
                if (callback) callback();
                return;
            }
            isKalenderLoading = true;
            fetch(`/api/kalender-nasional?year=${year}&month=${month}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => {
                if (!res.ok) throw new Error('Gagal mengambil data kalender nasional');
                return res.json();
            })
            .then(data => {
                kalenderDataCache[cacheKey] = {
                    libur: data.libur || {},
                    peringatan: data.peringatan || {}
                };
            })
            .catch(err => {
                console.warn('API Kalender Nasional offline atau gagal diakses:', err);
                kalenderDataCache[cacheKey] = { libur: {}, peringatan: {} };
            })
            .finally(() => {
                isKalenderLoading = false;
                if (callback) callback();
            });
        }

        function formatIndoDate(dateStr, withDayName = true) {
            if (!dateStr) return '-';
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            const y = parseInt(parts[0]);
            const m = parseInt(parts[1]) - 1;
            const d = parseInt(parts[2]);
            const dateObj = new Date(y, m, d);
            const dayName = dayNamesIndo[dateObj.getDay()] || '';
            const monthName = monthNamesIndo[m] || '';
            return withDayName ? `${dayName}, ${d} ${monthName} ${y}` : `${d} ${monthName} ${y}`;
        }

        function formatIndoChip(dateStr) {
            if (!dateStr) return '-';
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            const y = parseInt(parts[0]);
            const m = parseInt(parts[1]) - 1;
            const d = parseInt(parts[2]);
            const dateObj = new Date(y, m, d);
            const dayName = dayShortIndo[dateObj.getDay()] || '';
            const monthName = monthShortIndo[m] || '';
            return `${dayName}, ${d} ${monthName} ${y}`;
        }

        function extractEventText(item) {
            if (!item) return null;
            if (typeof item === 'string') return item;
            if (Array.isArray(item) && item.length > 0) {
                const first = item[0];
                return (typeof first === 'object') ? (first.nama || first.keterangan || first.summary || String(first)) : String(first);
            }
            if (typeof item === 'object') return item.nama || item.keterangan || item.summary || null;
            return null;
        }

        function isDateCutoffLocked(dateStr) {
            if (unlockedDatesSet.has(dateStr)) return false;
            if (closedDatesSet.has(dateStr)) return true;
            if (dateStr <= todayDateStr) {
                return true;
            }
            return false;
        }

        function showCustomToast(message, type = 'info', title = null) {
            let container = document.getElementById('toastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'fixed bottom-6 right-6 z-50 flex flex-col gap-3 pointer-events-none max-w-sm w-full';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = "pointer-events-auto bg-white/95 backdrop-blur-md border border-slate-200/90 shadow-[0_12px_36px_rgba(15,23,42,0.12)] rounded-2xl p-4 flex items-start gap-3.5 transition-all transform duration-300 translate-y-3 opacity-0";

            let iconHtml = '';
            let defaultTitle = '';
            if (type === 'warning') {
                defaultTitle = title || 'Perhatian';
                iconHtml = `<div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200/60"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>`;
            } else if (type === 'error') {
                defaultTitle = title || 'Kesalahan';
                iconHtml = `<div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-200/60"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></div>`;
            } else {
                defaultTitle = title || 'Informasi';
                iconHtml = `<div class="w-8 h-8 rounded-xl bg-kp-blue-50 text-kp-blue-600 flex items-center justify-center shrink-0 border border-kp-blue-200/60"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>`;
            }

            toast.innerHTML = `
                ${iconHtml}
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-800">${defaultTitle}</p>
                    <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">${message}</p>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600 transition p-1" onclick="this.parentElement.remove()">&times;</button>
            `;

            container.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-3', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        function updateSharedPanelInfoTanggal(panelId, labelId, badgeContainerId, dateStr, isSelected, mediaLabel) {
            if (!dateStr) return;
            const panel = document.getElementById(panelId);
            const label = document.getElementById(labelId);
            const badgeContainer = document.getElementById(badgeContainerId);
            if (!panel || !label || !badgeContainer) return;

            label.textContent = formatIndoDate(dateStr, true);

            const parts = dateStr.split('-');
            const year = parseInt(parts[0]);
            const month = parseInt(parts[1]);
            const cacheKey = `${year}-${month}`;
            const monthData = kalenderDataCache[cacheKey] || { libur: {}, peringatan: {} };

            const liburDesc = extractEventText(monthData.libur[dateStr]);
            const peringatanDesc = extractEventText(monthData.peringatan[dateStr]);

            const dateObj = new Date(year, month - 1, parseInt(parts[2]));
            const isSunday = (dateObj.getDay() === 0);
            const isSaturday = (dateObj.getDay() === 6);

            let selectedTagHtml = '';
            if (isSelected) {
                selectedTagHtml = `
                    <span class="inline-flex items-center text-xs font-bold px-2 py-0.5 rounded-md bg-kp-blue-600 text-white shadow-2xs">
                        Terjadwal ${mediaLabel}
                    </span>
                `;
            }

            let badgesHtml = '';
            if (liburDesc && peringatanDesc) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-rose-50/80 border-rose-200 text-rose-900 space-y-1.5";
                badgesHtml = `
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-rose-200 text-rose-700 shadow-2xs">Libur: ${liburDesc}</span>
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-amber-200 text-amber-800 shadow-2xs">Peringatan: ${peringatanDesc}</span>
                        ${selectedTagHtml}
                    </div>
                `;
            } else if (liburDesc) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-rose-50/80 border-rose-200 text-rose-900 space-y-1.5";
                badgesHtml = `
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-rose-200 text-rose-700 shadow-2xs">Libur: ${liburDesc}</span>
                        ${selectedTagHtml}
                    </div>
                `;
            } else if (peringatanDesc) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-amber-50/80 border-amber-200 text-amber-900 space-y-1.5";
                badgesHtml = `
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-amber-200 text-amber-800 shadow-2xs">Peringatan: ${peringatanDesc}</span>
                        ${selectedTagHtml}
                    </div>
                `;
            } else if (isSunday) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-rose-50/70 border-rose-200 text-rose-900 space-y-1.5";
                badgesHtml = `
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-rose-200 text-rose-700 shadow-2xs">Akhir Pekan (Minggu)</span>
                        ${selectedTagHtml}
                    </div>
                `;
            } else if (isSaturday) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-slate-50 border-slate-200 text-slate-800 space-y-1.5";
                badgesHtml = `
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-slate-200 text-slate-700 shadow-2xs">Akhir Pekan (Sabtu)</span>
                        ${selectedTagHtml}
                    </div>
                `;
            } else {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-sky-50/60 border-sky-200 text-sky-900 space-y-1.5";
                badgesHtml = `
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-sky-200 text-sky-800 shadow-2xs">Hari Kerja (Reguler)</span>
                        ${selectedTagHtml}
                    </div>
                `;
            }

            badgeContainer.innerHTML = badgesHtml;
        }

        // State Global Paket
        let activeMedia = {
            CTK: {{ $hasCtk ? 'true' : 'false' }},
            ONL: {{ $hasOnl ? 'true' : 'false' }},
            PTV: {{ $hasPtv ? 'true' : 'false' }}
        };

        // State Cetak
        let ctkSelectedDates = ['{{ $defaultFirstDate }}'];
        let ctkCalMonth = new Date('{{ $defaultFirstDate }}').getMonth();
        let ctkCalYear = new Date('{{ $defaultFirstDate }}').getFullYear();
        let ctkActiveDetailDate = '{{ $defaultFirstDate }}';
        let ctkTarifDasar = {{ $initProdCtk ? (int) $initProdCtk->tarif_dasar : 0 }};
        let ctkCaraHitung = '{{ $initProdCtk ? $initProdCtk->cara_hitung : "PER_MMK" }}';
        let ctkSelectedId = {{ $initProdCtk ? $initProdCtk->id : "null" }};

        // State Online
        let onlSelectedDates = ['{{ $defaultFirstDate }}'];
        let onlCalMonth = new Date('{{ $defaultFirstDate }}').getMonth();
        let onlCalYear = new Date('{{ $defaultFirstDate }}').getFullYear();
        let onlActiveDetailDate = '{{ $defaultFirstDate }}';
        let onlSelectedPortals = {!! json_encode(array_values((array) old('onl_portals', []))) !!};
        let onlTarifPortal = configs.tarif_portal || 750000;
        let onlSelectedMedsosTarif = 0;
        let onlActiveTab = 'PORTAL_BERITA';

        // State TV
        let ptvSelectedDates = ['{{ $defaultFirstDate }}'];
        let ptvCalMonth = new Date('{{ $defaultFirstDate }}').getMonth();
        let ptvCalYear = new Date('{{ $defaultFirstDate }}').getFullYear();
        let ptvActiveDetailDate = '{{ $defaultFirstDate }}';
        let ptvTarifDasar = {{ $initProdTv ? (int) $initProdTv->tarif_dasar : 0 }};
        let ptvSelectedId = {{ $initProdTv ? $initProdTv->id : "null" }};

        // State Keuangan
        let diskonPersenVal = {{ (float) old('diskon_persen', 0) }};
        let diskonNominalVal = {{ (int) old('nilai_diskon', 0) }};

        function formatRupiah(num) {
            num = Math.round(Number(num) || 0);
            return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function cleanNum(str) {
            if (typeof str === 'number') return Math.round(str);
            return parseInt(String(str || '0').replace(/[^0-9]/g, ''), 10) || 0;
        }

        // ==========================================
        // 1. CARA BAYAR & PELANGGAN LOGIC
        // ==========================================
        function setJenisPembayaran(jenis) {
            document.getElementById('jenis_pembayaran').value = jenis;
            const isTunai = (jenis === 'Tunai');
            const cardTunai = document.getElementById('cardBayarTunai');
            const cardKredit = document.getElementById('cardBayarKredit');
            const boxJatuhTempo = document.getElementById('boxJatuhTempo');
            const badge = document.getElementById('badgeStatusBayar');

            if (isTunai) {
                cardTunai.className = 'px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-emerald-50/70 border-emerald-500 text-emerald-950 font-semibold shadow-2xs';
                cardKredit.className = 'px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-white border-slate-200 text-slate-600 hover:border-slate-300 font-medium';
                document.getElementById('dotBayarTunai').className = 'text-emerald-600 flex items-center';
                document.getElementById('dotBayarKredit').className = 'text-transparent flex items-center';
                boxJatuhTempo.classList.add('hidden');
                badge.className = 'text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200';
                badge.innerText = 'Tunai';

                setBayarLunasPaket();
            } else {
                cardKredit.className = 'px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-amber-50/70 border-amber-500 text-amber-950 font-semibold shadow-2xs';
                cardTunai.className = 'px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-white border-slate-200 text-slate-600 hover:border-slate-300 font-medium';
                document.getElementById('dotBayarKredit').className = 'text-amber-600 flex items-center';
                document.getElementById('dotBayarTunai').className = 'text-transparent flex items-center';
                boxJatuhTempo.classList.remove('hidden');
                badge.className = 'text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200';
                badge.innerText = 'Kredit';

                const el = document.getElementById('jumlah_bayar');
                if (el) el.value = '0';
                handleBayarInput();
            }
            hitungKalkulasiPaket();
        }

        function handleSearchPelangganFocus(openAll = false) {
            const dropdown = document.getElementById('dropdownHasilPelanggan');
            const query = openAll ? '' : (document.getElementById('inputSearchPelanggan').value || '');
            renderPelangganDropdown(query);
            dropdown.classList.remove('hidden');
        }

        function handleSearchPelangganInput(val) {
            renderPelangganDropdown(val);
            document.getElementById('dropdownHasilPelanggan').classList.remove('hidden');
        }

        function renderPelangganDropdown(query = '') {
            const listEl = document.getElementById('listPelangganItem');
            const q = query.trim().toLowerCase();
            const filtered = window.currentPelangganList.filter(p => {
                if (!q) return true;
                return (p.nama_instansi && p.nama_instansi.toLowerCase().includes(q)) ||
                       (p.kode_pelanggan && p.kode_pelanggan.toLowerCase().includes(q)) ||
                       (p.no_telp && p.no_telp.includes(q));
            }).slice(0, 10);

            if (filtered.length === 0) {
                listEl.innerHTML = `<div class="p-3 text-xs text-slate-400 text-center italic">Tidak ditemukan pelanggan "${query}"</div>`;
                return;
            }

            listEl.innerHTML = filtered.map(p => `
                <div onclick="selectPelangganItem(${p.id})" class="p-2.5 hover:bg-kp-blue-50/60 rounded-xl cursor-pointer transition flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-900 truncate">${p.nama_instansi}</div>
                        <div class="text-[11px] text-slate-500 font-mono">[${p.kode_pelanggan || '-'}] • ${p.no_telp || 'No telp -'}</div>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] font-semibold bg-white border border-slate-200 rounded text-slate-600 uppercase shrink-0">${p.jenis_klien || 'UMUM'}</span>
                </div>
            `).join('');
        }

        function selectPelangganItem(id) {
            const p = window.currentPelangganList.find(x => x.id == id);
            if (!p) return;
            document.getElementById('pelanggan_id').value = p.id;
            document.getElementById('inputManualNamaPemasang').value = p.nama_instansi;
            document.getElementById('telepon_pemasang').value = p.no_telp || '';
            document.getElementById('alamat_pemasang').value = p.alamat || '';
            if (p.kontak_pic) document.getElementById('nama_pic').value = p.kontak_pic;

            document.getElementById('labelNamaPelanggan').innerText = p.nama_instansi;
            document.getElementById('badgeJenisPelanggan').innerText = p.jenis_klien || 'UMUM';
            document.getElementById('labelKodePelanggan').innerText = `[${p.kode_pelanggan || '-'}]`;

            document.getElementById('containerSearchPelanggan').classList.add('hidden');
            document.getElementById('cardPelangganTerpilih').classList.remove('hidden');
            document.getElementById('dropdownHasilPelanggan').classList.add('hidden');
        }

        function resetPilihanPelanggan() {
            document.getElementById('pelanggan_id').value = '';
            document.getElementById('inputManualNamaPemasang').value = '';
            document.getElementById('telepon_pemasang').value = '';
            document.getElementById('alamat_pemasang').value = '';
            document.getElementById('nama_pic').value = '';

            document.getElementById('cardPelangganTerpilih').classList.add('hidden');
            document.getElementById('containerSearchPelanggan').classList.remove('hidden');
            document.getElementById('inputSearchPelanggan').value = '';
        }

        function handleManualNamaInput(val) {
            if (document.getElementById('pelanggan_id').value) {
                document.getElementById('pelanggan_id').value = '';
                document.getElementById('cardPelangganTerpilih').classList.add('hidden');
                document.getElementById('containerSearchPelanggan').classList.remove('hidden');
            }
        }

        // ==========================================
        // 2. TOGGLE MEDIA & TEMPLATES
        // ==========================================
        function toggleMediaSection(mediaKey, isChecked) {
            activeMedia[mediaKey] = isChecked;
            const containerMap = {
                CTK: 'sectionContainerCetak',
                ONL: 'sectionContainerOnline',
                PTV: 'sectionContainerTv'
            };
            const toggleLabelMap = {
                CTK: 'labelToggleCtk',
                ONL: 'labelToggleOnl',
                PTV: 'labelTogglePtv'
            };
            const badgeClassMap = {
                CTK: '.badge-corner-media-ctk',
                ONL: '.badge-corner-media-onl',
                PTV: '.badge-corner-media-ptv'
            };

            const targetBox = document.getElementById(containerMap[mediaKey]);
            const targetLabel = document.getElementById(toggleLabelMap[mediaKey]);
            const badge = targetLabel ? targetLabel.querySelector(badgeClassMap[mediaKey]) : null;

            if (isChecked) {
                targetBox?.classList.remove('hidden');
                if (targetLabel) {
                    targetLabel.className = 'relative overflow-hidden p-3.5 rounded-xl border-2 border-kp-blue-600 bg-sky-50/70 text-slate-900 ring-1 ring-kp-blue-500/20 shadow-2xs font-bold transition cursor-pointer select-none flex flex-col justify-center min-h-[64px]';
                }
                if (badge) {
                    badge.classList.remove('hidden');
                }
            } else {
                targetBox?.classList.add('hidden');
                if (targetLabel) {
                    targetLabel.className = 'relative overflow-hidden p-3.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50/50 font-medium transition cursor-pointer select-none flex flex-col justify-center min-h-[64px]';
                }
                if (badge) {
                    badge.classList.add('hidden');
                }
            }

            hitungKalkulasiPaket();
        }

        function applyTemplatePaket(kode, nama, mediaList, diskon) {
            document.getElementById('kode_template').value = kode;
            document.getElementById('nama_paket').value = nama;

            // Update Media Checklist
            ['CTK', 'ONL', 'PTV'].forEach(m => {
                const wantActive = mediaList.includes(m);
                const chk = document.getElementById(`chkMedia${m.charAt(0) + m.slice(1).toLowerCase()}`);
                if (chk) {
                    chk.checked = wantActive;
                    toggleMediaSection(m, wantActive);
                }
            });

            // Update Diskon
            document.getElementById('diskon_persen').value = diskon;
            handleDiskonPersenChange(diskon);

            // Active button style
            document.querySelectorAll('[id^="btnTpl_"]').forEach(b => {
                b.className = 'px-2.5 py-1.5 rounded-lg text-xs font-medium border transition cursor-pointer flex items-center gap-1 bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100 hover:border-slate-300';
            });
            const activeBtn = document.getElementById(`btnTpl_${kode}`);
            if (activeBtn) {
                activeBtn.className = 'px-2.5 py-1.5 rounded-lg text-xs font-semibold border transition cursor-pointer flex items-center gap-1 bg-kp-blue-600 text-white border-kp-blue-600 shadow-2xs';
            }
        }

        // ==========================================
        // 3. IKLAN CETAK LOGIC
        // ==========================================
        function switchProductTabCtk(tabKey) {
            document.getElementById('ctk_kategori_form').value = tabKey;
            document.querySelectorAll('.tab-product-btn-ctk').forEach(b => {
                b.className = 'tab-product-btn-ctk px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 bg-white text-slate-700 font-medium border border-slate-200 hover:border-slate-300 hover:bg-slate-50';
            });
            const activeBtn = document.getElementById(`tabBtnCtk_${tabKey}`);
            if (activeBtn) {
                activeBtn.className = 'tab-product-btn-ctk px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 bg-kp-blue-600 text-white font-semibold shadow-xs border border-kp-blue-600';
            }

            document.querySelectorAll('.tab-product-content-ctk').forEach(c => c.classList.add('hidden'));
            const activeContent = document.getElementById(`tabContentCtk_${tabKey}`);
            if (activeContent) activeContent.classList.remove('hidden');

            const isBaris = (tabKey === 'BARIS');
            if (ctkSelectedId) {
                document.getElementById('sectionIklanBarisCtk').classList.toggle('hidden', !isBaris);
                document.getElementById('sectionIklanDisplayCtk').classList.toggle('hidden', isBaris);
            }
        }

        function pilihProdukIklanCtk(id, kode, tipe, warna, hitung, tarif, isFixed, defKol, defMm, minOrder) {
            ctkSelectedId = id;
            ctkTarifDasar = tarif;
            ctkCaraHitung = hitung;
            document.getElementById('ctk_id_iklan').value = id;

            const ph = document.getElementById('placeholderPilihProdukCtk');
            if (ph) ph.classList.add('hidden');

            const isBaris = (tipe === 'BARIS');
            document.getElementById('sectionIklanBarisCtk').classList.toggle('hidden', !isBaris);
            document.getElementById('sectionIklanDisplayCtk').classList.toggle('hidden', isBaris);
            const secMateri = document.getElementById('sectionMateriCtk');
            if (secMateri) secMateri.classList.remove('hidden');

            document.querySelectorAll('.produk-card-ctk').forEach(c => {
                c.className = 'produk-card-ctk text-left p-3 rounded-xl border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50 font-medium transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2';
                c.querySelector('.badge-corner-check-ctk')?.classList.add('hidden');
            });
            const activeCard = document.getElementById(`cardProdukCtk_${id}`);
            if (activeCard) {
                activeCard.className = 'produk-card-ctk text-left p-3 rounded-xl border-2 border-kp-blue-600 bg-kp-blue-50/40 ring-1 ring-kp-blue-500/20 shadow-xs font-bold transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2';
                activeCard.querySelector('.badge-corner-check-ctk')?.classList.remove('hidden');
            }

            if (!isBaris) {
                document.getElementById('in_ctk_kolom').value = defKol || 2;
                document.getElementById('in_ctk_tinggi').value = defMm || 100;
                document.getElementById('in_ctk_warna').value = warna || 'BW';
                document.getElementById('ctk_warna').value = warna || 'BW';
            }

            handleDimensiCtkChange();
        }

        function handleDimensiCtkChange() {
            if (!ctkSelectedId) {
                document.getElementById('ctk_harga_satuan').value = 0;
                document.getElementById('labelTarifDasarCtk').innerText = 'Rp 0';
                document.getElementById('labelTotalMmkCtk').innerText = '-';
                hitungKalkulasiPaket();
                return;
            }

            const kol = parseInt(document.getElementById('in_ctk_kolom').value, 10) || 1;
            const mm = parseInt(document.getElementById('in_ctk_tinggi').value, 10) || 20;
            const mmk = kol * mm;
            document.getElementById('ctk_lebar_kolom').value = kol;
            document.getElementById('ctk_tinggi_mm').value = mm;
            document.getElementById('labelTotalMmkCtk').innerText = `${mmk} mmk`;

            let tarif = ctkTarifDasar;
            if (ctkCaraHitung === 'PER_MMK') {
                tarif = ctkTarifDasar * mmk;
            }
            document.getElementById('ctk_harga_satuan').value = tarif;
            document.getElementById('labelTarifDasarCtk').innerText = formatRupiah(tarif);

            hitungKalkulasiPaket();
        }

        function handleWarnaCtkChange(val) {
            document.getElementById('ctk_warna').value = val;
            hitungKalkulasiPaket();
        }

        // ==========================================
        // CETAK CALENDAR LOGIC (1:1 TRANSAKSI KORAN)
        // ==========================================
        function goToTodayCalendarCtk() {
            const today = new Date();
            ctkCalMonth = today.getMonth();
            ctkCalYear = today.getFullYear();
            const todayStr = `${ctkCalYear}-${String(ctkCalMonth + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
            ctkActiveDetailDate = todayStr;

            fetchKalenderBulan(ctkCalYear, ctkCalMonth + 1, () => {
                renderCalendarCtk();
                updatePanelInfoTanggalCtk(todayStr);
            });
        }

        function changeCalendarMonthCtk(delta) {
            ctkCalMonth += delta;
            if (ctkCalMonth > 11) {
                ctkCalMonth = 0;
                ctkCalYear++;
            }
            if (ctkCalMonth < 0) {
                ctkCalMonth = 11;
                ctkCalYear--;
            }

            fetchKalenderBulan(ctkCalYear, ctkCalMonth + 1, () => {
                renderCalendarCtk();
            });
        }

        function renderCalendarCtk() {
            const monthTitleEl = document.getElementById('calendarMonthTitleCtk');
            if (monthTitleEl) {
                monthTitleEl.textContent = `${monthNamesIndo[ctkCalMonth]} ${ctkCalYear}`;
            }

            const grid = document.getElementById('calendarDaysGridCtk');
            if (!grid) return;
            grid.innerHTML = '';

            const cacheKey = `${ctkCalYear}-${ctkCalMonth + 1}`;
            const monthData = kalenderDataCache[cacheKey] || { libur: {}, peringatan: {} };
            const liburMap = monthData.libur || {};
            const peringatanMap = monthData.peringatan || {};

            const firstDayOfWeek = new Date(ctkCalYear, ctkCalMonth, 1).getDay();
            const daysInMonth = new Date(ctkCalYear, ctkCalMonth + 1, 0).getDate();

            for (let i = 0; i < firstDayOfWeek; i++) {
                const empty = document.createElement('div');
                empty.className = 'h-7.5 sm:h-8 pointer-events-none';
                grid.appendChild(empty);
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            for (let day = 1; day <= daysInMonth; day++) {
                const dateObj = new Date(ctkCalYear, ctkCalMonth, day);
                dateObj.setHours(0, 0, 0, 0);
                const dateStr = `${ctkCalYear}-${String(ctkCalMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                const isSelected = ctkSelectedDates.includes(dateStr);
                const isPast = dateObj < today;
                const isToday = (dateObj.getTime() === today.getTime());
                const isSunday = (dateObj.getDay() === 0);
                const isActiveDetail = (ctkActiveDetailDate === dateStr);
                const isCutoffLocked = !isPast && isDateCutoffLocked(dateStr);

                const liburDesc = extractEventText(liburMap[dateStr]);
                const peringatanDesc = extractEventText(peringatanMap[dateStr]);

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.setAttribute('data-date', dateStr);

                let tooltip = `${formatIndoDate(dateStr, true)}`;
                if (liburDesc) tooltip += ` | Libur: ${liburDesc}`;
                if (peringatanDesc) tooltip += ` | Peringatan: ${peringatanDesc}`;
                if (isCutoffLocked) tooltip += ` [Terkunci Cut-Off ${configJamCutoff} WIB]`;
                if (isSelected) tooltip += ` [Terbit Cetak Terjadwal]`;
                btn.title = tooltip;

                let btnClass = "h-7.5 sm:h-8 rounded-md p-0.5 transition cursor-pointer flex flex-col items-center justify-center relative select-none text-xs border ";

                if (isSelected) {
                    btnClass += "bg-kp-blue-600 text-white font-bold border-kp-blue-700 shadow-2xs ";
                } else if (isPast) {
                    btnClass += "text-slate-300 border-transparent cursor-not-allowed hover:bg-transparent ";
                } else if (isCutoffLocked) {
                    btnClass += "text-slate-500 font-semibold bg-slate-100 border-dashed border-slate-300 hover:bg-slate-200/70 ";
                } else if (liburDesc) {
                    btnClass += "text-rose-700 font-bold bg-rose-50 border-rose-200 hover:bg-rose-100 ";
                } else if (peringatanDesc) {
                    btnClass += "text-amber-800 font-bold bg-amber-50 border-amber-200 hover:bg-amber-100 ";
                } else if (isSunday) {
                    btnClass += "text-rose-600 font-semibold bg-rose-50/70 border-rose-200/80 hover:bg-rose-100 ";
                } else {
                    btnClass += "text-slate-700 font-medium border-transparent hover:bg-slate-100 ";
                }

                if (isActiveDetail && !isSelected) {
                    btnClass += "ring-2 ring-kp-blue-500/80 ";
                }

                if (isToday && !isSelected) {
                    btnClass += "ring-1 ring-slate-400 font-bold ";
                }

                btn.className = btnClass;
                btn.innerHTML = `<span class="leading-none text-xs font-semibold ${isSunday && !isSelected && !liburDesc && !isCutoffLocked ? 'text-rose-600' : ''}">${day}</span>`;

                btn.onclick = () => onCalendarCellClickCtk(dateStr, isPast);
                grid.appendChild(btn);
            }
        }

        function onCalendarCellClickCtk(dateStr, isPast) {
            ctkActiveDetailDate = dateStr;
            updatePanelInfoTanggalCtk(dateStr);

            if (isPast) {
                showCustomToast(`Tanggal ${formatIndoDate(dateStr, false)} sudah lewat batas edisi cetak.`, 'info', 'Edisi Sudah Lewat');
                renderCalendarCtk();
                return;
            }

            const targetQuota = parseInt(document.getElementById('in_qty_ctk')?.value || 1) || 1;
            const index = ctkSelectedDates.indexOf(dateStr);

            if (index > -1) {
                ctkSelectedDates.splice(index, 1);
                ctkSelectedDates.sort();
                syncTanggalCtk();
                updatePanelInfoTanggalCtk(dateStr);
                return;
            }

            if (isDateCutoffLocked(dateStr)) {
                showCustomToast(`Edisi ${formatIndoDate(dateStr, false)} telah melewati batas Cut-Off (${configJamCutoff} WIB).`, 'warning', 'Terkunci Cut-Off');
                renderCalendarCtk();
                return;
            }

            if (ctkSelectedDates.length >= targetQuota) {
                if (targetQuota === 1) {
                    ctkSelectedDates = [dateStr];
                    syncTanggalCtk();
                    updatePanelInfoTanggalCtk(dateStr);
                    return;
                }
                showCustomToast(`Seluruh ${targetQuota} tanggal edisi cetak telah terisi. Tambah angka pada "Frekuensi Terbit" jika ingin memilih tanggal lagi.`, 'warning', 'Batas Jadwal Terpenuhi');
                renderCalendarCtk();
                return;
            }

            ctkSelectedDates.push(dateStr);
            ctkSelectedDates.sort();
            syncTanggalCtk();
            updatePanelInfoTanggalCtk(dateStr);
        }

        function updatePanelInfoTanggalCtk(dateStr) {
            updateSharedPanelInfoTanggal('panelInfoTanggalCtk', 'infoTanggalLabelCtk', 'infoTanggalBadgeContainerCtk', dateStr, ctkSelectedDates.includes(dateStr), 'Cetak');
        }

        function renderSelectedDatesChipsCtk() {
            const container = document.getElementById('daftarTanggalChipsCtk');
            if (!container) return;
            container.innerHTML = '';

            const targetQuota = parseInt(document.getElementById('in_qty_ctk')?.value || 1) || 1;

            if (ctkSelectedDates.length === 0) {
                container.innerHTML = `
                    <div class="p-3 text-center rounded-xl border border-dashed border-slate-300 bg-white text-xs text-slate-400">
                        Belum ada tanggal dipilih. Klik tanggal di kalender.
                    </div>
                `;
            } else {
                ctkSelectedDates.forEach((d, i) => {
                    const chip = document.createElement('div');
                    const isActive = (ctkActiveDetailDate === d);
                    chip.className = `w-full flex items-center justify-between pl-3 pr-2 py-1.5 rounded-xl transition shadow-2xs group overflow-hidden ${
                        isActive 
                            ? 'bg-sky-50/50 border border-sky-400' 
                            : 'bg-white border border-slate-200 hover:border-kp-blue-300'
                    }`;
                    chip.innerHTML = `
                        <div class="flex items-center gap-2 cursor-pointer select-none whitespace-nowrap min-w-0" onclick="inspectSelectedDateCtk('${d}')">
                            <span class="text-xs font-bold text-slate-500 font-mono shrink-0">${i+1}.</span>
                            <span class="text-xs font-bold text-slate-800 group-hover:text-kp-blue-700 transition whitespace-nowrap">${formatIndoChip(d)}</span>
                        </div>
                        <button type="button" onclick="removeSingleDateCtk('${d}')"
                                title="Hapus tanggal ${d}"
                                class="w-5 h-5 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition cursor-pointer text-xs font-bold shrink-0 ml-2">
                            &times;
                        </button>
                    `;
                    container.appendChild(chip);
                });
            }

            const remaining = targetQuota - ctkSelectedDates.length;
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

            const labelTotal = document.getElementById('labelTotalDatesCountCtk');
            if (labelTotal) labelTotal.textContent = `${ctkSelectedDates.length} Tanggal`;
            const badgeParitas = document.getElementById('badgeParitasCtk');
            if (badgeParitas) badgeParitas.textContent = `${ctkSelectedDates.length} Tanggal Dipilih`;
        }

        function inspectSelectedDateCtk(dateStr) {
            ctkActiveDetailDate = dateStr;
            const parts = dateStr.split('-');
            ctkCalYear = parseInt(parts[0]);
            ctkCalMonth = parseInt(parts[1]) - 1;
            fetchKalenderBulan(ctkCalYear, ctkCalMonth + 1, () => {
                renderCalendarCtk();
                renderSelectedDatesChipsCtk();
                updatePanelInfoTanggalCtk(dateStr);
            });
        }

        function removeSingleDateCtk(dateStr) {
            const idx = ctkSelectedDates.indexOf(dateStr);
            if (idx > -1) {
                ctkSelectedDates.splice(idx, 1);
                syncTanggalCtk();
                updatePanelInfoTanggalCtk(dateStr);
            }
        }

        function resetSemuaTanggalCtk() {
            ctkSelectedDates = [];
            syncTanggalCtk();
            updatePanelInfoTanggalCtk(ctkActiveDetailDate || todayDateStr);
        }

        function stepJumlahMuatCtk(delta) {
            const input = document.getElementById('in_qty_ctk');
            let cur = parseInt(input.value) || 1;
            cur = Math.max(1, cur + delta);
            input.value = cur;

            if (ctkSelectedDates.length > cur) {
                ctkSelectedDates = ctkSelectedDates.slice(0, cur);
            }
            syncTanggalCtk();
        }

        function handleJumlahMuatCtkInput(raw) {
            const clean = raw.replace(/\D/g, '');
            document.getElementById('in_qty_ctk').value = clean;
            if (clean === '' || parseInt(clean) === 0) {
                renderSelectedDatesChipsCtk();
                return;
            }
            const total = parseInt(clean);
            if (ctkSelectedDates.length > total) {
                ctkSelectedDates = ctkSelectedDates.slice(0, total);
            }
            syncTanggalCtk();
        }

        function handleJumlahMuatCtkBlur(el) {
            let val = parseInt(el.value);
            if (isNaN(val) || val < 1) {
                val = Math.max(1, ctkSelectedDates.length);
                el.value = val;
            }
            syncTanggalCtk();
        }

        function syncTanggalCtk() {
            document.getElementById('ctk_tanggal_list').value = ctkSelectedDates.join(',');
            renderCalendarCtk();
            renderSelectedDatesChipsCtk();
            hitungKalkulasiPaket();
        }

        // ==========================================
        // 4. IKLAN ONLINE LOGIC
        // ==========================================
        function switchPlatformTabOnl(tabKey, tabName) {
            onlActiveTab = tabKey;
            document.getElementById('onl_kategori_tab').value = tabKey;
            document.querySelectorAll('.tab-platform-btn-onl').forEach(b => {
                b.className = 'tab-platform-btn-onl px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 bg-white text-slate-700 font-medium border border-slate-200 hover:border-slate-300 hover:bg-slate-50';
            });
            const activeBtn = document.getElementById(`tabBtnOnl_${tabKey}`);
            if (activeBtn) {
                activeBtn.className = 'tab-platform-btn-onl px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 bg-kp-blue-600 text-white font-semibold shadow-xs border border-kp-blue-600';
            }

            document.querySelectorAll('.tab-platform-content-onl').forEach(c => c.classList.add('hidden'));
            const activeContent = document.getElementById(`tabContentOnl_${tabKey}`);
            if (activeContent) activeContent.classList.remove('hidden');

            hitungTarifOnline();
        }

        function handlePortalCheckboxChangeOnl() {
            const checkedBoxes = Array.from(document.querySelectorAll('.portal-checkbox-onl:checked'));
            onlSelectedPortals = checkedBoxes.map(b => b.value);
            document.querySelectorAll('.portal-checkbox-card').forEach(card => {
                const chk = card.querySelector('.portal-checkbox-onl');
                const badge = card.querySelector('.badge-corner-check-onl');
                if (chk && chk.checked) {
                    card.className = 'portal-checkbox-card relative overflow-hidden flex flex-col justify-center px-3 py-2.5 rounded-xl border-2 border-kp-blue-600 bg-sky-50 text-kp-blue-900 font-bold ring-1 ring-kp-blue-500/20 shadow-2xs transition cursor-pointer select-none min-h-[68px]';
                    badge?.classList.remove('hidden');
                } else {
                    card.className = 'portal-checkbox-card relative overflow-hidden flex flex-col justify-center px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 font-medium transition cursor-pointer select-none min-h-[68px]';
                    badge?.classList.add('hidden');
                }
            });

            hitungTarifOnline();
        }

        function pilihSemuaPortalOnl(selectAll) {
            document.querySelectorAll('.portal-checkbox-onl').forEach(b => {
                b.checked = selectAll;
            });
            handlePortalCheckboxChangeOnl();
        }

        function toggleProdukIklanOnl(id, kode, nama, tarif, tab) {
            document.getElementById('onl_id_iklan').value = id;
            document.getElementById('onl_produk_jasa').value = nama;
            onlSelectedMedsosTarif = tarif;

            document.querySelectorAll('.produk-card-onl').forEach(c => {
                c.className = 'produk-card-onl text-left p-3.5 rounded-xl border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50 font-medium transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2.5 min-h-[92px]';
                c.querySelector('.badge-corner-check-onl-card')?.classList.add('hidden');
            });
            const activeCard = document.getElementById(`cardProdukOnl_${id}`);
            if (activeCard) {
                activeCard.className = 'produk-card-onl text-left p-3.5 rounded-xl border-2 border-kp-blue-600 bg-sky-50 shadow-2xs font-bold transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2.5 min-h-[92px]';
                activeCard.querySelector('.badge-corner-check-onl-card')?.classList.remove('hidden');
            }

            hitungTarifOnline();
        }

        function hitungTarifOnline() {
            let totalOnline = 0;
            if (onlActiveTab === 'PORTAL_BERITA') {
                totalOnline = onlSelectedPortals.length * onlTarifPortal;
            } else {
                totalOnline = onlSelectedMedsosTarif || 0;
            }
            document.getElementById('onl_harga_satuan').value = totalOnline;
            hitungKalkulasiPaket();
        }

        // ==========================================
        // ONLINE CALENDAR LOGIC (1:1 TRANSAKSI ONLINE)
        // ==========================================
        function goToTodayCalendarOnl() {
            const today = new Date();
            onlCalMonth = today.getMonth();
            onlCalYear = today.getFullYear();
            const todayStr = `${onlCalYear}-${String(onlCalMonth + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
            onlActiveDetailDate = todayStr;

            fetchKalenderBulan(onlCalYear, onlCalMonth + 1, () => {
                renderCalendarOnl();
                updatePanelInfoTanggalOnl(todayStr);
            });
        }

        function changeCalendarMonthOnl(delta) {
            onlCalMonth += delta;
            if (onlCalMonth > 11) {
                onlCalMonth = 0;
                onlCalYear++;
            }
            if (onlCalMonth < 0) {
                onlCalMonth = 11;
                onlCalYear--;
            }

            fetchKalenderBulan(onlCalYear, onlCalMonth + 1, () => {
                renderCalendarOnl();
            });
        }

        function renderCalendarOnl() {
            const monthTitleEl = document.getElementById('calendarMonthTitleOnl');
            if (monthTitleEl) {
                monthTitleEl.textContent = `${monthNamesIndo[onlCalMonth]} ${onlCalYear}`;
            }

            const grid = document.getElementById('calendarDaysGridOnl');
            if (!grid) return;
            grid.innerHTML = '';

            const cacheKey = `${onlCalYear}-${onlCalMonth + 1}`;
            const monthData = kalenderDataCache[cacheKey] || { libur: {}, peringatan: {} };
            const liburMap = monthData.libur || {};
            const peringatanMap = monthData.peringatan || {};

            const firstDayOfWeek = new Date(onlCalYear, onlCalMonth, 1).getDay();
            const daysInMonth = new Date(onlCalYear, onlCalMonth + 1, 0).getDate();

            for (let i = 0; i < firstDayOfWeek; i++) {
                const empty = document.createElement('div');
                empty.className = 'h-7.5 sm:h-8 pointer-events-none';
                grid.appendChild(empty);
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            for (let day = 1; day <= daysInMonth; day++) {
                const dateObj = new Date(onlCalYear, onlCalMonth, day);
                dateObj.setHours(0, 0, 0, 0);
                const dateStr = `${onlCalYear}-${String(onlCalMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                const isSelected = onlSelectedDates.includes(dateStr);
                const isPast = dateObj < today;
                const isToday = (dateObj.getTime() === today.getTime());
                const isSunday = (dateObj.getDay() === 0);
                const isActiveDetail = (onlActiveDetailDate === dateStr);
                const isCutoffLocked = !isPast && isDateCutoffLocked(dateStr);

                const liburDesc = extractEventText(liburMap[dateStr]);
                const peringatanDesc = extractEventText(peringatanMap[dateStr]);

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.setAttribute('data-date', dateStr);

                let tooltip = `${formatIndoDate(dateStr, true)}`;
                if (liburDesc) tooltip += ` | Libur: ${liburDesc}`;
                if (peringatanDesc) tooltip += ` | Peringatan: ${peringatanDesc}`;
                if (isCutoffLocked) tooltip += ` [Terkunci Cut-Off ${configJamCutoff} WIB]`;
                if (isSelected) tooltip += ` [Tayang Online Terjadwal]`;
                btn.title = tooltip;

                let btnClass = "h-7.5 sm:h-8 rounded-md p-0.5 transition cursor-pointer flex flex-col items-center justify-center relative select-none text-xs border ";

                if (isSelected) {
                    btnClass += "bg-kp-blue-600 text-white font-bold border-kp-blue-700 shadow-2xs ";
                } else if (isPast) {
                    btnClass += "text-slate-300 border-transparent cursor-not-allowed hover:bg-transparent ";
                } else if (isCutoffLocked) {
                    btnClass += "text-slate-500 font-semibold bg-slate-100 border-dashed border-slate-300 hover:bg-slate-200/70 ";
                } else if (liburDesc) {
                    btnClass += "text-rose-700 font-bold bg-rose-50 border-rose-200 hover:bg-rose-100 ";
                } else if (peringatanDesc) {
                    btnClass += "text-amber-800 font-bold bg-amber-50 border-amber-200 hover:bg-amber-100 ";
                } else if (isSunday) {
                    btnClass += "text-rose-600 font-semibold bg-rose-50/70 border-rose-200/80 hover:bg-rose-100 ";
                } else {
                    btnClass += "text-slate-700 font-medium border-transparent hover:bg-slate-100 ";
                }

                if (isActiveDetail && !isSelected) {
                    btnClass += "ring-2 ring-kp-blue-500/80 ";
                }

                if (isToday && !isSelected) {
                    btnClass += "ring-1 ring-slate-400 font-bold ";
                }

                btn.className = btnClass;
                btn.innerHTML = `<span class="leading-none text-xs font-semibold ${isSunday && !isSelected && !liburDesc && !isCutoffLocked ? 'text-rose-600' : ''}">${day}</span>`;

                btn.onclick = () => onCalendarCellClickOnl(dateStr, isPast);
                grid.appendChild(btn);
            }
        }

        function onCalendarCellClickOnl(dateStr, isPast) {
            onlActiveDetailDate = dateStr;
            updatePanelInfoTanggalOnl(dateStr);

            if (isPast) {
                showCustomToast(`Tanggal ${formatIndoDate(dateStr, false)} sudah lewat batas tayang online.`, 'info', 'Jadwal Lewat');
                renderCalendarOnl();
                return;
            }

            const targetQuota = parseInt(document.getElementById('in_qty_onl')?.value || 1) || 1;
            const index = onlSelectedDates.indexOf(dateStr);

            if (index > -1) {
                onlSelectedDates.splice(index, 1);
                onlSelectedDates.sort();
                syncTanggalOnl();
                updatePanelInfoTanggalOnl(dateStr);
                return;
            }

            if (isDateCutoffLocked(dateStr)) {
                showCustomToast(`Jadwal ${formatIndoDate(dateStr, false)} telah melewati batas Cut-Off (${configJamCutoff} WIB).`, 'warning', 'Terkunci Cut-Off');
                renderCalendarOnl();
                return;
            }

            if (onlSelectedDates.length >= targetQuota) {
                if (targetQuota === 1) {
                    onlSelectedDates = [dateStr];
                    syncTanggalOnl();
                    updatePanelInfoTanggalOnl(dateStr);
                    return;
                }
                showCustomToast(`Seluruh ${targetQuota} tanggal tayang online telah terisi. Tambah angka pada "Frekuensi Tayang" jika ingin memilih tanggal lagi.`, 'warning', 'Batas Jadwal Terpenuhi');
                renderCalendarOnl();
                return;
            }

            onlSelectedDates.push(dateStr);
            onlSelectedDates.sort();
            syncTanggalOnl();
            updatePanelInfoTanggalOnl(dateStr);
        }

        function updatePanelInfoTanggalOnl(dateStr) {
            updateSharedPanelInfoTanggal('panelInfoTanggalOnl', 'infoTanggalLabelOnl', 'infoTanggalBadgeContainerOnl', dateStr, onlSelectedDates.includes(dateStr), 'Online');
        }

        function renderSelectedDatesChipsOnl() {
            const container = document.getElementById('daftarTanggalChipsOnl');
            if (!container) return;
            container.innerHTML = '';

            const targetQuota = parseInt(document.getElementById('in_qty_onl')?.value || 1) || 1;

            if (onlSelectedDates.length === 0) {
                container.innerHTML = `
                    <div class="p-3 text-center rounded-xl border border-dashed border-slate-300 bg-white text-xs text-slate-400">
                        Belum ada tanggal dipilih. Klik tanggal di kalender.
                    </div>
                `;
            } else {
                onlSelectedDates.forEach((d, i) => {
                    const chip = document.createElement('div');
                    const isActive = (onlActiveDetailDate === d);
                    chip.className = `w-full flex items-center justify-between pl-3 pr-2 py-1.5 rounded-xl transition shadow-2xs group overflow-hidden ${
                        isActive 
                            ? 'bg-sky-50/50 border border-sky-400' 
                            : 'bg-white border border-slate-200 hover:border-kp-blue-300'
                    }`;
                    chip.innerHTML = `
                        <div class="flex items-center gap-2 cursor-pointer select-none whitespace-nowrap min-w-0" onclick="inspectSelectedDateOnl('${d}')">
                            <span class="text-xs font-bold text-slate-500 font-mono shrink-0">${i+1}.</span>
                            <span class="text-xs font-bold text-slate-800 group-hover:text-kp-blue-700 transition whitespace-nowrap">${formatIndoChip(d)}</span>
                        </div>
                        <button type="button" onclick="removeSingleDateOnl('${d}')"
                                title="Hapus tanggal ${d}"
                                class="w-5 h-5 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition cursor-pointer text-xs font-bold shrink-0 ml-2">
                            &times;
                        </button>
                    `;
                    container.appendChild(chip);
                });
            }

            const remaining = targetQuota - onlSelectedDates.length;
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

            const labelTotal = document.getElementById('labelTotalDatesCountOnl');
            if (labelTotal) labelTotal.textContent = `${onlSelectedDates.length} Tanggal`;
            const badgeParitas = document.getElementById('badgeParitasOnl');
            if (badgeParitas) badgeParitas.textContent = `${onlSelectedDates.length} Tanggal Dipilih`;
        }

        function inspectSelectedDateOnl(dateStr) {
            onlActiveDetailDate = dateStr;
            const parts = dateStr.split('-');
            onlCalYear = parseInt(parts[0]);
            onlCalMonth = parseInt(parts[1]) - 1;
            fetchKalenderBulan(onlCalYear, onlCalMonth + 1, () => {
                renderCalendarOnl();
                renderSelectedDatesChipsOnl();
                updatePanelInfoTanggalOnl(dateStr);
            });
        }

        function removeSingleDateOnl(dateStr) {
            const idx = onlSelectedDates.indexOf(dateStr);
            if (idx > -1) {
                onlSelectedDates.splice(idx, 1);
                syncTanggalOnl();
                updatePanelInfoTanggalOnl(dateStr);
            }
        }

        function resetSemuaTanggalOnl() {
            onlSelectedDates = [];
            syncTanggalOnl();
            updatePanelInfoTanggalOnl(onlActiveDetailDate || todayDateStr);
        }

        function stepJumlahTayangOnl(delta) {
            const input = document.getElementById('in_qty_onl');
            let cur = parseInt(input.value) || 1;
            cur = Math.max(1, cur + delta);
            input.value = cur;

            if (onlSelectedDates.length > cur) {
                onlSelectedDates = onlSelectedDates.slice(0, cur);
            }
            syncTanggalOnl();
        }

        function handleJumlahTayangOnlInput(raw) {
            const clean = raw.replace(/\D/g, '');
            document.getElementById('in_qty_onl').value = clean;
            if (clean === '' || parseInt(clean) === 0) {
                renderSelectedDatesChipsOnl();
                return;
            }
            const total = parseInt(clean);
            if (onlSelectedDates.length > total) {
                onlSelectedDates = onlSelectedDates.slice(0, total);
            }
            syncTanggalOnl();
        }

        function handleJumlahTayangOnlBlur(el) {
            let val = parseInt(el.value);
            if (isNaN(val) || val < 1) {
                val = Math.max(1, onlSelectedDates.length);
                el.value = val;
            }
            syncTanggalOnl();
        }

        function syncTanggalOnl() {
            document.getElementById('onl_tanggal_list').value = onlSelectedDates.join(',');
            renderCalendarOnl();
            renderSelectedDatesChipsOnl();
            hitungKalkulasiPaket();
        }

        // ==========================================
        // 5. PRIANGAN TV LOGIC (1:1 DENGAN TRANSAKSI PRIANGAN TV)
        // ==========================================
        let selectedTvProducts = {};
        const PORTAL_BASE_TARIF = {{ (int) ($configs['tarif_portal'] ?? 750000) }};

        function switchProductTabTv(tabKey) {
            document.getElementById('ptv_kategori_tab').value = tabKey;
            document.querySelectorAll('.tab-product-btn-tv').forEach(b => {
                b.className = 'tab-product-btn-tv px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 bg-white text-slate-700 font-medium border border-slate-200 hover:border-slate-300 hover:bg-slate-50';
                const badge = b.querySelector('span:nth-child(2)');
                if (badge) badge.className = 'px-1.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600';
            });
            const activeBtn = document.getElementById(`tabBtnTv_${tabKey}`);
            if (activeBtn) {
                activeBtn.className = 'tab-product-btn-tv px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 bg-kp-blue-600 text-white font-semibold shadow-xs border border-kp-blue-600';
                const badge = activeBtn.querySelector('span:nth-child(2)');
                if (badge) badge.className = 'px-1.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white';
            }

            document.querySelectorAll('.tab-product-content-tv').forEach(c => c.classList.add('hidden'));
            const activeContent = document.getElementById(`tabContentTv_${tabKey}`);
            if (activeContent) activeContent.classList.remove('hidden');
        }

        function handleTvPortalCheckboxChange() {
            document.querySelectorAll('.tv-portal-checkbox-card').forEach(card => {
                const cb = card.querySelector('.tv-portal-checkbox');
                const badge = card.querySelector('.badge-corner-check');
                if (cb && cb.checked) {
                    card.className = "tv-portal-checkbox-card relative overflow-hidden flex flex-col justify-center px-3 py-2.5 rounded-xl border-2 border-kp-blue-600 bg-sky-50 text-kp-blue-900 font-bold ring-1 ring-kp-blue-500/20 shadow-2xs transition cursor-pointer select-none min-h-[68px]";
                    if (badge) badge.classList.remove('hidden');
                } else {
                    card.className = "tv-portal-checkbox-card relative overflow-hidden flex flex-col justify-center px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 font-medium transition cursor-pointer select-none min-h-[68px]";
                    if (badge) badge.classList.add('hidden');
                }
            });
            recalculateTvSelection();
        }

        function pilihSemuaTvPortal(checked) {
            document.querySelectorAll('.tv-portal-checkbox').forEach(cb => {
                cb.checked = checked;
            });
            handleTvPortalCheckboxChange();
        }

        function toggleProdukTv(id, kode, nama, tarif, tabKey) {
            const isAlreadySelected = !!selectedTvProducts[id];
            const matchingCards = document.querySelectorAll(`.tv-produk-card[data-id="${id}"]`);

            if (isAlreadySelected) {
                delete selectedTvProducts[id];
                matchingCards.forEach(targetCard => {
                    targetCard.className = "tv-produk-card text-left p-3.5 rounded-xl border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50 font-medium transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2.5 min-h-[92px]";
                    const badge = targetCard.querySelector('.badge-corner-check');
                    if (badge) badge.classList.add('hidden');
                });
            } else {
                selectedTvProducts[id] = { id: id, kode: kode, nama: nama, tarif: Number(tarif), tabKey: tabKey };
                matchingCards.forEach(targetCard => {
                    targetCard.className = "tv-produk-card text-left p-3.5 rounded-xl border-2 border-kp-blue-600 bg-sky-50/20 ring-1 ring-kp-blue-500/20 shadow-xs font-bold transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2.5 min-h-[92px]";
                    const badge = targetCard.querySelector('.badge-corner-check');
                    if (badge) badge.classList.remove('hidden');
                });
            }

            recalculateTvSelection();
        }

        function recalculateTvSelection() {
            const items = Object.values(selectedTvProducts);
            let totalTarif = 0;
            items.forEach(item => {
                totalTarif += Number(item.tarif);
            });

            // Akumulasi Tarif 10 Portal Web
            const checkedPortals = Array.from(document.querySelectorAll('.tv-portal-checkbox:checked')).map(cb => cb.value);
            const portalCost = checkedPortals.length * PORTAL_BASE_TARIF;
            totalTarif += portalCost;

            // Update input harga
            const inputHarga = document.getElementById('ptv_harga_satuan');
            if (inputHarga) {
                inputHarga.value = totalTarif;
            }

            // Update hidden container produk IDs
            const contProd = document.getElementById('containerTvSelectedProductIds');
            if (contProd) {
                contProd.innerHTML = '';
                items.forEach(item => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'selected_produk_ids_tv[]';
                    inp.value = item.id;
                    contProd.appendChild(inp);
                });
            }

            const idInput = document.getElementById('ptv_id_iklan');
            if (idInput) {
                idInput.value = items.length > 0 ? items[0].id : (checkedPortals.length > 0 ? '{{ $iklanpriangan->first()?->id ?? "" }}' : '');
            }

            const namaInput = document.getElementById('ptv_produk_jasa');
            if (namaInput) {
                if (items.length > 0 && checkedPortals.length > 0) {
                    namaInput.value = items.map(i => i.nama).join(', ') + ' + ' + checkedPortals.length + ' Portal Web';
                } else if (items.length > 0) {
                    namaInput.value = items.map(i => i.nama).join(', ');
                } else if (checkedPortals.length > 0) {
                    namaInput.value = 'Jaringan Portal Web (' + checkedPortals.length + ' Portal)';
                } else {
                    namaInput.value = 'Paket Priangan TV';
                }
            }

            hitungKalkulasiPaket();
        }

        // ==========================================
        // TV CALENDAR LOGIC (1:1 TRANSAKSI PRIANGAN TV)
        // ==========================================
        function goToTodayCalendarTv() {
            const today = new Date();
            ptvCalMonth = today.getMonth();
            ptvCalYear = today.getFullYear();
            const todayStr = `${ptvCalYear}-${String(ptvCalMonth + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
            ptvActiveDetailDate = todayStr;

            fetchKalenderBulan(ptvCalYear, ptvCalMonth + 1, () => {
                renderCalendarTv();
                updatePanelInfoTanggalTv(todayStr);
            });
        }

        function changeCalendarMonthTv(delta) {
            ptvCalMonth += delta;
            if (ptvCalMonth > 11) {
                ptvCalMonth = 0;
                ptvCalYear++;
            }
            if (ptvCalMonth < 0) {
                ptvCalMonth = 11;
                ptvCalYear--;
            }

            fetchKalenderBulan(ptvCalYear, ptvCalMonth + 1, () => {
                renderCalendarTv();
            });
        }

        function renderCalendarTv() {
            const monthTitleEl = document.getElementById('calendarMonthTitleTv');
            if (monthTitleEl) {
                monthTitleEl.textContent = `${monthNamesIndo[ptvCalMonth]} ${ptvCalYear}`;
            }

            const grid = document.getElementById('calendarDaysGridTv');
            if (!grid) return;
            grid.innerHTML = '';

            const cacheKey = `${ptvCalYear}-${ptvCalMonth + 1}`;
            const monthData = kalenderDataCache[cacheKey] || { libur: {}, peringatan: {} };
            const liburMap = monthData.libur || {};
            const peringatanMap = monthData.peringatan || {};

            const firstDayOfWeek = new Date(ptvCalYear, ptvCalMonth, 1).getDay();
            const daysInMonth = new Date(ptvCalYear, ptvCalMonth + 1, 0).getDate();

            for (let i = 0; i < firstDayOfWeek; i++) {
                const empty = document.createElement('div');
                empty.className = 'h-7.5 sm:h-8 pointer-events-none';
                grid.appendChild(empty);
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            for (let day = 1; day <= daysInMonth; day++) {
                const dateObj = new Date(ptvCalYear, ptvCalMonth, day);
                dateObj.setHours(0, 0, 0, 0);
                const dateStr = `${ptvCalYear}-${String(ptvCalMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                const isSelected = ptvSelectedDates.includes(dateStr);
                const isPast = dateObj < today;
                const isToday = (dateObj.getTime() === today.getTime());
                const isSunday = (dateObj.getDay() === 0);
                const isActiveDetail = (ptvActiveDetailDate === dateStr);
                const isCutoffLocked = !isPast && isDateCutoffLocked(dateStr);

                const liburDesc = extractEventText(liburMap[dateStr]);
                const peringatanDesc = extractEventText(peringatanMap[dateStr]);

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.setAttribute('data-date', dateStr);

                let tooltip = `${formatIndoDate(dateStr, true)}`;
                if (liburDesc) tooltip += ` | Libur: ${liburDesc}`;
                if (peringatanDesc) tooltip += ` | Peringatan: ${peringatanDesc}`;
                if (isCutoffLocked) tooltip += ` [Terkunci Cut-Off ${configJamCutoff} WIB]`;
                if (isSelected) tooltip += ` [Siar TV Terjadwal]`;
                btn.title = tooltip;

                let btnClass = "h-7.5 sm:h-8 rounded-md p-0.5 transition cursor-pointer flex flex-col items-center justify-center relative select-none text-xs border ";

                if (isSelected) {
                    btnClass += "bg-kp-blue-600 text-white font-bold border-kp-blue-700 shadow-2xs ";
                } else if (isPast) {
                    btnClass += "text-slate-300 border-transparent cursor-not-allowed hover:bg-transparent ";
                } else if (isCutoffLocked) {
                    btnClass += "text-slate-500 font-semibold bg-slate-100 border-dashed border-slate-300 hover:bg-slate-200/70 ";
                } else if (liburDesc) {
                    btnClass += "text-rose-700 font-bold bg-rose-50 border-rose-200 hover:bg-rose-100 ";
                } else if (peringatanDesc) {
                    btnClass += "text-amber-800 font-bold bg-amber-50 border-amber-200 hover:bg-amber-100 ";
                } else if (isSunday) {
                    btnClass += "text-rose-600 font-semibold bg-rose-50/70 border-rose-200/80 hover:bg-rose-100 ";
                } else {
                    btnClass += "text-slate-700 font-medium border-transparent hover:bg-slate-100 ";
                }

                if (isActiveDetail && !isSelected) {
                    btnClass += "ring-2 ring-kp-blue-500/80 ";
                }

                if (isToday && !isSelected) {
                    btnClass += "ring-1 ring-slate-400 font-bold ";
                }

                btn.className = btnClass;
                btn.innerHTML = `<span class="leading-none text-xs font-semibold ${isSunday && !isSelected && !liburDesc && !isCutoffLocked ? 'text-rose-600' : ''}">${day}</span>`;

                btn.onclick = () => onCalendarCellClickTv(dateStr, isPast);
                grid.appendChild(btn);
            }
        }

        function onCalendarCellClickTv(dateStr, isPast) {
            ptvActiveDetailDate = dateStr;
            updatePanelInfoTanggalTv(dateStr);

            if (isPast) {
                showCustomToast(`Tanggal ${formatIndoDate(dateStr, false)} sudah lewat batas jadwal siar TV.`, 'info', 'Jadwal Lewat');
                renderCalendarTv();
                return;
            }

            const targetQuota = parseInt(document.getElementById('in_qty_ptv')?.value || 1) || 1;
            const index = ptvSelectedDates.indexOf(dateStr);

            if (index > -1) {
                ptvSelectedDates.splice(index, 1);
                ptvSelectedDates.sort();
                syncTanggalTv();
                updatePanelInfoTanggalTv(dateStr);
                return;
            }

            if (isDateCutoffLocked(dateStr)) {
                showCustomToast(`Jadwal siar ${formatIndoDate(dateStr, false)} telah melewati batas Cut-Off (${configJamCutoff} WIB).`, 'warning', 'Terkunci Cut-Off');
                renderCalendarTv();
                return;
            }

            if (ptvSelectedDates.length >= targetQuota) {
                if (targetQuota === 1) {
                    ptvSelectedDates = [dateStr];
                    syncTanggalTv();
                    updatePanelInfoTanggalTv(dateStr);
                    return;
                }
                showCustomToast(`Seluruh ${targetQuota} tanggal siar TV telah terisi. Tambah angka pada "Frekuensi Spot / Siar" jika ingin memilih tanggal lagi.`, 'warning', 'Batas Jadwal Terpenuhi');
                renderCalendarTv();
                return;
            }

            ptvSelectedDates.push(dateStr);
            ptvSelectedDates.sort();
            syncTanggalTv();
            updatePanelInfoTanggalTv(dateStr);
        }

        function updatePanelInfoTanggalTv(dateStr) {
            updateSharedPanelInfoTanggal('panelInfoTanggalTv', 'infoTanggalLabelTv', 'infoTanggalBadgeContainerTv', dateStr, ptvSelectedDates.includes(dateStr), 'Siar TV');
        }

        function renderSelectedDatesChipsTv() {
            const container = document.getElementById('daftarTanggalChipsPtv');
            if (!container) return;
            container.innerHTML = '';

            const targetQuota = parseInt(document.getElementById('in_qty_ptv')?.value || 1) || 1;

            if (ptvSelectedDates.length === 0) {
                container.innerHTML = `
                    <div class="p-3 text-center rounded-xl border border-dashed border-slate-300 bg-white text-xs text-slate-400">
                        Belum ada tanggal dipilih. Klik tanggal di kalender.
                    </div>
                `;
            } else {
                ptvSelectedDates.forEach((d, i) => {
                    const chip = document.createElement('div');
                    const isActive = (ptvActiveDetailDate === d);
                    chip.className = `w-full flex items-center justify-between pl-3 pr-2 py-1.5 rounded-xl transition shadow-2xs group overflow-hidden ${
                        isActive 
                            ? 'bg-sky-50/50 border border-sky-400' 
                            : 'bg-white border border-slate-200 hover:border-kp-blue-300'
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

            const remaining = targetQuota - ptvSelectedDates.length;
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

            const labelTotal = document.getElementById('labelTotalDatesCountPtv');
            if (labelTotal) labelTotal.textContent = `${ptvSelectedDates.length} Tanggal`;
            const badgeParitas = document.getElementById('badgeParitasTv');
            if (badgeParitas) badgeParitas.textContent = `${ptvSelectedDates.length} Tanggal Dipilih`;
        }

        function inspectSelectedDateTv(dateStr) {
            ptvActiveDetailDate = dateStr;
            const parts = dateStr.split('-');
            ptvCalYear = parseInt(parts[0]);
            ptvCalMonth = parseInt(parts[1]) - 1;
            fetchKalenderBulan(ptvCalYear, ptvCalMonth + 1, () => {
                renderCalendarTv();
                renderSelectedDatesChipsTv();
                updatePanelInfoTanggalTv(dateStr);
            });
        }

        function removeSingleDateTv(dateStr) {
            const idx = ptvSelectedDates.indexOf(dateStr);
            if (idx > -1) {
                ptvSelectedDates.splice(idx, 1);
                syncTanggalTv();
                updatePanelInfoTanggalTv(dateStr);
            }
        }

        function resetSemuaTanggalTv() {
            ptvSelectedDates = [];
            syncTanggalTv();
            updatePanelInfoTanggalTv(ptvActiveDetailDate || todayDateStr);
        }

        function stepJumlahTayangTv(delta) {
            const input = document.getElementById('in_qty_ptv');
            let cur = parseInt(input.value) || 1;
            cur = Math.max(1, cur + delta);
            input.value = cur;

            if (ptvSelectedDates.length > cur) {
                ptvSelectedDates = ptvSelectedDates.slice(0, cur);
            }
            syncTanggalTv();
        }

        function handleJumlahTayangTvInput(raw) {
            const clean = raw.replace(/\D/g, '');
            document.getElementById('in_qty_ptv').value = clean;
            if (clean === '' || parseInt(clean) === 0) {
                renderSelectedDatesChipsTv();
                return;
            }
            const total = parseInt(clean);
            if (ptvSelectedDates.length > total) {
                ptvSelectedDates = ptvSelectedDates.slice(0, total);
            }
            syncTanggalTv();
        }

        function handleJumlahTayangTvBlur(el) {
            let val = parseInt(el.value);
            if (isNaN(val) || val < 1) {
                val = Math.max(1, ptvSelectedDates.length);
                el.value = val;
            }
            syncTanggalTv();
        }

        function syncTanggalTv() {
            document.getElementById('ptv_tanggal_list').value = ptvSelectedDates.join(',');
            renderCalendarTv();
            renderSelectedDatesChipsTv();
            hitungKalkulasiPaket();
        }

        // ==========================================
        // 6. STRUK KALKULASI FINANSIAL GABUNGAN
        // ==========================================
        function hitungKalkulasiPaket() {
            let subCtk = 0;
            let subOnl = 0;
            let subPtv = 0;

            if (activeMedia.CTK) {
                const hargaCtk = cleanNum(document.getElementById('ctk_harga_satuan').value);
                const qtyCtk = Math.max(1, ctkSelectedDates.length);
                subCtk = hargaCtk * qtyCtk;
                document.getElementById('rowStrukCtk').classList.remove('hidden');
                document.getElementById('strukSubtotalCtk').innerText = formatRupiah(subCtk);
            } else {
                document.getElementById('rowStrukCtk').classList.add('hidden');
            }

            if (activeMedia.ONL) {
                const hargaOnl = cleanNum(document.getElementById('onl_harga_satuan').value);
                const qtyOnl = Math.max(1, onlSelectedDates.length);
                subOnl = hargaOnl * qtyOnl;
                document.getElementById('rowStrukOnl').classList.remove('hidden');
                document.getElementById('strukSubtotalOnl').innerText = formatRupiah(subOnl);
            } else {
                document.getElementById('rowStrukOnl').classList.add('hidden');
            }

            if (activeMedia.PTV) {
                const hargaPtv = cleanNum(document.getElementById('ptv_harga_satuan').value);
                const qtyPtv = Math.max(1, ptvSelectedDates.length);
                subPtv = hargaPtv * qtyPtv;
                document.getElementById('rowStrukPtv').classList.remove('hidden');
                document.getElementById('strukSubtotalPtv').innerText = formatRupiah(subPtv);
            } else {
                document.getElementById('rowStrukPtv').classList.add('hidden');
            }

            const totalBrutto = subCtk + subOnl + subPtv;
            document.getElementById('strukBrutto').innerText = formatRupiah(totalBrutto);

            // Diskon
            let diskon = diskonNominalVal;
            if (diskonPersenVal > 0) {
                diskon = Math.round(totalBrutto * (diskonPersenVal / 100));
            }
            diskon = Math.min(totalBrutto, Math.max(0, diskon));
            document.getElementById('nilai_diskon').value = diskon;

            const netto = Math.max(0, totalBrutto - diskon);
            const ppn = Math.round(netto * (ppnRate / 100));
            const totalTagihan = netto + ppn;

            document.getElementById('strukNetto').innerText = formatRupiah(netto);
            document.getElementById('strukPpn').innerText = formatRupiah(ppn);
            document.getElementById('strukTotalTagihan').innerText = formatRupiah(totalTagihan);

            // Update default bayar jika tunai
            const elBayar = document.getElementById('jumlah_bayar');
            const isTunai = (document.getElementById('jenis_pembayaran').value === 'Tunai');
            if (isTunai && document.activeElement !== elBayar) {
                if (elBayar) {
                    elBayar.value = totalTagihan > 0 ? totalTagihan.toLocaleString('id-ID') : '0';
                }
            }

            const bayarVal = cleanNum(elBayar?.value || '0');
            const sisaPiutang = Math.max(0, totalTagihan - bayarVal);
            const elPiutang = document.getElementById('strukPiutangPaket');
            if (elPiutang) {
                elPiutang.innerText = formatRupiah(sisaPiutang);
            }
        }

        function handleDiskonPersenChange(val) {
            diskonPersenVal = parseFloat(val) || 0;
            const brutto = cleanNum(document.getElementById('strukBrutto').innerText);
            diskonNominalVal = Math.round(brutto * (diskonPersenVal / 100));
            document.getElementById('diskon_nominal_display').value = diskonNominalVal.toLocaleString('id-ID');
            hitungKalkulasiPaket();
        }

        function handleDiskonNominalInput(el) {
            diskonNominalVal = cleanNum(el.value);
            el.value = diskonNominalVal.toLocaleString('id-ID');
            const brutto = cleanNum(document.getElementById('strukBrutto').innerText);
            diskonPersenVal = brutto > 0 ? ((diskonNominalVal / brutto) * 100) : 0;
            document.getElementById('diskon_persen').value = diskonPersenVal.toFixed(1);
            hitungKalkulasiPaket();
        }

        function setBayarLunasPaket() {
            const tot = cleanNum(document.getElementById('strukTotalTagihan')?.innerText || '0');
            const el = document.getElementById('jumlah_bayar');
            if (el) {
                el.value = tot > 0 ? tot.toLocaleString('id-ID') : '0';
            }
            const elPiutang = document.getElementById('strukPiutangPaket');
            if (elPiutang) {
                elPiutang.innerText = 'Rp 0';
            }
        }

        function handleBayarInput() {
            const el = document.getElementById('jumlah_bayar');
            const val = cleanNum(el?.value || '0');
            if (el) {
                el.value = val > 0 ? val.toLocaleString('id-ID') : '0';
            }
            const tot = cleanNum(document.getElementById('strukTotalTagihan')?.innerText || '0');
            const sisa = Math.max(0, tot - val);
            const elPiutang = document.getElementById('strukPiutangPaket');
            if (elPiutang) {
                elPiutang.innerText = formatRupiah(sisa);
            }
        }

        function handleKomisiPersenChange(val) {
            const p = parseFloat(val) || 0;
            const brutto = cleanNum(document.getElementById('strukBrutto').innerText);
            const nom = Math.round(brutto * (p / 100));
            document.getElementById('komisi_nominal').value = nom.toLocaleString('id-ID');
        }

        function handleKomisiNominalInput(el) {
            const nom = cleanNum(el.value);
            el.value = nom.toLocaleString('id-ID');
            const brutto = cleanNum(document.getElementById('strukBrutto').innerText);
            const p = brutto > 0 ? ((nom / brutto) * 100) : 0;
            document.getElementById('komisi_persen').value = p.toFixed(1);
        }

        function handleInsentifPersenChange(val) {
            const p = parseFloat(val) || 0;
            const brutto = cleanNum(document.getElementById('strukBrutto').innerText);
            const nom = Math.round(brutto * (p / 100));
            document.getElementById('insentif_nominal').value = nom.toLocaleString('id-ID');
        }

        function handleInsentifNominalInput(el) {
            const nom = cleanNum(el.value);
            el.value = nom.toLocaleString('id-ID');
            const brutto = cleanNum(document.getElementById('strukBrutto').innerText);
            const p = brutto > 0 ? ((nom / brutto) * 100) : 0;
            document.getElementById('insentif_persen').value = p.toFixed(1);
        }

        // ==========================================
        // 7. MODAL LOGIC & FORM SUBMISSION
        // ==========================================
        function openModalPelangganBaru() {
            document.getElementById('modalPelangganBaru').classList.remove('hidden');
        }
        function closeModalPelangganBaru() {
            document.getElementById('modalPelangganBaru').classList.add('hidden');
        }
        function simpanPelangganBaruOnTheFly() {
            const nama = document.getElementById('modal_nama_instansi').value.trim();
            if (!nama) {
                alert('Nama instansi / pemasang wajib diisi!');
                return;
            }
            const jenis = document.getElementById('modal_jenis_klien').value;
            const telp = document.getElementById('modal_no_telp').value.trim();
            const alamat = document.getElementById('modal_alamat').value.trim();

            const btn = document.getElementById('btnSimpanPelangganModal');
            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            fetch('{{ route("pelanggan.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    nama_instansi: nama,
                    jenis_klien: jenis,
                    no_telp: telp,
                    alamat: alamat
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerText = 'Simpan & Pilih';
                const p = data.data || data.pelanggan;
                if (p && p.id) {
                    window.currentPelangganList.unshift(p);
                    selectPelangganItem(p.id);
                    closeModalPelangganBaru();
                } else {
                    alert('Gagal menyimpan pelanggan: ' + (data.message || 'Coba lagi'));
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerText = 'Simpan & Pilih';
                alert('Terjadi kesalahan koneksi.');
            });
        }

        function openModalSalesAeBaru() {
            document.getElementById('modalSalesAeBaru').classList.remove('hidden');
        }
        function closeModalSalesAeBaru() {
            document.getElementById('modalSalesAeBaru').classList.add('hidden');
        }
        function autoGenerateKodeSalesModal() {
            const jenis = document.getElementById('modal_sales_jenis').value;
            fetch(`{{ route('sales-ae.generate-kode') }}?jenis=${jenis}`)
                .then(res => res.json())
                .then(data => {
                    if (data.kode) document.getElementById('modal_sales_kode').value = data.kode;
                })
                .catch(() => {});
        }
        function simpanSalesAeOnTheFly() {
            const jenis = document.getElementById('modal_sales_jenis').value;
            const kode = document.getElementById('modal_sales_kode').value.trim();
            const nama = document.getElementById('modal_sales_nama').value.trim();

            if (!kode || !nama) {
                alert('Kode AE dan Nama sales wajib diisi!');
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
                btn.innerText = 'Simpan & Pilih';
                if (data.success && data.salesAe) {
                    const sel = document.getElementById('sales_ae_id');
                    const opt = document.createElement('option');
                    opt.value = data.salesAe.id;
                    opt.text = `${data.salesAe.kode_ae} - ${data.salesAe.nama_ae}`;
                    opt.selected = true;
                    sel.appendChild(opt);
                    closeModalSalesAeBaru();
                } else {
                    alert('Gagal menyimpan Sales AE: ' + (data.message || 'Coba lagi'));
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerText = 'Simpan & Pilih';
                alert('Terjadi kesalahan koneksi.');
            });
        }

        function bersihkanSorotValidasi() {
            document.querySelectorAll('.form-inline-error').forEach(el => el.remove());
            document.querySelectorAll('.sorot-error-active').forEach(el => {
                el.classList.remove(
                    'sorot-error-active',
                    'border-rose-500',
                    'ring-4',
                    'ring-rose-500/25',
                    'bg-rose-50/20'
                );
            });
        }

        function sorotKolomValidasi(targetElOrId, pesan, focusEl = null) {
            bersihkanSorotValidasi();

            const el = typeof targetElOrId === 'string' ? document.getElementById(targetElOrId) : targetElOrId;
            if (!el) return;

            // Tambahkan visual highlight merah menyala
            el.classList.add(
                'sorot-error-active',
                'border-rose-500',
                'ring-4',
                'ring-rose-500/25',
                'bg-rose-50/20'
            );

            // Buat teks pemberitahuan merah langsung di bawah kolom
            const errDiv = document.createElement('div');
            errDiv.className = 'form-inline-error mt-1.5 flex items-center gap-1.5 text-xs font-bold text-rose-600 animate-in fade-in slide-in-from-top-1 duration-150';
            errDiv.innerHTML = `
                <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>${pesan}</span>
            `;

            // Tempatkan pesan di bawah elemen atau pembungkus kolomnya
            if (el.parentElement) {
                if (el.parentElement.classList.contains('relative') && el.parentElement.parentElement) {
                    el.parentElement.parentElement.appendChild(errDiv);
                } else {
                    el.parentElement.appendChild(errDiv);
                }
            }

            // Langsung sorot / scroll mulus ke kolom tersebut ke posisi tengah layar
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Fokuskan kursor ke input target
            const toFocus = focusEl || (['INPUT', 'TEXTAREA', 'SELECT'].includes(el.tagName) ? el : el.querySelector('input, textarea, select'));
            if (toFocus && typeof toFocus.focus === 'function') {
                setTimeout(() => {
                    toFocus.focus();
                }, 200);
            }

            // Hapus otomatis tanda merah saat pengguna mulai mengisi
            const removeHandler = () => {
                el.classList.remove(
                    'sorot-error-active',
                    'border-rose-500',
                    'ring-4',
                    'ring-rose-500/25',
                    'bg-rose-50/20'
                );
                errDiv.remove();
                el.removeEventListener('input', removeHandler);
                el.removeEventListener('change', removeHandler);
                if (toFocus && toFocus !== el) {
                    toFocus.removeEventListener('input', removeHandler);
                    toFocus.removeEventListener('change', removeHandler);
                }
            };

            el.addEventListener('input', removeHandler);
            el.addEventListener('change', removeHandler);
            if (toFocus && toFocus !== el) {
                toFocus.addEventListener('input', removeHandler);
                toFocus.addEventListener('change', removeHandler);
            }
        }

        function closeModalKonfirmasiSimpan() {
            const modal = document.getElementById('modalKonfirmasiSimpan');
            if (modal) modal.classList.add('hidden');
        }

        function eksekusiSubmitTransaksi() {
            if (window.isSubmitting) return;
            window.isSubmitting = true;

            const btns = [
                document.getElementById('btnSimpanPaket'),
                document.getElementById('btnSubmitFinal')
            ];
            btns.forEach(b => {
                if (b) {
                    b.disabled = true;
                    b.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Menyimpan Paket...</span>
                    `;
                }
            });

            document.getElementById('formPaketIklan').submit();
        }

        function konfirmasiSimpanPaket() {
            if (window.isSubmitting) return false;

            const missingErrors = [];

            // 1. Validasi Nama Pemesan
            const manualInput = document.getElementById('inputManualNamaPemasang');
            const searchInput = document.getElementById('inputSearchPelanggan');
            let finalNama = (manualInput && manualInput.value.trim()) || (searchInput && searchInput.value.trim()) || '';

            if (!finalNama) {
                missingErrors.push({
                    name: 'Nama Pemesan',
                    id: 'inputManualNamaPemasang',
                    msg: 'Nama pemesan / instansi wajib diisi'
                });
            } else if (manualInput && !manualInput.value.trim()) {
                manualInput.value = finalNama;
            }

            // 2. Validasi Sales / AE
            const salesAeEl = document.getElementById('sales_ae_id');
            if (salesAeEl && !salesAeEl.value) {
                missingErrors.push({
                    name: 'Sales / AE',
                    id: 'sales_ae_id',
                    msg: 'Pilih Sales / AE yang menangani pesanan'
                });
            }

            // 3. Validasi Pilihan Saluran Media (Minimal 2 Media)
            const countActive = Object.values(activeMedia).filter(Boolean).length;
            if (countActive < 2) {
                missingErrors.push({
                    name: 'Saluran Media (Min. 2)',
                    id: 'containerPilihanMedia',
                    msg: 'Pilih minimal 2 saluran media untuk transaksi paket (misal: Cetak + Online)',
                    targetEl: document.getElementById('chkMediaCtk')
                });
            }

            // 4. Validasi Detail per Media Terpilih
            if (activeMedia.CTK) {
                const ctkId = document.getElementById('ctk_id_iklan')?.value || ctkSelectedId;
                if (!ctkId) {
                    missingErrors.push({
                        name: 'Produk Iklan Cetak',
                        id: 'sectionContainerCetak',
                        msg: 'Pilih salah satu produk iklan cetak terlebih dahulu'
                    });
                } else {
                    const ctkKat = document.getElementById('ctk_kategori_form')?.value || '';
                    if (ctkKat === 'BARIS') {
                        const barisInputs = Array.from(document.querySelectorAll('#containerBarisRowsCtk input'));
                        const filledBaris = barisInputs.filter(i => i.value.trim().length > 0);
                        if (filledBaris.length === 0) {
                            const emptyInput = barisInputs.find(i => !i.value.trim()) || document.getElementById('sectionIklanBarisCtk');
                            missingErrors.push({
                                name: 'Naskah Iklan Baris',
                                id: 'sectionIklanBarisCtk',
                                msg: 'Naskah iklan baris wajib diisi',
                                targetEl: emptyInput
                            });
                        }
                    } else {
                        const kol = parseInt(document.getElementById('in_ctk_kolom')?.value || 0, 10);
                        const mm = parseInt(document.getElementById('in_ctk_tinggi')?.value || 0, 10);
                        if (kol < 1 || kol > 7) {
                            missingErrors.push({
                                name: 'Lebar Kolom Cetak (1-7)',
                                id: 'in_ctk_kolom',
                                msg: 'Lebar kolom cetak wajib diisi (1 - 7 kolom)'
                            });
                        }
                        if (mm < 10 || mm > 540) {
                            missingErrors.push({
                                name: 'Tinggi Cetak (mm)',
                                id: 'in_ctk_tinggi',
                                msg: 'Tinggi cetak wajib diisi (10 - 540 mm)'
                            });
                        }
                    }
                }

                if (!ctkSelectedDates || ctkSelectedDates.length === 0) {
                    missingErrors.push({
                        name: 'Tanggal Terbit Cetak',
                        id: 'containerKalenderCtk',
                        msg: 'Pilih minimal 1 tanggal terbit cetak di kalender'
                    });
                }
            }

            if (activeMedia.ONL) {
                if (onlActiveTab === 'PORTAL_BERITA') {
                    if (!onlSelectedPortals || onlSelectedPortals.length === 0) {
                        missingErrors.push({
                            name: 'Portal Berita Online',
                            id: 'tabContentOnl_PORTAL_BERITA',
                            msg: 'Pilih minimal 1 portal berita online'
                        });
                    }
                } else {
                    const onlId = document.getElementById('onl_id_iklan')?.value;
                    if (!onlId && !onlSelectedMedsosTarif) {
                        missingErrors.push({
                            name: 'Format Medsos Online',
                            id: 'sectionContainerOnline',
                            msg: 'Pilih salah satu format medsos online'
                        });
                    }
                }

                if (!onlSelectedDates || onlSelectedDates.length === 0) {
                    missingErrors.push({
                        name: 'Tanggal Publikasi Online',
                        id: 'containerKalenderOnl',
                        msg: 'Pilih minimal 1 tanggal publikasi online di kalender'
                    });
                }
            }

            if (activeMedia.PTV) {
                const checkedTvPortals = Array.from(document.querySelectorAll('.tv-portal-checkbox:checked')).map(cb => cb.value);
                const selectedTvCount = (typeof selectedTvProducts !== 'undefined') ? Object.keys(selectedTvProducts).length : 0;
                if (checkedTvPortals.length === 0 && selectedTvCount === 0) {
                    missingErrors.push({
                        name: 'Penempatan / Format Priangan TV',
                        id: 'sectionContainerTv',
                        msg: 'Pilih minimal 1 portal web atau format video Priangan TV'
                    });
                }

                if (!ptvSelectedDates || ptvSelectedDates.length === 0) {
                    missingErrors.push({
                        name: 'Tanggal Siar Priangan TV',
                        id: 'containerKalenderTv',
                        msg: 'Pilih minimal 1 tanggal siar Priangan TV di kalender'
                    });
                }
            }

            // 5. Validasi Total Tagihan
            const totalVal = cleanNum(document.getElementById('strukTotalTagihan')?.innerText || '0');
            if (totalVal <= 0) {
                missingErrors.push({
                    name: 'Total Tagihan (> Rp 0)',
                    id: 'strukTotalTagihan',
                    msg: 'Total tagihan paket iklan harus lebih dari Rp 0'
                });
            }

            // 6. Validasi Pembayaran (Tunai vs Kredit)
            const skemaVal = document.getElementById('jenis_pembayaran')?.value || 'Tunai';
            const bayarEl = document.getElementById('jumlah_bayar');
            const bayarVal = cleanNum(bayarEl ? bayarEl.value : '0');

            if (bayarVal > totalVal) {
                missingErrors.push({
                    name: 'Jumlah Bayar (melebihi tagihan)',
                    id: 'jumlah_bayar',
                    msg: `Jumlah bayar (Rp ${formatRupiah(bayarVal)}) melebihi total tagihan (Rp ${formatRupiah(totalVal)})`
                });
            } else if (skemaVal === 'Tunai' && totalVal > 0 && bayarVal <= 0) {
                missingErrors.push({
                    name: 'Pembayaran Tunai (harus > Rp 0)',
                    id: 'jumlah_bayar',
                    msg: 'Untuk transaksi Tunai, nominal pembayaran wajib diisi lunas'
                });
            }

            // Jika ada field yang belum valid: Tampilkan POPUP RINGKAS & sorot kolom pertama
            if (missingErrors.length > 0) {
                const listText = missingErrors.map(item => item.name).join(', ');
                if (typeof window.AppAlert !== 'undefined' && typeof window.AppAlert.warning === 'function') {
                    window.AppAlert.warning({
                        title: 'Lengkapi Data Berikut',
                        message: `Mohon lengkapi data transaksi: ${listText}.`
                    });
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Lengkapi Data Berikut',
                        text: `Mohon lengkapi data transaksi: ${listText}.`,
                        confirmButtonColor: '#005792'
                    });
                }
                sorotKolomValidasi(missingErrors[0].id, missingErrors[0].msg, missingErrors[0].targetEl);
                return false;
            }

            // Semua lolos: bersihkan tanda merah
            bersihkanSorotValidasi();

            // 7. Konfirmasi Simpan dengan AppAlert SweetAlert2 Elegan Selaras 3 Transaksi
            const selectedMediaNames = [];
            if (activeMedia.CTK) selectedMediaNames.push('Cetak');
            if (activeMedia.ONL) selectedMediaNames.push('Online');
            if (activeMedia.PTV) selectedMediaNames.push('Priangan TV');
            const mediaSummary = selectedMediaNames.length > 0 
                ? 'Paket (' + selectedMediaNames.join(' + ') + ')'
                : 'Paket Iklan Multi-Media';

            const piutangVal = Math.max(0, totalVal - bayarVal);
            const statusBadgeStr = (skemaVal === 'Tunai') 
                ? (piutangVal <= 0 ? 'Lunas' : 'Kurang Bayar')
                : (bayarVal > 0 ? (piutangVal <= 0 ? 'Lunas' : 'DP / Parsial') : 'Piutang Belum Bayar');

            // Update data di modal HTML cadangan (selaras dengan 3 transaksi: koran, online, tv)
            const elPemesan = document.getElementById('konfirmasiNamaPemesan');
            if (elPemesan) elPemesan.textContent = finalNama;
            const elSkema = document.getElementById('konfirmasiSkemaBayar');
            if (elSkema) elSkema.textContent = skemaVal;
            const elTotal = document.getElementById('konfirmasiTotalBiaya');
            if (elTotal) elTotal.textContent = formatRupiah(totalVal);

            if (typeof window.AppAlert !== 'undefined' && typeof window.AppAlert.confirmSaveTransaction === 'function') {
                window.AppAlert.confirmSaveTransaction({
                    title: 'Simpan Transaksi Paket?',
                    message: 'Pastikan seluruh data pesanan paket iklan dan nominal pembayaran sudah benar:',
                    summary: {
                        pemasang: finalNama,
                        produk: mediaSummary,
                        skema: skemaVal,
                        total: formatRupiah(totalVal),
                        bayar: formatRupiah(bayarVal),
                        status: statusBadgeStr
                    },
                    confirmText: 'Ya, Simpan Transaksi',
                    cancelText: 'Periksa Kembali',
                    onConfirm: function () {
                        eksekusiSubmitTransaksi();
                    }
                });
            } else {
                const modal = document.getElementById('modalKonfirmasiSimpan');
                if (modal) {
                    modal.classList.remove('hidden');
                } else if (confirm(`Simpan transaksi paket iklan?\n\nPemesan: ${finalNama}\nSkema: ${skemaVal}\nTotal: ${formatRupiah(totalVal)}`)) {
                    eksekusiSubmitTransaksi();
                }
            }
        }

        // Close dropdown when click outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('containerSearchPelanggan');
            const dropdown = document.getElementById('dropdownHasilPelanggan');
            if (container && dropdown && !container.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Initialize on Load
        document.addEventListener('DOMContentLoaded', function() {
            // Check old input tanggal jika ada
            const oldCtk = "{{ old('ctk_tanggal_list', $defaultFirstDate) }}";
            if (oldCtk) {
                ctkSelectedDates = oldCtk.split(',').map(s => s.trim()).filter(Boolean);
            }
            if (ctkSelectedDates.length === 0) ctkSelectedDates = ['{{ $defaultFirstDate }}'];
            ctkActiveDetailDate = ctkSelectedDates[0] || '{{ $defaultFirstDate }}';

            const oldOnl = "{{ old('onl_tanggal_list', $defaultFirstDate) }}";
            if (oldOnl) {
                onlSelectedDates = oldOnl.split(',').map(s => s.trim()).filter(Boolean);
            }
            if (onlSelectedDates.length === 0) onlSelectedDates = ['{{ $defaultFirstDate }}'];
            onlActiveDetailDate = onlSelectedDates[0] || '{{ $defaultFirstDate }}';

            const oldPtv = "{{ old('ptv_tanggal_list', $defaultFirstDate) }}";
            if (oldPtv) {
                ptvSelectedDates = oldPtv.split(',').map(s => s.trim()).filter(Boolean);
            }
            if (ptvSelectedDates.length === 0) ptvSelectedDates = ['{{ $defaultFirstDate }}'];
            ptvActiveDetailDate = ptvSelectedDates[0] || '{{ $defaultFirstDate }}';

            // Sync Stepper Inputs
            const inQtyCtk = document.getElementById('in_qty_ctk');
            if (inQtyCtk) inQtyCtk.value = ctkSelectedDates.length;
            const inQtyOnl = document.getElementById('in_qty_onl');
            if (inQtyOnl) inQtyOnl.value = onlSelectedDates.length;
            const inQtyPtv = document.getElementById('in_qty_ptv');
            if (inQtyPtv) inQtyPtv.value = ptvSelectedDates.length;

            fetchKalenderBulan(ctkCalYear, ctkCalMonth + 1, () => {
                renderCalendarCtk();
                renderSelectedDatesChipsCtk();
                updatePanelInfoTanggalCtk(ctkActiveDetailDate);

                renderCalendarOnl();
                renderSelectedDatesChipsOnl();
                updatePanelInfoTanggalOnl(onlActiveDetailDate);

                renderCalendarTv();
                renderSelectedDatesChipsTv();
                updatePanelInfoTanggalTv(ptvActiveDetailDate);
            });

            handlePortalCheckboxChangeOnl();
            handleTvPortalCheckboxChange();
            handleDimensiCtkChange();
            hitungKalkulasiPaket();
        });
    </script>
</x-app-layout>
