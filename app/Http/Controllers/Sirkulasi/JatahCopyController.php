<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Services\SirkulasiDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class JatahCopyController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    /**
     * Copy Data Penjualan (Salin pola alokasi antar-tanggal kerja & cetak bukti).
     */
    public function index(Request $request)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $lastWeekSameDay = Carbon::tomorrow()->subDays(7);

        $sumberTanggal = $request->input('sumber_tanggal', $today->toDateString());
        $targetTanggal = $request->input('target_tanggal', $tomorrow->toDateString());
        $jurusanPilihan = $request->input('jurusan', 'all');

        $jurusanList = $this->sirkulasi->getDaftarJurusan();
        $targetLockReason = $this->sirkulasi->getLockReasonTanggalJatah($targetTanggal);
        $isTargetLocked = $targetLockReason !== null;

        $daftarAgen = $this->sirkulasi->getDaftarAgen()->where('status', 'aktif');
        if ($jurusanPilihan !== 'all') {
            $daftarAgen = $daftarAgen->where('jurusan_kode', $jurusanPilihan);
        }

        $copiedMap = Cache::get('jatah_harian_' . $targetTanggal, []);
        $copiedMeta = Cache::get('jatah_meta_' . $targetTanggal, []);

        $cfg = $this->sirkulasi->getConfig();
        $hargaEceran = $cfg['harga_eceran'];

        $matrixJatah = $daftarAgen->map(function ($agen) use ($copiedMap, $hargaEceran) {
            $row = $copiedMap[$agen->id] ?? null;
            if (!$row) {
                return null;
            }

            $langganan = (int) ($row['langganan'] ?? 0);
            $konsinyasi = (int) ($row['konsinyasi'] ?? 0);
            $gratis = (int) ($row['gratis'] ?? 0);
            $pengenalan = (int) ($row['pengenalan'] ?? 0);
            $insidentil = (int) ($row['insidentil'] ?? 0);
            $totalJatah = $langganan + $konsinyasi + $gratis + $pengenalan + $insidentil;

            return (object)[
                'agen_id'        => $agen->id,
                'kode_agen'      => $agen->kode_agen,
                'nama_agen'      => $agen->nama_agen,
                'alamat'         => $agen->alamat,
                'kategori'       => $agen->kategori,
                'jurusan_kode'   => $agen->jurusan_kode,
                'jurusan_nama'   => $agen->jurusan_nama,
                'langganan'      => $langganan,
                'konsinyasi'     => $konsinyasi,
                'gratis'         => $gratis,
                'pengenalan'     => $pengenalan,
                'insidentil'     => $insidentil,
                'total_jatah'    => $totalJatah,
                'estimasi_omset' => $konsinyasi * $hargaEceran,
                'status_pola'    => 'Tersalin',
            ];
        })->filter()->values();

        $hasCopied = $matrixJatah->isNotEmpty();
        $uncopiedCount = $daftarAgen->filter(fn($agen) => !isset($copiedMap[$agen->id]))->count();
        $isFullyCopied = $daftarAgen->isNotEmpty() && $uncopiedCount === 0;

        $totalLangganan = $matrixJatah->sum('langganan');
        $totalKonsinyasi = $matrixJatah->sum('konsinyasi');
        $totalGratis = $matrixJatah->sum('gratis');
        $totalPengenalan = $matrixJatah->sum('pengenalan');
        $totalInsidentil = $matrixJatah->sum('insidentil');
        $grandTotalJatah = $matrixJatah->sum('total_jatah');
        $grandTotalOmset = $matrixJatah->sum('estimasi_omset');

        return view('pages.sirkulasi.jatah.copy', compact(
            'today',
            'tomorrow',
            'lastWeekSameDay',
            'sumberTanggal',
            'targetTanggal',
            'jurusanPilihan',
            'jurusanList',
            'hasCopied',
            'isFullyCopied',
            'uncopiedCount',
            'copiedMeta',
            'isTargetLocked',
            'targetLockReason',
            'matrixJatah',
            'totalLangganan',
            'totalKonsinyasi',
            'totalGratis',
            'totalPengenalan',
            'totalInsidentil',
            'grandTotalJatah',
            'grandTotalOmset'
        ));
    }

    /**
     * Eksekusi salin pola jatah antar tanggal.
     */
    public function process(Request $request)
    {
        $today = Carbon::today();
        $sumberTanggal = $request->input('sumber_tanggal', Carbon::today()->toDateString());
        $targetTanggal = $request->input('target_tanggal', Carbon::tomorrow()->toDateString());
        $jurusan = $request->input('jurusan', 'all');
        $action = $request->input('action_type', 'stay_and_print');

        if ($sumberTanggal === $targetTanggal) {
            return redirect()->route('sirkulasi.jatah.copy', [
                'sumber_tanggal' => $sumberTanggal,
                'target_tanggal' => $targetTanggal,
                'jurusan'        => $jurusan,
            ])->with('error', 'Tanggal asal dan tanggal tujuan tidak boleh sama.');
        }

        $targetLockReason = $this->sirkulasi->getLockReasonTanggalJatah($targetTanggal);
        if ($targetLockReason !== null) {
            return redirect()->route('sirkulasi.jatah.copy', [
                'sumber_tanggal' => $sumberTanggal,
                'target_tanggal' => $targetTanggal,
                'jurusan'        => $jurusan,
            ])->with('error', "Salin ditolak: {$targetLockReason} Data oplah tanggal tujuan sudah di-cutoff dan tidak dapat diubah.");
        }

        $sumberCarbon = Carbon::parse($sumberTanggal);
        $sumberSavedMap = Cache::get('jatah_harian_' . $sumberTanggal, []);

        if ($sumberCarbon->gt($today) && empty($sumberSavedMap)) {
            return redirect()->route('sirkulasi.jatah.copy', [
                'sumber_tanggal' => $sumberTanggal,
                'target_tanggal' => $targetTanggal,
                'jurusan'        => $jurusan,
            ])->with('error', "Data jatah tanggal {$sumberTanggal} masih kosong.");
        }

        $daftarAgen = $this->sirkulasi->getDaftarAgen()->where('status', 'aktif');
        if ($jurusan !== 'all') {
            $daftarAgen = $daftarAgen->where('jurusan_kode', $jurusan);
        }

        $targetSavedMap = Cache::get('jatah_harian_' . $targetTanggal, []);
        $agenBelumDisalin = $daftarAgen->filter(fn($agen) => !isset($targetSavedMap[$agen->id]));
        if ($agenBelumDisalin->isEmpty()) {
            return redirect()->route('sirkulasi.jatah.copy', [
                'sumber_tanggal' => $sumberTanggal,
                'target_tanggal' => $targetTanggal,
                'jurusan'        => $jurusan,
            ])->with('error', "Data jatah untuk tanggal {$targetTanggal} sudah tersedia. Gunakan menu Edit Data Penjualan Koran untuk mengubah alokasi agen tanpa menimpa data yang sudah diedit.");
        }

        $cfg = $this->sirkulasi->getConfig();
        $pctL = $cfg['pct_langganan'];
        $pctK = $cfg['pct_konsinyasi'];
        $pctG = $cfg['pct_gratis'];
        $copiedCount = 0;

        foreach ($agenBelumDisalin as $agen) {
            $src = $sumberSavedMap[$agen->id] ?? null;
            if ($sumberCarbon->gt($today) && !$src) {
                continue;
            }

            $targetSavedMap[$agen->id] = [
                'langganan'  => isset($src['langganan']) ? (int) $src['langganan'] : (int) ($agen->jatah_default * $pctL),
                'konsinyasi' => isset($src['konsinyasi']) ? (int) $src['konsinyasi'] : (int) ($agen->jatah_default * $pctK),
                'gratis'     => isset($src['gratis']) ? (int) $src['gratis'] : (int) ($agen->jatah_default * $pctG),
                'pengenalan' => isset($src['pengenalan']) ? (int) $src['pengenalan'] : 0,
                'insidentil' => isset($src['insidentil']) ? (int) $src['insidentil'] : 0,
            ];
            $copiedCount++;
        }

        if ($copiedCount === 0) {
            return redirect()->route('sirkulasi.jatah.copy', [
                'sumber_tanggal' => $sumberTanggal,
                'target_tanggal' => $targetTanggal,
                'jurusan'        => $jurusan,
            ])->with('error', "Data jatah tanggal {$sumberTanggal} masih kosong.");
        }

        Cache::put('jatah_harian_' . $targetTanggal, $targetSavedMap, now()->addDays(30));
        Cache::put('jatah_meta_' . $targetTanggal, [
            'copied_from' => $sumberTanggal,
            'copied_at'   => now()->format('d/m/Y H:i'),
            'copied_by'   => auth()->user()?->name ?? 'Operator',
        ], now()->addDays(30));

        if ($action === 'go_to_edit') {
            return redirect()->route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal, 'jurusan' => $jurusan])
                ->with('success', "Data jatah berhasil disalin dari {$sumberTanggal} ke {$targetTanggal}.");
        }

        return redirect()->route('sirkulasi.jatah.copy', [
            'sumber_tanggal' => $sumberTanggal,
            'target_tanggal' => $targetTanggal,
            'jurusan'        => $jurusan,
        ])->with('success', "Data jatah berhasil disalin dari {$sumberTanggal} ke {$targetTanggal} ({$copiedCount} agen).");
    }
}
