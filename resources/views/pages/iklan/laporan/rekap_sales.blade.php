<x-app-layout>
    <div class="space-y-5" x-data="{ activeView: '{{ $tab ?? 'all' }}', showModernCols: true }">

        <!-- Navigasi Terpadu Pusat Laporan Iklan -->
        @include('pages.iklan.laporan._nav_tabs')

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Laporan Penjualan Iklan (Semua AE &amp; Per Sales)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Rekapitulasi penjualan iklan seluruh Sektor Perolehan AE &amp; rincian perolehan per Sales/AE ({{ $periodeLabel }}).
                    </p>
                </div>
            </div>

            <!-- 2 Tombol Cetak Resmi Terpisah -->
            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                <a href="{{ route('laporan.rekap-sales.cetak', array_merge(request()->query(), ['mode_cetak' => 'semua_ae'])) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Semua AE</span>
                </a>
                <a href="{{ route('laporan.rekap-sales.cetak', array_merge(request()->query(), ['mode_cetak' => 'per_sales'])) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Per Sales</span>
                </a>
            </div>
        </div>

        <!-- Filter Periode Fleksibel + Sales AE, Media & Tampilan (Selaras /transaksipriangan) -->
        @php
            ob_start();
        @endphp
        <div class="sm:col-span-4 lg:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sales / AE</label>
            <select name="sales_ae_id" class="w-full h-11 pl-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                <option value="all" {{ $salesAeId === 'all' ? 'selected' : '' }}>Semua Sales / AE</option>
                @foreach($salesAes as $ae)
                    <option value="{{ $ae->id }}" {{ (string)$salesAeId === (string)$ae->id ? 'selected' : '' }}>
                        [{{ $ae->kode_ae }}] {{ $ae->nama_ae }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-4 lg:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Media</label>
            <select name="media" class="w-full h-11 pl-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                <option value="koran" {{ ($media ?? 'koran') === 'koran' ? 'selected' : '' }}>Koran Cetak</option>
                <option value="all" {{ ($media ?? 'koran') === 'all' ? 'selected' : '' }}>Semua Media (3 Unit)</option>
                <option value="online" {{ ($media ?? 'koran') === 'online' ? 'selected' : '' }}>Media Online</option>
                <option value="tv" {{ ($media ?? 'koran') === 'tv' ? 'selected' : '' }}>Priangan TV</option>
            </select>
        </div>
        <div class="sm:col-span-4 lg:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tampilan Tabel</label>
            <select name="tab" x-model="activeView" class="w-full h-11 pl-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                <option value="all">Semua Tabel</option>
                <option value="semua_ae">Penjualan Semua AE</option>
                <option value="per_sales">Penjualan Per Sales</option>
            </select>
        </div>
        @php
            $extraFiltersHtml = ob_get_clean();
        @endphp

        @include('pages.iklan.laporan._period_filter', [
            'actionRoute'    => route('laporan.rekap-sales'),
            'extraFilters'   => $extraFiltersHtml,
            'submitColClass' => 'sm:col-span-2',
        ])

        <!-- 4 KPI Ringkasan Perolehan Sales / AE (60-30-10 Palette) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Penjualan (Jumlah)</span>
                <p class="text-2xl font-bold font-mono text-kp-blue-700 mt-1 tabular-nums">
                    Rp {{ number_format($grandTotal['total_tagihan'] ?? 0, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block font-mono">
                    Netto: Rp {{ number_format($grandTotal['total_netto'] ?? 0, 0, ',', '.') }}
                </span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kas Masuk vs Sisa Piutang</span>
                <p class="text-2xl font-bold font-mono text-slate-900 mt-1 tabular-nums">
                    Rp {{ number_format($grandTotal['total_bayar'] ?? 0, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block font-mono">
                    Piutang: Rp {{ number_format($grandTotal['total_piutang'] ?? 0, 0, ',', '.') }} ({{ $grandTotal['rasio_lunas'] ?? 0 }}% Lunas)
                </span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Volume &amp; Order</span>
                <p class="text-2xl font-bold font-mono text-slate-900 mt-1 tabular-nums">
                    {{ number_format($grandTotal['count'] ?? 0, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Order ({{ number_format($grandTotal['total_mili'] ?? 0, 0, ',', '.') }} mmk)</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block font-mono">
                    Brs: {{ $grandTotal['order_brs'] ?? 0 }} &bull; Klm: {{ $grandTotal['order_klm'] ?? 0 }} &bull; Dsp: {{ $grandTotal['order_dsp'] ?? 0 }}
                </span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Insentif &amp; Komisi AE</span>
                <p class="text-2xl font-bold font-mono text-slate-900 mt-1 tabular-nums">
                    Rp {{ number_format(($grandTotal['total_insentif'] ?? 0) + ($grandTotal['total_komisi'] ?? 0), 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block font-mono">
                    Insentif: Rp {{ number_format($grandTotal['total_insentif'] ?? 0, 0, ',', '.') }} &bull; Komisi: Rp {{ number_format($grandTotal['total_komisi'] ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- ======================================================================= -->
        <!-- TABEL 1: LAPORAN PENJUALAN IKLAN SEMUA AE (SEKTOR PEROLEHAN)            -->
        <!-- ======================================================================= -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden"
             x-show="activeView === 'all' || activeView === 'semua_ae'">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">
                        Laporan Penjualan Iklan Penerbitan HU. Priangan — Semua AE (Sektor Perolehan)
                    </h2>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Hari / Tanggal : {{ $hariTanggal }}</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    Total Order: {{ number_format($grandTotal['count'] ?? 0, 0, ',', '.') }} Iklan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th rowspan="2" class="py-3 px-3 text-center w-10 border-r border-slate-200">No</th>
                            <th rowspan="2" class="py-3 px-3 border-r border-slate-200">Kode AE</th>
                            <th rowspan="2" class="py-3 px-3 border-r border-slate-200">Sektor Perolehan (Sales / AE)</th>
                            <th colspan="4" class="py-1.5 px-3 text-center border-b border-r border-slate-200 bg-slate-100/70">Order</th>
                            <th rowspan="2" class="py-3 px-2.5 text-right">Baris</th>
                            <th rowspan="2" class="py-3 px-2.5 text-right">Kolom (mm)</th>
                            <th rowspan="2" class="py-3 px-2.5 text-right">Displ (mm)</th>
                            <th rowspan="2" class="py-3 px-3 text-right border-r border-slate-200">Total Mili</th>
                            <th rowspan="2" class="py-3 px-3 text-right">Brutto</th>
                            <th rowspan="2" class="py-3 px-3 text-right">Reduksi</th>
                            <th rowspan="2" class="py-3 px-3 text-right">Netto</th>
                            <th rowspan="2" class="py-3 px-3 text-right">PPn</th>
                            <th rowspan="2" class="py-3 px-3 text-right border-r border-slate-200">Jumlah</th>
                            <th rowspan="2" class="py-3 px-3 text-right">Insentif</th>
                            <th rowspan="2" class="py-3 px-3 text-right border-r border-slate-200">Komisi</th>
                            <th rowspan="2" class="py-3 px-3 text-right" x-show="showModernCols">Dibayar</th>
                            <th rowspan="2" class="py-3 px-3 text-right" x-show="showModernCols">Piutang</th>
                            <th rowspan="2" class="py-3 px-2.5 text-right" x-show="showModernCols">% Porsi</th>
                        </tr>
                        <tr class="bg-slate-100/40">
                            <th class="py-1.5 px-2 text-right">Brs</th>
                            <th class="py-1.5 px-2 text-right">Klm</th>
                            <th class="py-1.5 px-2 text-right">Dsp</th>
                            <th class="py-1.5 px-2.5 text-right border-r border-slate-200">Jml</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap">
                        @php $noAe = 1; @endphp
                        @foreach($rekapPerAe as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3 text-center text-slate-500 font-normal border-r border-slate-100">{{ $noAe++ }}</td>
                                <td class="py-3 px-3 font-mono font-bold text-kp-blue-700 border-r border-slate-100">
                                    {{ $item['ae']->kode_ae }}
                                </td>
                                <td class="py-3 px-3 font-semibold text-slate-900 border-r border-slate-100">
                                    <a href="{{ route('laporan.rekap-sales', array_merge(request()->query(), ['sales_ae_id' => $item['ae']->id, 'tab' => 'per_sales'])) }}"
                                       class="hover:text-kp-blue-700 hover:underline">
                                        {{ $item['ae']->nama_ae }}
                                    </a>
                                </td>
                                <td class="py-3 px-2 text-right font-mono font-normal text-slate-600">{{ number_format($item['order_brs'], 0, ',', '.') }}</td>
                                <td class="py-3 px-2 text-right font-mono font-normal text-slate-600">{{ number_format($item['order_klm'], 0, ',', '.') }}</td>
                                <td class="py-3 px-2 text-right font-mono font-normal text-slate-600">{{ number_format($item['order_dsp'], 0, ',', '.') }}</td>
                                <td class="py-3 px-2.5 text-right font-mono font-normal text-slate-800 border-r border-slate-100">{{ number_format($item['count'], 0, ',', '.') }}</td>
                                <td class="py-3 px-2.5 text-right font-mono font-normal text-slate-600">{{ number_format($item['vol_baris'], 0, ',', '.') }}</td>
                                <td class="py-3 px-2.5 text-right font-mono font-normal text-slate-600">{{ number_format($item['vol_kolom_mm'], 0, ',', '.') }}</td>
                                <td class="py-3 px-2.5 text-right font-mono font-normal text-slate-600">{{ number_format($item['vol_displ_mm'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-800 border-r border-slate-100">{{ number_format($item['total_mili'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-700">{{ number_format($item['total_bruto'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-500">{{ number_format($item['total_reduksi'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-700">{{ number_format($item['total_netto'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-600">{{ number_format($item['total_ppn'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-semibold text-slate-900 border-r border-slate-100">{{ number_format($item['total_tagihan'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-700">{{ number_format($item['total_insentif'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-700 border-r border-slate-100">{{ number_format($item['total_komisi'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-700" x-show="showModernCols">{{ number_format($item['total_bayar'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-700" x-show="showModernCols">{{ number_format($item['total_piutang'], 0, ',', '.') }}</td>
                                <td class="py-3 px-2.5 text-right font-mono font-normal text-kp-blue-700" x-show="showModernCols">{{ number_format($item['kontribusi_persen'] ?? 0, 1, ',', '.') }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900">
                        <tr>
                            <td colspan="3" class="py-3 px-3 font-sans border-r border-slate-200">Jumlah...</td>
                            <td class="py-3 px-2 text-right">{{ number_format($grandTotal['order_brs'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-2 text-right">{{ number_format($grandTotal['order_klm'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-2 text-right">{{ number_format($grandTotal['order_dsp'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-2.5 text-right border-r border-slate-200">{{ number_format($grandTotal['count'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-2.5 text-right">{{ number_format($grandTotal['vol_baris'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-2.5 text-right">{{ number_format($grandTotal['vol_kolom_mm'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-2.5 text-right">{{ number_format($grandTotal['vol_displ_mm'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right border-r border-slate-200">{{ number_format($grandTotal['total_mili'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right">{{ number_format($grandTotal['total_bruto'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right text-slate-600">{{ number_format($grandTotal['total_reduksi'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right">{{ number_format($grandTotal['total_netto'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right">{{ number_format($grandTotal['total_ppn'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right border-r border-slate-200 text-kp-blue-700 font-bold">{{ number_format($grandTotal['total_tagihan'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right">{{ number_format($grandTotal['total_insentif'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right border-r border-slate-200">{{ number_format($grandTotal['total_komisi'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right" x-show="showModernCols">{{ number_format($grandTotal['total_bayar'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right" x-show="showModernCols">{{ number_format($grandTotal['total_piutang'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-2.5 text-right text-kp-blue-700" x-show="showModernCols">100,0%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- ======================================================================= -->
        <!-- TABEL 2: LAPORAN PENJUALAN IKLAN PER SALES / AE (RINCIAN PEMASANG)      -->
        <!-- ======================================================================= -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden"
             x-show="activeView === 'all' || activeView === 'per_sales'">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">
                        Laporan Penjualan Iklan Penerbitan HU. Priangan — Perolehan : {{ $selectedAe ? strtoupper($selectedAe->nama_ae) : 'Semua Sales / AE' }}
                    </h2>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Hari / Tanggal : {{ $hariTanggal }}</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ count($rincianList) }} Pemasang
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3 text-center w-10">No</th>
                            <th class="py-3 px-3" x-show="showModernCols">No. Faktur</th>
                            <th class="py-3 px-3" x-show="showModernCols">Tgl. Muat</th>
                            <th class="py-3 px-3">Nama Pemasang</th>
                            <th class="py-3 px-3">Jenis Iklan</th>
                            @if(!$selectedAe)
                                <th class="py-3 px-3">Kode AE</th>
                                <th class="py-3 px-3">Sales / AE</th>
                            @endif
                            <th class="py-3 px-2.5 text-right">Brs</th>
                            <th class="py-3 px-2.5 text-right">Klm</th>
                            <th class="py-3 px-2.5 text-right">Mm</th>
                            <th class="py-3 px-3 text-right border-r border-slate-200">Jml MM</th>
                            <th class="py-3 px-3 text-right">Harga Iklan</th>
                            <th class="py-3 px-2.5 text-right">Diskon</th>
                            <th class="py-3 px-3 text-right" x-show="showModernCols">Reduksi</th>
                            <th class="py-3 px-3 text-right">Netto</th>
                            <th class="py-3 px-3 text-right">PPn</th>
                            <th class="py-3 px-3 text-right border-r border-slate-200">Jumlah</th>
                            <th class="py-3 px-3 text-right" x-show="showModernCols">Dibayar</th>
                            <th class="py-3 px-3 text-right" x-show="showModernCols">Piutang</th>
                            <th class="py-3 px-3 text-center" x-show="showModernCols">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap">
                        @php
                            $sumBrs = 0;
                            $sumJmlMm = 0;
                            $sumHarga = 0;
                            $sumReduksi = 0;
                            $sumNetto = 0;
                            $sumPpn = 0;
                            $sumJumlah = 0;
                            $sumBayar = 0;
                            $sumPiutang = 0;
                        @endphp
                        @forelse($rincianList as $idx => $r)
                            @php
                                $sumBrs += $r->brs;
                                $sumJmlMm += $r->jml_mm;
                                $sumHarga += $r->harga_iklan;
                                $sumReduksi += $r->reduksi;
                                $sumNetto += $r->netto;
                                $sumPpn += $r->ppn;
                                $sumJumlah += $r->jumlah;
                                $sumBayar += $r->bayar;
                                $sumPiutang += $r->piutang;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3 px-3 font-mono font-bold text-kp-blue-700" x-show="showModernCols">{{ $r->nofaktur }}</td>
                                <td class="py-3 px-3 font-mono text-slate-600 font-normal" x-show="showModernCols">{{ \Carbon\Carbon::parse($r->tanggal_muat ?? $r->tanggal)->format('d/m/Y') }}</td>
                                <td class="py-3 px-3 font-semibold text-slate-900">{{ $r->nama_pemasang }}</td>
                                <td class="py-3 px-3 font-normal text-slate-700">{{ $r->jenis_iklan }}</td>
                                @if(!$selectedAe)
                                    <td class="py-3 px-3 font-mono font-bold text-kp-blue-700">{{ $r->sales_kode }}</td>
                                    <td class="py-3 px-3 font-normal text-slate-600">{{ $r->sales_nama }}</td>
                                @endif
                                <td class="py-3 px-2.5 text-right font-mono font-normal text-slate-600">{{ $r->brs }}</td>
                                <td class="py-3 px-2.5 text-right font-mono font-normal text-slate-600">{{ $r->klm }}</td>
                                <td class="py-3 px-2.5 text-right font-mono font-normal text-slate-600">{{ $r->mm }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-800 border-r border-slate-100">{{ number_format($r->jml_mm, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-700">{{ number_format($r->harga_iklan, 0, ',', '.') }}</td>
                                <td class="py-3 px-2.5 text-right font-mono font-normal text-slate-500">{{ number_format($r->diskon_persen, 2, ',', '.') }}%</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-500" x-show="showModernCols">{{ number_format($r->reduksi, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-700">{{ number_format($r->netto, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-600">{{ number_format($r->ppn, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-semibold text-slate-900 border-r border-slate-100">{{ number_format($r->jumlah, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-700" x-show="showModernCols">{{ number_format($r->bayar, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-700" x-show="showModernCols">{{ number_format($r->piutang, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-center" x-show="showModernCols">
                                    @if($r->is_bonus ?? false)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Bonus
                                        </span>
                                    @elseif($r->is_lunas)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Lunas
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            Piutang
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="py-12 text-center text-slate-500 font-normal">
                                    Tidak ada rincian pemasangan iklan pada filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($rincianList) > 0)
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                            <tr>
                                <td class="py-3 px-3 font-sans" :colspan="showModernCols ? {{ !$selectedAe ? 7 : 5 }} : {{ !$selectedAe ? 5 : 3 }}">
                                    Jumlah...
                                </td>
                                <td class="py-3 px-2.5 text-right">{{ number_format($sumBrs, 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                                <td class="py-3 px-3 text-right border-r border-slate-200">{{ number_format($sumJmlMm, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right">{{ number_format($sumHarga, 0, ',', '.') }}</td>
                                <td></td>
                                <td class="py-3 px-3 text-right text-slate-600" x-show="showModernCols">{{ number_format($sumReduksi, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right">{{ number_format($sumNetto, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right">{{ number_format($sumPpn, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right text-kp-blue-700 font-bold border-r border-slate-100">{{ number_format($sumJumlah, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right" x-show="showModernCols">{{ number_format($sumBayar, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right" x-show="showModernCols">{{ number_format($sumPiutang, 0, ',', '.') }}</td>
                                <td x-show="showModernCols"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
