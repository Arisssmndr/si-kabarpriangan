<x-app-layout>
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Pemasangan Iklan Koran (Front Office Digital)
                    </h1>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Formulir digital resmi Harian Umum Kabar Priangan — Menggantikan Formulir Kertas Manual
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('transaksikoran.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    &larr; <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>

        <!-- 1. Advertising Deadline & Cut-off Notice Banner (14:00 WIB) -->
        @php
            $isPastCutoff = $configs['is_past_cutoff'];
            $besok = now()->addDay()->translatedFormat('l, d F Y');
            $lusa  = now()->addDays(2)->translatedFormat('l, d F Y');
        @endphp
        <div class="rounded-xl p-4 border flex items-start gap-3.5 {{ $isPastCutoff ? 'bg-amber-50/80 border-amber-300 text-amber-900' : 'bg-emerald-50/80 border-emerald-300 text-emerald-900' }}">
            <div class="p-2 rounded-lg shrink-0 {{ $isPastCutoff ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1 text-xs">
                <div class="flex items-center justify-between font-bold text-sm mb-0.5">
                    <span>
                        {{ $isPastCutoff ? '⚠️ Telah Melewati Cut-Off Iklan Harian (' . $configs['jam_cutoff'] . ' WIB)' : '✅ Dalam Batas Waktu Terbit Cetak (Cut-Off ' . $configs['jam_cutoff'] . ' WIB)' }}
                    </span>
                    <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-white/70 border border-current">
                        Waktu Server: {{ now()->format('H:i') }} WIB
                    </span>
                </div>
                <p class="font-normal opacity-90 leading-relaxed">
                    @if($isPastCutoff)
                        Pemesanan iklan setelah pukul {{ $configs['jam_cutoff'] }} WIB secara otomatis diarahkan untuk terbit pada edisi <strong>{{ $lusa }}</strong>. Jika memerlukan dispensasi terbit besok, hubungi Bagian Redaksi & Pracetak.
                    @else
                        Materi iklan yang masuk sebelum pukul {{ $configs['jam_cutoff'] }} WIB dapat diterbitkan pada koran edisi esok hari: <strong>{{ $besok }}</strong>.
                    @endif
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800">
                <strong class="font-semibold block mb-1">Terdapat kesalahan input:</strong>
                <ul class="list-disc list-inside space-y-0.5 font-normal">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulir Transaksi Utama -->
        <form id="formTransaksiKoran" method="POST" action="{{ route('transaksikoran.store') }}" class="space-y-6">
            @csrf

            <!-- Hidden Fields untuk State -->
            <input type="hidden" name="nofakturkoran" value="{{ $nofakturkoran }}">
            <input type="hidden" name="ppn_persen" id="ppn_persen" value="{{ $configs['ppn_persen'] }}">
            <input type="hidden" name="kategori_form" id="kategori_form_input" value="{{ old('kategori_form', 'BARIS') }}">

            <!-- TAB SWITCHER: Form 1 (Baris) vs Form 2 (Kolom/Display/Adv) -->
            <div class="bg-white border border-slate-200 rounded-xl p-2 shadow-xs">
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" id="tabBtnBaris" onclick="switchFormTab('BARIS')"
                            class="py-3 px-4 rounded-lg font-bold text-xs flex items-center justify-center gap-2.5 transition cursor-pointer bg-kp-blue-600 text-white shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                        <span>FORMULIR 1 — IKLAN BARIS (Classified Ads)</span>
                    </button>
                    <button type="button" id="tabBtnKolom" onclick="switchFormTab('KOLOM')"
                            class="py-3 px-4 rounded-lg font-bold text-xs flex items-center justify-center gap-2.5 transition cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                        </svg>
                        <span>FORMULIR 2 — KOLOM, DISPLAY & ADVERTORIAL</span>
                    </button>
                </div>
            </div>

            <!-- BAGIAN 1: IDENTITAS PEMASANG & KANAL PENJUALAN -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-5">
                <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                            Data Pemesan & Kanal Penjualan
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Integrasi Master CRM Pelanggan & Pelacakan Komisi Sales</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-slate-400">No. Faktur:</span>
                        <span class="ml-1 text-xs font-mono font-bold text-kp-blue-700 bg-kp-blue-50 px-2 py-0.5 rounded border border-kp-blue-200">{{ $nofakturkoran }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Kanal Penjualan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kanal Penjualan <span class="text-red-500">*</span>
                        </label>
                        <select name="kanal_order" id="kanal_order" required onchange="handleKanalChange()"
                                class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <option value="FO" {{ old('kanal_order') == 'FO' ? 'selected' : '' }}>FO — Front Office (Walk-in Counter)</option>
                            <option value="AE" {{ old('kanal_order') == 'AE' ? 'selected' : '' }}>AE — Account Executive (Sales Lapangan)</option>
                            <option value="BIRO_IKLAN" {{ old('kanal_order') == 'BIRO_IKLAN' ? 'selected' : '' }}>BIRO IKLAN — Agensi Periklanan</option>
                            <option value="KORP" {{ old('kanal_order') == 'KORP' ? 'selected' : '' }}>KORP — Kerjasama Korporat / Pemda</option>
                        </select>
                    </div>

                    <!-- Pilih Pelanggan (CRM) -->
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Pelanggan Terdaftar (CRM)
                            </label>
                            <a href="{{ route('pelanggan.create') }}" target="_blank" class="text-[11px] font-semibold text-kp-blue-600 hover:underline">
                                + Tambah Klien Baru
                            </a>
                        </div>
                        <select name="pelanggan_id" id="pelanggan_id" onchange="pilihPelanggan(this)"
                                class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <option value="">-- Pilih dari Master Pelanggan (atau ketik manual di bawah) --</option>
                            @foreach($pelanggans as $plg)
                                <option value="{{ $plg->id }}" 
                                        data-nama="{{ $plg->nama_instansi }}"
                                        data-alamat="{{ $plg->alamat }}"
                                        data-telp="{{ $plg->no_telp }}"
                                        data-jenis="{{ $plg->jenis_klien }}"
                                        data-credit="{{ $plg->credit_limit }}"
                                        data-piutang="{{ $plg->total_piutang_koran }}"
                                        {{ old('pelanggan_id') == $plg->id ? 'selected' : '' }}>
                                    [{{ $plg->kode_pelanggan }}] {{ $plg->nama_instansi }} ({{ $plg->jenis_klien }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Info Box Pelanggan Terpilih -->
                <div id="pelangganInfoCard" class="hidden p-3.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Jenis Klien</span>
                        <span id="infoJenisKlien" class="font-bold text-slate-900">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Plafon Kredit</span>
                        <span id="infoCreditLimit" class="font-bold text-slate-900">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Total Piutang Berjalan</span>
                        <span id="infoPiutangBerjalan" class="font-bold text-rose-600">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">SOP Pembayaran</span>
                        <span id="infoSopBayar" class="font-semibold text-amber-700">-</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nama Instansi / Pemasang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_pemasangkoran" id="nama_pemasangkoran" value="{{ old('nama_pemasangkoran') }}" required placeholder="Nama instansi atau pemohon iklan"
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                No. Telepon / HP Pemasang
                            </label>
                            <input type="text" name="no_telp" id="no_telp" value="{{ old('no_telp') }}" placeholder="Nomor kontak/WhatsApp"
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nama Sales / Petugas FO <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="sales_iklankoran" id="sales_iklankoran" value="{{ old('sales_iklankoran', Auth::user()->name ?? 'Counter FO') }}" required
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tanggal Order Transaksi <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_transaksikoran" value="{{ old('tanggal_transaksikoran', date('Y-m-d')) }}" required
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Lengkap Pemasang <span class="text-red-500">*</span>
                            </label>
                            <textarea name="alamat_pemasangkoran" id="alamat_pemasangkoran" rows="4" required placeholder="Alamat lengkap instansi/pemasang..."
                                      class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">{{ old('alamat_pemasangkoran') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB KONTEN 1: FORMULIR IKLAN BARIS -->
            <div id="sectionFormBaris" class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-5">
                <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                            Spesifikasi & Teks Iklan Baris (Classified Ads)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Penghitungan otomatis baris dan simulasi kolom cetak koran</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-kp-blue-100 text-kp-blue-800">
                        Tarif: Rp {{ number_format($configs['tarif_per_baris'], 0, ',', '.') }} / Baris
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kop Iklan / Rubrik Wilayah
                        </label>
                        <select name="kop_iklan_kota" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <option value="Tasikmalaya">Tasikmalaya (Kota & Kab)</option>
                            <option value="Ciamis">Ciamis</option>
                            <option value="Garut">Garut</option>
                            <option value="Banjar">Kota Banjar</option>
                            <option value="Pangandaran">Pangandaran</option>
                            <option value="Sumedang">Sumedang</option>
                            <option value="Priangan Timur">Priangan Timur (Umum)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Produk / Jasa
                        </label>
                        <input type="text" name="produk_jasa" value="{{ old('produk_jasa') }}" placeholder="Contoh: Tanah Dijual / Lowongan / Motor"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tanggal Edisi Terbit <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_muatkoran" id="tanggal_muat_baris" 
                               value="{{ old('tanggal_muatkoran', $isPastCutoff ? date('Y-m-d', strtotime('+2 days')) : date('Y-m-d', strtotime('+1 day'))) }}" required
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>
                </div>

                <!-- Textarea Naskah Iklan Baris dengan Live Grid Counter -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Naskah Iklan Baris (Ketik Langsung di Sini)
                        </label>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="text-slate-500 font-mono"><strong id="charCount" class="text-slate-900">0</strong> Karakter</span>
                            <span class="text-slate-300">•</span>
                            <span class="px-2 py-0.5 rounded font-bold font-mono text-[11px] bg-indigo-50 text-indigo-700 border border-indigo-200">
                                <span id="lineCount">3</span> Baris Terhitung
                            </span>
                        </div>
                    </div>

                    <textarea name="naskah_iklan_baris" id="naskah_iklan_baris" rows="5" oninput="hitungNaskahBaris()"
                              placeholder="DIJUAL TANAH SHM LT 350m2 Strategis dkt RSUD Tasikmalaya. Hub: 081223456789..."
                              class="w-full p-3.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-mono text-slate-900 leading-relaxed focus:bg-white focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">{{ old('naskah_iklan_baris') }}</textarea>
                    
                    <div class="flex items-center justify-between mt-1.5 text-[11px] text-slate-400">
                        <span>Ketentuan: 1 baris cetak rata-rata 32 karakter. Minimal muat adalah 3 baris.</span>
                        <span class="text-amber-600 font-medium">Pracetak dapat langsung copy naskah ini tanpa ketik ulang</span>
                    </div>

                    <input type="hidden" name="jumlah_baris" id="jumlah_baris_input" value="3">
                </div>

                <!-- Simulasi Tampilan Kolom Baris Koran -->
                <div class="p-3.5 bg-amber-50/50 border border-amber-200 rounded-lg">
                    <span class="text-[10px] uppercase font-bold text-amber-800 block mb-1.5">Pratinjau Kolom Cetak Koran:</span>
                    <div id="barisLivePreview" class="p-3 bg-white border border-amber-300 rounded font-mono text-xs text-slate-900 whitespace-pre-wrap leading-tight font-medium">
                        (Pratinjau naskah iklan baris akan tampil di sini saat diketik)
                    </div>
                </div>
            </div>

            <!-- TAB KONTEN 2: FORMULIR IKLAN KOLOM, DISPLAY & ADVERTORIAL -->
            <div id="sectionFormKolom" class="hidden bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-5">
                <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                            Spesifikasi Iklan Kolom / Display / Advertorial
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Kalkulator otomatis Milimeter Kolom (mmk) dan pilihan warna</p>
                    </div>
                </div>

                <!-- Pilihan Tipe Iklan Kolom -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kategori Iklan Koran <span class="text-red-500">*</span>
                        </label>
                        <select name="id_iklankoran" id="id_iklankoran" required onchange="handleJenisIklanChange()"
                                class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            @foreach($iklankoran as $ik)
                                <option value="{{ $ik->id }}" {{ old('id_iklankoran') == $ik->id ? 'selected' : '' }}>
                                    {{ $ik->jenis_iklankoran }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Pilihan Warna <span class="text-red-500">*</span>
                        </label>
                        <select name="warna_iklan" id="warna_iklan" required onchange="hitungKalkulatorMmk()"
                                class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <option value="Hitam Putih (BW)" {{ old('warna_iklan') == 'Hitam Putih (BW)' ? 'selected' : '' }}>
                                Hitam Putih (BW) — Rp {{ number_format($configs['tarif_mmk_bw'], 0, ',', '.') }}/mmk
                            </option>
                            <option value="Full Color (FC)" {{ old('warna_iklan') == 'Full Color (FC)' ? 'selected' : '' }}>
                                Full Color (FC) — Rp {{ number_format($configs['tarif_mmk_fc'], 0, ',', '.') }}/mmk
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Halaman Penempatan <span class="text-red-500">*</span>
                        </label>
                        <select name="halaman_iklan" required
                                class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <option value="Halaman Dalam">Halaman Dalam</option>
                            <option value="Halaman 1 (Cover Depan)">Halaman 1 (Cover Depan)</option>
                            <option value="Halaman Belakang">Halaman Belakang (Back Cover)</option>
                        </select>
                    </div>
                </div>

                <!-- KALKULATOR MMK (Kolom x Tinggi mm) -->
                <div class="p-4 bg-slate-50 border border-slate-300 rounded-xl space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-900 uppercase">Kalkulator Milimeter Kolom (mmk)</span>
                        <span class="text-[11px] text-slate-500">Standar cetak HU Kabar Priangan: 1 halaman = 9 Kolom × 540 mm</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Lebar (Jumlah Kolom)</label>
                            <input type="number" min="1" max="9" name="lebar_kolom" id="lebar_kolom" value="{{ old('lebar_kolom', 2) }}" oninput="hitungKalkulatorMmk()"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-900 text-center">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tinggi (Milimeter / mm)</label>
                            <input type="number" min="10" max="540" name="tinggi_mm" id="tinggi_mm" value="{{ old('tinggi_mm', 100) }}" oninput="hitungKalkulatorMmk()"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-900 text-center">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Total mmk</label>
                            <input type="text" id="total_mmk_display" readonly value="200 mmk"
                                   class="w-full h-10 px-3 bg-slate-200 border border-slate-300 rounded-lg text-xs font-bold text-kp-blue-700 text-center cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tarif Satuan / mmk</label>
                            <input type="text" id="tarif_mmk_display" readonly value="Rp 25.000"
                                   class="w-full h-10 px-3 bg-slate-200 border border-slate-300 rounded-lg text-xs font-bold text-slate-700 text-center cursor-not-allowed">
                        </div>
                    </div>

                    <input type="hidden" name="ukuran_iklan" id="ukuran_iklan_input" value="2 Kolom x 100 mm (200 mmk)">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Status Materi Iklan
                        </label>
                        <select name="status_materi" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <option value="Siap_Cetak">Materi Siap Cetak (File PDF/JPG dari Pemasang)</option>
                            <option value="Butuh_Setting">Butuh Disetting / Didesain oleh Tim Pracetak KP</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Instruksi / Catatan Materi Desain
                        </label>
                        <input type="text" name="keterangan_materi" placeholder="Contoh: Logo BJB di kanan atas, foto bupati jangan terpotong"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>
                </div>
            </div>

            <!-- BAGIAN 3: JADWAL TANGGAL PEMUATAN & FREKUENSI (MULTI-DATE) -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-4">
                <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                            Jadwal Tanggal Pemuatan & Edisi Terbit
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Mendukung multi-tanggal seperti centang angka 1–31 pada form fisik asli</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Jumlah Edisi Pemuatan (Qty Kali Terbit) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" min="1" id="total_muatkoran" name="total_muatkoran" value="{{ old('total_muatkoran', 1) }}" required oninput="hitungSemua()"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Rincian Tanggal Pemuatan (Pisahkan Koma)
                        </label>
                        <input type="text" name="tanggal_muat_list" id="tanggal_muat_list" 
                               value="{{ old('tanggal_muat_list', $isPastCutoff ? date('Y-m-d', strtotime('+2 days')) : date('Y-m-d', strtotime('+1 day'))) }}"
                               placeholder="Contoh: 2026-10-15, 2026-10-16, 2026-10-20"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <p class="text-[11px] text-slate-400 mt-1">Jika terbit lebih dari 1 kali, cantumkan tanggal-tanggal muat yang disepakati.</p>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 4: SYARAT PEMBAYARAN & SOP CASH BEFORE PRINT (CBP GATE) -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-4">
                <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                            Syarat Pembayaran & Verifikasi CBP Gate
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">SOP Industri Pers: Walk-in wajib Cash Before Print (CBP), Kredit khusus Pemda & Agensi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Jenis Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_pembayaran" id="jenis_pembayaran" required onchange="handleJenisBayarChange()"
                                class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <option value="Tunai" {{ old('jenis_pembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai / Lunas Kasir (CBP)</option>
                            <option value="Kredit" {{ old('jenis_pembayaran') == 'Kredit' ? 'selected' : '' }}>Kredit (Termin / APBD / Agensi)</option>
                        </select>
                    </div>

                    <div id="colJatuhTempo" class="hidden">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tanggal Jatuh Tempo Pembayaran
                        </label>
                        <input type="date" name="jatuh_tempo_pembayaran" value="{{ old('jatuh_tempo_pembayaran', date('Y-m-d', strtotime('+30 days'))) }}"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div id="colBypassKredit" class="hidden">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Persetujuan Kredit (Manajer)
                        </label>
                        <div class="flex items-center h-10">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="disetujui_kredit" value="1" class="rounded border-slate-300 text-kp-blue-600 focus:ring-kp-blue-500">
                                <span class="text-xs font-semibold text-slate-700">Disetujui bypass CBP Gate</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Peringatan CBP Gate -->
                <div id="cbpAlertBox" class="p-3 bg-rose-50 border border-rose-200 rounded-lg flex items-start gap-2.5 text-xs text-rose-800">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <strong class="font-bold">SOP Cash Before Print (CBP) Aktif:</strong>
                        <span> Untuk transaksi Tunai, iklan akan terkunci di status <strong>Menunggu Pembayaran</strong> sampai kasir mengonfirmasi pelunasan sebelum dapat masuk ke antrean layout cetak.</span>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 5: KALKULASI FINANSIAL, DISKON, PPN 12% & TOTAL -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-5">
                <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                            Rincian Tarif, Diskon & Kalkulasi Pajak
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Rumus DPP & PPN {{ $configs['ppn_persen'] }}% sesuai regulasi perpajakan DJP terbaru</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tarif Satuan (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="harga_transaksikoran" name="harga_transaksikoran" value="{{ number_format($configs['tarif_per_baris'] * 3, 0, ',', '.') }}" required onkeyup="formatRupiahInput(this); hitungSemua();"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Diskon Agensi (Rp)
                        </label>
                        <input type="text" id="diskon_agensi" name="diskon_agensi" value="0" onkeyup="formatRupiahInput(this); hitungSemua();"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <span id="labelDiskonAgensi" class="text-[10px] text-slate-400 mt-0.5 block">Khusus Biro Iklan</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Diskon Umum / Kuantitas (Rp)
                        </label>
                        <input type="text" id="diskon_umum" name="diskon_umum" value="0" onkeyup="formatRupiahInput(this); hitungSemua();"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Diskon Khusus Manajer (Rp)
                        </label>
                        <input type="text" id="diskon_khusus" name="diskon_khusus" value="0" onkeyup="formatRupiahInput(this); hitungSemua();"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            DPP (Dasar Pengenaan Pajak)
                        </label>
                        <input type="text" id="dpp_display" readonly value="0"
                               class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-xs font-mono text-slate-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            PPN ({{ $configs['ppn_persen'] }}% Otomatis)
                        </label>
                        <input type="text" id="ppn_display" readonly value="0"
                               class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-700 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Bea Meterai (Rp)
                        </label>
                        <input type="text" id="materai" name="materai" readonly value="0"
                               class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-700 cursor-not-allowed">
                        <span id="meteraiNotice" class="text-[10px] text-slate-400 mt-0.5 block">Otomatis Rp 10.000 jika total ≥ 5 Juta</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-kp-blue-700 uppercase tracking-wider mb-1.5">
                            TOTAL TAGIHAN (BRUTO + PPN)
                        </label>
                        <input type="text" id="totaltagihan_display" readonly value="0"
                               class="w-full h-10 px-3.5 bg-kp-blue-50 border border-kp-blue-300 rounded-lg text-sm font-extrabold text-kp-blue-700 font-tabular cursor-not-allowed">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-200">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Jumlah Dibayar Sekarang / DP (Rp) <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" id="jumlahbayar_transaksikoran" name="jumlahbayar_transaksikoran" value="0" required onkeyup="formatRupiahInput(this); hitungSemua();"
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <button type="button" onclick="setBayarLunas()" class="px-3 h-10 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg shrink-0 transition">
                                Lunas (100%)
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-rose-700 uppercase tracking-wider mb-1.5">
                            SISA PIUTANG (Rp)
                        </label>
                        <input type="text" id="piutang_display" readonly value="0"
                               class="w-full h-10 px-3.5 bg-rose-50 border border-rose-300 rounded-lg text-sm font-bold text-rose-700 font-tabular cursor-not-allowed">
                    </div>
                </div>

                <!-- Footer Tombol Aksi -->
                <div class="flex items-center justify-between pt-5 border-t border-slate-200">
                    <div class="text-xs text-slate-500">
                        <span>Setelah disimpan, sistem akan otomatis menerbitkan dokumen <strong>SPPI (Insertion Order)</strong> resmi.</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('transaksikoran.index') }}" 
                           class="px-5 py-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs font-bold rounded-lg shadow-xs transition cursor-pointer flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Simpan Transaksi & Terbitkan SPPI</span>
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>

    @push('scripts')
    <script>
        const CONFIG = {
            ppnPersen: {{ $configs['ppn_persen'] }},
            tarifBaris: {{ $configs['tarif_per_baris'] }},
            tarifMmkBw: {{ $configs['tarif_mmk_bw'] }},
            tarifMmkFc: {{ $configs['tarif_mmk_fc'] }},
            minBaris: {{ $configs['minimal_baris'] }},
            maksKarakter: {{ $configs['maks_karakter'] }},
            diskonAgensiDefault: {{ $configs['diskon_agensi'] }},
            materaiNominal: {{ $configs['materai_nominal'] }},
            materaiThreshold: {{ $configs['materai_threshold'] }},
        };

        let currentTab = 'BARIS';

        function switchFormTab(tab) {
            currentTab = tab;
            document.getElementById('kategori_form_input').value = tab;

            const btnBaris = document.getElementById('tabBtnBaris');
            const btnKolom = document.getElementById('tabBtnKolom');
            const secBaris = document.getElementById('sectionFormBaris');
            const secKolom = document.getElementById('sectionFormKolom');

            if (tab === 'BARIS') {
                btnBaris.className = 'py-3 px-4 rounded-lg font-bold text-xs flex items-center justify-center gap-2.5 transition cursor-pointer bg-kp-blue-600 text-white shadow-xs';
                btnKolom.className = 'py-3 px-4 rounded-lg font-bold text-xs flex items-center justify-center gap-2.5 transition cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200';
                secBaris.classList.remove('hidden');
                secKolom.classList.add('hidden');
                hitungNaskahBaris();
            } else {
                btnKolom.className = 'py-3 px-4 rounded-lg font-bold text-xs flex items-center justify-center gap-2.5 transition cursor-pointer bg-kp-blue-600 text-white shadow-xs';
                btnBaris.className = 'py-3 px-4 rounded-lg font-bold text-xs flex items-center justify-center gap-2.5 transition cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200';
                secKolom.classList.remove('hidden');
                secBaris.classList.add('hidden');
                hitungKalkulatorMmk();
            }
        }

        function hitungNaskahBaris() {
            if (currentTab !== 'BARIS') return;
            const text = document.getElementById('naskah_iklan_baris').value || '';
            const charCount = text.length;
            document.getElementById('charCount').innerText = charCount;

            // Hitung baris: pecah berdasarkan enter dan panjang per 32 karakter
            const rawLines = text.split('\n');
            let totalLines = 0;
            rawLines.forEach(line => {
                const len = line.length;
                totalLines += Math.max(1, Math.ceil(len / CONFIG.maksKarakter));
            });

            // Minimal baris
            const finalLines = Math.max(CONFIG.minBaris, totalLines);
            document.getElementById('lineCount').innerText = finalLines;
            document.getElementById('jumlah_baris_input').value = finalLines;

            // Update live preview
            const previewEl = document.getElementById('barisLivePreview');
            previewEl.innerText = text.trim() ? text : '(Pratinjau naskah iklan baris akan tampil di sini saat diketik)';

            // Set harga otomatis = finalLines * tarifBaris
            const hargaBaris = finalLines * CONFIG.tarifBaris;
            document.getElementById('harga_transaksikoran').value = formatRupiah(hargaBaris);

            hitungSemua();
        }

        function hitungKalkulatorMmk() {
            if (currentTab !== 'KOLOM') return;
            const kolom = parseInt(document.getElementById('lebar_kolom').value) || 1;
            const mm = parseInt(document.getElementById('tinggi_mm').value) || 10;
            const warna = document.getElementById('warna_iklan').value;

            const totalMmk = kolom * mm;
            const tarifSatuan = (warna.includes('Full Color')) ? CONFIG.tarifMmkFc : CONFIG.tarifMmkBw;

            document.getElementById('total_mmk_display').value = totalMmk + ' mmk';
            document.getElementById('tarif_mmk_display').value = 'Rp ' + formatRupiah(tarifSatuan);
            document.getElementById('ukuran_iklan_input').value = kolom + ' Kolom x ' + mm + ' mm (' + totalMmk + ' mmk)';

            const hargaKolom = totalMmk * tarifSatuan;
            document.getElementById('harga_transaksikoran').value = formatRupiah(hargaKolom);

            hitungSemua();
        }

        function handleKanalChange() {
            const kanal = document.getElementById('kanal_order').value;
            const diskonAgensiInput = document.getElementById('diskon_agensi');
            const labelDiskonAgensi = document.getElementById('labelDiskonAgensi');

            if (kanal === 'BIRO_IKLAN') {
                // Beri default diskon agensi
                labelDiskonAgensi.innerText = `Biro Iklan (Komisi Baku ${CONFIG.diskonAgensiDefault}%)`;
                const harga = cleanNumber(document.getElementById('harga_transaksikoran').value);
                const qty = parseInt(document.getElementById('total_muatkoran').value) || 1;
                const totalOmset = harga * qty;
                const diskonAgensiNilai = Math.round(totalOmset * (CONFIG.diskonAgensiDefault / 100));
                diskonAgensiInput.value = formatRupiah(diskonAgensiNilai);
            } else {
                labelDiskonAgensi.innerText = 'Khusus Biro Iklan';
                diskonAgensiInput.value = '0';
            }
            hitungSemua();
        }

        function pilihPelanggan(select) {
            const opt = select.options[select.selectedIndex];
            if (!opt || !opt.value) {
                document.getElementById('pelangganInfoCard').classList.add('hidden');
                return;
            }

            document.getElementById('nama_pemasangkoran').value = opt.getAttribute('data-nama') || '';
            document.getElementById('alamat_pemasangkoran').value = opt.getAttribute('data-alamat') || '';
            document.getElementById('no_telp').value = opt.getAttribute('data-telp') || '';

            const jenis = opt.getAttribute('data-jenis');
            const credit = parseInt(opt.getAttribute('data-credit')) || 0;
            const piutang = parseInt(opt.getAttribute('data-piutang')) || 0;

            document.getElementById('infoJenisKlien').innerText = jenis;
            document.getElementById('infoCreditLimit').innerText = credit > 0 ? 'Rp ' + formatRupiah(credit) : 'CBP (Wajib Lunas)';
            document.getElementById('infoPiutangBerjalan').innerText = 'Rp ' + formatRupiah(piutang);
            document.getElementById('infoSopBayar').innerText = credit > 0 ? 'Boleh Kredit (Termin)' : 'Wajib Tunai Kasir';

            document.getElementById('pelangganInfoCard').classList.remove('hidden');

            if (jenis === 'Agensi') {
                document.getElementById('kanal_order').value = 'BIRO_IKLAN';
                handleKanalChange();
            }
        }

        function handleJenisBayarChange() {
            const jenis = document.getElementById('jenis_pembayaran').value;
            const colJatuhTempo = document.getElementById('colJatuhTempo');
            const colBypass = document.getElementById('colBypassKredit');
            const alertCbp = document.getElementById('cbpAlertBox');

            if (jenis === 'Kredit') {
                colJatuhTempo.classList.remove('hidden');
                colBypass.classList.remove('hidden');
                alertCbp.classList.add('hidden');
            } else {
                colJatuhTempo.classList.add('hidden');
                colBypass.classList.add('hidden');
                alertCbp.classList.remove('hidden');
            }
        }

        function hitungSemua() {
            const harga = cleanNumber(document.getElementById('harga_transaksikoran').value);
            const qty = parseInt(document.getElementById('total_muatkoran').value) || 1;
            const diskonAgensi = cleanNumber(document.getElementById('diskon_agensi').value);
            const diskonUmum = cleanNumber(document.getElementById('diskon_umum').value);
            const diskonKhusus = cleanNumber(document.getElementById('diskon_khusus').value);
            const diskonTotal = diskonAgensi + diskonUmum + diskonKhusus;

            const omset = harga * qty;
            const setelahDiskon = Math.max(0, omset - diskonTotal);

            // Auto Meterai (Rp 10.000 jika >= Rp 5.000.000)
            let meterai = 0;
            if (setelahDiskon >= CONFIG.materaiThreshold) {
                meterai = CONFIG.materaiNominal;
                document.getElementById('meteraiNotice').innerText = '✅ Dikenakan Bea Meterai Rp 10.000 (UU No. 10/2020)';
            } else {
                document.getElementById('meteraiNotice').innerText = 'Otomatis Rp 10.000 jika total ≥ 5 Juta';
            }
            document.getElementById('materai').value = formatRupiah(meterai);

            // Rumus Perpajakan DJP Resmi
            const ppnPersen = CONFIG.ppnPersen;
            const dpp = Math.round(setelahDiskon / (1 + (ppnPersen / 100)));
            const ppn = setelahDiskon - dpp;
            const totalTagihan = setelahDiskon + meterai;

            document.getElementById('dpp_display').value = 'Rp ' + formatRupiah(dpp);
            document.getElementById('ppn_display').value = 'Rp ' + formatRupiah(ppn);
            document.getElementById('totaltagihan_display').value = 'Rp ' + formatRupiah(totalTagihan);

            // Piutang
            const bayar = cleanNumber(document.getElementById('jumlahbayar_transaksikoran').value);
            const piutang = Math.max(0, totalTagihan - bayar);
            document.getElementById('piutang_display').value = 'Rp ' + formatRupiah(piutang);
        }

        function setBayarLunas() {
            const totalText = document.getElementById('totaltagihan_display').value;
            const total = cleanNumber(totalText);
            document.getElementById('jumlahbayar_transaksikoran').value = formatRupiah(total);
            hitungSemua();
        }

        function formatRupiah(angka) {
            if (!angka) return '0';
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        function cleanNumber(rupiah) {
            if (!rupiah) return 0;
            return parseFloat(rupiah.toString().replace(/[^0-9]/g, '')) || 0;
        }

        function formatRupiahInput(input) {
            let val = cleanNumber(input.value);
            input.value = formatRupiah(val);
        }

        // Init on page load
        document.addEventListener('DOMContentLoaded', () => {
            hitungNaskahBaris();
            handleJenisBayarChange();
        });
    </script>
    @endpush
</x-app-layout>
