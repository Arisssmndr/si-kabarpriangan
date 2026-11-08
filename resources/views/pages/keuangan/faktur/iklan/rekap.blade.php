<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-sky-50 border border-sky-200 flex items-center justify-center text-xl shrink-0">
                    📊
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            REKAP FAKTUR IKLAN
                        </h1>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 uppercase">
                            {{ $mode === 'bulan' ? 'Laporan Bulanan (' . strtoupper(str_replace('_', ' ', $group)) . ')' : 'Laporan Harian / per Tanggal' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi omzet, faktur terbit, penerimaan kas, dan piutang iklan (HU. Kabar Priangan)
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('keuangan.faktur.iklan.rekap.cetak', request()->query()) }}" target="_blank"
                   class="h-9 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold flex items-center gap-1.5 shadow-2xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>🖨 Cetak Rekapitulasi</span>
                </a>
            </div>
        </div>


        <!-- KPI Ringkas Periode Terpilih -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono">Faktur Terbit Periode</span>
                <div class="mt-2 text-2xl font-black text-slate-900 tracking-tight">
                    {{ $grandFaktur }} <span class="text-xs font-medium text-slate-500">Faktur</span>
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    Berdasarkan filter aktif
                </div>
            </div>

            <div class="bg-white border border-sky-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-sky-700 font-mono">Total Nilai Tagihan</span>
                <div class="mt-2 text-2xl font-black text-sky-700 tracking-tight font-mono">
                    Rp {{ number_format($grandTagihan, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-sky-800 font-semibold">
                    Akumulasi omset iklan
                </div>
            </div>

            <div class="bg-white border border-emerald-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 font-mono">Total Tertagih (Kas)</span>
                <div class="mt-2 text-2xl font-black text-emerald-600 tracking-tight font-mono">
                    Rp {{ number_format($grandTerbayar, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-emerald-700 font-semibold">
                    {{ $grandTagihan > 0 ? round(($grandTerbayar / $grandTagihan) * 100, 1) : 0 }}% dari target tagihan
                </div>
            </div>

            <div class="bg-white border border-rose-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-700 font-mono">Sisa Piutang Berjalan</span>
                <div class="mt-2 text-2xl font-black text-rose-600 tracking-tight font-mono">
                    Rp {{ number_format($grandPiutang, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-rose-700 font-semibold">
                    {{ $grandTagihan > 0 ? round(($grandPiutang / $grandTagihan) * 100, 1) : 0 }}% belum terbayar
                </div>
            </div>
        </div>

        <!-- Filter Sub-Menu Rekapitulasi (Persis Sesuai Sistem Asli Kabar Priangan) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs space-y-4">
            <!-- Pilihan Mode Utama: Per Tanggal vs Per Bulan -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mode Rekap:</span>
                    <a href="{{ route('keuangan.faktur.iklan.rekap', ['mode' => 'bulan', 'group' => 'per_bulan']) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition {{ $mode === 'bulan' ? 'bg-sky-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        🗓️ Rekap Faktur Iklan / Bulan
                    </a>
                    <a href="{{ route('keuangan.faktur.iklan.rekap', ['mode' => 'tanggal']) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition {{ $mode === 'tanggal' ? 'bg-sky-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        📅 Rekap Faktur Iklan / Tanggal
                    </a>
                </div>

                <div class="text-[11px] text-slate-400 font-mono">
                    HU. KABAR PRIANGAN - SISTEM FAKTUR
                </div>
            </div>

            <!-- Submenu Pengelompokan (Hanya muncul pada Mode Bulan sesuai sistem legacy) -->
            @if($mode === 'bulan')
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1">Opsi Sub-Menu:</span>
                    
                    @php
                        $subMenus = [
                            'per_bulan'      => 'PER BULAN',
                            'per_tahun'      => 'PER TAHUN',
                            'per_ae_bulan'   => 'PER AE / BLN',
                            'per_ae_tahun'   => 'PER AE / THN',
                            'rekap_ae_bulan' => 'REKAP AE / BLN',
                            'rekap_ae_tahun' => 'REKAP AE / THN',
                            'per_pemasang'   => 'PER PEMASANG',
                        ];
                    @endphp

                    @foreach($subMenus as $key => $label)
                        <a href="{{ route('keuangan.faktur.iklan.rekap', array_merge(request()->query(), ['mode' => 'bulan', 'group' => $key])) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-bold shrink-0 transition {{ $group === $key ? 'bg-slate-900 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Form Filter Periode (Mengadaptasi Dialog 'SISA FAKTUR' / Tahun & Rentang Bulan) -->
            <form method="GET" action="{{ route('keuangan.faktur.iklan.rekap') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-2">
                <input type="hidden" name="mode" value="{{ $mode }}">
                <input type="hidden" name="group" value="{{ $group }}">

                @if($mode === 'bulan')
                    <!-- Filter Tahun (Default 2026 / tahun berjalan) -->
                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Tahun
                        </label>
                        <select name="tahun" class="w-full text-xs rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                            @for($y = date('Y'); $y >= 2024; $y--)
                                <option value="{{ $y }}" {{ (request('tahun', $tahun ?? date('Y')) == $y) ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Filter Rentang Bulan: Bulan Awal s.d Bulan Akhir (Sesuai Dialog di Foto: BULAN 1 s.d BULAN 9) -->
                    <div class="sm:col-span-4">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Rentang Bulan (Bulan Awal s.d Akhir)
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="bulan_awal" class="w-full text-xs rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                                @foreach([1=>'1 - Jan',2=>'2 - Feb',3=>'3 - Mar',4=>'4 - Apr',5=>'5 - Mei',6=>'6 - Jun',7=>'7 - Jul',8=>'8 - Agu',9=>'9 - Sep',10=>'10 - Okt',11=>'11 - Nov',12=>'12 - Des'] as $m => $nama)
                                    <option value="{{ $m }}" {{ (request('bulan_awal', $bulanAwal ?? 1) == $m) ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                            <select name="bulan_akhir" class="w-full text-xs rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                                @foreach([1=>'1 - Jan',2=>'2 - Feb',3=>'3 - Mar',4=>'4 - Apr',5=>'5 - Mei',6=>'6 - Jun',7=>'7 - Jul',8=>'8 - Agu',9=>'9 - Sep',10=>'10 - Okt',11=>'11 - Nov',12=>'12 - Des'] as $m => $nama)
                                    <option value="{{ $m }}" {{ (request('bulan_akhir', $bulanAkhir ?? date('n')) == $m) ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Filter AE Khusus (Jika group per_ae_bulan atau per_ae_tahun) -->
                    @if(in_array($group, ['per_ae_bulan', 'per_ae_tahun']))
                        <div class="sm:col-span-3">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                                Pilih Account Executive (AE)
                            </label>
                            <select name="ae" class="w-full text-xs rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                                <option value="">Semua AE</option>
                                @foreach($aeList as $ae)
                                    <option value="{{ $ae }}" {{ (request('ae', $selectedAe ?? '') == $ae) ? 'selected' : '' }}>
                                        {{ $ae }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($group === 'per_pemasang')
                        <div class="sm:col-span-3">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                                Pilih Pemasang / Klien
                            </label>
                            <select name="customer_id" class="w-full text-xs rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                                <option value="">Semua Pemasang</option>
                                @foreach($customerList as $cust)
                                    <option value="{{ $cust->id }}" {{ (request('customer_id', $selectedCust ?? '') == $cust->id) ? 'selected' : '' }}>
                                        {{ $cust->nama_customer }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="sm:col-span-3"></div>
                    @endif

                    <div class="sm:col-span-2 flex items-end">
                        <button type="submit" class="w-full h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs">
                            Tampilkan
                        </button>
                    </div>

                @else
                    <!-- Mode Harian: Tanggal Mulai s/d Sampai -->
                    <div class="sm:col-span-5">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Dari Tanggal
                        </label>
                        <input type="date" name="dari_tanggal" value="{{ $dariTanggal }}" 
                               class="w-full text-xs rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                    </div>

                    <div class="sm:col-span-5">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Sampai Tanggal
                        </label>
                        <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal }}" 
                               class="w-full text-xs rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                    </div>

                    <div class="sm:col-span-2 flex items-end">
                        <button type="submit" class="w-full h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs">
                            Filter
                        </button>
                    </div>
                @endif
            </form>
        </div>

        <!-- Tabel Rekapitulasi -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">
                        @if($mode === 'bulan')
                            Tabel Rekapitulasi: {{ strtoupper(str_replace('_', ' ', $group)) }}
                        @else
                            Tabel Rekapitulasi: HARIAN PER TANGGAL
                        @endif
                    </h3>
                    <p class="text-xs text-slate-500">
                        @if($mode === 'bulan')
                            Tahun {{ $tahun }} &bull; Bulan {{ $bulanAwal }} s.d {{ $bulanAkhir }}
                        @else
                            {{ \Carbon\Carbon::parse($dariTanggal)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($sampaiTanggal)->translatedFormat('d M Y') }}
                        @endif
                    </p>
                </div>

                <div class="text-xs text-slate-500">
                    Total: <strong class="text-slate-900">{{ count($rekapData) }}</strong> baris rekap
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">
                                @if($mode === 'tanggal')
                                    Tanggal Faktur
                                @elseif($group === 'per_bulan')
                                    Bulan
                                @elseif($group === 'per_tahun')
                                    Tahun
                                @elseif(in_array($group, ['rekap_ae_bulan', 'rekap_ae_tahun']))
                                    Nama Account Executive (AE)
                                @elseif($group === 'per_pemasang')
                                    Nama Pemasang / Klien
                                @elseif($group === 'per_ae_bulan')
                                    Bulan (AE: {{ $selectedAe ?: 'Semua' }})
                                @elseif($group === 'per_ae_tahun')
                                    Tahun (AE: {{ $selectedAe ?: 'Semua' }})
                                @else
                                    Periode
                                @endif
                            </th>
                            <th class="py-3 px-4 text-center">Faktur Terbit</th>
                            <th class="py-3 px-4 text-right">Total Tagihan (Omzet)</th>
                            <th class="py-3 px-4 text-right">Tertagih (Tunai/Kas)</th>
                            <th class="py-3 px-4 text-right">Sisa Piutang</th>
                            <th class="py-3 px-4 text-center">Rasio Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rekapData as $row)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3 px-4 font-bold text-slate-800">
                                    @if($mode === 'tanggal')
                                        {{ \Carbon\Carbon::parse($row->periode_key)->translatedFormat('d F Y') }}
                                        <span class="text-[10px] text-slate-400 font-normal">({{ \Carbon\Carbon::parse($row->periode_key)->translatedFormat('l') }})</span>
                                    @elseif($group === 'per_bulan' || $group === 'per_ae_bulan')
                                        @php
                                            $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                                        @endphp
                                        Bulan {{ $row->periode_key }} ({{ $namaBulan[(int)$row->periode_key] ?? '' }})
                                    @elseif($group === 'per_pemasang')
                                        @php
                                            $cust = \App\Models\Customer::find($row->periode_key);
                                        @endphp
                                        {{ $cust->nama_customer ?? 'Pemasang #' . $row->periode_key }}
                                    @else
                                        {{ $row->periode_key }}
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-slate-700 font-mono">
                                    {{ $row->total_faktur }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-emerald-600">
                                    Rp {{ number_format($row->total_terbayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-rose-600">
                                    Rp {{ number_format($row->total_piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="inline-flex items-center gap-1">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            {{ $row->jumlah_lunas }} Lunas
                                        </span>
                                        @if($row->jumlah_piutang > 0)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                                {{ $row->jumlah_piutang }} Piutang
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <span class="text-3xl block mb-2">📊</span>
                                    Tidak ada data faktur iklan pada kriteria rekapitulasi ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($rekapData) > 0)
                        <tfoot class="bg-slate-50 font-extrabold text-slate-900 border-t-2 border-slate-200">
                            <tr>
                                <td class="py-3.5 px-4 uppercase tracking-wider">
                                    TOTAL KUMULATIF
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-sm">
                                    {{ $grandFaktur }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-sm text-sky-700">
                                    Rp {{ number_format($grandTagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-sm text-emerald-600">
                                    Rp {{ number_format($grandTerbayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-sm text-rose-600">
                                    Rp {{ number_format($grandPiutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center text-[11px] text-slate-500 font-normal">
                                    100% Terkonsolidasi
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
