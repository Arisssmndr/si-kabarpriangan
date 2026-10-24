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

        // Sinkronisasi otomatis ke Master Jenis Iklan jika tarif diubah dari Menu Pengaturan Tarif & Pajak
        if (isset($data['tarif_per_baris'])) {
            $tarifBaris = (int) str_replace(['.', ','], '', $data['tarif_per_baris']);
            \App\Models\JenisIklan::where('tipe_dasar', 'BARIS')->update(['tarif_dasar' => $tarifBaris]);
        }
        if (isset($data['tarif_mmk_bw'])) {
            $tarifBw = (int) str_replace(['.', ','], '', $data['tarif_mmk_bw']);
            \App\Models\JenisIklan::where('cara_hitung', 'PER_MMK')
                ->where('mode_warna', 'BW')
                ->update(['tarif_dasar' => $tarifBw]);
        }
        if (isset($data['tarif_mmk_fc'])) {
            $tarifFc = (int) str_replace(['.', ','], '', $data['tarif_mmk_fc']);
            \App\Models\JenisIklan::where('cara_hitung', 'PER_MMK')
                ->where('mode_warna', 'FC')
                ->update(['tarif_dasar' => $tarifFc]);
        }

        return redirect()->route('iklan.pengaturan.index')
            ->with('success', 'Konfigurasi tarif dan operasional iklan berhasil diperbarui!');
    }
}
