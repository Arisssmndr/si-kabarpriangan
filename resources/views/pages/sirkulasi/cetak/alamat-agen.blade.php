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

            table > thead,
            table > thead tr,
            table > thead th {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
                border-bottom: 1.5px solid #334155 !important;
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

    <div class="space-y-5">

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Nama &amp; Alamat Agen
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Direktori alamat agen dan sub-agen per wilayah serta jurusan armada distribusi.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <button type="button"
                        onclick="window.print()"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Direktori Agen</span>
                </button>
            </div>
        </div>

        <!-- Toolbar Filter Direktori -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.cetak.alamat-agen') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Wilayah</label>
                    <select name="wilayah" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Wilayah (01 s.d. 08)</option>
                        @foreach($wilayahList as $w)
                            <option value="{{ $w->kode }}" {{ ($wilayahPilihan ?? '') === $w->kode ? 'selected' : '' }}>
                                {{ $w->kode }} - {{ $w->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Rute Distribusi</label>
                    <select name="jurusan" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Rute (01 s.d. 15)</option>
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->kode }}" {{ ($jurusanPilihan ?? '') === $jur->kode ? 'selected' : '' }}>
                                {{ $jur->kode }} - {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Agen</label>
                    <select name="kategori" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Jenis</option>
                        <option value="11" {{ ($kategoriPilihan ?? '') === '11' ? 'selected' : '' }}>11 - Agen Besar</option>
                        <option value="99" {{ ($kategoriPilihan ?? '') === '99' ? 'selected' : '' }}>99 - Sub Agen</option>
                        <option value="00" {{ ($kategoriPilihan ?? '') === '00' ? 'selected' : '' }}>00 - Non Aktif</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Kartu KPI Ringkas (Layar) -->
        <div class="print:hidden grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Mitra Agen</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    {{ number_format($agenList->count(), 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Agen</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Sesuai filter direktori aktif</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Jatah Default</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($agenList->sum('jatah_default'), 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Alokasi harian terdaftar</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Komposisi Jaringan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ $agenList->where('kategori', '11')->count() }} <span class="text-xs font-sans font-normal text-slate-400">Agen Besar &bull; {{ $agenList->where('kategori', '99')->count() }} Sub Agen</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Format cetak A4 Landscape</span>
            </div>
        </div>

        <!-- Direktori Tabel & Lembar Cetak -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">

            <!-- Kop Cetak (Hanya Tampil Saat Print) -->
            <div class="hidden print:block border-b-2 border-slate-900 pb-4 mb-4 text-center">
                <h2 class="text-base font-extrabold uppercase tracking-tight text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                <h3 class="text-xs font-bold uppercase text-slate-700 mt-0.5">BUKU DIREKTORI NAMA &amp; ALAMAT AGEN SIRKULASI</h3>
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

            <!-- Sub-Header Card Tabel (Layar) -->
            <div class="print:hidden px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Buku Direktori Nama &amp; Alamat Agen</h2>
                    <p class="text-xs text-slate-500">Daftar titik serah koran harian Divisi Sirkulasi</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ $agenList->count() }} Mitra ({{ number_format($agenList->sum('jatah_default'), 0, ',', '.') }} eks)
                </span>
            </div>

            <div class="overflow-x-auto print:overflow-visible">
                <table class="w-full text-left text-sm border-collapse min-w-[1050px] print:min-w-0 print:w-full">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap print:bg-slate-100">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12 print:w-8 print:py-2 print:px-1.5 print:text-[10px]">No</th>
                            <th class="py-3 px-3.5 w-32 print:w-24 print:py-2 print:px-2 print:text-[10px]">Kode Agen</th>
                            <th class="py-3 px-3.5 min-w-[180px] print:min-w-0 print:py-2 print:px-2 print:text-[10px]">Nama Pemilik / Kios</th>
                            <th class="py-3 px-3.5 min-w-[220px] print:min-w-0 print:py-2 print:px-2 print:text-[10px]">Alamat Drop Point</th>
                            <th class="py-3 px-3.5 text-center w-24 print:w-14 print:py-2 print:px-1.5 print:text-[10px]">Jenis</th>
                            <th class="py-3 px-3.5 w-28 print:w-24 print:py-2 print:px-2 print:text-[10px]">Wilayah</th>
                            <th class="py-3 px-3.5 w-28 print:w-24 print:py-2 print:px-2 print:text-[10px]">Rute</th>
                            <th class="py-3 px-3.5 w-36 print:w-28 print:py-2 print:px-2 print:text-[10px]">Kontak / Telp</th>
                            <th class="py-3 px-3.5 text-right w-28 print:w-20 print:py-2 print:px-2 print:text-[10px]">Jatah (Eks)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($agenList as $idx => $agen)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal whitespace-nowrap print:py-1.5 print:px-1.5 print:text-[10px] print:text-slate-700">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm whitespace-nowrap print:py-1.5 print:px-2 print:text-[10px] print:text-slate-900">
                                    {{ $agen->kode_agen }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900 print:py-1.5 print:px-2 print:text-[10px] print:leading-tight">
                                    {{ $agen->nama_agen }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 text-xs leading-relaxed print:py-1.5 print:px-2 print:text-[9.5px] print:leading-snug print:text-slate-800">
                                    {{ $agen->alamat }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap print:py-1.5 print:px-1.5">
                                    @php
                                        $jenisBadge = match($agen->kategori) {
                                            '11' => 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200',
                                            '99' => 'bg-slate-100 text-slate-700 border-slate-200',
                                            default => 'bg-slate-100 text-slate-400 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $jenisBadge }} print:px-1.5 print:py-0 print:border-slate-300 print:text-[9.5px] print:text-slate-900 print:bg-slate-50">
                                        {{ $agen->kategori }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-700 whitespace-nowrap print:py-1.5 print:px-2 print:text-[10px]">
                                    {{ $agen->wilayah_nama }}
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap print:py-1.5 print:px-2 print:text-[10px]">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 print:px-1.5 print:py-0.5 print:text-[10px] print:bg-slate-50 print:border-slate-300">
                                        Rute {{ $agen->jurusan_kode }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-600 whitespace-nowrap tabular-nums print:py-1.5 print:px-2 print:text-[10px]">
                                    {{ $agen->telepon }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right whitespace-nowrap font-mono font-semibold text-slate-900 tabular-nums print:py-1.5 print:px-2 print:text-[10.5px]">
                                    {{ number_format($agen->jatah_default, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-500 font-normal">
                                    Tidak ada data agen yang sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($agenList->isNotEmpty())
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 print:bg-slate-100">
                            <tr>
                                <td colspan="8" class="py-3 px-3.5 text-right font-sans text-xs uppercase tracking-wider text-slate-600 whitespace-nowrap print:py-1.5 print:px-2 print:text-[10px]">Total Jatah:</td>
                                <td class="py-3 px-3.5 text-right font-bold text-kp-blue-700 whitespace-nowrap tabular-nums print:py-1.5 print:px-2 print:text-[10.5px] print:text-slate-900">
                                    {{ number_format($agenList->sum('jatah_default'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <div class="px-5 py-3.5 bg-slate-50/60 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500 print:mt-4 print:pt-2 print:px-0 print:bg-transparent">
                <span>Total Agen Terdata: <strong class="text-slate-800">{{ $agenList->count() }} Mitra Distribusi</strong></span>
                <span>Harian Umum Kabar Priangan &bull; Divisi Sirkulasi</span>
            </div>

        </div>

    </div>
</x-app-layout>
