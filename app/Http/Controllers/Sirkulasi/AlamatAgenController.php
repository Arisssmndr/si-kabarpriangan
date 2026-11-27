<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Services\SirkulasiDataService;
use Illuminate\Http\Request;

class AlamatAgenController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    /**
     * Nama & Alamat Agen (Cetak Direktori Agen per Wilayah/Jurusan).
     */
    public function index(Request $request)
    {
        $wilayahPilihan = $request->input('wilayah', 'all');
        $jurusanPilihan = $request->input('jurusan', 'all');
        $kategoriPilihan = $request->input('kategori', 'all');

        $agenList = $this->sirkulasi->getDaftarAgen();

        if ($wilayahPilihan !== 'all') {
            $agenList = $agenList->where('wilayah_kode', $wilayahPilihan);
        }
        if ($jurusanPilihan !== 'all') {
            $agenList = $agenList->where('jurusan_kode', $jurusanPilihan);
        }
        if ($kategoriPilihan !== 'all') {
            $agenList = $agenList->where('kategori', $kategoriPilihan);
        }

        $wilayahList = $this->sirkulasi->getDaftarWilayah();
        $jurusanList = $this->sirkulasi->getDaftarJurusan();
        $jenisList = $this->sirkulasi->getDaftarJenisAgen();

        return view('pages.sirkulasi.cetak.alamat-agen', compact(
            'agenList',
            'wilayahList',
            'jurusanList',
            'jenisList',
            'wilayahPilihan',
            'jurusanPilihan',
            'kategoriPilihan'
        ));
    }
}
