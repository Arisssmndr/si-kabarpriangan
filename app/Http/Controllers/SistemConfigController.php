<?php

namespace App\Http\Controllers;

use App\Models\JenisIklan;
use App\Models\SistemConfig;
use Illuminate\Http\Request;

class SistemConfigController extends Controller
{
    /**
     * Tampilkan halaman ringkasan pengaturan tarif & pajak iklan (read-only + modal ubah)
     */
    public function index()
    {
        // Bersihkan parameter meterai lama yang sudah tidak digunakan
        SistemConfig::whereIn('key', ['materai_nominal', 'materai_threshold'])->delete();

        $configs = SistemConfig::all()->keyBy('key');

        $barisJenis = JenisIklan::where('tipe_dasar', 'BARIS')->first();
        $mmkBwJenis = JenisIklan::where('cara_hitung', 'PER_MMK')->where('mode_warna', 'BW')->first();
        $mmkFcJenis = JenisIklan::where('cara_hitung', 'PER_MMK')->where('mode_warna', 'FC')->first();
        $portalJenis = JenisIklan::where('kode_jenis', 'ONL003')->first();

        $tarifBaris = $barisJenis
            ? (int) $barisJenis->tarif_dasar
            : (int) SistemConfig::get('tarif_per_baris', 22750);

        $tarifMmkBw = $mmkBwJenis
            ? (int) $mmkBwJenis->tarif_dasar
            : (int) SistemConfig::get('tarif_mmk_bw', 25000);

        $tarifMmkFc = $mmkFcJenis
            ? (int) $mmkFcJenis->tarif_dasar
            : (int) SistemConfig::get('tarif_mmk_fc', 45000);

        $tarifPortal = (int) SistemConfig::get(
            'tarif_portal_berita',
            $portalJenis ? (int) $portalJenis->tarif_dasar : 750000
        );

        $settings = [
            'ppn_persen'                  => SistemConfig::getPpnPersen(),
            'jam_cutoff_iklan'            => substr(SistemConfig::getJamCutoff(), 0, 5),
            'is_past_cutoff'              => SistemConfig::isPastCutoff(),
            'tarif_per_baris'             => $tarifBaris,
            'minimal_baris'               => (int) SistemConfig::get('minimal_baris', $barisJenis?->min_order ?: 2),
            'maksimal_karakter_per_baris' => (int) SistemConfig::get('maksimal_karakter_per_baris', 32),
            'tarif_mmk_bw'                => $tarifMmkBw,
            'tarif_mmk_fc'                => $tarifMmkFc,
            'tarif_portal_berita'         => $tarifPortal,
            'diskon_agensi_default'       => (float) SistemConfig::get('diskon_agensi_default', 15),
        ];

        $paketSummary = [
            'koran'  => JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'KRN'))->where('is_active', true)->count(),
            'online' => JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'ONL'))->where('is_active', true)->count(),
            'tv'     => JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'PTV'))->where('is_active', true)->count(),
        ];

        $lastUpdated = SistemConfig::max('updated_at');

        return view('pages.iklan.pengaturan.index', compact('configs', 'settings', 'paketSummary', 'lastUpdated'));
    }

    /**
     * Simpan perubahan pengaturan tarif & pajak serta sinkronkan ke Master Jenis Iklan
     */
    public function update(Request $request)
    {
        $cleanCurrency = static function ($value): int {
            return (int) preg_replace('/[^0-9]/', '', (string) $value);
        };

        $jamCutoffRaw = trim((string) $request->input('jam_cutoff_iklan', '14:00'));
        if (strlen($jamCutoffRaw) > 5) {
            $jamCutoffRaw = substr($jamCutoffRaw, 0, 5);
        }

        $request->merge([
            'jam_cutoff_iklan'    => $jamCutoffRaw,
            'tarif_per_baris'     => $cleanCurrency($request->input('tarif_per_baris')),
            'tarif_mmk_bw'        => $cleanCurrency($request->input('tarif_mmk_bw')),
            'tarif_mmk_fc'        => $cleanCurrency($request->input('tarif_mmk_fc')),
            'tarif_portal_berita' => $cleanCurrency($request->input('tarif_portal_berita')),
        ]);

        $validated = $request->validate([
            'ppn_persen'                  => 'required|numeric|min:0|max:100',
            'jam_cutoff_iklan'            => 'required|date_format:H:i',
            'tarif_per_baris'             => 'required|integer|min:1000',
            'minimal_baris'               => 'required|integer|min:1|max:50',
            'maksimal_karakter_per_baris' => 'required|integer|min:10|max:200',
            'tarif_mmk_bw'                => 'required|integer|min:1000',
            'tarif_mmk_fc'                => 'required|integer|min:1000',
            'tarif_portal_berita'         => 'required|integer|min:1000',
            'diskon_agensi_default'       => 'required|numeric|min:0|max:100',
        ]);

        // Pastikan parameter meterai lama dihapus
        SistemConfig::whereIn('key', ['materai_nominal', 'materai_threshold'])->delete();

        $descriptions = [
            'ppn_persen'                  => 'Persentase pajak PPN transaksi iklan',
            'jam_cutoff_iklan'            => 'Batas waktu harian penerimaan iklan koran edisi besok',
            'tarif_per_baris'             => 'Tarif dasar iklan baris per baris setiap muat',
            'minimal_baris'               => 'Jumlah baris minimal pemesanan iklan baris',
            'maksimal_karakter_per_baris' => 'Batas karakter teks per baris cetak iklan baris',
            'tarif_mmk_bw'                => 'Tarif iklan display hitam putih (BW) per mmk',
            'tarif_mmk_fc'                => 'Tarif iklan display berwarna (FC) per mmk',
            'tarif_portal_berita'         => 'Tarif publikasi berita per portal web Online & Priangan TV',
            'diskon_agensi_default'       => 'Persentase diskon standar biro iklan / agensi',
        ];

        foreach ($validated as $key => $val) {
            SistemConfig::set($key, $val, $descriptions[$key] ?? null);
        }

        // Sinkronisasi langsung ke Master Jenis Iklan agar form transaksi selalu selaras
        JenisIklan::where('tipe_dasar', 'BARIS')->update([
            'tarif_dasar' => (int) $validated['tarif_per_baris'],
            'min_order'   => (int) $validated['minimal_baris'],
        ]);

        JenisIklan::where('cara_hitung', 'PER_MMK')
            ->where('mode_warna', 'BW')
            ->update(['tarif_dasar' => (int) $validated['tarif_mmk_bw']]);

        JenisIklan::where('cara_hitung', 'PER_MMK')
            ->where('mode_warna', 'FC')
            ->update(['tarif_dasar' => (int) $validated['tarif_mmk_fc']]);

        JenisIklan::where('kode_jenis', 'ONL003')
            ->update(['tarif_dasar' => (int) $validated['tarif_portal_berita']]);

        return redirect()->route('iklan.pengaturan.index')
            ->with('success', 'Pengaturan tarif dan pajak berhasil disimpan.');
    }
}
