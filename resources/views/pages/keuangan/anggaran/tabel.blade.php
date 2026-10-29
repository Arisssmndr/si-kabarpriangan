<x-app-layout>
    <div class="space-y-6">

        <!-- Header Tabel Master Sesuai DOS ANGGARAN.EXE -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600 shadow-xs shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-600 animate-pulse"></span>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">
                            TABEL MASTER ANGGARAN (DOS ANGGARAN.EXE)
                        </h1>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                            HU. Kabar Priangan
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Referensi data master: Kode Bidang Bagian (01-07), Bagan Akun Perkiraan (COA), &amp; Rekening Bank Resmi
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('keuangan.anggaran.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs hover:shadow-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>+ Entry BP Priangan</span>
                </a>
                <a href="{{ route('keuangan.anggaran.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold transition">
                    <span>Daftar BP</span>
                </a>
            </div>
        </div>

        <!-- Navigation Sub-Tabs (BIDANG | PERKIRAAN PRIANGAN | REKENING BANK PRIANGAN) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-2 shadow-xs">
            <div class="flex flex-wrap items-center gap-2">
                <!-- Tab 1: Bidang -->
                <a href="{{ route('keuangan.anggaran.tabel.bidang') }}" 
                   class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-150 {{ $activeTab === 'bidang' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    <span>1. TABEL BIDANG</span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-md font-bold {{ $activeTab === 'bidang' ? 'bg-purple-800 text-white' : 'bg-slate-200 text-slate-700' }}">01-07</span>
                </a>

                <!-- Tab 2: Perkiraan Priangan -->
                <a href="{{ route('keuangan.anggaran.tabel.perkiraan') }}" 
                   class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-150 {{ $activeTab === 'perkiraan' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                    <span>2. PERKIRAAN PRIANGAN</span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-md font-bold {{ $activeTab === 'perkiraan' ? 'bg-purple-800 text-white' : 'bg-slate-200 text-slate-700' }}">COA</span>
                </a>

                <!-- Tab 3: Rekening Bank Priangan -->
                <a href="{{ route('keuangan.anggaran.tabel.rekening-bank') }}" 
                   class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-150 {{ $activeTab === 'rekening_bank' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                    <span>3. REKENING BANK PRIANGAN</span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-md font-bold {{ $activeTab === 'rekening_bank' ? 'bg-purple-800 text-white' : 'bg-slate-200 text-slate-700' }}">BANK</span>
                </a>
            </div>
        </div>

        <!-- SEARCH BAR & INFO -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <form method="GET" action="{{ url()->current() }}" class="flex-1 flex items-center gap-2 max-w-md">
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                    <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari kode, nama divisi, atau kata kunci..." 
                           class="w-full text-xs pl-10 pr-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 text-slate-800 font-medium">
                </div>
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Cari
                </button>
                @if(!empty($search))
                    <a href="{{ url()->current() }}" class="px-3 py-2 border border-slate-300 hover:bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl">
                        Reset
                    </a>
                @endif
            </form>
            <div class="text-[11px] text-slate-500 font-semibold flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Standar Baku DOS ANGGARAN.EXE &bull; Terhubung Real-Time ke Modul Kasir &amp; BP</span>
            </div>
        </div>

        <!-- TAB CONTENT 1: TABEL BIDANG -->
        @if($activeTab === 'bidang')
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <!-- Sub-Header Card -->
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                            Daftar Kode Bidang / Bagian Kabar Priangan (01 s/d 07)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Klasifikasi departemen pemohon pengeluaran anggaran dan pembebanan kas</p>
                    </div>
                    <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-purple-100 text-purple-800 border border-purple-200">
                        Total {{ count($bidangList) }} Bidang
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <!-- Table Header: Jelas & Rapi -->
                        <thead>
                            <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-700 text-[11px] font-extrabold uppercase tracking-wider">
                                <th class="py-3 px-4 w-20 text-center">Kode</th>
                                <th class="py-3 px-4 w-52">Bidang / Divisi</th>
                                <th class="py-3 px-4 min-w-[280px]">Deskripsi Operasional</th>
                                <th class="py-3 px-4 w-56">Bagan Akun (COA Beban)</th>
                                <th class="py-3 px-4 w-48">Penanggung Jawab</th>
                                <th class="py-3 px-4 w-36 text-center">Aktivitas BP</th>
                                <th class="py-3 px-4 w-28 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/80 text-slate-700">
                            @forelse($bidangList as $item)
                                <tr class="hover:bg-purple-50/20 transition-colors duration-150 align-top">
                                    <!-- 1. Kode Bagian -->
                                    <td class="py-4 px-4 text-center">
                                        <div class="inline-flex items-center justify-center w-9 h-9 rounded-xl font-mono text-xs font-black bg-purple-50 text-purple-700 border border-purple-200 shadow-2xs">
                                            {{ $item['kode'] }}
                                        </div>
                                    </td>

                                    <!-- 2. Nama Bidang -->
                                    <td class="py-4 px-4">
                                        <div class="font-black text-slate-900 text-sm tracking-tight leading-snug">
                                            {{ $item['nama'] }}
                                        </div>
                                        <div class="mt-1">
                                            <span class="inline-block text-[10px] font-mono font-semibold px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                                slug: {{ $item['slug'] }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- 3. Deskripsi -->
                                    <td class="py-4 px-4">
                                        <p class="text-xs text-slate-600 leading-relaxed font-normal">
                                            {{ $item['deskripsi'] }}
                                        </p>
                                    </td>

                                    <!-- 4. Bagan Akun COA -->
                                    <td class="py-4 px-4">
                                        <div class="space-y-1">
                                            <span class="inline-block px-2 py-0.5 rounded font-mono text-xs font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                                {{ $item['pos_beban'] }}
                                            </span>
                                            <div class="text-[11px] text-slate-500 font-medium leading-tight">
                                                {{ $item['nama_pos'] }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 5. Penanggung Jawab -->
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 border border-slate-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            </div>
                                            <span class="font-semibold text-xs text-slate-800 leading-snug">
                                                {{ $item['pj'] }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- 6. Aktivitas Real-Time -->
                                    <td class="py-4 px-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $item['total_bp'] > 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $item['total_bp'] > 0 ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                                {{ $item['total_bp'] }} BP
                                            </span>
                                            <span class="font-mono text-xs font-bold text-slate-800 mt-1">
                                                Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- 7. Aksi -->
                                    <td class="py-4 px-4 text-center">
                                        <a href="{{ route('keuangan.anggaran.create', ['divisi' => $item['slug']]) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-xs hover:shadow transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                            <span>+ Entry BP</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400 italic">
                                        Tidak ada data bidang yang cocok dengan pencarian "{{ $search }}".
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- TAB CONTENT 2: TABEL PERKIRAAN PRIANGAN -->
        @if($activeTab === 'perkiraan')
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <!-- Sub-Header Card -->
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                            Bagan Akun Perkiraan Pengeluaran Beban (Chart of Accounts Priangan)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Struktur kode rekening akuntansi pengeluaran operasional DOS ANGGARAN.EXE &amp; Kasir</p>
                    </div>
                    <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-purple-100 text-purple-800 border border-purple-200">
                        Total {{ count($perkiraanList) }} Akun Perkiraan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-700 text-[11px] font-extrabold uppercase tracking-wider">
                                <th class="py-3 px-4 w-48">Kode Perkiraan (COA)</th>
                                <th class="py-3 px-4 w-60">Nama Akun Perkiraan</th>
                                <th class="py-3 px-4 w-44">Terkait Bidang</th>
                                <th class="py-3 px-4 w-48">Klasifikasi Beban</th>
                                <th class="py-3 px-4 min-w-[260px]">Keterangan Pos Operasional</th>
                                <th class="py-3 px-4 w-24 text-center">Posisi</th>
                                <th class="py-3 px-4 w-40 text-right">Realisasi (BP)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/80 text-slate-700">
                            @forelse($perkiraanList as $item)
                                <tr class="hover:bg-purple-50/20 transition-colors duration-150 align-top">
                                    <td class="py-4 px-4">
                                        <span class="font-mono text-xs font-black px-2.5 py-1 rounded-lg bg-purple-50 text-purple-800 border border-purple-200">
                                            {{ $item['kode'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-slate-900 text-xs leading-snug">
                                            {{ $item['nama'] }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="inline-flex items-center gap-1.5 font-semibold text-xs text-slate-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            {{ $item['bidang'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $item['kategori'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <p class="text-xs text-slate-600 leading-relaxed font-normal">
                                            {{ $item['keterangan'] }}
                                        </p>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                            {{ $item['tipe'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <div class="font-mono font-bold text-slate-900 text-xs">
                                            {{ $item['total_bp'] }} Transaksi
                                        </div>
                                        <div class="font-mono text-[11px] text-emerald-700 font-bold mt-0.5">
                                            Rp {{ number_format($item['total_rupiah'], 0, ',', '.') }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400 italic">
                                        Tidak ada akun perkiraan yang cocok dengan pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- TAB CONTENT 3: TABEL REKENING BANK PRIANGAN -->
        @if($activeTab === 'rekening_bank')
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <!-- Sub-Header Card -->
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                            Daftar Rekening Bank Operasional Resmi HU. Kabar Priangan
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Rekening penampung dan rekening pencairan transfer Bukti Kas Keluar (BKK) Kasir</p>
                    </div>
                    <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-purple-100 text-purple-800 border border-purple-200">
                        Total {{ count($bankList) }} Rekening Bank
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-5 bg-slate-50/30">
                    @forelse($bankList as $bank)
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs hover:border-purple-300 hover:shadow-md transition space-y-3.5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                                        {{ $bank['tipe_rekening'] }}
                                    </span>
                                    <h3 class="text-sm font-black text-slate-900 mt-1.5">{{ $bank['bank_nama'] }}</h3>
                                    <p class="text-[11px] text-slate-500">{{ $bank['cabang'] }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            </div>

                            <div class="bg-slate-900 rounded-xl p-4 text-white font-mono flex items-center justify-between shadow-2xs">
                                <div>
                                    <div class="text-[10px] text-slate-400 uppercase tracking-wider">Nomor Rekening</div>
                                    <div class="text-base font-black tracking-wider text-emerald-400 mt-0.5">{{ $bank['no_rekening'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] text-slate-400 uppercase tracking-wider">Atas Nama</div>
                                    <div class="text-[11px] font-bold text-slate-200 truncate max-w-[160px]">{{ $bank['atas_nama'] }}</div>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-semibold block uppercase">Fungsi Utama:</span>
                                    <span class="font-medium text-slate-700 text-[11px] leading-snug">{{ $bank['fungsi_utama'] }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 font-semibold block uppercase">COA Buku Besar:</span>
                                    <span class="font-mono font-bold text-purple-700 text-xs">{{ $bank['kode_coa'] }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 py-12 text-center text-slate-400 italic">
                            Tidak ada data rekening bank yang cocok.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
