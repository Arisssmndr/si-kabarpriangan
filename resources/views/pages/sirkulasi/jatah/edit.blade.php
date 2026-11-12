<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Hanya Tampil di Layar, Standar Divisi Iklan 60-30-10) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Edit Data Penjualan Koran
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 leading-relaxed">
                        Pengaturan alokasi jatah koran harian per agen untuk langganan, konsinyasi, dan eceran.
                    </p>
                </div>
            </div>

            @if($hasJatahData)
                <div class="flex items-center gap-2.5 shrink-0">
                    <button type="button"
                            onclick="window.print()"
                            class="inline-flex items-center gap-2 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak</span>
                    </button>
                </div>
            @endif
        </div>

        @if(session('success'))
            <div class="print:hidden p-3.5 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-sm font-semibold text-kp-blue-700 flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="print:hidden p-3.5 bg-slate-100 border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(!empty($isCutoffLocked))
            <div class="print:hidden p-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    <span>
                        <strong class="text-slate-900">Terkunci (Hanya Baca):</strong> {{ $lockReason ?? 'Data edisi ini sudah ditutup dan tidak dapat diubah langsung.' }}
                    </span>
                </div>
                <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $targetTanggal]) }}"
                   class="inline-flex items-center justify-center h-9 px-3.5 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition shrink-0 whitespace-nowrap">
                    Status Tutup Buku
                </a>
            </div>
        @endif

        <!-- Filter Tanggal & Rute (Standar Divisi Iklan dengan Tombol Terapkan) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.jatah.edit') }}" class="flex flex-col lg:flex-row lg:items-end gap-3">
                <div class="w-full lg:w-52 shrink-0">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal</label>
                    <input type="date"
                           name="tanggal"
                           value="{{ $targetTanggal }}"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>
                <div class="w-full lg:flex-1 min-w-0">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Rute</label>
                    <select name="jurusan"
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
                    @if(request()->hasAny(['tanggal', 'jurusan']))
                        <a href="{{ route('sirkulasi.jatah.edit') }}"
                           title="Reset Filter"
                           class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center whitespace-nowrap">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

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
                        KODE DOK: SIRK-JAT-01
                    </span>
                    <p class="text-[10px] text-slate-600 mt-1">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
                    <p class="text-[10px] text-slate-600">Operator: {{ Auth::user()->name }}</p>
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-300 flex items-center justify-between">
                <div>
                    <h1 class="text-sm font-black text-slate-900 uppercase tracking-tight">MATRIKS ALOKASI DATA PENJUALAN KORAN (JATAH DISTRIBUSI H+1)</h1>
                    <p class="text-[11px] text-slate-700">
                        Edisi Terbit Distribusi: <strong class="text-slate-900">{{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('l, d F Y') }}</strong>
                        &bull; Filter Rute: <strong class="text-slate-900">{{ $jurusanPilihan === 'all' ? 'Semua Rute Distribusi' : 'Rute ' . $jurusanPilihan }}</strong>
                    </p>
                </div>
                <div class="text-right text-[11px] font-semibold text-slate-700">
                    Grand Total Jatah: <span class="font-black text-slate-900 text-xs">{{ number_format($grandTotalJatah, 0, ',', '.') }} eks</span>
                    &bull; Estimasi Omset: <span class="font-bold text-slate-900 text-xs">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if($hasJatahData)
            <!-- KPI Ringkasan Jatah (60-30-10) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 print:grid print:grid-cols-6 print:gap-2 print:mb-3">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Langganan</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm"><span id="kpi-langganan">{{ number_format($totalLangganan, 0, ',', '.') }}</span> <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Konsinyasi</span>
                    <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-sm"><span id="kpi-konsinyasi">{{ number_format($totalKonsinyasi, 0, ',', '.') }}</span> <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Gratis</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm"><span id="kpi-gratis">{{ number_format($totalGratis, 0, ',', '.') }}</span> <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pengenalan</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm"><span id="kpi-pengenalan">{{ number_format($totalPengenalan ?? 0, 0, ',', '.') }}</span> <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Insidentil</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm"><span id="kpi-insidentil">{{ number_format($totalInsidentil, 0, ',', '.') }}</span> <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-kp-blue-50 border border-kp-blue-200 rounded-xl p-4 shadow-xs col-span-2 sm:col-span-1 print:col-span-1 print:p-2 print:border-slate-300 print:rounded-none print:bg-slate-50">
                    <span class="text-xs font-semibold text-kp-blue-700 uppercase tracking-wider block print:text-slate-800">Total Jatah</span>
                    <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-sm print:text-slate-900"><span id="kpi-grand-total">{{ number_format($grandTotalJatah, 0, ',', '.') }}</span> <span class="text-xs font-sans font-normal text-kp-blue-600 print:text-slate-600">eks</span></p>
                </div>
            </div>
        @endif

        <!-- Form Daftar Jatah Agen (60-30-10) -->
        <form id="formEditJatah" method="POST" action="{{ route('sirkulasi.jatah.edit.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="tanggal_target" value="{{ $targetTanggal }}">

            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
                @if(!$hasJatahData)
                    <div class="p-8 sm:p-12 text-center bg-white">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 text-slate-500 mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Belum Ada Data Jatah</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                            Silakan copy jatah dari tanggal sebelumnya terlebih dahulu.
                        </p>
                        <div class="mt-4 print:hidden">
                            <a href="{{ route('sirkulasi.jatah.copy', ['sumber_tanggal' => $prevTanggal, 'target_tanggal' => $targetTanggal, 'jurusan' => $jurusanPilihan]) }}"
                               class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-sm font-bold rounded-xl shadow-xs transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                </svg>
                                <span>Copy Jatah</span>
                            </a>
                        </div>
                    </div>
                @else
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse print:min-w-0 print:w-full print:text-[10px]">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap print:bg-slate-100 print:text-slate-900">
                            <tr>
                                <th class="py-3 px-3.5 w-12 text-center print:py-1.5 print:px-2">No</th>
                                <th class="py-3 px-3.5 print:py-1.5 print:px-2">Kode Agen</th>
                                <th class="py-3 px-3.5 print:py-1.5 print:px-2">Nama Agen</th>
                                <th class="py-3 px-3.5 text-center print:py-1.5 print:px-2">Kode Rute</th>
                                <th class="py-3 px-3.5 print:py-1.5 print:px-2">Nama Rute</th>
                                <th class="py-3 px-3.5 text-center w-28 print:py-1.5 print:px-2">Langganan</th>
                                <th class="py-3 px-3.5 text-center w-28 print:py-1.5 print:px-2">Konsinyasi</th>
                                <th class="py-3 px-3.5 text-center w-28 print:py-1.5 print:px-2">Gratis</th>
                                <th class="py-3 px-3.5 text-center w-28 print:py-1.5 print:px-2">Pengenalan</th>
                                <th class="py-3 px-3.5 text-center w-28 print:py-1.5 print:px-2">Insidentil</th>
                                <th class="py-3 px-3.5 text-right print:py-1.5 print:px-2">Total</th>
                                <th class="py-3 px-3.5 text-right w-36 print:py-1.5 print:px-2">Omset</th>
                                <th class="print:hidden py-3 px-3.5 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 print:divide-slate-200">
                            @foreach($matrixJatah->values() as $idx => $item)
                                <tr class="jatah-row hover:bg-slate-50/80 transition print:hover:bg-transparent" data-agen-id="{{ $item->agen_id }}">
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
                                    <td class="py-3 px-2.5 text-center print:py-1.5 print:px-2">
                                        @if(!empty($isCutoffLocked))
                                            <span class="font-mono font-semibold text-slate-800">{{ number_format($item->langganan, 0, ',', '.') }}</span>
                                        @else
                                            <span class="hidden print:inline-block font-mono font-bold text-slate-900">{{ number_format($item->langganan, 0, ',', '.') }}</span>
                                            <span class="print:hidden inline-block">
                                                <input type="number" name="jatah[{{ $item->agen_id }}][langganan]" value="{{ $item->langganan }}" data-field="langganan" data-original="{{ $item->langganan }}" data-base-border="border-slate-300" min="0" class="jatah-input w-20 h-9 text-center text-sm rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono font-semibold text-slate-900 transition">
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2.5 text-center bg-kp-blue-50/40 print:bg-transparent print:py-1.5 print:px-2">
                                        @if(!empty($isCutoffLocked))
                                            <span class="font-mono font-bold text-kp-blue-700 print:text-slate-900">{{ number_format($item->konsinyasi, 0, ',', '.') }}</span>
                                        @else
                                            <span class="hidden print:inline-block font-mono font-bold text-slate-900">{{ number_format($item->konsinyasi, 0, ',', '.') }}</span>
                                            <span class="print:hidden inline-block">
                                                <input type="number" name="jatah[{{ $item->agen_id }}][konsinyasi]" value="{{ $item->konsinyasi }}" data-field="konsinyasi" data-original="{{ $item->konsinyasi }}" data-base-border="border-kp-blue-200" min="0" class="jatah-input w-20 h-9 text-center text-sm rounded-lg border-kp-blue-200 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono font-bold text-kp-blue-700 transition">
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2.5 text-center print:py-1.5 print:px-2">
                                        @if(!empty($isCutoffLocked))
                                            <span class="font-mono font-semibold text-slate-800">{{ number_format($item->gratis, 0, ',', '.') }}</span>
                                        @else
                                            <span class="hidden print:inline-block font-mono font-semibold text-slate-800">{{ number_format($item->gratis, 0, ',', '.') }}</span>
                                            <span class="print:hidden inline-block">
                                                <input type="number" name="jatah[{{ $item->agen_id }}][gratis]" value="{{ $item->gratis }}" data-field="gratis" data-original="{{ $item->gratis }}" data-base-border="border-slate-300" min="0" class="jatah-input w-20 h-9 text-center text-sm rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono font-semibold text-slate-900 transition">
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2.5 text-center print:py-1.5 print:px-2">
                                        @if(!empty($isCutoffLocked))
                                            <span class="font-mono font-semibold text-slate-800">{{ number_format($item->pengenalan, 0, ',', '.') }}</span>
                                        @else
                                            <span class="hidden print:inline-block font-mono font-semibold text-slate-800">{{ number_format($item->pengenalan, 0, ',', '.') }}</span>
                                            <span class="print:hidden inline-block">
                                                <input type="number" name="jatah[{{ $item->agen_id }}][pengenalan]" value="{{ $item->pengenalan }}" data-field="pengenalan" data-original="{{ $item->pengenalan }}" data-base-border="border-slate-300" min="0" class="jatah-input w-20 h-9 text-center text-sm rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono font-semibold text-slate-900 transition">
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2.5 text-center print:py-1.5 print:px-2">
                                        @if(!empty($isCutoffLocked))
                                            <span class="font-mono font-semibold text-slate-800">{{ number_format($item->insidentil, 0, ',', '.') }}</span>
                                        @else
                                            <span class="hidden print:inline-block font-mono font-semibold text-slate-800">{{ number_format($item->insidentil, 0, ',', '.') }}</span>
                                            <span class="print:hidden inline-block">
                                                <input type="number" name="jatah[{{ $item->agen_id }}][insidentil]" value="{{ $item->insidentil }}" data-field="insidentil" data-original="{{ $item->insidentil }}" data-base-border="border-slate-300" min="0" class="jatah-input w-20 h-9 text-center text-sm rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono font-semibold text-slate-900 transition">
                                            </span>
                                        @endif
                                    </td>
                                    <td class="row-total-eks py-3.5 px-3.5 text-right font-mono font-bold text-slate-900 tabular-nums print:py-1.5 print:px-2 print:text-[10.5px]">
                                        {{ number_format($item->total_jatah, 0, ',', '.') }} eks
                                    </td>
                                    <td class="py-3.5 px-3.5 print:py-1.5 print:px-2">
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm print:text-[10px]">
                                            <span class="text-slate-400 font-normal select-none print:text-slate-800">Rp</span>
                                            <span class="row-omset-val font-semibold text-kp-blue-700 tabular-nums print:text-slate-900">{{ number_format($item->estimasi_omset, 0, ',', '.') }}</span>
                                        </div>
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

                                                <a href="{{ route('sirkulasi.tabel.agen', ['search' => $item->kode_agen]) }}"
                                                   @click="open = false"
                                                   class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Detail Agen</span>
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
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-semibold text-slate-900 text-sm whitespace-nowrap print:bg-slate-100 print:border-t-2 print:border-slate-800 print:text-[10px]">
                            <tr>
                                <td colspan="5" class="py-3.5 px-3.5 text-right uppercase text-xs font-bold text-slate-700 print:py-2 print:px-2">Total</td>
                                <td id="foot-total-langganan" class="py-3.5 px-3 text-center font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalLangganan, 0, ',', '.') }}</td>
                                <td id="foot-total-konsinyasi" class="py-3.5 px-3 text-center font-mono text-kp-blue-700 tabular-nums print:text-slate-900 print:py-2 print:px-2">{{ number_format($totalKonsinyasi, 0, ',', '.') }}</td>
                                <td id="foot-total-gratis" class="py-3.5 px-3 text-center font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalGratis, 0, ',', '.') }}</td>
                                <td id="foot-total-pengenalan" class="py-3.5 px-3 text-center font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalPengenalan ?? 0, 0, ',', '.') }}</td>
                                <td id="foot-total-insidentil" class="py-3.5 px-3 text-center font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalInsidentil, 0, ',', '.') }}</td>
                                <td id="foot-grand-total-eks" class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-900 tabular-nums print:py-2 print:px-2">{{ number_format($grandTotalJatah, 0, ',', '.') }} eks</td>
                                <td id="foot-grand-total-omset" class="py-3.5 px-3.5 text-right font-mono font-bold text-kp-blue-700 tabular-nums print:text-slate-900 print:py-2 print:px-2">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</td>
                                <td class="print:hidden"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
            </div>

            @if($hasJatahData && empty($isCutoffLocked))
                <!-- Bilah Simpan Kontekstual (Simpel & Selaras 60-30-10, Muncul Hanya Saat Ada Perubahan) -->
                <div id="dirtySaveBar" class="hidden print:hidden sticky bottom-4 z-30 bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-lg flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="text-sm text-slate-600">
                        <span id="dirtyAgentCountText" class="font-semibold text-slate-900">0 agen diubah</span>
                        <span class="text-slate-300 mx-1.5">&bull;</span>
                        <span class="text-xs text-slate-500">Belum disimpan</span>
                    </div>
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button type="button"
                                id="btnBatalOplah"
                                class="inline-flex items-center justify-center h-9 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-lg text-sm font-semibold transition cursor-pointer">
                            Batalkan
                        </button>
                        <button type="submit"
                                id="btnSimpanOplah"
                                class="inline-flex items-center justify-center gap-2 h-9 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-lg text-sm font-bold shadow-xs transition cursor-pointer">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span id="btnSimpanLabel">Simpan Perubahan</span>
                        </button>
                    </div>
                </div>
            @endif
        </form>

        <!-- Tanda Tangan & Pengesahan Dokumen (Hanya Tampil Saat Cetak) -->
        <div class="hidden print:grid grid-cols-3 text-center text-xs text-slate-800 mt-8 pt-4 border-t border-slate-300 break-inside-avoid">
            <div>
                <p class="text-slate-500">Dibuat Oleh,</p>
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

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formEditJatah');
            const inputs = form ? Array.from(form.querySelectorAll('input.jatah-input')) : [];
            if (!form || inputs.length === 0) return;

            const hargaEceran = {{ (int) ($hargaEceran ?? 3000) }};
            const rows = Array.from(form.querySelectorAll('tr.jatah-row'));
            const dirtySaveBar = document.getElementById('dirtySaveBar');
            const dirtyAgentCountText = document.getElementById('dirtyAgentCountText');
            const btnBatal = document.getElementById('btnBatalOplah');
            const btnSimpan = document.getElementById('btnSimpanOplah');
            const btnSimpanLabel = document.getElementById('btnSimpanLabel');

            let dirtyAgentsCount = 0;
            let isSubmitting = false;

            const formatId = (num) => new Intl.NumberFormat('id-ID').format(num);

            const parsePositiveInt = (val) => {
                const parsed = parseInt(String(val ?? '').trim(), 10);
                return Number.isNaN(parsed) || parsed < 0 ? 0 : parsed;
            };

            function recalculateAndTrack() {
                const totals = {
                    langganan: 0,
                    konsinyasi: 0,
                    gratis: 0,
                    pengenalan: 0,
                    insidentil: 0,
                };
                let grandTotalEks = 0;
                let grandTotalOmset = 0;
                let changedRowCount = 0;

                rows.forEach((row) => {
                    let rowEks = 0;
                    let rowKonsinyasi = 0;
                    let rowHasChange = false;

                    const rowInputs = row.querySelectorAll('input.jatah-input');
                    rowInputs.forEach((inp) => {
                        const field = inp.dataset.field;
                        const original = parsePositiveInt(inp.dataset.original);
                        const current = parsePositiveInt(inp.value);
                        const baseBorder = inp.dataset.baseBorder || 'border-slate-300';

                        // Deteksi perubahan karakter/nilai terhadap data awal
                        const isRawEmptyOrChanged = inp.value.trim() === ''
                            ? original !== 0
                            : current !== original;

                        if (isRawEmptyOrChanged) {
                            rowHasChange = true;
                            inp.classList.remove(baseBorder, 'bg-white');
                            inp.classList.add('bg-kp-blue-50/70', 'border-kp-blue-500', 'ring-1', 'ring-kp-blue-500/30');
                        } else {
                            inp.classList.remove('bg-kp-blue-50/70', 'border-kp-blue-500', 'ring-1', 'ring-kp-blue-500/30');
                            inp.classList.add(baseBorder);
                        }

                        if (field && Object.prototype.hasOwnProperty.call(totals, field)) {
                            totals[field] += current;
                        }
                        if (field === 'konsinyasi') {
                            rowKonsinyasi = current;
                        }
                        rowEks += current;
                    });

                    if (rowHasChange) {
                        changedRowCount++;
                    }

                    const rowOmset = rowKonsinyasi * hargaEceran;
                    grandTotalEks += rowEks;
                    grandTotalOmset += rowOmset;

                    const rowTotalEl = row.querySelector('.row-total-eks');
                    const rowOmsetEl = row.querySelector('.row-omset-val');
                    if (rowTotalEl) rowTotalEl.textContent = formatId(rowEks) + ' eks';
                    if (rowOmsetEl) rowOmsetEl.textContent = formatId(rowOmset);
                });

                // Update Footer Tabel
                const setText = (id, text) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = text;
                };

                setText('foot-total-langganan', formatId(totals.langganan));
                setText('foot-total-konsinyasi', formatId(totals.konsinyasi));
                setText('foot-total-gratis', formatId(totals.gratis));
                setText('foot-total-pengenalan', formatId(totals.pengenalan));
                setText('foot-total-insidentil', formatId(totals.insidentil));
                setText('foot-grand-total-eks', formatId(grandTotalEks) + ' eks');
                setText('foot-grand-total-omset', 'Rp ' + formatId(grandTotalOmset));

                // Update KPI Cards di Atas Tabel
                setText('kpi-langganan', formatId(totals.langganan));
                setText('kpi-konsinyasi', formatId(totals.konsinyasi));
                setText('kpi-gratis', formatId(totals.gratis));
                setText('kpi-pengenalan', formatId(totals.pengenalan));
                setText('kpi-insidentil', formatId(totals.insidentil));
                setText('kpi-grand-total', formatId(grandTotalEks));

                // Update Sticky Save Bar
                dirtyAgentsCount = changedRowCount;
                if (dirtySaveBar) {
                    if (changedRowCount > 0) {
                        dirtySaveBar.classList.remove('hidden');
                        dirtySaveBar.classList.add('flex');
                    } else {
                        dirtySaveBar.classList.add('hidden');
                        dirtySaveBar.classList.remove('flex');
                    }
                }
                if (dirtyAgentCountText) {
                    dirtyAgentCountText.textContent = changedRowCount + ' agen diubah';
                }
            }

            inputs.forEach((inp) => {
                inp.addEventListener('input', recalculateAndTrack);
                inp.addEventListener('blur', function () {
                    const normalized = parsePositiveInt(this.value);
                    this.value = String(normalized);
                    recalculateAndTrack();
                });
            });

            if (btnBatal) {
                btnBatal.addEventListener('click', function () {
                    inputs.forEach((inp) => {
                        inp.value = inp.dataset.original ?? '0';
                    });
                    recalculateAndTrack();
                });
            }

            form.addEventListener('submit', function () {
                inputs.forEach((inp) => {
                    inp.value = String(parsePositiveInt(inp.value));
                });
                isSubmitting = true;
                if (btnSimpan) {
                    btnSimpan.disabled = true;
                    btnSimpan.classList.add('opacity-75', 'cursor-not-allowed');
                }
                if (btnSimpanLabel) {
                    btnSimpanLabel.textContent = 'Menyimpan...';
                }
            });

            window.addEventListener('beforeunload', function (e) {
                if (dirtyAgentsCount > 0 && !isSubmitting) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        });
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
