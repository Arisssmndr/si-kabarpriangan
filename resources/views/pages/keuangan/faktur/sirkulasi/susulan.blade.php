<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-xl shrink-0">
                    ⚡
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            FAKTUR SIRKULASI SUSULAN
                        </h1>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                            Khusus Susulan Oplah
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Kelola faktur untuk penambahan oplah eksemplar koran susulan, kiriman susulan agen, atau permintaan khusus di luar jatah harian
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('keuangan.faktur.create', ['kategori' => 'sirkulasi', 'tipe_faktur' => 'susulan']) }}" 
                   class="h-9 px-4 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Buat Faktur Susulan Baru</span>
                </a>
            </div>
        </div>


        <!-- KPI Ringkas Faktur Susulan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-amber-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700 font-mono">Total Faktur Susulan</span>
                <div class="mt-2 text-2xl font-black text-amber-600 tracking-tight">
                    {{ $totalSusulanCount }} <span class="text-xs font-medium text-slate-500">Dokumen</span>
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    Oplah kiriman tambahan agen
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono">Nilai Tagihan Susulan</span>
                <div class="mt-2 text-2xl font-black text-slate-900 tracking-tight font-mono">
                    Rp {{ number_format($totalSusulanTagihan, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    Total omset oplah susulan
                </div>
            </div>

            <div class="bg-white border border-emerald-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 font-mono">Setoran Terbayar (Kas)</span>
                <div class="mt-2 text-2xl font-black text-emerald-600 tracking-tight font-mono">
                    Rp {{ number_format($totalSusulanTerbayar, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-emerald-700 font-semibold">
                    {{ $totalSusulanTagihan > 0 ? round(($totalSusulanTerbayar / $totalSusulanTagihan) * 100, 1) : 0 }}% tertagih
                </div>
            </div>

            <div class="bg-white border border-rose-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-700 font-mono">Sisa Piutang Susulan</span>
                <div class="mt-2 text-2xl font-black text-rose-600 tracking-tight font-mono">
                    Rp {{ number_format($totalSusulanPiutang, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-rose-700 font-semibold">
                    Belum disetor agen/loper
                </div>
            </div>
        </div>

        <!-- Filter Pencarian -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('keuangan.faktur.sirkulasi.susulan') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-6">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Cari Faktur Susulan</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nomor faktur susulan atau nama agen/loper..." 
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:outline-hidden">
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Status Pembayaran</label>
                    <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:outline-hidden">
                        <option value="">Semua Status</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas (100%)</option>
                        <option value="cicilan" {{ request('status') === 'cicilan' ? 'selected' : '' }}>Cicilan (Ada DP)</option>
                        <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar (Tempo)</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-end gap-2">
                    <button type="submit" class="w-full h-9 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['q', 'status', 'dari_tanggal', 'sampai_tanggal']))
                        <a href="{{ route('keuangan.faktur.sirkulasi.susulan') }}" class="h-9 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center justify-center transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Faktur Susulan -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-amber-50/70 text-[11px] uppercase tracking-wider text-amber-900 font-bold border-b border-amber-200">
                        <tr>
                            <th class="py-3 px-4">No. Faktur Susulan</th>
                            <th class="py-3 px-4">Nama Agen / Toko Koran</th>
                            <th class="py-3 px-4">Catatan Permintaan Oplah</th>
                            <th class="py-3 px-4">Tgl &amp; Jatuh Tempo</th>
                            <th class="py-3 px-4 text-right">Nilai Tagihan</th>
                            <th class="py-3 px-4 text-right">Terbayar</th>
                            <th class="py-3 px-4 text-right">Sisa Piutang</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($fakturs as $faktur)
                            <tr class="hover:bg-amber-50/20 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-amber-900">
                                    <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" class="hover:underline flex items-center gap-1.5">
                                        <span class="text-xs">⚡</span>
                                        <span>{{ $faktur->no_faktur }}</span>
                                    </a>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $faktur->customer->nama_customer ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400">Wilayah: {{ Str::limit($faktur->customer->alamat ?? '-', 35) }}</div>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="text-slate-800 line-clamp-2">{{ $faktur->catatan ?? '-' }}</div>
                                    @if(!empty($faktur->rincian_biaya) && is_array($faktur->rincian_biaya))
                                        <div class="text-[10px] text-amber-700 mt-0.5">
                                            {{ count($faktur->rincian_biaya) }} item oplah terdaftar
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-[11px]">
                                    <div>Tgl: {{ $faktur->tanggal_faktur->format('d/m/Y') }}</div>
                                    <div class="text-slate-400 mt-0.5">Tempo: {{ $faktur->jatuh_tempo->format('d/m/Y') }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-emerald-600 font-mono">
                                    Rp {{ number_format($faktur->total_terbayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold {{ $faktur->sisa_piutang > 0 ? 'text-rose-600' : 'text-slate-400' }} font-mono">
                                    Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($faktur->status === 'lunas')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            ✓ Lunas
                                        </span>
                                    @elseif($faktur->status === 'cicilan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            Cicilan / DP
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            Belum Bayar
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" 
                                           class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition" 
                                           title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('keuangan.faktur.cetak', $faktur->id) }}" target="_blank"
                                           class="p-1.5 text-slate-500 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition"
                                           title="Cetak Faktur">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center">
                                    <div class="text-3xl mb-2">⚡</div>
                                    <p class="text-sm font-semibold text-slate-600">Belum ada faktur sirkulasi susulan.</p>
                                    <p class="text-xs text-slate-400 mt-1">Gunakan tombol "+ Buat Faktur Susulan Baru" saat ada penambahan oplah susulan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($fakturs->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $fakturs->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
