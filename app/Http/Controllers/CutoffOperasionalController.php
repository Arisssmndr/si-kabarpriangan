<?php

namespace App\Http\Controllers;

use App\Models\DailyCutoff;
use App\Models\MemoUnlock;
use App\Models\SistemConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CutoffOperasionalController extends Controller
{
    /**
     * Jalankan Tutup Transaksi Harian (Daily Cut-off Lock)
     */
    public function tutup(Request $request)
    {
        $request->validate([
            'tanggal_edisi' => 'required|date',
            'catatan'       => 'nullable|string|max:500',
        ]);

        $tanggal = $request->tanggal_edisi;

        DailyCutoff::updateOrCreate(
            ['tanggal_edisi' => $tanggal],
            [
                'jam_cutoff'        => SistemConfig::getJamCutoff(),
                'is_closed'         => true,
                'closed_by_user_id' => Auth::id(),
                'closed_at'         => now(),
                'catatan'           => $request->catatan ?? 'Tutup transaksi harian pracetak resmi.',
            ]
        );

        // Jika ada memo unlock aktif sebelumnya untuk tanggal ini, tutup juga
        MemoUnlock::whereDate('tanggal_target', $tanggal)
            ->where('status', 'OPEN')
            ->update([
                'status'               => 'CLOSED',
                'ditutup_kembali_pada' => now(),
            ]);

        return back()->with('success', "Transaksi edisi {$tanggal} resmi DITUTUP (Cut-Off). Data pracetak dikunci dari penambahan baru.");
    }

    /**
     * Ajukan & Buka Kunci Transaksi dengan Memo Digital (Otomatis Terkirim ke Divisi Akuntansi)
     */
    public function bukaMemo(Request $request)
    {
        $request->validate([
            'tanggal_target'    => 'required|date',
            'kategori_urgensi'  => 'required|string|max:100',
            'nama_pemasang'     => 'required|string|max:150',
            'pengaju'           => 'nullable|string|max:150',
            'spesifikasi_iklan' => 'nullable|string|max:150',
            'estimasi_nominal'  => 'nullable',
            'alasan'            => 'required|string|min:5|max:800',
            'diketahui_oleh'    => 'nullable|string|max:100',
        ]);

        $nomorMemo = $request->filled('nomor_memo')
            ? strtoupper(trim($request->nomor_memo))
            : MemoUnlock::generateNomorMemo();

        $estimasiClean = (int) preg_replace('/[^0-9]/', '', (string) ($request->estimasi_nominal ?? 0));
        $pengajuStr = trim((string) ($request->input('pengaju') ?: (Auth::user()->name ?? 'Front Office')));
        $spekInput  = trim((string) $request->input('spesifikasi_iklan'));
        $spesifikasiFinal = $spekInput !== '' ? $spekInput : $pengajuStr;

        $memo = MemoUnlock::create([
            'tanggal_target'      => $request->tanggal_target,
            'nomor_memo'          => $nomorMemo,
            'kategori_urgensi'    => $request->input('kategori_urgensi', 'Iklan Dukacita'),
            'nama_pemasang'       => $request->input('nama_pemasang'),
            'spesifikasi_iklan'   => $spesifikasiFinal,
            'estimasi_nominal'    => $estimasiClean,
            'alasan'              => $request->alasan,
            'tujuan_divisi'       => $pengajuStr,
            'status_akuntansi'    => 'TERKIRIM',
            'diketahui_oleh'      => $request->input('diketahui_oleh', 'Shinta Dewi, SH., M.H.'),
            'dibuka_oleh_user_id' => Auth::id(),
            'status'              => 'OPEN',
            'dibuka_pada'         => now(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status'     => 'success',
                'message'    => "Memo Digital #{$memo->nomor_memo} berhasil dikirim ke Divisi Akuntansi & kunci edisi {$request->tanggal_target} telah dibuka.",
                'data'       => [
                    'id'               => $memo->id,
                    'nomor_memo'       => $memo->nomor_memo,
                    'tanggal_target'   => \Carbon\Carbon::parse($memo->tanggal_target)->toDateString(),
                    'kategori_urgensi' => $memo->kategori_urgensi,
                    'cetak_url'        => route('iklan.cutoff.memo-unlock.cetak', $memo->id),
                ],
                'next_nomor' => MemoUnlock::generateNomorMemo(),
            ]);
        }

        return back()->with('success', "Memo Digital #{$memo->nomor_memo} resmi diterbitkan & dikirim ke Divisi Akuntansi. Kunci edisi {$request->tanggal_target} kini TERBUKA.");
    }

    /**
     * Tutup Kembali Memo Unlock setelah order darurat selesai diinput
     */
    public function tutupMemo(string $id)
    {
        $memo = MemoUnlock::findOrFail($id);
        $memo->update([
            'status'               => 'CLOSED',
            'ditutup_kembali_pada' => now(),
        ]);

        return back()->with('success', "Memo #{$memo->nomor_memo} resmi DITUTUP KEMBALI. Edisi kembali terkunci Cut-Off.");
    }

    /**
     * Tampilan / Cetak Dokumen Resmi Memo Digital Buka Cut-Off (Tembusan Divisi Akuntansi)
     */
    public function cetakMemo(string $id)
    {
        $memo = MemoUnlock::with('dibukaOleh')->findOrFail($id);

        return view('pages.iklan.rekap_pracetak.cetak_memo', compact('memo'));
    }
}
