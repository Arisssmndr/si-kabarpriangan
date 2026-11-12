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
                            Buka Kunci Data Sirkulasi
                        </h1>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                            Memo Unlock
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Otorisasi pembukaan kunci hitung koran sirkulasi dan reset pembukuan agen.
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
                        onclick="openModalBukaSirkulasi()"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Otorisasi Buka Sirkulasi</span>
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
        <!-- 2. TOOLBAR PENCARIAN & FILTER                                  -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.sirkulasi.buka-data') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-8">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pencarian Data Memo
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik no memo, alasan pembukaan, atau pemohon..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-4 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->filled('q'))
                        <a href="{{ route('accounting.sirkulasi.buka-data') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- ============================================================== -->
        <!-- 3. TABEL RIWAYAT MEMO BUKA SIRKULASI (FULL-WIDTH)              -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[360px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] font-bold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Memo</th>
                            <th class="py-3 px-3.5">Periode Bulan</th>
                            <th class="py-3 px-3.5">Alasan Pembukaan</th>
                            <th class="py-3 px-3.5">Pemohon</th>
                            <th class="py-3 px-3.5 text-center w-28">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($daftarMemo as $key => $memo)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-400 font-normal">
                                    {{ $daftarMemo->firstItem() + $key }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $memo->nomor_memo ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $memo->periode ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 max-w-sm truncate text-slate-600 font-normal" title="{{ $memo->alasan }}">
                                    {{ $memo->alasan ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    {{ $memo->user?->name ?? ($memo->diketahui_oleh ?? 'Divisi Sirkulasi') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        Disetujui
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
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">
                                            
                                            <button type="button" 
                                                    onclick="showMemoDetail('{{ addslashes($memo->nomor_memo) }}', '{{ $memo->periode ?? '-' }}', '{{ addslashes($memo->alasan ?? '-') }}', 'Disetujui')"
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
                                <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                    Tidak ada data memo buka kunci sirkulasi.
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
        <!-- 4. MODAL FORM: OTORISASI BUKA KUNCI SIRKULASI                  -->
        <!-- ============================================================== -->
        <div id="modalBukaSirkulasi" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[92vh] overflow-hidden">
                
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <h3 class="text-base font-bold text-slate-900">Otorisasi Buka Kunci Sirkulasi</h3>
                    <button type="button" onclick="closeModalBukaSirkulasi()" 
                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer" 
                            title="Tutup">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('accounting.sirkulasi.buka-data.proses') }}" class="flex flex-col flex-1 min-h-0">
                    @csrf

                    <div class="p-6 space-y-4 text-xs overflow-y-auto flex-1">
                        <!-- Baris 1: No Memo & Bulan -->
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
                                    Bulan Penjualan <span class="text-rose-500">*</span>
                                </label>
                                <input type="month" 
                                       name="bulan" 
                                       value="{{ date('Y-m') }}" 
                                       required
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                            </div>
                        </div>

                        <!-- Baris 2: Alasan -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Alasan Pembukaan Kunci <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="alasan" 
                                      rows="3" 
                                      required 
                                      placeholder="Koreksi jatah agen wilayah, retur koran susulan, persetujuan direksi..." 
                                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition resize-none"></textarea>
                        </div>

                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800">
                            <strong>Perhatian:</strong> Membuka kunci sirkulasi akan membatalkan jurnal umum terkait bulan tersebut dan me-revert penambahan saldo piutang agen.
                        </div>
                    </div>

                    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0">
                        <button type="button" 
                                onclick="closeModalBukaSirkulasi()" 
                                class="h-11 px-5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                onclick="return confirm('Buka kunci hitung sirkulasi bulan ini? Data jurnal terkait akan direset.')"
                                class="h-11 px-6 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-xl text-sm font-bold transition cursor-pointer shadow-xs">
                            Buka Kunci Sirkulasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function openModalBukaSirkulasi() {
            const modal = document.getElementById('modalBukaSirkulasi');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeModalBukaSirkulasi() {
            const modal = document.getElementById('modalBukaSirkulasi');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function showMemoDetail(nomorMemo, periode, alasan, status) {
            Swal.fire({
                title: `<span class="text-base font-bold text-slate-900">${nomorMemo}</span>`,
                html: `
                    <div class="text-left text-xs space-y-2 mt-2">
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-slate-500 block">Periode Bulan:</span>
                            <span class="font-bold text-slate-900 text-sm">${periode}</span>
                        </div>
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-slate-500 block">Status:</span>
                            <span class="font-semibold text-kp-blue-700">${status}</span>
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
                closeModalBukaSirkulasi();
            }
        });
    </script>
    @endpush
</x-app-layout>
