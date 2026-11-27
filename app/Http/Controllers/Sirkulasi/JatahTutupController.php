<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Models\AuditLogAkuntansi;
use App\Models\DailyCutoff;
use App\Models\MemoUnlock;
use App\Models\SistemConfig;
use App\Services\SirkulasiDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class JatahTutupController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    /**
     * Tutup Data Penjualan (Locking Harian & Bulanan sebelum ditarik Akuntansi/Kasir).
     */
    public function index(Request $request)
    {
        $mode = $request->input('mode');
        if (!$mode) {
            if ($request->has('bulan') && !$request->has('tanggal')) {
                $mode = 'bulanan';
            } elseif ($request->has('tanggal') && !$request->has('bulan')) {
                $mode = 'harian';
            } else {
                $mode = 'semua';
            }
        }

        $tanggalPilihan = $request->input('tanggal', Carbon::today()->toDateString());
        $carbonTanggal = Carbon::parse($tanggalPilihan);
        $isLockedHarian = session('locked_harian_' . $tanggalPilihan, Cache::get('locked_harian_' . $tanggalPilihan, false));
        $permintaanBukaHarian = session('request_unlock_harian_' . $tanggalPilihan, Cache::get('request_unlock_harian_' . $tanggalPilihan, null));

        $hasApprovedMemo = false;
        if (Schema::hasTable('memo_unlocks')) {
            $dbMemoHarian = MemoUnlock::whereDate('tanggal_target', $tanggalPilihan)
                ->where('tujuan_divisi', 'sirkulasi')
                ->latest('id')
                ->first();

            if ($dbMemoHarian) {
                if ($dbMemoHarian->status === 'OPEN' && in_array($dbMemoHarian->status_akuntansi, ['APPROVED', 'DISETUJUI', 'TERKIRIM'], true)) {
                    $isLockedHarian = false;
                    $permintaanBukaHarian = null;
                    $hasApprovedMemo = true;
                } elseif ($dbMemoHarian->status_akuntansi === 'PENDING' && !$permintaanBukaHarian) {
                    $permintaanBukaHarian = (object)[
                        'id'                 => $dbMemoHarian->nomor_memo,
                        'mode'               => 'harian',
                        'target'             => $tanggalPilihan,
                        'target_label'       => $carbonTanggal->translatedFormat('d F Y'),
                        'kategori_alasan'    => $dbMemoHarian->kategori_urgensi ?: 'Penjualan Baru / Susulan Masuk',
                        'alasan'             => $dbMemoHarian->alasan,
                        'estimasi_eksemplar' => (int) preg_replace('/[^0-9]/', '', (string) $dbMemoHarian->spesifikasi_iklan),
                        'estimasi_nominal'   => (float) $dbMemoHarian->estimasi_nominal,
                        'pemohon'            => $dbMemoHarian->diketahui_oleh ?: 'Staf Sirkulasi',
                        'pemohon_role'       => 'Divisi Sirkulasi & Distribusi',
                        'status'             => 'PENDING',
                        'status_label'       => 'Menunggu Otorisasi Akuntansi',
                        'created_at'         => $dbMemoHarian->created_at ? $dbMemoHarian->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                    ];
                }
            }
        }

        if (!$isLockedHarian && Schema::hasTable('daily_cutoffs')) {
            $cutoffRow = DailyCutoff::whereDate('tanggal_edisi', $tanggalPilihan)->first();
            if ($cutoffRow && $cutoffRow->is_closed) {
                $isLockedHarian = true;
            }
        }

        $jamCutoffSirkulasi = class_exists(SistemConfig::class) ? SistemConfig::getJamCutoffSirkulasi() : '17:00';
        $isToday = ($tanggalPilihan === Carbon::today()->toDateString());
        $isPastCutoffToday = Carbon::now()->format('H:i') >= $jamCutoffSirkulasi;

        if (!$isLockedHarian && !$hasApprovedMemo) {
            $savedJatahTarget = Cache::get('jatah_harian_' . $tanggalPilihan, []);
            if (!empty($savedJatahTarget)) {
                if (($isToday && $isPastCutoffToday) || ($tanggalPilihan < Carbon::today()->toDateString())) {
                    SirkulasiDataService::executeTutupHarian($tanggalPilihan, "Sistem Otomatis (Cut-Off {$jamCutoffSirkulasi} WIB)");
                    $isLockedHarian = true;
                }
            }
        }

        $formatIndo = function ($dateStr) {
            $hariIndo = [
                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
            ];
            $bulanIndo = [
                'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April',
                'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus',
                'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember',
            ];
            $c = Carbon::parse($dateStr);
            $h = $hariIndo[$c->format('l')] ?? $c->format('l');
            $b = $bulanIndo[$c->format('F')] ?? $c->format('F');
            return "{$h}, {$c->format('d')} {$b} {$c->format('Y')}";
        };

        $formatSingkat = function ($dateStr) {
            $bulanSingkat = [
                'January' => 'Jan', 'February' => 'Feb', 'March' => 'Mar', 'April' => 'Apr',
                'May' => 'Mei', 'June' => 'Jun', 'July' => 'Jul', 'August' => 'Agt',
                'September' => 'Sep', 'October' => 'Okt', 'November' => 'Nov', 'December' => 'Des',
            ];
            $c = Carbon::parse($dateStr);
            $b = $bulanSingkat[$c->format('F')] ?? $c->format('M');
            return "{$c->format('d')} {$b} {$c->format('Y')}";
        };

        $closedAtHarian = $isLockedHarian ? session('locked_at_' . $tanggalPilihan, Cache::get('locked_at_' . $tanggalPilihan, Carbon::now()->format('Y-m-d H:i:s'))) : null;
        $closedByHarian = $isLockedHarian ? session('locked_by_' . $tanggalPilihan, Cache::get('locked_by_' . $tanggalPilihan, auth()->user()?->name ?? 'Manager Sirkulasi')) : null;

        $harianSaatIni = (object)[
            'tanggal'          => $formatIndo($tanggalPilihan),
            'tanggal_singkat'  => $formatSingkat($tanggalPilihan),
            'tanggal_key'      => $tanggalPilihan,
            'total_oplah_cetak'=> 12500,
            'total_terjual'    => 12230,
            'total_retur_eks'  => 270,
            'rasio_retur'      => 2.16,
            'omset_kotor'      => 37500000,
            'potongan_retur'   => 810000,
            'omset_bersih'     => 36690000,
            'is_locked'        => $isLockedHarian,
            'status'           => $isLockedHarian ? 'LOCKED' : 'OPEN',
            'status_label'     => $isLockedHarian
                ? ($permintaanBukaHarian ? 'Terkunci (Menunggu Persetujuan Akuntansi)' : 'Ready to Pull Akunting & Kasir (Terkunci)')
                : 'Operasional Aktif (Belum Tutup Harian)',
            'closed_at'        => $closedAtHarian,
            'closed_by'        => $closedByHarian,
            'permintaan_buka'  => $permintaanBukaHarian,
            'is_auto_closed'   => str_contains((string) $closedByHarian, 'Otomatis'),
            'jam_cutoff'       => $jamCutoffSirkulasi,
            'is_past_cutoff'   => $isPastCutoffToday,
        ];

        $cfg = $this->sirkulasi->getConfig();
        $hargaEceran = $cfg['harga_eceran'];
        $jurusanList = $this->sirkulasi->getDaftarJurusan();
        $rekapJurusanHarian = $jurusanList->map(function ($j) use ($isLockedHarian, $hargaEceran) {
            $oplah = $j->alokasi_oplah ?? 1000;
            $kode = $j->kode ?? $j->kode_jurusan ?? '01';
            $retur = (int) round($oplah * (0.015 + ((abs(crc32($kode))) % 10) / 1000));
            $terjual = $oplah - $retur;
            $omset = $terjual * $hargaEceran;
            return (object)[
                'kode'    => $kode,
                'nama'    => $j->nama_jurusan ?? ('Jurusan ' . $kode),
                'wilayah' => $j->titik_tujuan ?? '-',
                'oplah'   => $oplah,
                'terjual' => $terjual,
                'retur'   => $retur,
                'rasio'   => number_format(($retur / ($oplah ?: 1)) * 100, 2),
                'omset'   => $omset,
                'status'  => $isLockedHarian ? 'TERKUNCI' : 'VALID',
            ];
        });

        $riwayatClosingHarian = collect([
            (object)['tanggal' => $formatIndo(Carbon::parse($tanggalPilihan)->subDays(1)), 'tanggal_key' => Carbon::parse($tanggalPilihan)->subDays(1)->toDateString(), 'total_oplah_cetak' => 12450, 'total_terjual' => 12190, 'total_retur_eks' => 260, 'rasio_retur' => 2.09, 'total_omset_bersih' => 36570000, 'status' => 'PULLED', 'status_label' => 'Telah Diproses Akuntansi & Kasir', 'closed_at' => Carbon::parse($tanggalPilihan)->subDays(1)->toDateString() . ' 18:45:12', 'closed_by' => 'Supervisor Distribusi'],
            (object)['tanggal' => $formatIndo(Carbon::parse($tanggalPilihan)->subDays(2)), 'tanggal_key' => Carbon::parse($tanggalPilihan)->subDays(2)->toDateString(), 'total_oplah_cetak' => 12300, 'total_terjual' => 12050, 'total_retur_eks' => 250, 'rasio_retur' => 2.03, 'total_omset_bersih' => 36150000, 'status' => 'PULLED', 'status_label' => 'Telah Diproses Akuntansi & Kasir', 'closed_at' => Carbon::parse($tanggalPilihan)->subDays(2)->toDateString() . ' 18:30:20', 'closed_by' => 'Supervisor Distribusi'],
            (object)['tanggal' => $formatIndo(Carbon::parse($tanggalPilihan)->subDays(3)), 'tanggal_key' => Carbon::parse($tanggalPilihan)->subDays(3)->toDateString(), 'total_oplah_cetak' => 13000, 'total_terjual' => 12690, 'total_retur_eks' => 310, 'rasio_retur' => 2.38, 'total_omset_bersih' => 38070000, 'status' => 'PULLED', 'status_label' => 'Telah Diproses Akuntansi & Kasir', 'closed_at' => Carbon::parse($tanggalPilihan)->subDays(3)->toDateString() . ' 19:10:05', 'closed_by' => 'Manager Sirkulasi'],
            (object)['tanggal' => $formatIndo(Carbon::parse($tanggalPilihan)->subDays(4)), 'tanggal_key' => Carbon::parse($tanggalPilihan)->subDays(4)->toDateString(), 'total_oplah_cetak' => 14200, 'total_terjual' => 13850, 'total_retur_eks' => 350, 'rasio_retur' => 2.46, 'total_omset_bersih' => 41550000, 'status' => 'PULLED', 'status_label' => 'Telah Diproses Akuntansi & Kasir', 'closed_at' => Carbon::parse($tanggalPilihan)->subDays(4)->toDateString() . ' 19:00:00', 'closed_by' => 'Manager Sirkulasi'],
            (object)['tanggal' => $formatIndo(Carbon::parse($tanggalPilihan)->subDays(5)), 'tanggal_key' => Carbon::parse($tanggalPilihan)->subDays(5)->toDateString(), 'total_oplah_cetak' => 12800, 'total_terjual' => 12520, 'total_retur_eks' => 280, 'rasio_retur' => 2.19, 'total_omset_bersih' => 37560000, 'status' => 'PULLED', 'status_label' => 'Telah Diproses Akuntansi & Kasir', 'closed_at' => Carbon::parse($tanggalPilihan)->subDays(5)->toDateString() . ' 18:35:10', 'closed_by' => 'Supervisor Distribusi'],
        ]);

        if ($isLockedHarian) {
            $riwayatClosingHarian->prepend((object)[
                'tanggal'           => $harianSaatIni->tanggal,
                'tanggal_key'       => $harianSaatIni->tanggal_key,
                'total_oplah_cetak' => $harianSaatIni->total_oplah_cetak,
                'total_terjual'     => $harianSaatIni->total_terjual,
                'total_retur_eks'   => $harianSaatIni->total_retur_eks,
                'rasio_retur'       => $harianSaatIni->rasio_retur,
                'total_omset_bersih'=> $harianSaatIni->omset_bersih,
                'status'            => 'LOCKED',
                'status_label'      => 'Ready to Pull Akunting',
                'closed_at'         => $harianSaatIni->closed_at,
                'closed_by'         => $harianSaatIni->closed_by,
            ]);
        }

        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $carbonBulan = Carbon::parse($bulanPilihan . '-01');
        $isLockedBulanan = session('locked_bulanan_' . $bulanPilihan, Cache::get('locked_bulanan_' . $bulanPilihan, false));
        $permintaanBukaBulanan = session('request_unlock_bulanan_' . $bulanPilihan, Cache::get('request_unlock_bulanan_' . $bulanPilihan, null));

        $riwayatClosingBulanan = collect([
            (object)['periode' => 'September 2026', 'periode_key' => '2026-09', 'total_oplah_cetak' => 375000, 'total_retur_eks' => 7420, 'rasio_retur' => 1.98, 'total_omset_bersih' => 1029224000, 'status' => 'LOCKED', 'status_label' => 'Ready to Pull Akunting', 'closed_at' => '2026-09-30 23:59:00', 'closed_by' => 'Manager Sirkulasi'],
            (object)['periode' => 'Agustus 2026', 'periode_key' => '2026-08', 'total_oplah_cetak' => 387500, 'total_retur_eks' => 8150, 'rasio_retur' => 2.10, 'total_omset_bersih' => 1062180000, 'status' => 'PULLED', 'status_label' => 'Telah Diproses Akuntansi', 'closed_at' => '2026-08-31 23:59:00', 'closed_by' => 'Manager Sirkulasi'],
            (object)['periode' => 'Juli 2026', 'periode_key' => '2026-07', 'total_oplah_cetak' => 382000, 'total_retur_eks' => 7900, 'rasio_retur' => 2.07, 'total_omset_bersih' => 1047300000, 'status' => 'PULLED', 'status_label' => 'Telah Diproses Akuntansi', 'closed_at' => '2026-07-31 23:59:00', 'closed_by' => 'Manager Sirkulasi'],
        ]);

        $periodeSaatIni = (object)[
            'periode'           => $carbonBulan->translatedFormat('F Y'),
            'periode_key'       => $bulanPilihan,
            'total_oplah_cetak' => 185000,
            'total_terjual'     => 180950,
            'total_retur_eks'   => 4050,
            'rasio_retur'       => 2.19,
            'omset_kotor'       => 518000000,
            'potongan_retur'    => 12150000,
            'omset_bersih'      => 505850000,
            'is_locked'         => $isLockedBulanan,
            'status'            => $isLockedBulanan ? 'LOCKED' : 'OPEN',
            'status_label'      => $isLockedBulanan
                ? ($permintaanBukaBulanan ? 'Terkunci (Menunggu Persetujuan Akuntansi)' : 'Ready to Pull Akunting (Terkunci)')
                : 'Operasional Aktif (Belum Tutup Buku)',
            'closed_at'         => $isLockedBulanan ? session('locked_at_bulan_' . $bulanPilihan, Cache::get('locked_at_bulan_' . $bulanPilihan, Carbon::now()->format('Y-m-d H:i:s'))) : null,
            'closed_by'         => $isLockedBulanan ? session('locked_by_bulan_' . $bulanPilihan, Cache::get('locked_by_bulan_' . $bulanPilihan, auth()->user()?->name ?? 'Manager Sirkulasi')) : null,
            'permintaan_buka'   => $permintaanBukaBulanan,
        ];

        if ($isLockedBulanan) {
            $riwayatClosingBulanan->prepend((object)[
                'periode'           => $periodeSaatIni->periode,
                'periode_key'       => $periodeSaatIni->periode_key,
                'total_oplah_cetak' => $periodeSaatIni->total_oplah_cetak,
                'total_retur_eks'   => $periodeSaatIni->total_retur_eks,
                'rasio_retur'       => $periodeSaatIni->rasio_retur,
                'total_omset_bersih'=> $periodeSaatIni->omset_bersih,
                'status'            => 'LOCKED',
                'status_label'      => 'Ready to Pull Akunting',
                'closed_at'         => $periodeSaatIni->closed_at,
                'closed_by'         => $periodeSaatIni->closed_by,
            ]);
        }

        $riwayatClosing = $riwayatClosingBulanan;
        $semuaPermintaanBuka = collect(session('semua_permintaan_buka', Cache::get('semua_permintaan_buka', [])));

        if (Schema::hasTable('memo_unlocks')) {
            $dbMemos = MemoUnlock::where('tujuan_divisi', 'sirkulasi')
                ->latest('id')
                ->limit(15)
                ->get();

            foreach ($dbMemos as $m) {
                $existsInList = $semuaPermintaanBuka->contains(fn($item) => is_object($item) && ($item->id ?? '') === $m->nomor_memo);
                if (!$existsInList) {
                    $isBulanan = str_starts_with((string) $m->nomor_memo, 'REQ-ULK-B');
                    $tgt = $isBulanan
                        ? Carbon::parse($m->tanggal_target)->format('Y-m')
                        : Carbon::parse($m->tanggal_target)->toDateString();
                    $tgtLabel = $isBulanan
                        ? Carbon::parse($m->tanggal_target)->translatedFormat('F Y')
                        : Carbon::parse($m->tanggal_target)->translatedFormat('d F Y');
                    $isApproved = ($m->status === 'OPEN' || in_array($m->status_akuntansi, ['APPROVED', 'DISETUJUI'], true));

                    $semuaPermintaanBuka->push((object)[
                        'id'                 => $m->nomor_memo,
                        'mode'               => $isBulanan ? 'bulanan' : 'harian',
                        'target'             => $tgt,
                        'target_label'       => $tgtLabel,
                        'kategori_alasan'    => $m->kategori_urgensi ?: 'Penjualan Baru / Susulan Masuk',
                        'alasan'             => $m->alasan,
                        'estimasi_eksemplar' => (int) preg_replace('/[^0-9]/', '', (string) $m->spesifikasi_iklan),
                        'estimasi_nominal'   => (float) $m->estimasi_nominal,
                        'pemohon'            => $m->diketahui_oleh ?: 'Staf Sirkulasi',
                        'pemohon_role'       => 'Divisi Sirkulasi & Distribusi',
                        'status'             => $isApproved ? 'APPROVED' : 'PENDING',
                        'status_label'       => $isApproved ? 'Disetujui & Dibuka Akuntansi' : 'Menunggu Otorisasi Akuntansi',
                        'created_at'         => $m->created_at ? $m->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        $user = auth()->user();
        $canDirectUnlock = $user && ($user->isAdmin() || $user->hasRole('akuntansi', 'accounting', 'keuangan'));
        $canManageCutoff = $user && ($user->isAdmin() || $user->hasRole('admin', 'administrator', 'manager', 'sirkulasi', 'akuntansi', 'accounting', 'keuangan'));
        $activeMemo = Schema::hasTable('memo_unlocks')
            ? MemoUnlock::whereDate('tanggal_target', $tanggalPilihan)
                ->where('tujuan_divisi', 'sirkulasi')
                ->latest('id')
                ->first()
            : null;

        return view('pages.sirkulasi.jatah.tutup', compact(
            'mode',
            'harianSaatIni',
            'tanggalPilihan',
            'rekapJurusanHarian',
            'riwayatClosingHarian',
            'riwayatClosingBulanan',
            'riwayatClosing',
            'periodeSaatIni',
            'bulanPilihan',
            'permintaanBukaHarian',
            'permintaanBukaBulanan',
            'semuaPermintaanBuka',
            'canDirectUnlock',
            'jamCutoffSirkulasi',
            'isPastCutoffToday',
            'canManageCutoff',
            'activeMemo'
        ));
    }

    /**
     * Kunci / Buka Kunci Data Penjualan Harian atau Bulanan.
     */
    public function lock(Request $request)
    {
        $mode = $request->input('mode', 'harian');
        $action = $request->input('action', 'lock');
        $operator = auth()->user()?->name ?? 'Manager Sirkulasi';

        if ($action === 'request_unlock') {
            return $this->requestUnlock($request);
        }

        if ($action === 'cancel_request_unlock') {
            return $this->cancelRequestUnlock($request);
        }

        if ($mode === 'harian') {
            $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
            $formatTanggal = Carbon::parse($tanggal)->translatedFormat('d F Y');

            if ($action === 'unlock') {
                $user = auth()->user();
                $canDirectUnlock = $user && ($user->isAdmin() || $user->hasRole('akuntansi', 'accounting', 'keuangan'));
                if (!$canDirectUnlock) {
                    return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggal])
                        ->with('error', "Akses Ditolak: Sesuai SOP, transaksi yang telah ditutup HANYA DAPAT DIBUKA KEMBALI OLEH BAGIAN AKUNTANSI. Silakan ajukan permohonan buka kunci ke Bagian Akuntansi.");
                }

                session()->forget('locked_harian_' . $tanggal);
                session()->forget('locked_at_' . $tanggal);
                session()->forget('locked_by_' . $tanggal);
                session()->forget('request_unlock_harian_' . $tanggal);
                Cache::forget('locked_harian_' . $tanggal);
                Cache::forget('locked_at_' . $tanggal);
                Cache::forget('locked_by_' . $tanggal);
                Cache::forget('request_unlock_harian_' . $tanggal);

                if (Schema::hasTable('daily_cutoffs')) {
                    DailyCutoff::whereDate('tanggal_edisi', $tanggal)->update(['is_closed' => false]);
                }

                if (Schema::hasTable('memo_unlocks')) {
                    $updated = MemoUnlock::whereDate('tanggal_target', $tanggal)
                        ->where('tujuan_divisi', 'sirkulasi')
                        ->update([
                            'status'                  => 'OPEN',
                            'status_akuntansi'        => 'APPROVED',
                            'dibuka_pada'             => now(),
                            'diterima_akuntansi_pada' => now(),
                        ]);

                    if (!$updated) {
                        MemoUnlock::create([
                            'tanggal_target'          => $tanggal,
                            'nomor_memo'              => 'MEMO/SIRK/' . Carbon::parse($tanggal)->format('Y/m/d'),
                            'kategori_urgensi'        => 'Buka Kunci Harian Sirkulasi',
                            'nama_pemasang'           => 'Divisi Sirkulasi',
                            'spesifikasi_iklan'       => 'Otorisasi Buka Kunci Harian',
                            'estimasi_nominal'        => 0,
                            'alasan'                  => 'Dibuka langsung oleh Divisi Akuntansi (' . $operator . ')',
                            'tujuan_divisi'           => 'sirkulasi',
                            'status_akuntansi'        => 'APPROVED',
                            'diketahui_oleh'          => $operator,
                            'dibuka_oleh_user_id'     => auth()->id() ?? 1,
                            'status'                  => 'OPEN',
                            'dibuka_pada'             => now(),
                            'diterima_akuntansi_pada' => now(),
                        ]);
                    }
                }

                if (class_exists(AuditLogAkuntansi::class) && Schema::hasTable('audit_log_akuntansis')) {
                    AuditLogAkuntansi::catat('BUKA_KUNCI', 'SIRKULASI', "Otorisasi buka kunci data penjualan harian sirkulasi tanggal {$tanggal} oleh {$operator}");
                }

                $semua = collect(session('semua_permintaan_buka', Cache::get('semua_permintaan_buka', [])))
                    ->map(function ($item) use ($tanggal) {
                        if (is_object($item) && $item->mode === 'harian' && $item->target === $tanggal) {
                            $item->status = 'APPROVED';
                            $item->status_label = 'Disetujui & Dibuka Akuntansi';
                            $item->unlocked_at = Carbon::now()->format('Y-m-d H:i:s');
                        }
                        return $item;
                    })->all();
                session()->put('semua_permintaan_buka', $semua);
                Cache::put('semua_permintaan_buka', $semua, now()->addDays(7));

                return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggal])
                    ->with('success', "Kunci Data Penjualan Harian Tanggal {$formatTanggal} ({$tanggal}) berhasil DIBUKA oleh Akuntansi. Staf Sirkulasi sekarang dapat menginput transaksi penjualan susulan.");
            }

            $savedJatah = Cache::get('jatah_harian_' . $tanggal, []);
            if (empty($savedJatah)) {
                return redirect()->back()
                    ->with('error', "Cut-Off Gagal: Belum ada data jatah oplah pada edisi {$formatTanggal} ({$tanggal}). Silakan lakukan Salin Jatah atau Edit Jatah terlebih dahulu sebelum melakukan Cut-Off.");
            }

            SirkulasiDataService::executeTutupHarian($tanggal, $operator);

            return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggal])
                ->with('success', "Data Penjualan Harian Edisi {$formatTanggal} ({$tanggal}) berhasil DITUTUP & DIKUNCI (Status: READY TO PULL AKUNTING / REKONSILIASI KASIR). Data jatah dan retur harian terkunci dari perubahan.");
        }

        $periode = $request->input('periode', Carbon::now()->format('Y-m'));
        $formatPeriode = Carbon::parse($periode . '-01')->translatedFormat('F Y');
        $targetDateBulan = Carbon::parse($periode . '-01')->endOfMonth()->toDateString();

        if ($action === 'unlock') {
            $user = auth()->user();
            $canDirectUnlock = $user && ($user->isAdmin() || $user->hasRole('akuntansi', 'accounting', 'keuangan'));
            if (!$canDirectUnlock) {
                return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $periode])
                    ->with('error', "Akses Ditolak: Sesuai SOP, transaksi yang telah ditutup HANYA DAPAT DIBUKA KEMBALI OLEH BAGIAN AKUNTANSI. Silakan ajukan permohonan buka kunci ke Bagian Akuntansi.");
            }

            session()->forget('locked_bulanan_' . $periode);
            session()->forget('locked_at_bulan_' . $periode);
            session()->forget('locked_by_bulan_' . $periode);
            session()->forget('request_unlock_bulanan_' . $periode);
            Cache::forget('locked_bulanan_' . $periode);
            Cache::forget('locked_at_bulan_' . $periode);
            Cache::forget('locked_by_bulan_' . $periode);
            Cache::forget('request_unlock_bulanan_' . $periode);

            if (Schema::hasTable('memo_unlocks')) {
                MemoUnlock::whereDate('tanggal_target', $targetDateBulan)
                    ->where('tujuan_divisi', 'sirkulasi')
                    ->update([
                        'status'                  => 'OPEN',
                        'status_akuntansi'        => 'APPROVED',
                        'dibuka_pada'             => now(),
                        'diterima_akuntansi_pada' => now(),
                    ]);
            }

            if (class_exists(AuditLogAkuntansi::class) && Schema::hasTable('audit_log_akuntansis')) {
                AuditLogAkuntansi::catat('BUKA_KUNCI', 'SIRKULASI', "Otorisasi buka kunci data penjualan bulanan sirkulasi periode {$periode} oleh {$operator}");
            }

            $semua = collect(session('semua_permintaan_buka', Cache::get('semua_permintaan_buka', [])))
                ->map(function ($item) use ($periode) {
                    if (is_object($item) && $item->mode === 'bulanan' && $item->target === $periode) {
                        $item->status = 'APPROVED';
                        $item->status_label = 'Disetujui & Dibuka Akuntansi';
                        $item->unlocked_at = Carbon::now()->format('Y-m-d H:i:s');
                    }
                    return $item;
                })->all();
            session()->put('semua_permintaan_buka', $semua);
            Cache::put('semua_permintaan_buka', $semua, now()->addDays(7));

            return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $periode])
                ->with('success', "Kunci Data Penjualan Periode Bulanan {$formatPeriode} berhasil DIBUKA oleh Akuntansi.");
        }

        session()->put('locked_bulanan_' . $periode, true);
        session()->put('locked_at_bulan_' . $periode, Carbon::now()->format('Y-m-d H:i:s'));
        session()->put('locked_by_bulan_' . $periode, $operator);
        Cache::put('locked_bulanan_' . $periode, true, now()->addDays(7));
        Cache::put('locked_at_bulan_' . $periode, Carbon::now()->format('Y-m-d H:i:s'), now()->addDays(7));
        Cache::put('locked_by_bulan_' . $periode, $operator, now()->addDays(7));

        if (Schema::hasTable('memo_unlocks')) {
            MemoUnlock::whereDate('tanggal_target', $targetDateBulan)
                ->where('tujuan_divisi', 'sirkulasi')
                ->where('status', 'OPEN')
                ->update([
                    'status'               => 'CLOSED',
                    'ditutup_kembali_pada' => now(),
                ]);
        }

        session()->forget('request_unlock_bulanan_' . $periode);
        Cache::forget('request_unlock_bulanan_' . $periode);

        return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $periode])
            ->with('success', "Data Penjualan Periode Bulanan {$formatPeriode} berhasil DITUTUP & DIKUNCI (Status: READY TO PULL AKUNTING). Data jatah dan retur terkunci dari perubahan.");
    }

    /**
     * Pengajuan Permintaan Pembukaan Kunci Transaksi ke Divisi Akuntansi.
     */
    public function requestUnlock(Request $request)
    {
        $mode = $request->input('mode', 'harian');
        $operator = auth()->user()?->name ?? 'Staf Sirkulasi';

        if ($mode === 'harian') {
            $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
            $formatTanggal = Carbon::parse($tanggal)->translatedFormat('d F Y');
            $isLocked = $this->sirkulasi->isTanggalJatahLocked($tanggal);

            if (!$isLocked) {
                return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggal])
                    ->with('error', "Transaksi penjualan tanggal {$formatTanggal} belum ditutup/dikunci. Data masih dapat diedit secara langsung tanpa perlu meminta izin ke Akuntansi.");
            }

            $request->validate([
                'alasan'             => 'required|string|min:5',
                'kategori_alasan'    => 'nullable|string',
                'estimasi_eksemplar' => 'nullable|integer',
                'estimasi_nominal'   => 'nullable|numeric',
            ], [
                'alasan.required' => 'Alasan permohonan pembukaan transaksi wajib diisi agar dapat diverifikasi oleh Akuntansi.',
                'alasan.min'      => 'Alasan permohonan minimal 5 karakter.',
            ]);

            $requestId = class_exists(MemoUnlock::class) ? MemoUnlock::generateNomorMemoSirkulasi() : ('REQ-ULK-H' . Carbon::parse($tanggal)->format('ymd') . '-' . strtoupper(substr(uniqid(), -4)));
            $estimasiEks = (int) $request->input('estimasi_eksemplar', 0);
            $estimasiNom = (int) str_replace(['.', ','], '', (string) $request->input('estimasi_nominal', 0));
            $kategoriAlasan = $request->input('kategori_alasan', 'Penjualan Baru / Susulan Masuk');

            $requestData = (object)[
                'id'                 => $requestId,
                'mode'               => 'harian',
                'target'             => $tanggal,
                'target_label'       => $formatTanggal,
                'kategori_alasan'    => $kategoriAlasan,
                'alasan'             => $request->input('alasan'),
                'estimasi_eksemplar' => $estimasiEks,
                'estimasi_nominal'   => (float) $estimasiNom,
                'pemohon'            => $operator,
                'pemohon_role'       => 'Divisi Sirkulasi & Distribusi',
                'status'             => 'PENDING',
                'status_label'       => 'Menunggu Otorisasi Akuntansi',
                'created_at'         => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            if (Schema::hasTable('memo_unlocks')) {
                MemoUnlock::updateOrCreate(
                    ['tanggal_target' => $tanggal, 'tujuan_divisi' => 'sirkulasi', 'status_akuntansi' => 'PENDING'],
                    [
                        'nomor_memo'          => $requestId,
                        'kategori_urgensi'    => $kategoriAlasan,
                        'nama_pemasang'       => $operator . ' (Sirkulasi)',
                        'spesifikasi_iklan'   => "Susulan {$estimasiEks} Eks (Harian)",
                        'estimasi_nominal'    => $estimasiNom,
                        'alasan'              => $request->input('alasan'),
                        'diketahui_oleh'      => $operator,
                        'dibuka_oleh_user_id' => auth()->id() ?? 1,
                        'status'              => 'CLOSED',
                    ]
                );
            }

            session()->put('request_unlock_harian_' . $tanggal, $requestData);
            Cache::put('request_unlock_harian_' . $tanggal, $requestData, now()->addDays(7));

            $semua = collect(session('semua_permintaan_buka', Cache::get('semua_permintaan_buka', [])))
                ->reject(fn($item) => is_object($item) && $item->mode === 'harian' && $item->target === $tanggal)
                ->prepend($requestData)->values()->all();
            session()->put('semua_permintaan_buka', $semua);
            Cache::put('semua_permintaan_buka', $semua, now()->addDays(7));

            $msg = "Memo permohonan pembukaan transaksi harian tanggal {$formatTanggal} ({$tanggal}) BERHASIL DIKIRIM ke Divisi Akuntansi (Nomor: {$requestId}).";
            if ($request->filled('redirect_to')) {
                return redirect($request->input('redirect_to'))->with('success', $msg);
            }

            return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggal])->with('success', $msg);
        }

        $periode = $request->input('periode', Carbon::now()->format('Y-m'));
        $formatPeriode = Carbon::parse($periode . '-01')->translatedFormat('F Y');
        $targetDateBulan = Carbon::parse($periode . '-01')->endOfMonth()->toDateString();
        $isLocked = session('locked_bulanan_' . $periode, Cache::get('locked_bulanan_' . $periode, false));

        if (!$isLocked) {
            return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $periode])
                ->with('error', "Transaksi penjualan periode {$formatPeriode} belum ditutup/dikunci.");
        }

        $request->validate([
            'alasan'             => 'required|string|min:5',
            'kategori_alasan'    => 'nullable|string',
            'estimasi_eksemplar' => 'nullable|integer',
            'estimasi_nominal'   => 'nullable|numeric',
        ], [
            'alasan.required' => 'Alasan permohonan pembukaan transaksi bulanan wajib diisi agar dapat diverifikasi oleh Akuntansi.',
            'alasan.min'      => 'Alasan permohonan minimal 5 karakter.',
        ]);

        $requestId = 'REQ-ULK-B' . Carbon::parse($periode . '-01')->format('ym') . '-' . strtoupper(substr(uniqid(), -4));
        $estimasiEks = (int) $request->input('estimasi_eksemplar', 0);
        $estimasiNom = (int) str_replace(['.', ','], '', (string) $request->input('estimasi_nominal', 0));
        $kategoriAlasan = $request->input('kategori_alasan', 'Penjualan Baru / Susulan Masuk');

        $requestData = (object)[
            'id'                 => $requestId,
            'mode'               => 'bulanan',
            'target'             => $periode,
            'target_label'       => $formatPeriode,
            'kategori_alasan'    => $kategoriAlasan,
            'alasan'             => $request->input('alasan'),
            'estimasi_eksemplar' => $estimasiEks,
            'estimasi_nominal'   => (float) $estimasiNom,
            'pemohon'            => $operator,
            'pemohon_role'       => 'Divisi Sirkulasi & Distribusi',
            'status'             => 'PENDING',
            'status_label'       => 'Menunggu Otorisasi Akuntansi',
            'created_at'         => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        if (Schema::hasTable('memo_unlocks')) {
            MemoUnlock::updateOrCreate(
                ['tanggal_target' => $targetDateBulan, 'tujuan_divisi' => 'sirkulasi', 'status_akuntansi' => 'PENDING'],
                [
                    'nomor_memo'          => $requestId,
                    'kategori_urgensi'    => $kategoriAlasan,
                    'nama_pemasang'       => $operator . ' (Sirkulasi)',
                    'spesifikasi_iklan'   => "Susulan {$estimasiEks} Eks (Bulanan {$periode})",
                    'estimasi_nominal'    => $estimasiNom,
                    'alasan'              => $request->input('alasan'),
                    'diketahui_oleh'      => $operator,
                    'dibuka_oleh_user_id' => auth()->id() ?? 1,
                    'status'              => 'CLOSED',
                ]
            );
        }

        session()->put('request_unlock_bulanan_' . $periode, $requestData);
        Cache::put('request_unlock_bulanan_' . $periode, $requestData, now()->addDays(7));

        $semua = collect(session('semua_permintaan_buka', Cache::get('semua_permintaan_buka', [])))
            ->reject(fn($item) => is_object($item) && $item->mode === 'bulanan' && $item->target === $periode)
            ->prepend($requestData)->values()->all();
        session()->put('semua_permintaan_buka', $semua);
        Cache::put('semua_permintaan_buka', $semua, now()->addDays(7));

        return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $periode])
            ->with('success', "Memo permohonan pembukaan transaksi bulanan periode {$formatPeriode} BERHASIL DIKIRIM ke Divisi Akuntansi (Tiket: {$requestId}).");
    }

    /**
     * Membatalkan permohonan buka kunci transaksi sebelum diproses Akuntansi.
     */
    public function cancelRequestUnlock(Request $request)
    {
        $mode = $request->input('mode', 'harian');

        if ($mode === 'harian') {
            $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
            session()->forget('request_unlock_harian_' . $tanggal);
            Cache::forget('request_unlock_harian_' . $tanggal);

            if (Schema::hasTable('memo_unlocks')) {
                MemoUnlock::whereDate('tanggal_target', $tanggal)
                    ->where('tujuan_divisi', 'sirkulasi')
                    ->where('status_akuntansi', 'PENDING')
                    ->delete();
            }

            $semua = collect(session('semua_permintaan_buka', Cache::get('semua_permintaan_buka', [])))
                ->reject(fn($item) => is_object($item) && $item->mode === 'harian' && $item->target === $tanggal)
                ->values()->all();
            session()->put('semua_permintaan_buka', $semua);
            Cache::put('semua_permintaan_buka', $semua, now()->addDays(7));

            return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggal])
                ->with('success', "Permintaan pembukaan kunci transaksi harian tanggal {$tanggal} telah dibatalkan.");
        }

        $periode = $request->input('periode', Carbon::now()->format('Y-m'));
        $targetDateBulan = Carbon::parse($periode . '-01')->endOfMonth()->toDateString();
        session()->forget('request_unlock_bulanan_' . $periode);
        Cache::forget('request_unlock_bulanan_' . $periode);

        if (Schema::hasTable('memo_unlocks')) {
            MemoUnlock::whereDate('tanggal_target', $targetDateBulan)
                ->where('tujuan_divisi', 'sirkulasi')
                ->where('status_akuntansi', 'PENDING')
                ->delete();
        }

        $semua = collect(session('semua_permintaan_buka', Cache::get('semua_permintaan_buka', [])))
            ->reject(fn($item) => is_object($item) && $item->mode === 'bulanan' && $item->target === $periode)
            ->values()->all();
        session()->put('semua_permintaan_buka', $semua);
        Cache::put('semua_permintaan_buka', $semua, now()->addDays(7));

        return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $periode])
            ->with('success', "Permintaan pembukaan kunci transaksi bulanan periode {$periode} telah dibatalkan.");
    }

    /**
     * Cetak Lembar Dokumen Resmi Memo Buka Cut-Off Sirkulasi.
     */
    public function cetakMemo(string $id)
    {
        $memo = MemoUnlock::with(['dibukaOleh', 'diresponOleh'])->findOrFail($id);
        return view('pages.sirkulasi.jatah.cetak_memo', compact('memo'));
    }
}
