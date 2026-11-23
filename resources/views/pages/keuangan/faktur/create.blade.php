<x-app-layout>
    <div class="space-y-5" x-data="{
        kategori: '{{ old('kategori', $kategoriDefault) }}',
        tipeFakturIklan: '{{ old('tipe_faktur', ($kategoriDefault === 'iklan' ? $tipeFakturDefault : 'reguler')) }}',
        tipeFakturSirkulasi: '{{ old('tipe_faktur', ($kategoriDefault === 'sirkulasi' ? $tipeFakturDefault : 'reguler')) }}',
        noFaktur: '{{ old('no_faktur', $noFakturSuggested) }}',
        suggestedIklReguler: '{{ $suggestedIklReguler ?? "FKKRN001" }}',
        suggestedIklSusulan: '{{ $suggestedIklSusulan ?? "FKKRN-SUS-001" }}',
        suggestedIklStopper: '{{ $suggestedIklStopper ?? "FKKRN-STP-001" }}',
        suggestedIklBonus: '{{ $suggestedIklBonus ?? "FKKRN-BNS-001" }}',
        suggestedSrkReguler: '{{ $suggestedSrkReguler ?? "FKSRK001" }}',
        suggestedSrkSusulan: '{{ $suggestedSrkSusulan ?? "FKSRK-SUS-001" }}',
        suggestedSrkInsidentil: '{{ $suggestedSrkInsidentil ?? "FKSRK-INS-001" }}',

        onKategoriChange() {
            if (this.kategori === 'sirkulasi') {
                this.onTipeSirkulasiChange();
            } else {
                this.onTipeIklanChange();
            }
        },
        onTipeIklanChange() {
            if (this.tipeFakturIklan === 'susulan') this.noFaktur = this.suggestedIklSusulan;
            else if (this.tipeFakturIklan === 'stopper') this.noFaktur = this.suggestedIklStopper;
            else if (this.tipeFakturIklan === 'bonus') this.noFaktur = this.suggestedIklBonus;
            else this.noFaktur = this.suggestedIklReguler;
        },
        onTipeSirkulasiChange() {
            if (this.tipeFakturSirkulasi === 'susulan') this.noFaktur = this.suggestedSrkSusulan;
            else if (this.tipeFakturSirkulasi === 'insidentil') this.noFaktur = this.suggestedSrkInsidentil;
            else this.noFaktur = this.suggestedSrkReguler;
        },
        salesNama: '{{ old('sales_nama', '') }}',
        customerId: '{{ old('customer_id', '') }}',
        tanggalFaktur: '{{ old('tanggal_faktur', date('Y-m-d')) }}',
        jatuhTempo: '{{ old('jatuh_tempo', date('Y-m-d', strtotime('+14 days'))) }}',
        catatan: '{{ old('catatan', '') }}',
        tipePembayaran: '{{ old('tipe_pembayaran', 'lunas_langsung') }}',
        nominalDp: {{ old('nominal_dp', 0) }},
        items: [
            { nama: '', qty: 1, harga: 0, subtotal: 0 }
        ],

        inputNoFakturIklan: '{{ request('no_faktur_iklan', '') }}',
        isLoadingIklan: false,
        iklanSuccessMsg: '',
        iklanErrorMsg: '',
        iklanWarningMsg: '',

        init() {
            if (this.inputNoFakturIklan) {
                this.tarikDataIklan(this.inputNoFakturIklan);
            }
        },

        async tarikDataIklan(nomor) {
            const query = (nomor || this.inputNoFakturIklan || this.noFaktur || '').trim();
            if (!query) {
                this.iklanErrorMsg = 'Silakan ketik atau pilih nomor faktur dari divisi iklan terlebih dahulu.';
                return;
            }

            this.isLoadingIklan = true;
            this.iklanSuccessMsg = '';
            this.iklanErrorMsg = '';
            this.iklanWarningMsg = '';

            try {
                const url = '{{ route('keuangan.faktur.cari-iklan') }}?no_faktur=' + encodeURIComponent(query);
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await res.json();

                if (!res.ok || !data.success) {
                    this.iklanErrorMsg = data.message || 'Data faktur iklan tidak ditemukan.';
                    return;
                }

                this.kategori = 'iklan';

                if (data.no_faktur) {
                    this.noFaktur = data.no_faktur;
                    this.inputNoFakturIklan = data.no_faktur;
                }

                if (data.customer_id) {
                    this.customerId = String(data.customer_id);
                    const selectEl = document.getElementById('select_customer');
                    if (selectEl) {
                        let exists = false;
                        for (let opt of selectEl.options) {
                            if (opt.value == data.customer_id) {
                                exists = true;
                                break;
                            }
                        }
                        if (!exists) {
                            const newOpt = new Option(data.nama_customer + ' (Iklan)', data.customer_id, true, true);
                            selectEl.add(newOpt);
                        }
                        selectEl.value = data.customer_id;
                    }
                }

                if (data.sales_nama) {
                    this.salesNama = data.sales_nama;
                }

                if (data.tanggal_faktur) {
                    this.tanggalFaktur = data.tanggal_faktur;
                }
                if (data.jatuh_tempo) {
                    this.jatuhTempo = data.jatuh_tempo;
                }

                if (data.items && data.items.length > 0) {
                    this.items = data.items.map(it => ({
                        nama: it.nama,
                        qty: Number(it.qty) || 1,
                        harga: Number(it.harga) || 0,
                        subtotal: Number(it.subtotal) || 0
                    }));
                }

                if (data.tipe_pembayaran) {
                    this.tipePembayaran = data.tipe_pembayaran;
                }
                this.nominalDp = Number(data.nominal_dp) || 0;

                if (data.catatan) {
                    this.catatan = data.catatan;
                }

                this.iklanSuccessMsg = `Data Faktur Iklan ${data.media} (${data.no_faktur}) a.n. ${data.nama_customer} dengan total Rp ${this.formatRupiah(data.total_tagihan)} berhasil dimuat ke formulir!`;

                if (data.already_registered) {
                    this.iklanWarningMsg = `Perhatian: Nomor faktur ini sudah pernah dibukukan di Keuangan (${data.registered_no}). Pastikan tidak terjadi duplikasi.`;
                }

            } catch (err) {
                console.error(err);
                this.iklanErrorMsg = 'Gagal menghubungi server untuk mengambil data faktur iklan.';
            } finally {
                this.isLoadingIklan = false;
            }
        },

        addItem() {
            this.items.push({ nama: '', qty: 1, harga: 0, subtotal: 0 });
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        calculateSubtotal(item) {
            item.subtotal = (Number(item.qty) || 0) * (Number(item.harga) || 0);
        },
        get totalTagihan() {
            return this.items.reduce((sum, item) => sum + (Number(item.subtotal) || 0), 0);
        },
        get sisaPiutang() {
            if (this.tipePembayaran === 'lunas_langsung') return 0;
            return Math.max(0, this.totalTagihan - (Number(this.nominalDp) || 0));
        },
        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    }">

        <!-- Header Halaman (Standar Divisi Iklan 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-9 sm:h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Penerbitan Faktur Baru
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Rincian transaksi penjualan divisi Iklan atau Sirkulasi dan penentuan skema pembayaran
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('keuangan.faktur.index') }}" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer">
                    <span>← Kembali</span>
                </a>
            </div>
        </div>

        @if(session('error'))
            <div class="p-4 bg-slate-100 border border-slate-300 text-slate-800 font-semibold rounded-xl text-sm flex items-center gap-2 shadow-2xs">
                <span>✕</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('keuangan.faktur.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Blok 1: Informasi Dasar & Pelanggan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                <div class="pb-3 border-b border-slate-200">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                        1. Data Transaksi &amp; Pelanggan
                    </h2>
                </div>

                <!-- Box Tarik Data Otomatis -->
                <div x-show="kategori === 'iklan'" x-transition class="p-4 rounded-xl bg-kp-blue-50/70 border border-kp-blue-200 shadow-2xs space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 uppercase">
                                Tarik Data Otomatis dari Faktur Divisi Iklan
                            </h3>
                            <p class="text-xs text-slate-500">
                                Ketik atau pilih nomor faktur dari divisi iklan (Koran / Online / TV) agar formulir terisi otomatis
                            </p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-white border border-kp-blue-200 px-2.5 py-0.5 rounded-md shrink-0">
                            Auto-Sync Iklan
                        </span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <div class="relative flex-1">
                            <input type="text"
                                   x-model="inputNoFakturIklan"
                                   list="list-faktur-iklan"
                                   @keydown.enter.prevent="tarikDataIklan(inputNoFakturIklan)"
                                   placeholder="Contoh: FKKRN001, FKONL001, FKPTV001..."
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 placeholder:font-sans placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">

                            <datalist id="list-faktur-iklan">
                                @if(isset($fakturIklanList))
                                    @foreach($fakturIklanList as $fi)
                                        <option value="{{ $fi['no_faktur'] }}">
                                            [{{ $fi['media'] }}] {{ $fi['pemasang'] }} - Rp {{ number_format($fi['tagihan'], 0, ',', '.') }} (AE: {{ $fi['sales'] }})
                                        </option>
                                    @endforeach
                                @endif
                            </datalist>
                        </div>

                        <button type="button"
                                @click="tarikDataIklan(inputNoFakturIklan)"
                                :disabled="isLoadingIklan"
                                class="h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2 disabled:opacity-60 cursor-pointer shrink-0">
                            <template x-if="isLoadingIklan">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </template>
                            <span x-text="isLoadingIklan ? 'Memuat Data...' : 'Muat Data Iklan'"></span>
                        </button>
                    </div>

                    <template x-if="iklanSuccessMsg">
                        <div class="p-3 rounded-xl bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 text-xs font-semibold flex items-center gap-2">
                            <span>✓</span>
                            <span x-text="iklanSuccessMsg"></span>
                        </div>
                    </template>

                    <template x-if="iklanWarningMsg">
                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs font-semibold flex items-center gap-2">
                            <span>[!]</span>
                            <span x-text="iklanWarningMsg"></span>
                        </div>
                    </template>

                    <template x-if="iklanErrorMsg">
                        <div class="p-3 rounded-xl bg-slate-100 border border-slate-300 text-slate-800 text-xs font-semibold flex items-center gap-2">
                            <span>✕</span>
                            <span x-text="iklanErrorMsg"></span>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Divisi / Kanal <span class="text-rose-500">*</span></label>
                        <select name="kategori" x-model="kategori" @change="onKategoriChange()" required 
                                class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs font-semibold">
                            <option value="iklan">Divisi Iklan</option>
                            <option value="sirkulasi">Divisi Sirkulasi</option>
                        </select>
                    </div>

                    <div class="sm:col-span-3" x-show="kategori === 'iklan'">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tipe Faktur Iklan <span class="text-rose-500">*</span></label>
                        <select x-model="tipeFakturIklan" @change="onTipeIklanChange()" 
                                class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs font-semibold">
                            <option value="reguler">Faktur Reguler</option>
                            <option value="susulan">Faktur Iklan Susulan</option>
                            <option value="stopper">Iklan Stopper</option>
                            <option value="bonus">Iklan Bonus / Barter</option>
                        </select>
                    </div>

                    <div class="sm:col-span-3" x-show="kategori === 'sirkulasi'">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tipe Faktur Sirkulasi <span class="text-rose-500">*</span></label>
                        <select x-model="tipeFakturSirkulasi" @change="onTipeSirkulasiChange()" 
                                class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs font-semibold">
                            <option value="reguler">Faktur Sirkulasi Reguler</option>
                            <option value="susulan">Faktur Sirkulasi Susulan</option>
                            <option value="insidentil">Faktur Insidentil</option>
                        </select>
                    </div>
                    <input type="hidden" name="tipe_faktur" :value="kategori === 'iklan' ? tipeFakturIklan : tipeFakturSirkulasi">

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor Faktur <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_faktur" x-model="noFaktur"
                               @change="if (kategori === 'iklan' && (noFaktur.toUpperCase().startsWith('FK') || noFaktur.length > 5)) tarikDataIklan(noFaktur)"
                               list="list-faktur-iklan"
                               required
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-kp-blue-700 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <div class="sm:col-span-3" x-show="kategori === 'iklan'">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Account Executive (AE)</label>
                        <input type="text" name="sales_nama" x-model="salesNama" list="ae-suggestions" placeholder="Pilih / ketik nama AE"
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <datalist id="ae-suggestions">
                            @if(isset($aeList))
                                @foreach($aeList as $ae)
                                    <option value="{{ $ae }}"></option>
                                @endforeach
                            @endif
                        </datalist>
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Customer / Klien <span class="text-rose-500">*</span></label>
                        <select name="customer_id" id="select_customer" x-model="customerId" required 
                                class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->nama_customer }} ({{ ucfirst($c->tipe) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Faktur <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_faktur" x-model="tanggalFaktur" required
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jatuh Tempo <span class="text-rose-500">*</span></label>
                        <input type="date" name="jatuh_tempo" x-model="jatuhTempo" required
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                </div>
            </div>

            <!-- Blok 2: Rincian Item Tagihan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                        2. Rincian Biaya &amp; Item Tagihan
                    </h2>
                    <button type="button" @click="addItem()" class="h-9 px-3.5 rounded-xl bg-kp-blue-50 hover:bg-kp-blue-100 text-kp-blue-700 text-xs font-bold border border-kp-blue-200 transition cursor-pointer">
                        + Tambah Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5">Uraian / Deskripsi Item</th>
                                <th class="py-3 px-3.5 w-28 text-center">Qty</th>
                                <th class="py-3 px-3.5 w-44 text-right">Harga Satuan (Rp)</th>
                                <th class="py-3 px-3.5 w-44 text-right">Subtotal (Rp)</th>
                                <th class="py-3 px-3.5 w-16 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="py-3 px-3.5">
                                        <input type="text" :name="'item_nama[' + index + ']'" x-model="item.nama" required
                                               placeholder="Contoh: Iklan Display / Oplah Koran..."
                                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                                    </td>
                                    <td class="py-3 px-3.5">
                                        <input type="number" :name="'item_qty[' + index + ']'" x-model="item.qty" @input="calculateSubtotal(item)" min="1" required
                                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-center text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                                    </td>
                                    <td class="py-3 px-3.5">
                                        <input type="number" :name="'item_harga[' + index + ']'" x-model="item.harga" @input="calculateSubtotal(item)" min="0" step="1000" required
                                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-right font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                                    </td>
                                    <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums" x-text="formatRupiah(item.subtotal)"></span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3.5 text-center">
                                        <button type="button" @click="removeItem(index)" :disabled="items.length === 1"
                                                class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition disabled:opacity-30 cursor-pointer">
                                            ✕
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="border-t border-slate-200 bg-slate-50 font-bold">
                            <tr>
                                <td colspan="3" class="py-3 px-3.5 text-right font-semibold text-slate-700">
                                    Total Tagihan Faktur:
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono text-sm">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-bold text-kp-blue-700 tabular-nums" x-text="formatRupiah(totalTagihan)"></span>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Blok 3: Skema Pembayaran -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-200">
                    3. Skema Pembayaran
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="p-4 rounded-xl border cursor-pointer transition flex items-start gap-3"
                           :class="tipePembayaran === 'lunas_langsung' ? 'border-kp-blue-600 bg-kp-blue-50/50' : 'border-slate-200 bg-white hover:bg-slate-50'">
                        <input type="radio" name="tipe_pembayaran" value="lunas_langsung" x-model="tipePembayaran" class="mt-1 text-kp-blue-600 focus:ring-kp-blue-500">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-900 uppercase">1. Bayar Lunas Langsung (100%)</span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Tanpa Inkaso</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Transaksi dibayar penuh saat faktur dibuat. Otomatis tercatat di kas masuk dan tidak masuk antrean petugas Inkaso.
                            </p>
                        </div>
                    </label>

                    <label class="p-4 rounded-xl border cursor-pointer transition flex items-start gap-3"
                           :class="tipePembayaran === 'cicilan_tempo' ? 'border-kp-blue-600 bg-kp-blue-50/50' : 'border-slate-200 bg-white hover:bg-slate-50'">
                        <input type="radio" name="tipe_pembayaran" value="cicilan_tempo" x-model="tipePembayaran" class="mt-1 text-kp-blue-600 focus:ring-kp-blue-500">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-900 uppercase">2. Bayar Bertahap / Tempo / Piutang</span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-300">Wajib Inkaso</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Jika ada uang muka (DP), nominal masuk Kas Masuk. Sisa saldo piutang otomatis masuk ke modul Penugasan Inkaso.
                            </p>
                        </div>
                    </label>
                </div>

                <div x-show="tipePembayaran === 'cicilan_tempo'" x-transition class="p-4 rounded-xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nominal Uang Muka / DP (Rp)</label>
                        <input type="number" name="nominal_dp" x-model="nominalDp" min="0" :max="totalTagihan" step="1000"
                               placeholder="0 jika belum ada pembayaran"
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                        <span class="text-xs text-slate-400 mt-1 block">Isi 0 jika piutang penuh / tempo murni.</span>
                    </div>

                    <div class="sm:col-span-6 flex flex-col justify-center">
                        <div class="text-xs font-semibold text-slate-500">Simulasi Sisa Saldo Piutang:</div>
                        <div class="text-lg font-bold text-kp-blue-700 font-mono mt-0.5">
                            Rp <span x-text="formatRupiah(sisaPiutang)"></span>
                        </div>
                        <span class="text-xs text-slate-400">Nominal ini yang akan ditagihkan petugas Inkaso lapangan.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-2">
                    <div class="sm:col-span-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Metode Bayar <span class="text-rose-500">*</span></label>
                        <select name="metode_bayar" required 
                                class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs font-semibold">
                            <option value="tunai">Tunai / Cash Kasir</option>
                            <option value="transfer">Transfer Rekening Bank</option>
                        </select>
                    </div>

                    <div class="sm:col-span-8">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Catatan Tambahan (Opsional)</label>
                        <input type="text" name="catatan" x-model="catatan" placeholder="Catatan kesepakatan tempo, nomor PO customer, dll..."
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="flex items-center justify-end gap-2.5 pt-2">
                <a href="{{ route('keuangan.faktur.index') }}" class="h-11 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold transition flex items-center justify-center">
                    Batal
                </a>
                <button type="submit" class="h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                    Terbitkan Faktur
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
