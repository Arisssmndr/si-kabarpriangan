<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\BuktiPengeluaran;
use App\Models\KasKeluar;
use App\Models\KasTransaksi;
use App\Models\PengajuanAnggaran;
use App\Models\AnggaranRab;
use App\Models\TutupAnggaran;
use App\Models\DpKasbon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnggaranController extends Controller
{
    /**
     * Dashboard & Pemantauan Anggaran Multi-Divisi (Sesuai Sistem DOS ANGGARAN.EXE)
     * Terintegrasi langsung dengan BP Priangan, Register PR, dan Pencairan Kasir BKK.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'semua'); // 'semua', 'pending', 'approved', 'cair', 'cek_pencairan'

        $query = PengajuanAnggaran::with([
            'creator', 
            'approver', 
            'disburser', 
            'buktiPengeluaran.kasKeluar'
        ]);

        // Filter Pencarian Teks (No. Pengajuan, No. BP, No. Register PR, Keterangan)
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('no_pengajuan', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%")
                    ->orWhereHas('buktiPengeluaran', function ($bp) use ($q) {
                        $bp->where('no_bp', 'like', "%{$q}%")
                           ->orWhere('no_register_anggaran', 'like', "%{$q}%")
                           ->orWhere('pemohon', 'like', "%{$q}%");
                    })
                    ->orWhereHas('buktiPengeluaran.kasKeluar', function ($kk) use ($q) {
                        $kk->where('no_bkk', 'like', "%{$q}%");
                    });
            });
        }

        // Filter Tab Workflow / Cek Pencairan
        if ($tab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($tab === 'approved' || $tab === 'siap_cair') {
            $query->where('status', 'approved');
        } elseif ($tab === 'cair') {
            $query->where('status', 'cair');
        }

        // Filter Status spesifik jika dikirim manual
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected', 'cair'])) {
            $query->where('status', $request->status);
        }

        // Filter Divisi
        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }

        $pengajuans = $query->latest('tanggal_pengajuan')->paginate(12)->withQueryString();

        // Ringkasan Anggaran Kumulatif
        $totalDiajukan  = (float) PengajuanAnggaran::sum('nominal_diajukan');
        $totalDicairkan = (float) PengajuanAnggaran::where('status', 'cair')->sum('nominal_disetujui');
        $pendingCount   = PengajuanAnggaran::where('status', 'pending')->count();
        $approvedCount  = PengajuanAnggaran::where('status', 'approved')->count();
        $cairCount      = PengajuanAnggaran::where('status', 'cair')->count();

        // Saldo Kas Saat Ini di Buku Kas (Treasury)
        $currentKasSaldo = (float) (KasTransaksi::latest('id')->value('saldo_akhir') ?? 0);

        // Daftar 7 Bidang Utama Sesuai Menu DOS TABEL > BIDANG
        $daftarDivisi = [
            'iklan'          => '01 - Divisi Iklan',
            'sirkulasi'      => '02 - Divisi Sirkulasi',
            'sekum'          => '03 - Sekretariat Umum (Sekum & SDM)',
            'akuntansi'      => '04 - Divisi Akuntansi',
            'keuangan'       => '05 - Divisi Keuangan',
            'redaksi'        => '06 - Divisi Redaksi & Wartawan',
            'bank'           => 'Kas & Bank Operasional',
            'management_fee' => '07 - Pimpinan / Management Fee',
        ];

        return view('pages.keuangan.anggaran.index', compact(
            'pengajuans',
            'totalDiajukan',
            'totalDicairkan',
            'pendingCount',
            'approvedCount',
            'cairCount',
            'currentKasSaldo',
            'daftarDivisi',
            'tab'
        ));
    }

    /**
     * Form Pengajuan Anggaran Baru (Entry BP Priangan).
     */
    public function create()
    {
        $daftarDivisi = [
            'iklan'          => '01 - Divisi Iklan',
            'sirkulasi'      => '02 - Divisi Sirkulasi',
            'sekum'          => '03 - Sekretariat Umum (Sekum & SDM)',
            'akuntansi'      => '04 - Divisi Akuntansi',
            'keuangan'       => '05 - Divisi Keuangan',
            'redaksi'        => '06 - Divisi Redaksi & Wartawan',
            'bank'           => 'Kas & Bank Operasional',
            'management_fee' => '07 - Pimpinan / Management Fee',
        ];

        $period = date('Ym');
        $prefixAng = "ANG-{$period}-";
        $latestAng = PengajuanAnggaran::where('no_pengajuan', 'like', "{$prefixAng}%")
            ->orderByRaw('CAST(SUBSTRING(no_pengajuan, -3) AS UNSIGNED) DESC')
            ->first();

        $nextSeq = 1;
        if ($latestAng) {
            $parts = explode('-', $latestAng->no_pengajuan);
            $nextSeq = ((int) end($parts)) + 1;
        }

        do {
            $noPengajuanSuggested = $prefixAng . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
            $nextSeq++;
        } while (PengajuanAnggaran::where('no_pengajuan', $noPengajuanSuggested)->exists());

        $noBpSuggested = BuktiPengeluaran::generateNoBp();

        return view('pages.keuangan.anggaran.create', compact('daftarDivisi', 'noPengajuanSuggested', 'noBpSuggested'));
    }

    /**
     * Simpan Pengajuan Anggaran Baru & Terbitkan BP Priangan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_pengajuan'      => 'required|string|unique:pengajuan_anggaran,no_pengajuan',
            'no_bp'             => 'nullable|string|unique:bukti_pengeluarans,no_bp',
            'divisi'            => 'required|in:iklan,sirkulasi,sekum,redaksi,akuntansi,keuangan,bank,management_fee',
            'tanggal_pengajuan' => 'required|date',
            'nominal_diajukan'  => 'required|numeric|min:1000',
            'keterangan'        => 'required|string|max:1000',
            'pemohon'           => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($request) {
            $userId = Auth::id() ?? 1;
            $userName = Auth::user()->name ?? 'Staf Anggaran';

            $pengajuan = PengajuanAnggaran::create([
                'no_pengajuan'      => $request->no_pengajuan,
                'divisi'            => $request->divisi,
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'nominal_diajukan'  => $request->nominal_diajukan,
                'nominal_disetujui' => null,
                'status'            => 'pending',
                'keterangan'        => $request->keterangan,
                'created_by'        => $userId,
            ]);

            // Pemetaan Kode Bagian Sesuai Standar DOS ANGGARAN.EXE
            $kodeBagian = match ($request->divisi) {
                'iklan'          => '01',
                'sirkulasi'      => '02',
                'sekum'          => '03',
                'akuntansi'      => '04',
                'keuangan'       => '05',
                'redaksi'        => '06',
                'management_fee' => '07',
                default          => '03',
            };

            $kodePerkiraan = match ($kodeBagian) {
                '01'    => '6.00.01.01.01.02',
                '02'    => '6.00.01.00.01.04',
                '03'    => '6.00.01.00.01.04',
                '04'    => '6.00.01.01.01.03',
                '05'    => '6.00.01.01.01.02',
                '06'    => '5.00.01.01.02',
                '07'    => '6.00.01.01.00.00',
                default => '6.00.01.00.01.04',
            };

            $namaPerkiraan = match ($kodeBagian) {
                '01'    => 'Beban Operasional Iklan',
                '02'    => 'Beban Sirkulasi & Ekspedisi',
                '03'    => 'Beban Umum & SDM / Perlengkapan RT',
                '04'    => 'Beban Administrasi Akuntansi',
                '05'    => 'Beban Keuangan & Pajak',
                '06'    => 'Beban Redaksi & Honor Wartawan',
                '07'    => 'Beban Operasional Pimpinan',
                default => 'Beban Operasional ' . $pengajuan->nama_divisi,
            };

            // Terbitkan Dokumen BP Priangan Terkoneksi (Bisa nomor otomatis / input manual)
            $noBp = $request->filled('no_bp') ? trim($request->no_bp) : BuktiPengeluaran::generateNoBp();
            BuktiPengeluaran::create([
                'no_bp'                 => $noBp,
                'no_register_anggaran'  => null, // Diisi saat disetujui (ACC)
                'pengajuan_anggaran_id' => $pengajuan->id,
                'divisi'                => $pengajuan->nama_divisi,
                'kode_bagian'           => $kodeBagian,
                'kode_perkiraan'        => $kodePerkiraan,
                'nama_perkiraan'        => $namaPerkiraan,
                'tanggal_pengajuan'     => $pengajuan->tanggal_pengajuan,
                'pemohon'               => $request->input('pemohon', $userName),
                'keperluan'             => $pengajuan->keterangan,
                'nominal_pengajuan'     => $pengajuan->nominal_diajukan,
                'nominal_acc'           => $pengajuan->nominal_diajukan,
                'status'                => 'pending',
                'catatan'               => 'Pendaftaran pengajuan anggaran baru via sistem terpadu',
            ]);

            return redirect()->route('keuangan.anggaran.index')
                ->with('success', "Pengajuan Anggaran {$pengajuan->no_pengajuan} & Dokumen BP {$noBp} berhasil didaftarkan dan menunggu persetujuan (ACC).");
        });
    }

    /**
     * Setujui (Approve) Pengajuan Anggaran & Terbitkan Nomor Register Anggaran (PR).
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->hasRole('keuangan', 'admin_keuangan'))) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Hanya Manajer Keuangan atau Administrator yang berwenang menyetujui anggaran.'
                ], 403);
            }
            return back()->with('error', 'Akses ditolak: Hanya Manajer Keuangan atau Administrator yang berwenang menyetujui anggaran.');
        }

        $request->validate([
            'nominal_disetujui' => 'required|numeric|min:1000',
        ]);

        return DB::transaction(function () use ($id, $request) {
            $pengajuan = PengajuanAnggaran::lockForUpdate()->findOrFail($id);
            if ($pengajuan->status !== 'pending') {
                return back()->with('error', 'Hanya pengajuan dengan status Pending yang dapat disetujui.');
            }

            $userId = Auth::id();
            $pengajuan->update([
                'status'            => 'approved',
                'nominal_disetujui' => $request->nominal_disetujui,
                'approved_by'       => $userId,
            ]);

            // Cari atau buatkan Dokumen BP Priangan
            $bp = BuktiPengeluaran::where('pengajuan_anggaran_id', $pengajuan->id)->first();
            if (!$bp) {
                $noBp = BuktiPengeluaran::generateNoBp();
                $bp = new BuktiPengeluaran();
                $bp->no_bp = $noBp;
                $bp->pengajuan_anggaran_id = $pengajuan->id;
                $bp->divisi = $pengajuan->nama_divisi;
                $bp->tanggal_pengajuan = $pengajuan->tanggal_pengajuan;
                $bp->pemohon = $pengajuan->creator->name ?? 'Staf ' . $pengajuan->nama_divisi;
                $bp->keperluan = $pengajuan->keterangan;
                $bp->nominal_pengajuan = $pengajuan->nominal_diajukan;
            }

            // Terbitkan Nomor Register Anggaran resmi (PR-YYYYMMDD-XXXX)
            $noRegister = $bp->no_register_anggaran ?: BuktiPengeluaran::generateNoRegister();

            $bp->no_register_anggaran = $noRegister;
            $bp->nominal_acc = $request->nominal_disetujui;
            $bp->status = 'approved';
            $bp->approved_by = $userId;
            $bp->save();

            $successMsg = "Pengajuan Anggaran {$pengajuan->no_pengajuan} [BP: {$bp->no_bp}] disetujui (ACC) dengan Register PR: {$noRegister} senilai Rp " . number_format($request->nominal_disetujui, 0, ',', '.') . ". Siap dicairkan ke Kas!";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'           => true,
                    'message'           => $successMsg,
                    'no_register'       => $noRegister,
                    'nominal_disetujui' => $request->nominal_disetujui,
                    'nominal_fmt'       => number_format($request->nominal_disetujui, 0, ',', '.'),
                ]);
            }

            return back()->with('success', $successMsg);
        });
    }

    /**
     * Tolak (Reject) Pengajuan Anggaran.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->hasRole('keuangan', 'admin_keuangan'))) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Hanya Manajer Keuangan atau Administrator yang berwenang menolak anggaran.'
                ], 403);
            }
            return back()->with('error', 'Akses ditolak: Hanya Manajer Keuangan atau Administrator yang berwenang menolak anggaran.');
        }

        $pengajuan = PengajuanAnggaran::findOrFail($id);
        if ($pengajuan->status !== 'pending') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pengajuan berstatus Pending yang dapat ditolak.'
                ], 422);
            }
            return back()->with('error', 'Hanya pengajuan berstatus Pending yang dapat ditolak.');
        }

        $userId = Auth::id();
        $pengajuan->update([
            'status'      => 'rejected',
            'approved_by' => $userId,
        ]);

        BuktiPengeluaran::where('pengajuan_anggaran_id', $pengajuan->id)
            ->update(['status' => 'rejected', 'approved_by' => $userId]);

        $successMsg = "Pengajuan Anggaran {$pengajuan->no_pengajuan} telah ditolak.";
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
            ]);
        }

        return back()->with('success', $successMsg);
    }

    /**
     * Pencairan Anggaran ke Kas (Cash Out): Otomatisasi BKK (KO-...) & Mutasi Kas Keluar.
     */
    public function cairkan(Request $request, $id)
    {
        return DB::transaction(function () use ($id, $request) {
            $pengajuan = PengajuanAnggaran::lockForUpdate()->findOrFail($id);

            if ($pengajuan->status !== 'approved') {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hanya pengajuan berstatus Disetujui (Approved / ACC) yang dapat dicairkan.'
                    ], 422);
                }
                return back()->with('error', 'Hanya pengajuan berstatus Disetujui (Approved / ACC) yang dapat dicairkan.');
            }

            $nominal = (float) $pengajuan->nominal_disetujui;
            $saldoKas = (float) (KasTransaksi::lockForUpdate()->latest('id')->value('saldo_akhir') ?? 0);

            if ($saldoKas < $nominal) {
                $errMsg = 'Saldo Kas tidak mencukupi! Saldo saat ini Rp ' . number_format($saldoKas, 0, ',', '.') . ', dibutuhkan Rp ' . number_format($nominal, 0, ',', '.') . '.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errMsg
                    ], 422);
                }
                return back()->with('error', $errMsg);
            }

            $userId = Auth::id() ?? 1;
            $tanggalCair = $request->input('tanggal_cair', Carbon::now()->toDateString());
            $metodeBayar = $request->input('metode_bayar', 'kas_fisik');

            // 1. Update Pengajuan Anggaran
            $pengajuan->update([
                'status'       => 'cair',
                'cair_by'      => $userId,
                'tanggal_cair' => $tanggalCair,
            ]);

            // 2. Ambil atau Buatkan Bukti Pengeluaran (BP)
            $bp = BuktiPengeluaran::where('pengajuan_anggaran_id', $pengajuan->id)->first();
            if (!$bp) {
                $bp = BuktiPengeluaran::create([
                    'no_bp'                 => BuktiPengeluaran::generateNoBp(),
                    'no_register_anggaran'  => BuktiPengeluaran::generateNoRegister(),
                    'pengajuan_anggaran_id' => $pengajuan->id,
                    'divisi'                => $pengajuan->nama_divisi,
                    'kode_bagian'           => '03',
                    'tanggal_pengajuan'     => $pengajuan->tanggal_pengajuan,
                    'pemohon'               => $pengajuan->creator->name ?? 'Pemohon',
                    'keperluan'             => $pengajuan->keterangan,
                    'nominal_pengajuan'     => $pengajuan->nominal_diajukan,
                    'nominal_acc'           => $nominal,
                    'status'                => 'dicairkan_kasir',
                    'approved_by'           => $pengajuan->approved_by ?? $userId,
                    'dicairkan_oleh'        => $userId,
                    'dicairkan_at'          => Carbon::now(),
                ]);
            } else {
                $bp->update([
                    'status'         => 'dicairkan_kasir',
                    'dicairkan_oleh' => $userId,
                    'dicairkan_at'   => Carbon::now(),
                ]);
            }

            // 3. Terbitkan Nomor Bukti Kas Keluar DOS KAS.EXE (KO-YYYYMMXXXX)
            $noBkk = KasKeluar::generateNoBkk();

            // 4. Catat ke Tabel Kas Keluar (BKK)
            $kodeBagian = $bp->kode_bagian ?: match ($pengajuan->divisi) {
                'iklan'     => '01',
                'sirkulasi' => '02',
                'sekum'     => '03',
                'akuntansi' => '04',
                'keuangan'  => '05',
                'redaksi'   => '06',
                default     => '03',
            };

            $kodePerkiraan = $bp->kode_perkiraan ?: match ($kodeBagian) {
                '01' => '6.00.01.01.01.02',
                '02' => '6.00.01.00.01.04',
                '03' => '6.00.01.00.01.04',
                '04' => '6.00.01.01.01.03',
                '05' => '6.00.01.01.01.02',
                '06' => '5.00.01.01.02',
                default => '6.00.01.00.01.04',
            };

            $kasKeluar = KasKeluar::create([
                'no_bkk'               => $noBkk,
                'tanggal_keluar'       => $tanggalCair,
                'tgl_transaksi'        => $tanggalCair,
                'no_bp_ref'            => $bp->no_bp,
                'no_register_anggaran' => $bp->no_register_anggaran,
                'kode_bagian'          => $kodeBagian,
                'kode_perkiraan'       => $kodePerkiraan,
                'nama_perkiraan'       => $bp->nama_perkiraan ?: ('Beban Operasional ' . $pengajuan->nama_divisi),
                'diberikan_kepada'     => $bp->pemohon,
                'nama_penerima'        => $bp->pemohon,
                'kategori'             => 'umum',
                'kategori_beban'       => 'umum',
                'jumlah_keluar'        => $nominal,
                'nominal_keluar'       => $nominal,
                'metode_bayar'         => $metodeBayar,
                'bp_id'                => $bp->id,
                'keterangan'           => "Pencairan BP {$bp->no_bp} [Reg: {$bp->no_register_anggaran}] ({$pengajuan->nama_divisi}): {$pengajuan->keterangan}",
                'kasir_id'             => $userId,
                'user_id'              => $userId,
                'is_closed'            => false,
                'is_pulled_accounting' => false,
                'status_tarik_akunting'=> false,
            ]);

            // 5. Catat Mutasi Kas Keluar Treasury Real-Time
            KasTransaksi::catatMutasi(
                'keluar',
                'Pencairan Anggaran ' . $pengajuan->nama_divisi,
                $nominal,
                "KO {$noBkk} - Pencairan BP {$bp->no_bp} [Reg: {$bp->no_register_anggaran}] ({$pengajuan->nama_divisi}): {$pengajuan->keterangan}",
                $kasKeluar,
                $userId,
                $tanggalCair
            );

            $successMsg = "Dana Anggaran Rp " . number_format($nominal, 0, ',', '.') . " berhasil dicairkan! BKK: {$noBkk} telah diterbitkan dan Mutasi Kas Keluar telah dipotong dari saldo kas.";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'      => true,
                    'message'      => $successMsg,
                    'no_bkk'       => $noBkk,
                    'no_bp'        => $bp->no_bp,
                    'voucher_url'  => route('keuangan.anggaran.voucher', $pengajuan->id),
                    'nominal'      => $nominal,
                    'nominal_fmt'  => number_format($nominal, 0, ',', '.'),
                    'no_pengajuan' => $pengajuan->no_pengajuan,
                ]);
            }

            return back()->with('success', $successMsg);
        });
    }

    /**
     * Cetak Bukti Voucher Kas Keluar / BKK Resmi.
     */
    public function cetakVoucher($id)
    {
        $pengajuan = PengajuanAnggaran::with([
            'creator', 
            'approver', 
            'disburser',
            'buktiPengeluaran.kasKeluar'
        ])->findOrFail($id);

        return view('pages.keuangan.anggaran.voucher', compact('pengajuan'));
    }

    /**
     * Cetak Rekap BP Priangan (Sesuai Menu DOS CETAK > REKAP BP PRIANGAN PER TANGGAL / BULAN)
     */
    public function rekapBp(Request $request)
    {
        $mode = $request->get('mode', 'bulan'); // 'tanggal', 'bulan'
        $bulan = (int) $request->get('bulan', date('m'));
        $tahun = (int) $request->get('tahun', date('Y'));
        $dariTanggal = $request->get('dari_tanggal', Carbon::today()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->get('sampai_tanggal', Carbon::today()->endOfMonth()->toDateString());

        $query = BuktiPengeluaran::with(['pengajuanAnggaran', 'kasKeluar', 'approver', 'disburser']);

        if ($mode === 'tanggal') {
            $query->whereBetween('tanggal_pengajuan', [$dariTanggal, $sampaiTanggal]);
            $periodeJudul = Carbon::parse($dariTanggal)->translatedFormat('d F Y') . ' s/d ' . Carbon::parse($sampaiTanggal)->translatedFormat('d F Y');
        } else {
            $query->whereYear('tanggal_pengajuan', $tahun)
                  ->whereMonth('tanggal_pengajuan', $bulan);
            $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');
            $periodeJudul = "BULAN " . strtoupper($namaBulan);
        }

        $bpList = $query->orderBy('tanggal_pengajuan', 'asc')->get();

        $totalPengajuan = (float) $bpList->sum('nominal_pengajuan');
        $totalAcc       = (float) $bpList->sum('nominal_acc');
        $totalCair      = (float) $bpList->where('status', 'dicairkan_kasir')->sum('nominal_acc');
        $totalPending   = (float) $bpList->where('status', '!=', 'dicairkan_kasir')->sum('nominal_acc');

        $viewData = compact(
            'bpList',
            'periodeJudul',
            'mode',
            'bulan',
            'tahun',
            'dariTanggal',
            'sampaiTanggal',
            'totalPengajuan',
            'totalAcc',
            'totalCair',
            'totalPending'
        );

        // Jika request cetak langsung (misal dari tombol Cetak / PDF) -> render halaman cetak standalone
        if ($request->get('cetak') == '1' || $request->get('print') == '1') {
            return view('pages.keuangan.anggaran.cetak-rekap-bp', $viewData);
        }

        // Tampilan standar di dalam aplikasi (dengan sidebar lengkap & fitur filter interaktif)
        return view('pages.keuangan.anggaran.rekap-bp', $viewData);
    }

    /**
     * Tabel Master: BIDANG (Kode Bagian 01 - 07 Sesuai Standar DOS ANGGARAN.EXE)
     */
    public function tabelBidang(Request $request)
    {
        $search = $request->get('q');
        
        $bidangList = [
            [
                'kode'       => '01',
                'slug'       => 'iklan',
                'nama'       => 'Divisi Iklan',
                'deskripsi'  => 'Operasional penerimaan iklan display, adv, kolom, baris, event, dan komisi agen iklan.',
                'pos_beban'  => '6.00.01.01.01.02',
                'nama_pos'   => 'Beban Operasional Iklan',
                'pj'         => 'Manajer Iklan / Koordinator',
            ],
            [
                'kode'       => '02',
                'slug'       => 'sirkulasi',
                'nama'       => 'Divisi Sirkulasi',
                'deskripsi'  => 'Distribusi koran harian, jatah agen koran, biaya ekspedisi luar kota, dan BBM armada sirkulasi.',
                'pos_beban'  => '6.00.01.00.01.04',
                'nama_pos'   => 'Beban Sirkulasi & Ekspedisi',
                'pj'         => 'Manajer Sirkulasi / Ekspedisi',
            ],
            [
                'kode'       => '03',
                'slug'       => 'sekum',
                'nama'       => 'Sekretariat Umum (Sekum & SDM)',
                'deskripsi'  => 'Rumah tangga kantor, perlengkapan ATK, listrik/air/telp, kebersihan, perawatan gedung & kendaraan.',
                'pos_beban'  => '6.00.01.00.01.04',
                'nama_pos'   => 'Beban Umum & SDM / Perlengkapan RT',
                'pj'         => 'Kabag Umum & SDM',
            ],
            [
                'kode'       => '04',
                'slug'       => 'akuntansi',
                'nama'       => 'Divisi Akuntansi',
                'deskripsi'  => 'Penyusunan laporan keuangan, pembukuan jurnal, konsultan audit eksternal, software akuntansi.',
                'pos_beban'  => '6.00.01.01.01.03',
                'nama_pos'   => 'Beban Administrasi Akuntansi',
                'pj'         => 'Kepala Bagian Akuntansi',
            ],
            [
                'kode'       => '05',
                'slug'       => 'keuangan',
                'nama'       => 'Divisi Keuangan & Kasir',
                'deskripsi'  => 'Treasury, manajemen kas masuk/keluar, administrasi bank, pajak operasional, materai, dan penagihan inkaso.',
                'pos_beban'  => '6.00.01.01.01.02',
                'nama_pos'   => 'Beban Keuangan & Pajak',
                'pj'         => 'Manajer Keuangan',
            ],
            [
                'kode'       => '06',
                'slug'       => 'redaksi',
                'nama'       => 'Divisi Redaksi & Wartawan',
                'deskripsi'  => 'Biaya liputan harian wartawan, honor tulisan kontributor daerah, fotografer, dan percetakan plat koran.',
                'pos_beban'  => '5.00.01.01.02',
                'nama_pos'   => 'Beban Redaksi & Honor Wartawan',
                'pj'         => 'Pemimpin Redaksi / Redaktur',
            ],
            [
                'kode'       => '07',
                'slug'       => 'management_fee',
                'nama'       => 'Pimpinan / Management Fee',
                'deskripsi'  => 'Biaya perjalanan dinas direksi/pimpinan, rapat manajemen eksekutif, dan pembagian management fee.',
                'pos_beban'  => '6.00.01.01.00.00',
                'nama_pos'   => 'Beban Operasional Pimpinan',
                'pj'         => 'Direksi / Pimpinan Perusahaan',
            ],
        ];

        // Hitung real-time jumlah BP yang terhubung ke masing-masing bidang
        foreach ($bidangList as &$b) {
            $b['total_bp'] = BuktiPengeluaran::where('kode_bagian', $b['kode'])->count();
            $b['total_nominal'] = BuktiPengeluaran::where('kode_bagian', $b['kode'])->sum('nominal_acc');
        }
        unset($b);

        if ($search) {
            $bidangList = array_filter($bidangList, function ($item) use ($search) {
                return stripos($item['nama'], $search) !== false 
                    || stripos($item['kode'], $search) !== false
                    || stripos($item['deskripsi'], $search) !== false;
            });
        }

        $activeTab = 'bidang';
        return view('pages.keuangan.anggaran.tabel', compact('bidangList', 'activeTab', 'search'));
    }

    /**
     * Tabel Master: PERKIRAAN PRIANGAN (Bagan Akun COA Pengeluaran Standar ANGGARAN.EXE)
     */
    public function tabelPerkiraan(Request $request)
    {
        $search = $request->get('q');

        $perkiraanList = [
            [
                'kode'        => '5.00.01.01.02',
                'nama'        => 'Beban Redaksi & Honor Wartawan',
                'bidang'      => '06 - Redaksi & Wartawan',
                'kategori'    => 'Beban Pokok Produksi',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Honor kontributor, biaya operasional liputan wartawan peliputan daerah & percetakan plat.',
            ],
            [
                'kode'        => '6.00.01.00.01.04',
                'nama'        => 'Beban Sirkulasi & Ekspedisi',
                'bidang'      => '02 - Sirkulasi',
                'kategori'    => 'Beban Operasional',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'BBM armada truk/motor koran, ekspedisi koran Tasik-Ciamis-Garut-Banjar-Pangandaran.',
            ],
            [
                'kode'        => '6.00.01.00.01.04',
                'nama'        => 'Beban Umum & SDM / Perlengkapan RT',
                'bidang'      => '03 - Sekum & SDM',
                'kategori'    => 'Beban Umum & Administrasi',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Listrik kantor Bumi Resik, tagihan air PDAM, internet redaksi, perlengkapan ATK, servis AC.',
            ],
            [
                'kode'        => '6.00.01.01.01.02',
                'nama'        => 'Beban Operasional Iklan',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Beban Penjualan',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Komisi biro iklan, insentif Account Executive (AE), biaya tender pemasangan iklan display.',
            ],
            [
                'kode'        => '6.00.01.01.01.02',
                'nama'        => 'Beban Keuangan & Pajak',
                'bidang'      => '05 - Keuangan',
                'kategori'    => 'Beban Administrasi Keuangan',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Pajak operasional, bea materai kuitansi, biaya transaksi transfer bank & perbankan.',
            ],
            [
                'kode'        => '6.00.01.01.01.03',
                'nama'        => 'Beban Administrasi Akuntansi',
                'bidang'      => '04 - Akuntansi',
                'kategori'    => 'Beban Administrasi',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Biaya jasa kantor akuntan publik, pengadaan buku/formulir akuntansi, pemeliharaan sistem.',
            ],
            [
                'kode'        => '6.00.01.01.00.00',
                'nama'        => 'Beban Operasional Pimpinan',
                'bidang'      => '07 - Pimpinan',
                'kategori'    => 'Beban Manajemen Eksekutif',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Akomodasi dinas pimpinan luar kota, rapat pemegang saham, dan alokasi management fee.',
            ],
        ];

        foreach ($perkiraanList as &$p) {
            $p['total_bp'] = BuktiPengeluaran::where('kode_perkiraan', $p['kode'])->count();
            $p['total_rupiah'] = BuktiPengeluaran::where('kode_perkiraan', $p['kode'])->sum('nominal_acc');
        }
        unset($p);

        if ($search) {
            $perkiraanList = array_filter($perkiraanList, function ($item) use ($search) {
                return stripos($item['nama'], $search) !== false 
                    || stripos($item['kode'], $search) !== false
                    || stripos($item['bidang'], $search) !== false;
            });
        }

        $activeTab = 'perkiraan';
        return view('pages.keuangan.anggaran.tabel', compact('perkiraanList', 'activeTab', 'search'));
    }

    /**
     * Tabel Master: REKENING BANK PRIANGAN (Standar DOS ANGGARAN.EXE)
     */
    public function tabelRekeningBank(Request $request)
    {
        $search = $request->get('q');

        $bankList = [
            [
                'bank_nama'       => 'Bank BJB (Bank Pembangunan Daerah Jabar Banten)',
                'cabang'          => 'KCP Tasikmalaya Sutisna Senjaya',
                'no_rekening'     => '001-023-456789-0',
                'atas_nama'       => 'PT BERKAH PIKIRAN RAKYAT (KABAR PRIANGAN)',
                'tipe_rekening'   => 'Giro Operasional Utama',
                'fungsi_utama'    => 'Pencairan Kasir & Pengeluaran BP Priangan, Rekening Koran BJB',
                'kode_coa'        => '1.00.02.01.01.01',
                'status'          => 'Aktif (Utama)',
            ],
            [
                'bank_nama'       => 'Bank Central Asia (BCA)',
                'cabang'          => 'KCU Tasikmalaya',
                'no_rekening'     => '054-089-1234',
                'atas_nama'       => 'PT BERKAH PIKIRAN RAKYAT',
                'tipe_rekening'   => 'Giro Bisnis Korporat',
                'fungsi_utama'    => 'Penerimaan Pembayaran Faktur Iklan Nasional & Langganan',
                'kode_coa'        => '1.00.02.01.01.02',
                'status'          => 'Aktif',
            ],
            [
                'bank_nama'       => 'Bank Mandiri (Persero)',
                'cabang'          => 'KC Tasikmalaya Otto Iskandardinata',
                'no_rekening'     => '131-00-9876543-2',
                'atas_nama'       => 'PT BERKAH PIKIRAN RAKYAT',
                'tipe_rekening'   => 'Giro Operasional Cadangan',
                'fungsi_utama'    => 'Operasional Khusus Penggajian (Payroll) & Pajak Perusahaan',
                'kode_coa'        => '1.00.02.01.01.03',
                'status'          => 'Aktif',
            ],
            [
                'bank_nama'       => 'Bank Rakyat Indonesia (BRI)',
                'cabang'          => 'Kanca Tasikmalaya',
                'no_rekening'     => '0100-01-001234-50-8',
                'atas_nama'       => 'PT BERKAH PIKIRAN RAKYAT',
                'tipe_rekening'   => 'BritAma Bisnis',
                'fungsi_utama'    => 'Penerimaan Setoran Koran dari Agen Sirkulasi Daerah Priangan Timur',
                'kode_coa'        => '1.00.02.01.01.04',
                'status'          => 'Aktif',
            ],
        ];

        if ($search) {
            $bankList = array_filter($bankList, function ($item) use ($search) {
                return stripos($item['bank_nama'], $search) !== false 
                    || stripos($item['no_rekening'], $search) !== false
                    || stripos($item['cabang'], $search) !== false
                    || stripos($item['fungsi_utama'], $search) !== false;
            });
        }

        $activeTab = 'rekening_bank';
        return view('pages.keuangan.anggaran.tabel', compact('bankList', 'activeTab', 'search'));
    }

    /**
     * ========================================================
     * MENU 2: ENTRY DOS ANGGARAN.EXE
     * Submenu: 1. RAB PRIANGAN
     * ========================================================
     */
    public function entryRab(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));

        $bidangMaster = [
            '01' => ['slug' => 'iklan',          'nama' => 'Divisi Iklan',               'default_plafon' => 15000000],
            '02' => ['slug' => 'sirkulasi',      'nama' => 'Divisi Sirkulasi',           'default_plafon' => 12000000],
            '03' => ['slug' => 'sekum',          'nama' => 'Sekretariat Umum (Sekum)',   'default_plafon' => 18000000],
            '04' => ['slug' => 'akuntansi',      'nama' => 'Divisi Akuntansi',           'default_plafon' => 5000000],
            '05' => ['slug' => 'keuangan',       'nama' => 'Divisi Keuangan & Kasir',    'default_plafon' => 8000000],
            '06' => ['slug' => 'redaksi',        'nama' => 'Divisi Redaksi & Wartawan',  'default_plafon' => 25000000],
            '07' => ['slug' => 'management_fee', 'nama' => 'Pimpinan / Management Fee',  'default_plafon' => 10000000],
        ];

        $rabItems = [];
        $totalPlafon = 0;
        $totalRealisasi = 0;

        foreach ($bidangMaster as $kode => $info) {
            $existingRab = AnggaranRab::where('periode', $periode)
                ->where('kode_bagian', $kode)
                ->first();

            $plafon = $existingRab ? (float) $existingRab->plafon_anggaran : $info['default_plafon'];

            // Hitung realisasi BP pada periode tersebut
            $realisasi = (float) BuktiPengeluaran::where('kode_bagian', $kode)
                ->where('tanggal_pengajuan', 'like', "{$periode}%")
                ->whereIn('status', ['approved', 'dicairkan_kasir'])
                ->sum('nominal_acc');

            $sisa = $plafon - $realisasi;
            $persenSerapan = $plafon > 0 ? min(100, round(($realisasi / $plafon) * 100, 1)) : 0;

            $rabItems[] = [
                'kode_bagian'    => $kode,
                'divisi'         => $info['slug'],
                'nama'           => $info['nama'],
                'plafon'         => $plafon,
                'realisasi'      => $realisasi,
                'sisa'           => $sisa,
                'persen'         => $persenSerapan,
                'keterangan'     => $existingRab?->keterangan ?? 'Alokasi Plafon Anggaran ' . Carbon::parse($periode)->translatedFormat('F Y'),
                'id'             => $existingRab?->id,
            ];

            $totalPlafon += $plafon;
            $totalRealisasi += $realisasi;
        }

        $totalSisa = $totalPlafon - $totalRealisasi;
        $totalPersen = $totalPlafon > 0 ? min(100, round(($totalRealisasi / $totalPlafon) * 100, 1)) : 0;

        return view('pages.keuangan.anggaran.rab', compact(
            'rabItems',
            'periode',
            'totalPlafon',
            'totalRealisasi',
            'totalSisa',
            'totalPersen'
        ));
    }

    public function storeRab(Request $request)
    {
        $request->validate([
            'periode'         => 'required|string',
            'kode_bagian'     => 'required|string',
            'plafon_anggaran' => 'required|numeric|min:0',
            'keterangan'      => 'nullable|string|max:255',
        ]);

        $divisi = match ($request->kode_bagian) {
            '01' => 'iklan',
            '02' => 'sirkulasi',
            '03' => 'sekum',
            '04' => 'akuntansi',
            '05' => 'keuangan',
            '06' => 'redaksi',
            '07' => 'management_fee',
            default => 'umum',
        };

        AnggaranRab::updateOrCreate(
            [
                'periode'     => $request->periode,
                'kode_bagian' => $request->kode_bagian,
            ],
            [
                'divisi'          => $divisi,
                'plafon_anggaran' => $request->plafon_anggaran,
                'keterangan'      => $request->keterangan,
                'created_by'      => Auth::id() ?? 1,
            ]
        );

        return redirect()->route('keuangan.anggaran.entry.rab', ['periode' => $request->periode])
            ->with('success', "Plafon RAB Bagian {$request->kode_bagian} periode {$request->periode} berhasil disimpan!");
    }

    /**
     * ========================================================
     * MENU 2: ENTRY DOS ANGGARAN.EXE
     * Submenu: 3. TUTUP ANGGARAN (Closing Anggaran Bulanan)
     * ========================================================
     */
    public function entryTutupAnggaran(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));

        $isClosed = TutupAnggaran::where('periode', $periode)->first();

        // Hitung akumulasi pada periode tersebut
        $totalDiajukan = (float) PengajuanAnggaran::where('tanggal_pengajuan', 'like', "{$periode}%")->sum('nominal_diajukan');
        $totalAcc      = (float) BuktiPengeluaran::where('tanggal_pengajuan', 'like', "{$periode}%")->sum('nominal_acc');
        $totalCair     = (float) BuktiPengeluaran::where('tanggal_pengajuan', 'like', "{$periode}%")->where('status', 'dicairkan_kasir')->sum('nominal_acc');
        $totalPending  = (float) BuktiPengeluaran::where('tanggal_pengajuan', 'like', "{$periode}%")->where('status', '!=', 'dicairkan_kasir')->sum('nominal_acc');
        
        $totalPlafonRAB = (float) AnggaranRab::where('periode', $periode)->sum('plafon_anggaran');
        if ($totalPlafonRAB <= 0) {
            $totalPlafonRAB = 93000000; // Default estimasi plafon 7 divisi
        }

        $sisaAnggaran = $totalPlafonRAB - $totalAcc;

        $riwayatClosing = TutupAnggaran::with('closedBy')->orderBy('periode', 'desc')->get();

        return view('pages.keuangan.anggaran.tutup-anggaran', compact(
            'periode',
            'isClosed',
            'totalDiajukan',
            'totalAcc',
            'totalCair',
            'totalPending',
            'totalPlafonRAB',
            'sisaAnggaran',
            'riwayatClosing'
        ));
    }

    public function prosesTutupAnggaran(Request $request)
    {
        $request->validate([
            'periode' => 'required|string',
            'catatan' => 'nullable|string|max:500',
        ]);

        $periode = $request->periode;

        $totalPlafon = (float) AnggaranRab::where('periode', $periode)->sum('plafon_anggaran');
        if ($totalPlafon <= 0) {
            $totalPlafon = 93000000;
        }

        $totalAcc   = (float) BuktiPengeluaran::where('tanggal_pengajuan', 'like', "{$periode}%")->sum('nominal_acc');
        $totalCair  = (float) BuktiPengeluaran::where('tanggal_pengajuan', 'like', "{$periode}%")->where('status', 'dicairkan_kasir')->sum('nominal_acc');
        $sisa       = $totalPlafon - $totalAcc;

        TutupAnggaran::updateOrCreate(
            ['periode' => $periode],
            [
                'tanggal_tutup'    => Carbon::today()->toDateString(),
                'total_plafon'     => $totalPlafon,
                'total_bp_acc'     => $totalAcc,
                'total_cair_kasir' => $totalCair,
                'sisa_anggaran'    => $sisa,
                'status'           => 'closed',
                'catatan'          => $request->catatan ?? "Tutup Anggaran Periode " . Carbon::parse($periode)->translatedFormat('F Y'),
                'ditutup_oleh'     => Auth::id() ?? 1,
            ]
        );

        return redirect()->route('keuangan.anggaran.entry.tutup-anggaran', ['periode' => $periode])
            ->with('success', "Proses Tutup Anggaran Periode {$periode} berhasil dikunci dan ditutup resmi!");
    }

    /**
     * ========================================================
     * MENU 2: ENTRY DOS ANGGARAN.EXE
     * Submenu: 4. DP KASBON (Uang Muka / Kasbon Operasional)
     * ========================================================
     */
    public function entryDpKasbon(Request $request)
    {
        $filterStatus = $request->get('status', 'semua');
        $search = $request->get('q');

        $query = DpKasbon::with(['buktiPengeluaran', 'creator']);

        if ($filterStatus === 'aktif') {
            $query->where('status', 'aktif');
        } elseif ($filterStatus === 'selesai') {
            $query->where('status', 'selesai');
        }

        if ($search) {
            $query->where(function ($sub) use ($search) {
                $sub->where('no_kasbon', 'like', "%{$search}%")
                    ->orWhere('penerima', 'like', "%{$search}%")
                    ->orWhere('keperluan', 'like', "%{$search}%")
                    ->orWhere('divisi', 'like', "%{$search}%");
            });
        }

        $kasbonList = $query->orderBy('tanggal_kasbon', 'desc')->get();

        $totalKasbonAktif = (float) DpKasbon::where('status', 'aktif')->sum('nominal');
        $totalKasbonSelesai = (float) DpKasbon::where('status', 'selesai')->sum('nominal');
        $jumlahAktif = DpKasbon::where('status', 'aktif')->count();

        $suggestedNoKb = DpKasbon::generateNoKasbon();

        return view('pages.keuangan.anggaran.dp-kasbon', compact(
            'kasbonList',
            'filterStatus',
            'search',
            'totalKasbonAktif',
            'totalKasbonSelesai',
            'jumlahAktif',
            'suggestedNoKb'
        ));
    }

    public function storeDpKasbon(Request $request)
    {
        $request->validate([
            'no_kasbon'       => 'required|string|unique:dp_kasbons,no_kasbon',
            'tanggal_kasbon'  => 'required|date',
            'divisi'          => 'required|string',
            'penerima'        => 'required|string|max:255',
            'nominal'         => 'required|numeric|min:1000',
            'keperluan'       => 'required|string|max:1000',
            'tgl_jatuh_tempo' => 'nullable|date',
        ]);

        $kodeBagian = match ($request->divisi) {
            'iklan'          => '01',
            'sirkulasi'      => '02',
            'sekum'          => '03',
            'akuntansi'      => '04',
            'keuangan'       => '05',
            'redaksi'        => '06',
            'management_fee' => '07',
            default          => '03',
        };

        DpKasbon::create([
            'no_kasbon'       => $request->no_kasbon,
            'tanggal_kasbon'  => $request->tanggal_kasbon,
            'kode_bagian'     => $kodeBagian,
            'divisi'          => $request->divisi,
            'penerima'        => $request->penerima,
            'nominal'         => $request->nominal,
            'keperluan'       => $request->keperluan,
            'tgl_jatuh_tempo' => $request->tgl_jatuh_tempo,
            'status'          => 'aktif',
            'created_by'      => Auth::id() ?? 1,
        ]);

        return redirect()->route('keuangan.anggaran.entry.dp-kasbon')
            ->with('success', "Data DP Kasbon {$request->no_kasbon} berhasil didaftarkan!");
    }

    public function selesaikanKasbon(Request $request, $id)
    {
        $kasbon = DpKasbon::findOrFail($id);
        $kasbon->status = 'selesai';
        $kasbon->save();

        return redirect()->route('keuangan.anggaran.entry.dp-kasbon')
            ->with('success', "Kasbon {$kasbon->no_kasbon} ({$kasbon->penerima}) telah diselesaikan / di-LPJ-kan!");
    }

    /**
     * ========================================================
     * MENU 3: CETAK DOS ANGGARAN.EXE (7 LAPORAN RESMI)
     * ========================================================
     */

    public function cetakHub(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        $tahun = (int) $request->get('tahun', date('Y'));

        // Data statistik cepat
        $totalBpBulanIni = BuktiPengeluaran::where('tanggal_pengajuan', 'like', "{$periode}%")->count();
        $totalNominalCair = (float) BuktiPengeluaran::where('tanggal_pengajuan', 'like', "{$periode}%")->where('status', 'dicairkan_kasir')->sum('nominal_acc');
        $totalBpTahunIni = BuktiPengeluaran::whereYear('tanggal_pengajuan', $tahun)->count();

        return view('pages.keuangan.anggaran.cetak.hub', compact(
            'periode',
            'tahun',
            'totalBpBulanIni',
            'totalNominalCair',
            'totalBpTahunIni'
        ));
    }

    /**
     * 1. REALISASI ANGGARAN SELURUH BIDANG PRIANGAN
     */
    public function cetakSeluruhBidang(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        $isPrint = $request->get('cetak') == '1' || $request->get('print') == '1';

        $bidangMaster = [
            '01' => ['slug' => 'iklan',          'nama' => 'Divisi Iklan',               'default_plafon' => 15000000],
            '02' => ['slug' => 'sirkulasi',      'nama' => 'Divisi Sirkulasi',           'default_plafon' => 12000000],
            '03' => ['slug' => 'sekum',          'nama' => 'Sekretariat Umum (Sekum)',   'default_plafon' => 18000000],
            '04' => ['slug' => 'akuntansi',      'nama' => 'Divisi Akuntansi',           'default_plafon' => 5000000],
            '05' => ['slug' => 'keuangan',       'nama' => 'Divisi Keuangan & Kasir',    'default_plafon' => 8000000],
            '06' => ['slug' => 'redaksi',        'nama' => 'Divisi Redaksi & Wartawan',  'default_plafon' => 25000000],
            '07' => ['slug' => 'management_fee', 'nama' => 'Pimpinan / Management Fee',  'default_plafon' => 10000000],
        ];

        $laporanList = [];
        $totalPlafon = 0;
        $totalRealisasi = 0;
        $totalBpCount = 0;

        foreach ($bidangMaster as $kode => $info) {
            $existingRab = AnggaranRab::where('periode', $periode)
                ->where('kode_bagian', $kode)
                ->first();
            $plafon = $existingRab ? (float) $existingRab->plafon_anggaran : $info['default_plafon'];

            $bpQuery = BuktiPengeluaran::where('kode_bagian', $kode)
                ->where('tanggal_pengajuan', 'like', "{$periode}%")
                ->whereIn('status', ['approved', 'dicairkan_kasir']);

            $realisasi = (float) $bpQuery->sum('nominal_acc');
            $countBp   = $bpQuery->count();
            $sisa      = $plafon - $realisasi;
            $persen    = $plafon > 0 ? min(100, round(($realisasi / $plafon) * 100, 1)) : 0;

            $laporanList[] = [
                'kode'       => $kode,
                'nama'       => $info['nama'],
                'plafon'     => $plafon,
                'realisasi'  => $realisasi,
                'sisa'       => $sisa,
                'persen'     => $persen,
                'count_bp'   => $countBp,
            ];

            $totalPlafon += $plafon;
            $totalRealisasi += $realisasi;
            $totalBpCount += $countBp;
        }

        $totalSisa = $totalPlafon - $totalRealisasi;
        $totalPersen = $totalPlafon > 0 ? min(100, round(($totalRealisasi / $totalPlafon) * 100, 1)) : 0;

        $viewData = compact(
            'laporanList',
            'periode',
            'totalPlafon',
            'totalRealisasi',
            'totalSisa',
            'totalPersen',
            'totalBpCount',
            'isPrint'
        );

        if ($isPrint) {
            return view('pages.keuangan.anggaran.cetak.print-seluruh-bidang', $viewData);
        }

        return view('pages.keuangan.anggaran.cetak.seluruh-bidang', $viewData);
    }

    /**
     * 2. REALISASI ANGGARAN PER BIDANG PRIANGAN
     */
    public function cetakPerBidang(Request $request)
    {
        $kodeBagian = $request->get('kode_bagian', '01');
        $periode = $request->get('periode', date('Y-m'));
        $isPrint = $request->get('cetak') == '1' || $request->get('print') == '1';

        $daftarBidang = [
            '01' => '01 - Divisi Iklan',
            '02' => '02 - Divisi Sirkulasi',
            '03' => '03 - Sekretariat Umum (Sekum & SDM)',
            '04' => '04 - Divisi Akuntansi',
            '05' => '05 - Divisi Keuangan & Kasir',
            '06' => '06 - Divisi Redaksi & Wartawan',
            '07' => '07 - Pimpinan / Management Fee',
        ];

        $namaBidang = $daftarBidang[$kodeBagian] ?? '01 - Divisi Iklan';

        $rab = AnggaranRab::where('periode', $periode)->where('kode_bagian', $kodeBagian)->first();
        $plafon = $rab ? (float) $rab->plafon_anggaran : 15000000;

        $bpList = BuktiPengeluaran::with(['pengajuanAnggaran', 'kasKeluar'])
            ->where('kode_bagian', $kodeBagian)
            ->where('tanggal_pengajuan', 'like', "{$periode}%")
            ->orderBy('tanggal_pengajuan', 'asc')
            ->get();

        $totalRealisasi = (float) $bpList->whereIn('status', ['approved', 'dicairkan_kasir'])->sum('nominal_acc');
        $totalCair = (float) $bpList->where('status', 'dicairkan_kasir')->sum('nominal_acc');
        $sisaPlafon = $plafon - $totalRealisasi;

        $viewData = compact(
            'kodeBagian',
            'namaBidang',
            'daftarBidang',
            'periode',
            'plafon',
            'bpList',
            'totalRealisasi',
            'totalCair',
            'sisaPlafon',
            'isPrint'
        );

        if ($isPrint) {
            return view('pages.keuangan.anggaran.cetak.print-per-bidang', $viewData);
        }

        return view('pages.keuangan.anggaran.cetak.per-bidang', $viewData);
    }

    /**
     * 3. REKAP BP PRIANGAN PER TANGGAL
     */
    public function cetakBpTanggal(Request $request)
    {
        $dariTanggal = $request->get('dari_tanggal', Carbon::today()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->get('sampai_tanggal', Carbon::today()->toDateString());
        $isPrint = $request->get('cetak') == '1' || $request->get('print') == '1';

        $bpList = BuktiPengeluaran::with(['pengajuanAnggaran', 'kasKeluar', 'approver', 'disburser'])
            ->whereBetween('tanggal_pengajuan', [$dariTanggal, $sampaiTanggal])
            ->orderBy('tanggal_pengajuan', 'asc')
            ->get();

        $totalPengajuan = (float) $bpList->sum('nominal_pengajuan');
        $totalAcc       = (float) $bpList->sum('nominal_acc');
        $totalCair      = (float) $bpList->where('status', 'dicairkan_kasir')->sum('nominal_acc');
        $totalPending   = (float) $bpList->where('status', '!=', 'dicairkan_kasir')->sum('nominal_acc');

        $periodeJudul = Carbon::parse($dariTanggal)->translatedFormat('d F Y') . ' s/d ' . Carbon::parse($sampaiTanggal)->translatedFormat('d F Y');

        $viewData = compact(
            'bpList',
            'dariTanggal',
            'sampaiTanggal',
            'periodeJudul',
            'totalPengajuan',
            'totalAcc',
            'totalCair',
            'totalPending',
            'isPrint'
        );

        if ($isPrint) {
            return view('pages.keuangan.anggaran.cetak.print-rekap-bp', array_merge($viewData, ['mode' => 'tanggal']));
        }

        return view('pages.keuangan.anggaran.cetak.bp-tanggal', $viewData);
    }

    /**
     * 4. REKAP BP PRIANGAN PER BULAN
     */
    public function cetakBpBulan(Request $request)
    {
        $bulan = (int) $request->get('bulan', date('m'));
        $tahun = (int) $request->get('tahun', date('Y'));
        $isPrint = $request->get('cetak') == '1' || $request->get('print') == '1';

        $bpList = BuktiPengeluaran::with(['pengajuanAnggaran', 'kasKeluar', 'approver', 'disburser'])
            ->whereYear('tanggal_pengajuan', $tahun)
            ->whereMonth('tanggal_pengajuan', $bulan)
            ->orderBy('tanggal_pengajuan', 'asc')
            ->get();

        $totalPengajuan = (float) $bpList->sum('nominal_pengajuan');
        $totalAcc       = (float) $bpList->sum('nominal_acc');
        $totalCair      = (float) $bpList->where('status', 'dicairkan_kasir')->sum('nominal_acc');
        $totalPending   = (float) $bpList->where('status', '!=', 'dicairkan_kasir')->sum('nominal_acc');

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');
        $periodeJudul = "BULAN " . strtoupper($namaBulan);

        $viewData = compact(
            'bpList',
            'bulan',
            'tahun',
            'periodeJudul',
            'totalPengajuan',
            'totalAcc',
            'totalCair',
            'totalPending',
            'isPrint'
        );

        if ($isPrint) {
            return view('pages.keuangan.anggaran.cetak.print-rekap-bp', array_merge($viewData, ['mode' => 'bulan']));
        }

        return view('pages.keuangan.anggaran.cetak.bp-bulan', $viewData);
    }

    /**
     * 5. REKAP REALISASI ANGGARAN PER BIDANG (COA)
     */
    public function cetakRekapBidangCoa(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        $isPrint = $request->get('cetak') == '1' || $request->get('print') == '1';

        $perkiraanMaster = [
            ['kode' => '6.00.01.01.01.02', 'nama' => 'Beban Operasional Iklan',              'bidang' => '01 - Iklan'],
            ['kode' => '6.00.01.00.01.04', 'nama' => 'Beban Sirkulasi & Ekspedisi',          'bidang' => '02 - Sirkulasi'],
            ['kode' => '6.00.01.00.01.04', 'nama' => 'Beban Umum & SDM / Perlengkapan RT',   'bidang' => '03 - Sekum & SDM'],
            ['kode' => '6.00.01.01.01.03', 'nama' => 'Beban Administrasi Akuntansi',         'bidang' => '04 - Akuntansi'],
            ['kode' => '6.00.01.01.01.02', 'nama' => 'Beban Keuangan & Pajak',               'bidang' => '05 - Keuangan'],
            ['kode' => '5.00.01.01.02',    'nama' => 'Beban Redaksi & Honor Wartawan',        'bidang' => '06 - Redaksi'],
            ['kode' => '6.00.01.01.00.00', 'nama' => 'Beban Operasional Pimpinan',           'bidang' => '07 - Pimpinan'],
        ];

        $rekapCoa = [];
        $grandTotal = 0;

        foreach ($perkiraanMaster as $pm) {
            $bpQ = BuktiPengeluaran::where('kode_perkiraan', $pm['kode'])
                ->where('tanggal_pengajuan', 'like', "{$periode}%")
                ->whereIn('status', ['approved', 'dicairkan_kasir']);

            $totalNominal = (float) $bpQ->sum('nominal_acc');
            $countBp      = $bpQ->count();

            $rekapCoa[] = [
                'kode'     => $pm['kode'],
                'nama'     => $pm['nama'],
                'bidang'   => $pm['bidang'],
                'total'    => $totalNominal,
                'count_bp' => $countBp,
            ];

            $grandTotal += $totalNominal;
        }

        $viewData = compact('rekapCoa', 'periode', 'grandTotal', 'isPrint');

        if ($isPrint) {
            return view('pages.keuangan.anggaran.cetak.print-rekap-coa', $viewData);
        }

        return view('pages.keuangan.anggaran.cetak.rekap-coa', $viewData);
    }

    /**
     * 6. CETAK ULANG BP PER TANGGAL
     */
    public function cetakUlangBp(Request $request)
    {
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        $bpList = BuktiPengeluaran::with(['pengajuanAnggaran', 'kasKeluar', 'approver', 'disburser'])
            ->whereDate('tanggal_pengajuan', $tanggal)
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.keuangan.anggaran.cetak.cetak-ulang-bp', compact('bpList', 'tanggal'));
    }

    /**
     * 7. REKAP REALISASI ANGGARAN PER TAHUN (12 Bulan Komparasi)
     */
    public function cetakTahunan(Request $request)
    {
        $tahun = (int) $request->get('tahun', date('Y'));
        $isPrint = $request->get('cetak') == '1' || $request->get('print') == '1';

        $bidangMaster = [
            '01' => '01 - Divisi Iklan',
            '02' => '02 - Divisi Sirkulasi',
            '03' => '03 - Sekum & SDM',
            '04' => '04 - Akuntansi',
            '05' => '05 - Keuangan',
            '06' => '06 - Redaksi',
            '07' => '07 - Pimpinan',
        ];

        $matriks = [];
        $totalPerBulan = array_fill(1, 12, 0);
        $grandTotalTahun = 0;

        foreach ($bidangMaster as $kode => $nama) {
            $bulanData = [];
            $totalBaris = 0;

            for ($b = 1; $b <= 12; $b++) {
                $nominal = (float) BuktiPengeluaran::where('kode_bagian', $kode)
                    ->whereYear('tanggal_pengajuan', $tahun)
                    ->whereMonth('tanggal_pengajuan', $b)
                    ->whereIn('status', ['approved', 'dicairkan_kasir'])
                    ->sum('nominal_acc');

                $bulanData[$b] = $nominal;
                $totalBaris += $nominal;
                $totalPerBulan[$b] += $nominal;
            }

            $matriks[] = [
                'kode'       => $kode,
                'nama'       => $nama,
                'bulan'      => $bulanData,
                'total'      => $totalBaris,
            ];

            $grandTotalTahun += $totalBaris;
        }

        $viewData = compact('matriks', 'tahun', 'totalPerBulan', 'grandTotalTahun', 'isPrint');

        if ($isPrint) {
            return view('pages.keuangan.anggaran.cetak.print-tahunan', $viewData);
        }

        return view('pages.keuangan.anggaran.cetak.tahunan', $viewData);
    }
}


