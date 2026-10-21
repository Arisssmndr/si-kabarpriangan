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
            bulan: '{{ $periodeLabel }}',
            nominal: 0,
            metode: 'tunai',
            bankTujuan: 'Bank BJB Sirkulasi',
            noRef: '',
            penagih: '{{ Auth::user()->name }}',
            keterangan: ''
        },
        get sisaPiutangBaru() {
            let sisa = this.activeAgen.piutang - Number(this.activeAgen.nominal);
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

            <div class="flex flex-wrap items-center gap-2">
                @if(session('cetak_sirkulasi'))
                    <button type="button" @click="showCetakModal = true" class="px-3.5 py-2 bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold text-xs rounded-xl border border-sky-200 transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti Setoran Terakhir</span>
                    </button>
                @endif

                <!-- Pager Navigasi Periode Bulan Cepat -->
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs shadow-2xs">
                    <a href="{{ route('kasir.penerimaan.sirkulasi', array_merge(request()->query(), ['bulan' => $prevMonth])) }}" 
                       title="Bulan Sebelumnya ({{ Carbon\Carbon::parse($prevMonth . '-01')->translatedFormat('M Y') }})" 
                       class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-white rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <span class="px-2.5 py-1 font-bold text-slate-700 flex items-center gap-1.5">
                        <span>Periode: {{ $periodeLabel }}</span>
                        @if(!$isCurrentMonth)
                            <span class="px-1.5 py-0.2 text-[9px] font-bold uppercase rounded bg-amber-100 text-amber-800 border border-amber-300">Arsip</span>
                        @endif
                    </span>
                    <a href="{{ route('kasir.penerimaan.sirkulasi', array_merge(request()->query(), ['bulan' => $nextMonth])) }}" 
                       title="Bulan Selanjutnya ({{ Carbon\Carbon::parse($nextMonth . '-01')->translatedFormat('M Y') }})" 
                       class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-white rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Perhitungan Ringkas Rekapitulasi Tagihan Agen -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Plafon Kredit Agen</span>
                <div class="mt-2 text-lg font-extrabold text-slate-900">
                    Rp {{ number_format($totalPlafon, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Batas toleransi jatah sirkulasi</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Saldo Piutang Beredar ({{ $periodeLabel }})</span>
                <div class="mt-2 text-lg font-extrabold text-rose-600">
                    Rp {{ number_format($totalPiutangBulan, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Total piutang koran belum disetor periode ini</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Jatah Default Distribusi Harian</span>
                <div class="mt-2 text-lg font-extrabold text-teal-700">
                    {{ number_format($totalJatah, 0, ',', '.') }} Eksemplar
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Kapasitas oplah terlayani kasir & agen</div>
            </div>
        </div>

        <!-- Filter & Search Toolbar: Status Tagihan, Pilihan Bulan, Kode & Nama Agen -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
            <form method="GET" action="{{ route('kasir.penerimaan.sirkulasi') }}" class="space-y-4">
                <!-- Baris 1: Filter Status Tagihan (Segmented Control / Radio) -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5 mr-1">
                            <svg class="w-4 h-4 text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            STATUS TAGIHAN:
                        </span>
                        <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200 text-xs shadow-2xs">
                            <label class="cursor-pointer px-3 py-1.5 rounded-lg font-medium transition {{ ($statusPiutang ?? 'belum_lunas') === 'belum_lunas' ? 'bg-white text-teal-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="belum_lunas" {{ ($statusPiutang ?? 'belum_lunas') === 'belum_lunas' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Hanya yang Punya Piutang (Belum Lunas)</span>
                            </label>
                            <label class="cursor-pointer px-3 py-1.5 rounded-lg font-medium transition {{ ($statusPiutang ?? '') === 'lunas' ? 'bg-white text-teal-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="lunas" {{ ($statusPiutang ?? '') === 'lunas' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Sudah Lunas</span>
                            </label>
                            <label class="cursor-pointer px-3 py-1.5 rounded-lg font-medium transition {{ ($statusPiutang ?? '') === 'semua' ? 'bg-white text-teal-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="semua" {{ ($statusPiutang ?? '') === 'semua' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Semua Status</span>
                            </label>
                        </div>
                    </div>

                    <!-- Indikator Filter Aktif & Reset -->
                    @if(($statusPiutang ?? 'belum_lunas') !== 'belum_lunas' || ($bulanPilihan ?? Carbon\Carbon::now()->format('Y-m')) !== Carbon\Carbon::now()->format('Y-m') || request('search') || request('kode_agen'))
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-lg font-medium flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Filter Aktif ({{ $periodeLabel }})
                            </span>
                            <a href="{{ route('kasir.penerimaan.sirkulasi') }}" class="text-xs text-rose-600 hover:text-rose-700 font-semibold hover:underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reset Filter
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Baris 2: Pilih Bulan & Pencarian Kode/Nama Agen -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <!-- Pilih Periode Bulan -->
                    <div class="sm:col-span-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            PILIH BULAN:
                        </label>
                        <div class="relative">
                            <input type="month" name="bulan" value="{{ $bulanPilihan }}" onchange="this.form.submit()" 
                                   class="w-full text-xs font-semibold py-2 px-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-slate-900 bg-sky-50/20">
                        </div>
                    </div>

                    <!-- Cari Kode Agen Input -->
                    <div class="sm:col-span-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Kode Agen:</label>
                        <div class="relative">
                            <input type="text" name="kode_agen" value="{{ request('kode_agen') }}" placeholder="Misal: 005" 
                                   class="w-full text-xs font-mono font-bold py-2 px-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-slate-900 bg-sky-50/30">
                        </div>
                    </div>

                    <!-- General Search Input -->
                    <div class="sm:col-span-4">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Nama Agen / Wilayah:</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama agen, kios, atau wilayah..." 
                                   class="w-full text-xs py-2 pl-9 pr-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 text-slate-900">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <!-- Tombol Submit Filter -->
                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button type="submit" class="w-full px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Filter Agen</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Tagihan Agen & Form Setoran -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    Daftar Agen Sirkulasi & Status Piutang ({{ $periodeLabel }})
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
                        @forelse($agenList as $agen)
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
                            <td class="px-4 py-3 text-right font-extrabold {{ $agen->saldo_piutang_bulan > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                Rp {{ number_format($agen->saldo_piutang_bulan, 0, ',', '.') }}
                                @if($agen->total_setoran_bulan > 0)
                                    <div class="text-[10px] font-normal text-slate-500 mt-0.5">
                                        Setoran: Rp {{ number_format($agen->total_setoran_bulan, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($agen->saldo_piutang_bulan > 0)
                                    <button type="button" 
                                            @click="activeAgen = { 
                                                id: {{ $agen->id }},
                                                kode: '{{ $agen->kode_agen }}',
                                                nama: '{{ addslashes($agen->nama_agen) }}', 
                                                wilayah: '{{ $agen->wilayah ?? '-' }}', 
                                                piutang: {{ $agen->saldo_piutang_bulan }}, 
                                                bulan: '{{ $periodeLabel }}',
                                                nominal: {{ $agen->saldo_piutang_bulan }},
                                                metode: 'tunai',
                                                bankTujuan: 'Bank BJB Sirkulasi',
                                                noRef: '',
                                                penagih: '{{ Auth::user()->name }}',
                                                keterangan: 'Setoran koran {{ addslashes($agen->nama_agen) }} Periode {{ $periodeLabel }}'
                                            }; showSetorModal = true"
                                            class="px-2.5 py-1.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-[11px] rounded-lg transition shadow-2xs">
                                        Input Setoran BKM
                                    </button>
                                @else
                                    <div class="inline-flex flex-col items-center">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Lunas Bersih
                                        </span>
                                        @if($agen->transaksi_terakhir)
                                            <span class="text-[9px] font-mono text-slate-400 mt-0.5" title="Nomor Kuitansi Setoran">
                                                {{ $agen->transaksi_terakhir->no_kuitansi }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="font-semibold text-slate-700 text-xs">Tidak ada data agen yang memenuhi kriteria filter</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        Status: <span class="font-bold">{{ $statusPiutang === 'belum_lunas' ? 'Hanya yang Punya Piutang (Belum Lunas)' : ($statusPiutang === 'lunas' ? 'Sudah Lunas' : 'Semua Status') }}</span> 
                                        • Periode: <span class="font-bold">{{ $periodeLabel }}</span>
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
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
                            <h4 class="text-sm font-bold text-slate-900">Form Setoran Kas Sirkulasi</h4>
                            <p class="text-[11px] text-slate-500">Pencatatan kas masuk setoran agen koran</p>
                        </div>
                    </div>
                    <button type="button" @click="showSetorModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.penerimaan.sirkulasi.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="agen_id" :value="activeAgen.id">
                    <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                    <input type="hidden" name="status_piutang" value="{{ $statusPiutang }}">

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
                            <div class="flex justify-between">
                                <span class="text-slate-500">Bulan Periode:</span>
                                <span class="font-bold text-slate-800" x-text="activeAgen.bulan"></span>
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

                        <!-- Nominal Uang Yang Disetor -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Uang Kas Masuk / Ditransfer (Rp)</label>
                            <input type="number" name="nominal_setoran" x-model="activeAgen.nominal" required min="1" 
                                   class="w-full text-sm font-mono font-bold py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-slate-900">
                        </div>

                        <!-- Rincian Rekonsiliasi Hasil Setoran -->
                        <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Jumlah Setoran Kas Masuk:</span>
                                <span class="font-bold font-mono text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeAgen.nominal || 0)"></span></span>
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
                    <div class="grid grid-cols-3 py-2 bg-sky-50 px-3 rounded-xl border border-sky-200/80 my-2">
                        <span class="font-bold text-sky-900">Jumlah Disetor</span>
                        <span class="col-span-2 font-mono font-black text-base text-sky-800">: Rp {{ number_format($srk['nominal'], 0, ',', '.') }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-1.5 text-xs bg-slate-50 px-3 rounded-lg border border-slate-200">
                        <span class="text-slate-500 font-semibold">Terbilang Huruf</span>
                        <span class="col-span-2 font-semibold italic text-slate-900">: {{ $srk['terbilang'] }}</span>
                    </div>

                    <!-- Tanda Tangan Cetak: Hanya Kasir Penerima Sah Sesuai SOP Operasional -->
                    <div class="pt-6 border-t border-slate-200 flex justify-end text-center text-xs text-slate-600">
                        <div class="w-48">
                            <div class="text-[11px] text-slate-500">Tasikmalaya, {{ Carbon\Carbon::now()->format('d/m/Y') }}</div>
                            <div class="font-semibold text-slate-700 mt-1">Kasir Penerima</div>
                            <div class="h-14"></div>
                            <div class="font-bold text-slate-900 font-mono underline">({{ $srk['kasir'] }})</div>
                            <div class="text-[10px] text-slate-400">Bagian Perkasiran HU Priangan</div>
                        </div>
                    </div>
                </div>

                <!-- Action Button Print & Close (Pinned Bottom) -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
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
