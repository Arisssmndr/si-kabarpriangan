{{--
    Panel "Inputan Shift Ini (Bisa Diedit)"
    Dipakai di halaman input kasir agar kasir bisa langsung mengoreksi inputan yang salah.

    Parameter:
      $jenis   : 'KI' (kas masuk) atau 'KO' (kas keluar)
      $filter  : (opsional) ['tipe_transaksi' => 'iklan'] atau ['kode_bagian' => '09']
      $judul   : (opsional) judul panel
--}}
@php
    $jenis  = $jenis ?? 'KI';
    $filter = $filter ?? [];
    $judul  = $judul ?? ($jenis === 'KI' ? 'Inputan Kas Masuk Shift Ini' : 'Inputan Kas Keluar Shift Ini');

    $notVoid  = function ($q) {
        $q->where('status_void', false)->orWhereNull('status_void');
    };
    $notClosed = function ($q) {
        $q->where('is_closed', false)->orWhereNull('is_closed');
    };

    if ($jenis === 'KI') {
        $query = \App\Models\KasMasuk::where($notVoid)->where($notClosed);
        foreach ($filter as $kolom => $nilai) {
            $query->where($kolom, $nilai);
        }
    } else {
        $query = \App\Models\KasKeluar::where($notVoid)->where($notClosed)
            ->where(function ($q) {
                $q->where('is_tax_offset', false)->orWhereNull('is_tax_offset');
            });
        foreach ($filter as $kolom => $nilai) {
            $query->where($kolom, $nilai);
        }
    }

    $barisEdit = $query->latest('id')->take(8)->get();
@endphp

<div class="print:hidden bg-white border border-amber-200 rounded-xl shadow-xs overflow-hidden">
    <div class="px-5 py-3.5 border-b border-amber-100 bg-amber-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </span>
            <div>
                <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider">{{ $judul }} (Bisa Diedit)</h3>
                <p class="text-[11px] text-amber-800/80">Salah input? Klik <b>Edit</b> untuk memperbaiki sebelum kas ditutup.</p>
            </div>
        </div>
        <a href="{{ route('kasir.utilitas.koreksi') }}" class="text-[11px] font-bold text-amber-800 hover:text-amber-900 underline underline-offset-2">
            Lihat semua di Edit &amp; Koreksi
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-4 py-2.5 font-semibold">No. Dokumen</th>
                    <th class="px-4 py-2.5 font-semibold">{{ $jenis === 'KI' ? 'Terima Dari' : 'Diberikan Kepada' }}</th>
                    <th class="px-4 py-2.5 font-semibold text-right">Nominal (Rp)</th>
                    <th class="px-4 py-2.5 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($barisEdit as $b)
                    @php
                        $noDok = $jenis === 'KI' ? ($b->no_kuitansi ?? $b->no_bkm) : $b->no_bkk;
                        $pihak = $jenis === 'KI' ? ($b->terima_dari ?? $b->nama_pembayar) : ($b->diberikan_kepada ?? $b->nama_penerima);
                        $nominal = $jenis === 'KI' ? ($b->jumlah_dibayar ?? $b->jumlah_diterima) : ($b->jumlah_keluar ?? 0);
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-4 py-2.5 font-mono font-bold text-slate-900">{{ $noDok }}</td>
                        <td class="px-4 py-2.5 font-semibold text-slate-800">{{ $pihak }}</td>
                        <td class="px-4 py-2.5 text-right font-mono font-bold {{ $jenis === 'KI' ? 'text-teal-700' : 'text-rose-700' }}">
                            Rp {{ number_format((float) $nominal, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2.5 text-center whitespace-nowrap">
                            <a href="{{ route('kasir.utilitas.koreksi', ['edit' => $b->id, 'tipe' => $jenis]) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold text-[10px] rounded-lg transition shadow-2xs">
                                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Edit</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-5 text-center text-slate-400">Belum ada inputan yang bisa diedit pada shift ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

