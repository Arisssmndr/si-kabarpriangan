<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\AkunPerkiraan;
use App\Models\AsetTetap;
use App\Models\AuditLogAkuntansi;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumDetail;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\PengajuanAnggaran;
use App\Models\PeriodeAkuntansi;
use App\Models\RekonsiliasiBank;
use App\Models\ShiftKasir;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BebanDanBankController extends Controller
{
    public function __construct(protected AccountingService $accountingService)
    {
    }

    public function bebanVerifikasi(Request $request)
    {
        $query = $this->accountingService->kasKeluarBebanAktif()->with('kasir');

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('no_bkk', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%")
                    ->orWhere('diberikan_kepada', 'like', "%{$q}%")
                    ->orWhere('kategori', 'like', "%{$q}%");
            });
        }

        if ($request->input('status') === 'pending') {
            $query->where('is_pulled_accounting', false);
        } elseif ($request->input('status') === 'posted') {
            $query->where('is_pulled_accounting', true);
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal_keluar', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_keluar', '<=', $request->sampai);
        }

        $daftarBeban = $query->latest('tanggal_keluar')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $daftarAkunBeban = AkunPerkiraan::whereIn('kepala', [5, 6])
            ->orWhere('kategori', 'like', '%Beban%')
            ->aktif()
            ->orderBy('kode_perkiraan')
            ->get();

        $totalBelumPosting = (float) $this->accountingService->kasKeluarBebanAktif()->where('is_pulled_accounting', false)->sum('nominal_keluar');
        $totalSudahPosting = (float) $this->accountingService->kasKeluarBebanAktif()->where('is_pulled_accounting', true)->sum('nominal_keluar');
        $countBelumPosting = $this->accountingService->kasKeluarBebanAktif()->where('is_pulled_accounting', false)->count();
        $countSudahPosting = $this->accountingService->kasKeluarBebanAktif()->where('is_pulled_accounting', true)->count();

        return view('pages.accounting.beban.verifikasi', compact(
            'daftarBeban',
            'daftarAkunBeban',
            'totalBelumPosting',
            'totalSudahPosting',
            'countBelumPosting',
            'countSudahPosting'
        ));
    }

    public function postingBebanGl(Request $request, $id)
    {
        $request->validate([
            'kode_perkiraan' => 'required|string',
        ]);

        $kasKeluar = KasKeluar::findOrFail($id);
        if ($kasKeluar->status_void) {
            return redirect()->back()->with('error', 'Bukti kas keluar yang sudah di-void tidak dapat diposting ke GL.');
        }
        if ($kasKeluar->is_pulled_accounting) {
            return redirect()->back()->with('error', 'Bukti kas keluar ini sudah diposting ke GL.');
        }

        if (!$kasKeluar->is_closed) {
            $shift = $kasKeluar->shift_id ? ShiftKasir::find($kasKeluar->shift_id) : null;
            if ($shift && in_array($shift->status, ['open', 'aktif'])) {
                return redirect()->back()->with('error', "Bukti kas keluar ini tercatat pada Shift Kasir yang masih AKTIF (belum ditutup). Sesuai standar operasional, penarikan & posting beban ke GL hanya dapat dilakukan setelah kasir melakukan Tutup Kasir (Closing Shift).");
            }
        }

        $nominal = (float) ($kasKeluar->nominal_keluar ?: $kasKeluar->jumlah_keluar ?: 0);
        $tglPosting = $kasKeluar->tanggal_keluar ?: Carbon::today()->toDateString();
        $periodeKey = Carbon::parse($tglPosting)->format('Y-m');

        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Posting beban operasional ditolak.");
        }

        $akunBeban = AkunPerkiraan::where('kode_perkiraan', $request->kode_perkiraan)->first();
        $namaAkunBeban = $akunBeban ? $akunBeban->nama_perkiraan : 'Beban Operasional';

        // Tentukan akun sumber dana kasir/bank secara dinamis berdasarkan metode bayar
        $metode = strtolower((string) ($kasKeluar->metode_bayar ?? 'tunai'));
        $ketLower = strtolower((string) ($kasKeluar->keterangan ?? ''));
        if ($metode === 'transfer' && str_contains($ketLower, 'bjb')) {
            $akunSumber = $this->accountingService->getMappedAccount('bank_bjb');
        } elseif ($metode === 'transfer') {
            $akunSumber = $this->accountingService->getMappedAccount('bank_bca');
        } else {
            $akunSumber = $this->accountingService->getMappedAccount('kas_operasional');
        }

        DB::transaction(function () use ($kasKeluar, $nominal, $tglPosting, $request, $namaAkunBeban, $akunSumber) {
            $noJurnal = JurnalUmum::generateNomorJurnal($tglPosting);

            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $tglPosting,
                'tipe'         => 'auto',
                'sumber'       => 'kasir',
                'referensi'    => $kasKeluar->no_bkk ?: "BKK-{$kasKeluar->id}",
                'keterangan'   => "Beban: {$kasKeluar->keterangan} ({$kasKeluar->diberikan_kepada})",
                'total_debet'  => $nominal,
                'total_kredit' => $nominal,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
                'approved_by'  => auth()->id() ?? 1,
                'approved_at'  => now(),
            ]);

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $request->kode_perkiraan,
                'nama_perkiraan' => $namaAkunBeban,
                'debet'          => $nominal,
                'kredit'         => 0,
                'urutan'         => 1,
            ]);

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $akunSumber['kode'],
                'nama_perkiraan' => $akunSumber['nama'],
                'debet'          => 0,
                'kredit'         => $nominal,
                'urutan'         => 2,
            ]);

            $kasKeluar->update([
                'is_pulled_accounting'  => true,
                'status_tarik_akunting' => true,
                'kode_perkiraan'        => $request->kode_perkiraan,
                'nama_perkiraan'        => $namaAkunBeban,
            ]);

            AuditLogAkuntansi::catat(
                'POST_BEBAN',
                'GL',
                "Posting beban {$kasKeluar->no_bkk} sebesar Rp " . number_format($nominal, 0, ',', '.') . " ke akun {$request->kode_perkiraan} (Jurnal: {$noJurnal})"
            );
        });

        return redirect()->back()->with('success', 'Beban pengeluaran kasir berhasil diposting ke GL.');
    }

    public function bebanMutasiBank(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $q = trim((string) $request->input('q', ''));
        $rekening = $request->input('rekening', 'all');
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        $queryMasuk = $this->accountingService->kasMasukAktif();
        if ($rekening === 'all') {
            $queryMasuk->where(function ($sub) {
                $sub->where('metode_bayar', 'transfer')
                    ->orWhereNotNull('bank_tujuan');
            });
        } elseif ($rekening === 'bca') {
            $queryMasuk->where('bank_tujuan', 'like', '%BCA%');
        } elseif ($rekening === 'bjb') {
            $queryMasuk->where('bank_tujuan', 'like', '%BJB%');
        } elseif ($rekening === 'kasir') {
            $queryMasuk->where('metode_bayar', 'tunai');
        }

        if ($dari && $sampai) {
            $queryMasuk->whereBetween('tgl_transaksi', [$dari, $sampai]);
        } else {
            $queryMasuk->where(function ($sub) use ($bulanPilihan) {
                $sub->where('tgl_transaksi', 'like', "{$bulanPilihan}%")
                    ->orWhere('tanggal_terima', 'like', "{$bulanPilihan}%");
            });
        }

        if ($q !== '') {
            $queryMasuk->where(function ($sub) use ($q) {
                $sub->where('no_kuitansi', 'like', "%{$q}%")
                    ->orWhere('no_bkm', 'like', "%{$q}%")
                    ->orWhere('no_faktur_ref', 'like', "%{$q}%")
                    ->orWhere('terima_dari', 'like', "%{$q}%")
                    ->orWhere('nama_pembayar', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%");
            });
        }

        $mutasiMasuk = $queryMasuk->latest('id')->get();

        $queryKeluar = KasKeluar::where(fn ($sub) => $sub->where('status_void', false)->orWhereNull('status_void'));
        if ($rekening === 'all') {
            $queryKeluar->where(function ($sub) {
                $sub->where('metode_bayar', 'transfer')
                    ->orWhere('kategori_beban', 'setor_bank');
            });
        } elseif ($rekening === 'bca') {
            $queryKeluar->where(function ($sub) {
                $sub->where('keterangan', 'like', '%BCA%')
                    ->orWhere('nama_perkiraan', 'like', '%BCA%');
            });
        } elseif ($rekening === 'bjb') {
            $queryKeluar->where(function ($sub) {
                $sub->where('keterangan', 'like', '%BJB%')
                    ->orWhere('nama_perkiraan', 'like', '%BJB%');
            });
        } elseif ($rekening === 'kasir') {
            $queryKeluar->where('metode_bayar', 'tunai');
        }

        if ($dari && $sampai) {
            $queryKeluar->whereBetween('tanggal_keluar', [$dari, $sampai]);
        } else {
            $queryKeluar->where('tanggal_keluar', 'like', "{$bulanPilihan}%");
        }

        if ($q !== '') {
            $queryKeluar->where(function ($sub) use ($q) {
                $sub->where('no_bkk', 'like', "%{$q}%")
                    ->orWhere('diberikan_kepada', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%");
            });
        }

        $mutasiKeluar = $queryKeluar->latest('tanggal_keluar')->get();

        $totalMasuk = (float) $mutasiMasuk->sum('jumlah_dibayar');
        $totalKeluar = (float) $mutasiKeluar->sum('nominal_keluar');
        $saldoMutasi = $totalMasuk - $totalKeluar;

        // Data Rekonsiliasi Bank (Rekening Koran)
        $rekonList = Schema::hasTable('rekonsiliasi_banks')
            ? RekonsiliasiBank::with('jurnal')
                ->where('periode', $bulanPilihan)
                ->orderByDesc('tanggal_transaksi')
                ->orderByDesc('id')
                ->get()
            : collect();

        $rekonStats = [
            'total'     => $rekonList->count(),
            'matched'   => $rekonList->where('status_match', 'matched')->count(),
            'adjusted'  => $rekonList->where('status_match', 'adjusted')->count(),
            'unmatched' => $rekonList->where('status_match', 'unmatched')->count(),
        ];

        $kodeBca = $this->accountingService->getMappedAccount('bank_bca')['kode'];
        $kodeBjb = $this->accountingService->getMappedAccount('bank_bjb')['kode'];
        $saldoBukuBca = (float) JurnalUmumDetail::whereHas('jurnal', fn ($qr) => $qr->where('status', 'posted'))
            ->where('kode_perkiraan', $kodeBca)
            ->selectRaw('COALESCE(SUM(debet - kredit), 0) as net')->value('net');
        $saldoBukuBjb = (float) JurnalUmumDetail::whereHas('jurnal', fn ($qr) => $qr->where('status', 'posted'))
            ->where('kode_perkiraan', $kodeBjb)
            ->selectRaw('COALESCE(SUM(debet - kredit), 0) as net')->value('net');

        return view('pages.accounting.beban.mutasi_bank', compact(
            'bulanPilihan',
            'mutasiMasuk',
            'mutasiKeluar',
            'totalMasuk',
            'totalKeluar',
            'saldoMutasi',
            'q',
            'rekening',
            'dari',
            'sampai',
            'rekonList',
            'rekonStats',
            'saldoBukuBca',
            'saldoBukuBjb'
        ));
    }

    public function storeRekonBank(Request $request)
    {
        $request->validate([
            'periode'           => 'required|date_format:Y-m',
            'rekening_bank'     => 'required|in:BCA,BJB',
            'tanggal_transaksi' => 'required|date',
            'uraian_bank'       => 'required|string|max:255',
            'tipe_mutasi'       => 'required|in:kredit_masuk,debet_keluar',
            'nominal'           => 'required|numeric|min:1',
            'referensi_bank'    => 'nullable|string|max:100',
        ]);

        $mapKey = $request->rekening_bank === 'BJB' ? 'bank_bjb' : 'bank_bca';
        $akunBank = $this->accountingService->getMappedAccount($mapKey);

        RekonsiliasiBank::create([
            'periode'             => $request->periode,
            'rekening_bank'       => $request->rekening_bank,
            'kode_perkiraan_bank' => $akunBank['kode'],
            'tanggal_transaksi'   => $request->tanggal_transaksi,
            'uraian_bank'         => $request->uraian_bank,
            'referensi_bank'      => $request->referensi_bank,
            'tipe_mutasi'         => $request->tipe_mutasi,
            'nominal'             => (float) $request->nominal,
            'status_match'        => 'unmatched',
            'user_id'             => auth()->id() ?? 1,
        ]);

        AuditLogAkuntansi::catat('REKON_TAMBAH', 'BANK', "Input mutasi rekening koran {$request->rekening_bank}: {$request->uraian_bank} (Rp " . number_format((float) $request->nominal, 0, ',', '.') . ")");

        return redirect()->back()->with('success', 'Baris mutasi rekening koran berhasil ditambahkan ke daftar rekonsiliasi.');
    }

    public function autoMatchRekonBank(Request $request)
    {
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));
        $unmatched = RekonsiliasiBank::where('periode', $periode)
            ->where('status_match', 'unmatched')
            ->get();

        $matchedCount = 0;

        foreach ($unmatched as $rek) {
            $start = Carbon::parse($rek->tanggal_transaksi)->subDays(3)->toDateString();
            $end = Carbon::parse($rek->tanggal_transaksi)->addDays(3)->toDateString();
            $nominal = (float) $rek->nominal;

            if ($rek->tipe_mutasi === 'kredit_masuk') {
                $km = $this->accountingService->kasMasukAktif()
                    ->where('bank_tujuan', 'like', '%' . $rek->rekening_bank . '%')
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('tgl_transaksi', [$start, $end])
                          ->orWhereBetween('tanggal_terima', [$start, $end]);
                    })
                    ->where(function ($q) use ($nominal) {
                        $q->where('jumlah_dibayar', $nominal)->orWhere('jumlah_diterima', $nominal);
                    })
                    ->first();

                if ($km) {
                    $rek->update([
                        'status_match'  => 'matched',
                        'matched_model' => 'KasMasuk',
                        'matched_id'    => $km->id,
                    ]);
                    $matchedCount++;
                }
            } else {
                $kk = KasKeluar::where(fn ($q) => $q->where('status_void', false)->orWhereNull('status_void'))
                    ->whereBetween('tanggal_keluar', [$start, $end])
                    ->where(function ($q) use ($nominal) {
                        $q->where('nominal_keluar', $nominal)->orWhere('jumlah_keluar', $nominal);
                    })
                    ->first();

                if ($kk) {
                    $rek->update([
                        'status_match'  => 'matched',
                        'matched_model' => 'KasKeluar',
                        'matched_id'    => $kk->id,
                    ]);
                    $matchedCount++;
                }
            }
        }

        return redirect()->back()->with('success', "Auto-matching rekonsiliasi selesai: {$matchedCount} transaksi rekening koran cocok dengan catatan sistem.");
    }

    public function adjustRekonBank(Request $request, int $id)
    {
        $rek = RekonsiliasiBank::findOrFail($id);
        if ($rek->status_match !== 'unmatched') {
            return redirect()->back()->with('error', 'Baris rekening koran ini sudah berstatus Matched / Adjusted.');
        }

        $tgl = $rek->tanggal_transaksi->toDateString();
        $periodeKey = Carbon::parse($tgl)->format('Y-m');
        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah terkunci.");
        }

        $nominal = (float) $rek->nominal;
        $mapKeyBank = $rek->rekening_bank === 'BJB' ? 'bank_bjb' : 'bank_bca';
        $akunBank = $this->accountingService->getMappedAccount($mapKeyBank);

        DB::transaction(function () use ($rek, $tgl, $nominal, $akunBank) {
            $noJurnal = JurnalUmum::generateNomorJurnal($tgl);
            $isCreditBank = $rek->tipe_mutasi === 'kredit_masuk'; // Bunga / Jasa Giro masuk bank

            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $tgl,
                'tipe'         => 'auto',
                'sumber'       => 'akrual',
                'referensi'    => "REKON-{$rek->rekening_bank}-{$rek->id}",
                'keterangan'   => "Penyesuaian Rekonsiliasi {$rek->rekening_bank}: {$rek->uraian_bank}",
                'total_debet'  => $nominal,
                'total_kredit' => $nominal,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
                'approved_by'  => auth()->id() ?? 1,
                'approved_at'  => now(),
            ]);

            if ($isCreditBank) {
                $akunBunga = $this->accountingService->getMappedAccount('pendapatan_bunga_bank');
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => $akunBank['kode'],
                    'nama_perkiraan' => $akunBank['nama'],
                    'debet'          => $nominal,
                    'kredit'         => 0,
                    'urutan'         => 1,
                ]);
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => $akunBunga['kode'],
                    'nama_perkiraan' => $akunBunga['nama'],
                    'debet'          => 0,
                    'kredit'         => $nominal,
                    'urutan'         => 2,
                ]);
            } else {
                $akunAdmin = $this->accountingService->getMappedAccount('beban_admin_bank');
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => $akunAdmin['kode'],
                    'nama_perkiraan' => $akunAdmin['nama'],
                    'debet'          => $nominal,
                    'kredit'         => 0,
                    'urutan'         => 1,
                ]);
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => $akunBank['kode'],
                    'nama_perkiraan' => $akunBank['nama'],
                    'debet'          => 0,
                    'kredit'         => $nominal,
                    'urutan'         => 2,
                ]);
            }

            $rek->update([
                'status_match'   => 'adjusted',
                'jurnal_umum_id' => $jurnal->id,
            ]);

            AuditLogAkuntansi::catat('REKON_ADJUST', 'BANK', "Jurnal penyesuaian rekonsiliasi bank {$noJurnal}: {$rek->uraian_bank} (Rp " . number_format($nominal, 0, ',', '.') . ")");
        });

        return redirect()->back()->with('success', 'Selisih rekening koran berhasil dijurnal ke GL (Jasa Giro / Biaya Admin Bank).');
    }

    public function destroyRekonBank(int $id)
    {
        $rek = RekonsiliasiBank::findOrFail($id);
        if ($rek->status_match === 'adjusted') {
            return redirect()->back()->with('error', 'Baris yang sudah dijurnal tidak boleh dihapus; gunakan Jurnal Pembalik.');
        }
        $rek->delete();
        return redirect()->back()->with('success', 'Baris mutasi rekening koran berhasil dihapus.');
    }

    public function bebanAkrual(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $q = trim((string) $request->input('q', ''));
        $daftarAkun = AkunPerkiraan::aktif()->orderBy('kode_perkiraan')->get();

        $query = JurnalUmum::with('details')
            ->whereIn('sumber', ['akrual', 'manual'])
            ->where('tanggal', 'like', "{$bulanPilihan}%");

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('no_jurnal', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%")
                    ->orWhere('referensi', 'like', "%{$q}%");
            });
        }

        $jurnalAkrual = $query->latest('tanggal')->get();

        $stats = [
            'total_jurnal'  => $jurnalAkrual->count(),
            'total_nominal' => (float) $jurnalAkrual->sum('total_debet'),
        ];

        return view('pages.accounting.beban.akrual', compact('bulanPilihan', 'daftarAkun', 'jurnalAkrual', 'q', 'stats'));
    }

    public function bebanAsetTetap(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $daftarAset = $this->accountingService->getDaftarAsetTetap();
        $endDate = Carbon::parse($bulanPilihan . '-01')->endOfMonth()->toDateString();

        $q = trim((string) $request->input('q', ''));
        $kategori = $request->input('kategori', 'all');

        $filteredAset = $daftarAset;
        if ($q !== '') {
            $filteredAset = array_filter($filteredAset, function ($item) use ($q) {
                return str_contains(strtolower($item['kode'] ?? ''), strtolower($q))
                    || str_contains(strtolower($item['nama'] ?? ''), strtolower($q));
            });
        }
        if ($kategori !== 'all') {
            $filteredAset = array_filter($filteredAset, fn ($item) => ($item['kategori'] ?? '') === $kategori);
        }
        $daftarAsetFiltered = array_values($filteredAset);

        $totalHargaBeli = array_sum(array_column($daftarAset, 'harga_beli'));
        $totalSusutBulanan = array_sum(array_column($daftarAset, 'susut_bulanan'));
        $akumulasiSusut = $this->accountingService->hitungAkumulasiSusutHingga($daftarAset, $endDate);
        $totalNilaiBuku = max(0, $totalHargaBeli - $akumulasiSusut);

        $sudahSusutBulanIni = JurnalUmum::where('referensi', "SUSUT-{$bulanPilihan}")
            ->whereNull('reversed_by_jurnal_id')
            ->exists();
        $daftarKategori = array_unique(array_column($daftarAset, 'kategori'));

        return view('pages.accounting.beban.aset_tetap', [
            'bulanPilihan'       => $bulanPilihan,
            'daftarAset'         => $daftarAsetFiltered,
            'totalHargaBeli'     => $totalHargaBeli,
            'totalSusutBulanan'  => $totalSusutBulanan,
            'akumulasiSusut'     => $akumulasiSusut,
            'totalNilaiBuku'     => $totalNilaiBuku,
            'sudahSusutBulanIni' => $sudahSusutBulanIni,
            'daftarKategori'     => $daftarKategori,
            'q'                  => $q,
            'kategoriPilihan'    => $kategori,
        ]);
    }

    public function storeAsetTetap(Request $request)
    {
        $request->validate([
            'nama'               => 'required|string|max:120',
            'kategori'           => 'required|string|max:60',
            'tgl_perolehan'      => 'required|date',
            'harga_beli'         => 'required|numeric|min:1000',
            'tarif_susut'        => 'required|numeric|min:1|max:100',
            'posting_perolehan'  => 'nullable|boolean',
            'sumber_pembayaran'  => 'nullable|in:bank_bca,bank_bjb,kas_operasional,utang_usaha_ap',
        ]);

        $assets = $this->accountingService->getDaftarAsetTetap(true);
        $nextNum = count($assets) + 1;
        $existingCodes = array_column($assets, 'kode');
        do {
            $kode = 'AST-' . str_pad((string) $nextNum, 3, '0', STR_PAD_LEFT);
            $nextNum++;
        } while (in_array($kode, $existingCodes, true));

        $harga = (float) $request->harga_beli;
        $tarif = (float) $request->tarif_susut;
        $susutTahunan = round($harga * ($tarif / 100));
        $susutBulanan = round($susutTahunan / 12);
        $akunAset = $this->accountingService->getMappedAccount('aset_tetap_default');

        DB::transaction(function () use ($request, $kode, $harga, $tarif, $susutTahunan, $susutBulanan, $akunAset, &$assets) {
            $jurnalId = null;

            if ($request->boolean('posting_perolehan')) {
                $tgl = $request->tgl_perolehan;
                $periodeKey = Carbon::parse($tgl)->format('Y-m');
                if (!PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
                    $sumberKey = $request->input('sumber_pembayaran', 'bank_bca');
                    $akunKredit = $this->accountingService->getMappedAccount($sumberKey);
                    $noJurnal = JurnalUmum::generateNomorJurnal($tgl);

                    $jurnal = JurnalUmum::create([
                        'no_jurnal'    => $noJurnal,
                        'tanggal'      => $tgl,
                        'tipe'         => 'auto',
                        'sumber'       => 'manual',
                        'referensi'    => $kode,
                        'keterangan'   => "Perolehan Aset Tetap {$kode}: {$request->nama}",
                        'total_debet'  => $harga,
                        'total_kredit' => $harga,
                        'is_balanced'  => true,
                        'status'       => 'posted',
                        'user_id'      => auth()->id() ?? 1,
                        'approved_by'  => auth()->id() ?? 1,
                        'approved_at'  => now(),
                    ]);

                    JurnalUmumDetail::create([
                        'jurnal_umum_id' => $jurnal->id,
                        'kode_perkiraan' => $akunAset['kode'],
                        'nama_perkiraan' => $akunAset['nama'],
                        'debet'          => $harga,
                        'kredit'         => 0,
                        'urutan'         => 1,
                    ]);
                    JurnalUmumDetail::create([
                        'jurnal_umum_id' => $jurnal->id,
                        'kode_perkiraan' => $akunKredit['kode'],
                        'nama_perkiraan' => $akunKredit['nama'],
                        'debet'          => 0,
                        'kredit'         => $harga,
                        'urutan'         => 2,
                    ]);
                    $jurnalId = $jurnal->id;
                }
            }

            if (Schema::hasTable('aset_tetaps')) {
                $this->accountingService->ensureAsetTetapSeeded();
                AsetTetap::create([
                    'kode'                => $kode,
                    'nama'                => trim($request->nama),
                    'kategori'            => trim($request->kategori),
                    'tgl_perolehan'       => $request->tgl_perolehan,
                    'harga_beli'          => $harga,
                    'tarif_susut'         => $tarif,
                    'susut_tahunan'       => $susutTahunan,
                    'susut_bulanan'       => $susutBulanan,
                    'kode_perkiraan_aset' => $akunAset['kode'],
                    'status'              => 'aktif',
                    'jurnal_perolehan_id' => $jurnalId,
                ]);
            } else {
                $assets[] = [
                    'kode'          => $kode,
                    'nama'          => trim($request->nama),
                    'kategori'      => trim($request->kategori),
                    'tgl_perolehan' => $request->tgl_perolehan,
                    'harga_beli'    => $harga,
                    'tarif_susut'   => $tarif,
                    'susut_tahunan' => $susutTahunan,
                    'susut_bulanan' => $susutBulanan,
                ];
                $this->accountingService->saveDaftarAsetTetap($assets);
            }

            AuditLogAkuntansi::catat('TAMBAH_ASET', 'ASET_TETAP', "Registrasi aset {$kode} ({$request->nama}) senilai Rp " . number_format($harga, 0, ',', '.'));
        });

        return redirect()->back()->with('success', "Aset Tetap {$kode} berhasil ditambahkan ke register.");
    }

    public function destroyAsetTetap(Request $request, string $kode)
    {
        if (Schema::hasTable('aset_tetaps')) {
            $aset = AsetTetap::where('kode', $kode)->first();
            if (!$aset) {
                return redirect()->back()->with('error', "Aset dengan kode {$kode} tidak ditemukan.");
            }
            $aset->delete();
        } else {
            $assets = $this->accountingService->getDaftarAsetTetap();
            $filtered = array_values(array_filter($assets, fn ($a) => ($a['kode'] ?? '') !== $kode));
            if (count($filtered) === count($assets)) {
                return redirect()->back()->with('error', "Aset dengan kode {$kode} tidak ditemukan.");
            }
            $this->accountingService->saveDaftarAsetTetap($filtered);
        }

        AuditLogAkuntansi::catat('HAPUS_ASET', 'ASET_TETAP', "Menghapus aset {$kode} dari daftar aset tetap.");

        return redirect()->back()->with('success', "Aset {$kode} berhasil dihapus dari register.");
    }

    public function postingPenyusutanAset(Request $request)
    {
        $request->validate(['bulan' => 'required|date_format:Y-m']);
        $bulan = $request->input('bulan');

        if (PeriodeAkuntansi::isPeriodeLocked($bulan)) {
            return redirect()->back()->with('error', "Periode {$bulan} sudah ditutup (terkunci). Posting penyusutan ditolak.");
        }

        $ref = "SUSUT-{$bulan}";
        if (JurnalUmum::where('referensi', $ref)->whereNull('reversed_by_jurnal_id')->exists()) {
            return redirect()->back()->with('error', "Penyusutan periode {$bulan} sudah diposting sebelumnya.");
        }

        $assets = $this->accountingService->getDaftarAsetTetap();
        $nominal = (float) array_sum(array_column($assets, 'susut_bulanan'));
        if ($nominal <= 0) {
            return redirect()->back()->with('error', 'Nominal penyusutan bernilai Rp 0.');
        }

        $tglPosting = Carbon::parse($bulan . '-01')->endOfMonth()->toDateString();
        $akunBebanSusut = $this->accountingService->getMappedAccount('beban_penyusutan');
        $akunAkumSusut = $this->accountingService->getMappedAccount('akumulasi_penyusutan');

        $noJurnal = DB::transaction(function () use ($tglPosting, $ref, $bulan, $nominal, $akunBebanSusut, $akunAkumSusut) {
            $noJurnal = JurnalUmum::generateNomorJurnal($tglPosting);
            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $tglPosting,
                'tipe'         => 'auto',
                'sumber'       => 'penyusutan',
                'referensi'    => $ref,
                'keterangan'   => "Beban Penyusutan Aset Tetap Periode {$bulan}",
                'total_debet'  => $nominal,
                'total_kredit' => $nominal,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
                'approved_by'  => auth()->id() ?? 1,
                'approved_at'  => now(),
            ]);

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $akunBebanSusut['kode'],
                'nama_perkiraan' => $akunBebanSusut['nama'],
                'debet'          => $nominal,
                'kredit'         => 0,
                'urutan'         => 1,
            ]);

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $akunAkumSusut['kode'],
                'nama_perkiraan' => $akunAkumSusut['nama'],
                'debet'          => 0,
                'kredit'         => $nominal,
                'urutan'         => 2,
            ]);

            AuditLogAkuntansi::catat(
                'POST_PENYUSUTAN',
                'GL',
                "Posting penyusutan bulanan {$bulan} sebesar Rp " . number_format($nominal, 0, ',', '.') . " (Jurnal: {$noJurnal})"
            );

            return $noJurnal;
        });

        return redirect()->back()->with('success', "Penyusutan Aset Tetap periode {$bulan} berhasil diposting ke GL ({$noJurnal}).");
    }

    public function bebanAnggaran(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $q = trim((string) $request->input('q', ''));
        $divisi = $request->input('divisi', 'all');
        $status = $request->input('status', 'all');

        $query = PengajuanAnggaran::where('tanggal_pengajuan', 'like', "{$bulanPilihan}%");

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nomor_pengajuan', 'like', "%{$q}%")
                    ->orWhere('pemohon', 'like', "%{$q}%")
                    ->orWhere('keperluan', 'like', "%{$q}%");
            });
        }

        if ($divisi !== 'all') {
            $query->where('divisi', $divisi);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $daftarAnggaran = $query->latest('id')->get();

        $totalPagu = (float) (PengajuanAnggaran::where('tanggal_pengajuan', 'like', "{$bulanPilihan}%")->sum('nominal_disetujui')
            ?: PengajuanAnggaran::where('tanggal_pengajuan', 'like', "{$bulanPilihan}%")->sum('nominal_diajukan'));
        $totalRealisasi = (float) $this->accountingService->kasKeluarBebanAktif()
            ->where('tanggal_keluar', 'like', "{$bulanPilihan}%")
            ->sum('nominal_keluar');
        $sisaAnggaran = max(0, $totalPagu - $totalRealisasi);

        return view('pages.accounting.beban.anggaran', compact(
            'bulanPilihan',
            'daftarAnggaran',
            'totalPagu',
            'totalRealisasi',
            'sisaAnggaran',
            'q',
            'divisi',
            'status'
        ));
    }
}
