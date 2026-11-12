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
                border: 1px solid #cbd5e1 !important;
            }
        }
    </style>

    <div class="space-y-5">

        <!-- Header Halaman (Standar Divisi Iklan, 60-30-10) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Nama &amp; Alamat Agen
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Direktori alamat agen dan sub agen per wilayah atau jurusan armada distribusi.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
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

        <!-- Filter Direktori (Selaras Filter Divisi Iklan, 60-30-10) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.cetak.alamat-agen') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Filter Wilayah</label>
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
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Filter Rute Distribusi</label>
                    <select name="jurusan" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Rute (01 s.d. 15)</option>
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->kode }}" {{ ($jurusanPilihan ?? '') === $jur->kode ? 'selected' : '' }}>
                                {{ $jur->kode }} - {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Agen</label>
                    <select name="kategori" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Jenis (11, 99, 00)</option>
                        <option value="11" {{ ($kategoriPilihan ?? '') === '11' ? 'selected' : '' }}>11 - Agen Besar</option>
                        <option value="99" {{ ($kategoriPilihan ?? '') === '99' ? 'selected' : '' }}>99 - Sub Agen</option>
                        <option value="00" {{ ($kategoriPilihan ?? '') === '00' ? 'selected' : '' }}>00 - Non Aktif</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-xl text-sm font-bold shadow-xs transition cursor-pointer">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Kop Buku Direktori (HANYA TAMPIL SAAT PRINT) -->
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

        <!-- Tabel Direktori Agen (Standar Divisi Iklan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
            <div class="overflow-x-auto min-h-[300px] print:overflow-visible">
                <table class="w-full text-left text-sm border-collapse print:min-w-0 print:w-full print:text-[10px]">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap print:bg-slate-100 print:text-slate-900">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">Kode Agen</th>
                            <th class="py-3 px-3.5">Nama Pemilik / Kios Koran</th>
                            <th class="py-3 px-3.5">Alamat Lengkap Kios / Drop Point</th>
                            <th class="py-3 px-3.5 text-center">Jenis</th>
                            <th class="py-3 px-3.5">Wilayah</th>
                            <th class="py-3 px-3.5">Rute</th>
                            <th class="py-3 px-3.5">Kontak / Telp</th>
                            <th class="py-3 px-3.5 text-right">Jatah (Eks)</th>
                            <th class="print:hidden py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($agenList->values() as $idx => $agen)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">{{ $agen->kode_agen }}</td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $agen->nama_agen }}</td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal max-w-[260px] truncate print:whitespace-normal" title="{{ $agen->alamat }}">
                                    {{ $agen->alamat }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $agen->kategori === '11' ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                        {{ $agen->kategori }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-700 font-normal">{{ $agen->wilayah_nama }}</td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-600 font-normal">Rute {{ $agen->jurusan_kode }}</td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-700 font-normal">{{ $agen->telepon ?: '-' }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    {{ number_format($agen->jatah_default, 0, ',', '.') }}
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

                                            <a href="{{ route('sirkulasi.tabel.agen', ['search' => $agen->kode_agen]) }}"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail Agen</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-500 font-normal">
                                    Tidak ada data agen yang cocok dengan filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($agenList->isNotEmpty())
                        <tfoot class="bg-slate-50 font-semibold text-slate-900 border-t-2 border-slate-200 text-sm whitespace-nowrap">
                            <tr>
                                <td colspan="8" class="py-3.5 px-3.5 text-right uppercase text-xs font-bold text-slate-700">Total Jatah ({{ $agenList->count() }} Agen):</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-bold text-kp-blue-700 tabular-nums">
                                    {{ number_format($agenList->sum('jatah_default'), 0, ',', '.') }}
                                </td>
                                <td class="print:hidden"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
