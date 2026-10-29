<x-app-layout>
    <div class="space-y-6" x-data="{
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

        // State Tarik Data dari Divisi Iklan
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

                // Berhasil ditemukan! Autofill seluruh formulir:
                this.kategori = 'iklan';

                // 1. Nomor Faktur
                if (data.no_faktur) {
                    this.noFaktur = data.no_faktur;
                    this.inputNoFakturIklan = data.no_faktur;
                }

                // 2. Customer
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

                // 3. AE / Sales
                if (data.sales_nama) {
                    this.salesNama = data.sales_nama;
                }

                // 4. Tanggal Transaksi & Jatuh Tempo
                if (data.tanggal_faktur) {
                    this.tanggalFaktur = data.tanggal_faktur;
                }
                if (data.jatuh_tempo) {
                    this.jatuhTempo = data.jatuh_tempo;
                }

                // 5. Rincian Item Tagihan
                if (data.items && data.items.length > 0) {
                    this.items = data.items.map(it => ({
                        nama: it.nama,
                        qty: Number(it.qty) || 1,
                        harga: Number(it.harga) || 0,
                        subtotal: Number(it.subtotal) || 0
                    }));
                }

                // 6. Skema Pembayaran
                if (data.tipe_pembayaran) {
                    this.tipePembayaran = data.tipe_pembayaran;
                }
                this.nominalDp = Number(data.nominal_dp) || 0;

                // 7. Catatan
                if (data.catatan) {
                    this.catatan = data.catatan;
                }

                // Pesan Sukses
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

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.faktur.index') }}" class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition">
                    ←
                </a>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                        PENERBITAN FAKTUR BARU
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Isi rincian transaksi penjualan divisi Iklan atau Sirkulasi dan tentukan skema pembayaran
                    </p>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('keuangan.faktur.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Blok 1: Informasi Dasar & Customer -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        1. Data Transaksi &amp; Pelanggan
                    </h2>
                    <span class="text-[11px] text-slate-500 font-medium">
                        Bisa input manual atau tarik otomatis dari no faktur iklan
                    </span>
                </div>

                <!-- Box Khusus: Tarik Data Otomatis dari Faktur Iklan -->
                <div x-show="kategori === 'iklan'" x-transition class="p-4 rounded-2xl bg-gradient-to-r from-blue-50/90 via-indigo-50/70 to-sky-50 border border-blue-200/90 shadow-2xs space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xs font-black shadow-xs">
                                ⚡
                            </span>
                            <div>
                                <h3 class="text-xs font-black text-slate-900 uppercase tracking-tight">
                                    Tarik Data Otomatis dari Faktur Divisi Iklan
                                </h3>
                                <p class="text-[11px] text-slate-500">
                                    Ketik atau pilih nomor faktur dari divisi iklan (Koran / Online / TV) agar data terisi seketika
                                </p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-blue-700 bg-blue-100/80 border border-blue-200 px-2.5 py-1 rounded-full shrink-0">
                            Sinkronisasi Iklan ➔ Keuangan
                        </span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <div class="relative flex-1">
                            <input type="text"
                                   x-model="inputNoFakturIklan"
                                   list="list-faktur-iklan"
                                   @keydown.enter.prevent="tarikDataIklan(inputNoFakturIklan)"
                                   placeholder="Contoh: FKKRN001, FKONL001, FKPTV001 atau ketik nomor faktur iklan..."
                                   class="w-full text-xs font-mono font-bold rounded-xl border border-blue-300 px-3.5 py-2.5 bg-white text-slate-900 placeholder:font-sans placeholder:font-normal placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500 shadow-2xs">

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
                                class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-bold shadow-xs transition flex items-center justify-center gap-2 disabled:opacity-60 cursor-pointer shrink-0">
                            <template x-if="isLoadingIklan">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </template>
                            <span x-text="isLoadingIklan ? 'Memuat Data...' : '🔍 Muat Data Iklan'"></span>
                        </button>
                    </div>

                    <!-- Notifikasi Pesan -->
                    <template x-if="iklanSuccessMsg">
                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                            <span class="text-emerald-600 font-black">✓</span>
                            <span x-text="iklanSuccessMsg"></span>
                        </div>
                    </template>

                    <template x-if="iklanWarningMsg">
                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold flex items-center gap-2">
                            <span class="text-amber-600 font-black">⚠️</span>
                            <span x-text="iklanWarningMsg"></span>
                        </div>
                    </template>

                    <template x-if="iklanErrorMsg">
                        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
                            <span class="text-rose-600 font-black">✕</span>
                            <span x-text="iklanErrorMsg"></span>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <!-- Divisi / Kategori -->
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Divisi / Kanal <span class="text-rose-500">*</span></label>
                        <select name="kategori" x-model="kategori" @change="onKategoriChange()" required class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 font-semibold text-slate-800">
                            <option value="iklan">Divisi Iklan</option>
                            <option value="sirkulasi">Divisi Sirkulasi</option>
                        </select>
                    </div>

                    <!-- Tipe Faktur (Khusus Iklan) -->
                    <div class="sm:col-span-3" x-show="kategori === 'iklan'">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Faktur Iklan <span class="text-rose-500">*</span></label>
                        <select x-model="tipeFakturIklan" @change="onTipeIklanChange()" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 font-semibold text-slate-800">
                            <option value="reguler">Faktur Reguler (Koran / Online / TV)</option>
                            <option value="susulan">⚡ Faktur Iklan Susulan (Order Mendadak / Closing)</option>
                            <option value="stopper">📰 Iklan Stopper (Pengisi Ruang)</option>
                            <option value="bonus">🎁 Iklan Bonus / Barter Promo</option>
                        </select>
                    </div>

                    <!-- Tipe Faktur (Khusus Sirkulasi) -->
                    <div class="sm:col-span-3" x-show="kategori === 'sirkulasi'">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Faktur Sirkulasi <span class="text-rose-500">*</span></label>
                        <select x-model="tipeFakturSirkulasi" @change="onTipeSirkulasiChange()" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 font-semibold text-slate-800">
                            <option value="reguler">Faktur Sirkulasi Reguler (Koran Harian)</option>
                            <option value="susulan">⚡ Faktur Sirkulasi Susulan (Oplah Tambahan)</option>
                            <option value="insidentil">🏷️ Faktur Insidentil (Eceran Langsung / Event)</option>
                        </select>
                    </div>
                    <input type="hidden" name="tipe_faktur" :value="kategori === 'iklan' ? tipeFakturIklan : tipeFakturSirkulasi">

                    <!-- Nomor Faktur -->
                    <div class="sm:col-span-3">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700">Nomor Faktur <span class="text-rose-500">*</span></label>
                            <button type="button"
                                    x-show="kategori === 'iklan'"
                                    @click="tarikDataIklan(noFaktur)"
                                    title="Tarik data otomatis dari nomor faktur ini"
                                    class="text-[10px] font-bold text-blue-600 hover:text-blue-800 underline">
                                Cek Iklan
                            </button>
                        </div>
                        <input type="text" name="no_faktur" x-model="noFaktur"
                               @change="if (kategori === 'iklan' && (noFaktur.toUpperCase().startsWith('FK') || noFaktur.length > 5)) tarikDataIklan(noFaktur)"
                               list="list-faktur-iklan"
                               required
                               class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2.5 bg-slate-50 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Account Executive (AE) / Sales -->
                    <div class="sm:col-span-3" x-show="kategori === 'iklan'">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Account Executive (AE)</label>
                        <input type="text" name="sales_nama" x-model="salesNama" list="ae-suggestions" placeholder="Pilih atau ketik nama AE"
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 font-medium">
                        <datalist id="ae-suggestions">
                            @if(isset($aeList))
                                @foreach($aeList as $ae)
                                    <option value="{{ $ae }}"></option>
                                @endforeach
                            @endif
                        </datalist>
                    </div>

                    <!-- Pilihan Customer -->
                    <div class="sm:col-span-6">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Customer / Klien <span class="text-rose-500">*</span></label>
                        <select name="customer_id" id="select_customer" x-model="customerId" required class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->nama_customer }} ({{ ucfirst($c->tipe) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal Faktur -->
                    <div class="sm:col-span-6">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Transaksi Faktur <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_faktur" x-model="tanggalFaktur" required
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Tanggal Jatuh Tempo -->
                    <div class="sm:col-span-6">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Jatuh Tempo <span class="text-rose-500">*</span></label>
                        <input type="date" name="jatuh_tempo" x-model="jatuhTempo" required
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Blok 2: Rincian Biaya Transaksi -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        2. Rincian Biaya &amp; Item Tagihan
                    </h2>

                    <button type="button" @click="addItem()" class="h-8 px-3 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold flex items-center gap-1 transition">
                        + Tambah Baris Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-[11px] uppercase text-slate-500 font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-3">Uraian / Deskripsi Item</th>
                                <th class="py-2.5 px-3 w-28 text-center">Qty</th>
                                <th class="py-2.5 px-3 w-44 text-right">Harga Satuan (Rp)</th>
                                <th class="py-2.5 px-3 w-44 text-right">Subtotal (Rp)</th>
                                <th class="py-2.5 px-3 w-16 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="py-2.5 px-3">
                                        <input type="text" :name="'item_nama[' + index + ']'" x-model="item.nama" required
                                               placeholder="Contoh: Iklan Display 1 Halaman Berwarna / Oplah Koran 500 eks..."
                                               class="w-full text-xs rounded-lg border border-slate-300 px-2.5 py-1.5 focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <input type="number" :name="'item_qty[' + index + ']'" x-model="item.qty" @input="calculateSubtotal(item)" min="1" required
                                               class="w-full text-xs text-center rounded-lg border border-slate-300 px-2.5 py-1.5 focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <input type="number" :name="'item_harga[' + index + ']'" x-model="item.harga" @input="calculateSubtotal(item)" min="0" step="1000" required
                                               class="w-full text-xs text-right rounded-lg border border-slate-300 px-2.5 py-1.5 focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp <span x-text="formatRupiah(item.subtotal)"></span>
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <button type="button" @click="removeItem(index)" :disabled="items.length === 1"
                                                class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition disabled:opacity-30">
                                            ✕
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="border-t-2 border-slate-200 bg-slate-50">
                            <tr>
                                <td colspan="3" class="py-3 px-3 text-right font-bold text-slate-700 uppercase">
                                    Total Tagihan Faktur:
                                </td>
                                <td class="py-3 px-3 text-right font-black text-slate-900 text-sm font-mono">
                                    Rp <span x-text="formatRupiah(totalTagihan)"></span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Blok 3: Logika Pembayaran & Integrasi Inkaso/Kas -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    3. Logika Pembayaran saat Faktur Dibuat
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Opsi 1: Lunas Langsung -->
                    <label class="p-4 rounded-2xl border-2 cursor-pointer transition flex items-start gap-3.5"
                           :class="tipePembayaran === 'lunas_langsung' ? 'border-emerald-500 bg-emerald-50/60 ring-2 ring-emerald-200' : 'border-slate-200 bg-slate-50 hover:bg-white'">
                        <input type="radio" name="tipe_pembayaran" value="lunas_langsung" x-model="tipePembayaran" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-black text-emerald-950 uppercase">1. Bayar Lunas Langsung (100%)</span>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-emerald-200 text-emerald-800">Tanpa Inkaso</span>
                            </div>
                            <p class="text-[11px] text-emerald-800 mt-1 leading-relaxed">
                                Transaksi dibayar penuh saat faktur dibuat. <strong>Otomatis mencatat Kas Masuk 100%</strong> dan <strong>TIDAK masuk antrean petugas Inkaso</strong>.
                            </p>
                        </div>
                    </label>

                    <!-- Opsi 2: Cicilan / Tempo -->
                    <label class="p-4 rounded-2xl border-2 cursor-pointer transition flex items-start gap-3.5"
                           :class="tipePembayaran === 'cicilan_tempo' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-200' : 'border-slate-200 bg-slate-50 hover:bg-white'">
                        <input type="radio" name="tipe_pembayaran" value="cicilan_tempo" x-model="tipePembayaran" class="mt-1 text-amber-600 focus:ring-amber-500">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-black text-amber-950 uppercase">2. Bayar Bertahap / Nyicil / Tempo</span>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-amber-200 text-amber-800">Wajib Inkaso</span>
                            </div>
                            <p class="text-[11px] text-amber-800 mt-1 leading-relaxed">
                                Jika ada uang muka (DP), nominal DP langsung masuk Kas Masuk. <strong>Sisa piutang otomatis masuk ke modul Penugasan Inkaso</strong>.
                            </p>
                        </div>
                    </label>
                </div>

                <!-- Input Khusus jika Cicilan/Tempo -->
                <div x-show="tipePembayaran === 'cicilan_tempo'" x-transition class="p-4 rounded-xl bg-amber-50 border border-amber-200 grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <div class="sm:col-span-6">
                        <label class="block text-xs font-bold text-amber-900 mb-1">Nominal Uang Muka / DP (Rp)</label>
                        <input type="number" name="nominal_dp" x-model="nominalDp" min="0" :max="totalTagihan" step="1000"
                               placeholder="0 jika belum ada pembayaran"
                               class="w-full text-xs font-mono font-bold rounded-lg border border-amber-300 px-3 py-2 bg-white focus:ring-2 focus:ring-amber-500">
                        <span class="text-[10px] text-amber-700 mt-0.5 block">Isi 0 jika tempo murni (belum ada pembayaran sama sekali).</span>
                    </div>

                    <div class="sm:col-span-6 flex flex-col justify-center">
                        <div class="text-xs text-amber-800 font-semibold">Simulasi Sisa Piutang Berjalan:</div>
                        <div class="text-lg font-black text-rose-600 font-mono mt-0.5">
                            Rp <span x-text="formatRupiah(sisaPiutang)"></span>
                        </div>
                        <span class="text-[10px] text-slate-500">Nominal ini yang akan ditagihkan oleh Petugas Inkaso lapangan.</span>
                    </div>
                </div>

                <!-- Metode Pembayaran & Catatan -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 pt-2">
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Metode Penerimaan Dana <span class="text-rose-500">*</span></label>
                        <select name="metode_bayar" required class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                            <option value="tunai">Tunai / Cash Kasir</option>
                            <option value="transfer">Transfer Rekening Bank</option>
                        </select>
                    </div>

                    <div class="sm:col-span-8">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                        <input type="text" name="catatan" x-model="catatan" placeholder="Catatan kesepakatan tempo, nomor PO customer, dll..."
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('keuangan.faktur.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                    Terbitkan Faktur Sekarang ➔
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
