<?php

namespace App\Http\Controllers;

use App\Models\SistemConfig;
use Illuminate\Http\Request;

class SistemConfigController extends Controller
{
    /**
     * Tampilkan formulir pengaturan periklanan & tarif
     */
    public function index()
    {
        $configs = SistemConfig::all()->keyBy('key');
        return view('pages.iklan.pengaturan.index', compact('configs'));
    }

    /**
     * Simpan perubahan pengaturan
     */
    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $val) {
            if ($val !== null) {
                // Bersihkan format ribuan jika input angka tarif
                if (in_array($key, ['tarif_per_baris', 'tarif_mmk_bw', 'tarif_mmk_fc', 'materai_nominal', 'materai_threshold'])) {
                    $val = str_replace(['.', ','], '', $val);
                }
                SistemConfig::set($key, $val);
            }
        }

        return redirect()->route('iklan.pengaturan.index')
            ->with('success', 'Konfigurasi tarif dan operasional iklan berhasil diperbarui!');
    }
}
