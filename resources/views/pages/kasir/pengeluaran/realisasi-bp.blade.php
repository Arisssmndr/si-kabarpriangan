<x-app-layout>
    <div class="space-y-6" x-data="{ 
        showCairModal: false, 
        showCetakModal: {{ session('cetak_bkk') ? 'true' : 'false' }},
        bkkPrintData: {{ session('cetak_bkk') ? json_encode(session('cetak_bkk')) : 'null' }},
        activeBp: { 
            id: null,
            no_bp: '', 
            no_register: '',
            pemohon: '', 
            divisi: '',
            kode_bagian: '03',
            kode_perkiraan: '6.00.01.00.01.04',
            keperluan: '', 
            nominal: 0, 
            penerima: '',
            kategori: 'umum',
            metode: 'kas_fisik',
            catatan: ''
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

        <!-- Header Page Realisasi BP & Kas Keluar (OripioFin Breadcrumbs & Style) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-1.5 text-xs text-slate-400 font-medium mb-1">
                    <a href="{{ route('kasir.dashboard') }}" class="hover:text-slate-600 transition">OripioFin</a>
                    <span class="text-slate-300">&rsaquo;</span>
                    <span class="text-slate-400 font-medium">Pengeluaran</span>
                    <span class="text-slate-300">&rsaquo;</span>
                    <span class="text-slate-800 font-semibold">Realisasi BP Anggaran</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    Realisasi BP &amp; Kas Keluar (KO)
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 mt-0.5">
                    Pencairan kas pengeluaran operasional (Sistem KAS) yang telah berstatus ACC Keuangan
                </p>
            </div>

            @if(session('cetak_bkk'))
                <button type="button" @click="showCetakModal = true" class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-[#0B6B44] font-bold text-xs rounded-xl border border-emerald-200 transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Bukti Kas Keluar (KO Terakhir)</span>
                </button>
            @endif
        </div>

        <!-- Cards Total ACC Anggaran (OripioFin Metric Cards) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs shadow-slate-100">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Pengajuan BP Masuk</span>
                <div class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ $bpList->count() }} Permohonan
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Seluruh divisi internal</div>
            </div>

            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs shadow-slate-100">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider block">Siap Dicairkan (Status: Approved)</span>
                <div class="mt-2 text-2xl font-extrabold text-amber-600">
                    Rp {{ number_format($bpList->where('status', 'approved')->sum('nominal_acc'), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">{{ $bpList->where('status', 'approved')->count() }} BP dengan No Register Anggaran</div>
            </div>

            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs shadow-slate-100">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Telah Dicairkan Kasir (KO Terbit)</span>
                <div class="mt-2 text-2xl font-extrabold text-[#0B6B44]">
                    Rp {{ number_format($bpList->where('status', 'dicairkan_kasir')->sum('nominal_acc'), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">{{ $bpList->where('status', 'dicairkan_kasir')->count() }} BP selesai direalisasi</div>
            </div>
        </div>

        <!-- Filter & Search Toolbar (OripioFin Style) -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-xs shadow-slate-100">
            <form method="GET" action="{{ route('kasir.pengeluaran.realisasi-bp') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. BP, No. BKK (KO-...), No. Register Anggaran (PR-...), pemohon..."
                           class="w-full text-xs py-2 pl-9 pr-3 rounded-xl border border-slate-200/80 focus:ring-1 focus:ring-[#0B6B44] focus:border-[#0B6B44] text-slate-800 placeholder-slate-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-[#0B6B44] hover:bg-[#095737] text-white font-bold text-xs rounded-xl transition shadow-xs">
                    Cari BP
                </button>
            </form>
        </div>

        <!-- Tabel Daftar BP ACC Anggaran (OripioFin Table Style) -->
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs shadow-slate-100 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#0B6B44]"></span>
                    Daftar Bukti Pengeluaran (BP) Sesuai Sistem KAS
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">Validasi ketat: Wajib Register Anggaran (PR-...) &amp; ACC Pimpinan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. BP & Register Anggaran</th>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Pemohon & Divisi</th>
                            <th class="px-4 py-3 font-semibold">Keperluan Pengeluaran</th>
                            <th class="px-4 py-3 font-semibold text-right">Nominal ACC</th>
                            <th class="px-4 py-3 font-semibold text-center">Status Anggaran</th>
                            <th class="px-4 py-3 font-semibold text-center">Cash Out</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
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
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3">
                                <div class="font-mono font-bold text-slate-900">{{ $bp->no_bp }}</div>
                                <div class="font-mono text-[10px] text-amber-700 font-semibold">
                                    {{ $bp->no_register_anggaran ?? 'PR-' . date('Ymd') . '-AUTO' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $bp->tanggal_pengajuan ? $bp->tanggal_pengajuan->translatedFormat('d M Y') : date('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800">{{ $bp->pemohon }}</div>
                                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-slate-100 text-slate-600 uppercase">
                                    Divisi {{ $bp->divisi }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700 font-medium">{{ $bp->keperluan }}</td>
                            <td class="px-4 py-3 text-right font-extrabold text-slate-900">Rp {{ number_format($bp->nominal_acc, 0, ',', '.') }}</td>
                            
                            <!-- Status Badge Keuangan -->
                            <td class="px-4 py-3 text-center">
                                @if($bp->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Approved (Siap Cair)
                                    </span>
                                @elseif($bp->status === 'dicairkan_kasir')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-300">
                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        KO Selesai
                                    </span>
                                @elseif($bp->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                        Pending Keuangan
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi Pencairan Kasir -->
                            <td class="px-4 py-3 text-center">
                                @if($bp->status === 'approved')
                                    <button type="button" 
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
                                            }; updateCoa(); showCairModal = true"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#0B6B44] hover:bg-[#095737] active:scale-95 text-white font-semibold text-[11px] rounded-xl transition shadow-xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span>Cairkan Kas</span>
                                    </button>
                                @elseif($bp->status === 'dicairkan_kasir')
                                    <div class="text-[10px] text-slate-500 font-medium">
                                        <span class="font-mono font-bold text-amber-800 block">{{ $bp->kasKeluar->no_bkk ?? 'KO Terbit' }}</span>
                                        {{ $bp->dicairkan_at ? $bp->dicairkan_at->format('d/m/Y H:i') : 'Selesai' }}
                                        @if($bp->kasKeluar)
                                        <button type="button" 
                                                @click="bkkPrintData = {
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
                                                class="mt-1 px-2 py-0.5 bg-amber-50 hover:bg-amber-100 text-amber-800 text-[10px] font-bold rounded border border-amber-200 transition flex items-center gap-1 mx-auto cursor-pointer">
                                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            <span>Cetak BKK</span>
                                        </button>
                                        @endif
                                    </div>
                                @else
                                    <button type="button" disabled title="Hanya BP yang telah disetujui (Approved) oleh Divisi Keuangan yang dapat dicairkan"
                                            class="px-3 py-1.5 bg-slate-100 text-slate-400 font-medium text-[11px] rounded-lg cursor-not-allowed">
                                        Terkunci
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-6 text-center text-slate-400">Tidak ada bukti pengeluaran yang ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>



        <!-- Modal Form Cairkan BP / Terbitkan KO (KAS) -->
        <div x-cloak x-show="showCairModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCairModal = false" class="bg-white rounded-2xl max-w-xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">
                <!-- Header (shrink-0) -->
                <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Form Pengeluaran Kas (KO - KAS)</h4>
                            <p class="text-[11px] text-slate-500">Pencairan Realisasi BP Berdasarkan Register Anggaran</p>
                        </div>
                    </div>
                    <button type="button" @click="showCairModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.pengeluaran.realisasi-bp.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="bp_id" :value="activeBp.id">
                    <input type="hidden" name="nominal_cair" :value="activeBp.nominal">

                    <!-- Scrollable Body (flex-1 overflow-y-auto) -->
                    <div class="flex-1 overflow-y-auto px-5 py-3 space-y-3 text-xs">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-500">No. Bukti Pengeluaran (BP):</span>
                                <span class="font-mono font-bold text-slate-900" x-text="activeBp.no_bp"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">No. Register Anggaran (Pimpinan):</span>
                                <span class="font-mono font-bold text-amber-700" x-text="activeBp.no_register"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Pemohon Asli:</span>
                                <span class="font-bold text-slate-800" x-text="activeBp.pemohon"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Keperluan:</span>
                                <span class="font-medium text-slate-700" x-text="activeBp.keperluan"></span>
                            </div>
                            <div class="flex justify-between pt-1 border-t border-slate-200/80">
                                <span class="font-bold text-slate-700">Nominal ACC Keuangan:</span>
                                <span class="font-mono font-black text-amber-700 text-sm">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeBp.nominal)"></span></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- Divisi Pemohon -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Divisi / Bagian</label>
                                <select name="kode_bagian" x-model="activeBp.kode_bagian" @change="updateCoa()" class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-800 font-semibold">
                                    <option value="03">Divisi Umum & SDM</option>
                                    <option value="06">Divisi Redaksi & Wartawan</option>
                                    <option value="01">Divisi Iklan (Komisi/Promosi)</option>
                                    <option value="02">Divisi Sirkulasi (Ekspedisi/BBM)</option>
                                    <option value="04">Divisi Akuntansi</option>
                                    <option value="05">Divisi Keuangan</option>
                                    <option value="07">Divisi Pimpinan</option>
                                </select>
                            </div>

                            <!-- Bagan Akun Perkiraan (COA) -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kode Perkiraan (COA)</label>
                                <select name="kode_perkiraan" x-model="activeBp.kode_perkiraan" class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-800 font-mono text-[11px]">
                                    <option value="6.00.01.00.01.04">6.00.01.00.01.04 - Beban Umum/RT/Listrik/BBM</option>
                                    <option value="5.00.01.01.02">5.00.01.01.02 - Beban Liputan Redaksi</option>
                                    <option value="6.00.01.01.01.02">6.00.01.01.01.02 - Beban Komisi Humas & Insentif AE</option>
                                    <option value="6.00.01.01.01.03">6.00.01.01.01.03 - Beban Administrasi Akuntansi</option>
                                    <option value="6.00.01.01.00.00">6.00.01.01.00.00 - Beban Operasional Pimpinan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- Nama Penerima Dana -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Diberikan Kepada (Penerima)</label>
                                <input type="text" name="penerima" x-model="activeBp.penerima" required 
                                       class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-900 font-semibold">
                            </div>

                            <!-- Metode Kas Keluar -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pencairan</label>
                                <select name="metode_bayar" x-model="activeBp.metode" class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-800">
                                    <option value="kas_fisik">Uang Tunai / Kas Fisik Laci</option>
                                    <option value="transfer">Transfer Rekening Bank BJB</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Pengeluaran</label>
                            <select name="kategori" x-model="activeBp.kategori" class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-800">
                                <option value="umum">Umum & Perlengkapan Rumah Tangga</option>
                                <option value="bbm_armada">BBM, Tol & Operasional Ekspedisi</option>
                                <option value="redaksi">Honorarium Redaksi & Liputan Lapangan</option>
                                <option value="komisi_kh">Komisi Humas (KH)</option>
                                <option value="insentif_ae">Insentif Sales / AE</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Pengeluaran (BKK)</label>
                            <input type="text" name="catatan" x-model="activeBp.catatan" 
                                   placeholder="Uraian pengeluaran kas keluar..." 
                                   class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-900">
                        </div>
                    </div>

                    <!-- Footer Action Buttons (shrink-0) -->
                    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showCairModal = false" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-lg transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg transition shadow-2xs cursor-pointer">
                            Cairkan Kas Keluar & Terbitkan KO
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Modal Cetak Bukti Kas Keluar (BKK / KO) DOS KAS -->
        <div x-cloak x-show="showCetakModal && bkkPrintData" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">
                <!-- Header Dokumen DOS KAS -->
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500 font-semibold">DIVISI KASIR - SISTEM OPERASI KAS</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded bg-amber-100 text-amber-800 uppercase tracking-widest border border-amber-300">
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
                        <span class="col-span-2 font-mono font-bold text-amber-700">: <span x-text="bkkPrintData?.no_register ?? 'PR-REGISTERED'"></span></span>
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
                    <div class="grid grid-cols-3 py-1.5 bg-amber-50/80 px-2 rounded-lg">
                        <span class="font-bold text-amber-900">Jumlah Kas Keluar</span>
                        <span class="col-span-2 font-mono font-black text-sm text-amber-800">: Rp <span x-text="new Intl.NumberFormat('id-ID').format(bkkPrintData?.nominal || 0)"></span></span>
                    </div>
                    <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
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
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Dokumen KO</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
