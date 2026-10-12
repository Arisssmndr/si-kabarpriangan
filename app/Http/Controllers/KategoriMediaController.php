<?php

namespace App\Http\Controllers;

use App\Models\KategoriMedia;
use Illuminate\Http\Request;

class KategoriMediaController extends Controller
{
    /**
     * Tampilkan daftar Kategori Media
     */
    public function index(Request $request)
    {
        $query = KategoriMedia::withCount('jenisIklan');

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                  ->orWhere('kode', 'like', "%{$keyword}%")
                  ->orWhere('deskripsi', 'like', "%{$keyword}%");
            });
        }

        // Filter berdasarkan ketersediaan paket iklan
        if ($request->filled('filter')) {
            if ($request->filter === 'with_paket') {
                $query->has('jenisIklan');
            } elseif ($request->filter === 'without_paket') {
                $query->doesntHave('jenisIklan');
            }
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        if ($sort === 'nama_asc') {
            $query->orderBy('nama', 'asc');
        } elseif ($sort === 'kode_asc') {
            $query->orderBy('kode', 'asc');
        } elseif ($sort === 'paket_desc') {
            $query->orderBy('jenis_iklan_count', 'desc');
        } else {
            $query->latest('id');
        }

        $kategoriList = $query->paginate(10)->withQueryString();

        return view('pages.iklan.master.kategori_media.index', compact('kategoriList'));
    }

    /**
     * Simpan Kategori Media baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode'      => 'required|string|max:10|unique:kategori_media,kode',
            'nama'      => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ], [
            'kode.unique' => 'Kode kategori sudah digunakan, silakan gunakan kode lain.',
        ]);

        $validated['kode'] = strtoupper(trim($validated['kode']));
        $validated['is_active'] = true;

        KategoriMedia::create($validated);

        return redirect()->route('kategori-media.index')
            ->with('success', "Kategori Media '{$validated['nama']}' berhasil ditambahkan!");
    }

    /**
     * Update Kategori Media
     */
    public function update(Request $request, $id)
    {
        $kategori = KategoriMedia::findOrFail($id);

        $validated = $request->validate([
            'kode'      => 'required|string|max:10|unique:kategori_media,kode,' . $kategori->id,
            'nama'      => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['kode'] = strtoupper(trim($validated['kode']));
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : $kategori->is_active;

        $kategori->update($validated);

        return redirect()->route('kategori-media.index')
            ->with('success', "Kategori Media '{$kategori->nama}' berhasil diperbarui!");
    }

    /**
     * Hapus Kategori Media
     */
    public function destroy($id)
    {
        $kategori = KategoriMedia::withCount('jenisIklan')->findOrFail($id);

        if ($kategori->jenis_iklan_count > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => "Kategori '{$kategori->nama}' memiliki {$kategori->jenis_iklan_count} jenis iklan terhubung. Hapus atau pindahkan jenis iklan terlebih dahulu.",
            ], 422);
        }

        $nama = $kategori->nama;
        $kategori->delete();

        return response()->json([
            'status'  => 'success',
            'message' => "Kategori Media '{$nama}' berhasil dihapus.",
        ]);
    }
}
