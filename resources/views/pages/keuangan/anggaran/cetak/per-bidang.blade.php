<x-app-layout>
    <div class="space-y-6">

        <!-- Tier 1: Header Halaman Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-9 sm:h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Realisasi Anggaran Per Bidang Priangan
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            CETAK #2 &bull; ANGGARAN.EXE
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Rincian pengeluaran per transaksi BP untuk <strong>{{ $namaBidang }}</strong>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('keuangan.anggaran.cetak.hub') }}" class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
                    &larr; Menu Cetak
                </a>
                <a href="{{ route('keuangan.anggaran.cetak.per-bidang', ['kode_bagian' => $kodeBagian, 'periode' => $periode, 'cetak' => 1]) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar BP (Dot-Matrix / PDF)</span>
                </a>
            </div>
        </div>

        <!-- Tier 2: 4 KPI Summary Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Plafon RAB Bulan Ini</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($plafon, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Kode Bagian: {{ $kodeBagian }}
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Realisasi Disetujui</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $bpList->count() }} Transaksi BP
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sudah Dicairkan Kasir</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalCair, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $bpList->where('status', 'dicairkan_kasir')->count() }} BP Terbayar
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sisa Plafon Anggaran</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($sisaPlafon, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $plafon > 0 ? round(($totalRealisasi / $plafon) * 100, 1) : 0 }}% Terpakai
                </div>
            </div>
        </div>

        <!-- Tier 3: Toolbar Filter Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <form method="GET" action="{{ route('keuangan.anggaran.cetak.per-bidang') }}" class="flex items-center gap-2 flex-wrap">
                <select name="kode_bagian" onchange="this.form.submit()" class="h-11 text-xs rounded-xl border border-slate-300 px-3 font-semibold text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                    @foreach($daftarBidang as $kode => $nama)
                        <option value="{{ $kode }}" {{ $kodeBagian === $kode ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>

                <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()" 
                       class="h-11 text-xs font-mono font-semibold rounded-xl border border-slate-300 px-3 bg-white text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                
                <button type="submit" class="h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl transition shadow-xs cursor-pointer">
                    Filter
                </button>
            </form>

            <a href="{{ route('keuangan.anggaran.create', ['divisi' => $kodeBagian]) }}" class="inline-flex items-center gap-1.5 h-11 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Entry BP Bidang Ini</span>
            </a>
        </div>

        <!-- Tier 4: Bagan Akun Perkiraan (COA) Bidang Ini (Pure Data Table) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[220px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3.5 px-3.5 text-center w-12">No</th>
                            <th class="py-3.5 px-3.5 min-w-[150px]">Kode Perkiraan</th>
                            <th class="py-3.5 px-3.5 min-w-[240px]">Nama Perkiraan (COA)</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Plafon RAB</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Realisasi BP</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Sisa RAB</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @php $sumCoa = 0; @endphp
                        @foreach($coaList as $key => $c)
                            @php 
                                $sumCoa += $c['alokasi']; 
                                $realisasiAkun = (float) $bpList->where('kode_perkiraan', $c['kode'])->sum('nominal_acc');
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $key + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">{{ $c['kode'] }}</td>
                                <td class="py-3.5 px-3.5 font-bold text-slate-900">{{ $c['nama'] }}</td>
                                <td class="py-3.5 px-3.5 text-right">
                                    @if($c['alokasi'] > 0)
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($c['alokasi'], 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-mono">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    @if($realisasiAkun > 0)
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($realisasiAkun, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-mono">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    @if($c['alokasi'] > 0)
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($c['alokasi'] - $realisasiAkun, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-mono">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200 font-semibold text-xs whitespace-nowrap text-slate-900">
                        <tr>
                            <td colspan="3" class="py-3.5 px-3.5 uppercase font-bold text-slate-900">
                                TOTAL ALOKASI PERKIRAAN BIDANG {{ $kodeBagian }}
                            </td>
                            <td class="py-3.5 px-3.5 text-right">
                                <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                    <span class="font-bold text-slate-900 tabular-nums">{{ number_format($sumCoa, 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-3.5 text-right">
                                <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                    <span class="font-bold text-kp-blue-700 tabular-nums">{{ number_format($totalRealisasi, 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-3.5 text-right">
                                <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                    <span class="font-bold text-slate-900 tabular-nums">{{ number_format($sumCoa - $totalRealisasi, 0, ',', '.') }}</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Tier 4 (Lanjutan): Tabel Rincian Dokumen BP Bidang Ini (Pure Data Table, Kolom Terpisah) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3.5 px-3.5 text-center w-12">No</th>
                            <th class="py-3.5 px-3.5 min-w-[110px]">Tanggal</th>
                            <th class="py-3.5 px-3.5 min-w-[140px]">No. BP</th>
                            <th class="py-3.5 px-3.5 min-w-[140px]">No. Register</th>
                            <th class="py-3.5 px-3.5 min-w-[130px]">Kode COA</th>
                            <th class="py-3.5 px-3.5 min-w-[150px]">Pemohon</th>
                            <th class="py-3.5 px-3.5 min-w-[240px]">Keperluan</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Nominal ACC</th>
                            <th class="py-3.5 px-3.5 text-center w-28">Status Kasir</th>
                            <th class="py-3.5 px-3.5 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($bpList as $idx => $bp)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-700">
                                    {{ \Carbon\Carbon::parse($bp->tanggal_pengajuan)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">
                                    {{ $bp->no_bp }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-700">
                                    {{ $bp->no_register_anggaran ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-700">
                                    {{ $bp->kode_perkiraan ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-bold text-slate-900">
                                    {{ $bp->pemohon }}
                                </td>
                                <td class="py-3.5 px-3.5 text-xs text-slate-600 whitespace-normal min-w-[240px] max-w-sm leading-relaxed">
                                    {{ $bp->keperluan }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($bp->nominal_acc ?? $bp->nominal_pengajuan, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($bp->status === 'dicairkan_kasir')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Cair Kasir
                                        </span>
                                    @elseif($bp->status === 'approved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            ACC
                                        </span>
                                    @elseif($bp->status === 'rejected')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-200 text-slate-700">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            Antrean
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <a href="{{ route('keuangan.anggaran.voucher', $bp->pengajuan_anggaran_id ?? $bp->id) }}" 
                                       target="_blank"
                                       class="px-2.5 py-1 rounded-lg border border-slate-300 hover:border-kp-blue-500 hover:bg-kp-blue-50 text-kp-blue-700 text-xs font-semibold transition shadow-2xs">
                                        Cetak BP
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400 italic">
                                    Tidak ada transaksi BP pada bidang dan periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200 font-semibold text-xs whitespace-nowrap text-slate-900">
                        <tr>
                            <td colspan="7" class="py-3.5 px-3.5 text-center uppercase font-bold text-slate-900">
                                TOTAL REALISASI BIDANG INI
                            </td>
                            <td class="py-3.5 px-3.5 text-right">
                                <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                    <span class="font-bold text-kp-blue-700 tabular-nums">{{ number_format($totalRealisasi, 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td colspan="2" class="py-3.5 px-3.5 text-center text-slate-500 font-normal text-xs">
                                {{ $bpList->count() }} Dokumen BP
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
