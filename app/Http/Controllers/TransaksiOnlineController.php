<?php

namespace App\Http\Controllers;

use App\Models\JenisIklan;
use App\Models\Pelanggan;
use App\Models\PembayaranIklan;
use App\Models\SalesAe;
use App\Models\SistemConfig;
use App\Models\Sppi;
use App\Models\TransaksiOnline;
use App\Services\FakturSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransaksiOnlineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TransaksiOnline::with(['iklanonline', 'pelanggan', 'salesAe'])->latest();

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nofakturonline', 'like', "%{$keyword}%")
                  ->orWhere('nama_pemasangonline', 'like', "%{$keyword}%")
                  ->orWhere('no_spk', 'like', "%{$keyword}%")
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

        if ($request->filled('status_produksi')) {
            $query->where('status_produksi', $request->status_produksi);
        }

        $transaksionline = $query->paginate(10)->withQueryString();
        $iklanonline = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'ONL'))->get();
        $ppnPersen = SistemConfig::getPpnPersen();

        $statusCounts = [
            'semua'           => TransaksiOnline::count(),
            'menunggu_bahan'  => TransaksiOnline::where('status_produksi', 'menunggu_bahan')->count(),
            'antrean_redaksi' => TransaksiOnline::where('status_produksi', 'antrean_redaksi')->count(),
            'tayang'          => TransaksiOnline::where('status_produksi', 'tayang')->count(),
            'selesai'         => TransaksiOnline::where('status_produksi', 'selesai')->count(),
        ];

        return view('pages.iklan.transaksi.online.index', compact('transaksionline', 'iklanonline', 'ppnPersen', 'statusCounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $iklanonline = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'ONL'))->get();
        if ($iklanonline->isEmpty()) {
            $iklanonline = JenisIklan::all();
        }
        $salesAes = SalesAe::where('is_active', true)->orderBy('kode_ae')->get();
        $pelanggans = Pelanggan::orderBy('nama_instansi')->get();
        $nofakturonline = \App\Services\NomorDokumenService::previewFaktur();
        $ppnPersen = SistemConfig::getPpnPersen();
        $configs = [
            'ppn_persen'     => $ppnPersen,
            'jam_cutoff'     => SistemConfig::getJamCutoff(),
            'is_past_cutoff' => SistemConfig::isPastCutoff(),
        ];

        return view('pages.iklan.transaksi.online.create', compact('nofakturonline', 'iklanonline', 'ppnPersen', 'salesAes', 'pelanggans', 'configs'));
    }

    public function cetak($id)
    {
        $transaksi = TransaksiOnline::with(['iklanonline', 'pelanggan', 'salesAe'])->findOrFail($id);
        return view('pages.iklan.transaksi.online.cetak', compact('transaksi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nofakturonline'          => 'required',
            'tanggal_transaksionline' => 'required|date',
            'nama_pemasangonline'     => 'required',
            'alamat_pemasangonline'   => 'required',
            'sales_iklanonline'       => 'required',
            'tanggal_muatiklanonline' => 'required|date',
            'total_muatiklanonline'   => 'required|numeric|min:1',

            // Keuangan
            'harga_transaksionline'       => 'required',
            'diskon_transaksionline'      => 'nullable',
            'jumlahbayar_transaksionline' => 'required',
        ]);

        // Bersihkan format mata uang
        $harga_bersih  = (int) preg_replace('/[^0-9]/', '', $request->harga_transaksionline ?? '0');
        $diskon_bersih = (int) preg_replace('/[^0-9]/', '', $request->diskon_transaksionline ?? '0');
        $bayar_bersih  = (int) preg_replace('/[^0-9]/', '', $request->jumlahbayar_transaksionline ?? '0');
        $qty           = (int) ($request->total_muatiklanonline ?: 1);

        // Kalkulasi Keuangan
        $total_omset   = $harga_bersih * $qty;
        $subtotal      = max(0, $total_omset - $diskon_bersih);
        $ppnPersen     = SistemConfig::getPpnPersen();
        $ppn           = (int) round($subtotal * ($ppnPersen / 100));
        $total_tagihan = $subtotal + $ppn;
        $piutang       = max(0, $total_tagihan - $bayar_bersih);

        // Komisioner (Perantara / Pihak Ketiga) & Komisi Fleksibel
        $komisionerNama = $request->filled('komisioner') ? trim($request->komisioner) : null;
        $komisiPersen   = $request->filled('komisi_persen') ? (float) $request->komisi_persen : 0;
        $komisi_input   = $request->filled('komisi_transaksionline') ? (int) str_replace(['.', ','], '', $request->komisi_transaksionline) : 0;
        
        if ($komisi_input > 0) {
            $komisi = $komisi_input;
            if ($subtotal > 0 && $komisiPersen == 0) {
                $komisiPersen = round(($komisi / $subtotal) * 100, 2);
            }
        } elseif ($komisiPersen > 0) {
            $komisi = (int) round(($subtotal * $komisiPersen) / 100);
        } else {
            $komisi = 0;
            $komisiPersen = 0;
        }

        // Insentif Sales AE / FO Fleksibel
        $insentifPersen = $request->filled('insentif_persen') ? (float) $request->insentif_persen : 0;
        $insentif_input = $request->filled('insentif_transaksionline') ? (int) str_replace(['.', ','], '', $request->insentif_transaksionline) : 0;

        if ($insentif_input > 0) {
            $insentif = $insentif_input;
            if ($subtotal > 0 && $insentifPersen == 0) {
                $insentifPersen = round(($insentif / $subtotal) * 100, 2);
            }
        } elseif ($insentifPersen > 0) {
            $insentif = (int) round(($subtotal * $insentifPersen) / 100);
        } else {
            $insentif = 0;
            $insentifPersen = 0;
        }

        // Cari atau validasi sales_ae_id
        $salesAeId = $request->sales_ae_id;
        if (!$salesAeId && $request->filled('sales_iklanonline')) {
            $salesAeId = SalesAe::where('nama_ae', $request->sales_iklanonline)->value('id');
        }

        // Upload File Bukti Tayang jika ada
        $filePath = null;
        if ($request->hasFile('file_bukti_tayang')) {
            $filePath = $request->file('file_bukti_tayang')->store('bukti_tayang_online', 'public');
        }

        $kategoriProduk = $request->kategori_produk_publikasi;
        if (!is_array($kategoriProduk) && !empty($kategoriProduk)) {
            $kategoriProduk = [$kategoriProduk];
        }

        // Resolusi Pelanggan & Generate No Faktur Atomik
        $pelangganId = Pelanggan::resolveFromRequest($request, 'nama_pemasangonline', 'alamat_pemasangonline', 'no_kontak_pemasang');
        $noFaktur = TransaksiOnline::createCode();

        DB::beginTransaction();
        try {

        // Simpan Data Order Publikasi
        $transaksi = TransaksiOnline::create([
            'nofakturonline'          => $noFaktur,
            'tanggal_transaksionline' => $request->tanggal_transaksionline,
            'pelanggan_id'            => $pelangganId,
            'nama_pemasangonline'     => $request->nama_pemasangonline,
            'produk_jasa'             => $request->produk_jasa,
            'no_spk'                  => $request->no_spk,
            'nama_pic'                => $request->nama_pic,
            'jabatan_pic'             => $request->jabatan_pic,
            'no_kontak_pemasang'      => $request->no_kontak_pemasang,
            'alamat_pemasangonline'   => $request->alamat_pemasangonline,

            'id_iklanonline'          => $request->id_iklanonline ?: JenisIklan::first()?->id,
            'portal_iklanonline'      => is_array($request->platforms_publikasi) ? implode(', ', $request->platforms_publikasi) : ($request->portal_iklanonline ?: 'Portal Berita'),
            'platforms_publikasi'     => $request->platforms_publikasi ?? ['Portal Berita'],
            'skema_pembayaran'        => $request->skema_pembayaran ?: 'sekali_tayang',
            'status_produksi'         => $request->status_produksi ?: 'menunggu_bahan',
            'kategori_produk_publikasi'=> $kategoriProduk ?? ['Advertorial Berita'],
            'bentuk_publikasi'        => $request->bentuk_publikasi ?: 'display',
            'bentuk_publikasi_lainnya'=> $request->bentuk_publikasi_lainnya,
            'materi_iklan'            => $request->materi_iklan,
            'url_materi_iklan'        => $request->url_materi_iklan,
            'url_berita_tayang'       => $request->url_berita_tayang,
            'url_medsos_tayang'       => $request->url_medsos_tayang,
            'file_bukti_tayang'       => $filePath,
            'tanggal_muatiklanonline' => $request->tanggal_muatiklanonline,
            'tanggal_selesai_tayang'  => $request->tanggal_selesai_tayang,
            'tanggal_realisasi_tayang'=> $request->tanggal_realisasi_tayang,
            'total_muatiklanonline'   => $qty,
            'catatan_redaksi'         => $request->catatan_redaksi,

            'sales_ae_id'             => $salesAeId,
            'sales_iklanonline'       => $request->sales_iklanonline,
            'komisioner'              => $komisionerNama,
            'komisi_persen'           => $komisiPersen,
            'komisi_transaksionline'  => $komisi,
            'insentif_persen'         => $insentifPersen,
            'insentif_transaksionline'=> $insentif,

            'harga_transaksionline'         => $harga_bersih,
            'diskon_transaksionline'        => $diskon_bersih,
            'ppn_transaksionline'           => $ppn,
            'ppn_persen'                    => $ppnPersen,
            'totaltagihan_transaksionline'  => $total_tagihan,
            'jumlahbayar_transaksionline'   => $bayar_bersih,
            'piutang_transaksionline'       => $piutang,
        ]);

            // Catat Pembayaran Pertama / DP jika ada uang yang disetorkan
            if ($bayar_bersih > 0) {
                PembayaranIklan::create([
                    'nokwitansi'     => PembayaranIklan::createCode('ONL'),
                    'media_type'     => 'online',
                    'transaksi_id'   => $transaksi->id,
                    'nofaktur'       => $transaksi->nofakturonline,
                    'tanggal_bayar'  => $transaksi->tanggal_transaksionline,
                    'nominal_bayar'  => $bayar_bersih,
                    'potongan_pph23' => 0,
                    'metode_bayar'   => 'Tunai_Kasir',
                    'nama_penyetor'  => $transaksi->nama_pemasangonline,
                    'catatan'        => 'Pembayaran awal pemesanan iklan online di loket FO',
                    'kasir_user_id'  => Auth::id(),
                ]);
            }

            // Auto-generate SPPI (Insertion Order Publikasi)
            Sppi::create([
                'nomor_sppi'   => Sppi::createCode('ONL'),
                'media_type'   => 'online',
                'transaksi_id' => $transaksi->id,
                'nofaktur'     => $transaksi->nofakturonline,
                'tanggal_sppi' => $transaksi->tanggal_transaksionline,
                'status_sppi'  => 'Belum_Ditandatangani',
            ]);

            // Sinkronisasi Terpadu ke Buku Faktur Keuangan (1 Transaksi = 1 Faktur Resmi)
            FakturSyncService::syncFromTransaksi($transaksi, 'online');

            DB::commit();

            return redirect()->route('transaksionline.index')
                ->with('success', 'Order Publikasi Online & Multimedia Berhasil Disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $transaksi = TransaksiOnline::with(['iklanonline', 'pelanggan', 'salesAe'])->findOrFail($id);
        $iklanonline = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'ONL'))->get();
        if ($iklanonline->isEmpty()) {
            $iklanonline = JenisIklan::all();
        }
        $salesAes = SalesAe::where('is_active', true)->orderBy('kode_ae')->get();
        $salesAe = $salesAes;
        $pelanggans = Pelanggan::orderBy('nama_instansi')->get();
        $nofakturonline = $transaksi->nofakturonline;
        $ppnPersen = $transaksi->ppn_persen ?: SistemConfig::getPpnPersen();
        $configs = [
            'ppn_persen'     => $ppnPersen,
            'jam_cutoff'     => SistemConfig::getJamCutoff(),
            'is_past_cutoff' => SistemConfig::isPastCutoff(),
        ];

        return view('pages.iklan.transaksi.online.create', compact('transaksi', 'nofakturonline', 'iklanonline', 'ppnPersen', 'salesAe', 'salesAes', 'pelanggans', 'configs'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaksi = TransaksiOnline::with(['iklanonline', 'pelanggan', 'salesAe'])->findOrFail($id);
        return view('pages.iklan.transaksi.online.cetak', compact('transaksi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_transaksionline' => 'required|date',
            'nama_pemasangonline'     => 'required',
            'alamat_pemasangonline'   => 'required',
            'sales_iklanonline'       => 'required',
            'tanggal_muatiklanonline' => 'required|date',
            'total_muatiklanonline'   => 'required|numeric|min:1',

            // Keuangan
            'harga_transaksionline'       => 'required',
            'diskon_transaksionline'      => 'nullable',
            'jumlahbayar_transaksionline' => 'required',
        ]);

        $harga_bersih  = (int) preg_replace('/[^0-9]/', '', $request->harga_transaksionline ?? '0');
        $diskon_bersih = (int) preg_replace('/[^0-9]/', '', $request->diskon_transaksionline ?? '0');
        $bayar_bersih  = (int) preg_replace('/[^0-9]/', '', $request->jumlahbayar_transaksionline ?? '0');
        $qty           = (int) ($request->total_muatiklanonline ?: 1);

        $total_omset   = $harga_bersih * $qty;
        $subtotal      = max(0, $total_omset - $diskon_bersih);
        $ppnPersen     = SistemConfig::getPpnPersen();
        $ppn           = (int) round($subtotal * ($ppnPersen / 100));
        $total_tagihan = $subtotal + $ppn;
        $piutang       = max(0, $total_tagihan - $bayar_bersih);

        // Komisioner (Perantara / Pihak Ketiga) & Komisi Fleksibel
        $komisionerNama = $request->filled('komisioner') ? trim($request->komisioner) : null;
        $komisiPersen   = $request->filled('komisi_persen') ? (float) $request->komisi_persen : 0;
        $komisi_input   = $request->filled('komisi_transaksionline') ? (int) str_replace(['.', ','], '', $request->komisi_transaksionline) : 0;
        
        if ($komisi_input > 0) {
            $komisi = $komisi_input;
            if ($subtotal > 0 && $komisiPersen == 0) {
                $komisiPersen = round(($komisi / $subtotal) * 100, 2);
            }
        } elseif ($komisiPersen > 0) {
            $komisi = (int) round(($subtotal * $komisiPersen) / 100);
        } else {
            $komisi = 0;
            $komisiPersen = 0;
        }

        // Insentif Sales AE / FO Fleksibel
        $insentifPersen = $request->filled('insentif_persen') ? (float) $request->insentif_persen : 0;
        $insentif_input = $request->filled('insentif_transaksionline') ? (int) str_replace(['.', ','], '', $request->insentif_transaksionline) : 0;

        if ($insentif_input > 0) {
            $insentif = $insentif_input;
            if ($subtotal > 0 && $insentifPersen == 0) {
                $insentifPersen = round(($insentif / $subtotal) * 100, 2);
            }
        } elseif ($insentifPersen > 0) {
            $insentif = (int) round(($subtotal * $insentifPersen) / 100);
        } else {
            $insentif = 0;
            $insentifPersen = 0;
        }

        $salesAeId = $request->sales_ae_id;
        if (!$salesAeId && $request->filled('sales_iklanonline')) {
            $salesAeId = SalesAe::where('nama_ae', $request->sales_iklanonline)->value('id');
        }

        DB::beginTransaction();
        try {
            $transaksi = TransaksiOnline::findOrFail($id);

            $kategoriProduk = $request->kategori_produk_publikasi;
            if (!is_array($kategoriProduk) && !empty($kategoriProduk)) {
                $kategoriProduk = [$kategoriProduk];
            }

            $updateData = [
                'tanggal_transaksionline' => $request->tanggal_transaksionline,
                'pelanggan_id'            => $request->pelanggan_id ?? $transaksi->pelanggan_id,
                'nama_pemasangonline'     => $request->nama_pemasangonline,
                'produk_jasa'             => $request->produk_jasa,
                'no_spk'                  => $request->no_spk ?? $transaksi->no_spk,
                'nama_pic'                => $request->nama_pic,
                'jabatan_pic'             => $request->jabatan_pic,
                'no_kontak_pemasang'      => $request->no_kontak_pemasang,
                'alamat_pemasangonline'   => $request->alamat_pemasangonline,

                'id_iklanonline'          => $request->id_iklanonline ?: $transaksi->id_iklanonline,
                'portal_iklanonline'      => is_array($request->platforms_publikasi) ? implode(', ', $request->platforms_publikasi) : ($request->portal_iklanonline ?: $transaksi->portal_iklanonline),
                'platforms_publikasi'     => $request->platforms_publikasi ?? $transaksi->platforms_publikasi,
                'skema_pembayaran'        => $request->skema_pembayaran ?: ($transaksi->skema_pembayaran ?: 'sekali_tayang'),
                'status_produksi'         => $request->status_produksi ?: $transaksi->status_produksi,
                'kategori_produk_publikasi'=> $kategoriProduk ?? $transaksi->kategori_produk_publikasi,
                'bentuk_publikasi'        => $request->bentuk_publikasi ?: ($transaksi->bentuk_publikasi ?: 'display'),
                'bentuk_publikasi_lainnya'=> $request->bentuk_publikasi_lainnya,
                'materi_iklan'            => $request->materi_iklan,
                'url_materi_iklan'        => $request->url_materi_iklan,
                'url_berita_tayang'       => $request->url_berita_tayang ?? $transaksi->url_berita_tayang,
                'url_medsos_tayang'       => $request->url_medsos_tayang ?? $transaksi->url_medsos_tayang,
                'tanggal_muatiklanonline' => $request->tanggal_muatiklanonline,
                'tanggal_selesai_tayang'  => $request->tanggal_selesai_tayang,
                'tanggal_realisasi_tayang'=> $request->tanggal_realisasi_tayang ?? $transaksi->tanggal_realisasi_tayang,
                'total_muatiklanonline'   => $qty,
                'catatan_redaksi'         => $request->catatan_redaksi ?? $transaksi->catatan_redaksi,

                'sales_ae_id'             => $salesAeId ?? $transaksi->sales_ae_id,
                'sales_iklanonline'       => $request->sales_iklanonline,
                'komisioner'              => $komisionerNama,
                'komisi_persen'           => $komisiPersen,
                'komisi_transaksionline'  => $komisi,
                'insentif_persen'         => $insentifPersen,
                'insentif_transaksionline'=> $insentif,

                'harga_transaksionline'         => $harga_bersih,
                'diskon_transaksionline'        => $diskon_bersih,
                'ppn_transaksionline'           => $ppn,
                'ppn_persen'                    => $ppnPersen,
                'totaltagihan_transaksionline'  => $total_tagihan,
                'jumlahbayar_transaksionline'   => $bayar_bersih,
                'piutang_transaksionline'       => $piutang,
            ];

            if ($request->hasFile('file_bukti_tayang')) {
                if ($transaksi->file_bukti_tayang) {
                    Storage::disk('public')->delete($transaksi->file_bukti_tayang);
                }
                $updateData['file_bukti_tayang'] = $request->file('file_bukti_tayang')->store('bukti_tayang_online', 'public');
            }

            $transaksi->update($updateData);

            // Sinkronisasi Pembayaran Awal di Kwitansi Kasir
            $pembayaranAwal = PembayaranIklan::where('transaksi_id', $transaksi->id)
                ->where('media_type', 'online')
                ->oldest()
                ->first();

            if ($bayar_bersih > 0) {
                if ($pembayaranAwal) {
                    $pembayaranAwal->update([
                        'nominal_bayar' => $bayar_bersih,
                        'nama_penyetor' => $transaksi->nama_pemasangonline,
                    ]);
                } else {
                    PembayaranIklan::create([
                        'nokwitansi'     => PembayaranIklan::createCode('ONL'),
                        'media_type'     => 'online',
                        'transaksi_id'   => $transaksi->id,
                        'nofaktur'       => $transaksi->nofakturonline,
                        'tanggal_bayar'  => $transaksi->tanggal_transaksionline,
                        'nominal_bayar'  => $bayar_bersih,
                        'potongan_pph23' => 0,
                        'metode_bayar'   => 'Tunai_Kasir',
                        'nama_penyetor'  => $transaksi->nama_pemasangonline,
                        'catatan'        => 'Pembayaran awal transaksi Online (Revisi Edit)',
                        'kasir_user_id'  => Auth::id(),
                    ]);
                }
            } elseif ($pembayaranAwal && $bayar_bersih == 0 && str_contains($pembayaranAwal->catatan, 'Pembayaran awal')) {
                $pembayaranAwal->delete();
            }

            // Sinkronisasi Terpadu ke Buku Faktur Keuangan (1 Transaksi = 1 Faktur Resmi)
            FakturSyncService::syncFromTransaksi($transaksi, 'online');

            DB::commit();

            return redirect()->route('transaksionline.index')
                ->with('success', 'Order Publikasi Online & Multimedia Berhasil Diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui transaksi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Update status alur produksi redaksi/medsos.
     */
    public function updateStatusProduksi(Request $request, $id)
    {
        $request->validate([
            'status_produksi' => 'required|in:menunggu_bahan,antrean_redaksi,tayang,selesai',
        ]);

        $transaksi = TransaksiOnline::findOrFail($id);
        $transaksi->update([
            'status_produksi' => $request->status_produksi,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Status alur produksi berhasil diubah ke: ' . $transaksi->status_produksi_label,
                'label' => $transaksi->status_produksi_label,
                'badge_class' => $transaksi->status_produksi_badge_class,
            ]);
        }

        return back()->with('success', 'Status alur produksi berhasil diperbarui menjadi: ' . $transaksi->status_produksi_label);
    }

    /**
     * Simpan Bukti Tayang (Digital Tearsheet & URL Live).
     */
    public function simpanBuktiTayang(Request $request, $id)
    {
        $request->validate([
            'url_berita_tayang'        => 'nullable|url',
            'url_medsos_tayang'        => 'nullable|url',
            'file_bukti_tayang'        => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            'tanggal_realisasi_tayang' => 'nullable|date',
            'catatan_redaksi'          => 'nullable|string',
            'status_produksi'          => 'nullable|in:menunggu_bahan,antrean_redaksi,tayang,selesai',
        ]);

        $transaksi = TransaksiOnline::findOrFail($id);

        $updateData = [
            'url_berita_tayang'        => $request->url_berita_tayang ?? $transaksi->url_berita_tayang,
            'url_medsos_tayang'        => $request->url_medsos_tayang ?? $transaksi->url_medsos_tayang,
            'tanggal_realisasi_tayang' => $request->tanggal_realisasi_tayang ?? ($transaksi->tanggal_realisasi_tayang ?: now()->toDateString()),
            'catatan_redaksi'          => $request->catatan_redaksi ?? $transaksi->catatan_redaksi,
        ];

        if ($request->filled('status_produksi')) {
            $updateData['status_produksi'] = $request->status_produksi;
        } elseif ($request->filled('url_berita_tayang') || $request->filled('url_medsos_tayang') || $request->hasFile('file_bukti_tayang')) {
            if ($transaksi->status_produksi === 'menunggu_bahan' || $transaksi->status_produksi === 'antrean_redaksi') {
                $updateData['status_produksi'] = 'tayang';
            }
        }

        if ($request->hasFile('file_bukti_tayang')) {
            if ($transaksi->file_bukti_tayang) {
                Storage::disk('public')->delete($transaksi->file_bukti_tayang);
            }
            $updateData['file_bukti_tayang'] = $request->file('file_bukti_tayang')->store('bukti_tayang_online', 'public');
        }

        $transaksi->update($updateData);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Laporan Bukti Tayang (Digital Tearsheet) Berhasil Disimpan!',
                'data' => $transaksi,
            ]);
        }

        return back()->with('success', 'Laporan Bukti Tayang (Digital Tearsheet) Berhasil Disimpan!');
    }

    /**
     * Prosedur Pembatalan Transaksi Iklan Online
     */
    public function batalkan(Request $request, $id)
    {
        $request->validate([
            'alasan_batal' => 'required|string|min:5|max:500',
        ]);

        $transaksi = TransaksiOnline::findOrFail($id);
        $transaksi->update([
            'status_produksi' => 'selesai',
            'catatan_redaksi' => trim(($transaksi->catatan_redaksi ?? '') . " [DIBATALKAN: {$request->alasan_batal}]"),
        ]);

        // Sinkronisasi status pembatalan ke Buku Faktur Keuangan
        FakturSyncService::cancelFaktur($transaksi->nofakturonline, $request->alasan_batal);

        return redirect()->route('transaksionline.index')
            ->with('success', "Order Publikasi Online {$transaksi->nofakturonline} resmi DIBATALKAN.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // 1. Cari data berdasarkan ID
        $transaksi = TransaksiOnline::findOrFail($id);

        if ($transaksi->jumlahbayar_transaksionline > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Order yang sudah memiliki riwayat pembayaran tidak dapat dihapus permanen. Gunakan prosedur Pembatalan Transaksi.'
            ], 422);
        }

        if ($transaksi->file_bukti_tayang) {
            Storage::disk('public')->delete($transaksi->file_bukti_tayang);
        }

        FakturSyncService::deleteFaktur($transaksi->nofakturonline);
        PembayaranIklan::where('transaksi_id', $transaksi->id)->where('media_type', 'online')->delete();
        Sppi::where('transaksi_id', $transaksi->id)->where('media_type', 'online')->delete();

        // 2. Hapus data
        $transaksi->delete();

        // 3. Return respon (Karena dipanggil via Axios/AJAX)
        return response()->json([
            'status' => 'success',
            'message' => 'Data Transaksi Berhasil Dihapus'
        ]);
    }
}
