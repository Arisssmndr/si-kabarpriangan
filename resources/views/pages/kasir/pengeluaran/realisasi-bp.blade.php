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

    <div class="space-y-6" 
         @open-cetak-modal.window="showCetakModal = true"
         x-data="{ 
        showCairModal: false, 
        showTolakModal: false,
        showCetakModal: false,
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
        <div class="space-y-6" :class="{ 'print:hidden': showCetakModal }">

            <!-- Flash Message Alert -->
            @if(session('success'))
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-700 text-sm font-bold">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-rose-700 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-700 text-sm font-bold">&times;</button>
                </div>
            @endif

            <!-- 1. Header Standar Divisi Iklan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Logo Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                                Realisasi BP & Pengeluaran Kas (KO)
                            </h1>
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-sky-50 text-[#0E527B] border border-sky-200">
                                Kas Keluar (KO)
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Pencairan kas pengeluaran operasional (Sistem KAS) hanya jika Nomor Register Anggaran (PR-YYYYMMDD-XXXX) telah berstatus ACC Keuangan.
                        </p>
                    </div>
                </div>

                @if(session('cetak_bkk'))
                    <button type="button" @click="showCetakModal = true" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl transition shadow-xs flex items-center gap-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti Kas Keluar (KO)</span>
                    </button>
                @endif
            </div>

            <!-- 2. KPI Cards Standar Divisi Iklan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Pengajuan BP Masuk</span>
                    <p class="mt-2 text-2xl font-bold font-mono text-slate-900">
                        {{ $bpList->count() }} <span class="text-xs font-normal text-slate-400">Permohonan</span>
                    </p>
                    <span class="mt-1 text-[11px] text-slate-400 block">Seluruh divisi internal</span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Siap Dicairkan Kasir (Approved)</span>
                    <p class="mt-2 text-2xl font-bold font-mono text-kp-blue-700">
                        Rp {{ number_format($bpList->where('status', 'approved')->sum('nominal_acc'), 0, ',', '.') }}
                    </p>
                    <span class="mt-1 text-[11px] text-slate-400 block">{{ $bpList->where('status', 'approved')->count() }} BP dengan No Register Anggaran</span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Telah Dicairkan Kasir (KO Terbit)</span>
                    <p class="mt-2 text-2xl font-bold font-mono text-slate-900">
                        Rp {{ number_format($bpList->where('status', 'dicairkan_kasir')->sum('nominal_acc'), 0, ',', '.') }}
                    </p>
                    <span class="mt-1 text-[11px] text-slate-400 block">{{ $bpList->where('status', 'dicairkan_kasir')->count() }} BP selesai direalisasi</span>
                </div>
            </div>

            <!-- 3. Filter Bar Terpisah -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs" x-data="{ periode: '{{ $periodeTipe ?? 'semua' }}' }">
                <form method="GET" action="{{ route('kasir.pengeluaran.realisasi-bp') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-4">
                        <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Cari Dokumen BP</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="No. BP, No. BKK, No. Register (PR-...), pemohon..."
                               class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Filter Divisi</label>
                        <select name="divisi" onchange="this.form.submit()" class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600">
                            <option value="semua" {{ ($divisi ?? 'semua') === 'semua' ? 'selected' : '' }}>Semua Divisi</option>
                            @foreach(($divisiList ?? [
                                'iklan' => 'Divisi Iklan',
                                'sirkulasi' => 'Divisi Sirkulasi',
                                'redaksi' => 'Divisi Redaksi',
                                'keuangan' => 'Bidang Keuangan',
                                'akuntansi' => 'Divisi Akuntansi',
                                'umum' => 'Divisi Umum & SDM',
                                'pimpinan' => 'Pimpinan / Direksi',
                            ]) as $divKey => $divLabel)
                                <option value="{{ $divKey }}" {{ strtolower($divisi ?? '') === $divKey ? 'selected' : '' }}>{{ $divLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Periode Waktu</label>
                        <select name="periode_tipe" x-model="periode" class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600">
                            <option value="semua">Semua Waktu</option>
                            <option value="hari">Per Hari (Tanggal)</option>
                            <option value="bulan">Per Bulan</option>
                            <option value="tahun">Per Tahun</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button type="submit" class="h-11 w-full bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Cari</span>
                        </button>
                        @if(!empty($search) || (($divisi ?? 'semua') !== 'semua') || ($periodeTipe ?? 'semua') !== 'semua')
                            <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" title="Reset Filter" class="h-11 px-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- 4. Split Layout: Antrean BP & Form Pencairan Kas Keluar -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Sisi Kiri: Antrean BP Anggaran (PR-...) -->
                <div class="lg:col-span-7 space-y-4">
                    <!-- Tab Filter Cepat Divisi -->
                    <div class="bg-white border border-slate-200 rounded-xl p-2 shadow-xs flex items-center gap-1.5 overflow-x-auto">
                        <a href="{{ route('kasir.pengeluaran.realisasi-bp', array_merge(request()->except('divisi'), ['divisi' => 'semua'])) }}"
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition {{ ($divisi ?? 'semua') === 'semua' ? 'bg-kp-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
                            Semua
                        </a>
                        @foreach(($divisiList ?? [
                            'iklan' => 'Iklan',
                            'sirkulasi' => 'Sirkulasi',
                            'redaksi' => 'Redaksi',
                            'keuangan' => 'Keuangan',
                            'akuntansi' => 'Akuntansi',
                            'umum' => 'Umum',
                        ]) as $divKey => $divLabel)
                            <a href="{{ route('kasir.pengeluaran.realisasi-bp', array_merge(request()->except('divisi'), ['divisi' => $divKey])) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition {{ strtolower($divisi ?? '') === $divKey ? 'bg-kp-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
                                {{ $divLabel }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Pure List BP (Tanpa Banner Strip) -->
                    <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden divide-y divide-slate-100 max-h-[700px] overflow-y-auto">
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
                             :class="activeBp.id === {{ $bp->id }} ? 'bg-sky-50/50 border-l-4 border-l-kp-blue-600' : ''"
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
                                <div>
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="font-mono font-bold text-slate-900 text-sm">{{ $bp->no_bp }}</span>
                                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $bp->no_register_anggaran ?? 'PR-' . date('Ymd') . '-AUTO' }}
                                        </span>
                                        @if($isBedaBulan && $bp->status === 'approved')
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-rose-50 text-rose-700 border border-rose-200">
                                                Beda Bulan ({{ $bp->tanggal_pengajuan->translatedFormat('M Y') }})
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-slate-800 font-semibold mt-1">
                                        {{ $bp->pemohon }} • <span class="text-slate-500 font-normal">Divisi {{ $bp->divisi }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">
                                        {{ $bp->keperluan }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-sm font-bold font-mono text-slate-900">
                                        Rp {{ number_format($bp->nominal_acc, 0, ',', '.') }}
                                    </div>
                                    <div class="mt-1">
                                        @if($bp->status === 'approved')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-sky-50 text-[#0E527B] border border-sky-200">
                                                Siap Cair
                                            </span>
                                        @elseif($bp->status === 'dicairkan_kasir')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                                Selesai
                                            </span>
                                        @elseif($bp->status === 'rejected')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                                Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-3 pt-2 border-t border-slate-100 text-xs text-slate-400">
                                <span>{{ $bp->tanggal_pengajuan ? $bp->tanggal_pengajuan->translatedFormat('d M Y') : date('d/m/Y') }}</span>
                                <div class="flex items-center gap-2">
                                    @if($bp->status === 'approved')
                                        <span class="text-kp-blue-700 font-semibold hover:underline flex items-center gap-1 text-xs">
                                            <span>Muat ke Form</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
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
                                                class="text-kp-blue-700 hover:text-kp-blue-800 font-semibold hover:underline flex items-center gap-1 text-xs cursor-pointer">
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

                <!-- Sisi Kanan: Form Pencairan Kas Keluar (BKK / BBK) -->
                <div class="lg:col-span-5 sticky top-6 space-y-4">
                    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Pencairan Kas/Bank Keluar
                            </h2>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-sky-50 text-[#0E527B] border border-sky-200"
                                  x-text="activeBp.metode === 'kas_fisik' ? 'BKK (TUNAI)' : 'BBK (CEK BANK)'">
                            </span>
                        </div>

                        <template x-if="!activeBp.id">
                            <div class="py-12 text-center text-slate-400 space-y-2">
                                <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-xs font-semibold text-slate-600">Pilih salah satu antrean BP di sisi kiri</p>
                                <p class="text-[11px] text-slate-400">Data nomor register dan peruntukan akan otomatis terisi di sini.</p>
                            </div>
                        </template>

                        <template x-if="activeBp.id">
                            <form method="POST" action="{{ route('kasir.pengeluaran.realisasi-bp.store') }}" class="mt-4 space-y-4 text-xs">
                                @csrf
                                <input type="hidden" name="bp_id" :value="activeBp.id">
                                <input type="hidden" name="nominal_cair" :value="activeBp.nominal">
                                <input type="hidden" name="jenis_dokumen" :value="activeBp.metode === 'kas_fisik' ? 'BKK' : 'BBK'">
                                <input type="hidden" name="no_dp" :value="activeBp.no_dp">
                                <input type="hidden" name="no_pr" :value="activeBp.no_pr">
                                <input type="hidden" name="no_po" :value="activeBp.no_po">
                                <input type="hidden" name="no_pum" :value="activeBp.no_pum">

                                <!-- Ringkasan BP Terpilih -->
                                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-1.5">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">No. Bukti BP:</span>
                                        <span class="font-mono font-bold text-slate-900" x-text="activeBp.no_bp"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">No. Register Anggaran:</span>
                                        <span class="font-mono font-bold text-kp-blue-700" x-text="activeBp.no_register"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Pemohon & Divisi:</span>
                                        <span class="font-semibold text-slate-800" x-text="activeBp.pemohon + ' (' + activeBp.divisi + ')'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Keperluan:</span>
                                        <span class="font-medium text-slate-700 text-right truncate max-w-[200px]" x-text="activeBp.keperluan"></span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t border-slate-200">
                                        <span class="font-semibold text-slate-700">Nominal ACC:</span>
                                        <span class="font-mono font-bold text-kp-blue-700 text-sm">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeBp.nominal)"></span></span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Divisi / Bagian</label>
                                        <select name="kode_bagian" x-model="activeBp.kode_bagian" @change="updateCoa()" class="h-10 w-full px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600">
                                            <option value="03">Umum & SDM</option>
                                            <option value="06">Redaksi & Wartawan</option>
                                            <option value="01">Iklan (Komisi/Promosi)</option>
                                            <option value="02">Sirkulasi (Ekspedisi/BBM)</option>
                                            <option value="04">Akuntansi</option>
                                            <option value="05">Keuangan</option>
                                            <option value="07">Pimpinan</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kode Perkiraan (COA)</label>
                                        <input type="hidden" name="kode_perkiraan" :value="activeBp.kode_perkiraan">
                                        <input type="text" :value="activeBp.kode_perkiraan" readonly
                                               class="h-10 w-full px-3 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs font-semibold text-slate-600">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Penerima Dana</label>
                                        <input type="text" name="penerima" x-model="activeBp.penerima" required 
                                               class="h-10 w-full px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Metode Pencairan</label>
                                        <select name="metode_bayar" x-model="activeBp.metode" class="h-10 w-full px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600">
                                            <option value="kas_fisik">BKK (Kas Besar Tunai)</option>
                                            <option value="cek">BBK (Cek / BG Bank)</option>
                                            <option value="transfer">BBK (Transfer Bank)</option>
                                        </select>
                                    </div>
                                </div>

                                <div x-show="activeBp.metode === 'cek' || activeBp.metode === 'transfer'" x-transition class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
                                    <label class="block text-[11px] font-semibold text-slate-700">Nomor Cek / BG Bank</label>
                                    <input type="text" name="no_cek" x-model="activeBp.no_cek" placeholder="Contoh: CEK-BJB-882910"
                                           class="h-10 w-full px-3 bg-white border border-slate-300 rounded-xl font-mono text-xs font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kategori Pengeluaran</label>
                                    <select name="kategori" x-model="activeBp.kategori" class="h-10 w-full px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600">
                                        <option value="umum">Umum & Pengadaan</option>
                                        <option value="bbm_armada">BBM & Ekspedisi</option>
                                        <option value="redaksi">Honorarium Redaksi</option>
                                        <option value="komisi_kh">Komisi Humas (KH)</option>
                                        <option value="insentif_ae">Insentif Sales / AE</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Uraian / Keterangan</label>
                                    <input type="text" name="catatan" x-model="activeBp.catatan" 
                                           placeholder="Uraian bukti kas/bank keluar..." 
                                           class="h-10 w-full px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                </div>

                                <div class="pt-2 space-y-2">
                                    <button type="submit" class="h-11 w-full bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span x-text="activeBp.metode === 'kas_fisik' ? 'Cairkan Tunai & Terbitkan BKK (KO)' : 'Cairkan Cek & Terbitkan BBK'"></span>
                                    </button>

                                    <button type="button" @click="showTolakModal = true" class="h-10 w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Kembalikan / Tolak DP dengan Catatan</span>
                                    </button>
                                </div>
                            </form>
                        </template>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal Decision Node: Tolak DP -->
        <div x-cloak x-show="showTolakModal && activeBp.id" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showTolakModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Kembalikan / Tolak DP & BP</h4>
                        <p class="text-[11px] text-slate-500" x-text="activeBp.no_bp + ' • ' + activeBp.pemohon"></p>
                    </div>
                    <button type="button" @click="showTolakModal = false" class="text-slate-400 hover:text-slate-700 font-bold text-lg">&times;</button>
                </div>
                <form method="POST" :action="'{{ url('/kasir/pengeluaran/realisasi-bp') }}/' + activeBp.id + '/tolak'" class="p-5 space-y-4 text-xs">
                    @csrf
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                        Kembalikan dokumen ke fungsi terkait beserta catatan penolakan jika anggaran tidak tersedia atau dokumen belum lengkap.
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Penolakan <span class="text-rose-600">*</span></label>
                        <textarea name="catatan_penolakan" rows="3" required placeholder="Alasan pengembalian dokumen..."
                                  class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-slate-900"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showTolakModal = false" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl shadow-xs">
                            Konfirmasi Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Cetak Dokumen BKK / BBK -->
        <div x-cloak x-show="showCetakModal && bkkPrintData" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500 font-semibold">DIVISI KASIR - SISTEM OPERASI KAS & BANK</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded-md uppercase tracking-widest border bg-sky-50 text-[#0E527B] border-sky-200"
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
                        <span class="text-slate-500">Dasar Pengeluaran</span>
                        <span class="col-span-2 font-mono font-bold text-slate-900">: <span x-text="bkkPrintData?.no_bp"></span> (<span x-text="bkkPrintData?.divisi"></span>)</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">No. Register / DP</span>
                        <span class="col-span-2 font-mono font-bold text-kp-blue-700">: <span x-text="(bkkPrintData?.no_register ?? 'PR-REGISTERED') + ' / ' + (bkkPrintData?.no_dp ?? '-')"></span></span>
                    </div>
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
                        <span class="col-span-2 font-mono font-black text-base text-kp-blue-700">: Rp <span x-text="new Intl.NumberFormat('id-ID').format(bkkPrintData?.nominal || 0)"></span></span>
                    </div>
                    <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-[10px] text-slate-500 block uppercase font-bold">Terbilang:</span>
                        <span class="text-xs font-semibold italic text-slate-800" x-text="bkkPrintData?.terbilang"></span>
                    </div>

                    <!-- Tanda Tangan Cetak SOP -->
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

                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl transition shadow-xs flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>