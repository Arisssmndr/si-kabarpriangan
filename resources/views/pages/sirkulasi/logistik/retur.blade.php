<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Berita Acara Penerimaan Retur (BAPR)</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-rose-50 text-rose-700 border border-rose-200">Pengurang Piutang</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pencatatan fisik koran sisa/retur dari agen yang menjadi dasar sah pengurangan piutang di Divisi Kasir & Akuntansi.
                    </p>
                </div>
            </div>

            <button type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>+ Input BAPR Retur Baru</span>
            </button>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Summary KPI Retur -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500">Total Fisik Koran Retur Hari Ini</span>
                    <div class="text-xl font-extrabold text-rose-600 mt-1">{{ number_format($totalReturEks, 0, ',', '.') }} Eksemplar</div>
                </div>
                <span class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center font-bold text-xs">
                    Eks
                </span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500">Total Nilai Pengurang Piutang Resmi</span>
                    <div class="text-xl font-extrabold text-slate-900 mt-1">Rp {{ number_format($totalNilaiRetur, 0, ',', '.') }}</div>
                </div>
                <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-bold text-xs">
                    Rp
                </span>
            </div>
        </div>

        <!-- Tabel BAPR -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-semibold">No. BAPR & Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Nama Agen & Jalur</th>
                            <th class="px-4 py-3 font-semibold text-right">Kirim (Eks)</th>
                            <th class="px-4 py-3 font-semibold text-right">Retur Fisik</th>
                            <th class="px-4 py-3 font-semibold text-right">Nilai Pengurang</th>
                            <th class="px-4 py-3 font-semibold text-center">Verifikasi Fisik</th>
                            <th class="px-5 py-3 font-semibold text-center">Status Akuntansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($baprList as $b)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3">
                                <div class="font-bold text-slate-900 font-mono">{{ $b->no_bapr }}</div>
                                <div class="text-[11px] text-slate-500">Tgl: {{ $b->tanggal }} &bull; Edisi: {{ $b->edisi_retur }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $b->nama_agen }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $b->kode_agen }} &bull; {{ $b->rute }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-700">
                                {{ number_format($b->jumlah_kirim, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="font-extrabold text-rose-600">{{ $b->retur_eks }} eks</div>
                                <div class="text-[10px] text-slate-400">({{ $b->persentase }}%)</div>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-slate-900">
                                Rp {{ number_format($b->nilai_rupiah, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(str_contains($b->status_verifikasi, 'Terverifikasi'))
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $b->status_verifikasi }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                        {{ $b->status_verifikasi }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $b->status_akuntansi }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
