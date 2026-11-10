<x-app-layout>
    <div class="space-y-6" x-data="{
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

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-semibold text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs font-semibold text-rose-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Header Tutup Kas Harian -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-white p-1 border border-emerald-200 shadow-2xs flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/kabarpriangan.png') }}" alt="Logo Kabar Priangan" class="h-full w-full object-contain rounded-lg" onerror="this.src='{{ asset('kabarpriangan.png') }}'">
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Tutup Kas Harian (Closing Shift KAS)
                        </h1>
                        @if(in_array($activeShift->status, ['closed', 'ditutup']))
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-rose-100 text-rose-800 border border-rose-300">
                                Status: Kas Ditutup (Terkunci)
                            </span>
                        @elseif($activeShift->reopen_status === 'approved')
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-100 text-amber-800 border border-amber-300">
                                Dibuka Kembali (ACC Accounting)
                            </span>
                        @else
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-emerald-100 text-emerald-800 border border-emerald-300">
                                Shift Aktif (Open)
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Mengunci seluruh transaksi kas harian, menghitung fisik pecahan uang, serta mengaktifkan flag ready_to_pull Akuntansi
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <a href="{{ route('kasir.utilitas.buka-kas') }}" 
                   class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs rounded-xl border border-amber-300 transition shadow-2xs flex items-center gap-1.5 self-start sm:self-auto">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    <span>Buka Kembali Kas (Persetujuan Accounting)</span>
                </a>
                <div class="text-left sm:text-right">
                    <span class="text-xs text-slate-500 font-medium">Kasir Penanggung Jawab:</span>
                    <div class="text-sm font-bold text-slate-900 font-mono">{{ Auth::user()->name ?? 'Kasir Priangan' }}</div>
                    <div class="text-[10px] text-slate-400">Shift ID #{{ $activeShift->id }} ({{ \Carbon\Carbon::parse($activeShift->tanggal_shift ?? $activeShift->tgl_shift ?? today())->translatedFormat('d F Y') }})</div>
                </div>
            </div>
        </div>

        @if(in_array($activeShift->status, ['closed', 'ditutup']))
            <!-- Banner Peringatan Kas Hari Ini Sudah Ditutup -->
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-xs">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-rose-900">
                            Kas Tanggal {{ \Carbon\Carbon::parse($activeShift->tanggal_shift ?? $activeShift->tgl_shift ?? today())->translatedFormat('d F Y') }} Telah Ditutup (Closed)
                        </h4>
                        <p class="text-xs text-rose-700 mt-0.5 leading-relaxed">
                            Seluruh menu input penerimaan (KI) dan pengeluaran (KO) untuk tanggal ini telah dikunci. Apabila terdapat koreksi atau transaksi susulan yang harus diinput pada tanggal ini, silakan ajukan <strong>Persetujuan Accounting (Buka Kembali Kas)</strong>. Untuk transaksi hari berikutnya, shift baru akan terbuka otomatis pada tanggal berikutnya.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @if($activeShift->reopen_status === 'requested' || $activeShift->reopen_requested)
                        <a href="{{ route('kasir.utilitas.buka-kas') }}" class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl transition shadow-xs">
                            Menunggu ACC Accounting
                        </a>
                    @else
                        <button type="button"
                                @click="reopenTarget = { id: {{ $activeShift->id }}, tanggal: '{{ \Carbon\Carbon::parse($activeShift->tanggal_shift ?? $activeShift->tgl_shift ?? today())->format('d/m/Y') }}' }; showReopenModal = true"
                                class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer">
                            Ajukan Buka Kembali Kas
                        </button>
                    @endif
                </div>
            </div>
        @endif

        <!-- Metric Balance Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Saldo Awal Fisik</span>
                <div class="mt-2 text-lg font-black text-slate-900">
                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Modal pembuka shift</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Kas Masuk (KI)</span>
                <div class="mt-2 text-lg font-black text-teal-700">
                    + Rp {{ number_format($totalKi, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Penerimaan kas laci</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Kas Keluar (KO)</span>
                <div class="mt-2 text-lg font-black text-rose-700">
                    - Rp {{ number_format($totalKo, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Realisasi BP & Komisi</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Saldo Akhir Teoritis</span>
                <div class="mt-2 text-lg font-black text-emerald-700">
                    Rp {{ number_format($saldoTeoritis, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Wajib sesuai uang laci</div>
            </div>
        </div>

        <!-- Form Denominasi Pecahan Fisik Uang Kasir -->
        <form method="POST" action="{{ route('kasir.utilitas.closing.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @csrf

            <!-- Kolom Input Pecahan Uang Kertas & Logam -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs lg:col-span-2 space-y-4">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        Rincian Fisik Uang Kertas & Logam (Sistem KAS)
                    </span>
                    <span class="text-[11px] font-normal text-slate-500">Masukkan jumlah lembar per pecahan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Pecahan 100k -->
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded bg-rose-100 text-rose-800 font-mono font-black text-xs flex items-center justify-center">100k</span>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Rp 100.000</span>
                                <span class="text-[10px] text-slate-400 font-mono">Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format((p100 || 0) * 100000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_100k" x-model="p100" min="0" placeholder="0 lbr"
                               class="w-24 text-right text-xs py-1.5 px-2.5 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-mono font-bold">
                    </div>

                    <!-- Pecahan 50k -->
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded bg-teal-100 text-teal-800 font-mono font-black text-xs flex items-center justify-center">50k</span>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Rp 50.000</span>
                                <span class="text-[10px] text-slate-400 font-mono">Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format((p50 || 0) * 50000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_50k" x-model="p50" min="0" placeholder="0 lbr"
                               class="w-24 text-right text-xs py-1.5 px-2.5 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-mono font-bold">
                    </div>

                    <!-- Pecahan 20k -->
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded bg-emerald-100 text-emerald-800 font-mono font-black text-xs flex items-center justify-center">20k</span>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Rp 20.000</span>
                                <span class="text-[10px] text-slate-400 font-mono">Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format((p20 || 0) * 20000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_20k" x-model="p20" min="0" placeholder="0 lbr"
                               class="w-24 text-right text-xs py-1.5 px-2.5 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-mono font-bold">
                    </div>

                    <!-- Pecahan 10k -->
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded bg-emerald-100 text-emerald-800 font-mono font-black text-xs flex items-center justify-center">10k</span>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Rp 10.000</span>
                                <span class="text-[10px] text-slate-400 font-mono">Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format((p10 || 0) * 10000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_10k" x-model="p10" min="0" placeholder="0 lbr"
                               class="w-24 text-right text-xs py-1.5 px-2.5 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-mono font-bold">
                    </div>

                    <!-- Pecahan 5k -->
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded bg-amber-100 text-amber-800 font-mono font-black text-xs flex items-center justify-center">5k</span>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Rp 5.000</span>
                                <span class="text-[10px] text-slate-400 font-mono">Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format((p5 || 0) * 5000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_5k" x-model="p5" min="0" placeholder="0 lbr"
                               class="w-24 text-right text-xs py-1.5 px-2.5 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-mono font-bold">
                    </div>

                    <!-- Pecahan 2k -->
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded bg-slate-200 text-slate-800 font-mono font-black text-xs flex items-center justify-center">2k</span>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Rp 2.000</span>
                                <span class="text-[10px] text-slate-400 font-mono">Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format((p2 || 0) * 2000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_2k" x-model="p2" min="0" placeholder="0 lbr"
                               class="w-24 text-right text-xs py-1.5 px-2.5 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-mono font-bold">
                    </div>

                    <!-- Pecahan 1k -->
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded bg-teal-100 text-teal-800 font-mono font-black text-xs flex items-center justify-center">1k</span>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Rp 1.000</span>
                                <span class="text-[10px] text-slate-400 font-mono">Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format((p1 || 0) * 1000)"></span></span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_1k" x-model="p1" min="0" placeholder="0 lbr"
                               class="w-24 text-right text-xs py-1.5 px-2.5 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-mono font-bold">
                    </div>

                    <!-- Uang Koin / Logam -->
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded bg-yellow-100 text-yellow-800 font-mono font-black text-xs flex items-center justify-center">Koin</span>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Total Uang Logam (Rp)</span>
                                <span class="text-[10px] text-slate-400 font-mono">Pecahan 500, 200, 100</span>
                            </div>
                        </div>
                        <input type="number" name="pecahan_koin" x-model="koin" min="0" placeholder="Rp 0"
                               class="w-28 text-right text-xs py-1.5 px-2.5 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-mono font-bold">
                    </div>
                </div>

                <!-- Catatan Serah Terima -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Serah Terima / Keterangan Closing</label>
                    <textarea name="catatan" rows="2" placeholder="Catatan fisik uang, titipan brankas, atau kendala operasional kasir..."
                              class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-rose-500 text-slate-800"></textarea>
                </div>
            </div>

            <!-- Kolom Ringkasan & Konfirmasi Kunci Shift -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs lg:col-span-1 space-y-4 flex flex-col justify-between">
                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-700"></span>
                        Rekonsiliasi Realtime
                    </h3>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-500">Saldo Teoritis Sistem:</span>
                            <span class="font-mono font-bold text-slate-900">Rp {{ number_format($saldoTeoritis, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-500">1. Uang Fisik di Laci:</span>
                            <span class="font-mono font-bold text-slate-800">Rp <span x-text="new Intl.NumberFormat('id-ID').format(totalFisikLaci)"></span></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100 bg-rose-50/50 px-2 rounded-lg">
                            <span class="text-rose-700 font-semibold">2. Kasbon Gantung (Aktif):</span>
                            <span class="font-mono font-bold text-rose-700">+ Rp {{ number_format($totalKasbonGantung ?? 0, 0, ',', '.') }}</span>
                        </div>
                        @if(($totalKasbonGantung ?? 0) > 0)
                        <div class="p-2 bg-rose-50/80 rounded-lg border border-rose-200 text-[10px] text-rose-800 space-y-1">
                            <span class="font-bold block">Rincian Kasbon Belum Kembali:</span>
                            @foreach($daftarKasbonGantung as $kb)
                                <div class="flex justify-between font-mono">
                                    <span>{{ $kb->nama_penerima }} ({{ Str::limit($kb->keperluan, 18) }})</span>
                                    <span class="font-bold">Rp {{ number_format($kb->nominal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                        @endif
                        <div class="flex justify-between py-1.5 border-b border-slate-200 bg-emerald-50/50 px-2 rounded-lg">
                            <span class="font-bold text-emerald-900">Total Fisik + Kasbon:</span>
                            <span class="font-mono font-black text-emerald-700 text-sm">Rp <span x-text="new Intl.NumberFormat('id-ID').format(totalFisikHitung)"></span></span>
                        </div>
                        <div class="p-3 rounded-xl border" :class="selisih === 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : (selisih > 0 ? 'bg-teal-50 border-teal-200 text-teal-900' : 'bg-rose-50 border-rose-200 text-rose-900')">
                            <span class="text-[10px] font-bold uppercase tracking-wider block">Status Selisih Shift:</span>
                            <div class="text-sm font-black font-mono mt-0.5">
                                <span x-show="selisih === 0">SEIMBANG (ZERO DIFFERENCE / BALANCE)</span>
                                <span x-show="selisih > 0">+ Rp <span x-text="new Intl.NumberFormat('id-ID').format(selisih)"></span> (LEBIH FISIK)</span>
                                <span x-show="selisih < 0">- Rp <span x-text="new Intl.NumberFormat('id-ID').format(Math.abs(selisih))"></span> (DEFISIT FISIK)</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-amber-50 p-3 rounded-lg border border-amber-200 text-[11px] text-amber-800 space-y-1">
                        <span class="font-bold block">Peringatan Kunci Shift:</span>
                        <p>Setelah ditutup, form kasir hari ini akan dikunci dan flag <code class="font-mono font-bold">ready_to_pull</code> akan aktif agar Modul Akuntansi dapat menarik mutasi kas ke Jurnal & Buku Besar.</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 space-y-2">
                    @if(in_array($activeShift->status, ['closed', 'ditutup']))
                        <button type="button" disabled
                                class="w-full py-3 bg-slate-200 text-slate-500 font-bold text-xs rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Kas Tanggal Ini Sudah Ditutup</span>
                        </button>
                        <a href="{{ route('kasir.utilitas.buka-kas') }}"
                           class="w-full py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold text-xs rounded-xl transition flex items-center justify-center gap-2">
                            <span>Ajukan Buka Kembali Kas (Persetujuan Accounting)</span>
                        </a>
                    @else
                        <button type="submit" 
                                data-confirm="Seluruh transaksi hari ini akan dikunci dan siap ditarik ke Akuntansi. Pastikan jumlah uang fisik sudah sesuai."
                                data-confirm-title="Tutup kas harian?"
                                data-confirm-ok="Tutup Kas"
                                data-confirm-theme="rose"
                                data-confirm-icon="lock"
                                class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Kunci Shift & Tutup Kas Harian</span>
                        </button>
                    @endif
                </div>
            </div>
        </form>

        <!-- Tabel Riwayat Closing Shift Sebelumnya -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                    Riwayat Tutup Kas Harian (Closing Terakhir)
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-emerald-700 text-white font-bold text-[11px] uppercase tracking-wider border-b border-emerald-800">
                        <tr>
                            <th class="px-4 py-3 font-bold text-white">Shift ID</th>
                            <th class="px-4 py-3 font-bold text-white">Tanggal Shift</th>
                            <th class="px-4 py-3 font-bold text-white">Kasir</th>
                            <th class="px-4 py-3 font-bold text-white text-right">Saldo Awal</th>
                            <th class="px-4 py-3 font-bold text-white text-right">Total KI</th>
                            <th class="px-4 py-3 font-bold text-white text-right">Total KO</th>
                            <th class="px-4 py-3 font-bold text-white text-right">Saldo Fisik</th>
                            <th class="px-4 py-3 font-bold text-white text-right">Selisih</th>
                            <th class="px-4 py-3 font-bold text-white text-center">Waktu Closing</th>
                            <th class="px-4 py-3 font-bold text-white text-center">Izin Buka Kas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono">
                        @forelse($riwayatClosing as $sc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-bold text-slate-900">#{{ $sc->id }}</td>
                            <td class="px-4 py-3 font-sans text-slate-600">{{ $sc->tanggal_shift ? \Carbon\Carbon::parse($sc->tanggal_shift)->format('d/m/Y') : ($sc->tgl_shift ? \Carbon\Carbon::parse($sc->tgl_shift)->format('d/m/Y') : '-') }}</td>
                            <td class="px-4 py-3 font-sans font-bold text-slate-800">{{ $sc->kasir->name ?? 'Kasir' }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($sc->saldo_awal_fisik, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-teal-700">+ Rp {{ number_format($sc->total_ki ?? $sc->total_masuk, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-rose-700">- Rp {{ number_format($sc->total_ko ?? $sc->total_keluar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-black text-slate-900">Rp {{ number_format($sc->saldo_akhir_fisik, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-bold {{ ($sc->selisih ?? $sc->selisih_kas) == 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ ($sc->selisih ?? $sc->selisih_kas) == 0 ? 'Rp 0' : number_format($sc->selisih ?? $sc->selisih_kas, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center font-sans text-slate-500">
                                {{ $sc->closed_at ? \Carbon\Carbon::parse($sc->closed_at)->format('d/m/Y H:i') : ($sc->updated_at ? $sc->updated_at->format('d/m/Y H:i') : '-') }}
                            </td>
                            <td class="px-4 py-3 text-center font-sans">
                                @if($sc->reopen_status === 'requested' || $sc->reopen_requested)
                                    <a href="{{ route('kasir.utilitas.buka-kas') }}" class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300 hover:bg-amber-100">
                                        Menunggu ACC Accounting
                                    </a>
                                @elseif($sc->reopen_status === 'approved')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                        Diizinkan (Re-opened)
                                    </span>
                                @else
                                    <button type="button"
                                            @click="reopenTarget = { id: {{ $sc->id }}, tanggal: '{{ $sc->tanggal_shift ? \Carbon\Carbon::parse($sc->tanggal_shift)->format('d/m/Y') : ($sc->tgl_shift ? \Carbon\Carbon::parse($sc->tgl_shift)->format('d/m/Y') : '-') }}' }; showReopenModal = true"
                                            class="px-2.5 py-1 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 cursor-pointer">
                                        Ajukan Berita Acara Re-open
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-5 py-6 text-center text-slate-400 font-sans">Belum ada riwayat closing sebelumnya.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Pengajuan Berita Acara Re-open Kas Harian (SOP #6) -->
        <div x-cloak x-show="showReopenModal && reopenTarget.id" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showReopenModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-3.5 bg-amber-50 border-b border-amber-100 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-amber-950">Pengajuan Berita Acara Buka Kembali Kas</h4>
                        <p class="text-[11px] text-amber-700" x-text="'Shift #' + reopenTarget.id + ' • Tanggal: ' + reopenTarget.tanggal"></p>
                    </div>
                    <button type="button" @click="showReopenModal = false" class="text-amber-500 hover:text-amber-800 font-bold text-lg">&times;</button>
                </div>
                <form method="POST" :action="'{{ url('/kasir/utilitas/buka-kas') }}/' + reopenTarget.id + '/ajukan'" class="p-5 space-y-4 text-xs">
                    @csrf
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-[11px] text-slate-700">
                        Sesuai <b>SOP Tutup Kas Harian & Penarikan Database Akuntansi</b>: Pembukaan kembali (<i>re-open</i>) kas yang sudah ditutup hanya dapat dilakukan melalui pengajuan Berita Acara yang di-ACC oleh Role <b>Accounting / Admin / Manajer Keuangan</b>.
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Uraian Berita Acara Pembukaan Kembali Kas <span class="text-rose-600">*</span></label>
                        <textarea name="berita_acara_reopen" rows="3" required minlength="10"
                                  placeholder="Jelaskan alasan pengajuan buka kembali kas (minimal 10 karakter), misal: Koreksi selisih input kuitansi iklan sore hari..."
                                  class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-100 focus:border-amber-500 text-slate-900"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showReopenModal = false" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-xs">
                            Kirim Berita Acara ke Accounting
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
