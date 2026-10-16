<?php

namespace App\Http\Controllers;

use App\Models\SalesAe;
use Illuminate\Http\Request;

class SalesAeController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesAe::with('user')->orderBy('jenis_kode')->orderBy('kode_ae');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('kode_ae', 'like', "%{$q}%")
                   ->orWhere('nama_ae', 'like', "%{$q}%");
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_kode', $request->jenis);
        }

        $salesAes = $query->paginate(20)->withQueryString();

        $stats = [
            'total'       => SalesAe::count(),
            'aktif'       => SalesAe::where('is_active', true)->count(),
            'struktural'  => SalesAe::where('jenis_kode', 'STRUKTURAL')->where('is_active', true)->count(),
            'koresponden' => SalesAe::where('jenis_kode', 'KORESPONDEN')->where('is_active', true)->count(),
        ];

        return view('pages.master.sales-ae.index', compact('salesAes', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_ae'                => 'required|string|max:20|unique:sales_ae,kode_ae',
            'nama_ae'                => 'required|string|max:150',
            'jenis_kode'             => 'required|in:STRUKTURAL,KORESPONDEN,FO',
            'tarif_insentif_default' => 'required|numeric|min:0|max:100',
            'tarif_komisi_max'       => 'required|numeric|min:0|max:100',
        ], [
            'kode_ae.unique' => 'Kode AE sudah digunakan oleh sales lain.',
        ]);

        $salesAe = SalesAe::create([
            'kode_ae'                => strtoupper(trim($request->kode_ae)),
            'nama_ae'                => $request->nama_ae,
            'jenis_kode'             => $request->jenis_kode,
            'tarif_insentif_default' => $request->tarif_insentif_default,
            'tarif_komisi_max'       => $request->tarif_komisi_max,
            'is_active'              => true,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Sales/AE [{$salesAe->kode_ae}] {$salesAe->nama_ae} berhasil ditambahkan.",
                'data'    => $salesAe,
            ]);
        }

        return redirect()->route('sales-ae.index')
            ->with('success', "Sales/AE [{$request->kode_ae}] {$request->nama_ae} berhasil ditambahkan.");
    }

    public function update(Request $request, SalesAe $salesAe)
    {
        $request->validate([
            'kode_ae'                => 'required|string|max:20|unique:sales_ae,kode_ae,' . $salesAe->id,
            'nama_ae'                => 'required|string|max:150',
            'jenis_kode'             => 'required|in:STRUKTURAL,KORESPONDEN,FO',
            'tarif_insentif_default' => 'required|numeric|min:0|max:100',
            'tarif_komisi_max'       => 'required|numeric|min:0|max:100',
        ], [
            'kode_ae.unique' => 'Kode AE sudah digunakan oleh sales lain.',
        ]);

        $salesAe->update([
            'kode_ae'                => strtoupper(trim($request->kode_ae)),
            'nama_ae'                => $request->nama_ae,
            'jenis_kode'             => $request->jenis_kode,
            'tarif_insentif_default' => $request->tarif_insentif_default,
            'tarif_komisi_max'       => $request->tarif_komisi_max,
        ]);

        return redirect()->route('sales-ae.index')
            ->with('success', "Data Sales/AE [{$salesAe->kode_ae}] berhasil diperbarui.");
    }

    public function destroy(SalesAe $salesAe)
    {
        if ($salesAe->transaksis()->exists()) {
            return redirect()->route('sales-ae.index')
                ->with('error', "Sales/AE [{$salesAe->kode_ae}] tidak dapat dihapus karena sudah memiliki transaksi terkait. Nonaktifkan saja.");
        }

        $nama = "[{$salesAe->kode_ae}] {$salesAe->nama_ae}";
        $salesAe->delete();

        return redirect()->route('sales-ae.index')
            ->with('success', "Sales/AE {$nama} berhasil dihapus.");
    }

    /**
     * Toggle status aktif/nonaktif
     */
    public function toggleStatus(SalesAe $salesAe)
    {
        $salesAe->update(['is_active' => !$salesAe->is_active]);

        $status = $salesAe->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Sales/AE [{$salesAe->kode_ae}] berhasil {$status}.");
    }

    /**
     * Auto-generate kode AE berdasarkan jenis
     */
    public function generateKode(Request $request)
    {
        $jenis = $request->jenis ?? 'STRUKTURAL';
        $prefix = match ($jenis) {
            'STRUKTURAL'  => 'S',
            'KORESPONDEN' => 'K',
            default       => 'FO',
        };

        $last = SalesAe::where('kode_ae', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(kode_ae, 2) AS UNSIGNED) DESC')
            ->first();

        $nextNum = $last
            ? ((int) substr($last->kode_ae, strlen($prefix))) + 1
            : 1;

        return response()->json(['kode' => $prefix . str_pad($nextNum, 2, '0', STR_PAD_LEFT)]);
    }

    // Stub methods required by resource controller
    public function create() { abort(404); }
    public function show(SalesAe $salesAe) { abort(404); }
    public function edit(SalesAe $salesAe) { abort(404); }
}
