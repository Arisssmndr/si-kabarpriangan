<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-purple-600 uppercase tracking-wider">Tabel Master</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Jenis Agen (00, 11, 99)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Struktur tingkatan mitra distribusi DOS SIRK.EXE dan aturan proteksi integritas kartu piutang akuntansi
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 text-xs font-semibold">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>Mode Pemantauan</span>
                </span>
                <a href="{{ route('sirkulasi.tabel.agen') }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 transition-colors inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>Pantau Data Agen</span>
                </a>
            </div>
        </div>

        <!-- Alert Proteksi Akuntansi & Batasan Hak Akses Sirkulasi -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <div class="text-xs text-amber-800 leading-relaxed">
                    <strong class="font-bold">Ketentuan Mutlak Standar Industri (SOP SIRK.EXE):</strong> Agen dengan kode <span class="font-mono font-bold bg-amber-100 px-1 py-0.2 rounded text-amber-900">00 (Agen Non Aktif)</span> <strong>DILARANG DI-HARD DELETE</strong> dari database. Perubahan status hanya berupa soft-delete agar data historis penerimaan setoran kasir, retur koran, dan kartu piutang di Divisi Accounting tidak hilang.
                </div>
            </div>

             <div class="p-4 bg-sky-50 border border-sky-200 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-sky-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <div class="text-xs text-sky-800 leading-relaxed">
                    <strong class="font-bold">Hak Akses Sirkulasi (Monitoring Only):</strong> Divisi Sirkulasi hanya memiliki hak akses untuk <strong>melihat dan memantau</strong> klasifikasi agen serta jatah distribusi koran. Divisi Sirkulasi <strong>tidak dapat mengelola data agen</strong> (seperti menambah atau mengedit data). Pengelolaan master data agen dilakukan terpusat oleh Administrator.
                </div>
            </div>
        </div>

        <!-- Tabel 3 Jenis Agen -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Daftar Klasifikasi Jenis & Ketentuan Agen</h3>
                <span class="text-[11px] text-slate-500 font-medium">3 Level Kemitraan Sirkulasi</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 w-16 text-center whitespace-nowrap">Kode</th>
                            <th class="py-3 px-4">Klasifikasi Jenis Agen</th>
                            <th class="py-3 px-4 text-center whitespace-nowrap">Harga Beli / Eksemplar</th>
                            <th class="py-3 px-4">Karakteristik & Mekanisme Pengiriman</th>
                            <th class="py-3 px-4 text-center w-48 whitespace-nowrap">Kebijakan Proteksi</th>
                            <th class="py-3 px-4 text-center w-36 whitespace-nowrap">Pemantauan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($jenisList as $j)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 text-xs font-mono font-bold rounded-md 
                                        {{ $j->kode === '00' ? 'bg-rose-100 text-rose-900 border border-rose-200' : '' }}
                                        {{ $j->kode === '11' ? 'bg-blue-100 text-blue-900 border border-blue-200' : '' }}
                                        {{ $j->kode === '99' ? 'bg-emerald-100 text-emerald-900 border border-emerald-200' : '' }}">
                                        {{ $j->kode }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-xs">{{ $j->nama }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $j->sistem_distribusi }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($j->harga_satuan > 0)
                                        <span class="font-bold text-slate-900">Rp {{ number_format($j->harga_satuan, 0, ',', '.') }}</span>
                                        <span class="text-[10px] text-slate-400">/ eks</span>
                                    @else
                                        <span class="text-slate-400 italic">Non-Aktif (Rp 0)</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 leading-relaxed max-w-md">
                                    {{ $j->ketentuan }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @php
                                        $badgeConfig = match($j->kode) {
                                             '00' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-500'],
                                             '11' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'dot' => 'bg-blue-500'],
                                             '99' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
                                             default => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400'],
                                         };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border whitespace-nowrap shadow-2xs {{ $badgeConfig['bg'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $badgeConfig['dot'] }}"></span>
                                        <span>{{ $j->status_aturan }}</span>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <a href="{{ route('sirkulasi.tabel.agen', ['kategori' => $j->kode]) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-lg text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200/80 transition-colors whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Pantau Agen {{ $j->kode }}</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
