<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="print:hidden no-print bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Neraca Keuangan (Balance Sheet)</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-purple-50 text-purple-700 border border-purple-200">Posisi Keuangan</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Laporan posisi aset (aktiva), kewajiban, dan ekuitas (pasiva) perusahaan berbasis standar akuntansi SAK EMKM.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('accounting.laporan.neraca') }}" class="flex items-center gap-2">
                    <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="text-xs rounded-lg border-slate-200 focus:border-purple-500 focus:ring-purple-500">
                    <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition">
                        Filter
                    </button>
                </form>
                <button type="button" onclick="window.print()" class="py-2 px-3 text-xs font-bold rounded-lg bg-purple-600 hover:bg-purple-700 text-white shadow-xs transition">
                    Cetak
                </button>
            </div>
        </div>

        <!-- Kop Cetak Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-3 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                    <p class="text-[10px] text-slate-600 font-semibold">DIVISI KEUANGAN & AKUNTANSI (SAK EMKM)</p>
                    <p class="text-[9px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</p>
                </div>
                <div class="text-right">
                    <div class="text-xs font-black uppercase text-slate-900">NERACA KEUANGAN (BALANCE SHEET)</div>
                    <div class="text-[10px] font-semibold text-slate-700">Per: {{ \Carbon\Carbon::parse($bulanPilihan . '-01')->endOfMonth()->translatedFormat('d F Y') }}</div>
                    <div class="text-[9px] text-slate-400 font-mono">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- 2 Kolom: Aset (Aktiva) vs Kewajiban & Ekuitas (Pasiva) -->
        <div class="report-print-container grid grid-cols-1 lg:grid-cols-2 print:grid-cols-2 gap-6 print:gap-4">
            <!-- Kolom Kiri: ASET -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden p-6 space-y-6">
                <div class="pb-3 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase">AKTIVA / ASET</h3>
                    <span class="text-xs text-slate-500 font-mono">(Rp)</span>
                </div>

                <!-- Aset Lancar -->
                <div class="space-y-2 text-xs">
                    <span class="font-bold text-purple-900 uppercase tracking-wide block">ASET LANCAR</span>
                    <div class="flex justify-between py-1 text-slate-700 pl-3">
                        <span>Kas & Bank (BCA, BJB, Kasir)</span>
                        <span class="font-semibold">{{ number_format($kasBank, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 text-slate-700 pl-3">
                        <span>Piutang Usaha Iklan</span>
                        <span class="font-semibold">{{ number_format($piutangIklan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 text-slate-700 pl-3">
                        <span>Piutang Usaha Agen Sirkulasi</span>
                        <span class="font-semibold">{{ number_format($piutangSirkulasi, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 text-slate-700 pl-3">
                        <span>Persediaan Bahan Baku (Kertas & Tinta)</span>
                        <span class="font-semibold">{{ number_format($persediaan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 font-bold text-slate-900 border-t border-slate-200 bg-slate-50 px-3 rounded">
                        <span>Total Aset Lancar</span>
                        <span class="font-extrabold text-slate-900">Rp {{ number_format($totalAsetLancar, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Aset Tetap -->
                <div class="space-y-2 text-xs pt-2">
                    <span class="font-bold text-purple-900 uppercase tracking-wide block">ASET TETAP</span>
                    <div class="flex justify-between py-1 text-slate-700 pl-3">
                        <span>Mesin Cetak & Komputer Grafis</span>
                        <span class="font-semibold">{{ number_format($mesinPeralatan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 text-slate-700 pl-3">
                        <span>Armada Mobil Distribusi Subuh</span>
                        <span class="font-semibold">{{ number_format($armadaMobil, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 text-rose-600 pl-3">
                        <span>Akumulasi Penyusutan</span>
                        <span class="font-semibold">({{ number_format(abs($akumulasiPenyusutan), 0, ',', '.') }})</span>
                    </div>
                    <div class="flex justify-between py-1.5 font-bold text-slate-900 border-t border-slate-200 bg-slate-50 px-3 rounded">
                        <span>Total Aset Tetap</span>
                        <span class="font-extrabold text-slate-900">Rp {{ number_format($totalAsetTetap, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="p-3.5 bg-purple-50 rounded-xl border border-purple-200 flex justify-between items-center text-xs">
                    <span class="font-extrabold text-purple-950 uppercase">TOTAL AKTIVA (ASET)</span>
                    <span class="text-base font-black text-purple-950">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Kolom Kanan: KEWAJIBAN & EKUITAS -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden p-6 space-y-6">
                <div class="pb-3 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase">PASIVA (KEWAJIBAN & EKUITAS)</h3>
                    <span class="text-xs text-slate-500 font-mono">(Rp)</span>
                </div>

                <!-- Kewajiban Jangka Pendek -->
                <div class="space-y-2 text-xs">
                    <span class="font-bold text-purple-900 uppercase tracking-wide block">KEWAJIBAN LANCAR</span>
                    <div class="flex justify-between py-1 text-slate-700 pl-3">
                        <span>Hutang Usaha Pabrik Percetakan/Kertas</span>
                        <span class="font-semibold">{{ number_format($hutangUsaha, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 text-slate-700 pl-3">
                        <span>Hutang Komisi KH & Insentif AE</span>
                        <span class="font-semibold">{{ number_format($hutangKomisi, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 font-bold text-slate-900 border-t border-slate-200 bg-slate-50 px-3 rounded">
                        <span>Total Kewajiban</span>
                        <span class="font-extrabold text-slate-900">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Ekuitas -->
                <div class="space-y-2 text-xs pt-2">
                    <span class="font-bold text-purple-900 uppercase tracking-wide block">EKUITAS / MODAL</span>
                    <div class="flex justify-between py-1 text-slate-700 pl-3">
                        <span>Modal Disetor PT Kabar Priangan</span>
                        <span class="font-semibold">{{ number_format($modalSaham, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 text-slate-700 pl-3">
                        <span>Laba Ditahan</span>
                        <span class="font-semibold">{{ number_format($labaDitahan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 text-emerald-700 pl-3">
                        <span>Laba Bersih Tahun Berjalan</span>
                        <span class="font-semibold">{{ number_format($labaTahunBerjalan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 font-bold text-slate-900 border-t border-slate-200 bg-slate-50 px-3 rounded">
                        <span>Total Ekuitas</span>
                        <span class="font-extrabold text-slate-900">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="p-3.5 bg-purple-50 rounded-xl border border-purple-200 flex justify-between items-center text-xs">
                    <span class="font-extrabold text-purple-950 uppercase">TOTAL PASIVA</span>
                    <span class="text-base font-black text-purple-950">Rp {{ number_format($totalPasiva, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Tanda Tangan Neraca (Hanya Muncul Saat Print) -->
        <div class="hidden print:grid grid-cols-3 gap-6 text-center text-xs pt-6 mt-6 border-t border-slate-300">
            <div>
                <div class="text-slate-500">Disusun Oleh:</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Staf Akuntansi</div>
            </div>
            <div>
                <div class="text-slate-500">Diperiksa Oleh:</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Kabag Keuangan</div>
            </div>
            <div>
                <div class="text-slate-500">Disetujui Oleh:</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Direktur / General Manager</div>
            </div>
        </div>
    </div>
</x-app-layout>
