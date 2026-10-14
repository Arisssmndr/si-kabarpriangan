<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use App\Models\KasTransaksi;
use App\Models\PembayaranFaktur;
use App\Models\PenugasanInkaso;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KasController extends Controller
{
    /**
     * Buku Kas (Treasury) - Mutasi Kas Real-Time dengan Running Balance.
     */
    public function index(Request $request)
    {
        $query = KasTransaksi::with(['creator']);

        // Filter Rentang Tanggal
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        // Filter Jenis
        if ($request->filled('jenis') && in_array($request->jenis, ['masuk', 'keluar'])) {
            $query->where('jenis', $request->jenis);
        }

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_kas', 'like', '%' . $request->kategori . '%');
        }

        $transaksis = $query->latest('id')->paginate(15)->withQueryString();

        // Rekapitulasi Kas Kumulatif & Periode Berjalan
        $today = Carbon::today()->toDateString();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        $totalMasuk = (float) KasTransaksi::where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = (float) KasTransaksi::where('jenis', 'keluar')->sum('nominal');
        $saldoSaatIni = (float) (KasTransaksi::latest('id')->value('saldo_akhir') ?? 0);

        $masukHariIni = (float) KasTransaksi::where('jenis', 'masuk')->whereDate('tanggal', $today)->sum('nominal');
        $keluarHariIni = (float) KasTransaksi::where('jenis', 'keluar')->whereDate('tanggal', $today)->sum('nominal');

        $masukBulanIni = (float) KasTransaksi::where('jenis', 'masuk')->whereYear('tanggal', $thisYear)->whereMonth('tanggal', $thisMonth)->sum('nominal');
        $keluarBulanIni = (float) KasTransaksi::where('jenis', 'keluar')->whereYear('tanggal', $thisYear)->whereMonth('tanggal', $thisMonth)->sum('nominal');

        // Rekap Filter Periode (jika tanggal dipilih)
        $filterMasuk = (float) (clone $query)->where('jenis', 'masuk')->sum('nominal');
        $filterKeluar = (float) (clone $query)->where('jenis', 'keluar')->sum('nominal');

        $kategoriList = KasTransaksi::select('kategori_kas')->distinct()->pluck('kategori_kas');

        return view('pages.keuangan.kas.index', compact(
            'transaksis',
            'totalMasuk',
            'totalKeluar',
            'saldoSaatIni',
            'masukHariIni',
            'keluarHariIni',
            'masukBulanIni',
            'keluarBulanIni',
            'filterMasuk',
            'filterKeluar',
            'kategoriList'
        ));
    }

    /**
     * Laporan Kas Harian.
     */
    public function harian(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::now()->toDateString());

        $transaksis = KasTransaksi::with(['creator'])
            ->whereDate('tanggal', $tanggal)
            ->orderBy('id', 'asc')
            ->get();

        $totalMasuk = (float) $transaksis->where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = (float) $transaksis->where('jenis', 'keluar')->sum('nominal');
        $surplusDefisit = $totalMasuk - $totalKeluar;

        // Saldo awal sebelum tanggal ini
        $lastBefore = KasTransaksi::whereDate('tanggal', '<', $tanggal)->latest('id')->first();
        $saldoAwal = $lastBefore ? (float) $lastBefore->saldo_akhir : 0.0;
        $saldoAkhir = $saldoAwal + $surplusDefisit;

        return view('pages.keuangan.kas.harian', compact(
            'tanggal',
            'transaksis',
            'totalMasuk',
            'totalKeluar',
            'surplusDefisit',
            'saldoAwal',
            'saldoAkhir'
        ));
    }

    /**
     * Laporan Kas Bulanan.
     */
    public function bulanan(Request $request)
    {
        $bulan = (int) $request->input('bulan', Carbon::now()->month);
        $tahun = (int) $request->input('tahun', Carbon::now()->year);

        $transaksis = KasTransaksi::with(['creator'])
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalMasuk = (float) $transaksis->where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = (float) $transaksis->where('jenis', 'keluar')->sum('nominal');
        $surplusDefisit = $totalMasuk - $totalKeluar;

        // Rekapitulasi per Kategori
        $rekapKategoriMasuk = $transaksis->where('jenis', 'masuk')->groupBy('kategori_kas')->map(fn($group) => $group->sum('nominal'));
        $rekapKategoriKeluar = $transaksis->where('jenis', 'keluar')->groupBy('kategori_kas')->map(fn($group) => $group->sum('nominal'));

        return view('pages.keuangan.kas.bulanan', compact(
            'bulan',
            'tahun',
            'transaksis',
            'totalMasuk',
            'totalKeluar',
            'surplusDefisit',
            'rekapKategoriMasuk',
            'rekapKategoriKeluar'
        ));
    }

    /**
     * Cetak Laporan Kas (Harian atau Bulanan).
     */
    public function cetak(Request $request)
    {
        $mode = $request->input('mode', 'harian');

        if ($mode === 'bulanan') {
            $bulan = (int) $request->input('bulan', Carbon::now()->month);
            $tahun = (int) $request->input('tahun', Carbon::now()->year);

            $transaksis = KasTransaksi::with(['creator'])
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $totalMasuk = (float) $transaksis->where('jenis', 'masuk')->sum('nominal');
            $totalKeluar = (float) $transaksis->where('jenis', 'keluar')->sum('nominal');

            return view('pages.keuangan.kas.cetak-bulanan', compact('bulan', 'tahun', 'transaksis', 'totalMasuk', 'totalKeluar'));
        }

        $tanggal = $request->input('tanggal', Carbon::now()->toDateString());
        $transaksis = KasTransaksi::with(['creator'])
            ->whereDate('tanggal', $tanggal)
            ->orderBy('id', 'asc')
            ->get();

        $totalMasuk = (float) $transaksis->where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = (float) $transaksis->where('jenis', 'keluar')->sum('nominal');

        $lastBefore = KasTransaksi::whereDate('tanggal', '<', $tanggal)->latest('id')->first();
        $saldoAwal = $lastBefore ? (float) $lastBefore->saldo_akhir : 0.0;
        $saldoAkhir = $saldoAwal + ($totalMasuk - $totalKeluar);

        return view('pages.keuangan.kas.cetak-harian', compact('tanggal', 'transaksis', 'totalMasuk', 'totalKeluar', 'saldoAwal', 'saldoAkhir'));
    }

    /**
     * Halaman Pusat Laporan Keuangan Terpadu (3 Tab: Kas Harian, Kas Bulanan, Rekapitulasi Inkaso)
     */
    public function laporan(Request $request)
    {
        $tab = $request->input('tab', 'harian');
        $now = Carbon::now();

        // 1. Data Tab Kas Harian
        $tanggalHarian = $request->input('tanggal', $now->toDateString());
        $txHarian = KasTransaksi::with(['creator'])
            ->whereDate('tanggal', $tanggalHarian)
            ->orderBy('id', 'asc')
            ->get();
        $totalMasukHarian = (float) $txHarian->where('jenis', 'masuk')->sum('nominal');
        $totalKeluarHarian = (float) $txHarian->where('jenis', 'keluar')->sum('nominal');
        $surplusDefisitHarian = $totalMasukHarian - $totalKeluarHarian;
        $lastBeforeHarian = KasTransaksi::whereDate('tanggal', '<', $tanggalHarian)->latest('id')->first();
        $saldoAwalHarian = $lastBeforeHarian ? (float) $lastBeforeHarian->saldo_akhir : 0.0;
        $saldoAkhirHarian = $saldoAwalHarian + $surplusDefisitHarian;

        // 2. Data Tab Kas Bulanan
        $bulanBulanan = (int) $request->input('bulan', $now->month);
        $tahunBulanan = (int) $request->input('tahun', $now->year);
        $txBulanan = KasTransaksi::with(['creator'])
            ->whereYear('tanggal', $tahunBulanan)
            ->whereMonth('tanggal', $bulanBulanan)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        $totalMasukBulanan = (float) $txBulanan->where('jenis', 'masuk')->sum('nominal');
        $totalKeluarBulanan = (float) $txBulanan->where('jenis', 'keluar')->sum('nominal');
        $surplusDefisitBulanan = $totalMasukBulanan - $totalKeluarBulanan;
        $rekapKategoriMasuk = $txBulanan->where('jenis', 'masuk')->groupBy('kategori_kas')->map(fn($g) => $g->sum('nominal'));
        $rekapKategoriKeluar = $txBulanan->where('jenis', 'keluar')->groupBy('kategori_kas')->map(fn($g) => $g->sum('nominal'));

        // 3. Data Tab Rekapitulasi Penagihan Inkaso
        $dariInkaso = $request->input('dari', $now->copy()->startOfMonth()->toDateString());
        $sampaiInkaso = $request->input('sampai', $now->toDateString());
        $petugasId = $request->input('petugas_id');

        $inkasoQuery = PenugasanInkaso::with(['faktur.customer', 'petugas', 'pembayaran'])
            ->whereBetween('tanggal_tugas', [$dariInkaso, $sampaiInkaso]);

        if ($petugasId) {
            $inkasoQuery->where('petugas_id', $petugasId);
        }

        $penugasanInkasoList = $inkasoQuery->latest('tanggal_tugas')->get();

        $totalTargetInkaso = (float) $penugasanInkasoList->sum(fn($p) => $p->faktur ? (float) $p->faktur->total_tagihan : 0);
        $totalTertagihInkaso = (float) $penugasanInkasoList->sum(fn($p) => (float) $p->pembayaran->sum('nominal_bayar'));
        $totalSisaPiutangInkaso = (float) $penugasanInkasoList->sum(fn($p) => $p->faktur ? (float) $p->faktur->sisa_piutang : 0);
        $persenTertagih = $totalTargetInkaso > 0 ? round(($totalTertagihInkaso / $totalTargetInkaso) * 100, 1) : 0;

        $daftarPetugas = User::where('role', 'petugas_inkaso')->orWhere('role', 'administrator')->orderBy('name')->get();

        return view('pages.keuangan.kas.laporan-terpadu', compact(
            'tab',
            // Harian
            'tanggalHarian',
            'txHarian',
            'totalMasukHarian',
            'totalKeluarHarian',
            'surplusDefisitHarian',
            'saldoAwalHarian',
            'saldoAkhirHarian',
            // Bulanan
            'bulanBulanan',
            'tahunBulanan',
            'txBulanan',
            'totalMasukBulanan',
            'totalKeluarBulanan',
            'surplusDefisitBulanan',
            'rekapKategoriMasuk',
            'rekapKategoriKeluar',
            // Inkaso
            'dariInkaso',
            'sampaiInkaso',
            'petugasId',
            'penugasanInkasoList',
            'totalTargetInkaso',
            'totalTertagihInkaso',
            'totalSisaPiutangInkaso',
            'persenTertagih',
            'daftarPetugas'
        ));
    }

    /**
     * Cetak Rekapitulasi Penagihan Inkaso
     */
    public function cetakRekapInkaso(Request $request)
    {
        $now = Carbon::now();
        $dari = $request->input('dari', $now->copy()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', $now->toDateString());
        $petugasId = $request->input('petugas_id');

        $query = PenugasanInkaso::with(['faktur.customer', 'petugas', 'pembayaran'])
            ->whereBetween('tanggal_tugas', [$dari, $sampai]);

        if ($petugasId) {
            $query->where('petugas_id', $petugasId);
        }

        $list = $query->latest('tanggal_tugas')->get();
        $petugas = $petugasId ? User::find($petugasId) : null;

        $totalTarget = (float) $list->sum(fn($p) => $p->faktur ? (float) $p->faktur->total_tagihan : 0);
        $totalTertagih = (float) $list->sum(fn($p) => (float) $p->pembayaran->sum('nominal_bayar'));
        $totalSisa = (float) $list->sum(fn($p) => $p->faktur ? (float) $p->faktur->sisa_piutang : 0);

        return view('pages.keuangan.inkaso.cetak-rekap', compact(
            'dari',
            'sampai',
            'list',
            'petugas',
            'totalTarget',
            'totalTertagih',
            'totalSisa'
        ));
    }
}
