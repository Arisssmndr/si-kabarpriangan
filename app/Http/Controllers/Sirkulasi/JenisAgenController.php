<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Services\SirkulasiDataService;
use Illuminate\Http\Request;

class JenisAgenController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    /**
     * Tabel Jenis Agen (00, 11, 99).
     */
    public function index(Request $request)
    {
        $jenisList = $this->sirkulasi->getDaftarJenisAgen();
        $agenList = $this->sirkulasi->getDaftarAgen();

        return view('pages.sirkulasi.tabel.jenis-agen', compact('jenisList', 'agenList'));
    }
}
