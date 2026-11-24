<x-app-layout>
    <style>
        .nota-slip {
            font-family: Consolas, "Courier New", Courier, Monaco, "Liberation Mono", monospace;
            color: #000000;
            background: #ffffff;
        }

        @media print {
            @page {
                size: letter portrait; /* Standar Continuous Form Letter 8.5" x 11" (215.9mm x 279.4mm) */
                margin: 0 !important;
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
                width: 215.9mm !important;
                max-width: 215.9mm !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .nota-sheet-wrapper {
                display: block !important;
                width: 215.9mm !important;
                max-width: 215.9mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Container 1 Halaman Continuous Form (8.5 x 11 inch / 215.9 x 279.4 mm) memuat pas 6 Nota (2 Kolom x 3 Baris) */
            /* Padding atas 8.5mm wajib untuk menghindari zona buta mekanik (unprintable top margin 8.5-12mm) printer Dot Matrix */
            .nota-continuous-page,
            .nota-a4-page {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                grid-template-rows: repeat(3, minmax(0, 1fr)) !important;
                gap: 2mm !important;
                width: 215.9mm !important;
                max-width: 215.9mm !important;
                height: 271mm !important;
                max-height: 271mm !important;
                padding: 8.5mm 4.5mm 3.5mm 4.5mm !important;
                box-sizing: border-box !important;
                margin: 0 auto !important;
                overflow: hidden !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .nota-slip {
                width: 100% !important;
                height: 100% !important;
                max-height: 85mm !important;
                box-sizing: border-box !important;
                box-shadow: none !important;
                border: 2px dashed #000000 !important;
                border-radius: 0 !important;
                padding: 2mm 3.5mm !important;
                margin: 0 !important;
                break-inside: avoid !important;
                page-break-inside: avoid !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                overflow: hidden !important;
            }

            .nota-row {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
                width: 100% !important;
                gap: 6px !important;
            }

            .nota-row-bottom {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: flex-end !important;
                width: 100% !important;
                gap: 6px !important;
            }

            .nota-wilayah-box {
                width: 58px !important;
                height: 58px !important;
                flex-shrink: 0 !important;
                border: 2.5px solid #000000 !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
            }

            .nota-breakdown-table {
                width: auto !important;
                border-collapse: collapse !important;
            }

            .nota-breakdown-table td {
                padding: 0.5px 6px 0.5px 0 !important;
                border: none !important;
                font-size: 14px !important;
                line-height: 1.18 !important;
                font-weight: 800 !important;
                color: #000000 !important;
            }

            .nota-page-break {
                page-break-after: always !important;
                break-after: page !important;
            }
        }
    </style>

    @php
        $edisiLabelId = \Carbon\Carbon::parse($tanggalDistribusi)->locale('id')->translatedFormat('l, d F Y');
    @endphp

    <div class="space-y-5">

        <!-- Header Halaman Modern (Standar Divisi Iklan, 60-30-10 — Hanya Tampil di Layar) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Nota Distribusi Percetakan
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 leading-relaxed">
                        Daftar nota kirim harian untuk bagian percetakan sesuai data Edit Jatah edisi {{ $edisiLabelId }}.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('sirkulasi.jatah.edit', array_filter(['tanggal' => $tanggalDistribusi, 'jurusan' => ($jurusanPilihan ?? 'all') !== 'all' ? $jurusanPilihan : null])) }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Edit Jatah</span>
                </a>

                @if($notaItems->isNotEmpty())
                    <button type="button"
                            onclick="window.print()"
                            class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Semua Nota</span>
                    </button>
                @endif
            </div>
        </div>


        <!-- Toolbar Filter Nota Distribusi (Standar Divisi Iklan dengan Tombol Terapkan) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.cetak.nota-distribusi') }}" class="flex flex-col lg:flex-row lg:items-end gap-3">
                <div class="w-full lg:w-44 shrink-0">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Edisi</label>
                    <input type="date"
                           name="tanggal"
                           value="{{ $tanggalDistribusi }}"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="w-full lg:w-56 shrink-0">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Rute Armada</label>
                    <select name="jurusan"
                            class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Rute</option>
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->kode }}" {{ ($jurusanPilihan ?? 'all') === $jur->kode ? 'selected' : '' }}>
                                {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full lg:flex-1 min-w-0">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Cari Agen / Wilayah</label>
                    <input type="text"
                           name="q"
                           value="{{ $search ?? '' }}"
                           placeholder="Ketik nama agen, kode agen, atau wilayah..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="w-full lg:w-auto shrink-0 flex items-center gap-2">
                    <button type="submit"
                            class="flex-1 lg:flex-none h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer whitespace-nowrap">
                        Terapkan
                    </button>
                    @if(request()->hasAny(['tanggal', 'jurusan', 'q']))
                        <a href="{{ route('sirkulasi.cetak.nota-distribusi') }}"
                           title="Reset ke Edisi Besok"
                           class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center whitespace-nowrap">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if($notaItems->isEmpty())
            <div class="print:hidden bg-white border border-slate-200/90 rounded-2xl p-8 sm:p-14 text-center shadow-xs">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-b from-kp-blue-50 to-kp-blue-100/70 text-kp-blue-700 border border-kp-blue-200/80 flex items-center justify-center mb-4 shadow-2xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                @if(($jurusanPilihan ?? 'all') !== 'all' || !empty($search))
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 mb-2.5">
                        <span>Pencarian Rute</span>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Tidak Ada Nota Sesuai Filter</h3>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-lg mx-auto mt-1.5 leading-relaxed">
                        Tidak ditemukan nota distribusi untuk kriteria filter yang dipilih pada edisi <strong class="text-slate-800 font-semibold">{{ \Carbon\Carbon::parse($tanggalDistribusi)->locale('id')->translatedFormat('d M Y') }}</strong>.
                    </p>
                    <div class="mt-5 flex items-center justify-center gap-3">
                        <a href="{{ route('sirkulasi.cetak.nota-distribusi', ['tanggal' => $tanggalDistribusi]) }}"
                           class="h-11 px-6 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold inline-flex items-center justify-center transition duration-150 shadow-xs">
                            Tampilkan Semua Agen Edisi Ini
                        </a>
                    </div>
                @else
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 mb-2.5">
                        <span>Dokumen Ekspedisi</span>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Nota Distribusi Belum Siap Dicetak</h3>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-lg mx-auto mt-1.5 leading-relaxed">
                        Edisi <strong class="text-slate-800 font-semibold">{{ \Carbon\Carbon::parse($tanggalDistribusi)->locale('id')->translatedFormat('d F Y') }}</strong> belum memiliki alokasi jatah oplah. Tetapkan jatah agen terlebih dahulu agar lembar nota kirim dan surat jalan per rute dapat dicetak.
                    </p>
                    <div class="mt-5 flex items-center justify-center gap-3 flex-wrap">
                        <a href="{{ route('sirkulasi.jatah.copy', ['target_tanggal' => $tanggalDistribusi]) }}"
                           class="h-11 px-6 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold inline-flex items-center justify-center transition duration-150 shadow-xs">
                            Salin Data Jatah
                        </a>
                        <a href="{{ route('sirkulasi.jatah.edit', ['tanggal' => $tanggalDistribusi]) }}"
                           class="h-11 px-5 rounded-xl bg-white hover:bg-slate-50 active:bg-slate-100 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold inline-flex items-center justify-center transition shadow-2xs">
                            Input Oplah Harian
                        </a>
                    </div>
                @endif
            </div>
        @else
            <!-- Ringkasan KPI Harian (60-30-10 — Hanya Tampil di Layar) -->
            <div class="print:hidden grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Nota</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalNota, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">agen</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Langganan</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalLangganan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Eceran</span>
                    <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">{{ number_format($totalEceran, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Gratis &amp; Promo</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalGratis + $totalPerkenalan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Insidentil</span>
                    <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalInsidentil, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                </div>
                <div class="bg-kp-blue-50 border border-kp-blue-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-kp-blue-700 uppercase tracking-wider block">Total Cetak</span>
                    <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">{{ number_format($totalEksemplar, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-kp-blue-600">eks</span></p>
                </div>
            </div>

            <!-- Tabel Modern Siap Cetak Nota Kirim (60-30-10 — Hanya Tampil di Layar) -->
            <div class="print:hidden bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">

                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 w-12 text-center">No</th>
                                <th class="py-3 px-3.5">Kode Agen</th>
                                <th class="py-3 px-3.5">Kode Perkiraan</th>
                                <th class="py-3 px-3.5">Nama Agen</th>
                                <th class="py-3 px-3.5">Alamat</th>
                                <th class="py-3 px-3.5 text-center">Inisial</th>
                                <th class="py-3 px-3.5">Wilayah</th>
                                <th class="py-3 px-3.5 text-center">Rute</th>
                                <th class="py-3 px-3.5 text-right">Langganan</th>
                                <th class="py-3 px-3.5 text-right">Eceran</th>
                                <th class="py-3 px-3.5 text-right">Gratis</th>
                                <th class="py-3 px-3.5 text-right">Perkenalan</th>
                                <th class="py-3 px-3.5 text-right">Insidentil</th>
                                <th class="py-3 px-3.5 text-right">Total Eks</th>
                                <th class="py-3 px-3.5 text-right">Ikat ({{ '@' . ($isiBendel ?? 50) }})</th>
                                <th class="py-3 px-3.5 text-right">Lepas</th>
                                <th class="py-3 px-3.5 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @foreach($notaItems->values() as $idx => $item)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">{{ $item->kode_agen }}</td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs text-slate-500">{{ $item->kode_perkiraan }}</td>
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $item->nama_agen }}</td>
                                    <td class="py-3.5 px-3.5 text-slate-600 font-normal max-w-[240px] truncate" title="{{ $item->alamat }}">{{ $item->alamat }}</td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200 font-mono">
                                            {{ $item->inisial_wilayah }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-slate-700 font-normal">{{ $item->wilayah_nama }}</td>
                                    <td class="py-3.5 px-3.5 text-center font-mono text-slate-600">R-{{ $item->jurusan_kode }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">{{ number_format($item->langganan, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 bg-kp-blue-50/40 tabular-nums">{{ number_format($item->eceran, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">{{ number_format($item->gratis, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">{{ number_format($item->perkenalan, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">{{ number_format($item->insidentil, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-900 tabular-nums">{{ number_format($item->total_eks, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">{{ number_format($item->bendel_50, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">{{ number_format($item->sisa_ecer, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-center"
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

                                                <a href="{{ route('sirkulasi.jatah.edit', ['tanggal' => $tanggalDistribusi, 'jurusan' => $item->jurusan_kode]) }}"
                                                   @click="open = false"
                                                   class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Ubah di Edit Jatah</span>
                                                </a>

                                                <a href="{{ route('sirkulasi.tabel.agen', ['search' => $item->kode_agen]) }}"
                                                   @click="open = false"
                                                   class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Detail Agen</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t-2 border-slate-200 font-semibold bg-slate-50 text-slate-900 text-sm whitespace-nowrap">
                            <tr>
                                <td colspan="8" class="py-3.5 px-3.5 text-right uppercase text-xs font-bold text-slate-700">Total Cetak Harian:</td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">{{ number_format($totalLangganan, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-kp-blue-700 tabular-nums">{{ number_format($totalEceran, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">{{ number_format($totalGratis, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">{{ number_format($totalPerkenalan, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">{{ number_format($totalInsidentil, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-bold text-kp-blue-700 tabular-nums">{{ number_format($totalEksemplar, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">{{ number_format($totalBendel, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">{{ number_format($totalLepas, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- FORMAT CETAK SLIP NOTA KIRIM HITAM-PUTIH (HANYA TAMPIL SAAT PRINT, CONTINUOUS FORM LETTER 6 NOTA 2x3) -->
            <div class="hidden print:block nota-sheet-wrapper">
                @foreach($notaItems->values()->chunk(6) as $pageIndex => $pageSlips)
                    <div class="nota-continuous-page {{ !$loop->last ? 'nota-page-break' : '' }}">
                        @foreach($pageSlips as $idx => $item)
                            @php
                                $nomorUrut = ($pageIndex * 6) + $loop->iteration;
                            @endphp
                            <div class="nota-slip bg-white">
                                <!-- Baris Header Slip: Judul & Metadata Edisi / No. Urut -->
                                <div class="nota-row" style="border-bottom: 2px solid #000000; padding-bottom: 3px;">
                                    <div>
                                        <div style="font-size: 20px; font-weight: 900; letter-spacing: 0.05em; line-height: 1.05; color: #000000; text-transform: uppercase;">
                                            NOTA KIRIM
                                        </div>
                                        <div style="font-size: 11px; font-weight: 800; letter-spacing: 0.03em; margin-top: 2px; color: #000000; text-transform: uppercase;">
                                            SIRKULASI HU. KABAR PRIANGAN
                                        </div>
                                    </div>
                                    <div style="text-align: right; font-size: 11.5px; font-weight: 800; line-height: 1.25; color: #000000; flex-shrink: 0;">
                                        <div style="text-transform: uppercase;">{{ $hariEdisi }}</div>
                                        <div style="margin-top: 2px;">NOTA {{ str_pad($nomorUrut, 2, '0', STR_PAD_LEFT) }}/{{ str_pad($totalNota, 2, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>

                                <!-- Blok Identitas Agen & Inisial Wilayah Besar -->
                                <div class="nota-row" style="padding: 3px 0; flex: 1; align-items: flex-start;">
                                    <div style="flex: 1; padding-right: 6px; min-width: 0;">
                                        <div style="font-size: 12px; font-weight: 800; color: #000000; line-height: 1.15;">
                                            KODE: {{ $item->kode_perkiraan }} <span style="font-weight: 700;">({{ $item->kode_agen }})</span>
                                        </div>
                                        <div style="font-size: 18.5px; font-weight: 900; text-transform: uppercase; color: #000000; line-height: 1.12; margin-top: 3px; word-break: break-word;">
                                            {{ strtoupper($item->nama_agen) }}
                                        </div>
                                        <div style="font-size: 16px; font-weight: 900; text-transform: uppercase; color: #000000; line-height: 1.15; margin-top: 2px; word-break: break-word;">
                                            RUTE: R-{{ $item->jurusan_kode }} &bull; {{ strtoupper($item->jurusan_nama ?? '') }}
                                        </div>
                                        <div style="font-size: 13px; font-weight: 700; text-transform: uppercase; color: #000000; line-height: 1.15; margin-top: 2px; word-break: break-word;">
                                            {{ strtoupper($item->alamat) }}
                                        </div>
                                        <div style="font-size: 14px; font-weight: 900; text-transform: uppercase; color: #000000; line-height: 1.15; margin-top: 2px; letter-spacing: 0.02em;">
                                            {{ strtoupper($item->wilayah_nama) }}
                                        </div>
                                    </div>

                                    <div class="nota-wilayah-box">
                                        <span style="font-size: 36px; font-weight: 900; line-height: 1; color: #000000;">{{ $item->inisial_wilayah }}</span>
                                    </div>
                                </div>

                                <!-- Blok Bawah: Rincian Eksemplar (Kiri) & Total Besar (Kanan) -->
                                <div class="nota-row-bottom" style="border-top: 2px solid #000000; padding-top: 4px;">
                                    <table class="no-brand-table nota-breakdown-table">
                                        <tbody>
                                            <tr>
                                                <td>LANGGANAN</td>
                                                <td>:</td>
                                                <td style="text-align: right; min-width: 38px;">{{ number_format($item->langganan, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td>ECERAN</td>
                                                <td>:</td>
                                                <td style="text-align: right; min-width: 38px;">{{ number_format($item->eceran, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td>GRATIS</td>
                                                <td>:</td>
                                                <td style="text-align: right; min-width: 38px;">{{ number_format($item->gratis, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td>PERKENALAN</td>
                                                <td>:</td>
                                                <td style="text-align: right; min-width: 38px;">{{ number_format($item->perkenalan, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td>INSIDENTIL</td>
                                                <td>:</td>
                                                <td style="text-align: right; min-width: 38px;">{{ number_format($item->insidentil, 0, ',', '.') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div style="text-align: right; color: #000000; flex-shrink: 0;">
                                        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.03em;">TOTAL EKSEMPLAR</div>
                                        <div style="display: inline-block; font-size: 48px; font-weight: 900; line-height: 0.95; border-bottom: 3.5px solid #000000; padding: 2px 4px 3px 4px; margin-top: 2px;">
                                            {{ number_format($item->total_eks, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</x-app-layout>
