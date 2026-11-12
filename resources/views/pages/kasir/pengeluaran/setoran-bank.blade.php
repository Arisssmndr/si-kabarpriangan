<x-app-layout>
    <div class="space-y-6" x-data="{ showModal: false, formBank: 'Bank BJB Cabang Tasikmalaya (Giro: 0012-0034-5678)', nominal: {{ $kasFisikTersedia }}, slip: '' }">

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-semibold text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Header Page Setor Kas ke Bank -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Setor Kas ke Bank (Banking In)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-blue-50 text-blue-700 border border-blue-200">
                            Badge: Bank
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pencatatan pengiriman uang fisik brankas kasir ke rekening giro operasional PT Berkah Pikiran Rakyat (Kabar Priangan)
                    </p>
                </div>
            </div>

            <button type="button" @click="showModal = true" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Catat Setoran Bank Baru</span>
            </button>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm shrink-0">
                    Rp
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-500 uppercase">Kas Fisik Siap Disetor</div>
                    <div class="text-lg font-bold text-emerald-600">Rp {{ number_format($kasFisikTersedia, 0, ',', '.') }}</div>
                    <div class="text-[10px] text-slate-400">Total brankas setelah realisasi BP</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-500 uppercase">Sudah Disetor Hari Ini</div>
                    <div class="text-lg font-bold text-blue-700">Rp {{ number_format($totalSudahSetorBank, 0, ',', '.') }}</div>
                    <div class="text-[10px] text-slate-400">Total masuk rekening giro</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-500 uppercase">Rekening Tujuan Utama</div>
                    <div class="text-sm font-bold text-slate-800">Bank BJB Cabang Tasikmalaya</div>
                    <div class="text-[10px] text-slate-400">Giro: 0012-0034-5678 (a.n. PT Pikiran Rakyat)</div>
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Setoran Bank -->
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Riwayat Setoran Fisik ke Bank (Slip Banking-In)</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Daftar setoran kas tunai dari kasir yang telah disetor ke rekening bank resmi</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold text-[10px]">
                        <tr>
                            <th class="px-4 py-3">No. Referensi / Tanggal</th>
                            <th class="px-4 py-3">Bank Tujuan & Rekening</th>
                            <th class="px-4 py-3">Petugas Penyetor</th>
                            <th class="px-4 py-3">No. Slip Teller</th>
                            <th class="px-4 py-3">Nominal Setoran</th>
                            <th class="px-4 py-3 text-center">Status Validasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($riwayatSetorBank as $item)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-800">{{ $item->no_referensi }}</div>
                                    <div class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }} • {{ $item->waktu }} WIB</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-800">{{ $item->bank_tujuan }}</div>
                                    <div class="text-[10px] text-slate-500 mt-0.5 italic">{{ $item->keterangan }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                                        {{ $item->penyetor }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-mono font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                                        {{ $item->no_slip }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-emerald-700 text-xs">
                                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                    Belum ada catatan setoran bank untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Setor Kas ke Bank -->
        <!-- Modal Form Setor Kas ke Bank -->
        <div x-cloak x-show="showModal" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Form Setoran Kas ke Bank (Banking In)</h3>
                    </div>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold leading-none">&times;</button>
                </div>

                <form action="{{ route('kasir.setoran-bank.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <div class="flex-1 overflow-y-auto px-5 py-3.5 space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Rekening Bank Tujuan</label>
                            <select name="bank_tujuan" x-model="formBank" class="w-full text-xs rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 py-2">
                                <option value="Bank BJB Cabang Tasikmalaya (Giro: 0012-0034-5678)">Bank BJB Cabang Tasikmalaya (Giro: 0012-0034-5678)</option>
                                <option value="Bank Mandiri Tasikmalaya (Giro: 131-00-9876543-2)">Bank Mandiri Tasikmalaya (Giro: 131-00-9876543-2)</option>
                                <option value="Bank BCA Tasikmalaya (Giro: 054-9988776)">Bank BCA Tasikmalaya (Giro: 054-9988776)</option>
                            </select>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-slate-700">Nominal Disetor (Rp)</label>
                                <span class="text-[10px] text-slate-500">Maksimal fisik: Rp {{ number_format($kasFisikTersedia, 0, ',', '.') }}</span>
                            </div>
                            <input type="number" name="nominal_setor" x-model="nominal" min="1" max="{{ $kasFisikTersedia > 0 ? $kasFisikTersedia : 100000000 }}" required 
                                   class="w-full text-xs rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 py-2 font-mono font-bold text-emerald-700">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Slip Teller / Validasi Mesin Bank</label>
                            <input type="text" name="no_slip" x-model="slip" placeholder="Contoh: SLIP-BJB-998124 atau No. Antrean Teller" required 
                                   class="w-full text-xs rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 py-2">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Berita Penyetoran</label>
                            <textarea name="keterangan" rows="2" placeholder="Catatan pelengkap, misal: Setor tunai hasil penerimaan iklan sirkulasi shift 1" class="w-full text-xs rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 flex items-start gap-2.5 text-[11px] text-blue-800">
                            <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Pastikan slip setoran teller fisik difoto atau diarsipkan sebagai lampiran penutupan Buku Kas Kasir pada akhir shift.</span>
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-xs font-semibold text-slate-700 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-100 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs transition-colors">
                            Simpan & Validasi Setoran
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
