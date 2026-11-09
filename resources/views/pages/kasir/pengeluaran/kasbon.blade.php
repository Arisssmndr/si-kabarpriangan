<x-app-layout>
    <div class="space-y-5" x-data="{ 
        activeTab: 'bs',
        showCetakModal: {{ session('cetak_kasbon') ? 'true' : 'false' }},
        showSelesaiModal: false,
        showAccModal: false,
        showTolakModal: false,
        selesaiTarget: { id: null, no: '', nominal: 0, realisasi: 0, no_bkkc: '{{ $nextNoBkkc ?? '' }}', tipe: 'selesai_bkkc', penerima: '' },
        accTarget: { id: null, no: '', nominal: 0, penerima: '', keperluan: '' },
        tolakTarget: { id: null, no: '', nominal: 0, penerima: '' },
        bkkcForm: {
            kasbonId: '',
            namaPenerima: '',
            keperluan: '',
            nominalBs: 0,
            nominalRealisasi: 0,
            noBkkcRef: '{{ $nextNoBkkc ?? '' }}',
            kodeBagian: '03'
        },
        form: {
            namaPenerima: '',
            keperluan: '',
            nominal: '',
            statusAcc: 'langsung',
            approvedBy: 'Ibu Eli Yulianti (Asisten Manajer Keuangan)',
            keterangan: ''
        },
        selectBsForBkkc(event) {
            const opt = event.target.selectedOptions[0];
            if (opt && opt.value) {
                this.bkkcForm.namaPenerima = opt.dataset.penerima || '';
                this.bkkcForm.keperluan = opt.dataset.keperluan || '';
                this.bkkcForm.nominalBs = parseFloat(opt.dataset.nominal || 0);
                this.bkkcForm.nominalRealisasi = parseFloat(opt.dataset.nominal || 0);
            } else {
                this.bkkcForm.nominalBs = 0;
            }
        }
    }">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-5" :class="{ 'print:hidden print-hide-when-modal-open': showCetakModal }">

            <!-- Header Halaman (60-30-10) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Kasbon & Kas Kecil Operasional
                        </h1>
                        <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                            Pencatatan bon sementara (BS), realisasi nota BKKC, dan pengisian kembali kas kecil (LKK).
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                    @if(session('cetak_kasbon'))
                        <button type="button" @click="showCetakModal = true" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak Slip Terakhir</span>
                        </button>
                    @endif
                    <a href="{{ route('kasir.laporan.posisi-kas') }}" class="inline-flex items-center gap-2 h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer whitespace-nowrap">
                        <span>Posisi Kas Fisik</span>
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                    <span>{{ session('success') }}</span>
                    <button type="button" @click="$el.parentElement.remove()" class="text-kp-blue-600 hover:text-kp-blue-800 font-bold px-1 cursor-pointer">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                    <span>{{ session('error') }}</span>
                    <button type="button" @click="$el.parentElement.remove()" class="text-slate-500 hover:text-slate-800 font-bold px-1 cursor-pointer">&times;</button>
                </div>
            @endif

            <!-- 4 Kartu KPI -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kasbon Gantung Aktif</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($totalKasbonGantung, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        {{ $countKasbonGantung }} slip aktif pemotong kas laci
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Menunggu ACC Keuangan</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($totalMenungguAcc, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        {{ $countMenungguAcc }} pengajuan menunggu otorisasi
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Estimasi Kas Fisik Laci</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($saldoFisikLaci, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block truncate">
                        Sistem Rp {{ number_format($saldoTeoritisShift, 0, ',', '.') }} dikurangi kasbon
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kasbon Bulan Ini</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($totalKasbonBulanIni, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        Akumulasi uang muka periode berjalan
                    </span>
                </div>
            </div>

            <!-- Navigasi Tab Kas Kecil -->
            <div class="bg-white border border-slate-200 rounded-xl p-1.5 shadow-xs flex flex-wrap items-center gap-1.5">
                <button type="button" @click="activeTab = 'bs'"
                        class="px-4 py-2 rounded-lg text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer"
                        :class="activeTab === 'bs' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-semibold'">
                    <span>1. Bon Sementara (BS)</span>
                    <span class="px-2 py-0.5 rounded-md text-xs font-mono" :class="activeTab === 'bs' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'">{{ $countKasbonGantung }}</span>
                </button>
                <button type="button" @click="activeTab = 'bkkc'"
                        class="px-4 py-2 rounded-lg text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer"
                        :class="activeTab === 'bkkc' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-semibold'">
                    <span>2. Penyelesaian & BKKC</span>
                </button>
                <button type="button" @click="activeTab = 'lkk'"
                        class="px-4 py-2 rounded-lg text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer"
                        :class="activeTab === 'lkk' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-semibold'">
                    <span>3. Pengisian Kembali (LKK & KI-09)</span>
                    @if(($bkkcSiapLkk ?? collect())->count() > 0)
                        <span class="px-2 py-0.5 rounded-md text-xs font-mono" :class="activeTab === 'lkk' ? 'bg-white/20 text-white' : 'bg-kp-blue-50 text-kp-blue-700'">{{ $bkkcSiapLkk->count() }}</span>
                    @endif
                </button>
            </div>

            <!-- TAB 1: BON SEMENTARA (BS / KASBON) & MONITORING -->
            <div x-show="activeTab === 'bs'" class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                
                <!-- Form Pengeluaran Kasbon -->
                <div class="lg:col-span-5 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden sticky top-4">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Form Bon Sementara (BS)</h2>
                            <p class="text-xs text-slate-500">Pengeluaran uang muka meja kasir</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                            {{ $nextNoBs ?? 'KB-BARU' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('kasir.pengeluaran.kasbon.store') }}" class="p-4 sm:p-5 space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Penerima</label>
                            <input type="text" name="nama_penerima" x-model="form.namaPenerima" required placeholder="Nama penerima uang muka..." 
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keperluan</label>
                            <input type="text" name="keperluan" x-model="form.keperluan" required placeholder="Uraian keperluan pengeluaran..." 
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nominal BS (Rp)</label>
                            <input type="number" name="nominal" x-model="form.nominal" required min="1000" step="500" placeholder="0" 
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Otorisasi (ACC)</label>
                            <div class="grid grid-cols-2 gap-2.5 text-xs">
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition"
                                       :class="form.statusAcc === 'langsung' ? 'bg-kp-blue-50 border-kp-blue-200 text-kp-blue-700 font-bold' : 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50'">
                                    <input type="radio" name="status_acc" value="langsung" x-model="form.statusAcc" class="text-kp-blue-600 focus:ring-kp-blue-600">
                                    <span>Sudah ACC</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition"
                                       :class="form.statusAcc === 'menunggu' ? 'bg-kp-blue-50 border-kp-blue-200 text-kp-blue-700 font-bold' : 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50'">
                                    <input type="radio" name="status_acc" value="menunggu" x-model="form.statusAcc" class="text-kp-blue-600 focus:ring-kp-blue-600">
                                    <span>Menunggu ACC</span>
                                </label>
                            </div>
                        </div>

                        <div x-show="form.statusAcc === 'langsung'" x-transition>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pejabat Penyetuju</label>
                            <select name="approved_by" x-model="form.approvedBy" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                <option value="Ibu Eli Yulianti (Asisten Manajer Keuangan)">Ibu Eli Yulianti (Asisten Manajer Keuangan)</option>
                                <option value="Moch. Al Nurdin (Pemimpin Perusahaan)">Moch. Al Nurdin (Pemimpin Perusahaan)</option>
                                <option value="Admin Keuangan">Admin Keuangan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan (Opsional)</label>
                            <textarea name="keterangan" x-model="form.keterangan" rows="2" placeholder="Catatan tambahan..." 
                                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                        </div>

                        <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-sm font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            <span>Simpan Bon Sementara</span>
                        </button>
                    </form>
                </div>

                <!-- Tabel Monitoring Kasbon -->
                <div class="lg:col-span-7 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden flex flex-col">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Daftar Bon Sementara (BS)</h2>
                            <p class="text-xs text-slate-500">Status persetujuan dan penyelesaian kasbon</p>
                        </div>

                        <div class="flex items-center gap-1.5 flex-wrap">
                            <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'all']) }}" 
                               class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ ($statusFilter ?? 'all') === 'all' ? 'bg-kp-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                                Semua
                            </a>
                            <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'menunggu_acc']) }}" 
                               class="px-2.5 py-1 rounded-lg text-xs font-semibold transition flex items-center gap-1 {{ ($statusFilter ?? '') === 'menunggu_acc' ? 'bg-kp-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                                <span>Menunggu ACC</span>
                                @if($countMenungguAcc > 0)
                                    <span class="font-mono">({{ $countMenungguAcc }})</span>
                                @endif
                            </a>
                            <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'pending']) }}" 
                               class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ ($statusFilter ?? '') === 'pending' ? 'bg-kp-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                                BS Aktif
                            </a>
                            <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'selesai_bkkc']) }}" 
                               class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ ($statusFilter ?? '') === 'selesai_bkkc' ? 'bg-kp-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                                Selesai BKKC
                            </a>
                            <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'lunas']) }}" 
                               class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ ($statusFilter ?? '') === 'lunas' ? 'bg-kp-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                                Lunas Tunai
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3">No. BS / Ref</th>
                                    <th class="py-3 px-3">Tanggal</th>
                                    <th class="py-3 px-3">Penerima & Keperluan</th>
                                    <th class="py-3 px-3 text-right">Nominal</th>
                                    <th class="py-3 px-3 text-center">Status</th>
                                    <th class="py-3 px-3">Otorisasi</th>
                                    <th class="py-3 px-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($kasbonList as $kb)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <div class="font-mono font-bold text-kp-blue-700 text-sm">{{ $kb->no_bs ?? $kb->no_kasbon }}</div>
                                        @if($kb->no_bkkc_ref)
                                            <span class="inline-block mt-0.5 text-[11px] font-mono px-1.5 py-0.5 rounded bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                {{ $kb->no_bkkc_ref }}
                                            </span>
                                        @endif
                                        @if($kb->no_lkk_ref)
                                            <span class="inline-block mt-0.5 text-[11px] font-mono px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $kb->no_lkk_ref }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 font-mono text-slate-600 whitespace-nowrap">{{ Carbon\Carbon::parse($kb->tanggal)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3 min-w-[160px]">
                                        <div class="font-semibold text-slate-900">{{ $kb->nama_penerima }}</div>
                                        <div class="text-xs text-slate-500 line-clamp-1">{{ $kb->keperluan }}</div>
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono tabular-nums whitespace-nowrap">
                                        <div class="font-semibold text-slate-900">
                                            Rp {{ number_format($kb->nominal_bs ?: $kb->nominal, 0, ',', '.') }}
                                        </div>
                                        @if($kb->nominal_realisasi !== null)
                                            <div class="text-xs text-kp-blue-700">
                                                Nota: Rp {{ number_format($kb->nominal_realisasi, 0, ',', '.') }}
                                            </div>
                                        @endif
                                        @if(($kb->selisih_kembali_tunai ?? 0) > 0)
                                            <div class="text-xs text-slate-500">
                                                Kembali: Rp {{ number_format($kb->selisih_kembali_tunai, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        @if($kb->status === 'menunggu_acc')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                Menunggu ACC
                                            </span>
                                        @elseif(in_array($kb->status, ['pending', 'aktif']))
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                BS Aktif
                                            </span>
                                        @elseif($kb->status === 'selesai_bkkc')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                Selesai BKKC
                                            </span>
                                        @elseif($kb->status === 'lunas')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                Lunas Kembali
                                            </span>
                                        @elseif($kb->status === 'masuk_bp')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                Masuk BP
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-400 border border-slate-200">
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-xs text-slate-600 whitespace-nowrap">
                                        @if($kb->approved_by)
                                            <span class="font-medium text-slate-700">{{ Str::limit($kb->approved_by, 18) }}</span>
                                        @else
                                            <span class="text-slate-400">Belum ACC</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        @if($kb->status === 'menunggu_acc')
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button type="button" 
                                                        @click="accTarget = { id: {{ $kb->id }}, no: '{{ $kb->no_kasbon }}', nominal: {{ $kb->nominal }}, penerima: '{{ addslashes($kb->nama_penerima) }}', keperluan: '{{ addslashes($kb->keperluan) }}' }; showAccModal = true"
                                                        class="px-2.5 py-1 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-lg transition cursor-pointer">
                                                    ACC
                                                </button>
                                                <button type="button" 
                                                        @click="tolakTarget = { id: {{ $kb->id }}, no: '{{ $kb->no_kasbon }}', nominal: {{ $kb->nominal }}, penerima: '{{ addslashes($kb->nama_penerima) }}' }; showTolakModal = true"
                                                        class="px-2.5 py-1 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 font-semibold text-xs rounded-lg transition cursor-pointer">
                                                    Tolak
                                                </button>
                                            </div>
                                        @elseif(in_array($kb->status, ['pending', 'aktif']))
                                            <button type="button" 
                                                    @click="selesaiTarget = { id: {{ $kb->id }}, no: '{{ $kb->no_bs ?? $kb->no_kasbon }}', nominal: {{ (float)($kb->nominal_bs ?: $kb->nominal) }}, realisasi: {{ (float)($kb->nominal_bs ?: $kb->nominal) }}, no_bkkc: '{{ $nextNoBkkc ?? '' }}', tipe: 'selesai_bkkc', penerima: '{{ addslashes($kb->nama_penerima) }}' }; showSelesaiModal = true"
                                                    class="px-3 py-1.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-lg transition cursor-pointer">
                                                Selesaikan
                                            </button>
                                        @else
                                            <span class="text-xs font-mono text-slate-400">{{ $kb->selesai_at ? Carbon\Carbon::parse($kb->selesai_at)->format('d/m/Y') : '-' }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-500">
                                        Tidak ada catatan kasbon pada status ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($kasbonList->hasPages())
                    <div class="p-3.5 border-t border-slate-200 bg-slate-50">
                        {{ $kasbonList->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- TAB 2: PENYELESAIAN KASBON & PENGGANTIAN BIAYA (BKKC) -->
            <div x-cloak x-show="activeTab === 'bkkc'" class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                <div class="lg:col-span-6 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Form Penyelesaian BS & Penggantian Biaya (BKKC)</h2>
                            <p class="text-xs text-slate-500">Pilih BS aktif atau input penggantian nota langsung</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                            {{ $nextNoBkkc ?? 'BKKC-AUTO' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('kasir.pengeluaran.kasbon.bkkc.store') }}" class="p-4 sm:p-5 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor BS Menggantung (Opsional)</label>
                            <select name="kasbon_id" x-model="bkkcForm.kasbonId" @change="selectBsForBkkc($event)"
                                    class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                <option value="">-- Tanpa BS (Penggantian Biaya Langsung) --</option>
                                @foreach($kasbonAktifList ?? [] as $bsAktif)
                                    <option value="{{ $bsAktif->id }}"
                                            data-penerima="{{ $bsAktif->nama_penerima }}"
                                            data-keperluan="{{ $bsAktif->keperluan }}"
                                            data-nominal="{{ (float)($bsAktif->nominal_bs ?: $bsAktif->nominal) }}">
                                        {{ $bsAktif->no_bs ?? $bsAktif->no_kasbon }} — {{ $bsAktif->nama_penerima }} (BS: Rp {{ number_format($bsAktif->nominal_bs ?: $bsAktif->nominal, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor Referensi BKKC</label>
                                <input type="text" name="no_bkkc_ref" x-model="bkkcForm.noBkkcRef" placeholder="{{ $nextNoBkkc ?? 'BKKC-...' }}"
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Divisi Beban</label>
                                <select name="kode_bagian" x-model="bkkcForm.kodeBagian" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                    <option value="03">03 - Umum & SDM</option>
                                    <option value="06">06 - Redaksi</option>
                                    <option value="01">01 - Iklan</option>
                                    <option value="02">02 - Sirkulasi</option>
                                    <option value="05">05 - Keuangan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Penerima</label>
                                <input type="text" name="nama_penerima" x-model="bkkcForm.namaPenerima" required placeholder="Nama karyawan..."
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Uraian Realisasi Nota</label>
                                <input type="text" name="keperluan" x-model="bkkcForm.keperluan" required placeholder="Uraian belanja sesuai nota..."
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nilai Realisasi Nota (Rp)</label>
                                <input type="number" name="nominal_realisasi" x-model="bkkcForm.nominalRealisasi" required min="0"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600">
                            </div>
                            <div class="flex flex-col justify-center">
                                <span class="text-xs text-slate-500 font-semibold">Selisih Kembali Tunai ke Laci:</span>
                                <span class="text-lg font-mono font-bold text-kp-blue-700 mt-0.5 tabular-nums">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(Math.max(0, (bkkcForm.nominalBs || 0) - (bkkcForm.nominalRealisasi || 0)))"></span>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                            Simpan Penyelesaian BKKC
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-6 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Daftar BKKC Siap LKK</h2>
                            <p class="text-xs text-slate-500">Bukti kas kecil selesai yang siap direkap ke LKK</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                            {{ count($bkkcSiapLkk ?? []) }} BKKC
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3">No. BKKC / BS</th>
                                    <th class="py-3 px-3">Penerima & Nota</th>
                                    <th class="py-3 px-3 text-right">Nominal BS</th>
                                    <th class="py-3 px-3 text-right">Realisasi</th>
                                    <th class="py-3 px-3 text-right">Kembali</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($bkkcSiapLkk ?? [] as $item)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-3 font-mono whitespace-nowrap">
                                        <div class="font-bold text-kp-blue-700">{{ $item->no_bkkc_ref ?? $item->no_kasbon }}</div>
                                        <div class="text-xs text-slate-400">{{ $item->no_bs ?? $item->no_kasbon }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-semibold text-slate-900">{{ $item->nama_penerima }}</div>
                                        <div class="text-xs text-slate-500">{{ $item->keperluan }}</div>
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono tabular-nums whitespace-nowrap">Rp {{ number_format($item->nominal_bs ?: $item->nominal, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-right font-mono font-semibold text-slate-900 tabular-nums whitespace-nowrap">Rp {{ number_format($item->nominal_realisasi ?? $item->nominal, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-right font-mono text-kp-blue-700 tabular-nums whitespace-nowrap">Rp {{ number_format($item->selisih_kembali_tunai ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-slate-500">Belum ada BKKC yang menunggu rekap LKK.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 3: PENGISIAN KEMBALI KAS KECIL (LKK & PENCAIRAN CEK KI-09) -->
            <div x-cloak x-show="activeTab === 'lkk'" class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                <div class="lg:col-span-6 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Rekap LKK & Pencairan Cek (KI-09)</h2>
                            <p class="text-xs text-slate-500">Pengisian kembali kas kecil dari rekening bank</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                            {{ $nextNoLkk ?? 'LKK-AUTO' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('kasir.pengeluaran.kasbon.lkk.store') }}" class="p-4 sm:p-5 space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor LKK</label>
                                <input type="text" name="no_lkk_ref" value="{{ $nextNoLkk ?? '' }}" required
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-kp-blue-700 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Total Pengisian (Rp)</label>
                                <input type="number" name="nominal_pengisian" value="{{ (float)($totalSiapLkk ?? 0) }}" min="0"
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Daftar BKKC Dihimpun</label>
                            <div class="max-h-44 overflow-y-auto border border-slate-200 rounded-xl p-2.5 space-y-1.5 bg-slate-50">
                                @forelse($bkkcSiapLkk ?? [] as $bkkc)
                                    <label class="flex items-center justify-between p-2.5 bg-white rounded-lg border border-slate-200 cursor-pointer text-xs">
                                        <span class="flex items-center gap-2 min-w-0">
                                            <input type="checkbox" name="kasbon_ids[]" value="{{ $bkkc->id }}" checked class="rounded text-kp-blue-600 focus:ring-kp-blue-600">
                                            <span class="font-mono font-bold text-kp-blue-700">{{ $bkkc->no_bkkc_ref ?? $bkkc->no_kasbon }}</span>
                                            <span class="text-slate-600 truncate">({{ $bkkc->nama_penerima }} - {{ Str::limit($bkkc->keperluan, 28) }})</span>
                                        </span>
                                        <span class="font-mono font-semibold text-slate-900 shrink-0 ml-2">Rp {{ number_format($bkkc->nominal_realisasi ?? $bkkc->nominal, 0, ',', '.') }}</span>
                                    </label>
                                @empty
                                    <div class="text-slate-500 text-xs text-center py-3">Tidak ada BKKC menunggu LKK. Pencairan KI-09 tetap dapat dicatat langsung.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-900">
                                <input type="checkbox" name="cairkan_cek_ki09" value="1" checked class="rounded text-kp-blue-600 focus:ring-kp-blue-600 w-4 h-4">
                                <span>Cairkan Cek Pengisian Kembali & Terbitkan KI-09</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Cek / BBK</label>
                                    <input type="text" name="no_cek_bbk" placeholder="CEK-BJB-LKK-001"
                                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rekening Sumber Dana</label>
                                    <select name="bank_sumber" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                        <option value="Bank BJB Operasional">Bank BJB Operasional</option>
                                        <option value="Bank BCA Tasikmalaya">Bank BCA Tasikmalaya</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                            Terbitkan LKK & Catat KI-09
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-6 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200">
                        <h2 class="text-sm font-bold text-slate-900">Riwayat LKK & Pencairan KI-09</h2>
                        <p class="text-xs text-slate-500">Daftar penarikan cek pengisian kembali kas kecil</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3">No. KI-09</th>
                                    <th class="py-3 px-3">Tanggal</th>
                                    <th class="py-3 px-3">Bank & Keterangan</th>
                                    <th class="py-3 px-3 text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($penarikanBankLkk ?? [] as $ki09)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-3 font-mono font-bold text-kp-blue-700 whitespace-nowrap">{{ $ki09->no_kuitansi }}</td>
                                    <td class="py-3 px-3 font-mono text-slate-600 whitespace-nowrap">{{ $ki09->tanggal ? $ki09->tanggal->format('d/m/Y') : '-' }}</td>
                                    <td class="py-3 px-3">
                                        <div class="font-semibold text-slate-900">{{ $ki09->bank_tujuan ?? 'Bank BJB' }}</div>
                                        <div class="text-xs text-slate-500">{{ $ki09->keterangan }}</div>
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono font-semibold text-slate-900 tabular-nums whitespace-nowrap">Rp {{ number_format($ki09->nominal_bayar, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-slate-500">Belum ada riwayat penarikan cek LKK (KI-09).</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Persetujuan (ACC) Kasbon -->
        <div x-cloak x-show="showAccModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="showAccModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">ACC Kasbon Keuangan</h3>
                        <p class="text-xs text-slate-500">Persetujuan pengeluaran uang muka kasir</p>
                    </div>
                    <button type="button" @click="showAccModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/pengeluaran/kasbon') }}/' + accTarget.id + '/acc'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-slate-500">No. Kasbon:</span>
                            <span class="font-mono font-bold text-kp-blue-700" x-text="accTarget.no"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Penerima:</span>
                            <span class="font-semibold text-slate-900" x-text="accTarget.penerima"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Keperluan:</span>
                            <span class="text-slate-700" x-text="accTarget.keperluan"></span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 pt-1.5">
                            <span class="text-slate-700 font-semibold">Nominal:</span>
                            <span class="font-bold font-mono text-kp-blue-700 text-sm">Rp <span x-text="new Intl.NumberFormat('id-ID').format(accTarget.nominal)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pejabat Penyetuju (ACC)</label>
                        <select name="approved_by" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                            <option value="Ibu Eli Yulianti (Asisten Manajer Keuangan)">Ibu Eli Yulianti (Asisten Manajer Keuangan)</option>
                            <option value="Moch. Al Nurdin (Pemimpin Perusahaan)">Moch. Al Nurdin (Pemimpin Perusahaan)</option>
                            <option value="{{ Auth::user()->name }} (Keuangan)">{{ Auth::user()->name }} (Keuangan)</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showAccModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl transition cursor-pointer">
                            Setujui & ACC
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tolak Kasbon -->
        <div x-cloak x-show="showTolakModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="showTolakModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tolak Permohonan Kasbon</h3>
                        <p class="text-xs text-slate-500">Konfirmasi pembatalan pengajuan kasbon</p>
                    </div>
                    <button type="button" @click="showTolakModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/pengeluaran/kasbon') }}/' + tolakTarget.id + '/tolak'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-slate-500">No. Kasbon:</span>
                            <span class="font-mono font-bold text-kp-blue-700" x-text="tolakTarget.no"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Penerima:</span>
                            <span class="font-semibold text-slate-900" x-text="tolakTarget.penerima"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Penolakan</label>
                        <input type="text" name="alasan_tolak" required placeholder="Masukkan alasan penolakan..." 
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showTolakModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition cursor-pointer">
                            Tolak Kasbon
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Selesaikan Kasbon -->
        <div x-cloak x-show="showSelesaiModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="showSelesaiModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Selesaikan Kasbon</h3>
                        <p class="text-xs text-slate-500">Realisasi nota BKKC, pengembalian tunai, atau alih BP</p>
                    </div>
                    <button type="button" @click="showSelesaiModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/pengeluaran/kasbon') }}/' + selesaiTarget.id + '/selesai'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-slate-500">No. Kasbon:</span>
                            <span class="font-mono font-bold text-kp-blue-700" x-text="selesaiTarget.no"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Penerima:</span>
                            <span class="font-semibold text-slate-900" x-text="selesaiTarget.penerima"></span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 pt-1.5">
                            <span class="text-slate-700 font-semibold">Nominal BS:</span>
                            <span class="font-bold font-mono text-slate-900 text-sm">Rp <span x-text="new Intl.NumberFormat('id-ID').format(selesaiTarget.nominal)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Penyelesaian</label>
                        <select name="tipe_selesai" x-model="selesaiTarget.tipe" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600">
                            <option value="selesai_bkkc">1. Realisasi Nota BKKC + Kembalikan Sisa Tunai</option>
                            <option value="lunas">2. Uang Fisik Dikembalikan Utuh 100% Tunai</option>
                            <option value="masuk_bp">3. Diterbitkan Nomor BP Anggaran Resmi</option>
                        </select>
                    </div>

                    <div x-show="selesaiTarget.tipe !== 'lunas'" class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2.5 text-xs">
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Nilai Nota (Rp)</label>
                                <input type="number" name="nominal_realisasi" x-model="selesaiTarget.realisasi" min="0"
                                       class="w-full h-9 px-2.5 bg-white border border-slate-300 rounded-lg font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Nomor BKKC / BP</label>
                                <input type="text" name="no_bkkc_ref" x-model="selesaiTarget.no_bkkc" placeholder="BKKC-..."
                                       class="w-full h-9 px-2.5 bg-white border border-slate-300 rounded-lg font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600">
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-1.5 border-t border-slate-200">
                            <span class="text-slate-600 font-semibold">Selisih Kembali ke Laci:</span>
                            <span class="font-mono font-bold text-kp-blue-700 text-sm">
                                Rp <span x-text="new Intl.NumberFormat('id-ID').format(Math.max(0, (selesaiTarget.nominal || 0) - (selesaiTarget.realisasi || 0)))"></span>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Penyelesaian</label>
                        <input type="text" name="catatan" placeholder="Catatan nota dan sisa kembalian..." 
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showSelesaiModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl transition cursor-pointer">
                            Simpan Penyelesaian
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Cetak Voucher Kasbon -->
        @if(session('cetak_kasbon'))
        @php $kb = session('cetak_kasbon'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                <div class="border-b-2 border-slate-900 px-6 py-4 flex items-start justify-between shrink-0 bg-white">
                    <div>
                        <div class="text-base font-bold tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500">PT. Priangan Berkah Media - Bagian Perkasiran</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-md bg-kp-blue-50 text-kp-blue-700 uppercase tracking-wider border border-kp-blue-200">
                            VOUCHER KASBON
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1.5">{{ $kb['no_kasbon'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $kb['tanggal'] }}</div>
                    </div>
                </div>

                <div class="print-document-body flex-1 overflow-y-auto p-6 space-y-3 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Diberikan Kepada</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $kb['penerima'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Keperluan Uang Muka</span>
                        <span class="col-span-2 text-slate-800 font-medium">: {{ $kb['keperluan'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Disetujui Oleh (ACC)</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $kb['approved_by'] }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-2.5 bg-slate-50 px-4 rounded-xl border border-slate-200 my-2.5">
                        <span class="font-bold text-slate-900 self-center">Nominal Uang Kasbon</span>
                        <span class="col-span-2 font-mono font-bold text-lg text-kp-blue-700">: Rp {{ number_format($kb['nominal'], 0, ',', '.') }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-2 text-xs bg-slate-50 px-4 rounded-xl border border-slate-200">
                        <span class="text-slate-500 font-semibold">Terbilang Huruf</span>
                        <span class="col-span-2 font-semibold italic text-slate-900">: {{ $kb['terbilang'] }}</span>
                    </div>

                    <div class="pt-6 border-t border-slate-200 grid grid-cols-3 gap-2 text-center text-xs text-slate-600">
                        <div>
                            <div class="font-semibold text-slate-700">Penerima Kasbon</div>
                            <div class="h-14"></div>
                            <div class="font-bold text-slate-900 underline">({{ $kb['penerima'] }})</div>
                        </div>
                        <div>
                            <div class="font-semibold text-slate-700">Menyetujui (Keuangan)</div>
                            <div class="h-14"></div>
                            <div class="font-bold text-slate-900 underline">({{ Str::words($kb['approved_by'], 2, '') }})</div>
                        </div>
                        <div>
                            <div class="font-semibold text-slate-700">Kasir Pembayar</div>
                            <div class="h-14"></div>
                            <div class="font-bold text-slate-900 font-mono underline">({{ $kb['kasir'] }})</div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2.5 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
