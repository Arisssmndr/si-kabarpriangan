<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Services\SirkulasiDataService;
use Illuminate\Http\Request;

class PengepakanController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    /**
     * Tabel Pengepakan (01 Bagian Sirkulasi).
     */
    public function index(Request $request)
    {
        $pengepakanList = $this->sirkulasi->getDaftarPengepakan();
        $jurusanList = $this->sirkulasi->getDaftarJurusan();

        return view('pages.sirkulasi.tabel.pengepakan', compact('pengepakanList', 'jurusanList'));
    }
}
