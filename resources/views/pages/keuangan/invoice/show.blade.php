<x-app-layout>
    <div class="space-y-6">

        <!-- Header Navigasi & Aksi Cetak Utama -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.invoice.index') }}" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl font-extrabold text-blue-950 tracking-tight">
                            {{ $invoice->no_invoice }}
                        </h1>
                        @if($invoice->status_pembayaran === 'lunas')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                ✓ LUNAS
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                BELUM LUNAS (PIUTANG)
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Diterbitkan: {{ $invoice->tanggal_invoice ? $invoice->tanggal_invoice->translatedFormat('d F Y') : '-' }} &bull; Kepada: <span class="font-bold text-slate-800">{{ $invoice->kepada }}</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <!-- Cetak Invoice (Foto 1) -->
                <a href="{{ route('keuangan.invoice.cetak-invoice', $invoice->id) }}" 
                   target="_blank" 
                   class="h-10 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold flex items-center gap-2 shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak INVOICE Resmi</span>
                </a>

                <!-- Cetak Kwitansi (Foto 2) -->
                <a href="{{ route('keuangan.invoice.cetak-kwitansi', $invoice->id) }}" 
                   target="_blank" 
                   class="h-10 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-2 shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Cetak KWITANSI Resmi</span>
                </a>

                <!-- Edit -->
                <a href="{{ route('keuangan.invoice.edit', $invoice->id) }}" 
                   class="h-10 px-4 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Edit</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold">
                ✓ {{ session('success') }}
            </div>
        @endif

        <!-- Grid Ringkasan Detail Dokumen -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Kolom Kiri: Rincian Penerima & Tagihan -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Card Detail Invoice -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">
                            Rincian Dokumen Invoice &amp; Kwitansi
                        </h2>
                        <span class="text-xs font-mono font-bold text-blue-900 bg-blue-50 px-2.5 py-1 rounded-lg">
                            {{ $invoice->no_invoice }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block mb-0.5">Kepada (Instansi / Customer):</span>
                            <div class="font-bold text-slate-900 text-sm">{{ $invoice->kepada }}</div>
                            <div class="text-slate-600 mt-1">{{ $invoice->alamat ?: '-' }}</div>
                            @if($invoice->attn)
                                <div class="text-slate-500 mt-1">Attn: <span class="font-semibold">{{ $invoice->attn }}</span></div>
                            @endif
                        </div>

                        <div>
                            <span class="text-slate-400 block mb-0.5">Tanggal Penerbitan &amp; Kota:</span>
                            <div class="font-bold text-slate-900">{{ $invoice->kota_terbit }}, {{ $invoice->tanggal_invoice ? $invoice->tanggal_invoice->translatedFormat('d F Y') : '-' }}</div>
                            <div class="mt-2 text-slate-400">Penandatangan:</div>
                            <div class="font-bold text-slate-800">{{ $invoice->nama_penandatangan }}</div>
                            <div class="text-slate-500">{{ $invoice->jabatan_penandatangan }}</div>
                        </div>
                    </div>

                    <!-- Tabel Item Biaya -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden mt-4">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px]">
                                    <th class="py-2.5 px-3">NO</th>
                                    <th class="py-2.5 px-3">Description</th>
                                    <th class="py-2.5 px-3 text-center">Qty</th>
                                    <th class="py-2.5 px-3 text-right">Unit Price (Rp)</th>
                                    <th class="py-2.5 px-3 text-right">Total Amount (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="py-3 px-3 align-top font-bold text-slate-700">1</td>
                                    <td class="py-3 px-3 align-top whitespace-pre-line text-slate-800 font-medium leading-relaxed">
                                        {{ $invoice->deskripsi }}
                                    </td>
                                    <td class="py-3 px-3 align-top text-center font-bold text-slate-700">
                                        {{ $invoice->qty }}
                                    </td>
                                    <td class="py-3 px-3 align-top text-right font-medium text-slate-700">
                                        {{ number_format($invoice->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-3 align-top text-right font-bold text-slate-900">
                                        {{ number_format($invoice->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="border-t border-slate-200 bg-slate-50/60 font-semibold text-slate-700">
                                <tr>
                                    <td colspan="3" class="border-0"></td>
                                    <td class="py-2 px-3 text-right font-bold">DPP:</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900">
                                        Rp {{ number_format($invoice->dpp, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="border-0"></td>
                                    <td class="py-2 px-3 text-right font-bold">Ppn 11%:</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900">
                                        Rp {{ number_format($invoice->ppn_nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="border-t border-slate-300 text-sm bg-blue-50/50">
                                    <td colspan="3" class="border-0"></td>
                                    <td class="py-2.5 px-3 text-right font-black text-blue-950">Total Tagihan:</td>
                                    <td class="py-2.5 px-3 text-right font-black text-blue-900 text-base">
                                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Terbilang Box -->
                    <div class="p-3.5 rounded-xl bg-amber-50/60 border border-amber-200 text-xs">
                        <span class="font-bold text-amber-900 uppercase text-[10px] tracking-wider block mb-1">Terbilang (Sum of):</span>
                        <div class="font-bold italic text-amber-950 text-sm">
                            "{{ $invoice->terbilang }}"
                        </div>
                    </div>

                    <!-- Catatan Transfer Bank -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                        <span class="font-bold text-slate-700 uppercase text-[10px] tracking-wider block mb-1">Catatan Pembayaran Transfer Bank:</span>
                        <div class="font-mono text-slate-700 whitespace-pre-line leading-relaxed">
                            {{ $invoice->catatan_transfer }}
                        </div>
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Quick Print & Hubungan Data Order -->
            <div class="space-y-6">

                <!-- Card Cetak Cepat -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                        Pilihan Cetak Dokumen
                    </h3>

                    <div class="space-y-3">
                        <a href="{{ route('keuangan.invoice.cetak-invoice', $invoice->id) }}" 
                           target="_blank" 
                           class="w-full p-4 rounded-xl border-2 border-blue-200 hover:border-blue-600 bg-blue-50/40 hover:bg-blue-50 transition flex items-center justify-between group cursor-pointer">
                            <div class="flex items-center gap-3">
                                <span class="p-2.5 rounded-xl bg-blue-600 text-white shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                </span>
                                <div>
                                    <div class="font-extrabold text-blue-950 text-xs">Lembar INVOICE Resmi</div>
                                    <div class="text-[10px] text-slate-500">Format A4 / Letter Kop Kabar Priangan</div>
                                </div>
                            </div>
                            <span class="text-blue-600 font-bold text-xs group-hover:translate-x-1 transition">→</span>
                        </a>

                        <a href="{{ route('keuangan.invoice.cetak-kwitansi', $invoice->id) }}" 
                           target="_blank" 
                           class="w-full p-4 rounded-xl border-2 border-emerald-200 hover:border-emerald-600 bg-emerald-50/40 hover:bg-emerald-50 transition flex items-center justify-between group cursor-pointer">
                            <div class="flex items-center gap-3">
                                <span class="p-2.5 rounded-xl bg-emerald-600 text-white shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </span>
                                <div>
                                    <div class="font-extrabold text-emerald-950 text-xs">Lembar KWITANSI Resmi</div>
                                    <div class="text-[10px] text-slate-500">Format Continuous / Bukti Pembayaran Sah</div>
                                </div>
                            </div>
                            <span class="text-emerald-600 font-bold text-xs group-hover:translate-x-1 transition">→</span>
                        </a>
                    </div>
                </div>

                <!-- Info Asal Order -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-3">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                        Sumber Order Transaksi Iklan
                    </h3>

                    @if($invoice->transaksiKoran)
                        <div class="text-xs space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 font-bold text-[10px]">Iklan Koran</span>
                                <span class="font-bold text-slate-800">{{ $invoice->transaksiKoran->nofakturkoran }}</span>
                            </div>
                            <div class="text-[11px] text-slate-600">
                                <div>No. Order: <span class="font-mono font-bold">{{ $invoice->transaksiKoran->no_order ?: '-' }}</span></div>
                                <div>Sales / AE: <span class="font-bold">{{ $invoice->transaksiKoran->sales_iklankoran ?: '-' }}</span></div>
                                <div>Tgl Transaksi: {{ $invoice->transaksiKoran->tanggal_transaksikoran }}</div>
                            </div>
                            <div class="pt-2">
                                <a href="{{ route('transaksikoran.show', $invoice->transaksiKoran->id) }}" 
                                   target="_blank" 
                                   class="text-[11px] font-bold text-blue-600 hover:underline inline-flex items-center gap-1">
                                    <span>Buka Order Koran Asli</span> ↗
                                </a>
                            </div>
                        </div>
                    @elseif($invoice->transaksiOnline)
                        <div class="text-xs space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">Iklan Online</span>
                                <span class="font-bold text-slate-800">{{ $invoice->transaksiOnline->nofakturonline }}</span>
                            </div>
                            <div class="text-[11px] text-slate-600">
                                <div>Portal: <span class="font-bold">{{ $invoice->transaksiOnline->portal_iklanonline ?: 'kabarpriangan.com' }}</span></div>
                                <div>Sales / AE: <span class="font-bold">{{ $invoice->transaksiOnline->sales_iklanonline ?: '-' }}</span></div>
                            </div>
                            <div class="pt-2">
                                <a href="{{ route('transaksionline.show', $invoice->transaksiOnline->id) }}" 
                                   target="_blank" 
                                   class="text-[11px] font-bold text-blue-600 hover:underline inline-flex items-center gap-1">
                                    <span>Buka Order Online Asli</span> ↗
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-xs text-slate-400 italic">
                            Dokumen dibuat langsung / tanpa tautan transaksi internal.
                        </div>
                    @endif
                </div>

            </div>

        </div>

    </div>
</x-app-layout>
