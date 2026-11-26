<?php

namespace App\Http\Controllers;

use App\Models\DailyCutoff;
use App\Models\JenisIklan;
use App\Models\KopIklan;
use App\Models\KopKota;
use App\Models\MemoUnlock;
use App\Models\OrderJadwalMuat;
use App\Models\PaketIklan;
use App\Models\Pelanggan;
use App\Models\PembayaranIklan;
use App\Models\SalesAe;
use App\Models\SistemConfig;
use App\Models\Sppi;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use App\Services\FakturQrService;
use App\Services\FakturSyncService;
use App\Services\NomorDokumenService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaketIklanController extends Controller
{
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
        ];
    }

    public static function getTemplatePaketList(): array
    {
        return [
            [
                'kode'          => 'CUSTOM',
                'nama'          => 'Custom Builder (Rakit Bebas)',
                'sub'           => 'Pilih bebas kombinasi 2 atau 3 media sesuai kebutuhan pemasang',
                'media'         => ['CTK', 'ONL'],
                'diskon_persen' => 0,
            ],
            [
                'kode'          => 'PKT_HEMAT',
                'nama'          => 'Paket Hemat 2 Media (Cetak + Online)',
                'sub'           => 'Iklan Cetak + Publikasi Portal Berita / Medsos',
                'media'         => ['CTK', 'ONL'],
                'diskon_persen' => 10,
            ],
            [
                'kode'          => 'PKT_BISNIS',
                'nama'          => 'Paket Bisnis Digital (Online + TV)',
                'sub'           => 'Portal Berita & Sosmed + Tayangan Spot Priangan TV',
                'media'         => ['ONL', 'PTV'],
                'diskon_persen' => 12,
            ],
            [
                'kode'          => 'PKT_EKSKLUSIF',
                'nama'          => 'Paket Eksklusif 3 Media (Cetak + Online + TV)',
                'sub'           => 'Kampanye Terpadu Lintas Media Kabar Priangan',
                'media'         => ['CTK', 'ONL', 'PTV'],
                'diskon_persen' => 15,
            ],
        ];
    }

    public function index(Request $request)
    {
        $query = PaketIklan::with([
            'pelanggan',
            'salesAe',
            'transaksiCetak',
            'transaksiOnline',
            'transaksiTv',
        ])->latest('id');

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('nofaktur_paket', 'like', "%{$q}%")
                    ->orWhere('nama_pemasang', 'like', "%{$q}%")
                    ->orWhere('nama_paket', 'like', "%{$q}%")
                    ->orWhere('sales_nama', 'like', "%{$q}%")
                    ->orWhereHas('transaksiCetak', fn($c) => $c->where('nofakturkoran', 'like', "%{$q}%"))
                    ->orWhereHas('transaksiOnline', fn($o) => $o->where('nofakturonline', 'like', "%{$q}%"))
                    ->orWhereHas('transaksiTv', fn($t) => $t->where('nofakturpriangan', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('status')) {
            $st = strtolower($request->status);
            if ($st === 'tunai') {
                $query->where('jenis_pembayaran', 'Tunai');
            } elseif ($st === 'kredit') {
                $query->where('jenis_pembayaran', 'Kredit');
            }
        }

        if ($request->filled('media')) {
            $m = strtoupper($request->media);
            $query->whereJsonContains('media_terpilih', $m);
        }

        $paketList = $query->paginate(15)->withQueryString();

        return view('pages.iklan.transaksi.paket.index', compact('paketList'));
    }

    public function create()
    {
        $nofakturpaket = NomorDokumenService::previewFaktur(null, 'PKT');

        $iklankoran = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'KRN'))
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $iklanonline = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'ONL'))
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $iklanpriangan = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'PTV'))
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $pelanggans = Pelanggan::where('is_active', true)->orderBy('nama_instansi')->get();
        $salesAes   = SalesAe::where('is_active', true)->orderBy('kode_ae')->get();
        $kopKotas   = KopKota::where('is_active', true)->orderBy('nomor_halaman')->get();
        $kopIklans  = KopIklan::where('is_active', true)->orderBy('urutan')->get();

        $barisItem = $iklankoran->where('tipe_dasar', 'BARIS')->first();
        $tarifBarisAktif = $barisItem ? (int) $barisItem->tarif_dasar : (int) SistemConfig::get('tarif_per_baris', 22750);

        $ppnPersen = SistemConfig::getPpnPersen();
        $configs = [
            'ppn_persen'      => $ppnPersen,
            'jam_cutoff'      => SistemConfig::getJamCutoff(),
            'is_past_cutoff'  => SistemConfig::isPastCutoff(),
            'tarif_per_baris' => $tarifBarisAktif,
            'tarif_mmk_bw'    => (int) SistemConfig::get('tarif_mmk_bw', 25000),
            'tarif_mmk_fc'    => (int) SistemConfig::get('tarif_mmk_fc', 45000),
            'minimal_baris'   => (int) SistemConfig::get('minimal_baris', 2),
            'maks_karakter'   => (int) SistemConfig::get('maksimal_karakter_per_baris', 32),
            'tarif_portal'    => (int) SistemConfig::get('tarif_portal_berita', 750000),
            'unlocked_dates'  => MemoUnlock::where('status', 'OPEN')
                ->whereDate('tanggal_target', '>=', now('Asia/Jakarta')->toDateString())
                ->pluck('tanggal_target')
                ->map(fn($d) => Carbon::parse($d)->toDateString())
                ->values()
                ->all(),
            'closed_dates'    => DailyCutoff::where('is_closed', true)
                ->whereDate('tanggal_edisi', '>=', now('Asia/Jakarta')->toDateString())
                ->pluck('tanggal_edisi')
                ->map(fn($d) => Carbon::parse($d)->toDateString())
                ->values()
                ->all(),
        ];

        $portalList        = self::getPortalList();
        $templatePaketList = self::getTemplatePaketList();

        $kalenderService = app(\App\Services\KalenderNasionalService::class);
        $kalenderAwal = $kalenderService->getKalenderBulan((int) date('Y'), (int) date('n'));

        return view('pages.iklan.transaksi.paket.create', compact(
            'nofakturpaket',
            'iklankoran',
            'iklanonline',
            'iklanpriangan',
            'pelanggans',
            'salesAes',
            'kopKotas',
            'kopIklans',
            'ppnPersen',
            'configs',
            'portalList',
            'templatePaketList',
            'kalenderAwal'
        ));
    }

    public function store(Request $request)
    {
        $mediaTerpilih = array_values(array_unique(array_filter((array) $request->input('media_terpilih', []))));
        if (count($mediaTerpilih) < 2) {
            return back()->withInput()->withErrors([
                'media_terpilih' => 'Paket Iklan wajib menggabungkan minimal 2 saluran media (misal: Cetak + Online, Cetak + TV, Online + TV, atau ketiganya).',
            ]);
        }

        $request->validate([
            'nama_pemasang'    => 'required|string|max:150',
            'sales_ae_id'      => 'required|exists:sales_ae,id',
            'jenis_pembayaran' => 'required|in:Tunai,Kredit',
        ], [
            'nama_pemasang.required'    => 'Nama pemesan / instansi pemasang wajib diisi.',
            'sales_ae_id.required'      => 'Sales / AE wajib dipilih.',
            'jenis_pembayaran.required' => 'Jenis pembayaran wajib dipilih (Tunai atau Kredit).',
        ]);

        $cleanNum = static fn($v): int => (int) preg_replace('/[^0-9]/', '', (string) ($v ?? 0));

        $hasCtk = in_array('CTK', $mediaTerpilih, true);
        $hasOnl = in_array('ONL', $mediaTerpilih, true);
        $hasPtv = in_array('PTV', $mediaTerpilih, true);

        // 1. Validasi & Hitung Brutto per Media
        $datesCtk = [];
        $bruttoCtk = 0;
        $qtyCtk = 1;
        $hargaSatuanCtk = 0;

        if ($hasCtk) {
            $rawDatesCtk = $request->input('ctk_tanggal_list', '');
            $datesCtk = is_array($rawDatesCtk)
                ? array_values(array_filter(array_map('trim', $rawDatesCtk)))
                : array_values(array_filter(array_map('trim', explode(',', (string) $rawDatesCtk))));

            if (empty($datesCtk)) {
                $datesCtk = [now('Asia/Jakarta')->addDay()->toDateString()];
            }
            sort($datesCtk);
            foreach ($datesCtk as $tgl) {
                if (DailyCutoff::isDateLocked($tgl)) {
                    return back()->withInput()->withErrors([
                        'ctk_tanggal_list' => "Tanggal terbit Iklan Cetak ({$tgl}) sudah ditutup (Cut-Off). Pemasangan hari ini minimal untuk terbit besok.",
                    ]);
                }
            }
            $qtyCtk = max(1, count($datesCtk));
            $hargaSatuanCtk = $cleanNum($request->input('ctk_harga_satuan', 0));
            $bruttoCtk = $hargaSatuanCtk * $qtyCtk;
            if ($bruttoCtk <= 0) {
                return back()->withInput()->withErrors(['ctk_harga_satuan' => 'Nominal tarif Iklan Cetak harus lebih dari Rp 0.']);
            }
        }

        $datesOnl = [];
        $bruttoOnl = 0;
        $qtyOnl = 1;
        $hargaSatuanOnl = 0;

        if ($hasOnl) {
            $rawDatesOnl = $request->input('onl_tanggal_list', '');
            $datesOnl = is_array($rawDatesOnl)
                ? array_values(array_filter(array_map('trim', $rawDatesOnl)))
                : array_values(array_filter(array_map('trim', explode(',', (string) $rawDatesOnl))));

            if (empty($datesOnl)) {
                $datesOnl = [now('Asia/Jakarta')->addDay()->toDateString()];
            }
            sort($datesOnl);
            foreach ($datesOnl as $tgl) {
                if (DailyCutoff::isDateLocked($tgl)) {
                    return back()->withInput()->withErrors([
                        'onl_tanggal_list' => "Tanggal tayang Iklan Online ({$tgl}) sudah ditutup (Cut-Off). Pemasangan hari ini minimal untuk tayang besok.",
                    ]);
                }
            }
            $qtyOnl = max(1, count($datesOnl));
            $hargaSatuanOnl = $cleanNum($request->input('onl_harga_satuan', 0));
            $bruttoOnl = $hargaSatuanOnl * $qtyOnl;
            if ($bruttoOnl <= 0) {
                return back()->withInput()->withErrors(['onl_harga_satuan' => 'Nominal tarif Iklan Online harus lebih dari Rp 0.']);
            }
        }

        $datesPtv = [];
        $bruttoPtv = 0;
        $qtyPtv = 1;
        $hargaSatuanPtv = 0;

        if ($hasPtv) {
            $rawDatesPtv = $request->input('ptv_tanggal_list', '');
            $datesPtv = is_array($rawDatesPtv)
                ? array_values(array_filter(array_map('trim', $rawDatesPtv)))
                : array_values(array_filter(array_map('trim', explode(',', (string) $rawDatesPtv))));

            if (empty($datesPtv)) {
                $datesPtv = [now('Asia/Jakarta')->addDay()->toDateString()];
            }
            sort($datesPtv);
            foreach ($datesPtv as $tgl) {
                if (DailyCutoff::isDateLocked($tgl)) {
                    return back()->withInput()->withErrors([
                        'ptv_tanggal_list' => "Tanggal siar Iklan TV ({$tgl}) sudah ditutup (Cut-Off). Pemasangan hari ini minimal untuk siar besok.",
                    ]);
                }
            }
            $qtyPtv = max(1, count($datesPtv));
            $hargaSatuanPtv = $cleanNum($request->input('ptv_harga_satuan', 0));
            $bruttoPtv = $hargaSatuanPtv * $qtyPtv;
            if ($bruttoPtv <= 0) {
                return back()->withInput()->withErrors(['ptv_harga_satuan' => 'Nominal tarif Iklan TV harus lebih dari Rp 0.']);
            }
        }

        $totalBrutto = $bruttoCtk + $bruttoOnl + $bruttoPtv;
        $diskonPersen = (float) ($request->input('diskon_persen', 0));
        $diskonTotal  = $cleanNum($request->input('nilai_diskon', 0));
        if ($diskonTotal <= 0 && $diskonPersen > 0) {
            $diskonTotal = (int) round($totalBrutto * ($diskonPersen / 100));
        }
        $diskonTotal = min($totalBrutto, max(0, $diskonTotal));

        $ppnPersen    = SistemConfig::getPpnPersen();
        $totalNetto   = max(0, $totalBrutto - $diskonTotal);
        $totalPpn     = (int) round($totalNetto * ($ppnPersen / 100));
        $totalTagihan = $totalNetto + $totalPpn;

        $jumlahBayar  = $cleanNum($request->input('jumlah_bayar', 0));
        $jenisBayar   = $request->input('jenis_pembayaran', 'Tunai');

        if ($jenisBayar === 'Tunai' && $totalTagihan > 0 && $jumlahBayar <= 0) {
            return back()->withInput()->withErrors([
                'jumlah_bayar' => 'Untuk transaksi Tunai, nominal pembayaran wajib diisi.',
            ]);
        }
        if ($jumlahBayar > $totalTagihan) {
            $jumlahBayar = $totalTagihan;
        }
        $sisaPiutang = max(0, $totalTagihan - $jumlahBayar);

        // 2. Alokasi Diskon & Pembayaran Secara Proporsional (Standar PSAK 72)
        $activeMediaKeys = [];
        if ($hasCtk) $activeMediaKeys['CTK'] = $bruttoCtk;
        if ($hasOnl) $activeMediaKeys['ONL'] = $bruttoOnl;
        if ($hasPtv) $activeMediaKeys['PTV'] = $bruttoPtv;

        $allocDiskon = ['CTK' => 0, 'ONL' => 0, 'PTV' => 0];
        $remainingDiskon = $diskonTotal;
        $keys = array_keys($activeMediaKeys);
        $lastKey = end($keys);
        foreach ($activeMediaKeys as $k => $bVal) {
            if ($k === $lastKey) {
                $allocDiskon[$k] = max(0, $remainingDiskon);
            } else {
                $part = $totalBrutto > 0 ? (int) round($diskonTotal * ($bVal / $totalBrutto)) : 0;
                $allocDiskon[$k] = $part;
                $remainingDiskon -= $part;
            }
        }

        // Hitung Netto, PPN, dan Total per Media
        $mediaCalc = [];
        $sumChildTagihan = 0;
        foreach ($activeMediaKeys as $k => $bVal) {
            $net = max(0, $bVal - $allocDiskon[$k]);
            $ppnVal = (int) round($net * ($ppnPersen / 100));
            $totVal = $net + $ppnVal;
            $mediaCalc[$k] = [
                'brutto'  => $bVal,
                'diskon'  => $allocDiskon[$k],
                'netto'   => $net,
                'ppn'     => $ppnVal,
                'tagihan' => $totVal,
                'bayar'   => 0,
                'piutang' => 0,
            ];
            $sumChildTagihan += $totVal;
        }

        // Alokasi Pembayaran Proporsional ke Faktur Anak
        $remainingBayar = $jumlahBayar;
        foreach ($activeMediaKeys as $k => $bVal) {
            if ($k === $lastKey) {
                $bayarK = max(0, min($mediaCalc[$k]['tagihan'], $remainingBayar));
            } else {
                $bayarK = $sumChildTagihan > 0
                    ? (int) round($jumlahBayar * ($mediaCalc[$k]['tagihan'] / $sumChildTagihan))
                    : 0;
                $bayarK = min($mediaCalc[$k]['tagihan'], $bayarK);
                $remainingBayar -= $bayarK;
            }
            $mediaCalc[$k]['bayar']   = $bayarK;
            $mediaCalc[$k]['piutang'] = max(0, $mediaCalc[$k]['tagihan'] - $bayarK);
        }

        // Samakan total induk dengan jumlah presisi anak
        $totalPpn     = array_sum(array_column($mediaCalc, 'ppn'));
        $totalTagihan = array_sum(array_column($mediaCalc, 'tagihan'));
        $sisaPiutang  = max(0, $totalTagihan - $jumlahBayar);

        // 3. Resolusi Pelanggan & Sales
        $pelangganId = Pelanggan::resolveFromRequest($request, 'nama_pemasang', 'alamat_pemasang', 'telepon_pemasang');

        $salesAe = SalesAe::find($request->sales_ae_id);
        $salesNama = $salesAe
            ? ($salesAe->kode_ae === 'FO' ? 'Front Office (Loket Langsung)' : ($salesAe->kode_ae . ' - ' . $salesAe->nama_ae))
            : 'Front Office';

        $tglTransaksi = $request->input('tanggal_transaksi', now('Asia/Jakarta')->toDateString());
        $templateKode = $request->input('kode_template', 'CUSTOM');
        $namaPaket    = $request->input('nama_paket') ?: ('Paket Lintas Media (' . implode(' + ', $mediaTerpilih) . ')');

        DB::beginTransaction();
        try {
            $noFakturPaket = PaketIklan::createCode();

            $paket = PaketIklan::create([
                'nofaktur_paket'    => $noFakturPaket,
                'tanggal_transaksi' => $tglTransaksi,
                'kode_template'     => $templateKode,
                'nama_paket'        => $namaPaket,
                'pelanggan_id'      => $pelangganId,
                'nama_pemasang'     => trim($request->nama_pemasang),
                'alamat_pemasang'   => $request->input('alamat_pemasang'),
                'telepon_pemasang'  => $request->input('telepon_pemasang'),
                'nama_pic'          => $request->input('nama_pic'),
                'sales_ae_id'       => $salesAe?->id,
                'sales_nama'        => $salesNama,
                'komisioner'        => $request->input('komisioner'),
                'komisi_persen'     => (float) $request->input('komisi_persen', 0),
                'komisi_nominal'    => $cleanNum($request->input('komisi_nominal', 0)),
                'insentif_persen'   => (float) $request->input('insentif_persen', 0),
                'insentif_nominal'  => $cleanNum($request->input('insentif_nominal', 0)),
                'media_terpilih'    => $mediaTerpilih,
                'nilai_brutto'      => $totalBrutto,
                'diskon_persen'     => $diskonPersen,
                'nilai_diskon'      => $diskonTotal,
                'nilai_netto'       => $totalNetto,
                'ppn_persen'        => $ppnPersen,
                'nilai_ppn'         => $totalPpn,
                'total_tagihan'     => $totalTagihan,
                'jumlah_bayar'      => $jumlahBayar,
                'piutang'           => $sisaPiutang,
                'jenis_pembayaran'  => $jenisBayar,
                'jatuh_tempo'       => $request->input('jatuh_tempo'),
                'status_order'      => 'Aktif',
                'created_by'        => Auth::id(),
            ]);

            // A. Simpan Anak: Iklan Cetak (TransaksiKoran)
            if ($hasCtk) {
                $cCalc = $mediaCalc['CTK'];
                $katCtk = $request->input('ctk_kategori_form', 'DISPLAY');
                $idIklanCtk = $request->input('ctk_id_iklan') ?: JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'KRN'))->first()?->id;
                $lebarKolom = (int) $request->input('ctk_lebar_kolom', 2);
                $tinggiMm   = (int) $request->input('ctk_tinggi_mm', 100);
                $jmlBaris   = (int) $request->input('ctk_jumlah_baris', 2);
                $ukuranStr  = ($katCtk === 'BARIS') ? "{$jmlBaris} Baris" : "{$lebarKolom} Kolom x {$tinggiMm} mm";

                $kopKotaId = $request->input('ctk_kop_kota_id') ?: null;
                $kopKotaRec = $kopKotaId ? KopKota::find($kopKotaId) : null;
                $halamanStr = $kopKotaRec ? ("Halaman {$kopKotaRec->nomor_halaman} ({$kopKotaRec->nama_penempatan})") : 'Halaman Dalam / Reguler';

                $fileCtkPath = null;
                if ($request->hasFile('ctk_file_materi')) {
                    $fileCtkPath = $request->file('ctk_file_materi')->store('materi_iklan', 'public');
                }

                $trxCetak = TransaksiKoran::create([
                    'paket_id'                    => $paket->id,
                    'nofakturkoran'               => TransaksiKoran::createCode(),
                    'tanggal_transaksikoran'      => $tglTransaksi,
                    'pelanggan_id'                => $pelangganId,
                    'nama_pemasangkoran'          => trim($request->nama_pemasang),
                    'nama_pic'                    => $request->input('nama_pic'),
                    'alamat_pemasangkoran'        => $request->input('alamat_pemasang'),
                    'id_iklankoran'               => $idIklanCtk,
                    'kategori_form'               => $katCtk,
                    'warna_iklan'                 => $request->input('ctk_warna', 'BW'),
                    'ukuran_iklan'                => $ukuranStr,
                    'jumlah_baris'                => $katCtk === 'BARIS' ? $jmlBaris : 0,
                    'naskah_iklan_baris'          => $request->input('ctk_naskah_baris') ?? '',
                    'lebar_kolom'                 => $katCtk !== 'BARIS' ? $lebarKolom : 0,
                    'tinggi_mm'                   => $katCtk !== 'BARIS' ? $tinggiMm : 0,
                    'halaman_iklan'               => $halamanStr,
                    'kop_kota_id'                 => $kopKotaId,
                    'kop_iklan_id'                => $request->input('ctk_kop_iklan_id') ?: null,
                    'produk_jasa'                 => $namaPaket,
                    'jenis_harga'                 => 'Paket',
                    'jenis_pembayaran'            => $jenisBayar,
                    'jatuh_tempo_pembayaran'      => $request->input('jatuh_tempo'),
                    'keterangan_materi'           => $request->input('ctk_keterangan_materi') ?: "Bagian dari Paket {$noFakturPaket}",
                    'status_materi'               => 'Siap_Cetak',
                    'file_materi'                 => $fileCtkPath,
                    'tanggal_muatkoran'           => $datesCtk[0],
                    'tanggal_muat_list'           => $datesCtk,
                    'sales_ae_id'                 => $salesAe?->id,
                    'sales_iklankoran'            => $salesNama,
                    'input_oleh_user_id'          => Auth::id(),
                    'total_muatkoran'             => $qtyCtk,
                    'harga_transaksikoran'        => $hargaSatuanCtk,
                    'diskon_transaksikoran'       => $cCalc['diskon'],
                    'diskon_persen'               => $diskonPersen,
                    'ppn_persen'                  => $ppnPersen,
                    'ppn_transaksikoran'          => $cCalc['ppn'],
                    'totaltagihan_transaksikoran' => $cCalc['tagihan'],
                    'jumlahbayar_transaksikoran'  => $cCalc['bayar'],
                    'piutang_transaksikoran'      => $cCalc['piutang'],
                    'insentif_persen'             => (float) $request->input('insentif_persen', 0),
                    'insentif_transaksikoran'     => 0,
                    'komisi_persen'               => (float) $request->input('komisi_persen', 0),
                    'komisi_transaksikoran'       => 0,
                    'komisioner'                  => $request->input('komisioner'),
                    'status_order'                => ($cCalc['piutang'] <= 0 || $request->boolean('disetujui_kredit')) ? 'Antrean_Pracetak' : 'Menunggu_Bayar',
                    'status_sppi'                 => 'Belum_Ditandatangani',
                ]);

                foreach ($datesCtk as $idx => $tglMuat) {
                    $isFirst = ($idx === 0);
                    OrderJadwalMuat::create([
                        'transaksi_id'  => $trxCetak->id,
                        'tanggal_muat'  => $tglMuat,
                        'edisi_ke'      => $idx + 1,
                        'jenis_muat'    => $isFirst ? 'BERNILAI' : 'BONUS',
                        'nominal_muat'  => $isFirst ? $cCalc['tagihan'] : 0,
                        'is_bonus'      => !$isFirst,
                        'status_tayang' => 'TERJADWAL',
                        'catatan'       => $isFirst
                            ? "Muat Bernilai (Paket {$noFakturPaket})"
                            : "Muat Bonus (Terbayar di Edisi #1 • Paket {$noFakturPaket})",
                    ]);
                }

                Sppi::create([
                    'nomor_sppi'   => Sppi::createCode('KRN'),
                    'media_type'   => 'koran',
                    'transaksi_id' => $trxCetak->id,
                    'nofaktur'     => $trxCetak->nofakturkoran,
                    'tanggal_sppi' => $tglTransaksi,
                    'status_sppi'  => 'Belum_Ditandatangani',
                ]);
            }

            // B. Simpan Anak: Iklan Online (TransaksiOnline)
            if ($hasOnl) {
                $oCalc = $mediaCalc['ONL'];
                $idIklanOnl = $request->input('onl_id_iklan') ?: JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'ONL'))->first()?->id;
                $portalsOnl = array_values(array_filter((array) $request->input('onl_portals', ['kabar-priangan.com'])));
                $platformsOnl = array_values(array_filter((array) $request->input('onl_platforms', ['Portal Berita'])));

                $fileOnlPath = null;
                if ($request->hasFile('onl_file_materi')) {
                    $fileOnlPath = $request->file('onl_file_materi')->store('materi_iklan', 'public');
                }

                $trxOnline = TransaksiOnline::create([
                    'paket_id'                     => $paket->id,
                    'nofakturonline'               => TransaksiOnline::createCode(),
                    'tanggal_transaksionline'      => $tglTransaksi,
                    'pelanggan_id'                 => $pelangganId,
                    'nama_pemasangonline'          => trim($request->nama_pemasang),
                    'nama_pic'                     => $request->input('nama_pic'),
                    'no_kontak_pemasang'           => $request->input('telepon_pemasang'),
                    'alamat_pemasangonline'        => $request->input('alamat_pemasang'),
                    'id_iklanonline'               => $idIklanOnl,
                    'produk_jasa'                  => $request->input('onl_produk_jasa') ?: $namaPaket,
                    'sales_ae_id'                  => $salesAe?->id,
                    'sales_iklanonline'            => $salesNama,
                    'total_muatiklanonline'        => $qtyOnl,
                    'tanggal_muatiklanonline'      => $datesOnl[0],
                    'tanggal_muat_list'            => implode(',', $datesOnl),
                    'portal_iklanonline'           => implode(', ', $portalsOnl),
                    'platforms_publikasi'          => $platformsOnl,
                    'skema_pembayaran'             => 'paket',
                    'status_produksi'              => 'antrean',
                    'kategori_produk_publikasi'    => $platformsOnl,
                    'materi_iklan'                 => $request->input('onl_materi_iklan') ?: $namaPaket,
                    'file_materi'                  => $fileOnlPath,
                    'url_materi_iklan'             => $request->input('onl_url_materi'),
                    'catatan_redaksi'              => ($request->input('onl_catatan') ? $request->input('onl_catatan') . ' • ' : '') . "Bagian dari Paket {$noFakturPaket}",
                    'harga_transaksionline'        => $oCalc['brutto'],
                    'diskon_transaksionline'       => $oCalc['diskon'],
                    'ppn_persen'                   => $ppnPersen,
                    'ppn_transaksionline'          => $oCalc['ppn'],
                    'totaltagihan_transaksionline' => $oCalc['tagihan'],
                    'jumlahbayar_transaksionline'  => $oCalc['bayar'],
                    'piutang_transaksionline'      => $oCalc['piutang'],
                    'insentif_persen'              => (float) $request->input('insentif_persen', 0),
                    'insentif_transaksionline'     => 0,
                    'komisi_persen'                => (float) $request->input('komisi_persen', 0),
                    'komisi_transaksionline'       => 0,
                    'komisioner'                   => $request->input('komisioner'),
                ]);

                Sppi::create([
                    'nomor_sppi'   => Sppi::createCode('ONL'),
                    'media_type'   => 'online',
                    'transaksi_id' => $trxOnline->id,
                    'nofaktur'     => $trxOnline->nofakturonline,
                    'tanggal_sppi' => $tglTransaksi,
                    'status_sppi'  => 'Belum_Ditandatangani',
                ]);
            }

            // C. Simpan Anak: Iklan TV (TransaksiPriangan)
            if ($hasPtv) {
                $tCalc = $mediaCalc['PTV'];
                $idIklanPtv = is_numeric($request->input('ptv_id_iklan')) ? (int) $request->input('ptv_id_iklan') : null;
                if (!$idIklanPtv) {
                    $idIklanPtv = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'PTV'))->first()?->id;
                }
                $portalsPtv = array_values(array_filter((array) $request->input('portal_iklanpriangan', [])));
                $platformsPtv = array_values(array_filter((array) $request->input('ptv_platforms', [])));
                if (!empty($portalsPtv)) {
                    $platformsPtv = array_values(array_unique(array_merge($platformsPtv, $portalsPtv)));
                }
                if (empty($platformsPtv)) {
                    $platformsPtv = ['Priangan TV Digital'];
                }

                $filePtvPath = null;
                if ($request->hasFile('ptv_file_materi')) {
                    $filePtvPath = $request->file('ptv_file_materi')->store('materi_iklan', 'public');
                }

                $trxTv = TransaksiPriangan::create([
                    'paket_id'                       => $paket->id,
                    'nofakturpriangan'               => TransaksiPriangan::createCode(),
                    'tanggal_transaksipriangan'      => $tglTransaksi,
                    'pelanggan_id'                   => $pelangganId,
                    'nama_pemasangpriangan'          => trim($request->nama_pemasang),
                    'nama_pic'                       => $request->input('nama_pic'),
                    'no_telp'                        => $request->input('telepon_pemasang'),
                    'alamat_pemasangpriangan'        => $request->input('alamat_pemasang'),
                    'id_iklanpriangan'               => $idIklanPtv,
                    'produk_jasa'                    => $request->input('ptv_produk_jasa') ?: $namaPaket,
                    'platforms_publikasi'            => $platformsPtv,
                    'materi_iklan'                   => $request->input('ptv_materi_iklan') ?: $namaPaket,
                    'file_materi'                    => $filePtvPath,
                    'url_materi_iklan'               => $request->input('ptv_url_materi'),
                    'catatan_redaksi'                => ($request->input('ptv_catatan') ? $request->input('ptv_catatan') . ' • ' : '') . "Bagian dari Paket {$noFakturPaket}",
                    'durasi_detik'                   => (int) $request->input('ptv_durasi_detik', 30),
                    'jam_tayang'                     => $request->input('ptv_jam_tayang', '19:00 WIB'),
                    'status_tayang'                  => 'terjadwal',
                    'sales_ae_id'                    => $salesAe?->id,
                    'sales_iklanpriangan'            => $salesNama,
                    'tanggal_muatiklanpriangan'      => $datesPtv[0],
                    'tanggal_muat_list'              => implode(',', $datesPtv),
                    'total_muatiklanpriangan'        => $qtyPtv,
                    'harga_transaksipriangan'        => $hargaSatuanPtv,
                    'diskon_transaksipriangan'       => $tCalc['diskon'],
                    'ppn_persen'                     => $ppnPersen,
                    'ppn_transaksipriangan'          => $tCalc['ppn'],
                    'totaltagihan_transaksipriangan' => $tCalc['tagihan'],
                    'jumlahbayar_transaksipriangan'  => $tCalc['bayar'],
                    'piutang_transaksipriangan'      => $tCalc['piutang'],
                    'insentif_persen'                => (float) $request->input('insentif_persen', 0),
                    'insentif_transaksipriangan'     => 0,
                    'komisi_persen'                  => (float) $request->input('komisi_persen', 0),
                    'komisi_transaksipriangan'       => 0,
                    'komisioner'                     => $request->input('komisioner'),
                ]);

                Sppi::create([
                    'nomor_sppi'   => Sppi::createCode('PTV'),
                    'media_type'   => 'priangan',
                    'transaksi_id' => $trxTv->id,
                    'nofaktur'     => $trxTv->nofakturpriangan,
                    'tanggal_sppi' => $tglTransaksi,
                    'status_sppi'  => 'Belum_Ditandatangani',
                ]);
            }

            // 4. Catat Kwitansi Pembayaran Kasir FO jika ada uang yang disetorkan
            if ($jumlahBayar > 0) {
                PembayaranIklan::create([
                    'nokwitansi'     => PembayaranIklan::createCode('PKT'),
                    'media_type'     => 'paket',
                    'transaksi_id'   => $paket->id,
                    'nofaktur'       => $paket->nofaktur_paket,
                    'tanggal_bayar'  => $tglTransaksi,
                    'nominal_bayar'  => $jumlahBayar,
                    'potongan_pph23' => 0,
                    'metode_bayar'   => ($jenisBayar === 'Tunai') ? 'Tunai_Kasir' : ($request->input('metode_bayar', 'Transfer_Bank')),
                    'nama_penyetor'  => trim($request->nama_pemasang),
                    'catatan'        => 'Pembayaran awal paket iklan lintas media di loket FO',
                    'kasir_user_id'  => Auth::id(),
                ]);
            }

            // 5. Generate 1 QR Faktur Paket Terpadu & Sync Buku Faktur Keuangan
            FakturQrService::generate($paket, 'paket', Auth::id());
            FakturSyncService::syncFromTransaksi($paket, 'paket');

            DB::commit();

            return redirect()->route('transaksipaket.show', $paket->id)
                ->with('success', "Paket Iklan {$paket->nofaktur_paket} berhasil disimpan dan terjadwal lintas media!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan paket iklan: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $paket = PaketIklan::with([
            'pelanggan',
            'salesAe',
            'inputOleh',
            'transaksiCetak.iklankoran',
            'transaksiCetak.jadwalMuat',
            'transaksiCetak.kopKota',
            'transaksiCetak.kopIklan',
            'transaksiOnline.iklanonline',
            'transaksiTv.iklanpriangan',
        ])->findOrFail($id);

        return view('pages.iklan.transaksi.paket.show', compact('paket'));
    }

    public function cetak(string $id)
    {
        $paket = PaketIklan::with([
            'pelanggan',
            'salesAe',
            'inputOleh',
            'transaksiCetak.iklankoran',
            'transaksiCetak.jadwalMuat',
            'transaksiCetak.kopKota',
            'transaksiOnline.iklanonline',
            'transaksiTv.iklanpriangan',
        ])->findOrFail($id);

        $qrFaktur = FakturQrService::resolveForCetak($paket, 'paket');
        $qrSvg    = $qrFaktur ? FakturQrService::renderSvg($qrFaktur, 106) : null;

        return view('pages.iklan.transaksi.paket.cetak', compact('paket', 'qrFaktur', 'qrSvg'));
    }

    public function destroy(string $id)
    {
        $paket = PaketIklan::with(['transaksiCetak', 'transaksiOnline', 'transaksiTv'])->findOrFail($id);

        if ($paket->is_pulled_akuntansi) {
            $msg = "Faktur Paket {$paket->no_faktur_paket} sudah dibukukan di Divisi Akuntansi (GL) dan tidak dapat dihapus. Gunakan prosedur Nota Kredit (Credit Note) Akuntansi.";
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        DB::transaction(function () use ($paket) {
            if ($paket->transaksiCetak) {
                $paket->transaksiCetak->jadwalMuat()->delete();
                $paket->transaksiCetak->delete();
            }
            if ($paket->transaksiOnline) {
                $paket->transaksiOnline->delete();
            }
            if ($paket->transaksiTv) {
                $paket->transaksiTv->delete();
            }
            FakturQrService::batalkanByNoFaktur($paket->nofaktur_paket, 'Paket dihapus');
            $paket->delete();
        });

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('transaksipaket.index')
            ->with('success', "Paket Iklan {$paket->nofaktur_paket} berhasil dihapus.");
    }
}

