<x-app-layout>
    <div class="space-y-6" x-data="{ 
        showTutupModal: false,
        saldoSistem: {{ $saldoAkhirShift }},
        fisikKas: {{ $saldoAkhirShift }},
        catatanSerahTerima: '',
        get selisih() {
            return Number(this.fisikKas) - Number(this.saldoSistem);
        }
    }">

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

        <!-- Header Page Buku Kas & Tutup Shift -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Buku Kas Harian & Tutup Shift Kasir
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-indigo-50 text-indigo-700 border border-indigo-200">
                            Badge: Shift
                        </span>
                        @if($activeShift->status === 'ditutup')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 text-slate-700 border border-slate-300">
                                Shift Ditutup (Siap Tarik Akunting)
                            </span>
                        @else
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Shift Aktif
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekonsiliasi mutasi kas harian, verifikasi saldo fisik, dan penutupan shift kasir 'Siap Tarik Akunting'
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($activeShift->status === 'aktif')
                    <button type="button" @click="showTutupModal = true" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center gap-2 shrink-0">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Tutup Shift Kasir (End Shift)</span>
                    </button>
                @else
                    <button type="button" @click="showTutupModal = true" class="px-4 py-2.5 bg-slate-700 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center gap-2 shrink-0">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Perbarui Rekonsiliasi Tutup Shift</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Live Counter Balance & Shift Status Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Saldo Awal Fisik Kasir</span>
                <div class="mt-2 text-lg font-extrabold text-slate-800 font-mono">
                    Rp {{ number_format($saldoAwalShift, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Modal uang kecil / serah terima kemarin</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Mutasi Masuk Hari Ini</span>
                <div class="mt-2 text-lg font-extrabold text-emerald-600 font-mono">
                    + Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Total BKM Iklan, Sirkulasi & Tunai</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Mutasi Keluar Hari Ini (BKK)</span>
                <div class="mt-2 text-lg font-extrabold text-rose-600 font-mono">
                    - Rp {{ number_format($totalPengeluaranBp, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Realisasi BP Operasional ACC Keuangan</div>
            </div>

            <div class="bg-white border-2 border-teal-500 rounded-xl p-4 shadow-xs bg-teal-50/20">
                <span class="text-xs font-bold text-teal-800">Saldo Akhir Sistem (Wajib Fisik)</span>
                <div class="mt-2 text-lg font-black text-teal-700 font-mono">
                    Rp {{ number_format($saldoAkhirShift, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-teal-600 font-semibold">Kasir Aktif: {{ Auth::user()->name }}</div>
            </div>

        </div>

        <!-- Breakdown Penerimaan Shift -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                Rincian Sumber Penerimaan Kas Shift Ini
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="flex items-center justify-between p-3.5 rounded-lg bg-slate-50 border border-slate-200/80">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        <span class="font-semibold text-slate-800">Setoran Iklan (BKM Faktur)</span>
                    </div>
                    <span class="font-mono font-extrabold text-slate-900">Rp {{ number_format($totalPenerimaanIklan, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-lg bg-slate-50 border border-slate-200/80">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        <span class="font-semibold text-slate-800">Setoran Sirkulasi (Koran)</span>
                    </div>
                    <span class="font-mono font-extrabold text-slate-900">Rp {{ number_format($totalSetoranSirkulasi, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-lg bg-slate-50 border border-slate-200/80">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="font-semibold text-slate-800">Penjualan Langsung / Tunai</span>
                    </div>
                    <span class="font-mono font-extrabold text-slate-900">Rp {{ number_format($totalPenjualanTunai, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Tabel Mutasi Kas Shift Hari Ini -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    Tabel Mutasi Kas Shift Real-Time (Hari Ini: {{ $today }})
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">BKM Masuk & BKK Keluar Terverifikasi Database</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Jam</th>
                            <th class="px-4 py-3 font-semibold">No. BKM / BKK</th>
                            <th class="px-4 py-3 font-semibold">Jenis & Kategori</th>
                            <th class="px-4 py-3 font-semibold">Uraian Transaksi</th>
                            <th class="px-4 py-3 font-semibold">Petugas / Pihak Terkait</th>
                            <th class="px-4 py-3 font-semibold text-right">Kas Masuk (Rp)</th>
                            <th class="px-4 py-3 font-semibold text-right">Kas Keluar (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($mutasiShift as $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 text-slate-500 font-mono">{{ $m->waktu }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $m->ref }}</td>
                            <td class="px-4 py-3">
                                @if($m->jenis === 'Kas Masuk')
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $m->kategori }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                        {{ $m->kategori }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700 font-medium">{{ $m->uraian }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $m->petugas ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-extrabold text-emerald-600 font-mono">
                                {{ $m->masuk > 0 ? 'Rp ' . number_format($m->masuk, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-extrabold text-rose-600 font-mono">
                                {{ $m->keluar > 0 ? 'Rp ' . number_format($m->keluar, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-6 text-center text-slate-400">Belum ada mutasi kas tercatat pada shift hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Confirm Tutup Shift Kasir -->
        <div x-cloak x-show="showTutupModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showTutupModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-slate-900 text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Formulir Tutup Shift Kasir (End Shift)</h4>
                            <p class="text-[11px] text-slate-500">Rekonsiliasi fisik & penguncian shift 'Siap Tarik Akunting'</p>
                        </div>
                    </div>
                    <button type="button" @click="showTutupModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.buku-kas.tutup') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf

                    <div class="flex-1 overflow-y-auto px-5 py-3.5 space-y-3">
                        <!-- Card Rekonsiliasi Saldo -->
                        <div class="bg-teal-50/70 border border-teal-200 p-3 rounded-xl text-xs space-y-1.5">
                            <div class="flex justify-between items-center">
                                <span class="text-teal-900 font-semibold">Perhitungan Saldo Sistem:</span>
                                <span class="font-mono font-black text-sm text-teal-800">Rp {{ number_format($saldoAkhirShift, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-[11px] text-teal-700 leading-relaxed border-t border-teal-200/60 pt-1.5">
                                Saldo Awal (Rp {{ number_format($saldoAwalShift, 0, ',', '.') }}) + Masuk (Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}) - Keluar (Rp {{ number_format($totalPengeluaranBp, 0, ',', '.') }}).
                            </div>
                        </div>

                        <!-- Input Uang Fisik Di Brankas -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Total Fisik Uang Kas Nyata / Opname (Rp)</label>
                            <input type="number" name="fisik_kas" x-model="fisikKas" required min="0" 
                                   class="w-full text-base font-mono font-black py-2.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-900">
                        </div>

                        <!-- Live Selisih Status -->
                        <div class="p-3 rounded-xl border text-xs" :class="selisih === 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : (selisih > 0 ? 'bg-sky-50 border-sky-200 text-sky-800' : 'bg-rose-50 border-rose-200 text-rose-800')">
                            <div class="flex justify-between items-center font-bold">
                                <span>Status Rekonsiliasi Kas:</span>
                                <span class="font-mono text-sm" x-text="selisih === 0 ? 'SEIMBANG (Rp 0)' : (selisih > 0 ? 'SURPLUS (+Rp ' + new Intl.NumberFormat('id-ID').format(selisih) + ')' : 'DEFISIT (-Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(selisih)) + ')')"></span>
                            </div>
                        </div>

                        <!-- Catatan Serah Terima Shift -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Serah Terima / Rekonsiliasi (Opsional)</label>
                            <textarea name="catatan_serah_terima" x-model="catatanSerahTerima" rows="2" 
                                      placeholder="Contoh: Berkas slip setor BJB telah dimasukkan amplop, saldo kas kecil sisa Rp 1.000.000 diserahkan ke kasir shift berikutnya..." 
                                      class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-900"></textarea>
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showTutupModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-2xs">
                            Kunci & Tutup Shift (Siap Tarik Akunting)
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
