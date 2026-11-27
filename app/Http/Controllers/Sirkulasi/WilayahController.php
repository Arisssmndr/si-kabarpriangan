<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Services\SirkulasiDataService;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    /**
     * Tabel Wilayah Pemasaran (01 s.d. 08).
     */
    public function index(Request $request)
    {
        $wilayahList = $this->sirkulasi->getDaftarWilayah();
        $totalAlokasi = $wilayahList->sum('alokasi_default');
        $totalAgen = $wilayahList->sum('total_agen');

        return view('pages.sirkulasi.tabel.wilayah', compact('wilayahList', 'totalAlokasi', 'totalAgen'));
    }
}
