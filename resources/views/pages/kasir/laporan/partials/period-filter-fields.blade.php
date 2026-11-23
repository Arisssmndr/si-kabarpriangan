@php
    $tipeColClass = $tipeColClass ?? 'sm:col-span-3';
    $inputColClass = $inputColClass ?? 'sm:col-span-6';
    $selectedBulan = (int) ($bulan ?? date('n'));
    $selectedTahun = (int) ($tahun ?? date('Y'));
    $selectedTanggal = $tanggal ?? date('Y-m-d');
@endphp

<div class="{{ $tipeColClass }}">
    <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Periode Waktu</label>
    <select name="periode_tipe"
            x-model="periode"
            class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 cursor-pointer">
        <option value="hari" {{ $periodeTipe === 'hari' ? 'selected' : '' }}>Per Tanggal</option>
        <option value="bulan" {{ $periodeTipe === 'bulan' ? 'selected' : '' }}>Per Bulan</option>
        <option value="tahun" {{ $periodeTipe === 'tahun' ? 'selected' : '' }}>Per Tahun</option>
    </select>
</div>

<div class="{{ $inputColClass }}">
    {{-- Per Tanggal --}}
    <div x-show="periode === 'hari'" x-cloak>
        <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Pilih Tanggal</label>
        <input type="date"
               name="tanggal"
               value="{{ $selectedTanggal }}"
               :disabled="periode !== 'hari'"
               class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600">
    </div>

    {{-- Per Bulan --}}
    <div x-show="periode === 'bulan'" x-cloak class="grid grid-cols-2 gap-2">
        <div>
            <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Bulan</label>
            <select name="bulan"
                    :disabled="periode !== 'bulan'"
                    class="h-11 w-full px-3 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 cursor-pointer">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $selectedBulan === $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Tahun</label>
            <select name="tahun"
                    :disabled="periode !== 'bulan'"
                    class="h-11 w-full px-3 bg-white border border-slate-300 rounded-xl font-mono text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 cursor-pointer">
                @for($y = (int) date('Y') + 1; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $selectedTahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>

    {{-- Per Tahun --}}
    <div x-show="periode === 'tahun'" x-cloak>
        <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Pilih Tahun</label>
        <select name="tahun"
                :disabled="periode !== 'tahun'"
                class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl font-mono text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 cursor-pointer">
            @for($y = (int) date('Y') + 1; $y >= 2020; $y--)
                <option value="{{ $y }}" {{ $selectedTahun === $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </div>
</div>

