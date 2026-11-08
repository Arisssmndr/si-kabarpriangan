<x-app-layout>
    <div class="space-y-5">

        <!-- Navigasi Terpadu Pusat Laporan Iklan -->
        @include('pages.iklan.laporan._nav_tabs')

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Laporan Pemuatan Iklan
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Daftar pemuatan iklan HU. Kabar Priangan ({{ $periodeLabel }}).
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('laporan.cetak', request()->query()) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Laporan Pemuatan</span>
                </a>
            </div>
        </div>

        <!-- Filter Periode Fleksibel + Basis Tanggal, Media & Status (Selaras /transaksipriangan) -->
        @php
            ob_start();
        @endphp
        <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Basis Tanggal</label>
            <select name="basis_tanggal" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                <option value="muat" {{ ($basisTanggal ?? 'muat') === 'muat' ? 'selected' : '' }}>Tanggal Muat</option>
                <option value="order" {{ ($basisTanggal ?? 'muat') === 'order' ? 'selected' : '' }}>Tanggal Order</option>
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Saluran Media</label>
            <select name="media" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                <option value="all" {{ $media === 'all' ? 'selected' : '' }}>Semua Media</option>
                <option value="koran" {{ $media === 'koran' ? 'selected' : '' }}>Koran Cetak</option>
                <option value="online" {{ $media === 'online' ? 'selected' : '' }}>Media Online</option>
                <option value="tv" {{ $media === 'tv' ? 'selected' : '' }}>Priangan TV</option>
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Tagihan</label>
            <select name="status" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="lunas" {{ $status === 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="piutang" {{ $status === 'piutang' ? 'selected' : '' }}>Belum Lunas</option>
                <option value="bonus" {{ $status === 'bonus' ? 'selected' : '' }}>Iklan Bonus</option>
            </select>
        </div>
        @php
            $extraFiltersHtml = ob_get_clean();
        @endphp

        @include('pages.iklan.laporan._period_filter', [
            'actionRoute'    => route('laporan.index'),
            'extraFilters'   => $extraFiltersHtml,
            'submitColClass' => 'sm:col-span-2',
        ])

        <!-- 4 KPI Finansial & Operasional Standar Industri (60-30-10 Palette) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Tagihan (Jumlah)</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalOmset, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block font-mono">
                    Brutto: Rp {{ number_format($totalBrutto ?? 0, 0, ',', '.') }}
                </span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kas Masuk (Terbayar)</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">
                    Collection Rate: <span class="font-semibold text-kp-blue-700 font-mono">{{ $rasioLunas ?? 0 }}%</span> ({{ $countLunas }} Lunas)
                </span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sisa Piutang</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">
                    {{ $countPiutang }} Faktur belum lunas
                </span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Volume Pemuatan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalTransaksi) }} <span class="text-xs font-sans font-normal text-slate-400">Order ({{ number_format($totalMili ?? 0, 0, ',', '.') }} mm)</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">
                    Komersial: {{ $orderKomersial ?? 0 }} &bull; Bonus: {{ $orderBonus ?? 0 }}
                </span>
            </div>
        </div>

        <!-- Tabel Rincian Pemuatan Iklan HU. Priangan (Selaras /transaksipriangan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden" x-data="{ showModernCols: true }">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">
                        Laporan Pemuatan Iklan HU. Priangan
                    </h2>
                    <p class="text-xs text-slate-500 font-normal">
                        {{ ($basisTanggal ?? 'muat') === 'muat' ? 'Tanggal Muat' : 'Tanggal Order' }} : {{ $hariTanggal }}
                    </p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                        {{ number_format($totalTransaksi) }} Order ({{ number_format($totalMili ?? 0, 0, ',', '.') }} Mili)
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3 text-center w-10">No</th>
                            <th class="py-3 px-3">No. Order</th>
                            <th class="py-3 px-3" x-show="showModernCols">No. Faktur</th>
                            <th class="py-3 px-3" x-show="showModernCols">Tgl. Muat</th>
                            <th class="py-3 px-3">No. Perkiraan</th>
                            <th class="py-3 px-3">Pemasang</th>
                            <th class="py-3 px-3">Alamat</th>
                            <th class="py-3 px-3">Jenis Iklan</th>
                            <th class="py-3 px-3" x-show="showModernCols">Sales</th>
                            <th class="py-3 px-2.5 text-right">Baris</th>
                            <th class="py-3 px-2.5 text-right">Kolom</th>
                            <th class="py-3 px-2.5 text-right">Displ</th>
                            <th class="py-3 px-3 text-right">Mili</th>
                            <th class="py-3 px-3 text-right">Brutto</th>
                            <th class="py-3 px-3 text-right">Reduksi</th>
                            <th class="py-3 px-3 text-right">Netto</th>
                            <th class="py-3 px-3 text-right">PPn</th>
                            <th class="py-3 px-3 text-right">Jumlah</th>
                            <th class="py-3 px-3 text-right" x-show="showModernCols">Dibayar</th>
                            <th class="py-3 px-3 text-right" x-show="showModernCols">Piutang</th>
                            <th class="py-3 px-3 text-center" x-show="showModernCols">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                        @forelse($items as $idx => $row)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3 text-center text-slate-500 font-normal">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-3 px-3 font-mono font-bold text-kp-blue-700">
                                    {{ $row->no_order }}
                                </td>
                                <td class="py-3 px-3 font-mono text-slate-600 font-normal" x-show="showModernCols">
                                    {{ $row->faktur }}
                                </td>
                                <td class="py-3 px-3 font-mono text-slate-600 font-normal" x-show="showModernCols">
                                    {{ \Carbon\Carbon::parse($row->tanggal_muat ?? $row->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-3 font-mono text-slate-500 font-normal">
                                    {{ $row->no_perkiraan }}
                                </td>
                                <td class="py-3 px-3 font-semibold text-slate-900">
                                    {{ $row->customer }}
                                </td>
                                <td class="py-3 px-3 font-normal text-slate-600 max-w-[200px] truncate" title="{{ $row->alamat }}">
                                    {{ $row->alamat }}
                                </td>
                                <td class="py-3 px-3 font-normal text-slate-700">
                                    {{ $row->paket }}
                                </td>
                                <td class="py-3 px-3 font-normal text-slate-600" x-show="showModernCols">
                                    {{ $row->sales }}
                                </td>
                                <td class="py-3 px-2.5 text-right font-mono font-normal text-slate-600">{{ $row->baris ?? 0 }}</td>
                                <td class="py-3 px-2.5 text-right font-mono font-normal text-slate-600">{{ $row->kolom ?? 0 }}</td>
                                <td class="py-3 px-2.5 text-right font-mono font-normal text-slate-600">{{ $row->displ ?? 0 }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-800">{{ number_format($row->mili ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-700">{{ number_format($row->brutto ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-500">{{ number_format($row->reduksi ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-700">{{ number_format($row->netto ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-600">{{ number_format($row->ppn ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-semibold text-slate-900">{{ number_format($row->total ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-700" x-show="showModernCols">{{ number_format($row->bayar ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-normal text-slate-700" x-show="showModernCols">{{ number_format($row->piutang ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-center" x-show="showModernCols">
                                    @if($row->is_bonus ?? false)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Bonus
                                        </span>
                                    @elseif($row->is_lunas)
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
                                <td colspan="21" class="py-12 text-center text-slate-500 font-normal">
                                    Tidak ada transaksi pemuatan iklan pada periode dan filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($items) > 0)
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                            <tr>
                                <td class="py-3 px-3 font-sans" :colspan="showModernCols ? 9 : 6">
                                    Jumlah...
                                </td>
                                <td class="py-3 px-2.5 text-right">{{ number_format($totalBaris ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-2.5 text-right">{{ number_format($totalKolom ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-2.5 text-right">{{ number_format($totalDispl ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right">{{ number_format($totalMili ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right">{{ number_format($totalBrutto ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right text-slate-600">{{ number_format($totalReduksi ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right">{{ number_format($totalNetto ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right">{{ number_format($totalPpn ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right text-kp-blue-700 font-bold">{{ number_format($totalOmset ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right" x-show="showModernCols">{{ number_format($totalKasMasuk ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right" x-show="showModernCols">{{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</td>
                                <td x-show="showModernCols"></td>
                            </tr>
                            <tr class="border-t border-slate-200 bg-slate-50/60">
                                <td class="py-2.5 px-3 font-sans" :colspan="showModernCols ? 9 : 6">
                                    Jumlah Order...
                                </td>
                                <td class="py-2.5 px-2.5 text-right">{{ number_format($orderBaris ?? 0, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-2.5 text-right">{{ number_format($orderKolom ?? 0, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-2.5 text-right">{{ number_format($orderDispl ?? 0, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-left font-sans text-xs font-normal text-slate-500" :colspan="showModernCols ? 9 : 6">
                                    Total {{ number_format($totalTransaksi) }} Order ({{ $orderKomersial ?? 0 }} Berbayar, {{ $orderBonus ?? 0 }} Bonus)
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
