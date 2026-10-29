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
                <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-sky-600 uppercase tracking-wider">Menu Cetak</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Nota & Distribusi Armada Subuh</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Surat jalan pengiriman armada, nota per jurusan, dan nota per agen/susulan sesuai standar DOS SIRK.EXE
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-sky-700 hover:bg-sky-800 text-white shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Surat Jalan</span>
                </button>
            </div>
        </div>

        <!-- Filter Sub-Menu Nota: 3 Pilihan Nota Sesuai DOS SIRK.EXE -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.cetak.nota-distribusi') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Format Dokumen Cetak</label>
                    <select name="jenis" class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-sky-500 focus:ring-sky-500">
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

                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Pilih Jurusan Armada</label>
                    <select name="jurusan" class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-sky-500 focus:ring-sky-500">
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->kode }}" {{ ($activeJurusan->kode ?? '') === $jur->kode ? 'selected' : '' }}>
                                {{ $jur->kode }} - {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Tanggal Distribusi Edisi</label>
                    <input type="date" name="tanggal" value="{{ $tanggalDistribusi }}" class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-sky-500 focus:ring-sky-500">
                </div>

                <div>
                    <button type="submit" class="w-full py-1.5 px-3 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                        Tampilkan Dokumen
                    </button>
                </div>
            </form>
        </div>

        <!-- Dokumen Siap Cetak (Printable Sheet) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs p-6 printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
            
            <!-- Kop Surat Jalan -->
            <div class="border-b-2 border-slate-900 pb-4 mb-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-base font-black tracking-tight text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                        <h3 class="text-xs font-bold text-slate-700 uppercase mt-0.5">
                            @if(($jenisNota ?? '') === 'nota-jurusan')
                                NOTA PENGIRIMAN PER JURUSAN ARMADA
                            @elseif(($jenisNota ?? '') === 'nota-agen')
                                NOTA PER AGEN / KIRIMAN SUSULAN
                            @else
                                SURAT JALAN & DAFTAR PENGEPAKAN (PACKING LIST) ARMADA SUBUH
                            @endif
                        </h3>
                    </div>
                    <div class="text-right text-xs">
                        <div class="font-mono font-bold text-slate-900 text-sm">NO: SJ/KP/{{ str_replace('-', '', $tanggalDistribusi) }}/{{ $activeJurusan->kode }}</div>
                        <div class="text-slate-500 text-[11px] mt-0.5">Edisi: {{ \Carbon\Carbon::parse($tanggalDistribusi)->translatedFormat('l, d F Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Detail Armada & Rute -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs mb-5">
                <div>
                    <span class="text-slate-400 text-[11px]">Jurusan / Jalur:</span>
                    <div class="font-bold text-slate-900 mt-0.5">{{ $activeJurusan->kode }} - {{ $activeJurusan->nama_jurusan }}</div>
                </div>
                <div>
                    <span class="text-slate-400 text-[11px]">Kendaraan Armada:</span>
                    <div class="font-bold text-slate-900 mt-0.5">{{ $activeJurusan->armada }}</div>
                </div>
                <div>
                    <span class="text-slate-400 text-[11px]">Sopir / Kru Subuh:</span>
                    <div class="font-bold text-slate-900 mt-0.5">{{ $activeJurusan->sopir }}</div>
                </div>
                <div>
                    <span class="text-slate-400 text-[11px]">Jadwal Berangkat:</span>
                    <div class="font-bold text-amber-700 font-mono mt-0.5">{{ $activeJurusan->jam_berangkat }}</div>
                </div>
            </div>

            <!-- Tabel Daftar Packing per Agen di Jurusan Terpilih -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="border-y-2 border-slate-300 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3 w-10 text-center">No</th>
                            <th class="py-2.5 px-3">Kode Agen</th>
                            <th class="py-2.5 px-4">Nama Agen & Drop Point</th>
                            <th class="py-2.5 px-4">Alamat Kios / Titik Serah</th>
                            <th class="py-2.5 px-3 text-center">Jenis</th>
                            <th class="py-2.5 px-3 text-right">Jatah Total</th>
                            <th class="py-2.5 px-4">Rincian Kemasan Pengepakan (Bendel 50an)</th>
                            <th class="py-2.5 px-4 text-center">Paraf Penerima</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($packingItems as $idx => $item)
                            <tr>
                                <td class="py-2.5 px-3 text-center text-slate-400">{{ $idx + 1 }}</td>
                                <td class="py-2.5 px-3 font-mono font-bold">{{ $item->kode_agen }}</td>
                                <td class="py-2.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $item->nama_agen }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $item->telepon }}</div>
                                </td>
                                <td class="py-2.5 px-4 text-slate-600 text-[11px] max-w-xs">{{ $item->alamat }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->kategori === '11' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $item->kategori }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-right font-extrabold text-slate-900">{{ $item->total_eks }} eks</td>
                                <td class="py-2.5 px-4 font-mono text-[11px] text-slate-800">
                                    <span class="font-bold text-sky-800">{{ $item->bendel_50 }} Bendel ({{ $item->bendel_50 * 50 }} eks)</span>
                                    @if($item->sisa_ecer > 0)
                                        + <span class="text-amber-700 font-bold">{{ $item->sisa_ecer }} eks lepas</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    <div class="w-20 border-b border-dotted border-slate-400 mx-auto h-5"></div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-6 text-center text-slate-400">
                                    Belum ada titik agen aktif pada jurusan armada ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t-2 border-slate-900 font-bold bg-slate-50 text-slate-900">
                        <tr>
                            <td colspan="5" class="py-3 px-4 text-right uppercase text-[11px]">Total Muatan Rute Ini:</td>
                            <td class="py-3 px-3 text-right text-sm text-sky-900 font-black">{{ number_format($totalEksemplar, 0, ',', '.') }} eks</td>
                            <td class="py-3 px-4 font-mono text-xs" colspan="2">
                                = <strong class="text-slate-900">{{ $totalBendel }} Bendel</strong> + <strong class="text-amber-800">{{ $totalEceran }} eks lepas</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Tanda Tangan Serah Terima Ekspedisi -->
            <div class="mt-8 pt-4 border-t border-slate-200 grid grid-cols-3 text-center text-xs text-slate-800">
                <div>
                    <p class="text-slate-500">Diserahkan Oleh,</p>
                    <p class="font-bold text-slate-900 mt-1">Petugas Pengepakan Gudang</p>
                    <div class="h-16"></div>
                    <p class="font-bold underline text-slate-900">( ......................................... )</p>
                </div>
                <div>
                    <p class="text-slate-500">Dibawa Oleh,</p>
                    <p class="font-bold text-slate-900 mt-1">Sopir / Ekspedisi Armada</p>
                    <div class="h-16"></div>
                    <p class="font-bold underline text-slate-900">( {{ $activeJurusan->sopir }} )</p>
                </div>
                <div>
                    <p class="text-slate-500">Mengetahui,</p>
                    <p class="font-bold text-slate-900 mt-1">Koordinator Sirkulasi</p>
                    <div class="h-16"></div>
                    <p class="font-bold underline text-slate-900">( ......................................... )</p>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
