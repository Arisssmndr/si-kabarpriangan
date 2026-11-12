<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-kp-blue-600 uppercase tracking-wider">Divisi Sirkulasi</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Master Agen & Sub-Agen</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Manajemen data jaringan Agen Besar (11), Sub-Agen (11-9), dan status kemitraan sirkulasi.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('sirkulasi.tabel.agen') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>Ke Tabel Data Agen</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-kp-blue-900 text-xs font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter & Search Toolbar -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.master.agen') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama agen, kode, wilayah..." 
                           class="w-full text-xs rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kategori</label>
                    <select name="kategori" class="w-full text-xs rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                        <option value="all" {{ $kategoriFilter == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                        <option value="11" {{ $kategoriFilter == '11' ? 'selected' : '' }}>Agen Besar (11)</option>
                        <option value="11-9" {{ $kategoriFilter == '11-9' ? 'selected' : '' }}>Sub-Agen (11-9)</option>
                        <option value="00" {{ $kategoriFilter == '00' ? 'selected' : '' }}>Non-Aktif (00)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Status</label>
                    <select name="status" class="w-full text-xs rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="aktif" {{ $statusFilter == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $statusFilter == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full py-2 px-3 text-xs font-semibold rounded-lg bg-kp-blue-600 text-white hover:bg-kp-blue-700 transition shadow-2xs">
                        Filter Data
                    </button>
                    <a href="{{ route('sirkulasi.master.agen') }}" class="py-2 px-3 text-xs font-medium rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Agen (Kolom Terpisah Lengkap) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left min-w-[1100px]">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] uppercase text-[10px] tracking-wider font-bold">
                        <tr>
                            <th class="px-3.5 py-3 whitespace-nowrap w-28">Kode Agen</th>
                            <th class="px-4 py-3 min-w-[160px]">Nama Agen</th>
                            <th class="px-3.5 py-3 whitespace-nowrap w-32">No. Telepon / HP</th>
                            <th class="px-3 py-3 text-center whitespace-nowrap w-28">Kategori</th>
                            <th class="px-3.5 py-3 whitespace-nowrap min-w-[120px]">Wilayah</th>
                            <th class="px-3.5 py-3 whitespace-nowrap min-w-[130px]">Jalur Rute</th>
                            <th class="px-3.5 py-3 text-right whitespace-nowrap w-24">Jatah Default</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap w-32">Saldo Piutang</th>
                            <th class="px-3 py-3 text-center whitespace-nowrap w-24">Status</th>
                            <th class="px-3.5 py-3 text-center whitespace-nowrap w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($agenList as $agen)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Kode Agen (Kolom Terpisah) -->
                            <td class="px-3.5 py-3 whitespace-nowrap">
                                <span class="inline-block px-2 py-0.5 rounded font-mono font-bold text-slate-900 bg-slate-100 text-xs border border-slate-200">
                                    {{ $agen->kode_agen }}
                                </span>
                            </td>

                            <!-- Nama Agen (Kolom Terpisah) -->
                            <td class="px-4 py-3 font-bold text-slate-900 text-xs">
                                {{ $agen->nama_agen }}
                                @if($agen->kategori === '11-9')
                                    <div class="text-[10px] text-slate-500 font-normal mt-0.5">Induk: {{ $agen->induk_agen }}</div>
                                @endif
                            </td>

                            <!-- Telepon (Kolom Terpisah) -->
                            <td class="px-3.5 py-3 whitespace-nowrap font-mono text-slate-600 text-xs">
                                {{ $agen->telepon ?: '-' }}
                            </td>

                            <!-- Kategori -->
                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                @if($agen->kategori === '11')
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">11 Besar</span>
                                @elseif($agen->kategori === '11-9')
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200">Sub-Agen</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-500 border border-slate-200">Non-Aktif</span>
                                @endif
                            </td>

                            <!-- Wilayah (Kolom Terpisah) -->
                            <td class="px-3.5 py-3 whitespace-nowrap text-slate-700 text-xs">
                                {{ $agen->wilayah }}
                            </td>

                            <!-- Jalur Rute (Kolom Terpisah) -->
                            <td class="px-3.5 py-3 whitespace-nowrap text-slate-600 text-xs">
                                {{ $agen->rute_nama }}
                            </td>

                            <!-- Jatah Default -->
                            <td class="px-3.5 py-3 text-right font-mono font-bold text-slate-900 text-xs whitespace-nowrap">
                                {{ number_format($agen->jatah_default, 0, ',', '.') }} eks
                            </td>

                            <!-- Saldo Piutang -->
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="font-mono font-bold text-slate-900 text-xs">Rp {{ number_format($agen->saldo_piutang, 0, ',', '.') }}</div>
                            </td>

                            <!-- Status -->
                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                @if($agen->status === 'aktif')
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-500 border border-slate-200">Non-Aktif</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                <form method="POST" action="{{ route('sirkulasi.master.agen.toggle', $agen->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 text-xs font-semibold rounded-lg border {{ $agen->status === 'aktif' ? 'bg-slate-100 text-slate-700 border-slate-300 hover:bg-slate-200' : 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200 hover:bg-kp-blue-100' }} transition cursor-pointer">
                                        {{ $agen->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-slate-400">Tidak ada data agen yang sesuai filter.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
