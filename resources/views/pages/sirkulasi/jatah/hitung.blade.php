<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print print:hidden">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Hitung Data Penjualan
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Kalkulasi oplah cetak, konsinyasi agen, dan estimasi omset per jurusan.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <form method="POST" action="{{ route('sirkulasi.jatah.hitung.process') }}" class="inline">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                    <button type="submit" class="inline-flex items-center gap-2 h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer whitespace-nowrap">
                        <span>Kalkulasi Ulang</span>
                    </button>
                </form>

                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Hasil Hitung</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="no-print print:hidden p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between gap-2 shadow-2xs">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="window.print()" class="h-9 px-3.5 text-xs font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white shadow-xs transition inline-flex items-center cursor-pointer">
                    <span>Cetak Sekarang</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="no-print print:hidden p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filter Edisi -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs no-print print:hidden">
            <form method="GET" action="{{ route('sirkulasi.jatah.hitung') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Edisi Distribusi</label>
                    <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>
                <div class="sm:col-span-3">
                    <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Filter Edisi
                    </button>
                </div>
            </form>
        </div>

        <!-- KOP DOKUMEN CETAK RESMI -->
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
                    <h1 class="text-sm font-black text-slate-900 uppercase tracking-tight">REKAPITULASI KALKULASI &amp; HITUNG DATA PENJUALAN PER JURUSAN</h1>
                    <p class="text-[11px] text-slate-700">
                        Edisi Terbit: <strong class="text-slate-900">{{ \Carbon\Carbon::parse($tanggalPilihan)->translatedFormat('l, d F Y') }}</strong>
                        &bull; Wilayah: <strong class="text-slate-900">15 Jurusan Armada Distribusi</strong>
                    </p>
                </div>
                <div class="text-right text-[11px] font-semibold text-slate-700">
                    Total Produksi Subuh: <span class="font-black text-slate-900 text-xs">{{ number_format($grandTotalEks, 0, ',', '.') }} eks</span>
                    &bull; Estimasi Omset: <span class="font-bold text-slate-900 text-xs">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Ringkasan Akumulasi KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:grid print:grid-cols-4 print:gap-2 print:mb-3">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Oplah Cetak</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">{{ number_format($grandTotalEks, 0, ',', '.') }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Target produksi subuh (Eks)</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Konsinyasi Agen</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalKonsinyasi, 0, ',', '.') }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Tarif komersil @ Rp3.000 (Eks)</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pelanggan Langganan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalLangganan, 0, ',', '.') }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Instansi, Pemda &amp; pelanggan (Eks)</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Estimasi Nilai Omset</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Tagihan berjalan agen</span>
            </div>
        </div>

        <!-- Tabel Akumulasi per 15 Jurusan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 print:hidden">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Kalkulasi Jatah Penjualan per Jurusan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Edisi {{ \Carbon\Carbon::parse($tanggalPilihan)->translatedFormat('d F Y') }} &bull; 15 Rute Armada</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono w-fit">
                    {{ count($rekapKalkulasi) }} Jurusan &bull; {{ number_format($grandTotalEks, 0, ',', '.') }} Eks
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5 w-20">Rute</th>
                            <th class="py-3 px-3.5">Nama Jurusan Armada</th>
                            <th class="py-3 px-3.5 text-right">Langganan</th>
                            <th class="py-3 px-3.5 text-right">Konsinyasi (Rp3.000)</th>
                            <th class="py-3 px-3.5 text-right">Gratis</th>
                            <th class="py-3 px-3.5 text-right">Insidentil</th>
                            <th class="py-3 px-3.5 text-right">Total Eksemplar</th>
                            <th class="py-3 px-3.5 text-right w-36">Omset Konsinyasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @foreach($rekapKalkulasi as $idx => $rk)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal w-12">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $rk->kode }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $rk->nama_jurusan }}
                                    <span class="hidden print:inline-block text-[9.5px] text-slate-500 ml-1">({{ $rk->armada }})</span>
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">
                                    {{ number_format($rk->langganan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums">
                                    {{ number_format($rk->konsinyasi, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">
                                    {{ number_format($rk->gratis, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">
                                    {{ number_format($rk->insidentil, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    {{ number_format($rk->total_eks, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($rk->omset_konsinyasi, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                        <tr>
                            <td colspan="3" class="py-3 px-3.5 text-right font-sans text-xs font-bold uppercase tracking-wider text-slate-700">Total Akumulasi</td>
                            <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalLangganan, 0, ',', '.') }}</td>
                            <td class="py-3 px-3.5 text-right text-kp-blue-700 font-bold tabular-nums">{{ number_format($totalKonsinyasi, 0, ',', '.') }}</td>
                            <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalGratis, 0, ',', '.') }}</td>
                            <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalInsidentil, 0, ',', '.') }}</td>
                            <td class="py-3 px-3.5 text-right font-bold tabular-nums">{{ number_format($grandTotalEks, 0, ',', '.') }}</td>
                            <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                    <span class="font-bold text-kp-blue-700 tabular-nums">{{ number_format($grandTotalOmset, 0, ',', '.') }}</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="px-4 sm:px-5 py-3 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2 print:hidden">
                <span>Rekapitulasi oplah dasar Surat Perintah Cetak Percetakan (SPCP).</span>
                <button type="button" onclick="window.print()" class="h-9 px-3.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer inline-flex items-center">
                    <span>Cetak Lembar Kalkulasi</span>
                </button>
            </div>
        </div>

        <!-- Tanda Tangan & Pengesahan Dokumen Hitung -->
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
