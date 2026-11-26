<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Bagan Akun (Chart of Accounts)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Master COA
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Master nomor perkiraan 7 segmen standar pers industri Kabar Priangan.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0" x-data>
                <button type="button"
                        @click="$dispatch('open-modal-import-coa')"
                        class="inline-flex items-center gap-1.5 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer">
                    <span>Import CSV</span>
                </button>
                <button type="button"
                        @click="$dispatch('open-modal-coa')"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>Tambah Akun Baru</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- 2. KPI / STATISTIK COA (METRICS RIBBON 60-30-10) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Akun Aktif</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($stats['total_akun'] ?? 0) }} Akun</div>
                <p class="text-xs text-slate-400 mt-0.5">Seluruh bagan perkiraan</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akiva &amp; Kas (Head 1)</span>
                <div class="text-lg font-bold text-kp-blue-700 mt-1 font-mono">{{ number_format($stats['total_aset'] ?? 0) }} Akun</div>
                <p class="text-xs text-slate-400 mt-0.5">Kas, bank, piutang, aset</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kewajiban &amp; Modal (2-3)</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($stats['total_kewajiban'] ?? 0) }} Akun</div>
                <p class="text-xs text-slate-400 mt-0.5">Utang lancar &amp; ekuitas</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Nominal &amp; Beban (4-6)</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($stats['total_operasional'] ?? 0) }} Akun</div>
                <p class="text-xs text-slate-400 mt-0.5">Pendapatan, HPP, operasional</p>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter Mandiri (Standar Divisi Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.pengaturan.coa') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Akun</label>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Ketik kode perkiraan, nama akun, atau kategori..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kepala Akun (Klasifikasi)</label>
                    <select name="kepala" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ request('kepala', 'all') === 'all' ? 'selected' : '' }}>Semua Kepala</option>
                        <option value="1" {{ request('kepala') === '1' ? 'selected' : '' }}>1 - Aset / Aktiva</option>
                        <option value="2" {{ request('kepala') === '2' ? 'selected' : '' }}>2 - Kewajiban</option>
                        <option value="3" {{ request('kepala') === '3' ? 'selected' : '' }}>3 - Ekuitas / Modal</option>
                        <option value="4" {{ request('kepala') === '4' ? 'selected' : '' }}>4 - Pendapatan</option>
                        <option value="5" {{ request('kepala') === '5' ? 'selected' : '' }}>5 - HPP Produksi</option>
                        <option value="6" {{ request('kepala') === '6' ? 'selected' : '' }}>6 - Beban Operasional</option>
                        <option value="7" {{ request('kepala') === '7' ? 'selected' : '' }}>7 - Pendapatan Lain</option>
                        <option value="8" {{ request('kepala') === '8' ? 'selected' : '' }}>8 - Beban Lain</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Posisi Saldo</label>
                    <select name="saldo_normal" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ request('saldo_normal', 'all') === 'all' ? 'selected' : '' }}>Semua Saldo</option>
                        <option value="debet" {{ request('saldo_normal') === 'debet' ? 'selected' : '' }}>Debet</option>
                        <option value="kredit" {{ request('saldo_normal') === 'kredit' ? 'selected' : '' }}>Kredit</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'kepala', 'saldo_normal']))
                        <a href="{{ route('accounting.pengaturan.coa') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Modal Tambah Akun -->
        <div x-data="{ open: false }" @open-modal-coa.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="open = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl p-6 border border-slate-200">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Tambah Akun Perkiraan Baru</h3>
                        <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('accounting.pengaturan.coa.store') }}" class="space-y-4 mt-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Perkiraan (7 Segmen)</label>
                            <input type="text" name="kode_perkiraan" required placeholder="Contoh: 1.00.04.01.01.01.00003" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Perkiraan</label>
                            <input type="text" name="nama_perkiraan" required placeholder="Contoh: Piutang Iklan Pemerintahan" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition" />
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Kepala Akun</label>
                                <select name="kepala" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                    <option value="1">1 - Aset / Aktiva</option>
                                    <option value="2">2 - Kewajiban / Utang</option>
                                    <option value="3">3 - Ekuitas / Modal</option>
                                    <option value="4">4 - Pendapatan Usaha</option>
                                    <option value="5">5 - Beban Pokok (HPP)</option>
                                    <option value="6">6 - Beban Operasional</option>
                                    <option value="7">7 - Pendapatan Lain</option>
                                    <option value="8">8 - Beban Lain-Lain</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Saldo Normal</label>
                                <select name="saldo_normal" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                    <option value="debet">Debet</option>
                                    <option value="kredit">Kredit</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Akun</label>
                            <input type="text" name="kategori" required placeholder="Contoh: Aset Lancar / Beban Operasional" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition" />
                        </div>
                        <div class="pt-3 flex gap-2">
                            <button type="submit" class="flex-1 h-10 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                                Simpan Akun
                            </button>
                            <button type="button" @click="open = false" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Import CSV COA -->
        <div x-data="{ open: false }" @open-modal-import-coa.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="open = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl p-6 border border-slate-200">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Import Bagan Akun (CSV)</h3>
                        <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('accounting.pengaturan.coa.import') }}" enctype="multipart/form-data" class="space-y-4 mt-4">
                        @csrf
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 space-y-1">
                            <p class="font-semibold text-slate-800">Format kolom CSV (pemisah koma atau titik koma):</p>
                            <code class="block font-mono text-xs text-kp-blue-700">kode_perkiraan,nama_perkiraan,kepala,kategori,saldo_normal</code>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih File CSV (.csv / .txt)</label>
                            <input type="file" name="file" accept=".csv,.txt" required class="w-full text-xs text-slate-700 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-kp-blue-50 file:text-kp-blue-700 hover:file:bg-kp-blue-100 cursor-pointer" />
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="submit" class="flex-1 h-10 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                                Upload &amp; Import COA
                            </button>
                            <button type="button" @click="open = false" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Pure Tabel Bagan Akun (COA) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">Kode Perkiraan</th>
                            <th class="py-3 px-3.5">Nama Akun</th>
                            <th class="py-3 px-3.5">Kelompok</th>
                            <th class="py-3 px-3.5 text-center">Kepala</th>
                            <th class="py-3 px-3.5 text-center">Saldo Normal</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($daftarAkun as $key => $akun)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400 font-mono">{{ $daftarAkun->firstItem() + $key }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono text-xs">{{ $akun->kode_perkiraan }}</td>
                                <td class="py-3 px-3.5">{{ $akun->nama_perkiraan }}</td>
                                <td class="py-3 px-3.5 text-xs text-slate-500">{{ $akun->kategori ?: '-' }}</td>
                                <td class="py-3 px-3.5 text-center font-mono font-semibold">{{ $akun->kepala }}</td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md uppercase {{ $akun->saldo_normal === 'debet' ? 'bg-kp-blue-50 text-kp-blue-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $akun->saldo_normal }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        Aktif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada akun perkiraan terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-t border-slate-200">
                {{ $daftarAkun->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
