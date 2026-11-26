<x-app-layout>
    <div class="space-y-5">
        <!-- 1. Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Entri Penyesuaian Koran Retur (BAPR)</h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Koreksi Piutang</span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Pencatatan bukti Berita Acara Penerimaan Retur dari Divisi Sirkulasi sebagai pengurang resmi piutang.
                    </p>
                </div>
            </div>

            <a href="{{ route('accounting.kartu.sirkulasi') }}" class="inline-flex items-center gap-1.5 h-10 px-4 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition shrink-0">
                <span>&larr; Kembali ke Kartu Piutang Agen</span>
            </a>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Form Input Retur -->
            <div class="lg:col-span-1 bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider pb-3 border-b border-slate-100">
                    Form Input BAPR Retur
                </h3>
                <form method="POST" action="{{ route('accounting.kartu.retur.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor BAPR Resmi</label>
                        <input type="text" name="no_bapr" placeholder="Contoh: BAPR/KP/2026/10/0145" required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs rounded-lg focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Verifikasi</label>
                        <input type="date" name="tanggal" value="{{ $today }}" required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs rounded-lg focus:border-kp-blue-600 focus:ring-kp-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Agen Koran</label>
                        <select name="agen" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs rounded-lg focus:border-kp-blue-600 focus:ring-kp-blue-600">
                            @foreach($daftarAgen as $ag)
                                <option value="{{ $ag->kode }}">{{ $ag->kode }} - {{ $ag->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Fisik (Eks)</label>
                            <input type="number" name="eksemplar" placeholder="Contoh: 15" min="1" required
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs rounded-lg focus:border-kp-blue-600 focus:ring-kp-blue-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tarif Agen (Rp)</label>
                            <input type="number" name="tarif" value="2800" required
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs rounded-lg focus:border-kp-blue-600 focus:ring-kp-blue-600">
                        </div>
                    </div>

                    <div class="p-3 bg-kp-blue-50 rounded-xl border border-kp-blue-200 text-xs text-slate-900">
                        <strong>Dampak Pembukuan:</strong><br>
                        (D) Retur Penjualan Sirkulasi (4-1210)<br>
                        (K) Piutang Usaha Sirkulasi (1-1220)
                    </div>

                    <button type="submit" class="w-full h-10 text-xs font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white shadow-xs transition cursor-pointer">
                        Simpan Penyesuaian Retur
                    </button>
                </form>
            </div>

            <!-- Pure Tabel Riwayat Retur Terverifikasi -->
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[350px]">
                    <table class="w-full text-sm text-left border-collapse min-w-[750px]">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                            <tr>
                                <th class="px-4 py-3 whitespace-nowrap w-36">No. BAPR</th>
                                <th class="px-3.5 py-3 whitespace-nowrap w-28">Tanggal</th>
                                <th class="px-4 py-3 min-w-[180px]">Nama Agen</th>
                                <th class="px-3.5 py-3 text-center whitespace-nowrap w-28">Fisik (Eks)</th>
                                <th class="px-4 py-3 text-right whitespace-nowrap w-36">Potongan Piutang</th>
                                <th class="px-4 py-3 text-center whitespace-nowrap w-28">Status GL</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                            @foreach($riwayatRetur as $r)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap text-xs">
                                    {{ $r->no_bapr }}
                                </td>
                                <td class="px-3.5 py-3 font-mono text-slate-600 whitespace-nowrap text-xs">
                                    {{ $r->tanggal }}
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    {{ $r->agen }}
                                </td>
                                <td class="px-3.5 py-3 text-center font-bold text-slate-900 font-mono">
                                    {{ $r->eksemplar }} eks
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($r->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        {{ $r->status_jurnal }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>