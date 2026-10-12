<?php

namespace App\Http\Controllers;

use App\Models\JenisIklan;
use App\Models\TransaksiKoran;
use Illuminate\Http\Request;

class TransaksiKoranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TransaksiKoran::with('iklankoran')->latest();

        // Fitur Pencarian
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nofakturkoran', 'like', "%{$keyword}%")
                  ->orWhere('nama_pemasangkoran', 'like', "%{$keyword}%")
                  ->orWhere('sales_iklankoran', 'like', "%{$keyword}%");
            });
        }

        // Filter Status Pembayaran
        if ($request->filled('status')) {
            if ($request->status === 'lunas') {
                $query->where('piutang_transaksikoran', '<=', 0);
            } elseif ($request->status === 'piutang') {
                $query->where('piutang_transaksikoran', '>', 0);
            }
        }

        $transaksikoran = $query->paginate(10)->withQueryString();
        $iklankoran = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'KRN'))->get();

        return view('pages.iklan.transaksi.koran.index', compact('transaksikoran', 'iklankoran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $iklankoran = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'KRN'))->get();
        $nofakturkoran = TransaksiKoran::createCode();
        return view('pages.iklan.transaksi.koran.create', compact('nofakturkoran', 'iklankoran'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nofakturkoran'          => 'required',
            'tanggal_transaksikoran' => 'required|date',
            'nama_pemasangkoran'     => 'required|string',
            'alamat_pemasangkoran'   => 'required|string',
            'id_iklankoran'          => 'required|exists:iklankoran,id',
            'halaman_iklan'          => 'required|string',
            'warna_iklan'            => 'required|string',
            'ukuran_iklan'           => 'nullable|string',
            'tanggal_muatkoran'      => 'required|date',
            'sales_iklankoran'       => 'required|string',
            'total_muatkoran'        => 'required|numeric|min:1',
            'harga_transaksikoran'   => 'required',
            'diskon_transaksikoran'  => 'nullable',
            'jumlahbayar_transaksikoran' => 'required',
            'komisi_transaksikoran'  => 'nullable',
            'insentif_transaksikoran'=> 'nullable',
        ]);

        $harga_bersih   = (int) str_replace(['.', ','], '', $request->harga_transaksikoran);
        $diskon_bersih  = (int) str_replace(['.', ','], '', $request->diskon_transaksikoran ?? 0);
        $bayar_bersih   = (int) str_replace(['.', ','], '', $request->jumlahbayar_transaksikoran);
        $qty            = (int) ($request->total_muatkoran ?? 1);

        $total_omset    = $harga_bersih * $qty;
        $total_tagihan  = max(0, $total_omset - $diskon_bersih);
        $dpp            = (int) round($total_tagihan / 1.11);
        $ppn            = $total_tagihan - $dpp;
        $piutang        = max(0, $total_tagihan - $bayar_bersih);

        $komisi_input   = $request->filled('komisi_transaksikoran') ? (int) str_replace(['.', ','], '', $request->komisi_transaksikoran) : 0;
        $insentif_input = $request->filled('insentif_transaksikoran') ? (int) str_replace(['.', ','], '', $request->insentif_transaksikoran) : 0;

        $komisi         = $komisi_input > 0 ? $komisi_input : (int) round($dpp * 0.20);
        $insentif       = $insentif_input > 0 ? $insentif_input : (int) round($dpp * 0.20);

        TransaksiKoran::create([
            'nofakturkoran'          => $request->nofakturkoran,
            'tanggal_transaksikoran' => $request->tanggal_transaksikoran,
            'nama_pemasangkoran'     => $request->nama_pemasangkoran,
            'alamat_pemasangkoran'   => $request->alamat_pemasangkoran,
            'id_iklankoran'          => $request->id_iklankoran,
            'halaman_iklan'          => $request->halaman_iklan,
            'warna_iklan'            => $request->warna_iklan,
            'ukuran_iklan'           => $request->ukuran_iklan ?? '-',
            'tanggal_muatkoran'      => $request->tanggal_muatkoran,
            'sales_iklankoran'       => $request->sales_iklankoran,
            'total_muatkoran'        => $qty,
            'harga_transaksikoran'   => $harga_bersih,
            'diskon_transaksikoran'  => $diskon_bersih,
            'insentif_transaksikoran'=> $insentif,
            'komisi_transaksikoran'  => $komisi,
            'ppn_transaksikoran'     => $ppn,
            'totaltagihan_transaksikoran' => $total_tagihan,
            'jumlahbayar_transaksikoran'  => $bayar_bersih,
            'piutang_transaksikoran'      => $piutang,
        ]);

        return redirect()->route('transaksikoran.index')
            ->with('success', 'Transaksi Iklan Koran Berhasil Disimpan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaksi = TransaksiKoran::findOrFail($id);

        $request->validate([
            'tanggal_transaksikoran' => 'required|date',
            'nama_pemasangkoran'     => 'required|string',
            'alamat_pemasangkoran'   => 'required|string',
            'id_iklankoran'          => 'required|exists:iklankoran,id',
            'halaman_iklan'          => 'required|string',
            'warna_iklan'            => 'required|string',
            'ukuran_iklan'           => 'nullable|string',
            'tanggal_muatkoran'      => 'required|date',
            'sales_iklankoran'       => 'required|string',
            'total_muatkoran'        => 'required|numeric|min:1',
            'harga_transaksikoran'   => 'required',
            'diskon_transaksikoran'  => 'nullable',
            'jumlahbayar_transaksikoran' => 'required',
            'komisi_transaksikoran'  => 'nullable',
            'insentif_transaksikoran'=> 'nullable',
        ]);

        $harga_bersih   = (int) str_replace(['.', ','], '', $request->harga_transaksikoran);
        $diskon_bersih  = (int) str_replace(['.', ','], '', $request->diskon_transaksikoran ?? 0);
        $bayar_bersih   = (int) str_replace(['.', ','], '', $request->jumlahbayar_transaksikoran);
        $qty            = (int) ($request->total_muatkoran ?? 1);

        $total_omset    = $harga_bersih * $qty;
        $total_tagihan  = max(0, $total_omset - $diskon_bersih);
        $dpp            = (int) round($total_tagihan / 1.11);
        $ppn            = $total_tagihan - $dpp;
        $piutang        = max(0, $total_tagihan - $bayar_bersih);

        $komisi_input   = $request->filled('komisi_transaksikoran') ? (int) str_replace(['.', ','], '', $request->komisi_transaksikoran) : 0;
        $insentif_input = $request->filled('insentif_transaksikoran') ? (int) str_replace(['.', ','], '', $request->insentif_transaksikoran) : 0;

        $komisi         = $komisi_input > 0 ? $komisi_input : (int) round($dpp * 0.20);
        $insentif       = $insentif_input > 0 ? $insentif_input : (int) round($dpp * 0.20);

        $transaksi->update([
            'tanggal_transaksikoran' => $request->tanggal_transaksikoran,
            'nama_pemasangkoran'     => $request->nama_pemasangkoran,
            'alamat_pemasangkoran'   => $request->alamat_pemasangkoran,
            'id_iklankoran'          => $request->id_iklankoran,
            'halaman_iklan'          => $request->halaman_iklan,
            'warna_iklan'            => $request->warna_iklan,
            'ukuran_iklan'           => $request->ukuran_iklan ?? $transaksi->ukuran_iklan,
            'tanggal_muatkoran'      => $request->tanggal_muatkoran,
            'sales_iklankoran'       => $request->sales_iklankoran,
            'total_muatkoran'        => $qty,
            'harga_transaksikoran'   => $harga_bersih,
            'diskon_transaksikoran'  => $diskon_bersih,
            'insentif_transaksikoran'=> $insentif,
            'komisi_transaksikoran'  => $komisi,
            'ppn_transaksikoran'     => $ppn,
            'totaltagihan_transaksikoran' => $total_tagihan,
            'jumlahbayar_transaksikoran'  => $bayar_bersih,
            'piutang_transaksikoran'      => $piutang,
        ]);

        return redirect()->route('transaksikoran.index')
            ->with('success', 'Transaksi Iklan Koran Berhasil Diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $transaksi = TransaksiKoran::findOrFail($id);
        $transaksi->delete();

        return response()->json(['status' => 'success']);
    }

    /**
     * Cetak faktur transaksi koran.
     */
    public function cetak($id)
    {
        $transaksi = TransaksiKoran::with('iklankoran')->findOrFail($id);
        return view('pages.iklan.transaksi.koran.cetak', compact('transaksi'));
    }
}
