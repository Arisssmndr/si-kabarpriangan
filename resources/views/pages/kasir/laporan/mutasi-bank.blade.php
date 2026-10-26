<x-app-layout>
    <div class="space-y-6" x-data="{
        activeBank: '{{ $bankDipilih === 'BCA' ? 'bca' : 'bjb' }}'
    }">

        <!-- Header Mutasi Kas Bank (Hidden on Print) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Laporan Rekening Bank (BJB & BCA)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-blue-100 text-blue-800 border border-blue-300">
                            Format DOS Rekening Bank
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Monitoring berkala mutasi buku bank: debet (penarikan kas tunai/cek) dan kredit (penerimaan setoran transfer iklan)
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 print:hidden no-print">
                <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan Bank</span>
                </button>
            </div>
        </div>

        <!-- Filter Bulan & Tahun -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden no-print">
            <form method="GET" action="{{ route('kasir.laporan.mutasi-bank') }}" class="flex flex-wrap items-center gap-3">
                <div class="w-44">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Bank</label>
                    <select name="bank" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 text-slate-800 font-semibold">
                        <option value="all" {{ ($bankDipilih ?? 'all') === 'all' ? 'selected' : '' }}>Semua Rekening Bank</option>
                        <option value="BJB" {{ ($bankDipilih ?? '') === 'BJB' ? 'selected' : '' }}>Bank BJB (05.00.01.006270.9)</option>
                        <option value="BCA" {{ ($bankDipilih ?? '') === 'BCA' ? 'selected' : '' }}>Bank BCA (Giro Tasikmalaya)</option>
                    </select>
                </div>
                <div class="w-40">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Bulan</label>
                    <select name="bulan" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 text-slate-800 font-semibold">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tahun</label>
                    <input type="number" name="tahun" value="{{ $tahun }}" 
                           class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 font-mono font-semibold text-slate-800">
                </div>
                <div class="pt-5">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition shadow-2xs">
                        Tampilkan Mutasi
                    </button>
                </div>
            </form>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 print:hidden no-print">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Mutasi Bank Jabar BJB</span>
                <div class="mt-2 text-lg font-black text-blue-900 font-mono flex items-center justify-between">
                    <span>Debet: Rp {{ number_format($totalDebetBjb, 0, ',', '.') }}</span>
                    <span class="text-emerald-700">Kredit: Rp {{ number_format($totalKreditBjb, 0, ',', '.') }}</span>
                </div>
                <div class="mt-1 text-[11px] text-slate-400 font-mono">Giro: 05.00.01.006270.9 Bank Jabar Cabang Tasikmalaya</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Mutasi Bank BCA</span>
                <div class="mt-2 text-lg font-black text-slate-900 font-mono flex items-center justify-between">
                    <span>Debet: Rp {{ number_format($totalDebetBca, 0, ',', '.') }}</span>
                    <span class="text-emerald-700">Kredit: Rp {{ number_format($totalKreditBca, 0, ',', '.') }}</span>
                </div>
                <div class="mt-1 text-[11px] text-slate-400 font-mono">Rekening Giro BCA Cabang Tasikmalaya</div>
            </div>
        </div>

        <!-- Tab Selector: Bank BJB vs Bank BCA -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2 print:hidden no-print">
            <button type="button" @click="activeBank = 'bjb'" :class="activeBank === 'bjb' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <span>Bank Jabar BJB ({{ $queryBjb->count() }} Mutasi)</span>
            </button>
            <button type="button" @click="activeBank = 'bca'" :class="activeBank === 'bca' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <span>Bank BCA ({{ $queryBca->count() }} Mutasi)</span>
            </button>
        </div>

        <!-- ======================================================== -->
        <!-- TABEL 1: MUTASI BANK JABAR BJB (Format Asli DOS KAS) -->
        <!-- ======================================================== -->
        <div x-show="activeBank === 'bjb'" class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs font-mono text-xs">
            <div class="max-w-4xl mx-auto space-y-3">
                <div class="border-b border-dashed border-slate-400 pb-2">
                    <div class="font-bold text-sm text-slate-900">HU. PRIANGAN</div>
                    <div class="font-bold text-xs text-slate-800">LAPORAN REKENING BANK BULAN {{ strtoupper($namaBulan) }}</div>
                    <div class="text-[11px] text-slate-600">BANK : 05.00.01.006270.9 Bank Jabar Cabang Tasikmalaya</div>
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
                        $isDebet = ($m->kode_bagian === '09');
                        $debetVal = $isDebet ? (float) $m->jumlah_dibayar : 0;
                        $kreditVal = !$isDebet ? (float) $m->jumlah_dibayar : 0;
                    @endphp
                    <div class="grid grid-cols-12 py-1 text-slate-800 hover:bg-slate-50 border-b border-slate-100">
                        <span class="col-span-2 text-slate-500">{{ $m->tanggal_terima ? \Carbon\Carbon::parse($m->tanggal_terima)->format('d-m-Y') : '-' }}</span>
                        <span class="col-span-6 font-semibold truncate pr-2">
                            {{ $m->keterangan ?? ($m->terima_dari . ' - ' . $m->no_kuitansi) }}
                        </span>
                        <span class="col-span-2 text-right font-mono {{ $debetVal > 0 ? 'text-blue-900 font-bold' : 'text-slate-400' }}">
                            {{ number_format($debetVal, 2, ',', '.') }}
                        </span>
                        <span class="col-span-2 text-right font-mono {{ $kreditVal > 0 ? 'text-emerald-800 font-bold' : 'text-slate-400' }}">
                            {{ number_format($kreditVal, 2, ',', '.') }}
                        </span>
                    </div>
                    @empty
                    <div class="py-6 text-center text-slate-400">
                        Tidak ada transaksi mutasi Bank BJB pada bulan {{ $namaBulan }}.
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
                    Tasikmalaya, {{ Carbon\Carbon::now()->translatedFormat('d-m-Y H:i:s') }}
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- TABEL 2: MUTASI BANK BCA (Format Asli DOS KAS) -->
        <!-- ======================================================== -->
        <div x-show="activeBank === 'bca'" class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs font-mono text-xs">
            <div class="max-w-4xl mx-auto space-y-3">
                <div class="border-b border-dashed border-slate-400 pb-2">
                    <div class="font-bold text-sm text-slate-900">HU. PRIANGAN</div>
                    <div class="font-bold text-xs text-slate-800">LAPORAN REKENING BANK BULAN {{ strtoupper($namaBulan) }}</div>
                    <div class="text-[11px] text-slate-600">BANK : Giro Bank BCA Cabang Tasikmalaya</div>
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
                        $isDebet = ($m->kode_bagian === '09');
                        $debetVal = $isDebet ? (float) $m->jumlah_dibayar : 0;
                        $kreditVal = !$isDebet ? (float) $m->jumlah_dibayar : 0;
                    @endphp
                    <div class="grid grid-cols-12 py-1 text-slate-800 hover:bg-slate-50 border-b border-slate-100">
                        <span class="col-span-2 text-slate-500">{{ $m->tanggal_terima ? \Carbon\Carbon::parse($m->tanggal_terima)->format('d-m-Y') : '-' }}</span>
                        <span class="col-span-6 font-semibold truncate pr-2">
                            {{ $m->keterangan ?? ($m->terima_dari . ' - ' . $m->no_kuitansi) }}
                        </span>
                        <span class="col-span-2 text-right font-mono {{ $debetVal > 0 ? 'text-blue-900 font-bold' : 'text-slate-400' }}">
                            {{ number_format($debetVal, 2, ',', '.') }}
                        </span>
                        <span class="col-span-2 text-right font-mono {{ $kreditVal > 0 ? 'text-emerald-800 font-bold' : 'text-slate-400' }}">
                            {{ number_format($kreditVal, 2, ',', '.') }}
                        </span>
                    </div>
                    @empty
                    <div class="py-6 text-center text-slate-400">
                        Tidak ada transaksi mutasi Bank BCA pada bulan {{ $namaBulan }}.
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
                    Tasikmalaya, {{ Carbon\Carbon::now()->translatedFormat('d-m-Y H:i:s') }}
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
