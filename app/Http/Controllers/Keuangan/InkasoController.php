<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use App\Models\KasTransaksi;
use App\Models\PembayaranFaktur;
use App\Models\PenugasanInkaso;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InkasoController extends Controller
{
    /**
     * Dashboard & Antrean Penugasan Lapangan (Inkaso).
     */
    public function index(Request $request)
    {
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

        // Generate No Tugas
        $period = date('Ym');
        $prefixTugas = "TGS-{$period}-";
        $latestTugas = PenugasanInkaso::where('no_tugas', 'like', "{$prefixTugas}%")
            ->orderByRaw('CAST(SUBSTRING(no_tugas, -3) AS UNSIGNED) DESC')
            ->first();

        $nextSeq = 1;
        if ($latestTugas) {
            $parts = explode('-', $latestTugas->no_tugas);
            $nextSeq = ((int) end($parts)) + 1;
        }

        $createdTasks = [];
        foreach ($fakturs as $faktur) {
            if ($faktur->sisa_piutang <= 0) continue;

            $penugasanLama = PenugasanInkaso::where('faktur_id', $faktur->id)
                ->where('status_tugas', 'berjalan')
                ->first();

            if ($penugasanLama) {
                $penugasanLama->update([
                    'nama_petugas'  => $namaPetugasInput,
                    'petugas_id'    => $petugasId,
                    'tanggal_tugas' => $request->tanggal_tugas,
                    'catatan'       => $request->catatan,
                ]);
                $createdTasks[] = $penugasanLama;
            } else {
                do {
                    $noTugas = $prefixTugas . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
                    $nextSeq++;
                } while (PenugasanInkaso::where('no_tugas', $noTugas)->exists());

                $penugasan = PenugasanInkaso::create([
                    'no_tugas'      => $noTugas,
                    'faktur_id'     => $faktur->id,
                    'petugas_id'    => $petugasId,
                    'nama_petugas'  => $namaPetugasInput,
                    'tanggal_tugas' => $request->tanggal_tugas,
                    'status_tugas'  => 'berjalan',
                    'catatan'       => $request->catatan,
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

        return back()->with('success', "Penugasan Inkaso berhasil dibuat untuk {$namaPetugasInput} (" . count($createdTasks) . " faktur).");
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
}
