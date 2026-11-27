<x-app-layout>
    <div class="space-y-5" x-data="{
        modalCatat: false,
        modalBayar: false,
        bayarAction: '',
        bayarNoTagihan: '',
        bayarVendor: '',
        bayarSisa: 0,
        nominalBayar: 0,
        dppVal: 0,
        ppnVal: 0,
        pph23Val: 0,
        openBayar(id, noTagihan, vendor, sisa) {
            this.bayarAction = '{{ url('/accounting/beban/hutang-vendor') }}/' + id + '/bayar';
            this.bayarNoTagihan = noTagihan;
            this.bayarVendor = vendor;
            this.bayarSisa = sisa;
            this.nominalBayar = sisa;
            this.modalBayar = true;
        }
    }">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Hutang Vendor & Supplier (AP)
                        </h1>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Accounts Payable
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Pencatatan tagihan supplier (kertas koran, tinta, percetakan, langganan) beserta PPN, PPh 23, dan pelunasan bertahap ke General Ledger.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="button" @click="modalCatat = true"
                        class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Catat Tagihan Vendor</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- KPI Ringkasan AP -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sisa Utang Vendor Aktif</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($stats['total_utang_aktif'], 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">Belum lunas ke supplier</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Tagihan Berjalan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($stats['count_tercatat']) }} Invoice
                </p>
                <span class="text-xs text-slate-500 mt-1 block">Status tercatat / parsial</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jatuh Tempo &le; 7 Hari</span>
                <p class="text-2xl font-bold {{ $stats['jatuh_tempo_dekat'] > 0 ? 'text-rose-700' : 'text-slate-900' }} mt-1 font-mono tabular-nums">
                    {{ number_format($stats['jatuh_tempo_dekat']) }} Invoice
                </p>
                <span class="text-xs text-slate-500 mt-1 block">Prioritas pembayaran segera</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total AP Terbayar</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($stats['total_terbayar'], 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">Akumulasi pelunasan vendor</span>
            </div>
        </div>

        <!-- Toolbar Filter & Pencarian -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.beban.hutang-vendor') }}" class="flex flex-col md:flex-row md:items-end gap-3">
                <div class="flex-1 min-w-0">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Cari Tagihan / Vendor</label>
                    <input type="text" name="q" value="{{ $q }}" placeholder="No. tagihan AP, No. invoice supplier, nama vendor, atau kategori..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition" />
                </div>
                <div class="w-full md:w-52">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Tagihan</label>
                    <select name="status" class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="tercatat" {{ $status === 'tercatat' ? 'selected' : '' }}>Tercatat (Belum Bayar)</option>
                        <option value="dibayar_sebagian" {{ $status === 'dibayar_sebagian' ? 'selected' : '' }}>Dibayar Sebagian</option>
                        <option value="lunas" {{ $status === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" class="h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white rounded-xl text-sm font-bold transition cursor-pointer shadow-xs">
                        Terapkan
                    </button>
                    <a href="{{ route('accounting.beban.hutang-vendor') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold inline-flex items-center justify-center transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Tagihan Vendor -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[320px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3.5">No. Tagihan / Invoice</th>
                            <th class="py-3 px-3.5">Vendor &amp; Kategori</th>
                            <th class="py-3 px-3.5">Tgl &amp; Jatuh Tempo</th>
                            <th class="py-3 px-3.5 text-right">Total Tagihan</th>
                            <th class="py-3 px-3.5 text-right">Terbayar</th>
                            <th class="py-3 px-3.5 text-right">Sisa Utang</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($tagihans as $row)
                            @php
                                $isOverdue = $row->jatuh_tempo && $row->status !== 'lunas' && \Carbon\Carbon::parse($row->jatuh_tempo)->isPast();
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3.5">
                                    <span class="font-mono font-bold text-kp-blue-700 text-sm block">{{ $row->no_tagihan }}</span>
                                    <span class="text-xs text-slate-500 font-mono">Inv: {{ $row->no_invoice_vendor ?: '-' }}</span>
                                </td>
                                <td class="py-3 px-3.5">
                                    <span class="font-semibold text-slate-900 block">{{ $row->nama_vendor }}</span>
                                    <span class="text-xs text-slate-500">{{ $row->kategori_beban }} &bull; <span class="font-mono">{{ $row->kode_perkiraan_beban }}</span></span>
                                </td>
                                <td class="py-3 px-3.5">
                                    <span class="font-mono text-xs text-slate-700 block">Tgl: {{ \Carbon\Carbon::parse($row->tanggal_tagihan)->format('d/m/Y') }}</span>
                                    <span class="font-mono text-xs {{ $isOverdue ? 'text-rose-700 font-bold' : 'text-slate-500' }}">
                                        J/T: {{ $row->jatuh_tempo ? \Carbon\Carbon::parse($row->jatuh_tempo)->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono tabular-nums font-semibold text-slate-900">
                                    Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}
                                    @if($row->pph23 > 0 || $row->ppn > 0)
                                        <span class="block text-xs text-slate-500 font-normal">
                                            DPP {{ number_format($row->dpp, 0, ',', '.') }}
                                            @if($row->ppn > 0) + PPN {{ number_format($row->ppn, 0, ',', '.') }} @endif
                                            @if($row->pph23 > 0) - PPh23 {{ number_format($row->pph23, 0, ',', '.') }} @endif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono tabular-nums text-slate-600">
                                    Rp {{ number_format($row->total_terbayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono tabular-nums font-bold {{ $row->sisa_utang > 0 ? 'text-rose-700' : 'text-slate-500' }}">
                                    Rp {{ number_format($row->sisa_utang, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($row->status === 'lunas')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Lunas</span>
                                    @elseif($row->status === 'dibayar_sebagian')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">Parsial</span>
                                    @elseif($row->status === 'draft')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">Draft</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">Tercatat</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($row->status !== 'lunas' && $row->sisa_utang > 0 && $row->status !== 'draft')
                                        <button type="button"
                                                @click="openBayar({{ $row->id }}, @js($row->no_tagihan), @js($row->nama_vendor), {{ (float) $row->sisa_utang }})"
                                                class="px-3 py-1.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white rounded-lg text-xs font-bold transition cursor-pointer shadow-xs">
                                            Bayar Utang
                                        </button>
                                    @elseif($row->jurnalAkui)
                                        <span class="font-mono text-xs text-slate-500">{{ $row->jurnalAkui->no_jurnal }}</span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-sm text-slate-500">
                                    Belum ada tagihan vendor (Accounts Payable) tercatat. Klik <span class="font-semibold text-kp-blue-700">Catat Tagihan Vendor</span> untuk memulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tagihans->hasPages())
                <div class="p-3.5 border-t border-slate-200">
                    {{ $tagihans->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Catat Tagihan Vendor Baru -->
        <div x-show="modalCatat" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="modalCatat = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-xl bg-white rounded-xl shadow-xl p-6 border border-slate-200">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Catat Tagihan Supplier / Vendor (AP)</h3>
                        <button type="button" @click="modalCatat = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold cursor-pointer">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('accounting.beban.hutang-vendor.store') }}" class="space-y-4 mt-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Vendor / Supplier</label>
                                <input type="text" name="nama_vendor" required placeholder="Contoh: PT Aspex Kumbong / Percetakan"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. Invoice Vendor</label>
                                <input type="text" name="no_invoice_vendor" placeholder="Opsional"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono focus:outline-none focus:border-kp-blue-600" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori Beban</label>
                                <input type="text" name="kategori_beban" required value="Kertas & Tinta Cetak"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Tagihan</label>
                                <input type="date" name="tanggal_tagihan" required value="{{ date('Y-m-d') }}"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jatuh Tempo</label>
                                <input type="date" name="jatuh_tempo" value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pos Akun Beban / Persediaan (Debet GL)</label>
                            <select name="kode_perkiraan_beban" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                @foreach($daftarAkunBeban as $akun)
                                    <option value="{{ $akun->kode_perkiraan }}">{{ $akun->kode_perkiraan }} - {{ $akun->nama_perkiraan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">DPP (Rp)</label>
                                <input type="number" step="1" min="1" name="dpp" required x-model.number="dppVal"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">PPN (Rp)</label>
                                <input type="number" step="1" min="0" name="ppn" x-model.number="ppnVal"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Potongan PPh 23 (Rp)</label>
                                <input type="number" step="1" min="0" name="pph23" x-model.number="pph23Val"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                            <span class="font-semibold text-slate-700">Neto Utang ke Vendor ({{ $akunUtangDefault['kode'] ?? '' }}):</span>
                            <span class="font-mono font-bold text-kp-blue-700 text-sm" x-text="'Rp ' + Math.max(0, (Number(dppVal||0) + Number(ppnVal||0) - Number(pph23Val||0))).toLocaleString('id-ID')"></span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Keterangan</label>
                            <input type="text" name="keterangan" placeholder="Keterangan tambahan pembelian / termin..."
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                        </div>

                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                            <input type="checkbox" name="post_gl_now" value="1" checked class="rounded border-slate-300 text-kp-blue-600">
                            <span>Otomatis bentuk &amp; posting jurnal pengakuan hutang ke General Ledger</span>
                        </label>

                        <div class="pt-2 flex gap-2">
                            <button type="submit" class="flex-1 h-10 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition cursor-pointer">
                                Simpan Tagihan Vendor
                            </button>
                            <button type="button" @click="modalCatat = false" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Bayar Tagihan Vendor -->
        <div x-show="modalBayar" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="modalBayar = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-white rounded-xl shadow-xl p-6 border border-slate-200">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Pelunasan Tagihan Vendor</h3>
                            <p class="text-xs text-slate-500 mt-0.5 font-mono" x-text="bayarNoTagihan + ' - ' + bayarVendor"></p>
                        </div>
                        <button type="button" @click="modalBayar = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold cursor-pointer">&times;</button>
                    </div>

                    <form method="POST" :action="bayarAction" class="space-y-4 mt-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Bayar</label>
                                <input type="date" name="tanggal_bayar" required value="{{ date('Y-m-d') }}"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Metode</label>
                                <select name="metode_bayar" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="tunai">Tunai Kasir</option>
                                    <option value="giro">Cek / Giro</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Sumber Kas / Rekening Bank</label>
                            <select name="kode_perkiraan_kas_bank" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                @foreach($akunPembayaran as $kode => $nama)
                                    <option value="{{ $kode }}">{{ $kode }} - {{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nominal Dibayar (Maks Rp <span x-text="Number(bayarSisa||0).toLocaleString('id-ID')"></span>)</label>
                            <input type="number" step="1" min="1" name="nominal_bayar" required x-model.number="nominalBayar"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Pembayaran</label>
                            <input type="text" name="keterangan" placeholder="No. referensi transfer / keterangan..."
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                        </div>

                        <div class="pt-2 flex gap-2">
                            <button type="submit" class="flex-1 h-10 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition cursor-pointer">
                                Posting Pembayaran ke GL
                            </button>
                            <button type="button" @click="modalBayar = false" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
