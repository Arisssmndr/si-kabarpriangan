<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Koreksi Piutang Iklan
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                            Credit Note (CN)
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Penerbitan nota kredit untuk pembatalan atau potongan tagihan iklan.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <div class="text-xs font-medium text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-200">
                    Format Bukti: <strong class="text-slate-900 font-mono">{{ $nextNoBukti }}</strong>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Form Input Koreksi (CN-IKL) -->
            <div class="lg:col-span-4 bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                <h2 class="text-sm font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">
                    Form Penerbitan CN-IKL
                </h2>

                <form method="POST" action="{{ route('accounting.iklan.simpan-koreksi') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. Bukti CN</label>
                        <input type="text" 
                               name="nomor_bukti" 
                               value="{{ $nextNoBukti }}" 
                               readonly
                               class="w-full h-10 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-700 cursor-not-allowed" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih No. Faktur</label>
                        <select name="no_faktur" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <option value="">-- Pilih Faktur Iklan --</option>
                            @foreach($daftarFaktur as $f)
                                <option value="{{ $f->no_faktur }}">
                                    {{ $f->no_faktur }} - {{ $f->pemasang?->nama_pemasang ?? $f->nama_pemohon }} (Rp {{ number_format($f->total_akhir ?? $f->subtotal, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Koreksi</label>
                        <input type="date" 
                               name="tanggal" 
                               value="{{ date('Y-m-d') }}" 
                               required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nilai Koreksi (Rp)</label>
                        <input type="number" 
                               name="nominal_koreksi" 
                               placeholder="Contoh: 150000" 
                               min="1" 
                               required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alasan Koreksi</label>
                        <textarea name="alasan" 
                                  rows="2" 
                                  required 
                                  placeholder="Koreksi salah ukuran, komplain cetak, dll..."
                                  class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full h-10 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                            Terbitkan Nota Kredit
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Riwayat Koreksi CN-IKL -->
            <div class="lg:col-span-8 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Riwayat Nota Kredit (CN-IKL)</h2>
                        <p class="text-xs text-slate-500">Daftar koreksi piutang yang telah diterbitkan.</p>
                    </div>
                </div>

                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 w-12 text-center">No</th>
                                <th class="py-3 px-3.5">No. Bukti CN</th>
                                <th class="py-3 px-3.5">No. Faktur</th>
                                <th class="py-3 px-3.5">Tanggal</th>
                                <th class="py-3 px-3.5 text-right">Nilai Koreksi</th>
                                <th class="py-3 px-3.5">Alasan</th>
                                <th class="py-3 px-3.5 text-center w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @forelse($daftarCn as $key => $cn)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-3.5 text-center text-xs text-slate-400">{{ $key + 1 }}</td>
                                    <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $cn->nomor_bukti }}</td>
                                    <td class="py-3 px-3.5">{{ $cn->no_referensi }}</td>
                                    <td class="py-3 px-3.5">{{ \Carbon\Carbon::parse($cn->tanggal)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($cn->nominal_koreksi, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-3.5 max-w-xs truncate">{{ $cn->alasan }}</td>
                                    <td class="py-3 px-3.5 text-center">
                                        <a href="{{ route('accounting.iklan.cetak-koreksi', $cn->id) }}" 
                                           target="_blank"
                                           class="text-xs font-semibold text-kp-blue-600 hover:text-kp-blue-700 underline">
                                            Cetak
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                        Belum ada penerbitan nota kredit iklan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

