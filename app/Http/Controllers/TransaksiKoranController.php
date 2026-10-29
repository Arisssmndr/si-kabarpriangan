<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Faktur;
use App\Models\JenisIklan;
use App\Models\TransaksiKoran;
use Carbon\Carbon;
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
                  ->orWhere('no_order', 'like', "%{$keyword}%")
                  ->orWhere('nama_pemasangkoran', 'like', "%{$keyword}%")
                  ->orWhere('materi_iklan', 'like', "%{$keyword}%")
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
        $nofakturkoran = TransaksiKoran::generateNoFaktur();
        $noOrder = TransaksiKoran::generateNoOrder();
        $identitas = TransaksiKoran::generateIdentitas();
        $existingCustomers = Customer::where('tipe', 'iklan')->orderBy('nama_customer')->get();

        return view('pages.iklan.transaksi.koran.create', compact(
            'nofakturkoran',
            'noOrder',
            'identitas',
            'iklankoran',
            'existingCustomers'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nofakturkoran'          => 'required|string',
            'no_order'               => 'nullable|string',
            'identitas'              => 'nullable|string',
            'tanggal_transaksikoran' => 'required|date',
            'nama_pemasangkoran'     => 'required|string',
            'alamat_pemasangkoran'   => 'required|string',
            'kota'                   => 'nullable|string',
            'kop_iklan'              => 'nullable|string',
            'merk'                   => 'nullable|string',
            'materi_iklan'           => 'nullable|string',
            'id_iklankoran'          => 'required|exists:iklankoran,id',
            'halaman_iklan'          => 'required|string',
            'warna_iklan'            => 'required|string',
            'ukuran_iklan'           => 'nullable|string',
            'tanggal_muatkoran'      => 'required|date',
            'sales_iklankoran'       => 'required|string',
            'total_muatkoran'        => 'required|numeric|min:1',
            'harga_transaksikoran'   => 'required',
            'diskon_persen'          => 'nullable',
            'diskon_transaksikoran'  => 'nullable',
            'materai_transaksikoran' => 'nullable',
            'jumlahbayar_transaksikoran' => 'nullable',
            'komisi_transaksikoran'  => 'nullable',
            'insentif_transaksikoran'=> 'nullable',
        ]);

        // Bersihkan angka
        $harga_bersih   = (int) str_replace(['.', ','], '', $request->harga_transaksikoran);
        $qty            = max(1, (int) ($request->total_muatkoran ?? 1));
        $brutto         = $harga_bersih * $qty;

        $diskon_bersih  = (int) str_replace(['.', ','], '', $request->diskon_transaksikoran ?? 0);
        $diskon_persen  = (float) str_replace(',', '.', $request->diskon_persen ?? 0);

        if ($diskon_persen > 0 && $diskon_bersih == 0 && $brutto > 0) {
            $diskon_bersih = (int) round($brutto * ($diskon_persen / 100));
        } elseif ($diskon_bersih > 0 && $diskon_persen == 0 && $brutto > 0) {
            $diskon_persen = round(($diskon_bersih / $brutto) * 100, 2);
        }

        $netto          = max(0, $brutto - $diskon_bersih);
        
        // PPN 11% (default dihitung jika checkbox aktif atau ppn terisi)
        $kena_ppn       = $request->has('kena_ppn') ? $request->boolean('kena_ppn') : true;
        $ppn            = $kena_ppn ? (int) round($netto * 0.11) : 0;
        
        $materai        = (int) str_replace(['.', ','], '', $request->materai_transaksikoran ?? 0);
        $total_tagihan  = $netto + $ppn + $materai;

        $bayar_bersih   = (int) str_replace(['.', ','], '', $request->jumlahbayar_transaksikoran ?? 0);
        $piutang        = max(0, $total_tagihan - $bayar_bersih);

        // Komisi & Insentif internal (20% dari Netto jika tidak diisi manual)
        $komisi_input   = $request->filled('komisi_transaksikoran') ? (int) str_replace(['.', ','], '', $request->komisi_transaksikoran) : 0;
        $insentif_input = $request->filled('insentif_transaksikoran') ? (int) str_replace(['.', ','], '', $request->insentif_transaksikoran) : 0;
        $komisi         = $komisi_input > 0 ? $komisi_input : (int) round($netto * 0.20);
        $insentif       = $insentif_input > 0 ? $insentif_input : (int) round($netto * 0.20);

        // No Order & No Faktur fallback
        $noOrder = $request->filled('no_order') ? $request->no_order : TransaksiKoran::generateNoOrder($request->tanggal_transaksikoran);
        $identitas = $request->filled('identitas') ? $request->identitas : TransaksiKoran::generateIdentitas();
        $kota = $request->filled('kota') ? strtoupper($request->kota) : 'TASIKMALAYA';

        $tx = TransaksiKoran::create([
            'nofakturkoran'          => $request->nofakturkoran,
            'identitas'              => $identitas,
            'no_order'               => $noOrder,
            'tanggal_transaksikoran' => $request->tanggal_transaksikoran,
            'nama_pemasangkoran'     => $request->nama_pemasangkoran,
            'alamat_pemasangkoran'   => $request->alamat_pemasangkoran,
            'kota'                   => $kota,
            'id_iklankoran'          => $request->id_iklankoran,
            'halaman_iklan'          => $request->halaman_iklan,
            'warna_iklan'            => $request->warna_iklan,
            'kop_iklan'              => $request->kop_iklan,
            'merk'                   => $request->merk,
            'materi_iklan'           => $request->materi_iklan,
            'ukuran_iklan'           => $request->ukuran_iklan ?? '-',
            'tanggal_muatkoran'      => $request->tanggal_muatkoran,
            'sales_iklankoran'       => $request->sales_iklankoran,
            'total_muatkoran'        => $qty,
            'harga_transaksikoran'   => $harga_bersih,
            'brutto_transaksikoran'  => $brutto,
            'diskon_persen'          => $diskon_persen,
            'diskon_transaksikoran'  => $diskon_bersih,
            'netto_transaksikoran'   => $netto,
            'ppn_transaksikoran'     => $ppn,
            'materai_transaksikoran' => $materai,
            'insentif_transaksikoran'=> $insentif,
            'komisi_transaksikoran'  => $komisi,
            'totaltagihan_transaksikoran' => $total_tagihan,
            'jumlahbayar_transaksikoran'  => $bayar_bersih,
            'piutang_transaksikoran'      => $piutang,
        ]);

        // Simpan / update data customer
        $customer = Customer::firstOrCreate(
            ['nama_customer' => $request->nama_pemasangkoran],
            [
                'identitas' => $identitas,
                'alamat'    => $request->alamat_pemasangkoran,
                'kota'      => $kota,
                'tipe'      => 'iklan',
            ]
        );
        if ($customer && empty($customer->identitas)) {
            $customer->update(['identitas' => $identitas, 'kota' => $kota]);
        }

        // Sinkronisasi otomatis ke Modul Faktur Keuangan
        $statusFaktur = $piutang <= 0 ? 'lunas' : ($bayar_bersih > 0 ? 'cicilan' : 'belum_bayar');
        Faktur::updateOrCreate(
            ['no_faktur' => $request->nofakturkoran],
            [
                'no_order'       => $noOrder,
                'customer_id'    => $customer->id,
                'sales_nama'     => $request->sales_iklankoran,
                'kategori'       => 'iklan',
                'tipe_faktur'    => 'reguler',
                'tanggal_faktur' => $request->tanggal_transaksikoran,
                'jatuh_tempo'    => Carbon::parse($request->tanggal_transaksikoran)->addDays(14),
                'materi_iklan'   => $request->materi_iklan,
                'brutto'         => $brutto,
                'diskon_persen'  => $diskon_persen,
                'diskon_nominal' => $diskon_bersih,
                'netto'          => $netto,
                'ppn_nominal'    => $ppn,
                'materai'        => $materai,
                'rincian_biaya'  => [
                    [
                        'item'     => ($request->materi_iklan ? $request->materi_iklan . ' - ' : '') . 'Iklan Koran ' . ($request->halaman_iklan ?? '') . ' (' . ($request->ukuran_iklan ?? 'Standar') . ')',
                        'qty'      => $qty,
                        'harga'    => $harga_bersih,
                        'subtotal' => $total_tagihan,
                    ]
                ],
                'total_tagihan'  => $total_tagihan,
                'total_terbayar' => $bayar_bersih,
                'sisa_piutang'   => $piutang,
                'status'         => $statusFaktur,
                'created_by'     => auth()->id() ?: 1,
                'catatan'        => 'Sinkronisasi Transaksi Front Office Iklan Koran (HU. Priangan)',
            ]
        );

        return redirect()->route('transaksikoran.index')
            ->with('success', 'Order Transaksi Iklan HU. Priangan (' . $request->nofakturkoran . ') Berhasil Disimpan!');
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
            'kota'                   => 'nullable|string',
            'kop_iklan'              => 'nullable|string',
            'merk'                   => 'nullable|string',
            'materi_iklan'           => 'nullable|string',
            'id_iklankoran'          => 'required|exists:iklankoran,id',
            'halaman_iklan'          => 'required|string',
            'warna_iklan'            => 'required|string',
            'ukuran_iklan'           => 'nullable|string',
            'tanggal_muatkoran'      => 'required|date',
            'sales_iklankoran'       => 'required|string',
            'total_muatkoran'        => 'required|numeric|min:1',
            'harga_transaksikoran'   => 'required',
            'diskon_persen'          => 'nullable',
            'diskon_transaksikoran'  => 'nullable',
            'materai_transaksikoran' => 'nullable',
            'jumlahbayar_transaksikoran' => 'nullable',
            'komisi_transaksikoran'  => 'nullable',
            'insentif_transaksikoran'=> 'nullable',
        ]);

        $harga_bersih   = (int) str_replace(['.', ','], '', $request->harga_transaksikoran);
        $qty            = max(1, (int) ($request->total_muatkoran ?? 1));
        $brutto         = $harga_bersih * $qty;

        $diskon_bersih  = (int) str_replace(['.', ','], '', $request->diskon_transaksikoran ?? 0);
        $diskon_persen  = (float) str_replace(',', '.', $request->diskon_persen ?? 0);

        if ($diskon_persen > 0 && $diskon_bersih == 0 && $brutto > 0) {
            $diskon_bersih = (int) round($brutto * ($diskon_persen / 100));
        } elseif ($diskon_bersih > 0 && $diskon_persen == 0 && $brutto > 0) {
            $diskon_persen = round(($diskon_bersih / $brutto) * 100, 2);
        }

        $netto          = max(0, $brutto - $diskon_bersih);
        $kena_ppn       = $request->has('kena_ppn') ? $request->boolean('kena_ppn') : true;
        $ppn            = $kena_ppn ? (int) round($netto * 0.11) : 0;
        
        $materai        = (int) str_replace(['.', ','], '', $request->materai_transaksikoran ?? 0);
        $total_tagihan  = $netto + $ppn + $materai;

        $bayar_bersih   = (int) str_replace(['.', ','], '', $request->jumlahbayar_transaksikoran ?? 0);
        $piutang        = max(0, $total_tagihan - $bayar_bersih);

        $komisi_input   = $request->filled('komisi_transaksikoran') ? (int) str_replace(['.', ','], '', $request->komisi_transaksikoran) : 0;
        $insentif_input = $request->filled('insentif_transaksikoran') ? (int) str_replace(['.', ','], '', $request->insentif_transaksikoran) : 0;
        $komisi         = $komisi_input > 0 ? $komisi_input : (int) round($netto * 0.20);
        $insentif       = $insentif_input > 0 ? $insentif_input : (int) round($netto * 0.20);

        $kota = $request->filled('kota') ? strtoupper($request->kota) : ($transaksi->kota ?: 'TASIKMALAYA');
        $identitas = $request->filled('identitas') ? $request->identitas : ($transaksi->identitas ?: TransaksiKoran::generateIdentitas());

        $transaksi->update([
            'identitas'              => $identitas,
            'no_order'               => $request->no_order ?: $transaksi->no_order,
            'tanggal_transaksikoran' => $request->tanggal_transaksikoran,
            'nama_pemasangkoran'     => $request->nama_pemasangkoran,
            'alamat_pemasangkoran'   => $request->alamat_pemasangkoran,
            'kota'                   => $kota,
            'id_iklankoran'          => $request->id_iklankoran,
            'halaman_iklan'          => $request->halaman_iklan,
            'warna_iklan'            => $request->warna_iklan,
            'kop_iklan'              => $request->kop_iklan,
            'merk'                   => $request->merk,
            'materi_iklan'           => $request->materi_iklan,
            'ukuran_iklan'           => $request->ukuran_iklan ?? $transaksi->ukuran_iklan,
            'tanggal_muatkoran'      => $request->tanggal_muatkoran,
            'sales_iklankoran'       => $request->sales_iklankoran,
            'total_muatkoran'        => $qty,
            'harga_transaksikoran'   => $harga_bersih,
            'brutto_transaksikoran'  => $brutto,
            'diskon_persen'          => $diskon_persen,
            'diskon_transaksikoran'  => $diskon_bersih,
            'netto_transaksikoran'   => $netto,
            'ppn_transaksikoran'     => $ppn,
            'materai_transaksikoran' => $materai,
            'insentif_transaksikoran'=> $insentif,
            'komisi_transaksikoran'  => $komisi,
            'totaltagihan_transaksikoran' => $total_tagihan,
            'jumlahbayar_transaksikoran'  => $bayar_bersih,
            'piutang_transaksikoran'      => $piutang,
        ]);

        // Update Faktur terkait di Modul Keuangan
        $faktur = Faktur::where('no_faktur', $transaksi->nofakturkoran)->first();
        if ($faktur) {
            $statusFaktur = $piutang <= 0 ? 'lunas' : ($bayar_bersih > 0 ? 'cicilan' : 'belum_bayar');
            $faktur->update([
                'materi_iklan'   => $request->materi_iklan,
                'brutto'         => $brutto,
                'diskon_persen'  => $diskon_persen,
                'diskon_nominal' => $diskon_bersih,
                'netto'          => $netto,
                'ppn_nominal'    => $ppn,
                'materai'        => $materai,
                'total_tagihan'  => $total_tagihan,
                'total_terbayar' => $bayar_bersih,
                'sisa_piutang'   => $piutang,
                'status'         => $statusFaktur,
            ]);
        }

        return redirect()->route('transaksikoran.index')
            ->with('success', 'Transaksi Iklan Koran Berhasil Diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $transaksi = TransaksiKoran::findOrFail($id);
        
        // Hapus juga faktur terkait jika ada
        Faktur::where('no_faktur', $transaksi->nofakturkoran)->delete();
        
        $transaksi->delete();

        return response()->json(['status' => 'success']);
    }

    /**
     * Cetak faktur transaksi koran (Format Lembar Continuous Form Dot-Matrix Asli HU. Priangan).
     */
    public function cetak($id)
    {
        $transaksi = TransaksiKoran::with('iklankoran')->findOrFail($id);
        return view('pages.iklan.transaksi.koran.cetak', compact('transaksi'));
    }
}
