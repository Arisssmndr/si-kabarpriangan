<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    /**
     * Tampilkan daftar master pelanggan (CRM)
     */
    public function index(Request $request)
    {
        $query = Pelanggan::withCount('transaksiKoran')->latest();

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('kode_pelanggan', 'like', "%{$keyword}%")
                  ->orWhere('nama_instansi', 'like', "%{$keyword}%")
                  ->orWhere('kontak_pic', 'like', "%{$keyword}%")
                  ->orWhere('no_telp', 'like', "%{$keyword}%")
                  ->orWhere('npwp_nik', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('jenis_klien')) {
            $query->where('jenis_klien', $request->jenis_klien);
        }

        $pelanggan = $query->paginate(15)->withQueryString();

        return view('pages.iklan.master.pelanggan.index', compact('pelanggan'));
    }

    /**
     * Form tambah pelanggan baru
     */
    public function create()
    {
        $nextCode = Pelanggan::createCode();
        return view('pages.iklan.master.pelanggan.create', compact('nextCode'));
    }

    /**
     * Simpan pelanggan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'jenis_klien'   => 'required|in:Perorangan,Swasta,Pemda,BUMN,Agensi',
            'npwp_nik'      => 'nullable|string|max:50',
            'alamat'        => 'nullable|string',
            'kontak_pic'    => 'nullable|string|max:100',
            'no_telp'       => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:100',
            'credit_limit'  => 'nullable|string',
        ]);

        $creditLimit = (int) str_replace(['.', ','], '', $request->credit_limit ?? 0);

        $pelanggan = Pelanggan::create([
            'kode_pelanggan' => Pelanggan::createCode(),
            'nama_instansi'  => $request->nama_instansi,
            'jenis_klien'    => $request->jenis_klien,
            'npwp_nik'       => $request->npwp_nik,
            'alamat'         => $request->alamat,
            'kontak_pic'     => $request->kontak_pic,
            'no_telp'        => $request->no_telp,
            'email'          => $request->email,
            'credit_limit'   => $creditLimit,
            'is_active'      => true,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pelanggan berhasil ditambahkan!',
                'data' => $pelanggan,
            ]);
        }

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan baru berhasil ditambahkan!');
    }

    /**
     * Form edit pelanggan
     */
    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('pages.iklan.master.pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Update data pelanggan
     */
    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'jenis_klien'   => 'required|in:Perorangan,Swasta,Pemda,BUMN,Agensi',
            'npwp_nik'      => 'nullable|string|max:50',
            'alamat'        => 'nullable|string',
            'kontak_pic'    => 'nullable|string|max:100',
            'no_telp'       => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:100',
            'credit_limit'  => 'nullable|string',
            'is_active'     => 'nullable|boolean',
        ]);

        $creditLimit = (int) str_replace(['.', ','], '', $request->credit_limit ?? 0);

        $pelanggan->update([
            'nama_instansi' => $request->nama_instansi,
            'jenis_klien'   => $request->jenis_klien,
            'npwp_nik'      => $request->npwp_nik,
            'alamat'        => $request->alamat,
            'kontak_pic'    => $request->kontak_pic,
            'no_telp'       => $request->no_telp,
            'email'         => $request->email,
            'credit_limit'  => $creditLimit,
            'is_active'     => $request->boolean('is_active', true),
        ]);

        return redirect()->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    /**
     * Hapus pelanggan
     */
    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        if ($pelanggan->transaksiKoran()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pelanggan tidak dapat dihapus karena memiliki riwayat transaksi!',
            ], 422);
        }

        $pelanggan->delete();
        return response()->json(['status' => 'success', 'message' => 'Pelanggan berhasil dihapus']);
    }

    /**
     * Endpoint API pencarian / autocomplete pelanggan untuk form FO
     */
    public function search(Request $request)
    {
        $keyword = $request->get('q', '');
        $kliens = Pelanggan::where('is_active', true)
            ->where(function ($q) use ($keyword) {
                $q->where('nama_instansi', 'like', "%{$keyword}%")
                  ->orWhere('kode_pelanggan', 'like', "%{$keyword}%")
                  ->orWhere('kontak_pic', 'like', "%{$keyword}%")
                  ->orWhere('no_telp', 'like', "%{$keyword}%");
            })
            ->limit(10)
            ->get();

        return response()->json($kliens);
    }
}
