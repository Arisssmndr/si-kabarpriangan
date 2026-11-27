<?php

namespace App\Http\Controllers;

use App\Services\SirkulasiDataService;
use Illuminate\Support\Collection;

/**
 * Facade kompatibilitas mundur untuk modul Sirkulasi.
 * Seluruh controller menu Sirkulasi kini dipecah 1 file = 1 menu di `app/Http/Controllers/Sirkulasi/`.
 */
class SirkulasiController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    public static function executeTutupHarian(string $tanggal, ?string $operator = null): bool
    {
        return SirkulasiDataService::executeTutupHarian($tanggal, $operator);
    }

    public function getDaftarWilayah(): Collection
    {
        return $this->sirkulasi->getDaftarWilayah();
    }

    public function getDaftarPengepakan(): Collection
    {
        return $this->sirkulasi->getDaftarPengepakan();
    }

    public function getDaftarJenisAgen(): Collection
    {
        return $this->sirkulasi->getDaftarJenisAgen();
    }

    public function getDaftarJurusan(): Collection
    {
        return $this->sirkulasi->getDaftarJurusan();
    }

    public function getDaftarAgen(): Collection
    {
        return $this->sirkulasi->getDaftarAgen();
    }

    public function getFakturPayloadByNo(string $noFaktur): ?array
    {
        return $this->sirkulasi->getFakturPayloadByNo($noFaktur);
    }
}
