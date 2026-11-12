<x-app-layout>
    <div class="space-y-5">

        <!-- ============================================================== -->
        <!-- 1. HEADER HALAMAN (PALET 60-30-10 & ACTION BAR)                -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Koreksi Retur Sirkulasi
                        </h1>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                            Credit Note (CN-SIR)
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Penerbitan nota kredit retur koran agen dan mutasi saldo piutang berjalan.
                    </p>
                </div>
            </div>

            <!-- Action Toolbar Kanan -->
            <div class="flex items-center gap-3 shrink-0 flex-wrap">
                <div class="hidden md:flex items-center gap-1.5 text-xs font-medium text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-200">
                    <span class="text-slate-400">Bukti Berikutnya:</span>
                    <span class="text-slate-900 font-mono font-bold">{{ $nextNoBukti }}</span>
                </div>

                <!-- Tombol Utama: Terbitkan Retur (Buka Modal Mengambang) -->
                <button type="button" 
                        onclick="openModalKoreksiSirkulasi()"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Terbitkan Retur (CN-SIR)</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- 2. KPI / STATISTIK CEPAT (METRICS RIBBON)                      -->
        <!-- ============================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Nota Retur</span>
                    <span class="text-xl font-bold text-slate-900 mt-1 block">{{ number_format($stats['total_nota'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-500 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akumulasi Retur</span>
                    <span class="text-2xl font-bold text-kp-blue-700 mt-1 block font-mono">Rp {{ number_format($stats['total_nominal'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 border border-kp-blue-200 flex items-center justify-center text-kp-blue-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Agen Disesuaikan</span>
                    <span class="text-2xl font-bold text-slate-900 mt-1 block font-mono">{{ number_format($stats['total_agen'] ?? 0, 0, ',', '.') }} Agen</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 border border-kp-blue-200 flex items-center justify-center text-kp-blue-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 3. TOOLBAR PENCARIAN & FILTER                                  -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.sirkulasi.koreksi') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik no bukti, nama agen, atau keterangan..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Dari Tanggal
                    </label>
                    <input type="date" 
                           name="dari" 
                           value="{{ request('dari') }}" 
                           class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Sampai Tanggal
                    </label>
                    <input type="date" 
                           name="sampai" 
                           value="{{ request('sampai') }}" 
                           class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'dari', 'sampai']))
                        <a href="{{ route('accounting.sirkulasi.koreksi') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- ============================================================== -->
        <!-- 4. TABEL RIWAYAT NOTA KREDIT SIRKULASI (FULL-WIDTH)            -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[360px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] font-bold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Bukti CN</th>
                            <th class="py-3 px-3.5">Nama Agen</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5 text-right w-36">Nilai Retur</th>
                            <th class="py-3 px-3.5">Keterangan / BAPR</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($daftarCn as $key => $cn)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-400 font-normal">
                                    {{ $daftarCn->firstItem() + $key }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $cn->nomor_bukti }}
                                </td>
                                <td class="py-3.5 px-3.5 font-bold text-slate-900">
                                    {{ $cn->nama_entitas }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    {{ \Carbon\Carbon::parse($cn->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($cn->nominal_koreksi, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 max-w-sm truncate text-slate-600 font-normal" title="{{ $cn->keterangan }}">
                                    {{ $cn->keterangan ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center" 
                                    x-data="{ open: false }" 
                                    :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <button type="button" 
                                                @click="open = !open" 
                                                class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                :class="{ 'text-slate-900 bg-slate-100': open }"
                                                title="Pilihan Aksi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="5" r="1.5" />
                                                <circle cx="12" cy="12" r="1.5" />
                                                <circle cx="12" cy="19" r="1.5" />
                                            </svg>
                                        </button>

                                        <div x-cloak 
                                             x-show="open" 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">
                                            
                                            <!-- Cetak Bukti Retur -->
                                            <a href="{{ route('accounting.sirkulasi.koreksi.cetak', $cn->id) }}" 
                                               target="_blank"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                <span>Cetak Bukti</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                    Belum ada data penerbitan nota kredit retur sirkulasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($daftarCn->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $daftarCn->links() }}
                </div>
            @endif
        </div>

        <!-- ============================================================== -->
        <!-- 5. MODAL FORM: TERBITKAN NOTA RETUR (GAYA DIVISI IKLAN)        -->
        <!-- ============================================================== -->
        <div id="modalKoreksiSirkulasi" 
             x-data="{
                agenId: '',
                saldoPiutang: 0,
                eksemplar: 0,
                harga: 3500,
                loading: false,
                get totalRetur() {
                    return (this.eksemplar || 0) * (this.harga || 0);
                },
                formatRp(val) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(val || 0));
                },
                fetchSaldo() {
                    if (!this.agenId) {
                        this.saldoPiutang = 0;
                        return;
                    }
                    this.loading = true;
                    fetch(`/accounting/sirkulasi/agen/${this.agenId}/saldo`)
                        .then(res => res.json())
                        .then(data => {
                            this.loading = false;
                            if (data.success) {
                                this.saldoPiutang = data.saldo_berjalan || 0;
                            }
                        })
                        .catch(() => { this.loading = false; });
                }
             }"
             class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[92vh] overflow-hidden">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <h3 class="text-base font-bold text-slate-900">Terbitkan Retur Koran Agen</h3>
                    <button type="button" onclick="closeModalKoreksiSirkulasi()" 
                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer" 
                            title="Tutup">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Form Input Retur -->
                <form method="POST" action="{{ route('accounting.sirkulasi.koreksi.store') }}" class="flex flex-col flex-1 min-h-0">
                    @csrf

                    <div class="p-6 space-y-4 text-xs overflow-y-auto flex-1">
                        <!-- Baris 1: No Bukti & Tanggal -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                    No. Bukti CN-SIR
                                </label>
                                <input type="text" 
                                       name="nomor_bukti" 
                                       value="{{ $nextNoBukti }}" 
                                       readonly
                                       class="w-full h-11 px-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono font-bold text-slate-600 cursor-not-allowed select-none" />
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                    Tanggal Retur <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" 
                                       name="tanggal" 
                                       value="{{ date('Y-m-d') }}" 
                                       required
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                            </div>
                        </div>

                        <!-- Baris 2: Pilih Agen -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Pilih Agen Sirkulasi <span class="text-rose-500">*</span>
                            </label>
                            <select name="agen_id" 
                                    x-model="agenId"
                                    @change="fetchSaldo()"
                                    required 
                                    class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                <option value="">-- Pilih Agen --</option>
                                @foreach($daftarAgen as $ag)
                                    <option value="{{ $ag->id }}">{{ $ag->nama_agen }} (Wilayah: {{ $ag->wilayah ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Info Saldo Piutang Berjalan Agen -->
                        <div x-show="agenId" x-cloak class="px-3.5 py-2.5 rounded-xl bg-kp-blue-50/70 border border-kp-blue-200/80 flex items-center justify-between text-xs">
                            <span class="text-slate-600">Saldo Piutang Berjalan:</span>
                            <span class="font-mono font-bold text-kp-blue-700 text-sm" x-text="formatRp(saldoPiutang)">Rp 0</span>
                        </div>

                        <!-- Baris 3: Jumlah Eksemplar & Harga -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                    Jumlah Eksemplar Retur <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" 
                                       name="jumlah_eksemplar" 
                                       x-model.number="eksemplar"
                                       min="1" 
                                       placeholder="0"
                                       required
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                    Harga / Eksemplar (Rp) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" 
                                       name="harga_eksemplar" 
                                       x-model.number="harga"
                                       min="1" 
                                       required
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                            </div>
                        </div>

                        <!-- Kotak Total Retur (Kalkulator Otomatis) -->
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-600">Total Potongan Retur:</span>
                            <span class="text-base font-bold font-mono text-kp-blue-700" x-text="formatRp(totalRetur)">Rp 0</span>
                        </div>

                        <!-- Baris 4: Keterangan / No. BAPR -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Alasan / No. Berita Acara (BAPR) <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="alasan" 
                                      rows="2" 
                                      required 
                                      placeholder="Berita acara retur koran sisa edisi, komplain pengiriman..." 
                                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0">
                        <button type="button" 
                                onclick="closeModalKoreksiSirkulasi()" 
                                class="h-11 px-5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="h-11 px-6 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-xl text-sm font-bold transition cursor-pointer shadow-xs">
                            Simpan Retur (CN-SIR)
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function openModalKoreksiSirkulasi() {
            const modal = document.getElementById('modalKoreksiSirkulasi');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeModalKoreksiSirkulasi() {
            const modal = document.getElementById('modalKoreksiSirkulasi');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalKoreksiSirkulasi();
            }
        });
    </script>
    @endpush
</x-app-layout>
