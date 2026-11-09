<x-app-layout>
    <div class="space-y-5" x-data="{ showModal: false, formBank: 'Bank BJB Cabang Tasikmalaya (Giro: 0012-0034-5678)', nominal: {{ $kasFisikTersedia }}, slip: '' }">

        <!-- Header Halaman (60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Setor Kas ke Bank (Banking In)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Pencatatan penyetoran uang fisik brankas kasir ke rekening giro operasional perusahaan.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button" @click="showModal = true" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Catat Setoran Bank</span>
                </button>
            </div>
        </div>

        <!-- Flash Message -->
        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
                <button type="button" @click="$el.parentElement.remove()" class="text-kp-blue-600 hover:text-kp-blue-800 font-bold px-1 cursor-pointer">&times;</button>
            </div>
        @endif

        <!-- 3 Kartu KPI -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kas Fisik Siap Disetor</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($kasFisikTersedia, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">
                    Total brankas setelah realisasi BP
                </span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sudah Disetor Hari Ini</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalSudahSetorBank, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">
                    Total masuk rekening giro hari ini
                </span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Rekening Tujuan Utama</span>
                <p class="text-lg font-bold text-slate-900 mt-1.5 truncate">
                    Bank BJB Cabang Tasikmalaya
                </p>
                <span class="text-xs text-slate-400 mt-1 block font-mono">
                    Giro: 0012-0034-5678
                </span>
            </div>
        </div>

        <!-- Tabel Riwayat Setoran Bank -->
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Riwayat Setoran Fisik ke Bank</h2>
                    <p class="text-xs text-slate-500">Daftar slip penyetoran kas tunai ke rekening bank resmi</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ count($riwayatSetorBank) }} Setoran
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Referensi</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Bank Tujuan & Keterangan</th>
                            <th class="py-3 px-3.5">Petugas Penyetor</th>
                            <th class="py-3 px-3.5">No. Slip Teller</th>
                            <th class="py-3 px-3.5 text-right">Nominal Setoran</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($riwayatSetorBank as $idx => $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm whitespace-nowrap">
                                    {{ $item->no_referensi }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                    <span class="text-xs text-slate-400">{{ $item->waktu }}</span>
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <div class="font-semibold text-slate-900">{{ $item->bank_tujuan }}</div>
                                    <div class="text-xs text-slate-500">{{ $item->keterangan }}</div>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-700 whitespace-nowrap">
                                    {{ $item->penyetor }}
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <span class="font-mono font-semibold text-kp-blue-700 bg-kp-blue-50 px-2 py-0.5 rounded-md border border-kp-blue-200 text-xs">
                                        {{ $item->no_slip }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums whitespace-nowrap">
                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-500">
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
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="showModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Form Setoran Kas ke Bank</h3>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold leading-none cursor-pointer">&times;</button>
                </div>

                <form action="{{ route('kasir.setoran-bank.store') }}" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Rekening Bank Tujuan</label>
                        <select name="bank_tujuan" x-model="formBank" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600">
                            <option value="Bank BJB Cabang Tasikmalaya (Giro: 0012-0034-5678)">Bank BJB Cabang Tasikmalaya (Giro: 0012-0034-5678)</option>
                            <option value="Bank Mandiri Tasikmalaya (Giro: 131-00-9876543-2)">Bank Mandiri Tasikmalaya (Giro: 131-00-9876543-2)</option>
                            <option value="Bank BCA Tasikmalaya (Giro: 054-9988776)">Bank BCA Tasikmalaya (Giro: 054-9988776)</option>
                        </select>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-xs font-semibold text-slate-700">Nominal Disetor (Rp)</label>
                            <span class="text-xs text-slate-500 font-mono">Maks: Rp {{ number_format($kasFisikTersedia, 0, ',', '.') }}</span>
                        </div>
                        <input type="number" name="nominal_setor" x-model="nominal" min="1" max="{{ $kasFisikTersedia > 0 ? $kasFisikTersedia : 100000000 }}" required 
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-kp-blue-700 focus:outline-none focus:border-kp-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Slip Teller</label>
                        <input type="text" name="no_slip" x-model="slip" placeholder="Contoh: SLIP-BJB-998124" required 
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Penyetoran</label>
                        <textarea name="keterangan" rows="2" placeholder="Catatan berita setoran..." 
                                  class="w-full p-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-kp-blue-600 hover:bg-kp-blue-700 rounded-xl shadow-xs transition cursor-pointer">
                            Simpan Setoran
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
