<x-app-layout>
    <div class="space-y-6" x-data="{
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
        showLaporanModal: false,
        laporanTugasId: '',
        laporanNoTugas: '',
        laporanFakturNo: '',
        laporanCustomer: '',
        laporanSisaPiutang: 0,
        laporanPetugasNama: '',
        hasilKunjungan: 'janji_bayar',
        tanggalLapor: '{{ date('Y-m-d') }}',
        tanggalJanjiBayar: '',
        nominalDiterima: 0,
        keteranganHasil: '',
        openLaporan(tugasId, noTugas, fakturNo, customer, sisaPiutang, petugas) {
            this.laporanTugasId = tugasId;
            this.laporanNoTugas = noTugas;
            this.laporanFakturNo = fakturNo;
            this.laporanCustomer = customer;
            this.laporanSisaPiutang = sisaPiutang;
            this.laporanPetugasNama = petugas;
            this.hasilKunjungan = 'janji_bayar';
            this.tanggalLapor = '{{ date('Y-m-d') }}';
            this.tanggalJanjiBayar = '';
            this.nominalDiterima = 0;
            this.keteranganHasil = '';
            this.showLaporanModal = true;
        },
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

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            PENAGIHAN LAPANGAN (INKASO)
                        </h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Antrean faktur piutang, penugasan petugas lapangan, cetak surat penagihan, dan input setoran kas
                    </p>
                </div>
            </div>


        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <!-- KPI Ringkas Inkaso -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-rose-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-700 font-mono">Total Piutang Menanti Tagih</span>
                <div class="mt-2 text-2xl font-black text-rose-600 tracking-tight font-mono">
                    Rp {{ number_format($totalPiutangBerjalan, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    Sisa saldo transaksi belum lunas
                </div>
            </div>

            <div class="bg-white border border-amber-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700 font-mono">Faktur Belum Ditugaskan</span>
                <div class="mt-2 text-2xl font-black text-amber-600 tracking-tight">
                    {{ $fakturBelumTertugaskan }} <span class="text-xs font-medium text-amber-700">Faktur</span>
                </div>
                <div class="mt-2 text-xs text-amber-800 font-semibold">
                    Perlu penugasan ke Petugas Inkaso
                </div>
            </div>

            <div class="bg-white border border-blue-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 font-mono">Penugasan di Lapangan</span>
                <div class="mt-2 text-2xl font-black text-blue-600 tracking-tight">
                    {{ $tugasAktifCount }} <span class="text-xs font-medium text-blue-700">Penugasan</span>
                </div>
                <div class="mt-2 text-xs text-blue-700 font-semibold">
                    Sedang dibawa petugas nagih
                </div>
            </div>

            <div class="bg-white border border-purple-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-700 font-mono">Janji Bayar Dipantau</span>
                <div class="mt-2 text-2xl font-black text-purple-600 tracking-tight">
                    {{ $janjiBayarCount }} <span class="text-xs font-medium text-purple-700">Klien</span>
                </div>
                <div class="mt-2 text-xs text-purple-800 font-semibold">
                    Keterangan janji tanggal bayar
                </div>
            </div>
        </div>

        <!-- Filter & Pencarian Faktur Inkaso -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('keuangan.inkaso.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="tab" :value="activeTab">

                <div class="sm:col-span-5">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">
                        Pencarian No. Faktur / Customer
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            🔍
                        </span>
                        <input type="text" name="q" x-model="searchQuery" value="{{ request('q') }}"
                               placeholder="Cari nomor faktur (misal: FKKRN001, FKSRK001) atau nama..." 
                               class="w-full text-xs rounded-xl border border-slate-300 pl-9 pr-3 py-2.5 focus:ring-2 focus:ring-amber-500 focus:outline-hidden">
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">
                        Kanal / Divisi
                    </label>
                    <select name="kategori" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-amber-500 focus:outline-hidden">
                        <option value="">Semua Kanal</option>
                        <option value="iklan" {{ request('kategori') === 'iklan' ? 'selected' : '' }}>Divisi Iklan</option>
                        <option value="sirkulasi" {{ request('kategori') === 'sirkulasi' ? 'selected' : '' }}>Divisi Sirkulasi</option>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">
                        Status / Petugas
                    </label>
                    <select name="status_petugas" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-amber-500 focus:outline-hidden">
                        <option value="">Semua Status Penugasan</option>
                        <option value="belum" {{ request('status_petugas') === 'belum' ? 'selected' : '' }}>⚠️ Belum Ditugaskan</option>
                        <option value="sudah" {{ request('status_petugas') === 'sudah' ? 'selected' : '' }}>🧑‍💼 Sudah Ada Petugas</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="w-full h-10 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>Cari Faktur</span>
                    </button>
                    @if(request()->hasAny(['q', 'kategori', 'status_petugas', 'petugas_id']))
                        <a href="{{ route('keuangan.inkaso.index', ['tab' => request('tab', 'antrean')]) }}" 
                           class="h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center justify-center transition"
                           title="Reset Pencarian">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tab Navigasi Inkaso -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2 flex-wrap">
            <button type="button" @click="activeTab = 'antrean'"
                    :class="activeTab === 'antrean' ? 'bg-white text-slate-900 font-bold border-slate-300 shadow-xs' : 'text-slate-500 hover:text-slate-800 border-transparent'"
                    class="px-4 py-2 rounded-xl text-xs border transition cursor-pointer flex items-center gap-2">
                <span>1. Antrean Faktur Piutang</span>
                <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">{{ $antreanPiutang->count() }}</span>
            </button>

            <button type="button" @click="activeTab = 'berjalan'"
                    :class="activeTab === 'berjalan' ? 'bg-white text-slate-900 font-bold border-slate-300 shadow-xs' : 'text-slate-500 hover:text-slate-800 border-transparent'"
                    class="px-4 py-2 rounded-xl text-xs border transition cursor-pointer flex items-center gap-2">
                <span>2. Penugasan Aktif Lapangan</span>
                <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">{{ $penugasanBerjalan->count() }}</span>
            </button>

            <button type="button" @click="activeTab = 'laporan'"
                    :class="activeTab === 'laporan' ? 'bg-white text-slate-900 font-bold border-slate-300 shadow-xs' : 'text-slate-500 hover:text-slate-800 border-transparent'"
                    class="px-4 py-2 rounded-xl text-xs border transition cursor-pointer flex items-center gap-2">
                <span>3. Laporan Hasil Penugasan (Catatan Bu Ulfa)</span>
                <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">{{ $laporanHasil->count() }}</span>
                @if($janjiBayarCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black bg-rose-500 text-white animate-pulse" title="{{ $janjiBayarCount }} Janji Bayar Aktif">{{ $janjiBayarCount }} Janji</span>
                @endif
            </button>

            <button type="button" @click="activeTab = 'riwayat'"
                    :class="activeTab === 'riwayat' ? 'bg-white text-slate-900 font-bold border-slate-300 shadow-xs' : 'text-slate-500 hover:text-slate-800 border-transparent'"
                    class="px-4 py-2 rounded-xl text-xs border transition cursor-pointer flex items-center gap-2">
                <span>4. Riwayat Selesai Lunas</span>
                <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ $riwayatPenugasan->count() }}</span>
            </button>
        </div>

        <!-- TAB 1: ANTREAN FAKTUR PIUTANG -->
        <div x-show="activeTab === 'antrean'" class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                        Antrean Faktur Memiliki Piutang (Sisa Tagihan > 0)
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Tugaskan petugas lapangan untuk menagih sisa saldo kepada klien/agen terkait
                    </p>
                </div>
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                        🔍
                    </span>
                    <input type="text" x-model="searchQuery" placeholder="Filter no. faktur cepat..." 
                           class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 pl-8 pr-7 py-1.5 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-hidden">
                    <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 text-xs font-bold">
                        ✕
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Faktur</th>
                            <th class="py-3 px-4">Customer</th>
                            <th class="py-3 px-4">Kanal</th>
                            <th class="py-3 px-4">Jatuh Tempo</th>
                            <th class="py-3 px-4 text-right">Total Tagihan</th>
                            <th class="py-3 px-4 text-right">Sisa Piutang</th>
                            <th class="py-3 px-4 text-center">Status Petugas</th>
                            <th class="py-3 px-4 text-center">Aksi Penugasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($antreanPiutang as $faktur)
                            <tr x-show="matchesSearch('{{ $faktur->no_faktur }}', '{{ addslashes($faktur->customer->nama_customer ?? '') }}', '{{ $faktur->kategori }}', '{{ addslashes($faktur->penugasanAktif->petugas->name ?? '') }}')"
                                class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                    <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" class="text-blue-600 hover:underline">
                                        {{ $faktur->no_faktur }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $faktur->customer->nama_customer ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $faktur->customer->no_telepon ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $faktur->kategori === 'iklan' ? 'bg-sky-50 text-sky-700' : 'bg-indigo-50 text-indigo-700' }}">
                                        {{ $faktur->kategori }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-[11px] font-mono text-rose-600 font-bold">
                                    {{ $faktur->jatuh_tempo->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold">
                                    Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-rose-600 font-mono">
                                    Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($faktur->penugasanAktif)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            🧑‍💼 {{ $faktur->penugasanAktif->petugas->name ?? 'Petugas' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Belum Ditugaskan
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <button type="button" 
                                            @click="openAssign({{ $faktur->id }}, '{{ $faktur->no_faktur }}', '{{ addslashes($faktur->customer->nama_customer ?? '') }}', {{ $faktur->sisa_piutang }})"
                                            class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition cursor-pointer shadow-2xs">
                                        {{ $faktur->penugasanAktif ? 'Tugaskan Ulang' : '+ Tugaskan Petugas' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">🔍</div>
                                    <p class="text-sm font-semibold text-slate-600">
                                        @if(request('q'))
                                            Tidak ada faktur piutang yang cocok dengan pencarian "{{ request('q') }}".
                                        @else
                                            Tidak ada antrean faktur piutang.
                                        @endif
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1">
                                        @if(request('q'))
                                            Periksa kembali nomor faktur atau klik tombol Reset.
                                        @else
                                            Seluruh faktur saat ini telah lunas 100%.
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
        <div x-show="activeTab === 'berjalan'" class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                        Penugasan Aktif (Sedang Berjalan di Lapangan)
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Petugas sedang membawa surat tugas. Klik "Input Setoran Kas" saat petugas menyetor uang ke kasir.
                    </p>
                </div>
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                        🔍
                    </span>
                    <input type="text" x-model="searchQuery" placeholder="Filter no. faktur / tugas..." 
                           class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 pl-8 pr-7 py-1.5 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                    <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 text-xs font-bold">
                        ✕
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Tugas</th>
                            <th class="py-3 px-4">Petugas Inkaso</th>
                            <th class="py-3 px-4">No. Faktur &amp; Klien</th>
                            <th class="py-3 px-4">Tanggal Tugas</th>
                            <th class="py-3 px-4 text-right">Target Piutang</th>
                            <th class="py-3 px-4 text-center">Dokumen Tugas</th>
                            <th class="py-3 px-4 text-center">Aksi Setoran Kas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($penugasanBerjalan as $tugas)
                            <tr x-show="matchesSearch('{{ $tugas->no_tugas }}', '{{ $tugas->faktur->no_faktur ?? '' }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '') }}', '{{ addslashes($tugas->petugas->name ?? '') }}')"
                                class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-amber-800">
                                    {{ $tugas->no_tugas }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $tugas->petugas->name ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $tugas->petugas->email ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-mono font-bold text-blue-600">{{ $tugas->faktur->no_faktur ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-700 mt-0.5">{{ $tugas->faktur->customer->nama_customer ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-[11px] font-mono">
                                    {{ $tugas->tanggal_tugas->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-rose-600 font-mono">
                                    Rp {{ number_format($tugas->faktur->sisa_piutang ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('keuangan.inkaso.surat-tugas.cetak', $tugas->id) }}" target="_blank"
                                       class="px-2.5 py-1 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-700 text-[11px] font-bold inline-flex items-center gap-1 transition">
                                        🖨 Cetak Surat Tugas
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        <button type="button" 
                                                @click="openLaporan({{ $tugas->id }}, '{{ $tugas->no_tugas }}', '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '') }}', {{ $tugas->faktur->sisa_piutang }}, '{{ addslashes($tugas->petugas->name ?? '') }}')"
                                                class="px-2.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-[11px] transition cursor-pointer shadow-2xs inline-flex items-center gap-1"
                                                title="Input Laporan Hasil Kunjungan Lapangan (Bu Ulfa)">
                                            <span>📝 Lapor Hasil</span>
                                        </button>
                                        <button type="button" 
                                                @click="openSetoran({{ $tugas->faktur_id }}, {{ $tugas->id }}, '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '') }}', {{ $tugas->faktur->sisa_piutang }})"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition cursor-pointer shadow-2xs inline-flex items-center gap-1"
                                                title="Input Setoran Dana Langsung">
                                            <span>💰 Setor Kas</span>
                                        </button>
                                    </div>
                                    @if($tugas->hasil_kunjungan)
                                        <div class="mt-1">
                                            @if($tugas->hasil_kunjungan === 'janji_bayar')
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-bold text-[10px]">
                                                    📅 Janji: {{ $tugas->tanggal_janji_bayar ? $tugas->tanggal_janji_bayar->format('d/m/Y') : '-' }}
                                                </span>
                                            @elseif($tugas->hasil_kunjungan === 'cicilan')
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 font-bold text-[10px]">
                                                    Cicil: Rp {{ number_format($tugas->nominal_diterima, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">📋</div>
                                    <p class="text-sm font-semibold text-slate-600">
                                        @if(request('q'))
                                             Tidak ada penugasan aktif yang cocok dengan "{{ request('q') }}".
                                        @else
                                             Tidak ada penugasan yang sedang berjalan.
                                        @endif
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1">Pilih tab "Antrean Faktur Piutang" untuk menugaskan petugas lapangan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 3: LAPORAN HASIL PENUGASAN (CATATAN BU ULFA) -->
        <div x-show="activeTab === 'laporan'" class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <span>Laporan Hasil Setelah Penugasan Lapangan (Catatan Bu Ulfa)</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">
                            {{ $laporanHasil->count() }} Laporan
                        </span>
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Rekam jejak kunjungan penagihan: Janji tanggal bayar, pembayaran cicilan, dan catatan khusus dari lapangan
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('keuangan.inkaso.rekap-hasil.cetak', request()->query()) }}" target="_blank"
                       class="px-3 py-1.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold inline-flex items-center gap-1.5 transition shadow-2xs">
                        <span>🖨 Cetak Rekap Hasil</span>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Tugas &amp; Tgl</th>
                            <th class="py-3 px-4">Petugas Inkaso</th>
                            <th class="py-3 px-4">Klien / Agen &amp; Wilayah</th>
                            <th class="py-3 px-4">No. Faktur</th>
                            <th class="py-3 px-4 text-center">Hasil Kunjungan</th>
                            <th class="py-3 px-4 text-right">Setoran Kas</th>
                            <th class="py-3 px-4">Catatan Keterangan Lapangan</th>
                            <th class="py-3 px-4 text-center">Status / Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($laporanHasil as $lh)
                            <tr class="hover:bg-purple-50/20 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-mono font-bold text-amber-900">{{ $lh->no_tugas }}</div>
                                    <div class="text-[10px] text-slate-400">Tugas: {{ $lh->tanggal_tugas->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-purple-700 font-semibold">Lapor: {{ $lh->tanggal_lapor ? $lh->tanggal_lapor->format('d/m/Y') : '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $lh->petugas->name ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $lh->faktur->customer->nama_customer ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $lh->faktur->customer->kota ?? '-' }} ({{ $lh->faktur->customer->no_telepon ?? '-' }})</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-blue-600">
                                    {{ $lh->faktur->no_faktur ?? '-' }}
                                    <div class="text-[10px] text-rose-600 font-mono">Sisa: Rp {{ number_format($lh->faktur->sisa_piutang ?? 0, 0, ',', '.') }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($lh->hasil_kunjungan === 'janji_bayar')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                            📅 Janji: {{ $lh->tanggal_janji_bayar ? $lh->tanggal_janji_bayar->format('d/m/Y') : '-' }}
                                        </span>
                                    @elseif($lh->hasil_kunjungan === 'cicilan')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-300">
                                            💰 Cicilan
                                        </span>
                                    @elseif($lh->hasil_kunjungan === 'lunas')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                            ✓ Lunas Penuh
                                        </span>
                                    @elseif($lh->hasil_kunjungan === 'tidak_bertemu')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                            👤 Tidak Bertemu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                            Belum Bayar
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold {{ $lh->nominal_diterima > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                    Rp {{ number_format($lh->nominal_diterima ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-[11px] text-slate-700 max-w-xs">
                                    <div class="italic">"{{ $lh->keterangan_hasil ?: ($lh->catatan ?: '-') }}"</div>
                                    <div class="text-[9px] text-slate-400 mt-0.5">Dicatat: {{ $lh->pelapor->name ?? 'Staf Keuangan' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($lh->pembayaran->isNotEmpty())
                                        @foreach($lh->pembayaran as $kw)
                                            <a href="{{ route('keuangan.inkaso.kwitansi.cetak', $kw->id) }}" target="_blank"
                                               class="px-2 py-0.5 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-[10px] font-bold block mb-1">
                                                🧾 Kwitansi Kas
                                            </a>
                                        @endforeach
                                    @elseif($lh->status_tugas === 'berjalan')
                                        <button type="button" 
                                                @click="openLaporan({{ $lh->id }}, '{{ $lh->no_tugas }}', '{{ $lh->faktur->no_faktur }}', '{{ addslashes($lh->faktur->customer->nama_customer ?? '') }}', {{ $lh->faktur->sisa_piutang }}, '{{ addslashes($lh->petugas->name ?? '') }}')"
                                                class="px-2 py-0.5 rounded bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-[10px] font-bold transition cursor-pointer">
                                            ✏️ Update
                                        </button>
                                    @else
                                        <span class="text-[10px] text-slate-400">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">📝</div>
                                    <p class="text-sm font-semibold text-slate-600">Belum ada laporan hasil penugasan yang diinput.</p>
                                    <p class="text-xs text-slate-400 mt-1">Gunakan tombol "📝 Lapor Hasil" pada tab "Penugasan Aktif Lapangan" saat petugas kembali melapor.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 4: RIWAYAT SELESAI -->
        <div x-show="activeTab === 'riwayat'" class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                    Riwayat Penugasan Lapangan yang Telah Selesai
                </h3>
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                        🔍
                    </span>
                    <input type="text" x-model="searchQuery" placeholder="Filter no. faktur riwayat..." 
                           class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 pl-8 pr-7 py-1.5 focus:bg-white focus:ring-2 focus:ring-slate-500 focus:outline-hidden">
                    <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 text-xs font-bold">
                        ✕
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Tugas</th>
                            <th class="py-3 px-4">Petugas</th>
                            <th class="py-3 px-4">No. Faktur &amp; Customer</th>
                            <th class="py-3 px-4">Tanggal Tugas</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Kwitansi Setoran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($riwayatPenugasan as $tugas)
                            <tr x-show="matchesSearch('{{ $tugas->no_tugas }}', '{{ $tugas->faktur->no_faktur ?? '' }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '') }}', '{{ addslashes($tugas->petugas->name ?? '') }}')"
                                class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-800">{{ $tugas->no_tugas }}</td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">{{ $tugas->petugas->name ?? '-' }}</td>
                                <td class="py-3.5 px-4">
                                    <div class="font-mono text-blue-600 font-bold">{{ $tugas->faktur->no_faktur ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-600">{{ $tugas->faktur->customer->nama_customer ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-[11px] font-mono">{{ $tugas->tanggal_tugas->format('d/m/Y') }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        ✓ Selesai Lunas
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($tugas->pembayaran->isNotEmpty())
                                        @foreach($tugas->pembayaran as $kw)
                                            <a href="{{ route('keuangan.inkaso.kwitansi.cetak', $kw->id) }}" target="_blank"
                                               class="text-[10px] font-bold text-blue-700 underline block">
                                                {{ $kw->no_kwitansi }} (Rp {{ number_format($kw->nominal_bayar, 0, ',', '.') }})
                                            </a>
                                        @endforeach
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
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

        <!-- ============================================== -->
        <!-- MODAL 1: FORM ASSIGN PETUGAS INKASO           -->
        <!-- ============================================== -->
        <div x-cloak x-show="showAssignModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative" @click.outside="showAssignModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <span>🧑‍💼</span>
                        <span>TUGASKAN PETUGAS INKASO</span>
                    </h3>
                    <button type="button" @click="showAssignModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>

                <form action="{{ route('keuangan.inkaso.assign') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="faktur_id" :value="assignFakturId">

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nomor Faktur:</span>
                            <span class="font-mono font-bold text-blue-700" x-text="assignFakturNo"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Customer:</span>
                            <span class="font-bold text-slate-800" x-text="assignCustomer"></span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-slate-200">
                            <span class="text-slate-500">Sisa Tagihan:</span>
                            <span class="font-mono font-black text-rose-600">Rp <span x-text="formatRupiah(assignPiutang)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Petugas Inkaso Lapangan <span class="text-rose-500">*</span></label>
                        <select name="petugas_id" required class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-amber-500">
                            <option value="">-- Pilih Petugas --</option>
                            @foreach($petugasInkaso as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ ucfirst($p->role) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Mulai Penugasan <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_tugas" value="{{ date('Y-m-d') }}" required
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Penugasan / Instruksi Khusus</label>
                        <textarea name="catatan" rows="2" placeholder="Contoh: Tagihkan sisa pelunasan ke bagian keuangan Mayasari..."
                                  class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-amber-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showAssignModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-xs transition">
                            Terbitkan Surat Tugas ➔
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL 2: FORM INPUT SETORAN INKASO            -->
        <!-- ============================================== -->
        <div x-cloak x-show="showSetoranModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative" @click.outside="showSetoranModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <span>💰</span>
                        <span>INPUT SETORAN INKASO KE KAS</span>
                    </h3>
                    <button type="button" @click="showSetoranModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>

                <form action="{{ route('keuangan.inkaso.setoran.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="faktur_id" :value="setoranFakturId">
                    <input type="hidden" name="penugasan_inkaso_id" :value="setoranTugasId">

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nomor Faktur:</span>
                            <span class="font-mono font-bold text-blue-700" x-text="setoranFakturNo"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Customer:</span>
                            <span class="font-bold text-slate-800" x-text="setoranCustomer"></span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-slate-200">
                            <span class="text-slate-500">Sisa Piutang Berjalan:</span>
                            <span class="font-mono font-black text-rose-600">Rp <span x-text="formatRupiah(setoranSisaPiutang)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Setoran Diterima (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="nominal_setoran" x-model="nominalSetoran" :max="setoranSisaPiutang" min="1" step="any" required
                               class="w-full text-sm font-mono font-black rounded-xl border border-emerald-400 px-3 py-2.5 bg-emerald-50/50 focus:ring-2 focus:ring-emerald-500 text-slate-900">
                        <span class="text-[10px] text-slate-500 mt-1 block">
                            Maksimal setoran: Rp <span x-text="formatRupiah(setoranSisaPiutang)"></span>
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Terima Kas</label>
                            <input type="date" name="tanggal_bayar" value="{{ date('Y-m-d') }}" required
                                   class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Metode</label>
                            <select name="metode_bayar" required class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500">
                                <option value="tunai">Uang Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Setoran</label>
                        <input type="text" name="catatan" placeholder="Catatan bukti setoran lapangan..."
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-[11px] text-emerald-800">
                        💡 <strong>Otomatisasi Sistem:</strong> Setoran ini akan langsung memotong sisa piutang faktur secara real-time dan mencatat mutasi <strong>Kas Masuk</strong> pada Buku Kas.
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showSetoranModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                            Simpan &amp; Masukkan Kas ➔
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL 3: FORM LAPORAN HASIL PENUGASAN LAPANGAN -->
        <!-- ============================================== -->
        <div x-cloak x-show="showLaporanModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 relative max-h-[90vh] overflow-y-auto" @click.outside="showLaporanModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                            <span>📝</span>
                            <span>LAPORAN HASIL PENUGASAN INKASO</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Catatan administrasi hasil kunjungan lapangan oleh Staf Keuangan (Bu Ulfa)</p>
                    </div>
                    <button type="button" @click="showLaporanModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>

                <form :action="'{{ url('/keuangan/inkaso') }}/' + laporanTugasId + '/laporan-hasil'" method="POST" class="mt-4 space-y-4">
                    @csrf

                    <!-- Info Ringkas Tugas -->
                    <div class="p-3.5 rounded-xl bg-purple-50/60 border border-purple-200 text-xs space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nomor Tugas:</span>
                            <span class="font-mono font-bold text-amber-900" x-text="laporanNoTugas"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Petugas Inkaso:</span>
                            <span class="font-bold text-slate-800" x-text="laporanPetugasNama"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">No. Faktur / Klien:</span>
                            <span class="font-bold text-slate-800"><span class="font-mono text-blue-700" x-text="laporanFakturNo"></span> &mdash; <span x-text="laporanCustomer"></span></span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-purple-200/80">
                            <span class="text-slate-600 font-semibold">Total Kewajiban Piutang:</span>
                            <span class="font-mono font-black text-rose-600">Rp <span x-text="formatRupiah(laporanSisaPiutang)"></span></span>
                        </div>
                    </div>

                    <!-- Pilihan Hasil Kunjungan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Hasil Kunjungan Lapangan <span class="text-rose-500">*</span></label>
                        <select name="hasil_kunjungan" x-model="hasilKunjungan" required
                                class="w-full text-xs font-bold rounded-xl border border-purple-300 px-3 py-2.5 bg-white text-purple-950 focus:ring-2 focus:ring-purple-500 shadow-2xs">
                            <option value="janji_bayar">📅 Janji Bayar — Klien belum bayar, ada komitmen tanggal tertentu</option>
                            <option value="cicilan">💰 Bayar Sebagian (Cicilan) — Klien membayar cicilan/sebagian hari ini</option>
                            <option value="lunas">✓ Bayar Lunas — Klien melunasi seluruh sisa piutang 100%</option>
                            <option value="belum_bayar">✕ Belum Ada Pembayaran — Klien belum bisa bayar saat dikunjungi</option>
                            <option value="tidak_bertemu">👤 Tidak Bertemu / Toko Tutup — Petugas tidak bertemu penanggungjawab</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Tanggal Lapor -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Kunjungan / Lapor <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_lapor" x-model="tanggalLapor" required
                                   class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-purple-500">
                        </div>

                        <!-- Tanggal Janji Bayar (Muncul Khusus Janji Bayar) -->
                        <div x-show="hasilKunjungan === 'janji_bayar'">
                            <label class="block text-xs font-bold text-amber-800 mb-1">Tanggal Janji Bayar <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_janji_bayar" x-model="tanggalJanjiBayar" :required="hasilKunjungan === 'janji_bayar'"
                                   class="w-full text-xs font-bold rounded-xl border border-amber-400 bg-amber-50/50 px-3 py-2 focus:ring-2 focus:ring-amber-500 text-amber-950">
                        </div>

                        <!-- Nominal Diterima (Muncul saat Cicilan / Lunas) -->
                        <div x-show="hasilKunjungan === 'cicilan' || hasilKunjungan === 'lunas'" class="sm:col-span-2">
                            <label class="block text-xs font-bold text-emerald-800 mb-1">Nominal Setoran Diterima (Rp) <span class="text-rose-500">*</span></label>
                            <input type="number" name="nominal_diterima" x-model="nominalDiterima" :max="laporanSisaPiutang" min="0" step="any"
                                   class="w-full text-sm font-mono font-black rounded-xl border border-emerald-400 px-3 py-2.5 bg-emerald-50/50 focus:ring-2 focus:ring-emerald-500 text-slate-900">
                            <span class="text-[10px] text-slate-500 mt-0.5 block">
                                Uang tunai/transfer yang disetor petugas ke kasir (Maksimal: Rp <span x-text="formatRupiah(laporanSisaPiutang)"></span>)
                            </span>
                        </div>
                    </div>

                    <!-- Keterangan / Catatan Bu Ulfa -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Rincian dari Petugas <span class="text-rose-500">*</span></label>
                        <textarea name="keterangan_hasil" x-model="keteranganHasil" rows="3" required
                                  placeholder="Contoh: Klien minta tempo pembayaran tanggal 5 Desember setelah transfer dana dari kantor cabang, sudah konfirmasi dengan manajer..."
                                  class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-purple-500"></textarea>
                    </div>

                    <div class="p-3 rounded-xl bg-purple-50 border border-purple-200 text-[11px] text-purple-900 leading-relaxed">
                        📌 <strong>Catatan Keuangan:</strong> Laporan ini akan tersimpan rapi pada tab <em>"3. Laporan Hasil Penugasan"</em> dan dapat dicetak sebagai bukti audit penagihan tanpa mengotori buku besar piutang.
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showLaporanModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                            Simpan Laporan Hasil ➔
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
