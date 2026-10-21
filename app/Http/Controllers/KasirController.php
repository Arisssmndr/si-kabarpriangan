<?php

namespace App\Http\Controllers;

use App\Models\Agen;
use App\Models\BuktiPengeluaran;
use App\Models\Faktur;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\KasTransaksi;
use App\Models\Kasbon;
use App\Models\PengajuanAnggaran;
use App\Models\ShiftKasir;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KasirController extends Controller
{
    /**
     * Generator Kalimat Terbilang Rupiah Resmi (Sistem KAS)
     * Contoh: 2500000 -> "Dua Juta Lima Ratus Ribu Rupiah"
     */
    public static function terbilang(float|int $angka): string
    {
        $bilang = function ($n) use (&$bilang) {
            $n = abs((float) $n);
            $baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
            if ($n < 12) {
                return ' ' . $baca[(int) $n];
            } elseif ($n < 20) {
                return $bilang($n - 10) . ' Belas';
            } elseif ($n < 100) {
                return $bilang((int) ($n / 10)) . ' Puluh' . $bilang($n % 10);
            } elseif ($n < 200) {
                return ' Seratus' . $bilang($n - 100);
            } elseif ($n < 1000) {
                return $bilang((int) ($n / 100)) . ' Ratus' . $bilang($n % 100);
            } elseif ($n < 2000) {
                return ' Seribu' . $bilang($n - 1000);
            } elseif ($n < 1000000) {
                return $bilang((int) ($n / 1000)) . ' Ribu' . $bilang(fmod($n, 1000));
            } elseif ($n < 1000000000) {
                return $bilang((int) ($n / 1000000)) . ' Juta' . $bilang(fmod($n, 1000000));
            } elseif ($n < 1000000000000) {
                return $bilang((int) ($n / 1000000000)) . ' Miliar' . $bilang(fmod($n, 1000000000));
            } else {
                return $bilang((int) ($n / 1000000000000)) . ' Triliun' . $bilang(fmod($n, 1000000000000));
            }
        };

        $teks = trim(preg_replace('/\s+/', ' ', $bilang($angka)));
        return $teks ? $teks . ' Rupiah' : 'Nol Rupiah';
    }

    /**
     * Helper: Mendapatkan atau menginisialisasi Shift Kasir yang aktif saat ini (DOS KAS.EXE).
     */
    private function getActiveShift(): ShiftKasir
    {
        $userId = Auth::id() ?? 1;

        $shift = ShiftKasir::where('kasir_id', $userId)
            ->whereIn('status', ['open', 'aktif'])
            ->whereDate('tgl_shift', Carbon::today())
            ->latest('id')
            ->first();

        if (!$shift) {
            $shift = ShiftKasir::where('kasir_id', $userId)
                ->whereIn('status', ['open', 'aktif'])
                ->latest('id')
                ->first();
        }

        if (!$shift) {
            $lastShift = ShiftKasir::where('kasir_id', $userId)
                ->whereIn('status', ['closed', 'ditutup'])
                ->latest('id')
                ->first();

            $saldoAwal = $lastShift ? (float) $lastShift->saldo_akhir_fisik : 1000000.00;

            $shift = ShiftKasir::create([
                'kasir_id'             => $userId,
                'tanggal_shift'        => Carbon::today(),
                'tgl_shift'            => Carbon::today(),
                'saldo_awal_fisik'     => $saldoAwal,
                'total_ki'             => 0,
                'total_ko'             => 0,
                'total_masuk'          => 0,
                'total_keluar'         => 0,
                'saldo_akhir_teoritis' => $saldoAwal,
                'saldo_akhir_fisik'    => $saldoAwal,
                'selisih'              => 0,
                'selisih_kas'          => 0,
                'status'               => 'open',
            ]);
        }

        return $shift;
    }

    /**
     * Helper: Validasi sistem KAS.EXE jika shift hari sebelumnya belum ditutup.
     */
    private function checkPreviousShiftClosed(): ?string
    {
        $userId = Auth::id() ?? 1;
        $unclosed = ShiftKasir::where('kasir_id', $userId)
            ->whereIn('status', ['open', 'aktif'])
            ->where(function($q) {
                $q->whereDate('tanggal_shift', '<', Carbon::today())
                  ->orWhereDate('tgl_shift', '<', Carbon::today());
            })
            ->first();

        if ($unclosed) {
            $tgl = Carbon::parse($unclosed->tanggal_shift ?? $unclosed->tgl_shift)->format('d/m/Y');
            return "Peringatan Sistem KAS: Shift Kasir tanggal {$tgl} belum ditutup (Closing Shift)! Form transaksi terkunci. Harap lakukan Tutup Kas Harian hari sebelumnya terlebih dahulu.";
        }

        return null;
    }

    /**
     * Overview / Dashboard Kasir
     */
    public function dashboard(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $thisMonth = Carbon::now()->month;

        $bayarHariIni = (int) (
            KasMasuk::whereDate('tgl_transaksi', $today)->sum('jumlah_dibayar')
        );

        $txHariIni = (int) (
            KasMasuk::whereDate('tgl_transaksi', $today)->count() +
            KasKeluar::whereDate('tgl_transaksi', $today)->count()
        );

        $totalPenerimaanBulan = (int) (
            KasMasuk::whereMonth('tgl_transaksi', $thisMonth)->sum('jumlah_dibayar')
        );

        // Kasbon gantung & status rekonsiliasi kas fisik
        $totalKasbonGantung = (float) Kasbon::where('status', 'pending')->sum('nominal');
        $countKasbonGantung = Kasbon::where('status', 'pending')->count();
        $countMenungguAcc = Kasbon::where('status', 'menunggu_acc')->count();

        $activeShift = $this->getActiveShift();
        $saldoAwalShift = (float) $activeShift->saldo_awal_fisik;
        $totalMasukShift = (float) KasMasuk::where('shift_id', $activeShift->id)->sum('jumlah_dibayar');
        $totalKeluarShift = (float) KasKeluar::where('shift_id', $activeShift->id)->sum('nominal_keluar');
        $saldoTeoritisShift = $saldoAwalShift + $totalMasukShift - $totalKeluarShift;
        $saldoFisikLaci = max(0, $saldoTeoritisShift - $totalKasbonGantung);

        // Transaksi kas masuk terkini
        $recentKasir = KasMasuk::with('kasir')
            ->latest('id')
            ->take(6)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'faktur'   => $item->no_bkm,
                    'media'    => ucfirst($item->tipe_transaksi),
                    'customer' => $item->nama_pembayar,
                    'bayar'    => (int) $item->jumlah_dibayar,
                    'sisa'     => (int) $item->sisa_piutang,
                    'tanggal'  => $item->tgl_transaksi->toDateString(),
                ];
            });

        return view('pages.kasir.dashboard', compact(
            'bayarHariIni',
            'txHariIni',
            'totalPenerimaanBulan',
            'recentKasir',
            'totalKasbonGantung',
            'countKasbonGantung',
            'countMenungguAcc',
            'saldoTeoritisShift',
            'saldoFisikLaci',
            'activeShift'
        ));
    }

    /**
     * Setoran Iklan (Penerimaan Pelunasan / DP Faktur Iklan)
     */
    public function penerimaanIklan(Request $request)
    {
        // 1. Parameter Request Filter
        $statusPiutang = $request->input('status_piutang', 'belum_lunas'); // belum_lunas, lunas, semua
        $periodeTipe   = $request->input('periode_tipe', 'semua');        // semua, hari, bulan, tahun
        $tanggalFilter = $request->input('tanggal', Carbon::today()->toDateString());
        $bulanFilter   = $request->input('bulan', date('m'));
        $tahunFilter   = $request->input('tahun', date('Y'));
        $saluranMedia  = $request->input('saluran_media', $request->input('media', 'semua'));
        $search        = $request->input('search');

        $fakturList = new Collection();

        // 1. Koran Cetak
        if (in_array($saluranMedia, ['semua', 'all', 'koran'])) {
            $koranQ = TransaksiKoran::with('pelanggan');

            // Filter Status Piutang
            if ($statusPiutang === 'belum_lunas') {
                $koranQ->where('piutang_transaksikoran', '>', 0);
            } elseif ($statusPiutang === 'lunas') {
                $koranQ->where('piutang_transaksikoran', '<=', 0);
            }

            // Filter Periode Tanggal Muat
            if ($periodeTipe === 'hari' && !empty($tanggalFilter)) {
                $koranQ->where(function ($q) use ($tanggalFilter) {
                    $q->whereDate('tanggal_muatkoran', $tanggalFilter)
                      ->orWhere(function ($sub) use ($tanggalFilter) {
                          $sub->whereNull('tanggal_muatkoran')
                              ->whereDate('tanggal_transaksikoran', $tanggalFilter);
                      });
                });
            } elseif ($periodeTipe === 'bulan' && !empty($bulanFilter) && !empty($tahunFilter)) {
                $koranQ->where(function ($q) use ($bulanFilter, $tahunFilter) {
                    $q->where(function ($sub) use ($bulanFilter, $tahunFilter) {
                        $sub->whereYear('tanggal_muatkoran', $tahunFilter)
                            ->whereMonth('tanggal_muatkoran', $bulanFilter);
                    })->orWhere(function ($sub) use ($bulanFilter, $tahunFilter) {
                        $sub->whereNull('tanggal_muatkoran')
                            ->whereYear('tanggal_transaksikoran', $tahunFilter)
                            ->whereMonth('tanggal_transaksikoran', $bulanFilter);
                    });
                });
            } elseif ($periodeTipe === 'tahun' && !empty($tahunFilter)) {
                $koranQ->where(function ($q) use ($tahunFilter) {
                    $q->whereYear('tanggal_muatkoran', $tahunFilter)
                      ->orWhere(function ($sub) use ($tahunFilter) {
                          $sub->whereNull('tanggal_muatkoran')
                              ->whereYear('tanggal_transaksikoran', $tahunFilter);
                      });
                });
            }

            // Quick Search
            if (!empty($search)) {
                $koranQ->where(function ($q) use ($search) {
                    $q->where('nofakturkoran', 'like', "%{$search}%")
                      ->orWhere('nama_pemasangkoran', 'like', "%{$search}%")
                      ->orWhere('tanggal_muatkoran', 'like', "%{$search}%")
                      ->orWhere('tanggal_transaksikoran', 'like', "%{$search}%")
                      ->orWhereHas('pelanggan', function ($sub) use ($search) {
                          $sub->where('nama_instansi', 'like', "%{$search}%")
                              ->orWhere('kode_pelanggan', 'like', "%{$search}%");
                      });
                });
            }

            foreach ($koranQ->latest('id')->get() as $item) {
                $tglMuat = $item->tanggal_muatkoran 
                    ? Carbon::parse($item->tanggal_muatkoran)->toDateString() 
                    : ($item->tanggal_transaksikoran ? Carbon::parse($item->tanggal_transaksikoran)->toDateString() : Carbon::today()->toDateString());
                $kodeRekening = $item->pelanggan->kode_pelanggan ?? '1.00.04.01.01.01';
                $deskripsiTayang = 'Muat ' . ($item->total_muatkoran ?? 1) . ' Edisi (' . Carbon::parse($tglMuat)->format('d/m/Y') . ')' . ($item->produk_jasa ? ' • ' . $item->produk_jasa : ($item->ukuran_iklan ? ' • ' . $item->ukuran_iklan : ''));

                $fakturList->push((object)[
                    'id'               => $item->id,
                    'type'             => 'koran',
                    'no_faktur'        => $item->nofakturkoran,
                    'faktur'           => $item->nofakturkoran,
                    'media'            => 'Koran Cetak',
                    'nama_klien'       => $item->nama_pemasangkoran,
                    'customer'         => $item->nama_pemasangkoran,
                    'kode_rekening'    => $kodeRekening,
                    'kode_perkiraan'   => $kodeRekening,
                    'tanggal_muat'     => $tglMuat,
                    'deskripsi_tayang' => $deskripsiTayang,
                    'sales'            => $item->sales_iklankoran ?? '-',
                    'total_tagihan'    => (int) $item->totaltagihan_transaksikoran,
                    'totaltagihan'     => (int) $item->totaltagihan_transaksikoran,
                    'sudah_dibayar'    => (int) $item->jumlahbayar_transaksikoran,
                    'jumlahbayar'      => (int) $item->jumlahbayar_transaksikoran,
                    'sisa_piutang'     => (int) $item->piutang_transaksikoran,
                    'piutang'          => (int) $item->piutang_transaksikoran,
                    'status_order'     => $item->status_order ?? ($item->piutang_transaksikoran <= 0 ? 'Lunas' : 'Belum Lunas'),
                    'siap_cair'        => (bool) ($item->siap_cair_kh_insentif || $item->piutang_transaksikoran <= 0),
                ]);
            }
        }

        // 2. Media Online
        if (in_array($saluranMedia, ['semua', 'all', 'online'])) {
            $onlineQ = TransaksiOnline::query();

            // Filter Status Piutang
            if ($statusPiutang === 'belum_lunas') {
                $onlineQ->where('piutang_transaksionline', '>', 0);
            } elseif ($statusPiutang === 'lunas') {
                $onlineQ->where('piutang_transaksionline', '<=', 0);
            }

            // Filter Periode Tanggal Muat / Tayang
            if ($periodeTipe === 'hari' && !empty($tanggalFilter)) {
                $onlineQ->where(function ($q) use ($tanggalFilter) {
                    $q->whereDate('tanggal_muatiklanonline', $tanggalFilter)
                      ->orWhere(function ($sub) use ($tanggalFilter) {
                          $sub->whereNull('tanggal_muatiklanonline')
                              ->whereDate('tanggal_transaksionline', $tanggalFilter);
                      });
                });
            } elseif ($periodeTipe === 'bulan' && !empty($bulanFilter) && !empty($tahunFilter)) {
                $onlineQ->where(function ($q) use ($bulanFilter, $tahunFilter) {
                    $q->where(function ($sub) use ($bulanFilter, $tahunFilter) {
                        $sub->whereYear('tanggal_muatiklanonline', $tahunFilter)
                            ->whereMonth('tanggal_muatiklanonline', $bulanFilter);
                    })->orWhere(function ($sub) use ($bulanFilter, $tahunFilter) {
                        $sub->whereNull('tanggal_muatiklanonline')
                            ->whereYear('tanggal_transaksionline', $tahunFilter)
                            ->whereMonth('tanggal_transaksionline', $bulanFilter);
                    });
                });
            } elseif ($periodeTipe === 'tahun' && !empty($tahunFilter)) {
                $onlineQ->where(function ($q) use ($tahunFilter) {
                    $q->whereYear('tanggal_muatiklanonline', $tahunFilter)
                      ->orWhere(function ($sub) use ($tahunFilter) {
                          $sub->whereNull('tanggal_muatiklanonline')
                              ->whereYear('tanggal_transaksionline', $tahunFilter);
                      });
                });
            }

            // Quick Search
            if (!empty($search)) {
                $onlineQ->where(function ($q) use ($search) {
                    $q->where('nofakturonline', 'like', "%{$search}%")
                      ->orWhere('nama_pemasangonline', 'like', "%{$search}%")
                      ->orWhere('tanggal_muatiklanonline', 'like', "%{$search}%")
                      ->orWhere('tanggal_transaksionline', 'like', "%{$search}%")
                      ->orWhere('portal_iklanonline', 'like', "%{$search}%");
                });
            }

            foreach ($onlineQ->latest('id')->get() as $item) {
                $tglMuat = $item->tanggal_muatiklanonline 
                    ? Carbon::parse($item->tanggal_muatiklanonline)->toDateString() 
                    : ($item->tanggal_transaksionline ? Carbon::parse($item->tanggal_transaksionline)->toDateString() : Carbon::today()->toDateString());
                $deskripsiTayang = 'Tayang Online (' . Carbon::parse($tglMuat)->format('d/m/Y') . ')' . ($item->portal_iklanonline ? ' • ' . $item->portal_iklanonline : '');

                $fakturList->push((object)[
                    'id'               => $item->id,
                    'type'             => 'online',
                    'no_faktur'        => $item->nofakturonline,
                    'faktur'           => $item->nofakturonline,
                    'media'            => 'Media Online',
                    'nama_klien'       => $item->nama_pemasangonline,
                    'customer'         => $item->nama_pemasangonline,
                    'kode_rekening'    => '1.00.04.01.01.02',
                    'kode_perkiraan'   => '1.00.04.01.01.02',
                    'tanggal_muat'     => $tglMuat,
                    'deskripsi_tayang' => $deskripsiTayang,
                    'sales'            => $item->sales_iklanonline ?? '-',
                    'total_tagihan'    => (int) $item->totaltagihan_transaksionline,
                    'totaltagihan'     => (int) $item->totaltagihan_transaksionline,
                    'sudah_dibayar'    => (int) $item->jumlahbayar_transaksionline,
                    'jumlahbayar'      => (int) $item->jumlahbayar_transaksionline,
                    'sisa_piutang'     => (int) $item->piutang_transaksionline,
                    'piutang'          => (int) $item->piutang_transaksionline,
                    'status_order'     => $item->piutang_transaksionline <= 0 ? 'Lunas' : 'Belum Lunas',
                    'siap_cair'        => (bool) ($item->piutang_transaksionline <= 0),
                ]);
            }
        }

        // 3. Priangan TV
        if (in_array($saluranMedia, ['semua', 'all', 'tv'])) {
            $tvQ = TransaksiPriangan::query();

            // Filter Status Piutang
            if ($statusPiutang === 'belum_lunas') {
                $tvQ->where('piutang_transaksipriangan', '>', 0);
            } elseif ($statusPiutang === 'lunas') {
                $tvQ->where('piutang_transaksipriangan', '<=', 0);
            }

            // Filter Periode Tanggal Muat / Tayang
            if ($periodeTipe === 'hari' && !empty($tanggalFilter)) {
                $tvQ->where(function ($q) use ($tanggalFilter) {
                    $q->whereDate('tanggal_muatiklanpriangan', $tanggalFilter)
                      ->orWhere(function ($sub) use ($tanggalFilter) {
                          $sub->whereNull('tanggal_muatiklanpriangan')
                              ->whereDate('tanggal_transaksipriangan', $tanggalFilter);
                      });
                });
            } elseif ($periodeTipe === 'bulan' && !empty($bulanFilter) && !empty($tahunFilter)) {
                $tvQ->where(function ($q) use ($bulanFilter, $tahunFilter) {
                    $q->where(function ($sub) use ($bulanFilter, $tahunFilter) {
                        $sub->whereYear('tanggal_muatiklanpriangan', $tahunFilter)
                            ->whereMonth('tanggal_muatiklanpriangan', $bulanFilter);
                    })->orWhere(function ($sub) use ($bulanFilter, $tahunFilter) {
                        $sub->whereNull('tanggal_muatiklanpriangan')
                            ->whereYear('tanggal_transaksipriangan', $tahunFilter)
                            ->whereMonth('tanggal_transaksipriangan', $bulanFilter);
                    });
                });
            } elseif ($periodeTipe === 'tahun' && !empty($tahunFilter)) {
                $tvQ->where(function ($q) use ($tahunFilter) {
                    $q->whereYear('tanggal_muatiklanpriangan', $tahunFilter)
                      ->orWhere(function ($sub) use ($tahunFilter) {
                          $sub->whereNull('tanggal_muatiklanpriangan')
                              ->whereYear('tanggal_transaksipriangan', $tahunFilter);
                      });
                });
            }

            // Quick Search
            if (!empty($search)) {
                $tvQ->where(function ($q) use ($search) {
                    $q->where('nofakturpriangan', 'like', "%{$search}%")
                      ->orWhere('nama_pemasangpriangan', 'like', "%{$search}%")
                      ->orWhere('tanggal_muatiklanpriangan', 'like', "%{$search}%")
                      ->orWhere('tanggal_transaksipriangan', 'like', "%{$search}%");
                });
            }

            foreach ($tvQ->latest('id')->get() as $item) {
                $tglMuat = $item->tanggal_muatiklanpriangan 
                    ? Carbon::parse($item->tanggal_muatiklanpriangan)->toDateString() 
                    : ($item->tanggal_transaksipriangan ? Carbon::parse($item->tanggal_transaksipriangan)->toDateString() : Carbon::today()->toDateString());
                $deskripsiTayang = 'Tayang TV (' . Carbon::parse($tglMuat)->format('d/m/Y') . ') • Priangan TV';

                $fakturList->push((object)[
                    'id'               => $item->id,
                    'type'             => 'tv',
                    'no_faktur'        => $item->nofakturpriangan,
                    'faktur'           => $item->nofakturpriangan,
                    'media'            => 'Priangan TV',
                    'nama_klien'       => $item->nama_pemasangpriangan,
                    'customer'         => $item->nama_pemasangpriangan,
                    'kode_rekening'    => '1.00.04.01.01.03',
                    'kode_perkiraan'   => '1.00.04.01.01.03',
                    'tanggal_muat'     => $tglMuat,
                    'deskripsi_tayang' => $deskripsiTayang,
                    'sales'            => $item->sales_iklanpriangan ?? '-',
                    'total_tagihan'    => (int) $item->totaltagihan_transaksipriangan,
                    'totaltagihan'     => (int) $item->totaltagihan_transaksipriangan,
                    'sudah_dibayar'    => (int) $item->jumlahbayar_transaksipriangan,
                    'jumlahbayar'      => (int) $item->jumlahbayar_transaksipriangan,
                    'sisa_piutang'     => (int) $item->piutang_transaksipriangan,
                    'piutang'          => (int) $item->piutang_transaksipriangan,
                    'status_order'     => $item->piutang_transaksipriangan <= 0 ? 'Lunas' : 'Belum Lunas',
                    'siap_cair'        => (bool) ($item->piutang_transaksipriangan <= 0),
                ]);
            }
        }

        // Urutkan berdasarkan tanggal_muat terbaru
        $fakturList = $fakturList->sortByDesc('tanggal_muat')->values();

        // Ringkasan Statistik Hasil Filter
        $stats = [
            'total_faktur'   => $fakturList->count(),
            'total_tagihan'  => $fakturList->sum('total_tagihan'),
            'total_dibayar'  => $fakturList->sum('sudah_dibayar'),
            'total_piutang'  => $fakturList->sum('sisa_piutang'),
            'faktur_piutang' => $fakturList->where('sisa_piutang', '>', 0)->count(),
            'faktur_lunas'   => $fakturList->where('sisa_piutang', '<=', 0)->count(),
        ];

        // 4. Daftar Petugas Inkaso untuk Dropdown
        $petugasInkasoList = User::whereIn('role', ['petugas_inkaso', 'penagih', 'kasir', 'admin_keuangan'])
            ->orderBy('name')
            ->get();

        if ($petugasInkasoList->isEmpty()) {
            $petugasInkasoList = collect([
                (object)['name' => 'Ahmad Fauzi (Inkaso Lapangan)'],
                (object)['name' => 'Rudi Hermawan (Inkaso Lapangan)'],
                (object)['name' => 'Dadan Ramdani (Inkaso Lapangan)'],
                (object)['name' => 'Petugas Loket Kasir Kantor'],
            ]);
        }

        $mediaFilter = $saluranMedia;

        return view('pages.kasir.penerimaan.iklan', compact(
            'fakturList',
            'search',
            'statusPiutang',
            'periodeTipe',
            'tanggalFilter',
            'bulanFilter',
            'tahunFilter',
            'saluranMedia',
            'mediaFilter',
            'petugasInkasoList',
            'stats'
        ));
    }

    /**
     * Process Payment for Iklan dengan Integrasi Database Transaction
     * Realisasi Setoran Iklan:
     * - Simpan ke kas_masuks
     * - Kurangi sisa_piutang & update status_bayar ('lunas'/'uang_muka')
     * - JIKA LUNAS: Update siap_cair_kh_insentif = true di modul Iklan/Anggaran
     */
    public function storePenerimaanIklan(Request $request)
    {
        if ($unclosedMsg = $this->checkPreviousShiftClosed()) {
            return redirect()->back()->with('error', $unclosedMsg);
        }

        $request->validate([
            'type'                  => 'required|in:koran,online,tv,faktur',
            'id'                    => 'required|integer',
            'nominal_bayar'         => 'required|numeric|min:1',
            'metode_pembayaran'     => 'required|string|in:tunai,transfer,qris',
            'petugas_inkaso'        => 'nullable|string',
            'bank_tujuan'           => 'nullable|string',
            'no_referensi_transfer' => 'nullable|string',
            'no_blanko'             => 'nullable|string',
            'kode_perkiraan'        => 'nullable|string',
            'is_instansi_tax'       => 'nullable|boolean',
            'potongan_pajak'        => 'nullable|numeric|min:0',
            'keterangan'            => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $shift = $this->getActiveShift();
            $nominalGross = (float) $request->input('nominal_bayar');
            $isInstansiTax = $request->boolean('is_instansi_tax') || $request->input('is_instansi_tax') === '1' || $request->input('is_instansi_tax') === 'true';
            $userId = Auth::id() ?? 1;

            // Logika Bisnis Pajak Instansi Pemerintah (KAS.EXE & Rekap Kas HU Priangan):
            // Jika instansi (DPRD/Pemda/Setwan): DPP = Bruto * 100 / 111, PPN = 11% DPP, PPh 23 = 2% DPP
            $nilaiDpp = 0;
            $potonganPpn = 0;
            $potonganPph23 = 0;
            $totalPotonganPajak = 0;
            $uangFisikDiterima = $nominalGross;

            if ($isInstansiTax) {
                $nilaiDpp = round(($nominalGross * 100) / 111, 2);
                $potonganPpn = round($nilaiDpp * 0.11, 2);
                $potonganPph23 = round($nilaiDpp * 0.02, 2);
                $totalPotonganPajak = round($potonganPpn + $potonganPph23, 2);
                $uangFisikDiterima = round($nominalGross - $totalPotonganPajak, 2);
            } else {
                $totalPotonganPajak = (float) ($request->input('potongan_pajak') ?? 0);
                $uangFisikDiterima = $nominalGross;
            }

            // Tagihan faktur dilunasi secara bulat (gross) oleh kasir
            $totalPengurangFaktur = $nominalGross;

            $namaPembayar = 'Pelanggan Iklan';
            $fakturNo = '-';
            $totalTagihan = 0;
            $sisaPiutang = 0;
            $mediaType = $request->type;

            // 1. Update Database Modul Iklan Terkait
            if ($request->type === 'koran') {
                $trx = TransaksiKoran::lockForUpdate()->findOrFail($request->id);
                $fakturNo = $trx->nofakturkoran;
                $namaPembayar = $trx->nama_pemasangkoran;
                $totalTagihan = (float) $trx->totaltagihan_transaksikoran;

                $trx->jumlahbayar_transaksikoran += $totalPengurangFaktur;
                $sisaPiutang = max(0, $totalTagihan - (float) $trx->jumlahbayar_transaksikoran);
                $trx->piutang_transaksikoran = $sisaPiutang;

                // JIKA LUNAS: Update status order dan buka komisi KH & Insentif AE
                if ($sisaPiutang <= 0) {
                    $trx->status_order = 'Lunas';
                    if (\Illuminate\Support\Facades\Schema::hasColumn($trx->getTable(), 'siap_cair_kh_insentif')) {
                        $trx->siap_cair_kh_insentif = true; // Komisi KH & Insentif dapat dicairkan di Divisi Kasir
                    }
                }
                $trx->save();

                // Sinkronkan ke tabel Faktur jika ada
                $faktur = Faktur::where('no_faktur', $fakturNo)->first();
                if ($faktur) {
                    $faktur->total_terbayar += $totalPengurangFaktur;
                    $faktur->sisa_piutang = max(0, (float) $faktur->total_tagihan - (float) $faktur->total_terbayar);
                    $faktur->status = $faktur->sisa_piutang <= 0 ? 'lunas' : 'cicilan';
                    $faktur->status_bayar = $faktur->sisa_piutang <= 0 ? 'lunas' : 'uang_muka';
                    if ($faktur->sisa_piutang <= 0 && \Illuminate\Support\Facades\Schema::hasColumn($faktur->getTable(), 'siap_cair_kh_insentif')) {
                        $faktur->siap_cair_kh_insentif = true;
                    }
                    $faktur->save();
                }

            } elseif ($request->type === 'online') {
                $trx = TransaksiOnline::lockForUpdate()->findOrFail($request->id);
                $fakturNo = $trx->nofakturonline;
                $namaPembayar = $trx->nama_pemasangonline;
                $totalTagihan = (float) $trx->totaltagihan_transaksionline;

                $trx->jumlahbayar_transaksionline += $totalPengurangFaktur;
                $sisaPiutang = max(0, $totalTagihan - (float) $trx->jumlahbayar_transaksionline);
                $trx->piutang_transaksionline = $sisaPiutang;
                if ($sisaPiutang <= 0 && \Illuminate\Support\Facades\Schema::hasColumn($trx->getTable(), 'siap_cair_kh_insentif')) {
                    $trx->siap_cair_kh_insentif = true;
                }
                $trx->save();

            } elseif ($request->type === 'tv') {
                $trx = TransaksiPriangan::lockForUpdate()->findOrFail($request->id);
                $fakturNo = $trx->nofakturpriangan;
                $namaPembayar = $trx->nama_pemasangpriangan;
                $totalTagihan = (float) $trx->totaltagihan_transaksipriangan;

                $trx->jumlahbayar_transaksipriangan += $totalPengurangFaktur;
                $sisaPiutang = max(0, $totalTagihan - (float) $trx->jumlahbayar_transaksipriangan);
                $trx->piutang_transaksipriangan = $sisaPiutang;
                if ($sisaPiutang <= 0 && \Illuminate\Support\Facades\Schema::hasColumn($trx->getTable(), 'siap_cair_kh_insentif')) {
                    $trx->siap_cair_kh_insentif = true;
                }
                $trx->save();

            } else {
                $faktur = Faktur::lockForUpdate()->findOrFail($request->id);
                $fakturNo = $faktur->no_faktur;
                $namaPembayar = $faktur->customer->nama_customer ?? 'Klien Faktur';
                $totalTagihan = (float) $faktur->total_tagihan;

                $faktur->total_terbayar += $totalPengurangFaktur;
                $sisaPiutang = max(0, $totalTagihan - (float) $faktur->total_terbayar);
                $faktur->sisa_piutang = $sisaPiutang;
                $faktur->status = $sisaPiutang <= 0 ? 'lunas' : 'cicilan';
                $faktur->status_bayar = $sisaPiutang <= 0 ? 'lunas' : 'uang_muka';
                if ($sisaPiutang <= 0 && \Illuminate\Support\Facades\Schema::hasColumn($faktur->getTable(), 'siap_cair_kh_insentif')) {
                    $faktur->siap_cair_kh_insentif = true;
                }
                $faktur->save();
            }

            // 2. Terbitkan Nomor Kuitansi Kas Masuk Format Asli DOS KAS.EXE (KI-01-YYYYMMXXXX)
            $noKuitansi = KasMasuk::generateNoKuitansi('01');
            $kodePerkiraan = $request->kode_perkiraan ?? ($sisaPiutang <= 0 ? '1.00.04.01.01.01' : '2.00.01.01.01.01');
            $namaPerkiraan = $sisaPiutang <= 0 ? 'Piutang Iklan' : 'Uang Muka/Titipan Iklan';

            // 3. Simpan Transaksi Kasir ke tabel kas_masuks (Nilai Bruto Penuh dicatat di Kas Masuk)
            $kasMasuk = KasMasuk::create([
                'no_kuitansi'           => $noKuitansi,
                'no_bkm'                => $noKuitansi,
                'shift_id'              => $shift->id,
                'tanggal_terima'        => Carbon::today(),
                'tgl_transaksi'         => Carbon::today(),
                'no_blanko'             => $request->no_blanko,
                'kode_bagian'           => '01',
                'kode_perkiraan'        => $kodePerkiraan,
                'nama_perkiraan'        => $namaPerkiraan,
                'terima_dari'           => $namaPembayar,
                'nama_pembayar'         => $namaPembayar,
                'penagih_inkaso'        => $request->petugas_inkaso,
                'no_faktur_ref'         => $fakturNo,
                'tipe_transaksi'        => 'iklan',
                'ref_id'                => $request->id,
                'ref_type'              => $mediaType,
                'metode_bayar'          => $request->metode_pembayaran,
                'bank_tujuan'           => $request->bank_tujuan,
                'no_referensi_transfer' => $request->no_referensi_transfer,
                'jumlah_tagihan'        => $totalTagihan,
                'total_tagihan'         => $totalTagihan,
                'nilai_dpp'             => $nilaiDpp,
                'potongan_ppn'          => $potonganPpn,
                'potongan_pph23'        => $potonganPph23,
                'is_instansi_tax'       => $isInstansiTax,
                'potongan_pajak'        => $totalPotonganPajak,
                'potongan_retur'        => 0,
                'jumlah_diterima'       => $uangFisikDiterima,
                'jumlah_dibayar'        => $nominalGross,
                'sisa_piutang'          => $sisaPiutang,
                'status_bayar'          => $sisaPiutang <= 0 ? 'lunas' : 'uang_muka',
                'status_pembayaran'     => $sisaPiutang <= 0 ? 'lunas' : 'sebagian',
                'keterangan'            => $request->keterangan ?? ("Penerimaan Kasir Iklan Faktur {$fakturNo} ({$namaPembayar})" . ($totalPotonganPajak > 0 ? " [Potong Pajak: Rp " . number_format($totalPotonganPajak, 0, ',', '.') . "]" : '')),
                'kasir_id'              => $userId,
                'user_id'               => $userId,
                'is_closed'             => false,
                'is_pulled_accounting'  => false,
                'status_tarik_akunting' => false,
            ]);

            // 4. OTOMATIS: Terbitkan Kas Keluar (KO) Penyeimbang Potongan Pajak & PPN
            // Sesuai prinsip akuntansi KAS: Jika ada PPN atau pajak dipotong oleh klien/dinas,
            // potongan tersebut otomatis masuk ke KO (Kas Keluar) penyeimbang agar buku kas tetap balance
            $noBkkPajak = null;
            $kasKeluarPajak = null;
            if ($totalPotonganPajak > 0) {
                $noBkkPajak = KasKeluar::generateNoBkk();
                $uraianPajak = $isInstansiTax 
                    ? "Beban Pajak Instansi WAPU Faktur {$fakturNo} (DPP: Rp " . number_format($nilaiDpp, 0, ',', '.') . " | PPN 11%: Rp " . number_format($potonganPpn, 0, ',', '.') . " | PPh 23: Rp " . number_format($potonganPph23, 0, ',', '.') . ")"
                    : "Beban Potongan Pajak / PPN Faktur {$fakturNo} ({$namaPembayar})";

                $namaPerkiraanPajak = $isInstansiTax
                    ? 'Beban Potongan Pajak Instansi (PPN 11% + PPh 23 2%)'
                    : 'Beban Potongan Pajak / PPN';

                $kasKeluarPajak = KasKeluar::create([
                    'no_bkk'                => $noBkkPajak,
                    'shift_id'              => $shift->id,
                    'tanggal_keluar'        => Carbon::today(),
                    'tgl_transaksi'         => Carbon::today(),
                    'no_bp_ref'             => $fakturNo,
                    'no_register_anggaran'  => 'TAX-' . Carbon::today()->format('Ymd') . '-' . str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'kode_bagian'           => '01', // Divisi Iklan (Sesuai Sumber Transaksi Pelunasan Iklan)
                    'kode_perkiraan'        => '2.00.03.01.01',
                    'nama_perkiraan'        => $namaPerkiraanPajak,
                    'diberikan_kepada'      => 'KAS NEGARA / DINAS ' . strtoupper($namaPembayar),
                    'nama_penerima'         => 'KAS NEGARA / DINAS ' . strtoupper($namaPembayar),
                    'kategori_beban'        => 'pajak_instansi',
                    'kategori'              => 'umum',
                    'jumlah_keluar'         => $totalPotonganPajak,
                    'nominal_keluar'        => $totalPotonganPajak,
                    'metode_bayar'          => 'kas_fisik',
                    'is_tax_offset'         => true,
                    'is_kasbon'             => false,
                    'keterangan'            => $uraianPajak,
                    'kasir_id'              => $userId,
                    'user_id'               => $userId,
                    'is_closed'             => false,
                    'is_pulled_accounting'  => false,
                    'status_tarik_akunting' => false,
                ]);

                // Update total KO di shift
                $shift->total_ko += $totalPotonganPajak;
                $shift->total_keluar += $totalPotonganPajak;

                // Catat mutasi keluar di KasTransaksi (Treasury)
                KasTransaksi::catatMutasi(
                    'keluar',
                    'Potongan Pajak & PPN (WAPU/Instansi)',
                    $totalPotonganPajak,
                    "KO {$noBkkPajak} - {$uraianPajak}",
                    $kasKeluarPajak,
                    $userId,
                    Carbon::today()->toDateString()
                );
            }

            // 5. Update Shift Kasir (DOS Field Sync)
            $shift->total_ki += $nominalGross;
            $shift->total_masuk += $nominalGross;
            $shift->saldo_akhir_teoritis = $shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko;
            $shift->saldo_akhir_fisik = $shift->saldo_akhir_teoritis;
            $shift->save();

            // 6. Catat Mutasi Kas Masuk ke Buku Kas Treasury
            KasTransaksi::catatMutasi(
                'masuk',
                'Pelunasan Iklan ' . ucfirst($mediaType),
                $nominalGross,
                "KI {$noKuitansi} - Pelunasan Faktur {$fakturNo} ({$namaPembayar})" . ($isInstansiTax ? " [Dipotong Pajak Rp " . number_format($totalPotonganPajak, 0, ',', '.') . "]" : ''),
                $kasMasuk,
                $userId,
                Carbon::today()->toDateString()
            );

            // Payload untuk auto-popup modal cetak Kuitansi / BKM
            $cetakData = [
                'no_kuitansi'     => $noKuitansi,
                'no_bkm'          => $noKuitansi,
                'kode_bagian'     => '01',
                'kode_perkiraan'  => $kodePerkiraan,
                'faktur'          => $fakturNo,
                'customer'        => $namaPembayar,
                'nominal'         => $nominalGross,
                'nominal_fisik'   => $uangFisikDiterima,
                'terbilang'       => self::terbilang($isInstansiTax ? $uangFisikDiterima : $nominalGross),
                'terbilang_gross' => self::terbilang($nominalGross),
                'metode'          => strtoupper($request->metode_pembayaran),
                'bank'            => $request->bank_tujuan ?? '-',
                'no_ref'          => $request->no_referensi_transfer ?? '-',
                'penagih'         => $request->petugas_inkaso ?? 'Kasir Langsung',
                'is_instansi_tax' => $isInstansiTax,
                'dpp'             => $nilaiDpp,
                'ppn'             => $potonganPpn,
                'pph23'           => $potonganPph23,
                'pajak'           => $totalPotonganPajak,
                'no_bkk_pajak'    => $noBkkPajak,
                'sisa_piutang'    => $sisaPiutang,
                'status_bayar'    => $sisaPiutang <= 0 ? 'LUNAS' : 'SEBAGIAN / DP',
                'tanggal'         => Carbon::now()->translatedFormat('d F Y, H:i'),
                'kasir'           => Auth::user()->name ?? 'Kasir Kabar Priangan',
            ];

            $cetakBkkPajak = null;
            if ($totalPotonganPajak > 0 && $noBkkPajak) {
                $cetakBkkPajak = [
                    'no_bkk'         => $noBkkPajak,
                    'no_bp'          => $fakturNo,
                    'no_register'    => $kasKeluarPajak->no_register_anggaran ?? ('TAX-' . Carbon::today()->format('Ymd')),
                    'penerima'       => 'KAS NEGARA / DINAS ' . strtoupper($namaPembayar),
                    'divisi'         => 'Divisi Iklan',
                    'kode_bagian'    => '01',
                    'kode_perkiraan' => '2.00.03.01.01',
                    'keperluan'      => $uraianPajak,
                    'metode'         => 'Potongan Faktur (Non-Fisik)',
                    'nominal'        => $totalPotonganPajak,
                    'terbilang'      => self::terbilang($totalPotonganPajak),
                    'approver'       => 'Sistem KAS / WAPU Instansi',
                    'kasir'          => Auth::user()->name ?? 'Kasir Kabar Priangan',
                    'tanggal'        => Carbon::now()->translatedFormat('d F Y, H:i'),
                ];
            }

            $taxInfoMsg = $totalPotonganPajak > 0
                ? " (Potongan Pajak/PPN sebesar Rp " . number_format($totalPotonganPajak, 0, ',', '.') . " telah otomatis masuk ke Kas Keluar [KO] Penyeimbang {$noBkkPajak})"
                : "";

            return redirect()->back()
                ->with('success', "Bukti Kas Masuk ({$noKuitansi}) untuk faktur {$fakturNo} sebesar Rp " . number_format($nominalGross, 0, ',', '.') . " berhasil disimpan!{$taxInfoMsg}")
                ->with('cetak_bkm', $cetakData)
                ->with('cetak_bkk_pajak', $cetakBkkPajak);
        });
    }

    /**
     * Setoran Sirkulasi Koran
     */
    public function penerimaanSirkulasi(Request $request)
    {
        $statusPiutang  = $request->input('status_piutang', 'belum_lunas');
        $bulanPilihan   = $request->input('bulan', Carbon::now()->format('Y-m'));
        $search         = $request->input('search');
        $kodeAgenFilter = $request->input('kode_agen');

        try {
            $carbonBulan = Carbon::parse($bulanPilihan . '-01');
        } catch (\Exception $e) {
            $bulanPilihan = Carbon::now()->format('Y-m');
            $carbonBulan = Carbon::parse($bulanPilihan . '-01');
        }

        $periodeLabel   = $carbonBulan->translatedFormat('F Y');
        $isCurrentMonth = ($bulanPilihan === Carbon::now()->format('Y-m'));
        $prevMonth      = $carbonBulan->copy()->subMonth()->format('Y-m');
        $nextMonth      = $carbonBulan->copy()->addMonth()->format('Y-m');

        // Ambil data agen dari database Master Agen Sirkulasi
        $agenQuery = Agen::query();
        if ($kodeAgenFilter) {
            $agenQuery->where('kode_agen', $kodeAgenFilter);
        } elseif ($search) {
            $agenQuery->where(function($q) use ($search) {
                $q->where('kode_agen', 'like', "%{$search}%")
                  ->orWhere('nama_agen', 'like', "%{$search}%")
                  ->orWhere('wilayah', 'like', "%{$search}%");
            });
        }
        $rawAgenList = $agenQuery->orderBy('kode_agen')->get();

        $year = (int) $carbonBulan->format('Y');
        $month = (int) $carbonBulan->format('m');

        // Hitung saldo piutang & status transaksi untuk bulan yang dipilih
        $allProcessedAgens = $rawAgenList->map(function ($agen) use ($isCurrentMonth, $year, $month) {
            // Cek setoran kas masuk pada bulan terpilih
            $transaksiBulan = KasMasuk::where('tipe_transaksi', 'sirkulasi')
                ->where(function ($q) use ($agen) {
                    $q->where('ref_id', $agen->id)
                      ->orWhere('kode_agen_ref', $agen->kode_agen);
                })
                ->whereYear('tgl_transaksi', $year)
                ->whereMonth('tgl_transaksi', $month)
                ->orderBy('id', 'desc')
                ->get();

            $totalDisetorBulan = (float) $transaksiBulan->sum('jumlah_dibayar');
            $lastTransaksi = $transaksiBulan->first();

            if ($isCurrentMonth) {
                // Bulan berjalan: saldo piutang langsung dari data master agen saat ini
                $saldoPiutangBulan = (float) $agen->saldo_piutang;
            } else {
                // Bulan sebelumnya / arsip:
                if ($lastTransaksi) {
                    $saldoPiutangBulan = (float) $lastTransaksi->sisa_piutang;
                } else {
                    // Jika tidak ada transaksi sama sekali di bulan lampau, anggap lunas (arsip tertutup)
                    $saldoPiutangBulan = 0.0;
                }
            }

            $agen->saldo_piutang_bulan = $saldoPiutangBulan;
            $agen->total_setoran_bulan = $totalDisetorBulan;
            $agen->transaksi_terakhir  = $lastTransaksi;
            $agen->is_lunas_bulan      = ($saldoPiutangBulan <= 0);

            return $agen;
        });

        // Hitung Ringkasan KPI sebelum filter status tagihan diterapkan
        $totalPlafon       = (float) $allProcessedAgens->sum('plafon_piutang');
        $totalPiutangBulan = (float) $allProcessedAgens->sum('saldo_piutang_bulan');
        $totalJatah        = (int) $allProcessedAgens->sum('jatah_default');

        // Terapkan Filter Status Tagihan: belum_lunas, lunas, semua
        if ($statusPiutang === 'belum_lunas') {
            $agenList = $allProcessedAgens->filter(fn($a) => $a->saldo_piutang_bulan > 0);
        } elseif ($statusPiutang === 'lunas') {
            $agenList = $allProcessedAgens->filter(fn($a) => $a->saldo_piutang_bulan <= 0);
        } else {
            $agenList = $allProcessedAgens;
        }

        $petugasInkasoList = User::whereIn('role', ['petugas_inkaso', 'penagih', 'sirkulasi', 'kasir'])
            ->orderBy('name')
            ->get();

        if ($petugasInkasoList->isEmpty()) {
            $petugasInkasoList = collect([
                (object)['name' => 'Randi Kurnia (Inkaso Sirkulasi)'],
                (object)['name' => 'Asep Saepuloh (Koordinator Armada)'],
                (object)['name' => 'Petugas Setoran Kasir'],
            ]);
        }

        return view('pages.kasir.penerimaan.sirkulasi', compact(
            'agenList',
            'petugasInkasoList',
            'search',
            'kodeAgenFilter',
            'statusPiutang',
            'bulanPilihan',
            'periodeLabel',
            'isCurrentMonth',
            'prevMonth',
            'nextMonth',
            'totalPlafon',
            'totalPiutangBulan',
            'totalJatah'
        ));
    }

    /**
     * Process Setoran Sirkulasi dengan Validasi BAPR & Update Piutang Agen
     * Realisasi Setoran Sirkulasi:
     * - Simpan data ke kas_masuks dengan validasi no BAPR (retur koran)
     * - Update status piutang tagihan agen di modul Sirkulasi
     * - Cetakan kuitansi hanya memuat nama agen, kode agen, jumlah, terbilang huruf, dan kasir penerima (tanpa saldo piutang masa lalu)
     */
    public function storePenerimaanSirkulasi(Request $request)
    {
        if ($unclosedMsg = $this->checkPreviousShiftClosed()) {
            return redirect()->back()->with('error', $unclosedMsg);
        }

        $request->validate([
            'agen_id'               => 'nullable|integer',
            'kode_agen'             => 'nullable|string',
            'nominal_setoran'       => 'required|numeric|min:1',
            'metode_pembayaran'     => 'required|string|in:tunai,transfer,qris',
            'penagih_inkaso'        => 'nullable|string',
            'bank_tujuan'           => 'nullable|string',
            'no_referensi_transfer' => 'nullable|string',
            'no_blanko'             => 'nullable|string',
            'kode_perkiraan'        => 'nullable|string',
            'no_bapr'               => 'nullable|string',
            'retur_eks'             => 'nullable|integer|min:0',
            'nilai_retur'           => 'nullable|numeric|min:0',
            'potongan_pajak'        => 'nullable|numeric|min:0',
            'periode'               => 'nullable|string',
            'keterangan'            => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $shift = $this->getActiveShift();
            $nominalSetoran = (float) $request->input('nominal_setoran');
            $returEks = (int) ($request->input('retur_eks') ?? 0);
            // Logika Bisnis Nyata Kabar Priangan: Potongan Retur Koran = eksemplar retur x Rp 2.100
            $nilaiRetur = $returEks > 0 ? ($returEks * 2100) : (float) ($request->input('nilai_retur') ?? 0);
            $totalPengurang = $nominalSetoran + $nilaiRetur;
            $userId = Auth::id() ?? 1;

            // 1. Lock Agen Record & Kurangi Piutang
            $agen = null;
            if ($request->agen_id) {
                $agen = Agen::lockForUpdate()->find($request->agen_id);
            }
            if (!$agen && $request->kode_agen) {
                $agen = Agen::lockForUpdate()->where('kode_agen', $request->kode_agen)->first();
            }
            if (!$agen) {
                return redirect()->back()->with('error', 'Validasi Gagal: Data Agen tidak ditemukan. Harap pilih agen yang valid.');
            }

            $saldoAwalPiutang = (float) $agen->saldo_piutang;
            $saldoAkhirPiutang = max(0, $saldoAwalPiutang - $totalPengurang);

            $agen->saldo_piutang = $saldoAkhirPiutang;
            $agen->save();

            // 2. Terbitkan Nomor Kuitansi Kas Masuk Format Asli DOS KAS.EXE (KI-02-YYYYMMXXXX)
            $noKuitansi = KasMasuk::generateNoKuitansi('02');
            $kodePerkiraan = $request->kode_perkiraan ?? ($saldoAkhirPiutang <= 0 ? '1.00.04.01.01.00' : '2.00.01.01.01.00');
            $namaPerkiraan = $saldoAkhirPiutang <= 0 ? 'Piutang Sirkulasi Koran Agen' : 'Uang Muka Sirkulasi';

            $periodeInput = $request->input('periode', Carbon::now()->format('Y-m'));
            $tglTransaksi = ($periodeInput === Carbon::now()->format('Y-m'))
                ? Carbon::today()
                : Carbon::parse($periodeInput . '-01')->endOfMonth();

            // 3. Simpan ke kas_masuks
            $kasMasuk = KasMasuk::create([
                'no_kuitansi'           => $noKuitansi,
                'no_bkm'                => $noKuitansi,
                'shift_id'              => $shift->id,
                'tanggal_terima'        => Carbon::today(),
                'tgl_transaksi'         => $tglTransaksi,
                'no_blanko'             => $request->no_blanko,
                'kode_bagian'           => '02',
                'kode_perkiraan'        => $kodePerkiraan,
                'nama_perkiraan'        => $namaPerkiraan,
                'terima_dari'           => $agen->nama_agen,
                'nama_pembayar'         => $agen->nama_agen,
                'penagih_inkaso'        => $request->penagih_inkaso,
                'kode_agen_ref'         => $agen->kode_agen ?? $agen->id,
                'kode_agen_input'       => $agen->kode_agen,
                'no_bapr_ref'           => $request->no_bapr,
                'tipe_transaksi'        => 'sirkulasi',
                'ref_id'                => $agen->id,
                'ref_type'              => 'agen_sirkulasi',
                'metode_bayar'          => $request->metode_pembayaran,
                'bank_tujuan'           => $request->bank_tujuan,
                'no_referensi_transfer' => $request->no_referensi_transfer,
                'jumlah_tagihan'        => $saldoAwalPiutang,
                'total_tagihan'         => $saldoAwalPiutang,
                'potongan_pajak'        => (float) ($request->potongan_pajak ?? 0),
                'potongan_retur'        => $nilaiRetur,
                'no_bapr'               => $request->no_bapr,
                'jumlah_retur_eks'      => $returEks,
                'jumlah_diterima'       => $nominalSetoran,
                'jumlah_dibayar'        => $nominalSetoran,
                'sisa_piutang'          => $saldoAkhirPiutang,
                'status_bayar'          => $saldoAkhirPiutang <= 0 ? 'lunas' : 'uang_muka',
                'status_pembayaran'     => $saldoAkhirPiutang <= 0 ? 'lunas' : 'sebagian',
                'keterangan'            => $request->keterangan ?? "Setoran Koran Agen {$agen->nama_agen} [{$agen->kode_agen}]" . ($request->no_bapr ? " (Retur BAPR: {$request->no_bapr} - {$returEks} eks @ Rp2.100)" : ''),
                'kasir_id'              => $userId,
                'user_id'               => $userId,
                'is_closed'             => false,
                'is_pulled_accounting'  => false,
                'status_tarik_akunting' => false,
            ]);

            // 4. OTOMATIS: Terbitkan Kas Keluar (KO) Penyeimbang Potongan Pajak Sirkulasi jika ada
            $potonganPajak = (float) ($request->potongan_pajak ?? 0);
            $noBkkPajak = null;
            if ($potonganPajak > 0) {
                $noBkkPajak = KasKeluar::generateNoBkk();
                $kasKeluarPajak = KasKeluar::create([
                    'no_bkk'                => $noBkkPajak,
                    'shift_id'              => $shift->id,
                    'tanggal_keluar'        => Carbon::today(),
                    'tgl_transaksi'         => Carbon::today(),
                    'no_bp_ref'             => $agen->kode_agen,
                    'no_register_anggaran'  => 'TAX-' . Carbon::today()->format('Ymd') . '-' . str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'kode_bagian'           => '02', // Divisi Sirkulasi (Sesuai Sumber Setoran Sirkulasi)
                    'kode_perkiraan'        => '2.00.03.01.01',
                    'nama_perkiraan'        => 'Beban Potongan Pajak Sirkulasi Koran',
                    'diberikan_kepada'      => 'KAS NEGARA / PAJAK AGEN ' . strtoupper($agen->nama_agen),
                    'nama_penerima'         => 'KAS NEGARA / PAJAK AGEN ' . strtoupper($agen->nama_agen),
                    'kategori_beban'        => 'pajak_instansi',
                    'kategori'              => 'umum',
                    'jumlah_keluar'         => $potonganPajak,
                    'nominal_keluar'        => $potonganPajak,
                    'metode_bayar'          => 'kas_fisik',
                    'is_tax_offset'         => true,
                    'is_kasbon'             => false,
                    'keterangan'            => "Beban Potongan Pajak Setoran Sirkulasi Agen {$agen->nama_agen} ({$agen->kode_agen})",
                    'kasir_id'              => $userId,
                    'user_id'               => $userId,
                    'is_closed'             => false,
                    'is_pulled_accounting'  => false,
                    'status_tarik_akunting' => false,
                ]);

                // Update total KO di shift
                $shift->total_ko += $potonganPajak;
                $shift->total_keluar += $potonganPajak;

                // Catat mutasi keluar di KasTransaksi (Treasury)
                KasTransaksi::catatMutasi(
                    'keluar',
                    'Potongan Pajak Sirkulasi Agen',
                    $potonganPajak,
                    "KO {$noBkkPajak} - Potongan Pajak Agen {$agen->nama_agen} ({$agen->kode_agen})",
                    $kasKeluarPajak,
                    $userId,
                    Carbon::today()->toDateString()
                );
            }

            // 5. Update Shift Kasir (DOS Field Sync)
            $shift->total_ki += $nominalSetoran;
            $shift->total_masuk += $nominalSetoran;
            $shift->saldo_akhir_teoritis = $shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko;
            $shift->saldo_akhir_fisik = $shift->saldo_akhir_teoritis;
            $shift->save();

            // 6. Catat Mutasi Kas Masuk agar Kartu Piutang Agen & GL Akunting membaca mutasi ini
            KasTransaksi::catatMutasi(
                'masuk',
                'Setoran Sirkulasi Agen Koran',
                $nominalSetoran,
                "KI {$noKuitansi} - Setoran Agen {$agen->nama_agen} ({$agen->kode_agen})" . ($request->no_bapr ? " | Retur {$returEks} eks x Rp2.100 (Rp " . number_format($nilaiRetur, 0, ',', '.') . ")" : '') . ($potonganPajak > 0 ? " [Dipotong Pajak Rp " . number_format($potonganPajak, 0, ',', '.') . "]" : ''),
                $kasMasuk,
                $userId,
                Carbon::today()->toDateString()
            );

            // Data untuk cetak Tanda Terima Setoran Koran (Sesuai Arahan: Saldo Piutang Lama Tidak Ditampilkan)
            $cetakSirkulasi = [
                'no_kuitansi'    => $noKuitansi,
                'no_bkm'         => $noKuitansi,
                'kode_bagian'    => '02',
                'kode_perkiraan' => $kodePerkiraan,
                'kode_agen'      => $agen->kode_agen,
                'nama_agen'      => $agen->nama_agen,
                'wilayah'        => $agen->wilayah ?? 'Tasikmalaya',
                'nominal'        => $nominalSetoran,
                'terbilang'      => self::terbilang($nominalSetoran),
                'metode'         => strtoupper($request->metode_pembayaran),
                'penagih'        => $request->penagih_inkaso ?? 'Kasir Langsung',
                'no_bapr'        => $request->no_bapr ?? '-',
                'retur_eks'      => $returEks,
                'nilai_retur'    => $nilaiRetur,
                'tanggal'        => Carbon::now()->translatedFormat('d F Y, H:i'),
                'kasir'          => Auth::user()->name ?? 'Kasir Kabar Priangan',
            ];

            return redirect()->back()
                ->with('success', "Setoran Sirkulasi dari {$agen->nama_agen} ({$agen->kode_agen}) sebesar Rp " . number_format($nominalSetoran, 0, ',', '.') . " berhasil dicatat ke Kas Masuk ({$noKuitansi})!")
                ->with('cetak_sirkulasi', $cetakSirkulasi);
        });
    }

    /**
     * Penjualan Koran Sisa Cetak (Pendapatan Lain-lain / KI-14)
     * Pemisahan tegas: Bukan retur koran agen (BAPR Accounting),
     * melainkan kelebihan cetak oplah dari percetakan yang dijual tunai / kiloan ke pengepul.
     */
    public function penerimaanKoranSisa(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        $search = $request->input('search');

        $query = KasMasuk::where('kode_bagian', '14')
            ->where('kode_perkiraan', '7.00.02.01.02')
            ->latest('id');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_kuitansi', 'like', "%{$search}%")
                  ->orWhere('terima_dari', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $riwayatSisa = $query->paginate(15);

        $totalSisaBulanIni = (float) KasMasuk::where('kode_bagian', '14')
            ->where('kode_perkiraan', '7.00.02.01.02')
            ->whereMonth('tanggal_terima', $thisMonth)
            ->whereYear('tanggal_terima', $thisYear)
            ->sum('jumlah_dibayar');

        $totalSisaHariIni = (float) KasMasuk::where('kode_bagian', '14')
            ->where('kode_perkiraan', '7.00.02.01.02')
            ->whereDate('tanggal_terima', $today)
            ->sum('jumlah_dibayar');

        return view('pages.kasir.penerimaan.koran-sisa', compact(
            'riwayatSisa',
            'totalSisaBulanIni',
            'totalSisaHariIni',
            'search'
        ));
    }

    /**
     * Store Penjualan Koran Sisa Cetak (Format: KI-14-YYYYMMXXXX)
     */
    public function storePenerimaanKoranSisa(Request $request)
    {
        if ($unclosedMsg = $this->checkPreviousShiftClosed()) {
            return redirect()->back()->with('error', $unclosedMsg);
        }

        $request->validate([
            'pembeli'         => 'required|string',
            'jenis_satuan'    => 'required|in:kg,eksemplar',
            'jumlah_satuan'   => 'required|numeric|min:0.1',
            'harga_per_satuan'=> 'required|numeric|min:1',
            'metode_bayar'    => 'required|string|in:tunai,transfer',
            'bank_tujuan'     => 'nullable|string',
            'no_blanko'       => 'nullable|string',
            'keterangan'      => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $shift = $this->getActiveShift();
            $jumlahSatuan = (float) $request->input('jumlah_satuan');
            $hargaSatuan = (float) $request->input('harga_per_satuan');
            $nominalTotal = round($jumlahSatuan * $hargaSatuan, 2);
            $userId = Auth::id() ?? 1;

            $noKuitansi = KasMasuk::generateNoKuitansi('14');

            $satuanLabel = $request->jenis_satuan === 'kg' ? 'Kg' : 'Eks';
            $keteranganRinci = $request->keterangan 
                ?? "Penjualan sisa cetak koran lepas ({$jumlahSatuan} {$satuanLabel} @ Rp " . number_format($hargaSatuan, 0, ',', '.') . ") kepada {$request->pembeli}";

            $kasMasuk = KasMasuk::create([
                'no_kuitansi'           => $noKuitansi,
                'no_bkm'                => $noKuitansi,
                'shift_id'              => $shift->id,
                'tanggal_terima'        => Carbon::today(),
                'tgl_transaksi'         => Carbon::today(),
                'no_blanko'             => $request->no_blanko,
                'kode_bagian'           => '14',
                'kode_perkiraan'        => '7.00.02.01.02',
                'nama_perkiraan'        => 'Penjualan Koran Sisa Cetak',
                'terima_dari'           => $request->pembeli,
                'nama_pembayar'         => $request->pembeli,
                'penagih_inkaso'        => 'Loket Kasir Tunai',
                'tipe_transaksi'        => 'koran_sisa',
                'ref_id'                => null,
                'ref_type'              => 'sisa_cetak_pabrik',
                'metode_bayar'          => $request->metode_bayar,
                'bank_tujuan'           => $request->bank_tujuan,
                'jumlah_tagihan'        => $nominalTotal,
                'total_tagihan'         => $nominalTotal,
                'potongan_pajak'        => 0,
                'potongan_retur'        => 0,
                'jumlah_diterima'       => $nominalTotal,
                'jumlah_dibayar'        => $nominalTotal,
                'sisa_piutang'          => 0,
                'status_bayar'          => 'lunas',
                'status_pembayaran'     => 'lunas',
                'keterangan'            => $keteranganRinci,
                'kasir_id'              => $userId,
                'user_id'               => $userId,
                'is_closed'             => false,
                'is_pulled_accounting'  => false,
                'status_tarik_akunting' => false,
            ]);

            // Update Shift
            $shift->total_ki += $nominalTotal;
            $shift->total_masuk += $nominalTotal;
            $shift->saldo_akhir_teoritis = $shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko;
            $shift->saldo_akhir_fisik = $shift->saldo_akhir_teoritis;
            $shift->save();

            // Mutasi Treasury
            KasTransaksi::catatMutasi(
                'masuk',
                'Penjualan Koran Sisa Cetak (Pendapatan Lain-lain)',
                $nominalTotal,
                "KI {$noKuitansi} - Sisa Cetak {$jumlahSatuan} {$satuanLabel} ({$request->pembeli})",
                $kasMasuk,
                $userId,
                Carbon::today()->toDateString()
            );

            $cetakSisa = [
                'no_kuitansi'    => $noKuitansi,
                'no_bkm'         => $noKuitansi,
                'kode_bagian'    => '14',
                'kode_perkiraan' => '7.00.02.01.02',
                'pembeli'        => $request->pembeli,
                'rincian'        => "{$jumlahSatuan} {$satuanLabel} @ Rp " . number_format($hargaSatuan, 0, ',', '.'),
                'nominal'        => $nominalTotal,
                'terbilang'      => self::terbilang($nominalTotal),
                'metode'         => strtoupper($request->metode_bayar),
                'tanggal'        => Carbon::now()->translatedFormat('d F Y, H:i'),
                'kasir'          => Auth::user()->name ?? 'Kasir Kabar Priangan',
            ];

            return redirect()->back()
                ->with('success', "Bukti Kas Masuk ({$noKuitansi}) Penjualan Koran Sisa Cetak sebesar Rp " . number_format($nominalTotal, 0, ',', '.') . " berhasil dicatat!")
                ->with('cetak_sisa', $cetakSisa);
        });
    }

    /**
     * Penjualan Langsung / Tunai tanpa faktur tempo
     */
    public function penerimaanTunai(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $recentTunai = KasMasuk::with('kasir')
            ->where('tipe_transaksi', 'tunai')
            ->latest('id')
            ->take(10)
            ->get();

        return view('pages.kasir.penerimaan.tunai', compact('recentTunai'));
    }

    /**
     * Process Penjualan Langsung
     */
    public function storePenerimaanTunai(Request $request)
    {
        if ($unclosedMsg = $this->checkPreviousShiftClosed()) {
            return redirect()->back()->with('error', $unclosedMsg);
        }

        $request->validate([
            'pembeli'    => 'required|string',
            'kategori'   => 'required|string',
            'keterangan' => 'required|string',
            'jumlah'     => 'required|numeric|min:1',
            'metode'     => 'required|string',
        ]);

        return DB::transaction(function () use ($request) {
            $shift = $this->getActiveShift();
            $nominal = (float) $request->input('jumlah');
            $userId = Auth::id() ?? 1;

            // Terbitkan Nomor Kuitansi Kas Masuk Format Asli DOS KAS.EXE (KI-14-YYYYMMXXXX)
            $noKuitansi = KasMasuk::generateNoKuitansi('14');

            $metodeBayar = match (strtolower($request->metode)) {
                'transfer qris', 'qris' => 'qris',
                'transfer bank', 'transfer' => 'transfer',
                default => 'tunai',
            };

            $kasMasuk = KasMasuk::create([
                'no_kuitansi'           => $noKuitansi,
                'no_bkm'                => $noKuitansi,
                'shift_id'              => $shift->id,
                'tanggal_terima'        => Carbon::today(),
                'tgl_transaksi'         => Carbon::today(),
                'kode_bagian'           => '14',
                'kode_perkiraan'        => '7.00.01.01.02',
                'nama_perkiraan'        => 'Penjualan Koran Sisa & Eceran Lepas',
                'terima_dari'           => $request->pembeli,
                'nama_pembayar'         => $request->pembeli,
                'penagih_inkaso'        => 'Loket Kasir Tunai',
                'tipe_transaksi'        => 'tunai',
                'ref_id'                => null,
                'ref_type'              => 'penjualan_langsung',
                'metode_bayar'          => $metodeBayar,
                'bank_tujuan'           => null,
                'no_referensi_transfer' => null,
                'jumlah_tagihan'        => $nominal,
                'total_tagihan'         => $nominal,
                'potongan_pajak'        => 0,
                'potongan_retur'        => 0,
                'jumlah_diterima'       => $nominal,
                'jumlah_dibayar'        => $nominal,
                'sisa_piutang'          => 0,
                'status_bayar'          => 'lunas',
                'status_pembayaran'     => 'lunas',
                'keterangan'            => "[{$request->kategori}] {$request->keterangan}",
                'kasir_id'              => $userId,
                'user_id'               => $userId,
                'is_closed'             => false,
                'is_pulled_accounting'  => false,
                'status_tarik_akunting' => false,
            ]);

            // Update Shift
            $shift->total_ki += $nominal;
            $shift->total_masuk += $nominal;
            $shift->saldo_akhir_teoritis = $shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko;
            $shift->saldo_akhir_fisik = $shift->saldo_akhir_teoritis;
            $shift->save();

            // Mutasi Treasury
            KasTransaksi::catatMutasi(
                'masuk',
                'Penjualan Langsung / Tunai Kasir',
                $nominal,
                "KI {$noKuitansi} - {$request->kategori} ({$request->pembeli}): {$request->keterangan}",
                $kasMasuk,
                $userId,
                Carbon::today()->toDateString()
            );

            $cetakTunai = [
                'no_kuitansi'=> $noKuitansi,
                'no_bkm'     => $noKuitansi,
                'kode_bagian'=> '14',
                'pembeli'    => $request->pembeli,
                'kategori'   => $request->kategori,
                'rincian'    => $request->keterangan,
                'nominal'    => $nominal,
                'terbilang'  => number_format($nominal, 0, ',', '.'),
                'metode'     => strtoupper($metodeBayar),
                'tanggal'    => Carbon::now()->translatedFormat('d F Y, H:i'),
                'kasir'      => Auth::user()->name ?? 'Kasir Kabar Priangan',
            ];

            return redirect()->back()
                ->with('success', "Kwitansi Kas Masuk ({$noKuitansi}) sebesar Rp " . number_format($nominal, 0, ',', '.') . " berhasil dicatat ke Kas Masuk!")
                ->with('cetak_tunai', $cetakTunai);
        });
    }

    /**
     * Realisasi Bukti Pengeluaran (BP) - BKK (Bukti Kas Keluar)
     */
    public function realisasiBp(Request $request)
    {
        $search = $request->input('search');

        $bpQuery = BuktiPengeluaran::with(['approver', 'disburser', 'kasKeluar']);
        if ($search) {
            $bpQuery->where(function($q) use ($search) {
                $q->where('no_bp', 'like', "%{$search}%")
                  ->orWhere('no_register_anggaran', 'like', "%{$search}%")
                  ->orWhere('pemohon', 'like', "%{$search}%")
                  ->orWhere('keperluan', 'like', "%{$search}%");
            });
        }

        $bpList = $bpQuery->orderByRaw("
            CASE status 
                WHEN 'approved' THEN 1 
                WHEN 'pending' THEN 2 
                WHEN 'dicairkan_kasir' THEN 3 
                WHEN 'rejected' THEN 4 
                ELSE 5 
            END ASC
        ")
            ->orderBy('id', 'desc')
            ->get();

        // Ambil riwayat Kas Keluar Potongan Pajak & PPN (KO Penyeimbang)
        $pajakKoQuery = KasKeluar::with(['kasir', 'shift'])
            ->where(function($q) {
                $q->where('is_tax_offset', true)
                  ->orWhere('kategori_beban', 'pajak_instansi')
                  ->orWhere('kode_perkiraan', '2.00.03.01.01');
            });

        if ($search) {
            $pajakKoQuery->where(function($q) use ($search) {
                $q->where('no_bkk', 'like', "%{$search}%")
                  ->orWhere('no_bp_ref', 'like', "%{$search}%")
                  ->orWhere('diberikan_kepada', 'like', "%{$search}%")
                  ->orWhere('nama_penerima', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $pajakKoList = $pajakKoQuery->orderBy('id', 'desc')->get();

        return view('pages.kasir.pengeluaran.realisasi-bp', compact('bpList', 'pajakKoList', 'search'));
    }

    /**
     * Process Realisasi BP (Release Cash / BKK)
     * Validasi: Hanya nomor BP berstatus 'approved' dan memiliki Nomor Register Anggaran (PR-...) yang dapat dicairkan!
     * Update BP terkait menjadi 'dicairkan_kasir', timestamp & nama kasir.
     */
    public function storeRealisasiBp(Request $request)
    {
        if ($unclosedMsg = $this->checkPreviousShiftClosed()) {
            return redirect()->back()->with('error', $unclosedMsg);
        }

        $request->validate([
            'bp_id'         => 'required|integer',
            'nominal_cair'  => 'required|numeric|min:1',
            'penerima'      => 'required|string',
            'metode_bayar'  => 'required|string|in:kas_fisik,transfer',
            'kode_bagian'   => 'nullable|string',
            'kode_perkiraan'=> 'nullable|string',
            'kategori'      => 'nullable|string',
            'catatan'       => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $shift = $this->getActiveShift();
            $nominalCair = (float) $request->input('nominal_cair');
            $userId = Auth::id() ?? 1;

            // 1. Lock BP Record & VALIDASI Status
            $bp = BuktiPengeluaran::lockForUpdate()->findOrFail($request->bp_id);

            if ($bp->status !== 'approved') {
                return redirect()->back()->with('error', "Validasi Ditolak: Hanya Bukti Pengeluaran (BP) yang berstatus 'Approved' dari Divisi Anggaran/Pimpinan yang dapat dicairkan! Status BP saat ini: '{$bp->status}'.");
            }

            // Pastikan Nomor Register Anggaran (PR-YYYYMMDD-XXXX) telah diterbitkan
            if (empty($bp->no_register_anggaran)) {
                $bp->no_register_anggaran = BuktiPengeluaran::generateNoRegister();
                $bp->save();
            }

            // 2. Terbitkan Nomor Bukti Kas Keluar DOS KAS.EXE (KO-YYYYMMXXXX)
            $noBkk = KasKeluar::generateNoBkk();

            // 3. Tentukan Kode Bagian & Bagan Akun Perkiraan (COA) Real Kabar Priangan
            $divisiLower = strtolower($bp->divisi ?? 'umum');
            $kodeBagian = $request->kode_bagian ?? match ($divisiLower) {
                'iklan'     => '01',
                'sirkulasi' => '02',
                'umum', 'sdm' => '03',
                'akuntansi' => '04',
                'keuangan'  => '05',
                'redaksi'   => '06',
                'pimpinan'  => '07',
                default     => '03',
            };

            $kodePerkiraan = $request->kode_perkiraan ?? match ($kodeBagian) {
                '01' => '6.00.01.01.01.02',
                '02' => '6.00.01.00.01.04',
                '03' => '6.00.01.00.01.04', // Beban Umum & SDM / Perlengkapan RT / Listrik / BBM
                '04' => '6.00.01.01.01.03',
                '05' => '6.00.01.01.01.02',
                '06' => '5.00.01.01.02',    // Beban Liputan Redaksi
                '07' => '6.00.01.01.00.00',
                default => '6.00.01.00.01.04',
            };

            $namaPerkiraan = match ($kodeBagian) {
                '01' => 'Beban Operasional Iklan',
                '02' => 'Beban Sirkulasi & Ekspedisi',
                '03' => 'Beban Umum & SDM / BBM / Perlengkapan RT',
                '04' => 'Beban Administrasi Akuntansi',
                '05' => 'Beban Keuangan & Pajak',
                '06' => 'Beban Redaksi & Honor Wartawan',
                '07' => 'Beban Operasional Pimpinan',
                default => 'Beban Operasional ' . ucfirst($bp->divisi ?? 'Umum'),
            };

            $kategoriBeban = $request->kategori ?? match ($kodeBagian) {
                '01'    => 'komisi_kh',
                '02'    => 'bbm_armada',
                '03'    => 'perlengkapan_rt',
                '06'    => 'redaksi',
                default => 'umum',
            };

            $kategoriStandar = match ($kategoriBeban) {
                'komisi_kh', 'insentif_ae', 'komisi_kh_insentif' => 'komisi_kh_insentif',
                'bbm_armada', 'operasional_bbm' => 'operasional_bbm',
                'redaksi' => 'redaksi',
                default => 'umum',
            };

            // 4. Simpan ke kas_keluars
            $kasKeluar = KasKeluar::create([
                'no_bkk'                => $noBkk,
                'shift_id'              => $shift->id,
                'tanggal_keluar'        => Carbon::today(),
                'tgl_transaksi'         => Carbon::today(),
                'no_bp_ref'             => $bp->no_bp,
                'no_register_anggaran'  => $bp->no_register_anggaran,
                'kode_bagian'           => $kodeBagian,
                'kode_perkiraan'        => $kodePerkiraan,
                'nama_perkiraan'        => $namaPerkiraan,
                'diberikan_kepada'      => $request->penerima,
                'nama_penerima'         => $request->penerima,
                'kategori_beban'        => $kategoriBeban,
                'kategori'              => $kategoriStandar,
                'jumlah_keluar'         => $nominalCair,
                'nominal_keluar'        => $nominalCair,
                'metode_bayar'          => $request->metode_bayar,
                'bp_id'                 => $bp->id,
                'keterangan'            => $request->catatan ?? "Pencairan BP {$bp->no_bp} [Reg: {$bp->no_register_anggaran}] ({$bp->pemohon}): {$bp->keperluan}",
                'kasir_id'              => $userId,
                'user_id'               => $userId,
                'is_closed'             => false,
                'is_pulled_accounting'  => false,
                'status_tarik_akunting' => false,
            ]);

            // 5. Update Status BP terkait menjadi 'dicairkan_kasir' beserta timestamp dan nama kasir
            $bp->update([
                'status'         => 'dicairkan_kasir',
                'dicairkan_oleh' => $userId,
                'dicairkan_at'   => Carbon::now(),
            ]);

            // Sinkronkan ke modul pengajuan_anggaran di Divisi Keuangan jika terhubung
            if ($bp->pengajuan_anggaran_id) {
                $pengajuan = PengajuanAnggaran::find($bp->pengajuan_anggaran_id);
                if ($pengajuan) {
                    $pengajuan->update([
                        'status'       => 'cair',
                        'cair_by'      => $userId,
                        'tanggal_cair' => Carbon::today()->toDateString(),
                    ]);
                }
            }

            // 6. Update Shift Kasir (DOS Field Sync)
            $shift->total_ko += $nominalCair;
            $shift->total_keluar += $nominalCair;
            $shift->saldo_akhir_teoritis = $shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko;
            $shift->saldo_akhir_fisik = $shift->saldo_akhir_teoritis;
            $shift->save();

            // 7. Catat Mutasi Kas Keluar Treasury
            KasTransaksi::catatMutasi(
                'keluar',
                'Realisasi BP Kasir (' . strtoupper($kategoriBeban) . ')',
                $nominalCair,
                "KO {$noBkk} - Pencairan BP {$bp->no_bp} [Reg: {$bp->no_register_anggaran}] kepada {$request->penerima} ({$bp->keperluan})",
                $kasKeluar,
                $userId,
                Carbon::today()->toDateString()
            );

            // Payload untuk Cetak Bukti Kas Keluar (BKK / KO)
            $cetakBkk = [
                'no_bkk'        => $noBkk,
                'no_bp'         => $bp->no_bp,
                'no_register'   => $bp->no_register_anggaran,
                'kode_bagian'   => $kodeBagian,
                'kode_perkiraan'=> $kodePerkiraan,
                'nama_perkiraan'=> $namaPerkiraan,
                'divisi'        => strtoupper($bp->divisi),
                'penerima'      => $request->penerima,
                'keperluan'     => $bp->keperluan,
                'nominal'       => $nominalCair,
                'terbilang'     => self::terbilang($nominalCair),
                'metode'        => strtoupper($request->metode_bayar === 'kas_fisik' ? 'Kas Tunai / Fisik' : 'Transfer Bank'),
                'approver'      => $bp->approver->name ?? 'Pimpinan / Divisi Anggaran',
                'tanggal'       => Carbon::now()->translatedFormat('d F Y, H:i'),
                'kasir'         => Auth::user()->name ?? 'Kasir Kabar Priangan',
            ];

            return redirect()->back()
                ->with('success', "Bukti Kas Keluar ({$noBkk}) [Register: {$bp->no_register_anggaran}] untuk {$bp->no_bp} sebesar Rp " . number_format($nominalCair, 0, ',', '.') . " berhasil dicairkan!")
                ->with('cetak_bkk', $cetakBkk);
        });
    }

    /**
     * Buku Kas & Tutup Shift Kasir
     */
    public function bukuKas(Request $request)
    {
        $today = Carbon::now()->translatedFormat('d F Y');
        $todayDate = Carbon::today()->toDateString();

        $activeShift = $this->getActiveShift();

        // Totals calculation hari ini dari database
        $totalPenerimaanIklan = (int) KasMasuk::whereDate('tgl_transaksi', $todayDate)
            ->where('tipe_transaksi', 'iklan')
            ->sum('jumlah_dibayar');

        $totalSetoranSirkulasi = (int) KasMasuk::whereDate('tgl_transaksi', $todayDate)
            ->where('tipe_transaksi', 'sirkulasi')
            ->sum('jumlah_dibayar');

        $totalPenjualanTunai = (int) KasMasuk::whereDate('tgl_transaksi', $todayDate)
            ->where('tipe_transaksi', 'tunai')
            ->sum('jumlah_dibayar');

        $totalKasMasuk = (int) KasMasuk::whereDate('tgl_transaksi', $todayDate)
            ->sum('jumlah_dibayar');

        $totalPengeluaranBp = (int) KasKeluar::whereDate('tgl_transaksi', $todayDate)
            ->sum('nominal_keluar');

        $saldoAwalShift = (int) $activeShift->saldo_awal_fisik;
        $saldoAkhirShift = $saldoAwalShift + $totalKasMasuk - $totalPengeluaranBp;

        // Faktor pengurang kas fisik harian: Kasbon / Uang Muka Operasional Gantung
        $totalKasbonGantung = (float) Kasbon::where('status', 'pending')->sum('nominal');
        $saldoFisikLaci = max(0, $saldoAkhirShift - $totalKasbonGantung);

        // Mutasi Kas Shift Hari ini (Gabungan KasMasuk, KasKeluar, dan Kasbon Gantung)
        $mutasiShift = new Collection();

        $masuks = KasMasuk::whereDate('tgl_transaksi', $todayDate)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($masuks as $m) {
            $mutasiShift->push((object)[
                'waktu'    => $m->created_at ? $m->created_at->format('H:i') : '08:00',
                'jenis'    => 'Kas Masuk',
                'ref'      => $m->no_bkm,
                'kategori' => 'Setoran ' . ucfirst($m->tipe_transaksi),
                'uraian'   => $m->keterangan ?? "Penerimaan {$m->tipe_transaksi} dari {$m->nama_pembayar}",
                'masuk'    => (int) $m->jumlah_dibayar,
                'keluar'   => 0,
                'petugas'  => $m->penagih_inkaso ?? '-',
            ]);
        }

        $keluars = KasKeluar::whereDate('tgl_transaksi', $todayDate)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($keluars as $k) {
            $mutasiShift->push((object)[
                'waktu'    => $k->created_at ? $k->created_at->format('H:i') : '10:00',
                'jenis'    => 'Kas Keluar',
                'ref'      => $k->no_bkk,
                'kategori' => 'Realisasi BP (' . str_replace('_', ' ', $k->kategori) . ')',
                'uraian'   => $k->keterangan ?? "Pencairan dana kepada {$k->nama_penerima}",
                'masuk'    => 0,
                'keluar'   => (int) $k->nominal_keluar,
                'petugas'  => $k->nama_penerima,
            ]);
        }

        // Tampilkan Kasbon / Uang Muka Meja Kasir Hari Ini
        $kasbonsHariIni = Kasbon::whereDate('tanggal', $todayDate)->orderBy('id', 'desc')->get();
        foreach ($kasbonsHariIni as $kb) {
            $mutasiShift->push((object)[
                'waktu'    => $kb->created_at ? $kb->created_at->format('H:i') : '09:00',
                'jenis'    => 'Kasbon (Uang Muka)',
                'ref'      => $kb->no_kasbon,
                'kategori' => 'Uang Muka Operasional (Gantung)',
                'uraian'   => "Kasbon operasional: {$kb->keperluan} (Penerima: {$kb->nama_penerima})",
                'masuk'    => 0,
                'keluar'   => (int) $kb->nominal,
                'petugas'  => $kb->nama_penerima . ' (ACC: ' . ($kb->approved_by ?? 'Menunggu') . ')',
            ]);
        }

        return view('pages.kasir.buku-kas.index', compact(
            'today',
            'activeShift',
            'saldoAwalShift',
            'totalKasMasuk',
            'totalPengeluaranBp',
            'saldoAkhirShift',
            'totalKasbonGantung',
            'saldoFisikLaci',
            'totalPenerimaanIklan',
            'totalSetoranSirkulasi',
            'totalPenjualanTunai',
            'mutasiShift'
        ));
    }

    /**
     * Process Closing Shift Kasir (End Shift)
     * - Hitung saldo sistem vs saldo fisik, kunci shift menjadi 'ditutup'
     * - Kasbon gantung otomatis menjadi faktor penyeimbang kas fisik (Fisik Laci + Kasbon Gantung = Saldo Sistem)
     * - Tandai seluruh kas_masuk & kas_keluar pada shift tersebut menjadi siap_tarik_akunting / status_tarik_akunting = true
     */
    public function tutupKasir(Request $request)
    {
        $request->validate([
            'fisik_kas'            => 'required|numeric|min:0',
            'catatan_serah_terima' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $shift = $this->getActiveShift();

            $totalMasuk = (float) KasMasuk::where('shift_id', $shift->id)->sum('jumlah_dibayar');
            $totalKeluar = (float) KasKeluar::where('shift_id', $shift->id)->sum('nominal_keluar');
            $saldoSistem = (float) ($shift->saldo_awal_fisik + $totalMasuk - $totalKeluar);
            $fisikKas = (float) $request->input('fisik_kas');

            // Kasbon Gantung Aktif sebagai komponen penyeimbang fisik kasir (Pak Eki)
            $totalKasbonGantung = (float) Kasbon::where('status', 'pending')->sum('nominal');
            $totalFisikPlusKasbon = $fisikKas + $totalKasbonGantung;
            $selisih = $totalFisikPlusKasbon - $saldoSistem;

            // 1. Kunci Shift Kasir
            $shift->update([
                'total_masuk'          => $totalMasuk,
                'total_keluar'         => $totalKeluar,
                'saldo_akhir_teoritis' => $saldoSistem,
                'saldo_akhir_fisik'    => $fisikKas,
                'total_kasbon_gantung' => $totalKasbonGantung,
                'selisih'              => $selisih,
                'selisih_kas'          => $selisih,
                'status'               => 'ditutup',
                'closed_at'            => Carbon::now(),
                'catatan_serah_terima' => $request->input('catatan_serah_terima'),
            ]);

            // 2. Tandai seluruh kas_masuk & kas_keluar pada shift ini: status_tarik_akunting = true
            KasMasuk::where('shift_id', $shift->id)->update(['status_tarik_akunting' => true]);
            KasKeluar::where('shift_id', $shift->id)->update(['status_tarik_akunting' => true]);

            $selisihFormat = number_format(abs($selisih), 0, ',', '.');
            $selisihLabel = $selisih == 0 ? 'Seimbang (Zero Difference / Balance)' : ($selisih > 0 ? "Surplus Kas (+Rp {$selisihFormat})" : "Defisit Kas (-Rp {$selisihFormat})");

            return redirect()->back()->with('success', "Shift Kasir berhasil ditutup! Saldo Sistem: Rp " . number_format($saldoSistem, 0, ',', '.') . " | Fisik Laci: Rp " . number_format($fisikKas, 0, ',', '.') . " + Kasbon Gantung: Rp " . number_format($totalKasbonGantung, 0, ',', '.') . " ({$selisihLabel}). Seluruh transaksi telah ditandai Siap Tarik Akunting untuk posting Jurnal & Buku Besar.");
        });
    }

    /**
     * Koran Langganan Instansi / Dinas Pemerintah
     */
    public function penerimaanLangganan(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status', 'all');

        $langgananList = collect([
            (object)[
                'id' => 1,
                'no_pelanggan' => 'LGN-TSM-001',
                'nama_instansi' => 'Sekretariat Daerah (Setda) Kota Tasikmalaya',
                'alamat' => 'Jl. Letnan Harun No. 1, Kota Tasikmalaya',
                'kontak' => 'Bagian Umum (Bpk. Yudi / 08122345xxxx)',
                'jumlah_eks' => 15,
                'harga_per_eks' => 110000,
                'periode' => 'Oktober 2026',
                'total_tagihan' => 1650000,
                'status_pembayaran' => 'Belum Lunas',
                'jatuh_tempo' => '2026-10-20',
            ],
            (object)[
                'id' => 2,
                'no_pelanggan' => 'LGN-TSM-002',
                'nama_instansi' => 'Bapenda Kabupaten Tasikmalaya',
                'alamat' => 'Komplek Perkantoran Pemkab, Singaparna',
                'kontak' => 'Subbag Keuangan (Ibu Nenden)',
                'jumlah_eks' => 10,
                'harga_per_eks' => 110000,
                'periode' => 'Oktober 2026',
                'total_tagihan' => 1100000,
                'status_pembayaran' => 'Lunas',
                'jatuh_tempo' => '2026-10-15',
            ],
            (object)[
                'id' => 3,
                'no_pelanggan' => 'LGN-CMS-003',
                'nama_instansi' => 'DPRD Kabupaten Ciamis',
                'alamat' => 'Jl. Ir. H. Juanda No. 164, Ciamis',
                'kontak' => 'Humas Protokol Setwan (Bpk. Asep)',
                'jumlah_eks' => 20,
                'harga_per_eks' => 110000,
                'periode' => 'Oktober 2026',
                'total_tagihan' => 2200000,
                'status_pembayaran' => 'Belum Lunas',
                'jatuh_tempo' => '2026-10-25',
            ],
            (object)[
                'id' => 4,
                'no_pelanggan' => 'LGN-BJR-004',
                'nama_instansi' => 'Polres Banjar',
                'alamat' => 'Jl. Siliwangi No. 145, Kota Banjar',
                'kontak' => 'Sie Humas Polres',
                'jumlah_eks' => 8,
                'harga_per_eks' => 110000,
                'periode' => 'Oktober 2026',
                'total_tagihan' => 880000,
                'status_pembayaran' => 'Belum Lunas',
                'jatuh_tempo' => '2026-10-18',
            ],
            (object)[
                'id' => 5,
                'no_pelanggan' => 'LGN-TSM-005',
                'nama_instansi' => 'Dinas Pendidikan Kota Tasikmalaya',
                'alamat' => 'Jl. Ir. H. Juanda No. 95, Tasikmalaya',
                'kontak' => 'Pengadaan Barang & Jasa (Bpk. Dani)',
                'jumlah_eks' => 12,
                'harga_per_eks' => 110000,
                'periode' => 'Oktober 2026',
                'total_tagihan' => 1320000,
                'status_pembayaran' => 'Lunas',
                'jatuh_tempo' => '2026-10-10',
            ],
        ]);

        if ($search) {
            $langgananList = $langgananList->filter(function($item) use ($search) {
                return str_contains(strtolower($item->nama_instansi), strtolower($search)) ||
                       str_contains(strtolower($item->no_pelanggan), strtolower($search));
            });
        }

        if ($statusFilter !== 'all') {
            $langgananList = $langgananList->filter(function($item) use ($statusFilter) {
                return strtolower($item->status_pembayaran) === strtolower($statusFilter);
            });
        }

        return view('pages.kasir.penerimaan.langganan', compact('langgananList', 'search', 'statusFilter'));
    }

    /**
     * Store Setoran Langganan Koran Instansi
     */
    public function storePenerimaanLangganan(Request $request)
    {
        $request->validate([
            'no_pelanggan'      => 'required|string',
            'nama_instansi'     => 'required|string',
            'nominal_bayar'     => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|string',
            'no_spk_po'         => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $shift = $this->getActiveShift();
            $nominal = (float) $request->input('nominal_bayar');
            $noBkm = KasMasuk::generateNoBkm();

            $kasMasuk = KasMasuk::create([
                'no_bkm'                => $noBkm,
                'shift_id'              => $shift->id,
                'tgl_transaksi'         => Carbon::today(),
                'tipe_transaksi'        => 'sirkulasi',
                'ref_id'                => null,
                'ref_type'              => 'langganan_instansi',
                'nama_pembayar'         => $request->nama_instansi,
                'penagih_inkaso'        => 'Kolektor Langganan',
                'metode_bayar'          => $request->metode_pembayaran === 'Transfer BJB' ? 'transfer' : 'tunai',
                'total_tagihan'         => $nominal,
                'jumlah_dibayar'        => $nominal,
                'sisa_piutang'          => 0,
                'status_pembayaran'     => 'lunas',
                'keterangan'            => "Pelunasan Koran Langganan {$request->nama_instansi} ({$request->no_pelanggan})" . ($request->no_spk_po ? " SPK: {$request->no_spk_po}" : ''),
                'user_id'               => Auth::id() ?? 1,
                'status_tarik_akunting' => false,
            ]);

            $shift->total_masuk += $nominal;
            $shift->saldo_akhir_fisik = $shift->saldo_awal_fisik + $shift->total_masuk - $shift->total_keluar;
            $shift->save();

            return redirect()->back()->with('success', "Kwitansi Langganan Koran ({$noBkm}) untuk {$request->nama_instansi} sebesar Rp " . number_format($nominal, 0, ',', '.') . " berhasil diterbitkan!");
        });
    }

    /**
     * Setoran Kas Fisik ke Rekening Bank Perusahaan (Banking In)
     */
    public function setoranBank(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $activeShift = $this->getActiveShift();

        $totalKasMasuk = (int) KasMasuk::whereDate('tgl_transaksi', $today)->sum('jumlah_dibayar');
        $totalPengeluaranBp = (int) KasKeluar::whereDate('tgl_transaksi', $today)->sum('nominal_keluar');
        $saldoAwal = (int) $activeShift->saldo_awal_fisik;

        $totalSudahSetorBank = 0;
        $kasFisikTersedia = max(0, ($saldoAwal + $totalKasMasuk - $totalPengeluaranBp) - $totalSudahSetorBank);

        $riwayatSetorBank = collect([
            (object)[
                'id'           => 1,
                'no_referensi' => 'STB-20261012-01',
                'tanggal'      => Carbon::now()->toDateString(),
                'waktu'        => '11:45',
                'bank_tujuan'  => 'Bank BJB Cabang Tasikmalaya (Giro: 0012-0034-5678)',
                'penyetor'     => Auth::user()->name ?? 'Kasir Kabar Priangan',
                'nominal'      => 5000000,
                'no_slip'      => 'SLIP-BJB-998124',
                'keterangan'   => 'Setor tunai hasil penerimaan iklan & koran pagi',
                'status'       => 'Tervalidasi Teller',
            ],
        ]);

        return view('pages.kasir.pengeluaran.setoran-bank', compact('kasFisikTersedia', 'totalSudahSetorBank', 'riwayatSetorBank'));
    }

    /**
     * Store Setoran Kas ke Bank
     */
    public function storeSetoranBank(Request $request)
    {
        $request->validate([
            'bank_tujuan'   => 'required|string',
            'nominal_setor' => 'required|numeric|min:1',
            'no_slip'       => 'required|string',
            'keterangan'    => 'nullable|string',
        ]);

        $noRef = 'STB-' . date('Ymd') . rand(10, 99);
        $nominal = (int) $request->input('nominal_setor');

        return redirect()->back()->with('success', "Setoran Kas ke Bank ({$noRef}) sebesar Rp " . number_format($nominal, 0, ',', '.') . " berhasil dicatat!");
    }

    /**
     * Riwayat & Pratinjau Cetak Seluruh Dokumen Kasir (BKM, BKK, Kwitansi)
     */
    public function riwayatCetak(Request $request)
    {
        $search = $request->input('search');
        $jenisFilter = $request->input('jenis', 'all');

        $dokumenList = new Collection();

        // 1. Kas Masuk (BKM)
        $masuks = KasMasuk::with('kasir')->latest('id')->take(20)->get();
        foreach ($masuks as $m) {
            $dokumenList->push((object)[
                'no_dokumen'        => $m->no_bkm,
                'jenis'             => 'BKM ' . ucfirst($m->tipe_transaksi),
                'tanggal'           => $m->tgl_transaksi->toDateString(),
                'penerima_pembayar' => $m->nama_pembayar,
                'uraian'            => $m->keterangan ?? "Penerimaan {$m->tipe_transaksi}",
                'nominal'           => (int) $m->jumlah_dibayar,
                'metode'            => strtoupper($m->metode_bayar) . ($m->bank_tujuan ? " ({$m->bank_tujuan})" : ''),
                'kasir'             => $m->kasir->name ?? 'Kasir Kabar Priangan',
            ]);
        }

        // 2. Kas Keluar (BKK)
        $keluars = KasKeluar::with(['kasir', 'buktiPengeluaran'])->latest('id')->take(20)->get();
        foreach ($keluars as $k) {
            $dokumenList->push((object)[
                'no_dokumen'        => $k->no_bkk,
                'jenis'             => 'BKK Pengeluaran',
                'tanggal'           => $k->tgl_transaksi->toDateString(),
                'penerima_pembayar' => $k->nama_penerima,
                'uraian'            => $k->keterangan ?? "Pencairan " . str_replace('_', ' ', $k->kategori),
                'nominal'           => (int) $k->nominal_keluar,
                'metode'            => strtoupper($k->metode_bayar === 'kas_fisik' ? 'Kas Tunai' : 'Transfer'),
                'kasir'             => $k->kasir->name ?? 'Kasir Kabar Priangan',
            ]);
        }

        if ($search) {
            $dokumenList = $dokumenList->filter(function($doc) use ($search) {
                return str_contains(strtolower($doc->no_dokumen), strtolower($search)) ||
                       str_contains(strtolower($doc->penerima_pembayar), strtolower($search)) ||
                       str_contains(strtolower($doc->uraian), strtolower($search));
            });
        }

        if ($jenisFilter !== 'all') {
            $dokumenList = $dokumenList->filter(function($doc) use ($jenisFilter) {
                return str_contains(strtolower($doc->jenis), strtolower($jenisFilter));
            });
        }

        return view('pages.kasir.riwayat.index', compact('dokumenList', 'search', 'jenisFilter'));
    }

    /**
     * Laporan Harian Kasir (LHK)
     */
    public function laporanHarian(Request $request)
    {
        $tanggalPilihan = $request->input('tanggal', Carbon::today()->toDateString());
        $formatTanggal = Carbon::parse($tanggalPilihan)->translatedFormat('l, d F Y');

        $activeShift = $this->getActiveShift();
        $saldoAwal = (int) $activeShift->saldo_awal_fisik;

        $totalPenerimaanIklan = (int) KasMasuk::whereDate('tgl_transaksi', $tanggalPilihan)
            ->where('tipe_transaksi', 'iklan')
            ->sum('jumlah_dibayar');

        $totalSirkulasi = (int) KasMasuk::whereDate('tgl_transaksi', $tanggalPilihan)
            ->where('tipe_transaksi', 'sirkulasi')
            ->sum('jumlah_dibayar');

        $totalTunaiLangsung = (int) KasMasuk::whereDate('tgl_transaksi', $tanggalPilihan)
            ->where('tipe_transaksi', 'tunai')
            ->sum('jumlah_dibayar');

        $totalLangganan = 0;
        $totalKasMasuk = $totalPenerimaanIklan + $totalSirkulasi + $totalTunaiLangsung;

        $pengeluaranBpBkk = (int) KasKeluar::whereDate('tgl_transaksi', $tanggalPilihan)
            ->sum('nominal_keluar');
        $setorBank = 0;
        $totalPengeluaran = $pengeluaranBpBkk + $setorBank;

        $saldoAkhir = ($saldoAwal + $totalKasMasuk) - $totalPengeluaran;

        // Faktor pengurang kas fisik harian: Kasbon / Uang Muka Operasional Gantung
        $totalKasbonGantung = (int) Kasbon::where('status', 'pending')->whereDate('tanggal', '<=', $tanggalPilihan)->sum('nominal');
        $saldoFisikLaci = max(0, $saldoAkhir - $totalKasbonGantung);

        return view('pages.kasir.laporan.harian', compact(
            'tanggalPilihan',
            'formatTanggal',
            'saldoAwal',
            'totalPenerimaanIklan',
            'totalSirkulasi',
            'totalLangganan',
            'totalTunaiLangsung',
            'totalKasMasuk',
            'pengeluaranBpBkk',
            'setorBank',
            'totalPengeluaran',
            'saldoAkhir',
            'totalKasbonGantung',
            'saldoFisikLaci'
        ));
    }

    /**
     * Mutasi Kas Bank Masuk (Jasa Giro / Transfer BJB - Bagian 14)
     */
    /**
     * Penarikan Kas Tunai Bank (Cek/Giro BJB & BCA - Bagian 09 / KI-09)
     * Kasir mencairkan uang tunai fisik dari giro bank perusahaan (BJB / BCA)
     * untuk mengisi brankas kas fisik kasir.
     */
    public function penerimaanBank(Request $request)
    {
        $search = $request->input('search');
        $bankFilter = $request->input('bank', 'all');

        $bankQuery = KasMasuk::where('kode_bagian', '09');

        if ($search) {
            $bankQuery->where(function($q) use ($search) {
                $q->where('no_kuitansi', 'like', "%{$search}%")
                  ->orWhere('terima_dari', 'like', "%{$search}%")
                  ->orWhere('no_referensi_transfer', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($bankFilter !== 'all') {
            $bankQuery->where('bank_tujuan', 'like', "%{$bankFilter}%");
        }

        $bankList = $bankQuery->latest('id')->paginate(15);

        $totalTarikBjbBulanIni = (float) KasMasuk::where('kode_bagian', '09')
            ->where('bank_tujuan', 'like', '%BJB%')
            ->whereMonth('tanggal_terima', Carbon::now()->month)
            ->whereYear('tanggal_terima', Carbon::now()->year)
            ->sum('jumlah_diterima');

        $totalTarikBcaBulanIni = (float) KasMasuk::where('kode_bagian', '09')
            ->where('bank_tujuan', 'like', '%BCA%')
            ->whereMonth('tanggal_terima', Carbon::now()->month)
            ->whereYear('tanggal_terima', Carbon::now()->year)
            ->sum('jumlah_diterima');

        $totalBankBulanIni = $totalTarikBjbBulanIni + $totalTarikBcaBulanIni;

        return view('pages.kasir.penerimaan.bank', compact(
            'bankList',
            'totalBankBulanIni',
            'totalTarikBjbBulanIni',
            'totalTarikBcaBulanIni',
            'search',
            'bankFilter'
        ));
    }

    /**
     * Store Penarikan Tunai Kas Bank (Format Asli DOS KAS.EXE: KI-09-YYYYMMXXXX)
     */
    public function storePenerimaanBank(Request $request)
    {
        if ($unclosedMsg = $this->checkPreviousShiftClosed()) {
            return redirect()->back()->with('error', $unclosedMsg);
        }

        $request->validate([
            'bank_sumber'       => 'required|in:Bank BJB,Bank BCA',
            'no_cek_giro'       => 'required|string',
            'nominal'           => 'required|numeric|min:1',
            'penarik'           => 'required|string',
            'no_blanko'         => 'nullable|string',
            'kode_perkiraan'    => 'nullable|string',
            'keterangan'        => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $shift = $this->getActiveShift();
            $nominal = (float) $request->input('nominal');
            $userId = Auth::id() ?? 1;

            $noKuitansi = KasMasuk::generateNoKuitansi('09');
            $bankSumber = $request->bank_sumber;
            $kodePerkiraan = $request->kode_perkiraan ?? '1.02.01.01.01'; // Bank Penerbitan
            $namaPerkiraan = "Penarikan Tunai Cek/Giro {$bankSumber}";

            $kasMasuk = KasMasuk::create([
                'no_kuitansi'           => $noKuitansi,
                'no_bkm'                => $noKuitansi,
                'shift_id'              => $shift->id,
                'tanggal_terima'        => Carbon::today(),
                'tgl_transaksi'         => Carbon::today(),
                'no_blanko'             => $request->no_blanko,
                'kode_bagian'           => '09',
                'kode_perkiraan'        => $kodePerkiraan,
                'nama_perkiraan'        => $namaPerkiraan,
                'terima_dari'           => "{$bankSumber} Cabang Tasikmalaya",
                'nama_pembayar'         => "{$bankSumber} Cabang Tasikmalaya",
                'penagih_inkaso'        => $request->penarik,
                'tipe_transaksi'        => 'bank',
                'metode_bayar'          => 'tunai', // Uang fisik masuk ke kasir
                'bank_tujuan'           => $bankSumber,
                'no_referensi_transfer' => $request->no_cek_giro,
                'jumlah_tagihan'        => $nominal,
                'total_tagihan'         => $nominal,
                'potongan_pajak'        => 0,
                'potongan_retur'        => 0,
                'jumlah_diterima'       => $nominal,
                'jumlah_dibayar'        => $nominal,
                'sisa_piutang'          => 0,
                'status_bayar'          => 'lunas',
                'status_pembayaran'     => 'lunas',
                'keterangan'            => $request->keterangan ?? "Penarikan Tunai Cek/Giro No. {$request->no_cek_giro} dari {$bankSumber} untuk Kas Operasional Kasir",
                'kasir_id'              => $userId,
                'user_id'               => $userId,
                'is_closed'             => false,
                'is_pulled_accounting'  => false,
                'status_tarik_akunting' => false,
            ]);

            // Tambah uang fisik kasir
            $shift->total_ki += $nominal;
            $shift->total_masuk += $nominal;
            $shift->saldo_akhir_teoritis = $shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko;
            $shift->saldo_akhir_fisik = $shift->saldo_akhir_teoritis;
            $shift->save();

            KasTransaksi::catatMutasi(
                'masuk',
                'Penarikan Tunai Bank (' . $bankSumber . ')',
                $nominal,
                "KI {$noKuitansi} - Pencairan Cek/Giro {$request->no_cek_giro} {$bankSumber} ({$request->penarik})",
                $kasMasuk,
                $userId,
                Carbon::today()->toDateString()
            );

            $cetakBank = [
                'no_kuitansi'    => $noKuitansi,
                'no_bkm'         => $noKuitansi,
                'kode_bagian'    => '09',
                'kode_perkiraan' => $kodePerkiraan,
                'bank'           => $bankSumber,
                'no_cek'         => $request->no_cek_giro,
                'penarik'        => $request->penarik,
                'nominal'        => $nominal,
                'terbilang'      => self::terbilang($nominal),
                'tanggal'        => Carbon::now()->translatedFormat('d F Y, H:i'),
                'kasir'          => Auth::user()->name ?? 'Kasir Kabar Priangan',
            ];

            return redirect()->back()
                ->with('success', "Penarikan Tunai Bank ({$noKuitansi}) Cek/Giro {$request->no_cek_giro} {$bankSumber} sebesar Rp " . number_format($nominal, 0, ',', '.') . " berhasil dicatat ke Kas Fisik!")
                ->with('cetak_bank', $cetakBank);
        });
    }

    /**
     * Pencairan Komisi Humas (KH ~20%) & Insentif Sales/AE (~10%) - Bagian 01
     */
    public function pengeluaranKomisi(Request $request)
    {
        $search = $request->input('search');

        // Faktur Iklan yang sudah lunas dan siap dicairkan komisinya
        $koranQuery = TransaksiKoran::where(function($q) {
            $q->where('status_order', 'Lunas')
              ->orWhere('siap_cair_kh_insentif', true)
              ->orWhere('piutang_transaksikoran', '<=', 0);
        });

        if ($search) {
            $koranQuery->where(function($q) use ($search) {
                $q->where('nofakturkoran', 'like', "%{$search}%")
                  ->orWhere('nama_pemasangkoran', 'like', "%{$search}%")
                  ->orWhere('sales_iklankoran', 'like', "%{$search}%");
            });
        }

        $fakturLunas = $koranQuery->latest()->take(20)->get();

        // Riwayat Pencairan Komisi (KO)
        $riwayatKomisi = KasKeluar::where('kode_bagian', '01')
            ->whereIn('kategori_beban', ['komisi_kh', 'insentif_ae'])
            ->latest('id')
            ->take(15)
            ->get();

        return view('pages.kasir.pengeluaran.komisi', compact('fakturLunas', 'riwayatKomisi', 'search'));
    }

    /**
     * Store Pencairan Komisi KH & Insentif AE (Format: KO-YYYYMMXXXX)
     */
    public function storePengeluaranKomisi(Request $request)
    {
        if ($unclosedMsg = $this->checkPreviousShiftClosed()) {
            return redirect()->back()->with('error', $unclosedMsg);
        }

        $request->validate([
            'faktur_ref'    => 'required|string',
            'jenis_komisi'  => 'required|string|in:komisi_kh,insentif_ae',
            'penerima'      => 'required|string',
            'nominal'       => 'required|numeric|min:1',
            'keterangan'    => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $shift = $this->getActiveShift();
            $nominal = (float) $request->input('nominal');
            $userId = Auth::id() ?? 1;

            $noBkk = KasKeluar::generateNoBkk();

            $labelKomisi = $request->jenis_komisi === 'komisi_kh' ? 'Komisi Humas (KH)' : 'Insentif Sales / AE';

            $kasKeluar = KasKeluar::create([
                'no_bkk'                => $noBkk,
                'shift_id'              => $shift->id,
                'tanggal_keluar'        => Carbon::today(),
                'tgl_transaksi'         => Carbon::today(),
                'no_bp_ref'             => $request->faktur_ref,
                'no_register_anggaran'  => 'PR-' . Carbon::today()->format('Ymd') . '-' . str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'kode_bagian'           => '01',
                'kode_perkiraan'        => '6.00.01.01.01.02',
                'nama_perkiraan'        => "Beban {$labelKomisi} Iklan",
                'diberikan_kepada'      => $request->penerima,
                'nama_penerima'         => $request->penerima,
                'kategori_beban'        => $request->jenis_komisi,
                'kategori'              => 'komisi_kh_insentif',
                'jumlah_keluar'         => $nominal,
                'nominal_keluar'        => $nominal,
                'metode_bayar'          => 'kas_fisik',
                'keterangan'            => $request->keterangan ?? "Pencairan {$labelKomisi} atas Faktur {$request->faktur_ref} kepada {$request->penerima}",
                'kasir_id'              => $userId,
                'user_id'               => $userId,
                'is_closed'             => false,
                'is_pulled_accounting'  => false,
                'status_tarik_akunting' => false,
            ]);

            $shift->total_ko += $nominal;
            $shift->total_keluar += $nominal;
            $shift->saldo_akhir_teoritis = $shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko;
            $shift->saldo_akhir_fisik = $shift->saldo_akhir_teoritis;
            $shift->save();

            KasTransaksi::catatMutasi(
                'keluar',
                'Pencairan ' . $labelKomisi,
                $nominal,
                "KO {$noBkk} - {$labelKomisi} Faktur {$request->faktur_ref} kepada {$request->penerima}",
                $kasKeluar,
                $userId,
                Carbon::today()->toDateString()
            );

            $cetakKomisi = [
                'no_bkk'        => $noBkk,
                'no_bp'         => $request->faktur_ref,
                'jenis_komisi'  => $labelKomisi,
                'penerima'      => $request->penerima,
                'nominal'       => $nominal,
                'terbilang'     => self::terbilang($nominal),
                'tanggal'       => Carbon::now()->translatedFormat('d F Y, H:i'),
                'kasir'         => Auth::user()->name ?? 'Kasir Kabar Priangan',
            ];

            return redirect()->back()
                ->with('success', "Bukti Kas Keluar ({$noBkk}) pencairan {$labelKomisi} sebesar Rp " . number_format($nominal, 0, ',', '.') . " berhasil diterbitkan!")
                ->with('cetak_komisi', $cetakKomisi);
        });
    }

    /**
     * Pencatatan Kasbon / Uang Muka Meja Kasir Operasional (Admin: Pak Eki)
     * Pengeluaran kas darurat/operasional tanpa nomor register BP yang di-ACC Keuangan.
     * Kasbon otomatis menjadi faktor pengurang pada posisi kas fisik harian meja kasir.
     */
    public function pengeluaranKasbon(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status', 'all');

        $query = Kasbon::with('kasir')->latest('id');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_kasbon', 'like', "%{$search}%")
                  ->orWhere('nama_penerima', 'like', "%{$search}%")
                  ->orWhere('keperluan', 'like', "%{$search}%")
                  ->orWhere('approved_by', 'like', "%{$search}%");
            });
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $kasbonList = $query->paginate(15);

        $totalKasbonGantung = (float) Kasbon::where('status', 'pending')->sum('nominal');
        $countKasbonGantung = Kasbon::where('status', 'pending')->count();
        $totalMenungguAcc = (float) Kasbon::where('status', 'menunggu_acc')->sum('nominal');
        $countMenungguAcc = Kasbon::where('status', 'menunggu_acc')->count();

        $totalKasbonBulanIni = (float) Kasbon::whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->where('status', '!=', 'ditolak')
            ->sum('nominal');

        // Posisi Kas Fisik Shift Aktif Pak Eki
        $activeShift = $this->getActiveShift();
        $saldoAwalShift = (float) $activeShift->saldo_awal_fisik;
        $totalKiShift = (float) KasMasuk::where('shift_id', $activeShift->id)->sum('jumlah_dibayar');
        $totalKoShift = (float) KasKeluar::where('shift_id', $activeShift->id)->sum('nominal_keluar');
        $saldoTeoritisShift = $saldoAwalShift + $totalKiShift - $totalKoShift;
        $saldoFisikLaci = max(0, $saldoTeoritisShift - $totalKasbonGantung);

        $currentUser = Auth::user();
        $isFinance = $currentUser && (
            $currentUser->isAdmin() ||
            in_array($currentUser->role, ['keuangan', 'admin_keuangan', 'kepala_divisi'])
        );

        return view('pages.kasir.pengeluaran.kasbon', compact(
            'kasbonList',
            'totalKasbonGantung',
            'countKasbonGantung',
            'totalMenungguAcc',
            'countMenungguAcc',
            'totalKasbonBulanIni',
            'activeShift',
            'saldoAwalShift',
            'totalKiShift',
            'totalKoShift',
            'saldoTeoritisShift',
            'saldoFisikLaci',
            'search',
            'statusFilter',
            'isFinance'
        ));
    }

    /**
     * Store Pencatatan Kasbon (KB-YYYYMMXXXX) oleh Pak Eki (Admin Kasir)
     * Kasbon dapat di-ACC langsung oleh Keuangan atau diajukan untuk di-ACC Keuangan.
     * Otomatis menjadi faktor pengurang pada rekonsiliasi kas fisik harian.
     */
    public function storePengeluaranKasbon(Request $request)
    {
        if ($unclosedMsg = $this->checkPreviousShiftClosed()) {
            return redirect()->back()->with('error', $unclosedMsg);
        }

        $request->validate([
            'nama_penerima' => 'required|string',
            'keperluan'     => 'required|string',
            'nominal'       => 'required|numeric|min:1000',
            'status_acc'    => 'required|in:langsung,menunggu',
            'approved_by'   => 'nullable|string',
            'keterangan'    => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $shift = $this->getActiveShift();
            $nominal = (float) $request->input('nominal');
            $userId = Auth::id() ?? 1;
            $statusAcc = $request->input('status_acc', 'langsung');

            $status = ($statusAcc === 'langsung') ? 'pending' : 'menunggu_acc';
            $approvedBy = ($statusAcc === 'langsung') ? ($request->approved_by ?? 'Ibu Eli Yulianti (Asisten Manajer Keuangan)') : null;
            $approvedAt = ($statusAcc === 'langsung') ? Carbon::now() : null;

            $noKasbon = Kasbon::generateNoKasbon();

            $kasbon = Kasbon::create([
                'no_kasbon'     => $noKasbon,
                'shift_id'      => $shift->id,
                'tanggal'       => Carbon::today(),
                'nama_penerima' => $request->nama_penerima,
                'keperluan'     => $request->keperluan,
                'nominal'       => $nominal,
                'status'        => $status,
                'approved_by'   => $approvedBy,
                'approved_at'   => $approvedAt,
                'kasir_id'      => $userId,
                'keterangan'    => $request->keterangan,
            ]);

            // Jika status langsung aktif (di-ACC), uang fisik kasir langsung dikeluarkan:
            // Kasbon otomatis menjadi faktor pengurang pada posisi kas fisik meja kasir harian.
            if ($status === 'pending') {
                $totalGantung = (float) Kasbon::where('shift_id', $shift->id)->where('status', 'pending')->sum('nominal');
                $shift->total_kasbon_gantung = $totalGantung;
                $shift->saldo_akhir_fisik = max(0, (float) ($shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko - $totalGantung));
                $shift->save();
            }

            $cetakKasbon = [
                'no_kasbon'     => $noKasbon,
                'penerima'      => $request->nama_penerima,
                'keperluan'     => $request->keperluan,
                'nominal'       => $nominal,
                'terbilang'     => self::terbilang($nominal),
                'approved_by'   => $approvedBy ?? 'Menunggu ACC Keuangan',
                'tanggal'       => Carbon::now()->translatedFormat('d F Y, H:i'),
                'kasir'         => Auth::user()->name ?? 'Eki (Admin Kasir)',
                'status'        => $status,
            ];

            $msg = $status === 'pending'
                ? "Kasbon ({$noKasbon}) sebesar Rp " . number_format($nominal, 0, ',', '.') . " kepada {$request->nama_penerima} berhasil dikeluarkan oleh Admin Kasir (ACC: {$approvedBy})! Otomatis memotong kas fisik laci kasir."
                : "Pengajuan Kasbon ({$noKasbon}) sebesar Rp " . number_format($nominal, 0, ',', '.') . " untuk {$request->nama_penerima} berhasil dicatat! Menunggu ACC Keuangan sebelum uang fisik kasir diserahkan.";

            return redirect()->back()
                ->with('success', $msg)
                ->with('cetak_kasbon', $cetakKasbon);
        });
    }

    /**
     * Persetujuan / ACC Kasbon oleh Bagian Keuangan
     * Mengubah status kasbon menjadi aktif ('pending') dan memotong uang fisik kasir.
     */
    public function accKasbon(Request $request, $id)
    {
        $kasbon = Kasbon::findOrFail($id);

        if ($kasbon->status !== 'menunggu_acc') {
            return redirect()->back()->with('error', "Kasbon ({$kasbon->no_kasbon}) tidak dalam status menunggu persetujuan!");
        }

        return DB::transaction(function () use ($kasbon, $request) {
            $shift = $this->getActiveShift();
            $approverName = $request->input('approved_by') ?? (Auth::user()->name ?? 'Ibu Eli Yulianti (Asisten Manajer Keuangan)');

            $kasbon->update([
                'status'      => 'pending',
                'approved_by' => $approverName,
                'approved_at' => Carbon::now(),
                'shift_id'    => $shift->id,
                'keterangan'  => ($kasbon->keterangan ? $kasbon->keterangan . ' | ' : '') . "Di-ACC Keuangan oleh {$approverName}",
            ]);

            // Faktor pengurang kas fisik aktif
            $totalGantung = (float) Kasbon::where('shift_id', $shift->id)->where('status', 'pending')->sum('nominal');
            $shift->total_kasbon_gantung = $totalGantung;
            $shift->saldo_akhir_fisik = max(0, (float) ($shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko - $totalGantung));
            $shift->save();

            $cetakKasbon = [
                'no_kasbon'     => $kasbon->no_kasbon,
                'penerima'      => $kasbon->nama_penerima,
                'keperluan'     => $kasbon->keperluan,
                'nominal'       => (float) $kasbon->nominal,
                'terbilang'     => self::terbilang((float) $kasbon->nominal),
                'approved_by'   => $approverName,
                'tanggal'       => Carbon::now()->translatedFormat('d F Y, H:i'),
                'kasir'         => Auth::user()->name ?? 'Eki (Admin Kasir)',
                'status'        => 'pending',
            ];

            return redirect()->back()
                ->with('success', "Kasbon ({$kasbon->no_kasbon}) Rp " . number_format($kasbon->nominal, 0, ',', '.') . " TELAH DI-ACC oleh {$approverName}! Uang fisik resmi diserahkan ke {$kasbon->nama_penerima} dan menjadi pengurang kas fisik.")
                ->with('cetak_kasbon', $cetakKasbon);
        });
    }

    /**
     * Penolakan Pengajuan Kasbon oleh Bagian Keuangan
     */
    public function tolakKasbon(Request $request, $id)
    {
        $request->validate([
            'alasan_tolak' => 'required|string|min:3',
        ]);

        $kasbon = Kasbon::findOrFail($id);

        if ($kasbon->status !== 'menunggu_acc') {
            return redirect()->back()->with('error', "Kasbon ({$kasbon->no_kasbon}) tidak dapat ditolak karena sudah disetujui/diproses!");
        }

        $kasbon->update([
            'status'       => 'ditolak',
            'alasan_tolak' => $request->alasan_tolak,
            'selesai_at'   => Carbon::now(),
        ]);

        return redirect()->back()->with('error', "Pengajuan Kasbon ({$kasbon->no_kasbon}) telah DITOLAK oleh Keuangan: '{$request->alasan_tolak}'. Uang fisik tidak dikeluarkan.");
    }

    /**
     * Selesaikan Kasbon: Uang dikembalikan (lunas) atau Dibuatkan BP resmi (masuk_bp)
     */
    public function selesaikanKasbon(Request $request, $id)
    {
        $request->validate([
            'tipe_selesai' => 'required|in:lunas,masuk_bp',
            'catatan'      => 'nullable|string',
        ]);

        $kasbon = Kasbon::findOrFail($id);

        if ($kasbon->status !== 'pending') {
            return redirect()->back()->with('error', "Kasbon ({$kasbon->no_kasbon}) telah diselesaikan atau belum di-ACC!");
        }

        return DB::transaction(function () use ($kasbon, $request) {
            $shift = $this->getActiveShift();

            $kasbon->update([
                'status'     => $request->tipe_selesai,
                'keterangan' => ($kasbon->keterangan ? $kasbon->keterangan . ' | ' : '') . ($request->catatan ?? ('Diselesaikan: ' . ($request->tipe_selesai === 'lunas' ? 'Uang dikembalikan tunai ke kasir' : 'Telah dibuatkan nomor BP Anggaran'))),
                'selesai_at' => Carbon::now(),
            ]);

            // Kasbon gantung berkurang
            $totalGantung = (float) Kasbon::where('shift_id', $shift->id)->where('status', 'pending')->sum('nominal');
            $shift->total_kasbon_gantung = $totalGantung;

            // Jika lunas tunai: uang fisik kembali masuk laci
            // Jika masuk_bp: uang fisik tetap di luar karena sudah dibelanjakan, dan nanti dibukukan via KO resmi
            if ($request->tipe_selesai === 'lunas') {
                $shift->saldo_akhir_fisik = max(0, (float) ($shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko - $totalGantung));
            }
            $shift->save();

            $msg = $request->tipe_selesai === 'lunas'
                ? "Kasbon ({$kasbon->no_kasbon}) sebesar Rp " . number_format($kasbon->nominal, 0, ',', '.') . " telah dilunasi! Uang fisik telah kembali ke meja kasir Pak Eki."
                : "Kasbon ({$kasbon->no_kasbon}) sebesar Rp " . number_format($kasbon->nominal, 0, ',', '.') . " telah ditandai Masuk BP Anggaran!";

            return redirect()->back()->with('success', $msg);
        });
    }

    /**
     * Laporan & Mutasi Kas: Rekapitulasi Kas Masuk & Keluar (Per Hari / Per Bulan / Per Tahun)
     * Format mengadopsi standar cetakan DOS HU. PRIANGAN REKAP KAS (Foto Fisik KAS.EXE)
     */
    public function laporanRekap(Request $request)
    {
        $periodeTipe = $request->input('periode_tipe', 'bulan'); // hari, bulan, tahun, rentang
        $tanggalPilihan = $request->input('tanggal', Carbon::today()->toDateString());
        $bulanPilihan = (int) $request->input('bulan', Carbon::now()->month);
        $tahunPilihan = (int) $request->input('tahun', Carbon::now()->year);
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $bagianFilter = $request->input('kode_bagian', 'all');

        $kiQuery = KasMasuk::query();
        $koQuery = KasKeluar::query();

        if ($periodeTipe === 'hari') {
            $kiQuery->whereDate('tanggal_terima', $tanggalPilihan);
            $koQuery->whereDate('tanggal_keluar', $tanggalPilihan);
            $labelPeriode = "Hari: " . Carbon::parse($tanggalPilihan)->translatedFormat('d F Y');
        } elseif ($periodeTipe === 'tahun') {
            $kiQuery->whereYear('tanggal_terima', $tahunPilihan);
            $koQuery->whereYear('tanggal_keluar', $tahunPilihan);
            $labelPeriode = "Tahun: {$tahunPilihan}";
        } elseif ($periodeTipe === 'rentang') {
            $kiQuery->whereBetween('tanggal_terima', [$startDate, $endDate]);
            $koQuery->whereBetween('tanggal_keluar', [$startDate, $endDate]);
            $labelPeriode = "Periode: " . Carbon::parse($startDate)->format('d/m/Y') . " s/d " . Carbon::parse($endDate)->format('d/m/Y');
        } else {
            // Default: Per Bulan
            $kiQuery->whereMonth('tanggal_terima', $bulanPilihan)->whereYear('tanggal_terima', $tahunPilihan);
            $koQuery->whereMonth('tanggal_keluar', $bulanPilihan)->whereYear('tanggal_keluar', $tahunPilihan);
            $labelPeriode = "Bulan: " . Carbon::create($tahunPilihan, $bulanPilihan, 1)->translatedFormat('F Y');
        }

        if ($bagianFilter !== 'all') {
            $kiQuery->where('kode_bagian', $bagianFilter);
            $koQuery->where('kode_bagian', $bagianFilter);
        }

        $kasMasuks = $kiQuery->orderBy('tanggal_terima', 'asc')->get();
        $kasKeluars = $koQuery->orderBy('tanggal_keluar', 'asc')->get();

        $totalMasuk = (float) $kasMasuks->sum('jumlah_dibayar');
        $totalKeluar = (float) $kasKeluars->sum('nominal_keluar');
        $saldoSurplusDefisit = $totalMasuk - $totalKeluar;

        // Klasifikasi COA Sesuai Format Asli Rekap Kas HU. Priangan
        $coaPenerimaan = [
            '1.02.01.01.01'    => ['nama' => 'BANK PENERBITAN (PENARIKAN GIRO)', 'nominal' => 0],
            '1.00.04.01.01.00' => ['nama' => 'SIRKULASI KORAN AGEN', 'nominal' => 0],
            '1.00.04.01.01.01' => ['nama' => 'PELUNASAN PIUTANG IKLAN', 'nominal' => 0],
            '2.00.01.01.01.01' => ['nama' => 'HUTANG KEPADA KONSUMEN IKLAN (UANG MUKA)', 'nominal' => 0],
            '7.00.02.01.02'    => ['nama' => 'PENJUALAN KORAN SISA CETAK', 'nominal' => 0],
            '7.00.01.01.03'    => ['nama' => 'PENERIMAAN KARYAWAN', 'nominal' => 0],
            '7.00.01.01.04'    => ['nama' => 'JASA IKLAN / LAIN-LAIN', 'nominal' => 0],
        ];

        foreach ($kasMasuks as $ki) {
            $coa = $ki->kode_perkiraan ?? '7.00.01.01.04';
            if (isset($coaPenerimaan[$coa])) {
                $coaPenerimaan[$coa]['nominal'] += (float) $ki->jumlah_dibayar;
            } else {
                $coaPenerimaan['7.00.01.01.04']['nominal'] += (float) $ki->jumlah_dibayar;
            }
        }

        $coaPengeluaran = [
            '1.02.01.01.01'    => ['nama' => 'BANK PENERBITAN (SETOR BANK)', 'nominal' => 0],
            '2.00.03.01.01'    => ['nama' => 'HUTANG PAJAK (PPN 11% & PPh 23)', 'nominal' => 0],
            '6.00.01.00.01.02' => ['nama' => 'BEBAN IKLAN / KOMISI KH & INSENTIF AE', 'nominal' => 0],
            '6.00.01.01.01.01' => ['nama' => 'BEBAN KEUANGAN', 'nominal' => 0],
            '6.00.01.00.01.04' => ['nama' => 'BEBAN UMUM & SDM / BBM / RT', 'nominal' => 0],
            '8.00.01.01.03'    => ['nama' => 'PENGELUARAN LAIN-LAIN / KASBON', 'nominal' => 0],
        ];

        foreach ($kasKeluars as $ko) {
            $coa = $ko->kode_perkiraan ?? '8.00.01.01.03';
            if (isset($coaPengeluaran[$coa])) {
                $coaPengeluaran[$coa]['nominal'] += (float) $ko->nominal_keluar;
            } else {
                $coaPengeluaran['8.00.01.01.03']['nominal'] += (float) $ko->nominal_keluar;
            }
        }

        $saldoKasTersedia = $totalMasuk; // Kas tersedia dari mutasi periode
        $saldoAkhirRekap = $saldoSurplusDefisit;

        return view('pages.kasir.laporan.rekap', compact(
            'kasMasuks',
            'kasKeluars',
            'totalMasuk',
            'totalKeluar',
            'saldoSurplusDefisit',
            'saldoKasTersedia',
            'saldoAkhirRekap',
            'coaPenerimaan',
            'coaPengeluaran',
            'periodeTipe',
            'tanggalPilihan',
            'bulanPilihan',
            'tahunPilihan',
            'startDate',
            'endDate',
            'labelPeriode',
            'bagianFilter'
        ));
    }

    /**
     * Export Rekapitulasi Kas ke CSV / Excel Format
     */
    public function exportLaporanRekap(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $kasMasuks = KasMasuk::whereBetween('tanggal_terima', [$startDate, $endDate])->orderBy('id')->get();
        $kasKeluars = KasKeluar::whereBetween('tanggal_keluar', [$startDate, $endDate])->orderBy('id')->get();

        $filename = "Rekap_Kas_{$startDate}_sampai_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($kasMasuks, $kasKeluars, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($file, ['HARIAN UMUM KABAR PRIANGAN - REKAPITULASI KAS (SISTEM KAS)']);
            fputcsv($file, ["Periode: {$startDate} s/d {$endDate}"]);
            fputcsv($file, []);

            fputcsv($file, ['--- KAS MASUK (KI) ---']);
            fputcsv($file, ['No', 'No Kuitansi', 'Tanggal', 'Divisi', 'Kode COA', 'Nama Perkiraan', 'Terima Dari', 'Jumlah (Rp)', 'Status']);
            foreach ($kasMasuks as $idx => $ki) {
                fputcsv($file, [
                    $idx + 1,
                    $ki->no_kuitansi ?? $ki->no_bkm,
                    $ki->tanggal_terima ? Carbon::parse($ki->tanggal_terima)->format('d/m/Y') : '-',
                    $ki->nama_divisi,
                    $ki->kode_perkiraan,
                    $ki->nama_perkiraan,
                    $ki->terima_dari ?? $ki->nama_pembayar,
                    $ki->jumlah_dibayar ?? $ki->jumlah_diterima,
                    strtoupper($ki->status_bayar ?? 'Lunas'),
                ]);
            }
            fputcsv($file, ['', '', '', '', '', '', 'TOTAL KAS MASUK', $kasMasuks->sum('jumlah_dibayar'), '']);
            fputcsv($file, []);

            fputcsv($file, ['--- KAS KELUAR (KO) ---']);
            fputcsv($file, ['No', 'No BKK', 'Tanggal', 'Divisi', 'No Register BP', 'Kode COA', 'Diberikan Kepada', 'Jumlah (Rp)', 'Kategori']);
            foreach ($kasKeluars as $idx => $ko) {
                fputcsv($file, [
                    $idx + 1,
                    $ko->no_bkk,
                    $ko->tanggal_keluar ? Carbon::parse($ko->tanggal_keluar)->format('d/m/Y') : '-',
                    $ko->nama_divisi,
                    $ko->no_register_anggaran ?? $ko->no_bp_ref,
                    $ko->kode_perkiraan,
                    $ko->diberikan_kepada ?? $ko->nama_penerima,
                    $ko->nominal_keluar ?? $ko->jumlah_keluar,
                    $ko->kategori_beban ?? $ko->kategori,
                ]);
            }
            fputcsv($file, ['', '', '', '', '', '', 'TOTAL KAS KELUAR', $kasKeluars->sum('nominal_keluar'), '']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Laporan Mutasi Rekening Bank (BJB & BCA)
     * Format mengadopsi standar cetakan DOS HU. PRIANGAN LAPORAN REKENING BANK (Foto Fisik KAS.EXE)
     */
    public function laporanMutasiBank(Request $request)
    {
        $bankDipilih = $request->input('bank', 'all'); // all, BJB, BCA
        $bulan = (int) $request->input('bulan', Carbon::now()->month);
        $tahun = (int) $request->input('tahun', Carbon::now()->year);

        // Rekening Operasional Kabar Priangan:
        // 1. Bank Jabar BJB: 05.00.01.006270.9 Cabang Tasikmalaya
        // 2. Bank BCA: 054.1234567 Cabang Tasikmalaya
        $queryBjb = KasMasuk::where(function($q) {
                $q->where('bank_tujuan', 'like', '%BJB%')
                  ->orWhere('penagih_inkaso', 'like', '%BJB%');
            })
            ->whereMonth('tanggal_terima', $bulan)
            ->whereYear('tanggal_terima', $tahun)
            ->orderBy('tanggal_terima', 'asc')
            ->get();

        $queryBca = KasMasuk::where(function($q) {
                $q->where('bank_tujuan', 'like', '%BCA%')
                  ->orWhere('penagih_inkaso', 'like', '%BCA%');
            })
            ->whereMonth('tanggal_terima', $bulan)
            ->whereYear('tanggal_terima', $tahun)
            ->orderBy('tanggal_terima', 'asc')
            ->get();

        $totalDebetBjb = (float) $queryBjb->where('kode_bagian', '09')->sum('jumlah_dibayar'); // Penarikan (Debet)
        $totalKreditBjb = (float) $queryBjb->where('kode_bagian', '!=', '09')->sum('jumlah_dibayar'); // Setoran (Kredit)

        $totalDebetBca = (float) $queryBca->where('kode_bagian', '09')->sum('jumlah_dibayar');
        $totalKreditBca = (float) $queryBca->where('kode_bagian', '!=', '09')->sum('jumlah_dibayar');

        $namaBulan = Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y');

        return view('pages.kasir.laporan.mutasi-bank', compact(
            'queryBjb',
            'queryBca',
            'totalDebetBjb',
            'totalKreditBjb',
            'totalDebetBca',
            'totalKreditBca',
            'bankDipilih',
            'bulan',
            'tahun',
            'namaBulan'
        ));
    }

    /**
     * Laporan Mutasi Kas per Bagan Akun Perkiraan (COA)
     */
    public function laporanPerkiraan(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);

        $daftarCoa = [
            '1.00.04.01.01.01' => 'Piutang Iklan',
            '1.00.04.01.01.00' => 'Piutang Sirkulasi Koran Agen',
            '2.00.01.01.01.01' => 'Uang Muka / Titipan Iklan',
            '2.00.01.01.01.00' => 'Uang Muka Sirkulasi',
            '2.00.03.01.01'    => 'Potongan PPN / PPh Iklan',
            '5.00.01.01.02'    => 'Beban Liputan Redaksi',
            '6.00.01.00.01.04' => 'Beban Umum & SDM / Rumah Tangga / BBM',
            '6.00.01.01.01.02' => 'Beban Komisi Humas & Insentif AE',
            '7.00.01.01.01'    => 'Jasa Giro / Bunga Bank BJB',
            '7.00.01.01.02'    => 'Penjualan Koran Sisa & Eceran Lepas',
        ];

        $rekapPerkiraan = [];

        foreach ($daftarCoa as $kode => $nama) {
            $totalDebetMasuk = (float) KasMasuk::where('kode_perkiraan', $kode)
                ->whereMonth('tanggal_terima', $bulan)
                ->whereYear('tanggal_terima', $tahun)
                ->sum('jumlah_diterima');

            $totalKreditKeluar = (float) KasKeluar::where('kode_perkiraan', $kode)
                ->whereMonth('tanggal_keluar', $bulan)
                ->whereYear('tanggal_keluar', $tahun)
                ->sum('jumlah_keluar');

            $rekapPerkiraan[] = (object)[
                'kode'   => $kode,
                'nama'   => $nama,
                'masuk'  => $totalDebetMasuk,
                'keluar' => $totalKreditKeluar,
                'saldo'  => $totalDebetMasuk - $totalKreditKeluar,
            ];
        }

        return view('pages.kasir.laporan.perkiraan', compact('rekapPerkiraan', 'bulan', 'tahun'));
    }

    /**
     * Laporan Posisi Saldo Kas Fisik vs Kas Bank
     */
    public function laporanPosisiKas(Request $request)
    {
        $activeShift = $this->getActiveShift();

        $today = Carbon::today()->toDateString();
        $totalKiHariIni = (float) KasMasuk::whereDate('tanggal_terima', $today)->sum('jumlah_diterima');
        $totalKoHariIni = (float) KasKeluar::whereDate('tanggal_keluar', $today)->sum('jumlah_keluar');

        $saldoAwal = (float) $activeShift->saldo_awal_fisik;
        $saldoTeoritis = $saldoAwal + $totalKiHariIni - $totalKoHariIni;

        // Faktor pengurang kas fisik harian: Kasbon / Uang Muka Operasional Gantung (Pak Eki)
        $daftarKasbonGantung = Kasbon::where('status', 'pending')->latest('id')->get();
        $totalKasbonGantung = (float) $daftarKasbonGantung->sum('nominal');
        $saldoFisikDiharapkan = max(0, $saldoTeoritis - $totalKasbonGantung);

        // Saldo fisik kasir aktual di laci:
        $saldoFisik = (float) ($activeShift->saldo_akhir_fisik > 0 ? $activeShift->saldo_akhir_fisik : $saldoFisikDiharapkan);
        $totalFisikPlusKasbon = $saldoFisik + $totalKasbonGantung;
        $selisih = $totalFisikPlusKasbon - $saldoTeoritis;

        $totalBankBulanIni = (float) KasMasuk::where('kode_bagian', '14')
            ->whereMonth('tanggal_terima', Carbon::now()->month)
            ->sum('jumlah_diterima');

        return view('pages.kasir.laporan.posisi-kas', compact(
            'activeShift',
            'saldoAwal',
            'totalKiHariIni',
            'totalKoHariIni',
            'saldoTeoritis',
            'totalKasbonGantung',
            'daftarKasbonGantung',
            'saldoFisikDiharapkan',
            'saldoFisik',
            'selisih',
            'totalBankBulanIni'
        ));
    }

    /**
     * Monitoring Kasbon & Uang Muka Operasional
     */
    public function laporanKasbon(Request $request)
    {
        $search = $request->input('search');

        // Uang Muka / Titipan (COA 2.00.01.01.01.01 atau 2.00.01.01.01.00)
        $uangMukaQuery = KasMasuk::whereIn('kode_perkiraan', ['2.00.01.01.01.01', '2.00.01.01.01.00'])
            ->orWhere('status_bayar', 'uang_muka');

        if ($search) {
            $uangMukaQuery->where(function($q) use ($search) {
                $q->where('terima_dari', 'like', "%{$search}%")
                  ->orWhere('no_kuitansi', 'like', "%{$search}%");
            });
        }

        $daftarUangMuka = $uangMukaQuery->latest('id')->paginate(15);

        return view('pages.kasir.laporan.kasbon', compact('daftarUangMuka', 'search'));
    }

    /**
     * Utilitas Kontrol: Tutup Kas Harian (Closing Shift)
     * Rekonsiliasi Kas Opname Fisik + Kasbon Gantung vs Saldo Sistem Teoritis
     */
    public function utilitasClosing(Request $request)
    {
        $activeShift = $this->getActiveShift();
        $today = Carbon::today()->toDateString();

        $totalKi = (float) KasMasuk::whereDate('tanggal_terima', $today)->sum('jumlah_dibayar');
        $totalKo = (float) KasKeluar::whereDate('tanggal_keluar', $today)->sum('nominal_keluar');
        $saldoAwal = (float) $activeShift->saldo_awal_fisik;
        $saldoTeoritis = $saldoAwal + $totalKi - $totalKo;

        // Saldo Kasbon Gantung (Uang keluar dari meja kasir yang belum diselesaikan/masuk BP)
        $daftarKasbonGantung = Kasbon::where('status', 'pending')->latest('id')->get();
        $totalKasbonGantung = (float) $daftarKasbonGantung->sum('nominal');

        // Riwayat Closing sebelumnya
        $riwayatClosing = ShiftKasir::with('kasir')
            ->whereIn('status', ['closed', 'ditutup'])
            ->latest('id')
            ->take(10)
            ->get();

        return view('pages.kasir.utilitas.closing', compact(
            'activeShift',
            'saldoAwal',
            'totalKi',
            'totalKo',
            'saldoTeoritis',
            'daftarKasbonGantung',
            'totalKasbonGantung',
            'riwayatClosing'
        ));
    }

    /**
     * Store Tutup Kas Harian (Kunci Shift & Tandai ready_to_pull Akuntansi)
     * Rekonsiliasi: Saldo Teoritis = (Fisik Laci Kasir + Kasbon Gantung Berjalan)
     */
    public function storeUtilitasClosing(Request $request)
    {
        $request->validate([
            'pecahan_100k' => 'nullable|integer|min:0',
            'pecahan_50k'  => 'nullable|integer|min:0',
            'pecahan_20k'  => 'nullable|integer|min:0',
            'pecahan_10k'  => 'nullable|integer|min:0',
            'pecahan_5k'   => 'nullable|integer|min:0',
            'pecahan_2k'   => 'nullable|integer|min:0',
            'pecahan_1k'   => 'nullable|integer|min:0',
            'pecahan_koin' => 'nullable|numeric|min:0',
            'catatan'      => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $shift = $this->getActiveShift();

            // Hitung total fisik pecahan uang kasir
            $p100 = ((int) $request->input('pecahan_100k', 0)) * 100000;
            $p50  = ((int) $request->input('pecahan_50k', 0)) * 50000;
            $p20  = ((int) $request->input('pecahan_20k', 0)) * 20000;
            $p10  = ((int) $request->input('pecahan_10k', 0)) * 10000;
            $p5   = ((int) $request->input('pecahan_5k', 0)) * 5000;
            $p2   = ((int) $request->input('pecahan_2k', 0)) * 2000;
            $p1   = ((int) $request->input('pecahan_1k', 0)) * 1000;
            $koin = (float) $request->input('pecahan_koin', 0);

            $totalFisikLaci = $p100 + $p50 + $p20 + $p10 + $p5 + $p2 + $p1 + $koin;

            // Kasbon Gantung Aktif (sebagai komponen penyeimbang fisik kasir)
            $totalKasbonGantung = (float) Kasbon::where('status', 'pending')->sum('nominal');
            $totalFisikPlusKasbon = $totalFisikLaci + $totalKasbonGantung;

            $totalKi = (float) KasMasuk::where('shift_id', $shift->id)->sum('jumlah_dibayar');
            $totalKo = (float) KasKeluar::where('shift_id', $shift->id)->sum('nominal_keluar');
            $saldoTeoritis = (float) ($shift->saldo_awal_fisik + $totalKi - $totalKo);
            $selisih = $totalFisikPlusKasbon - $saldoTeoritis;

            // 1. Kunci Shift
            $shift->update([
                'total_ki'             => $totalKi,
                'total_ko'             => $totalKo,
                'total_masuk'          => $totalKi,
                'total_keluar'         => $totalKo,
                'saldo_akhir_teoritis' => $saldoTeoritis,
                'saldo_akhir_fisik'    => $totalFisikLaci,
                'total_kasbon_gantung' => $totalKasbonGantung,
                'selisih'              => $selisih,
                'selisih_kas'          => $selisih,
                'status'               => 'closed',
                'closed_at'            => Carbon::now(),
                'catatan_serah_terima' => $request->input('catatan'),
            ]);

            // 2. Kunci seluruh transaksi hari itu dan tandai flag 'ready_to_pull' untuk modul Akuntansi
            KasMasuk::where('shift_id', $shift->id)->update([
                'is_closed'             => true,
                'is_pulled_accounting'  => false,
                'status_tarik_akunting' => true,
            ]);

            KasKeluar::where('shift_id', $shift->id)->update([
                'is_closed'             => true,
                'is_pulled_accounting'  => false,
                'status_tarik_akunting' => true,
            ]);

            $labelSelisih = $selisih == 0 ? 'SEIMBANG (ZERO DIFFERENCE / BALANCE)' : ($selisih > 0 ? "+Rp " . number_format($selisih, 0, ',', '.') . " (LEBIH FISIK)" : "-Rp " . number_format(abs($selisih), 0, ',', '.') . " (SELISIH KURANG)");

            return redirect()->route('kasir.dashboard')
                ->with('success', "Tutup Kas Harian (Closing Shift) Berhasil! Fisik Laci: Rp " . number_format($totalFisikLaci, 0, ',', '.') . " + Kasbon Gantung: Rp " . number_format($totalKasbonGantung, 0, ',', '.') . " | Saldo Teoritis: Rp " . number_format($saldoTeoritis, 0, ',', '.') . " [{$labelSelisih}]. Seluruh transaksi kasir hari ini telah dikunci dan siap ditarik oleh modul Akuntansi!");
        });
    }

    /**
     * Utilitas Kontrol: Perbaikan & Koreksi Transaksi (Void)
     */
    public function utilitasKoreksi(Request $request)
    {
        $shift = $this->getActiveShift();

        // Ambil transaksi yang belum closed (masih open shift) untuk dapat dikoreksi/void
        $openMasuk = KasMasuk::where('shift_id', $shift->id)
            ->where('is_closed', false)
            ->where('status_void', false)
            ->latest('id')
            ->get();

        $openKeluar = KasKeluar::where('shift_id', $shift->id)
            ->where('is_closed', false)
            ->where('status_void', false)
            ->latest('id')
            ->get();

        $voidHistory = KasMasuk::where('status_void', true)
            ->latest('void_at')
            ->take(10)
            ->get();

        return view('pages.kasir.utilitas.koreksi', compact('openMasuk', 'openKeluar', 'voidHistory', 'shift'));
    }

    /**
     * Process Void / Pembatalan Transaksi Kasir dengan Audit Log
     */
    public function voidTransaksi(Request $request, $id)
    {
        $request->validate([
            'tipe_dokumen' => 'required|in:KI,KO',
            'alasan_void'  => 'required|string|min:5',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $userId = Auth::id() ?? 1;

            if ($request->tipe_dokumen === 'KI') {
                $trx = KasMasuk::lockForUpdate()->findOrFail($id);

                if ($trx->is_closed) {
                    return redirect()->back()->with('error', "Transaksi ({$trx->no_kuitansi}) telah ditutup (Closing Harian) dan tidak dapat dibatalkan!");
                }

                $trx->update([
                    'status_void' => true,
                    'void_by'     => $userId,
                    'void_at'     => Carbon::now(),
                    'alasan_void' => $request->alasan_void,
                ]);

                // Rollback Piutang jika Iklan
                if ($trx->tipe_transaksi === 'iklan' && $trx->no_faktur_ref) {
                    $koran = TransaksiKoran::where('nofakturkoran', $trx->no_faktur_ref)->first();
                    if ($koran) {
                        $koran->jumlahbayar_transaksikoran = max(0, $koran->jumlahbayar_transaksikoran - $trx->jumlah_diterima);
                        $koran->piutang_transaksikoran += $trx->jumlah_diterima;
                        $koran->status_order = 'Belum Lunas';
                        $koran->siap_cair_kh_insentif = false;
                        $koran->save();
                    }
                }

                // Rollback Piutang Agen jika Sirkulasi
                if ($trx->tipe_transaksi === 'sirkulasi' && $trx->kode_agen_ref) {
                    $agen = Agen::where('kode_agen', $trx->kode_agen_ref)->first();
                    if ($agen) {
                        $agen->saldo_piutang += ($trx->jumlah_diterima + $trx->potongan_retur);
                        $agen->save();
                    }
                }

                // Kurangi shift
                $shift = ShiftKasir::find($trx->shift_id);
                if ($shift) {
                    $shift->total_ki = max(0, $shift->total_ki - $trx->jumlah_diterima);
                    $shift->total_masuk = max(0, $shift->total_masuk - $trx->jumlah_diterima);
                    $shift->saldo_akhir_teoritis = $shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko;
                    $shift->save();
                }

                return redirect()->back()->with('success', "Transaksi Kas Masuk ({$trx->no_kuitansi}) berhasil di-VOID dan saldo piutang telah dikembalikan!");

            } else {
                $trx = KasKeluar::lockForUpdate()->findOrFail($id);

                if ($trx->is_closed) {
                    return redirect()->back()->with('error', "Transaksi ({$trx->no_bkk}) telah ditutup (Closing Harian) dan tidak dapat dibatalkan!");
                }

                $trx->update([
                    'status_void' => true,
                    'void_by'     => $userId,
                    'void_at'     => Carbon::now(),
                    'alasan_void' => $request->alasan_void,
                ]);

                // Kembalikan status BP menjadi approved jika ada
                if ($trx->bp_id) {
                    $bp = BuktiPengeluaran::find($trx->bp_id);
                    if ($bp) {
                        $bp->update(['status' => 'approved', 'dicairkan_oleh' => null, 'dicairkan_at' => null]);
                    }
                }

                // Kurangi shift
                $shift = ShiftKasir::find($trx->shift_id);
                if ($shift) {
                    $shift->total_ko = max(0, $shift->total_ko - $trx->jumlah_keluar);
                    $shift->total_keluar = max(0, $shift->total_keluar - $trx->jumlah_keluar);
                    $shift->saldo_akhir_teoritis = $shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko;
                    $shift->save();
                }

                return redirect()->back()->with('success', "Transaksi Kas Keluar ({$trx->no_bkk}) berhasil di-VOID dan status BP dikembalikan ke Approved!");
            }
        });
    }

    /**
     * Utilitas Kontrol: Cetak Ulang Kuitansi (BKM / BKK)
     */
    public function utilitasCetakUlang(Request $request)
    {
        $search = $request->input('search');
        $jenisFilter = $request->input('jenis', 'all');

        $dokumenList = new Collection();

        // 1. Kas Masuk (KI)
        $masukQ = KasMasuk::with('kasir')->latest('id');
        if ($search) {
            $masukQ->where(function($q) use ($search) {
                $q->where('no_kuitansi', 'like', "%{$search}%")
                  ->orWhere('no_bkm', 'like', "%{$search}%")
                  ->orWhere('terima_dari', 'like', "%{$search}%")
                  ->orWhere('nama_pembayar', 'like', "%{$search}%")
                  ->orWhere('no_faktur_ref', 'like', "%{$search}%");
            });
        }
        foreach ($masukQ->take(25)->get() as $m) {
            $dokumenList->push((object)[
                'id'          => $m->id,
                'no_dokumen'  => $m->no_kuitansi ?? $m->no_bkm,
                'tipe'        => 'KI',
                'jenis_nama'  => 'Kas Masuk (KI - ' . $m->nama_divisi . ')',
                'kode_bagian' => $m->kode_bagian,
                'kode_coa'    => $m->kode_perkiraan,
                'tanggal'     => $m->tanggal_terima ? Carbon::parse($m->tanggal_terima)->format('d/m/Y') : '-',
                'pihak'       => $m->terima_dari ?? $m->nama_pembayar,
                'uraian'      => $m->keterangan ?? "Penerimaan {$m->tipe_transaksi}",
                'nominal'     => (float) $m->jumlah_diterima,
                'metode'      => strtoupper($m->metode_bayar),
                'kasir'       => $m->kasir->name ?? 'Kasir Kabar Priangan',
                'is_void'     => (bool) $m->status_void,
            ]);
        }

        // 2. Kas Keluar (KO)
        $keluarQ = KasKeluar::with('kasir')->latest('id');
        if ($search) {
            $keluarQ->where(function($q) use ($search) {
                $q->where('no_bkk', 'like', "%{$search}%")
                  ->orWhere('no_register_anggaran', 'like', "%{$search}%")
                  ->orWhere('diberikan_kepada', 'like', "%{$search}%")
                  ->orWhere('nama_penerima', 'like', "%{$search}%");
            });
        }
        foreach ($keluarQ->take(25)->get() as $k) {
            $dokumenList->push((object)[
                'id'          => $k->id,
                'no_dokumen'  => $k->no_bkk,
                'tipe'        => 'KO',
                'jenis_nama'  => 'Kas Keluar (KO - ' . $k->nama_divisi . ')',
                'kode_bagian' => $k->kode_bagian,
                'kode_coa'    => $k->kode_perkiraan,
                'tanggal'     => $k->tanggal_keluar ? Carbon::parse($k->tanggal_keluar)->format('d/m/Y') : '-',
                'pihak'       => $k->diberikan_kepada ?? $k->nama_penerima,
                'uraian'      => $k->keterangan ?? "Pencairan " . ($k->kategori_beban ?? 'Umum'),
                'nominal'     => (float) $k->jumlah_keluar,
                'metode'      => strtoupper($k->metode_bayar === 'kas_fisik' ? 'Kas Fisik' : 'Transfer'),
                'kasir'       => $k->kasir->name ?? 'Kasir Kabar Priangan',
                'is_void'     => (bool) $k->status_void,
            ]);
        }

        if ($jenisFilter !== 'all') {
            $dokumenList = $dokumenList->filter(function($doc) use ($jenisFilter) {
                return $doc->tipe === $jenisFilter;
            });
        }

        return view('pages.kasir.utilitas.cetak-ulang', compact('dokumenList', 'search', 'jenisFilter'));
    }

    /**
     * Utilitas Kontrol: Menu Buka Kembali Kas (Reopen Shift)
     * Membutuhkan Otorisasi / Perizinan dari Manager Keuangan atau Bagian Keuangan
     */
    public function bukaKasIndex(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status', 'all');

        $query = ShiftKasir::with(['kasir', 'reopenRequester', 'reopenApprover'])
            ->latest('id');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereDate('tanggal_shift', 'like', "%{$search}%")
                  ->orWhereDate('tgl_shift', 'like', "%{$search}%")
                  ->orWhere('catatan_serah_terima', 'like', "%{$search}%")
                  ->orWhere('reopen_reason', 'like', "%{$search}%")
                  ->orWhereHas('kasir', function($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($statusFilter === 'requested') {
            $query->where('reopen_status', 'requested');
        } elseif ($statusFilter === 'closed') {
            $query->whereIn('status', ['closed', 'ditutup']);
        } elseif ($statusFilter === 'open') {
            $query->whereIn('status', ['open', 'aktif']);
        }

        $shifts = $query->paginate(15);

        $currentUser = Auth::user();
        $isFinanceManager = $currentUser && (
            $currentUser->isAdmin() ||
            in_array($currentUser->role, ['keuangan', 'admin_keuangan', 'kepala_divisi'])
        );

        return view('pages.kasir.utilitas.buka-kas', compact(
            'shifts',
            'search',
            'statusFilter',
            'isFinanceManager'
        ));
    }

    /**
     * Pengajuan Buka Kembali Kas oleh Kasir
     */
    public function ajukanBukaKembaliKas(Request $request, $id)
    {
        $shift = ShiftKasir::findOrFail($id);

        if ($shift->status !== 'closed' && $shift->status !== 'ditutup') {
            return redirect()->back()->with('error', 'Hanya shift kasir yang berstatus CLOSED (TUTUP) yang dapat diajukan untuk dibuka kembali!');
        }

        $request->validate([
            'alasan' => 'required|string|min:5',
        ]);

        $shift->update([
            'reopen_status'       => 'requested',
            'reopen_reason'       => $request->alasan,
            'reopen_requested_by' => Auth::id(),
            'reopen_requested_at' => Carbon::now(),
            'reopen_notes'        => null,
        ]);

        $tgl = Carbon::parse($shift->tanggal_shift ?? $shift->tgl_shift)->format('d/m/Y');

        return redirect()->back()->with('success', "Permohonan Buka Kembali Kas tanggal {$tgl} berhasil diajukan! Menunggu otorisasi resmi dari Manager Keuangan.");
    }

    /**
     * Setujui / Otorisasi Buka Kembali Kas (Hanya oleh / dengan kredensial Manager Keuangan)
     */
    public function setujuiBukaKembaliKas(Request $request, $id)
    {
        $shift = ShiftKasir::findOrFail($id);

        $currentUser = Auth::user();
        $isCurrentFinance = $currentUser && (
            $currentUser->isAdmin() ||
            in_array($currentUser->role, ['keuangan', 'admin_keuangan', 'kepala_divisi'])
        );

        $approver = null;

        // Jika user yang sedang login adalah Manager Keuangan / Admin, bisa langsung ACC
        if ($isCurrentFinance) {
            $approver = $currentUser;
        } else {
            // Jika login sebagai Kasir biasa, verifikasi kredensial Manager Keuangan yang mendampingi
            $request->validate([
                'manager_email'    => 'required|email',
                'manager_password' => 'required|string',
            ], [
                'manager_email.required'    => 'Email Manager Keuangan wajib diisi untuk otorisasi!',
                'manager_password.required' => 'Password Manager Keuangan wajib diisi!',
            ]);

            $manager = User::where('email', $request->manager_email)->first();

            if (!$manager || !Hash::check($request->manager_password, $manager->password)) {
                return redirect()->back()->with('error', 'Otorisasi Gagal: Email atau Password Manager Keuangan salah!');
            }

            if (!$manager->isAdmin() && !in_array($manager->role, ['keuangan', 'admin_keuangan', 'kepala_divisi'])) {
                return redirect()->back()->with('error', 'Otorisasi Ditolak: Akun tersebut bukan Manager Keuangan / Bagian Keuangan yang berwenang!');
            }

            $approver = $manager;
        }

        return DB::transaction(function () use ($shift, $approver, $request) {
            // 1. Ubah status shift kembali menjadi 'open'
            $shift->update([
                'status'              => 'open',
                'reopen_status'       => 'approved',
                'reopen_approved_by'  => $approver->id,
                'reopen_approved_at'  => Carbon::now(),
                'reopen_notes'        => $request->input('catatan', 'Disetujui oleh ' . $approver->name),
            ]);

            // 2. Buka kembali seluruh transaksi kas masuk dan keluar pada shift ini
            KasMasuk::where('shift_id', $shift->id)->update([
                'is_closed'             => false,
                'status_tarik_akunting' => false,
            ]);

            KasKeluar::where('shift_id', $shift->id)->update([
                'is_closed'             => false,
                'status_tarik_akunting' => false,
            ]);

            $tgl = Carbon::parse($shift->tanggal_shift ?? $shift->tgl_shift)->format('d/m/Y');

            return redirect()->back()->with('success', "Kas Harian tanggal {$tgl} BERHASIL DIBUKA KEMBALI atas perizinan resmi dari {$approver->name} ({$approver->role})! Form transaksi dan koreksi kasir telah aktif kembali.");
        });
    }

    /**
     * Tolak Permohonan Buka Kembali Kas oleh Manager Keuangan
     */
    public function tolakBukaKembaliKas(Request $request, $id)
    {
        $shift = ShiftKasir::findOrFail($id);
        $currentUser = Auth::user();

        if (!$currentUser->isAdmin() && !in_array($currentUser->role, ['keuangan', 'admin_keuangan', 'kepala_divisi'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk menolak permohonan pembukaan kas!');
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|min:3',
        ]);

        $shift->update([
            'reopen_status'      => 'rejected',
            'reopen_approved_by' => $currentUser->id,
            'reopen_approved_at' => Carbon::now(),
            'reopen_notes'       => 'Ditolak: ' . $request->alasan_penolakan,
        ]);

        $tgl = Carbon::parse($shift->tanggal_shift ?? $shift->tgl_shift)->format('d/m/Y');

        return redirect()->back()->with('error', "Permohonan Buka Kembali Kas tanggal {$tgl} telah ditolak oleh {$currentUser->name}.");
    }
}
