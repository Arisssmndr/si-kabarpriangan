<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <h1 class="text-xl font-extrabold text-blue-950 tracking-tight">
                            INVOICE &amp; KWITANSI IKLAN
                        </h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Divisi Faktur &amp; Piutang &bull; Penagihan Resmi Order Iklan (Format Kop Harian Umum Kabar Priangan)
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('keuangan.invoice.create') }}" 
                   class="h-10 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold flex items-center gap-2 shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>+ Buat Invoice &amp; Kwitansi Baru</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Card Statistik Ringkasan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase">Total Invoice Diterbitkan</span>
                    <span class="p-2 rounded-xl bg-blue-50 text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-800 mt-2">{{ $countTotal }} <span class="text-xs font-medium text-slate-400">Dokumen</span></div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase">Akumulasi Nilai Tagihan</span>
                    <span class="p-2 rounded-xl bg-purple-50 text-purple-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="text-xl font-black text-slate-800 mt-2">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase">Sudah Lunas</span>
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                </div>
                <div class="text-xl font-black text-emerald-600 mt-2">Rp {{ number_format($totalLunas, 0, ',', '.') }}</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase">Belum Lunas (Piutang)</span>
                    <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="text-xl font-black text-amber-600 mt-2">Rp {{ number_format($totalBelumLunas, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            <form action="{{ route('keuangan.invoice.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Cari No Invoice / Instansi / Tema</label>
                    <div class="relative">
                        <input type="text" 
                               name="q" 
                               value="{{ request('q') }}" 
                               placeholder="Contoh: 006/KEU-HU.KP, Prokopim, Global Cities..." 
                               class="w-full text-xs rounded-xl border border-slate-300 pl-9 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Status Pembayaran</label>
                    <select name="status" class="w-full text-xs rounded-xl border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="belum_lunas" {{ request('status') === 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Sudah Lunas</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="w-full text-xs rounded-xl border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="w-full text-xs rounded-xl border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit" class="h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition cursor-pointer shrink-0">
                        Filter
                    </button>
                    @if(request()->anyFilled(['q', 'status', 'dari_tanggal', 'sampai_tanggal']))
                        <a href="{{ route('keuangan.invoice.index') }}" class="h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold flex items-center justify-center transition" title="Reset Filter">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Invoice & Kwitansi -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px] tracking-wider">
                            <th class="py-3 px-4">No. Invoice &amp; Tgl</th>
                            <th class="py-3 px-4">Kepada / Instansi</th>
                            <th class="py-3 px-4">Deskripsi / Tema Iklan</th>
                            <th class="py-3 px-4 text-right">DPP (Rp)</th>
                            <th class="py-3 px-4 text-right">PPN 11% (Rp)</th>
                            <th class="py-3 px-4 text-right">Total Tagihan (Rp)</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi &amp; Cetak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($invoices as $inv)
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-blue-900">{{ $inv->no_invoice }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $inv->tanggal_invoice ? $inv->tanggal_invoice->translatedFormat('d M Y') : '-' }}
                                    </div>
                                    @if($inv->transaksiKoran)
                                        <span class="inline-block mt-1 text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-sky-100 text-sky-800">
                                            Koran: {{ $inv->transaksiKoran->nofakturkoran }}
                                        </span>
                                    @elseif($inv->transaksiOnline)
                                        <span class="inline-block mt-1 text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800">
                                            Online: {{ $inv->transaksiOnline->nofakturonline }}
                                        </span>
                                    @elseif($inv->no_faktur_iklan)
                                        <span class="inline-block mt-1 text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-800">
                                            Faktur: {{ $inv->no_faktur_iklan }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $inv->kepada }}</div>
                                    <div class="text-[11px] text-slate-500 truncate max-w-xs" title="{{ $inv->alamat }}">
                                        {{ $inv->alamat ?: '-' }}
                                    </div>
                                    @if($inv->attn)
                                        <div class="text-[10px] text-slate-400 mt-0.5">Attn: {{ $inv->attn }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="text-xs text-slate-800 whitespace-pre-line line-clamp-2 max-w-sm" title="{{ $inv->deskripsi }}">
                                        {{ $inv->deskripsi }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-1 italic">
                                        Qty: {{ $inv->qty }} &bull; Terbilang: {{ $inv->terbilang }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-700">
                                    {{ number_format($inv->dpp, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-700">
                                    {{ number_format($inv->ppn_nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900 text-sm">
                                    Rp {{ number_format($inv->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($inv->status_pembayaran === 'lunas')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            ✓ LUNAS
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            BELUM LUNAS
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        <!-- Cetak Invoice (Foto 1) -->
                                        <a href="{{ route('keuangan.invoice.cetak-invoice', $inv->id) }}" 
                                           target="_blank" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-[11px] transition shadow-2xs"
                                           title="Cetak Lembar Invoice Kop Resmi Kabar Priangan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            <span>Invoice</span>
                                        </a>

                                        <!-- Cetak Kwitansi (Foto 2) -->
                                        <a href="{{ route('keuangan.invoice.cetak-kwitansi', $inv->id) }}" 
                                           target="_blank" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-[11px] transition shadow-2xs"
                                           title="Cetak Lembar Kwitansi Resmi Pembayaran">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span>Kwitansi</span>
                                        </a>

                                        <!-- Detail -->
                                        <a href="{{ route('keuangan.invoice.show', $inv->id) }}" 
                                           class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition" 
                                           title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        <!-- Edit -->
                                        <a href="{{ route('keuangan.invoice.edit', $inv->id) }}" 
                                           class="p-1.5 rounded-lg text-amber-600 hover:text-amber-800 hover:bg-amber-50 transition" 
                                           title="Edit Invoice">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <!-- Hapus -->
                                        <form action="{{ route('keuangan.invoice.destroy', $inv->id) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice {{ $inv->no_invoice }}?')" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition cursor-pointer" 
                                                    title="Hapus Invoice">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="font-bold text-sm text-slate-600">Belum Ada Dokumen Invoice &amp; Kwitansi</span>
                                        <p class="text-xs text-slate-400 mt-1">Klik tombol "+ Buat Invoice &amp; Kwitansi Baru" untuk mengambil data dari order transaksi iklan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($invoices->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
