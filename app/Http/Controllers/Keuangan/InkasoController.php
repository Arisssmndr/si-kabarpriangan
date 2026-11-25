<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use App\Models\KasTransaksi;
use App\Models\PembayaranFaktur;
use App\Models\PenugasanInkaso;
use App\Models\SetoranInkasoLapangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InkasoController extends Controller
{
    /**
     * Dashboard & Antrean Penugasan Lapangan (Inkaso) — KHUSUS ADMIN FAKTUR / KEUANGAN.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        // Guard: Jika user adalah Petugas Inkaso murni, dilarang membuka desktop admin, langsung arahkan ke portal mobile
        if ($user && ($user->hasRole('petugas_inkaso') || $user->hasRole('inkaso')) && !$user->hasRole('faktur') && !$user->hasRole('administrator') && !$user->hasRole('keuangan') && !$user->hasRole('admin_keuangan')) {
            return redirect()->route('keuangan.inkaso.mobile');
        }

        $q = trim($request->input('q', ''));
        $kategori = $request->input('kategori');
        $statusPetugas = $request->input('status_petugas');
        $petugasId = $request->input('petugas_id');
        $wilayah = trim($request->input('wilayah', ''));

        // 1. Antrean Faktur Piutang (Sisa Piutang > 0)
        $antreanQuery = Faktur::with(['customer', 'penugasanAktif.petugas', 'pembayaran', 'penugasanInkaso.petugas'])
            ->where('sisa_piutang', '>', 0)
            ->where('status', '!=', 'lunas');

        if ($q !== '') {
            $antreanQuery->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%")
                          ->orWhere('no_telepon', 'like', "%{$q}%");
                    });
            });
        }

        if ($kategori) {
            $antreanQuery->where('kategori', $kategori);
        }

        if ($wilayah !== '') {
            $antreanQuery->whereHas('customer', function ($c) use ($wilayah) {
                $c->where('kota', 'like', "%{$wilayah}%")
                  ->orWhere('alamat', 'like', "%{$wilayah}%");
            });
        }

        if ($statusPetugas === 'belum') {
            $antreanQuery->whereDoesntHave('penugasanAktif');
        } elseif ($statusPetugas === 'sudah') {
            $antreanQuery->whereHas('penugasanAktif');
        }

        if ($petugasId) {
            $antreanQuery->whereHas('penugasanAktif', function ($p) use ($petugasId) {
                $p->where('petugas_id', $petugasId);
            });
        }

        $antreanPiutang = $antreanQuery->latest('tanggal_faktur')->get();

        // 2. Daftar Penugasan Berjalan
        $berjalanQuery = PenugasanInkaso::with(['faktur.customer', 'petugas'])
            ->where('status_tugas', 'berjalan');

        if ($q !== '') {
            $berjalanQuery->where(function ($sub) use ($q) {
                $sub->where('no_tugas', 'like', "%{$q}%")
                    ->orWhereHas('faktur', function ($f) use ($q) {
                        $f->where('no_faktur', 'like', "%{$q}%")
                          ->orWhereHas('customer', function ($c) use ($q) {
                              $c->where('nama_customer', 'like', "%{$q}%");
                          });
                    })
                    ->orWhereHas('petugas', function ($p) use ($q) {
                        $p->where('name', 'like', "%{$q}%");
                    });
            });
        }

        if ($kategori) {
            $berjalanQuery->whereHas('faktur', function ($f) use ($kategori) {
                $f->where('kategori', $kategori);
            });
        }

        if ($wilayah !== '') {
            $berjalanQuery->whereHas('faktur.customer', function ($c) use ($wilayah) {
                $c->where('kota', 'like', "%{$wilayah}%")
                  ->orWhere('alamat', 'like', "%{$wilayah}%");
            });
        }

        if ($petugasId) {
            $berjalanQuery->where('petugas_id', $petugasId);
        }

        $penugasanBerjalan = $berjalanQuery->latest('tanggal_tugas')->get();

        // 3. Riwayat Penugasan Selesai/Batal
        $riwayatQuery = PenugasanInkaso::with(['faktur.customer', 'petugas', 'pembayaran'])
            ->whereIn('status_tugas', ['selesai', 'batal']);

        if ($q !== '') {
            $riwayatQuery->where(function ($sub) use ($q) {
                $sub->where('no_tugas', 'like', "%{$q}%")
                    ->orWhereHas('faktur', function ($f) use ($q) {
                        $f->where('no_faktur', 'like', "%{$q}%")
                          ->orWhereHas('customer', function ($c) use ($q) {
                              $c->where('nama_customer', 'like', "%{$q}%");
                          });
                    })
                    ->orWhereHas('petugas', function ($p) use ($q) {
                        $p->where('name', 'like', "%{$q}%");
                    });
            });
        }

        if ($kategori) {
            $riwayatQuery->whereHas('faktur', function ($f) use ($kategori) {
                $f->where('kategori', $kategori);
            });
        }

        if ($wilayah !== '') {
            $riwayatQuery->whereHas('faktur.customer', function ($c) use ($wilayah) {
                $c->where('kota', 'like', "%{$wilayah}%")
                  ->orWhere('alamat', 'like', "%{$wilayah}%");
            });
        }

        if ($petugasId) {
            $riwayatQuery->where('petugas_id', $petugasId);
        }

        $riwayatPenugasan = $riwayatQuery->latest('updated_at')->take(50)->get();

        // 4. Laporan Hasil Penugasan (Catatan Bu Ulfa: Janji Bayar, Cicilan, Belum Bayar, dll)
        $laporanQuery = PenugasanInkaso::with(['faktur.customer', 'petugas', 'pelapor', 'pembayaran'])
            ->whereNotNull('hasil_kunjungan');

        if ($q !== '') {
            $laporanQuery->where(function ($sub) use ($q) {
                $sub->where('no_tugas', 'like', "%{$q}%")
                    ->orWhere('keterangan_hasil', 'like', "%{$q}%")
                    ->orWhereHas('faktur', function ($f) use ($q) {
                        $f->where('no_faktur', 'like', "%{$q}%")
                          ->orWhereHas('customer', function ($c) use ($q) {
                              $c->where('nama_customer', 'like', "%{$q}%");
                          });
                    })
                    ->orWhereHas('petugas', function ($p) use ($q) {
                        $p->where('name', 'like', "%{$q}%");
                    });
            });
        }

        if ($kategori) {
            $laporanQuery->whereHas('faktur', function ($f) use ($kategori) {
                $f->where('kategori', $kategori);
            });
        }

        if ($wilayah !== '') {
            $laporanQuery->whereHas('faktur.customer', function ($c) use ($wilayah) {
                $c->where('kota', 'like', "%{$wilayah}%")
                  ->orWhere('alamat', 'like', "%{$wilayah}%");
            });
        }

        if ($petugasId) {
            $laporanQuery->where('petugas_id', $petugasId);
        }

        if ($request->filled('hasil_kunjungan')) {
            $laporanQuery->where('hasil_kunjungan', $request->hasil_kunjungan);
        }

        $laporanHasil = $laporanQuery->latest('tanggal_lapor')->latest('id')->get();
        $janjiBayarCount = PenugasanInkaso::where('hasil_kunjungan', 'janji_bayar')->where('status_tugas', '!=', 'selesai')->count();

        // 5. Daftar Petugas Inkaso untuk Dropdown
        $petugasInkaso = User::where('role', 'petugas_inkaso')
            ->orWhere('role', 'administrator')
            ->orderBy('name')
            ->get();

        // Metrik Ringkas Inkaso
        $totalPiutangBerjalan = (float) Faktur::where('sisa_piutang', '>', 0)->sum('sisa_piutang');
        $fakturBelumTertugaskan = Faktur::where('sisa_piutang', '>', 0)
            ->where('status', '!=', 'lunas')
            ->whereDoesntHave('penugasanAktif')
            ->count();
        $tugasAktifCount = PenugasanInkaso::where('status_tugas', 'berjalan')->count();

        return view('pages.keuangan.inkaso.index', compact(
            'antreanPiutang',
            'penugasanBerjalan',
            'laporanHasil',
            'janjiBayarCount',
            'riwayatPenugasan',
            'petugasInkaso',
            'totalPiutangBerjalan',
            'fakturBelumTertugaskan',
            'tugasAktifCount',
            'q',
            'wilayah'
        ));
    }

    /**
     * Tugaskan Faktur Piutang kepada Petugas Inkaso.
     * Mendukung 1 s/d 10 faktur piutang sekaligus, pilihan petugas standar (Ahmad Supardi, Rudi Hermawan, dsb.)
     * atau nama kustom tanpa harus membuat akun user baru.
     */
    public function assign(Request $request)
    {
        $user = Auth::user();
        // Guard: Hanya Admin Faktur / Keuangan yang berhak mengatur dan menugaskan faktur piutang
        if ($user && ($user->hasRole('petugas_inkaso') || $user->hasRole('inkaso')) && !$user->hasRole('faktur') && !$user->hasRole('administrator') && !$user->hasRole('keuangan') && !$user->hasRole('admin_keuangan')) {
            abort(403, 'Akses ditolak: Hanya Admin Faktur yang berhak mengatur dan menugaskan faktur ke petugas inkaso.');
        }

        $request->validate([
            'faktur_id'          => 'nullable',
            'faktur_ids'         => 'nullable',
            'petugas_nama'       => 'nullable|string',
            'manual_petugas_nama'=> 'nullable|string',
            'petugas_id'         => 'nullable',
            'tanggal_tugas'      => 'required|date',
            'catatan'            => 'nullable|string',
        ]);

        // Resolve target fakturs (single atau batch hingga 10 faktur)
        $rawIds = $request->input('faktur_ids', '');
        if (is_array($rawIds)) {
            $parsedIds = $rawIds;
        } elseif (is_string($rawIds) && trim($rawIds) !== '') {
            $parsedIds = explode(',', $rawIds);
        } elseif ($request->filled('faktur_id')) {
            $parsedIds = [$request->input('faktur_id')];
        } else {
            return back()->with('error', 'Pilih minimal satu faktur piutang untuk ditugaskan.');
        }

        $selectedIds = collect($parsedIds)
            ->map(fn ($v) => (int) trim((string) $v))
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->take(10)
            ->values();

        if ($selectedIds->isEmpty()) {
            return back()->with('error', 'Faktur tidak valid.');
        }

        $fakturs = Faktur::whereIn('id', $selectedIds->all())->get();
        if ($fakturs->isEmpty()) {
            return back()->with('error', 'Faktur piutang tidak ditemukan.');
        }

        // Tentukan Nama Petugas Lapangan
        $namaPetugasInput = trim((string) $request->input('petugas_nama', ''));
        if ($namaPetugasInput === '__manual__') {
            $namaPetugasInput = trim((string) $request->input('manual_petugas_nama', ''));
        } elseif ($namaPetugasInput === '__blank__') {
            $namaPetugasInput = '';
        }

        // Cari petugas_id jika cocok dengan User di database
        $petugasId = null;
        if ($request->filled('petugas_id') && is_numeric($request->petugas_id)) {
            $userPetugas = User::find($request->petugas_id);
            if ($userPetugas) {
                $petugasId = $userPetugas->id;
                if (empty($namaPetugasInput)) {
                    $namaPetugasInput = $userPetugas->name;
                }
            }
        }

        if (empty($namaPetugasInput)) {
            $namaPetugasInput = 'Ahmad Supardi';
        }

        // Fallback petugas_id jika belum terhubung ke user tertentu
        if (!$petugasId) {
            $matchedUser = User::where('name', 'like', "%{$namaPetugasInput}%")->first();
            $petugasId = $matchedUser ? $matchedUser->id : (auth()->id() ?? User::where('role', 'administrator')->value('id') ?? 1);
        }

        // Generate No Tugas individual & tentukan No Surat Grup (1 nomor surat untuk seluruh faktur batch / penugasan aktif petugas di tanggal yg sama)
        $period = Carbon::parse($request->tanggal_tugas)->format('Ym');
        $prefixTugas = "TGS-{$period}-";
        $prefixSurat = "ST-{$period}-";

        $nextSeq = ((int) PenugasanInkaso::where('no_tugas', 'like', "{$prefixTugas}%")
            ->pluck('no_tugas')
            ->map(function ($val) {
                $parts = explode('-', (string) $val);
                return (int) end($parts);
            })
            ->max()) + 1;

        $noSuratGrup = null;
        if (!$request->boolean('buat_surat_baru')) {
            $noSuratGrup = PenugasanInkaso::where('petugas_id', $petugasId)
                ->whereDate('tanggal_tugas', Carbon::parse($request->tanggal_tugas)->toDateString())
                ->whereIn('status_tugas', ['berjalan', 'menunggu_kasir'])
                ->whereNotNull('no_surat_grup')
                ->value('no_surat_grup');
        }

        if (!$noSuratGrup) {
            $nextSuratSeq = ((int) PenugasanInkaso::where('no_surat_grup', 'like', "{$prefixSurat}%")
                ->pluck('no_surat_grup')
                ->map(function ($val) {
                    $parts = explode('-', (string) $val);
                    return (int) end($parts);
                })
                ->max()) + 1;

            // Nomor Surat Grup yang sama untuk seluruh faktur dalam batch assign ini
            do {
                $noSuratGrup = $prefixSurat . str_pad($nextSuratSeq, 3, '0', STR_PAD_LEFT);
                $nextSuratSeq++;
            } while (PenugasanInkaso::where('no_surat_grup', $noSuratGrup)->exists());
        }

        $createdTasks = [];
        foreach ($fakturs as $faktur) {
            if ($faktur->sisa_piutang <= 0) continue;

            $penugasanLama = PenugasanInkaso::where('faktur_id', $faktur->id)
                ->where('status_tugas', 'berjalan')
                ->first();

            if ($penugasanLama) {
                $penugasanLama->update([
                    'nama_petugas'      => $namaPetugasInput,
                    'petugas_id'        => $petugasId,
                    'tanggal_tugas'     => $request->tanggal_tugas,
                    'status_konfirmasi' => 'menunggu',
                    'catatan'           => $request->catatan,
                    'no_surat_grup'     => $noSuratGrup,
                ]);
                $createdTasks[] = $penugasanLama;
            } else {
                do {
                    $noTugas = $prefixTugas . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
                    $nextSeq++;
                } while (PenugasanInkaso::where('no_tugas', $noTugas)->exists());

                $penugasan = PenugasanInkaso::create([
                    'no_tugas'          => $noTugas,
                    'no_surat_grup'     => $noSuratGrup,
                    'faktur_id'         => $faktur->id,
                    'petugas_id'        => $petugasId,
                    'nama_petugas'      => $namaPetugasInput,
                    'tanggal_tugas'     => $request->tanggal_tugas,
                    'status_tugas'      => 'berjalan',
                    'status_konfirmasi' => 'menunggu',
                    'catatan'           => $request->catatan,
                ]);
                $createdTasks[] = $penugasan;
            }
        }

        if (empty($createdTasks)) {
            return back()->with('error', 'Semua faktur terpilih sudah lunas.');
        }

        $firstTask = $createdTasks[0];
        $targetIdsStr = $selectedIds->join(',');

        if ($request->filled('redirect_cetak') || $request->input('action') === 'cetak') {
            return redirect()->route('keuangan.inkaso.surat-tugas.cetak', [
                'id'         => $firstTask->id,
                'faktur_ids' => $targetIdsStr,
                'petugas'    => $namaPetugasInput,
            ]);
        }

        return back()->with('success', "Surat Penugasan {$noSuratGrup} berhasil dibuat untuk {$namaPetugasInput} (" . count($createdTasks) . " faktur).");
    }

    /**
     * Input Setoran Hasil Tagihan Inkaso oleh Kasir/Admin.
     */
    public function storeSetoran(Request $request)
    {
        if (auth()->user()->hasRole('faktur') && !auth()->user()->hasRole('kasir', 'keuangan', 'admin_keuangan', 'administrator')) {
            return back()->with('error', 'Akses ditolak: Bagian Faktur & Piutang tidak berwenang menginput atau mengatur nominal setoran kas. Transaksi penerimaan kas hanya dapat diproses oleh bagian Kasir.');
        }

        $request->validate([
            'faktur_id'           => 'required|exists:faktur,id',
            'penugasan_inkaso_id' => 'nullable|exists:penugasan_inkaso,id',
            'nominal_setoran'     => 'required|numeric|min:1',
            'tanggal_bayar'       => 'required|date',
            'metode_bayar'        => 'required|in:tunai,transfer',
            'catatan'             => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $faktur = Faktur::lockForUpdate()->findOrFail($request->faktur_id);

            $nominal = (float) $request->nominal_setoran;
            if ($nominal > (float) $faktur->sisa_piutang) {
                return back()->with('error', 'Nominal setoran (Rp ' . number_format($nominal, 0, ',', '.') . ') melebihi sisa piutang faktur (Rp ' . number_format($faktur->sisa_piutang, 0, ',', '.') . ').');
            }

            $userId = Auth::id() ?? 1;

            // 1. Terbitkan Kwitansi Setoran (Pencarian nomor kwitansi yang aman dan tidak duplikat)
            $period = date('Ym');
            $prefixKwitansi = 'KW-INK-' . $period . '-';
            
            $latestBayar = PembayaranFaktur::where('no_kwitansi', 'like', "{$prefixKwitansi}%")
                ->orderByRaw('CAST(SUBSTRING(no_kwitansi, -4) AS UNSIGNED) DESC')
                ->first();

            $nextSeq = 1;
            if ($latestBayar) {
                $parts = explode('-', $latestBayar->no_kwitansi);
                $nextSeq = ((int) end($parts)) + 1;
            }

            do {
                $kwitansiNo = $prefixKwitansi . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
                $nextSeq++;
            } while (PembayaranFaktur::where('no_kwitansi', $kwitansiNo)->exists());

            $bayar = PembayaranFaktur::create([
                'no_kwitansi'         => $kwitansiNo,
                'faktur_id'           => $faktur->id,
                'penugasan_inkaso_id' => $request->penugasan_inkaso_id,
                'tanggal_bayar'       => $request->tanggal_bayar,
                'nominal_bayar'       => $nominal,
                'metode_bayar'        => $request->metode_bayar,
                'tipe_bayar'          => 'cicilan_inkaso',
                'diterima_oleh'       => $userId,
                'catatan'             => $request->catatan ?? 'Setoran hasil penagihan Inkaso lapangan',
            ]);

            // 2. Update Nilai Faktur Real-Time
            $faktur->total_terbayar = (float) $faktur->total_terbayar + $nominal;
            $faktur->sisa_piutang   = max(0, (float) $faktur->sisa_piutang - $nominal);

            $statusSelesai = false;
            if ($faktur->sisa_piutang <= 0) {
                $faktur->status = 'lunas';
                $statusSelesai = true;
            } else {
                $faktur->status = 'belum_bayar';
            }
            $faktur->save();

            // 3. Update status penugasan inkaso putaran ini menjadi selesai
            // Jika lunas 100% -> faktur pindah ke Tab 4: Riwayat Selesai Lunas
            // Jika cicilan (sisa > 0) -> penugasan ditutup, faktur otomatis kembali ke Tab 1: Antrean Faktur Piutang dengan sisa saldo berkurang
            $tugasToClose = null;
            if ($request->filled('penugasan_inkaso_id')) {
                $tugasToClose = PenugasanInkaso::find($request->penugasan_inkaso_id);
            }
            if (!$tugasToClose) {
                $tugasToClose = PenugasanInkaso::where('faktur_id', $faktur->id)
                    ->where('status_tugas', 'berjalan')
                    ->first();
            }

            if ($tugasToClose) {
                $existingKet = $tugasToClose->keterangan_hasil;
                $catatanKasir = $request->catatan ? trim($request->catatan) : '';
                $keteranganFinal = $existingKet;
                if ($catatanKasir) {
                    $keteranganFinal = $existingKet ? ($existingKet . ' | Kasir: ' . $catatanKasir) : $catatanKasir;
                } elseif (!$keteranganFinal) {
                    $keteranganFinal = $statusSelesai
                        ? 'Pelunasan 100% via setoran kasir'
                        : ('Setoran cicilan ke kasir: Rp ' . number_format($nominal, 0, ',', '.') . ' (Sisa Piutang: Rp ' . number_format($faktur->sisa_piutang, 0, ',', '.') . ')');
                }

                $tugasToClose->update([
                    'status_tugas'     => 'selesai',
                    'hasil_kunjungan'  => $statusSelesai ? 'lunas' : 'cicilan',
                    'nominal_diterima' => $nominal,
                    'keterangan_hasil' => $keteranganFinal,
                    'tanggal_lapor'    => $request->tanggal_bayar,
                ]);
            }

            // 3. Mutasi Kas Masuk Otomatis ke Buku Kas
            $kategoriKas = 'Setoran Inkaso Faktur ' . ucfirst($faktur->kategori);
            KasTransaksi::catatMutasi(
                'masuk',
                $kategoriKas,
                $nominal,
                'Setoran Inkaso ' . $kwitansiNo . ' atas Faktur ' . $faktur->no_faktur . ' (' . ($faktur->customer->nama_customer ?? '-') . ')',
                $bayar,
                $userId,
                $request->tanggal_bayar
            );

            if ($statusSelesai) {
                $msg = 'Setoran Inkaso Rp ' . number_format($nominal, 0, ',', '.') . ' berhasil diterima. Faktur ' . $faktur->no_faktur . ' kini telah LUNAS 100%!';
            } else {
                $msg = 'Setoran cicilan Rp ' . number_format($nominal, 0, ',', '.') . ' berhasil dibukukan ke Kas. Faktur kembali ke Antrean Piutang dengan sisa saldo Rp ' . number_format($faktur->sisa_piutang, 0, ',', '.') . '.';
            }

            return back()->with('success', $msg);
        });
    }

    /**
     * Catat Laporan Hasil Kunjungan Lapangan oleh Staf Faktur & Piutang (Bu Ulfa).
     * Bagian Faktur HANYA mencatat keterangan administrasi lapangan (tanpa input nominal rupiah).
     * Input nominal rupiah setoran uang kas diproses oleh Kasir.
     */
    public function simpanLaporanHasil(Request $request, $id)
    {
        $request->validate([
            'hasil_kunjungan'     => 'required|in:lunas,cicilan,janji_bayar,belum_bayar,tidak_bertemu',
            'tanggal_lapor'       => 'required|date',
            'tanggal_janji_bayar' => 'nullable|date',
            'keterangan_hasil'    => 'required|string|max:1000',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $tugas = PenugasanInkaso::with(['faktur.customer', 'petugas'])->lockForUpdate()->findOrFail($id);
            $userId = Auth::id() ?? 1;

            $hasil = $request->hasil_kunjungan;

            $tugas->tanggal_lapor       = $request->tanggal_lapor;
            $tugas->hasil_kunjungan     = $hasil;
            $tugas->tanggal_janji_bayar = $request->tanggal_janji_bayar;
            $tugas->keterangan_hasil    = $request->keterangan_hasil;
            $tugas->dilaporkan_oleh     = $userId;

            // Alur Siklus Penugasan Inkaso:
            // 1. Jika hasil penugasan adalah cicilan / lunas:
            //    Staf Faktur hanya mencatat keterangan hasil kunjungan inkaso (tanpa input nominal uang di faktur).
            //    Status tugas tetap 'berjalan' agar Kasir dapat memproses uang fisik di Tab Laporan Hasil / Penugasan Aktif.
            //    Faktur otomatis berkurang (cicilan) atau lunas SETELAH Kasir menginput setoran kas.
            if ($hasil === 'cicilan' || $hasil === 'lunas') {
                $tugas->status_tugas = 'berjalan';
            } elseif (in_array($hasil, ['janji_bayar', 'belum_bayar', 'tidak_bertemu'])) {
                // 2. Jika tidak ada pembayaran uang (janji bayar, belum bayar, tidak bertemu):
                //    Jika dicentang 'kembalikan ke antrean', putaran tugas diselesaikan sehingga berkas fisik faktur
                //    otomatis kembali ke Tab 1 Antrean Belum Ditugaskan untuk siklus penugasan berikutnya.
                if ($request->input('kembalikan_ke_antrean') == 1) {
                    $tugas->status_tugas = 'selesai';
                }
            }

            $tugas->save();

            $msg = "Laporan hasil penugasan {$tugas->no_tugas} berhasil dicatat.";
            if ($hasil === 'cicilan' || $hasil === 'lunas') {
                $msg .= " Keterangan hasil penugasan telah masuk ke Tab Laporan Hasil untuk diproses input setoran kas oleh Kasir.";
            } elseif ($hasil === 'janji_bayar' && $request->filled('tanggal_janji_bayar')) {
                $tglJanji = Carbon::parse($request->tanggal_janji_bayar)->translatedFormat('d F Y');
                $msg .= " Catatan Janji Bayar pada {$tglJanji} telah masuk agenda penagihan.";
            }

            return back()->with('success', $msg);
        });
    }

    /**
     * Cetak Lembar Rekap Hasil Penugasan Lapangan Inkaso.
     */
    public function cetakRekapHasil(Request $request)
    {
        $petugasId = $request->input('petugas_id');
        $hasil = $request->input('hasil_kunjungan');
        $dari = $request->input('dari_tanggal', Carbon::today()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai_tanggal', Carbon::today()->toDateString());

        $query = PenugasanInkaso::with(['faktur.customer', 'petugas', 'pelapor', 'pembayaran'])
            ->whereNotNull('hasil_kunjungan')
            ->whereBetween('tanggal_lapor', [$dari, $sampai]);

        if ($petugasId) {
            $query->where('petugas_id', $petugasId);
        }
        if ($hasil) {
            $query->where('hasil_kunjungan', $hasil);
        }

        $laporanList = $query->latest('tanggal_lapor')->get();
        $petugasNama = $petugasId ? (User::find($petugasId)->name ?? 'Semua Petugas') : 'Semua Petugas Inkaso';

        return view('pages.keuangan.inkaso.cetak-rekap-hasil', compact('laporanList', 'dari', 'sampai', 'petugasNama'));
    }

    /**
     * Cetak Surat Tugas Penagihan / Daftar Tagihan Harian Petugas.
     * Mendukung 1 s/d maksimal 10 Faktur dalam 1 Surat Penugasan Inkaso.
     */
    public function cetakSuratTugas(Request $request, $id)
    {
        $penugasan = PenugasanInkaso::with(['faktur.customer', 'faktur.creator', 'petugas'])->findOrFail($id);
        $namaPetugas = trim($request->input('petugas', $penugasan->petugas_nama));

        // Ambil daftar ID faktur dari parameter faktur_ids (comma-separated atau array), maksimal 10 faktur
        $rawIds = $request->input('faktur_ids', '');
        if (is_array($rawIds)) {
            $parsedIds = $rawIds;
        } elseif (is_string($rawIds) && trim($rawIds) !== '') {
            $parsedIds = explode(',', $rawIds);
        } else {
            $parsedIds = [$penugasan->faktur_id];
        }

        $selectedIds = collect($parsedIds)
            ->map(fn ($v) => (int) trim((string) $v))
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->take(10)
            ->values();

        if ($selectedIds->isEmpty()) {
            $selectedIds = collect([(int) $penugasan->faktur_id]);
        }

        $fakturs = Faktur::with(['customer', 'creator'])
            ->whereIn('id', $selectedIds->all())
            ->get()
            ->sortBy(fn ($f) => $selectedIds->search($f->id))
            ->values();

        $faktur = $penugasan->faktur;

        // Daftar piutang aktif untuk dropdown panel tambah faktur lainnya
        $daftarFakturPiutang = Faktur::with('customer')
            ->where('sisa_piutang', '>', 0)
            ->where('status', '!=', 'lunas')
            ->orderBy('tanggal_faktur', 'desc')
            ->take(50)
            ->get();

        return view('pages.keuangan.inkaso.cetak-surat-tugas', compact('penugasan', 'faktur', 'fakturs', 'selectedIds', 'namaPetugas', 'daftarFakturPiutang'));
    }

    /**
     * Cetak Kwitansi Setoran Inkaso.
     */
    public function kwitansi($id)
    {
        $pembayaran = PembayaranFaktur::with(['faktur.customer', 'penugasanInkaso.petugas', 'penerima'])->findOrFail($id);
        return view('pages.keuangan.inkaso.kwitansi', compact('pembayaran'));
    }

    /**
     * Tampilan Antarmuka Khusus Mobile untuk Petugas Inkaso di Lapangan.
     */
    public function mobileIndex(Request $request)
    {
        $user = Auth::user();
        $petugasId = $user->id;
        $q = trim($request->input('q', ''));

        // 1. Surat Tugas Baru Masuk (Menunggu Konfirmasi: Terima atau Tolak)
        $tugasMenungguQuery = PenugasanInkaso::with(['faktur.customer'])
            ->where(function ($sub) use ($petugasId, $user) {
                $sub->where('petugas_id', $petugasId)
                    ->orWhere('nama_petugas', $user->name);
            })
            ->where('status_konfirmasi', 'menunggu')
            ->where('status_tugas', '!=', 'selesai');

        $suratTugasMenunggu = $tugasMenungguQuery->latest('tanggal_tugas')->get();

        // 2. Daftar Tagihan Aktif yang Diterima & Sedang Berjalan di Lapangan
        $tagihanAktifQuery = PenugasanInkaso::with(['faktur.customer'])
            ->where(function ($sub) use ($petugasId, $user) {
                $sub->where('petugas_id', $petugasId)
                    ->orWhere('nama_petugas', $user->name);
            })
            ->where('status_konfirmasi', 'diterima')
            ->whereIn('status_tugas', ['berjalan', 'menunggu_kasir'])
            ->whereHas('faktur', function ($f) {
                $f->where('sisa_piutang', '>', 0);
            });

        if ($q !== '') {
            $tagihanAktifQuery->where(function ($sub) use ($q) {
                $sub->where('no_tugas', 'like', "%{$q}%")
                    ->orWhere('no_surat_grup', 'like', "%{$q}%")
                    ->orWhereHas('faktur', function ($f) use ($q) {
                        $f->where('no_faktur', 'like', "%{$q}%")
                          ->orWhereHas('customer', function ($c) use ($q) {
                              $c->where('nama_customer', 'like', "%{$q}%")
                                ->orWhere('kota', 'like', "%{$q}%");
                          });
                    });
            });
        }

        $tagihanAktif = $tagihanAktifQuery->latest('tanggal_tugas')->get();

        // Ambil seluruh penugasan diterima pada no_surat_grup yang aktif (termasuk faktur yg sudah lunas di surat tsb)
        $activeGrupNumbers = $tagihanAktif->pluck('no_surat_grup')->filter()->unique()->values();
        $allTasksByGrup = collect();
        if ($activeGrupNumbers->isNotEmpty()) {
            $allTasksByGrup = PenugasanInkaso::with(['faktur.customer', 'setoranLapangan'])
                ->whereIn('no_surat_grup', $activeGrupNumbers->all())
                ->where(function ($sub) use ($petugasId, $user) {
                    $sub->where('petugas_id', $petugasId)
                        ->orWhere('nama_petugas', $user->name);
                })
                ->where('status_konfirmasi', 'diterima')
                ->get()
                ->groupBy('no_surat_grup');
        }

        // 3. Setoran Titipan Hari Ini yang Menunggu Scan Kasir (Pending)
        $setoranPending = SetoranInkasoLapangan::with(['faktur.customer'])
            ->where('petugas_id', $petugasId)
            ->where('status_approval', 'pending')
            ->latest('id')
            ->get();

        // 4. Riwayat Pembayaran Sah yang Telah Disetujui Kasir (Sah & Resmi)
        $riwayatSetoranSah = SetoranInkasoLapangan::with(['faktur.customer', 'kasir'])
            ->where('petugas_id', $petugasId)
            ->where('status_approval', 'approved')
            ->latest('approved_at')
            ->take(30)
            ->get();

        // 4b. Riwayat Surat Penugasan Grup yang Sudah Selesai / Disimpan
        $riwayatSuratRaw = PenugasanInkaso::with(['faktur.customer', 'setoranLapangan'])
            ->where(function ($sub) use ($petugasId, $user) {
                $sub->where('petugas_id', $petugasId)
                    ->orWhere('nama_petugas', $user->name);
            })
            ->where('status_tugas', 'selesai')
            ->latest('updated_at')
            ->get();

        $riwayatSuratSelesai = $riwayatSuratRaw
            ->whereNotNull('no_surat_grup')
            ->groupBy('no_surat_grup')
            ->filter(function ($group) use ($tagihanAktif) {
                $noSurat = $group->first()->no_surat_grup;
                return !$tagihanAktif->contains('no_surat_grup', $noSurat);
            })
            ->take(20);

        // 4c. Daftar Semua Faktur dari Penugasan/Riwayat untuk tab "Semua Faktur" di Riwayat
        $riwayatSemuaFaktur = $riwayatSuratRaw->take(50);

        // Rekap Setoran Hari Ini untuk KPI
        $setoranHariIni = SetoranInkasoLapangan::with(['faktur.customer'])
            ->where('petugas_id', $petugasId)
            ->whereDate('waktu_terima_lapangan', Carbon::today())
            ->latest('id')
            ->get();

        // Ringkasan KPI Mobile Hari Ini
        $totalTargetPiutang = (float) $tagihanAktif->sum(fn ($t) => $t->faktur->sisa_piutang ?? 0);
        $totalTerkumpulHariIni = (float) $setoranHariIni->where('status_approval', 'approved')->sum('nominal_titipan');
        $totalPendingKasirHariIni = (float) $setoranHariIni->where('status_approval', 'pending')->sum('nominal_titipan');
        $pendingBskByFaktur = $setoranPending->keyBy('faktur_id');

        // Kelompokkan tagihan aktif berdasarkan no_surat_grup (untuk tampilan per surat penugasan)
        // Tagihan tanpa no_surat_grup dikelompokkan individual dengan key = IND-{no_tugas}
        $tagihanGrupSurat = $tagihanAktif->groupBy(function ($t) {
            return $t->no_surat_grup ?: ('IND-' . $t->no_tugas);
        });

        return view('pages.keuangan.inkaso.mobile', compact(
            'user',
            'suratTugasMenunggu',
            'tagihanAktif',
            'tagihanGrupSurat',
            'allTasksByGrup',
            'setoranPending',
            'riwayatSetoranSah',
            'riwayatSuratSelesai',
            'riwayatSemuaFaktur',
            'setoranHariIni',
            'totalTargetPiutang',
            'totalTerkumpulHariIni',
            'totalPendingKasirHariIni',
            'pendingBskByFaktur',
            'q'
        ));
    }

    /**
     * Konfirmasi Penerimaan atau Penolakan Surat Tugas oleh Petugas Inkaso Mobile.
     */
    public function konfirmasiPenugasan(Request $request, $id)
    {
        $request->validate([
            'aksi'             => 'required|in:terima,tolak',
            'alasan_penolakan' => 'nullable|string|max:500',
        ]);

        $tugas = PenugasanInkaso::with('faktur')->findOrFail($id);

        if ($request->aksi === 'terima') {
            $tugas->update([
                'status_konfirmasi' => 'diterima',
                'status_tugas'      => 'berjalan',
                'waktu_konfirmasi'  => Carbon::now(),
            ]);

            return back()->with('success', "Surat penugasan faktur {$tugas->faktur->no_faktur} berhasil DITERIMA. Masuk ke antrean kunjungan.");
        }

        // Jika Tolak: kembalikan faktur ke antrean Admin Faktur
        $tugas->update([
            'status_konfirmasi' => 'ditolak',
            'status_tugas'      => 'batal',
            'alasan_penolakan'  => $request->alasan_penolakan ?? 'Ditolak oleh Petugas Inkaso di lapangan',
            'waktu_konfirmasi'  => Carbon::now(),
        ]);

        return back()->with('warning', "Surat penugasan faktur {$tugas->faktur->no_faktur} DITOLAK dan dikembalikan ke antrean Admin Faktur.");
    }

    /**
     * Input & Simpan Pembayaran Lapangan oleh Petugas Inkaso -> Menghasilkan BSK Ber-Barcode.
     */
    public function simpanSetoranLapangan(Request $request)
    {
        $request->validate([
            'faktur_id'           => 'required|exists:faktur,id',
            'penugasan_id'        => 'nullable|exists:penugasan_inkaso,id',
            'nominal_bayar'       => 'required|numeric|min:1000',
            'metode_bayar'        => 'required|in:tunai,transfer',
            'keterangan_angsuran' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($request) {
            $faktur = Faktur::with('customer')->lockForUpdate()->findOrFail($request->faktur_id);
            $nominalBayar = (float) $request->nominal_bayar;

            // 1. Cek apakah faktur ini sudah memiliki BSK pending yang belum disahkan kasir (Cegah duplikat klik/multi-submit)
            $existingPendingBsk = SetoranInkasoLapangan::where('faktur_id', $faktur->id)
                ->where('status_approval', 'pending')
                ->lockForUpdate()
                ->first();

            if ($existingPendingBsk) {
                return back()->with('error', "Faktur {$faktur->no_faktur} sudah memiliki bukti setoran pending ({$existingPendingBsk->no_bsk}) sebesar Rp " . number_format($existingPendingBsk->nominal_titipan, 0, ',', '.') . " yang sedang menunggu approval kasir. Harap setorkan ke kasir terlebih dahulu.");
            }

            if ($nominalBayar > (float) $faktur->sisa_piutang) {
                return back()->with('error', "Nominal pembayaran (Rp " . number_format($nominalBayar, 0, ',', '.') . ") melebihi sisa piutang faktur (Rp " . number_format($faktur->sisa_piutang, 0, ',', '.') . ").");
            }

            $noBsk = SetoranInkasoLapangan::generateNoBsk();
            $user = Auth::user();

            $setoran = SetoranInkasoLapangan::create([
                'no_bsk'                => $noBsk,
                'penugasan_id'          => $request->penugasan_id,
                'faktur_id'             => $faktur->id,
                'petugas_id'            => $user->id,
                'nama_pembayar'         => $faktur->customer->nama_customer ?? 'Pelanggan',
                'nominal_titipan'       => $nominalBayar,
                'metode_bayar'          => $request->metode_bayar,
                'keterangan_angsuran'   => $request->keterangan_angsuran ?: 'Pembayaran angsuran lapangan',
                'barcode_token'         => $noBsk,
                'waktu_terima_lapangan' => Carbon::now(),
                'status_approval'       => 'pending',
            ]);

            // Perbarui status penugasan jika ada
            if ($request->penugasan_id) {
                $tugas = PenugasanInkaso::lockForUpdate()->find($request->penugasan_id);
                if ($tugas) {
                    $tugas->update([
                        'status_tugas'     => 'berjalan',
                        'nominal_diterima' => (float) $tugas->nominal_diterima + $nominalBayar,
                        'keterangan_hasil' => $request->keterangan_angsuran ?: 'Pembayaran di lapangan',
                        'tanggal_lapor'    => Carbon::today(),
                    ]);
                }
            }

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Bukti Setoran Koleksi {$noBsk} berhasil diterbitkan.",
                    'data'    => $setoran,
                    'redirect_url' => route('keuangan.inkaso.bsk.show', $noBsk),
                ]);
            }

            return redirect()->route('keuangan.inkaso.bsk.show', $noBsk)
                ->with('success', "Bukti Setoran Koleksi {$noBsk} berhasil diterbitkan! Tunjukkan barcode ini ke Kasir.");
        });
    }

    /**
     * Update nominal/metode/keterangan pembayaran BSK yang sudah memiliki QR
     * selagi belum disetujui Kasir dan Surat Penugasan belum disimpan/diselesaikan.
     */
    public function updateSetoranLapangan(Request $request, $id)
    {
        $request->validate([
            'nominal_bayar'       => 'required|numeric|min:1000',
            'metode_bayar'        => 'required|in:tunai,transfer',
            'keterangan_angsuran' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        return DB::transaction(function () use ($request, $id, $user) {
            $setoran = SetoranInkasoLapangan::with(['faktur', 'penugasan'])
                ->lockForUpdate()
                ->findOrFail($id);

            if ((int) $setoran->petugas_id !== (int) $user->id) {
                return back()->with('error', 'Anda tidak berhak mengubah bukti setoran petugas lain.');
            }

            if ($setoran->status_approval !== 'pending') {
                return back()->with('error', "Bukti Setoran {$setoran->no_bsk} sudah diproses Kasir ({$setoran->status_approval}) dan tidak dapat diubah lagi.");
            }

            $tugas = $setoran->penugasan_id
                ? PenugasanInkaso::lockForUpdate()->find($setoran->penugasan_id)
                : null;

            if ($tugas && $tugas->status_tugas === 'selesai') {
                return back()->with('error', "Surat penugasan {$tugas->no_surat_grup} sudah disimpan/diselesaikan, sehingga nominal pembayaran {$setoran->no_bsk} tidak dapat diperbarui lagi.");
            }

            $faktur = Faktur::lockForUpdate()->findOrFail($setoran->faktur_id);
            $nominalBaru = (float) $request->nominal_bayar;
            $nominalLama = (float) $setoran->nominal_titipan;

            if ($nominalBaru > (float) $faktur->sisa_piutang) {
                return back()->with('error', "Nominal pembayaran (Rp " . number_format($nominalBaru, 0, ',', '.') . ") melebihi sisa piutang faktur (Rp " . number_format($faktur->sisa_piutang, 0, ',', '.') . ").");
            }

            $setoran->update([
                'nominal_titipan'       => $nominalBaru,
                'metode_bayar'          => $request->metode_bayar,
                'keterangan_angsuran'   => $request->keterangan_angsuran ?: $setoran->keterangan_angsuran,
                'waktu_terima_lapangan' => Carbon::now(),
            ]);

            if ($tugas) {
                $selisih = $nominalBaru - $nominalLama;
                $tugas->update([
                    'nominal_diterima' => max(0, (float) $tugas->nominal_diterima + $selisih),
                    'keterangan_hasil' => $request->keterangan_angsuran ?: $tugas->keterangan_hasil,
                    'tanggal_lapor'    => Carbon::today(),
                ]);
            }

            return redirect()->route('keuangan.inkaso.mobile', ['tab' => 'penagihan'])
                ->with('success', "Pembayaran BSK {$setoran->no_bsk} (QR) berhasil diperbarui menjadi Rp " . number_format($nominalBaru, 0, ',', '.') . ".");
        });
    }

    /**
     * Petugas Inkaso langsung menyetujui (Terima Semua) seluruh faktur dalam 1 Surat Penugasan Grup.
     */
    public function konfirmasiGrup(Request $request, string $noSuratGrup)
    {
        $user = Auth::user();

        $tugasList = PenugasanInkaso::with('faktur')
            ->where('no_surat_grup', $noSuratGrup)
            ->where(function ($sub) use ($user) {
                $sub->where('petugas_id', $user->id)
                    ->orWhere('nama_petugas', $user->name);
            })
            ->where('status_konfirmasi', 'menunggu')
            ->where('status_tugas', '!=', 'selesai')
            ->get();

        if ($tugasList->isEmpty()) {
            return back()->with('error', "Surat penugasan {$noSuratGrup} tidak ditemukan atau sudah dikonfirmasi.");
        }

        DB::transaction(function () use ($tugasList) {
            foreach ($tugasList as $tugas) {
                $tugas->update([
                    'status_konfirmasi' => 'diterima',
                    'status_tugas'      => 'berjalan',
                    'waktu_konfirmasi'  => Carbon::now(),
                ]);
            }
        });

        return back()->with('success', "Surat Penugasan {$noSuratGrup} ({$tugasList->count()} faktur) berhasil DITERIMA seluruhnya. Target piutang dashboard diperbarui.");
    }

    /**
     * Petugas Inkaso menyimpan & menyelesaikan Surat Penugasan Grup.
     * Menandai seluruh penugasan aktif dalam no_surat_grup sebagai 'selesai' (termasuk yang ada BSK pending maupun belum bayar),
     * sehingga data pembayaran terkunci dan surat berpindah ke Riwayat (sementara BSK pending tetap bisa di-scan Kasir).
     */
    public function selesaiGrup(Request $request, string $noSuratGrup)
    {
        $user = Auth::user();

        $allInGrup = PenugasanInkaso::with(['faktur.customer', 'setoranLapangan'])
            ->where('no_surat_grup', $noSuratGrup)
            ->where(function ($sub) use ($user) {
                $sub->where('petugas_id', $user->id)
                    ->orWhere('nama_petugas', $user->name);
            })
            ->where('status_konfirmasi', 'diterima')
            ->get();

        $penugasanAktif = $allInGrup->whereIn('status_tugas', ['berjalan', 'menunggu_kasir']);

        if ($penugasanAktif->isEmpty()) {
            return back()->with('error', "Surat penugasan {$noSuratGrup} tidak ditemukan atau sudah selesai.");
        }

        return DB::transaction(function () use ($allInGrup, $penugasanAktif, $noSuratGrup) {
            $totalFaktur    = $allInGrup->count();
            $totalPiutang   = $allInGrup->sum(fn ($t) => (float) ($t->faktur->total_tagihan ?? 0));
            $totalTerkumpul = $allInGrup->sum(fn ($t) => max((float) ($t->faktur->total_terbayar ?? 0), (float) $t->nominal_diterima));
            $totalSisa      = max(0, $totalPiutang - $totalTerkumpul);

            foreach ($penugasanAktif as $tugas) {
                $hasBskPending = $tugas->setoranLapangan->where('status_approval', 'pending')->count() > 0;
                $hasilKunjungan = ($tugas->nominal_diterima > 0 || ($tugas->faktur->total_terbayar ?? 0) > 0 || $hasBskPending)
                    ? 'cicilan'
                    : 'belum_bayar';

                $tugas->update([
                    'status_tugas'     => 'selesai',
                    'hasil_kunjungan'  => $tugas->hasil_kunjungan ?? $hasilKunjungan,
                    'keterangan_hasil' => $tugas->keterangan_hasil
                        ?? "Surat penugasan {$noSuratGrup} disimpan oleh petugas. Terkumpul/BSK: Rp " . number_format($totalTerkumpul, 0, ',', '.'),
                    'tanggal_lapor'    => now()->toDateString(),
                ]);
            }

            $msg = "Surat Penugasan {$noSuratGrup} ({$totalFaktur} faktur) berhasil disimpan & diselesaikan. "
                . "Total piutang: Rp " . number_format($totalPiutang, 0, ',', '.')
                . " | Terkumpul: Rp " . number_format($totalTerkumpul, 0, ',', '.');

            return redirect()->route('keuangan.inkaso.mobile', ['tab' => 'riwayat'])
                ->with('success', $msg);
        });
    }

    /**
     * Tampilan Bukti Setoran Koleksi (BSK) Digital dengan Barcode Code128 & QR Code untuk Kasir.
     */
    public function showBsk($no_bsk)
    {
        $setoran = SetoranInkasoLapangan::with(['faktur.customer', 'petugas', 'penugasan'])->where('no_bsk', $no_bsk)->firstOrFail();
        return view('pages.keuangan.inkaso.bsk-show', compact('setoran'));
    }

    /**
     * Update Profil Petugas Inkaso (Nama, Foto Profil WhatsApp Style, No HP, Password).
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name'       => 'required|string|max:255',
            'telepon'    => 'nullable|string|max:30',
            'foto'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'hapus_foto' => 'nullable|boolean',
        ]);

        $uploadDir = public_path('uploads/avatars');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // 1. Opsi Hapus Foto Profil
        if ($request->boolean('hapus_foto')) {
            if ($user->foto && file_exists($uploadDir . '/' . $user->foto)) {
                @unlink($uploadDir . '/' . $user->foto);
            }
            $user->foto = null;
        }

        // 2. Opsi Upload Foto Profil Baru
        if ($request->hasFile('foto')) {
            if ($user->foto && file_exists($uploadDir . '/' . $user->foto)) {
                @unlink($uploadDir . '/' . $user->foto);
            }
            $file = $request->file('foto');
            $filename = 'inkaso_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $user->foto = $filename;
        }

        // 3. Update Nama & Telepon Resmi Petugas
        $oldName = $user->name;
        $user->name = trim($request->name);
        $user->telepon = $request->telepon ? trim($request->telepon) : null;

        $user->save();

        // 5. Sinkronkan nama petugas ke data penugasan aktif
        if ($oldName !== $user->name) {
            PenugasanInkaso::where('petugas_id', $user->id)
                ->where('status_tugas', 'berjalan')
                ->update(['nama_petugas' => $user->name]);
        }

        return redirect()->route('keuangan.inkaso.mobile', ['tab' => 'profil'])
            ->with('success', 'Profil dan foto petugas berhasil diperbarui!');
    }
}
