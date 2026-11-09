<?php

namespace App\Http\Controllers;

use App\Models\JenisIklan;
use App\Models\Pelanggan;
use App\Models\PembayaranIklan;
use App\Models\SalesAe;
use App\Models\SistemConfig;
use App\Models\Sppi;
use App\Models\TransaksiOnline;
use App\Services\FakturQrService;
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
    public function create(\App\Services\KalenderNasionalService $kalenderService)
    {
        $iklanonline = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'ONL'))
            ->where('is_active', true)
            ->get();
        if ($iklanonline->isEmpty()) {
            $iklanonline = JenisIklan::all();
        }
        $portalList = [
            ['id' => 'kabar-priangan.com', 'nama' => 'kabar-priangan.com', 'wilayah' => 'Pusat / Tasikmalaya Raya'],
            ['id' => 'kabar-tasikmalaya.com', 'nama' => 'kabar-tasikmalaya.com', 'wilayah' => 'Kota & Kab. Tasikmalaya'],
            ['id' => 'kabar-singaparna.com', 'nama' => 'kabar-singaparna.com', 'wilayah' => 'Kabupaten Tasikmalaya'],
            ['id' => 'kabar-sumedang.com', 'nama' => 'kabar-sumedang.com', 'wilayah' => 'Kabupaten Sumedang'],
            ['id' => 'kabar-ciamis.com', 'nama' => 'kabar-ciamis.com', 'wilayah' => 'Kabupaten Ciamis'],
            ['id' => 'kabar-banjar.com', 'nama' => 'kabar-banjar.com', 'wilayah' => 'Kota Banjar'],
            ['id' => 'kabar-garut.com', 'nama' => 'kabar-garut.com', 'wilayah' => 'Kabupaten Garut'],
            ['id' => 'kabar-bandung.com', 'nama' => 'kabar-bandung.com', 'wilayah' => 'Bandung Raya'],
            ['id' => 'kabar-pangandaran.com', 'nama' => 'kabar-pangandaran.com', 'wilayah' => 'Kabupaten Pangandaran'],
        ];
        $salesAes = SalesAe::where('is_active', true)->orderBy('kode_ae')->get();
        $pelanggans = Pelanggan::orderBy('nama_instansi')->get();
        $nofakturonline = \App\Services\NomorDokumenService::previewFaktur();
        $ppnPersen = SistemConfig::getPpnPersen();
        $configs = [
            'ppn_persen'     => $ppnPersen,
            'jam_cutoff'     => SistemConfig::getJamCutoff(),
            'is_past_cutoff' => SistemConfig::isPastCutoff(),
            'tarif_portal'   => (int) SistemConfig::get('tarif_portal_berita', 750000),
        ];
        $kalenderAwal = $kalenderService->getKalenderBulan((int) date('Y'), (int) date('n'));

        return view('pages.iklan.transaksi.online.create', compact('nofakturonline', 'iklanonline', 'ppnPersen', 'salesAes', 'pelanggans', 'configs', 'portalList', 'kalenderAwal'));
    }

    public function cetak($id)
    {
        $transaksi = TransaksiOnline::with(['iklanonline', 'pelanggan', 'salesAe'])->findOrFail($id);
        $qrFaktur  = FakturQrService::resolveForCetak($transaksi, 'online');
        $qrSvg     = $qrFaktur ? FakturQrService::renderSvg($qrFaktur, 106) : null;

        return view('pages.iklan.transaksi.online.cetak', compact('transaksi', 'qrFaktur', 'qrSvg'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'nofakturonline.required'          => 'Nomor faktur online wajib diisi.',
            'tanggal_transaksionline.required' => 'Tanggal transaksi wajib diisi.',
            'tanggal_transaksionline.date'     => 'Format tanggal transaksi tidak valid.',
            'nama_pemasangonline.required'     => 'Nama pemesan / instansi pemasang wajib diisi.',
            'id_iklanonline.required'          => 'Jenis iklan / produk online wajib dipilih.',
            'id_iklanonline.exists'            => 'Jenis iklan yang dipilih tidak valid atau sudah nonaktif.',
            'sales_ae_id.required'             => 'Sales / AE wajib dipilih.',
            'sales_ae_id.exists'               => 'Sales / AE yang dipilih tidak terdaftar di sistem.',
            'tanggal_muatiklanonline.required' => 'Tanggal tayang iklan wajib diisi.',
            'total_muatiklanonline.required'   => 'Jumlah frekuensi tayang wajib diisi.',
            'total_muatiklanonline.min'        => 'Jumlah frekuensi tayang minimal 1 kali.',
            'jenis_pembayaran.required'        => 'Skema pembayaran wajib dipilih (Tunai atau Kredit).',
            'jenis_pembayaran.in'              => 'Skema pembayaran harus berupa Tunai atau Kredit.',
            'materi_iklan.required'            => 'Judul atau materi iklan online wajib diisi.',
            'harga_transaksionline.required'   => 'Harga / tarif iklan online wajib diisi.',
            'jumlahbayar_transaksionline.required' => 'Jumlah bayar wajib diisi.',
        ];

        $request->validate([
            'nofakturonline'          => 'required',
            'tanggal_transaksionline' => 'required|date',
            'nama_pemasangonline'     => 'required|string',
            'alamat_pemasangonline'   => 'nullable|string',
            'id_iklanonline'          => 'required|exists:jenis_iklan,id',
            'sales_ae_id'             => 'required|exists:sales_ae,id',
            'sales_iklanonline'       => 'nullable|string',
            'tanggal_muatiklanonline' => 'required|date',
            'total_muatiklanonline'   => 'required|numeric|min:1',
            'jenis_pembayaran'        => 'required|in:Tunai,Kredit',
            'materi_iklan'            => 'required|string',

            // Keuangan
            'harga_transaksionline'       => 'required',
            'diskon_transaksionline'      => 'nullable',
            'jumlahbayar_transaksionline' => 'required',
        ], $messages);

        // Bersihkan format mata uang
        $harga_bersih  = (int) preg_replace('/[^0-9]/', '', (string) ($request->harga_transaksionline ?? '0'));
        $diskon_bersih = (int) preg_replace('/[^0-9]/', '', (string) ($request->diskon_transaksionline ?? '0'));
        $bayar_bersih  = (int) preg_replace('/[^0-9]/', '', (string) ($request->jumlahbayar_transaksionline ?? '0'));
        $qty           = max(1, (int) ($request->total_muatiklanonline ?: 1));

        $jenisIklanModel = JenisIklan::find($request->id_iklanonline);
        $isBonus = $jenisIklanModel && ($jenisIklanModel->tipe_dasar === 'BONUS' || 
                                       stripos($jenisIklanModel->nama_jenis, 'bonus') !== false || 
                                       stripos($jenisIklanModel->nama_jenis, 'barter') !== false || 
                                       stripos($jenisIklanModel->nama_jenis, 'csr') !== false);

        if (!$isBonus && $harga_bersih < 1) {
            return back()->withInput()->withErrors([
                'harga_transaksionline' => 'Harga transaksi iklan online harus lebih besar dari Rp 0 (kecuali kategori Bonus/Barter/CSR).'
            ]);
        }

        // Kalkulasi Keuangan
        $total_omset   = $harga_bersih * $qty;
        $subtotal      = max(0, $total_omset - $diskon_bersih);
        $ppnPersen     = SistemConfig::getPpnPersen();
        $ppn           = (int) round($subtotal * ($ppnPersen / 100));
        $total_tagihan = $subtotal + $ppn;

        // Aturan Pembayaran Tunai (otomatis lunas) & Kredit (otomatis Rp 0 / DP terukur)
        if ($request->jenis_pembayaran === 'Tunai') {
            if ($total_tagihan > 0) {
                $bayar_bersih = $total_tagihan;
            }
        } else {
            if ($bayar_bersih > $total_tagihan) {
                return back()->withInput()->withErrors([
                    'jumlahbayar_transaksionline' => 'Jumlah bayar (Rp ' . number_format($bayar_bersih, 0, ',', '.') . ') tidak boleh melebihi total tagihan (Rp ' . number_format($total_tagihan, 0, ',', '.') . ').'
                ]);
            }
        }
        if ($bayar_bersih < 0) {
            return back()->withInput()->withErrors([
                'jumlahbayar_transaksionline' => 'Jumlah bayar tidak boleh bernilai negatif.'
            ]);
        }

        $piutang       = max(0, $total_tagihan - $bayar_bersih);

        // Tahap 1: Komisi Komisioner (KH / Eksternal) diambil langsung dari DPP (Netto / Subtotal)
        $komisionerNama = $request->filled('komisioner') ? trim($request->komisioner) : null;
        $komisiPersen   = $request->filled('komisi_persen') ? (float) $request->komisi_persen : 0;
        $komisi_input   = $request->filled('komisi_transaksionline') ? (int) str_replace(['.', ','], '', $request->komisi_transaksionline) : 0;

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
        $insentif_input  = $request->filled('insentif_transaksionline') ? (int) str_replace(['.', ','], '', $request->insentif_transaksionline) : 0;

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

        // Cari atau validasi sales_ae_id & nama sales resmi
        $salesAeId = $request->sales_ae_id;
        $salesAeRecord = $salesAeId ? SalesAe::find($salesAeId) : null;
        $salesNama = $salesAeRecord 
            ? (($salesAeRecord->kode_ae === 'FO') ? 'Front Office (Loket Langsung)' : ($salesAeRecord->kode_ae . ' - ' . $salesAeRecord->nama_ae)) 
            : ($request->sales_iklanonline ?: 'Front Office (Loket Langsung)');

        // Upload File Materi Iklan jika ada
        $fileMateriPath = null;
        if ($request->hasFile('file_materi')) {
            $file = $request->file('file_materi');
            $cleanFaktur = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nofakturonline ?? time());
            $fileName = 'materi_onl_' . time() . '_' . $cleanFaktur . '.' . $file->getClientOriginalExtension();
            $fileMateriPath = $file->storeAs('materi_iklan', $fileName, 'public');
        } elseif ($request->hasFile('file_bukti_tayang')) {
            $fileMateriPath = $request->file('file_bukti_tayang')->store('materi_iklan', 'public');
        }

        // Resolusi Multi-Date Jadwal Tayang
        $rawDates = $request->tanggal_muat_list;
        if (is_string($rawDates)) {
            $rawDates = array_values(array_filter(array_map('trim', explode(',', $rawDates))));
        } elseif (!is_array($rawDates)) {
            $rawDates = [];
        }
        if (empty($rawDates) && $request->filled('tanggal_muatiklanonline')) {
            $rawDates = [$request->tanggal_muatiklanonline];
        }
        $primaryTanggalMuat = $rawDates[0] ?? $request->tanggal_muatiklanonline ?? now()->addDay()->toDateString();
        $tanggalMuatListStr = !empty($rawDates) ? implode(',', $rawDates) : $primaryTanggalMuat;

        // Resolusi Portals
        $rawPortals = $request->portal_iklanonline;
        if (is_string($rawPortals)) {
            $portals = array_values(array_filter(array_map('trim', explode(',', $rawPortals))));
        } elseif (is_array($rawPortals)) {
            $portals = $rawPortals;
        } else {
            $portals = [];
        }
        $portalUtama = !empty($portals) ? implode(', ', $portals) : 'kabar-priangan.com';

        // Resolusi Platforms Publikasi (Mendukung multi-platform misal Portal + Medsos)
        $platforms = $request->platforms_publikasi;
        if (is_string($platforms)) {
            $platforms = array_values(array_filter(array_map('trim', explode(',', $platforms))));
        } elseif (!is_array($platforms)) {
            $platforms = [];
        }

        // Jika user tidak mengirimkan platforms_publikasi (fallback legacy)
        if (empty($platforms)) {
            if (!empty($portals)) {
                $platforms[] = 'Portal Berita';
            }
            if ($request->filled('kategori_form')) {
                $tabMap = [
                    'PORTAL_BERITA' => 'Portal Berita',
                    'INSTAGRAM'     => 'Instagram',
                    'TIKTOK'        => 'TikTok',
                    'YOUTUBE'       => 'YouTube',
                    'PODCAST'       => 'Podcast',
                ];
                $currentTabPlatform = $tabMap[$request->kategori_form] ?? null;
                if ($currentTabPlatform && !in_array($currentTabPlatform, $platforms)) {
                    $platforms[] = $currentTabPlatform;
                }
            }
        }

        if (empty($platforms)) {
            $platforms = ['Portal Berita'];
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
            'alamat_pemasangonline'   => $request->alamat_pemasangonline ?: '-',

            'id_iklanonline'          => $request->id_iklanonline ?: JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'ONL'))->first()?->id,
            'portal_iklanonline'      => $portalUtama,
            'platforms_publikasi'     => $platforms,
            'skema_pembayaran'        => $request->skema_pembayaran ?: ($request->jenis_pembayaran === 'Kredit' ? 'kredit' : 'sekali_tayang'),
            'status_produksi'         => $request->status_produksi ?: 'menunggu_bahan',
            'kategori_produk_publikasi'=> $kategoriProduk ?? ['PORTAL_BERITA'],
            'bentuk_publikasi'        => $request->bentuk_publikasi ?: 'display',
            'bentuk_publikasi_lainnya'=> $request->bentuk_publikasi_lainnya,
            'materi_iklan'            => $request->materi_iklan ?? $request->keterangan_materi,
            'file_materi'             => $fileMateriPath,
            'url_materi_iklan'        => $request->url_materi_iklan,
            'url_berita_tayang'       => $request->url_berita_tayang,
            'url_medsos_tayang'       => $request->url_medsos_tayang,
            'file_bukti_tayang'       => $fileMateriPath,
            'tanggal_muatiklanonline' => $primaryTanggalMuat,
            'tanggal_muat_list'       => $tanggalMuatListStr,
            'tanggal_selesai_tayang'  => $request->tanggal_selesai_tayang,
            'tanggal_realisasi_tayang'=> $request->tanggal_realisasi_tayang,
            'total_muatiklanonline'   => $qty,
            'catatan_redaksi'         => $request->catatan_redaksi ?? $request->keterangan_materi,

            'sales_ae_id'             => $salesAeId,
            'sales_iklanonline'       => $salesNama,
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
    public function edit(string $id, \App\Services\KalenderNasionalService $kalenderService)
    {
        $transaksi = TransaksiOnline::with(['iklanonline', 'pelanggan', 'salesAe'])->findOrFail($id);

        if ($lockReason = FakturQrService::getAccountingOrCutoffLockReason($transaksi, 'online')) {
            return redirect()->route('transaksionline.index')
                ->with('error', "Transaksi {$transaksi->nofakturonline} tidak dapat diedit: {$lockReason}");
        }
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
            'tarif_portal'   => (int) SistemConfig::get('tarif_portal_berita', 750000),
        ];

        $portalList = [
            ['id' => 'kabar-priangan.com', 'nama' => 'kabar-priangan.com', 'wilayah' => 'Pusat / Tasikmalaya Raya'],
            ['id' => 'kabar-tasikmalaya.com', 'nama' => 'kabar-tasikmalaya.com', 'wilayah' => 'Kota & Kab. Tasikmalaya'],
            ['id' => 'kabar-singaparna.com', 'nama' => 'kabar-singaparna.com', 'wilayah' => 'Kabupaten Tasikmalaya'],
            ['id' => 'kabar-sumedang.com', 'nama' => 'kabar-sumedang.com', 'wilayah' => 'Kabupaten Sumedang'],
            ['id' => 'kabar-ciamis.com', 'nama' => 'kabar-ciamis.com', 'wilayah' => 'Kabupaten Ciamis'],
            ['id' => 'kabar-banjar.com', 'nama' => 'kabar-banjar.com', 'wilayah' => 'Kota Banjar'],
            ['id' => 'kabar-garut.com', 'nama' => 'kabar-garut.com', 'wilayah' => 'Kabupaten Garut'],
            ['id' => 'kabar-bandung.com', 'nama' => 'kabar-bandung.com', 'wilayah' => 'Bandung Raya'],
            ['id' => 'kabar-pangandaran.com', 'nama' => 'kabar-pangandaran.com', 'wilayah' => 'Kabupaten Pangandaran'],
        ];
        $kalenderAwal = $kalenderService->getKalenderBulan((int) date('Y'), (int) date('n'));

        return view('pages.iklan.transaksi.online.create', compact('transaksi', 'nofakturonline', 'iklanonline', 'ppnPersen', 'salesAe', 'salesAes', 'pelanggans', 'configs', 'portalList', 'kalenderAwal'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaksi = TransaksiOnline::with(['iklanonline', 'pelanggan', 'salesAe', 'pembayaranList.kasirUser', 'sppi'])->findOrFail($id);
        return view('pages.iklan.transaksi.online.show', compact('transaksi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $messages = [
            'tanggal_transaksionline.required' => 'Tanggal transaksi wajib diisi.',
            'tanggal_transaksionline.date'     => 'Format tanggal transaksi tidak valid.',
            'nama_pemasangonline.required'     => 'Nama pemesan / instansi pemasang wajib diisi.',
            'id_iklanonline.required'          => 'Jenis iklan / produk online wajib dipilih.',
            'id_iklanonline.exists'            => 'Jenis iklan yang dipilih tidak valid atau sudah nonaktif.',
            'sales_ae_id.required'             => 'Sales / AE wajib dipilih.',
            'sales_ae_id.exists'               => 'Sales / AE yang dipilih tidak terdaftar di sistem.',
            'tanggal_muatiklanonline.required' => 'Tanggal tayang iklan wajib diisi.',
            'total_muatiklanonline.required'   => 'Jumlah frekuensi tayang wajib diisi.',
            'total_muatiklanonline.min'        => 'Jumlah frekuensi tayang minimal 1 kali.',
            'jenis_pembayaran.required'        => 'Skema pembayaran wajib dipilih (Tunai atau Kredit).',
            'jenis_pembayaran.in'              => 'Skema pembayaran harus berupa Tunai atau Kredit.',
            'materi_iklan.required'            => 'Judul atau materi iklan online wajib diisi.',
            'harga_transaksionline.required'   => 'Harga / tarif iklan online wajib diisi.',
            'jumlahbayar_transaksionline.required' => 'Jumlah bayar wajib diisi.',
        ];

        $request->validate([
            'tanggal_transaksionline' => 'required|date',
            'nama_pemasangonline'     => 'required|string',
            'alamat_pemasangonline'   => 'nullable|string',
            'id_iklanonline'          => 'required|exists:jenis_iklan,id',
            'sales_ae_id'             => 'required|exists:sales_ae,id',
            'sales_iklanonline'       => 'nullable|string',
            'tanggal_muatiklanonline' => 'required|date',
            'total_muatiklanonline'   => 'required|numeric|min:1',
            'jenis_pembayaran'        => 'required|in:Tunai,Kredit',
            'materi_iklan'            => 'required|string',

            // Keuangan
            'harga_transaksionline'       => 'required',
            'diskon_transaksionline'      => 'nullable',
            'jumlahbayar_transaksionline' => 'required',
        ], $messages);

        $harga_bersih  = (int) preg_replace('/[^0-9]/', '', (string) ($request->harga_transaksionline ?? '0'));
        $diskon_bersih = (int) preg_replace('/[^0-9]/', '', (string) ($request->diskon_transaksionline ?? '0'));
        $bayar_bersih  = (int) preg_replace('/[^0-9]/', '', (string) ($request->jumlahbayar_transaksionline ?? '0'));
        $qty           = max(1, (int) ($request->total_muatiklanonline ?: 1));

        $jenisIklanModel = JenisIklan::find($request->id_iklanonline);
        $isBonus = $jenisIklanModel && ($jenisIklanModel->tipe_dasar === 'BONUS' || 
                                       stripos($jenisIklanModel->nama_jenis, 'bonus') !== false || 
                                       stripos($jenisIklanModel->nama_jenis, 'barter') !== false || 
                                       stripos($jenisIklanModel->nama_jenis, 'csr') !== false);

        if (!$isBonus && $harga_bersih < 1) {
            return back()->withInput()->withErrors([
                'harga_transaksionline' => 'Harga transaksi iklan online harus lebih besar dari Rp 0 (kecuali kategori Bonus/Barter/CSR).'
            ]);
        }

        $total_omset   = $harga_bersih * $qty;
        $subtotal      = max(0, $total_omset - $diskon_bersih);
        $ppnPersen     = SistemConfig::getPpnPersen();
        $ppn           = (int) round($subtotal * ($ppnPersen / 100));
        $total_tagihan = $subtotal + $ppn;

        // Aturan Pembayaran Tunai & Kredit
        if ($request->jenis_pembayaran === 'Tunai') {
            if ($total_tagihan > 0) {
                $bayar_bersih = $total_tagihan;
            }
        } else {
            if ($bayar_bersih > $total_tagihan) {
                return back()->withInput()->withErrors([
                    'jumlahbayar_transaksionline' => 'Jumlah bayar (Rp ' . number_format($bayar_bersih, 0, ',', '.') . ') tidak boleh melebihi total tagihan (Rp ' . number_format($total_tagihan, 0, ',', '.') . ').'
                ]);
            }
        }
        if ($bayar_bersih < 0) {
            return back()->withInput()->withErrors([
                'jumlahbayar_transaksionline' => 'Jumlah bayar tidak boleh bernilai negatif.'
            ]);
        }

        $piutang       = max(0, $total_tagihan - $bayar_bersih);

        // Tahap 1: Komisi Komisioner (KH / Eksternal) diambil langsung dari DPP (Netto / Subtotal)
        $komisionerNama = $request->filled('komisioner') ? trim($request->komisioner) : null;
        $komisiPersen   = $request->filled('komisi_persen') ? (float) $request->komisi_persen : 0;
        $komisi_input   = $request->filled('komisi_transaksionline') ? (int) str_replace(['.', ','], '', $request->komisi_transaksionline) : 0;

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
        $insentif_input  = $request->filled('insentif_transaksionline') ? (int) str_replace(['.', ','], '', $request->insentif_transaksionline) : 0;

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

        $salesAeId = $request->sales_ae_id;
        $salesAeRecord = $salesAeId ? SalesAe::find($salesAeId) : null;
        $salesNama = $salesAeRecord 
            ? (($salesAeRecord->kode_ae === 'FO') ? 'Front Office (Loket Langsung)' : ($salesAeRecord->kode_ae . ' - ' . $salesAeRecord->nama_ae)) 
            : ($request->sales_iklanonline ?: 'Front Office (Loket Langsung)');

        DB::beginTransaction();
        try {
            $transaksi = TransaksiOnline::findOrFail($id);

            if ($qrLockError = FakturQrService::guardBeforeUpdate($transaksi, 'online', (float) $bayar_bersih, (string) $request->nama_pemasangonline, (float) $total_tagihan)) {
                DB::rollBack();
                return back()->withErrors(['error' => $qrLockError])->withInput();
            }

            $kategoriProduk = $request->kategori_produk_publikasi;
            if (!is_array($kategoriProduk) && !empty($kategoriProduk)) {
                $kategoriProduk = [$kategoriProduk];
            }

            // Resolusi Multi-Date Jadwal Tayang
            $rawDates = $request->tanggal_muat_list;
            if (is_string($rawDates)) {
                $rawDates = array_values(array_filter(array_map('trim', explode(',', $rawDates))));
            } elseif (!is_array($rawDates)) {
                $rawDates = [];
            }
            if (empty($rawDates) && $request->filled('tanggal_muatiklanonline')) {
                $rawDates = [$request->tanggal_muatiklanonline];
            }
            $primaryTanggalMuat = $rawDates[0] ?? $request->tanggal_muatiklanonline ?? $transaksi->tanggal_muatiklanonline;
            $tanggalMuatListStr = !empty($rawDates) ? implode(',', $rawDates) : ($transaksi->tanggal_muat_list ?: $primaryTanggalMuat);

            // Resolusi Portals
            $rawPortals = $request->portal_iklanonline;
            if (is_string($rawPortals)) {
                $portals = array_values(array_filter(array_map('trim', explode(',', $rawPortals))));
            } elseif (is_array($rawPortals)) {
                $portals = $rawPortals;
            } else {
                $portals = [];
            }
            $portalUtama = !empty($portals) ? implode(', ', $portals) : ($transaksi->portal_iklanonline ?: 'kabar-priangan.com');

            // Resolusi Platforms Publikasi (Mendukung multi-platform misal Portal + Medsos)
            $platforms = $request->platforms_publikasi;
            if (is_string($platforms)) {
                $platforms = array_values(array_filter(array_map('trim', explode(',', $platforms))));
            } elseif (!is_array($platforms)) {
                $platforms = [];
            }

            // Jika user tidak mengirimkan platforms_publikasi (fallback legacy)
            if (empty($platforms)) {
                if (!empty($portals)) {
                    $platforms[] = 'Portal Berita';
                }
                if ($request->filled('kategori_form')) {
                    $tabMap = [
                        'PORTAL_BERITA' => 'Portal Berita',
                        'INSTAGRAM'     => 'Instagram',
                        'TIKTOK'        => 'TikTok',
                        'YOUTUBE'       => 'YouTube',
                        'PODCAST'       => 'Podcast',
                    ];
                    $currentTabPlatform = $tabMap[$request->kategori_form] ?? null;
                    if ($currentTabPlatform && !in_array($currentTabPlatform, $platforms)) {
                        $platforms[] = $currentTabPlatform;
                    }
                }
            }

            if (empty($platforms)) {
                $platforms = $transaksi->platforms_publikasi ?: ['Portal Berita'];
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
                'portal_iklanonline'      => $portalUtama,
                'platforms_publikasi'     => $platforms,
                'skema_pembayaran'        => $request->skema_pembayaran ?: ($transaksi->skema_pembayaran ?: 'sekali_tayang'),
                'status_produksi'         => $request->status_produksi ?: $transaksi->status_produksi,
                'kategori_produk_publikasi'=> $kategoriProduk ?? $transaksi->kategori_produk_publikasi,
                'bentuk_publikasi'        => $request->bentuk_publikasi ?: ($transaksi->bentuk_publikasi ?: 'display'),
                'bentuk_publikasi_lainnya'=> $request->bentuk_publikasi_lainnya,
                'materi_iklan'            => $request->materi_iklan ?? $request->keterangan_materi ?? $transaksi->materi_iklan,
                'url_materi_iklan'        => $request->url_materi_iklan ?? $transaksi->url_materi_iklan,
                'url_berita_tayang'       => $request->url_berita_tayang ?? $transaksi->url_berita_tayang,
                'url_medsos_tayang'       => $request->url_medsos_tayang ?? $transaksi->url_medsos_tayang,
                'tanggal_muatiklanonline' => $primaryTanggalMuat,
                'tanggal_muat_list'       => $tanggalMuatListStr,
                'tanggal_selesai_tayang'  => $request->tanggal_selesai_tayang,
                'tanggal_realisasi_tayang'=> $request->tanggal_realisasi_tayang ?? $transaksi->tanggal_realisasi_tayang,
                'total_muatiklanonline'   => $qty,
                'catatan_redaksi'         => $request->catatan_redaksi ?? $request->keterangan_materi ?? $transaksi->catatan_redaksi,

                'sales_ae_id'             => $salesAeId ?? $transaksi->sales_ae_id,
                'sales_iklanonline'       => $salesNama,
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

            if ($request->hasFile('file_materi')) {
                if ($transaksi->file_materi) {
                    Storage::disk('public')->delete($transaksi->file_materi);
                }
                $file = $request->file('file_materi');
                $cleanFaktur = preg_replace('/[^A-Za-z0-9_\-]/', '_', $transaksi->nofakturonline);
                $fileName = 'materi_onl_' . time() . '_' . $cleanFaktur . '.' . $file->getClientOriginalExtension();
                $updateData['file_materi'] = $file->storeAs('materi_iklan', $fileName, 'public');
            } elseif ($request->hasFile('file_bukti_tayang')) {
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

        if ($qrCancelError = FakturQrService::guardBeforeCancelOrDelete($transaksi, 'online')) {
            return back()->with('error', $qrCancelError);
        }

        $transaksi->update([
            'status_produksi' => 'selesai',
            'catatan_redaksi' => trim(($transaksi->catatan_redaksi ?? '') . " [DIBATALKAN: {$request->alasan_batal}]"),
        ]);

        // Sinkronisasi status pembatalan ke Buku Faktur Keuangan & QR Pembayaran
        FakturSyncService::cancelFaktur($transaksi->nofakturonline, $request->alasan_batal);
        FakturQrService::batalkan($transaksi->id, 'online', "Transaksi dibatalkan: {$request->alasan_batal}");

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

        if ($qrDelError = FakturQrService::guardBeforeCancelOrDelete($transaksi, 'online')) {
            return response()->json([
                'status'  => 'error',
                'message' => $qrDelError,
            ], 422);
        }

        if ($transaksi->jumlahbayar_transaksionline > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Order yang sudah memiliki riwayat pembayaran tidak dapat dihapus permanen. Gunakan prosedur Pembatalan Transaksi.'
            ], 422);
        }

        if ($transaksi->file_bukti_tayang) {
            Storage::disk('public')->delete($transaksi->file_bukti_tayang);
        }

        FakturQrService::batalkan($transaksi->id, 'online', 'Transaksi dihapus');
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
