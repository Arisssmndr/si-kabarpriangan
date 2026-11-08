<?php

namespace App\Http\Controllers;

use App\Models\KopKota;
use Illuminate\Http\Request;

class KopKotaController extends Controller
{
    public function index(Request $request)
    {
        $query = KopKota::orderBy('nomor_halaman')->orderBy('kode');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('nama_penempatan', 'like', "%{$q}%")
                   ->orWhere('kode', 'like', "%{$q}%")
                   ->orWhere('wilayah_biro', 'like', "%{$q}%");
            });
        }

        $kopKotas = $query->paginate(20)->withQueryString();

        return view('pages.iklan.master.kop_kota.index', compact('kopKotas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'             => 'required|string|max:30|unique:kop_kota,kode',
            'nomor_halaman'    => 'required|integer|min:1|max:64',
            'nama_penempatan'  => 'required|string|max:100',
            'wilayah_biro'     => 'required|string|max:100',
            'surcharge_persen' => 'nullable|numeric|min:0|max:100',
        ]);

        KopKota::create([
            'kode'             => strtoupper(trim($request->kode)),
            'nomor_halaman'    => $request->nomor_halaman,
            'nama_penempatan'  => $request->nama_penempatan,
            'wilayah_biro'     => $request->wilayah_biro,
            'surcharge_persen' => $request->surcharge_persen ?? 0,
            'is_active'        => true,
        ]);

        return redirect()->route('kop-kota.index')
            ->with('success', "Master Penempatan Halaman [{$request->kode}] berhasil ditambahkan.");
    }

    public function update(Request $request, KopKota $kopKotum)
    {
        $request->validate([
            'nomor_halaman'    => 'required|integer|min:1|max:64',
            'nama_penempatan'  => 'required|string|max:100',
            'wilayah_biro'     => 'required|string|max:100',
            'surcharge_persen' => 'nullable|numeric|min:0|max:100',
        ]);

        $kopKotum->update([
            'nomor_halaman'    => $request->nomor_halaman,
            'nama_penempatan'  => $request->nama_penempatan,
            'wilayah_biro'     => $request->wilayah_biro,
            'surcharge_persen' => $request->surcharge_persen ?? 0,
        ]);

        return redirect()->route('kop-kota.index')
            ->with('success', "Data Halaman [{$kopKotum->kode}] berhasil diperbarui.");
    }

    public function toggleStatus(KopKota $kopKotum)
    {
        $kopKotum->update(['is_active' => !$kopKotum->is_active]);

        $status = $kopKotum->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('kop-kota.index')
            ->with('success', "Halaman [{$kopKotum->kode}] berhasil {$status}.");
    }

    public function destroy(KopKota $kopKotum)
    {
        $kopKotum->update(['is_active' => false]);

        return redirect()->route('kop-kota.index')
            ->with('success', "Halaman [{$kopKotum->kode}] dinonaktifkan.");
    }
}
