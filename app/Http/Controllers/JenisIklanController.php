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

        return view('pages.iklan.master.jenis_iklan.index', compact('jenisList', 'kategoriList'));
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

        JenisIklan::create([
            'kategori_media_id' => $kategori->id,
            'kode_jenis'        => $kode,
            'nama_jenis'        => $request->nama_jenis,
            'tarif_dasar'       => 0,
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
            'keterangan'        => 'nullable|string',
            'is_active'         => 'nullable|boolean',
        ]);

        $kode = $request->filled('kode_jenis') 
            ? strtoupper(trim($request->kode_jenis)) 
            : $jenis->kode_jenis;

        $jenis->update([
            'kategori_media_id' => $request->kategori_media_id,
            'kode_jenis'        => $kode,
            'nama_jenis'        => $request->nama_jenis,
            'tarif_dasar'       => 0,
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
