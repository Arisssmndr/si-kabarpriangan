<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Riwayat Memo Sirkulasi
                        </h1>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-sky-50 text-kp-blue-700 border border-kp-blue-200">
                            Divisi Sirkulasi
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Daftar memo permohonan buka kunci data oplah harian ke Divisi Akuntansi.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <a href="{{ route('sirkulasi.jatah.edit') }}"
                   class="inline-flex items-center gap-1.5 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Input Oplah Harian</span>
                </a>
            </div>
        </div>

        <!-- 4 KPI Card Metrics -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Memo</span>
                <span class="text-2xl font-bold text-slate-900 mt-1 block font-mono">{{ number_format($stats['total'] ?? 0, 0, ',', '.') }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider block">Menunggu Akuntansi</span>
                <span class="text-2xl font-bold text-amber-600 mt-1 block font-mono">{{ number_format($stats['pending'] ?? 0, 0, ',', '.') }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-kp-blue-700 uppercase tracking-wider block">Disetujui</span>
                <span class="text-2xl font-bold text-kp-blue-700 mt-1 block font-mono">{{ number_format($stats['approved'] ?? 0, 0, ',', '.') }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-rose-600 uppercase tracking-wider block">Ditolak</span>
                <span class="text-2xl font-bold text-rose-600 mt-1 block font-mono">{{ number_format($stats['rejected'] ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.memo.history') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Cari Memo / Alasan</label>
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Nomor memo, alasan..."
                           class="w-full h-10 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Persetujuan</label>
                    <select name="status" class="w-full h-10 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition outline-none">
                        <option value="">Semua Status</option>
                        <option value="PENDING" {{ ($status ?? '') === 'PENDING' ? 'selected' : '' }}>Menunggu Akuntansi</option>
                        <option value="APPROVED" {{ ($status ?? '') === 'APPROVED' ? 'selected' : '' }}>Disetujui</option>
                        <option value="REJECTED" {{ ($status ?? '') === 'REJECTED' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Edisi Target</label>
                    <input type="date" name="tanggal" value="{{ $tanggal ?? '' }}"
                           class="w-full h-10 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition outline-none">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Filter
                    </button>
                    @if(!empty($q) || !empty($status) || !empty($tanggal))
                        <a href="{{ route('sirkulasi.memo.history') }}" class="h-10 px-3.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Riwayat Memo -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[320px]">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Memo</th>
                            <th class="py-3 px-3.5">Tanggal Pengajuan</th>
                            <th class="py-3 px-3.5">Tanggal Edisi</th>
                            <th class="py-3 px-3.5">Pemohon</th>
                            <th class="py-3 px-3.5">Alasan Buka Data</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5">Catatan Akuntansi</th>
                            <th class="py-3 px-3.5 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($memos as $idx => $m)
                            @php
                                $st = strtoupper((string) ($m->status_akuntansi ?? ''));
                                $isApproved = in_array($st, ['APPROVED', 'OPEN', 'DISETUJUI'], true);
                                $isRejected = in_array($st, ['REJECTED', 'DITOLAK'], true);
                                $isPending = ($st === 'PENDING');
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center font-mono text-slate-400">
                                    {{ $memos->firstItem() + $idx }}
                                </td>
                                <td class="py-3 px-3.5">
                                    <span class="font-mono font-bold text-slate-900 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">
                                        {{ $m->nomor_memo }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-medium text-slate-900">{{ $m->created_at ? $m->created_at->format('d/m/Y') : '-' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $m->created_at ? $m->created_at->format('H:i') . ' WIB' : '' }}</div>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-semibold text-slate-900">
                                        {{ $m->tanggal_target ? \Carbon\Carbon::parse($m->tanggal_target)->translatedFormat('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td class="py-3 px-3.5">
                                    <span class="font-medium text-slate-800">
                                        {{ $m->diketahui_oleh ?: ($m->dibukaOleh->name ?? 'Staf Sirkulasi') }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 max-w-xs truncate" title="{{ $m->alasan }}">
                                    <span class="text-slate-700">{{ $m->alasan }}</span>
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($isApproved)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-50 text-kp-blue-700 border border-sky-200">
                                            Disetujui
                                        </span>
                                    @elseif($isRejected)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 max-w-xs truncate" title="{{ $m->catatan_akuntansi ?? '-' }}">
                                    <span class="text-slate-500 italic">{{ $m->catatan_akuntansi ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <a href="{{ route('sirkulasi.jatah.memo.cetak', $m->id) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition">
                                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        <span>Cetak</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-500">
                                    <svg class="w-9 h-9 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="font-medium">Belum ada riwayat memo buka data.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Pengajuan memo dari halaman Input Oplah Harian akan tercatat di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($memos->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 bg-white">
                    {{ $memos->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
