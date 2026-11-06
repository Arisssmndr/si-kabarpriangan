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

        // Daftar 21 Bidang Resmi Kabar Priangan (Sesuai Menu DOS TABEL > BIDANG 01 s/d 21)
        $daftarDivisi = BuktiPengeluaran::getDaftarBidang();

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
        $daftarDivisi = BuktiPengeluaran::getDaftarBidang();
        $master21 = BuktiPengeluaran::getMaster21Bidang();

        $periodeIni = date('Y-m');
        $allCoaByBidang = [];
        $plafonByBidang = [];

        foreach ($master21 as $kode => $info) {
            $coaRaw = BuktiPengeluaran::getCoaPerBidang($kode);

            // Plafon & Realisasi Bidang
            $rabBidang = AnggaranRab::where('periode', $periodeIni)->where('kode_bagian', $kode)->first();
            $plafonVal = $rabBidang ? (float) $rabBidang->plafon_anggaran : (float) $info['default_plafon'];
            $realisasiVal = (float) BuktiPengeluaran::where('kode_bagian', $kode)
                ->where('tanggal_pengajuan', 'like', "{$periodeIni}%")
                ->whereIn('status', ['approved', 'dicairkan_kasir'])
                ->sum('nominal_acc');
            $sisaVal = $plafonVal - $realisasiVal;

            $plafonByBidang[$kode] = [
                'plafon'    => $plafonVal,
                'realisasi' => $realisasiVal,
                'sisa'      => $sisaVal,
            ];
            $plafonByBidang[$info['slug']] = $plafonByBidang[$kode];

            $coaListParsed = [];
            foreach ($coaRaw as $c) {
                $realisasiCoa = (float) BuktiPengeluaran::where('kode_bagian', $kode)
                    ->where('kode_perkiraan', $c['kode'])
                    ->where('tanggal_pengajuan', 'like', "{$periodeIni}%")
                    ->whereIn('status', ['approved', 'dicairkan_kasir'])
                    ->sum('nominal_acc');

                $alokasiCoa = (float) ($c['alokasi'] ?? 0);
                $sisaCoa = $alokasiCoa - $realisasiCoa;

                $coaListParsed[] = [
                    'kode'      => $c['kode'],
                    'nama'      => $c['nama'],
                    'alokasi'   => $alokasiCoa,
                    'realisasi' => $realisasiCoa,
                    'sisa'      => $sisaCoa,
                ];
            }

            $allCoaByBidang[$kode] = $coaListParsed;
            $allCoaByBidang[$info['slug']] = $coaListParsed;
        }

        $bankList = BuktiPengeluaran::getDaftarRekeningBank();

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
        $selectedDivisi = request('divisi', '');

        return view('pages.keuangan.anggaran.create', compact(
            'daftarDivisi',
            'master21',
            'allCoaByBidang',
            'plafonByBidang',
            'bankList',
            'noPengajuanSuggested',
            'noBpSuggested',
            'selectedDivisi'
        ));
    }

    /**
     * Simpan Pengajuan Anggaran Baru & Terbitkan BP Priangan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_pengajuan'      => 'required|string|unique:pengajuan_anggaran,no_pengajuan',
            'no_bp'             => 'nullable|string|unique:bukti_pengeluarans,no_bp',
            'divisi'            => 'required|string',
            'kode_perkiraan'    => 'nullable|string',
            'nama_perkiraan'    => 'nullable|string',
            'tanggal_pengajuan' => 'required|date',
            'nominal_diajukan'  => 'required|numeric|min:1000',
            'keterangan'        => 'required|string|max:1000',
            'pemohon'           => 'nullable|string|max:255',
            'metode_pencairan'  => 'nullable|string',
            'bank_penerima'     => 'nullable|string',
            'no_rekening'       => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $userId = Auth::id() ?? 1;
            $userName = Auth::user()->name ?? 'Staf Anggaran';

            // Pemetaan Kode Bagian Sesuai Standar DOS ANGGARAN.EXE (01 s/d 21)
            $master21 = BuktiPengeluaran::getMaster21Bidang();
            $kodeBagian = '01';
            $namaDivisi = 'Iklan';
            $divisiSlug = 'iklan';

            if (isset($master21[$request->divisi])) {
                $kodeBagian = $request->divisi;
                $namaDivisi = $master21[$kodeBagian]['nama'];
                $divisiSlug = $master21[$kodeBagian]['slug'];
            } else {
                foreach ($master21 as $k => $info) {
                    if ($info['slug'] === $request->divisi) {
                        $kodeBagian = $k;
                        $namaDivisi = $info['nama'];
                        $divisiSlug = $info['slug'];
                        break;
                    }
                }
            }

            // Tentukan Kode Perkiraan & Nama Perkiraan spesifik
            $kodePerkiraan = $request->input('kode_perkiraan');
            $namaPerkiraan = $request->input('nama_perkiraan');

            if (empty($kodePerkiraan)) {
                $kodePerkiraan = $master21[$kodeBagian]['pos_beban'] ?? '6.00.01.01.01.02';
                $namaPerkiraan = $master21[$kodeBagian]['nama_pos'] ?? "Beban Operasional {$namaDivisi}";
            } elseif (empty($namaPerkiraan)) {
                $coaList = BuktiPengeluaran::getCoaPerBidang($kodeBagian);
                foreach ($coaList as $c) {
                    if ($c['kode'] === $kodePerkiraan) {
                        $namaPerkiraan = $c['nama'];
                        break;
                    }
                }
                $namaPerkiraan = $namaPerkiraan ?: "Pos Perkiraan {$kodePerkiraan}";
            }

            // Catatan metode pencairan
            $catatanPencairan = 'Pendaftaran pengajuan anggaran baru via sistem terpadu';
            if ($request->input('metode_pencairan') === 'transfer') {
                $catatanPencairan .= " | Pencairan Transfer Bank {$request->input('bank_penerima')} No. Rek {$request->input('no_rekening')} a/n {$request->input('pemohon')}";
            } else {
                $catatanPencairan .= " | Pencairan via Kasir (Tunai)";
            }

            // Status Persetujuan: Langsung ACC (terbit Register PR) atau Pending
            $statusPersetujuan = $request->input('status_persetujuan', 'approved');
            $isApproved = ($statusPersetujuan === 'approved');
            $noRegister = $isApproved ? BuktiPengeluaran::generateNoRegister() : null;

            // Buat record pengajuan anggaran
            $pengajuan = PengajuanAnggaran::create([
                'no_pengajuan'      => $request->no_pengajuan,
                'divisi'            => $divisiSlug,
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'nominal_diajukan'  => $request->nominal_diajukan,
                'nominal_disetujui' => $isApproved ? $request->nominal_diajukan : null,
                'status'            => $isApproved ? 'approved' : 'pending',
                'keterangan'        => $request->keterangan,
                'created_by'        => $userId,
                'approved_by'       => $isApproved ? $userId : null,
            ]);

            // Terbitkan Dokumen BP Priangan Terkoneksi (Bisa nomor otomatis / input manual)
            $noBp = $request->filled('no_bp') ? trim($request->no_bp) : BuktiPengeluaran::generateNoBp();
            BuktiPengeluaran::create([
                'no_bp'                 => $noBp,
                'no_register_anggaran'  => $noRegister,
                'pengajuan_anggaran_id' => $pengajuan->id,
                'divisi'                => $namaDivisi,
                'kode_bagian'           => $kodeBagian,
                'kode_perkiraan'        => $kodePerkiraan,
                'nama_perkiraan'        => $namaPerkiraan,
                'tanggal_pengajuan'     => $pengajuan->tanggal_pengajuan,
                'pemohon'               => $request->input('pemohon', $userName),
                'keperluan'             => $pengajuan->keterangan,
                'nominal_pengajuan'     => $pengajuan->nominal_diajukan,
                'nominal_acc'           => $pengajuan->nominal_diajukan,
                'status'                => $isApproved ? 'approved' : 'pending',
                'approved_by'           => $isApproved ? $userId : null,
                'catatan'               => $catatanPencairan,
            ]);

            $statusMsg = $isApproved ? "langsung disetujui (Register PR: {$noRegister}) dan siap dicairkan ke Kas" : "berhasil didaftarkan";
            return redirect()->route('keuangan.anggaran.index')
                ->with('success', "Pengajuan Anggaran {$pengajuan->no_pengajuan} & Dokumen BP {$noBp} ({$kodePerkiraan} - {$namaPerkiraan}) {$statusMsg}.");
        });
    }

    /**
     * Setujui (Approve) Pengajuan Anggaran & Terbitkan Nomor Register Anggaran (PR).
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.'
                ], 401);
            }
            return redirect()->route('login');
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
        if (!$user) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.'
                ], 401);
            }
            return redirect()->route('login');
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
     * Tabel Master: BIDANG (Kode Bagian 01 - 21 Sesuai Standar DOS ANGGARAN.EXE)
     */
    public function tabelBidang(Request $request)
    {
        $search = $request->get('q');
        
        $bidangList = array_values(BuktiPengeluaran::getMaster21Bidang());

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

        // Daftar Akun Bagan Perkiraan (COA) Standar PT. Berkah Pikiran Rakyat (Sesuai Cetak Kertas Kontinu)
        $perkiraanList = [
            // --- BIDANG 01: IKLAN (Sesuai Lembar Cetak Asli Video) ---
            [
                'kode'        => '1.00.04.01.01.01',
                'nama'        => 'IKLAN SK.PRI',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Piutang Iklan',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Piutang order pemasangan iklan surat kabar Priangan cetak.',
            ],
            [
                'kode'        => '1.00.04.01.01.02',
                'nama'        => 'IKLAN ONLINE',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Piutang Iklan',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Piutang pemasangan banner & advetorial portal kabar-priangan.com.',
            ],
            [
                'kode'        => '2.00.01.01.01.01',
                'nama'        => 'HUTANG IKLAN',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Hutang Titipan',
                'tipe'        => 'KREDIT',
                'keterangan'  => 'Titipan uang muka order iklan tempo belum tayang.',
            ],
            [
                'kode'        => '2.00.01.01.01.02',
                'nama'        => 'HUTANG IKLAN ONLINE',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Hutang Titipan',
                'tipe'        => 'KREDIT',
                'keterangan'  => 'Titipan uang muka paket iklan digital.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.00.00',
                'nama'        => 'Gaji / Honor tetap',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Beban SDM',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Gaji pokok staf divisi iklan.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.00.01',
                'nama'        => 'Honor pegawai non struktural',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Beban SDM',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Honor sales lapangan / freelance iklan.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.00.02.00',
                'nama'        => 'Tunj. Uang Makan',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Tunjangan',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Tunjangan uang makan staf iklan.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.00.02.01',
                'nama'        => 'Tunj. Transportasi',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Tunjangan',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Tunjangan transportasi operasional sales.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.00.02.02',
                'nama'        => 'Tunj. Kesehatan',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Tunjangan',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Bantuan biaya kesehatan staf.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.00.02.03',
                'nama'        => 'Piket / Kelebihan jam kerja',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Lembur',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Uang lembur deadline closing edisi cetak.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.00.02.04',
                'nama'        => 'Tunjangan Hari Raya',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Tunjangan',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Alokasi THR divisi iklan.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.00.02.05',
                'nama'        => 'TUNJANGAN JABATAN',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Tunjangan',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Tunjangan manajerial divisi iklan.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.00.02.06',
                'nama'        => 'Tunjangan Natura',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Tunjangan',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Tunjangan sembako / natura kerja.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.01.00.00',
                'nama'        => 'Uang Saku Dinas',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Perjalanan Dinas',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Uang harian tugas luar kota canvassing biro.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.01.00.01',
                'nama'        => 'BBM / Transportasi Dinas',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Perjalanan Dinas',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'BBM kendaraan operasional sales iklan.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.01.00.02',
                'nama'        => 'Hotel / Akomodasi Dinas',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Perjalanan Dinas',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Penginapan sales dinas luar kota.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.01.01.02',
                'nama'        => 'Service / Suku Cadang / Oli',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Pemeliharaan',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Perawatan armada kendaraan operasional.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.01.02.00',
                'nama'        => 'Perlengkapan kantor / ATK',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Perlengkapan',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Formulir order iklan, kertas faktur continuous form.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.01.02.02',
                'nama'        => 'Jamuan Rapat / Relasi',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Entertainment',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Jamuan rapat klien pasang iklan & biro agency.',
            ],
            [
                'kode'        => '6.00.01.00.01.02.01.02.03',
                'nama'        => 'Telepon / Handphone / Pager',
                'bidang'      => '01 - Iklan',
                'kategori'    => 'Komunikasi',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Pulsa & kuota koordinasi sales iklan.',
            ],

            // --- BIDANG LAINNYA SESUAI 21 BIDANG RESMI ---
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
                'bidang'      => '03 - Umum & SDM',
                'kategori'    => 'Beban Umum & Administrasi',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Listrik kantor Bumi Resik, tagihan air PDAM, internet redaksi, perlengkapan ATK, servis AC.',
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
                'kode'        => '6.00.01.01.01.02',
                'nama'        => 'Beban Keuangan & Pajak',
                'bidang'      => '05 - Keuangan',
                'kategori'    => 'Beban Administrasi Keuangan',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Pajak operasional, bea materai kuitansi, biaya transaksi transfer bank & perbankan.',
            ],
            [
                'kode'        => '5.00.01.01.02',
                'nama'        => 'Beban Redaksi & Honor Wartawan',
                'bidang'      => '06 - Redaksi',
                'kategori'    => 'Beban Pokok Produksi',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Honor kontributor, biaya operasional liputan wartawan peliputan daerah & percetakan plat.',
            ],
            [
                'kode'        => '6.00.01.01.00.00',
                'nama'        => 'Beban Operasional Pimpinan',
                'bidang'      => '07 - Pimpinan',
                'kategori'    => 'Beban Manajemen Eksekutif',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Akomodasi dinas pimpinan luar kota, rapat pemegang saham, dan alokasi management fee.',
            ],
            [
                'kode'        => '5.00.01.01.03',
                'nama'        => 'Beban Teknik Redaksi & Layout',
                'bidang'      => '08 - Teknik Redaksi',
                'kategori'    => 'Beban Pracetak',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Setting layout koran, tata artistik, pracetak CTP (Computer to Plate).',
            ],
            [
                'kode'        => '5.00.01.02.01',
                'nama'        => 'Beban Bahan Baku & Cetak Penerbitan',
                'bidang'      => '09 - Bank Penerbitan',
                'kategori'    => 'Bahan Baku & Cetak',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Pengadaan kertas koran (newsprint), tinta cetak web offset, plat cetak, dan ongkos cetak.',
            ],
            [
                'kode'        => '6.00.01.01.01.05',
                'nama'        => 'Beban Pemasaran & Promosi Event',
                'bidang'      => '10 - Pemasaran',
                'kategori'    => 'Beban Pemasaran',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Promosi brand koran, event kemitraan, expo, sponsorship Kabar Priangan.',
            ],
            [
                'kode'        => '6.00.01.01.99.00',
                'nama'        => 'Beban Lain-Lain Usaha',
                'bidang'      => '11 - Beban Lain-Lain Usaha',
                'kategori'    => 'Beban Lain-Lain',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Beban tak terduga operasional yang tidak tercakup dalam pos divisi reguler.',
            ],
            [
                'kode'        => '2.00.01.02.01.00',
                'nama'        => 'Penyesuaian & Koreksi Kwitansi',
                'bidang'      => '12 - Koreksi Kwitansi',
                'kategori'    => 'Penyesuaian',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Penyesuaian selisih pencatatan kwitansi, pembatalan/void kuitansi kasir.',
            ],
            [
                'kode'        => '2.00.02.01.01.00',
                'nama'        => 'Kewajiban / Hutang Pajak',
                'bidang'      => '13 - Hutang Pajak',
                'kategori'    => 'Kewajiban Pajak',
                'tipe'        => 'KREDIT',
                'keterangan'  => 'Penyetoran kewajiban pajak perusahaan (PPh 21, PPh 23, PPN).',
            ],
            [
                'kode'        => '2.00.03.01.01.00',
                'nama'        => 'Hutang Antar Perusahaan Afiliasi',
                'bidang'      => '14 - Hutang Afiliasi',
                'kategori'    => 'Hutang Afiliasi',
                'tipe'        => 'KREDIT',
                'keterangan'  => 'Transaksi rekening koran antar entitas Pikiran Rakyat Group.',
            ],
            [
                'kode'        => '6.00.01.00.01.01',
                'nama'        => 'Beban Administrasi Sekretariat Perusahaan',
                'bidang'      => '15 - Sekretariat',
                'kategori'    => 'Beban Administrasi',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Administrasi surat menyurat, legalitas hukum, notaris, arsip pers.',
            ],
            [
                'kode'        => '6.00.01.01.01.08',
                'nama'        => 'Beban Operasional Portal Online & IT',
                'bidang'      => '16 - Online',
                'kategori'    => 'Media Digital',
                'tipe'        => 'DEBIT',
                'keterangan'  => 'Server cloud, hosting, domain portal web kabarpriangan.com, SEO & medsos.',
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

        $bankList = BuktiPengeluaran::getDaftarRekeningBank();

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
        $selectedBidang = $request->get('kode_bagian', '01');
        $activeTab = $request->get('tab', 'perkiraan');

        // Master 21 Bidang Priangan (01 s/d 21)
        $bidangMaster = BuktiPengeluaran::getMaster21Bidang();

        $selectedMaster = $bidangMaster[$selectedBidang] ?? ($bidangMaster['01'] ?? [
            'nama' => 'Iklan',
            'nama_lengkap' => '01 - Divisi Iklan',
            'slug' => 'iklan',
            'default_plafon' => 15000000,
        ]);

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
                'nama'           => $info['nama_lengkap'],
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

        // Rincian Akun Perkiraan (COA) untuk Bidang Terpilih: Kode Perkiraan, Perkiraan, RAB, Realisasi, Sisa Rab
        $coaListRaw = BuktiPengeluaran::getCoaPerBidang($selectedBidang);
        $bpPerkiraan = BuktiPengeluaran::where('kode_bagian', $selectedBidang)
            ->where('tanggal_pengajuan', 'like', "{$periode}%")
            ->whereIn('status', ['approved', 'dicairkan_kasir'])
            ->get();

        $coaItems = [];
        $totalRabCoa = 0;
        $totalRealisasiCoa = 0;

        foreach ($coaListRaw as $c) {
            $realisasiCoa = (float) $bpPerkiraan->where('kode_perkiraan', $c['kode'])->sum('nominal_acc');
            $rabCoa = (float) ($c['alokasi'] ?? 0);
            $sisaRabCoa = $rabCoa - $realisasiCoa;
            $persenCoa = $rabCoa > 0 ? min(100, round(($realisasiCoa / $rabCoa) * 100, 1)) : 0;

            $coaItems[] = [
                'kode_perkiraan' => $c['kode'],
                'perkiraan'      => $c['nama'],
                'rab'            => $rabCoa,
                'realisasi'      => $realisasiCoa,
                'sisa_rab'       => $sisaRabCoa,
                'persen'         => $persenCoa,
            ];

            $totalRabCoa += $rabCoa;
            $totalRealisasiCoa += $realisasiCoa;
        }

        $totalSisaRabCoa = $totalRabCoa - $totalRealisasiCoa;
        $totalPersenCoa = $totalRabCoa > 0 ? min(100, round(($totalRealisasiCoa / $totalRabCoa) * 100, 1)) : 0;

        return view('pages.keuangan.anggaran.rab', compact(
            'rabItems',
            'bidangMaster',
            'selectedBidang',
            'selectedMaster',
            'activeTab',
            'coaItems',
            'totalRabCoa',
            'totalRealisasiCoa',
            'totalSisaRabCoa',
            'totalPersenCoa',
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

        $master21 = BuktiPengeluaran::getMaster21Bidang();
        $divisi = $master21[$request->kode_bagian]['slug'] ?? 'umum';

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

        $bidangMaster = BuktiPengeluaran::getMaster21Bidang();

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
                'nama'       => $info['nama_lengkap'],
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

        $daftarBidang = BuktiPengeluaran::getDaftarBidang();
        $masterInfo = BuktiPengeluaran::getMaster21Bidang()[$kodeBagian] ?? null;
        $namaBidang = $masterInfo['nama'] ?? ($daftarBidang[$kodeBagian] ?? 'Iklan');
        $namaBidangLengkap = $masterInfo['nama_lengkap'] ?? "{$kodeBagian} - {$namaBidang}";
        $coaList = BuktiPengeluaran::getCoaPerBidang($kodeBagian);

        $rab = AnggaranRab::where('periode', $periode)->where('kode_bagian', $kodeBagian)->first();
        $plafon = $rab ? (float) $rab->plafon_anggaran : ($masterInfo['default_plafon'] ?? 15000000);

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
            'namaBidangLengkap',
            'daftarBidang',
            'coaList',
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

        // Master 21 Bidang Priangan (01 s/d 21)
        $bidangMaster = BuktiPengeluaran::getDaftarBidang();

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


