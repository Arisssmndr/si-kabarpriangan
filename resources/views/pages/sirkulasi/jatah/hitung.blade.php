<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Hanya Tampil di Layar, Standar Divisi Iklan 60-30-10) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Hitung Data Penjualan
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 leading-relaxed">
                        Kalkulasi akumulasi eksemplar cetak, rincian langganan, konsinyasi agen Rp {{ number_format($hargaEceran ?? 3000, 0, ',', '.') }}, dan estimasi omset.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <form method="GET" action="{{ route('sirkulasi.jatah.hitung') }}" class="flex items-center gap-2">
                    <input type="date"
                           name="tanggal"
                           value="{{ $tanggalPilihan }}"
                           class="h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                    <button type="submit" class="h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        Filter Edisi
                    </button>
                </form>

                <button type="button"
                        onclick="window.print()"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Hasil Hitung</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="print:hidden p-3.5 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-sm font-semibold text-kp-blue-700 flex items-center justify-between gap-2 shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="window.print()" class="h-8 px-3 text-xs font-bold rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white transition inline-flex items-center gap-1 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Sekarang</span>
                </button>
            </div>
        @endif

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
                        KODE DOK: SIRK-HIT-03
                    </span>
                    <p class="text-[10px] text-slate-600 mt-1">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
                    <p class="text-[10px] text-slate-600">Operator: {{ Auth::user()->name }}</p>
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-300 flex items-center justify-between">
                <div>
                    <h1 class="text-sm font-black text-slate-900 uppercase tracking-tight">REKAPITULASI KALKULASI DATA PENJUALAN PER JURUSAN</h1>
                    <p class="text-[11px] text-slate-700">
                        Edisi Terbit Distribusi: <strong class="text-slate-900">{{ \Carbon\Carbon::parse($tanggalPilihan)->translatedFormat('l, d F Y') }}</strong>
                        &bull; Wilayah: <strong class="text-slate-900">Seluruh 15 Jurusan Armada Distribusi</strong>
                    </p>
                </div>
                <div class="text-right text-[11px] font-semibold text-slate-700">
                    Total Produksi Subuh: <span class="font-black text-slate-900 text-xs">{{ number_format($grandTotalEks, 0, ',', '.') }} eks</span>
                    &bull; Estimasi Omset: <span class="font-bold text-slate-900 text-xs">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Ringkasan Akumulasi KPI Cards (60-30-10) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 print:grid print:grid-cols-6 print:gap-2 print:mb-3">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Langganan</span>
                <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalLangganan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                <span class="text-xs text-slate-400 mt-0.5 block print:hidden">Instansi &amp; Warga</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Konsinyasi</span>
                <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-sm print:text-slate-900">{{ number_format($totalKonsinyasi, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                <span class="text-xs text-slate-400 mt-0.5 block print:hidden">Tarif Rp {{ number_format($hargaEceran ?? 3000, 0, ',', '.') }}/eks</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jatah Gratis</span>
                <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalGratis, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                <span class="text-xs text-slate-400 mt-0.5 block print:hidden">Arsip &amp; Bukti Iklan</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pengenalan</span>
                <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalPengenalan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                <span class="text-xs text-slate-400 mt-0.5 block print:hidden">Trial Calon Baru</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Insidentil</span>
                <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalInsidentil, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                <span class="text-xs text-slate-400 mt-0.5 block print:hidden">Edisi Khusus</span>
            </div>

            <div class="bg-kp-blue-50 border border-kp-blue-200 rounded-xl p-4 shadow-xs col-span-2 sm:col-span-1 print:col-span-1 print:p-2 print:border-slate-300 print:rounded-none print:bg-slate-50">
                <span class="text-xs font-semibold text-kp-blue-700 uppercase tracking-wider block print:text-slate-800">Total Oplah</span>
                <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-sm print:text-slate-900">{{ number_format($grandTotalEks, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-kp-blue-600 print:text-slate-600">eks</span></p>
                <span class="text-xs text-kp-blue-700 font-mono mt-0.5 block print:hidden">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Tabel Akumulasi per 15 Jurusan (Standar Divisi Iklan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
            <div class="px-4 py-3.5 border-b border-slate-200 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 print:px-0 print:py-2 print:bg-transparent print:border-b-2 print:border-slate-800">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Rincian Kalkulasi Jatah Penjualan per Jurusan</h2>
                    <p class="text-xs text-slate-500 print:text-slate-700">Edisi: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($tanggalPilihan)->translatedFormat('l, d F Y') }}</strong> &bull; 15 Rute Armada Subuh</p>
                </div>
                <form method="POST" action="{{ route('sirkulasi.jatah.hitung.process') }}" class="inline print:hidden">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                    <button type="submit" class="h-9 px-3.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold shadow-2xs transition cursor-pointer inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        <span>Kalkulasi Ulang</span>
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse print:min-w-0 print:w-full print:text-[10px]">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap print:bg-slate-100 print:text-slate-900">
                        <tr>
                            <th class="py-3 px-3.5 w-16 text-center print:py-1.5 print:px-2">Rute</th>
                            <th class="py-3 px-3.5 print:py-1.5 print:px-2">Nama Jurusan</th>
                            <th class="py-3 px-3.5 print:py-1.5 print:px-2">Armada</th>
                            <th class="py-3 px-3.5 text-right print:py-1.5 print:px-2">Langganan</th>
                            <th class="py-3 px-3.5 text-right print:py-1.5 print:px-2">Konsinyasi</th>
                            <th class="py-3 px-3.5 text-right print:py-1.5 print:px-2">Gratis</th>
                            <th class="py-3 px-3.5 text-right print:py-1.5 print:px-2">Pengenalan</th>
                            <th class="py-3 px-3.5 text-right print:py-1.5 print:px-2">Insidentil</th>
                            <th class="py-3 px-3.5 text-right print:py-1.5 print:px-2">Total Eksemplar</th>
                            <th class="py-3 px-3.5 text-right w-40 print:py-1.5 print:px-2">Omset Konsinyasi</th>
                            <th class="print:hidden py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 print:divide-slate-200">
                        @foreach($rekapKalkulasi as $rk)
                            <tr class="hover:bg-slate-50/80 transition print:hover:bg-transparent">
                                <td class="py-3.5 px-3.5 text-center font-mono font-bold text-kp-blue-700 text-sm print:text-slate-900 print:py-1.5 print:px-2 print:text-[10px]">
                                    {{ $rk->kode }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900 print:py-1.5 print:px-2 print:text-[10.5px]">
                                    {{ $rk->nama_jurusan }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal print:py-1.5 print:px-2 print:text-[10px]">
                                    {{ $rk->armada }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-800 tabular-nums print:py-1.5 print:px-2">
                                    {{ number_format($rk->langganan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums print:text-slate-900 print:py-1.5 print:px-2">
                                    {{ number_format($rk->konsinyasi, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums print:py-1.5 print:px-2">
                                    {{ number_format($rk->gratis, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums print:text-slate-900 print:py-1.5 print:px-2">
                                    {{ number_format($rk->pengenalan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums print:py-1.5 print:px-2">
                                    {{ number_format($rk->insidentil, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-900 tabular-nums print:py-1.5 print:px-2 print:text-[10.5px]">
                                    {{ number_format($rk->total_eks, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3.5 px-3.5 print:py-1.5 print:px-2">
                                    <div class="flex justify-between items-center gap-2 max-w-[135px] ml-auto font-mono text-sm print:text-[10px]">
                                        <span class="text-slate-400 font-normal select-none print:text-slate-800">Rp</span>
                                        <span class="font-semibold text-kp-blue-700 tabular-nums print:text-slate-900">{{ number_format($rk->omset_konsinyasi, 0, ',', '.') }}</span>
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

                                            <a href="{{ route('sirkulasi.jatah.edit', ['tanggal' => $tanggalPilihan, 'jurusan' => $rk->kode]) }}"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Edit Jatah Rute</span>
                                            </a>

                                            <a href="{{ route('sirkulasi.cetak.nota-distribusi', ['jurusan' => $rk->kode, 'tanggal' => $tanggalPilihan]) }}"
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
                            <td colspan="3" class="py-3.5 px-3.5 text-right uppercase text-xs font-bold text-slate-700 whitespace-nowrap print:py-2 print:px-2">Total Akumulasi:</td>
                            <td class="py-3.5 px-3.5 text-right font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalLangganan, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3.5 text-right font-mono text-kp-blue-700 tabular-nums print:text-slate-900 print:py-2 print:px-2">{{ number_format($totalKonsinyasi, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3.5 text-right font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalGratis, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3.5 text-right font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalPengenalan, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3.5 text-right font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalInsidentil, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-900 tabular-nums print:py-2 print:px-2">{{ number_format($grandTotalEks, 0, ',', '.') }} eks</td>
                            <td class="py-3.5 px-3.5 text-right font-mono font-bold text-kp-blue-700 tabular-nums print:text-slate-900 print:py-2 print:px-2">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</td>
                            <td class="print:hidden"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Tanda Tangan & Pengesahan Dokumen Hitung (Hanya Tampil Saat Cetak) -->
        <div class="hidden print:grid grid-cols-3 text-center text-xs text-slate-800 mt-8 pt-4 border-t border-slate-300 break-inside-avoid">
            <div>
                <p class="text-slate-500">Dihitung Oleh,</p>
                <p class="font-bold text-slate-900 mt-1">Petugas Operasional Sirkulasi</p>
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
