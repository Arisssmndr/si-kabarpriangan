<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-teal-600 uppercase tracking-wider">Menu Cetak</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Laporan Pemasaran (DOS SIRK.EXE)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        8 format standar cetak laporan pemasaran: Agen Besar, Sub Agen, Rekap Bulanan, Wilayah, Jurusan, Penerimaan, & Oplah Tahunan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-teal-700 hover:bg-teal-800 text-white shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </div>

        <!-- 8 Tabs Menu Pemasaran DOS SIRK.EXE -->
        <div class="bg-white border border-slate-200 rounded-xl p-2 shadow-xs overflow-x-auto">
            <div class="flex items-center gap-1 min-w-max">
                <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'agen-besar', 'bulan' => $bulan]) }}" 
                   class="px-3 py-1.5 text-xs rounded-lg font-semibold transition-colors {{ ($tab ?? 'agen-besar') === 'agen-besar' ? 'bg-teal-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    1. Agen Besar / Terbit
                </a>
                <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'sub-agen', 'bulan' => $bulan]) }}" 
                   class="px-3 py-1.5 text-xs rounded-lg font-semibold transition-colors {{ ($tab ?? '') === 'sub-agen' ? 'bg-teal-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    2. Sub Agen / Terbit
                </a>
                <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'rekap-bulan', 'bulan' => $bulan]) }}" 
                   class="px-3 py-1.5 text-xs rounded-lg font-semibold transition-colors {{ ($tab ?? '') === 'rekap-bulan' ? 'bg-teal-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    3. Rekap Terbit / Bulan
                </a>
                <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'rekap-wilayah', 'bulan' => $bulan]) }}" 
                   class="px-3 py-1.5 text-xs rounded-lg font-semibold transition-colors {{ ($tab ?? '') === 'rekap-wilayah' ? 'bg-teal-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    4. Rekap / Wilayah
                </a>
                <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'rekap-jurusan', 'bulan' => $bulan]) }}" 
                   class="px-3 py-1.5 text-xs rounded-lg font-semibold transition-colors {{ ($tab ?? '') === 'rekap-jurusan' ? 'bg-teal-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    5. Rekap / Jurusan
                </a>
                <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'penerimaan', 'bulan' => $bulan]) }}" 
                   class="px-3 py-1.5 text-xs rounded-lg font-semibold transition-colors {{ ($tab ?? '') === 'penerimaan' ? 'bg-teal-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    6. Penerimaan / Bulan
                </a>
                <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'oplah-tahun', 'bulan' => $bulan]) }}" 
                   class="px-3 py-1.5 text-xs rounded-lg font-semibold transition-colors {{ ($tab ?? '') === 'oplah-tahun' ? 'bg-teal-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    7. Oplah / Tahun
                </a>
                <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'bulan-wilayah', 'bulan' => $bulan]) }}" 
                   class="px-3 py-1.5 text-xs rounded-lg font-semibold transition-colors {{ ($tab ?? '') === 'bulan-wilayah' ? 'bg-teal-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    8. Bulan / Wilayah
                </a>
            </div>
        </div>

        <!-- Konten Laporan Terpilih -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden p-6 printable-area">
            
            <!-- Kop Cetak Surat Kabar -->
            <div class="border-b-2 border-slate-900 pb-4 mb-5 text-center">
                <h2 class="text-base font-extrabold uppercase tracking-tight text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                <h3 class="text-xs font-bold uppercase text-slate-700 mt-0.5">DIVISI SIRKULASI &bull; SISTEM INFORMASI SIRKULASI (SIRK.EXE)</h3>
                <p class="text-[11px] text-slate-500 mt-1">
                    Jl. Dr. Sukardjo No. 84 Tasikmalaya &bull; Telepon: (0265) 331234 &bull; Priangan Timur
                </p>
                <div class="mt-3 inline-block px-3 py-1 rounded bg-slate-100 font-bold text-xs uppercase text-slate-800">
                    @if($tab === 'sub-agen')
                        LAPORAN PEMASARAN SUB AGEN PER TERBIT
                    @elseif($tab === 'rekap-bulan')
                        REKAPITULASI PENJUALAN PER TERBIT PER BULAN
                    @elseif($tab === 'rekap-wilayah')
                        REKAPITULASI DISTRIBUSI PER TERBIT PER WILAYAH (01 s.d. 08)
                    @elseif($tab === 'rekap-jurusan')
                        REKAPITULASI PENGIRIMAN PER TERBIT PER JURUSAN (15 RUTE ARMADA)
                    @elseif($tab === 'penerimaan')
                        LAPORAN PENERIMAAN KORAN DARI PERCETAKAN PER BULAN
                    @elseif($tab === 'oplah-tahun')
                        AUDIT TIRAS & REKAP OPLAH CETAK PER TAHUN (SPS INDONESIA)
                    @elseif($tab === 'bulan-wilayah')
                        REKAPITULASI PENJUALAN BULANAN PER WILAYAH
                    @else
                        LAPORAN PEMASARAN AGEN BESAR PER TERBIT
                    @endif
                    &bull; PERIODE {{ strtoupper(\Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y')) }}
                </div>
            </div>

            <!-- Tabel Data Berdasarkan Tab -->
            @if($tab === 'sub-agen')
                <!-- 2. Sub Agen -->
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="border-y border-slate-300 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">Kode Sub Agen</th>
                            <th class="py-2.5 px-3">Nama Sub Agen / Kios</th>
                            <th class="py-2.5 px-3">Induk Agen Besar</th>
                            <th class="py-2.5 px-3">Wilayah</th>
                            <th class="py-2.5 px-3 text-right">Alokasi Harian</th>
                            <th class="py-2.5 px-3 text-right">Konsinyasi (Rp3.000)</th>
                            <th class="py-2.5 px-3 text-right">Estimasi Tagihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($agenList->where('kategori', '99') as $sub)
                            <tr>
                                <td class="py-2 px-3 font-mono font-bold">{{ $sub->kode_agen }}</td>
                                <td class="py-2 px-3 font-semibold">{{ $sub->nama_agen }}</td>
                                <td class="py-2 px-3 text-slate-600">{{ $sub->induk_agen }}</td>
                                <td class="py-2 px-3">{{ $sub->wilayah_nama }}</td>
                                <td class="py-2 px-3 text-right font-bold">{{ $sub->jatah_default }} eks</td>
                                <td class="py-2 px-3 text-right">{{ (int)($sub->jatah_default * 0.8) }} eks</td>
                                <td class="py-2 px-3 text-right font-bold text-slate-900">Rp {{ number_format((int)($sub->jatah_default * 0.8) * 3000 * 30, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @elseif($tab === 'rekap-wilayah' || $tab === 'bulan-wilayah')
                <!-- 4 & 8. Rekap Wilayah -->
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="border-y border-slate-300 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3 text-center">Kode</th>
                            <th class="py-2.5 px-4">Wilayah Pemasaran</th>
                            <th class="py-2.5 px-4">Tipe Alokasi</th>
                            <th class="py-2.5 px-4 text-center">Titik Agen</th>
                            <th class="py-2.5 px-4 text-right">Oplah Rata-rata / Hari</th>
                            <th class="py-2.5 px-4 text-right">Total Oplah Bulanan</th>
                            <th class="py-2.5 px-4 text-right">Omset Bruto Bulanan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($wilayahList as $wil)
                            <tr>
                                <td class="py-2 px-3 text-center font-mono font-bold">{{ $wil->kode }}</td>
                                <td class="py-2 px-4 font-bold">{{ $wil->nama }}</td>
                                <td class="py-2 px-4">{{ $wil->tipe }}</td>
                                <td class="py-2 px-4 text-center font-bold">{{ $wil->total_agen }} titik</td>
                                <td class="py-2 px-4 text-right">{{ number_format($wil->alokasi_default, 0, ',', '.') }} eks</td>
                                <td class="py-2 px-4 text-right font-bold text-slate-900">{{ number_format($wil->alokasi_default * 30, 0, ',', '.') }} eks</td>
                                <td class="py-2 px-4 text-right font-bold text-emerald-800">Rp {{ number_format(($wil->alokasi_default * 30 * 2800), 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @elseif($tab === 'rekap-jurusan')
                <!-- 5. Rekap Jurusan -->
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="border-y border-slate-300 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3 text-center">Rute</th>
                            <th class="py-2.5 px-4">Nama Jurusan Armada</th>
                            <th class="py-2.5 px-4">Armada Mobil</th>
                            <th class="py-2.5 px-4 text-center">Jam Berangkat</th>
                            <th class="py-2.5 px-4 text-center">Total Agen</th>
                            <th class="py-2.5 px-4 text-right">Alokasi Harian</th>
                            <th class="py-2.5 px-4 text-right">Akumulasi Bulanan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($jurusanList as $jur)
                            <tr>
                                <td class="py-2 px-3 text-center font-mono font-bold">{{ $jur->kode }}</td>
                                <td class="py-2 px-4 font-bold">{{ $jur->nama_jurusan }}</td>
                                <td class="py-2 px-4">{{ $jur->armada }}</td>
                                <td class="py-2 px-4 text-center font-mono text-[11px]">{{ $jur->jam_berangkat }}</td>
                                <td class="py-2 px-4 text-center font-bold">{{ $jur->total_agen }}</td>
                                <td class="py-2 px-4 text-right">{{ number_format($jur->alokasi_oplah, 0, ',', '.') }} eks</td>
                                <td class="py-2 px-4 text-right font-bold text-slate-900">{{ number_format($jur->alokasi_oplah * 30, 0, ',', '.') }} eks</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @elseif($tab === 'oplah-tahun')
                <!-- 7. Oplah per Tahun -->
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="border-y border-slate-300 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-4">Bulan Terbit</th>
                            <th class="py-2.5 px-4 text-center">Jumlah Hari Terbit</th>
                            <th class="py-2.5 px-4 text-right">Total Oplah Cetak</th>
                            <th class="py-2.5 px-4 text-right">Rata-rata Oplah Harian</th>
                            <th class="py-2.5 px-4 text-right">Total Retur (Fisik)</th>
                            <th class="py-2.5 px-4 text-center">Rasio Retur</th>
                            <th class="py-2.5 px-4 text-right">Nett Sales Terjual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-medium">
                        @php
                            $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        @endphp
                        @foreach($namaBulan as $idx => $nb)
                            <tr>
                                <td class="py-2 px-4 font-bold">{{ $nb }} 2026</td>
                                <td class="py-2 px-4 text-center">30 hari</td>
                                <td class="py-2 px-4 text-right font-bold">{{ number_format(375000 + ($idx * 1200), 0, ',', '.') }} eks</td>
                                <td class="py-2 px-4 text-right">{{ number_format((375000 + ($idx * 1200)) / 30, 0, ',', '.') }} eks</td>
                                <td class="py-2 px-4 text-right text-rose-600">{{ number_format(7500 + ($idx * 40), 0, ',', '.') }} eks</td>
                                <td class="py-2 px-4 text-center">2.0%</td>
                                <td class="py-2 px-4 text-right font-bold text-emerald-800">{{ number_format((375000 + ($idx * 1200)) - (7500 + ($idx * 40)), 0, ',', '.') }} eks</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @else
                <!-- Default: 1. Agen Besar per Terbit -->
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="border-y border-slate-300 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">Kode Agen</th>
                            <th class="py-2.5 px-4">Nama Agen Besar</th>
                            <th class="py-2.5 px-3">Wilayah</th>
                            <th class="py-2.5 px-3">Jurusan Armada</th>
                            <th class="py-2.5 px-3 text-right">Jatah Langganan</th>
                            <th class="py-2.5 px-3 text-right text-emerald-800">Konsinyasi Rp3.000</th>
                            <th class="py-2.5 px-3 text-right font-extrabold text-slate-900">Total Oplah Terbit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($agenList->where('kategori', '11') as $ab)
                            <tr>
                                <td class="py-2.5 px-3 font-mono font-bold">{{ $ab->kode_agen }}</td>
                                <td class="py-2.5 px-4 font-bold text-slate-900">{{ $ab->nama_agen }}</td>
                                <td class="py-2.5 px-3">{{ $ab->wilayah_nama }}</td>
                                <td class="py-2.5 px-3 text-[11px] text-slate-600">Rute {{ $ab->jurusan_kode }}</td>
                                <td class="py-2.5 px-3 text-right">{{ (int)($ab->jatah_default * 0.6) }} eks</td>
                                <td class="py-2.5 px-3 text-right font-semibold text-emerald-700">{{ (int)($ab->jatah_default * 0.4) }} eks</td>
                                <td class="py-2.5 px-3 text-right font-bold text-slate-900">{{ $ab->jatah_default }} eks</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- Tanda Tangan Pengesahan -->
            <div class="mt-8 pt-4 border-t border-slate-200 grid grid-cols-2 text-center text-xs text-slate-800">
                <div>
                    <p class="text-slate-500">Mengetahui,</p>
                    <p class="font-bold text-slate-900 mt-1">Pimpinan Perusahaan / General Manager</p>
                    <div class="h-16"></div>
                    <p class="font-bold underline text-slate-900">( .................................................... )</p>
                </div>
                <div>
                    <p class="text-slate-500">Tasikmalaya, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p class="font-bold text-slate-900 mt-1">Manajer Divisi Sirkulasi</p>
                    <div class="h-16"></div>
                    <p class="font-bold underline text-slate-900">( .................................................... )</p>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
