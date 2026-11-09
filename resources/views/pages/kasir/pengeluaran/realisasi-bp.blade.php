<x-app-layout>
    @php
        $firstApproved = $bpList->where('status', 'approved')->first();
        $firstDiv = strtolower($firstApproved->divisi ?? 'umum');
        $firstBagian = match($firstDiv) {
            'iklan' => '01',
            'sirkulasi' => '02',
            'redaksi' => '06',
            'keuangan' => '05',
            'akuntansi' => '04',
            default => '03'
        };
        $firstCoa = match($firstBagian) {
            '01' => '6.00.01.01.01.02',
            '06' => '5.00.01.01.02',
            default => '6.00.01.00.01.04'
        };
    @endphp

    <div class="space-y-5" x-data="{ 
        showCairModal: false, 
        showTolakModal: false,
        showCetakModal: {{ session('cetak_bkk') ? 'true' : 'false' }},
        bkkPrintData: {{ session('cetak_bkk') ? json_encode(session('cetak_bkk')) : 'null' }},
        activeBp: { 
            id: {{ $firstApproved ? $firstApproved->id : 'null' }},
            no_bp: '{{ $firstApproved ? $firstApproved->no_bp : '' }}', 
            no_register: '{{ $firstApproved ? ($firstApproved->no_register_anggaran ?? 'PR-' . date('Ymd') . '-AUTO') : '' }}',
            no_dp: '{{ $firstApproved ? ($firstApproved->no_dp ?? 'DP-' . $firstApproved->no_bp) : '' }}',
            no_pr: '{{ $firstApproved ? ($firstApproved->no_pr ?? $firstApproved->no_register_anggaran ?? '') : '' }}',
            no_po: '{{ $firstApproved ? ($firstApproved->no_po ?? '') : '' }}',
            no_pum: '{{ $firstApproved ? ($firstApproved->no_pum ?? '') : '' }}',
            no_cek: '',
            pemohon: '{{ $firstApproved ? addslashes($firstApproved->pemohon) : '' }}', 
            divisi: '{{ $firstApproved ? $firstApproved->divisi : '' }}',
            kode_bagian: '{{ $firstBagian }}',
            kode_perkiraan: '{{ $firstCoa }}',
            keperluan: '{{ $firstApproved ? addslashes($firstApproved->keperluan) : '' }}', 
            nominal: {{ $firstApproved ? (float)$firstApproved->nominal_acc : 0 }}, 
            penerima: '{{ $firstApproved ? addslashes($firstApproved->pemohon) : '' }}',
            kategori: '{{ $firstBagian === '06' ? 'redaksi' : ($firstBagian === '02' ? 'bbm_armada' : ($firstBagian === '01' ? 'komisi_kh' : 'umum')) }}',
            metode: 'kas_fisik',
            is_beda_bulan: {{ ($firstApproved && $firstApproved->tanggal_pengajuan && $firstApproved->tanggal_pengajuan->format('Y-m') !== now()->format('Y-m')) ? 'true' : 'false' }},
            bulan_pengajuan: '{{ $firstApproved && $firstApproved->tanggal_pengajuan ? $firstApproved->tanggal_pengajuan->translatedFormat('F Y') : now()->translatedFormat('F Y') }}',
            catatan: '{{ $firstApproved ? 'Pencairan BP ' . $firstApproved->no_bp : '' }}'
        },
        updateCoa() {
            const coaMap = {
                '01': { coa: '6.00.01.01.01.02', kat: 'komisi_kh', label: 'Beban Komisi Humas & Insentif AE' },
                '02': { coa: '6.00.01.00.01.04', kat: 'bbm_armada', label: 'Beban Sirkulasi & Ekspedisi / BBM' },
                '03': { coa: '6.00.01.00.01.04', kat: 'umum', label: 'Beban Umum & SDM / RT / BBM' },
                '04': { coa: '6.00.01.01.01.03', kat: 'umum', label: 'Beban Administrasi Akuntansi' },
                '05': { coa: '6.00.01.01.01.02', kat: 'umum', label: 'Beban Keuangan & Pajak' },
                '06': { coa: '5.00.01.01.02', kat: 'redaksi', label: 'Beban Liputan Redaksi & Honor' },
                '07': { coa: '6.00.01.01.00.00', kat: 'umum', label: 'Beban Operasional Pimpinan' }
            };
            if (coaMap[this.activeBp.kode_bagian]) {
                this.activeBp.kode_perkiraan = coaMap[this.activeBp.kode_bagian].coa;
                this.activeBp.kategori = coaMap[this.activeBp.kode_bagian].kat;
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
                            Realisasi BP & Pengeluaran Kas (KO)
                        </h1>
                        <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                            Verifikasi disposisi proposal (DP) dan pencairan bukti pengeluaran kas/bank (BKK/BBK).
                        </p>
                    </div>
                </div>

                @if(session('cetak_bkk'))
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button type="button" @click="showCetakModal = true" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak BKK Terakhir</span>
                        </button>
                    </div>
                @endif
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

            <!-- 3 Kartu KPI -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Siap Dicairkan (Approved)</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($bpList->where('status', 'approved')->sum('nominal_acc'), 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        {{ $bpList->where('status', 'approved')->count() }} BP memiliki nomor register anggaran
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Telah Dicairkan (KO Terbit)</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($bpList->where('status', 'dicairkan_kasir')->sum('nominal_acc'), 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        {{ $bpList->where('status', 'dicairkan_kasir')->count() }} BP selesai direalisasi
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Pengajuan BP</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        {{ $bpList->count() }} <span class="text-xs font-sans font-normal text-slate-400">Permohonan</span>
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        Seluruh divisi internal
                    </span>
                </div>
            </div>

            <!-- Toolbar Pencarian -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <form method="GET" action="{{ route('kasir.pengeluaran.realisasi-bp') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Bukti Pengeluaran</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. BP, No. BKK (KO-...), No. Register (PR-...), pemohon, atau keterangan..."
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer">
                            Cari
                        </button>
                        @if(!empty($search))
                            <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Split Layout: Antrean BP & Form Pencairan -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                
                <!-- SISI KIRI: Antrean BP Anggaran -->
                <div class="lg:col-span-7 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Antrean Bukti Pengeluaran (BP)</h2>
                            <p class="text-xs text-slate-500">Klik baris untuk memuat data ke formulir pencairan</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                            {{ $bpList->count() }} Data
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100 max-h-[700px] overflow-y-auto">
                        @forelse($bpList as $bp)
                        @php
                            $divLower = strtolower($bp->divisi ?? 'umum');
                            $defaultBagian = match($divLower) {
                                'iklan' => '01',
                                'sirkulasi' => '02',
                                'redaksi' => '06',
                                'keuangan' => '05',
                                'akuntansi' => '04',
                                default => '03'
                            };
                            $defaultCoa = match($defaultBagian) {
                                '01' => '6.00.01.01.01.02',
                                '06' => '5.00.01.01.02',
                                default => '6.00.01.00.01.04'
                            };
                            $isApproved = $bp->status === 'approved';
                            $isBedaBulan = $bp->tanggal_pengajuan && $bp->tanggal_pengajuan->format('Y-m') !== now()->format('Y-m');
                        @endphp
                        <div class="p-4 hover:bg-slate-50/80 transition cursor-pointer"
                             :class="activeBp.id === {{ $bp->id }} ? 'bg-kp-blue-50/50 border-l-4 border-l-kp-blue-600' : ''"
                             @click="activeBp = { 
                                 id: {{ $bp->id }},
                                 no_bp: '{{ $bp->no_bp }}', 
                                 no_register: '{{ $bp->no_register_anggaran ?? 'PR-' . date('Ymd') . '-AUTO' }}',
                                 no_dp: '{{ $bp->no_dp ?? ('DP-' . $bp->no_bp) }}',
                                 no_pr: '{{ $bp->no_pr ?? ($bp->no_register_anggaran ?? '') }}',
                                 no_po: '{{ $bp->no_po ?? '' }}',
                                 no_pum: '{{ $bp->no_pum ?? '' }}',
                                 no_cek: '',
                                 pemohon: '{{ addslashes($bp->pemohon) }}', 
                                 divisi: '{{ $bp->divisi }}',
                                 kode_bagian: '{{ $defaultBagian }}',
                                 kode_perkiraan: '{{ $defaultCoa }}',
                                 keperluan: '{{ addslashes($bp->keperluan) }}', 
                                 nominal: {{ $bp->nominal_acc }}, 
                                 penerima: '{{ addslashes($bp->pemohon) }}',
                                 kategori: '{{ $defaultBagian === '06' ? 'redaksi' : ($defaultBagian === '02' ? 'bbm_armada' : ($defaultBagian === '01' ? 'komisi_kh' : 'umum')) }}',
                                 metode: 'kas_fisik',
                                 is_beda_bulan: {{ $isBedaBulan ? 'true' : 'false' }},
                                 bulan_pengajuan: '{{ $bp->tanggal_pengajuan ? $bp->tanggal_pengajuan->translatedFormat('F Y') : now()->translatedFormat('F Y') }}',
                                 catatan: 'Pencairan BP {{ $bp->no_bp }}'
                             }; updateCoa()">
                            
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="font-mono font-bold text-kp-blue-700 text-sm">{{ $bp->no_bp }}</span>
                                        <span class="px-2 py-0.5 text-[11px] font-mono font-semibold rounded bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $bp->no_register_anggaran ?? 'PR-' . date('Ymd') . '-AUTO' }}
                                        </span>
                                        @if($isBedaBulan && $bp->status === 'approved')
                                            <span class="px-2 py-0.5 text-[11px] font-medium rounded bg-slate-100 text-slate-700 border border-slate-300">
                                                Beda Bulan ({{ $bp->tanggal_pengajuan->translatedFormat('M Y') }})
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm font-semibold text-slate-900 mt-1">
                                        {{ $bp->pemohon }} <span class="text-slate-500 font-normal">&bull; Divisi {{ $bp->divisi }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">
                                        {{ $bp->keperluan }}
                                    </p>
                                    @if(!empty($bp->catatan_penolakan))
                                        <p class="text-xs text-slate-700 font-medium mt-1.5 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                                            Catatan Penolakan: {{ $bp->catatan_penolakan }}
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-sm font-mono font-bold text-slate-900 tabular-nums">
                                        Rp {{ number_format($bp->nominal_acc, 0, ',', '.') }}
                                    </div>
                                    <div class="mt-1">
                                        @if($bp->status === 'approved')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                Siap Cair
                                            </span>
                                        @elseif($bp->status === 'dicairkan_kasir')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ ($bp->kasKeluar->jenis_dokumen ?? 'BKK') }} Selesai
                                            </span>
                                        @elseif($bp->status === 'rejected')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-400 border border-slate-200">
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-slate-100 text-xs text-slate-500">
                                <span class="font-mono">{{ $bp->tanggal_pengajuan ? $bp->tanggal_pengajuan->format('d/m/Y') : date('d/m/Y') }}</span>
                                <div class="flex items-center gap-2">
                                    @if($bp->status === 'approved')
                                        <span class="text-kp-blue-700 font-semibold hover:underline">
                                            Muat ke Form &rarr;
                                        </span>
                                    @elseif($bp->status === 'dicairkan_kasir' && $bp->kasKeluar)
                                        <button type="button" 
                                                @click.stop="bkkPrintData = {
                                                    no_bkk: '{{ $bp->kasKeluar->no_bkk }}',
                                                    jenis_dokumen: '{{ $bp->kasKeluar->jenis_dokumen ?? 'BKK' }}',
                                                    tanggal: '{{ $bp->dicairkan_at ? $bp->dicairkan_at->translatedFormat('d F Y, H:i') : date('d F Y, H:i') }}',
                                                    penerima: '{{ addslashes($bp->pemohon) }}',
                                                    no_bp: '{{ $bp->no_bp }}',
                                                    no_dp: '{{ $bp->kasKeluar->no_dp ?? $bp->no_dp ?? '-' }}',
                                                    no_pr: '{{ $bp->kasKeluar->no_pr ?? $bp->no_pr ?? '-' }}',
                                                    no_po: '{{ $bp->kasKeluar->no_po ?? $bp->no_po ?? '-' }}',
                                                    no_pum: '{{ $bp->kasKeluar->no_pum ?? $bp->no_pum ?? '-' }}',
                                                    no_cek: '{{ $bp->kasKeluar->no_cek ?? '-' }}',
                                                    divisi: '{{ $bp->divisi }}',
                                                    no_register: '{{ $bp->no_register_anggaran ?? 'PR-REGISTERED' }}',
                                                    kode_perkiraan: '{{ $bp->kasKeluar->kode_perkiraan ?? $defaultCoa }}',
                                                    keperluan: '{{ addslashes($bp->keperluan) }}',
                                                    metode: '{{ ($bp->kasKeluar->jenis_dokumen ?? 'BKK') === 'BBK' ? 'Pengeluaran Bank (Cek/BG)' : 'Kas Fisik Kasir (Tunai)' }}',
                                                    nominal: {{ (float) $bp->nominal_acc }},
                                                    terbilang: '{{ \App\Http\Controllers\KasirController::terbilang((float) $bp->nominal_acc) }}',
                                                    approver: '{{ addslashes($bp->approver->name ?? 'Manajer & Direksi') }}',
                                                    kasir: '{{ addslashes($bp->disburser->name ?? Auth::user()->name ?? 'Kasir') }}'
                                                }; showCetakModal = true;"
                                                class="text-kp-blue-700 hover:underline font-semibold flex items-center gap-1 cursor-pointer">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            <span>Cetak {{ $bp->kasKeluar->jenis_dokumen ?? 'KO' }} ({{ $bp->kasKeluar->no_bkk }})</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="py-12 text-center text-slate-500 text-sm">
                            Tidak ada bukti pengeluaran yang ditemukan.
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- SISI KANAN: Form Pencairan Kas Keluar (BKK / BBK) -->
                <div class="lg:col-span-5 sticky top-4 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Verifikasi DP & Pencairan</h2>
                            <p class="text-xs text-slate-500">Terbitkan BKK (Tunai) atau BBK (Bank)</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono"
                              x-text="activeBp.metode === 'kas_fisik' ? 'BKK (TUNAI)' : 'BBK (BANK)'">
                        </span>
                    </div>

                    <template x-if="!activeBp.id">
                        <div class="p-8 text-center text-slate-500 space-y-1">
                            <p class="text-sm font-semibold text-slate-700">Pilih antrean BP di sisi kiri</p>
                            <p class="text-xs text-slate-400">Nomor register dan peruntukan akan dimuat otomatis.</p>
                        </div>
                    </template>

                    <template x-if="activeBp.id">
                        <form method="POST" action="{{ route('kasir.pengeluaran.realisasi-bp.store') }}" class="p-4 sm:p-5 space-y-3.5 text-xs">
                            @csrf
                            <input type="hidden" name="bp_id" :value="activeBp.id">
                            <input type="hidden" name="nominal_cair" :value="activeBp.nominal">
                            <input type="hidden" name="jenis_dokumen" :value="activeBp.metode === 'kas_fisik' ? 'BKK' : 'BBK'">

                            <!-- Peringatan Validasi Lintas Bulan -->
                            <div x-show="activeBp.is_beda_bulan" class="p-3 bg-slate-100 border border-slate-300 rounded-xl text-slate-800 space-y-1">
                                <div class="font-bold text-slate-900">Peringatan Lintas Bulan</div>
                                <p>Dokumen BP/DP diterbitkan pada <b x-text="activeBp.bulan_pengajuan"></b>. Pencairan lintas bulan wajib dikembalikan untuk pengajuan DP bulan berjalan.</p>
                            </div>

                            <!-- Ringkasan BP Terpilih -->
                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">No. Bukti BP:</span>
                                    <span class="font-mono font-bold text-kp-blue-700" x-text="activeBp.no_bp"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">No. Register Anggaran:</span>
                                    <span class="font-mono font-bold text-slate-900" x-text="activeBp.no_register"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Pemohon & Divisi:</span>
                                    <span class="font-semibold text-slate-900" x-text="activeBp.pemohon + ' (' + activeBp.divisi + ')'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Keperluan:</span>
                                    <span class="font-medium text-slate-700 text-right truncate max-w-[200px]" x-text="activeBp.keperluan"></span>
                                </div>
                                <div class="flex justify-between pt-1.5 border-t border-slate-200">
                                    <span class="font-bold text-slate-700">Nominal ACC:</span>
                                    <span class="font-mono font-bold text-kp-blue-700 text-sm">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeBp.nominal)"></span></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Divisi / Bagian</label>
                                    <select name="kode_bagian" x-model="activeBp.kode_bagian" @change="updateCoa()" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                        <option value="03">Divisi Umum & SDM</option>
                                        <option value="06">Divisi Redaksi & Wartawan</option>
                                        <option value="01">Divisi Iklan (Komisi/Promosi)</option>
                                        <option value="02">Divisi Sirkulasi (Ekspedisi/BBM)</option>
                                        <option value="04">Divisi Akuntansi</option>
                                        <option value="05">Divisi Keuangan</option>
                                        <option value="07">Divisi Pimpinan</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Perkiraan (COA)</label>
                                    <input type="hidden" name="kode_perkiraan" :value="activeBp.kode_perkiraan">
                                    <input type="text" :value="activeBp.kode_perkiraan" readonly
                                           class="w-full h-10 px-3 bg-slate-100 border border-slate-200 rounded-xl text-xs font-mono font-bold text-kp-blue-700 cursor-not-allowed">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Penerima Dana</label>
                                    <input type="text" name="penerima" x-model="activeBp.penerima" required 
                                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Pencairan</label>
                                    <select name="metode_bayar" x-model="activeBp.metode" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                        <option value="kas_fisik">BKK (Kas Besar Tunai)</option>
                                        <option value="cek">BBK (Cek / Bilyet Giro Bank)</option>
                                        <option value="transfer">BBK (Transfer Rekening Bank)</option>
                                    </select>
                                </div>
                            </div>

                            <div x-show="activeBp.metode === 'cek' || activeBp.metode === 'transfer'" x-transition class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5">
                                <label class="block text-xs font-semibold text-slate-700">Nomor Cek / Bilyet Giro Bank</label>
                                <input type="text" name="no_cek" x-model="activeBp.no_cek" placeholder="CEK-BJB-882910"
                                       class="w-full h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600">
                            </div>

                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                                <span class="text-xs font-semibold text-slate-700 block">Referensi Dokumen Pengadaan</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[11px] font-medium text-slate-600 mb-0.5">No. DP</label>
                                        <input type="text" name="no_dp" x-model="activeBp.no_dp" placeholder="DP-..."
                                               class="w-full h-9 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-medium text-slate-600 mb-0.5">No. PR</label>
                                        <input type="text" name="no_pr" x-model="activeBp.no_pr" placeholder="PR-..."
                                               class="w-full h-9 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-medium text-slate-600 mb-0.5">No. PO</label>
                                        <input type="text" name="no_po" x-model="activeBp.no_po" placeholder="PO-... (Opsional)"
                                               class="w-full h-9 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-medium text-slate-600 mb-0.5">No. P-UM</label>
                                        <input type="text" name="no_pum" x-model="activeBp.no_pum" placeholder="PUM-... (Opsional)"
                                               class="w-full h-9 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Pengeluaran</label>
                                <select name="kategori" x-model="activeBp.kategori" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                    <option value="umum">Umum & Perlengkapan Rumah Tangga / Pengadaan</option>
                                    <option value="bbm_armada">BBM, Tol & Operasional Ekspedisi</option>
                                    <option value="redaksi">Honorarium Redaksi & Liputan Lapangan</option>
                                    <option value="komisi_kh">Komisi Humas (KH)</option>
                                    <option value="insentif_ae">Insentif Sales / AE</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Pengeluaran</label>
                                <input type="text" name="catatan" x-model="activeBp.catatan" 
                                       placeholder="Uraian bukti kas/bank keluar..." 
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600">
                            </div>

                            <div class="pt-1 space-y-2">
                                <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                                    <span x-text="activeBp.metode === 'kas_fisik' ? 'Cairkan Tunai & Terbitkan BKK' : 'Cairkan Bank & Terbitkan BBK'"></span>
                                </button>

                                <button type="button" @click="showTolakModal = true" class="w-full h-10 px-4 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 font-semibold text-xs rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer">
                                    <span>Kembalikan / Tolak DP</span>
                                </button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>

        </div>

        <!-- Modal Kembalikan / Tolak DP -->
        <div x-cloak x-show="showTolakModal && activeBp.id" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="showTolakModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Kembalikan / Tolak DP & BP</h3>
                        <p class="text-xs text-slate-500 font-mono mt-0.5" x-text="activeBp.no_bp + ' • ' + activeBp.pemohon"></p>
                    </div>
                    <button type="button" @click="showTolakModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
                </div>
                <form method="POST" :action="'{{ url('/kasir/pengeluaran/realisasi-bp') }}/' + activeBp.id + '/tolak'" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Penolakan / Pengembalian Dokumen <span class="text-rose-600">*</span></label>
                        <textarea name="catatan_penolakan" rows="3" required placeholder="Alasan pengembalian dokumen ke bagian pengaju..."
                                  class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showTolakModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition cursor-pointer">
                            Konfirmasi Tolak DP
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Cetak Bukti Kas Keluar (BKK / BBK) -->
        <div x-cloak x-show="showCetakModal && bkkPrintData" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-white">
                    <div>
                        <div class="text-base font-bold tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500 font-semibold">DIVISI KASIR - SISTEM OPERASI KAS & BANK</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md uppercase tracking-wider border bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200"
                              x-text="(bkkPrintData?.jenis_dokumen === 'BBK') ? 'BUKTI BANK KELUAR (BBK)' : 'BUKTI KAS KELUAR (BKK / KO)'">
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1" x-text="bkkPrintData?.no_bkk"></div>
                        <div class="text-[10px] text-slate-500" x-text="bkkPrintData?.tanggal"></div>
                    </div>
                </div>

                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Diberikan Kepada</span>
                        <span class="col-span-2 font-bold text-slate-900">: <span x-text="bkkPrintData?.penerima"></span></span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Dasar Pengeluaran / Ref</span>
                        <span class="col-span-2 font-mono font-bold text-slate-900">: <span x-text="bkkPrintData?.no_bp"></span> (<span x-text="bkkPrintData?.divisi"></span>)</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">No. Register / DP</span>
                        <span class="col-span-2 font-mono font-bold text-kp-blue-700">: <span x-text="(bkkPrintData?.no_register ?? 'PR-REGISTERED') + ' / ' + (bkkPrintData?.no_dp ?? '-')"></span></span>
                    </div>
                    <template x-if="(bkkPrintData?.no_pr && bkkPrintData?.no_pr !== '-') || (bkkPrintData?.no_po && bkkPrintData?.no_po !== '-') || (bkkPrintData?.no_pum && bkkPrintData?.no_pum !== '-')">
                        <div class="grid grid-cols-3 py-1 border-b border-slate-100 bg-slate-50 px-2 rounded">
                            <span class="text-slate-500">Ref. Pengadaan</span>
                            <span class="col-span-2 font-mono text-[11px] text-slate-800">: PR: <span x-text="bkkPrintData?.no_pr || '-'"></span> &bull; PO: <span x-text="bkkPrintData?.no_po || '-'"></span> &bull; P-UM: <span x-text="bkkPrintData?.no_pum || '-'"></span></span>
                        </div>
                    </template>
                    <template x-if="bkkPrintData?.no_cek && bkkPrintData?.no_cek !== '-'">
                        <div class="grid grid-cols-3 py-1 border-b border-slate-100 bg-slate-50 px-2 rounded">
                            <span class="text-slate-700 font-semibold">No. Cek / Bilyet Giro</span>
                            <span class="col-span-2 font-mono font-bold text-slate-900">: <span x-text="bkkPrintData?.no_cek"></span></span>
                        </div>
                    </template>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Bagan Akun (COA)</span>
                        <span class="col-span-2 font-mono text-slate-700">: <span x-text="'Divisi ' + (bkkPrintData?.divisi || 'Umum') + ' - ' + (bkkPrintData?.kode_perkiraan || '6.00.01.00.01.04')"></span></span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Untuk Keperluan</span>
                        <span class="col-span-2 text-slate-800">: <span x-text="bkkPrintData?.keperluan"></span></span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Metode Pengeluaran</span>
                        <span class="col-span-2 font-semibold text-slate-800">: <span x-text="bkkPrintData?.metode"></span></span>
                    </div>
                    <div class="grid grid-cols-3 py-2 bg-slate-50 px-3 rounded-xl border border-slate-200 my-2">
                        <span class="font-bold text-slate-900">Jumlah Keluar</span>
                        <span class="col-span-2 font-mono font-bold text-base text-kp-blue-700">: Rp <span x-text="new Intl.NumberFormat('id-ID').format(bkkPrintData?.nominal || 0)"></span></span>
                    </div>
                    <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-[10px] text-slate-500 block uppercase font-bold">Terbilang:</span>
                        <span class="text-xs font-semibold italic text-slate-800" x-text="bkkPrintData?.terbilang"></span>
                    </div>

                    <div class="grid grid-cols-4 pt-4 border-t border-slate-200 text-center text-[10px] text-slate-600 gap-1">
                        <div>
                            <div>Direksi</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">(Direktur)</div>
                        </div>
                        <div>
                            <div>Manajer Keu</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">(<span x-text="bkkPrintData?.approver || 'Manajer'"></span>)</div>
                        </div>
                        <div>
                            <div>Kasir Pembayar</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900 font-mono">(<span x-text="bkkPrintData?.kasir || 'Kasir'"></span>)</div>
                        </div>
                        <div>
                            <div>Penerima</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">(<span x-text="bkkPrintData?.penerima"></span>)</div>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
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

    </div>
</x-app-layout>
