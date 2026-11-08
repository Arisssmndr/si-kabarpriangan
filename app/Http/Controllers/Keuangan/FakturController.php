<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Faktur;
use App\Models\KasTransaksi;
use App\Models\PembayaranFaktur;
use App\Models\StokOpnameFaktur;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FakturController extends Controller
{
    /**
     * Dispatcher Utama: Mengarahkan langsung ke Faktur Iklan Keseluruhan.
     */
    public function index(Request $request)
    {
        // Jika ada filter kategori sirkulasi yang dikirim, arahkan ke modul sirkulasi
        if ($request->get('kategori') === 'sirkulasi') {
            return redirect()->route('keuangan.faktur.sirkulasi.index');
        }

        return redirect()->route('keuangan.faktur.iklan.index', $request->query());
    }

    // =========================================================================
    // 1. MODUL FAKTUR IKLAN
    // =========================================================================

    /**
     * 1.A. Faktur Iklan Keseluruhan.
     */
    public function iklanKeseluruhan(Request $request)
    {
        $query = Faktur::with(['customer', 'creator', 'penugasanAktif.petugas'])
            ->where('kategori', 'iklan');

        // Filter Status Pembayaran
        if ($request->filled('status') && in_array($request->status, ['belum_bayar', 'cicilan', 'lunas'])) {
            $query->where('status', $request->status);
        }

        // Filter Tipe Faktur
        if ($request->filled('tipe_faktur') && in_array($request->tipe_faktur, ['reguler', 'susulan', 'stopper', 'bonus'])) {
            $query->where('tipe_faktur', $request->tipe_faktur);
        }

        // Filter Rentang Tanggal
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_faktur', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_faktur', '<=', $request->sampai_tanggal);
        }

        // Filter Pencarian No. Faktur atau Nama Customer
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%");
                    });
            });
        }

        $fakturs = $query->latest('tanggal_faktur')->latest('id')->paginate(12)->withQueryString();

        // KPI Ringkas Faktur Iklan
        $baseIklan = Faktur::where('kategori', 'iklan');
        $totalFakturCount  = (clone $baseIklan)->count();
        $totalTagihanSum   = (float) (clone $baseIklan)->sum('total_tagihan');
        $totalTerbayarSum  = (float) (clone $baseIklan)->sum('total_terbayar');
        $totalPiutangSum   = (float) (clone $baseIklan)->sum('sisa_piutang');
        $fakturLunasCount  = (clone $baseIklan)->where('status', 'lunas')->count();
        $fakturPiutangCount = (clone $baseIklan)->where('status', '!=', 'lunas')->count();

        return view('pages.keuangan.faktur.iklan.index', compact(
            'fakturs',
            'totalFakturCount',
            'totalTagihanSum',
            'totalTerbayarSum',
            'totalPiutangSum',
            'fakturLunasCount',
            'fakturPiutangCount'
        ));
    }

    /**
     * 1.B. Faktur Iklan Susulan (Materi Terbit Mendadak / Susulan Order).
     */
    public function iklanSusulan(Request $request)
    {
        $query = Faktur::with(['customer', 'creator', 'penugasanAktif.petugas'])
            ->where('kategori', 'iklan')
            ->where('tipe_faktur', 'susulan');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('status') && in_array($request->status, ['lunas', 'cicilan', 'belum_bayar'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_faktur', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_faktur', '<=', $request->sampai_tanggal);
        }

        $fakturs = $query->latest('tanggal_faktur')->paginate(12)->withQueryString();

        $baseSusulan = Faktur::where('kategori', 'iklan')->where('tipe_faktur', 'susulan');
        $totalSusulanCount    = (clone $baseSusulan)->count();
        $totalSusulanTagihan  = (float) (clone $baseSusulan)->sum('total_tagihan');
        $totalSusulanTerbayar = (float) (clone $baseSusulan)->sum('total_terbayar');
        $totalSusulanPiutang  = (float) (clone $baseSusulan)->sum('sisa_piutang');

        return view('pages.keuangan.faktur.iklan.susulan', compact(
            'fakturs',
            'totalSusulanCount',
            'totalSusulanTagihan',
            'totalSusulanTerbayar',
            'totalSusulanPiutang'
        ));
    }

    /**
     * 1.C. Rekap Faktur Iklan (per Tanggal & per Bulan).
     */
    public function rekapIklan(Request $request)
    {
        $mode = $request->get('mode', 'bulan'); // 'bulan' atau 'tanggal'
        $group = $request->get('group', 'per_bulan');
        $now = Carbon::now();

        // Ambil list AE & Customer untuk filter di view
        $aeList = Faktur::where('kategori', 'iklan')
            ->whereNotNull('sales_nama')
            ->where('sales_nama', '!=', '')
            ->distinct()
            ->orderBy('sales_nama')
            ->pluck('sales_nama')
            ->toArray();
        if (empty($aeList)) {
            $aeList = ['Ahmad Fauzi', 'Dedi Gunawan', 'Rina Marlina', 'Iwan Ridwan'];
        }

        $customerList = Customer::where('tipe', 'iklan')->orderBy('nama_customer')->get(['id', 'nama_customer', 'alamat']);

        $tahun = (int) $request->get('tahun', $now->year);
        $bulanAwal = (int) $request->get('bulan_awal', 1);
        $bulanAkhir = (int) $request->get('bulan_akhir', $now->month);
        $selectedAe = $request->get('ae', '');
        $selectedCust = $request->get('customer_id', '');

        $dariTanggal = $request->get('dari_tanggal', $now->copy()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->get('sampai_tanggal', $now->copy()->endOfMonth()->toDateString());

        if ($mode === 'bulan') {
            $query = Faktur::where('kategori', 'iklan');

            if ($group === 'per_tahun') {
                $query->selectRaw("
                    YEAR(tanggal_faktur) as periode_key,
                    COUNT(*) as total_faktur,
                    SUM(total_tagihan) as total_tagihan,
                    SUM(total_terbayar) as total_terbayar,
                    SUM(sisa_piutang) as total_piutang,
                    SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                    SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang
                ")
                ->groupBy(DB::raw("YEAR(tanggal_faktur)"))
                ->orderByDesc('periode_key');
            } elseif ($group === 'per_ae_tahun') {
                $query->selectRaw("
                    YEAR(tanggal_faktur) as periode_key,
                    COUNT(*) as total_faktur,
                    SUM(total_tagihan) as total_tagihan,
                    SUM(total_terbayar) as total_terbayar,
                    SUM(sisa_piutang) as total_piutang,
                    SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                    SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang
                ")
                ->when($selectedAe, function($q) use ($selectedAe) {
                    $q->where('sales_nama', $selectedAe);
                })
                ->groupBy(DB::raw("YEAR(tanggal_faktur)"))
                ->orderByDesc('periode_key');
            } elseif (in_array($group, ['rekap_ae_bulan', 'rekap_ae_tahun'])) {
                $query->selectRaw("
                    COALESCE(sales_nama, 'Tanpa AE') as periode_key,
                    COUNT(*) as total_faktur,
                    SUM(total_tagihan) as total_tagihan,
                    SUM(total_terbayar) as total_terbayar,
                    SUM(sisa_piutang) as total_piutang,
                    SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                    SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang
                ")
                ->whereYear('tanggal_faktur', $tahun);

                if ($group === 'rekap_ae_bulan') {
                    $query->whereBetween(DB::raw("MONTH(tanggal_faktur)"), [$bulanAwal, $bulanAkhir]);
                }

                $query->groupBy('sales_nama')->orderByDesc('total_tagihan');
            } elseif ($group === 'per_pemasang') {
                $query->selectRaw("
                    customer_id as periode_key,
                    COUNT(*) as total_faktur,
                    SUM(total_tagihan) as total_tagihan,
                    SUM(total_terbayar) as total_terbayar,
                    SUM(sisa_piutang) as total_piutang,
                    SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                    SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang
                ")
                ->whereYear('tanggal_faktur', $tahun)
                ->whereBetween(DB::raw("MONTH(tanggal_faktur)"), [$bulanAwal, $bulanAkhir]);

                if ($selectedCust) {
                    $query->where('customer_id', $selectedCust);
                }

                $query->groupBy('customer_id')->orderByDesc('total_tagihan');
            } else {
                // Default: per_bulan atau per_ae_bulan
                $query->selectRaw("
                    MONTH(tanggal_faktur) as periode_key,
                    COUNT(*) as total_faktur,
                    SUM(total_tagihan) as total_tagihan,
                    SUM(total_terbayar) as total_terbayar,
                    SUM(sisa_piutang) as total_piutang,
                    SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                    SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang
                ")
                ->whereYear('tanggal_faktur', $tahun)
                ->whereBetween(DB::raw("MONTH(tanggal_faktur)"), [$bulanAwal, $bulanAkhir]);

                if ($group === 'per_ae_bulan' && $selectedAe) {
                    $query->where('sales_nama', $selectedAe);
                }

                $query->groupBy(DB::raw("MONTH(tanggal_faktur)"))->orderBy('periode_key');
            }

            $rekapData = $query->get();
        } else {
            // Mode Tanggal (Harian)
            $rekapData = Faktur::selectRaw("
                tanggal_faktur as periode_key,
                COUNT(*) as total_faktur,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang,
                SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang
            ")
            ->where('kategori', 'iklan')
            ->whereBetween('tanggal_faktur', [$dariTanggal, $sampaiTanggal])
            ->groupBy('tanggal_faktur')
            ->orderByDesc('tanggal_faktur')
            ->get();
        }

        $grandTagihan = (float) $rekapData->sum('total_tagihan');
        $grandTerbayar = (float) $rekapData->sum('total_terbayar');
        $grandPiutang = (float) $rekapData->sum('total_piutang');
        $grandFaktur = (int) $rekapData->sum('total_faktur');

        return view('pages.keuangan.faktur.iklan.rekap', compact(
            'mode',
            'group',
            'tahun',
            'bulanAwal',
            'bulanAkhir',
            'aeList',
            'selectedAe',
            'customerList',
            'selectedCust',
            'dariTanggal',
            'sampaiTanggal',
            'rekapData',
            'grandTagihan',
            'grandTerbayar',
            'grandPiutang',
            'grandFaktur'
        ));
    }

    /**
     * Cetak Lembar Rekapitulasi Faktur Iklan.
     */
    public function cetakRekapIklan(Request $request)
    {
        $mode = $request->get('mode', 'bulan');
        $group = $request->get('group', 'per_bulan');
        $now = Carbon::now();

        $tahun = (int) $request->get('tahun', $now->year);
        $bulanAwal = (int) $request->get('bulan_awal', 1);
        $bulanAkhir = (int) $request->get('bulan_akhir', $now->month);
        $selectedAe = $request->get('ae', '');
        $selectedCust = $request->get('customer_id', '');

        $dariTanggal = $request->get('dari_tanggal', $now->copy()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->get('sampai_tanggal', $now->copy()->endOfMonth()->toDateString());

        if ($mode === 'bulan') {
            $query = Faktur::where('kategori', 'iklan');

            if ($group === 'per_tahun') {
                $query->selectRaw("
                    YEAR(tanggal_faktur) as periode_key,
                    COUNT(*) as total_faktur,
                    SUM(total_tagihan) as total_tagihan,
                    SUM(total_terbayar) as total_terbayar,
                    SUM(sisa_piutang) as total_piutang,
                    SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                    SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang
                ")
                ->groupBy(DB::raw("YEAR(tanggal_faktur)"))
                ->orderByDesc('periode_key');

                $periodeJudul = "Rekap Per Tahun";
            } elseif ($group === 'per_ae_tahun') {
                $query->selectRaw("
                    YEAR(tanggal_faktur) as periode_key,
                    COUNT(*) as total_faktur,
                    SUM(total_tagihan) as total_tagihan,
                    SUM(total_terbayar) as total_terbayar,
                    SUM(sisa_piutang) as total_piutang,
                    SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                    SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang
                ")
                ->when($selectedAe, function($q) use ($selectedAe) {
                    $q->where('sales_nama', $selectedAe);
                })
                ->groupBy(DB::raw("YEAR(tanggal_faktur)"))
                ->orderByDesc('periode_key');

                $periodeJudul = "Rekap Per AE / Tahun: " . ($selectedAe ?: 'Semua AE');
            } elseif (in_array($group, ['rekap_ae_bulan', 'rekap_ae_tahun'])) {
                $query->selectRaw("
                    COALESCE(sales_nama, 'Tanpa AE') as periode_key,
                    COUNT(*) as total_faktur,
                    SUM(total_tagihan) as total_tagihan,
                    SUM(total_terbayar) as total_terbayar,
                    SUM(sisa_piutang) as total_piutang,
                    SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                    SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang
                ")
                ->whereYear('tanggal_faktur', $tahun);

                if ($group === 'rekap_ae_bulan') {
                    $query->whereBetween(DB::raw("MONTH(tanggal_faktur)"), [$bulanAwal, $bulanAkhir]);
                    $periodeJudul = "Rekap AE / Bulan (Tahun {$tahun}, Bulan {$bulanAwal} s/d {$bulanAkhir})";
                } else {
                    $periodeJudul = "Rekap AE / Tahun (Tahun {$tahun})";
                }

                $query->groupBy('sales_nama')->orderByDesc('total_tagihan');
            } elseif ($group === 'per_pemasang') {
                $query->selectRaw("
                    customer_id as periode_key,
                    COUNT(*) as total_faktur,
                    SUM(total_tagihan) as total_tagihan,
                    SUM(total_terbayar) as total_terbayar,
                    SUM(sisa_piutang) as total_piutang,
                    SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                    SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang
                ")
                ->whereYear('tanggal_faktur', $tahun)
                ->whereBetween(DB::raw("MONTH(tanggal_faktur)"), [$bulanAwal, $bulanAkhir]);

                if ($selectedCust) {
                    $query->where('customer_id', $selectedCust);
                }

                $query->groupBy('customer_id')->orderByDesc('total_tagihan');
                $periodeJudul = "Rekap Per Pemasang (Tahun {$tahun}, Bulan {$bulanAwal} s/d {$bulanAkhir})";
            } else {
                $query->selectRaw("
                    MONTH(tanggal_faktur) as periode_key,
                    COUNT(*) as total_faktur,
                    SUM(total_tagihan) as total_tagihan,
                    SUM(total_terbayar) as total_terbayar,
                    SUM(sisa_piutang) as total_piutang,
                    SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                    SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang
                ")
                ->whereYear('tanggal_faktur', $tahun)
                ->whereBetween(DB::raw("MONTH(tanggal_faktur)"), [$bulanAwal, $bulanAkhir]);

                if ($group === 'per_ae_bulan' && $selectedAe) {
                    $query->where('sales_nama', $selectedAe);
                    $periodeJudul = "Rekap Per AE / Bulan: {$selectedAe} (Tahun {$tahun})";
                } else {
                    $periodeJudul = "Rekap Per Bulan (Tahun {$tahun}, Bulan {$bulanAwal} s/d {$bulanAkhir})";
                }

                $query->groupBy(DB::raw("MONTH(tanggal_faktur)"))->orderBy('periode_key');
            }

            $rekapData = $query->get();
        } else {
            $rekapData = Faktur::selectRaw("
                tanggal_faktur as periode_key,
                COUNT(*) as total_faktur,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang,
                SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang
            ")
            ->where('kategori', 'iklan')
            ->whereBetween('tanggal_faktur', [$dariTanggal, $sampaiTanggal])
            ->groupBy('tanggal_faktur')
            ->orderByDesc('tanggal_faktur')
            ->get();

            $periodeJudul = Carbon::parse($dariTanggal)->translatedFormat('d F Y') . ' s/d ' . Carbon::parse($sampaiTanggal)->translatedFormat('d F Y');
        }

        return view('pages.keuangan.faktur.iklan.cetak-rekap', compact('mode', 'group', 'rekapData', 'periodeJudul'));
    }

    /**
     * 1.D. Rekap Faktur Iklan Tunai (Lunas Langsung / Kas).
     */
    public function iklanTunai(Request $request)
    {
        $query = Faktur::with(['customer', 'creator', 'pembayaran.penerima'])
            ->where('kategori', 'iklan')
            ->where('status', 'lunas');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_faktur', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_faktur', '<=', $request->sampai_tanggal);
        }

        $fakturs = $query->latest('tanggal_faktur')->paginate(12)->withQueryString();

        $baseTunai = Faktur::where('kategori', 'iklan')->where('status', 'lunas');
        $totalTunaiCount   = (clone $baseTunai)->count();
        $totalNominalTunai = (float) (clone $baseTunai)->sum('total_tagihan');

        return view('pages.keuangan.faktur.iklan.tunai', compact(
            'fakturs',
            'totalTunaiCount',
            'totalNominalTunai'
        ));
    }

    /**
     * 1.E. Rekap Faktur Iklan Piutang (Format Per Bulan & Aging Piutang).
     */
    public function iklanPiutang(Request $request)
    {
        $now = Carbon::now();
        $tahun = (int) $request->get('tahun', $now->year);
        $bulanPilihan = $request->filled('bulan') && $request->bulan !== '' ? (int) $request->bulan : null;

        // 1. Rekapitulasi Piutang per Bulan (Grouped per Bulan untuk Tahun terpilih)
        $rekapBulanan = Faktur::selectRaw("
            MONTH(tanggal_faktur) as bulan,
            COUNT(*) as total_faktur,
            SUM(total_tagihan) as total_tagihan,
            SUM(total_terbayar) as total_terbayar,
            SUM(sisa_piutang) as total_piutang,
            SUM(CASE WHEN status = 'cicilan' THEN 1 ELSE 0 END) as total_cicilan,
            SUM(CASE WHEN status = 'belum_bayar' THEN 1 ELSE 0 END) as total_belum_bayar,
            SUM(CASE WHEN DATEDIFF(?, jatuh_tempo) <= 0 THEN sisa_piutang ELSE 0 END) as nominal_lancar,
            SUM(CASE WHEN DATEDIFF(?, jatuh_tempo) BETWEEN 1 AND 30 THEN sisa_piutang ELSE 0 END) as nominal_perhatian,
            SUM(CASE WHEN DATEDIFF(?, jatuh_tempo) > 30 THEN sisa_piutang ELSE 0 END) as nominal_kritis
        ", [$now->toDateString(), $now->toDateString(), $now->toDateString()])
        ->where('kategori', 'iklan')
        ->where('sisa_piutang', '>', 0)
        ->whereYear('tanggal_faktur', $tahun)
        ->groupBy(DB::raw("MONTH(tanggal_faktur)"))
        ->orderBy('bulan')
        ->get();

        // 2. Query Detail Faktur Piutang (difilter per tahun dan bulan pilihan jika ada)
        $query = Faktur::with(['customer', 'creator', 'penugasanAktif.petugas'])
            ->where('kategori', 'iklan')
            ->where('sisa_piutang', '>', 0)
            ->whereYear('tanggal_faktur', $tahun);

        if ($bulanPilihan) {
            $query->whereMonth('tanggal_faktur', $bulanPilihan);
        }

        if ($request->filled('status') && in_array($request->status, ['belum_bayar', 'cicilan'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%");
                    });
            });
        }

        // Filter Penugasan Inkaso
        if ($request->filled('status_inkaso')) {
            if ($request->status_inkaso === 'sudah') {
                $query->whereHas('penugasanAktif');
            } elseif ($request->status_inkaso === 'belum') {
                $query->whereDoesntHave('penugasanAktif');
            }
        }

        $fakturs = $query->latest('tanggal_faktur')->paginate(12)->withQueryString();

        // 3. Ringkasan KPI Aging Piutang Tahun Terpilih
        $grandTotalPiutangCount = (int) $rekapBulanan->sum('total_faktur');
        $grandTotalTagihan      = (float) $rekapBulanan->sum('total_tagihan');
        $grandTotalTerbayar     = (float) $rekapBulanan->sum('total_terbayar');
        $grandTotalPiutang      = (float) $rekapBulanan->sum('total_piutang');
        $grandLancarNominal     = (float) $rekapBulanan->sum('nominal_lancar');
        $grandPerhatianNominal  = (float) $rekapBulanan->sum('nominal_perhatian');
        $grandKritisNominal     = (float) $rekapBulanan->sum('nominal_kritis');

        return view('pages.keuangan.faktur.iklan.piutang', compact(
            'fakturs',
            'rekapBulanan',
            'tahun',
            'bulanPilihan',
            'grandTotalPiutangCount',
            'grandTotalTagihan',
            'grandTotalTerbayar',
            'grandTotalPiutang',
            'grandLancarNominal',
            'grandPerhatianNominal',
            'grandKritisNominal'
        ));
    }

    /**
     * Cetak Lembar Rekapitulasi Faktur Iklan Piutang Per Bulan.
     */
    public function cetakRekapIklanPiutang(Request $request)
    {
        $now = Carbon::now();
        $tahun = (int) $request->get('tahun', $now->year);
        $bulanPilihan = $request->filled('bulan') && $request->bulan !== '' ? (int) $request->bulan : null;

        $rekapBulanan = Faktur::selectRaw("
            MONTH(tanggal_faktur) as bulan,
            COUNT(*) as total_faktur,
            SUM(total_tagihan) as total_tagihan,
            SUM(total_terbayar) as total_terbayar,
            SUM(sisa_piutang) as total_piutang,
            SUM(CASE WHEN status = 'cicilan' THEN 1 ELSE 0 END) as total_cicilan,
            SUM(CASE WHEN status = 'belum_bayar' THEN 1 ELSE 0 END) as total_belum_bayar,
            SUM(CASE WHEN DATEDIFF(?, jatuh_tempo) <= 0 THEN sisa_piutang ELSE 0 END) as nominal_lancar,
            SUM(CASE WHEN DATEDIFF(?, jatuh_tempo) BETWEEN 1 AND 30 THEN sisa_piutang ELSE 0 END) as nominal_perhatian,
            SUM(CASE WHEN DATEDIFF(?, jatuh_tempo) > 30 THEN sisa_piutang ELSE 0 END) as nominal_kritis
        ", [$now->toDateString(), $now->toDateString(), $now->toDateString()])
        ->where('kategori', 'iklan')
        ->where('sisa_piutang', '>', 0)
        ->whereYear('tanggal_faktur', $tahun)
        ->when($bulanPilihan, function ($q) use ($bulanPilihan) {
            $q->whereMonth('tanggal_faktur', $bulanPilihan);
        })
        ->groupBy(DB::raw("MONTH(tanggal_faktur)"))
        ->orderBy('bulan')
        ->get();

        $grandTotalPiutangCount = (int) $rekapBulanan->sum('total_faktur');
        $grandTotalTagihan      = (float) $rekapBulanan->sum('total_tagihan');
        $grandTotalTerbayar     = (float) $rekapBulanan->sum('total_terbayar');
        $grandTotalPiutang      = (float) $rekapBulanan->sum('total_piutang');
        $grandLancarNominal     = (float) $rekapBulanan->sum('nominal_lancar');
        $grandPerhatianNominal  = (float) $rekapBulanan->sum('nominal_perhatian');
        $grandKritisNominal     = (float) $rekapBulanan->sum('nominal_kritis');

        $periodeJudul = $bulanPilihan 
            ? "Bulan " . Carbon::create()->month($bulanPilihan)->translatedFormat('F') . " {$tahun}"
            : "Tahun {$tahun} (Januari - Desember)";

        return view('pages.keuangan.faktur.iklan.cetak-piutang', compact(
            'rekapBulanan',
            'tahun',
            'bulanPilihan',
            'periodeJudul',
            'grandTotalPiutangCount',
            'grandTotalTagihan',
            'grandTotalTerbayar',
            'grandTotalPiutang',
            'grandLancarNominal',
            'grandPerhatianNominal',
            'grandKritisNominal'
        ));
    }

    /**
     * 1.F. Rekap Faktur Iklan Stopper & Bonus (Non-Komersial / Kompensasi Space).
     */
    public function iklanStopperBonus(Request $request)
    {
        $now = Carbon::now();
        $tahun = (int) $request->get('tahun', $now->year);
        $bulanPilihan = $request->filled('bulan') && $request->bulan !== '' ? (int) $request->bulan : null;

        $query = Faktur::with(['customer', 'creator'])
            ->where('kategori', 'iklan')
            ->whereIn('tipe_faktur', ['stopper', 'bonus'])
            ->whereYear('tanggal_faktur', $tahun);

        if ($bulanPilihan) {
            $query->whereMonth('tanggal_faktur', $bulanPilihan);
        }

        if ($request->filled('tipe_faktur') && in_array($request->tipe_faktur, ['stopper', 'bonus'])) {
            $query->where('tipe_faktur', $request->tipe_faktur);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_faktur', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_faktur', '<=', $request->sampai_tanggal);
        }

        $fakturs = $query->latest('tanggal_faktur')->paginate(12)->withQueryString();

        // Rekapitulasi bulanan Stopper & Bonus
        $rekapBulanan = Faktur::selectRaw("
            MONTH(tanggal_faktur) as bulan,
            COUNT(*) as total_faktur,
            SUM(CASE WHEN tipe_faktur = 'stopper' THEN 1 ELSE 0 END) as total_stopper,
            SUM(CASE WHEN tipe_faktur = 'bonus' THEN 1 ELSE 0 END) as total_bonus,
            SUM(total_tagihan) as total_nilai_komersial
        ")
        ->where('kategori', 'iklan')
        ->whereIn('tipe_faktur', ['stopper', 'bonus'])
        ->whereYear('tanggal_faktur', $tahun)
        ->groupBy(DB::raw("MONTH(tanggal_faktur)"))
        ->orderBy('bulan')
        ->get();

        // KPI Ringkas
        $baseTotal = Faktur::where('kategori', 'iklan')->whereIn('tipe_faktur', ['stopper', 'bonus'])->whereYear('tanggal_faktur', $tahun);
        $totalCount            = (clone $baseTotal)->count();
        $totalStopperCount     = (clone $baseTotal)->where('tipe_faktur', 'stopper')->count();
        $totalBonusCount       = (clone $baseTotal)->where('tipe_faktur', 'bonus')->count();
        $totalNilaiKomersial   = (float) (clone $baseTotal)->sum('total_tagihan');

        return view('pages.keuangan.faktur.iklan.stopper-bonus', compact(
            'fakturs',
            'rekapBulanan',
            'tahun',
            'bulanPilihan',
            'totalCount',
            'totalStopperCount',
            'totalBonusCount',
            'totalNilaiKomersial'
        ));
    }

    /**
     * 1.G. Stok Opname Faktur Iklan (Audit Seri Faktur, Dimensi AE, Periode & Pemasang).
     */
    public function stokOpname(Request $request)
    {
        $now = Carbon::now();
        $allowedTabs = ['per_ae_bulan', 'per_ae_tahun', 'rekap_ae_bulan', 'rekap_ae_tahun', 'per_bulan', 'per_tahun', 'per_pemasang', 'audit_blangko'];
        $tab = $request->get('tab', 'per_ae_bulan');
        if (!in_array($tab, $allowedTabs)) {
            $tab = 'per_ae_bulan';
        }

        $tahun = (int) $request->get('tahun', $now->year);
        $bulan = $request->filled('bulan') && $request->bulan !== '' ? (int) $request->bulan : $now->month;

        // Ambil daftar Account Executive (AE) / Sales Iklan
        $listAE = Faktur::where('kategori', 'iklan')
            ->whereNotNull('sales_nama')
            ->where('sales_nama', '!=', '')
            ->distinct()
            ->pluck('sales_nama')
            ->toArray();

        $salesKoran = DB::table('transaksikoran')->whereNotNull('sales_iklankoran')->where('sales_iklankoran', '!=', '')->distinct()->pluck('sales_iklankoran')->toArray();
        $salesPriangan = DB::table('transaksipriangan')->whereNotNull('sales_iklanpriangan')->where('sales_iklanpriangan', '!=', '')->distinct()->pluck('sales_iklanpriangan')->toArray();
        $listAE = array_values(array_unique(array_filter(array_merge($listAE, $salesKoran, $salesPriangan, ['Ahmad Fauzi', 'Dedi Gunawan', 'Rina Marlina', 'Iwan Ridwan']))));
        sort($listAE);

        $selectedAE = $request->get('ae', $listAE[0] ?? 'Ahmad Fauzi');

        // Data variabel untuk setiap tab
        $fakturs = null;
        $kpiAE = [];
        $rekapBulananAE = collect();
        $rekapAEBulan = collect();
        $rekapAETahun = collect();
        $rekapPerBulan = collect();
        $rekapPerTahun = collect();
        $rekapPemasang = collect();

        // 1. Tab: Per AE / Bulan
        if ($tab === 'per_ae_bulan') {
            $queryFaktur = Faktur::with(['customer', 'creator'])
                ->where('kategori', 'iklan')
                ->whereYear('tanggal_faktur', $tahun)
                ->whereMonth('tanggal_faktur', $bulan);

            if ($selectedAE !== 'semua') {
                $queryFaktur->where('sales_nama', $selectedAE);
            }

            if ($request->filled('q')) {
                $q = $request->q;
                $queryFaktur->where(function ($s) use ($q) {
                    $s->where('no_faktur', 'like', "%{$q}%")
                      ->orWhereHas('customer', function ($c) use ($q) {
                          $c->where('nama_customer', 'like', "%{$q}%");
                      });
                });
            }

            $kpiAE = [
                'total_faktur'   => (clone $queryFaktur)->count(),
                'seri_awal'      => (clone $queryFaktur)->orderBy('no_faktur')->value('no_faktur') ?? '-',
                'seri_akhir'     => (clone $queryFaktur)->orderByDesc('no_faktur')->value('no_faktur') ?? '-',
                'total_tagihan'  => (float) (clone $queryFaktur)->sum('total_tagihan'),
                'total_terbayar' => (float) (clone $queryFaktur)->sum('total_terbayar'),
                'total_piutang'  => (float) (clone $queryFaktur)->sum('sisa_piutang'),
                'total_reguler'  => (clone $queryFaktur)->where('tipe_faktur', 'reguler')->count(),
                'total_stopper'  => (clone $queryFaktur)->where('tipe_faktur', 'stopper')->count(),
                'total_bonus'    => (clone $queryFaktur)->where('tipe_faktur', 'bonus')->count(),
            ];

            $fakturs = $queryFaktur->orderBy('no_faktur')->paginate(15)->withQueryString();
        }

        // 2. Tab: Per AE / Tahun
        elseif ($tab === 'per_ae_tahun') {
            $rekapBulananAE = Faktur::selectRaw("
                MONTH(tanggal_faktur) as bulan,
                COUNT(*) as total_faktur,
                MIN(no_faktur) as seri_awal,
                MAX(no_faktur) as seri_akhir,
                SUM(CASE WHEN tipe_faktur = 'reguler' THEN 1 ELSE 0 END) as total_reguler,
                SUM(CASE WHEN tipe_faktur = 'stopper' THEN 1 ELSE 0 END) as total_stopper,
                SUM(CASE WHEN tipe_faktur = 'bonus' THEN 1 ELSE 0 END) as total_bonus,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang
            ")
            ->where('kategori', 'iklan')
            ->whereYear('tanggal_faktur', $tahun)
            ->when($selectedAE !== 'semua', function($q) use ($selectedAE) {
                $q->where('sales_nama', $selectedAE);
            })
            ->groupBy(DB::raw("MONTH(tanggal_faktur)"))
            ->orderBy('bulan')
            ->get();

            $queryFakturTahun = Faktur::with(['customer'])
                ->where('kategori', 'iklan')
                ->whereYear('tanggal_faktur', $tahun)
                ->when($selectedAE !== 'semua', function($q) use ($selectedAE) {
                    $q->where('sales_nama', $selectedAE);
                });

            $kpiAE = [
                'total_faktur'   => (clone $queryFakturTahun)->count(),
                'seri_awal'      => (clone $queryFakturTahun)->orderBy('no_faktur')->value('no_faktur') ?? '-',
                'seri_akhir'     => (clone $queryFakturTahun)->orderByDesc('no_faktur')->value('no_faktur') ?? '-',
                'total_tagihan'  => (float) (clone $queryFakturTahun)->sum('total_tagihan'),
                'total_terbayar' => (float) (clone $queryFakturTahun)->sum('total_terbayar'),
                'total_piutang'  => (float) (clone $queryFakturTahun)->sum('sisa_piutang'),
                'total_reguler'  => (clone $queryFakturTahun)->where('tipe_faktur', 'reguler')->count(),
                'total_stopper'  => (clone $queryFakturTahun)->where('tipe_faktur', 'stopper')->count(),
                'total_bonus'    => (clone $queryFakturTahun)->where('tipe_faktur', 'bonus')->count(),
            ];

            $fakturs = $queryFakturTahun->latest('tanggal_faktur')->paginate(15)->withQueryString();
        }

        // 3. Tab: Rekap AE / Bulan
        elseif ($tab === 'rekap_ae_bulan') {
            $rekapAEBulan = Faktur::selectRaw("
                COALESCE(sales_nama, 'Tanpa AE') as sales_nama,
                COUNT(*) as total_faktur,
                MIN(no_faktur) as seri_awal,
                MAX(no_faktur) as seri_akhir,
                SUM(CASE WHEN tipe_faktur = 'reguler' THEN 1 ELSE 0 END) as total_reguler,
                SUM(CASE WHEN tipe_faktur = 'stopper' THEN 1 ELSE 0 END) as total_stopper,
                SUM(CASE WHEN tipe_faktur = 'bonus' THEN 1 ELSE 0 END) as total_bonus,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang
            ")
            ->where('kategori', 'iklan')
            ->whereYear('tanggal_faktur', $tahun)
            ->whereMonth('tanggal_faktur', $bulan)
            ->groupBy('sales_nama')
            ->orderByDesc('total_tagihan')
            ->get();
        }

        // 4. Tab: Rekap AE / Tahun
        elseif ($tab === 'rekap_ae_tahun') {
            $rekapAETahun = Faktur::selectRaw("
                COALESCE(sales_nama, 'Tanpa AE') as sales_nama,
                COUNT(*) as total_faktur,
                MIN(no_faktur) as seri_awal,
                MAX(no_faktur) as seri_akhir,
                SUM(CASE WHEN tipe_faktur = 'reguler' THEN 1 ELSE 0 END) as total_reguler,
                SUM(CASE WHEN tipe_faktur = 'stopper' THEN 1 ELSE 0 END) as total_stopper,
                SUM(CASE WHEN tipe_faktur = 'bonus' THEN 1 ELSE 0 END) as total_bonus,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang
            ")
            ->where('kategori', 'iklan')
            ->whereYear('tanggal_faktur', $tahun)
            ->groupBy('sales_nama')
            ->orderByDesc('total_tagihan')
            ->get();
        }

        // 5. Tab: Per Bulan (Rekap Stok Opname Bulanan)
        elseif ($tab === 'per_bulan') {
            $rekapPerBulan = Faktur::selectRaw("
                MONTH(tanggal_faktur) as bulan,
                COUNT(*) as total_faktur,
                MIN(no_faktur) as seri_awal,
                MAX(no_faktur) as seri_akhir,
                SUM(CASE WHEN tipe_faktur = 'reguler' THEN 1 ELSE 0 END) as total_reguler,
                SUM(CASE WHEN tipe_faktur = 'stopper' THEN 1 ELSE 0 END) as total_stopper,
                SUM(CASE WHEN tipe_faktur = 'bonus' THEN 1 ELSE 0 END) as total_bonus,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang
            ")
            ->where('kategori', 'iklan')
            ->whereYear('tanggal_faktur', $tahun)
            ->groupBy(DB::raw("MONTH(tanggal_faktur)"))
            ->orderBy('bulan')
            ->get();
        }

        // 6. Tab: Per Tahun (Rekap Multi-Tahun)
        elseif ($tab === 'per_tahun') {
            $rekapPerTahun = Faktur::selectRaw("
                YEAR(tanggal_faktur) as tahun,
                COUNT(*) as total_faktur,
                MIN(no_faktur) as seri_awal,
                MAX(no_faktur) as seri_akhir,
                COUNT(DISTINCT sales_nama) as total_ae,
                COUNT(DISTINCT customer_id) as total_pemasang,
                SUM(CASE WHEN tipe_faktur = 'reguler' THEN 1 ELSE 0 END) as total_reguler,
                SUM(CASE WHEN tipe_faktur = 'stopper' THEN 1 ELSE 0 END) as total_stopper,
                SUM(CASE WHEN tipe_faktur = 'bonus' THEN 1 ELSE 0 END) as total_bonus,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang
            ")
            ->where('kategori', 'iklan')
            ->groupBy(DB::raw("YEAR(tanggal_faktur)"))
            ->orderByDesc('tahun')
            ->get();
        }

        // 7. Tab: Per Pemasang
        elseif ($tab === 'per_pemasang') {
            $queryPemasang = Faktur::selectRaw("
                customer_id,
                COUNT(*) as total_faktur,
                MIN(no_faktur) as seri_awal,
                MAX(no_faktur) as seri_akhir,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang,
                GROUP_CONCAT(DISTINCT sales_nama SEPARATOR ', ') as daftar_ae
            ")
            ->with('customer')
            ->where('kategori', 'iklan')
            ->whereYear('tanggal_faktur', $tahun);

            if ($request->filled('bulan') && $request->bulan !== '') {
                $queryPemasang->whereMonth('tanggal_faktur', (int) $request->bulan);
            }

            if ($request->filled('q')) {
                $q = $request->q;
                $queryPemasang->whereHas('customer', function($c) use ($q) {
                    $c->where('nama_customer', 'like', "%{$q}%")
                      ->orWhere('alamat', 'like', "%{$q}%");
                });
            }

            $rekapPemasang = $queryPemasang->groupBy('customer_id')
                ->orderByDesc('total_tagihan')
                ->paginate(15)
                ->withQueryString();
        }

        // Data Audit Fisik Blangko (Modal & Ringkasan Berita Acara)
        $currentMonth = Carbon::now()->format('Y-m');
        $opnames = StokOpnameFaktur::with('petugas')
            ->where('kategori', 'iklan')
            ->latest('tanggal_opname')
            ->paginate(10);

        $periodPrefix = 'INV-IKL-' . Carbon::now()->format('Ym');
        $fakturBulanIniCount = Faktur::where('kategori', 'iklan')
            ->where('no_faktur', 'like', "{$periodPrefix}%")
            ->count();

        $fakturPertama = Faktur::where('kategori', 'iklan')
            ->where('no_faktur', 'like', "{$periodPrefix}%")
            ->orderBy('no_faktur')
            ->value('no_faktur') ?? "{$periodPrefix}-001";

        $fakturTerakhir = Faktur::where('kategori', 'iklan')
            ->where('no_faktur', 'like', "{$periodPrefix}%")
            ->orderByDesc('no_faktur')
            ->value('no_faktur') ?? "{$periodPrefix}-001";

        return view('pages.keuangan.faktur.iklan.stok-opname', compact(
            'tab',
            'tahun',
            'bulan',
            'listAE',
            'selectedAE',
            'fakturs',
            'kpiAE',
            'rekapBulananAE',
            'rekapAEBulan',
            'rekapAETahun',
            'rekapPerBulan',
            'rekapPerTahun',
            'rekapPemasang',
            'opnames',
            'currentMonth',
            'fakturBulanIniCount',
            'fakturPertama',
            'fakturTerakhir'
        ));
    }

    /**
     * Cetak Lembar Laporan Stok Opname Faktur Iklan (Berdasarkan Dimensi Pilihan).
     */
    public function cetakStokOpname(Request $request)
    {
        $now = Carbon::now();
        $tab = $request->get('tab', 'per_ae_bulan');
        $tahun = (int) $request->get('tahun', $now->year);
        $bulan = $request->filled('bulan') && $request->bulan !== '' ? (int) $request->bulan : $now->month;
        $selectedAE = $request->get('ae', 'Ahmad Fauzi');

        $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');
        $periodeJudul = "Periode: Bulan {$namaBulan} Tahun {$tahun}";

        $fakturs = collect();
        $rekapData = collect();
        $kpi = [];

        if ($tab === 'per_ae_bulan') {
            $queryFaktur = Faktur::with(['customer'])
                ->where('kategori', 'iklan')
                ->whereYear('tanggal_faktur', $tahun)
                ->whereMonth('tanggal_faktur', $bulan);

            if ($selectedAE !== 'semua') {
                $queryFaktur->where('sales_nama', $selectedAE);
            }
            $fakturs = $queryFaktur->orderBy('no_faktur')->get();
            $periodeJudul = "Laporan Faktur AE: {$selectedAE} - Bulan {$namaBulan} {$tahun}";
        } elseif ($tab === 'per_ae_tahun') {
            $rekapData = Faktur::selectRaw("
                MONTH(tanggal_faktur) as bulan,
                COUNT(*) as total_faktur,
                MIN(no_faktur) as seri_awal,
                MAX(no_faktur) as seri_akhir,
                SUM(CASE WHEN tipe_faktur = 'reguler' THEN 1 ELSE 0 END) as total_reguler,
                SUM(CASE WHEN tipe_faktur = 'stopper' THEN 1 ELSE 0 END) as total_stopper,
                SUM(CASE WHEN tipe_faktur = 'bonus' THEN 1 ELSE 0 END) as total_bonus,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang
            ")
            ->where('kategori', 'iklan')
            ->whereYear('tanggal_faktur', $tahun)
            ->when($selectedAE !== 'semua', function($q) use ($selectedAE) {
                $q->where('sales_nama', $selectedAE);
            })
            ->groupBy(DB::raw("MONTH(tanggal_faktur)"))
            ->orderBy('bulan')
            ->get();
            $periodeJudul = "Rekapitulasi Faktur AE: {$selectedAE} - Tahun {$tahun}";
        } elseif ($tab === 'rekap_ae_bulan') {
            $rekapData = Faktur::selectRaw("
                COALESCE(sales_nama, 'Tanpa AE') as sales_nama,
                COUNT(*) as total_faktur,
                MIN(no_faktur) as seri_awal,
                MAX(no_faktur) as seri_akhir,
                SUM(CASE WHEN tipe_faktur = 'reguler' THEN 1 ELSE 0 END) as total_reguler,
                SUM(CASE WHEN tipe_faktur = 'stopper' THEN 1 ELSE 0 END) as total_stopper,
                SUM(CASE WHEN tipe_faktur = 'bonus' THEN 1 ELSE 0 END) as total_bonus,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang
            ")
            ->where('kategori', 'iklan')
            ->whereYear('tanggal_faktur', $tahun)
            ->whereMonth('tanggal_faktur', $bulan)
            ->groupBy('sales_nama')
            ->orderByDesc('total_tagihan')
            ->get();
            $periodeJudul = "Rekapitulasi Kinerja Seluruh AE - Bulan {$namaBulan} {$tahun}";
        } elseif ($tab === 'rekap_ae_tahun') {
            $rekapData = Faktur::selectRaw("
                COALESCE(sales_nama, 'Tanpa AE') as sales_nama,
                COUNT(*) as total_faktur,
                MIN(no_faktur) as seri_awal,
                MAX(no_faktur) as seri_akhir,
                SUM(CASE WHEN tipe_faktur = 'reguler' THEN 1 ELSE 0 END) as total_reguler,
                SUM(CASE WHEN tipe_faktur = 'stopper' THEN 1 ELSE 0 END) as total_stopper,
                SUM(CASE WHEN tipe_faktur = 'bonus' THEN 1 ELSE 0 END) as total_bonus,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang
            ")
            ->where('kategori', 'iklan')
            ->whereYear('tanggal_faktur', $tahun)
            ->groupBy('sales_nama')
            ->orderByDesc('total_tagihan')
            ->get();
            $periodeJudul = "Rekapitulasi Tahunan Seluruh AE - Tahun {$tahun}";
        } elseif ($tab === 'per_bulan') {
            $rekapData = Faktur::selectRaw("
                MONTH(tanggal_faktur) as bulan,
                COUNT(*) as total_faktur,
                MIN(no_faktur) as seri_awal,
                MAX(no_faktur) as seri_akhir,
                SUM(CASE WHEN tipe_faktur = 'reguler' THEN 1 ELSE 0 END) as total_reguler,
                SUM(CASE WHEN tipe_faktur = 'stopper' THEN 1 ELSE 0 END) as total_stopper,
                SUM(CASE WHEN tipe_faktur = 'bonus' THEN 1 ELSE 0 END) as total_bonus,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang
            ")
            ->where('kategori', 'iklan')
            ->whereYear('tanggal_faktur', $tahun)
            ->groupBy(DB::raw("MONTH(tanggal_faktur)"))
            ->orderBy('bulan')
            ->get();
            $periodeJudul = "Stok Opname Seri Faktur Bulanan - Tahun {$tahun}";
        } elseif ($tab === 'per_tahun') {
            $rekapData = Faktur::selectRaw("
                YEAR(tanggal_faktur) as tahun,
                COUNT(*) as total_faktur,
                MIN(no_faktur) as seri_awal,
                MAX(no_faktur) as seri_akhir,
                COUNT(DISTINCT sales_nama) as total_ae,
                COUNT(DISTINCT customer_id) as total_pemasang,
                SUM(CASE WHEN tipe_faktur = 'reguler' THEN 1 ELSE 0 END) as total_reguler,
                SUM(CASE WHEN tipe_faktur = 'stopper' THEN 1 ELSE 0 END) as total_stopper,
                SUM(CASE WHEN tipe_faktur = 'bonus' THEN 1 ELSE 0 END) as total_bonus,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang
            ")
            ->where('kategori', 'iklan')
            ->groupBy(DB::raw("YEAR(tanggal_faktur)"))
            ->orderByDesc('tahun')
            ->get();
            $periodeJudul = "Stok Opname Multi-Tahun Pemakaian Seri Faktur Iklan";
        } elseif ($tab === 'per_pemasang') {
            $queryPemasang = Faktur::selectRaw("
                customer_id,
                COUNT(*) as total_faktur,
                MIN(no_faktur) as seri_awal,
                MAX(no_faktur) as seri_akhir,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang,
                GROUP_CONCAT(DISTINCT sales_nama SEPARATOR ', ') as daftar_ae
            ")
            ->with('customer')
            ->where('kategori', 'iklan')
            ->whereYear('tanggal_faktur', $tahun);

            if ($request->filled('bulan') && $request->bulan !== '') {
                $queryPemasang->whereMonth('tanggal_faktur', (int) $request->bulan);
            }
            $rekapData = $queryPemasang->groupBy('customer_id')->orderByDesc('total_tagihan')->get();
            $periodeJudul = "Rekapitulasi Faktur Per Pemasang Iklan - Tahun {$tahun}";
        }

        return view('pages.keuangan.faktur.iklan.cetak-stok-opname', compact(
            'tab',
            'tahun',
            'bulan',
            'selectedAE',
            'fakturs',
            'rekapData',
            'periodeJudul'
        ));
    }

    /**
     * Simpan Catatan Stok Opname Faktur Baru.
     */
    public function storeStokOpname(Request $request)
    {
        $request->validate([
            'tanggal_opname'     => 'required|date',
            'periode_bulan'      => 'required|string|max:7',
            'nomor_seri_awal'    => 'required|string|max:50',
            'nomor_seri_akhir'   => 'required|string|max:50',
            'stok_awal'          => 'required|integer|min:1',
            'jumlah_terpakai'    => 'required|integer|min:0',
            'jumlah_rusak_batal' => 'required|integer|min:0',
            'sisa_blangko_fisik' => 'required|integer|min:0',
            'keterangan'         => 'nullable|string|max:1000',
        ]);

        $stokAwal = (int) $request->stok_awal;
        $terpakai = (int) $request->jumlah_terpakai;
        $rusak = (int) $request->jumlah_rusak_batal;
        $sisaFisik = (int) $request->sisa_blangko_fisik;

        $pertanggungjawaban = $terpakai + $rusak + $sisaFisik;
        $selisih = $stokAwal - $pertanggungjawaban;
        $statusOpname = ($selisih === 0) ? 'sesuai' : 'selisih';

        $noDoc = 'SO-FAK-' . Carbon::parse($request->tanggal_opname)->format('Ym') . '-' . str_pad(StokOpnameFaktur::count() + 1, 3, '0', STR_PAD_LEFT);

        StokOpnameFaktur::create([
            'no_dokumen'         => $noDoc,
            'kategori'           => 'iklan',
            'tanggal_opname'     => $request->tanggal_opname,
            'periode_bulan'      => $request->periode_bulan,
            'nomor_seri_awal'    => $request->nomor_seri_awal,
            'nomor_seri_akhir'   => $request->nomor_seri_akhir,
            'stok_awal'          => $stokAwal,
            'jumlah_terpakai'    => $terpakai,
            'jumlah_rusak_batal' => $rusak,
            'sisa_blangko_fisik' => $sisaFisik,
            'selisih'            => $selisih,
            'status_opname'      => $statusOpname,
            'keterangan'         => $request->keterangan,
            'petugas_id'         => Auth::id(),
        ]);

        return redirect()->route('keuangan.faktur.iklan.stok-opname')
            ->with('success', "Dokumen Stok Opname {$noDoc} berhasil disimpan dengan status: " . strtoupper($statusOpname) . ".");
    }

    // =========================================================================
    // 2. MODUL FAKTUR SIRKULASI
    // =========================================================================

    /**
     * 2.A. Faktur Sirkulasi Keseluruhan (Distribusi Koran, Oplah Harian, Susulan & Insidentil).
     */
    public function sirkulasiIndex(Request $request)
    {
        $query = Faktur::with(['customer', 'creator', 'penugasanAktif.petugas'])
            ->where('kategori', 'sirkulasi');

        // Filter Status Pembayaran
        if ($request->filled('status') && in_array($request->status, ['belum_bayar', 'cicilan', 'lunas'])) {
            $query->where('status', $request->status);
        }

        // Filter Tipe Faktur
        if ($request->filled('tipe_faktur') && in_array($request->tipe_faktur, ['reguler', 'susulan', 'insidentil'])) {
            $query->where('tipe_faktur', $request->tipe_faktur);
        }

        // Filter Rentang Tanggal
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_faktur', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_faktur', '<=', $request->sampai_tanggal);
        }

        // Pencarian No. Faktur atau Nama Agen/Loper
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%");
                    });
            });
        }

        $fakturs = $query->latest('tanggal_faktur')->latest('id')->paginate(12)->withQueryString();

        $baseSirkulasi = Faktur::where('kategori', 'sirkulasi');
        $totalSirkulasiCount    = (clone $baseSirkulasi)->count();
        $totalSirkulasiTagihan  = (float) (clone $baseSirkulasi)->sum('total_tagihan');
        $totalSirkulasiTerbayar = (float) (clone $baseSirkulasi)->sum('total_terbayar');
        $totalSirkulasiPiutang  = (float) (clone $baseSirkulasi)->sum('sisa_piutang');
        $lunasCount             = (clone $baseSirkulasi)->where('status', 'lunas')->count();
        $piutangCount           = (clone $baseSirkulasi)->where('status', '!=', 'lunas')->count();
        $susulanCount           = (clone $baseSirkulasi)->where('tipe_faktur', 'susulan')->count();
        $insidentilCount         = (clone $baseSirkulasi)->where('tipe_faktur', 'insidentil')->count();

        return view('pages.keuangan.faktur.sirkulasi.index', compact(
            'fakturs',
            'totalSirkulasiCount',
            'totalSirkulasiTagihan',
            'totalSirkulasiTerbayar',
            'totalSirkulasiPiutang',
            'lunasCount',
            'piutangCount',
            'susulanCount',
            'insidentilCount'
        ));
    }

    /**
     * 2.B. Faktur Sirkulasi Susulan (Oplah Tambahan / Kiriman Susulan di Luar Jatah Rutin).
     */
    public function sirkulasiSusulan(Request $request)
    {
        $query = Faktur::with(['customer', 'creator', 'penugasanAktif.petugas'])
            ->where('kategori', 'sirkulasi')
            ->where('tipe_faktur', 'susulan');

        if ($request->filled('status') && in_array($request->status, ['belum_bayar', 'cicilan', 'lunas'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_faktur', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_faktur', '<=', $request->sampai_tanggal);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%");
                    });
            });
        }

        $fakturs = $query->latest('tanggal_faktur')->latest('id')->paginate(12)->withQueryString();

        $baseSusulan = Faktur::where('kategori', 'sirkulasi')->where('tipe_faktur', 'susulan');
        $totalSusulanCount    = (clone $baseSusulan)->count();
        $totalSusulanTagihan  = (float) (clone $baseSusulan)->sum('total_tagihan');
        $totalSusulanTerbayar = (float) (clone $baseSusulan)->sum('total_terbayar');
        $totalSusulanPiutang  = (float) (clone $baseSusulan)->sum('sisa_piutang');

        return view('pages.keuangan.faktur.sirkulasi.susulan', compact(
            'fakturs',
            'totalSusulanCount',
            'totalSusulanTagihan',
            'totalSusulanTerbayar',
            'totalSusulanPiutang'
        ));
    }

    /**
     * 2.C. Rekap Faktur Sirkulasi (per Tanggal & per Bulan).
     */
    public function sirkulasiRekap(Request $request)
    {
        $mode = $request->get('mode', 'tanggal'); // 'tanggal' atau 'bulan'
        $now = Carbon::now();

        if ($mode === 'bulan') {
            $tahun = (int) $request->get('tahun', $now->year);

            $rekapData = Faktur::selectRaw("
                MONTH(tanggal_faktur) as bulan,
                COUNT(*) as total_faktur,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang,
                SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang,
                SUM(CASE WHEN tipe_faktur = 'susulan' THEN 1 ELSE 0 END) as total_susulan,
                SUM(CASE WHEN tipe_faktur = 'insidentil' THEN 1 ELSE 0 END) as total_insidentil
            ")
            ->where('kategori', 'sirkulasi')
            ->whereYear('tanggal_faktur', $tahun)
            ->groupBy(DB::raw("MONTH(tanggal_faktur)"))
            ->orderBy('bulan')
            ->get();

            $grandTagihan  = (float) $rekapData->sum('total_tagihan');
            $grandTerbayar = (float) $rekapData->sum('total_terbayar');
            $grandPiutang  = (float) $rekapData->sum('total_piutang');
            $grandFaktur   = (int) $rekapData->sum('total_faktur');

            return view('pages.keuangan.faktur.sirkulasi.rekap', compact(
                'mode',
                'tahun',
                'rekapData',
                'grandTagihan',
                'grandTerbayar',
                'grandPiutang',
                'grandFaktur'
            ));
        }

        // Mode Tanggal (Harian)
        $dariTanggal   = $request->get('dari_tanggal', $now->copy()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->get('sampai_tanggal', $now->copy()->endOfMonth()->toDateString());

        $rekapData = Faktur::selectRaw("
            tanggal_faktur,
            COUNT(*) as total_faktur,
            SUM(total_tagihan) as total_tagihan,
            SUM(total_terbayar) as total_terbayar,
            SUM(sisa_piutang) as total_piutang,
            SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
            SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang,
            SUM(CASE WHEN tipe_faktur = 'susulan' THEN 1 ELSE 0 END) as total_susulan,
            SUM(CASE WHEN tipe_faktur = 'insidentil' THEN 1 ELSE 0 END) as total_insidentil
        ")
        ->where('kategori', 'sirkulasi')
        ->whereBetween('tanggal_faktur', [$dariTanggal, $sampaiTanggal])
        ->groupBy('tanggal_faktur')
        ->orderByDesc('tanggal_faktur')
        ->get();

        $grandTagihan  = (float) $rekapData->sum('total_tagihan');
        $grandTerbayar = (float) $rekapData->sum('total_terbayar');
        $grandPiutang  = (float) $rekapData->sum('total_piutang');
        $grandFaktur   = (int) $rekapData->sum('total_faktur');

        return view('pages.keuangan.faktur.sirkulasi.rekap', compact(
            'mode',
            'dariTanggal',
            'sampaiTanggal',
            'rekapData',
            'grandTagihan',
            'grandTerbayar',
            'grandPiutang',
            'grandFaktur'
        ));
    }

    /**
     * Cetak Lembar Rekapitulasi Faktur Sirkulasi.
     */
    public function cetakRekapSirkulasi(Request $request)
    {
        $mode = $request->get('mode', 'tanggal');
        $now = Carbon::now();

        if ($mode === 'bulan') {
            $tahun = (int) $request->get('tahun', $now->year);
            $rekapData = Faktur::selectRaw("
                MONTH(tanggal_faktur) as bulan,
                COUNT(*) as total_faktur,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang,
                SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang,
                SUM(CASE WHEN tipe_faktur = 'susulan' THEN 1 ELSE 0 END) as total_susulan,
                SUM(CASE WHEN tipe_faktur = 'insidentil' THEN 1 ELSE 0 END) as total_insidentil
            ")
            ->where('kategori', 'sirkulasi')
            ->whereYear('tanggal_faktur', $tahun)
            ->groupBy(DB::raw("MONTH(tanggal_faktur)"))
            ->orderBy('bulan')
            ->get();

            $periodeJudul = "Tahun {$tahun}";
        } else {
            $dariTanggal   = $request->get('dari_tanggal', $now->copy()->startOfMonth()->toDateString());
            $sampaiTanggal = $request->get('sampai_tanggal', $now->copy()->endOfMonth()->toDateString());

            $rekapData = Faktur::selectRaw("
                tanggal_faktur,
                COUNT(*) as total_faktur,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang,
                SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang,
                SUM(CASE WHEN tipe_faktur = 'susulan' THEN 1 ELSE 0 END) as total_susulan,
                SUM(CASE WHEN tipe_faktur = 'insidentil' THEN 1 ELSE 0 END) as total_insidentil
            ")
            ->where('kategori', 'sirkulasi')
            ->whereBetween('tanggal_faktur', [$dariTanggal, $sampaiTanggal])
            ->groupBy('tanggal_faktur')
            ->orderBy('tanggal_faktur')
            ->get();

            $periodeJudul = Carbon::parse($dariTanggal)->translatedFormat('d F Y') . ' s/d ' . Carbon::parse($sampaiTanggal)->translatedFormat('d F Y');
        }

        $grandTagihan  = (float) $rekapData->sum('total_tagihan');
        $grandTerbayar = (float) $rekapData->sum('total_terbayar');
        $grandPiutang  = (float) $rekapData->sum('total_piutang');
        $grandFaktur   = (int) $rekapData->sum('total_faktur');

        return view('pages.keuangan.faktur.sirkulasi.cetak-rekap', compact(
            'mode',
            'periodeJudul',
            'rekapData',
            'grandTagihan',
            'grandTerbayar',
            'grandPiutang',
            'grandFaktur'
        ));
    }

    /**
     * 2.D. Faktur Sirkulasi Insidentil (Eceran Langsung / Pembelian Borongan Acara / Event / Dinas).
     */
    public function sirkulasiInsidentil(Request $request)
    {
        $query = Faktur::with(['customer', 'creator', 'penugasanAktif.petugas'])
            ->where('kategori', 'sirkulasi')
            ->where('tipe_faktur', 'insidentil');

        if ($request->filled('status') && in_array($request->status, ['belum_bayar', 'cicilan', 'lunas'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_faktur', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_faktur', '<=', $request->sampai_tanggal);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%");
                    });
            });
        }

        $fakturs = $query->latest('tanggal_faktur')->latest('id')->paginate(12)->withQueryString();

        $baseInsidentil = Faktur::where('kategori', 'sirkulasi')->where('tipe_faktur', 'insidentil');
        $totalInsidentilCount    = (clone $baseInsidentil)->count();
        $totalInsidentilTagihan  = (float) (clone $baseInsidentil)->sum('total_tagihan');
        $totalInsidentilTerbayar = (float) (clone $baseInsidentil)->sum('total_terbayar');
        $totalInsidentilPiutang  = (float) (clone $baseInsidentil)->sum('sisa_piutang');

        return view('pages.keuangan.faktur.sirkulasi.insidentil', compact(
            'fakturs',
            'totalInsidentilCount',
            'totalInsidentilTagihan',
            'totalInsidentilTerbayar',
            'totalInsidentilPiutang'
        ));
    }

    // =========================================================================
    // 3. PENERBITAN FAKTUR & OPERASI UMUM
    // =========================================================================

    /**
     * API Pencarian Data Transaksi Divisi Iklan untuk Autofill Faktur Keuangan.
     */
    public function cariFakturIklan(Request $request)
    {
        $noFaktur = trim($request->get('no_faktur', ''));

        if (empty($noFaktur)) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan masukkan nomor faktur dari divisi iklan.',
            ], 422);
        }

        // Cari transaksi di ketiga tabel: Koran, Online, Priangan TV
        // 1. Transaksi Koran
        $koran = TransaksiKoran::with('iklankoran')
            ->where('nofakturkoran', $noFaktur)
            ->orWhereRaw('LOWER(TRIM(nofakturkoran)) = ?', [strtolower($noFaktur)])
            ->first();

        // 2. Transaksi Online
        $online = null;
        if (!$koran) {
            $online = TransaksiOnline::with('iklanonline')
                ->where('nofakturonline', $noFaktur)
                ->orWhereRaw('LOWER(TRIM(nofakturonline)) = ?', [strtolower($noFaktur)])
                ->first();
        }

        // 3. Transaksi Priangan TV
        $priangan = null;
        if (!$koran && !$online) {
            $priangan = TransaksiPriangan::with('iklanpriangan')
                ->where('nofakturpriangan', $noFaktur)
                ->orWhereRaw('LOWER(TRIM(nofakturpriangan)) = ?', [strtolower($noFaktur)])
                ->first();
        }

        if (!$koran && !$online && !$priangan) {
            return response()->json([
                'success' => false,
                'message' => "Faktur Iklan dengan nomor '{$noFaktur}' tidak ditemukan di transaksi Koran, Online, maupun Priangan TV.",
            ], 404);
        }

        if ($koran) {
            $media             = 'Koran';
            $noFakturDitemukan = $koran->nofakturkoran;
            $namaCustomer      = trim($koran->nama_pemasangkoran);
            $alamatCustomer    = trim($koran->alamat_pemasangkoran ?? '');
            $salesNama         = trim($koran->sales_iklankoran ?? '');
            $tanggalTransaksi  = $koran->tanggal_transaksikoran ? Carbon::parse($koran->tanggal_transaksikoran)->toDateString() : date('Y-m-d');
            
            $namaJenis = $koran->iklankoran->nama_jenis ?? 'Iklan Koran';
            $rincianDesc = "Iklan Koran: {$namaJenis}";
            $detailRincian = [];
            if (!empty($koran->halaman_iklan)) $detailRincian[] = $koran->halaman_iklan;
            if (!empty($koran->warna_iklan)) $detailRincian[] = $koran->warna_iklan;
            if (!empty($koran->ukuran_iklan)) $detailRincian[] = $koran->ukuran_iklan;
            if (!empty($detailRincian)) {
                $rincianDesc .= ' (' . implode(', ', $detailRincian) . ')';
            }

            $qty          = max(1, (int) ($koran->total_muatkoran ?? 1));
            $totalTagihan = (float) ($koran->totaltagihan_transaksikoran ?: $koran->harga_transaksikoran);
            $jumlahBayar  = (float) ($koran->jumlahbayar_transaksikoran ?? 0);
            $piutang      = (float) ($koran->piutang_transaksikoran ?? 0);

        } elseif ($online) {
            $media             = 'Online';
            $noFakturDitemukan = $online->nofakturonline;
            $namaCustomer      = trim($online->nama_pemasangonline);
            $alamatCustomer    = trim($online->alamat_pemasangonline ?? '');
            $salesNama         = trim($online->sales_iklanonline ?? '');
            $tanggalTransaksi  = $online->tanggal_transaksionline ? Carbon::parse($online->tanggal_transaksionline)->toDateString() : date('Y-m-d');

            $namaJenis   = $online->iklanonline->nama_jenis ?? 'Iklan Online';
            $portal      = $online->portal_iklanonline ?: 'kabarpriangan.pikiran-rakyat.com';
            $rincianDesc = "Iklan Online: {$namaJenis} (Portal: {$portal})";

            $qty          = max(1, (int) ($online->total_muatiklanonline ?? 1));
            $totalTagihan = (float) ($online->totaltagihan_transaksionline ?: $online->harga_transaksionline);
            $jumlahBayar  = (float) ($online->jumlahbayar_transaksionline ?? 0);
            $piutang      = (float) ($online->piutang_transaksionline ?? 0);

        } else {
            $media             = 'Priangan TV';
            $noFakturDitemukan = $priangan->nofakturpriangan;
            $namaCustomer      = trim($priangan->nama_pemasangpriangan);
            $alamatCustomer    = trim($priangan->alamat_pemasangpriangan ?? '');
            $salesNama         = trim($priangan->sales_iklanpriangan ?? '');
            $tanggalTransaksi  = $priangan->tanggal_transaksipriangan ? Carbon::parse($priangan->tanggal_transaksipriangan)->toDateString() : date('Y-m-d');

            $namaJenis   = $priangan->iklanpriangan->nama_jenis ?? 'Iklan Siaran TV';
            $rincianDesc = "Iklan Priangan TV: {$namaJenis}";

            $qty          = max(1, (int) ($priangan->total_muatiklanpriangan ?? 1));
            $totalTagihan = (float) ($priangan->totaltagihan_transaksipriangan ?: $priangan->harga_transaksipriangan);
            $jumlahBayar  = (float) ($priangan->jumlahbayar_transaksipriangan ?? 0);
            $piutang      = (float) ($priangan->piutang_transaksipriangan ?? 0);
        }

        // Cari atau buat otomatis master Customer
        $customer = Customer::where('nama_customer', $namaCustomer)->first();
        if (!$customer) {
            $customer = Customer::create([
                'nama_customer' => $namaCustomer,
                'no_telepon'    => '-',
                'alamat'        => $alamatCustomer ?: 'Tasikmalaya',
                'tipe'          => 'iklan',
            ]);
        }

        // Hitung harga satuan dan item qty
        if ($qty > 0 && ((int) $totalTagihan % $qty === 0)) {
            $unitPrice = (int) ($totalTagihan / $qty);
            $itemQty = $qty;
        } else {
            $unitPrice = (int) $totalTagihan;
            $itemQty = 1;
            if ($qty > 1) {
                $rincianDesc .= " ({$qty}x Muat)";
            }
        }

        // Tipe pembayaran berdasarkan sisa piutang di transaksi iklan
        $isLunas = ($piutang <= 0 && $jumlahBayar >= $totalTagihan);
        $tipePembayaran = $isLunas ? 'lunas_langsung' : 'cicilan_tempo';
        $nominalDp = $isLunas ? 0 : $jumlahBayar;

        // Cek apakah sudah pernah didaftarkan di tabel faktur keuangan
        $existingFaktur = Faktur::where('no_faktur', $noFakturDitemukan)
            ->orWhere('catatan', 'like', "%{$noFakturDitemukan}%")
            ->first();

        return response()->json([
            'success'            => true,
            'media'              => $media,
            'no_faktur'          => $noFakturDitemukan,
            'customer_id'        => $customer->id,
            'nama_customer'      => $customer->nama_customer,
            'alamat_customer'    => $customer->alamat,
            'sales_nama'         => $salesNama,
            'tanggal_faktur'     => $tanggalTransaksi,
            'jatuh_tempo'        => Carbon::parse($tanggalTransaksi)->addDays(14)->toDateString(),
            'items'              => [
                [
                    'nama'     => $rincianDesc,
                    'qty'      => $itemQty,
                    'harga'    => $unitPrice,
                    'subtotal' => (int) $totalTagihan,
                ]
            ],
            'total_tagihan'      => $totalTagihan,
            'jumlah_bayar'       => $jumlahBayar,
            'piutang'            => $piutang,
            'tipe_pembayaran'    => $tipePembayaran,
            'nominal_dp'         => $nominalDp,
            'catatan'            => "Faktur Divisi Iklan {$media} (No. {$noFakturDitemukan})",
            'already_registered' => (bool) $existingFaktur,
            'registered_no'      => $existingFaktur ? $existingFaktur->no_faktur : null,
        ]);
    }

    /**
     * Form Penerbitan Faktur Baru.
     */
    public function create(Request $request)
    {
        $kategoriDefault   = $request->get('kategori', 'iklan');
        $tipeFakturDefault = $request->get('tipe_faktur', 'reguler');
        $customers = Customer::orderBy('nama_customer')->get();

        // Format Penomoran
        $period = date('Ym');
        if ($kategoriDefault === 'sirkulasi') {
            if ($tipeFakturDefault === 'susulan') {
                $prefix = 'INV-SRK-SUS-';
            } elseif ($tipeFakturDefault === 'insidentil') {
                $prefix = 'INV-SRK-INS-';
            } else {
                $prefix = 'INV-SRK-';
            }
            $count = Faktur::where('no_faktur', 'like', "{$prefix}{$period}-%")->count() + 1;
            $noFakturSuggested = $prefix . $period . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        } else {
            // Standar Industri Iklan: Seragam dengan Transaksi Iklan (FK/YYYY/MM/NNNN)
            $noFakturSuggested = \App\Services\NomorDokumenService::previewFaktur();
        }

        // Ambil daftar AE
        $aeList = User::whereIn('role', ['iklan', 'petugas_inkaso'])->pluck('name')
            ->merge(Faktur::whereNotNull('sales_nama')->pluck('sales_nama'))
            ->merge(TransaksiKoran::pluck('sales_iklankoran'))
            ->merge(TransaksiOnline::pluck('sales_iklanonline'))
            ->merge(TransaksiPriangan::pluck('sales_iklanpriangan'))
            ->filter()->unique()->values()->toArray();

        // Ambil daftar Faktur Iklan untuk autocomplete / bantuan pilihan
        $fakturKoran = TransaksiKoran::latest('id')->take(20)->get()->map(function($t) {
            return [
                'no_faktur' => $t->nofakturkoran,
                'media'     => 'Koran',
                'pemasang'  => $t->nama_pemasangkoran,
                'sales'     => $t->sales_iklankoran,
                'tagihan'   => (float) $t->totaltagihan_transaksikoran,
                'piutang'   => (float) $t->piutang_transaksikoran,
                'tanggal'   => $t->tanggal_transaksikoran,
            ];
        });
        $fakturOnline = TransaksiOnline::latest('id')->take(20)->get()->map(function($t) {
            return [
                'no_faktur' => $t->nofakturonline,
                'media'     => 'Online',
                'pemasang'  => $t->nama_pemasangonline,
                'sales'     => $t->sales_iklanonline,
                'tagihan'   => (float) $t->totaltagihan_transaksionline,
                'piutang'   => (float) $t->piutang_transaksionline,
                'tanggal'   => $t->tanggal_transaksionline,
            ];
        });
        $fakturPriangan = TransaksiPriangan::latest('id')->take(20)->get()->map(function($t) {
            return [
                'no_faktur' => $t->nofakturpriangan,
                'media'     => 'Priangan TV',
                'pemasang'  => $t->nama_pemasangpriangan,
                'sales'     => $t->sales_iklanpriangan,
                'tagihan'   => (float) $t->totaltagihan_transaksipriangan,
                'piutang'   => (float) $t->piutang_transaksipriangan,
                'tanggal'   => $t->tanggal_transaksipriangan,
            ];
        });
        $fakturIklanList = $fakturKoran->merge($fakturOnline)->merge($fakturPriangan)->sortByDesc('tanggal')->values();

        return view('pages.keuangan.faktur.create', compact(
            'customers',
            'kategoriDefault',
            'tipeFakturDefault',
            'noFakturSuggested',
            'aeList',
            'fakturIklanList'
        ));
    }

    /**
     * Simpan Penerbitan Faktur Baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_faktur'        => 'required|string|unique:faktur,no_faktur',
            'customer_id'      => 'required|exists:customers,id',
            'kategori'         => 'required|in:iklan,sirkulasi',
            'tipe_faktur'      => 'nullable|in:reguler,susulan,insidentil,stopper,bonus',
            'tanggal_faktur'   => 'required|date',
            'jatuh_tempo'      => 'required|date|after_or_equal:tanggal_faktur',
            'item_nama'        => 'required|array|min:1',
            'item_qty'         => 'required|array|min:1',
            'item_harga'       => 'required|array|min:1',
            'tipe_pembayaran'  => 'required|in:lunas_langsung,cicilan_tempo',
            'nominal_dp'       => 'nullable|numeric|min:0',
            'metode_bayar'     => 'required|in:tunai,transfer',
            'catatan'          => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $tipeFaktur = $request->input('tipe_faktur', 'reguler');
            if ($request->kategori === 'sirkulasi') {
                if (!in_array($tipeFaktur, ['reguler', 'susulan', 'insidentil'])) {
                    $tipeFaktur = 'reguler';
                }
            } else {
                if (!in_array($tipeFaktur, ['reguler', 'susulan', 'stopper', 'bonus'])) {
                    $tipeFaktur = 'reguler';
                }
            }

            // 1. Susun Rincian Biaya
            $rincianBiaya = [];
            $totalTagihan = 0.0;
            foreach ($request->item_nama as $index => $nama) {
                if (trim($nama) === '') continue;
                $qty = max(1, (float) ($request->item_qty[$index] ?? 1));
                $harga = max(0, (float) ($request->item_harga[$index] ?? 0));
                $subtotal = $qty * $harga;
                $totalTagihan += $subtotal;

                $rincianBiaya[] = [
                    'item'     => $nama,
                    'qty'      => $qty,
                    'harga'    => $harga,
                    'subtotal' => $subtotal,
                ];
            }

            if (empty($rincianBiaya) || $totalTagihan <= 0) {
                return back()->withInput()->with('error', 'Minimal harus ada 1 item transaksi dengan harga lebih dari 0.');
            }

            $userId = Auth::id();
            $customer = Customer::findOrFail($request->customer_id);

            // Tentukan No. Faktur Terpadu
            $noFaktur = $request->no_faktur;
            if ($request->kategori === 'iklan') {
                if (empty($noFaktur) || str_contains($noFaktur, 'XXXX') || Faktur::where('no_faktur', $noFaktur)->exists()) {
                    $noFaktur = \App\Services\NomorDokumenService::generateFaktur();
                }
            }

            // 2. Evaluasi Logika Pembayaran
            if ($request->tipe_pembayaran === 'lunas_langsung') {
                $totalTerbayar = $totalTagihan;
                $sisaPiutang   = 0.0;
                $status        = 'lunas';

                $faktur = Faktur::create([
                    'no_faktur'      => $noFaktur,
                    'customer_id'    => $customer->id,
                    'sales_nama'     => $request->sales_nama,
                    'kategori'       => $request->kategori,
                    'tipe_faktur'    => $tipeFaktur,
                    'tanggal_faktur' => $request->tanggal_faktur,
                    'jatuh_tempo'    => $request->jatuh_tempo,
                    'rincian_biaya'  => $rincianBiaya,
                    'total_tagihan'  => $totalTagihan,
                    'total_terbayar' => $totalTerbayar,
                    'sisa_piutang'   => $sisaPiutang,
                    'status'         => $status,
                    'created_by'     => $userId,
                    'catatan'        => $request->catatan,
                ]);

                // Catat Kwitansi Pelunasan
                $kwitansiNo = 'KW-' . date('Ym') . '-' . str_pad(PembayaranFaktur::count() + 1, 4, '0', STR_PAD_LEFT);
                $bayar = PembayaranFaktur::create([
                    'no_kwitansi'         => $kwitansiNo,
                    'faktur_id'           => $faktur->id,
                    'penugasan_inkaso_id' => null,
                    'tanggal_bayar'       => $request->tanggal_faktur,
                    'nominal_bayar'       => $totalTagihan,
                    'metode_bayar'        => $request->metode_bayar,
                    'tipe_bayar'          => 'lunas_langsung',
                    'diterima_oleh'       => $userId,
                    'catatan'             => 'Pelunasan 100% langsung saat penerbitan faktur',
                ]);

                // Kas Masuk Otomatis
                KasTransaksi::catatMutasi(
                    'masuk',
                    'Pelunasan Faktur ' . ucfirst($request->kategori),
                    $totalTagihan,
                    'Pelunasan langsung Faktur ' . $faktur->no_faktur . ' (' . $customer->nama_customer . ')',
                    $bayar,
                    $userId,
                    $request->tanggal_faktur
                );

                $pesan = 'Faktur ' . $faktur->no_faktur . ' berhasil diterbitkan dan LUNAS.';
            } else {
                $dp = (float) ($request->nominal_dp ?? 0);
                if ($dp > $totalTagihan) {
                    $dp = $totalTagihan;
                }

                $totalTerbayar = $dp;
                $sisaPiutang   = max(0, $totalTagihan - $dp);
                $status        = ($sisaPiutang <= 0) ? 'lunas' : (($dp > 0) ? 'cicilan' : 'belum_bayar');

                $faktur = Faktur::create([
                    'no_faktur'      => $noFaktur,
                    'customer_id'    => $customer->id,
                    'sales_nama'     => $request->sales_nama,
                    'kategori'       => $request->kategori,
                    'tipe_faktur'    => $tipeFaktur,
                    'tanggal_faktur' => $request->tanggal_faktur,
                    'jatuh_tempo'    => $request->jatuh_tempo,
                    'rincian_biaya'  => $rincianBiaya,
                    'total_tagihan'  => $totalTagihan,
                    'total_terbayar' => $totalTerbayar,
                    'sisa_piutang'   => $sisaPiutang,
                    'status'         => $status,
                    'created_by'     => $userId,
                    'catatan'        => $request->catatan,
                ]);

                if ($dp > 0) {
                    $kwitansiNo = 'KW-' . date('Ym') . '-' . str_pad(PembayaranFaktur::count() + 1, 4, '0', STR_PAD_LEFT);
                    $bayarDp = PembayaranFaktur::create([
                        'no_kwitansi'         => $kwitansiNo,
                        'faktur_id'           => $faktur->id,
                        'penugasan_inkaso_id' => null,
                        'tanggal_bayar'       => $request->tanggal_faktur,
                        'nominal_bayar'       => $dp,
                        'metode_bayar'        => $request->metode_bayar,
                        'tipe_bayar'          => 'cicilan_inkaso',
                        'diterima_oleh'       => $userId,
                        'catatan'             => 'Uang Muka (DP) saat penerbitan faktur',
                    ]);

                    KasTransaksi::catatMutasi(
                        'masuk',
                        'Uang Muka (DP) Faktur ' . ucfirst($request->kategori),
                        $dp,
                        'Penerimaan DP Faktur ' . $faktur->no_faktur . ' (' . $customer->nama_customer . ')',
                        $bayarDp,
                        $userId,
                        $request->tanggal_faktur
                    );
                }

                $pesan = 'Faktur ' . $faktur->no_faktur . ' berhasil diterbitkan dengan sisa piutang Rp ' . number_format($sisaPiutang, 0, ',', '.') . '.';
            }

            return redirect()->route('keuangan.faktur.show', $faktur->id)->with('success', $pesan);
        });
    }

    /**
     * Detail Rincian Faktur, Pembayaran, dan Status Inkaso.
     */
    public function show($id)
    {
        $faktur = Faktur::with([
            'customer',
            'creator',
            'pembayaran.penerima',
            'penugasanInkaso.petugas',
        ])->findOrFail($id);

        return view('pages.keuangan.faktur.show', compact('faktur'));
    }

    /**
     * Cetak Lembar Faktur Standar Kabar Priangan.
     */
    public function cetak($id)
    {
        $faktur = Faktur::with(['customer', 'creator', 'pembayaran'])->findOrFail($id);
        return view('pages.keuangan.faktur.cetak', compact('faktur'));
    }

    /**
     * Modul Fungsi & Utilitas Faktur
     */
    public function fungsiIndex()
    {
        return view('pages.keuangan.alur');
    }

    /**
     * Sinkronisasi data transaksi ke faktur
     */
    public function syncTransaksi(Request $request)
    {
        return redirect()->route('keuangan.faktur.iklan.index')->with('success', 'Data transaksi berhasil disinkronisasi ke modul faktur.');
    }
}

