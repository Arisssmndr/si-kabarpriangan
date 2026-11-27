<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Agen;
use App\Models\AuditLogAkuntansi;
use App\Models\Faktur;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumDetail;
use App\Models\KoreksiPiutang;
use App\Models\LogHitungAkuntansi;
use App\Models\MemoUnlock;
use App\Models\PeriodeAkuntansi;
use App\Models\ShiftKasir;
use App\Models\SirkulasiFakturBulanan;
use App\Models\SirkulasiRetur;
use App\Services\AccountingService;
use App\Services\SirkulasiDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KartuSirkulasiController extends Controller
{
    public function __construct(protected AccountingService $accountingService)
    {
    }

    public function sirkulasiSaldoAwal(Request $request)
    {
        $query = Agen::query();

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('kode_agen', 'like', "%{$keyword}%")
                  ->orWhere('nama_agen', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('wilayah') && $request->wilayah !== 'all') {
            $query->where('wilayah', $request->wilayah);
        }

        if ($request->filled('status_saldo') && $request->status_saldo !== 'all') {
            if ($request->status_saldo === 'ada_saldo') {
                $query->where('saldo_piutang', '>', 0);
            } elseif ($request->status_saldo === 'nol') {
                $query->where('saldo_piutang', '<=', 0);
            }
        }

        $daftarAgen = $query->orderBy('nama_agen')->get();
        $totalPiutangAwal = Agen::sum('saldo_piutang');
        $daftarWilayah = Agen::whereNotNull('wilayah')->where('wilayah', '!=', '')->distinct()->pluck('wilayah');

        $stats = [
            'total_agen'         => Agen::count(),
            'agen_bersaldo'      => Agen::where('saldo_piutang', '>', 0)->count(),
            'total_piutang_awal' => $totalPiutangAwal,
            'rata_piutang'       => (float) (Agen::where('saldo_piutang', '>', 0)->avg('saldo_piutang') ?? 0),
        ];

        return view('pages.accounting.sirkulasi.saldo_awal', compact('daftarAgen', 'totalPiutangAwal', 'daftarWilayah', 'stats'));
    }

    public function prosesSirkulasiSaldoAwal(Request $request)
    {
        $request->validate([
            'agen_id'       => 'required|exists:agens,id',
            'saldo_piutang' => 'required|numeric|min:0',
        ]);

        $agen = Agen::findOrFail($request->agen_id);
        $saldoLama = $agen->saldo_piutang;
        $agen->update(['saldo_piutang' => $request->saldo_piutang]);

        AuditLogAkuntansi::catat(
            'UPDATE_SALDO_AWAL',
            'SIRKULASI',
            "Update saldo piutang agen {$agen->nama_agen} ({$agen->kode_agen}) dari Rp " . number_format($saldoLama, 0, ',', '.') . " menjadi Rp " . number_format($request->saldo_piutang, 0, ',', '.')
        );

        return redirect()->back()->with('success', "Saldo awal agen {$agen->nama_agen} berhasil diperbarui.");
    }

    public function sirkulasiHitungPenjualan(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $bulanInt = (int) substr($bulanPilihan, 5, 2);
        $tahunInt = (int) substr($bulanPilihan, 0, 4);
        $startMonth = Carbon::parse($bulanPilihan . '-01')->startOfMonth()->toDateString();
        $endMonth = Carbon::parse($bulanPilihan . '-01')->endOfMonth()->toDateString();

        $sudahDihitung = LogHitungAkuntansi::where('jenis', 'penjualan_sirkulasi')
            ->where('bulan_periode', $bulanInt)
            ->where('tahun_periode', $tahunInt)
            ->where('status', 'sukses')
            ->exists();

        $logHitung = LogHitungAkuntansi::where('jenis', 'penjualan_sirkulasi')
            ->where('bulan_periode', $bulanInt)
            ->where('tahun_periode', $tahunInt)
            ->first();

        $calc = $this->accountingService->hitungOmsetSirkulasiRange($startMonth, $endMonth);
        $daftarAgenAll = $calc['daftar_agen'];
        $perAgenCalc = $calc['per_agen'];
        $totalJatah = $daftarAgenAll->sum('jatah_default');
        $hargaKoran = $calc['harga_rata'];
        $estimasiHari = $calc['hari'];
        $totalOmset = (float) $calc['total_nominal'];
        $pricing = $calc['pricing'];

        $q = trim((string) $request->input('q', ''));
        $wilayah = $request->input('wilayah', 'all');

        $filteredAgen = $daftarAgenAll;
        if ($q !== '') {
            $filteredAgen = $filteredAgen->filter(function ($a) use ($q) {
                return str_contains(strtolower($a->nama_agen), strtolower($q))
                    || str_contains(strtolower($a->kode_agen), strtolower($q));
            });
        }
        if ($wilayah !== 'all') {
            $filteredAgen = $filteredAgen->filter(fn ($a) => $a->wilayah === $wilayah);
        }

        $daftarAgen = $filteredAgen;
        $daftarWilayah = Agen::whereNotNull('wilayah')->where('wilayah', '!=', '')->distinct()->pluck('wilayah');

        return view('pages.accounting.sirkulasi.hitung_penjualan', compact(
            'bulanPilihan',
            'sudahDihitung',
            'logHitung',
            'daftarAgen',
            'perAgenCalc',
            'totalJatah',
            'hargaKoran',
            'totalOmset',
            'estimasiHari',
            'pricing',
            'daftarWilayah',
            'q',
            'wilayah'
        ));
    }

    public function prosesSirkulasiHitungPenjualan(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $bulanInt = (int) substr($bulan, 5, 2);
        $tahunInt = (int) substr($bulan, 0, 4);
        $startMonth = Carbon::parse($bulan . '-01')->startOfMonth()->toDateString();
        $endMonth = Carbon::parse($bulan . '-01')->endOfMonth()->toDateString();

        if (PeriodeAkuntansi::isPeriodeLocked($bulan)) {
            return redirect()->back()->with('error', "Periode {$bulan} sudah ditutup (terkunci). Pembentukan omset sirkulasi ditolak.");
        }

        $exists = LogHitungAkuntansi::where('jenis', 'penjualan_sirkulasi')
            ->where('bulan_periode', $bulanInt)
            ->where('tahun_periode', $tahunInt)
            ->where('status', 'sukses')
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', "Penjualan sirkulasi bulan {$bulan} sudah pernah dihitung.");
        }

        $calc = $this->accountingService->hitungOmsetSirkulasiRange($startMonth, $endMonth);
        $daftarAgen = $calc['daftar_agen'];
        $perAgenCalc = $calc['per_agen'];
        $daysInMonth = $calc['hari'];
        $totalOmset = (float) $calc['total_nominal'];

        $tglPosting = Carbon::parse($bulan . '-01')->endOfMonth()->toDateString();

        DB::transaction(function () use ($bulan, $bulanInt, $tahunInt, $totalOmset, $tglPosting, $daftarAgen, $perAgenCalc, $daysInMonth) {
            $noJurnal = JurnalUmum::generateNomorJurnal($tglPosting);

            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $tglPosting,
                'tipe'         => 'auto',
                'sumber'       => 'sirkulasi',
                'referensi'    => "SIRK-{$bulan}",
                'keterangan'   => "Hitung Penjualan Koran Sirkulasi Bulan {$bulan} ({$daysInMonth} Hari)",
                'total_debet'  => $totalOmset,
                'total_kredit' => $totalOmset,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            $akunPiutangSirk = $this->accountingService->getMappedAccount('piutang_sirkulasi');
            $akunPendapatanSirk = $this->accountingService->getMappedAccount('pendapatan_sirkulasi');

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $akunPiutangSirk['kode'],
                'nama_perkiraan' => $akunPiutangSirk['nama'],
                'debet'          => $totalOmset,
                'kredit'         => 0,
                'urutan'         => 1,
            ]);

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $akunPendapatanSirk['kode'],
                'nama_perkiraan' => $akunPendapatanSirk['nama'],
                'debet'          => 0,
                'kredit'         => $totalOmset,
                'urutan'         => 2,
            ]);

            foreach ($daftarAgen as $agen) {
                $omsetAgen = (float) ($perAgenCalc[$agen->id]['nominal'] ?? 0);
                if ($omsetAgen > 0) {
                    $agen->increment('saldo_piutang', $omsetAgen);
                }
            }

            LogHitungAkuntansi::create([
                'jenis'             => 'penjualan_sirkulasi',
                'tanggal_transaksi' => $tglPosting,
                'bulan_periode'     => $bulanInt,
                'tahun_periode'     => $tahunInt,
                'total_faktur'      => $daftarAgen->count(),
                'total_nominal'     => $totalOmset,
                'status'            => 'sukses',
                'user_id'           => auth()->id() ?? 1,
                'catatan'           => "Hitung omset sirkulasi bulan {$bulan} ({$daysInMonth} hari, tarif SistemConfig). No Jurnal: {$noJurnal}",
            ]);

            AuditLogAkuntansi::catat(
                'HITUNG_PENJUALAN',
                'SIRKULASI',
                "Hitung omset sirkulasi bulan {$bulan} sebesar Rp " . number_format($totalOmset, 0, ',', '.') . " (Jurnal: {$noJurnal})"
            );
        });

        return redirect()->back()->with('success', "Penjualan sirkulasi bulan {$bulan} ({$daysInMonth} hari) berhasil dihitung sesuai tarif SistemConfig dan diposting ke GL.");
    }

    public function sirkulasiHitungPembayaran(Request $request)
    {
        $tanggalPilihan = $request->input('tanggal', Carbon::today()->toDateString());
        $sudahDihitung = LogHitungAkuntansi::where('jenis', 'pembayaran_sirkulasi')
            ->whereDate('tanggal_transaksi', $tanggalPilihan)
            ->where('status', 'sukses')
            ->exists();

        $logHitung = LogHitungAkuntansi::where('jenis', 'pembayaran_sirkulasi')
            ->whereDate('tanggal_transaksi', $tanggalPilihan)
            ->first();

        $q = trim((string) $request->input('q', ''));
        $metode = $request->input('metode', 'all');

        $query = $this->accountingService->kasMasukAktif()
            ->with('agen')
            ->where(function ($sub) {
                $sub->where('kode_bagian', '02')
                    ->orWhere('tipe_transaksi', 'sirkulasi');
            })
            ->where(function ($sub) use ($tanggalPilihan) {
                $sub->whereDate('tgl_transaksi', $tanggalPilihan)
                    ->orWhereDate('tanggal_terima', $tanggalPilihan);
            });

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('no_kuitansi', 'like', "%{$q}%")
                    ->orWhere('no_bkm', 'like', "%{$q}%")
                    ->orWhere('no_faktur_ref', 'like', "%{$q}%")
                    ->orWhere('terima_dari', 'like', "%{$q}%")
                    ->orWhere('nama_pembayar', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%")
                    ->orWhereHas('agen', function ($agenQuery) use ($q) {
                        $agenQuery->where('nama_agen', 'like', "%{$q}%")
                                  ->orWhere('kode_agen', 'like', "%{$q}%");
                    });
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

        $daftarSetoran = $query->latest('id')->get();

        $totalCount = $daftarSetoran->count();
        $totalSetoran = (float) $daftarSetoran->sum('jumlah_dibayar');

        return view('pages.accounting.sirkulasi.hitung_pembayaran', compact(
            'tanggalPilihan',
            'sudahDihitung',
            'logHitung',
            'daftarSetoran',
            'totalCount',
            'totalSetoran',
            'q',
            'metode'
        ));
    }

    public function prosesSirkulasiHitungPembayaran(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $periodeKey = Carbon::parse($tanggal)->format('Y-m');

        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Penarikan setoran sirkulasi ditolak.");
        }

        $sudah = LogHitungAkuntansi::where('jenis', 'pembayaran_sirkulasi')
            ->whereDate('tanggal_transaksi', $tanggal)
            ->where('status', 'sukses')
            ->exists();

        if ($sudah) {
            return redirect()->back()->with('error', "Pembayaran sirkulasi tanggal {$tanggal} sudah pernah dihitung.");
        }

        $hasOpenShift = ShiftKasir::where(function ($q) use ($tanggal) {
                $q->whereDate('tanggal_shift', $tanggal)
                  ->orWhereDate('tgl_shift', $tanggal);
            })
            ->whereIn('status', ['open', 'aktif'])
            ->exists();

        if ($hasOpenShift) {
            return redirect()->back()->with('error', "Shift Kasir untuk tanggal {$tanggal} masih AKTIF (belum ditutup). Sesuai standar operasional, penarikan setoran sirkulasi HANYA dapat dilakukan setelah kasir melakukan Tutup Kasir (Closing Shift).");
        }

        $daftarSetoran = $this->accountingService->kasMasukAktif()
            ->where(function ($q) {
                $q->where('kode_bagian', '02')
                  ->orWhere('tipe_transaksi', 'sirkulasi');
            })
            ->where(function ($q) use ($tanggal) {
                $q->whereDate('tgl_transaksi', $tanggal)
                  ->orWhereDate('tanggal_terima', $tanggal);
            })
            ->get();

        $totalSetoran = (float) $daftarSetoran->sum('jumlah_dibayar');
        if ($totalSetoran <= 0) {
            return redirect()->back()->with('error', "Tidak ada setoran sirkulasi pada tanggal {$tanggal} untuk diproses.");
        }

        DB::transaction(function () use ($tanggal, $totalSetoran, $daftarSetoran) {
            $noJurnal = JurnalUmum::generateNomorJurnal($tanggal);

            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $tanggal,
                'tipe'         => 'auto',
                'sumber'       => 'sirkulasi',
                'referensi'    => "SETOR-SIRK-{$tanggal}",
                'keterangan'   => "Penerimaan Kas Setoran Agen Sirkulasi {$tanggal}",
                'total_debet'  => $totalSetoran,
                'total_kredit' => $totalSetoran,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            $urutan = 1;
            $debitByAccount = [];
            foreach ($daftarSetoran as $km) {
                $nominalRow = (float) ($km->jumlah_dibayar ?? $km->jumlah_diterima ?? 0);
                if ($nominalRow <= 0) {
                    continue;
                }
                $acc = $this->accountingService->resolveCashOrBankAccount($km);
                if (!isset($debitByAccount[$acc['kode']])) {
                    $debitByAccount[$acc['kode']] = ['nama' => $acc['nama'], 'nominal' => 0.0];
                }
                $debitByAccount[$acc['kode']]['nominal'] += $nominalRow;
            }

            if (empty($debitByAccount)) {
                $accBca = $this->accountingService->getMappedAccount('bank_bca');
                $debitByAccount[$accBca['kode']] = ['nama' => $accBca['nama'], 'nominal' => $totalSetoran];
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

            $akunPiutangSirk = $this->accountingService->getMappedAccount('piutang_sirkulasi');
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $akunPiutangSirk['kode'],
                'nama_perkiraan' => $akunPiutangSirk['nama'],
                'debet'          => 0,
                'kredit'         => $totalSetoran,
                'urutan'         => $urutan++,
            ]);

            // Flagging KasMasuk & Faktur saja — piutang agen & faktur sudah dikurangi kasir saat BKM dibuat / inkaso diapprove
            foreach ($daftarSetoran as $km) {
                $km->update([
                    'is_pulled_accounting'  => true,
                    'status_tarik_akunting' => true,
                ]);

                if (!empty($km->no_faktur_ref)) {
                    Faktur::where('no_faktur', $km->no_faktur_ref)->update([
                        'is_pulled_akuntansi' => true,
                        'pulled_at'           => Carbon::now(),
                    ]);
                }
            }

            LogHitungAkuntansi::create([
                'jenis'             => 'pembayaran_sirkulasi',
                'tanggal_transaksi' => $tanggal,
                'total_faktur'      => $daftarSetoran->count(),
                'total_nominal'     => $totalSetoran,
                'status'            => 'sukses',
                'user_id'           => auth()->id() ?? 1,
                'catatan'           => "Posting penerimaan sirkulasi tanggal {$tanggal}. No Jurnal: {$noJurnal}",
            ]);

            AuditLogAkuntansi::catat(
                'HITUNG_PEMBAYARAN',
                'SIRKULASI',
                "Posting penerimaan sirkulasi tanggal {$tanggal} sebesar Rp " . number_format($totalSetoran, 0, ',', '.') . " (Jurnal: {$noJurnal})"
            );
        });

        return redirect()->back()->with('success', "Setoran sirkulasi tanggal {$tanggal} berhasil diposting ke GL.");
    }

    public function sirkulasiKoreksi(Request $request)
    {
        $query = KoreksiPiutang::where('jenis', 'sirkulasi');

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('no_bukti', 'like', "%{$keyword}%")
                  ->orWhere('nama_entitas', 'like', "%{$keyword}%")
                  ->orWhere('keterangan', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal_koreksi', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_koreksi', '<=', $request->sampai);
        }

        $daftarCn = $query->latest('id')->paginate(15)->withQueryString();
        $daftarAgen = Agen::where('status', 'aktif')->orderBy('nama_agen')->get();
        $nextNoBukti = KoreksiPiutang::generateNoBukti('sirkulasi');
        $hargaDefault = $this->accountingService->getSirkulasiPricing()['harga_eceran'];

        $stats = [
            'total_nota'    => KoreksiPiutang::where('jenis', 'sirkulasi')->count(),
            'total_nominal' => (float) KoreksiPiutang::where('jenis', 'sirkulasi')->sum('jumlah_koreksi'),
            'total_agen'    => KoreksiPiutang::where('jenis', 'sirkulasi')->distinct('nama_entitas')->count('nama_entitas'),
        ];

        return view('pages.accounting.sirkulasi.koreksi', compact('daftarCn', 'daftarAgen', 'nextNoBukti', 'stats', 'hargaDefault'));
    }

    public function storeSirkulasiKoreksi(Request $request)
    {
        $request->validate([
            'agen_id'          => 'required|exists:agens,id',
            'tanggal'          => 'required|date',
            'jumlah_eksemplar' => 'required|integer|min:1',
            'harga_eksemplar'  => 'required|numeric|min:1',
            'alasan'           => 'required|string|max:255',
        ]);

        $periodeKey = Carbon::parse($request->tanggal)->format('Y-m');
        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Penerbitan nota kredit retur sirkulasi ditolak.");
        }

        $agen = Agen::findOrFail($request->agen_id);
        $jumlahKoreksi = $request->jumlah_eksemplar * $request->harga_eksemplar;
        $saldoSebelum = (float) $agen->saldo_piutang;
        $saldoSetelah = max(0, $saldoSebelum - $jumlahKoreksi);

        DB::transaction(function () use ($request, $agen, $jumlahKoreksi, $saldoSetelah) {
            $noBukti = KoreksiPiutang::generateNoBukti('sirkulasi', $request->tanggal);
            $noJurnal = JurnalUmum::generateNomorJurnal($request->tanggal);

            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $request->tanggal,
                'tipe'         => 'auto',
                'sumber'       => 'sirkulasi',
                'referensi'    => $noBukti,
                'keterangan'   => "Retur Koran Agen {$agen->nama_agen} ({$request->jumlah_eksemplar} Eks)",
                'total_debet'  => $jumlahKoreksi,
                'total_kredit' => $jumlahKoreksi,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '4.00.05.01.02.00.00001',
                'nama_perkiraan' => 'Retur & Koreksi Sirkulasi Koran (CN-SIR)',
                'debet'          => $jumlahKoreksi,
                'kredit'         => 0,
                'urutan'         => 1,
            ]);

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '1.00.04.01.01.00.00002',
                'nama_perkiraan' => 'Piutang Usaha Koran Sirkulasi',
                'debet'          => 0,
                'kredit'         => $jumlahKoreksi,
                'urutan'         => 2,
            ]);

            KoreksiPiutang::create([
                'no_bukti'              => $noBukti,
                'jenis'                 => 'sirkulasi',
                'tanggal_koreksi'       => $request->tanggal,
                'faktur_id'             => $agen->id,
                'no_faktur'             => "RETUR-{$agen->kode_agen}",
                'no_order'              => "EKS-{$request->jumlah_eksemplar}",
                'kode_perkiraan'        => '1.00.04.01.01.00.00002',
                'nama_entitas'          => $agen->nama_agen,
                'alamat'                => $agen->wilayah,
                'nilai_penjualan'       => $agen->saldo_piutang,
                'jumlah_koreksi'        => $jumlahKoreksi,
                'saldo_setelah_koreksi' => $saldoSetelah,
                'keterangan'            => "Retur {$request->jumlah_eksemplar} eks @ Rp " . number_format($request->harga_eksemplar, 0, ',', '.') . " - " . $request->alasan,
                'jurnal_umum_id'        => $jurnal->id,
                'user_id'               => auth()->id() ?? 1,
            ]);

            $agen->update(['saldo_piutang' => $saldoSetelah]);

            AuditLogAkuntansi::catat(
                'RETUR_KORAN',
                'SIRKULASI',
                "Penerbitan CN-SIR {$noBukti} untuk agen {$agen->nama_agen} ({$request->jumlah_eksemplar} eks, Rp " . number_format($jumlahKoreksi, 0, ',', '.') . ")"
            );
        });

        return redirect()->back()->with('success', "Nota kredit retur sirkulasi CN-SIR berhasil diterbitkan.");
    }

    public function cetakSirkulasiKoreksi($id)
    {
        $koreksi = KoreksiPiutang::findOrFail($id);
        return view('pages.accounting.sirkulasi.cetak_koreksi', compact('koreksi'));
    }

    public function sirkulasiFakturBulanan(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $statusBayar = $request->input('status', 'all');
        $search = trim((string) $request->input('q', ''));

        $query = SirkulasiFakturBulanan::where('periode_bulan', $bulanPilihan);

        if ($statusBayar !== 'all') {
            $query->where('status_pembayaran', $statusBayar);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_agen', 'like', "%{$search}%")
                  ->orWhere('kode_agen', 'like', "%{$search}%")
                  ->orWhere('no_faktur', 'like', "%{$search}%");
            });
        }

        $fakturList = $query->orderBy('no_faktur')->paginate(20)->withQueryString();

        $baseQuery = SirkulasiFakturBulanan::where('periode_bulan', $bulanPilihan);
        $totalFakturCount = $baseQuery->count();
        $totalOplahGross = (int) $baseQuery->sum('total_oplah_kirim');
        $totalReturEks = (int) $baseQuery->sum('total_retur_eks');
        $totalOplahNetto = (int) $baseQuery->sum('oplah_bersih');
        $totalTagihanKotor = (float) $baseQuery->sum('total_tagihan_kotor');
        $totalPotonganRetur = (float) $baseQuery->sum('total_potongan_retur');
        $totalTagihanBersih = (float) $baseQuery->sum('total_tagihan_bersih');
        $totalTerbayar = (float) $baseQuery->sum('total_terbayar');
        $totalSisaPiutang = (float) $baseQuery->sum('sisa_piutang');

        $daftarAgen = Agen::where('status', 'aktif')->orderBy('nama_agen')->get();
        $returList = SirkulasiRetur::where('periode_bulan', $bulanPilihan)->latest()->get();

        return view('pages.accounting.sirkulasi.faktur_bulanan', compact(
            'bulanPilihan',
            'statusBayar',
            'search',
            'fakturList',
            'totalFakturCount',
            'totalOplahGross',
            'totalReturEks',
            'totalOplahNetto',
            'totalTagihanKotor',
            'totalPotonganRetur',
            'totalTagihanBersih',
            'totalTerbayar',
            'totalSisaPiutang',
            'daftarAgen',
            'returList'
        ));
    }

    public function prosesGenerateFakturBulanan(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $startMonth = Carbon::parse($bulanPilihan . '-01')->startOfMonth()->toDateString();
        $endMonth = Carbon::parse($bulanPilihan . '-01')->endOfMonth()->toDateString();
        $daysInMonth = Carbon::parse($bulanPilihan . '-01')->daysInMonth;

        $calc = $this->accountingService->hitungOmsetSirkulasiRange($startMonth, $endMonth);
        $daftarAgen = $calc['daftar_agen'];
        $pricing = $calc['pricing'];
        $hargaBesar = (float) $pricing['harga_agen_besar'];
        $hargaSub = (float) $pricing['harga_eceran'];

        $tglFaktur = Carbon::parse($bulanPilihan . '-01')->endOfMonth()->toDateString();
        $jatuhTempo = Carbon::parse($bulanPilihan . '-01')->addMonth()->setDay(10)->toDateString();

        DB::transaction(function () use ($bulanPilihan, $daftarAgen, $daysInMonth, $hargaBesar, $hargaSub, $tglFaktur, $jatuhTempo) {
            $index = 1;
            foreach ($daftarAgen as $agen) {
                $totalOplahKirim = 0;
                $rincianHarian = [];
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $dateStr = sprintf('%s-%02d', $bulanPilihan, $d);
                    $harianMap = Cache::get('jatah_harian_' . $dateStr, []);
                    $custom = $harianMap[$agen->id] ?? null;
                    if ($custom) {
                        $dailyEks = (int) ($custom['langganan'] ?? 0) + (int) ($custom['konsinyasi'] ?? 0) + (int) ($custom['gratis'] ?? 0) + (int) ($custom['pengenalan'] ?? 0) + (int) ($custom['insidentil'] ?? 0);
                    } else {
                        $dailyEks = (int) $agen->jatah_default;
                    }
                    $totalOplahKirim += $dailyEks;
                    $rincianHarian[] = [
                        'tanggal' => $dateStr,
                        'hari'    => $d,
                        'oplah'   => $dailyEks,
                    ];
                }

                $totalReturApproved = (int) SirkulasiRetur::where('periode_bulan', $bulanPilihan)
                    ->where('agen_id', $agen->id)
                    ->where('status', 'disetujui')
                    ->sum('jumlah_retur_disetujui');

                $baprLatest = SirkulasiRetur::where('periode_bulan', $bulanPilihan)
                    ->where('agen_id', $agen->id)
                    ->latest('id')
                    ->value('no_bapr');

                $oplahBersih = max(0, $totalOplahKirim - $totalReturApproved);

                $isBesar = in_array((string) $agen->kategori, ['11', 'agen_besar'], true);
                $tarif = $isBesar ? $hargaBesar : $hargaSub;

                $totalTagihanKotor = $totalOplahKirim * $tarif;
                $totalPotonganRetur = $totalReturApproved * $tarif;
                $totalTagihanBersih = $oplahBersih * $tarif;

                $fakturBulanan = SirkulasiFakturBulanan::where('periode_bulan', $bulanPilihan)
                    ->where('agen_id', $agen->id)
                    ->first();

                if (!$fakturBulanan) {
                    $noFaktur = SirkulasiFakturBulanan::generateNoFaktur($bulanPilihan, $index++);
                    $fakturBulanan = new SirkulasiFakturBulanan();
                    $fakturBulanan->no_faktur = $noFaktur;
                    $fakturBulanan->periode_bulan = $bulanPilihan;
                    $fakturBulanan->agen_id = $agen->id;
                    $fakturBulanan->total_terbayar = 0;
                }

                $sisaPiutang = max(0, $totalTagihanBersih - (float) $fakturBulanan->total_terbayar);
                $statusBayar = $sisaPiutang <= 0 ? 'lunas' : ((float) $fakturBulanan->total_terbayar > 0 ? 'sebagian' : 'belum_lunas');

                $fakturBulanan->kode_agen = $agen->kode_agen;
                $fakturBulanan->nama_agen = $agen->nama_agen;
                $fakturBulanan->kategori_agen = (string) $agen->kategori;
                $fakturBulanan->wilayah = $agen->wilayah;
                $fakturBulanan->total_oplah_kirim = $totalOplahKirim;
                $fakturBulanan->total_retur_eks = $totalReturApproved;
                $fakturBulanan->oplah_bersih = $oplahBersih;
                $fakturBulanan->tarif_per_eks = $tarif;
                $fakturBulanan->total_tagihan_kotor = $totalTagihanKotor;
                $fakturBulanan->total_potongan_retur = $totalPotonganRetur;
                $fakturBulanan->total_tagihan_bersih = $totalTagihanBersih;
                $fakturBulanan->sisa_piutang = $sisaPiutang;
                $fakturBulanan->status_pembayaran = $statusBayar;
                $fakturBulanan->rincian_harian = $rincianHarian;
                $fakturBulanan->no_bapr = $baprLatest;
                $fakturBulanan->tanggal_faktur = $tglFaktur;
                $fakturBulanan->jatuh_tempo = $jatuhTempo;
                $fakturBulanan->diterbitkan_oleh = auth()->id();
                $fakturBulanan->save();

                $fakturMaster = Faktur::where('no_faktur', $fakturBulanan->no_faktur)->first();
                if (!$fakturMaster) {
                    $fakturMaster = new Faktur();
                    $fakturMaster->no_faktur = $fakturBulanan->no_faktur;
                    $fakturMaster->no_order = "SRK-{$bulanPilihan}-" . $agen->kode_agen;
                    $fakturMaster->kategori = 'sirkulasi';
                    $fakturMaster->tipe_faktur = 'bulanan';
                    $fakturMaster->customer_id = $agen->id;
                    $fakturMaster->sales_nama = 'Divisi Sirkulasi';
                    $fakturMaster->tanggal_faktur = $tglFaktur;
                    $fakturMaster->jatuh_tempo = $jatuhTempo;
                    $fakturMaster->total_terbayar = 0;
                    $fakturMaster->created_by = auth()->id() ?? 1;
                }
                $fakturMaster->brutto = $totalTagihanKotor;
                $fakturMaster->diskon_nominal = $totalPotonganRetur;
                $fakturMaster->netto = $totalTagihanBersih;
                $fakturMaster->total_tagihan = $totalTagihanBersih;
                $fakturMaster->sisa_piutang = $sisaPiutang;
                $fakturMaster->status = $sisaPiutang <= 0 ? 'lunas' : 'belum_bayar';
                $fakturMaster->status_bayar = $sisaPiutang <= 0 ? 'lunas' : 'belum_bayar';
                $fakturMaster->rincian_biaya = [
                    'periode'   => $bulanPilihan,
                    'kirim_eks' => $totalOplahKirim,
                    'retur_eks' => $totalReturApproved,
                    'nett_eks'  => $oplahBersih,
                    'tarif'     => $tarif,
                    'no_bapr'   => $baprLatest,
                ];
                $fakturMaster->save();
            }

            AuditLogAkuntansi::catat(
                'GENERATE_FAKTUR_BULANAN',
                'SIRKULASI',
                "Penerbitan Faktur Sirkulasi Bulanan Terkonsolidasi periode {$bulanPilihan} untuk " . $daftarAgen->count() . " agen."
            );
        });

        return redirect()->back()->with('success', "Faktur Sirkulasi Bulanan periode {$bulanPilihan} berhasil di-generate dan siap ditagihkan ke Agen.");
    }

    public function storeVerifikasiRetur(Request $request)
    {
        $request->validate([
            'periode_bulan'          => 'required|string',
            'agen_id'                => 'required|exists:agens,id',
            'tanggal_retur'          => 'required|date',
            'jumlah_retur_disetujui' => 'required|integer|min:0',
            'jumlah_retur_fisik'     => 'nullable|integer|min:0',
            'catatan_kondisi'        => 'nullable|string',
        ]);

        $agen = Agen::findOrFail($request->agen_id);
        $periodeBulan = $request->periode_bulan;

        if (PeriodeAkuntansi::isPeriodeLocked($periodeBulan)) {
            return redirect()->back()->with('error', "Periode {$periodeBulan} sudah ditutup (terkunci). Verifikasi retur BAPR ditolak.");
        }

        $noBapr = SirkulasiRetur::generateNoBapr($periodeBulan);
        $returFisik = $request->jumlah_retur_fisik ?? $request->jumlah_retur_disetujui;
        $pricing = $this->accountingService->getSirkulasiPricing();
        $isBesar = in_array((string) $agen->kategori, ['11', 'agen_besar'], true);
        $tarifDefault = $isBesar ? (float) $pricing['harga_agen_besar'] : (float) $pricing['harga_eceran'];

        DB::transaction(function () use ($request, $agen, $periodeBulan, $noBapr, $returFisik, $tarifDefault) {
            SirkulasiRetur::create([
                'no_bapr'                => $noBapr,
                'periode_bulan'          => $periodeBulan,
                'agen_id'                => $agen->id,
                'kode_agen'              => $agen->kode_agen,
                'nama_agen'              => $agen->nama_agen,
                'tanggal_retur'          => $request->tanggal_retur,
                'jumlah_retur_fisik'     => $returFisik,
                'jumlah_retur_disetujui' => $request->jumlah_retur_disetujui,
                'catatan_kondisi'        => $request->catatan_kondisi,
                'status'                 => 'disetujui',
                'diverifikasi_oleh'      => auth()->id(),
            ]);

            $faktur = SirkulasiFakturBulanan::where('periode_bulan', $periodeBulan)
                ->where('agen_id', $agen->id)
                ->first();

            $tarifPakai = $faktur ? (float) $faktur->tarif_per_eks : $tarifDefault;
            $potonganJurnal = (float) $request->jumlah_retur_disetujui * $tarifPakai;

            if ($faktur) {
                $totalRetur = (int) SirkulasiRetur::where('periode_bulan', $periodeBulan)
                    ->where('agen_id', $agen->id)
                    ->where('status', 'disetujui')
                    ->sum('jumlah_retur_disetujui');

                $oplahBersih = max(0, $faktur->total_oplah_kirim - $totalRetur);
                $potonganRetur = $totalRetur * $faktur->tarif_per_eks;
                $tagihanBersih = $oplahBersih * $faktur->tarif_per_eks;
                $sisaPiutang = max(0, $tagihanBersih - (float) $faktur->total_terbayar);

                $faktur->update([
                    'total_retur_eks'      => $totalRetur,
                    'oplah_bersih'         => $oplahBersih,
                    'total_potongan_retur' => $potonganRetur,
                    'total_tagihan_bersih' => $tagihanBersih,
                    'sisa_piutang'         => $sisaPiutang,
                    'status_pembayaran'    => $sisaPiutang <= 0 ? 'lunas' : ((float) $faktur->total_terbayar > 0 ? 'sebagian' : 'belum_lunas'),
                    'no_bapr'              => $noBapr,
                ]);

                $fMaster = Faktur::where('no_faktur', $faktur->no_faktur)->first();
                if ($fMaster) {
                    $fMaster->update([
                        'diskon_nominal' => $potonganRetur,
                        'netto'          => $tagihanBersih,
                        'total_tagihan'  => $tagihanBersih,
                        'sisa_piutang'   => $sisaPiutang,
                        'status'         => $sisaPiutang <= 0 ? 'lunas' : 'belum_bayar',
                        'status_bayar'   => $sisaPiutang <= 0 ? 'lunas' : 'belum_bayar',
                    ]);
                }
            }

            if ($potonganJurnal > 0) {
                $noJurnal = JurnalUmum::generateNomorJurnal($request->tanggal_retur);
                $akunRetur = $this->accountingService->getMappedAccount('retur_sirkulasi');
                $akunPiutang = $this->accountingService->getMappedAccount('piutang_sirkulasi');

                $jurnal = JurnalUmum::create([
                    'no_jurnal'    => $noJurnal,
                    'tanggal'      => $request->tanggal_retur,
                    'tipe'         => 'auto',
                    'sumber'       => 'sirkulasi',
                    'referensi'    => $noBapr,
                    'keterangan'   => "BAPR Retur Koran {$agen->nama_agen} ({$request->jumlah_retur_disetujui} eks, {$periodeBulan})",
                    'total_debet'  => $potonganJurnal,
                    'total_kredit' => $potonganJurnal,
                    'is_balanced'  => true,
                    'status'       => 'posted',
                    'user_id'      => auth()->id() ?? 1,
                ]);

                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => $akunRetur['kode'],
                    'nama_perkiraan' => $akunRetur['nama'],
                    'debet'          => $potonganJurnal,
                    'kredit'         => 0,
                    'urutan'         => 1,
                ]);

                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => $akunPiutang['kode'],
                    'nama_perkiraan' => $akunPiutang['nama'],
                    'debet'          => 0,
                    'kredit'         => $potonganJurnal,
                    'urutan'         => 2,
                ]);
            }

            AuditLogAkuntansi::catat(
                'VERIFIKASI_RETUR',
                'SIRKULASI',
                "Penerbitan BAPR {$noBapr} retur koran {$agen->nama_agen} ({$request->jumlah_retur_disetujui} eks, periode {$periodeBulan})"
            );
        });

        return redirect()->back()->with('success', "Verifikasi retur fisik koran berhasil disimpan (No. BAPR: {$noBapr}). Tagihan agen telah disesuaikan.");
    }

    public function cetakFakturBulanan($id)
    {
        $faktur = SirkulasiFakturBulanan::with('agen')->findOrFail($id);
        $cfg = $this->accountingService->getSirkulasiPricing();
        return view('pages.accounting.sirkulasi.cetak_faktur_bulanan', compact('faktur', 'cfg'));
    }

    public function sirkulasiBukaData(Request $request)
    {
        $query = MemoUnlock::query();
        $query->where(function ($q) {
            $q->where('tujuan_divisi', 'sirkulasi')
              ->orWhere('nomor_memo', 'like', '%SIRK%');
        });

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_memo', 'like', "%{$keyword}%")
                  ->orWhere('alasan', 'like', "%{$keyword}%")
                  ->orWhere('diketahui_oleh', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status_akuntansi', strtoupper($request->status));
        }

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $daftarMemo = $query->latest('id')->paginate(15)->withQueryString();

        $year = now()->format('Y');
        $month = now()->format('m');
        $countSirk = MemoUnlock::where('nomor_memo', 'like', "MEMO/SIRK/{$year}/{$month}%")->count() + 1;
        $nextNoMemo = "MEMO/SIRK/{$year}/{$month}/" . str_pad((string) $countSirk, 4, '0', STR_PAD_LEFT);

        $stats = [
            'total'              => MemoUnlock::where(fn ($q) => $q->where('tujuan_divisi', 'sirkulasi')->orWhere('nomor_memo', 'like', '%SIRK%'))->count(),
            'menunggu_otorisasi' => MemoUnlock::where(fn ($q) => $q->where('tujuan_divisi', 'sirkulasi')->orWhere('nomor_memo', 'like', '%SIRK%'))->where('status_akuntansi', 'PENDING')->count(),
            'disetujui'          => MemoUnlock::where(fn ($q) => $q->where('tujuan_divisi', 'sirkulasi')->orWhere('nomor_memo', 'like', '%SIRK%'))->where('status_akuntansi', 'APPROVED')->count(),
            'bulan_ini'          => LogHitungAkuntansi::where('jenis', 'penjualan_sirkulasi')
                ->where('bulan_periode', (int) now()->format('m'))
                ->where('tahun_periode', (int) now()->format('Y'))
                ->where('status', 'sukses')
                ->exists() ? 'Terkunci' : 'Terbuka',
        ];

        $memoPending = MemoUnlock::where(function ($q) {
                $q->where('tujuan_divisi', 'sirkulasi')->orWhere('nomor_memo', 'like', '%SIRK%');
            })
            ->where('status_akuntansi', 'PENDING')
            ->latest('id')
            ->get();

        return view('pages.accounting.sirkulasi.buka_data', compact('daftarMemo', 'nextNoMemo', 'stats', 'memoPending'));
    }

    public function prosesSirkulasiBukaData(Request $request)
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
                    'TOLAK_KUNCI',
                    'SIRKULASI',
                    "Penolakan Memo Unlock Sirkulasi {$memo->nomor_memo} target edisi {$memo->tanggal_target}. Alasan: {$request->alasan_penolakan}"
                );

                return redirect()->back()->with('success', "Memo {$memo->nomor_memo} resmi DITOLAK dengan catatan.");
            }

            $memo->update([
                'status_akuntansi'        => 'APPROVED',
                'status'                  => 'OPEN',
                'direspon_oleh_user_id'   => Auth::id(),
                'diterima_akuntansi_pada' => now(),
                'catatan_akuntansi'       => $request->alasan ?: 'Disetujui oleh Divisi Akuntansi',
            ]);

            AuditLogAkuntansi::catat(
                'BUKA_KUNCI',
                'SIRKULASI',
                "Persetujuan Memo Unlock Sirkulasi {$memo->nomor_memo} target edisi {$memo->tanggal_target}"
            );

            return redirect()->back()->with('success', "Memo {$memo->nomor_memo} berhasil disetujui. Input/Edit oplah edisi {$memo->tanggal_target} dibuka kembali.");
        }

        $request->validate([
            'bulan'   => 'required|string',
            'no_memo' => 'required|string',
            'alasan'  => 'required|string',
        ]);

        $bulanInt = (int) substr($request->bulan, 5, 2);
        $tahunInt = (int) substr($request->bulan, 0, 4);
        $startMonth = Carbon::parse($request->bulan . '-01')->startOfMonth()->toDateString();
        $endMonth = Carbon::parse($request->bulan . '-01')->endOfMonth()->toDateString();

        if (PeriodeAkuntansi::isPeriodeLocked($request->bulan)) {
            return redirect()->back()->with('error', "Periode {$request->bulan} sudah ditutup (terkunci). Pembatalan hitung sirkulasi ditolak.");
        }

        $calc = $this->accountingService->hitungOmsetSirkulasiRange($startMonth, $endMonth);
        $daftarAgen = $calc['daftar_agen'];
        $perAgenCalc = $calc['per_agen'];

        DB::transaction(function () use ($request, $bulanInt, $tahunInt, $daftarAgen, $perAgenCalc) {
            $jurnal = JurnalUmum::where('sumber', 'sirkulasi')
                ->where('referensi', "SIRK-{$request->bulan}")
                ->whereNull('reversed_by_jurnal_id')
                ->first();

            if ($jurnal) {
                $this->accountingService->reverseJurnal($jurnal, "Buka kunci sirkulasi {$request->bulan} (Memo: {$request->no_memo})");
            }

            foreach ($daftarAgen as $agen) {
                $omsetAgen = (float) ($perAgenCalc[$agen->id]['nominal'] ?? 0);
                if ($omsetAgen > 0) {
                    $agen->update(['saldo_piutang' => max(0, (float) $agen->saldo_piutang - $omsetAgen)]);
                }
            }

            $log = LogHitungAkuntansi::where('jenis', 'penjualan_sirkulasi')
                ->where('bulan_periode', $bulanInt)
                ->where('tahun_periode', $tahunInt)
                ->first();

            if ($log) {
                $log->update(['status' => 'batal_dibuka']);
            }

            AuditLogAkuntansi::catat(
                'BUKA_KUNCI',
                'SIRKULASI',
                "Buka kunci hitung sirkulasi bulan {$request->bulan} via memo {$request->no_memo}. Alasan: {$request->alasan} (Jurnal & saldo agen di-revert)"
            );
        });

        return redirect()->back()->with('success', "Kunci sirkulasi bulan {$request->bulan} berhasil dibuka dan pembukuan di-revert.");
    }

    public function cetakMemo(string $id)
    {
        $memo = MemoUnlock::with(['dibukaOleh', 'diresponOleh'])->findOrFail($id);
        return view('pages.accounting.cetak_memo', compact('memo'));
    }

    public function memoHistory(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $divisi = strtolower(trim((string) $request->input('divisi', 'semua')));
        $status = strtoupper(trim((string) $request->input('status', '')));
        $tanggal = $request->input('tanggal');

        $stats = [
            'total'     => MemoUnlock::count(),
            'sirkulasi' => MemoUnlock::where(function ($sub) {
                $sub->where('tujuan_divisi', 'sirkulasi')
                    ->orWhere('nomor_memo', 'like', '%SRK%')
                    ->orWhere('nomor_memo', 'like', '%SIRK%')
                    ->orWhere('nomor_memo', 'like', 'REQ-ULK-%');
            })->count(),
            'iklan'     => MemoUnlock::where(function ($sub) {
                $sub->where('tujuan_divisi', 'iklan')
                    ->orWhere(function ($nested) {
                        $nested->whereNotIn('tujuan_divisi', ['sirkulasi', 'kasir'])
                               ->where('nomor_memo', 'like', '%IKL%')
                               ->orWhere(function ($raw) {
                                   $raw->whereNotIn('tujuan_divisi', ['sirkulasi', 'kasir'])
                                       ->where('nomor_memo', 'not like', '%SRK%')
                                       ->where('nomor_memo', 'not like', '%SIRK%')
                                       ->where('nomor_memo', 'not like', 'REQ-ULK-%')
                                       ->where('nomor_memo', 'not like', '%KAS%');
                               });
                    });
            })->count(),
            'kasir'     => MemoUnlock::where(function ($sub) {
                $sub->where('tujuan_divisi', 'kasir')
                    ->orWhere('nomor_memo', 'like', '%KAS%');
            })->count(),
            'pending'   => MemoUnlock::where('status_akuntansi', 'PENDING')->count(),
        ];

        $query = MemoUnlock::with(['dibukaOleh', 'diresponOleh'])->latest();

        if ($divisi === 'sirkulasi') {
            $query->where(function ($sub) {
                $sub->where('tujuan_divisi', 'sirkulasi')
                    ->orWhere('nomor_memo', 'like', '%SRK%')
                    ->orWhere('nomor_memo', 'like', '%SIRK%')
                    ->orWhere('nomor_memo', 'like', 'REQ-ULK-%');
            });
        } elseif ($divisi === 'iklan') {
            $query->where(function ($sub) {
                $sub->where('tujuan_divisi', 'iklan')
                    ->orWhere(function ($nested) {
                        $nested->whereNotIn('tujuan_divisi', ['sirkulasi', 'kasir'])
                               ->where('nomor_memo', 'like', '%IKL%')
                               ->orWhere(function ($raw) {
                                   $raw->whereNotIn('tujuan_divisi', ['sirkulasi', 'kasir'])
                                       ->where('nomor_memo', 'not like', '%SRK%')
                                       ->where('nomor_memo', 'not like', '%SIRK%')
                                       ->where('nomor_memo', 'not like', 'REQ-ULK-%')
                                       ->where('nomor_memo', 'not like', '%KAS%');
                               });
                    });
            });
        } elseif ($divisi === 'kasir') {
            $query->where(function ($sub) {
                $sub->where('tujuan_divisi', 'kasir')
                    ->orWhere('nomor_memo', 'like', '%KAS%');
            });
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nomor_memo', 'like', "%{$q}%")
                    ->orWhere('alasan', 'like', "%{$q}%")
                    ->orWhere('nama_pemasang', 'like', "%{$q}%")
                    ->orWhere('catatan_akuntansi', 'like', "%{$q}%");
            });
        }

        if ($status !== '' && $status !== 'SEMUA') {
            if ($status === 'APPROVED') {
                $query->whereIn('status_akuntansi', ['APPROVED', 'OPEN', 'DISETUJUI', 'TERKIRIM']);
            } elseif ($status === 'REJECTED') {
                $query->whereIn('status_akuntansi', ['REJECTED', 'DITOLAK']);
            } else {
                $query->where('status_akuntansi', $status);
            }
        }

        if (!empty($tanggal)) {
            $query->whereDate('tanggal_target', $tanggal);
        }

        $memos = $query->paginate(15)->withQueryString();

        return view('pages.accounting.memo.history', compact('memos', 'stats', 'q', 'divisi', 'status', 'tanggal'));
    }

    public function sirkulasiAgen(Request $request)
    {
        $search = $request->input('search');
        $kategoriFilter = $request->input('kategori', 'all');
        $statusFilter = $request->input('status', 'all');
        $wilayahFilter = $request->input('wilayah', 'all');
        $jurusanFilter = $request->input('jurusan', 'all');

        $query = Agen::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_agen', 'like', "%{$search}%")
                  ->orWhere('kode_agen', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('wilayah', 'like', "%{$search}%");
            });
        }

        if ($kategoriFilter !== 'all') {
            $query->where('kategori', $kategoriFilter);
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($wilayahFilter !== 'all') {
            $query->where('wilayah', $wilayahFilter);
        }

        if ($jurusanFilter !== 'all') {
            $query->where('rute_kode', $jurusanFilter);
        }

        $agenList = $query->orderBy('kode_agen')->get();

        $sirkulasiCtrl = app(SirkulasiDataService::class);
        $wilayahList = $sirkulasiCtrl->getDaftarWilayah();
        $jurusanList = $sirkulasiCtrl->getDaftarJurusan();
        $jenisList = $sirkulasiCtrl->getDaftarJenisAgen();

        $stats = [
            'total_agen'    => Agen::count(),
            'agen_besar'    => Agen::where('kategori', '11')->count(),
            'sub_agen'      => Agen::where('kategori', '99')->count(),
            'nonaktif'      => Agen::where('kategori', '00')->orWhere('status', 'nonaktif')->count(),
            'total_piutang' => (float) Agen::sum('saldo_piutang'),
        ];

        return view('pages.accounting.sirkulasi.agen', compact(
            'agenList',
            'wilayahList',
            'jurusanList',
            'jenisList',
            'search',
            'kategoriFilter',
            'statusFilter',
            'wilayahFilter',
            'jurusanFilter',
            'stats'
        ));
    }

    public function storeSirkulasiAgen(Request $request)
    {
        $request->validate([
            'nama_agen'      => 'required|string|max:150',
            'kategori'       => 'required|in:11,99,00',
            'wilayah_kode'   => 'required|string',
            'jurusan_kode'   => 'required|string',
            'alamat'         => 'nullable|string|max:255',
            'telepon'        => 'nullable|string|max:50',
            'jatah_default'  => 'required|integer|min:0',
            'plafon_piutang' => 'nullable|numeric|min:0',
            'induk_agen'     => 'nullable|string|max:150',
        ]);

        $kategori = $request->input('kategori', '11');
        $kodeInput = trim((string) $request->input('kode_agen', ''));
        if ($kodeInput === '') {
            $nextNum = Agen::count() + 1;
            $kodeInput = sprintf('AG-%s-%03d', $kategori, $nextNum);
            while (Agen::where('kode_agen', $kodeInput)->exists()) {
                $nextNum++;
                $kodeInput = sprintf('AG-%s-%03d', $kategori, $nextNum);
            }
        }

        $sirkulasiCtrl = app(SirkulasiDataService::class);
        $wilayahObj = $sirkulasiCtrl->getDaftarWilayah()->firstWhere('kode', $request->wilayah_kode);
        $jurusanObj = $sirkulasiCtrl->getDaftarJurusan()->firstWhere('kode', $request->jurusan_kode);
        $wilayahNama = $wilayahObj->nama ?? 'Tasikmalaya';
        $jurusanNama = $jurusanObj->nama_jurusan ?? $jurusanObj->nama_rute ?? 'Tasikmalaya Kota - Singaparna';
        $isNonAktif = $kategori === '00';

        $agen = Agen::updateOrCreate(
            ['kode_agen' => strtoupper($kodeInput)],
            [
                'nama_agen'      => $request->nama_agen,
                'kategori'       => $kategori,
                'induk_agen'     => $request->input('induk_agen') ?: '-',
                'rute_kode'      => $request->jurusan_kode,
                'wilayah'        => $wilayahNama,
                'alamat'         => $request->input('alamat') ?: ('Wilayah ' . $wilayahNama),
                'telepon'        => $request->input('telepon') ?: '-',
                'jatah_default'  => $isNonAktif ? 0 : (int) $request->jatah_default,
                'plafon_piutang' => (float) $request->input('plafon_piutang', 5000000),
                'saldo_piutang'  => 0,
                'status'         => $isNonAktif ? 'nonaktif' : 'aktif',
            ]
        );

        $metaMap = Cache::get('sirkulasi_agen_meta', []);
        $metaMap[$agen->kode_agen] = [
            'tanggal_daftar' => $request->input('tanggal_daftar', date('Y-m-d')),
            'alamat'         => $request->input('alamat') ?: ('Wilayah ' . $wilayahNama),
            'wilayah_kode'   => $request->wilayah_kode,
            'wilayah_nama'   => $wilayahNama,
            'jurusan_kode'   => $request->jurusan_kode,
            'jurusan_nama'   => $jurusanNama,
            'kategori_asli'  => $kategori === '00' ? '11' : $kategori,
        ];
        Cache::put('sirkulasi_agen_meta', $metaMap, now()->addDays(30));

        if (class_exists(AuditLogAkuntansi::class) && Schema::hasTable('audit_log_akuntansis')) {
            AuditLogAkuntansi::catat('TAMBAH_AGEN', 'AKUNTANSI_SIRKULASI', "Penambahan agen koran baru oleh Akuntansi: {$agen->kode_agen} - {$agen->nama_agen}");
        }

        return redirect()->route('accounting.sirkulasi.agen')
            ->with('success', "Data Agen ({$agen->kode_agen} - {$agen->nama_agen}) berhasil ditambahkan oleh Divisi Akuntansi.");
    }

    public function updateSirkulasiAgen(Request $request, $id)
    {
        $agen = Agen::findOrFail($id);

        $request->validate([
            'nama_agen'      => 'required|string|max:150',
            'kategori'       => 'required|in:11,99,00',
            'wilayah_kode'   => 'required|string',
            'jurusan_kode'   => 'required|string',
            'alamat'         => 'nullable|string|max:255',
            'telepon'        => 'nullable|string|max:50',
            'jatah_default'  => 'required|integer|min:0',
            'plafon_piutang' => 'nullable|numeric|min:0',
            'induk_agen'     => 'nullable|string|max:150',
        ]);

        $kategori = $request->input('kategori', '11');
        $sirkulasiCtrl = app(SirkulasiDataService::class);
        $wilayahObj = $sirkulasiCtrl->getDaftarWilayah()->firstWhere('kode', $request->wilayah_kode);
        $jurusanObj = $sirkulasiCtrl->getDaftarJurusan()->firstWhere('kode', $request->jurusan_kode);
        $wilayahNama = $wilayahObj->nama ?? $agen->wilayah;
        $jurusanNama = $jurusanObj->nama_jurusan ?? $jurusanObj->nama_rute ?? 'Tasikmalaya Kota - Singaparna';
        $isNonAktif = $kategori === '00';

        $agen->update([
            'nama_agen'      => $request->nama_agen,
            'kategori'       => $kategori,
            'induk_agen'     => $request->input('induk_agen') ?: '-',
            'rute_kode'      => $request->jurusan_kode,
            'wilayah'        => $wilayahNama,
            'alamat'         => $request->input('alamat') ?: $agen->alamat,
            'telepon'        => $request->input('telepon') ?: '-',
            'jatah_default'  => $isNonAktif ? 0 : (int) $request->jatah_default,
            'plafon_piutang' => (float) $request->input('plafon_piutang', $agen->plafon_piutang),
            'status'         => $isNonAktif ? 'nonaktif' : 'aktif',
        ]);

        $metaMap = Cache::get('sirkulasi_agen_meta', []);
        $existingMeta = $metaMap[$agen->kode_agen] ?? [];
        $metaMap[$agen->kode_agen] = array_merge($existingMeta, [
            'alamat'        => $request->input('alamat') ?: ($existingMeta['alamat'] ?? ('Wilayah ' . $wilayahNama)),
            'wilayah_kode'  => $request->wilayah_kode,
            'wilayah_nama'  => $wilayahNama,
            'jurusan_kode'  => $request->jurusan_kode,
            'jurusan_nama'  => $jurusanNama,
            'kategori_asli' => $kategori === '00' ? ($existingMeta['kategori_asli'] ?? '11') : $kategori,
        ]);
        Cache::put('sirkulasi_agen_meta', $metaMap, now()->addDays(30));

        if (class_exists(AuditLogAkuntansi::class) && Schema::hasTable('audit_log_akuntansis')) {
            AuditLogAkuntansi::catat('EDIT_AGEN', 'AKUNTANSI_SIRKULASI', "Perubahan data agen oleh Akuntansi: {$agen->kode_agen} - {$agen->nama_agen}");
        }

        return redirect()->route('accounting.sirkulasi.agen')
            ->with('success', "Data Agen ({$agen->kode_agen} - {$agen->nama_agen}) berhasil diperbarui.");
    }

    public function destroySirkulasiAgen($id)
    {
        $agen = Agen::findOrFail($id);

        if ($agen->saldo_piutang > 0) {
            return redirect()->route('accounting.sirkulasi.agen')
                ->with('error', "Gagal Menghapus: Agen {$agen->nama_agen} masih memiliki saldo piutang berjalan sebesar Rp " . number_format($agen->saldo_piutang, 0, ',', '.') . ". Set status agen menjadi Non-Aktif (00).");
        }

        $kode = $agen->kode_agen;
        $nama = $agen->nama_agen;

        $metaMap = Cache::get('sirkulasi_agen_meta', []);
        unset($metaMap[$kode]);
        Cache::put('sirkulasi_agen_meta', $metaMap, now()->addDays(30));

        $agen->delete();

        if (class_exists(AuditLogAkuntansi::class) && Schema::hasTable('audit_log_akuntansis')) {
            AuditLogAkuntansi::catat('HAPUS_AGEN', 'AKUNTANSI_SIRKULASI', "Penghapusan agen koran: {$kode} - {$nama}");
        }

        return redirect()->route('accounting.sirkulasi.agen')
            ->with('success', "Data Agen ({$kode} - {$nama}) berhasil dihapus dari sistem.");
    }

    public function toggleStatusSirkulasiAgen($id)
    {
        $agen = Agen::findOrFail($id);
        $newStatus = $agen->status === 'aktif' ? 'nonaktif' : 'aktif';
        $newKategori = $newStatus === 'nonaktif' ? '00' : ($agen->kategori === '00' ? '11' : $agen->kategori);

        $agen->update([
            'status'   => $newStatus,
            'kategori' => $newKategori,
        ]);

        if (class_exists(AuditLogAkuntansi::class) && Schema::hasTable('audit_log_akuntansis')) {
            AuditLogAkuntansi::catat('TOGGLE_STATUS_AGEN', 'AKUNTANSI_SIRKULASI', "Status agen {$agen->kode_agen} diubah menjadi {$newStatus}");
        }

        return redirect()->route('accounting.sirkulasi.agen')
            ->with('success', "Status agen {$agen->nama_agen} berhasil diubah menjadi " . strtoupper($newStatus) . ".");
    }

    public function sirkulasiAgenSaldo($id)
    {
        $agen = Agen::findOrFail($id);
        $startMonth = Carbon::today()->startOfMonth()->toDateString();
        $endMonth = Carbon::today()->endOfMonth()->toDateString();
        $pricing = $this->accountingService->getSirkulasiPricing();

        $penjualanBulanIni = 0.0;
        $cursor = Carbon::parse($startMonth);
        $end = Carbon::parse($endMonth);
        while ($cursor->lte($end)) {
            $savedMap = Cache::get('jatah_harian_' . $cursor->toDateString(), []);
            $penjualanBulanIni += $this->accountingService->hitungNilaiJatahAgenHarian($agen, $savedMap[$agen->id] ?? null, $pricing)['nominal'];
            $cursor->addDay();
        }

        $pembayaranBulanIni = (float) $this->accountingService->kasMasukAktif()
            ->where(function ($q) use ($agen) {
                $q->where('kode_agen_ref', $agen->kode_agen)
                  ->orWhere('terima_dari', 'like', '%' . $agen->nama_agen . '%');
            })
            ->whereYear('tanggal_terima', Carbon::today()->year)
            ->whereMonth('tanggal_terima', Carbon::today()->month)
            ->sum('jumlah_dibayar');

        $koreksiBulanIni = (float) KoreksiPiutang::where('jenis', 'sirkulasi')
            ->where(function ($q) use ($agen) {
                $q->where('kode_perkiraan', 'like', '%' . $agen->kode_agen . '%')
                  ->orWhere('nama_entitas', $agen->nama_agen);
            })
            ->whereYear('tanggal_koreksi', Carbon::today()->year)
            ->whereMonth('tanggal_koreksi', Carbon::today()->month)
            ->sum('jumlah_koreksi');

        $saldoBerjalan = (float) $agen->saldo_piutang;
        $sisaPiutangLalu = max(0, $saldoBerjalan - $penjualanBulanIni + $pembayaranBulanIni + $koreksiBulanIni);

        return response()->json([
            'success'              => true,
            'agen'                 => $agen,
            'sisa_piutang_lalu'    => $sisaPiutangLalu,
            'penjualan_bulan_ini'  => $penjualanBulanIni,
            'pembayaran_bulan_ini' => $pembayaranBulanIni,
            'koreksi_bulan_ini'    => $koreksiBulanIni,
            'saldo_berjalan'       => $saldoBerjalan,
        ]);
    }
}
