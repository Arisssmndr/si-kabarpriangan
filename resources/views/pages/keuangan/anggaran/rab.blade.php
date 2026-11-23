<x-app-layout>
    <div x-data="{ 
        tab: '{{ $activeTab }}',
        editModal: false, 
        activeItem: { kode_bagian: '', nama: '', plafon: 0, keterangan: '' },
        openEdit(item) {
            this.activeItem = { 
                kode_bagian: item.kode_bagian, 
                nama: item.nama, 
                plafon: item.plafon, 
                keterangan: item.keterangan || '' 
            };
            this.editModal = true;
        }
    }" class="space-y-6">

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
                            RAB Priangan (Rencana Anggaran Biaya)
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Menu Entry DOS
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Penetapan plafon anggaran operasional per bidang (01-21), rincian akun perkiraan (COA), kontrol serapan, dan sisa anggaran berjalan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('keuangan.anggaran.cetak.per-bidang', ['kode_bagian' => $selectedBidang, 'periode' => $periode, 'cetak' => 1]) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Format Dot-Matrix</span>
                </a>
                <a href="{{ route('keuangan.anggaran.create', ['divisi' => $selectedMaster['slug'] ?? 'iklan']) }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Entry BP Bidang Ini</span>
                </a>
            </div>
        </div>

        <!-- Tier 2: 4 KPI Summary Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Plafon Anggaran</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalPlafon, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Periode {{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }}
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Realisasi BP Terpakai</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    BP Disetujui &amp; Dicairkan
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sisa Anggaran Tersedia</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalSisa, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Saldo Plafon Belum Terpakai
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Tingkat Serapan Anggaran</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    {{ $totalPersen }}%
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-kp-blue-600 h-1.5 rounded-full" style="width: {{ min(100, $totalPersen) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Tier 3: Toolbar Filter & Sub-Tabs Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 overflow-x-auto">
                    <button type="button" @click="tab = 'perkiraan'"
                            :class="tab === 'perkiraan' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold'"
                            class="h-10 px-4 rounded-xl text-xs transition cursor-pointer whitespace-nowrap">
                        Rincian Per Perkiraan (COA)
                    </button>
                    <button type="button" @click="tab = 'rekap'"
                            :class="tab === 'rekap' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold'"
                            class="h-10 px-4 rounded-xl text-xs transition cursor-pointer whitespace-nowrap">
                        Rekap 21 Bidang Priangan
                    </button>
                </div>

                <!-- Form Filter Periode Bulan -->
                <form method="GET" action="{{ route('keuangan.anggaran.entry.rab') }}" class="flex items-center gap-2">
                    <input type="hidden" name="kode_bagian" value="{{ $selectedBidang }}">
                    <input type="hidden" name="tab" :value="tab">
                    <label class="text-xs font-semibold text-slate-700 whitespace-nowrap">Periode:</label>
                    <input type="month" name="periode" value="{{ $periode }}" 
                           class="h-10 text-xs font-mono font-semibold rounded-xl border border-slate-300 px-3 bg-white text-slate-800 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                    <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl transition shadow-xs cursor-pointer">
                        Pilih
                    </button>
                </form>
            </div>

            <!-- Selector Bidang jika di tab Perkiraan -->
            <div x-show="tab === 'perkiraan'" class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <form method="GET" action="{{ route('keuangan.anggaran.entry.rab') }}" class="flex items-center gap-2 flex-wrap">
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <input type="hidden" name="tab" value="perkiraan">
                    <label class="text-xs font-semibold text-slate-700 whitespace-nowrap">Pilih Bidang:</label>
                    <select name="kode_bagian" onchange="this.form.submit()" 
                            class="h-10 text-xs font-semibold rounded-xl border border-slate-300 px-3 bg-white text-slate-800 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600 max-w-xs sm:max-w-md">
                        @foreach($bidangMaster as $kb => $bm)
                            <option value="{{ $kb }}" {{ $selectedBidang === $kb ? 'selected' : '' }}>
                                {{ $bm['nama_lengkap'] }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <div class="text-xs text-slate-600 font-medium">
                    Bidang Aktif: <strong class="text-slate-900">{{ $selectedMaster['nama_lengkap'] ?? $selectedBidang }}</strong>
                </div>
            </div>
        </div>

        <!-- Tier 4: Pure Data Table Standar Divisi Iklan -->

        <!-- TAB CONTENT 1: RINCIAN PERKIRAAN -->
        <div x-show="tab === 'perkiraan'" class="space-y-4">
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3.5 px-3.5 text-center w-12">No</th>
                                <th class="py-3.5 px-3.5 min-w-[150px]">Kode Perkiraan</th>
                                <th class="py-3.5 px-3.5 min-w-[240px]">Nama Perkiraan (COA)</th>
                                <th class="py-3.5 px-3.5 text-right min-w-[140px]">Plafon RAB</th>
                                <th class="py-3.5 px-3.5 text-right min-w-[140px]">Realisasi BP</th>
                                <th class="py-3.5 px-3.5 text-right min-w-[140px]">Sisa RAB</th>
                                <th class="py-3.5 px-3.5 text-center w-28">% Serapan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @forelse($coaItems as $key => $c)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $key + 1 }}</td>
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">
                                        {{ $c['kode_perkiraan'] }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-bold text-slate-900">
                                        {{ $c['perkiraan'] }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right">
                                        @if($c['rab'] > 0)
                                            <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($c['rab'], 0, ',', '.') }}</span>
                                            </div>
                                        @else
                                            <span class="text-slate-400 font-mono">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right">
                                        @if($c['realisasi'] > 0)
                                            <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($c['realisasi'], 0, ',', '.') }}</span>
                                            </div>
                                        @else
                                            <span class="text-slate-400 font-mono">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right">
                                        @if($c['rab'] > 0)
                                            <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($c['sisa_rab'], 0, ',', '.') }}</span>
                                            </div>
                                        @else
                                            <span class="text-slate-400 font-mono">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $c['persen'] > 90 ? 'bg-slate-200 text-slate-900' : 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' }}">
                                            {{ $c['persen'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400 italic">
                                        Tidak ada akun perkiraan untuk bidang ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-slate-50 border-t border-slate-200 font-semibold text-xs whitespace-nowrap text-slate-900">
                            <tr>
                                <td colspan="3" class="py-3.5 px-3.5 uppercase font-bold text-slate-900">
                                    TOTAL BIDANG {{ $selectedBidang }} ({{ $selectedMaster['nama'] }})
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-bold text-slate-900 tabular-nums">{{ number_format($totalRabCoa, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-bold text-kp-blue-700 tabular-nums">{{ number_format($totalRealisasiCoa, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-bold text-slate-900 tabular-nums">{{ number_format($totalSisaRabCoa, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono font-bold text-kp-blue-700">
                                    {{ $totalPersenCoa }}%
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT 2: REKAP 21 BIDANG PRIANGAN (Kolom Terpisah) -->
        <div x-show="tab === 'rekap'" class="space-y-4">
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3.5 px-3.5 text-center w-12">No</th>
                                <th class="py-3.5 px-3.5 text-center w-16">Kode</th>
                                <th class="py-3.5 px-3.5 min-w-[180px]">Nama Bidang</th>
                                <th class="py-3.5 px-3.5 min-w-[200px]">Keterangan</th>
                                <th class="py-3.5 px-3.5 text-right min-w-[140px]">Plafon RAB</th>
                                <th class="py-3.5 px-3.5 text-right min-w-[140px]">Realisasi BP</th>
                                <th class="py-3.5 px-3.5 text-right min-w-[140px]">Sisa Plafon</th>
                                <th class="py-3.5 px-3.5 min-w-[180px]">Serapan Anggaran</th>
                                <th class="py-3.5 px-3.5 text-center min-w-[180px]">Aksi Kelola</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @foreach($rabItems as $key => $item)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $key + 1 }}</td>
                                    <td class="py-3.5 px-3.5 text-center font-mono font-bold text-kp-blue-700">
                                        <span class="px-2 py-0.5 rounded bg-kp-blue-50 border border-kp-blue-200">{{ $item['kode_bagian'] }}</span>
                                    </td>
                                    <td class="py-3.5 px-3.5 font-bold text-slate-900">{{ $item['nama'] }}</td>
                                    <td class="py-3.5 px-3.5 text-xs text-slate-500">{{ $item['keterangan'] }}</td>
                                    <td class="py-3.5 px-3.5 text-right">
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($item['plafon'], 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right">
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($item['realisasi'], 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right">
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($item['sisa'], 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5">
                                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                            <span class="text-slate-700 font-mono">{{ $item['persen'] }}%</span>
                                            <span class="text-slate-500 text-[11px] font-normal">
                                                {{ $item['persen'] > 90 ? 'Hampir Habis' : ($item['persen'] > 75 ? 'Peringatan' : 'Aman') }}
                                            </span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="h-1.5 rounded-full bg-kp-blue-600" style="width: {{ min(100, $item['persen']) }}%"></div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('keuangan.anggaran.entry.rab', ['kode_bagian' => $item['kode_bagian'], 'tab' => 'perkiraan', 'periode' => $periode]) }}" 
                                               class="px-2.5 py-1 rounded-lg border border-slate-300 hover:border-kp-blue-500 hover:bg-kp-blue-50 text-kp-blue-700 text-xs font-semibold transition shadow-2xs">
                                                Perkiraan
                                            </a>
                                            <button type="button" 
                                                    @click="openEdit({{ json_encode($item) }})" 
                                                    class="px-2.5 py-1 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-2xs">
                                                Edit Plafon
                                            </button>
                                            <a href="{{ route('keuangan.anggaran.create', ['divisi' => $item['divisi']]) }}" 
                                               class="px-2.5 py-1 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold transition shadow-2xs">
                                                + BP
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MODAL EDIT PLAFON ANGGARAN (Alpine.js) -->
        <div x-cloak x-show="editModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200" @click.outside="editModal = false">
                <form action="{{ route('keuangan.anggaran.entry.rab.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <input type="hidden" name="kode_bagian" :value="activeItem.kode_bagian">

                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-tight">
                                Set Plafon RAB Priangan
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="activeItem.nama + ' (Bagian ' + activeItem.kode_bagian + ')'"></p>
                        </div>
                        <button type="button" @click="editModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                    </div>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Periode Anggaran</label>
                            <input type="text" value="{{ $periode }}" readonly class="w-full text-xs font-mono font-semibold rounded-xl border border-slate-300 px-3 py-2.5 bg-slate-100 text-slate-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Batas Plafon Anggaran (Rp) <span class="text-rose-500">*</span></label>
                            <input type="number" name="plafon_anggaran" x-model="activeItem.plafon" min="0" step="500000" required
                                   class="w-full text-sm font-mono font-bold rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600 text-slate-900">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Keterangan Alokasi</label>
                            <input type="text" name="keterangan" x-model="activeItem.keterangan" placeholder="Contoh: Alokasi anggaran operasional bulanan..."
                                   class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600 text-slate-800">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 mt-4 border-t border-slate-100">
                        <button type="button" @click="editModal = false" class="h-10 px-4 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-5 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                            Simpan Plafon
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
