<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Models\Agen;
use App\Models\Rute;
use Illuminate\Http\Request;

class RuteController extends Controller
{
    /**
     * Tabel Master Rute / Jurusan Distribusi Armada Sirkulasi.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status', 'all');

        $query = Rute::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama_rute', 'like', "%{$search}%")
                  ->orWhere('armada', 'like', "%{$search}%")
                  ->orWhere('sopir', 'like', "%{$search}%")
                  ->orWhere('titik_tujuan', 'like', "%{$search}%");
            });
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $ruteList = $query->orderBy('kode')->get();
        $totalOplah = $ruteList->sum('alokasi_oplah');
        $totalRute = $ruteList->count();

        return view('pages.sirkulasi.tabel.rute', compact('ruteList', 'totalOplah', 'totalRute', 'search', 'statusFilter'));
    }

    /**
     * Simpan Master Rute Baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode'          => 'required|string|max:10|unique:rutes,kode',
            'nama_rute'     => 'required|string|max:150',
            'armada'        => 'nullable|string|max:150',
            'sopir'         => 'nullable|string|max:150',
            'jam_berangkat' => 'nullable|string|max:50',
            'titik_tujuan'  => 'nullable|string',
            'alokasi_oplah' => 'nullable|integer|min:0',
        ]);

        Rute::create([
            'kode'          => strtoupper(trim($request->kode)),
            'nama_rute'     => $request->nama_rute,
            'armada'        => $request->armada ?: '-',
            'sopir'         => $request->sopir ?: '-',
            'jam_berangkat' => $request->jam_berangkat ?: '02:30 WIB',
            'titik_tujuan'  => $request->titik_tujuan ?: '-',
            'alokasi_oplah' => (int) $request->input('alokasi_oplah', 0),
            'status'        => 'aktif',
        ]);

        return redirect()->route('sirkulasi.tabel.rute')->with('success', "Rute baru {$request->kode} - {$request->nama_rute} berhasil ditambahkan.");
    }

    /**
     * Update Master Rute.
     */
    public function update(Request $request, $id)
    {
        $rute = Rute::findOrFail($id);

        $request->validate([
            'nama_rute'     => 'required|string|max:150',
            'armada'        => 'nullable|string|max:150',
            'sopir'         => 'nullable|string|max:150',
            'jam_berangkat' => 'nullable|string|max:50',
            'titik_tujuan'  => 'nullable|string',
            'alokasi_oplah' => 'nullable|integer|min:0',
        ]);

        $rute->update([
            'nama_rute'     => $request->nama_rute,
            'armada'        => $request->armada ?: $rute->armada,
            'sopir'         => $request->sopir ?: $rute->sopir,
            'jam_berangkat' => $request->jam_berangkat ?: $rute->jam_berangkat,
            'titik_tujuan'  => $request->titik_tujuan ?: $rute->titik_tujuan,
            'alokasi_oplah' => (int) $request->input('alokasi_oplah', $rute->alokasi_oplah),
        ]);

        return redirect()->route('sirkulasi.tabel.rute')->with('success', "Data rute {$rute->kode} - {$rute->nama_rute} berhasil diperbarui.");
    }

    /**
     * Hapus Master Rute.
     */
    public function destroy($id)
    {
        $rute = Rute::findOrFail($id);
        $kode = $rute->kode;
        $nama = $rute->nama_rute;

        if (Agen::where('rute_kode', $kode)->exists()) {
            return redirect()->route('sirkulasi.tabel.rute')->with('error', "Gagal menghapus: Rute {$kode} masih digunakan oleh data agen.");
        }

        $rute->delete();

        return redirect()->route('sirkulasi.tabel.rute')->with('success', "Rute {$kode} - {$nama} berhasil dihapus.");
    }

    /**
     * Toggle Status Master Rute.
     */
    public function toggleStatus($id)
    {
        $rute = Rute::findOrFail($id);
        $rute->status = $rute->status === 'aktif' ? 'nonaktif' : 'aktif';
        $rute->save();

        return redirect()->route('sirkulasi.tabel.rute')->with('success', "Status rute {$rute->kode} berhasil diubah menjadi " . strtoupper($rute->status) . ".");
    }
}
