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
     * Tampilkan halaman pusat Laporan Keuangan Terpadu (Single Report Center)
     */
    public function index(Request $request)
    {
        $filterData = $this->resolveReportData($request);

        return view('pages.iklan.laporan.index', $filterData);
    }

    /**
     * Cetak dokumen resmi Laporan Iklan (Print Friendly View)
     */
    public function cetak(Request $request)
    {
        $filterData = $this->resolveReportData($request);

        return view('pages.iklan.laporan.cetak', $filterData);
    }

    /**
     * Laporan Rekap Sales / AE per Tanggal Muat & per Bulan (Warisan IKL.EXE)
     */
    public function rekapSales(Request $request)
    {
        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));
        $salesAeId = $request->input('sales_ae_id', 'all');

        $salesAes = SalesAe::where('is_active', true)->orderBy('kode_ae')->get();

        $query = TransaksiKoran::with(['salesAe', 'iklankoran', 'pelanggan'])
            ->whereYear('tanggal_transaksikoran', $tahun)
            ->whereMonth('tanggal_transaksikoran', $bulan)
            ->where('status_order', '!=', 'Batal');

        if ($salesAeId !== 'all' && $salesAeId !== '') {
            $query->where('sales_ae_id', $salesAeId);
        }

        $transaksiList = $query->orderBy('tanggal_transaksikoran')->get();

        // Agregasi per Sales AE
        $rekapPerAe = [];
        foreach ($salesAes as $ae) {
            $itemsAe = $transaksiList->where('sales_ae_id', $ae->id);
            $rekapPerAe[$ae->id] = [
                'ae'            => $ae,
                'count'         => $itemsAe->count(),
                'total_bruto'   => $itemsAe->sum('harga_transaksikoran'),
                'total_tagihan' => $itemsAe->sum('totaltagihan_transaksikoran'),
                'total_bayar'   => $itemsAe->sum('jumlahbayar_transaksikoran'),
                'total_piutang' => $itemsAe->sum('piutang_transaksikoran'),
                'total_insentif'=> $itemsAe->sum('insentif_transaksikoran'),
                'total_komisi'  => $itemsAe->sum('komisi_transaksikoran'),
            ];
        }

        $grandTotal = [
            'count'         => $transaksiList->count(),
            'total_tagihan' => $transaksiList->sum('totaltagihan_transaksikoran'),
            'total_bayar'   => $transaksiList->sum('jumlahbayar_transaksikoran'),
            'total_piutang' => $transaksiList->sum('piutang_transaksikoran'),
            'total_insentif'=> $transaksiList->sum('insentif_transaksikoran'),
            'total_komisi'  => $transaksiList->sum('komisi_transaksikoran'),
        ];

        return view('pages.iklan.laporan.rekap_sales', compact(
            'bulan', 'tahun', 'salesAeId', 'salesAes', 'rekapPerAe', 'transaksiList', 'grandTotal'
        ));
    }

    /**
     * Laporan Insentif & Komisi AE (Slip Insentif Bulanan)
     */
    public function insentifAe(Request $request)
    {
        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));

        $salesAes = SalesAe::where('is_active', true)->where('kode_ae', '!=', 'FO')->orderBy('kode_ae')->get();

        $dataInsentif = [];
        foreach ($salesAes as $ae) {
            $transaksis = TransaksiKoran::where('sales_ae_id', $ae->id)
                ->whereYear('tanggal_transaksikoran', $tahun)
                ->whereMonth('tanggal_transaksikoran', $bulan)
                ->where('status_order', '!=', 'Batal')
                ->get();

            $omzetNetto = $transaksis->sum('totaltagihan_transaksikoran');
            $lunas = $transaksis->where('piutang_transaksikoran', '<=', 0)->sum('totaltagihan_transaksikoran');
            $belumLunas = $transaksis->where('piutang_transaksikoran', '>', 0)->sum('totaltagihan_transaksikoran');

            $insentifTercatat = $transaksis->sum('insentif_transaksikoran');
            $komisiTercatat   = $transaksis->sum('komisi_transaksikoran');

            $dataInsentif[] = [
                'ae'                => $ae,
                'total_order'       => $transaksis->count(),
                'omzet_total'       => $omzetNetto,
                'omzet_lunas'       => $lunas,
                'omzet_piutang'     => $belumLunas,
                'insentif_nominal'  => $insentifTercatat,
                'komisi_nominal'    => $komisiTercatat,
                'total_hak_sales'   => $insentifTercatat + $komisiTercatat,
            ];
        }

        return view('pages.iklan.laporan.insentif_ae', compact('bulan', 'tahun', 'dataInsentif'));
    }

    /**
     * Laporan Klasifikasi Produk Iklan per Bulan (Baris vs Kolom vs Display vs Advertorial)
     */
    public function klasifikasi(Request $request)
    {
        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));

        $kategoriList = ['BARIS', 'KOLOM', 'DISPLAY', 'ADVERTORIAL', 'BONUS'];
        $rekapKlasifikasi = [];
        $totalSemua = 0;

        foreach ($kategoriList as $kat) {
            $orders = TransaksiKoran::where('kategori_form', $kat)
                ->whereYear('tanggal_transaksikoran', $tahun)
                ->whereMonth('tanggal_transaksikoran', $bulan)
                ->where('status_order', '!=', 'Batal')
                ->get();

            $nominal = $orders->sum('totaltagihan_transaksikoran');
            $totalSemua += $nominal;

            $rekapKlasifikasi[$kat] = [
                'nama'    => $kat,
                'count'   => $orders->count(),
                'nominal' => $nominal,
            ];
        }

        return view('pages.iklan.laporan.klasifikasi', compact('bulan', 'tahun', 'rekapKlasifikasi', 'totalSemua'));
    }

    /**
     * Laporan Pembatalan Iklan & Audit Trail
     */
    public function pembatalan(Request $request)
    {
        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));

        $pembatalanList = TransaksiKoran::with(['iklankoran', 'pelanggan', 'dibatalkanOleh'])
            ->where('status_order', 'Batal')
            ->whereYear('tanggal_transaksikoran', $tahun)
            ->whereMonth('tanggal_transaksikoran', $bulan)
            ->latest('dibatalkan_at')
            ->get();

        return view('pages.iklan.laporan.pembatalan', compact('bulan', 'tahun', 'pembatalanList'));
    }

    /**
     * Laporan Aging Piutang Iklan (0-30, 31-60, 61-90, >90 hari) Lintas Seluruh Media
     */
    public function agingPiutang(Request $request)
    {
        $now = Carbon::now();
        $agingBuckets = [
            '0_30'  => ['label' => '0 - 30 Hari',   'items' => [], 'total' => 0],
            '31_60' => ['label' => '31 - 60 Hari',  'items' => [], 'total' => 0],
            '61_90' => ['label' => '61 - 90 Hari',  'items' => [], 'total' => 0],
            'gt_90' => ['label' => '> 90 Hari (Macet)', 'items' => [], 'total' => 0],
        ];

        // 1. Piutang Koran
        $piutangKoran = TransaksiKoran::with(['pelanggan', 'salesAe'])
            ->where('piutang_transaksikoran', '>', 0)
            ->where('status_order', '!=', 'Batal')
            ->get();

        foreach ($piutangKoran as $row) {
            $tgl = Carbon::parse($row->tanggal_transaksikoran);
            $days = $tgl->diffInDays($now);
            $sisa = (int) $row->piutang_transaksikoran;

            $itemData = [
                'faktur'        => $row->nofakturkoran,
                'media'         => 'Koran',
                'tanggal'       => $tgl->format('d/m/Y'),
                'pelanggan'     => $row->pelanggan?->nama_instansi ?: $row->nama_pemasangkoran,
                'sales'         => $row->sales_iklankoran ?? '-',
                'total_tagihan' => $row->totaltagihan_transaksikoran,
                'piutang'       => $sisa,
                'umur_hari'     => $days,
            ];

            if ($days <= 30) {
                $agingBuckets['0_30']['items'][] = $itemData;
                $agingBuckets['0_30']['total'] += $sisa;
            } elseif ($days <= 60) {
                $agingBuckets['31_60']['items'][] = $itemData;
                $agingBuckets['31_60']['total'] += $sisa;
            } elseif ($days <= 90) {
                $agingBuckets['61_90']['items'][] = $itemData;
                $agingBuckets['61_90']['total'] += $sisa;
            } else {
                $agingBuckets['gt_90']['items'][] = $itemData;
                $agingBuckets['gt_90']['total'] += $sisa;
            }
        }

        // 2. Piutang Online
        $piutangOnline = TransaksiOnline::with(['pelanggan', 'salesAe'])
            ->where('piutang_transaksionline', '>', 0)
            ->get();

        foreach ($piutangOnline as $row) {
            $tgl = Carbon::parse($row->tanggal_transaksionline);
            $days = $tgl->diffInDays($now);
            $sisa = (int) $row->piutang_transaksionline;

            $itemData = [
                'faktur'        => $row->nofakturonline,
                'media'         => 'Online',
                'tanggal'       => $tgl->format('d/m/Y'),
                'pelanggan'     => $row->pelanggan?->nama_instansi ?: $row->nama_pemasangonline,
                'sales'         => $row->sales_iklanonline ?? '-',
                'total_tagihan' => $row->totaltagihan_transaksionline,
                'piutang'       => $sisa,
                'umur_hari'     => $days,
            ];

            if ($days <= 30) {
                $agingBuckets['0_30']['items'][] = $itemData;
                $agingBuckets['0_30']['total'] += $sisa;
            } elseif ($days <= 60) {
                $agingBuckets['31_60']['items'][] = $itemData;
                $agingBuckets['31_60']['total'] += $sisa;
            } elseif ($days <= 90) {
                $agingBuckets['61_90']['items'][] = $itemData;
                $agingBuckets['61_90']['total'] += $sisa;
            } else {
                $agingBuckets['gt_90']['items'][] = $itemData;
                $agingBuckets['gt_90']['total'] += $sisa;
            }
        }

        // 3. Piutang Priangan TV
        $piutangTv = TransaksiPriangan::with(['pelanggan', 'salesAe'])
            ->where('piutang_transaksipriangan', '>', 0)
            ->get();

        foreach ($piutangTv as $row) {
            $tgl = Carbon::parse($row->tanggal_transaksipriangan);
            $days = $tgl->diffInDays($now);
            $sisa = (int) $row->piutang_transaksipriangan;

            $itemData = [
                'faktur'        => $row->nofakturpriangan,
                'media'         => 'Priangan TV',
                'tanggal'       => $tgl->format('d/m/Y'),
                'pelanggan'     => $row->pelanggan?->nama_instansi ?: $row->nama_pemasangpriangan,
                'sales'         => $row->sales_iklanpriangan ?? '-',
                'total_tagihan' => $row->totaltagihan_transaksipriangan,
                'piutang'       => $sisa,
                'umur_hari'     => $days,
            ];

            if ($days <= 30) {
                $agingBuckets['0_30']['items'][] = $itemData;
                $agingBuckets['0_30']['total'] += $sisa;
            } elseif ($days <= 60) {
                $agingBuckets['31_60']['items'][] = $itemData;
                $agingBuckets['31_60']['total'] += $sisa;
            } elseif ($days <= 90) {
                $agingBuckets['61_90']['items'][] = $itemData;
                $agingBuckets['61_90']['total'] += $sisa;
            } else {
                $agingBuckets['gt_90']['items'][] = $itemData;
                $agingBuckets['gt_90']['total'] += $sisa;
            }
        }

        $totalPiutangKeseluruhan = array_sum(array_column($agingBuckets, 'total'));

        return view('pages.iklan.laporan.aging_piutang', compact('agingBuckets', 'totalPiutangKeseluruhan'));
    }

    /**
     * Helper privat untuk mengolah filter, agregasi, dan penggabungan dataset transaksi
     */
    private function resolveReportData(Request $request): array
    {
        $period = $request->input('period', 'month');
        $media  = $request->input('media', 'all'); // 'all', 'koran', 'online', 'tv'
        $status = $request->input('status', 'all'); // 'all', 'lunas', 'piutang'

        $now = Carbon::now();

        // Resolusi Tanggal berdasarkan Preset
        if ($period === 'today') {
            $dari = $now->toDateString();
            $sampai = $now->toDateString();
            $periodTitle = 'Hari Ini';
            $periodSub = $now->translatedFormat('d M Y');
            $periodLabel = 'Hari Ini (' . $now->translatedFormat('d M Y') . ')';
        } elseif ($period === '7days' || $period === 'week') {
            $period = '7days';
            $dari = $now->copy()->subDays(6)->toDateString();
            $sampai = $now->toDateString();
            $periodTitle = '7 Hari Terakhir';
            $periodSub = Carbon::parse($dari)->translatedFormat('d M') . ' - ' . $now->translatedFormat('d M Y');
            $periodLabel = '7 Hari Terakhir (' . $periodSub . ')';
        } elseif ($period === '30days') {
            $dari = $now->copy()->subDays(29)->toDateString();
            $sampai = $now->toDateString();
            $periodTitle = '30 Hari Terakhir';
            $periodSub = Carbon::parse($dari)->translatedFormat('d M') . ' - ' . $now->translatedFormat('d M Y');
            $periodLabel = '30 Hari Terakhir (' . $periodSub . ')';
        } elseif ($period === 'month') {
            $dari = $now->copy()->startOfMonth()->toDateString();
            $sampai = $now->copy()->endOfMonth()->toDateString();
            $periodTitle = 'Bulan Ini';
            $periodSub = $now->translatedFormat('F Y');
            $periodLabel = 'Bulan Ini (' . $now->translatedFormat('F Y') . ')';
        } elseif ($period === 'last_month') {
            $subMonth = $now->copy()->subMonth();
            $dari = $subMonth->copy()->startOfMonth()->toDateString();
            $sampai = $subMonth->copy()->endOfMonth()->toDateString();
            $periodTitle = 'Bulan Lalu';
            $periodSub = $subMonth->translatedFormat('F Y');
            $periodLabel = 'Bulan Lalu (' . $subMonth->translatedFormat('F Y') . ')';
        } elseif ($period === 'year') {
            $dari = $now->copy()->startOfYear()->toDateString();
            $sampai = $now->copy()->endOfYear()->toDateString();
            $periodTitle = 'Tahun Ini';
            $periodSub = $now->translatedFormat('Y');
            $periodLabel = 'Tahun Ini (' . $now->translatedFormat('Y') . ')';
        } else {
            // Mode Kustom Tanggal
            $dari = $request->input('dari', $now->copy()->startOfMonth()->toDateString());
            $sampai = $request->input('sampai', $now->toDateString());
            $periodTitle = 'Kustom';
            $periodSub = Carbon::parse($dari)->translatedFormat('d M') . ' - ' . Carbon::parse($sampai)->translatedFormat('d M Y');
            $periodLabel = Carbon::parse($dari)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($sampai)->translatedFormat('d M Y');
            $period = 'custom';
        }

        $items = new Collection();
        $startDate = Carbon::parse($dari)->startOfDay();
        $endDate   = Carbon::parse($sampai)->endOfDay();

        // 1. Ambil Transaksi Koran
        if (in_array($media, ['all', 'koran'])) {
            $qKoran = TransaksiKoran::with(['iklankoran', 'pelanggan'])
                ->whereBetween('tanggal_transaksikoran', [$startDate, $endDate])
                ->where('status_order', '!=', 'Batal');

            if ($status === 'lunas') {
                $qKoran->where('piutang_transaksikoran', '<=', 0);
            } elseif ($status === 'piutang') {
                $qKoran->where('piutang_transaksikoran', '>', 0);
            }

            foreach ($qKoran->get() as $row) {
                $items->push((object)[
                    'id'            => $row->id,
                    'media_type'    => 'koran',
                    'media_code'    => 'KRN',
                    'media_name'    => 'Koran Cetak',
                    'faktur'        => $row->nofakturkoran,
                    'tanggal'       => $row->tanggal_transaksikoran,
                    'customer'      => $row->pelanggan?->nama_instansi ?: $row->nama_pemasangkoran,
                    'alamat'        => $row->alamat_pemasangkoran,
                    'paket'         => $row->iklankoran->nama_jenis ?? ($row->kategori_form ?? 'Display'),
                    'total'         => (int) $row->totaltagihan_transaksikoran,
                    'bayar'         => (int) $row->jumlahbayar_transaksikoran,
                    'piutang'       => (int) $row->piutang_transaksikoran,
                    'is_lunas'      => (int) $row->piutang_transaksikoran <= 0,
                    'sales'         => $row->sales_iklankoran ?? '-',
                    'created_at'    => $row->created_at,
                ]);
            }
        }

        // 2. Ambil Transaksi Online
        if (in_array($media, ['all', 'online'])) {
            $qOnline = TransaksiOnline::with(['iklanonline', 'pelanggan'])
                ->whereBetween('tanggal_transaksionline', [$startDate, $endDate]);

            if ($status === 'lunas') {
                $qOnline->where('piutang_transaksionline', '<=', 0);
            } elseif ($status === 'piutang') {
                $qOnline->where('piutang_transaksionline', '>', 0);
            }

            foreach ($qOnline->get() as $row) {
                $items->push((object)[
                    'id'            => $row->id,
                    'media_type'    => 'online',
                    'media_code'    => 'ONL',
                    'media_name'    => 'Media Online',
                    'faktur'        => $row->nofakturonline,
                    'tanggal'       => $row->tanggal_transaksionline,
                    'customer'      => $row->pelanggan?->nama_instansi ?: $row->nama_pemasangonline,
                    'alamat'        => $row->alamat_pemasangonline,
                    'paket'         => is_array($row->kategori_produk_publikasi) ? implode(', ', $row->kategori_produk_publikasi) : ($row->iklanonline->nama_jenis ?? 'Publikasi Online'),
                    'total'         => (int) $row->totaltagihan_transaksionline,
                    'bayar'         => (int) $row->jumlahbayar_transaksionline,
                    'piutang'       => (int) $row->piutang_transaksionline,
                    'is_lunas'      => (int) $row->piutang_transaksionline <= 0,
                    'sales'         => $row->sales_iklanonline ?? '-',
                    'created_at'    => $row->created_at,
                ]);
            }
        }

        // 3. Ambil Transaksi TV
        if (in_array($media, ['all', 'tv'])) {
            $qTv = TransaksiPriangan::with(['iklanpriangan', 'pelanggan'])
                ->whereBetween('tanggal_transaksipriangan', [$startDate, $endDate]);

            if ($status === 'lunas') {
                $qTv->where('piutang_transaksipriangan', '<=', 0);
            } elseif ($status === 'piutang') {
                $qTv->where('piutang_transaksipriangan', '>', 0);
            }

            foreach ($qTv->get() as $row) {
                $items->push((object)[
                    'id'            => $row->id,
                    'media_type'    => 'tv',
                    'media_code'    => 'PTV',
                    'media_name'    => 'Priangan TV',
                    'faktur'        => $row->nofakturpriangan,
                    'tanggal'       => $row->tanggal_transaksipriangan,
                    'customer'      => $row->pelanggan?->nama_instansi ?: $row->nama_pemasangpriangan,
                    'alamat'        => $row->alamat_pemasangpriangan,
                    'paket'         => $row->iklanpriangan->nama_jenis ?? 'Broadcast TV',
                    'total'         => (int) $row->totaltagihan_transaksipriangan,
                    'bayar'         => (int) $row->jumlahbayar_transaksipriangan,
                    'piutang'       => (int) $row->piutang_transaksipriangan,
                    'is_lunas'      => (int) $row->piutang_transaksipriangan <= 0,
                    'sales'         => $row->sales_iklanpriangan ?? '-',
                    'created_at'    => $row->created_at,
                ]);
            }
        }

        // Urutkan data berdasarkan tanggal transaksi desc
        $sortedItems = $items->sortByDesc('tanggal')->values();

        // Agregasi Finansial
        $totalTransaksi = $sortedItems->count();
        $totalOmset     = $sortedItems->sum('total');
        $totalKasMasuk  = $sortedItems->sum('bayar');
        $totalPiutang   = $sortedItems->sum('piutang');
        $countLunas     = $sortedItems->where('is_lunas', true)->count();
        $countPiutang   = $sortedItems->where('is_lunas', false)->count();

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

        return [
            'items'          => $sortedItems,
            'totalTransaksi' => $totalTransaksi,
            'totalOmset'     => $totalOmset,
            'totalKasMasuk'  => $totalKasMasuk,
            'totalPiutang'   => $totalPiutang,
            'countLunas'     => $countLunas,
            'countPiutang'   => $countPiutang,
            'breakdown'      => $breakdown,
            'period'         => $period,
            'periodTitle'    => $periodTitle,
            'periodSub'      => $periodSub,
            'periodLabel'    => $periodLabel,
            'dari'           => $dari,
            'sampai'         => $sampai,
            'media'          => $media,
            'status'         => $status,
        ];
    }
}
