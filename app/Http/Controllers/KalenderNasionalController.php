<?php

namespace App\Http\Controllers;

use App\Services\KalenderNasionalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KalenderNasionalController extends Controller
{
    protected KalenderNasionalService $service;

    public function __construct(KalenderNasionalService $service)
    {
        $this->service = $service;
    }

    /**
     * Ambil data libur & hari peringatan nasional untuk tahun & bulan tertentu.
     */
    public function getBulan(Request $request): JsonResponse
    {
        $tahun = (int) ($request->query('year') ?? date('Y'));
        $bulan = (int) ($request->query('month') ?? date('n'));

        $data = $this->service->getKalenderBulan($tahun, $bulan);

        return response()->json([
            'year'       => $tahun,
            'month'      => $bulan,
            'libur'      => $data['libur'],
            'peringatan' => $data['peringatan'],
        ]);
    }
}
