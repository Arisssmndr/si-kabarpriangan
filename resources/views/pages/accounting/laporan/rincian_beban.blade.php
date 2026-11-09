<x-app-layout>
    <div class="space-y-6">

        <!-- Top Action & Filter Bar (Print Hidden) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Rincian Beban Operasional
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-sky-50 text-kp-blue-700 border border-kp-blue-200">
                            Analisis Biaya
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Daftar rincian realisasi seluruh beban kas kecil dan operasional per pos.
                    </p>
                </div>
            </div>

            <!-- Filter Periode & Cetak Button -->
            <div class="flex items-center gap-2.5 shrink-0">
                <form method="GET" action="{{ route('accounting.laporan.rincian-beban') }}" class="flex items-center gap-2">
                    <input type="month" name="periode" value="{{ $periode }}" class="h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 transition" />
                    <button type="submit" class="h-9 px-3.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition cursor-pointer">
                        Filter
                    </button>
                </form>

                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 h-9 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Resmi</span>
                </button>
            </div>
        </div>

        <!-- Dokumen Resmi Rincian Beban -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs text-slate-800 print:border-none print:shadow-none print:p-0 max-w-5xl mx-auto">
            
            <!-- Kop Dokumen Resmi Kabar Priangan -->
            <div class="border-b-2 border-slate-900 pb-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-black tracking-wider uppercase text-slate-900">PT Berkah Pikiran Rakyat</h2>
                        <h3 class="text-base font-bold text-[#0E527B] tracking-wide uppercase">Harian Umum Kabar Priangan</h3>
                        <p class="text-[11px] text-slate-500">Jl. Dr. Sukardjo No. 47, Tawangsari, Kec. Tawang, Kota Tasikmalaya, Jawa Barat 46112</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-black uppercase text-slate-900">
                            RINCIAN BEBAN OPERASIONAL PER KASIR
                        </div>
                        <div class="text-xs font-semibold text-[#0E527B] mt-0.5">
                            Periode: {{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}
                        </div>
                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                            Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Transaksi Beban -->
            <div class="mb-8 overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-300 text-slate-700 uppercase font-semibold text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3 text-center w-10">No</th>
                            <th class="py-2.5 px-3 text-left">No. BKK</th>
                            <th class="py-2.5 px-3 text-left">Tanggal</th>
                            <th class="py-2.5 px-3 text-left">Diberikan Kepada</th>
                            <th class="py-2.5 px-3 text-left">Pos Akun</th>
                            <th class="py-2.5 px-3 text-left">Keterangan</th>
                            <th class="py-2.5 px-3 text-right">Nominal Realisasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($daftarBeban as $key => $b)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2 px-3 text-center text-slate-400">{{ $key + 1 }}</td>
                                <td class="py-2 px-3 font-bold text-slate-900 font-mono">{{ $b->no_bkk ?: ('BKK-' . $b->id) }}</td>
                                <td class="py-2 px-3">{{ \Carbon\Carbon::parse($b->tanggal_keluar)->format('d/m/Y') }}</td>
                                <td class="py-2 px-3">{{ $b->diberikan_kepada ?: ($b->nama_penerima ?: '-') }}</td>
                                <td class="py-2 px-3 font-mono text-[11px]">{{ $b->kode_perkiraan ?: '6.00.01' }}</td>
                                <td class="py-2 px-3 max-w-xs truncate">{{ $b->keterangan }}</td>
                                <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($b->nominal_keluar ?: $b->jumlah_keluar ?: 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    Tidak ada data beban operasional pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-sky-50/70 border-t-2 border-slate-300 font-bold">
                            <td colspan="6" class="py-2.5 px-3 text-right uppercase">
                                TOTAL REALISASI BEBAN
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-900 text-xs font-bold">
                                Rp {{ number_format($totalBeban, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Lembar Tanda Tangan Resmi (Standar Kabar Priangan) -->
            <div class="pt-8 border-t-2 border-slate-800 mt-8">
                <div class="grid grid-cols-3 gap-6 text-center text-xs">
                    <div>
                        <div class="text-slate-500 font-medium">Dibuat Oleh:</div>
                        <div class="text-slate-700 font-semibold mt-1">Staf Akuntansi</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">{{ auth()->user()->name ?? 'Staf Akuntansi' }}</div>
                        <div class="text-[10px] text-slate-400">Accounting Officer</div>
                    </div>

                    <div>
                        <div class="text-slate-500 font-medium">Diperiksa Oleh:</div>
                        <div class="text-slate-700 font-semibold mt-1">Bagian Keuangan & Pajak</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">( .................................................. )</div>
                        <div class="text-[10px] text-slate-400">Kabag Akuntansi & Pajak</div>
                    </div>

                    <div>
                        <div class="text-slate-500 font-medium">Disetujui Oleh:</div>
                        <div class="text-slate-700 font-semibold mt-1">Pemimpin Perusahaan</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">( .................................................. )</div>
                        <div class="text-[10px] text-slate-400">Manager Keuangan</div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>

