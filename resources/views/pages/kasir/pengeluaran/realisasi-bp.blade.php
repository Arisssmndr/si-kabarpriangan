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

    <div class="space-y-6" x-data="{ 
        showCairModal: false, 
        showCetakModal: {{ session('cetak_bkk') ? 'true' : 'false' }},
        bkkPrintData: {{ session('cetak_bkk') ? json_encode(session('cetak_bkk')) : 'null' }},
        activeBp: { 
            id: {{ $firstApproved ? $firstApproved->id : 'null' }},
            no_bp: '{{ $firstApproved ? $firstApproved->no_bp : '' }}', 
            no_register: '{{ $firstApproved ? ($firstApproved->no_register_anggaran ?? 'PR-' . date('Ymd') . '-AUTO') : '' }}',
            pemohon: '{{ $firstApproved ? addslashes($firstApproved->pemohon) : '' }}', 
            divisi: '{{ $firstApproved ? $firstApproved->divisi : '' }}',
            kode_bagian: '{{ $firstBagian }}',
            kode_perkiraan: '{{ $firstCoa }}',
            keperluan: '{{ $firstApproved ? addslashes($firstApproved->keperluan) : '' }}', 
            nominal: {{ $firstApproved ? (float)$firstApproved->nominal_acc : 0 }}, 
            penerima: '{{ $firstApproved ? addslashes($firstApproved->pemohon) : '' }}',
            kategori: '{{ $firstBagian === '06' ? 'redaksi' : ($firstBagian === '02' ? 'bbm_armada' : ($firstBagian === '01' ? 'komisi_kh' : 'umum')) }}',
            metode: 'kas_fisik',
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
        <div class="space-y-6" :class="{ 'print:hidden': showCetakModal }">

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-semibold text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs font-semibold text-rose-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Header Page Realisasi BP & Kas Keluar -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <!-- Breadcrumb minimalis dengan indikator biru aktif -->
                    <div class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-1">
                        <span>Kasir</span>
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span>Pengeluaran</span>
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-blue-600 font-semibold flex items-center gap-1.5 bg-blue-50/80 px-2 py-0.5 rounded-md border border-blue-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                            Realisasi BP
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Realisasi BP & Pengeluaran Kas (KO)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-rose-50 text-rose-700 border border-rose-200">
                            Kas Keluar (KO)
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                            KO-YYYYMMXXXX
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pencairan kas pengeluaran operasional (Sistem KAS) hanya jika Nomor Register Anggaran (PR-YYYYMMDD-XXXX) telah berstatus ACC Keuangan
                    </p>
                </div>
            </div>

            @if(session('cetak_bkk'))
                <button type="button" @click="showCetakModal = true" class="px-3.5 py-2 bg-blue-50 hover:bg-blue-100 active:scale-95 text-blue-700 font-bold text-xs rounded-xl border border-blue-200 transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Bukti Kas Keluar (KO Terakhir)</span>
                </button>
            @endif
        </div>

        <!-- Metric KPI Cards (OripioFin Hover Lift) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100 hover:-translate-y-1 hover:shadow-md transition-all duration-200 group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Pengajuan BP Masuk</span>
                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-slate-900 tracking-tight">
                    {{ $bpList->count() }} Permohonan BP
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Seluruh divisi internal</div>
            </div>

            <div class="bg-white border border-rose-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100 hover:-translate-y-1 hover:shadow-md transition-all duration-200 group bg-rose-50/10">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-rose-700 uppercase tracking-wider">Siap Dicairkan Kasir (Approved)</span>
                    <span class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-extrabold text-rose-600 tracking-tight">
                    Rp {{ number_format($bpList->where('status', 'approved')->sum('nominal_acc'), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-rose-600/80">{{ $bpList->where('status', 'approved')->count() }} BP dengan No Register Anggaran</div>
            </div>

            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100 hover:-translate-y-1 hover:shadow-md transition-all duration-200 group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Telah Dicairkan Kasir (KO Terbit)</span>
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-emerald-600 tracking-tight">
                    Rp {{ number_format($bpList->where('status', 'dicairkan_kasir')->sum('nominal_acc'), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">{{ $bpList->where('status', 'dicairkan_kasir')->count() }} BP selesai direalisasi</div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm shadow-slate-100">
            <form method="GET" action="{{ route('kasir.pengeluaran.realisasi-bp') }}" class="flex items-center gap-3">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. BP, No. BKK (KO-...), No. Register Anggaran (PR-...), pemohon, atau keterangan..."
                           class="w-full text-xs py-2.5 pl-9 pr-3 rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs rounded-xl transition shadow-xs shadow-blue-500/20 cursor-pointer">
                    Cari Kas Keluar
                </button>
            </form>
        </div>

        <!-- SPLIT LAYOUT: Sisi Kiri Antrean BP ACC (PR-...) & Sisi Kanan Form Pencairan Kas Keluar (KO) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            
            <!-- SISI KIRI: Antrean BP Anggaran Berstatus ACC Pimpinan (PR-...) -->
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm shadow-slate-100 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                                Antrean BP Anggaran ACC Pimpinan
                            </h3>
                        </div>
                        <span class="text-[11px] text-slate-500 font-medium">Klik untuk memuat ke formulir pencairan</span>
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
                        @endphp
                        <div class="p-4 hover:bg-slate-50/70 transition-all duration-150 cursor-pointer"
                             :class="activeBp.id === {{ $bp->id }} ? 'bg-blue-50/50 border-l-4 border-l-blue-600 shadow-2xs' : ''"
                             @click="activeBp = { 
                                 id: {{ $bp->id }},
                                 no_bp: '{{ $bp->no_bp }}', 
                                 no_register: '{{ $bp->no_register_anggaran ?? 'PR-' . date('Ymd') . '-AUTO' }}',
                                 pemohon: '{{ addslashes($bp->pemohon) }}', 
                                 divisi: '{{ $bp->divisi }}',
                                 kode_bagian: '{{ $defaultBagian }}',
                                 kode_perkiraan: '{{ $defaultCoa }}',
                                 keperluan: '{{ addslashes($bp->keperluan) }}', 
                                 nominal: {{ $bp->nominal_acc }}, 
                                 penerima: '{{ addslashes($bp->pemohon) }}',
                                 kategori: '{{ $defaultBagian === '06' ? 'redaksi' : ($defaultBagian === '02' ? 'bbm_armada' : ($defaultBagian === '01' ? 'komisi_kh' : 'umum')) }}',
                                 metode: 'kas_fisik',
                                 catatan: 'Pencairan BP {{ $bp->no_bp }}'
                             }; updateCoa()">
                            
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-slate-900 text-xs">{{ $bp->no_bp }}</span>
                                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded bg-amber-50 text-amber-800 border border-amber-200">
                                            {{ $bp->no_register_anggaran ?? 'PR-' . date('Ymd') . '-AUTO' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-slate-700 font-semibold mt-1">
                                        {{ $bp->pemohon }} • <span class="text-slate-500 font-medium">Divisi {{ $bp->divisi }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">
                                        {{ $bp->keperluan }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-sm font-extrabold text-slate-900">
                                        Rp {{ number_format($bp->nominal_acc, 0, ',', '.') }}
                                    </div>
                                    <div class="mt-1">
                                        @if($bp->status === 'approved')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Siap Cair
                                            </span>
                                        @elseif($bp->status === 'dicairkan_kasir')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                                KO Selesai
                                            </span>
                                        @elseif($bp->status === 'rejected')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                                Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-400">
                                <span>{{ $bp->tanggal_pengajuan ? $bp->tanggal_pengajuan->translatedFormat('d M Y') : date('d/m/Y') }}</span>
                                <div class="flex items-center gap-2">
                                    @if($bp->status === 'approved')
                                        <span class="text-blue-600 font-bold hover:underline flex items-center gap-1">
                                            <span>Muat ke Form</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </span>
                                    @elseif($bp->status === 'dicairkan_kasir' && $bp->kasKeluar)
                                        <button type="button" 
                                                @click.stop="bkkPrintData = {
                                                    no_bkk: '{{ $bp->kasKeluar->no_bkk }}',
                                                    tanggal: '{{ $bp->dicairkan_at ? $bp->dicairkan_at->translatedFormat('d F Y, H:i') : date('d F Y, H:i') }}',
                                                    penerima: '{{ addslashes($bp->pemohon) }}',
                                                    no_bp: '{{ $bp->no_bp }}',
                                                    divisi: '{{ $bp->divisi }}',
                                                    no_register: '{{ $bp->no_register_anggaran ?? 'PR-REGISTERED' }}',
                                                    kode_perkiraan: '{{ $bp->kasKeluar->kode_perkiraan ?? $defaultCoa }}',
                                                    keperluan: '{{ addslashes($bp->keperluan) }}',
                                                    metode: 'Kas Fisik Kasir',
                                                    nominal: {{ (float) $bp->nominal_acc }},
                                                    terbilang: '{{ \App\Http\Controllers\KasirController::terbilang((float) $bp->nominal_acc) }}',
                                                    approver: '{{ addslashes($bp->approver->name ?? 'Pimpinan / ACC Keuangan') }}',
                                                    kasir: '{{ addslashes($bp->disburser->name ?? Auth::user()->name ?? 'Kasir') }}'
                                                }; showCetakModal = true;"
                                                class="text-blue-600 hover:text-blue-800 font-bold hover:underline flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            <span>Cetak KO ({{ $bp->kasKeluar->no_bkk }})</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="p-8 text-center text-slate-400">
                            <p class="text-xs font-semibold">Tidak ada bukti pengeluaran yang ditemukan.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- SISI KANAN: Form Pencairan Kas Keluar (KO) OripioFin Style -->
            <div class="lg:col-span-5 sticky top-6 space-y-4">
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100">
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Form Pencairan Kas Keluar</h3>
                                <p class="text-[11px] text-slate-500">Penerbitan Bukti Kas Keluar (KO)</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-rose-50 text-rose-700 border border-rose-200">
                            Cash Out
                        </span>
                    </div>

                    <template x-if="!activeBp.id">
                        <div class="py-12 text-center text-slate-400 space-y-2">
                            <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-xs font-semibold text-slate-600">Pilih salah satu antrean BP berstatus ACC di sisi kiri</p>
                            <p class="text-[11px] text-slate-400">Data nomor register dan peruntukan akan otomatis terisi di sini.</p>
                        </div>
                    </template>

                    <template x-if="activeBp.id">
                        <form method="POST" action="{{ route('kasir.pengeluaran.realisasi-bp.store') }}" class="mt-4 space-y-3.5 text-xs">
                            @csrf
                            <input type="hidden" name="bp_id" :value="activeBp.id">
                            <input type="hidden" name="nominal_cair" :value="activeBp.nominal">

                            <!-- Ringkasan BP Terpilih -->
                            <div class="bg-slate-50/80 p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">No. Bukti BP:</span>
                                    <span class="font-mono font-bold text-slate-900" x-text="activeBp.no_bp"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">No. Register Anggaran:</span>
                                    <span class="font-mono font-bold text-blue-700" x-text="activeBp.no_register"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Pemohon & Divisi:</span>
                                    <span class="font-bold text-slate-800" x-text="activeBp.pemohon + ' (' + activeBp.divisi + ')'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Keperluan:</span>
                                    <span class="font-medium text-slate-700 text-right truncate max-w-[200px]" x-text="activeBp.keperluan"></span>
                                </div>
                                <div class="flex justify-between pt-1.5 border-t border-slate-200">
                                    <span class="font-bold text-slate-700">Nominal ACC:</span>
                                    <span class="font-mono font-black text-rose-600 text-sm">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeBp.nominal)"></span></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <!-- Divisi / Bagian -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Divisi / Bagian</label>
                                    <select name="kode_bagian" x-model="activeBp.kode_bagian" @change="updateCoa()" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 font-semibold bg-white">
                                        <option value="03">Divisi Umum & SDM</option>
                                        <option value="06">Divisi Redaksi & Wartawan</option>
                                        <option value="01">Divisi Iklan (Komisi/Promosi)</option>
                                        <option value="02">Divisi Sirkulasi (Ekspedisi/BBM)</option>
                                        <option value="04">Divisi Akuntansi</option>
                                        <option value="05">Divisi Keuangan</option>
                                        <option value="07">Divisi Pimpinan</option>
                                    </select>
                                </div>

                                <!-- Kode Perkiraan (COA) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Kode Perkiraan (COA)</label>
                                    <select name="kode_perkiraan" x-model="activeBp.kode_perkiraan" class="w-full text-xs py-2 px-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 font-mono text-[11px] bg-white">
                                        <option value="6.00.01.00.01.04">6.00.01.00.01.04 - Umum/BBM</option>
                                        <option value="5.00.01.01.02">5.00.01.01.02 - Liputan Redaksi</option>
                                        <option value="6.00.01.01.01.02">6.00.01.01.01.02 - Komisi/Insentif</option>
                                        <option value="6.00.01.01.01.03">6.00.01.01.01.03 - Administrasi</option>
                                        <option value="6.00.01.01.00.00">6.00.01.01.00.00 - Pimpinan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <!-- Penerima Dana -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Diberikan Kepada (Penerima)</label>
                                    <input type="text" name="penerima" x-model="activeBp.penerima" required 
                                           class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-900 font-semibold bg-white">
                                </div>

                                <!-- Metode Kas Keluar -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Metode Pencairan</label>
                                    <select name="metode_bayar" x-model="activeBp.metode" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 bg-white">
                                        <option value="kas_fisik">Uang Tunai (Kas Fisik Laci)</option>
                                        <option value="transfer">Transfer Rekening Bank BJB</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Kategori Pengeluaran</label>
                                <select name="kategori" x-model="activeBp.kategori" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 bg-white">
                                    <option value="umum">Umum & Perlengkapan Rumah Tangga</option>
                                    <option value="bbm_armada">BBM, Tol & Operasional Ekspedisi</option>
                                    <option value="redaksi">Honorarium Redaksi & Liputan Lapangan</option>
                                    <option value="komisi_kh">Komisi Humas (KH)</option>
                                    <option value="insentif_ae">Insentif Sales / AE</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Keterangan Pengeluaran (BKK)</label>
                                <input type="text" name="catatan" x-model="activeBp.catatan" 
                                       placeholder="Uraian bukti kas keluar..." 
                                       class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-900 bg-white">
                            </div>

                            <button type="submit" class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold text-xs rounded-xl transition shadow-xs shadow-rose-500/20 flex items-center justify-center gap-2 cursor-pointer mt-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Cairkan Kas Keluar & Terbitkan KO</span>
                            </button>
                        </form>
                    </template>
                </div>
            </div>

        </div>
        </div>

        <!-- Modal Cetak Bukti Kas Keluar (BKK / KO) Minimalis Elegan -->
        <div x-cloak x-show="showCetakModal && bkkPrintData" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200/80 overflow-hidden">
                <!-- Header Dokumen BKK -->
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500 font-semibold">DIVISI KASIR - SISTEM OPERASI KAS</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded-md bg-rose-50 text-rose-700 uppercase tracking-widest border border-rose-200">
                            BUKTI KAS KELUAR (KO)
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1" x-text="bkkPrintData?.no_bkk"></div>
                        <div class="text-[10px] text-slate-500" x-text="bkkPrintData?.tanggal"></div>
                    </div>
                </div>

                <!-- Body Dokumen (Scrollable) -->
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
                        <span class="text-slate-500">No. Register Anggaran</span>
                        <span class="col-span-2 font-mono font-bold text-blue-700">: <span x-text="bkkPrintData?.no_register ?? 'PR-REGISTERED'"></span></span>
                    </div>
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
                    <div class="grid grid-cols-3 py-2 bg-rose-50 px-3 rounded-xl border border-rose-200/80 my-2">
                        <span class="font-bold text-rose-900">Jumlah Kas Keluar</span>
                        <span class="col-span-2 font-mono font-black text-base text-rose-800">: Rp <span x-text="new Intl.NumberFormat('id-ID').format(bkkPrintData?.nominal || 0)"></span></span>
                    </div>
                    <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-[10px] text-slate-500 block uppercase font-bold">Terbilang:</span>
                        <span class="text-xs font-semibold italic text-slate-800" x-text="bkkPrintData?.terbilang"></span>
                    </div>

                    <!-- Tanda Tangan Cetak -->
                    <div class="grid grid-cols-3 pt-4 border-t border-slate-200 text-center text-[10px] text-slate-600">
                        <div>
                            <div>Yang Menerima</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">(<span x-text="bkkPrintData?.penerima"></span>)</div>
                        </div>
                        <div>
                            <div>Mengetahui / ACC</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">(<span x-text="bkkPrintData?.approver || 'Pimpinan / Keuangan'"></span>)</div>
                        </div>
                        <div>
                            <div>Kasir Pembayar</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900 font-mono">(<span x-text="bkkPrintData?.kasir || 'Kasir Kabar Priangan'"></span>)</div>
                        </div>
                    </div>
                </div>

                <!-- Action Button Print & Close (Pinned Bottom) -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer active:scale-95">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold text-xs rounded-xl transition shadow-xs shadow-rose-500/20 flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
