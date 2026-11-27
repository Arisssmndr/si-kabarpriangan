@php
    $isPrint = $isPrintMode ?? false;
    $thClass = $isPrint ? 'py-2 px-2.5 font-bold uppercase text-[10px]' : 'py-3.5 px-3.5 font-bold uppercase text-xs text-white tracking-wider';
    $tdClass = $isPrint ? 'py-1.5 px-2.5 text-[10px]' : 'py-3.5 px-3.5';
    $tfClass = $isPrint ? 'py-2 px-2.5 font-bold text-[10px]' : 'py-3.5 px-3.5';
    $tableClass = $isPrint
        ? 'w-full text-left text-[10px] border-collapse no-brand-table'
        : 'w-full text-left text-sm border-collapse print:text-[10px]';
    $theadClass = $isPrint
        ? 'bg-slate-100 border-y border-slate-400 text-slate-900 font-bold uppercase whitespace-nowrap'
        : 'bg-[#0E527B] text-white border-b border-[#0E527B] font-bold text-xs uppercase tracking-wider whitespace-nowrap';
    $cardWrapClass = $isPrint
        ? 'bg-white overflow-visible'
        : 'bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none';
    $kpiGridClass = $isPrint
        ? 'grid grid-cols-4 gap-2 mb-3'
        : 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:grid print:grid-cols-4 print:gap-2 print:mb-3';
    $kpiCardClass = $isPrint
        ? 'border border-slate-300 p-2 bg-slate-50/50'
        : 'bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none';
@endphp

