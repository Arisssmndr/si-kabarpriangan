<?php

namespace App\Http\Controllers;

use App\Models\JenisIklan;
use App\Models\KategoriMedia;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /**
     * Dispatcher utama: Mengarahkan otomatis ke dashboard divisi pengguna yang sedang login.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $role = strtolower(trim((string) $user->role));

        return match ($role) {
            'iklan'                      => redirect()->route('iklan.dashboard'),
            'keuangan', 'admin_keuangan' => redirect()->route('keuangan.dashboard'),
            'faktur'                     => redirect()->route('keuangan.faktur.index'),
            'anggaran'                   => redirect()->route('keuangan.anggaran.index'),
            'accounting'                 => redirect()->route('accounting.dashboard'),
            'sirkulasi'                  => redirect()->route('sirkulasi.dashboard'),
            'kasir'                      => redirect()->route('kasir.dashboard'),
            default                      => redirect()->route('keuangan.dashboard'),
        };
    }

    /**
     * Dashboard Divisi Iklan (Koran Cetak, Media Online, Priangan TV)
     */
    public function iklan(Request $request)
    {
        $range = $request->input('range', 'month');
        $now = Carbon::now();

        // 1. Rentang waktu berdasarkan filter
        switch ($range) {
            case 'today':
                $startDate = $now->toDateString();
                $endDate = $now->toDateString();
                $rangeTitle = 'Hari Ini';
                $rangeSub = $now->translatedFormat('d M Y');
                $rangeLabel = 'Hari Ini (' . $now->translatedFormat('d M Y') . ')';
                break;
            case '7days':
            case 'week':
                $range = '7days';
                $startDate = $now->copy()->subDays(6)->toDateString();
                $endDate = $now->toDateString();
                $rangeTitle = '7 Hari Terakhir';
                $rangeSub = Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . $now->translatedFormat('d M Y');
                $rangeLabel = '7 Hari Terakhir (' . $rangeSub . ')';
                break;
            case '30days':
                $startDate = $now->copy()->subDays(29)->toDateString();
                $endDate = $now->toDateString();
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
                $endDate = $request->input('sampai', $now->toDateString());
                $rangeTitle = 'Kustom';
                $rangeSub = Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . Carbon::parse($endDate)->translatedFormat('d M Y');
                $rangeLabel = 'Kustom (' . $rangeSub . ')';
                break;
            case 'month':
            default:
                $startDate = $now->copy()->startOfMonth()->toDateString();
                $endDate = $now->copy()->endOfMonth()->toDateString();
                $rangeTitle = 'Bulan Ini';
                $rangeSub = $now->translatedFormat('F Y');
                $rangeLabel = 'Bulan Ini (' . $rangeSub . ')';
                $range = 'month';
                break;
        }

        $dari = $startDate;
        $sampai = $endDate;

        // 2. Query Transaksi Per Saluran Media
        // A. Koran Cetak
        $koranQuery = TransaksiKoran::whereBetween('tanggal_transaksikoran', [$startDate, $endDate]);
        $omzetKoran = (int) $koranQuery->sum('totaltagihan_transaksikoran');
        $bayarKoran = (int) $koranQuery->sum('jumlahbayar_transaksikoran');
        $piutangKoran = (int) $koranQuery->sum('piutang_transaksikoran');
        $countKoran = (int) $koranQuery->count();

        // B. Media Online
        $onlineQuery = TransaksiOnline::whereBetween('tanggal_transaksionline', [$startDate, $endDate]);
        $omzetOnline = (int) $onlineQuery->sum('totaltagihan_transaksionline');
        $bayarOnline = (int) $onlineQuery->sum('jumlahbayar_transaksionline');
        $piutangOnline = (int) $onlineQuery->sum('piutang_transaksionline');
        $countOnline = (int) $onlineQuery->count();

        // C. Priangan TV
        $tvQuery = TransaksiPriangan::whereBetween('tanggal_transaksipriangan', [$startDate, $endDate]);
        $omzetTv = (int) $tvQuery->sum('totaltagihan_transaksipriangan');
        $bayarTv = (int) $tvQuery->sum('jumlahbayar_transaksipriangan');
        $piutangTv = (int) $tvQuery->sum('piutang_transaksipriangan');
        $countTv = (int) $tvQuery->count();

        // 3. Agregasi Total
        $totalOmset = $omzetKoran + $omzetOnline + $omzetTv;
        $totalOmzet = $totalOmset;
        $totalBayar = $bayarKoran + $bayarOnline + $bayarTv;
        $totalPiutang = $piutangKoran + $piutangOnline + $piutangTv;
        $totalTransaksi = $countKoran + $countOnline + $countTv;

        // Persentase Kontribusi Omzet
        $pctKoran = $totalOmset > 0 ? round(($omzetKoran / $totalOmset) * 100) : 0;
        $pctOnline = $totalOmset > 0 ? round(($omzetOnline / $totalOmset) * 100) : 0;
        $pctTv = $totalOmset > 0 ? round(($omzetTv / $totalOmset) * 100) : 0;

        $channels = [
            'koran' => [
                'nama'       => 'Koran Cetak',
                'sub'        => 'Harian Umum Kabar Priangan',
                'kode'       => 'KRN',
                'badge_bg'   => 'bg-sky-50 text-sky-700 border-sky-200',
                'omzet'      => $omzetKoran,
                'bayar'      => $bayarKoran,
                'piutang'    => $piutangKoran,
                'count'      => $countKoran,
                'percent'    => $pctKoran,
                'route_tx'   => 'transaksikoran.index',
                'route_new'  => 'transaksikoran.create',
            ],
            'online' => [
                'nama'       => 'Media Online',
                'sub'        => 'kabarpriangan.pikiran-rakyat.com',
                'kode'       => 'ONL',
                'badge_bg'   => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'omzet'      => $omzetOnline,
                'bayar'      => $bayarOnline,
                'piutang'    => $piutangOnline,
                'count'      => $countOnline,
                'percent'    => $pctOnline,
                'route_tx'   => 'transaksionline.index',
                'route_new'  => 'transaksionline.create',
            ],
            'tv' => [
                'nama'       => 'Priangan TV',
                'sub'        => 'Channel Siaran Televisi & Streaming',
                'kode'       => 'PTV',
                'badge_bg'   => 'bg-teal-50 text-teal-700 border-teal-200',
                'omzet'      => $omzetTv,
                'bayar'      => $bayarTv,
                'piutang'    => $piutangTv,
                'count'      => $countTv,
                'percent'    => $pctTv,
                'route_tx'   => 'transaksipriangan.index',
                'route_new'  => 'transaksipriangan.create',
            ],
        ];

        // 4. Data Master Ringkas
        $totalKategori = KategoriMedia::where('is_active', true)->count();
        $totalJenisIklan = JenisIklan::where('is_active', true)->count();

        // 5. Transaksi Terbaru Gabungan (Top 6 Terbaru)
        $recentTransactions = new Collection();

        $recentKoran = TransaksiKoran::with('iklankoran')->latest('id')->take(4)->get();
        foreach ($recentKoran as $k) {
            $recentTransactions->push((object)[
                'id'         => $k->id,
                'channel'    => 'Koran Cetak',
                'code'       => 'KRN',
                'faktur'     => $k->nofakturkoran,
                'tanggal'    => $k->tanggal_transaksikoran,
                'customer'   => $k->nama_pemasangkoran,
                'paket'      => $k->iklankoran->nama_jenis ?? '-',
                'total'      => (int) $k->totaltagihan_transaksikoran,
                'piutang'    => (int) $k->piutang_transaksikoran,
                'is_lunas'   => (int) $k->piutang_transaksikoran <= 0,
                'created_at' => $k->created_at,
                'route_view' => route('transaksikoran.index'),
            ]);
        }

        $recentOnline = TransaksiOnline::with('iklanonline')->latest('id')->take(4)->get();
        foreach ($recentOnline as $o) {
            $recentTransactions->push((object)[
                'id'         => $o->id,
                'channel'    => 'Media Online',
                'code'       => 'ONL',
                'faktur'     => $o->nofakturonline,
                'tanggal'    => $o->tanggal_transaksionline,
                'customer'   => $o->nama_pemasangonline,
                'paket'      => $o->iklanonline->nama_jenis ?? '-',
                'total'      => (int) $o->totaltagihan_transaksionline,
                'piutang'    => (int) $o->piutang_transaksionline,
                'is_lunas'   => (int) $o->piutang_transaksionline <= 0,
                'created_at' => $o->created_at,
                'route_view' => route('transaksionline.index'),
            ]);
        }

        $recentTv = TransaksiPriangan::with('iklanpriangan')->latest('id')->take(4)->get();
        foreach ($recentTv as $t) {
            $recentTransactions->push((object)[
                'id'         => $t->id,
                'channel'    => 'Priangan TV',
                'code'       => 'PTV',
                'faktur'     => $t->nofakturpriangan,
                'tanggal'    => $t->tanggal_transaksipriangan,
                'customer'   => $t->nama_pemasangpriangan,
                'paket'      => $t->iklanpriangan->nama_jenis ?? '-',
                'total'      => (int) $t->totaltagihan_transaksipriangan,
                'piutang'    => (int) $t->piutang_transaksipriangan,
                'is_lunas'   => (int) $t->piutang_transaksipriangan <= 0,
                'created_at' => $t->created_at,
                'route_view' => route('transaksipriangan.index'),
            ]);
        }

        $recentStream = $recentTransactions->sortByDesc('created_at')->take(6)->values();

        // 6. Data Trend 7 Hari
        $dailyLabels = [];
        $dailyKoran = [];
        $dailyOnline = [];
        $dailyTv = [];

        for ($i = 6; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i)->toDateString();
            $dailyLabels[] = Carbon::parse($d)->translatedFormat('d M');
            $dailyKoran[] = (int) TransaksiKoran::whereDate('tanggal_transaksikoran', $d)->sum('totaltagihan_transaksikoran');
            $dailyOnline[] = (int) TransaksiOnline::whereDate('tanggal_transaksionline', $d)->sum('totaltagihan_transaksionline');
            $dailyTv[] = (int) TransaksiPriangan::whereDate('tanggal_transaksipriangan', $d)->sum('totaltagihan_transaksipriangan');
        }

        return view('pages.iklan.dashboard', compact(
            'range',
            'rangeLabel',
            'rangeTitle',
            'rangeSub',
            'dari',
            'sampai',
            'totalOmset',
            'totalOmzet',
            'totalBayar',
            'totalPiutang',
            'totalTransaksi',
            'channels',
            'totalKategori',
            'totalJenisIklan',
            'recentStream',
            'dailyLabels',
            'dailyKoran',
            'dailyOnline',
            'dailyTv'
        ));
    }

    /**
     * Dashboard Administrator (Executive Overview 5 Divisi)
     */
    public function admin(Request $request)
    {
        $totKoran = (int) TransaksiKoran::sum('totaltagihan_transaksikoran');
        $totOnline = (int) TransaksiOnline::sum('totaltagihan_transaksionline');
        $totTv = (int) TransaksiPriangan::sum('totaltagihan_transaksipriangan');
        $totalOmsetIklan = $totKoran + $totOnline + $totTv;

        $bayarKoran = (int) TransaksiKoran::sum('jumlahbayar_transaksikoran');
        $bayarOnline = (int) TransaksiOnline::sum('jumlahbayar_transaksionline');
        $bayarTv = (int) TransaksiPriangan::sum('jumlahbayar_transaksipriangan');
        $totalBayarIklan = $bayarKoran + $bayarOnline + $bayarTv;

        $piutangKoran = (int) TransaksiKoran::sum('piutang_transaksikoran');
        $piutangOnline = (int) TransaksiOnline::sum('piutang_transaksionline');
        $piutangTv = (int) TransaksiPriangan::sum('piutang_transaksipriangan');
        $totalPiutangIklan = $piutangKoran + $piutangOnline + $piutangTv;

        $totalTxIklan = TransaksiKoran::count() + TransaksiOnline::count() + TransaksiPriangan::count();

        $arusKasMasuk = $totalBayarIklan;
        $estimasiPengeluaranBulanIni = (int) ($arusKasMasuk * 0.42);
        $saldoKasTersedia = max(0, $arusKasMasuk - $estimasiPengeluaranBulanIni);

        $labaKotorBerjalan = max(0, $totalOmsetIklan - $estimasiPengeluaranBulanIni);
        $totalJurnalPending = 4;
        $statusBukuBesar = 'Seimbang (Balance)';

        $oplahHariIni = 12500;
        $totalAgenAktif = 78;
        $returKoranHariIni = 320;
        $persentaseTerdistribusi = 97.4;

        $today = Carbon::today()->toDateString();
        $penerimaanHariIni = (int) (
            TransaksiKoran::whereDate('tanggal_transaksikoran', $today)->sum('jumlahbayar_transaksikoran') +
            TransaksiOnline::whereDate('tanggal_transaksionline', $today)->sum('jumlahbayar_transaksionline') +
            TransaksiPriangan::whereDate('tanggal_transaksipriangan', $today)->sum('jumlahbayar_transaksipriangan')
        );
        $txHariIni = (int) (
            TransaksiKoran::whereDate('tanggal_transaksikoran', $today)->count() +
            TransaksiOnline::whereDate('tanggal_transaksionline', $today)->count() +
            TransaksiPriangan::whereDate('tanggal_transaksipriangan', $today)->count()
        );

        $totalUsers = User::count();

        return view('pages.admin.dashboard', compact(
            'totalOmsetIklan',
            'totalBayarIklan',
            'totalPiutangIklan',
            'totalTxIklan',
            'arusKasMasuk',
            'estimasiPengeluaranBulanIni',
            'saldoKasTersedia',
            'labaKotorBerjalan',
            'totalJurnalPending',
            'statusBukuBesar',
            'oplahHariIni',
            'totalAgenAktif',
            'returKoranHariIni',
            'persentaseTerdistribusi',
            'penerimaanHariIni',
            'txHariIni',
            'totalUsers'
        ));
    }

    /**
     * Dashboard Divisi Keuangan (Monitoring Arus Kas, Piutang & Aging Schedule)
     */
    public function keuangan(Request $request)
    {
        $range = $request->input('range', 'month');
        $now = Carbon::now();

        // 1. Rentang waktu berdasarkan filter tanggal
        switch ($range) {
            case 'today':
                $startDate = $now->toDateString();
                $endDate = $now->toDateString();
                $rangeTitle = 'Hari Ini';
                $rangeSub = $now->translatedFormat('d M Y');
                $rangeLabel = 'Hari Ini (' . $now->translatedFormat('d M Y') . ')';
                break;
            case '7days':
            case 'week':
                $range = '7days';
                $startDate = $now->copy()->subDays(6)->toDateString();
                $endDate = $now->toDateString();
                $rangeTitle = '7 Hari Terakhir';
                $rangeSub = Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . $now->translatedFormat('d M Y');
                $rangeLabel = '7 Hari Terakhir (' . $rangeSub . ')';
                break;
            case '30days':
                $startDate = $now->copy()->subDays(29)->toDateString();
                $endDate = $now->toDateString();
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
                $endDate = $request->input('sampai', $now->toDateString());
                $rangeTitle = 'Kustom';
                $rangeSub = Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . Carbon::parse($endDate)->translatedFormat('d M Y');
                $rangeLabel = 'Kustom (' . $rangeSub . ')';
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

        // 2. Query Transaksi Per Saluran Media dalam Rentang Tanggal Terpilih
        $koranQuery = TransaksiKoran::whereBetween('tanggal_transaksikoran', [$startDate, $endDate]);
        $onlineQuery = TransaksiOnline::whereBetween('tanggal_transaksionline', [$startDate, $endDate]);
        $tvQuery = TransaksiPriangan::whereBetween('tanggal_transaksipriangan', [$startDate, $endDate]);

        $tagihanKoran = (int) $koranQuery->sum('totaltagihan_transaksikoran');
        $bayarKoran = (int) $koranQuery->sum('jumlahbayar_transaksikoran');
        $piutangKoran = (int) $koranQuery->sum('piutang_transaksikoran');
        $countKoran = (int) $koranQuery->count();

        $tagihanOnline = (int) $onlineQuery->sum('totaltagihan_transaksionline');
        $bayarOnline = (int) $onlineQuery->sum('jumlahbayar_transaksionline');
        $piutangOnline = (int) $onlineQuery->sum('piutang_transaksionline');
        $countOnline = (int) $onlineQuery->count();

        $tagihanTv = (int) $tvQuery->sum('totaltagihan_transaksipriangan');
        $bayarTv = (int) $tvQuery->sum('jumlahbayar_transaksipriangan');
        $piutangTv = (int) $tvQuery->sum('piutang_transaksipriangan');
        $countTv = (int) $tvQuery->count();

        // 3. Agregasi Total Finansial Periode Terpilih
        $totalTagihan = $tagihanKoran + $tagihanOnline + $tagihanTv;
        $totalPenerimaan = $bayarKoran + $bayarOnline + $bayarTv;
        $totalPiutangPeriode = $piutangKoran + $piutangOnline + $piutangTv;
        $totalTransaksi = $countKoran + $countOnline + $countTv;

        // Rasio Penagihan / Collection Rate (%)
        $collectionRate = $totalTagihan > 0 ? round(($totalPenerimaan / $totalTagihan) * 100, 1) : 0;

        // Faktur Belum Lunas Periode Ini
        $invoicePendingPeriode = (int) (
            TransaksiKoran::whereBetween('tanggal_transaksikoran', [$startDate, $endDate])->where('piutang_transaksikoran', '>', 0)->count() +
            TransaksiOnline::whereBetween('tanggal_transaksionline', [$startDate, $endDate])->where('piutang_transaksionline', '>', 0)->count() +
            TransaksiPriangan::whereBetween('tanggal_transaksipriangan', [$startDate, $endDate])->where('piutang_transaksipriangan', '>', 0)->count()
        );

        // 4. Data Finansial Hari Ini & Kumulatif Menyeluruh
        $today = Carbon::today()->toDateString();
        $masukHariIni = (int) (
            TransaksiKoran::whereDate('tanggal_transaksikoran', $today)->sum('jumlahbayar_transaksikoran') +
            TransaksiOnline::whereDate('tanggal_transaksionline', $today)->sum('jumlahbayar_transaksionline') +
            TransaksiPriangan::whereDate('tanggal_transaksipriangan', $today)->sum('jumlahbayar_transaksipriangan')
        );

        $masukBulanIni = (int) (
            TransaksiKoran::whereMonth('tanggal_transaksikoran', $now->month)->whereYear('tanggal_transaksikoran', $now->year)->sum('jumlahbayar_transaksikoran') +
            TransaksiOnline::whereMonth('tanggal_transaksionline', $now->month)->whereYear('tanggal_transaksionline', $now->year)->sum('jumlahbayar_transaksionline') +
            TransaksiPriangan::whereMonth('tanggal_transaksipriangan', $now->month)->whereYear('tanggal_transaksipriangan', $now->year)->sum('jumlahbayar_transaksipriangan')
        );

        // Piutang Kumulatif Keseluruhan (Total Outstanding Real di Sistem)
        $totalPiutangKumulatif = (int) (
            TransaksiKoran::sum('piutang_transaksikoran') +
            TransaksiOnline::sum('piutang_transaksionline') +
            TransaksiPriangan::sum('piutang_transaksipriangan')
        );

        $totalInvoicePendingKumulatif = (int) (
            TransaksiKoran::where('piutang_transaksikoran', '>', 0)->count() +
            TransaksiOnline::where('piutang_transaksionline', '>', 0)->count() +
            TransaksiPriangan::where('piutang_transaksipriangan', '>', 0)->count()
        );

        // 5. Data Per Saluran Media
        $channels = [
            'koran' => [
                'nama'         => 'Koran Cetak',
                'sub'          => 'Harian Umum Kabar Priangan',
                'kode'         => 'KRN',
                'badge_bg'     => 'bg-sky-50 text-sky-700 border-sky-200',
                'tagihan'      => $tagihanKoran,
                'bayar'        => $bayarKoran,
                'piutang'      => $piutangKoran,
                'count'        => $countKoran,
                'persen_lunas' => $tagihanKoran > 0 ? round(($bayarKoran / $tagihanKoran) * 100, 1) : 0,
                'route_tx'     => 'transaksikoran.index',
            ],
            'online' => [
                'nama'         => 'Media Online',
                'sub'          => 'kabarpriangan.pikiran-rakyat.com',
                'kode'         => 'ONL',
                'badge_bg'     => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'tagihan'      => $tagihanOnline,
                'bayar'        => $bayarOnline,
                'piutang'      => $piutangOnline,
                'count'        => $countOnline,
                'persen_lunas' => $tagihanOnline > 0 ? round(($bayarOnline / $tagihanOnline) * 100, 1) : 0,
                'route_tx'     => 'transaksionline.index',
            ],
            'tv' => [
                'nama'         => 'Priangan TV',
                'sub'          => 'Televisi & Siaran Streaming',
                'kode'         => 'PTV',
                'badge_bg'     => 'bg-teal-50 text-teal-700 border-teal-200',
                'tagihan'      => $tagihanTv,
                'bayar'        => $bayarTv,
                'piutang'      => $piutangTv,
                'count'        => $countTv,
                'persen_lunas' => $tagihanTv > 0 ? round(($bayarTv / $tagihanTv) * 100, 1) : 0,
                'route_tx'     => 'transaksipriangan.index',
            ],
        ];

        // 6. Analisis Umur Piutang (Aging Schedule)
        $agingLancar = 0;      // <= 30 hari
        $agingLancarCount = 0;
        $agingPerhatian = 0;   // 31 - 60 hari
        $agingPerhatianCount = 0;
        $agingKritis = 0;      // > 60 hari
        $agingKritisCount = 0;

        $unpaidInvoices = new Collection();

        // Ambil piutang Koran
        $allUnpaidKoran = TransaksiKoran::with('iklankoran')->where('piutang_transaksikoran', '>', 0)->get();
        foreach ($allUnpaidKoran as $k) {
            $tgl = Carbon::parse($k->tanggal_transaksikoran);
            $ageDays = (int) $tgl->diffInDays($now, false);
            if ($ageDays < 0) $ageDays = 0;
            $piutangVal = (int) $k->piutang_transaksikoran;

            if ($ageDays <= 30) {
                $agingLancar += $piutangVal;
                $agingLancarCount++;
                $statusAging = 'Lancar (< 30 Hari)';
                $badgeAging = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            } elseif ($ageDays <= 60) {
                $agingPerhatian += $piutangVal;
                $agingPerhatianCount++;
                $statusAging = 'Perhatian (31-60 Hari)';
                $badgeAging = 'bg-amber-50 text-amber-700 border-amber-200';
            } else {
                $agingKritis += $piutangVal;
                $agingKritisCount++;
                $statusAging = 'Jatuh Tempo (> 60 Hari)';
                $badgeAging = 'bg-rose-50 text-rose-700 border-rose-200';
            }

            $unpaidInvoices->push((object)[
                'id'            => $k->id,
                'media'         => 'Koran Cetak',
                'media_kode'    => 'KRN',
                'badge_media'   => 'bg-sky-50 text-sky-700 border-sky-200',
                'nofaktur'      => $k->nofakturkoran,
                'tanggal'       => $k->tanggal_transaksikoran,
                'age_days'      => $ageDays,
                'pemasang'      => $k->nama_pemasangkoran,
                'paket'         => $k->iklankoran->nama_jenis ?? 'Iklan Koran',
                'totaltagihan'  => (int) $k->totaltagihan_transaksikoran,
                'jumlahbayar'   => (int) $k->jumlahbayar_transaksikoran,
                'piutang'       => $piutangVal,
                'status_aging'  => $statusAging,
                'badge_aging'   => $badgeAging,
                'cetak_url'     => route('transaksikoran.cetak', $k->id),
            ]);
        }

        // Ambil piutang Online
        $allUnpaidOnline = TransaksiOnline::with('iklanonline')->where('piutang_transaksionline', '>', 0)->get();
        foreach ($allUnpaidOnline as $o) {
            $tgl = Carbon::parse($o->tanggal_transaksionline);
            $ageDays = (int) $tgl->diffInDays($now, false);
            if ($ageDays < 0) $ageDays = 0;
            $piutangVal = (int) $o->piutang_transaksionline;

            if ($ageDays <= 30) {
                $agingLancar += $piutangVal;
                $agingLancarCount++;
                $statusAging = 'Lancar (< 30 Hari)';
                $badgeAging = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            } elseif ($ageDays <= 60) {
                $agingPerhatian += $piutangVal;
                $agingPerhatianCount++;
                $statusAging = 'Perhatian (31-60 Hari)';
                $badgeAging = 'bg-amber-50 text-amber-700 border-amber-200';
            } else {
                $agingKritis += $piutangVal;
                $agingKritisCount++;
                $statusAging = 'Jatuh Tempo (> 60 Hari)';
                $badgeAging = 'bg-rose-50 text-rose-700 border-rose-200';
            }

            $unpaidInvoices->push((object)[
                'id'            => $o->id,
                'media'         => 'Media Online',
                'media_kode'    => 'ONL',
                'badge_media'   => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'nofaktur'      => $o->nofakturonline,
                'tanggal'       => $o->tanggal_transaksionline,
                'age_days'      => $ageDays,
                'pemasang'      => $o->nama_pemasangonline,
                'paket'         => $o->iklanonline->nama_jenis ?? 'Iklan Online',
                'totaltagihan'  => (int) $o->totaltagihan_transaksionline,
                'jumlahbayar'   => (int) $o->jumlahbayar_transaksionline,
                'piutang'       => $piutangVal,
                'status_aging'  => $statusAging,
                'badge_aging'   => $badgeAging,
                'cetak_url'     => route('transaksionline.cetak', $o->id),
            ]);
        }

        // Ambil piutang TV
        $allUnpaidTv = TransaksiPriangan::with('iklanpriangan')->where('piutang_transaksipriangan', '>', 0)->get();
        foreach ($allUnpaidTv as $t) {
            $tgl = Carbon::parse($t->tanggal_transaksipriangan);
            $ageDays = (int) $tgl->diffInDays($now, false);
            if ($ageDays < 0) $ageDays = 0;
            $piutangVal = (int) $t->piutang_transaksipriangan;

            if ($ageDays <= 30) {
                $agingLancar += $piutangVal;
                $agingLancarCount++;
                $statusAging = 'Lancar (< 30 Hari)';
                $badgeAging = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            } elseif ($ageDays <= 60) {
                $agingPerhatian += $piutangVal;
                $agingPerhatianCount++;
                $statusAging = 'Perhatian (31-60 Hari)';
                $badgeAging = 'bg-amber-50 text-amber-700 border-amber-200';
            } else {
                $agingKritis += $piutangVal;
                $agingKritisCount++;
                $statusAging = 'Jatuh Tempo (> 60 Hari)';
                $badgeAging = 'bg-rose-50 text-rose-700 border-rose-200';
            }

            $unpaidInvoices->push((object)[
                'id'            => $t->id,
                'media'         => 'Priangan TV',
                'media_kode'    => 'PTV',
                'badge_media'   => 'bg-teal-50 text-teal-700 border-teal-200',
                'nofaktur'      => $t->nofakturpriangan,
                'tanggal'       => $t->tanggal_transaksipriangan,
                'age_days'      => $ageDays,
                'pemasang'      => $t->nama_pemasangpriangan,
                'paket'         => $t->iklanpriangan->nama_jenis ?? 'Iklan TV',
                'totaltagihan'  => (int) $t->totaltagihan_transaksipriangan,
                'jumlahbayar'   => (int) $t->jumlahbayar_transaksipriangan,
                'piutang'       => $piutangVal,
                'status_aging'  => $statusAging,
                'badge_aging'   => $badgeAging,
                'cetak_url'     => route('transaksipriangan.cetak', $t->id),
            ]);
        }

        // Urutkan daftar piutang belum lunas: prioritas piutang terbesar
        $topUnpaidInvoices = $unpaidInvoices->sortByDesc('piutang')->take(8)->values();

        // 7. Transaksi Terbaru Gabungan (Top 8 Terbaru Lintas Saluran)
        $recentTransactions = new Collection();

        $recentKoran = TransaksiKoran::with('iklankoran')->latest('id')->take(4)->get();
        foreach ($recentKoran as $k) {
            $recentTransactions->push((object)[
                'id'          => $k->id,
                'channel'     => 'Koran Cetak',
                'code'        => 'KRN',
                'faktur'      => $k->nofakturkoran,
                'tanggal'     => $k->tanggal_transaksikoran,
                'customer'    => $k->nama_pemasangkoran,
                'paket'       => $k->iklankoran->nama_jenis ?? 'Iklan Koran',
                'total'       => (int) $k->totaltagihan_transaksikoran,
                'bayar'       => (int) $k->jumlahbayar_transaksikoran,
                'piutang'     => (int) $k->piutang_transaksikoran,
                'is_lunas'    => (int) $k->piutang_transaksikoran <= 0,
                'created_at'  => $k->created_at,
                'route_view'  => route('transaksikoran.index'),
                'route_cetak' => route('transaksikoran.cetak', $k->id),
            ]);
        }

        $recentOnline = TransaksiOnline::with('iklanonline')->latest('id')->take(4)->get();
        foreach ($recentOnline as $o) {
            $recentTransactions->push((object)[
                'id'          => $o->id,
                'channel'     => 'Media Online',
                'code'        => 'ONL',
                'faktur'      => $o->nofakturonline,
                'tanggal'     => $o->tanggal_transaksionline,
                'customer'    => $o->nama_pemasangonline,
                'paket'       => $o->iklanonline->nama_jenis ?? 'Iklan Online',
                'total'       => (int) $o->totaltagihan_transaksionline,
                'bayar'       => (int) $o->jumlahbayar_transaksionline,
                'piutang'     => (int) $o->piutang_transaksionline,
                'is_lunas'    => (int) $o->piutang_transaksionline <= 0,
                'created_at'  => $o->created_at,
                'route_view'  => route('transaksionline.index'),
                'route_cetak' => route('transaksionline.cetak', $o->id),
            ]);
        }

        $recentTv = TransaksiPriangan::with('iklanpriangan')->latest('id')->take(4)->get();
        foreach ($recentTv as $t) {
            $recentTransactions->push((object)[
                'id'          => $t->id,
                'channel'     => 'Priangan TV',
                'code'        => 'PTV',
                'faktur'      => $t->nofakturpriangan,
                'tanggal'     => $t->tanggal_transaksipriangan,
                'customer'    => $t->nama_pemasangpriangan,
                'paket'       => $t->iklanpriangan->nama_jenis ?? 'Iklan TV',
                'total'       => (int) $t->totaltagihan_transaksipriangan,
                'bayar'       => (int) $t->jumlahbayar_transaksipriangan,
                'piutang'     => (int) $t->piutang_transaksipriangan,
                'is_lunas'    => (int) $t->piutang_transaksipriangan <= 0,
                'created_at'  => $t->created_at,
                'route_view'  => route('transaksipriangan.index'),
                'route_cetak' => route('transaksipriangan.cetak', $t->id),
            ]);
        }

        $recentStream = $recentTransactions->sortByDesc('created_at')->take(8)->values();

        // 8. Trend Kas Masuk 7 Hari Terakhir (Per Media Saluran untuk Chart.js)
        $dailyLabels = [];
        $dailyKoran = [];
        $dailyOnline = [];
        $dailyTv = [];

        for ($i = 6; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i)->toDateString();
            $dailyLabels[] = Carbon::parse($d)->translatedFormat('d M');
            $dailyKoran[] = (int) TransaksiKoran::whereDate('tanggal_transaksikoran', $d)->sum('jumlahbayar_transaksikoran');
            $dailyOnline[] = (int) TransaksiOnline::whereDate('tanggal_transaksionline', $d)->sum('jumlahbayar_transaksionline');
            $dailyTv[] = (int) TransaksiPriangan::whereDate('tanggal_transaksipriangan', $d)->sum('jumlahbayar_transaksipriangan');
        }

        return view('pages.keuangan.dashboard', compact(
            'range',
            'rangeLabel',
            'rangeTitle',
            'rangeSub',
            'dari',
            'sampai',
            'totalTagihan',
            'totalPenerimaan',
            'totalPiutangPeriode',
            'totalPiutangKumulatif',
            'totalTransaksi',
            'collectionRate',
            'invoicePendingPeriode',
            'totalInvoicePendingKumulatif',
            'masukHariIni',
            'masukBulanIni',
            'channels',
            'agingLancar',
            'agingLancarCount',
            'agingPerhatian',
            'agingPerhatianCount',
            'agingKritis',
            'agingKritisCount',
            'topUnpaidInvoices',
            'recentStream',
            'dailyLabels',
            'dailyKoran',
            'dailyOnline',
            'dailyTv'
        ));
    }

    /**
     * Halaman Interaktif Alur Kerja Faktur, Inkaso, Anggaran & Buku Kas
     */
    public function alurFakturInkaso()
    {
        return view('pages.keuangan.alur');
    }

    /**
     * Dashboard Divisi Accounting
     */
    public function accounting(Request $request)
    {
        $omsetUsaha = (int) (
            TransaksiKoran::sum('totaltagihan_transaksikoran') +
            TransaksiOnline::sum('totaltagihan_transaksionline') +
            TransaksiPriangan::sum('totaltagihan_transaksipriangan')
        );

        $penerimaanKas = (int) (
            TransaksiKoran::sum('jumlahbayar_transaksikoran') +
            TransaksiOnline::sum('jumlahbayar_transaksionline') +
            TransaksiPriangan::sum('jumlahbayar_transaksipriangan')
        );

        $piutangUsaha = (int) (
            TransaksiKoran::sum('piutang_transaksikoran') +
            TransaksiOnline::sum('piutang_transaksionline') +
            TransaksiPriangan::sum('piutang_transaksipriangan')
        );

        $estimasiBeban = (int) ($omsetUsaha * 0.45);
        $estimasiLabaBersih = max(0, $omsetUsaha - $estimasiBeban);

        return view('pages.accounting.dashboard', compact(
            'omsetUsaha',
            'penerimaanKas',
            'piutangUsaha',
            'estimasiBeban',
            'estimasiLabaBersih'
        ));
    }

    /**
     * Dashboard Divisi Sirkulasi
     */
    public function sirkulasi(Request $request)
    {
        $oplahHarian = 12500;
        $wilayah = [
            ['nama' => 'Kota & Kab. Tasikmalaya', 'oplah' => 5200, 'agen' => 32, 'retur_pct' => 1.8],
            ['nama' => 'Kabupaten Ciamis', 'oplah' => 2800, 'agen' => 18, 'retur_pct' => 2.1],
            ['nama' => 'Kota Banjar', 'oplah' => 1400, 'agen' => 10, 'retur_pct' => 1.5],
            ['nama' => 'Kabupaten Garut', 'oplah' => 1900, 'agen' => 12, 'retur_pct' => 2.6],
            ['nama' => 'Kabupaten Pangandaran', 'oplah' => 1200, 'agen' => 8, 'retur_pct' => 2.0],
        ];

        $totalAgen = 80;
        $totalRetur = 275;
        $efektivitasDistribusi = 97.8;

        return view('pages.sirkulasi.dashboard', compact(
            'oplahHarian',
            'wilayah',
            'totalAgen',
            'totalRetur',
            'efektivitasDistribusi'
        ));
    }

    /**
     * Dashboard Divisi Kasir
     */
    public function kasir(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $bayarHariIni = (int) (
            TransaksiKoran::whereDate('tanggal_transaksikoran', $today)->sum('jumlahbayar_transaksikoran') +
            TransaksiOnline::whereDate('tanggal_transaksionline', $today)->sum('jumlahbayar_transaksionline') +
            TransaksiPriangan::whereDate('tanggal_transaksipriangan', $today)->sum('jumlahbayar_transaksipriangan')
        );

        $txHariIni = (int) (
            TransaksiKoran::whereDate('tanggal_transaksikoran', $today)->count() +
            TransaksiOnline::whereDate('tanggal_transaksionline', $today)->count() +
            TransaksiPriangan::whereDate('tanggal_transaksipriangan', $today)->count()
        );

        $totalPenerimaanBulan = (int) (
            TransaksiKoran::whereMonth('tanggal_transaksikoran', Carbon::now()->month)->sum('jumlahbayar_transaksikoran') +
            TransaksiOnline::whereMonth('tanggal_transaksionline', Carbon::now()->month)->sum('jumlahbayar_transaksionline') +
            TransaksiPriangan::whereMonth('tanggal_transaksipriangan', Carbon::now()->month)->sum('jumlahbayar_transaksipriangan')
        );

        $recentKasir = new Collection();
        $koran = TransaksiKoran::latest('id')->take(3)->get();
        foreach ($koran as $k) {
            $recentKasir->push((object)[
                'faktur'   => $k->nofakturkoran,
                'media'    => 'Koran Cetak',
                'customer' => $k->nama_pemasangkoran,
                'bayar'    => (int) $k->jumlahbayar_transaksikoran,
                'sisa'     => (int) $k->piutang_transaksikoran,
                'tanggal'  => $k->tanggal_transaksikoran,
            ]);
        }
        $online = TransaksiOnline::latest('id')->take(3)->get();
        foreach ($online as $o) {
            $recentKasir->push((object)[
                'faktur'   => $o->nofakturonline,
                'media'    => 'Media Online',
                'customer' => $o->nama_pemasangonline,
                'bayar'    => (int) $o->jumlahbayar_transaksionline,
                'sisa'     => (int) $o->piutang_transaksionline,
                'tanggal'  => $o->tanggal_transaksionline,
            ]);
        }

        return view('pages.kasir.dashboard', compact(
            'bayarHariIni',
            'txHariIni',
            'totalPenerimaanBulan',
            'recentKasir'
        ));
    }
}
