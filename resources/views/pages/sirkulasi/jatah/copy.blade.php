<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman (Hanya Tampil di Layar) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-sky-600 uppercase tracking-wider">Operasional Jatah</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Copy Data Penjualan (SIRK.EXE)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Salin pola alokasi antar-tanggal kerja ke target edisi berikutnya dan cetak bukti hasil salinan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($hasCopied)
                    <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-sky-50 text-sky-800 border border-sky-200/80">
                        Format: A4 Landscape
                    </span>
                    <button type="button" onclick="window.print()" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-sky-700 hover:bg-sky-800 text-white shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        <span>Cetak Hasil Salin</span>
                    </button>
                @endif
                <a href="{{ route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal]) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition-colors inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    <span>Ke Edit Matriks</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="print:hidden p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-medium text-emerald-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                    <button type="button" onclick="window.print()" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white shadow-xs transition-colors inline-flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        <span>Cetak Hasil Salin</span>
                    </button>
                    <a href="{{ route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal]) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white border border-emerald-300 text-emerald-900 hover:bg-emerald-100 transition-colors inline-flex items-center gap-1">
                        <span>Sesuaikan di Edit Matriks &rarr;</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- Panel Form Konfigurasi Salin Pola Jatah (Hanya Tampil di Layar) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
            <div class="border-b border-slate-100 pb-3 mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Tentukan Tanggal untuk Menyalin Data Penjualan</h3>
                    <p class="text-[11px] text-slate-500">Pilih tanggal sumber (pola asal) dan tanggal target edisi kerja yang akan diterapkan.</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="setPreset('{{ $today->toDateString() }}', '{{ $tomorrow->toDateString() }}')" 
                            class="px-2.5 py-1 text-[11px] font-medium rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer">
                        Hari Ini &rarr; Besok
                    </button>
                    <button type="button" onclick="setPreset('{{ $lastWeekSameDay->toDateString() }}', '{{ $tomorrow->toDateString() }}')" 
                            class="px-2.5 py-1 text-[11px] font-medium rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer">
                        Minggu Lalu &rarr; Besok
                    </button>
                </div>
            </div>

            <form id="formSalinJatah" method="POST" action="{{ route('sirkulasi.jatah.copy.process') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tanggal Sumber (Pola Asal) <span class="text-rose-500">*</span></label>
                        <input type="date" id="inputSumberTanggal" name="sumber_tanggal" value="{{ $sumberTanggal }}" required
                               class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-sky-500 focus:ring-sky-500">
                        <p class="text-[10px] text-slate-400 mt-1">Pola asal: {{ \Carbon\Carbon::parse($sumberTanggal)->translatedFormat('l, d F Y') }}</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tanggal Target Distribusi <span class="text-rose-500">*</span></label>
                        <input type="date" id="inputTargetTanggal" name="target_tanggal" value="{{ $targetTanggal }}" required
                               class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-sky-500 focus:ring-sky-500">
                        <p class="text-[10px] text-slate-400 mt-1">Edisi tujuan: {{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('l, d F Y') }}</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Filter Rute Distribusi</label>
                        <select name="jurusan" class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-sky-500 focus:ring-sky-500">
                            <option value="all">Semua Rute Distribusi</option>
                            @foreach($jurusanList as $jur)
                                <option value="{{ $jur->kode }}" {{ ($jurusanPilihan ?? '') === $jur->kode ? 'selected' : '' }}>
                                    {{ $jur->kode }} - {{ $jur->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Pilih rute armada atau semua mitra agen</p>
                    </div>
                </div>

                <div class="p-3 bg-amber-50/80 border border-amber-200/80 rounded-lg text-amber-900 text-xs flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <div>
                        <span class="font-bold">Ketentuan Salin Data Penjualan:</span> Data penjualan dan jatah antar-agen akan disalin secara otomatis sesuai tanggal yang Anda tentukan di atas. Hasil salinan akan dimuat di tabel bawah dan dapat langsung dicetak.
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                    <button type="submit" name="action_type" value="stay_and_print" class="px-5 py-2 text-xs font-bold rounded-lg bg-sky-600 hover:bg-sky-700 text-white shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                        <span>Proses Salin Jatah & Siapkan Hasil Cetak</span>
                    </button>
                    <button type="submit" name="action_type" value="go_to_edit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-amber-600 hover:bg-amber-700 text-white shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        <span>Salin & Buka di Edit Matriks</span>
                    </button>
                </div>
            </form>
        </div>

        @if(!$hasCopied)
            <!-- State Kosong: Belum Ada Data yang Disalin -->
            <div class="bg-white border border-dashed border-slate-300 rounded-xl p-10 text-center shadow-xs">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-sky-50 text-sky-500 border border-sky-100 flex items-center justify-center mb-3.5 shadow-2xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Tabel Hasil Salin Masih Kosong</h4>
                <p class="text-xs text-slate-500 max-w-lg mx-auto mt-1 leading-relaxed">
                    Data penjualan belum disalin. Silakan tentukan <strong>Tanggal Sumber (Pola Asal)</strong> dan <strong>Tanggal Target Distribusi</strong> pada formulir di atas, lalu klik tombol <strong class="text-sky-700">"Proses Salin Jatah & Siapkan Hasil Cetak"</strong> untuk memproses dan mencetak hasil salinannya.
                </p>
                <div class="mt-4 flex items-center justify-center gap-2 text-[11px] text-slate-400">
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Menunggu proses penyalinan data
                    </span>
                    <span>&bull;</span>
                    <span>Pola asal yang dipilih: {{ \Carbon\Carbon::parse($sumberTanggal)->translatedFormat('d F Y') }}</span>
                </div>
            </div>
        @else
            <!-- KOP DOKUMEN CETAK RESMI (HANYA TAMPIL SAAT CETAK) -->
            <div class="hidden print:block mb-4 border-b-2 border-slate-900 pb-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-extrabold tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                        <p class="text-[11px] font-bold text-slate-700">DIVISI SIRKULASI & DISTRIBUSI — SISTEM OPERASIONAL SIRK.EXE</p>
                        <p class="text-[10px] text-slate-500">Jl. Sutisna Senjaya No. 120 Tasikmalaya &bull; Telp (0265) 331888 &bull; sirkulasi@kabarpriangan.co.id</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2 py-0.5 border border-slate-400 font-mono text-[10px] font-bold text-slate-800 uppercase">
                            KODE DOK: SIRK-COPY-02
                        </span>
                        <p class="text-[10px] text-slate-600 mt-1">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
                        <p class="text-[10px] text-slate-600">Operator: {{ Auth::user()->name }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-300 flex items-center justify-between">
                    <div>
                        <h1 class="text-sm font-black text-slate-900 uppercase tracking-tight">BUKTI REKAPITULASI HASIL SALIN (COPY) DATA PENJUALAN KORAN</h1>
                        <div class="text-[11px] text-slate-700 mt-0.5">
                            <span>Pola Asal (Sumber): <strong class="text-slate-900">{{ \Carbon\Carbon::parse($sumberTanggal)->translatedFormat('l, d F Y') }}</strong></span>
                            <span class="mx-2 text-slate-400">&rarr;</span>
                            <span>Edisi Tujuan (Target): <strong class="text-slate-900">{{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('l, d F Y') }}</strong></span>
                            <span class="mx-2 text-slate-400">&bull;</span>
                            <span>Rute: <strong class="text-slate-900">{{ $jurusanPilihan === 'all' ? 'Semua Rute Distribusi' : 'Rute ' . $jurusanPilihan }}</strong></span>
                        </div>
                    </div>
                    <div class="text-right text-[11px] font-semibold text-slate-700">
                        Grand Total Salin: <span class="font-black text-slate-900 text-xs">{{ number_format($grandTotalJatah, 0, ',', '.') }} eks</span>
                        &bull; Estimasi Omset: <span class="font-bold text-slate-900 text-xs">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- KPI Ringkasan Matriks Hasil Copy -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 print:grid print:grid-cols-5 print:gap-2 print:mb-3">
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                    <div class="text-[11px] text-slate-500 font-semibold uppercase">Langganan Disalin</div>
                    <div class="text-lg font-bold text-slate-900 mt-1 print:text-sm">{{ number_format($totalLangganan, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">eks</span></div>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                    <div class="text-[11px] text-emerald-600 font-semibold uppercase">Konsinyasi (Rp3.000)</div>
                    <div class="text-lg font-bold text-emerald-700 mt-1 print:text-sm">{{ number_format($totalKonsinyasi, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">eks</span></div>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                    <div class="text-[11px] text-sky-600 font-semibold uppercase">Jatah Gratis</div>
                    <div class="text-lg font-bold text-sky-700 mt-1 print:text-sm">{{ number_format($totalGratis, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">eks</span></div>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                    <div class="text-[11px] text-purple-600 font-semibold uppercase">Insidentil</div>
                    <div class="text-lg font-bold text-purple-700 mt-1 print:text-sm">{{ number_format($totalInsidentil, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">eks</span></div>
                </div>
                <div class="bg-sky-50 border border-sky-200 rounded-xl p-3.5 shadow-xs col-span-2 sm:col-span-1 print:col-span-1 print:p-2 print:border-slate-300 print:rounded-none print:bg-slate-50">
                    <div class="text-[11px] text-sky-800 font-semibold uppercase print:text-slate-800">Total Eksemplar Salin</div>
                    <div class="text-lg font-extrabold text-sky-900 mt-1 print:text-sm print:text-slate-900">{{ number_format($grandTotalJatah, 0, ',', '.') }} <span class="text-[10px] text-sky-700 print:text-slate-600 font-normal">eks</span></div>
                </div>
            </div>

            <!-- Tabel Hasil Copy Data Penjualan -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 print:px-0 print:py-2 print:bg-transparent print:border-b-2 print:border-slate-800">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 print:text-slate-900">Matriks Rincian Hasil Salin Data Penjualan Koran</h3>
                        <p class="text-[11px] text-slate-500 print:text-slate-700">
                            Disalin dari Edisi: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($sumberTanggal)->translatedFormat('d F Y') }}</strong>
                            ke Edisi Target: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('l, d F Y') }}</strong>
                        </p>
                    </div>
                    <div class="flex items-center gap-2 print:hidden">
                        <button type="button" onclick="window.print()" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-sky-700 hover:bg-sky-800 text-white shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            <span>Cetak Hasil Salin</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 min-w-[1050px] print:min-w-0 print:w-full print:text-[10px]">
                        <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200 print:bg-slate-100 print:text-slate-900 print:text-[9.5px]">
                            <tr>
                                <th class="py-3 px-3 w-12 text-center whitespace-nowrap print:py-1.5 print:px-2">No</th>
                                <th class="py-3 px-3.5 whitespace-nowrap w-28 print:py-1.5 print:px-2">Kode</th>
                                <th class="py-3 px-4 min-w-[170px] print:py-1.5 print:px-2">Nama Agen & Kios</th>
                                <th class="py-3 px-3 whitespace-nowrap min-w-[140px] print:py-1.5 print:px-2">Rute Distribusi</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap print:py-1.5 print:px-2">Langganan (Eks)</th>
                                <th class="py-3 px-3 text-center bg-emerald-50/50 text-emerald-900 whitespace-nowrap print:bg-transparent print:text-slate-900 print:py-1.5 print:px-2">Konsinyasi (Eks)</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap print:py-1.5 print:px-2">Gratis (Eks)</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap print:py-1.5 print:px-2">Insidentil (Eks)</th>
                                <th class="py-3 px-3.5 text-right font-extrabold text-slate-900 whitespace-nowrap w-24 print:py-1.5 print:px-2">Total Jatah</th>
                                <th class="py-3 px-4 text-right whitespace-nowrap w-32 print:py-1.5 print:px-2">Estimasi Omset</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap w-24 print:py-1.5 print:px-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium print:divide-slate-200">
                            @foreach($matrixJatah as $idx => $item)
                                <tr class="hover:bg-slate-50/80 transition-colors print:hover:bg-transparent">
                                    <td class="py-2.5 px-3 text-center text-slate-400 whitespace-nowrap print:py-1.5 print:px-2 print:text-slate-700">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-2.5 px-3.5 whitespace-nowrap print:py-1.5 print:px-2">
                                        <span class="inline-block px-2.5 py-1 rounded-md font-mono font-bold text-slate-900 bg-slate-100 text-xs border border-slate-200/80 print:bg-transparent print:border-none print:p-0 print:text-[10px]">
                                            {{ $item->kode_agen }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 print:py-1.5 print:px-2">
                                        <span class="font-bold text-slate-900 text-xs print:text-[10.5px]">{{ $item->nama_agen }}</span>
                                    </td>
                                    <td class="py-2.5 px-3 text-xs text-slate-600 whitespace-nowrap print:py-1.5 print:px-2 print:text-[10px]">
                                        <span class="font-semibold text-slate-800">Rute {{ $item->jurusan_kode }}</span>
                                        <span class="text-[11px] text-slate-400 print:text-slate-600">({{ Str::limit($item->jurusan_nama, 18) }})</span>
                                    </td>
                                    <td class="py-2.5 px-3 text-center whitespace-nowrap font-mono font-semibold text-slate-800 print:py-1.5 print:px-2">
                                        {{ number_format($item->langganan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center bg-emerald-50/30 whitespace-nowrap font-mono font-bold text-emerald-800 print:bg-transparent print:text-slate-900 print:py-1.5 print:px-2">
                                        {{ number_format($item->konsinyasi, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center whitespace-nowrap font-mono text-slate-700 print:py-1.5 print:px-2">
                                        {{ number_format($item->gratis, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center whitespace-nowrap font-mono text-slate-700 print:py-1.5 print:px-2">
                                        {{ number_format($item->insidentil, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-3.5 text-right font-black text-slate-900 text-xs whitespace-nowrap tabular-nums print:py-1.5 print:px-2 print:text-[10.5px]">
                                        {{ number_format($item->total_jatah, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-bold text-emerald-700 whitespace-nowrap tabular-nums font-mono print:py-1.5 print:px-2 print:text-slate-900 print:text-[10px]">
                                        Rp {{ number_format($item->estimasi_omset, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center whitespace-nowrap print:py-1.5 print:px-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200 print:bg-transparent print:border-none print:p-0 print:text-slate-800">
                                            Tersalin
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-100 font-bold text-slate-800 border-t-2 border-slate-200 print:bg-slate-100 print:text-slate-900 print:border-t-2 print:border-slate-800">
                            <tr>
                                <td colspan="4" class="py-3 px-4 text-right uppercase text-[11px] whitespace-nowrap print:py-2 print:px-2 print:text-[10px]">Akumulasi Total:</td>
                                <td class="py-3 px-3 text-center whitespace-nowrap tabular-nums print:py-2 print:px-2">{{ number_format($totalLangganan, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-center text-emerald-800 whitespace-nowrap tabular-nums print:text-slate-900 print:py-2 print:px-2">{{ number_format($totalKonsinyasi, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-center text-sky-800 whitespace-nowrap tabular-nums print:text-slate-900 print:py-2 print:px-2">{{ number_format($totalGratis, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-center text-purple-800 whitespace-nowrap tabular-nums print:text-slate-900 print:py-2 print:px-2">{{ number_format($totalInsidentil, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right text-sky-900 font-black text-sm whitespace-nowrap tabular-nums print:text-slate-900 print:py-2 print:px-2 print:text-xs">{{ number_format($grandTotalJatah, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right text-emerald-800 font-bold text-sm whitespace-nowrap tabular-nums font-mono print:text-slate-900 print:py-2 print:px-2 print:text-xs">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-center text-slate-400 print:hidden">-</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="p-4 bg-slate-50/60 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2 print:hidden">
                    <span>Pola salinan terhubung dengan jadwal pengiriman armada subuh dan matriks jatah harian.</span>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="window.print()" class="px-4 py-1.5 bg-sky-700 hover:bg-sky-800 text-white rounded-lg text-xs font-semibold shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            <span>Cetak Hasil Salin</span>
                        </button>
                        <a href="{{ route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal]) }}" class="px-4 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold shadow-2xs transition-colors inline-flex items-center gap-1.5">
                            <span>Lanjut ke Edit Matriks &rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tanda Tangan & Pengesahan Dokumen Salin (Hanya Tampil Saat Cetak) -->
            <div class="hidden print:grid grid-cols-3 text-center text-xs text-slate-800 mt-8 pt-4 border-t border-slate-300 break-inside-avoid">
                <div>
                    <p class="text-slate-500">Dibuat & Disalin Oleh,</p>
                    <p class="font-bold text-slate-900 mt-1">Petugas Operator Sirkulasi</p>
                    <div class="h-16"></div>
                    <p class="font-bold underline text-slate-900">( {{ Auth::user()->name }} )</p>
                    <p class="text-[10px] text-slate-400">Staf Sirkulasi</p>
                </div>
                <div>
                    <p class="text-slate-500">Diperiksa Oleh,</p>
                    <p class="font-bold text-slate-900 mt-1">Koordinator Distribusi Subuh</p>
                    <div class="h-16"></div>
                    <p class="font-bold underline text-slate-900">( ......................................... )</p>
                    <p class="text-[10px] text-slate-400">Koordinator Lapangan</p>
                </div>
                <div>
                    <p class="text-slate-500">Mengetahui / Menyetujui,</p>
                    <p class="font-bold text-slate-900 mt-1">Kepala Bagian Sirkulasi</p>
                    <div class="h-16"></div>
                    <p class="font-bold underline text-slate-900">( ......................................... )</p>
                    <p class="text-[10px] text-slate-400">Kabag Sirkulasi KP</p>
                </div>
            </div>
        @endif

    </div>

    @push('scripts')
    <script>
        function setPreset(sumber, target) {
            document.getElementById('inputSumberTanggal').value = sumber;
            document.getElementById('inputTargetTanggal').value = target;
        }
    </script>
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 8mm 10mm 10mm 10mm;
            }
            body {
                background-color: #ffffff !important;
                color: #0f172a !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .printable-area {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }
            table {
                border-collapse: collapse !important;
                width: 100% !important;
            }
            th, td {
                border: 1px solid #cbd5e1 !important;
            }
        }
    </style>
    @endpush
</x-app-layout>
