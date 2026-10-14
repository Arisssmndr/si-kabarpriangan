<x-app-layout>
    <div class="space-y-6" x-data="{ showTutupModal: false }">

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
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekonsiliasi mutasi kas harian, verifikasi saldo fisik, dan penutupan shift kasir 'Siap Tarik Akunting'
                    </p>
                </div>
            </div>

            <button type="button" @click="showTutupModal = true" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Tutup Shift Kasir (Siap Tarik Akunting)</span>
            </button>
        </div>

        <!-- Summary Cards Balance & Shift Status -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Saldo Modal Kasir Awal</span>
                <div class="mt-2 text-lg font-extrabold text-slate-800">
                    Rp {{ number_format($saldoAwalShift, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Kembalian awal shift</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Kas Masuk Shift</span>
                <div class="mt-2 text-lg font-extrabold text-emerald-600">
                    + Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Setoran Iklan, Sirkulasi & Tunai</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Kas Keluar Shift (BKK)</span>
                <div class="mt-2 text-lg font-extrabold text-rose-600">
                    - Rp {{ number_format($totalPengeluaranBp, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Realisasi BP Operasional ACC</div>
            </div>

            <div class="bg-white border-2 border-teal-500 rounded-xl p-4 shadow-xs bg-teal-50/20">
                <span class="text-xs font-bold text-teal-800">Saldo Akhir Fisik (Wajib)</span>
                <div class="mt-2 text-lg font-black text-teal-700">
                    Rp {{ number_format($saldoAkhirShift, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-teal-600 font-semibold">Kasir: {{ Auth::user()->name }}</div>
            </div>

        </div>

        <!-- Breakdown Penerimaan Shift -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                Rincian Penerimaan Kas Shift Ini
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
                    Tabel Mutasi Kas Shift (Hari Ini: {{ $today }})
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">Jurnal Transaksi Real-Time</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Jam</th>
                            <th class="px-4 py-3 font-semibold">No. Ref / Struk</th>
                            <th class="px-4 py-3 font-semibold">Kategori Mutasi</th>
                            <th class="px-4 py-3 font-semibold">Uraian / Keterangan</th>
                            <th class="px-4 py-3 font-semibold text-right">Kas Masuk (Rp)</th>
                            <th class="px-4 py-3 font-semibold text-right">Kas Keluar (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($mutasiShift as $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 text-slate-500 font-mono">{{ $m->waktu }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $m->ref }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $m->kategori }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $m->uraian }}</td>
                            <td class="px-4 py-3 text-right font-extrabold text-emerald-600">
                                {{ $m->masuk > 0 ? 'Rp ' . number_format($m->masuk, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-extrabold text-rose-600">
                                {{ $m->keluar > 0 ? 'Rp ' . number_format($m->keluar, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Confirm Tutup Shift Kasir -->
        <div x-cloak x-show="showTutupModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showTutupModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-slate-900 text-emerald-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Konfirmasi Tutup Shift Kasir</h4>
                            <p class="text-[11px] text-slate-500">Status akan diubah menjadi 'Siap Tarik Akunting'</p>
                        </div>
                    </div>
                    <button type="button" @click="showTutupModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.buku-kas.tutup') }}" class="mt-4 space-y-4">
                    @csrf

                    <div class="bg-teal-50 border border-teal-200 p-3.5 rounded-xl text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-teal-700 font-semibold">Perhitungan Saldo Fisik Sistem:</span>
                            <span class="font-mono font-black text-teal-800">Rp {{ number_format($saldoAkhirShift, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[11px] text-teal-600 pt-1">Pastikan uang tunai di brankas kasir sama dengan angka di atas sebelum menutup shift.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Fisik Kas Hasil Opname (Rp)</label>
                        <input type="number" name="fisik_kas" value="{{ $saldoAkhirShift }}" required 
                               class="w-full text-sm font-mono font-bold py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Shift (Opsional)</label>
                        <textarea name="catatan_shift" rows="2" placeholder="Catatan selisih kas / berkas fisik yang diserahterimakan..." 
                                  class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-900"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="showTutupModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-lg transition shadow-2xs">
                            Kunci & Tutup Shift Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
