<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Aset Tetap &amp; Penyusutan
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Aktiva Tetap
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Register aktiva tetap pers dan posting otomatis jurnal penyusutan metode garis lurus ke GL.
                    </p>
                </div>
            </div>

            <div class="flex items-center flex-wrap gap-2 shrink-0" x-data>
                <button type="button"
                        @click="$dispatch('open-modal-aset')"
                        class="inline-flex items-center gap-1.5 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <span>+ Tambah Aset Tetap</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold shadow-2xs">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold shadow-2xs">
                {{ session('error') }}
            </div>
        @endif

        <!-- 3 KPI Cards Aset Tetap (Report Informasi Nilai & Penyusutan Aktiva) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Harga Perolehan</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalHargaBeli, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Penyusutan Bulanan</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalSusutBulanan, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-500 mt-0.5 block">Akumulasi s.d. {{ $bulanPilihan }}: Rp {{ number_format($akumulasiSusut, 0, ',', '.') }}</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Nilai Buku Aktiva</span>
                <div class="text-xl font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($totalNilaiBuku, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter Mandiri (Standar Divisi Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.beban.aset-tetap') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Aset</label>
                    <input type="text" 
                           name="q" 
                           value="{{ $q ?? '' }}" 
                           placeholder="Ketik kode aset atau nama barang..." 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori Aktiva</label>
                    <select name="kategori" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Kategori</option>
                        @foreach($daftarKategori ?? [] as $kat)
                            <option value="{{ $kat }}" {{ ($kategoriPilihan ?? 'all') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Periode Bulan</label>
                    <input type="month" 
                           name="bulan" 
                           value="{{ $bulanPilihan }}" 
                           class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'kategori']) || request('bulan') != date('Y-m'))
                        <a href="{{ route('accounting.beban.aset-tetap') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tombol Aksi Posting Penyusutan GL (Terpisah Mandiri) -->
        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
            <div class="text-xs text-slate-500">
                Penyusutan bulanan metode garis lurus periode <span class="font-mono font-bold text-slate-800">{{ \Carbon\Carbon::parse($bulanPilihan . '-01')->isoFormat('MMMM Y') }}</span>
            </div>
            <div>
                @if($sudahSusutBulanIni)
                    <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 border border-slate-200 text-slate-600 font-semibold text-xs rounded-xl">
                        <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Penyusutan {{ $bulanPilihan }} Sudah Diposting ke GL</span>
                    </span>
                @else
                    <form method="POST" action="{{ route('accounting.beban.aset-tetap.penyusutan') }}"
                          onsubmit="return confirmSubmit(this, event, {
                              title: 'Posting Penyusutan Aset ke GL?',
                              text: 'Posting Jurnal Beban Penyusutan bulan {{ $bulanPilihan }} sebesar Rp {{ number_format($totalSusutBulanan, 0, ',', '.') }} ke Buku Besar?',
                              theme: 'blue',
                              icon: 'journal',
                              confirmText: 'Ya, Posting Jurnal'
                          })">
                        @csrf
                        <input type="hidden" name="bulan" value="{{ $bulanPilihan }}">
                        <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            <span>Posting Penyusutan {{ $bulanPilihan }} ke GL</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Modal Tambah Aset Tetap -->
        <div x-data="{ open: false }" @open-modal-aset.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="open = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl p-6 border border-slate-200">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Registrasi Aset Tetap Baru</h3>
                        <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 text-lg leading-none cursor-pointer">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('accounting.beban.aset-tetap.store') }}" class="space-y-4 mt-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Aktiva Tetap</label>
                            <input type="text" name="nama" required placeholder="Contoh: Printer CTP Pelat Cetak" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition" />
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
                                <select name="kategori" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                    <option value="Bangunan Permanen">Bangunan Permanen</option>
                                    <option value="Mesin Produksi">Mesin Produksi</option>
                                    <option value="Kendaraan">Kendaraan</option>
                                    <option value="Peralatan Kantor">Peralatan Kantor</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Perolehan</label>
                                <input type="date" name="tgl_perolehan" value="{{ date('Y-m-d') }}" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Perolehan (Rp)</label>
                                <input type="number" name="harga_beli" min="1000" required placeholder="15000000" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 transition" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tarif Penyusutan (% / Tahun)</label>
                                <input type="number" step="0.5" name="tarif_susut" min="1" max="100" value="12.5" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 transition" />
                            </div>
                        </div>
                        <div class="pt-3 flex gap-2">
                            <button type="submit" class="flex-1 h-10 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                                Simpan Aset
                            </button>
                            <button type="button" @click="open = false" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Pure Tabel Register Aset Tetap -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[260px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">Kode Aset</th>
                            <th class="py-3 px-3.5">Nama Aktiva</th>
                            <th class="py-3 px-3.5">Kategori</th>
                            <th class="py-3 px-3.5">Tgl Perolehan</th>
                            <th class="py-3 px-3.5 text-right">Harga Beli</th>
                            <th class="py-3 px-3.5 text-center">Tarif/Tahun</th>
                            <th class="py-3 px-3.5 text-right">Susut/Bulan</th>
                            <th class="py-3 px-3.5 text-center w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($daftarAset as $key => $aset)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400 font-mono">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $aset['kode'] }}</td>
                                <td class="py-3 px-3.5 font-medium text-slate-900">{{ $aset['nama'] }}</td>
                                <td class="py-3 px-3.5">{{ $aset['kategori'] }}</td>
                                <td class="py-3 px-3.5 text-slate-600">{{ \Carbon\Carbon::parse($aset['tgl_perolehan'])->format('d/m/Y') }}</td>
                                <td class="py-3 px-3.5 text-right font-mono">Rp {{ number_format($aset['harga_beli'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-center font-mono">{{ $aset['tarif_susut'] }}%</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($aset['susut_bulanan'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <x-action-dropdown>
                                        <form method="POST" action="{{ route('accounting.beban.aset-tetap.destroy', $aset['kode']) }}"
                                              onsubmit="return confirmSubmit(this, event, {
                                                  title: 'Hapus Aset Tetap?',
                                                  target: '{{ $aset['kode'] }}',
                                                  text: 'Apakah Anda yakin ingin menghapus aset <strong>{{ $aset['kode'] }}</strong> dari daftar aktiva tetap?',
                                                  theme: 'rose',
                                                  icon: 'trash',
                                                  confirmText: 'Ya, Hapus Aset'
                                              })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full flex items-center gap-2 px-3.5 py-2 text-rose-700 hover:bg-rose-50 transition text-left cursor-pointer font-medium">
                                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus Aset</span>
                                            </button>
                                        </form>
                                    </x-action-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada aset tetap yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t border-slate-200 font-bold">
                            <td colspan="5" class="py-3 px-3.5 text-right uppercase text-xs text-slate-700">Total Aktiva</td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($totalHargaBeli, 0, ',', '.') }}
                            </td>
                            <td></td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($totalSusutBulanan, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
