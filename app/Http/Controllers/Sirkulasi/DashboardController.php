<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Models\KasMasuk;
use App\Services\KalenderNasionalService;
use App\Services\SirkulasiDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    /**
     * Dashboard Operasional & Finansial Divisi Sirkulasi (Filter Periode Lengkap).
     */
    public function index(Request $request)
    {
        $range = $request->input('range', 'month');
        $now = Carbon::now();
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $todayStr = $today->toDateString();

        switch ($range) {
            case 'today':
                $startDate = $todayStr;
                $endDate = $todayStr;
                $rangeTitle = 'Hari Ini';
                $rangeSub = $now->translatedFormat('d M Y');
                break;
            case '7days':
            case 'week':
                $range = '7days';
                $startDate = $now->copy()->subDays(6)->toDateString();
                $endDate = $todayStr;
                $rangeTitle = '7 Hari Terakhir';
                $rangeSub = Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . $now->translatedFormat('d M Y');
                break;
            case '30days':
                $startDate = $now->copy()->subDays(29)->toDateString();
                $endDate = $todayStr;
                $rangeTitle = '30 Hari Terakhir';
                $rangeSub = Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . $now->translatedFormat('d M Y');
                break;
            case 'last_month':
                $subMonth = $now->copy()->subMonth();
                $startDate = $subMonth->copy()->startOfMonth()->toDateString();
                $endDate = $subMonth->copy()->endOfMonth()->toDateString();
                $rangeTitle = 'Bulan Lalu';
                $rangeSub = $subMonth->translatedFormat('F Y');
                break;
            case 'year':
                $startDate = $now->copy()->startOfYear()->toDateString();
                $endDate = $now->copy()->endOfYear()->toDateString();
                $rangeTitle = 'Tahun Ini';
                $rangeSub = $now->translatedFormat('Y');
                break;
            case 'custom':
                $startDate = $request->input('dari', $now->copy()->startOfMonth()->toDateString());
                $endDate = $request->input('sampai', $todayStr);
                if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
                    [$startDate, $endDate] = [$endDate, $startDate];
                }
                $rangeTitle = 'Pilih Tanggal';
                $rangeSub = Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . Carbon::parse($endDate)->translatedFormat('d M Y');
                break;
            case 'month':
            default:
                $range = 'month';
                $startDate = $now->copy()->startOfMonth()->toDateString();
                $endDate = $now->copy()->endOfMonth()->toDateString();
                $rangeTitle = 'Bulan Ini';
                $rangeSub = $now->translatedFormat('F Y');
                break;
        }

        $dari = $startDate;
        $sampai = $endDate;
        $daysInRange = max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1);

        $daftarAgen = $this->sirkulasi->getDaftarAgen();
        $aktifAgens = $daftarAgen->where('status', 'aktif');
        $totalAgen = $daftarAgen->count();
        $totalAgenAktif = $aktifAgens->count();
        $totalAgenNonAktif = $daftarAgen->where('status', 'non-aktif')->count();

        $wilayahList = $this->sirkulasi->getDaftarWilayah();
        $jurusanList = $this->sirkulasi->getDaftarJurusan();
        $totalWilayah = $wilayahList->count();
        $totalJurusan = $jurusanList->count();

        $totalPiutang = (int) $daftarAgen->sum('saldo_piutang');
        $jumlahAgenPiutang = (int) $daftarAgen->where('saldo_piutang', '>', 0)->count();

        $agenBesar = $aktifAgens->where('kategori', '11');
        $subAgen = $aktifAgens->where('kategori', '99');

        $cfg = $this->sirkulasi->getConfig();
        $hargaBesar = $cfg['harga_agen_besar'];
        $hargaEceran = $cfg['harga_eceran'];
        $pctL = $cfg['pct_langganan'];
        $pctK = $cfg['pct_konsinyasi'];

        $dailyStatsForAgens = static function (Collection $agens, string $dateStr, array $cachedMap) use ($hargaBesar, $hargaEceran, $pctL, $pctK): array {
            $kirim = 0;
            $retur = 0;
            $omset = 0;
            $langganan = 0;
            $konsinyasi = 0;
            $gratis = 0;

            foreach ($agens as $ag) {
                $custom = $cachedMap[$ag->id] ?? null;
                if ($custom) {
                    $l = (int) ($custom['langganan'] ?? 0);
                    $k = (int) ($custom['konsinyasi'] ?? 0);
                    $g = (int) ($custom['gratis'] ?? 0) + (int) ($custom['pengenalan'] ?? 0) + (int) ($custom['insidentil'] ?? 0);
                    $jatahHari = $l + $k + $g;
                } else {
                    $jatahHari = (int) $ag->jatah_default;
                    $l = (int) ($jatahHari * $pctL);
                    $k = (int) ($jatahHari * $pctK);
                    $g = max(0, $jatahHari - $l - $k);
                }

                $retHari = (int) round($jatahHari * 0.022);
                $nettHari = max(0, $jatahHari - $retHari);
                $harga = $ag->kategori === '11' ? $hargaBesar : $hargaEceran;

                $kirim += $jatahHari;
                $retur += $retHari;
                $omset += $nettHari * $harga;
                $langganan += $l;
                $konsinyasi += $k;
                $gratis += $g;
            }

            return [
                'kirim'      => $kirim,
                'retur'      => $retur,
                'nett'       => max(0, $kirim - $retur),
                'omset'      => $omset,
                'langganan'  => $langganan,
                'konsinyasi' => $konsinyasi,
                'gratis'     => $gratis,
            ];
        };

        $baseHarianBesar = $dailyStatsForAgens($agenBesar, $todayStr, []);
        $baseHarianSub = $dailyStatsForAgens($subAgen, $todayStr, []);

        $todayCache = Cache::get('jatah_harian_' . $todayStr, []);
        $todayStatsBesar = !empty($todayCache) ? $dailyStatsForAgens($agenBesar, $todayStr, $todayCache) : $baseHarianBesar;
        $todayStatsSub = !empty($todayCache) ? $dailyStatsForAgens($subAgen, $todayStr, $todayCache) : $baseHarianSub;

        $oplahHarian = $todayStatsBesar['kirim'] + $todayStatsSub['kirim'];
        $totalReturHarian = $todayStatsBesar['retur'] + $todayStatsSub['retur'];
        $penjualanHariIni = $todayStatsBesar['omset'] + $todayStatsSub['omset'];
        $efektivitasDistribusi = $oplahHarian > 0 ? round((($oplahHarian - $totalReturHarian) / $oplahHarian) * 100, 1) : 100.0;

        $totalKirimPeriode = 0;
        $totalRetur = 0;
        $totalTerjualPeriode = 0;
        $omsetBesar = 0;
        $omsetSub = 0;
        $eksBesarPeriode = 0;
        $eksSubPeriode = 0;
        $porsiLangganan = 0;
        $porsiKonsinyasi = 0;
        $porsiGratis = 0;

        $cursor = Carbon::parse($startDate);
        $endCursor = Carbon::parse($endDate);
        while ($cursor->lte($endCursor)) {
            $dStr = $cursor->toDateString();
            $cachedDay = Cache::get('jatah_harian_' . $dStr, []);
            $stBesar = !empty($cachedDay) ? $dailyStatsForAgens($agenBesar, $dStr, $cachedDay) : $baseHarianBesar;
            $stSub = !empty($cachedDay) ? $dailyStatsForAgens($subAgen, $dStr, $cachedDay) : $baseHarianSub;

            $omsetBesar += $stBesar['omset'];
            $omsetSub += $stSub['omset'];
            $eksBesarPeriode += $stBesar['kirim'];
            $eksSubPeriode += $stSub['kirim'];
            $totalKirimPeriode += $stBesar['kirim'] + $stSub['kirim'];
            $totalRetur += $stBesar['retur'] + $stSub['retur'];
            $totalTerjualPeriode += $stBesar['nett'] + $stSub['nett'];
            $porsiLangganan += $stBesar['langganan'] + $stSub['langganan'];
            $porsiKonsinyasi += $stBesar['konsinyasi'] + $stSub['konsinyasi'];
            $porsiGratis += $stBesar['gratis'] + $stSub['gratis'];

            $cursor->addDay();
        }

        $wilayahNonKomersil = $wilayahList->whereIn('kode', ['06', '08']);
        $eksNonKomersilHarian = (int) $wilayahNonKomersil->sum('alokasi_default');
        $eksNonKomersilPeriode = $eksNonKomersilHarian * $daysInRange;

        $totalOmset = $omsetBesar + $omsetSub;

        $totalBayarKasir = 0;
        $jumlahSetoranKasir = 0;
        if (Schema::hasTable('kas_masuks')) {
            $kasSirkulasiQ = KasMasuk::where('tipe_transaksi', 'sirkulasi')
                ->whereBetween('tgl_transaksi', [$startDate, $endDate]);
            $totalBayarKasir = (int) (clone $kasSirkulasiQ)->sum('jumlah_dibayar');
            $jumlahSetoranKasir = (int) (clone $kasSirkulasiQ)->count();
        }

        $piutangBesar = (int) $agenBesar->sum('saldo_piutang');
        $piutangSub = (int) $subAgen->sum('saldo_piutang');
        $pctBesar = $totalOmset > 0 ? round(($omsetBesar / $totalOmset) * 100) : 0;
        $pctSub = $totalOmset > 0 ? round(($omsetSub / $totalOmset) * 100) : 0;
        $totalEksSemua = max(1, $totalKirimPeriode + $eksNonKomersilPeriode);
        $pctNonKomersil = round(($eksNonKomersilPeriode / $totalEksSemua) * 100);

        $channels = [
            'besar' => [
                'nama'      => '11 - Agen Besar',
                'count'     => $agenBesar->count(),
                'unit'      => 'Agen',
                'percent'   => $pctBesar,
                'omzet'     => $omsetBesar,
                'kirim_eks' => $eksBesarPeriode,
                'piutang'   => $piutangBesar,
                'info_ops'  => 'Tarif standar: Rp ' . number_format($hargaBesar, 0, ',', '.') . ' / eks &bull; Harian: ' . number_format($baseHarianBesar['kirim'], 0, ',', '.') . ' eks',
                'route_new' => 'sirkulasi.jatah.edit',
                'route_tx'  => route('sirkulasi.tabel.agen', ['kategori' => '11']),
            ],
            'sub' => [
                'nama'      => '99 - Sub Agen & Kios',
                'count'     => $subAgen->count(),
                'unit'      => 'Mitra',
                'percent'   => $pctSub,
                'omzet'     => $omsetSub,
                'kirim_eks' => $eksSubPeriode,
                'piutang'   => $piutangSub,
                'info_ops'  => 'Tarif konsinyasi: Rp ' . number_format($hargaEceran, 0, ',', '.') . ' / eks &bull; Harian: ' . number_format($baseHarianSub['kirim'], 0, ',', '.') . ' eks',
                'route_new' => 'sirkulasi.jatah.edit',
                'route_tx'  => route('sirkulasi.tabel.agen', ['kategori' => '99']),
            ],
            'khusus' => [
                'nama'      => '06/08 - Gratis & Insidentil',
                'count'     => $wilayahNonKomersil->sum('total_agen'),
                'unit'      => 'Titik',
                'percent'   => $pctNonKomersil,
                'omzet'     => 0,
                'kirim_eks' => $eksNonKomersilPeriode,
                'piutang'   => 0,
                'info_ops'  => 'Bukti iklan, redaksi & suplemen &bull; Harian: ' . number_format($eksNonKomersilHarian, 0, ',', '.') . ' eks',
                'route_new' => 'sirkulasi.cetak.pemasaran',
                'route_tx'  => route('sirkulasi.tabel.wilayah'),
            ],
        ];

        $isTodayClosed = $this->sirkulasi->isTanggalJatahLocked($todayStr);

        $dailyLabels = [];
        $dailyBesar = [];
        $dailySub = [];

        if ($range === 'year' || $daysInRange > 31) {
            $curMonth = Carbon::parse($startDate)->startOfMonth();
            $endMonth = Carbon::parse($endDate)->endOfMonth();

            while ($curMonth->lte($endMonth)) {
                $dailyLabels[] = $curMonth->translatedFormat('M Y');
                $mStart = $curMonth->copy()->startOfMonth();
                $mEnd = $curMonth->copy()->endOfMonth();
                $mOmsetBesar = 0;
                $mOmsetSub = 0;

                $mCur = $mStart->copy();
                while ($mCur->lte($mEnd)) {
                    $d = $mCur->toDateString();
                    $cMap = Cache::get('jatah_harian_' . $d, []);
                    $stB = !empty($cMap) ? $dailyStatsForAgens($agenBesar, $d, $cMap) : $baseHarianBesar;
                    $stS = !empty($cMap) ? $dailyStatsForAgens($subAgen, $d, $cMap) : $baseHarianSub;
                    $mOmsetBesar += $stB['omset'];
                    $mOmsetSub += $stS['omset'];
                    $mCur->addDay();
                }

                $dailyBesar[] = $mOmsetBesar;
                $dailySub[] = $mOmsetSub;
                $curMonth->addMonth();
            }
        } elseif ($range === 'today' || $daysInRange === 1) {
            $dailyLabels = [$now->translatedFormat('d M Y')];
            $cMap = Cache::get('jatah_harian_' . $todayStr, []);
            $stB = !empty($cMap) ? $dailyStatsForAgens($agenBesar, $todayStr, $cMap) : $baseHarianBesar;
            $stS = !empty($cMap) ? $dailyStatsForAgens($subAgen, $todayStr, $cMap) : $baseHarianSub;
            $dailyBesar[] = $stB['omset'];
            $dailySub[] = $stS['omset'];
        } else {
            $curDay = Carbon::parse($startDate);
            $endDay = Carbon::parse($endDate);

            while ($curDay->lte($endDay)) {
                $d = $curDay->toDateString();
                $dailyLabels[] = $curDay->translatedFormat('d M');
                $cMap = Cache::get('jatah_harian_' . $d, []);
                $stB = !empty($cMap) ? $dailyStatsForAgens($agenBesar, $d, $cMap) : $baseHarianBesar;
                $stS = !empty($cMap) ? $dailyStatsForAgens($subAgen, $d, $cMap) : $baseHarianSub;
                $dailyBesar[] = $stB['omset'];
                $dailySub[] = $stS['omset'];
                $curDay->addDay();
            }
        }

        $chartTitle = 'Grafik Tren Penjualan Koran (' . $rangeTitle . ')';
        $chartSub = 'Nilai estimasi penjualan Agen Besar (11) dan Sub Agen (99) periode ' . strtolower($rangeTitle) . ' (' . $rangeSub . ')';

        $wilayahLeaderboard = $wilayahList->map(function ($w) use ($daysInRange, $hargaBesar) {
            $oplahPeriode = (int) $w->alokasi_default * $daysInRange;
            $estimasiOmset = $w->tipe === 'Non-Komersil (Gratis)' ? 0 : (int) ($oplahPeriode * 0.978) * $hargaBesar;

            return (object) [
                'kode'           => $w->kode,
                'nama'           => $w->nama,
                'tipe'           => $w->tipe,
                'total_agen'     => (int) $w->total_agen,
                'alokasi_harian' => (int) $w->alokasi_default,
                'oplah_periode'  => $oplahPeriode,
                'estimasi_omset' => $estimasiOmset,
            ];
        })->sortByDesc('oplah_periode')->take(6)->values();

        $totalJatahMix = max(1, $porsiLangganan + $porsiKonsinyasi + $porsiGratis);
        $komposisiDistribusi = [
            'langganan'  => ['eks' => $porsiLangganan, 'pct' => round(($porsiLangganan / $totalJatahMix) * 100)],
            'konsinyasi' => ['eks' => $porsiKonsinyasi, 'pct' => round(($porsiKonsinyasi / $totalJatahMix) * 100)],
            'gratis'     => ['eks' => $porsiGratis, 'pct' => round(($porsiGratis / $totalJatahMix) * 100)],
        ];

        $bulanKey = Carbon::parse($endDate)->format('Y-m');
        $periodCode = str_replace('-', '', $bulanKey);

        $recentFaktur = $aktifAgens->take(8)->map(function ($agen) use ($daysInRange, $periodCode, $hargaBesar, $hargaEceran) {
            $kirim = (int) $agen->jatah_default * $daysInRange;
            $retur = (int) round($kirim * 0.022);
            $nett = max(0, $kirim - $retur);
            $harga = $agen->kategori === '11' ? $hargaBesar : $hargaEceran;
            $tagihan = $nett * $harga;
            $noFaktur = sprintf('FKT-SRK-%s-%03d', $periodCode, $agen->id);

            return (object) [
                'id'            => $agen->id,
                'no_faktur'     => $noFaktur,
                'kode_agen'     => $agen->kode_agen,
                'nama_agen'     => $agen->nama_agen,
                'kategori'      => $agen->kategori,
                'wilayah_nama'  => $agen->wilayah_nama,
                'jurusan_kode'  => $agen->jurusan_kode,
                'jurusan_nama'  => $agen->jurusan_nama,
                'kirim'         => $kirim,
                'retur'         => $retur,
                'nett'          => $nett,
                'tagihan'       => $tagihan,
                'saldo_piutang' => (int) $agen->saldo_piutang,
                'status'        => (int) $agen->saldo_piutang <= 0 ? 'Lunas' : 'Piutang',
            ];
        })->values();

        $kalenderService = app(KalenderNasionalService::class);
        $kalenderAwal = $kalenderService->getKalenderBulan((int) date('Y'), (int) date('n'));

        return view('pages.sirkulasi.dashboard', compact(
            'range',
            'rangeTitle',
            'rangeSub',
            'dari',
            'sampai',
            'daysInRange',
            'today',
            'tomorrow',
            'oplahHarian',
            'totalKirimPeriode',
            'totalTerjualPeriode',
            'totalReturHarian',
            'totalRetur',
            'efektivitasDistribusi',
            'totalOmset',
            'penjualanHariIni',
            'totalBayarKasir',
            'jumlahSetoranKasir',
            'totalPiutang',
            'jumlahAgenPiutang',
            'totalAgen',
            'totalAgenAktif',
            'totalAgenNonAktif',
            'totalWilayah',
            'totalJurusan',
            'isTodayClosed',
            'channels',
            'dailyLabels',
            'dailyBesar',
            'dailySub',
            'chartTitle',
            'chartSub',
            'wilayahLeaderboard',
            'komposisiDistribusi',
            'recentFaktur',
            'kalenderAwal'
        ));
    }
}
