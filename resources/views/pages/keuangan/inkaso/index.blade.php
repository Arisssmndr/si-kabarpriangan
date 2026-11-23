<x-app-layout>
    <div class="space-y-5" x-data="{
        activeTab: '{{ request('tab', 'antrean') }}',
        searchQuery: '{{ request('q', '') }}',
        showAssignModal: false,
        assignFakturId: '',
        assignFakturNo: '',
        assignCustomer: '',
        assignPiutang: 0,
        showSetoranModal: false,
        setoranFakturId: '',
        setoranTugasId: '',
        setoranFakturNo: '',
        setoranCustomer: '',
        setoranSisaPiutang: 0,
        nominalSetoran: 0,
        openAssign(id, noFaktur, customer, piutang) {
            this.assignFakturId = id;
            this.assignFakturNo = noFaktur;
            this.assignCustomer = customer;
            this.assignPiutang = piutang;
            this.showAssignModal = true;
        },
        openSetoran(fakturId, tugasId, noFaktur, customer, sisaPiutang) {
            this.setoranFakturId = fakturId;
            this.setoranTugasId = tugasId;
            this.setoranFakturNo = noFaktur;
            this.setoranCustomer = customer;
            this.setoranSisaPiutang = sisaPiutang;
            this.nominalSetoran = sisaPiutang;
            this.showSetoranModal = true;
        },
        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        },
        matchesSearch(...terms) {
            if (!this.searchQuery || this.searchQuery.trim() === '') return true;
            const q = this.searchQuery.toLowerCase().trim();
            return terms.some(t => t && t.toString().toLowerCase().includes(q));
        }
    }">

        <!-- 1. Header Halaman (Standar Divisi Iklan 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-9 sm:h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Penagihan Lapangan (Inkaso)
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 shrink-0">
                            Kolektor &amp; Kasir
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Antrean faktur piutang, penugasan petugas lapangan, cetak surat penagihan, dan input setoran kas
                    </p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 font-semibold rounded-xl text-sm flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-slate-100 border border-slate-300 text-slate-800 font-semibold rounded-xl text-sm flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- 2. KPI Summary Cards (Standar Divisi Iklan 60-30-10) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Piutang Menanti Tagih</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalPiutangBerjalan, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    Sisa saldo transaksi belum lunas
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Faktur Belum Ditugaskan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($fakturBelumTertugaskan) }} <span class="text-xs font-normal text-slate-400 font-sans">Faktur</span>
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    Perlu penugasan petugas inkaso
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Penugasan Berjalan</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    {{ number_format($tugasAktifCount) }} <span class="text-xs font-normal text-slate-400 font-sans">Tugas</span>
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    Menunggu setoran dana ke kasir
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Riwayat Selesai</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($riwayatPenugasan->count()) }} <span class="text-xs font-normal text-slate-400 font-sans">Tugas</span>
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    Penugasan lunas dan disetor
                </div>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter (h-11 rounded-xl) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('keuangan.inkaso.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="tab" :value="activeTab">

                <div class="sm:col-span-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Data</label>
                    <input type="text" 
                           name="q" 
                           x-model="searchQuery" 
                           value="{{ request('q') }}"
                           placeholder="Cari no. faktur atau nama customer..." 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kanal / Divisi</label>
                    <select name="kategori" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs font-semibold">
                        <option value="">Semua Kanal</option>
                        <option value="iklan" {{ request('kategori') === 'iklan' ? 'selected' : '' }}>Divisi Iklan</option>
                        <option value="sirkulasi" {{ request('kategori') === 'sirkulasi' ? 'selected' : '' }}>Divisi Sirkulasi</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Penugasan</label>
                    <select name="status_petugas" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="">Semua Status</option>
                        <option value="belum" {{ request('status_petugas') === 'belum' ? 'selected' : '' }}>Belum Ditugaskan</option>
                        <option value="sudah" {{ request('status_petugas') === 'sudah' ? 'selected' : '' }}>Sudah Ditugaskan</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'kategori', 'status_petugas', 'petugas_id']))
                        <a href="{{ route('keuangan.inkaso.index', ['tab' => request('tab', 'antrean')]) }}" 
                           class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tab Navigasi Inkaso -->
        <div class="bg-white border border-slate-200 rounded-xl p-2.5 shadow-xs">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <button type="button" @click="activeTab = 'antrean'"
                        :class="activeTab === 'antrean' ? 'bg-kp-blue-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50 font-semibold'"
                        class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-2 shrink-0">
                    <span>1. Antrean Faktur Piutang</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'antrean' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'">{{ $antreanPiutang->count() }}</span>
                </button>

                <button type="button" @click="activeTab = 'berjalan'"
                        :class="activeTab === 'berjalan' ? 'bg-kp-blue-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50 font-semibold'"
                        class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-2 shrink-0">
                    <span>2. Penugasan Aktif Lapangan</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'berjalan' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'">{{ $penugasanBerjalan->count() }}</span>
                </button>

                <button type="button" @click="activeTab = 'riwayat'"
                        :class="activeTab === 'riwayat' ? 'bg-kp-blue-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50 font-semibold'"
                        class="px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-2 shrink-0">
                    <span>3. Riwayat Selesai</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'riwayat' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'">{{ $riwayatPenugasan->count() }}</span>
                </button>
            </div>
        </div>

        <!-- 4. Pure Data Table Sesuai Tab Terpilih -->

        <!-- TAB 1: ANTREAN FAKTUR PIUTANG -->
        <div x-show="activeTab === 'antrean'" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Faktur</th>
                            <th class="py-3 px-3.5">Jatuh Tempo</th>
                            <th class="py-3 px-3.5">Pemasang / Pelanggan</th>
                            <th class="py-3 px-3.5">Telepon</th>
                            <th class="py-3 px-3.5 text-center">Kanal</th>
                            <th class="py-3 px-3.5 text-right w-32">Tagihan</th>
                            <th class="py-3 px-3.5 text-right w-32">Sisa Piutang</th>
                            <th class="py-3 px-3.5 text-center">Status Petugas</th>
                            <th class="py-3 px-3.5 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($antreanPiutang as $key => $faktur)
                            <tr x-show="matchesSearch('{{ $faktur->no_faktur }}', '{{ addslashes($faktur->customer->nama_customer ?? '') }}', '{{ $faktur->kategori }}', '{{ addslashes($faktur->penugasanAktif->petugas->name ?? '') }}')"
                                class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $key + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" class="hover:underline">
                                        {{ $faktur->no_faktur }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-mono font-normal">
                                    {{ optional($faktur->jatuh_tempo)->format('d/m/Y') ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900 uppercase">
                                    {{ $faktur->customer->nama_customer ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    {{ $faktur->customer->no_telepon ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium uppercase border border-slate-200 {{ $faktur->kategori === 'iklan' ? 'bg-kp-blue-50 text-kp-blue-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $faktur->kategori }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($faktur->total_tagihan, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-normal text-slate-700 tabular-nums">{{ number_format($faktur->sisa_piutang, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($faktur->penugasanAktif)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200">
                                            {{ $faktur->penugasanAktif->petugas->name ?? 'Petugas' }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-slate-100 text-slate-700 border-slate-200">
                                            Belum Ditugaskan
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <button type="button" 
                                            @click="openAssign({{ $faktur->id }}, '{{ $faktur->no_faktur }}', '{{ addslashes($faktur->customer->nama_customer ?? '') }}', {{ $faktur->sisa_piutang }})"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold text-xs transition cursor-pointer shadow-2xs">
                                        {{ $faktur->penugasanAktif ? 'Tugaskan Ulang' : '+ Tugaskan' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400">
                                    <p class="text-sm font-semibold text-slate-600">
                                        @if(request('q'))
                                             Tidak ada faktur piutang yang cocok dengan pencarian "{{ request('q') }}".
                                        @else
                                             Tidak ada antrean faktur piutang. Seluruh faktur telah lunas 100%.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2: PENUGASAN AKTIF LAPANGAN -->
        <div x-show="activeTab === 'berjalan'" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Tugas</th>
                            <th class="py-3 px-3.5">Tanggal Tugas</th>
                            <th class="py-3 px-3.5">Petugas Inkaso</th>
                            <th class="py-3 px-3.5">No. Faktur</th>
                            <th class="py-3 px-3.5">Pemasang / Pelanggan</th>
                            <th class="py-3 px-3.5 text-right w-32">Target Piutang</th>
                            <th class="py-3 px-3.5 text-center">Dokumen</th>
                            <th class="py-3 px-3.5 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($penugasanBerjalan as $key => $tugas)
                            <tr x-show="matchesSearch('{{ $tugas->no_tugas }}', '{{ $tugas->faktur->no_faktur ?? '' }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '') }}', '{{ addslashes($tugas->petugas->name ?? '') }}')"
                                class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $key + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-slate-900">
                                    {{ $tugas->no_tugas }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-mono font-normal">
                                    {{ optional($tugas->tanggal_tugas)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $tugas->petugas->name ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $tugas->faktur->no_faktur ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900 uppercase">
                                    {{ $tugas->faktur->customer->nama_customer ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-normal text-slate-700 tabular-nums">{{ number_format($tugas->faktur->sisa_piutang ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <a href="{{ route('keuangan.inkaso.surat-tugas.cetak', $tugas->id) }}" target="_blank"
                                       class="px-2.5 py-1 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-semibold inline-flex items-center transition">
                                        Cetak Surat Tugas
                                    </a>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <button type="button" 
                                            @click="openSetoran({{ $tugas->faktur_id }}, {{ $tugas->id }}, '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '') }}', {{ $tugas->faktur->sisa_piutang }})"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs transition cursor-pointer shadow-2xs">
                                        Input Setoran
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <p class="text-sm font-semibold text-slate-600">
                                        @if(request('q'))
                                            Tidak ada penugasan aktif yang cocok dengan "{{ request('q') }}".
                                        @else
                                            Tidak ada penugasan yang sedang berjalan.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 3: RIWAYAT SELESAI -->
        <div x-show="activeTab === 'riwayat'" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Tugas</th>
                            <th class="py-3 px-3.5">Tanggal Tugas</th>
                            <th class="py-3 px-3.5">Petugas Inkaso</th>
                            <th class="py-3 px-3.5">No. Faktur</th>
                            <th class="py-3 px-3.5">Pemasang / Pelanggan</th>
                            <th class="py-3 px-3.5 text-center">Kwitansi Setoran</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($riwayatPenugasan as $key => $tugas)
                            <tr x-show="matchesSearch('{{ $tugas->no_tugas }}', '{{ $tugas->faktur->no_faktur ?? '' }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '') }}', '{{ addslashes($tugas->petugas->name ?? '') }}')"
                                class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $key + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-slate-900">{{ $tugas->no_tugas }}</td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-mono font-normal">{{ optional($tugas->tanggal_tugas)->format('d/m/Y') }}</td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $tugas->petugas->name ?? '-' }}</td>
                                <td class="py-3.5 px-3.5 font-mono text-kp-blue-700 font-bold text-sm">{{ $tugas->faktur->no_faktur ?? '-' }}</td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900 uppercase">{{ $tugas->faktur->customer->nama_customer ?? '-' }}</td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($tugas->pembayaran->isNotEmpty())
                                        @foreach($tugas->pembayaran as $kw)
                                            <a href="{{ route('keuangan.inkaso.kwitansi.cetak', $kw->id) }}" target="_blank"
                                               class="text-xs font-mono font-semibold text-kp-blue-700 hover:underline block">
                                                {{ $kw->no_kwitansi }} (Rp {{ number_format($kw->nominal_bayar, 0, ',', '.') }})
                                            </a>
                                        @endforeach
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200">
                                        Lunas Selesai
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <p class="text-sm font-semibold text-slate-600">
                                        @if(request('q'))
                                            Tidak ada riwayat penugasan yang cocok dengan "{{ request('q') }}".
                                        @else
                                            Belum ada riwayat penugasan yang selesai.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL 1: FORM ASSIGN PETUGAS INKASO -->
        <div x-cloak x-show="showAssignModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative" @click.outside="showAssignModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">
                        Tugaskan Petugas Inkaso
                    </h3>
                    <button type="button" @click="showAssignModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>

                <form action="{{ route('keuangan.inkaso.assign') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="faktur_id" :value="assignFakturId">

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nomor Faktur:</span>
                            <span class="font-mono font-bold text-kp-blue-700" x-text="assignFakturNo"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Customer:</span>
                            <span class="font-bold text-slate-800" x-text="assignCustomer"></span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-slate-200">
                            <span class="text-slate-700 font-semibold">Sisa Tagihan:</span>
                            <span class="font-mono font-bold text-kp-blue-700">Rp <span x-text="formatRupiah(assignPiutang)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Petugas Inkaso Lapangan <span class="text-rose-500">*</span></label>
                        <select name="petugas_id" required class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-sm text-slate-900 font-semibold bg-white focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 shadow-2xs cursor-pointer">
                            <option value="">-- Pilih Petugas --</option>
                            @foreach($petugasInkaso as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ ucfirst($p->role) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Mulai Penugasan <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_tugas" value="{{ date('Y-m-d') }}" required
                               class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-sm text-slate-900 font-semibold bg-white focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Penugasan / Instruksi Khusus</label>
                        <textarea name="catatan" rows="2" placeholder="Catatan instruksi penagihan..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 shadow-2xs"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                        <button type="button" @click="showAssignModal = false" class="h-10 px-4 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-5 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-900 text-xs sm:text-sm font-bold shadow-xs transition cursor-pointer">
                            Terbitkan Surat Tugas ➔
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 2: FORM INPUT SETORAN INKASO -->
        <div x-cloak x-show="showSetoranModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative" @click.outside="showSetoranModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">
                        Input Setoran Inkaso ke Kas
                    </h3>
                    <button type="button" @click="showSetoranModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>

                <form action="{{ route('keuangan.inkaso.setoran.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="faktur_id" :value="setoranFakturId">
                    <input type="hidden" name="penugasan_inkaso_id" :value="setoranTugasId">

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nomor Faktur:</span>
                            <span class="font-mono font-bold text-kp-blue-700" x-text="setoranFakturNo"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Customer:</span>
                            <span class="font-bold text-slate-800" x-text="setoranCustomer"></span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-slate-200">
                            <span class="text-slate-700 font-semibold">Sisa Piutang Berjalan:</span>
                            <span class="font-mono font-bold text-kp-blue-700">Rp <span x-text="formatRupiah(setoranSisaPiutang)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal Setoran Diterima (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="nominal_setoran" x-model="nominalSetoran" :max="setoranSisaPiutang" min="1" step="any" required
                               class="w-full h-11 px-3.5 text-sm font-mono font-bold rounded-xl border border-slate-300 bg-white focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 text-slate-900 shadow-2xs">
                        <span class="text-xs text-slate-400 mt-1 block">
                            Maksimal setoran: Rp <span x-text="formatRupiah(setoranSisaPiutang)"></span>
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Terima Kas</label>
                            <input type="date" name="tanggal_bayar" value="{{ date('Y-m-d') }}" required
                                   class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-sm font-semibold bg-white focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode</label>
                            <select name="metode_bayar" required class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-sm font-semibold bg-white focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 shadow-2xs cursor-pointer">
                                <option value="tunai">Uang Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Setoran</label>
                        <input type="text" name="catatan" placeholder="Catatan bukti setoran lapangan..."
                               class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 shadow-2xs">
                    </div>

                    <div class="p-3 rounded-xl bg-kp-blue-50 border border-kp-blue-200 text-xs text-kp-blue-800">
                        Setoran ini otomatis memotong sisa piutang faktur dan mencatat mutasi <strong>Kas Masuk</strong> pada Buku Kas.
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                        <button type="button" @click="showSetoranModal = false" class="h-10 px-4 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-5 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-bold shadow-xs transition cursor-pointer">
                            Simpan &amp; Masukkan Kas ➔
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
