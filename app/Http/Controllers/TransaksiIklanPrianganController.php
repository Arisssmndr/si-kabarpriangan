<?php

namespace App\Http\Controllers;

use App\Models\JenisIklan;
use App\Models\TransaksiPriangan;
use Illuminate\Http\Request;

class TransaksiIklanPrianganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TransaksiPriangan::with('iklanpriangan')->latest();

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nofakturpriangan', 'like', "%{$keyword}%")
                  ->orWhere('nama_pemasangpriangan', 'like', "%{$keyword}%")
                  ->orWhere('sales_iklanpriangan', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'lunas') {
                $query->where('piutang_transaksipriangan', '<=', 0);
            } elseif ($request->status === 'piutang') {
                $query->where('piutang_transaksipriangan', '>', 0);
            }
        }

        $transaksipriangan = $query->paginate(10)->withQueryString();
        $iklanpriangan = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'PTV'))->get();

        return view('pages.iklan.transaksi.priangan.index', compact('transaksipriangan', 'iklanpriangan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $iklanpriangan = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'PTV'))->get();
        $nofakturpriangan = TransaksiPriangan::createCode();
        return view('pages.iklan.transaksi.priangan.create', compact('nofakturpriangan', 'iklanpriangan'));
    }

    public function cetak($id)
    {
        // 1. Ambil data transaksi berdasarkan ID
        // Gunakan 'with' untuk mengambil data relasi jenis iklan juga
        $transaksi = TransaksiPriangan::with('iklanpriangan')->findOrFail($id);

        // 2. Tampilkan view cetak
        // Pastikan path view sesuai dengan struktur folder Anda
        return view('pages.iklan.transaksi.priangan.cetak', compact('transaksi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nofakturpriangan'          => 'required',
            'tanggal_transaksipriangan' => 'required|date',
            'nama_pemasangpriangan'     => 'required',
            'alamat_pemasangpriangan'   => 'required',
            'id_iklanpriangan'          => 'required',
            'sales_iklanpriangan'       => 'required',
            'tanggal_muatiklanpriangan' => 'required|date',
            'harga_transaksipriangan'   => 'required',
            'jumlahbayar_transaksipriangan' => 'required',
        ]);

        // Bersihkan angka
        $harga_bersih    = (int) str_replace(['.', ','], '', $request->harga_transaksipriangan);
        $diskon_bersih   = (int) str_replace(['.', ','], '', $request->diskon_transaksipriangan ?? 0);
        $dibayar_bersih  = (int) str_replace(['.', ','], '', $request->jumlahbayar_transaksipriangan ?? 0);
        $qty_bersih      = max(1, (int) ($request->total_muatiklanpriangan ?? 1));

        // Kalkulasi Keuangan Server-Side
        $total_omset    = $harga_bersih * $qty_bersih;
        $total_tagihan  = max(0, $total_omset - $diskon_bersih);
        $dpp            = (int) round($total_tagihan / 1.11);
        $ppn            = $total_tagihan - $dpp;
        $piutang_bersih = max(0, $total_tagihan - $dibayar_bersih);

        $komisi_input   = $request->filled('komisi_transaksipriangan') ? (int) str_replace(['.', ','], '', $request->komisi_transaksipriangan) : 0;
        $insentif_input = $request->filled('insentif_transaksipriangan') ? (int) str_replace(['.', ','], '', $request->insentif_transaksipriangan) : 0;

        $komisi         = $komisi_input > 0 ? $komisi_input : (int) round($dpp * 0.20);
        $insentif       = $insentif_input > 0 ? $insentif_input : (int) round(($dpp - $komisi) * 0.20);

        TransaksiPriangan::create([
            'nofakturpriangan'              => $request->nofakturpriangan,
            'tanggal_transaksipriangan'     => $request->tanggal_transaksipriangan,
            'nama_pemasangpriangan'         => $request->nama_pemasangpriangan,
            'alamat_pemasangpriangan'       => $request->alamat_pemasangpriangan,
            'id_iklanpriangan'              => $request->id_iklanpriangan,
            'sales_iklanpriangan'           => $request->sales_iklanpriangan,
            'tanggal_muatiklanpriangan'     => $request->tanggal_muatiklanpriangan,
            'total_muatiklanpriangan'       => $qty_bersih,
            'harga_transaksipriangan'       => $harga_bersih,
            'diskon_transaksipriangan'      => $diskon_bersih,
            'insentif_transaksipriangan'    => $insentif,
            'komisi_transaksipriangan'      => $komisi,
            'ppn_transaksipriangan'         => $ppn,
            'totaltagihan_transaksipriangan'=> $total_tagihan,
            'jumlahbayar_transaksipriangan' => $dibayar_bersih,
            'piutang_transaksipriangan'     => $piutang_bersih,
        ]);

        return redirect()->route('transaksipriangan.index')
            ->with('success', 'Transaksi Iklan Priangan TV berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'tanggal_transaksipriangan'     => 'required|date',
            'nama_pemasangpriangan'         => 'required',
            'alamat_pemasangpriangan'       => 'required',
            'id_iklanpriangan'              => 'required',
            'sales_iklanpriangan'           => 'required',
            'tanggal_muatiklanpriangan'     => 'required|date',
            'harga_transaksipriangan'       => 'required',
            'jumlahbayar_transaksipriangan' => 'required',
        ]);

        $harga_bersih    = (int) str_replace(['.', ','], '', $request->harga_transaksipriangan);
        $diskon_bersih   = (int) str_replace(['.', ','], '', $request->diskon_transaksipriangan ?? 0);
        $dibayar_bersih  = (int) str_replace(['.', ','], '', $request->jumlahbayar_transaksipriangan ?? 0);
        $qty_bersih      = max(1, (int) ($request->total_muatiklanpriangan ?? 1));

        $total_omset    = $harga_bersih * $qty_bersih;
        $total_tagihan  = max(0, $total_omset - $diskon_bersih);
        $dpp            = (int) round($total_tagihan / 1.11);
        $ppn            = $total_tagihan - $dpp;
        $piutang_bersih = max(0, $total_tagihan - $dibayar_bersih);

        $komisi_input   = $request->filled('komisi_transaksipriangan') ? (int) str_replace(['.', ','], '', $request->komisi_transaksipriangan) : 0;
        $insentif_input = $request->filled('insentif_transaksipriangan') ? (int) str_replace(['.', ','], '', $request->insentif_transaksipriangan) : 0;

        $transaksi = TransaksiPriangan::findOrFail($id);

        $komisi   = $komisi_input > 0 ? $komisi_input : ($transaksi->komisi_transaksipriangan ?: (int) round($dpp * 0.20));
        $insentif = $insentif_input > 0 ? $insentif_input : ($transaksi->insentif_transaksipriangan ?: (int) round(($dpp - $komisi) * 0.20));

        $transaksi->update([
            'tanggal_transaksipriangan'     => $request->tanggal_transaksipriangan,
            'nama_pemasangpriangan'         => $request->nama_pemasangpriangan,
            'alamat_pemasangpriangan'       => $request->alamat_pemasangpriangan,
            'id_iklanpriangan'              => $request->id_iklanpriangan,
            'sales_iklanpriangan'           => $request->sales_iklanpriangan,
            'tanggal_muatiklanpriangan'     => $request->tanggal_muatiklanpriangan,
            'total_muatiklanpriangan'       => $qty_bersih,
            'harga_transaksipriangan'       => $harga_bersih,
            'diskon_transaksipriangan'      => $diskon_bersih,
            'insentif_transaksipriangan'    => $insentif,
            'komisi_transaksipriangan'      => $komisi,
            'ppn_transaksipriangan'         => $ppn,
            'totaltagihan_transaksipriangan'=> $total_tagihan,
            'jumlahbayar_transaksipriangan' => $dibayar_bersih,
            'piutang_transaksipriangan'     => $piutang_bersih,
        ]);

        return redirect()->route('transaksipriangan.index')
            ->with('success', 'Data Transaksi Priangan TV Berhasil Diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = TransaksiPriangan::findOrFail($id);
        $data->delete();
        return response()->json(['status' => 'success']);
    }
}
