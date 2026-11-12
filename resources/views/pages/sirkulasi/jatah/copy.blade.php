<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Hanya Tampil di Layar, Standar Divisi Iklan 60-30-10) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Copy Data Penjualan Koran
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 leading-relaxed">
                        Penyalinan alokasi jatah koran harian dari edisi sebelumnya untuk persiapan distribusi agen.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                @if(!empty($isTargetLocked))
                    <button type="button" disabled
                            title="{{ $targetLockReason ?? 'Tanggal tujuan sudah ditutup / terkunci' }}"
                            class="inline-flex items-center gap-2 h-10 px-4 bg-slate-100 border border-slate-300 text-slate-500 text-xs sm:text-sm font-bold rounded-xl cursor-not-allowed whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        <span>Terkunci</span>
                    </button>
                @elseif(!$isFullyCopied)
                    <form method="POST"
                          action="{{ route('sirkulasi.jatah.copy.process') }}"
                          onsubmit="this.querySelector('[name=sumber_tanggal]').value = document.getElementById('inputSumberTanggal').value; this.querySelector('[name=target_tanggal]').value = document.getElementById('inputTargetTanggal').value; this.querySelector('[name=jurusan]').value = document.getElementById('selectJurusanCopy').value;">
                        @csrf
                        <input type="hidden" name="action_type" value="stay_and_print">
                        <input type="hidden" name="sumber_tanggal" value="{{ $sumberTanggal }}">
                        <input type="hidden" name="target_tanggal" value="{{ $targetTanggal }}">
                        <input type="hidden" name="jurusan" value="{{ $jurusanPilihan }}">
                        <button type="submit"
                                class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                            <span>Copy Jatah</span>
                        </button>
                    </form>
                @else
                    <button type="button" disabled
                            title="Data tanggal tujuan sudah disalin dan dilindungi agar perubahan di Input Oplah tidak tertimpa"
                            class="inline-flex items-center gap-2 h-10 px-4 bg-slate-100 border border-slate-300 text-slate-500 text-xs sm:text-sm font-bold rounded-xl cursor-not-allowed whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Sudah Disalin</span>
                    </button>
                @endif

                @if($hasCopied)
                    <a href="{{ route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal, 'jurusan' => $jurusanPilihan]) }}"
                       class="inline-flex items-center gap-2 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        <span>Input Oplah Harian</span>
                    </a>

                    <button type="button"
                            onclick="window.print()"
                            class="inline-flex items-center gap-2 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        <span>Cetak</span>
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="print:hidden p-3.5 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-sm font-semibold text-kp-blue-700 flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="print:hidden p-3.5 bg-slate-100 border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Panel Filter Tanggal & Rute (Standar Divisi Iklan dengan Tombol Terapkan, 60-30-10) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.jatah.copy') }}" class="flex flex-col lg:flex-row lg:items-end gap-3">
                <div class="w-full lg:w-44 shrink-0">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Dari Tanggal</label>
                    <input type="date" id="inputSumberTanggal" name="sumber_tanggal" value="{{ $sumberTanggal }}" required
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>

                <div class="w-full lg:w-44 shrink-0">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Ke Tanggal</label>
                    <input type="date" id="inputTargetTanggal" name="target_tanggal" value="{{ $targetTanggal }}" required
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>

                <div class="w-full lg:flex-1 min-w-0">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Rute</label>
                    <select id="selectJurusanCopy" name="jurusan"
                            class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Rute</option>
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->kode }}" {{ ($jurusanPilihan ?? '') === $jur->kode ? 'selected' : '' }}>
                                {{ $jur->kode }} - {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full lg:w-auto shrink-0 flex items-center gap-2">
                    <button type="submit"
                            class="flex-1 lg:flex-none h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer whitespace-nowrap">
                        Terapkan
                    </button>
                    @if(request()->hasAny(['sumber_tanggal', 'target_tanggal', 'jurusan']))
                        <a href="{{ route('sirkulasi.jatah.copy') }}"
                           title="Reset Filter"
                           class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center whitespace-nowrap">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if(!empty($isTargetLocked))
            <div class="print:hidden p-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    <span>
                        <strong class="text-slate-900">Tanggal Tujuan Terkunci:</strong> {{ $targetLockReason ?? 'Tanggal tujuan sudah ditutup dan tidak dapat disalin.' }}
                    </span>
                </div>
                <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $targetTanggal]) }}"
                   class="inline-flex items-center justify-center h-9 px-3.5 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition shrink-0 whitespace-nowrap">
                    Status Tutup Buku
                </a>
            </div>
        @endif

        @if($isFullyCopied)
            <div class="print:hidden p-3.5 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-xs sm:text-sm text-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    <span>
                        Data jatah untuk <strong class="text-slate-900">{{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('d M Y') }}</strong> sudah tersedia dan dikunci dari salin ulang agar tidak menimpa perubahan agen.
                    </span>
                </div>
            </div>
        @endif

        @if(!$hasCopied)
            <!-- State Kosong -->
            <div class="bg-white border border-dashed border-slate-300 rounded-xl p-10 text-center shadow-xs">
                <div class="w-12 h-12 mx-auto rounded-xl bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center mb-3.5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-900">Belum Ada Data Disalin</h4>
                @if(!empty($isTargetLocked))
                    <p class="text-xs sm:text-sm text-slate-500 max-w-lg mx-auto mt-1 leading-relaxed">
                        {{ $targetLockReason ?? 'Tanggal tujuan sudah ditutup dan tidak dapat disalin.' }}
                    </p>
                @else
                    <p class="text-xs sm:text-sm text-slate-500 max-w-lg mx-auto mt-1 leading-relaxed">
                        Klik <strong class="text-kp-blue-700">Copy Jatah</strong> untuk menyalin pola oplah dari tanggal asal ke tanggal tujuan, atau ubah filter lalu klik <strong class="text-slate-700">Terapkan</strong>.
                    </p>
                    <div class="mt-4">
                        <form method="POST"
                              action="{{ route('sirkulasi.jatah.copy.process') }}"
                              onsubmit="this.querySelector('[name=sumber_tanggal]').value = document.getElementById('inputSumberTanggal').value; this.querySelector('[name=target_tanggal]').value = document.getElementById('inputTargetTanggal').value; this.querySelector('[name=jurusan]').value = document.getElementById('selectJurusanCopy').value;"
                              class="inline-block">
                            @csrf
                            <input type="hidden" name="action_type" value="stay_and_print">
                            <input type="hidden" name="sumber_tanggal" value="{{ $sumberTanggal }}">
                            <input type="hidden" name="target_tanggal" value="{{ $targetTanggal }}">
                            <input type="hidden" name="jurusan" value="{{ $jurusanPilihan }}">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                                <span>Copy Jatah Sekarang</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @else
            <!-- KOP DOKUMEN CETAK RESMI (HANYA TAMPIL SAAT CETAK) -->
            <div class="hidden print:block mb-4 border-b-2 border-slate-900 pb-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-extrabold tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                        <p class="text-[11px] font-bold text-slate-700">DIVISI SIRKULASI &amp; DISTRIBUSI</p>
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
                        <h1 class="text-sm font-black text-slate-900 uppercase tracking-tight">BUKTI REKAPITULASI HASIL SALIN DATA PENJUALAN KORAN</h1>
                        <div class="text-[11px] text-slate-700 mt-0.5">
                            <span>Pola Asal: <strong class="text-slate-900">{{ \Carbon\Carbon::parse($copiedMeta['copied_from'] ?? $sumberTanggal)->translatedFormat('l, d F Y') }}</strong></span>
                            <span class="mx-2 text-slate-400">&rarr;</span>
                            <span>Edisi Target: <strong class="text-slate-900">{{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('l, d F Y') }}</strong></span>
                        </div>
                    </div>
                    <div class="text-right text-[11px] font-semibold text-slate-700">
                        Total Salin: <span class="font-black text-slate-900 text-xs">{{ number_format($grandTotalJatah, 0, ',', '.') }} eks</span>
                        &bull; Estimasi Omset: <span class="font-bold text-slate-900 text-xs">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- KPI Ringkasan Matriks Hasil Copy (60-30-10) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 print:grid print:grid-cols-6 print:gap-2 print:mb-3">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Langganan</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalLangganan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Konsinyasi</span>
                    <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalKonsinyasi, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Gratis</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalGratis, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pengenalan</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalPengenalan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Insidentil</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalInsidentil, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-kp-blue-50 border border-kp-blue-200 rounded-xl p-4 shadow-xs col-span-2 sm:col-span-1 print:col-span-1 print:p-2 print:border-slate-300 print:rounded-none print:bg-slate-50">
                    <span class="text-xs font-semibold text-kp-blue-700 uppercase tracking-wider block print:text-slate-800">Total Jatah</span>
                    <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-sm print:text-slate-900">{{ number_format($grandTotalJatah, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-kp-blue-600 print:text-slate-600">eks</span></p>
                </div>
            </div>

            <!-- Tabel Hasil Copy Jatah -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse print:min-w-0 print:w-full print:text-[10px]">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap print:bg-slate-100 print:text-slate-900">
                            <tr>
                                <th class="py-3 px-3.5 w-12 text-center print:py-1.5 print:px-2">No</th>
                                <th class="py-3 px-3.5 print:py-1.5 print:px-2">Kode Agen</th>
                                <th class="py-3 px-3.5 print:py-1.5 print:px-2">Nama Agen</th>
                                <th class="py-3 px-3.5 text-center print:py-1.5 print:px-2">Kode Rute</th>
                                <th class="py-3 px-3.5 print:py-1.5 print:px-2">Nama Rute</th>
                                <th class="py-3 px-3.5 text-right print:py-1.5 print:px-2">Langganan</th>
                                <th class="py-3 px-3.5 text-right print:py-1.5 print:px-2">Konsinyasi</th>
                                <th class="py-3 px-3.5 text-right print:py-1.5 print:px-2">Gratis</th>
                                <th class="py-3 px-3.5 text-right print:py-1.5 print:px-2">Pengenalan</th>
                                <th class="py-3 px-3.5 text-right print:py-1.5 print:px-2">Insidentil</th>
                                <th class="py-3 px-3.5 text-right print:py-1.5 print:px-2">Total</th>
                                <th class="py-3 px-3.5 text-right w-36 print:py-1.5 print:px-2">Omset</th>
                                <th class="py-3 px-3.5 text-center print:py-1.5 print:px-2">Status</th>
                                <th class="print:hidden py-3 px-3.5 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 print:divide-slate-200">
                            @foreach($matrixJatah->values() as $idx => $item)
                                <tr class="hover:bg-slate-50/80 transition print:hover:bg-transparent">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal print:py-1.5 print:px-2 print:text-slate-700">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm print:py-1.5 print:px-2 print:text-slate-900 print:text-[10px]">
                                        {{ $item->kode_agen }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900 print:py-1.5 print:px-2 print:text-[10.5px]">
                                        {{ $item->nama_agen }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center font-mono text-slate-600 font-normal print:py-1.5 print:px-2 print:text-[10px]">
                                        R-{{ $item->jurusan_kode }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-slate-600 font-normal print:py-1.5 print:px-2 print:text-[10px]">
                                        {{ $item->jurusan_nama }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-800 print:py-1.5 print:px-2">
                                        {{ number_format($item->langganan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right bg-kp-blue-50/40 font-mono font-semibold text-kp-blue-700 print:bg-transparent print:text-slate-900 print:py-1.5 print:px-2">
                                        {{ number_format($item->konsinyasi, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 print:py-1.5 print:px-2">
                                        {{ number_format($item->gratis, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 print:py-1.5 print:px-2">
                                        {{ number_format($item->pengenalan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 print:py-1.5 print:px-2">
                                        {{ number_format($item->insidentil, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-900 tabular-nums print:py-1.5 print:px-2 print:text-[10.5px]">
                                        {{ number_format($item->total_jatah, 0, ',', '.') }} eks
                                    </td>
                                    <td class="py-3.5 px-3.5 print:py-1.5 print:px-2">
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm print:text-[10px]">
                                            <span class="text-slate-400 font-normal select-none print:text-slate-800">Rp</span>
                                            <span class="font-semibold text-kp-blue-700 tabular-nums print:text-slate-900">{{ number_format($item->estimasi_omset, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center print:py-1.5 print:px-2">
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 print:bg-transparent print:border-none print:p-0 print:text-slate-800">
                                            Tersalin
                                        </span>
                                    </td>
                                    <td class="print:hidden py-3.5 px-3.5 text-center"
                                        x-data="{ open: false }"
                                        :class="{ 'relative z-50': open }">
                                        <div class="relative inline-block text-left" @click.outside="open = false">
                                            <button type="button"
                                                    @click="open = !open"
                                                    class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                    :class="{ 'text-slate-900 bg-slate-100': open }"
                                                    title="Pilihan Aksi">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="5" r="1.5" />
                                                    <circle cx="12" cy="12" r="1.5" />
                                                    <circle cx="12" cy="19" r="1.5" />
                                                </svg>
                                            </button>

                                            <div x-cloak
                                                 x-show="open"
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 scale-100"
                                                 x-transition:leave-end="opacity-0 scale-95"
                                                 class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">

                                                <a href="{{ route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal, 'jurusan' => $item->jurusan_kode]) }}"
                                                   @click="open = false"
                                                   class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Edit Jatah</span>
                                                </a>

                                                <a href="{{ route('sirkulasi.cetak.nota-distribusi', ['jurusan' => $item->jurusan_kode, 'tanggal' => $targetTanggal]) }}"
                                                   @click="open = false"
                                                   class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span>Surat Jalan Rute</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 font-semibold text-slate-900 border-t-2 border-slate-200 print:bg-slate-100 print:border-t-2 print:border-slate-800 print:text-[10px]">
                            <tr>
                                <td colspan="5" class="py-3.5 px-3.5 text-right uppercase text-xs font-bold text-slate-700 print:py-2 print:px-2">Total</td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalLangganan, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-kp-blue-700 tabular-nums print:text-slate-900 print:py-2 print:px-2">{{ number_format($totalKonsinyasi, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalGratis, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalPengenalan, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalInsidentil, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-900 tabular-nums print:py-2 print:px-2">{{ number_format($grandTotalJatah, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-bold text-kp-blue-700 tabular-nums print:text-slate-900 print:py-2 print:px-2">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-center text-slate-400 print:hidden">-</td>
                                <td class="print:hidden"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Tanda Tangan & Pengesahan Dokumen Salin (Hanya Tampil Saat Cetak) -->
            <div class="hidden print:grid grid-cols-3 text-center text-xs text-slate-800 mt-8 pt-4 border-t border-slate-300 break-inside-avoid">
                <div>
                    <p class="text-slate-500">Dibuat &amp; Disalin Oleh,</p>
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
