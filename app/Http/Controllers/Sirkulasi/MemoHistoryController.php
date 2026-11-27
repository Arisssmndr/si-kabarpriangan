<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Models\MemoUnlock;
use Illuminate\Http\Request;

class MemoHistoryController extends Controller
{
    /**
     * Riwayat Seluruh Pengajuan Memo Buka Kunci Sirkulasi.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $status = strtoupper(trim((string) $request->input('status', '')));
        $tanggal = $request->input('tanggal');

        $baseQuery = MemoUnlock::query()->where(function ($sub) {
            $sub->where('tujuan_divisi', 'sirkulasi')
                ->orWhere('nomor_memo', 'like', '%SRK%')
                ->orWhere('nomor_memo', 'like', '%SIRK%')
                ->orWhere('nomor_memo', 'like', 'REQ-ULK-%');
        });

        $stats = [
            'total'    => (clone $baseQuery)->count(),
            'pending'  => (clone $baseQuery)->where('status_akuntansi', 'PENDING')->count(),
            'approved' => (clone $baseQuery)->whereIn('status_akuntansi', ['APPROVED', 'OPEN', 'DISETUJUI'])->count(),
            'rejected' => (clone $baseQuery)->whereIn('status_akuntansi', ['REJECTED', 'DITOLAK'])->count(),
        ];

        $query = (clone $baseQuery)->with(['dibukaOleh', 'diresponOleh'])->latest();

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nomor_memo', 'like', "%{$q}%")
                    ->orWhere('alasan', 'like', "%{$q}%")
                    ->orWhere('diketahui_oleh', 'like', "%{$q}%")
                    ->orWhere('catatan_akuntansi', 'like', "%{$q}%");
            });
        }

        if ($status !== '' && $status !== 'SEMUA') {
            if ($status === 'APPROVED') {
                $query->whereIn('status_akuntansi', ['APPROVED', 'OPEN', 'DISETUJUI']);
            } elseif ($status === 'REJECTED') {
                $query->whereIn('status_akuntansi', ['REJECTED', 'DITOLAK']);
            } else {
                $query->where('status_akuntansi', $status);
            }
        }

        if (!empty($tanggal)) {
            $query->whereDate('tanggal_target', $tanggal);
        }

        $memos = $query->paginate(15)->withQueryString();

        return view('pages.sirkulasi.memo.history', compact('memos', 'stats', 'q', 'status', 'tanggal'));
    }
}
