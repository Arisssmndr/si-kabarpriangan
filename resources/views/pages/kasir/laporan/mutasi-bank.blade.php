<x-app-layout>
    @php
        $unitPelaksana = Auth::user()?->kasirReportUnitLabel() ?? 'Divisi Kasir';
    @endphp
    <div class="space-y-6" x-data="{
        activeBank: '{{ $bankDipilih === 'BCA' ? 'bca' : 'bjb' }}',
        periode: '{{ $periodeTipe ?? 'bulan' }}'
    }">

        <!-- 1. Header Standar Divisi Iklan (Hidden on Print) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Logo Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                            Laporan Rekening Bank (BJB & BCA)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-sky-50 text-[#0E527B] border border-sky-200">
                            Buku Bank
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $unitPelaksana }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Monitoring berkala mutasi buku bank: debet (penarikan kas tunai/cek) dan kredit (penerimaan setoran transfer iklan).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 print:hidden no-print shrink-0">
                <button type="button" onclick="window.print()" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl transition shadow-xs flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan Bank</span>
                </button>
            </div>
        </div>

        <!-- 2. KPI Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 print:hidden no-print">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Mutasi Bank Jabar BJB</span>
                <div class="mt-2 text-xl font-bold text-slate-900 font-mono flex items-center justify-between">
                    <span>Debet: Rp {{ number_format($totalDebetBjb, 0, ',', '.') }}</span>
                    <span class="text-kp-blue-700">Kredit: Rp {{ number_format($totalKreditBjb, 0, ',', '.') }}</span>
                </div>
                <span class="mt-1 text-[11px] text-slate-400 font-mono block">Giro: 05.00.01.006270.9 Bank Jabar Cabang Tasikmalaya</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Mutasi Bank BCA</span>
                <div class="mt-2 text-xl font-bold text-slate-900 font-mono flex items-center justify-between">
                    <span>Debet: Rp {{ number_format($totalDebetBca, 0, ',', '.') }}</span>
                    <span class="text-kp-blue-700">Kredit: Rp {{ number_format($totalKreditBca, 0, ',', '.') }}</span>
                </div>
                <span class="mt-1 text-[11px] text-slate-400 font-mono block">Rekening Giro BCA Cabang Tasikmalaya</span>
            </div>
        </div>

        <!-- 3. Filter Bar Terpisah -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden no-print">
            <form method="GET" action="{{ route('kasir.laporan.mutasi-bank') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Pilih Bank</label>
                    <select name="bank" class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600">
                        <option value="all" {{ ($bankDipilih ?? 'all') === 'all' ? 'selected' : '' }}>Semua Rekening Bank</option>
                        <option value="BJB" {{ ($bankDipilih ?? '') === 'BJB' ? 'selected' : '' }}>Bank BJB (05.00.01.006270.9)</option>
                        <option value="BCA" {{ ($bankDipilih ?? '') === 'BCA' ? 'selected' : '' }}>Bank BCA (Giro Tasikmalaya)</option>
                    </select>
                </div>
                @include('pages.kasir.laporan.partials.period-filter-fields', [
                    'periodeTipe'   => $periodeTipe ?? 'bulan',
                    'tanggal'       => $tanggal ?? date('Y-m-d'),
                    'bulan'         => $bulan,
                    'tahun'         => $tahun,
                    'tipeColClass'  => 'sm:col-span-3',
                    'inputColClass' => 'sm:col-span-4',
                ])
                <div class="sm:col-span-2">
                    <button type="submit" class="h-11 w-full bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-xs cursor-pointer">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>

        <!-- 4. Tab Selector: Bank BJB vs Bank BCA -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2 print:hidden no-print">
            <button type="button" @click="activeBank = 'bjb'" 
                    :class="activeBank === 'bjb' ? 'bg-kp-blue-600 text-white font-semibold' : 'bg-white text-slate-700 hover:bg-slate-100 font-medium border border-slate-200'" 
                    class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2 cursor-pointer shadow-xs">
                <span>Bank Jabar BJB ({{ $queryBjb->count() }} Mutasi)</span>
            </button>
            <button type="button" @click="activeBank = 'bca'" 
                    :class="activeBank === 'bca' ? 'bg-kp-blue-600 text-white font-semibold' : 'bg-white text-slate-700 hover:bg-slate-100 font-medium border border-slate-200'" 
                    class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2 cursor-pointer shadow-xs">
                <span>Bank BCA ({{ $queryBca->count() }} Mutasi)</span>
            </button>
        </div>

        <!-- TABEL 1: MUTASI BANK JABAR BJB -->
        <div x-show="activeBank === 'bjb'" class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs font-mono text-xs">
            <div class="max-w-4xl mx-auto space-y-3">
                <div class="flex justify-between items-start border-b border-dashed border-slate-400 pb-2 gap-4">
                    <div>
                        <div class="font-bold text-sm text-slate-900">HU KABAR PRIANGAN</div>
                        <div class="font-bold text-xs text-slate-800">LAPORAN REKENING BANK &mdash; {{ strtoupper($labelPeriode ?? $namaBulan) }}</div>
                        <div class="text-[11px] text-slate-600">BANK : 05.00.01.006270.9 Bank Jabar Cabang Tasikmalaya</div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="font-bold text-[11px] text-slate-900 uppercase">SIKAPRI - {{ strtoupper($unitPelaksana) }}</div>
                        <div class="text-[10px] text-slate-500">{{ $unitPelaksana }}: {{ Auth::user()->name ?? $unitPelaksana }}</div>
                    </div>
                </div>

                <div class="border-b border-slate-900 py-1 font-bold text-slate-700">
                    <div class="grid grid-cols-12 text-[11px]">
                        <span class="col-span-2">Tanggal</span>
                        <span class="col-span-6">Uraian</span>
                        <span class="col-span-2 text-right">Debet (Rp)</span>
                        <span class="col-span-2 text-right">Kredit (Rp)</span>
                    </div>
                </div>

                <div class="space-y-1">
                    @forelse($queryBjb as $m)
                    @php
                        $isDebet = ($m->kode_bagian !== '09' || $m->metode_bayar === 'transfer');
                        $debetVal = $isDebet ? (float) $m->jumlah_dibayar : 0;
                        $kreditVal = !$isDebet ? (float) $m->jumlah_dibayar : 0;
                    @endphp
                    <div class="grid grid-cols-12 py-1 text-slate-800 hover:bg-slate-50 border-b border-slate-100">
                        <span class="col-span-2 text-slate-500">{{ $m->tanggal_terima ? \Carbon\Carbon::parse($m->tanggal_terima)->format('d-m-Y') : '-' }}</span>
                        <span class="col-span-6 font-semibold truncate pr-2">
                            {{ $m->keterangan ?? ($m->terima_dari . ' - ' . $m->no_kuitansi) }}
                        </span>
                        <span class="col-span-2 text-right font-mono {{ $debetVal > 0 ? 'text-slate-900 font-bold' : 'text-slate-400' }}">
                            {{ number_format($debetVal, 2, ',', '.') }}
                        </span>
                        <span class="col-span-2 text-right font-mono {{ $kreditVal > 0 ? 'text-kp-blue-700 font-bold' : 'text-slate-400' }}">
                            {{ number_format($kreditVal, 2, ',', '.') }}
                        </span>
                    </div>
                    @empty
                    <div class="py-6 text-center text-slate-400">
                        Tidak ada transaksi mutasi Bank BJB pada {{ $labelPeriode ?? $namaBulan }}.
                    </div>
                    @endforelse
                </div>

                <div class="border-t-2 border-b-2 border-slate-900 py-2 font-bold text-slate-900 mt-4">
                    <div class="grid grid-cols-12 text-xs">
                        <span class="col-span-8 uppercase">JUMLAH...</span>
                        <span class="col-span-2 text-right font-mono">{{ number_format($totalDebetBjb, 2, ',', '.') }}</span>
                        <span class="col-span-2 text-right font-mono">{{ number_format($totalKreditBjb, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="text-[10px] text-slate-400 text-right pt-2">
                    {{ $unitPelaksana }} ({{ Auth::user()->name ?? $unitPelaksana }}) | Tasikmalaya, {{ Carbon\Carbon::now()->translatedFormat('d-m-Y H:i:s') }}
                </div>
            </div>
        </div>

        <!-- TABEL 2: MUTASI BANK BCA -->
        <div x-show="activeBank === 'bca'" class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs font-mono text-xs">
            <div class="max-w-4xl mx-auto space-y-3">
                <div class="flex justify-between items-start border-b border-dashed border-slate-400 pb-2 gap-4">
                    <div>
                        <div class="font-bold text-sm text-slate-900">HU KABAR PRIANGAN</div>
                        <div class="font-bold text-xs text-slate-800">LAPORAN REKENING BANK &mdash; {{ strtoupper($labelPeriode ?? $namaBulan) }}</div>
                        <div class="text-[11px] text-slate-600">BANK : Giro Bank BCA Cabang Tasikmalaya</div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="font-bold text-[11px] text-slate-900 uppercase">SIKAPRI - {{ strtoupper($unitPelaksana) }}</div>
                        <div class="text-[10px] text-slate-500">{{ $unitPelaksana }}: {{ Auth::user()->name ?? $unitPelaksana }}</div>
                    </div>
                </div>

                <div class="border-b border-slate-900 py-1 font-bold text-slate-700">
                    <div class="grid grid-cols-12 text-[11px]">
                        <span class="col-span-2">Tanggal</span>
                        <span class="col-span-6">Uraian</span>
                        <span class="col-span-2 text-right">Debet (Rp)</span>
                        <span class="col-span-2 text-right">Kredit (Rp)</span>
                    </div>
                </div>

                <div class="space-y-1">
                    @forelse($queryBca as $m)
                    @php
                        $isDebet = ($m->kode_bagian !== '09' || $m->metode_bayar === 'transfer');
                        $debetVal = $isDebet ? (float) $m->jumlah_dibayar : 0;
                        $kreditVal = !$isDebet ? (float) $m->jumlah_dibayar : 0;
                    @endphp
                    <div class="grid grid-cols-12 py-1 text-slate-800 hover:bg-slate-50 border-b border-slate-100">
                        <span class="col-span-2 text-slate-500">{{ $m->tanggal_terima ? \Carbon\Carbon::parse($m->tanggal_terima)->format('d-m-Y') : '-' }}</span>
                        <span class="col-span-6 font-semibold truncate pr-2">
                            {{ $m->keterangan ?? ($m->terima_dari . ' - ' . $m->no_kuitansi) }}
                        </span>
                        <span class="col-span-2 text-right font-mono {{ $debetVal > 0 ? 'text-slate-900 font-bold' : 'text-slate-400' }}">
                            {{ number_format($debetVal, 2, ',', '.') }}
                        </span>
                        <span class="col-span-2 text-right font-mono {{ $kreditVal > 0 ? 'text-kp-blue-700 font-bold' : 'text-slate-400' }}">
                            {{ number_format($kreditVal, 2, ',', '.') }}
                        </span>
                    </div>
                    @empty
                    <div class="py-6 text-center text-slate-400">
                        Tidak ada transaksi mutasi Bank BCA pada {{ $labelPeriode ?? $namaBulan }}.
                    </div>
                    @endforelse
                </div>

                <div class="border-t-2 border-b-2 border-slate-900 py-2 font-bold text-slate-900 mt-4">
                    <div class="grid grid-cols-12 text-xs">
                        <span class="col-span-8 uppercase">JUMLAH...</span>
                        <span class="col-span-2 text-right font-mono">{{ number_format($totalDebetBca, 2, ',', '.') }}</span>
                        <span class="col-span-2 text-right font-mono">{{ number_format($totalKreditBca, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="text-[10px] text-slate-400 text-right pt-2">
                    {{ $unitPelaksana }} ({{ Auth::user()->name ?? $unitPelaksana }}) | Tasikmalaya, {{ Carbon\Carbon::now()->translatedFormat('d-m-Y H:i:s') }}
                </div>
            </div>
        </div>

    </div>
</x-app-layout>