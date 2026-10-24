<?php

namespace App\Http\Controllers;

use App\Models\JenisIklan;
use App\Models\Pelanggan;
use App\Models\PembayaranIklan;
use App\Models\SalesAe;
use App\Models\SistemConfig;
use App\Models\Sppi;
use App\Models\TransaksiPriangan;
use App\Services\FakturSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiIklanPrianganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TransaksiPriangan::with(['iklanpriangan', 'salesAe', 'pelanggan'])->latest();

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
        if ($iklanpriangan->isEmpty()) {
            $iklanpriangan = JenisIklan::all();
        }
        $nofakturpriangan = \App\Services\NomorDokumenService::previewFaktur();
        $ppnPersen = SistemConfig::getPpnPersen();
        $pelanggans = Pelanggan::where('is_active', true)->orderBy('nama_instansi')->get();
        $salesAes = SalesAe::where('is_active', true)->orderBy('nama_ae')->get();
        $configs = [
            'ppn_persen'     => $ppnPersen,
            'jam_cutoff'     => SistemConfig::getJamCutoff(),
            'is_past_cutoff' => SistemConfig::isPastCutoff(),
        ];
        return view('pages.iklan.transaksi.priangan.create', compact('nofakturpriangan', 'iklanpriangan', 'ppnPersen', 'pelanggans', 'salesAes', 'configs'));
    }

    public function cetak($id)
    {
        $transaksi = TransaksiPriangan::with(['iklanpriangan', 'salesAe', 'pelanggan'])->findOrFail($id);
        return view('pages.iklan.transaksi.priangan.cetak', compact('transaksi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_transaksipriangan'     => 'required|date',
            'nama_pemasangpriangan'         => 'required|string',
            'id_iklanpriangan'              => 'required',
            'tanggal_muatiklanpriangan'     => 'required|date',
            'harga_transaksipriangan'       => 'required',
            'jumlahbayar_transaksipriangan' => 'required',
        ]);

        // Bersihkan format angka
        $harga_bersih    = (int) str_replace(['.', ','], '', $request->harga_transaksipriangan);
        $diskon_bersih   = (int) str_replace(['.', ','], '', $request->diskon_transaksipriangan ?? 0);
        $dibayar_bersih  = (int) str_replace(['.', ','], '', $request->jumlahbayar_transaksipriangan ?? 0);
        $qty_bersih      = max(1, (int) ($request->total_muatiklanpriangan ?? 1));

        // Kalkulasi Keuangan Server-Side (Standar PPN Eksklusif)
        $total_omset    = $harga_bersih * $qty_bersih;
        $subtotal       = max(0, $total_omset - $diskon_bersih);
        $ppnPersen      = SistemConfig::getPpnPersen();
        $ppn            = (int) round($subtotal * ($ppnPersen / 100));
        $total_tagihan  = $subtotal + $ppn;
        $piutang_bersih = max(0, $total_tagihan - $dibayar_bersih);

        // Komisi Perantara Fleksibel
        $komisionerNama = $request->filled('komisioner') ? trim($request->komisioner) : null;
        $komisiPersen   = $request->filled('komisi_persen') ? (float) $request->komisi_persen : 0;
        $komisi_input   = $request->filled('komisi_transaksipriangan') ? (int) str_replace(['.', ','], '', $request->komisi_transaksipriangan) : 0;
        
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

        // Insentif Sales AE
        $insentifPersen = $request->filled('insentif_persen') ? (float) $request->insentif_persen : 0;
        $insentif_input = $request->filled('insentif_transaksipriangan') ? (int) str_replace(['.', ','], '', $request->insentif_transaksipriangan) : 0;
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
        $salesNama = $request->sales_iklanpriangan;
        if ($salesAeId) {
            $ae = SalesAe::find($salesAeId);
            if ($ae) {
                $salesNama = $ae->kode_ae . ' - ' . $ae->nama_ae;
            }
        } elseif (!$salesNama) {
            $salesNama = 'Front Office (Loket Langsung)';
        }

        DB::beginTransaction();
        try {
            // Resolusi Pelanggan
            $pelangganId = Pelanggan::resolveFromRequest($request, 'nama_pemasangpriangan', 'alamat_pemasangpriangan', 'no_telp');

            // Generate nomor faktur sekuensial atomik bebas bentrok
            $noFaktur = TransaksiPriangan::createCode();

            $transaksi = TransaksiPriangan::create([
                'nofakturpriangan'              => $noFaktur,
                'tanggal_transaksipriangan'     => $request->tanggal_transaksipriangan,
                'nama_pemasangpriangan'         => $request->nama_pemasangpriangan,
                'pelanggan_id'                  => $pelangganId,
                'nama_pic'                      => $request->nama_pic,
                'jabatan_pic'                   => $request->jabatan_pic,
                'no_telp'                       => $request->no_telp,
                'produk_jasa'                   => $request->produk_jasa,
                'no_spk'                        => $request->no_spk,
                'alamat_pemasangpriangan'       => $request->alamat_pemasangpriangan ?? '-',
                'id_iklanpriangan'              => $request->id_iklanpriangan,
                'materi_iklan'                  => $request->materi_iklan,
                'durasi_detik'                  => $request->durasi_detik ?? 30,
                'jam_tayang'                    => $request->jam_tayang,
                'status_tayang'                 => $request->status_tayang ?? 'terjadwal',
                'sales_ae_id'                   => $salesAeId,
                'sales_iklanpriangan'           => $salesNama,
                'komisioner'                    => $komisionerNama,
                'komisi_persen'                 => $komisiPersen,
                'insentif_persen'               => $insentifPersen,
                'tanggal_muatiklanpriangan'     => $request->tanggal_muatiklanpriangan,
                'total_muatiklanpriangan'       => $qty_bersih,
                'harga_transaksipriangan'       => $harga_bersih,
                'diskon_transaksipriangan'      => $diskon_bersih,
                'insentif_transaksipriangan'    => $insentif,
                'komisi_transaksipriangan'      => $komisi,
                'ppn_transaksipriangan'         => $ppn,
                'ppn_persen'                    => $ppnPersen,
                'totaltagihan_transaksipriangan'=> $total_tagihan,
                'jumlahbayar_transaksipriangan' => $dibayar_bersih,
                'piutang_transaksipriangan'     => $piutang_bersih,
            ]);

            // Catat Pembayaran Pertama / DP jika ada uang yang disetorkan
            if ($dibayar_bersih > 0) {
                PembayaranIklan::create([
                    'nokwitansi'     => PembayaranIklan::createCode('PTV'),
                    'media_type'     => 'priangan',
                    'transaksi_id'   => $transaksi->id,
                    'nofaktur'       => $transaksi->nofakturpriangan,
                    'tanggal_bayar'  => $transaksi->tanggal_transaksipriangan,
                    'nominal_bayar'  => $dibayar_bersih,
                    'potongan_pph23' => 0,
                    'metode_bayar'   => 'Tunai_Kasir',
                    'nama_penyetor'  => $transaksi->nama_pemasangpriangan,
                    'catatan'        => 'Pembayaran awal saat pemesanan iklan Priangan TV di FO',
                    'kasir_user_id'  => Auth::id(),
                ]);
            }

            // Auto-generate SPPI (Insertion Order Siar)
            Sppi::create([
                'nomor_sppi'   => Sppi::createCode('PTV'),
                'media_type'   => 'priangan',
                'transaksi_id' => $transaksi->id,
                'nofaktur'     => $transaksi->nofakturpriangan,
                'tanggal_sppi' => $transaksi->tanggal_transaksipriangan,
                'status_sppi'  => 'Belum_Ditandatangani',
            ]);

            // Sinkronisasi Terpadu ke Buku Faktur Keuangan (1 Transaksi = 1 Faktur Resmi)
            FakturSyncService::syncFromTransaksi($transaksi, 'priangan');

            DB::commit();

            return redirect()->route('transaksipriangan.index')
                ->with('success', "Order Iklan Priangan TV {$noFaktur} berhasil disimpan!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaksi = TransaksiPriangan::with(['iklanpriangan', 'salesAe', 'pelanggan'])->findOrFail($id);
        return view('pages.iklan.transaksi.priangan.cetak', compact('transaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $transaksi = TransaksiPriangan::with(['iklanpriangan', 'salesAe', 'pelanggan'])->findOrFail($id);
        $iklanpriangan = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'PTV'))->get();
        if ($iklanpriangan->isEmpty()) {
            $iklanpriangan = JenisIklan::all();
        }
        $nofakturpriangan = $transaksi->nofakturpriangan;
        $ppnPersen = $transaksi->ppn_persen ?: SistemConfig::getPpnPersen();
        $pelanggans = Pelanggan::where('is_active', true)->orderBy('nama_instansi')->get();
        $salesAes = SalesAe::where('is_active', true)->orderBy('nama_ae')->get();
        $configs = [
            'ppn_persen'     => $ppnPersen,
            'jam_cutoff'     => SistemConfig::getJamCutoff(),
            'is_past_cutoff' => SistemConfig::isPastCutoff(),
        ];

        return view('pages.iklan.transaksi.priangan.create', compact('transaksi', 'nofakturpriangan', 'iklanpriangan', 'ppnPersen', 'pelanggans', 'salesAes', 'configs'));
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

        // Kalkulasi Keuangan Server-Side (Standar PPN Eksklusif, Selaras dengan Store)
        $total_omset    = $harga_bersih * $qty_bersih;
        $subtotal       = max(0, $total_omset - $diskon_bersih);
        $ppnPersen      = SistemConfig::getPpnPersen();
        $ppn            = (int) round($subtotal * ($ppnPersen / 100));
        $total_tagihan  = $subtotal + $ppn;
        $piutang_bersih = max(0, $total_tagihan - $dibayar_bersih);

        // Komisi Perantara Fleksibel
        $komisionerNama = $request->filled('komisioner') ? trim($request->komisioner) : null;
        $komisiPersen   = $request->filled('komisi_persen') ? (float) $request->komisi_persen : 0;
        $komisi_input   = $request->filled('komisi_transaksipriangan') ? (int) str_replace(['.', ','], '', $request->komisi_transaksipriangan) : 0;
        
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

        // Insentif Sales AE Fleksibel
        $insentifPersen = $request->filled('insentif_persen') ? (float) $request->insentif_persen : 0;
        $insentif_input = $request->filled('insentif_transaksipriangan') ? (int) str_replace(['.', ','], '', $request->insentif_transaksipriangan) : 0;
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

        DB::beginTransaction();
        try {
            $transaksi = TransaksiPriangan::findOrFail($id);

            $transaksi->update([
                'tanggal_transaksipriangan'     => $request->tanggal_transaksipriangan,
                'nama_pemasangpriangan'         => $request->nama_pemasangpriangan,
                'pelanggan_id'                  => $request->pelanggan_id ?? $transaksi->pelanggan_id,
                'alamat_pemasangpriangan'       => $request->alamat_pemasangpriangan,
                'id_iklanpriangan'              => $request->id_iklanpriangan,
                'sales_ae_id'                   => $request->sales_ae_id ?? $transaksi->sales_ae_id,
                'sales_iklanpriangan'           => $request->sales_iklanpriangan,
                'komisioner'                    => $komisionerNama,
                'komisi_persen'                 => $komisiPersen,
                'insentif_persen'               => $insentifPersen,
                'tanggal_muatiklanpriangan'     => $request->tanggal_muatiklanpriangan,
                'total_muatiklanpriangan'       => $qty_bersih,
                'harga_transaksipriangan'       => $harga_bersih,
                'diskon_transaksipriangan'      => $diskon_bersih,
                'insentif_transaksipriangan'    => $insentif,
                'komisi_transaksipriangan'      => $komisi,
                'ppn_transaksipriangan'         => $ppn,
                'ppn_persen'                    => $ppnPersen,
                'totaltagihan_transaksipriangan'=> $total_tagihan,
                'jumlahbayar_transaksipriangan' => $dibayar_bersih,
                'piutang_transaksipriangan'     => $piutang_bersih,
            ]);

            // Sinkronisasi Pembayaran Awal di Kwitansi Kasir
            $pembayaranAwal = PembayaranIklan::where('transaksi_id', $transaksi->id)
                ->where('media_type', 'priangan')
                ->oldest()
                ->first();

            if ($dibayar_bersih > 0) {
                if ($pembayaranAwal) {
                    $pembayaranAwal->update([
                        'nominal_bayar' => $dibayar_bersih,
                        'nama_penyetor' => $transaksi->nama_pemasangpriangan,
                    ]);
                } else {
                    PembayaranIklan::create([
                        'nokwitansi'     => PembayaranIklan::createCode('PTV'),
                        'media_type'     => 'priangan',
                        'transaksi_id'   => $transaksi->id,
                        'nofaktur'       => $transaksi->nofakturpriangan,
                        'tanggal_bayar'  => $transaksi->tanggal_transaksipriangan,
                        'nominal_bayar'  => $dibayar_bersih,
                        'potongan_pph23' => 0,
                        'metode_bayar'   => 'Tunai_Kasir',
                        'nama_penyetor'  => $transaksi->nama_pemasangpriangan,
                        'catatan'        => 'Pembayaran awal transaksi Priangan TV (Revisi Edit)',
                        'kasir_user_id'  => Auth::id(),
                    ]);
                }
            } elseif ($pembayaranAwal && $dibayar_bersih == 0 && str_contains($pembayaranAwal->catatan, 'Pembayaran awal')) {
                $pembayaranAwal->delete();
            }

            // Sinkronisasi Terpadu ke Buku Faktur Keuangan (1 Transaksi = 1 Faktur Resmi)
            FakturSyncService::syncFromTransaksi($transaksi, 'priangan');

            DB::commit();

            return redirect()->route('transaksipriangan.index')
                ->with('success', 'Data Transaksi Priangan TV Berhasil Diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui transaksi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Prosedur Pembatalan Transaksi Iklan Priangan TV
     */
    public function batalkan(Request $request, string $id)
    {
        $request->validate([
            'alasan_batal' => 'required|string|min:5|max:500',
        ]);

        $transaksi = TransaksiPriangan::findOrFail($id);
        $transaksi->update([
            'status_tayang' => 'batal',
        ]);

        // Sinkronisasi status pembatalan ke Buku Faktur Keuangan
        FakturSyncService::cancelFaktur($transaksi->nofakturpriangan, $request->alasan_batal);

        return redirect()->route('transaksipriangan.index')
            ->with('success', "Order Priangan TV {$transaksi->nofakturpriangan} resmi DIBATALKAN.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = TransaksiPriangan::findOrFail($id);

        if ($data->jumlahbayar_transaksipriangan > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Order Priangan TV yang sudah memiliki riwayat pembayaran tidak dapat dihapus permanen. Gunakan prosedur Pembatalan Transaksi.'
            ], 422);
        }

        FakturSyncService::deleteFaktur($data->nofakturpriangan);
        PembayaranIklan::where('transaksi_id', $data->id)->where('media_type', 'priangan')->delete();
        Sppi::where('transaksi_id', $data->id)->where('media_type', 'priangan')->delete();
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}

