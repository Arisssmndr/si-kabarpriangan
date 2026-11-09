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
            'iklan'      => redirect()->route('iklan.dashboard'),
            'faktur'     => redirect()->route('keuangan.faktur.iklan.index'),
            'anggaran'   => redirect()->route('keuangan.anggaran.index'),
            'keuangan'   => redirect()->route('keuangan.dashboard'),
            'accounting' => redirect()->route('accounting.dashboard'),
            'sirkulasi'  => redirect()->route('sirkulasi.dashboard'),
            'kasir'      => redirect()->route('kasir.dashboard'),
            default      => redirect()->route('iklan.dashboard'),
        };
    }

    /**
     * Dashboard Divisi Iklan (Koran Cetak, Media Online, Priangan TV)
     */
    public function iklan(Request $request)
    {
        $range = $request->input('range', 'month');
        $now = Carbon::now();
        $todayStr = $now->toDateString();
        $tomorrowStr = $now->copy()->addDay()->toDateString();

        // 1. Rentang waktu berdasarkan filter
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
                $rangeLabel = 'Bulan Ini (' . $rangeSub . ')';
                $range = 'month';
                break;
        }

        $dari = $startDate;
        $sampai = $endDate;

        // 2. Query Transaksi Per Media pada Periode Terpilih
        // A. Iklan Koran (Kecualikan order batal)
        $koranQuery = TransaksiKoran::whereBetween('tanggal_transaksikoran', [$startDate, $endDate])
            ->where('status_order', '!=', 'Batal');
        $omzetKoran = (int) (clone $koranQuery)->sum('totaltagihan_transaksikoran');
        $bayarKoran = (int) (clone $koranQuery)->sum('jumlahbayar_transaksikoran');
        $piutangKoran = (int) (clone $koranQuery)->sum('piutang_transaksikoran');
        $countKoran = (int) (clone $koranQuery)->count();
        $belumLunasKoran = (int) (clone $koranQuery)->where('piutang_transaksikoran', '>', 0)->count();
        $khKoran = (int) (clone $koranQuery)->sum('komisi_transaksikoran');
        $insentifKoran = (int) (clone $koranQuery)->sum('insentif_transaksikoran');

        // B. Iklan Online
        $onlineQuery = TransaksiOnline::whereBetween('tanggal_transaksionline', [$startDate, $endDate]);
        $omzetOnline = (int) (clone $onlineQuery)->sum('totaltagihan_transaksionline');
        $bayarOnline = (int) (clone $onlineQuery)->sum('jumlahbayar_transaksionline');
        $piutangOnline = (int) (clone $onlineQuery)->sum('piutang_transaksionline');
        $countOnline = (int) (clone $onlineQuery)->count();
        $belumLunasOnline = (int) (clone $onlineQuery)->where('piutang_transaksionline', '>', 0)->count();
        $khOnline = (int) (clone $onlineQuery)->sum('komisi_transaksionline');
        $insentifOnline = (int) (clone $onlineQuery)->sum('insentif_transaksionline');

        // C. Iklan TV (Priangan TV)
        $tvQuery = TransaksiPriangan::whereBetween('tanggal_transaksipriangan', [$startDate, $endDate]);
        $omzetTv = (int) (clone $tvQuery)->sum('totaltagihan_transaksipriangan');
        $bayarTv = (int) (clone $tvQuery)->sum('jumlahbayar_transaksipriangan');
        $piutangTv = (int) (clone $tvQuery)->sum('piutang_transaksipriangan');
        $countTv = (int) (clone $tvQuery)->count();
        $belumLunasTv = (int) (clone $tvQuery)->where('piutang_transaksipriangan', '>', 0)->count();
        $khTv = (int) (clone $tvQuery)->sum('komisi_transaksipriangan');
        $insentifTv = (int) (clone $tvQuery)->sum('insentif_transaksipriangan');

        // 3. Agregasi Total Periode & Ringkasan Hari Ini
        $totalOmset = $omzetKoran + $omzetOnline + $omzetTv;
        $totalOmzet = $totalOmset;
        $totalBayar = $bayarKoran + $bayarOnline + $bayarTv;
        $totalPiutang = $piutangKoran + $piutangOnline + $piutangTv;
        $totalTransaksi = $countKoran + $countOnline + $countTv;
        $totalBelumLunas = $belumLunasKoran + $belumLunasOnline + $belumLunasTv;
        $totalKomisiKh = $khKoran + $khOnline + $khTv;
        $totalInsentifSales = $insentifKoran + $insentifOnline + $insentifTv;

        $koranTodayQ = TransaksiKoran::whereDate('tanggal_transaksikoran', $todayStr)->where('status_order', '!=', 'Batal');
        $onlineTodayQ = TransaksiOnline::whereDate('tanggal_transaksionline', $todayStr);
        $tvTodayQ = TransaksiPriangan::whereDate('tanggal_transaksipriangan', $todayStr);

        $penjualanHariIni = (int) (clone $koranTodayQ)->sum('totaltagihan_transaksikoran')
            + (int) (clone $onlineTodayQ)->sum('totaltagihan_transaksionline')
            + (int) (clone $tvTodayQ)->sum('totaltagihan_transaksipriangan');

        $transaksiHariIni = (int) (clone $koranTodayQ)->count()
            + (int) (clone $onlineTodayQ)->count()
            + (int) (clone $tvTodayQ)->count();

        // 4. Ringkasan Setoran Kasir (Kode QR Faktur)
        $qrAktifQuery = \App\Models\FakturQrPembayaran::where('status', \App\Models\FakturQrPembayaran::STATUS_AKTIF);
        $qrDisetorQuery = \App\Models\FakturQrPembayaran::where('status', \App\Models\FakturQrPembayaran::STATUS_DISETOR)
            ->whereBetween(\Illuminate\Support\Facades\DB::raw('DATE(COALESCE(discan_pada, updated_at))'), [$startDate, $endDate]);

        $qrSummary = [
            'menunggu_count'   => (int) (clone $qrAktifQuery)->count(),
            'menunggu_nominal' => (int) round((float) (clone $qrAktifQuery)->sum('nominal_bayar')),
            'disetor_count'    => (int) (clone $qrDisetorQuery)->count(),
            'disetor_nominal'  => (int) round((float) (clone $qrDisetorQuery)->sum('nominal_bayar')),
        ];

        // 5. Ringkasan Jadwal Tayang Hari Ini & Besok + Antrean Produksi
        $countKoranTayangOn = static function (string $dateStr): int {
            return (int) TransaksiKoran::where('status_order', '!=', 'Batal')
                ->where(function ($q) use ($dateStr) {
                    $q->whereDate('tanggal_muatkoran', $dateStr)
                        ->orWhereHas('jadwalMuat', fn($jq) => $jq->whereDate('tanggal_muat', $dateStr));
                })
                ->count();
        };

        $tayangHariIni = [
            'koran'  => $countKoranTayangOn($todayStr),
            'online' => (int) TransaksiOnline::whereDate('tanggal_muatiklanonline', $todayStr)->count(),
            'tv'     => (int) TransaksiPriangan::whereDate('tanggal_muatiklanpriangan', $todayStr)->count(),
        ];
        $tayangHariIni['total'] = $tayangHariIni['koran'] + $tayangHariIni['online'] + $tayangHariIni['tv'];

        $tayangBesok = [
            'koran'  => $countKoranTayangOn($tomorrowStr),
            'online' => (int) TransaksiOnline::whereDate('tanggal_muatiklanonline', $tomorrowStr)->count(),
            'tv'     => (int) TransaksiPriangan::whereDate('tanggal_muatiklanpriangan', $tomorrowStr)->count(),
        ];
        $tayangBesok['total'] = $tayangBesok['koran'] + $tayangBesok['online'] + $tayangBesok['tv'];

        $antreanPracetakKoran = (int) TransaksiKoran::whereIn('status_order', ['Antrean_Pracetak', 'Menunggu_SPPI', 'Menunggu_Proof'])->count();
        $menungguTayangOnline = (int) TransaksiOnline::whereIn('status_produksi', ['menunggu_bahan', 'antrean_redaksi'])->count();
        $terjadwalTv = (int) TransaksiPriangan::whereDate('tanggal_muatiklanpriangan', '>=', $todayStr)->count();

        // Persentase Kontribusi Penjualan
        $pctKoran = $totalOmset > 0 ? round(($omzetKoran / $totalOmset) * 100) : 0;
        $pctOnline = $totalOmset > 0 ? round(($omzetOnline / $totalOmset) * 100) : 0;
        $pctTv = $totalOmset > 0 ? round(($omzetTv / $totalOmset) * 100) : 0;

        $channels = [
            'koran' => [
                'nama'       => 'Iklan Koran',
                'sub'        => 'Harian Umum Kabar Priangan',
                'kode'       => 'KRN',
                'badge_bg'   => 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200',
                'omzet'      => $omzetKoran,
                'bayar'      => $bayarKoran,
                'piutang'    => $piutangKoran,
                'count'      => $countKoran,
                'percent'    => $pctKoran,
                'info_ops'   => 'Terbit besok: ' . $tayangBesok['koran'] . ' iklan • Pracetak: ' . $antreanPracetakKoran,
                'route_tx'   => 'transaksikoran.index',
                'route_new'  => 'transaksikoran.create',
            ],
            'online' => [
                'nama'       => 'Iklan Online',
                'sub'        => '9 Portal Berita & Media Sosial',
                'kode'       => 'ONL',
                'badge_bg'   => 'bg-slate-100 text-slate-700 border-slate-200',
                'omzet'      => $omzetOnline,
                'bayar'      => $bayarOnline,
                'piutang'    => $piutangOnline,
                'count'      => $countOnline,
                'percent'    => $pctOnline,
                'info_ops'   => 'Tayang hari ini: ' . $tayangHariIni['online'] . ' • Antrean redaksi: ' . $menungguTayangOnline,
                'route_tx'   => 'transaksionline.index',
                'route_new'  => 'transaksionline.create',
            ],
            'tv' => [
                'nama'       => 'Iklan TV',
                'sub'        => 'Priangan TV, Streaming & Portal',
                'kode'       => 'PTV',
                'badge_bg'   => 'bg-slate-100 text-slate-700 border-slate-200',
                'omzet'      => $omzetTv,
                'bayar'      => $bayarTv,
                'piutang'    => $piutangTv,
                'count'      => $countTv,
                'percent'    => $pctTv,
                'info_ops'   => 'Tayang hari ini: ' . $tayangHariIni['tv'] . ' • Jadwal aktif: ' . $terjadwalTv,
                'route_tx'   => 'transaksipriangan.index',
                'route_new'  => 'transaksipriangan.create',
            ],
        ];

        // 6. Data Master & Pengaturan Aktif
        $totalKategori = KategoriMedia::where('is_active', true)->count();
        $totalJenisIklan = JenisIklan::where('is_active', true)->count();
        $totalPemasang = \App\Models\Pelanggan::where('is_active', true)->count();
        $totalSales = \App\Models\SalesAe::where('is_active', true)->count();
        $jamCutoff = substr(\App\Models\SistemConfig::getJamCutoff(), 0, 5);
        $isPastCutoff = \App\Models\SistemConfig::isPastCutoff();
        $ppnPersen = \App\Models\SistemConfig::getPpnPersen();

        // 7. Rekap Penjualan per Sales / AE pada Periode Terpilih (Gabungan 3 Media)
        $salesBucket = [];
        $pushSalesStat = static function (string $namaSales, int $tagihan, int $bayar, int $insentif) use (&$salesBucket) {
            $cleanName = trim($namaSales) !== '' ? trim($namaSales) : 'Front Office';
            $key = mb_strtolower($cleanName);
            if (!isset($salesBucket[$key])) {
                $salesBucket[$key] = [
                    'nama'     => $cleanName,
                    'faktur'   => 0,
                    'omzet'    => 0,
                    'bayar'    => 0,
                    'insentif' => 0,
                ];
            }
            $salesBucket[$key]['faktur']++;
            $salesBucket[$key]['omzet'] += $tagihan;
            $salesBucket[$key]['bayar'] += $bayar;
            $salesBucket[$key]['insentif'] += $insentif;
        };

        foreach ((clone $koranQuery)->with('salesAe')->get(['id', 'sales_ae_id', 'sales_iklankoran', 'totaltagihan_transaksikoran', 'jumlahbayar_transaksikoran', 'insentif_transaksikoran']) as $row) {
            $namaSales = $row->salesAe?->nama_ae ?: ($row->sales_iklankoran ?: 'Front Office');
            $pushSalesStat($namaSales, (int) $row->totaltagihan_transaksikoran, (int) $row->jumlahbayar_transaksikoran, (int) $row->insentif_transaksikoran);
        }
        foreach ((clone $onlineQuery)->with('salesAe')->get(['id', 'sales_ae_id', 'sales_iklanonline', 'totaltagihan_transaksionline', 'jumlahbayar_transaksionline', 'insentif_transaksionline']) as $row) {
            $namaSales = $row->salesAe?->nama_ae ?: ($row->sales_iklanonline ?: 'Front Office');
            $pushSalesStat($namaSales, (int) $row->totaltagihan_transaksionline, (int) $row->jumlahbayar_transaksionline, (int) $row->insentif_transaksionline);
        }
        foreach ((clone $tvQuery)->with('salesAe')->get(['id', 'sales_ae_id', 'sales_iklanpriangan', 'totaltagihan_transaksipriangan', 'jumlahbayar_transaksipriangan', 'insentif_transaksipriangan']) as $row) {
            $namaSales = $row->salesAe?->nama_ae ?: ($row->sales_iklanpriangan ?: 'Front Office');
            $pushSalesStat($namaSales, (int) $row->totaltagihan_transaksipriangan, (int) $row->jumlahbayar_transaksipriangan, (int) $row->insentif_transaksipriangan);
        }

        $rekapSalesDash = collect($salesBucket)
            ->sortByDesc('omzet')
            ->take(6)
            ->values();

        // 8. Jadwal Tayang Terdekat (Hari Ini & Ke Depan Lintas 3 Media)
        $jadwalItems = new Collection();

        $koranJadwalRows = TransaksiKoran::with('iklankoran')
            ->where('status_order', '!=', 'Batal')
            ->whereDate('tanggal_muatkoran', '>=', $todayStr)
            ->orderBy('tanggal_muatkoran')
            ->take(6)
            ->get();
        foreach ($koranJadwalRows as $kj) {
            $tglMuat = Carbon::parse($kj->tanggal_muatkoran)->toDateString();
            $jadwalItems->push((object) [
                'tanggal'    => $tglMuat,
                'media'      => 'Koran',
                'faktur'     => $kj->nofakturkoran,
                'pemasang'   => $kj->nama_pemasangkoran,
                'keterangan' => ($kj->iklankoran?->nama_jenis ?: ($kj->kategori_form ?: 'Iklan Koran')) . ($kj->halaman_iklan ? ' • ' . $kj->halaman_iklan : ''),
                'is_today'   => $tglMuat === $todayStr,
                'route_show' => route('transaksikoran.show', $kj->id),
            ]);
        }

        $onlineJadwalRows = TransaksiOnline::with('iklanonline')
            ->whereDate('tanggal_muatiklanonline', '>=', $todayStr)
            ->orderBy('tanggal_muatiklanonline')
            ->take(6)
            ->get();
        foreach ($onlineJadwalRows as $oj) {
            $tglMuat = Carbon::parse($oj->tanggal_muatiklanonline)->toDateString();
            $jadwalItems->push((object) [
                'tanggal'    => $tglMuat,
                'media'      => 'Online',
                'faktur'     => $oj->nofakturonline,
                'pemasang'   => $oj->nama_pemasangonline,
                'keterangan' => $oj->iklanonline?->nama_jenis ?: 'Publikasi Online',
                'is_today'   => $tglMuat === $todayStr,
                'route_show' => route('transaksionline.show', $oj->id),
            ]);
        }

        $tvJadwalRows = TransaksiPriangan::with('iklanpriangan')
            ->whereDate('tanggal_muatiklanpriangan', '>=', $todayStr)
            ->orderBy('tanggal_muatiklanpriangan')
            ->take(6)
            ->get();
        foreach ($tvJadwalRows as $tj) {
            $tglMuat = Carbon::parse($tj->tanggal_muatiklanpriangan)->toDateString();
            $jadwalItems->push((object) [
                'tanggal'    => $tglMuat,
                'media'      => 'TV',
                'faktur'     => $tj->nofakturpriangan,
                'pemasang'   => $tj->nama_pemasangpriangan,
                'keterangan' => ($tj->iklanpriangan?->nama_jenis ?: 'Iklan TV') . ($tj->jam_tayang ? ' • ' . $tj->jam_tayang : ''),
                'is_today'   => $tglMuat === $todayStr,
                'route_show' => route('transaksipriangan.show', $tj->id),
            ]);
        }

        $jadwalTerdekat = $jadwalItems->sortBy('tanggal')->take(6)->values();

        // 9. Transaksi Terbaru Gabungan (Top 8 Terbaru + Status QR Setoran Kasir)
        $recentTransactions = new Collection();

        $recentKoran = TransaksiKoran::with(['iklankoran', 'salesAe'])
            ->where('status_order', '!=', 'Batal')
            ->latest('id')
            ->take(6)
            ->get();
        $qrKoranMap = \App\Models\FakturQrPembayaran::where('kategori', \App\Models\FakturQrPembayaran::KATEGORI_KORAN)
            ->whereIn('transaksi_id', $recentKoran->pluck('id'))
            ->whereIn('status', [\App\Models\FakturQrPembayaran::STATUS_AKTIF, \App\Models\FakturQrPembayaran::STATUS_DISETOR])
            ->orderBy('id')
            ->get()
            ->keyBy('transaksi_id');

        foreach ($recentKoran as $k) {
            $labelJenisKoran = match ($k->kategori_form) {
                'BARIS'       => 'Iklan Baris',
                'KOLOM'       => 'Iklan Kolom',
                'DISPLAY'     => 'Iklan Display',
                'ADVERTORIAL' => 'Advertorial',
                default       => ($k->iklankoran?->jenis_iklankoran ?: ($k->iklankoran?->nama_jenis ?: ($k->kategori_form ?: 'Iklan Koran')))
            };
            $qrRec = $qrKoranMap->get($k->id);

            $recentTransactions->push((object) [
                'id'          => $k->id,
                'channel'     => 'Koran',
                'code'        => 'KRN',
                'faktur'      => $k->nofakturkoran,
                'tanggal'     => $k->tanggal_transaksikoran,
                'customer'    => $k->nama_pemasangkoran,
                'sales'       => $k->salesAe?->nama_ae ?: ($k->sales_iklankoran ?: ($k->sales_koran ?: '-')),
                'paket'       => $labelJenisKoran,
                'total'       => (int) $k->totaltagihan_transaksikoran,
                'bayar'       => (int) $k->jumlahbayar_transaksikoran,
                'piutang'     => (int) $k->piutang_transaksikoran,
                'is_lunas'    => (int) $k->piutang_transaksikoran <= 0,
                'qr_status'   => $qrRec?->status,
                'created_at'  => $k->created_at,
                'route_view'  => route('transaksikoran.show', $k->id),
                'route_show'  => route('transaksikoran.show', $k->id),
                'route_edit'  => route('transaksikoran.edit', $k->id),
                'route_cetak' => route('transaksikoran.cetak', $k->id),
                'route_sppi'  => route('transaksikoran.sppi', $k->id),
            ]);
        }

        $recentOnline = TransaksiOnline::with(['iklanonline', 'salesAe'])->latest('id')->take(6)->get();
        $qrOnlineMap = \App\Models\FakturQrPembayaran::where('kategori', \App\Models\FakturQrPembayaran::KATEGORI_ONLINE)
            ->whereIn('transaksi_id', $recentOnline->pluck('id'))
            ->whereIn('status', [\App\Models\FakturQrPembayaran::STATUS_AKTIF, \App\Models\FakturQrPembayaran::STATUS_DISETOR])
            ->orderBy('id')
            ->get()
            ->keyBy('transaksi_id');

        foreach ($recentOnline as $o) {
            if (!empty($o->kategori_produk_publikasi) && is_array($o->kategori_produk_publikasi)) {
                $labelJenisOnline = implode(', ', $o->kategori_produk_publikasi);
            } else {
                $labelJenisOnline = $o->iklanonline?->jenis_iklanonline ?? ($o->iklanonline?->nama_jenis ?? 'Iklan Online');
            }
            $qrRec = $qrOnlineMap->get($o->id);

            $recentTransactions->push((object) [
                'id'          => $o->id,
                'channel'     => 'Online',
                'code'        => 'ONL',
                'faktur'      => $o->nofakturonline,
                'tanggal'     => $o->tanggal_transaksionline,
                'customer'    => $o->nama_pemasangonline,
                'sales'       => $o->salesAe?->nama_ae ?: ($o->sales_iklanonline ?: '-'),
                'paket'       => $labelJenisOnline,
                'total'       => (int) $o->totaltagihan_transaksionline,
                'bayar'       => (int) $o->jumlahbayar_transaksionline,
                'piutang'     => (int) $o->piutang_transaksionline,
                'is_lunas'    => (int) $o->piutang_transaksionline <= 0,
                'qr_status'   => $qrRec?->status,
                'created_at'  => $o->created_at,
                'route_view'  => route('transaksionline.show', $o->id),
                'route_show'  => route('transaksionline.show', $o->id),
                'route_edit'  => route('transaksionline.edit', $o->id),
                'route_cetak' => route('transaksionline.cetak', $o->id),
                'route_sppi'  => route('transaksionline.sppi', $o->id),
            ]);
        }

        $recentTv = TransaksiPriangan::with(['iklanpriangan', 'salesAe'])->latest('id')->take(6)->get();
        $qrTvMap = \App\Models\FakturQrPembayaran::where('kategori', \App\Models\FakturQrPembayaran::KATEGORI_TV)
            ->whereIn('transaksi_id', $recentTv->pluck('id'))
            ->whereIn('status', [\App\Models\FakturQrPembayaran::STATUS_AKTIF, \App\Models\FakturQrPembayaran::STATUS_DISETOR])
            ->orderBy('id')
            ->get()
            ->keyBy('transaksi_id');

        foreach ($recentTv as $t) {
            $qrRec = $qrTvMap->get($t->id);

            $recentTransactions->push((object) [
                'id'          => $t->id,
                'channel'     => 'TV',
                'code'        => 'PTV',
                'faktur'      => $t->nofakturpriangan,
                'tanggal'     => $t->tanggal_transaksipriangan,
                'customer'    => $t->nama_pemasangpriangan,
                'sales'       => $t->salesAe?->nama_ae ?: ($t->sales_iklanpriangan ?: '-'),
                'paket'       => $t->iklanpriangan?->jenis_iklanpriangan ?? ($t->iklanpriangan?->nama_jenis ?? '-'),
                'total'       => (int) ($t->totaltagihan_transaksipriangan ?? $t->harga_transaksipriangan),
                'bayar'       => (int) $t->jumlahbayar_transaksipriangan,
                'piutang'     => (int) $t->piutang_transaksipriangan,
                'is_lunas'    => (int) $t->piutang_transaksipriangan <= 0,
                'qr_status'   => $qrRec?->status,
                'created_at'  => $t->created_at,
                'route_view'  => route('transaksipriangan.show', $t->id),
                'route_show'  => route('transaksipriangan.show', $t->id),
                'route_edit'  => route('transaksipriangan.edit', $t->id),
                'route_cetak' => route('transaksipriangan.cetak', $t->id),
                'route_sppi'  => route('transaksipriangan.sppi', $t->id),
            ]);
        }

        $recentStream = $recentTransactions->sortByDesc('created_at')->take(8)->values();

        // 10. Data Grafik Penjualan 7 Hari Terakhir (Kecualikan Koran Batal)
        $dailyLabels = [];
        $dailyKoran = [];
        $dailyOnline = [];
        $dailyTv = [];

        for ($i = 6; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i)->toDateString();
            $dailyLabels[] = Carbon::parse($d)->translatedFormat('d M');
            $dailyKoran[] = (int) TransaksiKoran::whereDate('tanggal_transaksikoran', $d)
                ->where('status_order', '!=', 'Batal')
                ->sum('totaltagihan_transaksikoran');
            $dailyOnline[] = (int) TransaksiOnline::whereDate('tanggal_transaksionline', $d)->sum('totaltagihan_transaksionline');
            $dailyTv[] = (int) TransaksiPriangan::whereDate('tanggal_transaksipriangan', $d)->sum('totaltagihan_transaksipriangan');
        }

        // 11. Data Kalender Nasional & Hari Penting Pers
        $kalenderService = app(\App\Services\KalenderNasionalService::class);
        $kalenderAwal = $kalenderService->getKalenderBulan((int) date('Y'), (int) date('n'));

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
            'totalBelumLunas',
            'totalKomisiKh',
            'totalInsentifSales',
            'penjualanHariIni',
            'transaksiHariIni',
            'qrSummary',
            'tayangHariIni',
            'tayangBesok',
            'channels',
            'totalKategori',
            'totalJenisIklan',
            'totalPemasang',
            'totalSales',
            'jamCutoff',
            'isPastCutoff',
            'ppnPersen',
            'rekapSalesDash',
            'jadwalTerdekat',
            'recentStream',
            'dailyLabels',
            'dailyKoran',
            'dailyOnline',
            'dailyTv',
            'kalenderAwal'
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

        // Data Kalender Nasional & Hari Besar Pers (API Live Ready)
        $kalenderService = app(\App\Services\KalenderNasionalService::class);
        $kalenderAwal = $kalenderService->getKalenderBulan((int) date('Y'), (int) date('n'));

        // Transaksi Terbaru Gabungan Lintas 3 Saluran Media
        $recentTransactions = new Collection();
        $recentKoran = TransaksiKoran::with('iklankoran')->latest('id')->take(3)->get();
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
        $recentOnline = TransaksiOnline::with('iklanonline')->latest('id')->take(3)->get();
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
        $recentTv = TransaksiPriangan::with('iklanpriangan')->latest('id')->take(3)->get();
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
        $recentStream = $recentTransactions->sortByDesc('created_at')->take(5)->values();

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
            'totalUsers',
            'kalenderAwal',
            'recentStream'
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
