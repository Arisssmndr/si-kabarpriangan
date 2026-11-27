<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\InvoiceIklan;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceIklanController extends Controller
{
    /**
     * Daftar Semua Invoice & Kwitansi Iklan
     */
    public function index(Request $request)
    {
        $query = InvoiceIklan::with(['transaksiKoran', 'transaksiOnline', 'creator'])->latest('tanggal_invoice');

        // Filter Pencarian (No Invoice, Customer/Kepada, Deskripsi)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_invoice', 'like', "%{$q}%")
                    ->orWhere('kepada', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        // Filter Status Pembayaran
        if ($request->filled('status') && in_array($request->status, ['belum_lunas', 'lunas'])) {
            $query->where('status_pembayaran', $request->status);
        }

        // Filter Rentang Tanggal
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_invoice', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_invoice', '<=', $request->sampai_tanggal);
        }

        $invoices = $query->paginate(15)->withQueryString();

        // Statistik Ringkasan
        $totalNominal = InvoiceIklan::sum('total_amount');
        $totalLunas = InvoiceIklan::where('status_pembayaran', 'lunas')->sum('total_amount');
        $totalBelumLunas = InvoiceIklan::where('status_pembayaran', 'belum_lunas')->sum('total_amount');
        $countTotal = InvoiceIklan::count();

        return view('pages.keuangan.invoice.index', compact(
            'invoices',
            'totalNominal',
            'totalLunas',
            'totalBelumLunas',
            'countTotal'
        ));
    }

    /**
     * Form Pembuatan Invoice & Kwitansi Baru
     */
    public function create(Request $request)
    {
        $suggestedNoInvoice = InvoiceIklan::generateNoInvoice();
        $defaultTransferNote = InvoiceIklan::defaultCatatanTransfer();

        // Ambil daftar No Faktur terbaru dari Iklan Koran & Online untuk autocomplete
        $daftarFakturKoran = TransaksiKoran::whereNotNull('nofakturkoran')
            ->where('nofakturkoran', '!=', '')
            ->latest('tanggal_transaksikoran')
            ->select('id', 'nofakturkoran', 'nama_pemasangkoran', 'totaltagihan_transaksikoran')
            ->take(50)
            ->get();

        $daftarFakturOnline = TransaksiOnline::whereNotNull('nofakturonline')
            ->where('nofakturonline', '!=', '')
            ->latest('tanggal_transaksionline')
            ->select('id', 'nofakturonline', 'nama_pemasangonline', 'totaltagihan_transaksionline')
            ->take(30)
            ->get();

        // Jika ada order ID atau no_faktur yang dikirim langsung via parameter URL
        $orderData = null;
        if ($request->filled('transaksi_koran_id')) {
            $koran = TransaksiKoran::find($request->transaksi_koran_id);
            if ($koran) {
                $orderData = $this->formatOrderKoranData($koran);
            }
        } elseif ($request->filled('transaksi_online_id')) {
            $online = TransaksiOnline::find($request->transaksi_online_id);
            if ($online) {
                $orderData = $this->formatOrderOnlineData($online);
            }
        } elseif ($request->filled('no_faktur')) {
            $noFakturParam = trim($request->no_faktur);
            $koran = TransaksiKoran::where('nofakturkoran', $noFakturParam)->first();
            if ($koran) {
                $orderData = $this->formatOrderKoranData($koran);
            } else {
                $online = TransaksiOnline::where('nofakturonline', $noFakturParam)->first();
                if ($online) {
                    $orderData = $this->formatOrderOnlineData($online);
                }
            }
        }

        return view('pages.keuangan.invoice.create', compact(
            'suggestedNoInvoice',
            'defaultTransferNote',
            'orderData',
            'daftarFakturKoran',
            'daftarFakturOnline'
        ));
    }

    /**
     * API Pencarian Order Iklan (Koran & Online) untuk Modal / AJAX Form Autofill
     */
    public function searchOrder(Request $request)
    {
        $noFakturParam = trim($request->get('no_faktur', ''));

        // Jika pencarian spesifik berdasarkan No. Faktur (misal saat user ketik/pilih no faktur langsung)
        if ($noFakturParam !== '') {
            $koran = TransaksiKoran::where('nofakturkoran', $noFakturParam)->first();
            if ($koran) {
                return response()->json([
                    'success' => true,
                    'found'   => true,
                    'source'  => 'koran',
                    'data'    => $this->formatOrderKoranData($koran),
                    'message' => "Order Iklan Koran {$koran->nofakturkoran} berhasil ditemukan."
                ]);
            }

            $online = TransaksiOnline::where('nofakturonline', $noFakturParam)->first();
            if ($online) {
                return response()->json([
                    'success' => true,
                    'found'   => true,
                    'source'  => 'online',
                    'data'    => $this->formatOrderOnlineData($online),
                    'message' => "Order Iklan Online {$online->nofakturonline} berhasil ditemukan."
                ]);
            }

            // Coba pencarian LIKE jika tidak sama persis
            $koranLike = TransaksiKoran::where('nofakturkoran', 'like', "%{$noFakturParam}%")->first();
            if ($koranLike) {
                return response()->json([
                    'success' => true,
                    'found'   => true,
                    'source'  => 'koran',
                    'data'    => $this->formatOrderKoranData($koranLike),
                    'message' => "Order Iklan Koran {$koranLike->nofakturkoran} berhasil ditemukan."
                ]);
            }

            $onlineLike = TransaksiOnline::where('nofakturonline', 'like', "%{$noFakturParam}%")->first();
            if ($onlineLike) {
                return response()->json([
                    'success' => true,
                    'found'   => true,
                    'source'  => 'online',
                    'data'    => $this->formatOrderOnlineData($onlineLike),
                    'message' => "Order Iklan Online {$onlineLike->nofakturonline} berhasil ditemukan."
                ]);
            }

            return response()->json([
                'success' => true,
                'found'   => false,
                'message' => "Order dengan No. Faktur '{$noFakturParam}' tidak ditemukan."
            ]);
        }

        $q = trim($request->get('q', ''));
        $results = [];

        // 1. Cari di Transaksi Koran
        $korans = TransaksiKoran::where(function ($sub) use ($q) {
                $sub->where('nofakturkoran', 'like', "%{$q}%")
                    ->orWhere('nama_pemasangkoran', 'like', "%{$q}%")
                    ->orWhere('materi_iklan', 'like', "%{$q}%")
                    ->orWhere('no_order', 'like', "%{$q}%");
            })
            ->latest('tanggal_transaksikoran')
            ->take(15)
            ->get();

        foreach ($korans as $k) {
            $results[] = [
                'type'             => 'koran',
                'id'               => $k->id,
                'no_order'         => $k->no_order ?: '-',
                'no_faktur'        => $k->nofakturkoran ?: '-',
                'nama_pemasang'    => $k->nama_pemasangkoran ?: '-',
                'alamat'           => $k->alamat_pemasangkoran ?: '',
                'attn'             => $k->nama_pic ?: 'Bagian Keuangan',
                'materi'           => $k->materi_iklan ?: '-',
                'tanggal_transaksi'=> $k->tanggal_transaksikoran ? Carbon::parse($k->tanggal_transaksikoran)->translatedFormat('d F Y') : '-',
                'tanggal_muat'     => $k->tanggal_muatkoran ?: '-',
                'ukuran'           => $k->ukuran_iklan ?: '-',
                'warna'            => $k->warna_iklan ?: 'FC',
                'kategori_form'    => $k->kategori_form ?: 'Advertorial',
                'qty'              => $k->total_muatkoran ?: 1,
                'total_tagihan'    => (float) ($k->totaltagihan_transaksikoran ?: 0),
                'netto'            => (float) ($k->netto_transaksikoran ?: 0),
                'formatted_data'   => $this->formatOrderKoranData($k),
            ];
        }

        // 2. Cari di Transaksi Online
        $onlines = TransaksiOnline::where(function ($sub) use ($q) {
                $sub->where('nofakturonline', 'like', "%{$q}%")
                    ->orWhere('nama_pemasangonline', 'like', "%{$q}%");
            })
            ->latest('tanggal_transaksionline')
            ->take(10)
            ->get();

        foreach ($onlines as $o) {
            $results[] = [
                'type'             => 'online',
                'id'               => $o->id,
                'no_order'         => '-',
                'no_faktur'        => $o->nofakturonline ?: '-',
                'nama_pemasang'    => $o->nama_pemasangonline ?: '-',
                'alamat'           => $o->alamat_pemasangonline ?: '',
                'attn'             => 'Bagian Keuangan',
                'materi'           => 'Iklan Portal Online: ' . ($o->portal_iklanonline ?: 'kabarpriangan.com'),
                'tanggal_transaksi'=> $o->tanggal_transaksionline ? Carbon::parse($o->tanggal_transaksionline)->translatedFormat('d F Y') : '-',
                'tanggal_muat'     => $o->tanggal_muatiklanonline ?: '-',
                'ukuran'           => '-',
                'warna'            => 'Online',
                'kategori_form'    => 'Iklan Online',
                'qty'              => $o->total_muatiklanonline ?: 1,
                'total_tagihan'    => (float) ($o->totaltagihan_transaksionline ?: $o->harga_transaksionline ?: 0),
                'netto'            => (float) ($o->harga_transaksionline ?: 0),
                'formatted_data'   => $this->formatOrderOnlineData($o),
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => $results
        ]);
    }

    /**
     * Format Data Order Transaksi Koran agar siap di-autofill ke Invoice
     */
    protected function formatOrderKoranData(TransaksiKoran $k): array
    {
        $kategoriForm = $k->kategori_form ?: 'Advertorial';
        $warna = $k->warna_iklan ? strtoupper($k->warna_iklan) : 'FC';
        $materi = $k->materi_iklan ?: 'Publikasi Iklan Koran Kabar Priangan';
        $tglMuat = $k->tanggal_muatkoran ?: ($k->tanggal_transaksikoran ? Carbon::parse($k->tanggal_transaksikoran)->translatedFormat('d F Y') : '-');
        $ukuran = $k->ukuran_iklan ?: '-';

        // Susun teks deskripsi persis seperti contoh fisik invoice/kwitansi
        $deskripsi = "{$kategoriForm} {$warna} dengan tema :\n{$materi}\nTanggal Muat : {$tglMuat} Ukuran : {$ukuran}";

        $totalTagihan = (float) ($k->totaltagihan_transaksikoran ?: $k->harga_transaksikoran ?: 0);
        $qty = (int) ($k->total_muatkoran ?: 1);

        // Jika netto/DPP ada di data koran gunakan itu, jika tidak hitung DPP = Total / 1.11
        if ($k->netto_transaksikoran > 0) {
            $dpp = (float) $k->netto_transaksikoran;
            $ppn = (float) ($k->ppn_transaksikoran ?: ($totalTagihan - $dpp));
        } else {
            $dpp = round($totalTagihan / 1.11, 2);
            $ppn = round($totalTagihan - $dpp, 2);
        }

        $unitPrice = $qty > 0 ? round($dpp / $qty, 2) : $dpp;

        return [
            'no_faktur_iklan'     => $k->nofakturkoran ?: '',
            'transaksi_koran_id'  => $k->id,
            'transaksi_online_id' => null,
            'kepada'              => $k->nama_pemasangkoran ?: '',
            'alamat'              => $k->alamat_pemasangkoran ?: '',
            'attn'                => $k->nama_pic ?: 'Bagian Keuangan',
            'deskripsi'           => $deskripsi,
            'qty'                 => $qty,
            'unit_price'          => $unitPrice,
            'subtotal'            => $dpp,
            'dpp'                 => $dpp,
            'ppn_persen'          => 11.00,
            'ppn_nominal'         => $ppn,
            'total_amount'        => $totalTagihan,
            'terbilang'           => InvoiceIklan::terbilang($totalTagihan),
            'tanggal_invoice'     => $k->tanggal_transaksikoran ? Carbon::parse($k->tanggal_transaksikoran)->format('Y-m-d') : date('Y-m-d'),
        ];
    }

    /**
     * Format Data Order Transaksi Online agar siap di-autofill ke Invoice
     */
    protected function formatOrderOnlineData(TransaksiOnline $o): array
    {
        $materi = 'Iklan Portal Online Kabar Priangan (' . ($o->portal_iklanonline ?: 'kabarpriangan.com') . ')';
        $tglMuat = $o->tanggal_muatiklanonline ?: ($o->tanggal_transaksionline ? Carbon::parse($o->tanggal_transaksionline)->translatedFormat('d F Y') : '-');

        $deskripsi = "Pemasangan Iklan Online Media dengan tema :\n{$materi}\nTanggal Tayang : {$tglMuat}";

        $totalTagihan = (float) ($o->totaltagihan_transaksionline ?: $o->harga_transaksionline ?: 0);
        $qty = (int) ($o->total_muatiklanonline ?: 1);

        $dpp = round($totalTagihan / 1.11, 2);
        $ppn = round($totalTagihan - $dpp, 2);
        $unitPrice = $qty > 0 ? round($dpp / $qty, 2) : $dpp;

        return [
            'no_faktur_iklan'     => $o->nofakturonline ?: '',
            'transaksi_koran_id'  => null,
            'transaksi_online_id' => $o->id,
            'kepada'              => $o->nama_pemasangonline ?: '',
            'alamat'              => $o->alamat_pemasangonline ?: '',
            'attn'                => 'Bagian Keuangan',
            'deskripsi'           => $deskripsi,
            'qty'                 => $qty,
            'unit_price'          => $unitPrice,
            'subtotal'            => $dpp,
            'dpp'                 => $dpp,
            'ppn_persen'          => 11.00,
            'ppn_nominal'         => $ppn,
            'total_amount'        => $totalTagihan,
            'terbilang'           => InvoiceIklan::terbilang($totalTagihan),
            'tanggal_invoice'     => $o->tanggal_transaksionline ? Carbon::parse($o->tanggal_transaksionline)->format('Y-m-d') : date('Y-m-d'),
        ];
    }

    /**
     * Simpan Invoice & Kwitansi Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_invoice'             => 'required|string|max:100|unique:invoice_iklans,no_invoice',
            'no_faktur_iklan'        => 'nullable|string|max:100',
            'transaksi_koran_id'     => 'nullable|exists:transaksikoran,id',
            'transaksi_online_id'    => 'nullable|exists:transaksionline,id',
            'kepada'                 => 'required|string|max:255',
            'alamat'                 => 'nullable|string',
            'attn'                   => 'nullable|string|max:255',
            'tanggal_invoice'        => 'required|date',
            'deskripsi'              => 'required|string',
            'qty'                    => 'required|integer|min:1',
            'unit_price'             => 'required|numeric|min:0',
            'subtotal'               => 'required|numeric|min:0',
            'dpp'                    => 'required|numeric|min:0',
            'ppn_persen'             => 'nullable|numeric|min:0|max:100',
            'ppn_nominal'            => 'required|numeric|min:0',
            'total_amount'           => 'required|numeric|min:0',
            'terbilang'              => 'nullable|string',
            'catatan_transfer'       => 'nullable|string',
            'kota_terbit'            => 'required|string|max:100',
            'nama_penandatangan'     => 'required|string|max:255',
            'jabatan_penandatangan'  => 'required|string|max:255',
            'status_pembayaran'      => 'required|in:belum_lunas,lunas',
        ]);

        // Auto-generate terbilang jika kosong
        if (empty($validated['terbilang'])) {
            $validated['terbilang'] = InvoiceIklan::terbilang($validated['total_amount']);
        }

        // Catatan transfer default jika kosong
        if (empty($validated['catatan_transfer'])) {
            $validated['catatan_transfer'] = InvoiceIklan::defaultCatatanTransfer();
        }

        $validated['created_by'] = Auth::id() ?: 1;

        $invoice = InvoiceIklan::create($validated);

        return redirect()->route('keuangan.invoice.show', $invoice->id)
            ->with('success', "Invoice & Kwitansi {$invoice->no_invoice} berhasil dibuat.");
    }

    /**
     * Tampilkan Detail Dokumen Invoice & Kwitansi
     */
    public function show($id)
    {
        $invoice = InvoiceIklan::with(['transaksiKoran', 'transaksiOnline', 'creator'])->findOrFail($id);

        return view('pages.keuangan.invoice.show', compact('invoice'));
    }

    /**
     * Form Edit Dokumen Invoice & Kwitansi
     */
    public function edit($id)
    {
        $invoice = InvoiceIklan::findOrFail($id);

        $daftarFakturKoran = TransaksiKoran::whereNotNull('nofakturkoran')
            ->where('nofakturkoran', '!=', '')
            ->latest('tanggal_transaksikoran')
            ->select('id', 'nofakturkoran', 'nama_pemasangkoran', 'totaltagihan_transaksikoran')
            ->take(50)
            ->get();

        $daftarFakturOnline = TransaksiOnline::whereNotNull('nofakturonline')
            ->where('nofakturonline', '!=', '')
            ->latest('tanggal_transaksionline')
            ->select('id', 'nofakturonline', 'nama_pemasangonline', 'totaltagihan_transaksionline')
            ->take(30)
            ->get();

        return view('pages.keuangan.invoice.edit', compact('invoice', 'daftarFakturKoran', 'daftarFakturOnline'));
    }

    /**
     * Simpan Perubahan Invoice & Kwitansi
     */
    public function update(Request $request, $id)
    {
        $invoice = InvoiceIklan::findOrFail($id);

        $validated = $request->validate([
            'no_invoice'             => 'required|string|max:100|unique:invoice_iklans,no_invoice,' . $invoice->id,
            'no_faktur_iklan'        => 'nullable|string|max:100',
            'transaksi_koran_id'     => 'nullable|exists:transaksikoran,id',
            'transaksi_online_id'    => 'nullable|exists:transaksionline,id',
            'kepada'                 => 'required|string|max:255',
            'alamat'                 => 'nullable|string',
            'attn'                   => 'nullable|string|max:255',
            'tanggal_invoice'        => 'required|date',
            'deskripsi'              => 'required|string',
            'qty'                    => 'required|integer|min:1',
            'unit_price'             => 'required|numeric|min:0',
            'subtotal'               => 'required|numeric|min:0',
            'dpp'                    => 'required|numeric|min:0',
            'ppn_persen'             => 'nullable|numeric|min:0|max:100',
            'ppn_nominal'            => 'required|numeric|min:0',
            'total_amount'           => 'required|numeric|min:0',
            'terbilang'              => 'nullable|string',
            'catatan_transfer'       => 'nullable|string',
            'kota_terbit'            => 'required|string|max:100',
            'nama_penandatangan'     => 'required|string|max:255',
            'jabatan_penandatangan'  => 'required|string|max:255',
            'status_pembayaran'      => 'required|in:belum_lunas,lunas',
        ]);

        if (empty($validated['terbilang'])) {
            $validated['terbilang'] = InvoiceIklan::terbilang($validated['total_amount']);
        }

        $invoice->update($validated);

        return redirect()->route('keuangan.invoice.show', $invoice->id)
            ->with('success', "Invoice & Kwitansi {$invoice->no_invoice} berhasil diperbarui.");
    }

    /**
     * Hapus Dokumen Invoice & Kwitansi
     */
    public function destroy($id)
    {
        $invoice = InvoiceIklan::findOrFail($id);
        $no = $invoice->no_invoice;
        $invoice->delete();

        return redirect()->route('keuangan.invoice.index')
            ->with('success', "Invoice {$no} berhasil dihapus.");
    }

    /**
     * Cetak Lembar INVOICE Resmi (Sesuai Foto 1)
     */
    public function cetakInvoice($id)
    {
        $invoice = InvoiceIklan::with(['transaksiKoran', 'transaksiOnline'])->findOrFail($id);

        return view('pages.keuangan.invoice.cetak-invoice', compact('invoice'));
    }

    /**
     * Cetak Lembar KWITANSI Resmi (Sesuai Foto 2)
     */
    public function cetakKwitansi($id)
    {
        $invoice = InvoiceIklan::with(['transaksiKoran', 'transaksiOnline'])->findOrFail($id);

        return view('pages.keuangan.invoice.cetak-kwitansi', compact('invoice'));
    }
}
