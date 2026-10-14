<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Master Pelanggan & Klien Iklan (CRM)
                    </h1>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Database terpusat klien Pemda, BUMN, Korporat, Biro Iklan & Walk-in Harian Umum Kabar Priangan
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('pelanggan.create') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah Pelanggan Baru</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter & Search Bar -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('pelanggan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-6">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama instansi, kode pelanggan, PIC, atau no telp..."
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>
                <div class="sm:col-span-4">
                    <select name="jenis_klien" onchange="this.form.submit()"
                            class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="">Semua Jenis Klien</option>
                        <option value="Perorangan" {{ request('jenis_klien') == 'Perorangan' ? 'selected' : '' }}>Perorangan (Walk-in)</option>
                        <option value="Swasta" {{ request('jenis_klien') == 'Swasta' ? 'selected' : '' }}>Swasta / Korporat</option>
                        <option value="Pemda" {{ request('jenis_klien') == 'Pemda' ? 'selected' : '' }}>Instansi Pemda</option>
                        <option value="BUMN" {{ request('jenis_klien') == 'BUMN' ? 'selected' : '' }}>BUMN / BUMD</option>
                        <option value="Agensi" {{ request('jenis_klien') == 'Agensi' ? 'selected' : '' }}>Biro Iklan (Agensi)</option>
                    </select>
                </div>
                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="w-full h-10 px-4 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'jenis_klien']))
                        <a href="{{ route('pelanggan.index') }}" class="h-10 px-3 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Pelanggan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Kode & Instansi</th>
                            <th class="py-3 px-4">Jenis Klien</th>
                            <th class="py-3 px-4">Kontak PIC & Telp</th>
                            <th class="py-3 px-4">NPWP / NIK</th>
                            <th class="py-3 px-4 text-right">Credit Limit</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        @forelse($pelanggan as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $item->nama_instansi }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $item->kode_pelanggan }}</div>
                                    @if($item->alamat)
                                        <div class="text-[11px] text-slate-500 mt-1 line-clamp-1">{{ $item->alamat }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @php
                                        $badgeColor = match($item->jenis_klien) {
                                            'Pemda' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            'BUMN' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'Agensi' => 'bg-purple-100 text-purple-800 border-purple-200',
                                            'Swasta' => 'bg-amber-100 text-amber-800 border-amber-200',
                                            default => 'bg-slate-100 text-slate-800 border-slate-200'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $badgeColor }}">
                                        {{ $item->jenis_klien }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-slate-900">{{ $item->kontak_pic ?: '-' }}</div>
                                    <div class="text-slate-500 font-mono text-[11px]">{{ $item->no_telp ?: '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">
                                    {{ $item->npwp_nik ?: '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-semibold font-tabular">
                                    @if($item->credit_limit > 0)
                                        <span class="text-slate-900">Rp {{ number_format($item->credit_limit, 0, ',', '.') }}</span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 bg-rose-50 text-rose-700 text-[10px] font-semibold rounded border border-rose-200">
                                            CBP (Wajib Lunas)
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($item->is_active)
                                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500" title="Aktif"></span>
                                    @else
                                        <span class="inline-block w-2 h-2 rounded-full bg-slate-300" title="Non-aktif"></span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('pelanggan.edit', $item->id) }}" 
                                           class="px-2.5 py-1 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-[11px] font-semibold rounded transition cursor-pointer">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    Belum ada data pelanggan yang cocok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pelanggan->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    {{ $pelanggan->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
