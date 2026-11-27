<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Models\MemoUnlock;
use App\Models\SistemConfig;
use App\Services\SirkulasiDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class JatahEditController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    /**
     * Edit Data Penjualan (Input matriks jatah H+1).
     */
    public function index(Request $request)
    {
        $today = Carbon::today();
        $targetTanggal = $request->input('tanggal', Carbon::tomorrow()->toDateString());
        $jurusanPilihan = $request->input('jurusan', 'all');
        $carbonTarget = Carbon::parse($targetTanggal);
        $isFutureDate = $carbonTarget->gt($today);
        $prevTanggal = $carbonTarget->copy()->subDay()->toDateString();

        $daftarAgen = $this->sirkulasi->getDaftarAgen()->where('status', 'aktif');
        if ($jurusanPilihan !== 'all') {
            $daftarAgen = $daftarAgen->where('jurusan_kode', $jurusanPilihan);
        }

        $savedJatahMap = Cache::get('jatah_harian_' . $targetTanggal, []);
        $jatahMeta = Cache::get('jatah_meta_' . $targetTanggal);

        $cfg = $this->sirkulasi->getConfig();
        $hargaEceran = $cfg['harga_eceran'];
        $pctL = $cfg['pct_langganan'];
        $pctK = $cfg['pct_konsinyasi'];
        $pctG = $cfg['pct_gratis'];

        $matrixJatah = collect([]);
        if (!$isFutureDate || !empty($savedJatahMap)) {
            $matrixJatah = $daftarAgen->map(function ($agen) use ($savedJatahMap, $isFutureDate, $hargaEceran, $pctL, $pctK, $pctG) {
                $custom = $savedJatahMap[$agen->id] ?? null;
                if ($isFutureDate && !$custom) {
                    return null;
                }

                $langganan = isset($custom['langganan']) ? (int) $custom['langganan'] : (int) ($agen->jatah_default * $pctL);
                $konsinyasi = isset($custom['konsinyasi']) ? (int) $custom['konsinyasi'] : (int) ($agen->jatah_default * $pctK);
                $gratis = isset($custom['gratis']) ? (int) $custom['gratis'] : (int) ($agen->jatah_default * $pctG);
                $pengenalan = isset($custom['pengenalan']) ? (int) $custom['pengenalan'] : 0;
                $insidentil = isset($custom['insidentil']) ? (int) $custom['insidentil'] : 0;
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
                ];
            })->filter()->values();
        }

        $hasJatahData = $matrixJatah->isNotEmpty();

        $totalLangganan = $matrixJatah->sum('langganan');
        $totalKonsinyasi = $matrixJatah->sum('konsinyasi');
        $totalGratis = $matrixJatah->sum('gratis');
        $totalPengenalan = $matrixJatah->sum('pengenalan');
        $totalInsidentil = $matrixJatah->sum('insidentil');
        $grandTotalJatah = $matrixJatah->sum('total_jatah');
        $grandTotalOmset = $matrixJatah->sum('estimasi_omset');

        $jurusanList = $this->sirkulasi->getDaftarJurusan();
        $lockReason = $this->sirkulasi->getLockReasonTanggalJatah($targetTanggal);
        $isCutoffLocked = $lockReason !== null;
        $activeMemo = Schema::hasTable('memo_unlocks')
            ? MemoUnlock::whereDate('tanggal_target', $targetTanggal)
                ->where('tujuan_divisi', 'sirkulasi')
                ->latest('id')
                ->first()
            : null;

        $jamCutoffSirkulasi = class_exists(SistemConfig::class) ? SistemConfig::getJamCutoffSirkulasi() : '17:00';
        $user = auth()->user();
        $canManageCutoff = $user && ($user->isAdmin() || $user->hasRole('admin', 'administrator', 'manager', 'sirkulasi', 'akuntansi', 'accounting', 'keuangan'));
        $isPastCutoffToday = Carbon::now()->format('H:i') >= $jamCutoffSirkulasi;

        return view('pages.sirkulasi.jatah.edit', compact(
            'matrixJatah',
            'targetTanggal',
            'prevTanggal',
            'isFutureDate',
            'hasJatahData',
            'jatahMeta',
            'jurusanPilihan',
            'jurusanList',
            'totalLangganan',
            'totalKonsinyasi',
            'totalGratis',
            'totalPengenalan',
            'totalInsidentil',
            'grandTotalJatah',
            'grandTotalOmset',
            'today',
            'isCutoffLocked',
            'lockReason',
            'activeMemo',
            'hargaEceran',
            'jamCutoffSirkulasi',
            'canManageCutoff',
            'isPastCutoffToday'
        ));
    }

    /**
     * Simpan perubahan jatah oplah harian.
     */
    public function store(Request $request)
    {
        $targetTanggal = $request->input('tanggal_target', $request->input('tanggal', Carbon::tomorrow()->toDateString()));
        $lockReason = $this->sirkulasi->getLockReasonTanggalJatah($targetTanggal);

        if ($lockReason !== null) {
            return redirect()->route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal])
                ->with('error', "Perubahan ditolak: {$lockReason} Data oplah yang sudah di-cutoff tidak dapat diubah kembali.");
        }

        if ($request->has('jatah') && is_array($request->input('jatah'))) {
            $existing = Cache::get('jatah_harian_' . $targetTanggal, []);
            foreach ($request->input('jatah') as $agenId => $row) {
                $existing[$agenId] = [
                    'langganan'  => max(0, (int) ($row['langganan'] ?? 0)),
                    'konsinyasi' => max(0, (int) ($row['konsinyasi'] ?? 0)),
                    'gratis'     => max(0, (int) ($row['gratis'] ?? 0)),
                    'pengenalan' => max(0, (int) ($row['pengenalan'] ?? 0)),
                    'insidentil' => max(0, (int) ($row['insidentil'] ?? 0)),
                ];
            }
            Cache::put('jatah_harian_' . $targetTanggal, $existing, now()->addDays(30));
        }

        return redirect()->route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal])
            ->with('success', "Data jatah tanggal {$targetTanggal} berhasil disimpan.");
    }
}
