<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Master Agen & Sub-Agen</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-50 text-amber-700 border border-amber-200">Sirkulasi</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Manajemen data jaringan Agen Besar (11), Sub-Agen (11-9), dan Non-Aktif (00 - proteksi piutang akuntansi).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-amber-600 hover:bg-amber-700 text-white shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>Tambah Agen Baru</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter & Search Toolbar -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.master.agen') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama agen, kode, wilayah..." 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Kategori</label>
                    <select name="kategori" class="w-full text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                        <option value="all" {{ $kategoriFilter == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                        <option value="11" {{ $kategoriFilter == '11' ? 'selected' : '' }}>Agen Besar (11)</option>
                        <option value="11-9" {{ $kategoriFilter == '11-9' ? 'selected' : '' }}>Sub-Agen (11-9)</option>
                        <option value="00" {{ $kategoriFilter == '00' ? 'selected' : '' }}>Non-Aktif (00)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Status</label>
                    <select name="status" class="w-full text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="aktif" {{ $statusFilter == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $statusFilter == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full py-2 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition">
                        Filter Data
                    </button>
                    <a href="{{ route('sirkulasi.master.agen') }}" class="py-2 px-3 text-xs font-medium rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Agen -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Kode & Nama Agen</th>
                            <th class="px-4 py-3 font-semibold">Kategori</th>
                            <th class="px-4 py-3 font-semibold">Rute / Jalur</th>
                            <th class="px-4 py-3 font-semibold text-right">Jatah Default</th>
                            <th class="px-4 py-3 font-semibold text-right">Saldo Piutang</th>
                            <th class="px-4 py-3 font-semibold text-center">Status</th>
                            <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($agenList as $agen)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $agen->nama_agen }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $agen->kode_agen }} &bull; {{ $agen->telepon }}</div>
                                @if($agen->kategori === '11-9')
                                    <div class="text-[10px] text-amber-700 mt-0.5">Induk: {{ $agen->induk_agen }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($agen->kategori === '11')
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-sky-50 text-sky-700 border border-sky-200">Agen Besar (11)</span>
                                @elseif($agen->kategori === '11-9')
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-50 text-amber-700 border border-amber-200">Sub-Agen (11-9)</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 text-slate-600 border border-slate-300">Non-Aktif (00)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-800">{{ $agen->rute_nama }}</div>
                                <div class="text-[11px] text-slate-500">{{ $agen->wilayah }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-extrabold text-slate-900">
                                {{ number_format($agen->jatah_default, 0, ',', '.') }} eks
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="font-bold text-slate-900">Rp {{ number_format($agen->saldo_piutang, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-slate-400">Plafon: Rp {{ number_format($agen->plafon_piutang, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($agen->status === 'aktif')
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form method="POST" action="{{ route('sirkulasi.master.agen.toggle', $agen->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 text-[11px] font-semibold rounded border {{ $agen->status === 'aktif' ? 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }} transition">
                                        {{ $agen->status === 'aktif' ? 'Non-aktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">Tidak ada data agen yang sesuai filter.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
