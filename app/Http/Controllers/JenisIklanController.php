<?php

namespace App\Http\Controllers;

use App\Models\JenisIklan;
use App\Models\KategoriMedia;
use Illuminate\Http\Request;

class JenisIklanController extends Controller
{
    /**
     * Tampilkan daftar Jenis / Paket Iklan
     */
    public function index(Request $request)
    {
        $kategoriList = KategoriMedia::where('is_active', true)->orderBy('nama')->get();

        $query = JenisIklan::with('kategoriMedia')->latest('id');

        // Filter Kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_media_id', $request->kategori_id);
        }

        // Pencarian Nama / Kode
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_jenis', 'like', "%{$keyword}%")
                  ->orWhere('kode_jenis', 'like', "%{$keyword}%")
                  ->orWhere('keterangan', 'like', "%{$keyword}%");
            });
        }

        $jenisList = $query->paginate(10)->withQueryString();

        $stats = [
            'total'  => JenisIklan::count(),
            'koran'  => JenisIklan::whereHas('kategoriMedia', fn ($q) => $q->where('kode', 'like', '%KORAN%'))->count(),
            'online' => JenisIklan::whereHas('kategoriMedia', fn ($q) => $q->where('kode', 'like', '%ONLINE%'))->count(),
            'tv'     => JenisIklan::whereHas('kategoriMedia', fn ($q) => $q->where('kode', 'like', '%TV%'))->count(),
        ];

        return view('pages.iklan.master.jenis_iklan.index', compact('jenisList', 'kategoriList', 'stats'));
    }

    /**
     * Endpoint API AJAX untuk mendapatkan rekomendasi kode jenis iklan berikutnya
     */
    public function getNextCode($kategoriId)
    {
        $kategori = KategoriMedia::findOrFail($kategoriId);
        $nextCode = $kategori->generateNextJenisCode();

        return response()->json([
            'status' => 'success',
            'code'   => $nextCode,
        ]);
    }

    /**
     * Simpan Jenis Iklan Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori_media_id' => 'required|exists:kategori_media,id',
            'kode_jenis'        => 'nullable|string|max:30',
            'nama_jenis'        => 'required|string|max:150',
            'tipe_dasar'        => 'nullable|in:BARIS,KOLOM,DISPLAY,ADVERTORIAL,KHUSUS,BONUS',
            'mode_warna'        => 'nullable|in:BW,FC',
            'cara_hitung'       => 'nullable|in:PAKET_FIXED,PER_MMK,PER_BARIS,FIXED_ZERO',
            'tarif_dasar'       => 'nullable',
            'is_fixed_size'     => 'nullable|boolean',
            'default_kolom'     => 'nullable|integer|min:1|max:7',
            'default_tinggi_mm' => 'nullable|integer|min:1|max:540',
            'keterangan'        => 'nullable|string',
        ]);

        $kategori = KategoriMedia::findOrFail($request->kategori_media_id);

        // Kode dibuat otomatis dari database jika tidak diisi atau jika sudah ada duplikat
        $kode = $request->filled('kode_jenis') 
            ? strtoupper(trim($request->kode_jenis)) 
            : $kategori->generateNextJenisCode();

        if (JenisIklan::where('kode_jenis', $kode)->exists()) {
            $kode = $kategori->generateNextJenisCode();
        }

        $tarifClean = (int) str_replace(['.', ',', ' '], '', $request->tarif_dasar ?? 0);

        JenisIklan::create([
            'kategori_media_id' => $kategori->id,
            'kode_jenis'        => $kode,
            'nama_jenis'        => $request->nama_jenis,
            'tipe_dasar'        => $request->tipe_dasar ?? 'KOLOM',
            'mode_warna'        => $request->mode_warna ?? 'BW',
            'cara_hitung'       => $request->cara_hitung ?? 'PAKET_FIXED',
            'tarif_dasar'       => $tarifClean,
            'is_fixed_size'     => $request->has('is_fixed_size') ? (bool) $request->is_fixed_size : true,
            'default_kolom'     => $request->default_kolom ?: 1,
            'default_tinggi_mm' => $request->default_tinggi_mm ?: 50,
            'keterangan'        => $request->keterangan,
            'is_active'         => true,
        ]);

        return redirect()->route('jenis-iklan.index')
            ->with('success', "Jenis Iklan '{$request->nama_jenis}' ({$kode}) berhasil ditambahkan!");
    }

    /**
     * Update Jenis Iklan
     */
    public function update(Request $request, $id)
    {
        $jenis = JenisIklan::findOrFail($id);

        $request->validate([
            'kategori_media_id' => 'required|exists:kategori_media,id',
            'kode_jenis'        => 'nullable|string|max:30|unique:jenis_iklan,kode_jenis,' . $jenis->id,
            'nama_jenis'        => 'required|string|max:150',
            'tipe_dasar'        => 'nullable|in:BARIS,KOLOM,DISPLAY,ADVERTORIAL,KHUSUS,BONUS',
            'mode_warna'        => 'nullable|in:BW,FC',
            'cara_hitung'       => 'nullable|in:PAKET_FIXED,PER_MMK,PER_BARIS,FIXED_ZERO',
            'tarif_dasar'       => 'nullable',
            'is_fixed_size'     => 'nullable|boolean',
            'default_kolom'     => 'nullable|integer|min:1|max:7',
            'default_tinggi_mm' => 'nullable|integer|min:1|max:540',
            'keterangan'        => 'nullable|string',
            'is_active'         => 'nullable|boolean',
        ]);

        $kode = $request->filled('kode_jenis') 
            ? strtoupper(trim($request->kode_jenis)) 
            : $jenis->kode_jenis;

        $tarifClean = $request->filled('tarif_dasar')
            ? (int) str_replace(['.', ',', ' '], '', $request->tarif_dasar)
            : $jenis->tarif_dasar;

        $jenis->update([
            'kategori_media_id' => $request->kategori_media_id,
            'kode_jenis'        => $kode,
            'nama_jenis'        => $request->nama_jenis,
            'tipe_dasar'        => $request->tipe_dasar ?? $jenis->tipe_dasar ?? 'KOLOM',
            'mode_warna'        => $request->mode_warna ?? $jenis->mode_warna ?? 'BW',
            'cara_hitung'       => $request->cara_hitung ?? $jenis->cara_hitung ?? 'PAKET_FIXED',
            'tarif_dasar'       => $tarifClean,
            'is_fixed_size'     => $request->has('is_fixed_size') ? (bool) $request->is_fixed_size : $jenis->is_fixed_size,
            'default_kolom'     => $request->default_kolom ?? $jenis->default_kolom,
            'default_tinggi_mm' => $request->default_tinggi_mm ?? $jenis->default_tinggi_mm,
            'keterangan'        => $request->keterangan,
            'is_active'         => $request->has('is_active') ? (bool)$request->is_active : $jenis->is_active,
        ]);

        return redirect()->route('jenis-iklan.index')
            ->with('success', "Jenis Iklan '{$jenis->nama_jenis}' berhasil diperbarui!");
    }

    /**
     * Hapus Jenis Iklan
     */
    public function destroy($id)
    {
        $jenis = JenisIklan::findOrFail($id);
        $nama = $jenis->nama_jenis;
        $jenis->delete();

        return response()->json([
            'status'  => 'success',
            'message' => "Jenis Iklan '{$nama}' berhasil dihapus.",
        ]);
    }
}
