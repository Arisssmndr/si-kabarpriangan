<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Hidden on Print) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Monitoring Kasbon & Uang Muka (Titipan)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Daftar uang muka iklan, deposit agen koran sirkulasi, dan kasbon berjalan (COA 2.00.01.01.01).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 print:hidden no-print">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Daftar Titipan</span>
                </button>
            </div>
        </div>

        <!-- Toolbar Pencarian -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden no-print">
            <form method="GET" action="{{ route('kasir.laporan.kasbon') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Data</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nomor kuitansi, nama pemasang, atau agen penagih..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer">
                        Cari
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('kasir.laporan.kasbon') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Kop Cetak Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-3 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                    <p class="text-[10px] text-slate-600 font-semibold">DIVISI KASIR & KEUANGAN - MONITORING KASBON & TITIPAN</p>
                    <p class="text-[9px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</p>
                </div>
                <div class="text-right">
                    <div class="text-xs font-bold uppercase text-slate-900">DAFTAR KASBON & UANG MUKA (TITIPAN)</div>
                    <div class="text-[10px] font-semibold text-slate-700">Tanggal: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
                    <div class="text-[9px] text-slate-400 font-mono">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- Tabel Monitoring Uang Muka / Titipan -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Uang Muka & Titipan Berjalan</h2>
                    <p class="text-xs text-slate-500">Saldo penerimaan uang muka dan sisa tagihan terkait</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ $daftarUangMuka->total() }} Data
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Kuitansi</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Divisi & COA</th>
                            <th class="py-3 px-3.5">Pemasang / Penyetor</th>
                            <th class="py-3 px-3.5 text-right">Uang Muka Diterima</th>
                            <th class="py-3 px-3.5 text-right">Sisa Tagihan</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($daftarUangMuka as $key => $um)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                {{ $daftarUangMuka->firstItem() + $key }}
                            </td>
                            <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm whitespace-nowrap">{{ $um->no_kuitansi ?? $um->no_bkm }}</td>
                            <td class="py-3.5 px-3.5 font-mono text-slate-600 whitespace-nowrap">{{ $um->tanggal_terima ? \Carbon\Carbon::parse($um->tanggal_terima)->format('d/m/Y') : '-' }}</td>
                            <td class="py-3.5 px-3.5 whitespace-nowrap">
                                <div class="font-medium text-slate-800 text-xs">{{ $um->nama_divisi }}</div>
                                <div class="font-mono text-xs text-slate-500">{{ $um->kode_perkiraan }}</div>
                            </td>
                            <td class="py-3.5 px-3.5">
                                <div class="font-semibold text-slate-900">{{ $um->terima_dari ?? $um->nama_pembayar }}</div>
                                <div class="text-xs text-slate-500">{{ $um->keterangan }}</div>
                            </td>
                            <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums whitespace-nowrap">
                                Rp {{ number_format($um->jumlah_diterima ?? $um->jumlah_dibayar, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums whitespace-nowrap">
                                Rp {{ number_format($um->sisa_piutang, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ strtoupper($um->status_bayar ?? 'UANG MUKA') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">Tidak ada saldo uang muka atau titipan yang aktif.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($daftarUangMuka->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 bg-slate-50 print:hidden">
                    {{ $daftarUangMuka->links() }}
                </div>
            @endif
        </div>

        <!-- Tanda Tangan Laporan Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:grid grid-cols-3 gap-6 text-center text-xs pt-6 mt-6 border-t border-slate-300">
            <div>
                <div class="text-slate-500">Dibuat Oleh:</div>
                <div class="text-slate-700 font-semibold mt-1">Petugas Kasir</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">{{ Auth::user()->name }}</div>
                <div class="text-[10px] text-slate-500">Kasir Priangan</div>
            </div>
            <div>
                <div class="text-slate-500">Diperiksa Oleh:</div>
                <div class="text-slate-700 font-semibold mt-1">Bagian Akuntansi / Pajak</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Kabag Keuangan</div>
            </div>
            <div>
                <div class="text-slate-500">Disetujui Oleh:</div>
                <div class="text-slate-700 font-semibold mt-1">Pemimpin Perusahaan</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Direktur / GM</div>
            </div>
        </div>

    </div>
</x-app-layout>
