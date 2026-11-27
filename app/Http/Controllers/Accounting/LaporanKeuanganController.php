<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\AkunPerkiraan;
use App\Models\JurnalUmumDetail;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanKeuanganController extends Controller
{
    public function __construct(protected AccountingService $accountingService)
    {
    }

    /**
     * Laporan Laba Rugi (100% GL-Driven dari JurnalUmum berstatus POSTED + Sub-Ledger Guard).
     */
    public function labaRugi(Request $request)
    {
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));

        $glMap = JurnalUmumDetail::whereHas('jurnal', fn ($q) => $q->where('status', 'posted')->where('tanggal', 'like', "{$periode}%"))
            ->selectRaw('kode_perkiraan, COALESCE(SUM(debet), 0) as total_debet, COALESCE(SUM(kredit), 0) as total_kredit')
            ->groupBy('kode_perkiraan')
            ->get();

        $pendapatanIklan = 0.0;
        $pendapatanSirkulasi = 0.0;
        $pendapatanLain = 0.0;
        $returIklan = 0.0;
        $returSirkulasi = 0.0;
        $hppCetak = 0.0;
        $bebanKasKeluar = 0.0;
        $bebanPenyusutan = 0.0;
        $bebanLain = 0.0;

        $kodeSusut = $this->accountingService->getMappedAccount('beban_penyusutan')['kode'];

        foreach ($glMap as $row) {
            $kode = (string) $row->kode_perkiraan;
            $d = (float) $row->total_debet;
            $k = (float) $row->total_kredit;
            $netKredit = $k - $d;
            $netDebet = $d - $k;

            if (str_starts_with($kode, '4.00.01.') || str_starts_with($kode, '4.00.02.') || str_starts_with($kode, '4.00.03.')) {
                $pendapatanIklan += $netKredit;
            } elseif (str_starts_with($kode, '4.00.04.')) {
                $pendapatanSirkulasi += $netKredit;
            } elseif (str_starts_with($kode, '4.00.05.01.01')) {
                $returIklan += $netDebet;
            } elseif (str_starts_with($kode, '4.00.05.01.02')) {
                $returSirkulasi += $netDebet;
            } elseif (str_starts_with($kode, '4.') || str_starts_with($kode, '7.')) {
                $pendapatanLain += $netKredit;
            } elseif (str_starts_with($kode, '5.')) {
                $hppCetak += $netDebet;
            } elseif ($kode === $kodeSusut || str_starts_with($kode, '6.00.22.')) {
                $bebanPenyusutan += $netDebet;
            } elseif (str_starts_with($kode, '6.')) {
                $bebanKasKeluar += $netDebet;
            } elseif (str_starts_with($kode, '8.')) {
                $bebanLain += $netDebet;
            }
        }

        $pendapatanIklan = $pendapatanIklan + $pendapatanLain;
        $totalPendapatanKotor = $pendapatanIklan + $pendapatanSirkulasi;
        $totalRetur = $returIklan + $returSirkulasi;
        $pendapatanBersih = max(0, $totalPendapatanKotor - $totalRetur);

        $labaKotor = $pendapatanBersih - $hppCetak;
        $bebanKasKeluar = $bebanKasKeluar + $bebanLain;
        $totalBebanOperasional = $bebanKasKeluar + $bebanPenyusutan;
        $labaOperasional = $labaKotor - $totalBebanOperasional;
        $labaBersih = $labaOperasional;

        $unpostedSummary = $this->accountingService->getUnpostedSubledgerSummary($periode);

        return view('pages.accounting.laporan.laba_rugi', compact(
            'periode',
            'pendapatanIklan',
            'pendapatanSirkulasi',
            'totalPendapatanKotor',
            'returIklan',
            'returSirkulasi',
            'totalRetur',
            'pendapatanBersih',
            'hppCetak',
            'labaKotor',
            'bebanKasKeluar',
            'bebanPenyusutan',
            'totalBebanOperasional',
            'labaOperasional',
            'labaBersih',
            'unpostedSummary'
        ));
    }

    /**
     * Neraca Keuangan (100% GL-Driven: Saldo Awal COA + Mutasi Posted s.d. akhir periode).
     */
    public function neraca(Request $request)
    {
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));
        $endDate = Carbon::parse($periode . '-01')->endOfMonth()->toDateString();

        $masterCoa = AkunPerkiraan::aktif()->get()->keyBy('kode_perkiraan');
        $glCumulative = JurnalUmumDetail::whereHas('jurnal', fn ($q) => $q->where('status', 'posted')->whereDate('tanggal', '<=', $endDate))
            ->selectRaw('kode_perkiraan, COALESCE(SUM(debet), 0) as total_debet, COALESCE(SUM(kredit), 0) as total_kredit')
            ->groupBy('kode_perkiraan')
            ->get()
            ->keyBy('kode_perkiraan');

        $allCodes = $masterCoa->keys()->merge($glCumulative->keys())->unique();

        $kasOperasional = 0.0;
        $piutangIklan = 0.0;
        $piutangSirkulasi = 0.0;
        $asetLancarLain = 0.0;
        $hargaPerolehanAset = 0.0;
        $akumulasiPenyusutan = 0.0;
        $utangUsaha = 0.0;
        $utangPajak = 0.0;
        $modalSaham = 0.0;
        $labaDitahanGl = 0.0;
        $labaRugiAkumulasiGl = 0.0;

        $kodePiutangIklan = $this->accountingService->getMappedAccount('piutang_iklan')['kode'];
        $kodePiutangSirk = $this->accountingService->getMappedAccount('piutang_sirkulasi')['kode'];

        foreach ($allCodes as $kode) {
            $akun = $masterCoa->get($kode);
            $gl = $glCumulative->get($kode);
            $masterAwal = (float) ($akun?->saldo_awal ?? 0);
            $isKreditNormal = ($akun?->saldo_normal === 'kredit');

            $d = (float) ($gl->total_debet ?? 0) + (!$isKreditNormal && $masterAwal != 0 ? $masterAwal : 0);
            $k = (float) ($gl->total_kredit ?? 0) + ($isKreditNormal && $masterAwal != 0 ? $masterAwal : 0);
            $netDebet = $d - $k;
            $netKredit = $k - $d;

            if (str_starts_with($kode, '1.00.00.') || str_starts_with($kode, '1.00.01.') || str_starts_with($kode, '1.02.')) {
                $kasOperasional += $netDebet;
            } elseif ($kode === $kodePiutangIklan || str_starts_with($kode, '1.00.04.01.01.01.')) {
                $piutangIklan += $netDebet;
            } elseif ($kode === $kodePiutangSirk || str_starts_with($kode, '1.00.04.')) {
                $piutangSirkulasi += $netDebet;
            } elseif (str_starts_with($kode, '1.00.05.') || str_starts_with($kode, '1.00.06.') || str_starts_with($kode, '1.00.07.') || str_starts_with($kode, '1.00.08.')) {
                $asetLancarLain += $netDebet;
            } elseif (str_starts_with($kode, '1.00.10.')) {
                $hargaPerolehanAset += $netDebet;
            } elseif (str_starts_with($kode, '1.00.11.')) {
                $akumulasiPenyusutan += $netKredit;
            } elseif (str_starts_with($kode, '1.')) {
                $asetLancarLain += $netDebet;
            } elseif (str_starts_with($kode, '2.00.03.')) {
                $utangPajak += $netKredit;
            } elseif (str_starts_with($kode, '2.')) {
                $utangUsaha += $netKredit;
            } elseif (str_starts_with($kode, '3.00.01.')) {
                $modalSaham += $netKredit;
            } elseif (str_starts_with($kode, '3.')) {
                $labaDitahanGl += $netKredit;
            } elseif (str_starts_with($kode, '4.') || str_starts_with($kode, '7.')) {
                $labaRugiAkumulasiGl += $netKredit;
            } elseif (str_starts_with($kode, '5.') || str_starts_with($kode, '6.') || str_starts_with($kode, '8.')) {
                $labaRugiAkumulasiGl -= $netDebet;
            }
        }

        // Gabungkan aset lancar lain (termasuk PPh 23 / Biaya Dibayar Dimuka / Persediaan) ke piutang atau tampilkan proporsional tanpa merusak keseimbangan
        $piutangIklan = $piutangIklan + $asetLancarLain;
        $totalAsetLancar = $kasOperasional + $piutangIklan + $piutangSirkulasi;

        // Jika belum ada jurnal perolehan 1.00.10 di GL tetapi ada akumulasi penyusutan & register aset tetap aktif, tampilkan nilai aset register hanya sebagai referensi terpisah; Neraca GL murni memakai saldo akun 1.00.10 & 1.00.11
        $nilaiBukuAset = $hargaPerolehanAset - $akumulasiPenyusutan;
        $totalAset = $totalAsetLancar + $nilaiBukuAset;

        $totalKewajiban = $utangUsaha + $utangPajak;
        $labaDitahan = $labaDitahanGl + $labaRugiAkumulasiGl;
        $totalEkuitas = $modalSaham + $labaDitahan;

        $unpostedSummary = $this->accountingService->getUnpostedSubledgerSummary($periode);

        return view('pages.accounting.laporan.neraca', compact(
            'periode',
            'kasOperasional',
            'piutangIklan',
            'piutangSirkulasi',
            'totalAsetLancar',
            'hargaPerolehanAset',
            'akumulasiPenyusutan',
            'nilaiBukuAset',
            'totalAset',
            'utangUsaha',
            'utangPajak',
            'totalKewajiban',
            'modalSaham',
            'labaDitahan',
            'totalEkuitas',
            'unpostedSummary'
        ));
    }

    public function arusKas(Request $request)
    {
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));
        $prevPeriode = Carbon::parse($periode . '-01')->subMonth()->format('Y-m');

        $kmScope = fn () => $this->accountingService->kasMasukAktif();
        $kkScope = fn () => $this->accountingService->kasKeluarBebanAktif();

        $penerimaanIklan = (float) $kmScope()
            ->where('tipe_transaksi', 'iklan')
            ->where(function ($q) use ($periode) {
                $q->where('tgl_transaksi', 'like', "{$periode}%")
                  ->orWhere('tanggal_terima', 'like', "{$periode}%");
            })->sum('jumlah_dibayar');

        $penerimaanSirkulasi = (float) $kmScope()
            ->where('tipe_transaksi', 'sirkulasi')
            ->where(function ($q) use ($periode) {
                $q->where('tgl_transaksi', 'like', "{$periode}%")
                  ->orWhere('tanggal_terima', 'like', "{$periode}%");
            })->sum('jumlah_dibayar');

        $penerimaanLainnya = (float) $kmScope()
            ->whereNotIn('tipe_transaksi', ['iklan', 'sirkulasi'])
            ->where(function ($q) use ($periode) {
                $q->where('tgl_transaksi', 'like', "{$periode}%")
                  ->orWhere('tanggal_terima', 'like', "{$periode}%");
            })->sum('jumlah_dibayar');

        $penerimaanPelanggan = $penerimaanIklan + $penerimaanSirkulasi + $penerimaanLainnya;

        $pengeluaranKomisi = (float) $kkScope()
            ->where(function ($q) {
                $q->where('kategori_beban', 'like', '%komisi%')
                  ->orWhere('kategori', 'like', '%komisi%');
            })
            ->where('tanggal_keluar', 'like', "{$periode}%")
            ->sum('nominal_keluar');

        $pengeluaranOperasionalLain = (float) $kkScope()
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('kategori_beban')
                        ->orWhere('kategori_beban', 'not like', '%komisi%');
                })->where(function ($sub) {
                    $sub->whereNull('kategori')
                        ->orWhere('kategori', 'not like', '%komisi%');
                });
            })
            ->where('tanggal_keluar', 'like', "{$periode}%")
            ->sum('nominal_keluar');

        $pembayaranOperasional = $pengeluaranKomisi + $pengeluaranOperasionalLain;
        $arusKasOperasi = $penerimaanPelanggan - $pembayaranOperasional;

        $assets = $this->accountingService->getDaftarAsetTetap();
        $pembelianAsetTetap = 0.0;
        foreach ($assets as $a) {
            if (isset($a['tgl_perolehan']) && str_starts_with($a['tgl_perolehan'], $periode)) {
                $pembelianAsetTetap += (float) ($a['harga_beli'] ?? 0);
            }
        }
        $arusKasInvestasi = -$pembelianAsetTetap;
        $arusKasPendanaan = 0.0;
        $kenaikanBersihKas = $arusKasOperasi + $arusKasInvestasi + $arusKasPendanaan;

        $saldoAwalKasMasuk = (float) $kmScope()
            ->where(function ($q) use ($periode) {
                $q->where('tgl_transaksi', '<', "{$periode}-01")
                  ->orWhere('tanggal_terima', '<', "{$periode}-01");
            })->sum('jumlah_dibayar');

        $saldoAwalKasKeluar = (float) $kkScope()
            ->where('tanggal_keluar', '<', "{$periode}-01")
            ->sum('nominal_keluar');

        $saldoAwalKas = max(0, $saldoAwalKasMasuk - $saldoAwalKasKeluar);
        $saldoAkhirKas = $saldoAwalKas + $kenaikanBersihKas;

        $prevPenerimaanIklan = (float) $kmScope()
            ->where('tipe_transaksi', 'iklan')
            ->where(function ($q) use ($prevPeriode) {
                $q->where('tgl_transaksi', 'like', "{$prevPeriode}%")
                  ->orWhere('tanggal_terima', 'like', "{$prevPeriode}%");
            })->sum('jumlah_dibayar');

        $prevPenerimaanSirkulasi = (float) $kmScope()
            ->where('tipe_transaksi', 'sirkulasi')
            ->where(function ($q) use ($prevPeriode) {
                $q->where('tgl_transaksi', 'like', "{$prevPeriode}%")
                  ->orWhere('tanggal_terima', 'like', "{$prevPeriode}%");
            })->sum('jumlah_dibayar');

        $prevPenerimaanLainnya = (float) $kmScope()
            ->whereNotIn('tipe_transaksi', ['iklan', 'sirkulasi'])
            ->where(function ($q) use ($prevPeriode) {
                $q->where('tgl_transaksi', 'like', "{$prevPeriode}%")
                  ->orWhere('tanggal_terima', 'like', "{$prevPeriode}%");
            })->sum('jumlah_dibayar');

        $prevTotalKasMasuk = $prevPenerimaanIklan + $prevPenerimaanSirkulasi + $prevPenerimaanLainnya;

        $prevPengeluaranKomisi = (float) $kkScope()
            ->where(function ($q) {
                $q->where('kategori_beban', 'like', '%komisi%')
                  ->orWhere('kategori', 'like', '%komisi%');
            })
            ->where('tanggal_keluar', 'like', "{$prevPeriode}%")
            ->sum('nominal_keluar');

        $prevPengeluaranOps = (float) $kkScope()
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('kategori_beban')
                        ->orWhere('kategori_beban', 'not like', '%komisi%');
                })->where(function ($sub) {
                    $sub->whereNull('kategori')
                        ->orWhere('kategori', 'not like', '%komisi%');
                });
            })
            ->where('tanggal_keluar', 'like', "{$prevPeriode}%")
            ->sum('nominal_keluar');

        $prevTotalKasKeluar = $prevPengeluaranKomisi + $prevPengeluaranOps;
        $prevArusKasOperasi = $prevTotalKasMasuk - $prevTotalKasKeluar;

        $prevPembelianAset = 0.0;
        foreach ($assets as $a) {
            if (isset($a['tgl_perolehan']) && str_starts_with($a['tgl_perolehan'], $prevPeriode)) {
                $prevPembelianAset += (float) ($a['harga_beli'] ?? 0);
            }
        }
        $prevArusKasInvestasi = -$prevPembelianAset;
        $prevArusKasPendanaan = 0.0;
        $prevKenaikanBersihKas = $prevArusKasOperasi + $prevArusKasInvestasi + $prevArusKasPendanaan;

        $prevSaldoAwalMasuk = (float) $kmScope()
            ->where(function ($q) use ($prevPeriode) {
                $q->where('tgl_transaksi', '<', "{$prevPeriode}-01")
                  ->orWhere('tanggal_terima', '<', "{$prevPeriode}-01");
            })->sum('jumlah_dibayar');
        $prevSaldoAwalKeluar = (float) $kkScope()
            ->where('tanggal_keluar', '<', "{$prevPeriode}-01")
            ->sum('nominal_keluar');
        $prevSaldoAwalKas = max(0, $prevSaldoAwalMasuk - $prevSaldoAwalKeluar);
        $prevSaldoAkhirKas = $prevSaldoAwalKas + $prevKenaikanBersihKas;

        $transaksiMasuk = $kmScope()
            ->where(function ($q) use ($periode) {
                $q->where('tgl_transaksi', 'like', "{$periode}%")
                  ->orWhere('tanggal_terima', 'like', "{$periode}%");
            })
            ->orderByDesc('tgl_transaksi')
            ->orderByDesc('id')
            ->take(20)
            ->get();

        $transaksiKeluar = $kkScope()
            ->where('tanggal_keluar', 'like', "{$periode}%")
            ->orderByDesc('tanggal_keluar')
            ->orderByDesc('id')
            ->take(20)
            ->get();

        return view('pages.accounting.laporan.arus_kas', compact(
            'periode',
            'prevPeriode',
            'penerimaanPelanggan',
            'penerimaanIklan',
            'penerimaanSirkulasi',
            'penerimaanLainnya',
            'pembayaranOperasional',
            'pengeluaranKomisi',
            'pengeluaranOperasionalLain',
            'pembelianAsetTetap',
            'arusKasOperasi',
            'arusKasInvestasi',
            'arusKasPendanaan',
            'kenaikanBersihKas',
            'saldoAwalKas',
            'saldoAkhirKas',
            'prevTotalKasMasuk',
            'prevPenerimaanIklan',
            'prevPenerimaanSirkulasi',
            'prevPenerimaanLainnya',
            'prevTotalKasKeluar',
            'prevPengeluaranKomisi',
            'prevPengeluaranOps',
            'prevArusKasOperasi',
            'prevPembelianAset',
            'prevArusKasInvestasi',
            'prevArusKasPendanaan',
            'prevKenaikanBersihKas',
            'prevSaldoAwalKas',
            'prevSaldoAkhirKas',
            'transaksiMasuk',
            'transaksiKeluar'
        ));
    }

    public function ekuitas(Request $request)
    {
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));
        $endDate = Carbon::parse($periode . '-01')->endOfMonth()->toDateString();
        $lrData = $this->labaRugi($request)->getData();

        $kodeModal = $this->accountingService->getMappedAccount('modal_disetor')['kode'];
        $akunModal = AkunPerkiraan::where('kode_perkiraan', $kodeModal)->first();

        $glModal = (float) JurnalUmumDetail::whereHas('jurnal', fn ($q) => $q->where('status', 'posted')->whereDate('tanggal', '<=', $endDate))
            ->where('kode_perkiraan', 'like', '3.00.01%')
            ->selectRaw('COALESCE(SUM(kredit) - SUM(debet), 0) as net')
            ->value('net');

        $modalAwal = (float) ($akunModal?->saldo_awal ?? 0) + $glModal;
        $labaBersih = (float) ($lrData['labaOperasional'] ?? 0);
        $priveDividen = 0.0;
        $modalAkhir = $modalAwal + $labaBersih - $priveDividen;

        return view('pages.accounting.laporan.ekuitas', compact(
            'periode',
            'modalAwal',
            'labaBersih',
            'priveDividen',
            'modalAkhir'
        ));
    }

    public function rincianBeban(Request $request)
    {
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));

        $daftarBeban = $this->accountingService->kasKeluarBebanAktif()
            ->where('tanggal_keluar', 'like', "{$periode}%")
            ->latest('id')
            ->get();

        $totalBeban = $daftarBeban->sum('nominal_keluar');

        $glBebanSummary = JurnalUmumDetail::whereHas('jurnal', fn ($q) => $q->where('status', 'posted')->where('tanggal', 'like', "{$periode}%"))
            ->where(fn ($q) => $q->where('kode_perkiraan', 'like', '5.%')->orWhere('kode_perkiraan', 'like', '6.%'))
            ->selectRaw('kode_perkiraan, nama_perkiraan, COALESCE(SUM(debet) - SUM(kredit), 0) as total_gl')
            ->groupBy('kode_perkiraan', 'nama_perkiraan')
            ->orderByDesc('total_gl')
            ->get();

        return view('pages.accounting.laporan.rincian_beban', compact(
            'periode',
            'daftarBeban',
            'totalBeban',
            'glBebanSummary'
        ));
    }

    public function cetakNeraca(Request $request)
    {
        $response = $this->neraca($request);
        return view('pages.accounting.laporan.cetak.neraca', $response->getData());
    }

    public function cetakLabaRugi(Request $request)
    {
        $response = $this->labaRugi($request);
        return view('pages.accounting.laporan.cetak.laba_rugi', $response->getData());
    }

    public function cetakArusKas(Request $request)
    {
        $response = $this->arusKas($request);
        return view('pages.accounting.laporan.cetak.arus_kas', $response->getData());
    }

    public function cetakEkuitas(Request $request)
    {
        $response = $this->ekuitas($request);
        return view('pages.accounting.laporan.cetak.ekuitas', $response->getData());
    }

    public function cetakRincianBeban(Request $request)
    {
        $response = $this->rincianBeban($request);
        return view('pages.accounting.laporan.cetak.rincian_beban', $response->getData());
    }
}
