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

<div class="print:hidden bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-700 border-collapse">
            <thead class="bg-slate-50 text-slate-700 text-xs font-semibold uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">
                <tr>
                    <th class="px-4 py-3 font-semibold">No. Dokumen ({{ $jenis }})</th>
                    <th class="px-4 py-3 font-semibold">{{ $jenis === 'KI' ? 'Terima Dari' : 'Diberikan Kepada' }}</th>
                    <th class="px-4 py-3 font-semibold text-right">Nominal (Rp)</th>
                    <th class="px-4 py-3 font-semibold text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 whitespace-nowrap">
                @forelse($barisEdit as $b)
                    @php
                        $noDok = $jenis === 'KI' ? ($b->no_kuitansi ?? $b->no_bkm) : $b->no_bkk;
                        $pihak = $jenis === 'KI' ? ($b->terima_dari ?? $b->nama_pembayar) : ($b->diberikan_kepada ?? $b->nama_penerima);
                        $nominal = $jenis === 'KI' ? ($b->jumlah_dibayar ?? $b->jumlah_diterima) : ($b->jumlah_keluar ?? 0);
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-4 py-3 font-mono font-bold text-kp-blue-700">{{ $noDok }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $pihak }}</td>
                        <td class="px-4 py-3 text-right font-mono font-semibold text-slate-900 tabular-nums">
                            Rp {{ number_format((float) $nominal, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <a href="{{ route('kasir.utilitas.koreksi', ['edit' => $b->id, 'tipe' => $jenis]) }}"
                               class="inline-flex items-center gap-1.5 h-8 px-3 bg-slate-100 hover:bg-kp-blue-50 text-slate-700 hover:text-kp-blue-700 border border-slate-300 hover:border-kp-blue-200 font-semibold text-xs rounded-lg transition shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Koreksi</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-slate-500 text-sm">Belum ada inputan yang bisa dikoreksi pada shift ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

