<x-app-layout>
    @php
        $isShiftClosed = in_array($activeShift->status, ['closed', 'ditutup']);
        $prefillPecahan = ($isShiftClosed && is_array($activeShift->rincian_pecahan)) ? $activeShift->rincian_pecahan : [];
    @endphp
    <div class="space-y-5" x-data="{
        showReopenModal: false,
        reopenTarget: { id: null, tanggal: '' },
        showDetailModal: false,
        selectedRiwayat: null,
        compareId: '',
        riwayatItems: @js($riwayatPayloads ?? []),
        compareOptions: @js($compareOptions ?? []),
        saldoTeoritis: {{ $saldoTeoritis }},
        p100: {{ (int) old('pecahan_100k', $prefillPecahan['100k'] ?? 0) }},
        p50: {{ (int) old('pecahan_50k', $prefillPecahan['50k'] ?? 0) }},
        p20: {{ (int) old('pecahan_20k', $prefillPecahan['20k'] ?? 0) }},
        p10: {{ (int) old('pecahan_10k', $prefillPecahan['10k'] ?? 0) }},
        p5: {{ (int) old('pecahan_5k', $prefillPecahan['5k'] ?? 0) }},
        p2: {{ (int) old('pecahan_2k', $prefillPecahan['2k'] ?? 0) }},
        p1: {{ (int) old('pecahan_1k', $prefillPecahan['1k'] ?? 0) }},
        koin: {{ (float) old('pecahan_koin', $prefillPecahan['koin'] ?? 0) }},
        get rincianPecahan() {
            const list = [
                { key: '100k', label: 'Rp 100.000', nominal: 100000, qty: parseInt(this.p100 || 0), unit: 'lbr' },
                { key: '50k',  label: 'Rp 50.000',  nominal: 50000,  qty: parseInt(this.p50 || 0),  unit: 'lbr' },
                { key: '20k',  label: 'Rp 20.000',  nominal: 20000,  qty: parseInt(this.p20 || 0),  unit: 'lbr' },
                { key: '10k',  label: 'Rp 10.000',  nominal: 10000,  qty: parseInt(this.p10 || 0),  unit: 'lbr' },
                { key: '5k',   label: 'Rp 5.000',   nominal: 5000,   qty: parseInt(this.p5 || 0),   unit: 'lbr' },
                { key: '2k',   label: 'Rp 2.000',   nominal: 2000,   qty: parseInt(this.p2 || 0),   unit: 'lbr' },
                { key: '1k',   label: 'Rp 1.000',   nominal: 1000,   qty: parseInt(this.p1 || 0),   unit: 'lbr' },
            ].map(item => ({ ...item, subtotal: Math.max(0, item.qty) * item.nominal }));
            const koinVal = Math.max(0, parseFloat(this.koin || 0));
            if (koinVal > 0) {
                list.push({ key: 'koin', label: 'Koin Logam', nominal: 1, qty: koinVal, unit: 'nominal', subtotal: koinVal });
            }
            return list.filter(i => i.qty > 0);
        },
        get totalLembar() {
            return Math.max(0, parseInt(this.p100 || 0)) +
                   Math.max(0, parseInt(this.p50 || 0)) +
                   Math.max(0, parseInt(this.p20 || 0)) +
                   Math.max(0, parseInt(this.p10 || 0)) +
                   Math.max(0, parseInt(this.p5 || 0)) +
                   Math.max(0, parseInt(this.p2 || 0)) +
                   Math.max(0, parseInt(this.p1 || 0));
        },
        get totalFisikLaci() {
            return (Math.max(0, parseInt(this.p100 || 0)) * 100000) +
                   (Math.max(0, parseInt(this.p50 || 0)) * 50000) +
                   (Math.max(0, parseInt(this.p20 || 0)) * 20000) +
                   (Math.max(0, parseInt(this.p10 || 0)) * 10000) +
                   (Math.max(0, parseInt(this.p5 || 0)) * 5000) +
                   (Math.max(0, parseInt(this.p2 || 0)) * 2000) +
                   (Math.max(0, parseInt(this.p1 || 0)) * 1000) +
                   (Math.max(0, parseFloat(this.koin || 0)));
        },
        get totalFisikHitung() {
            return this.totalFisikLaci;
        },
        get selisih() {
            return this.totalFisikHitung - this.saldoTeoritis;
        },
        openDetailModal(id) {
            const found = this.riwayatItems.find(r => r.id === id) || this.compareOptions.find(r => r.id === id);
            if (!found) return;
            this.selectedRiwayat = found;
            const others = this.compareOptions.filter(r => r.id !== id);
            this.compareId = others.length > 0 ? String(others[0].id) : '';
            this.showDetailModal = true;
        },
        get compareRiwayat() {
            if (!this.compareId) return null;
            return this.compareOptions.find(r => String(r.id) === String(this.compareId)) || null;
        },
        getCompareRow(key) {
            if (!this.compareRiwayat || !this.compareRiwayat.pecahan) return null;
            return this.compareRiwayat.pecahan.find(p => p.key === key) || null;
        },
        formatRupiah(val) {
            return new Intl.NumberFormat('id-ID').format(val || 0);
        }
    }">

        <!-- ============================================================== -->
        <!-- 1. HEADER HALAMAN (PALET 60-30-10 & ACTION BAR)                -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Tutup Kas Harian (Closing Shift)
                        </h1>
                        @if(in_array($activeShift->status, ['closed', 'ditutup']))
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                Kas Ditutup (Terkunci)
                            </span>
                        @elseif($activeShift->reopen_status === 'approved')
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-sky-50 text-kp-blue-700 border border-sky-200">
                                Dibuka Kembali (ACC Akuntansi)
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-sky-50 text-kp-blue-700 border border-kp-blue-200">
                                Shift Aktif (Terbuka)
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Kunci seluruh transaksi kas harian, hitung fisik uang meja kasir, dan rekonsiliasi data dengan Bagian Akuntansi.
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3 shrink-0">
                <a href="{{ route('kasir.utilitas.buka-kas') }}" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98]">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>
                    <span>Buka Kembali Kas</span>
                </a>
                <div class="text-left sm:text-right">
                    <span class="text-xs text-slate-400 font-medium block">Kasir Penanggung Jawab:</span>
                    <span class="text-sm font-bold text-slate-900 font-mono">{{ Auth::user()->name ?? 'Kasir Priangan' }}</span>
                    <span class="text-xs text-slate-400 block">{{ \Carbon\Carbon::parse($activeShift->tanggal_shift ?? $activeShift->tgl_shift ?? today())->translatedFormat('d F Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-3.5 bg-sky-50 border border-sky-200 rounded-xl text-xs font-semibold text-[#0E527B] flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-700 text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs font-semibold text-rose-800 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(in_array($activeShift->status, ['closed', 'ditutup']))
            <!-- Banner Peringatan Kas Hari Ini Sudah Ditutup -->
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-xs">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-rose-900">
                            Kas Tanggal {{ \Carbon\Carbon::parse($activeShift->tanggal_shift ?? $activeShift->tgl_shift ?? today())->translatedFormat('d F Y') }} Telah Ditutup (Closed)
                        </h4>
                        <p class="text-xs text-rose-700 mt-0.5 leading-relaxed">
                            Seluruh input penerimaan (KI) dan pengeluaran (KO) untuk tanggal ini telah dikunci. Apabila terdapat koreksi atau transaksi susulan yang harus diinput pada tanggal ini, silakan ajukan Berita Acara Buka Kembali Kas ke Bagian Akuntansi.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @if($activeShift->reopen_status === 'requested' || $activeShift->reopen_requested)
                        <span class="px-3.5 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl border border-slate-300">
                            Menunggu ACC Accounting
                        </span>
                    @else
                        <button type="button"
                                @click="reopenTarget = { id: {{ $activeShift->id }}, tanggal: '{{ \Carbon\Carbon::parse($activeShift->tanggal_shift ?? $activeShift->tgl_shift ?? today())->format('d/m/Y') }}' }; showReopenModal = true"
                                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] cursor-pointer">
                            Ajukan Buka Kembali Kas
                        </button>
                    @endif
                </div>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- 2. KPI / STATISTIK CEPAT (METRICS RIBBON 60-30-10)             -->
        <!-- ============================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3.5">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Awal Fisik</span>
                <span class="text-2xl font-bold font-mono text-slate-900 mt-1 block">
                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Modal pembuka shift</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Masuk (KI)</span>
                <span class="text-2xl font-bold font-mono text-kp-blue-700 mt-1 block">
                    + Rp {{ number_format($totalKi, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Penerimaan kas laci</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Keluar (KO)</span>
                <span class="text-2xl font-bold font-mono text-rose-600 mt-1 block">
                    - Rp {{ number_format($totalKo, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Realisasi BP & Komisi</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Akhir Teoritis</span>
                <span class="text-2xl font-bold font-mono text-slate-900 mt-1 block">
                    Rp {{ number_format($saldoTeoritis, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Target uang fisik laci</span>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 3. FORM DENOMINASI PECAHAN FISIK UANG (9 GAMBAR RUPIAH)       -->
        <!-- ============================================================== -->
        <form method="POST" action="{{ route('kasir.utilitas.closing.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            @csrf

            <!-- Kolom Input Pecahan Uang Kertas & Logam -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs lg:col-span-2 space-y-4">
                <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-kp-blue-600"></span>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                            Rincian Fisik Uang Meja Kasir (Denominasi Rupiah)
                        </h3>
                    </div>
                    <span class="text-xs text-slate-400">Masukkan jumlah lembar per pecahan</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Pecahan 100k (Soekarno & Hatta) -->
                    <div class="flex items-center justify-between gap-2 p-2.5 bg-slate-50 rounded-xl border border-slate-200 hover:border-kp-blue-300 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ asset('images/rupiah/100k.jpg') }}?v=2" alt="Uang Kertas Rp 100.000"
                                 class="w-16 h-8 object-cover rounded shadow-2xs border border-slate-300 shrink-0 select-none">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-900 block truncate">Rp 100.000</span>
                                <span class="text-[10px] text-slate-500 font-mono block truncate">Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format((p100 || 0) * 100000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_100k" x-model="p100" min="0" placeholder="0"
                               class="w-20 shrink-0 text-right text-sm py-1.5 px-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 font-mono font-bold">
                    </div>

                    <!-- Pecahan 50k (Ir. H. Djuanda) -->
                    <div class="flex items-center justify-between gap-2 p-2.5 bg-slate-50 rounded-xl border border-slate-200 hover:border-kp-blue-300 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ asset('images/rupiah/50k.jpg') }}?v=2" alt="Uang Kertas Rp 50.000"
                                 class="w-16 h-8 object-cover rounded shadow-2xs border border-slate-300 shrink-0 select-none">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-900 block truncate">Rp 50.000</span>
                                <span class="text-[10px] text-slate-500 font-mono block truncate">Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format((p50 || 0) * 50000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_50k" x-model="p50" min="0" placeholder="0"
                               class="w-20 shrink-0 text-right text-sm py-1.5 px-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 font-mono font-bold">
                    </div>

                    <!-- Pecahan 20k (Dr. G.S.S.J. Ratulangi) -->
                    <div class="flex items-center justify-between gap-2 p-2.5 bg-slate-50 rounded-xl border border-slate-200 hover:border-kp-blue-300 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ asset('images/rupiah/20k.jpg') }}?v=2" alt="Uang Kertas Rp 20.000"
                                 class="w-16 h-8 object-cover rounded shadow-2xs border border-slate-300 shrink-0 select-none">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-900 block truncate">Rp 20.000</span>
                                <span class="text-[10px] text-slate-500 font-mono block truncate">Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format((p20 || 0) * 20000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_20k" x-model="p20" min="0" placeholder="0"
                               class="w-20 shrink-0 text-right text-sm py-1.5 px-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 font-mono font-bold">
                    </div>

                    <!-- Pecahan 10k (Frans Kaisiepo) -->
                    <div class="flex items-center justify-between gap-2 p-2.5 bg-slate-50 rounded-xl border border-slate-200 hover:border-kp-blue-300 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ asset('images/rupiah/10k.jpg') }}?v=2" alt="Uang Kertas Rp 10.000"
                                 class="w-16 h-8 object-cover rounded shadow-2xs border border-slate-300 shrink-0 select-none">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-900 block truncate">Rp 10.000</span>
                                <span class="text-[10px] text-slate-500 font-mono block truncate">Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format((p10 || 0) * 10000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_10k" x-model="p10" min="0" placeholder="0"
                               class="w-20 shrink-0 text-right text-sm py-1.5 px-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 font-mono font-bold">
                    </div>

                    <!-- Pecahan 5k (Dr. K.H. Idham Chalid) -->
                    <div class="flex items-center justify-between gap-2 p-2.5 bg-slate-50 rounded-xl border border-slate-200 hover:border-kp-blue-300 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ asset('images/rupiah/5k.jpg') }}?v=2" alt="Uang Kertas Rp 5.000"
                                 class="w-16 h-8 object-cover rounded shadow-2xs border border-slate-300 shrink-0 select-none">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-900 block truncate">Rp 5.000</span>
                                <span class="text-[10px] text-slate-500 font-mono block truncate">Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format((p5 || 0) * 5000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_5k" x-model="p5" min="0" placeholder="0"
                               class="w-20 shrink-0 text-right text-sm py-1.5 px-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 font-mono font-bold">
                    </div>

                    <!-- Pecahan 2k (Mohammad Hoesni Thamrin) -->
                    <div class="flex items-center justify-between gap-2 p-2.5 bg-slate-50 rounded-xl border border-slate-200 hover:border-kp-blue-300 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ asset('images/rupiah/2k.jpg') }}?v=2" alt="Uang Kertas Rp 2.000"
                                 class="w-16 h-8 object-cover rounded shadow-2xs border border-slate-300 shrink-0 select-none">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-900 block truncate">Rp 2.000</span>
                                <span class="text-[10px] text-slate-500 font-mono block truncate">Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format((p2 || 0) * 2000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_2k" x-model="p2" min="0" placeholder="0"
                               class="w-20 shrink-0 text-right text-sm py-1.5 px-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 font-mono font-bold">
                    </div>

                    <!-- Pecahan 1k (Tjut Meutia) -->
                    <div class="flex items-center justify-between gap-2 p-2.5 bg-slate-50 rounded-xl border border-slate-200 hover:border-kp-blue-300 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ asset('images/rupiah/1k.jpg') }}?v=2" alt="Uang Kertas Rp 1.000"
                                 class="w-16 h-8 object-cover rounded shadow-2xs border border-slate-300 shrink-0 select-none">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-900 block truncate">Rp 1.000</span>
                                <span class="text-[10px] text-slate-500 font-mono block truncate">Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format((p1 || 0) * 1000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_1k" x-model="p1" min="0" placeholder="0"
                               class="w-20 shrink-0 text-right text-sm py-1.5 px-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 font-mono font-bold">
                    </div>

                    <!-- Uang Koin / Logam (TE 2016 - Rp 1.000 & Rp 500) -->
                    <div class="flex items-center justify-between gap-2 p-2.5 bg-slate-50 rounded-xl border border-slate-200 hover:border-kp-blue-300 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-16 h-8 shrink-0 flex items-center justify-center relative select-none">
                                <div class="w-7 h-7 rounded-full overflow-hidden border border-slate-300 shadow-2xs -mr-2 z-0">
                                    <img src="{{ asset('images/rupiah/koin_500.jpg') }}" alt="Koin Rp 500" class="w-full h-full object-cover scale-105">
                                </div>
                                <div class="w-8 h-8 rounded-full overflow-hidden border border-slate-300 shadow-2xs z-10">
                                    <img src="{{ asset('images/rupiah/koin_1000.jpg') }}" alt="Koin Rp 1.000" class="w-full h-full object-cover scale-105">
                                </div>
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-900 block truncate">Total Koin Logam (Rp)</span>
                                <span class="text-[10px] text-slate-500 font-mono block truncate">Pecahan 1.000, 500, 200, 100</span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_koin" x-model="koin" min="0" placeholder="Rp 0"
                               class="w-20 shrink-0 text-right text-sm py-1.5 px-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 font-mono font-bold">
                    </div>
                </div>

                <!-- Catatan Serah Terima -->
                <div class="pt-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Catatan Serah Terima / Keterangan Tutup Kas
                    </label>
                    <textarea name="catatan" 
                              rows="2" 
                              placeholder="Catatan fisik uang, titipan brankas, atau kendala operasional kasir..."
                              class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition"></textarea>
                </div>
            </div>

            <!-- Kolom Ringkasan & Konfirmasi Kunci Shift -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs lg:col-span-1 space-y-4 flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="pb-3 border-b border-slate-100 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-kp-blue-600"></span>
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                                Rekonsiliasi Langsung
                            </h3>
                        </div>
                        <span class="text-[11px] font-mono font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700"
                              x-text="totalLembar + ' lbr'"></span>
                    </div>

                    <div class="space-y-3 text-xs font-mono">
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="font-sans text-slate-500">Saldo Teoritis Sistem:</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($saldoTeoritis, 0, ',', '.') }}</span>
                        </div>

                        <!-- Rincian Per Pecahan Live (Nominal x Jumlah Lembar = Subtotal) -->
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 space-y-2">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-1.5">
                                <span class="font-sans text-[11px] font-bold uppercase tracking-wider text-slate-700">Rincian Pecahan Terhitung</span>
                                <span class="text-[11px] font-bold text-kp-blue-700" x-text="totalLembar + ' lembar'"></span>
                            </div>

                            <template x-if="rincianPecahan.length === 0">
                                <p class="font-sans text-[11px] text-slate-400 italic py-1">
                                    Masukkan jumlah lembar pecahan di sebelah kiri untuk melihat rincian (mis. Rp 100.000 &times; 10 = Rp 1.000.000).
                                </p>
                            </template>

                            <div class="space-y-1.5 max-h-48 overflow-y-auto pr-0.5" x-show="rincianPecahan.length > 0">
                                <template x-for="item in rincianPecahan" :key="item.key">
                                    <div class="flex items-center justify-between gap-2 text-[11px] py-1 border-b border-slate-100 last:border-b-0">
                                        <span class="text-slate-700 font-semibold">
                                            <span x-text="item.label"></span>
                                            <template x-if="item.unit !== 'nominal'">
                                                <span> &times; <span class="text-slate-900 font-bold" x-text="item.qty"></span> lbr</span>
                                            </template>
                                        </span>
                                        <span class="font-bold text-slate-900 tabular-nums shrink-0" x-text="'Rp ' + formatRupiah(item.subtotal)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="font-sans text-slate-500">1. Uang Fisik di Laci:</span>
                            <span class="font-bold text-slate-800">Rp <span x-text="formatRupiah(totalFisikLaci)"></span></span>
                        </div>
                        <div class="flex justify-between py-2 bg-sky-50 px-3 rounded-xl border border-sky-200">
                            <span class="font-sans font-bold text-[#0E527B]">Total Fisik Terhitung:</span>
                            <span class="font-bold text-kp-blue-700 text-sm">Rp <span x-text="formatRupiah(totalFisikHitung)"></span></span>
                        </div>

                        <div class="p-3.5 rounded-xl border" :class="selisih === 0 ? 'bg-sky-50 border-sky-200 text-[#0E527B]' : (selisih > 0 ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-rose-50 border-rose-200 text-rose-900')">
                            <span class="text-[10px] font-bold uppercase tracking-wider block font-sans">Status Rekonsiliasi &amp; Selisih Kas:</span>
                            <div class="text-sm font-black font-mono mt-0.5">
                                <span x-show="selisih === 0">BALANCE / SEIMBANG (TIDAK ADA SELISIH)</span>
                                <span x-show="selisih > 0">TIDAK BALANCE (+ Rp <span x-text="formatRupiah(selisih)"></span> LEBIH FISIK)</span>
                                <span x-show="selisih < 0">TIDAK BALANCE (- Rp <span x-text="formatRupiah(Math.abs(selisih))"></span> KURANG FISIK)</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs text-slate-600 space-y-1">
                        <span class="font-bold text-slate-800 block">Pemberitahuan Tutup Kas:</span>
                        <p>Setelah ditutup, formulir kasir hari ini akan dikunci dan seluruh mutasi kas siap diteruskan ke Bagian Akuntansi untuk dicatat ke Jurnal &amp; Buku Besar.</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 space-y-2">
                    @if(in_array($activeShift->status, ['closed', 'ditutup']))
                        <button type="button" disabled
                                class="w-full h-11 bg-slate-100 border border-slate-200 text-slate-400 font-bold text-xs rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Kas Tanggal Ini Sudah Ditutup</span>
                        </button>
                    @else
                        <button type="submit" 
                                data-confirm="Seluruh transaksi hari ini akan dikunci dan siap diteruskan ke Bagian Akuntansi. Pastikan jumlah uang fisik sudah sesuai."
                                data-confirm-title="Tutup kas harian?"
                                data-confirm-ok="Tutup Kas"
                                data-confirm-theme="rose"
                                data-confirm-icon="lock"
                                class="w-full h-11 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Kunci &amp; Tutup Kas Harian</span>
                        </button>
                    @endif
                </div>
            </div>
        </form>

        <!-- ============================================================== -->
        <!-- 4. PURE DATA TABLE (RIWAYAT TUTUP KAS & FILTER TANGGAL)        -->
        <!-- ============================================================== -->
        <div id="riwayat-closing" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-slate-50/60">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-kp-blue-600"></span>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                            Riwayat Tutup Kas &amp; Arsip Rincian Pecahan
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Filter per tanggal untuk melihat riwayat kas sebelumnya beserta rincian jumlah lembar pecahan &amp; perbandingan antar hari.
                    </p>
                </div>

                <form method="GET" action="{{ route('kasir.utilitas.closing') }}#riwayat-closing" class="flex flex-wrap items-center gap-2">
                    <label for="tanggal_riwayat" class="text-xs font-semibold text-slate-600">Filter Tanggal:</label>
                    <input type="date"
                           id="tanggal_riwayat"
                           name="tanggal_riwayat"
                           value="{{ $tanggalRiwayat ?? '' }}"
                           class="h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100">
                    <button type="submit"
                            class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer">
                        Tampilkan Tanggal
                    </button>
                    @if(!empty($tanggalRiwayat))
                        <a href="{{ route('kasir.utilitas.closing') }}#riwayat-closing"
                           class="inline-flex items-center h-10 px-3.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 font-semibold text-xs rounded-xl transition">
                            Semua Tanggal
                        </a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] border-b-2 border-[#FFB900] text-white font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="px-4 py-3">No. Sesi</th>
                            <th class="px-4 py-3">Tanggal Kas</th>
                            <th class="px-4 py-3">Kasir</th>
                            <th class="px-4 py-3 text-right">Saldo Awal</th>
                            <th class="px-4 py-3 text-right">Total KI</th>
                            <th class="px-4 py-3 text-right">Total KO</th>
                            <th class="px-4 py-3 text-right">Saldo Fisik</th>
                            <th class="px-4 py-3 text-right">Selisih</th>
                            <th class="px-4 py-3 text-center">Waktu Tutup</th>
                            <th class="px-4 py-3 text-center">Detail Kas</th>
                            <th class="px-4 py-3 text-center">Izin Buka Kas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono">
                        @forelse($riwayatClosing as $sc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-bold text-kp-blue-700">#{{ $sc->id }}</td>
                            <td class="px-4 py-3 font-sans text-slate-700 font-medium text-xs">{{ $sc->tanggal_shift ? \Carbon\Carbon::parse($sc->tanggal_shift)->translatedFormat('d M Y') : ($sc->tgl_shift ? \Carbon\Carbon::parse($sc->tgl_shift)->translatedFormat('d M Y') : '-') }}</td>
                            <td class="px-4 py-3 font-sans font-semibold text-slate-800">{{ $sc->kasir->name ?? 'Kasir' }}</td>
                            <td class="px-4 py-3 text-right text-slate-700">Rp {{ number_format($sc->saldo_awal_fisik, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-kp-blue-700">+ Rp {{ number_format($sc->total_ki ?? $sc->total_masuk, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-rose-600">- Rp {{ number_format($sc->total_ko ?? $sc->total_keluar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-black text-slate-900">Rp {{ number_format($sc->saldo_akhir_fisik, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-bold {{ ($sc->selisih ?? $sc->selisih_kas) == 0 ? 'text-kp-blue-700' : 'text-rose-600' }}">
                                {{ ($sc->selisih ?? $sc->selisih_kas) == 0 ? 'Rp 0' : number_format($sc->selisih ?? $sc->selisih_kas, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center font-sans text-xs text-slate-500">
                                {{ $sc->closed_at ? \Carbon\Carbon::parse($sc->closed_at)->format('d/m/Y H:i') : ($sc->updated_at ? $sc->updated_at->format('d/m/Y H:i') : '-') }}
                            </td>
                            <td class="px-4 py-3 text-center font-sans">
                                <button type="button"
                                        @click="openDetailModal({{ $sc->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-sky-50 text-kp-blue-700 hover:bg-sky-100 border border-sky-200 cursor-pointer transition whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Lihat Rincian</span>
                                </button>
                            </td>
                            <td class="px-4 py-3 text-center font-sans">
                                @if($sc->reopen_status === 'requested' || $sc->reopen_requested)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 text-kp-blue-700 border border-sky-200">
                                        Menunggu ACC
                                    </span>
                                @elseif($sc->reopen_status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 text-kp-blue-700 border border-sky-200">
                                        Dibuka Kembali
                                    </span>
                                @else
                                    <button type="button"
                                            @click="reopenTarget = { id: {{ $sc->id }}, tanggal: '{{ $sc->tanggal_shift ? \Carbon\Carbon::parse($sc->tanggal_shift)->format('d/m/Y') : ($sc->tgl_shift ? \Carbon\Carbon::parse($sc->tgl_shift)->format('d/m/Y') : '-') }}' }; showReopenModal = true"
                                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-white text-slate-700 hover:bg-slate-50 border border-slate-300 cursor-pointer transition">
                                        Ajukan Buka Kas
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="px-5 py-8 text-center text-slate-400 font-sans text-sm">
                                {{ !empty($tanggalRiwayat) ? 'Tidak ada riwayat tutup kas pada tanggal tersebut.' : 'Belum ada riwayat tutup kas sebelumnya.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 5. MODAL DETAIL RIWAYAT KAS & PEMBANDING ANTAR TANGGAL         -->
        <!-- ============================================================== -->
        <div x-cloak x-show="showDetailModal && selectedRiwayat" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showDetailModal = false" class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl border border-slate-200 overflow-hidden my-4">
                <div class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-4">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Detail Riwayat Data Kas &amp; Rincian Pecahan</h4>
                        <p class="text-xs text-slate-500 mt-0.5"
                           x-text="'Sesi #' + (selectedRiwayat?.id ?? '-') + ' • Tanggal: ' + (selectedRiwayat?.tanggal ?? '-') + ' • Kasir: ' + (selectedRiwayat?.kasir_name ?? '-')"></p>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-slate-700 font-bold text-xl leading-none cursor-pointer">&times;</button>
                </div>

                <div class="p-5 space-y-4 max-h-[80vh] overflow-y-auto">
                    <!-- Pilih Pembanding Hari Berikutnya / Lainnya -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <label class="text-xs font-semibold text-slate-700">Bandingkan dengan Tanggal Kas Lain:</label>
                        <select x-model="compareId"
                                class="h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600">
                            <option value="">-- Tidak Dibandingkan --</option>
                            <template x-for="opt in compareOptions.filter(o => o.id !== selectedRiwayat?.id)" :key="opt.id">
                                <option :value="String(opt.id)"
                                        x-text="opt.tanggal + ' (Sesi #' + opt.id + ' • Saldo Fisik: Rp ' + formatRupiah(opt.saldo_fisik) + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Ringkasan Saldo Hari Terpilih -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 text-xs">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-slate-500 block text-[11px]">Saldo Awal</span>
                            <span class="font-mono font-bold text-slate-900 text-sm" x-text="'Rp ' + formatRupiah(selectedRiwayat?.saldo_awal)"></span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-slate-500 block text-[11px]">Total Kas Masuk (KI)</span>
                            <span class="font-mono font-bold text-kp-blue-700 text-sm" x-text="'+ Rp ' + formatRupiah(selectedRiwayat?.total_ki)"></span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-slate-500 block text-[11px]">Total Kas Keluar (KO)</span>
                            <span class="font-mono font-bold text-rose-600 text-sm" x-text="'- Rp ' + formatRupiah(selectedRiwayat?.total_ko)"></span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-slate-500 block text-[11px]">Saldo Teoritis Sistem</span>
                            <span class="font-mono font-bold text-slate-900 text-sm" x-text="'Rp ' + formatRupiah(selectedRiwayat?.saldo_teoritis)"></span>
                        </div>
                        <div class="p-3 bg-sky-50 rounded-xl border border-sky-200">
                            <span class="text-[#0E527B] font-semibold block text-[11px]">Total Fisik Laci Tercatat</span>
                            <span class="font-mono font-black text-kp-blue-700 text-sm" x-text="'Rp ' + formatRupiah(selectedRiwayat?.saldo_fisik)"></span>
                        </div>
                        <div class="p-3 rounded-xl border" :class="(selectedRiwayat?.selisih ?? 0) === 0 ? 'bg-sky-50 border-sky-200 text-[#0E527B]' : 'bg-rose-50 border-rose-200 text-rose-800'">
                            <span class="block text-[11px] font-semibold">Status Selisih</span>
                            <span class="font-mono font-black text-sm"
                                  x-text="(selectedRiwayat?.selisih ?? 0) === 0 ? 'BALANCE (Rp 0)' : ((selectedRiwayat?.selisih ?? 0) > 0 ? '+ Rp ' + formatRupiah(selectedRiwayat.selisih) : '- Rp ' + formatRupiah(Math.abs(selectedRiwayat.selisih)))"></span>
                        </div>
                    </div>

                    <template x-if="selectedRiwayat && !selectedRiwayat.has_rincian_pecahan">
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                            Catatan: Sesi tutup kas ini tersimpan sebelum fitur arsip pecahan aktif, sehingga rincian lembar per pecahan tidak tercatat. Total saldo fisik keseluruhan tetap tercatat: <strong class="font-mono" x-text="'Rp ' + formatRupiah(selectedRiwayat.saldo_fisik)"></strong>.
                        </div>
                    </template>

                    <!-- Tabel Rincian Lembar Pecahan (+ Pembanding Opsional) -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead class="bg-[#0A72AC] border-b-2 border-[#FFB900] text-white font-bold">
                                <tr>
                                    <th class="px-3.5 py-2.5">Pecahan</th>
                                    <th class="px-3.5 py-2.5 text-right" x-text="selectedRiwayat?.tanggal_short + ' (Qty)'"></th>
                                    <th class="px-3.5 py-2.5 text-right" x-text="selectedRiwayat?.tanggal_short + ' (Subtotal)'"></th>
                                    <template x-if="compareRiwayat">
                                        <th class="px-3.5 py-2.5 text-right" x-text="compareRiwayat.tanggal_short + ' (Qty &amp; Subtotal)'"></th>
                                    </template>
                                    <template x-if="compareRiwayat">
                                        <th class="px-3.5 py-2.5 text-right">Selisih Nominal</th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono">
                                <template x-for="row in (selectedRiwayat?.pecahan || [])" :key="row.key">
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-3.5 py-2 font-sans font-semibold text-slate-800" x-text="row.label"></td>
                                        <td class="px-3.5 py-2 text-right text-slate-700">
                                            <span x-text="row.unit === 'nominal' ? '-' : (row.qty + ' lbr')"></span>
                                        </td>
                                        <td class="px-3.5 py-2 text-right font-bold text-slate-900" x-text="'Rp ' + formatRupiah(row.subtotal)"></td>
                                        <template x-if="compareRiwayat">
                                            <td class="px-3.5 py-2 text-right bg-sky-50/30 text-slate-700">
                                                <template x-if="getCompareRow(row.key)">
                                                    <span>
                                                        <span class="text-slate-500" x-show="row.unit !== 'nominal'" x-text="getCompareRow(row.key).qty + ' lbr • '"></span>
                                                        <span class="font-bold text-slate-900" x-text="'Rp ' + formatRupiah(getCompareRow(row.key).subtotal)"></span>
                                                    </span>
                                                </template>
                                            </td>
                                        </template>
                                        <template x-if="compareRiwayat">
                                            <td class="px-3.5 py-2 text-right bg-slate-50 font-bold"
                                                :class="(row.subtotal - (getCompareRow(row.key)?.subtotal || 0)) === 0 ? 'text-slate-500' : ((row.subtotal - (getCompareRow(row.key)?.subtotal || 0)) > 0 ? 'text-kp-blue-700' : 'text-rose-600')">
                                                <span x-text="(() => {
                                                    const diff = row.subtotal - (getCompareRow(row.key)?.subtotal || 0);
                                                    if (diff === 0) return 'Rp 0';
                                                    return (diff > 0 ? '+ Rp ' : '- Rp ') + formatRupiah(Math.abs(diff));
                                                })()"></span>
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono font-bold text-xs">
                                <tr>
                                    <td class="px-3.5 py-2.5 font-sans text-slate-800 uppercase">Total Pecahan Kertas &amp; Saldo Fisik</td>
                                    <td class="px-3.5 py-2.5 text-right text-slate-800" x-text="(selectedRiwayat?.total_lembar ?? 0) + ' lbr'"></td>
                                    <td class="px-3.5 py-2.5 text-right text-kp-blue-700" x-text="'Rp ' + formatRupiah(selectedRiwayat?.saldo_fisik)"></td>
                                    <template x-if="compareRiwayat">
                                        <td class="px-3.5 py-2.5 text-right bg-sky-50/60 text-slate-800">
                                            <span x-text="compareRiwayat.total_lembar + ' lbr • Rp ' + formatRupiah(compareRiwayat.saldo_fisik)"></span>
                                        </td>
                                    </template>
                                    <template x-if="compareRiwayat">
                                        <td class="px-3.5 py-2.5 text-right bg-slate-100/80"
                                            :class="((selectedRiwayat?.saldo_fisik || 0) - compareRiwayat.saldo_fisik) >= 0 ? 'text-kp-blue-700' : 'text-rose-600'">
                                            <span x-text="(() => {
                                                const diff = (selectedRiwayat?.saldo_fisik || 0) - compareRiwayat.saldo_fisik;
                                                if (diff === 0) return 'Rp 0';
                                                return (diff > 0 ? '+ Rp ' : '- Rp ') + formatRupiah(Math.abs(diff));
                                            })()"></span>
                                        </td>
                                    </template>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Catatan Serah Terima -->
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                        <span class="font-bold text-slate-700 block mb-0.5">Catatan Serah Terima:</span>
                        <span class="text-slate-600" x-text="selectedRiwayat?.catatan || '-'"></span>
                    </div>
                </div>

                <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                    <span x-text="'Ditutup pada: ' + (selectedRiwayat?.waktu_tutup ?? '-')"></span>
                    <button type="button"
                            @click="showDetailModal = false"
                            class="h-9 px-4 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-xs cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 6. MODAL PENGAJUAN BERITA ACARA BUKA KAS                       -->
        <!-- ============================================================== -->
        <div x-cloak x-show="showReopenModal && reopenTarget.id" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showReopenModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Pengajuan Berita Acara Buka Kembali Kas</h4>
                        <p class="text-xs text-slate-500" x-text="'Sesi #' + reopenTarget.id + ' • Tanggal: ' + reopenTarget.tanggal"></p>
                    </div>
                    <button type="button" @click="showReopenModal = false" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer">&times;</button>
                </div>
                <form method="POST" :action="'{{ url('/kasir/utilitas/buka-kas') }}/' + reopenTarget.id + '/ajukan'" class="p-5 space-y-4 text-xs">
                    @csrf
                    <div class="p-3.5 bg-sky-50 border border-sky-200 rounded-xl text-xs text-slate-700 leading-relaxed">
                        Sesuai <b>SOP Tutup Kas Harian</b>: Pembukaan kembali kas yang sudah ditutup hanya dapat dilakukan melalui pengajuan Berita Acara yang disetujui oleh <b>Bagian Akuntansi / Manajer Keuangan</b>.
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Uraian Berita Acara Pembukaan Kembali Kas <span class="text-rose-600">*</span>
                        </label>
                        <textarea name="berita_acara_reopen" 
                                  rows="3" 
                                  required 
                                  minlength="10"
                                  placeholder="Jelaskan alasan pengajuan buka kembali kas (minimal 10 karakter), misal: Koreksi selisih input kuitansi iklan..."
                                  class="w-full text-sm p-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 text-slate-900"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showReopenModal = false" class="h-10 px-4 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold rounded-xl text-xs cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold rounded-xl text-xs shadow-xs cursor-pointer">
                            Kirim Berita Acara ke Akuntansi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>