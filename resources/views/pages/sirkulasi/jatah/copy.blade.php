<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print print:hidden">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Copy Data Penjualan
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Salin pola alokasi jatah koran dari edisi sebelumnya ke tanggal target.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                @if($hasCopied)
                    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Hasil Salin</span>
                    </button>
                @endif
                <a href="{{ route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal]) }}" class="inline-flex items-center gap-1.5 h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer whitespace-nowrap">
                    <span>Ke Edit Matriks</span>
                </a>
            </div>
        </div>

        @if(session('error'))
            <div class="no-print print:hidden p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between gap-2 shadow-2xs">
                <span>{{ session('error') }}</span>
                <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $targetTanggal]) }}" class="h-9 px-3.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-xl text-xs font-bold inline-flex items-center shrink-0 transition cursor-pointer">
                    Ajukan Memo
                </a>
            </div>
        @endif

        @if($isTargetLocked ?? false)
            <div class="no-print print:hidden p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                <span>
                    Target edisi ({{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('d F Y') }}) terkunci Cut-Off. Ajukan memo ke Akuntansi untuk menyalin jatah.
                </span>
                <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $targetTanggal]) }}" class="h-9 px-3.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs font-bold rounded-xl shadow-xs inline-flex items-center shrink-0 transition cursor-pointer">
                    Tutup Data &amp; Memo
                </a>
            </div>
        @endif

        @if(session('success'))
            <div class="no-print print:hidden p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
                <span>{{ session('success') }}</span>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" onclick="window.print()" class="h-9 px-3.5 text-xs font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white shadow-xs transition inline-flex items-center cursor-pointer">
                        <span>Cetak Hasil Salin</span>
                    </button>
                    <a href="{{ route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal]) }}" class="h-9 px-3.5 text-xs font-semibold rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 transition inline-flex items-center cursor-pointer">
                        <span>Edit Matriks</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- Panel Form Konfigurasi Salin Pola Jatah -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden no-print print:hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Konfigurasi Tanggal Salin Jatah</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih tanggal sumber dan tanggal target edisi distribusi</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="setPreset('{{ $today->toDateString() }}', '{{ $tomorrow->toDateString() }}')"
                            class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 transition cursor-pointer">
                        Hari Ini &rarr; Besok
                    </button>
                    <button type="button" onclick="setPreset('{{ $lastWeekSameDay->toDateString() }}', '{{ $tomorrow->toDateString() }}')"
                            class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 transition cursor-pointer">
                        Minggu Lalu &rarr; Besok
                    </button>
                </div>
            </div>

            <form id="formSalinJatah" method="POST" action="{{ route('sirkulasi.jatah.copy.process') }}" class="p-4 sm:p-5 space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Sumber (Pola Asal)</label>
                        <input type="date" id="inputSumberTanggal" name="sumber_tanggal" value="{{ $sumberTanggal }}" required
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Target Distribusi</label>
                        <input type="date" id="inputTargetTanggal" name="target_tanggal" value="{{ $targetTanggal }}" required
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Filter Rute Distribusi</label>
                        <select name="jurusan" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="all">Semua Rute Distribusi</option>
                            @foreach($jurusanList as $jur)
                                <option value="{{ $jur->kode }}" {{ ($jurusanPilihan ?? '') === $jur->kode ? 'selected' : '' }}>
                                    {{ $jur->kode }} - {{ $jur->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    @if($isTargetLocked ?? false)
                        <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $targetTanggal]) }}" class="h-11 px-4 text-sm font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 transition inline-flex items-center cursor-pointer">
                            <span>Target Terkunci &mdash; Ajukan Memo</span>
                        </a>
                    @else
                        <button type="submit" name="action_type" value="stay_and_print" class="h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer inline-flex items-center gap-2">
                            <span>Proses Salin Jatah</span>
                        </button>
                        <button type="submit" name="action_type" value="go_to_edit" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition cursor-pointer inline-flex items-center gap-2">
                            <span>Salin &amp; Edit Matriks</span>
                        </button>
                    @endif
                </div>
            </form>
        </div>

        @if(!$hasCopied)
            <div class="bg-white border border-slate-200 rounded-xl p-8 text-center shadow-xs">
                <h3 class="text-sm font-bold text-slate-900">Belum Ada Data Disalin</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                    Pilih tanggal sumber dan tanggal target di atas, lalu klik Proses Salin Jatah.
                </p>
            </div>
        @else
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
                            KODE DOK: SIRK-COPY-02
                        </span>
                        <p class="text-[10px] text-slate-600 mt-1">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
                        <p class="text-[10px] text-slate-600">Operator: {{ Auth::user()->name }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-300 flex items-center justify-between">
                    <div>
                        <h1 class="text-sm font-black text-slate-900 uppercase tracking-tight">BUKTI REKAPITULASI HASIL SALIN (COPY) DATA PENJUALAN KORAN</h1>
                        <div class="text-[11px] text-slate-700 mt-0.5">
                            <span>Pola Asal: <strong class="text-slate-900">{{ \Carbon\Carbon::parse($sumberTanggal)->translatedFormat('l, d F Y') }}</strong></span>
                            <span class="mx-2 text-slate-400">&rarr;</span>
                            <span>Edisi Tujuan: <strong class="text-slate-900">{{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('l, d F Y') }}</strong></span>
                            <span class="mx-2 text-slate-400">&bull;</span>
                            <span>Rute: <strong class="text-slate-900">{{ $jurusanPilihan === 'all' ? 'Semua Rute Distribusi' : 'Rute ' . $jurusanPilihan }}</strong></span>
                        </div>
                    </div>
                    <div class="text-right text-[11px] font-semibold text-slate-700">
                        Grand Total Salin: <span class="font-black text-slate-900 text-xs">{{ number_format($grandTotalJatah, 0, ',', '.') }} eks</span>
                        &bull; Estimasi Omset: <span class="font-bold text-slate-900 text-xs">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- KPI Ringkasan Matriks Hasil Copy -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 print:grid print:grid-cols-5 print:gap-2 print:mb-3">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Langganan Disalin</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalLangganan, 0, ',', '.') }}</p>
                    <span class="text-xs text-slate-400 mt-1 block">Eksemplar pelanggan</span>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Konsinyasi (Rp3.000)</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">{{ number_format($totalKonsinyasi, 0, ',', '.') }}</p>
                    <span class="text-xs text-slate-400 mt-1 block">Eksemplar konsinyasi</span>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jatah Gratis</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalGratis, 0, ',', '.') }}</p>
                    <span class="text-xs text-slate-400 mt-1 block">Eksemplar promosi</span>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Insidentil</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalInsidentil, 0, ',', '.') }}</p>
                    <span class="text-xs text-slate-400 mt-1 block">Eksemplar eceran</span>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Eksemplar Salin</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">{{ number_format($grandTotalJatah, 0, ',', '.') }}</p>
                    <span class="text-xs text-slate-400 mt-1 block">Total eksemplar edisi</span>
                </div>
            </div>

            <!-- Tabel Hasil Copy Data Penjualan -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area">
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 print:hidden">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Matriks Hasil Salin Data Penjualan</h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ \Carbon\Carbon::parse($sumberTanggal)->translatedFormat('d F Y') }} &rarr; {{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('d F Y') }}
                        </p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono w-fit">
                        {{ number_format($grandTotalJatah, 0, ',', '.') }} Eks Disalin
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 w-12 text-center">No</th>
                                <th class="py-3 px-3.5 w-28">Kode</th>
                                <th class="py-3 px-3.5 min-w-[170px]">Nama Agen &amp; Kios</th>
                                <th class="py-3 px-3.5 min-w-[140px]">Rute Distribusi</th>
                                <th class="py-3 px-3.5 text-right">Langganan</th>
                                <th class="py-3 px-3.5 text-right">Konsinyasi</th>
                                <th class="py-3 px-3.5 text-right">Gratis</th>
                                <th class="py-3 px-3.5 text-right">Insidentil</th>
                                <th class="py-3 px-3.5 text-right w-24">Total Jatah</th>
                                <th class="py-3 px-3.5 text-right w-32">Estimasi Omset</th>
                                <th class="py-3 px-3.5 text-center w-24">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @foreach($matrixJatah as $idx => $item)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal w-12">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                        {{ $item->kode_agen }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                        {{ $item->nama_agen }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                        <span class="font-semibold text-slate-800">Rute {{ $item->jurusan_kode }}</span>
                                        <span class="text-slate-400">({{ Str::limit($item->jurusan_nama, 18) }})</span>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">
                                        {{ number_format($item->langganan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums">
                                        {{ number_format($item->konsinyasi, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">
                                        {{ number_format($item->gratis, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">
                                        {{ number_format($item->insidentil, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                        {{ number_format($item->total_jatah, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($item->estimasi_omset, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Tersalin
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                            <tr>
                                <td colspan="4" class="py-3 px-3.5 text-right font-sans text-xs font-bold uppercase tracking-wider text-slate-700">Akumulasi Total</td>
                                <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalLangganan, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right text-kp-blue-700 font-bold tabular-nums">{{ number_format($totalKonsinyasi, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalGratis, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalInsidentil, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right font-bold tabular-nums">{{ number_format($grandTotalJatah, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-bold text-kp-blue-700 tabular-nums">{{ number_format($grandTotalOmset, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 text-center text-slate-400 print:hidden">&mdash;</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="px-4 sm:px-5 py-3 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2 print:hidden">
                    <span>Data salinan tersimpan pada matriks jatah edisi tujuan.</span>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="window.print()" class="h-9 px-3.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer inline-flex items-center">
                            <span>Cetak Hasil Salin</span>
                        </button>
                        <a href="{{ route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal]) }}" class="h-9 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-xl text-xs font-semibold transition inline-flex items-center cursor-pointer">
                            <span>Edit Matriks</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tanda Tangan & Pengesahan Dokumen Salin -->
            <div class="hidden print:grid grid-cols-3 text-center text-xs text-slate-800 mt-8 pt-4 border-t border-slate-300 break-inside-avoid">
                <div>
                    <p class="text-slate-500">Dibuat &amp; Disalin Oleh,</p>
                    <p class="font-bold text-slate-900 mt-1">Petugas Operator Sirkulasi</p>
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
        @endif

    </div>

    @push('scripts')
    <script>
        function setPreset(sumber, target) {
            document.getElementById('inputSumberTanggal').value = sumber;
            document.getElementById('inputTargetTanggal').value = target;
        }
    </script>
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
