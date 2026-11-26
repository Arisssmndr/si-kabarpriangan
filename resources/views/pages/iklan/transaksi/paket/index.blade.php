<x-app-layout>
    <div class="space-y-5">

        <!-- 1. Header Standar Menu Divisi Iklan (60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Transaksi Paket Iklan (Lintas Media)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Bundling terpadu Iklan Cetak, Online, dan Priangan TV dalam 1 Faktur Paket resmi.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('transaksipaket.create') }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Buat Paket Baru</span>
                </a>
            </div>
        </div>

        <!-- 2. Filter & Pencarian -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('transaksipaket.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Paket</label>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Cari No. Faktur Paket, Pemasang, atau Sales..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kombinasi Media</label>
                    <select name="media" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="">Semua Media</option>
                        <option value="CTK" {{ request('media') === 'CTK' ? 'selected' : '' }}>Mengandung Iklan Cetak</option>
                        <option value="ONL" {{ request('media') === 'ONL' ? 'selected' : '' }}>Mengandung Iklan Online</option>
                        <option value="PTV" {{ request('media') === 'PTV' ? 'selected' : '' }}>Mengandung Iklan TV</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Pembayaran</label>
                    <select name="status" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="">Semua</option>
                        <option value="tunai" {{ request('status') === 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="kredit" {{ request('status') === 'kredit' ? 'selected' : '' }}>Kredit</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-sm rounded-xl shadow-xs transition cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'media', 'status']))
                        <a href="{{ route('transaksipaket.index') }}" class="h-11 px-3.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 3. Tabel Daftar Paket Iklan (60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3.5 px-4 text-center w-12">No</th>
                            <th class="py-3.5 px-4">No. Faktur Paket</th>
                            <th class="py-3.5 px-4">Tanggal</th>
                            <th class="py-3.5 px-4">Pemasang</th>
                            <th class="py-3.5 px-4">Sales / AE</th>
                            <th class="py-3.5 px-4">Nama Paket &amp; Kanal Media</th>
                            <th class="py-3.5 px-4 text-right">Total Tagihan (Rp)</th>
                            <th class="py-3.5 px-4 text-center">Pembayaran</th>
                            <th class="py-3.5 px-4 text-center w-14">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($paketList as $idx => $p)
                            @php
                                $isTunai = ($p->jenis_pembayaran === 'Tunai' || ($p->total_tagihan > 0 && $p->jumlah_bayar >= $p->total_tagihan));
                                $mediaArr = is_array($p->media_terpilih) ? $p->media_terpilih : [];
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 text-center text-slate-500">
                                    {{ $paketList->firstItem() + $idx }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-kp-blue-700">
                                    <a href="{{ route('transaksipaket.show', $p->id) }}" class="hover:underline">
                                        {{ $p->nofaktur_paket }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">
                                    {{ \Carbon\Carbon::parse($p->tanggal_transaksi)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $p->nama_pemasang }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $p->salesAe?->nama_ae ?: ($p->sales_nama ?: '-') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-800 text-xs">{{ $p->nama_paket ?: 'Paket Bundling' }}</div>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        @if(in_array('CTK', $mediaArr) || $p->transaksiCetak)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">
                                                Cetak {{ $p->transaksiCetak?->nofakturkoran ? '(' . $p->transaksiCetak->nofakturkoran . ')' : '' }}
                                            </span>
                                        @endif
                                        @if(in_array('ONL', $mediaArr) || $p->transaksiOnline)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                                                Online {{ $p->transaksiOnline?->nofakturonline ? '(' . $p->transaksiOnline->nofakturonline . ')' : '' }}
                                            </span>
                                        @endif
                                        @if(in_array('PTV', $mediaArr) || $p->transaksiTv)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                                                TV {{ $p->transaksiTv?->nofakturpriangan ? '(' . $p->transaksiTv->nofakturpriangan . ')' : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 tabular-nums">
                                    {{ number_format($p->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($isTunai)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Tunai
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            Kredit
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <x-action-dropdown>
                                        <a href="{{ route('transaksipaket.show', $p->id) }}"
                                           class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-kp-blue-700 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Detail Paket
                                        </a>
                                        <a href="{{ route('transaksipaket.cetak', $p->id) }}" target="_blank"
                                           class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-kp-blue-700 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            Cetak Faktur Paket
                                        </a>
                                        <div class="border-t border-slate-100 my-1"></div>
                                        <button type="button"
                                                onclick="hapusPaket('{{ $p->id }}', '{{ $p->nofaktur_paket }}')"
                                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus Paket
                                        </button>
                                    </x-action-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada data transaksi paket iklan lintas media. Klik "Buat Paket Baru" untuk menambah data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($paketList->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                    {{ $paketList->links() }}
                </div>
            @endif
        </div>

    </div>

    @push('scripts')
    <script>
        function hapusPaket(id, nofaktur) {
            if (typeof window.AppAlert !== 'undefined' && typeof window.AppAlert.confirmDelete === 'function') {
                window.AppAlert.confirmDelete({
                    title: 'Hapus Paket Iklan',
                    target: nofaktur,
                    confirmText: 'Ya, Hapus Paket',
                }).then(async (res) => {
                    if (res.isConfirmed) {
                        const token = document.querySelector('meta[name="csrf-token"]').content;
                        const resp = await fetch('/transaksipaket/' + id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        });
                        if (resp.ok) {
                            location.reload();
                        }
                    }
                });
            }
        }
    </script>
    @endpush
</x-app-layout>

