<?php

namespace App\Http\Controllers;

use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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

        // 1. Ambil Transaksi Koran
        if (in_array($media, ['all', 'koran'])) {
            $qKoran = TransaksiKoran::with('iklankoran')
                ->whereBetween('tanggal_transaksikoran', [$dari, $sampai]);

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
                    'customer'      => $row->nama_pemasangkoran,
                    'alamat'        => $row->alamat_pemasangkoran,
                    'paket'         => $row->iklankoran->nama_jenis ?? '-',
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
            $qOnline = TransaksiOnline::with('iklanonline')
                ->whereBetween('tanggal_transaksionline', [$dari, $sampai]);

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
                    'customer'      => $row->nama_pemasangonline,
                    'alamat'        => $row->alamat_pemasangonline,
                    'paket'         => $row->iklanonline->nama_jenis ?? '-',
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
            $qTv = TransaksiPriangan::with('iklanpriangan')
                ->whereBetween('tanggal_transaksipriangan', [$dari, $sampai]);

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
                    'customer'      => $row->nama_pemasangpriangan,
                    'alamat'        => $row->alamat_pemasangpriangan,
                    'paket'         => $row->iklanpriangan->nama_jenis ?? '-',
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
