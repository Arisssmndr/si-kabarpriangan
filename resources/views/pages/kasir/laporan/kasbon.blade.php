<x-app-layout>
    <div class="space-y-6">

        <!-- Header Monitoring Kasbon & Uang Muka (Hidden on Print) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Monitoring Kasbon & Uang Muka (Titipan)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-100 text-amber-800 border border-amber-300">
                            COA 2.00.01.01.01
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pengawasan uang muka pemasangan iklan, titipan deposit agen koran sirkulasi, serta kasbon berjalan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 print:hidden no-print">
                <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Daftar Titipan</span>
                </button>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs print:hidden no-print">
            <form method="GET" action="{{ route('kasir.laporan.kasbon') }}" class="flex items-center gap-3">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nomor kuitansi, nama pemasang, atau agen penagih..."
                           class="w-full text-xs py-2 pl-9 pr-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-800">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-lg transition">
                    Cari
                </button>
            </form>
        </div>

        <!-- Kop Cetak Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-3 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                    <p class="text-[10px] text-slate-600 font-semibold">DIVISI KASIR & KEUANGAN - MONITORING KASBON & TITIPAN</p>
                    <p class="text-[9px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</p>
                </div>
                <div class="text-right">
                    <div class="text-xs font-black uppercase text-slate-900">DAFTAR KASBON & UANG MUKA (TITIPAN)</div>
                    <div class="text-[10px] font-semibold text-slate-700">Tanggal: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
                    <div class="text-[9px] text-slate-400 font-mono">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- Tabel Monitoring Uang Muka / Titipan -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-amber-50/40">
                <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                    Daftar Uang Muka / Titipan Kasir Berjalan
                </h3>
                <span class="text-xs font-bold text-slate-500">Total: {{ $daftarUangMuka->total() }} Data</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. Kuitansi</th>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Divisi & COA</th>
                            <th class="px-4 py-3 font-semibold">Pemasang / Penyetor</th>
                            <th class="px-4 py-3 font-semibold text-right">Uang Muka Diterima (Rp)</th>
                            <th class="px-4 py-3 font-semibold text-right">Sisa Tagihan (Rp)</th>
                            <th class="px-4 py-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($daftarUangMuka as $um)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-amber-900">{{ $um->no_kuitansi ?? $um->no_bkm }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $um->tanggal_terima ? \Carbon\Carbon::parse($um->tanggal_terima)->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 font-bold text-[10px] text-slate-700">{{ $um->nama_divisi }}</span>
                                <div class="font-mono text-[10px] text-slate-500">{{ $um->kode_perkiraan }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800">{{ $um->terima_dari ?? $um->nama_pembayar }}</div>
                                <div class="text-[10px] text-slate-500">{{ $um->keterangan }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-black text-amber-800">
                                Rp {{ number_format($um->jumlah_diterima ?? $um->jumlah_dibayar, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-700">
                                Rp {{ number_format($um->sisa_piutang, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    {{ strtoupper($um->status_bayar ?? 'UANG MUKA') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-6 text-center text-slate-400">Tidak ada saldo uang muka atau titipan yang aktif.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($daftarUangMuka->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 print:hidden">
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
