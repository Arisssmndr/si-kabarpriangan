<x-app-layout>
    <div class="space-y-6" x-data="{ showModal: false, formBank: 'Bank BJB Cabang Tasikmalaya (Giro: 05.00.01.006270.9)', nominal: {{ $kasFisikTersedia }}, slip: '' }">

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-700 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- 1. Header Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Logo Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                            Setor Kas ke Bank (Banking In)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-sky-50 text-[#0E527B] border border-sky-200">
                            Bank In
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Pencatatan pengiriman uang fisik brankas kasir ke rekening giro operasional PT Berkah Pikiran Rakyat (Kabar Priangan).
                    </p>
                </div>
            </div>

            <button type="button" @click="showModal = true" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Catat Setoran Bank Baru</span>
            </button>
        </div>

        <!-- 2. KPI Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kas Fisik Siap Disetor</span>
                <p class="mt-2 text-2xl font-bold font-mono text-kp-blue-700">Rp {{ number_format($kasFisikTersedia, 0, ',', '.') }}</p>
                <span class="mt-1 text-[11px] text-slate-400 block">Total brankas setelah realisasi BP</span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sudah Disetor Hari Ini</span>
                <p class="mt-2 text-2xl font-bold font-mono text-slate-900">Rp {{ number_format($totalSudahSetorBank, 0, ',', '.') }}</p>
                <span class="mt-1 text-[11px] text-slate-400 block">Total masuk rekening giro</span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Rekening Giro Resmi</span>
                <p class="mt-2 text-sm font-bold font-mono text-slate-800">BJB: 05.00.01.006270.9 • BCA: 054-0388-991</p>
                <span class="mt-1 text-[11px] text-slate-400 block">Cabang Tasikmalaya (PT Berkah Pikiran Rakyat)</span>
            </div>
        </div>

        <!-- 3. Pure Data Table: Riwayat Setoran Bank (Tanpa Banner Strip) -->
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-emerald-700 border-b border-emerald-800 text-white font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-4">No. Referensi / Tanggal</th>
                            <th class="py-3 px-4">Bank Tujuan & Rekening</th>
                            <th class="py-3 px-4">Petugas Penyetor</th>
                            <th class="py-3 px-4">No. Slip Teller</th>
                            <th class="py-3 px-4 text-right">Nominal Setoran (Rp)</th>
                            <th class="py-3 px-4 text-center">Status Validasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($riwayatSetorBank as $item)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-mono font-bold text-slate-900">{{ $item->no_referensi }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d/m/Y') }} • {{ $item->waktu }} WIB</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-800">{{ $item->bank_tujuan }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $item->keterangan }}</div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                                        {{ $item->penyetor }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="font-mono text-xs font-semibold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                                        {{ $item->no_slip }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-[#0E527B] border border-sky-200">
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                    Belum ada catatan setoran bank untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Setor Kas ke Bank -->
        <div x-cloak x-show="showModal" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#0E527B] border border-sky-200 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Form Setoran Kas ke Bank (Banking In)</h3>
                    </div>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold leading-none">&times;</button>
                </div>

                <form action="{{ route('kasir.setoran-bank.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Rekening Bank Tujuan</label>
                            <select name="bank_tujuan" x-model="formBank" class="h-10 w-full px-3 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600">
                                <option value="Bank BJB Cabang Tasikmalaya (Giro: 05.00.01.006270.9)">Bank BJB Cabang Tasikmalaya (Giro: 05.00.01.006270.9)</option>
                                <option value="Bank BCA Cabang Tasikmalaya (Giro: 054-0388-991)">Bank BCA Cabang Tasikmalaya (Giro: 054-0388-991)</option>
                            </select>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-slate-700">Nominal Disetor (Rp)</label>
                                <span class="text-[11px] text-slate-400">Maksimal fisik: Rp {{ number_format($kasFisikTersedia, 0, ',', '.') }}</span>
                            </div>
                            <input type="number" name="nominal_setor" x-model="nominal" min="1" max="{{ $kasFisikTersedia > 0 ? $kasFisikTersedia : 100000000 }}" required 
                                   class="h-10 w-full px-3.5 bg-white border border-slate-300 rounded-xl font-mono font-bold text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Slip Teller / Validasi Mesin Bank</label>
                            <input type="text" name="no_slip" x-model="slip" placeholder="Contoh: SLIP-BJB-998124 atau No. Antrean Teller" required 
                                   class="h-10 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Berita Penyetoran</label>
                            <textarea name="keterangan" rows="2" placeholder="Catatan pelengkap, misal: Setor tunai hasil penerimaan iklan sirkulasi shift 1" class="w-full p-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600"></textarea>
                        </div>

                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 flex items-start gap-2.5 text-xs text-slate-600">
                            <svg class="w-4 h-4 text-kp-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Pastikan slip setoran fisik diarsipkan sebagai lampiran penutupan Buku Kas Kasir pada akhir shift.</span>
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-xs font-semibold text-slate-700 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-kp-blue-600 hover:bg-kp-blue-700 rounded-xl shadow-xs transition cursor-pointer">
                            Simpan & Validasi Setoran
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>