<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        {{ isset($transaksi) ? 'Ubah Data & Jadwal Iklan Koran' : 'Entri Pemuatan Iklan' }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl font-normal">
                        {{ isset($transaksi) ? 'Formulir koreksi dan reschedule iklan cetak harian Kabar Priangan.' : 'Formulir pemesanan iklan cetak harian Kabar Priangan.' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0 flex-wrap sm:flex-nowrap">
                <div class="text-right hidden sm:block">
                    <div class="text-[11px] font-semibold text-slate-500">No. Faktur</div>
                    <div class="font-mono text-base font-bold text-kp-blue-700 leading-tight">{{ $nofakturkoran }}</div>
                </div>
                <a href="{{ isset($transaksi) ? route('transaksikoran.show', $transaksi->id) : route('transaksikoran.index') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                    &larr; <span>{{ isset($transaksi) ? 'Kembali' : 'Transaksi Koran' }}</span>
                </a>
            </div>
        </div>

        <!-- Notice Deadline & Cut-Off Cetak Harian (3 Status: Normal / Kuning / Merah) -->
        @php
            \Carbon\Carbon::setLocale('id');
            $jamCutoffStr = $configs['jam_cutoff'] ?? '14:00';
            $nowTime = \Carbon\Carbon::now('Asia/Jakarta');
            $cutoffToday = \Carbon\Carbon::today('Asia/Jakarta')->setTimeFromTimeString($jamCutoffStr);
            $warningToday = $cutoffToday->copy()->subHour(); // 1 jam sebelum batas cutoff (contoh: 13:00)

            if ($nowTime->greaterThanOrEqualTo($cutoffToday)) {
                $cutoffState = 'passed'; // Lewat batas cutoff (14:00 - 23:59) -> MERAH
            } elseif ($nowTime->greaterThanOrEqualTo($warningToday)) {
                $cutoffState = 'warning'; // 1 jam sebelum batas cutoff (13:00 - 14:00) -> KUNING / ORANGE
            } else {
                $cutoffState = 'normal'; // Jam biasa sebelum 1 jam cutoff (00:00 - 13:00) -> NORMAL PUTIH
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
                'passed'  => 'Lewat Batas Cetak: ' . $jamCutoffStr . ' WIB',
                'warning' => 'Mendekati Batas Cetak: ' . $jamCutoffStr . ' WIB',
                default   => 'Batas Cetak Hari Ini: ' . $jamCutoffStr . ' WIB',
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
                            Edisi Terdekat: <span class="{{ $bannerEdisiClass }}">{{ $edisiTerdekat }}</span>
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
                    Mohon periksa kembali formulir Anda:
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-2 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 3. Form Utama: Formulir Order & Pratinjau Langsung -->
        @php
            $initialDates = isset($transaksi) 
                ? ($transaksi->jadwalMuat->pluck('tanggal_muat')->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))->all() ?: [\Carbon\Carbon::parse($transaksi->tanggal_muatkoran)->format('Y-m-d')])
                : [];
            $initialDatesStr = !empty($initialDates) ? implode(',', $initialDates) : now()->addDay()->toDateString();
        @endphp
        <form id="formTransaksiKoran" method="POST" action="{{ isset($transaksi) ? route('transaksikoran.update', $transaksi->id) : route('transaksikoran.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($transaksi))
                @method('PUT')
            @endif

            <!-- Hidden Technical Fields -->
            <input type="hidden" name="nofakturkoran" value="{{ $nofakturkoran }}">
            <input type="hidden" name="tanggal_transaksikoran" value="{{ old('tanggal_transaksikoran', isset($transaksi) ? $transaksi->tanggal_transaksikoran?->format('Y-m-d') : now()->toDateString()) }}">
            <input type="hidden" name="ppn_persen" id="ppn_persen" value="{{ old('ppn_persen', isset($transaksi) ? $transaksi->ppn_persen : $configs['ppn_persen']) }}">
            <input type="hidden" name="kategori_form" id="kategori_form" value="{{ old('kategori_form', isset($transaksi) ? $transaksi->kategori_form : 'BARIS') }}">
            <input type="hidden" name="warna_iklan" id="warna_iklan" value="{{ old('warna_iklan', isset($transaksi) ? $transaksi->warna_iklan : 'BW') }}">
            <input type="hidden" name="id_iklankoran" id="id_iklankoran" value="{{ old('id_iklankoran', isset($transaksi) ? $transaksi->id_iklankoran : '') }}">
            <input type="hidden" name="tanggal_muat_list" id="tanggal_muat_list" value="{{ old('tanggal_muat_list', $initialDatesStr) }}">
            <input type="hidden" name="tanggal_muatkoran" id="tanggal_muatkoran" value="{{ old('tanggal_muatkoran', isset($transaksi) ? $transaksi->tanggal_muatkoran?->format('Y-m-d') : now()->addDay()->toDateString()) }}">
            <input type="hidden" name="harga_transaksikoran" id="harga_transaksikoran" value="{{ old('harga_transaksikoran', isset($transaksi) ? $transaksi->harga_transaksikoran : 0) }}">
            <input type="hidden" name="ukuran_iklan" id="ukuran_iklan" value="{{ old('ukuran_iklan', isset($transaksi) ? $transaksi->ukuran_iklan : '') }}">
            <input type="hidden" name="jenis_pembayaran" id="jenis_pembayaran" value="{{ old('jenis_pembayaran', isset($transaksi) ? $transaksi->jenis_pembayaran : 'Tunai') }}">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- ============================================================== -->
                <!-- PANEL KIRI: INPUT DATA TRANSAKSI                               -->
                <!-- ============================================================== -->
                <div class="lg:col-span-7 space-y-5">

                    <!-- SEKSI 1: CARA BAYAR & DATA PEMASANG -->
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

                            @php
                                $currentJenisBayar = old('jenis_pembayaran', isset($transaksi) ? $transaksi->jenis_pembayaran : 'Tunai');
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

                        <!-- Baris Input Pemasang: 2 Kolom Sejajar (Pilih dari Data Pelanggan vs Input Nama Langsung) -->
                        <div class="space-y-3 pt-3 border-t border-slate-100">
                            @php
                                $selectedOldPelanggan = old('pelanggan_id') 
                                    ? $pelanggans->firstWhere('id', old('pelanggan_id')) 
                                    : (isset($transaksi) ? ($transaksi->pelanggan ?? null) : null);
                                $oldNamaPemasang = old('nama_pemasangkoran', $selectedOldPelanggan->nama_instansi ?? (isset($transaksi) ? $transaksi->nama_pemasangkoran : ''));
                            @endphp

                            <!-- Hidden Form Fields -->
                            <input type="hidden" name="pelanggan_id" id="pelanggan_id" value="{{ old('pelanggan_id', $selectedOldPelanggan->id ?? (isset($transaksi) ? $transaksi->pelanggan_id : '')) }}">
                            <input type="hidden" name="nama_pemasangkoran" id="nama_pemasangkoran" value="{{ $oldNamaPemasang }}">

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
                                                <span id="labelPiutangPelanggan" class="font-semibold text-rose-600 {{ ($selectedOldPelanggan && ($selectedOldPelanggan->total_piutang_koran ?? 0) > 0) ? '' : 'hidden' }}">
                                                    Piutang: Rp {{ number_format($selectedOldPelanggan->total_piutang_koran ?? 0, 0, ',', '.') }}
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
                                                <span class="text-xs text-slate-500">Pelanggan baru?</span>
                                                <button type="button" onclick="openModalPelangganBaru()" class="flex items-center gap-1.5 text-xs font-semibold text-kp-blue-600 hover:underline cursor-pointer">
                                                    <svg class="w-3 h-3 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                    </svg>    
                                                    <span class="text-xs font-semibold text-kp-blue-600 hover:underline cursor-pointer">Pelanggan Baru</span>
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

                        <!-- Baris: No. Telepon / WA & Nama PIC (2 Kolom Berdampingan Lega seperti Tunai & Kredit) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="text-sm font-semibold text-slate-700 mb-1.5 block">
                                    No. Telepon / WA
                                </label>
                                <div class="relative flex items-center">
                                    <input type="text" name="no_telp" id="no_telp"
                                           placeholder="08xxxxxxxxxx"
                                           value="{{ old('no_telp', isset($transaksi) ? ($transaksi->no_telp ?? $selectedOldPelanggan?->no_telp) : '') }}"
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
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>

                        <!-- Baris: Alamat Lengkap (Paling Bawah, Lebar Penuh & Lega) -->
                        <div class="pt-1">
                            <label class="text-sm font-semibold text-slate-700 mb-1.5 block">
                                Alamat
                            </label>
                            <textarea name="alamat_pemasangkoran" id="alamat_pemasangkoran" rows="2"
                                      placeholder="Alamat lengkap pemesan..."
                                      class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs resize-none">{{ old('alamat_pemasangkoran', isset($transaksi) ? ($transaksi->alamat_pemasangkoran ?? $selectedOldPelanggan?->alamat) : '') }}</textarea>
                        </div>

                        <!-- Sales & Komisioner (Clean, Simple, Elegan) -->
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
                                            : ($salesAes->firstWhere('jenis_kode', 'FO')->id ?? $salesAes->first()->id ?? ''));
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
                                                    default => $ae->jenis_kode
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
                                    <input type="hidden" name="sales_iklankoran" id="sales_iklankoran" value="{{ old('sales_iklankoran', isset($transaksi) ? $transaksi->sales_iklankoran : $initialSalesName) }}">

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
                                                <input type="text" name="insentif_transaksikoran" id="insentif_transaksikoran" 
                                                       value="{{ old('insentif_transaksikoran', isset($transaksi) && $transaksi->insentif_transaksikoran ? number_format($transaksi->insentif_transaksikoran, 0, ',', '.') : 0) }}"
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
                                                <span class="absolute right-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">%</span>
                                            </div>
                                            <div class="col-span-3 relative">
                                                <span class="absolute left-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">Rp</span>
                                                <input type="text" name="komisi_transaksikoran" id="komisi_transaksikoran" 
                                                       value="{{ old('komisi_transaksikoran', isset($transaksi) && $transaksi->komisi_transaksikoran ? number_format($transaksi->komisi_transaksikoran, 0, ',', '.') : 0) }}"
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

                    <!-- SEKSI 2: JENIS IKLAN -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">2</span>
                                <span>Jenis Iklan</span>
                            </span>
                        </div>

                        @php
                            // Kelompokkan produk berdasarkan tipe_dasar (5 Kategori Resmi: BARIS, KOLOM, DISPLAY, ADVERTORIAL, BONUS)
                            $normalizedIklan = $iklankoran->map(function($p) {
                                if ($p->tipe_dasar === 'KHUSUS') {
                                    $p->tipe_dasar = 'DISPLAY';
                                }
                                return $p;
                            });
                            $groupedProducts = $normalizedIklan->groupBy('tipe_dasar');
                            $tabOrder = ['BARIS', 'KOLOM', 'DISPLAY', 'ADVERTORIAL', 'BONUS'];
                            $defaultTab = old('kategori_form', isset($transaksi) ? $transaksi->kategori_form : 'BARIS');
                            if ($defaultTab === 'KHUSUS') $defaultTab = 'DISPLAY';
                            if (!$groupedProducts->has($defaultTab) && $groupedProducts->isNotEmpty()) {
                                $defaultTab = $groupedProducts->keys()->first();
                            }
                            $targetProductId = old('id_iklankoran', isset($transaksi) ? $transaksi->id_iklankoran : null);
                            $initialProduct = $targetProductId ? $normalizedIklan->firstWhere('id', $targetProductId) : null;
                        @endphp

                        <!-- Segmented Tab Controls (5 Kategori: 4 Komersial + 1 Non-Komersial/Layanan Masyarakat) -->
                        <div class="flex flex-wrap gap-2 p-1.5 bg-slate-100 rounded-2xl border border-slate-200/80">
                            @foreach($tabOrder as $tabKey)
                                @if($groupedProducts->has($tabKey))
                                    @php
                                        $countItems = $groupedProducts[$tabKey]->count();
                                        $tabLabel = match($tabKey) {
                                            'BARIS'       => 'Iklan Baris',
                                            'KOLOM'       => 'Iklan Kolom',
                                            'DISPLAY'     => 'Iklan Display',
                                            'ADVERTORIAL' => 'Iklan Advertorial',
                                            'BONUS'       => 'Layanan Masyarakat / Bonus',
                                            default       => $tabKey
                                        };
                                        $isTabActive = ($defaultTab === $tabKey);
                                    @endphp
                                    <button type="button" onclick="switchProductTab('{{ $tabKey }}')"
                                            id="tabBtn_{{ $tabKey }}"
                                            class="tab-product-btn px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 {{ $isTabActive ? 'bg-kp-blue-600 text-white font-semibold shadow-xs border border-kp-blue-600' : 'bg-white text-slate-700 font-medium border border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                                        <span>{{ $tabLabel }}</span>
                                        <span class="px-1.5 py-0.5 rounded-full text-xs font-semibold {{ $isTabActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $countItems }}
                                        </span>
                                    </button>
                                @endif
                            @endforeach
                        </div>

                        <!-- Tab Panels: Menampilkan Kartu Produk Sesuai Kategori -->
                        @foreach($tabOrder as $tabKey)
                            @if($groupedProducts->has($tabKey))
                                @php
                                    $items = $groupedProducts[$tabKey];
                                    $isTabActive = ($defaultTab === $tabKey);
                                @endphp
                                <div id="tabContent_{{ $tabKey }}" class="tab-product-content {{ $isTabActive ? '' : 'hidden' }}">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                        @foreach($items as $item)
                                            @php
                                                $isSelected = ($initialProduct && $initialProduct->id == $item->id);
                                                $badgeColor = match($item->mode_warna) {
                                                    'FC' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                    default => 'bg-slate-100 text-slate-700 border-slate-200'
                                                };
                                            @endphp
                                            <button type="button" 
                                                    onclick="pilihProdukIklan({{ $item->id }}, '{{ $item->kode_jenis }}', '{{ $item->tipe_dasar }}', '{{ $item->mode_warna }}', '{{ $item->cara_hitung }}', {{ $item->tarif_dasar }}, {{ $item->is_fixed_size ? 'true' : 'false' }}, {{ $item->default_kolom ?? 1 }}, {{ $item->default_tinggi_mm ?? 50 }}, {{ $item->min_order ?? 2 }})"
                                                    id="cardProduk_{{ $item->id }}"
                                                    data-id="{{ $item->id }}"
                                                    data-kode="{{ $item->kode_jenis }}"
                                                    data-tab="{{ $tabKey }}"
                                                    data-nama="{{ $item->nama_jenis }}"
                                                    data-kol="{{ $item->default_kolom ?? 1 }}"
                                                    data-mm="{{ $item->default_tinggi_mm ?? 50 }}"
                                                    data-fixed="{{ $item->is_fixed_size ? 'true' : 'false' }}"
                                                    data-warna="{{ $item->mode_warna }}"
                                                    data-tipe="{{ $item->tipe_dasar }}"
                                                    data-tarif="{{ $item->tarif_dasar }}"
                                                    data-hitung="{{ $item->cara_hitung }}"
                                                    class="produk-card text-left p-3 rounded-xl border transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2 {{ $isSelected ? 'border-2 border-kp-blue-600 bg-kp-blue-50/40 ring-1 ring-kp-blue-500/20 shadow-xs' : 'border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50' }}">
                                                
                                                <!-- Badge Ceklist Lancip Segitiga Sudut Kiri Atas (Foto 2 Style) -->
                                                <span class="badge-corner-check absolute top-0 left-0 w-7 h-7 pointer-events-none z-10 {{ $isSelected ? '' : 'hidden' }}">
                                                    <svg class="w-7 h-7" viewBox="0 0 28 28">
                                                        <path d="M0 0 L28 0 L0 28 Z" fill="#0A72AC" />
                                                        <path d="M4 9 L8 13 L14 6" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>

                                                <div class="text-sm font-bold text-slate-900 leading-snug line-clamp-2">
                                                    {{ $item->nama_jenis }}
                                                </div>
                                                <div class="text-xs font-bold text-kp-blue-700 font-mono">
                                                    @if($item->cara_hitung === 'PER_BARIS')
                                                        Rp {{ number_format($item->tarif_dasar, 0, ',', '.') }} / baris
                                                    @elseif($item->cara_hitung === 'PER_MMK')
                                                        Rp {{ number_format($item->tarif_dasar, 0, ',', '.') }} / mmk
                                                    @elseif($item->cara_hitung === 'FIXED_ZERO' || $item->tarif_dasar == 0)
                                                        Rp 0 (Layanan Masyarakat)
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

                        <!-- Baris Penempatan: Halaman & Rubrik -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                            <div>
                                <label class="text-sm font-semibold text-slate-700 mb-1.5 block">
                                    Penempatan Halaman <span class="text-rose-500">*</span>
                                </label>
                                <select name="kop_kota_id" id="kop_kota_id" onchange="handleKopKotaChange(this)"
                                        class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                    @foreach($kopKotas as $kota)
                                        <option value="{{ $kota->id }}" 
                                                data-halaman="{{ $kota->nomor_halaman }}"
                                                data-nama="{{ $kota->nama_penempatan }}"
                                                data-surcharge="{{ $kota->surcharge_persen }}"
                                                {{ (old('kop_kota_id', isset($transaksi) ? $transaksi->kop_kota_id : null) == $kota->id || (!old('kop_kota_id') && !isset($transaksi) && ($kota->kode === 'HAL1' || $loop->first))) ? 'selected' : '' }}>
                                            {{ $kota->nama_penempatan }} @if($kota->surcharge_persen > 0) (+{{ number_format($kota->surcharge_persen, 2) }}%) @endif
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="halaman_iklan" id="halaman_iklan" value="{{ old('halaman_iklan', isset($transaksi) ? $transaksi->halaman_iklan : ($kopKotas->first()?->nama_penempatan ?? 'Halaman 1 (Halaman Depan / Cover)')) }}">
                            </div>

                            <!-- Rubrik Baris -->
                            <div id="containerKopIklan">
                                <label class="text-sm font-semibold text-slate-700 mb-1.5 block">
                                    Rubrik Iklan
                                </label>
                                <select name="kop_iklan_id" id="kop_iklan_id"
                                        class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                    @foreach($kopIklans as $kop)
                                        <option value="{{ $kop->id }}" {{ (old('kop_iklan_id', isset($transaksi) ? $transaksi->kop_iklan_id : null) == $kop->id || (!old('kop_iklan_id') && !isset($transaksi) && $kop->kode === 'HILANG')) ? 'selected' : '' }}>
                                            {{ $kop->nama_kop }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SEKSI 3: UKURAN & NASKAH IKLAN -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">3</span>
                                <span>Ukuran &amp; Naskah</span>
                            </span>
                        </div>

                        <!-- PLACEHOLDER SEBELUM PILIH PRODUK -->
                        <div id="placeholderPilihProduk" class="p-6 text-center rounded-xl border border-dashed border-slate-300 bg-slate-50/80 {{ $initialProduct ? 'hidden' : '' }}">
                            <p class="font-semibold text-slate-700 text-sm">Pilih Jenis Iklan Terlebih Dahulu</p>
                            <p class="text-xs text-slate-400 mt-1">Form spesifikasi ukuran dan materi akan tampil otomatis setelah jenis iklan dipilih pada Seksi 2 di atas.</p>
                        </div>

                        <!-- KONTEN KHUSUS IKLAN BARIS -->
                        <div id="sectionIklanBaris" class="space-y-4 {{ ($initialProduct && $initialProduct->tipe_dasar === 'BARIS') ? '' : 'hidden' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <label class="text-sm font-semibold text-slate-800">
                                        Naskah Iklan Baris <span class="text-rose-500">*</span>
                                    </label>
                                    <span class="text-xs text-slate-500 bg-slate-100 px-2 py-0.5 rounded font-medium">Maks. 32 karakter/baris</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-kp-blue-50 text-kp-blue-800 border border-kp-blue-200">
                                        <span id="labelJumlahBaris" class="text-sm font-bold">{{ old('jumlah_baris', isset($transaksi) ? ($transaksi->jumlah_baris ?: 2) : 2) }}</span> Baris
                                    </span>
                                </div>
                            </div>

                            <!-- Dynamic Numbered Row Inputs Container -->
                            <div id="containerBarisRows" class="space-y-2">
                                <!-- Row 1 sampai N akan digenerate secara interaktif via JS -->
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                                <button type="button" id="btnTambahBaris" onclick="tambahBarisInput()"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>Tambah Baris</span>
                                </button>
                                <span class="text-xs text-slate-500 font-medium" id="labelTarifBaris">
                                    Tarif: Rp {{ number_format(($initialProduct && ($initialProduct->cara_hitung === 'PER_BARIS' || $initialProduct->tipe_dasar === 'BARIS')) ? $initialProduct->tarif_dasar : $configs['tarif_per_baris'], 0, ',', '.') }}/baris
                                </span>
                            </div>

                            <!-- Live Visual Box Print Preview Iklan Baris -->
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                                <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        Pratinjau Baris:
                                    </span>
                                    <span id="previewBarisCharTotal" class="font-mono text-xs text-slate-500">0 Karakter</span>
                                </div>
                                <div id="previewBoxBaris" class="bg-white p-2.5 rounded-lg border border-slate-200 font-mono text-xs text-slate-900 leading-snug divide-y divide-slate-100 shadow-2xs">
                                    <div class="text-slate-400 italic text-xs py-1 text-center">Naskah iklan baris akan tampil di sini...</div>
                                </div>
                            </div>

                            <input type="hidden" name="naskah_iklan_baris" id="naskah_iklan_baris" value="{{ old('naskah_iklan_baris', isset($transaksi) ? ($transaksi->naskah_iklan_baris ?? $transaksi->keterangan_materi) : '') }}">
                            <input type="hidden" name="jumlah_baris" id="jumlah_baris" value="{{ old('jumlah_baris', isset($transaksi) ? ($transaksi->jumlah_baris ?: 2) : 2) }}">
                        </div>

                        <!-- KONTEN KHUSUS KOLOM / DISPLAY / ADVERTORIAL -->
                        <div id="sectionIklanDisplay" class="space-y-3 hidden">
                            <!-- SUB-PANEL 1: RINGKASAN SPESIFIKASI PAKET (MINIMALIS & ELEGAN) -->
                            <div id="panelUkuranFixed" class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="text-sm font-bold text-slate-900 truncate" id="fixedAdPackageName">Iklan Kolom BW (1 Kolom × 50 mm)</div>
                                        <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                            <span id="fixedAdDimensiRingkas" class="text-xs font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200/70">1 Kolom × 50 mm (50 mmk)</span>
                                            <span id="fixedAdColorBadge" class="text-xs font-semibold px-2 py-0.5 rounded-md border bg-slate-50 text-slate-600 border-slate-200">Hitam Putih (BW)</span>
                                        </div>
                                    </div>

                                    <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-white hover:border-slate-300 transition cursor-pointer select-none shrink-0 self-start sm:self-auto shadow-2xs font-medium">
                                        <input type="checkbox" id="checkUkuranManual" onchange="toggleUkuranManual(this.checked)"
                                               class="w-4 h-4 text-kp-blue-600 rounded border-slate-300 focus:ring-kp-blue-500 cursor-pointer">
                                        <span class="text-xs text-slate-700">Ubah Ukuran Manual</span>
                                    </label>
                                </div>

                                <!-- Elemen tersembunyi untuk kompatibilitas skrip JS -->
                                <span id="fixedAdKolomLabel" class="hidden"></span>
                                <span id="fixedAdTinggiLabel" class="hidden"></span>
                                <span id="fixedAdTotalMmkLabel" class="hidden"></span>
                            </div>

                            <!-- SUB-PANEL 2: PENGATURAN UKURAN KUSTOM MANUAL (LEBAR, TINGGI, TOTAL LUAS) -->
                            <div id="panelUkuranCustom" class="hidden p-3.5 bg-slate-50/70 rounded-xl border border-slate-200 shadow-2xs">
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                                    <div class="sm:col-span-4">
                                        <label for="lebar_kolom" class="text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                            <span>Lebar Kolom <span class="text-rose-500">*</span></span>
                                            <span class="text-[11px] font-normal text-slate-400">1 – 7 kol</span>
                                        </label>
                                        <div class="relative flex items-center">
                                            <input type="number" name="lebar_kolom" id="lebar_kolom" min="1" max="7"
                                                   value="{{ old('lebar_kolom', isset($transaksi) ? ($transaksi->kolom_iklan ?: 2) : 2) }}" oninput="handleDimensiChange()"
                                                   class="w-full h-10 pl-3 pr-14 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                            <span class="absolute right-3 text-xs text-slate-400 font-medium pointer-events-none">kolom</span>
                                        </div>
                                    </div>

                                    <div class="sm:col-span-4">
                                        <label for="tinggi_mm" class="text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                            <span>Tinggi (mm) <span class="text-rose-500">*</span></span>
                                            <span class="text-[11px] font-normal text-slate-400">10 – 540 mm</span>
                                        </label>
                                        <div class="relative flex items-center">
                                            <input type="number" name="tinggi_mm" id="tinggi_mm" min="10" max="540" step="5"
                                                   value="{{ old('tinggi_mm', isset($transaksi) ? ($transaksi->tinggi_iklan ?: 100) : 100) }}" oninput="handleDimensiChange()"
                                                   class="w-full h-10 pl-3 pr-10 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                            <span class="absolute right-3 text-xs text-slate-400 font-medium pointer-events-none">mm</span>
                                        </div>
                                    </div>

                                    <div class="sm:col-span-4">
                                        <label class="text-xs font-bold text-slate-700 mb-1.5 block">
                                            Total Luas
                                        </label>
                                        <div class="h-10 px-3.5 bg-kp-blue-50/70 border border-kp-blue-200/90 rounded-xl flex items-center justify-between shadow-2xs">
                                            <span class="text-xs font-medium text-kp-blue-800">Luas Cetak:</span>
                                            <span id="labelTotalMmk" class="text-xs sm:text-sm font-bold font-mono text-kp-blue-900">200 mmk</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Materi File -->
                            <div class="pt-1">
                                <label class="text-sm font-semibold text-slate-700 mb-1.5 block">
                                    File Desain
                                </label>
                                <div class="border-2 border-dashed border-slate-300 hover:border-kp-blue-500 rounded-xl p-4 text-center bg-slate-50/50 hover:bg-kp-blue-50/20 transition cursor-pointer relative">
                                    <input type="file" name="file_materi" id="file_materi" accept=".pdf,.jpg,.jpeg,.png,.webp,.tiff"
                                           onchange="handleFileUpload(this)"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <div class="flex flex-col items-center justify-center gap-1 pointer-events-none">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <div id="fileMateriLabel" class="text-xs text-slate-600 font-medium">
                                            @if(isset($transaksi) && $transaksi->file_materi)
                                                <span class="font-semibold text-emerald-700">File saat ini: {{ basename($transaksi->file_materi) }}</span> (Unggah baru jika ingin mengganti)
                                            @else
                                                <span class="font-semibold text-kp-blue-600">Pilih file</span> atau tarik file ke sini
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-400">PDF, JPG, PNG, TIFF (Maks. 15MB)</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan Tambahan -->
                        <div>
                            <label class="text-sm font-semibold text-slate-700 mb-1.5 block">Catatan (Opsional)</label>
                            <input type="text" name="keterangan_materi" id="keterangan_materi"
                                   placeholder="Catatan khusus untuk cetak (opsional)..."
                                   value="{{ old('keterangan_materi', isset($transaksi) ? $transaksi->keterangan_materi : '') }}"
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <!-- SEKSI 4: JADWAL TAYANG FLEKSIBEL -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-kp-blue-600 text-white flex items-center justify-center text-xs font-bold">4</span>
                                <span>Jadwal Terbit</span>
                            </span>
                            <div id="statusParitasTanggal" class="text-xs font-semibold px-2.5 py-1 rounded-lg border bg-emerald-50 text-emerald-800 border-emerald-200 inline-flex items-center gap-1.5 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>1 Tanggal Dipilih</span>
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
                                            <button type="button" onclick="stepJumlahMuat(-1)"
                                                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm transition cursor-pointer select-none">
                                                −
                                            </button>
                                            <input type="text" inputmode="numeric" name="total_muatkoran" id="total_muatkoran"
                                                   value="{{ old('total_muatkoran', isset($transaksi) ? ($transaksi->total_muatkoran ?: 1) : 1) }}"
                                                   oninput="handleJumlahMuatInput(this.value)"
                                                   onblur="handleJumlahMuatBlur(this)"
                                                   autocomplete="off"
                                                   class="w-14 h-8 px-1 text-center font-bold text-sm text-slate-900 border-0 focus:ring-0 focus:outline-none">
                                            <button type="button" onclick="stepJumlahMuat(1)"
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
                                            <span id="labelTotalDatesCount" class="px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-100 text-kp-blue-800 whitespace-nowrap">1 Tanggal</span>
                                        </div>
                                        <button type="button" onclick="resetSemuaTanggal()"
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

                                    <!-- Legenda Simpel Ringkas (Seragam Persegi / Kotak) -->
                                    <div class="flex items-center justify-between text-xs text-slate-500 border-t border-slate-100 pt-2 px-0.5">
                                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-600 inline-block shadow-2xs"></span> Tanggal Terbit</span>
                                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-rose-50 border border-rose-300 inline-block"></span> Libur / Minggu</span>
                                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-amber-50 border border-amber-300 inline-block"></span> Hari Peringatan</span>
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
                <!-- PANEL KANAN: SIMULASI & RINCIAN BIAYA                          -->
                <!-- ============================================================== -->
                <div class="lg:col-span-5 space-y-5 lg:sticky lg:top-6">

                    <!-- 1. SIMULASI HALAMAN KORAN REALISTIS (HU KABAR PRIANGAN) -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">

                        <!-- Header Simulasi: Bersih, Minimalis & Profesional -->
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 gap-2">
                            <h3 class="text-sm font-bold text-slate-900 tracking-tight whitespace-nowrap shrink-0">Simulasi Cetak Koran</h3>
                            <span id="simulatorHalamanBadge" class="text-[10.5px] font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 font-mono shadow-2xs whitespace-nowrap shrink-0">
                                Hal. 1 • Cover / Utama
                            </span>
                        </div>

                        <!-- Bar Kontrol Studio: Modern Segmented Alignment Dock -->
                        <div class="flex flex-wrap items-center justify-between gap-2.5 bg-slate-50/80 p-2 sm:p-2.5 rounded-xl border border-slate-200">
                            <div class="flex items-center gap-2">
                                <!-- Segmented Alignment Dock -->
                                <div class="flex items-center gap-1 p-1 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
                                    <!-- Perataan Vertikal: Atas, Tengah, Bawah -->
                                    <div class="flex items-center gap-0.5">
                                        <button type="button" onclick="setSlotVertical('top')" id="btnV_top"
                                                title="Rata Atas (Top Align)"
                                                class="excel-v-btn w-7 h-7 rounded-lg flex items-center justify-center transition cursor-pointer text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-transparent">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="4" y1="4" x2="20" y2="4"></line>
                                                <rect x="7" y="8" width="10" height="12" rx="1.5"></rect>
                                            </svg>
                                        </button>
                                        <button type="button" onclick="setSlotVertical('middle')" id="btnV_middle"
                                                title="Rata Tengah Vertikal (Middle Align)"
                                                class="excel-v-btn w-7 h-7 rounded-lg flex items-center justify-center transition cursor-pointer text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-transparent">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="4" y1="12" x2="20" y2="12"></line>
                                                <rect x="7" y="6" width="10" height="12" rx="1.5"></rect>
                                            </svg>
                                        </button>
                                        <button type="button" onclick="setSlotVertical('bottom')" id="btnV_bottom"
                                                title="Rata Bawah (Bottom Align)"
                                                class="excel-v-btn w-7 h-7 rounded-lg flex items-center justify-center transition cursor-pointer bg-kp-blue-50 text-kp-blue-700 font-bold border border-kp-blue-300 shadow-2xs">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="4" y1="20" x2="20" y2="20"></line>
                                                <rect x="7" y="4" width="10" height="12" rx="1.5"></rect>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="h-5 w-px bg-slate-200 mx-0.5"></div>

                                    <!-- Perataan Horizontal: Kiri, Tengah, Kanan -->
                                    <div class="flex items-center gap-0.5">
                                        <button type="button" onclick="setSlotHorizontal('left')" id="btnH_left"
                                                title="Rata Kiri (Align Left)"
                                                class="excel-h-btn w-7 h-7 rounded-lg flex items-center justify-center transition cursor-pointer text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-transparent">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="4" y1="4" x2="4" y2="20"></line>
                                                <rect x="8" y="7" width="12" height="10" rx="1.5"></rect>
                                            </svg>
                                        </button>
                                        <button type="button" onclick="setSlotHorizontal('center')" id="btnH_center"
                                                title="Rata Tengah Horizontal (Center)"
                                                class="excel-h-btn w-7 h-7 rounded-lg flex items-center justify-center transition cursor-pointer text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-transparent">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="12" y1="4" x2="12" y2="20"></line>
                                                <rect x="6" y="7" width="12" height="10" rx="1.5"></rect>
                                            </svg>
                                        </button>
                                        <button type="button" onclick="setSlotHorizontal('right')" id="btnH_right"
                                                title="Rata Kanan (Align Right)"
                                                class="excel-h-btn w-7 h-7 rounded-lg flex items-center justify-center transition cursor-pointer bg-kp-blue-50 text-kp-blue-700 font-bold border border-kp-blue-300 shadow-2xs">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="20" y1="4" x2="20" y2="20"></line>
                                                <rect x="4" y="7" width="12" height="10" rx="1.5"></rect>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="h-6 w-px bg-slate-300 mx-0.5"></div>

                                <!-- Tombol Khusus Kuping (Header Utama) -->
                                <button type="button" onclick="setSlotKuping()" id="btn_kuping"
                                        class="h-9 px-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition flex items-center gap-1.5 shadow-2xs cursor-pointer text-center text-xs font-semibold"
                                        title="Posisi Khusus: Kuping Header Koran">
                                    <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span>Kuping Header</span>
                                </button>
                            </div>

                            <!-- Tools Tampilan: Garis Kolom & Perbesar -->
                            <div class="flex items-center gap-1.5 shrink-0 ml-auto">
                                <button type="button" onclick="toggleSimulatorGrid()" id="btnSimulatorGrid"
                                        class="h-8 px-2.5 text-xs font-semibold rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition flex items-center gap-1 shadow-2xs cursor-pointer"
                                        title="Tampilkan Garis Kolom">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M9 4v16m6-16v16"/></svg>
                                    <span>Garis Kolom</span>
                                </button>
                                <button type="button" onclick="toggleSimulatorZoom()" id="btnSimulatorZoom"
                                        class="h-8 px-2.5 text-xs font-semibold rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition flex items-center gap-1 shadow-2xs cursor-pointer"
                                        title="Perbesar Lembar Koran">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                    <span id="labelSimulatorZoom">Perbesar</span>
                                </button>
                            </div>
                        </div>

                        <!-- MAIN NEWSPAPER CANVAS CONTAINER (SCROLLABLE & ZOOMABLE) -->
                        <div id="newspaperViewport" 
                             class="relative bg-slate-200/80 rounded-2xl p-2.5 sm:p-3 border border-slate-300 overflow-x-hidden overflow-y-auto max-h-[500px] transition-all cursor-default [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden"
                             style="scrollbar-width: none; -ms-overflow-style: none;">
                            
                            <!-- Lembar Koran Broadsheet Autentik (HU Kabar Priangan) -->
                            <div id="broadsheetPaper" 
                                 class="mx-auto bg-[#faf8f4] border border-stone-300 shadow-xl ring-1 ring-black/5 rounded-xs p-2 sm:p-2.5 transition-transform duration-300 origin-top flex flex-col justify-between select-none relative cursor-default"
                                 style="width: 100%; max-width: 420px; min-height: 440px; font-family: 'Times New Roman', Times, Georgia, serif;">
                                
                                <!-- Horizontal Broadsheet Fold Line (Lipatan Tengah 270 mm) -->
                                <div class="absolute inset-x-0 top-1/2 h-2 -translate-y-1/2 bg-gradient-to-b from-stone-400/15 via-black/8 to-transparent pointer-events-none z-20"></div>

                                <!-- Running Page Folio / Header -->
                                <div id="newspaperHeaderContainer" class="border-b-2 border-black pb-1 mb-1">
                                    <div class="flex items-center justify-between text-[6.5px] border-b border-black pb-0.5 mb-0.5 font-serif">
                                        <span class="font-bold italic">HU. Kabar Priangan</span>
                                        <span id="simulatorTanggalHeader">{{ now()->addDay()->locale('id')->translatedFormat('d F Y') }}</span>
                                        <span id="simulatorHalMini" class="font-mono font-bold">HALAMAN 1</span>
                                    </div>
                                    <div class="text-[9.5px] font-black tracking-widest text-center text-stone-900 uppercase font-serif">
                                        HARIAN UMUM KABAR PRIANGAN
                                    </div>
                                </div>

                                <!-- Body Lembar Broadsheet (Ruler + 7 Kolom Canvas) -->
                                <div class="flex items-stretch gap-1 flex-1 relative min-h-[350px]">
                                    
                                    <!-- Penggaris Sisi Kiri (0 - 540 mm) -->
                                    <div class="w-5 bg-stone-900 text-stone-300 rounded-xs py-1 flex flex-col justify-between text-[6px] font-mono text-center select-none shrink-0 border-r border-stone-700 shadow-xs z-10">
                                        <span class="text-amber-300 font-bold">0</span>
                                        <span class="opacity-50">50</span>
                                        <span class="opacity-80">100</span>
                                        <span class="opacity-50">150</span>
                                        <span class="opacity-80">200</span>
                                        <span class="text-amber-300 font-bold border-y border-amber-400/40 py-0.5">270</span>
                                        <span class="opacity-50">350</span>
                                        <span class="opacity-80">400</span>
                                        <span class="opacity-50">480</span>
                                        <span class="text-amber-300 font-bold">540</span>
                                    </div>

                                    <!-- 7 Columns Newspaper Page Canvas -->
                                    <div id="koranGridContainer" class="flex-1 relative flex flex-col justify-between overflow-hidden bg-transparent">
                                        
                                        <!-- Panduan Garis 7 Kolom (Toggleable) -->
                                        <div id="colGuidesOverlay" class="absolute inset-0 grid grid-cols-7 gap-0 pointer-events-none z-10 opacity-25">
                                            <div class="border-r border-dashed border-stone-500 h-full"></div>
                                            <div class="border-r border-dashed border-stone-500 h-full"></div>
                                            <div class="border-r border-dashed border-stone-500 h-full"></div>
                                            <div class="border-r border-dashed border-stone-500 h-full"></div>
                                            <div class="border-r border-dashed border-stone-500 h-full"></div>
                                            <div class="border-r border-dashed border-stone-500 h-full"></div>
                                            <div class="h-full"></div>
                                        </div>

                                        <!-- Artikel & Berita Redaksi Realistis (Sesuai Koran Asli Kabar Priangan) -->
                                        <div id="newspaperEditorialContent" class="space-y-1 text-stone-800 text-[6.5px] leading-tight pointer-events-none select-none">
                                            <!-- Di-render dinamis via JS -->
                                        </div>

                                        <!-- KOTAK SLOT IKLAN AKTIF -->
                                        <div id="simulasiIklanBoxWrapper" class="relative z-30 w-full flex justify-end mt-auto transition-all duration-300">
                                            <div id="simulasiIklanBox"
                                                 class="border-2 border-kp-blue-600 bg-white/95 text-stone-900 flex flex-col justify-between transition-all duration-300 shadow-md relative overflow-hidden"
                                                 style="width: 28.5%; height: 75px;">
                                                
                                                <!-- Overlay Watermark & Dimensi Slot -->
                                                <div class="absolute top-0.5 left-0.5 text-[6.5px] font-mono font-bold px-1 rounded bg-kp-blue-900/85 text-white z-20 flex items-center gap-1 shadow-2xs">
                                                    <span id="simulasiBadgeWarna">BW</span>
                                                    <span id="simulasiDimensiBadge">2 Kol × 100 mm</span>
                                                </div>

                                                <!-- Konten Internal Slot: Gambar Asli Terunggah / Live Teks Baris / Dummy Iklan Redaksi -->
                                                <div id="adSlotInnerContent" class="w-full h-full p-1 flex flex-col justify-center items-center text-center">
                                                    <div id="simulasiBoxTitle" class="text-[9px] font-black text-kp-blue-950 uppercase leading-tight font-serif">
                                                        Iklan Baris
                                                    </div>
                                                    <div id="simulasiBoxSubtitle" class="text-[7.5px] font-mono text-kp-blue-900 font-semibold">
                                                        2 Baris Cetak
                                                    </div>
                                                </div>

                                                <!-- Visual Baris Khusus Naskah Teks -->
                                                <div id="simulasiBarisTextContainer" class="w-full mt-1 space-y-0.5 hidden text-left font-mono text-[7px] leading-tight text-slate-800 bg-white/90 p-1 rounded border border-kp-blue-300">
                                                    <!-- Baris-baris naskah terisi via JS -->
                                                </div>

                                                <!-- Garis Kalibrasi CMYK (Jika Full Color) -->
                                                <div id="simulasiCmykStrip" class="absolute bottom-0 inset-x-0 h-1 hidden grid grid-cols-4 pointer-events-none">
                                                    <div class="bg-cyan-500"></div>
                                                    <div class="bg-fuchsia-500"></div>
                                                    <div class="bg-yellow-400"></div>
                                                    <div class="bg-black"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!-- Broadsheet Running Footer -->
                                <div class="border-t border-stone-300 mt-1 pt-0.5 flex items-center justify-between text-[6px] text-stone-500 font-sans">
                                    <span>WARTAWAN HU. KABAR PRIANGAN DILENGKAPI KARTU PERS</span>
                                    <span id="simulatorFooterUrl">kabarpriangan.pikiran-rakyat.com</span>
                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- 2. STRUK KALKULASI FINANSIAL -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-xs font-semibold text-slate-900 flex items-center gap-1.5">

                                <span class="text-sm font-bold text-slate-900">Rincian Biaya Transaksi</span>
                            </span>
                            <span id="badgeStatusBayar" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
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
                                        <td class="py-1 text-right font-mono font-normal text-slate-700 truncate" id="strukTarifDasar">-</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1 text-slate-600 font-medium whitespace-nowrap">Ukuran</td>
                                        <td class="py-1 text-center font-bold text-slate-400">:</td>
                                        <td class="py-1 text-right font-normal text-slate-700" id="strukDimensi">-</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1 text-slate-600 font-medium whitespace-nowrap">Frekuensi</td>
                                        <td class="py-1 text-center font-bold text-slate-400">:</td>
                                        <td class="py-1 text-right font-normal text-slate-700" id="strukFrekuensi">1 Kali Terbit</td>
                                    </tr>
                                    <tr class="border-t border-slate-200">
                                        <td class="pt-2 pb-1 text-slate-800 font-bold whitespace-nowrap">Brutto</td>
                                        <td class="pt-2 pb-1 text-center font-bold text-slate-400">:</td>
                                        <td class="pt-2 pb-1 text-right font-mono font-bold text-slate-900" id="strukBruto">Rp 0</td>
                                    </tr>

                                    <!-- 2. Section Input Diskon (Colspan 3) -->
                                    <tr class="border-t border-slate-200">
                                        <td colspan="3" class="pt-2.5 pb-2">
                                            <input type="hidden" name="diskon_agensi" id="diskon_agensi" value="0">
                                            <div class="space-y-1.5">
                                                <div class="flex items-center justify-between">
                                                    <label class="text-xs font-semibold text-slate-700">Diskon Transaksi</label>
                                                    <span class="text-[11px] text-slate-400 font-normal">Isi % atau nominal Rp</span>
                                                </div>
                                                <div class="grid grid-cols-5 gap-2">
                                                    <div class="col-span-2 relative">
                                                        <input type="number" name="diskon_persen" id="diskon_persen" 
                                                               value="{{ old('diskon_persen', isset($transaksi) ? $transaksi->diskon_persen : 0) }}" 
                                                               step="0.5" min="0" max="100"
                                                               placeholder="0"
                                                               oninput="handleDiskonPersenChange(this.value)"
                                                               class="w-full h-10 px-2.5 pr-6 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                                        <span class="absolute right-2 top-2.5 text-xs text-slate-400 font-bold pointer-events-none">%</span>
                                                    </div>
                                                    <div class="col-span-3 relative">
                                                        <span class="absolute left-2.5 top-2.5 text-xs text-slate-400 font-bold pointer-events-none">Rp</span>
                                                        <input type="text" name="diskon_khusus_display" id="diskon_khusus_display" 
                                                               value="{{ old('diskon_khusus', isset($transaksi) && $transaksi->diskon_khusus ? number_format($transaksi->diskon_khusus, 0, ',', '.') : 0) }}"
                                                               placeholder="0"
                                                               onkeyup="handleDiskonNominalInput(this)"
                                                               style="padding-left: 2.25rem !important;"
                                                               class="w-full h-10 pr-2.5 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                                        <input type="hidden" name="diskon_khusus" id="diskon_khusus" 
                                                               value="{{ old('diskon_khusus', isset($transaksi) ? $transaksi->diskon_khusus : 0) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- 3. Netto (DPP), PPN, Materai -->
                                    <tr class="border-t border-slate-200">
                                        <td class="pt-2 pb-1 text-slate-800 font-semibold whitespace-nowrap">Netto (DPP)</td>
                                        <td class="pt-2 pb-1 text-center font-bold text-slate-400">:</td>
                                        <td class="pt-2 pb-1 text-right font-mono font-semibold text-slate-800" id="strukDpp">Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1 text-slate-600 font-medium whitespace-nowrap">PPN ({{ $configs['ppn_persen'] }}%)</td>
                                        <td class="py-1 text-center font-bold text-slate-400">:</td>
                                        <td class="py-1 text-right font-mono font-normal text-slate-700" id="strukPpn">Rp 0</td>
                                    </tr>
                                    <tr id="rowMaterai" class="hidden">
                                        <td class="py-1 text-slate-600 font-medium whitespace-nowrap">Meterai</td>
                                        <td class="py-1 text-center font-bold text-slate-400">:</td>
                                        <td class="py-1 text-right font-mono font-normal text-slate-700" id="strukMaterai">Rp 0</td>
                                    </tr>

                                    <!-- 4. TOTAL TAGIHAN -->
                                    <tr class="border-t-2 border-slate-800">
                                        <td class="py-2.5 text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide whitespace-nowrap">Total Tagihan</td>
                                        <td class="py-2.5 text-center font-bold text-slate-400">:</td>
                                        <td class="py-2.5 text-right font-mono text-base sm:text-lg font-extrabold text-kp-blue-700" id="strukTotalNetto">Rp 0</td>
                                    </tr>

                                    <!-- 5. Input Nominal Bayar (Colspan 3) -->
                                    <tr class="border-t border-slate-100">
                                        <td colspan="3" class="pt-3 pb-2">
                                            <div class="space-y-1.5">
                                                <div class="flex items-center justify-between">
                                                    <label class="text-xs font-semibold text-slate-700">Nominal Bayar</label>
                                                    <button type="button" onclick="setBayarLunas()" class="text-xs font-semibold text-kp-blue-600 hover:underline cursor-pointer">
                                                        Bayar Lunas
                                                    </button>
                                                </div>
                                                <input type="text" inputmode="numeric" name="jumlahbayar_transaksikoran" id="jumlahbayar_transaksikoran" 
                                                       value="{{ old('jumlahbayar_transaksikoran') ? number_format((int) str_replace(['.', ','], '', old('jumlahbayar_transaksikoran')), 0, ',', '.') : (isset($transaksi) && $transaksi->jumlahbayar_transaksikoran ? number_format($transaksi->jumlahbayar_transaksikoran, 0, ',', '.') : '0') }}"
                                                       oninput="handleBayarInput()"
                                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition format-rupiah">
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- 6. Sisa Tagihan -->
                                    <tr class="border-t border-slate-100">
                                        <td class="pt-2 pb-1 text-slate-600 font-semibold whitespace-nowrap">Sisa Tagihan</td>
                                        <td class="pt-2 pb-1 text-center font-bold text-slate-400">:</td>
                                        <td class="pt-2 pb-1 text-right font-mono font-bold text-slate-900" id="strukPiutang">Rp 0</td>
                                    </tr>
                                </tbody>
                            </table>
                            <input type="hidden" name="materai" id="inputMaterai" value="0">
                        </div>

                        <!-- Tombol Aksi Submit Transaksi -->
                        <div class="pt-4 border-t border-slate-100 space-y-2">
                            <button type="button" onclick="konfirmasiSimpanTransaksi()" id="btnSimpanOrder"
                                    class="w-full h-12 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                <span>{{ isset($transaksi) ? 'Simpan Perubahan Transaksi' : 'Simpan Transaksi' }}</span>
                            </button>
                            @if(isset($transaksi))
                                <a href="{{ route('transaksikoran.show', $transaksi->id) }}" 
                                   class="w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-xl transition flex items-center justify-center text-center">
                                    Batal
                                </a>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        </form>

        <!-- ============================================================== -->
        <!-- ============================================================== -->
        @include('pages.iklan.transaksi.partials.modal_pelanggan_baru')

        <!-- ============================================================== -->
        <!-- MODAL KONFIRMASI SIMPAN TRANSAKSI                              -->
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
                            {{ isset($transaksi) ? 'Konfirmasi Perubahan Transaksi' : 'Konfirmasi Simpan Transaksi' }}
                        </h3>
                        <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">
                            Pastikan data pemesan, materi iklan, dan skema pembayaran telah sesuai sebelum menyimpan transaksi ini.
                        </p>
                    </div>

                    <!-- Ringkasan Singkat -->
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
                            <span>{{ isset($transaksi) ? 'Ya, Perbarui' : 'Ya, Simpan' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- MODAL TAMBAH SALES / AE (STANDAR 3 MEDIA)                       -->
        <!-- ============================================================== -->
        @include('pages.iklan.transaksi.partials.modal_sales_ae_baru')

            </div>
        </div>

    </div>

    <!-- JAVASCRIPT ENGINE UNTUK 2026 REACTIVE EXPERIENCE -->
    <script>
        // State Global Transaksi
        let currentProduct = @if($initialProduct) {
            id: {{ $initialProduct->id }},
            kode: '{{ $initialProduct->kode_jenis }}',
            tipe: '{{ $initialProduct->tipe_dasar }}',
            warna: '{{ $initialProduct->mode_warna }}',
            caraHitung: '{{ $initialProduct->cara_hitung }}',
            tarif: {{ isset($transaksi) && $transaksi->harga_transaksikoran ? $transaksi->harga_transaksikoran : $initialProduct->tarif_dasar }},
            isFixed: {{ $initialProduct->is_fixed_size ? 'true' : 'false' }},
            kolom: {{ isset($transaksi) && $transaksi->kolom_iklan ? $transaksi->kolom_iklan : ($initialProduct->default_kolom ?? 1) }},
            tinggi: {{ isset($transaksi) && $transaksi->tinggi_iklan ? $transaksi->tinggi_iklan : ($initialProduct->default_tinggi_mm ?? 0) }},
            minOrder: {{ $initialProduct->min_order ?? 2 }},
            nama: @json($initialProduct->nama_jenis)
        } @else null @endif;

        const configPpnPersen = {{ $configs['ppn_persen'] }};
        const configThresholdMaterai = {{ $configs['materai_threshold'] }};
        const configNominalMaterai = {{ $configs['materai_nominal'] }};

        // Multi-Date State & Integrasi Kalender Nasional (UI n-desapresence Standard)
        let selectedDates = [];
        let currentCalMonth = new Date().getMonth();
        let currentCalYear = new Date().getFullYear();
        let activeDetailDate = null;
        let kalenderDataCache = {
            [`${new Date().getFullYear()}-${new Date().getMonth() + 1}`]: @json($kalenderAwal ?? ['libur' => [], 'peringatan' => []])
        };
        let isKalenderLoading = false;

        // Iklan Baris Row State (ISU 2)
        let barisLines = ['', '']; // Minimal 2 baris awal

        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi tanggal default (besok)
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowStr = tomorrow.toISOString().split('T')[0];
            
            // Check old input tanggal jika ada
            const oldTanggalList = "{{ old('tanggal_muat_list', $initialDatesStr) }}";
            if (oldTanggalList) {
                selectedDates = oldTanggalList.split(',').map(s => s.trim()).filter(Boolean);
            }
            if (selectedDates.length === 0) {
                selectedDates = [tomorrowStr];
            }
            activeDetailDate = selectedDates[0] || tomorrowStr;

            // Jika ada tanggal terpilih, sesuaikan bulan kalender dengan tanggal pertama
            if (selectedDates.length > 0) {
                const firstD = new Date(selectedDates[0]);
                if (!isNaN(firstD.getTime())) {
                    currentCalYear = firstD.getFullYear();
                    currentCalMonth = firstD.getMonth();
                }
            }

            // Inisialisasi Naskah Baris dari old input jika ada
            const oldNaskah = @json(old('naskah_iklan_baris', isset($transaksi) ? ($transaksi->naskah_iklan_baris ?? $transaksi->keterangan_materi ?? '') : ''));
            if (oldNaskah && oldNaskah.trim().length > 0) {
                const splitLines = oldNaskah.split('\n');
                barisLines = splitLines.length >= 2 ? splitLines.slice(0, 7) : [splitLines[0] || '', ''];
            }

            initBarisInputs();
            renderCalendar();
            updatePanelInfoTanggal(activeDetailDate);
            renderSelectedDatesChips();
            
            // Inisialisasi simulator halaman sesuai pilihan penempatan halaman awal
            const selKop = document.getElementById('kop_kota_id');
            if (selKop && selKop.selectedIndex >= 0) {
                let initHal = parseInt(selKop.options[selKop.selectedIndex].dataset.halaman) || 1;
                if (initHal > 8) initHal = 4;
                setSimulatorPage(initHal, false);
            } else {
                setSimulatorPage(1, false);
            }

            // Inisialisasi status awal produk terpilih jika ada
            if (currentProduct) {
                pilihProdukIklan(
                    currentProduct.id,
                    currentProduct.kode,
                    currentProduct.tipe,
                    currentProduct.warna,
                    currentProduct.caraHitung,
                    currentProduct.tarif,
                    currentProduct.isFixed,
                    currentProduct.kolom,
                    currentProduct.tinggi,
                    currentProduct.minOrder,
                    currentProduct.nama
                );
            } else {
                resetStrukPreviewToEmpty();
            }

            @if(isset($transaksi) && $transaksi->kategori_form !== 'BARIS')
                const initKol = {{ $transaksi->kolom_iklan ?: 2 }};
                const initMm = {{ $transaksi->tinggi_iklan ?: 100 }};
                document.getElementById('lebar_kolom').value = initKol;
                document.getElementById('tinggi_mm').value = initMm;
                const isCustomDim = (initKol !== (currentProduct.defaultKolom || 2) || initMm !== (currentProduct.defaultTinggi || 50));
                if (isCustomDim || !currentProduct.isFixed) {
                    toggleUkuranManual(true, false);
                }
            @endif

            // Realtime update simulator on typing client name & phone
            const manualNameInput = document.getElementById('inputManualNamaPemasang');
            if (manualNameInput) manualNameInput.addEventListener('input', () => updateSimulator());
            const telpInput = document.getElementById('telepon_pemasangkoran');
            if (telpInput) telpInput.addEventListener('input', () => updateSimulator());

            // Set initial jenis pembayaran UI
            const initialJenis = document.getElementById('jenis_pembayaran').value || 'Tunai';
            setJenisPembayaran(initialJenis);

            // Panggil ulang kalkulasi finansial
            recalculateFinancials();

            // Form Submit Interceptor & Guardrails
            document.getElementById('formTransaksiKoran').addEventListener('submit', function (e) {
                // Guardrail 0: Validasi & Fallback Nama Pemasang
                const hiddenNamaInput = document.getElementById('nama_pemasangkoran');
                const manualInput = document.getElementById('inputManualNamaPemasang');
                const searchInput = document.getElementById('inputSearchPelanggan');

                let finalNama = (manualInput && manualInput.value.trim()) || (hiddenNamaInput.value || '').trim() || (searchInput && searchInput.value.trim());

                if (!finalNama) {
                    e.preventDefault();
                    showCustomToast('Silakan pilih pelanggan dari daftar atau ketik nama pemesan di kolom yang tersedia.', 'warning', 'Data Pemesan Belum Lengkap');
                    if (manualInput) manualInput.focus();
                    return false;
                }
                hiddenNamaInput.value = finalNama;

                // Iklan Baris minimal 2 baris teks terisi
                const tipe = currentProduct.tipe;
                if (tipe === 'BARIS') {
                    const filledCount = barisLines.filter(l => l.trim().length > 0).length;
                    if (filledCount < 2) {
                        e.preventDefault();
                        showCustomToast('Iklan baris wajib diisi minimal 2 baris naskah cetak sebelum disimpan.', 'warning', 'Naskah Belum Lengkap');
                        return false;
                    }
                }

                // Kesesuaian frekuensi terbit dengan jumlah tanggal terpilih
                const totalMuat = parseInt(document.getElementById('total_muatkoran').value) || 1;
                if (selectedDates.length !== totalMuat) {
                    e.preventDefault();
                    showCustomToast(`Frekuensi terbit (${totalMuat} edisi) harus sesuai dengan jumlah tanggal yang dipilih (${selectedDates.length} tanggal). Sesuaikan di kalender.`, 'warning', 'Kesesuaian Tanggal Terbit');
                    return false;
                }
            });

            // Keyboard shortcut F2 untuk simpan
            window.addEventListener('keydown', function (e) {
                if (e.key === 'F2') {
                    e.preventDefault();
                    document.getElementById('btnSimpanOrder').click();
                }
            });
        });

        // ==============================================================
        // ISU 4: METODE PEMBAYARAN 60-30-10 PALETTE
        // ==============================================================
        function setJenisPembayaran(jenis) {
            document.getElementById('jenis_pembayaran').value = jenis;

            const cardTunai = document.getElementById('cardBayarTunai');
            const cardKredit = document.getElementById('cardBayarKredit');
            const dotTunai = document.getElementById('dotBayarTunai');
            const dotKredit = document.getElementById('dotBayarKredit');
            const iconBoxTunai = document.getElementById('iconBoxTunai');
            const iconBoxKredit = document.getElementById('iconBoxKredit');

            if (jenis === 'Tunai') {
                // Tunai AKTIF
                cardTunai.className = "px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-emerald-50/70 border-emerald-500 text-emerald-950 font-semibold shadow-2xs";
                dotTunai.className = "text-emerald-600 flex items-center";
                if (iconBoxTunai) iconBoxTunai.className = "w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0";

                // Kredit INAKTIF
                cardKredit.className = "px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-white border-slate-200 text-slate-600 hover:border-slate-300 font-medium";
                dotKredit.className = "text-transparent flex items-center";
                if (iconBoxKredit) iconBoxKredit.className = "w-6 h-6 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center shrink-0";
            } else {
                // Kredit AKTIF
                cardKredit.className = "px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-amber-50/70 border-amber-500 text-amber-950 font-semibold shadow-2xs";
                dotKredit.className = "text-amber-600 flex items-center";
                if (iconBoxKredit) iconBoxKredit.className = "w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0";

                // Tunai INAKTIF
                cardTunai.className = "px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-white border-slate-200 text-slate-600 hover:border-slate-300 font-medium";
                dotTunai.className = "text-transparent flex items-center";
                if (iconBoxTunai) iconBoxTunai.className = "w-6 h-6 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center shrink-0";
            }

            recalculateFinancials();
        }

        // ==============================================================
        // ISU 6: TAB GROUPING PRODUK IKLAN
        // ==============================================================
        function switchProductTab(targetTab) {
            // Update Tab Button Styles (Selaras Ukuran Foto 1)
            document.querySelectorAll('.tab-product-btn').forEach(btn => {
                btn.className = "tab-product-btn px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 bg-white text-slate-700 font-medium border border-slate-200 hover:border-slate-300 hover:bg-slate-50";
                const badge = btn.querySelector('span:last-child');
                if (badge) badge.className = "px-1.5 py-0.5 rounded-full text-xs bg-slate-100 text-slate-600 font-medium";
            });

            const activeBtn = document.getElementById('tabBtn_' + targetTab);
            if (activeBtn) {
                activeBtn.className = "tab-product-btn px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 bg-kp-blue-600 text-white font-semibold shadow-xs border border-kp-blue-600";
                const badge = activeBtn.querySelector('span:last-child');
                if (badge) badge.className = "px-1.5 py-0.5 rounded-full text-xs bg-white/20 text-white font-semibold";
            }

            // Show/Hide Content
            document.querySelectorAll('.tab-product-content').forEach(content => {
                content.classList.add('hidden');
            });
            const targetContent = document.getElementById('tabContent_' + targetTab);
            if (targetContent) {
                targetContent.classList.remove('hidden');

                // Jika produk terpilih saat ini bukan dari tab ini, pilih produk pertama di tab ini
                const currentCard = document.getElementById('cardProduk_' + currentProduct.id);
                if (!currentCard || currentCard.dataset.tab !== targetTab) {
                    const firstBtn = targetContent.querySelector('.produk-card');
                    if (firstBtn) firstBtn.click();
                }
            }
        }
        function toggleUkuranManual(isManual, syncValues = true) {
            const panelCustom = document.getElementById('panelUkuranCustom');
            const chk = document.getElementById('checkUkuranManual');
            if (chk) {
                chk.checked = isManual;
                const wrapper = chk.closest('label');
                if (wrapper) {
                    if (isManual) {
                        wrapper.className = "inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-kp-blue-500/50 bg-kp-blue-50 text-kp-blue-800 transition cursor-pointer select-none shrink-0 self-start sm:self-auto shadow-2xs font-semibold";
                    } else {
                        wrapper.className = "inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-white hover:border-slate-300 text-slate-700 transition cursor-pointer select-none shrink-0 self-start sm:self-auto shadow-2xs font-medium";
                    }
                }
            }
            if (panelCustom) {
                if (isManual) {
                    panelCustom.classList.remove('hidden');
                } else {
                    panelCustom.classList.add('hidden');
                    if (syncValues && currentProduct) {
                        const defKol = currentProduct.defaultKolom || currentProduct.kolom || 2;
                        const defMm = currentProduct.defaultTinggi || currentProduct.tinggi || 50;
                        document.getElementById('lebar_kolom').value = defKol;
                        document.getElementById('tinggi_mm').value = defMm;
                    }
                }
            }
            handleDimensiChange();
            recalculateFinancials();
            updateSimulator();
        }

        function pilihProdukIklan(id, kode, tipe, warna, caraHitung, tarif, isFixed, defaultKolom, defaultTinggi, minOrder, fallbackNama) {
            const isFixedBool = (isFixed === true || isFixed === 'true');
            const cardEl = document.getElementById('cardProduk_' + id);
            const resolvedNama = cardEl?.dataset?.nama || fallbackNama || currentProduct?.nama || 'Iklan';
            const defK = defaultKolom || (tipe === 'KOLOM' || tipe === 'DISPLAY' ? 2 : 1);
            const defM = defaultTinggi || 50;

            currentProduct = {
                id, kode, tipe, warna, caraHitung, tarif,
                isFixed: isFixedBool,
                kolom: defK,
                tinggi: defM,
                defaultKolom: defK,
                defaultTinggi: defM,
                minOrder: minOrder || (tipe === 'BARIS' ? 2 : 1),
                nama: resolvedNama
            };

            // Update UI card aktif
            document.querySelectorAll('.produk-card').forEach(card => {
                card.className = "produk-card text-left p-3 rounded-xl border transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2 border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50";
                const b = card.querySelector('.badge-corner-check');
                if (b) b.classList.add('hidden');
            });
            const activeCard = document.getElementById('cardProduk_' + id);
            if (activeCard) {
                activeCard.className = "produk-card text-left p-3 rounded-xl border transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2 border-2 border-kp-blue-600 bg-kp-blue-50/40 ring-1 ring-kp-blue-500/20 shadow-xs";
                const b = activeCard.querySelector('.badge-corner-check');
                if (b) b.classList.remove('hidden');
            }

            // Sync Hidden Form Fields
            document.getElementById('id_iklankoran').value = id;
            document.getElementById('kategori_form').value = tipe;
            document.getElementById('warna_iklan').value = warna;

            // Tampilkan section yang relevan
            const secBaris = document.getElementById('sectionIklanBaris');
            const secDisplay = document.getElementById('sectionIklanDisplay');
            const contKopIklan = document.getElementById('containerKopIklan');
            const panelFixed = document.getElementById('panelUkuranFixed');
            const placeholderEl = document.getElementById('placeholderPilihProduk');
            if (placeholderEl) placeholderEl.classList.add('hidden');

            if (tipe === 'BARIS') {
                secBaris.classList.remove('hidden');
                secDisplay.classList.add('hidden');
                contKopIklan.classList.remove('hidden');
                const lblTarif = document.getElementById('labelTarifBaris');
                if (lblTarif) {
                    lblTarif.textContent = `Tarif: Rp ${tarif.toLocaleString('id-ID')}/baris`;
                }
                syncBarisNaskah();
            } else {
                secBaris.classList.add('hidden');
                secDisplay.classList.remove('hidden');
                contKopIklan.classList.add('hidden');

                if (panelFixed) panelFixed.classList.remove('hidden');

                const elName = document.getElementById('fixedAdPackageName');
                if (elName) elName.textContent = currentProduct.nama;
                const elKol = document.getElementById('fixedAdKolomLabel');
                if (elKol) elKol.textContent = `${defK} Kolom`;
                const elTinggi = document.getElementById('fixedAdTinggiLabel');
                if (elTinggi) elTinggi.textContent = `${defM} mm`;
                const elTotalMmk = document.getElementById('fixedAdTotalMmkLabel');
                if (elTotalMmk) elTotalMmk.textContent = `${defK * defM} mmk`;
                const elColor = document.getElementById('fixedAdColorBadge');
                if (elColor) elColor.textContent = (warna === 'FC' ? 'Full Color (FC)' : 'Hitam Putih (BW)');
                const elRingkas = document.getElementById('fixedAdDimensiRingkas');
                if (elRingkas) elRingkas.textContent = `${defK} Kolom × ${defM} mm (${defK * defM} mmk)`;

                document.getElementById('lebar_kolom').value = defK;
                document.getElementById('tinggi_mm').value = defM;

                // Jika produk bertipe DISPLAY non-fixed, buka panel manual untuk memudahkan pemilihan preset
                // Jika produk bertipe paket modul baku (KOLOM, ADVERTORIAL, Kuping), panel manual tertutup
                const shouldOpenManual = (!isFixedBool && tipe === 'DISPLAY');
                const chk = document.getElementById('checkUkuranManual');
                if (chk) chk.checked = shouldOpenManual;
                toggleUkuranManual(shouldOpenManual, false);
            }

            handleDimensiChange();
            recalculateFinancials();
            updateSimulator();
        }

        // ==============================================================
        // ISU 2: INPUT NASKAH IKLAN BARIS PER ROW (MAKS. 32 CHARS, MIN 2 - MAKS 7)
        // ==============================================================
        function initBarisInputs() {
            renderBarisRows();
            syncBarisNaskah();
            const lblTarif = document.getElementById('labelTarifBaris');
            if (lblTarif && currentProduct && (currentProduct.caraHitung === 'PER_BARIS' || currentProduct.tipe === 'BARIS')) {
                lblTarif.textContent = `Tarif: Rp ${currentProduct.tarif.toLocaleString('id-ID')}/baris`;
            }
        }

        function renderBarisRows() {
            const container = document.getElementById('containerBarisRows');
            container.innerHTML = '';

            barisLines.forEach((text, index) => {
                const rowNum = index + 1;
                const rowDiv = document.createElement('div');
                rowDiv.className = "flex items-center gap-2 p-2 bg-slate-50 border border-slate-200 rounded-xl transition-all focus-within:bg-white focus-within:border-kp-blue-500 focus-within:ring-2 focus-within:ring-kp-blue-500/10";
                
                // Badge Nomor Baris
                const numBadge = document.createElement('span');
                numBadge.className = "w-18 text-center py-1 rounded-lg bg-slate-200/80 text-slate-700 font-mono text-xs font-bold shrink-0";
                numBadge.textContent = `Baris ${rowNum}`;
                rowDiv.appendChild(numBadge);

                // Input Text Teks (Maks 32 chars)
                const input = document.createElement('input');
                input.type = 'text';
                input.maxLength = 32;
                input.value = text;
                input.placeholder = `Tulis naskah baris ${rowNum} (maks. 32 karakter)...`;
                input.className = "flex-1 h-9 px-2 bg-transparent text-sm font-mono font-medium text-slate-900 focus:outline-none";
                input.oninput = (e) => handleSingleBarisInput(index, e.target.value);
                rowDiv.appendChild(input);

                // Character Counter
                const counter = document.createElement('span');
                counter.id = `barisCounter_${index}`;
                const len = text.length;
                counter.className = `w-14 text-right font-mono text-xs font-bold shrink-0 ${len === 32 ? 'text-amber-600' : (len > 0 ? 'text-emerald-700' : 'text-slate-400')}`;
                counter.textContent = `${len}/32`;
                rowDiv.appendChild(counter);

                // Delete Button (Hanya jika baris > 2)
                if (barisLines.length > 2) {
                    const btnDel = document.createElement('button');
                    btnDel.type = 'button';
                    btnDel.title = 'Hapus baris ini';
                    btnDel.className = "w-7 h-7 flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg text-sm cursor-pointer transition shrink-0";
                    btnDel.innerHTML = '&times;';
                    btnDel.onclick = () => hapusBarisInput(index);
                    rowDiv.appendChild(btnDel);
                }

                container.appendChild(rowDiv);
            });

            // Update status tombol tambah (maks 7 baris)
            const btnTambah = document.getElementById('btnTambahBaris');
            if (barisLines.length >= 7) {
                btnTambah.disabled = true;
                btnTambah.classList.add('opacity-40', 'cursor-not-allowed');
            } else {
                btnTambah.disabled = false;
                btnTambah.classList.remove('opacity-40', 'cursor-not-allowed');
            }
        }

        function handleSingleBarisInput(index, val) {
            barisLines[index] = val;
            const counter = document.getElementById(`barisCounter_${index}`);
            if (counter) {
                const len = val.length;
                counter.textContent = `${len}/32`;
                counter.className = `w-14 text-right font-mono text-xs font-bold shrink-0 ${len === 32 ? 'text-amber-600' : (len > 0 ? 'text-emerald-700' : 'text-slate-400')}`;
            }
            syncBarisNaskah();
        }

        function tambahBarisInput() {
            if (barisLines.length < 7) {
                barisLines.push('');
                renderBarisRows();
                syncBarisNaskah();
            }
        }

        function hapusBarisInput(index) {
            if (barisLines.length > 2) {
                barisLines.splice(index, 1);
                renderBarisRows();
                syncBarisNaskah();
            }
        }

        function syncBarisNaskah() {
            // Join string
            const fullNaskah = barisLines.join('\n');
            document.getElementById('naskah_iklan_baris').value = fullNaskah;

            // Hitung baris terisi (minimal 2)
            const filledLines = barisLines.filter(l => l.trim().length > 0);
            const countFilled = Math.max(2, filledLines.length);

            document.getElementById('jumlah_baris').value = countFilled;
            document.getElementById('labelJumlahBaris').textContent = countFilled;

            // Update Live Preview Box Iklan Baris
            renderBarisLivePreview();
            recalculateFinancials();
            updateSimulator();
        }

        function renderBarisLivePreview() {
            const previewBox = document.getElementById('previewBoxBaris');
            const totalCharSpan = document.getElementById('previewBarisCharTotal');
            previewBox.innerHTML = '';

            let totalChars = 0;
            const filledLines = barisLines.map(l => l.trim()).filter(l => l.length > 0);

            if (filledLines.length === 0) {
                previewBox.innerHTML = '<div class="text-slate-400 italic text-xs py-1 text-center">(Ketik naskah baris 1 dan baris 2 di atas...)</div>';
                totalCharSpan.textContent = '0 Karakter Total';
                return;
            }

            filledLines.forEach((line, i) => {
                totalChars += line.length;
                const lineDiv = document.createElement('div');
                lineDiv.className = "py-1 flex items-baseline justify-between gap-2";
                lineDiv.innerHTML = `
                    <span class="font-bold text-slate-800 tracking-wider uppercase">${line}</span>
                    <span class="text-xs text-slate-400 font-mono shrink-0">${line.length} ch</span>
                `;
                previewBox.appendChild(lineDiv);
            });

            totalCharSpan.textContent = `${totalChars} Karakter Total (${filledLines.length} Baris)`;
        }

        // ==============================================================
        // ISU 1: SIMULATOR TATA LETAK HALAMAN REALISTIS (50, 75, 100, 500 MM & BARIS TEXT)
        // ==============================================================
        function handleDimensiChange() {
            let kol = parseInt(document.getElementById('lebar_kolom').value) || 2;
            let mm = parseInt(document.getElementById('tinggi_mm').value) || 50;

            const chk = document.getElementById('checkUkuranManual');
            const isManualActive = chk ? chk.checked : (!currentProduct || !currentProduct.isFixed);

            if (!isManualActive && currentProduct && currentProduct.isFixed) {
                kol = currentProduct.defaultKolom || currentProduct.kolom || 1;
                mm = currentProduct.defaultTinggi || currentProduct.tinggi || 40;
                document.getElementById('lebar_kolom').value = kol;
                document.getElementById('tinggi_mm').value = mm;
            } else {
                // Batas Fisik 1-7 Kolom & 10-540 mm
                if (kol > 7) { kol = 7; document.getElementById('lebar_kolom').value = 7; }
                if (kol < 1) { kol = 1; document.getElementById('lebar_kolom').value = 1; }
                if (mm > 540) { mm = 540; document.getElementById('tinggi_mm').value = 540; }
                if (mm < 10) { mm = 10; document.getElementById('tinggi_mm').value = 10; }
            }

            const totalMmk = kol * mm;
            const labelMmk = document.getElementById('labelTotalMmk');
            if (labelMmk) labelMmk.textContent = totalMmk.toLocaleString('id-ID') + ' mmk';
            document.getElementById('ukuran_iklan').value = `${kol} kol × ${mm} mm`;

            // Sinkronkan rincian di kartu spesifikasi produk
            const elKol = document.getElementById('fixedAdKolomLabel');
            if (elKol) elKol.textContent = `${kol} Kolom`;
            const elTinggi = document.getElementById('fixedAdTinggiLabel');
            if (elTinggi) elTinggi.textContent = `${mm} mm`;
            const elTotalMmk = document.getElementById('fixedAdTotalMmkLabel');
            if (elTotalMmk) elTotalMmk.textContent = `${totalMmk} mmk`;
            const elRingkas = document.getElementById('fixedAdDimensiRingkas');
            if (elRingkas) elRingkas.textContent = `${kol} Kolom × ${mm} mm (${totalMmk} mmk)`;

            syncProductCardSelection(kol, mm, isManualActive);
            recalculateFinancials();
            updateSimulator();
        }

        function syncProductCardSelection(kol, mm, isManualActive) {
            if (!currentProduct) return;

            const elName = document.getElementById('fixedAdPackageName');
            const activeTabContent = document.querySelector('.tab-product-content:not(.hidden)');
            if (!activeTabContent) return;

            const cardsInTab = activeTabContent.querySelectorAll('.produk-card');

            // Jika produk bertipe fleksibel / non-fixed (seperti Iklan Display), kartu tetap aktif normal
            if (!currentProduct.isFixed) {
                const flexCard = document.getElementById('cardProduk_' + currentProduct.id);
                if (flexCard) {
                    cardsInTab.forEach(card => {
                        card.className = "produk-card text-left p-3 rounded-xl border transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2 border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50";
                        const b = card.querySelector('.badge-corner-check');
                        if (b) b.classList.add('hidden');
                    });
                    flexCard.className = "produk-card text-left p-3 rounded-xl border transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2 border-2 border-kp-blue-600 bg-kp-blue-50/40 ring-1 ring-kp-blue-500/20 shadow-xs";
                    const check = flexCard.querySelector('.badge-corner-check');
                    if (check) check.classList.remove('hidden');
                    if (elName) elName.textContent = flexCard.dataset.nama || currentProduct.nama;
                }
                return;
            }

            // Untuk produk bertipe fixed (seperti Iklan Kolom BW 1x50, 2x100, dsb):
            // 1. Cek apakah ada kartu baku di tab yang cocok persis dengan dimensi kol x mm yang baru
            let matchingCard = null;
            cardsInTab.forEach(card => {
                const cKol = parseInt(card.dataset.kol);
                const cMm = parseInt(card.dataset.mm);
                if (cKol === kol && cMm === mm) {
                    matchingCard = card;
                }
            });

            if (matchingCard) {
                // Dimensi COCOK dengan produk baku (misal user ketik 2 kolom x 100 mm)
                // Pindahkan seleksi ke kartu baku tersebut di Seksi 2!
                cardsInTab.forEach(card => {
                    card.className = "produk-card text-left p-3 rounded-xl border transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2 border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50";
                    const b = card.querySelector('.badge-corner-check');
                    if (b) b.classList.add('hidden');
                });

                matchingCard.className = "produk-card text-left p-3 rounded-xl border transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2 border-2 border-kp-blue-600 bg-kp-blue-50/40 ring-1 ring-kp-blue-500/20 shadow-xs";
                const check = matchingCard.querySelector('.badge-corner-check');
                if (check) check.classList.remove('hidden');

                const matchedId = matchingCard.dataset.id;
                document.getElementById('id_iklankoran').value = matchedId;
                currentProduct.id = matchedId;
                currentProduct.nama = matchingCard.dataset.nama;
                currentProduct.defaultKolom = kol;
                currentProduct.defaultTinggi = mm;

                if (elName) {
                    elName.textContent = matchingCard.dataset.nama;
                }
            } else if (isManualActive) {
                // Dimensi KUSTOM yang tidak cocok dengan produk baku manapun di tab (misal 7 x 270 mm atau 3 x 80 mm)
                // Hapus tanda centang di kartu baku lama agar TIDAK ada kartu yang terselect salah!
                cardsInTab.forEach(card => {
                    const b = card.querySelector('.badge-corner-check');
                    if (b) b.classList.add('hidden');
                    if (card.id === 'cardProduk_' + currentProduct.id) {
                        card.className = "produk-card text-left p-3 rounded-xl border-2 border-dashed border-kp-blue-400 bg-kp-blue-50/20 transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2 shadow-2xs";
                    } else {
                        card.className = "produk-card text-left p-3 rounded-xl border transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2 border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50";
                    }
                });

                // Perbarui judul di Seksi 3 agar tidak lagi memunculkan dimensi lama "(1 Kolom × 50 mm)"
                if (elName) {
                    const baseNama = currentProduct.nama.replace(/\s*\(\d+.*?\)/gi, '').trim();
                    elName.textContent = `${baseNama} (Ukuran Kustom: ${kol} Kolom × ${mm} mm)`;
                }
            } else {
                // Saat manual tidak aktif, pastikan kartu default terselect normal
                const defaultCard = document.getElementById('cardProduk_' + currentProduct.id);
                if (defaultCard) {
                    cardsInTab.forEach(card => {
                        card.className = "produk-card text-left p-3 rounded-xl border transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2 border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50";
                        const b = card.querySelector('.badge-corner-check');
                        if (b) b.classList.add('hidden');
                    });
                    defaultCard.className = "produk-card text-left p-3 rounded-xl border transition cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2 border-2 border-kp-blue-600 bg-kp-blue-50/40 ring-1 ring-kp-blue-500/20 shadow-xs";
                    const check = defaultCard.querySelector('.badge-corner-check');
                    if (check) check.classList.remove('hidden');
                    if (elName) elName.textContent = defaultCard.dataset.nama || currentProduct.nama;
                }
            }
        }

        // ==============================================================
        // REALISTIC KABAR PRIANGAN NEWSPAPER SIMULATOR STATE & LOGIC
        // ==============================================================
        let currentSimulatorPage = 5;
        let currentSlotPosition = 'bottom-right';
        let isSimulatorZoomed = false;
        let showSimulatorGrid = true;
        let uploadedAdImageSrc = null;

        function toggleSimulatorGrid() {
            showSimulatorGrid = !showSimulatorGrid;
            const overlay = document.getElementById('colGuidesOverlay');
            const btn = document.getElementById('btnSimulatorGrid');
            if (overlay) overlay.style.display = showSimulatorGrid ? 'grid' : 'none';
            if (btn) {
                if (showSimulatorGrid) {
                    btn.classList.add('bg-kp-blue-50', 'border-kp-blue-300', 'text-kp-blue-700');
                    btn.classList.remove('bg-white', 'text-slate-700');
                } else {
                    btn.classList.remove('bg-kp-blue-50', 'border-kp-blue-300', 'text-kp-blue-700');
                    btn.classList.add('bg-white', 'text-slate-700');
                }
            }
        }

        function toggleSimulatorZoom() {
            isSimulatorZoomed = !isSimulatorZoomed;
            const paper = document.getElementById('broadsheetPaper');
            const label = document.getElementById('labelSimulatorZoom');
            const btn = document.getElementById('btnSimulatorZoom');
            if (isSimulatorZoomed) {
                paper.style.transform = 'scale(1.35)';
                paper.style.transformOrigin = 'top center';
                if (label) label.textContent = 'Kecilkan';
                if (btn) btn.classList.add('bg-kp-blue-50', 'border-kp-blue-300', 'text-kp-blue-700');
            } else {
                paper.style.transform = 'scale(1)';
                if (label) label.textContent = 'Perbesar';
                if (btn) btn.classList.remove('bg-kp-blue-50', 'border-kp-blue-300', 'text-kp-blue-700');
            }
        }

        function setSimulatorPage(pageNumber, syncDropdown = true) {
            currentSimulatorPage = parseInt(pageNumber) || 1;
            if (currentSimulatorPage < 1) currentSimulatorPage = 1;
            if (currentSimulatorPage > 8) currentSimulatorPage = 8;

            const pageLabels = {
                1: 'Hal. 1 • Cover / Utama',
                2: 'Hal. 2 • Garut - Sumedang',
                3: 'Hal. 3 • Kab. Tasikmalaya',
                4: 'Hal. 4 • Ciamis (Redaksi)',
                5: 'Hal. 5 • Banjar - Pangandaran',
                6: 'Hal. 6 • Kota Tasikmalaya',
                7: 'Hal. 7 • Priangan / Iklan',
                8: 'Hal. 8 • Back Cover'
            };

            const badge = document.getElementById('simulatorHalamanBadge');
            if (badge) badge.textContent = pageLabels[currentSimulatorPage] || `Hal. ${currentSimulatorPage}`;

            const miniHal = document.getElementById('simulatorHalMini');
            if (miniHal) miniHal.textContent = `HALAMAN ${currentSimulatorPage}`;

            if (syncDropdown) {
                const sel = document.getElementById('kop_kota_id');
                if (sel) {
                    for (let i = 0; i < sel.options.length; i++) {
                        if (parseInt(sel.options[i].dataset.halaman) === currentSimulatorPage) {
                            sel.selectedIndex = i;
                            document.getElementById('halaman_iklan').value = sel.options[i].dataset.nama || sel.options[i].text;
                            recalculateFinancials();
                            break;
                        }
                    }
                }
            }

            renderNewspaperPageTemplate();
            updateSimulator();
        }

        let slotVertical = 'bottom';   // 'top', 'middle', 'bottom'
        let slotHorizontal = 'right';  // 'left', 'center', 'right'
        let isSlotKuping = false;

        function setSlotVertical(v) {
            isSlotKuping = false;
            slotVertical = v;
            refreshExcelToolbarUI();
            updateSimulator();
        }

        function setSlotHorizontal(h) {
            isSlotKuping = false;
            slotHorizontal = h;
            refreshExcelToolbarUI();
            updateSimulator();
        }

        function setSlotKuping() {
            isSlotKuping = !isSlotKuping;
            if (isSlotKuping) {
                slotVertical = 'top';
                slotHorizontal = 'right';
            }
            refreshExcelToolbarUI();
            updateSimulator();
        }

        function refreshExcelToolbarUI() {
            // Vertikal buttons
            ['top', 'middle', 'bottom'].forEach(v => {
                const btn = document.getElementById(`btnV_${v}`);
                if (!btn) return;
                if (!isSlotKuping && slotVertical === v) {
                    btn.className = "excel-v-btn w-7 h-7 rounded-lg flex items-center justify-center transition cursor-pointer bg-kp-blue-50 text-kp-blue-700 font-bold border border-kp-blue-300 shadow-2xs";
                } else {
                    btn.className = "excel-v-btn w-7 h-7 rounded-lg flex items-center justify-center transition cursor-pointer text-slate-600 hover:bg-slate-100 hover:text-slate-900 border border-transparent";
                }
            });

            // Horizontal buttons
            ['left', 'center', 'right'].forEach(h => {
                const btn = document.getElementById(`btnH_${h}`);
                if (!btn) return;
                if (!isSlotKuping && slotHorizontal === h) {
                    btn.className = "excel-h-btn w-7 h-7 rounded-lg flex items-center justify-center transition cursor-pointer bg-kp-blue-50 text-kp-blue-700 font-bold border border-kp-blue-300 shadow-2xs";
                } else {
                    btn.className = "excel-h-btn w-7 h-7 rounded-lg flex items-center justify-center transition cursor-pointer text-slate-600 hover:bg-slate-100 hover:text-slate-900 border border-transparent";
                }
            });

            // Kuping button
            const kupingBtn = document.getElementById('btn_kuping');
            if (kupingBtn) {
                if (isSlotKuping) {
                    kupingBtn.className = "h-9 px-3 rounded-xl border-2 border-kp-blue-600 bg-sky-50 text-kp-blue-900 font-bold shadow-2xs transition flex items-center gap-1.5 cursor-pointer text-center text-xs";
                } else {
                    kupingBtn.className = "h-9 px-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition flex items-center gap-1.5 shadow-2xs cursor-pointer text-center text-xs font-semibold";
                }
            }
        }

        function setSlotPosition(pos) {
            if (pos === 'top-ear' || pos === 'kuping') {
                setSlotKuping();
            } else if (pos === 'bottom-left') {
                slotVertical = 'bottom';
                slotHorizontal = 'left';
                isSlotKuping = false;
                refreshExcelToolbarUI();
                updateSimulator();
            } else if (pos === 'center') {
                slotVertical = 'middle';
                slotHorizontal = 'center';
                isSlotKuping = false;
                refreshExcelToolbarUI();
                updateSimulator();
            } else {
                slotVertical = 'bottom';
                slotHorizontal = 'right';
                isSlotKuping = false;
                refreshExcelToolbarUI();
                updateSimulator();
            }
        }

        function renderNewspaperPageTemplate() {
            const headerContainer = document.getElementById('newspaperHeaderContainer');
            const contentContainer = document.getElementById('newspaperEditorialContent');
            if (!headerContainer || !contentContainer) return;

            const tanggalText = document.getElementById('simulatorTanggalHeader')?.textContent || 'Jumat, 9 Oktober 2026';

            if (currentSimulatorPage === 1) {
                headerContainer.innerHTML = `
                    <div class="flex items-center justify-between text-[6px] border-b border-black pb-0.5 mb-0.5 font-sans">
                        <span class="font-bold">EDISI JABAR &amp; PRIANGAN TIMUR</span>
                        <span>${tanggalText}</span>
                        <span class="font-bold">ECERAN Rp 3.000,-</span>
                    </div>
                    <div class="text-center my-0.5">
                        <div class="text-[6px] font-sans font-bold tracking-widest text-stone-600 uppercase">HARIAN UMUM</div>
                        <div class="text-[17px] font-black tracking-tight text-stone-900 uppercase font-serif leading-none" style="letter-spacing: -0.5px;">
                            Kabar Priangan
                        </div>
                        <div class="flex items-center justify-center gap-2 text-[5.5px] italic text-stone-600 font-serif border-t border-b border-black/20 py-0.5 mt-0.5">
                            <span>TERBAIK &amp; BERPENGARUH</span>
                            <span>•</span>
                            <span>GRUP PIKIRAN RAKYAT</span>
                            <span>•</span>
                            <span class="font-mono font-bold">HALAMAN 1</span>
                        </div>
                    </div>
                `;
                contentContainer.innerHTML = `
                    <div class="space-y-1">
                        <div class="border-b border-stone-400 pb-0.5">
                            <div class="text-[5.5px] font-sans font-bold text-rose-800 uppercase tracking-wider">HEADLINE / OLAHRAGA PRIANGAN</div>
                            <div class="text-[9.5px] font-black uppercase text-stone-900 leading-tight font-serif">
                                Hasil Drawing League Cup 2026: PSGC Ciamis Tantang Dua Klub Liga 1
                            </div>
                            <div class="text-[6px] italic text-stone-700 font-serif">
                                Laskar Singacala Masuk Zona Barat 2 Grup F Bersama Garudayaksa FC &amp; PSS Sleman
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-1">
                            <div class="col-span-4 bg-stone-200 border border-stone-400 rounded-2xs p-1 flex flex-col justify-center items-center text-center">
                                <svg class="w-4 h-4 text-stone-400 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-[5px] font-sans font-semibold text-stone-600 uppercase">DOK. KP / DOK. LATIHAN PSGC</span>
                                <span class="text-[5px] text-stone-500 italic">Skuad PSGC Siap Tampil Maksimal di Edisi Perdana</span>
                            </div>
                            <div class="col-span-3 text-justify font-serif text-[6px] leading-[1.2]">
                                <p class="indent-1"><span class="font-bold font-sans">CIAMIS, (KP).-</span> Asisten Pelatih PSGC Eka Ramdani menyambut positif hasil drawing. Menjadi panggung krusial memberi jam terbang pemain muda... <span class="italic text-[5px] text-stone-600">(Bersambung ke hal. 7)</span></p>
                            </div>
                        </div>
                    </div>
                `;
            } else if (currentSimulatorPage === 2) {
                headerContainer.innerHTML = `
                    <div class="flex items-center justify-between text-[6.5px] border-b-2 border-black pb-0.5 mb-1 font-serif">
                        <span class="font-bold italic">Kabar Priangan</span>
                        <span>${tanggalText}</span>
                        <span class="font-bold font-mono">hal. 2 Garut - Sumedang</span>
                        <span class="text-[5.5px] text-stone-500 font-sans">kabarpriangan.pikiran-rakyat.com</span>
                    </div>
                `;
                contentContainer.innerHTML = `
                    <div class="space-y-1">
                        <div class="border-b border-stone-400 pb-0.5">
                            <div class="text-[5.5px] font-sans font-bold text-kp-blue-800 uppercase tracking-wider">KABAR SINGKAT • SUMEDANG</div>
                            <div class="text-[9px] font-black uppercase text-stone-900 leading-tight font-serif">
                                Siap Catatkan Sejarah Baru: Sumedang Bakal Ujicoba Pilkades Digital Serentak
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-[5.5px] text-justify leading-tight">
                            <div class="col-span-4">
                                <p><span class="font-bold font-sans">SUMEDANG, (KP).-</span> Bupati Sumedang H. Dony Ahmad Munir menegaskan kesiapan pelaksanaan Pilkades digital serentak saat Rakor di Aula Tampomas PPS. Sistem e-voting ini perdana di Jawa Barat untuk transparansi...</p>
                            </div>
                            <div class="col-span-3 border-l border-stone-300 pl-1">
                                <div class="text-[5px] font-bold uppercase text-stone-800">GARUT UPDATE</div>
                                <p>Pemkab Garut percepat rekondisi infrastruktur akses jalan logistik perkebunan dan sentra UMKM di Garut Selatan...</p>
                            </div>
                        </div>
                    </div>
                `;
            } else if (currentSimulatorPage === 3) {
                headerContainer.innerHTML = `
                    <div class="flex items-center justify-between text-[6.5px] border-b-2 border-black pb-0.5 mb-1 font-serif">
                        <span class="font-bold italic">Kabar Priangan</span>
                        <span>${tanggalText}</span>
                        <span class="font-bold font-mono">hal. 3 Kab. Tasikmalaya</span>
                        <span class="text-[5.5px] text-stone-500 font-sans">kabarpriangan.pikiran-rakyat.com</span>
                    </div>
                `;
                contentContainer.innerHTML = `
                    <div class="space-y-1">
                        <div class="border-b border-stone-400 pb-0.5">
                            <div class="text-[5.5px] font-sans font-bold text-emerald-800 uppercase tracking-wider">KABAR SINGKAT • KAB. TASIKMALAYA</div>
                            <div class="text-[9px] font-black uppercase text-stone-900 leading-tight font-serif">
                                Pegiat Olahraga Apresiasi Komitmen Pemkab Tasikmalaya Perbaiki Stadion Kaliki
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-[5.5px] text-justify leading-tight">
                            <div class="col-span-4">
                                <p><span class="font-bold font-sans">MANGUNREJA, (KP).-</span> Mantan pelatih Persitas Nurdin bersama KONI Kabupaten Tasikmalaya mengapresiasi respons Pemkab atas aspirasi sarana olahraga yang representatif bagi atlet daerah...</p>
                            </div>
                            <div class="col-span-3 border-l border-stone-300 pl-1">
                                <div class="text-[5px] font-bold uppercase text-stone-800">SINGAPARNA</div>
                                <p>Penguatan pelayanan publik terpadu di Kompleks Perkantoran Sukapura Singaparna terus dioptimalkan...</p>
                            </div>
                        </div>
                    </div>
                `;
            } else if (currentSimulatorPage === 4) {
                headerContainer.innerHTML = `
                    <div class="flex items-center justify-between text-[6.5px] border-b-2 border-black pb-0.5 mb-1 font-serif">
                        <span class="font-bold italic">Kabar Priangan</span>
                        <span>${tanggalText}</span>
                        <span class="font-bold font-mono">hal. 4 Ciamis</span>
                        <span class="text-[5.5px] text-stone-500 font-sans">kabarpriangan.pikiran-rakyat.com</span>
                    </div>
                `;
                contentContainer.innerHTML = `
                    <div class="space-y-1">
                        <div class="border-b border-stone-400 pb-0.5">
                            <div class="text-[5.5px] font-sans font-bold text-stone-700 uppercase tracking-wider">KABAR CIAMIS &amp; LASKAR SINGACALA</div>
                            <div class="text-[9px] font-black uppercase text-stone-900 leading-tight font-serif">
                                PSGC Ciamis Bidik Kemenangan Tandang Hadapi Adhyaksa FC Bekasi
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-[5.5px] text-justify leading-tight">
                            <div class="col-span-4">
                                <p><span class="font-bold font-sans">CIAMIS, (KP).-</span> Skuad PSGC Ciamis mengusung target penuh di Stadion Patriot Candrabhaga sebagai persembahan spesial bagi CEO Herdiat Sunarya...</p>
                            </div>
                            <div class="col-span-3 bg-stone-100 border border-stone-400 p-1 text-[5px] leading-tight font-sans">
                                <div class="font-bold text-center border-b border-stone-300 pb-0.5 uppercase">BOX TARIF IKLAN RESMI KP</div>
                                <div class="mt-0.5 space-y-0.5">
                                    <div>• <b>Baris:</b> Rp 22.750 / baris</div>
                                    <div>• <b>Kolom:</b> Rp 13.000 / mmk</div>
                                    <div>• <b>Advertorial FC:</b> Rp 25 Jt/hal</div>
                                    <div class="text-[4.5px] text-stone-500 pt-0.5">Kantor: Jl. Dr. Soekardjo 70 Tsk (0265) 335300</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else if (currentSimulatorPage === 5) {
                headerContainer.innerHTML = `
                    <div class="flex items-center justify-between text-[6.5px] border-b-2 border-black pb-0.5 mb-1 font-serif">
                        <span class="font-bold italic">Kabar Priangan</span>
                        <span>${tanggalText}</span>
                        <span class="font-bold font-mono">hal. 5 Banjar - Pangandaran</span>
                        <span class="text-[5.5px] text-stone-500 font-sans">kabarpriangan.pikiran-rakyat.com</span>
                    </div>
                `;
                contentContainer.innerHTML = `
                    <div class="space-y-1">
                        <div class="border-b border-stone-400 pb-0.5">
                            <div class="text-[5.5px] font-sans font-bold text-stone-700 uppercase tracking-wider">KABAR SINGKAT • BANJAR &amp; PANGANDARAN</div>
                            <div class="text-[9px] font-black uppercase text-stone-900 leading-tight font-serif">
                                Tiga Posisi Strategis di Lingkungan Polres Banjar Resmi Berganti
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-[5.5px] text-justify leading-tight">
                            <div class="col-span-4">
                                <p><span class="font-bold font-sans">BANJAR, (KP).-</span> Kapolres Banjar AKBP Didi Dewantoro memimpin upacara sertijab Wakapolres, Kasatres Narkoba, dan Kapolsek Langensari di Lapang Apel Polres Banjar...</p>
                            </div>
                            <div class="col-span-3 border-l border-stone-300 pl-1">
                                <div class="text-[5px] font-bold uppercase text-stone-800">PANGANDARAN</div>
                                <p>Balawista Pangandaran siagakan rambu keselamatan pantai jelang peningkatan okupansi hotel akhir pekan...</p>
                            </div>
                        </div>
                    </div>
                `;
            } else if (currentSimulatorPage === 6) {
                headerContainer.innerHTML = `
                    <div class="flex items-center justify-between text-[6.5px] border-b-2 border-black pb-0.5 mb-1 font-serif">
                        <span class="font-bold italic">Kabar Priangan</span>
                        <span>${tanggalText}</span>
                        <span class="font-bold font-mono">hal. 6 Kota Tasikmalaya</span>
                        <span class="text-[5.5px] text-stone-500 font-sans">kabarpriangan.pikiran-rakyat.com</span>
                    </div>
                `;
                contentContainer.innerHTML = `
                    <div class="space-y-1">
                        <div class="border-b border-stone-400 pb-0.5">
                            <div class="text-[5.5px] font-sans font-bold text-amber-800 uppercase tracking-wider">KABAR SINGKAT • KOTA TASIKMALAYA</div>
                            <div class="text-[9px] font-black uppercase text-stone-900 leading-tight font-serif">
                                Sidak DPRD Kota Tasikmalaya: 19 Siswa SDN Galunggung Tolak Makanan MBG
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-[5.5px] text-justify leading-tight">
                            <div class="col-span-4">
                                <p><span class="font-bold font-sans">KOTA TASIK, (KP).-</span> Anggota DPRD Kota Tasikmalaya Kepler Sianturi melakukan sidak ke SDN Galunggung dan menemukan separuh siswa kelas menolak menu MBG. Diusulkan orang tua dilibatkan...</p>
                            </div>
                            <div class="col-span-3 border-l border-stone-300 pl-1">
                                <div class="text-[5px] font-bold uppercase text-stone-800">HZ MUSTOFA</div>
                                <p>Dishub Kota Tasikmalaya tertibkan juru parkir liar di sepanjang koridor semi pedestrian HZ Mustofa...</p>
                            </div>
                        </div>
                    </div>
                `;
            } else if (currentSimulatorPage === 7) {
                headerContainer.innerHTML = `
                    <div class="flex items-center justify-between text-[6.5px] border-b border-black pb-0.5 mb-0.5 font-serif">
                        <span class="font-bold italic">Kabar Priangan</span>
                        <span>${tanggalText}</span>
                        <span class="font-bold font-mono">hal. 7 Kabar Priangan</span>
                        <span class="font-bold uppercase tracking-wider text-[6px]">POLITIK &amp; RAGAM IKLAN</span>
                    </div>
                    <div class="bg-black text-white text-center py-0.5 text-[8px] font-black tracking-widest font-sans uppercase">
                        &gt;&gt;&gt; R A G A M &nbsp; I K L A N &lt;&lt;&lt;
                    </div>
                    <div class="flex justify-between text-[5.5px] font-bold text-stone-700 border-b border-stone-400 py-0.5 uppercase font-sans">
                        <span>KEHILANGAN</span><span>OTOBURSA</span><span>PROPERTI</span><span>JASA USAHA</span><span>LOWONGAN</span>
                    </div>
                `;
                contentContainer.innerHTML = `
                    <div class="space-y-1">
                        <div class="grid grid-cols-7 gap-1 text-[5.5px]">
                            <div class="col-span-2 border border-stone-800 p-0.5 bg-white text-center">
                                <div class="font-bold uppercase bg-stone-900 text-white text-[5.5px]">KEHILANGAN STNK</div>
                                <div class="text-[4.5px] mt-0.5 font-sans leading-tight">Hilang STNK Spd Mtr Honda Beat Z-4821-KM an. Yadi S. d/a Indihiang Kota Tsk.</div>
                            </div>
                            <div class="col-span-2 border border-stone-800 p-0.5 bg-white text-center">
                                <div class="font-bold uppercase bg-stone-900 text-white text-[5.5px]">DCM COMPUTER</div>
                                <div class="text-[4.5px] mt-0.5 font-sans leading-tight">Servis Laptop, Printer &amp; Jaringan Tasikmalaya Garansi Cepat.</div>
                            </div>
                            <div class="col-span-3 border border-stone-800 p-0.5 bg-white text-center">
                                <div class="font-bold uppercase bg-stone-900 text-white text-[5.5px]">TERUS JAYA MOTOR</div>
                                <div class="text-[4.5px] mt-0.5 font-sans leading-tight">Jual Beli Mobil Bekas Cash &amp; Kredit Tasikmalaya. Hub: 0812-2284-xxxx</div>
                            </div>
                        </div>
                    </div>
                `;
            } else if (currentSimulatorPage === 8) {
                headerContainer.innerHTML = `
                    <div class="flex items-center justify-between text-[6.5px] border-b-2 border-black pb-0.5 mb-1 font-serif">
                        <span class="font-bold italic">Kabar Priangan</span>
                        <span>${tanggalText}</span>
                        <span class="font-bold font-mono">hal. 8 DPRD Kab. Tasikmalaya</span>
                        <span class="font-bold uppercase tracking-wider text-[6px] text-amber-700">BACK COVER / ADVERTORIAL</span>
                    </div>
                `;
                contentContainer.innerHTML = `
                    <div class="space-y-1">
                        <div class="border-b border-stone-400 pb-0.5">
                            <div class="text-[5.5px] font-sans font-bold text-kp-blue-900 uppercase tracking-wider">PARIPURNA DEWAN • LIPUTAN KHUSUS ADVERTORIAL</div>
                            <div class="text-[9px] font-black uppercase text-stone-900 leading-tight font-serif">
                                DPRD dan Pemkab Tasikmalaya Sepakati Penetapan Propemperda &amp; Nota Keuangan
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-[5.5px] text-justify leading-tight">
                            <div class="col-span-4">
                                <p><span class="font-bold font-sans">SINGAPARNA, (KP).-</span> Rapat Paripurna Pertama menyepakati penetapan perubahan Propemperda 2026 dilanjutkan penyampaian penjelasan Bupati Tasikmalaya terkait Nota Pengantar Keuangan...</p>
                            </div>
                            <div class="col-span-3 bg-stone-200 border border-stone-400 p-1 flex flex-col items-center justify-center text-center">
                                <svg class="w-4 h-4 text-stone-400 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span class="text-[5px] font-bold uppercase text-stone-700">DOK. SEKRETARIAT DPRD</span>
                                <span class="text-[4.5px] text-stone-500 italic">Pimpinan Dewan Bersama Bupati</span>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        function updateSimulator() {
            const box = document.getElementById('simulasiIklanBox');
            const wrapper = document.getElementById('simulasiIklanBoxWrapper');
            const innerContent = document.getElementById('adSlotInnerContent');
            const summary = document.getElementById('simulatorUkuranSummary');
            const metricReal = document.getElementById('simulatorMetricReal');
            const badgeWarna = document.getElementById('simulasiBadgeWarna');
            const dimensiBadge = document.getElementById('simulasiDimensiBadge');
            const cmykStrip = document.getElementById('simulasiCmykStrip');
            const barisTextContainer = document.getElementById('simulasiBarisTextContainer');

            if (!box) return;

            if (!currentProduct) {
                box.classList.add('hidden');
                if (summary) summary.textContent = "Belum Ada Produk Dipilih";
                return;
            }
            box.classList.remove('hidden');

            const isFC = (currentProduct.warna === 'FC');
            if (badgeWarna) {
                badgeWarna.textContent = isFC ? 'FC' : 'BW';
                badgeWarna.className = `px-1 rounded text-[6.5px] font-mono font-bold ${isFC ? 'bg-rose-600 text-white' : 'bg-stone-800 text-white'}`;
            }
            
            if (cmykStrip) {
                if (isFC) {
                    cmykStrip.classList.remove('hidden');
                    cmykStrip.classList.add('grid');
                } else {
                    cmykStrip.classList.add('hidden');
                    cmykStrip.classList.remove('grid');
                }
            }

            // Customer Name & Phone
            const rawClientName = document.getElementById('inputManualNamaPemasang')?.value || 
                                  document.getElementById('nama_pemasangkoran')?.value || 
                                  'Pemasang Iklan';
            const clientName = rawClientName.trim().length > 0 ? rawClientName : 'Pemasang Iklan';
            const clientPhone = document.getElementById('telepon_pemasangkoran')?.value || '';
            const rubrikSelect = document.getElementById('kop_iklan_id');
            const rubrikText = rubrikSelect ? rubrikSelect.options[rubrikSelect.selectedIndex]?.text : 'IKLAN KORAN';

            // Position Handling (Excel 2-Axis Alignment + Kuping)
            if (wrapper) {
                let vClass = "mt-auto mb-0";
                let hClass = "justify-end";
                let orderClass = "order-last";

                if (isSlotKuping) {
                    vClass = "mt-0 mb-auto";
                    hClass = "justify-end";
                    orderClass = "order-first";
                } else {
                    if (slotVertical === 'top') {
                        vClass = "mt-0 mb-auto";
                        orderClass = "order-first";
                    } else if (slotVertical === 'middle') {
                        vClass = "my-auto";
                        orderClass = "";
                    } else {
                        vClass = "mt-auto mb-0";
                        orderClass = "order-last";
                    }

                    if (slotHorizontal === 'left') {
                        hClass = "justify-start";
                    } else if (slotHorizontal === 'center') {
                        hClass = "justify-center";
                    } else {
                        hClass = "justify-end";
                    }
                }

                wrapper.className = `relative z-30 w-full flex ${hClass} ${vClass} ${orderClass} transition-all duration-300`;
            }

            if (currentProduct.tipe === 'BARIS') {
                const baris = parseInt(document.getElementById('jumlah_baris')?.value) || 2;
                box.style.width = '28.5%';
                const calcHeight = Math.max(50, baris * 14 + 20);
                box.style.height = `${calcHeight}px`;

                if (dimensiBadge) dimensiBadge.textContent = `${baris} Baris (1 Kol)`;
                if (summary) summary.textContent = `${baris} Baris Teks (1 Kolom Broadsheet)`;
                if (metricReal) metricReal.textContent = `Lebar ±46 mm × Tinggi ±${baris * 4} mm • ${isFC ? 'Warna (FC)' : 'Hitam Putih (BW)'}`;

                if (barisTextContainer) barisTextContainer.classList.add('hidden');

                const activeLines = barisLines.filter(l => l.trim().length > 0);
                let linesHtml = '';
                if (activeLines.length > 0) {
                    linesHtml = activeLines.slice(0, 4).map((l, idx) => 
                        `<div class="truncate text-[6.5px] leading-tight font-serif text-stone-900">${idx+1}. ${l}</div>`
                    ).join('');
                    if (activeLines.length > 4) {
                        linesHtml += `<div class="text-[5.5px] text-stone-400 italic font-sans">+ ${activeLines.length - 4} baris lainnya...</div>`;
                    }
                } else {
                    linesHtml = `<div class="text-[6.5px] text-stone-400 italic">Ketik naskah baris di formulir...</div>`;
                }

                if (innerContent) {
                    innerContent.innerHTML = `
                        <div class="w-full h-full p-1 flex flex-col justify-between text-left bg-[#fffdf9] border border-stone-400">
                            <div class="font-bold text-[7px] uppercase font-serif text-stone-900 border-b border-stone-200 pb-0.5">
                                ${currentProduct.nama || 'IKLAN BARIS'}
                            </div>
                            <div class="my-auto space-y-0.5">
                                ${linesHtml}
                            </div>
                            <div class="text-[6px] font-mono font-bold text-stone-800 border-t border-dashed border-stone-300 pt-0.5">
                                ${clientPhone ? 'Telp: ' + clientPhone : 'KP Iklan Baris'}
                            </div>
                        </div>
                    `;
                }

            } else {
                if (barisTextContainer) barisTextContainer.classList.add('hidden');

                const kol = parseInt(document.getElementById('lebar_kolom')?.value) || 2;
                const mm = parseInt(document.getElementById('tinggi_mm')?.value) || 100;

                const pctWidth = Math.min(100, Math.max(14.28, (kol / 7) * 100));
                const pxHeight = Math.min(260, Math.max(40, (mm / 540) * 260));

                box.style.width = `${pctWidth}%`;
                box.style.height = `${pxHeight}px`;

                if (dimensiBadge) dimensiBadge.textContent = `${kol} Kol × ${mm} mm`;
                if (summary) summary.textContent = `${kol} Kolom × ${mm} mm (${kol * mm} mmk)`;

                const lebarMmReal = kol * 46 + (kol - 1) * 3;
                if (metricReal) metricReal.textContent = `Lebar ±${lebarMmReal} mm × Tinggi ${mm} mm • ${isFC ? 'Warna (FC)' : 'Hitam Putih (BW)'}`;

                if (innerContent) {
                    if (uploadedAdImageSrc) {
                        innerContent.innerHTML = `
                            <div class="w-full h-full p-0.5 flex items-center justify-center bg-stone-100 overflow-hidden">
                                <img src="${uploadedAdImageSrc}" 
                                     alt="Desain Iklan" 
                                     class="w-full h-full object-contain ${!isFC ? 'filter grayscale contrast-125' : ''}" />
                            </div>
                        `;
                    } else {
                        innerContent.innerHTML = `
                            <div class="w-full h-full p-1.5 flex flex-col justify-between items-center text-center bg-gradient-to-b from-[#faf8f5] to-[#f0ece1] ${!isFC ? 'filter grayscale contrast-125' : ''}">
                                <div class="w-full border-b border-stone-300 pb-0.5 text-[6.5px] font-bold tracking-wider text-kp-blue-900 uppercase font-sans">
                                    ${rubrikText}
                                </div>
                                <div class="my-auto py-1">
                                    <div class="text-[8.5px] font-black text-stone-900 uppercase tracking-tight leading-tight font-serif">
                                        ${clientName}
                                    </div>
                                    <div class="text-[6px] text-stone-600 italic font-serif">
                                        Harian Umum Kabar Priangan
                                    </div>
                                </div>
                                <div class="w-full border-t border-stone-300 pt-0.5 text-[6px] font-mono text-stone-700 flex justify-between items-center">
                                    <span class="font-bold">${kol}k × ${mm}mm</span>
                                    <span class="font-bold">${clientPhone || '08xx-xxxx-xxxx'}</span>
                                </div>
                            </div>
                        `;
                    }
                }
            }
        }

        // ==============================================================
        // SEKSI 4: KALENDER NASIONAL & JADWAL TERBIT INTERAKTIF (STANDAR INDUSTRI)
        // Terhubung ke API Libur Nasional & Hari Peringatan Nasional (Pattern KKN n-desapresence)
        // ==============================================================
        const monthNamesIndo = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const dayNamesIndo = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        const dayShortIndo = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];
        const monthShortIndo = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];

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

        function goToTodayCalendar() {
            const today = new Date();
            currentCalMonth = today.getMonth();
            currentCalYear = today.getFullYear();
            const todayStr = `${currentCalYear}-${String(currentCalMonth + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
            activeDetailDate = todayStr;

            fetchKalenderBulan(currentCalYear, currentCalMonth + 1, () => {
                renderCalendar();
                updatePanelInfoTanggal(todayStr);
            });
        }

        function changeCalendarMonth(delta) {
            currentCalMonth += delta;
            if (currentCalMonth > 11) {
                currentCalMonth = 0;
                currentCalYear++;
            }
            if (currentCalMonth < 0) {
                currentCalMonth = 11;
                currentCalYear--;
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
            if (grid) {
                grid.classList.add('opacity-40', 'pointer-events-none');
            }

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
                if (grid) {
                    grid.classList.remove('opacity-40', 'pointer-events-none');
                }
                if (callback) callback();
            });
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

            const firstDayOfWeek = new Date(currentCalYear, currentCalMonth, 1).getDay(); // 0 = Minggu
            const daysInMonth = new Date(currentCalYear, currentCalMonth + 1, 0).getDate();

            // Sel kosong sebelum tanggal 1 (Kompak seperti semula)
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

                // Tooltip info saat hover
                let tooltip = `${formatIndoDate(dateStr, true)}`;
                if (liburDesc) tooltip += ` | Libur: ${liburDesc}`;
                if (peringatanDesc) tooltip += ` | Peringatan: ${peringatanDesc}`;
                if (isSelected) tooltip += ` [Terbit Koran Terjadwal]`;
                btn.title = tooltip;

                // Dynamic Styling Sel Kalender Kompak (Persegi / Kotak Tanggal)
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

                if (isActiveDetail && !isSelected) {
                    btnClass += "ring-2 ring-kp-blue-500/80 ";
                }

                if (isToday && !isSelected) {
                    btnClass += "ring-1 ring-slate-400 font-bold ";
                }

                btn.className = btnClass;

                // Sel Kalender: Angka tanggal kotak bersih tanpa titik lingkaran
                btn.innerHTML = `
                    <span class="leading-none text-xs font-semibold ${isSunday && !isSelected && !liburDesc ? 'text-rose-600' : ''}">${day}</span>
                `;

                btn.onclick = () => onCalendarCellClick(dateStr, isPast);
                grid.appendChild(btn);
            }
        }

        // ==============================================================
        // LOGIKA KLIK TANGGAL: DETAIL PANEL & TOGGLE KUOTA PEMUATAN
        // ==============================================================
        function onCalendarCellClick(dateStr, isPast) {
            activeDetailDate = dateStr;
            updatePanelInfoTanggal(dateStr);

            if (isPast) {
                showCustomToast(
                    `Tanggal ${formatIndoDate(dateStr, false)} sudah lewat batas edisi cetak. Anda dapat melihat informasi agenda pada tanggal ini.`,
                    'info',
                    'Edisi Sudah Lewat'
                );
                renderCalendar();
                return;
            }

            const totalMuatInput = document.getElementById('total_muatkoran');
            const targetQuota = parseInt(totalMuatInput.value) || 1;
            const index = selectedDates.indexOf(dateStr);

            if (index > -1) {
                // Unselect tanggal terpilih
                selectedDates.splice(index, 1);
                selectedDates.sort();
                syncTanggalMuat();
                updatePanelInfoTanggal(dateStr);
            } else {
                // Cek apakah jumlah pemuatan masih mencukupi
                if (selectedDates.length >= targetQuota) {
                    showCustomToast(
                        `Seluruh ${targetQuota} tanggal edisi telah terisi. Tambah angka pada "Frekuensi Terbit" jika ingin memilih tanggal lagi.`,
                        'warning',
                        'Batas Jadwal Terpenuhi'
                    );

                    // Feedback visual pada box counter khas Kabar Priangan
                    totalMuatInput.parentElement.classList.add('ring-2', 'ring-kp-blue-500', 'border-kp-blue-500');
                    setTimeout(() => {
                        totalMuatInput.parentElement.classList.remove('ring-2', 'ring-kp-blue-500', 'border-kp-blue-500');
                    }, 1200);
                    renderCalendar();
                    return;
                }

                // Masukkan tanggal ke jadwal terbit
                selectedDates.push(dateStr);
                selectedDates.sort();
                syncTanggalMuat();
                updatePanelInfoTanggal(dateStr);
            }
        }

        // ==============================================================
        // PANEL INFORMASI TANGGAL: HANYA HARI LIBUR & PERINGATAN
        // ==============================================================
        function updatePanelInfoTanggal(dateStr) {
            if (!dateStr) return;

            const panel = document.getElementById('panelInfoTanggal');
            const labelTanggal = document.getElementById('infoTanggalLabel');
            const badgeContainer = document.getElementById('infoTanggalBadgeContainer');

            if (!panel || !labelTanggal || !badgeContainer) return;

            labelTanggal.textContent = formatIndoDate(dateStr, true);

            // Cari data libur / peringatan dari cache kalender
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

            const isSelectedThisDate = selectedDates.includes(dateStr);
            let selectedTagHtml = '';
            if (isSelectedThisDate) {
                selectedTagHtml = `
                    <span class="inline-flex items-center text-xs font-bold px-2 py-0.5 rounded-md bg-kp-blue-600 text-white shadow-2xs">
                        Terjadwal Terbit
                    </span>
                `;
            }

            let badgesHtml = '';

            if (liburDesc && peringatanDesc) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-rose-50/80 border-rose-200 text-rose-900 space-y-1.5";
                badgesHtml = `
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-rose-200 text-rose-700 shadow-2xs">
                            Libur Nasional: ${liburDesc}
                        </span>
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-amber-200 text-amber-800 shadow-2xs">
                            Peringatan: ${peringatanDesc}
                        </span>
                        ${selectedTagHtml}
                    </div>
                `;
            } else if (liburDesc) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-rose-50/80 border-rose-200 text-rose-900 space-y-1.5";
                badgesHtml = `
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-rose-200 text-rose-700 shadow-2xs">
                            Libur Nasional: ${liburDesc}
                        </span>
                        ${selectedTagHtml}
                    </div>
                `;
            } else if (peringatanDesc) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-amber-50/80 border-amber-200 text-amber-900 space-y-1.5";
                badgesHtml = `
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-amber-200 text-amber-800 shadow-2xs">
                            Peringatan: ${peringatanDesc}
                        </span>
                        ${selectedTagHtml}
                    </div>
                `;
            } else if (isSunday) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-rose-50/70 border-rose-200 text-rose-900 space-y-1.5";
                badgesHtml = `
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-rose-200 text-rose-700 shadow-2xs">
                            Edisi Akhir Pekan (Minggu)
                        </span>
                        ${selectedTagHtml}
                    </div>
                `;
            } else if (isSaturday) {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-slate-50 border-slate-200 text-slate-800 space-y-1.5";
                badgesHtml = `
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-slate-200 text-slate-700 shadow-2xs">
                            Edisi Akhir Pekan (Sabtu)
                        </span>
                        ${selectedTagHtml}
                    </div>
                `;
            } else {
                panel.className = "p-2.5 rounded-xl border transition-all text-xs bg-sky-50/60 border-sky-200 text-sky-900 space-y-1.5";
                badgesHtml = `
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-sky-200 text-sky-800 shadow-2xs">
                            Edisi Reguler (Hari Kerja)
                        </span>
                        ${selectedTagHtml}
                    </div>
                `;
            }

            badgeContainer.innerHTML = badgesHtml;
        }

        // ==============================================================
        // SINKRONISASI DAFTAR TANGGAL CHIPS & FORM SUBMIT
        // ==============================================================
        function renderSelectedDatesChips() {
            const container = document.getElementById('daftarTanggalChips');
            if (!container) return;
            container.innerHTML = '';

            const totalMuatInput = document.getElementById('total_muatkoran');
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
                    const isActive = (typeof activeDetailDate !== 'undefined' && d === activeDetailDate);
                    chip.className = `w-full flex items-center justify-between pl-3 pr-2 py-1.5 rounded-xl transition shadow-2xs group overflow-hidden ${
                        isActive 
                            ? 'bg-sky-50/50 border border-sky-400' 
                            : 'bg-white border border-slate-200 hover:border-kp-blue-300'
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

            // Tampilkan Slot Kosong jika kuota belum terpenuhi (Simpel, Bersih, Anti-Numpuk)
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
            if (labelTotal) {
                labelTotal.textContent = `${selectedDates.length} Tanggal`;
            }
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
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowStr = tomorrow.toISOString().split('T')[0];
            
            selectedDates = [tomorrowStr];
            document.getElementById('total_muatkoran').value = 1;
            activeDetailDate = tomorrowStr;
            
            syncTanggalMuat();
            updatePanelInfoTanggal(tomorrowStr);
            showCustomToast('Pilihan tanggal telah di-reset ke 1 kali terbit (edisi besok).', 'info');
        }

        function syncTanggalMuat() {
            document.getElementById('tanggal_muat_list').value = selectedDates.join(',');
            if (selectedDates.length > 0) {
                document.getElementById('tanggal_muatkoran').value = selectedDates[0];
            }
            renderCalendar();
            renderSelectedDatesChips();
            validateParitasTanggal();
            recalculateFinancials();
        }

        function handleJumlahMuatInput(val) {
            const clean = val.replace(/[^0-9]/g, '');
            if (val !== clean) {
                document.getElementById('total_muatkoran').value = clean;
            }

            if (clean === '' || parseInt(clean) === 0) {
                validateParitasTanggal();
                renderSelectedDatesChips();
                return;
            }

            let total = parseInt(clean);
            if (total > 60) {
                total = 60;
                document.getElementById('total_muatkoran').value = 60;
            }

            // Jika kuota dikurangi di bawah jumlah tanggal terpilih, pangkas ke kuota baru
            if (selectedDates.length > total) {
                selectedDates = selectedDates.slice(0, total);
                renderCalendar();
            }

            renderSelectedDatesChips();
            validateParitasTanggal();
            recalculateFinancials();
        }

        function handleJumlahMuatBlur(input) {
            const val = input.value.trim();
            if (!val || parseInt(val) < 1) {
                input.value = Math.max(1, selectedDates.length);
            }
            validateParitasTanggal();
            renderSelectedDatesChips();
            recalculateFinancials();
        }

        function stepJumlahMuat(delta) {
            let cur = parseInt(document.getElementById('total_muatkoran').value) || 1;
            cur += delta;
            if (cur < 1) cur = 1;
            if (cur > 60) cur = 60;
            document.getElementById('total_muatkoran').value = cur;

            // Jika berkurang di bawah jumlah tanggal yang sudah ada, truncate ke jumlah baru
            if (selectedDates.length > cur) {
                selectedDates = selectedDates.slice(0, cur);
                renderCalendar();
            }

            renderSelectedDatesChips();
            validateParitasTanggal();
            recalculateFinancials();
        }

        function validateParitasTanggal() {
            const rawVal = document.getElementById('total_muatkoran').value.trim();
            const badge = document.getElementById('statusParitasTanggal');
            const count = selectedDates.length;

            if (rawVal === '' || parseInt(rawVal) === 0) {
                badge.className = "text-xs font-semibold px-2.5 py-1 rounded-lg border bg-amber-50 text-amber-800 border-amber-200 inline-flex items-center gap-1.5 shadow-2xs";
                badge.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Isi Frekuensi</span>
                `;
                return;
            }

            const total = parseInt(rawVal);

            if (count === total) {
                badge.className = "text-xs font-semibold px-2.5 py-1 rounded-lg border bg-emerald-50 text-emerald-800 border-emerald-200 inline-flex items-center gap-1.5 shadow-2xs";
                badge.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>${count} Tanggal Dipilih</span>
                `;
            } else if (count < total) {
                badge.className = "text-xs font-semibold px-2.5 py-1 rounded-lg border bg-sky-50 text-sky-800 border-sky-200 inline-flex items-center gap-1.5 shadow-2xs";
                badge.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Kurang ${total - count} Tanggal</span>
                `;
            } else {
                badge.className = "text-xs font-semibold px-2.5 py-1 rounded-lg border bg-rose-50 text-rose-800 border-rose-200 inline-flex items-center gap-1.5 shadow-2xs";
                badge.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Lebih ${count - total} Tanggal</span>
                `;
            }
        }

        // ==============================================================
        // KALKULASI FINANSIAL REAL-TIME (TRANSPARAN)
        // ==============================================================
        function resetStrukPreviewToEmpty() {
            const elJudul = document.getElementById('strukProdukJudul');
            if (elJudul) elJudul.textContent = "Belum Ada Produk Dipilih";
            const elTarif = document.getElementById('strukTarifDasar');
            if (elTarif) elTarif.textContent = "-";
            const elDimensi = document.getElementById('strukDimensi');
            if (elDimensi) elDimensi.textContent = "-";
            const frekuensi = (typeof selectedDates !== 'undefined' && selectedDates.length) ? selectedDates.length : 1;
            const elFrek = document.getElementById('strukFrekuensi');
            if (elFrek) elFrek.textContent = `${frekuensi} Kali Terbit`;
            const elBruto = document.getElementById('strukBruto');
            if (elBruto) elBruto.textContent = "Rp 0";
            const elDpp = document.getElementById('strukDpp');
            if (elDpp) elDpp.textContent = "Rp 0";
            const elPpn = document.getElementById('strukPpn');
            if (elPpn) elPpn.textContent = "Rp 0";
            const elNetto = document.getElementById('strukTotalNetto');
            if (elNetto) elNetto.textContent = "Rp 0";

            const hrg = document.getElementById('harga_transaksikoran');
            if (hrg) hrg.value = "0";

            const inBayar = document.getElementById('in_bayar');
            if (inBayar && (!inBayar.value || inBayar.value === '0')) inBayar.value = "";

            const rowMaterai = document.getElementById('rowMaterai');
            if (rowMaterai) rowMaterai.classList.add('hidden');
            const elPiutang = document.getElementById('strukPiutang') || document.getElementById('strukSisaBayar');
            if (elPiutang) elPiutang.textContent = "Rp 0";

            const box = document.getElementById('simulasiIklanBox');
            if (box) box.classList.add('hidden');
            const summary = document.getElementById('simulatorUkuranSummary');
            if (summary) summary.textContent = "Belum Ada Produk Dipilih";

            const placeholderEl = document.getElementById('placeholderPilihProduk');
            if (placeholderEl) placeholderEl.classList.remove('hidden');
            const secBaris = document.getElementById('sectionIklanBaris');
            if (secBaris) secBaris.classList.add('hidden');
            const secDisplay = document.getElementById('sectionIklanDisplay');
            if (secDisplay) secDisplay.classList.add('hidden');
            const contKopIklan = document.getElementById('containerKopIklan');
            if (contKopIklan) contKopIklan.classList.add('hidden');

            const inPersen = document.getElementById('diskon_persen');
            if (inPersen) inPersen.value = '0';
            const inDisp = document.getElementById('diskon_khusus_display');
            if (inDisp) inDisp.value = '0';
            const inKhusus = document.getElementById('diskon_khusus');
            if (inKhusus) inKhusus.value = '0';
            const dInfo = document.getElementById('strukDiskonInfo');
            if (dInfo) dInfo.textContent = '- Rp 0';
        }

        function recalculateFinancials(syncFromPersen = true) {
            if (!currentProduct) {
                resetStrukPreviewToEmpty();
                return;
            }

            const frekuensi = parseInt(document.getElementById('total_muatkoran').value) || 1;
            let brutoPerMuat = 0;
            let dimensiLabel = '';
            let tarifLabel = '';

            if (currentProduct.caraHitung === 'PER_BARIS') {
                const baris = parseInt(document.getElementById('jumlah_baris').value) || 2;
                brutoPerMuat = baris * currentProduct.tarif;
                dimensiLabel = `${baris} Baris`;
                tarifLabel = `Rp ${currentProduct.tarif.toLocaleString('id-ID')} / baris`;
            } else if (currentProduct.caraHitung === 'PER_MMK') {
                const kol = parseInt(document.getElementById('lebar_kolom').value) || 2;
                const mm = parseInt(document.getElementById('tinggi_mm').value) || 100;
                const mmk = kol * mm;
                brutoPerMuat = mmk * currentProduct.tarif;
                dimensiLabel = `${mmk.toLocaleString('id-ID')} mmk (${kol} kol × ${mm} mm)`;
                tarifLabel = `Rp ${currentProduct.tarif.toLocaleString('id-ID')} / mmk`;
            } else if (currentProduct.caraHitung === 'FIXED_ZERO') {
                brutoPerMuat = 0;
                dimensiLabel = 'Paket Barter/Bonus';
                tarifLabel = 'Rp 0';
            } else {
                // PAKET FIXED
                const kol = parseInt(document.getElementById('lebar_kolom').value) || currentProduct.kolom || 1;
                const mm = parseInt(document.getElementById('tinggi_mm').value) || currentProduct.tinggi || 50;
                const chk = document.getElementById('checkUkuranManual');
                const isManual = chk && chk.checked;
                const defK = currentProduct.defaultKolom || currentProduct.kolom || 1;
                const defM = currentProduct.defaultTinggi || currentProduct.tinggi || 40;

                if (isManual && (kol !== defK || mm !== defM)) {
                    // Jika user sengaja kustomisasi dimensi paket, hitung berdasarkan tarif standar MMK
                    const tarifMmkFallback = (currentProduct.warna === 'FC' ? 24000 : 12000);
                    brutoPerMuat = kol * mm * tarifMmkFallback;
                    dimensiLabel = `Kustom ${kol} kol × ${mm} mm (${kol * mm} mmk)`;
                    tarifLabel = `Rp ${tarifMmkFallback.toLocaleString('id-ID')} / mmk (Tarif Kustom)`;
                } else {
                    brutoPerMuat = currentProduct.tarif;
                    dimensiLabel = `Paket ${kol} kol × ${mm} mm (${kol * mm} mmk)`;
                    tarifLabel = `Rp ${currentProduct.tarif.toLocaleString('id-ID')}`;
                }
            }

            // Surcharge Penempatan Halaman (misal: Cover Hal 1 +50%, Back Cover Hal 8 +25%)
            const kopKotaSelect = document.getElementById('kop_kota_id');
            let surchargePersen = 0;
            if (kopKotaSelect && kopKotaSelect.selectedIndex >= 0) {
                surchargePersen = parseFloat(kopKotaSelect.options[kopKotaSelect.selectedIndex].dataset.surcharge) || 0;
            }
            if (surchargePersen > 0) {
                brutoPerMuat = Math.round(brutoPerMuat * (1 + (surchargePersen / 100)));
                tarifLabel += ` (+${surchargePersen}% Hal. Khusus)`;
            }

            const totalBruto = brutoPerMuat * frekuensi;
            document.getElementById('harga_transaksikoran').value = brutoPerMuat;

            // Sinkronisasi Diskon % ke Rp jika dimensi/produk berubah
            if (syncFromPersen) {
                updateDiskonFromBruto(totalBruto);
            }

            // Potongan Diskon
            const totalDiskon = parseFloat(document.getElementById('diskon_khusus')?.value) || 0;
            const persenAktif = parseFloat(document.getElementById('diskon_persen')?.value) || 0;
            const diskonInfo = document.getElementById('strukDiskonInfo');
            if (diskonInfo) {
                if (totalDiskon > 0) {
                    diskonInfo.textContent = `- Rp ${totalDiskon.toLocaleString('id-ID')}${persenAktif > 0 ? ' (' + persenAktif + '%)' : ''}`;
                } else {
                    diskonInfo.textContent = '- Rp 0';
                }
            }

            const dppBersih = Math.max(0, totalBruto - totalDiskon);
            const ppn = Math.round(dppBersih * (configPpnPersen / 100));

            // Materai Logic (> threshold)
            let materai = 0;
            const rowMaterai = document.getElementById('rowMaterai');
            if (dppBersih >= configThresholdMaterai) {
                materai = configNominalMaterai;
                rowMaterai.classList.remove('hidden');
            } else {
                rowMaterai.classList.add('hidden');
            }
            document.getElementById('inputMaterai').value = materai;

            const grandTotal = dppBersih + ppn + materai;

            // Update Struk Labels
            document.getElementById('strukTarifDasar').textContent = tarifLabel;
            document.getElementById('strukDimensi').textContent = dimensiLabel;
            document.getElementById('strukFrekuensi').textContent = `${frekuensi} Kali Terbit`;
            document.getElementById('strukBruto').textContent = `Rp ${totalBruto.toLocaleString('id-ID')}`;
            document.getElementById('strukDpp').textContent = `Rp ${dppBersih.toLocaleString('id-ID')}`;
            document.getElementById('strukPpn').textContent = `Rp ${ppn.toLocaleString('id-ID')}`;
            document.getElementById('strukTotalNetto').textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;

            // Sinkronisasi Komisi & Insentif Fleksibel
            updateInsentifFromDpp(dppBersih);
            updateKomisiFromDpp(dppBersih);

            handleBayarInput(grandTotal);
        }

        function handleDiskonPersenChange(val) {
            const brutoStr = document.getElementById('strukBruto')?.textContent || '0';
            const totalBruto = parseCurrency(brutoStr);
            const persen = parseFloat(val) || 0;
            const nominal = Math.round((totalBruto * persen) / 100);

            const inputDisplay = document.getElementById('diskon_khusus_display');
            const inputHidden = document.getElementById('diskon_khusus');
            if (inputDisplay) inputDisplay.value = nominal > 0 ? nominal.toLocaleString('id-ID') : '0';
            if (inputHidden) inputHidden.value = nominal;

            recalculateFinancials(false);
        }

        function handleDiskonNominalInput(el) {
            if (typeof window.formatRupiahInput === 'function') {
                window.formatRupiahInput(el);
            }
            const raw = parseCurrency(el.value);
            const inputHidden = document.getElementById('diskon_khusus');
            if (inputHidden) inputHidden.value = raw;

            const brutoStr = document.getElementById('strukBruto')?.textContent || '0';
            const totalBruto = parseCurrency(brutoStr);
            const inputPersen = document.getElementById('diskon_persen');
            if (totalBruto > 0 && raw > 0) {
                const persen = Math.round((raw / totalBruto) * 1000) / 10;
                if (inputPersen) inputPersen.value = persen;
            } else if (raw === 0 && inputPersen) {
                inputPersen.value = 0;
            }

            recalculateFinancials(false);
        }

        function updateDiskonFromBruto(totalBruto) {
            const inputPersen = document.getElementById('diskon_persen');
            const persen = parseFloat(inputPersen?.value) || 0;
            const inputDisplay = document.getElementById('diskon_khusus_display');
            const inputHidden = document.getElementById('diskon_khusus');
            if (persen > 0 && totalBruto > 0) {
                const nominal = Math.round((totalBruto * persen) / 100);
                if (inputDisplay) inputDisplay.value = nominal.toLocaleString('id-ID');
                if (inputHidden) inputHidden.value = nominal;
            } else if (totalBruto === 0) {
                if (inputDisplay) inputDisplay.value = '0';
                if (inputHidden) inputHidden.value = 0;
            }
        }

        function updateInsentifFromDpp(dpp) {
            const dppVal = dpp !== undefined ? dpp : (parseCurrency(document.getElementById('strukDpp').textContent) || 0);
            const persen = parseFloat(document.getElementById('insentif_persen').value) || 0;
            const inputNominal = document.getElementById('insentif_transaksikoran');
            if (persen > 0) {
                const nominal = Math.round((dppVal * persen) / 100);
                inputNominal.value = nominal.toLocaleString('id-ID');
            }
        }

        function handleInsentifPersenChange(val) {
            const dpp = parseCurrency(document.getElementById('strukDpp').textContent) || 0;
            const persen = parseFloat(val) || 0;
            const nominal = Math.round((dpp * persen) / 100);
            document.getElementById('insentif_transaksikoran').value = nominal.toLocaleString('id-ID');
        }

        function handleInsentifNominalInput(el) {
            if (typeof window.formatRupiahInput === 'function') {
                window.formatRupiahInput(el);
            }
            const raw = parseCurrency(el.value);
            const dpp = parseCurrency(document.getElementById('strukDpp').textContent) || 0;
            if (dpp > 0) {
                const persen = Math.round((raw / dpp) * 1000) / 10;
                document.getElementById('insentif_persen').value = persen;
            }
        }

        function updateKomisiFromDpp(dpp) {
            const dppVal = dpp !== undefined ? dpp : (parseCurrency(document.getElementById('strukDpp').textContent) || 0);
            const persen = parseFloat(document.getElementById('komisi_persen').value) || 0;
            const inputNominal = document.getElementById('komisi_transaksikoran');
            if (persen > 0) {
                const nominal = Math.round((dppVal * persen) / 100);
                inputNominal.value = nominal.toLocaleString('id-ID');
            }
        }

        function handleKomisiPersenChange(val) {
            const dpp = parseCurrency(document.getElementById('strukDpp').textContent) || 0;
            const persen = parseFloat(val) || 0;
            const nominal = Math.round((dpp * persen) / 100);
            document.getElementById('komisi_transaksikoran').value = nominal.toLocaleString('id-ID');
        }

        function handleKomisiNominalInput(el) {
            if (typeof window.formatRupiahInput === 'function') {
                window.formatRupiahInput(el);
            }
            const raw = parseCurrency(el.value);
            const dpp = parseCurrency(document.getElementById('strukDpp').textContent) || 0;
            if (dpp > 0) {
                const persen = Math.round((raw / dpp) * 1000) / 10;
                document.getElementById('komisi_persen').value = persen;
            }
        }

        function handleBayarInput(grandTotalCalc) {
            const elBayar = document.getElementById('jumlahbayar_transaksikoran');
            if (elBayar && typeof window.formatRupiahInput === 'function') {
                window.formatRupiahInput(elBayar);
            }
            const grandTotal = grandTotalCalc !== undefined ? grandTotalCalc : parseCurrency(document.getElementById('strukTotalNetto').textContent);
            let bayar = parseCurrency(elBayar ? elBayar.value : '0');
            const sisaPiutang = Math.max(0, grandTotal - bayar);

            document.getElementById('strukPiutang').textContent = `Rp ${sisaPiutang.toLocaleString('id-ID')}`;

            const badge = document.getElementById('badgeStatusBayar');
            const jenis = document.getElementById('jenis_pembayaran').value;

            if (sisaPiutang <= 0 && grandTotal > 0) {
                badge.className = "text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200";
                badge.textContent = "Lunas";
            } else if (jenis === 'Tunai') {
                badge.className = "text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200";
                badge.textContent = "Menunggu Pembayaran";
            } else {
                badge.className = "text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200";
                badge.textContent = "Belum Lunas";
            }
        }

        function setBayarLunas() {
            const grandTotal = parseCurrency(document.getElementById('strukTotalNetto').textContent);
            const elBayar = document.getElementById('jumlahbayar_transaksikoran');
            if (elBayar) {
                elBayar.value = grandTotal > 0 ? grandTotal.toLocaleString('id-ID') : '0';
            }
            handleBayarInput(grandTotal);
        }

        function parseCurrency(str) {
            return parseInt(str.replace(/[^0-9]/g, '')) || 0;
        }

        // Kop Kota & Sales Handler
        function handleKopKotaChange(sel) {
            const opt = sel.options[sel.selectedIndex];
            document.getElementById('halaman_iklan').value = opt.dataset.nama || opt.text;
            let halNum = parseInt(opt.dataset.halaman) || 1;
            if (halNum > 8) halNum = 4; // if HAL_DALAM (nomor 9), default to Hal 4
            setSimulatorPage(halNum, false);
            recalculateFinancials();
        }

        function handleSalesAeChange(sel) {
            const opt = sel.options[sel.selectedIndex];
            document.getElementById('sales_iklankoran').value = opt.dataset.nama || opt.text;
        }

        // ==============================================================
        // ENTERPRISE AUTOCOMPLETE & DUAL-WAY CLIENT SWITCHER
        // ==============================================================
        let searchPelangganTimeout = null;
        let initialPelanggansCache = @json($pelanggans->take(30));
        let currentPelangganMode = 'dual';

        function pilihSebagaiManual(name) {
            const manualInput = document.getElementById('inputManualNamaPemasang');
            if (manualInput) manualInput.value = name || '';
            document.getElementById('nama_pemasangkoran').value = name || '';
            document.getElementById('pelanggan_id').value = '';
            document.getElementById('dropdownHasilPelanggan').classList.add('hidden');
            if (manualInput) manualInput.focus();
            showCustomToast(`Nama "${name}" dipasang ke kolom nama manual.`, 'info');
        }

        function handleSearchPelangganFocus(forceOpen = false) {
            const dropdown = document.getElementById('dropdownHasilPelanggan');
            const input = document.getElementById('inputSearchPelanggan');
            if (forceOpen || input.value.trim().length === 0) {
                renderPelangganDropdown(initialPelanggansCache, input.value.trim());
                dropdown.classList.remove('hidden');
            } else if (input.value.trim().length > 0) {
                dropdown.classList.remove('hidden');
            }
        }

        function handleSearchPelangganInput(query) {
            const spinner = document.getElementById('spinnerCariPelanggan');
            const dropdown = document.getElementById('dropdownHasilPelanggan');
            
            clearTimeout(searchPelangganTimeout);
            query = query.trim();

            // Fallback sinkronisasi teks pencarian ke nama_pemasangkoran
            document.getElementById('nama_pemasangkoran').value = query;

            if (query.length === 0) {
                renderPelangganDropdown(initialPelanggansCache, '');
                dropdown.classList.remove('hidden');
                return;
            }

            // Quick local filter instant response
            const localMatches = initialPelanggansCache.filter(p => {
                const q = query.toLowerCase();
                return (p.nama_instansi && p.nama_instansi.toLowerCase().includes(q)) ||
                       (p.kode_pelanggan && p.kode_pelanggan.toLowerCase().includes(q)) ||
                       (p.no_telp && p.no_telp.includes(q)) ||
                       (p.alamat && p.alamat.toLowerCase().includes(q));
            });
            if (localMatches.length > 0) {
                renderPelangganDropdown(localMatches, query);
                dropdown.classList.remove('hidden');
            }

            // Server-side debounced search for full database
            if (spinner) spinner.classList.remove('hidden');
            searchPelangganTimeout = setTimeout(() => {
                fetch(`{{ route('pelanggan.search') }}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (spinner) spinner.classList.add('hidden');
                        renderPelangganDropdown(data, query);
                        dropdown.classList.remove('hidden');
                    })
                    .catch(() => {
                        if (spinner) spinner.classList.add('hidden');
                    });
            }, 200);
        }

        function handleSearchPelangganKeydown(event) {
            const dropdown = document.getElementById('dropdownHasilPelanggan');
            if (dropdown.classList.contains('hidden')) return;

            const items = dropdown.querySelectorAll('.pelanggan-item-btn');
            if (!items.length) return;

            let activeIndex = -1;
            items.forEach((btn, idx) => {
                if (btn.classList.contains('bg-kp-blue-50/90')) activeIndex = idx;
            });

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                const nextIdx = (activeIndex + 1) % items.length;
                items.forEach(b => b.classList.remove('bg-kp-blue-50/90', 'border-kp-blue-200'));
                items[nextIdx].classList.add('bg-kp-blue-50/90', 'border-kp-blue-200');
                items[nextIdx].scrollIntoView({ block: 'nearest' });
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                const prevIdx = (activeIndex - 1 + items.length) % items.length;
                items.forEach(b => b.classList.remove('bg-kp-blue-50/90', 'border-kp-blue-200'));
                items[prevIdx].classList.add('bg-kp-blue-50/90', 'border-kp-blue-200');
                items[prevIdx].scrollIntoView({ block: 'nearest' });
            } else if (event.key === 'Enter') {
                if (activeIndex >= 0 && items[activeIndex]) {
                    event.preventDefault();
                    items[activeIndex].click();
                }
            } else if (event.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        }

        function renderPelangganDropdown(list, currentQuery = '') {
            const container = document.getElementById('listPelangganItem');
            const safeQuery = (currentQuery || '').replace(/"/g, '&quot;').replace(/'/g, "\\'");
            window.currentPelangganList = list || [];

            if (!list || list.length === 0) {
                container.innerHTML = `
                    <div class="p-3 text-center space-y-2">
                        <p class="text-xs text-slate-500">Tidak ada pelanggan master yang cocok.</p>
                        ${currentQuery ? `
                            <button type="button" onclick="pilihSebagaiManual('${safeQuery}')"
                                    class="w-full px-3 py-2 bg-kp-blue-50 hover:bg-kp-blue-100 text-kp-blue-700 text-xs font-semibold rounded-xl border border-kp-blue-200 transition cursor-pointer flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <span>Gunakan "${currentQuery}" sebagai Pemasang Manual</span>
                            </button>
                        ` : ''}
                    </div>`;
                return;
            }

            let html = list.map((item, idx) => {
                const piutangVal = Number(item.total_piutang_koran || 0);
                const piutangBadge = piutangVal > 0 
                    ? `<span class="text-xs font-semibold text-rose-600 block">Piutang: Rp ${piutangVal.toLocaleString('id-ID')}</span>`
                    : `<span class="text-xs font-medium text-emerald-600 block">Piutang: Rp 0</span>`;

                return `
                    <button type="button" 
                            onclick="selectPelangganByIndex(${idx})"
                            class="pelanggan-item-btn w-full text-left p-2.5 rounded-xl hover:bg-kp-blue-50/70 transition flex items-center justify-between gap-3 group cursor-pointer border border-transparent hover:border-kp-blue-200">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-semibold text-xs text-slate-900 group-hover:text-kp-blue-700">${item.nama_instansi}</span>
                                <span class="px-1.5 py-0.5 text-xs font-semibold rounded bg-slate-100 text-slate-700 border border-slate-200 uppercase">${item.jenis_klien || 'Umum'}</span>
                                <span class="text-xs font-mono text-slate-400 font-medium">[${item.kode_pelanggan || 'PLG'}]</span>
                            </div>
                            <div class="text-xs text-slate-500 truncate mt-0.5 flex items-center gap-2">
                                ${item.no_telp ? `<span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>${item.no_telp}</span>` : ''}
                                ${item.alamat ? `<span class="inline-flex items-center gap-1 truncate max-w-[200px]"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>${item.alamat}</span>` : ''}
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            ${piutangBadge}
                            <span class="text-xs text-kp-blue-600 font-semibold group-hover:underline">Pilih ↵</span>
                        </div>
                    </button>
                `;
            }).join('');

            // Tampilkan opsi pilih teks bebas jika user sedang mengetik
            if (currentQuery && currentQuery.length > 1) {
                html += `
                    <div class="p-2 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-2 mt-1 rounded-b-xl">
                        <span class="text-xs text-slate-600 truncate">Bukan pelanggan master di atas?</span>
                        <button type="button" onclick="pilihSebagaiManual('${safeQuery}')"
                                class="px-2.5 py-1 text-xs font-semibold text-kp-blue-700 bg-white border border-slate-200 hover:border-kp-blue-300 rounded-lg shadow-2xs hover:bg-kp-blue-50 transition cursor-pointer shrink-0">
                            Gunakan "${currentQuery}" (Manual) ↵
                        </button>
                    </div>
                `;
            }

            container.innerHTML = html;
        }

        function selectPelangganByIndex(idx) {
            if (window.currentPelangganList && window.currentPelangganList[idx]) {
                selectPelangganItem(window.currentPelangganList[idx]);
            }
        }

        function selectPelangganItem(item) {
            if (!item) return;

            // Form inputs
            const pelIdInput = document.getElementById('pelanggan_id');
            if (pelIdInput) pelIdInput.value = item.id || '';

            const namaInput = document.getElementById('nama_pemasangkoran');
            if (namaInput) namaInput.value = item.nama_instansi || '';

            const alamatInput = document.getElementById('alamat_pemasangkoran');
            if (alamatInput) alamatInput.value = item.alamat || '';

            const noTelpInput = document.getElementById('no_telp');
            if (noTelpInput) noTelpInput.value = item.no_telp || '';

            // Update Selected Card labels safely
            const namaLabel = document.getElementById('labelNamaPelanggan');
            if (namaLabel) namaLabel.textContent = item.nama_instansi || '';

            const jenisBadge = document.getElementById('badgeJenisPelanggan');
            if (jenisBadge) jenisBadge.textContent = item.jenis_klien || 'Umum';

            const kodeLabel = document.getElementById('labelKodePelanggan');
            if (kodeLabel) kodeLabel.textContent = `[${item.kode_pelanggan || 'PLG'}]`;

            const telpEl = document.getElementById('labelTelpPelanggan');
            if (telpEl) {
                if (item.no_telp) {
                    telpEl.innerHTML = `
                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>${item.no_telp}</span>
                    `;
                    telpEl.classList.remove('hidden');
                } else {
                    telpEl.classList.add('hidden');
                }
            }

            const alamatEl = document.getElementById('labelAlamatPelanggan');
            if (alamatEl) {
                if (item.alamat) {
                    alamatEl.innerHTML = `
                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>${item.alamat}</span>
                    `;
                    alamatEl.classList.remove('hidden');
                } else {
                    alamatEl.classList.add('hidden');
                }
            }

            const piutangVal = Number(item.total_piutang_koran || 0);
            const piutangEl = document.getElementById('labelPiutangPelanggan');
            if (piutangEl) {
                if (piutangVal > 0) {
                    piutangEl.innerHTML = `
                        <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Piutang: Rp ${piutangVal.toLocaleString('id-ID')}</span>
                    `;
                    piutangEl.classList.remove('hidden');
                } else {
                    piutangEl.classList.add('hidden');
                }
            }

            // Sinkronisasi ke kolom input manual agar nama instansi tampil jelas
            const manualInput = document.getElementById('inputManualNamaPemasang');
            if (manualInput) manualInput.value = item.nama_instansi || '';

            // Tampilkan Card di Kolom 1 & Sembunyikan Input Search di Kolom 1
            const cardEl = document.getElementById('cardPelangganTerpilih');
            if (cardEl) cardEl.classList.remove('hidden');

            const searchCont = document.getElementById('containerSearchPelanggan');
            if (searchCont) searchCont.classList.add('hidden');

            const dropdownEl = document.getElementById('dropdownHasilPelanggan');
            if (dropdownEl) dropdownEl.classList.add('hidden');

            const searchInput = document.getElementById('inputSearchPelanggan');
            if (searchInput) searchInput.value = '';
            updateSimulator();
        }

        function resetPilihanPelanggan() {
            document.getElementById('pelanggan_id').value = '';
            document.getElementById('cardPelangganTerpilih').classList.add('hidden');
            document.getElementById('containerSearchPelanggan').classList.remove('hidden');

            // Kosongkan nama pemesan, no telp, alamat, dan pic saat dibatalkan
            const hiddenNama = document.getElementById('nama_pemasangkoran');
            if (hiddenNama) hiddenNama.value = '';
            const manualInput = document.getElementById('inputManualNamaPemasang');
            if (manualInput) manualInput.value = '';
            const telpInput = document.getElementById('no_telp');
            if (telpInput) telpInput.value = '';
            const alamatInput = document.getElementById('alamat_pemasangkoran');
            if (alamatInput) alamatInput.value = '';
            const picInput = document.getElementById('nama_pic');
            if (picInput) picInput.value = '';

            const searchInput = document.getElementById('inputSearchPelanggan');
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
                handleSearchPelangganFocus(true);
            }
            updateSimulator();
        }

        function handleManualNamaInput(val) {
            document.getElementById('nama_pemasangkoran').value = val;
            // Jika user mengetik manual nama baru, kosongkan relasi master agar tersimpan sebagai non-master
            document.getElementById('pelanggan_id').value = '';
            updateSimulator();

            // Jika card master terpilih sedang aktif dan user mengetik nama baru secara manual, kembalikan ke search
            const cardSelected = document.getElementById('cardPelangganTerpilih');
            const contSearch = document.getElementById('containerSearchPelanggan');
            if (cardSelected && !cardSelected.classList.contains('hidden') && val.trim().length > 0) {
                cardSelected.classList.add('hidden');
                if (contSearch) contSearch.classList.remove('hidden');
                const searchInput = document.getElementById('inputSearchPelanggan');
                if (searchInput) searchInput.value = '';
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            const container = document.getElementById('containerSearchPelanggan');
            const dropdown = document.getElementById('dropdownHasilPelanggan');
            if (container && dropdown && !container.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        function handleFileUpload(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                document.getElementById('fileMateriLabel').innerHTML = `
                    <span class="inline-flex items-center gap-1.5 font-semibold text-slate-800">
                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>${file.name}</span>
                    </span>
                `;
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        uploadedAdImageSrc = e.target.result;
                        updateSimulator();
                    };
                    reader.readAsDataURL(file);
                } else {
                    uploadedAdImageSrc = null;
                    updateSimulator();
                }
            }
        }

        // Standar Sistem Notifikasi & Peringatan Redaksi Pers (Enterprise Publisher Alert)
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
            let badgeHtml = '';

            if (type === 'warning') {
                iconHtml = `
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>`;
                defaultTitle = 'Batas Jadwal Terpenuhi';
                badgeHtml = `<span class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200/80 px-2 py-0.5 rounded-full">Perhatian</span>`;
            } else if (type === 'error') {
                iconHtml = `
                    <div class="w-9 h-9 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                    </div>`;
                defaultTitle = 'Peringatan Operasional';
                badgeHtml = `<span class="text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200/80 px-2 py-0.5 rounded-full">Kendala</span>`;
            } else if (type === 'success') {
                iconHtml = `
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>`;
                defaultTitle = 'Berhasil Tersimpan';
                badgeHtml = `<span class="text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 rounded-full">Sukses</span>`;
            } else {
                iconHtml = `
                    <div class="w-9 h-9 rounded-xl bg-kp-blue-500/10 border border-kp-blue-500/20 text-kp-blue-600 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </div>`;
                defaultTitle = 'Informasi Transaksi';
                badgeHtml = `<span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200/80 px-2 py-0.5 rounded-full">Info</span>`;
            }

            toast.innerHTML = `
                ${iconHtml}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="text-xs font-bold text-slate-900 tracking-tight">${title || defaultTitle}</h4>
                        ${badgeHtml}
                    </div>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">${message}</p>
                </div>
                <button type="button" class="w-6 h-6 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer shrink-0 ml-0.5 -mr-1 -mt-0.5" title="Tutup Notifikasi">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            `;

            toast.querySelector('button').onclick = () => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 250);
            };

            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-3', 'opacity-0');
            });

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => toast.remove(), 250);
                }
            }, 4500);
        }

        // Modal Pelanggan Baru On-The-Fly
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

        // Modal Konfirmasi Simpan Transaksi Interceptor
        function konfirmasiSimpanTransaksi() {
            // 1. Validasi Nama Pemesan
            const hiddenNamaInput = document.getElementById('nama_pemasangkoran');
            const manualInput = document.getElementById('inputManualNamaPemasang');
            const searchInput = document.getElementById('inputSearchPelanggan');

            let finalNama = (manualInput && manualInput.value.trim()) || (hiddenNamaInput && hiddenNamaInput.value.trim()) || (searchInput && searchInput.value.trim());

            if (!finalNama) {
                showCustomToast('Silakan pilih pelanggan dari daftar atau ketik nama pemesan di kolom yang tersedia.', 'warning', 'Nama Pemesan Wajib Diisi');
                if (manualInput) {
                    manualInput.focus();
                    manualInput.classList.add('ring-2', 'ring-rose-500');
                    setTimeout(() => manualInput.classList.remove('ring-2', 'ring-rose-500'), 2500);
                }
                return false;
            }

            if (hiddenNamaInput) hiddenNamaInput.value = finalNama;

            // 2. Validasi Khusus Produk
            if (typeof currentProduct !== 'undefined' && currentProduct.tipe === 'BARIS') {
                const filledCount = barisLines.filter(l => l.trim().length > 0).length;
                if (filledCount < 2) {
                    showCustomToast('Iklan baris wajib diisi minimal 2 baris naskah cetak sebelum disimpan.', 'warning', 'Naskah Belum Lengkap');
                    return false;
                }
            }

            const totalMuatEl = document.getElementById('total_muatkoran');
            if (totalMuatEl && typeof selectedDates !== 'undefined') {
                const totalMuat = parseInt(totalMuatEl.value) || 1;
                if (selectedDates.length !== totalMuat) {
                    showCustomToast(`Frekuensi terbit (${totalMuat} edisi) harus sesuai dengan jumlah tanggal yang dipilih (${selectedDates.length} tanggal). Sesuaikan di kalender.`, 'warning', 'Kesesuaian Tanggal Terbit');
                    return false;
                }
            }

            // 3. Set data ringkasan ke modal konfirmasi
            const namaLabel = document.getElementById('konfirmasiNamaPemesan');
            if (namaLabel) namaLabel.textContent = finalNama;

            const skemaVal = document.getElementById('jenis_pembayaran')?.value || 'Tunai';
            const skemaLabel = document.getElementById('konfirmasiSkemaBayar');
            if (skemaLabel) skemaLabel.textContent = skemaVal;

            const totalNettoStr = document.getElementById('strukTotalNetto')?.textContent || 'Rp 0';
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
            document.getElementById('formTransaksiKoran').submit();
        }

        function simpanPelangganOnTheFly() {
            const nama = document.getElementById('modal_nama_instansi').value.trim();
            const jenis = document.getElementById('modal_jenis_klien').value;
            const telp = document.getElementById('modal_no_telp').value.trim();
            const alamat = document.getElementById('modal_alamat').value.trim();
            const npwp = document.getElementById('modal_npwp_nik')?.value?.trim() || '';
            const creditLimit = document.getElementById('modal_credit_limit')?.value?.trim() || '0';
            const email = document.getElementById('modal_email')?.value?.trim() || '';

            if (!nama) {
                showCustomToast('Nama instansi/pemasang wajib diisi!', 'warning', 'Form Belum Lengkap');
                return;
            }

            const btn = document.getElementById('btnSimpanPelangganModal');
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'Menyimpan...';
            }

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
                    alamat: alamat,
                    npwp_nik: npwp,
                    credit_limit: creditLimit,
                    email: email
                })
            })
            .then(res => res.json())
            .then(data => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerText = 'Simpan & Pilih';
                }

                if (data.status === 'success' || data.data) {
                    const plg = data.data;
                    initialPelanggansCache.unshift(plg);
                    selectPelangganItem(plg);
                    closeModalPelangganBaru();

                    // Bersihkan form modal
                    document.getElementById('modal_nama_instansi').value = '';
                    document.getElementById('modal_no_telp').value = '';
                    document.getElementById('modal_alamat').value = '';
                    if (document.getElementById('modal_npwp_nik')) document.getElementById('modal_npwp_nik').value = '';
                    if (document.getElementById('modal_credit_limit')) document.getElementById('modal_credit_limit').value = '';
                    if (document.getElementById('modal_email')) document.getElementById('modal_email').value = '';

                    showCustomToast(`Pelanggan ${plg.nama_instansi} berhasil didaftarkan & dipilih.`, 'success', 'Pelanggan Baru');
                } else {
                    showCustomToast('Gagal menambahkan pelanggan: ' + (data.message || 'Terjadi kesalahan'), 'error', 'Peringatan');
                }
            })
            .catch(err => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerText = 'Simpan & Pilih';
                }
                showCustomToast('Terjadi kesalahan jaringan: ' + err.message, 'error', 'Koneksi');
            });
        }

        // Modal Sales / AE On-The-Fly
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
                showCustomToast('Kode AE dan Nama Lengkap Sales wajib diisi!', 'warning');
                return;
            }

            const btn = document.getElementById('btnSimpanSalesAeModal');
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'Menyimpan...';
            }

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
                if (btn) {
                    btn.disabled = false;
                    btn.innerText = 'Simpan & Pilih';
                }

                if (data.success && data.data) {
                    const ae = data.data;
                    const select = document.getElementById('sales_ae_id');
                    const newOpt = new Option(`[${ae.kode_ae}] ${ae.nama_ae} (${ae.jenis_kode})`, ae.id, true, true);
                    newOpt.dataset.kode = ae.kode_ae;
                    newOpt.dataset.nama = ae.nama_ae;
                    select.add(newOpt);

                    const hiddenSales = document.getElementById('sales_iklankoran');
                    if (hiddenSales) {
                        hiddenSales.value = `${ae.kode_ae} - ${ae.nama_ae}`;
                    }

                    document.getElementById('modal_sales_nama').value = '';
                    closeModalSalesAeBaru();
                    showCustomToast(`Sales/AE [${ae.kode_ae}] ${ae.nama_ae} berhasil ditambahkan.`, 'success');
                } else {
                    showCustomToast('Gagal menambahkan Sales/AE: ' + (data.message || 'Periksa data input Anda'), 'error');
                }
            })
            .catch(err => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = `
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Simpan &amp; Pilih</span>
                    `;
                }
                showCustomToast('Terjadi kesalahan: ' + err.message, 'error');
            });
        }
    </script>

    <!-- Global Toast Container -->
    <div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2.5 pointer-events-none max-w-sm"></div>
</x-app-layout>
