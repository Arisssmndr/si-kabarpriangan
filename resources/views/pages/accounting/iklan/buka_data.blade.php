<x-app-layout>
    <div class="space-y-5">

        <!-- ============================================================== -->
        <!-- 1. HEADER HALAMAN (PALET 60-30-10 & ACTION BAR)                -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Buka Kunci Data Iklan
                        </h1>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                            Memo Unlock
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Otorisasi dan persetujuan pembukaan kunci data transaksi iklan.
                    </p>
                </div>
            </div>

            <!-- Action Toolbar Kanan -->
            <div class="flex items-center gap-3 shrink-0 flex-wrap">
                <div class="hidden md:flex items-center gap-1.5 text-xs font-medium text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-200">
                    <span class="text-slate-400">Memo Baru:</span>
                    <span class="text-slate-900 font-mono font-bold">{{ $nextNoMemo }}</span>
                </div>

                <!-- Tombol Utama: Otorisasi Buka Data (Buka Modal Mengambang) -->
                <button type="button" 
                        onclick="openModalBukaData()"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Otorisasi Buka Data</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- 2. KPI / STATISTIK CEPAT (METRICS RIBBON 60-30-10)             -->
        <!-- ============================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Memo</span>
                    <span class="text-2xl font-bold text-slate-900 mt-1 block font-mono">{{ number_format($stats['total'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 border border-kp-blue-200 flex items-center justify-center text-kp-blue-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Menunggu Otorisasi</span>
                    <span class="text-2xl font-bold text-slate-900 mt-1 block font-mono">{{ number_format($stats['pending'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 border border-kp-blue-200 flex items-center justify-center text-kp-blue-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Disetujui / Terbuka</span>
                    <span class="text-2xl font-bold text-kp-blue-700 mt-1 block font-mono">{{ number_format($stats['approved'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 border border-kp-blue-200 flex items-center justify-center text-kp-blue-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 3. TOOLBAR PENCARIAN & FILTER LENGKAP                          -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.iklan.buka-data') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik no memo, faktur, pemasang, pemohon..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Status
                    </label>
                    <select name="status" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Dari Tanggal
                    </label>
                    <input type="date" 
                           name="dari" 
                           value="{{ request('dari') }}" 
                           class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Sampai Tanggal
                    </label>
                    <input type="date" 
                           name="sampai" 
                           value="{{ request('sampai') }}" 
                           class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'status', 'dari', 'sampai']))
                        <a href="{{ route('accounting.iklan.buka-data') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- ============================================================== -->
        <!-- 4. TABEL DAFTAR MEMO UNLOCK (FULL-WIDTH & PALET 60-30-10)       -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[360px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] uppercase text-[10px] tracking-wider font-bold whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Memo</th>
                            <th class="py-3 px-3.5">Tanggal Target</th>
                            <th class="py-3 px-3.5">Nama Pemasang</th>
                            <th class="py-3 px-3.5">No. Faktur / Spesifikasi</th>
                            <th class="py-3 px-3.5">Alasan Pembukaan</th>
                            <th class="py-3 px-3.5">Pemohon</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($daftarMemo as $key => $memo)
                            @php
                                $isApproved = in_array(strtoupper($memo->status_akuntansi ?? ''), ['APPROVED', 'OPEN']) || strtoupper($memo->status ?? '') === 'OPEN';
                                $statusLabel = $isApproved ? 'Disetujui' : 'Menunggu';
                                $statusClass = $isApproved 
                                    ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' 
                                    : 'bg-slate-100 text-slate-700 border-slate-200';
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-400 font-normal">
                                    {{ $daftarMemo->firstItem() + $key }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $memo->nomor_memo ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    {{ $memo->tanggal_target ? \Carbon\Carbon::parse($memo->tanggal_target)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-bold text-slate-900">
                                    {{ $memo->nama_pemasang ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-mono text-xs">
                                    {{ $memo->spesifikasi_iklan ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 max-w-xs truncate text-slate-600 font-normal" title="{{ $memo->alasan }}">
                                    {{ $memo->alasan ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    {{ $memo->diketahui_oleh ?? ($memo->user?->name ?? 'Divisi Iklan') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 text-center" 
                                    x-data="{ open: false }" 
                                    :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <button type="button" 
                                                @click="open = !open" 
                                                class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                :class="{ 'text-slate-900 bg-slate-100': open }"
                                                title="Pilihan Aksi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="5" r="1.5" />
                                                <circle cx="12" cy="12" r="1.5" />
                                                <circle cx="12" cy="19" r="1.5" />
                                            </svg>
                                        </button>

                                        <div x-cloak 
                                             x-show="open" 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-52 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">
                                            
                                            @if(!$isApproved)
                                                <!-- Setujui Buka Data -->
                                                <button type="button" 
                                                        onclick="openApproveModal('{{ $memo->id }}', '{{ addslashes($memo->nomor_memo) }}', '{{ addslashes($memo->nama_pemasang ?? '') }}')"
                                                        @click="open = false"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-semibold text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition text-left cursor-pointer">
                                                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Setujui Buka Kunci</span>
                                                </button>
                                            @endif

                                            <!-- Lihat Detail -->
                                            <button type="button" 
                                                    onclick="showMemoDetail('{{ addslashes($memo->nomor_memo) }}', '{{ $memo->tanggal_target ? \Carbon\Carbon::parse($memo->tanggal_target)->format('d/m/Y') : '-' }}', '{{ addslashes($memo->nama_pemasang ?? '-') }}', '{{ addslashes($memo->alasan ?? '-') }}', '{{ addslashes($statusLabel) }}', '{{ addslashes($memo->diketahui_oleh ?? '-') }}')"
                                                    @click="open = false"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail Memo</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400 text-sm">
                                    Tidak ada data memo unlock yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($daftarMemo->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $daftarMemo->links() }}
                </div>
            @endif
        </div>

        <!-- ============================================================== -->
        <!-- 5. MODAL FORM: OTORISASI BUKA DATA BARU (GAYA DIVISI IKLAN)    -->
        <!-- ============================================================== -->
        <div id="modalBukaData" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[92vh] overflow-hidden">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <h3 class="text-base font-bold text-slate-900">Otorisasi Buka Kunci Data</h3>
                    <button type="button" onclick="closeModalBukaData()" 
                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer" 
                            title="Tutup">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Form Buka Data -->
                <form method="POST" action="{{ route('accounting.iklan.proses-buka-data') }}" class="flex flex-col flex-1 min-h-0">
                    @csrf

                    <div class="p-6 space-y-4 text-xs overflow-y-auto flex-1">
                        <!-- Baris 1: No Memo & Tanggal Target -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                    No. Memo
                                </label>
                                <input type="text" 
                                       name="no_memo" 
                                       value="{{ $nextNoMemo }}" 
                                       readonly
                                       class="w-full h-11 px-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono font-bold text-slate-600 cursor-not-allowed select-none" />
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                    Tanggal Target <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" 
                                       name="tanggal_target" 
                                       value="{{ date('Y-m-d') }}" 
                                       required
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                            </div>
                        </div>

                        <!-- Baris 2: No. Faktur / Pilihan Cepat -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                No. Faktur Iklan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="no_faktur" 
                                   id="input_no_faktur"
                                   list="list_faktur_buka"
                                   placeholder="Ketik atau pilih No. Faktur..." 
                                   required
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                            <datalist id="list_faktur_buka">
                                @foreach($daftarFaktur as $f)
                                    <option value="{{ $f->nofakturkoran }}">{{ $f->nama_pemasangkoran }} (Rp {{ number_format($f->totaltagihan_transaksikoran, 0, ',', '.') }})</option>
                                @endforeach
                            </datalist>
                        </div>

                        <!-- Baris 3: Urgensi -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Kategori Urgensi
                            </label>
                            <select name="kategori_urgensi" 
                                    class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                <option value="SEDANG">Biasa / Normal</option>
                                <option value="TINGGI">Penting</option>
                                <option value="MENDESAK">Mendesak (Deadline Cetak)</option>
                            </select>
                        </div>

                        <!-- Baris 4: Alasan Pembukaan -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Alasan Pembukaan Kunci <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="alasan" 
                                      rows="3" 
                                      required 
                                      placeholder="Penjelasan revisi tarif, edisi muat, atau persetujuan pimpinan..." 
                                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0">
                        <button type="button" 
                                onclick="closeModalBukaData()" 
                                class="h-11 px-5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="h-11 px-6 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-xl text-sm font-bold transition cursor-pointer shadow-xs">
                            Buka Kunci Faktur
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 6. MODAL PERSETUJUAN CEPAT (APPROVE MEMO MASUK)                -->
        <!-- ============================================================== -->
        <div id="modalApproveMemo" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Persetujuan Buka Kunci</h3>
                    <button type="button" onclick="closeApproveModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('accounting.iklan.proses-buka-data') }}">
                    @csrf
                    <input type="hidden" name="memo_id" id="approve_memo_id">

                    <div class="p-6 space-y-4 text-xs">
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="text-xs text-slate-500">Nomor Memo:</div>
                            <div class="text-sm font-mono font-bold text-kp-blue-700" id="approve_nomor_memo">-</div>
                            <div class="text-xs text-slate-500 mt-2">Pemasang / Target:</div>
                            <div class="text-sm font-semibold text-slate-900" id="approve_nama_pemasang">-</div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Catatan Otorisasi Akuntansi
                            </label>
                            <textarea name="alasan" 
                                      rows="2" 
                                      placeholder="Disetujui untuk perubahan..." 
                                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition resize-none">Disetujui oleh Akuntansi</textarea>
                        </div>
                    </div>

                    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeApproveModal()" class="h-11 px-5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-11 px-6 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-xl text-sm font-bold transition cursor-pointer shadow-xs">
                            Setujui & Buka
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- ============================================================== -->
    <!-- 7. JAVASCRIPT MODAL INTERAKTIF                                 -->
    <!-- ============================================================== -->
    @push('scripts')
    <script>
        function openModalBukaData() {
            const modal = document.getElementById('modalBukaData');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeModalBukaData() {
            const modal = document.getElementById('modalBukaData');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function openApproveModal(id, nomorMemo, namaPemasang) {
            document.getElementById('approve_memo_id').value = id;
            document.getElementById('approve_nomor_memo').innerText = nomorMemo;
            document.getElementById('approve_nama_pemasang').innerText = namaPemasang || '-';
            const modal = document.getElementById('modalApproveMemo');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeApproveModal() {
            const modal = document.getElementById('modalApproveMemo');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function showMemoDetail(nomorMemo, tanggal, pemasang, alasan, status, pemohon) {
            Swal.fire({
                title: `<span class="text-base font-bold text-slate-900">${nomorMemo}</span>`,
                html: `
                    <div class="text-left text-xs space-y-2 mt-2">
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-slate-500 block">Pemasang / Target:</span>
                            <span class="font-bold text-slate-900 text-sm">${pemasang}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="p-2 bg-slate-50 rounded-lg border border-slate-200">
                                <span class="text-slate-500 block">Tanggal:</span>
                                <span class="font-semibold text-slate-900">${tanggal}</span>
                            </div>
                            <div class="p-2 bg-slate-50 rounded-lg border border-slate-200">
                                <span class="text-slate-500 block">Status:</span>
                                <span class="font-semibold text-slate-900">${status}</span>
                            </div>
                        </div>
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-slate-500 block">Pemohon:</span>
                            <span class="font-medium text-slate-900">${pemohon}</span>
                        </div>
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-slate-500 block">Alasan Pembukaan:</span>
                            <span class="font-medium text-slate-800">${alasan}</span>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#0A72AC',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl text-xs font-bold px-5 py-2.5'
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalBukaData();
                closeApproveModal();
            }
        });
    </script>
    @endpush
</x-app-layout>
