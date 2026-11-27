<x-app-layout>
    <div class="space-y-5" x-data="{
        modalReverse: false,
        reverseAction: '',
        reverseNoJurnal: '',
        openReverse(id, noJurnal) {
            this.reverseAction = '{{ url('/accounting/gl/jurnal') }}/' + id + '/reverse';
            this.reverseNoJurnal = noJurnal;
            this.modalReverse = true;
        }
    }">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Jurnal Umum (General Ledger)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Buku Besar
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Pencatatan berpasangan seluruh transaksi penjualan, kasir, dan penyesuaian.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('accounting.gl.jurnal.cetak', request()->only(['q', 'sumber', 'dari', 'sampai'])) }}" target="_blank"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-xs sm:text-sm font-bold rounded-xl shadow-2xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Jurnal</span>
                </a>
                <a href="{{ route('accounting.gl.memorial') }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>Input Jurnal Memorial</span>
                </a>
            </div>
        </div>

        <!-- 2. KPI / Metrics Ribbon (Palet 60-30-10) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Jurnal</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($stats['total_jurnal'] ?? 0, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Entri transaksi buku</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Nilai Debit</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($stats['total_nominal'] ?? 0, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Volume perputaran kas/piutang</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Status Posted</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($stats['posted_count'] ?? 0, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Tervalidasi & terkunci GL</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Draft / Penyesuaian</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($stats['draft_count'] ?? 0, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Menunggu konfirmasi final</span>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter Mandiri (12-Col Grid) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('accounting.gl.jurnal') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end">
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pencarian Jurnal</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="No jurnal, referensi, keterangan..." 
                               class="w-full h-11 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Sumber Jurnal</label>
                    <select name="sumber" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="all" {{ request('sumber', 'all') === 'all' ? 'selected' : '' }}>Semua Sumber</option>
                        <option value="iklan" {{ request('sumber') === 'iklan' ? 'selected' : '' }}>Iklan</option>
                        <option value="sirkulasi" {{ request('sumber') === 'sirkulasi' ? 'selected' : '' }}>Sirkulasi</option>
                        <option value="kasir" {{ request('sumber') === 'kasir' ? 'selected' : '' }}>Kasir / Beban</option>
                        <option value="penyusutan" {{ request('sumber') === 'penyusutan' ? 'selected' : '' }}>Penyusutan</option>
                        <option value="akrual" {{ request('sumber') === 'akrual' ? 'selected' : '' }}>Akrual</option>
                        <option value="manual" {{ request('sumber') === 'manual' ? 'selected' : '' }}>Manual / Memorial</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Posting</label>
                    <select name="status" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="posted" {{ request('status') === 'posted' ? 'selected' : '' }}>Posted</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari') }}" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="sm:col-span-1 flex items-center gap-2">
                    <button type="submit" class="w-full h-11 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer" title="Cari Data">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <a href="{{ route('accounting.gl.jurnal') }}" class="h-11 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center" title="Reset Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.033 8.033 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                </div>
            </form>
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

        <!-- Pure Tabel Jurnal Umum Berpasangan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase text-xs tracking-wider font-semibold whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">No. Jurnal</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Sumber</th>
                            <th class="py-3 px-3.5">No. Referensi</th>
                            <th class="py-3 px-3.5">Keterangan</th>
                            <th class="py-3 px-3.5 text-right w-36">Total Transaksi (Rp)</th>
                            <th class="py-3 px-3.5 text-center w-28">Status</th>
                            <th class="py-3 px-3.5 text-center w-36">Kontrol Audit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($jurnalList as $key => $jurnal)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400">{{ $jurnalList->firstItem() + $key }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">
                                    {{ $jurnal->no_jurnal }}
                                    @if($jurnal->reversalOf)
                                        <span class="block text-xs font-normal text-amber-700">Pembalik {{ $jurnal->reversalOf->no_jurnal }}</span>
                                    @elseif($jurnal->reversedBy)
                                        <span class="block text-xs font-normal text-rose-600">Dibalik: {{ $jurnal->reversedBy->no_jurnal }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}</td>
                                <td class="py-3 px-3.5">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md uppercase {{ $jurnal->sumber === 'iklan' ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                                        {{ $jurnal->sumber ?: 'Auto' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 font-mono text-xs">{{ $jurnal->referensi ?: '-' }}</td>
                                <td class="py-3 px-3.5 max-w-xs truncate">{{ $jurnal->keterangan }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($jurnal->total_debet, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($jurnal->status === 'draft')
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200">
                                            Draft
                                        </span>
                                    @elseif($jurnal->reversed_by_jurnal_id)
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-300">
                                            Reversed
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Posted
                                        </span>
                                        @if($jurnal->approved_by)
                                            <span class="block text-xs text-slate-500 mt-0.5">Terverifikasi</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    @if($jurnal->status === 'draft' || !$jurnal->approved_by)
                                        <form method="POST" action="{{ route('accounting.gl.jurnal.approve', $jurnal->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-kp-blue-600 hover:bg-kp-blue-700 text-white rounded-lg text-xs font-bold transition cursor-pointer">
                                                {{ $jurnal->status === 'draft' ? 'Approve & Post' : 'Verifikasi' }}
                                            </button>
                                        </form>
                                    @elseif($jurnal->status === 'posted' && !$jurnal->reversed_by_jurnal_id && $jurnal->tipe !== 'pembalik')
                                        <button type="button"
                                                @click="openReverse({{ $jurnal->id }}, @js($jurnal->no_jurnal))"
                                                class="px-2.5 py-1 bg-white hover:bg-rose-50 text-rose-700 border border-rose-200 rounded-lg text-xs font-semibold transition cursor-pointer">
                                            Jurnal Pembalik
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400">Final</span>
                                    @endif
                                </td>
                            </tr>
                            @if($jurnal->details && $jurnal->details->count() > 0)
                                <tr class="bg-slate-50/40">
                                    <td colspan="9" class="p-3 pl-12">
                                        <table class="w-full text-xs">
                                            <thead>
                                                <tr class="text-slate-500 border-b border-slate-200">
                                                    <th class="py-1 text-left font-semibold">Kode Perkiraan</th>
                                                    <th class="py-1 text-left font-semibold">Nama Akun</th>
                                                    <th class="py-1 text-right font-semibold w-28">Debet (Rp)</th>
                                                    <th class="py-1 text-right font-semibold w-28">Kredit (Rp)</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @foreach($jurnal->details as $d)
                                                    <tr>
                                                        <td class="py-1 font-mono {{ $d->kredit > 0 ? 'pl-4 text-slate-500' : 'text-slate-800 font-medium' }}">{{ $d->kode_perkiraan }}</td>
                                                        <td class="py-1 {{ $d->kredit > 0 ? 'pl-4 text-slate-500' : 'text-slate-800' }}">{{ $d->nama_perkiraan }}</td>
                                                        <td class="py-1 text-right font-mono text-slate-800">{{ $d->debet > 0 ? number_format($d->debet, 0, ',', '.') : '-' }}</td>
                                                        <td class="py-1 text-right font-mono text-slate-800">{{ $d->kredit > 0 ? number_format($d->kredit, 0, ',', '.') : '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada jurnal umum yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-t border-slate-200">
                {{ $jurnalList->links() }}
            </div>
        </div>

        <!-- Modal Jurnal Pembalik (Reversal) -->
        <div x-show="modalReverse" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="modalReverse = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-white rounded-xl shadow-xl p-6 border border-slate-200">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Terbitkan Jurnal Pembalik (Reversal)</h3>
                            <p class="text-xs text-slate-500 mt-0.5 font-mono" x-text="'Referensi: ' + reverseNoJurnal"></p>
                        </div>
                        <button type="button" @click="modalReverse = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold cursor-pointer">&times;</button>
                    </div>
                    <form method="POST" :action="reverseAction" class="space-y-4 mt-4">
                        @csrf
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                            Sesuai prinsip <strong>Immutable General Ledger</strong>, jurnal yang sudah diposting tidak dihapus. Sistem akan membuat jurnal lawan (Debet &harr; Kredit) dengan nilai identik.
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alasan Koreksi / Pembalikan</label>
                            <textarea name="alasan" rows="3" required minlength="3" placeholder="Contoh: Koreksi nominal atau salah pos akun..."
                                      class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600"></textarea>
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="submit" class="flex-1 h-10 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition cursor-pointer">
                                Terbitkan Jurnal Pembalik
                            </button>
                            <button type="button" @click="modalReverse = false" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

