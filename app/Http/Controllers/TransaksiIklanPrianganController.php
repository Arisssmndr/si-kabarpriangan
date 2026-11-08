<?php

namespace App\Http\Controllers;

use App\Models\JenisIklan;
use App\Models\Pelanggan;
use App\Models\PembayaranIklan;
use App\Models\SalesAe;
use App\Models\SistemConfig;
use App\Models\Sppi;
use App\Models\TransaksiPriangan;
use App\Services\FakturQrService;
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

    public static function getPortalList(): array
    {
        return [
            ['id' => 'kabar-priangan.com', 'nama' => 'kabar-priangan.com', 'wilayah' => 'Pusat / Tasikmalaya Raya'],
            ['id' => 'kabar-tasikmalaya.com', 'nama' => 'kabar-tasikmalaya.com', 'wilayah' => 'Kota & Kab. Tasikmalaya'],
            ['id' => 'kabar-singaparna.com', 'nama' => 'kabar-singaparna.com', 'wilayah' => 'Kabupaten Tasikmalaya'],
            ['id' => 'kabar-sumedang.com', 'nama' => 'kabar-sumedang.com', 'wilayah' => 'Kabupaten Sumedang'],
            ['id' => 'kabar-ciamis.com', 'nama' => 'kabar-ciamis.com', 'wilayah' => 'Kabupaten Ciamis'],
            ['id' => 'kabar-banjar.com', 'nama' => 'kabar-banjar.com', 'wilayah' => 'Kota Banjar'],
            ['id' => 'kabar-garut.com', 'nama' => 'kabar-garut.com', 'wilayah' => 'Kabupaten Garut'],
            ['id' => 'kabar-bandung.com', 'nama' => 'kabar-bandung.com', 'wilayah' => 'Bandung Raya'],
            ['id' => 'kabar-pangandaran.com', 'nama' => 'kabar-pangandaran.com', 'wilayah' => 'Kabupaten Pangandaran'],
            ['id' => 'kabarpriangantv.com', 'nama' => 'kabarpriangantv.com', 'wilayah' => 'Portal Digital Priangan TV'],
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(\App\Services\KalenderNasionalService $kalenderService)
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
            'tarif_portal'   => (int) SistemConfig::get('tarif_portal_berita', 750000),
        ];
        $portalList = self::getPortalList();
        $kalenderAwal = $kalenderService->getKalenderBulan((int) date('Y'), (int) date('n'));
        return view('pages.iklan.transaksi.priangan.create', compact('nofakturpriangan', 'iklanpriangan', 'ppnPersen', 'pelanggans', 'salesAes', 'configs', 'portalList', 'kalenderAwal'));
    }

    public function cetak($id)
    {
        $transaksi = TransaksiPriangan::with(['iklanpriangan', 'salesAe', 'pelanggan'])->findOrFail($id);
        $qrFaktur  = FakturQrService::resolveForCetak($transaksi, 'priangan');
        $qrSvg     = $qrFaktur ? FakturQrService::renderSvg($qrFaktur, 106) : null;

        return view('pages.iklan.transaksi.priangan.cetak', compact('transaksi', 'qrFaktur', 'qrSvg'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'tanggal_transaksipriangan.required' => 'Tanggal transaksi siaran TV wajib diisi.',
            'tanggal_transaksipriangan.date'     => 'Format tanggal transaksi tidak valid.',
            'nama_pemasangpriangan.required'     => 'Nama pemesan / instansi pemasang wajib diisi.',
            'id_iklanpriangan.required'          => 'Program / format siaran TV wajib dipilih.',
            'id_iklanpriangan.exists'            => 'Program siaran TV yang dipilih tidak valid atau sudah nonaktif.',
            'sales_ae_id.required'               => 'Sales / AE wajib dipilih.',
            'sales_ae_id.exists'                 => 'Sales / AE yang dipilih tidak terdaftar di sistem.',
            'tanggal_muatiklanpriangan.required' => 'Tanggal siaran iklan TV wajib diisi.',
            'jenis_pembayaran.required'          => 'Skema pembayaran wajib dipilih (Tunai atau Kredit).',
            'jenis_pembayaran.in'                => 'Skema pembayaran harus berupa Tunai atau Kredit.',
            'materi_iklan.required'              => 'Judul atau materi siaran TV wajib diisi.',
            'durasi_detik.between'               => 'Durasi siaran harus berada di antara 5 sampai 7.200 detik.',
            'harga_transaksipriangan.required'   => 'Harga transaksi siaran TV wajib diisi.',
            'jumlahbayar_transaksipriangan.required' => 'Jumlah bayar wajib diisi.',
        ];

        $request->validate([
            'tanggal_transaksipriangan'     => 'required|date',
            'nama_pemasangpriangan'         => 'required|string',
            'alamat_pemasangpriangan'       => 'nullable|string',
            'id_iklanpriangan'              => 'required|exists:jenis_iklan,id',
            'sales_ae_id'                   => 'required|exists:sales_ae,id',
            'sales_iklanpriangan'           => 'nullable|string',
            'tanggal_muatiklanpriangan'     => 'required|date',
            'jenis_pembayaran'              => 'required|in:Tunai,Kredit',
            'materi_iklan'                  => 'required|string',
            'durasi_detik'                  => 'nullable|integer|between:5,7200',
            'harga_transaksipriangan'       => 'required',
            'jumlahbayar_transaksipriangan' => 'required',
        ], $messages);

        // Bersihkan format angka
        $harga_bersih    = (int) str_replace(['.', ','], '', $request->harga_transaksipriangan);
        $diskon_bersih   = (int) str_replace(['.', ','], '', $request->diskon_transaksipriangan ?? 0);
        $dibayar_bersih  = (int) str_replace(['.', ','], '', $request->jumlahbayar_transaksipriangan ?? 0);
        $qty_bersih      = max(1, (int) ($request->total_muatiklanpriangan ?? 1));

        $jenisIklanModel = JenisIklan::find($request->id_iklanpriangan);
        $isBonus = $jenisIklanModel && ($jenisIklanModel->tipe_dasar === 'BONUS' || 
                                       stripos($jenisIklanModel->nama_jenis, 'bonus') !== false || 
                                       stripos($jenisIklanModel->nama_jenis, 'barter') !== false || 
                                       stripos($jenisIklanModel->nama_jenis, 'csr') !== false);

        if (!$isBonus && $harga_bersih < 1) {
            return back()->withInput()->withErrors([
                'harga_transaksipriangan' => 'Harga transaksi siaran TV harus lebih besar dari Rp 0 (kecuali kategori Bonus/Barter/CSR).'
            ]);
        }

        // Kalkulasi Keuangan Server-Side (Standar PPN Eksklusif)
        $total_omset    = $harga_bersih * $qty_bersih;
        $subtotal       = max(0, $total_omset - $diskon_bersih);
        $ppnPersen      = SistemConfig::getPpnPersen();
        $ppn            = (int) round($subtotal * ($ppnPersen / 100));
        $total_tagihan  = $subtotal + $ppn;

        // Aturan Pembayaran Tunai & Kredit
        if ($request->jenis_pembayaran === 'Tunai') {
            if ($total_tagihan > 0) {
                $dibayar_bersih = $total_tagihan;
            }
        } else {
            if ($dibayar_bersih > $total_tagihan) {
                return back()->withInput()->withErrors([
                    'jumlahbayar_transaksipriangan' => 'Jumlah bayar (Rp ' . number_format($dibayar_bersih, 0, ',', '.') . ') tidak boleh melebihi total tagihan (Rp ' . number_format($total_tagihan, 0, ',', '.') . ').'
                ]);
            }
        }
        if ($dibayar_bersih < 0) {
            return back()->withInput()->withErrors([
                'jumlahbayar_transaksipriangan' => 'Jumlah bayar tidak boleh bernilai negatif.'
            ]);
        }

        $piutang_bersih = max(0, $total_tagihan - $dibayar_bersih);

        // Tahap 1: Komisi Komisioner (KH / Eksternal) diambil langsung dari DPP (Netto / Subtotal)
        $komisionerNama = $request->filled('komisioner') ? trim($request->komisioner) : null;
        $komisiPersen   = $request->filled('komisi_persen') ? (float) $request->komisi_persen : 0;
        $komisi_input   = $request->filled('komisi_transaksipriangan') ? (int) str_replace(['.', ','], '', $request->komisi_transaksipriangan) : 0;

        if ($subtotal <= 0) {
            $komisi = 0;
            $komisiPersen = 0;
        } elseif ($komisi_input > 0) {
            $komisi = min($subtotal, $komisi_input);
            if ($komisiPersen == 0) {
                $komisiPersen = round(($komisi / $subtotal) * 100, 2);
            }
        } elseif ($komisiPersen > 0) {
            $komisi = (int) round(($subtotal * $komisiPersen) / 100);
        } else {
            $komisi = 0;
            $komisiPersen = 0;
        }

        // Tahap 2: Insentif Sales (AE / Internal) diambil dari Sisa DPP setelah dikurangi Komisi Komisioner (jika KH=0 otomatis dari DPP penuh)
        $sisaDppInsentif = max(0, $subtotal - $komisi);
        $insentifPersen  = $request->filled('insentif_persen') ? (float) $request->insentif_persen : 0;
        $insentif_input  = $request->filled('insentif_transaksipriangan') ? (int) str_replace(['.', ','], '', $request->insentif_transaksipriangan) : 0;

        if ($sisaDppInsentif <= 0) {
            $insentif = 0;
            $insentifPersen = 0;
        } elseif ($insentif_input > 0) {
            $insentif = min($sisaDppInsentif, $insentif_input);
            if ($insentifPersen == 0) {
                $insentifPersen = round(($insentif / $sisaDppInsentif) * 100, 2);
            }
        } elseif ($insentifPersen > 0) {
            $insentif = (int) round(($sisaDppInsentif * $insentifPersen) / 100);
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

        // Upload File Materi Iklan jika ada
        $fileMateriPath = null;
        if ($request->hasFile('file_materi')) {
            $file = $request->file('file_materi');
            $cleanFaktur = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nofakturpriangan ?? time());
            $fileName = 'materi_ptv_' . time() . '_' . $cleanFaktur . '.' . $file->getClientOriginalExtension();
            $fileMateriPath = $file->storeAs('materi_iklan', $fileName, 'public');
        }

        // Resolusi Multi-Date Jadwal Tayang
        $rawDates = $request->tanggal_muat_list;
        if (is_string($rawDates)) {
            $rawDates = array_values(array_filter(array_map('trim', explode(',', $rawDates))));
        } elseif (!is_array($rawDates)) {
            $rawDates = [];
        }
        if (empty($rawDates) && $request->filled('tanggal_muatiklanpriangan')) {
            $rawDates = [$request->tanggal_muatiklanpriangan];
        }
        $primaryTanggalMuat = $rawDates[0] ?? $request->tanggal_muatiklanpriangan ?? now()->addDay()->toDateString();
        $tanggalMuatListStr = !empty($rawDates) ? implode(',', $rawDates) : $primaryTanggalMuat;

        // Resolusi Platforms / Saluran & Portals
        $rawPortals = $request->portal_iklanpriangan ?? $request->portal_iklanonline;
        if (is_string($rawPortals)) {
            $portals = array_values(array_filter(array_map('trim', explode(',', $rawPortals))));
        } elseif (is_array($rawPortals)) {
            $portals = $rawPortals;
        } else {
            $portals = [];
        }

        $platforms = $request->platforms_publikasi;
        if (is_string($platforms)) {
            $platforms = array_values(array_filter(array_map('trim', explode(',', $platforms))));
        } elseif (!is_array($platforms)) {
            $platforms = [];
        }

        if (!empty($portals)) {
            foreach ($portals as $p) {
                if (!in_array($p, $platforms)) {
                    $platforms[] = $p;
                }
            }
        }

        if (empty($platforms) && $request->filled('kategori_tv_form')) {
            $tvMap = [
                'PORTAL_WEB'     => 'Portal Web Priangan',
                'PODCAST'        => 'Studio Podcast TV',
                'DISPLAY_MEDSOS' => 'Medsos Priangan TV',
                'BANNER_WEB'     => 'Website Priangan TV',
                'VIDEO_LIPSUS'   => 'Liputan Khusus TV',
            ];
            $platforms = [$tvMap[$request->kategori_tv_form] ?? 'Priangan TV Channel'];
        }
        if (empty($platforms)) {
            $platforms = ['Priangan TV Channel'];
        }

        // Resolusi ID Produk & Deskripsi Produk Jasa
        $idIklanPriangan = $request->id_iklanpriangan;
        if (empty($idIklanPriangan) || !is_numeric($idIklanPriangan)) {
            $idIklanPriangan = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'PTV'))->first()?->id ?? JenisIklan::first()?->id ?? 12;
        }

        $produkJasa = $request->produk_jasa;
        if (empty($produkJasa) && !empty($portals)) {
            $produkJasa = count($portals) > 3 ? 'Jaringan ' . count($portals) . ' Portal Web' : implode(' + ', $portals);
        }
        if (empty($produkJasa)) {
            $produkJasa = 'Iklan Priangan TV';
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
                'produk_jasa'                   => $produkJasa,
                'no_spk'                        => $request->no_spk,
                'alamat_pemasangpriangan'       => $request->alamat_pemasangpriangan ?? '-',
                'id_iklanpriangan'              => $idIklanPriangan,
                'platforms_publikasi'           => $platforms,
                'materi_iklan'                  => $request->materi_iklan ?? $request->keterangan_materi,
                'file_materi'                   => $fileMateriPath,
                'url_materi_iklan'              => $request->url_materi_iklan,
                'catatan_redaksi'               => $request->catatan_redaksi,
                'durasi_detik'                  => $request->durasi_detik ?? 30,
                'jam_tayang'                    => $request->jam_tayang,
                'status_tayang'                 => $request->status_tayang ?? 'terjadwal',
                'sales_ae_id'                   => $salesAeId,
                'sales_iklanpriangan'           => $salesNama,
                'komisioner'                    => $komisionerNama,
                'komisi_persen'                 => $komisiPersen,
                'insentif_persen'               => $insentifPersen,
                'tanggal_muatiklanpriangan'     => $primaryTanggalMuat,
                'tanggal_muat_list'             => $tanggalMuatListStr,
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
        $transaksi = TransaksiPriangan::with(['iklanpriangan', 'salesAe', 'pelanggan', 'pembayaranList.kasirUser', 'sppi'])->findOrFail($id);
        return view('pages.iklan.transaksi.priangan.show', compact('transaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, \App\Services\KalenderNasionalService $kalenderService)
    {
        $transaksi = TransaksiPriangan::with(['iklanpriangan', 'salesAe', 'pelanggan'])->findOrFail($id);

        if ($lockReason = FakturQrService::getAccountingOrCutoffLockReason($transaksi, 'priangan')) {
            return redirect()->route('transaksipriangan.index')
                ->with('error', "Transaksi {$transaksi->nofakturpriangan} tidak dapat diedit: {$lockReason}");
        }
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
            'tarif_portal'   => (int) SistemConfig::get('tarif_portal_berita', 750000),
        ];
        $portalList = self::getPortalList();
        $kalenderAwal = $kalenderService->getKalenderBulan((int) date('Y'), (int) date('n'));

        return view('pages.iklan.transaksi.priangan.create', compact('transaksi', 'nofakturpriangan', 'iklanpriangan', 'ppnPersen', 'pelanggans', 'salesAes', 'configs', 'portalList', 'kalenderAwal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $messages = [
            'tanggal_transaksipriangan.required' => 'Tanggal transaksi siaran TV wajib diisi.',
            'tanggal_transaksipriangan.date'     => 'Format tanggal transaksi tidak valid.',
            'nama_pemasangpriangan.required'     => 'Nama pemesan / instansi pemasang wajib diisi.',
            'id_iklanpriangan.required'          => 'Program / format siaran TV wajib dipilih.',
            'id_iklanpriangan.exists'            => 'Program siaran TV yang dipilih tidak valid atau sudah nonaktif.',
            'sales_ae_id.required'               => 'Sales / AE wajib dipilih.',
            'sales_ae_id.exists'                 => 'Sales / AE yang dipilih tidak terdaftar di sistem.',
            'tanggal_muatiklanpriangan.required' => 'Tanggal siaran iklan TV wajib diisi.',
            'jenis_pembayaran.required'          => 'Skema pembayaran wajib dipilih (Tunai atau Kredit).',
            'jenis_pembayaran.in'                => 'Skema pembayaran harus berupa Tunai atau Kredit.',
            'materi_iklan.required'              => 'Judul atau materi siaran TV wajib diisi.',
            'durasi_detik.between'               => 'Durasi siaran harus berada di antara 5 sampai 7.200 detik.',
            'harga_transaksipriangan.required'   => 'Harga transaksi siaran TV wajib diisi.',
            'jumlahbayar_transaksipriangan.required' => 'Jumlah bayar wajib diisi.',
        ];

        $request->validate([
            'tanggal_transaksipriangan'     => 'required|date',
            'nama_pemasangpriangan'         => 'required|string',
            'alamat_pemasangpriangan'       => 'nullable|string',
            'id_iklanpriangan'              => 'required|exists:jenis_iklan,id',
            'sales_ae_id'                   => 'required|exists:sales_ae,id',
            'sales_iklanpriangan'           => 'nullable|string',
            'tanggal_muatiklanpriangan'     => 'required|date',
            'jenis_pembayaran'              => 'required|in:Tunai,Kredit',
            'materi_iklan'                  => 'required|string',
            'durasi_detik'                  => 'nullable|integer|between:5,7200',
            'harga_transaksipriangan'       => 'required',
            'jumlahbayar_transaksipriangan' => 'required',
        ], $messages);

        $harga_bersih    = (int) str_replace(['.', ','], '', $request->harga_transaksipriangan);
        $diskon_bersih   = (int) str_replace(['.', ','], '', $request->diskon_transaksipriangan ?? 0);
        $dibayar_bersih  = (int) str_replace(['.', ','], '', $request->jumlahbayar_transaksipriangan ?? 0);
        $qty_bersih      = max(1, (int) ($request->total_muatiklanpriangan ?? 1));

        $jenisIklanModel = JenisIklan::find($request->id_iklanpriangan);
        $isBonus = $jenisIklanModel && ($jenisIklanModel->tipe_dasar === 'BONUS' || 
                                       stripos($jenisIklanModel->nama_jenis, 'bonus') !== false || 
                                       stripos($jenisIklanModel->nama_jenis, 'barter') !== false || 
                                       stripos($jenisIklanModel->nama_jenis, 'csr') !== false);

        if (!$isBonus && $harga_bersih < 1) {
            return back()->withInput()->withErrors([
                'harga_transaksipriangan' => 'Harga transaksi siaran TV harus lebih besar dari Rp 0 (kecuali kategori Bonus/Barter/CSR).'
            ]);
        }

        // Kalkulasi Keuangan Server-Side (Standar PPN Eksklusif, Selaras dengan Store)
        $total_omset    = $harga_bersih * $qty_bersih;
        $subtotal       = max(0, $total_omset - $diskon_bersih);
        $ppnPersen      = SistemConfig::getPpnPersen();
        $ppn            = (int) round($subtotal * ($ppnPersen / 100));
        $total_tagihan  = $subtotal + $ppn;

        // Aturan Pembayaran Tunai & Kredit
        if ($request->jenis_pembayaran === 'Tunai') {
            if ($total_tagihan > 0) {
                $dibayar_bersih = $total_tagihan;
            }
        } else {
            if ($dibayar_bersih > $total_tagihan) {
                return back()->withInput()->withErrors([
                    'jumlahbayar_transaksipriangan' => 'Jumlah bayar (Rp ' . number_format($dibayar_bersih, 0, ',', '.') . ') tidak boleh melebihi total tagihan (Rp ' . number_format($total_tagihan, 0, ',', '.') . ').'
                ]);
            }
        }
        if ($dibayar_bersih < 0) {
            return back()->withInput()->withErrors([
                'jumlahbayar_transaksipriangan' => 'Jumlah bayar tidak boleh bernilai negatif.'
            ]);
        }

        $piutang_bersih = max(0, $total_tagihan - $dibayar_bersih);

        $salesAeId = $request->sales_ae_id;
        $ae = $salesAeId ? SalesAe::find($salesAeId) : null;
        $salesNama = $ae ? ($ae->kode_ae . ' - ' . $ae->nama_ae) : ($request->sales_iklanpriangan ?: 'Front Office (Loket Langsung)');

        // Tahap 1: Komisi Komisioner (KH / Eksternal) diambil langsung dari DPP (Netto / Subtotal)
        $komisionerNama = $request->filled('komisioner') ? trim($request->komisioner) : null;
        $komisiPersen   = $request->filled('komisi_persen') ? (float) $request->komisi_persen : 0;
        $komisi_input   = $request->filled('komisi_transaksipriangan') ? (int) str_replace(['.', ','], '', $request->komisi_transaksipriangan) : 0;

        if ($subtotal <= 0) {
            $komisi = 0;
            $komisiPersen = 0;
        } elseif ($komisi_input > 0) {
            $komisi = min($subtotal, $komisi_input);
            if ($komisiPersen == 0) {
                $komisiPersen = round(($komisi / $subtotal) * 100, 2);
            }
        } elseif ($komisiPersen > 0) {
            $komisi = (int) round(($subtotal * $komisiPersen) / 100);
        } else {
            $komisi = 0;
            $komisiPersen = 0;
        }

        // Tahap 2: Insentif Sales (AE / Internal) diambil dari Sisa DPP setelah dikurangi Komisi Komisioner (jika KH=0 otomatis dari DPP penuh)
        $sisaDppInsentif = max(0, $subtotal - $komisi);
        $insentifPersen  = $request->filled('insentif_persen') ? (float) $request->insentif_persen : 0;
        $insentif_input  = $request->filled('insentif_transaksipriangan') ? (int) str_replace(['.', ','], '', $request->insentif_transaksipriangan) : 0;

        if ($sisaDppInsentif <= 0) {
            $insentif = 0;
            $insentifPersen = 0;
        } elseif ($insentif_input > 0) {
            $insentif = min($sisaDppInsentif, $insentif_input);
            if ($insentifPersen == 0) {
                $insentifPersen = round(($insentif / $sisaDppInsentif) * 100, 2);
            }
        } elseif ($insentifPersen > 0) {
            $insentif = (int) round(($sisaDppInsentif * $insentifPersen) / 100);
        } else {
            $insentif = 0;
            $insentifPersen = 0;
        }

        // Upload File Materi Iklan jika ada
        $fileMateriPath = null;
        if ($request->hasFile('file_materi')) {
            $file = $request->file('file_materi');
            $cleanFaktur = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nofakturpriangan ?? time());
            $fileName = 'materi_ptv_' . time() . '_' . $cleanFaktur . '.' . $file->getClientOriginalExtension();
            $fileMateriPath = $file->storeAs('materi_iklan', $fileName, 'public');
        }

        // Resolusi Multi-Date Jadwal Tayang
        $rawDates = $request->tanggal_muat_list;
        if (is_string($rawDates)) {
            $rawDates = array_values(array_filter(array_map('trim', explode(',', $rawDates))));
        } elseif (!is_array($rawDates)) {
            $rawDates = [];
        }
        if (empty($rawDates) && $request->filled('tanggal_muatiklanpriangan')) {
            $rawDates = [$request->tanggal_muatiklanpriangan];
        }

        // Resolusi Platforms / Saluran & Portals
        $rawPortals = $request->portal_iklanpriangan ?? $request->portal_iklanonline;
        if (is_string($rawPortals)) {
            $portals = array_values(array_filter(array_map('trim', explode(',', $rawPortals))));
        } elseif (is_array($rawPortals)) {
            $portals = $rawPortals;
        } else {
            $portals = [];
        }

        $platforms = $request->platforms_publikasi;
        if (is_string($platforms)) {
            $platforms = array_values(array_filter(array_map('trim', explode(',', $platforms))));
        } elseif (!is_array($platforms)) {
            $platforms = [];
        }

        if (!empty($portals)) {
            foreach ($portals as $p) {
                if (!in_array($p, $platforms)) {
                    $platforms[] = $p;
                }
            }
        }

        DB::beginTransaction();
        try {
            $transaksi = TransaksiPriangan::findOrFail($id);

            if ($qrLockError = FakturQrService::guardBeforeUpdate($transaksi, 'priangan', (float) $dibayar_bersih, (string) $request->nama_pemasangpriangan, (float) $total_tagihan)) {
                DB::rollBack();
                return back()->withErrors(['error' => $qrLockError])->withInput();
            }

            if (empty($platforms) && $request->filled('kategori_tv_form')) {
                $tvMap = [
                    'PORTAL_WEB'     => 'Portal Web Priangan',
                    'PODCAST'        => 'Studio Podcast TV',
                    'DISPLAY_MEDSOS' => 'Medsos Priangan TV',
                    'BANNER_WEB'     => 'Website Priangan TV',
                    'VIDEO_LIPSUS'   => 'Liputan Khusus TV',
                ];
                $platforms = [$tvMap[$request->kategori_tv_form] ?? 'Priangan TV Channel'];
            }
            if (empty($platforms)) {
                $platforms = $transaksi->platforms_publikasi ?: ['Priangan TV Channel'];
            }

            $primaryTanggalMuat = $rawDates[0] ?? $request->tanggal_muatiklanpriangan ?? $transaksi->tanggal_muatiklanpriangan;
            $tanggalMuatListStr = !empty($rawDates) ? implode(',', $rawDates) : ($transaksi->tanggal_muat_list ?: $primaryTanggalMuat);

            $idIklanPriangan = (!empty($request->id_iklanpriangan) && is_numeric($request->id_iklanpriangan)) ? $request->id_iklanpriangan : $transaksi->id_iklanpriangan;
            $produkJasa = $request->produk_jasa;
            if (empty($produkJasa) && !empty($portals)) {
                $produkJasa = count($portals) > 3 ? 'Jaringan ' . count($portals) . ' Portal Web' : implode(' + ', $portals);
            }
            if (empty($produkJasa)) {
                $produkJasa = $transaksi->produk_jasa ?: 'Iklan Priangan TV';
            }

            $updateData = [
                'tanggal_transaksipriangan'     => $request->tanggal_transaksipriangan,
                'nama_pemasangpriangan'         => $request->nama_pemasangpriangan,
                'pelanggan_id'                  => $request->pelanggan_id ?? $transaksi->pelanggan_id,
                'nama_pic'                      => $request->nama_pic ?? $transaksi->nama_pic,
                'jabatan_pic'                   => $request->jabatan_pic ?? $transaksi->jabatan_pic,
                'no_telp'                       => $request->no_telp ?? $transaksi->no_telp,
                'produk_jasa'                   => $produkJasa,
                'no_spk'                        => $request->no_spk ?? $transaksi->no_spk,
                'alamat_pemasangpriangan'       => $request->alamat_pemasangpriangan,
                'id_iklanpriangan'              => $idIklanPriangan,
                'platforms_publikasi'           => $platforms ?: $transaksi->platforms_publikasi,
                'materi_iklan'                  => $request->materi_iklan ?? $request->keterangan_materi ?? $transaksi->materi_iklan,
                'durasi_detik'                  => $request->filled('durasi_detik') ? $request->durasi_detik : ($transaksi->durasi_detik ?? 30),
                'jam_tayang'                    => $request->filled('jam_tayang') ? $request->jam_tayang : $transaksi->jam_tayang,
                'url_materi_iklan'              => $request->has('url_materi_iklan') ? $request->url_materi_iklan : $transaksi->url_materi_iklan,
                'catatan_redaksi'               => $request->has('catatan_redaksi') ? $request->catatan_redaksi : $transaksi->catatan_redaksi,
                'sales_ae_id'                   => $salesAeId ?? $transaksi->sales_ae_id,
                'sales_iklanpriangan'           => $salesNama,
                'komisioner'                    => $komisionerNama,
                'komisi_persen'                 => $komisiPersen,
                'insentif_persen'               => $insentifPersen,
                'tanggal_muatiklanpriangan'     => $primaryTanggalMuat,
                'tanggal_muat_list'             => $tanggalMuatListStr,
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
            ];

            if ($fileMateriPath) {
                if ($transaksi->file_materi) {
                    Storage::disk('public')->delete($transaksi->file_materi);
                }
                $updateData['file_materi'] = $fileMateriPath;
            }

            $transaksi->update($updateData);

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

        if ($qrCancelError = FakturQrService::guardBeforeCancelOrDelete($transaksi, 'priangan')) {
            return back()->with('error', $qrCancelError);
        }

        $transaksi->update([
            'status_tayang' => 'batal',
        ]);

        // Sinkronisasi status pembatalan ke Buku Faktur Keuangan & QR Pembayaran
        FakturSyncService::cancelFaktur($transaksi->nofakturpriangan, $request->alasan_batal);
        FakturQrService::batalkan($transaksi->id, 'priangan', "Transaksi dibatalkan: {$request->alasan_batal}");

        return redirect()->route('transaksipriangan.index')
            ->with('success', "Order Priangan TV {$transaksi->nofakturpriangan} resmi DIBATALKAN.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = TransaksiPriangan::findOrFail($id);

        if ($qrDelError = FakturQrService::guardBeforeCancelOrDelete($data, 'priangan')) {
            return response()->json([
                'status'  => 'error',
                'message' => $qrDelError,
            ], 422);
        }

        if ($data->jumlahbayar_transaksipriangan > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Order Priangan TV yang sudah memiliki riwayat pembayaran tidak dapat dihapus permanen. Gunakan prosedur Pembatalan Transaksi.'
            ], 422);
        }

        FakturQrService::batalkan($data->id, 'priangan', 'Transaksi dihapus');
        FakturSyncService::deleteFaktur($data->nofakturpriangan);
        PembayaranIklan::where('transaksi_id', $data->id)->where('media_type', 'priangan')->delete();
        Sppi::where('transaksi_id', $data->id)->where('media_type', 'priangan')->delete();
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}

