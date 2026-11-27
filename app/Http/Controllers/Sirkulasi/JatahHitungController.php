<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Services\SirkulasiDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JatahHitungController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    /**
     * Hitung Data Penjualan (Kalkulasi akumulasi eksemplar).
     */
    public function index(Request $request)
    {
        $cfg = $this->sirkulasi->getConfig();
        $hargaEceran = $cfg['harga_eceran'];
        $tanggalPilihan = $request->input('tanggal', Carbon::tomorrow()->toDateString());
        $jurusanList = $this->sirkulasi->getDaftarJurusan();

        $rekapKalkulasi = $jurusanList->map(function ($jurusan) use ($hargaEceran) {
            $langganan = (int) ($jurusan->alokasi_oplah * 0.58);
            $konsinyasi = (int) ($jurusan->alokasi_oplah * 0.36);
            $gratis = (int) ($jurusan->alokasi_oplah * 0.04);
            $insidentil = $jurusan->alokasi_oplah - ($langganan + $konsinyasi + $gratis);
            $totalEks = $langganan + $konsinyasi + $gratis + $insidentil;
            $omsetKonsinyasi = $konsinyasi * $hargaEceran;

            return (object)[
                'kode'             => $jurusan->kode,
                'nama_jurusan'     => $jurusan->nama_jurusan,
                'armada'           => $jurusan->armada,
                'langganan'        => $langganan,
                'konsinyasi'       => $konsinyasi,
                'gratis'           => $gratis,
                'pengenalan'       => 0,
                'insidentil'       => $insidentil,
                'total_eks'        => $totalEks,
                'omset_konsinyasi' => $omsetKonsinyasi,
            ];
        });

        $totalLangganan = $rekapKalkulasi->sum('langganan');
        $totalKonsinyasi = $rekapKalkulasi->sum('konsinyasi');
        $totalGratis = $rekapKalkulasi->sum('gratis');
        $totalPengenalan = 0;
        $totalInsidentil = $rekapKalkulasi->sum('insidentil');
        $grandTotalEks = $rekapKalkulasi->sum('total_eks');
        $grandTotalOmset = $rekapKalkulasi->sum('omset_konsinyasi');

        return view('pages.sirkulasi.jatah.hitung', compact(
            'tanggalPilihan',
            'rekapKalkulasi',
            'totalLangganan',
            'totalKonsinyasi',
            'totalGratis',
            'totalPengenalan',
            'totalInsidentil',
            'grandTotalEks',
            'grandTotalOmset',
            'hargaEceran'
        ));
    }

    /**
     * Proses kalkulasi ulang.
     */
    public function process(Request $request)
    {
        $tanggal = $request->input('tanggal');
        return redirect()->route('sirkulasi.jatah.hitung', ['tanggal' => $tanggal])
            ->with('success', "Perhitungan akumulasi eksemplar penjualan untuk tanggal {$tanggal} berhasil dikalkulasi ulang.");
    }
}
