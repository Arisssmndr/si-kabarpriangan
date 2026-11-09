<x-app-layout>
    <style>
        @media print {
            @page {
                size: A4 portrait;
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
                        Nota &amp; Distribusi Armada Subuh
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Surat jalan pengiriman armada, daftar pengepakan jurusan, dan nota kiriman agen.
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
                    <span>Cetak Surat Jalan</span>
                </button>
            </div>
        </div>

        <!-- Toolbar Filter Nota -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.cetak.nota-distribusi') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Format Dokumen</label>
                    <select name="jenis" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="distribusi-jurusan" {{ ($jenisNota ?? '') === 'distribusi-jurusan' ? 'selected' : '' }}>
                            1. Distribusi per Jurusan (Surat Jalan Subuh)
                        </option>
                        <option value="nota-jurusan" {{ ($jenisNota ?? '') === 'nota-jurusan' ? 'selected' : '' }}>
                            2. Nota per Jurusan
                        </option>
                        <option value="nota-agen" {{ ($jenisNota ?? '') === 'nota-agen' ? 'selected' : '' }}>
                            3. Nota per Agen / Susulan
                        </option>
                    </select>
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jurusan Armada</label>
                    <select name="jurusan" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->kode }}" {{ ($activeJurusan->kode ?? '') === $jur->kode ? 'selected' : '' }}>
                                {{ $jur->kode }} - {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Edisi</label>
                    <input type="date"
                           name="tanggal"
                           value="{{ $tanggalDistribusi }}"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>

        <!-- KPI Ringkas Muatan Armada (Layar) -->
        <div class="print:hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Muatan Rute</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    {{ number_format($totalEksemplar, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">{{ $packingItems->count() }} titik agen serah</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kemasan Pengepakan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ $totalBendel }} <span class="text-xs font-sans font-normal text-slate-400">Bendel + {{ $totalEceran }} ecer</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Standar 50 eks per bendel</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Armada &amp; Sopir</span>
                <p class="text-base font-bold text-slate-900 mt-1.5 truncate">
                    {{ $activeJurusan->armada }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Kru: {{ $activeJurusan->sopir }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jadwal Berangkat</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ $activeJurusan->jam_berangkat }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">{{ $activeJurusan->kode }} - {{ $activeJurusan->nama_jurusan }}</span>
            </div>
        </div>

        <!-- Dokumen Siap Cetak (Printable Sheet) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">

            <!-- Kop Surat Jalan (Tampil Saat Cetak) -->
            <div class="hidden print:block border-b-2 border-slate-900 pb-4 mb-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-base font-black tracking-tight text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                        <h3 class="text-xs font-bold text-slate-700 uppercase mt-0.5">
                            @if(($jenisNota ?? '') === 'nota-jurusan')
                                NOTA PENGIRIMAN PER JURUSAN ARMADA
                            @elseif(($jenisNota ?? '') === 'nota-agen')
                                NOTA PER AGEN / KIRIMAN SUSULAN
                            @else
                                SURAT JALAN &amp; DAFTAR PENGEPAKAN (PACKING LIST) ARMADA SUBUH
                            @endif
                        </h3>
                    </div>
                    <div class="text-right text-xs">
                        <div class="font-mono font-bold text-slate-900 text-sm">NO: SJ/KP/{{ str_replace('-', '', $tanggalDistribusi) }}/{{ $activeJurusan->kode }}</div>
                        <div class="text-slate-500 text-[11px] mt-0.5">Edisi: {{ \Carbon\Carbon::parse($tanggalDistribusi)->translatedFormat('l, d F Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Detail Armada & Rute (Tampil Saat Cetak) -->
            <div class="hidden print:grid grid-cols-4 gap-3 p-3 bg-slate-50 rounded-lg border border-slate-300 text-xs mb-4">
                <div>
                    <span class="text-slate-500 text-[10px]">Jurusan / Jalur:</span>
                    <div class="font-bold text-slate-900 mt-0.5">{{ $activeJurusan->kode }} - {{ $activeJurusan->nama_jurusan }}</div>
                </div>
                <div>
                    <span class="text-slate-500 text-[10px]">Kendaraan Armada:</span>
                    <div class="font-bold text-slate-900 mt-0.5">{{ $activeJurusan->armada }}</div>
                </div>
                <div>
                    <span class="text-slate-500 text-[10px]">Sopir / Kru Subuh:</span>
                    <div class="font-bold text-slate-900 mt-0.5">{{ $activeJurusan->sopir }}</div>
                </div>
                <div>
                    <span class="text-slate-500 text-[10px]">Jadwal Berangkat:</span>
                    <div class="font-bold text-slate-900 font-mono mt-0.5">{{ $activeJurusan->jam_berangkat }}</div>
                </div>
            </div>

            <!-- Sub-Header Card Tabel (Layar) -->
            <div class="print:hidden px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">
                        @if(($jenisNota ?? '') === 'nota-jurusan')
                            Nota Pengiriman per Jurusan Armada
                        @elseif(($jenisNota ?? '') === 'nota-agen')
                            Nota per Agen / Kiriman Susulan
                        @else
                            Surat Jalan &amp; Daftar Pengepakan Armada Subuh
                        @endif
                    </h2>
                    <p class="text-xs text-slate-500 font-mono">
                        SJ/KP/{{ str_replace('-', '', $tanggalDistribusi) }}/{{ $activeJurusan->kode }} &bull; {{ \Carbon\Carbon::parse($tanggalDistribusi)->translatedFormat('d F Y') }}
                    </p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ $activeJurusan->kode }} - {{ $activeJurusan->nama_jurusan }}
                </span>
            </div>

            <!-- Tabel Daftar Packing per Agen -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">Kode Agen</th>
                            <th class="py-3 px-3.5">Nama Agen &amp; Drop Point</th>
                            <th class="py-3 px-3.5">Alamat Kios / Titik Serah</th>
                            <th class="py-3 px-3.5 text-center">Jenis</th>
                            <th class="py-3 px-3.5 text-right">Jatah Total</th>
                            <th class="py-3 px-3.5">Rincian Pengepakan (Bendel 50an)</th>
                            <th class="py-3 px-3.5 text-center">Paraf Penerima</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($packingItems as $idx => $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm whitespace-nowrap print:text-slate-900">
                                    {{ $item->kode_agen }}
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <div class="font-semibold text-slate-900">{{ $item->nama_agen }}</div>
                                    <div class="text-xs text-slate-500 font-mono">{{ $item->telepon }}</div>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 text-xs max-w-xs">{{ $item->alamat }}</td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $item->kategori === '11' ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                        {{ $item->kategori }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 whitespace-nowrap tabular-nums">
                                    {{ $item->total_eks }} eks
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-700 whitespace-nowrap">
                                    <span class="font-semibold text-kp-blue-700 print:text-slate-900">{{ $item->bendel_50 }} Bendel ({{ $item->bendel_50 * 50 }} eks)</span>
                                    @if($item->sisa_ecer > 0)
                                        + <span class="font-semibold text-slate-700">{{ $item->sisa_ecer }} eks lepas</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <div class="w-20 border-b border-dotted border-slate-400 mx-auto h-5"></div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada titik agen aktif pada jurusan armada ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 print:border-slate-900">
                        <tr>
                            <td colspan="5" class="py-3 px-3.5 text-right font-sans text-xs uppercase tracking-wider text-slate-600">Total Muatan Rute Ini:</td>
                            <td class="py-3 px-3.5 text-right font-bold text-kp-blue-700 whitespace-nowrap tabular-nums print:text-slate-900">
                                {{ number_format($totalEksemplar, 0, ',', '.') }} eks
                            </td>
                            <td class="py-3 px-3.5 text-xs whitespace-nowrap" colspan="2">
                                = <strong class="text-slate-900">{{ $totalBendel }} Bendel</strong> + <strong class="text-slate-700">{{ $totalEceran }} eks lepas</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Tanda Tangan Serah Terima Ekspedisi -->
            <div class="p-6 border-t border-slate-200 grid grid-cols-3 text-center text-xs text-slate-800 print:px-0 print:mt-6 print:pt-4">
                <div>
                    <p class="text-slate-500">Diserahkan Oleh,</p>
                    <p class="font-bold text-slate-900 mt-1">Petugas Pengepakan Gudang</p>
                    <div class="h-14"></div>
                    <p class="font-bold underline text-slate-900">( ......................................... )</p>
                </div>
                <div>
                    <p class="text-slate-500">Dibawa Oleh,</p>
                    <p class="font-bold text-slate-900 mt-1">Sopir / Ekspedisi Armada</p>
                    <div class="h-14"></div>
                    <p class="font-bold underline text-slate-900">( {{ $activeJurusan->sopir }} )</p>
                </div>
                <div>
                    <p class="text-slate-500">Mengetahui,</p>
                    <p class="font-bold text-slate-900 mt-1">Koordinator Sirkulasi</p>
                    <div class="h-14"></div>
                    <p class="font-bold underline text-slate-900">( ......................................... )</p>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
