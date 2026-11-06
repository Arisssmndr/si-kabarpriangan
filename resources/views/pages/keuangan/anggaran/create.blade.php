<x-app-layout>
    <div class="space-y-6" x-data="{
        divisi: '{{ old('divisi', $selectedDivisi ?? '') }}',
        allCoa: {{ json_encode($allCoaByBidang) }},
        plafonByBidang: {{ json_encode($plafonByBidang) }},
        selectedKode: '{{ old('kode_perkiraan', '') }}',
        selectedNama: '{{ old('nama_perkiraan', '') }}',
        metodePencairan: '{{ old('metode_pencairan', 'tunai') }}',
        nominal: '{{ old('nominal_diajukan', '') }}',

        get currentCoaList() {
            if (!this.divisi || !this.allCoa[this.divisi]) return [];
            return this.allCoa[this.divisi];
        },
        get currentPlafon() {
            if (!this.divisi || !this.plafonByBidang[this.divisi]) {
                return { plafon: 0, realisasi: 0, sisa: 0 };
            }
            return this.plafonByBidang[this.divisi];
        },
        get selectedCoaDetail() {
            if (!this.selectedKode) return null;
            return this.currentCoaList.find(c => c.kode === this.selectedKode) || null;
        },
        onBidangChange() {
            this.selectedKode = '';
            this.selectedNama = '';
        },
        onCoaChange(event) {
            const val = event.target.value;
            this.selectedKode = val;
            const item = this.currentCoaList.find(c => c.kode === val);
            if (item) {
                this.selectedNama = item.nama;
            } else {
                this.selectedNama = '';
            }
        },
        formatRupiah(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        }
    }">

        <!-- Header Entry BP Priangan Sesuai Standar DOS ANGGARAN.EXE & Continuous Form -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.anggaran.index') }}" class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition cursor-pointer font-bold text-sm">
                    ←
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <h1 class="text-xl font-extrabold text-blue-950 tracking-tight">
                            ENTRY BP PRIANGAN (BUKTI PENGELUARAN ANGGARAN)
                        </h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pendaftaran dokumen BP operasional unit bidang &amp; pembebanan kode perkiraan (COA) terintegrasi ke buku kas
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200">
                    Standar DOS ANGGARAN.EXE
                </span>
            </div>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                <div class="font-bold mb-1">Terdapat kesalahan pengisian formulir:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="max-w-4xl mx-auto">
            <form action="{{ route('keuangan.anggaran.store') }}" method="POST" class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs space-y-6">
                @csrf

                <!-- BAGIAN 1: IDENTITAS DOKUMEN & BIDANG -->
                <div>
                    <h2 class="text-xs font-black text-blue-950 uppercase tracking-wider pb-2 border-b border-slate-200 flex items-center justify-between">
                        <span>1. Identitas Dokumen &amp; Unit Bidang</span>
                        <span class="text-[10px] text-slate-400 font-normal normal-case">Nomor register otomatis diterbitkan saat disetujui</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <!-- Nomor Pengajuan Dokumen -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Pengajuan Dokumen <span class="text-rose-500">*</span></label>
                            <input type="text" name="no_pengajuan" value="{{ old('no_pengajuan', $noPengajuanSuggested) }}" required readonly
                                   class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2.5 bg-slate-50 text-slate-700 cursor-not-allowed">
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Format: ANG-YYYYMM-XXX (Nomor antrean resmi pengajuan)</span>
                        </div>

                        <!-- Nomor BP Priangan (Sistem DOS) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-slate-700">Nomor BP Priangan (Sistem DOS) <span class="text-rose-500">*</span></label>
                                <span class="text-[10px] font-semibold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">Auto / Bisa Diedit</span>
                            </div>
                            <input type="text" name="no_bp" value="{{ old('no_bp', $noBpSuggested ?? 'BP-' . date('Ym') . '-0001') }}" required
                                   class="w-full text-xs font-mono font-bold rounded-xl border border-blue-300 px-3 py-2.5 bg-white text-blue-950 focus:ring-2 focus:ring-blue-500 shadow-2xs">
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Format resmi DOS BP: BP-YYYYMM-XXXX (Dicetak pada continuous form)</span>
                        </div>

                        <!-- Bidang Priangan (Kode Bagian 01 s/d 21) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Bidang Priangan (Kode Bagian) <span class="text-rose-500">*</span></label>
                            <select name="divisi" x-model="divisi" @change="onBidangChange()" required
                                    class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 font-semibold text-slate-800">
                                <option value="">-- Pilih Bidang Priangan (01 s/d 21) --</option>
                                @foreach($daftarDivisi as $key => $label)
                                    <option value="{{ $key }}" {{ old('divisi', $selectedDivisi ?? '') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Pilih unit kerja penanggung jawab anggaran operasional</span>
                        </div>

                        <!-- Tanggal Pengajuan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pengajuan <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_pengajuan" value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}" required
                                   class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 text-slate-800 font-semibold">
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Tanggal buku pengajuan bukti pengeluaran</span>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN 2: PEMBEBANAN KODE PERKIRAAN (COA) & SISA RAB -->
                <div class="pt-2">
                    <h2 class="text-xs font-black text-blue-950 uppercase tracking-wider pb-2 border-b border-slate-200 flex items-center justify-between">
                        <span>2. Pembebanan Akun Perkiraan (COA Sesuai Printout Cetak)</span>
                        <span class="text-[10px] text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded">Klasifikasi Biaya Otomatis</span>
                    </h2>

                    <!-- Panel Info Plafon Bidang Aktif -->
                    <div x-show="divisi" class="mt-4 p-3.5 bg-slate-50 border border-slate-200 rounded-xl grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 block uppercase">Plafon RAB Bulan Ini</span>
                            <span class="font-mono font-bold text-slate-800 text-sm" x-text="formatRupiah(currentPlafon.plafon)"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 block uppercase">Total Realisasi Terpakai</span>
                            <span class="font-mono font-bold text-blue-700 text-sm" x-text="formatRupiah(currentPlafon.realisasi)"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 block uppercase">Sisa Plafon Bidang</span>
                            <span class="font-mono font-black text-sm"
                                  :class="currentPlafon.sisa >= 0 ? 'text-emerald-700' : 'text-rose-700'"
                                  x-text="formatRupiah(currentPlafon.sisa)"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <!-- Dropdown Kode Perkiraan -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Kode Perkiraan &amp; Nama Akun (COA) <span class="text-rose-500">*</span>
                            </label>
                            <select name="kode_perkiraan" x-model="selectedKode" @change="onCoaChange($event)" required
                                    class="w-full text-xs font-mono font-semibold rounded-xl border border-blue-300 px-3 py-2.5 bg-white text-slate-800 focus:ring-2 focus:ring-blue-500 shadow-2xs">
                                <option value="">-- Pilih Kode Perkiraan (COA) Sesuai Rincian Biaya --</option>
                                <template x-for="item in currentCoaList" :key="item.kode">
                                    <option :value="item.kode" x-text="item.kode + ' - ' + item.nama"></option>
                                </template>
                            </select>
                            <input type="hidden" name="nama_perkiraan" :value="selectedNama">
                            <span class="text-[10px] text-slate-500 mt-0.5 block">
                                Pilih kode pos akun biaya untuk mencatat klasifikasi pengeluaran pada pembukuan.
                            </span>
                        </div>

                        <!-- Badge Detail Akun Terpilih -->
                        <div x-show="selectedCoaDetail" class="sm:col-span-2 p-3 bg-blue-50/60 border border-blue-200 rounded-xl text-xs text-blue-950 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="font-bold text-[10px] text-blue-700 uppercase tracking-wider block">Akun Terpilih:</span>
                                <span class="font-mono font-bold" x-text="selectedKode"></span> &mdash; <span class="font-semibold" x-text="selectedNama"></span>
                            </div>
                            <div class="flex items-center gap-3 text-right">
                                <template x-if="selectedCoaDetail && selectedCoaDetail.alokasi > 0">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <span class="text-[10px] text-slate-500 block uppercase">Plafon Jatah RAB</span>
                                            <span class="font-mono font-bold text-slate-700" x-text="formatRupiah(selectedCoaDetail.alokasi)"></span>
                                        </div>
                                        <div class="border-l border-blue-200 pl-3">
                                            <span class="text-[10px] text-slate-500 block uppercase">Sisa Jatah Pos</span>
                                            <span class="font-mono font-black text-emerald-700" x-text="formatRupiah(selectedCoaDetail.sisa)"></span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="selectedCoaDetail && selectedCoaDetail.alokasi <= 0">
                                    <span class="text-[11px] text-slate-500 italic bg-white px-2 py-1 rounded border border-slate-200">
                                        Mengikuti pagu/plafon umum bidang
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN 3: NOMINAL PENGAJUAN & PENERIMA DANA -->
                <div class="pt-2">
                    <h2 class="text-xs font-black text-blue-950 uppercase tracking-wider pb-2 border-b border-slate-200 flex items-center justify-between">
                        <span>3. Nominal Usulan &amp; Jalur Pencairan Dana</span>
                        <span class="text-[10px] text-slate-400 font-normal normal-case">Diverifikasi &amp; di-ACC Manajer Keuangan</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <!-- Nominal Diajukan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Usulan BP (Rp) <span class="text-rose-500">*</span></label>
                            <input type="number" name="nominal_diajukan" x-model="nominal" min="1000" step="1000" required placeholder="Contoh: 1500000"
                                   class="w-full text-base font-mono font-black rounded-xl border border-blue-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 text-blue-950 bg-blue-50/20">
                            <div class="flex items-center justify-between mt-1 text-[11px]">
                                <span class="text-slate-500">Terbaca:</span>
                                <span class="font-mono font-bold text-blue-700" x-text="formatRupiah(nominal)"></span>
                            </div>
                        </div>

                        <!-- Pemohon / Penerima -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemohon / Penerima Dana <span class="text-rose-500">*</span></label>
                            <input type="text" name="pemohon" value="{{ old('pemohon', Auth::user()->name ?? '') }}" required placeholder="Nama staf / penanggungjawab teknis..."
                                   class="w-full text-xs font-bold rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 text-slate-800">
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Nama orang yang menerima pencairan dana</span>
                        </div>

                        <!-- Metode Pencairan Kas -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pencairan Kas <span class="text-rose-500">*</span></label>
                            <select name="metode_pencairan" x-model="metodePencairan" required
                                    class="w-full text-xs font-semibold rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 text-slate-800">
                                <option value="tunai">Kasir (Kas Tunai Priangan)</option>
                                <option value="transfer">Transfer Rekening Bank Priangan</option>
                            </select>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Kasir akan mencairkan sesuai metode yang dipilih</span>
                        </div>

                        <!-- Form Tambahan Khusus Transfer Bank -->
                        <div x-show="metodePencairan === 'transfer'" class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Bank Priangan</label>
                                <select name="bank_penerima" class="w-full text-xs font-semibold rounded-xl border border-slate-300 px-2 py-2.5 focus:ring-2 focus:ring-blue-500">
                                    <option value="BCA">BCA (Operasional)</option>
                                    <option value="BJB">BJB (Koran)</option>
                                    <option value="Mandiri">Mandiri (Iklan)</option>
                                    <option value="BRI">BRI (Sirkulasi)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">No. Rekening Tujuan</label>
                                <input type="text" name="no_rekening" value="{{ old('no_rekening') }}" placeholder="No. rekening..."
                                       class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-2 py-2.5 focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN 4: RINCIAN KEPERLUAN -->
                <div class="pt-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Rincian Keperluan Penggunaan Dana <span class="text-rose-500">*</span></label>
                    <textarea name="keterangan" rows="3" required placeholder="Jelaskan kebutuhan pengeluaran dana operasional secara spesifik (misal: Biaya peliputan liputan khusus Pilkada, pembelian kertas HVS &amp; tinta mesin cetak web, servis berkala armada distribusi, konsumsi rapat redaksi)..."
                              class="w-full text-xs rounded-xl border border-slate-300 p-3 focus:ring-2 focus:ring-blue-500 leading-relaxed text-slate-800">{{ old('keterangan') }}</textarea>
                </div>

                <!-- Notice Alur Integrasi Kas Priangan -->
                <div class="p-4 rounded-xl bg-blue-50/50 border border-blue-200 text-xs text-blue-950 leading-relaxed space-y-1">
                    <div class="font-bold text-blue-900">Alur Integrasi Pengeluaran Kas (DOS ANGGARAN.EXE &amp; Kasir Priangan):</div>
                    <p class="text-slate-600">
                        1. Dokumen BP yang didaftarkan berstatus <strong>Pending</strong>.<br>
                        2. Setelah diverifikasi &amp; disetujui Manajer Keuangan, sistem menerbitkan <strong>Nomor Register Anggaran (PR-YYYYMMDD-XXXX)</strong>.<br>
                        3. Kasir Priangan memproses pencairan fisik sehingga terbit <strong>Bukti Kas Keluar (BKK KO-YYYYMMXXXX)</strong> dan saldo kas terpotong otomatis.
                    </p>
                </div>

                <!-- Tombol Aksi (Penerapan Warna 60-30-10) -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <a href="{{ route('keuangan.anggaran.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-amber-950 text-xs font-black shadow-xs transition cursor-pointer border border-amber-500">
                        Simpan &amp; Terbitkan BP Priangan
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
