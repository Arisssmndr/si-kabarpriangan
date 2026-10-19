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

        $cutoff = DailyCutoff::updateOrCreate(
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
        MemoUnlock::where('tanggal_target', $tanggal)
            ->where('status', 'OPEN')
            ->update([
                'status'               => 'CLOSED',
                'ditutup_kembali_pada' => now(),
            ]);

        return back()->with('success', "Transaksi edisi {$tanggal} resmi DITUTUP. Data pracetak dikunci dari penambahan baru.");
    }

    /**
     * Buka Kunci Transaksi dengan Memo Unlock Supervisor
     */
    public function bukaMemo(Request $request)
    {
        $request->validate([
            'tanggal_target' => 'required|date',
            'nomor_memo'     => 'required|string|max:50',
            'alasan'         => 'required|string|min:5|max:500',
        ]);

        $memo = MemoUnlock::create([
            'tanggal_target'      => $request->tanggal_target,
            'nomor_memo'          => strtoupper(trim($request->nomor_memo)),
            'alasan'              => $request->alasan,
            'dibuka_oleh_user_id' => Auth::id(),
            'status'              => 'OPEN',
            'dibuka_pada'         => now(),
        ]);

        return back()->with('success', "Kunci transaksi edisi {$request->tanggal_target} berhasil DIBUKA via Memo #{$memo->nomor_memo}. Audit trail aktif.");
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

        return back()->with('success', "Memo #{$memo->nomor_memo} resmi DITUTUP KEMBALI. Transaksi kembali terkunci.");
    }
}
