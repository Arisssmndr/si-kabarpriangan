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

        <!-- Header RAB Priangan Sesuai DOS ANGGARAN.EXE -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <h1 class="text-xl font-black text-blue-950 tracking-tight">
                            RAB PRIANGAN (RENCANA ANGGARAN BIAYA)
                        </h1>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-900 border border-blue-200">
                            Menu Entry DOS
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Penetapan plafon anggaran operasional per bidang (01-21), rincian akun perkiraan (COA), kontrol serapan, dan sisa anggaran berjalan
                    </p>
                </div>
            </div>

            <!-- Filter Periode Bulan -->
            <form method="GET" action="{{ route('keuangan.anggaran.entry.rab') }}" class="flex items-center gap-2">
                <input type="hidden" name="kode_bagian" value="{{ $selectedBidang }}">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <label class="text-xs font-bold text-slate-600">Periode:</label>
                <input type="month" name="periode" value="{{ $periode }}" 
                       class="text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2 bg-slate-50 text-slate-800 focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">
                    Pilih
                </button>
            </form>
        </div>

        <!-- 4 Stat Summary Cards (Aturan 60% Putih, 30% Biru, 10% Kuning) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Plafon (Putih 60%) -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Plafon Anggaran</span>
                <div class="mt-1 text-xl font-black font-mono text-slate-900">
                    Rp {{ number_format($totalPlafon, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-500">
                    Akumulasi 21 Bidang Periode {{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }}
                </div>
            </div>

            <!-- 2. Realisasi BP Terpakai (Biru 30%) -->
            <div class="bg-white border border-blue-200 rounded-2xl p-5 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700">Realisasi BP Terpakai</span>
                <div class="mt-1 text-xl font-black font-mono text-blue-700">
                    Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-blue-700 font-semibold">
                    BP Disetujui &amp; Dicairkan
                </div>
            </div>

            <!-- 3. Sisa Plafon Anggaran (Aksen Kuning 10%) -->
            <div class="bg-white border border-amber-300 rounded-2xl p-5 shadow-xs bg-amber-50/30">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800">Sisa Anggaran Tersedia</span>
                <div class="mt-1 text-xl font-black font-mono text-amber-600">
                    Rp {{ number_format($totalSisa, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-amber-800">
                    Saldo plafon yang belum terpakai
                </div>
            </div>

            <!-- 4. Rata-Rata Serapan (Biru 30%) -->
            <div class="bg-white border border-blue-200 rounded-2xl p-5 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700">Tingkat Serapan Anggaran</span>
                <div class="mt-1 text-xl font-black font-mono text-blue-600">
                    {{ $totalPersen }}%
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, $totalPersen) }}%"></div>
                </div>
            </div>
        </div>

        <!-- TAB NAVIGASI: RINCIAN PERKIRAAN vs REKAP 21 BIDANG -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-2">
            <div class="flex items-center gap-2 overflow-x-auto">
                <button type="button" @click="tab = 'perkiraan'"
                        :class="tab === 'perkiraan' ? 'bg-blue-600 text-white font-extrabold shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold'"
                        class="px-4 py-2 rounded-xl text-xs transition cursor-pointer whitespace-nowrap">
                    Rincian Per Perkiraan (Kode Perkiraan, Perkiraan, RAB, Realisasi, Sisa Rab)
                </button>
                <button type="button" @click="tab = 'rekap'"
                        :class="tab === 'rekap' ? 'bg-blue-600 text-white font-extrabold shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold'"
                        class="px-4 py-2 rounded-xl text-xs transition cursor-pointer whitespace-nowrap">
                    Rekap 21 Bidang Priangan
                </button>
            </div>
            
            <a href="{{ route('keuangan.anggaran.cetak.per-bidang', ['kode_bagian' => $selectedBidang, 'periode' => $periode, 'cetak' => 1]) }}"
               target="_blank"
               class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-900 border border-blue-200 text-xs font-bold transition whitespace-nowrap">
                Cetak Format Dot-Matrix (Bidang {{ $selectedBidang }})
            </a>
        </div>

        <!-- TAB CONTENT 1: RINCIAN KODE PERKIRAAN (COA) PER BIDANG -->
        <div x-show="tab === 'perkiraan'" class="space-y-4">
            
            <!-- Selector Bidang 01 s/d 21 -->
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
                <form method="GET" action="{{ route('keuangan.anggaran.entry.rab') }}" class="flex items-center gap-3 flex-wrap">
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <input type="hidden" name="tab" value="perkiraan">
                    <label class="text-xs font-bold text-slate-700 whitespace-nowrap">Pilih Bidang:</label>
                    <select name="kode_bagian" onchange="this.form.submit()" 
                            class="text-xs font-bold rounded-xl border border-slate-300 px-3 py-2 bg-slate-50 text-slate-800 focus:ring-2 focus:ring-blue-500 max-w-xs sm:max-w-md">
                        @foreach($bidangMaster as $kb => $bm)
                            <option value="{{ $kb }}" {{ $selectedBidang === $kb ? 'selected' : '' }}>
                                {{ $bm['nama_lengkap'] }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold px-3 py-1.5 rounded-xl bg-blue-50 text-blue-900 border border-blue-200">
                        Bidang Aktif: <strong>{{ $selectedMaster['nama_lengkap'] ?? $selectedBidang }}</strong>
                    </span>
                    <a href="{{ route('keuangan.anggaran.create', ['divisi' => $selectedMaster['slug'] ?? 'iklan']) }}" 
                       class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-amber-950 border border-amber-500/30 font-black text-xs transition shadow-xs">
                        + Entry BP Bidang Ini
                    </a>
                </div>
            </div>

            <!-- Tabel Rincian: Kode Perkiraan, Perkiraan, RAB, Realisasi, Sisa Rab -->
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h2 class="text-sm font-black text-blue-950 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            Rincian Anggaran: {{ $selectedMaster['nama_lengkap'] }} (Periode {{ $periode }})
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Struktur perkiraan akuntansi DOS ANGGARAN.EXE &amp; formulir continuous cetak Priangan</p>
                    </div>
                    <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                        {{ count($coaItems) }} Akun Perkiraan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-blue-50/70 border-b border-blue-200 text-blue-950 text-[10px] font-black uppercase tracking-wider">
                                <th class="py-3 px-4 w-52 font-mono">KODE PERKIRAAN</th>
                                <th class="py-3 px-4 min-w-[240px]">PERKIRAAN</th>
                                <th class="py-3 px-4 w-40 text-right font-mono">RAB</th>
                                <th class="py-3 px-4 w-40 text-right font-mono">REALISASI</th>
                                <th class="py-3 px-4 w-40 text-right font-mono">SISA RAB</th>
                                <th class="py-3 px-4 w-28 text-center font-mono">% SERAPAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/80 text-slate-700 font-mono text-[11px]">
                            @forelse($coaItems as $c)
                                <tr class="hover:bg-blue-50/20 transition align-middle">
                                    <!-- KODE PERKIRAAN -->
                                    <td class="py-3 px-4 font-bold text-blue-950">
                                        {{ $c['kode_perkiraan'] }}
                                    </td>

                                    <!-- PERKIRAAN -->
                                    <td class="py-3 px-4 font-sans font-bold text-slate-900 text-xs">
                                        {{ $c['perkiraan'] }}
                                    </td>

                                    <!-- RAB -->
                                    <td class="py-3 px-4 text-right font-bold text-slate-900">
                                        {{ $c['rab'] > 0 ? 'Rp ' . number_format($c['rab'], 0, ',', '.') : '-' }}
                                    </td>

                                    <!-- REALISASI -->
                                    <td class="py-3 px-4 text-right font-bold text-blue-700">
                                        {{ $c['realisasi'] > 0 ? 'Rp ' . number_format($c['realisasi'], 0, ',', '.') : '-' }}
                                    </td>

                                    <!-- SISA RAB -->
                                    <td class="py-3 px-4 text-right font-bold {{ $c['sisa_rab'] >= 0 ? 'text-amber-800' : 'text-rose-600' }}">
                                        {{ $c['rab'] > 0 ? 'Rp ' . number_format($c['sisa_rab'], 0, ',', '.') : '-' }}
                                    </td>

                                    <!-- % SERAPAN -->
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold {{ $c['persen'] > 90 ? 'bg-rose-100 text-rose-800' : ($c['persen'] > 75 ? 'bg-amber-100 text-amber-900' : 'bg-blue-50 text-blue-800') }}">
                                            {{ $c['persen'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400 italic">
                                        Tidak ada akun perkiraan untuk bidang ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-blue-50/40 border-t-2 border-slate-900 font-mono text-xs font-black">
                            <tr>
                                <td colspan="2" class="py-3.5 px-4 uppercase text-blue-950 font-black">
                                    TOTAL BIDANG {{ $selectedBidang }} ({{ $selectedMaster['nama'] }})
                                </td>
                                <td class="py-3.5 px-4 text-right text-slate-900">
                                    Rp {{ number_format($totalRabCoa, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right text-blue-700">
                                    Rp {{ number_format($totalRealisasiCoa, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right {{ $totalSisaRabCoa >= 0 ? 'text-amber-900' : 'text-rose-600' }}">
                                    Rp {{ number_format($totalSisaRabCoa, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center text-blue-900">
                                    {{ $totalPersenCoa }}%
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT 2: REKAP 21 BIDANG PRIANGAN -->
        <div x-show="tab === 'rekap'" class="space-y-4">
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-blue-950 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            Alokasi Plafon RAB per Bidang (01 s/d 21)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Pantau dan kelola batas maksimal pengajuan BP masing-masing bidang</p>
                    </div>
                    <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                        Periode: {{ $periode }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-blue-50/70 border-b border-blue-200 text-blue-950 text-[10px] font-black uppercase tracking-wider">
                                <th class="py-3 px-4 w-16 text-center">Kode</th>
                                <th class="py-3 px-4 w-52">Bidang / Unit Kerja</th>
                                <th class="py-3 px-4 w-44 text-right">Plafon RAB (Rp)</th>
                                <th class="py-3 px-4 w-44 text-right">Realisasi BP (Rp)</th>
                                <th class="py-3 px-4 w-44 text-right">Sisa Plafon (Rp)</th>
                                <th class="py-3 px-4 min-w-[200px]">Serapan Anggaran</th>
                                <th class="py-3 px-4 w-44 text-center">Aksi Kelola</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/80 text-slate-700">
                            @foreach($rabItems as $item)
                                <tr class="hover:bg-blue-50/20 transition align-middle">
                                    <!-- Kode -->
                                    <td class="py-4 px-4 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl font-mono text-xs font-black bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                                            {{ $item['kode_bagian'] }}
                                        </span>
                                    </td>

                                    <!-- Divisi -->
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-slate-900 text-sm">{{ $item['nama'] }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $item['keterangan'] }}</div>
                                    </td>

                                    <!-- Plafon -->
                                    <td class="py-4 px-4 text-right font-mono font-black text-slate-900 text-xs">
                                        Rp {{ number_format($item['plafon'], 0, ',', '.') }}
                                    </td>

                                    <!-- Realisasi -->
                                    <td class="py-4 px-4 text-right font-mono font-bold text-blue-700 text-xs">
                                        Rp {{ number_format($item['realisasi'], 0, ',', '.') }}
                                    </td>

                                    <!-- Sisa -->
                                    <td class="py-4 px-4 text-right font-mono font-bold text-slate-900 text-xs">
                                        Rp {{ number_format($item['sisa'], 0, ',', '.') }}
                                    </td>

                                    <!-- Progress Serapan -->
                                    <td class="py-4 px-4">
                                        <div class="flex items-center justify-between text-[11px] font-bold mb-1">
                                            <span class="text-slate-600">{{ $item['persen'] }}%</span>
                                            <span class="{{ $item['persen'] > 90 ? 'text-rose-600' : ($item['persen'] > 75 ? 'text-amber-600' : 'text-blue-700') }}">
                                                {{ $item['persen'] > 90 ? 'Hampir Habis' : ($item['persen'] > 75 ? 'Peringatan' : 'Aman') }}
                                            </span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/60">
                                            <div class="h-2 rounded-full transition-all duration-300 {{ $item['persen'] > 90 ? 'bg-rose-500' : ($item['persen'] > 75 ? 'bg-amber-500' : 'bg-blue-600') }}" 
                                                 style="width: {{ min(100, $item['persen']) }}%"></div>
                                        </div>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-4 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                            <a href="{{ route('keuangan.anggaran.entry.rab', ['kode_bagian' => $item['kode_bagian'], 'tab' => 'perkiraan', 'periode' => $periode]) }}" 
                                               class="px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-900 border border-blue-200 font-bold text-xs transition shadow-2xs">
                                                Perkiraan
                                            </a>
                                            <button type="button" 
                                                    @click="openEdit({{ json_encode($item) }})" 
                                                    class="px-2.5 py-1.5 rounded-lg border border-slate-300 hover:border-blue-400 bg-white hover:bg-blue-50 text-slate-700 font-bold text-xs transition shadow-2xs">
                                                Edit Plafon
                                            </button>
                                            <!-- Tombol Aksen Kuning 10% -->
                                            <a href="{{ route('keuangan.anggaran.create', ['divisi' => $item['divisi']]) }}" 
                                               class="px-2.5 py-1.5 rounded-lg bg-amber-400 hover:bg-amber-300 text-amber-950 border border-amber-500/30 font-black text-xs transition shadow-2xs">
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
        <div x-cloak x-show="editModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" 
                     @click="editModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="editModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    
                    <form action="{{ route('keuangan.anggaran.entry.rab.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="periode" value="{{ $periode }}">
                        <input type="hidden" name="kode_bagian" :value="activeItem.kode_bagian">

                        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">
                                    Set Plafon RAB Priangan
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5" x-text="activeItem.nama + ' (Bagian ' + activeItem.kode_bagian + ')'"></p>
                            </div>
                            <button type="button" @click="editModal = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold">Tutup</button>
                        </div>

                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Periode Anggaran</label>
                                <input type="text" value="{{ $periode }}" readonly class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2 bg-slate-100 text-slate-600">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Batas Plafon Anggaran (Rp) <span class="text-rose-500">*</span></label>
                                <input type="number" name="plafon_anggaran" x-model="activeItem.plafon" min="0" step="500000" required
                                       class="w-full text-sm font-mono font-black rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan / Keterangan Alokasi</label>
                                <input type="text" name="keterangan" x-model="activeItem.keterangan" placeholder="Contoh: Alokasi anggaran operasional bulanan..."
                                       class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 text-slate-800">
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                            <button type="button" @click="editModal = false" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                Simpan Plafon
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
