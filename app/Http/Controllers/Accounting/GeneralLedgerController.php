<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\AkunPerkiraan;
use App\Models\AuditLogAkuntansi;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumDetail;
use App\Models\PeriodeAkuntansi;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralLedgerController extends Controller
{
    public function __construct(protected AccountingService $accountingService)
    {
    }

    public function glJurnal(Request $request)
    {
        $query = JurnalUmum::with(['details', 'approvedBy', 'reversedBy', 'reversalOf']);

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('no_jurnal', 'like', "%{$q}%")
                    ->orWhere('referensi', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%");
            });
        }
        if ($request->filled('sumber') && $request->sumber !== 'all') {
            $query->where('sumber', $request->sumber);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $stats = [
            'total_jurnal'  => (clone $query)->count(),
            'total_nominal' => (float) (clone $query)->sum('total_debet'),
            'posted_count'  => (clone $query)->where('status', 'posted')->count(),
            'draft_count'   => (clone $query)->where('status', 'draft')->count(),
        ];

        $jurnalList = $query->latest('tanggal')->latest('id')->paginate(20)->withQueryString();
        return view('pages.accounting.gl.jurnal', compact('jurnalList', 'stats'));
    }

    public function cetakGlJurnal(Request $request)
    {
        $query = JurnalUmum::with('details');
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('no_jurnal', 'like', "%{$q}%")
                    ->orWhere('referensi', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%");
            });
        }
        if ($request->filled('sumber') && $request->sumber !== 'all') {
            $query->where('sumber', $request->sumber);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }
        $jurnalList = $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc')->get();
        return view('pages.accounting.gl.cetak_jurnal', compact('jurnalList'));
    }

    public function glMemorial(Request $request)
    {
        $daftarAkun = AkunPerkiraan::aktif()->orderBy('kode_perkiraan')->get();
        return view('pages.accounting.gl.memorial', compact('daftarAkun'));
    }

    public function storeGlMemorial(Request $request)
    {
        $request->validate([
            'tanggal'     => 'required|date',
            'keterangan'  => 'required|string',
            'akun_debet'  => 'required|string',
            'akun_kredit' => 'required|string',
            'nominal'     => 'required|numeric|min:1',
            'action_mode' => 'nullable|in:posted,draft',
            'sumber'      => 'nullable|in:manual,akrual',
        ]);

        if ($request->akun_debet === $request->akun_kredit) {
            return redirect()->back()->withInput()->with('error', 'Akun Debet dan Akun Kredit tidak boleh sama.');
        }

        $periodeKey = Carbon::parse($request->tanggal)->format('Y-m');
        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Pembuatan jurnal ditolak.");
        }

        $status = $request->input('action_mode', 'posted');
        $sumber = $request->input('sumber', 'manual');
        $nominal = (float) $request->nominal;
        $akunDebet = AkunPerkiraan::where('kode_perkiraan', $request->akun_debet)->first();
        $akunKredit = AkunPerkiraan::where('kode_perkiraan', $request->akun_kredit)->first();

        $noJurnal = DB::transaction(function () use ($request, $nominal, $akunDebet, $akunKredit, $status, $sumber) {
            $noJurnal = JurnalUmum::generateNomorJurnal($request->tanggal);
            $isPosted = $status === 'posted';

            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $request->tanggal,
                'tipe'         => 'memorial',
                'sumber'       => $sumber,
                'referensi'    => $request->referensi ?: ($sumber === 'akrual' ? 'AKRUAL' : 'MEMORIAL'),
                'keterangan'   => $request->keterangan,
                'total_debet'  => $nominal,
                'total_kredit' => $nominal,
                'is_balanced'  => true,
                'status'       => $status,
                'user_id'      => auth()->id() ?? 1,
                'approved_by'  => $isPosted ? (auth()->id() ?? 1) : null,
                'approved_at'  => $isPosted ? now() : null,
            ]);

            JurnalUmumDetail::create([
                'jurnal_umum_id'    => $jurnal->id,
                'akun_perkiraan_id' => $akunDebet?->id,
                'kode_perkiraan'    => $request->akun_debet,
                'nama_perkiraan'    => $akunDebet ? $akunDebet->nama_perkiraan : 'Akun Debet',
                'debet'             => $nominal,
                'kredit'            => 0,
                'urutan'            => 1,
            ]);

            JurnalUmumDetail::create([
                'jurnal_umum_id'    => $jurnal->id,
                'akun_perkiraan_id' => $akunKredit?->id,
                'kode_perkiraan'    => $request->akun_kredit,
                'nama_perkiraan'    => $akunKredit ? $akunKredit->nama_perkiraan : 'Akun Kredit',
                'debet'             => 0,
                'kredit'            => $nominal,
                'urutan'            => 2,
            ]);

            AuditLogAkuntansi::catat(
                $isPosted ? 'POST_MEMORIAL' : 'DRAFT_MEMORIAL',
                'GL',
                ($isPosted ? 'Posting ' : 'Simpan Draft ') . "Jurnal {$noJurnal} sebesar Rp " . number_format($nominal, 0, ',', '.') . " ({$request->keterangan})"
            );

            return $noJurnal;
        });

        $msg = $status === 'draft'
            ? "Draft Jurnal {$noJurnal} berhasil disimpan (menunggu persetujuan posting)."
            : "Jurnal Memorial {$noJurnal} berhasil diposting ke GL.";

        return redirect()->route('accounting.gl.jurnal')->with('success', $msg);
    }

    public function approveJurnal(Request $request, int $id)
    {
        $jurnal = JurnalUmum::with('details')->findOrFail($id);
        $periodeKey = Carbon::parse($jurnal->tanggal)->format('Y-m');

        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah terkunci. Persetujuan ditolak.");
        }

        if (!$jurnal->is_balanced || abs((float) $jurnal->total_debet - (float) $jurnal->total_kredit) > 0.01) {
            return redirect()->back()->with('error', "Jurnal {$jurnal->no_jurnal} tidak seimbang (Debet != Kredit).");
        }

        $notes = trim((string) $request->input('approval_notes', 'Diverifikasi & disetujui ke General Ledger'));

        $jurnal->update([
            'status'         => 'posted',
            'approved_by'    => auth()->id() ?? 1,
            'approved_at'    => now(),
            'approval_notes' => $notes,
        ]);

        AuditLogAkuntansi::catat('APPROVE_JURNAL', 'GL', "Verifikasi/Posting jurnal {$jurnal->no_jurnal}: {$notes}");

        return redirect()->back()->with('success', "Jurnal {$jurnal->no_jurnal} resmi diverifikasi & berstatus POSTED.");
    }

    public function reverseJurnal(Request $request, int $id)
    {
        $request->validate([
            'alasan' => 'required|string|min:3|max:255',
        ]);

        $jurnal = JurnalUmum::with('details')->findOrFail($id);
        if ($jurnal->status !== 'posted') {
            return redirect()->back()->with('error', "Hanya jurnal berstatus POSTED yang dapat dibuatkan Jurnal Pembalik.");
        }
        if ($jurnal->reversed_by_jurnal_id) {
            return redirect()->back()->with('error', "Jurnal {$jurnal->no_jurnal} sudah pernah dibalik sebelumnya.");
        }

        $tglRev = Carbon::today()->toDateString();
        $periodeRev = Carbon::parse($tglRev)->format('Y-m');
        if (PeriodeAkuntansi::isPeriodeLocked($periodeRev)) {
            return redirect()->back()->with('error', "Periode {$periodeRev} sudah ditutup (terkunci).");
        }

        $rev = $this->accountingService->reverseJurnal($jurnal, $request->alasan, $tglRev);

        AuditLogAkuntansi::catat(
            'REVERSE_JURNAL',
            'GL',
            "Penerbitan Jurnal Pembalik {$rev->no_jurnal} atas {$jurnal->no_jurnal}. Alasan: {$request->alasan}"
        );

        return redirect()->back()->with('success', "Jurnal Pembalik {$rev->no_jurnal} berhasil diterbitkan (jejak audit jurnal lama tetap terjaga).");
    }

    public function realisasiUangMuka(Request $request)
    {
        $request->validate([
            'no_faktur' => 'required|string',
            'tanggal'   => 'required|date',
            'nominal'   => 'required|numeric|min:1',
        ]);

        $periodeKey = Carbon::parse($request->tanggal)->format('Y-m');
        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Realisasi uang muka ditolak.");
        }

        $nominal = (float) $request->nominal;
        $akunUm = $this->accountingService->getMappedAccount('uang_muka_iklan');
        $akunPendapatan = $this->accountingService->getMappedAccount('pendapatan_koran');

        $noJurnal = DB::transaction(function () use ($request, $nominal, $akunUm, $akunPendapatan) {
            $noJurnal = JurnalUmum::generateNomorJurnal($request->tanggal);
            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $request->tanggal,
                'tipe'         => 'auto',
                'sumber'       => 'iklan',
                'referensi'    => "RLS-UM/{$request->no_faktur}",
                'keterangan'   => "Realisasi Pendapatan Uang Muka Iklan Faktur {$request->no_faktur}",
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
                'kode_perkiraan' => $akunUm['kode'],
                'nama_perkiraan' => $akunUm['nama'],
                'debet'          => $nominal,
                'kredit'         => 0,
                'urutan'         => 1,
            ]);

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $akunPendapatan['kode'],
                'nama_perkiraan' => $akunPendapatan['nama'],
                'debet'          => 0,
                'kredit'         => $nominal,
                'urutan'         => 2,
            ]);

            AuditLogAkuntansi::catat(
                'REALISASI_UM',
                'IKLAN',
                "Realisasi uang muka faktur {$request->no_faktur} sebesar Rp " . number_format($nominal, 0, ',', '.') . " (Jurnal: {$noJurnal})"
            );

            return $noJurnal;
        });

        return redirect()->back()->with('success', "Uang muka iklan {$request->no_faktur} berhasil direalisasikan ke Pendapatan ({$noJurnal}).");
    }

    public function glBukuBesar(Request $request)
    {
        $daftarAkun = AkunPerkiraan::aktif()->orderBy('kode_perkiraan')->get();
        $akunPilihan = $request->input('kode_perkiraan', $daftarAkun->first()?->kode_perkiraan);
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        $akun = AkunPerkiraan::where('kode_perkiraan', $akunPilihan)->first();

        // Hitung Saldo Awal Akumulatif (Saldo Awal Master COA + mutasi posted sebelum $dari)
        $saldoAwalMaster = (float) ($akun?->saldo_awal ?? 0);
        $mutasiSebelumDebet = 0.0;
        $mutasiSebelumKredit = 0.0;

        if ($dari && $akunPilihan) {
            $aggSebelum = JurnalUmumDetail::where('kode_perkiraan', $akunPilihan)
                ->whereHas('jurnal', function ($q) use ($dari) {
                    $q->where('status', 'posted')->whereDate('tanggal', '<', $dari);
                })
                ->selectRaw('COALESCE(SUM(debet), 0) as d, COALESCE(SUM(kredit), 0) as k')
                ->first();

            $mutasiSebelumDebet = (float) ($aggSebelum->d ?? 0);
            $mutasiSebelumKredit = (float) ($aggSebelum->k ?? 0);
        }

        $isKreditNormal = ($akun?->saldo_normal === 'kredit');
        $saldoAwal = $saldoAwalMaster + ($isKreditNormal
            ? ($mutasiSebelumKredit - $mutasiSebelumDebet)
            : ($mutasiSebelumDebet - $mutasiSebelumKredit));

        $query = JurnalUmumDetail::with('jurnal')
            ->where('kode_perkiraan', $akunPilihan)
            ->whereHas('jurnal', function ($q) use ($dari, $sampai) {
                $q->where('status', 'posted');
                if ($dari) {
                    $q->whereDate('tanggal', '>=', $dari);
                }
                if ($sampai) {
                    $q->whereDate('tanggal', '<=', $sampai);
                }
            });

        $mutasiBuku = $query->latest('id')->get();

        $totalDebet = (float) $mutasiBuku->sum('debet');
        $totalKredit = (float) $mutasiBuku->sum('kredit');
        $mutasiNet = $isKreditNormal ? ($totalKredit - $totalDebet) : ($totalDebet - $totalKredit);
        $saldoAkhir = $saldoAwal + $mutasiNet;

        return view('pages.accounting.gl.buku_besar', compact(
            'daftarAkun',
            'akunPilihan',
            'akun',
            'mutasiBuku',
            'saldoAwal',
            'totalDebet',
            'totalKredit',
            'saldoAkhir',
            'dari',
            'sampai'
        ));
    }

    public function cetakGlBukuBesar(Request $request)
    {
        $response = $this->glBukuBesar($request);
        return view('pages.accounting.gl.cetak_buku_besar', $response->getData());
    }

    public function glNeracaLajur(Request $request)
    {
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));
        $q = trim((string) $request->input('q', ''));
        $kepala = $request->input('kepala', 'all');
        $sembunyikanNol = $request->boolean('hide_zero', false);
        $startOfPeriod = Carbon::parse($periode . '-01')->startOfMonth()->toDateString();

        $queryAkun = AkunPerkiraan::aktif()->orderBy('kode_perkiraan');
        if ($q !== '') {
            $queryAkun->where(function ($sub) use ($q) {
                $sub->where('kode_perkiraan', 'like', "%{$q}%")
                    ->orWhere('nama_perkiraan', 'like', "%{$q}%");
            });
        }
        if ($kepala !== 'all') {
            $queryAkun->where('kepala', (int) $kepala);
        }

        $daftarAkun = $queryAkun->get();

        // 1. Query saldo awal akumulatif sebelum periode berjalan (untuk akun neraca kepala 1-3 + master saldo_awal)
        $openingMap = JurnalUmumDetail::whereHas('jurnal', function ($query) use ($startOfPeriod) {
                $query->where('status', 'posted')->whereDate('tanggal', '<', $startOfPeriod);
            })
            ->selectRaw('kode_perkiraan, COALESCE(SUM(debet), 0) as total_debet, COALESCE(SUM(kredit), 0) as total_kredit')
            ->groupBy('kode_perkiraan')
            ->get()
            ->keyBy('kode_perkiraan');

        // 2. Query mutasi bulan berjalan
        $mutasiMap = JurnalUmumDetail::whereHas('jurnal', function ($query) use ($periode) {
                $query->where('status', 'posted')->where('tanggal', 'like', "{$periode}%");
            })
            ->selectRaw('kode_perkiraan, COALESCE(SUM(debet), 0) as total_debet, COALESCE(SUM(kredit), 0) as total_kredit')
            ->groupBy('kode_perkiraan')
            ->get()
            ->keyBy('kode_perkiraan');

        $neracaLajur = [];
        $totalMutasiDebet = 0.0;
        $totalMutasiKredit = 0.0;
        $totalDebet = 0.0;
        $totalKredit = 0.0;

        foreach ($daftarAkun as $akun) {
            $isBalanceSheet = in_array((int) $akun->kepala, [1, 2, 3], true);
            $masterAwal = (float) ($akun->saldo_awal ?? 0);

            $openRow = $isBalanceSheet ? $openingMap->get($akun->kode_perkiraan) : null;
            $openDebet = (float) ($openRow->total_debet ?? 0) + (($akun->saldo_normal !== 'kredit' && $masterAwal > 0) ? $masterAwal : 0);
            $openKredit = (float) ($openRow->total_kredit ?? 0) + (($akun->saldo_normal === 'kredit' && $masterAwal > 0) ? $masterAwal : 0);

            $mutRow = $mutasiMap->get($akun->kode_perkiraan);
            $mutDebet = (float) ($mutRow->total_debet ?? 0);
            $mutKredit = (float) ($mutRow->total_kredit ?? 0);

            $cumDebet = $openDebet + $mutDebet;
            $cumKredit = $openKredit + $mutKredit;

            if ($akun->saldo_normal === 'kredit') {
                $saldoKredit = max(0, $cumKredit - $cumDebet);
                $saldoDebet = max(0, $cumDebet - $cumKredit);
                $saldoAwalNet = ($openKredit - $openDebet);
            } else {
                $saldoDebet = max(0, $cumDebet - $cumKredit);
                $saldoKredit = max(0, $cumKredit - $cumDebet);
                $saldoAwalNet = ($openDebet - $openKredit);
            }

            if ($sembunyikanNol && $saldoDebet == 0 && $saldoKredit == 0 && $mutDebet == 0 && $mutKredit == 0 && $saldoAwalNet == 0) {
                continue;
            }

            $totalMutasiDebet += $mutDebet;
            $totalMutasiKredit += $mutKredit;
            $totalDebet += $saldoDebet;
            $totalKredit += $saldoKredit;

            $neracaLajur[] = [
                'kode'          => $akun->kode_perkiraan,
                'nama'          => $akun->nama_perkiraan,
                'posisi_normal' => $akun->saldo_normal ?? 'debet',
                'saldo_awal'    => $saldoAwalNet,
                'mutasi_debet'  => $mutDebet,
                'mutasi_kredit' => $mutKredit,
                'debet'         => $saldoDebet,
                'kredit'        => $saldoKredit,
            ];
        }

        return view('pages.accounting.gl.neraca_lajur', compact(
            'periode',
            'neracaLajur',
            'totalMutasiDebet',
            'totalMutasiKredit',
            'totalDebet',
            'totalKredit',
            'q',
            'kepala',
            'sembunyikanNol'
        ));
    }

    public function cetakGlNeracaLajur(Request $request)
    {
        $response = $this->glNeracaLajur($request);
        return view('pages.accounting.gl.cetak_neraca_lajur', $response->getData());
    }
}
