<x-app-layout>
    <div class="space-y-6" x-data="{ showCairModal: false, activeBp: { no_bp: '', pemohon: '', keperluan: '', nominal: 0, penerima: '' } }">

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

        <!-- Header Page Realisasi BP -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Realisasi Bukti Pengeluaran (BP)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-50 text-amber-700 border border-amber-200">
                            Badge: BKK
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pencairan kas pengeluaran operasional (Komisi KH/Insentif Sales, BBM Operasional, Honor) yang telah disetujui ACC Anggaran Keuangan
                    </p>
                </div>
            </div>
        </div>

        <!-- Cards Total ACC Anggaran -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Pengajuan BP ACC Anggaran</span>
                <div class="mt-2 text-lg font-extrabold text-slate-900">
                    {{ $bpList->count() }} Permohonan BP
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Siap atau telah direalisasi kasir</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Menunggu Pencairan Kasir</span>
                <div class="mt-2 text-lg font-extrabold text-amber-600">
                    Rp {{ number_format($bpList->where('status_kasir', 'Belum Dicairkan')->sum('nominal_acc'), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">{{ $bpList->where('status_kasir', 'Belum Dicairkan')->count() }} BP belum diproses BKK</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Telah Dicairkan Shift Ini</span>
                <div class="mt-2 text-lg font-extrabold text-emerald-600">
                    Rp {{ number_format($bpList->where('status_kasir', '!=', 'Belum Dicairkan')->sum('nominal_acc'), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Kas keluar (BKK) resmi terbit</div>
            </div>
        </div>

        <!-- Tabel Daftar BP ACC Anggaran -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    Daftar Bukti Pengeluaran ACC Anggaran
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">Berdasarkan Persetujuan Keuangan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. BP</th>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Pemohon & Divisi</th>
                            <th class="px-4 py-3 font-semibold">Keperluan Pengeluaran</th>
                            <th class="px-4 py-3 font-semibold text-right">Nominal ACC (Rp)</th>
                            <th class="px-4 py-3 font-semibold text-center">Status Anggaran</th>
                            <th class="px-4 py-3 font-semibold text-center">Aksi Pencairan BKK</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($bpList as $bp)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $bp->no_bp }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ \Carbon\Carbon::parse($bp->tanggal_pengajuan)->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800">{{ $bp->pemohon }}</div>
                                <span class="text-[10px] text-slate-500">{{ $bp->divisi }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-700 font-medium">{{ $bp->keperluan }}</td>
                            <td class="px-4 py-3 text-right font-extrabold text-slate-900">Rp {{ number_format($bp->nominal_acc, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $bp->status_persetujuan }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($bp->status_kasir === 'Belum Dicairkan')
                                    <button type="button" 
                                            @click="activeBp = { no_bp: '{{ $bp->no_bp }}', pemohon: '{{ addslashes($bp->pemohon) }}', keperluan: '{{ addslashes($bp->keperluan) }}', nominal: {{ $bp->nominal_acc }}, penerima: '{{ addslashes($bp->pemohon) }}' }; showCairModal = true"
                                            class="px-2.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-[11px] rounded-lg transition shadow-2xs">
                                        Cairkan BKK
                                    </button>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $bp->status_kasir }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Cairkan BP / Terbitkan BKK -->
        <div x-cloak x-show="showCairModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showCairModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Terbitkan Bukti Kas Keluar (BKK)</h4>
                            <p class="text-[11px] text-slate-500">Pencairan uang tunai / transfer dari kasir</p>
                        </div>
                    </div>
                    <button type="button" @click="showCairModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.pengeluaran.realisasi-bp.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="no_bp" :value="activeBp.no_bp">
                    <input type="hidden" name="nominal_cair" :value="activeBp.nominal">

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">No. Bukti Pengeluaran (BP):</span>
                            <span class="font-mono font-bold text-slate-900" x-text="activeBp.no_bp"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Pemohon / Divisi:</span>
                            <span class="font-bold text-slate-800" x-text="activeBp.pemohon"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Keperluan:</span>
                            <span class="font-medium text-slate-700" x-text="activeBp.keperluan"></span>
                        </div>
                        <div class="flex justify-between pt-1 border-t border-slate-200">
                            <span class="text-slate-500">Nominal ACC Kasir:</span>
                            <span class="font-extrabold text-amber-700">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeBp.nominal)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Penerima Uang / Tanda Tangan Kasir</label>
                        <input type="text" name="penerima" x-model="activeBp.penerima" required 
                               class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Serah Terima Kasir (Opsional)</label>
                        <input type="text" name="catatan" placeholder="Contoh: Diserahkan tunai di meja kasir pukul 14:15" 
                               class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-900">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="showCairModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs rounded-lg transition shadow-2xs">
                            Cetak BKK & Serahkan Kas
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
