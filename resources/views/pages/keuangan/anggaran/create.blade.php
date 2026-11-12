<x-app-layout>
    <div class="space-y-6" x-data="{
        divisi: '{{ old('divisi', $selectedDivisi ?? '') }}',
        allCoa: {{ json_encode($allCoaByBidang) }},
        plafonByBidang: {{ json_encode($plafonByBidang) }},
        selectedKode: '{{ old('kode_perkiraan', '') }}',
        selectedNama: '{{ old('nama_perkiraan', '') }}',
        metodePencairan: '{{ old('metode_pencairan', 'tunai') }}',
        nominal: '{{ old('nominal_diajukan', '') }}',
        coaRows: [
            { kode: '{{ old('kode_perkiraan', '') }}', nama: '{{ old('nama_perkiraan', '') }}', nominal: '{{ old('nominal_diajukan', '') }}', rincian: '' }
        ],

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
            const firstKode = this.coaRows[0]?.kode || this.selectedKode;
            if (!firstKode) return null;
            return this.currentCoaList.find(c => c.kode === firstKode) || null;
        },
        getCoaDetailByKode(kode) {
            if (!kode) return null;
            return this.currentCoaList.find(c => c.kode === kode) || null;
        },
        onBidangChange() {
            this.selectedKode = '';
            this.selectedNama = '';
            this.coaRows = [{ kode: '', nama: '', nominal: '', rincian: '' }];
            this.recalcTotal();
        },
        onRowCoaChange(index, event) {
            const val = event.target.value;
            this.coaRows[index].kode = val;
            const item = this.currentCoaList.find(c => c.kode === val);
            this.coaRows[index].nama = item ? item.nama : '';
            if (index === 0) {
                this.selectedKode = val;
                this.selectedNama = this.coaRows[0].nama;
            }
        },
        addCoaRow() {
            this.coaRows.push({ kode: '', nama: '', nominal: '', rincian: '' });
        },
        removeCoaRow(index) {
            if (this.coaRows.length > 1) {
                this.coaRows.splice(index, 1);
                this.selectedKode = this.coaRows[0]?.kode || '';
                this.selectedNama = this.coaRows[0]?.nama || '';
                this.recalcTotal();
            }
        },
        recalcTotal() {
            const sum = this.coaRows.reduce((acc, r) => acc + (Number(r.nominal) || 0), 0);
            if (sum > 0) {
                this.nominal = sum;
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

                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700">
                                Daftar Kode Perkiraan &amp; Nama Akun (COA) <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" @click="addCoaRow()"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold transition shadow-2xs cursor-pointer">
                                <span>+ Tambah Kode Perkiraan (COA)</span>
                            </button>
                        </div>

                        <!-- Hidden utama untuk kompatibilitas 1 baris -->
                        <input type="hidden" name="kode_perkiraan" :value="coaRows[0]?.kode || ''">
                        <input type="hidden" name="nama_perkiraan" :value="coaRows[0]?.nama || ''">

                        <!-- Loop Baris Kode Perkiraan (Bisa 1 atau Lebih dari 1) -->
                        <template x-for="(row, idx) in coaRows" :key="idx">
                            <div class="p-3.5 rounded-xl border border-blue-200 bg-blue-50/20 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-extrabold text-blue-950 uppercase tracking-wider" x-text="'Rincian Perkiraan #' + (idx + 1)"></span>
                                    <button type="button" x-show="coaRows.length > 1" @click="removeCoaRow(idx)"
                                            class="px-2 py-0.5 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-[10px] font-bold transition cursor-pointer">
                                        Hapus Baris
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- Pilih Kode Perkiraan (Lebar Penuh) -->
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Akun Perkiraan (COA)</label>
                                        <select :name="'coa_items[' + idx + '][kode]'" x-model="row.kode" @change="onRowCoaChange(idx, $event)" required
                                                class="w-full text-xs font-mono font-semibold rounded-xl border border-blue-300 px-3 py-2.5 bg-white text-slate-800 focus:ring-2 focus:ring-blue-500 shadow-2xs">
                                            <option value="">-- Pilih Kode Perkiraan (COA) Sesuai Rincian Biaya --</option>
                                            <template x-for="item in currentCoaList" :key="item.kode">
                                                <option :value="item.kode" x-text="item.kode + ' - ' + item.nama"></option>
                                            </template>
                                        </select>
                                        <input type="hidden" :name="'coa_items[' + idx + '][nama]'" :value="row.nama">
                                    </div>

                                    <!-- Nominal Per Baris (Muncul saat lebih dari 1 perkiraan) -->
                                    <div x-show="coaRows.length > 1">
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Pos Ini (Rp) *</label>
                                        <input type="number" :name="'coa_items[' + idx + '][nominal]'" x-model="row.nominal" @input="recalcTotal()" min="0" step="1000" placeholder="Contoh: 500000"
                                               class="w-full text-xs font-mono font-bold rounded-xl border border-blue-300 px-3 py-2.5 bg-white text-blue-950 focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <!-- Keterangan Spesifik Per Baris -->
                                    <div x-show="coaRows.length > 1">
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Pos (Opsional)</label>
                                        <input type="text" :name="'coa_items[' + idx + '][rincian]'" x-model="row.rincian" placeholder="Rincian penggunaan pos ini..."
                                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 bg-white text-slate-800 focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>

                                <!-- Info Sisa Plafon per Baris COA -->
                                <template x-if="getCoaDetailByKode(row.kode)">
                                    <div class="pt-2 border-t border-blue-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] text-blue-950">
                                        <div>
                                            <span class="font-bold text-blue-700">Akun:</span>
                                            <span class="font-mono font-bold" x-text="row.kode"></span> &mdash; <span class="font-semibold" x-text="row.nama"></span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <template x-if="getCoaDetailByKode(row.kode).alokasi > 0">
                                                <div class="flex items-center gap-3">
                                                    <span>Plafon Pos: <strong class="font-mono" x-text="formatRupiah(getCoaDetailByKode(row.kode).alokasi)"></strong></span>
                                                    <span class="text-emerald-700">Sisa Pos: <strong class="font-mono font-black" x-text="formatRupiah(getCoaDetailByKode(row.kode).sisa)"></strong></span>
                                                </div>
                                            </template>
                                            <template x-if="getCoaDetailByKode(row.kode).alokasi <= 0">
                                                <span class="text-slate-500 italic">Mengikuti plafon umum bidang</span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <span class="text-[10px] text-slate-500 block">
                            Anda dapat memilih 1 Kode Perkiraan saja, atau klik <strong>"+ Tambah Kode Perkiraan (COA)"</strong> jika dalam 1 pengajuan bidang terdapat beberapa pos biaya sekaligus.
                        </span>
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

                <!-- Status Pengesahan Dokumen BP -->
                @if(Auth::user()?->hasRole('keuangan', 'admin_keuangan', 'administrator'))
                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div>
                            <span class="font-bold text-slate-800 block">Status Pengesahan Dokumen BP (Otorisasi Keuangan)</span>
                            <span class="text-slate-500 text-[11px]">Manajer Keuangan dapat menyimpan sebagai Pending atau langsung menyetujui (ACC) untuk menerbitkan Register PR.</span>
                        </div>
                        <select name="status_persetujuan" class="text-xs font-bold rounded-lg border border-blue-300 px-3 py-2 bg-white text-blue-950 focus:ring-2 focus:ring-blue-500 shadow-2xs">
                            <option value="pending" selected>Simpan Sebagai Pending (ACC di Tabel)</option>
                            <option value="approved">Langsung Disetujui / Terbitkan PR (Siap Cair)</option>
                        </select>
                    </div>
                @else
                    <input type="hidden" name="status_persetujuan" value="pending">
                    <div class="p-3.5 bg-amber-50/70 border border-amber-200 rounded-xl flex items-center justify-between gap-3 text-xs">
                        <div>
                            <span class="font-bold text-amber-950 block">Alur Otorisasi: Menunggu Persetujuan (ACC) Manajer Keuangan</span>
                            <span class="text-slate-600 text-[11px]">Pengajuan BP ini akan masuk ke daftar antrean <strong>Manajer Keuangan</strong> untuk di-ACC (terbit Register PR). Setelah disetujui, bagian Anggaran dapat langsung <strong>Mencairkan ke Kasir &amp; Menerbitkan BKK</strong>.</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300 shrink-0">
                            Pending ACC Keuangan
                        </span>
                    </div>
                @endif

                <!-- Notice Alur Integrasi Kas Priangan -->
                <div class="p-4 rounded-xl bg-blue-50/50 border border-blue-200 text-xs text-blue-950 leading-relaxed space-y-1">
                    <div class="font-bold text-blue-900">Alur Integrasi Pengeluaran Kas (Anggaran &rarr; Keuangan &rarr; Kasir):</div>
                    <p class="text-slate-600">
                        1. <strong>Bagian Anggaran</strong> menginput permohonan Bukti Pengeluaran (BP Priangan).<br>
                        2. <strong>Manajer Keuangan</strong> memverifikasi dan menyetujui (ACC) sehingga terbit <strong>Nomor Register Anggaran (PR-YYYYMMDD-XXXX)</strong>.<br>
                        3. Setelah di-ACC Keuangan, <strong>Bagian Anggaran</strong> memproses <strong>Cairkan ke Kasir &amp; Terbitkan BKK (KO-YYYYMMXXXX)</strong> untuk pencairan dana.
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
