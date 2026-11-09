<x-app-layout>
    <div class="space-y-5" x-data="{
        showReopenModal: false,
        reopenTarget: { id: null, tanggal: '' },
        saldoTeoritis: {{ $saldoTeoritis }},
        kasbonGantung: {{ $totalKasbonGantung ?? 0 }},
        p100: 0,
        p50: 0,
        p20: 0,
        p10: 0,
        p5: 0,
        p2: 0,
        p1: 0,
        koin: 0,
        get totalFisikLaci() {
            return (parseInt(this.p100 || 0) * 100000) +
                   (parseInt(this.p50 || 0) * 50000) +
                   (parseInt(this.p20 || 0) * 20000) +
                   (parseInt(this.p10 || 0) * 10000) +
                   (parseInt(this.p5 || 0) * 5000) +
                   (parseInt(this.p2 || 0) * 2000) +
                   (parseInt(this.p1 || 0) * 1000) +
                   (parseFloat(this.koin || 0));
        },
        get totalFisikHitung() {
            return this.totalFisikLaci + Number(this.kasbonGantung);
        },
        get selisih() {
            return this.totalFisikHitung - this.saldoTeoritis;
        }
    }">

        {{-- Flash Message Alert --}}
        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-kp-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-kp-blue-600 hover:text-kp-blue-800 text-base font-bold leading-none cursor-pointer">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-slate-500 hover:text-slate-800 text-base font-bold leading-none cursor-pointer">&times;</button>
            </div>
        @endif

        {{-- Header Halaman --}}
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Tutup Kas Harian (Closing Shift)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Perhitungan pecahan fisik uang laci dan penguncian mutasi kas harian.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <div class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    <span class="text-slate-500">Kasir: <strong class="text-slate-900">{{ Auth::user()->name ?? 'Kasir Priangan' }}</strong></span>
                    <span class="mx-1.5 text-slate-300">•</span>
                    <span class="font-mono font-bold text-kp-blue-700">Shift #{{ $activeShift->id }}</span>
                </div>
                <a href="{{ route('kasir.utilitas.buka-kas') }}"
                   class="inline-flex items-center gap-2 px-4 h-10 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    <span>Buka Kembali Kas</span>
                </a>
            </div>
        </div>

        {{-- Kartu KPI Saldo Shift --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Awal Fisik</span>
                <div class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-400 mt-1 block">Modal awal shift</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Masuk (KI)</span>
                <div class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalKi, 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-400 mt-1 block">Penerimaan kas tunai</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Keluar (KO)</span>
                <div class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalKo, 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-400 mt-1 block">Pengeluaran kas tunai</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Akhir Teoritis</span>
                <div class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($saldoTeoritis, 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-400 mt-1 block">Target saldo fisik kas</span>
            </div>
        </div>

        {{-- Form Denominasi Pecahan Fisik Uang Kasir --}}
        <form method="POST" action="{{ route('kasir.utilitas.closing.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            @csrf

            {{-- Kolom Rincian Pecahan --}}
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs lg:col-span-2 space-y-4">
                <div class="pb-3 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Rincian Fisik Uang Kertas & Logam</h2>
                    <span class="text-xs text-slate-500">Jumlah lembar per pecahan</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- Pecahan 100k --}}
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center gap-2.5">
                            <span class="w-11 h-9 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 font-mono font-bold text-xs flex items-center justify-center shrink-0">100k</span>
                            <div>
                                <span class="text-sm font-semibold text-slate-900 block">Rp 100.000</span>
                                <span class="text-xs text-slate-500 font-mono tabular-nums">Rp <span x-text="new Intl.NumberFormat('id-ID').format((p100 || 0) * 100000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_100k" x-model="p100" min="0" placeholder="0"
                               class="w-24 h-10 px-3 text-right bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono font-semibold tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    {{-- Pecahan 50k --}}
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center gap-2.5">
                            <span class="w-11 h-9 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 font-mono font-bold text-xs flex items-center justify-center shrink-0">50k</span>
                            <div>
                                <span class="text-sm font-semibold text-slate-900 block">Rp 50.000</span>
                                <span class="text-xs text-slate-500 font-mono tabular-nums">Rp <span x-text="new Intl.NumberFormat('id-ID').format((p50 || 0) * 50000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_50k" x-model="p50" min="0" placeholder="0"
                               class="w-24 h-10 px-3 text-right bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono font-semibold tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    {{-- Pecahan 20k --}}
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center gap-2.5">
                            <span class="w-11 h-9 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 font-mono font-bold text-xs flex items-center justify-center shrink-0">20k</span>
                            <div>
                                <span class="text-sm font-semibold text-slate-900 block">Rp 20.000</span>
                                <span class="text-xs text-slate-500 font-mono tabular-nums">Rp <span x-text="new Intl.NumberFormat('id-ID').format((p20 || 0) * 20000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_20k" x-model="p20" min="0" placeholder="0"
                               class="w-24 h-10 px-3 text-right bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono font-semibold tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    {{-- Pecahan 10k --}}
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center gap-2.5">
                            <span class="w-11 h-9 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 font-mono font-bold text-xs flex items-center justify-center shrink-0">10k</span>
                            <div>
                                <span class="text-sm font-semibold text-slate-900 block">Rp 10.000</span>
                                <span class="text-xs text-slate-500 font-mono tabular-nums">Rp <span x-text="new Intl.NumberFormat('id-ID').format((p10 || 0) * 10000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_10k" x-model="p10" min="0" placeholder="0"
                               class="w-24 h-10 px-3 text-right bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono font-semibold tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    {{-- Pecahan 5k --}}
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center gap-2.5">
                            <span class="w-11 h-9 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 font-mono font-bold text-xs flex items-center justify-center shrink-0">5k</span>
                            <div>
                                <span class="text-sm font-semibold text-slate-900 block">Rp 5.000</span>
                                <span class="text-xs text-slate-500 font-mono tabular-nums">Rp <span x-text="new Intl.NumberFormat('id-ID').format((p5 || 0) * 5000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_5k" x-model="p5" min="0" placeholder="0"
                               class="w-24 h-10 px-3 text-right bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono font-semibold tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    {{-- Pecahan 2k --}}
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center gap-2.5">
                            <span class="w-11 h-9 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 font-mono font-bold text-xs flex items-center justify-center shrink-0">2k</span>
                            <div>
                                <span class="text-sm font-semibold text-slate-900 block">Rp 2.000</span>
                                <span class="text-xs text-slate-500 font-mono tabular-nums">Rp <span x-text="new Intl.NumberFormat('id-ID').format((p2 || 0) * 2000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_2k" x-model="p2" min="0" placeholder="0"
                               class="w-24 h-10 px-3 text-right bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono font-semibold tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    {{-- Pecahan 1k --}}
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center gap-2.5">
                            <span class="w-11 h-9 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 font-mono font-bold text-xs flex items-center justify-center shrink-0">1k</span>
                            <div>
                                <span class="text-sm font-semibold text-slate-900 block">Rp 1.000</span>
                                <span class="text-xs text-slate-500 font-mono tabular-nums">Rp <span x-text="new Intl.NumberFormat('id-ID').format((p1 || 0) * 1000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_1k" x-model="p1" min="0" placeholder="0"
                               class="w-24 h-10 px-3 text-right bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono font-semibold tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    {{-- Uang Logam / Koin --}}
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center gap-2.5">
                            <span class="w-11 h-9 rounded-lg bg-slate-200/80 border border-slate-300 text-slate-700 font-mono font-bold text-xs flex items-center justify-center shrink-0">Koin</span>
                            <div>
                                <span class="text-sm font-semibold text-slate-900 block">Total Uang Logam</span>
                                <span class="text-xs text-slate-500">Nominal Rupiah koin</span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_koin" x-model="koin" min="0" placeholder="0"
                               class="w-28 h-10 px-3 text-right bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono font-semibold tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                </div>

                {{-- Catatan Serah Terima --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Catatan Serah Terima Shift</label>
                    <textarea name="catatan" rows="2" placeholder="Catatan fisik uang laci atau serah terima kasir (opsional)..."
                              class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                </div>
            </div>

            {{-- Kolom Rekonsiliasi & Konfirmasi --}}
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs lg:col-span-1 flex flex-col justify-between space-y-4">
                <div class="space-y-4">
                    <div class="pb-3 border-b border-slate-200">
                        <h2 class="text-sm font-bold text-slate-900">Rekonsiliasi Fisik & Sistem</h2>
                    </div>

                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Saldo Teoritis Sistem</span>
                            <span class="font-mono tabular-nums font-semibold text-slate-900">Rp {{ number_format($saldoTeoritis, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Uang Fisik di Laci</span>
                            <span class="font-mono tabular-nums font-semibold text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(totalFisikLaci)"></span></span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Kasbon Gantung Aktif</span>
                            <span class="font-mono tabular-nums font-semibold text-slate-900">+ Rp {{ number_format($totalKasbonGantung ?? 0, 0, ',', '.') }}</span>
                        </div>

                        @if(($totalKasbonGantung ?? 0) > 0)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-700 space-y-1">
                            <span class="font-semibold text-slate-600 block">Rincian Kasbon Gantung:</span>
                            @foreach($daftarKasbonGantung as $kb)
                                <div class="flex justify-between font-mono tabular-nums">
                                    <span class="font-sans truncate pr-2">{{ $kb->nama_penerima }} ({{ Str::limit($kb->keperluan, 18) }})</span>
                                    <span class="font-semibold shrink-0">Rp {{ number_format($kb->nominal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                        @endif

                        <div class="flex justify-between py-2 px-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="font-semibold text-slate-800">Total Fisik + Kasbon</span>
                            <span class="font-mono tabular-nums font-bold text-kp-blue-700">Rp <span x-text="new Intl.NumberFormat('id-ID').format(totalFisikHitung)"></span></span>
                        </div>

                        <div class="p-3.5 rounded-xl border"
                             :class="selisih === 0 ? 'bg-kp-blue-50 border-kp-blue-200 text-kp-blue-800' : 'bg-slate-100 border-slate-300 text-slate-800'">
                            <span class="text-xs font-semibold uppercase tracking-wider block">Status Selisih Shift</span>
                            <div class="text-sm font-bold font-mono tabular-nums mt-1">
                                <span x-show="selisih === 0">SEIMBANG (Rp 0)</span>
                                <span x-show="selisih > 0">+ Rp <span x-text="new Intl.NumberFormat('id-ID').format(selisih)"></span> (LEBIH FISIK)</span>
                                <span x-show="selisih < 0">- Rp <span x-text="new Intl.NumberFormat('id-ID').format(Math.abs(selisih))"></span> (KURANG FISIK)</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        Setelah shift dikunci, mutasi kas harian siap ditarik ke jurnal Divisi Akuntansi.
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-200">
                    <button type="submit"
                            data-confirm="Seluruh transaksi hari ini akan dikunci dan siap ditarik ke Akuntansi. Pastikan jumlah uang fisik sudah sesuai."
                            data-confirm-title="Tutup kas harian?"
                            data-confirm-ok="Tutup Kas"
                            data-confirm-icon="lock"
                            class="w-full h-11 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Kunci Shift & Tutup Kas</span>
                    </button>
                </div>
            </div>
        </form>

        {{-- Tabel Riwayat Closing Shift Sebelumnya --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Riwayat Tutup Kas Harian</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3">Shift ID</th>
                            <th class="py-3 px-3">Tanggal Shift</th>
                            <th class="py-3 px-3">Kasir</th>
                            <th class="py-3 px-3 text-right">Saldo Awal</th>
                            <th class="py-3 px-3 text-right">Total KI</th>
                            <th class="py-3 px-3 text-right">Total KO</th>
                            <th class="py-3 px-3 text-right">Saldo Fisik</th>
                            <th class="py-3 px-3 text-right">Selisih</th>
                            <th class="py-3 px-3 text-center">Waktu Closing</th>
                            <th class="py-3 px-3 text-center">Izin Buka Kas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($riwayatClosing as $sc)
                        @php
                            $nilaiSelisih = $sc->selisih ?? $sc->selisih_kas ?? 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 font-mono font-bold text-kp-blue-700 text-sm whitespace-nowrap">#{{ $sc->id }}</td>
                            <td class="py-3 px-3 font-mono text-xs text-slate-600 whitespace-nowrap">{{ $sc->tanggal_shift ? \Carbon\Carbon::parse($sc->tanggal_shift)->format('d/m/Y') : ($sc->tgl_shift ? \Carbon\Carbon::parse($sc->tgl_shift)->format('d/m/Y') : '-') }}</td>
                            <td class="py-3 px-3 font-semibold text-slate-900 whitespace-nowrap">{{ $sc->kasir->name ?? 'Kasir' }}</td>
                            <td class="py-3 px-3 text-right font-mono tabular-nums whitespace-nowrap">Rp {{ number_format($sc->saldo_awal_fisik, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono tabular-nums text-kp-blue-700 font-medium whitespace-nowrap">+ Rp {{ number_format($sc->total_ki ?? $sc->total_masuk, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono tabular-nums whitespace-nowrap">- Rp {{ number_format($sc->total_ko ?? $sc->total_keluar, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono tabular-nums font-semibold text-slate-900 whitespace-nowrap">Rp {{ number_format($sc->saldo_akhir_fisik, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono tabular-nums font-semibold whitespace-nowrap {{ $nilaiSelisih == 0 ? 'text-kp-blue-700' : 'text-slate-700' }}">
                                {{ $nilaiSelisih == 0 ? 'Rp 0' : 'Rp ' . number_format($nilaiSelisih, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono text-xs text-slate-500 whitespace-nowrap">
                                {{ $sc->closed_at ? \Carbon\Carbon::parse($sc->closed_at)->format('d/m/Y H:i') : ($sc->updated_at ? $sc->updated_at->format('d/m/Y H:i') : '-') }}
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                @if($sc->reopen_status === 'requested' || $sc->reopen_requested)
                                    <a href="{{ route('kasir.utilitas.buka-kas') }}" class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition">
                                        Menunggu Izin
                                    </a>
                                @elseif($sc->reopen_status === 'approved')
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        Dibuka Kembali
                                    </span>
                                @else
                                    <button type="button"
                                            @click="reopenTarget = { id: {{ $sc->id }}, tanggal: '{{ $sc->tanggal_shift ? \Carbon\Carbon::parse($sc->tanggal_shift)->format('d/m/Y') : ($sc->tgl_shift ? \Carbon\Carbon::parse($sc->tgl_shift)->format('d/m/Y') : '-') }}' }; showReopenModal = true"
                                            class="px-3 py-1 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                                        Ajukan Buka Kas
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="py-8 px-4 text-center text-sm text-slate-400">Belum ada riwayat tutup kas sebelumnya.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Pengajuan Berita Acara Re-open Kas --}}
        <div x-cloak x-show="showReopenModal && reopenTarget.id" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.away="showReopenModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Pengajuan Berita Acara Buka Kas</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Shift #<span class="font-mono font-bold text-kp-blue-700" x-text="reopenTarget.id"></span> • <span class="font-mono" x-text="reopenTarget.tanggal"></span></p>
                    </div>
                    <button type="button" @click="showReopenModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold leading-none cursor-pointer">&times;</button>
                </div>
                <form method="POST" :action="'{{ url('/kasir/utilitas/buka-kas') }}/' + reopenTarget.id + '/ajukan'" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Uraian Berita Acara <span class="text-rose-600">*</span></label>
                        <textarea name="berita_acara_reopen" rows="3" required minlength="10"
                                  placeholder="Jelaskan alasan pembukaan kembali shift kas (minimal 10 karakter)..."
                                  class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200">
                        <button type="button" @click="showReopenModal = false" class="px-4 h-10 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 h-10 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition cursor-pointer">
                            Kirim Berita Acara
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
