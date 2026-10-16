<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Konsisten (Standar Kabar Priangan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Data Master Pemasang (Klien Iklan)
                    </h1>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Database profil pelanggan, limit piutang, verifikasi NPWP, dan direktori instansi Kabar Priangan
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('pelanggan.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs font-semibold rounded-lg shadow-xs hover:shadow transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Pemasang Baru</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- TOP KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Pemasang</span>
                <p class="text-lg font-extrabold text-slate-900 mt-1 font-tabular">
                    {{ number_format($stats['total'] ?? 0, 0, ',', '.') }} Klien
                </p>
                <span class="text-[10px] text-slate-400 mt-0.5 block">Terdaftar di master data</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Instansi &amp; Korporat</span>
                <p class="text-lg font-extrabold text-kp-blue-700 mt-1 font-tabular">
                    {{ number_format($stats['instansi'] ?? 0, 0, ',', '.') }} Lembaga
                </p>
                <span class="text-[10px] text-slate-400 mt-0.5 block">Pemda, BUMN, Swasta &amp; Agensi</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Akun Kredit / Tempo</span>
                <p class="text-lg font-extrabold text-amber-600 mt-1 font-tabular">
                    {{ number_format($stats['kredit'] ?? 0, 0, ',', '.') }} Klien
                </p>
                <span class="text-[10px] text-slate-400 mt-0.5 block">Memiliki plafon batas kredit</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Akun Tunai (CBP)</span>
                <p class="text-lg font-extrabold text-emerald-600 mt-1 font-tabular">
                    {{ number_format($stats['cbp'] ?? 0, 0, ',', '.') }} Klien
                </p>
                <span class="text-[10px] text-slate-400 mt-0.5 block">Cash Before Print (Wajib lunas)</span>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter Komprehensif -->
        <div class="bg-white border border-slate-300 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('pelanggan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik nama instansi, kode pemasang, PIC, no telp, atau NPWP..."
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-black placeholder-gray-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Klasifikasi Klien
                    </label>
                    <select name="jenis_klien" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-black focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="">Semua Jenis Klien</option>
                        <option value="Perorangan" {{ request('jenis_klien') === 'Perorangan' ? 'selected' : '' }}>Perorangan (Walk-In / CBP)</option>
                        <option value="Swasta" {{ request('jenis_klien') === 'Swasta' ? 'selected' : '' }}>Perusahaan Swasta</option>
                        <option value="Pemda" {{ request('jenis_klien') === 'Pemda' ? 'selected' : '' }}>Instansi Pemda / Dinas</option>
                        <option value="BUMN" {{ request('jenis_klien') === 'BUMN' ? 'selected' : '' }}>BUMN / BUMD</option>
                        <option value="Agensi" {{ request('jenis_klien') === 'Agensi' ? 'selected' : '' }}>Biro Iklan (Agensi)</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-xs rounded-lg shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'jenis_klien']))
                        <a href="{{ route('pelanggan.index') }}" class="h-10 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-lg transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Master Pemasang Terpadu -->
        <div class="bg-white border border-slate-300 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 bg-slate-50/90 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Master Data Pemasang</h2>
                    <p class="text-xs text-slate-500 mt-0.5 font-normal">Total {{ $pelanggan->total() }} klien terdaftar di sistem</p>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-300 bg-slate-100 text-slate-800 uppercase tracking-wider text-[11px] whitespace-nowrap">
                            <th class="py-3.5 px-3.5 font-bold text-center w-12">No</th>
                            <th class="py-3.5 px-3.5 font-bold">Kode &amp; Nama Pemasang</th>
                            <th class="py-3.5 px-3.5 font-bold">Klasifikasi</th>
                            <th class="py-3.5 px-3.5 font-bold">Kontak PIC &amp; Telepon</th>
                            <th class="py-3.5 px-3.5 font-bold">NPWP / NIK</th>
                            <th class="py-3.5 px-3.5 font-bold text-right">Plafon Kredit</th>
                            <th class="py-3.5 px-3.5 font-bold text-center">Riwayat Order</th>
                            <th class="py-3.5 px-3.5 font-bold text-center">Status</th>
                            <th class="py-3.5 px-3.5 text-center w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 whitespace-nowrap">
                        @forelse($pelanggan as $key => $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3.5 text-center text-gray-500 font-normal">
                                    {{ $pelanggan->firstItem() + $key }}
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-bold text-slate-900">{{ $item->nama_instansi }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono mt-0.5">{{ $item->kode_pelanggan }}</div>
                                    @if($item->alamat)
                                        <div class="text-[10px] text-slate-400 mt-0.5 truncate max-w-xs">{{ $item->alamat }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5">
                                    @php
                                        $badgeColor = match($item->jenis_klien) {
                                            'Pemda' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            'BUMN' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'Agensi' => 'bg-purple-100 text-purple-800 border-purple-200',
                                            'Swasta' => 'bg-amber-100 text-amber-800 border-amber-200',
                                            default => 'bg-slate-100 text-slate-800 border-slate-200'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold border {{ $badgeColor }}">
                                        {{ $item->jenis_klien }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-semibold text-slate-900">{{ $item->kontak_pic ?: '-' }}</div>
                                    <div class="text-slate-500 font-mono text-[11px] mt-0.5">{{ $item->no_telp ?: '-' }}</div>
                                </td>
                                <td class="py-3 px-3.5 font-mono text-slate-700">
                                    {{ $item->npwp_nik ?: '-' }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-bold font-tabular">
                                    @if($item->credit_limit > 0)
                                        <span class="text-slate-900">Rp {{ number_format($item->credit_limit, 0, ',', '.') }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 bg-rose-50 text-rose-700 text-[10px] font-bold rounded border border-rose-200">
                                            CBP (Wajib Lunas)
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold rounded border border-slate-200 font-mono">
                                        {{ $item->transaksi_koran_count ?? 0 }} Transaksi
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($item->is_active)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Aktif</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Nonaktif</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('pelanggan.edit', $item->id) }}" 
                                           class="px-2.5 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-2xs transition cursor-pointer">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Belum ada data pelanggan yang cocok</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Ubah kata kunci pencarian atau filter klasifikasi klien</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pelanggan->hasPages())
                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50/80">
                    {{ $pelanggan->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
