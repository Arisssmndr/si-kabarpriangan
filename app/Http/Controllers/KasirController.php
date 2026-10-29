<?php

namespace App\Http\Controllers;

use App\Models\Agen;
use App\Models\BuktiPengeluaran;
use App\Models\Faktur;
use App\Models\PembayaranFaktur;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\KasTransaksi;
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
use Illuminate\Support\Facades\Schema;

class KasirController extends Controller
{
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
            'recentKasir'
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
            $koranQ = TransaksiKoran::query();

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
                      ->orWhere('tanggal_transaksikoran', 'like', "%{$search}%");
                });
            }

            foreach ($koranQ->latest('id')->get() as $item) {
                $tglMuat = $item->tanggal_muatkoran 
                    ? Carbon::parse($item->tanggal_muatkoran)->toDateString() 
                    : ($item->tanggal_transaksikoran ? Carbon::parse($item->tanggal_transaksikoran)->toDateString() : Carbon::today()->toDateString());
                $kodeRekening = '1.00.04.01.01.01';
                $deskripsiTayang = 'Muat ' . ($item->total_muatkoran ?? 1) . ' Edisi (' . Carbon::parse($tglMuat)->format('d/m/Y') . ')' . ($item->ukuran_iklan ? ' • ' . $item->ukuran_iklan : '');

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
            'potongan_pajak'        => 'nullable|numeric|min:0',
            'keterangan'            => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $shift = $this->getActiveShift();
            $nominalBayar = (float) $request->input('nominal_bayar');
            $potonganPajak = (float) ($request->input('potongan_pajak') ?? 0);
            $totalPengurang = $nominalBayar + $potonganPajak;
            $userId = Auth::id() ?? 1;

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

                $trx->jumlahbayar_transaksikoran += $totalPengurang;
                $sisaPiutang = max(0, $totalTagihan - (float) $trx->jumlahbayar_transaksikoran);
                $trx->piutang_transaksikoran = $sisaPiutang;

                // JIKA LUNAS: Update status order dan buka komisi KH & Insentif AE
                if ($sisaPiutang <= 0) {
                    if (Schema::hasColumn('transaksikoran', 'status_order')) {
                        $trx->status_order = 'Lunas';
                    }
                    $trx->siap_cair_kh_insentif = true; // Komisi KH & Insentif dapat dicairkan di Divisi Kasir
                }
                $trx->save();

                // Sinkronkan ke tabel Faktur jika ada
                $faktur = Faktur::where('no_faktur', $fakturNo)->first();
                if ($faktur) {
                    $faktur->total_terbayar += $totalPengurang;
                    $faktur->sisa_piutang = max(0, (float) $faktur->total_tagihan - (float) $faktur->total_terbayar);
                    $faktur->status = $faktur->sisa_piutang <= 0 ? 'lunas' : 'cicilan';
                    $faktur->status_bayar = $faktur->sisa_piutang <= 0 ? 'lunas' : 'uang_muka';
                    if ($faktur->sisa_piutang <= 0) {
                        $faktur->siap_cair_kh_insentif = true;
                    }
                    $faktur->save();
                }

            } elseif ($request->type === 'online') {
                $trx = TransaksiOnline::lockForUpdate()->findOrFail($request->id);
                $fakturNo = $trx->nofakturonline;
                $namaPembayar = $trx->nama_pemasangonline;
                $totalTagihan = (float) $trx->totaltagihan_transaksionline;

                $trx->jumlahbayar_transaksionline += $totalPengurang;
                $sisaPiutang = max(0, $totalTagihan - (float) $trx->jumlahbayar_transaksionline);
                $trx->piutang_transaksionline = $sisaPiutang;
                if ($sisaPiutang <= 0) {
                    $trx->siap_cair_kh_insentif = true;
                }
                $trx->save();

            } elseif ($request->type === 'tv') {
                $trx = TransaksiPriangan::lockForUpdate()->findOrFail($request->id);
                $fakturNo = $trx->nofakturpriangan;
                $namaPembayar = $trx->nama_pemasangpriangan;
                $totalTagihan = (float) $trx->totaltagihan_transaksipriangan;

                $trx->jumlahbayar_transaksipriangan += $totalPengurang;
                $sisaPiutang = max(0, $totalTagihan - (float) $trx->jumlahbayar_transaksipriangan);
                $trx->piutang_transaksipriangan = $sisaPiutang;
                if ($sisaPiutang <= 0) {
                    $trx->siap_cair_kh_insentif = true;
                }
                $trx->save();

            } else {
                $faktur = Faktur::lockForUpdate()->findOrFail($request->id);
                $fakturNo = $faktur->no_faktur;
                $namaPembayar = $faktur->customer->nama_customer ?? 'Klien Faktur';
                $totalTagihan = (float) $faktur->total_tagihan;

                $faktur->total_terbayar += $totalPengurang;
                $sisaPiutang = max(0, $totalTagihan - (float) $faktur->total_terbayar);
                $faktur->sisa_piutang = $sisaPiutang;
                $faktur->status = $sisaPiutang <= 0 ? 'lunas' : 'cicilan';
                $faktur->status_bayar = $sisaPiutang <= 0 ? 'lunas' : 'uang_muka';
                if ($sisaPiutang <= 0) {
                    $faktur->siap_cair_kh_insentif = true;
                }
                $faktur->save();
            }

            // 2. Terbitkan Nomor Kuitansi Kas Masuk Format Asli DOS KAS.EXE (KI-01-YYYYMMXXXX)
            $noKuitansi = KasMasuk::generateNoKuitansi('01');
            $kodePerkiraan = $request->kode_perkiraan ?? ($sisaPiutang <= 0 ? '1.00.04.01.01.01' : '2.00.01.01.01.01');
            $namaPerkiraan = $sisaPiutang <= 0 ? 'Piutang Iklan' : 'Uang Muka/Titipan Iklan';

            // 3. Simpan Transaksi Kasir ke tabel kas_masuks
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
                'potongan_pajak'        => $potonganPajak,
                'potongan_retur'        => 0,
                'jumlah_diterima'       => $nominalBayar,
                'jumlah_dibayar'        => $nominalBayar,
                'sisa_piutang'          => $sisaPiutang,
                'status_bayar'          => $sisaPiutang <= 0 ? 'lunas' : 'uang_muka',
                'status_pembayaran'     => $sisaPiutang <= 0 ? 'lunas' : 'sebagian',
                'keterangan'            => $request->keterangan ?? "Penerimaan Kasir Iklan Faktur {$fakturNo} ({$namaPembayar})",
                'kasir_id'              => $userId,
                'user_id'               => $userId,
                'is_closed'             => false,
                'is_pulled_accounting'  => false,
                'status_tarik_akunting' => false,
            ]);

            // 4. Update Shift Kasir (DOS Field Sync)
            $shift->total_ki += $nominalBayar;
            $shift->total_masuk += $nominalBayar;
            $shift->saldo_akhir_teoritis = $shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko;
            $shift->saldo_akhir_fisik = $shift->saldo_akhir_teoritis;
            $shift->save();

            // 5. Catat Mutasi Kas Masuk ke Buku Kas Treasury
            KasTransaksi::catatMutasi(
                'masuk',
                'Pelunasan Iklan ' . ucfirst($mediaType),
                $nominalBayar,
                "KI {$noKuitansi} - Pelunasan Faktur {$fakturNo} ({$namaPembayar})",
                $kasMasuk,
                $userId,
                Carbon::today()->toDateString()
            );

            // Payload untuk auto-popup modal cetak Kuitansi / BKM
            $cetakData = [
                'no_kuitansi'   => $noKuitansi,
                'no_bkm'        => $noKuitansi,
                'kode_bagian'   => '01',
                'kode_perkiraan'=> $kodePerkiraan,
                'faktur'        => $fakturNo,
                'customer'      => $namaPembayar,
                'nominal'       => $nominalBayar,
                'terbilang'     => number_format($nominalBayar, 0, ',', '.'),
                'metode'        => strtoupper($request->metode_pembayaran),
                'bank'          => $request->bank_tujuan ?? '-',
                'no_ref'        => $request->no_referensi_transfer ?? '-',
                'penagih'       => $request->petugas_inkaso ?? 'Kasir Langsung',
                'pajak'         => $potonganPajak,
                'sisa_piutang'  => $sisaPiutang,
                'status_bayar'  => $sisaPiutang <= 0 ? 'LUNAS' : 'SEBAGIAN / DP',
                'tanggal'       => Carbon::now()->translatedFormat('d F Y, H:i'),
                'kasir'         => Auth::user()->name ?? 'Kasir Kabar Priangan',
            ];

            return redirect()->back()
                ->with('success', "Bukti Kas Masuk ({$noKuitansi}) untuk faktur {$fakturNo} sebesar Rp " . number_format($nominalBayar, 0, ',', '.') . " berhasil disimpan dan memotong saldo piutang!")
                ->with('cetak_bkm', $cetakData);
        });
    }

    /**
     * Setoran Sirkulasi Koran
     */
    public function penerimaanSirkulasi(Request $request)
    {
        $search = $request->input('search');

        // Ambil data agen dari database Master Agen Sirkulasi
        $agenQuery = Agen::query();
        if ($search) {
            $agenQuery->where(function($q) use ($search) {
                $q->where('kode_agen', 'like', "%{$search}%")
                  ->orWhere('nama_agen', 'like', "%{$search}%")
                  ->orWhere('wilayah', 'like', "%{$search}%");
            });
        }
        $agenList = $agenQuery->orderBy('kode_agen')->get();

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

        return view('pages.kasir.penerimaan.sirkulasi', compact('agenList', 'petugasInkasoList', 'search'));
    }

    /**
     * Process Setoran Sirkulasi dengan Validasi BAPR & Update Piutang Agen
     * Realisasi Setoran Sirkulasi:
     * - Simpan data ke kas_masuks dengan validasi no BAPR (retur koran)
     * - Update status piutang tagihan agen di modul Sirkulasi
     * - Sediakan mutasi yang siap dibaca oleh Kartu Piutang Agen di Akunting
     */
    public function storePenerimaanSirkulasi(Request $request)
    {
        if ($unclosedMsg = $this->checkPreviousShiftClosed()) {
            return redirect()->back()->with('error', $unclosedMsg);
        }

        $request->validate([
            'agen_id'               => 'required|integer',
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
            $agen = Agen::lockForUpdate()->findOrFail($request->agen_id);
            $saldoAwalPiutang = (float) $agen->saldo_piutang;
            $saldoAkhirPiutang = max(0, $saldoAwalPiutang - $totalPengurang);

            $agen->saldo_piutang = $saldoAkhirPiutang;
            $agen->save();

            // 2. Terbitkan Nomor Kuitansi Kas Masuk Format Asli DOS KAS.EXE (KI-02-YYYYMMXXXX)
            $noKuitansi = KasMasuk::generateNoKuitansi('02');
            $kodePerkiraan = $request->kode_perkiraan ?? ($saldoAkhirPiutang <= 0 ? '1.00.04.01.01.00' : '2.00.01.01.01.00');
            $namaPerkiraan = $saldoAkhirPiutang <= 0 ? 'Piutang Sirkulasi Koran Agen' : 'Uang Muka Sirkulasi';

            // 3. Simpan ke kas_masuks
            $kasMasuk = KasMasuk::create([
                'no_kuitansi'           => $noKuitansi,
                'no_bkm'                => $noKuitansi,
                'shift_id'              => $shift->id,
                'tanggal_terima'        => Carbon::today(),
                'tgl_transaksi'         => Carbon::today(),
                'no_blanko'             => $request->no_blanko,
                'kode_bagian'           => '02',
                'kode_perkiraan'        => $kodePerkiraan,
                'nama_perkiraan'        => $namaPerkiraan,
                'terima_dari'           => $agen->nama_agen,
                'nama_pembayar'         => $agen->nama_agen,
                'penagih_inkaso'        => $request->penagih_inkaso,
                'kode_agen_ref'         => $agen->kode_agen ?? $agen->id,
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
                'keterangan'            => $request->keterangan ?? "Setoran Koran Agen {$agen->nama_agen}" . ($request->no_bapr ? " (Retur BAPR: {$request->no_bapr} - {$returEks} eks @ Rp2.100)" : ''),
                'kasir_id'              => $userId,
                'user_id'               => $userId,
                'is_closed'             => false,
                'is_pulled_accounting'  => false,
                'status_tarik_akunting' => false,
            ]);

            // 4. Update Shift Kasir (DOS Field Sync)
            $shift->total_ki += $nominalSetoran;
            $shift->total_masuk += $nominalSetoran;
            $shift->saldo_akhir_teoritis = $shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko;
            $shift->saldo_akhir_fisik = $shift->saldo_akhir_teoritis;
            $shift->save();

            // 5. Catat Mutasi Kas Masuk agar Kartu Piutang Agen & GL Akunting membaca mutasi ini
            KasTransaksi::catatMutasi(
                'masuk',
                'Setoran Sirkulasi Agen Koran',
                $nominalSetoran,
                "KI {$noKuitansi} - Setoran Agen {$agen->nama_agen} ({$agen->kode_agen})" . ($request->no_bapr ? " | Retur {$returEks} eks x Rp2.100 (Rp " . number_format($nilaiRetur, 0, ',', '.') . ")" : ''),
                $kasMasuk,
                $userId,
                Carbon::today()->toDateString()
            );

            // 6. Alokasikan Setoran ke Faktur Sirkulasi Agen yang masih memiliki sisa piutang
            $openFakturs = Faktur::where('kategori', 'sirkulasi')
                ->where('sisa_piutang', '>', 0)
                ->where(function ($q) use ($agen) {
                    $q->whereHas('customer', function ($cq) use ($agen) {
                        $cq->where('nama_customer', 'like', "%{$agen->nama_agen}%")
                           ->orWhere('identitas', $agen->kode_agen);
                    });
                })
                ->orderBy('tanggal_faktur', 'asc')
                ->get();

            $sisaAlokasi = $totalPengurang;
            foreach ($openFakturs as $openFaktur) {
                if ($sisaAlokasi <= 0) break;

                $bayarNominal = min($sisaAlokasi, (float) $openFaktur->sisa_piutang);
                $openFaktur->total_terbayar = (float) $openFaktur->total_terbayar + $bayarNominal;
                $openFaktur->sisa_piutang = max(0, (float) $openFaktur->sisa_piutang - $bayarNominal);
                if ($openFaktur->sisa_piutang <= 0) {
                    $openFaktur->status = 'lunas';
                    $openFaktur->status_bayar = 'lunas';
                } else {
                    $openFaktur->status = 'cicilan';
                    $openFaktur->status_bayar = 'sebagian';
                }
                $openFaktur->save();

                PembayaranFaktur::create([
                    'no_kwitansi'   => $noKuitansi,
                    'faktur_id'     => $openFaktur->id,
                    'tanggal_bayar' => Carbon::today(),
                    'nominal_bayar' => $bayarNominal,
                    'metode_bayar'  => $request->metode_pembayaran,
                    'tipe_bayar'    => $openFaktur->sisa_piutang <= 0 ? 'lunas_langsung' : 'cicilan_inkaso',
                    'diterima_oleh' => $userId,
                    'catatan'       => "Setoran Kasir Sirkulasi Agen {$agen->nama_agen} (BKM: {$noKuitansi})" . ($returEks > 0 ? " [Termasuk Retur {$returEks} eks]" : ""),
                ]);

                $sisaAlokasi -= $bayarNominal;
            }

            // Data untuk cetak Tanda Terima Setoran Koran
            $cetakSirkulasi = [
                'no_kuitansi'    => $noKuitansi,
                'no_bkm'         => $noKuitansi,
                'kode_bagian'    => '02',
                'kode_perkiraan' => $kodePerkiraan,
                'kode_agen'      => $agen->kode_agen,
                'nama_agen'      => $agen->nama_agen,
                'wilayah'        => $agen->wilayah ?? 'Tasikmalaya',
                'nominal'        => $nominalSetoran,
                'terbilang'      => number_format($nominalSetoran, 0, ',', '.'),
                'metode'         => strtoupper($request->metode_pembayaran),
                'penagih'        => $request->penagih_inkaso ?? 'Kasir Langsung',
                'no_bapr'        => $request->no_bapr ?? '-',
                'retur_eks'      => $returEks,
                'nilai_retur'    => $nilaiRetur,
                'saldo_sebelum'  => $saldoAwalPiutang,
                'saldo_sesudah'  => $saldoAkhirPiutang,
                'status_piutang' => $saldoAkhirPiutang <= 0 ? 'LUNAS BERSIH' : 'MASIH ADA SISA PIUTANG',
                'tanggal'        => Carbon::now()->translatedFormat('d F Y, H:i'),
                'kasir'          => Auth::user()->name ?? 'Kasir Kabar Priangan',
            ];

            return redirect()->back()
                ->with('success', "Setoran Sirkulasi dari {$agen->nama_agen} ({$agen->kode_agen}) sebesar Rp " . number_format($nominalSetoran, 0, ',', '.') . " berhasil dicatat ke Kas Masuk ({$noKuitansi}) dan memotong Kartu Piutang Agen!")
                ->with('cetak_sirkulasi', $cetakSirkulasi);
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

        return view('pages.kasir.pengeluaran.realisasi-bp', compact('bpList', 'search'));
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
                'terbilang'     => number_format($nominalCair, 0, ',', '.'),
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

        // Mutasi Kas Shift Hari ini (Gabungan KasMasuk dan KasKeluar)
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

        return view('pages.kasir.buku-kas.index', compact(
            'today',
            'activeShift',
            'saldoAwalShift',
            'totalKasMasuk',
            'totalPengeluaranBp',
            'saldoAkhirShift',
            'totalPenerimaanIklan',
            'totalSetoranSirkulasi',
            'totalPenjualanTunai',
            'mutasiShift'
        ));
    }

    /**
     * Process Closing Shift Kasir (End Shift)
     * - Hitung saldo sistem vs saldo fisik, kunci shift menjadi 'ditutup'
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
            $selisih = $fisikKas - $saldoSistem;

            // 1. Kunci Shift Kasir
            $shift->update([
                'total_masuk'          => $totalMasuk,
                'total_keluar'         => $totalKeluar,
                'saldo_akhir_fisik'    => $fisikKas,
                'selisih_kas'          => $selisih,
                'status'               => 'ditutup',
                'closed_at'            => Carbon::now(),
                'catatan_serah_terima' => $request->input('catatan_serah_terima'),
            ]);

            // 2. Tandai seluruh kas_masuk & kas_keluar pada shift ini: status_tarik_akunting = true
            KasMasuk::where('shift_id', $shift->id)->update(['status_tarik_akunting' => true]);
            KasKeluar::where('shift_id', $shift->id)->update(['status_tarik_akunting' => true]);

            $selisihFormat = number_format(abs($selisih), 0, ',', '.');
            $selisihLabel = $selisih == 0 ? 'Seimbang (Balance)' : ($selisih > 0 ? "Surplus Kas (+Rp {$selisihFormat})" : "Defisit Kas (-Rp {$selisihFormat})");

            return redirect()->back()->with('success', "Shift Kasir berhasil ditutup! Saldo Sistem: Rp " . number_format($saldoSistem, 0, ',', '.') . " | Saldo Fisik: Rp " . number_format($fisikKas, 0, ',', '.') . " ({$selisihLabel}). Seluruh transaksi telah ditandai Siap Tarik Akunting untuk posting Jurnal & Buku Besar.");
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
            'saldoAkhir'
        ));
    }

    /**
     * Mutasi Kas Bank Masuk (Jasa Giro / Transfer BJB - Bagian 14)
     */
    public function penerimaanBank(Request $request)
    {
        $search = $request->input('search');

        $bankQuery = KasMasuk::where(function($q) {
            $q->where('kode_bagian', '14')
              ->orWhere('metode_bayar', 'transfer')
              ->orWhere('tipe_transaksi', 'bank');
        });

        if ($search) {
            $bankQuery->where(function($q) use ($search) {
                $q->where('no_kuitansi', 'like', "%{$search}%")
                  ->orWhere('terima_dari', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $bankList = $bankQuery->latest('id')->paginate(15);

        $totalBankBulanIni = (float) KasMasuk::where(function($q) {
                $q->where('kode_bagian', '14')->orWhere('metode_bayar', 'transfer');
            })
            ->whereMonth('tanggal_terima', Carbon::now()->month)
            ->whereYear('tanggal_terima', Carbon::now()->year)
            ->sum('jumlah_diterima');

        return view('pages.kasir.penerimaan.bank', compact('bankList', 'totalBankBulanIni', 'search'));
    }

    /**
     * Store Mutasi Kas Bank Masuk (Format: KI-14-YYYYMMXXXX)
     */
    public function storePenerimaanBank(Request $request)
    {
        if ($unclosedMsg = $this->checkPreviousShiftClosed()) {
            return redirect()->back()->with('error', $unclosedMsg);
        }

        $request->validate([
            'jenis_transaksi'   => 'required|string|in:jasa_giro,transfer_bjb,bunga_tabungan,pendapatan_lain',
            'terima_dari'       => 'required|string',
            'nominal'           => 'required|numeric|min:1',
            'no_referensi'      => 'nullable|string',
            'no_blanko'         => 'nullable|string',
            'kode_perkiraan'    => 'nullable|string',
            'keterangan'        => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $shift = $this->getActiveShift();
            $nominal = (float) $request->input('nominal');
            $userId = Auth::id() ?? 1;

            $noKuitansi = KasMasuk::generateNoKuitansi('14');

            $kodePerkiraan = $request->kode_perkiraan ?? ($request->jenis_transaksi === 'jasa_giro' ? '7.00.01.01.01' : '7.00.01.01.02');
            $namaPerkiraan = match ($request->jenis_transaksi) {
                'jasa_giro'       => 'Jasa Giro / Bunga Bank BJB',
                'bunga_tabungan'  => 'Pendapatan Bunga Bank',
                'transfer_bjb'    => 'Penerimaan Transfer Bank BJB',
                default           => 'Pendapatan Lain-lain / BPR',
            };

            $kasMasuk = KasMasuk::create([
                'no_kuitansi'           => $noKuitansi,
                'no_bkm'                => $noKuitansi,
                'shift_id'              => $shift->id,
                'tanggal_terima'        => Carbon::today(),
                'tgl_transaksi'         => Carbon::today(),
                'no_blanko'             => $request->no_blanko,
                'kode_bagian'           => '14',
                'kode_perkiraan'        => $kodePerkiraan,
                'nama_perkiraan'        => $namaPerkiraan,
                'terima_dari'           => $request->terima_dari,
                'nama_pembayar'         => $request->terima_dari,
                'penagih_inkaso'        => 'Bank BJB Cabang Tasikmalaya',
                'tipe_transaksi'        => 'bank',
                'metode_bayar'          => 'transfer',
                'bank_tujuan'           => 'Bank BJB Tasikmalaya',
                'no_referensi_transfer' => $request->no_referensi,
                'jumlah_tagihan'        => $nominal,
                'total_tagihan'         => $nominal,
                'potongan_pajak'        => 0,
                'potongan_retur'        => 0,
                'jumlah_diterima'       => $nominal,
                'jumlah_dibayar'        => $nominal,
                'sisa_piutang'          => 0,
                'status_bayar'          => 'lunas',
                'status_pembayaran'     => 'lunas',
                'keterangan'            => $request->keterangan ?? "Penerimaan {$namaPerkiraan} dari {$request->terima_dari}",
                'kasir_id'              => $userId,
                'user_id'               => $userId,
                'is_closed'             => false,
                'is_pulled_accounting'  => false,
                'status_tarik_akunting' => false,
            ]);

            $shift->total_ki += $nominal;
            $shift->total_masuk += $nominal;
            $shift->saldo_akhir_teoritis = $shift->saldo_awal_fisik + $shift->total_ki - $shift->total_ko;
            $shift->saldo_akhir_fisik = $shift->saldo_akhir_teoritis;
            $shift->save();

            KasTransaksi::catatMutasi(
                'masuk',
                'Penerimaan Kas Bank (' . $namaPerkiraan . ')',
                $nominal,
                "KI {$noKuitansi} - {$namaPerkiraan} ({$request->terima_dari})",
                $kasMasuk,
                $userId,
                Carbon::today()->toDateString()
            );

            $cetakBank = [
                'no_kuitansi'    => $noKuitansi,
                'no_bkm'         => $noKuitansi,
                'kode_bagian'    => '14',
                'kode_perkiraan' => $kodePerkiraan,
                'terima_dari'    => $request->terima_dari,
                'jenis'          => $namaPerkiraan,
                'nominal'        => $nominal,
                'terbilang'      => number_format($nominal, 0, ',', '.'),
                'tanggal'        => Carbon::now()->translatedFormat('d F Y, H:i'),
                'kasir'          => Auth::user()->name ?? 'Kasir Kabar Priangan',
            ];

            return redirect()->back()
                ->with('success', "Mutasi Bank Masuk ({$noKuitansi}) sebesar Rp " . number_format($nominal, 0, ',', '.') . " berhasil dicatat!")
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
                'terbilang'     => number_format($nominal, 0, ',', '.'),
                'tanggal'       => Carbon::now()->translatedFormat('d F Y, H:i'),
                'kasir'         => Auth::user()->name ?? 'Kasir Kabar Priangan',
            ];

            return redirect()->back()
                ->with('success', "Bukti Kas Keluar ({$noBkk}) pencairan {$labelKomisi} sebesar Rp " . number_format($nominal, 0, ',', '.') . " berhasil diterbitkan!")
                ->with('cetak_komisi', $cetakKomisi);
        });
    }

    /**
     * Laporan & Mutasi Kas: Rekapitulasi Kas Masuk & Keluar (Per Tanggal / Per Bulan)
     */
    public function laporanRekap(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $bagianFilter = $request->input('kode_bagian', 'all');

        $kiQuery = KasMasuk::whereBetween('tanggal_terima', [$startDate, $endDate]);
        $koQuery = KasKeluar::whereBetween('tanggal_keluar', [$startDate, $endDate]);

        if ($bagianFilter !== 'all') {
            $kiQuery->where('kode_bagian', $bagianFilter);
            $koQuery->where('kode_bagian', $bagianFilter);
        }

        $kasMasuks = $kiQuery->orderBy('tanggal_terima', 'asc')->get();
        $kasKeluars = $koQuery->orderBy('tanggal_keluar', 'asc')->get();

        $totalMasuk = (float) $kasMasuks->sum('jumlah_diterima');
        $totalKeluar = (float) $kasKeluars->sum('jumlah_keluar');
        $saldoSurplusDefisit = $totalMasuk - $totalKeluar;

        return view('pages.kasir.laporan.rekap', compact(
            'kasMasuks',
            'kasKeluars',
            'totalMasuk',
            'totalKeluar',
            'saldoSurplusDefisit',
            'startDate',
            'endDate',
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
            fputcsv($file, ['No', 'No Kuitansi', 'Tanggal', 'Bagian', 'Kode COA', 'Nama Perkiraan', 'Terima Dari', 'Jumlah (Rp)', 'Status']);
            foreach ($kasMasuks as $idx => $ki) {
                fputcsv($file, [
                    $idx + 1,
                    $ki->no_kuitansi ?? $ki->no_bkm,
                    $ki->tanggal_terima ? Carbon::parse($ki->tanggal_terima)->format('d/m/Y') : '-',
                    $ki->kode_bagian,
                    $ki->kode_perkiraan,
                    $ki->nama_perkiraan,
                    $ki->terima_dari ?? $ki->nama_pembayar,
                    $ki->jumlah_diterima,
                    strtoupper($ki->status_bayar ?? 'Lunas'),
                ]);
            }
            fputcsv($file, ['', '', '', '', '', '', 'TOTAL KAS MASUK', $kasMasuks->sum('jumlah_diterima'), '']);
            fputcsv($file, []);

            fputcsv($file, ['--- KAS KELUAR (KO) ---']);
            fputcsv($file, ['No', 'No BKK', 'Tanggal', 'Bagian', 'No Register BP', 'Kode COA', 'Diberikan Kepada', 'Jumlah (Rp)', 'Kategori']);
            foreach ($kasKeluars as $idx => $ko) {
                fputcsv($file, [
                    $idx + 1,
                    $ko->no_bkk,
                    $ko->tanggal_keluar ? Carbon::parse($ko->tanggal_keluar)->format('d/m/Y') : '-',
                    $ko->kode_bagian,
                    $ko->no_register_anggaran ?? $ko->no_bp_ref,
                    $ko->kode_perkiraan,
                    $ko->diberikan_kepada ?? $ko->nama_penerima,
                    $ko->jumlah_keluar,
                    $ko->kategori_beban ?? $ko->kategori,
                ]);
            }
            fputcsv($file, ['', '', '', '', '', '', 'TOTAL KAS KELUAR', $kasKeluars->sum('jumlah_keluar'), '']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Laporan Mutasi Kas Bank (Harian / Bulanan)
     */
    public function laporanMutasiBank(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);

        $mutasiBank = KasMasuk::where(function($q) {
                $q->where('kode_bagian', '14')
                  ->orWhere('metode_bayar', 'transfer');
            })
            ->whereMonth('tanggal_terima', $bulan)
            ->whereYear('tanggal_terima', $tahun)
            ->orderBy('tanggal_terima', 'asc')
            ->get();

        $totalPenerimaanBank = $mutasiBank->sum('jumlah_diterima');

        return view('pages.kasir.laporan.mutasi-bank', compact('mutasiBank', 'totalPenerimaanBank', 'bulan', 'tahun'));
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
        $saldoFisik = (float) $activeShift->saldo_akhir_fisik;
        $selisih = $saldoFisik - $saldoTeoritis;

        $totalBankBulanIni = (float) KasMasuk::where('kode_bagian', '14')
            ->whereMonth('tanggal_terima', Carbon::now()->month)
            ->sum('jumlah_diterima');

        return view('pages.kasir.laporan.posisi-kas', compact(
            'activeShift',
            'saldoAwal',
            'totalKiHariIni',
            'totalKoHariIni',
            'saldoTeoritis',
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
     */
    public function utilitasClosing(Request $request)
    {
        $activeShift = $this->getActiveShift();
        $today = Carbon::today()->toDateString();

        $totalKi = (float) KasMasuk::whereDate('tanggal_terima', $today)->sum('jumlah_diterima');
        $totalKo = (float) KasKeluar::whereDate('tanggal_keluar', $today)->sum('jumlah_keluar');
        $saldoAwal = (float) $activeShift->saldo_awal_fisik;
        $saldoTeoritis = $saldoAwal + $totalKi - $totalKo;

        // Riwayat Closing sebelumnya
        $riwayatClosing = ShiftKasir::with('kasir')
            ->whereIn('status', ['closed', 'ditutup'])
            ->latest('id')
            ->take(10)
            ->get();

        return view('pages.kasir.utilitas.closing', compact('activeShift', 'saldoAwal', 'totalKi', 'totalKo', 'saldoTeoritis', 'riwayatClosing'));
    }

    /**
     * Store Tutup Kas Harian (Kunci Shift & Tandai ready_to_pull Akuntansi)
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

            $totalFisik = $p100 + $p50 + $p20 + $p10 + $p5 + $p2 + $p1 + $koin;

            $totalKi = (float) KasMasuk::where('shift_id', $shift->id)->sum('jumlah_diterima');
            $totalKo = (float) KasKeluar::where('shift_id', $shift->id)->sum('jumlah_keluar');
            $saldoTeoritis = (float) ($shift->saldo_awal_fisik + $totalKi - $totalKo);
            $selisih = $totalFisik - $saldoTeoritis;

            // 1. Kunci Shift
            $shift->update([
                'total_ki'             => $totalKi,
                'total_ko'             => $totalKo,
                'total_masuk'          => $totalKi,
                'total_keluar'         => $totalKo,
                'saldo_akhir_teoritis' => $saldoTeoritis,
                'saldo_akhir_fisik'    => $totalFisik,
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

            $labelSelisih = $selisih == 0 ? 'SEIMBANG (ZERO DIFFERENCE)' : ($selisih > 0 ? "+Rp " . number_format($selisih, 0, ',', '.') . " (LEBIH FISIK)" : "-Rp " . number_format(abs($selisih), 0, ',', '.') . " (SELISIH KURANG)");

            return redirect()->route('kasir.dashboard')
                ->with('success', "Tutup Kas Harian (Closing Shift) Berhasil! Saldo Fisik: Rp " . number_format($totalFisik, 0, ',', '.') . " | Saldo Teoritis: Rp " . number_format($saldoTeoritis, 0, ',', '.') . " [{$labelSelisih}]. Seluruh transaksi kasir hari ini telah dikunci dan siap ditarik oleh modul Akuntansi!");
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
                'jenis_nama'  => 'Kas Masuk (KI - Bagian ' . $m->kode_bagian . ')',
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
                'jenis_nama'  => 'Kas Keluar (KO - Bagian ' . $k->kode_bagian . ')',
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
}
