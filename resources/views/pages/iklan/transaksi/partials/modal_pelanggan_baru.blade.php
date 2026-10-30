<!-- ============================================================== -->
<!-- MODAL TAMBAH PELANGGAN BARU (SELARAS DENGAN MASTER PRODUK)     -->
<!-- ============================================================== -->
<div id="modalPelangganBaru" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full shadow-xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[92vh] overflow-hidden">
        
        <!-- Modal Header (Bersih, Ramping, Tanpa Logo/Deskripsi - Standar Master Iklan) -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
            <h3 class="text-base font-bold text-slate-900">Tambah Pelanggan Baru</h3>
            <button type="button" onclick="closeModalPelangganBaru()" 
                    class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer" 
                    title="Tutup">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Form Body (Scrollable bila tinggi layar terbatas) -->
        <div class="p-6 space-y-3.5 text-xs overflow-y-auto flex-1">
            <div>
                <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                    Nama Pemesan / Instansi <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="modal_nama_instansi" placeholder="Contoh: PT. Sumber Barokah / H. Ahmad Sanusi"
                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                        Kategori Pelanggan
                    </label>
                    <select id="modal_jenis_klien" 
                            class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="Perorangan">Perorangan</option>
                        <option value="Swasta">Perusahaan / Swasta</option>
                        <option value="Pemda">Instansi / Dinas Pemda</option>
                        <option value="Agensi">Biro Iklan / Agensi</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                        Nomor WhatsApp / HP
                    </label>
                    <input type="text" id="modal_no_telp" placeholder="Contoh: 081234567890" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                    Alamat Lengkap Kantor / Domisili
                </label>
                <textarea id="modal_alamat" rows="2" placeholder="Alamat lengkap jalan, kelurahan, kecamatan..." 
                          class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition resize-none"></textarea>
            </div>

            <!-- Toggle Informasi Tambahan Lengkap (Opsional) -->
            <div class="pt-0.5">
                <button type="button" onclick="toggleModalPelangganExtra()" 
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-kp-blue-600 hover:text-kp-blue-800 transition cursor-pointer select-none">
                    <svg id="iconToggleExtra" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span id="labelToggleExtra">+ Informasi Lengkap (NPWP/NIK, PIC, Email, Plafon Kredit)</span>
                </button>
            </div>

            <!-- Container Informasi Tambahan Lengkap -->
            <div id="containerModalPelangganExtra" class="space-y-3.5 pt-2 border-t border-slate-100 hidden">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                            NPWP / NIK (16 Digit)
                        </label>
                        <input type="text" id="modal_npwp_nik" placeholder="Nomor NPWP atau NIK..."
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                            Plafon Kredit (Credit Limit)
                        </label>
                        <div class="flex rounded-xl border border-slate-300 focus-within:border-kp-blue-600 focus-within:ring-1 focus-within:ring-kp-blue-600 overflow-hidden bg-white shadow-2xs transition h-11">
                            <span class="inline-flex items-center px-3 bg-slate-50 border-r border-slate-200 text-xs text-slate-500 font-medium select-none pointer-events-none">Rp</span>
                            <input type="text" inputmode="numeric" id="modal_credit_limit" placeholder="0"
                                   onkeyup="formatRupiahInput(this)"
                                   class="flex-1 h-11 px-3.5 bg-transparent text-sm font-mono font-bold text-slate-900 border-none focus:outline-none focus:ring-0 format-rupiah" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                            Nama Kontak PIC
                        </label>
                        <input type="text" id="modal_kontak_pic" placeholder="Nama penanggung jawab / PIC..."
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                            Alamat Email
                        </label>
                        <input type="email" id="modal_email" placeholder="alamat@email.com"
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer (Bersih & Simpel Sesuai Master Produk) -->
        <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0">
            <button type="button" onclick="closeModalPelangganBaru()" 
                    class="h-11 px-5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold transition cursor-pointer">
                Batal
            </button>
            <button type="button" onclick="simpanPelangganOnTheFly()" id="btnSimpanPelangganModal" 
                    class="h-11 px-6 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-xl text-sm font-bold transition cursor-pointer shadow-xs">
                Simpan &amp; Pilih
            </button>
        </div>

    </div>
</div>
