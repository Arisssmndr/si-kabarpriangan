<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\AccountingAccountMapping;
use App\Models\AkunPerkiraan;
use App\Models\AuditLogAkuntansi;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumDetail;
use App\Models\PeriodeAkuntansi;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaturanController extends Controller
{
    public function __construct(protected AccountingService $accountingService)
    {
    }

    public function masterCoa(Request $request)
    {
        $query = AkunPerkiraan::query();

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('kode_perkiraan', 'like', "%{$q}%")
                    ->orWhere('nama_perkiraan', 'like', "%{$q}%")
                    ->orWhere('kategori', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kepala') && $request->kepala !== 'all') {
            $query->where('kepala', (int) $request->kepala);
        }

        if ($request->filled('level') && $request->level !== 'all') {
            $query->where('level', (int) $request->level);
        }

        if ($request->filled('saldo_normal') && $request->saldo_normal !== 'all') {
            $query->where('saldo_normal', strtolower($request->saldo_normal));
        }

        $daftarAkun = $query->orderBy('kode_perkiraan')->paginate(25)->withQueryString();

        $stats = [
            'total_akun'        => AkunPerkiraan::count(),
            'total_aset'        => AkunPerkiraan::where('kepala', 1)->count(),
            'total_kewajiban'   => AkunPerkiraan::whereIn('kepala', [2, 3])->count(),
            'total_operasional' => AkunPerkiraan::whereIn('kepala', [4, 5, 6])->count(),
        ];

        return view('pages.accounting.pengaturan.coa', compact('daftarAkun', 'stats'));
    }

    public function storeCoa(Request $request)
    {
        $request->validate([
            'kode_perkiraan' => 'required|string|unique:akun_perkiraan,kode_perkiraan',
            'nama_perkiraan' => 'required|string',
            'kepala'         => 'required|integer|between:1,8',
            'kategori'       => 'required|string',
            'saldo_normal'   => 'required|in:debet,kredit',
            'saldo_awal'     => 'nullable|numeric|min:0',
        ]);

        AkunPerkiraan::create([
            'kode_perkiraan'     => $request->kode_perkiraan,
            'nama_perkiraan'     => $request->nama_perkiraan,
            'kepala'             => $request->kepala,
            'kategori'           => $request->kategori,
            'saldo_normal'       => $request->saldo_normal,
            'saldo_awal'         => (float) $request->input('saldo_awal', 0),
            'saldo_awal_tanggal' => Carbon::today()->startOfYear()->toDateString(),
            'entitas'            => 'Kabar Priangan',
            'status_aktif'       => true,
            'deskripsi'          => $request->deskripsi,
        ]);

        AuditLogAkuntansi::catat('TAMBAH_COA', 'MASTER', "Tambah akun {$request->kode_perkiraan} - {$request->nama_perkiraan}");

        return redirect()->back()->with('success', 'Akun perkiraan berhasil ditambahkan.');
    }

    public function updateSaldoAwalCoa(Request $request, int $id)
    {
        $request->validate([
            'saldo_awal' => 'required|numeric|min:0',
        ]);

        $akun = AkunPerkiraan::findOrFail($id);
        $lama = (float) ($akun->saldo_awal ?? 0);
        $baru = (float) $request->saldo_awal;

        $akun->update([
            'saldo_awal'         => $baru,
            'saldo_awal_tanggal' => $request->input('saldo_awal_tanggal') ?: Carbon::today()->startOfYear()->toDateString(),
        ]);

        AuditLogAkuntansi::catat(
            'SALDO_AWAL_COA',
            'MASTER',
            "Update saldo awal COA {$akun->kode_perkiraan} dari Rp " . number_format($lama, 0, ',', '.') . " menjadi Rp " . number_format($baru, 0, ',', '.')
        );

        return redirect()->back()->with('success', "Saldo awal akun {$akun->kode_perkiraan} berhasil diperbarui.");
    }

    public function importCoa(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $request->file('file')->getRealPath();
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines || count($lines) === 0) {
            return redirect()->back()->with('error', 'File CSV kosong atau tidak dapat dibaca.');
        }

        $imported = 0;
        $updated = 0;
        $delimiter = str_contains($lines[0], ';') ? ';' : ',';

        foreach ($lines as $idx => $line) {
            $cols = array_map('trim', str_getcsv($line, $delimiter));
            if (count($cols) < 2 || $cols[0] === '') {
                continue;
            }
            if ($idx === 0 && (stripos($cols[0], 'kode') !== false || stripos($cols[1], 'nama') !== false)) {
                continue;
            }

            $kode = $cols[0];
            $nama = $cols[1];
            $kepala = isset($cols[2]) && is_numeric($cols[2]) ? (int) $cols[2] : (int) substr($kode, 0, 1);
            $kepala = ($kepala >= 1 && $kepala <= 8) ? $kepala : 1;
            $kategori = ($cols[3] ?? '') !== '' ? $cols[3] : 'Umum';
            $saldoNormalRaw = strtolower($cols[4] ?? '');
            if (!in_array($saldoNormalRaw, ['debet', 'kredit'], true)) {
                $saldoNormalRaw = in_array($kepala, [2, 3, 4], true) ? 'kredit' : 'debet';
            }

            $existing = AkunPerkiraan::where('kode_perkiraan', $kode)->first();
            if ($existing) {
                $existing->update([
                    'nama_perkiraan' => $nama,
                    'kepala'         => $kepala,
                    'kategori'       => $kategori,
                    'saldo_normal'   => $saldoNormalRaw,
                    'status_aktif'   => true,
                ]);
                $updated++;
            } else {
                AkunPerkiraan::create([
                    'kode_perkiraan' => $kode,
                    'nama_perkiraan' => $nama,
                    'kepala'         => $kepala,
                    'kategori'       => $kategori,
                    'saldo_normal'   => $saldoNormalRaw,
                    'entitas'        => 'Kabar Priangan',
                    'status_aktif'   => true,
                ]);
                $imported++;
            }
        }

        if ($imported === 0 && $updated === 0) {
            return redirect()->back()->with('error', 'Tidak ada baris COA valid pada file CSV (format: kode_perkiraan,nama_perkiraan,kepala,kategori,saldo_normal).');
        }

        AuditLogAkuntansi::catat('IMPORT_COA', 'MASTER', "Import CSV COA: {$imported} akun baru, {$updated} akun diperbarui.");

        return redirect()->back()->with('success', "Import COA selesai: {$imported} akun ditambahkan, {$updated} akun diperbarui.");
    }

    public function accountMapping()
    {
        $this->accountingService->ensureAccountMappingsSeeded();
        $mappings = AccountingAccountMapping::orderBy('kategori_modul')->orderBy('label')->get();
        $daftarAkun = AkunPerkiraan::aktif()->orderBy('kode_perkiraan')->get();

        return view('pages.accounting.pengaturan.mapping', compact('mappings', 'daftarAkun'));
    }

    public function updateAccountMapping(Request $request)
    {
        $request->validate([
            'mappings' => 'required|array',
        ]);

        foreach ($request->mappings as $id => $kodePerkiraan) {
            $mapping = AccountingAccountMapping::find($id);
            $akun = AkunPerkiraan::where('kode_perkiraan', $kodePerkiraan)->first();
            if ($mapping && $akun) {
                $mapping->update([
                    'kode_perkiraan' => $akun->kode_perkiraan,
                    'nama_perkiraan' => $akun->nama_perkiraan,
                ]);
            }
        }

        AuditLogAkuntansi::catat('UPDATE_MAPPING_COA', 'MASTER', 'Memperbarui konfigurasi pemetaan akun COA otomatis sistem.');

        return redirect()->back()->with('success', 'Konfigurasi Mapping Akun COA otomatis berhasil disimpan.');
    }

    public function tutupBuku(Request $request)
    {
        $periodeKey = $request->input('periode_key', Carbon::now()->format('Y-m'));
        $periodeList = PeriodeAkuntansi::orderByDesc('tahun')->orderByDesc('bulan')->get();
        $unpostedSummary = $this->accountingService->getUnpostedSubledgerSummary($periodeKey);

        $glCheck = JurnalUmum::where('status', 'posted')
            ->where('tanggal', 'like', "{$periodeKey}%")
            ->selectRaw('COALESCE(SUM(total_debet), 0) as total_d, COALESCE(SUM(total_kredit), 0) as total_k')
            ->first();

        $glTotalDebet = (float) ($glCheck->total_d ?? 0);
        $glTotalKredit = (float) ($glCheck->total_k ?? 0);
        $isGlBalanced = abs($glTotalDebet - $glTotalKredit) < 0.01;

        return view('pages.accounting.pengaturan.tutup_buku', compact(
            'periodeList',
            'periodeKey',
            'unpostedSummary',
            'glTotalDebet',
            'glTotalKredit',
            'isGlBalanced'
        ));
    }

    public function prosesTutupBuku(Request $request)
    {
        $request->validate(['periode_key' => 'required|string']);
        $periodeKey = $request->periode_key;

        // Cegah tutup buku bila masih ada draft jurnal pada periode tersebut (kecuali force_close)
        $draftCount = JurnalUmum::where('tanggal', 'like', "{$periodeKey}%")->where('status', 'draft')->count();
        if ($draftCount > 0 && !$request->boolean('force_close')) {
            return redirect()->back()->with('error', "Masih terdapat {$draftCount} jurnal berstatus DRAFT pada periode {$periodeKey}. Setujui atau selesaikan draft jurnal terlebih dahulu sebelum Tutup Buku.");
        }

        DB::transaction(function () use ($request, $periodeKey) {
            // Optional: Posting Closing Entry memindahkan Laba/Rugi Bersih periode ke Laba Tahun Berjalan (3.00.03...)
            if ($request->boolean('post_closing_entry')) {
                $existingClosing = JurnalUmum::where('referensi', "CLOSE-{$periodeKey}")
                    ->whereNull('reversed_by_jurnal_id')
                    ->exists();

                if (!$existingClosing) {
                    $glRows = JurnalUmumDetail::whereHas('jurnal', fn ($q) => $q->where('status', 'posted')->where('tanggal', 'like', "{$periodeKey}%"))
                        ->selectRaw('kode_perkiraan, nama_perkiraan, COALESCE(SUM(debet), 0) as d, COALESCE(SUM(kredit), 0) as k')
                        ->groupBy('kode_perkiraan', 'nama_perkiraan')
                        ->get();

                    $closingLines = [];
                    $netIncome = 0.0;

                    foreach ($glRows as $row) {
                        $kode = (string) $row->kode_perkiraan;
                        if (!(str_starts_with($kode, '4.') || str_starts_with($kode, '5.') || str_starts_with($kode, '6.') || str_starts_with($kode, '7.') || str_starts_with($kode, '8.'))) {
                            continue;
                        }
                        $net = (float) $row->k - (float) $row->d; // + jika kredit > debet (pendapatan), - jika beban
                        if (abs($net) < 0.01) {
                            continue;
                        }
                        $netIncome += $net;
                        $closingLines[] = [
                            'kode_perkiraan' => $kode,
                            'nama_perkiraan' => $row->nama_perkiraan,
                            'debet'          => $net > 0 ? $net : 0,
                            'kredit'         => $net < 0 ? abs($net) : 0,
                        ];
                    }

                    if (!empty($closingLines)) {
                        $akunLaba = $this->accountingService->getMappedAccount('laba_tahun_berjalan');
                        $tglTutup = Carbon::parse($periodeKey . '-01')->endOfMonth()->toDateString();
                        $noJurnal = JurnalUmum::generateNomorJurnal($tglTutup);
                        $sumSide = array_sum(array_column($closingLines, 'debet')) + ($netIncome < 0 ? abs($netIncome) : 0);

                        $jurnal = JurnalUmum::create([
                            'no_jurnal'    => $noJurnal,
                            'tanggal'      => $tglTutup,
                            'tipe'         => 'auto',
                            'sumber'       => 'manual',
                            'referensi'    => "CLOSE-{$periodeKey}",
                            'keterangan'   => "Jurnal Penutup (Closing Entry) Periode {$periodeKey}",
                            'total_debet'  => $sumSide,
                            'total_kredit' => $sumSide,
                            'is_balanced'  => true,
                            'status'       => 'posted',
                            'user_id'      => auth()->id() ?? 1,
                            'approved_by'  => auth()->id() ?? 1,
                            'approved_at'  => now(),
                        ]);

                        $urutan = 1;
                        foreach ($closingLines as $line) {
                            JurnalUmumDetail::create([
                                'jurnal_umum_id' => $jurnal->id,
                                'kode_perkiraan' => $line['kode_perkiraan'],
                                'nama_perkiraan' => $line['nama_perkiraan'],
                                'debet'          => $line['debet'],
                                'kredit'         => $line['kredit'],
                                'urutan'         => $urutan++,
                            ]);
                        }
                        JurnalUmumDetail::create([
                            'jurnal_umum_id' => $jurnal->id,
                            'kode_perkiraan' => $akunLaba['kode'],
                            'nama_perkiraan' => $akunLaba['nama'],
                            'debet'          => $netIncome < 0 ? abs($netIncome) : 0,
                            'kredit'         => $netIncome > 0 ? $netIncome : 0,
                            'urutan'         => $urutan++,
                        ]);
                    }
                }
            }

            $periode = PeriodeAkuntansi::where('periode_key', $periodeKey)->first();
            if ($periode) {
                $periode->update([
                    'status'               => 'terkunci',
                    'ditutup_pada'         => now(),
                    'ditutup_oleh_user_id' => auth()->id() ?? 1,
                ]);
            } else {
                $th = (int) substr($periodeKey, 0, 4);
                $bl = (int) substr($periodeKey, 5, 2);
                PeriodeAkuntansi::create([
                    'tahun'                => $th,
                    'bulan'                => $bl,
                    'periode_key'          => $periodeKey,
                    'status'               => 'terkunci',
                    'ditutup_pada'         => now(),
                    'ditutup_oleh_user_id' => auth()->id() ?? 1,
                    'catatan'              => 'Closing periode bulanan.',
                ]);
            }

            AuditLogAkuntansi::catat('TUTUP_BUKU', 'PERIODE', "Tutup buku periode {$periodeKey}");
        });

        return redirect()->back()->with('success', "Periode {$periodeKey} berhasil ditutup dan dikunci.");
    }

    public function bukaTutupBuku(Request $request)
    {
        $request->validate([
            'periode_key' => 'required|string',
            'alasan'      => 'required|string|min:5|max:255',
        ]);

        $periode = PeriodeAkuntansi::where('periode_key', $request->periode_key)->firstOrFail();
        $periode->update([
            'status'  => 'terbuka',
            'catatan' => 'Dibuka kembali: ' . trim($request->alasan),
        ]);

        AuditLogAkuntansi::catat('BUKA_PERIODE', 'PERIODE', "Membuka kembali periode {$request->periode_key}. Alasan: {$request->alasan}");

        return redirect()->back()->with('success', "Periode {$request->periode_key} berhasil dibuka kembali secara terkontrol.");
    }

    public function auditLog(Request $request)
    {
        $query = AuditLogAkuntansi::with('user');

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('aksi', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('modul') && $request->modul !== 'all') {
            $query->where('modul', $request->modul);
        }

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $logs = $query->latest('id')->paginate(25)->withQueryString();

        $stats = [
            'total_log'     => AuditLogAkuntansi::count(),
            'hari_ini'      => AuditLogAkuntansi::whereDate('created_at', Carbon::today())->count(),
            'total_modul'   => AuditLogAkuntansi::distinct('modul')->count('modul'),
            'user_tercatat' => AuditLogAkuntansi::distinct('user_id')->count('user_id'),
        ];

        return view('pages.accounting.pengaturan.audit_log', compact('logs', 'stats'));
    }
}
