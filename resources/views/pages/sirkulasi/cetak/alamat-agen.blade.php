<x-app-layout>
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 8mm 10mm;
            }

            header,
            footer,
            nav,
            aside,
            .print\:hidden,
            .no-print {
                display: none !important;
            }

            body, html, main, .min-h-screen {
                background: #ffffff !important;
                background-color: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                min-height: auto !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .printable-area {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .overflow-x-auto {
                overflow: visible !important;
                display: block !important;
                width: 100% !important;
            }

            table {
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                table-layout: auto !important;
                border-collapse: collapse !important;
            }

            tr {
                page-break-inside: avoid;
            }

            th, td {
                padding: 5px 6px !important;
                font-size: 10px !important;
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
            }
        }
    </style>

    <div class="space-y-6">

        <!-- Header Halaman -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Menu Cetak</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Nama & Alamat Agen (Direktori Distribusi)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Cetak buku direktori alamat agen dan sub agen per wilayah atau jurusan armada distribusi
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200/80">
                    Format: A4 Landscape
                </span>
                <button type="button" onclick="window.print()" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-amber-700 hover:bg-amber-800 text-white shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Direktori Agen</span>
                </button>
            </div>
        </div>

        <!-- Filter Direktori -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.cetak.alamat-agen') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Filter Wilayah</label>
                    <select name="wilayah" class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                        <option value="all">Semua Wilayah (01 s.d. 08)</option>
                        @foreach($wilayahList as $w)
                            <option value="{{ $w->kode }}" {{ ($wilayahPilihan ?? '') === $w->kode ? 'selected' : '' }}>
                                {{ $w->kode }} - {{ $w->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Filter Rute Distribusi</label>
                    <select name="jurusan" class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                        <option value="all">Semua Rute (01 s.d. 15)</option>
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->kode }}" {{ ($jurusanPilihan ?? '') === $jur->kode ? 'selected' : '' }}>
                                {{ $jur->kode }} - {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Jenis Agen</label>
                    <select name="kategori" class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                        <option value="all">Semua Jenis (11, 99, 00)</option>
                        <option value="11" {{ ($kategoriPilihan ?? '') === '11' ? 'selected' : '' }}>11 - Agen Besar</option>
                        <option value="99" {{ ($kategoriPilihan ?? '') === '99' ? 'selected' : '' }}>99 - Sub Agen</option>
                        <option value="00" {{ ($kategoriPilihan ?? '') === '00' ? 'selected' : '' }}>00 - Non Aktif (Arsip)</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="w-full py-1.5 px-3 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                        Filter Direktori
                    </button>
                </div>
            </form>
        </div>

        <!-- Direktori Cetak -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs p-6 printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
            
            <div class="border-b-2 border-slate-900 pb-4 mb-4 text-center">
                <h2 class="text-base font-extrabold uppercase tracking-tight text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                <h3 class="text-xs font-bold uppercase text-slate-700 mt-0.5">BUKU DIREKTORI NAMA & ALAMAT AGEN SIRKULASI</h3>
                <p class="text-[11px] text-slate-500 mt-1">
                    Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y - H:i') }} WIB
                    @if(($wilayahPilihan ?? 'all') !== 'all')
                        &bull; Wilayah: <span class="font-bold text-slate-700">{{ $wilayahList->firstWhere('kode', $wilayahPilihan)?->nama ?? $wilayahPilihan }}</span>
                    @endif
                    @if(($jurusanPilihan ?? 'all') !== 'all')
                        &bull; Rute: <span class="font-bold text-slate-700">{{ $jurusanList->firstWhere('kode', $jurusanPilihan)?->nama_jurusan ?? $jurusanPilihan }}</span>
                    @endif
                    @if(($kategoriPilihan ?? 'all') !== 'all')
                        &bull; Jenis: <span class="font-bold text-slate-700">{{ $kategoriPilihan == '11' ? 'Agen Besar' : ($kategoriPilihan == '99' ? 'Sub Agen' : 'Non Aktif') }}</span>
                    @endif
                </p>
            </div>

            <div class="overflow-x-auto print:overflow-visible">
                <table class="w-full text-left text-xs text-slate-700 min-w-[1050px] print:min-w-0 print:w-full">
                    <thead class="border-y-2 border-slate-300 font-bold uppercase text-[10px] tracking-wider bg-slate-50/50 print:bg-slate-100">
                        <tr>
                            <th class="py-3 px-3 w-12 text-center whitespace-nowrap print:w-8 print:py-2 print:px-1.5 print:text-[10px]">No</th>
                            <th class="py-3 px-3.5 whitespace-nowrap w-32 print:w-24 print:py-2 print:px-2 print:text-[10px]">Kode Agen</th>
                            <th class="py-3 px-4 min-w-[180px] print:min-w-0 print:py-2 print:px-2 print:text-[10px]">Nama Pemilik / Kios Koran</th>
                            <th class="py-3 px-4 min-w-[220px] print:min-w-0 print:py-2 print:px-2 print:text-[10px]">Alamat Lengkap Kios / Drop Point</th>
                            <th class="py-3 px-3 text-center whitespace-nowrap w-20 print:w-14 print:py-2 print:px-1.5 print:text-[10px]">Jenis</th>
                            <th class="py-3 px-3 whitespace-nowrap w-28 print:w-24 print:py-2 print:px-2 print:text-[10px]">Wilayah</th>
                            <th class="py-3 px-3 whitespace-nowrap w-28 print:w-24 print:py-2 print:px-2 print:text-[10px]">Rute Distribusi</th>
                            <th class="py-3 px-3.5 whitespace-nowrap w-36 print:w-28 print:py-2 print:px-2 print:text-[10px]">Kontak / Telp</th>
                            <th class="py-3 px-4 text-right whitespace-nowrap w-28 print:w-20 print:py-2 print:px-2 print:text-[10px]">Jatah (Eks)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-medium">
                        @forelse($agenList as $idx => $agen)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-3 text-center text-slate-400 font-mono text-xs whitespace-nowrap print:py-1.5 print:px-1.5 print:text-[10px] print:text-slate-600">{{ $idx + 1 }}</td>
                                <td class="py-3 px-3.5 whitespace-nowrap print:py-1.5 print:px-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md font-mono font-bold text-slate-800 bg-slate-100 text-xs border border-slate-200/90 tracking-wide whitespace-nowrap print:px-1.5 print:py-0.5 print:text-[10px] print:border-slate-300 print:bg-slate-50">
                                        {{ $agen->kode_agen }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900 text-xs print:py-1.5 print:px-2 print:text-[10px] print:leading-tight">
                                    {{ $agen->nama_agen }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 text-xs leading-relaxed print:py-1.5 print:px-2 print:text-[9.5px] print:leading-snug print:text-slate-800">
                                    {{ $agen->alamat }}
                                </td>
                                <td class="py-3 px-3 text-center whitespace-nowrap print:py-1.5 print:px-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $agen->kategori === '11' ? 'bg-blue-100 text-blue-800' : ($agen->kategori === '99' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800') }} print:px-1.5 print:py-0 print:border print:border-slate-300 print:text-[9.5px]">
                                        {{ $agen->kategori }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-xs text-slate-700 whitespace-nowrap print:py-1.5 print:px-2 print:text-[10px]">
                                    {{ $agen->wilayah_nama }}
                                </td>
                                <td class="py-3 px-3 text-xs text-slate-600 whitespace-nowrap font-medium print:py-1.5 print:px-2 print:text-[10px]">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] print:px-1.5 print:py-0.5 print:text-[10px] print:bg-slate-50 print:border print:border-slate-300 print:font-semibold">
                                        Rute {{ $agen->jurusan_kode }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 font-mono text-xs text-slate-700 whitespace-nowrap tabular-nums print:py-1.5 print:px-2 print:text-[10px]">
                                    <div class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 print:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <span>{{ $agen->telepon }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap font-mono font-bold text-slate-900 text-xs tabular-nums print:py-1.5 print:px-2 print:text-[10.5px]">
                                    {{ number_format($agen->jatah_default, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-6 text-center text-slate-400">
                                    Tidak ada data agen yang cocok dengan filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($agenList->isNotEmpty())
                        <tfoot class="bg-slate-50 font-bold text-slate-800 border-t-2 border-slate-300 print:bg-slate-100">
                            <tr>
                                <td colspan="8" class="py-2.5 px-4 text-right uppercase text-[11px] whitespace-nowrap print:py-1.5 print:px-2 print:text-[10px]">Total Jatah:</td>
                                <td class="py-2.5 px-4 text-right font-mono font-extrabold text-slate-900 text-xs whitespace-nowrap tabular-nums print:py-1.5 print:px-2 print:text-[10.5px]">
                                    {{ number_format($agenList->sum('jatah_default'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <div class="mt-8 pt-4 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500 print:mt-4 print:pt-2">
                <span>Total Agen Terdata: <strong>{{ $agenList->count() }} Mitra Distribusi</strong></span>
                <span>Divisi Sirkulasi Kabar Priangan &bull; Sistem DOS SIRK.EXE</span>
            </div>

        </div>

    </div>
</x-app-layout>
