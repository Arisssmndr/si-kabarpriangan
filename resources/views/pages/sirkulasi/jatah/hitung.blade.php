<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman (Hanya Tampil di Layar) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Operasional Jatah</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Hitung Data Penjualan (Kalkulasi Akumulasi)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Kalkulasi akumulasi eksemplar cetak, rincian langganan, konsinyasi agen Rp3.000, jatah gratis, dan estimasi penerimaan omset
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('sirkulasi.jatah.hitung') }}" class="flex items-center gap-2">
                    <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" class="text-xs rounded-lg border-slate-200 py-1.5 focus:border-emerald-500 focus:ring-emerald-500">
                    <button type="submit" class="py-1.5 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition-colors cursor-pointer">
                        Filter Edisi
                    </button>
                </form>

                <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                    Format: A4 Landscape
                </span>

                <button type="button" onclick="window.print()" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Hasil Hitung</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="print:hidden p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-medium text-emerald-800 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="window.print()" class="px-3 py-1 text-xs font-bold rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white transition-colors inline-flex items-center gap-1 cursor-pointer">
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
                    <p class="text-[11px] font-bold text-slate-700">DIVISI SIRKULASI & DISTRIBUSI — SISTEM OPERASIONAL SIRK.EXE</p>
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
                    <h1 class="text-sm font-black text-slate-900 uppercase tracking-tight">REKAPITULASI KALKULASI & HITUNG DATA PENJUALAN PER JURUSAN</h1>
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

        <!-- Ringkasan Akumulasi Card Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 print:grid print:grid-cols-4 print:gap-2 print:mb-3">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider print:text-slate-600">Total Oplah Cetak</span>
                <div class="text-xl font-extrabold text-slate-900 mt-1 print:text-sm">{{ number_format($grandTotalEks, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">eks</span></div>
                <div class="text-[11px] text-slate-400 mt-0.5 print:text-[9.5px]">Target Produksi Subuh</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                <span class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider print:text-emerald-800">Konsinyasi Agen</span>
                <div class="text-xl font-extrabold text-emerald-700 mt-1 print:text-sm print:text-slate-900">{{ number_format($totalKonsinyasi, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">eks</span></div>
                <div class="text-[11px] text-slate-400 mt-0.5 print:text-[9.5px]">Tarif Komersil @ Rp3.000</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none">
                <span class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider print:text-blue-800">Pelanggan Langganan</span>
                <div class="text-xl font-extrabold text-blue-700 mt-1 print:text-sm print:text-slate-900">{{ number_format($totalLangganan, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">eks</span></div>
                <div class="text-[11px] text-slate-400 mt-0.5 print:text-[9.5px]">Instansi, Pemda & Warga</div>
            </div>

            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300 print:rounded-none print:bg-slate-50">
                <span class="text-[11px] font-semibold text-emerald-800 uppercase tracking-wider print:text-slate-800">Estimasi Nilai Omset</span>
                <div class="text-xl font-extrabold text-emerald-900 mt-1 print:text-sm print:text-slate-900">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</div>
                <div class="text-[11px] text-emerald-700 mt-0.5 print:text-[9.5px]">Tagihan Berjalan Agen</div>
            </div>
        </div>

        <!-- Tabel Akumulasi per 15 Jurusan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 print:px-0 print:py-2 print:bg-transparent print:border-b-2 print:border-slate-800">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 print:text-slate-900">Rincian Kalkulasi Jatah Penjualan per Jurusan</h3>
                    <span class="text-[11px] text-slate-500 print:text-slate-700">Edisi: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($tanggalPilihan)->translatedFormat('l, d F Y') }}</strong> &bull; Total Armada: 15 Rute Mobil Subuh</span>
                </div>
                <div class="flex items-center gap-2 print:hidden">
                    <button type="button" onclick="window.print()" class="px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        <span>Cetak Dokumen</span>
                    </button>
                    <form method="POST" action="{{ route('sirkulasi.jatah.hitung.process') }}" class="inline">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                        <button type="submit" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            <span>Kalkulasi Ulang</span>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 min-w-[950px] print:min-w-0 print:w-full print:text-[10px]">
                    <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200 print:bg-slate-100 print:text-slate-900 print:text-[9.5px]">
                        <tr>
                            <th class="py-3 px-3 w-16 text-center print:py-1.5 print:px-2">Rute</th>
                            <th class="py-3 px-4 print:py-1.5 print:px-2">Nama Jurusan Armada</th>
                            <th class="py-3 px-3 text-right whitespace-nowrap print:py-1.5 print:px-2">Langganan</th>
                            <th class="py-3 px-3 text-right text-emerald-800 whitespace-nowrap print:text-slate-900 print:py-1.5 print:px-2">Konsinyasi (Rp3.000)</th>
                            <th class="py-3 px-3 text-right whitespace-nowrap print:py-1.5 print:px-2">Gratis</th>
                            <th class="py-3 px-3 text-right whitespace-nowrap print:py-1.5 print:px-2">Insidentil</th>
                            <th class="py-3 px-4 text-right font-extrabold text-slate-900 whitespace-nowrap print:py-1.5 print:px-2">Total Eksemplar</th>
                            <th class="py-3 px-4 text-right whitespace-nowrap print:py-1.5 print:px-2">Omset Konsinyasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium print:divide-slate-200">
                        @foreach($rekapKalkulasi as $rk)
                            <tr class="hover:bg-slate-50/80 transition-colors print:hover:bg-transparent">
                                <td class="py-3 px-3 text-center print:py-1.5 print:px-2">
                                    <span class="font-mono font-bold text-xs px-2 py-0.5 rounded bg-slate-100 text-slate-800 border border-slate-200 print:border-none print:bg-transparent print:p-0 print:text-[10px]">
                                        {{ $rk->kode }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 print:py-1.5 print:px-2">
                                    <span class="font-bold text-slate-900 text-xs print:text-[10.5px]">{{ $rk->nama_jurusan }}</span>
                                    <span class="hidden print:inline-block text-[9.5px] text-slate-500 ml-1">({{ $rk->armada }})</span>
                                </td>
                                <td class="py-3 px-3 text-right font-semibold text-slate-800 font-mono tabular-nums print:py-1.5 print:px-2">
                                    {{ number_format($rk->langganan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-emerald-700 font-mono tabular-nums print:text-slate-900 print:py-1.5 print:px-2">
                                    {{ number_format($rk->konsinyasi, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right text-slate-600 font-mono tabular-nums print:py-1.5 print:px-2">
                                    {{ number_format($rk->gratis, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right text-slate-600 font-mono tabular-nums print:py-1.5 print:px-2">
                                    {{ number_format($rk->insidentil, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-extrabold text-slate-900 text-xs font-mono tabular-nums print:py-1.5 print:px-2 print:text-[10.5px]">
                                    {{ number_format($rk->total_eks, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">eks</span>
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-emerald-700 font-mono tabular-nums print:text-slate-900 print:py-1.5 print:px-2 print:text-[10px]">
                                    Rp {{ number_format($rk->omset_konsinyasi, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-100 font-bold text-slate-800 border-t-2 border-slate-200 print:bg-slate-100 print:text-slate-900 print:border-t-2 print:border-slate-800">
                        <tr>
                            <td colspan="2" class="py-3 px-4 text-right uppercase text-[11px] whitespace-nowrap print:py-2 print:px-2 print:text-[10px]">Total Akumulasi:</td>
                            <td class="py-3 px-3 text-right font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalLangganan, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right text-emerald-800 font-mono tabular-nums print:text-slate-900 print:py-2 print:px-2">{{ number_format($totalKonsinyasi, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalGratis, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono tabular-nums print:py-2 print:px-2">{{ number_format($totalInsidentil, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-slate-900 text-sm font-black font-mono tabular-nums print:py-2 print:px-2 print:text-xs">{{ number_format($grandTotalEks, 0, ',', '.') }} eks</td>
                            <td class="py-3 px-4 text-right text-emerald-800 text-sm font-extrabold font-mono tabular-nums print:text-slate-900 print:py-2 print:px-2 print:text-xs">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="p-4 bg-slate-50/60 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2 print:hidden">
                <span>Perhitungan akumulasi oplah digunakan sebagai basis Surat Perintah Cetak Percetakan (SPCP) Subuh.</span>
                <button type="button" onclick="window.print()" class="px-4 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Lembar Kalkulasi</span>
                </button>
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