@if($tab === 'sub-agen')
    @php
        $totSubKirim = $subAgenRows->sum('kirim');
        $totSubKons = $subAgenRows->sum('konsinyasi');
        $totSubRetur = $subAgenRows->sum('retur');
        $totSubNett = $subAgenRows->sum('nett');
        $totSubTagihan = $subAgenRows->sum('nilai_tagihan');
    @endphp

    <div class="{{ $kpiGridClass }}">
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Sub Agen</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ $subAgenRows->count() }} <span class="text-xs font-sans font-normal text-slate-400">Mitra</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kirim</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ number_format($totSubKirim, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Koran Terjual (Retur: {{ number_format($totSubRetur, 0, ',', '.') }})</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ number_format($totSubNett, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Tagihan</span>
            <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm text-slate-900' : 'print:text-sm' }}">Rp {{ number_format($totSubTagihan, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="{{ $cardWrapClass }}">
        @if(!$isPrint)
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 print:hidden">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Laporan Sub Agen &amp; Kios</h2>
                    <p class="text-xs text-slate-500">{{ $periodeLabel }} ({{ $daysInRange }} hari)</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ $subAgenRows->count() }} Mitra
                </span>
            </div>
        @endif
        <div class="{{ $isPrint ? '' : 'overflow-x-auto min-h-[260px]' }}">
            <table class="{{ $tableClass }}">
                <thead class="{{ $theadClass }}">
                    <tr>
                        <th class="{{ $thClass }} text-center w-10">No</th>
                        <th class="{{ $thClass }}">Kode Agen</th>
                        <th class="{{ $thClass }}">Nama Sub Agen</th>
                        <th class="{{ $thClass }}">Induk Agen</th>
                        <th class="{{ $thClass }}">Wilayah</th>
                        <th class="{{ $thClass }}">Rute Jurusan</th>
                        <th class="{{ $thClass }} text-right">Total Kirim</th>
                        <th class="{{ $thClass }} text-right">Konsinyasi</th>
                        <th class="{{ $thClass }} text-right">Retur</th>
                        <th class="{{ $thClass }} text-right">Koran Terjual</th>
                        <th class="{{ $thClass }} text-right">Total Tagihan</th>
                        @if(!$isPrint)
                            <th class="print:hidden {{ $thClass }} text-center w-10"></th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                    @forelse($subAgenRows as $idx => $sub)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="{{ $tdClass }} text-center text-slate-500">{{ $idx + 1 }}</td>
                            <td class="{{ $tdClass }} font-mono font-bold text-kp-blue-700">{{ $sub->kode_agen }}</td>
                            <td class="{{ $tdClass }} font-semibold text-slate-900">{{ $sub->nama_agen }}</td>
                            <td class="{{ $tdClass }} text-slate-600">{{ $sub->induk_agen }}</td>
                            <td class="{{ $tdClass }}">{{ $sub->wilayah_nama }}</td>
                            <td class="{{ $tdClass }} font-mono">{{ $sub->jurusan_kode }}</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($sub->kirim, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($sub->konsinyasi, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($sub->retur, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($sub->nett, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-kp-blue-700 tabular-nums">Rp {{ number_format($sub->nilai_tagihan, 0, ',', '.') }}</td>
                            @if(!$isPrint)
                                <td class="print:hidden {{ $tdClass }} text-center" x-data="{ open: false }" :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <button type="button" @click="open = !open" class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer" title="Pilihan Aksi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5" /><circle cx="12" cy="12" r="1.5" /><circle cx="12" cy="19" r="1.5" /></svg>
                                        </button>
                                        <div x-cloak x-show="open" x-transition.duration.150ms class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 text-left">
                                            <a href="{{ route('sirkulasi.tabel.agen', ['search' => $sub->kode_agen]) }}" @click="open = false" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <span>Detail Agen</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isPrint ? 11 : 12 }}" class="py-10 text-center text-slate-500 text-sm">Tidak ada data sub agen yang sesuai dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                    <tr>
                        <td colspan="6" class="{{ $tfClass }} text-right font-sans uppercase text-xs font-bold text-slate-700">Total Sub Agen:</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totSubKirim, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totSubKons, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totSubRetur, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totSubNett, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right text-kp-blue-700 font-bold">Rp {{ number_format($totSubTagihan, 0, ',', '.') }}</td>
                        @if(!$isPrint)<td class="print:hidden"></td>@endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@elseif($tab === 'rekap-terbit-bulan')
    @php
        $totDailyKirim = collect($dailyRows)->sum('kirim');
        $totDailyLangganan = collect($dailyRows)->sum('langganan');
        $totDailyKonsinyasi = collect($dailyRows)->sum('konsinyasi');
        $totDailyGratis = collect($dailyRows)->sum('gratis');
        $totDailyPengenalan = collect($dailyRows)->sum('pengenalan');
        $totDailyInsidentil = collect($dailyRows)->sum('insidentil');
        $totDailyRetur = collect($dailyRows)->sum('retur');
        $totDailyNett = collect($dailyRows)->sum('nett');
    @endphp

    <div class="{{ $kpiGridClass }}">
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jumlah Hari</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ count($dailyRows) }} <span class="text-xs font-sans font-normal text-slate-400">Hari</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kirim</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ number_format($totDailyKirim, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Retur</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ number_format($totDailyRetur, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Koran Terjual</span>
            <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm text-slate-900' : 'print:text-sm' }}">{{ number_format($totDailyNett, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
    </div>

    <div class="{{ $cardWrapClass }}">
        @if(!$isPrint)
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 print:hidden">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Rekap Penjualan Harian</h2>
                    <p class="text-xs text-slate-500">{{ $periodeLabel }} ({{ count($dailyRows) }} edisi)</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ $agenList->count() }} Agen Aktif
                </span>
            </div>
        @endif
        <div class="{{ $isPrint ? '' : 'overflow-x-auto min-h-[260px]' }}">
            <table class="{{ $tableClass }}">
                <thead class="{{ $theadClass }}">
                    <tr>
                        <th class="{{ $thClass }} text-center w-10">No</th>
                        <th class="{{ $thClass }}">Tanggal</th>
                        <th class="{{ $thClass }}">Hari</th>
                        <th class="{{ $thClass }}">Status</th>
                        <th class="{{ $thClass }} text-right">Langganan</th>
                        <th class="{{ $thClass }} text-right">Konsinyasi</th>
                        <th class="{{ $thClass }} text-right">Gratis</th>
                        <th class="{{ $thClass }} text-right">Pengenalan</th>
                        <th class="{{ $thClass }} text-right">Insidentil</th>
                        <th class="{{ $thClass }} text-right">Total Kirim</th>
                        <th class="{{ $thClass }} text-right">Retur</th>
                        <th class="{{ $thClass }} text-right">Koran Terjual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                    @forelse($dailyRows as $idx => $dr)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="{{ $tdClass }} text-center text-slate-500">{{ $idx + 1 }}</td>
                            <td class="{{ $tdClass }} font-mono font-bold text-kp-blue-700">{{ $dr->tanggal_fmt }}</td>
                            <td class="{{ $tdClass }} font-medium text-slate-900">{{ $dr->hari }}</td>
                            <td class="{{ $tdClass }}">
                                <span class="inline-block px-2 py-0.5 rounded-md text-xs font-semibold border {{ $dr->status_data === 'Tersimpan' ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                    {{ $dr->status_data }}
                                </span>
                            </td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($dr->langganan, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($dr->konsinyasi, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($dr->gratis, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($dr->pengenalan, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($dr->insidentil, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($dr->kirim, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($dr->retur, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono font-bold text-kp-blue-700 tabular-nums">{{ number_format($dr->nett, 0, ',', '.') }} eks</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="py-10 text-center text-slate-500 text-sm">Tidak ada data terbit pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                    <tr>
                        <td colspan="4" class="{{ $tfClass }} text-right font-sans uppercase text-xs font-bold text-slate-700">Total Periode ({{ count($dailyRows) }} Edisi):</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totDailyLangganan, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totDailyKonsinyasi, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totDailyGratis, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totDailyPengenalan, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totDailyInsidentil, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totDailyKirim, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totDailyRetur, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right text-kp-blue-700 font-bold">{{ number_format($totDailyNett, 0, ',', '.') }} eks</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@elseif($tab === 'rekap-wilayah')
    @php
        $totalTitikAgen = $wilayahRekapRows->sum('agen_terfilter');
        $totalRataHarian = $wilayahRekapRows->sum('rata_harian');
        $totalKirimWil = $wilayahRekapRows->sum('kirim');
        $totalReturWil = $wilayahRekapRows->sum('retur');
        $totalNettWil = $wilayahRekapRows->sum('nett');
        $totalOmsetWil = $wilayahRekapRows->sum('nilai_omset');
    @endphp

    <div class="{{ $kpiGridClass }}">
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Wilayah</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ $wilayahRekapRows->count() }} <span class="text-xs font-sans font-normal text-slate-400">Wilayah</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Agen</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ $totalTitikAgen }} <span class="text-xs font-sans font-normal text-slate-400">Agen</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kirim (Rata-rata {{ number_format($totalRataHarian, 0, ',', '.') }}/hari)</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ number_format($totalKirimWil, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Penjualan</span>
            <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm text-slate-900' : 'print:text-sm' }}">Rp {{ number_format($totalOmsetWil, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="{{ $cardWrapClass }}">
        @if(!$isPrint)
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 print:hidden">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Laporan Penjualan per Wilayah</h2>
                    <p class="text-xs text-slate-500">{{ $periodeLabel }} ({{ $daysInRange }} hari)</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ $wilayahRekapRows->count() }} Zona
                </span>
            </div>
        @endif
        <div class="{{ $isPrint ? '' : 'overflow-x-auto min-h-[260px]' }}">
            <table class="{{ $tableClass }}">
                <thead class="{{ $theadClass }}">
                    <tr>
                        <th class="{{ $thClass }} text-center w-14">Kode</th>
                        <th class="{{ $thClass }}">Wilayah</th>
                        <th class="{{ $thClass }}">Tipe</th>
                        <th class="{{ $thClass }} text-center">Jumlah Agen</th>
                        <th class="{{ $thClass }} text-right">Rata-rata / Hari</th>
                        <th class="{{ $thClass }} text-right">Total Kirim</th>
                        <th class="{{ $thClass }} text-right">Retur</th>
                        <th class="{{ $thClass }} text-right">Koran Terjual</th>
                        <th class="{{ $thClass }} text-right">Total Penjualan</th>
                        @if(!$isPrint)<th class="print:hidden {{ $thClass }} text-center w-10"></th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                    @foreach($wilayahRekapRows as $wil)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="{{ $tdClass }} text-center font-mono font-bold text-kp-blue-700">{{ $wil->kode }}</td>
                            <td class="{{ $tdClass }} font-semibold text-slate-900">{{ $wil->nama }}</td>
                            <td class="{{ $tdClass }}">
                                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $wil->tipe === 'Komersil' ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                    {{ $wil->tipe }}
                                </span>
                            </td>
                            <td class="{{ $tdClass }} text-center font-mono font-semibold text-slate-900">{{ $wil->agen_terfilter }} agen</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($wil->rata_harian, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($wil->kirim, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($wil->retur, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($wil->nett, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-kp-blue-700 tabular-nums">Rp {{ number_format($wil->nilai_omset, 0, ',', '.') }}</td>
                            @if(!$isPrint)
                                <td class="print:hidden {{ $tdClass }} text-center" x-data="{ open: false }" :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <button type="button" @click="open = !open" class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer" title="Pilihan Aksi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5" /><circle cx="12" cy="12" r="1.5" /><circle cx="12" cy="19" r="1.5" /></svg>
                                        </button>
                                        <div x-cloak x-show="open" x-transition.duration.150ms class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 text-left">
                                            <a href="{{ route('sirkulasi.tabel.agen', ['wilayah' => $wil->kode]) }}" @click="open = false" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <span>Lihat Daftar Agen</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                    <tr>
                        <td colspan="3" class="{{ $tfClass }} text-right font-sans uppercase text-xs font-bold text-slate-700">Total Akumulasi:</td>
                        <td class="{{ $tfClass }} text-center">{{ $totalTitikAgen }} agen</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totalRataHarian, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totalKirimWil, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totalReturWil, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totalNettWil, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right text-kp-blue-700 font-bold">Rp {{ number_format($totalOmsetWil, 0, ',', '.') }}</td>
                        @if(!$isPrint)<td class="print:hidden"></td>@endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@elseif($tab === 'rekap-jurusan')
    @php
        $totalAgenJurusan = $jurusanRekapRows->sum('total_agen');
        $totalRataJur = $jurusanRekapRows->sum('rata_harian');
        $totalKirimJur = $jurusanRekapRows->sum('kirim');
        $totalReturJur = $jurusanRekapRows->sum('retur');
        $totalNettJur = $jurusanRekapRows->sum('nett');
    @endphp

    <div class="{{ $kpiGridClass }}">
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Rute</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ $jurusanRekapRows->count() }} <span class="text-xs font-sans font-normal text-slate-400">Rute</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Agen</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ $totalAgenJurusan }} <span class="text-xs font-sans font-normal text-slate-400">Agen</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Rata-rata / Hari</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ number_format($totalRataJur, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kirim</span>
            <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm text-slate-900' : 'print:text-sm' }}">{{ number_format($totalKirimJur, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
    </div>

    <div class="{{ $cardWrapClass }}">
        @if(!$isPrint)
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 print:hidden">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Laporan per Rute Jurusan</h2>
                    <p class="text-xs text-slate-500">{{ $periodeLabel }} ({{ $daysInRange }} hari)</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ $jurusanRekapRows->count() }} Rute
                </span>
            </div>
        @endif
        <div class="{{ $isPrint ? '' : 'overflow-x-auto min-h-[260px]' }}">
            <table class="{{ $tableClass }}">
                <thead class="{{ $theadClass }}">
                    <tr>
                        <th class="{{ $thClass }} text-center w-14">Kode Rute</th>
                        <th class="{{ $thClass }}">Nama Rute Jurusan</th>
                        <th class="{{ $thClass }}">Tujuan</th>
                        <th class="{{ $thClass }} text-center">Jam Berangkat</th>
                        <th class="{{ $thClass }} text-center">Jumlah Agen</th>
                        <th class="{{ $thClass }} text-right">Rata-rata / Hari</th>
                        <th class="{{ $thClass }} text-right">Total Kirim</th>
                        <th class="{{ $thClass }} text-right">Retur</th>
                        <th class="{{ $thClass }} text-right">Koran Terjual</th>
                        @if(!$isPrint)<th class="print:hidden {{ $thClass }} text-center w-10"></th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                    @foreach($jurusanRekapRows as $jur)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="{{ $tdClass }} text-center font-mono font-bold text-kp-blue-700">{{ $jur->kode }}</td>
                            <td class="{{ $tdClass }} font-semibold text-slate-900">{{ $jur->nama_jurusan }}</td>
                            <td class="{{ $tdClass }} text-slate-600 max-w-[240px] truncate" title="{{ $jur->titik_tujuan }}">{{ $jur->titik_tujuan ?: '-' }}</td>
                            <td class="{{ $tdClass }} text-center font-mono">{{ $jur->jam_berangkat }}</td>
                            <td class="{{ $tdClass }} text-center font-mono font-semibold text-slate-900">{{ $jur->total_agen }} agen</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($jur->rata_harian, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-kp-blue-700 tabular-nums">{{ number_format($jur->kirim, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($jur->retur, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($jur->nett, 0, ',', '.') }} eks</td>
                            @if(!$isPrint)
                                <td class="print:hidden {{ $tdClass }} text-center" x-data="{ open: false }" :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <button type="button" @click="open = !open" class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer" title="Pilihan Aksi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5" /><circle cx="12" cy="12" r="1.5" /><circle cx="12" cy="19" r="1.5" /></svg>
                                        </button>
                                        <div x-cloak x-show="open" x-transition.duration.150ms class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 text-left">
                                            <a href="{{ route('sirkulasi.cetak.nota-distribusi', ['jurusan' => $jur->kode]) }}" @click="open = false" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <span>Surat Jalan Rute</span>
                                            </a>
                                            <a href="{{ route('sirkulasi.tabel.agen', ['jurusan' => $jur->kode]) }}" @click="open = false" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <span>Lihat Agen Rute</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                    <tr>
                        <td colspan="4" class="{{ $tfClass }} text-right font-sans uppercase text-xs font-bold text-slate-700">Total {{ $jurusanRekapRows->count() }} Jurusan:</td>
                        <td class="{{ $tfClass }} text-center">{{ $totalAgenJurusan }} agen</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totalRataJur, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right text-kp-blue-700 font-bold">{{ number_format($totalKirimJur, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totalReturJur, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totalNettJur, 0, ',', '.') }} eks</td>
                        @if(!$isPrint)<td class="print:hidden"></td>@endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@elseif($tab === 'penerimaan-koran')
    @php
        $totPenKirim = $penerimaanRows->sum('kirim');
        $totPenRetur = $penerimaanRows->sum('retur');
        $totPenNett = $penerimaanRows->sum('nett');
        $totPenTagihan = $penerimaanRows->sum('tagihan_bersih');
        $totPenTerbayar = $penerimaanRows->sum('terbayar');
        $totPenPiutang = $penerimaanRows->sum('sisa_piutang');
    @endphp

    <div class="{{ $kpiGridClass }}">
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Koran Terjual</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ number_format($totPenNett, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Tagihan</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">Rp {{ number_format($totPenTagihan, 0, ',', '.') }}</p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sudah Dibayar</span>
            <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm text-slate-900' : 'print:text-sm' }}">Rp {{ number_format($totPenTerbayar, 0, ',', '.') }}</p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sisa Tagihan</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">Rp {{ number_format($totPenPiutang, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="{{ $cardWrapClass }}">
        @if(!$isPrint)
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 print:hidden">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Laporan Penerimaan Koran</h2>
                    <p class="text-xs text-slate-500">
                        {{ $periodeLabel }} &bull; Basis: <span class="font-semibold text-slate-700">{{ $sumberDataPenerimaan ?? 'Faktur Resmi' }}</span>
                        @if($modePeriode === 'harian' || $modePeriode === 'mingguan')
                            <span class="text-amber-700 font-medium">(Konsolidasi Faktur Edisi Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $bulanYm)->translatedFormat('F Y') }})</span>
                        @endif
                    </p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ $penerimaanRows->count() }} Data Agen
                </span>
            </div>
        @endif
        <div class="{{ $isPrint ? '' : 'overflow-x-auto min-h-[260px]' }}">
            <table class="{{ $tableClass }}">
                <thead class="{{ $theadClass }}">
                    <tr>
                        <th class="{{ $thClass }} text-center w-10">No</th>
                        <th class="{{ $thClass }}">No. Faktur</th>
                        <th class="{{ $thClass }}">Kode Agen</th>
                        <th class="{{ $thClass }}">Nama Agen</th>
                        <th class="{{ $thClass }}">Wilayah</th>
                        <th class="{{ $thClass }} text-right">Total Kirim</th>
                        <th class="{{ $thClass }} text-right">Retur</th>
                        <th class="{{ $thClass }} text-right">Koran Terjual</th>
                        <th class="{{ $thClass }} text-right">Total Tagihan</th>
                        <th class="{{ $thClass }} text-right">Sudah Dibayar</th>
                        <th class="{{ $thClass }} text-right">Sisa Tagihan</th>
                        <th class="{{ $thClass }} text-center">Status Bayar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                    @forelse($penerimaanRows as $idx => $pr)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="{{ $tdClass }} text-center text-slate-500">{{ $idx + 1 }}</td>
                            <td class="{{ $tdClass }} font-mono font-semibold text-kp-blue-700">{{ $pr->no_faktur }}</td>
                            <td class="{{ $tdClass }} font-mono">{{ $pr->kode_agen }}</td>
                            <td class="{{ $tdClass }} font-semibold text-slate-900">{{ $pr->nama_agen }}</td>
                            <td class="{{ $tdClass }}">{{ $pr->wilayah_nama }}</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($pr->kirim, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($pr->retur, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($pr->nett, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-slate-900 tabular-nums">Rp {{ number_format($pr->tagihan_bersih, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-kp-blue-700 tabular-nums">Rp {{ number_format($pr->terbayar, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">Rp {{ number_format($pr->sisa_piutang, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-center">
                                @if($pr->status_bayar === 'lunas')
                                    <span class="inline-block px-2 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Lunas</span>
                                @elseif($pr->status_bayar === 'sebagian')
                                    <span class="inline-block px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">Sebagian</span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">Belum Bayar</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="py-10 text-center text-slate-500 text-sm">Tidak ada data penerimaan koran yang sesuai dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                    <tr>
                        <td colspan="5" class="{{ $tfClass }} text-right font-sans uppercase text-xs font-bold text-slate-700">Total Penerimaan Koran:</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totPenKirim, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totPenRetur, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totPenNett, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right">Rp {{ number_format($totPenTagihan, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right text-kp-blue-700 font-bold">Rp {{ number_format($totPenTerbayar, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right font-bold">Rp {{ number_format($totPenPiutang, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@elseif($tab === 'oplah-tahun')
    @php
        $totalCetakTahunan = collect($rowsTahunan)->sum('kirim');
        $totalReturTahunan = collect($rowsTahunan)->sum('retur');
        $totalNettTahunan = collect($rowsTahunan)->sum('nett');
        $totalTagihanTahunan = collect($rowsTahunan)->sum('nilai_tagihan');
        $totalTerbayarTahunan = collect($rowsTahunan)->sum('terbayar');
        $totalHariTahunan = max(1, collect($rowsTahunan)->sum('jumlah_hari'));
        $rasioTahunan = $totalCetakTahunan > 0 ? round(($totalReturTahunan / $totalCetakTahunan) * 100, 1) : 0.0;
    @endphp

    <div class="{{ $kpiGridClass }}">
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kirim ({{ $tahunAudit }})</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ number_format($totalCetakTahunan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Rata-rata / Hari</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ number_format($totalCetakTahunan / $totalHariTahunan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Retur</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ number_format($totalReturTahunan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">({{ $rasioTahunan }}%)</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Koran Terjual</span>
            <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm text-slate-900' : 'print:text-sm' }}">{{ number_format($totalNettTahunan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
    </div>

    <div class="{{ $cardWrapClass }}">
        @if(!$isPrint)
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 print:hidden">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Laporan Penjualan Koran Tahunan</h2>
                    <p class="text-xs text-slate-500">Tahun: {{ $tahunAudit }} &bull; Sumber Data: Agen &amp; Retur Harian</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    12 Bulan
                </span>
            </div>
        @endif
        <div class="{{ $isPrint ? '' : 'overflow-x-auto min-h-[260px]' }}">
            <table class="{{ $tableClass }}">
                <thead class="{{ $theadClass }}">
                    <tr>
                        <th class="{{ $thClass }}">Bulan</th>
                        <th class="{{ $thClass }} text-center">Jumlah Hari</th>
                        <th class="{{ $thClass }} text-right">Total Kirim</th>
                        <th class="{{ $thClass }} text-right">Rata-rata / Hari</th>
                        <th class="{{ $thClass }} text-right">Retur</th>
                        <th class="{{ $thClass }} text-center">Persentase Retur</th>
                        <th class="{{ $thClass }} text-right">Koran Terjual</th>
                        <th class="{{ $thClass }} text-right">Total Tagihan</th>
                        <th class="{{ $thClass }} text-right">Sudah Dibayar</th>
                        @if(!$isPrint)<th class="print:hidden {{ $thClass }} text-center w-10"></th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                    @foreach($rowsTahunan as $rt)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="{{ $tdClass }} font-semibold text-slate-900">{{ $rt->label }}</td>
                            <td class="{{ $tdClass }} text-center font-mono text-slate-600">{{ $rt->jumlah_hari }} hari</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($rt->kirim, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($rt->rata_harian, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($rt->retur, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-center">
                                <span class="inline-block px-2 py-0.5 rounded-md font-mono text-xs bg-slate-100 text-slate-700 border border-slate-200">{{ number_format($rt->rasio_retur, 1) }}%</span>
                            </td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-kp-blue-700 tabular-nums">{{ number_format($rt->nett, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">Rp {{ number_format($rt->nilai_tagihan, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-slate-900 tabular-nums">Rp {{ number_format($rt->terbayar, 0, ',', '.') }}</td>
                            @if(!$isPrint)
                                <td class="print:hidden {{ $tdClass }} text-center" x-data="{ open: false }" :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <button type="button" @click="open = !open" class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer" title="Pilihan Aksi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5" /><circle cx="12" cy="12" r="1.5" /><circle cx="12" cy="19" r="1.5" /></svg>
                                        </button>
                                        <div x-cloak x-show="open" x-transition.duration.150ms class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 text-left">
                                            <a href="{{ route('sirkulasi.cetak.penjualan', ['bulan' => $rt->bulan_key]) }}" @click="open = false" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <span>Detail Penjualan</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                    <tr>
                        <td colspan="2" class="{{ $tfClass }} text-right font-sans uppercase text-xs font-bold text-slate-700">Akumulasi {{ $tahunAudit }}:</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totalCetakTahunan, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totalCetakTahunan / $totalHariTahunan, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totalReturTahunan, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-center">{{ number_format($rasioTahunan, 1) }}%</td>
                        <td class="{{ $tfClass }} text-right text-kp-blue-700 font-bold">{{ number_format($totalNettTahunan, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">Rp {{ number_format($totalTagihanTahunan, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right font-bold">Rp {{ number_format($totalTerbayarTahunan, 0, ',', '.') }}</td>
                        @if(!$isPrint)<td class="print:hidden"></td>@endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@elseif($tab === 'rekap-bln-wilayah')
    @php
        $totBlnWilAgen = $wilayahRekapRows->sum('agen_terfilter');
        $totBlnWilKirim = $wilayahRekapRows->sum('kirim');
        $totBlnWilRetur = $wilayahRekapRows->sum('retur');
        $totBlnWilNett = $wilayahRekapRows->sum('nett');
        $totBlnWilTagihan = $wilayahRekapRows->sum('tagihan_bersih');
        $totBlnWilTerbayar = $wilayahRekapRows->sum('terbayar');
        $totBlnWilPiutang = $wilayahRekapRows->sum('sisa_piutang');
    @endphp

    <div class="{{ $kpiGridClass }}">
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kirim</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ number_format($totBlnWilKirim, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Tagihan</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">Rp {{ number_format($totBlnWilTagihan, 0, ',', '.') }}</p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sudah Dibayar</span>
            <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm text-slate-900' : 'print:text-sm' }}">Rp {{ number_format($totBlnWilTerbayar, 0, ',', '.') }}</p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sisa Tagihan</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">Rp {{ number_format($totBlnWilPiutang, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="{{ $cardWrapClass }}">
        @if(!$isPrint)
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 print:hidden">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Laporan Bulanan per Wilayah</h2>
                    <p class="text-xs text-slate-500">{{ $periodeLabel }} &bull; Basis Finansial: <span class="font-semibold text-slate-700">{{ $sumberDataPenerimaan ?? 'Realisasi Berjalan' }}</span></p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ $wilayahRekapRows->count() }} Zona
                </span>
            </div>
        @endif
        <div class="{{ $isPrint ? '' : 'overflow-x-auto min-h-[260px]' }}">
            <table class="{{ $tableClass }}">
                <thead class="{{ $theadClass }}">
                    <tr>
                        <th class="{{ $thClass }} text-center w-14">Kode</th>
                        <th class="{{ $thClass }}">Wilayah</th>
                        <th class="{{ $thClass }} text-center">Jumlah Agen</th>
                        <th class="{{ $thClass }} text-right">Total Kirim</th>
                        <th class="{{ $thClass }} text-right">Retur</th>
                        <th class="{{ $thClass }} text-right">Koran Terjual</th>
                        <th class="{{ $thClass }} text-right">Total Tagihan</th>
                        <th class="{{ $thClass }} text-right">Sudah Dibayar</th>
                        <th class="{{ $thClass }} text-right">Sisa Tagihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                    @foreach($wilayahRekapRows as $bw)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="{{ $tdClass }} text-center font-mono font-bold text-kp-blue-700">{{ $bw->kode }}</td>
                            <td class="{{ $tdClass }} font-semibold text-slate-900">{{ $bw->nama }}</td>
                            <td class="{{ $tdClass }} text-center font-mono font-semibold">{{ $bw->agen_terfilter }}</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($bw->kirim, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($bw->retur, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($bw->nett, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-slate-900 tabular-nums">Rp {{ number_format($bw->tagihan_bersih, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-kp-blue-700 tabular-nums">Rp {{ number_format($bw->terbayar, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">Rp {{ number_format($bw->sisa_piutang, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                    <tr>
                        <td colspan="2" class="{{ $tfClass }} text-right font-sans uppercase text-xs font-bold text-slate-700">Total Akumulasi Wilayah:</td>
                        <td class="{{ $tfClass }} text-center">{{ $totBlnWilAgen }}</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totBlnWilKirim, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totBlnWilRetur, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totBlnWilNett, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">Rp {{ number_format($totBlnWilTagihan, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right text-kp-blue-700 font-bold">Rp {{ number_format($totBlnWilTerbayar, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right font-bold">Rp {{ number_format($totBlnWilPiutang, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@elseif($tab === 'agen-besar')
    <!-- Default: 1. Agen Besar per Terbit -->
    @php
        $totalJatahBesar = $agenBesarRows->sum('kirim');
        $totalLanggananBesar = $agenBesarRows->sum('langganan');
        $totalKonsinyasiBesar = $agenBesarRows->sum('konsinyasi');
        $totalGratisBesar = $agenBesarRows->sum('gratis');
        $totalPengenalanBesar = $agenBesarRows->sum('pengenalan');
        $totalInsidentilBesar = $agenBesarRows->sum('insidentil');
        $totalReturBesar = $agenBesarRows->sum('retur');
        $totalNettBesar = $agenBesarRows->sum('nett');
    @endphp

    <div class="{{ $kpiGridClass }}">
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kirim</span>
            <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm text-slate-900' : 'print:text-sm' }}">{{ number_format($totalJatahBesar, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
            <span class="text-xs text-slate-400 mt-1 block {{ $isPrint ? 'hidden' : 'print:hidden' }}">{{ $agenBesarRows->count() }} Mitra Agen Besar</span>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Langganan</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ number_format($totalLanggananBesar, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Konsinyasi (Titipan)</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ number_format($totalKonsinyasiBesar, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
        <div class="{{ $kpiCardClass }}">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Koran Terjual (Retur: {{ number_format($totalReturBesar, 0, ',', '.') }})</span>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums {{ $isPrint ? 'text-sm' : 'print:text-sm' }}">{{ number_format($totalNettBesar, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
        </div>
    </div>

    <div class="{{ $cardWrapClass }}">
        @if(!$isPrint)
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 print:hidden">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Laporan Agen Besar</h2>
                    <p class="text-xs text-slate-500">{{ $periodeLabel }} ({{ $daysInRange }} hari)</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ $agenBesarRows->count() }} Agen Besar
                </span>
            </div>
        @endif
        <div class="{{ $isPrint ? '' : 'overflow-x-auto min-h-[260px]' }}">
            <table class="{{ $tableClass }}">
                <thead class="{{ $theadClass }}">
                    <tr>
                        <th class="{{ $thClass }} text-center w-10">No</th>
                        <th class="{{ $thClass }}">Kode Agen</th>
                        <th class="{{ $thClass }}">Nama Agen</th>
                        <th class="{{ $thClass }}">Wilayah</th>
                        <th class="{{ $thClass }}">Rute Jurusan</th>
                        <th class="{{ $thClass }} text-right">Langganan</th>
                        <th class="{{ $thClass }} text-right">Konsinyasi</th>
                        <th class="{{ $thClass }} text-right">Gratis</th>
                        <th class="{{ $thClass }} text-right">Total Kirim</th>
                        <th class="{{ $thClass }} text-right">Retur</th>
                        <th class="{{ $thClass }} text-right">Koran Terjual</th>
                        @if(!$isPrint)<th class="print:hidden {{ $thClass }} text-center w-10"></th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                    @forelse($agenBesarRows as $idx => $ab)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="{{ $tdClass }} text-center text-slate-500">{{ $idx + 1 }}</td>
                            <td class="{{ $tdClass }} font-mono font-bold text-kp-blue-700">{{ $ab->kode_agen }}</td>
                            <td class="{{ $tdClass }} font-semibold text-slate-900">{{ $ab->nama_agen }}</td>
                            <td class="{{ $tdClass }}">{{ $ab->wilayah_nama }}</td>
                            <td class="{{ $tdClass }} font-mono">Rute {{ $ab->jurusan_kode }}</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($ab->langganan, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-kp-blue-700 tabular-nums">{{ number_format($ab->konsinyasi, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($ab->gratis + $ab->pengenalan + $ab->insidentil, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono font-bold text-slate-900 tabular-nums">{{ number_format($ab->kirim, 0, ',', '.') }} eks</td>
                            <td class="{{ $tdClass }} text-right font-mono tabular-nums">{{ number_format($ab->retur, 0, ',', '.') }}</td>
                            <td class="{{ $tdClass }} text-right font-mono font-semibold text-kp-blue-700 tabular-nums">{{ number_format($ab->nett, 0, ',', '.') }} eks</td>
                            @if(!$isPrint)
                                <td class="print:hidden {{ $tdClass }} text-center" x-data="{ open: false }" :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <button type="button" @click="open = !open" class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer" title="Pilihan Aksi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5" /><circle cx="12" cy="12" r="1.5" /><circle cx="12" cy="19" r="1.5" /></svg>
                                        </button>
                                        <div x-cloak x-show="open" x-transition.duration.150ms class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 text-left">
                                            <a href="{{ route('sirkulasi.tabel.agen', ['search' => $ab->kode_agen]) }}" @click="open = false" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <span>Detail Agen</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isPrint ? 11 : 12 }}" class="py-10 text-center text-slate-500 text-sm">Tidak ada data agen besar yang sesuai dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                    <tr>
                        <td colspan="5" class="{{ $tfClass }} text-right font-sans uppercase text-xs font-bold text-slate-700">Total Agen Besar:</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totalLanggananBesar, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right text-kp-blue-700">{{ number_format($totalKonsinyasiBesar, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totalGratisBesar + $totalPengenalanBesar + $totalInsidentilBesar, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right font-bold">{{ number_format($totalJatahBesar, 0, ',', '.') }} eks</td>
                        <td class="{{ $tfClass }} text-right">{{ number_format($totalReturBesar, 0, ',', '.') }}</td>
                        <td class="{{ $tfClass }} text-right text-kp-blue-700 font-bold">{{ number_format($totalNettBesar, 0, ',', '.') }} eks</td>
                        @if(!$isPrint)<td class="print:hidden"></td>@endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-xl p-8 text-center my-6">
        <p class="text-slate-600 font-semibold">Format laporan tidak ditemukan atau belum dipilih.</p>
        <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'agen-besar']) }}" class="mt-3 inline-flex items-center px-4 py-2 bg-[#0E527B] text-white text-xs font-semibold rounded-lg shadow-xs hover:bg-[#07334F] transition">
            Kembali ke Laporan Agen Besar
        </a>
    </div>
@endif
