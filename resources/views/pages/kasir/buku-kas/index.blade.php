<x-app-layout>
    <div class="space-y-6" x-data="{ 
        showTutupModal: false,
        saldoSistem: {{ $saldoAkhirShift }},
        kasbonGantung: {{ $totalKasbonGantung }},
        fisikKas: {{ $saldoFisikLaci }},
        catatanSerahTerima: '',
        get selisih() {
            return (Number(this.fisikKas) + Number(this.kasbonGantung)) - Number(this.saldoSistem);
        }
    }">

        <!-- Top Breadcrumbs & Header Bar (OripioFin Style) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5 text-slate-400">
                    <button class="w-8 h-8 rounded-full bg-white border border-slate-200/80 flex items-center justify-center hover:bg-slate-50 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button class="w-8 h-8 rounded-full bg-white border border-slate-200/80 flex items-center justify-center hover:bg-slate-50 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                    <span class="hover:text-slate-600 cursor-pointer">OripioFin</span>
                    <span>&rsaquo;</span>
                    <span class="hover:text-slate-600 cursor-pointer">Buku Kas</span>
                    <span>&rsaquo;</span>
                    <span class="text-slate-800 font-semibold">Buku Kas Harian & Tutup Shift</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($activeShift->status === 'aktif')
                    <button type="button" @click="showTutupModal = true" class="px-4 py-2.5 bg-[#0B6B44] hover:bg-[#095737] text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-2 shrink-0">
                        <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Tutup Shift Kasir (End Shift)</span>
                    </button>
                @else
                    <button type="button" @click="showTutupModal = true" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-2 shrink-0">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Perbarui Rekonsiliasi Tutup Shift</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Alert Flash Message -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl text-xs font-semibold text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200/80 rounded-2xl text-xs font-semibold text-rose-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Live Counter Balance & Shift Status Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100">
                <span class="text-xs font-semibold text-slate-400">Saldo Awal Fisik</span>
                <div class="mt-2 text-lg font-black text-slate-800 font-mono tracking-tight">
                    Rp {{ number_format($saldoAwalShift, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Modal kas kecil awal shift</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100">
                <span class="text-xs font-semibold text-slate-400">Mutasi Masuk Hari Ini</span>
                <div class="mt-2 text-lg font-black text-emerald-600 font-mono tracking-tight">
                    + Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-emerald-600/80 font-medium">BKM Iklan, Sirkulasi & Tunai</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100">
                <span class="text-xs font-semibold text-slate-400">Mutasi Keluar (BKK)</span>
                <div class="mt-2 text-lg font-black text-rose-600 font-mono tracking-tight">
                    - Rp {{ number_format($totalPengeluaranBp, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-rose-500/80 font-medium">Realisasi BP Operasional ACC</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100">
                <span class="text-xs font-bold text-slate-500">Saldo Buku Sistem GL</span>
                <div class="mt-2 text-lg font-black text-slate-900 font-mono tracking-tight">
                    Rp {{ number_format($saldoAkhirShift, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Awal + Masuk - Keluar</div>
            </div>

            <div class="bg-white rounded-2xl border-2 border-emerald-500/30 p-5 shadow-xs shadow-slate-100 bg-emerald-50/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-900">Target Kas Fisik Laci</span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Kasir</span>
                </div>
                <div class="mt-2 text-lg font-black text-[#0B6B44] font-mono tracking-tight">
                    Rp {{ number_format($saldoFisikLaci, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-rose-600 font-medium">(-) Kasbon: Rp {{ number_format($totalKasbonGantung, 0, ',', '.') }}</div>
            </div>

        </div>

        <!-- Breakdown Penerimaan Shift -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100 space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Rincian Sumber Penerimaan Kas Shift Ini
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50/70 border border-slate-200/80">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span class="font-semibold text-slate-700">Setoran Iklan (BKM Faktur)</span>
                    </div>
                    <span class="font-mono font-bold text-slate-900">Rp {{ number_format($totalPenerimaanIklan, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50/70 border border-slate-200/80">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                        <span class="font-semibold text-slate-700">Setoran Sirkulasi (Koran)</span>
                    </div>
                    <span class="font-mono font-bold text-slate-900">Rp {{ number_format($totalSetoranSirkulasi, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50/70 border border-slate-200/80">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                        <span class="font-semibold text-slate-700">Penjualan Langsung / Tunai</span>
                    </div>
                    <span class="font-mono font-bold text-slate-900">Rp {{ number_format($totalPenjualanTunai, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Tabel Mutasi Kas Shift Hari Ini -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs shadow-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0B6B44]"></span>
                    Tabel Mutasi Kas Shift Real-Time (Hari Ini: {{ $today }})
                </h3>
                <span class="text-[11px] text-slate-400 font-medium">BKM Masuk & BKK Keluar Terverifikasi Database</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50/70 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold">Jam</th>
                            <th class="px-5 py-3.5 font-semibold">No. BKM / BKK</th>
                            <th class="px-5 py-3.5 font-semibold">Jenis & Kategori</th>
                            <th class="px-5 py-3.5 font-semibold">Uraian Transaksi</th>
                            <th class="px-5 py-3.5 font-semibold">Petugas / Pihak Terkait</th>
                            <th class="px-5 py-3.5 font-semibold text-right">Kas Masuk (Rp)</th>
                            <th class="px-5 py-3.5 font-semibold text-right">Kas Keluar (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($mutasiShift as $m)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-5 py-3.5 text-slate-400 font-mono">{{ $m->waktu }}</td>
                            <td class="px-5 py-3.5 font-mono font-bold text-slate-800">{{ $m->ref }}</td>
                            <td class="px-5 py-3.5">
                                @if($m->jenis === 'Kas Masuk')
                                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $m->kategori }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-rose-50 text-rose-700 border border-rose-200/60 inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        {{ $m->kategori }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-700 font-medium">{{ $m->uraian }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $m->petugas ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-right font-black text-emerald-600 font-mono">
                                {{ $m->masuk > 0 ? 'Rp ' . number_format($m->masuk, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-black text-rose-600 font-mono">
                                {{ $m->keluar > 0 ? 'Rp ' . number_format($m->keluar, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">Belum ada mutasi kas tercatat pada shift hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Confirm Tutup Shift Kasir -->
        <div x-cloak x-show="showTutupModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showTutupModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Formulir Tutup Shift Kasir (End Shift)</h4>
                            <p class="text-[11px] text-slate-400">Rekonsiliasi fisik & penguncian shift 'Siap Tarik Akunting'</p>
                        </div>
                    </div>
                    <button type="button" @click="showTutupModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.buku-kas.tutup') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf

                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                        <!-- Card Rekonsiliasi Saldo -->
                        <div class="bg-emerald-50/50 border border-emerald-200/80 p-4 rounded-2xl text-xs space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-700 font-semibold">1. Saldo Buku Sistem (GL):</span>
                                <span class="font-mono font-black text-sm text-slate-900">Rp {{ number_format($saldoAkhirShift, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-rose-700 font-semibold">
                                <span>2. (-) Kasbon Gantung Operasional (ACC Keuangan):</span>
                                <span class="font-mono font-bold text-rose-700">- Rp {{ number_format($totalKasbonGantung, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[#0B6B44] font-bold border-t border-emerald-200/60 pt-2">
                                <span>3. Target Kas Fisik di Laci Meja Kasir (1 - 2):</span>
                                <span class="font-mono font-black text-sm text-[#0B6B44]">Rp {{ number_format($saldoFisikLaci, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-[10px] text-slate-500 leading-relaxed pt-1">
                                Rekonsiliasi Kas: Uang Tunai Nyata di Laci + Voucher Kasbon Gantung = Saldo Pembukuan Sistem.
                            </div>
                        </div>

                        <!-- Input Uang Fisik Di Brankas -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Total Fisik Uang Kas Nyata di Laci / Opname (Rp)</label>
                            <input type="number" name="fisik_kas" x-model="fisikKas" required min="0" 
                                   class="w-full text-base font-mono font-black py-2.5 px-3.5 rounded-xl border border-slate-200/80 focus:ring-2 focus:ring-[#0B6B44]/20 focus:border-[#0B6B44] text-slate-900 bg-slate-50/50">
                            <p class="text-[11px] text-slate-400 mt-1">Uang fisik yang dihitung di laci kasir (tidak termasuk lembar voucher kasbon gantung).</p>
                        </div>

                        <!-- Live Selisih Status -->
                        <div class="p-3.5 rounded-xl border text-xs" :class="selisih === 0 ? 'bg-emerald-50 border-emerald-200/80 text-emerald-800' : (selisih > 0 ? 'bg-sky-50 border-sky-200/80 text-sky-800' : 'bg-rose-50 border-rose-200/80 text-rose-800')">
                            <div class="flex justify-between items-center font-bold">
                                <span>Status Rekonsiliasi Kas:</span>
                                <span class="font-mono text-sm" x-text="selisih === 0 ? 'SEIMBANG (Rp 0)' : (selisih > 0 ? 'SURPLUS (+Rp ' + new Intl.NumberFormat('id-ID').format(selisih) + ')' : 'DEFISIT (-Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(selisih)) + ')')"></span>
                            </div>
                        </div>

                        <!-- Catatan Serah Terima Shift -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Serah Terima / Rekonsiliasi (Opsional)</label>
                            <textarea name="catatan_serah_terima" x-model="catatanSerahTerima" rows="2" 
                                      placeholder="Contoh: Berkas slip setor BJB telah dimasukkan amplop, saldo kas kecil sisa Rp 1.000.000 diserahkan ke kasir shift berikutnya..." 
                                      class="w-full text-xs py-2 px-3.5 rounded-xl border border-slate-200/80 focus:ring-2 focus:ring-[#0B6B44]/20 focus:border-[#0B6B44] text-slate-900 bg-slate-50/50"></textarea>
                        </div>
                    </div>

                    <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/60 flex items-center justify-end gap-2.5 shrink-0">
                        <button type="button" @click="showTutupModal = false" class="px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200/80 transition shadow-2xs">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2.5 bg-[#0B6B44] hover:bg-[#095737] text-white font-bold text-xs rounded-xl transition shadow-xs">
                            Kunci & Tutup Shift (Siap Tarik Akunting)
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
