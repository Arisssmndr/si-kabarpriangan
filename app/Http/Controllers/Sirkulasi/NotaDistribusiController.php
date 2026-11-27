<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Services\SirkulasiDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NotaDistribusiController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    /**
     * Nota Kirim Percetakan & Agen (Cetak massal harian sesuai data Edit Jatah).
     */
    public function index(Request $request)
    {
        $cfg = $this->sirkulasi->getConfig();
        $isiBendel = $cfg['isi_bendel'];
        $pctL = $cfg['pct_langganan'];
        $pctK = $cfg['pct_konsinyasi'];
        $pctG = $cfg['pct_gratis'];

        $today = Carbon::today();
        $tanggalDistribusi = $request->input('tanggal', Carbon::tomorrow()->toDateString());
        $jurusanPilihan = $request->input('jurusan', 'all');
        $search = trim((string) $request->input('q', ''));
        $carbonTarget = Carbon::parse($tanggalDistribusi);
        $isFutureDate = $carbonTarget->gt($today);

        $savedJatahMap = Cache::get('jatah_harian_' . $tanggalDistribusi, []);
        $daftarAgen = $this->sirkulasi->getDaftarAgen()
            ->where('status', 'aktif')
            ->when($jurusanPilihan !== 'all', fn($col) => $col->where('jurusan_kode', $jurusanPilihan))
            ->when($search !== '', function ($col) use ($search) {
                $needle = mb_strtolower($search);
                return $col->filter(function ($agen) use ($needle) {
                    return str_contains(mb_strtolower((string) $agen->nama_agen), $needle)
                        || str_contains(mb_strtolower((string) $agen->kode_agen), $needle)
                        || str_contains(mb_strtolower((string) $agen->alamat), $needle)
                        || str_contains(mb_strtolower((string) $agen->wilayah_nama), $needle);
                });
            })
            ->sortBy(fn($a) => sprintf('%s-%06d', $a->jurusan_kode ?? '99', $a->id))
            ->values();

        $inisialWilayahMap = [
            '01' => 'T', '02' => 'G', '03' => 'C', '04' => 'B',
            '05' => 'S', '06' => 'P', '07' => 'BDG', '08' => 'I',
        ];

        $notaItems = $daftarAgen->map(function ($agen) use ($savedJatahMap, $isFutureDate, $inisialWilayahMap, $isiBendel, $pctL, $pctK, $pctG) {
            $custom = $savedJatahMap[$agen->id] ?? null;
            if ($isFutureDate && !$custom) {
                return null;
            }

            $langganan  = isset($custom['langganan']) ? (int) $custom['langganan'] : (int) ($agen->jatah_default * $pctL);
            $eceran     = isset($custom['konsinyasi']) ? (int) $custom['konsinyasi'] : (int) ($agen->jatah_default * $pctK);
            $gratis     = isset($custom['gratis']) ? (int) $custom['gratis'] : (int) ($agen->jatah_default * $pctG);
            $perkenalan = isset($custom['pengenalan']) ? (int) $custom['pengenalan'] : 0;
            $insidentil = isset($custom['insidentil']) ? (int) $custom['insidentil'] : 0;
            $totalEks   = $langganan + $eceran + $gratis + $perkenalan + $insidentil;

            if ($totalEks <= 0) {
                return null;
            }

            return (object)[
                'agen_id'         => $agen->id,
                'kode_agen'       => $agen->kode_agen,
                'kode_perkiraan'  => '1.00.04.01.01.00.' . str_pad((string) $agen->id, 4, '0', STR_PAD_LEFT),
                'nama_agen'       => $agen->nama_agen,
                'alamat'          => $agen->alamat,
                'wilayah_kode'    => $agen->wilayah_kode,
                'wilayah_nama'    => $agen->wilayah_nama,
                'inisial_wilayah' => $inisialWilayahMap[$agen->wilayah_kode] ?? strtoupper(substr(trim($agen->wilayah_nama ?? 'U'), 0, 1)),
                'jurusan_kode'    => $agen->jurusan_kode,
                'jurusan_nama'    => $agen->jurusan_nama,
                'langganan'       => $langganan,
                'eceran'          => $eceran,
                'gratis'          => $gratis,
                'perkenalan'      => $perkenalan,
                'insidentil'      => $insidentil,
                'total_eks'       => $totalEks,
                'bendel_50'       => intdiv($totalEks, $isiBendel),
                'sisa_ecer'       => $totalEks % $isiBendel,
            ];
        })->filter()->values();

        $jurusanList     = $this->sirkulasi->getDaftarJurusan();
        $hariEdisi       = strtoupper($carbonTarget->copy()->locale('id')->translatedFormat('l, d/m/Y'));
        $totalNota       = $notaItems->count();
        $totalEksemplar  = $notaItems->sum('total_eks');
        $totalLangganan  = $notaItems->sum('langganan');
        $totalEceran     = $notaItems->sum('eceran');
        $totalGratis     = $notaItems->sum('gratis');
        $totalPerkenalan = $notaItems->sum('perkenalan');
        $totalInsidentil = $notaItems->sum('insidentil');
        $totalBendel     = $notaItems->sum('bendel_50');
        $totalLepas      = $notaItems->sum('sisa_ecer');

        return view('pages.sirkulasi.cetak.nota-distribusi', compact(
            'tanggalDistribusi',
            'jurusanPilihan',
            'jurusanList',
            'search',
            'hariEdisi',
            'notaItems',
            'totalNota',
            'totalEksemplar',
            'totalLangganan',
            'totalEceran',
            'totalGratis',
            'totalPerkenalan',
            'totalInsidentil',
            'totalBendel',
            'totalLepas',
            'isiBendel'
        ));
    }
}
