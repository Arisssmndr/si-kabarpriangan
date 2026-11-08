<x-app-layout>
    <div class="space-y-6">

        <!-- Header Panel Pengaturan Terpusat -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Pengaturan Sistem
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Atur data perusahaan, rekening resmi, pajak, dan informasi operasional.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition">
                    &larr; <span>Dashboard Admin</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs shadow-2xs">
                <div class="font-bold flex items-center gap-2 mb-1.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Terdapat kesalahan pengisian formulir:</span>
                </div>
                <ul class="list-disc pl-5 space-y-0.5 text-[11px]">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- 1. IDENTITAS & LEGALITAS PERUSAHAAN PERS -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-lg bg-sky-100 text-kp-blue-700 flex items-center justify-center font-bold text-xs">1</span>
                        <h2 class="text-xs font-bold text-slate-800">Identitas & Legalitas Perusahaan</h2>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">Dokumen Resmi</span>
                </div>

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama Badan Usaha / PT <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $configs['nama_perusahaan'] ?? '') }}" required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama Brand Penerbitan / Media <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_media" value="{{ old('nama_media', $configs['nama_media'] ?? '') }}" required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Slogan / Tagline Media
                        </label>
                        <input type="text" name="slogan_media" value="{{ old('slogan_media', $configs['slogan_media'] ?? '') }}"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Alamat Kantor Redaksi &amp; Percetakan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="alamat_perusahaan" rows="2" required
                                  class="w-full p-2.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">{{ old('alamat_perusahaan', $configs['alamat_perusahaan'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Kota Penerbitan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="kota_terbit" value="{{ old('kota_terbit', $configs['kota_terbit'] ?? 'Tasikmalaya') }}" required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            NPWP Perusahaan Resmi
                        </label>
                        <input type="text" name="npwp_perusahaan" value="{{ old('npwp_perusahaan', $configs['npwp_perusahaan'] ?? '') }}" placeholder="00.000.000.0-000.000"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            No. Telepon Kantor
                        </label>
                        <input type="text" name="telepon_perusahaan" value="{{ old('telepon_perusahaan', $configs['telepon_perusahaan'] ?? '') }}"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Email Resmi Redaksi / Iklan
                        </label>
                        <input type="email" name="email_perusahaan" value="{{ old('email_perusahaan', $configs['email_perusahaan'] ?? '') }}"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>
                </div>
            </div>

            <!-- 2. REKENING BANK RESMI PERUSAHAAN (FAKTUR & KWITANSI) -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">2</span>
                        <h2 class="text-xs font-bold text-slate-800">Rekening Bank Resmi</h2>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">Rekening Utama</span>
                </div>

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama Bank Utama <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="bank_nama" value="{{ old('bank_nama', $configs['bank_nama'] ?? 'Bank BJB') }}" required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Kantor Cabang
                        </label>
                        <input type="text" name="bank_cabang" value="{{ old('bank_cabang', $configs['bank_cabang'] ?? 'Cabang Tasikmalaya') }}"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Nomor Rekening Resmi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="bank_rekening" value="{{ old('bank_rekening', $configs['bank_rekening'] ?? '0010010203040') }}" required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Atas Nama Rekening <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="bank_atas_nama" value="{{ old('bank_atas_nama', $configs['bank_atas_nama'] ?? 'PT BERKAH PIKIRAN RAKYAT') }}" required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <!-- Rekening Alternatif / Sekunder -->
                    <div class="col-span-1 md:col-span-2 lg:col-span-4 pt-3 border-t border-slate-100">
                        <span class="text-[11px] font-semibold text-slate-500">Rekening Lain (Opsional)</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama Bank Sekunder
                        </label>
                        <input type="text" name="bank_secondary_nama" value="{{ old('bank_secondary_nama', $configs['bank_secondary_nama'] ?? 'Bank Mandiri') }}"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Nomor Rekening Sekunder
                        </label>
                        <input type="text" name="bank_secondary_rekening" value="{{ old('bank_secondary_rekening', $configs['bank_secondary_rekening'] ?? '') }}"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-mono font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Atas Nama Rekening Sekunder
                        </label>
                        <input type="text" name="bank_secondary_atas_nama" value="{{ old('bank_secondary_atas_nama', $configs['bank_secondary_atas_nama'] ?? 'PT BERKAH PIKIRAN RAKYAT') }}"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>
                </div>
            </div>

            <!-- 3. KEBIJAKAN OPERASIONAL, PERPAJAKAN & KREDIT PELANGGAN -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">3</span>
                        <h2 class="text-xs font-bold text-slate-800">Kebijakan Operasional & Perpajakan</h2>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">Kebijakan</span>
                </div>

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Persentase PPN Resmi <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" step="0.1" name="ppn_persen" value="{{ old('ppn_persen', $configs['ppn_persen'] ?? '12') }}" required
                                   class="w-full h-10 px-3 pr-8 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-400 pointer-events-none">%</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Standar UU HPP nasional (11% s.d. 12%)</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Jam Batas Order Iklan Koran (WIB) <span class="text-rose-500">*</span>
                        </label>
                        <input type="time" name="jam_cutoff_iklan" value="{{ old('jam_cutoff_iklan', $configs['jam_cutoff_iklan'] ?? '14:00') }}" required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <p class="text-[10px] text-slate-400 mt-1">Batas order masuk untuk cetak esok hari</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Limit Kredit Default Pelanggan Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex rounded-lg shadow-2xs">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-xs font-semibold">Rp</span>
                            <input type="text" name="credit_limit_default" 
                                   value="{{ number_format((float) ($configs['credit_limit_default'] ?? 25000000), 0, ',', '.') }}" 
                                   onkeyup="formatRupiahInput(this)" required
                                   class="flex-1 h-10 px-3 bg-white border border-slate-300 rounded-r-lg text-xs font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Batas piutang awal saat input pemasang iklan baru</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Ambang Batas Bea Meterai (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex rounded-lg shadow-2xs">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-xs font-semibold">Rp</span>
                            <input type="text" name="materai_threshold" 
                                   value="{{ number_format((float) ($configs['materai_threshold'] ?? 5000000), 0, ',', '.') }}" 
                                   onkeyup="formatRupiahInput(this)" required
                                   class="flex-1 h-10 px-3 bg-white border border-slate-300 rounded-r-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Nominal Bea Meterai (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex rounded-lg shadow-2xs">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-xs font-semibold">Rp</span>
                            <input type="text" name="materai_nominal" 
                                   value="{{ number_format((float) ($configs['materai_nominal'] ?? 10000), 0, ',', '.') }}" 
                                   onkeyup="formatRupiahInput(this)" required
                                   class="flex-1 h-10 px-3 bg-white border border-slate-300 rounded-r-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Diskon Default Biro / Agensi (%)
                        </label>
                        <div class="relative">
                            <input type="number" step="1" name="diskon_agensi_default" value="{{ old('diskon_agensi_default', $configs['diskon_agensi_default'] ?? '15') }}"
                                   class="w-full h-10 px-3 pr-8 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-400 pointer-events-none">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. PREFIX PENOMORAN DOKUMEN & KONTROL MODUL -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- 4A. Penomoran Dokumen Otomatis -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs">4</span>
                                <h2 class="text-xs font-bold text-slate-800">Penomoran Dokumen</h2>
                            </div>
                        </div>

                        <div class="p-5 grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Prefix Faktur</label>
                                <input type="text" name="prefix_faktur" value="{{ old('prefix_faktur', $configs['prefix_faktur'] ?? 'FKT') }}" required
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-900 uppercase">
                                <p class="text-[10px] text-slate-400 mt-1">Contoh: FKT-001/2026</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Prefix Kwitansi</label>
                                <input type="text" name="prefix_kwitansi" value="{{ old('prefix_kwitansi', $configs['prefix_kwitansi'] ?? 'KWT') }}" required
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-900 uppercase">
                                <p class="text-[10px] text-slate-400 mt-1">Contoh: KWT-001/2026</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Prefix SPPI</label>
                                <input type="text" name="prefix_sppi" value="{{ old('prefix_sppi', $configs['prefix_sppi'] ?? 'SPPI') }}" required
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-900 uppercase">
                                <p class="text-[10px] text-slate-400 mt-1">Contoh: SPPI-001/2026</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-100 text-[11px] text-slate-500">
                        <span class="font-semibold text-slate-700">Info Reset Tahunan:</span> Penomoran otomatis merujuk pada tahun transaksi berjalan dan di-reset setiap tanggal 1 Januari secara otomatis oleh sistem.
                    </div>
                </div>

                <!-- 4B. Feature Toggles Modul Operasional -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs">5</span>
                                <div>
                                    <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Kendali Modul Operasional (Feature Toggles)</h2>
                                    <p class="text-[11px] text-slate-500 font-normal">Aktifkan atau nonaktifkan modul bisnis perusahaan sesuai kebutuhan</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 space-y-3">
                            <label class="flex items-center justify-between p-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition cursor-pointer">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Modul Iklan Koran Cetak</div>
                                    <div class="text-[11px] text-slate-500">Order, tarif mmk/baris, SPPI, dan lembar dummy pracetak</div>
                                </div>
                                <input type="checkbox" name="modul_iklan_koran" value="1" {{ ($configs['modul_iklan_koran'] ?? '1') == '1' ? 'checked' : '' }}
                                       class="w-4 h-4 text-kp-blue-600 rounded border-slate-300 focus:ring-kp-blue-500">
                            </label>

                            <label class="flex items-center justify-between p-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition cursor-pointer">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Modul Iklan Media Online</div>
                                    <div class="text-[11px] text-slate-500">kabarpriangan.pikiran-rakyat.com advertorial &amp; banner</div>
                                </div>
                                <input type="checkbox" name="modul_iklan_online" value="1" {{ ($configs['modul_iklan_online'] ?? '1') == '1' ? 'checked' : '' }}
                                       class="w-4 h-4 text-kp-blue-600 rounded border-slate-300 focus:ring-kp-blue-500">
                            </label>

                            <label class="flex items-center justify-between p-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition cursor-pointer">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Modul Priangan TV (Streaming &amp; TV)</div>
                                    <div class="text-[11px] text-slate-500">Spot iklan siaran televisi lokal dan live streaming</div>
                                </div>
                                <input type="checkbox" name="modul_iklan_tv" value="1" {{ ($configs['modul_iklan_tv'] ?? '1') == '1' ? 'checked' : '' }}
                                       class="w-4 h-4 text-kp-blue-600 rounded border-slate-300 focus:ring-kp-blue-500">
                            </label>

                            <label class="flex items-center justify-between p-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition cursor-pointer">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Modul Sirkulasi &amp; Distribusi Agen</div>
                                    <div class="text-[11px] text-slate-500">Jatah oplah harian, jatah H+1, dan ekspedisi koran</div>
                                </div>
                                <input type="checkbox" name="modul_sirkulasi" value="1" {{ ($configs['modul_sirkulasi'] ?? '1') == '1' ? 'checked' : '' }}
                                       class="w-4 h-4 text-kp-blue-600 rounded border-slate-300 focus:ring-kp-blue-500">
                            </label>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-100 text-[11px] text-slate-500">
                        <span class="font-semibold text-slate-700">Perhatian:</span> Menonaktifkan modul akan menyembunyikan menu akses cepat dari pengguna non-administrator.
                    </div>
                </div>

            </div>

            <!-- Tombol Aksi Simpan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex items-center justify-between gap-4 sticky bottom-4 z-20">
                <div class="text-xs text-slate-500 hidden sm:block">
                    Pastikan seluruh data legalitas dan rekening bank telah sesuai dengan dokumen akta perusahaan.
                </div>
                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-5 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-lg shadow-sm hover:shadow transition flex items-center gap-2 cursor-pointer">
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
