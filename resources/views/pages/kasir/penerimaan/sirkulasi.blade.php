<x-app-layout>
    <div class="space-y-6" x-data="{ 
        showSetorModal: false, 
        showCetakModal: {{ session('cetak_sirkulasi') ? 'true' : 'false' }},
        activeAgen: { 
            id: null,
            kode: '',
            nama: '', 
            wilayah: '', 
            piutang: 0,
            nominal: 0,
            noBapr: '',
            returEks: 0,
            nilaiRetur: 0,
            metode: 'tunai',
            bankTujuan: 'Bank BJB Sirkulasi',
            noRef: '',
            penagih: '{{ Auth::user()->name }}',
            keterangan: ''
        },
        get totalPengurangSah() {
            return Number(this.activeAgen.nominal) + Number(this.activeAgen.nilaiRetur);
        },
        get sisaPiutangBaru() {
            let sisa = this.activeAgen.piutang - this.totalPengurangSah;
            return sisa < 0 ? 0 : sisa;
        }
    }">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-6" :class="{ 'print:hidden': showCetakModal }">

        <!-- Alert Flash Message -->
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

        <!-- Header Page Setoran Sirkulasi -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Setoran Sirkulasi Koran Harian
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-sky-50 text-sky-700 border border-sky-200">
                            Badge: Koran
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekonsiliasi tagihan agen koran, perhitungan sah potongan retur BAPR, dan pencatatan kas setoran harian
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if(session('cetak_sirkulasi'))
                    <button type="button" @click="showCetakModal = true" class="px-3.5 py-2 bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold text-xs rounded-xl border border-sky-200 transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti Setoran Terakhir</span>
                    </button>
                @endif
                <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-2 rounded-xl border border-slate-200">
                    Periode: {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                </span>
            </div>
        </div>

        <!-- Perhitungan Ringkas Rekapitulasi Tagihan Agen -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Plafon Kredit Agen</span>
                <div class="mt-2 text-lg font-extrabold text-slate-900">
                    Rp {{ number_format($agenList->sum('plafon_piutang'), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Batas toleransi jatah sirkulasi</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Saldo Piutang Beredar Saat Ini</span>
                <div class="mt-2 text-lg font-extrabold text-rose-600">
                    Rp {{ number_format($agenList->sum('saldo_piutang'), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Total piutang koran belum disetor</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Jatah Default Distribusi Harian</span>
                <div class="mt-2 text-lg font-extrabold text-teal-700">
                    {{ number_format($agenList->sum('jatah_default'), 0, ',', '.') }} Eksemplar
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Kapasitas oplah terlayani kasir & agen</div>
            </div>
        </div>

        <!-- Tabel Tagihan Agen & Form Setoran -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    Daftar Agen Sirkulasi & Status Piutang
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">Terintegrasi dengan Master Agen & Kartu Piutang Akunting</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Kode & Nama Agen</th>
                            <th class="px-4 py-3 font-semibold">Kategori & Rute</th>
                            <th class="px-4 py-3 font-semibold">Wilayah</th>
                            <th class="px-4 py-3 font-semibold text-right">Jatah Eks</th>
                            <th class="px-4 py-3 font-semibold text-right">Plafon Piutang</th>
                            <th class="px-4 py-3 font-semibold text-right">Saldo Piutang Koran</th>
                            <th class="px-4 py-3 font-semibold text-center">Aksi Setor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($agenList as $agen)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $agen->nama_agen }}</div>
                                <span class="font-mono text-[10px] text-slate-400">{{ $agen->kode_agen }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $agen->kategori === '11' ? 'Agen Besar' : 'Sub-Agen' }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $agen->rute_kode ?? 'R-01' }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600 font-medium">{{ $agen->wilayah ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-medium text-slate-700">{{ number_format($agen->jatah_default) }} eks</td>
                            <td class="px-4 py-3 text-right text-slate-600">Rp {{ number_format($agen->plafon_piutang, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-extrabold {{ $agen->saldo_piutang > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                Rp {{ number_format($agen->saldo_piutang, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($agen->saldo_piutang > 0)
                                    <button type="button" 
                                            @click="activeAgen = { 
                                                id: {{ $agen->id }},
                                                kode: '{{ $agen->kode_agen }}',
                                                nama: '{{ addslashes($agen->nama_agen) }}', 
                                                wilayah: '{{ $agen->wilayah ?? '-' }}', 
                                                piutang: {{ $agen->saldo_piutang }}, 
                                                nominal: {{ $agen->saldo_piutang }},
                                                noBapr: '',
                                                returEks: 0,
                                                nilaiRetur: 0,
                                                metode: 'tunai',
                                                bankTujuan: 'Bank BJB Sirkulasi',
                                                noRef: '',
                                                penagih: '{{ Auth::user()->name }}',
                                                keterangan: 'Setoran koran {{ $agen->nama_agen }}'
                                            }; showSetorModal = true"
                                            class="px-2.5 py-1.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-[11px] rounded-lg transition shadow-2xs">
                                        Input Setoran BKM
                                    </button>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Lunas Bersih
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Setoran Sirkulasi -->
        <div x-cloak x-show="showSetorModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showSetorModal = false" class="bg-white rounded-2xl max-w-xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">
                <!-- Header (shrink-0) -->
                <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Form Rekonsiliasi & Setoran Sirkulasi</h4>
                            <p class="text-[11px] text-slate-500">Pencatatan kas masuk setoran agen koran dengan potongan retur BAPR</p>
                        </div>
                    </div>
                    <button type="button" @click="showSetorModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.penerimaan.sirkulasi.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="agen_id" :value="activeAgen.id">
                    <input type="hidden" name="periode" value="{{ \Carbon\Carbon::now()->format('Y-m') }}">

                    <!-- Scrollable Body (flex-1 overflow-y-auto) -->
                    <div class="flex-1 overflow-y-auto px-5 py-3 space-y-3 text-xs">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Nama Agen:</span>
                                <span class="font-bold text-slate-900" x-text="activeAgen.nama + ' (' + activeAgen.kode + ')'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Wilayah Sirkulasi:</span>
                                <span class="font-bold text-slate-800" x-text="activeAgen.wilayah"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Saldo Piutang Koran Saat Ini:</span>
                                <span class="font-bold text-rose-600">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeAgen.piutang)"></span></span>
                            </div>
                        </div>

                        <!-- Identitas Form Divisi Sirkulasi -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 p-2.5 bg-slate-100/70 rounded-xl border border-slate-200 text-xs">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Divisi</label>
                                <div class="py-1 px-2.5 bg-white rounded-lg border border-slate-300 font-bold text-slate-800 flex items-center justify-between text-xs">
                                    <span>Divisi Sirkulasi</span>
                                    <span class="text-[9px] px-1 py-0.2 bg-sky-100 text-sky-800 rounded font-sans font-bold">KAS</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Kode Perkiraan (COA)</label>
                                <select name="kode_perkiraan" class="w-full text-xs py-1 px-2 rounded-lg border border-slate-300 font-mono font-semibold text-slate-800 focus:ring-1 focus:ring-sky-500">
                                    <option value="1.00.04.01.01.00">1.00.04.01.01.00 (Piutang Sirkulasi)</option>
                                    <option value="2.00.01.01.01.00">2.00.01.01.01.00 (Uang Muka Sirkulasi)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-0.5">No. Blanko Fisik (Opsional)</label>
                                <input type="text" name="no_blanko" placeholder="Contoh: BLK-02881" 
                                       class="w-full text-xs py-1 px-2.5 rounded-lg border border-slate-300 font-mono text-slate-800 focus:ring-1 focus:ring-sky-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- Petugas Inkaso Dropdown -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Petugas Inkaso Sirkulasi</label>
                                <select name="penagih_inkaso" x-model="activeAgen.penagih" class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-slate-800">
                                    @foreach($petugasInkasoList as $petugas)
                                        <option value="{{ $petugas->name }}">{{ $petugas->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Metode Setoran -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Metode Setoran</label>
                                <select name="metode_pembayaran" x-model="activeAgen.metode" class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-slate-800">
                                    <option value="tunai">Tunai Meja Kasir</option>
                                    <option value="transfer">Transfer Bank BJB Sirkulasi</option>
                                    <option value="qris">QRIS Resmi Kabar Priangan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Input Khusus Transfer -->
                        <div x-show="activeAgen.metode === 'transfer'" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 p-2.5 bg-sky-50/50 rounded-xl border border-sky-100">
                            <div>
                                <label class="block text-[11px] font-bold text-sky-900 mb-0.5">Bank Tujuan</label>
                                <select name="bank_tujuan" x-model="activeAgen.bankTujuan" class="w-full text-xs py-1 px-2.5 rounded-lg border border-sky-300 focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-slate-800">
                                    <option value="Bank BJB Sirkulasi">Bank BJB Sirkulasi (Giro: 0012-0034-5678)</option>
                                    <option value="Bank Mandiri Sirkulasi">Bank Mandiri (Giro: 131-00-9876543-2)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-sky-900 mb-0.5">No. Referensi / Bukti Transfer</label>
                                <input type="text" name="no_referensi_transfer" x-model="activeAgen.noRef" placeholder="Contoh: TRF-BJB-984128" 
                                       class="w-full text-xs py-1 px-2.5 rounded-lg border border-sky-300 focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-slate-800 font-mono">
                            </div>
                        </div>

                        <!-- BAGIAN KHUSUS RETUR BAPR -->
                        <div class="p-2.5 bg-amber-50/60 rounded-xl border border-amber-200/80 space-y-2">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-amber-900">
                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Koreksi Retur Koran Sah (BAPR - Berita Acara Retur Koran)</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-[11px] font-semibold text-amber-800 mb-0.5">Nomor BAPR Resmi</label>
                                    <input type="text" name="no_bapr" x-model="activeAgen.noBapr" placeholder="BAPR/KP/2026/10/0142" 
                                           class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-amber-300 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-900 font-mono">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-amber-800 mb-0.5">Eksemplar Retur</label>
                                    <input type="number" name="retur_eks" x-model="activeAgen.returEks" min="0" placeholder="0" 
                                           @input="activeAgen.nilaiRetur = activeAgen.returEks * 2100"
                                           class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-amber-300 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-900 font-mono">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-amber-800 mb-0.5">Nilai Rupiah Retur (Rp)</label>
                                    <input type="number" name="nilai_retur" x-model="activeAgen.nilaiRetur" min="0" placeholder="0" 
                                           class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-amber-300 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-900 font-mono font-bold text-amber-700">
                                </div>
                            </div>
                            <span class="text-[10px] text-amber-700 block">Tarif riil agen: Rp 2.100/eks. Nilai potongan retur dihitung otomatis sebagai pengurang piutang sah.</span>
                        </div>

                        <!-- Nominal Uang Yang Disetor -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Uang Kas Masuk / Ditransfer (Rp)</label>
                            <input type="number" name="nominal_setoran" x-model="activeAgen.nominal" required min="1" 
                                   class="w-full text-sm font-mono font-bold py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-slate-900">
                        </div>

                        <!-- Rincian Rekonsiliasi Hasil Setoran -->
                        <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Total Pengurang Sah (Uang + Retur):</span>
                                <span class="font-bold font-mono text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(totalPengurangSah)"></span></span>
                            </div>
                            <div class="flex justify-between items-center text-slate-600 border-t border-slate-200/60 pt-1">
                                <span>Sisa Saldo Piutang Setelah Setoran:</span>
                                <span class="font-bold font-mono text-sm" :class="sisaPiutangBaru === 0 ? 'text-emerald-600' : 'text-amber-600'">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(sisaPiutangBaru)"></span>
                                    <span class="text-[10px] ml-1 font-semibold" x-text="sisaPiutangBaru === 0 ? '(LUNAS BERSIH)' : '(SISA PIUTANG)'"></span>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="keterangan" x-model="activeAgen.keterangan" placeholder="Keterangan setoran agen..." 
                                   class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-slate-900">
                        </div>
                    </div>

                    <!-- Footer Action Buttons (shrink-0) -->
                    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showSetorModal = false" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs rounded-lg transition shadow-2xs">
                            Simpan & Terbitkan BKM Sirkulasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Modal Cetak Tanda Terima Setoran Koran Otomatis -->
        @if(session('cetak_sirkulasi'))
        @php $srk = session('cetak_sirkulasi'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">
                <!-- Header Dokumen -->
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500">Divisi Sirkulasi & Perkasiran Harian</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded bg-sky-100 text-sky-800 uppercase tracking-widest border border-sky-300">
                            TANDA TERIMA SETORAN KORAN
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $srk['no_bkm'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $srk['tanggal'] }}</div>
                    </div>
                </div>

                <!-- Body Dokumen (Scrollable) -->
                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2.5 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Nama Agen</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $srk['nama_agen'] }} ({{ $srk['kode_agen'] }})</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Wilayah Sirkulasi</span>
                        <span class="col-span-2 text-slate-800">: {{ $srk['wilayah'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Petugas Inkaso</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $srk['penagih'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Metode Bayar</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $srk['metode'] }}</span>
                    </div>
                    @if($srk['no_bapr'] !== '-')
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 bg-amber-50/60 px-2 rounded-lg">
                        <span class="text-amber-900 font-semibold">Bukti Retur Sah (BAPR)</span>
                        <span class="col-span-2 font-mono text-amber-900">: {{ $srk['no_bapr'] }} ({{ $srk['retur_eks'] }} eks / Rp {{ number_format($srk['nilai_retur'], 0, ',', '.') }})</span>
                    </div>
                    @endif
                    <div class="grid grid-cols-3 py-1.5 bg-sky-50 px-2 rounded-lg">
                        <span class="font-bold text-sky-900">Uang Setoran Diterima</span>
                        <span class="col-span-2 font-mono font-black text-sm text-sky-800">: Rp {{ $srk['terbilang'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Saldo Piutang Sebelum</span>
                        <span class="col-span-2 font-mono text-slate-700">: Rp {{ number_format($srk['saldo_sebelum'], 0, ',', '.') }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1">
                        <span class="text-slate-500">Sisa Piutang Setelah Setor</span>
                        <span class="col-span-2 font-mono font-bold {{ $srk['saldo_sesudah'] == 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            : Rp {{ number_format($srk['saldo_sesudah'], 0, ',', '.') }} ({{ $srk['status_piutang'] }})
                        </span>
                    </div>

                    <!-- Tanda Tangan Cetak -->
                    <div class="grid grid-cols-2 pt-4 border-t border-slate-200 text-center text-[11px] text-slate-600">
                        <div>
                            <div>Penyetor / Petugas Agen</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">({{ $srk['nama_agen'] }})</div>
                        </div>
                        <div>
                            <div>Kasir Penerima</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900 font-mono">({{ $srk['kasir'] }})</div>
                        </div>
                    </div>
                </div>

                <!-- Action Button Print & Close (Pinned Bottom) -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0 print:hidden">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti Setoran Koran</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
