<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">

        <!-- Form Pengaturan Terpusat -->
        <form method="POST" action="{{ route('admin.pengaturan.update') }}" id="formPengaturanAdmin" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Header Panel Pengaturan Terpusat (Standar Iklan 60-30-10) -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-10 w-auto object-contain shrink-0" onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                                Pengaturan Sistem &amp; Profil Perusahaan
                            </h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">
                                Konfigurasi Global
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 truncate">
                            Parameter legalitas media pers, rekening bank operasional, kebijakan perpajakan, dan kontrol modul.
                        </p>
                    </div>
                </div>

                <div class="flex items-center flex-wrap gap-2.5 shrink-0">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="inline-flex items-center gap-2 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Konfigurasi</span>
                    </button>
                </div>
            </div>

            <!-- Notifikasi Sukses -->
            @if(session('success'))
                <div class="p-4 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-900 rounded-xl text-xs sm:text-sm font-medium flex items-center gap-2.5 shadow-2xs">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Notifikasi Kesalahan -->
            @if (isset($errors) && $errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs sm:text-sm font-medium shadow-2xs space-y-1">
                    <div class="font-bold flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Terdapat kesalahan pengisian formulir:</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-0.5 text-xs text-rose-700">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 1. IDENTITAS & LEGALITAS PERUSAHAAN PERS -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                <div class="px-5 py-4 bg-slate-50/70 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 flex items-center justify-center font-bold text-xs shrink-0 font-mono">1</span>
                        <div>
                            <h2 class="text-sm sm:text-base font-bold text-slate-900">Identitas &amp; Legalitas Perusahaan</h2>
                            <p class="text-xs text-slate-500 font-normal mt-0.5">Identitas resmi penerbitan pers untuk faktur, kwitansi, dan kop surat</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-slate-700 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-md">Dokumen Resmi</span>
                </div>

                <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Badan Usaha / PT <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $configs['nama_perusahaan'] ?? '') }}" required
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Brand Penerbitan / Media <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_media" value="{{ old('nama_media', $configs['nama_media'] ?? '') }}" required
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Slogan / Tagline Media
                        </label>
                        <input type="text" name="slogan_media" value="{{ old('slogan_media', $configs['slogan_media'] ?? '') }}"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Alamat Kantor Redaksi &amp; Percetakan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="alamat_perusahaan" rows="2" required
                                  class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">{{ old('alamat_perusahaan', $configs['alamat_perusahaan'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kota Penerbitan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="kota_terbit" value="{{ old('kota_terbit', $configs['kota_terbit'] ?? 'Tasikmalaya') }}" required
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            NPWP Perusahaan Resmi
                        </label>
                        <input type="text" name="npwp_perusahaan" value="{{ old('npwp_perusahaan', $configs['npwp_perusahaan'] ?? '') }}" placeholder="00.000.000.0-000.000"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            No. Telepon Kantor
                        </label>
                        <input type="text" name="telepon_perusahaan" value="{{ old('telepon_perusahaan', $configs['telepon_perusahaan'] ?? '') }}"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Email Resmi Redaksi / Iklan
                        </label>
                        <input type="email" name="email_perusahaan" value="{{ old('email_perusahaan', $configs['email_perusahaan'] ?? '') }}"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>
                </div>
            </div>

            <!-- 2. REKENING BANK RESMI PERUSAHAAN (FAKTUR & KWITANSI) -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                <div class="px-5 py-4 bg-slate-50/70 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 flex items-center justify-center font-bold text-xs shrink-0 font-mono">2</span>
                        <div>
                            <h2 class="text-sm sm:text-base font-bold text-slate-900">Rekening Bank Resmi</h2>
                            <p class="text-xs text-slate-500 font-normal mt-0.5">Tujuan transfer pembayaran yang tercetak pada invoice &amp; kuitansi</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-slate-700 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-md">Rekening Utama</span>
                </div>

                <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Bank Utama <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="bank_nama" value="{{ old('bank_nama', $configs['bank_nama'] ?? 'Bank BJB') }}" required
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kantor Cabang
                        </label>
                        <input type="text" name="bank_cabang" value="{{ old('bank_cabang', $configs['bank_cabang'] ?? 'Cabang Tasikmalaya') }}"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nomor Rekening Resmi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="bank_rekening" value="{{ old('bank_rekening', $configs['bank_rekening'] ?? '0010010203040') }}" required
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Atas Nama Rekening <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="bank_atas_nama" value="{{ old('bank_atas_nama', $configs['bank_atas_nama'] ?? 'PT BERKAH PIKIRAN RAKYAT') }}" required
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>

                    <!-- Rekening Alternatif / Sekunder -->
                    <div class="col-span-1 md:col-span-2 lg:col-span-4 pt-3 border-t border-slate-100">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Rekening Alternatif (Opsional)</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Bank Sekunder
                        </label>
                        <input type="text" name="bank_secondary_nama" value="{{ old('bank_secondary_nama', $configs['bank_secondary_nama'] ?? 'Bank Mandiri') }}"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nomor Rekening Sekunder
                        </label>
                        <input type="text" name="bank_secondary_rekening" value="{{ old('bank_secondary_rekening', $configs['bank_secondary_rekening'] ?? '') }}"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Atas Nama Rekening Sekunder
                        </label>
                        <input type="text" name="bank_secondary_atas_nama" value="{{ old('bank_secondary_atas_nama', $configs['bank_secondary_atas_nama'] ?? 'PT BERKAH PIKIRAN RAKYAT') }}"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>
                </div>
            </div>

            <!-- 3. KEBIJAKAN OPERASIONAL, PERPAJAKAN & KREDIT PELANGGAN -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                <div class="px-5 py-4 bg-slate-50/70 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 flex items-center justify-center font-bold text-xs shrink-0 font-mono">3</span>
                        <div>
                            <h2 class="text-sm sm:text-base font-bold text-slate-900">Kebijakan Operasional &amp; Perpajakan</h2>
                            <p class="text-xs text-slate-500 font-normal mt-0.5">Parameter PPN, batas waktu cetak iklan, bea meterai, dan batasan kredit</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-slate-700 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-md">Kebijakan Bisnis</span>
                </div>

                <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Persentase PPN Resmi <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <input type="number" step="0.1" name="ppn_persen" value="{{ old('ppn_persen', $configs['ppn_persen'] ?? '12') }}" required
                                   class="w-full h-10 px-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                            <span class="absolute right-3.5 text-xs text-slate-400 font-semibold pointer-events-none">%</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5">Standar UU HPP nasional (11% s.d. 12%)</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Jam Batas Order Iklan Koran (WIB) <span class="text-rose-500">*</span>
                        </label>
                        <input type="time" name="jam_cutoff_iklan" value="{{ old('jam_cutoff_iklan', $configs['jam_cutoff_iklan'] ?? '14:00') }}" required
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                        <p class="text-xs text-slate-500 mt-1.5">Batas order masuk untuk cetak esok hari</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Limit Kredit Default Pelanggan Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex rounded-xl shadow-2xs overflow-hidden">
                            <span class="inline-flex items-center px-3.5 border border-r-0 border-slate-300 bg-slate-50 text-slate-600 text-xs font-bold font-mono">Rp</span>
                            <input type="text" name="credit_limit_default" 
                                   value="{{ number_format((float) ($configs['credit_limit_default'] ?? 25000000), 0, ',', '.') }}" 
                                   onkeyup="formatRupiahInput(this)" required
                                   class="flex-1 h-10 px-3.5 bg-white border border-slate-300 rounded-r-xl text-sm font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition">
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5">Batas piutang awal saat input pemasang iklan baru</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Ambang Batas Bea Meterai <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex rounded-xl shadow-2xs overflow-hidden">
                            <span class="inline-flex items-center px-3.5 border border-r-0 border-slate-300 bg-slate-50 text-slate-600 text-xs font-bold font-mono">Rp</span>
                            <input type="text" name="materai_threshold" 
                                   value="{{ number_format((float) ($configs['materai_threshold'] ?? 5000000), 0, ',', '.') }}" 
                                   onkeyup="formatRupiahInput(this)" required
                                   class="flex-1 h-10 px-3.5 bg-white border border-slate-300 rounded-r-xl text-sm font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nominal Bea Meterai <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex rounded-xl shadow-2xs overflow-hidden">
                            <span class="inline-flex items-center px-3.5 border border-r-0 border-slate-300 bg-slate-50 text-slate-600 text-xs font-bold font-mono">Rp</span>
                            <input type="text" name="materai_nominal" 
                                   value="{{ number_format((float) ($configs['materai_nominal'] ?? 10000), 0, ',', '.') }}" 
                                   onkeyup="formatRupiahInput(this)" required
                                   class="flex-1 h-10 px-3.5 bg-white border border-slate-300 rounded-r-xl text-sm font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Diskon Default Biro / Agensi
                        </label>
                        <div class="relative flex items-center">
                            <input type="number" step="0.5" min="0" max="100" name="diskon_agensi_default" value="{{ old('diskon_agensi_default', $configs['diskon_agensi_default'] ?? '15') }}"
                                   class="w-full h-10 px-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                            <span class="absolute right-3.5 text-xs text-slate-400 font-semibold pointer-events-none">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. PREFIX PENOMORAN DOKUMEN & KONTROL MODUL -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- 4A. Penomoran Dokumen Otomatis -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="px-5 py-4 bg-slate-50/70 border-b border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 flex items-center justify-center font-bold text-xs shrink-0 font-mono">4</span>
                                <div>
                                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Penomoran Dokumen Otomatis</h2>
                                    <p class="text-xs text-slate-500 font-normal mt-0.5">Kode awalan resmi untuk faktur, kuitansi, dan SPPI</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6 grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Prefix Faktur</label>
                                <input type="text" name="prefix_faktur" value="{{ old('prefix_faktur', $configs['prefix_faktur'] ?? 'FKT') }}" required
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 uppercase focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <p class="text-xs text-slate-500 mt-1.5">Contoh: FKT-001/2026</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Prefix Kwitansi</label>
                                <input type="text" name="prefix_kwitansi" value="{{ old('prefix_kwitansi', $configs['prefix_kwitansi'] ?? 'KWT') }}" required
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 uppercase focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <p class="text-xs text-slate-500 mt-1.5">Contoh: KWT-001/2026</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Prefix SPPI</label>
                                <input type="text" name="prefix_sppi" value="{{ old('prefix_sppi', $configs['prefix_sppi'] ?? 'SPPI') }}" required
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 uppercase focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <p class="text-xs text-slate-500 mt-1.5">Contoh: SPPI-001/2026</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-100 text-xs text-slate-500">
                        <span class="font-bold text-slate-700">Info Reset Tahunan:</span> Penomoran otomatis merujuk pada tahun transaksi berjalan dan di-reset setiap tanggal 1 Januari secara otomatis oleh sistem.
                    </div>
                </div>

                <!-- 4B. Feature Toggles Modul Operasional -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="px-5 py-4 bg-slate-50/70 border-b border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 flex items-center justify-center font-bold text-xs shrink-0 font-mono">5</span>
                                <div>
                                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Kendali Modul Operasional</h2>
                                    <p class="text-xs text-slate-500 font-normal mt-0.5">Aktifkan atau nonaktifkan modul bisnis perusahaan sesuai kebutuhan</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6 space-y-3.5">
                            <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50/70 transition cursor-pointer">
                                <div>
                                    <div class="text-sm font-bold text-slate-900">Modul Iklan Koran Cetak</div>
                                    <div class="text-xs text-slate-500 mt-0.5">Order, tarif mmk/baris, SPPI, dan lembar dummy pracetak</div>
                                </div>
                                <input type="checkbox" name="modul_iklan_koran" value="1" {{ ($configs['modul_iklan_koran'] ?? '1') == '1' ? 'checked' : '' }}
                                       class="w-5 h-5 text-kp-blue-600 rounded-md border-slate-300 focus:ring-kp-blue-500">
                            </label>

                            <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50/70 transition cursor-pointer">
                                <div>
                                    <div class="text-sm font-bold text-slate-900">Modul Iklan Media Online</div>
                                    <div class="text-xs text-slate-500 mt-0.5">kabarpriangan.pikiran-rakyat.com advertorial &amp; banner</div>
                                </div>
                                <input type="checkbox" name="modul_iklan_online" value="1" {{ ($configs['modul_iklan_online'] ?? '1') == '1' ? 'checked' : '' }}
                                       class="w-5 h-5 text-kp-blue-600 rounded-md border-slate-300 focus:ring-kp-blue-500">
                            </label>

                            <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50/70 transition cursor-pointer">
                                <div>
                                    <div class="text-sm font-bold text-slate-900">Modul Priangan TV (Streaming &amp; TV)</div>
                                    <div class="text-xs text-slate-500 mt-0.5">Spot iklan siaran televisi lokal dan live streaming</div>
                                </div>
                                <input type="checkbox" name="modul_iklan_tv" value="1" {{ ($configs['modul_iklan_tv'] ?? '1') == '1' ? 'checked' : '' }}
                                       class="w-5 h-5 text-kp-blue-600 rounded-md border-slate-300 focus:ring-kp-blue-500">
                            </label>

                            <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50/70 transition cursor-pointer">
                                <div>
                                    <div class="text-sm font-bold text-slate-900">Modul Sirkulasi &amp; Distribusi Agen</div>
                                    <div class="text-xs text-slate-500 mt-0.5">Jatah oplah harian, jatah H+1, dan ekspedisi koran</div>
                                </div>
                                <input type="checkbox" name="modul_sirkulasi" value="1" {{ ($configs['modul_sirkulasi'] ?? '1') == '1' ? 'checked' : '' }}
                                       class="w-5 h-5 text-kp-blue-600 rounded-md border-slate-300 focus:ring-kp-blue-500">
                            </label>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-100 text-xs text-slate-500">
                        <span class="font-bold text-slate-700">Perhatian:</span> Menonaktifkan modul akan menyembunyikan menu akses cepat dari pengguna non-administrator.
                    </div>
                </div>

            </div>

            <!-- Tombol Aksi Simpan Sticky Bawah -->
            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs flex items-center justify-between gap-4 sticky bottom-4 z-20">
                <div class="text-xs sm:text-sm text-slate-500 hidden sm:block">
                    Pastikan seluruh data legalitas dan rekening bank telah sesuai dengan dokumen akta perusahaan.
                </div>
                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-bold rounded-xl transition flex items-center justify-center">
                        Batal
                    </a>
                    <button type="submit" 
                            class="h-10 px-6 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Seluruh Konfigurasi</span>
                    </button>
                </div>
            </div>

        </form>

    </div>

    <script>
        function formatRupiahInput(input) {
            let val = input.value.replace(/[^0-9]/g, '');
            if (val === '') {
                input.value = '';
                return;
            }
            input.value = new Intl.NumberFormat('id-ID').format(val);
        }
    </script>
</x-app-layout>
