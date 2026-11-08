@php
    $actionRoute = $actionRoute ?? route('laporan.index');
    $currentMode = $modePeriode ?? 'bulanan';
@endphp

<div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs"
     x-data="{ mode: '{{ $currentMode }}' }">
    <form method="GET" action="{{ $actionRoute }}" class="space-y-4">
        <input type="hidden" name="mode_periode" :value="mode">
        @if(!empty($tab))
            <input type="hidden" name="tab" value="{{ $tab }}">
        @endif

        <!-- Baris 1: Pilihan Mode Periode Fleksibel (Harian, Mingguan, Bulanan, Tahunan, Rentang Tanggal) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 pb-3 border-b border-slate-100">
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="text-sm font-semibold text-slate-600 mr-1">Mode Periode:</span>

                <button type="button"
                        @click="mode = 'harian'"
                        :class="mode === 'harian' ? 'bg-kp-blue-600 text-white border-kp-blue-600 font-semibold shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100 font-medium'"
                        class="h-9 px-3.5 rounded-lg text-sm border transition cursor-pointer">
                    Harian
                </button>

                <button type="button"
                        @click="mode = 'mingguan'"
                        :class="mode === 'mingguan' ? 'bg-kp-blue-600 text-white border-kp-blue-600 font-semibold shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100 font-medium'"
                        class="h-9 px-3.5 rounded-lg text-sm border transition cursor-pointer">
                    Mingguan
                </button>

                <button type="button"
                        @click="mode = 'bulanan'"
                        :class="mode === 'bulanan' ? 'bg-kp-blue-600 text-white border-kp-blue-600 font-semibold shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100 font-medium'"
                        class="h-9 px-3.5 rounded-lg text-sm border transition cursor-pointer">
                    Bulanan
                </button>

                <button type="button"
                        @click="mode = 'tahunan'"
                        :class="mode === 'tahunan' ? 'bg-kp-blue-600 text-white border-kp-blue-600 font-semibold shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100 font-medium'"
                        class="h-9 px-3.5 rounded-lg text-sm border transition cursor-pointer">
                    Tahunan
                </button>

                <button type="button"
                        @click="mode = 'custom'"
                        :class="mode === 'custom' ? 'bg-kp-blue-600 text-white border-kp-blue-600 font-semibold shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100 font-medium'"
                        class="h-9 px-3.5 rounded-lg text-sm border transition cursor-pointer">
                    Rentang Tanggal
                </button>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $periodeLabel ?? $hariTanggal }}
                </span>
            </div>
        </div>

        <!-- Baris 2: Input Parameter Periode & Filter Tambahan -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">

            <!-- Input Mode Harian / Mingguan -->
            <div class="sm:col-span-4" x-show="mode === 'harian' || mode === 'mingguan'" x-cloak>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" x-text="mode === 'harian' ? 'Tanggal Laporan' : 'Tanggal Akhir Pekan (7 Hari)'"></label>
                <input type="date" name="tanggal" value="{{ $tanggal ?? date('Y-m-d') }}"
                       :disabled="mode !== 'harian' && mode !== 'mingguan'"
                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
            </div>

            <!-- Input Mode Bulanan -->
            <div class="sm:col-span-4 lg:col-span-2" x-show="mode === 'bulanan'" x-cloak>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bulan</label>
                <select name="bulan"
                        :disabled="mode !== 'bulanan'"
                        class="w-full h-11 pl-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ ($bulan ?? date('n')) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Input Tahun (Bulanan & Tahunan) -->
            <div class="sm:col-span-2" x-show="mode === 'bulanan' || mode === 'tahunan'" x-cloak>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun</label>
                <input type="number" name="tahun" value="{{ $tahun ?? date('Y') }}" min="2020" max="2035"
                       :disabled="mode !== 'bulanan' && mode !== 'tahunan'"
                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
            </div>

            <!-- Input Mode Rentang Kustom -->
            <div class="sm:col-span-2" x-show="mode === 'custom'" x-cloak>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ $dari ?? date('Y-m-01') }}"
                       :disabled="mode !== 'custom'"
                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
            </div>

            <div class="sm:col-span-2" x-show="mode === 'custom'" x-cloak>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ $sampai ?? date('Y-m-d') }}"
                       :disabled="mode !== 'custom'"
                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
            </div>

            <!-- Slot Filter Spesifik Halaman -->
            {!! $extraFilters ?? '' !!}

            <!-- Tombol Terapkan & Reset -->
            <div class="{{ $submitColClass ?? 'sm:col-span-2' }} flex items-center gap-2">
                <button type="submit"
                        class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer whitespace-nowrap">
                    Tampilkan
                </button>
                <a href="{{ $actionRoute }}"
                   title="Reset ke Default"
                   class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl flex items-center justify-center transition">
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>
