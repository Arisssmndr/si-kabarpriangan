<x-app-layout>
    <div class="space-y-6" 
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
        <div class="space-y-6" :class="{ 'print:hidden': showCetakModal }">

        <!-- Flash Message -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-semibold text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs font-semibold text-rose-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Header Page -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Penjualan Koran Sisa Cetak (KI-14)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Pendapatan Lain-lain (7.00.02.01.02)
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pencatatan kas masuk tunai dari penjualan sisa oplah cetak pabrik / bekas per kilogram ke pengepul (Terpisah dari retur BAPR sirkulasi)
                    </p>
                </div>
            </div>

            @if(session('cetak_sisa'))
                <button type="button" @click="showCetakModal = true" class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl border border-emerald-200 transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Ulang BKM Koran Sisa</span>
                </button>
            @endif
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Penjualan Koran Sisa Hari Ini</span>
                <div class="mt-2 text-xl font-extrabold text-emerald-600">
                    Rp {{ number_format($totalSisaHariIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Kas tunai langsung diterima meja kasir</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Akumulasi Bulan Ini</span>
                <div class="mt-2 text-xl font-extrabold text-slate-900">
                    Rp {{ number_format($totalSisaBulanIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Periode: {{ Carbon\Carbon::now()->translatedFormat('F Y') }}</div>
            </div>

            <div class="bg-white border border-emerald-200 rounded-xl p-4 shadow-xs bg-emerald-50/20">
                <span class="text-xs font-semibold text-emerald-800">Akun Rekening Kas (Bagan COA)</span>
                <div class="mt-2 text-base font-bold font-mono text-emerald-900">
                    7.00.02.01.02
                </div>
                <div class="mt-1 text-[11px] text-emerald-700">Penjualan Koran Sisa Pabrik / Cetak</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Input Setoran Penjualan Koran Sisa -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs p-5">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </span>
                    <h3 class="text-sm font-bold text-slate-900">Form Kas Masuk Sisa Cetak</h3>
                </div>

                <form method="POST" action="{{ route('kasir.penerimaan.koran-sisa.store') }}" class="space-y-3.5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pembeli / Pengepul Koran</label>
                        <input type="text" name="pembeli" x-model="form.pembeli" required placeholder="Contoh: Pak Yana (Pengepul Tasik)" 
                               class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 text-slate-900">
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Satuan Ukur</label>
                            <select name="jenis_satuan" x-model="form.jenisSatuan" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 text-slate-900">
                                <option value="kg">Kilogram (Kg) / Timbangan</option>
                                <option value="eksemplar">Eksemplar (Eks)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah</label>
                            <input type="number" step="0.1" name="jumlah_satuan" x-model="form.jumlahSatuan" required min="0.1" 
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 font-mono text-slate-900">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Harga Satuan (Rp per <span x-text="form.jenisSatuan === 'kg' ? 'Kg' : 'Eksemplar'"></span>)
                        </label>
                        <input type="number" name="harga_per_satuan" x-model="form.hargaSatuan" required min="1" 
                                class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 font-mono text-slate-900">
                    </div>

                    <!-- Live Total Kalkulasi -->
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Total Kas Masuk (KI-14):</span>
                            <span class="text-xs text-emerald-700">
                                <span x-text="form.jumlahSatuan"></span> <span x-text="form.jenisSatuan"></span> @ Rp <span x-text="new Intl.NumberFormat('id-ID').format(form.hargaSatuan)"></span>
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-base font-black font-mono text-emerald-900">
                                Rp <span x-text="new Intl.NumberFormat('id-ID').format(totalBayar)"></span>
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Metode Penerimaan</label>
                            <select name="metode_bayar" x-model="form.metodeBayar" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 text-slate-900">
                                <option value="tunai">Kas Tunai (Fisik Meja)</option>
                                <option value="transfer">Transfer Bank BJB</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">No. Blanko Fisik</label>
                            <input type="text" name="no_blanko" placeholder="Opsional" 
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 font-mono text-slate-900">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Catatan</label>
                        <textarea name="keterangan" rows="2" placeholder="Contoh: Sisa cetak oplah edisi 19-21 Oktober dijual bekas..." 
                                  class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 text-slate-900"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Kas Masuk (KI-14)</span>
                    </button>
                </form>
            </div>

            <!-- Tabel Riwayat Penjualan Koran Sisa -->
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden flex flex-col">
                <div class="px-5 py-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 bg-slate-50/50">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Riwayat Penerimaan Koran Sisa Cetak
                    </h3>
                    <form method="GET" action="{{ route('kasir.penerimaan.koran-sisa') }}" class="flex items-center gap-1.5">
                        <input type="date" name="tanggal" value="{{ $tanggal ?? request('tanggal') }}" title="Pilih Tanggal Sisa Cetak"
                               class="text-xs py-1 px-2 rounded-lg border border-slate-300 text-slate-800 font-semibold bg-white cursor-pointer">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari pembeli..." 
                               class="text-xs py-1 px-2.5 rounded-lg border border-slate-300 w-32 sm:w-40">
                        <button type="submit" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white text-xs rounded-lg font-semibold cursor-pointer">Cari</button>
                        @if(!empty($search) || !empty($tanggal))
                            <a href="{{ route('kasir.penerimaan.koran-sisa') }}" title="Reset Filter" class="p-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-100/90 text-slate-800 font-bold text-[11px] uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 font-bold text-slate-800">No. Kuitansi</th>
                                <th class="px-4 py-3 font-bold text-slate-800">Tanggal</th>
                                <th class="px-4 py-3 font-bold text-slate-800">Pembeli / Pengepul</th>
                                <th class="px-4 py-3 font-bold text-slate-800">Keterangan / Rincian</th>
                                <th class="px-4 py-3 font-bold text-slate-800 text-right">Nominal (Rp)</th>
                                <th class="px-4 py-3 font-bold text-slate-800 text-center">Metode</th>
                                <th class="px-4 py-3 font-bold text-slate-800 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($riwayatSisa as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $item->no_kuitansi }}</td>
                                <td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ $item->tanggal_terima ? Carbon\Carbon::parse($item->tanggal_terima)->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $item->terima_dari }}</td>
                                <td class="px-4 py-3 text-slate-600 max-w-xs truncate">{{ $item->keterangan }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-emerald-700">Rp {{ number_format($item->jumlah_dibayar, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase {{ $item->metode_bayar === 'transfer' ? 'bg-teal-50 text-teal-700 border border-teal-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                        {{ $item->metode_bayar }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @if(!$item->is_closed && !$item->status_void)
                                        <button type="button"
                                                @click="openEditSisa({
                                                    id: {{ $item->id }},
                                                    no_kuitansi: '{{ $item->no_kuitansi }}',
                                                    pembeli: '{{ addslashes($item->terima_dari ?? '') }}',
                                                    jumlah: {{ $item->jumlah_dibayar }},
                                                    metode: '{{ $item->metode_bayar }}',
                                                    no_blanko: '{{ addslashes($item->no_blanko ?? '') }}',
                                                    keterangan: '{{ addslashes($item->keterangan ?? '') }}'
                                                })"
                                                class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-bold text-[10px] rounded-lg transition shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </button>
                                    @else
                                        <span class="text-[10px] font-semibold text-slate-400">Terkunci</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                    Belum ada transaksi penjualan sisa cetak koran pada periode ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($riwayatSisa->hasPages())
                <div class="p-3 border-t border-slate-100 bg-slate-50">
                    {{ $riwayatSisa->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Modal Edit Transaksi Koran Sisa (KI-14) -->
        <div x-cloak x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showEditModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-emerald-50/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Edit Inputan Koran Sisa (KI-14)</h4>
                            <p class="text-[11px] text-slate-500" x-text="'No. Kuitansi: ' + editForm.no_kuitansi"></p>
                        </div>
                    </div>
                    <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form method="POST" :action="'/kasir/utilitas/koreksi/' + editForm.id + '/update'" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="tipe_dokumen" value="KI">

                    <div class="flex-1 overflow-y-auto px-5 py-3.5 space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pembeli / Pengepul Koran <span class="text-rose-600">*</span></label>
                            <input type="text" name="nama_pihak" x-model="editForm.pembeli" required
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 text-slate-900 font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Total Nominal Pembayaran (Rp) <span class="text-rose-600">*</span></label>
                            <input type="number" name="jumlah_nominal" x-model="editForm.jumlah" required min="1"
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 font-mono font-bold text-slate-900">
                        </div>

                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Metode Penerimaan</label>
                                <select name="metode_bayar" x-model="editForm.metode" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 text-slate-900">
                                    <option value="tunai">Kas Tunai (Fisik Meja)</option>
                                    <option value="transfer">Transfer Bank BJB</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">No. Blanko Fisik</label>
                                <input type="text" name="no_blanko" x-model="editForm.no_blanko" placeholder="Opsional"
                                       class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 font-mono text-slate-900">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Rincian</label>
                            <textarea name="keterangan" x-model="editForm.keterangan" rows="2"
                                      class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 text-slate-900"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Koreksi / Catatan Pengeditan <span class="text-rose-600">*</span></label>
                            <input type="text" name="alasan_koreksi" x-model="editForm.alasan_koreksi" required minlength="3"
                                   placeholder="Contoh: Koreksi berat timbangan / nama pengepul..."
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-emerald-300 focus:ring-1 focus:ring-emerald-500 text-slate-900 font-semibold">
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        </div>

        <!-- Modal Cetak BKM Koran Sisa -->
        @if(session('cetak_sisa'))
        @php $sisa = session('cetak_sisa'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">
                <!-- Header Dokumen -->
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500">PT. Priangan Berkah Media - Bagian Perkasiran</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded bg-emerald-100 text-emerald-800 uppercase tracking-widest border border-emerald-300">
                            BUKTI KAS MASUK (KI-14)
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $sisa['no_kuitansi'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $sisa['tanggal'] }}</div>
                    </div>
                </div>

                <!-- Body Dokumen -->
                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2.5 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Kode Perkiraan</span>
                        <span class="col-span-2 font-mono font-bold text-slate-900">: {{ $sisa['kode_perkiraan'] }} (Penjualan Koran Sisa Cetak)</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Diterima Dari</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $sisa['pembeli'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Rincian Barang</span>
                        <span class="col-span-2 text-slate-800">: {{ $sisa['rincian'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Metode Bayar</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $sisa['metode'] }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-2 bg-emerald-50 px-3 rounded-xl border border-emerald-200/80 my-2">
                        <span class="font-bold text-emerald-900">Jumlah Diterima</span>
                        <span class="col-span-2 font-mono font-black text-base text-emerald-900">: Rp {{ number_format($sisa['nominal'], 0, ',', '.') }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-1.5 text-xs bg-slate-50 px-3 rounded-lg border border-slate-200">
                        <span class="text-slate-500 font-semibold">Terbilang Huruf</span>
                        <span class="col-span-2 font-semibold italic text-slate-900">: {{ $sisa['terbilang'] }}</span>
                    </div>

                    <!-- Tanda Tangan Cetak -->
                    <div class="pt-6 border-t border-slate-200 flex justify-between text-center text-xs text-slate-600">
                        <div class="w-40">
                            <div class="font-semibold text-slate-700">Penyetor / Pembeli</div>
                            <div class="h-12"></div>
                            <div class="font-bold text-slate-900 underline">({{ $sisa['pembeli'] }})</div>
                        </div>
                        <div class="w-40">
                            <div class="text-[11px] text-slate-500">Tasikmalaya, {{ Carbon\Carbon::now()->format('d/m/Y') }}</div>
                            <div class="font-semibold text-slate-700 mt-0.5">Kasir Penerima</div>
                            <div class="h-12"></div>
                            <div class="font-bold text-slate-900 font-mono underline">({{ $sisa['kasir'] }})</div>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti KI-14</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
