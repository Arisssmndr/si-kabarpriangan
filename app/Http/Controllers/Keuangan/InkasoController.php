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
        $antreanQuery = Faktur::with(['customer', 'penugasanAktif.petugas'])
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
     */
    public function assign(Request $request)
    {
        $request->validate([
            'faktur_id'     => 'required|exists:faktur,id',
            'petugas_id'    => 'required|exists:users,id',
            'tanggal_tugas' => 'required|date',
            'catatan'       => 'nullable|string',
        ]);

        $faktur = Faktur::findOrFail($request->faktur_id);
        if ($faktur->sisa_piutang <= 0) {
            return back()->with('error', 'Faktur ini sudah lunas, tidak dapat ditugaskan ke Inkaso.');
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

        do {
            $noTugas = $prefixTugas . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
            $nextSeq++;
        } while (PenugasanInkaso::where('no_tugas', $noTugas)->exists());

        $penugasan = PenugasanInkaso::create([
            'no_tugas'      => $noTugas,
            'faktur_id'     => $faktur->id,
            'petugas_id'    => $request->petugas_id,
            'tanggal_tugas' => $request->tanggal_tugas,
            'status_tugas'  => 'berjalan',
            'catatan'       => $request->catatan,
        ]);

        return back()->with('success', "Penugasan Inkaso {$noTugas} berhasil dibuat untuk petugas {$penugasan->petugas->name}.");
    }

    /**
     * Input Setoran Hasil Tagihan Inkaso oleh Kasir/Admin.
     */
    public function storeSetoran(Request $request)
    {
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

            $userId = Auth::id();

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

                // Tutup penugasan inkaso aktif jika ada
                PenugasanInkaso::where('faktur_id', $faktur->id)
                    ->where('status_tugas', 'berjalan')
                    ->update(['status_tugas' => 'selesai']);
            }
            $faktur->save();

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

            $msg = 'Setoran Inkaso Rp ' . number_format($nominal, 0, ',', '.') . ' berhasil diterima dan masuk ke Buku Kas.';
            if ($statusSelesai) {
                $msg .= ' Faktur ' . $faktur->no_faktur . ' kini telah LUNAS 100%! Penugasan inkaso ditutup.';
            }

            return back()->with('success', $msg);
        });
    }

    /**
     * Catat Laporan Hasil Kunjungan Lapangan oleh Staf Keuangan (Bu Ulfa).
     * Mendukung status: Janji Bayar (dengan tanggal janji), Cicilan, Lunas, dan Belum Bayar.
     */
    public function simpanLaporanHasil(Request $request, $id)
    {
        $request->validate([
            'hasil_kunjungan'     => 'required|in:lunas,cicilan,janji_bayar,belum_bayar,tidak_bertemu',
            'tanggal_lapor'       => 'required|date',
            'tanggal_janji_bayar' => 'nullable|date',
            'nominal_diterima'    => 'nullable|numeric|min:0',
            'keterangan_hasil'    => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $tugas = PenugasanInkaso::with(['faktur.customer', 'petugas'])->lockForUpdate()->findOrFail($id);
            $faktur = $tugas->faktur;
            $userId = Auth::id() ?? 1;

            $nominal = (float) ($request->nominal_diterima ?? 0);
            $hasil = $request->hasil_kunjungan;

            $tugas->tanggal_lapor       = $request->tanggal_lapor;
            $tugas->hasil_kunjungan     = $hasil;
            $tugas->tanggal_janji_bayar = $request->tanggal_janji_bayar;
            $tugas->nominal_diterima    = $nominal;
            $tugas->keterangan_hasil    = $request->keterangan_hasil;
            $tugas->dilaporkan_oleh     = $userId;

            // Jika ada penerimaan setoran dana tunai/transfer
            if ($nominal > 0) {
                if ($nominal > (float) $faktur->sisa_piutang) {
                    return back()->with('error', 'Nominal diterima (Rp ' . number_format($nominal, 0, ',', '.') . ') melebihi sisa piutang faktur (Rp ' . number_format($faktur->sisa_piutang, 0, ',', '.') . ').');
                }

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
                    'penugasan_inkaso_id' => $tugas->id,
                    'tanggal_bayar'       => $request->tanggal_lapor,
                    'nominal_bayar'       => $nominal,
                    'metode_bayar'        => 'tunai',
                    'tipe_bayar'          => 'cicilan_inkaso',
                    'diterima_oleh'       => $userId,
                    'catatan'             => $request->keterangan_hasil ?? "Laporan Hasil Penugasan Inkaso ({$hasil})",
                ]);

                $faktur->total_terbayar = (float) $faktur->total_terbayar + $nominal;
                $faktur->sisa_piutang   = max(0, (float) $faktur->sisa_piutang - $nominal);

                if ($faktur->sisa_piutang <= 0 || $hasil === 'lunas') {
                    $faktur->status = 'lunas';
                    $tugas->status_tugas = 'selesai';
                }
                $faktur->save();

                // Mutasi Kas Masuk Otomatis ke Buku Kas
                $kategoriKas = 'Setoran Inkaso Faktur ' . ucfirst($faktur->kategori);
                KasTransaksi::catatMutasi(
                    'masuk',
                    $kategoriKas,
                    $nominal,
                    'Setoran Hasil Inkaso ' . $tugas->no_tugas . ' (' . ($faktur->customer->nama_customer ?? '-') . ')',
                    $bayar,
                    $userId,
                    $request->tanggal_lapor
                );
            } elseif ($hasil === 'lunas' && $faktur->sisa_piutang <= 0) {
                $tugas->status_tugas = 'selesai';
            }

            $tugas->save();

            $msg = "Laporan hasil penugasan {$tugas->no_tugas} berhasil dicatat.";
            if ($hasil === 'janji_bayar' && $request->filled('tanggal_janji_bayar')) {
                $tglJanji = Carbon::parse($request->tanggal_janji_bayar)->translatedFormat('d F Y');
                $msg .= " Catatan Janji Bayar pada {$tglJanji} telah masuk agenda penagihan.";
            } elseif ($nominal > 0) {
                $msg .= " Setoran dana Rp " . number_format($nominal, 0, ',', '.') . " berhasil dibukukan.";
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
     */
    public function cetakSuratTugas($id)
    {
        $penugasan = PenugasanInkaso::with(['faktur.customer', 'petugas'])->findOrFail($id);
        return view('pages.keuangan.inkaso.cetak-surat-tugas', compact('penugasan'));
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
