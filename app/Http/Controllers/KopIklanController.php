<?php

namespace App\Http\Controllers;

use App\Models\KopIklan;
use Illuminate\Http\Request;

class KopIklanController extends Controller
{
    public function index(Request $request)
    {
        $query = KopIklan::orderBy('urutan')->orderBy('kode');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('nama_kop', 'like', "%{$q}%")
                   ->orWhere('kode', 'like', "%{$q}%");
            });
        }

        $kopIklans = $query->paginate(20)->withQueryString();

        return view('pages.iklan.master.kop_iklan.index', compact('kopIklans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'      => 'required|string|max:30|unique:kop_iklan,kode',
            'nama_kop'  => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255',
            'urutan'    => 'nullable|integer|min:1',
        ]);

        KopIklan::create([
            'kode'      => strtoupper(trim($request->kode)),
            'nama_kop'  => $request->nama_kop,
            'deskripsi' => $request->deskripsi,
            'urutan'    => $request->urutan ?? 1,
            'is_active' => true,
        ]);

        return redirect()->route('kop-iklan.index')
            ->with('success', "Rubrik Iklan Baris [{$request->kode}] berhasil ditambahkan.");
    }

    public function update(Request $request, KopIklan $kopIklan)
    {
        $request->validate([
            'nama_kop'  => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255',
            'urutan'    => 'nullable|integer|min:1',
        ]);

        $kopIklan->update([
            'nama_kop'  => $request->nama_kop,
            'deskripsi' => $request->deskripsi,
            'urutan'    => $request->urutan ?? 1,
        ]);

        return redirect()->route('kop-iklan.index')
            ->with('success', "Data Rubrik [{$kopIklan->kode}] berhasil diperbarui.");
    }

    public function toggleStatus(KopIklan $kopIklan)
    {
        $kopIklan->update(['is_active' => !$kopIklan->is_active]);

        $status = $kopIklan->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('kop-iklan.index')
            ->with('success', "Rubrik [{$kopIklan->kode}] berhasil {$status}.");
    }

    public function destroy(KopIklan $kopIklan)
    {
        $kopIklan->update(['is_active' => false]);

        return redirect()->route('kop-iklan.index')
            ->with('success', "Rubrik [{$kopIklan->kode}] dinonaktifkan.");
    }
}
