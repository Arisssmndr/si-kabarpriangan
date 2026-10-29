<x-app-layout>
    <div x-data="{ 
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
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shadow-xs shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 animate-pulse"></span>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">
                            RAB PRIANGAN (RENCANA ANGGARAN BIAYA)
                        </h1>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Menu Entry DOS
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Penetapan plafon anggaran operasional per bidang (01-07), kontrol serapan, dan sisa anggaran berjalan
                    </p>
                </div>
            </div>

            <!-- Filter Periode Bulan -->
            <form method="GET" action="{{ route('keuangan.anggaran.entry.rab') }}" class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-600">Periode:</label>
                <input type="month" name="periode" value="{{ $periode }}" 
                       class="text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2 bg-slate-50 text-slate-800 focus:ring-2 focus:ring-purple-500">
                <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Pilih
                </button>
            </form>
        </div>

        <!-- 4 Stat Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Plafon -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Plafon Anggaran</span>
                <div class="mt-1 text-xl font-black font-mono text-slate-900">
                    Rp {{ number_format($totalPlafon, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-500">
                    Akumulasi 7 Bidang Periode {{ \Carbon\Carbon::parse($periode)->translatedFormat('F Y') }}
                </div>
            </div>

            <!-- 2. Realisasi BP Terpakai -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Realisasi BP Terpakai</span>
                <div class="mt-1 text-xl font-black font-mono text-purple-700">
                    Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-purple-600 font-semibold">
                    BP Disetujui &amp; Dicairkan
                </div>
            </div>

            <!-- 3. Sisa Plafon Anggaran -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sisa Anggaran Tersedia</span>
                <div class="mt-1 text-xl font-black font-mono text-emerald-600">
                    Rp {{ number_format($totalSisa, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-500">
                    Saldo plafon yang belum terpakai
                </div>
            </div>

            <!-- 4. Rata-Rata Serapan -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tingkat Serapan Anggaran</span>
                <div class="mt-1 text-xl font-black font-mono text-blue-600">
                    {{ $totalPersen }}%
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, $totalPersen) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Tabel RAB Priangan per Bidang -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        Alokasi Plafon RAB per Bidang (01 s/d 07)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pantau dan kelola batas maksimal pengajuan BP masing-masing bidang</p>
                </div>
                <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Periode: {{ $periode }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-700 text-[11px] font-extrabold uppercase tracking-wider">
                            <th class="py-3 px-4 w-16 text-center">Kode</th>
                            <th class="py-3 px-4 w-52">Bidang / Unit Kerja</th>
                            <th class="py-3 px-4 w-44 text-right">Plafon RAB (Rp)</th>
                            <th class="py-3 px-4 w-44 text-right">Realisasi BP (Rp)</th>
                            <th class="py-3 px-4 w-44 text-right">Sisa Plafon (Rp)</th>
                            <th class="py-3 px-4 min-w-[200px]">Serapan Anggaran</th>
                            <th class="py-3 px-4 w-36 text-center">Aksi Kelola</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/80 text-slate-700">
                        @foreach($rabItems as $item)
                            <tr class="hover:bg-slate-50/60 transition align-middle">
                                <!-- Kode -->
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl font-mono text-xs font-black bg-purple-50 text-purple-700 border border-purple-200 shadow-2xs">
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
                                <td class="py-4 px-4 text-right font-mono font-bold text-purple-700 text-xs">
                                    Rp {{ number_format($item['realisasi'], 0, ',', '.') }}
                                </td>

                                <!-- Sisa -->
                                <td class="py-4 px-4 text-right font-mono font-bold text-emerald-700 text-xs">
                                    Rp {{ number_format($item['sisa'], 0, ',', '.') }}
                                </td>

                                <!-- Progress Serapan -->
                                <td class="py-4 px-4">
                                    <div class="flex items-center justify-between text-[11px] font-bold mb-1">
                                        <span class="text-slate-600">{{ $item['persen'] }}%</span>
                                        <span class="{{ $item['persen'] > 90 ? 'text-rose-600' : ($item['persen'] > 75 ? 'text-amber-600' : 'text-emerald-600') }}">
                                            {{ $item['persen'] > 90 ? 'Hampir Habis' : ($item['persen'] > 75 ? 'Peringatan' : 'Aman') }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/60">
                                        <div class="h-2 rounded-full transition-all duration-300 {{ $item['persen'] > 90 ? 'bg-rose-500' : ($item['persen'] > 75 ? 'bg-amber-500' : 'bg-emerald-500') }}" 
                                             style="width: {{ min(100, $item['persen']) }}%"></div>
                                    </div>
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                @click="openEdit({{ json_encode($item) }})" 
                                                class="px-2.5 py-1.5 rounded-lg border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition shadow-2xs">
                                            Edit Plafon
                                        </button>
                                        <a href="{{ route('keuangan.anggaran.create', ['divisi' => $item['divisi']]) }}" 
                                           class="px-2.5 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold text-xs transition">
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
                            <button type="button" @click="editModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
                        </div>

                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Periode Anggaran</label>
                                <input type="text" value="{{ $periode }}" readonly class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2 bg-slate-100 text-slate-600">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Batas Plafon Anggaran (Rp) <span class="text-rose-500">*</span></label>
                                <input type="number" name="plafon_anggaran" x-model="activeItem.plafon" min="0" step="500000" required
                                       class="w-full text-sm font-mono font-black rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-purple-500 text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan / Keterangan Alokasi</label>
                                <input type="text" name="keterangan" x-model="activeItem.keterangan" placeholder="Contoh: Alokasi anggaran operasional bulanan..."
                                       class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-purple-500 text-slate-800">
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                            <button type="button" @click="editModal = false" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                Simpan Plafon
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
