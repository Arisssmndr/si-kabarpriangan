<x-app-layout>
    <div class="space-y-6" x-data="{ showSetorModal: false, activeAgen: { nama: '', wilayah: '', bersih: 0, sisa: 0, nominal: 0 } }">

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
                        Rekonsiliasi tagihan agen koran, perhitungan otomatis potongan retur BAPR, dan pencatatan kas setoran harian
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    Periode: {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                </span>
            </div>
        </div>

        <!-- Perhitungan Ringkas Rekapitulasi Tagihan & Retur BAPR -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Tagihan Kotor Agen</span>
                <div class="mt-2 text-lg font-extrabold text-slate-900">
                    Rp {{ number_format($agenList->sum('tagihan_kotor'), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Total cetak & distribusi koran</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Potongan Retur Koran (BAPR)</span>
                <div class="mt-2 text-lg font-extrabold text-amber-600">
                    - Rp {{ number_format($agenList->sum('nilai_retur'), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Total {{ $agenList->sum('retur_bapr') }} eksemplar retur terverifikasi</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Tagihan Bersih Wajib Setor</span>
                <div class="mt-2 text-lg font-extrabold text-teal-700">
                    Rp {{ number_format($agenList->sum('tagihan_bersih'), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Nilai kewajiban bersih agen</div>
            </div>
        </div>

        <!-- Tabel Tagihan Agen & Form Setoran -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    Daftar Agen Sirkulasi & Status Setoran
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">Berdasarkan BAPR Berita Acara Penerimaan Retur</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Kode & Nama Agen</th>
                            <th class="px-4 py-3 font-semibold">Wilayah</th>
                            <th class="px-4 py-3 font-semibold text-right">Eks. Kotor</th>
                            <th class="px-4 py-3 font-semibold text-right">Retur (BAPR)</th>
                            <th class="px-4 py-3 font-semibold text-right">Tagihan Bersih</th>
                            <th class="px-4 py-3 font-semibold text-right">Setoran Masuk</th>
                            <th class="px-4 py-3 font-semibold text-right">Sisa Tagihan</th>
                            <th class="px-4 py-3 font-semibold text-center">Aksi Setor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($agenList as $agen)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $agen->nama_agen }}</div>
                                <span class="font-mono text-[10px] text-slate-400">{{ $agen->kode_agen }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 font-medium">{{ $agen->wilayah }}</td>
                            <td class="px-4 py-3 text-right font-medium text-slate-700">{{ number_format($agen->eksemplar) }} eks</td>
                            <td class="px-4 py-3 text-right text-amber-700 font-semibold">
                                {{ $agen->retur_bapr }} eks <span class="block text-[10px] text-slate-400">(-Rp {{ number_format($agen->nilai_retur) }})</span>
                            </td>
                            <td class="px-4 py-3 text-right font-extrabold text-slate-900">Rp {{ number_format($agen->tagihan_bersih, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-bold text-emerald-600">Rp {{ number_format($agen->setoran_tercatat, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-extrabold {{ $agen->sisa_tagihan > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                Rp {{ number_format($agen->sisa_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($agen->sisa_tagihan > 0)
                                    <button type="button" 
                                            @click="activeAgen = { nama: '{{ addslashes($agen->nama_agen) }}', wilayah: '{{ $agen->wilayah }}', bersih: {{ $agen->tagihan_bersih }}, sisa: {{ $agen->sisa_tagihan }}, nominal: {{ $agen->sisa_tagihan }} }; showSetorModal = true"
                                            class="px-2.5 py-1.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-[11px] rounded-lg transition shadow-2xs">
                                        Input Setoran
                                    </button>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Lunas
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Setoran Sirkulasi -->
        <div x-cloak x-show="showSetorModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showSetorModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Form Setoran Agen Sirkulasi</h4>
                            <p class="text-[11px] text-slate-500">Pencatatan kas masuk setoran agen koran harian</p>
                        </div>
                    </div>
                    <button type="button" @click="showSetorModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.penerimaan.sirkulasi.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="nama_agen" :value="activeAgen.nama">
                    <input type="hidden" name="periode" value="{{ \Carbon\Carbon::now()->format('Y-m') }}">

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Nama Agen:</span>
                            <span class="font-bold text-slate-900" x-text="activeAgen.nama"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Wilayah / Sub-Agen:</span>
                            <span class="font-bold text-slate-800" x-text="activeAgen.wilayah"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Sisa Tagihan Koran:</span>
                            <span class="font-bold text-rose-600">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeAgen.sisa)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Setoran Diserahkan (Rp)</label>
                        <input type="number" name="nominal_setoran" x-model="activeAgen.nominal" required min="1" :max="activeAgen.sisa" 
                               class="w-full text-sm font-mono font-bold py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Metode Setoran</label>
                        <select name="metode_pembayaran" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-slate-800">
                            <option value="Tunai Kasir">Setoran Tunai di Meja Kasir</option>
                            <option value="Transfer Bank BJB">Transfer Bank BJB Sirkulasi</option>
                            <option value="Transfer Mandiri">Transfer Mandiri Sirkulasi</option>
                        </select>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="showSetorModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs rounded-lg transition shadow-2xs">
                            Simpan Setoran Sirkulasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
