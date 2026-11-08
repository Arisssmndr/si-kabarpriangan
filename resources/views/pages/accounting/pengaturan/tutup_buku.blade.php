<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Tutup Buku Periode (Closing)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-rose-50 text-rose-700 border border-rose-200">
                            Periode Lock
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Penguncian transaksi periode bulanan/tahunan untuk mencegah perubahan data lampau.
                    </p>
                </div>
            </div>
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Checklist & Action Tutup Buku -->
            <div class="lg:col-span-5 bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">
                    Proses Tutup Buku Bulanan
                </h2>

                <div class="space-y-3">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">&check;</span>
                        <div>
                            <div class="text-xs font-bold text-slate-900">1. Penjualan Iklan &amp; Sirkulasi</div>
                            <div class="text-[11px] text-slate-500">Seluruh omset harian &amp; bulanan telah dihitung.</div>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">&check;</span>
                        <div>
                            <div class="text-xs font-bold text-slate-900">2. Kasir &amp; Rekonsiliasi Bank</div>
                            <div class="text-[11px] text-slate-500">Mutasi kasir harian dan rekening koran telah tervalidasi.</div>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">&check;</span>
                        <div>
                            <div class="text-xs font-bold text-slate-900">3. Keseimbangan Jurnal GL</div>
                            <div class="text-[11px] text-slate-500">Semua jurnal umum dan penyesuaian seimbang (Debet = Kredit).</div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('accounting.pengaturan.tutup-buku.proses') }}" class="pt-3 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Periode Bulan yang Ditutup</label>
                        <input type="month" 
                               name="periode_key" 
                               value="{{ date('Y-m') }}" 
                               required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition" />
                    </div>

                    <button type="submit" 
                            onclick="return confirm('PERINGATAN: Tutup buku akan mengunci transaksi pada periode ini! Lanjutkan?')"
                            class="w-full h-11 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        <span>Kunci &amp; Tutup Buku Periode</span>
                    </button>
                </form>
            </div>

            <!-- Tabel Status Periode Pembukuan -->
            <div class="lg:col-span-7 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Riwayat Status Periode Akuntansi</h2>
                        <p class="text-xs text-slate-500">Daftar periode terkunci dan aktif.</p>
                    </div>
                </div>

                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 w-12 text-center">No</th>
                                <th class="py-3 px-3.5">Bulan &amp; Tahun</th>
                                <th class="py-3 px-3.5">Status</th>
                                <th class="py-3 px-3.5">Tanggal Tutup</th>
                                <th class="py-3 px-3.5">Ditutup Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @forelse($periodeList as $key => $p)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-3.5 text-center text-xs text-slate-400 font-mono">{{ $key + 1 }}</td>
                                    <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $p->periode_key ?: sprintf('%04d-%02d', $p->tahun, $p->bulan) }}</td>
                                    <td class="py-3 px-3.5">
                                        @php $isLocked = ($p->status === 'terkunci'); @endphp
                                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md {{ $isLocked ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                            {{ $isLocked ? 'Terkunci (Closed)' : 'Aktif (Open)' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3.5 font-mono text-slate-600">{{ $p->ditutup_pada ? \Carbon\Carbon::parse($p->ditutup_pada)->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="py-3 px-3.5 text-slate-800">{{ $p->ditutupOleh?->name ?? ($isLocked ? 'Administrator' : '-') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 text-sm">
                                        Belum ada riwayat periode pembukuan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

