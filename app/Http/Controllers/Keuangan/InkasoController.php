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

        if ($petugasId) {
            $riwayatQuery->where('petugas_id', $petugasId);
        }

        $riwayatPenugasan = $riwayatQuery->latest('updated_at')->take(50)->get();

        // 4. Daftar Petugas Inkaso untuk Dropdown
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
            'riwayatPenugasan',
            'petugasInkaso',
            'totalPiutangBerjalan',
            'fakturBelumTertugaskan',
            'tugasAktifCount',
            'q'
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
