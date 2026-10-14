<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Entri Penyesuaian Koran Retur (BAPR)</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-rose-50 text-rose-700 border border-rose-200">Koreksi Piutang</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pencatatan bukti Berita Acara Penerimaan Retur dari Divisi Sirkulasi sebagai pengurang resmi piutang agen agar balance.
                    </p>
                </div>
            </div>

            <a href="{{ route('accounting.kartu.sirkulasi') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                <span>&larr; Kembali ke Kartu Piutang Agen</span>
            </a>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-purple-500 focus:ring-purple-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Verifikasi</label>
                        <input type="date" name="tanggal" value="{{ $today }}" required
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-purple-500 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Agen Koran</label>
                        <select name="agen" required class="w-full text-xs rounded-lg border-slate-200 focus:border-purple-500 focus:ring-purple-500">
                            @foreach($daftarAgen as $ag)
                                <option value="{{ $ag->kode }}">{{ $ag->kode }} - {{ $ag->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Fisik (Eks)</label>
                            <input type="number" name="eksemplar" placeholder="Contoh: 15" min="1" required
                                   class="w-full text-xs rounded-lg border-slate-200 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tarif Agen (Rp)</label>
                            <input type="number" name="tarif" value="2800" required
                                   class="w-full text-xs rounded-lg border-slate-200 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                    </div>

                    <div class="p-3 bg-purple-50 rounded-lg border border-purple-200 text-xs text-purple-900">
                        <strong>Dampak Pembukuan:</strong><br>
                        (D) Retur Penjualan Sirkulasi (4-1210)<br>
                        (K) Piutang Usaha Sirkulasi (1-1220)
                    </div>

                    <button type="submit" class="w-full py-2 px-3 text-xs font-bold rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition">
                        Simpan Penyesuaian Retur
                    </button>
                </form>
            </div>

            <!-- Riwayat Retur Terverifikasi -->
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Riwayat Penyesuaian BAPR yang Telah Masuk Jurnal
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 font-semibold">No. BAPR & Tanggal</th>
                                <th class="px-4 py-3 font-semibold">Nama Agen</th>
                                <th class="px-3 py-3 font-semibold text-center">Fisik (Eks)</th>
                                <th class="px-4 py-3 font-semibold text-right">Potongan Piutang</th>
                                <th class="px-4 py-3 font-semibold text-center">Status GL</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($riwayatRetur as $r)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900 font-mono">{{ $r->no_bapr }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $r->tanggal }}</div>
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    {{ $r->agen }}
                                </td>
                                <td class="px-3 py-3 text-center font-bold text-rose-600">
                                    {{ $r->eksemplar }} eks
                                </td>
                                <td class="px-4 py-3 text-right font-extrabold text-slate-900">
                                    Rp {{ number_format($r->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
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
