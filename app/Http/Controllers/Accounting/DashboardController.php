<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Agen;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumDetail;
use App\Models\KoreksiPiutang;
use App\Models\LogHitungAkuntansi;
use App\Models\MemoUnlock;
use App\Models\PeriodeAkuntansi;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use App\Services\AccountingService;
use App\Services\KalenderNasionalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct(protected AccountingService $accountingService)
    {
    }

    /**
     * Dashboard Eksekutif Accounting.
     * Clean, modern, palet 60-30-10, terhubung penuh ke DB lintas divisi & GL.
     */
    public function index(Request $request)
    {
        $range = $request->input('range', 'month');
        $now = Carbon::now();
        $todayStr = $now->toDateString();

        switch ($range) {
            case 'today':
                $startDate = $todayStr;
                $endDate = $todayStr;
                $rangeTitle = 'Hari Ini';
                $rangeSub = $now->translatedFormat('d M Y');
                $rangeLabel = 'Hari Ini (' . $now->translatedFormat('d M Y') . ')';
                break;
            case '7days':
            case 'week':
                $range = '7days';
                $startDate = $now->copy()->subDays(6)->toDateString();
                $endDate = $todayStr;
                $rangeTitle = '7 Hari Terakhir';
                $rangeSub = Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . $now->translatedFormat('d M Y');
                $rangeLabel = '7 Hari Terakhir (' . $rangeSub . ')';
                break;
            case '30days':
                $startDate = $now->copy()->subDays(29)->toDateString();
                $endDate = $todayStr;
                $rangeTitle = '30 Hari Terakhir';
                $rangeSub = Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . $now->translatedFormat('d M Y');
                $rangeLabel = '30 Hari Terakhir (' . $rangeSub . ')';
                break;
            case 'last_month':
                $subMonth = $now->copy()->subMonth();
                $startDate = $subMonth->copy()->startOfMonth()->toDateString();
                $endDate = $subMonth->copy()->endOfMonth()->toDateString();
                $rangeTitle = 'Bulan Lalu';
                $rangeSub = $subMonth->translatedFormat('F Y');
                $rangeLabel = 'Bulan Lalu (' . $rangeSub . ')';
                break;
            case 'year':
                $startDate = $now->copy()->startOfYear()->toDateString();
                $endDate = $now->copy()->endOfYear()->toDateString();
                $rangeTitle = 'Tahun Ini';
                $rangeSub = $now->translatedFormat('Y');
                $rangeLabel = 'Tahun Ini (' . $now->translatedFormat('Y') . ')';
                break;
            case 'custom':
                $startDate = $request->input('dari', $now->copy()->startOfMonth()->toDateString());
                $endDate = $request->input('sampai', $todayStr);
                $rangeTitle = 'Pilih Tanggal';
                $rangeSub = Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . Carbon::parse($endDate)->translatedFormat('d M Y');
                $rangeLabel = 'Pilih Tanggal (' . $rangeSub . ')';
                break;
            case 'month':
            default:
                $startDate = $now->copy()->startOfMonth()->toDateString();
                $endDate = $now->copy()->endOfMonth()->toDateString();
                $rangeTitle = 'Bulan Ini';
                $rangeSub = $now->translatedFormat('F Y');
                $rangeLabel = 'Bulan Ini (' . $now->translatedFormat('F Y') . ')';
                $range = 'month';
                break;
        }

        $dari = $startDate;
        $sampai = $endDate;

        // 2. Pendapatan & Penerimaan Lintas Divisi (Iklan Koran, Online, TV, dan Sirkulasi)
        $koranQuery = TransaksiKoran::whereBetween('tanggal_transaksikoran', [$startDate, $endDate])
            ->where('status_order', '!=', 'Batal');
        $onlineQuery = TransaksiOnline::whereBetween('tanggal_transaksionline', [$startDate, $endDate])
            ->where(fn ($q) => $q->whereNull('status_produksi')->orWhere('status_produksi', '!=', 'batal'));
        $tvQuery = TransaksiPriangan::whereBetween('tanggal_transaksipriangan', [$startDate, $endDate])
            ->where(fn ($q) => $q->whereNull('status_tayang')->orWhere('status_tayang', '!=', 'batal'));

        $omsetKoran = (float) (clone $koranQuery)->sum('totaltagihan_transaksikoran');
        $omsetOnline = (float) (clone $onlineQuery)->sum('totaltagihan_transaksionline');
        $omsetTv = (float) (clone $tvQuery)->sum('totaltagihan_transaksipriangan');

        // Pendapatan Sirkulasi: prioritaskan GL jika sudah diposting dalam rentang periode, atau kalkulasi aktual tarif SistemConfig
        $glSirkulasiRange = (float) JurnalUmumDetail::whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'posted')->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->where('kode_perkiraan', '4.00.04.01.01.00.00001')
            ->sum('kredit');

        $sirkCalcRange = $this->accountingService->hitungOmsetSirkulasiRange($startDate, $endDate);
        $omsetSirkulasi = $glSirkulasiRange > 0 ? $glSirkulasiRange : (float) $sirkCalcRange['total_nominal'];

        $omsetUsaha = $omsetKoran + $omsetOnline + $omsetTv + $omsetSirkulasi;

        // Kas Masuk Riil dari KasMasuk aktif (tidak void) pada rentang periode
        $kasMasukRangeQuery = $this->accountingService->kasMasukAktif()
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tgl_transaksi', [$startDate, $endDate])
                  ->orWhereBetween('tanggal_terima', [$startDate, $endDate]);
            });

        $penerimaanKas = (float) (clone $kasMasukRangeQuery)->sum('jumlah_dibayar');
        if ($penerimaanKas <= 0) {
            $penerimaanKas = (float) (clone $koranQuery)->sum('jumlahbayar_transaksikoran')
                + (float) (clone $onlineQuery)->sum('jumlahbayar_transaksionline')
                + (float) (clone $tvQuery)->sum('jumlahbayar_transaksipriangan');
        }

        $bayarKoran = (float) (clone $koranQuery)->sum('jumlahbayar_transaksikoran');
        $bayarOnline = (float) (clone $onlineQuery)->sum('jumlahbayar_transaksionline');
        $bayarTv = (float) (clone $tvQuery)->sum('jumlahbayar_transaksipriangan');
        $bayarSirkulasi = (float) (clone $kasMasukRangeQuery)
            ->where(function ($q) {
                $q->where('kode_bagian', '02')->orWhere('tipe_transaksi', 'sirkulasi');
            })
            ->sum('jumlah_dibayar');

        // Piutang Kumulatif Aktif (Iklan + Sirkulasi)
        $piutangKoran = (float) TransaksiKoran::where('status_order', '!=', 'Batal')->sum('piutang_transaksikoran');
        $piutangOnline = (float) TransaksiOnline::where(fn ($q) => $q->whereNull('status_produksi')->orWhere('status_produksi', '!=', 'batal'))
            ->sum('piutang_transaksionline');
        $piutangTv = (float) TransaksiPriangan::where(fn ($q) => $q->whereNull('status_tayang')->orWhere('status_tayang', '!=', 'batal'))
            ->sum('piutang_transaksipriangan');
        $piutangSirkulasi = (float) Agen::sum('saldo_piutang');
        $piutangUsaha = $piutangKoran + $piutangOnline + $piutangTv + $piutangSirkulasi;

        $fakturBelumLunasCount = TransaksiKoran::where('status_order', '!=', 'Batal')->where('piutang_transaksikoran', '>', 0)->count()
            + TransaksiOnline::where(fn ($q) => $q->whereNull('status_produksi')->orWhere('status_produksi', '!=', 'batal'))
                ->where('piutang_transaksionline', '>', 0)->count()
            + TransaksiPriangan::where(fn ($q) => $q->whereNull('status_tayang')->orWhere('status_tayang', '!=', 'batal'))
                ->where('piutang_transaksipriangan', '>', 0)->count()
            + Agen::where('saldo_piutang', '>', 0)->count();

        // Retur & Beban Riil dari Database (mengecualikan BKK void & setor kas ke bank)
        $totalRetur = (float) KoreksiPiutang::whereBetween('tanggal_koreksi', [$startDate, $endDate])->sum('jumlah_koreksi');
        $pendapatanBersih = max(0, $omsetUsaha - $totalRetur);

        $bebanKasKeluar = (float) $this->accountingService->kasKeluarBebanAktif()
            ->whereBetween('tanggal_keluar', [$startDate, $endDate])
            ->sum('nominal_keluar');

        $glBebanPosted = (float) JurnalUmumDetail::whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'posted')->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->where(function ($q) {
                $q->where('kode_perkiraan', 'like', '5.%')->orWhere('kode_perkiraan', 'like', '6.%');
            })
            ->sum('debet');

        $totalBebanRiil = max($bebanKasKeluar, $glBebanPosted);
        $hppKasKeluar = (float) $this->accountingService->kasKeluarBebanAktif()
            ->whereBetween('tanggal_keluar', [$startDate, $endDate])
            ->where(function ($q) {
                $q->where('kode_bagian', '02')
                  ->orWhere('kategori', 'like', '%Produksi%')
                  ->orWhere('kode_perkiraan', 'like', '5.%');
            })
            ->sum('nominal_keluar');
        $labaKotor = $pendapatanBersih - $hppKasKeluar;
        $estimasiLabaBersih = $pendapatanBersih - $totalBebanRiil;

        // 3. Komposisi 4 Saluran Pendapatan (Koran, Online, TV, Sirkulasi)
        $channels = [
            'koran' => [
                'nama'     => 'Iklan Koran',
                'omzet'    => $omsetKoran,
                'bayar'    => $bayarKoran,
                'piutang'  => $piutangKoran,
                'percent'  => $omsetUsaha > 0 ? round(($omsetKoran / $omsetUsaha) * 100, 1) : 0,
                'count'    => (clone $koranQuery)->count() . ' Faktur',
                'route_go' => route('accounting.iklan.hitung-penjualan'),
            ],
            'online' => [
                'nama'     => 'Iklan Online',
                'omzet'    => $omsetOnline,
                'bayar'    => $bayarOnline,
                'piutang'  => $piutangOnline,
                'percent'  => $omsetUsaha > 0 ? round(($omsetOnline / $omsetUsaha) * 100, 1) : 0,
                'count'    => (clone $onlineQuery)->count() . ' Faktur',
                'route_go' => route('accounting.iklan.hitung-penjualan'),
            ],
            'tv' => [
                'nama'     => 'Priangan TV',
                'omzet'    => $omsetTv,
                'bayar'    => $bayarTv,
                'piutang'  => $piutangTv,
                'percent'  => $omsetUsaha > 0 ? round(($omsetTv / $omsetUsaha) * 100, 1) : 0,
                'count'    => (clone $tvQuery)->count() . ' Faktur',
                'route_go' => route('accounting.iklan.hitung-penjualan'),
            ],
            'sirkulasi' => [
                'nama'     => 'Sirkulasi Koran',
                'omzet'    => $omsetSirkulasi,
                'bayar'    => $bayarSirkulasi,
                'piutang'  => $piutangSirkulasi,
                'percent'  => $omsetUsaha > 0 ? round(($omsetSirkulasi / $omsetUsaha) * 100, 1) : 0,
                'count'    => $sirkCalcRange['daftar_agen']->count() . ' Agen',
                'route_go' => route('accounting.sirkulasi.hitung-penjualan'),
            ],
        ];

        // 4. Status Operasional & Kontrol Akuntansi
        $today = $todayStr;
        $isIklanDihitung = LogHitungAkuntansi::sudahDihitung('penjualan_iklan', $today);
        $isBayarDihitung = LogHitungAkuntansi::sudahDihitung('pembayaran_iklan', $today);
        $statusHitungIklanHariIni = $isIklanDihitung;
        $statusHitungSirkulasiBulanIni = LogHitungAkuntansi::where('jenis', 'penjualan_sirkulasi')
            ->where('bulan_periode', (int) Carbon::now()->format('m'))
            ->where('tahun_periode', (int) Carbon::now()->format('Y'))
            ->where('status', 'sukses')
            ->exists();

        $currentPeriodKey = Carbon::now()->format('Y-m');
        $periodeAktif = PeriodeAkuntansi::where('periode_key', $currentPeriodKey)->first();

        $bebanBelumPostingCount = $this->accountingService->kasKeluarBebanAktif()
            ->where('is_pulled_accounting', false)
            ->count();
        $bebanBelumPostingNominal = (float) $this->accountingService->kasKeluarBebanAktif()
            ->where('is_pulled_accounting', false)
            ->sum('nominal_keluar');
        $jurnalPeriodeCount = JurnalUmum::whereBetween('tanggal', [$startDate, $endDate])->count();
        $jurnalPeriodeNominal = (float) JurnalUmum::whereBetween('tanggal', [$startDate, $endDate])->sum('total_debet');
        $memoPendingCount = MemoUnlock::where('status_akuntansi', 'PENDING')->count();

        // 5. Data Grafik Tren Arus Keuangan 7 Hari Terakhir (Bar Chart Chart.js)
        $dailyLabels = [];
        $dailyIklan = [];
        $dailySirkulasi = [];
        $dailyKasMasuk = [];
        $dailyBeban = [];
        $sirkPricing = $this->accountingService->getSirkulasiPricing();
        $agenAktifList = $sirkCalcRange['daftar_agen'];

        for ($i = 6; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i)->toDateString();
            $dailyLabels[] = Carbon::parse($d)->translatedFormat('d M');

            $iklanDay = (float) TransaksiKoran::whereDate('tanggal_transaksikoran', $d)->where('status_order', '!=', 'Batal')->sum('totaltagihan_transaksikoran')
                + (float) TransaksiOnline::whereDate('tanggal_transaksionline', $d)
                    ->where(fn ($q) => $q->whereNull('status_produksi')->orWhere('status_produksi', '!=', 'batal'))
                    ->sum('totaltagihan_transaksionline')
                + (float) TransaksiPriangan::whereDate('tanggal_transaksipriangan', $d)
                    ->where(fn ($q) => $q->whereNull('status_tayang')->orWhere('status_tayang', '!=', 'batal'))
                    ->sum('totaltagihan_transaksipriangan');

            $savedMapDay = Cache::get('jatah_harian_' . $d, []);
            $sirkDay = 0.0;
            foreach ($agenAktifList as $ag) {
                $sirkDay += $this->accountingService->hitungNilaiJatahAgenHarian($ag, $savedMapDay[$ag->id] ?? null, $sirkPricing)['nominal'];
            }

            $kasInDay = (float) $this->accountingService->kasMasukAktif()
                ->where(function ($q) use ($d) {
                    $q->whereDate('tgl_transaksi', $d)->orWhereDate('tanggal_terima', $d);
                })
                ->sum('jumlah_dibayar');

            $bebanDay = (float) $this->accountingService->kasKeluarBebanAktif()
                ->whereDate('tanggal_keluar', $d)
                ->sum('nominal_keluar');

            $dailyIklan[] = (int) round($iklanDay);
            $dailySirkulasi[] = (int) round($sirkDay);
            $dailyKasMasuk[] = (int) round($kasInDay);
            $dailyBeban[] = (int) round($bebanDay);
        }

        // 6. Data Grafik Bar Beban per Kategori / Bagian (Real DB)
        $bebanKategoriRows = $this->accountingService->kasKeluarBebanAktif()
            ->whereBetween('tanggal_keluar', [$startDate, $endDate])
            ->selectRaw("COALESCE(NULLIF(kategori, ''), NULLIF(nama_perkiraan, ''), 'Operasional Umum') as label_kategori, SUM(nominal_keluar) as total")
            ->groupBy('label_kategori')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $bebanChartLabels = $bebanKategoriRows->pluck('label_kategori')->values()->all();
        $bebanChartValues = $bebanKategoriRows->pluck('total')->map(fn ($v) => (int) round((float) $v))->values()->all();

        if (empty($bebanChartLabels)) {
            $glBebanRows = JurnalUmumDetail::whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                    $q->where('status', 'posted')->whereBetween('tanggal', [$startDate, $endDate]);
                })
                ->where(function ($q) {
                    $q->where('kode_perkiraan', 'like', '5.%')->orWhere('kode_perkiraan', 'like', '6.%');
                })
                ->selectRaw('nama_perkiraan as label_kategori, SUM(debet) as total')
                ->groupBy('nama_perkiraan')
                ->orderByDesc('total')
                ->limit(6)
                ->get();

            $bebanChartLabels = $glBebanRows->pluck('label_kategori')->values()->all();
            $bebanChartValues = $glBebanRows->pluck('total')->map(fn ($v) => (int) round((float) $v))->values()->all();
        }

        // 7. Kalender Operasional & Hari Penting
        $kalenderService = app(KalenderNasionalService::class);
        $kalenderAwal = $kalenderService->getKalenderBulan((int) date('Y'), (int) date('n'));

        // 8. 10 Jurnal Umum Terbaru
        $jurnalTerbaru = JurnalUmum::with('details')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->take(10)
            ->get();
        $jurnalTerakhir = $jurnalTerbaru;

        // 9. Posisi Likuiditas Kas & Saldo Bank (100% Real DB/GL berdasarkan Mapping COA Dinamis)
        $kodeKasir = $this->accountingService->getMappedAccount('kas_operasional')['kode'];
        $kodeBca = $this->accountingService->getMappedAccount('bank_bca')['kode'];
        $kodeBjb = $this->accountingService->getMappedAccount('bank_bjb')['kode'];

        $saldoKasirDb = (float) JurnalUmumDetail::whereHas('jurnal', fn ($q) => $q->where('status', 'posted'))
            ->where('kode_perkiraan', $kodeKasir)
            ->selectRaw('COALESCE(SUM(debet - kredit), 0) as saldo')
            ->value('saldo');

        $kasMasukTunaiTotal = (float) $this->accountingService->kasMasukAktif()
            ->where(fn ($q) => $q->where('metode_bayar', 'tunai')->orWhereNull('metode_bayar'))
            ->sum('jumlah_dibayar');
        $kasKeluarTotal = (float) $this->accountingService->kasKeluarBebanAktif()->sum('nominal_keluar');
        $saldoKasir = $saldoKasirDb != 0.0 ? $saldoKasirDb : max(0.0, $kasMasukTunaiTotal - $kasKeluarTotal);

        $saldoBcaDb = (float) JurnalUmumDetail::whereHas('jurnal', fn ($q) => $q->where('status', 'posted'))
            ->where('kode_perkiraan', $kodeBca)
            ->selectRaw('COALESCE(SUM(debet - kredit), 0) as saldo')
            ->value('saldo');
        $saldoBca = max(0.0, $saldoBcaDb);

        $saldoBjbDb = (float) JurnalUmumDetail::whereHas('jurnal', fn ($q) => $q->where('status', 'posted'))
            ->where('kode_perkiraan', $kodeBjb)
            ->selectRaw('COALESCE(SUM(debet - kredit), 0) as saldo')
            ->value('saldo');
        $saldoBjb = max(0.0, $saldoBjbDb);

        $totalDanaLikuid = max(0.0, $saldoKasir + $saldoBca + $saldoBjb);
        $unpostedSummary = $this->accountingService->getUnpostedSubledgerSummary($now->format('Y-m'));

        // 10. Matriks Rasio Keuangan Eksekutif (Real DB)
        $dsoDays = match ($range) {
            'today' => 1,
            '7days' => 7,
            default => 30,
        };
        $dso = $omsetUsaha > 0 ? min(90, max(1, (int) round(($piutangUsaha / $omsetUsaha) * $dsoDays))) : 0;

        $totalAsetLancar = $totalDanaLikuid + $piutangUsaha;
        $totalKewajiban = max(0.0, (float) JurnalUmumDetail::whereHas('jurnal', fn ($q) => $q->where('status', 'posted'))
            ->where('kode_perkiraan', 'like', '2.%')
            ->selectRaw('COALESCE(SUM(kredit - debet), 0) as saldo')
            ->value('saldo'));

        $currentRatio = $totalKewajiban > 0
            ? number_format($totalAsetLancar / $totalKewajiban, 2) . 'x'
            : ($totalAsetLancar > 0 ? '∞' : '0.00x');
        $cashRatio = $totalBebanRiil > 0 ? number_format($totalDanaLikuid / $totalBebanRiil, 2) . 'x' : '0.00x';
        $oer = $omsetUsaha > 0 ? round(($totalBebanRiil / $omsetUsaha) * 100, 1) : 0.0;

        return view('pages.accounting.dashboard', compact(
            'range',
            'rangeLabel',
            'rangeTitle',
            'rangeSub',
            'dari',
            'sampai',
            'omsetUsaha',
            'penerimaanKas',
            'piutangUsaha',
            'fakturBelumLunasCount',
            'totalBebanRiil',
            'totalRetur',
            'labaKotor',
            'estimasiLabaBersih',
            'channels',
            'statusHitungIklanHariIni',
            'statusHitungSirkulasiBulanIni',
            'isIklanDihitung',
            'isBayarDihitung',
            'periodeAktif',
            'bebanBelumPostingCount',
            'bebanBelumPostingNominal',
            'jurnalPeriodeCount',
            'jurnalPeriodeNominal',
            'memoPendingCount',
            'dailyLabels',
            'dailyIklan',
            'dailySirkulasi',
            'dailyKasMasuk',
            'dailyBeban',
            'bebanChartLabels',
            'bebanChartValues',
            'kalenderAwal',
            'jurnalTerbaru',
            'jurnalTerakhir',
            'today',
            'saldoKasir',
            'saldoBca',
            'saldoBjb',
            'totalDanaLikuid',
            'currentRatio',
            'dso',
            'cashRatio',
            'oer',
            'unpostedSummary'
        ));
    }
}
