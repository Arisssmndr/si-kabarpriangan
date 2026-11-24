<x-app-layout>
    <script>
    function inkasoManager() {
        return {
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
            assignSelectedIds: [],
            assignPetugasId: '{{ $petugasInkaso->first()->id ?? '' }}',
            assignPetugasNama: '{{ $petugasInkaso->first()->name ?? 'Petugas Inkaso' }}',
            isManualPetugas: false,
            manualPetugasNama: '',

            openLaporan(tugasId, noTugas, fakturNo, customer, sisaPiutang, petugas, hasil = 'cicilan', ket = '', tglJanji = '', tglLapor = '') {
                this.laporanTugasId = tugasId;
                this.laporanNoTugas = noTugas;
                this.laporanFakturNo = fakturNo;
                this.laporanCustomer = customer;
                this.laporanSisaPiutang = parseFloat(sisaPiutang) || 0;
                this.laporanPetugasNama = petugas || '';
                this.hasilKunjungan = hasil || 'cicilan';
                this.tanggalLapor = tglLapor || '{{ date('Y-m-d') }}';
                this.tanggalJanjiBayar = tglJanji || '';
                this.keteranganHasil = ket || '';
                this.showLaporanModal = true;
            },
            openAssign(id, noFaktur, customer, piutang, currentPetugasId = '') {
                this.assignFakturId = id;
                this.assignFakturNo = noFaktur;
                this.assignCustomer = customer;
                this.assignPiutang = parseFloat(piutang) || 0;
                if (!this.assignSelectedIds.includes(id)) {
                    this.assignSelectedIds = [id];
                }
                if (currentPetugasId) {
                    this.assignPetugasId = currentPetugasId;
                } else if ('{{ $petugasInkaso->first()->id ?? '' }}') {
                    this.assignPetugasId = '{{ $petugasInkaso->first()->id ?? '' }}';
                }
                this.showAssignModal = true;
            },
            openAssignBulk() {
                if (this.assignSelectedIds.length === 0) return;
                const firstId = this.assignSelectedIds[0];
                const firstCb = document.querySelector(`.cb-antrean-faktur[value="${firstId}"]`);
                this.assignFakturId = firstId;
                this.assignFakturNo = firstCb ? (firstCb.getAttribute('data-nofaktur') || `Faktur #${firstId}`) : `Faktur #${firstId}`;
                this.assignCustomer = firstCb ? (firstCb.getAttribute('data-customer') || '-') : '-';
                this.assignPiutang = firstCb ? (parseFloat(firstCb.getAttribute('data-piutang')) || 0) : 0;
                if ('{{ $petugasInkaso->first()->id ?? '' }}') {
                    this.assignPetugasId = '{{ $petugasInkaso->first()->id ?? '' }}';
                }
                this.showAssignModal = true;
            },
            toggleAssignModalFaktur(id) {
                if (this.assignSelectedIds.includes(id)) {
                    if (this.assignSelectedIds.length > 1) {
                        this.assignSelectedIds = this.assignSelectedIds.filter(v => v !== id);
                    }
                } else {
                    if (this.assignSelectedIds.length >= 10) {
                        alert('Maksimal 10 faktur untuk 1 Surat Tugas Inkaso!');
                        return;
                    }
                    this.assignSelectedIds.push(id);
                }
            },
            onCheckAntreanRow(cb, id) {
                if (cb.checked) {
                    if (this.assignSelectedIds.length >= 10) {
                        cb.checked = false;
                        alert('Maksimal 10 faktur yang dapat dipilih untuk 1 Surat Penugasan Inkaso.');
                        return;
                    }
                    if (!this.assignSelectedIds.includes(id)) {
                        this.assignSelectedIds.push(id);
                    }
                } else {
                    this.assignSelectedIds = this.assignSelectedIds.filter(v => v !== id);
                }
            },
            setAssignPetugasPill(nama) {
                this.assignPetugasNama = nama;
                const sel = document.getElementById('assignSelectPetugas');
                if (sel) {
                    const standardOptions = ['Ahmad Supardi', 'Rudi Hermawan', 'Dadan Ramdani', 'Yudi Permana'];
                    if (standardOptions.includes(nama)) {
                        sel.value = nama;
                        this.isManualPetugas = false;
                        this.manualPetugasNama = '';
                    } else if (nama === '__blank__') {
                        sel.value = '__blank__';
                        this.isManualPetugas = false;
                        this.manualPetugasNama = '';
                    } else {
                        let found = false;
                        for (let i = 0; i < sel.options.length; i++) {
                            if (sel.options[i].value === nama) {
                                sel.value = nama;
                                found = true;
                                break;
                            }
                        }
                        if (!found) {
                            sel.value = '__manual__';
                            this.isManualPetugas = true;
                            this.manualPetugasNama = nama;
                        } else {
                            this.isManualPetugas = false;
                        }
                    }
                }
            },
            handleAssignPetugasChange(val) {
                if (val === '__manual__') {
                    this.isManualPetugas = true;
                    this.assignPetugasNama = this.manualPetugasNama;
                } else if (val === '__blank__') {
                    this.isManualPetugas = false;
                    this.assignPetugasNama = '';
                    this.manualPetugasNama = '';
                } else {
                    this.isManualPetugas = false;
                    this.assignPetugasNama = val;
                    this.manualPetugasNama = '';
                }
            },
            openSetoran(fakturId, tugasId, noFaktur, customer, sisaPiutang, defaultNominal = null) {
                this.setoranFakturId = fakturId;
                this.setoranTugasId = tugasId;
                this.setoranFakturNo = noFaktur;
                this.setoranCustomer = customer;
                this.setoranSisaPiutang = parseFloat(sisaPiutang) || 0;
                this.nominalSetoran = (defaultNominal !== null && defaultNominal !== undefined && parseFloat(defaultNominal) > 0)
                    ? parseFloat(defaultNominal)
                    : '';
                this.showSetoranModal = true;
            },
            formatRupiah(number) {
                const n = parseFloat(number) || 0;
                return new Intl.NumberFormat('id-ID').format(n);
            },
            matchesSearch(...terms) {
                if (!this.searchQuery || this.searchQuery.trim() === '') return true;
                const q = this.searchQuery.toLowerCase().trim();
                return terms.some(t => t && t.toString().toLowerCase().includes(q));
            }
        };
    }
    window.inkasoManager = inkasoManager;
    if (window.Alpine) {
        window.Alpine.data('inkasoManager', inkasoManager);
    } else {
        document.addEventListener('alpine:init', () => {
            window.Alpine.data('inkasoManager', inkasoManager);
        });
    }
    </script>

    <div class="space-y-6" x-data="inkasoManager()">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <h1 class="text-xl font-extrabold text-[#0E527B] tracking-tight">
                        PENAGIHAN LAPANGAN (INKASO)
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Antrean faktur piutang, penugasan petugas lapangan, cetak surat penagihan, dan monitoring koleksi
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

                <div class="sm:col-span-4">
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

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">
                        Wilayah Priangan
                    </label>
                    <select name="wilayah" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-amber-500 focus:outline-hidden">
                        <option value="">Semua Wilayah</option>
                        <option value="Kota Tasikmalaya" {{ request('wilayah') === 'Kota Tasikmalaya' ? 'selected' : '' }}>Kota Tasikmalaya</option>
                        <option value="Kab. Tasikmalaya" {{ request('wilayah') === 'Kab. Tasikmalaya' ? 'selected' : '' }}>Kab. Tasikmalaya</option>
                        <option value="Ciamis" {{ request('wilayah') === 'Ciamis' ? 'selected' : '' }}>Kab. Ciamis</option>
                        <option value="Banjar" {{ request('wilayah') === 'Banjar' ? 'selected' : '' }}>Kota Banjar</option>
                        <option value="Garut" {{ request('wilayah') === 'Garut' ? 'selected' : '' }}>Kab. Garut</option>
                        <option value="Pangandaran" {{ request('wilayah') === 'Pangandaran' ? 'selected' : '' }}>Kab. Pangandaran</option>
                        <option value="Sumedang" {{ request('wilayah') === 'Sumedang' ? 'selected' : '' }}>Kab. Sumedang</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">
                        Status / Petugas
                    </label>
                    <select name="status_petugas" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-amber-500 focus:outline-hidden">
                        <option value="">Semua Status</option>
                        <option value="belum" {{ request('status_petugas') === 'belum' ? 'selected' : '' }}>⚠️ Belum Ditugaskan</option>
                        <option value="sudah" {{ request('status_petugas') === 'sudah' ? 'selected' : '' }}>🧑‍💼 Sudah Ada Petugas</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="w-full h-10 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>Cari Faktur</span>
                    </button>
                    @if(request()->hasAny(['q', 'kategori', 'wilayah', 'status_petugas', 'petugas_id']))
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
                <span>3. Laporan Hasil Penugasan </span>
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
        <div x-show="activeTab === 'antrean'" class="space-y-6">
            @php
                $fakturBelumTugas = $antreanPiutang->filter(fn($f) => !$f->penugasanAktif);
                $fakturSudahTugas = $antreanPiutang->filter(fn($f) => $f->penugasanAktif)->groupBy(fn($f) => $f->penugasanAktif->petugas_nama);
            @endphp

            <!-- 1. BAGIAN: ANTREAN BELUM DITUGASKAN -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-amber-50/40">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                                1. Antrean Belum Ditugaskan (Menanti Petugas Inkaso)
                            </h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                {{ $fakturBelumTugas->count() }} Faktur
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Centang faktur yang ingin ditugaskan (maksimal 10 faktur per surat tugas) lalu klik tombol Tugaskan
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" 
                                x-show="assignSelectedIds.length > 0"
                                @click.stop="openAssignBulk()"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-amber-950 border border-amber-500/30 text-xs font-black shadow-xs transition cursor-pointer">
                            <span>+ Buat Surat Tugas Inkaso (<span x-text="assignSelectedIds.length"></span>/10 Faktur) ➔</span>
                        </button>
                        <div class="relative w-full sm:w-60">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                🔍
                            </span>
                            <input type="text" x-model="searchQuery" placeholder="Filter no. faktur cepat..." 
                                   class="w-full text-xs rounded-xl border border-slate-200 bg-white pl-8 pr-7 py-1.5 focus:ring-2 focus:ring-amber-500 focus:outline-hidden">
                            <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 text-xs font-bold">
                                ✕
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-3 text-center w-10">Pilih</th>
                                <th class="py-3 px-4">No. Faktur</th>
                                <th class="py-3 px-4">Customer</th>
                                <th class="py-3 px-4">Kanal</th>
                                <th class="py-3 px-4">Jatuh Tempo</th>
                                <th class="py-3 px-4 text-right">Total Tagihan</th>
                                <th class="py-3 px-4 text-right">Sisa Piutang</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Aksi Penugasan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($fakturBelumTugas as $faktur)
                                <tr x-show="matchesSearch('{{ $faktur->no_faktur }}', '{{ addslashes($faktur->customer->nama_customer ?? '') }}', '{{ $faktur->kategori }}')"
                                    class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3 text-center">
                                        <input type="checkbox"
                                               class="cb-antrean-faktur rounded border-slate-300 text-amber-600 focus:ring-amber-500 cursor-pointer"
                                               value="{{ $faktur->id }}"
                                               data-nofaktur="{{ $faktur->no_faktur }}"
                                               data-customer="{{ $faktur->customer->nama_customer ?? 'UMUM' }}"
                                               data-piutang="{{ (float) $faktur->sisa_piutang }}"
                                               :checked="assignSelectedIds.includes({{ $faktur->id }})"
                                               @change="onCheckAntreanRow($event.target, {{ $faktur->id }})"
                                               title="Centang untuk gabung ke Surat Tugas Inkaso (Maks. 10 Faktur)">
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                        <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" class="text-blue-600 hover:underline">
                                            {{ $faktur->no_faktur }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900">{{ $faktur->customer->nama_customer ?? '-' }}</div>
                                        <div class="text-[10px] text-slate-400 flex items-center gap-1.5 flex-wrap">
                                            @if(!empty($faktur->customer->kota))
                                                <span class="inline-block bg-slate-100 text-slate-700 px-1.5 py-0.2 rounded font-semibold text-[9px] border border-slate-200">
                                                    📍 {{ $faktur->customer->kota }}
                                                </span>
                                            @endif
                                            <span>{{ $faktur->customer->no_telepon ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $faktur->kategori === 'iklan' ? 'bg-sky-50 text-sky-700' : 'bg-indigo-50 text-indigo-700' }}">
                                            {{ $faktur->kategori }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-[11px] font-mono text-rose-600 font-bold">
                                        {{ $faktur->jatuh_tempo ? $faktur->jatuh_tempo->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-semibold">
                                        Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono">
                                        <div class="font-black text-rose-600">Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}</div>
                                        @if(($faktur->total_terbayar ?? 0) > 0)
                                            <div class="text-[10px] text-emerald-700 font-bold mt-0.5">
                                                ✓ Cicilan Masuk: Rp {{ number_format($faktur->total_terbayar, 0, ',', '.') }}
                                                <span class="text-slate-400">({{ $faktur->pembayaran->count() }}x)</span>
                                            </div>
                                        @endif
                                        @php
                                            $tugasLama = $faktur->penugasanInkaso ? $faktur->penugasanInkaso->sortByDesc('id')->first() : null;
                                        @endphp
                                        @if($tugasLama && ($tugasLama->keterangan_hasil || $tugasLama->hasil_kunjungan))
                                            <div class="text-[9px] text-purple-700 italic truncate max-w-[190px] ml-auto mt-0.5 font-sans" title="Catatan Terakhir: {{ $tugasLama->keterangan_hasil ?: $tugasLama->hasil_kunjungan }} ({{ $tugasLama->petugas_nama }})">
                                                "{{ $tugasLama->keterangan_hasil ?: $tugasLama->hasil_kunjungan }}"
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                            ⏳ Belum Ditugaskan
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                            <a href="{{ route('keuangan.faktur.cetak', $faktur->id) }}" target="_blank"
                                               class="px-2.5 py-1.5 rounded-xl border border-blue-200 bg-blue-50/70 hover:bg-blue-100 text-blue-700 font-bold text-xs transition inline-flex items-center gap-1 shadow-2xs"
                                               title="Cetak Lembar Faktur Tagihan Asli">
                                                <span>🖨 Faktur</span>
                                            </a>
                                            <button type="button" 
                                                    @click.stop="openAssign({{ (int)$faktur->id }}, '{{ addslashes($faktur->no_faktur ?? '') }}', '{{ addslashes($faktur->customer->nama_customer ?? '') }}', {{ (float)($faktur->sisa_piutang ?? 0) }})"
                                                    class="px-3 py-1.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-amber-950 font-black text-xs transition cursor-pointer shadow-2xs border border-amber-500/30">
                                                + Tugaskan
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-8 text-center text-slate-400">
                                        <div class="text-2xl mb-1">🎉</div>
                                        <p class="text-xs font-bold text-slate-700">Tidak ada faktur yang menanti penugasan.</p>
                                        <p class="text-[11px] text-slate-400">Semua faktur piutang aktif telah berhasil dikelompokkan ke petugas inkaso di bawah.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. BAGIAN: FAKTUR SUDAH DITUGASKAN (TERKELOMPOK PER PETUGAS) -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                            <span>2. Faktur Sudah Ditugaskan ke Inkaso (Terkelompok per Petugas)</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Faktur langsung dikelompokkan berdasarkan nama petugas inkaso lapangan untuk kemudahan cetak surat tugas gabungan
                        </p>
                    </div>
                </div>

                @forelse($fakturSudahTugas as $petugasNama => $groupFakturs)
                    @php
                        $firstTugasId = $groupFakturs->first()->penugasanAktif->id ?? null;
                        $allFakturIds = $groupFakturs->pluck('id')->join(',');
                        $totalGroupPiutang = $groupFakturs->sum('sisa_piutang');
                        $searchGroupTerms = $petugasNama . ' ' . $groupFakturs->pluck('no_faktur')->join(' ') . ' ' . $groupFakturs->map(fn($f) => $f->customer->nama_customer ?? '')->join(' ');
                    @endphp
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden"
                         x-show="matchesSearch('{{ addslashes($searchGroupTerms) }}')">
                        <!-- Group Header Card -->
                        <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-black text-sm shadow-xs shrink-0">
                                    {{ strtoupper(substr($petugasNama, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm font-extrabold text-slate-900">{{ $petugasNama }}</h4>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                            {{ $groupFakturs->count() }} Faktur Ditugaskan
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-2">
                                        <span>Total Target Piutang:</span>
                                        <span class="font-mono font-black text-rose-600">Rp {{ number_format($totalGroupPiutang, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($firstTugasId)
                                    <a href="{{ route('keuangan.inkaso.surat-tugas.cetak', ['id' => $firstTugasId, 'faktur_ids' => $allFakturIds, 'petugas' => $petugasNama]) }}" target="_blank"
                                       class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition inline-flex items-center gap-1.5 shadow-xs">
                                        <span>🖨 Cetak Surat Tugas Kelompok ({{ $groupFakturs->count() }} Faktur)</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Sub-Table Faktur di dalam Petugas Ini -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-600">
                                <thead class="bg-slate-50/50 text-[10px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-100">
                                    <tr>
                                        <th class="py-2.5 px-4">No. Faktur</th>
                                        <th class="py-2.5 px-4">Customer &amp; Wilayah</th>
                                        <th class="py-2.5 px-4">Kanal</th>
                                        <th class="py-2.5 px-4">Jatuh Tempo</th>
                                        <th class="py-2.5 px-4 text-right">Total Tagihan</th>
                                        <th class="py-2.5 px-4 text-right">Sisa Piutang</th>
                                        <th class="py-2.5 px-4 text-center">No. Tugas</th>
                                        <th class="py-2.5 px-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    @foreach($groupFakturs as $faktur)
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="py-3 px-4 font-mono font-bold text-blue-600">
                                                <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" class="hover:underline">
                                                    {{ $faktur->no_faktur }}
                                                </a>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="font-bold text-slate-900">{{ $faktur->customer->nama_customer ?? '-' }}</div>
                                                <div class="text-[10px] text-slate-400 flex items-center gap-1">
                                                    @if(!empty($faktur->customer->kota))
                                                        <span class="text-slate-600">📍 {{ $faktur->customer->kota }}</span>
                                                    @endif
                                                    <span>{{ $faktur->customer->no_telepon ?? '' }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $faktur->kategori === 'iklan' ? 'bg-sky-50 text-sky-700' : 'bg-indigo-50 text-indigo-700' }}">
                                                    {{ $faktur->kategori }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-[11px] font-mono text-slate-600">
                                                {{ $faktur->jatuh_tempo ? $faktur->jatuh_tempo->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="py-3 px-4 text-right font-mono">
                                                Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-4 text-right font-black text-rose-600 font-mono">
                                                Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-4 text-center font-mono text-[11px] text-slate-500">
                                                {{ $faktur->penugasanAktif->no_tugas ?? '-' }}
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <div class="flex items-center justify-center gap-1 flex-wrap">
                                                    <a href="{{ route('keuangan.faktur.cetak', $faktur->id) }}" target="_blank"
                                                       class="px-2 py-1 rounded-lg border border-blue-200 bg-blue-50/70 hover:bg-blue-100 text-blue-700 font-bold text-[10px] transition inline-flex items-center gap-0.5 shadow-2xs"
                                                       title="Cetak Lembar Faktur Tagihan Asli">
                                                        🖨 Faktur
                                                    </a>
                                                    <a href="{{ route('keuangan.inkaso.surat-tugas.cetak', $faktur->penugasanAktif->id) }}" target="_blank"
                                                       class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-[10px] transition"
                                                       title="Cetak Surat Tugas Faktur Ini Saja">
                                                        📄 Tugas
                                                    </a>
                                                    <button type="button" 
                                                            @click.stop="openAssign({{ (int)$faktur->id }}, '{{ addslashes($faktur->no_faktur ?? '') }}', '{{ addslashes($faktur->customer->nama_customer ?? '') }}', {{ (float)($faktur->sisa_piutang ?? 0) }}, '{{ addslashes($faktur->penugasanAktif->petugas_nama ?? '') }}')"
                                                            class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] transition cursor-pointer"
                                                            title="Ubah Petugas Penagihan">
                                                        Ubah
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 text-center text-slate-400">
                        <p class="text-xs">Belum ada faktur yang ditugaskan ke petugas inkaso.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 2: PENUGASAN AKTIF LAPANGAN (TERKELOMPOK PER PETUGAS) -->
        <div x-show="activeTab === 'berjalan'" class="space-y-6">
            @php
                $penugasanGrouped = $penugasanBerjalan->groupBy(fn($t) => $t->petugas_nama);
            @endphp

            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        <span>Penugasan Aktif Lapangan (Terkelompok per Petugas)</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Faktur aktif dikelompokkan per Petugas Inkaso. Anda dapat mencetak surat tugas gabungan atau menginput laporan hasil &amp; setoran kas per faktur.
                    </p>
                </div>
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                        🔍
                    </span>
                    <input type="text" x-model="searchQuery" placeholder="Filter no. faktur / tugas..." 
                           class="w-full text-xs rounded-xl border border-slate-200 bg-white pl-8 pr-7 py-1.5 focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                    <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 text-xs font-bold">
                        ✕
                    </button>
                </div>
            </div>

            @forelse($penugasanGrouped as $petugasNama => $tasks)
                @php
                    $firstTask = $tasks->first();
                    $allFakturIds = $tasks->pluck('faktur_id')->join(',');
                    $totalGroupPiutang = $tasks->sum(fn($t) => $t->faktur->sisa_piutang ?? 0);
                    $searchTerms = $petugasNama . ' ' . $tasks->pluck('no_tugas')->join(' ') . ' ' . $tasks->map(fn($t) => ($t->faktur->no_faktur ?? '') . ' ' . ($t->faktur->customer->nama_customer ?? ''))->join(' ');
                @endphp
                <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden"
                     x-show="matchesSearch('{{ addslashes($searchTerms) }}')">
                    <!-- Group Header Card -->
                    <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-black text-sm shadow-xs shrink-0">
                                {{ strtoupper(substr($petugasNama, 0, 2)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-sm font-extrabold text-slate-900">{{ $petugasNama }}</h4>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                        {{ $tasks->count() }} Penugasan Aktif
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-2">
                                    <span>Total Target Piutang:</span>
                                    <span class="font-mono font-black text-rose-600">Rp {{ number_format($totalGroupPiutang, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($firstTask)
                                <a href="{{ route('keuangan.inkaso.surat-tugas.cetak', ['id' => $firstTask->id, 'faktur_ids' => $allFakturIds, 'petugas' => $petugasNama]) }}" target="_blank"
                                   class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition inline-flex items-center gap-1.5 shadow-xs">
                                    <span>🖨 Cetak Surat Tugas Kelompok ({{ $tasks->count() }} Faktur)</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Sub-Table Faktur Aktif di bawah Petugas Ini -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50/60 text-[10px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-100">
                                <tr>
                                    <th class="py-2.5 px-4">No. Tugas</th>
                                    <th class="py-2.5 px-4">No. Faktur &amp; Klien</th>
                                    <th class="py-2.5 px-4">Tanggal Tugas</th>
                                    <th class="py-2.5 px-4 text-right">Target Piutang</th>
                                    <th class="py-2.5 px-4 text-center">Dokumen Tugas</th>
                                    <th class="py-2.5 px-4 text-center">{{ auth()->user()->hasRole('kasir', 'keuangan', 'admin_keuangan', 'administrator') ? 'Aksi Setoran Kas' : 'Aksi Laporan Tugas' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach($tasks as $tugas)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-3 px-4 font-mono font-bold text-amber-800">
                                            {{ $tugas->no_tugas }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-mono font-bold text-blue-600">
                                                <a href="{{ route('keuangan.faktur.show', $tugas->faktur_id) }}" class="hover:underline">
                                                    {{ $tugas->faktur->no_faktur ?? '-' }}
                                                </a>
                                            </div>
                                            <div class="text-[11px] text-slate-800 mt-0.5 font-bold">{{ $tugas->faktur->customer->nama_customer ?? '-' }}</div>
                                            @if(!empty($tugas->faktur->customer->kota))
                                                <div class="text-[10px] text-slate-500">📍 {{ $tugas->faktur->customer->kota }}</div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-[11px] font-mono text-slate-600">
                                            {{ $tugas->tanggal_tugas->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-black text-rose-600 font-mono">
                                            Rp {{ number_format($tugas->faktur->sisa_piutang ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <a href="{{ route('keuangan.faktur.cetak', $tugas->faktur_id) }}" target="_blank"
                                                   class="px-2 py-1 rounded-lg border border-blue-200 bg-blue-50/70 hover:bg-blue-100 text-blue-700 text-[10px] font-bold inline-flex items-center gap-0.5 transition shadow-2xs"
                                                   title="Cetak Faktur Tagihan Asli Beserta Riwayat Pembayaran">
                                                    🖨 Faktur
                                                </a>
                                                <a href="{{ route('keuangan.inkaso.surat-tugas.cetak', $tugas->id) }}" target="_blank"
                                                   class="px-2 py-1 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-700 text-[10px] font-bold inline-flex items-center gap-0.5 transition"
                                                   title="Cetak Surat Tugas Faktur Ini Saja">
                                                    📄 Tugas
                                                </a>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                                <button type="button" 
                                                        @click.stop="openLaporan({{ (int)$tugas->id }}, '{{ addslashes($tugas->no_tugas ?? '') }}', '{{ addslashes($tugas->faktur->no_faktur ?? '') }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '') }}', {{ (float)($tugas->faktur->sisa_piutang ?? 0) }}, '{{ addslashes($tugas->petugas_nama ?? '') }}', '{{ $tugas->hasil_kunjungan ?: 'cicilan' }}', '{{ addslashes($tugas->keterangan_hasil ?? '') }}', '{{ $tugas->tanggal_janji_bayar?->format('Y-m-d') ?? '' }}', '{{ $tugas->tanggal_lapor?->format('Y-m-d') ?? '' }}')"
                                                        class="px-2.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-[11px] transition cursor-pointer shadow-2xs inline-flex items-center gap-1"
                                                        title="Input Laporan Hasil Kunjungan Lapangan">
                                                    <span class="pointer-events-none">📝 Lapor Hasil</span>
                                                </button>
                                                @if(auth()->user()->hasRole('kasir', 'admin_keuangan', 'administrator'))
                                                    <button type="button" 
                                                            @click.stop="openSetoran({{ (int)$tugas->faktur_id }}, {{ (int)$tugas->id }}, '{{ addslashes($tugas->faktur->no_faktur ?? '') }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '') }}', {{ (float)($tugas->faktur->sisa_piutang ?? 0) }})"
                                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition cursor-pointer shadow-2xs inline-flex items-center gap-1"
                                                            title="Input Penerimaan Uang Kas (Kasir)">
                                                        <span class="pointer-events-none">💰 Setor Kas</span>
                                                    </button>
                                                @endif
                                            </div>
                                            @if($tugas->hasil_kunjungan)
                                                <div class="mt-1">
                                                    @if($tugas->hasil_kunjungan === 'janji_bayar')
                                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-bold text-[10px]">
                                                             Janji: {{ $tugas->tanggal_janji_bayar ? $tugas->tanggal_janji_bayar->format('d/m/Y') : '-' }}
                                                        </span>
                                                    @elseif($tugas->hasil_kunjungan === 'cicilan')
                                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 font-bold text-[10px]" title="{{ $tugas->keterangan_hasil }}">
                                                             Cicilan: {{ Str::limit($tugas->keterangan_hasil ?: 'Ada cicilan', 22) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center text-slate-400">
                    <div class="text-3xl mb-2">📋</div>
                    <p class="text-sm font-semibold text-slate-600">
                        @if(request('q'))
                            Tidak ada penugasan aktif yang cocok dengan "{{ request('q') }}".
                        @else
                            Tidak ada penugasan yang sedang berjalan.
                        @endif
                    </p>
                    <p class="text-xs text-slate-400 mt-1">Pilih tab "Antrean Faktur Piutang" untuk menugaskan petugas lapangan.</p>
                </div>
            @endforelse
        </div>

        <!-- TAB 3: LAPORAN HASIL PENUGASAN (CATATAN BU ULFA) -->
        <div x-show="activeTab === 'laporan'" class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <span>Laporan Hasil Setelah Penugasan Lapangan </span>
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
                                    {{ $lh->petugas_nama }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $lh->faktur->customer->nama_customer ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $lh->faktur->customer->kota ?? '-' }} ({{ $lh->faktur->customer->no_telepon ?? '-' }})</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-blue-600">
                                    <div class="flex items-center justify-between gap-1">
                                        <span>{{ $lh->faktur->no_faktur ?? '-' }}</span>
                                        @if($lh->faktur_id)
                                            <a href="{{ route('keuangan.faktur.cetak', $lh->faktur_id) }}" target="_blank"
                                               class="text-[10px] font-bold text-blue-700 hover:underline bg-blue-50 border border-blue-200 px-1.5 py-0.5 rounded font-sans inline-flex items-center gap-0.5 shadow-2xs"
                                               title="Cetak Lembar Faktur dengan Riwayat Angsuran">
                                                🖨 Faktur
                                            </a>
                                        @endif
                                    </div>
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
                                <td class="py-3.5 px-4 text-right font-mono">
                                    @if($lh->pembayaran->isNotEmpty())
                                        <div class="font-black text-emerald-700">Rp {{ number_format($lh->pembayaran->sum('nominal_bayar'), 0, ',', '.') }}</div>
                                        <span class="text-[9px] font-sans font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded inline-block mt-0.5">
                                            ✓ Masuk Kas
                                        </span>
                                    @elseif($lh->status_tugas === 'berjalan' && in_array($lh->hasil_kunjungan, ['cicilan', 'lunas']))
                                        <span class="text-[10px] font-sans font-bold text-amber-800 bg-amber-50 border border-amber-300 px-2 py-0.5 rounded inline-block animate-pulse">
                                            ⏳ Menanti Input Kasir
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-semibold">Rp 0</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-[11px] text-slate-700 max-w-xs">
                                    <div class="italic">"{{ $lh->keterangan_hasil ?: ($lh->catatan ?: '-') }}"</div>
                                    <div class="text-[9px] text-slate-400 mt-0.5">Dicatat: {{ $lh->pelapor->name ?? 'Staf Faktur & Piutang' }}</div>
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
                                        <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                            <a href="{{ route('keuangan.faktur.cetak', $lh->faktur_id) }}" target="_blank"
                                               class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-[10px] font-bold transition inline-flex items-center gap-1 shadow-2xs"
                                               title="Cetak Lembar Faktur untuk Diserahkan ke Kasir">
                                                🖨 Faktur
                                            </a>
                                            <button type="button" 
                                                    @click.stop="openLaporan({{ (int)$lh->id }}, '{{ addslashes($lh->no_tugas ?? '') }}', '{{ addslashes($lh->faktur->no_faktur ?? '') }}', '{{ addslashes($lh->faktur->customer->nama_customer ?? '') }}', {{ (float)($lh->faktur->sisa_piutang ?? 0) }}, '{{ addslashes($lh->petugas_nama ?? '') }}', '{{ $lh->hasil_kunjungan ?: 'cicilan' }}', '{{ addslashes($lh->keterangan_hasil ?? '') }}', '{{ $lh->tanggal_janji_bayar?->format('Y-m-d') ?? '' }}', '{{ $lh->tanggal_lapor?->format('Y-m-d') ?? '' }}')"
                                                    class="px-2 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-[10px] font-bold transition cursor-pointer"
                                                    title="Update Keterangan Lapangan">
                                                ✏️ Edit
                                            </button>
                                            @if(auth()->user()->hasRole('kasir', 'admin_keuangan', 'administrator'))
                                                <button type="button" 
                                                        @click.stop="openSetoran({{ (int)$lh->faktur_id }}, {{ (int)$lh->id }}, '{{ addslashes($lh->faktur->no_faktur ?? '') }}', '{{ addslashes($lh->faktur->customer->nama_customer ?? '') }}', {{ (float)($lh->faktur->sisa_piutang ?? 0) }})"
                                                        class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold transition cursor-pointer shadow-2xs inline-flex items-center gap-1"
                                                        title="Input Penerimaan Uang Kas (Kasir)">
                                                    <span>💰 Input Setoran Kas</span>
                                                </button>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 text-[10px] font-bold border border-amber-200"
                                                      title="Faktur diserahkan ke Kasir untuk penerimaan uang fisik dan pemotongan piutang">
                                                    ⏳ Faktur di Kasir
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">Selesai</span>
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
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="font-mono text-blue-600 font-bold">{{ $tugas->faktur->no_faktur ?? '-' }}</span>
                                        @if($tugas->faktur_id)
                                            <a href="{{ route('keuangan.faktur.cetak', $tugas->faktur_id) }}" target="_blank"
                                               class="text-[10px] font-bold text-emerald-800 hover:underline bg-emerald-50 border border-emerald-300 px-2 py-0.5 rounded inline-flex items-center gap-1 shadow-2xs font-sans"
                                               title="Cetak Faktur Lunas Beserta Riwayat Pembayaran Lengkap">
                                                🖨 Faktur Lunas
                                            </a>
                                        @endif
                                    </div>
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
        <div x-cloak x-show="showAssignModal" 
             @click.self="showAssignModal = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 relative max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                            <span>🧑‍💼</span>
                            <span>Tugaskan Petugas Inkaso &amp; Cetak Surat Tugas</span>
                        </h3>
                        <p class="text-[11px] text-slate-500">1 Surat Penugasan Inkaso dapat memuat hingga <strong>10 Faktur Piutang</strong></p>
                    </div>
                    <button type="button" @click="showAssignModal = false" class="text-slate-400 hover:text-slate-600 font-bold p-1 rounded-lg">✕</button>
                </div>

                <form action="{{ route('keuangan.inkaso.assign') }}" method="POST" class="mt-4 space-y-4" target="_blank" @submit="setTimeout(() => { showAssignModal = false; location.reload(); }, 600)">
                    @csrf
                    <input type="hidden" name="faktur_id" :value="assignFakturId">
                    <input type="hidden" name="faktur_ids" :value="assignSelectedIds.join(',')">
                    <input type="hidden" name="redirect_cetak" value="1">

                    <!-- Ringkasan Faktur Terpilih -->
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-amber-800 font-medium">Faktur Terpilih:</span>
                            <span class="font-mono font-bold text-slate-900" x-text="assignFakturNo"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-amber-800 font-medium">Customer:</span>
                            <span class="font-bold text-slate-900 truncate max-w-[240px]" x-text="assignCustomer"></span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-amber-200/70">
                            <span class="text-amber-800 font-semibold">Total Sisa Piutang:</span>
                            <span class="font-mono font-black text-rose-600">Rp <span x-text="formatRupiah(assignPiutang)"></span></span>
                        </div>
                    </div>

                    <!-- Checklist Tambah Faktur Lainnya (Maks 10 Faktur) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-slate-700">
                                Faktur Piutang yang Dibawa (Maks. 10 Faktur):
                            </label>
                            <span class="text-[11px] font-mono font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                <span x-text="assignSelectedIds.length || 1"></span> / 10 Faktur
                            </span>
                        </div>
                        <div class="border border-slate-200 rounded-xl bg-slate-50/70 p-2 space-y-1.5 overflow-y-auto max-h-36">
                            @foreach($antreanPiutang as $fOpt)
                                <label class="flex items-center justify-between gap-2 p-2 rounded-lg bg-white border border-slate-200 hover:border-blue-300 cursor-pointer text-xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <input type="checkbox"
                                               class="cb-assign-modal-faktur rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                               value="{{ $fOpt->id }}"
                                               data-nofaktur="{{ $fOpt->no_faktur }}"
                                               data-customer="{{ $fOpt->customer->nama_customer ?? 'UMUM' }}"
                                               data-piutang="{{ (float) $fOpt->sisa_piutang }}"
                                               @change="toggleAssignModalFaktur({{ $fOpt->id }})"
                                               :checked="assignSelectedIds.includes({{ $fOpt->id }})">
                                        <span class="font-mono font-bold text-blue-700">{{ $fOpt->no_faktur }}</span>
                                        <span class="text-slate-700 font-semibold truncate">{{ $fOpt->customer->nama_customer ?? 'UMUM' }}</span>
                                    </div>
                                    <span class="font-mono font-bold text-amber-700 shrink-0">Rp {{ number_format($fOpt->sisa_piutang, 0, ',', '.') }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Pilihan Petugas Penagih Lapangan (Inkaso dari User Database) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Pilih Petugas Penagih Lapangan (Inkaso): <span class="text-rose-500">*</span>
                        </label>
                        <select name="petugas_id" 
                                id="assignSelectPetugas"
                                x-model="assignPetugasId"
                                required
                                class="w-full h-10 px-3 rounded-xl border border-slate-300 text-xs text-slate-900 font-bold bg-white focus:ring-2 focus:ring-[#0E527B] focus:outline-hidden shadow-2xs cursor-pointer">
                            <option value="">-- Pilih Akun Petugas Inkaso --</option>
                            @foreach($petugasInkaso as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->email }})</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">
                            Faktur terpilih akan langsung terkirim ke antrean Surat Tugas pada layar HP Petugas Inkaso yang dipilih.
                        </p>
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
                        <button type="submit" class="px-5 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-amber-950 text-xs font-black shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                            <span>Buka &amp; Cetak Surat Tugas ➔</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if(auth()->user()->hasRole('kasir', 'admin_keuangan', 'administrator'))
        <!-- ============================================== -->
        <!-- MODAL 2: FORM INPUT SETORAN INKASO            -->
        <!-- ============================================== -->
        <div x-cloak x-show="showSetoranModal" 
             @click.self="showSetoranModal = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative">
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

                    <!-- Status Prediksi Pelunasan & Alur Serah Terima Faktur -->
                    <div class="p-3 rounded-xl border text-[11px] transition"
                         :class="(nominalSetoran && nominalSetoran >= setoranSisaPiutang) ? 'bg-emerald-50 border-emerald-300 text-emerald-900' : 'bg-amber-50 border-amber-300 text-amber-900'">
                        <div class="font-bold flex items-center justify-between">
                            <span>Kalkulasi Saldo Setelah Setoran:</span>
                            <span class="font-mono font-black" :class="(setoranSisaPiutang - (nominalSetoran || 0) <= 0) ? 'text-emerald-700' : 'text-rose-700'">
                                Sisa Piutang: Rp <span x-text="formatRupiah(Math.max(0, setoranSisaPiutang - (nominalSetoran || 0)))"></span>
                            </span>
                        </div>
                        <div class="mt-1.5 text-[11px] leading-relaxed">
                            <template x-if="nominalSetoran && nominalSetoran >= setoranSisaPiutang">
                                <span>✓ <strong>LUNAS 100%:</strong> Tagihan lunas sepenuhnya. Faktur & penugasan berpindah ke <strong>Tab 4: Riwayat Selesai Lunas</strong> dan kwitansi kas diterbitkan.</span>
                            </template>
                            <template x-if="!nominalSetoran || nominalSetoran < setoranSisaPiutang">
                                <span>↺ <strong>SETORAN CICILAN / SEBAGIAN:</strong> Setelah kasir menerima uang dan cetak kwitansi, penugasan putaran ini ditutup. Lembar faktur fisik yang diserahkan petugas <strong>otomatis kembali ke Antrean Faktur Piutang</strong> dengan sisa piutang yang sudah berkurang untuk siklus penugasan berikutnya.</span>
                            </template>
                        </div>
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
        @endif

        <!-- ============================================== -->
        <!-- MODAL 3: FORM LAPORAN HASIL PENUGASAN LAPANGAN -->
        <!-- ============================================== -->
        <div x-cloak x-show="showLaporanModal" 
             @click.self="showLaporanModal = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 relative max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                            <span>📝</span>
                            <span>LAPORAN HASIL PENUGASAN INKASO</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Catatan administrasi hasil kunjungan lapangan oleh Staf Keuangan </p>
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
                        <div :class="hasilKunjungan === 'janji_bayar' ? '' : 'sm:col-span-2'">
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

                    </div>

                    <!-- Keterangan / Catatan Lapangan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Rincian dari Petugas <span class="text-rose-500">*</span></label>
                        <textarea name="keterangan_hasil" x-model="keteranganHasil" rows="3" required
                                  placeholder="Contoh: baru bayar 500000, atau janji transfer sisa pelunasan tanggal 28..."
                                  class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-purple-500"></textarea>
                    </div>

                    <!-- Banner Info Alur Kasir (Khusus jika Cicilan / Lunas) -->
                    <div x-show="hasilKunjungan === 'cicilan' || hasilKunjungan === 'lunas'" class="p-3.5 rounded-xl bg-amber-50 border border-amber-300 text-xs">
                        <div class="flex items-start gap-2.5 text-amber-950 text-[11px] leading-relaxed font-semibold">
                            <span class="text-base leading-none">📋</span>
                            <div>
                                <strong>Catatan Faktur untuk Kasir:</strong> Divisi Faktur hanya mencatat keterangan hasil kunjungan (tanpa input nominal rupiah). Catatan ini akan masuk ke <strong>Tab 3: Laporan Hasil Penugasan</strong>. Kasir yang akan menginput nominal uang setoran kas. Faktur otomatis memotong saldo piutang (cicilan) atau lunas <strong>setelah Kasir menginput setoran kas</strong>.
                            </div>
                        </div>
                    </div>

                    <!-- Checkbox Serah Terima Faktur Kembali ke Antrean Piutang (Khusus Kunjungan Tanpa Uang / Janji Bayar / Belum Bayar) -->
                    <div x-show="hasilKunjungan === 'janji_bayar' || hasilKunjungan === 'belum_bayar' || hasilKunjungan === 'tidak_bertemu'" class="p-3 rounded-xl bg-purple-50/80 border border-purple-200 text-xs">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="kembalikan_ke_antrean" value="1" checked class="mt-0.5 rounded border-purple-300 text-purple-600 focus:ring-purple-500">
                            <span class="text-slate-800 text-[11px] leading-relaxed">
                                <strong>Kembalikan Berkas ke Antrean Piutang:</strong> Petugas telah menyerahkan kembali berkas fisik faktur ke Divisi Faktur tanpa pembayaran. Selesaikan putaran penugasan ini dan kembalikan faktur ke <strong>Antrean Faktur Belum Ditugaskan</strong> agar siap ditugaskan kembali di siklus berikutnya.
                            </span>
                        </label>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-700 leading-relaxed">
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
