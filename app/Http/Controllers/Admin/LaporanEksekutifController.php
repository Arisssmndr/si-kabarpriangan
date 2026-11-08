<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesAe;
use App\Models\SistemConfig;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class LaporanEksekutifController extends Controller
{
    /**
     * Dashboard Laporan Eksekutif Konsolidasi Lintas Divisi (CEO / Direksi Level)
     */
    public function index(Request $request)
    {
        $data = $this->prepareReportData($request);
        return view('pages.admin.laporan.index', $data);
    }

    /**
     * Lembar Cetak Laporan Eksekutif Resmi Direksi (Clean Print Document)
     */
    public function cetak(Request $request)
    {
        $data = $this->prepareReportData($request);
        return view('pages.admin.laporan.cetak', $data);
    }

    /**
     * Helper agregasi data konsolidasi bisnis terpadu
     */
    protected function prepareReportData(Request $request): array
    {
        $now = Carbon::now();
        $periode = $request->input('periode', 'bulan_ini');
        $tahun = (int) $request->input('tahun', $now->year);

        // 1. Tentukan rentang tanggal analisa
        switch ($periode) {
            case 'hari_ini':
                $startDate = $now->copy()->startOfDay()->toDateString();
                $endDate = $now->copy()->endOfDay()->toDateString();
                $periodeLabel = 'Hari Ini (' . $now->translatedFormat('d F Y') . ')';
                break;
            case 'bulan_lalu':
                $prevMonth = $now->copy()->subMonth();
                $startDate = $prevMonth->copy()->startOfMonth()->toDateString();
                $endDate = $prevMonth->copy()->endOfMonth()->toDateString();
                $periodeLabel = 'Bulan Lalu (' . $prevMonth->translatedFormat('F Y') . ')';
                break;
            case 'tahun_ini':
                $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfYear()->toDateString();
                $endDate = Carbon::createFromDate($tahun, 12, 31)->endOfYear()->toDateString();
                $periodeLabel = 'Tahun Buku ' . $tahun;
                break;
            case 'custom':
                $startDate = $request->input('tgl_mulai', $now->copy()->startOfMonth()->toDateString());
                $endDate = $request->input('tgl_selesai', $now->toDateString());
                $periodeLabel = Carbon::parse($startDate)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($endDate)->translatedFormat('d M Y');
                break;
            case 'bulan_ini':
            default:
                $startDate = $now->copy()->startOfMonth()->toDateString();
                $endDate = $now->copy()->endOfMonth()->toDateString();
                $periodeLabel = 'Bulan Ini (' . $now->translatedFormat('F Y') . ')';
                $periode = 'bulan_ini';
                break;
        }

        // 2. Query Transaksi Berdasarkan Saluran Media
        // A. Koran Cetak (tidak termasuk order Batal)
        $qKoran = TransaksiKoran::whereBetween('tanggal_transaksikoran', [$startDate, $endDate])
            ->where('status_order', '!=', 'Batal');
        $omsetKoran = (int) $qKoran->sum('totaltagihan_transaksikoran');
        $bayarKoran = (int) $qKoran->sum('jumlahbayar_transaksikoran');
        $piutangKoran = (int) $qKoran->sum('piutang_transaksikoran');
        $txKoran = (int) $qKoran->count();

        // B. Media Online
        $qOnline = TransaksiOnline::whereBetween('tanggal_transaksionline', [$startDate, $endDate]);
        $omsetOnline = (int) $qOnline->sum('totaltagihan_transaksionline');
        $bayarOnline = (int) $qOnline->sum('jumlahbayar_transaksionline');
        $piutangOnline = (int) $qOnline->sum('piutang_transaksionline');
        $txOnline = (int) $qOnline->count();

        // C. Priangan TV
        $qTv = TransaksiPriangan::whereBetween('tanggal_transaksipriangan', [$startDate, $endDate]);
        $omsetTv = (int) $qTv->sum('totaltagihan_transaksipriangan');
        $bayarTv = (int) $qTv->sum('jumlahbayar_transaksipriangan');
        $piutangTv = (int) $qTv->sum('piutang_transaksipriangan');
        $txTv = (int) $qTv->count();

        // Total Konsolidasi
        $totalOmset = $omsetKoran + $omsetOnline + $omsetTv;
        $totalBayar = $bayarKoran + $bayarOnline + $bayarTv;
        $totalPiutang = $piutangKoran + $piutangOnline + $piutangTv;
        $totalTransaksi = $txKoran + $txOnline + $txTv;

        $collectionRate = $totalOmset > 0 ? round(($totalBayar / $totalOmset) * 100, 1) : 0;

        // Persentase Kontribusi Omset per Saluran Media
        $pctKoran = $totalOmset > 0 ? round(($omsetKoran / $totalOmset) * 100, 1) : 0;
        $pctOnline = $totalOmset > 0 ? round(($omsetOnline / $totalOmset) * 100, 1) : 0;
        $pctTv = $totalOmset > 0 ? round(($omsetTv / $totalOmset) * 100, 1) : 0;

        // 3. Analisa Komparasi MoM (Bulan Ini vs Bulan Lalu)
        $startBulanIni = $now->copy()->startOfMonth()->toDateString();
        $endBulanIni = $now->copy()->endOfMonth()->toDateString();
        $omsetBulanIni = (int) (
            TransaksiKoran::whereBetween('tanggal_transaksikoran', [$startBulanIni, $endBulanIni])->where('status_order', '!=', 'Batal')->sum('totaltagihan_transaksikoran') +
            TransaksiOnline::whereBetween('tanggal_transaksionline', [$startBulanIni, $endBulanIni])->sum('totaltagihan_transaksionline') +
            TransaksiPriangan::whereBetween('tanggal_transaksipriangan', [$startBulanIni, $endBulanIni])->sum('totaltagihan_transaksipriangan')
        );

        $startBulanLalu = $now->copy()->subMonth()->startOfMonth()->toDateString();
        $endBulanLalu = $now->copy()->subMonth()->endOfMonth()->toDateString();
        $omsetBulanLalu = (int) (
            TransaksiKoran::whereBetween('tanggal_transaksikoran', [$startBulanLalu, $endBulanLalu])->where('status_order', '!=', 'Batal')->sum('totaltagihan_transaksikoran') +
            TransaksiOnline::whereBetween('tanggal_transaksionline', [$startBulanLalu, $endBulanLalu])->sum('totaltagihan_transaksionline') +
            TransaksiPriangan::whereBetween('tanggal_transaksipriangan', [$startBulanLalu, $endBulanLalu])->sum('totaltagihan_transaksipriangan')
        );

        $growthNominal = $omsetBulanIni - $omsetBulanLalu;
        $growthPct = $omsetBulanLalu > 0 ? round(($growthNominal / $omsetBulanLalu) * 100, 1) : ($omsetBulanIni > 0 ? 100 : 0);

        // 4. Trend 12 Bulan (Januari - Desember Tahun Berjalan) untuk Grafik Visual
        $trendLabels = [];
        $trendKoran = [];
        $trendOnline = [];
        $trendTv = [];
        $trendTotal = [];
        $trendBayar = [];
        $trendPiutang = [];

        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($m = 1; $m <= 12; $m++) {
            $trendLabels[] = $namaBulan[$m - 1];

            $mStart = Carbon::createFromDate($tahun, $m, 1)->startOfMonth()->toDateString();
            $mEnd = Carbon::createFromDate($tahun, $m, 1)->endOfMonth()->toDateString();

            $kVal = (int) TransaksiKoran::whereBetween('tanggal_transaksikoran', [$mStart, $mEnd])
                ->where('status_order', '!=', 'Batal')
                ->sum('totaltagihan_transaksikoran');

            $oVal = (int) TransaksiOnline::whereBetween('tanggal_transaksionline', [$mStart, $mEnd])
                ->sum('totaltagihan_transaksionline');

            $tVal = (int) TransaksiPriangan::whereBetween('tanggal_transaksipriangan', [$mStart, $mEnd])
                ->sum('totaltagihan_transaksipriangan');

            $totVal = $kVal + $oVal + $tVal;

            $bVal = (int) (
                TransaksiKoran::whereBetween('tanggal_transaksikoran', [$mStart, $mEnd])->where('status_order', '!=', 'Batal')->sum('jumlahbayar_transaksikoran') +
                TransaksiOnline::whereBetween('tanggal_transaksionline', [$mStart, $mEnd])->sum('jumlahbayar_transaksionline') +
                TransaksiPriangan::whereBetween('tanggal_transaksipriangan', [$mStart, $mEnd])->sum('jumlahbayar_transaksipriangan')
            );

            $pVal = (int) (
                TransaksiKoran::whereBetween('tanggal_transaksikoran', [$mStart, $mEnd])->where('status_order', '!=', 'Batal')->sum('piutang_transaksikoran') +
                TransaksiOnline::whereBetween('tanggal_transaksionline', [$mStart, $mEnd])->sum('piutang_transaksionline') +
                TransaksiPriangan::whereBetween('tanggal_transaksipriangan', [$mStart, $mEnd])->sum('piutang_transaksipriangan')
            );

            $trendKoran[] = $kVal;
            $trendOnline[] = $oVal;
            $trendTv[] = $tVal;
            $trendTotal[] = $totVal;
            $trendBayar[] = $bVal;
            $trendPiutang[] = $pVal;
        }

        // 5. Top 10 Pemasang Iklan Terbesar (Akumulasi 3 Saluran di Periode Terpilih)
        $pemasangList = [];

        $transKoran = TransaksiKoran::whereBetween('tanggal_transaksikoran', [$startDate, $endDate])
            ->where('status_order', '!=', 'Batal')
            ->select('nama_pemasangkoran as nama', 'totaltagihan_transaksikoran as tagihan', 'jumlahbayar_transaksikoran as bayar', 'piutang_transaksikoran as piutang')
            ->get();
        foreach ($transKoran as $item) {
            $key = strtoupper(trim((string)$item->nama));
            if (!$key) continue;
            if (!isset($pemasangList[$key])) {
                $pemasangList[$key] = ['nama' => $item->nama, 'omset' => 0, 'bayar' => 0, 'piutang' => 0, 'order_count' => 0];
            }
            $pemasangList[$key]['omset'] += (int) $item->tagihan;
            $pemasangList[$key]['bayar'] += (int) $item->bayar;
            $pemasangList[$key]['piutang'] += (int) $item->piutang;
            $pemasangList[$key]['order_count'] += 1;
        }

        $transOnline = TransaksiOnline::whereBetween('tanggal_transaksionline', [$startDate, $endDate])
            ->select('nama_pemasangonline as nama', 'totaltagihan_transaksionline as tagihan', 'jumlahbayar_transaksionline as bayar', 'piutang_transaksionline as piutang')
            ->get();
        foreach ($transOnline as $item) {
            $key = strtoupper(trim((string)$item->nama));
            if (!$key) continue;
            if (!isset($pemasangList[$key])) {
                $pemasangList[$key] = ['nama' => $item->nama, 'omset' => 0, 'bayar' => 0, 'piutang' => 0, 'order_count' => 0];
            }
            $pemasangList[$key]['omset'] += (int) $item->tagihan;
            $pemasangList[$key]['bayar'] += (int) $item->bayar;
            $pemasangList[$key]['piutang'] += (int) $item->piutang;
            $pemasangList[$key]['order_count'] += 1;
        }

        $transTv = TransaksiPriangan::whereBetween('tanggal_transaksipriangan', [$startDate, $endDate])
            ->select('nama_pemasangpriangan as nama', 'totaltagihan_transaksipriangan as tagihan', 'jumlahbayar_transaksipriangan as bayar', 'piutang_transaksipriangan as piutang')
            ->get();
        foreach ($transTv as $item) {
            $key = strtoupper(trim((string)$item->nama));
            if (!$key) continue;
            if (!isset($pemasangList[$key])) {
                $pemasangList[$key] = ['nama' => $item->nama, 'omset' => 0, 'bayar' => 0, 'piutang' => 0, 'order_count' => 0];
            }
            $pemasangList[$key]['omset'] += (int) $item->tagihan;
            $pemasangList[$key]['bayar'] += (int) $item->bayar;
            $pemasangList[$key]['piutang'] += (int) $item->piutang;
            $pemasangList[$key]['order_count'] += 1;
        }

        // Sort Top 10 Pemasang
        $topPemasang = collect($pemasangList)->sortByDesc('omset')->take(10)->values();

        // 6. Ranking Performa Sales / Account Executive
        $salesList = [];
        $allAe = SalesAe::where('is_active', true)->get();
        foreach ($allAe as $ae) {
            $salesList[$ae->id] = [
                'id'          => $ae->id,
                'kode'        => $ae->kode_ae,
                'nama'        => $ae->nama_ae,
                'omset'       => 0,
                'bayar'       => 0,
                'piutang'     => 0,
                'order_count' => 0,
            ];
        }

        // Hitung kontribusi sales dari Transaksi Koran
        $salesKoran = TransaksiKoran::whereBetween('tanggal_transaksikoran', [$startDate, $endDate])
            ->where('status_order', '!=', 'Batal')
            ->whereNotNull('sales_ae_id')
            ->selectRaw('sales_ae_id, SUM(totaltagihan_transaksikoran) as omset, SUM(jumlahbayar_transaksikoran) as bayar, SUM(piutang_transaksikoran) as piutang, COUNT(*) as cnt')
            ->groupBy('sales_ae_id')
            ->get();

        foreach ($salesKoran as $sk) {
            if (isset($salesList[$sk->sales_ae_id])) {
                $salesList[$sk->sales_ae_id]['omset'] += (int) $sk->omset;
                $salesList[$sk->sales_ae_id]['bayar'] += (int) $sk->bayar;
                $salesList[$sk->sales_ae_id]['piutang'] += (int) $sk->piutang;
                $salesList[$sk->sales_ae_id]['order_count'] += (int) $sk->cnt;
            }
        }

        // Hitung kontribusi sales dari Transaksi Online
        $salesOnline = TransaksiOnline::whereBetween('tanggal_transaksionline', [$startDate, $endDate])
            ->whereNotNull('sales_ae_id')
            ->selectRaw('sales_ae_id, SUM(totaltagihan_transaksionline) as omset, SUM(jumlahbayar_transaksionline) as bayar, SUM(piutang_transaksionline) as piutang, COUNT(*) as cnt')
            ->groupBy('sales_ae_id')
            ->get();

        foreach ($salesOnline as $so) {
            if (isset($salesList[$so->sales_ae_id])) {
                $salesList[$so->sales_ae_id]['omset'] += (int) $so->omset;
                $salesList[$so->sales_ae_id]['bayar'] += (int) $so->bayar;
                $salesList[$so->sales_ae_id]['piutang'] += (int) $so->piutang;
                $salesList[$so->sales_ae_id]['order_count'] += (int) $so->cnt;
            }
        }

        $rankingSales = collect($salesList)->sortByDesc('omset')->values();

        // 7. Ambil Identitas Perusahaan dari SistemConfig
        $namaPerusahaan = SistemConfig::get('nama_perusahaan', 'PT Berkah Pikiran Rakyat');
        $namaMedia = SistemConfig::get('nama_media', 'Harian Umum Kabar Priangan');
        $alamatPerusahaan = SistemConfig::get('alamat_perusahaan', 'Jl. Dr. Sukardjo No. 47, Tasikmalaya');
        $kotaTerbit = SistemConfig::get('kota_terbit', 'Tasikmalaya');

        return compact(
            'periode',
            'periodeLabel',
            'tahun',
            'startDate',
            'endDate',
            'omsetKoran',
            'bayarKoran',
            'piutangKoran',
            'txKoran',
            'omsetOnline',
            'bayarOnline',
            'piutangOnline',
            'txOnline',
            'omsetTv',
            'bayarTv',
            'piutangTv',
            'txTv',
            'totalOmset',
            'totalBayar',
            'totalPiutang',
            'totalTransaksi',
            'collectionRate',
            'pctKoran',
            'pctOnline',
            'pctTv',
            'omsetBulanIni',
            'omsetBulanLalu',
            'growthNominal',
            'growthPct',
            'trendLabels',
            'trendKoran',
            'trendOnline',
            'trendTv',
            'trendTotal',
            'trendBayar',
            'trendPiutang',
            'topPemasang',
            'rankingSales',
            'namaPerusahaan',
            'namaMedia',
            'alamatPerusahaan',
            'kotaTerbit'
        );
    }
}
