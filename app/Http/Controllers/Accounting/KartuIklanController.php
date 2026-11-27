<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\AuditLogAkuntansi;
use App\Models\DailyCutoff;
use App\Models\Faktur;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumDetail;
use App\Models\KoreksiPiutang;
use App\Models\LogHitungAkuntansi;
use App\Models\MemoUnlock;
use App\Models\PaketIklan;
use App\Models\PenugasanInkaso;
use App\Models\PeriodeAkuntansi;
use App\Models\ShiftKasir;
use App\Models\SistemConfig;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\FakturSyncService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KartuIklanController extends Controller
{
    public function __construct(protected AccountingService $accountingService)
    {
    }

    public function iklanHitungPenjualan(Request $request)
    {
        $tanggalPilihan = $request->input('tanggal', Carbon::today()->toDateString());
        $sudahDihitung = LogHitungAkuntansi::sudahDihitung('penjualan_iklan', $tanggalPilihan);
        $logHitung = LogHitungAkuntansi::where('jenis', 'penjualan_iklan')
            ->whereDate('tanggal_transaksi', $tanggalPilihan)
            ->first();

        $q = trim((string) $request->input('q', ''));
        $kanal = $request->input('kanal', 'all');
        $status = $request->input('status', 'all');

        $daftarFaktur = collect();

        // 1. Kanal Koran
        if ($kanal === 'all' || $kanal === 'koran') {
            $queryKoran = TransaksiKoran::whereDate('tanggal_muatkoran', $tanggalPilihan)
                ->where('status_order', '!=', 'Batal');

            if ($q !== '') {
                $queryKoran->where(function ($sub) use ($q) {
                    $sub->where('nofakturkoran', 'like', "%{$q}%")
                        ->orWhere('nama_pemasangkoran', 'like', "%{$q}%");
                });
            }

            if ($status === 'lunas') {
                $queryKoran->where(function ($sub) {
                    $sub->where('piutang_transaksikoran', '<=', 0)
                        ->orWhere('jenis_pembayaran', 'like', '%tunai%')
                        ->orWhere('jenis_pembayaran', 'like', '%muka%');
                });
            } elseif ($status === 'piutang') {
                $queryKoran->where('piutang_transaksikoran', '>', 0);
            }

            foreach ($queryKoran->get() as $item) {
                $isUm = ($item->piutang_transaksikoran <= 0 || strtolower((string) $item->jenis_pembayaran) === 'tunai' || str_contains(strtolower((string) $item->jenis_pembayaran), 'muka'));
                $daftarFaktur->push((object) [
                    'id'                     => $item->id,
                    'no_faktur'              => $item->nofakturkoran,
                    'tanggal'                => $item->tanggal_muatkoran,
                    'kode_perkiraan_display' => $isUm ? '2.00.01.01.01.01.00001' : '1.00.04.01.01.01.00001',
                    'tipe_akun_display'      => $isUm ? 'Uang Muka' : 'Piutang',
                    'nama_pemasang'          => $item->nama_pemasangkoran,
                    'jenis_iklan'            => 'Koran (' . ($item->kategori_form ?: 'Display') . ')',
                    'kanal'                  => 'koran',
                    'tarif_bruto'            => (float) $item->harga_transaksikoran,
                    'ppn'                    => (float) $item->ppn_transaksikoran,
                    'total_netto'            => (float) $item->totaltagihan_transaksikoran,
                    'piutang'                => (float) $item->piutang_transaksikoran,
                    'is_lunas'               => $isUm,
                ]);
            }
        }

        // 2. Kanal Online
        if ($kanal === 'all' || $kanal === 'online') {
            $queryOnline = TransaksiOnline::where(function ($sub) use ($tanggalPilihan) {
                $sub->whereDate('tanggal_muatiklanonline', $tanggalPilihan)
                    ->orWhereDate('tanggal_transaksionline', $tanggalPilihan);
            })->where(fn ($sub) => $sub->whereNull('status_produksi')->orWhere('status_produksi', '!=', 'batal'));

            if ($q !== '') {
                $queryOnline->where(function ($sub) use ($q) {
                    $sub->where('nofakturonline', 'like', "%{$q}%")
                        ->orWhere('nama_pemasangonline', 'like', "%{$q}%");
                });
            }

            if ($status === 'lunas') {
                $queryOnline->where('piutang_transaksionline', '<=', 0);
            } elseif ($status === 'piutang') {
                $queryOnline->where('piutang_transaksionline', '>', 0);
            }

            foreach ($queryOnline->get() as $item) {
                $isUm = ($item->piutang_transaksionline <= 0);
                $daftarFaktur->push((object) [
                    'id'                     => $item->id,
                    'no_faktur'              => $item->nofakturonline,
                    'tanggal'                => $item->tanggal_muatiklanonline ?: $item->tanggal_transaksionline,
                    'kode_perkiraan_display' => $isUm ? '2.00.01.01.01.01.00001' : '1.00.04.01.01.01.00002',
                    'tipe_akun_display'      => $isUm ? 'Uang Muka' : 'Piutang',
                    'nama_pemasang'          => $item->nama_pemasangonline,
                    'jenis_iklan'            => 'Online Web Portal',
                    'kanal'                  => 'online',
                    'tarif_bruto'            => max(0, (float) $item->totaltagihan_transaksionline - (float) $item->ppn_transaksionline),
                    'ppn'                    => (float) $item->ppn_transaksionline,
                    'total_netto'            => (float) $item->totaltagihan_transaksionline,
                    'piutang'                => (float) $item->piutang_transaksionline,
                    'is_lunas'               => $isUm,
                ]);
            }
        }

        // 3. Kanal Priangan TV
        if ($kanal === 'all' || $kanal === 'priangan' || $kanal === 'tv') {
            $queryTv = TransaksiPriangan::where(function ($sub) use ($tanggalPilihan) {
                $sub->whereDate('tanggal_muatiklanpriangan', $tanggalPilihan)
                    ->orWhereDate('tanggal_transaksipriangan', $tanggalPilihan);
            })->where(fn ($sub) => $sub->whereNull('status_tayang')->orWhere('status_tayang', '!=', 'batal'));

            if ($q !== '') {
                $queryTv->where(function ($sub) use ($q) {
                    $sub->where('nofakturpriangan', 'like', "%{$q}%")
                        ->orWhere('nama_pemasangpriangan', 'like', "%{$q}%");
                });
            }

            if ($status === 'lunas') {
                $queryTv->where('piutang_transaksipriangan', '<=', 0);
            } elseif ($status === 'piutang') {
                $queryTv->where('piutang_transaksipriangan', '>', 0);
            }

            foreach ($queryTv->get() as $item) {
                $isUm = ($item->piutang_transaksipriangan <= 0);
                $daftarFaktur->push((object) [
                    'id'                     => $item->id,
                    'no_faktur'              => $item->nofakturpriangan,
                    'tanggal'                => $item->tanggal_muatiklanpriangan ?: $item->tanggal_transaksipriangan,
                    'kode_perkiraan_display' => $isUm ? '2.00.01.01.01.01.00001' : '1.00.04.01.01.01.00003',
                    'tipe_akun_display'      => $isUm ? 'Uang Muka' : 'Piutang',
                    'nama_pemasang'          => $item->nama_pemasangpriangan,
                    'jenis_iklan'            => 'Priangan TV Broadcast',
                    'kanal'                  => 'priangan',
                    'tarif_bruto'            => max(0, (float) $item->totaltagihan_transaksipriangan - (float) $item->ppn_transaksipriangan),
                    'ppn'                    => (float) $item->ppn_transaksipriangan,
                    'total_netto'            => (float) $item->totaltagihan_transaksipriangan,
                    'piutang'                => (float) $item->piutang_transaksipriangan,
                    'is_lunas'               => $isUm,
                ]);
            }
        }

        $daftarKoran = $daftarFaktur;
        $totalCount = $daftarFaktur->count();
        $totalBruto = (float) $daftarFaktur->sum('tarif_bruto');
        $totalPpn = (float) $daftarFaktur->sum('ppn');
        $totalNetto = (float) $daftarFaktur->sum('total_netto');
        $totalNominal = $totalNetto;
        $totalFaktur = $totalCount;

        return view('pages.accounting.iklan.hitung_penjualan', compact(
            'tanggalPilihan',
            'sudahDihitung',
            'logHitung',
            'daftarKoran',
            'daftarFaktur',
            'totalCount',
            'totalBruto',
            'totalPpn',
            'totalNetto',
            'totalNominal',
            'totalFaktur',
            'q',
            'kanal',
            'status'
        ));
    }

    public function prosesIklanHitungPenjualan(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $periodeKey = Carbon::parse($tanggal)->format('Y-m');

        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Pembentukan jurnal penjualan ditolak.");
        }

        $todayStr = now('Asia/Jakarta')->toDateString();
        if ($tanggal === $todayStr && !DailyCutoff::isDateLocked($tanggal) && !SistemConfig::isPastCutoff()) {
            $jamCutoff = SistemConfig::getJamCutoff();
            return redirect()->back()->with('error', "Penjualan tanggal hari ini ({$tanggal}) belum melewati jam cut-off ({$jamCutoff} WIB). Penarikan omset penjualan iklan hanya dapat dilakukan setelah batas cut-off tercapai atau tanggal berganti.");
        }

        if (LogHitungAkuntansi::sudahDihitung('penjualan_iklan', $tanggal)) {
            return redirect()->back()->with('error', "Penjualan tanggal {$tanggal} sudah pernah dihitung.");
        }

        $daftarKoran = TransaksiKoran::whereDate('tanggal_muatkoran', $tanggal)
            ->where('status_order', '!=', 'Batal')
            ->get();

        $daftarOnline = TransaksiOnline::where(function ($q) use ($tanggal) {
                $q->whereDate('tanggal_muatiklanonline', $tanggal)
                  ->orWhereDate('tanggal_transaksionline', $tanggal);
            })
            ->where(fn ($q) => $q->whereNull('status_produksi')->orWhere('status_produksi', '!=', 'batal'))
            ->get();

        $daftarTv = TransaksiPriangan::where(function ($q) use ($tanggal) {
                $q->whereDate('tanggal_muatiklanpriangan', $tanggal)
                  ->orWhereDate('tanggal_transaksipriangan', $tanggal);
            })
            ->where(fn ($q) => $q->whereNull('status_tayang')->orWhere('status_tayang', '!=', 'batal'))
            ->get();

        $tagihanKoran = (float) $daftarKoran->sum('totaltagihan_transaksikoran');
        $ppnKoran = (float) $daftarKoran->sum('ppn_transaksikoran');
        $dppKoran = max(0, $tagihanKoran - $ppnKoran);

        $tagihanOnline = (float) $daftarOnline->sum('totaltagihan_transaksionline');
        $ppnOnline = (float) $daftarOnline->sum('ppn_transaksionline');
        $dppOnline = max(0, $tagihanOnline - $ppnOnline);

        $tagihanTv = (float) $daftarTv->sum('totaltagihan_transaksipriangan');
        $ppnTv = (float) $daftarTv->sum('ppn_transaksipriangan');
        $dppTv = max(0, $tagihanTv - $ppnTv);

        $totalNominal = $tagihanKoran + $tagihanOnline + $tagihanTv;
        $totalPpn = $ppnKoran + $ppnOnline + $ppnTv;
        $totalFaktur = $daftarKoran->count() + $daftarOnline->count() + $daftarTv->count();

        if ($totalNominal <= 0) {
            return redirect()->back()->with('error', "Tidak ada transaksi muat iklan pada tanggal {$tanggal} untuk dihitung.");
        }

        DB::transaction(function () use (
            $tanggal, $totalFaktur, $totalNominal, $totalPpn, $dppKoran, $dppOnline, $dppTv,
            $tagihanKoran, $tagihanOnline, $tagihanTv, $daftarKoran, $daftarOnline, $daftarTv
        ) {
            LogHitungAkuntansi::create([
                'jenis'             => 'penjualan_iklan',
                'tanggal_transaksi' => $tanggal,
                'total_faktur'      => $totalFaktur,
                'total_nominal'     => $totalNominal,
                'status'            => 'sukses',
                'user_id'           => auth()->id() ?? 1,
                'catatan'           => "Penarikan penjualan iklan harian {$totalFaktur} order (Koran: Rp " . number_format($tagihanKoran, 0, ',', '.') . ", Online: Rp " . number_format($tagihanOnline, 0, ',', '.') . ", TV: Rp " . number_format($tagihanTv, 0, ',', '.') . ").",
            ]);

            $noJurnal = JurnalUmum::generateNomorJurnal($tanggal);
            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $tanggal,
                'tipe'         => 'auto',
                'sumber'       => 'iklan',
                'referensi'    => "MUAT/{$tanggal}",
                'keterangan'   => "Penjualan Iklan Multi-Kanal Tanggal {$tanggal}",
                'total_debet'  => $totalNominal,
                'total_kredit' => $totalNominal,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            $urutan = 1;
            $akunPiutangIklan = $this->accountingService->getMappedAccount('piutang_iklan');
            $akunPpnKeluaran = $this->accountingService->getMappedAccount('ppn_keluaran');
            $akunPendapatanKoran = $this->accountingService->getMappedAccount('pendapatan_koran');
            $akunPendapatanOnline = $this->accountingService->getMappedAccount('pendapatan_online');
            $akunPendapatanTv = $this->accountingService->getMappedAccount('pendapatan_tv');

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $akunPiutangIklan['kode'],
                'nama_perkiraan' => $akunPiutangIklan['nama'],
                'debet'          => $totalNominal,
                'kredit'         => 0,
                'urutan'         => $urutan++,
            ]);

            if ($totalPpn > 0) {
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => $akunPpnKeluaran['kode'],
                    'nama_perkiraan' => $akunPpnKeluaran['nama'],
                    'debet'          => 0,
                    'kredit'         => $totalPpn,
                    'urutan'         => $urutan++,
                ]);
            }

            if ($dppKoran > 0) {
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => $akunPendapatanKoran['kode'],
                    'nama_perkiraan' => $akunPendapatanKoran['nama'],
                    'debet'          => 0,
                    'kredit'         => $dppKoran,
                    'urutan'         => $urutan++,
                ]);
            }

            if ($dppOnline > 0) {
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => $akunPendapatanOnline['kode'],
                    'nama_perkiraan' => $akunPendapatanOnline['nama'],
                    'debet'          => 0,
                    'kredit'         => $dppOnline,
                    'urutan'         => $urutan++,
                ]);
            }

            if ($dppTv > 0) {
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => $akunPendapatanTv['kode'],
                    'nama_perkiraan' => $akunPendapatanTv['nama'],
                    'debet'          => 0,
                    'kredit'         => $dppTv,
                    'urutan'         => $urutan++,
                ]);
            }

            TransaksiKoran::whereDate('tanggal_muatkoran', $tanggal)
                ->where('status_order', '!=', 'Batal')
                ->update(['is_pulled_akuntansi' => true, 'pulled_at' => Carbon::now()]);

            foreach ($daftarKoran as $tk) {
                FakturSyncService::syncFromTransaksi($tk, 'koran');
            }
            foreach ($daftarOnline as $to) {
                FakturSyncService::syncFromTransaksi($to, 'online');
            }
            foreach ($daftarTv as $tt) {
                FakturSyncService::syncFromTransaksi($tt, 'tv');
            }

            TransaksiOnline::where(function ($q) use ($tanggal) {
                    $q->whereDate('tanggal_muatiklanonline', $tanggal)
                      ->orWhereDate('tanggal_transaksionline', $tanggal);
                })
                ->where(fn ($q) => $q->whereNull('status_produksi')->orWhere('status_produksi', '!=', 'batal'))
                ->update(['is_pulled_akuntansi' => true, 'pulled_at' => Carbon::now()]);

            TransaksiPriangan::where(function ($q) use ($tanggal) {
                    $q->whereDate('tanggal_muatiklanpriangan', $tanggal)
                      ->orWhereDate('tanggal_transaksipriangan', $tanggal);
                })
                ->where(fn ($q) => $q->whereNull('status_tayang')->orWhere('status_tayang', '!=', 'batal'))
                ->update(['is_pulled_akuntansi' => true, 'pulled_at' => Carbon::now()]);

            PaketIklan::whereDate('tanggal_transaksi', $tanggal)
                ->update(['is_pulled_akuntansi' => true, 'pulled_at' => Carbon::now()]);

            AuditLogAkuntansi::catat(
                'HITUNG_PENJUALAN',
                'IKLAN',
                "Hitung penjualan iklan tanggal {$tanggal} sebesar Rp " . number_format($totalNominal, 0, ',', '.') . " (Jurnal: {$noJurnal})"
            );
        });

        return redirect()->route('accounting.iklan.hitung-penjualan', ['tanggal' => $tanggal])
            ->with('success', "Penjualan tanggal {$tanggal} berhasil dihitung dan diposting ke GL.");
    }

    public function iklanHitungPembayaran(Request $request)
    {
        $tanggalPilihan = $request->input('tanggal', Carbon::today()->toDateString());
        $sudahDihitung = LogHitungAkuntansi::sudahDihitung('pembayaran_iklan', $tanggalPilihan);
        $logHitung = LogHitungAkuntansi::where('jenis', 'pembayaran_iklan')
            ->whereDate('tanggal_transaksi', $tanggalPilihan)
            ->first();

        $q = trim((string) $request->input('q', ''));
        $metode = $request->input('metode', 'all');

        $query = $this->accountingService->kasMasukAktif()
            ->with('transaksiKoran')
            ->where(function ($queryScope) {
                $queryScope->where('kode_bagian', '01')
                           ->orWhere('tipe_transaksi', 'iklan');
            })
            ->where(function ($queryDate) use ($tanggalPilihan) {
                $queryDate->whereDate('tgl_transaksi', $tanggalPilihan)
                          ->orWhereDate('tanggal_terima', $tanggalPilihan);
            });

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('no_kuitansi', 'like', "%{$q}%")
                    ->orWhere('no_bkm', 'like', "%{$q}%")
                    ->orWhere('no_faktur_ref', 'like', "%{$q}%")
                    ->orWhere('terima_dari', 'like', "%{$q}%")
                    ->orWhere('nama_pembayar', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%");
            });
        }

        if ($metode !== 'all') {
            if ($metode === 'tunai') {
                $query->where(function ($sub) {
                    $sub->where('metode_bayar', 'tunai')
                        ->orWhere('bank_tujuan', 'like', '%kasir%');
                });
            } elseif ($metode === 'bca') {
                $query->where(function ($sub) {
                    $sub->where('metode_bayar', 'transfer')->where('bank_tujuan', 'like', '%bca%');
                });
            } elseif ($metode === 'bjb') {
                $query->where(function ($sub) {
                    $sub->where('metode_bayar', 'transfer')->where('bank_tujuan', 'like', '%bjb%');
                });
            } else {
                $query->where('metode_bayar', $metode);
            }
        }

        $daftarBayar = $query->latest('id')->get();

        $totalCount = $daftarBayar->count();
        $totalBayar = (float) $daftarBayar->sum('jumlah_dibayar');
        $totalPPh = (float) $daftarBayar->sum('potongan_pajak');
        $totalNettoKas = max(0, $totalBayar - $totalPPh);

        return view('pages.accounting.iklan.hitung_pembayaran', compact(
            'tanggalPilihan',
            'sudahDihitung',
            'logHitung',
            'daftarBayar',
            'totalCount',
            'totalBayar',
            'totalPPh',
            'totalNettoKas',
            'q',
            'metode'
        ));
    }

    public function prosesIklanHitungPembayaran(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $periodeKey = Carbon::parse($tanggal)->format('Y-m');

        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Penarikan pelunasan ditolak.");
        }

        if (LogHitungAkuntansi::sudahDihitung('pembayaran_iklan', $tanggal)) {
            return redirect()->back()->with('error', "Pembayaran tanggal {$tanggal} sudah pernah dihitung.");
        }

        $hasOpenShift = ShiftKasir::where(function ($q) use ($tanggal) {
                $q->whereDate('tanggal_shift', $tanggal)
                  ->orWhereDate('tgl_shift', $tanggal);
            })
            ->whereIn('status', ['open', 'aktif'])
            ->exists();

        if ($hasOpenShift) {
            return redirect()->back()->with('error', "Shift Kasir untuk tanggal {$tanggal} masih AKTIF (belum ditutup). Sesuai standar operasional, penarikan pembayaran kas HANYA dapat dilakukan setelah kasir melakukan Tutup Kasir (Closing Shift).");
        }

        $daftarBayar = $this->accountingService->kasMasukAktif()
            ->with('transaksiKoran')
            ->where(function ($q) {
                $q->where('kode_bagian', '01')
                  ->orWhere('tipe_transaksi', 'iklan');
            })
            ->where(function ($q) use ($tanggal) {
                $q->whereDate('tgl_transaksi', $tanggal)
                  ->orWhereDate('tanggal_terima', $tanggal);
            })
            ->get();

        $totalBayar = (float) $daftarBayar->sum('jumlah_dibayar');
        $totalPPh = (float) $daftarBayar->sum('potongan_pajak');
        $totalNettoKas = max(0, $totalBayar - $totalPPh);
        $totalKwitansi = $daftarBayar->count();

        if ($totalBayar <= 0) {
            return redirect()->back()->with('error', "Tidak ada penerimaan kas pelunasan iklan pada tanggal {$tanggal} untuk diproses.");
        }

        DB::transaction(function () use ($tanggal, $totalKwitansi, $totalBayar, $totalPPh, $totalNettoKas, $daftarBayar) {
            $noJurnal = JurnalUmum::generateNomorJurnal($tanggal);
            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $tanggal,
                'tipe'         => 'auto',
                'sumber'       => 'kasir',
                'referensi'    => "KAS-IKL/{$tanggal}",
                'keterangan'   => "Penerimaan Kasir Pelunasan Iklan {$tanggal}",
                'total_debet'  => $totalBayar,
                'total_kredit' => $totalBayar,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            $urutan = 1;
            $debitByAccount = [];
            foreach ($daftarBayar as $km) {
                $netRow = max(0, (float) $km->jumlah_dibayar - (float) ($km->potongan_pajak ?? 0));
                if ($netRow <= 0) {
                    continue;
                }
                $acc = $this->accountingService->resolveCashOrBankAccount($km);
                if (!isset($debitByAccount[$acc['kode']])) {
                    $debitByAccount[$acc['kode']] = ['nama' => $acc['nama'], 'nominal' => 0.0];
                }
                $debitByAccount[$acc['kode']]['nominal'] += $netRow;
            }

            if (empty($debitByAccount) && $totalNettoKas > 0) {
                $accKas = $this->accountingService->getMappedAccount('kas_operasional');
                $debitByAccount[$accKas['kode']] = ['nama' => $accKas['nama'], 'nominal' => $totalNettoKas];
            }

            foreach ($debitByAccount as $kodeDebet => $rowDebet) {
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => $kodeDebet,
                    'nama_perkiraan' => $rowDebet['nama'],
                    'debet'          => $rowDebet['nominal'],
                    'kredit'         => 0,
                    'urutan'         => $urutan++,
                ]);
            }

            if ($totalPPh > 0) {
                $akunPph23 = $this->accountingService->getMappedAccount('piutang_pph23');
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => $akunPph23['kode'],
                    'nama_perkiraan' => $akunPph23['nama'],
                    'debet'          => $totalPPh,
                    'kredit'         => 0,
                    'urutan'         => $urutan++,
                ]);
            }

            $akunPiutang = $this->accountingService->getMappedAccount('piutang_iklan');
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $akunPiutang['kode'],
                'nama_perkiraan' => $akunPiutang['nama'],
                'debet'          => 0,
                'kredit'         => $totalBayar,
                'urutan'         => $urutan++,
            ]);

            // Flagging KasMasuk & Faktur sebagai ditarik akuntansi tanpa double-decrement sisa_piutang
            foreach ($daftarBayar as $km) {
                $km->update([
                    'is_pulled_accounting'  => true,
                    'status_tarik_akunting' => true,
                ]);

                $noFakturRef = $km->no_faktur_ref ?: $km->transaksiKoran?->nofakturkoran;
                if (!empty($noFakturRef)) {
                    Faktur::where('no_faktur', $noFakturRef)->update([
                        'is_pulled_akuntansi' => true,
                        'pulled_at'           => Carbon::now(),
                    ]);
                }
            }

            LogHitungAkuntansi::create([
                'jenis'             => 'pembayaran_iklan',
                'tanggal_transaksi' => $tanggal,
                'total_faktur'      => $totalKwitansi,
                'total_nominal'     => $totalBayar,
                'status'            => 'sukses',
                'user_id'           => auth()->id() ?? 1,
                'catatan'           => "Penarikan kwitansi pelunasan kasir {$totalKwitansi} transaksi. No Jurnal: {$noJurnal}",
            ]);

            AuditLogAkuntansi::catat(
                'HITUNG_PEMBAYARAN',
                'IKLAN',
                "Hitung pembayaran iklan tanggal {$tanggal} sebesar Rp " . number_format($totalBayar, 0, ',', '.') . " (Jurnal: {$noJurnal})"
            );
        });

        return redirect()->route('accounting.iklan.hitung-pembayaran', ['tanggal' => $tanggal])
            ->with('success', "Pembayaran tanggal {$tanggal} berhasil dihitung dan diposting ke GL.");
    }

    public function iklanKoreksi(Request $request)
    {
        $query = KoreksiPiutang::where('jenis', 'iklan');

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('no_bukti', 'like', "%{$keyword}%")
                  ->orWhere('no_faktur', 'like', "%{$keyword}%")
                  ->orWhere('nama_entitas', 'like', "%{$keyword}%")
                  ->orWhere('keterangan', 'like', "%{$keyword}%")
                  ->orWhere('kategori_alasan', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('media') && $request->media !== 'all') {
            $query->where('media_type', $request->media);
        }

        if ($request->filled('kategori_alasan') && $request->kategori_alasan !== 'all') {
            $query->where('kategori_alasan', $request->kategori_alasan);
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal_koreksi', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_koreksi', '<=', $request->sampai);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_koreksi', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_koreksi', $request->tahun);
        }

        if ($request->filled('status_piutang') && $request->status_piutang !== 'all') {
            if ($request->status_piutang === 'lunas') {
                $query->where('saldo_setelah_koreksi', '<=', 0);
            } elseif ($request->status_piutang === 'piutang') {
                $query->where('saldo_setelah_koreksi', '>', 0);
            }
        }

        $daftarCn = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $daftarFaktur = collect();

        $fakturKoran = TransaksiKoran::where('status_order', '!=', 'Batal')
            ->where('piutang_transaksikoran', '>', 0)
            ->latest('id')
            ->take(50)
            ->get()
            ->map(fn ($k) => [
                'no_faktur'     => $k->nofakturkoran,
                'media'         => 'Koran Cetak',
                'media_type'    => 'koran',
                'nama'          => $k->nama_pemasangkoran,
                'total_tagihan' => (float) $k->totaltagihan_transaksikoran,
                'sisa_piutang'  => (float) $k->piutang_transaksikoran,
                'ppn_nominal'   => (float) ($k->ppn_transaksikoran ?? 0),
                'tanggal'       => $k->tanggal_transaksikoran ?? $k->tanggal_muatkoran,
            ]);
        $daftarFaktur = $daftarFaktur->concat($fakturKoran);

        $fakturOnline = TransaksiOnline::where('status_produksi', '!=', 'batal')
            ->where('piutang_transaksionline', '>', 0)
            ->latest('id')
            ->take(30)
            ->get()
            ->map(fn ($o) => [
                'no_faktur'     => $o->nofakturonline,
                'media'         => 'Iklan Online',
                'media_type'    => 'online',
                'nama'          => $o->nama_pemasangonline,
                'total_tagihan' => (float) $o->totaltagihan_transaksionline,
                'sisa_piutang'  => (float) $o->piutang_transaksionline,
                'ppn_nominal'   => (float) ($o->ppn_transaksionline ?? 0),
                'tanggal'       => $o->tanggal_transaksionline ?? $o->tanggal_muatiklanonline,
            ]);
        $daftarFaktur = $daftarFaktur->concat($fakturOnline);

        $fakturTv = TransaksiPriangan::where('status_tayang', '!=', 'batal')
            ->where('piutang_transaksipriangan', '>', 0)
            ->latest('id')
            ->take(30)
            ->get()
            ->map(fn ($t) => [
                'no_faktur'     => $t->nofakturpriangan,
                'media'         => 'Priangan TV',
                'media_type'    => 'tv',
                'nama'          => $t->nama_pemasangpriangan,
                'total_tagihan' => (float) $t->totaltagihan_transaksipriangan,
                'sisa_piutang'  => (float) $t->piutang_transaksipriangan,
                'ppn_nominal'   => (float) ($t->ppn_transaksipriangan ?? 0),
                'tanggal'       => $t->tanggal_transaksipriangan ?? $t->tanggal_muatiklanpriangan,
            ]);
        $daftarFaktur = $daftarFaktur->concat($fakturTv);

        $fakturPaket = PaketIklan::where('status_order', '!=', 'Batal')
            ->where('piutang', '>', 0)
            ->latest('id')
            ->take(30)
            ->get()
            ->map(fn ($p) => [
                'no_faktur'     => $p->nofaktur_paket,
                'media'         => 'Paket Iklan',
                'media_type'    => 'paket',
                'nama'          => $p->nama_pemasang,
                'total_tagihan' => (float) $p->total_tagihan,
                'sisa_piutang'  => (float) $p->piutang,
                'ppn_nominal'   => (float) ($p->nilai_ppn ?? 0),
                'tanggal'       => $p->tanggal_transaksi,
            ]);
        $daftarFaktur = $daftarFaktur->concat($fakturPaket);

        $nextNoBukti = KoreksiPiutang::generateNoBukti('iklan');

        $activeKoranCount = TransaksiKoran::where('status_order', '!=', 'Batal')->where('piutang_transaksikoran', '>', 0)->count();
        $activeOnlineCount = TransaksiOnline::where('status_produksi', '!=', 'batal')->where('piutang_transaksionline', '>', 0)->count();
        $activeTvCount = TransaksiPriangan::where('status_tayang', '!=', 'batal')->where('piutang_transaksipriangan', '>', 0)->count();
        $activePaketCount = PaketIklan::where('status_order', '!=', 'Batal')->where('piutang', '>', 0)->count();

        $stats = [
            'total_count'   => KoreksiPiutang::where('jenis', 'iklan')->count(),
            'total_nominal' => (float) KoreksiPiutang::where('jenis', 'iklan')->sum('jumlah_koreksi'),
            'total_faktur'  => KoreksiPiutang::where('jenis', 'iklan')->distinct('no_faktur')->count('no_faktur'),
            'faktur_aktif'  => $activeKoranCount + $activeOnlineCount + $activeTvCount + $activePaketCount,
        ];

        return view('pages.accounting.iklan.koreksi', compact('daftarCn', 'daftarFaktur', 'nextNoBukti', 'stats'));
    }

    public function apiCariFakturKoreksi(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = collect();

        $fakturs = Faktur::with('customer')
            ->where('sisa_piutang', '>', 0)
            ->where('status', '!=', 'batal')
            ->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%");
                    });
            })
            ->take(20)
            ->get();

        foreach ($fakturs as $f) {
            $mediaLabel = match (strtolower((string) $f->kategori)) {
                'koran', 'cetak' => 'Koran Cetak',
                'online'         => 'Iklan Online',
                'tv'             => 'Priangan TV',
                'paket'          => 'Paket Lintas Media',
                default          => 'Faktur Umum',
            };
            $results->push([
                'no_faktur'     => $f->no_faktur,
                'media'         => $mediaLabel,
                'media_type'    => strtolower((string) $f->kategori),
                'nama'          => $f->customer->nama_customer ?? 'Klien Faktur',
                'total_tagihan' => (float) $f->total_tagihan,
                'sisa_piutang'  => (float) $f->sisa_piutang,
                'ppn_nominal'   => (float) ($f->ppn_nominal ?? 0),
                'tanggal'       => $f->tanggal_faktur ? $f->tanggal_faktur->format('Y-m-d') : null,
            ]);
        }

        if ($results->isEmpty()) {
            $koranTrx = TransaksiKoran::where('piutang_transaksikoran', '>', 0)
                ->where('status_order', '!=', 'Batal')
                ->where(function ($sub) use ($q) {
                    $sub->where('nofakturkoran', 'like', "%{$q}%")
                        ->orWhere('nama_pemasangkoran', 'like', "%{$q}%");
                })->take(10)->get();

            foreach ($koranTrx as $k) {
                $results->push([
                    'no_faktur'     => $k->nofakturkoran,
                    'media'         => 'Koran Cetak',
                    'media_type'    => 'koran',
                    'nama'          => $k->nama_pemasangkoran,
                    'total_tagihan' => (float) $k->totaltagihan_transaksikoran,
                    'sisa_piutang'  => (float) $k->piutang_transaksikoran,
                    'ppn_nominal'   => (float) ($k->ppn_transaksikoran ?? 0),
                    'tanggal'       => $k->tanggal_transaksikoran ?? $k->tanggal_muatkoran,
                ]);
            }
        }

        return response()->json($results->values());
    }

    public function storeIklanKoreksi(Request $request)
    {
        if ($request->has('nominal_koreksi') && !$request->filled('jumlah_koreksi')) {
            $request->merge(['jumlah_koreksi' => $request->nominal_koreksi]);
        }
        if ($request->has('alasan') && !$request->filled('keterangan')) {
            $request->merge(['keterangan' => $request->alasan]);
        }

        if ($request->filled('jumlah_koreksi')) {
            $cleanNominal = str_replace(['.', ','], '', (string) $request->jumlah_koreksi);
            $request->merge(['jumlah_koreksi' => $cleanNominal]);
        }

        $request->validate([
            'no_faktur'        => 'required|string',
            'tanggal'          => 'required|date',
            'jumlah_koreksi'   => 'required|numeric|min:1',
            'kategori_alasan'  => 'nullable|string',
            'keterangan'       => 'required|string|max:500',
        ]);

        $periodeKey = Carbon::parse($request->tanggal)->format('Y-m');
        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Penerbitan nota kredit ditolak.");
        }

        $noFaktur = trim((string) $request->no_faktur);
        $mediaType = 'koran';
        $namaPemasang = '-';
        $alamatPemasang = '-';
        $totalPenjualan = 0.0;
        $sisaSebelum = 0.0;
        $ppnNominal = 0.0;
        $targetFakturId = null;
        $noOrder = null;

        $trxKoran = TransaksiKoran::where('nofakturkoran', $noFaktur)->first();
        $trxOnline = TransaksiOnline::where('nofakturonline', $noFaktur)->first();
        $trxPriangan = TransaksiPriangan::where('nofakturpriangan', $noFaktur)->first();
        $trxPaket = PaketIklan::where('nofaktur_paket', $noFaktur)->first();
        $fakturKeuangan = Faktur::where('no_faktur', $noFaktur)->first();

        if ($trxKoran) {
            $mediaType = 'koran';
            $targetFakturId = $trxKoran->id;
            $namaPemasang = $trxKoran->nama_pemasangkoran;
            $alamatPemasang = $trxKoran->alamat_pemasangkoran;
            $totalPenjualan = (float) $trxKoran->totaltagihan_transaksikoran;
            $sisaSebelum = (float) $trxKoran->piutang_transaksikoran;
            $ppnNominal = (float) ($trxKoran->ppn_transaksikoran ?? 0);
            $noOrder = $trxKoran->no_order ?? $trxKoran->nofakturkoran;
        } elseif ($trxOnline) {
            $mediaType = 'online';
            $targetFakturId = $trxOnline->id;
            $namaPemasang = $trxOnline->nama_pemasangonline;
            $alamatPemasang = $trxOnline->alamat_pemasangonline;
            $totalPenjualan = (float) $trxOnline->totaltagihan_transaksionline;
            $sisaSebelum = (float) $trxOnline->piutang_transaksionline;
            $ppnNominal = (float) ($trxOnline->ppn_transaksionline ?? 0);
            $noOrder = $trxOnline->no_order ?? $trxOnline->nofakturonline;
        } elseif ($trxPriangan) {
            $mediaType = 'tv';
            $targetFakturId = $trxPriangan->id;
            $namaPemasang = $trxPriangan->nama_pemasangpriangan;
            $alamatPemasang = $trxPriangan->alamat_pemasangpriangan;
            $totalPenjualan = (float) $trxPriangan->totaltagihan_transaksipriangan;
            $sisaSebelum = (float) $trxPriangan->piutang_transaksipriangan;
            $ppnNominal = (float) ($trxPriangan->ppn_transaksipriangan ?? 0);
            $noOrder = $trxPriangan->no_order ?? $trxPriangan->nofakturpriangan;
        } elseif ($trxPaket) {
            $mediaType = 'paket';
            $targetFakturId = $trxPaket->id;
            $namaPemasang = $trxPaket->nama_pemasang;
            $alamatPemasang = $trxPaket->alamat_pemasang ?? '-';
            $totalPenjualan = (float) $trxPaket->total_tagihan;
            $sisaSebelum = (float) $trxPaket->piutang;
            $ppnNominal = (float) ($trxPaket->nilai_ppn ?? 0);
            $noOrder = $trxPaket->nofaktur_paket;
        } elseif ($fakturKeuangan) {
            $mediaType = strtolower((string) ($fakturKeuangan->kategori ?: 'faktur'));
            $targetFakturId = $fakturKeuangan->id;
            $namaPemasang = $fakturKeuangan->customer->nama_customer ?? 'Klien Faktur';
            $alamatPemasang = $fakturKeuangan->customer->alamat ?? '-';
            $totalPenjualan = (float) $fakturKeuangan->total_tagihan;
            $sisaSebelum = (float) $fakturKeuangan->sisa_piutang;
            $ppnNominal = (float) ($fakturKeuangan->ppn_nominal ?? 0);
            $noOrder = $fakturKeuangan->no_order ?? $fakturKeuangan->no_faktur;
        } else {
            return redirect()->back()->with('error', "Faktur '{$noFaktur}' tidak ditemukan di seluruh data transaksi iklan.");
        }

        if ($sisaSebelum <= 0) {
            return redirect()->back()->with('error', "Faktur '{$noFaktur}' sudah berstatus LUNAS (sisa piutang Rp 0), tidak dapat diterbitkan Nota Kredit.");
        }

        $kodePerkiraan = '1.00.04.01.01.01.00001';
        $jumlahKoreksi = (float) $request->jumlah_koreksi;

        if ($jumlahKoreksi > $sisaSebelum) {
            return redirect()->back()->withInput()->with('error', "Nilai koreksi (Rp " . number_format($jumlahKoreksi, 0, ',', '.') . ") tidak boleh melebihi sisa piutang faktur saat ini (Rp " . number_format($sisaSebelum, 0, ',', '.') . ").");
        }

        $saldoSetelah = max(0, $sisaSebelum - $jumlahKoreksi);
        $kategoriAlasan = $request->input('kategori_alasan', 'KOMPLAIN_CETAK');

        $koreksiPpn = 0.0;
        if ($ppnNominal > 0 && $totalPenjualan > 0) {
            $ppnRatio = min(0.20, max(0.01, $ppnNominal / $totalPenjualan));
            $koreksiPpn = round($jumlahKoreksi * $ppnRatio, 2);
        }
        $koreksiDpp = round($jumlahKoreksi - $koreksiPpn, 2);

        DB::transaction(function () use (
            $noFaktur, $request, $mediaType, $namaPemasang, $alamatPemasang,
            $totalPenjualan, $jumlahKoreksi, $saldoSetelah, $kodePerkiraan,
            $koreksiPpn, $koreksiDpp, $kategoriAlasan, $targetFakturId, $noOrder,
            $trxKoran, $trxOnline, $trxPriangan, $trxPaket
        ) {
            $noBukti = KoreksiPiutang::generateNoBukti('iklan', $request->tanggal);

            $noJurnal = JurnalUmum::generateNomorJurnal($request->tanggal);
            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $request->tanggal,
                'tipe'         => 'auto',
                'sumber'       => 'iklan',
                'referensi'    => $noBukti,
                'keterangan'   => "Nota Kredit CN ({$kategoriAlasan}) {$noFaktur} - {$namaPemasang}",
                'total_debet'  => $jumlahKoreksi,
                'total_kredit' => $jumlahKoreksi,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            $urutan = 1;

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '4.00.05.01.01.00.00001',
                'nama_perkiraan' => 'Koreksi & Diskon Penjualan Iklan (CN-IKL)',
                'debet'          => $koreksiDpp,
                'kredit'         => 0,
                'urutan'         => $urutan++,
            ]);

            if ($koreksiPpn > 0) {
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => '2.00.03.01.01.00.00001',
                    'nama_perkiraan' => 'Utang Pajak PPN Keluaran 12%',
                    'debet'          => $koreksiPpn,
                    'kredit'         => 0,
                    'urutan'         => $urutan++,
                ]);
            }

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $kodePerkiraan,
                'nama_perkiraan' => 'Piutang Usaha Iklan Umum',
                'debet'          => 0,
                'kredit'         => $jumlahKoreksi,
                'urutan'         => $urutan++,
            ]);

            KoreksiPiutang::create([
                'no_bukti'              => $noBukti,
                'jenis'                 => 'iklan',
                'media_type'            => $mediaType,
                'kategori_alasan'       => $kategoriAlasan,
                'tanggal_koreksi'       => $request->tanggal,
                'faktur_id'             => $targetFakturId,
                'no_faktur'             => $noFaktur,
                'no_order'              => $noOrder,
                'kode_perkiraan'        => $kodePerkiraan,
                'nama_entitas'          => $namaPemasang,
                'alamat'                => $alamatPemasang,
                'nilai_penjualan'       => $totalPenjualan,
                'jumlah_koreksi'        => $jumlahKoreksi,
                'dpp_koreksi'           => $koreksiDpp,
                'ppn_koreksi'           => $koreksiPpn,
                'saldo_setelah_koreksi' => $saldoSetelah,
                'keterangan'            => $request->keterangan,
                'jurnal_umum_id'        => $jurnal->id,
                'user_id'               => auth()->id() ?? 1,
            ]);

            if ($trxKoran) {
                $trxKoran->piutang_transaksikoran = $saldoSetelah;
                if ($saldoSetelah <= 0) {
                    $trxKoran->status_order = 'Lunas';
                }
                $trxKoran->save();
            } elseif ($trxOnline) {
                $trxOnline->piutang_transaksionline = $saldoSetelah;
                $trxOnline->save();
            } elseif ($trxPriangan) {
                $trxPriangan->piutang_transaksipriangan = $saldoSetelah;
                $trxPriangan->save();
            } elseif ($trxPaket) {
                $trxPaket->piutang = $saldoSetelah;
                if ($saldoSetelah <= 0) {
                    $trxPaket->status_order = 'Lunas';
                }
                $trxPaket->save();

                if ($trxPaket->relationLoaded('transaksiKoran') || method_exists($trxPaket, 'transaksiKoran')) {
                    $trxPaket->transaksiKoran()->update(['piutang_transaksikoran' => 0]);
                    $trxPaket->transaksiOnline()->update(['piutang_transaksionline' => 0]);
                    $trxPaket->transaksiPriangan()->update(['piutang_transaksipriangan' => 0]);
                }
            }

            $fakturSync = Faktur::where('no_faktur', $noFaktur)->first();
            if ($fakturSync) {
                $fakturSync->sisa_piutang = max(0, (float) $fakturSync->sisa_piutang - $jumlahKoreksi);
                if ($fakturSync->sisa_piutang <= 0) {
                    $fakturSync->status = 'lunas';
                    $fakturSync->status_bayar = 'lunas';

                    PenugasanInkaso::where('faktur_id', $fakturSync->id)
                        ->where('status_tugas', 'berjalan')
                        ->update(['status_tugas' => 'selesai']);
                }
                $fakturSync->save();
            }

            AuditLogAkuntansi::catat(
                'KOREKSI_CN',
                'IKLAN',
                "Koreksi CN {$noBukti} ({$kategoriAlasan}) untuk faktur {$noFaktur} [{$mediaType}] sebesar Rp " . number_format($jumlahKoreksi, 0, ',', '.') . " (Jurnal: {$noJurnal})"
            );
        });

        return redirect()->route('accounting.iklan.koreksi')
            ->with('success', "Bukti koreksi CN-IKL berhasil disimpan dan otomatis disinkronkan ke Buku Faktur Keuangan & Inkaso.");
    }

    public function cetakIklanKoreksi($id)
    {
        $koreksi = KoreksiPiutang::findOrFail($id);
        return view('pages.accounting.iklan.cetak_koreksi', compact('koreksi'));
    }

    public function iklanBukaData(Request $request)
    {
        $query = MemoUnlock::query();

        $query->where(function ($q) {
            $q->where('tujuan_divisi', 'Divisi Akuntansi')
              ->orWhere('tujuan_divisi', 'iklan')
              ->orWhereNull('tujuan_divisi')
              ->orWhere('status_akuntansi', 'PENDING');
        });

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_memo', 'like', "%{$keyword}%")
                  ->orWhere('nama_pemasang', 'like', "%{$keyword}%")
                  ->orWhere('spesifikasi_iklan', 'like', "%{$keyword}%")
                  ->orWhere('alasan', 'like', "%{$keyword}%")
                  ->orWhere('diketahui_oleh', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where(function ($q) {
                    $q->where('status_akuntansi', 'PENDING')
                      ->orWhere('status', 'PENDING')
                      ->orWhereNull('status_akuntansi');
                });
            } elseif ($request->status === 'approved') {
                $query->where(function ($q) {
                    $q->whereIn('status_akuntansi', ['APPROVED', 'OPEN'])
                      ->orWhere('status', 'OPEN');
                });
            }
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal_target', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_target', '<=', $request->sampai);
        }

        $daftarMemo = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total'    => MemoUnlock::count(),
            'pending'  => MemoUnlock::where(function ($q) {
                $q->where('status_akuntansi', 'PENDING')
                  ->orWhere('status', 'PENDING')
                  ->orWhereNull('status_akuntansi');
            })->count(),
            'approved' => MemoUnlock::where(function ($q) {
                $q->whereIn('status_akuntansi', ['APPROVED', 'OPEN'])
                  ->orWhere('status', 'OPEN');
            })->count(),
        ];

        $nextNoMemo = MemoUnlock::generateNomorMemo();

        $daftarFaktur = TransaksiKoran::where('status_order', '!=', 'Batal')
            ->latest('id')
            ->limit(50)
            ->get(['id', 'nofakturkoran', 'nama_pemasangkoran', 'totaltagihan_transaksikoran', 'tanggal_transaksikoran']);

        return view('pages.accounting.iklan.buka_data', compact('daftarMemo', 'stats', 'nextNoMemo', 'daftarFaktur'));
    }

    public function prosesIklanBukaData(Request $request)
    {
        if ($request->filled('memo_id')) {
            $memo = MemoUnlock::findOrFail($request->memo_id);

            if ($request->input('action') === 'reject' || $request->filled('alasan_penolakan')) {
                $request->validate([
                    'alasan_penolakan' => 'required|string|min:3|max:500',
                ], [
                    'alasan_penolakan.required' => 'Alasan penolakan wajib diisi agar pemohon mengetahui penyebabnya.',
                ]);

                $memo->update([
                    'status_akuntansi'      => 'REJECTED',
                    'status'                => 'CLOSED',
                    'direspon_oleh_user_id' => Auth::id(),
                    'ditolak_pada'          => now(),
                    'catatan_akuntansi'     => $request->alasan_penolakan,
                ]);

                AuditLogAkuntansi::catat(
                    'TOLAK_CUTOFF',
                    'IKLAN',
                    "Penolakan buka data cut-off memo {$memo->nomor_memo} target {$memo->tanggal_target}. Alasan: {$request->alasan_penolakan}"
                );

                return redirect()->back()->with('success', "Memo {$memo->nomor_memo} resmi DITOLAK dengan catatan.");
            }

            $memo->update([
                'status_akuntansi'        => 'APPROVED',
                'status'                  => 'OPEN',
                'direspon_oleh_user_id'   => Auth::id(),
                'diterima_akuntansi_pada' => now(),
                'catatan_akuntansi'       => $request->alasan ?: 'Disetujui oleh Akuntansi',
            ]);

            AuditLogAkuntansi::catat(
                'BUKA_CUTOFF',
                'IKLAN',
                "Persetujuan buka data cut-off memo {$memo->nomor_memo} target {$memo->tanggal_target}"
            );

            return redirect()->back()->with('success', "Memo {$memo->nomor_memo} berhasil disetujui. Transaksi dibuka.");
        }

        $request->validate([
            'no_faktur'        => 'required|string',
            'alasan'           => 'required|string',
            'tanggal_target'   => 'nullable|date',
            'kategori_urgensi' => 'nullable|string',
        ]);

        $faktur = TransaksiKoran::where('nofakturkoran', $request->no_faktur)->first();
        $nomorMemo = $request->no_memo ?: MemoUnlock::generateNomorMemo();

        $memo = MemoUnlock::create([
            'tanggal_target'          => $request->tanggal_target ?: ($faktur?->tanggal_muatkoran ?: now()->toDateString()),
            'nomor_memo'              => $nomorMemo,
            'kategori_urgensi'        => $request->kategori_urgensi ?: 'SEDANG',
            'nama_pemasang'           => $faktur?->nama_pemasangkoran ?: ($request->nama_pemasang ?: "Faktur: {$request->no_faktur}"),
            'spesifikasi_iklan'       => "Otorisasi Faktur {$request->no_faktur}",
            'estimasi_nominal'        => $faktur ? (int) $faktur->totaltagihan_transaksikoran : 0,
            'alasan'                  => $request->alasan,
            'tujuan_divisi'           => 'Divisi Akuntansi',
            'status_akuntansi'        => 'APPROVED',
            'status'                  => 'OPEN',
            'diketahui_oleh'          => auth()->user()?->name ?? 'Staf Akuntansi',
            'dibuka_oleh_user_id'     => auth()->id() ?: (User::first()?->id ?? 1),
            'dibuka_pada'             => now(),
            'diterima_akuntansi_pada' => now(),
        ]);

        AuditLogAkuntansi::catat(
            'BUKA_CUTOFF',
            'IKLAN',
            "Otorisasi buka kunci data mandiri memo {$memo->nomor_memo} untuk faktur {$request->no_faktur}"
        );

        return redirect()->back()->with('success', "Otorisasi buka kunci faktur {$request->no_faktur} berhasil diterbitkan (Memo: {$memo->nomor_memo}).");
    }

    public function prosesBatalDpInsentif(Request $request)
    {
        $request->validate([
            'no_faktur' => 'nullable|string|max:100',
            'alasan'    => 'nullable|string|max:255',
        ]);

        $noFaktur = trim((string) $request->input('no_faktur', ''));
        $alasan = trim((string) $request->input('alasan', 'Pembatalan pengajuan DP Insentif oleh Divisi Accounting'));

        if ($noFaktur !== '') {
            $jurnal = JurnalUmum::where('referensi', 'like', "%{$noFaktur}%")
                ->where('keterangan', 'like', '%Insentif%')
                ->whereNull('reversed_by_jurnal_id')
                ->latest('id')
                ->first();

            if ($jurnal) {
                $periodeKey = Carbon::parse($jurnal->tanggal)->format('Y-m');
                if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
                    return redirect()->back()->with('error', "Periode {$periodeKey} sudah terkunci.");
                }
                $this->accountingService->reverseJurnal($jurnal, $alasan);
            }
        }

        AuditLogAkuntansi::catat(
            'BATAL_DP_INSENTIF',
            'IKLAN',
            'Pembatalan pengajuan DP Insentif' . ($noFaktur ? " ({$noFaktur})" : '') . ": {$alasan}"
        );

        return redirect()->back()->with('success', 'Pengajuan DP Insentif berhasil dibatalkan dan dicatat ke Audit Log.');
    }

    public function prosesUnpullPembayaranIklan(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $periodeKey = Carbon::parse($tanggal)->format('Y-m');

        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Pembatalan penarikan ditolak.");
        }

        DB::transaction(function () use ($tanggal) {
            $jurnals = JurnalUmum::where('sumber', 'kasir')
                ->where('referensi', "KAS-IKL/{$tanggal}")
                ->whereNull('reversed_by_jurnal_id')
                ->get();

            foreach ($jurnals as $j) {
                $this->accountingService->reverseJurnal($j, "Unpull pembayaran kasir tanggal {$tanggal}", $tanggal);
            }

            $daftarKasMasuk = $this->accountingService->kasMasukAktif()
                ->with('transaksiKoran')
                ->where(function ($q) {
                    $q->where('kode_bagian', '01')
                      ->orWhere('tipe_transaksi', 'iklan');
                })
                ->where(function ($q) use ($tanggal) {
                    $q->whereDate('tgl_transaksi', $tanggal)
                      ->orWhereDate('tanggal_terima', $tanggal);
                })
                ->get();

            $fakturNos = [];
            foreach ($daftarKasMasuk as $km) {
                $km->update([
                    'is_pulled_accounting'  => false,
                    'status_tarik_akunting' => false,
                ]);
                $ref = $km->no_faktur_ref ?: $km->transaksiKoran?->nofakturkoran;
                if (!empty($ref)) {
                    $fakturNos[] = $ref;
                }
            }

            if (!empty($fakturNos)) {
                Faktur::whereIn('no_faktur', array_unique($fakturNos))->update([
                    'is_pulled_akuntansi' => false,
                    'pulled_at'           => null,
                ]);
            }

            LogHitungAkuntansi::where('jenis', 'pembayaran_iklan')
                ->whereDate('tanggal_transaksi', $tanggal)
                ->update(['status' => 'batal_dibuka']);

            AuditLogAkuntansi::catat('UNPULL_BAYAR', 'IKLAN', "Buka kunci pembayaran kasir tanggal {$tanggal} (Jurnal terkait di-revert)");
        });

        return redirect()->back()->with('success', "Penarikan pembayaran tanggal {$tanggal} berhasil dibuka dan jurnal GL di-revert.");
    }

    public function iklanBelanja(Request $request)
    {
        $query = $request->input('q');
        $kategori = $request->input('kategori', 'all');
        $statusBayar = $request->input('status_bayar', 'all');
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        $builder = TransaksiKoran::where('status_order', '!=', 'Batal');

        if ($query) {
            $builder->where(function ($q) use ($query) {
                $q->where('nama_pemasangkoran', 'like', "%{$query}%")
                  ->orWhere('nofakturkoran', 'like', "%{$query}%");
            });
        }

        if ($kategori !== 'all') {
            $builder->where('kategori_form', $kategori);
        }

        if ($statusBayar !== 'all') {
            if ($statusBayar === 'lunas') {
                $builder->where('piutang_transaksikoran', '<=', 0);
            } elseif ($statusBayar === 'piutang') {
                $builder->where('piutang_transaksikoran', '>', 0);
            }
        }

        if ($dari) {
            $builder->whereDate('tanggal_muatkoran', '>=', $dari);
        }
        if ($sampai) {
            $builder->whereDate('tanggal_muatkoran', '<=', $sampai);
        }

        $riwayat = $builder->latest('tanggal_muatkoran')->paginate(20)->withQueryString();

        $stats = [
            'total_transaksi' => TransaksiKoran::where('status_order', '!=', 'Batal')->count(),
            'total_omset'     => (float) TransaksiKoran::where('status_order', '!=', 'Batal')->sum('totaltagihan_transaksikoran'),
            'total_piutang'   => (float) TransaksiKoran::where('status_order', '!=', 'Batal')->sum('piutang_transaksikoran'),
            'total_lunas'     => TransaksiKoran::where('status_order', '!=', 'Batal')->where('piutang_transaksikoran', '<=', 0)->count(),
        ];

        return view('pages.accounting.iklan.belanja', compact('riwayat', 'query', 'kategori', 'statusBayar', 'dari', 'sampai', 'stats'));
    }
}
