<?php

namespace App\Http\Controllers;

use App\Models\JenisIklan;
use App\Models\TransaksiOnline;
use Illuminate\Http\Request;

class TransaksiOnlineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TransaksiOnline::with('iklanonline')->latest();

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nofakturonline', 'like', "%{$keyword}%")
                  ->orWhere('nama_pemasangonline', 'like', "%{$keyword}%")
                  ->orWhere('sales_iklanonline', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'lunas') {
                $query->where('piutang_transaksionline', '<=', 0);
            } elseif ($request->status === 'piutang') {
                $query->where('piutang_transaksionline', '>', 0);
            }
        }

        $transaksionline = $query->paginate(10)->withQueryString();
        $iklanonline = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'ONL'))->get();

        return view('pages.iklan.transaksi.online.index', compact('transaksionline', 'iklanonline'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $iklanonline = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'ONL'))->get();
        $nofakturonline = TransaksiOnline::createCode();
        return view('pages.iklan.transaksi.online.create', compact('nofakturonline', 'iklanonline'));
    }

    public function cetak($id)
    {
        // Ambil data transaksi berdasarkan ID beserta relasi iklannya
        $transaksi = TransaksiOnline::with('iklanonline')->findOrFail($id);

        // Arahkan ke view cetak
        return view('pages.iklan.transaksi.online.cetak', compact('transaksi'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // --- CEK DATA DISINI ---
        // dd($request->all()); 
        // Hapus tanda // di depan dd, lalu coba submit form.
        // Jika muncul layar hitam berisi data, berarti form HTML aman.
        // Jika tidak muncul apa-apa, berarti tombol submit bermasalah.
        $request->validate([
            'nofakturonline'          => 'required',
            'tanggal_transaksionline' => 'required|date',
            'nama_pemasangonline'     => 'required',
            'alamat_pemasangonline'   => 'required',

            'id_iklanonline'          => 'required',
            'portal_iklanonline'        => 'nullable',
            'tanggal_muatiklanonline'   => 'required|date',
            'sales_iklanonline'         => 'required',
            'total_muatiklanonline'     => 'required|numeric|min:1', // Validasi Qty

            // Input Uang (String Berformat Rupiah)
            'harga_transaksionline'       => 'required',
            'diskon_transaksionline'      => 'required',
            'jumlahbayar_transaksionline' => 'required',
        ]);

        // 2. Bersihkan Format Rupiah (Hapus Titik)
        $harga_bersih  = (int) str_replace('.', '', $request->harga_transaksionline);
        $diskon_bersih = (int) str_replace('.', '', $request->diskon_transaksionline);
        $bayar_bersih  = (int) str_replace('.', '', $request->jumlahbayar_transaksionline);

        // Ambil Qty (Total Muat)
        $qty = $request->total_muatiklanonline;

        // 3. LOGIKA PERHITUNGAN SERVER (Safety)

        // A. Total Omset (Harga x Total Muat)
        $total_omset = $harga_bersih * $qty;

        // B. Hitung Insentif & Komisi (20% dari Total Omset)
        $insentif = $total_omset * 0.20;
        $komisi   = $total_omset * 0.20;

        // C. Hitung Subtotal (Omset - Diskon)
        $subtotal = $total_omset - $diskon_bersih;
        if ($subtotal < 0) $subtotal = 0;

        // D. Hitung PPN (11% dari Subtotal)
        $ppn = $subtotal * 0.11;

        // E. Hitung Total Tagihan Akhir
        $total_tagihan = $subtotal + $ppn;

        // F. Hitung Piutang
        $piutang = $total_tagihan - $bayar_bersih;
        if ($piutang < 0) $piutang = 0; // Jika lunas/kembalian

        // 4. Simpan ke Database
        TransaksiOnline::create([
            'nofakturonline'          => $request->nofakturonline,
            'tanggal_transaksionline' => $request->tanggal_transaksionline,
            'nama_pemasangonline'     => $request->nama_pemasangonline,
            'alamat_pemasangonline'   => $request->alamat_pemasangonline,

            'id_iklanonline'          => $request->id_iklanonline,
            'portal_iklanonline'        => $request->portal_iklanonline ? : '-',
            'tanggal_muatiklanonline'   => $request->tanggal_muatiklanonline,
            'sales_iklanonline'         => $request->sales_iklanonline,

            // Simpan Data Angka
            'total_muatiklanonline'         => $qty,
            'harga_transaksionline'       => $harga_bersih, // Harga Satuan
            'insentif_transaksionline'          => $insentif,
            'komisi_transaksionline'            => $komisi,
            'diskon_transaksionline'            => $diskon_bersih,
            'ppn_transaksionline'               => $ppn,
            'totaltagihan_transaksionline'  => $total_tagihan,
            'jumlahbayar_transaksionline' => $bayar_bersih,
            'piutang_transaksionline'     => $piutang,
        ]);

        return redirect()->route('transaksionline.index')
            ->with('success', 'Data Iklan Online Berhasil Ditambahkan!');
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
    public function update(Request $request, $id)
    {
        // 1. Validasi
        $request->validate([
            'tanggal_transaksionline' => 'required|date',
            'nama_pemasangonline'     => 'required',
            'alamat_pemasangonline'   => 'required',
            'id_iklanonline'          => 'required',
            'portal_iklanonline'      => 'nullable',
            'sales_iklanonline'       => 'required',
            'tanggal_muatiklanonline' => 'required|date',
            'total_muatiklanonline'   => 'required|numeric|min:1', // Wajib Qty

            // Input Uang
            'harga_transaksionline'       => 'required',
            'diskon_transaksionline'      => 'required',
            'jumlahbayar_transaksionline' => 'required',
        ]);

        // 2. Bersihkan Format Rupiah
        $harga_bersih  = (int) str_replace('.', '', $request->harga_transaksionline);
        $diskon_bersih = (int) str_replace('.', '', $request->diskon_transaksionline);
        $bayar_bersih  = (int) str_replace('.', '', $request->jumlahbayar_transaksionline);

        // Ambil Qty
        $qty = $request->total_muatiklanonline;

        // 3. HITUNG ULANG LOGIKA KEUANGAN (SAMA SEPERTI STORE)

        // A. Total Omset
        $total_omset = $harga_bersih * $qty;

        // B. Insentif & Komisi
        $insentif = $total_omset * 0.20;
        $komisi   = $total_omset * 0.20;

        // C. Subtotal
        $subtotal = $total_omset - $diskon_bersih;
        if ($subtotal < 0) $subtotal = 0;

        // D. PPN & Total
        $ppn = $subtotal * 0.11;
        $total_tagihan = $subtotal + $ppn;

        // E. Piutang
        $piutang = $total_tagihan - $bayar_bersih;
        if ($piutang < 0) $piutang = 0;

        // 4. UPDATE DATABASE
        $transaksi = \App\Models\TransaksiOnline::findOrFail($id);

        $transaksi->update([
            'tanggal_transaksionline' => $request->tanggal_transaksionline,
            'nama_pemasangonline'     => $request->nama_pemasangonline,
            'alamat_pemasangonline'   => $request->alamat_pemasangonline,
            'id_iklanonline'          => $request->id_iklanonline,
            'portal_iklanonline'      => $request->portal_iklanonline ? : '-',
            'sales_iklanonline'       => $request->sales_iklanonline,
            'tanggal_muatiklanonline' => $request->tanggal_muatiklanonline,

            // Update Data Keuangan
            'total_muatiklanonline'         => $qty,
            'harga_transaksionline'         => $harga_bersih,
            'insentif_transaksionline'      => $insentif,
            'diskon_transaksionline'        => $diskon_bersih,
            'komisi_transaksionline'        => $komisi,
            'ppn_transaksionline'           => $ppn,
            'totaltagihan_transaksionline'  => $total_tagihan,
            'jumlahbayar_transaksionline'   => $bayar_bersih,
            'piutang_transaksionline'       => $piutang,
        ]);

        return redirect()->route('transaksionline.index')
            ->with('success', 'Data Iklan Online Berhasil Di Update!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // 1. Cari data berdasarkan ID
        $transaksi = \App\Models\TransaksiOnline::findOrFail($id);

        // 2. Hapus data
        $transaksi->delete();

        // 3. Return respon (Karena dipanggil via Axios/AJAX)
        return response()->json([
            'status' => 'success',
            'message' => 'Data Transaksi Berhasil Dihapus'
        ]);
    }
}
