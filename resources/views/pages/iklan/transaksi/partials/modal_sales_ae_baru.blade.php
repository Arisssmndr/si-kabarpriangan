<!-- ============================================================== -->
<!-- MODAL TAMBAH SALES / AE (SELARAS DENGAN MASTER PRODUK)        -->
<!-- ============================================================== -->
<div id="modalSalesAeBaru" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full shadow-xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[92vh] overflow-hidden">
        
        <!-- Modal Header (Bersih, Ramping, Tanpa Logo/Deskripsi - Standar Master Iklan) -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
            <h3 class="text-base font-bold text-slate-900">Tambah Sales / AE</h3>
            <button type="button" onclick="closeModalSalesAeBaru()" 
                    class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer" 
                    title="Tutup">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Form Body (Scrollable bila tinggi layar terbatas) -->
        <div class="p-6 space-y-3.5 text-xs overflow-y-auto flex-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                        Kategori Sales <span class="text-rose-500">*</span>
                    </label>
                    <select id="modal_sales_jenis" onchange="autoGenerateKodeSalesModal()"
                            class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="STRUKTURAL">Account Executive (Kode S)</option>
                        <option value="KORESPONDEN">Koresponden (Kode K)</option>
                        <option value="FO">Loket Kantor (FO)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                        Kode Sales <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="text" id="modal_sales_kode" required maxlength="20"
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl font-mono font-bold text-sm uppercase text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition"
                               placeholder="S01 / K01" />
                        <button type="button" onclick="autoGenerateKodeSalesModal()" title="Buat Kode Otomatis"
                                class="h-11 px-3.5 bg-kp-blue-50 hover:bg-kp-blue-100 text-kp-blue-700 border border-kp-blue-200 rounded-xl transition cursor-pointer flex items-center justify-center shadow-2xs shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                    Nama Lengkap Sales / AE <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="modal_sales_nama" placeholder="Contoh: Budi Santoso"
                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
            </div>
        </div>

        <!-- Modal Footer (Bersih & Simpel Sesuai Master Produk) -->
        <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0">
            <button type="button" onclick="closeModalSalesAeBaru()" 
                    class="h-11 px-5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold transition cursor-pointer">
                Batal
            </button>
            <button type="button" onclick="simpanSalesAeOnTheFly()" id="btnSimpanSalesAeModal" 
                    class="h-11 px-6 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-xl text-sm font-bold transition cursor-pointer shadow-xs">
                Simpan &amp; Pilih
            </button>
        </div>

    </div>
</div>
