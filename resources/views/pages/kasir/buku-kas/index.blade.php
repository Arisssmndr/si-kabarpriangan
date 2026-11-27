<x-app-layout>
    <div class="space-y-5" x-data="{ 
        showTutupModal: false,
        saldoSistem: {{ $saldoAkhirShift }},
        fisikKas: {{ $saldoFisikLaci }},
        catatanSerahTerima: '',
        get selisih() {
            return Number(this.fisikKas) - Number(this.saldoSistem);
        }
    }">

        <!-- Alert Flash Message -->
        @if(session('success'))
            <div class="p-4 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-xs font-semibold text-kp-blue-800 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-kp-blue-500 hover:text-kp-blue-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-slate-100 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-slate-500 hover:text-slate-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- 1. Header Page Buku Kas & Tutup Shift (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Buku Kas Harian & Tutup Shift Kasir
                        </h1>
                        @if(in_array($activeShift->status, ['closed', 'ditutup']))
                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                Shift Ditutup (Siap Tarik Akunting)
                            </span>
                        @elseif($activeShift->reopen_status === 'approved')
                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                Dibuka Kembali (ACC Accounting)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-kp-blue-600 animate-pulse"></span>
                                Shift Aktif
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Rekonsiliasi mutasi kas harian, verifikasi saldo fisik, dan penutupan shift kasir 'Siap Tarik Akunting'
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if(in_array($activeShift->status, ['open', 'aktif']))
                    <button type="button" @click="showTutupModal = true" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Tutup Shift Kasir</span>
                    </button>
                @else
                    <a href="{{ route('kasir.utilitas.buka-kas') }}" class="inline-flex items-center gap-2 h-10 px-4 bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                        <span>Ajukan Buka Kembali Kas</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- 2. Live Counter Balance Cards (60-30-10 Palette) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Awal Kasir</span>
                <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($saldoAwalShift, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Modal kas kecil kemarin</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Mutasi Masuk (KI)</span>
                <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    + Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Total BKM Shift Ini</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Mutasi Keluar (KO)</span>
                <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    - Rp {{ number_format($totalPengeluaranBp, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Realisasi BP ACC</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Buku Sistem</span>
                <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($saldoAkhirShift, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Awal + KI - KO</span>
            </div>

            <div class="bg-white border border-kp-blue-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-kp-blue-800 uppercase tracking-wider block">Target Kas Fisik</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Kasir</span>
                </div>
                <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($saldoFisikLaci, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">Saldo tunai bersih di laci</span>
            </div>

        </div>

        <!-- 3. Rincian Sumber Penerimaan Kas Shift Ini -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-2 h-2 rounded-full bg-kp-blue-600"></span>
                Rincian Sumber Penerimaan Kas Shift Ini
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-kp-blue-600"></span>
                        <span class="font-semibold text-slate-800 text-sm">Setoran Iklan (BKM)</span>
                    </div>
                    <span class="font-mono font-bold text-slate-900 text-sm">Rp {{ number_format($totalPenerimaanIklan, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        <span class="font-semibold text-slate-800 text-sm">Setoran Koran Agen</span>
                    </div>
                    <span class="font-mono font-bold text-slate-900 text-sm">Rp {{ number_format($totalSetoranSirkulasi, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-slate-600"></span>
                        <span class="font-semibold text-slate-800 text-sm">Penerimaan Tunai / Lain</span>
                    </div>
                    <span class="font-mono font-bold text-slate-900 text-sm">Rp {{ number_format($totalPenjualanTunai, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- 4. Pure Table Mutasi Kas Shift Hari Ini -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] border-b-2 border-[#FFB900] text-white font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3 font-semibold text-white">Jam</th>
                            <th class="py-3 px-3 font-semibold text-white">No. BKM / BKK</th>
                            <th class="py-3 px-3 font-semibold text-white">Jenis & Kategori</th>
                            <th class="py-3 px-3 font-semibold text-white">Uraian Transaksi</th>
                            <th class="py-3 px-3 font-semibold text-white">Petugas / Pihak Terkait</th>
                            <th class="py-3 px-3 font-semibold text-white text-right">Kas Masuk (Rp)</th>
                            <th class="py-3 px-3 font-semibold text-white text-right">Kas Keluar (Rp)</th>
                            <th class="py-3 px-3 font-semibold text-white text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                        @forelse($mutasiShift as $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 text-slate-500 font-mono">{{ $m->waktu }}</td>
                            <td class="py-3 px-3 font-mono font-bold text-kp-blue-700">{{ $m->ref }}</td>
                            <td class="py-3 px-3">
                                @if($m->jenis === 'Kas Masuk')
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        {{ $m->kategori }}
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $m->kategori }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-700 font-medium max-w-[250px] truncate" title="{{ $m->uraian }}">{{ $m->uraian }}</td>
                            <td class="py-3 px-3 text-slate-500">{{ $m->petugas ?? '-' }}</td>
                            <td class="py-3 px-3 text-right font-mono font-semibold text-slate-900">
                                {{ $m->masuk > 0 ? 'Rp ' . number_format($m->masuk, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-semibold text-slate-900">
                                {{ $m->keluar > 0 ? 'Rp ' . number_format($m->keluar, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <a href="{{ route('kasir.utilitas.koreksi') }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Edit</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400 text-xs">Belum ada mutasi kas tercatat pada shift hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Confirm Tutup Shift Kasir -->
        <div x-cloak x-show="showTutupModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showTutupModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between shrink-0 bg-slate-50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-kp-blue-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Formulir Tutup Shift Kasir</h4>
                            <p class="text-xs text-slate-500">Rekonsiliasi fisik & penguncian shift 'Siap Tarik Akunting'</p>
                        </div>
                    </div>
                    <button type="button" @click="showTutupModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.buku-kas.tutup') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf

                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3.5">
                        <!-- Card Rekonsiliasi Saldo -->
                        <div class="bg-slate-50 border border-slate-200 p-3.5 rounded-xl text-xs space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-700 font-semibold">1. Saldo Buku Sistem (GL):</span>
                                <span class="font-mono font-bold text-sm text-slate-900">Rp {{ number_format($saldoAkhirShift, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-slate-900 font-bold border-t border-slate-200 pt-1.5">
                                <span>2. Target Kas Fisik di Laci Meja Kasir:</span>
                                <span class="font-mono font-bold text-sm text-kp-blue-700">Rp {{ number_format($saldoFisikLaci, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 leading-relaxed pt-0.5">
                                Rekonsiliasi Kas: Uang Tunai Nyata di Laci = Saldo Pembukuan Sistem.
                            </div>
                        </div>

                        <!-- Input Uang Fisik Di Brankas -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Total Fisik Uang Kas Nyata di Laci / Opname (Rp)</label>
                            <input type="number" name="fisik_kas" x-model="fisikKas" required min="0" 
                                   class="w-full h-11 px-3.5 text-base font-mono font-bold rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 text-slate-900">
                            <p class="text-xs text-slate-500 mt-1">Uang fisik yang dihitung di laci kasir sesuai saldo pembukuan sistem.</p>
                        </div>

                        <!-- Live Selisih Status -->
                        <div class="p-3 rounded-xl border text-xs" :class="selisih === 0 ? 'bg-kp-blue-50 border-kp-blue-200 text-kp-blue-800' : (selisih > 0 ? 'bg-slate-50 border-slate-300 text-slate-800' : 'bg-slate-100 border-slate-300 text-slate-900')">
                            <div class="flex justify-between items-center font-bold">
                                <span>Status Rekonsiliasi Kas:</span>
                                <span class="font-mono text-sm" x-text="selisih === 0 ? 'SEIMBANG (Rp 0)' : (selisih > 0 ? 'SURPLUS (+Rp ' + new Intl.NumberFormat('id-ID').format(selisih) + ')' : 'DEFISIT (-Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(selisih)) + ')')"></span>
                            </div>
                        </div>

                        <!-- Catatan Serah Terima Shift -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Serah Terima / Rekonsiliasi (Opsional)</label>
                            <textarea name="catatan_serah_terima" x-model="catatanSerahTerima" rows="2" 
                                      placeholder="Contoh: Berkas slip setor BJB telah dimasukkan amplop, saldo kas kecil sisa Rp 1.000.000 diserahkan ke kasir shift berikutnya..." 
                                      class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 text-slate-900"></textarea>
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showTutupModal = false" class="h-10 px-4 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 transition">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl transition shadow-2xs">
                            Kunci & Tutup Shift
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>