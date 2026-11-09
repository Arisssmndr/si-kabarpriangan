<x-app-layout>
    <div class="space-y-5" x-data="{
        activeBank: '{{ $bankDipilih === 'BCA' ? 'bca' : 'bjb' }}'
    }">

        <!-- Header Halaman (Hidden on Print) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Laporan Rekening Bank (BJB & BCA)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Daftar mutasi debet dan kredit rekening giro operasional ({{ $namaBulan }}).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 print:hidden no-print">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan Bank</span>
                </button>
            </div>
        </div>

        <!-- Filter Bulan & Tahun -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden no-print">
            <form method="GET" action="{{ route('kasir.laporan.mutasi-bank') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Rekening Bank</label>
                    <select name="bank" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ ($bankDipilih ?? 'all') === 'all' ? 'selected' : '' }}>Semua Rekening Bank</option>
                        <option value="BJB" {{ ($bankDipilih ?? '') === 'BJB' ? 'selected' : '' }}>Bank BJB (05.00.01.006270.9)</option>
                        <option value="BCA" {{ ($bankDipilih ?? '') === 'BCA' ? 'selected' : '' }}>Bank BCA (Giro Tasikmalaya)</option>
                    </select>
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bulan</label>
                    <select name="bulan" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun</label>
                    <input type="number" name="tahun" value="{{ $tahun }}" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>

        <!-- 2 Kartu KPI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 print:hidden no-print">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Mutasi Bank Jabar BJB</span>
                <div class="mt-1.5 flex flex-wrap items-baseline justify-between gap-2 font-mono tabular-nums">
                    <span class="text-lg font-bold text-kp-blue-700">Debet: Rp {{ number_format($totalDebetBjb, 0, ',', '.') }}</span>
                    <span class="text-sm font-semibold text-slate-700">Kredit: Rp {{ number_format($totalKreditBjb, 0, ',', '.') }}</span>
                </div>
                <span class="text-xs text-slate-400 mt-1 block font-mono">Giro: 05.00.01.006270.9 Bank Jabar Cabang Tasikmalaya</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Mutasi Bank BCA</span>
                <div class="mt-1.5 flex flex-wrap items-baseline justify-between gap-2 font-mono tabular-nums">
                    <span class="text-lg font-bold text-slate-900">Debet: Rp {{ number_format($totalDebetBca, 0, ',', '.') }}</span>
                    <span class="text-sm font-semibold text-slate-700">Kredit: Rp {{ number_format($totalKreditBca, 0, ',', '.') }}</span>
                </div>
                <span class="text-xs text-slate-400 mt-1 block font-mono">Rekening Giro BCA Cabang Tasikmalaya</span>
            </div>
        </div>

        <!-- Tab Selector: Bank BJB vs Bank BCA -->
        <div class="bg-white border border-slate-200 rounded-xl p-1.5 shadow-xs inline-flex flex-wrap items-center gap-1.5 print:hidden no-print">
            <button type="button" @click="activeBank = 'bjb'"
                    :class="activeBank === 'bjb' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-semibold'"
                    class="px-4 py-2 rounded-lg text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer">
                <span>Bank Jabar BJB ({{ $queryBjb->count() }} Mutasi)</span>
            </button>
            <button type="button" @click="activeBank = 'bca'"
                    :class="activeBank === 'bca' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-semibold'"
                    class="px-4 py-2 rounded-lg text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer">
                <span>Bank BCA ({{ $queryBca->count() }} Mutasi)</span>
            </button>
        </div>

        <!-- TABEL 1: MUTASI BANK JABAR BJB -->
        <div x-show="activeBank === 'bjb'" class="report-print-container bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs font-mono text-xs">
            <div class="max-w-4xl mx-auto space-y-3">
                <div class="border-b border-dashed border-slate-400 pb-2">
                    <div class="font-bold text-sm text-slate-900">HU. PRIANGAN</div>
                    <div class="font-bold text-xs text-slate-800">LAPORAN REKENING BANK BULAN {{ strtoupper($namaBulan) }}</div>
                    <div class="text-[11px] text-slate-600">BANK : 05.00.01.006270.9 Bank Jabar Cabang Tasikmalaya</div>
                </div>

                <div class="border-b border-slate-900 py-1.5 font-bold text-slate-700">
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
                    <div class="grid grid-cols-12 py-1.5 text-slate-800 hover:bg-slate-50 border-b border-slate-100">
                        <span class="col-span-2 text-slate-600">{{ $m->tanggal_terima ? \Carbon\Carbon::parse($m->tanggal_terima)->format('d-m-Y') : '-' }}</span>
                        <span class="col-span-6 font-semibold text-slate-900 truncate pr-2">
                            {{ $m->keterangan ?? ($m->terima_dari . ' - ' . $m->no_kuitansi) }}
                        </span>
                        <span class="col-span-2 text-right font-mono tabular-nums {{ $debetVal > 0 ? 'text-kp-blue-700 font-bold' : 'text-slate-400' }}">
                            {{ number_format($debetVal, 2, ',', '.') }}
                        </span>
                        <span class="col-span-2 text-right font-mono tabular-nums {{ $kreditVal > 0 ? 'text-slate-900 font-bold' : 'text-slate-400' }}">
                            {{ number_format($kreditVal, 2, ',', '.') }}
                        </span>
                    </div>
                    @empty
                    <div class="py-8 text-center text-slate-500 font-sans">
                        Tidak ada transaksi mutasi Bank BJB pada bulan {{ $namaBulan }}.
                    </div>
                    @endforelse
                </div>

                <div class="border-t-2 border-b-2 border-slate-900 py-2 font-bold text-slate-900 mt-4 bg-slate-50 px-2">
                    <div class="grid grid-cols-12 text-xs">
                        <span class="col-span-8 uppercase">JUMLAH...</span>
                        <span class="col-span-2 text-right font-mono text-kp-blue-700 tabular-nums">{{ number_format($totalDebetBjb, 2, ',', '.') }}</span>
                        <span class="col-span-2 text-right font-mono tabular-nums">{{ number_format($totalKreditBjb, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 text-right pt-2">
                    Tasikmalaya, {{ Carbon\Carbon::now()->translatedFormat('d-m-Y H:i:s') }}
                </div>
            </div>
        </div>

        <!-- TABEL 2: MUTASI BANK BCA -->
        <div x-show="activeBank === 'bca'" class="report-print-container bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs font-mono text-xs">
            <div class="max-w-4xl mx-auto space-y-3">
                <div class="border-b border-dashed border-slate-400 pb-2">
                    <div class="font-bold text-sm text-slate-900">HU. PRIANGAN</div>
                    <div class="font-bold text-xs text-slate-800">LAPORAN REKENING BANK BULAN {{ strtoupper($namaBulan) }}</div>
                    <div class="text-[11px] text-slate-600">BANK : Giro Bank BCA Cabang Tasikmalaya</div>
                </div>

                <div class="border-b border-slate-900 py-1.5 font-bold text-slate-700">
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
                    <div class="grid grid-cols-12 py-1.5 text-slate-800 hover:bg-slate-50 border-b border-slate-100">
                        <span class="col-span-2 text-slate-600">{{ $m->tanggal_terima ? \Carbon\Carbon::parse($m->tanggal_terima)->format('d-m-Y') : '-' }}</span>
                        <span class="col-span-6 font-semibold text-slate-900 truncate pr-2">
                            {{ $m->keterangan ?? ($m->terima_dari . ' - ' . $m->no_kuitansi) }}
                        </span>
                        <span class="col-span-2 text-right font-mono tabular-nums {{ $debetVal > 0 ? 'text-kp-blue-700 font-bold' : 'text-slate-400' }}">
                            {{ number_format($debetVal, 2, ',', '.') }}
                        </span>
                        <span class="col-span-2 text-right font-mono tabular-nums {{ $kreditVal > 0 ? 'text-slate-900 font-bold' : 'text-slate-400' }}">
                            {{ number_format($kreditVal, 2, ',', '.') }}
                        </span>
                    </div>
                    @empty
                    <div class="py-8 text-center text-slate-500 font-sans">
                        Tidak ada transaksi mutasi Bank BCA pada bulan {{ $namaBulan }}.
                    </div>
                    @endforelse
                </div>

                <div class="border-t-2 border-b-2 border-slate-900 py-2 font-bold text-slate-900 mt-4 bg-slate-50 px-2">
                    <div class="grid grid-cols-12 text-xs">
                        <span class="col-span-8 uppercase">JUMLAH...</span>
                        <span class="col-span-2 text-right font-mono text-kp-blue-700 tabular-nums">{{ number_format($totalDebetBca, 2, ',', '.') }}</span>
                        <span class="col-span-2 text-right font-mono tabular-nums">{{ number_format($totalKreditBca, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 text-right pt-2">
                    Tasikmalaya, {{ Carbon\Carbon::now()->translatedFormat('d-m-Y H:i:s') }}
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
