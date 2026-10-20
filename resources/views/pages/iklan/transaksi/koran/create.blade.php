<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-black tracking-tight">
                        Entri Pemuatan Iklan
                    </h1>
                    <p class="text-xs text-black mt-0.5 font-normal">
                        Formulir pemesanan iklan cetak harian Kabar Priangan.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <div class="text-[11px] font-bold text-black">No. Faktur</div>
                    <div class="font-mono text-sm font-bold text-kp-blue-700">{{ $nofakturkoran }}</div>
                </div>
                <a href="{{ route('transaksikoran.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-black text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    &larr; <span>Kembali ke Transaksi Koran</span>
                </a>
            </div>
        </div>

        <!-- 2. Notice Deadline & Cut-Off Cetak Harian (Modern Enterprise Theme) -->
        @php
            \Carbon\Carbon::setLocale('id');
            $isPastCutoff = $configs['is_past_cutoff'];
            $besok = now()->addDay()->locale('id')->translatedFormat('l, d F Y');
            $lusa  = now()->addDays(2)->locale('id')->translatedFormat('l, d F Y');
            $edisiTerdekat = $isPastCutoff ? $lusa : $besok;
        @endphp
        <div class="rounded-xl border {{ $isPastCutoff ? 'bg-amber-50/70 border-amber-200 text-amber-900' : 'bg-gradient-to-r from-sky-50/90 via-white to-slate-50 border-sky-100 text-slate-800' }} px-4 py-2.5 shadow-2xs transition">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg {{ $isPastCutoff ? 'bg-amber-100 text-amber-700 border border-amber-300' : 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' }} flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs">
                        <span class="font-bold {{ $isPastCutoff ? 'text-amber-800' : 'text-slate-900' }}">
                            {{ $isPastCutoff ? 'Lewat Batas Cetak Harian (' . $configs['jam_cutoff'] . ' WIB)' : 'Batas Terbit Besok: ' . $configs['jam_cutoff'] . ' WIB' }}
                        </span>
                        <span class="text-slate-300 hidden sm:inline">|</span>
                        <span class="text-slate-600 font-medium">
                            Edisi Terdekat: <span class="font-semibold {{ $isPastCutoff ? 'text-amber-700' : 'text-kp-blue-700' }}">{{ $edisiTerdekat }}</span>
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 self-end sm:self-center font-mono text-[11px] font-semibold px-2.5 py-1 rounded-md bg-white border border-slate-200 text-slate-600 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ now()->format('H:i') }} WIB</span>
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
        <form id="formTransaksiKoran" method="POST" action="{{ route('transaksikoran.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Hidden Technical Fields -->
            <input type="hidden" name="nofakturkoran" value="{{ $nofakturkoran }}">
            <input type="hidden" name="tanggal_transaksikoran" value="{{ now()->toDateString() }}">
            <input type="hidden" name="ppn_persen" id="ppn_persen" value="{{ $configs['ppn_persen'] }}">
            <input type="hidden" name="kategori_form" id="kategori_form" value="{{ old('kategori_form', 'BARIS') }}">
            <input type="hidden" name="warna_iklan" id="warna_iklan" value="{{ old('warna_iklan', 'BW') }}">
            <input type="hidden" name="id_iklankoran" id="id_iklankoran" value="{{ old('id_iklankoran', $iklankoran->first()->id ?? '') }}">
            <input type="hidden" name="tanggal_muat_list" id="tanggal_muat_list" value="{{ old('tanggal_muat_list', now()->addDay()->toDateString()) }}">
            <input type="hidden" name="tanggal_muatkoran" id="tanggal_muatkoran" value="{{ old('tanggal_muatkoran', now()->addDay()->toDateString()) }}">
            <input type="hidden" name="harga_transaksikoran" id="harga_transaksikoran" value="0">
            <input type="hidden" name="ukuran_iklan" id="ukuran_iklan" value="">
            <input type="hidden" name="jenis_pembayaran" id="jenis_pembayaran" value="{{ old('jenis_pembayaran', 'Tunai') }}">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- ============================================================== -->
                <!-- PANEL KIRI: INPUT DATA TRANSAKSI                               -->
                <!-- ============================================================== -->
                <div class="lg:col-span-7 space-y-5">

                    <!-- SEKSI 1: CARA BAYAR & DATA PEMASANG -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-xs font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-[11px] font-bold">1</span>
                                <span>Data Pemesan</span>
                            </span>
                        </div>

                        <!-- PILIHAN SKEMA PEMBAYARAN -->
                        <div>
                            <label class="text-xs font-medium text-slate-700 mb-2 block">
                                Skema Pembayaran <span class="text-rose-500">*</span>
                            </label>

                            <div class="grid grid-cols-2 gap-2.5">
                                <!-- Card 1: TUNAI -->
                                <button type="button" id="cardBayarTunai" onclick="setJenisPembayaran('Tunai')"
                                        class="px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-emerald-50/70 border-emerald-500 text-emerald-950 font-semibold shadow-2xs">
                                    <div class="flex items-center gap-2.5">
                                        <div id="iconBoxTunai" class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <rect x="2" y="6" width="20" height="12" rx="2" />
                                                <circle cx="12" cy="12" r="2" />
                                                <path d="M6 12h.01M18 12h.01" />
                                            </svg>
                                        </div>
                                        <span class="text-xs tracking-tight">Tunai</span>
                                    </div>
                                    <span id="dotBayarTunai" class="text-emerald-600 flex items-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </button>

                                <!-- Card 2: KREDIT -->
                                <button type="button" id="cardBayarKredit" onclick="setJenisPembayaran('Kredit')"
                                        class="px-3.5 py-2.5 rounded-xl border-2 transition cursor-pointer flex items-center justify-between bg-white border-slate-200 text-slate-600 hover:border-slate-300 font-medium">
                                    <div class="flex items-center gap-2.5">
                                        <div id="iconBoxKredit" class="w-6 h-6 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <rect x="2" y="5" width="20" height="14" rx="2" />
                                                <line x1="2" y1="10" x2="22" y2="10" />
                                            </svg>
                                        </div>
                                        <span class="text-xs tracking-tight">Kredit</span>
                                    </div>
                                    <span id="dotBayarKredit" class="text-transparent flex items-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Baris Input Pemasang: 2 Kolom Sejajar (Pilih dari Data Pelanggan vs Input Nama Langsung) -->
                        <div class="space-y-3 pt-3 border-t border-slate-100">
                            @php
                                $selectedOldPelanggan = old('pelanggan_id') ? $pelanggans->firstWhere('id', old('pelanggan_id')) : null;
                                $oldNamaPemasang = old('nama_pemasangkoran', $selectedOldPelanggan->nama_instansi ?? '');
                            @endphp

                            <!-- Hidden Form Fields -->
                            <input type="hidden" name="pelanggan_id" id="pelanggan_id" value="{{ old('pelanggan_id', $selectedOldPelanggan->id ?? '') }}">
                            <input type="hidden" name="nama_pemasangkoran" id="nama_pemasangkoran" value="{{ $oldNamaPemasang }}">

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
                                            <div class="text-[10px] text-slate-500 flex items-center gap-3 mt-0.5 flex-wrap">
                                                <span id="labelTelpPelanggan" class="inline-flex items-center gap-1 truncate {{ empty($selectedOldPelanggan?->no_telp) ? 'hidden' : '' }}">
                                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    <span>{{ $selectedOldPelanggan->no_telp ?? '' }}</span>
                                                </span>
                                                <span id="labelAlamatPelanggan" class="inline-flex items-center gap-1 truncate {{ empty($selectedOldPelanggan?->alamat) ? 'hidden' : '' }}">
                                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <span>{{ $selectedOldPelanggan->alamat ?? '' }}</span>
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
                                                   placeholder="Cari pelanggan terdaftar..."
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
                                               placeholder="Nama instansi atau perorangan..."
                                               value="{{ $oldNamaPemasang }}"
                                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Alamat & Telepon -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="text-xs font-medium text-slate-700 mb-1 block">
                                    Alamat
                                </label>
                                <textarea name="alamat_pemasangkoran" id="alamat_pemasangkoran" rows="2"
                                          placeholder="Alamat lengkap pemesan..."
                                          class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs resize-none">{{ old('alamat_pemasangkoran') }}</textarea>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-700 mb-1 block">
                                    No. Telepon / WA
                                </label>
                                <div class="relative flex items-center">
                                    <input type="text" name="no_telp" id="no_telp"
                                           placeholder="08xxxxxxxxxx"
                                           value="{{ old('no_telp') }}"
                                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-kp-blue-500/20 focus:border-kp-blue-600 transition shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- Sales & Komisioner (Clean, Simple, Elegan) -->
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
                                        $defaultAeId = $salesAes->firstWhere('jenis_kode', 'FO')->id ?? $salesAes->first()->id ?? '';
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
                                                    default => $ae->jenis_kode
                                                };
                                                $displayNama = ($ae->kode_ae === 'FO') 
                                                    ? 'Front Office (Loket Langsung)' 
                                                    : $ae->nama_ae . ' — ' . $roleLabel;
                                            @endphp
                                            <option value="{{ $ae->id }}"
                                                    data-kode="{{ $ae->kode_ae }}"
                                                    data-nama="{{ $ae->nama_ae }}"
                                                    {{ old('sales_ae_id', $defaultAeId) == $ae->id ? 'selected' : '' }}>
                                                {{ $displayNama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="sales_iklankoran" id="sales_iklankoran" value="Front Office (Loket Langsung)">

                                    <div>
                                        <label class="block text-[11px] font-medium text-slate-600 mb-1">
                                            Insentif Sales
                                        </label>
                                        <div class="grid grid-cols-5 gap-2">
                                            <div class="col-span-2 relative">
                                                <input type="number" name="insentif_persen" id="insentif_persen" 
                                                       value="{{ old('insentif_persen', 0) }}" step="0.5" min="0" max="100"
                                                       placeholder="0"
                                                       oninput="handleInsentifPersenChange(this.value)"
                                                       class="w-full h-8 px-2.5 pr-6 bg-white border border-slate-200 rounded-lg text-xs font-mono font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                                <span class="absolute right-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">%</span>
                                            </div>
                                            <div class="col-span-3 relative">
                                                <span class="absolute left-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">Rp</span>
                                                <input type="text" name="insentif_transaksikoran" id="insentif_transaksikoran" 
                                                       value="{{ old('insentif_transaksikoran', 0) }}"
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
                                               value="{{ old('komisioner') }}"
                                               placeholder="Nama perantara (jika ada)"
                                               class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-medium text-slate-600 mb-1">
                                            Komisi Perantara
                                        </label>
                                        <div class="grid grid-cols-5 gap-2">
                                            <div class="col-span-2 relative">
                                                <input type="number" name="komisi_persen" id="komisi_persen" 
                                                       value="{{ old('komisi_persen', 0) }}" step="0.5" min="0" max="100"
                                                       placeholder="0"
                                                       oninput="handleKomisiPersenChange(this.value)"
                                                       class="w-full h-8 px-2.5 pr-6 bg-white border border-slate-200 rounded-lg text-xs font-mono font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                                <span class="absolute right-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">%</span>
                                            </div>
                                            <div class="col-span-3 relative">
                                                <span class="absolute left-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">Rp</span>
                                                <input type="text" name="komisi_transaksikoran" id="komisi_transaksikoran" 
                                                       value="{{ old('komisi_transaksikoran', 0) }}"
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

                    <!-- SEKSI 2: JENIS IKLAN -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-xs font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-[11px] font-bold">2</span>
                                <span>Jenis Iklan</span>
                            </span>
                        </div>

                        @php
                            // Kelompokkan produk berdasarkan tipe_dasar
                            $groupedProducts = $iklankoran->groupBy('tipe_dasar');
                            $tabOrder = ['BARIS', 'KOLOM', 'DISPLAY', 'ADVERTORIAL', 'KHUSUS', 'BONUS'];
                            $defaultTab = old('kategori_form', 'BARIS');
                            if (!$groupedProducts->has($defaultTab) && $groupedProducts->isNotEmpty()) {
                                $defaultTab = $groupedProducts->keys()->first();
                            }
                        @endphp

                        <!-- Segmented Tab Controls -->
                        <div class="flex flex-wrap gap-2 p-1.5 bg-slate-100 rounded-2xl border border-slate-200/80">
                            @foreach($tabOrder as $tabKey)
                                @if($groupedProducts->has($tabKey))
                                    @php
                                        $countItems = $groupedProducts[$tabKey]->count();
                                        $tabLabel = match($tabKey) {
                                            'BARIS'       => 'Iklan Baris',
                                            'KOLOM'       => 'Iklan Kolom',
                                            'DISPLAY'     => 'Display',
                                            'ADVERTORIAL' => 'Advertorial',
                                            'KHUSUS'      => 'Khusus / Kuping',
                                            'BONUS'       => 'Bonus / CSR',
                                            default       => $tabKey
                                        };
                                        $isTabActive = ($defaultTab === $tabKey);
                                    @endphp
                                    <button type="button" onclick="switchProductTab('{{ $tabKey }}')"
                                            id="tabBtn_{{ $tabKey }}"
                                            class="tab-product-btn px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 {{ $isTabActive ? 'bg-kp-blue-600 text-white font-semibold shadow-xs border border-kp-blue-600' : 'bg-white text-slate-700 font-medium border border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                                        <span>{{ $tabLabel }}</span>
                                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $isTabActive ? 'bg-white/20 text-white font-semibold' : 'bg-slate-100 text-slate-600 font-medium' }}">
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
                                                $isSelected = (old('id_iklankoran', $iklankoran->first()->id ?? null) == $item->id);
                                                $badgeColor = match($item->mode_warna) {
                                                    'FC' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                    default => 'bg-slate-100 text-slate-700 border-slate-200'
                                                };
                                            @endphp
                                            <button type="button" 
                                                    onclick="pilihProdukIklan({{ $item->id }}, '{{ $item->kode_jenis }}', '{{ $item->tipe_dasar }}', '{{ $item->mode_warna }}', '{{ $item->cara_hitung }}', {{ $item->tarif_dasar }}, {{ $item->is_fixed_size ? 'true' : 'false' }}, {{ $item->default_kolom ?? 1 }}, {{ $item->default_tinggi_mm ?? 50 }}, {{ $item->min_order ?? 2 }})"
                                                    id="cardProduk_{{ $item->id }}"
                                                    data-tab="{{ $tabKey }}"
                                                    class="produk-card text-left p-3 rounded-xl border transition cursor-pointer relative flex flex-col justify-between gap-2 {{ $isSelected ? 'border-kp-blue-600 bg-kp-blue-50/40 ring-2 ring-kp-blue-500/20 shadow-xs' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50' }}">
                                                    <div class="flex items-center justify-between gap-1 mb-1.5">
                                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-md border {{ $badgeColor }} inline-flex items-center gap-1.5">
                                                            @if($item->mode_warna === 'FC')
                                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                                                <span>Full Color</span>
                                                            @else
                                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-600 shrink-0"></span>
                                                                <span>Hitam Putih</span>
                                                            @endif
                                                        </span>
                                                        <span class="text-[9px] font-mono text-slate-400 uppercase font-medium">{{ $item->kode_jenis }}</span>
                                                    </div>
                                                    <div class="text-xs font-semibold text-slate-900 leading-snug line-clamp-2">
                                                        {{ $item->nama_jenis }}
                                                    </div>
                                                <div class="pt-1 border-t border-slate-100/80">
                                                    <div class="text-[11px] font-semibold text-kp-blue-700 font-mono">
                                                        @if($item->cara_hitung === 'PER_BARIS')
                                                            Rp {{ number_format($item->tarif_dasar, 0, ',', '.') }}/brs
                                                        @elseif($item->cara_hitung === 'PER_MMK')
                                                            Rp {{ number_format($item->tarif_dasar, 0, ',', '.') }}/mmk
                                                        @elseif($item->cara_hitung === 'FIXED_ZERO')
                                                            Rp 0 (Bonus)
                                                        @else
                                                            Rp {{ number_format($item->tarif_dasar, 0, ',', '.') }}
                                                        @endif
                                                    </div>
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
                                <label class="text-xs font-medium text-slate-700 mb-1 block">
                                    Penempatan Halaman <span class="text-rose-500">*</span>
                                </label>
                                <select name="kop_kota_id" id="kop_kota_id" onchange="handleKopKotaChange(this)"
                                        class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                    @foreach($kopKotas as $kota)
                                        <option value="{{ $kota->id }}" 
                                                data-halaman="{{ $kota->nomor_halaman }}"
                                                data-nama="{{ $kota->nama_penempatan }}"
                                                data-surcharge="{{ $kota->surcharge_persen }}"
                                                {{ (old('kop_kota_id') == $kota->id || (!old('kop_kota_id') && ($kota->kode === 'HAL1' || $loop->first))) ? 'selected' : '' }}>
                                            {{ $kota->nama_penempatan }} @if($kota->surcharge_persen > 0) (+{{ number_format($kota->surcharge_persen, 2) }}%) @endif
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="halaman_iklan" id="halaman_iklan" value="{{ $kopKotas->first()?->nama_penempatan ?? 'Halaman 1 (Halaman Depan / Cover)' }}">
                            </div>

                            <!-- Rubrik Baris -->
                            <div id="containerKopIklan">
                                <label class="text-xs font-medium text-slate-700 mb-1 block">
                                    Rubrik Iklan
                                </label>
                                <select name="kop_iklan_id" id="kop_iklan_id"
                                        class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                    @foreach($kopIklans as $kop)
                                        <option value="{{ $kop->id }}" {{ $kop->kode === 'HILANG' ? 'selected' : '' }}>
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
                            <span class="text-xs font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-[11px] font-bold">3</span>
                                <span>Ukuran &amp; Naskah</span>
                            </span>
                        </div>

                        <!-- KONTEN KHUSUS IKLAN BARIS -->
                        <div id="sectionIklanBaris" class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <label class="text-xs font-medium text-slate-800">
                                        Naskah Iklan Baris <span class="text-rose-500">*</span>
                                    </label>
                                    <span class="text-[10px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded font-medium">Maks. 32 karakter/baris</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[11px] font-medium px-2.5 py-1 rounded-lg bg-kp-blue-50 text-kp-blue-800 border border-kp-blue-200 font-mono">
                                        <span id="labelJumlahBaris" class="text-sm font-bold">2</span> Baris
                                    </span>
                                </div>
                            </div>

                            <!-- Dynamic Numbered Row Inputs Container -->
                            <div id="containerBarisRows" class="space-y-2">
                                <!-- Row 1 sampai N akan digenerate secara interaktif via JS -->
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                                <button type="button" id="btnTambahBaris" onclick="tambahBarisInput()"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-xl transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>Tambah Baris</span>
                                </button>
                                <span class="text-[11px] text-slate-500 font-mono">
                                    Tarif: Rp {{ number_format($configs['tarif_per_baris'], 0, ',', '.') }}/baris
                                </span>
                            </div>

                            <!-- Live Visual Box Print Preview Iklan Baris -->
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                                <div class="flex items-center justify-between text-[11px] font-semibold text-slate-700">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        Pratinjau Baris:
                                    </span>
                                    <span id="previewBarisCharTotal" class="font-mono text-[10px] text-slate-500">0 Karakter</span>
                                </div>
                                <div id="previewBoxBaris" class="bg-white p-2.5 rounded-lg border border-slate-200 font-mono text-xs text-slate-900 leading-snug divide-y divide-slate-100 shadow-2xs">
                                    <div class="text-slate-400 italic text-[11px] py-1 text-center">Naskah iklan baris akan tampil di sini...</div>
                                </div>
                            </div>

                            <input type="hidden" name="naskah_iklan_baris" id="naskah_iklan_baris" value="{{ old('naskah_iklan_baris') }}">
                            <input type="hidden" name="jumlah_baris" id="jumlah_baris" value="{{ old('jumlah_baris', 2) }}">
                        </div>

                        <!-- KONTEN KHUSUS KOLOM / DISPLAY / ADVERTORIAL -->
                        <div id="sectionIklanDisplay" class="space-y-4 hidden">
                            <!-- Presets Cepat Dimensi -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-medium text-slate-700 block">Pilihan Ukuran:</label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <button type="button" onclick="setDimensi(1, 50)" data-kol="1" data-mm="50"
                                            class="preset-dimensi-btn px-2.5 py-1.5 rounded-xl text-left border transition cursor-pointer flex flex-col justify-between border-slate-200 bg-slate-50/70 hover:bg-white hover:border-slate-300">
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-[10px] text-slate-500 font-medium">Mini</span>
                                            <span class="preset-check text-kp-blue-600 hidden"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                        </div>
                                        <div class="text-xs font-semibold text-slate-900">1 kol × 50 mm</div>
                                        <div class="text-[9px] font-mono text-slate-400">50 mmk</div>
                                    </button>

                                    <button type="button" onclick="setDimensi(2, 50)" data-kol="2" data-mm="50"
                                            class="preset-dimensi-btn px-2.5 py-1.5 rounded-xl text-left border transition cursor-pointer flex flex-col justify-between border-slate-200 bg-slate-50/70 hover:bg-white hover:border-slate-300">
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-[10px] text-slate-500 font-medium">Kartu Nama</span>
                                            <span class="preset-check text-kp-blue-600 hidden"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                        </div>
                                        <div class="text-xs font-semibold text-slate-900">2 kol × 50 mm</div>
                                        <div class="text-[9px] font-mono text-slate-400">100 mmk</div>
                                    </button>

                                    <button type="button" onclick="setDimensi(2, 100)" data-kol="2" data-mm="100"
                                            class="preset-dimensi-btn px-2.5 py-1.5 rounded-xl text-left border transition cursor-pointer flex flex-col justify-between border-slate-200 bg-slate-50/70 hover:bg-white hover:border-slate-300">
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-[10px] text-slate-500 font-medium">Kolom Standar</span>
                                            <span class="preset-check text-kp-blue-600 hidden"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                        </div>
                                        <div class="text-xs font-semibold text-slate-900">2 kol × 100 mm</div>
                                        <div class="text-[9px] font-mono text-slate-400">200 mmk</div>
                                    </button>

                                    <button type="button" onclick="setDimensi(3, 150)" data-kol="3" data-mm="150"
                                            class="preset-dimensi-btn px-2.5 py-1.5 rounded-xl text-left border transition cursor-pointer flex flex-col justify-between border-slate-200 bg-slate-50/70 hover:bg-white hover:border-slate-300">
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-[10px] text-slate-500 font-medium">Display Sedang</span>
                                            <span class="preset-check text-kp-blue-600 hidden"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                        </div>
                                        <div class="text-xs font-semibold text-slate-900">3 kol × 150 mm</div>
                                        <div class="text-[9px] font-mono text-slate-400">450 mmk</div>
                                    </button>

                                    <button type="button" onclick="setDimensi(4, 270)" data-kol="4" data-mm="270"
                                            class="preset-dimensi-btn px-2.5 py-1.5 rounded-xl text-left border transition cursor-pointer flex flex-col justify-between border-slate-200 bg-slate-50/70 hover:bg-white hover:border-slate-300">
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-[10px] text-slate-500 font-medium">¼ Halaman</span>
                                            <span class="preset-check text-kp-blue-600 hidden"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                        </div>
                                        <div class="text-xs font-semibold text-slate-900">4 kol × 270 mm</div>
                                        <div class="text-[9px] font-mono text-slate-400">1.080 mmk</div>
                                    </button>

                                    <button type="button" onclick="setDimensi(7, 270)" data-kol="7" data-mm="270"
                                            class="preset-dimensi-btn px-2.5 py-1.5 rounded-xl text-left border transition cursor-pointer flex flex-col justify-between border-slate-200 bg-slate-50/70 hover:bg-white hover:border-slate-300">
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-[10px] text-slate-500 font-medium">½ Halaman</span>
                                            <span class="preset-check text-kp-blue-600 hidden"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                        </div>
                                        <div class="text-xs font-semibold text-slate-900">7 kol × 270 mm</div>
                                        <div class="text-[9px] font-mono text-slate-400">1.890 mmk</div>
                                    </button>

                                    <button type="button" onclick="setDimensi(7, 540)" data-kol="7" data-mm="540"
                                            class="preset-dimensi-btn px-2.5 py-1.5 rounded-xl text-left border transition cursor-pointer flex flex-col justify-between border-slate-200 bg-slate-50/70 hover:bg-white hover:border-slate-300 sm:col-span-2">
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-[10px] text-slate-500 font-medium">1 Halaman Penuh</span>
                                            <span class="preset-check text-kp-blue-600 hidden"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                                        </div>
                                        <div class="text-xs font-semibold text-slate-900">7 kol × 540 mm</div>
                                        <div class="text-[9px] font-mono text-slate-400">3.780 mmk</div>
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                                <div class="sm:col-span-4">
                                    <label class="text-xs font-medium text-slate-700 mb-1 block">
                                        Lebar (Kolom) <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" name="lebar_kolom" id="lebar_kolom" min="1" max="7"
                                               value="{{ old('lebar_kolom', 2) }}" oninput="handleDimensiChange()"
                                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                        <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-medium pointer-events-none">kolom</span>
                                    </div>
                                </div>

                                <div class="sm:col-span-4">
                                    <label class="text-xs font-medium text-slate-700 mb-1 block">
                                        Tinggi (mm) <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" name="tinggi_mm" id="tinggi_mm" min="10" max="540" step="5"
                                               value="{{ old('tinggi_mm', 100) }}" oninput="handleDimensiChange()"
                                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                        <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-medium pointer-events-none">mm</span>
                                    </div>
                                </div>

                                <div class="sm:col-span-4">
                                    <div class="h-10 px-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                                        <span class="text-xs font-medium text-slate-600">Total Luas:</span>
                                        <span id="labelTotalMmk" class="font-mono text-xs font-semibold text-slate-900">200 mmk</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Materi File -->
                            <div class="pt-1">
                                <label class="text-xs font-medium text-slate-700 mb-1 block">
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
                                            <span class="font-semibold text-kp-blue-600">Pilih file</span> atau tarik file ke sini
                                        </div>
                                        <p class="text-[10px] text-slate-400">PDF, JPG, PNG, TIFF (Maks. 15MB)</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan Tambahan -->
                        <div>
                            <label class="text-xs font-medium text-slate-700 mb-1 block">Catatan (Opsional)</label>
                            <input type="text" name="keterangan_materi" id="keterangan_materi"
                                   placeholder="Catatan khusus untuk cetak (opsional)..."
                                   value="{{ old('keterangan_materi') }}"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition">
                        </div>
                    </div>

                    <!-- SEKSI 4: JADWAL TAYANG FLEKSIBEL -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-xs font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-[11px] font-bold">4</span>
                                <span>Jadwal Terbit</span>
                            </span>
                            <div id="statusParitasTanggal" class="text-[11px] font-semibold px-2.5 py-1 rounded-lg border bg-emerald-50 text-emerald-800 border-emerald-200 inline-flex items-center gap-1.5 shadow-2xs">
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
                                                   value="{{ old('total_muatkoran', 1) }}"
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
                                            <span class="text-xs font-medium text-slate-700 whitespace-nowrap">Tanggal Terbit:</span>
                                            <span id="labelTotalDatesCount" class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-kp-blue-100 text-kp-blue-800 whitespace-nowrap">1 Tanggal</span>
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
                                            <span id="calendarMonthTitle" class="text-xs font-bold text-slate-900"></span>
                                            <button type="button" onclick="goToTodayCalendar()"
                                                    class="text-[10px] font-semibold text-kp-blue-600 hover:text-kp-blue-800 cursor-pointer underline">
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
                                    <div class="grid grid-cols-7 gap-1 text-center text-[11px] font-semibold text-slate-400">
                                        <span class="text-rose-500">Min</span>
                                        <span>Sen</span>
                                        <span>Sel</span>
                                        <span>Rab</span>
                                        <span>Kam</span>
                                        <span>Jum</span>
                                        <span>Sab</span>
                                    </div>

                                    <!-- Grid Hari Kalender -->
                                    <div id="calendarDaysGrid" class="grid grid-cols-7 gap-1 text-center text-xs">
                                        <!-- Rendered via JS -->
                                    </div>

                                    <!-- Legenda Simpel Ringkas (Seragam Persegi / Kotak) -->
                                    <div class="flex items-center justify-between text-[10px] text-slate-500 border-t border-slate-100 pt-2 px-0.5">
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
                                        <span id="infoTanggalBadge" class="text-[10px] font-medium px-2 py-0.5 rounded-md bg-slate-200 text-slate-700 whitespace-nowrap">
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
                            <h3 class="text-xs font-bold text-slate-900 tracking-tight whitespace-nowrap shrink-0">Simulasi Cetak Koran</h3>
                            <span id="simulatorHalamanBadge" class="text-[10.5px] font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 font-mono shadow-2xs whitespace-nowrap shrink-0">
                                Hal. 1 • Cover / Utama
                            </span>
                        </div>

                        <!-- Bar Kontrol Studio: Terintegrasi 1 Baris Presisi -->
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <!-- Posisi Slot (Segmented Control Apple/Figma Style) -->
                            <div class="flex items-center gap-1.5 min-w-0">
                                <span class="text-[11px] font-semibold text-slate-500 shrink-0">Posisi:</span>
                                <div id="containerSlotPosSelector" class="inline-flex p-0.5 rounded-lg bg-slate-100 border border-slate-200/80 text-[10px]">
                                    <button type="button" onclick="setSlotPosition('bottom-right')" id="posBtn_br"
                                            class="pos-slot-btn px-2.5 py-1 rounded-md transition-all font-semibold cursor-pointer border border-kp-blue-500 bg-white text-kp-blue-700 shadow-2xs">
                                        Kanan
                                    </button>
                                    <button type="button" onclick="setSlotPosition('bottom-left')" id="posBtn_bl"
                                            class="pos-slot-btn px-2.5 py-1 rounded-md transition-all font-medium cursor-pointer text-slate-600 hover:text-slate-900 border border-transparent">
                                        Kiri
                                    </button>
                                    <button type="button" onclick="setSlotPosition('center')" id="posBtn_ct"
                                            class="pos-slot-btn px-2.5 py-1 rounded-md transition-all font-medium cursor-pointer text-slate-600 hover:text-slate-900 border border-transparent">
                                        Tengah
                                    </button>
                                    <button type="button" onclick="setSlotPosition('top-ear')" id="posBtn_te"
                                            class="pos-slot-btn px-2.5 py-1 rounded-md transition-all font-medium cursor-pointer text-slate-600 hover:text-slate-900 border border-transparent">
                                        Kuping
                                    </button>
                                </div>
                            </div>

                            <!-- Tools Tampilan: Garis Kolom & Perbesar -->
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" onclick="toggleSimulatorGrid()" id="btnSimulatorGrid"
                                        class="h-7 px-2.5 text-[10px] font-medium rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition flex items-center gap-1 shadow-2xs cursor-pointer"
                                        title="Tampilkan Garis Kolom">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M9 4v16m6-16v16"/></svg>
                                    <span>Garis Kolom</span>
                                </button>
                                <button type="button" onclick="toggleSimulatorZoom()" id="btnSimulatorZoom"
                                        class="h-7 px-2.5 text-[10px] font-medium rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition flex items-center gap-1 shadow-2xs cursor-pointer"
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

                                <span>Rincian Biaya</span>
                            </span>
                            <span id="badgeStatusBayar" class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                Menunggu Pembayaran
                            </span>
                        </div>

                        <!-- Baris Rincian Biaya -->
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between text-slate-600">
                                <span>Tarif Satuan:</span>
                                <span id="strukTarifDasar" class="font-mono font-semibold text-slate-900">Rp 22.750 / baris</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Ukuran:</span>
                                <span id="strukDimensi" class="font-mono font-semibold text-slate-900">2 Baris</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Frekuensi:</span>
                                <span id="strukFrekuensi" class="font-mono font-semibold text-slate-900">1 Edisi</span>
                            </div>

                            <div class="pt-2 border-t border-slate-100 flex justify-between text-slate-800 font-semibold">
                                <span>Subtotal:</span>
                                <span id="strukBruto" class="font-mono font-bold text-slate-900">Rp 45.500</span>
                            </div>

                            <!-- Input Diskon -->
                            <div class="pt-2 border-t border-slate-100 grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[10px] font-medium text-slate-600 block mb-0.5">Diskon (%)</label>
                                    <input type="number" name="diskon_agensi" id="diskon_agensi" min="0" max="100" value="0"
                                           oninput="recalculateFinancials()"
                                           class="w-full h-8 px-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono font-semibold text-slate-800">
                                </div>
                                <div>
                                    <label class="text-[10px] font-medium text-slate-600 block mb-0.5">Potongan (Rp)</label>
                                    <input type="number" name="diskon_khusus" id="diskon_khusus" min="0" value="0" step="5000"
                                           oninput="recalculateFinancials()"
                                           class="w-full h-8 px-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono font-semibold text-slate-800">
                                </div>
                            </div>

                            <!-- DPP & PPN -->
                            <div class="pt-2 border-t border-slate-100 space-y-1.5">
                                <div class="flex justify-between text-slate-600">
                                    <span>DPP:</span>
                                    <span id="strukDpp" class="font-mono text-slate-800">Rp 40.625</span>
                                </div>
                                <div class="flex justify-between text-slate-600">
                                    <span>PPN ({{ $configs['ppn_persen'] }}%):</span>
                                    <span id="strukPpn" class="font-mono text-slate-800">Rp 4.875</span>
                                </div>
                                <div id="rowMaterai" class="flex justify-between text-slate-600 hidden">
                                    <span>Meterai:</span>
                                    <span id="strukMaterai" class="font-mono text-slate-800">Rp 10.000</span>
                                </div>
                                <input type="hidden" name="materai" id="inputMaterai" value="0">
                            </div>

                            <!-- Total Netto Akhir -->
                            <div class="pt-2.5 border-t-2 border-slate-800 flex justify-between items-baseline">
                                <span class="text-xs font-bold text-slate-900 uppercase tracking-wide">Total Tagihan:</span>
                                <span id="strukTotalNetto" class="font-mono text-base font-bold text-kp-blue-600">Rp 45.500</span>
                            </div>

                            <!-- Input Pembayaran Kasir / DP -->
                            <div class="pt-3 border-t border-slate-100 space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-medium text-slate-700">Nominal Bayar:</label>
                                    <button type="button" onclick="setBayarLunas()" class="text-[10px] font-semibold text-kp-blue-600 hover:underline cursor-pointer">
                                        Bayar Lunas
                                    </button>
                                </div>
                                <input type="number" name="jumlahbayar_transaksikoran" id="jumlahbayar_transaksikoran" min="0" value="0"
                                       oninput="handleBayarInput()"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                
                                <div class="flex justify-between items-center text-xs pt-1 font-semibold">
                                    <span class="text-slate-500">Sisa Tagihan:</span>
                                    <span id="strukPiutang" class="font-mono text-slate-900">Rp 45.500</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Submit Transaksi -->
                        <div class="pt-4 border-t border-slate-100 space-y-2">
                            <button type="submit" id="btnSimpanOrder"
                                    class="w-full py-3 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                <span>Simpan Transaksi</span>
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </form>

        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- MODAL TAMBAH PELANGGAN BARU                                    -->
        <!-- ============================================================== -->
        <div id="modalPelangganBaru" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
                
                <!-- Header Modal: Khas Kabar Priangan dengan Aksen Biru Elegan -->
                <div class="px-6 py-4 bg-gradient-to-r from-kp-blue-50/90 via-white to-blue-50/30 border-b border-kp-blue-100/80 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-kp-blue-600 text-white flex items-center justify-center shadow-xs shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 tracking-tight">Tambah Pelanggan Baru</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5 font-normal">Daftarkan data pemesan iklan ke buku pelanggan</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModalPelangganBaru()" 
                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer text-lg leading-none"
                            title="Tutup Modal">&times;</button>
                </div>

                <!-- Form Body -->
                <div class="p-5 sm:p-6 space-y-3.5 text-xs">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Nama Pemesan / Instansi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="modal_nama_instansi" placeholder="Contoh: PT. Sumber Barokah / H. Ahmad Sanusi"
                               class="w-full h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-normal text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Kategori Pelanggan
                            </label>
                            <select id="modal_jenis_klien" 
                                    class="w-full h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-normal text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                <option value="Perorangan">Perorangan</option>
                                <option value="Swasta">Perusahaan / Swasta</option>
                                <option value="Pemda">Instansi / Dinas Pemda</option>
                                <option value="Agensi">Biro Iklan / Agensi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                Nomor WhatsApp / HP
                            </label>
                            <input type="text" id="modal_no_telp" placeholder="Contoh: 081234567890" 
                                   class="w-full h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-normal text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Alamat Lengkap Kantor / Domisili
                        </label>
                        <textarea id="modal_alamat" rows="2" placeholder="Alamat lengkap jalan, kelurahan, kecamatan..." 
                                  class="w-full p-2.5 bg-white border border-slate-300 rounded-lg text-xs font-normal text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                    </div>
                </div>

                <!-- Footer: Kontras Tinggi, Selaras & Khas KP -->
                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeModalPelangganBaru()" 
                            class="px-4 py-2 bg-white hover:bg-slate-100 active:bg-slate-200 border border-slate-300 text-slate-700 hover:text-slate-900 rounded-lg text-xs font-medium transition shadow-2xs cursor-pointer">
                        Batal
                    </button>
                    <button type="button" onclick="simpanPelangganOnTheFly()" 
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
        <!-- MODAL TAMBAH SALES / AE                                         -->
        <!-- ============================================================== -->
        <div id="modalSalesAeBaru" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
                
                <!-- Header Modal: Khas Kabar Priangan dengan Aksen Biru Elegan -->
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
                                       placeholder="S01 / K01">
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
                        <input type="text" id="modal_sales_nama" placeholder="Contoh: Budi Santoso"
                               class="w-full h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-normal text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                </div>

                <!-- Footer: Kontras Tinggi, Selaras & Khas KP -->
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

    <!-- JAVASCRIPT ENGINE UNTUK 2026 REACTIVE EXPERIENCE -->
    <script>
        // State Global Transaksi
        let currentProduct = {
            id: {{ $iklankoran->first()->id ?? 1 }},
            kode: '{{ $iklankoran->first()->kode_jenis ?? "BARIS" }}',
            tipe: '{{ $iklankoran->first()->tipe_dasar ?? "BARIS" }}',
            warna: '{{ $iklankoran->first()->mode_warna ?? "BW" }}',
            caraHitung: '{{ $iklankoran->first()->cara_hitung ?? "PER_BARIS" }}',
            tarif: {{ $iklankoran->first()->tarif_dasar ?? 22750 }},
            isFixed: {{ ($iklankoran->first()->is_fixed_size ?? true) ? 'true' : 'false' }},
            kolom: {{ $iklankoran->first()->default_kolom ?? 1 }},
            tinggi: {{ $iklankoran->first()->default_tinggi_mm ?? 0 }},
            minOrder: {{ $iklankoran->first()->min_order ?? 2 }}
        };

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
            const oldTanggalList = "{{ old('tanggal_muat_list') }}";
            if (oldTanggalList) {
                selectedDates = oldTanggalList.split(',').map(s => s.trim()).filter(Boolean);
            }
            if (selectedDates.length === 0) {
                selectedDates = [tomorrowStr];
            }
            activeDetailDate = selectedDates[0] || tomorrowStr;

            // Inisialisasi Naskah Baris dari old input jika ada
            const oldNaskah = @json(old('naskah_iklan_baris', ''));
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

            handleDimensiChange();
            recalculateFinancials();
            updateSimulator();

            // Realtime update simulator on typing client name & phone
            const manualNameInput = document.getElementById('inputManualNamaPemasang');
            if (manualNameInput) manualNameInput.addEventListener('input', () => updateSimulator());
            const telpInput = document.getElementById('telepon_pemasangkoran');
            if (telpInput) telpInput.addEventListener('input', () => updateSimulator());

            // Set initial jenis pembayaran UI
            const initialJenis = document.getElementById('jenis_pembayaran').value || 'Tunai';
            setJenisPembayaran(initialJenis);

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
                if (badge) badge.className = "px-1.5 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-600 font-medium";
            });

            const activeBtn = document.getElementById('tabBtn_' + targetTab);
            if (activeBtn) {
                activeBtn.className = "tab-product-btn px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5 bg-kp-blue-600 text-white font-semibold shadow-xs border border-kp-blue-600";
                const badge = activeBtn.querySelector('span:last-child');
                if (badge) badge.className = "px-1.5 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-semibold";
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

        function pilihProdukIklan(id, kode, tipe, warna, caraHitung, tarif, isFixed, defaultKolom, defaultTinggi, minOrder) {
            currentProduct = {
                id, kode, tipe, warna, caraHitung, tarif, isFixed,
                kolom: defaultKolom || (tipe === 'KOLOM' || tipe === 'DISPLAY' ? 2 : 1),
                tinggi: defaultTinggi || 50,
                minOrder: minOrder || (tipe === 'BARIS' ? 2 : 1)
            };

            // Update UI card aktif
            document.querySelectorAll('.produk-card').forEach(card => {
                card.className = "produk-card text-left p-3 rounded-xl border transition cursor-pointer relative flex flex-col justify-between gap-2 border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50";
            });
            const activeCard = document.getElementById('cardProduk_' + id);
            if (activeCard) {
                activeCard.className = "produk-card text-left p-3 rounded-xl border transition cursor-pointer relative flex flex-col justify-between gap-2 border-kp-blue-600 bg-kp-blue-50/40 ring-2 ring-kp-blue-500/20 shadow-xs";
            }

            // Sync Hidden Form Fields
            document.getElementById('id_iklankoran').value = id;
            document.getElementById('kategori_form').value = tipe;
            document.getElementById('warna_iklan').value = warna;

            // Tampilkan section yang relevan
            const secBaris = document.getElementById('sectionIklanBaris');
            const secDisplay = document.getElementById('sectionIklanDisplay');
            const contKopIklan = document.getElementById('containerKopIklan');

            if (tipe === 'BARIS') {
                secBaris.classList.remove('hidden');
                secDisplay.classList.add('hidden');
                contKopIklan.classList.remove('hidden');
                syncBarisNaskah();
            } else {
                secBaris.classList.add('hidden');
                secDisplay.classList.remove('hidden');
                contKopIklan.classList.add('hidden');

                if (isFixed) {
                    document.getElementById('lebar_kolom').value = defaultKolom;
                    document.getElementById('tinggi_mm').value = defaultTinggi;
                } else if (tipe === 'KOLOM' || tipe === 'DISPLAY') {
                    // Minimal 2 kolom untuk kolom/display
                    const curKol = parseInt(document.getElementById('lebar_kolom').value) || 1;
                    if (curKol < 2) {
                        document.getElementById('lebar_kolom').value = 2;
                    }
                }
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
                numBadge.className = "w-16 text-center py-1 rounded-lg bg-slate-200/80 text-slate-700 font-mono text-[11px] font-bold shrink-0";
                numBadge.textContent = `Baris ${rowNum}`;
                rowDiv.appendChild(numBadge);

                // Input Text Teks (Maks 32 chars)
                const input = document.createElement('input');
                input.type = 'text';
                input.maxLength = 32;
                input.value = text;
                input.placeholder = `Tulis naskah baris ${rowNum} (maks. 32 karakter)...`;
                input.className = "flex-1 h-8 px-2 bg-transparent text-xs font-mono font-medium text-slate-900 focus:outline-none";
                input.oninput = (e) => handleSingleBarisInput(index, e.target.value);
                rowDiv.appendChild(input);

                // Character Counter
                const counter = document.createElement('span');
                counter.id = `barisCounter_${index}`;
                const len = text.length;
                counter.className = `w-14 text-right font-mono text-[11px] font-bold shrink-0 ${len === 32 ? 'text-amber-600' : (len > 0 ? 'text-emerald-700' : 'text-slate-400')}`;
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
                counter.className = `w-14 text-right font-mono text-[11px] font-bold shrink-0 ${len === 32 ? 'text-amber-600' : (len > 0 ? 'text-emerald-700' : 'text-slate-400')}`;
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
                previewBox.innerHTML = '<div class="text-slate-400 italic text-[11px] py-1 text-center">(Ketik naskah baris 1 dan baris 2 di atas...)</div>';
                totalCharSpan.textContent = '0 Karakter Total';
                return;
            }

            filledLines.forEach((line, i) => {
                totalChars += line.length;
                const lineDiv = document.createElement('div');
                lineDiv.className = "py-1 flex items-baseline justify-between gap-2";
                lineDiv.innerHTML = `
                    <span class="font-bold text-slate-800 tracking-wider uppercase">${line}</span>
                    <span class="text-[9px] text-slate-400 font-mono shrink-0">${line.length} ch</span>
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

            // Batas Fisik 1-7 Kolom & 10-540 mm
            if (kol > 7) { kol = 7; document.getElementById('lebar_kolom').value = 7; }
            if (kol < 1) { kol = 1; document.getElementById('lebar_kolom').value = 1; }
            if (mm > 540) { mm = 540; document.getElementById('tinggi_mm').value = 540; }
            if (mm < 10) { mm = 10; document.getElementById('tinggi_mm').value = 10; }

            const totalMmk = kol * mm;
            document.getElementById('labelTotalMmk').textContent = totalMmk.toLocaleString('id-ID') + ' mmk';
            document.getElementById('ukuran_iklan').value = `${kol} kol × ${mm} mm`;

            updatePresetActiveState(kol, mm);
            recalculateFinancials();
            updateSimulator();
        }

        function setDimensi(kol, mm) {
            document.getElementById('lebar_kolom').value = kol;
            document.getElementById('tinggi_mm').value = mm;
            handleDimensiChange();
        }

        function updatePresetActiveState(kol, mm) {
            document.querySelectorAll('.preset-dimensi-btn').forEach(btn => {
                const btnKol = parseInt(btn.dataset.kol);
                const btnMm = parseInt(btn.dataset.mm);
                const check = btn.querySelector('.preset-check');
                if (btnKol === kol && btnMm === mm) {
                    btn.className = "preset-dimensi-btn px-2.5 py-1.5 rounded-xl text-left border-2 transition cursor-pointer flex flex-col justify-between border-kp-blue-600 bg-kp-blue-50/80 text-kp-blue-900 shadow-xs ring-2 ring-kp-blue-500/20";
                    if (check) check.classList.remove('hidden');
                } else {
                    btn.className = "preset-dimensi-btn px-2.5 py-1.5 rounded-xl text-left border transition cursor-pointer flex flex-col justify-between border-slate-200 bg-slate-50/60 hover:bg-white hover:border-slate-300 text-slate-700";
                    if (check) check.classList.add('hidden');
                }
            });
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

        function setSlotPosition(pos) {
            currentSlotPosition = pos;
            document.querySelectorAll('.pos-slot-btn').forEach(btn => {
                btn.className = "pos-slot-btn px-2.5 py-1 rounded-md transition-all font-medium cursor-pointer text-slate-600 hover:text-slate-900 border border-transparent";
            });
            const key = pos === 'bottom-right' ? 'br' : (pos === 'bottom-left' ? 'bl' : (pos === 'center' ? 'ct' : 'te'));
            const activeBtn = document.getElementById(`posBtn_${key}`);
            if (activeBtn) {
                activeBtn.className = "pos-slot-btn px-2.5 py-1 rounded-md transition-all font-semibold cursor-pointer border border-kp-blue-500 bg-white text-kp-blue-700 shadow-2xs";
            }
            updateSimulator();
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

            // Position Handling
            if (wrapper) {
                if (currentSlotPosition === 'bottom-left') {
                    wrapper.className = "relative z-30 w-full flex justify-start mt-auto transition-all duration-300";
                } else if (currentSlotPosition === 'center') {
                    wrapper.className = "relative z-30 w-full flex justify-center my-auto transition-all duration-300";
                } else if (currentSlotPosition === 'top-ear') {
                    wrapper.className = "relative z-30 w-full flex justify-end mb-auto transition-all duration-300";
                } else {
                    wrapper.className = "relative z-30 w-full flex justify-end mt-auto transition-all duration-300";
                }
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
            if (typeof item === 'object' && item.nama) return item.nama;
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
                    <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-md bg-kp-blue-600 text-white shadow-2xs">
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
                    <div class="p-3 text-center rounded-xl border border-dashed border-slate-300 bg-white text-[11px] text-slate-400">
                        Belum ada tanggal dipilih. Klik tanggal di kalender.
                    </div>
                `;
            } else {
                selectedDates.forEach((d, i) => {
                    const chip = document.createElement('div');
                    chip.className = "flex items-center justify-between px-2.5 py-1.5 rounded-xl bg-white border border-slate-200 hover:border-kp-blue-300 transition shadow-2xs group whitespace-nowrap";
                    chip.innerHTML = `
                        <div class="flex items-center gap-2 cursor-pointer whitespace-nowrap" onclick="inspectSelectedDate('${d}')">
                            <span class="px-1.5 py-0.5 rounded bg-kp-blue-50 text-kp-blue-700 text-[10px] font-bold font-mono">#${i+1}</span>
                            <span class="text-xs font-bold text-slate-800 group-hover:text-kp-blue-700 transition">${formatIndoChip(d)}</span>
                        </div>
                        <button type="button" onclick="removeSingleDate('${d}')"
                                title="Hapus tanggal ${d}"
                                class="w-6 h-6 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition cursor-pointer text-sm font-bold ml-1 shrink-0">
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
                    <span class="text-[10px] font-bold font-mono px-2 py-0.5 rounded-md bg-white text-kp-blue-700 border border-kp-blue-200 shrink-0 shadow-2xs">
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
                badge.className = "text-[11px] font-semibold px-2.5 py-1 rounded-lg border bg-amber-50 text-amber-800 border-amber-200 inline-flex items-center gap-1.5 shadow-2xs";
                badge.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Isi Frekuensi</span>
                `;
                return;
            }

            const total = parseInt(rawVal);

            if (count === total) {
                badge.className = "text-[11px] font-semibold px-2.5 py-1 rounded-lg border bg-emerald-50 text-emerald-800 border-emerald-200 inline-flex items-center gap-1.5 shadow-2xs";
                badge.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>${count} Tanggal Dipilih</span>
                `;
            } else if (count < total) {
                badge.className = "text-[11px] font-semibold px-2.5 py-1 rounded-lg border bg-sky-50 text-sky-800 border-sky-200 inline-flex items-center gap-1.5 shadow-2xs";
                badge.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Kurang ${total - count} Tanggal</span>
                `;
            } else {
                badge.className = "text-[11px] font-semibold px-2.5 py-1 rounded-lg border bg-rose-50 text-rose-800 border-rose-200 inline-flex items-center gap-1.5 shadow-2xs";
                badge.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Lebih ${count - total} Tanggal</span>
                `;
            }
        }

        // ==============================================================
        // KALKULASI FINANSIAL REAL-TIME (TRANSPARAN)
        // ==============================================================
        function recalculateFinancials() {
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
                brutoPerMuat = currentProduct.tarif;
                dimensiLabel = 'Paket Modul Tetap';
                tarifLabel = `Rp ${currentProduct.tarif.toLocaleString('id-ID')}`;
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

            // Diskon
            const diskonPersen = parseFloat(document.getElementById('diskon_agensi').value) || 0;
            const diskonKhusus = parseFloat(document.getElementById('diskon_khusus').value) || 0;
            const nominalDiskonPersen = Math.round(totalBruto * (diskonPersen / 100));
            const totalDiskon = nominalDiskonPersen + diskonKhusus;

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
            const raw = parseCurrency(el.value);
            el.value = raw > 0 ? raw.toLocaleString('id-ID') : '';
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
            const raw = parseCurrency(el.value);
            el.value = raw > 0 ? raw.toLocaleString('id-ID') : '';
            const dpp = parseCurrency(document.getElementById('strukDpp').textContent) || 0;
            if (dpp > 0) {
                const persen = Math.round((raw / dpp) * 1000) / 10;
                document.getElementById('komisi_persen').value = persen;
            }
        }

        function handleBayarInput(grandTotalCalc) {
            const grandTotal = grandTotalCalc !== undefined ? grandTotalCalc : parseCurrency(document.getElementById('strukTotalNetto').textContent);
            let bayar = parseFloat(document.getElementById('jumlahbayar_transaksikoran').value) || 0;
            const sisaPiutang = Math.max(0, grandTotal - bayar);

            document.getElementById('strukPiutang').textContent = `Rp ${sisaPiutang.toLocaleString('id-ID')}`;

            const badge = document.getElementById('badgeStatusBayar');
            const jenis = document.getElementById('jenis_pembayaran').value;

            if (sisaPiutang <= 0 && grandTotal > 0) {
                badge.className = "text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200";
                badge.textContent = "Lunas";
            } else if (jenis === 'Tunai') {
                badge.className = "text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200";
                badge.textContent = "Menunggu Pembayaran";
            } else {
                badge.className = "text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200";
                badge.textContent = "Belum Lunas";
            }
        }

        function setBayarLunas() {
            const grandTotal = parseCurrency(document.getElementById('strukTotalNetto').textContent);
            document.getElementById('jumlahbayar_transaksikoran').value = grandTotal;
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
                    ? `<span class="text-[10px] font-semibold text-rose-600 block">Piutang: Rp ${piutangVal.toLocaleString('id-ID')}</span>`
                    : `<span class="text-[10px] font-medium text-emerald-600 block">Piutang: Rp 0</span>`;

                return `
                    <button type="button" 
                            onclick="selectPelangganByIndex(${idx})"
                            class="pelanggan-item-btn w-full text-left p-2.5 rounded-xl hover:bg-kp-blue-50/70 transition flex items-center justify-between gap-3 group cursor-pointer border border-transparent hover:border-kp-blue-200">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-semibold text-xs text-slate-900 group-hover:text-kp-blue-700">${item.nama_instansi}</span>
                                <span class="px-1.5 py-0.2 text-[9px] font-semibold rounded bg-slate-100 text-slate-700 border border-slate-200 uppercase">${item.jenis_klien || 'Umum'}</span>
                                <span class="text-[10px] font-mono text-slate-400 font-medium">[${item.kode_pelanggan || 'PLG'}]</span>
                            </div>
                            <div class="text-[11px] text-slate-500 truncate mt-0.5 flex items-center gap-2">
                                ${item.no_telp ? `<span class="inline-flex items-center gap-1"><svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>${item.no_telp}</span>` : ''}
                                ${item.alamat ? `<span class="inline-flex items-center gap-1 truncate max-w-[200px]"><svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>${item.alamat}</span>` : ''}
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            ${piutangBadge}
                            <span class="text-[10px] text-kp-blue-600 font-semibold group-hover:underline">Pilih ↵</span>
                        </div>
                    </button>
                `;
            }).join('');

            // Tampilkan opsi pilih teks bebas jika user sedang mengetik
            if (currentQuery && currentQuery.length > 1) {
                html += `
                    <div class="p-2 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-2 mt-1 rounded-b-xl">
                        <span class="text-[11px] text-slate-600 truncate">Bukan pelanggan master di atas?</span>
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
                badgeHtml = `<span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200/80 px-2 py-0.5 rounded-full">Perhatian</span>`;
            } else if (type === 'error') {
                iconHtml = `
                    <div class="w-9 h-9 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                    </div>`;
                defaultTitle = 'Peringatan Operasional';
                badgeHtml = `<span class="text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200/80 px-2 py-0.5 rounded-full">Kendala</span>`;
            } else if (type === 'success') {
                iconHtml = `
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>`;
                defaultTitle = 'Berhasil Tersimpan';
                badgeHtml = `<span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 rounded-full">Sukses</span>`;
            } else {
                iconHtml = `
                    <div class="w-9 h-9 rounded-xl bg-kp-blue-500/10 border border-kp-blue-500/20 text-kp-blue-600 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </div>`;
                defaultTitle = 'Informasi Transaksi';
                badgeHtml = `<span class="text-[10px] font-bold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200/80 px-2 py-0.5 rounded-full">Info</span>`;
            }

            toast.innerHTML = `
                ${iconHtml}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="text-xs font-bold text-slate-900 tracking-tight">${title || defaultTitle}</h4>
                        ${badgeHtml}
                    </div>
                    <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">${message}</p>
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
            document.getElementById('modalPelangganBaru').classList.remove('hidden');
        }

        function closeModalPelangganBaru() {
            document.getElementById('modalPelangganBaru').classList.add('hidden');
        }

        function simpanPelangganOnTheFly() {
            const nama = document.getElementById('modal_nama_instansi').value.trim();
            const jenis = document.getElementById('modal_jenis_klien').value;
            const telp = document.getElementById('modal_no_telp').value.trim();
            const alamat = document.getElementById('modal_alamat').value.trim();

            if (!nama) {
                showCustomToast('Nama instansi/pemasang wajib diisi!', 'warning', 'Form Belum Lengkap');
                return;
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
                    alamat: alamat
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' || data.data) {
                    const plg = data.data;
                    initialPelanggansCache.unshift(plg);
                    selectPelangganItem(plg);
                    closeModalPelangganBaru();
                    showCustomToast(`Pelanggan ${plg.nama_instansi} berhasil didaftarkan & dipilih.`, 'success', 'Pelanggan Baru');
                } else {
                    showCustomToast('Gagal menambahkan pelanggan: ' + (data.message || 'Terjadi kesalahan'), 'error', 'Peringatan');
                }
            })
            .catch(err => {
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
                btn.disabled = false;
                btn.innerText = 'Simpan & Pilih';
                showCustomToast('Terjadi kesalahan: ' + err.message, 'error');
            });
        }
    </script>

    <!-- Global Toast Container -->
    <div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2.5 pointer-events-none max-w-sm"></div>
</x-app-layout>
