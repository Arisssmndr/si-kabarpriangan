<x-app-layout>
    <div class="space-y-5" x-data="{ 
        showTutupModal: false,
        saldoSistem: {{ $saldoAkhirShift }},
        kasbonGantung: {{ $totalKasbonGantung }},
        fisikKas: {{ $saldoFisikLaci }},
        catatanSerahTerima: '',
        get selisih() {
            return (Number(this.fisikKas) + Number(this.kasbonGantung)) - Number(this.saldoSistem);
        }
    }">

        <!-- Header Halaman (60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Buku Kas Harian & Tutup Shift
                        </h1>
                        @if($activeShift->status === 'ditutup')
                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                Shift Ditutup
                            </span>
                        @else
                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                Shift Aktif
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Rekonsiliasi mutasi kas harian, verifikasi saldo fisik, dan penutupan shift kasir.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button" @click="showTutupModal = true" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $activeShift->status === 'aktif' ? 'Tutup Shift Kasir' : 'Perbarui Tutup Shift' }}</span>
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
                <button type="button" @click="$el.parentElement.remove()" class="text-kp-blue-600 hover:text-kp-blue-800 font-bold px-1 cursor-pointer">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
                <button type="button" @click="$el.parentElement.remove()" class="text-slate-500 hover:text-slate-800 font-bold px-1 cursor-pointer">&times;</button>
            </div>
        @endif

        <!-- 5 Kartu KPI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Awal Fisik</span>
                <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($saldoAwalShift, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Modal awal shift</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Mutasi Masuk</span>
                <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Iklan, Sirkulasi & Tunai</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Mutasi Keluar (BKK)</span>
                <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalPengeluaranBp, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Realisasi BP ACC</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Buku Sistem</span>
                <p class="text-xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($saldoAkhirShift, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Awal + Masuk - Keluar</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Target Kas Fisik Laci</span>
                <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($saldoFisikLaci, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block font-mono">Kasbon: Rp {{ number_format($totalKasbonGantung, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Rincian Sumber Penerimaan Kas Shift -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-3">
            <h2 class="text-sm font-bold text-slate-900">Rincian Sumber Penerimaan Shift</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 text-sm">
                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="font-medium text-slate-700">Setoran Iklan (BKM Faktur)</span>
                    <span class="font-mono font-bold text-slate-900 tabular-nums">Rp {{ number_format($totalPenerimaanIklan, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="font-medium text-slate-700">Setoran Sirkulasi (Koran)</span>
                    <span class="font-mono font-bold text-slate-900 tabular-nums">Rp {{ number_format($totalSetoranSirkulasi, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="font-medium text-slate-700">Penjualan Langsung / Tunai</span>
                    <span class="font-mono font-bold text-slate-900 tabular-nums">Rp {{ number_format($totalPenjualanTunai, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Tabel Mutasi Kas Shift Hari Ini -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Mutasi Kas Shift ({{ $today }})</h2>
                    <p class="text-xs text-slate-500">Daftar bukti kas masuk (BKM) dan kas keluar (BKK) hari ini</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ count($mutasiShift) }} Transaksi
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Jam</th>
                            <th class="py-3 px-3.5">No. BKM / BKK</th>
                            <th class="py-3 px-3.5">Kategori</th>
                            <th class="py-3 px-3.5">Uraian Transaksi</th>
                            <th class="py-3 px-3.5">Petugas / Pihak</th>
                            <th class="py-3 px-3.5 text-right">Kas Masuk</th>
                            <th class="py-3 px-3.5 text-right">Kas Keluar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($mutasiShift as $idx => $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-3.5 text-slate-600 font-mono whitespace-nowrap">{{ $m->waktu }}</td>
                            <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm whitespace-nowrap">{{ $m->ref }}</td>
                            <td class="py-3.5 px-3.5 whitespace-nowrap">
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
                            <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $m->uraian }}</td>
                            <td class="py-3.5 px-3.5 text-slate-600 whitespace-nowrap">{{ $m->petugas ?? '-' }}</td>
                            <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums whitespace-nowrap">
                                {{ $m->masuk > 0 ? 'Rp ' . number_format($m->masuk, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums whitespace-nowrap">
                                {{ $m->keluar > 0 ? 'Rp ' . number_format($m->keluar, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">Belum ada mutasi kas tercatat pada shift hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tutup Shift Kasir -->
        <div x-cloak x-show="showTutupModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="showTutupModal = false" class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tutup Shift Kasir</h3>
                        <p class="text-xs text-slate-500">Rekonsiliasi uang fisik dan penguncian shift</p>
                    </div>
                    <button type="button" @click="showTutupModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.buku-kas.tutup') }}" class="p-5 space-y-4">
                    @csrf

                    <div class="bg-slate-50 border border-slate-200 p-3.5 rounded-xl text-xs space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 font-medium">1. Saldo Buku Sistem (GL):</span>
                            <span class="font-mono font-bold text-slate-900">Rp {{ number_format($saldoAkhirShift, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 font-medium">2. (-) Kasbon Gantung Aktif:</span>
                            <span class="font-mono font-semibold text-slate-700">- Rp {{ number_format($totalKasbonGantung, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center font-bold border-t border-slate-200 pt-2">
                            <span class="text-slate-900">3. Target Kas Fisik Laci (1 - 2):</span>
                            <span class="font-mono text-sm text-kp-blue-700">Rp {{ number_format($saldoFisikLaci, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Total Fisik Uang Kas di Laci (Rp)</label>
                        <input type="number" name="fisik_kas" x-model="fisikKas" required min="0" 
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-base font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600">
                    </div>

                    <div class="p-3 rounded-xl border text-xs" :class="selisih === 0 ? 'bg-kp-blue-50 border-kp-blue-200 text-kp-blue-700' : 'bg-slate-100 border-slate-300 text-slate-800'">
                        <div class="flex justify-between items-center font-bold">
                            <span>Status Rekonsiliasi:</span>
                            <span class="font-mono text-sm" x-text="selisih === 0 ? 'SEIMBANG (Rp 0)' : (selisih > 0 ? 'SURPLUS (+Rp ' + new Intl.NumberFormat('id-ID').format(selisih) + ')' : 'DEFISIT (-Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(selisih)) + ')')"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Serah Terima (Opsional)</label>
                        <textarea name="catatan_serah_terima" x-model="catatanSerahTerima" rows="2" 
                                  placeholder="Catatan serah terima shift..." 
                                  class="w-full p-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showTutupModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                            Kunci & Tutup Shift
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
