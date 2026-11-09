<?php

namespace App\Http\Controllers;

use App\Models\JenisIklan;
use App\Models\Pelanggan;
use App\Models\SalesAe;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Tampilkan halaman Laporan Pemuatan Iklan & Rekap Omzet 3 Media (Acuan Resmi Print #4)
     */
    public function index(Request $request)
    {
        $filterData = $this->resolveReportData($request);

        return view('pages.iklan.laporan.index', $filterData);
    }

    /**
     * Cetak dokumen resmi Laporan Pemuatan Iklan HU. Priangan (Continuous Form Dot-Matrix - Print #4)
     */
    public function cetak(Request $request)
    {
        $filterData = $this->resolveReportData($request);

        return view('pages.iklan.laporan.cetak', $filterData);
    }

    /**
     * Laporan Penjualan Iklan Semua AE (Print #3) & Per Sales / AE (Print #1)
     */
    public function rekapSales(Request $request)
    {
        $data = $this->resolveRekapSalesData($request);

        return view('pages.iklan.laporan.rekap_sales', $data);
    }

    /**
     * Cetak Laporan Penjualan Iklan Semua AE & Per Sales / AE (Continuous Form Dot-Matrix - Print #3 & Print #1)
     */
    public function cetakRekapSales(Request $request)
    {
        $data = $this->resolveRekapSalesData($request);

        return view('pages.iklan.laporan.cetak_rekap_sales', $data);
    }

    /**
     * Laporan Insentif & Komisi AE (Slip Insentif Periode Fleksibel)
     */
    public function insentifAe(Request $request)
    {
        $data = $this->resolveInsentifAeData($request);

        return view('pages.iklan.laporan.insentif_ae', $data);
    }

    /**
     * Cetak Laporan Insentif & Komisi AE (Continuous Form Dot-Matrix)
     */
    public function cetakInsentifAe(Request $request)
    {
        $data = $this->resolveInsentifAeData($request);

        return view('pages.iklan.laporan.cetak_insentif_ae', $data);
    }

    /**
     * Laporan Klasifikasi Produk Iklan (Baris vs Kolom vs Display vs Advertorial vs Bonus)
     */
    public function klasifikasi(Request $request)
    {
        $data = $this->resolveKlasifikasiData($request);

        return view('pages.iklan.laporan.klasifikasi', $data);
    }

    /**
     * Cetak Laporan Klasifikasi Iklan (Continuous Form Dot-Matrix)
     */
    public function cetakKlasifikasi(Request $request)
    {
        $data = $this->resolveKlasifikasiData($request);

        return view('pages.iklan.laporan.cetak_klasifikasi', $data);
    }

    /**
     * Laporan Pembatalan Iklan & Audit Trail
     */
    public function pembatalan(Request $request)
    {
        $data = $this->resolvePembatalanData($request);

        return view('pages.iklan.laporan.pembatalan', $data);
    }

    /**
     * Cetak Laporan Pembatalan Iklan (Continuous Form Dot-Matrix)
     */
    public function cetakPembatalan(Request $request)
    {
        $data = $this->resolvePembatalanData($request);

        return view('pages.iklan.laporan.cetak_pembatalan', $data);
    }

    /**
     * Laporan Aging Piutang Iklan (0-30, 31-60, 61-90, >90 hari) Lintas Seluruh Media
     */
    public function agingPiutang(Request $request)
    {
        $data = $this->resolveAgingPiutangData($request);

        return view('pages.iklan.laporan.aging_piutang', $data);
    }

    /**
     * Cetak Laporan Aging Piutang Iklan (Continuous Form Dot-Matrix)
     */
    public function cetakAgingPiutang(Request $request)
    {
        $data = $this->resolveAgingPiutangData($request);

        return view('pages.iklan.laporan.cetak_aging_piutang', $data);
    }

    /**
     * Universal Resolver Periode Fleksibel:
     * Mendukung Harian (1 Tanggal), Mingguan (7 Hari), Bulanan (Bulan & Tahun), Tahunan (1 Tahun), & Rentang Kustom (Dari - Sampai)
     */
    private function resolveFlexiblePeriod(Request $request, string $defaultMode = 'bulanan'): array
    {
        $now = Carbon::now();
        $rawMode = $request->input('mode_periode', '');
        $rawPeriod = $request->input('period', '');
        $rawTanggal = $request->input('tanggal', '');

        // Pemetaan kompatibilitas penuh antara mode_periode & period lama
        if ($rawMode !== '') {
            $modePeriode = $rawMode;
        } elseif ($rawPeriod !== '') {
            $modePeriode = match ($rawPeriod) {
                'today', 'harian'          => 'harian',
                '7days', 'week', 'mingguan'=> 'mingguan',
                'month', 'last_month', 'bulanan' => 'bulanan',
                'year', 'tahunan'          => 'tahunan',
                'custom', '30days'         => 'custom',
                default                    => $defaultMode,
            };
        } elseif (!empty($rawTanggal)) {
            $modePeriode = 'harian';
        } else {
            $modePeriode = $defaultMode;
        }

        $bulan = (int) $request->input('bulan', $now->month);
        $tahun = (int) $request->input('tahun', $now->year);
        if ($rawPeriod === 'last_month' && !$request->has('bulan')) {
            $sub = $now->copy()->subMonth();
            $bulan = (int) $sub->month;
            $tahun = (int) $sub->year;
        }

        $tanggal = !empty($rawTanggal) ? $rawTanggal : $now->toDateString();
        $dari = $request->input('dari', '');
        $sampai = $request->input('sampai', '');

        if ($modePeriode === 'harian') {
            $chosenDate = !empty($rawTanggal) ? $rawTanggal : (!empty($dari) ? $dari : $now->toDateString());
            $cTgl = Carbon::parse($chosenDate);
            $startDate = $cTgl->copy()->startOfDay();
            $endDate = $cTgl->copy()->endOfDay();
            $tanggal = $cTgl->toDateString();
            $dari = $tanggal;
            $sampai = $tanggal;
            $bulan = (int) $cTgl->month;
            $tahun = (int) $cTgl->year;
            $hariNama = strtoupper($cTgl->locale('id')->isoFormat('dddd'));
            $hariTanggal = "{$hariNama} / " . $cTgl->format('d-m-Y');
            $periodeLabel = 'Harian: ' . $cTgl->translatedFormat('l, d F Y');
            $periodTitle = 'Harian';
            $periodSub = $cTgl->translatedFormat('d M Y');
            $period = 'today';
        } elseif ($modePeriode === 'mingguan') {
            $anchorDate = !empty($rawTanggal) ? Carbon::parse($rawTanggal) : (!empty($sampai) ? Carbon::parse($sampai) : $now->copy());
            $endDate = $anchorDate->copy()->endOfDay();
            $startDate = $anchorDate->copy()->subDays(6)->startOfDay();
            $tanggal = $anchorDate->toDateString();
            $dari = $startDate->toDateString();
            $sampai = $endDate->toDateString();
            $hariTanggal = 'MINGGUAN : ' . $startDate->format('d-m-Y') . ' S/D ' . $endDate->format('d-m-Y');
            $periodeLabel = 'Mingguan (' . $startDate->translatedFormat('d M Y') . ' – ' . $endDate->translatedFormat('d M Y') . ')';
            $periodTitle = 'Mingguan (7 Hari)';
            $periodSub = $startDate->translatedFormat('d M') . ' - ' . $endDate->translatedFormat('d M Y');
            $period = '7days';
        } elseif ($modePeriode === 'tahunan') {
            $startDate = Carbon::create($tahun, 1, 1)->startOfYear()->startOfDay();
            $endDate = $startDate->copy()->endOfYear()->endOfDay();
            $dari = $startDate->toDateString();
            $sampai = $endDate->toDateString();
            $hariTanggal = 'TAHUN ' . $tahun . ' (JANUARI - DESEMBER ' . $tahun . ')';
            $periodeLabel = 'Tahunan: Tahun ' . $tahun;
            $periodTitle = 'Tahunan';
            $periodSub = 'Tahun ' . $tahun;
            $period = 'year';
        } elseif ($modePeriode === 'custom') {
            if ($rawPeriod === '30days' && empty($dari)) {
                $startDate = $now->copy()->subDays(29)->startOfDay();
                $endDate = $now->copy()->endOfDay();
            } else {
                $dStr = !empty($dari) ? $dari : $now->copy()->startOfMonth()->toDateString();
                $sStr = !empty($sampai) ? $sampai : $now->toDateString();
                $startDate = Carbon::parse($dStr)->startOfDay();
                $endDate = Carbon::parse($sStr)->endOfDay();
                if ($startDate->gt($endDate)) {
                    [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
                }
            }
            $dari = $startDate->toDateString();
            $sampai = $endDate->toDateString();
            if ($startDate->isSameDay($endDate)) {
                $hariNama = strtoupper($startDate->locale('id')->isoFormat('dddd'));
                $hariTanggal = "{$hariNama} / " . $startDate->format('d-m-Y');
                $periodeLabel = 'Tanggal: ' . $startDate->translatedFormat('l, d F Y');
                $periodSub = $startDate->translatedFormat('d M Y');
            } else {
                $hariTanggal = $startDate->format('d-m-Y') . ' S/D ' . $endDate->format('d-m-Y');
                $periodeLabel = 'Rentang: ' . $startDate->translatedFormat('d M Y') . ' s/d ' . $endDate->translatedFormat('d M Y');
                $periodSub = $startDate->translatedFormat('d M') . ' - ' . $endDate->translatedFormat('d M Y');
            }
            $periodTitle = 'Rentang Kustom';
            $period = 'custom';
        } else {
            // Default: 'bulanan'
            $modePeriode = 'bulanan';
            $bulan = max(1, min(12, $bulan));
            $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth()->startOfDay();
            $endDate = $startDate->copy()->endOfMonth()->endOfDay();
            $dari = $startDate->toDateString();
            $sampai = $endDate->toDateString();
            $hariTanggal = 'BULAN ' . strtoupper($startDate->translatedFormat('F Y'));
            $periodeLabel = 'Bulanan: ' . $startDate->translatedFormat('F Y');
            $periodTitle = 'Bulanan';
            $periodSub = $startDate->translatedFormat('F Y');
            $period = 'month';
        }

        return [
            'modePeriode'  => $modePeriode,
            'period'       => $period,
            'periodTitle'  => $periodTitle,
            'periodSub'    => $periodSub,
            'periodLabel'  => $periodeLabel,
            'periodeLabel' => $periodeLabel,
            'hariTanggal'  => $hariTanggal,
            'tanggal'      => $modePeriode === 'harian' ? $tanggal : ($rawTanggal ?: $now->toDateString()),
            'bulan'        => $bulan,
            'tahun'        => $tahun,
            'dari'         => $dari,
            'sampai'       => $sampai,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
        ];
    }

    /**
     * Helper: Klasifikasi tipe dimensi koran (BARIS, KOLOM, DISPL) sesuai standar IKL.EXE
     */
    private function resolveKoranDimension(TransaksiKoran $row): array
    {
        $jenisStr = strtoupper(($row->kategori_form ?? '') . ' ' . ($row->iklankoran?->nama_jenis ?? '') . ' ' . ($row->iklankoran?->tipe_dasar ?? ''));
        $isBaris = ($row->kategori_form === 'BARIS' || str_contains($jenisStr, 'BARIS'));
        $isKolom = !$isBaris && ($row->kategori_form === 'KOLOM' || str_contains($jenisStr, 'KOLOM') || ((int) ($row->lebar_kolom ?? 1) === 1 && $row->kategori_form !== 'DISPLAY'));
        $isDispl = !$isBaris && !$isKolom;

        $baris = $isBaris ? (int) ($row->jumlah_baris ?? 2) : 0;
        $klmWidth = !$isBaris ? (int) ($row->lebar_kolom ?? 1) : 0;
        $kolom = $isKolom ? $klmWidth : 0;
        $displ = $isDispl ? $klmWidth : 0;
        $tinggiMm = !$isBaris ? (int) ($row->tinggi_mm ?? 0) : 0;
        $jmlMm = !$isBaris ? ($klmWidth * $tinggiMm) : 0;

        return [
            'ad_type'  => $isBaris ? 'BARIS' : ($isKolom ? 'KOLOM' : 'DISPL'),
            'baris'    => $baris,
            'klm'      => $klmWidth,
            'kolom'    => $kolom,
            'displ'    => $displ,
            'mm'       => $tinggiMm,
            'mili'     => $tinggiMm,
            'jml_mm'   => $jmlMm,
            'kolom_mm' => $isKolom ? $jmlMm : 0,
            'displ_mm' => $isDispl ? $jmlMm : 0,
        ];
    }

    /**
     * Helper: Pengolahan data Laporan Penjualan Semua AE (Print #3) & Per Sales / AE (Print #1)
     */
    private function resolveRekapSalesData(Request $request): array
    {
        $p = $this->resolveFlexiblePeriod($request, 'bulanan');
        $startDate = $p['startDate'];
        $endDate = $p['endDate'];

        $salesAeId = $request->input('sales_ae_id', 'all');
        $tab = $request->input('tab', 'all'); // 'all', 'semua_ae' (Print #3), 'per_sales' (Print #1)
        $modeCetak = $request->input('mode_cetak', ''); // 'semua_ae', 'per_sales', 'gabungan'
        $media = $request->input('media', 'koran'); // 'koran' (default IKL.EXE), 'all', 'online', 'tv'

        $salesAes = SalesAe::where('is_active', true)->orderBy('kode_ae')->get();

        $selectedAe = null;
        if ($salesAeId !== 'all' && $salesAeId !== '') {
            $selectedAe = $salesAes->firstWhere('id', (int) $salesAeId) ?? SalesAe::find($salesAeId);
        }

        // Ambil Transaksi Koran pada rentang periode
        $queryKoran = TransaksiKoran::with(['salesAe', 'iklankoran', 'pelanggan'])
            ->whereBetween('tanggal_transaksikoran', [$startDate, $endDate])
            ->where('status_order', '!=', 'Batal');

        if ($selectedAe) {
            $queryKoran->where('sales_ae_id', $selectedAe->id);
        }

        $transaksiList = ($media === 'all' || $media === 'koran')
            ? $queryKoran->orderBy('tanggal_transaksikoran')->orderBy('id')->get()
            : collect();

        // Tambahan Multi-Media (Online & TV) jika pengguna memilih 'all', 'online', atau 'tv'
        $onlineList = collect();
        if (in_array($media, ['all', 'online'])) {
            $qOnl = TransaksiOnline::with(['salesAe', 'iklanonline', 'pelanggan'])
                ->whereBetween('tanggal_transaksionline', [$startDate, $endDate]);
            if ($selectedAe) {
                $qOnl->where('sales_ae_id', $selectedAe->id);
            }
            $onlineList = $qOnl->orderBy('tanggal_transaksionline')->orderBy('id')->get();
        }

        $tvList = collect();
        if (in_array($media, ['all', 'tv'])) {
            $qTv = TransaksiPriangan::with(['salesAe', 'iklanpriangan', 'pelanggan'])
                ->whereBetween('tanggal_transaksipriangan', [$startDate, $endDate]);
            if ($selectedAe) {
                $qTv->where('sales_ae_id', $selectedAe->id);
            }
            $tvList = $qTv->orderBy('tanggal_transaksipriangan')->orderBy('id')->get();
        }

        // Agregasi per Sales AE (Sesuai Print #3: Laporan Penjualan Iklan Semua AE - Sektor Perolehan)
        $rekapPerAe = [];
        $aeGroups = $selectedAe ? collect([$selectedAe]) : $salesAes;

        foreach ($aeGroups as $ae) {
            $itemsKoranAe = $transaksiList->where('sales_ae_id', $ae->id);
            $itemsOnlineAe = $onlineList->where('sales_ae_id', $ae->id);
            $itemsTvAe = $tvList->where('sales_ae_id', $ae->id);
            $rekapPerAe[$ae->id] = $this->aggregateSalesGroup(
                $ae->kode_ae,
                $ae->nama_ae,
                $ae->jenis_kode,
                $ae,
                $itemsKoranAe,
                $itemsOnlineAe,
                $itemsTvAe
            );
        }

        // Tangkap transaksi tanpa sales_ae_id (Front Office / Walk-In) jika mode 'all'
        if (!$selectedAe) {
            $knownIds = $salesAes->pluck('id')->all();
            $unassignedKoran = $transaksiList->filter(fn($t) => empty($t->sales_ae_id) || !in_array($t->sales_ae_id, $knownIds));
            $unassignedOnline = $onlineList->filter(fn($t) => empty($t->sales_ae_id) || !in_array($t->sales_ae_id, $knownIds));
            $unassignedTv = $tvList->filter(fn($t) => empty($t->sales_ae_id) || !in_array($t->sales_ae_id, $knownIds));

            if ($unassignedKoran->isNotEmpty() || $unassignedOnline->isNotEmpty() || $unassignedTv->isNotEmpty()) {
                $dummyFo = (object) [
                    'id'         => 'fo_unassigned',
                    'kode_ae'    => 'FO',
                    'nama_ae'    => 'FRONT OFFICE (WALK-IN)',
                    'jenis_kode' => 'FO',
                ];
                $rekapPerAe['fo_unassigned'] = $this->aggregateSalesGroup(
                    'FO',
                    'FRONT OFFICE (WALK-IN)',
                    'FO',
                    $dummyFo,
                    $unassignedKoran,
                    $unassignedOnline,
                    $unassignedTv
                );
            }
        }

        // Rincian Perolehan Pemasang (Sesuai Print #1: Laporan Penjualan Iklan Per Sales / AE)
        $rincianList = [];
        foreach ($transaksiList as $trx) {
            $dim = $this->resolveKoranDimension($trx);
            $brutto = (int) $trx->nilai_brutto;
            $reduksi = (int) $trx->nilai_diskon;
            $netto = (int) $trx->nilai_netto;
            $ppn = (int) $trx->nilai_ppn;
            $materai = (int) $trx->nilai_materai;
            $jumlah = (int) $trx->jumlah_yang_dibayar;
            $bayar = (int) ($trx->jumlahbayar_transaksikoran ?? 0);
            $piutang = (int) $trx->sisa_piutang;
            $diskonPersen = (float) ($trx->diskon_persen ?? ($brutto > 0 && $reduksi > 0 ? round(($reduksi / $brutto) * 100, 2) : 0));
            $jenisStr = strtoupper($trx->iklankoran?->nama_jenis ?: ($trx->kategori_form ?: 'IKLAN KORAN'));
            $isBonus = ($jumlah <= 0 || str_contains($jenisStr, 'BONUS'));

            $rincianList[] = (object) [
                'id'            => $trx->id,
                'media_code'    => 'KRN',
                'media_name'    => 'Koran',
                'nofaktur'      => $trx->nofakturkoran,
                'no_order'      => $trx->id ? (2026100000 + $trx->id) : $trx->nofakturkoran,
                'tanggal'       => $trx->tanggal_transaksikoran,
                'tanggal_muat'  => $trx->tanggal_muatkoran ?? $trx->tanggal_transaksikoran,
                'nama_pemasang' => strtoupper($trx->pelanggan?->nama_instansi ?: $trx->nama_pemasangkoran),
                'alamat'        => strtoupper($trx->alamat_pemasangkoran ?: ($trx->pelanggan?->alamat ?: '-')),
                'jenis_iklan'   => $jenisStr,
                'sales_ae_id'   => $trx->sales_ae_id ?: 'fo_unassigned',
                'sales_kode'    => $trx->salesAe?->kode_ae ?: 'FO',
                'sales_nama'    => strtoupper($trx->salesAe?->nama_ae ?: ($trx->sales_iklankoran ?: 'FRONT OFFICE')),
                'brs'           => $dim['baris'],
                'klm'           => $dim['klm'],
                'mm'            => $dim['mm'],
                'jml_mm'        => $dim['jml_mm'],
                'harga_iklan'   => $brutto,
                'diskon_persen' => $diskonPersen,
                'reduksi'       => $reduksi,
                'netto'         => $netto,
                'ppn'           => $ppn,
                'materai'       => $materai,
                'jumlah'        => $jumlah,
                'bayar'         => $bayar,
                'piutang'       => $piutang,
                'is_lunas'      => $piutang <= 0,
                'is_bonus'      => $isBonus,
                'status_label'  => $isBonus ? 'BONUS' : ($piutang <= 0 ? 'LUNAS' : 'PIUTANG'),
                'insentif'      => (int) ($trx->insentif_transaksikoran ?? 0),
                'komisi'        => (int) ($trx->komisi_transaksikoran ?? 0),
            ];
        }

        foreach ($onlineList as $trx) {
            $brutto = (int) $trx->nilai_brutto;
            $reduksi = (int) $trx->nilai_diskon;
            $netto = (int) $trx->nilai_netto;
            $ppn = (int) $trx->nilai_ppn;
            $jumlah = (int) $trx->totaltagihan_transaksionline;
            $bayar = (int) ($trx->jumlahbayar_transaksionline ?? 0);
            $piutang = (int) $trx->piutang_transaksionline;
            $diskonPersen = $brutto > 0 && $reduksi > 0 ? round(($reduksi / $brutto) * 100, 2) : 0;
            $jenisStr = strtoupper(is_array($trx->kategori_produk_publikasi) ? implode(', ', $trx->kategori_produk_publikasi) : ($trx->iklanonline?->nama_jenis ?: 'MEDIA ONLINE'));
            $isBonus = ($jumlah <= 0 || str_contains($jenisStr, 'BONUS'));

            $rincianList[] = (object) [
                'id'            => $trx->id,
                'media_code'    => 'ONL',
                'media_name'    => 'Online',
                'nofaktur'      => $trx->nofakturonline,
                'no_order'      => $trx->id ? (2026200000 + $trx->id) : $trx->nofakturonline,
                'tanggal'       => $trx->tanggal_transaksionline,
                'tanggal_muat'  => $trx->tanggal_muatiklanonline ?? $trx->tanggal_transaksionline,
                'nama_pemasang' => strtoupper($trx->pelanggan?->nama_instansi ?: $trx->nama_pemasangonline),
                'alamat'        => strtoupper($trx->alamat_pemasangonline ?: ($trx->pelanggan?->alamat ?: '-')),
                'jenis_iklan'   => $jenisStr,
                'sales_ae_id'   => $trx->sales_ae_id ?: 'fo_unassigned',
                'sales_kode'    => $trx->salesAe?->kode_ae ?: 'FO',
                'sales_nama'    => strtoupper($trx->salesAe?->nama_ae ?: ($trx->sales_iklanonline ?: 'FRONT OFFICE')),
                'brs'           => 0,
                'klm'           => (int) ($trx->total_muatiklanonline ?? 1),
                'mm'            => 0,
                'jml_mm'        => 0,
                'harga_iklan'   => $brutto,
                'diskon_persen' => $diskonPersen,
                'reduksi'       => $reduksi,
                'netto'         => $netto,
                'ppn'           => $ppn,
                'materai'       => 0,
                'jumlah'        => $jumlah,
                'bayar'         => $bayar,
                'piutang'       => $piutang,
                'is_lunas'      => $piutang <= 0,
                'is_bonus'      => $isBonus,
                'status_label'  => $isBonus ? 'BONUS' : ($piutang <= 0 ? 'LUNAS' : 'PIUTANG'),
                'insentif'      => (int) ($trx->insentif_transaksionline ?? 0),
                'komisi'        => (int) ($trx->komisi_transaksionline ?? 0),
            ];
        }

        foreach ($tvList as $trx) {
            $brutto = (int) ($trx->nilai_brutto ?? $trx->harga_transaksipriangan ?? 0);
            $reduksi = (int) ($trx->nilai_diskon ?? $trx->diskon_transaksipriangan ?? 0);
            $netto = (int) ($trx->nilai_netto ?? ($brutto - $reduksi));
            $ppn = (int) ($trx->nilai_ppn ?? $trx->ppn_transaksipriangan ?? 0);
            $jumlah = (int) $trx->totaltagihan_transaksipriangan;
            $bayar = (int) ($trx->jumlahbayar_transaksipriangan ?? 0);
            $piutang = (int) $trx->piutang_transaksipriangan;
            $diskonPersen = $brutto > 0 && $reduksi > 0 ? round(($reduksi / $brutto) * 100, 2) : 0;
            $jenisStr = strtoupper($trx->iklanpriangan?->nama_jenis ?: 'PRIANGAN TV');
            $isBonus = ($jumlah <= 0 || str_contains($jenisStr, 'BONUS'));

            $rincianList[] = (object) [
                'id'            => $trx->id,
                'media_code'    => 'PTV',
                'media_name'    => 'Priangan TV',
                'nofaktur'      => $trx->nofakturpriangan,
                'no_order'      => $trx->id ? (2026300000 + $trx->id) : $trx->nofakturpriangan,
                'tanggal'       => $trx->tanggal_transaksipriangan,
                'tanggal_muat'  => $trx->tanggal_muatiklanpriangan ?? $trx->tanggal_transaksipriangan,
                'nama_pemasang' => strtoupper($trx->pelanggan?->nama_instansi ?: $trx->nama_pemasangpriangan),
                'alamat'        => strtoupper($trx->alamat_pemasangpriangan ?: ($trx->pelanggan?->alamat ?: '-')),
                'jenis_iklan'   => $jenisStr,
                'sales_ae_id'   => $trx->sales_ae_id ?: 'fo_unassigned',
                'sales_kode'    => $trx->salesAe?->kode_ae ?: 'FO',
                'sales_nama'    => strtoupper($trx->salesAe?->nama_ae ?: ($trx->sales_iklanpriangan ?: 'FRONT OFFICE')),
                'brs'           => 0,
                'klm'           => (int) ($trx->total_muatiklanpriangan ?? 1),
                'mm'            => 0,
                'jml_mm'        => 0,
                'harga_iklan'   => $brutto,
                'diskon_persen' => $diskonPersen,
                'reduksi'       => $reduksi,
                'netto'         => $netto,
                'ppn'           => $ppn,
                'materai'       => 0,
                'jumlah'        => $jumlah,
                'bayar'         => $bayar,
                'piutang'       => $piutang,
                'is_lunas'      => $piutang <= 0,
                'is_bonus'      => $isBonus,
                'status_label'  => $isBonus ? 'BONUS' : ($piutang <= 0 ? 'LUNAS' : 'PIUTANG'),
                'insentif'      => (int) ($trx->insentif_transaksipriangan ?? 0),
                'komisi'        => (int) ($trx->komisi_transaksipriangan ?? 0),
            ];
        }

        $rekapCollection = collect($rekapPerAe);
        $totalTagihanAll = $rekapCollection->sum('total_tagihan');
        $totalBayarAll = $rekapCollection->sum('total_bayar');

        // Hitung % Kontribusi Omzet & Rasio Lunas per AE (Standar Industri)
        foreach ($rekapPerAe as $k => $rowAe) {
            $rekapPerAe[$k]['kontribusi_persen'] = $totalTagihanAll > 0
                ? round(($rowAe['total_tagihan'] / $totalTagihanAll) * 100, 1)
                : 0.0;
            $rekapPerAe[$k]['rasio_lunas'] = $rowAe['total_tagihan'] > 0
                ? round(($rowAe['total_bayar'] / $rowAe['total_tagihan']) * 100, 1)
                : ($rowAe['count'] > 0 ? 100.0 : 0.0);
        }

        $grandTotal = [
            'order_brs'      => $rekapCollection->sum('order_brs'),
            'order_klm'      => $rekapCollection->sum('order_klm'),
            'order_dsp'      => $rekapCollection->sum('order_dsp'),
            'count'          => $rekapCollection->sum('count'),
            'order_bonus'    => $rekapCollection->sum('order_bonus'),
            'vol_baris'      => $rekapCollection->sum('vol_baris'),
            'vol_kolom_mm'   => $rekapCollection->sum('vol_kolom_mm'),
            'vol_displ_mm'   => $rekapCollection->sum('vol_displ_mm'),
            'total_mili'     => $rekapCollection->sum('total_mili'),
            'total_bruto'    => $rekapCollection->sum('total_bruto'),
            'total_reduksi'  => $rekapCollection->sum('total_reduksi'),
            'total_netto'    => $rekapCollection->sum('total_netto'),
            'total_ppn'      => $rekapCollection->sum('total_ppn'),
            'total_materai'  => $rekapCollection->sum('total_materai'),
            'total_tagihan'  => $totalTagihanAll,
            'total_bayar'    => $totalBayarAll,
            'total_piutang'  => $rekapCollection->sum('total_piutang'),
            'total_insentif' => $rekapCollection->sum('total_insentif'),
            'total_komisi'   => $rekapCollection->sum('total_komisi'),
            'rasio_lunas'    => $totalTagihanAll > 0 ? round(($totalBayarAll / $totalTagihanAll) * 100, 1) : 0.0,
        ];

        // Kelompokkan rincian per AE untuk cetak Print #1 per Sales secara rapi
        $rincianGroupedByAe = collect($rincianList)->groupBy('sales_nama');

        return array_merge($p, compact(
            'salesAeId',
            'selectedAe',
            'salesAes',
            'tab',
            'modeCetak',
            'media',
            'rekapPerAe',
            'transaksiList',
            'rincianList',
            'rincianGroupedByAe',
            'grandTotal'
        ));
    }

    /**
     * Helper agregasi 1 grup Sales / AE (Mendukung Koran + Multi-Media & Metrik Standar Industri)
     */
    private function aggregateSalesGroup(
        string $kodeAe,
        string $namaAe,
        string $jenisKode,
        $aeObj,
        Collection $itemsKoran,
        ?Collection $itemsOnline = null,
        ?Collection $itemsTv = null
    ): array {
        $itemsOnline = $itemsOnline ?? collect();
        $itemsTv = $itemsTv ?? collect();

        $orderBrs = 0;
        $orderKlm = 0;
        $orderDsp = 0;
        $orderBonus = 0;
        $volBaris = 0;
        $volKolomMm = 0;
        $volDisplMm = 0;
        $totalBruto = 0;
        $totalReduksi = 0;
        $totalNetto = 0;
        $totalPpn = 0;
        $totalMaterai = 0;
        $totalTagihan = 0;
        $totalBayar = 0;
        $totalPiutang = 0;
        $totalInsentif = 0;
        $totalKomisi = 0;

        foreach ($itemsKoran as $trx) {
            $dim = $this->resolveKoranDimension($trx);
            if ($dim['ad_type'] === 'BARIS') {
                $orderBrs++;
            } elseif ($dim['ad_type'] === 'KOLOM') {
                $orderKlm++;
            } else {
                $orderDsp++;
            }

            $jenisStr = strtoupper(($trx->kategori_form ?? '') . ' ' . ($trx->iklankoran?->nama_jenis ?? ''));
            if ((int) $trx->jumlah_yang_dibayar <= 0 || str_contains($jenisStr, 'BONUS')) {
                $orderBonus++;
            }

            $volBaris += $dim['baris'];
            $volKolomMm += $dim['kolom_mm'];
            $volDisplMm += $dim['displ_mm'];

            $totalBruto += (int) $trx->nilai_brutto;
            $totalReduksi += (int) $trx->nilai_diskon;
            $totalNetto += (int) $trx->nilai_netto;
            $totalPpn += (int) $trx->nilai_ppn;
            $totalMaterai += (int) $trx->nilai_materai;
            $totalTagihan += (int) $trx->jumlah_yang_dibayar;
            $totalBayar += (int) ($trx->jumlahbayar_transaksikoran ?? 0);
            $totalPiutang += (int) $trx->sisa_piutang;
            $totalInsentif += (int) ($trx->insentif_transaksikoran ?? 0);
            $totalKomisi += (int) ($trx->komisi_transaksikoran ?? 0);
        }

        foreach ($itemsOnline as $trx) {
            $orderDsp++;
            $tagihan = (int) $trx->totaltagihan_transaksionline;
            if ($tagihan <= 0) {
                $orderBonus++;
            }
            $totalBruto += (int) $trx->nilai_brutto;
            $totalReduksi += (int) $trx->nilai_diskon;
            $totalNetto += (int) $trx->nilai_netto;
            $totalPpn += (int) $trx->nilai_ppn;
            $totalTagihan += $tagihan;
            $totalBayar += (int) ($trx->jumlahbayar_transaksionline ?? 0);
            $totalPiutang += (int) $trx->piutang_transaksionline;
            $totalInsentif += (int) ($trx->insentif_transaksionline ?? 0);
            $totalKomisi += (int) ($trx->komisi_transaksionline ?? 0);
        }

        foreach ($itemsTv as $trx) {
            $orderDsp++;
            $tagihan = (int) $trx->totaltagihan_transaksipriangan;
            if ($tagihan <= 0) {
                $orderBonus++;
            }
            $bruto = (int) ($trx->nilai_brutto ?? $trx->harga_transaksipriangan ?? 0);
            $reduksi = (int) ($trx->nilai_diskon ?? $trx->diskon_transaksipriangan ?? 0);
            $netto = (int) ($trx->nilai_netto ?? ($bruto - $reduksi));
            $ppn = (int) ($trx->nilai_ppn ?? $trx->ppn_transaksipriangan ?? 0);

            $totalBruto += $bruto;
            $totalReduksi += $reduksi;
            $totalNetto += $netto;
            $totalPpn += $ppn;
            $totalTagihan += $tagihan;
            $totalBayar += (int) ($trx->jumlahbayar_transaksipriangan ?? 0);
            $totalPiutang += (int) $trx->piutang_transaksipriangan;
            $totalInsentif += (int) ($trx->insentif_transaksipriangan ?? 0);
            $totalKomisi += (int) ($trx->komisi_transaksipriangan ?? 0);
        }

        $totalCount = $itemsKoran->count() + $itemsOnline->count() + $itemsTv->count();

        return [
            'ae'                => $aeObj,
            'sektor_label'      => trim($kodeAe . ' ' . strtoupper($namaAe)),
            'order_brs'         => $orderBrs,
            'order_klm'         => $orderKlm,
            'order_dsp'         => $orderDsp,
            'count'             => $totalCount,
            'order_bonus'       => $orderBonus,
            'vol_baris'         => $volBaris,
            'vol_kolom_mm'      => $volKolomMm,
            'vol_displ_mm'      => $volDisplMm,
            'total_mili'        => $volKolomMm + $volDisplMm,
            'total_bruto'       => $totalBruto,
            'total_reduksi'     => $totalReduksi,
            'total_netto'       => $totalNetto,
            'total_ppn'         => $totalPpn,
            'total_materai'     => $totalMaterai,
            'total_tagihan'     => $totalTagihan,
            'total_bayar'       => $totalBayar,
            'total_piutang'     => $totalPiutang,
            'total_insentif'    => $totalInsentif,
            'total_komisi'      => $totalKomisi,
            'kontribusi_persen' => 0.0,
            'rasio_lunas'       => 0.0,
        ];
    }

    /**
     * Helper: Pengolahan data Insentif & Komisi AE (Mendukung Periode Fleksibel + Filter Sales, Media & Status)
     */
    private function resolveInsentifAeData(Request $request): array
    {
        $p = $this->resolveFlexiblePeriod($request, 'bulanan');
        $startDate = $p['startDate'];
        $endDate = $p['endDate'];

        $salesAeId = $request->input('sales_ae_id', 'all');
        $media = $request->input('media', 'all'); // 'all', 'koran', 'online', 'tv'
        $status = $request->input('status', 'all'); // 'all', 'lunas', 'piutang'

        $salesAes = SalesAe::where('is_active', true)->where('kode_ae', '!=', 'FO')->orderBy('kode_ae')->get();
        $aeGroups = ($salesAeId !== 'all' && $salesAeId !== '')
            ? $salesAes->where('id', (int) $salesAeId)
            : $salesAes;

        $dataInsentif = [];
        foreach ($aeGroups as $ae) {
            // 1. Koran
            $itemsKoran = collect();
            if (in_array($media, ['all', 'koran'])) {
                $qKoran = TransaksiKoran::with('iklankoran')
                    ->where('sales_ae_id', $ae->id)
                    ->whereBetween('tanggal_transaksikoran', [$startDate, $endDate])
                    ->where('status_order', '!=', 'Batal');
                if ($status === 'lunas') {
                    $qKoran->where('piutang_transaksikoran', '<=', 0);
                } elseif ($status === 'piutang') {
                    $qKoran->where('piutang_transaksikoran', '>', 0);
                }
                $itemsKoran = $qKoran->get();
            }

            // 2. Online
            $itemsOnline = collect();
            if (in_array($media, ['all', 'online'])) {
                $qOnl = TransaksiOnline::with('iklanonline')
                    ->where('sales_ae_id', $ae->id)
                    ->whereBetween('tanggal_transaksionline', [$startDate, $endDate]);
                if ($status === 'lunas') {
                    $qOnl->where('piutang_transaksionline', '<=', 0);
                } elseif ($status === 'piutang') {
                    $qOnl->where('piutang_transaksionline', '>', 0);
                }
                $itemsOnline = $qOnl->get();
            }

            // 3. Priangan TV
            $itemsTv = collect();
            if (in_array($media, ['all', 'tv'])) {
                $qTv = TransaksiPriangan::with('iklanpriangan')
                    ->where('sales_ae_id', $ae->id)
                    ->whereBetween('tanggal_transaksipriangan', [$startDate, $endDate]);
                if ($status === 'lunas') {
                    $qTv->where('piutang_transaksipriangan', '<=', 0);
                } elseif ($status === 'piutang') {
                    $qTv->where('piutang_transaksipriangan', '>', 0);
                }
                $itemsTv = $qTv->get();
            }

            $agg = $this->aggregateSalesGroup($ae->kode_ae, $ae->nama_ae, $ae->jenis_kode, $ae, $itemsKoran, $itemsOnline, $itemsTv);

            $lunasKoran = $itemsKoran->where('piutang_transaksikoran', '<=', 0)->sum('totaltagihan_transaksikoran');
            $lunasOnline = $itemsOnline->where('piutang_transaksionline', '<=', 0)->sum('totaltagihan_transaksionline');
            $lunasTv = $itemsTv->where('piutang_transaksipriangan', '<=', 0)->sum('totaltagihan_transaksipriangan');
            $lunas = (int) ($lunasKoran + $lunasOnline + $lunasTv);

            $belumLunasKoran = $itemsKoran->where('piutang_transaksikoran', '>', 0)->sum('totaltagihan_transaksikoran');
            $belumLunasOnline = $itemsOnline->where('piutang_transaksionline', '>', 0)->sum('totaltagihan_transaksionline');
            $belumLunasTv = $itemsTv->where('piutang_transaksipriangan', '>', 0)->sum('totaltagihan_transaksipriangan');
            $belumLunas = (int) ($belumLunasKoran + $belumLunasOnline + $belumLunasTv);

            $omzetTotal = (int) $agg['total_tagihan'];
            $insentifTercatat = (int) $agg['total_insentif'];
            $komisiTercatat   = (int) $agg['total_komisi'];

            $dataInsentif[] = [
                'ae'               => $ae,
                'sektor_label'     => trim($ae->kode_ae . ' ' . strtoupper($ae->nama_ae)),
                'order_brs'        => $agg['order_brs'],
                'order_klm'        => $agg['order_klm'],
                'order_dsp'        => $agg['order_dsp'],
                'total_order'      => $agg['count'],
                'total_mili'       => $agg['total_mili'],
                'omzet_bruto'      => $agg['total_bruto'],
                'omzet_reduksi'    => $agg['total_reduksi'],
                'omzet_netto'      => $agg['total_netto'],
                'omzet_ppn'        => $agg['total_ppn'],
                'omzet_total'      => $omzetTotal,
                'omzet_lunas'      => $lunas,
                'omzet_piutang'    => $belumLunas,
                'insentif_nominal' => $insentifTercatat,
                'komisi_nominal'   => $komisiTercatat,
                'total_hak_sales'  => $insentifTercatat + $komisiTercatat,
            ];
        }

        $col = collect($dataInsentif);
        $grandTotal = [
            'order_brs'        => $col->sum('order_brs'),
            'order_klm'        => $col->sum('order_klm'),
            'order_dsp'        => $col->sum('order_dsp'),
            'total_order'      => $col->sum('total_order'),
            'total_mili'       => $col->sum('total_mili'),
            'omzet_bruto'      => $col->sum('omzet_bruto'),
            'omzet_reduksi'    => $col->sum('omzet_reduksi'),
            'omzet_netto'      => $col->sum('omzet_netto'),
            'omzet_ppn'        => $col->sum('omzet_ppn'),
            'omzet_total'      => $col->sum('omzet_total'),
            'omzet_lunas'      => $col->sum('omzet_lunas'),
            'omzet_piutang'    => $col->sum('omzet_piutang'),
            'insentif_nominal' => $col->sum('insentif_nominal'),
            'komisi_nominal'   => $col->sum('komisi_nominal'),
            'total_hak_sales'  => $col->sum('total_hak_sales'),
        ];

        return array_merge($p, compact(
            'salesAes',
            'salesAeId',
            'media',
            'status',
            'dataInsentif',
            'grandTotal'
        ));
    }

    /**
     * Helper: Pengolahan data Klasifikasi Produk Iklan (Mendukung Periode Fleksibel + Basis Tanggal, Media & Status)
     */
    private function resolveKlasifikasiData(Request $request): array
    {
        $p = $this->resolveFlexiblePeriod($request, 'bulanan');
        $startDate = $p['startDate'];
        $endDate = $p['endDate'];

        $basisTanggal = $request->input('basis_tanggal', 'muat'); // 'muat', 'order'
        $media = $request->input('media', 'all'); // 'all', 'koran', 'online', 'tv'
        $status = $request->input('status', 'all'); // 'all', 'lunas', 'piutang', 'bonus'

        $rekapKlasifikasi = [];
        $totalSemua = 0;

        // 1. Klasifikasi Koran Cetak (BARIS, KOLOM, DISPLAY, ADVERTORIAL, BONUS)
        if (in_array($media, ['all', 'koran'])) {
            $kategoriList = ['BARIS', 'KOLOM', 'DISPLAY', 'ADVERTORIAL', 'BONUS'];
            foreach ($kategoriList as $kat) {
                $qKoran = TransaksiKoran::with('iklankoran')
                    ->where('kategori_form', $kat)
                    ->where('status_order', '!=', 'Batal');

                if ($basisTanggal === 'muat') {
                    $qKoran->whereBetween(DB::raw('COALESCE(tanggal_muatkoran, tanggal_transaksikoran)'), [
                        $startDate->toDateString(),
                        $endDate->toDateString(),
                    ]);
                } else {
                    $qKoran->whereBetween('tanggal_transaksikoran', [$startDate, $endDate]);
                }

                if ($status === 'lunas') {
                    $qKoran->where('piutang_transaksikoran', '<=', 0)->where('totaltagihan_transaksikoran', '>', 0);
                } elseif ($status === 'piutang') {
                    $qKoran->where('piutang_transaksikoran', '>', 0);
                } elseif ($status === 'bonus') {
                    $qKoran->where('totaltagihan_transaksikoran', '<=', 0);
                }

                $orders = $qKoran->get();
                $agg = $this->aggregateSalesGroup($kat, $kat, $kat, null, $orders);
                $nominal = (int) $orders->sum('totaltagihan_transaksikoran');
                $totalSemua += $nominal;

                $rekapKlasifikasi[$kat] = [
                    'nama'          => $kat,
                    'media_label'   => 'Koran Cetak',
                    'count'         => $orders->count(),
                    'vol_baris'     => $agg['vol_baris'],
                    'vol_kolom_mm'  => $agg['vol_kolom_mm'],
                    'vol_displ_mm'  => $agg['vol_displ_mm'],
                    'total_mili'    => $agg['total_mili'],
                    'brutto'        => $agg['total_bruto'],
                    'reduksi'       => $agg['total_reduksi'],
                    'netto'         => $agg['total_netto'],
                    'ppn'           => $agg['total_ppn'],
                    'materai'       => $agg['total_materai'],
                    'nominal'       => $nominal,
                    'bayar'         => $agg['total_bayar'],
                    'piutang'       => $agg['total_piutang'],
                ];
            }
        }

        // 2. Klasifikasi Media Online
        if (in_array($media, ['all', 'online'])) {
            $qOnline = TransaksiOnline::with('iklanonline');
            if ($basisTanggal === 'muat') {
                $qOnline->whereBetween(DB::raw('COALESCE(tanggal_muatiklanonline, tanggal_transaksionline)'), [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ]);
            } else {
                $qOnline->whereBetween('tanggal_transaksionline', [$startDate, $endDate]);
            }

            if ($status === 'lunas') {
                $qOnline->where('piutang_transaksionline', '<=', 0)->where('totaltagihan_transaksionline', '>', 0);
            } elseif ($status === 'piutang') {
                $qOnline->where('piutang_transaksionline', '>', 0);
            } elseif ($status === 'bonus') {
                $qOnline->where('totaltagihan_transaksionline', '<=', 0);
            }

            $ordersOnline = $qOnline->get();
            $aggOnl = $this->aggregateSalesGroup('ONLINE', 'MEDIA ONLINE', 'ONLINE', null, collect(), $ordersOnline, collect());
            $nominalOnl = (int) $ordersOnline->sum('totaltagihan_transaksionline');
            $totalSemua += $nominalOnl;

            $rekapKlasifikasi['MEDIA ONLINE'] = [
                'nama'          => 'MEDIA ONLINE',
                'media_label'   => 'Media Online',
                'count'         => $ordersOnline->count(),
                'vol_baris'     => 0,
                'vol_kolom_mm'  => 0,
                'vol_displ_mm'  => 0,
                'total_mili'    => 0,
                'brutto'        => $aggOnl['total_bruto'],
                'reduksi'       => $aggOnl['total_reduksi'],
                'netto'         => $aggOnl['total_netto'],
                'ppn'           => $aggOnl['total_ppn'],
                'materai'       => 0,
                'nominal'       => $nominalOnl,
                'bayar'         => $aggOnl['total_bayar'],
                'piutang'       => $aggOnl['total_piutang'],
            ];
        }

        // 3. Klasifikasi Priangan TV
        if (in_array($media, ['all', 'tv'])) {
            $qTv = TransaksiPriangan::with('iklanpriangan');
            if ($basisTanggal === 'muat') {
                $qTv->whereBetween(DB::raw('COALESCE(tanggal_muatiklanpriangan, tanggal_transaksipriangan)'), [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ]);
            } else {
                $qTv->whereBetween('tanggal_transaksipriangan', [$startDate, $endDate]);
            }

            if ($status === 'lunas') {
                $qTv->where('piutang_transaksipriangan', '<=', 0)->where('totaltagihan_transaksipriangan', '>', 0);
            } elseif ($status === 'piutang') {
                $qTv->where('piutang_transaksipriangan', '>', 0);
            } elseif ($status === 'bonus') {
                $qTv->where('totaltagihan_transaksipriangan', '<=', 0);
            }

            $ordersTv = $qTv->get();
            $aggTv = $this->aggregateSalesGroup('TV', 'PRIANGAN TV', 'TV', null, collect(), collect(), $ordersTv);
            $nominalTv = (int) $ordersTv->sum('totaltagihan_transaksipriangan');
            $totalSemua += $nominalTv;

            $rekapKlasifikasi['PRIANGAN TV'] = [
                'nama'          => 'PRIANGAN TV',
                'media_label'   => 'Priangan TV',
                'count'         => $ordersTv->count(),
                'vol_baris'     => 0,
                'vol_kolom_mm'  => 0,
                'vol_displ_mm'  => 0,
                'total_mili'    => 0,
                'brutto'        => $aggTv['total_bruto'],
                'reduksi'       => $aggTv['total_reduksi'],
                'netto'         => $aggTv['total_netto'],
                'ppn'           => $aggTv['total_ppn'],
                'materai'       => 0,
                'nominal'       => $nominalTv,
                'bayar'         => $aggTv['total_bayar'],
                'piutang'       => $aggTv['total_piutang'],
            ];
        }

        $col = collect($rekapKlasifikasi);
        $grandTotal = [
            'count'        => $col->sum('count'),
            'vol_baris'    => $col->sum('vol_baris'),
            'vol_kolom_mm' => $col->sum('vol_kolom_mm'),
            'vol_displ_mm' => $col->sum('vol_displ_mm'),
            'total_mili'   => $col->sum('total_mili'),
            'brutto'       => $col->sum('brutto'),
            'reduksi'      => $col->sum('reduksi'),
            'netto'        => $col->sum('netto'),
            'ppn'          => $col->sum('ppn'),
            'materai'      => $col->sum('materai'),
            'nominal'      => $totalSemua,
            'bayar'        => $col->sum('bayar'),
            'piutang'      => $col->sum('piutang'),
        ];

        return array_merge($p, compact(
            'basisTanggal',
            'media',
            'status',
            'rekapKlasifikasi',
            'totalSemua',
            'grandTotal'
        ));
    }

    /**
     * Helper: Pengolahan data Pembatalan Iklan (Mendukung Periode Fleksibel + Filter Sales, Kategori & Pencarian)
     */
    private function resolvePembatalanData(Request $request): array
    {
        $p = $this->resolveFlexiblePeriod($request, 'bulanan');
        $startDate = $p['startDate'];
        $endDate = $p['endDate'];

        $salesAeId = $request->input('sales_ae_id', 'all');
        $kategori = $request->input('kategori', 'all');
        $q = trim((string) $request->input('q', ''));

        $salesAes = SalesAe::where('is_active', true)->orderBy('kode_ae')->get();

        $query = TransaksiKoran::with(['iklankoran', 'pelanggan', 'salesAe', 'dibatalkanOleh'])
            ->where('status_order', 'Batal')
            ->whereBetween('tanggal_transaksikoran', [$startDate, $endDate]);

        if ($salesAeId !== 'all' && $salesAeId !== '') {
            $query->where('sales_ae_id', (int) $salesAeId);
        }

        if ($kategori !== 'all' && $kategori !== '') {
            $query->where('kategori_form', $kategori);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nofakturkoran', 'like', "%{$q}%")
                    ->orWhere('nama_pemasangkoran', 'like', "%{$q}%")
                    ->orWhere('alasan_batal', 'like', "%{$q}%");
            });
        }

        $pembatalanList = $query->latest('dibatalkan_at')->get();

        $totalBrutto = $pembatalanList->sum(fn($i) => $i->nilai_brutto);
        $totalReduksi = $pembatalanList->sum(fn($i) => $i->nilai_diskon);
        $totalNetto = $pembatalanList->sum(fn($i) => $i->nilai_netto);
        $totalPpn = $pembatalanList->sum(fn($i) => $i->nilai_ppn);
        $totalTagihan = $pembatalanList->sum('totaltagihan_transaksikoran');

        return array_merge($p, compact(
            'salesAes',
            'salesAeId',
            'kategori',
            'q',
            'pembatalanList',
            'totalBrutto',
            'totalReduksi',
            'totalNetto',
            'totalPpn',
            'totalTagihan'
        ));
    }

    /**
     * Helper: Pengolahan data Aging Piutang Iklan Lintas 3 Media
     */
    private function resolveAgingPiutangData(Request $request): array
    {
        $now = Carbon::now();
        $agingBuckets = [
            '0_30'  => ['label' => '0 - 30 Hari (Lancar)',          'items' => [], 'total_tagihan' => 0, 'total_bayar' => 0, 'total' => 0],
            '31_60' => ['label' => '31 - 60 Hari (Perhatian)',      'items' => [], 'total_tagihan' => 0, 'total_bayar' => 0, 'total' => 0],
            '61_90' => ['label' => '61 - 90 Hari (Diragukan)',      'items' => [], 'total_tagihan' => 0, 'total_bayar' => 0, 'total' => 0],
            'gt_90' => ['label' => '> 90 Hari (Macet / Somasi)',    'items' => [], 'total_tagihan' => 0, 'total_bayar' => 0, 'total' => 0],
        ];

        $flatItems = [];

        $pushToBucket = function (array $itemData) use (&$agingBuckets, &$flatItems) {
            $days = $itemData['umur_hari'];
            $sisa = $itemData['piutang'];
            $tagihan = $itemData['total_tagihan'];
            $bayar = $itemData['terbayar'];

            if ($days <= 30) {
                $key = '0_30';
            } elseif ($days <= 60) {
                $key = '31_60';
            } elseif ($days <= 90) {
                $key = '61_90';
            } else {
                $key = 'gt_90';
            }

            $itemData['bucket_label'] = $agingBuckets[$key]['label'];
            $agingBuckets[$key]['items'][] = $itemData;
            $agingBuckets[$key]['total_tagihan'] += $tagihan;
            $agingBuckets[$key]['total_bayar'] += $bayar;
            $agingBuckets[$key]['total'] += $sisa;
            $flatItems[] = $itemData;
        };

        // 1. Piutang Koran
        $piutangKoran = TransaksiKoran::with(['pelanggan', 'salesAe', 'iklankoran'])
            ->where('piutang_transaksikoran', '>', 0)
            ->where('status_order', '!=', 'Batal')
            ->get();

        foreach ($piutangKoran as $row) {
            $tgl = Carbon::parse($row->tanggal_transaksikoran);
            $days = (int) $tgl->diffInDays($now);
            $sisa = (int) $row->piutang_transaksikoran;
            $tagihan = (int) $row->totaltagihan_transaksikoran;
            $bayar = (int) ($row->jumlahbayar_transaksikoran ?? max(0, $tagihan - $sisa));

            $pushToBucket([
                'no_order'      => $row->id ? (2026100000 + $row->id) : $row->nofakturkoran,
                'faktur'        => $row->nofakturkoran,
                'no_perkiraan'  => $row->pelanggan?->kode_pelanggan ?: ('1.00.04.01.01.01.' . str_pad($row->pelanggan_id ?? $row->id, 5, '0', STR_PAD_LEFT)),
                'media'         => 'Koran',
                'jenis'         => strtoupper($row->iklankoran?->nama_jenis ?: ($row->kategori_form ?: 'IKLAN KORAN')),
                'tanggal'       => $tgl->format('d-m-Y'),
                'pelanggan'     => strtoupper($row->pelanggan?->nama_instansi ?: $row->nama_pemasangkoran),
                'alamat'        => strtoupper($row->alamat_pemasangkoran ?: ($row->pelanggan?->alamat ?: '-')),
                'sales'         => strtoupper($row->salesAe ? ($row->salesAe->kode_ae . ' ' . $row->salesAe->nama_ae) : ($row->sales_iklankoran ?? 'FO')),
                'total_tagihan' => $tagihan,
                'terbayar'      => $bayar,
                'piutang'       => $sisa,
                'umur_hari'     => $days,
            ]);
        }

        // 2. Piutang Online
        $piutangOnline = TransaksiOnline::with(['pelanggan', 'salesAe', 'iklanonline'])
            ->where('piutang_transaksionline', '>', 0)
            ->get();

        foreach ($piutangOnline as $row) {
            $tgl = Carbon::parse($row->tanggal_transaksionline);
            $days = (int) $tgl->diffInDays($now);
            $sisa = (int) $row->piutang_transaksionline;
            $tagihan = (int) $row->totaltagihan_transaksionline;
            $bayar = (int) ($row->jumlahbayar_transaksionline ?? max(0, $tagihan - $sisa));

            $pushToBucket([
                'no_order'      => $row->id ? (2026200000 + $row->id) : $row->nofakturonline,
                'faktur'        => $row->nofakturonline,
                'no_perkiraan'  => $row->pelanggan?->kode_pelanggan ?: ('1.00.04.01.01.01.' . str_pad($row->pelanggan_id ?? $row->id, 5, '0', STR_PAD_LEFT)),
                'media'         => 'Online',
                'jenis'         => strtoupper($row->iklanonline?->nama_jenis ?: 'MEDIA ONLINE'),
                'tanggal'       => $tgl->format('d-m-Y'),
                'pelanggan'     => strtoupper($row->pelanggan?->nama_instansi ?: $row->nama_pemasangonline),
                'alamat'        => strtoupper($row->alamat_pemasangonline ?: ($row->pelanggan?->alamat ?: '-')),
                'sales'         => strtoupper($row->salesAe ? ($row->salesAe->kode_ae . ' ' . $row->salesAe->nama_ae) : ($row->sales_iklanonline ?? 'FO')),
                'total_tagihan' => $tagihan,
                'terbayar'      => $bayar,
                'piutang'       => $sisa,
                'umur_hari'     => $days,
            ]);
        }

        // 3. Piutang Priangan TV
        $piutangTv = TransaksiPriangan::with(['pelanggan', 'salesAe', 'iklanpriangan'])
            ->where('piutang_transaksipriangan', '>', 0)
            ->get();

        foreach ($piutangTv as $row) {
            $tgl = Carbon::parse($row->tanggal_transaksipriangan);
            $days = (int) $tgl->diffInDays($now);
            $sisa = (int) $row->piutang_transaksipriangan;
            $tagihan = (int) $row->totaltagihan_transaksipriangan;
            $bayar = (int) ($row->jumlahbayar_transaksipriangan ?? max(0, $tagihan - $sisa));

            $pushToBucket([
                'no_order'      => $row->id ? (2026300000 + $row->id) : $row->nofakturpriangan,
                'faktur'        => $row->nofakturpriangan,
                'no_perkiraan'  => $row->pelanggan?->kode_pelanggan ?: ('1.00.04.01.01.01.' . str_pad($row->pelanggan_id ?? $row->id, 5, '0', STR_PAD_LEFT)),
                'media'         => 'Priangan TV',
                'jenis'         => strtoupper($row->iklanpriangan?->nama_jenis ?: 'PRIANGAN TV'),
                'tanggal'       => $tgl->format('d-m-Y'),
                'pelanggan'     => strtoupper($row->pelanggan?->nama_instansi ?: $row->nama_pemasangpriangan),
                'alamat'        => strtoupper($row->alamat_pemasangpriangan ?: ($row->pelanggan?->alamat ?: '-')),
                'sales'         => strtoupper($row->salesAe ? ($row->salesAe->kode_ae . ' ' . $row->salesAe->nama_ae) : ($row->sales_iklanpriangan ?? 'FO')),
                'total_tagihan' => $tagihan,
                'terbayar'      => $bayar,
                'piutang'       => $sisa,
                'umur_hari'     => $days,
            ]);
        }

        $totalTagihanKeseluruhan = array_sum(array_column($agingBuckets, 'total_tagihan'));
        $totalTerbayarKeseluruhan = array_sum(array_column($agingBuckets, 'total_bayar'));
        $totalPiutangKeseluruhan = array_sum(array_column($agingBuckets, 'total'));
        $hariTanggal = strtoupper($now->locale('id')->isoFormat('dddd')) . ' / ' . $now->format('d-m-Y');

        return compact(
            'agingBuckets',
            'flatItems',
            'totalTagihanKeseluruhan',
            'totalTerbayarKeseluruhan',
            'totalPiutangKeseluruhan',
            'hariTanggal'
        );
    }

    /**
     * Helper privat untuk mengolah filter, agregasi, dan penggabungan dataset transaksi
     * Sesuai Print #4 (Laporan Pemuatan Iklan HU. Priangan) + Standar Industri Multi-Media
     */
    private function resolveReportData(Request $request): array
    {
        $p = $this->resolveFlexiblePeriod($request, 'bulanan');
        $startDate = $p['startDate'];
        $endDate = $p['endDate'];

        $media = $request->input('media', 'all'); // 'all', 'koran', 'online', 'tv'
        $status = $request->input('status', 'all'); // 'all', 'lunas', 'piutang', 'bonus'
        $basisTanggal = $request->input('basis_tanggal', 'muat'); // 'muat' (Tanggal Muat/Terbit) atau 'order' (Tanggal Order)

        $items = new Collection();

        // 1. Ambil Transaksi Koran
        if (in_array($media, ['all', 'koran'])) {
            $qKoran = TransaksiKoran::with(['iklankoran', 'pelanggan', 'salesAe'])
                ->where('status_order', '!=', 'Batal');

            if ($basisTanggal === 'muat') {
                $qKoran->whereBetween(DB::raw('COALESCE(tanggal_muatkoran, tanggal_transaksikoran)'), [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ]);
            } else {
                $qKoran->whereBetween('tanggal_transaksikoran', [$startDate, $endDate]);
            }

            if ($status === 'lunas') {
                $qKoran->where('piutang_transaksikoran', '<=', 0)->where('totaltagihan_transaksikoran', '>', 0);
            } elseif ($status === 'piutang') {
                $qKoran->where('piutang_transaksikoran', '>', 0);
            } elseif ($status === 'bonus') {
                $qKoran->where('totaltagihan_transaksikoran', '<=', 0);
            }

            foreach ($qKoran->get() as $row) {
                $dim = $this->resolveKoranDimension($row);
                $totalTagihan = (int) $row->totaltagihan_transaksikoran;
                $piutang = (int) $row->piutang_transaksikoran;
                $paketStr = $row->iklankoran->nama_jenis ?? ($row->kategori_form ?? 'DISPLAY');
                $isBonus = ($totalTagihan <= 0 || str_contains(strtoupper($paketStr), 'BONUS'));

                $items->push((object)[
                    'id'            => $row->id,
                    'no_order'      => $row->id ? (2026100000 + $row->id) : $row->nofakturkoran,
                    'no_perkiraan'  => $row->pelanggan?->kode_pelanggan ?: ('1.00.04.01.01.01.' . str_pad($row->pelanggan_id ?? $row->id, 5, '0', STR_PAD_LEFT)),
                    'media_type'    => 'koran',
                    'media_code'    => 'KRN',
                    'media_name'    => 'Koran Cetak',
                    'faktur'        => $row->nofakturkoran,
                    'tanggal'       => $row->tanggal_transaksikoran,
                    'tanggal_muat'  => $row->tanggal_muatkoran ?? $row->tanggal_transaksikoran,
                    'customer'      => $row->pelanggan?->nama_instansi ?: $row->nama_pemasangkoran,
                    'alamat'        => $row->alamat_pemasangkoran ?: ($row->pelanggan?->alamat ?: '-'),
                    'paket'         => $paketStr,
                    'ad_type'       => $dim['ad_type'],
                    'baris'         => $dim['baris'],
                    'kolom'         => $dim['kolom'],
                    'displ'         => $dim['displ'],
                    'mili'          => $dim['mili'],
                    'jml_mm'        => $dim['jml_mm'],
                    'brutto'        => (int) $row->nilai_brutto,
                    'reduksi'       => (int) $row->nilai_diskon,
                    'netto'         => (int) $row->nilai_netto,
                    'ppn'           => (int) $row->nilai_ppn,
                    'materai'       => (int) $row->nilai_materai,
                    'total'         => $totalTagihan,
                    'bayar'         => (int) $row->jumlahbayar_transaksikoran,
                    'piutang'       => $piutang,
                    'is_lunas'      => $piutang <= 0,
                    'is_bonus'      => $isBonus,
                    'status_label'  => $isBonus ? 'BONUS' : ($piutang <= 0 ? 'LUNAS' : 'PIUTANG'),
                    'sales_kode'    => $row->salesAe?->kode_ae ?: 'FO',
                    'sales'         => $row->salesAe ? ($row->salesAe->kode_ae . ' ' . $row->salesAe->nama_ae) : ($row->sales_iklankoran ?? 'FO'),
                    'created_at'    => $row->created_at,
                ]);
            }
        }

        // 2. Ambil Transaksi Online
        if (in_array($media, ['all', 'online'])) {
            $qOnline = TransaksiOnline::with(['iklanonline', 'pelanggan', 'salesAe']);

            if ($basisTanggal === 'muat') {
                $qOnline->whereBetween(DB::raw('COALESCE(tanggal_muatiklanonline, tanggal_transaksionline)'), [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ]);
            } else {
                $qOnline->whereBetween('tanggal_transaksionline', [$startDate, $endDate]);
            }

            if ($status === 'lunas') {
                $qOnline->where('piutang_transaksionline', '<=', 0)->where('totaltagihan_transaksionline', '>', 0);
            } elseif ($status === 'piutang') {
                $qOnline->where('piutang_transaksionline', '>', 0);
            } elseif ($status === 'bonus') {
                $qOnline->where('totaltagihan_transaksionline', '<=', 0);
            }

            foreach ($qOnline->get() as $row) {
                $totalTagihan = (int) $row->totaltagihan_transaksionline;
                $piutang = (int) $row->piutang_transaksionline;
                $paketStr = is_array($row->kategori_produk_publikasi) ? implode(', ', $row->kategori_produk_publikasi) : ($row->iklanonline->nama_jenis ?? 'Publikasi Online');
                $isBonus = ($totalTagihan <= 0 || str_contains(strtoupper($paketStr), 'BONUS'));

                $items->push((object)[
                    'id'            => $row->id,
                    'no_order'      => $row->id ? (2026200000 + $row->id) : $row->nofakturonline,
                    'no_perkiraan'  => $row->pelanggan?->kode_pelanggan ?: ('1.00.04.01.01.01.' . str_pad($row->pelanggan_id ?? $row->id, 5, '0', STR_PAD_LEFT)),
                    'media_type'    => 'online',
                    'media_code'    => 'ONL',
                    'media_name'    => 'Media Online',
                    'faktur'        => $row->nofakturonline,
                    'tanggal'       => $row->tanggal_transaksionline,
                    'tanggal_muat'  => $row->tanggal_muatiklanonline ?? $row->tanggal_transaksionline,
                    'customer'      => $row->pelanggan?->nama_instansi ?: $row->nama_pemasangonline,
                    'alamat'        => $row->alamat_pemasangonline ?: ($row->pelanggan?->alamat ?: '-'),
                    'paket'         => $paketStr,
                    'ad_type'       => 'DISPL',
                    'baris'         => 0,
                    'kolom'         => 0,
                    'displ'         => (int) ($row->total_muatiklanonline ?? 1),
                    'mili'          => 0,
                    'jml_mm'        => 0,
                    'brutto'        => (int) $row->nilai_brutto,
                    'reduksi'       => (int) $row->nilai_diskon,
                    'netto'         => (int) $row->nilai_netto,
                    'ppn'           => (int) $row->nilai_ppn,
                    'materai'       => 0,
                    'total'         => $totalTagihan,
                    'bayar'         => (int) $row->jumlahbayar_transaksionline,
                    'piutang'       => $piutang,
                    'is_lunas'      => $piutang <= 0,
                    'is_bonus'      => $isBonus,
                    'status_label'  => $isBonus ? 'BONUS' : ($piutang <= 0 ? 'LUNAS' : 'PIUTANG'),
                    'sales_kode'    => $row->salesAe?->kode_ae ?: 'FO',
                    'sales'         => $row->salesAe ? ($row->salesAe->kode_ae . ' ' . $row->salesAe->nama_ae) : ($row->sales_iklanonline ?? 'FO'),
                    'created_at'    => $row->created_at,
                ]);
            }
        }

        // 3. Ambil Transaksi TV
        if (in_array($media, ['all', 'tv'])) {
            $qTv = TransaksiPriangan::with(['iklanpriangan', 'pelanggan', 'salesAe']);

            if ($basisTanggal === 'muat') {
                $qTv->whereBetween(DB::raw('COALESCE(tanggal_muatiklanpriangan, tanggal_transaksipriangan)'), [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ]);
            } else {
                $qTv->whereBetween('tanggal_transaksipriangan', [$startDate, $endDate]);
            }

            if ($status === 'lunas') {
                $qTv->where('piutang_transaksipriangan', '<=', 0)->where('totaltagihan_transaksipriangan', '>', 0);
            } elseif ($status === 'piutang') {
                $qTv->where('piutang_transaksipriangan', '>', 0);
            } elseif ($status === 'bonus') {
                $qTv->where('totaltagihan_transaksipriangan', '<=', 0);
            }

            foreach ($qTv->get() as $row) {
                $totalTagihan = (int) $row->totaltagihan_transaksipriangan;
                $piutang = (int) $row->piutang_transaksipriangan;
                $paketStr = $row->iklanpriangan->nama_jenis ?? 'Broadcast TV';
                $isBonus = ($totalTagihan <= 0 || str_contains(strtoupper($paketStr), 'BONUS'));

                $items->push((object)[
                    'id'            => $row->id,
                    'no_order'      => $row->id ? (2026300000 + $row->id) : $row->nofakturpriangan,
                    'no_perkiraan'  => $row->pelanggan?->kode_pelanggan ?: ('1.00.04.01.01.01.' . str_pad($row->pelanggan_id ?? $row->id, 5, '0', STR_PAD_LEFT)),
                    'media_type'    => 'tv',
                    'media_code'    => 'PTV',
                    'media_name'    => 'Priangan TV',
                    'faktur'        => $row->nofakturpriangan,
                    'tanggal'       => $row->tanggal_transaksipriangan,
                    'tanggal_muat'  => $row->tanggal_muatiklanpriangan ?? $row->tanggal_transaksipriangan,
                    'customer'      => $row->pelanggan?->nama_instansi ?: $row->nama_pemasangpriangan,
                    'alamat'        => $row->alamat_pemasangpriangan ?: ($row->pelanggan?->alamat ?: '-'),
                    'paket'         => $paketStr,
                    'ad_type'       => 'DISPL',
                    'baris'         => 0,
                    'kolom'         => 0,
                    'displ'         => (int) ($row->total_muatiklanpriangan ?? 1),
                    'mili'          => 0,
                    'jml_mm'        => 0,
                    'brutto'        => (int) ($row->nilai_brutto ?? $row->harga_transaksipriangan ?? 0),
                    'reduksi'       => (int) ($row->nilai_diskon ?? $row->diskon_transaksipriangan ?? 0),
                    'netto'         => (int) ($row->nilai_netto ?? (($row->harga_transaksipriangan ?? 0) - ($row->diskon_transaksipriangan ?? 0))),
                    'ppn'           => (int) ($row->nilai_ppn ?? $row->ppn_transaksipriangan ?? 0),
                    'materai'       => 0,
                    'total'         => $totalTagihan,
                    'bayar'         => (int) $row->jumlahbayar_transaksipriangan,
                    'piutang'       => $piutang,
                    'is_lunas'      => $piutang <= 0,
                    'is_bonus'      => $isBonus,
                    'status_label'  => $isBonus ? 'BONUS' : ($piutang <= 0 ? 'LUNAS' : 'PIUTANG'),
                    'sales_kode'    => $row->salesAe?->kode_ae ?: 'FO',
                    'sales'         => $row->salesAe ? ($row->salesAe->kode_ae . ' ' . $row->salesAe->nama_ae) : ($row->sales_iklanpriangan ?? 'FO'),
                    'created_at'    => $row->created_at,
                ]);
            }
        }

        // Urutkan data berdasarkan tanggal & ID
        $sortedItems = $items->sortByDesc(fn($i) => $basisTanggal === 'muat' ? $i->tanggal_muat : $i->tanggal)->values();

        // Agregasi Dimensi & Finansial Lengkap (Sesuai Print #4 + Metrik Standar Industri)
        $totalTransaksi = $sortedItems->count();
        $totalBaris     = $sortedItems->sum('baris');
        $totalKolom     = $sortedItems->sum('kolom');
        $totalDispl     = $sortedItems->sum('displ');
        $totalMili      = $sortedItems->sum('mili');
        $totalJmlMm     = $sortedItems->sum('jml_mm');
        $orderBaris     = $sortedItems->where('ad_type', 'BARIS')->count();
        $orderKolom     = $sortedItems->where('ad_type', 'KOLOM')->count();
        $orderDispl     = $sortedItems->where('ad_type', 'DISPL')->count();

        $orderBonus     = $sortedItems->where('is_bonus', true)->count();
        $orderKomersial = $totalTransaksi - $orderBonus;
        $miliBonus      = $sortedItems->where('is_bonus', true)->sum('mili');
        $miliKomersial  = $totalMili - $miliBonus;

        $totalBrutto    = $sortedItems->sum('brutto');
        $totalReduksi   = $sortedItems->sum('reduksi');
        $totalNetto     = $sortedItems->sum('netto');
        $totalPpn       = $sortedItems->sum('ppn');
        $totalMaterai   = $sortedItems->sum('materai');
        $totalOmset     = $sortedItems->sum('total');
        $totalKasMasuk  = $sortedItems->sum('bayar');
        $totalPiutang   = $sortedItems->sum('piutang');
        $countLunas     = $sortedItems->where('is_lunas', true)->where('is_bonus', false)->count();
        $countPiutang   = $sortedItems->where('is_lunas', false)->count();
        $rasioLunas     = $totalOmset > 0 ? round(($totalKasMasuk / $totalOmset) * 100, 1) : 0.0;

        // Breakdown per Media
        $breakdown = [
            'koran'  => [
                'nama'   => 'Koran Cetak',
                'kode'   => 'KRN',
                'count'  => $sortedItems->where('media_type', 'koran')->count(),
                'total'  => $sortedItems->where('media_type', 'koran')->sum('total'),
                'bayar'  => $sortedItems->where('media_type', 'koran')->sum('bayar'),
                'piutang'=> $sortedItems->where('media_type', 'koran')->sum('piutang'),
            ],
            'online' => [
                'nama'   => 'Media Online',
                'kode'   => 'ONL',
                'count'  => $sortedItems->where('media_type', 'online')->count(),
                'total'  => $sortedItems->where('media_type', 'online')->sum('total'),
                'bayar'  => $sortedItems->where('media_type', 'online')->sum('bayar'),
                'piutang'=> $sortedItems->where('media_type', 'online')->sum('piutang'),
            ],
            'tv'     => [
                'nama'   => 'Priangan TV',
                'kode'   => 'PTV',
                'count'  => $sortedItems->where('media_type', 'tv')->count(),
                'total'  => $sortedItems->where('media_type', 'tv')->sum('total'),
                'bayar'  => $sortedItems->where('media_type', 'tv')->sum('bayar'),
                'piutang'=> $sortedItems->where('media_type', 'tv')->sum('piutang'),
            ],
        ];

        return array_merge($p, [
            'items'          => $sortedItems,
            'totalTransaksi' => $totalTransaksi,
            'totalBaris'     => $totalBaris,
            'totalKolom'     => $totalKolom,
            'totalDispl'     => $totalDispl,
            'totalMili'      => $totalMili,
            'totalJmlMm'     => $totalJmlMm,
            'orderBaris'     => $orderBaris,
            'orderKolom'     => $orderKolom,
            'orderDispl'     => $orderDispl,
            'orderBonus'     => $orderBonus,
            'orderKomersial' => $orderKomersial,
            'miliBonus'      => $miliBonus,
            'miliKomersial'  => $miliKomersial,
            'totalBrutto'    => $totalBrutto,
            'totalReduksi'   => $totalReduksi,
            'totalNetto'     => $totalNetto,
            'totalPpn'       => $totalPpn,
            'totalMaterai'   => $totalMaterai,
            'totalOmset'     => $totalOmset,
            'totalKasMasuk'  => $totalKasMasuk,
            'totalPiutang'   => $totalPiutang,
            'countLunas'     => $countLunas,
            'countPiutang'   => $countPiutang,
            'rasioLunas'     => $rasioLunas,
            'breakdown'      => $breakdown,
            'media'          => $media,
            'status'         => $status,
            'basisTanggal'   => $basisTanggal,
        ]);
    }
}
