<x-app-layout>
    <div class="space-y-5 pb-10" 
         @open-cetak-modal.window="showCetakModal = true"
         x-data="{ 
        showCetakModal: false,
        showEditModal: false,
        form: {
            pembeli: '',
            jenisSatuan: 'kg',
            jumlahSatuan: 10,
            hargaSatuan: 3000,
            metodeBayar: 'tunai',
            bankTujuan: 'Bank BJB Cabang Tasikmalaya',
            noBlanko: '',
            keterangan: ''
        },
        editForm: {
            id: null,
            no_kuitansi: '',
            pembeli: '',
            jumlah: 0,
            metode: 'tunai',
            no_blanko: '',
            keterangan: '',
            alasan_koreksi: ''
        },
        get totalBayar() {
            let jml = Number(this.form.jumlahSatuan) || 0;
            let hrg = Number(this.form.hargaSatuan) || 0;
            return Math.round(jml * hrg);
        },
        openEditSisa(item) {
            this.editForm = {
                id: item.id,
                no_kuitansi: item.no_kuitansi,
                pembeli: item.pembeli,
                jumlah: item.jumlah,
                metode: item.metode,
                no_blanko: item.no_blanko,
                keterangan: item.keterangan,
                alasan_koreksi: ''
            };
            this.showEditModal = true;
        }
    }">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-5" :class="{ 'print:hidden': showCetakModal }">

        <!-- Flash Message -->
        @if(session('success'))
            <div class="p-4 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-xs font-semibold text-kp-blue-800 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-kp-blue-500 hover:text-kp-blue-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- 1. Header Page Penjualan Koran Sisa (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Penjualan Koran Sisa Cetak (KI-14)
                        </h1>
                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Pendapatan Lain (7.00.02.01.02)
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Pencatatan kas masuk tunai dari penjualan sisa oplah cetak pabrik / bekas per kilogram ke pengepul (Terpisah dari retur BAPR sirkulasi)
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if(session('cetak_sisa'))
                    <button type="button" @click="showCetakModal = true" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl transition shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Ulang BKM Koran Sisa</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- 2. KPI Cards (60-30-10 Palette) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Penjualan Koran Sisa Hari Ini</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalSisaHariIni, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Kas tunai langsung diterima meja kasir</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Akumulasi Bulan Ini</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalSisaBulanIni, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Periode: {{ Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akun Rekening Kas (Bagan COA)</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    7.00.02.01.02
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Penjualan Koran Sisa Pabrik / Cetak</span>
            </div>
        </div>

        <!-- 3. Content Split: Form Input & Pure Table Riwayat -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Form Input Setoran Penjualan Koran Sisa -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs p-4 sm:p-5">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100">
                    <span class="w-8 h-8 rounded-xl bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </span>
                    <h3 class="text-sm font-bold text-slate-900">Form Kas Masuk Sisa Cetak</h3>
                </div>

                <form method="POST" action="{{ route('kasir.penerimaan.koran-sisa.store') }}" class="space-y-3.5">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pembeli / Pengepul</label>
                        <input type="text" name="pembeli" x-model="form.pembeli" required placeholder="Contoh: Pak Yana (Pengepul Tasik)" 
                               class="w-full h-11 px-3.5 text-sm rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-slate-900">
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan Ukur</label>
                            <select name="jenis_satuan" x-model="form.jenisSatuan" class="w-full h-11 px-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-sm text-slate-900 bg-white">
                                <option value="kg">Kilogram (Kg)</option>
                                <option value="eksemplar">Eksemplar (Eks)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah</label>
                            <input type="number" step="0.1" name="jumlah_satuan" x-model="form.jumlahSatuan" required min="0.1" 
                                   class="w-full h-11 px-3.5 font-mono text-sm rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-slate-900">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Harga Satuan (Rp per <span x-text="form.jenisSatuan === 'kg' ? 'Kg' : 'Eksemplar'"></span>)
                        </label>
                        <input type="number" name="harga_per_satuan" x-model="form.hargaSatuan" required min="1" 
                                class="w-full h-11 px-3.5 font-mono text-sm rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-slate-900">
                    </div>

                    <!-- Live Total Kalkulasi -->
                    <div class="p-3 bg-kp-blue-50 rounded-xl border border-kp-blue-200 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-kp-blue-800 uppercase tracking-wider block">Total Kas Masuk (KI-14):</span>
                            <span class="text-xs text-kp-blue-700">
                                <span x-text="form.jumlahSatuan"></span> <span x-text="form.jenisSatuan"></span> @ Rp <span x-text="new Intl.NumberFormat('id-ID').format(form.hargaSatuan)"></span>
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-base font-bold font-mono text-kp-blue-700">
                                Rp <span x-text="new Intl.NumberFormat('id-ID').format(totalBayar)"></span>
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Penerimaan</label>
                            <select name="metode_bayar" x-model="form.metodeBayar" class="w-full h-11 px-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-sm text-slate-900 bg-white">
                                <option value="tunai">Kas Tunai</option>
                                <option value="transfer">Transfer Bank BJB</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">No. Blanko Fisik</label>
                            <input type="text" name="no_blanko" placeholder="Opsional" 
                                   class="w-full h-11 px-3.5 font-mono text-sm rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-slate-900">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Catatan</label>
                        <textarea name="keterangan" rows="2" placeholder="Contoh: Sisa cetak oplah edisi kemarin..." 
                                  class="w-full text-sm p-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-slate-900"></textarea>
                    </div>

                    <button type="submit" class="w-full h-11 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs sm:text-sm rounded-xl transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Kas Masuk (KI-14)</span>
                    </button>
                </form>
            </div>

            <!-- Tabel Riwayat Penjualan Koran Sisa (Pure Data Table) -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Filter Bar Riwayat Koran Sisa -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <form method="GET" action="{{ route('kasir.penerimaan.koran-sisa') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <input type="date" name="tanggal" value="{{ $tanggal ?? request('tanggal') }}" title="Pilih Tanggal Sisa Cetak"
                                   class="h-10 px-3 rounded-xl border border-slate-300 text-slate-900 text-xs font-medium bg-white focus:outline-none focus:border-kp-blue-600">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari pembeli..." 
                                   class="h-10 px-3.5 rounded-xl border border-slate-300 text-slate-900 text-xs focus:outline-none focus:border-kp-blue-600 w-full sm:w-48">
                            <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs rounded-xl font-bold cursor-pointer transition">Cari</button>
                            @if(!empty($search) || !empty($tanggal))
                                <a href="{{ route('kasir.penerimaan.koran-sisa') }}" title="Reset Filter" class="h-10 px-3 bg-white hover:bg-slate-50 text-slate-600 rounded-xl border border-slate-300 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Pure Data Table -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="overflow-x-auto min-h-[300px]">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-[#0A72AC] border-b-2 border-[#FFB900] text-white font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3">No. Kuitansi</th>
                                    <th class="py-3 px-3">Tanggal</th>
                                    <th class="py-3 px-3">Pembeli / Pengepul</th>
                                    <th class="py-3 px-3">Keterangan / Rincian</th>
                                    <th class="py-3 px-3 text-right">Nominal (Rp)</th>
                                    <th class="py-3 px-3 text-center">Metode</th>
                                    <th class="py-3 px-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                                @forelse($riwayatSisa as $item)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-3 font-mono font-bold text-kp-blue-700">{{ $item->no_kuitansi }}</td>
                                    <td class="py-3 px-3 text-slate-500 whitespace-nowrap">{{ $item->tanggal_terima ? Carbon\Carbon::parse($item->tanggal_terima)->format('d/m/Y') : '-' }}</td>
                                    <td class="py-3 px-3 font-semibold text-slate-900">{{ $item->nama_pembayar }}</td>
                                    <td class="py-3 px-3 text-slate-600 max-w-[200px] truncate" title="{{ $item->keterangan }}">
                                        {{ $item->keterangan }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($item->jumlah_dibayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 uppercase font-mono">
                                            {{ $item->metode_bayar }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        @if(!$item->is_closed && !$item->status_void)
                                            <button type="button"
                                                    @click="openEditSisa({
                                                        id: {{ $item->id }},
                                                        no_kuitansi: '{{ $item->no_kuitansi }}',
                                                        pembeli: '{{ addslashes($item->nama_pembayar ?? $item->terima_dari ?? '') }}',
                                                        jumlah: {{ $item->jumlah_dibayar }},
                                                        metode: '{{ $item->metode_bayar }}',
                                                        no_blanko: '{{ addslashes($item->no_blanko ?? '') }}',
                                                        keterangan: '{{ addslashes($item->keterangan ?? '') }}'
                                                    })"
                                                    class="inline-flex items-center gap-1 h-8 px-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-semibold text-xs rounded-lg transition shadow-2xs cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Edit</span>
                                            </button>
                                        @else
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">Terkunci</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400 text-xs">Belum ada riwayat penjualan koran sisa.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit Transaksi Koran Sisa -->
        <div x-cloak x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showEditModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between shrink-0 bg-slate-50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-kp-blue-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Edit Penerimaan Koran Sisa</h4>
                            <p class="text-xs text-slate-500">Perbaikan transaksi kas masuk penjualan koran sisa</p>
                        </div>
                    </div>
                    <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <form method="POST" :action="'/kasir/utilitas/koreksi/' + editForm.id + '/update'" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="tipe_dokumen" value="KI">

                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pembeli / Pengepul <span class="text-slate-500">*</span></label>
                            <input type="text" name="nama_pihak" x-model="editForm.pembeli" required
                                   class="w-full h-11 px-3.5 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-sm text-slate-900 font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Diterima (Rp) <span class="text-slate-500">*</span></label>
                            <input type="number" name="jumlah_nominal" x-model="editForm.jumlah" required min="1"
                                   class="w-full h-11 px-3.5 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 font-mono text-base font-bold text-slate-900">
                        </div>

                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Bayar</label>
                                <select name="metode_bayar" x-model="editForm.metode" class="w-full h-11 px-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-sm text-slate-900 bg-white">
                                    <option value="tunai">Tunai</option>
                                    <option value="transfer">Transfer Bank</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">No. Blanko Fisik</label>
                                <input type="text" name="no_blanko" x-model="editForm.no_blanko" placeholder="Opsional"
                                       class="w-full h-11 px-3.5 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 font-mono text-sm text-slate-900">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Uraian</label>
                            <textarea name="keterangan" x-model="editForm.keterangan" rows="2"
                                      class="w-full text-sm p-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-slate-900"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Koreksi <span class="text-slate-500">*</span></label>
                            <input type="text" name="alasan_koreksi" x-model="editForm.alasan_koreksi" required minlength="3"
                                   placeholder="Contoh: Koreksi berat timbangan..."
                                   class="w-full h-11 px-3.5 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-sm text-slate-900 font-medium">
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showEditModal = false" class="h-10 px-4 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 transition">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Modal Cetak BKM Koran Sisa - Format Continuous Form Dot Matrix -->
        @if(session('cetak_sisa'))
        @php $sisa = session('cetak_sisa'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="max-w-4xl w-full max-h-[92vh] flex flex-col">
                <div class="mb-3 flex items-center justify-between print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs font-sans cursor-pointer">
                        ← Kembali ke Daftar Koran Sisa
                    </button>
                    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition cursor-pointer font-sans">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Lembar BKM Koran Sisa (Print / PDF)</span>
                    </button>
                </div>

                <div class="print-document-sheet dot-matrix-container bg-white rounded-lg shadow-2xl overflow-y-auto border border-slate-300 flex font-dot-matrix text-black">
                    <div class="w-8 bg-slate-50 border-r border-dashed border-slate-300 py-6 flex flex-col items-center justify-between gap-4 select-none shrink-0 tractor-feed print:hidden">
                        @for ($i = 0; $i < 14; $i++)
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200 border border-slate-300"></div>
                        @endfor
                    </div>

                    <div class="print-document-body flex-1 p-6 sm:p-8 text-[12px] leading-relaxed text-black">
                        <div class="text-left mb-3">
                            <h1 class="text-base sm:text-lg font-black tracking-wider uppercase font-dot-matrix text-black">
                                KUITANSI KORAN SISA CETAK (BKM) HU. PRIANGAN
                            </h1>
                            <p class="text-[11px] font-bold text-neutral-700 tracking-tight leading-tight">
                                JL. DINDINGARI RAYA NO.12 TLP. (0265)-335300, FAX. 335677 PERUM BUMI RESIK PANGLAYUNGAN TASIKMALAYA 46134
                            </p>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="grid grid-cols-12 gap-x-4 gap-y-1 mb-3">
                            <div class="col-span-7 space-y-0.5">
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Identitas</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold">4.00.14.01.01.01</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Nama</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold uppercase">{{ $sisa['pembeli'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Metode</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">{{ $sisa['metode'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Kota</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">TASIKMALAYA</span>
                                </div>
                            </div>

                            <div class="col-span-5 space-y-0.5">
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">No. Kuitansi</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold font-mono text-blue-900">{{ $sisa['no_bkm'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Jenis Transaksi</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">KORAN SISA CETAK (KI-14)</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Tanggal</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold">{{ $sisa['tanggal'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="grid grid-cols-12 gap-x-3 gap-y-1 mb-3">
                            <div class="col-span-4 flex">
                                <span class="w-20 shrink-0 font-bold whitespace-nowrap">Kategori</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase">SIRKULASI / SISA CETAK</span>
                            </div>
                            <div class="col-span-3 flex">
                                <span class="w-12 shrink-0 font-bold whitespace-nowrap">Tipe</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase">KI-14</span>
                            </div>
                            <div class="col-span-5 flex">
                                <span class="w-24 shrink-0 font-bold whitespace-nowrap">Keterangan</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase text-[11px] leading-tight">{{ $sisa['rincian'] }}</span>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="space-y-1 max-w-lg mb-4 font-bold">
                            <div class="flex justify-between">
                                <span>Brutto</span>
                                <span>Rp. {{ $sisa['terbilang'] }},00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Diskon 0,00 %</span>
                                <span>Rp. 0,00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Netto</span>
                                <span>Rp. {{ $sisa['terbilang'] }},00</span>
                            </div>
                            <div class="border-t border-dashed border-black pt-1 flex justify-between text-sm font-black">
                                <span>Jumlah Total Diterima</span>
                                <span>Rp. {{ $sisa['terbilang'] }},00</span>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-6"></div>

                        <div class="grid grid-cols-2 gap-4 items-end pt-2">
                            <div class="pl-4">
                                <div class="inline-block border-2 border-emerald-600 text-emerald-700 px-5 py-2 rounded-full font-black text-center rotate-[-4deg] opacity-90">
                                    <div class="text-[9px] uppercase tracking-widest">KABAR PRIANGAN</div>
                                    <div class="text-sm tracking-wider border-t border-b border-emerald-600 my-0.5 py-0.5">L U N A S</div>
                                    <div class="text-[8px] font-mono">DIVISI KEUANGAN</div>
                                </div>
                            </div>

                            <div class="text-right flex flex-col gap-5">
                                <div>
                                    <p class="text-[11px] font-bold">Tasikmalaya, {{ date('d-m-Y') }}</p>
                                    <p class="text-[11px] font-semibold mt-0.5">Bagian Kasir,</p>
                                    <div class="h-10"></div>
                                    <p class="font-bold underline text-[11px] uppercase">{{ $sisa['kasir'] }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold">Pembeli / Pengepul,</p>
                                    <div class="h-10"></div>
                                    <p class="font-bold underline text-[11px] uppercase">{{ $sisa['pembeli'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-8 bg-slate-50 border-l border-dashed border-slate-300 py-6 flex flex-col items-center justify-between gap-4 select-none shrink-0 tractor-feed print:hidden">
                        @for ($i = 0; $i < 14; $i++)
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200 border border-slate-300"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>